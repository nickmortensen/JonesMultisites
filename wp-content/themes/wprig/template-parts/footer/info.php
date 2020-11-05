<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<div id="company-info" class="footer_element full-grid">
	<span id="description"><?= esc_html( get_bloginfo( 'description', 'display' ) ); ?> </span>
	<span id="about_us"><?= ABOUT_US; ?></span>
</div><!-- end div#company-info -->
