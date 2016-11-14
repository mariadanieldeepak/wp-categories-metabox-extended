<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
 * Load WP Categories Metabox Extended plugin.
 *
 * @since 1.0.0
 *
 * @param string $plugin_file Main plugin file.
 */
function wpcme_load_wp_categories_metabox_extended( $plugin_file ) {
	global $wp_categories_metabox_extended;

	$plugin_dir = plugin_dir_path( $plugin_file );

	// Setup autoloader.
	require_once 'include/WPCategoriesMetaboxExtendedAutoLoader.php';

	$loader = new \WPCategoriesMetaboxExtended\WPCategoriesMetaboxExtendedAutoLoader();
	$loader->add_namespace( 'WPCategoriesMetaboxExtended', $plugin_dir . 'include' );

	if ( file_exists( $plugin_dir . 'tests/' ) ) {
		// if tests are present, then add them.
		$loader->add_namespace( 'WPCategoriesMetaboxExtended', $plugin_dir . 'tests/wp-tests' );
	}

	$loader->register();

	if ( ! isset( $wp_categories_metabox_extended ) ) {
		$ui_manager          = new \WPCategoriesMetaboxExtended\UI\UIManager();
		$term_helper         = new \WPCategoriesMetaboxExtended\Core\Utilities\TermHelper();

		$wp_categories_metabox_extended = new \WPCategoriesMetaboxExtended\Core\WPCategoriesMetaboxExtended( $plugin_file, $ui_manager, $term_helper );
		$wp_categories_metabox_extended->add_loadie( new \WPCategoriesMetaboxExtended\Core\Request\WPCategoriesMetaboxExtendedAction() );

		add_action( 'plugins_loaded', array( $wp_categories_metabox_extended, 'load' ) );
	}

}

/**
 * Return the global instance of Email Log plugin.
 * Eventually the EmailLog class might become singleton.
 *
 * @since 1.0.0
 *
 * @global \WPCategoriesMetaboxExtended\core\WPCategoriesMetaboxExtended $search_categories_metabox
 *
 * @return \WPCategoriesMetaboxExtended\core\WPCategoriesMetaboxExtended
 */
function wp_categories_metabox_extended() {
	global $wp_categories_metabox_extended;

	return $wp_categories_metabox_extended;
}