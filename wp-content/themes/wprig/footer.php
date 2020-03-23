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
	<footer id="colophon" class="site-footer border-t-2 border-gray-400 relative bottom-0 flex flex-row flex-no-wrap justify-around">
		<?= wp_rig()->get_location_links(); ?>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
