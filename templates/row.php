<?php
/**
 * Template part for displaying profiles as Row.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Herd Profiles
 */

while ( have_posts() ) {
	the_post();

	$image       = get_field( 'employee_headshot' );
	$position    = get_field( 'employee_position' );
	$office      = get_field( 'employee_office_location' );
	$phone       = get_field( 'employee_phone_number' );
	$email       = get_field( 'employee_email_address' );
	$contact_for = get_field( 'employee_contact_for' );
	?>
	<div class="marsha-row herd-profiles-row">
		<div class="columns herd-profiles-row__photo">
			<?php if ( get_field( 'employee_headshot' ) ) { ?>
				<img src="<?php echo esc_url( $image['sizes']['medium'] ?? $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="herd-profiles-row__image herd-profiles-row__image--centered" />
			<?php } ?>
		</div>
		<div class="columns herd-profiles-row__main">
			<?php
			if ( ! herd_profiles_link_employee_names( $the_term ) ) {
				?>
					<span class="herd-profiles-row__name"><?php the_title(); ?></span><br>
				<?php
			} elseif ( get_field( 'employee_more_info_link' ) ) {
				?>
					<span class="herd-profiles-row__name"><a href="<?php echo esc_url( get_field( 'employee_more_info_link' ) ); ?>" class="herd-profiles-row__name-link"><?php the_title(); ?></a></span><br>
				<?php
			} else {
				?>
				<span class="herd-profiles-row__name"><a href="<?php echo esc_url( get_post_permalink() ); ?>" rel="noopener noreferrer" class="herd-profiles-row__name-link"><?php the_title(); ?></a></span><br>
				<?php
			}

			if ( get_field( 'employee_preferred_pronouns' ) ) {
				?>
				Preferred Pronouns: <?php echo esc_html( get_field( 'employee_preferred_pronouns' ) ); ?><br>
				<?php
			}

			echo esc_html( $position );
			?>
			<br>

			<?php if ( get_field( 'employee_office_location' ) ) { ?>
				Location: <?php echo esc_html( $office ); ?><br>
			<?php } ?>

			<?php if ( get_field( 'employee_phone_number' ) ) { ?>
				Telephone: <a href="tel:+1-<?php echo esc_attr( herd_profiles_format_phone( $phone ) ); ?>"><?php echo esc_html( herd_profiles_format_phone( $phone ) ); ?></a><br>
			<?php } ?>

			<?php if ( get_field( 'employee_email_address' ) && herd_profiles_show_email( 'listing' ) ) { ?>
			E-mail: <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
			<?php } ?>

			<?php if ( get_field( 'employee_website' ) ) { ?>
				<a href="<?php echo esc_url( get_field( 'employee_website' ) ); ?>" target="_blank">Website</a>
			<?php } ?>
		</div>

		<div class="columns herd-profiles-row__aside">
		<?php
		if ( $contact_for ) {
			if ( ! empty( get_field( 'profile_row_title', 'option' ) ) ) {
				$row_title = get_field( 'profile_row_title', 'option' );
			} else {
				$row_title = 'Contact ' . get_the_title() . ' for:';
			}
			?>
			<strong><?php echo esc_html( $row_title ); ?></strong>
			<ul>
				<?php
				foreach ( $contact_for as $item ) {
					?>
					<li><?php echo esc_html( $item['contact_text'] ); ?></li>
					<?php
				}
				?>
			</ul>
		<?php } ?>
		</div>
	</div>
<?php } ?>
