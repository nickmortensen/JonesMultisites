<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/user/nickmortensen
 * @since      1.0.0
 *
 * @package    Jones_Multisite
 * @subpackage Jones_Multisite/includes
 */

namespace JonesMulti;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Jones_Multisite
 * @subpackage Jones_Multisite/includes
 * @author     Nick Mortensen <nmortensen@jonessign.com>
 */
class Jones_Multisite_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}

}
