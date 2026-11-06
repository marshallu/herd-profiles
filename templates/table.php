<?php
/**
 * Template part for displaying profiles as HTML Table.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Herd Profiles
 */

?>
<div class="large-table">
	<table class="table table-striped table-bordered herd:w-full">
		<thead>
			<tr class="">
				<th>Name</th>
				<th>Title</th>
				<th>Office</th>
				<th>Phone</th>
				<?php
				if ( herd_profiles_show_email( 'listing' ) ) {
					?>
					<th>Email</th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<tr class="">
					<td class="herd:text-gray-900">
						<?php
						if ( ! herd_profiles_link_employee_names( $the_term ) ) {
							?>
								<?php echo esc_html( get_the_title() ); ?>
							<?php
						} else {
							?>
								<a href="<?php echo esc_url( get_post_permalink() ); ?>" rel="noopener noreferrer"><?php echo esc_html( get_the_title() ); ?></a>
						<?php } ?>
					</td>
					<td class="herd:text-gray-900"><?php echo esc_html( get_field( 'employee_position' ) ); ?></td>
					<td class="herd:text-gray-900"><?php echo esc_html( get_field( 'employee_office_location' ) ); ?></td>
					<td class="herd:text-gray-900"><?php echo esc_html( herd_profiles_format_phone( get_field( 'employee_phone_number' ) ) ); ?></td>
					<?php
					if ( herd_profiles_show_email( 'listing' ) ) {
						?>
						<td class=""><a href="mailto:<?php echo esc_attr( get_field( 'employee_email_address' ) ); ?>" rel="noopener noreferrer"><?php echo esc_html( get_field( 'employee_email_address' ) ); ?></a></td>
					<?php } ?>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
</div>
