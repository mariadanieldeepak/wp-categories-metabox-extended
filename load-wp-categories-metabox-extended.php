<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Load WP Categories Metabox Extended plugin.
 *
 * @since 1.1.0 Renamed variable to `$wpcme` for convenience.
 * @since 1.0.0
 *
 * @param string $plugin_file Main plugin file.
 */
function wpcme_load_wp_categories_metabox_extended( $plugin_file ) {
	global $wpcme;

	$plugin_dir = plugin_dir_path( $plugin_file );

	// Setup autoloader.
	require_once 'include/AutoLoader.php';

	$loader = new \WPCategoriesMetaboxExtended\AutoLoader();
	$loader->add_namespace( 'WPCategoriesMetaboxExtended', $plugin_dir . 'include' );

	if ( file_exists( $plugin_dir . 'tests/' ) ) {
		// if tests are present, then add them.
		$loader->add_namespace( 'WPCategoriesMetaboxExtended', $plugin_dir . 'tests/wp-tests' );
	}

	$loader->register();

	// Avoid repeated instantiations.
	if ( isset( $wpcme ) ) {
		return;
	}

	$ui_manager  = new \WPCategoriesMetaboxExtended\UI\UIManager();
	$term_helper = new \WPCategoriesMetaboxExtended\Core\Utilities\TermHelper();

	// Dependency injection principle.
	$wpcme = new \WPCategoriesMetaboxExtended\Core\WPCategoriesMetaboxExtended( $plugin_file, $ui_manager, $term_helper );
	$wpcme->add_loadie( new \WPCategoriesMetaboxExtended\Core\Request\WPCategoriesMetaboxExtendedAction() );

	add_action( 'plugins_loaded', array( $wpcme, 'load' ) );
}

/**
 * Return the global instance of Email Log plugin.
 * Eventually the EmailLog class might become singleton.
 *
 * @since 1.1.0 Renamed variable to `$wpcme` for convenience.
 * @since 1.0.0
 *
 * @global \WPCategoriesMetaboxExtended\core\WPCategoriesMetaboxExtended $search_categories_metabox
 *
 * @return \WPCategoriesMetaboxExtended\core\WPCategoriesMetaboxExtended
 */
function wp_categories_metabox_extended() {
	global $wpcme;

	return $wpcme;
}
