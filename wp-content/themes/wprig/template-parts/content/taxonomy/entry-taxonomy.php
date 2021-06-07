<?php
/**
 * Template part for displaying a post
 *['body_class']
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( is_search() ) {
	$pageclass = '';
}
[
	'body_class' => $classes,
] = $args;

$args['classes']  = $classes;
$args['template'] = is_tax() ? 'taxonomy' : get_post_type();


?>


	<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?> itemscope itemtype="http://schema.org/Article">
		<meta itemprop="inLanguage" content="en-GB">


			<?php
			get_template_part( 'template-parts/content/taxonomy/entry_header', $args['template'], $args );

			if ( is_search() ) {
				get_template_part( 'template-parts/content/entry_summary', get_post_type() );
			} else {
				get_template_part( 'template-parts/content/taxonomy/entry_content', $args['template'], $args );
			}

			get_template_part( 'template-parts/content/entry_footer', $args['template'], $args );
			?>
	</article><!-- #post-<?php the_ID(); ?> -->


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

	// Show comments only when the post type supports it and when comments are open or at least one comment exists.
	if ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		comments_template();
	}
}

