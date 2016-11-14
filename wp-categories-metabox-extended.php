<?php
/*
Plugin Name: WP Categories Metabox Extended
Plugin URI:  https://mariadanieldeepak.com/wordpress/wp-categories-metabox-extended/
Description: This plugin includes a search field to the Categories Metabox in the WordPress Editor.
Version:     1.0.0
Author:      Maria Daniel Deepak
Author URI:  https://danieldeepak.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-categories-metabox-extended
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if ( version_compare( PHP_VERSION, '5.3.0', '<' ) ) {
	/**
	 * Only support PHP 5.3.0 and above.
	 *
	 * @since 1.0.0
	 */
	function wpcme_wp_categories_metabox_extended_compatibility_notice() {
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

	add_action( 'admin_notices', 'wpcme_wp_categories_metabox_extended_compatibility_notice' );

	/**
	 * Deactivate WP Categories Metabox Extended.
	 *
	 * @since 1.0.0
	 */
	function wpcme_wp_categories_metabox_extended_deactivate() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	add_action( 'admin_init', 'wpcme_wp_categories_metabox_extended_deactivate' );

	return;
}

// PHP is at least 5.3, so we can safely include namespace code.
require_once( 'load-wp-categories-metabox-extended.php' );
wpcme_load_wp_categories_metabox_extended( __FILE__ );
