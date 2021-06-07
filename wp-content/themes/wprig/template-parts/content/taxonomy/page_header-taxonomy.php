<?php
/**
 * Template part for displaying the section containing the header for a given taxonomy.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$classes   = [];
$classes[] = is_tax() ? 'taxonomy' : '';
$classes[] = is_tax() ? get_queried_object()->taxonomy : '';

$template_args['body_class'] = $classes;
$template_args['name']      = get_queried_object()->name;
$template_args['id']      = get_queried_object()->term_id;
$template_args['name']      = get_queried_object()->name;
?>



<?php if ( is_archive() ) : ?>
	<section class="taxonomy-description main-top">
		<?php get_template_part( 'template-parts/content/taxonomy/description', 'taxonomy', $template_args ); ?>
	</section>

	<section class="related-projects main-three">
		<?php get_template_part( 'template-parts/content/taxonomy/related', 'projects', $template_args ); ?>
	</section>

	<!-- <section class="related-gallery main-five"> -->
	<?php //get_template_part( 'template-parts/content/taxonomy/related', 'gallery', $template_args ); ?>
	<!-- </section> -->

<?php elseif ( is_404() ) : ?>
	<section class="page-header main-top">
		<h1 class="page-title">
			<?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'wp-rig' ); ?>
		</h1>
	</section><!-- .page-header -->

<?php elseif ( is_search() ) : ?>
	<section class="page-header main-top">
		<h1 class="page-title">
			<?php
			printf(
				/* translators: %s: search query */
				esc_html__( 'Search Results for: %s', 'wp-rig' ),
				'<span>' . get_search_query() . '</span>'
			);
			?>
		</h1>
	</section><!-- .page-header -->

<?php endif; ?>
