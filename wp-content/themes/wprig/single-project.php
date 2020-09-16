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

$logo_url = wp_rig()->get_project_svg( $post->ID );
$svg      = wp_rig()->the_svg( $logo_url, false, 25 ); // false means return, rather than echo the img tag and 25 is the width in vmin.
$excerpt  = has_excerpt( get_the_id() ) ? wp_strip_all_tags( get_the_excerpt() ) : '';
$slides   = wp_rig()->get_project_slideshow( get_the_id() );
?>

<style>
	#project_aside {
		margin: 2vw;
		padding: 1vmin;
	}

	#project_study_content p {
		font-size: clamp(18px, 4vmin, 28px);
	}
</style>



<section class="flex row-nw justify-space-between align-start" id="project_study_content">
	<article id="project-<?php the_ID(); ?>" class="entry px-4">
		<p><?= $excerpt; ?></p>
	</article><!-- #project-<?php the_ID(); ?> -->

	<aside id="project_aside" class="flex col-nw justify-center align-center" >
		<?= $svg; ?> <!-- SVG Logo -->

		<span class="project_detail"><?= wp_rig()->get_project_detail( $post->ID, 'client' ); ?></span>
		<span class="project_detail"><?= wp_rig()->get_project_detail( $post->ID, 'tease' ); ?></span>
		<span class="project_detail" hidden><?= wp_rig()->get_project_detail( $post->ID, 'job_id' ); ?></span>
		<span class="project_detail"><?= wp_rig()->get_project_detail( $post->ID, 'year_complete' ); ?></span>
	</aside>
</section><!-- end section#project_study_content -->


<!-- Only output a touchscreen slider featuring photos of the project if there are 2 or more photos included in the Rectangular Images field. -->
<?php

if ( $slides ) {
	get_template_part( 'template-parts/content/touch-slider' );
}

?>
<!--
<pre>
	<?php print_r( $slides ); ?>
</pre> -->
	<main id="primary"> </main><!-- #primary -->



<?php
get_footer();
