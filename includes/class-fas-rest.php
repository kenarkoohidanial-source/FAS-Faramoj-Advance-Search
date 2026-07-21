<?php
/**
 * Multilingual REST API Endpoint Logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAS_Rest {

    public function register_routes() {
        register_rest_route( 'fas/v1', '/search', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_search_results' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public function get_search_results( WP_REST_Request $request ) {
        $term = sanitize_text_field( $request->get_param( 's' ) );
        $lang = sanitize_text_field( $request->get_param( 'lang' ) );

        if ( empty( $term ) ) {
            return new WP_REST_Response( array( 'error' => 'Empty search term' ), 400 );
        }

        // Log search query metrics dynamically for our Statistics submenu
        $this->log_search_stats( $term );

        // Switch language context if WPML is active
        if ( function_exists( 'wpml_object_id_filter' ) && ! empty( $lang ) ) {
            do_action( 'wpml_switch_language', $lang );
        }

        // Switch language context if Polylang is active
        if ( function_exists( 'PLL' ) && ! empty( $lang ) ) {
            PLL()->curlang = PLL()->model->get_language( $lang );
        }

        // Custom Transient Cache Duration from Settings (default 1 hour) via unified get_option
        $cache_duration = FAS_Core::get_option( 'fas_cache_duration', HOUR_IN_SECONDS );
        if ( empty( $cache_duration ) && $cache_duration !== '0' ) {
            $cache_duration = HOUR_IN_SECONDS;
        }

        // Optimized Cache Key based on term and language
        $cache_key = 'fas_search_' . md5( $term . '_' . $lang );
        
        $results = false;
        if ( $cache_duration > 0 ) {
            $results = get_transient( $cache_key );
        }

        if ( false === $results ) {
            $results = $this->execute_db_query( $term, $lang );
            if ( $cache_duration > 0 ) {
                set_transient( $cache_key, $results, intval( $cache_duration ) );
            }
        }

        return new WP_REST_Response( $results, 200 );
    }

    /**
     * Log search queries statistics (total count & popular terms tracking)
     */
    private function log_search_stats( $term ) {
        if ( empty( $term ) || strlen( $term ) < 3 ) {
            return;
        }

        // Standardize lowercase matching
        $term_clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $term ) ) : strtolower( trim( $term ) );
        $stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [] ) );

        if ( ! isset( $stats['total_count'] ) ) {
            $stats['total_count'] = 0;
        }
        $stats['total_count']++;

        if ( ! isset( $stats['terms'] ) ) {
            $stats['terms'] = array();
        }

        if ( ! isset( $stats['terms'][ $term_clean ] ) ) {
            $stats['terms'][ $term_clean ] = 0;
        }
        $stats['terms'][ $term_clean ]++;

        // Keep top 100 popular terms to protect option storage
        arsort( $stats['terms'] );
        if ( count( $stats['terms'] ) > 100 ) {
            $stats['terms'] = array_slice( $stats['terms'], 0, 100, true );
        }

        update_option( 'fas_search_stats', $stats );
    }

    /**
     * Convert Persian and Arabic numbers to English digits.
     */
    private function convert_persian_to_english_digits( $string ) {
        $persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
        $arabic  = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
        $english = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
        
        $string = str_replace( $persian, $english, $string );
        return str_replace( $arabic, $english, $string );
    }

    /**
     * Convert English numbers to Persian digits.
     */
    private function convert_english_to_persian_digits( $string ) {
        $persian = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
        $english = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
        
        return str_replace( $english, $persian, $string );
    }

    /**
     * Helper to highlight the query in a text safely.
     */
    private function highlight_text( $text, $term ) {
        $escaped = esc_html( $text );
        if ( empty( $term ) ) {
            return $escaped;
        }

        // Generate highlight patterns for raw, English, and Persian digit normalized variations
        $terms = array_unique( array_filter( array(
            $term,
            $this->convert_persian_to_english_digits( $term ),
            $this->convert_english_to_persian_digits( $term )
        ) ) );

        foreach ( $terms as $t ) {
            $quoted_term = preg_quote( $t, '/' );
            $escaped = preg_replace(
                '/(' . $quoted_term . ')/i',
                '<mark class="fas-highlight">$1</mark>',
                $escaped
            );
        }

        return $escaped;
    }

    /**
     * Fetch all unique post IDs matching search terms in content or metadata.
     */
    private function get_matched_post_ids( $post_type, $search_terms, $meta_keys = [] ) {
        $all_ids = array();

        foreach ( $search_terms as $t ) {
            // 1. Keyword search
            $args1 = array(
                'post_type'      => $post_type,
                'posts_per_page' => 50,
                's'              => $t,
                'post_status'    => 'publish',
                'fields'         => 'ids',
            );
            $query1 = new WP_Query( $args1 );
            if ( ! empty( $query1->posts ) ) {
                $all_ids = array_merge( $all_ids, $query1->posts );
            }

            // 2. Custom metadata fields search (ACF)
            if ( ! empty( $meta_keys ) ) {
                $meta_query = array( 'relation' => 'OR' );
                foreach ( $meta_keys as $key ) {
                    $meta_query[] = array(
                        'key'     => $key,
                        'value'   => $t,
                        'compare' => 'LIKE',
                    );
                }

                $args2 = array(
                    'post_type'      => $post_type,
                    'posts_per_page' => 50,
                    'post_status'    => 'publish',
                    'fields'         => 'ids',
                    'meta_query'     => $meta_query,
                );
                $query2 = new WP_Query( $args2 );
                if ( ! empty( $query2->posts ) ) {
                    $all_ids = array_merge( $all_ids, $query2->posts );
                }
            }
        }

        return array_unique( $all_ids );
    }

    private function execute_db_query( $term, $lang ) {
        $normalized_terms = array_unique( array_filter( array(
            $term,
            $this->convert_persian_to_english_digits( $term ),
            $this->convert_english_to_persian_digits( $term )
        ) ) );

        // Split queries exactly by post type to isolate products (WooCommerce) vs posts vs pages
        $product_ids = $this->get_matched_post_ids( 'product', $normalized_terms, array( 'technical_specifications', 'frequency_range' ) );
        $post_ids    = $this->get_matched_post_ids( 'post', $normalized_terms );
        $page_ids    = $this->get_matched_post_ids( 'page', $normalized_terms );

        $formatted_results = array(
            'all'      => [], // Combined list of all matches
            'products' => [],
            'posts'    => [],
            'docs'     => []
        );

        // Limit each specific tab results to top 15 items
        $final_product_ids = array_slice( $product_ids, 0, 15 );
        $final_post_ids    = array_slice( $post_ids, 0, 15 );
        $final_page_ids    = array_slice( $page_ids, 0, 15 );

        // Render function helper
        $render_items = function( $ids, $default_category ) use ( $term ) {
            $items = array();
            if ( empty( $ids ) ) {
                return $items;
            }

            $query = new WP_Query( array(
                'post_type' => array( 'product', 'post', 'page' ),
                'post__in'  => $ids,
                'orderby'   => 'post__in',
            ) );

            if ( $query->have_posts() ) {
                $template_path = locate_template( 'faramoj-advanced-search/search-result-item.php' );
                if ( ! $template_path ) {
                    $template_path = plugin_dir_path( dirname( __FILE__ ) ) . 'templates/search-result-item.php';
                }

                while ( $query->have_posts() ) {
                    $query->the_post();
                    $id = get_the_ID();
                    $type = get_post_type();
                    
                    $title   = get_the_title();
                    $excerpt = wp_trim_words( get_the_excerpt(), 15, '...' );

                    $item = array(
                        'title'        => $title,
                        'permalink'    => get_permalink(),
                        'image'        => get_the_post_thumbnail_url( $id, 'thumbnail' ) ?: '',
                        'excerpt'      => $excerpt,
                        'title_html'   => $this->highlight_text( $title, $term ),
                        'excerpt_html' => $this->highlight_text( $excerpt, $term ),
                    );

                    $category = $default_category;
                    if ( 'all' === $default_category ) {
                        if ( $type === 'product' ) {
                            $category = 'products';
                        } elseif ( $type === 'post' ) {
                            $category = 'posts';
                        } else {
                            $category = 'docs';
                        }
                    }

                    ob_start();
                    $args = array( 'item' => $item, 'category' => $category );
                    include $template_path;
                    $rendered_html = ob_get_clean();

                    $items[] = array( 'html' => $rendered_html );
                }
                wp_reset_postdata();
            }

            return $items;
        };

        $formatted_results['products'] = $render_items( $final_product_ids, 'products' );
        $formatted_results['posts']    = $render_items( $final_post_ids, 'posts' );
        $formatted_results['docs']     = $render_items( $final_page_ids, 'docs' );

        // Merge pre-rendered results directly in PHP for the combined 'all' tab.
        // This is 100% reliable, uses zero extra SQL queries, and avoids WPML/Polylang multi-CPT filter conflicts.
        $all_items = array_merge(
            $formatted_results['products'],
            $formatted_results['posts'],
            $formatted_results['docs']
        );
        $formatted_results['all'] = array_slice( $all_items, 0, 20 );

        return $formatted_results;
    }
}
