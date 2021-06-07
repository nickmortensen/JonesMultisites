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
global $posts;
global $page;
global $wp_query;

$qo = get_queried_object();

get_header();

wp_rig()->print_styles( 'wp-rig-content' );

$template_args = [
	'template' => 'archive',

];
?>
	<main id="primary" class="site-main">


	<?php
		if ( have_posts() ) {
			get_template_part( 'template-parts/content/page_header' );
		?>

		<section class="projects_list main_one">
		<?php
			while ( have_posts() ) {
				the_post();

				get_template_part( 'template-parts/content/entry', get_post_type(), $template_args );
			}
		}

	?>
	</section>

		<?php get_footer(); ?>
	</main>

<?php wp_footer(); ?>



</body><!--SANITY CHECK -->
</html>
