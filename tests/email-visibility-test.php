<?php
/**
 * Regression test for the Show Email Address display setting.
 *
 * Run on a WordPress site with ACF and this plugin active:
 * wp eval-file tests/email-visibility-test.php
 *
 * It creates one temporary profile, changes the setting, and restores both.
 *
 * @package herd-profiles
 */

$herd_test_option    = 'options_profile_show_email_address';
$herd_test_reference = '_options_profile_show_email_address';
$herd_test_saved     = array(
	'value'     => get_option( $herd_test_option, null ),
	'reference' => get_option( $herd_test_reference, null ),
);
$herd_test_failures  = 0;
$herd_test_email     = 'regression-test@marshall.edu';
$herd_test_post_id   = wp_insert_post(
	array(
		'post_type'   => 'employee',
		'post_status' => 'publish',
		'post_title'  => 'Email Visibility Test',
	)
);
update_field( 'employee_email_address', $herd_test_email, $herd_test_post_id );

$herd_test_cases = array(
	// Never saved, as on a new site: the field default "both" applies.
	'never saved'              => array( null, null, true, true ),
	// Saved through the settings page, which stores the value and its field reference.
	'saved both'               => array( 'both', 'field_5f5a67199170c', true, true ),
	'saved listing'            => array( 'listing', 'field_5f5a67199170c', true, false ),
	'saved profile'            => array( 'profile', 'field_5f5a67199170c', false, true ),
	'saved none'               => array( 'none', 'field_5f5a67199170c', false, false ),
	// Value stored without its reference, such as by a script: still honored.
	'none without a reference' => array( 'none', null, false, false ),
);

foreach ( $herd_test_cases as $herd_test_name => $herd_test_case ) {
	list( $herd_test_value, $herd_test_ref, $herd_test_listing, $herd_test_profile ) = $herd_test_case;

	delete_option( $herd_test_option );
	delete_option( $herd_test_reference );
	if ( null !== $herd_test_value ) {
		update_option( $herd_test_option, $herd_test_value, false );
	}
	if ( null !== $herd_test_ref ) {
		update_option( $herd_test_reference, $herd_test_ref, false );
	}
	acf_flush_value_cache( 'options', 'profile_show_email_address' );

	$herd_test_html   = do_shortcode( '[herd_profiles ids="' . $herd_test_post_id . '" layout="table"]' );
	$herd_test_actual = array(
		'listing' => false !== strpos( $herd_test_html, 'mailto:' . $herd_test_email ),
		'profile' => herd_profiles_show_email( 'profile' ),
	);
	$herd_test_expect = array(
		'listing' => $herd_test_listing,
		'profile' => $herd_test_profile,
	);

	if ( $herd_test_actual === $herd_test_expect ) {
		WP_CLI::log( "ok   {$herd_test_name}" );
	} else {
		++$herd_test_failures;
		WP_CLI::log( "FAIL {$herd_test_name}: expected " . wp_json_encode( $herd_test_expect ) . ', got ' . wp_json_encode( $herd_test_actual ) );
	}
}

wp_delete_post( $herd_test_post_id, true );
delete_option( $herd_test_option );
delete_option( $herd_test_reference );
if ( null !== $herd_test_saved['value'] ) {
	update_option( $herd_test_option, $herd_test_saved['value'], false );
}
if ( null !== $herd_test_saved['reference'] ) {
	update_option( $herd_test_reference, $herd_test_saved['reference'], false );
}
acf_flush_value_cache( 'options', 'profile_show_email_address' );

if ( $herd_test_failures ) {
	WP_CLI::error( "{$herd_test_failures} email visibility case(s) failed." );
}
WP_CLI::success( 'Email visibility settings behave as expected.' );
