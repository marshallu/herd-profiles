<?php
/**
 * Template part for displaying profiles as Row.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package MU Profiles
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
	<div class="marsha-row mup:flex mup:flex-wrap mup:-mx-2 mup:lg:-mx-6 mup:py-6 mup:border-b mup:border-gray-100">
		<div class="columns mup:w-full mup:lg:w-1/6 mup:lg:px-6 mup:mt-6 mup:lg:mt-0">
			<?php if ( get_field( 'employee_headshot' ) ) { ?>
				<img src="<?php echo esc_url( $image['sizes']['medium'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="mup:mx-auto mup:rounded-lg" />
			<?php } ?>
		</div>
		<div class="columns mup:w-full mup:lg:w-5/12 mup:lg:px-6 mup:mt-6 mup:lg:mt-0">
			<?php
			if ( get_field( 'employee_more_info_link' ) ) {
				?>
					<span class="mup:text-lg mup:font-bold"><a href="<?php echo esc_url( get_field( 'employee_more_info_link' ) ); ?>" class="mup:underline mup:hover:no-underline"><?php the_title(); ?></a></span><br>
				<?php
			} else {
				if ( get_field( 'department_hide_link_to_profile', $the_term ) ) {
					?>
						<span class="mup:text-lg mup:font-bold"><?php the_title(); ?></span><br>
					<?php
				} else {
					?>
					<span class="mup:text-lg mup:font-bold"><a href="<?php echo esc_url( get_post_permalink() ); ?>" rel="noopener noreferrer" class="mup:underline mup:hover:no-underline"><?php the_title(); ?></a></span><br>
					<?php
				}
				?>

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
				Telephone: <a href="tel:+1-<?php echo esc_attr( mu_profiles_format_phone( $phone ) ); ?>"><?php echo esc_html( mu_profiles_format_phone( $phone ) ); ?></a><br>
			<?php } ?>

			<?php if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) { ?>
			E-mail: <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
			<?php } ?>

			<?php if ( get_field( 'employee_website' ) ) { ?>
				<a href="<?php echo esc_url( get_field( 'employee_website' ) ); ?>" target="_blank">Website</a>
			<?php } ?>
		</div>

		<div class="columns mup:w-full mup:lg:w-5/12 mup:lg:px-6  mup:mt-6 mup:lg:mt-0">
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
