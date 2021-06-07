<?php
/**
 * Template part for displaying a post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$classes = [];
if ( is_search() ) {
	$classes[] = 'search';
}

if ( is_archive() ) {
	$classes[] = 'archive';
}

$classes = [ 'entry', 'main-one' ];
if ( is_tax() ) {
	$classes[] = 'taxonomy';
}
$get_template_args = $classes;

global $wpdb;
global $post;

?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $classes ); ?> itemscope itemtype="http://schema.org/Article">

	<?php
			get_template_part( 'template-parts/content/entry_header', get_post_type(), $get_template_args );

			if ( is_search() ) {
				get_template_part( 'template-parts/content/entry_summary', get_post_type(), $get_template_args );
			} else {
				get_template_part( 'template-parts/content/entry_content', get_post_type(), $get_template_args );
			}

			get_template_part( 'template-parts/content/entry_footer', get_post_type(), $get_template_args );
			?>

</article><!-- #post-<?php the_ID(); ?> -->


