<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php if ( ! wp_rig()->is_amp() ) : ?>
		<script>document.documentElement.classList.remove( 'no-js' );</script>
	<?php endif; ?>
<script>
	const ENVIRONMENT = 'development';
</script>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<div class="floating-navigation-buttons">
		<a title="Connect with us!" id="sidemenu-mail-button" class="material-icons button"> mail_outline </a>
		<a title="Search the site." id="sidemenu-search-button" class="material-icons button"> search </a>
		<a title="Open/Close side navigation menu" id="sidemenu-toggle-button" class="material-icons button"> menu </a>
	</div>


	<aside id="sidemenu">
		<a id="close-sidemenu" class="material-icons button reversed" title="close side menu"> close </a>
		<nav id="sidemenu-navigation">
		<?= implode( "\n\t\t\t", wp_rig()->get_project_sidemenu_items( 8 ) ); ?>
		</nav>
	</aside>

<!-- always on widget to show me how wide the screen is -->
<?php
if ( 'development' === ENVIRONMENT ) {
	get_template_part( 'template-parts/developer/screenwidth' );
}
?>
<!-- toggle side menu script -->
<script>
const sideNavToggler = document.getElementById( 'sidemenu-toggle-button' );
const sideNavCloser  = document.querySelector( '#close-sidemenu' );
[ a, b ] = [ 'menu', 'close' ];
sideNavToggler.addEventListener( 'click', function() {
	document.body.classList.toggle( 'hide-sidemenu' );
	this.textContent = document.body.classList.contains( 'hide-sidemenu' ) ? a : b;
	let title = document.body.classList.contains( 'hide-sidemenu' ) ? 'Open Side Menu' : 'Close Side Menu';
	this.setAttribute( 'title', title );
	window.focus( document.body ); // so you can still scroll
}, false);

sideNavCloser.addEventListener( 'click', function() {
	document.body.classList.toggle( 'hide-sidemenu' );
	sideNavToggler.textContent = 'menu';
}, true);
</script>
<!-- END toggle side menu script -->



<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>

	<header id="masthead" class="site-header">
		<?php get_template_part( 'template-parts/header/custom_header' ); ?>

		<?php get_template_part( 'template-parts/header/branding' ); ?>

		<?php get_template_part( 'template-parts/header/navigation' ); ?>
	</header><!-- #masthead -->




