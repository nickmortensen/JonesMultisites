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
global $post_type;
global $template;

echo $post_type;

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
dump_debug($post_type);
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
get_header();

wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-project', 'wp-rig-flickity' );
?>

<main id="single-item" class="site-main project">

<style>

	.single_project_profile {
		background: center / cover no-repeat url(<?= $featured_src; ?>), var(--blue-700);
		background-blend-mode: multiply;
	}
.svg-contain {
	background: #efefef;
	width: 300px;
	height: 300px;
	border-radius: 50%;
}

.client_logo {
	width: 10vw;
	height: 10vw;
}

</style>

<section class="single_project_profile">
<div>
	<?php the_title( '<h1 class="light-text">', '</h1>' ); ?>
	<span class="light-text excerpt project_excerpt"><?= $excerpt; ?></span>
</div>
<object data="<?= $svg; ?>" type="image/svg+xml" class="client_logo">Client Logo</object>
</section><!-- .entry-header -->

<?php
if ( 'development' === ENVIRONMENT ) {
	get_template_part( 'template-parts/content/admin_tweaks' );
}
?>


<style>
	figure.flickity {
		min-width:600px;
	}

	#sliding-element {
		grid-column: 1 / -1;
	}

	#sliding-element img.flickity-image::after {
		content: attr(data-caption);
	}
</style>

<?php if ( $slideshow_images ) : ?>
<div id="sliding-element">

<?php
	foreach ( $slideshow_images as $identifier => $url ) {
		$caption = '' !== get_the_excerpt( $id ) ? get_the_excerpt( $identifier ) : '';
		$options = [
			'data-caption' => $caption,
			'class'        => 'flickity-image',
		];
		echo wp_get_attachment_image( $identifier, 'large', false, $options );
}
?>

</div>


<script>
	const slider   = document.querySelector( '#sliding-element' );
	const flickity = new Flickity( slider, {
		cellAlign: 'left',
		contain: true,
		freeScroll: true,
		wrapAround: true
	} );
</script>

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


<section class="related-projects"></section>
</main><!-- #single-project -->





<?php if ( 'development' !== ENVIRONMENT ) : ?>
	<script>
	const bgBlendOptions = document.querySelector( '#blend' );
	bgBlendOptions.addEventListener( 'change', function(e) {
		console.log( 'project header blend mode is now', e.target.value );
		document.querySelector( '.single_project_profile' ).style.backgroundBlendMode = e.target.value;
		document.querySelector( '#vertical' ).style.backgroundBlendMode = e.target.value;
	} );

	const textBlendOptions = document.querySelector( '#headingblend' );
	textBlendOptions.addEventListener( 'change', function(e) {
		document.querySelector('.hide-on-wide h1').style.mixBlendMode = e.target.value;
	})
</script>


<?php endif; ?>


<?php
get_footer();
