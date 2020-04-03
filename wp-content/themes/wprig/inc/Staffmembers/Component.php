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
			'is_management'        => [ $this, 'is_management' ],
			'is_current'           => [ $this, 'is_current' ],
			'get_date_of_hire'     => [ $this, 'get_date_of_hire' ],
		];
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_staffmembers_script' ] );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		add_action( 'init', [ $this, 'create_staffmember_posttype' ] );
		add_filter( 'manage_staffmember_posts_columns', [ $this, 'new_admin_columns' ] );
		add_action( 'manage_staffmember_posts_custom_column', [ $this, 'populate_data' ], 10, 2 );
		add_action( 'quick_edit_custom_box', [ $this, 'new_quickedit_field_management' ], 10, 2 );
		add_action( 'quickedit_javascript', [ $this, 'quickedit_javascript' ] );
		add_filter( 'post_row_actions', [ $this, 'expand_quick_edit_link' ], 10, 2 );
		add_action( 'save_post', [ $this, 'save_quickedit_data' ], 10, 2 );
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
		 * @see WP_Rig\WP_Rig\AdditionalFields\Component->render_staffmember_field_callback()
		 */

		 //phpcs:disable
		/**
		 * Additional Details about the staffmember - field is defined in WP_Rig\WP_Rig\AdditionalFields\Component
		 */
		$args = [
			'name'                => 'Information',
			'id'                  => 'staffInfo', // Name of the custom field type we setup.
			'type'                => 'staffmember',
			'label_cb'            => $this->get_project_address_field_label_cb(),
			'show_names'          => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'             => [ 'staff-information-fields' ],
			'after_row'           => '<hr>',

		];
		$metabox->add_field( $args );

		/**
		 * Checkbox Field to determine whether this staff is considered to be management.
		 */
		$args = [
			'name'    => 'Current',
			'id'      => 'staffCurrent',
			'type'    => 'checkbox',
			'desc'    => 'Is this staffmember currently with the company?',
			'classes' => [ 'checkbox-as-toggle' ],
			'default' => $this->set_checkbox_default( true ),
		];
		$metabox->add_field( $args );

		/**
		 * Calendar Field For Date Of Hire.
		 */
		$args = [
			'name'    => 'Date Hired',
			'id'      => 'staffHire',
			'type'    => 'text_date',
			'desc'    => 'date of hire',
			'classes' => [ 'custom-calendar' ],
			'attributes' => [
				'data-datepicker' => json_encode( [
					'yearRange' => '-42:+0',
				] ),
			],
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
			'classes' => [ 'checkbox-as-toggle' ],
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
		$key = 'staffInfo';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
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
		$key = 'staffHire';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Output the management status of the staffmember.
	 *
	 * @param int $id The ID of the staffmember post.
	 *
	 */
	public function is_management( $id ) {
		$key = 'staffManagement';
		return get_post_meta( $id, $key, true );
	}

	/**
	 * Output the management status of the staffmember.
	 *
	 * @param int $id The ID of the staffmember post.
	 *
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
	public function new_admin_columns( $columns ) {
		unset( $columns['taxonomy-location']);
		$new['cb']              = array_slice( $columns, 0, 1 );
		$new['id']              = 'ID';
		$new['title']           = array_slice( $columns, 0, 1 );
		$new['staffManagement'] = '<span title="Is this a Manager?" style="color:var(--yellow-500);" class="material-icons">supervisor_account</span>';
		$new['staffCurrent']    = '<span title="Is this Person Currently On Staff?" style="color:var(--yellow-500);" class="material-icons">how_to_reg</span>';
		return array_merge( $new, $columns );
	}

	/**
	 * Grab the data for the new fields.
	 *
	 * @param string $column Name of column.
	 * @param int $post_id   Post ID.
	 */
	public function populate_data( $column, $post_id ) {
		switch ( $column ) {
			case 'staffCurrent':
				$color = 'on' === get_post_meta( $post_id, 'staffCurrent', true ) ? 'green' : 'red';
				$title = 'on' === get_post_meta( $post_id, 'staffManagement', true ) ? 'done_outline' : 'clear';
				$output = '<span style="color: var(--' . $color . '-600);" class="material-icons">' . $title . '</span>';
				break;
			case 'staffManagement':
				$color = 'on' === get_post_meta( $post_id, 'staffManagement', true ) ? 'green' : 'red';
				$title = 'on' === get_post_meta( $post_id, 'staffManagement', true ) ? 'done_outline' : 'clear';
				$output = '<span style="color: var(--' . $color . '-600);" class="material-icons">' . $title . '</span>';
				break;
			default:
				$are_they = 'huh?';
				$output = $post_id;
		}
		echo $output;
	}

	/**
	 * Create additional fields for the quick edit screen.
	 *
	 * @param string $column The name of the column.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 * @link https://ducdoan.com/add-custom-field-to-quick-edit-screen-in-wordpress/
	 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
	 */
	public function new_quickedit_field_management( $column ) {
		global $current_screen;
		$html = '';
		$posttype = $current_screen->post_type;
		// if ( 'staffManagement' !== $column ) return;
		if ( 'staffManagement' === $column ) {
			$html .= '<fieldset class="inline-edit-col-left clear">';
			$html .= '<div class="inline-edit-group wp-clearfix">';
			$html .= '<label class="alignleft" for="staffManagement">';
			$html .= '<input type="checkbox" name="staffManagement" id="staffManagement" value="yes"/>';
			$html .= '<span class="checkbox-title">Is Management</span></label>';
			$html .= '</div>';
			$html .= '</fieldset>';

		}

	}

	/**
	 * Only return default value if we don't have a post ID (in the 'post' query variable)
	 *
	 * @param  bool  $default On/Off (true/false)
	 * @return mixed          Returns true or '', the blank default
	 * @link https://github.com/CMB2/CMB2/wiki/Tips-&-Tricks#setting-a-default-value-for-a-checkbox
	 */
	private function set_checkbox_default( $default ) {
		return isset( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
	}

	/**
	 * Handle saving of the Quick Edit Data When added.
	 *
	 * Capture what is entered in the quickedit and save into the posts postmeta field.
	 *
	 * @param int $post_id   Post ID.
	 * @param $post The post.
	*/
	public function save_quickedit_data( $post_id ) {
		// Return if it is called by Autosave -- no need at that point.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return $post_id;
		// Check if user has permission to do this. If not, bail out.
		if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;

		$data = empty( $_POST['staffManagement'] ) ? 0 : 1;
		update_post_meta( $post_id, 'staffManagement', $data );
	}

	/**
	 * A little javascript to help populate the live data.
	 */
	public function quickedit_javascript() {
		global $current_screen;
		if ( 'staffmember' !== $current_screen->post_type ) return;
		?>
		<script type="text/javascript">

		function checked_staffManagement( fieldValue ) {
			inlineEditPost.revert();
			if ( 'on' === fieldValue ) {
				document.getElementById( 'isManagement' ).checked = true;
			}
		}


		</script>
		<?php
	}
	/**
	 * Pass headline news value to checked_headline_news javascript function
	 *
	 * @param array $actions
	 * @param array $post
	 *
	 * @return array
	 */
	function expand_quick_edit_link( $actions, $post ) {
		global $current_screen;

		if ( 'staffmember' !== $current_screen->post_type ) {
			return $actions;
		}

		$data                               = get_post_meta( $post->ID, 'staffManagement', true );
		$data                               = empty( $data ) ? '' : 'on';
		$actions['inline hide-if-no-js']    = '<a href="#" class="editinline" title="';
		$actions['inline hide-if-no-js']    .= esc_attr( 'Edit this item inline' ) . '"';
		$actions['inline hide-if-no-js']    .= " onclick=\"checked_staffManagement('on')\" >";
		$actions['inline hide-if-no-js']    .= 'QuickEdit';
		$actions['inline hide-if-no-js']    .= '</a>';

		return $actions;
	}

	/**
	 * Set the data withinthe quick edit fields I have created.
	 *
	 * @param string $actions Don't know.
	 * @param object $post The post object.
	 */
	public function quickedit_set_data( $actions, $post ) {
		$management = get_post_meta( $post->ID, 'staffManagement', true );
		$current    = get_post_meta( $post->ID, 'staffCurrent', true );


	}


}//end class definition.
