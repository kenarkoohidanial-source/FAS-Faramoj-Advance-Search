<?php
namespace Faramoj\AdvancedSearch\Api;

use Faramoj\AdvancedSearch\Core\Options;
use WP_Query;

class SearchService {

    private $options;

    public function __construct(Options $options) {
        $this->options = $options;
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

    public function execute_db_query( $term, $lang ) {
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

        $limit = $this->options->get_option( 'fas_results_count', 15 );
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
                    $template_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/search-result-item.php';
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
