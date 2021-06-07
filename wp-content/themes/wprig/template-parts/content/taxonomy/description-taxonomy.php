<?php
/**
 * The title of the taxonomy as well as an article based on the longer description of the taxonomy.

 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

[
	'body_class' => $classes,
] = $args;


global $template;
$args['classes']  = $classes;
$args['template'] = is_tax() ? 'taxonomy' : get_post_type();

?>



	<!-- begin taxonomy name -->
	<div class="section-title">
		<?php the_archive_title( '<h1>', '</h1>' ); ?>
	</div>

	<!-- begin taxonomy writeup -->
	<div class="section-content">
		<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?> itemscope itemtype="http://schema.org/Article">
			<meta itemprop="inLanguage" content="en-GB">
			<?php wp_rig()->the_term_description_indepth(); ?>
		</article><!-- #post-<?php the_ID(); ?> -->
	</div>




	<?php
if ( is_singular( get_post_type() ) ) {
	// Show post navigation only when the post type is 'post' or has an archive.
	if ( 'post' === get_post_type() || get_post_type_object( get_post_type() )->has_archive ) {
		the_post_navigation(
			array(
				'prev_text' => '<div data-explanation="previous" class="post-navigation-sub">
									<span data-text="previous" class="material-icons"> arrow_right_alt </span> <span>' . esc_html__( 'Previous:', 'wp-rig' ) . '</span></div>%title',
				'next_text' => '<div data-explanation="next" class="post-navigation-sub">
				<span data-text="next" class="material-icons"> arrow_right_alt </span>
				<span>' . esc_html__( 'Next:', 'wp-rig' ) . '</span>
				</div>%title',
			)
		);
	}

}

