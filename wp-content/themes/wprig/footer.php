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
global $locations;
$fp_photos = [ 661, 662 ];

$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}
?>


		<footer
		id="colophon"
		class="site-footer"
		style="
			background: center / cover no-repeat url(<?= $bgsrcs[1]; ?>),
			linear-gradient( var(--blue-900) 20%, var(--gray-900) 60%);
			background-blend-mode: multiply;">

			<div id="location-select">
				<?php get_template_part( 'template-parts/footer/location', 'select' ); ?>
			</div><!-- /#location-select -->

			<div id="location-map">
				<?php get_template_part( 'template-parts/footer/location', 'map' ); ?>
			</div><!-- /#location-map -->

			<div id="location-address">
				</div><!-- /#location-address -->
			<?php get_template_part( 'template-parts/footer/location', 'address' ); ?>

		</footer><!-- #colophon -->

	</div><!-- div#page-->
<?php wp_footer(); ?>

<script>
	const show = element => element.style.display = 'flex';
	const hide = element => element.style.display = 'none';
	// target the map markers
	const locationMap    = document.querySelector('.map');
	const markerInfoDivs = document.querySelectorAll( '.inner' );



	// document.querySelector( '#location_addresses' ).innerHTML = eachAddress.join('');
	let branches = document.querySelectorAll( 'div.single_jones_address' );


branches.forEach( branch => {
	hide( branch );
	if ( 'nat' === branch.dataset.slug ) show( branch );
});

const selectToAlter  = document.querySelector( '.location-select-element' );
// selectToAlter.classList.add( 'skin-boxes')
const options = {
	onChange( val ) {
		console.log( 'value is ', val );
		const markerInfoDivs = document.querySelectorAll( '.inner' );
		let branches         = document.querySelectorAll( '.single_jones_address' );
		let infoBox           = document.querySelector( `[data-location-info="${val}"]` );
		let target           = document.querySelector('[data-slug="grb"]');
		branches.forEach( branch => {
			hide( branch );
			show(infoBox);
			if ( branch.dataset.slug === val ) {
				show( branch );
			}
		})
		markerInfoDivs.forEach( location => {
			let slug = location.dataset.locationInfo;
			if (slug !== val && ! location.classList.contains( 'hidden' ) ) {
				location.classList.add( 'hidden' );
			}
			if ( slug === val && location.classList.contains( 'hidden' ) ) {
				location.classList.remove( 'hidden' );
			}
		})
	}
};

new SelectAlternative( selectToAlter, options );

</script>



</body>
</html>
