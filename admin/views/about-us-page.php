<?php
/**
 * HTML layout for the Faramoj Advanced Search About Us submenu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Determine Admin display locale
$admin_locale = $this->get_admin_display_locale();
$is_rtl = ( 'fa' === $admin_locale );
$dir_style = $is_rtl ? 'direction: rtl; text-align: right;' : 'direction: ltr; text-align: left;';

$i18n = array(
    'title' => $is_rtl ? 'جستجوی زنده پیشرفته فراموج' : 'Faramoj Advanced Live Search',
    'badge' => $is_rtl ? 'نسخه ۱.۱.۵' : 'Version 1.1.5',
    'desc' => $is_rtl ? 'پلاگین جستجوی پیشرفته فراموج (FAS) یک موتور جستجوی زنده، بسیار بهینه و مبتنی بر AJAX است که به طور تخصصی برای محصولات مخابراتی فنی، مقالات و مستندات فنی و مهندسی طراحی و بهینه‌سازی شده است.' : 'Faramoj Advanced Search (FAS) is an ultra-high performance, AJAX-driven live search engine tailored specifically for technical telecommunication products, articles, and documentation.',
    'dev_by' => $is_rtl ? 'با افتخار توسعه‌یافته و بهینه‌سازی‌شده توسط' : 'Proudly Developed & Optimized by',
    'developer' => $is_rtl ? 'دانیال کنارکوهی' : 'Danial Kenarkoohi',
    'f1_title' => $is_rtl ? 'پایانه‌های REST سفارشی' : 'Custom REST Endpoints',
    'f1_desc' => $is_rtl ? 'با عبور از گلوگاه کند فایل admin-ajax.php وردپرس، به طور مستقیم به REST API متصل شده و سرعت پیشنهادات زنده را چند برابر می‌کند.' : 'Bypasses the standard bottleneck of admin-ajax.php to provide instant, real-time live suggestions.',
    'f2_title' => $is_rtl ? 'سازگاری کامل چندزبانه' : 'Multilingual Compatibility',
    'f2_desc' => $is_rtl ? 'یکپارچه‌سازی بومی با افزونه‌های Polylang و WPML به همراه فیلترینگ خودکار زبان فعال و داشتن تب‌های ترجمه اختصاصی.' : 'Integrated natively with Polylang and WPML. Automatic locale filtering and separate translations options.',
    'f3_title' => $is_rtl ? 'نمایه‌سازی پیشرفته فیلدهای ACF' : 'ACF Complex Indexing',
    'f3_desc' => $is_rtl ? 'توانایی جستجوی عمیق و بومی در فیلدهای اختصاصی پیشرفته (مانند frequency_range و technical_specifications).' : 'Indexes customized Advanced Custom Fields (frequency_range & technical_specifications) natively.',
    'f4_title' => $is_rtl ? 'سازگاری دوطرفه اعداد (Persian/Arabic)' : 'Dual Digit Compatibility',
    'f4_desc' => $is_rtl ? 'موتور نرمال‌سازی پیشرفته که اعداد فارسی و عربی تایپ شده توسط کاربر را با اعداد انگلیسی پایگاه داده مطابقت می‌دهد.' : 'Bi-directional normalization search engine matching Persian, Arabic and English numerical characters.',
);
?>
<div class="wrap fas-admin-wrap" style="max-width: 850px; margin: 40px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; text-align: center; <?php echo $dir_style; ?>">

    <!-- About card wrapper with dynamic Glassmorphism class -->
    <div class="fas-card" style="border-top: 4px solid #0066cc !important; padding: 48px 32px; text-align: center;">
        
        <!-- Logo Emblem -->
        <div style="width: 80px; height: 80px; border-radius: 20px; background: #0066cc; color: #ffffff; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; box-shadow: 0 10px 20px rgba(0,102,204,0.25);">
            <span class="dashicons dashicons-search" style="font-size: 40px; width: 40px; height: 40px; line-height: 1.1;"></span>
        </div>

        <h2 style="margin: 0; font-size: 26px; font-weight: 800; color: #0f172a;"><?php echo esc_html( $i18n['title'] ); ?></h2>
        <span style="display: inline-block; background: rgba(0,0,0,0.05); color: #475569; font-weight: 700; font-size: 11px; padding: 3px 12px; border-radius: 12px; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo esc_html( $i18n['badge'] ); ?></span>

        <p style="font-size: 15px; color: #475569; line-height: 1.6; max-width: 600px; margin: 24px auto 32px auto;">
            <?php echo esc_html( $i18n['desc'] ); ?>
        </p>

        <!-- Dynamic Grid of Highlight Features -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>; margin-bottom: 40px;">
            <div style="background: rgba(255,255,255,0.45); border: 1px solid rgba(255,255,255,0.4); border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                <span class="dashicons dashicons-rest-api" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php echo esc_html( $i18n['f1_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php echo esc_html( $i18n['f1_desc'] ); ?></p>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.45); border: 1px solid rgba(255,255,255,0.4); border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                <span class="dashicons dashicons-translation" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php echo esc_html( $i18n['f2_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php echo esc_html( $i18n['f2_desc'] ); ?></p>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.45); border: 1px solid rgba(255,255,255,0.4); border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                <span class="dashicons dashicons-database" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php echo esc_html( $i18n['f3_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php echo esc_html( $i18n['f3_desc'] ); ?></p>
                </div>
            </div>

            <div style="background: rgba(255,255,255,0.45); border: 1px solid rgba(255,255,255,0.4); border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                <span class="dashicons dashicons-universal-access" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php echo esc_html( $i18n['f4_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php echo esc_html( $i18n['f4_desc'] ); ?></p>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 24px;">
            <p style="margin: 0; font-size: 12px; color: #94a3b8; font-weight: 600;">
                <?php echo esc_html( $i18n['dev_by'] ); ?>
                <span style="color: #0066cc; font-weight: 700; margin: 0 4px;"><?php echo esc_html( $i18n['developer'] ); ?></span>
            </p>
        </div>

    </div>

</div>
