<?php
/**
 * WP_Rig\WP_Rig\Customized_Navigation\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Customized_Navigation;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Post;
use Walker_Nav_Menu;
use function add_action;
use function add_filter;
use function register_nav_menus;
use function esc_html__;
use function has_nav_menu;
use function wp_nav_menu;
use function wp_enqueue_script;

/**
 * Class for managing navigation menus.
 *
 * Exposes template tags:
 * * `wp_rig()->is_customized_nav_menu_active()`
 * * `wp_rig()->display_customized_nav_menu( array $args = [] )`
 */
class Component extends Walker_Nav_Menu implements Component_Interface, Templating_Component_Interface {

	const CUSTOMIZED_NAV_MENU_SLUG = 'customized';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'customized_menus';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// add_action( 'after_setup_theme', [ $this, 'action_register_customized_navigation_menu' ] );
		// add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_aos_script' ] );
		// add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_customized_navigation_script' ] );
	}
/**
 * Enqueue the animate on scroll javascript from remote.
 *
 * @return void
 */
public function action_enqueue_aos_script() {
	// Quick and dirty to remotely hosted version.
	wp_enqueue_script(
		'animate-on-scroll',
		'https://unpkg.com/aos@2.3.0/dist/aos.js',
		'jquery',
		'1',
		false
	);
	wp_script_add_data( $this->get_slug(), 'async', false );
	wp_script_add_data( $this->get_slug(), 'precache', true );
}

/**
 * Enqueue the minified customized_navigation javascript file.
 *
 * @return void
 */
public function action_enqueue_customized_navigation_script() {
	// Quick and dirty to remotely hosted version.
	// wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/customized_navigation.min.js' ) ),
	wp_enqueue_script(
		'customized-navigation',
		get_theme_file_uri( '/assets/js/customized_navigation.min.js' ),
		[ 'animate-on-scroll' ],
		'5',
		false
	);
	// Add key/value attribute pairs to the script call.
	wp_script_add_data( $this->get_slug(), 'async', false );
	wp_script_add_data( $this->get_slug(), 'precache', true );
}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [
			'is_customized_navigation_menu_active' => [ $this, 'is_customized_navigation_menu_active' ],
			'display_customized_navigation_menu'   => [ $this, 'display_customized_navigation_menu' ],
		];
	}

	/**
	 * Registers the customized navigation menus.
	 */
	public function action_register_customized_navigation_menu() {
		register_nav_menus(
			[
				static::CUSTOMIZED_NAV_MENU_SLUG => esc_html__( 'Customized', 'wp-rig' ),
			]
		);
	}

	/**
	 * Checks whether the primary navigation menu is active.
	 *
	 * @return bool True if the customized navigation menu is active, false otherwise.
	 */
	public function is_customized_navigation_menu_active() : bool {
		return (bool) has_nav_menu( static::CUSTOMIZED_NAV_MENU_SLUG );
	}

	/**
	 * Displays the customized navigation menu.
	 *
	 * @param array $args Optional. Array of arguments. See `wp_nav_menu()` documentation for a list of supported
	 *                    arguments.
	 */
	public function display_customized_navigation_menu( array $args = [] ) {
		if ( ! isset( $args['container'] ) ) {
			$args['container'] = 'ul';
		}

		$args['theme_location'] = static::CUSTOMIZED_NAV_MENU_SLUG;

		wp_nav_menu( $args );
	}
}
