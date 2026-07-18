<?php
/**
 * Template part for displaying profiles as Basic.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package MU Profiles
 */

if ( is_page_template( array( 'page-full-width.php', 'page-full-width-hero.php', 'page-secondary-nav.php', 'page-secondary-classic.php', 'page-experience.php' ) ) ) {
	$width = ' mup:lg:w-1/3 ';
} else {
	$width = ' mup:lg:w-1/2 ';
}
?>
<div class="">
	<!-- <h2 class="mup:text-3xl mup:font-extrabold mup:tracking-tight mup:sm:text-4xl">Meet our leadership</h2> -->
	<div class="mup:flex mup:flex-wrap mup:lg:-mx-6">
		<?php
		while ( have_posts() ) :
			the_post();
			$image = get_field( 'employee_headshot' );
			?>
			<div class="mup:w-full <?php echo esc_attr( $width ); ?> mup:lg:px-6 mup:mb-8">
				<div class="mup:flex mup:flex-wrap mup:flex-row mup:lg:-mx-2">
					<div class="mup:w-full mup:lg:w-1/4 mup:lg:px-2">
						<img class="mup:object-cover mup:rounded-lg" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
					</div>
					<div class="mup:w-full mup:lg:w-3/4 mup:lg:px-2 mup:mt-4 mup:lg:mt-0">
						<div class="mup:text-lg mup:font-semibold mup:space-y-1">
						<div><?php echo esc_html( get_the_title() ); ?></div>
						<p class="mup:text-gray-500"><?php echo esc_html( get_field( 'employee_position' ) ); ?></p>
						</div>
						<div class="mup:text-lg mup:mt-1">
						<p class="mup:text-gray-500"><?php echo esc_html( get_field( 'employee_biography' ) ); ?></p>
						</div>
					</div>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
</div>
