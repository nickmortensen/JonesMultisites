<?php
/**
 * Template part for displaying the page section of the currently displayed page
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$classes   = [];
$classes[] = is_tax() ? 'taxonomy' : '';
$classes[] = is_tax() ? get_queried_object()->taxonomy : '';

$template_args['body_class'] = $classes;

if ( is_404() ) {
	?>
	<section class="page-header">
		<h1 class="page-title">
			<?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'wp-rig' ); ?>
		</h1>
	</section><!-- .page-header -->

	<?php
} elseif ( is_home() && ! have_posts() ) {
	?>
	<section class="page-header">
		<h1 class="page-title">
			<?php esc_html_e( 'Nothing Found', 'wp-rig' ); ?>
		</h1>
	</section><!-- .page-header -->
	<?php
} elseif ( is_home() && ! is_front_page() ) {
	?>
	<section class="page-header">
		<h1 class="page-title">
			<?php single_post_title(); ?>
		</h1>
		<span class="material_icons">picture_as_pdf</span>
	</section><!-- .page-header -->

	<?php
} elseif ( is_search() ) {
	?>
	<section class="page-header">
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

	<?php
} elseif ( is_archive() ) {
?>

	<section class="archive_page page-header main_top">

	<?php
		the_archive_title( '<h1 class="page-title">', '</h1>' );
		the_archive_description( '<div class="archive-description">', '</div>' );
	?>
	</section><!-- .page-header -->
	<?php// get_template_part( 'template-parts/content/description', 'taxonomy', $template_args ); ?>


<?php
}


