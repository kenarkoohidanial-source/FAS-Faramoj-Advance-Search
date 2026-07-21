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
        add_options_page(
            __( 'Faramoj Advanced Search Settings', 'faramoj-search' ),
            __( 'Faramoj Search', 'faramoj-search' ),
            'manage_options',
            'faramoj-search',
            array( $this, 'render_settings_page' )
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

        // Correct enqueue hook registration
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
        if ( 'settings_page_faramoj-search' !== $hook ) {
            return;
        }

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
