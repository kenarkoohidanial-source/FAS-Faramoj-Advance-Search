<?php
namespace Faramoj\AdvancedSearch\Api;

use Faramoj\AdvancedSearch\Core\Options;
use WP_REST_Request;
use WP_REST_Response;

class RestEndpoint {

    private $options;
    private $searchService;
    private $searchLogger;

    public function __construct(Options $options, SearchService $searchService, SearchLogger $searchLogger) {
        $this->options = $options;
        $this->searchService = $searchService;
        $this->searchLogger = $searchLogger;
    }

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
        $this->searchLogger->log_search_stats( $term );

        // Switch language context if WPML is active
        if ( function_exists( 'wpml_object_id_filter' ) && ! empty( $lang ) ) {
            do_action( 'wpml_switch_language', $lang );
        }

        // Switch language context if Polylang is active
        if ( function_exists( 'PLL' ) && ! empty( $lang ) ) {
            PLL()->curlang = PLL()->model->get_language( $lang );
        }

        // Custom Transient Cache Duration from Settings (default 1 hour)
        $cache_duration = $this->options->get_option( 'fas_cache_duration', HOUR_IN_SECONDS );
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
            $results = $this->searchService->execute_db_query( $term, $lang );
            if ( $cache_duration > 0 ) {
                set_transient( $cache_key, $results, intval( $cache_duration ) );
            }
        }

        return new WP_REST_Response( $results, 200 );
    }
}
