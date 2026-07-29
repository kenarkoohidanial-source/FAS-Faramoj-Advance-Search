<?php
/**
 * Plugin Name: Faramoj Advanced Search
 * Description: A highly optimized, modern multilingual search engine with ACF support.
 * Version: 1.2.6
 * Author: Danial Kenarkoohi
 * Text Domain: faramoj-search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Autoload Classes
require_once plugin_dir_path( __FILE__ ) . 'includes/class-fas-core.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-fas-i18n.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-fas-rest.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/class-fas-admin.php';

function run_faramoj_advanced_search() {
    $plugin = new FAS_Core();
    $plugin->run();

    // Load Elementor integration if Elementor is active
    if ( did_action( 'elementor/loaded' ) ) {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-fas-elementor.php';
        new FAS_Elementor();
    }
}
add_action( 'plugins_loaded', 'run_faramoj_advanced_search' );
