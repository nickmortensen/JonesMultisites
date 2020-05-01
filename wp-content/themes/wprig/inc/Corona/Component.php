<?php
/**
 * WP_Rig\WP_Rig\Editor\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Editor;

use WP_Rig\WP_Rig\Component_Interface;
use function add_action;
use function add_theme_support;
use function wp_enqueue_script;
use function wp_localize_script;

/**
 * Class for integrating with the block editor.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'corona';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_corona_script' ] );
	}

	/**
	 * Enqueues a script that improves navigation menu accessibility.
	 */
	public function action_enqueue_corona_script() {

		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		// Enqueue the navigation script. The last element asks whether to load the script within the footer. We don't want that.
		wp_enqueue_script(
			'wp-rig-corona',
			get_theme_file_uri( '/assets/js/corona.min.js' ),
			[],
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/corona.min.js' ) ),
			false
		);

		/*
		Allows us to add the js right within the module.
		Setting 'precache' to true means we are loading this script in the head of the document.
		By setting 'async' to true,it tells the browser to wait until it finishes loading to run the script.
		'Defer' would mean wait until EVERYTHING is done loading to run the script.
		*/
		wp_script_add_data( 'wp-rig-corona', 'async', true );
		wp_script_add_data( 'wp-rig-corona', 'precache', true );
		wp_localize_script(
			'wp-rig-corona',
			'setup',
			[
				'geoId'   => '55009',
				'url' => 'https://coronavirus.data.json', // obviously wrong, but this is just for the purpose.
			]
		);
	}


}
