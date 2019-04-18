<?php

namespace WPCategoriesMetaboxExtended\UI;

use WPCategoriesMetaboxExtended\Core\Loadie;

class UIManager implements Loadie {

	const LOAD_TERMS_NONCE_ACTION = 'wpcme_load_categories';

	const NONCE_QUERY_ARG = 'nonce';

	/**
	 * @var array $allowed_screens
	 */
	public $allowed_screens = array( 'post' );

	/**
	 * @var string $current_screen_id
	 */
	protected $current_screen_id;

	/**
	 * Load() will be invoked through Loadies in Plugin's primary class.
	 *
	 * This method serves as the entry point for the class.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		$this->register_hooks();
	}

	/**
	 * Setup the required hooks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {
		add_action( 'current_screen', array( $this, 'on_current_screen' ) );
	}

	/**
	 * Replace the default category metabox with WP Categories Metabox
	 * Extended.
	 *
	 * @since 1.0.0
	 *
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/current_screen
	 */
	public function on_current_screen() {
		$this->current_screen_id = $this->get_current_screen_id();

		/**
		 * Included for backwards compatibility.
		 *
		 * @deprecated 1.1.0
		 *
		 * TODO: To be removed in the next version.
		 */
		$allowed_screens = apply_filters( 'wpcme_show_search_terms_metabox_on_screens', $this->allowed_screens );

		/**
		 * Filters the list of screens where WP Categories Metabox Extended
		 * should be shown.
		 *
		 * @since 1.1.0 Rename filter.
		 * @since 1.0.0
		 *
		 * @param array $allowed_screens List of screen IDs where
		 *                               WP Categories Metabox Extended
		 *                               will be displayed.
		 */
		$allowed_screens = apply_filters( 'wpcme_extended_metabox_screens', $allowed_screens );

		if ( ! in_array( $this->current_screen_id, $allowed_screens, true ) ) {
			return;
		}

		// Do not add metabox when Gutenberg is enabled.
		if ( use_block_editor_for_post_type( $this->current_screen_id ) ) {
			return;
		}

		$this->remove_default_categories_metabox();
		$this->add_wp_categories_metabox_extended();
	}

	/**
	 * Gets the current screen ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string Screen ID.
	 */
	public function get_current_screen_id() {
		$screen = get_current_screen();

		return $screen->id;
	}

	/**
	 * Removes the default `Category` metabox from the WordPress editor.
	 *
	 * @since 1.0.0
	 */
	public function remove_default_categories_metabox() {
		remove_meta_box( 'categorydiv', 'post', 'side' );
	}

	/**
	 * Adds Categories metabox with the Search feature.
	 *
	 * @since 1.0.0
	 */
	public function add_wp_categories_metabox_extended() {
		add_meta_box( 'wpcategoriesmetaboxextendeddiv', __( 'Categories', 'search-categories-metabox' ), array(
			$this,
			'render_search_term_metabox',
		), 'post', 'side', 'default', null );

		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
	}

	/**
	 * Loads the CSS required for the Admin section.
	 *
	 * @since 1.0.0
	 */
	public function load_admin_styles() {

		$wp_categories_metabox_extended = wp_categories_metabox_extended();

		wp_enqueue_style(
			'wpcme_wp_admin_editor_css',
			plugin_dir_url( $wp_categories_metabox_extended->get_plugin_file() ) . 'assets/css/wp-editor.css',
			false,
			$wp_categories_metabox_extended->get_plugin_version()
		);
	}

	/**
	 * Loads the Javascripts requried for the Admin section.
	 *
	 * @since 1.0.0
	 */
	public function load_admin_scripts() {

		global $post_id;
		$wp_categories_metabox_extended = wp_categories_metabox_extended();

		wp_enqueue_script(
			'wpcme_wp_admin_editor_js',
			plugin_dir_url( $wp_categories_metabox_extended->get_plugin_file() ) . 'assets/js/wp-categories-metabox-extended.js',
			array( 'jquery' ),
			$wp_categories_metabox_extended->get_plugin_version()
		);

		wp_localize_script(
			'wpcme_wp_admin_editor_js',
			'ajax_object',
			array(
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				self::NONCE_QUERY_ARG => wp_create_nonce( self::LOAD_TERMS_NONCE_ACTION ),
				'screen'              => $this->current_screen_id,
				'load_terms_action'   => 'load_terms',
				'messages'            => array(
					'no_results'      => __( 'No results found for the given search term.', 'wp-categories-metabox-extended' ),
					'something_wrong' => __( 'Something went wrong!', 'wp-categories-metabox-extended' ),
				),
				'post_id'             => isset( $post_id ) ? $post_id : 0,
			)
		);
	}

	/**
	 * Renders the Category div in the posts editor.
	 *
	 * @since 1.0.0
	 */
	public function render_search_term_metabox() {
		?>
        <div class="wpcme-container">
            <div class="spinner-parent">
                <input id="wpcme-search-term" type="text" name="wpcme-search-term" size="16"/>
                <span class="wpcme-spinner"><img src="/wp-admin/images/loading.gif"/></span>
            </div>
            <input type="button" class="button" id="wpcme-search-clear"
                   value="<?php _e( 'Clear', 'wp-categories-metabox-extended' ) ?>"/>

            <div class="wpcme-terms-panel">
                <ul id="wpcme-terms-ul">

                </ul>
                <span class="selected-terms">

                </span>
            </div>
        </div>
		<?php
	}

	/**
	 * Gets the load terms action nonce.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_load_terms_nonce() {
		return self::LOAD_TERMS_NONCE_ACTION;
	}

	/**
	 * Gets the load terms nonce.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_nonce_query_arg() {
		return self::NONCE_QUERY_ARG;
	}
}
