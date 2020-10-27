<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<div class="site-info">
	<span>
<?= esc_html( get_bloginfo( 'description', 'display' ) ); ?>
	</span>
</div><!-- .site-info -->

<div class="site-details">
	<span><?= ABOUT_US; ?></span>
</div>
