<?php
/**
 * Template part for displaying projects that are related to this taxonomy;
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>




	<?php
$related = wp_rig()->get_related();
for ( $i = 0; $i < count( $related ); $i++ ) {
	$figure_array = wp_rig()->get_related_post_linked_figure_array( $related[ $i ], 'medium_large' );

	[
		'img_url'    => $image_url,
		'post_title' => $project_name,
		'link'       => $project_url,
		'vertical'   => [
			'id'  => $vertical_id,
			'url' => $vertical_url,
		],
	 ] = $figure_array;

	$figure = <<<FIG

<figure>
<img src="$vertical_url" />
	<figcaption>
	<a href="$project_url" title="link to the $project_name profile">$project_name</a>;
	</figcaption>
</figure>


FIG;
	echo $figure;
}

?>


