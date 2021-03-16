<?php
/**
 * Template part for displaying the header floating navigation buttons.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<?php if ( 'development' === ENVIRONMENT ) : ?>
	<style>
.developer {
	grid-area: dev;
	display: flex;
	justify-content: space-around;
	align-items: center;
	background: var(--indigo-700);
}
</style>
<?php endif; ?>


<!------------------------------------------------
------ Navigation Buttons FLOAT ------------------
------------------------------------------------->
<div data-gridarea="floating-nav-buttons" class="floating-navigation-buttons" style="--flexflow: column nowrap;">
	<a href="#frontpage-email-cta" title="Connect with us!" id="sidemenu-mail-button" class="material-icons floating-btn"> mail_outline </a>
	<a id="sidemenu-search-button" class="material-icons floating-btn" title="Search the site." > search </a>
	<a id="sidemenu-toggle-button" title="Open/Close side navigation menu" class="material-icons floating-btn">menu</a>

	<!---------------- DEVELOPMENT ONLY NAV BUTTONS ------------------------>
	<?php
	if ( 'development' === ENVIRONMENT ) {
		wp_rig()->print_styles( 'wp-rig-developer' );
		get_template_part( 'template-parts/developer/scrolltotop' ); // Scroll to top Button Currently Does Not work.
		get_template_part( 'template-parts/developer/screenwidth' ); // shows the screenwidth at the bottom.
		get_template_part( 'template-parts/developer/grid', 'identify' ); // shows the screenwidth at the bottom.
	}
	?>
	<!---------------- END DEVELOPMENT ONLY ------------------------>
</div>
	<!------------------------------------------------
	------ END Navigation Buttons ------------------
	------------------------------------------------->
<script>
	const sideMenuToggle = document.getElementById( 'sidemenu-toggle-button' );
	const sideMenu       = document.getElementById( 'sideMenuContainer');
	const isSidebarOpen  = () => sideMenu.classList.contains( 'show-sidebar-menu' );

	const toggleSideMenu = () => {
		sideMenu.classList.toggle( 'show-sidebar-menu' );
		setTimeout( function() {
			const textContent = isSidebarOpen() ? 'close' : 'menu';
			const titleContent = isSidebarOpen() ? 'Close the Side Menu' : 'Open the Side Menu' ;
			sideMenuToggle.textContent = textContent;
			sideMenuToggle.setAttribute( 'title', titleContent );
		}, 800)
	}
	sideMenuToggle.addEventListener( 'click', toggleSideMenu, false );
	document.getElementById( 'sidemenu-close' ).addEventListener('click', toggleSideMenu, false);
</script>
