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

	<?php
	if ( ! wp_rig()->is_amp() ) {
		?>
		<script>document.documentElement.classList.remove( 'no-js' );</script>
		<?php
	}
	?>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<style>
	.header {
		grid-column: span 5;
		display: grid;
		grid-gap: 20px;
		grid-template-columns: 1fr 4fr;
	}
	.item9 {
		grid-column: span 2
	}
</style>
<div id="page" class="container">
	<header class="item header">
		<div class="logo">Logo</div>
		<nav class="navigation">Navigation</nav>
	</header>

