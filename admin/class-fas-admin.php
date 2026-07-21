<?php
/**
 * Admin settings page logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAS_Admin {

    /**
     * Add options page to the WordPress menu.
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
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        register_setting( 'fas_settings_group', 'fas_cache_duration', array(
            'type'              => 'integer',
            'sanitize_callback' => 'intval',
            'default'           => HOUR_IN_SECONDS,
        ) );

        register_setting( 'fas_settings_group', 'fas_theme_mode', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'dark',
        ) );

        // Floating button configuration options
        register_setting( 'fas_settings_group', 'fas_enable_floating', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'yes',
        ) );

        register_setting( 'fas_settings_group', 'fas_floating_position', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'bottom-right',
        ) );

        register_setting( 'fas_settings_group', 'fas_display_pages_type', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'all', // 'all', 'specific', 'none'
        ) );

        register_setting( 'fas_settings_group', 'fas_display_specific_pages', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ) );

        register_setting( 'fas_settings_group', 'fas_floating_bg', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '#0066cc',
        ) );

        // Popup dimensions
        register_setting( 'fas_settings_group', 'fas_popup_width', array(
            'type'              => 'integer',
            'sanitize_callback' => 'intval',
            'default'           => 750,
        ) );

        register_setting( 'fas_settings_group', 'fas_popup_max_height', array(
            'type'              => 'integer',
            'sanitize_callback' => 'intval',
            'default'           => 600,
        ) );

        // Tabs Customization
        register_setting( 'fas_settings_group', 'fas_tabs_order', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'all,products,posts,docs',
        ) );

        // All Results Tab
        register_setting( 'fas_settings_group', 'fas_tab_all_title', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'All Results',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_all_color', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '#0066cc',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_all_icon', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'dashicons-grid-view',
        ) );

        // Products Tab
        register_setting( 'fas_settings_group', 'fas_tab_products_title', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'Products',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_products_color', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '#10b981',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_products_icon', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'dashicons-cart',
        ) );

        // Posts/News Tab
        register_setting( 'fas_settings_group', 'fas_tab_posts_title', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'News & Articles',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_posts_color', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '#f59e0b',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_posts_icon', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'dashicons-welcome-write-blog',
        ) );

        // Documentation/Pages Tab
        register_setting( 'fas_settings_group', 'fas_tab_docs_title', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'Documentation',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_docs_color', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '#6366f1',
        ) );
        register_setting( 'fas_settings_group', 'fas_tab_docs_icon', array(
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => 'dashicons-book-alt',
        ) );

        // Correct enqueue hook registration during settings initialization
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
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
     * Enqueue Admin Panel Custom Styles.
     */
    public function enqueue_admin_assets( $hook ) {
        // Only load on our settings page to avoid style conflicts
        if ( 'toplevel_page_faramoj-search' !== $hook ) {
            return;
        }

        // Enqueue WP Color Picker styles and scripts
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

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
