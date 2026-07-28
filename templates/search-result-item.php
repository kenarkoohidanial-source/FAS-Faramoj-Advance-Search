<?php
/**
 * Single Search Result Entry Template
 * Overridable in child themes via 'faramoj-advanced-search/search-result-item.php'
 * This is loaded dynamically and can be referenced for template customization.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Variables passed: $item (array containing title, permalink, image, excerpt), $category (string)
$item     = isset( $args['item'] ) ? $args['item'] : array();
$category = isset( $args['category'] ) ? $args['category'] : 'products';

if ( empty( $item ) ) {
    return;
}
?>
<a href="<?php echo esc_url( $item['permalink'] ); ?>" class="fas-result-item" data-post-id="<?php echo esc_attr( $item['id'] ?? 0 ); ?>" data-post-title="<?php echo esc_attr( $item['title'] ); ?>">
    <?php if ( ! empty( $item['image'] ) ) : ?>
        <img class="fas-result-image" src="<?php echo esc_url( $item['image'] ); ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
    <?php else : ?>
        <div class="fas-result-fallback-icon">
            <?php if ( 'products' === $category ) : ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                    <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
            <?php elseif ( 'posts' === $category ) : ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            <?php else : ?>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="fas-result-info">
        <h4 class="fas-result-title"><?php echo $item['title_html']; ?></h4>
        <p class="fas-result-excerpt"><?php echo $item['excerpt_html']; ?></p>
    </div>
</a>
