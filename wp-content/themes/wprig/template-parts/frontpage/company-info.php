<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<div id="company-info" class="full-grid">
	<h3 id="slogan"><?= esc_html( get_bloginfo( 'description', 'display' ) ); ?> </h3>
	<span id="about-us"><?= ABOUT_US; ?></span>
</div><!-- end div#company-info -->
