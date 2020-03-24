<?php
/**
 * WP_Rig\WP_Rig\Global_Taxonomies\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Global_Taxonomies;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function add_action;


/**
 * TOC
 * #1 get_slug()
 * #2 initialize()
 * #3 template_tags()
 * #4 global_taxonomies()
 * #5 create_signtype_taxonomy()
 * #6 ()
 * #7 ()
 */

/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *

 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'global_taxonomies';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		/**
		 * If I ever decide to use this as a must use plugin, I will need this action.
		 * add_action( 'muplugins_loaded', [ $this, 'global_taxonomies' ] );
		 */
		add_action( 'plugins_loaded', [ $this, 'global_taxonomies' ] );
		add_action( 'wp_loaded', [ $this, 'global_taxonomies' ] );
		add_action( 'template_redirect', [ $this, 'global_taxonomies' ] );
		add_action( 'init', [ $this, 'global_taxonomies' ] );
		add_action( 'registered_taxonomy', [ $this, 'global_taxonomies' ] );
		add_action( 'switch_blog', [ $this, 'global_taxonomies' ] );
		// create signtype taxonomy at init.
		add_action( 'init', [ $this, 'create_signtype_taxonomy' ] );
		add_action( 'init', [ $this, 'create_location_taxonomy' ] );
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
	 * Adds a pingback url auto-discovery header for singularly identifiable articles.
	 *
	 * Replaces the prefix jco2_, jco3_, etc. with 'jco_'.
	 */
	public function global_taxonomies() {
		global $wpdb;
		$wpdb->terms         = 'jco_terms';
		$wpdb->term_taxonomy = 'jco_term_taxonomy';
		$wpdb->termmeta      = 'jco_termmeta';
	}

	/**
	 * Creates the custom taxonomy: 'signtype'.
	 *
	 * @note: is it better to create it here than in a different module entirely dedicated to the signtype taxonomy?
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
	 */
	public function create_signtype_taxonomy() {
		$singular      = 'signtype';
		$plural        = ucfirst( $singular ) . 's';
		$labels        = [
			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => 'All' . $plural,
			'parent_item'                => 'Main',
			'parent_item_colon'          => 'Main ' . $singular,
			'new_item_name'              => 'New ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'edit_item'                  => 'Edit ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'separate_items_with_commas' => 'Separate sign types w/commas',
			'add_or_remove_items'        => 'Add or remove ' . $plural,
			'choose_from_most_used'      => 'Frequently Used ' . $plural,
			'popular_items'              => 'Popular ' . $plural,
			'search_items'               => 'Search ' . $plural,
			'not_found'                  => 'Not Found',
			'no_terms'                   => 'No ' . $plural,
			'items_list'                 => $plural . ' list',
			'items_list_navigation'      => $plural . ' list navigation',
			'back_to_terms'              => 'Back to ' . $singular . ' Tags',
		];
		$rewrite       = [
			'slug'         => $singular,
			'with_front'   => true,
			'hierarchical' => false,
		];
		$args          = [
			'labels'             => $labels,
			'public'             => true,
			'description'        => 'Types of Signage',
			'hierarchical'       => false,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'show_admin_column'  => true,
			'show_tagcloud'      => true,
			'rewrite'            => $rewrite,
			'show_in_rest'       => true,
			'rest_base'          => $singular,
			'query_var'          => $singular,
		];
		$objects_array = [
			'post',
			'page',
			'attachment',
			'nav_menu_item',
		];
		register_taxonomy( 'signtype', $objects_array, $args );
	}//end create_signtype_taxonomy()

	/**
	 * Creates the custom taxonomy: 'Location'.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
	 */
	public function create_location_taxonomy() {
		$singular      = 'location';
		$plural        = ucfirst( $singular ) . 's';
		$labels        = [
			'name'                       => $plural . '- jones sign co',
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => 'All' . $plural,
			'parent_item'                => 'Main',
			'parent_item_colon'          => 'Main ' . $singular,
			'new_item_name'              => 'New ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'edit_item'                  => 'Edit ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'separate_items_with_commas' => 'Separate locations with commas',
			'add_or_remove_items'        => 'Add or remove ' . $plural,
			'choose_from_most_used'      => 'Frequently Used ' . $plural,
			'popular_items'              => 'Popular ' . $plural,
			'search_items'               => 'Search ' . $plural,
			'not_found'                  => 'Not Found',
			'no_terms'                   => 'No ' . $plural,
			'items_list'                 => $plural . ' list',
			'items_list_navigation'      => $plural . ' list navigation',
			'back_to_terms'              => 'Back to ' . $singular . ' Tags',
		];
		$rewrite       = [
			'slug'         => $singular,
			'with_front'   => true,
			'hierarchical' => false,
		];
		$args          = [
			'labels'             => $labels,
			'description'        => 'Covers Various Jones Sign Company Locations around North America',
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'show_in_menu'       => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'query_var'          => $singular,
			'rewrite'            => $rewrite,
			'show_in_rest'       => true,
			'rest_base'          => $singular,
		];
		$objects_array = [
			'post',
			'page',
			'attachment',
			'nav_menu_item',
		];
		register_taxonomy( 'location', $objects_array, $args );
	}//end create_location_taxonomy()

}
