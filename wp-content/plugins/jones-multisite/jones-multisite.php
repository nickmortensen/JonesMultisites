<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/user/nickmortensen
 * @since             1.0.0
 * @package           Jones_Multisite
 *
 * @wordpress-plugin
 * Plugin Name:       Jones Multisite Plugin
 * Plugin URI:        https://github.com/nickmortensen/jones-multisite
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Nick Mortensen
 * Author URI:        https://github.com/user/nickmortensen
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jones-multisite
 * Domain Path:       /languages
 */

namespace JonesMulti;

use JonesMulti\Deactivator as Deactivate;
// use JonesMulti\Activator as Activate;
use JonesMulti\Jones_Multisite as Primary;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'JONES_MULTISITE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jones-multisite-activator.php
 */
function activate_jones_multisite() {
	JonesMulti\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jones-multisite-deactivator.php
 */
function deactivate_jones_multisite() {
	Deactivate::deactivate();
}

register_activation_hook( __FILE__, 'activate_jones_multisite' );
register_deactivation_hook( __FILE__, 'deactivate_jones_multisite' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jones-multisite.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jones_multisite() {

	$plugin = new Primary();
	$plugin->run();

}
run_jones_multisite();
