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

        $cache_duration         = get_option( 'fas_cache_duration', HOUR_IN_SECONDS );
        $theme_mode             = get_option( 'fas_theme_mode', 'dark' );
        $enable_floating        = get_option( 'fas_enable_floating', 'yes' );
        $floating_position      = get_option( 'fas_floating_position', 'bottom-right' );
        $display_pages_type     = get_option( 'fas_display_pages_type', 'all' );
        $display_specific_pages = get_option( 'fas_display_specific_pages', '' );
        $floating_bg            = get_option( 'fas_floating_bg', '#0066cc' );
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

        <div class="fas-card">
            <h3><?php esc_html_e( 'Floating Search Trigger Button Settings', 'faramoj-search' ); ?></h3>
            <table class="form-table fas-form-table">
                <tr>
                    <th scope="row">
                        <label for="fas_enable_floating"><?php esc_html_e( 'Enable Floating Button', 'faramoj-search' ); ?></label>
                    </th>
                    <td>
                        <select name="fas_enable_floating" id="fas_enable_floating" class="regular-text">
                            <option value="yes" <?php selected( $enable_floating, 'yes' ); ?>><?php esc_html_e( 'Enabled', 'faramoj-search' ); ?></option>
                            <option value="no" <?php selected( $enable_floating, 'no' ); ?>><?php esc_html_e( 'Disabled', 'faramoj-search' ); ?></option>
                        </select>
                        <span class="fas-description"><?php esc_html_e( 'Display a floating quick-search trigger button automatically in the frontend.', 'faramoj-search' ); ?></span>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="fas_floating_position"><?php esc_html_e( 'Floating Button Position', 'faramoj-search' ); ?></label>
                    </th>
                    <td>
                        <select name="fas_floating_position" id="fas_floating_position" class="regular-text">
                            <option value="bottom-right" <?php selected( $floating_position, 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'faramoj-search' ); ?></option>
                            <option value="bottom-left" <?php selected( $floating_position, 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'faramoj-search' ); ?></option>
                            <option value="top-right" <?php selected( $floating_position, 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'faramoj-search' ); ?></option>
                            <option value="top-left" <?php selected( $floating_position, 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'faramoj-search' ); ?></option>
                        </select>
                        <span class="fas-description"><?php esc_html_e( 'Determine the exact placement of the floating trigger icon.', 'faramoj-search' ); ?></span>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="fas_display_pages_type"><?php esc_html_e( 'Display Pages', 'faramoj-search' ); ?></label>
                    </th>
                    <td>
                        <select name="fas_display_pages_type" id="fas_display_pages_type" class="regular-text">
                            <option value="all" <?php selected( $display_pages_type, 'all' ); ?>><?php esc_html_e( 'Show on All Pages', 'faramoj-search' ); ?></option>
                            <option value="specific" <?php selected( $display_pages_type, 'specific' ); ?>><?php esc_html_e( 'Show on Specific Pages Only', 'faramoj-search' ); ?></option>
                            <option value="none" <?php selected( $display_pages_type, 'none' ); ?>><?php esc_html_e( 'Disable Automatic Output (Manual Shortcode Only)', 'faramoj-search' ); ?></option>
                        </select>
                        <span class="fas-description"><?php esc_html_e( 'Select the locations on your site where the floating search button is displayed.', 'faramoj-search' ); ?></span>
                    </td>
                </tr>

                <tr id="fas_specific_pages_row" style="<?php echo ( 'specific' === $display_pages_type ) ? '' : 'display:none;'; ?>">
                    <th scope="row">
                        <label for="fas_display_specific_pages"><?php esc_html_e( 'Specific Page IDs or Slugs', 'faramoj-search' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="fas_display_specific_pages" id="fas_display_specific_pages" value="<?php echo esc_attr( $display_specific_pages ); ?>" class="regular-text" placeholder="e.g. 12, home, contact-us">
                        <span class="fas-description"><?php esc_html_e( 'Enter page IDs or slugs separated by commas.', 'faramoj-search' ); ?></span>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="fas_floating_bg"><?php esc_html_e( 'Floating Button Brand Color', 'faramoj-search' ); ?></label>
                    </th>
                    <td>
                        <input type="color" name="fas_floating_bg" id="fas_floating_bg" value="<?php echo esc_attr( $floating_bg ); ?>" style="width: 50px; height: 35px; border-radius: 4px; padding: 0; cursor: pointer;">
                        <span class="fas-description"><?php esc_html_e( 'Highlight active selections or floating triggers using corporate brand colors.', 'faramoj-search' ); ?></span>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button(); ?>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var displaySelect = document.getElementById('fas_display_pages_type');
    var specificRow = document.getElementById('fas_specific_pages_row');
    if (displaySelect && specificRow) {
        displaySelect.addEventListener('change', function() {
            if (this.value === 'specific') {
                specificRow.style.display = '';
            } else {
                specificRow.style.display = 'none';
            }
        });
    }
});
</script>
