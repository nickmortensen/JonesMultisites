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
use function get_current_screen;
use function wp_enqueue_script;
use function get_post_meta;
use function wp_localize_script;
use function register_post_type;

/**
 * TABLE OF CONTENTS.
 *
 * 1. get_plural().
 * 2. get_slug()
 * 3. get_staffmember()
 */

/**
 * Class for building out the Staffmembers post type.
 *
 * @property string $size
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
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// Create the post type of 'staffmember'.
		add_action( 'init', [ $this, 'create_staffmember_posttype' ] );
		// Add metabox to posts to add our meta info.
		add_action( 'add_meta_boxes', [ $this, 'add_metabox_to_staffmembers' ], 10, 2 );
		// Add additional fields to staffmember that save the bulk of the information for a staffmember as serialized json.
		add_filter( 'cmb2_render_staffmember', [ $this, 'render_staffmember_field_callback' ], 10, 5 );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		// Add custom columns for the admin edit screens.
		add_action( 'manage_staffmember_posts_columns', [ $this, 'new_admin_columns' ], 10, 1 );
		// Grab and display data in the new admin columns.
		add_action( 'manage_staffmember_posts_custom_column', [ $this, 'manage_custom_admin_columns' ], 10, 2 );
		// Output form elements for quickedit interface.
		add_action( 'quick_edit_custom_box', [ $this, 'display_quick_edit_custom' ], 10, 2 );
		// Load the javascript admin script (for prepopulting fields with JS).
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts_and_styles' ] );
		// Save the new data added to the staffmember post type in the quick edit screen.
		add_action( 'save_post', [ $this, 'save_post' ], 10, 1 ); // call on save, to update metainfo attached to our metabox.
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
			'is_management'        => [ $this, 'is_management' ],
			'is_current'           => [ $this, 'is_current' ],
			'get_date_of_hire'     => [ $this, 'get_date_of_hire' ],
		];
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
			'wp-rig-staffmembers',
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
			'supports'              => array( 'title', 'thumbnail', 'excerpt', 'post-formats', 'page-attributes' ),
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
			'capability_type'       => 'post',
			'show_in_rest'          => true,
			'rest_base'             => 'staffmember',
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
		$prefix       = 'staffmembers_';
		$metabox_args = [
			'context'      => 'normal',
			'classes'      => $prefix . 'metabox',
			'id'           => $prefix . '_metabox',
			'object_types' => [ 'staffmember' ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Staffmember Information',
			'show_title'   => false,
		];
		// Create the metabox to add fields to.
		$metabox = new_cmb2_box( $metabox_args );
		/**
		 * Get the label for the project address field;
		 */
		function get_label_cb() {
			$html = '<span style="color:var(--indigo-600);font-size: 2.5rem; border-bottom: 2px solid var(--indigo-600)">Base Info</span>';
			return $html;
		}

		/**
		 * Staffmember Info Field. CUSTOM.
		 *
		 * @see WP_Rig\WP_Rig\AdditionalFields\Component->render_staffmember_field_callback()
		 */

		/**
		 * Additional Details about the staffmember - field is defined in WP_Rig\WP_Rig\AdditionalFields\Component
		 */
		$args = [
			'name'       => 'Information',
			'id'         => 'staffInfo', // Name of the custom field type we setup.
			'type'       => 'staffmember',
			'label_cb'   => get_label_cb(),
			'show_names' => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'    => [ 'staff-information-fields' ],
			'after_row'  => '<hr>',

		];
		$metabox->add_field( $args );

		/**
		 * Calendar Field For Date Of Hire.
		 */
		$args = [
			'name'       => 'Date Hired',
			'id'         => 'staffHire',
			'type'       => 'text_date',
			'desc'       => 'date of hire',
			'classes'    => [ 'custom-calendar' ],
			'attributes' => [
				'data-datepicker' => wp_json_encode( [
					'yearRange' => '-42:+0',
				] ),
			],
		];
		$metabox->add_field( $args );
	}//end additional_fields()

	/**
	 * Retrieve the postmeta for the general contact information for the staffmember
	 *
	 * @param int $id Project post type id.
	 * @return array $output - JSON Encoded array containing, full_title, short_title, jones id, deskphone, extension, mobilephone, and email address.
	 */
	public function get_staffmember_info( $id ) {
		$key    = 'staffInfo';
		$single = true; // If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the postmeta for the date on which the staffmember was hired.
	 *
	 * @param int $id  Staffmember post type id.
	 * @return array $output - JSON Encoded array containing, deskphone, extension, mobilephone, and email address.
	 */
	public function get_date_of_hire( $id ) {
		$key    = 'staffHire';
		$single = true; // If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Output the management status of the staffmember.
	 *
	 * @param int $id The ID of the staffmember post.
	 */
	public function is_management( $id ) {
		$key = 'staffManagement';
		return get_post_meta( $id, $key, true );
	}

	/**
	 * Output the management status of the staffmember.
	 *
	 * @param int $id The ID of the staffmember post.
	 */
	public function is_current( $id ) {
		$key = 'staffCurrent';
		return get_post_meta( $id, $key, true );
	}

	/**
	 * Set additional administrator columns based on postmeta fields that are added.
	 *
	 * @param array $columns Array of columns I have already within the quick edit screen.
	 * @return array The newly added columns plus the existing columns that I have within the quick edit screen.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 */
	public static function new_admin_columns( $columns ) {
		global $post_type;
		$adminurl = admin_url( '/wp-admin/edit.php?post_type=' . $post_type . '&orderby=identifier&order=asc' );
		unset( $columns['taxonomy-location'] );
		unset( $columns['date'] );
		$new = [
			'cb'          => array_slice( $columns, 0, 1 ),
			'id'          => 'ID',
			'staff_title' => 'Position',
			'staff_id'    => '',
			'title'       => array_slice( $columns, 0, 1 ),
		];

		$new_columns['staff_management'] = 'Mgmt?';
		$new_columns['staff_current']    = 'Current?';

		return array_merge( $new, $columns, $new_columns );
	}

	/**
	 * Grab the data for the new columns in the admin area for this post type.
	 *
	 * @param string $column_name Name of column.
	 * @param int    $post_id   Post ID.
	 */
	public function manage_custom_admin_columns( $column_name, $post_id ) {
		global $post_type;
		$html       = '';
		$staff_info = get_post_meta( $post_id, 'staffInfo', true );
		switch ( $column_name ) {
			case 'id':
				$html = $post_id;
				break;
			case 'staff_title':
				$jobtitle    = isset( $staff_info['full_title'] ) ? $staff_info['full_title'] : '';
				$short_title = isset( $staff_info['short_title'] ) ? $staff_info['short_title'] : '';
				$html        = "<div id=\"full_title_$post_id\">$jobtitle</div>";
				$html       .= "<div id=\"short_title_$post_id\" hidden>$short_title</div>";
				break;
			case 'staff_id':
				$img      = $this->output_circular_images( $post_id );
				$staff_id = $staff_info['staff_id'] ?? '';
				$html     = "$img<div id=\"staff_id_$post_id\" data-staffid=\"$staff_id\">$staff_id</div>";
				break;
			case 'staff_management':
				$state = $staff_info['staff_management'] ?? 'off';
				$html  = '<div id="staff_management_' . $post_id . '" data-state="' . $state . '">';
				$color = 'on' === $state ? 'var(--indigo-600)' : 'var(--gray-500)';
				$html .= '<span class="material-icons" style="color: ' . $color . ';">supervisor_account</span></span>';
				$html .= '</div>';
				break;
			case 'staff_current':
				$state = $staff_info['staff_current'] ?? 'off';
				$html  = '<div id="staff_current_' . $post_id . '" data-state="' . $state . '">';
				$color = 'on' === $state ? 'var(--green-600)' : 'var(--gray-500)';
				$html .= '<span class="material-icons" style="color: ' . $color . ';">check_circle</span></span>';
				$html .= '</div>';
				break;
			default:
				$html = '';
		}
		echo $html;
	}

	/**
	 * Output the image for the person as an image in a circle.
	 *
	 * @param int    $post_id The ID of the staffmember.
	 * @param string $size The size of the image -- defaults to 'thumbnail.
	 * @return string      The HTML to display the photo.
	 */
	public function output_circular_images( $post_id, $size = 'thumbnail' ) {
		$thumb_url = get_the_post_thumbnail_url( $post_id, $size );
		return '<svg role="none" style="height: 36px; width: 36px;">
				<mask id="avatar">
					<circle cx="18" cy="18" fill="white" r="18"></circle>
				</mask>
				<g mask="url(#avatar)">
					<image x="0" y="0" height="100%" preserveAspectRatio="xMidYMid slice" width="100%" xlink:href="' . $thumb_url . '" style="height: 36px; width: 36px;"></image>
					<circle cx="18" cy="18" r="18" style="stroke-width:2;stroke:rgba(0,0,0,0.1);fill:none;"></circle>
				</g>
			</svg>';
	}

	/**
	 * Add a new metabox on our single staffmember edit screen. Shows up on the side.
	 *
	 * @param string $post_type The Type of post - in our case: 'staffmember'.
	 * @param int    $post The dentifier of the post - the number.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Types
	 * @link https://ducdoan.com/add-custom-field-to-quick-edit-screen-in-wordpress/
	 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
	 */
	public function add_metabox_to_staffmembers( $post_type, $post ) {
		$id       = 'staffinfo-short-details';
		$title    = 'More Staff Detail';
		$callback = [ $this, 'display_staff_metabox_output' ];
		$screen   = 'staffmember';
		$context  = 'side';
		$priority = 'high';
		add_meta_box( $id, $title, $callback, $screen, $context, $priority );
	}

	/**
	 * Displays additional fields within the staffmember post type, populating as needed.
	 *
	 * @param int $post The post ID.
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_staff_metabox_output( $post ) {

		$html = '';
		wp_nonce_field( 'post_metadata', 'post_metadata_field' );

		$staff_info       = get_post_meta( $post->ID, 'staffInfo', true );
		$full_title       = $staff_info['full_title'] ?? '';
		$short_title      = $staff_info['short_title'] ?? '';
		$staff_current    = $staff_info['staff_current'] ?? 'off';
		$staff_management = $staff_info['staff_management'] ?? 'off';
		$staff_id         = $staff_info['staff_id'] ?? '';
		$desired_value    = 'on';

		$html .= '<div class="inline-edit-group wp-clearfix toggle_checkbox">';

		$html .= '<div class="njm flex row-nowrap justify-space-between">';
		$html .= '<label class"on-your-left" for="staffInfo[staff_management]">Management?</label>';
		$html .= '<input name="staffInfo[staff_management]" type="checkbox" id="staffInfo[staff_management]" value="on" ' . checked( $staff_management, 'on', false ) . ' />';
		$html .= '</div>';

		$html .= '<div class="njm flex row-nowrap justify-space-between">';
		$html .= '<label class"on-your-left" for="staffInfo[staff_current]">Current?</label>';
		$html .= '<input name="staffInfo[staff_current]" type="checkbox" id="staffInfo[staff_current]" value="on" ' . checked( $staff_current, 'on', false ) . '/>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label for="staffInfo[full_title]" class="alignleft" >Full Title</label>';
		$html .= '<input class="widefat" type="text" name="staffInfo[full_title]" id="staffInfo[full_title]" value="' . $full_title . '"/>';
		$html .= '</div>';
		$html .= '<div>';
		$html .= '<label for="staffInfo[short_title]" class="alignleft" >Title (Shortened)</label>';
		$html .= '<input class="widefat" type="text" class="text_small" name="staffInfo[short_title]" id="staffInfo[short_title]" value="' . $short_title . '"/>';
		$html .= '</div>';
		$html .= '<div>';
		$html .= '<label for="staffInfo[staff_id]" class="alignleft" >Staff ID</label>';
		$html .= '<input class="widefat" type="text" class="text_small" name="staffInfo[staff_id]" id="staffInfo[staff_id]" value="' . $staff_id . '"/>';
		$html .= '</div>';

		$html .= '</div>';

		echo $html;
	}

	/**
	 * Set a checkbox efault value if we don't have a post ID (in the 'post' query variable).
	 *
	 * @param bool $default On/Off (true/false).
	 * @return mixed        Returns true or '', the blank default
	 */
	private function default_for_staff_current_checkbox_field( $default ) {
		return isset ( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
	}

	/**
	 * Displays our custom content on the quick-edit interface, no values can be pre-populated (all done in JS)
	 *
	 * @param string $column Name of column.
	 *
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_quick_edit_custom( $column ) {
		$html = '';
		wp_nonce_field( 'post_metadata', 'post_metadata_field' );
		switch ( $column ) {
			// Output checkbox with name attribute staff_management.
			case 'staff_management':
				$html .= '<fieldset class="inline-edit-col-left clear">';
				$html .= '<div class="toggle_checkbox">';
				$html .= '<label for="staffInfo[staff_management]">Management?</label>';
				$html .= '<input name="staffInfo[staff_management]" type="checkbox" id="staff_management" value="on"/>';
				$html .= '</div>';
				break;
			// Output checkbox with name attribute staff_current.
			case 'staff_current':
				$html .= '<div class="toggle_checkbox">';
				$html .= '<label for="staffInfo[staff_current]">Current?</label>';
				$html .= '<input name="staffInfo[staff_current]" type="checkbox" id="staff_current" value="on"/>';
				$html .= '</div>';
				break;
			case 'staff_id':
				$html .= '<div class="inline-edit-group wp-clearfix njm flex row-nw justify-start align-center">';
				$html .= '<label for="staffInfo[staff_id]" class="alignleft" >Staff ID</label>';
				$html .= '<input type="text" class="text_small" name="staffInfo[staff_id]" id="staff_id"/>';
				$html .= '</div>';
				$html .= '<div class="inline-edit-group wp-clearfix njm flex row-nw justify-start align-center">';
				$html .= '<label for="staffInfo[full_title]" class="alignleft" >Full Title</label>';
				$html .= '<input type="text" name="staffInfo[full_title]" id="full_title"/>';
				$html .= '</div>';
				$html .= '<div class="inline-edit-group wp-clearfix njm flex row-nw justify-start align-center">';
				$html .= '<label for="staffInfo[short_title]" class="alignleft" >Short Title</label>';
				$html .= '<input type="text" name="staffInfo[short_title]" id="short_title"/>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			default:
				$html = '';
		} // End switch.
		echo $html;
	}

	/**
	 * Saving meta info (used for both traditional and quick-edit saves)
	 *
	 * @param int $post_id The id of the post.
	 */
	public function save_post( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( 'staffmember' === $post_type ) {

			// check nonce set.
			if ( ! isset( $_POST['post_metadata_field'] ) ) return false;

			// verify nonce.
			if ( ! wp_verify_nonce( $_POST['post_metadata_field'], 'post_metadata' ) ) return false;

			// If not autosaving.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

			$staff_info = $_POST['staffInfo'];

			$staff_id         = isset ( $staff_info['staff_id'] ) ? sanitize_text_field( $staff_info['staff_id'] ) : '';
			$full_title       = isset ( $staff_info['full_title'] ) ? sanitize_text_field( $staff_info['full_title'] ) : '';
			$short_title      = isset ( $staff_info['short_title'] ) ? sanitize_text_field( $staff_info['short_title'] ) : '';
			$staff_current    = isset ( $staff_info['staff_current'] ) ? $staff_info['staff_current'] : 'off';
			$staff_management = isset ( $staff_info['staff_management'] ) ? $staff_info['staff_management'] : 'off';

			$newdata = [
				'staff_id'         => $staff_id,
				'full_title'       => $full_title,
				'short_title'      => $short_title,
				'staff_management' => $staff_management,
				'staff_current'    => $staff_current,
			];

			// This field is saved as serialized data, so I need to use wp_parse_args to get to it.
			update_post_meta( $post_id, 'staffInfo', wp_parse_args( $newdata, get_post_meta( $post_id, 'staffInfo', true ) ) );
		}

	}//end save_post()

	/**
	 * Enqueue admin js to pre-populate the quick-edit fields.
	 */
	public function enqueue_admin_scripts_and_styles() {
		global $post_type;

		if ( is_admin () && 'staffmember' === $post_type ) :
		// Only use the minified script if we are in a production environment.
		$script_uri   = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/staffmember_quickedit.js' ) : get_theme_file_uri( '/assets/js/staffmember_quickedit.min.js' );
		$version      = '20';
		$dependencies = [ 'jquery', 'inline-edit-post' ]; // location: wp-admin/js/inline-edit-post.js.
		$in_footer    = true; // True if we want to load the script in footer, false to load within header.
		// Enqueue the navigation script.
		wp_enqueue_script( 'staffmember-quickedit', $script_uri, $dependencies, $version, $in_footer );
		endif;
	}

	/**
	 * Render fields specifically for the staffmember post type. Saves all as serialized data.
	 *
	 * @see Previous field names are
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function render_staffmember_field_callback( $field, $value, $object_id, $object_type, $field_type ) {

		// Keys are field names (ids as well), values are the default value of the field.
		$new_values = [
			'desk_phone'   => '',
			'desk_ext'     => '',
			'mobile_phone' => '',
			'staff_email'  => '',
			'is_sales'     => '',
			'experience'   => '',
			'date_hired'   => '',
		];
		$value      = wp_parse_args( $value, $new_values );
		?>


		<section class="staffmember-info-fields" style="background:var(--blue-100);">


			<!-- desk phone-->
			<div class="field-div" data-fieldid="desk_phone">
				<span>
					<label for="<?= $field_type->_id( '_desk_phone' ); ?>'">Desk Phone</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[desk_phone]' ),
						'id'    => $field_type->_id( '_desk_phone' ),
						'value' => $value['desk_phone'],
						'type'  => 'text_small',
						'class' => '',
					]
				);
				?>
			</div>
			<!-- /desk phone -->

			<!-- extension -->
			<div class="field-div" data-fieldid="desk_ext">
				<span>
					<label for="<?= $field_type->_id( '_desk_ext' ); ?>'">ext</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[desk_ext]' ),
						'id'    => $field_type->_id( '_desk_ext' ),
						'value' => $value['desk_ext'],
						'type'  => 'text_small',
						'class' => '',
					]
				);
				?>
			</div><!-- /extension -->

			<!-- mobile -->
			<div class="field-div" data-fieldid="mobile_phone">
				<span>
					<label for="<?= $field_type->_id( '_mobile_phone' ); ?>'">Mobile</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[mobile_phone]' ),
						'id'    => $field_type->_id( '_mobile_phone' ),
						'value' => $value['mobile_phone'],
						'type'  => 'text_small',
						'class' => '',
					]
				);
				?>
			</div><!-- /mobile-->

			<!-- email -->
			<div class="field-div" data-fieldid="staff_email">
				<span>
					<label for="<?= $field_type->_id( '_staff_email' ); ?>'">Email</label>
				</span>
				<?= $field_type->input(
					[
						'name'    => $field_type->_name( '[staff_email]' ),
						'id'      => $field_type->_id( '_staff_email' ),
						'value'   => $value['staff_email'],
						'type'    => 'text_email',
						'default' => '',
						'class'   => '',
					]
				);
				?>
			</div><!-- /email-->

		</section>
	<?php
	}


}//end class
