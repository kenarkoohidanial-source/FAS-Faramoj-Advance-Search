<?php
namespace Faramoj\AdvancedSearch\Core;

class Options {
    /**
     * Helper to get active language suffix dynamically.
     */
    public function get_lang_suffix() {
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
     * Safely retrieve a plugin option with dynamic language suffix and multifold fallback logic.
     */
    public function get_option( $key, $default = '' ) {
        $suffix = $this->get_lang_suffix();
        $val = get_option( $key . $suffix );

        // If not found, try fallback to no suffix
        if ( false === $val || '' === $val ) {
            $val = get_option( $key );
        }

        // If still not found, try common suffix fallbacks (fa or en)
        if ( false === $val || '' === $val ) {
            $alt_suffix = ( '_fa' === $suffix ) ? '_en' : '_fa';
            $val = get_option( $key . $alt_suffix );
        }

        // Return default if everything is empty
        if ( false === $val || '' === $val ) {
            return $default;
        }

        return $val;
    }
}
