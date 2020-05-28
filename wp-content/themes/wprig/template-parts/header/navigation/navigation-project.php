<?php
/**
 * Template part for displaying the header navigation menu on a Project post type.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( ! wp_rig()->is_project_nav_menu_active() ) {
	return;
}
