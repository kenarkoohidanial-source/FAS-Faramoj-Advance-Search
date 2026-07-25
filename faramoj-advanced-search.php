<?php
/**
 * Plugin Name: Faramoj Advanced Search
 * Description: A highly optimized, modern multilingual search engine with ACF support.
 * Version: 1.1.5
 * Author: Danial Kenarkoohi
 * Text Domain: faramoj-search
 */

if ( ! defined( 'ABSPATH' ) ) {
    return; // Exit if accessed directly.
}

// Load Composer Autoloader
if ( file_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
}

use Faramoj\AdvancedSearch\Container;
use Faramoj\AdvancedSearch\Bootstrap;
use Faramoj\AdvancedSearch\Core\Options;
use Faramoj\AdvancedSearch\Core\I18n;
use Faramoj\AdvancedSearch\Admin\AdminSettings;
use Faramoj\AdvancedSearch\Api\SearchLogger;
use Faramoj\AdvancedSearch\Api\SearchService;
use Faramoj\AdvancedSearch\Api\RestEndpoint;
use Faramoj\AdvancedSearch\Frontend\Assets;
use Faramoj\AdvancedSearch\Frontend\Views;
use Faramoj\AdvancedSearch\Integrations\Elementor\ElementorIntegration;

function run_faramoj_advanced_search() {
    $container = new Container();

    // Register Services
    $container->set(Options::class, function() {
        return new Options();
    });

    $container->set(I18n::class, function() {
        return new I18n();
    });

    $container->set(AdminSettings::class, function() {
        return new AdminSettings();
    });

    $container->set(SearchLogger::class, function() {
        return new SearchLogger();
    });

    $container->set(SearchService::class, function($c) {
        return new SearchService($c->get(Options::class));
    });

    $container->set(RestEndpoint::class, function($c) {
        return new RestEndpoint(
            $c->get(Options::class),
            $c->get(SearchService::class),
            $c->get(SearchLogger::class)
        );
    });

    $container->set(Assets::class, function($c) {
        return new Assets($c->get(Options::class));
    });

    $container->set(Views::class, function($c) {
        return new Views($c->get(Options::class));
    });

    $container->set(ElementorIntegration::class, function() {
        return new ElementorIntegration();
    });

    $bootstrap = new Bootstrap($container);
    $bootstrap->boot();
}

add_action( 'plugins_loaded', 'run_faramoj_advanced_search' );
