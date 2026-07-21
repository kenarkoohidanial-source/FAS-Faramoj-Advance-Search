<?php
/**
 * HTML layout for the admin panel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap fas-admin-wrap">
    <div class="fas-header">
        <span class="fas-logo-badge"><?php esc_html_e( 'FAS', 'faramoj-search' ); ?></span>
        <h1 class="wp-heading-inline"><?php esc_html_e( 'Faramoj Advanced Search', 'faramoj-search' ); ?></h1>
    </div>
    <hr class="wp-header-end">

    <form method="post" action="options.php">
        <?php
        settings_fields( 'fas_settings_group' );
        do_settings_sections( 'fas_settings_group' );

        $cache_duration = get_option( 'fas_cache_duration', HOUR_IN_SECONDS );
        $theme_mode     = get_option( 'fas_theme_mode', 'dark' );
        ?>

        <div class="fas-card">
            <h3><?php esc_html_e( 'Search Engine Settings', 'faramoj-search' ); ?></h3>
            <table class="form-table fas-form-table">
                <tr>
                    <th scope="row">
                        <label for="fas_cache_duration"><?php esc_html_e( 'Cache Duration (Transient)', 'faramoj-search' ); ?></label>
                    </th>
                    <td>
                        <input type="number" name="fas_cache_duration" id="fas_cache_duration" value="<?php echo esc_attr( $cache_duration ); ?>" class="regular-text" min="0">
                        <span class="fas-description"><?php esc_html_e( 'Time in seconds to cache search results. Use 3600 for 1 hour. Set to 0 to disable transient caching.', 'faramoj-search' ); ?></span>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="fas_theme_mode"><?php esc_html_e( 'Overlay Design Accent', 'faramoj-search' ); ?></label>
                    </th>
                    <td>
                        <select name="fas_theme_mode" id="fas_theme_mode" class="regular-text">
                            <option value="dark" <?php selected( $theme_mode, 'dark' ); ?>><?php esc_html_e( 'Deep Slate Dark Mode Overlay', 'faramoj-search' ); ?></option>
                            <option value="light" <?php selected( $theme_mode, 'light' ); ?>><?php esc_html_e( 'Clean Corporate Light Mode Overlay', 'faramoj-search' ); ?></option>
                        </select>
                        <span class="fas-description"><?php esc_html_e( 'Select the visual aesthetic for your modal search overlay.', 'faramoj-search' ); ?></span>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(); ?>
    </form>
</div>
