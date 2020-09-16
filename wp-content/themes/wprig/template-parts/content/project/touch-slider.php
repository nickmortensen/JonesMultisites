<?php
/**
 * Template part for displaying a post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $slides; // we established the variable in the enclosing single-project.php file, but yet it still needs to be established here.
?>

<?php wp_rig()->print_styles( 'flickity' ); ?>
<style>
	figure.flickity {
		min-width:600px;
	}
</style>
<div id="touchscreen_slider" class="project">

<?php
	foreach ( $slides as $identifier => $url ) {
		$caption = '' !== get_the_excerpt( $id ) ? '<figcaption><span class="caption">' . get_the_excerpt( $identifier ) . '</span></figcaption>' : '';
		$options = [
			'data-caption' => $caption,
			'class'        => 'flickity-image',
		];
		echo wp_get_attachment_image( $identifier, 'large', false, $options );
	}
?>

</div>


<script>
	const slider   = document.querySelector( '#touchscreen_slider' );
	const flickity = new Flickity( slider, {
		cellAlign: 'left',
		contain: true,
		freeScroll: true,
		wrapAround: true
	} );
</script>



