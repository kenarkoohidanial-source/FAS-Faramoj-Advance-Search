<?php
namespace Faramoj\AdvancedSearch\Integrations\Elementor;

class ElementorIntegration {

    public function __construct() {
        // Register custom Elementor widget
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
    }

    /**
     * Register Search trigger widget in Elementor
     */
    public function register_widgets( $widgets_manager ) {
        $widgets_manager->register( new ElementorWidget() );
    }
}
