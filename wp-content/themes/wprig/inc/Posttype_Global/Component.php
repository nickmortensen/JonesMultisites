<?php
/**
 * WP_Rig\WP_Rig\Posttype_Global\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Posttype_Global;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Query;
use function add_action;
use function get_terms;
use function get_term;
use function get_term_meta;
use function register_taxonomy;

/**
 * Class to create and use custom taxonomy terms to Jones Sign Company.
 *
 * @since 1.0.0
 */
class Component implements Component_Interface, Templating_Component_Interface {
	/**
	 * The slug of this taxonomy.
	 *
	 * @access   private
	 * @var      string    $slug The slug of this taxonomy..
	 */
	private $slug = 'posttype_global';

	/**
	 * An Associative array containing all taxonomies using the singular name as the key.
	 *
	 * @access   public
	 * @var      array    $taxonomies All custom taxonomies using the singular name as the key.
	 */
	public $posttypes = [
		[
			'singular'    => 'project',
			'plural'      => 'projects',
			'description' => 'Company Projects and Details',
			'icon'        => 'dashicons-admin-multisite',
			'taxonomies'  => [ 'expertise', 'location' ],

		],
		[
			'singular'    => 'client',
			'plural'      => 'clientele',
			'description' => 'Clients and Details',
			'icon'        => 'dashicons-id-alt',
			'taxonomies'  => [ 'expertise', 'location' ],

		],
		[
			'singular'    => 'staffmember',
			'plural'      => 'staffmembers',
			'description' => 'Company Staffmembers and Details',
			'icon'        => 'dashicons-id-alt',
			'taxonomies'  => [ 'expertise', 'location' ],

		],
	];

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'posttypes';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		/* use the custom post types for jonessign.com */
		add_action( 'init', [ $this, 'instantiate_the_posttypes' ] );
	}

	/**
	 * Gets template tags to EXPOSE as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [];
	}

	/**
	 * Create the posttypes: 'project', 'staffmember', 'client'.
	 */
	public function instantiate_the_posttypes() {
		$posttypes = $this->create_jonessign_posttypes();
		foreach ( $posttypes as $posttype ) {
			register_post_type( $posttype['singular'], $posttype['args'] );
		}
	}

	/**
	 * Create the labels for the posttypes.
	 *
	 * @param string $singular Post type name in singular sform.
	 * @param string $plural Post type name in plural sform.
	 */
	public function create_posttype_labels( $singular, $plural ) {
		$s = ucfirst( $singular );
		$p = ucfirst( $plural );
		return [
			'name'                  => $p,
			'singular_name'         => $s,
			'menu_name'             => $s,
			'name_admin_bar'        => $s,
			'archives'              => $s . ' Archives',
			'attributes'            => 'Attributes',
			'parent_item_colon'     => 'Parent Item: ',
			'all_items'             => 'all ' . $p,
			'add_new_item'          => 'Add New ' . $s,
			'add_new'               => 'Add New',
			'new_item'              => 'New ' . $s,
			'edit_item'             => 'Edit ' . $s,
			'update_item'           => 'Update ' . $s,
			'view_item'             => 'View ' . $s,
			'view_items'            => 'View ' . $p,
			'search_items'          => 'Search ' . $p,
			'not_found'             => 'Not found',
			'not_found_in_trash'    => 'Not found in Trash',
			'featured_image'        => 'Featured Image',
			'set_featured_image'    => 'Set featured image',
			'remove_featured_image' => 'Remove featured image',
			'use_featured_image'    => 'Use as featured image',
			'insert_into_item'      => 'Insert into item',
			'uploaded_to_this_item' => 'Uploaded to this ' . $s,
			'items_list'            => $p . ' list',
			'items_list_navigation' => $p . ' list nav',
			'filter_items_list'     => 'Filter ' . $p . ' List',
		];
	}

	/**
	 * Create the arguments for the posttypes.
	 *
	 * @param string $singular Post type name in singular sform.
	 * @param string $plural Post type name in plural sform.
	 * @param string $icon A menu icon.
	 * @param array  $taxonomies The taxonomies to be used on this post type.
	 */
	public function create_posttype_args( $singular, $plural, $icon, $taxonomies ) {
		return [
			'label'                 => ucfirst( $singular ),
			'description'           => 'Jones Sign Company ' . ucfirst( $plural ) . ' and Details',
			'labels'                => $this->create_posttype_labels( $singular, $plural ),
			'supports'              => [ 'title', 'thumbnail', 'excerpt', 'post-formats', 'page-attributes' ],
			'taxonomies'            => (array) $taxonomies,
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 100,
			'menu_icon'             => $icon,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => [
				'slug'       => $singular,
				'with_front' => true,
				'pages'      => true,
				'feeds'      => true,
			],
			'capability_type'       => 'post',
			'show_in_rest'          => true,
			'rest_base'             => $singular,
			'rest_controller_class' => 'WP_REST_Client_Controller',
		];
	}

	/**
	 * Create all the custom post types needed for the multisite Jones Sign company website.
	 */
	public function create_jonessign_posttypes() {
		$posttypes = [
			[
				'singular'    => 'project',
				'plural'      => 'projects',
				'description' => 'Company Projects and Details',
				'icon'        => 'dashicons-id-alt',
				'taxonomies'  => [ 'expertise', 'signtype', 'location' ],

			],
			[
				'singular'    => 'client',
				'plural'      => 'clientele',
				'description' => 'Clients and Details',
				'icon'        => 'dashicons-admin-multisite',
				'taxonomies'  => [ 'expertise', 'signtype', 'location' ],

			],
			[
				'singular'    => 'staffmember',
				'plural'      => 'staffmembers',
				'description' => 'Company Staffmembers and Details',
				'icon'        => 'dashicons-id-alt',
				'taxonomies'  => [ 'expertise', 'location' ],

			],
		];

		$total = count( $posttypes );
		for ( $i = 0; $i < $total; $i++ ) {
			$singular                = $posttypes[ $i ]['singular'];
			$plural                  = $posttypes[ $i ]['plural'];
			$description             = $posttypes[ $i ]['description'];
			$icon                    = $posttypes[ $i ]['icon'];
			$taxonomies              = $posttypes[ $i ]['taxonomies'];
			$posttypes[ $i ]['args'] = $this->create_posttype_args( $singular, $plural, $taxonomies, $icon );
		}
		return $posttypes;
	}

	/**
	 * Get all the published items from a given post type.
	 *
	 * @param string $posttype Could be project, staffmember, or client.
	 */
	public static function get_all_posttype( $posttype ) {
		$args = [
			'post_type'        => $posttype,
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'suppress_filters' => true,
		];
		return new \WP_QUERY( $args );
	}

	/**
	 * Get vertical image for a post.
	 *
	 * @param int    $post_id The id of the post.
	 * @param string $posttype Could be 'project', 'client', 'staffmember' -- don't let it be staffmember as there are no vertical images for that post type being collected.
	 * @param bool   $return_as_id Whether to return the vertical image as an ID or a url, default is true - so it returns an id.
	 *
	 * @return mixed If $return_as_id = true, returns id of the vertical image, otherwise returns the url.
	 */
	public static function get_posttype_vertical_image( int $post_id, string $posttype, bool $return_as_id = true ) {
		$meta_field = $return_as_id ? $posttype . 'VerticalImage_id' : $posttype . 'VerticalImage';
		return get_post_meta( $post_id, $meta_field, $single = true );
	}

	/**
	 * Get the vertical and the featured images.
	 *
	 * @param int    $id The post id.
	 * @param string $posttype Could be 'project', 'client', 'staffmember' -- don't let it be staffmember as there are no vertical images for that post type being collected.
	 *
	 * @return array Both the featured image and the vertical image ids
	 */
	public static function get_header_images_ids( int $id, string $posttype ) : array {
		$images             = [];
		$images['featured'] = get_post_thumbnail_id( $id );
		$images['vertical'] = self::get_posttype_vertical_image( $id, $posttype, true );
		return $images;
	}

}//end class
