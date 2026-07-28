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
                                <div class="fas-input-row">
                                    <input type="number" name="fas_cache_duration<?php echo esc_attr( $suffix ); ?>" id="fas_cache_duration" value="<?php echo esc_attr( $cache_duration ); ?>" class="regular-text" min="0" style="border-radius: 6px; border: 1px solid #cbd5e1;">
                                    <div class="fas-tooltip-wrapper">
                                        <span class="fas-info-icon">!</span>
                                        <div class="fas-tooltip-content"><?php echo esc_html( $i18n['cache_duration_desc'] ); ?> (مجاز: 0 به بالا)</div>
                                    </div>
                                </div>
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
                                <div class="fas-input-row">
                                    <input type="number" name="fas_popup_width<?php echo esc_attr( $suffix ); ?>" id="fas_popup_width" value="<?php echo esc_attr( $popup_width ); ?>" class="regular-text" min="400" max="1200" style="border-radius: 6px; border: 1px solid #cbd5e1;">
                                    <div class="fas-tooltip-wrapper">
                                        <span class="fas-info-icon">!</span>
                                        <div class="fas-tooltip-content"><?php echo esc_html( $i18n['popup_width_desc'] ); ?> (مجاز: 400 تا 1200)</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['popup_height'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <div class="fas-input-row">
                                    <input type="number" name="fas_popup_max_height<?php echo esc_attr( $suffix ); ?>" id="fas_popup_max_height" value="<?php echo esc_attr( $popup_max_height ); ?>" class="regular-text" min="300" max="900" style="border-radius: 6px; border: 1px solid #cbd5e1;">
                                    <div class="fas-tooltip-wrapper">
                                        <span class="fas-info-icon">!</span>
                                        <div class="fas-tooltip-content"><?php echo esc_html( $i18n['popup_height_desc'] ); ?> (مجاز: 300 تا 900)</div>
                                    </div>
                                </div>
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
                                <div class="fas-input-row">
                                    <input type="number" name="fas_floating_offset_x<?php echo esc_attr( $suffix ); ?>" id="fas_floating_offset_x" value="<?php echo esc_attr( $floating_offset_x ); ?>" class="regular-text" min="0" max="100" style="border-radius: 6px; border: 1px solid #cbd5e1;">
                                    <div class="fas-tooltip-wrapper">
                                        <span class="fas-info-icon">!</span>
                                        <div class="fas-tooltip-content"><?php echo esc_html( $i18n['offset_x_desc'] ); ?> (مجاز: 0 تا 100)</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 0; font-weight: 600; color: #475569;"><?php echo esc_html( $i18n['offset_y'] ); ?></td>
                            <td style="padding: 10px 0;">
                                <div class="fas-input-row">
                                    <input type="number" name="fas_floating_offset_y<?php echo esc_attr( $suffix ); ?>" id="fas_floating_offset_y" value="<?php echo esc_attr( $floating_offset_y ); ?>" class="regular-text" min="0" max="100" style="border-radius: 6px; border: 1px solid #cbd5e1;">
                                    <div class="fas-tooltip-wrapper">
                                        <span class="fas-info-icon">!</span>
                                        <div class="fas-tooltip-content"><?php echo esc_html( $i18n['offset_y_desc'] ); ?> (مجاز: 0 تا 100)</div>
                                    </div>
                                </div>
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
                    <div id="fas-sortable-tabs" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px; padding: 12px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; flex-direction: row;">
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
                                <div style="flex:1; display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons <?php echo esc_attr( $tab_all_icon ); ?>" id="fas_tab_all_icon_preview"></span>
                                    <input type="text" name="fas_tab_all_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_all_icon" value="<?php echo esc_attr( $tab_all_icon ); ?>" class="regular-text" placeholder="dashicons-grid-view" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;" oninput="document.getElementById('fas_tab_all_icon_preview').className = 'dashicons ' + this.value;">
                                </div>
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
                                <div style="flex:1; display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons <?php echo esc_attr( $tab_products_icon ); ?>" id="fas_tab_products_icon_preview"></span>
                                    <input type="text" name="fas_tab_products_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_products_icon" value="<?php echo esc_attr( $tab_products_icon ); ?>" class="regular-text" placeholder="dashicons-cart" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;" oninput="document.getElementById('fas_tab_products_icon_preview').className = 'dashicons ' + this.value;">
                                </div>
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
                                <div style="flex:1; display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons <?php echo esc_attr( $tab_posts_icon ); ?>" id="fas_tab_posts_icon_preview"></span>
                                    <input type="text" name="fas_tab_posts_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_posts_icon" value="<?php echo esc_attr( $tab_posts_icon ); ?>" class="regular-text" placeholder="dashicons-welcome-write-blog" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;" oninput="document.getElementById('fas_tab_posts_icon_preview').className = 'dashicons ' + this.value;">
                                </div>
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
                                <div style="flex:1; display: flex; align-items: center; gap: 8px;">
                                    <span class="dashicons <?php echo esc_attr( $tab_docs_icon ); ?>" id="fas_tab_docs_icon_preview"></span>
                                    <input type="text" name="fas_tab_docs_icon<?php echo esc_attr( $suffix ); ?>" id="fas_tab_docs_icon" value="<?php echo esc_attr( $tab_docs_icon ); ?>" class="regular-text" placeholder="dashicons-book-alt" style="flex:1; border-radius: 6px; border: 1px solid #cbd5e1; padding: 6px 12px;" oninput="document.getElementById('fas_tab_docs_icon_preview').className = 'dashicons ' + this.value;">
                                </div>
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
                    <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="background: #e11d48; color: #fff; font-size: 10px; padding: 3px 8px; border-radius: 12px; font-weight: 800; text-transform: uppercase;"><?php echo esc_html( $i18n['live'] ); ?></span>
                            <span><?php echo esc_html( $i18n['live_preview'] ); ?></span>
                        </div>
                        
                        <!-- Preview Mode Selector -->
                        <div style="display: flex; gap: 6px;">
                            <button type="button" class="button fas-mode-toggle is-active" data-mode="popup" style="border-radius: 16px; font-size: 11px; font-weight: 700; background: #0066cc; color: white; border: none; padding: 4px 12px; cursor: pointer;"><?php echo $is_rtl ? 'پاپ‌آپ پدیدار شده' : 'Open Popup Mode'; ?></button>
                            <button type="button" class="button fas-mode-toggle" data-mode="button" style="border-radius: 16px; font-size: 11px; font-weight: 700; padding: 4px 12px; cursor: pointer;"><?php echo $is_rtl ? 'دکمه شناور' : 'Floating Button Mode'; ?></button>
                        </div>
                    </h3>
                    
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
                        <?php echo esc_html( $i18n['live_preview_desc'] ); ?>
                    </p>

                    <!-- Device Toggle Controls -->
                    <div style="display: flex; gap: 12px; justify-content: center; margin-bottom: 20px;">
                        <button type="button" class="button fas-preview-toggle is-active" data-device="desktop" style="border-radius: 20px; font-weight: 600; background: #0066cc; color: white; border: none; padding: 5px 18px; cursor: pointer;">Desktop View</button>
                        <button type="button" class="button fas-preview-toggle" data-device="mobile" style="border-radius: 20px; font-weight: 600; padding: 5px 18px; cursor: pointer;">Mobile View</button>
                    </div>

                    <!-- Scoped CSS Overrides to prevent floating button & overlay from spilling into Admin UI -->
                    <style>
                        #fas-mock-preview-area {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            padding: 20px 0;
                            background: #f8fafc;
                            border-radius: 12px;
                            border: 1px dashed #cbd5e1;
                            min-height: 540px;
                        }
                        #fas-mock-modal-wrapper.fas-desktop-canvas {
                            width: 100%;
                            min-height: 520px;
                            border-radius: 16px;
                            position: relative !important;
                            overflow: hidden !important;
                            background: radial-gradient(circle, #f8fafc 0%, #cbd5e1 100%);
                            border: 1px solid #cbd5e1;
                            box-shadow: inset 0 2px 6px rgba(0,0,0,0.04);
                            transition: all 0.3s ease;
                        }
                        #fas-mock-modal-wrapper.fas-phone-bezel {
                            width: 340px;
                            height: 560px;
                            border-radius: 38px;
                            border: 10px solid #0f172a;
                            position: relative !important;
                            overflow: hidden !important;
                            background: radial-gradient(circle, #f8fafc 0%, #cbd5e1 100%);
                            box-shadow: 0 20px 40px -10px rgba(15, 23, 42, 0.35);
                            transition: all 0.3s ease;
                        }
                        #fas-mock-modal-wrapper #fas-preview-floating-btn {
                            position: absolute !important;
                            z-index: 5 !important;
                            margin: 0 !important;
                            cursor: pointer;
                        }
                        #fas-mock-modal-wrapper .fas-search-overlay {
                            position: absolute !important;
                            top: 0 !important;
                            left: 0 !important;
                            width: 100% !important;
                            height: 100% !important;
                            z-index: 10 !important;
                            padding-top: 50px !important;
                            box-sizing: border-box !important;
                        }
                        #fas-mock-modal-wrapper.fas-phone-bezel .fas-search-overlay {
                            padding-top: 35px !important;
                        }
                        #fas-mock-modal-wrapper.fas-phone-bezel .fas-search-container {
                            width: 92% !important;
                            max-width: 320px !important;
                        }
                        .fas-phone-notch {
                            width: 110px;
                            height: 16px;
                            background: #0f172a;
                            border-bottom-left-radius: 10px;
                            border-bottom-right-radius: 10px;
                            position: absolute;
                            top: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            z-index: 40;
                        }
                    </style>

                    <!-- Main Preview Workspace -->
                    <div id="fas-mock-preview-area">
                        <div id="fas-mock-modal-wrapper" class="fas-desktop-canvas">
                            
                            <!-- Phone Notch (Visible only in Mobile View) -->
                            <div id="fas-phone-notch-el" class="fas-phone-notch" style="display: none;"></div>

                            <!-- Floating Trigger Button Mockup -->
                            <button type="button" id="fas-preview-floating-btn" class="fas-search-trigger fas-floating-trigger" style="display: none;">
                                <svg class="fas-search-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block !important; width: 22px !important; height: 22px !important; visibility: visible !important;">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </button>

                            <!-- Search Modal Overlay Mockup -->
                            <div id="fas-preview-modal-overlay" class="fas-search-overlay fas-mock-overlay is-open">
                                <div class="fas-search-container" id="fas-preview-search-container">
                                    <!-- Input Wrapper -->
                                    <div class="fas-search-input-wrapper">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="11" cy="11" r="8"></circle>
                                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                        </svg>
                                        <input type="text" class="fas-search-input" id="fas-preview-mock-input" placeholder="<?php echo esc_attr( $i18n['type_search'] ); ?>" readonly style="cursor: pointer;">
                                        <span class="fas-modal-close" id="fas-preview-modal-close-btn" role="button" aria-label="Close Search">&times;</span>
                                    </div>

                                    <!-- Search History Panel -->
                                    <div class="fas-search-history" id="fas-preview-search-history" style="display: block;"></div>

                                    <!-- Category Tabs -->
                                    <div class="fas-search-tabs" id="fas-preview-search-tabs"></div>

                                    <!-- Results Panel -->
                                    <div class="fas-results-panel" id="fas-preview-results-panel">
                                        <div class="fas-tab-content is-active" id="fas-preview-tab-content"></div>
                                    </div>
                                </div>
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
    var activeMode   = 'popup'; // 'popup' or 'button'

    var suffix = '<?php echo esc_js( $suffix ); ?>';
    var isRtl  = <?php echo $is_rtl ? 'true' : 'false'; ?>;

    // Helper to extract option value across suffix variants
    function getVal(key, fallback) {
        var el = $('[name="' + key + suffix + '"]');
        if (!el.length || el.val() === null || el.val() === '') {
            el = $('[name="' + key + '"]');
        }
        if (!el.length || el.val() === null || el.val() === '') {
            el = $('#' + key);
        }
        if (el.length && el.val() !== null && el.val() !== '') {
            return el.val();
        }
        return fallback;
    }

    // Initialize standard WP color pickers
    $('.fas-color-picker').wpColorPicker({
        change: function(event, ui) {
            setTimeout(updateLivePreview, 20);
        },
        clear: function() {
            setTimeout(updateLivePreview, 20);
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
            $('#fas_tabs_order' + suffix).val(order.join(','));
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

    // Device Toggle (Desktop / Mobile)
    $('.fas-preview-toggle').on('click', function() {
        $('.fas-preview-toggle').removeClass('is-active').css({ 'background': '', 'color': '' });
        $(this).addClass('is-active').css({ 'background': '#0066cc', 'color': 'white' });
        activeDevice = $(this).data('device');
        updateLivePreview();
    });

    // Mode Toggle (Popup / Button)
    $('.fas-mode-toggle').on('click', function() {
        $('.fas-mode-toggle').removeClass('is-active').css({ 'background': '', 'color': '' });
        $(this).addClass('is-active').css({ 'background': '#0066cc', 'color': 'white' });
        activeMode = $(this).data('mode');
        updateLivePreview();
    });

    // Floating Button Click in Preview opens Modal
    $(document).on('click', '#fas-preview-floating-btn', function() {
        activeMode = 'popup';
        $('.fas-mode-toggle').removeClass('is-active').css({ 'background': '', 'color': '' });
        $('.fas-mode-toggle[data-mode="popup"]').addClass('is-active').css({ 'background': '#0066cc', 'color': 'white' });
        updateLivePreview();
    });

    // Close Modal button in Preview closes Modal
    $(document).on('click', '#fas-preview-modal-close-btn', function(e) {
        e.preventDefault();
        activeMode = 'button';
        $('.fas-mode-toggle').removeClass('is-active').css({ 'background': '', 'color': '' });
        $('.fas-mode-toggle[data-mode="button"]').addClass('is-active').css({ 'background': '#0066cc', 'color': 'white' });
        updateLivePreview();
    });

    // Tab Switcher inside Preview Modal
    $(document).on('click', '#fas-preview-search-tabs .fas-tab-btn', function() {
        var key = $(this).data('tab');
        $('#fas-preview-search-tabs .fas-tab-btn').removeClass('is-active').each(function() {
            $(this).css({ 'background': '', 'border-color': '', 'color': '' });
        });
        
        var accentColor = $(this).data('color');
        $(this).addClass('is-active').css({
            'background': accentColor,
            'border-color': accentColor,
            'color': '#ffffff'
        });
        
        renderMockResultForTab(key, accentColor);
    });

    // Pure Client-side Instant Live Preview Engine
    function updateLivePreview() {
        var themeMode         = getVal('fas_theme_mode', 'dark');
        var popupWidth        = parseInt(getVal('fas_popup_width', 750), 10);
        var popupMaxHeight     = parseInt(getVal('fas_popup_max_height', 600), 10);
        var titleSizeDesktop   = getVal('fas_title_size_desktop', 15);
        var titleSizeMobile    = getVal('fas_title_size_mobile', 14);
        var excerptSizeDesktop = getVal('fas_excerpt_size_desktop', 13);
        var excerptSizeMobile  = getVal('fas_excerpt_size_mobile', 12);
        var historyCount       = parseInt(getVal('fas_history_count', 5), 10);
        var historyBg          = getVal('fas_history_bg', 'rgba(255, 255, 255, 0.1)');
        var historyHoverBg     = getVal('fas_history_hover_bg', 'rgba(255, 255, 255, 0.2)');
        var historyTextSize    = getVal('fas_history_text_size', 13);
        var floatingBg         = getVal('fas_floating_bg', '#0066cc');
        var btnSizeDesktop     = getVal('fas_btn_size_desktop', 56);
        var btnSizeMobile      = getVal('fas_btn_size_mobile', 48);
        var floatingPos        = getVal('fas_floating_position', 'bottom-right');
        var floatingOffsetX    = parseInt(getVal('fas_floating_offset_x', 24), 10);
        var floatingOffsetY    = parseInt(getVal('fas_floating_offset_y', 24), 10);
        var enableFloating     = getVal('fas_enable_floating', 'yes');

        var isMobile = (activeDevice === 'mobile');

        // Apply device framing (Desktop vs Mobile Bezel)
        var wrapper = $('#fas-mock-modal-wrapper');
        var notch = $('#fas-phone-notch-el');
        if (isMobile) {
            wrapper.removeClass('fas-desktop-canvas').addClass('fas-phone-bezel');
            notch.show();
        } else {
            wrapper.removeClass('fas-phone-bezel').addClass('fas-desktop-canvas');
            notch.hide();
        }

        // Apply dynamic CSS variables on mock wrapper
        wrapper.css({
            '--fas-primary': floatingBg,
            '--fas-popup-width': (isMobile ? '320px' : popupWidth + 'px'),
            '--fas-popup-max-height': (isMobile ? '460px' : popupMaxHeight + 'px'),
            '--fas-title-size-desktop': (isMobile ? titleSizeMobile : titleSizeDesktop) + 'px',
            '--fas-excerpt-size-desktop': (isMobile ? excerptSizeMobile : excerptSizeDesktop) + 'px',
            '--fas-history-bg': historyBg,
            '--fas-history-hover-bg': historyHoverBg,
            '--fas-history-text-size': historyTextSize + 'px'
        });

        // Overlay Theme Mode
        var overlay = $('#fas-preview-modal-overlay');
        overlay.removeClass('fas-theme-dark fas-theme-light').addClass('fas-theme-' + themeMode);

        // Adjust visibility according to activeMode
        var floatBtn = $('#fas-preview-floating-btn');

        if (activeMode === 'button') {
            overlay.hide().css('display', 'none').removeClass('is-open');

            if (enableFloating === 'no') {
                floatBtn.hide().css('display', 'none');
            } else {
                floatBtn.show().css('display', 'flex');
                var btnSize = isMobile ? btnSizeMobile : btnSizeDesktop;
                floatBtn.css({
                    'background-color': floatingBg,
                    'width': btnSize + 'px',
                    'height': btnSize + 'px',
                    'border-radius': '50%',
                    'align-items': 'center',
                    'justify-content': 'center',
                    'color': '#ffffff'
                });

                // Clamp offsets so floating button stays strictly inside mock wrapper
                floatBtn.css({ 'top': 'auto', 'bottom': 'auto', 'left': 'auto', 'right': 'auto' });
                var offX = Math.min(floatingOffsetX, 35) + 'px';
                var offY = Math.min(floatingOffsetY, 35) + 'px';

                if (floatingPos === 'bottom-right') {
                    floatBtn.css({ bottom: offY, right: offX });
                } else if (floatingPos === 'bottom-left') {
                    floatBtn.css({ bottom: offY, left: offX });
                } else if (floatingPos === 'top-right') {
                    floatBtn.css({ top: offY, right: offX });
                } else if (floatingPos === 'top-left') {
                    floatBtn.css({ top: offY, left: offX });
                }
            }
        } else {
            // Open Popup Mode
            overlay.show().css('display', 'flex').addClass('is-open');
            floatBtn.hide().css('display', 'none');
        }

        // Tabs order & details
        var tabsOrderStr = getVal('fas_tabs_order', 'all,products,posts,docs');
        var tabsOrderArr = tabsOrderStr.split(',').map(function(s){ return s.trim(); });

        var tabDetails = {
            'all': {
                title: getVal('fas_tab_all_title', 'All Results'),
                color: getVal('fas_tab_all_color', '#0066cc'),
                icon: getVal('fas_tab_all_icon', 'dashicons-grid-view'),
                custom_icon: getVal('fas_tab_all_custom_icon', '')
            },
            'products': {
                title: getVal('fas_tab_products_title', 'Products'),
                color: getVal('fas_tab_products_color', '#10b981'),
                icon: getVal('fas_tab_products_icon', 'dashicons-cart'),
                custom_icon: getVal('fas_tab_products_custom_icon', '')
            },
            'posts': {
                title: getVal('fas_tab_posts_title', 'News & Articles'),
                color: getVal('fas_tab_posts_color', '#f59e0b'),
                icon: getVal('fas_tab_posts_icon', 'dashicons-welcome-write-blog'),
                custom_icon: getVal('fas_tab_posts_custom_icon', '')
            },
            'docs': {
                title: getVal('fas_tab_docs_title', 'Documentation'),
                color: getVal('fas_tab_docs_color', '#6366f1'),
                icon: getVal('fas_tab_docs_icon', 'dashicons-book-alt'),
                custom_icon: getVal('fas_tab_docs_custom_icon', '')
            }
        };

        var tabsHtml = '';
        var currentActiveKey = $('#fas-preview-search-tabs .fas-tab-btn.is-active').data('tab');
        if (!currentActiveKey || !tabDetails[currentActiveKey]) {
            currentActiveKey = tabsOrderArr[0] || 'all';
        }

        tabsOrderArr.forEach(function(key) {
            if (!tabDetails[key]) return;
            var t = tabDetails[key];
            var isActive = (key === currentActiveKey);
            var activeClass = isActive ? 'is-active' : '';
            var activeStyle = isActive ? ('background:' + t.color + '; border-color:' + t.color + '; color:#ffffff;') : '';

            tabsHtml += '<button type="button" class="fas-tab-btn ' + activeClass + '" data-tab="' + key + '" data-color="' + t.color + '" style="--tab-accent:' + t.color + ';' + activeStyle + '">';
            if (t.custom_icon) {
                tabsHtml += '<img src="' + t.custom_icon + '" style="width:16px; height:16px; object-fit:contain; flex-shrink:0;">';
            } else {
                tabsHtml += '<span class="dashicons ' + t.icon + '"></span>';
            }
            tabsHtml += '<span>' + t.title + '</span></button>';
        });

        $('#fas-preview-search-tabs').html(tabsHtml);

        // History Panel
        var histContainer = $('#fas-preview-search-history');
        if (historyCount > 0) {
            var titleText = isRtl ? 'تاریخچه جستجو' : 'Search History';
            var clearText = isRtl ? 'پاک کردن' : 'Clear';
            var mockTerms = ['phase-30', 'antenna', 'radio', 'modem', 'wifi'];
            var histHtml = '<div class="fas-search-history-header" style="flex-direction:' + (isRtl ? 'row-reverse' : 'row') + ';">';
            histHtml += '<span class="fas-search-history-title">' + titleText + '</span>';
            histHtml += '<button type="button" class="fas-search-history-clear">' + clearText + '</button>';
            histHtml += '</div><div class="fas-search-history-items">';

            for (var i = 0; i < Math.min(mockTerms.length, historyCount); i++) {
                histHtml += '<button type="button" class="fas-search-history-item" style="flex-direction:' + (isRtl ? 'row-reverse' : 'row') + '; background:' + historyBg + '; font-size:' + historyTextSize + 'px;">';
                histHtml += '<span class="fas-search-history-item-text">' + mockTerms[i] + '</span>';
                histHtml += '<span class="fas-search-history-item-remove">&times;</span>';
                histHtml += '</button>';
            }
            histHtml += '</div>';
            histContainer.html(histHtml).show();
        } else {
            histContainer.hide();
        }

        renderMockResultForTab(currentActiveKey, tabDetails[currentActiveKey] ? tabDetails[currentActiveKey].color : '#0066cc');
    }

    function renderMockResultForTab(tabKey, accentColor) {
        var isMobile = (activeDevice === 'mobile');
        var titleSizeDesktop   = getVal('fas_title_size_desktop', 15);
        var titleSizeMobile    = getVal('fas_title_size_mobile', 14);
        var excerptSizeDesktop = getVal('fas_excerpt_size_desktop', 13);
        var excerptSizeMobile  = getVal('fas_excerpt_size_mobile', 12);

        var titleSize = isMobile ? titleSizeMobile : titleSizeDesktop;
        var excerptSize = isMobile ? excerptSizeMobile : excerptSizeDesktop;

        var dirStr = isRtl ? 'row-reverse' : 'row';
        var alignStr = isRtl ? 'right' : 'left';

        var titleMap = {
            'all': isRtl ? 'آنتن فوق پیشرفته Phase-30ISO' : 'Phase-30ISO Antenna',
            'products': isRtl ? 'رادیو وای‌فای پرقدرت Faramoj Wave' : 'Faramoj Wave High-Power Radio',
            'posts': isRtl ? 'راهنمای کامل پیکربندی شبکه فراموج' : 'Complete Faramoj Network Config Guide',
            'docs': isRtl ? 'مستندات فنی و راهنمای نصب افزونه' : 'Technical Specs & Installation Docs'
        };

        var itemTitle = titleMap[tabKey] || (isRtl ? 'نتیجه مربوط به این بخش' : 'Sample Search Result');

        var html = '<div class="fas-result-item" style="display:flex; align-items:center; gap:16px; padding:12px; border-radius:12px; margin-bottom:8px; flex-direction:' + dirStr + ';">';
        html += '<div style="width:44px; height:44px; border-radius:8px; background:rgba(100,116,139,0.15); display:flex; align-items:center; justify-content:center; color:' + accentColor + '; flex-shrink:0;">';
        html += '<span class="dashicons dashicons-search" style="font-size:22px; width:22px; height:22px; color:' + accentColor + ';"></span>';
        html += '</div>';
        html += '<div style="text-align:' + alignStr + '; flex-grow:1;">';
        html += '<h4 class="fas-result-title" style="font-size:' + titleSize + 'px; margin:0 0 4px 0; color:var(--fas-text-main); font-weight:700;">' + itemTitle + '</h4>';
        html += '<p class="fas-result-excerpt" style="font-size:' + excerptSize + 'px; margin:0; color:var(--fas-text-muted);">' + (isRtl ? 'توضیحات و مشخصات فنی محصول/مطلب اختصاصی سیستم فراموج...' : 'Technical specs and details snippet for Faramoj search engine...') + '</p>';
        html += '</div>';
        html += '</div>';

        $('#fas-preview-tab-content').html(html);
    }

    // Attach listeners to ALL inputs for instant zero-latency updates
    $('#fas-settings-form input, #fas-settings-form select, #fas-settings-form textarea').on('input change keyup', function() {
        updateLivePreview();
    });

    // Initial render
    updateLivePreview();
});
</script>
