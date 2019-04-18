<?php
/*
Plugin Name: WP Categories Metabox Extended
Plugin URI:  https://mariadanieldeepak.com/wordpress/wp-categories-metabox-extended/
Description: This plugin includes a search field to the Categories Metabox in the WordPress Editor.
Version:     1.1.1
Author:      Maria Daniel Deepak
Author URI:  https://danieldeepak.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-categories-metabox-extended
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Deactivate WP Categories Metabox Extended.
 *
 * @since 1.1.0 Renamed the function name.
 * @since 1.0.0
 */
function wpcme_deactivate_plugin() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Display compatibility notice for PHP version < 5.3.0.
 *
 * @since 1.1.0 Renamed the function name.
 * @since 1.0.0
 */
function wpcme_display_compatibility_notice() {
	?>
    <div class="error">
        <p>
			<?php
			_e( 'WP Categories Metabox Extended plugin requires at least PHP 5.3 to function properly. Please upgrade PHP.', 'wp-categories-metabox-extended' );
			?>
        </p>
    </div>
	<?php
}

/**
 * Returns TRUE if PHP version is < 5.3.0
 *
 * @since 1.1.0
 *
 * @return bool
 */
function wpcme_is_php_below_530() {
	if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
		return true;
	}

	return false;
}

/**
 * Loads the Plugin if PHP is >= 5.3.0
 *
 * @since 1.1.0
 */
function wpcme_load() {
	if ( wpcme_is_php_below_530() ) {
		add_action( 'admin_notices', 'wpcme_display_compatibility_notice' );
		add_action( 'admin_init', 'wpcme_deactivate_plugin' );

		return;
	}

	// PHP is at least 5.3, so we can safely include namespace code.
	require_once( 'load-wp-categories-metabox-extended.php' );
	wpcme_load_wp_categories_metabox_extended( __FILE__ );
}

/**
 * Entry point.
 *
 * @since 1.1.0
 */
wpcme_load();
