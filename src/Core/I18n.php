<?php
namespace Faramoj\AdvancedSearch\Core;

class I18n {

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'faramoj-search',
            false,
            dirname( dirname( dirname( plugin_basename( __FILE__ ) ) ) ) . '/languages/'
        );
    }

    /**
     * Get the active language code dynamically.
     */
    public function get_current_language() {
        $lang = 'en';

        if ( function_exists( 'pll_current_language' ) ) {
            $lang = pll_current_language();
        } elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $lang = ICL_LANGUAGE_CODE;
        } elseif ( isset( $_GET['lang'] ) ) {
            $lang = sanitize_text_field( $_GET['lang'] );
        }

        return $lang;
    }
}
