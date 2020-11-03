<?php
/**
 * Template part for displaying the footer widget area
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $locations;
?>



<section class="footer location_selection">

	<div class="dropdown_container">

		<select id="location" class="cs-select">
			<option value="" disabled selected>Choose Nearest Location</option>
			<?php foreach ( $locations as $location ) echo wp_rig()->get_location_option( $location ); ?>
		</select>

	</div><!-- end div.location_selector_container -->

	<div id="location_addresses"></div><!-- end div#each-address -->
</section>

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
		[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function( el ) {
			new SelectFx( el, {
				onChange: function( val ) {
					let branches = document.querySelectorAll( '.single_jones_address' );
					let target = document.querySelector('[data-slug="grb"]');
					branches.forEach( branch => {
						hide( branch );
						if ( branch.dataset.slug === val ) show( branch );

					})
				}
			});
		} );
	})();
</script>

