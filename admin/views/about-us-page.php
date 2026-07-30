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
    'badge' => $is_rtl ? 'نسخه ۱.۲.۶' : 'Version 1.2.6',
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
<style>
/* Scoped CSS for Modern About Us Page */
.fas-about-grid-bg {
    background-color: transparent;
    background-image: radial-gradient(rgba(0, 102, 204, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
    border-radius: 12px;
}

.fas-glass-logo {
    width: 80px;
    height: 80px;
    border-radius: 24px;
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 24px auto;
    box-shadow: 0 8px 32px rgba(0, 102, 204, 0.15), inset 0 0 0 1px rgba(255,255,255,0.5);
    position: relative;
    z-index: 2;
}

.fas-glass-logo::before {
    content: '';
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    width: 50px; height: 50px;
    background: #0066cc;
    border-radius: 50%;
    filter: blur(20px);
    z-index: -1;
    opacity: 0.4;
}

.fas-feature-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;
    box-shadow: 0 10px 40px rgba(0,0,0,0.04);
    transition: all 0.3s ease;
}

.fas-feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,102,204,0.08);
    border-color: rgba(0,102,204,0.2);
}

.fas-feature-icon {
    color: #0066cc;
    font-size: 28px;
    width: 28px;
    height: 28px;
    margin-top: 2px;
    transition: all 0.3s ease;
}

.fas-feature-card:hover .fas-feature-icon {
    transform: scale(1.1);
    color: #0284c7;
}
</style>

<div class="wrap fas-admin-wrap" style="max-width: 850px; margin: 40px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; text-align: center; <?php echo $dir_style; ?>">

    <!-- About card wrapper with dynamic Glassmorphism class -->
    <div class="fas-card fas-about-grid-bg" style="border-top: 4px solid #0066cc !important; padding: 48px 32px; text-align: center; background-color: #f8fafc; border: 1px solid #e2e8f0;">
        
        <!-- Modern Glassmorphism Logo Emblem -->
        <div class="fas-glass-logo">
            <span class="dashicons dashicons-search" style="font-size: 36px; width: 36px; height: 36px; color: #0066cc; line-height: 1;"></span>
        </div>

        <h2 style="margin: 0; font-size: 28px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;"><?php echo esc_html( $i18n['title'] ); ?></h2>

        <!-- Modern Version Badge -->
        <span style="display: inline-block; background: rgba(0, 102, 204, 0.1); color: #0066cc; font-weight: 700; font-size: 12px; padding: 6px 16px; border-radius: 20px; margin-top: 12px; text-transform: uppercase; letter-spacing: 0.5px; border: 1px solid rgba(0, 102, 204, 0.2);">
            <?php echo esc_html( $i18n['badge'] ); ?>
        </span>

        <p style="font-size: 16px; color: #475569; line-height: 1.6; max-width: 650px; margin: 24px auto 40px auto;">
            <?php echo esc_html( $i18n['desc'] ); ?>
        </p>

        <!-- Dynamic Grid of Highlight Features -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>; margin-bottom: 40px;">

            <div class="fas-feature-card">
                <span class="dashicons dashicons-rest-api fas-feature-icon"></span>
                <div>
                    <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 700; color: #0f172a;"><?php echo esc_html( $i18n['f1_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.5;"><?php echo esc_html( $i18n['f1_desc'] ); ?></p>
                </div>
            </div>

            <div class="fas-feature-card">
                <span class="dashicons dashicons-translation fas-feature-icon"></span>
                <div>
                    <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 700; color: #0f172a;"><?php echo esc_html( $i18n['f2_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.5;"><?php echo esc_html( $i18n['f2_desc'] ); ?></p>
                </div>
            </div>

            <div class="fas-feature-card">
                <span class="dashicons dashicons-database fas-feature-icon"></span>
                <div>
                    <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 700; color: #0f172a;"><?php echo esc_html( $i18n['f3_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.5;"><?php echo esc_html( $i18n['f3_desc'] ); ?></p>
                </div>
            </div>

            <div class="fas-feature-card">
                <span class="dashicons dashicons-universal-access fas-feature-icon"></span>
                <div>
                    <h4 style="margin: 0 0 6px 0; font-size: 15px; font-weight: 700; color: #0f172a;"><?php echo esc_html( $i18n['f4_title'] ); ?></h4>
                    <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.5;"><?php echo esc_html( $i18n['f4_desc'] ); ?></p>
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
