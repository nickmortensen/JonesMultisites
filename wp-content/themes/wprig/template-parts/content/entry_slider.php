<?php
/**
 * Template part for displaying a project's slideshow -- using flickity.js + jQuery
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $slideshow_images; // we established the variable in the enclosing single-project.php file, but yet it still needs to be established here.

?>

<section class="project-touch-slider">
	<?php
		foreach ( $slideshow_images as $slide_id => $url ) {
			$caption = '' !== get_the_excerpt( $id ) ? '<figcaption><span class="caption">' . get_the_excerpt( $id ) . '</span></figcaption>' : '';
			$options = [
				'data-caption' => $caption,
				'class'        => 'flickity-image',
			];
			$slide_image_size = 'medium'; // 'large', 'wp-rig-featured', 'medium' are the better options here.
			echo wp_get_attachment_image( $slide_id, 'large', false, $options );
		}

	?>
</section><!-- end section.project-touch-slider -->

<script>
	const slider = document.querySelector( '.project-touch-slider' );
	const options = {
		cellAlign: 'left',
		contain: true,
		freeScroll: true,
		wrapAround: true,
	};
	const flickity = new Flickity( slider, options );
</script>
