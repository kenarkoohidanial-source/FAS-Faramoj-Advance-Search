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

        register_rest_route( 'fas/v1', '/track-click', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'track_search_click' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public function track_search_click( WP_REST_Request $request ) {
        $post_id = intval( $request->get_param( 'post_id' ) );
        $title   = sanitize_text_field( $request->get_param( 'title' ) );

        if ( empty( $post_id ) ) {
            return new WP_REST_Response( array( 'error' => 'Empty post ID' ), 400 );
        }

        $stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [], 'clicks' => [] ) );

        if ( ! isset( $stats['clicks'] ) ) {
            $stats['clicks'] = array();
        }

        // Use post_id as key, but store title for display purposes
        if ( ! isset( $stats['clicks'][ $post_id ] ) ) {
            $stats['clicks'][ $post_id ] = array(
                'count' => 0,
                'title' => $title
            );
        }

        $stats['clicks'][ $post_id ]['count']++;

        // Keep top 100 clicked items to protect option storage
        uasort( $stats['clicks'], function( $a, $b ) {
            return $b['count'] <=> $a['count'];
        } );

        if ( count( $stats['clicks'] ) > 100 ) {
            $stats['clicks'] = array_slice( $stats['clicks'], 0, 100, true );
        }

        update_option( 'fas_search_stats', $stats );

        return new WP_REST_Response( array( 'success' => true ), 200 );
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

        // Optimized Cache Key based on term and language, using cache versioning
        $cache_version = get_option( 'fas_cache_version', 1 );
        $cache_key = 'fas_search_' . md5( $term . '_' . $lang . '_' . $cache_version );
        
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

        // Get IP
        $ip = '';
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
        } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        }
        
        $timestamp = current_time( 'mysql' );

        // Standardize lowercase matching
        $term_clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $term ) ) : strtolower( trim( $term ) );
        
        // Defer database writing to the shutdown hook so it does not block the REST API response
        add_action( 'shutdown', function() use ( $term_clean, $ip, $timestamp ) {
            $stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [] ) );

            if ( ! isset( $stats['total_count'] ) ) {
                $stats['total_count'] = 0;
            }
            $stats['total_count']++;

            if ( ! isset( $stats['terms'] ) ) {
                $stats['terms'] = array();
            }

            if ( ! isset( $stats['terms'][ $term_clean ] ) ) {
                $stats['terms'][ $term_clean ] = array(
                    'count' => 0,
                    'logs'  => array()
                );
            } else if ( ! is_array( $stats['terms'][ $term_clean ] ) ) {
                // Migrate old format
                $old_count = $stats['terms'][ $term_clean ];
                $stats['terms'][ $term_clean ] = array(
                    'count' => $old_count,
                    'logs'  => array()
                );
            }

            $stats['terms'][ $term_clean ]['count']++;
            
            // Add IP log, keep max 10 recent logs per term to prevent bloat
            array_unshift( $stats['terms'][ $term_clean ]['logs'], array( 'ip' => $ip, 'time' => $timestamp ) );
            if ( count( $stats['terms'][ $term_clean ]['logs'] ) > 10 ) {
                $stats['terms'][ $term_clean ]['logs'] = array_slice( $stats['terms'][ $term_clean ]['logs'], 0, 10 );
            }

            // Custom sort function to sort by count
            uasort( $stats['terms'], function( $a, $b ) {
                $count_a = is_array($a) ? $a['count'] : $a;
                $count_b = is_array($b) ? $b['count'] : $b;
                return $count_b <=> $count_a;
            } );

            // Keep top 100 popular terms to protect option storage
            if ( count( $stats['terms'] ) > 100 ) {
                $stats['terms'] = array_slice( $stats['terms'], 0, 100, true );
            }

            update_option( 'fas_search_stats', $stats );
        } );
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

        // Build combined arguments for keyword search
        $keyword_args = array(
            'post_type'      => $post_type,
            'posts_per_page' => 50,
            'post_status'    => 'publish',
            'fields'         => 'ids',
        );
        
        // Add a single custom filter to modify the SQL for the 's' parameter
        // to use OR logic on the different normalized terms.
        $search_filter = function( $search, $wp_query ) use ( $search_terms ) {
            global $wpdb;
            if ( empty( $search ) ) {
                return $search;
            }
            $search = '';
            foreach ( $search_terms as $term ) {
                $like = '%' . $wpdb->esc_like( $term ) . '%';
                $search .= $wpdb->prepare( " OR ({$wpdb->posts}.post_title LIKE %s) OR ({$wpdb->posts}.post_content LIKE %s)", $like, $like );
            }
            if ( ! empty( $search ) ) {
                $search = ' AND (' . ltrim( $search, ' OR' ) . ') ';
            }
            return $search;
        };
        add_filter( 'posts_search', $search_filter, 10, 2 );

        // This single WP_Query replaces the keyword N+1 queries.
        // We set 's' to an arbitrary string to trigger the posts_search filter
        $keyword_args['s'] = 'FAS_MAGIC_SEARCH_STRING';
        $query1 = new WP_Query( $keyword_args );
        if ( ! empty( $query1->posts ) ) {
            $all_ids = array_merge( $all_ids, $query1->posts );
        }

        // Clean up our filter
        remove_filter( 'posts_search', $search_filter, 10 );


        // Build combined arguments for ACF / metadata search
        if ( ! empty( $meta_keys ) ) {
            $meta_query = array( 'relation' => 'OR' );
            
            foreach ( $search_terms as $t ) {
                foreach ( $meta_keys as $key ) {
                    $meta_query[] = array(
                        'key'     => $key,
                        'value'   => $t,
                        'compare' => 'LIKE',
                    );
                }
            }

            $meta_args = array(
                'post_type'      => $post_type,
                'posts_per_page' => 50,
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'meta_query'     => $meta_query,
            );
            
            $query2 = new WP_Query( $meta_args );
            if ( ! empty( $query2->posts ) ) {
                $all_ids = array_merge( $all_ids, $query2->posts );
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

        $limit = FAS_Core::get_option( 'fas_results_count', 15 );
        if ( empty( $limit ) || $limit < 1 ) {
            $limit = 15;
        }

        // Limit each specific tab results
        $final_product_ids = array_slice( $product_ids, 0, $limit );
        $final_post_ids    = array_slice( $post_ids, 0, $limit );
        $final_page_ids    = array_slice( $page_ids, 0, $limit );

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
                        'id'           => $id,
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

        // "Did you mean" logic if no results
        if ( empty( $formatted_results['all'] ) ) {
            $formatted_results['did_you_mean'] = $this->get_did_you_mean_suggestion( $term );
        }

        return $formatted_results;
    }

    /**
     * Find the closest matching successfully searched term from stats.
     */
    private function get_did_you_mean_suggestion( $query ) {
        $stats = get_option( 'fas_search_stats', array() );
        if ( empty( $stats['terms'] ) ) {
            return '';
        }

        $query_clean = function_exists( 'mb_strtolower' ) ? mb_strtolower( trim( $query ) ) : strtolower( trim( $query ) );
        $best_match = '';
        $shortest_dist = -1;

        foreach ( $stats['terms'] as $term => $data ) {
            // Levenshtein function limits string length to 255
            if ( strlen( $query_clean ) > 255 || strlen( $term ) > 255 ) {
                continue;
            }

            // Calculate similarity (Levenshtein distance)
            $dist = levenshtein( $query_clean, $term );

            // Allow for typos (distance up to 3 for longer words, 1 for short words)
            $max_dist = strlen($query_clean) <= 4 ? 1 : 3;

            // Ensure distance is valid (not -1) and within threshold
            if ( $dist >= 0 && $dist <= $max_dist ) {
                if ( $shortest_dist === -1 || $dist < $shortest_dist ) {
                    // Make sure it's not the exact same query
                    if ( $query_clean !== $term ) {
                        $best_match = $term;
                        $shortest_dist = $dist;
                    }
                }
            }
        }

        return $best_match;
    }
}
