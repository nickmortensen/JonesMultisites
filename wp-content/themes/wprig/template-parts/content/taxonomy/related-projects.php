<?php
/**
 * Template part for displaying projects that are related to this taxonomy;
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<div class="section-title">
	<h3><?php the_archive_title(); ?> Projects</h3>
</div>

<div class="section-content related-projects-list">


	<?php


$related = wp_rig()->get_related();
$related_count = count( $related );
for ( $i = 0; $i < $related_count; $i++ ) {

$figure_array = wp_rig()->get_related_post_linked_figure_array( $related[ $i ], 'medium_large' );

	[
		'img_id'     => $image_id,
		'img_url'    => $image_url,
		'post_title' => $project_name,
		'link'       => $project_url,
		'vertical'   => [
			'id'  => $vertical_id,
			'url' => $vertical_url,
		],
	] = $figure_array;

	$figure = <<<FIG

<figure data-terms="" class="project-preview">
	<div>
	<a href="$project_url" title="link to the $project_name profile">
		<img class="vertical" src="$vertical_url" />
		<img class="horizontal" src="$image_url" />
	</a>
	</div>
	<figcaption>
		<a href="$project_url" title="link to the $project_name profile">$project_name</a>
	</figcaption>
</figure>

FIG;
	echo $figure;
}

?>

</div>
