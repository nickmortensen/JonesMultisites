<?php
/**
 * Template part for displaying the footer widget area
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$locations = wp_rig()->get_location_ids( 75 );
?>

	<select class="location-select-element">
		<option value="" disabled selected>Choose A Location</option>
		<?php foreach ( wp_rig()->get_location_ids( 75 ) as $location ) echo wp_rig()->get_location_option( $location ); ?>
	</select>
	<!-- end div#location_addresses -->

