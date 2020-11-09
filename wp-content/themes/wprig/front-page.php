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
$locations = wp_rig()->get_location_ids( 75, 72 );



$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}

$locations = wp_rig()->get_location_ids( 75 );
?>



	<main id="primary" class="site-main full-grid">
		<?php
		// phpcs:disable
		// while ( have_posts() ) {
		// 	the_post();

		// 	get_template_part( 'template-parts/content/entry', get_post_type() );
		// }
		// phpcs:enable

		get_template_part( 'template-parts/frontpage/project_cards' );
		?>

<!-- <section id="frontpage-experiment" class="full-grid light-text" style="background: #0273b9">

<h1>Custom Select <span>Without Plugin</span></h1>
<div class="select_outer">
	<div id="selected-default"></div>

	<select id="location-select" name="jones-location-select" class="">
		<option value="grb" data-location-id="60">Jones Green Bay</option>
		<option value="mxz" data-location-id="66">Jones Juárez</option>
		<option value="las" data-location-id="61">Jones Las Vegas</option>
		<option value="lax" data-location-id="70">Jones Los Angeles</option>
		<option value="mia" data-location-id="73">Jones Miami</option>
		<option value="msp" data-location-id="74">Jones Minneapolis</option>
		<option value="nat" data-location-id="72" selected="selected">Jones Sign Company</option>
		<option value="phl" data-location-id="64">Jones Philadelphia</option>
		<option value="rno" data-location-id="63">Jones Reno</option>
		<option value="san" data-location-id="69">Jones San Diego</option>
		<option value="tpa" data-location-id="68">Jones Tampa</option>
		<option value="mxt" data-location-id="65">Jones Tijuana</option>
		<option value="vab" data-location-id="67">Jones Virginia Beach</option>
	</select>
</div>


</section> -->



	</main><!-- #primary -->

	<script>
/*
Reference: http://jsfiddle.net/BB3JK/47/
*/

// let selectOuter       = document.getElementById('frontpage-experiment');
// let select            = selectOuter.querySelector( 'select' );
// const options         = Array.from(select.options);
// let numberOfOptions   = options.length;
// const defaultLocation = options[select.selectedIndex];

// const styled       = document.createElement( 'div' );
// styled.textContent = defaultLocation.textContent;
// styled.classList.add( 'select-styled' );
// const unorderedList = document.createElement('ul');
// function createListElement( options ) {
// 	options.forEach( option => {
// 		unorderedList.innerHTML += `<li data-value="${option.value}" data-location-id="${option.dataset.locationId}">${option.textContent}</li>`;
// 	})
// }

// let selectedDefaultDiv = document.querySelector("#selected-default");
// let selectParent       = selectedDefaultDiv.parentNode;
// let list               = createListElement( options );
// selectParent.appendChild( unorderedList );
// selectParent.insertBefore( styled, unorderedList );






	</script>


<?php
get_footer();
