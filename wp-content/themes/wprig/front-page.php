<?php
/**
 * Render your site front page, whether the
 * front page displays the blog posts index or a static page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();


// Use grid layout if blog index is displayed.
if ( is_home() ) {
	wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-front-page' );
} else {
	wp_rig()->print_styles( 'wp-rig-content' );
}
?>


<?php
wp_rig()->print_styles( 'wp-rig-project', 'wp-rig-content', 'wp-rig-flickity' );
$fp_photos = [ 661, 662 ];
$locations = wp_rig()->get_location_ids( 75, 72 );



$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}

$locations = wp_rig()->get_location_ids( 75 );
?>


<main id="primary" class="site-main frontpage">

		<?php
		// phpcs:disable
		// while ( have_posts() ) {
		// 	the_post();

		// 	get_template_part( 'template-parts/content/entry', get_post_type() );
		// }
		// phpcs:enable
		get_template_part( 'template-parts/frontpage/masthead' );
		get_template_part( 'template-parts/frontpage/form-experiment' );

		get_template_part( 'template-parts/frontpage/company-info' );
		get_template_part( 'template-parts/frontpage/project-cards' );
		?>



	</main><!-- main -->



<?php
get_footer();
