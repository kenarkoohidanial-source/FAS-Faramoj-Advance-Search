<?php
/**
 * FAS Indexer Service
 * Maintains the custom search index table for hyper-fast querying.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class FAS_Indexer {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'fas_search_index';
    }

    /**
     * Create the custom table schema.
     */
    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$this->table_name} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            post_id bigint(20) unsigned NOT NULL,
            post_type varchar(20) NOT NULL,
            normalized_title text NOT NULL,
            normalized_content longtext NOT NULL,
            normalized_meta longtext NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY post_id (post_id),
            KEY post_type (post_type)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Index a specific post by tokenizing its content and saving to the custom table.
     */
    public function index_post( $post_id ) {
        if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
            return;
        }

        $post = get_post( $post_id );
        if ( ! $post || $post->post_status !== 'publish' ) {
            $this->remove_post( $post_id );
            return;
        }

        // Only index products, posts, and pages
        if ( ! in_array( $post->post_type, array( 'product', 'post', 'page' ) ) ) {
            return;
        }

        // Get metadata to index (e.g., specific ACF fields or SKUs)
        $meta_keys = array( 'technical_specifications', 'frequency_range', '_sku' );
        $meta_values = array();
        foreach ( $meta_keys as $key ) {
            $val = get_post_meta( $post_id, $key, true );
            if ( ! empty( $val ) ) {
                $meta_values[] = is_array( $val ) ? implode( ' ', $val ) : $val;
            }
        }
        $meta_string = implode( ' ', $meta_values );

        global $wpdb;

        $wpdb->replace(
            $this->table_name,
            array(
                'post_id'            => $post->ID,
                'post_type'          => $post->post_type,
                'normalized_title'   => FAS_Tokenizer::tokenize_to_string( $post->post_title ),
                'normalized_content' => FAS_Tokenizer::tokenize_to_string( wp_strip_all_tags( $post->post_content ) ),
                'normalized_meta'    => FAS_Tokenizer::tokenize_to_string( $meta_string ),
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s',
                '%s'
            )
        );
    }

    /**
     * Remove a post from the index.
     */
    public function remove_post( $post_id ) {
        global $wpdb;
        $wpdb->delete( $this->table_name, array( 'post_id' => $post_id ), array( '%d' ) );
    }

    /**
     * Process a chunk of posts into the index.
     */
    public function sync_chunk( $offset, $limit ) {
        global $wpdb;
        $posts = $wpdb->get_results( $wpdb->prepare( "
            SELECT ID FROM {$wpdb->posts}
            WHERE post_status = 'publish'
            AND post_type IN ('product', 'post', 'page')
            ORDER BY ID ASC
            LIMIT %d OFFSET %d
        ", $limit, $offset ) );

        if ( empty( $posts ) ) {
            return 0;
        }

        foreach ( $posts as $p ) {
            $this->index_post( $p->ID );
        }

        return count( $posts );
    }

    /**
     * Helper to get total indexable posts count.
     */
    public function get_total_indexable_posts() {
        global $wpdb;
        return (int) $wpdb->get_var( "
            SELECT COUNT(ID) FROM {$wpdb->posts}
            WHERE post_status = 'publish'
            AND post_type IN ('product', 'post', 'page')
        " );
    }

    /**
     * AJAX handler for safe chunked synchronization.
     */
    public function ajax_sync_chunk() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }

        $offset = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
        $limit  = 50; // Safe chunk limit per request

        global $wpdb;
        // If starting fresh, truncate the table
        if ( $offset === 0 ) {
            $wpdb->query( "TRUNCATE TABLE {$this->table_name}" );
        }

        $processed = $this->sync_chunk( $offset, $limit );
        $total     = $this->get_total_indexable_posts();

        if ( $processed === 0 || ( $offset + $processed ) >= $total ) {
            update_option( 'fas_index_built', '1' );
            wp_send_json_success( array( 'done' => true, 'total' => $total ) );
        } else {
            wp_send_json_success( array(
                'done'   => false,
                'offset' => $offset + $processed,
                'total'  => $total
            ) );
        }
    }
}

/**
 * WP-CLI Support for FAS Indexer
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    class FAS_CLI_Indexer {
        /**
         * Rebuild the Faramoj Advanced Search Index.
         *
         * ## EXAMPLES
         *
         *     wp fas index --sync
         *
         * @when after_wp_load
         */
        public function index( $args, $assoc_args ) {
            if ( isset( $assoc_args['sync'] ) ) {
                WP_CLI::line( 'Starting FAS Index Sync...' );
                $indexer = new FAS_Indexer();

                global $wpdb;
                $wpdb->query( "TRUNCATE TABLE " . $wpdb->prefix . "fas_search_index" );

                $total = $indexer->get_total_indexable_posts();
                $progress = \WP_CLI\Utils\make_progress_bar( 'Indexing Posts', $total );

                $offset = 0;
                $limit = 100;
                while ( true ) {
                    $processed = $indexer->sync_chunk( $offset, $limit );
                    if ( $processed === 0 ) {
                        break;
                    }
                    $progress->tick( $processed );
                    $offset += $processed;
                }
                $progress->finish();
                update_option( 'fas_index_built', '1' );
                WP_CLI::success( "Search index built successfully!" );
            }
        }
    }
    WP_CLI::add_command( 'fas', 'FAS_CLI_Indexer' );
}
