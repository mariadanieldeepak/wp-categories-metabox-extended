<?php

namespace WPCategoriesMetaboxExtended\Core;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class WPCategoriesMetaboxExtended {

	/**
	 * @const PLUGIN_VERSION Plugin version.
	 */
	const PLUGIN_VERSION = '1.0.0';

	/**
	 * @var string $plugin_file Plugin base file.
	 */
	public $plugin_file;

	/**
	 * @var bool $is_loaded Indicates if the plugin is loaded.
	 */
	public $is_loaded;

	/**
	 * @var \WPCategoriesMetaboxExtended\Core\Loadie[]
	 */
	protected $loadies = [];

	/**
	 * @var \WPCategoriesMetaboxExtended\UI\UIManager $ui_manager
	 */
	public $ui_manager;

	/**
	 * @var \WPCategoriesMetaboxExtended\Core\Utilities\TermHelper
	 */
	public $term_helper;

	/**
	 * WPCategoriesMetaboxExtended constructor.
	 *
	 * @param $plugin_file
	 * @param $ui_manager
	 */
	public function __construct( $plugin_file, $ui_manager, $term_helper ) {
		$this->plugin_file = $plugin_file;
		$this->ui_manager  = $ui_manager;
		$this->term_helper = $term_helper;

		$this->add_loadie( $ui_manager );
	}

	/**
	 * Load() is invoked on `plugins_loaded` hook.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		if ( $this->is_loaded ) {
			return;
		}

		foreach ( $this->loadies as $loadie ) {
			$loadie->load();
		}

		$this->is_loaded = true;

		/**
		 * Fires when WP Categories Metabox Extended plugin has been loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wpcme_wp_categories_metabox_extended_loaded' );
	}

	/**
	 * Adds Loadie objects that are to be invoked.
	 *
	 * @since 1.0.0
	 *
	 * @param Loadie $loadie
	 *
	 * @return bool Returns FALSE if the plugin has already been loaded.
	 */
	public function add_loadie( $loadie ) {
		if ( $this->is_loaded ) {
			return false;
		}

		if ( $loadie instanceof Loadie ) {
			$this->loadies[] = $loadie;
		}
	}

	/**
	 * Getter for $plugin_file.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin base file.
	 */
	public function get_plugin_file() {
		return $this->plugin_file;
	}

	/**
	 * Getter for Plugin version.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin version.
	 */
	public function get_plugin_version() {
		return self::PLUGIN_VERSION;
	}
}