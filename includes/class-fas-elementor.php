<?php
/**
 * Elementor integration handler for Faramoj Advanced Search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class FAS_Elementor {

    public function __construct() {
        // Register custom Elementor widget
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
    }

    /**
     * Register Search trigger widget in Elementor
     */
    public function register_widgets( $widgets_manager ) {
        require_once plugin_dir_path( __FILE__ ) . 'class-fas-elementor-widget.php';
        $widgets_manager->register( new FAS_Elementor_Widget() );
    }
}
