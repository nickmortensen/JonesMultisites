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

	<main id="single-item" class="project">

		<header class="entry-header"></header><!-- .entry-header -->

	</main><!-- #single-project -->



	<?php
if ( 'development' !== ENVIRONMENT ) {
	get_template_part( 'template-parts/content/admin_tweaks' );
}
?>

<?php if ( 'development' !== ENVIRONMENT ) : ?>
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
get_footer();
