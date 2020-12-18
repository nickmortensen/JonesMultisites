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
 * Class for managing navigation menus.
 */
// class SideMenuWalker extends Walker_Nav_Menu {

// Set the properties of the element which give the ID of the current item and its parent
// var $db_fields = array( 'parent' => 'parent_id', 'id' => 'object_id' );

// **
// * Starts the list before the elements are added.
// *
// * @since 3.0.0
// *
// * @see Walker::start_lvl().
// *
// * @param string   $output Used to append additional content (passed by reference).
// * @param int      $depth  Depth of menu item. Used for padding.
// * @param stdClass $args   An object of wp_nav_menu() arguments.
// */
// public function start_lvl( &$output, $depth = 0, $args = null ) {
// if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
// $t = '';
// $n = '';
// } else {
// $t = "\t";
// $n = "\n";
// }
// $indent = str_repeat( $t, $depth );

// The default class.
// $classes = array( 'sub-menu' );

// **
// * Filters the CSS class(es) applied to a menu list element.
// *
// * @since 4.8.0
// *
// * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
// * @param stdClass $args    An object of `wp_nav_menu()` arguments.
// * @param int      $depth   Depth of menu item. Used for padding.
// */
// $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
// $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

// $output .= "{$n}{$indent}<ul$class_names>{$n}";
// }

// **
// * Ends the list of after the elements are added.
// *
// * @since 3.0.0
// *
// * @see Walker::end_lvl()
// *
// * @param string   $output Used to append additional content (passed by reference).
// * @param int      $depth  Depth of menu item. Used for padding.
// * @param stdClass $args   An object of wp_nav_menu() arguments.
// */
// public function end_lvl( &$output, $depth = 0, $args = null ) {
// if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
// $t = '';
// $n = '';
// } else {
// $t = "\t";
// $n = "\n";
// }
// $indent  = str_repeat( $t, $depth );
// $output .= "$indent</ul>{$n}";
// }


// **
// * Start element for the walker.
// *
// * @param string  $output What already exists. Passed by reference to the Walker Class that exists stadard in WordPress.
// * @param WP_Post $item Menu item data object.
// * @param int     $depth Depth of Tree.
// * @param array   $args An object of wp_nav_menu() argument.
// * @param int     $id Current item ID;
// */
// public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
// $object      = $item->object;
// $type        = $item->type;
// $title       = $item->title;
// $description = $item->description ?? 'menu item';
// $permalink   = $item->url ?? '#';
// $classes     = implode( ' ', $item->classes );

// $output .= "<li class=\"$classes\">";
// $output .= ( $permalink && '#' !== $permalink ) ? "<a title=\"link to the $description page\"href=\"$permalink\">" : '<span>';
// $output .= $title;
// $output .= ( $permalink && '#' !== $permalink ) ? '</a>' : '</span>';

// $output .= "</li>";
// }

// **
// * Ends the element output, if needed.
// *
// * @since 3.0.0
// *
// * @see Walker::end_el()
// *
// * @param string   $output Used to append additional content (passed by reference).
// * @param WP_Post  $item   Page data object. Not used.
// * @param int      $depth  Depth of page. Not Used.
// * @param stdClass $args   An object of wp_nav_menu() arguments.
// */
// public function end_el( &$output, $item, $depth = 0, $args = null ) {
// if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
// $t = '';
// $n = '';
// } else {
// $t = "\t";
// $n = "\n";
// }
// $output .= "</li>{$n}";
// }
// }
