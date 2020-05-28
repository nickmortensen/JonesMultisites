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

get_header( 'client' );

wp_rig()->print_styles( 'wp-rig-content' );

?>

<section class="experimental">

<img src="<?=get_the_post_thumbnail_url( $post->ID );?>" />
<h1><?php



function get_testimonial_content( $identifier ) {
	$testimonials = get_post_meta( $identifier, 'clientTestimonial', true );
	$count = count( $testimonials );
	if ( $count > 1 ) {
		foreach ( $testimonials as $testimonial ) {
			$name     = $testimonial['name'];
			$position = $testimonial['position'];
			$content  = $testimonial['testimonial'];
			$linkedin = $testimonial['linkedin'];
			echo $linkedin;
			echo "<br>";
		}
		echo "There is more than one testimonial";
	} else {
		echo "there are one or less testimonials in this client post";
	}

}

echo get_testimonial_content( $post->ID );
echo '<pre>';
print_r( count( get_post_meta( $post->ID, 'clientTestimonial', false )[0] ) );
echo '</pre>';
?>
</h1>

</section>
	<main id="primary" class="site-main">
		<?php
		if ( have_posts() ) {

			get_template_part( 'template-parts/content/page_header' );

			while ( have_posts() ) {
				the_post();

				get_template_part( 'template-parts/content/entry', get_post_type() );
			}

			if ( ! is_singular() ) {
				get_template_part( 'template-parts/content/pagination' );
			}
		} else {
			get_template_part( 'template-parts/content/error' );
		}
		?>
	</main><!-- #primary -->
<?php
get_sidebar();
get_footer();
