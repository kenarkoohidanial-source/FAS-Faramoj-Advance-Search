<?php

$file = 'includes/class-fas-rest.php';
$content = file_get_contents($file);

$old_method = '    /**
     * Fetch all unique post IDs matching search terms in content or metadata.
     *
     * @param string $post_type The post type to search in.
     * @param array $search_terms Array of search terms.
     * @param array $meta_keys Array of meta keys to search in.
     * @return array Array of matched post IDs.
     */
    private function get_matched_post_ids( $post_type, $search_terms, $meta_keys = [] ) {
        $all_ids = array();

        // Build combined arguments for keyword search
        $keyword_args = array(
            \'post_type\'      => $post_type,
            \'posts_per_page\' => 50,
            \'post_status\'    => \'publish\',
            \'fields\'         => \'ids\',
        );

        // Add a single custom filter to modify the SQL for the \'s\' parameter
        // to use OR logic on the different normalized terms.
        $search_filter = function( $search, $wp_query ) use ( $search_terms ) {
            global $wpdb;
            if ( empty( $search ) ) {
                return $search;
            }
            $search = \'\';
            foreach ( $search_terms as $term ) {
                $like = \'%\' . $wpdb->esc_like( $term ) . \'%\';
                $search .= $wpdb->prepare( " OR ({$wpdb->posts}.post_title LIKE %s) OR ({$wpdb->posts}.post_content LIKE %s)", $like, $like );
            }
            if ( ! empty( $search ) ) {
                $search = \' AND (\' . ltrim( $search, \' OR\' ) . \') \';
            }
            return $search;
        };
        add_filter( \'posts_search\', $search_filter, 10, 2 );

        // This single WP_Query replaces the keyword N+1 queries.
        // We set \'s\' to an arbitrary string to trigger the posts_search filter
        $keyword_args[\'s\'] = \'FAS_MAGIC_SEARCH_STRING\';
        $query1 = new WP_Query( $keyword_args );
        if ( ! empty( $query1->posts ) ) {
            $all_ids = array_merge( $all_ids, $query1->posts );
        }

        // Clean up our filter
        remove_filter( \'posts_search\', $search_filter, 10 );


        // Build combined arguments for ACF / metadata search
        if ( ! empty( $meta_keys ) ) {
            $meta_query = array( \'relation\' => \'OR\' );

            foreach ( $search_terms as $t ) {
                foreach ( $meta_keys as $key ) {
                    $meta_query[] = array(
                        \'key\'     => $key,
                        \'value\'   => $t,
                        \'compare\' => \'LIKE\',
                    );
                }
            }

            $meta_args = array(
                \'post_type\'      => $post_type,
                \'posts_per_page\' => 50,
                \'post_status\'    => \'publish\',
                \'fields\'         => \'ids\',
                \'meta_query\'     => $meta_query,
            );

            $query2 = new WP_Query( $meta_args );
            if ( ! empty( $query2->posts ) ) {
                $all_ids = array_merge( $all_ids, $query2->posts );
            }
        }

        return array_unique( $all_ids );
    }';

$new_method = '    /**
     * Fetch all unique post IDs matching search terms in content or metadata via raw SQL for maximum performance.
     *
     * @param string $post_type The post type to search in.
     * @param array $search_terms Array of search terms.
     * @param array $meta_keys Array of meta keys to search in.
     * @return array Array of matched post IDs.
     */
    private function get_matched_post_ids( $post_type, $search_terms, $meta_keys = [] ) {
        global $wpdb;

        if ( empty( $search_terms ) ) {
            return array();
        }

        $query_parts = array();
        $query_args  = array( $post_type ); // First parameter for post_type

        // 1. Build Title and Content Search Block
        $search_blocks = array();
        foreach ( $search_terms as $term ) {
            $like = \'%\' . $wpdb->esc_like( $term ) . \'%\';
            $search_blocks[] = "({$wpdb->posts}.post_title LIKE %s OR {$wpdb->posts}.post_content LIKE %s)";
            $query_args[] = $like;
            $query_args[] = $like;
        }
        $query_parts[] = "(" . implode( " OR ", $search_blocks ) . ")";

        // 2. Build Meta Search Block (ACF) if applicable
        if ( ! empty( $meta_keys ) ) {
            $meta_blocks = array();
            foreach ( $search_terms as $term ) {
                $like = \'%\' . $wpdb->esc_like( $term ) . \'%\';
                foreach ( $meta_keys as $key ) {
                    $meta_blocks[] = "({$wpdb->postmeta}.meta_key = %s AND {$wpdb->postmeta}.meta_value LIKE %s)";
                    $query_args[] = $key;
                    $query_args[] = $like;
                }
            }
            if ( ! empty( $meta_blocks ) ) {
                $query_parts[] = "(" . implode( " OR ", $meta_blocks ) . ")";
            }
        }

        // 3. Assemble Final Unified Query
        $where_clause = implode( " OR ", $query_parts );

        $sql = "
            SELECT DISTINCT {$wpdb->posts}.ID
            FROM {$wpdb->posts}
        ";

        if ( ! empty( $meta_keys ) ) {
            $sql .= " LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ";
        }

        $sql .= "
            WHERE {$wpdb->posts}.post_type = %s
            AND {$wpdb->posts}.post_status = \'publish\'
            AND ( {$where_clause} )
            LIMIT 50
        ";

        $prepared_sql = $wpdb->prepare( $sql, $query_args );

        // Execute direct query
        $results = $wpdb->get_col( $prepared_sql );

        return ! empty( $results ) ? array_map( \'intval\', $results ) : array();
    }';

$content = str_replace($old_method, $new_method, $content);
file_put_contents($file, $content);
echo "Done\n";
