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


<nav id="side-hamburger-nav" role="navigation">
		<div class="hamburger-top-header">
			<div class="nav-burger"><div class="burger"></div> </div><!-- end div.nav-burger -->
		</div><!-- end div.top-header -->
		<ul id="hamburger-vertical-menu-list" class="nav">
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
const hamburgerNav = document.querySelector( '#side-hamburger-nav' );

hamburgerNav.addEventListener( 'mouseover', function() {
	this.classList.add('iopened');
}, false);

</script>
