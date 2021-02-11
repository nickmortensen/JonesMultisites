<?php
/**
 * The side navigation
 *
 * This is the template that displays the side navigation built froma tutorial.
 *
 * @link https://www.youtube.com/watch?v=uiZqDLqjGRY
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

	<?php wp_head(); ?>

</head>

<?php wp_rig()->print_styles( 'wp-rig-front-page' ); ?>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<aside id="sidemenu">
	<nav>
		<h4>Backend</h4>
		<a href="#">Dashboard</a>
		<a href="#">Profile</a>
		<a href="#">Preferences</a>
		<a href="#">Archive</a>

		<h4>Settings</h4>
		<a href="#">Accessibility</a>
		<a href="#">Theme</a>
		<a href="#">Admin</a>
	</nav>

	<a href="#" id="sidenav-close" title="Close Menu" aria-label="Close Menu"></a>
</aside>

<main>
	<header>
		<a href="#sidenav-open" id="sidenav-button" class="hamburger" title="Open Menu" aria-label="Open Menu">
			<svg viewBox="0 0 50 40" role="presentation" focusable="false" aria-label="trigram for heaven symbol">
				<line x1="0" x2="100%" y1="10%" y2="10%" />
				<line x1="0" x2="100%" y1="50%" y2="50%" />
				<line x1="0" x2="100%" y1="90%" y2="90%" />
			</svg>
		</a>
		<h1>Jones Sign Co.</h1>
	</header>
