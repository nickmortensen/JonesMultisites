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

	<div class="dropdown_container">

		<select id="location" class="cs-select">
			<option value="" disabled selected>Choose Nearest Location</option>
			<?php foreach ( $locations as $location ) echo wp_rig()->get_location_option( $location ); ?>
		</select>

	</div><!-- end div.dropdown_container -->

	<div id="location_addresses"></div><!-- end div#location_addresses -->

</div><!-- end div#location_selector -->

<script>
document.querySelector('.cs-select').classList.toggle( 'cs-skin-border' );
document.querySelector( '#location_addresses' ).innerHTML = eachAddress.join('');
let branches = document.querySelectorAll( 'div.single_jones_address' );

const show = element => element.style.display = 'flex';
const hide = element => element.style.display = 'none';
branches.forEach( branch => {
	hide( branch );
	if ( 'nat' === branch.dataset.slug ) show( branch );
});

	(function() {
		new SelectFx(
			document.querySelector( '.cs-select'), {
				onChange: function( val ) {
					let branches = document.querySelectorAll( '.single_jones_address' );
					let target = document.querySelector('[data-slug="grb"]');
					branches.forEach( branch => {
						hide( branch );
						if ( branch.dataset.slug === val ) show( branch );
					})
				}
			});
	})();
</script>

