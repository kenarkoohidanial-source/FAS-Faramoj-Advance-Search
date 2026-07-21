<?php
/**
 * HTML layout for the admin panel with Live Interactive Preview
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap fas-admin-wrap">
    <div class="fas-header">
        <span class="fas-logo-badge"><?php esc_html_e( 'FAS', 'faramoj-search' ); ?></span>
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Faramoj Advanced Search', 'faramoj-search' ); ?></h1>
    </div>
    <hr class="wp-header-end">

    <div class="fas-dashboard-grid" style="display: flex; gap: 32px; align-items: flex-start; margin-top: 20px;">

        <!-- Column 1: Settings Form -->
        <div class="fas-col-settings" style="flex: 1; max-width: 60%;">
            <form method="post" action="options.php" id="fas-settings-form">
                <?php
                settings_fields( 'fas_settings_group' );
                do_settings_sections( 'fas_settings_group' );

                $cache_duration         = get_option( 'fas_cache_duration', HOUR_IN_SECONDS );
                $theme_mode             = get_option( 'fas_theme_mode', 'dark' );
                $enable_floating        = get_option( 'fas_enable_floating', 'yes' );
                $floating_position      = get_option( 'fas_floating_position', 'bottom-right' );
                $display_pages_type     = get_option( 'fas_display_pages_type', 'all' );
                $display_specific_pages = get_option( 'fas_display_specific_pages', '' );
                $floating_bg            = get_option( 'fas_floating_bg', '#0066cc' );

                $popup_width            = get_option( 'fas_popup_width', 750 );
                $popup_max_height       = get_option( 'fas_popup_max_height', 600 );
                $tabs_order             = get_option( 'fas_tabs_order', 'all,products,posts,docs' );

                $tab_all_title          = get_option( 'fas_tab_all_title', 'All Results' );
                $tab_all_color          = get_option( 'fas_tab_all_color', '#0066cc' );
                $tab_all_icon           = get_option( 'fas_tab_all_icon', 'dashicons-grid-view' );

                $tab_products_title     = get_option( 'fas_tab_products_title', 'Products' );
                $tab_products_color     = get_option( 'fas_tab_products_color', '#10b981' );
                $tab_products_icon      = get_option( 'fas_tab_products_icon', 'dashicons-cart' );

                $tab_posts_title        = get_option( 'fas_tab_posts_title', 'News & Articles' );
                $tab_posts_color        = get_option( 'fas_tab_posts_color', '#f59e0b' );
                $tab_posts_icon         = get_option( 'fas_tab_posts_icon', 'dashicons-welcome-write-blog' );

                $tab_docs_title         = get_option( 'fas_tab_docs_title', 'Documentation' );
                $tab_docs_color         = get_option( 'fas_tab_docs_color', '#6366f1' );
                $tab_docs_icon          = get_option( 'fas_tab_docs_icon', 'dashicons-book-alt' );
                ?>

                <!-- Engine & Cache -->
                <div class="fas-card">
                    <h3><?php esc_html_e( 'Search Engine & Performance Settings', 'faramoj-search' ); ?></h3>
                    <table class="form-table fas-form-table">
                        <tr>
                            <th scope="row">
                                <label for="fas_cache_duration"><?php esc_html_e( 'Cache Duration (Transient)', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="fas_cache_duration" id="fas_cache_duration" value="<?php echo esc_attr( $cache_duration ); ?>" class="regular-text" min="0">
                                <span class="fas-description"><?php esc_html_e( 'Time in seconds to cache search results. Set to 0 to disable transient caching.', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Custom Popup Layout & Dimensions -->
                <div class="fas-card">
                    <h3><?php esc_html_e( 'Popup Layout & Dimensions', 'faramoj-search' ); ?></h3>
                    <table class="form-table fas-form-table">
                        <tr>
                            <th scope="row">
                                <label for="fas_theme_mode"><?php esc_html_e( 'Theme Accent Mode', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <select name="fas_theme_mode" id="fas_theme_mode" class="regular-text">
                                    <option value="dark" <?php selected( $theme_mode, 'dark' ); ?>><?php esc_html_e( 'Deep Slate Dark Mode Overlay', 'faramoj-search' ); ?></option>
                                    <option value="light" <?php selected( $theme_mode, 'light' ); ?>><?php esc_html_e( 'Clean Corporate Light Mode Overlay', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="fas_popup_width"><?php esc_html_e( 'Popup Max Width (px)', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="fas_popup_width" id="fas_popup_width" value="<?php echo esc_attr( $popup_width ); ?>" class="regular-text" min="300" max="1500">
                                <span class="fas-description"><?php esc_html_e( 'Width of the modal container. Recommended: 750px.', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="fas_popup_max_height"><?php esc_html_e( 'Popup Max Height (px)', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="fas_popup_max_height" id="fas_popup_max_height" value="<?php echo esc_attr( $popup_max_height ); ?>" class="regular-text" min="300" max="1200">
                                <span class="fas-description"><?php esc_html_e( 'Maximum height of the result box. Recommended: 600px.', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Tabs Customization & Ordering -->
                <div class="fas-card">
                    <h3><?php esc_html_e( 'Tabs Sort Order & Titles', 'faramoj-search' ); ?></h3>
                    <p class="description" style="margin-bottom: 15px;">
                        <?php esc_html_e( 'Set the exact order of tabs by listing their keys separated by commas (allowed keys: all, products, posts, docs).', 'faramoj-search' ); ?>
                    </p>
                    <table class="form-table fas-form-table">
                        <tr>
                            <th scope="row">
                                <label for="fas_tabs_order"><?php esc_html_e( 'Tabs Order List', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="fas_tabs_order" id="fas_tabs_order" value="<?php echo esc_attr( $tabs_order ); ?>" class="regular-text">
                                <span class="fas-description"><?php esc_html_e( 'Default: all,products,posts,docs', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>

                        <!-- Tab 1: All -->
                        <tr>
                            <th scope="row"><strong>[All]</strong> <?php esc_html_e( 'Title, Color & Icon', 'faramoj-search' ); ?></th>
                            <td>
                                <input type="text" name="fas_tab_all_title" id="fas_tab_all_title" value="<?php echo esc_attr( $tab_all_title ); ?>" class="regular-text" placeholder="All Results" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_all_icon" id="fas_tab_all_icon" value="<?php echo esc_attr( $tab_all_icon ); ?>" class="regular-text" placeholder="dashicons-grid-view" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_all_color" id="fas_tab_all_color" value="<?php echo esc_attr( $tab_all_color ); ?>" class="fas-color-picker">
                            </td>
                        </tr>

                        <!-- Tab 2: Products -->
                        <tr>
                            <th scope="row"><strong>[Products]</strong> <?php esc_html_e( 'Title, Color & Icon', 'faramoj-search' ); ?></th>
                            <td>
                                <input type="text" name="fas_tab_products_title" id="fas_tab_products_title" value="<?php echo esc_attr( $tab_products_title ); ?>" class="regular-text" placeholder="Products" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_products_icon" id="fas_tab_products_icon" value="<?php echo esc_attr( $tab_products_icon ); ?>" class="regular-text" placeholder="dashicons-cart" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_products_color" id="fas_tab_products_color" value="<?php echo esc_attr( $tab_products_color ); ?>" class="fas-color-picker">
                            </td>
                        </tr>

                        <!-- Tab 3: Posts -->
                        <tr>
                            <th scope="row"><strong>[News/Posts]</strong> <?php esc_html_e( 'Title, Color & Icon', 'faramoj-search' ); ?></th>
                            <td>
                                <input type="text" name="fas_tab_posts_title" id="fas_tab_posts_title" value="<?php echo esc_attr( $tab_posts_title ); ?>" class="regular-text" placeholder="News & Articles" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_posts_icon" id="fas_tab_posts_icon" value="<?php echo esc_attr( $tab_posts_icon ); ?>" class="regular-text" placeholder="dashicons-welcome-write-blog" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_posts_color" id="fas_tab_posts_color" value="<?php echo esc_attr( $tab_posts_color ); ?>" class="fas-color-picker">
                            </td>
                        </tr>

                        <!-- Tab 4: Docs -->
                        <tr>
                            <th scope="row"><strong>[Docs/Pages]</strong> <?php esc_html_e( 'Title, Color & Icon', 'faramoj-search' ); ?></th>
                            <td>
                                <input type="text" name="fas_tab_docs_title" id="fas_tab_docs_title" value="<?php echo esc_attr( $tab_docs_title ); ?>" class="regular-text" placeholder="Documentation" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_docs_icon" id="fas_tab_docs_icon" value="<?php echo esc_attr( $tab_docs_icon ); ?>" class="regular-text" placeholder="dashicons-book-alt" style="margin-bottom:6px;"><br>
                                <input type="text" name="fas_tab_docs_color" id="fas_tab_docs_color" value="<?php echo esc_attr( $tab_docs_color ); ?>" class="fas-color-picker">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Floating Trigger Button settings -->
                <div class="fas-card">
                    <h3><?php esc_html_e( 'Floating Search Trigger Button Settings', 'faramoj-search' ); ?></h3>
                    <table class="form-table fas-form-table">
                        <tr>
                            <th scope="row">
                                <label for="fas_enable_floating"><?php esc_html_e( 'Enable Floating Button', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <select name="fas_enable_floating" id="fas_enable_floating" class="regular-text">
                                    <option value="yes" <?php selected( $enable_floating, 'yes' ); ?>><?php esc_html_e( 'Enabled', 'faramoj-search' ); ?></option>
                                    <option value="no" <?php selected( $enable_floating, 'no' ); ?>><?php esc_html_e( 'Disabled', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="fas_floating_position"><?php esc_html_e( 'Floating Button Position', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <select name="fas_floating_position" id="fas_floating_position" class="regular-text">
                                    <option value="bottom-right" <?php selected( $floating_position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'faramoj-search' ); ?></option>
                                    <option value="bottom-left" <?php selected( $floating_position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'faramoj-search' ); ?></option>
                                    <option value="top-right" <?php selected( $floating_position, 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'faramoj-search' ); ?></option>
                                    <option value="top-left" <?php selected( $floating_position, 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="fas_display_pages_type"><?php esc_html_e( 'Display Pages', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <select name="fas_display_pages_type" id="fas_display_pages_type" class="regular-text">
                                    <option value="all" <?php selected( $display_pages_type, 'all' ); ?>><?php esc_html_e( 'Show on All Pages', 'faramoj-search' ); ?></option>
                                    <option value="specific" <?php selected( $display_pages_type, 'specific' ); ?>><?php esc_html_e( 'Show on Specific Pages Only', 'faramoj-search' ); ?></option>
                                    <option value="none" <?php selected( $display_pages_type, 'none' ); ?>><?php esc_html_e( 'Disable Automatic Output (Manual Shortcode Only)', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr id="fas_specific_pages_row" style="<?php echo ( 'specific' === $display_pages_type ) ? '' : 'display:none;'; ?>">
                            <th scope="row">
                                <label for="fas_display_specific_pages"><?php esc_html_e( 'Specific Page IDs or Slugs', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="fas_display_specific_pages" id="fas_display_specific_pages" value="<?php echo esc_attr( $display_specific_pages ); ?>" class="regular-text" placeholder="e.g. 12, home, contact-us">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="fas_floating_bg"><?php esc_html_e( 'Floating Button Brand Color', 'faramoj-search' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="fas_floating_bg" id="fas_floating_bg" value="<?php echo esc_attr( $floating_bg ); ?>" class="fas-color-picker">
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button(); ?>
            </form>
        </div>

        <!-- Column 2: LIVE INTERACTIVE PREVIEW -->
        <div class="fas-col-preview" style="flex: 1; position: sticky; top: 50px; background: #fff; border: 1px solid #ccd0d4; padding: 24px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0; border-bottom: 1px solid #eee; padding-bottom:12px; display:flex; align-items:center; gap:8px;">
                <span style="background: #e11d48; color: #fff; font-size:10px; padding: 2px 6px; border-radius:12px; font-weight:bold; text-transform:uppercase;"><?php esc_html_e( 'Live', 'faramoj-search' ); ?></span>
                <?php esc_html_e( 'Modal Overlay Live Preview', 'faramoj-search' ); ?>
            </h3>

            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
                <?php esc_html_e( 'The live preview below reflects your color accents, tab titles, reordering, and popup dimension changes in real-time.', 'faramoj-search' ); ?>
            </p>

            <!-- Interactive Mock Search Container -->
            <div id="fas-mock-modal-wrapper" style="background: rgba(18, 24, 36, 0.05); padding: 30px 20px; border-radius: 8px; display: flex; justify-content: center; overflow: hidden; max-height: 480px;">

                <div id="fas-preview-container" class="fas-search-container" style="width: 100%; max-width: 100%; border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden;">

                    <!-- Input -->
                    <div class="fas-search-input-wrapper" style="display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid #e2e8f0;">
                        <span class="dashicons dashicons-search" style="color: #64748b; margin-right: 8px;"></span>
                        <input type="text" placeholder="Type to search..." style="border:none; outline:none; background:transparent; width:100%; font-size:15px; color:#1e293b;" disabled>
                        <span class="dashicons dashicons-no-alt" style="color:#64748b; font-size:18px; cursor:not-allowed;"></span>
                    </div>

                    <!-- Tabs -->
                    <div id="fas-preview-tabs" class="fas-search-tabs" style="display: flex; background: #f8fafc; border-bottom: 1px solid #e2e8f0; overflow-x: auto;">
                        <!-- Generated dynamically by Javascript -->
                    </div>

                    <!-- Results box mock -->
                    <div id="fas-preview-results" style="padding: 16px; background:#fff; min-height: 120px;">
                        <div class="fas-result-item" style="display: flex; align-items: center; gap: 12px; padding: 8px; border-radius: 8px; background: #f8fafc; border: 1px solid #f1f5f9; margin-bottom: 8px;">
                            <div style="width: 40px; height: 40px; border-radius: 6px; background: #e2e8f0; display:flex; align-items:center; justify-content:center; color:#64748b;">
                                <span class="dashicons dashicons-products"></span>
                            </div>
                            <div>
                                <h4 style="margin: 0 0 2px 0; font-size: 14px; color:#1e293b; font-weight:600;">Phase-30ISO Antenna</h4>
                                <p style="margin:0; font-size:12px; color:#64748b;">Premium dual-band technical telecommunication product spec...</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Initialize standard WP color pickers
    $('.fas-color-picker').wpColorPicker({
        change: function(event, ui) {
            // Trigger live preview update on color change
            setTimeout(updateLivePreview, 50);
        }
    });

    // Handle Page Display Select changes
    $('#fas_display_pages_type').on('change', function() {
        if ($(this).val() === 'specific') {
            $('#fas_specific_pages_row').show();
        } else {
            $('#fas_specific_pages_row').hide();
        }
    });

    // Re-render live preview on settings modification
    $('#fas-settings-form input, #fas-settings-form select').on('input change', updateLivePreview);

    function updateLivePreview() {
        // Dimensions
        var width = $('#fas_popup_width').val() || 750;
        var maxH  = $('#fas_popup_max_height').val() || 600;
        $('#fas-preview-container').css({
            'max-width': width + 'px',
            'max-height': maxH + 'px'
        });

        // Theme Accent Mode
        var mode = $('#fas_theme_mode').val();
        if (mode === 'dark') {
            $('#fas-preview-container').css({
                'background-color': '#121824',
                'border-color': '#1e293b'
            });
            $('#fas-preview-tabs').css({
                'background-color': '#0f172a',
                'border-bottom-color': '#1e293b'
            });
            $('#fas-preview-results').css('background-color', '#121824');
            $('#fas-preview-results .fas-result-item').css({
                'background-color': '#1e293b',
                'border-color': '#334155'
            });
            $('#fas-preview-results h4').css('color', '#f1f5f9');
            $('#fas-preview-results p').css('color', '#94a3b8');
            $('#fas-preview-container input').css('color', '#f1f5f9');
        } else {
            $('#fas-preview-container').css({
                'background-color': '#ffffff',
                'border-color': '#e2e8f0'
            });
            $('#fas-preview-tabs').css({
                'background-color': '#f8fafc',
                'border-bottom-color': '#e2e8f0'
            });
            $('#fas-preview-results').css('background-color', '#ffffff');
            $('#fas-preview-results .fas-result-item').css({
                'background-color': '#f8fafc',
                'border-color': '#f1f5f9'
            });
            $('#fas-preview-results h4').css('color', '#1e293b');
            $('#fas-preview-results p').css('color', '#64748b');
            $('#fas-preview-container input').css('color', '#1e293b');
        }

        // Parse Tabs Configuration & Order
        var orderRaw = $('#fas_tabs_order').val() || 'all,products,posts,docs';
        var orderArr = orderRaw.split(',').map(function(item) { return item.trim(); });

        var tabsHtml = '';
        var activeClassAdded = false;

        orderArr.forEach(function(key) {
            var title = '';
            var color = '';
            var icon  = '';

            if (key === 'all') {
                title = $('#fas_tab_all_title').val() || 'All Results';
                color = $('#fas_tab_all_color').val() || '#0066cc';
                icon  = $('#fas_tab_all_icon').val() || 'dashicons-grid-view';
            } else if (key === 'products') {
                title = $('#fas_tab_products_title').val() || 'Products';
                color = $('#fas_tab_products_color').val() || '#10b981';
                icon  = $('#fas_tab_products_icon').val() || 'dashicons-cart';
            } else if (key === 'posts') {
                title = $('#fas_tab_posts_title').val() || 'News & Articles';
                color = $('#fas_tab_posts_color').val() || '#f59e0b';
                icon  = $('#fas_tab_posts_icon').val() || 'dashicons-welcome-write-blog';
            } else if (key === 'docs') {
                title = $('#fas_tab_docs_title').val() || 'Documentation';
                color = $('#fas_tab_docs_color').val() || '#6366f1';
                icon  = $('#fas_tab_docs_icon').val() || 'dashicons-book-alt';
            } else {
                return; // skip unknown keys
            }

            // Create styles inline for preview
            var isActive = !activeClassAdded;
            activeClassAdded = true;

            var tabStyle = 'display: flex; align-items: center; gap: 6px; padding: 10px 14px; border: none; background: none; font-weight: 500; font-size:13px; cursor: pointer; color: ' + (isActive ? color : '#64748b') + '; border-bottom: 2px solid ' + (isActive ? color : 'transparent') + ';';
            tabsHtml += '<button type="button" style="' + tabStyle + '">' +
                        '<span class="dashicons ' + icon + '" style="font-size:16px; width:16px; height:16px; color:' + (isActive ? color : '#94a3b8') + ';"></span>' +
                        '<span>' + title + '</span>' +
                        '</button>';
        });

        $('#fas-preview-tabs').html(tabsHtml);
    }

    // Initial load
    updateLivePreview();
});
</script>
