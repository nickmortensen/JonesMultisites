<?php
/**
 * Template part for displaying projects that are related to this taxonomy;
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>



<?php

$related_images = wp_rig()->get_related( 'attachment' );
foreach ( $related_images  as $image ) {
	[ $url, $width, $height ] = wp_get_attachment_image_src( $image, 'medium' );
	$ratio = intdiv( $width, $height );
	$ratio = $width / $height;
	$output = '<img src="';
	$output .= $url;
	$output .= '" />';
	$orientation = $ratio ? ' oriented horizonally' : ' oriented vertially';
	$orientation = $ratio > 1 ? ' oriented horizonally' : ' oriented vertially';
	$output .= '<span>' . $ratio . ' ' . $orientation .'</span>';


	// echo $output;

}

$projects = wp_rig()->get_all_posttype( 'project' )->posts;
print_pre( $projects );
?>


