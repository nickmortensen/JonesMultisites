<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $blog_id;

$fp_photos = [ 661, 662 ];

$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}
?>


<footer id="colophon" class="site-footer" style="background-blend-mode: multiply; background: var(--blue-800) center / cover no-repeat url(<?= $bgsrcs[1]; ?>);">
	<?php get_template_part( 'template-parts/footer/info' ); ?>
	<?php get_template_part( 'template-parts/footer/locations' ); ?>
</footer><!-- #colophon -->

</div><!-- #page -->
	<?php wp_footer(); ?>
</body>
</html>
