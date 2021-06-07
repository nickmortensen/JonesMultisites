<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;



[ 'requested_by' => $request_from ] = $args;
$locations = wp_rig()->get_location_ids( 75 );

?>

<section id="jones-facility-locations" class="<?= $request_from; ?>">
<div class="facilities_header">
	<?= count( wp_rig()->get_location_ids( 75, 72 ) ); ?> Locations Across North America
</div>

<div id="usa-map-container" class="facilities_map">
	<?= wp_rig()->get_locations_mapped(); ?>
</div><!-- end #map-container -->


<div class="facilities_information">

<?php

	foreach ( $locations as $location ) {
		echo wp_rig()->get_single_location_details_frontpage( $location );
	}
?>


	</div>
</section>


<script>

function pinHandler( e ) {
	e.preventDefault();
	let hash              = e.target.dataset.slug;

	console.log( `THE HASH IS: ${hash}` );
	const locationInfoDiv = document.getElementById( hash );
	const individualLocations = document.querySelectorAll( '.facilities_information > div' );
	individualLocations.forEach( location => {
		if ( location.dataset.locationSlug === hash && location.classList.contains( 'remove' ) ) {
			location.classList.toggle( 'remove' );
		} else if ( location.dataset.locationSlug !== hash && ! location.classList.contains( 'remove' ) ) {
			location.classList.toggle( 'remove' );
		}
	} );

}
const locationPins = document.querySelectorAll( '.jones_facility_pins > a.material-icons' );

locationPins.forEach( pin => {
	pin.addEventListener('click', pinHandler, false );
});
</script>
