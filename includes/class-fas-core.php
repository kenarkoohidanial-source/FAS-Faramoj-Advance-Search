<?php
/**
 * Core Plugin Class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAS_Core {

    /**
     * Run the orchestrator.
     */
    public function run() {
        // Load Internationalization
        $i18n = new FAS_I18n();
        add_action( 'plugins_loaded', array( $i18n, 'load_plugin_textdomain' ) );

        // Initialize REST API
        $rest = new FAS_Rest();
        add_action( 'rest_api_init', array( $rest, 'register_routes' ) );

        // Admin Panel Settings
        if ( is_admin() ) {
            $admin = new FAS_Admin();
            add_action( 'admin_menu', array( $admin, 'add_admin_menu' ) );
            add_action( 'admin_init', array( $admin, 'register_settings' ) );
        }

        // Frontend Styles & Scripts
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

        // Register shortcode to trigger search
        add_shortcode( 'fas_search_trigger', array( $this, 'render_search_trigger' ) );

        // Inject the search modal markup into the footer
        add_action( 'wp_footer', array( $this, 'inject_search_modal' ) );
    }

    /**
     * Enqueue CSS & Javascript assets.
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'fas-public-css',
            plugins_url( 'public/css/fas-public.css', dirname( __FILE__ ) ),
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'fas-public-js',
            plugins_url( 'public/js/fas-public.js', dirname( __FILE__ ) ),
            array(),
            '1.0.0',
            true
        );

        // Fetch dynamic locale
        $current_lang = 'en';
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language();
        } elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $current_lang = ICL_LANGUAGE_CODE;
        }

        wp_localize_script( 'fas-public-js', 'fas_params', array(
            'ajax_url' => esc_url_raw( rest_url( 'fas/v1/search' ) ),
            'lang'     => sanitize_text_field( $current_lang ),
            'nonce'    => wp_create_nonce( 'wp_rest' ),
            'i18n'     => array(
                'placeholder' => __( 'Search products, articles, docs...', 'faramoj-search' ),
                'no_results'  => __( 'No results found', 'faramoj-search' ),
                'products'    => __( 'Products', 'faramoj-search' ),
                'posts'       => __( 'News & Articles', 'faramoj-search' ),
                'docs'        => __( 'Documentation', 'faramoj-search' ),
                'searching'   => __( 'Searching...', 'faramoj-search' ),
            )
        ) );
    }

    /**
     * Render the search trigger button shortcode.
     */
    public function render_search_trigger( $atts ) {
        $atts = shortcode_atts( array(
            'label' => __( 'Search...', 'faramoj-search' ),
            'class' => '',
        ), $atts, 'fas_search_trigger' );

        ob_start();
        ?>
        <button class="fas-search-trigger <?php echo esc_attr( $atts['class'] ); ?>">
            <svg class="fas-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span><?php echo esc_html( $atts['label'] ); ?></span>
        </button>
        <?php
        return ob_get_clean();
    }

    /**
     * Inject Search Modal overlay to the footer.
     */
    public function inject_search_modal() {
        $theme_template = locate_template( 'faramoj-advanced-search/search-modal.php' );
        if ( $theme_template ) {
            include $theme_template;
        } else {
            include plugin_dir_path( dirname( __FILE__ ) ) . 'templates/search-modal.php';
        }
    }
}
