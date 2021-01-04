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

	<?php wp_head(); ?>
</head>

<?php wp_rig()->print_styles( 'wp-rig-front-page' ); ?>


<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	<!-- hamburger menu -->
<?php get_template_part( 'template-parts/menu/hamburger' ); ?>
<div id="page" class="site">


	<header class="site-header">
		<?php get_template_part( 'template-parts/header/branding' ); ?>
		<?php get_template_part( 'template-parts/header/navigation' ); ?>
		<?php get_template_part( 'template-parts/header/searchbar' ); ?>
		<!-- <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a> -->
	</header>


<script>

let indicateWindowWidth;
window.addEventListener( 'resize', () => {
	clearTimeout( indicateWindowWidth );
	indicateWindowWidth = setTimeout( () => {
		console.log( window.innerWidth );
		document.querySelector( '.search-field' ).value = `window is now ${window.innerWidth}px`;
	}, 500 );

})

</script>
