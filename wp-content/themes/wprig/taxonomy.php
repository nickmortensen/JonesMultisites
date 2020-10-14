<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $template;

get_header();
wrap( get_queried_object() );
wrap( basename( $template ) );
wrap( get_post_type() );


wp_rig()->print_styles( 'wp-rig-content' );


?>

<main id="primary" class="site-main">
		<?php

		echo 'The following posts are tied to this taxonomy: ';
		while ( have_posts() ) {
			the_post();
			echo get_the_id() . "\r";
		}
		?>
	</main><!-- #primary -->

<section id="single-taxonomy-content">
<h2>this section</h2>

</section>
	<main id="primary" class="site-main" style="color: var(--indigo-600);">
	</main><!-- #primary -->
<?php

wrap( get_queried_object() );

if ( is_super_admin() ) {
	edit_tag_link();
}
get_footer();
