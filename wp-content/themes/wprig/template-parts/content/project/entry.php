<?php
/**
 * Template part for displaying a post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$logo_url = wp_rig()->get_project_svg( $post->ID );
$svg      = wp_rig()->the_svg( $logo_url, false, 25 );
$excerpt  = has_excerpt( get_the_id() ) ? wp_strip_all_tags(get_the_excerpt() ) : '';
?>

<style>
	#project_aside {
		background: var(--gray-200);
		margin: 2vw;
		padding: 1vmin;
	}
	span.project_detail {
		font-size: calc(var(--global-font-size)*1.2px)
	}
</style>

<section class="flex row-nw justify-space-between align-start" id="project_study_content">
	<article id="project-<?php the_ID(); ?>" class="entry px-4">
		<p><?= $excerpt; ?></p>
	</article><!-- #project-<?php the_ID(); ?> -->

	<aside id="project_aside" class="flex col-nw justify-center align-center" >
		<?= $svg ; ?> <!-- SVG Logo -->

		<span class="project_detail"><?= wp_rig()->get_project_detail( $post->ID, 'client' ); ?></span>
		<span class="project_detail"><?= wp_rig()->get_project_detail( $post->ID, 'tease' ); ?></span>
		<span class="project_detail" hidden><?= wp_rig()->get_project_detail( $post->ID, 'job_id' ); ?></span>
		<span class="project_detail"><?= wp_rig()->get_project_detail( $post->ID, 'year_complete' ); ?></span>
	</aside>
</section><!-- end section#project_study_content -->
<?php
// if ( is_singular( get_post_type() ) ) {
	// Show post navigation only when the post type is 'post' or has an archive.
// 	if ( 'post' === get_post_type() || get_post_type_object( get_post_type() )->has_archive ) {
// 		the_post_navigation(
// 			[
// 				'prev_text' => '<div class="post-navigation-sub"><span>' . esc_html__( 'Previous:', 'wp-rig' ) . '</span></div>%title',
// 				'next_text' => '<div class="post-navigation-sub"><span>' . esc_html__( 'Next:', 'wp-rig' ) . '</span></div>%title',
// 			]
// 		);
// 	}

// 	// Show comments only when the post type supports it and when comments are open or at least one comment exists.
// 	if ( post_type_supports( get_post_type(), 'comments' ) && ( comments_open() || get_comments_number() ) ) {
// 		comments_template();
// 	}
// }
$slides      = wp_rig()->get_project_slideshow( get_the_id() );
// $slides      = wp_rig()->get_project_slideshow( get_the_id() ) || 'nothing';
// if ($slides && ( 1 < count( $slides ) ) ) {
// 	wp_rig()->print_styles( 'project' );
// ?>
<pre>
	<?php
	if ( ! get_post_meta( get_the_id(), 'projectImagesSlideshow', true ) )
	{
		echo 'slidehow is false';
	} else
	{
		echo count( get_post_meta( get_the_id(), 'projectImagesSlideshow', true ) );
	}

	?>
</pre>
<!-- Only output a touchscreen slider featuring photos of the project if there are 2 or more photos included in the Rectangular Images field. -->

<?php if ( $slides ): ?>
<?php wp_rig()->print_styles( 'flickity' ); ?>
<div id="touchscreen_slider" class="project">
<style>
	figure.flickity {
		min-width:600px;
	}

</style>
<?php
	$i = 0;
	foreach( $slides as $id => $url ) {
		$caption = '' !== get_the_excerpt( $id ) ? '<figcaption><span class="caption">' . get_the_excerpt( $id ) . '</span></figcaption>' : '';
		$item = wp_get_attachment_image( $id, 'large', false, [ 'data-caption' => $caption, 'class' => 'flickity-image' ] );
		echo $item;
		$i++;

	}
?>

</div>


<script>
	const slider   = document.querySelector( '#touchscreen_slider' );
	const flickity = new Flickity( slider, {
		cellAlign: 'left',
		contain: true,
		freeScroll: true,
		wrapAround: true
	} );
</script>


<?php endif; ?>
