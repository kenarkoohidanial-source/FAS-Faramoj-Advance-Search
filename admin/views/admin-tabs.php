<?php
/**
 * Shared Admin Tabs Navigation for Faramoj Advanced Search
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : 'faramoj-search';
$is_rtl = ( $this->get_admin_display_locale() === 'fa' );

$tabs = array(
    'faramoj-search' => array(
        'title' => $is_rtl ? 'تنظیمات جستجو' : 'Faramoj Search',
        'icon'  => 'dashicons-search'
    ),
    'fas-statistics' => array(
        'title' => $is_rtl ? 'آمار جستجوها' : 'Statistics',
        'icon'  => 'dashicons-chart-bar'
    ),
    'fas-tools' => array(
        'title' => $is_rtl ? 'تنظیمات و ابزارها' : 'Settings & Tools',
        'icon'  => 'dashicons-admin-tools'
    ),
    'fas-about-us' => array(
        'title' => $is_rtl ? 'درباره ما' : 'About Us',
        'icon'  => 'dashicons-info'
    )
);
?>
<div class="nav-tab-wrapper fas-nav-tabs" style="margin-bottom: 25px; border-bottom: 1px solid #cbd5e1; padding-bottom: 0; display: flex; gap: 8px;">
    <?php foreach ( $tabs as $page_slug => $tab_data ) : ?>
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $page_slug ) ); ?>" 
           class="nav-tab <?php echo ( $current_page === $page_slug ) ? 'nav-tab-active' : ''; ?>"
           style="display: flex; align-items: center; gap: 6px; font-weight: <?php echo ( $current_page === $page_slug ) ? '700' : '600'; ?>; color: <?php echo ( $current_page === $page_slug ) ? '#0066cc' : '#475569'; ?>; border: none; background: <?php echo ( $current_page === $page_slug ) ? '#ffffff' : 'transparent'; ?>; border-bottom: <?php echo ( $current_page === $page_slug ) ? '3px solid #0066cc' : '3px solid transparent'; ?>; padding: 10px 16px; transition: all 0.2s; font-size: 14px; text-decoration: none;">
            <span class="dashicons <?php echo esc_attr( $tab_data['icon'] ); ?>" style="font-size: 18px; width: 18px; height: 18px;"></span>
            <?php echo esc_html( $tab_data['title'] ); ?>
        </a>
    <?php endforeach; ?>
</div>

<style>
    .fas-nav-tabs .nav-tab:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .fas-nav-tabs .nav-tab-active:hover {
        background: #ffffff;
    }
</style>
