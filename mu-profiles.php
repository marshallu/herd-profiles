<?php
/**
 * MU Profiles
 *
 * This plugin was built to allow for Marshall University websites to display employee profiles.
 *
 * @package MU Profiles
 *
 * Plugin Name: MU Profiles
 * Plugin URI: https://www.marshall.edu
 * Description: A facutly, staff, employee management plugin for Marshall University
 * Version: 1.4.0
 * Author: Christopher McComas
 */

if ( ! class_exists( 'ACF' ) ) {
	return new WP_Error( 'broke', __( 'Advanced Custom Fields is required for this plugin.', 'my_textdomain' ) );
}

require plugin_dir_path( __FILE__ ) . '/acf-fields.php';
require plugin_dir_path( __FILE__ ) . '/display-custom.php';
require plugin_dir_path( __FILE__ ) . '/editor.php';
require plugin_dir_path( __FILE__ ) . '/shortcodes.php';

add_filter(
	'timber/locations',
	function ( array $dirs ): array {
		$dirs[] = array( plugin_dir_path( __FILE__ ) . 'templates' );
		return $dirs;
	}
);

/**
 * Register a custom post type called "employee".
 *
 * @see get_post_type_labels() for label keys.
 */
function mu_profiles_employee_post_type() {
	$labels = array(
		'name'                  => _x( 'Profiles', 'Post type general name', 'mu-profiles' ),
		'singular_name'         => _x( 'Profile', 'Post type singular name', 'mu-profiles' ),
		'menu_name'             => _x( 'Profiles', 'Admin Menu text', 'mu-profiles' ),
		'name_admin_bar'        => _x( 'Profile', 'Add New on Toolbar', 'mu-profiles' ),
		'add_new'               => __( 'Add New', 'mu-profiles' ),
		'add_new_item'          => __( 'Add New Profile', 'mu-profiles' ),
		'new_item'              => __( 'New Profile', 'mu-profiles' ),
		'edit_item'             => __( 'Edit Profile', 'mu-profiles' ),
		'view_item'             => __( 'View Profile', 'mu-profiles' ),
		'all_items'             => __( 'All Profiles', 'mu-profiles' ),
		'search_items'          => __( 'Search Profiles', 'mu-profiles' ),
		'parent_item_colon'     => __( 'Parent Profiles:', 'mu-profiles' ),
		'not_found'             => __( 'No Profiles found.', 'mu-profiles' ),
		'not_found_in_trash'    => __( 'No Profiles found in Trash.', 'mu-profiles' ),
		'featured_image'        => _x( 'Profile Image', 'Overrides the "Featured Image" phrase for this post type. Added in 4.3', 'mu-profiles' ),
		'set_featured_image'    => _x( 'Set image', 'Overrides the "Set featured image" phrase for this post type. Added in 4.3', 'mu-profiles' ),
		'remove_featured_image' => _x( 'Remove image', 'Overrides the "Remove featured image" phrase for this post type. Added in 4.3', 'mu-profiles' ),
		'use_featured_image'    => _x( 'Use as image', 'Overrides the "Use as featured image" phrase for this post type. Added in 4.3', 'mu-profiles' ),
		'archives'              => _x( 'Profile archives', 'The post type archive label used in nav menus. Default "Post Archives". Added in 4.4', 'mu-profiles' ),
		'insert_into_item'      => _x( 'Insert into Profile', 'Overrides the "Insert into post"/"Insert into page" phrase (used when inserting media into a post). Added in 4.4', 'mu-profiles' ),
		'uploaded_to_this_item' => _x( 'Uploaded to this Profile', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase (used when viewing media attached to a post). Added in 4.4', 'mu-profiles' ),
		'filter_items_list'     => _x( 'Filter Profiles list', 'Screen reader text for the filter links heading on the post type listing screen. Default "Filter posts list"/"Filter pages list". Added in 4.4', 'mu-profiles' ),
		'items_list_navigation' => _x( 'Profiles list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default "Posts list navigation"/"Pages list navigation". Added in 4.4', 'mu-profiles' ),
		'items_list'            => _x( 'Profiles list', 'Screen reader text for the items list heading on the post type listing screen. Default "Posts list"/"Pages list". Added in 4.4', 'mu-profiles' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_rest'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'profile' ),
		'capability_type'    => 'employee',
		'map_meta_cap'       => true,
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 55,
		'supports'           => array( 'title', 'custom-fields', 'page-attributes', 'revisions' ),
		'taxonomies'         => array( 'department' ),
		'menu_icon'          => 'dashicons-groups',
	);

	register_post_type( 'employee', $args );
}

/**
 * Add custom department taxonomy.
 *
 * Additional custom taxonomies can be defined here
 * http://codex.wordpress.org/Function_Reference/register_taxonomy
 */
function add_custom_department_taxonomy() {
	$labels = array(
		'name'              => _x( 'Departments', 'taxonomy general name', 'mu-profiles' ),
		'singular_name'     => _x( 'Department', 'taxonomy singular name', 'mu-profiles' ),
		'search_items'      => __( 'Search Departments', 'mu-profiles' ),
		'all_items'         => __( 'All Departments', 'mu-profiles' ),
		'parent_item'       => __( 'Parent Department', 'mu-profiles' ),
		'parent_item_colon' => __( 'Parent Department:', 'mu-profiles' ),
		'edit_item'         => __( 'Edit Department', 'mu-profiles' ),
		'update_item'       => __( 'Update Department', 'mu-profiles' ),
		'add_new_item'      => __( 'Add New Department', 'mu-profiles' ),
		'new_item_name'     => __( 'New Department Name', 'mu-profiles' ),
		'menu_name'         => __( 'All Departments', 'mu-profiles' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'directory' ),
		'capabilities'      => array(
			'manage_terms' => 'manage_department',
			'edit_terms'   => 'edit_department',
			'delete_terms' => 'delete_department',
			'assign_terms' => 'assign_department',
		),
	);

	register_taxonomy( 'department', array( 'employee' ), $args );
}

if ( function_exists( 'acf_add_options_sub_page' ) ) {
	acf_add_options_sub_page(
		array(
			'page_title'  => 'Display Settings',
			'menu_title'  => 'Display Settings',
			'menu_slug'   => 'profile-display-settings',
			'capability'  => 'manage_options',
			'parent_slug' => 'edit.php?post_type=employee',
			'redirect'    => false,
		)
	);
}

/**
 * Flush rewrites whenever the plugin is activated.
 */
function mu_profiles_activate() {
	flush_rewrite_rules(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules -- Required once when the plugin is activated.
}
register_activation_hook( __FILE__, 'mu_profiles_activate' );

/**
 * Flush rewrites whenever the plugin is deactivated, also unregister 'employee' post type and 'department' taxonomy.
 */
function mu_profiles_deactivate() {
	unregister_post_type( 'employee' );
	unregister_taxonomy( 'department' );
	flush_rewrite_rules(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules -- Required once when the plugin is deactivated.
}
register_deactivation_hook( __FILE__, 'mu_profiles_deactivate' );

add_filter(
	'get_the_archive_title',
	function ( $title ) {
		if ( is_tax( 'departments' ) ) {
			$title = single_term_title( '', false );
		}
		return $title;
	}
);

/**
 * Rename the department page title from Archives to Profiles.
 *
 * @param string $title The string of the default title.
 * @return string
 */
function mu_profiles_archive_title( $title ) {
	if ( is_tax( 'department' ) ) {
		$title = get_the_archive_title() . ' Department Profiles | ' . get_bloginfo( 'name' );
		return $title;
	}
	return $title;
}
add_filter( 'wpseo_title', 'mu_profiles_archive_title', 9999 );

/**
 * Load Employee Template
 *
 * @param string $template The filename of the template for a single employee.
 * @return string
 */
function load_employee_template( $template ) {
	global $post;

	if ( 'employee' === $post->post_type && locate_template( array( 'single-employee.php' ) ) !== $template ) {
		return plugin_dir_path( __FILE__ ) . 'templates/single-employee.php';
	}

	return $template;
}
add_filter( 'single_template', 'load_employee_template' );

/**
 * Load Department Template
 *
 * @param string $template The filename of the template for the Department taxonomy.
 * @return string
 */
function load_department_template( $template ) {
	global $post;

	if ( is_tax( 'department' ) && locate_template( array( 'taxonomy-department.php' ) ) !== $template ) {
		return plugin_dir_path( __FILE__ ) . 'templates/taxonomy-department.php';
	}

	return $template;
}
add_filter( 'template_include', 'load_department_template' );

/**
 * Format the phone number for consistency
 *
 * @param string $phone The phone number string.
 * @return string
 */
function mu_profiles_format_phone( $phone ) {
	$phone = str_replace( '304.696.', '304-696-', $phone );
	$phone = str_replace( '304.256.', '304-256-', $phone );
	$phone = str_replace( '304.746.', '304-746-', $phone );
	$phone = str_replace( '(304) ', '304-', $phone );
	$phone = str_replace( '(304)-', '304-', $phone );
	$phone = str_replace( '(304)696', '304-696', $phone );
	return $phone;
}
add_action( 'init', 'mu_profiles_employee_post_type' );
add_action( 'init', 'add_custom_department_taxonomy' );

/**
 * Redirect to the department page if the department has a redirect set.
 */
function mu_profiles_redirect_department_page_if_set() {
	if ( is_tax( 'department' ) ) {
		if ( get_field( 'department_redirect_department_page', get_queried_object() ) ) {
			wp_safe_redirect( get_site_url() );
			die();
		}
	}
}
add_action( 'template_redirect', 'mu_profiles_redirect_department_page_if_set' );

/**
 * Proper way to enqueue scripts and styles
 */
function mu_profiles_scripts_and_styles() {
	wp_enqueue_style( 'mu-profiles', plugin_dir_url( __FILE__ ) . 'css/mu-profiles.css', array(), filemtime( plugin_dir_path( __FILE__ ) . 'css/mu-profiles.css' ), 'all' );
}
add_action( 'wp_enqueue_scripts', 'mu_profiles_scripts_and_styles' );

/**
 * Register the employee post type with MU SEO if the plugin is active.
 *
 * @param string[] $post_types Array of post type slugs.
 * @return string[]
 */
function mu_profiles_seo_post_types( $post_types ) {
	$post_types[] = 'employee';
	return $post_types;
}
add_filter( 'mu_seo_post_types', 'mu_profiles_seo_post_types' );

/**
 * Use the employee headshot as the social/OG image for single employee posts.
 *
 * @param int $image_id Attachment ID resolved so far, or 0 if nothing found yet.
 * @param int $post_id  The current post ID.
 * @return int
 */
add_filter(
	'mu_seo_og_image_id',
	function ( $image_id, $post_id ) {
		if ( $image_id || ! is_singular( 'employee' ) ) {
			return $image_id;
		}
		$headshot = get_field( 'employee_headshot', $post_id );
		return $headshot ? absint( $headshot['ID'] ) : $image_id;
	},
	10,
	2
);

/**
 * Set the OG type to 'profile' for single employee posts.
 *
 * @param string $type The default OG type.
 * @return string
 */
add_filter(
	'mu_seo_og_type',
	function ( $type ) {
		if ( is_singular( 'employee' ) ) {
			return 'profile';
		}
		return $type;
	}
);

/**
 * Get versioned CSS/JS files compiled from Vite.
 *
 * @param string $filename The file name to get the manifest.json.
 * @param string $main_dir The main directory to look for the file.
 *
 * @return string
 */
function mu_profiles_vite( $filename, $main_dir = 'source' ) {
	$filename = $main_dir . $filename;

	$mix_data   = file_get_contents( plugin_dir_path( __FILE__ ) . '/public/build/manifest.json' );
	$files_list = json_decode( $mix_data, true );

	if ( array_key_exists( $filename, $files_list ) ) {
		return esc_url( plugin_dir_url( __FILE__ ) . '/public/build/' . $files_list[ $filename ]['file'] );
	}
}
