<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $blog_id;
$fp_photos = [ 661, 662 ];

$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}
$locations = wp_rig()->get_location_ids( 75 );
?>


		<footer
		id="colophon"
		class="site-footer"
		style="
			background: center / cover no-repeat url(<?= $bgsrcs[1]; ?>), linear-gradient( var(--blue-900) 20%, var(--gray-900) 60%);
			background-blend-mode: multiply;">

			<div id="explainer">
				<h2>Jones Sign Company</h2>
				<h3><?= count( $locations ); ?> Locations across the United States</h3>
			</div>

			<div id="location-map">
				<?php get_template_part( 'template-parts/footer/location', 'map' ); ?>
			</div><!-- /#location-map -->

			<div id="location-address">
				</div><!-- /#location-address -->
			<?php get_template_part( 'template-parts/footer/location', 'address' ); ?>

		</footer><!-- #colophon -->

<?php wp_footer(); ?>
	</div><!-- div#page-->

<script>
	const show = element => element.style.display = 'flex';
	const hide = element => element.style.display = 'none';

// new SelectAlternative( selectToAlter, options );

function showHideLocationInfo(e) {
	const slug = e.target.dataset.branchMarker;

	const addressDivs = document.querySelectorAll( `.single_jones_address` );
	addressDivs.forEach( location => {
		if ( location.dataset.slug === slug && ! location.classList.contains( 'address-hidden') ) {
			return;
		}

		if ( location.dataset.slug !== slug && ! location.classList.contains( 'address-hidden') ) {
			location.classList.toggle( 'address-hidden' );
		}

		if ( location.dataset.slug === slug && location.classList.contains( 'address-hidden') ) {
			location.classList.toggle( 'address-hidden' );
		}
	})

}
const mapMarkers = [...document.querySelectorAll( '.map-markers > li' ) ];

mapMarkers.forEach( marker => {
	marker.addEventListener( 'mouseover', showHideLocationInfo )
} );

</script>



</body>
</html>
