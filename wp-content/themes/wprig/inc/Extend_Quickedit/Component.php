<?php
/**
 * WP_Rig\WP_Rig\Extend_Quickedit\Component class.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Extend_Quickedit;

use WP_Rig\WP_Rig\Staffmembers\Component as Staffmembers;
use WP_Rig\WP_Rig\Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function add_filter;


/**
 * TABLE OF CONTENTS.
 * 1. get_state_options()
 * 2. get_slug()
 * 3. initialize()
 * 4. cmb2_render_rating_field_callback() -- Star Rating.
 * 5. render_address_field_callback().
 * 6. render_staffmember_field_callback().
 * 7. render_jonesaddress_field_callback().
 */
class Component implements Component_Interface {

	/**
	 * The instance has yet to be established.
	 *
	 * @var string
	 */
	private static $instance = null;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'extendquickedit';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// add_action( 'manage_staffmember_posts_columns', array( $this, 'add_custom_admin_column' ), 10, 1 ); // add custom column.
		// add_action( 'manage_staffmember_posts_custom_column', array( $this, 'manage_custom_admin_columns' ), 10, 2 ); // populate column.
		// add_action( 'quick_edit_custom_box', array( $this, 'display_quick_edit_custom' ), 10, 2 ); // output form elements for quickedit interface.
		// add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ) ); // enqueue admin script (for prepopulting fields with JS).
		// add_action( 'add_meta_boxes', array( $this, 'add_metabox_to_posts' ), 10, 2 ); // add metabox to posts to add our meta info.
		// add_action( 'save_post', array( $this, 'save_post' ), 10, 1 ); // call on save, to update metainfo attached to our metabox.
	}






	/**
	 * Enqueue admin js to pre-populate the quick-edit fields
	 */
	public function enqueue_admin_scripts_and_styles() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}
		// Only use the minified script if we are in a production environment.
		$script_uri   = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/staffmember_quickedit.js' ) : get_theme_file_uri( '/assets/js/staffmember_quickedit.min.js' );
		$version      = '20';
		$dependencies = [ 'jquery', 'inline-edit-post' ]; // location: wp-admin/js/inline-edit-post.js.
		$in_footer    = true; // True if we want to load the script in footer, false to load within header.
		// Enqueue the navigation script.
		wp_enqueue_script( 'staffmember-quickedit', $script_uri, $dependencies, $version, $in_footer );
	}

	/**
	 * Display our custom content on the quick-edit interface, no values can be pre-populated (all done in JS)
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
				$html .= '<div class="inline-edit-group wp-clearfix toggle_checkbox">';
				$html .= '<input name="staff_management" type="checkbox" id="staff_management" value="YES"/>';
				$html .= '<label for="staff_management">Management?</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			// Output checkbox with name attribute staff_current.
			case 'staff_current':
				$html .= '<fieldset class="inline-edit-col-left clear">';
				$html .= '<div class="inline-edit-group wp-clearfix toggle_checkbox">';
				$html .= '<input name="staff_current" type="checkbox" id="staff_current" value="YES"/>';
				$html .= '<label for="staff_current">';
				$html .= 'current?</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'jones_id':
				$html .= '<fieldset class="inline-edit-col-left clear">';
				$html .= '<div class="inline-edit-group wp-clearfix">';
				$html .= '<label for="jones_id" class="alignleft" >Staff ID</label>';
				$html .= '<input type="text" class="text_small" name="jones_id" id="jones_id"/>';
				$html .= '</div>';
				$html .= '<div class="inline-edit-group wp-clearfix">';
				$html .= '<label for="staff_title" class="alignleft" >Title</label>';
				$html .= '<input type="text" name="staff_title" id="staff_title"/>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			default:
				$html = '';
		} // End switch.
		echo $html;
	}

	/**
	 * Add a custom columns on the admin page -- will not hold any data.
	 *
	 * @param string $columns name of columns.
	 *
	 * @see manage_custom_admin_columns() for method to populate data into these columns.
	 *
	 * @link https://developer.wordpress.org/reference
	 */
	public function add_custom_admin_column( $columns ) {
		// return Staffmembers::new_admin_columns( $columns );
		global $post_type;
		$adminurl = admin_url( '/wp-admin/edit.php?post_type=' . $post_type . '&orderby=identifier&order=asc' );
		unset( $columns['taxonomy-location'] );
		unset( $columns['date'] );
		unset( $columns['cb'] );
		$id['cb']                        = '<input type = "checkbox"/>';
		$id['identifier']                = '#';
		$id['staff_title']               = 'Position';
		$id['jones_id']                  = 'Jones ID';
		$new_columns['staff_management'] = 'Mgmt?';
		$new_columns['staff_current']    = 'Current?';

		return array_merge( $id, $columns, $new_columns );
	}

	/**
	 * Grab the data for the new columns in the admin area for this post type.
	 *
	 * @param string $column_name Name of column.
	 * @param int    $post_id   Post ID.
	 */
	public function manage_custom_admin_columns( $column_name, $post_id ) {
		// Staffmembers::populate_data( $column_name, $post_id );
		$html    = '';
		switch ( $column_name ) {
			case 'identifier':
				$html = $post_id;
				break;
			case 'staff_title':
				$staff_info = get_post_meta( $post_id, 'staffInfo', true );
				$jobtitle   = $staff_info['full_title'];
				$html       = '<div id="staff_title_' . $post_id . '">';
				$html      .= $jobtitle;
				$html      .= '</div>';
				break;
			case 'jones_id':
				$html     = $this->output_circular_images( $post_id );
				$staff_id = get_post_meta( $post_id, 'staffID', true );
				$html    .= '<div id="jones_id_' . $post_id . '" data-jonesid="' . $staff_id . '">' .  $staff_id . '</div>';
				break;
			case 'staff_management':
				$state = get_post_meta( $post_id, 'staffManagement', true ) ? 'on' : 'off';
				$html  = '<div id="staff_management_' . $post_id . '" data-state="' . $state . '">';
				$color = 'on' === $state ? 'var(--indigo-600)' : 'var(--gray-500)';
				$html .= '<span class="material-icons" style="color: ' . $color . ';">supervisor_account</span></span>';
				$html .= '</div>';
				break;
			case 'staff_current':
				$state = get_post_meta( $post_id, 'staffCurrent', true ) ? 'on' : 'off';
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
	 * Saving meta info (used for both traditional and quick-edit saves)
	 *
	 * @param int $post_id The id of the post.
	 */
	public function save_post( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( 'staffmember' === $post_type ) {

			// check nonce set.
			if ( ! isset( $_POST['post_metadata_field'] ) ) {
					return false;
			}

			// verify nonce.
			if ( ! wp_verify_nonce( $_POST['post_metadata_field'], 'post_metadata' ) ) {
					return false;
			}

			// if not autosaving.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return false;
			}

			$staff_id      = sanitize_text_field ( $_POST['jones_id'] ) ?? '';
			$staff_title   = sanitize_text_field ( $_POST['staff_title'] ) ?? '';
			$is_current    = sanitize_text_field ( $_POST['staff_current'] ) ?? '';
			$is_management = sanitize_text_field ( $_POST['staff_management'] ) ?? '';

			// This field is saved as serialized data, so I need to use wp_parse_args to get to it.
			update_post_meta( $post_id, 'staffInfo', wp_parse_args( [ 'full_title' => $staff_title ], get_post_meta( $post_id, 'staffInfo', true ) ) );

			update_post_meta( $post_id, 'staffID', $staff_id );
			update_post_meta( $post_id, 'staffCurrent', $is_current );
			update_post_meta( $post_id, 'staffManagement', $is_management );
		}//end if 'staffmember' === $post_type
	}//end save_post()

	/**
	 * Get singleton instance
	 */
	public static function getInstance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
				}
			return self::$instance;
	}



}//end class

$staffmember_quickedit = Component::getInstance();

