<?php
/**
 * Customize the Dashboard and Editor for Profiles and Departments
 *
 * @package herd-profiles
 */

/**
 * Remove Date, Last Modified, and Yoast SEO Columns from Profile listing page
 *
 * @param type $columns Default WordPress post columns.
 */
function set_custom_edit_employee_columns( $columns ) {
	if ( ! is_super_admin() ) {
		unset( $columns['date'] );
		unset( $columns['modified'] );
	}

	unset( $columns['wpseo-score'] );
	unset( $columns['wpseo-score-readability'] );
	unset( $columns['wpseo-title'] );
	unset( $columns['wpseo-metadesc'] );
	unset( $columns['wpseo-focuskw'] );
	unset( $columns['wpseo-links'] );
	unset( $columns['wpseo-linked'] );
	$columns['title']      = __( 'Name', 'herd-profiles' );
	$columns['menu_order'] = __( 'Order', 'herd-profiles' );
	return $columns;
}
add_filter( 'manage_employee_posts_columns', 'set_custom_edit_employee_columns' );
add_filter( 'manage_edit-employee_sortable_columns', 'herd_profiles_add_custom_column_make_sortable' );

/**
 * Make the Last Modified and Order columns sortable
 *
 * @param object $columns The list of columns.
 * @return object
 */
function herd_profiles_add_custom_column_make_sortable( $columns ) {
	$columns['modified']   = 'modified';
	$columns['menu_order'] = 'menu_order';
	return $columns;
}

/**
 * Render the Order column value for the Profile listing page.
 *
 * @param string $column  The column name.
 * @param int    $post_id The current post ID.
 */
function herd_profiles_render_employee_columns( $column, $post_id ) {
	if ( 'menu_order' === $column ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
add_action( 'manage_employee_posts_custom_column', 'herd_profiles_render_employee_columns', 10, 2 );

/**
 * Change placeholder text on Create/Edit Profile page to 'Enter Name Here'
 *
 * @param string $title The default title placeholder.
 *
 * @return string
 */
function herd_profiles_change_default_title_to_name( $title ) {
	$screen = get_current_screen();

	if ( 'employee' === $screen->post_type ) {
		$title = 'Enter Name Here';
	}

	return $title;
}
add_filter( 'enter_title_here', 'herd_profiles_change_default_title_to_name' );

/**
 * Modify the title to the name on save.
 *
 * @param array $data The array of data.
 * @return array
 */
function herd_profiles_modify_title( $data ) {
	if ( 'employee' === $data['post_type'] && ( isset( $_POST['acf']['field_60d9fadc1cb1d'] ) && isset( $_POST['acf']['field_60d9faf11cb1f'] ) ) ) { // phpcs:ignore
		$employee_first = sanitize_text_field( wp_unslash( $_POST['acf']['field_60d9fadc1cb1d'] ) ); // phpcs:ignore
		$employee_last  = sanitize_text_field( wp_unslash( $_POST['acf']['field_60d9faf11cb1f'] ) ); // phpcs:ignore

		if ( isset( $_POST['acf']['field_60d9fb1ad119e'] ) ) { // phpcs:ignore
			$employee_title = sanitize_text_field( wp_unslash( $_POST['acf']['field_60d9fb1ad119e'] ) ); // phpcs:ignore
		} else {
			$employee_title = '';
		}

		if ( isset( $_POST['acf']['field_60d9fae71cb1e'] ) ) { // phpcs:ignore
			$employee_middle = sanitize_text_field( wp_unslash( $_POST['acf']['field_60d9fae71cb1e'] ) ); // phpcs:ignore
		} else {
			$employee_middle = '';
		}

		if ( isset( $_POST['acf']['field_60d9faf11cc20'] ) ) { // phpcs:ignore
			$employee_suffix = sanitize_text_field( wp_unslash( $_POST['acf']['field_60d9faf11cc20'] ) ); // phpcs:ignore
		} else {
			$employee_suffix = '';
		}

		$full_name = '';

		if ( $employee_title ) {
			$full_name .= $employee_title . ' ';
		}

		$full_name .= $employee_first . ' ';

		if ( $employee_middle ) {
			$full_name .= $employee_middle . ' ';
		}

		$full_name .= $employee_last;

		if ( $employee_suffix ) {
			$full_name .= ', ' . $employee_suffix;
		}

		$data['post_title'] = trim( $full_name );
	}
	return $data; // Returns the modified data.
}
add_filter( 'wp_insert_post_data', 'herd_profiles_modify_title', '99', 1 );
