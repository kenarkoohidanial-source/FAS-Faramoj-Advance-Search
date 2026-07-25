<?php

$file = 'includes/class-fas-core.php';
$content = file_get_contents($file);

$old_block = '    /**
     * Enqueue CSS & Javascript assets.
     */
    public function enqueue_assets() {
        // Enqueue Dashicons for beautiful tab icons in the frontend
        wp_enqueue_style( \'dashicons\' );';

$new_block = '    /**
     * Enqueue CSS & Javascript assets only when required.
     */
    public function enqueue_assets() {
        // Optimization: Do not load on backend (admin side) if called globally by mistake
        if ( is_admin() ) {
            return;
        }

        // Optimization: Check if floating trigger is disabled globally and shortcode is missing
        $enable_floating = self::get_option( \'fas_enable_floating\', \'yes\' );
        global $post;

        $has_shortcode = is_a( $post, \'WP_Post\' ) && has_shortcode( $post->post_content, \'fas_search_trigger\' );

        if ( \'yes\' !== $enable_floating && ! $has_shortcode ) {
            // Also check Elementor if active
            $has_elementor_widget = false;
            if ( class_exists( \'\Elementor\Plugin\' ) && is_a( $post, \'WP_Post\' ) ) {
                $document = \Elementor\Plugin::$instance->documents->get( $post->ID );
                if ( $document && $document->is_built_with_elementor() ) {
                     // Very basic check since parsing full Elementor data can be heavy
                     $elementor_data = get_post_meta( $post->ID, \'_elementor_data\', true );
                     if ( strpos( $elementor_data, \'fas-search-widget\' ) !== false ) {
                         $has_elementor_widget = true;
                     }
                }
            }
            if ( ! $has_elementor_widget ) {
                return; // Nothing needs the search popup on this page
            }
        }

        // Handle page visibility logic from settings
        if ( \'yes\' === $enable_floating ) {
            $display_type = self::get_option( \'fas_display_pages_type\', \'all\' );
            if ( \'none\' === $display_type && ! $has_shortcode ) {
                return;
            }
            if ( \'specific\' === $display_type && ! $has_shortcode ) {
                $specific_input = self::get_option( \'fas_display_specific_pages\', \'\' );
                if ( empty( $specific_input ) ) {
                    return;
                }
                $pages_arr = array_map( \'trim\', explode( \',\', $specific_input ) );
                $should_show = false;
                foreach ( $pages_arr as $val ) {
                    if ( empty( $val ) ) continue;
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
        }

        // Enqueue Dashicons for beautiful tab icons in the frontend
        wp_enqueue_style( \'dashicons\' );';

$content = str_replace($old_block, $new_block, $content);
file_put_contents($file, $content);
echo "Done\n";
