<?php
/**
 * HTML layout for the Faramoj Advanced Search About Us submenu
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap fas-admin-wrap" style="max-width: 850px; margin: 40px auto; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif; text-align: center;">

    <!-- About card wrapper with top accent line -->
    <div style="background: #ffffff; border: 1px solid #cbd5e1; border-top: 4px solid #0066cc; border-radius: 12px; padding: 48px 32px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);">

        <!-- Logo Emblem -->
        <div style="width: 80px; height: 80px; border-radius: 20px; background: #0066cc; color: #ffffff; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px auto; box-shadow: 0 10px 20px rgba(0,102,204,0.25);">
            <span class="dashicons dashicons-search" style="font-size: 40px; width: 40px; height: 40px; line-height: 1.1;"></span>
        </div>

        <h2 style="margin: 0; font-size: 26px; font-weight: 800; color: #0f172a;"><?php esc_html_e( 'Faramoj Advanced Live Search', 'faramoj-search' ); ?></h2>
        <span style="display: inline-block; background: #e2e8f0; color: #475569; font-weight: 700; font-size: 11px; padding: 3px 12px; border-radius: 12px; margin-top: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Version 1.1.1</span>

        <p style="font-size: 15px; color: #475569; line-height: 1.6; max-width: 600px; margin: 24px auto 32px auto;">
            <?php esc_html_e( 'Faramoj Advanced Search (FAS) is an ultra-high performance, AJAX-driven live search engine tailored specifically for technical telecommunication products, articles, and documentation.', 'faramoj-search' ); ?>
        </p>

        <!-- Dynamic Grid of Highlight Features -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; margin-bottom: 40px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px;">
                <span class="dashicons dashicons-rest-api" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php esc_html_e( 'Custom REST Endpoints', 'faramoj-search' ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php esc_html_e( 'Bypasses the standard bottleneck of admin-ajax.php to provide instant, real-time live suggestions.', 'faramoj-search' ); ?></p>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px;">
                <span class="dashicons dashicons-translation" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php esc_html_e( 'Multilingual Compatibility', 'faramoj-search' ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php esc_html_e( 'Integrated natively with Polylang and WPML. Automatic locale filtering and separate translations options.', 'faramoj-search' ); ?></p>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px;">
                <span class="dashicons dashicons-database" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php esc_html_e( 'ACF Complex Indexing', 'faramoj-search' ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php esc_html_e( 'Indexes customized Advanced Custom Fields (frequency_range & technical_specifications) natively.', 'faramoj-search' ); ?></p>
                </div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; display: flex; align-items: flex-start; gap: 14px;">
                <span class="dashicons dashicons-universal-access" style="color: #0066cc; font-size: 24px; width: 24px; height: 24px; margin-top: 3px;"></span>
                <div>
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #1e293b;"><?php esc_html_e( 'Dual Digit Compatibility', 'faramoj-search' ); ?></h4>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.4;"><?php esc_html_e( 'Bi-directional normalization search engine matching Persian, Arabic and English numerical characters.', 'faramoj-search' ); ?></p>
                </div>
            </div>
        </div>

        <div style="border-top: 1px solid #cbd5e1; padding-top: 24px;">
            <p style="margin: 0; font-size: 12px; color: #94a3b8; font-weight: 600;">
                <?php esc_html_e( 'Proudly Developed & Optimized by', 'faramoj-search' ); ?>
                <span style="color: #0066cc; font-weight: 700; margin-left: 4px;">Danial Kenarkoohi</span>
            </p>
        </div>

    </div>

</div>
