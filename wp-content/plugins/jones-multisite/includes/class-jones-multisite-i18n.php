<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/user/nickmortensen
 * @since      1.0.0
 *
 * @package    Jones_Multisite
 * @subpackage Jones_Multisite/includes
 */

namespace JonesMulti;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Jones_Multisite
 * @subpackage Jones_Multisite/includes
 * @author     Nick Mortensen <nmortensen@jonessign.com>
 */
class Internationalization {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'jones-multisite',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
