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
$group_name = 'fas_settings_group_' . $active_lang;

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

// Determine Admin display locale
$admin_locale = $this->get_admin_display_locale();
$is_rtl = ( 'fa' === $admin_locale );
$dir_style = $is_rtl ? 'direction: rtl; text-align: right;' : 'direction: ltr; text-align: left;';

$i18n = array(
    'configure_lang' => $is_rtl ? 'تنظیم زبان فعال:' : 'Configure Active Language:',
    'title' => $is_rtl ? 'موتور جستجوی پیشرفته فراموج' : 'Faramoj Advanced Search',
    'save_changes' => $is_rtl ? 'ذخیره تغییرات' : 'Save Changes',
    'engine_performance' => $is_rtl ? 'موتور جستجو و کارایی' : 'Search Engine & Performance',
    'cache_duration' => $is_rtl ? 'مدت زمان حافظه پنهان (ترنزینت)' : 'Cache Duration (Transient)',
    'cache_duration_desc' => $is_rtl ? 'مدت زمان به ثانیه برای کش کردن نتایج. برای ۱ ساعت از ۳۶۰۰ استفاده کنید. برای غیرفعال کردن کش عدد ۰ را قرار دهید.' : 'Time in seconds to cache results. Use 3600 for 1 hour. Set 0 to disable cache.',
    'popup_layout' => $is_rtl ? 'طراحی و ابعاد پاپ‌آپ' : 'Popup Layout & Dimensions',
    'theme_accent' => $is_rtl ? 'حالت رنگی و تم پاپ‌آپ' : 'Theme Accent Mode',
    'dark_mode' => $is_rtl ? 'تم زغالی تیره پاپ‌آپ (Deep Slate)' : 'Deep Slate Dark Mode Overlay',
    'light_mode' => $is_rtl ? 'تم روشن شرکتی پاپ‌آپ (Clean Corporate)' : 'Clean Corporate Light Mode Overlay',
    'popup_width' => $is_rtl ? 'عرض پاپ‌آپ (پیکسل)' : 'Popup Width (px)',
    'popup_width_desc' => $is_rtl ? 'عرض کادر پاپ‌آپ پدیدار شده (محدوده مجاز: ۳۰۰ تا ۲۰۰۰ پیکسل)' : 'Width constraint (range: 300px - 2000px).',
    'popup_height' => $is_rtl ? 'حداکثر ارتفاع پاپ‌آپ (پیکسل)' : 'Popup Max Height (px)',
    'popup_height_desc' => $is_rtl ? 'حداکثر ارتفاع کادر نتایج جستجو (محدوده مجاز: ۳۰۰ تا ۱۵۰۰ پیکسل)' : 'Max result pane height (range: 300px - 1500px).',
    'floating_settings' => $is_rtl ? 'تنظیمات دکمه شناور جستجو' : 'Floating Search Trigger Settings',
    'enable_floating' => $is_rtl ? 'فعال‌سازی دکمه شناور' : 'Enable Floating Button',
    'enabled' => $is_rtl ? 'فعال شده' : 'Enabled',
    'disabled' => $is_rtl ? 'غیرفعال شده' : 'Disabled',
    'floating_pos' => $is_rtl ? 'موقعیت دکمه شناور در صفحه' : 'Floating Button Position',
    'bottom_right' => $is_rtl ? 'پایین راست' : 'Bottom Right',
    'bottom_left' => $is_rtl ? 'پایین چپ' : 'Bottom Left',
    'top_right' => $is_rtl ? 'بالا راست' : 'Top Right',
    'top_left' => $is_rtl ? 'بالا چپ' : 'Top Left',
    'offset_x' => $is_rtl ? 'فاصله افقی دکمه (X پیکسل)' : 'Horizontal Offset (X px)',
    'offset_x_desc' => $is_rtl ? 'فاصله از لبه کناری صفحه وب.' : 'Distance from side edge of the screen.',
    'offset_y' => $is_rtl ? 'فاصله عمودی دکمه (Y پیکسل)' : 'Vertical Offset (Y px)',
    'offset_y_desc' => $is_rtl ? 'فاصله از لبه بالا یا پایین صفحه وب.' : 'Distance from top or bottom edge of the screen.',
    'display_pages' => $is_rtl ? 'صفحات وب قابل نمایش' : 'Display Pages',
    'show_all' => $is_rtl ? 'نمایش در تمام صفحات وب' : 'Show on All Pages',
    'show_specific' => $is_rtl ? 'نمایش فقط در صفحات خاص و منتخب' : 'Show on Specific Pages Only',
    'disable_auto' => $is_rtl ? 'غیرفعال‌سازی نمایش خودکار (فقط خروجی با شورت‌کد)' : 'Disable Automatic Output (Manual Shortcode Only)',
    'specific_pages' => $is_rtl ? 'لیست شناسه‌ها (ID) یا نام مستعار (Slug) صفحات خاص' : 'Specific Page IDs or Slugs',
    'floating_bg' => $is_rtl ? 'رنگ دکمه شناور جستجو' : 'Floating Button Brand Color',
    'tabs_customization' => $is_rtl ? 'ترتیب چیدمان و شخصی‌سازی تب‌ها' : 'Tabs Order & Titles Customization',
    'tabs_desc' => $is_rtl ? 'با کشیدن و رها کردن کادرهای زیر، می‌توانید به راحتی ترتیب نمایش دسته‌بندی تب‌ها در پاپ‌آپ را تغییر دهید.' : 'Drag and drop the pill handles below to easily reorder your search categories.',
    'tab_all_cust' => $is_rtl ? 'شخصی‌سازی تب [همه نتایج]' : '[All Results] Customization',
    'tab_prod_cust' => $is_rtl ? 'شخصی‌سازی تب [محصولات]' : '[Products] Customization',
    'tab_post_cust' => $is_rtl ? 'شخصی‌سازی تب [اخبار و مقالات]' : '[News & Articles] Customization',
    'tab_doc_cust' => $is_rtl ? 'شخصی‌سازی تب [مستندات و آموزش]' : '[Documentation] Customization',
    'live' => $is_rtl ? 'زنده' : 'Live',
    'live_preview' => $is_rtl ? 'پیش‌نمایش زنده چیدمان پاپ‌آپ' : 'Modal Overlay Live Preview',
    'live_preview_desc' => $is_rtl ? 'پیش‌نمایش تعاملی زیر به طور زنده تغییرات چیدمان پاپ‌آپ، ابعاد پیکسل، و حالت‌های تیره/روشن تنظیم شده در بالا را نمایش می‌دهد.' : 'The live preview below renders without any layout constraints. Resize your popup width and max-height freely using the dimension controls above to see the layout changes in real-time.',
    'type_search' => $is_rtl ? 'عبارتی را برای جستجو تایپ کنید...' : 'Type to search...',
);
?>
<div class="wrap fas-admin-wrap" style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; <?php echo $dir_style; ?>">

    <style>
        .fas-admin-wrap .wp-picker-container {
            direction: ltr !important;
        }
        <?php if ($is_rtl) : ?>
        .fas-admin-wrap .fas-form-table td,
        .fas-admin-wrap .fas-form-table th {
            text-align: right !important;
        }
        .fas-admin-wrap .fas-form-table td .fas-description {
            text-align: right !important;
        }
        <?php endif; ?>
    </style>

    <!-- Redesigned Top-bar Header Box -->
    <div class="fas-top-bar" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; display: flex; flex-direction: column; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); gap: 20px;">

        <!-- Center aligned Title -->
        <div style="text-align: center; width: 100%;">
            <h2 style="margin: 0; font-size: 26px; font-weight: 850; color: #0066cc;"><?php echo esc_html( $i18n['title'] ); ?> <span style="font-size: 14px; color: #64748b; font-weight: 500;">(<?php echo esc_html( $langs[ $active_lang ] ); ?>)</span></h2>
        </div>

        <!-- Lower row: Save Changes Button on the left, Active Language Selector on the right -->
        <div style="width: 100%; display: flex; justify-content: space-between; align-items: center; flex-direction: row; gap: 16px; box-sizing: border-box;">
            <!-- Save changes button on the left (or opposite depending on LTR/RTL) -->
            <div>
                <button type="submit" form="fas-settings-form" class="button button-primary button-large" style="background: #0066cc; border: none; font-weight: 700; padding: 12px 28px; height: auto; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,102,204,0.3); cursor: pointer; transition: transform 0.2s;">
                    <?php echo esc_html( $i18n['save_changes'] ); ?>
                </button>
            </div>

            <!-- Active language selection dropdown on the right -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="fas_lang_switcher" style="font-weight: 700; color: #475569; font-size: 14px;"><?php echo esc_html( $i18n['configure_lang'] ); ?></label>
                <select id="fas_lang_switcher" onchange="location = this.value;" style="border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px; font-weight: 600; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                    <?php foreach ( $langs as $code => $name ) : ?>
                        <option value="<?php echo esc_url( add_query_arg( 'fas_lang', $code ) ); ?>" <?php selected( $active_lang, $code ); ?>><?php echo esc_html( $name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form method="post" action="options.php" id="fas-settings-form">
        <?php
        settings_fields( $group_name );
        do_settings_sections( $group_name );
        ?>

        <div class="fas-settings-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">

            <!-- Left Column: Settings -->
            <div class="fas-settings-column-left" style="display: flex; flex-direction: column; gap: 24px;">

                <!-- Section 1: Engine & Performance -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                        <span class="dashicons dashicons-performance" style="color: #0066cc;"></span>
                        <span><?php echo esc_html( $i18n['engine_performance'] ); ?></span>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['cache_duration'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_cache_duration<?php echo esc_attr( $suffix ); ?>" id="fas_cache_duration" value="<?php echo esc_attr( $cache_duration ); ?>" class="regular-text" min="0" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 6px; font-size: 11px; color: #64748b; display: block;"><?php echo esc_html( $i18n['cache_duration_desc'] ); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 2: Popup & Dimensions -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                        <span class="dashicons dashicons-editor-expand" style="color: #0066cc;"></span>
                        <span><?php echo esc_html( $i18n['popup_layout'] ); ?></span>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['theme_accent'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_theme_mode<?php echo esc_attr( $suffix ); ?>" id="fas_theme_mode" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="dark" <?php selected( $theme_mode, 'dark' ); ?>><?php echo esc_html( $i18n['dark_mode'] ); ?></option>
                                    <option value="light" <?php selected( $theme_mode, 'light' ); ?>><?php echo esc_html( $i18n['light_mode'] ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['popup_width'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_popup_width<?php echo esc_attr( $suffix ); ?>" id="fas_popup_width" value="<?php echo esc_attr( $popup_width ); ?>" class="regular-text" min="300" max="2000" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php echo esc_html( $i18n['popup_width_desc'] ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['popup_height'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_popup_max_height<?php echo esc_attr( $suffix ); ?>" id="fas_popup_max_height" value="<?php echo esc_attr( $popup_max_height ); ?>" class="regular-text" min="300" max="1500" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php echo esc_html( $i18n['popup_height_desc'] ); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 3: Floating Trigger Position & Offsets -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                        <span class="dashicons dashicons-admin-appearance" style="color: #0066cc;"></span>
                        <span><?php echo esc_html( $i18n['floating_settings'] ); ?></span>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['enable_floating'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_enable_floating<?php echo esc_attr( $suffix ); ?>" id="fas_enable_floating" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="yes" <?php selected( $enable_floating, 'yes' ); ?>><?php echo esc_html( $i18n['enabled'] ); ?></option>
                                    <option value="no" <?php selected( $enable_floating, 'no' ); ?>><?php echo esc_html( $i18n['disabled'] ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['floating_pos'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_floating_position<?php echo esc_attr( $suffix ); ?>" id="fas_floating_position" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="bottom-right" <?php selected( $floating_position, 'bottom-right' ); ?>><?php echo esc_html( $i18n['bottom_right'] ); ?></option>
                                    <option value="bottom-left" <?php selected( $floating_position, 'bottom-left' ); ?>><?php echo esc_html( $i18n['bottom_left'] ); ?></option>
                                    <option value="top-right" <?php selected( $floating_position, 'top-right' ); ?>><?php echo esc_html( $i18n['top_right'] ); ?></option>
                                    <option value="top-left" <?php selected( $floating_position, 'top-left' ); ?>><?php echo esc_html( $i18n['top_left'] ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['offset_x'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_floating_offset_x<?php echo esc_attr( $suffix ); ?>" id="fas_floating_offset_x" value="<?php echo esc_attr( $floating_offset_x ); ?>" class="regular-text" min="0" max="300" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php echo esc_html( $i18n['offset_x_desc'] ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['offset_y'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_floating_offset_y<?php echo esc_attr( $suffix ); ?>" id="fas_floating_offset_y" value="<?php echo esc_attr( $floating_offset_y ); ?>" class="regular-text" min="0" max="300" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                                <span class="fas-description" style="margin-top: 4px; font-size: 11px; color: #64748b; display: block;"><?php echo esc_html( $i18n['offset_y_desc'] ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['display_pages'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <select name="fas_display_pages_type<?php echo esc_attr( $suffix ); ?>" id="fas_display_pages_type" class="regular-text" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1; height: 35px;">
                                    <option value="all" <?php selected( $display_pages_type, 'all' ); ?>><?php echo esc_html( $i18n['show_all'] ); ?></option>
                                    <option value="specific" <?php selected( $display_pages_type, 'specific' ); ?>><?php echo esc_html( $i18n['show_specific'] ); ?></option>
                                    <option value="none" <?php selected( $display_pages_type, 'none' ); ?>><?php echo esc_html( $i18n['disable_auto'] ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr id="fas_specific_pages_row" style="<?php echo ( 'specific' === $display_pages_type ) ? '' : 'display:none;'; ?>">
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['specific_pages'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="text" name="fas_display_specific_pages<?php echo esc_attr( $suffix ); ?>" id="fas_display_specific_pages" value="<?php echo esc_attr( $display_specific_pages ); ?>" class="regular-text" placeholder="e.g. 12, home, contact-us" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['floating_bg'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <input type="text" name="fas_floating_bg<?php echo esc_attr( $suffix ); ?>" id="fas_floating_bg" value="<?php echo esc_attr( $floating_bg ); ?>" class="fas-color-picker">
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

            <!-- Right Column: Reordering & Tabs Customization -->
            <div class="fas-settings-column-right" style="display: flex; flex-direction: column; gap: 24px;">

                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                        <span class="dashicons dashicons-move" style="color: #0066cc;"></span>
                        <span><?php echo esc_html( $i18n['tabs_customization'] ); ?></span>
                    </h3>

                    <p style="margin: 0 0 12px 0; font-size: 12px; color: #64748b; line-height: 1.5;">
                        <?php echo esc_html( $i18n['tabs_desc'] ); ?>
                    </p>

                    <!-- Drag and Drop Sortable Container -->
                    <div id="fas-sortable-tabs" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
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
                            <span style="font-weight: 700; color: #334155; font-size: 13px;"><?php echo esc_html( $i18n['tab_all_cust'] ); ?></span>
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
                            <span style="font-weight: 700; color: #334155; font-size: 13px;"><?php echo esc_html( $i18n['tab_prod_cust'] ); ?></span>
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
                            <span style="font-weight: 700; color: #334155; font-size: 13px;"><?php echo esc_html( $i18n['tab_post_cust'] ); ?></span>
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
                            <span style="font-weight: 700; color: #334155; font-size: 13px;"><?php echo esc_html( $i18n['tab_doc_cust'] ); ?></span>
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

    <!-- Full Width Card for Live Preview -->
    <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); margin-top: 30px; width: 100%; box-sizing: border-box; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
        <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
            <span style="background: #e11d48; color: #fff; font-size: 10px; padding: 3px 8px; border-radius: 12px; font-weight: 800; text-transform: uppercase;"><?php echo esc_html( $i18n['live'] ); ?></span>
            <span><?php echo esc_html( $i18n['live_preview'] ); ?></span>
        </h3>

        <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;">
            <?php echo esc_html( $i18n['live_preview_desc'] ); ?>
        </p>

        <!-- Centered live preview container wrapper with generous workspace -->
        <div id="fas-mock-modal-wrapper" style="background: radial-gradient(circle, #f8fafc 0%, #f1f5f9 100%); padding: 60px 20px; border-radius: 12px; display: flex; justify-content: center; overflow: auto; min-height: 250px; border: 1px solid #cbd5e1;">

            <div id="fas-preview-container" class="fas-search-container" style="width: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; <?php echo $dir_style; ?>">

                <!-- Input wrapper -->
                <div class="fas-search-input-wrapper" style="display: flex; align-items: center; padding: 18px 24px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                    <span class="dashicons dashicons-search" style="color: #64748b; margin: <?php echo $is_rtl ? '0 0 0 12px' : '0 12px 0 0'; ?>; font-size: 20px; width: 20px; height: 20px;"></span>
                    <input type="text" placeholder="<?php echo esc_attr( $i18n['type_search'] ); ?>" style="border:none; outline:none; background:transparent; width:100%; font-size:18px; color:#1e293b; font-weight:500; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;" disabled>

                    <!-- Close button in preview -->
                    <button class="fas-modal-close" style="background: rgba(100, 116, 139, 0.1); border: none !important; color: #64748b; width: 34px; height: 34px; border-radius: 50% !important; margin: <?php echo $is_rtl ? '0 14px 0 0' : '0 0 0 14px'; ?>; display: flex; align-items: center; justify-content: center;" disabled>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>

                <!-- Tabs (Dynamic pills layout) -->
                <div id="fas-preview-tabs" class="fas-search-tabs" style="flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                    <!-- Loaded dynamically via javascript -->
                </div>

                <!-- Results box mock -->
                <div id="fas-preview-results" style="padding: 20px 24px; min-height: 140px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <div class="fas-result-item" style="display: flex; align-items: center; gap: 16px; padding: 12px; border-radius: 12px; margin-bottom: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                        <div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(100,116,139,0.1); display:flex; align-items:center; justify-content:center; color:#64748b;">
                            <span class="dashicons dashicons-cart" style="font-size: 22px; width:22px; height:22px;"></span>
                        </div>
                        <div style="text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                            <h4 style="margin: 0 0 4px 0; font-size: 15px; font-weight:600;"><?php echo $is_rtl ? 'آنتن فوق پیشرفته Phase-30ISO' : 'Phase-30ISO Antenna'; ?></h4>
                            <p style="margin:0; font-size:13px; color:#64748b; line-height:1.4;"><?php echo $is_rtl ? 'محصول مخابراتی دو بانده فوق پیشرفته با مشخصات فنی عالی...' : 'Premium dual-band technical telecommunication product spec...'; ?></p>
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
