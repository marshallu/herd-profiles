<?php
/**
 * Customization of display items required for the Herd Profiles plugin
 *
 * Department page ordering, ensuring all profiles will display not just 10, and custom department listing on profiles.
 *
 * @package herd-profiles
 */

/**
 * Override order of department listings using meta of order by
 * before sorting alphabetically.
 *
 * @param object $query The defauly query.
 */
function herd_profiles_order_department_archives( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( is_tax( 'department' ) ) {
		// Last name sorting is applied after the query by herd_profiles_sort_department_archives().
		$query->set( 'order', 'asc' );
		$query->set( 'orderby', 'menu_order title' );
		$query->parse_query();
		return;
	}
}

/**
 * Sort profiles by menu order, then last name, first name, and title.
 *
 * Done in PHP rather than with a meta_query so profiles missing a first or
 * last name are still listed; those fall back to their title.
 *
 * @param WP_Post[] $posts The profiles to sort.
 * @return WP_Post[]
 */
function herd_profiles_sort_by_name( $posts ) {
	$keys = array();

	foreach ( $posts as $the_post ) {
		$last  = get_post_meta( $the_post->ID, 'last_name', true );
		$first = get_post_meta( $the_post->ID, 'first_name', true );

		$keys[ $the_post->ID ] = array(
			(int) $the_post->menu_order,
			strtolower( $last ? $last : $the_post->post_title ),
			strtolower( (string) $first ),
			strtolower( $the_post->post_title ),
		);
	}

	usort(
		$posts,
		function ( $a, $b ) use ( $keys ) {
			return $keys[ $a->ID ] <=> $keys[ $b->ID ];
		}
	);

	return $posts;
}

/**
 * Sort department archives by last name when the option is enabled.
 *
 * @param WP_Post[] $posts The queried posts.
 * @param WP_Query  $query The query.
 * @return WP_Post[]
 */
function herd_profiles_sort_department_archives( $posts, $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_tax( 'department' ) ) {
		return $posts;
	}

	if ( get_field( 'sort_by_last_name_first_name', 'option' ) ) {
		$posts = herd_profiles_sort_by_name( $posts );
	}

	return $posts;
}
add_filter( 'the_posts', 'herd_profiles_sort_department_archives', 10, 2 );
add_action( 'pre_get_posts', 'herd_profiles_order_department_archives', 1 );

/**
 * All department listings will display all listings.
 *
 * @param object $query The defauly query.
 */
function herd_profiles_unlimited_profiles( $query ) {
	// exit out if it's the admin or it isn't the main query.
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( is_tax( 'department' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
}
add_action( 'pre_get_posts', 'herd_profiles_unlimited_profiles', 1 );

/**
 * Displays listings of Departments on Profile listings and Profiles
 *
 * @param integer $post The Post ID to get the terms for.
 * @param boolean $shortcode Whether or not it's being requested from a shortcode.
 */
function herd_profiles_department_listing( $post, $shortcode = false ) {
	$terms = get_the_terms( $post, 'department' );

	$links = array();

	if ( $terms ) {
		foreach ( $terms as $the_term ) {
			$url = get_term_link( $the_term, 'department' );

			if ( ! is_wp_error( $url ) && ! get_field( 'department_hide', $the_term ) ) {
				$links[] = '<a href="' . esc_url( $url ) . '" rel="tag">' . $the_term->name . '</a>';
			}
		}

		if ( $shortcode ) {
			return wp_kses_post( implode( ', ', $links ) );
		} else {
			echo wp_kses_post( implode( ', ', $links ) );
		}
	}
}

/**
 * Displays listings of Departments on Profile listings and Profiles
 *
 * @param integer $post The Post ID to get the terms for.
 */
function herd_profiles_department( $post ) {
	$terms = get_the_terms( $post, 'department' );

	$links = array();

	if ( $terms ) {
		foreach ( $terms as $the_term ) {
			$url = get_term_link( $the_term, 'department' );

			if ( ! is_wp_error( $url ) && ! get_field( 'department_hide', $the_term ) ) {
				$links[] = '<a href="' . esc_url( $url ) . '" rel="tag">' . $the_term->name . '</a>';
			}
		}
		echo wp_kses_post( implode( ', ', $links ) );
	}
}
