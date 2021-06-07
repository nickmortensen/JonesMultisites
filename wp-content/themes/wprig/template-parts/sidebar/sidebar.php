<?php
/**
 * Top bar on screens under 1400px and sidebar on screens larger.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$titletext = 'Go to the Jones Sign Company Homepage';
if ( is_home() ) {
	$titletext = 'You are on the Jones Sign Homepage';
}
?>





<div class="sidebar sidebar_top">
<?= wp_rig()->get_navbar_icons(); ?>
</div>
<!--<div class="sidebar sidebar_side"></div> end div.sidebar -->

<?php //get_template_part( 'template-parts/sidebar/socialmedia' ); ?>



<!-- when clicking the haburger menu, toggle the sidebar -->
<script type="text/javascript">
const sidebarToggleContainer = document.querySelector( ".navigation-icons > #sidebarToggleContainer" );
sidebarToggleContainer.addEventListener( 'click', function( e ) {
	e.preventDefault();
	document.documentElement.classList.toggle( 'sidebar-open' );
});
</script>


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
// let verticalToggleSidebarIconContainer = document.querySelector( '.navigation-icons.sidebar > div#sidebarToggleContainer' );
// verticalToggleSidebarIconContainer.addEventListener( 'click', function( e ) {
// 	e.preventDefault();
// 	document.documentElement.classList.toggle( 'sidebar-open' );
// });
/**
 * Add or remove a class of 'search-open' when the search icon in the sidebar is clicked
 */
// let searchToggler = document.getElementById( 'searchToggleContainer' );
// searchToggler.addEventListener( 'click', function() {
// 	toggleBodyClass( 'search-open' );
// 	console.log( 'class search-open has been toggled' );

// })


</script>
