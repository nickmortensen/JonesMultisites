<?php
/**
 * The `wp_rig()` function.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/**
 * Provides access to all available template tags of the theme.
 *
 * When called for the first time, the function will initialize the theme.
 *
 * @return Template_Tags Template tags instance exposing template tag methods.
 */
function wp_rig() : Template_Tags {
	static $theme = null;

	if ( null === $theme ) {
		$theme = new Theme();
		$theme->initialize();
	}

	return $theme->template_tags();
}

/**
 * Wrap intput in '<pre>' tags and print_r.
 *
 * @param mix $input Anything you want printed and wrapped in a pre tag.
 */
function wrap( $input ) {
	echo '<pre>';
	var_dump( $input ); //phpcs:ignore
	echo '</pre>';
}

