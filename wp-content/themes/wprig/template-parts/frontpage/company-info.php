<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<section id="about" class="frontpage">
	<h3 id="slogan"><?= esc_html( get_bloginfo( 'description', 'display' ) ); ?> </h3>
	<span id="about-us"><?= ABOUT_US; ?></span>
</section><!-- end section#company-info -->
