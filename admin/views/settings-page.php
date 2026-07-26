<?php
/**
 * HTML layout for the admin panel with Live Interactive Preview & Multilingual settings
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Retrieve active languages
$langs = $this->get_active_languages();

// Detect active language suffix - defaults to active admin display locale instead of hardcoded 'en'
$active_lang = isset( $_GET['fas_lang'] ) ? sanitize_text_field( $_GET['fas_lang'] ) : $this->get_admin_display_locale();
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

$history_count          = get_option( 'fas_history_count' . $suffix, 5 );
$history_bg             = get_option( 'fas_history_bg' . $suffix, 'rgba(255, 255, 255, 0.1)' );
$history_hover_bg       = get_option( 'fas_history_hover_bg' . $suffix, 'rgba(255, 255, 255, 0.2)' );
$history_text_size      = get_option( 'fas_history_text_size' . $suffix, 13 );

// Fixed Sortable Loading Bug: Check if empty or corrupted, fall back to core tabs
$tabs_order             = get_option( 'fas_tabs_order' . $suffix );
if ( empty( $tabs_order ) || strpos( $tabs_order, 'all' ) === false ) {
    $tabs_order = 'all,products,posts,docs';
}
$tabs_order_arr         = array_map( 'trim', explode( ',', $tabs_order ) );

$tab_all_title          = get_option( 'fas_tab_all_title' . $suffix, 'All Results' );
$tab_all_color          = get_option( 'fas_tab_all_color' . $suffix, '#0066cc' );
$tab_all_icon           = get_option( 'fas_tab_all_icon' . $suffix, 'dashicons-grid-view' );
$tab_all_custom_icon    = get_option( 'fas_tab_all_custom_icon' . $suffix, '' );

$tab_products_title     = get_option( 'fas_tab_products_title' . $suffix, 'Products' );
$tab_products_color     = get_option( 'fas_tab_products_color' . $suffix, '#10b981' );
$tab_products_icon      = get_option( 'fas_tab_products_icon' . $suffix, 'dashicons-cart' );
$tab_products_custom_icon = get_option( 'fas_tab_products_custom_icon' . $suffix, '' );

$tab_posts_title        = get_option( 'fas_tab_posts_title' . $suffix, 'News & Articles' );
$tab_posts_color        = get_option( 'fas_tab_posts_color' . $suffix, '#f59e0b' );
$tab_posts_icon         = get_option( 'fas_tab_posts_icon' . $suffix, 'dashicons-welcome-write-blog' );
$tab_posts_custom_icon  = get_option( 'fas_tab_posts_custom_icon' . $suffix, '' );

$tab_docs_title         = get_option( 'fas_tab_docs_title' . $suffix, 'Documentation' );
$tab_docs_color         = get_option( 'fas_tab_docs_color' . $suffix, '#6366f1' );
$tab_docs_icon          = get_option( 'fas_tab_docs_icon' . $suffix, 'dashicons-book-alt' );
$tab_docs_custom_icon   = get_option( 'fas_tab_docs_custom_icon' . $suffix, '' );

$tab_details = array(
    'all' => array(
        'title'       => $tab_all_title,
        'color'       => $tab_all_color,
        'icon'        => $tab_all_icon,
        'custom_icon' => $tab_all_custom_icon,
    ),
    'products' => array(
        'title'       => $tab_products_title,
        'color'       => $tab_products_color,
        'icon'        => $tab_products_icon,
        'custom_icon' => $tab_products_custom_icon,
    ),
    'posts' => array(
        'title'       => $tab_posts_title,
        'color'       => $tab_posts_color,
        'icon'        => $tab_posts_icon,
        'custom_icon' => $tab_posts_custom_icon,
    ),
    'docs' => array(
        'title'       => $tab_docs_title,
        'color'       => $tab_docs_color,
        'icon'        => $tab_docs_icon,
        'custom_icon' => $tab_docs_custom_icon,
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
    'save_success' => $is_rtl ? 'تنظیمات با موفقیت ذخیره شد ✓' : 'Settings saved successfully ✓',
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
    'upload_lbl' => $is_rtl ? 'آیکون دلخواه PNG یا SVG:' : 'Custom SVG or PNG Icon:',
    'upload_btn' => $is_rtl ? 'انتخاب آیکون' : 'Upload',
    'live' => $is_rtl ? 'زنده' : 'Live',
    'live_preview' => $is_rtl ? 'پیش‌نمایش زنده چیدمان پاپ‌آپ' : 'Modal Overlay Live Preview',
    'live_preview_desc' => $is_rtl ? 'پیش‌نمایش تعاملی زیر به طور زنده تغییرات چیدمان پاپ‌آپ، ابعاد پیکسل، و حالت‌های تیره/روشن تنظیم شده در بالا را نمایش می‌دهد.' : 'The live preview below renders without any layout constraints. Resize your popup width and max-height freely using the dimension controls above to see the layout changes in real-time.',
    'type_search' => $is_rtl ? 'جستجو در محصولات، مقالات، مستندات...' : 'Search products, articles, docs...',
);

$settings_saved = isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'];
$btn_text = $settings_saved ? $i18n['save_success'] : $i18n['save_changes'];
$btn_bg   = $settings_saved ? '#10b981' : '#0066cc';
?>
<div class="wrap fas-admin-wrap" style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; <?php echo $dir_style; ?>">
    <?php if ( $settings_saved ) : ?>
        <script>
            // Revert save button text and color after 3 seconds
            setTimeout(function() {
                var btn = document.getElementById('fas-main-save-btn');
                if (btn) {
                    btn.innerText = '<?php echo esc_js( $i18n['save_changes'] ); ?>';
                    btn.style.background = '#0066cc';
                    btn.style.boxShadow = '0 4px 12px rgba(0,102,204,0.3)';
                }
            }, 3000);
        </script>
    <?php endif; ?>
    
    <style>
        .fas-admin-wrap .wp-picker-container {
            direction: ltr !important;
        }
        .fas-admin-wrap input[type="number"] {
            padding: 8px 16px !important;
            height: 40px !important;
            box-sizing: border-box;
        }
        .fas-card {
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .fas-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.06) !important;
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
    <div class="fas-top-bar" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); gap: 20px;">
        
        <!-- Right/Left aligned Title based on RTL via flex container -->
        <div style="display: flex; align-items: center;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 850; color: #0066cc;"><?php echo esc_html( $i18n['title'] ); ?> <span style="font-size: 14px; color: #64748b; font-weight: 500;">(<?php echo esc_html( $langs[ $active_lang ] ); ?>)</span></h2>
        </div>
        
        <!-- Actions -->
        <div style="display: flex; align-items: center; gap: 16px;">
            <!-- Active language selection dropdown -->
            <div style="display: flex; align-items: center; gap: 8px;">
                <label for="fas_lang_switcher" style="font-weight: 700; color: #475569; font-size: 14px;"><?php echo esc_html( $i18n['configure_lang'] ); ?></label>
                <select id="fas_lang_switcher" onchange="location = this.value;" style="border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px; font-weight: 600; color: #0f172a; outline: none; background: #f8fafc; cursor: pointer;">
                    <?php foreach ( $langs as $code => $name ) : ?>
                        <option value="<?php echo esc_url( add_query_arg( 'fas_lang', $code ) ); ?>" <?php selected( $active_lang, $code ); ?>><?php echo esc_html( $name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Save changes button -->
            <div>
                <button type="submit" id="fas-main-save-btn" form="fas-settings-form" class="button button-primary button-large" style="background: <?php echo esc_attr( $btn_bg ); ?>; border: none; font-weight: 700; padding: 12px 28px; height: auto; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,102,204,0.3); cursor: pointer; transition: background 0.3s ease, transform 0.2s, color 0.3s ease;">
                    <?php echo esc_html( $btn_text ); ?>
                </button>
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
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
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
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
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

                <!-- Section: Results Styling & Limits -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-editor-ul" style="color: #0066cc;"></span>
                        <span><?php echo $is_rtl ? 'تنظیمات نتایج جستجو' : 'Search Results Settings'; ?></span>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'تعداد نتایج در هر تب' : 'Results per Tab'; ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_results_count<?php echo esc_attr( $suffix ); ?>" id="fas_results_count" value="<?php echo esc_attr( get_option( 'fas_results_count' . $suffix, 15 ) ); ?>" class="regular-text" min="1" max="50" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'سایز عنوان (دسکتاپ / موبایل)' : 'Title Size (Desktop / Mobile)'; ?></td>
                            <td style="padding: 10px 0; display: flex; justify-content: space-between; gap: 4px;">
                                <input type="number" name="fas_title_size_desktop<?php echo esc_attr( $suffix ); ?>" id="fas_title_size_desktop" value="<?php echo esc_attr( get_option( 'fas_title_size_desktop' . $suffix, 15 ) ); ?>" class="regular-text" style="width: 48%; min-width: 80px; box-sizing: border-box; border-radius: 6px; border: 1px solid #cbd5e1;" placeholder="Desktop px">
                                <input type="number" name="fas_title_size_mobile<?php echo esc_attr( $suffix ); ?>" id="fas_title_size_mobile" value="<?php echo esc_attr( get_option( 'fas_title_size_mobile' . $suffix, 14 ) ); ?>" class="regular-text" style="width: 48%; min-width: 80px; box-sizing: border-box; border-radius: 6px; border: 1px solid #cbd5e1;" placeholder="Mobile px">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'سایز توضیحات (دسکتاپ / موبایل)' : 'Excerpt Size (Desktop / Mobile)'; ?></td>
                            <td style="padding: 10px 0; display: flex; justify-content: space-between; gap: 4px;">
                                <input type="number" name="fas_excerpt_size_desktop<?php echo esc_attr( $suffix ); ?>" id="fas_excerpt_size_desktop" value="<?php echo esc_attr( get_option( 'fas_excerpt_size_desktop' . $suffix, 13 ) ); ?>" class="regular-text" style="width: 48%; min-width: 80px; box-sizing: border-box; border-radius: 6px; border: 1px solid #cbd5e1;" placeholder="Desktop px">
                                <input type="number" name="fas_excerpt_size_mobile<?php echo esc_attr( $suffix ); ?>" id="fas_excerpt_size_mobile" value="<?php echo esc_attr( get_option( 'fas_excerpt_size_mobile' . $suffix, 12 ) ); ?>" class="regular-text" style="width: 48%; min-width: 80px; box-sizing: border-box; border-radius: 6px; border: 1px solid #cbd5e1;" placeholder="Mobile px">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section: History Settings -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-backup" style="color: #0066cc;"></span>
                        <span><?php echo $is_rtl ? 'تنظیمات تاریخچه جستجو' : 'Search History Settings'; ?></span>
                    </h3>
                    <table class="form-table fas-form-table" style="margin: 0; width: 100%;">
                        <tr>
                            <td style="padding: 10px 0; width: 220px; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'تعداد نمایش' : 'Display Count'; ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_history_count<?php echo esc_attr( $suffix ); ?>" id="fas_history_count" value="<?php echo esc_attr( $history_count ); ?>" class="regular-text" min="0" max="20" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'سایز متن' : 'Text Size'; ?></td>
                            <td style="padding: 10px 0;">
                                <input type="number" name="fas_history_text_size<?php echo esc_attr( $suffix ); ?>" id="fas_history_text_size" value="<?php echo esc_attr( $history_text_size ); ?>" class="regular-text" min="10" max="24" style="width: 100%; border-radius: 6px; border: 1px solid #cbd5e1;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'رنگ پس زمینه آیتم' : 'Item Background'; ?></td>
                            <td style="padding: 10px 0;">
                                <input type="text" name="fas_history_bg<?php echo esc_attr( $suffix ); ?>" id="fas_history_bg" value="<?php echo esc_attr( $history_bg ); ?>" class="fas-color-picker" data-alpha-enabled="true">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'رنگ هاور آیتم' : 'Item Hover Background'; ?></td>
                            <td style="padding: 10px 0;">
                                <input type="text" name="fas_history_hover_bg<?php echo esc_attr( $suffix ); ?>" id="fas_history_hover_bg" value="<?php echo esc_attr( $history_hover_bg ); ?>" class="fas-color-picker" data-alpha-enabled="true">
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Section 3: Floating Trigger Position & Offsets -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
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
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo $is_rtl ? 'سایز دکمه (دسکتاپ / موبایل)' : 'Button Size (Desktop / Mobile)'; ?></td>
                            <td style="padding: 10px 0; display: flex; justify-content: space-between; gap: 4px;">
                                <input type="number" name="fas_btn_size_desktop<?php echo esc_attr( $suffix ); ?>" id="fas_btn_size_desktop" value="<?php echo esc_attr( get_option( 'fas_btn_size_desktop' . $suffix, 56 ) ); ?>" class="regular-text" style="width: 48%; min-width: 80px; box-sizing: border-box; border-radius: 6px; border: 1px solid #cbd5e1;" placeholder="Desktop px">
                                <input type="number" name="fas_btn_size_mobile<?php echo esc_attr( $suffix ); ?>" id="fas_btn_size_mobile" value="<?php echo esc_attr( get_option( 'fas_btn_size_mobile' . $suffix, 48 ) ); ?>" class="regular-text" style="width: 48%; min-width: 80px; box-sizing: border-box; border-radius: 6px; border: 1px solid #cbd5e1;" placeholder="Mobile px">
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

            <!-- Right Column: Reordering & Tabs Customization + Interactive Live Preview underneath -->
            <div class="fas-settings-column-right" style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Category customization card -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
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
                                <?php if ( ! empty( $tab['custom_icon'] ) ) : ?>
                                    <img src="<?php echo esc_url( $tab['custom_icon'] ); ?>" style="width:16px; height:16px; object-fit:contain; flex-shrink:0;">
                                <?php else : ?>
                                    <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" style="font-size: 16px; width: 16px; height:16px; color: <?php echo esc_attr( $tab['color'] ); ?>;"></span>
                                <?php endif; ?>
                                <span><?php echo esc_html( $tab['title'] ); ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <input type="hidden" name="fas_tabs_order<?php echo esc_attr( $suffix ); ?>" id="fas_tabs_order" value="<?php echo esc_attr( $tabs_order ); ?>">

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        
                        <!-- Tab 1: All -->
                        <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                            <span style="font-weight: 700; color: #1e293b; font-size: 13px;"><?php echo esc_html( $i18n['tab_all_cust'] ); ?></span>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <input type="text" name="fas_tab_all_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_title" value="<?php echo esc_attr( $tab_all_title ); ?>" class="regular-text" placeholder="All Results" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                                <input type="text" name="fas_tab_all_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_icon" value="<?php echo esc_attr( $tab_all_icon ); ?>" class="regular-text" placeholder="dashicons-grid-view" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                            </div>
                            
                            <!-- Custom SVG/PNG Icon Row -->
                            <div style="margin-top: 10px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px;"><?php echo esc_html( $i18n['upload_lbl'] ); ?></span>
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" name="fas_tab_all_custom_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_custom_icon" value="<?php echo esc_attr( $tab_all_custom_icon ); ?>" style="flex: 1; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; padding: 6px 12px;" placeholder="https://example.com/icon.svg">
                                    <button type="button" class="button fas-upload-btn" data-target="fas_tab_all_custom_icon" style="border-radius: 6px; font-weight: 700; height: auto; padding: 6px 14px;"><?php echo esc_html( $i18n['upload_btn'] ); ?></button>
                                </div>
                            </div>

                            <div style="margin-top: 10px;">
                                <input type="text" name="fas_tab_all_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_color" value="<?php echo esc_attr( $tab_all_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>

                        <!-- Tab 2: Products -->
                        <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                            <span style="font-weight: 700; color: #1e293b; font-size: 13px;"><?php echo esc_html( $i18n['tab_prod_cust'] ); ?></span>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <input type="text" name="fas_tab_products_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_title" value="<?php echo esc_attr( $tab_products_title ); ?>" class="regular-text" placeholder="Products" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                                <input type="text" name="fas_tab_products_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_icon" value="<?php echo esc_attr( $tab_products_icon ); ?>" class="regular-text" placeholder="dashicons-cart" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                            </div>
                            
                            <!-- Custom SVG/PNG Icon Row -->
                            <div style="margin-top: 10px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px;"><?php echo esc_html( $i18n['upload_lbl'] ); ?></span>
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" name="fas_tab_products_custom_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_custom_icon" value="<?php echo esc_attr( $tab_products_custom_icon ); ?>" style="flex: 1; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; padding: 6px 12px;" placeholder="https://example.com/icon.svg">
                                    <button type="button" class="button fas-upload-btn" data-target="fas_tab_products_custom_icon" style="border-radius: 6px; font-weight: 700; height: auto; padding: 6px 14px;"><?php echo esc_html( $i18n['upload_btn'] ); ?></button>
                                </div>
                            </div>

                            <div style="margin-top: 10px;">
                                <input type="text" name="fas_tab_products_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_color" value="<?php echo esc_attr( $tab_products_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>

                        <!-- Tab 3: Posts -->
                        <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                            <span style="font-weight: 700; color: #1e293b; font-size: 13px;"><?php echo esc_html( $i18n['tab_post_cust'] ); ?></span>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <input type="text" name="fas_tab_posts_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_title" value="<?php echo esc_attr( $tab_posts_title ); ?>" class="regular-text" placeholder="News & Articles" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                                <input type="text" name="fas_tab_posts_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_icon" value="<?php echo esc_attr( $tab_posts_icon ); ?>" class="regular-text" placeholder="dashicons-welcome-write-blog" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                            </div>
                            
                            <!-- Custom SVG/PNG Icon Row -->
                            <div style="margin-top: 10px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px;"><?php echo esc_html( $i18n['upload_lbl'] ); ?></span>
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" name="fas_tab_posts_custom_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_custom_icon" value="<?php echo esc_attr( $tab_posts_custom_icon ); ?>" style="flex: 1; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; padding: 6px 12px;" placeholder="https://example.com/icon.svg">
                                    <button type="button" class="button fas-upload-btn" data-target="fas_tab_posts_custom_icon" style="border-radius: 6px; font-weight: 700; height: auto; padding: 6px 14px;"><?php echo esc_html( $i18n['upload_btn'] ); ?></button>
                                </div>
                            </div>

                            <div style="margin-top: 10px;">
                                <input type="text" name="fas_tab_posts_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_color" value="<?php echo esc_attr( $tab_posts_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>

                        <!-- Tab 4: Docs -->
                        <div style="background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);">
                            <span style="font-weight: 700; color: #1e293b; font-size: 13px;"><?php echo esc_html( $i18n['tab_doc_cust'] ); ?></span>
                            <div style="display: flex; gap: 10px; margin-top: 10px;">
                                <input type="text" name="fas_tab_docs_title<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_title" value="<?php echo esc_attr( $tab_docs_title ); ?>" class="regular-text" placeholder="Documentation" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                                <input type="text" name="fas_tab_docs_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_icon" value="<?php echo esc_attr( $tab_docs_icon ); ?>" class="regular-text" placeholder="dashicons-book-alt" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;">
                            </div>
                            
                            <!-- Custom SVG/PNG Icon Row -->
                            <div style="margin-top: 10px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                <span style="font-size: 11px; color: #64748b; font-weight: 700; display: block; margin-bottom: 4px;"><?php echo esc_html( $i18n['upload_lbl'] ); ?></span>
                                <div style="display: flex; gap: 8px;">
                                    <input type="text" name="fas_tab_docs_custom_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_custom_icon" value="<?php echo esc_attr( $tab_docs_custom_icon ); ?>" style="flex: 1; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 12px; padding: 6px 12px;" placeholder="https://example.com/icon.svg">
                                    <button type="button" class="button fas-upload-btn" data-target="fas_tab_docs_custom_icon" style="border-radius: 6px; font-weight: 700; height: auto; padding: 6px 14px;"><?php echo esc_html( $i18n['upload_btn'] ); ?></button>
                                </div>
                            </div>

                            <div style="margin-top: 10px;">
                                <input type="text" name="fas_tab_docs_color<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_color" value="<?php echo esc_attr( $tab_docs_color ); ?>" class="fas-color-picker">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Interactive Live Preview placed beautifully under the Category sorting/customization cards -->
                <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                        <span style="background: #e11d48; color: #fff; font-size: 10px; padding: 3px 8px; border-radius: 12px; font-weight: 800; text-transform: uppercase;"><?php echo esc_html( $i18n['live'] ); ?></span>
                        <span><?php echo esc_html( $i18n['live_preview'] ); ?></span>
                    </h3>
                    
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 24px;">
                        <?php echo esc_html( $i18n['live_preview_desc'] ); ?>
                    </p>

                    <!-- Device Toggle Controls -->
                    <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 20px;">
                        <button type="button" class="button fas-preview-toggle is-active" data-device="desktop" style="border-radius: 20px; font-weight: 600; background: #0066cc; color: white; border: none; padding: 5px 15px;">Desktop View</button>
                        <button type="button" class="button fas-preview-toggle" data-device="mobile" style="border-radius: 20px; font-weight: 600; padding: 5px 15px;">Mobile View</button>
                    </div>

                    <!-- Centered live preview container wrapper with generous workspace -->
                    <div id="fas-mock-modal-wrapper" style="position: relative; background: radial-gradient(circle, #f8fafc 0%, #f1f5f9 100%); padding: 40px 10px; border-radius: 12px; display: flex; justify-content: center; align-items: center; overflow: auto; min-height: 400px; border: 1px solid #cbd5e1;">
                        
                        <!-- Mock Floating Button -->
                        <div id="fas-mock-floating-btn" style="position: absolute; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: white; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);">
                            <span class="dashicons dashicons-search" style="font-size: 20px;"></span>
                        </div>

                        <div id="fas-preview-container" class="fas-search-container" style="display: none; width: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; <?php echo $dir_style; ?>">
                            
                            <!-- Search History mock -->
                            <div id="fas-preview-history" class="fas-search-history" style="padding: 10px 24px; border-bottom: 1px solid #e2e8f0; display: block; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                                    <span style="font-size: 13px; font-weight: 600; color: #64748b;"><?php echo $is_rtl ? 'تاریخچه جستجو' : 'Search History'; ?></span>
                                    <span style="font-size: 12px; color: #e11d48; cursor: pointer;"><?php echo $is_rtl ? 'پاک کردن' : 'Clear'; ?></span>
                                </div>
                                <div id="fas-preview-history-items" style="display: flex; flex-wrap: wrap; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                                    <!-- Items injected via JS -->
                                </div>
                            </div>

                            <!-- Input wrapper -->
                            <div class="fas-search-input-wrapper" style="display: flex; align-items: center; padding: 18px 24px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                                <span class="dashicons dashicons-search" style="color: #64748b; margin: <?php echo $is_rtl ? '0 0 0 12px' : '0 12px 0 0'; ?>; font-size: 20px; width: 20px; height: 20px;"></span>
                                <input id="fas-mock-preview-input" type="text" placeholder="<?php echo esc_attr( $i18n['type_search'] ); ?>" style="border:none; outline:none; background:transparent; width:100%; font-size:18px; color:#1e293b; font-weight:500; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                
                                <!-- Close button in preview -->
                                <button class="fas-modal-close" style="background: rgba(100, 116, 139, 0.1); border: none !important; color: #64748b; width: 34px; height: 34px; border-radius: 50% !important; margin: <?php echo $is_rtl ? '0 14px 0 0' : '0 0 0 14px'; ?>; display: flex; align-items: center; justify-content: center; cursor: pointer;">
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
                                <!-- Will be injected via JS -->
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
jQuery(document).ready(function($) {
    var activeDevice = 'desktop';

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

    // Media library uploader script for SVG/PNG icon uploads
    $('.fas-upload-btn').on('click', function(e) {
        e.preventDefault();
        var button = $(this);
        var target_id = button.data('target');
        var uploader = wp.media({
            title: '<?php echo esc_js( $is_rtl ? 'انتخاب آیکون' : 'Choose Icon' ); ?>',
            button: {
                text: '<?php echo esc_js( $is_rtl ? 'تایید و درج آیکون' : 'Select Icon' ); ?>'
            },
            multiple: false
        }).on('select', function() {
            var attachment = uploader.state().get('selection').first().toJSON();
            $('#' + target_id).val(attachment.url).trigger('change');
            updateLivePreview();
        }).open();
    });

    // Handle Page Display Select changes
    $('#fas_display_pages_type').on('change', function() {
        if ($(this).val() === 'specific') {
            $('#fas_specific_pages_row').show();
        } else {
            $('#fas_specific_pages_row').hide();
        }
    });

    // Device Toggle
    $('.fas-preview-toggle').on('click', function() {
        $('.fas-preview-toggle').removeClass('is-active').css({ 'background': '', 'color': '' });
        $(this).addClass('is-active').css({ 'background': '#0066cc', 'color': 'white' });
        activeDevice = $(this).data('device');
        updateLivePreview();
    });

    // Mock Floating Button Click
    $('#fas-mock-floating-btn').on('click', function() {
        $(this).hide();
        $('#fas-preview-container').fadeIn(200);
    });

    // Mock Close Button
    $('.fas-modal-close').on('click', function(e) {
        e.preventDefault();
        $('#fas-preview-container').fadeOut(200, function() {
            $('#fas-mock-floating-btn').show();
        });
    });

    // Mock Input typing
    $('#fas-mock-preview-input').prop('disabled', false).on('input', function() {
        var val = $(this).val();
        if (val.length > 0) {
            renderMockResults();
        } else {
            $('#fas-preview-results').html('');
        }
    });

    // Re-render live preview on settings modification
    $('#fas-settings-form input, #fas-settings-form select').on('input change', updateLivePreview);

    function updateLivePreview() {
        // Dimensions & Device
        var width = $('#fas_popup_width').val() || 750;
        var maxH  = $('#fas_popup_max_height').val() || 600;
        
        var isMobile = (activeDevice === 'mobile');
        var containerWidth = isMobile ? '360px' : width + 'px';
        var containerHeight = isMobile ? '640px' : maxH + 'px';
        
        $('#fas-preview-container').css({
            'max-width': containerWidth,
            'max-height': containerHeight,
            'height': isMobile ? '640px' : 'auto'
        });

        // Floating Button
        var btnSize = isMobile ? ($('#fas_btn_size_mobile').val() || 48) : ($('#fas_btn_size_desktop').val() || 56);
        var btnColor = $('#fas_floating_bg').val() || '#0066cc';
        var btnPos = $('#fas_floating_position').val() || 'bottom-right';
        var offsetX = $('#fas_floating_offset_x').val() || 24;
        var offsetY = $('#fas_floating_offset_y').val() || 24;
        
        var posCss = {
            'width': btnSize + 'px',
            'height': btnSize + 'px',
            'background': btnColor,
            'top': 'auto',
            'bottom': 'auto',
            'left': 'auto',
            'right': 'auto'
        };

        if (btnPos === 'bottom-right') {
            posCss['bottom'] = offsetY + 'px';
            posCss['right'] = offsetX + 'px';
        } else if (btnPos === 'bottom-left') {
            posCss['bottom'] = offsetY + 'px';
            posCss['left'] = offsetX + 'px';
        } else if (btnPos === 'top-right') {
            posCss['top'] = offsetY + 'px';
            posCss['right'] = offsetX + 'px';
        } else if (btnPos === 'top-left') {
            posCss['top'] = offsetY + 'px';
            posCss['left'] = offsetX + 'px';
        }

        $('#fas-mock-floating-btn').css(posCss);

        // History Mock styling
        var histTextSize = $('#fas_history_text_size').val() || 13;
        var histBg = $('#fas_history_bg').val() || 'rgba(255, 255, 255, 0.1)';

        // Handle hex to rgba fallback if user selects solid color from picker
        if (histBg.indexOf('#') === 0 && histBg.length === 7) {
            var r = parseInt(histBg.slice(1, 3), 16),
                g = parseInt(histBg.slice(3, 5), 16),
                b = parseInt(histBg.slice(5, 7), 16);
            histBg = 'rgba(' + r + ', ' + g + ', ' + b + ', 0.1)';
        }

        var isRtl = <?php echo $is_rtl ? 'true' : 'false'; ?>;
        var histHtml = '';
        var mockTerms = ['phase-30', 'antenna', 'radio'];
        for(var i=0; i<Math.min(mockTerms.length, ($('#fas_history_count').val() || 5)); i++) {
            histHtml += '<button style="background: '+histBg+'; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); color: #0f172a; padding: 6px 12px; border-radius: 16px; font-size: '+histTextSize+'px; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); flex-direction: '+(isRtl ? 'row-reverse' : 'row')+';">';
            histHtml += '<span>'+mockTerms[i]+'</span>';
            histHtml += '<span style="color: #64748b; font-size: 14px;">&times;</span>';
            histHtml += '</button>';
        }
        $('#fas-preview-history-items').html(histHtml);

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
            $('#fas-preview-history').css('border-bottom-color', '#1e293b');
            $('#fas-preview-history-items button').css('color', '#f8fafc').css('border-color', 'rgba(255,255,255,0.08)');
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
            $('#fas-preview-history').css('border-bottom-color', '#e2e8f0');
            $('#fas-preview-history-items button').css('color', '#0f172a').css('border-color', 'rgba(0,0,0,0.06)');
        }

        // Live preview of Farsi placeholder when editing Farsi settings
        var placeholderVal = '<?php echo esc_js( $i18n['type_search'] ); ?>';
        $('#fas-mock-preview-input').attr('placeholder', placeholderVal);

        // Parse Tabs Configuration & Order
        var orderRaw = $('#fas_tabs_order').val() || 'all,products,posts,docs';
        var orderArr = orderRaw.split(',').map(function(item) { return item.trim(); });

        var tabsHtml = '';
        var activeClassAdded = false;

        orderArr.forEach(function(key) {
            var title = '';
            var color = '';
            var icon  = '';
            var customIcon = '';

            if (key === 'all') {
                title = $('#fas_tab_all_title').val() || 'All Results';
                color = $('#fas_tab_all_color').val() || '#0066cc';
                icon  = $('#fas_tab_all_icon').val() || 'dashicons-grid-view';
                customIcon = $('#fas_tab_all_custom_icon').val() || '';
            } else if (key === 'products') {
                title = $('#fas_tab_products_title').val() || 'Products';
                color = $('#fas_tab_products_color').val() || '#10b981';
                icon  = $('#fas_tab_products_icon').val() || 'dashicons-cart';
                customIcon = $('#fas_tab_products_custom_icon').val() || '';
            } else if (key === 'posts') {
                title = $('#fas_tab_posts_title').val() || 'News & Articles';
                color = $('#fas_tab_posts_color').val() || '#f59e0b';
                icon  = $('#fas_tab_posts_icon').val() || 'dashicons-welcome-write-blog';
                customIcon = $('#fas_tab_posts_custom_icon').val() || '';
            } else if (key === 'docs') {
                title = $('#fas_tab_docs_title').val() || 'Documentation';
                color = $('#fas_tab_docs_color').val() || '#6366f1';
                icon  = $('#fas_tab_docs_icon').val() || 'dashicons-book-alt';
                customIcon = $('#fas_tab_docs_custom_icon').val() || '';
            } else {
                return; // skip unknown keys
            }

            var isActive = !activeClassAdded;
            activeClassAdded = true;

            var tabStyle = 'display: flex; align-items: center; gap: 8px; padding: 8px 16px; border: 1px solid '+(isActive ? color : 'transparent')+'; background: '+(isActive ? color : 'rgba(100, 116, 139, 0.05)')+'; font-weight: 600; font-size:13px; cursor: pointer; color: ' + (isActive ? '#ffffff' : '#64748b') + '; border-radius: 20px; transition: all 0.3s ease; white-space: nowrap;';
            
            var iconHtml = '';
            if (customIcon) {
                iconHtml = '<img src="' + customIcon + '" style="width:16px; height:16px; object-fit:contain; flex-shrink:0;">';
            } else {
                iconHtml = '<span class="dashicons ' + icon + '" style="font-size:18px; width:18px; height:18px; color:' + (isActive ? '#ffffff' : '#64748b') + ';"></span>';
            }

            tabsHtml += '<button type="button" style="' + tabStyle + '">' +
                        iconHtml +
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

        if ($('#fas-mock-preview-input').val() !== '') {
            renderMockResults();
        }
    }

    function renderMockResults() {
        $('#fas-preview-history').hide();
        var isMobile = (activeDevice === 'mobile');
        var titleSize = isMobile ? ($('#fas_title_size_mobile').val() || 14) : ($('#fas_title_size_desktop').val() || 15);
        var excerptSize = isMobile ? ($('#fas_excerpt_size_mobile').val() || 12) : ($('#fas_excerpt_size_desktop').val() || 13);
        
        var mode = $('#fas_theme_mode').val();
        var itemBg = (mode === 'dark') ? '#1e293b' : '#f8fafc';
        var titleColor = (mode === 'dark') ? '#f8fafc' : '#0f172a';
        var excerptColor = (mode === 'dark') ? '#94a3b8' : '#64748b';
        var isRtl = <?php echo $is_rtl ? 'true' : 'false'; ?>;
        var dirStr = isRtl ? 'row-reverse' : 'row';
        var alignStr = isRtl ? 'right' : 'left';
        
        var html = '<div class="fas-result-item" style="display: flex; align-items: center; gap: 16px; padding: 12px; border-radius: 12px; margin-bottom: 8px; flex-direction: '+dirStr+'; background: '+itemBg+';">';
        html += '<div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(100,116,139,0.1); display:flex; align-items:center; justify-content:center; color:#64748b;">';
        html += '<span class="dashicons dashicons-cart" style="font-size: 22px; width:22px; height:22px;"></span>';
        html += '</div>';
        html += '<div style="text-align: '+alignStr+';">';
        html += '<h4 style="margin: 0 0 4px 0; font-size: '+titleSize+'px; font-weight:600; color: '+titleColor+';">' + (isRtl ? 'آنتن فوق پیشرفته Phase-30ISO' : 'Phase-30ISO Antenna') + '</h4>';
        html += '<p style="margin:0; font-size: '+excerptSize+'px; color: '+excerptColor+'; line-height:1.4;">' + (isRtl ? 'محصول مخابراتی دو بانده فوق پیشرفته...' : 'Premium dual-band technical product spec...') + '</p>';
        html += '</div>';
        html += '</div>';

        $('#fas-preview-results').html(html);
    }

    // Initial load
    updateLivePreview();
});
</script>
