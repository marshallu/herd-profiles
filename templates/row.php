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
	<div class="marsha-row herd:flex herd:flex-wrap herd:-mx-2 herd:lg:-mx-6 herd:py-6 herd:border-b herd:border-gray-100">
		<div class="columns herd:w-full herd:lg:w-1/6 herd:lg:px-6 herd:mt-6 herd:lg:mt-0">
			<?php if ( get_field( 'employee_headshot' ) ) { ?>
				<img src="<?php echo esc_url( $image['sizes']['medium'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="herd:mx-auto herd:rounded-lg" />
			<?php } ?>
		</div>
		<div class="columns herd:w-full herd:lg:w-5/12 herd:lg:px-6 herd:mt-6 herd:lg:mt-0">
			<?php
			if ( get_field( 'employee_more_info_link' ) ) {
				?>
					<span class="herd:text-lg herd:font-bold"><a href="<?php echo esc_url( get_field( 'employee_more_info_link' ) ); ?>" class="herd:underline herd:hover:no-underline"><?php the_title(); ?></a></span><br>
				<?php
			} else {
				if ( get_field( 'department_hide_link_to_profile', $the_term ) ) {
					?>
						<span class="herd:text-lg herd:font-bold"><?php the_title(); ?></span><br>
					<?php
				} else {
					?>
					<span class="herd:text-lg herd:font-bold"><a href="<?php echo esc_url( get_post_permalink() ); ?>" rel="noopener noreferrer" class="herd:underline herd:hover:no-underline"><?php the_title(); ?></a></span><br>
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
				Telephone: <a href="tel:+1-<?php echo esc_attr( herd_profiles_format_phone( $phone ) ); ?>"><?php echo esc_html( herd_profiles_format_phone( $phone ) ); ?></a><br>
			<?php } ?>

			<?php if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) { ?>
			E-mail: <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a>
			<?php } ?>

			<?php if ( get_field( 'employee_website' ) ) { ?>
				<a href="<?php echo esc_url( get_field( 'employee_website' ) ); ?>" target="_blank">Website</a>
			<?php } ?>
		</div>

		<div class="columns herd:w-full herd:lg:w-5/12 herd:lg:px-6  herd:mt-6 herd:lg:mt-0">
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
