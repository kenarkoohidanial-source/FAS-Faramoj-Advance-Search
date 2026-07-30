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
     * Full sync to process all existing published posts into the index.
     */
    public function sync_all() {
        global $wpdb;
        // Truncate first to avoid duplication issues
        $wpdb->query( "TRUNCATE TABLE {$this->table_name}" );

        // Process in chunks to prevent memory exhaustion
        $offset = 0;
        $limit = 100;

        while ( true ) {
            $posts = $wpdb->get_results( $wpdb->prepare( "
                SELECT ID FROM {$wpdb->posts}
                WHERE post_status = 'publish'
                AND post_type IN ('product', 'post', 'page')
                LIMIT %d OFFSET %d
            ", $limit, $offset ) );

            if ( empty( $posts ) ) {
                break;
            }

            foreach ( $posts as $p ) {
                $this->index_post( $p->ID );
            }

            $offset += $limit;
        }
    }
}
