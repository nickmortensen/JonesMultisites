<?php
/**
 * Render your site front page, whether the front page displays the blog posts index or a static page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header( 'experimental' );

// Use grid layout if blog index is displayed.
if ( is_home() ) {
	wp_rig()->print_styles( 'wp-rig-project', 'wp-rig-content', 'wp-rig-front-page' );
} else {
	wp_rig()->print_styles( 'wp-rig-content' );
}
?>
	<main data-gridarea="main" class="frontpage">

	<!-- <?php //get_template_part( 'template-parts/frontpage/safety' ); ?> -->
	<?php get_template_part( 'template-parts/frontpageoptions/projectcards' ); ?>
	<?php get_template_part( 'template-parts/frontpageoptions/contact' ); ?>
	<?php get_template_part( 'template-parts/frontpageoptions/locations' ); ?>
	</main>

		<!-- always on widget to show me how wide the screen is -->



<?php
get_footer( 'experimental' );
