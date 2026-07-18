<?php
/**
 * Customization of display items required for the MU Profiles plugin
 *
 * Department page ordering, ensuring all profiles will display not just 10, and custom department listing on profiles.
 *
 * @package mu-profiles
 */

/**
 * Override order of department listings using meta of order by
 * before sorting alphabetically.
 *
 * @param object $query The defauly query.
 */
function mu_profiles_order_department_archives( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( is_tax( 'department' ) ) {
		if ( get_field( 'sort_by_last_name_first_name', 'option' ) ) {
			$query->set(
				'meta_query',
				array(
					'last_name'  => array(
						'key' => 'last_name',
					),
					'first_name' => array(
						'key' => 'first_name',
					),
				)
			);

			$query->set(
				'orderby',
				array(
					'menu_order' => 'ASC',
					'last_name'  => 'ASC',
					'first_name' => 'ASC',
				)
			);
		} else {
			$query->set( 'order', 'asc' );
			$query->set( 'orderby', 'menu_order title' );
		}
		$query->parse_query();
		return;
	}
}
add_action( 'pre_get_posts', 'mu_profiles_order_department_archives', 1 );

/**
 * All department listings will display all listings.
 *
 * @param object $query The defauly query.
 */
function mu_profiles_unlimited_profiles( $query ) {
	// exit out if it's the admin or it isn't the main query.
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( is_tax( 'department' ) ) {
		$query->set( 'posts_per_page', -1 );
	}
}
add_action( 'pre_get_posts', 'mu_profiles_unlimited_profiles', 1 );

/**
 * Displays listings of Departments on Profile listings and Profiles
 *
 * @param integer $post The Post ID to get the terms for.
 * @param boolean $shortcode Whether or not it's being requested from a shortcode.
 */
function mu_profiles_department_listing( $post, $shortcode = false ) {
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
function mu_profiles_department( $post ) {
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
