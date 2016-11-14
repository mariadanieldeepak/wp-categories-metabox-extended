<?php

namespace WPCategoriesMetaboxExtended\Core\Request;

use WPCategoriesMetaboxExtended\Core\Loadie;

/**
 * WPCategoriesMetaboxExtendedAction AJAX action class.
 *
 * @since 1.0.0
 *
 * @package WPCategoriesMetaboxExtended\Core\Request
 */
class WPCategoriesMetaboxExtendedAction implements Loadie {

	/**
	 * This is the entry point of the class.
	 */
	public function load() {
		add_action( 'wp_ajax_load_terms', array( $this, 'load_terms' ) );
		add_action( 'wp_ajax_nopriv_load_terms', array( $this, 'load_terms' ) );
	}

	/**
	 * Returns the Terms as JSON response.
	 */
	public function load_terms() {
		$wp_categories_metabox_extended = wp_categories_metabox_extended();

		check_ajax_referer( $wp_categories_metabox_extended->ui_manager->get_load_terms_nonce(), $wp_categories_metabox_extended->ui_manager->get_nonce_query_arg() );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( new \WP_Error( 'USER_NOT_LOGGED_IN', 'This request cannot be made unless User logs in to WP.' ) );
		}

		$post_type   = isset( $_POST['screen'] ) ? sanitize_text_field( $_POST['screen'] ) : '';
		$search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( $_POST['search_term'] ) : '';
		// Post ID will be optional while user is creating a new post.
		$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

		if ( trim( $post_type ) === '' ) {
			wp_send_json_error( new \WP_Error( 'POST_TYPE_EMPTY', 'Post Type cannot be empty.' ) );
		}

		$wp_categories_metabox_extended = wp_categories_metabox_extended();
		$taxonomies                     = $wp_categories_metabox_extended->term_helper->get_post_type_taxonomies( $post_type );

		$terms = $wp_categories_metabox_extended->term_helper->get_terms_by_taxonomy( $taxonomies[0], $search_term );

		// $post_terms will be returned as an empty array, while user is
		// creating a new post in the editor.
		$post_terms = array();

		if ( $post_id > 0 ) {
			$post_terms = $wp_categories_metabox_extended->term_helper->get_object_term_ids( $post_id, $taxonomies[0] );
		}

		$data = array(
			'terms'      => $terms,
			'post_terms' => $post_terms,
		);

		wp_send_json_success( $data );
	}
}
