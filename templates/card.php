<?php
/**
 * Template part for displaying profiles as Card.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Herd Profiles
 */

?>
<div class="herd-profiles-card">
<?php
while ( have_posts() ) {
	the_post();

	$image    = get_field( 'employee_headshot' );
	$position = get_field( 'employee_position' );
	$office   = get_field( 'employee_office_location' );
	$phone    = get_field( 'employee_phone_number' );
	$email    = get_field( 'employee_email_address' );
	?>
	<div class="herd-profiles-card__item">
		<div class="herd-profiles-card__box">
			<?php
			if ( herd_profiles_link_employee_names( $the_term ) ) {
				?>
				<div class="herd-profiles-card__name"><a href="<?php echo esc_url( get_post_permalink() ); ?>"><?php the_title(); ?></a></div>
				<?php
			} else {
				?>
				<div class="herd-profiles-card__name"><?php the_title(); ?></div>
			<?php } ?>

			<div class="herd-profiles-card__body">
			<div class="herd-profiles-card__photo">
				<?php if ( get_field( 'employee_headshot' ) ) { ?>
					<img class="herd-profiles-card__image" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
				<?php } ?>
			</div>
			<div class="herd-profiles-card__details">
				<?php if ( get_field( 'employee_position' ) ) { ?>
					<div class="herd-profiles-card__position"><?php echo esc_html( get_field( 'employee_position' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_office_location' ) ) { ?>
					<div>Office: <?php echo esc_html( get_field( 'employee_office_location' ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_phone_number' ) ) { ?>
					<div>Phone: <?php echo esc_attr( herd_profiles_format_phone( get_field( 'employee_phone_number' ) ) ); ?></div>
				<?php } ?>

				<?php if ( get_field( 'employee_email_address' ) && herd_profiles_show_email( 'listing' ) ) { ?>
					<div class="herd-profiles-contact-item"><a href="mailto:<?php echo esc_attr( get_field( 'employee_email_address' ) ); ?>"><?php echo esc_html( get_field( 'employee_email_address' ) ); ?></a></div>
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
