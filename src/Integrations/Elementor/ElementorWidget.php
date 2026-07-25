<?php
namespace Faramoj\AdvancedSearch\Integrations\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    return; // Changed from exit to return for testing purpose, though this shouldn't be executed in CLI
}

class ElementorWidget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'fas_search_trigger';
    }

    public function get_title() {
        return esc_html__( 'Faramoj Search Trigger', 'faramoj-search' );
    }

    public function get_icon() {
        return 'eicon-search';
    }

    public function get_categories() {
        return [ 'general' ];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__( 'Settings', 'faramoj-search' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'label_text',
            [
                'label' => esc_html__( 'Button Label', 'faramoj-search' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__( 'Search...', 'faramoj-search' ),
                'placeholder' => esc_html__( 'Type button label', 'faramoj-search' ),
            ]
        );

        $this->add_control(
            'button_layout',
            [
                'label' => esc_html__( 'Layout Mode', 'faramoj-search' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'button',
                'options' => [
                    'button' => esc_html__( 'Button with Icon', 'faramoj-search' ),
                    'icon'   => esc_html__( 'Icon Only', 'faramoj-search' ),
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $label = ! empty( $settings['label_text'] ) ? $settings['label_text'] : '';
        $layout = ! empty( $settings['button_layout'] ) ? $settings['button_layout'] : 'button';

        if ( 'icon' === $layout ) {
            ?>
            <button class="fas-search-trigger fas-elementor-icon-trigger" aria-label="<?php esc_attr_e( 'Search', 'faramoj-search' ); ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
            </button>
            <?php
        } else {
            ?>
            <button class="fas-search-trigger fas-elementor-btn-trigger">
                <svg class="fas-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span><?php echo esc_html( $label ); ?></span>
            </button>
            <?php
        }
    }
}
