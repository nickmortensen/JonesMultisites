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

<?php

echo '<pre>';
echo $_SERVER['HTTP_USER_AGENT'];
echo '<br>';
$agentmatch = wp_rig()->user_agent_matches( [ 'opera' ] );

echo '</pre>';
get_footer();
