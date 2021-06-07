<?php
/**
 * Template part for displaying a post's content.
 *
 * Last Update: 05_May_2021
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( is_tax() ) {
	$classes[] = 'taxonomy';
}

the_content(
	sprintf(
		wp_kses(
			/* translators: %s: Name of current post. Only visible to screen readers */
			__( 'Continue reading<span class="screen-reader-text hide-on-print"> "%s"</span>', 'wp-rig' ),
			array(
				'span' => array(
					'class' => array(),
				),
			)
		),
		get_the_title()
	)
);

// Presumes we are in a situation where a post is so long that a  "continue reading' link is merited.
wp_link_pages(
	[
		'before'           => '<div class="page-links hide-on-print">' . esc_html__( 'Pages:', 'wp-rig' ),
		'after'            => '</div>',
		'link_before'      => '',
		'link_after'       => '',
		'aria_current'     => 'page', // Options include 'step', 'location', 'date', 'time', 'true', 'false','page'.
		'next_or_number'   => 'number',
		'separator'        => '',
		'nextpagelink'     => 'Next Page',
		'previouspagelink' => 'Previous Page',
		'pagelink'         => 'Page %',
		'echo'             => true,
	]
);
