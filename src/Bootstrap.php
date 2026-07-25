<?php
namespace Faramoj\AdvancedSearch;

class Bootstrap {
    private $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function boot() {
        // Core Setup
        $i18n = $this->container->get(Core\I18n::class);
        add_action( 'plugins_loaded', array( $i18n, 'load_plugin_textdomain' ) );

        // REST API
        $rest = $this->container->get(Api\RestEndpoint::class);
        add_action( 'rest_api_init', array( $rest, 'register_routes' ) );

        // Admin
        if ( is_admin() ) {
            $admin = $this->container->get(Admin\AdminSettings::class);
            add_action( 'admin_menu', array( $admin, 'add_admin_menu' ) );
            add_action( 'admin_init', array( $admin, 'register_settings' ) );
        }

        // Frontend
        $assets = $this->container->get(Frontend\Assets::class);
        add_action( 'wp_enqueue_scripts', array( $assets, 'enqueue_assets' ) );

        $views = $this->container->get(Frontend\Views::class);
        add_shortcode( 'fas_search_trigger', array( $views, 'render_search_trigger' ) );
        add_action( 'wp_footer', array( $views, 'inject_search_modal' ) );
        add_action( 'wp_footer', array( $views, 'maybe_inject_floating_trigger' ), 9 );

        // Integrations
        if ( did_action( 'elementor/loaded' ) ) {
            $this->container->get(Integrations\Elementor\ElementorIntegration::class);
        }
    }
}
