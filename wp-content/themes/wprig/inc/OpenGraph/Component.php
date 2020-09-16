<?php
/**
 * WP_Rig\WP_Rig\OpenGraph\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\OpenGraph;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Post;
use function add_action;
use function add_filter;
use function esc_html__;

/**
 * Class for managing navigation menus.
 *
 * Exposes template tags:
 * * `wp_rig()->is_primary_nav_menu_active()`
 * * `wp_rig()->display_primary_nav_menu( array $args = [] )`
 */
class Component implements Component_Interface, Templating_Component_Interface {


	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'open_graph';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [];
	}

	/**
	 * Add property to the opengraph tag for a given page.
	 *
	 * @param string $property The property attribute (minus the "og:") - 'title', 'type', 'image', 'url'.
	 * @param string $content  The content to go within the content attribute.
	 */
	public function add_og_property( $property, $content ) {
		return null;
	}

}
