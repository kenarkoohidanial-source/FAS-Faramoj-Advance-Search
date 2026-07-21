<?php
/**
 * Admin settings page logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAS_Admin {

    /**
     * Helper to get active site languages (WPML, Polylang, or Fallbacks)
     */
    public function get_active_languages() {
        $langs = array();

        if ( function_exists( 'pll_languages_list' ) ) {
            // Polylang
            $raw_langs = pll_languages_list( array( 'fields' => 'all' ) );
            if ( ! empty( $raw_langs ) ) {
                foreach ( $raw_langs as $l ) {
                    $langs[ $l->slug ] = $l->name;
                }
            }
        } elseif ( function_exists( 'icl_get_languages' ) ) {
            // WPML
            $raw_langs = icl_get_languages();
            if ( ! empty( $raw_langs ) ) {
                foreach ( $raw_langs as $l ) {
                    $langs[ $l['language_code'] ] = $l['translated_name'];
                }
            }
        }

        // Fallback Farsi and English standard configurations if no plugins active
        if ( empty( $langs ) ) {
            $langs['fa'] = 'فارسی (Persian)';
            $langs['en'] = 'English';
        }

        return $langs;
    }

    /**
     * Add options page and submenus to the WordPress menu.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Faramoj Advanced Search Settings', 'faramoj-search' ),
            __( 'Faramoj Search', 'faramoj-search' ),
            'manage_options',
            'faramoj-search',
            array( $this, 'render_settings_page' ),
            'dashicons-search', // Beautiful search dashicon
            80 // Placement in menu
        );

        add_submenu_page(
            'faramoj-search',
            __( 'Statistics', 'faramoj-search' ),
            __( 'Statistics', 'faramoj-search' ),
            'manage_options',
            'fas-statistics',
            array( $this, 'render_statistics_page' )
        );

        add_submenu_page(
            'faramoj-search',
            __( 'About Us', 'faramoj-search' ),
            __( 'About Us', 'faramoj-search' ),
            'manage_options',
            'fas-about-us',
            array( $this, 'render_about_us_page' )
        );
    }

    /**
     * Register plugin settings dynamically for each active language.
     */
    public function register_settings() {
        $langs = $this->get_active_languages();

        foreach ( array_keys( $langs ) as $lang_code ) {
            $suffix = '_' . $lang_code;

            register_setting( 'fas_settings_group', 'fas_cache_duration' . $suffix, array(
                'type'              => 'integer',
                'sanitize_callback' => 'intval',
                'default'           => HOUR_IN_SECONDS,
            ) );

            register_setting( 'fas_settings_group', 'fas_theme_mode' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'dark',
            ) );

            register_setting( 'fas_settings_group', 'fas_enable_floating' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'yes',
            ) );

            register_setting( 'fas_settings_group', 'fas_floating_position' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'bottom-right',
            ) );

            register_setting( 'fas_settings_group', 'fas_display_pages_type' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'all',
            ) );

            register_setting( 'fas_settings_group', 'fas_display_specific_pages' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '',
            ) );

            register_setting( 'fas_settings_group', 'fas_floating_bg' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '#0066cc',
            ) );

            register_setting( 'fas_settings_group', 'fas_floating_offset_x' . $suffix, array(
                'type'              => 'integer',
                'sanitize_callback' => 'intval',
                'default'           => 24,
            ) );

            register_setting( 'fas_settings_group', 'fas_floating_offset_y' . $suffix, array(
                'type'              => 'integer',
                'sanitize_callback' => 'intval',
                'default'           => 24,
            ) );

            register_setting( 'fas_settings_group', 'fas_popup_width' . $suffix, array(
                'type'              => 'integer',
                'sanitize_callback' => 'intval',
                'default'           => 750,
            ) );

            register_setting( 'fas_settings_group', 'fas_popup_max_height' . $suffix, array(
                'type'              => 'integer',
                'sanitize_callback' => 'intval',
                'default'           => 600,
            ) );

            register_setting( 'fas_settings_group', 'fas_tabs_order' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'all,products,posts,docs',
            ) );

            // All Results
            register_setting( 'fas_settings_group', 'fas_tab_all_title' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'All Results',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_all_color' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '#0066cc',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_all_icon' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'dashicons-grid-view',
            ) );

            // Products
            register_setting( 'fas_settings_group', 'fas_tab_products_title' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'Products',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_products_color' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '#10b981',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_products_icon' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'dashicons-cart',
            ) );

            // Posts
            register_setting( 'fas_settings_group', 'fas_tab_posts_title' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'News & Articles',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_posts_color' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '#f59e0b',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_posts_icon' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'dashicons-welcome-write-blog',
            ) );

            // Docs
            register_setting( 'fas_settings_group', 'fas_tab_docs_title' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'Documentation',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_docs_color' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => '#6366f1',
            ) );
            register_setting( 'fas_settings_group', 'fas_tab_docs_icon' . $suffix, array(
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default'           => 'dashicons-book-alt',
            ) );
        }

        // Flush cached transients whenever admin settings are saved to database
        add_action( 'update_option_fas_cache_duration', array( $this, 'flush_search_cache' ) );
        add_action( 'updated_option', array( $this, 'maybe_flush_search_cache' ), 10, 3 );

        // Correct enqueue hook registration during settings initialization
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
    }

    /**
     * Flush all transient search results to clear any stale cache immediately.
     */
    public function flush_search_cache() {
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_fas_search_%' OR option_name LIKE '_transient_timeout_fas_search_%'" );
    }

    /**
     * Trigger flush search cache if any of our options are saved.
     */
    public function maybe_flush_search_cache( $option, $old_value, $value ) {
        if ( strpos( $option, 'fas_' ) === 0 ) {
            $this->flush_search_cache();
        }
    }

    /**
     * Render the settings page HTML layout.
     */
    public function render_settings_page() {
        $view_path = plugin_dir_path( __FILE__ ) . 'views/settings-page.php';
        if ( file_exists( $view_path ) ) {
            include $view_path;
        } else {
            echo '<div class="wrap"><h2>' . esc_html__( 'Faramoj Advanced Search', 'faramoj-search' ) . '</h2><p>' . esc_html__( 'Error: Settings view file not found.', 'faramoj-search' ) . '</p></div>';
        }
    }

    /**
     * Render the search query statistics metrics.
     */
    public function render_statistics_page() {
        $view_path = plugin_dir_path( __FILE__ ) . 'views/statistics-page.php';
        if ( file_exists( $view_path ) ) {
            include $view_path;
        } else {
            echo '<div class="wrap"><h2>' . esc_html__( 'Faramoj Advanced Search Statistics', 'faramoj-search' ) . '</h2><p>' . esc_html__( 'Error: Statistics view file not found.', 'faramoj-search' ) . '</p></div>';
        }
    }

    /**
     * Render the About Us information.
     */
    public function render_about_us_page() {
        $view_path = plugin_dir_path( __FILE__ ) . 'views/about-us-page.php';
        if ( file_exists( $view_path ) ) {
            include $view_path;
        } else {
            echo '<div class="wrap"><h2>' . esc_html__( 'About Us', 'faramoj-search' ) . '</h2><p>' . esc_html__( 'Error: About Us view file not found.', 'faramoj-search' ) . '</p></div>';
        }
    }

    /**
     * Enqueue Admin Panel Custom Styles.
     */
    public function enqueue_admin_assets( $hook ) {
        // Enqueue styles only on our specific plugin submenus
        $pages = array(
            'toplevel_page_faramoj-search',
            'faramoj-search_page_fas-statistics',
            'faramoj-search_page_fas-about-us'
        );

        if ( ! in_array( $hook, $pages, true ) ) {
            return;
        }

        // Enqueue WP Color Picker and standard jQuery UI Sortable scripts/styles
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui-sortable' );

        // Enqueue an empty stylesheet handle so we can safely add inline styles to it
        wp_register_style( 'fas-admin-css', false );
        wp_enqueue_style( 'fas-admin-css' );

        // Read CSS from local PHP-constructed view or string directly to avoid hardened server blockages
        ob_start();
        include plugin_dir_path( __FILE__ ) . 'css/fas-admin.php';
        $custom_css = ob_get_clean();

        // Strip PHP tags if any (our css file has <?php header... but contains css below)
        $custom_css = preg_replace( '/^<\?php.*?\?>/s', '', $custom_css );

        wp_add_inline_style( 'fas-admin-css', $custom_css );
    }
}
