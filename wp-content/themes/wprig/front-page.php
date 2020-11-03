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
?>


<?php
wp_rig()->print_styles( 'wp-rig-project', 'wp-rig-front-page', 'wp-rig-content', 'wp-rig-flickity' );
$fp_photos = [ 661, 662 ];

$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}

$locations = wp_rig()->get_location_ids( 75 );
?>
	<!--Begin frontpage_masthead -->
	<section class="frontpage_masthead"
	style="min-height: 35vw; background-blend-mode: darken; background: var(--blue-600) center / cover no-repeat url(<?= $bgsrcs[1]; ?>);">
	</section><!-- END frontpage_masthead -->


	<main id="primary" class="site-main">
		<?php
		// while ( have_posts() ) {
		// 	the_post();

		// 	get_template_part( 'template-parts/content/entry', get_post_type() );
		// }

		get_template_part( 'template-parts/frontpage/project_cards' );
		?>
	</main><!-- #primary -->

<?php
get_footer();
