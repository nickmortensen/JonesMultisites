<?php
/**
 * Template part for displaying the footer widget area
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $locations;
?>



<div id="location_selector" class="footer_element full-grid">


	<select id="location" class="branch-select">
		<option value="" disabled selected>Choose Nearest Location</option>
		<?php foreach ( $locations as $location ) echo wp_rig()->get_location_option( $location ); ?>
	</select>
	<!-- end div#location_addresses -->

</div><!-- end div#location_selector -->
<div id="location_addresses" class="full-grid"></div>



<script>
document.querySelector( '#location_addresses' ).innerHTML = eachAddress.join('');
</script>






