<?php
/**
 * Template part for displaying profiles as Card.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Herd Profiles
 */

?>
<div class="herd:flex herd:flex-wrap herd:mx-0 herd:lg:-mx-4">
<?php
while ( have_posts() ) {
	the_post();

	$image    = get_field( 'employee_headshot' );
	$position = get_field( 'employee_position' );
	$office   = get_field( 'employee_office_location' );
	$phone    = get_field( 'employee_phone_number' );
	$email    = get_field( 'employee_email_address' );
	?>
	<div class="herd:w-full herd:lg:w-1/3 herd:px-0 herd:lg:px-4 herd:mb-4 herd:lg:mb-8 herd:flex herd:flex-row">
		<div class="herd:w-full herd:bg-gray-100 herd:border herd:border-gray-200 herd:px-4 herd:py-4">
			<?php
			if ( get_field( 'profile_link_to_profile', 'option' ) ) {
				?>
				<div class="herd:text-xl herd:font-semibold"><a href="<?php echo esc_url( get_post_permalink() ); ?>"><?php the_title(); ?></a></div>
				<?php
			} else {
				?>
				<div class="herd:text-xl herd:font-semibold"><?php the_title(); ?></div>
			<?php } ?>

			<div class="herd:pt-3 herd:flex herd:space-x-4">
			<div class="herd:w-1/3">
				<?php if ( get_field( 'employee_headshot' ) ) { ?>
					<img class="herd:rounded-lg" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
				<?php } ?>
			</div>
			<div class="herd:w-2/3">
				<?php if ( get_field( 'employee_position' ) ) { ?>
					<div class="herd:font-semibold"><?php echo esc_html( get_field( 'employee_position' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_office_location' ) ) { ?>
					<div>Office: <?php echo esc_html( get_field( 'employee_office_location' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_phone_number' ) ) { ?>
					<div>Phone: <?php echo esc_attr( herd_profiles_format_phone( get_field( 'employee_phone_number' ) ) ); ?></div>
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
