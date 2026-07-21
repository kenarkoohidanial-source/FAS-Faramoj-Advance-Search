<?php
/**
 * HTML layout for the Faramoj Advanced Search Statistics submenu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Determine Admin display locale
$admin_locale = $this->get_admin_display_locale();
$is_rtl = ( 'fa' === $admin_locale );
$dir_style = $is_rtl ? 'direction: rtl; text-align: right;' : 'direction: ltr; text-align: left;';

$i18n = array(
    'notice_cleared' => $is_rtl ? 'تمامی آمار و ارقام جستجوها با موفقیت پاکسازی شد.' : 'Search statistics have been successfully cleared.',
    'title' => $is_rtl ? 'آمار و آنالیز جستجوی کاربران' : 'Search Analytics & Statistics',
    'desc' => $is_rtl ? 'بینش و تحلیل دقیق و زمان‌واقعی (Real-time) از کلمات کلیدی، محصولات و کدهایی که کاربران شما به دنبال آن‌ها هستند.' : 'Real-time insight into what technical items and articles your users are searching for.',
    'clear_btn' => $is_rtl ? 'پاکسازی کامل تمامی آمارها' : 'Clear All Statistics',
    'confirm_clear' => $is_rtl ? 'آیا مطمئن هستید که می‌خواهید تمامی آمارهای جستجو را پاک کرده و شمارنده‌ها را صفر کنید؟' : 'Are you sure you want to delete all search logs and reset counters?',
    'total_queries' => $is_rtl ? 'مجموع عبارات ردیابی شده' : 'Total Queries Tracked',
    'popular_keywords' => $is_rtl ? 'محبوب‌ترین و بیشترین کلمات کلیدی جستجو شده' : 'Most Popular Search Keywords',
    'no_data' => $is_rtl ? 'هنوز هیچ آمار جستجویی در سیستم ثبت نشده است.' : 'No search query data logged yet.',
    'col_rank' => $is_rtl ? 'رتبه' : 'Rank',
    'col_term' => $is_rtl ? 'کلمه کلیدی جستجو' : 'Search Keyword',
    'col_count' => $is_rtl ? 'تعداد دفعات جستجو' : 'Search Count',
);

// Handle statistics clear request
if ( isset( $_POST['fas_clear_stats'] ) && check_admin_referer( 'fas_clear_stats_nonce', 'fas_stats_nonce' ) ) {
    update_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [] ) );
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $i18n['notice_cleared'] ) . '</p></div>';
}

$stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [] ) );
$total_count = isset( $stats['total_count'] ) ? intval( $stats['total_count'] ) : 0;
$terms = isset( $stats['terms'] ) && is_array( $stats['terms'] ) ? $stats['terms'] : array();

// Sort popular terms
arsort( $terms );
?>
<div class="wrap fas-admin-wrap" style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; <?php echo $dir_style; ?>">

    <!-- Header -->
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
        <div style="text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 800; color: #0066cc; display: flex; align-items: center; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                <span class="dashicons dashicons-chart-bar" style="font-size: 24px; width: 24px; height: 24px;"></span>
                <span><?php echo esc_html( $i18n['title'] ); ?></span>
            </h2>
            <p style="margin: 6px 0 0 0; font-size: 13px; color: #64748b;"><?php echo esc_html( $i18n['desc'] ); ?></p>
        </div>

        <?php if ( ! empty( $terms ) || $total_count > 0 ) : ?>
            <form method="post" action="">
                <?php wp_nonce_field( 'fas_clear_stats_nonce', 'fas_stats_nonce' ); ?>
                <button type="submit" name="fas_clear_stats" class="button button-secondary" style="border-radius: 6px; font-weight: 600; padding: 8px 16px; height: auto; border: 1px solid #e11d48; color: #e11d48;" onclick="return confirm('<?php echo esc_js( $i18n['confirm_clear'] ); ?>');">
                    <?php echo esc_html( $i18n['clear_btn'] ); ?>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Cards Row -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 30px;">

        <!-- Total Queries Card -->
        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 250px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(0,102,204,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <span class="dashicons dashicons-search" style="font-size: 32px; width: 32px; height: 32px; color: #0066cc;"></span>
            </div>
            <span style="font-size: 14px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo esc_html( $i18n['total_queries'] ); ?></span>
            <h3 style="margin: 10px 0 0 0; font-size: 48px; font-weight: 900; color: #0f172a; line-height: 1;"><?php echo esc_html( number_format_i18n( $total_count ) ); ?></h3>
        </div>

        <!-- Popular Queries Card -->
        <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
            <h3 style="margin: 0 0 16px 0; font-size: 15px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; display: flex; align-items: center; gap: 8px; flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                <span class="dashicons dashicons-awards" style="color: #0066cc;"></span>
                <span><?php echo esc_html( $i18n['popular_keywords'] ); ?></span>
            </h3>

            <?php if ( empty( $terms ) ) : ?>
                <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                    <span class="dashicons dashicons-database" style="font-size: 48px; width: 48px; height: 48px; color: #94a3b8; margin-bottom: 12px;"></span>
                    <p style="margin: 0; font-size: 14px; font-weight: 500;"><?php echo esc_html( $i18n['no_data'] ); ?></p>
                </div>
            <?php else : ?>
                <div style="max-height: 350px; overflow-y: auto; padding-right: 10px;">
                    <table class="wp-list-table widefat fixed striped table-view-list" style="border: none; box-shadow: none;">
                        <thead>
                            <tr style="text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                                <th style="font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;"><?php echo esc_html( $i18n['col_rank'] ); ?></th>
                                <th style="font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;"><?php echo esc_html( $i18n['col_term'] ); ?></th>
                                <th style="font-weight: 700; color: #475569; border-bottom: 2px solid #e2e8f0; text-align: <?php echo $is_rtl ? 'left' : 'right'; ?>;"><?php echo esc_html( $i18n['col_count'] ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $rank = 1;
                            foreach ( $terms as $term => $count ) :
                            ?>
                                <tr>
                                    <td style="font-weight: 700; color: #0f172a; width: 80px;">
                                        <?php if ( $rank === 1 ) : ?>
                                            <span style="background: #f59e0b; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 11px;">1st</span>
                                        <?php elseif ( $rank === 2 ) : ?>
                                            <span style="background: #94a3b8; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 11px;">2nd</span>
                                        <?php elseif ( $rank === 3 ) : ?>
                                            <span style="background: #b45309; color: #ffffff; padding: 2px 8px; border-radius: 12px; font-size: 11px;">3rd</span>
                                        <?php else : ?>
                                            #<?php echo intval( $rank ); ?>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-weight: 600; color: #334155;">
                                        <code><?php echo esc_html( $term ); ?></code>
                                    </td>
                                    <td style="font-weight: 700; text-align: <?php echo $is_rtl ? 'left' : 'right'; ?>; color: #0066cc;">
                                        <?php echo esc_html( number_format_i18n( $count ) ); ?>
                                    </td>
                                </tr>
                            <?php
                                $rank++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>
