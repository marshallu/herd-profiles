<?php
/**
 * Template part for displaying profiles as Card.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package MU Profiles
 */

?>
<div class="mup:grid mup:grid-cols-1 mup:md:grid-cols-2 mup:lg:grid-cols-3 mup:xl:grid-cols-4 mup:gap-8">
<?php
while ( have_posts() ) {
	the_post();

	$image    = get_field( 'employee_headshot' );
	$position = get_field( 'employee_position' );
	$office   = get_field( 'employee_office_location' );
	$phone    = get_field( 'employee_phone_number' );
	$email    = get_field( 'employee_email_address' );
	?>
	<div class="mup:flex">
		<div class="mup:w-full mup:px-4 mup:py-4">
			<div class="mup:flex mup:flex-col mup:space-y-4">
			<div class="">
				<?php if ( get_field( 'employee_headshot' ) ) { ?>
					<img class="mup:h-full mup:w-full" src="<?php echo esc_url( $image['url'] ); ?>" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
				<?php } ?>
			</div>
			<div>
				<?php
				if ( get_field( 'profile_link_to_profile', 'option' ) ) {
					?>
					<div class="mup:text-xl mup:font-semibold"><a href="<?php echo esc_url( get_post_permalink() ); ?>"><?php the_title(); ?></a></div>
					<?php
				} else {
					?>
					<div class="mup:text-xl mup:font-semibold"><?php the_title(); ?></div>
				<?php } ?>
				<?php if ( get_field( 'employee_position' ) ) { ?>
					<div class="mup:mt-1"><?php echo esc_html( get_field( 'employee_position' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_office_location' ) ) { ?>
					<div class="mup:mt-1">Office: <?php echo esc_html( get_field( 'employee_office_location' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_phone_number' ) ) { ?>
					<div class="mup:mt-1">Phone: <?php echo esc_attr( mu_profiles_format_phone( get_field( 'employee_phone_number' ) ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) { ?>
					<div class="mup:flex mup:items-center mup:my-2"><a href="mailto:<?php echo esc_attr( get_field( 'employee_email_address' ) ); ?>"><?php echo esc_html( get_field( 'employee_email_address' ) ); ?></a></div>
				<?php } ?>

				<?php if ( get_field( 'employee_website' ) ) { ?>
					<div><a href="<?php echo esc_url( get_field( 'employee_website' ) ); ?>">Visit Website</a></div>
				<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
</div>
