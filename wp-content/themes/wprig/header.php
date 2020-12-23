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

		<div id="content">

			<header class="banner" id="main-header" role="banner">

				<?php get_template_part( 'template-parts/header/branding' ); ?>
				<?php get_template_part( 'template-parts/header/navigation' ); ?>
				<?php get_template_part( 'template-parts/header/searchbar' ); ?>
				<!-- <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a> -->
			</header>



