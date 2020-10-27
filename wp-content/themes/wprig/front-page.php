<?php
/**
 * Render your site front page, whether the front page displays the blog posts index or a static page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();
?>


<?php
wp_rig()->print_styles( 'wp-rig-project', 'wp-rig-front-page', 'wp-rig-content', 'wp-rig-flickity' );
$fp_photos = [ 661, 662 ];

$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}
?>
<section class="frontpage_masthead"
style="min-height: 35vw; background-blend-mode: darken; background: var(--blue-600) center / cover no-repeat url(<?= $bgsrcs[1]; ?>);">

</section><!--frontpage_masthead -->


	<!-- project cards -->
	<section id="fp-projects" class="frontpage-projects">
		<h2 class="section-title">Recent Projects</h2>
		<?= wp_rig()->get_drag_svg(); ?>
		<div id="frontpage-draggable-slide-container" style="width: 100%;">
			<?php
				$project_ids = wp_rig()->get_recent_project_ids( 8 );

				foreach ( $project_ids as $project ) {
					echo wp_rig()->get_project_card( $project );
				}
			?>
		</div><!-- end div#fp-project-cards -->
	</section>

	<section>

<!-- <label for="jones-location-select">Choose a Location</label>
<select name="jones-location-select" id="jones-location-select">
	<option value="">--plaease pick a location</option>
</select> -->

<?php
$location_ids = wp_rig()->get_location_ids( [75] );
$location_data = [];
?>

<style>
	.hideThisDiv {
		display: none;
	}
	</style>

<label for="jones-location-select">Choose a Location</label>
<select id="location-select" name="jones-location-select" id="jones-location-select">
	<?php
		foreach ( $location_ids as $location ) {
			$location_data[] = wp_rig()->get_location_info( $location );
			echo wp_rig()->get_location_option( $location );
		}
	?>
</select>

<section>
<?php

foreach ( $location_ids as $location ) {
	$location_data[] = wp_rig()->get_location_info( $location );
	echo wp_rig()->get_single_location_details( $location );
}
?>

</section>

<script>

	const locationAddressDivs   = document.querySelectorAll( '.location_details' );
	const locationSelectOptions = document.getElementById( 'location-select' );
	let selected = locationSelectOptions.selectedIndex;
	locationAddressDivs.forEach(item => {
		const divSlug = item.dataset.locationSlug;
		if ( locationSelectOptions[selected].value !== divSlug ) {
			item.classList.add('hideThisDiv');
		}
	})
function changeDiv( element ) {
	let slug = element.target.value;

	locationAddressDivs.forEach(item => {
		const divSlug = item.dataset.locationSlug;
		if ( slug === divSlug ) {
			item.classList.remove('hideThisDiv');
		}
		if ( slug !== divSlug && ! item.classList.contains( 'hideThisDiv' ) ) {
			item.classList.toggle('hideThisDiv');;
		}
	})
}
	locationSelectOptions.addEventListener( 'change', changeDiv)
	// locationAddressDivs.forEach(item => console.log( item.dataset.locationSlug ) );

</script>

	</section>

	<script>
	const fpProject = document.querySelector( '#frontpage-draggable-slide-container' );
	const optional = {
		cellAlign: 'center',
		groupCells: true,
		contain: true,
		freeScroll: true,
		wrapAround: true,
		adaptiveHeight: false,
		arrowShape: 'M 0,50 L 60,00 L 50,30 L 80,30 L 80,70 L 50,70 L 60,100 Z',
	};
	const flickity = new Flickity( fpProject, optional );
</script>

<?php


get_footer();
