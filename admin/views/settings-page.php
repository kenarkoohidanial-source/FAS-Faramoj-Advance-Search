<?php
/**
 * HTML layout for the admin panel with Live Interactive Preview & Multilingual settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve active languages
$langs = $this->get_active_languages();

// Detect active language suffix
$active_lang = isset( $_GET['fas_lang'] ) ? sanitize_text_field( $_GET['fas_lang'] ) : 'en';
if ( ! isset( $langs[ $active_lang ] ) ) {
    reset( $langs );
    $active_lang = key( $langs );
}

$suffix = '_' . $active_lang;

// Retrieve options specific to the selected language context
$cache_duration         = get_option( 'fas_cache_duration' . $suffix, HOUR_IN_SECONDS );
$theme_mode             = get_option( 'fas_theme_mode' . $suffix, 'dark' );
$enable_floating        = get_option( 'fas_enable_floating' . $suffix, 'yes' );
$floating_position      = get_option( 'fas_floating_position' . $suffix, 'bottom-right' );
$display_pages_type     = get_option( 'fas_display_pages_type' . $suffix, 'all' );
$display_specific_pages = get_option( 'fas_display_specific_pages' . $suffix, '' );
$floating_bg            = get_option( 'fas_floating_bg' . $suffix, '#0066cc' );

$floating_offset_x      = get_option( 'fas_floating_offset_x' . $suffix, 24 );
$floating_offset_y      = get_option( 'fas_floating_offset_y' . $suffix, 24 );

$popup_width            = get_option( 'fas_popup_width' . $suffix, 750 );
$popup_max_height       = get_option( 'fas_popup_max_height' . $suffix, 600 );
$tabs_order             = get_option( 'fas_tabs_order' . $suffix, 'all,products,posts,docs' );
$tabs_order_arr         = array_map( 'trim', explode( ',', $tabs_order ) );

$tab_all_title          = get_option( 'fas_tab_all_title' . $suffix, 'All Results' );
$tab_all_color          = get_option( 'fas_tab_all_color' . $suffix, '#0066cc' );
$tab_all_icon           = get_option( 'fas_tab_all_icon' . $suffix, 'dashicons-grid-view' );

$tab_products_title     = get_option( 'fas_tab_products_title' . $suffix, 'Products' );
$tab_products_color     = get_option( 'fas_tab_products_color' . $suffix, '#10b981' );
$tab_products_icon      = get_option( 'fas_tab_products_icon' . $suffix, 'dashicons-cart' );

$tab_posts_title        = get_option( 'fas_tab_posts_title' . $suffix, 'News & Articles' );
$tab_posts_color        = get_option( 'fas_tab_posts_color' . $suffix, '#f59e0b' );
$tab_posts_icon         = get_option( 'fas_tab_posts_icon' . $suffix, 'dashicons-welcome-write-blog' );

$tab_docs_title         = get_option( 'fas_tab_docs_title' . $suffix, 'Documentation' );
$tab_docs_color         = get_option( 'fas_tab_docs_color' . $suffix, '#6366f1' );
$tab_docs_icon          = get_option( 'fas_tab_docs_icon' . $suffix, 'dashicons-book-alt' );

$tab_details = array(
    'all' => array(
        'title' => $tab_all_title,
        'color' => $tab_all_color,
        'icon'  => $tab_all_icon,
    ),
    'products' => array(
        'title' => $tab_products_title,
        'color' => $tab_products_color,
        'icon'  => $tab_products_icon,
    ),
    'posts' => array(
        'title' => $tab_posts_title,
        'color' => $tab_posts_color,
        'icon'  => $tab_posts_icon,
    ),
    'docs' => array(
        'title' => $tab_docs_title,
        'color' => $tab_docs_color,
        'icon'  => $tab_docs_icon,
    ),
);
?>
<div class="wrap fas-admin-wrap" style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;">

    <!-- Top-bar Header Box -->
    <div class="fas-top-bar" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.04);">
        <div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="fas_lang_switcher" style="font-weight: 700; color: #475569; font-size: 13px;"><?php esc_html_e( 'Configure Language:', 'faramoj-search' ); ?></label>
                <select id="fas_lang_switcher" onchange="location = this.value;" style="border-radius: 6px; border: 1px solid #cbd5e1; padding: 4px 10px; font-weight: 600; color: #0f172a; outline: none;">
                    <?php foreach ( $langs as $code => $name ) : ?>
                        <option value="<?php echo esc_url( add_query_arg( 'fas_lang', $code ) ); ?>" <?php selected( $active_lang, $code ); ?>><?php echo esc_html( $name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <h2 style="margin: 12px 0 0 0; font-size: 22px; font-weight: 800; color: #0066cc;"><?php esc_html_e( 'Faramoj Advanced Search', 'faramoj-search' ); ?> <span style="font-size: 13px; color: #64748b; font-weight: 500;">(<?php echo esc_html( $langs[ $active_lang ] ); ?>)</span></h2>
        </div>

        <div>
            <!-- Save Changes button placed neatly on the left side of the top bar box -->
            <button type="submit" form="fas-settings-form" class="button button-primary button-large" style="background: #0066cc; border: none; font-weight: 700; padding: 10px 24px; height: auto; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,102,204,0.3); cursor: pointer; transition: transform 0.2s;">
                <?php esc_html_e( 'Save Changes', 'faramoj-search' ); ?>
            </button>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form method="post" action="options.php" id="fas-settings-form">
        <?php
        settings_fields( 'fas_settings_group' );
        do_settings_sections( 'fas_settings_group' );
        ?>

        <div class="fas-settings-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">

            <!-- Left Column: Settings -->
            <div class="fas-settings-column-left" style="display: flex; flex-direction: column; gap: 24px;">

                <!-- Section 1: Engine & Performance -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-performance" style="color: #0066cc;"></span>
                        <?php esc_html_e( 'Search Engine & Performance', 'faramoj-search' ); ?>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php esc_html_e( 'Cache Duration (Transient)', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_cache_duration<?php echo esc_attr( $suffix ); ?>" id="fas_cache_duration" value="<?php echo esc_attr( $cache_duration ); ?>" class="regular-text" min="0" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 6px; font-size: 11px; color: #64748b; display: block;"><?php esc_html_e( 'Time in seconds to cache results. Use 3600 for 1 hour. Set 0 to disable cache.', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 2: Popup & Dimensions -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-editor-expand" style="color: #0066cc;"></span>
                        <?php esc_html_e( 'Popup Layout & Dimensions', 'faramoj-search' ); ?>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php esc_html_e( 'Theme Accent Mode', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_theme_mode<?php echo esc_attr( $suffix ); ?>" id="fas_theme_mode" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="dark" <?php selected( $theme_mode, 'dark' ); ?>><?php esc_html_e( 'Deep Slate Dark Mode Overlay', 'faramoj-search' ); ?></option>
                                    <option value="light" <?php selected( $theme_mode, 'light' ); ?>><?php esc_html_e( 'Clean Corporate Light Mode Overlay', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Popup Width (px)', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_popup_width<?php echo esc_attr( $suffix ); ?>" id="fas_popup_width" value="<?php echo esc_attr( $popup_width ); ?>" class="regular-text" min="300" max="2000" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php esc_html_e( 'Width constraint (range: 300px - 2000px).', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Popup Max Height (px)', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_popup_max_height<?php echo esc_attr( $suffix ); ?>" id="fas_popup_max_height" value="<?php echo esc_attr( $popup_max_height ); ?>" class="regular-text" min="300" max="1500" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php esc_html_e( 'Max result pane height (range: 300px - 1500px).', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 3: Floating Trigger Position & Offsets -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-admin-appearance" style="color: #0066cc;"></span>
                        <?php esc_html_e( 'Floating Search Trigger Settings', 'faramoj-search' ); ?>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php esc_html_e( 'Enable Floating Button', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_enable_floating<?php echo esc_attr( $suffix ); ?>" id="fas_enable_floating" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="yes" <?php selected( $enable_floating, 'yes' ); ?>><?php esc_html_e( 'Enabled', 'faramoj-search' ); ?></option>
                                    <option value="no" <?php selected( $enable_floating, 'no' ); ?>><?php esc_html_e( 'Disabled', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Floating Button Position', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_floating_position<?php echo esc_attr( $suffix ); ?>" id="fas_floating_position" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="bottom-right" <?php selected( $floating_position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'faramoj-search' ); ?></option>
                                    <option value="bottom-left" <?php selected( $floating_position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'faramoj-search' ); ?></option>
                                    <option value="top-right" <?php selected( $floating_position, 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'faramoj-search' ); ?></option>
                                    <option value="top-left" <?php selected( $floating_position, 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Horizontal Offset (X px)', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_floating_offset_x<?php echo esc_attr( $suffix ); ?>" id="fas_floating_offset_x" value="<?php echo esc_attr( $floating_offset_x ); ?>" class="regular-text" min="0" max="300" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php esc_html_e( 'Distance from side edge of the screen.', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Vertical Offset (Y px)', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_floating_offset_y<?php echo esc_attr( $suffix ); ?>" id="fas_floating_offset_y" value="<?php echo esc_attr( $floating_offset_y ); ?>" class="regular-text" min="0" max="300" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php esc_html_e( 'Distance from top or bottom edge of the screen.', 'faramoj-search' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Display Pages', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_display_pages_type<?php echo esc_attr( $suffix ); ?>" id="fas_display_pages_type" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="all" <?php selected( $display_pages_type, 'all' ); ?>><?php esc_html_e( 'Show on All Pages', 'faramoj-search' ); ?></option>
                                    <option value="specific" <?php selected( $display_pages_type, 'specific' ); ?>><?php esc_html_e( 'Show on Specific Pages Only', 'faramoj-search' ); ?></option>
                                    <option value="none" <?php selected( $display_pages_type, 'none' ); ?>><?php esc_html_e( 'Disable Automatic Output (Manual Shortcode Only)', 'faramoj-search' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr id="fas_specific_pages_row" style="<?php echo ( 'specific' === $display_pages_type ) ? '' : 'display:none;'; ?>">
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Specific Page IDs or Slugs', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="text" name="fas_display_specific_pages<?php echo esc_attr( $suffix ); ?>" id="fas_display_specific_pages" value="<?php echo esc_attr( $display_specific_pages ); ?>" class="regular-text" placeholder="e.g. 12, home, contact-us" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php esc_html_e( 'Floating Button Brand Color', 'faramoj-search' ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="text" name="fas_floating_bg<?php echo esc_attr( $suffix ); ?>" id="fas_floating_bg" value="<?php echo esc_attr( $floating_bg ); ?>" class="fas-color-picker">
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

            <!-- Right Column: Reordering & Tabs Customization -->
            <div class="fas-settings-column-right" style="display: flex; flex-direction: column; gap: 24px;">

                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-move" style="color: #0066cc;"></span>
                        <?php esc_html_e( 'Tabs Order & Titles Customization', 'faramoj-search' ); ?>
                    </h3>

                    <p style="margin: 0 0 12px 0; font-size: 12px; color: #64748b; line-height: 1.5;">
                        <?php esc_html_e( 'Drag and drop the pill handles below to easily reorder your search categories.', 'faramoj-search' ); ?>
                    </p>

                    <!-- Drag and Drop Sortable Container -->
                    <div id="fas-sortable-tabs" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px;">
                        <?php
                        foreach ( $tabs_order_arr as $tab_key ) {
                            if ( ! isset( $tab_details[ $tab_key ] ) ) {
                                continue;
                            }
                            $tab = $tab_details[ $tab_key ];
                            ?>
                            <div class="fas-sortable-item" data-key="<?php echo esc_attr( $tab_key ); ?>" style="padding: 10px 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; cursor: move; display: flex; align-items: center; gap: 8px; font-weight: 600; color: #475569; box-shadow: 0 1px 2px rgba(0,0,0,0.03); transition: transform 0.2s;">
                                <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" style="font-size: 16px; width: 16px; height:16px; color: <?php echo esc_attr( $tab['color'] ); ?>;"></span>
                                <span><?php echo esc_html( $tab['title'] ); ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <input type="hidden" name="fas_tabs_order<?php echo esc_attr( $suffix ); ?>" id="fas_tabs_order" value="<?php echo esc_attr( $tabs_order ); ?>">

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <!-- Tab 1: All -->
                        <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <span style="font-weight: 700; color: #334155; font-size: 13px;">[All Results] Customization</span>
                            <div style="display: flex; gap: 10px; margin-top: 8px;">
                                <input type="text" name="fas_tab_all_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_title" value="<?php echo esc_attr( $tab_all_title ); ?>" class="regular-text" placeholder="All Results" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <input type="text" name="fas_tab_all_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_icon" value="<?php echo esc_attr( $tab_all_icon ); ?>" class="regular-text" placeholder="dashicons-grid-view" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </div>
                            <div style="margin-top: 8px;">
                                <input type="text" name="fas_tab_all_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_color" value="<?php echo esc_attr( $tab_all_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>

                        <!-- Tab 2: Products -->
                        <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <span style="font-weight: 700; color: #334155; font-size: 13px;">[Products] Customization</span>
                            <div style="display: flex; gap: 10px; margin-top: 8px;">
                                <input type="text" name="fas_tab_products_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_title" value="<?php echo esc_attr( $tab_products_title ); ?>" class="regular-text" placeholder="Products" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <input type="text" name="fas_tab_products_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_icon" value="<?php echo esc_attr( $tab_products_icon ); ?>" class="regular-text" placeholder="dashicons-cart" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </div>
                            <div style="margin-top: 8px;">
                                <input type="text" name="fas_tab_products_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_color" value="<?php echo esc_attr( $tab_products_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>

                        <!-- Tab 3: Posts -->
                        <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <span style="font-weight: 700; color: #334155; font-size: 13px;">[News & Articles] Customization</span>
                            <div style="display: flex; gap: 10px; margin-top: 8px;">
                                <input type="text" name="fas_tab_posts_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_title" value="<?php echo esc_attr( $tab_posts_title ); ?>" class="regular-text" placeholder="News & Articles" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <input type="text" name="fas_tab_posts_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_icon" value="<?php echo esc_attr( $tab_posts_icon ); ?>" class="regular-text" placeholder="dashicons-welcome-write-blog" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </div>
                            <div style="margin-top: 8px;">
                                <input type="text" name="fas_tab_posts_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_color" value="<?php echo esc_attr( $tab_posts_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>

                        <!-- Tab 4: Docs -->
                        <div style="background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <span style="font-weight: 700; color: #334155; font-size: 13px;">[Documentation] Customization</span>
                            <div style="display: flex; gap: 10px; margin-top: 8px;">
                                <input type="text" name="fas_tab_docs_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_title" value="<?php echo esc_attr( $tab_docs_title ); ?>" class="regular-text" placeholder="Documentation" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <input type="text" name="fas_tab_docs_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_icon" value="<?php echo esc_attr( $tab_docs_icon ); ?>" class="regular-text" placeholder="dashicons-book-alt" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </div>
                            <div style="margin-top: 8px;">
                                <input type="text" name="fas_tab_docs_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_color" value="<?php echo esc_attr( $tab_docs_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </form>

    <!-- Full Width Card for Live Preview (placed freely at the bottom) -->
    <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-top: 30px; width: 100%; box-sizing: border-box;">
        <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
            <span style="background: #e11d48; color: #fff; font-size: 10px; padding: 3px 8px; border-radius: 12px; font-weight: 800; text-transform: uppercase;"><?php esc_html_e( 'Live', 'faramoj-search' ); ?></span>
            <?php esc_html_e( 'Modal Overlay Live Preview', 'faramoj-search' ); ?>
        </h3>

        <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;">
            <?php esc_html_e( 'The live preview below renders without any layout constraints. Resize your popup width and max-height freely using the dimension controls above to see the layout changes in real-time.', 'faramoj-search' ); ?>
        </p>

        <!-- Centered live preview container wrapper with generous workspace -->
        <div id="fas-mock-modal-wrapper" style="background: radial-gradient(circle, #f8fafc 0%, #f1f5f9 100%); padding: 60px 20px; border-radius: 12px; display: flex; justify-content: center; overflow: auto; min-height: 250px; border: 1px solid #cbd5e1;">

            <div id="fas-preview-container" class="fas-search-container" style="width: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">

                <!-- Input wrapper -->
                <div class="fas-search-input-wrapper" style="display: flex; align-items: center; padding: 18px 24px;">
                    <span class="dashicons dashicons-search" style="color: #64748b; margin-right: 12px; font-size: 20px; width: 20px; height: 20px;"></span>
                    <input type="text" placeholder="Type to search..." style="border:none; outline:none; background:transparent; width:100%; font-size:18px; color:#1e293b; font-weight:500;" disabled>

                    <!-- Close button in preview -->
                    <button class="fas-modal-close" style="background: rgba(100, 116, 139, 0.1); border: none !important; color: #64748b; width: 34px; height: 34px; border-radius: 50% !important; margin-left: 14px; display: flex; align-items: center; justify-content: center;" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Tabs (Dynamic pills layout) -->
                <div id="fas-preview-tabs" class="fas-search-tabs">
                    <!-- Loaded dynamically via javascript -->
                </div>

                <!-- Results box mock -->
                <div id="fas-preview-results" style="padding: 20px 24px; min-height: 140px;">
                    <div class="fas-result-item" style="display: flex; align-items: center; gap: 16px; padding: 12px; border-radius: 12px; margin-bottom: 8px;">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(100,116,139,0.1); display:flex; align-items:center; justify-content:center; color:#64748b;">
                            <span class="dashicons dashicons-cart" style="font-size: 22px; width:22px; height:22px;"></span>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight:600;">Phase-30ISO Antenna</h4>
                            <p style="margin:0; font-size:13px; color:#64748b; line-height:1.4;">Premium dual-band technical telecommunication product spec...</p>
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

    // Make Tabs Order Sortable via jQuery UI Sortable
    $('#fas-sortable-tabs').sortable({
        update: function(event, ui) {
            var order = [];
            $('#fas-sortable-tabs .fas-sortable-item').each(function() {
                order.push($(this).data('key'));
            });
            $('#fas_tabs_order').val(order.join(','));
            // Update Live Preview immediately
            updateLivePreview();
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
                'background-color': '#0f172a',
                'border-color': '#1e293b'
            });
            $('#fas-preview-results').css('background-color', '#0f172a');
            $('#fas-preview-results .fas-result-item').css({
                'background-color': '#1e293b',
                'border-color': 'transparent'
            });
            $('#fas-preview-results h4').css('color', '#f8fafc');
            $('#fas-preview-results p').css('color', '#94a3b8');
            $('#fas-preview-container input').css('color', '#f8fafc');
            $('#fas-preview-container .fas-search-input-wrapper').css('border-bottom-color', '#1e293b');
        } else {
            $('#fas-preview-container').css({
                'background-color': '#ffffff',
                'border-color': '#e2e8f0'
            });
            $('#fas-preview-results').css('background-color', '#ffffff');
            $('#fas-preview-results .fas-result-item').css({
                'background-color': '#f8fafc',
                'border-color': 'transparent'
            });
            $('#fas-preview-results h4').css('color', '#0f172a');
            $('#fas-preview-results p').css('color', '#64748b');
            $('#fas-preview-container input').css('color', '#0f172a');
            $('#fas-preview-container .fas-search-input-wrapper').css('border-bottom-color', '#e2e8f0');
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

            var isActive = !activeClassAdded;
            activeClassAdded = true;

            var tabStyle = 'display: flex; align-items: center; gap: 8px; padding: 8px 16px; border: 1px solid '+(isActive ? color : 'transparent')+'; background: '+(isActive ? color : 'rgba(100, 116, 139, 0.05)')+'; font-weight: 600; font-size:13px; cursor: pointer; color: ' + (isActive ? '#ffffff' : '#64748b') + '; border-radius: 20px; transition: all 0.3s ease; white-space: nowrap;';
            tabsHtml += '<button type="button" style="' + tabStyle + '">' +
                        '<span class="dashicons ' + icon + '" style="font-size:18px; width:18px; height:18px; color:' + (isActive ? '#ffffff' : '#64748b') + ';"></span>' +
                        '<span>' + title + '</span>' +
                        '</button>';
        });

        $('#fas-preview-tabs').html(tabsHtml).css({
            'display': 'flex',
            'background-color': 'transparent',
            'border-bottom-width': '1px',
            'border-bottom-style': 'solid',
            'border-bottom-color': (mode === 'dark' ? '#1e293b' : '#e2e8f0'),
            'padding': '10px 16px',
            'gap': '12px'
        });
    }

    // Initial load
    updateLivePreview();
});
</script>
