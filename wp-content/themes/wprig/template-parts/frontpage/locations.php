<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$locations = wp_rig()->get_location_ids( 75 );

?>
<section id="joneslocations" class="frontpage">
	<div id="map-container">
	<?= wp_rig()->get_locations_mapped(); ?>
	</div><!-- end #map-container -->
</section>


<script>
document.querySelector( '#location-address' ).innerHTML = eachAddress.join('');
</script>
