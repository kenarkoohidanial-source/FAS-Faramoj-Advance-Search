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
    'popular_keywords' => $is_rtl ? '📊 محبوب‌ترین و بیشترین کلمات کلیدی جستجو شده' : 'Most Popular Search Keywords 📊',
    'no_data' => $is_rtl ? 'هنوز هیچ آمار جستجویی در سیستم ثبت نشده است.' : 'No search query data logged yet.',
    'col_rank' => $is_rtl ? 'رتبه' : 'Rank',
    'col_term' => $is_rtl ? 'کلمه کلیدی جستجو' : 'Search Keyword',
    'col_count' => $is_rtl ? 'تعداد دفعات جستجو' : 'Search Count',
);

// Handle statistics clear request
if ( isset( $_POST['fas_clear_stats'] ) && check_admin_referer( 'fas_clear_stats_nonce', 'fas_stats_nonce' ) ) {
    update_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [], 'clicks' => [] ) );
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $i18n['notice_cleared'] ) . '</p></div>';
}

$stats = get_option( 'fas_search_stats', array( 'total_count' => 0, 'terms' => [], 'clicks' => [] ) );
$total_count = isset( $stats['total_count'] ) ? intval( $stats['total_count'] ) : 0;
$terms = isset( $stats['terms'] ) && is_array( $stats['terms'] ) ? $stats['terms'] : array();
$clicks = isset( $stats['clicks'] ) && is_array( $stats['clicks'] ) ? $stats['clicks'] : array();

// Separate terms by language (Simple check for Persian/Arabic characters)
$persian_terms = array();
$english_terms = array();

foreach ( $terms as $term => $data ) {
    if ( preg_match('/[\x{0600}-\x{06FF}]/u', $term) ) {
        $persian_terms[$term] = $data;
    } else {
        $english_terms[$term] = $data;
    }
}

// Ensure sorting by count
$sort_by_count = function($a, $b) {
    $count_a = is_array($a) ? $a['count'] : $a;
    $count_b = is_array($b) ? $b['count'] : $b;
    return $count_b <=> $count_a;
};
uasort( $persian_terms, $sort_by_count );
uasort( $english_terms, $sort_by_count );

// Analytics: Calculate trending keywords (Unique IPs in the last 24h)
$recent_trends = array();
$twenty_four_hours_ago = strtotime('-24 hours');

foreach ( $terms as $term => $data ) {
    if ( is_array($data) && isset($data['logs']) ) {
        $unique_ips = array();
        foreach ( $data['logs'] as $log ) {
            if ( strtotime($log['time']) >= $twenty_four_hours_ago ) {
                $unique_ips[] = $log['ip'];
            }
        }
        $unique_count = count( array_unique( $unique_ips ) );
        if ( $unique_count > 0 ) {
            $recent_trends[$term] = $unique_count;
        }
    }
}
arsort( $recent_trends );
$recent_trends = array_slice( $recent_trends, 0, 5, true );

?>
<div class="wrap fas-admin-wrap" style="max-width: 1200px; margin: 20px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; <?php echo $dir_style; ?>">
    <style>
        .fas-tab-nav { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid #cbd5e1; padding-bottom: 10px; }
        .fas-tab-nav button { background: transparent; border: none; font-size: 16px; font-weight: 700; color: #64748b; padding: 8px 16px; cursor: pointer; transition: all 0.2s; border-radius: 6px; }
        .fas-tab-nav button:hover { background: #f1f5f9; color: #0f172a; }
        .fas-tab-nav button.active { background: #0066cc; color: white; }
        .fas-term-row { cursor: pointer; transition: background 0.2s; }
        .fas-term-row:hover { background: #f8fafc; }
        .fas-logs-panel { display: none; padding: 16px; background: #f8fafc; border-top: 1px dashed #cbd5e1; font-size: 13px; color: #475569; }
        .fas-log-item { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e2e8f0; }
        .fas-log-item:last-child { border-bottom: none; }
        .fas-trend-link { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: #10b981; text-decoration: none; font-weight: 600; padding: 4px 8px; border: 1px solid #10b981; border-radius: 4px; margin-top: 8px; transition: all 0.2s; }
        .fas-trend-link:hover { background: #10b981; color: white; }
    </style>
    
    <!-- Header -->
    <div class="fas-top-bar" style="padding: 24px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div style="text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
            <h2 style="margin: 0; font-size: 22px; font-weight: 800; color: #0066cc; display: flex; align-items: center; gap: 8px;">
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
        
        <div style="display: flex; flex-direction: column; gap: 30px;">
            <!-- Total Queries Card -->
            <div class="fas-card" style="padding: 24px; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 200px;">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(0,102,204,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                    <span class="dashicons dashicons-search" style="font-size: 32px; width: 32px; height: 32px; color: #0066cc;"></span>
                </div>
                <span style="font-size: 14px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo esc_html( $i18n['total_queries'] ); ?></span>
                <h3 style="margin: 10px 0 0 0; font-size: 48px; font-weight: 900; color: #0f172a; line-height: 1; border: none; padding: 0; background: transparent;"><?php echo esc_html( number_format_i18n( $total_count ) ); ?></h3>
            </div>

            <!-- Content Ideas / Trend Analytics Card -->
            <div class="fas-card" style="padding: 24px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255, 255, 255, 0.4); padding-bottom: 12px; margin-bottom: 16px;">
                    <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-lightbulb" style="color: #f59e0b;"></span>
                        <span><?php echo $is_rtl ? 'تحلیل و پیشنهادات تولید محتوا' : 'Content Ideas & Analytics'; ?></span>
                    </h3>
                </div>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">
                    <?php echo $is_rtl ? 'واژگان زیر در ۲۴ ساعت گذشته توسط بیشترین افراد (آی‌پی‌های یکتا) جستجو شده‌اند. نوشتن مقاله درباره این کلمات به شدت توصیه می‌شود:' : 'These keywords were searched by the most unique individuals (unique IPs) in the last 24 hours. Writing articles about them is highly recommended:'; ?>
                </p>
                <?php if ( empty($recent_trends) ) : ?>
                    <div style="padding: 16px; background: #f8fafc; border-radius: 8px; text-align: center; color: #94a3b8; font-size: 13px;">
                        <?php echo $is_rtl ? 'در ۲۴ ساعت گذشته جستجوی یکتایی ثبت نشده است.' : 'No unique searches recorded in the last 24 hours.'; ?>
                    </div>
                <?php else: ?>
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        <?php foreach ( $recent_trends as $trend_term => $ip_count ) : ?>
                            <li style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: #f1f5f9; margin-bottom: 8px; border-radius: 6px;">
                                <strong style="color: #0f172a;"><?php echo esc_html($trend_term); ?></strong>
                                <span style="font-size: 12px; font-weight: 600; color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 4px 8px; border-radius: 12px;">
                                    <?php echo sprintf( $is_rtl ? '%s کاربر یکتا' : '%s unique users', number_format_i18n($ip_count) ); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <!-- Popular Queries Card with Glassmorphism class -->
        <div class="fas-card" style="padding: 24px; text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255, 255, 255, 0.4); padding-bottom: 12px; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 15px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <span><?php echo esc_html( $i18n['popular_keywords'] ); ?></span>
                </h3>
            </div>

            <!-- Charts Container -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div style="background: rgba(255,255,255,0.5); padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin-top:0; color: #475569; text-align: center;"><?php echo $is_rtl ? 'بیشترین عبارات جستجو شده' : 'Most Searched Terms'; ?></h4>
                    <canvas id="searchTermsChart" style="max-height: 250px;"></canvas>
                </div>
                <div style="background: rgba(255,255,255,0.5); padding: 16px; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <h4 style="margin-top:0; color: #475569; text-align: center;"><?php echo $is_rtl ? 'بیشترین نتایج کلیک شده' : 'Most Clicked Results'; ?></h4>
                    <canvas id="clickedResultsChart" style="max-height: 250px;"></canvas>
                </div>
            </div>

            <?php if ( empty( $terms ) ) : ?>
                <div style="text-align: center; padding: 40px 20px; color: #64748b;">
                    <span class="dashicons dashicons-database" style="font-size: 48px; width: 48px; height: 48px; color: #94a3b8; margin-bottom: 12px;"></span>
                    <p style="margin: 0; font-size: 14px; font-weight: 500;"><?php echo esc_html( $i18n['no_data'] ); ?></p>
                </div>
            <?php else : ?>
                <div class="fas-tab-nav" style="flex-direction: <?php echo $is_rtl ? 'row-reverse' : 'row'; ?>;">
                    <button type="button" class="active" data-target="fas-persian-terms"><?php echo $is_rtl ? 'عبارات فارسی / عربی' : 'Persian / Arabic'; ?></button>
                    <button type="button" data-target="fas-english-terms"><?php echo $is_rtl ? 'عبارات انگلیسی' : 'English'; ?></button>
                </div>

                <div class="fas-stats-tab-content active" id="fas-persian-terms">
                    <?php fas_render_stats_table_helper( $persian_terms, $i18n, $is_rtl ); ?>
                </div>

                <div class="fas-stats-tab-content" id="fas-english-terms" style="display: none;">
                    <?php fas_render_stats_table_helper( $english_terms, $i18n, $is_rtl ); ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

</div>

<script>
jQuery(document).ready(function($) {
    // Tab switching logic
    $('.fas-tab-nav button').on('click', function() {
        $('.fas-tab-nav button').removeClass('active');
        $(this).addClass('active');
        
        $('.fas-stats-tab-content').hide();
        $('#' + $(this).data('target')).fadeIn(200);
    });

    // Toggle logs panel
    $('.fas-term-row').on('click', function() {
        $(this).next('.fas-logs-panel-tr').find('.fas-logs-panel').slideToggle(200);
    });
});
</script>

<?php
// Prepare data for Chart.js
$chart_terms_labels = [];
$chart_terms_data = [];
$term_counter = 0;
foreach ( $persian_terms + $english_terms as $term => $data ) {
    if ( $term_counter >= 10 ) break; // Top 10 for chart
    $chart_terms_labels[] = $term;
    $chart_terms_data[] = is_array($data) ? $data['count'] : $data;
    $term_counter++;
}

$chart_clicks_labels = [];
$chart_clicks_data = [];
$click_counter = 0;
foreach ( $clicks as $post_id => $data ) {
    if ( $click_counter >= 10 ) break;
    $chart_clicks_labels[] = mb_strimwidth( $data['title'], 0, 30, '...' );
    $chart_clicks_data[] = $data['count'];
    $click_counter++;
}
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        // Search Terms Chart
        const termsCtx = document.getElementById('searchTermsChart');
        if (termsCtx) {
            new Chart(termsCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chart_terms_labels); ?>,
                    datasets: [{
                        label: '<?php echo $is_rtl ? 'تعداد جستجو' : 'Search Count'; ?>',
                        data: <?php echo json_encode($chart_terms_data); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        // Clicked Results Chart
        const clicksCtx = document.getElementById('clickedResultsChart');
        if (clicksCtx) {
            new Chart(clicksCtx, {
                type: 'doughnut',
                data: {
                    labels: <?php echo json_encode($chart_clicks_labels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($chart_clicks_data); ?>,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(153, 102, 255, 0.6)',
                            'rgba(255, 159, 64, 0.6)',
                            'rgba(199, 199, 199, 0.6)',
                            'rgba(83, 102, 255, 0.6)',
                            'rgba(255, 99, 255, 0.6)',
                            'rgba(99, 255, 132, 0.6)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
    }
});
</script>

<?php
// Define helper function to render tables to keep code DRY
// Notice: In real object-oriented design this should be a method, but defining it inline here for context ease.
function fas_render_stats_table_helper( $terms, $i18n, $is_rtl ) {
    if ( empty( $terms ) ) {
        echo '<p style="color: #64748b; font-weight: 500;">' . ( $is_rtl ? 'داده‌ای برای این زبان یافت نشد.' : 'No data found for this language.' ) . '</p>';
        return;
    }
    ?>
    <div style="max-height: 450px; overflow-y: auto; padding-right: 10px;">
        <table class="wp-list-table widefat fixed striped table-view-list" style="border: none; box-shadow: none; background: transparent;">
            <thead>
                <tr style="text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>;">
                    <th style="font-weight: 700; color: #475569; border-bottom: 2px solid rgba(255, 255, 255, 0.4); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>; background: transparent; width: 60px;"><?php echo esc_html( $i18n['col_rank'] ); ?></th>
                    <th style="font-weight: 700; color: #475569; border-bottom: 2px solid rgba(255, 255, 255, 0.4); text-align: <?php echo $is_rtl ? 'right' : 'left'; ?>; background: transparent;"><?php echo esc_html( $i18n['col_term'] ); ?></th>
                    <th style="font-weight: 700; color: #475569; border-bottom: 2px solid rgba(255, 255, 255, 0.4); text-align: <?php echo $is_rtl ? 'left' : 'right'; ?>; background: transparent; width: 100px;"><?php echo esc_html( $i18n['col_count'] ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $rank = 1;
                foreach ( $terms as $term => $data ) : 
                    $count = is_array($data) ? $data['count'] : $data;
                    $logs = is_array($data) && isset($data['logs']) ? $data['logs'] : array();
                    $trend_url = 'https://trends.google.com/trends/explore?q=' . urlencode($term);
                ?>
                    <tr class="fas-term-row" style="background: transparent;">
                        <td style="font-weight: 700; color: #0f172a;">
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
                            <br>
                            <a href="<?php echo esc_url($trend_url); ?>" target="_blank" class="fas-trend-link">
                                <span class="dashicons dashicons-external" style="font-size: 12px; width: 12px; height: 12px;"></span> Google Trends
                            </a>
                        </td>
                        <td style="font-weight: 700; text-align: <?php echo $is_rtl ? 'left' : 'right'; ?>; color: #0066cc;">
                            <?php echo esc_html( number_format_i18n( $count ) ); ?>
                            <span class="dashicons dashicons-arrow-down-alt2" style="font-size: 14px; width: 14px; height: 14px; color: #94a3b8; margin-top: 4px;"></span>
                        </td>
                    </tr>
                    <tr class="fas-logs-panel-tr" style="background: transparent;">
                        <td colspan="3" style="padding: 0;">
                            <div class="fas-logs-panel">
                                <?php if ( empty($logs) ) : ?>
                                    <p style="margin: 0; font-style: italic;"><?php echo $is_rtl ? 'هیچ لاگ آی‌پی جدیدی ثبت نشده است.' : 'No recent IP logs found.'; ?></p>
                                <?php else : ?>
                                    <div style="font-weight: 600; margin-bottom: 8px; color: #0f172a;"><?php echo $is_rtl ? 'آخرین کاربرانی که جستجو کردند:' : 'Recent users who searched this:'; ?></div>
                                    <?php foreach ( $logs as $log ) : ?>
                                        <div class="fas-log-item">
                                            <span style="font-family: monospace; color: #0066cc;"><?php echo esc_html( $log['ip'] ); ?></span>
                                            <span style="color: #64748b;"><?php echo esc_html( $log['time'] ); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php 
                    $rank++;
                endforeach; 
                ?>
            </tbody>
        </table>
    </div>
    <?php
}
