<?php
/**
 * WP_Rig\WP_Rig\Posttype_Global\Component class.
 * Last Update 29_April_2021.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Posttype_Global;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\TaxonomyGlobal\Component as Taxonomies;
use WP_Rig\WP_Rig\Posttype_Project\Component as Projects;

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
		return [
			'get_recent_posttype_posts'            => [ $this, 'get_recent_posttype_posts' ],
			'get_posttype_vertical_image_id'       => [ $this, 'get_posttype_vertical_image_id' ],
			'get_posttype_featured_images'         => [ $this, 'get_posttype_featured_images' ],
			'get_related_posts_linked_figures'     => [ $this, 'get_related_posts_linked_figures' ],
			'get_related_post_linked_figure_array' => [ $this, 'get_related_post_linked_figure_array' ],
			'get_all_posttype' => [ $this, 'get_all_posttype' ],
		];
	}

/**
 * Get related posts as html figure elements.
 *
 * @note Only to be used on Taxonomy Archive Pages.
 *
 * @param string $posttype The type of post - choose among: 'project', 'staffmember', 'client'.
 */
public function get_related_posts_linked_figures( $posttype = 'project' ) {
	$related             = [];
	$related_project_ids = Taxonomies::get_related();
	$size = 'medium'; // Could also use 'large' or 'wp-rig-featured';
	for ( $i = 0; $i < count( $related_project_ids ); $i++ ) {
		[
			'ID'           => $project_id,
			'post_title'   => $title,
			'post_excerpt' => $excerpt,
			'post_type'    => $post_type,
			'guid'         => $link,
			'post_name'    => $slug,
		] = get_post( $related_project_ids[ $i ], ARRAY_A );
		$related[] = [
			'id'         => $project_id,
			'img_id'     => get_post_thumbnail_id( $related_project_ids[ $i ] ),
			'img_url'    => wp_get_attachment_image_src( get_post_thumbnail_id( $related_project_ids[ $i ] ), $size )[0],
			'post_title' => $title,
			'link'       => $link,
		];
	}
	return $related;
}

/**
 * GET data array that will be used for the linked figure for projects/locations/clients/staffmembers
 *
 * @param int    $post_id The ID for the post that is related to this taxonomy.
 * @param string $size    Choose among: 'full', 'medium_large', 'large', 'wp-rig-featured', 'medium', or 'thumbnail'.
 *
 */
public static function get_related_post_linked_figure_array( $post_id, $size = 'medium' ) {
	[
		'ID'           => $post_id,
		'post_title'   => $title,
		'post_excerpt' => $excerpt,
		'post_type'    => $post_type,
		'guid'         => $link,
		'post_name'    => $slug,
	] = get_post( $post_id, ARRAY_A );
	$vertical = [
		'id'  => self::get_posttype_vertical_image_id( $post_id, $post_type ),
		'url' => self:: get_posttype_vertical_image_url( $post_id, $post_type, $size )
	];
	return [
		'id'         => $post_id,
		'vertical'   => $vertical,
		'img_id'     => get_post_thumbnail_id( $post_id ),
		'img_url'    => wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size )[0],
		'post_title' => $title,
		'link'       => $link,
	];

}

	/**
	 * Create the posttypes: 'project', 'staffmember', 'client'.
	 */
	public function instantiate_the_posttypes() {
		$posttypes = $this->create_jonessign_posttypes();
		foreach ( $posttypes as $post_type ) {
			register_post_type( $post_type['singular'], $post_type['args'] );
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
	 * @param string $description A description of this post type.
	 * @param string $icon A menu icon.
	 * @param array  $taxonomies The taxonomies to be used on this post type.
	 * @param array  $supports The options that this post type will support.
	 */
	public function create_posttype_args( $singular, $plural, $description, $icon, $taxonomies, $supports ) {
		return [
			'label'                 => ucfirst( $singular ),
			'description'           => $description,
			'labels'                => $this->create_posttype_labels( $singular, $plural ),
			'supports'              => [ 'title', 'thumbnail', 'excerpt', 'post-formats', 'page-attributes', 'editor' ],
			'taxonomies'            => (array) $taxonomies,
			'hierarchical'          => false,
			'public'                => in_array( $singular, [ 'project', 'client', 'staffmember' ], true ),
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
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'feeds'                 => in_array( $singular, [ 'project', 'client' ], true ),
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
				'icon'        => 'dashicons-welcome-widgets-menus',
				'taxonomies'  => [ 'expertise', 'signtype', 'location' ],
				'supports'    => [ 'additional-fields', 'title', 'excerpt', 'thumbnail', 'post-formats', 'page-attributes' ],

			],
			[
				'singular'    => 'client',
				'plural'      => 'clientele',
				'description' => 'Clients and Details',
				'icon'        => 'dashicons-businessman',
				'taxonomies'  => [ 'expertise', 'signtype', 'location' ],
				'supports'    => [ 'additional-fields', 'title', 'excerpt', 'thumbnail', 'post-formats', 'page-attributes' ],

			],
			[
				'singular'    => 'staffmember',
				'plural'      => 'staffmembers',
				'description' => 'Company Staffmembers and Details',
				'icon'        => 'dashicons-id-alt',
				'taxonomies'  => [ 'expertise', 'location' ],
				'supports'    => [ 'additional-fields', 'title', 'excerpt', 'thumbnail', 'post-formats', 'page-attributes' ],

			],
			[
				'singular'    => 'inquiry',
				'plural'      => 'inquiries',
				'description' => 'Incoming Inquiries & Sales Leads',
				'icon'        => 'dashicons-email-alt2',
				'taxonomies'  => [ 'expertise', 'location', 'signtype' ],
				'supports'    => [ 'additional-fields', 'title', 'excerpt', 'post-formats', 'page-attributes' ],
			],
		];

		$total = count( $posttypes );
		for ( $i = 0; $i < $total; $i++ ) {
			$singular                = $posttypes[ $i ]['singular'];
			$plural                  = $posttypes[ $i ]['plural'];
			$description             = $posttypes[ $i ]['description'];
			$icon                    = $posttypes[ $i ]['icon'];
			$taxonomies              = $posttypes[ $i ]['taxonomies'];
			$supports                = $posttypes[ $i ]['supports'];
			$posttypes[ $i ]['args'] = $this->create_posttype_args( $singular, $plural, $description, $icon, $taxonomies, $supports );
		}
		return $posttypes;
	}

	/**
	 * Get all the published items from a given post type.
	 *
	 * @param string $post_typeCould be project, staffmember, or client.
	 */
	public static function get_all_posttype( $post_type ) {
		$args = [
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'suppress_filters' => true,
		];
		return new \WP_QUERY( $args );
	}


	/**
	 * Retrieve the square image information meta for 1 x 1 aspect image for the given sign type.
	 *
	 * @param int    $post_id ID value for the post.
	 * @param string $post_type The custom post type, staffmember, project, client.
	 *
	 * @return mixed if $output_as_id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public static function get_posttype_square_image_id( $post_id, $post_type ) {
		$output = get_post_meta( $post_id, $post_type . 'SquareImage_id', true ) ? get_post_meta( $post_id, $post_type . 'SquareImage_id', true ) : 773;
		return $output;
	}

	/**
	 * Retrieve the square image information meta for 1 x 1 aspect image for the given sign type.
	 *
	 * @param int    $post_id ID value for the post.
	 * @param string $post_type The custom post type, staffmember, project, client.
	 * @param string $size - Choose among: 'full', 'medium_large', 'wp-rig-featured', 'large', 'medium', or 'thumbnail'.
	 *
	 * @return string URL of the square image.
	 */
	public static function get_posttype_square_image_url( $post_id, $post_type, string $size ) {
		$image_id = self::get_posttype_square_image_id( $post_id, $post_type );
		return wp_get_attachment_image_src( $image_id, $size )[0];
	}

	/**
	 * Get vertical image for a post.
	 *
	 * @param int    $post_id The id of the post.
	 * @param string $post_type Could be 'project', 'client', 'staffmember' -- don't let it be staffmember as there are no vertical images for that post type being collected.
	 *
	 * @return int Vertical Image id number.
	 */
	public static function get_posttype_vertical_image_id( int $post_id, string $post_type ) {
		$output = get_post_meta( $post_id, $post_type . 'VerticalImage_id', true ) ? get_post_meta( $post_id, $post_type . 'VerticalImage_id', true ) : 809;
		return $output;
	}

	/**
	 * Get vertical image for a post.
	 *
	 * @param int    $post_id The id of the post.
	 * @param string $post_type Could be 'project', 'client', 'staffmember' -- don't let it be staffmember as there are no vertical images for that post type being collected.
	 * @param string $size - Choose among: 'full', 'medium_large', 'wp-rig-featured', 'large', 'medium', or 'thumbnail'.
	 *
	 * @return string Vertical Image Url.
	 */
	public static function get_posttype_vertical_image_url( int $post_id, string $post_type, string $size ) {
		$image_id = self::get_posttype_vertical_image_id( $post_id, $post_type );
		return wp_get_attachment_image_src( $image_id, $size )[0];
	}

	/**
	 * Get the vertical, square, and the featured images.
	 *
	 * @param int    $id The post id.
	 * @param string $post_type Could be 'project', 'client', 'staffmember' -- don't let it be staffmember as there are no vertical images for that post type being collected.
	 * @param string $size - Choose among: 'full', 'medium_large', 'wp-rig-featured', 'large', 'medium', or 'thumbnail'.
	 *
	 * @return array Both the featured image and the vertical image ids
	 */
	public static function get_posttype_featured_images( int $post_id, string $post_type, string $size ) : array {
		$images               = [];
		$horizontal_id = ( get_post_thumbnail_id( $post_id ) ) ? get_post_thumbnail_id( $post_id ) : 771;

		$images['horizontal'] = [
			'id'  => $horizontal_id,
			'url' => wp_get_attachment_image_src( $horizontal_id, $size )[0],
		];
		$images['vertical']   = [
			'id'  => self::get_posttype_vertical_image_id( $post_id, $post_type ),
			'url' => self::get_posttype_vertical_image_url( $post_id, $post_type, $size ),
		];
		$images['square']     = [
			'id'  => self::get_posttype_square_image_id( $post_id, $post_type ),
			'url' => self::get_posttype_square_image_url( $post_id, $post_type, $size ),
		];
		return $images;
	}

	/**
	 * Get Recent posts of this post type.
	 *
	 * @param int  $quantity Number of projects we want.
	 * @param bool $just_id Do we want to output objects for the post with all sorts of post information, or merely ID?
	 *
	 * @return array Either  an array of post ids or an array of the basic post data
	 */
	public function get_recent_posttype_posts( $quantity = 12, $just_ids = true ) {
		global $post_type;
		global $post;

		$this_post_id         = [ $post->ID ];
		$additional_arguments = [ 'fields' => 'ids' ];
		$args = [
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'posts_per_page'   => $quantity,
			'orderby'          => 'modified',
			'suppress_filters' => true,
			'post__not_in'     => $this_post_id,
		];

		if ( $just_ids ) {
			$args = wp_parse_args( $additional_arguments, $args );
		}
		$posts = new \WP_QUERY( $args );
		return $posts->posts;
	}

	/**
	 * Enhance the post navigation built into WordPress
	 */
	public function enhanced_post_havigation( $current_post_id ) {
		global $post_type;
	}
}//end class
