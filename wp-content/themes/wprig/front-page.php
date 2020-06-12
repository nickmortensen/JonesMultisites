<?php
/**
 * Render your site front page, whether the front page displays the blog posts index or a static page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();
$blog_identifier = get_current_blog_id();

// Use grid layout if blog index is displayed.
if ( is_home() ) {
	wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-front-page' );
} else {
	wp_rig()->print_styles( 'wp-rig-content' );
}
?>
<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/entry', get_post_type() );
	}
	get_template_part( 'template-parts/content/pagination' );
	?>
</main><!-- #primary -->

<pre>
<?php

$args = [
	'post_type'   => 'project',
	'post_status' => 'publish',
];

$projects = new \WP_QUERY( $args );
$image = 406;
$image = 7;
print_r( wp_rig()->get_image_rating( $image ));
echo "<br>";
print_r( rest_url( 'wp/v2/' ) );
echo "<br>";
$termid = 17;
// print_r( wp_rig()->check_signtype_images( $termid ) );

$sizes = [ 'cinematic', 'vertical', 'square', 'rectangular' ];
$needs = [];
// foreach ( $sizes as $size ) {
	print_r( wp_rig()->check_signtype_images( $termid ) );
// 	if( get_term_meta( $termid, 'signtype' . ucfirst( $size ), true ) ) {
// 		continue;
// 	};
// 	$needs[] = $size;
// }

// if ( $needs ) {
// 	print_r( ' we need to attach the following image types: ' . implode( ',', $needs ) );
// } else {
// 	print_r ('nothing to attach' );
// }
?>
</pre>


<?php
get_footer();

/**
 * LCA ARENA PHOTO https://commons.wikimedia.org/wiki/User:Adam_Bishop
 * evening
 * https://commons.wikimedia.org/wiki/User:Michael_Barera
 */
