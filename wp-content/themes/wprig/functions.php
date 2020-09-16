<?php
/**
 * WP Rig functions and definitions
 *
 * This file must be parseable by PHP 5.2.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package wp_rig
 */

define( 'WP_RIG_MINIMUM_WP_VERSION', '4.5' );
define( 'WP_RIG_MINIMUM_PHP_VERSION', '7.0' );

// Bail if requirements are not met.
if ( version_compare( $GLOBALS['wp_version'], WP_RIG_MINIMUM_WP_VERSION, '<' ) || version_compare( phpversion(), WP_RIG_MINIMUM_PHP_VERSION, '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

// Include WordPress shims.
require get_template_directory() . '/inc/wordpress-shims.php';

// Setup autoloader (via Composer or custom).
if ( file_exists( get_template_directory() . '/vendor/autoload.php' ) ) {
	require get_template_directory() . '/vendor/autoload.php';
} else {
	/**
	 * Custom autoloader function for theme classes.
	 *
	 * @access private
	 *
	 * @param string $class_name Class name to load.
	 * @return bool True if the class was loaded, false otherwise.
	 */
	function _wp_rig_autoload( $class_name ) {
		$namespace = 'WP_Rig\WP_Rig';

		if ( strpos( $class_name, $namespace . '\\' ) !== 0 ) {
			return false;
		}

		$parts = explode( '\\', substr( $class_name, strlen( $namespace . '\\' ) ) );

		$path = get_template_directory() . '/inc';
		foreach ( $parts as $part ) {
			$path .= '/' . $part;
		}
		$path .= '.php';

		if ( ! file_exists( $path ) ) {
			return false;
		}

		require_once $path;

		return true;
	}
	spl_autoload_register( '_wp_rig_autoload' );
}

// Load the `wp_rig()` entry point function.
require get_template_directory() . '/inc/functions.php';

// Initialize the theme.
call_user_func( 'WP_Rig\WP_Rig\wp_rig' );


/**
 * Distinguish which site we are dealing with.
 *
 * @param int $id the 'blog' id as assigned by WordPress.
 */
function get_location_by_blog_id( $id ) {
	//phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
	$locations = [
		[ 'location' => 'national', 'url' => 'www.jonessign.io', 'slug' => 'nat', 'term_id' => 71 ],
		[ 'location' => 'green bay', 'url' => 'greenbay.jonessign.io', 'slug' => 'grb', 'term_id' => 72 ],
		[ 'location' => 'philadelphia', 'url' => 'philadelphia.jonessign.io', 'slug' => 'phl', 'term_id' => 64 ],
		[ 'location' => 'denver', 'url' => 'denver.jonessign.io', 'slug' => 'den', 'term_id' => 75 ],
		[ 'location' => 'los angeles', 'url' => 'losangeles.jonessign.io', 'slug' => 'lax', 'term_id' => 70 ],
		[ 'location' => 'san diego', 'url' => 'sandiego.jonessign.io', 'slug' => 'san', 'term_id' => 69 ],
		[ 'location' => 'miami', 'url' => 'miami.jonessign.io', 'slug' => 'mia', 'term_id' => 73 ],
		[ 'location' => 'minneapolis', 'url' => 'minneapolis.jonessign.io', 'slug' => 'msp', 'term_id' => 74 ],
		[ 'location' => 'richmond', 'url' => 'richmond.jonessign.io', 'slug' => 'ric', 'term_id' => 62 ],
		[ 'location' => 'tampa', 'url' => 'tampa.jonessign.io', 'slug' => 'tpa', 'term_id' => 68 ],
		[ 'location' => 'las vegas', 'url' => 'vegas.jonessign.io', 'slug' => 'las', 'term_id' => 61 ],
		[ 'location' => 'virginia beach', 'url' => 'virginiabeach.jonessign.io', 'slug' => 'vab', 'term_id' => 67 ],
		[ 'location' => 'juarez', 'url' => 'juarez.jonessign.io', 'slug' => 'mxj', 'term_id' => 66 ],
	];

	switch ( $id ) {
		case 1:
			$site = $locations[0];
			break;
		case 2:
			$site = $locations[1];
			break;
		case 3:
			$site = $locations[2];
			break;
		case 4:
			$site = $locations[3];
			break;
		case 5:
			$site = $locations[4];
			break;
		case 6:
			$site = $locations[5];
			break;
		case 7:
			$site = $locations[6];
			break;
		case 8:
			$site = $locations[7];
			break;
		case 9:
			$site = $locations[8];
			break;
		case 10:
			$site = $locations[9];
			break;
		case 11:
			$site = $locations[10];
			break;
		case 12:
			$site = $locations[11];
			break;
		case 13:
			$site = $locations[12];
			break;
		default:
			$site = $locations[0];
	}
	return $site;
	//phpcs:enable
}
