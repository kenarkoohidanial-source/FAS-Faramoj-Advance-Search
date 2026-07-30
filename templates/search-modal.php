<?php
/**
 * Search Modal Overlay Template
 * Overridable in child themes via 'faramoj-advanced-search/search-modal.php'
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$suffix = FAS_Core::get_lang_suffix();

$theme_mode    = FAS_Core::get_option( 'fas_theme_mode', 'dark' );
$overlay_class = ( 'light' === $theme_mode ) ? 'fas-theme-light' : 'fas-theme-dark';

$enable_voice_search = FAS_Core::get_option( 'fas_enable_voice_search', 'yes' );

$tabs_order     = FAS_Core::get_option( 'fas_tabs_order', 'all,products,posts,docs' );
$tabs_order_arr = array_map( 'trim', explode( ',', $tabs_order ) );

// Fetch individual tab titles, colors, icons, and custom uploaded SVG/PNG icons via unified FAS_Core::get_option
$tab_details = array(
    'all' => array(
        'title'       => FAS_Core::get_option( 'fas_tab_all_title', 'All Results' ),
        'color'       => FAS_Core::get_option( 'fas_tab_all_color', '#0066cc' ),
        'icon'        => FAS_Core::get_option( 'fas_tab_all_icon', 'dashicons-grid-view' ),
        'custom_icon' => FAS_Core::get_option( 'fas_tab_all_custom_icon', '' ),
    ),
    'products' => array(
        'title'       => FAS_Core::get_option( 'fas_tab_products_title', 'Products' ),
        'color'       => FAS_Core::get_option( 'fas_tab_products_color', '#10b981' ),
        'icon'        => FAS_Core::get_option( 'fas_tab_products_icon', 'dashicons-cart' ),
        'custom_icon' => FAS_Core::get_option( 'fas_tab_products_custom_icon', '' ),
    ),
    'posts' => array(
        'title'       => FAS_Core::get_option( 'fas_tab_posts_title', 'News & Articles' ),
        'color'       => FAS_Core::get_option( 'fas_tab_posts_color', '#f59e0b' ),
        'icon'        => FAS_Core::get_option( 'fas_tab_posts_icon', 'dashicons-welcome-write-blog' ),
        'custom_icon' => FAS_Core::get_option( 'fas_tab_posts_custom_icon', '' ),
    ),
    'docs' => array(
        'title'       => FAS_Core::get_option( 'fas_tab_docs_title', 'Documentation' ),
        'color'       => FAS_Core::get_option( 'fas_tab_docs_color', '#6366f1' ),
        'icon'        => FAS_Core::get_option( 'fas_tab_docs_icon', 'dashicons-book-alt' ),
        'custom_icon' => FAS_Core::get_option( 'fas_tab_docs_custom_icon', '' ),
    ),
);

// Determine dynamic placeholder based on current suffix
$is_fa = ( '_fa' === $suffix );
$placeholder_text = $is_fa ? 'جستجو در محصولات، مقالات، مستندات...' : 'Search products, articles, docs...';
?>
<div class="fas-search-overlay <?php echo esc_attr( $overlay_class ); ?>">
    <div class="fas-search-container">
        <!-- Input Wrapper -->
        <div class="fas-search-input-wrapper">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" class="fas-search-input" placeholder="<?php echo esc_attr( $placeholder_text ); ?>" aria-label="<?php esc_attr_e( 'Live Search', 'faramoj-search' ); ?>">
            <?php if ( 'yes' === $enable_voice_search ) : ?>
                <span class="fas-voice-search-btn" role="button" aria-label="<?php esc_attr_e( 'Voice Search', 'faramoj-search' ); ?>" title="<?php echo $is_fa ? 'جستجوی صوتی' : 'Voice Search'; ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path>
                        <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                        <line x1="12" y1="19" x2="12" y2="23"></line>
                        <line x1="8" y1="23" x2="16" y2="23"></line>
                    </svg>
                </span>
            <?php endif; ?>
            <!-- Standardized Close Span Element to totally bypass theme-level button overrides -->
            <span class="fas-modal-close" role="button" aria-label="<?php esc_attr_e( 'Close Search', 'faramoj-search' ); ?>">&times;</span>
        </div>

        <!-- Search History Panel -->
        <div class="fas-search-history" style="display: none;"></div>

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
                    <?php if ( ! empty( $tab['custom_icon'] ) ) : ?>
                        <img src="<?php echo esc_url( $tab['custom_icon'] ); ?>" class="fas-custom-tab-icon" style="width: 16px; height: 16px; object-fit: contain; flex-shrink: 0;" alt="<?php echo esc_attr( $tab['title'] ); ?>">
                    <?php else : ?>
                        <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
                    <?php endif; ?>
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
