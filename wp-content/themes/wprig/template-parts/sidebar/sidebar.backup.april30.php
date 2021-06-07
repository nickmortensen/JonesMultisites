<?php
/**
 * Top bar on screens under 1400px and sidebar on screens larger.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 * @note before update on 30_April_2021
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$titletext = 'Go to the Jones Sign Company Homepage';
if ( is_home() ) {
	$titletext = 'You are on the Jones Sign Homepage';
}
?>

<div id="sidebar">
	<div class="sidebar-other-container"></div>
	<div class="sidebar-icons-container">
		<div data-opens="sidebar-general" id="svgIconContainer" class="sidebar-single-icon-container">
			<a class="sidebar branding" href="<?= esc_url( home_url() ); ?>" title="<?= $titletext; ?>">
				<style>
					svg {
						--circle-fill: transparent;
						--circle-stroke-width: 10;
						--circle-stroke-color: var(--foreground);
						--logo-fill: var(--foreground);
						height: 64px;
						width: 64px;
						margin-top: 8px;
					}

					circle {
							fill: var(--circle-fill, #0273b9);
							stroke: var(--circle-stroke-color, #0273b9);
							stroke-width: var(--circle-stroke-width, 10);
							stroke-miterlimit: var(--miterlimit, 10);
						}

					path { fill: var(--logo-fill, #fcdde6); }
				</style>
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="pylon_circle" class="jones_icon" x="0px" y="0px" viewBox="0 0 500 500" style="enable-background:new 0 0 500 500;" xml:space="preserve">
					<circle cx="262.9" cy="242.5" r="215.3"/>
					<path d="M430.4,225.9H248.7c-8.1,0-14.8-6.6-14.8-14.8v-18h-18.6c-3.3,8.7-11.9,15.6-22.2,15.9L89.8,370.5 c-4.3-5.9-8.4-12-12.1-18.3l98.1-150c-4.8-4.5-7.8-10.6-7.8-17.7c0-13.5,10.9-24.3,24.5-24.3c11.7,0,21.5,7.8,23.9,19.3H234v-24.8 c0-8.1,6.6-14.8,14.8-14.8h181.6c8.1,0,14.8,6.6,14.8,14.8v56.3C445.1,219.3,438.5,225.9,430.4,225.9z"/>
				</svg>
			</a>
		</div><!-- end div#svgiconcontainer -->

		<div data-opens="sidebar-general" id="sidebarToggleContainer" class="sidebar-single-icon-container open_close_sidebar" title="open/close sidebar menu">
			<div>
				<span></span>
				<span></span>
				<span></span>
			</div>
		</div><!-- end div#sidebartogglecontainer -->

		<div id="open_space" class="sidebar-single-icon-container">
				<!-- <h3 class="developer"></h3> -->
		</div><!-- end div#open_space -->

		<div data-opens="sidebar-search" id="searchToggleContainer" class="sidebar-single-icon-container">
			<a id="searchToggle" class="material-icons nav_search" title="search jonessign.com" data-iconname="search" type="button">search</a>
		</div><!-- end div#searchtogglecontainer -->

		<div data-opens="sidebar-contact" id="mailIconContainer" class="sidebar-single-icon-container">
			<a id="contactUsToggle" class="material-icons nav_contact" title="contact us" data-iconname="mail_outline" type="button">mail_outline</a>
		</div><!-- end div#searchtogglecontainer -->

		<div data-opens="sidebar-general" id="moreToggleContainer" class="sidebar-single-icon-container">
				<a class="material-icons nav_more" title="search jonessign.com" data-iconname="more_vert" type="button">more_vert</a>
		</div><!-- end div#moretogglecontainer -->
	</div><!-- end div.sidebar-icons-container -->
	<div class="sidebar-social-media-links-container">
	<?php get_template_part( 'template-parts/sidebar/socialmedia' ); ?>
	</div>

</div><!-- end div#sidebar -->

<script>
/**
 * Show current window width on page load and then reset it upon resize.
 */
let resizeTimer;
function displayWindowWidth() {
	let dev = document.createElement('h3');
	dev.classList.add( 'developer' );
	dev.textContent = `${window.innerWidth}px`;
	document.getElementById( 'open_space' ).appendChild( dev );
}
displayWindowWidth();

window.addEventListener( 'resize', () => {
	document.body.classList.add( 'resizing' );
	document.querySelector( '#open_space > h3.developer ' ).innerHTML = `${window.innerWidth}px`;
	clearTimeout( resizeTimer );
	resizeTimer = setTimeout( () => {
		document.body.classList.toggle( 'resizing' );
	}, 650);
} )

</script>

<script>

// const toggleIcon = document.getElementById( 'svgIconContainer' );
const toggleSidebarIconContainer = document.getElementById( 'sidebarToggleContainer' );
toggleSidebarIconContainer.addEventListener( 'click', function( e ) {
	e.preventDefault();
	document.getElementById( 'sidebarToggleContainer' ).classList.toggle( 'menu-open' );
	document.documentElement.classList.toggle( 'sidebar-open' );
});
/**
 * Add or remove a class of 'search-open' when the search icon in the sidebar is clicked
 */
let searchToggler = document.getElementById( 'searchToggleContainer' );
searchToggler.addEventListener( 'click', function() {
	toggleBodyClass( 'search-open' );
	console.log( 'class search-open has been toggled' );

})


</script>
