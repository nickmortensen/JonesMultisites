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
	<section id="fp-projects" class="frontpage-projects" style="display: none" >
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





<section id="locations_dropdown_container">
	<?php $location_ids = wp_rig()->get_location_ids( 72, 75 ); ?>
	<label for="jones_location_dropdown"><?= count( $location_ids ); ?>Locations Across North America</label>

	<div class="select_outer">
		<select name="jones_location_dropdown" id="locations_select">
			<option value="none" disabled selected>Choose your option</option>
			<?php

				foreach ( $location_ids as $location ) {
					$location_data[] = wp_rig()->get_location_info( $location );
					echo wp_rig()->get_location_option( $location );
				}
			?>
		</select>
	</div><!-- end div.select_outer -->

	<div class="all_location_details">
	<?php

	$location_data = [];
	echo wp_rig()->get_single_location_details( 72 );
	foreach ( $location_ids as $location ) {
		[
			'id'                => $id,
			'name'              => $name,
			'slug'              => $slug,
			'blog_id'           => $blog,
			'description'       => $description,
			'location_image_id' => $location_image,
			'city_image_id'     => $city_image,
			'subdomain'         => $subdomain,
			'nimble'            => $nimble,
			'address'           => $address,
			'capabilities'      => $capabilities,
		]        = wp_rig()->get_location_info( $location );
		$output  = '';
		$output .= wp_sprintf( '<div class="location_details hide_location" title="%s" data-show-location="%s" data-location-slug="%s" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">', $description, $blog, $slug );
		$output .= wp_sprintf( '<h2>%s</h2>', ucwords( $name ) );
		$output .= wp_rig()->get_single_location_address( $location );
		$output .= wp_sprintf( '<a href="tel:+1-%s" itemprop="telephone">%s</a>', $address['phone'], $address['phone'] );
		$output .= '</div><!-- end div.location_details -->';
		echo $output;
	}
	?>
	</div><!-- end div.all_location_details -->
</section>






<script>
	let fullAddresses = document.querySelectorAll( '.location_details' );
function showLocation( e ) {
	let selectionValue = e.target.value;
	let fullAddresses = document.querySelectorAll( '.location_details' );
	console.log( 'selected item with a value of', e.target.value );
	fullAddresses.forEach( address => {
		if (address.dataset.locationSlug !== selectionValue ) {
			if ( address.classList.contains( 'hide_location' ) ) {
				return;
			} else {
				address.classList.add( 'hide_location' )
			}
		}
		if (selectionValue === address.dataset.locationSlug) {
			address.classList.remove( 'hide_location' );
		}
	} )
}
const selectBox = document.querySelector( '#locations_select');
const locationOptions = Array.from( selectBox.options );

selectBox.addEventListener( 'change', showLocation, true );

// 	const locationAddressDivs   = document.querySelectorAll( '.location_details' );
// 	const locationSelectOptions = document.getElementById( 'location-select' );
// 	let selected = locationSelectOptions.selectedIndex;
// 	locationAddressDivs.forEach(item => {
// 		const divSlug = item.dataset.locationSlug;
// 		if ( locationSelectOptions[selected].value !== divSlug ) {
// 			item.classList.add('hideThisDiv');
// 		}
// 	})
// function changeDiv( element ) {
// 	let slug = element.target.value;

// 	locationAddressDivs.forEach(item => {
// 		const divSlug = item.dataset.locationSlug;
// 		if ( slug === divSlug ) {
// 			item.classList.remove('hideThisDiv');
// 		}
// 		if ( slug !== divSlug && ! item.classList.contains( 'hideThisDiv' ) ) {
// 			item.classList.toggle('hideThisDiv');;
// 		}
// 	})
// }
	// locationSelectOptions.addEventListener( 'change', changeDiv)
	// locationAddressDivs.forEach(item => console.log( item.dataset.locationSlug ) );

</script>



<script>
	const element = document.querySelector( 'select.cs-select' );
	const options = {
		newTab : true,
		// open links in new tab (when data-link used in option)

		stickyPlaceholder : true,
		// when opening the select element, the default placeholder (if any) is shown

		onChange : function( val ) { return false; }
	};
	// const effects = new SelectFx( element, options );
</script>
	<script>
			(function() {
				[].slice.call( document.querySelectorAll( 'select.cs-select' ) ).forEach( function(el) {
					new SelectFx(el, {
						stickyPlaceholder: false
					});
				} );
			})();
		</script>



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
