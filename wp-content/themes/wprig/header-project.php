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



<?php
$blog        = get_current_blog_id();
wp_rig()->print_styles( 'project' );

$post_type = get_post_type();


$project = [
	'address'    => wp_rig()->get_footer_project_address( $post->ID ),
	'structured' => wp_rig()->get_structured_project_address( $post->ID ),
	'thumbnail'  => get_the_post_thumbnail_url( $post->ID, 'featured' ),
	'vertical'   => get_post_meta( $post->ID, 'projectVerticalImage' ),
];

?>

<style>
	#controls {
		min-height: 12vw;
	}

</style>


<?php if ( 'project' === get_post_type() ) : ?>
<div style="display:none;" id="controls" class="flex row-nw justify-around align-center" hidden>
	<div id="blendmodeselect">
		<label for="blends" class="bg-white">background blendmode</label>
		<select name="blends" id="blends">
			<option value="normal">normal</option><option value="multiply" selected>multiply</option><option value="screen">screen</option><option value="overlay">overlay</option><option value="darken">darken</option><option value="lighten">lighten</option><option value="color-dodge">color-dodge</option><option value="color-burn">color-burn</option><option value="hard-light">hard-light</option><option value="soft-light">soft-light</option><option value="difference">difference</option><option value="exclusion">exclusion</option><option value="hue">hue</option><option value="saturation">saturation</option><option value="color">color</option><option value="luminosity">luminosity</option>
		</select>
	</div><!-- end div#blendmodeselect -->
	<div id="backdropmodeselect">
		<label for="backdrop" class="bg-white">backdrop filter </label>
		<select name="backdrop" id="backdrop">
			<option value="normal">normal</option><option value="multiply" selected>multiply</option><option value="screen">screen</option><option value="overlay">overlay</option><option value="darken">darken</option><option value="lighten">lighten</option><option value="color-dodge">color-dodge</option><option value="color-burn">color-burn</option><option value="hard-light">hard-light</option><option value="soft-light">soft-light</option><option value="difference">difference</option><option value="exclusion">exclusion</option><option value="hue">hue</option><option value="saturation">saturation</option><option value="color">color</option><option value="luminosity">luminosity</option>
		</select>
	</div><!-- end div#blendmodeselect -->
</div><!-- end div#controls -->
<?php endif; ?>


<body <?php body_class( 'w-screen ml-0 bg-blue-100' ); ?>>
<?php wp_body_open(); ?>



<div id="page" class="site">

<!-- masthead should look considerably different on screens wider than 1000 px -->
	<!-- header should include a menu, logo, search -->
	<header id="masthead" class="site-header">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>
	</header><!-- #masthead -->




	<style>
		#wide-header > div {
			min-height: 80vw;
			height: 100%;
			background-size: cover;
			background-repeat: no-repeat;
		}

		#wide-header > div:first-of-type {
			background: url( <?= $project['vertical'][0]; ?>), linear-gradient(#e66465 0%, #9198e5 61%);
			background-size: cover;
			background-repeat: no-repeat;
		}
		#normal-header {
			background: url( <?= $project['thumbnail']; ?>), linear-gradient(#e66465 0%, #9198e5 61%);
			background-size: cover;
			background-repeat: no-repeat;
		}
	</style>
	<section class="header" id="project-head">
		<!-- for screens smaller than 1300px -->
		<div id="normal-header" class="normal-width w-screen flex col-nw justify-end align-center">
			<div id="project-title">
				<h2 class="project-title"><?= the_title(); ?></h2>
			</div><!-- end div#project-title -->
		</div><!-- end div#project-header.normal-width-->


		<!-- for screens larger than 1300px -->
		<div id="wide-header" class="particularly-wide">
			<div></div>
			<div>
				<h2 class="project-title"><?= the_title(); ?></h2>
			</div>
		</div>
	</section>




</div><!-- end div#page -->

<section>
	<pre>
		<?php

		print_r( get_post_custom_keys( $post->ID ) );
		echo $post_type;

		?>
	</pre>
</section>

<script>
	const projectHeader = document.getElementById( 'project-header' );
	const blendSelect = document.getElementById('blends');
	const backdrop = document.getElementById('backdrop');




	const changeBlendMode = event => document.getElementById( 'project-header' ).style.backgroundBlendMode = event.target.value;
	blendSelect.addEventListener( 'change', changeBlendMode, true );


	const backdropFilters = [];
	const options = [ 'blur', 'brightness', 'contrast', 'grayscale', 'hue-rotate', 'invert', 'opacity', 'saturate', 'sepia'];
	const units = [ 'length', 'percent', 'percent', 'percent', 'degree', 'percent', 'percent', 'percent', 'percent'];
	const result = units.reduce( ( result, field, index ) => {
		result[options[index]] = field;
		return result;
	}, {} );

	const backdropTypes = {
		blur: "length",
		brightness: "percent",
		contrast: "percent",
		grayscale: "percent",
		"hue-rotate": "degree",
		invert: "percent",
		opacity: "percent",
		saturate: "percent",
		sepia: "percent",
	};
	console.log( result );
</script>

<?php


//phpcs:disable
// get_template_part( 'template-parts/header/project_header' );
// get_template_part( 'template-parts/header/branding' );
// get_template_part( 'template-parts/header/navigation' );

?>
