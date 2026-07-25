<?php
$file = 'includes/class-fas-rest.php';
$content = file_get_contents($file);

$old_block = '        // Optimized Cache Key based on term and language, using cache versioning
        $cache_version = get_option( \'fas_cache_version\', 1 );
        $cache_key = \'fas_search_\' . md5( $term . \'_\' . $lang . \'_\' . $cache_version );

        $results = $this->execute_db_query( $term, $lang );

        return new WP_REST_Response( $results, 200 );';


$new_block = '        // Optimized Cache Key based on term and language, using cache versioning
        $cache_version = get_option( \'fas_cache_version\', 1 );
        $cache_key = \'fas_search_\' . md5( $term . \'_\' . $lang . \'_\' . $cache_version );

        // Attempt to fetch from Transient API cache first
        $cached_results = get_transient( $cache_key );
        if ( false !== $cached_results ) {
            return new WP_REST_Response( $cached_results, 200 );
        }

        $results = $this->execute_db_query( $term, $lang );

        // Save the full HTML array to transient
        set_transient( $cache_key, $results, $cache_duration );

        return new WP_REST_Response( $results, 200 );';

$content = str_replace($old_block, $new_block, $content);
file_put_contents($file, $content);
echo "Done\n";
