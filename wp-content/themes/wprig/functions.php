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

/* @link https://www.wpbeginner.com/wp-tutorials/how-to-increase-the-maximum-file-upload-size-in-wordpress/ */
@ini_set( 'upload_max_size', '64M' );
@ini_set( 'post_max_size', '64M' );
@ini_set( 'max_execution_time', '300' );


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
 * A quicker way to get to the raw php output
 *
 * @param string $content - The content to output inside the pre tag.
 * @param bool   $type_pr  use print_r or var_dump function true is printR false is var_dump.
 * $type_pr
 */
function show( $content, $type_pr = true ) {
	echo '<pre>';
	if ( $type_pr ) {
		print_r( $content );
	} else {
		var_dump( $content );
	}
	echo '</pre>';
}

// Load the `wp_rig()` entry point function.
require get_template_directory() . '/inc/Nicer.php';
/**
 * Nicely prints human-readable information about a value.
 * Note: This funciton is provided for compatibility reasons with earlier version.
 * You don't need this file if you plan on using the class directly.
 *
 * @param mixed $value The value to print.
 * @param bool  $return (Optional) Return printed HTML instead of writing it out (default is false).
 *
 * @return string If $return is true, the rendered HTML otherwise null.
 */
function nice_r( $value, $return = false ) {
	$n = new Nicer( $value );
	return $return ? $n->generate() : $n->render();
}

require get_template_directory() . '/inc/Clearer.php';
/**
 * Nicely prints human-readable information about a value.
 * Note: This funciton is provided for compatibility reasons with earlier version.
 * You don't need this file if you plan on using the class directly.
 *
 * @param mixed $value The value to print.
 * @param bool $return (Optional) Return printed HTML instead of writing it out (default is false).
 * @return string If $return is true, the rendered HTML otherwise null.
 */
function clearer( $value, $return = false ) {
	$n = new Clearer( $value );
	return $return ? $n->generate() : $n->render();
}

/**
 * Better Debugging compared to print_r.
 *
 * @param any  $mixed    mandatory any data type.
 * @param bool $die      optional  , default true.
 * @param bool $var_dump optional  , default false.
 *
 * returns preformatted print_r or vardump output.
 */
function print_pre( $mixed, $die = true, $var_dump = false ) {
	echo '<pre>';
	if ( $var_dump ) {
		var_dump( $mixed );
	} else {
		print_r( $mixed );
	}
	echo '<pre>';

	if ( $die ) {
		die( '__END__' );
	}
}


