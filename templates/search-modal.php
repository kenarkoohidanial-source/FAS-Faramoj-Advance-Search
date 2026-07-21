<?php
/**
 * Search Modal Overlay Template
 * Overridable in child themes via 'faramoj-advanced-search/search-modal.php'
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$theme_mode = get_option( 'fas_theme_mode', 'dark' );
$overlay_class = ( 'light' === $theme_mode ) ? 'fas-theme-light' : 'fas-theme-dark';
?>
<div class="fas-search-overlay <?php echo esc_attr( $overlay_class ); ?>">
    <div class="fas-search-container">
        <!-- Input Wrapper -->
        <div class="fas-search-input-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" class="fas-search-input" placeholder="<?php esc_attr_e( 'Search products, articles, docs...', 'faramoj-search' ); ?>" aria-label="<?php esc_attr_e( 'Live Search', 'faramoj-search' ); ?>">
            <button class="fas-modal-close" aria-label="<?php esc_attr_e( 'Close Search', 'faramoj-search' ); ?>">&times;</button>
        </div>

        <!-- Category Tabs -->
        <div class="fas-search-tabs">
            <button class="fas-tab-btn is-active" data-tab="products"><?php esc_html_e( 'Products', 'faramoj-search' ); ?></button>
            <button class="fas-tab-btn" data-tab="posts"><?php esc_html_e( 'News & Articles', 'faramoj-search' ); ?></button>
            <button class="fas-tab-btn" data-tab="docs"><?php esc_html_e( 'Documentation', 'faramoj-search' ); ?></button>
        </div>

        <!-- Results Panel -->
        <div class="fas-results-panel">
            <div id="fas-tab-products" class="fas-tab-content is-active"></div>
            <div id="fas-tab-posts" class="fas-tab-content"></div>
            <div id="fas-tab-docs" class="fas-tab-content"></div>
        </div>
    </div>
</div>
