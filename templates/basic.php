<?php
/**
 * Template part for displaying profiles as Basic.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Herd Profiles
 */

if ( is_page_template( array( 'page-full-width.php', 'page-full-width-hero.php', 'page-secondary-nav.php', 'page-secondary-classic.php', 'page-experience.php' ) ) ) {
	$width = ' herd:lg:w-1/3 ';
} else {
	$width = ' herd:lg:w-1/2 ';
}
?>
<div class="">
	<!-- <h2 class="herd:text-3xl herd:font-extrabold herd:tracking-tight herd:sm:text-4xl">Meet our leadership</h2> -->
	<div class="herd:flex herd:flex-wrap herd:lg:-mx-6">
		<?php
		while ( have_posts() ) :
			the_post();
			$image = get_field( 'employee_headshot' );
			?>
			<div class="herd:w-full <?php echo esc_attr( $width ); ?> herd:lg:px-6 herd:mb-8">
				<div class="herd:flex herd:flex-wrap herd:flex-row herd:lg:-mx-2">
					<div class="herd:w-full herd:lg:w-1/4 herd:lg:px-2">
						<img class="herd:object-cover herd:rounded-lg" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
					</div>
					<div class="herd:w-full herd:lg:w-3/4 herd:lg:px-2 herd:mt-4 herd:lg:mt-0">
						<div class="herd:text-lg herd:font-semibold herd:space-y-1">
						<div><?php echo esc_html( get_the_title() ); ?></div>
						<p class="herd:text-gray-500"><?php echo esc_html( get_field( 'employee_position' ) ); ?></p>
						</div>
						<div class="herd:text-lg herd:mt-1">
						<p class="herd:text-gray-500"><?php echo esc_html( get_field( 'employee_biography' ) ); ?></p>
						</div>
					</div>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
</div>
