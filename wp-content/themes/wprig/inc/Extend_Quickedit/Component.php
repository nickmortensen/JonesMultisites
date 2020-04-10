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
		add_action( 'manage_staffmember_posts_columns', array( $this, 'add_custom_admin_column' ), 10, 1 ); // add custom column.
		add_action( 'manage_staffmember_posts_custom_column', array( $this, 'manage_custom_admin_columns' ), 10, 2 ); // populate column.
		add_action( 'quick_edit_custom_box', array( $this, 'display_quick_edit_custom' ), 10, 2 ); // output form elements for quickedit interface.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts_and_styles' ) ); // enqueue admin script (for prepopulting fields with JS).
		// add_action( 'add_meta_boxes', array( $this, 'add_metabox_to_posts' ), 10, 2 ); // add metabox to posts to add our meta info.
		add_action( 'save_post', array( $this, 'save_post' ), 10, 1 ); // call on save, to update metainfo attached to our metabox.
		add_action( 'cmb2_init', [ $this, 'add_metabox_to_posts' ] );
	}



	/**
	 * Add a new metabox on our single post edit screen
	 *
	 * @param string $post_type Type of post - in our case here - 'staffmember'.
	 * @param int    $post ID of post.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 * @link https://ducdoan.com/add-custom-field-to-quick-edit-screen-in-wordpress/
	 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
	 */
	public function add_metabox_to_posts() {


			// add_meta_box( 'additional-meta-box', __( 'Additional Info', 'post-quick-edit-extension' ), array( $this, 'display_metabox_output' ), 'staffmember', 'side', 'high' );
			$args = [
				'id'           => 'more-staff-details',
				'context'      => 'side',
				'priority'     => 'high',
				'object_types' => [ 'staffmember' ],
				'show_in_rest' => \WP_REST_Server::ALLMETHODS,
				'show_names'   => true,
				'title'        => 'More Details',

			];

			$cmb = new_cmb2_box( $args );

			$cmb->add_field( [
				'row_classes' => [ 'customized_radio', 'staff_management' ],
				'desc'        => 'Management',
				'type'        => 'checkbox',
				'id'          => 'staffManagement',
				'default'     => $this->default_for_staff_current_checkbox_field( false ),
			] );

			$cmb->add_field( [
				'row_classes' => [ 'customized_radio', 'staff_current' ],
				'desc'        => 'Current',
				'type'        => 'checkbox',
				'id'          => 'staffCurrent',
				'default'     => $this->default_for_staff_current_checkbox_field( true ),
			] );

			$args = [
				'type' => 'text_small',
				'name' => 'Jones ID',
				'id'   => 'staffID',
			];
			$cmb->add_field( $args );
	}

	/**
	 * Set a checkbox efault value if we don't have a post ID (in the 'post' query variable).
	 *
	 * @param  bool  $default On/Off (true/false)
	 * @return mixed          Returns true or '', the blank default
	 */
	public function default_for_staff_current_checkbox_field( $default ) {
		return isset ( $_GET['post'] ) ? '' : ( $default ? (string) $default : '' );
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
		$in_footer    = false; // True if we want to load the script in footer, false to load within header.
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

		// output post featured checkbox
		if ( $column === 'post_featured' ) {
			$html .= '<fieldset class="inline-edit-col-left clear">';
			$html .= '<div class="inline-edit-group wp-clearfix">';
			$html .= '<label class="alignleft" for="post_featured_no">';
			$html .= '<input type="radio" name="post_featured" id="post_featured_no" value="no"/>';
			$html .= '<span class="checkbox-title">Post Not Featured QE</span></label>';
			$html .= '<label class="alignleft" for="post_featured_yes">';
			$html .= '<input type="radio" name="post_featured" id="post_featured_yes" value="yes"/>';
			$html .= '<span class="checkbox-title">Post Featured</span></label>';

			$html .= '</div>';
			$html .= '</fieldset>';
		}
		// output post rating select field
		elseif ( 'post_rating' === $column ) {
			$html .= '<fieldset class="inline-edit-col-center ">';
			$html .= '<div class="inline-edit-group wp-clearfix">';
			$html .= '<label class="alignleft" for="post_rating">Post Rating QE</label>';
			$html .= '<select name="post_rating" id="post_rating" value="">';
			$html .= '<option value="1">1</option>';
			$html .= '<option value="2">2</option>';
			$html .= '<option value="3">3</option>';
			$html .= '<option value="4">4</option>';
			$html .= '<option value="5">5</option>';
			$html .= '</select>';
			$html .= '</div>';
			$html .= '</fieldset>';
		} // output post subtitle text field.
		elseif ( 'post_subtitle' == $column ) {
			$html .= '<fieldset class="inline-edit-col-right ">';
			$html .= '<div class="inline-edit-group wp-clearfix">';
			$html .= '<label class="alignleft" for="post_rating">Post Subtitle QE </label>';
			$html .= '<input type="text" name="post_subtitle" id="post_subtitle" value="" />';
			$html .= '</div>';
			$html .= '</fieldset>';
		}

		echo $html;
	}

	/**
	 * Add a custom column to hold our data.
	 *
	 * @param string $columns name of columns.
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
		$id['cb']                              = '<input type="checkbox"/>';
		$id['identifier']                      = '#';
		$id['staffmember_title']               = 'Position';
		$id['jones_id']                        = 'Jones ID';
		$new_columns['staffmember_management'] = 'Mgmt?';
		$new_columns['staffmember_current'] = 'Current?';

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
		$newcols = [ 'identifier', 'jones_id', 'staffmember_management', 'staffmember_title' ];
		$html    = '';
		switch ( $column_name ) {
			case 'identifier':
				$html = $post_id;
				break;
			case 'staffmember_title':
				$staff_info = get_post_meta( $post_id, 'staffInfo', true );
				$jobtitle   = $staff_info['full_title'];
				$html       = '<div id="staff_title_' . $post_id . '">';
				$html      .= $jobtitle;
				$html      .= '</div>';
				break;
			case 'jones_id':
				$html  = $this->output_circular_images( $post_id );
				$html .= '<div id="staff_id_' . $post_id . '">';
				$html .= get_post_meta( $post_id, 'staffID', true ) ?? '';
				$html .= '</div>';
				break;
			case 'staffmember_management':
				$state = get_post_meta( $post_id, 'staffManagement', true ) ? 'on' : 'off';
				$html  = '<div id="staff_management_' . $post_id . '" data-state="' . $state . '">';
				$color = 'on' === $state ? 'var(--indigo-600)' : 'var(--gray-500)';
				$html .= '<span class="material-icons" style="color: ' . $color . ';">supervisor_account</span></span>';
				$html .= '</div>';
				break;
			case 'staffmember_current':
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
	 * Output the image for the person as an image in a circle
	 *
	 * @param int $post_id The ID of the staffmember.
	 * @return string The HTML to display the photo.
	 */
	public function output_circular_images( $post_id ) {
		$size      = 'thumbnail';
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

			// all good to save.
			$featured_post = isset( $_POST['post_featured'] ) ? sanitize_text_field( $_POST['post_featured'] ) : '';
			$post_rating   = isset( $_POST['post_rating'] ) ? sanitize_text_field( $_POST['post_rating'] ) : '';
			$post_subtitle = isset( $_POST['post_subtitle'] ) ? sanitize_text_field( $_POST['post_subtitle'] ) : '';

			$staff_id      = sanitize_text_field ( $_POST['staffID'] ) ?? '';
			$is_current    = sanitize_text_field ( $_POST['isCurrent'] ) ?? '';
			$is_management = sanitize_text_field ( $_POST['isManagement'] ) ?? '';

			update_post_meta( $post_id, 'post_featured', $featured_post );
			update_post_meta( $post_id, 'post_rating', $post_rating );
			update_post_meta( $post_id, 'post_subtitle', $post_subtitle );

			update_post_meta( $post_id, 'staffCurrent', $staff_id );
			update_post_meta( $post_id, 'isCurrent', $is_current );
			update_post_meta( $post_id, 'isManagement', $is_management );
		}
	}

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
