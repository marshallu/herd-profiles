<?php
/**
 * Template part for displaying profiles as Full Profile
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
		<div class="columns herd:w-full herd:lg:w-1/4 herd:lg:px-6 herd:mt-6 herd:lg:mt-0">
			<?php if ( get_field( 'employee_headshot' ) ) { ?>
				<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" class="herd:rounded-lg" />
			<?php } ?>
		</div>
		<div class="columns herd:w-full herd:lg:w-3/4 herd:lg:px-6 herd:mt-6 herd:lg:mt-0 entry-content">
		<?php
		if ( get_field( 'department_hide_link_to_profile', $the_term ) ) {
			?>
			<div class="herd:text-xl herd:font-semibold herd:uppercase"><?php the_title(); ?></a></div>
			<?php
		} else {
			?>
			<div class="herd:text-xl herd:font-semibold herd:uppercase"><a href="<?php echo esc_url( get_post_permalink() ); ?>"><?php the_title(); ?></a></div>
			<?php
		}
		?>
			<div class="herd:mt-3 herd:mb-4">
				<?php if ( get_field( 'employee_position' ) ) { ?>
					<div class="herd:font-semibold"><?php echo esc_html( get_field( 'employee_position' ) ); ?></div>
				<?php } ?>

				<?php herd_profiles_department_listing( $post->ID ); ?>

			</div>

			<?php if ( get_field( 'employee_office_location' ) ) { ?>
					<div class="herd:flex herd:items-center herd:my-2">
						<svg class="herd:text-gray-200 herd:fill-current herd:h-5 herd:w-5 herd:mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0zM192 272c44.183 0 80-35.817 80-80s-35.817-80-80-80-80 35.817-80 80 35.817 80 80 80z"></path></svg>
						<?php echo esc_html( get_field( 'employee_office_location' ) ); ?>
					</div>
			<?php } ?>

			<?php if ( get_field( 'employee_phone_number' ) ) { ?>
					<div class="herd:flex herd:items-center herd:my-2">
						<svg class="herd:text-gray-200 herd:fill-current herd:h-5 herd:w-5 herd:mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg>
						<?php echo esc_attr( herd_profiles_format_phone( get_field( 'employee_phone_number' ) ) ); ?>
					</div>
			<?php } ?>

			<?php if ( get_field( 'employee_email_address' ) && ( 'both' === get_field( 'profile_show_email_address', 'option' ) || 'listing' === get_field( 'profile_show_email_address', 'option' ) ) ) { ?>
					<div class="herd:flex herd:items-center herd:my-2">
						<svg class="herd:text-gray-200 herd:fill-current herd:h-5 herd:w-5 herd:mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path></svg>
						<a href="mailto:<?php echo esc_attr( get_field( 'employee_email_address' ) ); ?>"><?php echo esc_html( get_field( 'employee_email_address' ) ); ?></a>
					</div>
			<?php } ?>
			<div class="herd:mt-6">
				<?php echo get_field( 'employee_biography' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
<?php } ?>
