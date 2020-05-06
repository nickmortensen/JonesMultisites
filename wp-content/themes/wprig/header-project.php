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
$project = [
	'address'   => wp_rig()->get_structured_project_address( $post->ID ),
	'thumbnail' => get_the_post_thumbnail_url( $post->ID, 'featured' ),
];
$slides = get_post_meta( $post->ID, 'projectImagesSlideshow', true );
$address = wp_rig()->get_footer_project_address( $post->ID );
$logo_url = wp_rig()->get_project_svg( $post->ID );
$svg      = wp_rig()->the_svg( $logo_url, false, 25 );

$blog               = get_current_blog_id();


?>

<style>
	#controls {
		min-height: 12vw;
	}
	#project-header {
		padding: 3vw;
		min-height: 50vw;
		background-image: url( <?= $project['thumbnail']; ?>);
		background-color: var(--yellow-600);
		background-blend-mode: multiply;
		background-size: cover;
	}
</style>


<?php if ( 'project' === get_post_type() ) : ?>
<?php wp_rig()->print_styles( 'project' ); ?>

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

	<header id="masthead" class="site-header">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>

	<div id="project-header" class="w-screen flex col-nw justify-end align-center">
		<div id="project-title"> <h2 class="project-title"><?= the_title(); ?></h2></div>
	</div><!-- end div#project-header -->




	</header><!-- #masthead -->

	<main id="primary" class="w-screen project-profile flex row-nw justify-end align-center">
		<div>
			<?= $address['state']; ?>
		</div>
<div id="slideshow" class="p-2 flex row-wrap justify-start align-items-start">

<style>
	img.ss-samp {
		max-width: 300px;
	}
</style>
<?php
$slides = get_post_meta( $post->ID, 'projectImagesSlideshow', true );
foreach ( $slides as $id => $url ) {
echo "<img class=\"ss-samp\" src=\"$url\" />";
	echo '<br>';
}
echo $project['address'];
?>

</div>

		<div class="client-information flex col-nw justify-start align-start p-8 bg-gray-500 text-white text-4xl">
			<div class="align-self-center"> <?= $svg; ?> </div>
			<div> <?= wp_rig()->get_project_detail( $post->ID, 'client' ); ?> </div>
			<div> <?= wp_rig()->get_project_detail( $post->ID, 'tease' ); ?> </div>
			<div> <?= wp_rig()->get_project_detail( $post->ID, 'job_id' ); ?> </div>
			<div> <?= wp_rig()->get_project_detail( $post->ID, 'year_complete' ); ?> </div>
		</div>

	</main><!-- #primary -->





		<?php


		//phpcs:disable
		// get_template_part( 'template-parts/header/project_header' );
		// get_template_part( 'template-parts/header/branding' );
		// get_template_part( 'template-parts/header/navigation' );

		?>

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

