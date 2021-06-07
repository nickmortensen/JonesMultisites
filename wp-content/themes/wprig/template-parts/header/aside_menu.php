<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( ! wp_rig()->is_aside_nav_menu_active() ) {
	return;
}

?>

<div id="hamburger-menu-toggle">
	<i class="material-icons">menu</i>
</div>
<nav id="hamburger_navigation" role="navigation" class="">
	<ul id="hamburger_vertical_menu">
		<?php
		$aside_menu_args = [
			'walker'               => new Hamburger_Walker_Nav_Menu(),
			'menu'                 => 'projects',
			'menu_class'           => 'side',
			'menu_id'              => 'projects',
			'container'            => 'ul',
			'items_wrap'           => '%3$s',
			'depth'                => 1,
			'container_aria_label' => 'Project Profiles',
		];
		wp_rig()->display_aside_nav_menu( $aside_menu_args );
		?>
	</ul>
</nav><!-- end nav#side-hamburger-nav -->

<!-- #projects-navigation -->
<script>
const hamburgerNav          = document.querySelector( '#hamburger_navigation' );

const sideMenuIcon          = document.querySelector( '#hamburger-menu-toggle > i' );
const sideMenuIconContainer = document.querySelector( '#hamburger-menu-toggle' );


const checkBodyClasses = className => document.body.classList.contains( className );

function handleMenuMouseOver() {
	let isMenuOpen = checkBodyClasses( 'sidemenu__open' );
	let buttonText = !isMenuOpen ? 'menu_open' : 'menu';
	if ( ! isMenuOpen ) {
		document.body.classList.add( 'sidemenu__open' );
		setTimeout(function() {
			document.querySelector( '#hamburger-menu-toggle > i' ).textContent = buttonText;
		}, 300);
	}
}


function handleMouseExit() {
	let isMenuOpen = checkBodyClasses( 'sidemenu__open' );
	let buttonText = !isMenuOpen ? 'menu_open' : 'menu';
	if ( isMenuOpen ) {
		document.body.classList.remove( 'sidemenu__open' );
		setTimeout(function() {
			document.querySelector( '#hamburger-menu-toggle > i' ).textContent = buttonText;
		}, 300);
	}
}
hamburgerNav.addEventListener( 'mouseover', handleMenuMouseOver, false );
hamburgerNav.addEventListener( 'mouseleave', handleMouseExit, false );


</script>
