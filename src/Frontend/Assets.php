<?php
namespace Faramoj\AdvancedSearch\Frontend;

use Faramoj\AdvancedSearch\Core\Options;

class Assets {

    private $options;

    public function __construct(Options $options) {
        $this->options = $options;
    }

    /**
     * Enqueue CSS & Javascript assets.
     */
    public function enqueue_assets() {
        // Enqueue Dashicons for beautiful tab icons in the frontend
        wp_enqueue_style( 'dashicons' );

        $plugin_dir_url = plugin_dir_url( dirname( dirname( __FILE__ ) ) );

        wp_enqueue_style(
            'fas-public-css',
            $plugin_dir_url . 'public/css/fas-public.css',
            array(),
            '1.1.5'
        );

        wp_enqueue_script(
            'fas-public-js',
            $plugin_dir_url . 'public/js/fas-public.js',
            array(),
            '1.1.5',
            true
        );

        // Fetch dynamic locale
        $current_lang = 'en';
        if ( function_exists( 'pll_current_language' ) ) {
            $current_lang = pll_current_language();
        } elseif ( defined( 'ICL_LANGUAGE_CODE' ) ) {
            $current_lang = ICL_LANGUAGE_CODE;
        } else {
            $locale = get_locale();
            if ( strpos( $locale, 'fa' ) === 0 ) {
                $current_lang = 'fa';
            }
        }

        // Fetch custom tabs sequence
        $tabs_order = $this->options->get_option( 'fas_tabs_order', 'all,products,posts,docs' );
        $tabs_order_arr = array_map( 'trim', explode( ',', $tabs_order ) );

        // Dynamically translate placeholder text according to loaded frontend language
        $is_fa = ( 'fa' === $current_lang );
        $placeholder_text = $is_fa ? 'جستجو در محصولات، مقالات، مستندات...' : 'Search products, articles, docs...';
        $no_results_text  = $is_fa ? 'هیچ نتیجه‌ای یافت نشد' : 'No results found';
        $searching_text   = $is_fa ? 'در حال جستجو...' : 'Searching...';

        wp_localize_script( 'fas-public-js', 'fas_params', array(
            'ajax_url'   => esc_url_raw( rest_url( 'fas/v1/search' ) ),
            'lang'       => sanitize_text_field( $current_lang ),
            'nonce'      => wp_create_nonce( 'wp_rest' ),
            'tabs_order' => $tabs_order_arr,
            'i18n'       => array(
                'placeholder' => $placeholder_text,
                'no_results'  => $no_results_text,
                'searching'   => $searching_text,
            )
        ) );

        // Inject custom floating brand color and popup dimensions dynamically
        $floating_bg       = $this->options->get_option( 'fas_floating_bg', '#0066cc' );
        $popup_width       = $this->options->get_option( 'fas_popup_width', 750 );
        $popup_max_height  = $this->options->get_option( 'fas_popup_max_height', 600 );
        $floating_offset_x = $this->options->get_option( 'fas_floating_offset_x', 24 );
        $floating_offset_y = $this->options->get_option( 'fas_floating_offset_y', 24 );

        $btn_size_desktop = $this->options->get_option( 'fas_btn_size_desktop', 56 );
        $btn_size_mobile  = $this->options->get_option( 'fas_btn_size_mobile', 48 );
        $title_size_desktop = $this->options->get_option( 'fas_title_size_desktop', 15 );
        $title_size_mobile  = $this->options->get_option( 'fas_title_size_mobile', 14 );
        $excerpt_size_desktop = $this->options->get_option( 'fas_excerpt_size_desktop', 13 );
        $excerpt_size_mobile  = $this->options->get_option( 'fas_excerpt_size_mobile', 12 );

        $custom_inline_css = "
            :root {
                --fas-primary: " . esc_attr( $floating_bg ) . ";
                --fas-popup-width: " . esc_attr( $popup_width ) . "px;
                --fas-popup-max-height: " . esc_attr( $popup_max_height ) . "px;
                --fas-offset-x: " . esc_attr( $floating_offset_x ) . "px;
                --fas-offset-y: " . esc_attr( $floating_offset_y ) . "px;
                --fas-btn-size-desktop: " . esc_attr( $btn_size_desktop ) . "px;
                --fas-btn-size-mobile: " . esc_attr( $btn_size_mobile ) . "px;
                --fas-title-size-desktop: " . esc_attr( $title_size_desktop ) . "px;
                --fas-title-size-mobile: " . esc_attr( $title_size_mobile ) . "px;
                --fas-excerpt-size-desktop: " . esc_attr( $excerpt_size_desktop ) . "px;
                --fas-excerpt-size-mobile: " . esc_attr( $excerpt_size_mobile ) . "px;
            }
        ";
        wp_add_inline_style( 'fas-public-css', $custom_inline_css );
    }
}
