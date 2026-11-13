<?php
/**
 * Template part for displaying profiles as Basic.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Herd Profiles
 */

if ( is_page_template( array( 'page-full-width.php', 'page-full-width-hero.php', 'page-secondary-nav.php', 'page-secondary-classic.php', 'page-experience.php' ) ) ) {
	$width = 'herd-profiles-basic__item--third';
} else {
	$width = '';
}
?>
<div class="herd-profiles-basic">
	<div class="herd-profiles-basic__list">
		<?php
		while ( have_posts() ) :
			the_post();
			$image = get_field( 'employee_headshot' );
			?>
			<div class="herd-profiles-basic__item <?php echo esc_attr( $width ); ?>">
				<div class="herd-profiles-basic__inner">
					<div class="herd-profiles-basic__photo">
						<?php if ( $image ) { ?>
							<img class="herd-profiles-basic__image" src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
						<?php } ?>
					</div>
					<div class="herd-profiles-basic__body">
						<div class="herd-profiles-basic__heading">
						<div class="herd-profiles-basic__name"><?php echo esc_html( get_the_title() ); ?></div>
						<p class="herd-profiles-basic__position"><?php echo esc_html( get_field( 'employee_position' ) ); ?></p>
						</div>
						<div class="herd-profiles-basic__bio">
						<p class="herd-profiles-basic__bio-text"><?php echo esc_html( get_field( 'employee_biography' ) ); ?></p>
						</div>
					</div>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
</div>
