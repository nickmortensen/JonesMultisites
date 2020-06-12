<?php
/**
 * Template part for displaying the header branding
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;
$site_icon_svg = wp_get_attachment_image_src( 272 )[0];
?>
<style>
.site-title {
	color: var(--color-theme-white);
	font-size: clamp(1rem, 2.5vw, 2rem);
}
</style>
<div class="site-branding">
	<img width="25vw" src="<?= $site_icon_svg; ?>" alt="">
	<?php the_custom_logo(); ?>

	<?php if ( is_front_page() && is_home() ) :	?>
		<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
	<?php else: ?>

		<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>

	<?php endif; ?>

	<?php
	$wp_rig_description = get_bloginfo( 'description', 'display' );
	if ( $wp_rig_description || is_customize_preview() ) {
		?>
		<p class="site-description">
			<?php echo $wp_rig_description; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped */ ?>
		</p>
		<?php
	}
	?>
</div><!-- .site-branding -->
