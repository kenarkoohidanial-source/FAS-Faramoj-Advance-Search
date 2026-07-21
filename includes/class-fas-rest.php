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

        // Switch language context if WPML is active
        if ( function_exists( 'wpml_object_id_filter' ) && ! empty( $lang ) ) {
            do_action( 'wpml_switch_language', $lang );
        }

        // Switch language context if Polylang is active
        if ( function_exists( 'PLL' ) && ! empty( $lang ) ) {
            PLL()->curlang = PLL()->model->get_language( $lang );
        }

        // Custom Transient Cache Duration from Settings (default 1 hour)
        $cache_duration = get_option( 'fas_cache_duration', HOUR_IN_SECONDS );
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
            $results = $this->execute_db_query( $term );
            if ( $cache_duration > 0 ) {
                set_transient( $cache_key, $results, intval( $cache_duration ) );
            }
        }

        return new WP_REST_Response( $results, 200 );
    }

    /**
     * Helper to highlight the query in a text safely.
     */
    private function highlight_text( $text, $term ) {
        $escaped = esc_html( $text );
        if ( empty( $term ) ) {
            return $escaped;
        }
        $quoted_term = preg_quote( $term, '/' );
        return preg_replace(
            '/(' . $quoted_term . ')/i',
            '<mark class="fas-highlight">$1</mark>',
            $escaped
        );
    }

    private function execute_db_query( $term ) {
        // Querying Products with Title, Content, and ACF Technical Specs
        // Example: Searching 'VHF-04E' antenna specifications
        $args = array(
            'post_type'      => array( 'product', 'post', 'page' ),
            'posts_per_page' => 20,
            's'              => $term,
            'post_status'    => 'publish',
            'meta_query'     => array(
                'relation' => 'OR',
                array(
                    'key'     => 'technical_specifications', // Target ACF field
                    'value'   => $term,
                    'compare' => 'LIKE',
                ),
                array(
                    'key'     => 'frequency_range', // Target ACF field
                    'value'   => $term,
                    'compare' => 'LIKE',
                )
            )
        );

        $query = new WP_Query( $args );
        $formatted_results = array( 'products' => [], 'posts' => [], 'docs' => [] );

        if ( $query->have_posts() ) {
            // Find overridable template path
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

                $category = 'docs';
                if ( $type === 'product' ) {
                    $category = 'products';
                } elseif ( $type === 'post' ) {
                    $category = 'posts';
                }

                // Render item through standard overridable templates
                ob_start();
                $args = array( 'item' => $item, 'category' => $category );
                include $template_path;
                $rendered_html = ob_get_clean();

                if ( $category === 'products' ) {
                    $formatted_results['products'][] = array( 'html' => $rendered_html );
                } elseif ( $category === 'posts' ) {
                    $formatted_results['posts'][] = array( 'html' => $rendered_html );
                } else {
                    $formatted_results['docs'][] = array( 'html' => $rendered_html );
                }
            }
            wp_reset_postdata();
        }

        return $formatted_results;
    }
}
