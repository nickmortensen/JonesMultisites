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
class="full-grid"
style="
	background: linear-gradient( var(--blue-900) 20%, var(--gray-900) 60%), center / cover no-repeat url(<?= $bgsrcs[1]; ?>) ;
	background-blend-mode: multiply;">

<?php get_template_part( 'template-parts/footer/info' ); ?>
<?php get_template_part( 'template-parts/footer/locations' ); ?>
<?php get_template_part( 'template-parts/footer/locations_map' ); ?>
</footer><!-- #colophon -->

<?php wp_footer(); ?>
</div><!--all_elements-->


<script>

const show = element => element.style.display = 'block';
const hide = element => element.style.display = 'none';
// target the map markers
const locationMap = document.querySelector('.map');

const markerInfoDivs = document.querySelectorAll( '.map-marker-info-inner' );



document.querySelector('.branch-select').classList.toggle( 'preferred-border' );
// document.querySelector( '#location_addresses' ).innerHTML = eachAddress.join('');
let branches = document.querySelectorAll( 'div.single_jones_address' );


branches.forEach( branch => {
	hide( branch );
	if ( 'nat' === branch.dataset.slug ) show( branch );
});

	(function() {
		new SelectFx(
		// new SelectAlternative(
			document.querySelector( '.branch-select'), {
				onChange: function( val ) {
					console.log( 'value is ', val );
					const markerInfoDivs = document.querySelectorAll( '.map-marker-info-inner' );
					let branches         = document.querySelectorAll( '.single_jones_address' );
					const infoBox        = document.querySelector( `[data-location-info = "${val}"]` );
					let target           = document.querySelector('[data-slug= "grb"]');
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
			});
	})();
</script>



</body>
</html>
