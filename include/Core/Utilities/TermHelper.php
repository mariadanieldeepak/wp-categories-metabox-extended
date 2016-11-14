<?php

namespace WPCategoriesMetaboxExtended\Core\Utilities;

/**
 * Class TermHelper
 *
 * @since 1.0.0
 *
 * @package WPCategoriesMetaboxExtended\Core\Utilities
 */
class TermHelper {
	/**
	 * Gets the taxonomies by post type.
	 *
	 * @param string $post_type WordPress Post Type.
	 *
	 * @return array List of Taxonomies
	 */
	public function get_post_type_taxonomies( $post_type ) {
		$taxonomies = get_object_taxonomies( $post_type, 'objects' );

		$taxonomy_names = [];
		foreach ( $taxonomies as $taxonomy ) {
			if ( ! $taxonomy->public ) {
				continue;
			}

			if ( ! $taxonomy->hierarchical ) {
				continue;
			}
			$taxonomy_names[] = $taxonomy->name;
		}

		return $taxonomy_names;
	}

	/**
	 * Gets the list of terms by taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param        $taxonomy
	 * @param string $search_term
	 *
	 * @return array|int|\WP_Error
	 */
	public function get_terms_by_taxonomy( $taxonomy, $search_term = '' ) {

		$args = array(
			'taxonomy'   => $taxonomy,
			'fields'     => 'id=>name',
			'hide_empty' => false,
		);

		if ( ! empty( $search_term ) ) {
			$args = wp_parse_args( array( 'name__like' => $search_term ), $args );
		}

		return get_terms( $args );
	}

	/**
	 * Gets the terms ids of a given post.
	 *
	 * @since 1.0.0
	 *
	 * @param $object_id
	 * @param $taxonomy
	 *
	 * @return array|\WP_Error
	 */
	public function get_object_term_ids( $object_id, $taxonomy ) {
		return wp_get_object_terms( $object_id, $taxonomy, array( 'fields' => 'ids' ) );
	}

	/**
	 * Gets the Post taxonomy Category slug by Post ID.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param $post_id
	 * @return array
	 */
	public function get_post_taxonomy( $post_id ) {
		$taxonomies = get_post_taxonomies( $post_id );
		$taxonomy   = $taxonomies[0];
		return $taxonomy;
	}
}