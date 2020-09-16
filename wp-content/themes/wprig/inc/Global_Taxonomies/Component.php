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
		add_action( 'init', [ $this, 'create_industry_taxonomy' ] );
		add_action( 'init', [ $this, 'create_location_taxonomy' ] );
		add_action( 'init', [ $this, 'create_expertise_taxonomy' ] );
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
			'get_images_tagged' => [ $this, 'get_images_tagged' ],
		];
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
			'edit_item'                  => 'Edit signtype tag',
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
			'back_to_terms'              => 'Back to signtype Tags',
		];
		$rewrite       = [
			'slug'         => $singular,
			'with_front'   => true,
			'hierarchical' => false,
		];
		$args          = [
			'labels'                => $labels,
			'public'                => true,
			'description'           => 'Types of Signage',
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_quick_edit'    => true,
			'show_admin_column'     => true,
			'show_tagcloud'         => true,
			'rewrite'               => $rewrite,
			'show_in_rest'          => true,
			'rest_base'             => $singular,
			'query_var'             => $singular,
			'update_count_callback' => '_update_generic_term_count',
		];
		$objects_array = [
			'attachment',
			'post',
			'project',
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
			'name'                       => $plural,
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => 'All' . $plural,
			'parent_item'                => 'Main',
			'parent_item_colon'          => 'Main ' . $singular,
			'new_item_name'              => 'New ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'edit_item'                  => 'Edit signtype tag',
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
			'labels'                => $labels,
			'description'           => 'Covers Various Jones Sign Company Locations around North America',
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_quick_edit'    => true,
			'show_in_menu'          => true,
			'show_admin_column'     => true,
			'show_in_nav_menus'     => true,
			'show_tagcloud'         => true,
			'query_var'             => $singular,
			'rewrite'               => $rewrite,
			'show_in_rest'          => true,
			'rest_base'             => $singular,
			'update_count_callback' => '_update_generic_term_count',
		];
		$objects_array = [
			'staffmember',
		];
		register_taxonomy( 'location', $objects_array, $args );
	}//end create_location_taxonomy()

	/**
	 * Creates the custom taxonomy: 'services'.
	 *
	 * @note: is it better to create it here than in a different module entirely dedicated to the signtype taxonomy?
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
	 */
	public function create_industry_taxonomy() {
		$singular      = 'industry';
		$plural        = 'industries';
		$labels        = [
			'name'                       => ucfirst( $plural ),
			'singular_name'              => $singular,
			'menu_name'                  => ucfirst( $plural ),
			'all_items'                  => 'All' . $plural,
			'parent_item'                => 'Main',
			'parent_item_colon'          => 'Main ' . $singular,
			'new_item_name'              => 'New ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'edit_item'                  => 'Edit ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'separate_items_with_commas' => 'Separate Industries w/comma',
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
			'slug'         => 'industry',
			'with_front'   => true,
			'hierarchical' => false,
		];
		$args          = [
			'labels'                => $labels,
			'public'                => true,
			'description'           => 'Industries',
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_quick_edit'    => true,
			'show_admin_column'     => true,
			'show_tagcloud'         => true,
			'rewrite'               => $rewrite,
			'show_in_rest'          => true,
			'rest_base'             => 'industry',
			'query_var'             => 'industry',
			'update_count_callback' => '_update_generic_term_count',
		];
		$objects_array = [
			'client',
			'attachment',
			'project',
		];
		register_taxonomy( 'industry', $objects_array, $args );
	} //end create_service_taxonomy()

	/**
	 * Creates the custom taxonomy: 'service'.
	 *
	 * @note: is it better to create it here than in a different module entirely dedicated to the signtype taxonomy?
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
	 */
	public function create_expertise_taxonomy() {
		$singular = 'expertise';
		$plural   = 'expertises';
		$labels   = [
			'name'                       => ucfirst( $plural ),
			'singular_name'              => $singular,
			'menu_name'                  => ucfirst( $singular ),
			'all_items'                  => 'All' . $plural,
			'parent_item'                => 'Main',
			'parent_item_colon'          => 'Main ' . $singular,
			'new_item_name'              => 'New ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'edit_item'                  => 'Edit ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'separate_items_with_commas' => 'Separate Expertises w/comma',
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
		$rewrite  = [
			'slug'         => $singular,
			'with_front'   => true,
			'hierarchical' => false,
		];
		$args     = [
			'labels'                => $labels,
			'public'                => true,
			'description'           => ucfirst( $singular ),
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_quick_edit'    => true,
			'show_admin_column'     => true,
			'show_tagcloud'         => true,
			'rewrite'               => $rewrite,
			'show_in_rest'          => true,
			'rest_base'             => $singular,
			'query_var'             => $singular,
			'update_count_callback' => '_update_generic_term_count',
		];
		$objects  = [
			'post',
			'page',
			'attachment',
			'project',
		];
		register_taxonomy( 'expertise', $objects, $args );
	} //end create_expertisetaxonomy()

	/**
	 * Retrieve a list of the ids of all the attachments that have this tag attached to them.
	 *
	 * @param int $term The id for a particular term.
	 *
	 * @return array $ids An array of the ids for the photos assigned to this signtype.
	 */
	public function get_images_tagged( $term ) : array {

		$ids             = [];
		$query_arguments = [
			'tag'       => $term,
			'post_type' => 'attachment',
		];
		return $ids;
	}


	/**
	 * Count the words or characters in the term description.
	 *
	 * @param int $term_id The ID number of the term you'd like to check.
	 */
	private static function get_description_characters( $term_id ): int {
		return str_word_count( term_description( $term_id ) );
	}

	/**
	 * Count the words or characters in the taxonomy field.
	 *
	 * @param int    $term_id The ID number of the term you'd like to check.
	 * @param string $taxonomy The Taxonomy.
	 */
	private static function count_words_indepth( $term_id, $taxonomy = 'signtype' ): int {
		$key = $taxonomy . 'Indepth';
		return str_word_count( get_term_meta( $term_id, $key, $single ) );
	}

	/**
	 * Count the words or characters in the term description.
	 *
	 * @param int    $term_id The ID number of the term you'd like to check.
	 * @param string $termmeta_key The fieldname as used in the meta_key of the termmeta table.
	 */
	private static function count_words_in_termmeta( $term_id, $termmeta_key = 'signtypeIndepth' ): int {
		$sales_description = get_term_meta( $term_id, $fieldname, false );
		return str_word_count( $sales_description );
	}


	/**
	 * Check to see whether there is a sign image of a certain dimension.
	 *
	 * @param int    $term_id The specific taxonomy term id.
	 * @param string $taxonomy The name of the taxonomy tag.
	 */
	public static function check_tag_images( $term_id, $taxonomy ) : array {
		$options = [ 'vertical', 'cinematic', 'rectangular', 'square' ];
		$needs   = []; // Array will populate with the sizes of images needed if all image sizes have yet to be uploaded.
		$output  = [
			'color'   => '--indigo-600',
			'message' => 'all photo sizes attached',
		];
		foreach ( $options as $size ) {
			if ( ! get_term_meta( $term_id, $taxonomy . ucfirst( $size ), true ) ) {
				$needs[] = $size;
			}
		}
		if ( $needs ) {
			$output['color']   = '--gray-400';
			$output['message'] = 'need image sizes: ' . implode( ' | ', $needs );
		}
		return $output;
	}

	/**
	 * Check to see whether there is a sign image of a certain dimension.
	 *
	 * @param int    $term_id The specific taxonomy term id.
	 * @param string $taxonomy The name of the taxonomy tag.
	 * @param int    $minimum The minimum characters a description could be.
	 */
	public static function is_description_long_enough( $term_id, $taxonomy, $minimum ) : array {
		$output = [
			'color'   => '--indigo-600',
			'message' => 'description OK',
			'icon'    => 'assignment_turned_in',
			'title'   => "primary description has more than $minimum characters",
		];
		if ( $minimum > self::get_description_characters( $term_id ) ) {
			$output = [
				'color'   => '--gray-500',
				'message' => 'description has less than ' . $minimum . ' characters',
				'icon'    => 'assignment_late',
				'title'   => "primary description is less than $minimum words",
			];
		}
		return $output;
	}


	/**
	 * Check to see whether theecondary description of the signtype is long enough.
	 *
	 * @param int    $term_id The specific taxonomy term id.
	 * @param string $taxonomy The name of the taxonomy tag.
	 * @param int    $minimum The minimum characters a description could be.
	 */
	public static function is_indepth_description_long_enough( $term_id, $taxonomy, $minimum ) : array {
		$output = [
			'color'   => '--indigo-600',
			'message' => 'description OK',
			'icon'    => 'description',
			'title'   => "secondary description is greater than $minimum characters",
		];
		if ( $minimum > self::count_words_in_termmeta( $term_id ) ) {
			$output = [
				'color'   => '--gray-500',
				'message' => 'description has less than ' . $minimum . ' characters',
				'icon'    => 'description',
				'title'   => 'secondary description should be longer',
			];
		}
		return $output;
	}

}
