<?php
/**
 * Template part for displaying a post's content.
 *
 * Last Update: 05_May_2021
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

the_content(
	sprintf(
		wp_kses(
			/* translators: %s: Name of current post. Only visible to screen readers */
			__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'wp-rig' ),
			[
				'span' => [
					'class' => [],
				],
			]
		),
		get_the_title()
	)
);

wp_link_pages(
	[
		'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'wp-rig' ),
		'after'  => '</div>',
	]
);
?>

