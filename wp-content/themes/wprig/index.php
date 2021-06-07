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

get_header();




wp_rig()->print_styles( 'wp-rig-content' );

$template_args = [
	'template' => 'index',
];
?>


<div class="content-wrapper">

	<!-- navigation bar which stays affixed to top on scroll and moves to the left of the page on screens larger thant 1400px -->
	<?php echo wp_rig()->get_general_header( $header_arguments ); ?>

	<!-- page loading feature for when we are using ajax to get new content -->
	<div class="load-indicator"></div><!-- end div.load-indicator -->

	<section class="content-blocks-wrapper">
		<div class="block-content block-left"></div><!-- end .block-content.block-left -->

		<div class="block-content block-right">

			<!-- main div closes after footer in footer.php fil -->
			<main id="primary" class="site-main">
					<?php
					if ( have_posts() ) {

						get_template_part( 'template-parts/content/page_header', '', $template_args );

						while ( have_posts() ) {
							the_post();

							get_template_part( 'template-parts/content/entry', get_post_type(), $template_args );
						}

						if ( ! is_singular() ) {
							get_template_part( 'template-parts/content/pagination', '', $template_args );
						}
					} else {
						get_template_part( 'template-parts/content/error', '', $template_args );
					}
					?>

				</main>


		</div><!-- end div.block-content block-right -->



	</section><!-- end div.content-blocks-wrapper -->


	<?php get_footer(); ?>



</div><!-- end div.content-wrapper -->

<?php wp_footer(); ?>



</body><!--SANITY CHECK -->
</html>
