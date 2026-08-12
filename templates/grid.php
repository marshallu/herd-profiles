<?php
/**
 * Template part for displaying profiles as Card.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Herd Profiles
 */

?>
<div class="herd:grid herd:grid-cols-1 herd:md:grid-cols-2 herd:lg:grid-cols-3 herd:xl:grid-cols-4 herd:gap-8">
<?php
while ( have_posts() ) {
	the_post();

	$image    = get_field( 'employee_headshot' );
	$position = get_field( 'employee_position' );
	$office   = get_field( 'employee_office_location' );
	$phone    = get_field( 'employee_phone_number' );
	$email    = get_field( 'employee_email_address' );
	?>
	<div class="herd:flex">
		<div class="herd:w-full herd:px-4 herd:py-4">
			<div class="herd:flex herd:flex-col herd:space-y-4">
			<div class="">
				<?php if ( get_field( 'employee_headshot' ) ) { ?>
					<img class="herd:h-full herd:w-full" src="<?php echo esc_url( $image['url'] ); ?>" srcset="<?php echo esc_attr( wp_get_attachment_image_srcset( $image['ID'], 'large' ) ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
				<?php } ?>
			</div>
			<div>
				<?php
				if ( get_field( 'profile_link_to_profile', 'option' ) ) {
					?>
					<div class="herd:text-xl herd:font-semibold"><a href="<?php echo esc_url( get_post_permalink() ); ?>"><?php the_title(); ?></a></div>
					<?php
				} else {
					?>
					<div class="herd:text-xl herd:font-semibold"><?php the_title(); ?></div>
				<?php } ?>
				<?php if ( get_field( 'employee_position' ) ) { ?>
					<div class="herd:mt-1"><?php echo esc_html( get_field( 'employee_position' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_office_location' ) ) { ?>
					<div class="herd:mt-1">Office: <?php echo esc_html( get_field( 'employee_office_location' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_phone_number' ) ) { ?>
					<div class="herd:mt-1">Phone: <?php echo esc_attr( herd_profiles_format_phone( get_field( 'employee_phone_number' ) ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) { ?>
					<div class="herd:flex herd:items-center herd:my-2"><a href="mailto:<?php echo esc_attr( get_field( 'employee_email_address' ) ); ?>"><?php echo esc_html( get_field( 'employee_email_address' ) ); ?></a></div>
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
