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

<footer data-gridarea="footer">

	<div data-gridarea="footerbottom" class="copyright">
		<div>
			<span style="width: 40px;"><?= wp_rig()->get_jones_icon(); ?></span>
			<span> <?= wp_rig()->get_copyright_notice(); ?> </span>
		</div>
	</div>


</footer>


<?php wp_footer(); ?>


</body>
</html>
