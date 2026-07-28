<?php
/**
 * HTML layout for the Tools & Settings Import/Export page
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Determine Admin display locale
$admin_locale = $this->get_admin_display_locale();
$is_rtl = ( 'fa' === $admin_locale );
$dir_style = $is_rtl ? 'direction: rtl; text-align: right;' : 'direction: ltr; text-align: left;';

$i18n = array(
    'title' => $is_rtl ? 'تنظیمات و ابزارها' : 'Settings & Tools',
    'desc' => $is_rtl ? 'مدیریت درون‌ریزی/برون‌بری تنظیمات افزونه و کدهای کوتاه (Shortcodes).' : 'Manage plugin settings import/export and view shortcodes.',
    'shortcode_title' => $is_rtl ? 'کد کوتاه دکمه جستجو' : 'Search Trigger Shortcode',
    'shortcode_desc' => $is_rtl ? 'برای نمایش دکمه باز کردن پاپ‌آپ جستجو در هر کجای سایت (مانند برگه‌ها، نوشته‌ها یا ابزارک‌ها)، از کد کوتاه زیر استفاده کنید:' : 'Use the following shortcode to display the search trigger button anywhere on your site:',
    'copy_btn' => $is_rtl ? 'کپی کردن' : 'Copy',
    'copied' => $is_rtl ? 'کپی شد!' : 'Copied!',
    'export_title' => $is_rtl ? 'برون‌بری تنظیمات (Export)' : 'Export Settings',
    'export_desc' => $is_rtl ? 'کد زیر حاوی تمامی تنظیمات ذخیره شده افزونه برای تمام زبان‌هاست. آن را کپی کرده و برای انتقال به سایت دیگر ذخیره کنید.' : 'The code below contains all saved plugin settings across all languages. Copy and save it to migrate to another site.',
    'import_title' => $is_rtl ? 'درون‌ریزی تنظیمات (Import)' : 'Import Settings',
    'import_desc' => $is_rtl ? 'کد برون‌بری شده را در کادر زیر قرار داده و روی دکمه درون‌ریزی کلیک کنید. توجه: این کار تنظیمات فعلی شما را بازنویسی می‌کند.' : 'Paste the exported code into the box below and click import. Warning: This will overwrite your current settings.',
    'import_btn' => $is_rtl ? 'درون‌ریزی تنظیمات' : 'Import Settings',
    'import_success' => $is_rtl ? 'تنظیمات با موفقیت درون‌ریزی شدند.' : 'Settings imported successfully.',
    'import_error' => $is_rtl ? 'خطا در درون‌ریزی: داده‌های وارد شده نامعتبر است.' : 'Import error: Invalid data provided.',
);

// Collect all plugin options for Export
global $wpdb;
$plugin_options = array();
$results = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'fas_%' AND option_name NOT LIKE '_transient_%'" );
foreach ( $results as $row ) {
    $plugin_options[ $row->option_name ] = maybe_unserialize( $row->option_value );
}
$export_json = wp_json_encode( $plugin_options );

?>
<div class="wrap fas-admin-wrap" style="max-width: 1000px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; <?php echo $dir_style; ?>">
    <div class="fas-top-bar" style="padding: 24px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div style="text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 800; color: #0066cc; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-admin-tools" style="font-size: 24px; width: 24px; height: 24px;"></span>
                <span><?php echo esc_html( $i18n['title'] ); ?></span>
            </h2>
            <p style="margin: 6px 0 0 0; font-size: 13px; color: #64748b;"><?php echo esc_html( $i18n['desc'] ); ?></p>
        </div>
    </div>

    <?php
    // Handle Import POST
    if ( isset( $_POST['fas_import_data'] ) && check_admin_referer( 'fas_import_nonce', 'fas_import_nonce_field' ) ) {
        $import_json = stripslashes( $_POST['fas_import_data'] );
        $import_data = json_decode( $import_json, true );
        
        if ( is_array( $import_data ) && ! empty( $import_data ) ) {
            foreach ( $import_data as $key => $value ) {
                if ( strpos( $key, 'fas_' ) === 0 ) {
                    update_option( $key, $value );
                }
            }
            // Update the export json string to reflect new state
            $plugin_options = array();
            $results = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'fas_%' AND option_name NOT LIKE '_transient_%'" );
            foreach ( $results as $row ) {
                $plugin_options[ $row->option_name ] = maybe_unserialize( $row->option_value );
            }
            $export_json = wp_json_encode( $plugin_options );

            echo '<div class="notice notice-success is-dismissible" style="border-radius: 8px; border-color: #10b981; border-width: 0 4px 0 0;"><p><strong>' . esc_html( $i18n['import_success'] ) . '</strong></p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible" style="border-radius: 8px; border-color: #e11d48; border-width: 0 4px 0 0;"><p><strong>' . esc_html( $i18n['import_error'] ) . '</strong></p></div>';
        }
    }
    ?>

    <div style="display: flex; flex-direction: column; gap: 30px;">
        
        <!-- Shortcode Section -->
        <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
            <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                <span class="dashicons dashicons-shortcode" style="color: #0066cc;"></span>
                <span><?php echo esc_html( $i18n['shortcode_title'] ); ?></span>
            </h3>
            <p style="font-size: 14px; color: #475569; margin-bottom: 16px;">
                <?php echo esc_html( $i18n['shortcode_desc'] ); ?>
            </p>
            <div style="display: flex; align-items: center; gap: 12px; background: #f8fafc; padding: 12px 16px; border-radius: 8px; border: 1px dashed #cbd5e1; width: fit-content; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                <code id="fas_shortcode_text" style="font-size: 16px; color: #0066cc; background: transparent; padding: 0;">[fas_search_trigger]</code>
                <button type="button" id="fas_copy_shortcode" class="button" style="border-radius: 6px; font-weight: 600;">
                    <?php echo esc_html( $i18n['copy_btn'] ); ?>
                </button>
            </div>
            <p style="font-size: 12px; color: #64748b; margin-top: 12px;">
                <?php echo $is_rtl ? 'پارامترهای قابل استفاده:' : 'Available parameters:'; ?> <code>label="متن دلخواه"</code>, <code>class="کلاس-css-دلخواه"</code>
            </p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Export Section -->
            <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <span class="dashicons dashicons-download" style="color: #10b981;"></span>
                    <span><?php echo esc_html( $i18n['export_title'] ); ?></span>
                </h3>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px; min-height: 40px;">
                    <?php echo esc_html( $i18n['export_desc'] ); ?>
                </p>
                <textarea readonly id="fas_export_data" style="width: 100%; height: 200px; font-family: monospace; font-size: 12px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px; background: #f8fafc; color: #334155; resize: none;"><?php echo esc_textarea( $export_json ); ?></textarea>
                <button type="button" id="fas_copy_export" class="button button-secondary" style="margin-top: 12px; border-radius: 6px; font-weight: 600;">
                    <span class="dashicons dashicons-admin-page" style="margin-top: 3px;"></span> <?php echo esc_html( $i18n['copy_btn'] ); ?>
                </button>
            </div>

            <!-- Import Section -->
            <div class="fas-card" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                <h3 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <span class="dashicons dashicons-upload" style="color: #f59e0b;"></span>
                    <span><?php echo esc_html( $i18n['import_title'] ); ?></span>
                </h3>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px; min-height: 40px;">
                    <?php echo esc_html( $i18n['import_desc'] ); ?>
                </p>
                <form method="post" action="">
                    <?php wp_nonce_field( 'fas_import_nonce', 'fas_import_nonce_field' ); ?>
                    <textarea name="fas_import_data" required placeholder='{"fas_theme_mode_en":"dark", ...}' style="width: 100%; height: 200px; font-family: monospace; font-size: 12px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 12px; resize: none; direction: ltr; text-align: left;"></textarea>
                    <button type="submit" class="button button-primary" style="margin-top: 12px; border-radius: 6px; font-weight: 700; background: #0066cc; border-color: #0066cc;" onclick="return confirm('Are you sure? This will overwrite your current settings.');">
                        <?php echo esc_html( $i18n['import_btn'] ); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var copyShortcodeBtn = document.getElementById('fas_copy_shortcode');
    if (copyShortcodeBtn) {
        copyShortcodeBtn.addEventListener('click', function() {
            var text = document.getElementById('fas_shortcode_text').innerText;
            navigator.clipboard.writeText(text).then(function() {
                var originalText = copyShortcodeBtn.innerText;
                copyShortcodeBtn.innerText = '<?php echo esc_js( $i18n['copied'] ); ?>';
                setTimeout(function() { copyShortcodeBtn.innerText = originalText; }, 2000);
            });
        });
    }

    var copyExportBtn = document.getElementById('fas_copy_export');
    if (copyExportBtn) {
        copyExportBtn.addEventListener('click', function() {
            var textArea = document.getElementById('fas_export_data');
            textArea.select();
            document.execCommand('copy');
            var originalHtml = copyExportBtn.innerHTML;
            copyExportBtn.innerHTML = '<span class="dashicons dashicons-yes" style="margin-top: 3px; color: #10b981;"></span> <?php echo esc_js( $i18n['copied'] ); ?>';
            setTimeout(function() { copyExportBtn.innerHTML = originalHtml; }, 2000);
        });
    }
});
</script>
