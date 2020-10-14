<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $post;
global $template;

[
	'svg'           => $svg,
	'slideshow'     => $slideshow_images,
	'square'        => $square,
	'vertical'      => $vertical_url,
	'thumbnail'     => $thumbnail_id,
	'thumb_url'     => $thumb_url,
	'signtypes'     => $signtypes,
	'expertise'     => $expertise,
	'industries'    => $industries,
	'modified'      => $modified,
	'excerpt'       => $excerpt,
	'address'       => $address,
	'alt_name'      => $alt_name,
	'tease'         => $tease,
	'partners'      => $partners,
	'client'        => $client,
	'year_complete' => $year_complete,
] = wp_rig()->get_all_project_info( $post->ID );
[
	'vertical' => $vertical,
	'featured' => $featured,
] = wp_rig()->get_project_header_images( $post->ID );

/**
 * Get the city and state with a comma separating the two
 *
 * @param array $address Contains address information.
 */
function get_city_state( $address ) {
	return $address['city'] . ', ' . $address['state'];
}

$imagedata_array = [ 'src', 'class', 'alt', 'srcset', 'sizes', 'loading' ];
$vertical_srcset = wp_get_attachment_image_srcset( $vertical, 'medium_large' );
$featured_srcset = wp_get_attachment_image_srcset( $featured, 'medium' );

$vertical_src = wp_get_attachment_image_src( $vertical, 'medium_large' )[0];
$featured_src = wp_get_attachment_image_src( $featured, 'wp-rig-featured' )[0];
get_header();

wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-project' );
?>

<style>
@media (max-width: 1399px ) {
	.project-header {
		background: var(--blue-900) center / cover no-repeat url( <?=$featured_src; ?> )
	}
}


</style>

	<main id="single-item" class="project">

		<header class="entry-header" >
			<?php if ( ! is_search() ) : ?>
			<div class="single-header hide-on-wide" style="background: var(--blue-900) center / cover no-repeat url( <?=$featured_src; ?> );">
				<h1><?= get_the_title(); ?></h1>
				<h2><?= wp_rig()->get_city_state( $address ); ?></h2>
				<h2><?= $tease ?></h2>
			</div> <!--end div.single-header -->


			<!-- only shows on a wide screen > 1200px -->
			<div class="single-header:wide">
				<div id="vertical" style="background: var(--blue-900) center / cover no-repeat url( <?=$vertical_src; ?> );"></div>
				<div>
					<h1><?= get_the_title(); ?></h1>
					<img style="height:100px;" src="<?= $svg; ?>"/>
					<h2><?= wp_rig()->get_city_state( $address ); ?></h2>
					<article class="narrative"><?= wp_rig()->get_project_narrative( $post->ID ); ?></article>
				</div>
			</div><!-- end div.single-header:wide -->
			<?php endif; ?>
		</header><!-- .entry-header -->

		<section class="single-item-content hide-on-wide">
			<article class="narrative"><?= wp_rig()->get_project_narrative( $post->ID ); ?></article>
			<aside class="additional-info">
				<img style="height:100px;" src="<?= $svg; ?>"/>
			</aside>
		</section><!-- end section.single-project-content -->

	</main><!-- #single-project -->
	<?php
if ( 'development' === ENVIRONMENT ) {
	get_template_part( 'template-parts/content/admin_tweaks' );
}
?>

<?php if ( 'development' === ENVIRONMENT ) : ?>
	<script>
	const bgBlendOptions = document.querySelector( '#blend' );
	bgBlendOptions.addEventListener( 'change', function(e) {
		console.log( 'project header blend mode is now', e.target.value );
		document.querySelector( '.single-header' ).style.backgroundBlendMode = e.target.value;
		document.querySelector( '#vertical' ).style.backgroundBlendMode = e.target.value;
	} );

	const textBlendOptions = document.querySelector( '#headingblend' );
	textBlendOptions.addEventListener( 'change', function(e) {
		document.querySelector('.hide-on-wide h1').style.mixBlendMode = e.target.value;
	})
</script>
<?php endif; ?>

<?php
if ( 3 < count( $slideshow_images ) ) {
	wp_rig()->print_styles( 'wp-rig-flickity' ); // Load the styles for flickity touch slider. Already preloaded.
	get_template_part( 'template-parts/content/entry_slider' );
}

?>




<?php

echo basename( $template );
wrap( $template );

wrap( wp_rig()->get_all_project_info( $post->ID ) );






get_footer();
