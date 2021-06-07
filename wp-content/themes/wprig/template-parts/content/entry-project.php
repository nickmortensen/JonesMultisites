<?php
/**
 * Template part for displaying a post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

global $post;
global $page;

// $archive = $args['is_archive'];
// print_pre( $args );

if ( ! is_archive() ) {
	return;
}
?>


<!-- as this is an archive page, we want to output cards for the individual project -->

<?php


$vertical_id = wp_rig()->get_project_vertical_image_id( $post->ID );
$vertical    = wp_get_attachment_image( $vertical_id, 'medium_large' );
$horizontal  = get_the_post_thumbnail( $post->ID, 'medium', [ 'class' => 'showMIDDLE'] );



?>

<div data-project-id="<?= $post->ID; ?>" class="archive_project_card single-project">

	<figure class="project-figure">
		<?php echo $vertical; ?>
		<?php echo $horizontal; ?>
		<figcaption> secondary title </figcaption>
	</figure>
	<div class="project-details">
		<h2 class="project-name"><?= get_the_title(); ?></h2>
		<button onclick="location.href='/projects/<?= $post->post_name; ?>'" class="btn-1">Continue</button>
	</div><!-- end div.project-details -->
</div><!-- end div.single-project -->

</div>


