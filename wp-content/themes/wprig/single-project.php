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

get_header();

global $post;
global $post_type;
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

$tags = array_merge( $signtypes, $expertise, $industries );

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

$vertical_src = wp_get_attachment_image_src( $vertical, 'medium_large' )[0] ?? '';
$featured_src = wp_get_attachment_image_src( $featured, 'medium_large' )[0];


wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-project' );
?>

<main class="project_profile">

	<style>

	:root {
		--project-header-image: url( <?= $vertical_src; ?>);
	}

		.single_project_profile > section:first-of-type {
			background: center / cover no-repeat url(<?= $featured_src; ?>), var(--blue-700);
			background-blend-mode: multiply;
			min-height: 451px;
			min-width: 100%;
		}

		.svg-contain {
			background: transparent;
			width: 300px;
			height: 300px;
		}

		.client_logo {
			width: 10vw;
			height: 10vw;
		}

		.project-header {
			position: relative;
			display: grid;
			grid-template-columns: repeat(6, 1fr);
			grid-template-rows: repeat(10, 1fr);
		}

		.project_title {
			grid-column: 4 / -1;
			grid-row: 1 / span 10;
			background: var(--blue-600);
			z-index: 7;
			mix-blend-mode: color-burn;
		}

		.project_initial_image {
			grid-column: 1 / span 3;
			grid-row: 1 / span 10;
			z-index: 6;
			background-image: var(--project-header-image);
			background-size: cover;
		}

		@media screen and (max-width: 600px) {
			.project_header {
				min-height: 660px;
			}
			.project_initial_image {
				grid-column: 1 / -1;
				grid-row: 1 / span 10;
				z-index: 6;
				background-image: var(--project-header-image);
				background-size: cover;
			}

			.project_title {
				grid-column: 1 / -1;
				grid-row: 7 / span 3;
				background: var(--blue-600);
				z-index: 7;
				mix-blend-mode: multiply;
			}
		}

	</style>

<section class="project-header">

	<div class="project_title"><?php the_title( '<h1 class="light-text">', '</h1>' ); ?></div>
	<div class="project_initial_image"></div>

</section>


<section class="project_profile">

<?php
	foreach ( $tags as $term_to_link ) {
		echo wp_rig()->get_term_hyperlink( $term_to_link );
	}
?>

	<div>
		<span class="light-text excerpt project_excerpt"><?= $excerpt; ?></span>
	</div>
	<div class="client_logo"> <img src="<?= $svg; ?>" /> </div>



<?php
	if ( 'development' !== ENVIRONMENT ) {
		get_template_part( 'template-parts/developer/bgblend' );
	}
?>

</section>
<style>

	#sliding-element {
		grid-column: 1 / -1;
	}

	#sliding-element img.flickity-image::after {
		content: attr(data-caption);
	}
</style>



<!-- Draggable slideshow to display photos -->
<?php if ( $slideshow_images ) : ?>
<?php wp_rig()->print_styles( 'wp-rig-flickity' ); ?>

<section id="sliding-element">
<?php
	foreach ( $slideshow_images as $identifier => $url ) {
		$caption = '' !== get_the_excerpt( $id ) ? get_the_excerpt( $identifier ) : '';
		$options = [
			'data-caption' => $caption,
			'class'        => '',
		];
		echo wp_get_attachment_image( $identifier, 'large', false, $options );
}
?>
</section>

<script>
	const slider   = document.querySelector( '#sliding-element' );
	const flickity = new Flickity( slider, {
		cellAlign: 'left',
		contain: true,
		freeScroll: true,
		wrapAround: true,

	} );
</script>
<!-- END Draggable slideshow to display photos -->


<?php endif; ?>

<style>
	.related-projects {
		width: 100%;
		min-width: 780px;
		height: 400px;
		margin-top: 5%;
		display: flex;
		flex-flow: row-wrap;
		justify-content: space-around;
	}
</style>

<section id="project-page-related-projects" class="related_projects"></section>

<!-- project tags here for signtypes, expertise, and industry -->
<section id="misc" class="project_misc">
<?php
if ( 'development' === ENVIRONMENT ) {
	// phpcs:disable
	/* wp_rig()->wrap_pre( wp_rig()->get_all_project_info( $post->ID ) );*/
	wp_rig()->wrap_pre( $tags );
	foreach ( $tags as $term_to_link ) {
		// echo wp_rig()->get_term_hyperlink( $term_to_link );
	}
}
	// phpcs:enable

?>
</section>

</main><!-- #single-project -->




<?php
get_footer();
