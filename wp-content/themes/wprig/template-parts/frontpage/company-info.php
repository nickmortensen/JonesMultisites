<?php
/**
 * Template part for displaying the footer info.
 * Last Update 29_April_2021
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

[ 'requested_by' => $request_from ] = $args;
?>
<!-- Show this on the frontpage only, normally it can be a non-prominent part of the footer -->

<section id="aboutjones" class="<?= $request_from; ?>">
	<div class="section-title"><h4><?= esc_html( get_bloginfo( 'description', 'display' ) ); ?> </h4></div>
	<div class="section-content">
		<p><?= ABOUT_US; ?></p>
	</div>
</section>
