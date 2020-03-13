<?php
/**
 * WP_Rig\WP_Rig\Staffmembers\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Staffmembers;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function wp_enqueue_script;
use function get_post_meta;
use function wp_localize_script;
use function register_post_type;

/**
 * Class for improving accessibility among various core features.
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * The size of the thumbnail.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $size The chosen size of the thumbnail (wp media imge-size for the list to choose among).
	 */
	private $size = 'thumbnail';

	/**
	 * Gets the unique identifier for the theme component in plural form.
	 *
	 * @return string Component slug.
	 */
	public function get_plural() : string {
		return 'staffmembers';
	}

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'staffmember';
	}

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_staffmember() : string {
		return 'build out still - this should puch out an object with all the data for a staffmember';
	}

	/**
	 * Get the label for the Staffmember Additional Information;
	 *
	 * @return string HTML for the label staffmember post type.
	 */
	private function get_project_address_field_label_cb() {
		return '<span class="indigo" style="font-size: 2.5rem;">Staffmember Info</span><hr>';
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
			'get_staffmember_info' => [ $this, 'get_staffmember_info' ],
		];
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_staffmembers_script' ] );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		add_action( 'init', [ $this, 'create_staffmember_posttype' ] );
	}

	/**
	 * Enqueues a script that improves navigation menu accessibility.
	 */
	public function action_enqueue_staffmembers_script() {

		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		// Enqueue the navigation script. The last element asks whether to load the script within the footer. We don't want that.
		wp_enqueue_script(
			'wp-rig-projects',
			get_theme_file_uri( '/assets/js/staffmembers.min.js' ),
			[],
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/staffmembers.min.js' ) ),
			false
		);

		/*
		Allows us to add the js right within the module.
		Setting 'precache' to true means we are loading this script in the head of the document.
		By setting 'async' to true,it tells the browser to wait until it finishes loading to run the script.
		'Defer' would mean wait until EVERYTHING is done loading to run the script.
		*/
		wp_script_add_data( 'wp-rig-staffmembers', 'async', true );
		wp_script_add_data( 'wp-rig-staffmembers', 'precache', true );
		wp_localize_script(
			'wp-rig-staffmembers',
			'wpRigScreenReaderText',
			[
				'expand'   => __( 'Expand child menu', 'wp-rig' ),
				'collapse' => __( 'Collapse child menu', 'wp-rig' ),
			]
		);
	}


	/**
	 * Creates the custom post type: 'Staffmember'.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/
	 */
	public function create_staffmember_posttype() {
		$icon_for_posttype   = 'dashicons-id-alt';
		$taxonomies_to_apply = [ 'expertise', 'location' ];
		$singular            = 'staffmember';
		$plural              = $singular . 's';

		$labels  = [
			'name'                  => ucfirst( $plural ),
			'singular_name'         => ucfirst( $singular ),
			'menu_name'             => ucfirst( $singular ),
			'name_admin_bar'        => ucfirst( $singular ),
			'archives'              => ucfirst( $singular ) . ' Archives',
			'attributes'            => 'Attributes',
			'parent_item_colon'     => 'Parent Item: ',
			'all_items'             => 'all ' . $plural,
			'add_new_item'          => 'Add New ' . ucfirst( $singular ),
			'add_new'               => 'Add New',
			'new_item'              => 'New ' . ucfirst( $singular ),
			'edit_item'             => 'Edit ' . ucfirst( $singular ),
			'update_item'           => 'Update ' . ucfirst( $singular ),
			'view_item'             => 'View ' . ucfirst( $singular ),
			'view_items'            => 'View ' . ucfirst( $plural ),
			'search_items'          => 'Search ' . ucfirst( $plural ),
			'not_found'             => 'Not found',
			'not_found_in_trash'    => 'Not found in Trash',
			'featured_image'        => 'Featured Image',
			'set_featured_image'    => 'Set featured image',
			'remove_featured_image' => 'Remove featured image',
			'use_featured_image'    => 'Use as featured image',
			'insert_into_item'      => 'Insert into item',
			'uploaded_to_this_item' => 'Uploaded to this ' . $singular,
			'items_list'            => ucfirst( $plural ) . ' list',
			'items_list_navigation' => ucfirst( $plural ) . ' list nav',
			'filter_items_list'     => 'Filter ' . ucfirst( $plural ) . ' List',
		];
		$rewrite = [
			'slug'       => $singular,
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		];
		$args    = [
			'label'                 => ucfirst( $singular ),
			'description'           => 'Jones Sign Company ' . ucfirst( $plural ) . ' and Details',
			'labels'                => $labels,
			'supports'              => array( 'title', 'thumbnail', 'excerpt', 'post-formats' ),
			'taxonomies'            => $taxonomies_to_apply,
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 100,
			'menu_icon'             => $icon_for_posttype,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'page',
			'show_in_rest'          => true,
			'rest_base'             => 'client',
			'rest_controller_class' => 'WP_REST_Client_Controller',
		];
		register_post_type( 'staffmember', $args );
	} // post type 'staffmember' create.

	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since    1.0.0
	 */
	public function additional_fields() {
		$after        = '<hr>';
		$prefix       = 'staffmember';
		$metabox_args = [
			'context'      => 'normal',
			'classes'      => $prefix . '-posttype-metabox',
			'id'           => $prefix . '_metabox',
			'object_types' => [ 'staffmember' ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Staffmember Information',
		];
		// Create the metabox to add fields to.
		$metabox = new_cmb2_box( $metabox_args );
		/**
		 * Get the label for the project address field;
		 */
		function get_label_cb() {
			return '<span class="indigo" style="font-size: 2.5rem;">Base Info</span><hr>';
		}

		/**
		 * Staffmember Info Field. CUSTOM.
		 *
		 * @see render_staffmember_field_callback()
		 */
//phpcs:disable
		/**
		 * Location of the project - address plus latitude and longitude.
		 */
		$args = [
			'name'                => 'Information',
			'id'                  => 'staffInfo', // Name of the custom field type we setup.
			'type'                => 'staffmember',
			'options'             => [],
			'label_cb'            => $this->get_project_address_field_label_cb(),
			'repeatable'          => false,
			'text'                => ['add_row_text' => 'Add another location' ],
			'show_names'          => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'             => [ 'repeatable', 'projectLocationAddress' ],
			'after_row'           => '<hr>',

		];
		$metabox->add_field( $args );
		/**
		 * Checkbox Field to determine whether this staff is considered to be management.
		 */
		$args = [
			'name' => 'Management',
			'id'   => 'staffManagement',
			'type' => 'checkbox',
			'desc' => 'Is this staffmember a manager?',
		];
		$metabox->add_field( $args );

	}//end additional_fields()

	/**
	 * Retrieve the postmeta for the year this project completed, started, or is expected to complete.
	 *
	 * @param int $id Project post type id.
	 * @return string 4 digit year that the post either completes, was started, or begins.
	 */
	public function get_staffmember_info( $id ) {
		$key = 'staffInfo';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

}//end class definition.
