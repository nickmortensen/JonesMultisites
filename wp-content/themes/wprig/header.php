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
<div id="page" class="site frontpage">

<?php
$image_id       = 703;
$sizes          = [ 'full', 'medium_large', 'large', 'wp-rig-featured' ];
$sourceset      = wp_get_attachment_image_srcset( $image_id, $sizes[1] );
$masthead_image =  wp_get_attachment_image_src( $image_id, $sizes[1] );
?>

<style>
section#head.masthead {
	min-width: 100%;
	height: clamp(800px, 20vh, 900px);
	background:
		url(<?= $masthead_image[0]; ?>)
		bottom right / cover
		no-repeat
		var(--color-theme-primary);
	background-blend-mode: darken;
	height: clamp(800px, 40vh, 900px);

	display: grid;

	grid-template-columns: repeat( 6, 1fr );
	grid-template-rows: 1fr 4fr;
	grid-template-areas:
		"headertopper headertopper headertopper headertopper headertopper headertopper"
		"headercontent headercontent headercontent blank blank blank";
}


.masthead-topper {
	display: grid;
	grid-template-columns: repeat( 5, 1fr );
	grid-area: headertopper;
	grid-template-areas: "logo logo connect connect connect";
}

div.masthead {
	grid-area: headercontent;
	min-height: 160px;
	min-width: 280px;
	background: #ffc600;
	opacity: 0.75;
	mix-blend-mode: luminosity;
}
</style>

<section id="head" class="masthead">

	<div class="masthead-topper">
	<?php get_template_part( 'template-parts/header/branding' ); ?>
	<?php //get_template_part( 'template-parts/header/navigation' ); ?>
	<?php get_template_part( 'template-parts/header/connect' ); ?>
	</div>
</section>


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
