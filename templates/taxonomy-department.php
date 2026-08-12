<?php
/**
 * Default template for displaying Department listings for Herd Profiles
 *
 * @package Herd Profiles
 */

use Timber\Timber;

$context  = Timber::context();
$the_term = get_queried_object();

// Tell herdpress base.twig that this template handles no-hero itself.
$context['main_template'] = true;

// Page title.
if ( get_field( 'department_custom_title', $the_term ) ) {
	$context['page_title'] = get_field( 'department_custom_title', $the_term );
} else {
	$context['page_title'] = get_the_archive_title();
}

// Archive description (returns HTML string).
$context['archive_description'] = get_the_archive_description();

// Determine which listing sub-template to use.
$dept_listing   = get_field( 'department_listing_display', $the_term );
$global_listing = get_field( 'profile_listing_display', 'option' );

$layout_map = array(
	'row'          => 'row.php',
	'enhanced'     => 'enhanced.php',
	'table'        => 'table.php',
	'full-profile' => 'full-profile.php',
	'card'         => 'card.php',
	'basic'        => 'basic.php',
	'grid'         => 'grid.php',
	'icon-card'    => 'icon-card.php',
);

if ( isset( $layout_map[ $dept_listing ] ) ) {
	$template_file = $layout_map[ $dept_listing ];
} elseif ( isset( $layout_map[ $global_listing ] ) ) {
	$template_file = $layout_map[ $global_listing ];
} else {
	$template_file = 'table.php';
}

// Capture the listing HTML from the legacy PHP sub-template.
if ( have_posts() ) {
	ob_start();
	include plugin_dir_path( __FILE__ ) . $template_file;
	$context['listing_html'] = ob_get_clean();
}

Timber::render( 'taxonomy-department.twig', $context );
