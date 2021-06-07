<?php
/**
 * The template for displaying the <footer class="
 * Note that we are closing the 'main' so that it surrounds the footer and then adding all the screipts just above the closing body tag
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<footer class="wide-footer flex-hide-flex">
	<div class="section-title">
		<h4><?= esc_html( get_bloginfo( 'description', 'display' ) ); ?> </h4>
	</div>

	<div class="section-content">
		<span class="about_us"><?= ABOUT_US; ?></span>
	</div><!-- end div#company-info -->
</footer><!-- #colophon -->

<?php wp_footer(); ?>

