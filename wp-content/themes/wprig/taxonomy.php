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
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wp_rig
 *
 * @updated 05_May_2021
 */

namespace WP_Rig\WP_Rig;

[
	'term_id'     => $term_identifier,
	'name'        => $term_name,
	'count'       => $count,
	'description' => $description,
	'slug'        => $slug,
	'taxonomy'    => $taxonomy,
] = get_object_vars( get_queried_object() );

$header_images = wp_rig()->get_term_header_images( $term_identifier, 'medium' );

$classes   = [];
$classes[] = is_tax() ? 'taxonomy' : '';
$classes[] = is_tax() ? get_queried_object()->taxonomy : '';
$classes[] = is_tax() ? get_queried_object()->slug : '';

$template_args['body_class'] = $classes;

global $wp_query;

// print_pre( $wp_query );
// print_pre( get_queried_object() );
$square = wp_rig()->get_term_images();

// get_header( 'taxonomy' );
get_header();

wp_rig()->print_styles( 'wp-rig-content' );

$taxname = is_tax() ? 'is taxonomy' : 'not taxonomy';

?>
	<main id="primary" <?php post_class( $classes ); ?>>

	<?php

		if ( is_tax() ) {

			get_template_part( 'template-parts/content/taxonomy/page_header', 'taxonomy', $template_args );


			while ( have_posts() ) {
				//get_template_part( 'template-parts/content/entry', 'taxonomy', $template_args );
				the_post();

			}

			if ( ! is_singular() ) {
				get_template_part( 'template-parts/content/pagination' );
			}
		} else {
			get_template_part( 'template-parts/content/error' );
		}
		?>

		<?php get_footer(); ?>
	</main>

	<?php wp_footer(); ?>

	</body><!--SANITY CHECK -->
</html>
