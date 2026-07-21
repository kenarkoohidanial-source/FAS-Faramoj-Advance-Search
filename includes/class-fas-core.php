<?php
/**
 * Core Plugin Class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAS_Core {

    /**
     * Helper to get active language suffix dynamically.
     */
    public static function get_lang_suffix() {
        $lang = 'en'; // default fallback
        if ( function_exists( 'pll_current_language' ) ) {
            $pll_lang = pll_current_language();
            if ( ! empty( $pll_lang ) ) {
                $lang = $pll_lang;
            }
        } elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $lang = ICL_LANGUAGE_CODE;
        } else {
            // Guess fallback from WordPress locale (e.g. fa_IR or en_US)
            $locale = get_locale();
            if ( strpos( $locale, 'fa' ) === 0 ) {
                $lang = 'fa';
            }
        }
        return '_' . $lang;
    }

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

        // Auto-inject Floating Trigger button into footer if enabled
        add_action( 'wp_footer', array( $this, 'maybe_inject_floating_trigger' ), 9 );
    }

    /**
     * Enqueue CSS & Javascript assets.
     */
    public function enqueue_assets() {
        // Enqueue Dashicons for beautiful tab icons in the frontend
        wp_enqueue_style( 'dashicons' );

        wp_enqueue_style(
            'fas-public-css',
            plugins_url( 'public/css/fas-public.css', dirname( __FILE__ ) ),
            array(),
            '1.1.1'
        );

        wp_enqueue_script(
            'fas-public-js',
            plugins_url( 'public/js/fas-public.js', dirname( __FILE__ ) ),
            array(),
            '1.1.1',
            true
        );

        // Fetch dynamic locale
        $current_lang = 'en';
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language();
        } elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $current_lang = ICL_LANGUAGE_CODE;
        }

        $suffix = self::get_lang_suffix();

        // Fetch custom tabs sequence
        $tabs_order = get_option( 'fas_tabs_order' . $suffix, 'all,products,posts,docs' );
        $tabs_order_arr = array_map( 'trim', explode( ',', $tabs_order ) );

        wp_localize_script( 'fas-public-js', 'fas_params', array(
            'ajax_url'   => esc_url_raw( rest_url( 'fas/v1/search' ) ),
            'lang'       => sanitize_text_field( $current_lang ),
            'nonce'      => wp_create_nonce( 'wp_rest' ),
            'tabs_order' => $tabs_order_arr,
            'i18n'       => array(
                'placeholder' => __( 'Search products, articles, docs...', 'faramoj-search' ),
                'no_results'  => __( 'No results found', 'faramoj-search' ),
                'searching'   => __( 'Searching...', 'faramoj-search' ),
            )
        ) );

        // Inject custom floating brand color and popup dimensions dynamically with active language suffix
        $floating_bg       = get_option( 'fas_floating_bg' . $suffix, '#0066cc' );
        $popup_width       = get_option( 'fas_popup_width' . $suffix, 750 );
        $popup_max_height  = get_option( 'fas_popup_max_height' . $suffix, 600 );
        $floating_offset_x = get_option( 'fas_floating_offset_x' . $suffix, 24 );
        $floating_offset_y = get_option( 'fas_floating_offset_y' . $suffix, 24 );

        $custom_inline_css = "
            :root {
                --fas-primary: " . esc_attr( $floating_bg ) . ";
                --fas-popup-width: " . esc_attr( $popup_width ) . "px;
                --fas-popup-max-height: " . esc_attr( $popup_max_height ) . "px;
                --fas-offset-x: " . esc_attr( $floating_offset_x ) . "px;
                --fas-offset-y: " . esc_attr( $floating_offset_y ) . "px;
            }
        ";
        wp_add_inline_style( 'fas-public-css', $custom_inline_css );
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
            <svg class="fas-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; vertical-align: middle !important; width: 16px !important; height: 16px !important; visibility: visible !important;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span style="vertical-align: middle !important;"><?php echo esc_html( $atts['label'] ); ?></span>
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

    /**
     * Conditionally inject the floating search button into wp_footer.
     */
    public function maybe_inject_floating_trigger() {
        $suffix = self::get_lang_suffix();

        $enable_floating = get_option( 'fas_enable_floating' . $suffix, 'yes' );
        if ( 'yes' !== $enable_floating ) {
            return;
        }

        $display_type = get_option( 'fas_display_pages_type' . $suffix, 'all' );
        if ( 'none' === $display_type ) {
            return;
        }

        // Handle Page Visibility conditions
        if ( 'specific' === $display_type ) {
            $specific_input = get_option( 'fas_display_specific_pages' . $suffix, '' );
            if ( empty( $specific_input ) ) {
                return;
            }

            // Split items by comma
            $pages_arr = array_map( 'trim', explode( ',', $specific_input ) );
            $should_show = false;

            foreach ( $pages_arr as $val ) {
                if ( empty( $val ) ) {
                    continue;
                }
                // Check if numeric page ID or matching slug/title
                if ( is_numeric( $val ) && is_page( intval( $val ) ) ) {
                    $should_show = true;
                    break;
                } elseif ( is_page( $val ) || is_single( $val ) ) {
                    $should_show = true;
                    break;
                }
            }

            if ( ! $should_show ) {
                return;
            }
        }

        $position = get_option( 'fas_floating_position' . $suffix, 'bottom-right' );
        ?>
        <button class="fas-search-trigger fas-floating-trigger fas-position-<?php echo esc_attr( $position ); ?>" aria-label="<?php esc_attr_e( 'Search', 'faramoj-search' ); ?>">
            <svg class="fas-search-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; width: 22px !important; height: 22px !important; visibility: visible !important;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
        <?php
    }
}
