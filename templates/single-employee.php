<?php
/**
 * Default template for individual profiles with Herd Profiles
 *
 * @package Herd Profiles
 */

use Timber\Timber;

// Advance the WordPress loop so get_field() and template tags work correctly.
if ( have_posts() ) {
	the_post();
}

$context = Timber::context();
$post_id = get_the_ID(); // phpcs:ignore

// Tell HerdPress base.twig not to include no-hero.twig — we handle it ourselves
// in single-employee.twig so it works across all themes (including Marsha).
$context['main_template'] = true;

// --- Basic info ---
$context['employee_title']  = get_the_title();
$context['post_id']         = $post_id;
$context['post_class']      = implode( ' ', get_post_class( '', $post_id ) );
$context['pronouns']        = get_field( 'employee_preferred_pronouns' );
$context['hide_profile']    = get_field( 'hide_profile', 'option' );
$context['position']        = get_field( 'employee_position' );
$context['office_location'] = get_field( 'employee_office_location' );
$context['phone']           = get_field( 'employee_phone_number' )
	? herd_profiles_format_phone( get_field( 'employee_phone_number' ) )
	: '';
$context['website']         = get_field( 'employee_website' );
$context['more_info']       = get_field( 'more_info_link' );
$context['bookings_url']    = get_field( 'employee_bookings_url' );
$context['biography']       = get_field( 'employee_biography' );
$context['facebook']        = get_field( 'employee_facebook' );
$context['twitter']         = get_field( 'employee_twitter' );
$context['linkedin']        = get_field( 'employee_linkedin' );

// --- Email (visibility controlled by option/profile field) ---
$show_email_option  = get_field( 'profile_show_email_address', 'option' );
$show_email_profile = get_field( 'profile_show_email_address', 'profile' );
if ( get_field( 'employee_email_address' ) && ( 'both' === $show_email_option || 'both' === $show_email_profile ) ) {
	$context['email'] = get_field( 'employee_email_address' );
}

// --- Headshot ---
$headshot = get_field( 'employee_headshot' );
if ( $headshot ) {
	$context['headshot'] = array(
		'src'    => $headshot['sizes']['large'],
		'srcset' => wp_get_attachment_image_srcset( $headshot['ID'], 'large' ),
		'alt'    => $headshot['alt'],
	);
}

// --- CV ---
$cv = get_field( 'employee_cvresume' );
if ( $cv ) {
	$context['cv_url'] = $cv['url'];
}

// --- Department links ---
$context['departments_html'] = herd_profiles_department_listing( $post_id, true );

// --- Videos ---
$videos = get_field( 'videos' );
if ( $videos && ! empty( $videos['youtube_videos'] ) ) {
	$context['video_section_title'] = ! empty( $videos['video_section_title'] ) ? $videos['video_section_title'] : '';
	$context['youtube_videos']      = $videos['youtube_videos'];
}

// --- Build toggle sections ---
$has_content_fields = (
	get_field( 'employee_contact_for' ) || get_field( 'employee_education' ) ||
	get_field( 'employee_teaching_philosophy' ) || get_field( 'employee_awards' ) ||
	get_field( 'employee_scholarship' ) || get_field( 'employee_research' ) ||
	get_field( 'employee_conferences' ) || get_field( 'employee_lists' ) ||
	get_field( 'employee_department_service' ) || get_field( 'employee_college_service' ) ||
	get_field( 'employee_university_service' ) || get_field( 'employee_service_profession' ) ||
	get_field( 'employee_service_community' )
);

if ( $has_content_fields ) {
	$toggles = '[mu_toggles]';

	// Helper to build a list of items inside a toggle.
	$list_svg = '<svg class="herd:mr-4 herd:h-6 herd:w-6 herd:fill-green herd:mt-1 herd:lg:mt-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M20 10a10 10 0 1 1-20 0 10 10 0 0 1 20 0zm-2 0a8 8 0 1 0-16 0 8 8 0 0 0 16 0zm-8 2H5V8h5V5l5 5-5 5v-3z"/></svg>';

	$build_list = function ( array $rows, string $field_key ) use ( $list_svg ) {
		$out = '<div role="list" class="herd:flex herd:flex-wrap herd:mx-0 herd:lg:-mx-4 herd:justify-center">';
		foreach ( $rows as $row ) {
			$out .= '<div role="listitem" class="herd:px-0 herd:lg:px-4 herd:w-full herd:lg:w-full herd:flex">';
			$out .= '<div class="herd:w-full herd:flex herd:items-start herd:px-3 herd:my-2">';
			$out .= $list_svg;
			$out .= '<span class="herd:flex-1">' . wp_kses_post( $row[ $field_key ] ?? '' ) . '</span>';
			$out .= '</div></div>';
		}
		$out .= '</div>';
		return $out;
	};

	if ( get_field( 'employee_contact_for' ) ) {
		$list_title = ! empty( get_field( 'profile_row_title', 'option' ) ) ? get_field( 'profile_row_title', 'option' ) : 'Contact For';
		$toggles   .= '[mu_toggle open=true content_class="overflow-x-auto" id="contactfor" title="' . esc_attr( $list_title ) . '"]';
		$toggles   .= $build_list( get_field( 'employee_contact_for' ), 'contact_text' );
		$toggles   .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_education' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" id="education" title="Education Information"]';
		$toggles .= $build_list( get_field( 'employee_education' ), 'education_information' );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_teaching_philosophy' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Teaching Philosophy"]';
		$toggles .= wp_kses_post( get_field( 'employee_teaching_philosophy' ) );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_awards' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Employee Awards and Honors"]';
		$toggles .= $build_list( get_field( 'employee_awards' ), 'award_information' );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_scholarship' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Scholarship"]';
		$toggles .= $build_list( get_field( 'employee_scholarship' ), 'scholarship_information' );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_research' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Contracts, Grants, and Sponsored Research"]';
		$toggles .= $build_list( get_field( 'employee_research' ), 'contracts_grants_and_sponsored_research_item' );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_conferences' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Conference Presentations"]';
		$toggles .= $build_list( get_field( 'employee_conferences' ), 'conference_information' );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_lists' ) ) {
		foreach ( get_field( 'employee_lists' ) as $list ) {
			if ( empty( $list['employee_list_item'] ) ) {
				continue;
			}
			$toggles .= '[mu_toggle content_class="overflow-x-auto" title="' . esc_attr( $list['list_title'] ) . '"]';
			$toggles .= $build_list( $list['employee_list_item'], 'list_item_detail' );
			$toggles .= '[/mu_toggle]';
		}
	}

	if ( get_field( 'employee_department_service' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Service to the Department"]';
		$toggles .= $build_list( get_field( 'employee_department_service' ), 'service_information' );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_college_service' ) ) {
		$out = '<div role="list" class="herd:flex herd:flex-wrap herd:mx-0 herd:lg:-mx-4 herd:justify-center">';
		$svg = '<svg class="herd:mr-4 herd:h-6 herd:w-6 herd:fill-green herd:mt-1 herd:lg:mt-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M20 10a10 10 0 1 1-20 0 10 10 0 0 1 20 0zm-2 0a8 8 0 1 0-16 0 8 8 0 0 0 16 0zm-8 2H5V8h5V5l5 5-5 5v-3z"/></svg>';
		foreach ( get_field( 'employee_college_service' ) as $row ) {
			$text = ! empty( $row['service_information'] ) ? $row['service_information'] : ( $row['service_to_the_college'] ?? '' );
			$out .= '<div role="listitem" class="herd:px-0 herd:lg:px-4 herd:w-full herd:lg:w-full herd:flex"><div class="herd:w-full herd:flex herd:items-start herd:px-3 herd:my-2">' . $svg . '<span class="herd:flex-1">' . wp_kses_post( $text ) . '</span></div></div>';
		}
		$out     .= '</div>';
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Service to the College"]' . $out . '[/mu_toggle]';
	}

	if ( get_field( 'employee_university_service' ) ) {
		$out = '<div role="list" class="herd:flex herd:flex-wrap herd:mx-0 herd:lg:-mx-4 herd:justify-center">';
		$svg = '<svg class="herd:mr-4 herd:h-6 herd:w-6 herd:fill-green herd:mt-1 herd:lg:mt-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M20 10a10 10 0 1 1-20 0 10 10 0 0 1 20 0zm-2 0a8 8 0 1 0-16 0 8 8 0 0 0 16 0zm-8 2H5V8h5V5l5 5-5 5v-3z"/></svg>';
		foreach ( get_field( 'employee_university_service' ) as $row ) {
			$text = ! empty( $row['service_information'] ) ? $row['service_information'] : ( $row['service_to_the_university'] ?? '' );
			$out .= '<div role="listitem" class="herd:px-0 herd:lg:px-4 herd:w-full herd:lg:w-full herd:flex"><div class="herd:w-full herd:flex herd:items-start herd:px-3 herd:my-2">' . $svg . '<span class="herd:flex-1">' . wp_kses_post( $text ) . '</span></div></div>';
		}
		$out     .= '</div>';
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Service to the University"]' . $out . '[/mu_toggle]';
	}

	if ( get_field( 'employee_service_profession' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Service to the Profession"]';
		$toggles .= $build_list( get_field( 'employee_service_profession' ), 'service_to_the_profession' );
		$toggles .= '[/mu_toggle]';
	}

	if ( get_field( 'employee_service_community' ) ) {
		$toggles .= '[mu_toggle content_class="overflow-x-auto" title="Service to the Community"]';
		$toggles .= $build_list( get_field( 'employee_service_community' ), 'service_information' );
		$toggles .= '[/mu_toggle]';
	}

	$toggles                .= '[/mu_toggles]';
	$context['toggles_html'] = do_shortcode( $toggles );
}

Timber::render( 'single-employee.twig', $context );
