<?php
/**
 * Template part for displaying images that are related to this taxonomy in a gallery form;
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;
?>


<div class="section-title">
	<h3><?php the_archive_title(); ?> Gallery</h3>
</div>

<div class="section-content related-images-gallery">

<?php
$related_images = wp_rig()->get_related( 'attachment' );

foreach ( $related_images  as $image_id ) {
	[ $url, $width, $height ] = wp_get_attachment_image_src( $image_id, 'medium' );
	$ratio                    = intdiv( $width, $height );
	$ratio                    = $width / $height;
	$orientation              = $ratio > 1 ? 'horizontal' : 'vertical';
if ( 'vertical' === $orientation ) {
	continue;
}
	$output = <<<IMAGE
		<img data-id="$image_id" src="$url" width="25%" data-width="$width" data-height="$height" data-orientation="$orientation" />

IMAGE;
	// $output                   = '<img width="' . $width . 'px" data-orientation="' . $orientation . '" src="';
	// $output                  .= $url;
	// $output                  .= '" />';


	echo $output;

}

?>

</div>


<?php


