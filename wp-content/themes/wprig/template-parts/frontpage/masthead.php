<?php
/**
 * Template part for displaying the frontpage masthead for Jonessign.com
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$image_id       = 703;
$sizes          = [ 'full', 'medium_large', 'large', 'wp-rig-featured' ];
$sourceset      = wp_get_attachment_image_srcset( $image_id, $sizes[1] );
$masthead_image = wp_get_attachment_image_src( $image_id, $sizes[1] );

?>

<style>
section#masthead.frontpage {
	width: 100%;
	background-color: #ffc600;
	height: clamp(800px, 20vh, 900px);
	background-image: url(<?= $masthead_image[0]; ?>);
	background-size: cover;
	/* background-position: 25% -50%; */
	background-clip: padding-box;

}

</style>


<section id="masthead" class="frontpage">


</section><!-- end section#cmasthead -->
