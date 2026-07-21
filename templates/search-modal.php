<?php
/**
 * Search Modal Overlay Template
 * Overridable in child themes via 'faramoj-advanced-search/search-modal.php'
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$suffix = FAS_Core::get_lang_suffix();

$theme_mode    = get_option( 'fas_theme_mode' . $suffix, 'dark' );
$overlay_class = ( 'light' === $theme_mode ) ? 'fas-theme-light' : 'fas-theme-dark';

$tabs_order     = get_option( 'fas_tabs_order' . $suffix, 'all,products,posts,docs' );
$tabs_order_arr = array_map( 'trim', explode( ',', $tabs_order ) );

// Fetch individual tab titles, colors, and icons
$tab_details = array(
    'all' => array(
        'title' => get_option( 'fas_tab_all_title' . $suffix, 'All Results' ),
        'color' => get_option( 'fas_tab_all_color' . $suffix, '#0066cc' ),
        'icon'  => get_option( 'fas_tab_all_icon' . $suffix, 'dashicons-grid-view' ),
    ),
    'products' => array(
        'title' => get_option( 'fas_tab_products_title' . $suffix, 'Products' ),
        'color' => get_option( 'fas_tab_products_color' . $suffix, '#10b981' ),
        'icon'  => get_option( 'fas_tab_products_icon' . $suffix, 'dashicons-cart' ),
    ),
    'posts' => array(
        'title' => get_option( 'fas_tab_posts_title' . $suffix, 'News & Articles' ),
        'color' => get_option( 'fas_tab_posts_color' . $suffix, '#f59e0b' ),
        'icon'  => get_option( 'fas_tab_posts_icon' . $suffix, 'dashicons-welcome-write-blog' ),
    ),
    'docs' => array(
        'title' => get_option( 'fas_tab_docs_title' . $suffix, 'Documentation' ),
        'color' => get_option( 'fas_tab_docs_color' . $suffix, '#6366f1' ),
        'icon'  => get_option( 'fas_tab_docs_icon' . $suffix, 'dashicons-book-alt' ),
    ),
);
?>
<div class="fas-search-overlay <?php echo esc_attr( $overlay_class ); ?>">
    <div class="fas-search-container">
        <!-- Input Wrapper -->
        <div class="fas-search-input-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" class="fas-search-input" placeholder="<?php esc_attr_e( 'Search products, articles, docs...', 'faramoj-search' ); ?>" aria-label="<?php esc_attr_e( 'Live Search', 'faramoj-search' ); ?>">
            <button class="fas-modal-close" aria-label="<?php esc_attr_e( 'Close Search', 'faramoj-search' ); ?>">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Category Tabs (Dynamically Ordered & Styled) -->
        <div class="fas-search-tabs">
            <?php
            $first = true;
            foreach ( $tabs_order_arr as $tab_key ) :
                if ( ! isset( $tab_details[ $tab_key ] ) ) {
                    continue;
                }
                $tab  = $tab_details[ $tab_key ];
                $active_class = $first ? 'is-active' : '';
                $first = false;
                ?>
                <button class="fas-tab-btn <?php echo esc_attr( $active_class ); ?>"
                        data-tab="<?php echo esc_attr( $tab_key ); ?>"
                        data-accent-color="<?php echo esc_attr( $tab['color'] ); ?>"
                        style="--tab-accent: <?php echo esc_attr( $tab['color'] ); ?>;">
                    <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
                    <span><?php echo esc_html( $tab['title'] ); ?></span>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Results Panel -->
        <div class="fas-results-panel">
            <?php
            $first = true;
            foreach ( $tabs_order_arr as $tab_key ) :
                $active_class = $first ? 'is-active' : '';
                $first = false;
                ?>
                <div id="fas-tab-<?php echo esc_attr( $tab_key ); ?>" class="fas-tab-content <?php echo esc_attr( $active_class ); ?>"></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
