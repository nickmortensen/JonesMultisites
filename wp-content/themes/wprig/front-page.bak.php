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

// Use grid layout if blog index is displayed.
if ( is_home() ) {
	wp_rig()->print_styles( 'wp-rig-project', 'wp-rig-content', 'wp-rig-front-page' );
} else {
	wp_rig()->print_styles( 'wp-rig-content' );
}
?>
	<main id="primary" class="site-main frontpage">
		<?php get_template_part( 'template-parts/frontpage/locations' ); ?>
		<?php get_template_part( 'template-parts/frontpage/project-cards' ); ?>
		<?php get_template_part( 'template-parts/frontpage/contact' ); ?>

	</main><!-- #primary -->




<?php
get_template_part( 'template-parts/developer/scrolltotop' );
get_footer();
