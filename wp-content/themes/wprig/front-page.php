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

echo '<pre>';
print_r( wp_rig()->get_location_city_photo_url( $blog_identifier ) );
echo '</pre>';
// Use grid layout if blog index is displayed.
if ( is_home() ) {
	wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-front-page' );
} else {
	wp_rig()->print_styles( 'wp-rig-content' );
}
echo '<pre>';
// print_r( $GLOBALS['wpdb'] );
echo '<br>';
print_r( get_main_network_id() );
echo '<br>';
print_r( get_current_network_id() );
echo '<br>';
print_r( get_network() );
echo '<br>';
print_r( network_home_url() );
echo '</pre>';
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
get_footer();
