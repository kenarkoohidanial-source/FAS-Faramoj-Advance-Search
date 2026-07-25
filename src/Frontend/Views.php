<?php
namespace Faramoj\AdvancedSearch\Frontend;

use Faramoj\AdvancedSearch\Core\Options;

class Views {

    private $options;

    public function __construct(Options $options) {
        $this->options = $options;
    }

    /**
     * Render the search trigger button shortcode.
     */
    public function render_search_trigger( $atts ) {
        $atts = shortcode_atts( array(
            'label' => __( 'Search...', 'faramoj-search' ),
            'class' => '',
        ), $atts, 'fas_search_trigger' );

        ob_start();
        ?>
        <button class="fas-search-trigger <?php echo esc_attr( $atts['class'] ); ?>">
            <svg class="fas-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; vertical-align: middle !important; width: 16px !important; height: 16px !important; visibility: visible !important;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span style="vertical-align: middle !important;"><?php echo esc_html( $atts['label'] ); ?></span>
        </button>
        <?php
        return ob_get_clean();
    }

    /**
     * Inject Search Modal overlay to the footer.
     */
    public function inject_search_modal() {
        $theme_template = locate_template( 'faramoj-advanced-search/search-modal.php' );
        if ( $theme_template ) {
            include $theme_template;
        } else {
            include plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/search-modal.php';
        }
    }

    /**
     * Conditionally inject the floating search button into wp_footer.
     */
    public function maybe_inject_floating_trigger() {
        $enable_floating = $this->options->get_option( 'fas_enable_floating', 'yes' );
        if ( 'yes' !== $enable_floating ) {
            return;
        }

        $display_type = $this->options->get_option( 'fas_display_pages_type', 'all' );
        if ( 'none' === $display_type ) {
            return;
        }

        // Handle Page Visibility conditions
        if ( 'specific' === $display_type ) {
            $specific_input = $this->options->get_option( 'fas_display_specific_pages', '' );
            if ( empty( $specific_input ) ) {
                return;
            }

            // Split items by comma
            $pages_arr = array_map( 'trim', explode( ',', $specific_input ) );
            $should_show = false;

            foreach ( $pages_arr as $val ) {
                if ( empty( $val ) ) {
                    continue;
                }
                // Check if numeric page ID or matching slug/title
                if ( is_numeric( $val ) && is_page( intval( $val ) ) ) {
                    $should_show = true;
                    break;
                } elseif ( is_page( $val ) || is_single( $val ) ) {
                    $should_show = true;
                    break;
                }
            }

            if ( ! $should_show ) {
                return;
            }
        }

        $position = $this->options->get_option( 'fas_floating_position', 'bottom-right' );
        ?>
        <button class="fas-search-trigger fas-floating-trigger fas-position-<?php echo esc_attr( $position ); ?>" aria-label="<?php esc_attr_e( 'Search', 'faramoj-search' ); ?>">
            <svg class="fas-search-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; width: 22px !important; height: 22px !important; visibility: visible !important;">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </button>
        <?php
    }
}
