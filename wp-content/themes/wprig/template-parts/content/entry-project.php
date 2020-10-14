<?php
/**
 * Template part for displaying a post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<!-- the header for a project -->
<?php get_template_part( 'template-parts/content/entry_header', get_post_type() ); ?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>
<span class="material-icons">picture_as_pdf</span>

<?php

	if ( is_search() ) {
		get_template_part( 'template-parts/content/entry_summary', get_post_type() );
	} else {
		get_template_part( 'template-parts/content/entry_content', get_post_type() );
	}

	get_template_part( 'template-parts/content/entry_footer', get_post_type() );
	?>
</article><!-- #post-<?php the_ID(); ?> -->



<?php
if ( is_singular( get_post_type() ) ) {
	// Show post navigation only when the post type is 'post' or has an archive.
	// See @link https://developer.wordpress.org/reference/functions/get_the_post_navigation/.
	if ( 'post' === get_post_type() || get_post_type_object( get_post_type() )->has_archive ) {
		the_post_navigation(
			[
				'prev_text'          => '<div class="post-navigation-sub"><span>' . esc_html__( 'Previous Project:', 'wp-rig' ) . '</span></div>%title',
				'next_text'          => '<div class="post-navigation-sub"><span>' . esc_html__( 'Next Project:', 'wp-rig' ) . '</span></div>%title',
				'in_same_term'       => false, // Should the link be within the same taxonomy term?
				'screen_reader_text' => 'Jones Sign Company Project Navigation',
				'aria_label'         => 'More Projects from Jones Sign Company',
				'class'              => 'post-navigation',
			]
		);
	}

	// Show comments only when the post type supports it and when comments are open or at least one comment exists.
	if ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
		comments_template();
	}
}
?>
