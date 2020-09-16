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

?>

			<?php get_template_part( 'template-parts/footer/info' ); ?>
			<footer id="colophon" class="site-footer flex col-nowrap justify-around">

				<div class="location_links flex row-nw justify-around align-start"> <?= wp_rig()->get_location_links(); ?> </div><!-- end div.location_links -->

			</footer><!-- /footer#colophon -->
		</div><!-- #page -->

	<?php wp_footer(); ?>

	</body>
</html>
