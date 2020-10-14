<?php
/**
 * WP_Rig\WP_Rig\Posttype_Client\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Posttype_Client;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\Posttype_Project\Component as Projects;
use WP_Rig\WP_Rig\AdditionalFields\Component as AdditionalFields;
use WP_Rig\WP_Rig\Posttype_Global\Component as PostTypes;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function get_current_screen;
use function wp_enqueue_script;
use function get_post_meta;
use function wp_localize_script;
use function register_post_type;

/**
 * Clientele posts.
 */
class Component implements Component_Interface, Templating_Component_Interface {
	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'client';
	}

	/**
	 * The plural of this posttype.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name The slug of this posttype..
	 */
	private $plural_name = 'clientele';


	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_quickedit' ] );
		add_filter( 'cmb2_sanitize_testimonial', [ $this, 'sanitize_testimonial_field' ], 10, 5 );
		add_filter( 'cmb2_types_esc_testimonial', [ $this, 'types_esc_testimonial_field' ], 10, 4 );
		add_action( 'manage_client_posts_columns', [ $this, 'make_new_admin_columns' ], 10, 1 ); // Add empty columns for the admin edit screens.
		add_action( 'manage_client_posts_custom_column', [ $this, 'manage_new_admin_columns' ], 10, 2 ); // Add data to the new admin columns.
		add_action( 'add_meta_boxes', [ $this, 'add_metabox_to_client' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'action_enqueue_client_script' ] );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
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
			'get_client_info'         => [ $this, 'get_client_info' ],
			'build_project_selectbox' => [ $this, 'build_project_selectbox' ],
			'get_project_details'     => [ $this, 'get_project_details' ],
		];
	}

	/**
	 * Build the selectbox options for project.
	 *
	 * @todo Create Project Post Type Component.
	 */
	public function get_project_details() {
		return Projects::get_all_projects();
	}

	/**
	 * Build the outer selectbox for project posts.
	 */
	public function build_project_selectbox() {
		$select  = '<select name="clientProjects" id="clientProjects" size="1" multiple>';
		$select .= '<option value="">-- Select Projects --</option>';
		$select .= $this->build_project_options(); // Can this function live inside this class and not be public.
		$select .= '</select>';
		$select .= '<label class="after" for="project-select">Select Multiple: <kbd class="key">Shift</kbd> + click</label>';
		return $select;
	}

	/**
	 * Build options to be added to the select input element for project posts.
	 */
	public function build_project_options() {
		$details  = [];
		$projects = $this->get_project_details();
		$total    = count( $projects );
		for ( $i = 0; $i < $total; $i++ ) {
			$details[] = "<option value=\"{$projects[ $i ]->ID}\">{$projects[ $i ]->post_title}</option>";
		}
		return implode( "\n", $details );
	}

	/**
	 * Add a new metabox on the post type's edit screen. Shows up on the side.
	 *
	 * @param string $post_type The Type of post - in our case.
	 * @param int    $post The identifier of the post - the number.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_meta_box/
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Types
	 * @link https://ducdoan.com/add-custom-field-to-quick-edit-screen-in-wordpress/
	 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
	 */
	public function add_metabox_to_client( $post_type, $post ) {
		$id       = 'client_projects';
		$title    = 'Project Partnership';
		$callback = [ $this, 'display_client_metabox_output_checkboxes' ];
		$screen   = $this->get_slug();
		$context  = 'side';
		$priority = 'high';
		add_meta_box( $id, $title, $callback, $screen, $context, $priority );
	}

	/**
	 * Display additional fields within the client post type, populating as needed.
	 *
	 * @param int $post The post ID.
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_client_metabox_output( $post ) {
		$html = '<div id="client_side_metadata">';
		wp_nonce_field( 'post_metadata', 'client_metadata_field' );
		$html .= $this->build_project_selectbox();
		$html .= '</div>';
		echo $html;
	}

	/**
	 * Displays projects textbox so that we can check the projects the client was involved with.
	 *
	 * @param int $post The post ID.
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_client_metabox_output_checkboxes( $post ) {
		$id_labels  = [];
		$checkboxes = [];
		$projects   = $this->get_project_details();
		$total      = count( $projects );
		$name       = 'clientProjects';
		for ( $i = 0; $i < $total; $i++ ) {
			$project = explode( ' ', $projects[ $i ]->post_title, 3 )[0];
				if ( isset( explode( ' ', $projects[ $i ]->post_title, 3 )[1] ) ) {
					$project .= ' ' . explode( ' ', $projects[ $i ]->post_title, 3 )[1];
				}
			$id_labels[]  = [
				'id'      => $projects[ $i ]->ID,
				'project' => $projects[ $i ]->post_title,
			];
			$checkboxes[] = '<div id="project-checkboxes">';
			$checkboxes[] = '<input type="checkbox" id="' . $projects[ $i ]->ID . '" name="' . $name . '" value="' . $projects[ $i ]->ID . '">';
			$checkboxes[] = '<label for="' . $projects[ $i ]->ID . '" title="' . $projects[ $i ]->post_title . '">' . $project . '</label>';
			$checkboxes[] = '</div>';
		}
		wp_nonce_field( 'post_metadata', 'client_metadata_field' );
		$html  = '<div id="client_side_metadata" class="inline-edit-group wp-clearfix">';
		$html .= implode( '', $checkboxes );
		$html .= '</div>';
		echo $html;
	}

	/**
	 * Saving meta info (used for both traditional and quick-edit saves)
	 *
	 * @param int $post_id The id of the post.
	 */
	public function save_post( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( 'client' === $post_type ) {
			// wp_nonce_field( 'post_metadata', 'client_metadata_field' );
			// check nonce set.
			if ( ! isset( $_POST['client_metadata_field'] ) ) return false;

			// verify nonce.
			if ( ! wp_verify_nonce( $_POST['client_metadata_field'], 'post_metadata' ) ) return false;

			// If not autosaving.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

			$client_projects = $_POST['clientProjects'];

			$jobs = isset ( $client_projects ) ? sanitize_text_field( $client_projects ) : '';

			// This field is saved as serialized data, so I need to use wp_parse_args to get to it.
			update_post_meta( $post_id, 'clientProjects', wp_parse_args( $jobs, get_post_meta( $post_id, 'clientProjects', true ) ) );
		}

	}//end save_post()

	/**
	 * Get all the published clientele.
	 */
	public function get_all_clientele() {

		$args = [
			'post_type'   => 'client',
			'post_status' => 'publish',
		];

		return new \WP_QUERY( $args );
	}


	/**
	 * Get the label for the client information field;
	 *
	 * @return string HTML for the label on the client field within the client post type.
	 */
	private function client_info_label_cb() {
		return '<span class="indigo" style="font-size: 2.5rem;">Client Info</span><hr>';
	}

	/**
	 * Retrieve the postmeta for the year this project completed, started, or is expected to complete.
	 *
	 * @param int $post_id Project post type id.
	 * @return string 4 digit year that the post either completes, was started, or begins.
	 */
	public function get_client_info( $post_id ) {
		return get_post_meta( $post_id, 'clientInformation' ); // false is default, true if I want only the first value within the array.
	}

	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since    1.0.0
	 */
	public function additional_fields() {

		/**
		 * Get the label for the project address fiel.
		 *
		 * @param string $field field string.
		 */
		function get_label_cb( $field ) {
			return '<div class="label_callback">' . ucfirst( $field ) . ' Information</div>';
		}

		$metabox_args = [
			'context'      => 'normal',
			'id'           => 'client-information-metabox',
			'object_types' => [ $this->get_slug() ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Client Overview',
			'show_title'   => false,
			'cmb_styles'   => false,
		];
		$metabox      = new_cmb2_box( $metabox_args );

		// Company logo field.
		$args = [
			'id'           => 'clientInformationLogo',
			'name'         => 'logo',
			'type'         => 'file',
			'options'      => [
				'url' => false,
			],
			'text'         => [
				'add_upload_file_text' => 'add svg',
			],
			'query_args'   => [
				'type' => 'image/svg',
			],
			'preview_size' => 'thumbnail',
		];
		$metabox->add_field( $args );

		// Client Basic Information Field.
		$args = [
			'name'       => 'Client',
			'id'         => 'clientInformation', // Name of the custom field type we setup.
			'type'       => 'client',
			'label_cb'   => get_label_cb( 'client' ),
			'show_names' => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'    => [ 'client_fields' ],
		];
		$metabox->add_field( $args );

		// Hidden field to collect the projects.
		$args = [
			'name'    => 'clProject',
			'id'      => 'clientProjectHidden',
			'name'    => __( 'Attached Posts', 'yourtextdomain' ),
			'desc'    => __( 'Drag posts from the left column to the right column to attach them to this page.<br />You may rearrange the order of the posts in the right column by dragging and dropping.', 'yourtextdomain' ),
			'type'    => 'custom_attached_posts',
			'column'  => true, // Output in the admin post-listing as a custom column. https://github.com/CMB2/CMB2/wiki/Field-Parameters#column has more.
			'options' => [
				'show_thumbnails' => true, // Show thumbnails on the left.
				'filter_boxes'    => true, // Show a text box for filtering the results.
				'query_args'      => [
					'posts_per_page' => 10,
					'post_type'      => 'project',
				], // override the get_posts args.
			],
		];
		$metabox->add_field( $args );

		/**
		 * Group field for Testimonials.
		 */
		$args = [
			'id'          => 'clientTestimonials',
			'type'        => 'group',
			'description' => 'Testimonials from Clientele',
			'repeatable'  => true,
			'show_names'  => false,
			'button_side' => 'right',
			'options'     => [
				'group_title'    => 'Testimonials {#}', // since version 1.1.4, {#} gets replaced by row number!
				'add_button'     => 'Add Testimonial',
				'remove_button'  => 'Remove Testimonial',
				'sortable'       => true,
				'closed'         => true, // true to have the groups closed by default.
				'remove_confirm' => 'Are you sure you want to remove?', // Performs confirmation before removing group.
			],
		];

		$testimonials = $metabox->add_field( $args );

		// Testimonial From the client.
		$args = [
			'classes' => [ 'input-full-width' ],
			'name'    => 'name',
			'desc'    => 'name of person giving the testimonial',
			'default' => '',
			'id'      => 'name',
			'type'    => 'text',
		];
		$metabox->add_group_field( $testimonials, $args );

		$args = [
			'classes' => [ 'input-full-width' ],
			'name'    => 'position',
			'desc'    => 'position of person giving the testimonial',
			'default' => '',
			'id'      => 'position',
			'type'    => 'text',
		];
		$metabox->add_group_field( $testimonials, $args );

		$args = [
			'classes' => [ 'input-full-width' ],
			'name'    => 'testimonial',
			'desc'    => 'testimonial of person giving the testimonial',
			'default' => '',
			'id'      => 'testimonial',
			'type'    => 'text',
		];
		$metabox->add_group_field( $testimonials, $args );

		$args = [
			'classes' => [ 'input-full-width' ],
			'name'    => 'linkedin',
			'desc'    => 'linkedin of person giving the testimonial',
			'default' => '',
			'id'      => 'linkedin',
			'type'    => 'text',
		];
		$metabox->add_group_field( $testimonials, $args );
	}//end additional_fields()

	/**
	 * Set additional administrator columns based on postmeta fields that are added to the post type - columns will have no data just yet.
	 *
	 * @param array $columns Array of columns I have already within the quick edit screen. Existing options are 'cb', 'title', 'taxonomy-signtype', 'date'.
	 * @return array The newly added columns plus the existing columns that I have within the quick edit screen.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 */
	public function make_new_admin_columns( $columns ) {
		global $post_type;
		$adminurl = admin_url( '/wp-admin/edit.php?post_type=' . $post_type . '&orderby=identifier&order=asc' );
		unset( $columns['taxonomy-location'] );
		unset( $columns['date'] );
		$new = [
			'cb'          => array_slice( $columns, 0, 1 ),
			'id'          => 'ID',
			'title'       => array_slice( $columns, 0, 1 ),
			'logo'        => 'logo',
			'testimonial' => '<span title="has testimonial?" style="font-size: 2.9rem;color: var(--gray-100);" class="material-icons"> insert_comment </span>',
			'information' => '<span title="client information" style="font-size: 2.9rem;color: var(--gray-100);" class="material-icons"> info </span>',
		];

		return array_merge( $new, $columns );
	}

	/**
	 * Get testimonial field defaults.
	 */
	public function get_testimonial_defaults() {
		global $post;
		return (array) get_post_meta( $post->ID, 'clientTestimonial', false );

	}

	/**
	 * Get data to include in the new admin columns for this post.
	 *
	 * @param string $column_name Name of the column.
	 * @param int    $post_id ID of the post.
	 */
	public function manage_new_admin_columns( $column_name, $post_id ) {
		global $post_type;

		switch ( $column_name ) {
			case 'id':
				$html = $post_id;
				break;
			case 'logo':
				$thumb_url = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
				$style     = 'style    = "width: 60px;"';
				$thumbnail = '<img src = "' . $thumb_url . '">';
				$logo_url  = wp_get_attachment_image_src( get_post_meta( $post_id, 'clientInformationLogo_id', true ), 'thumbnail', true )[0];
				$logo      = '<img src = "' . $logo_url . '">';
				if ( $thumb_url ) {
					$html = '<img src="' . $thumb_url . '" ' . $style . '">';
				} elseif ( $logo_url ) {
					$html = '<img src="' . $logo_url . '" ' . $style . '">';
				} else {
					$html = '';
				}
				break;
			case 'information':
				$html    = '';
				$info    = get_post_meta( $post_id, 'clientInformation', true );
				$company = $info['company'] ?? '';
				$website = $info['website'] ?? '';
				$since   = $info['since'] ?? '';
				if ( '' !== $company && '' !== $website ) {
					$html .= '<span id="has-company" style="transform: rotate(90deg); font-size: 1.9rem;color: var(--green-500);" class="material-icons" title="' . $company . '" data-url="' . $website . '"> link </span>';
				} else {
					$html .= '<span id="has-company" style="transform: rotate(90deg); font-size: 1.9rem;color: var(--gray-500);" class="material-icons" title="" data-url=""> link </span>';
				}

				if ( '' !== $since ) {
					$html .= "<br><strong> $since</strong>";
				}
				break;
			case 'testimonial':
				$html        = '';
				$testimonial = get_post_meta( $post_id, 'clientTestimonial', false ); // false means entire array; True means just the first one.
				$name        = $testimonial['name'] ?? '';
				$test        = $testimonial['testimonial'] ?? '';
				$linked      = $testimonial['linkedin'] ?? '';
				if ( '' !== $testimonial ) {
					$html .= '<span id="has-testimonial" style="font-size: 1.9rem;color: var(--green-500);" class="material-icons" title="' . $test . '"> add_comment </span>';
				} else {
					$html .= '<span style="font-size: 1.9rem;color: var(--gray-500);" class="material-icons" title=""> rate_review </span>';
				}

				if ( '' !== $name ) {
					$html .= '<span id="has-name" style="font-size: 1.9rem;color: var(--green-500);" class="material-icons" title="has name" alt="' . $name . '"> how_to_reg </span>';
				} else {
					$html .= '<span id="has-name" style="font-size: 1.9rem;color: var(--gray-500);" class="material-icons" title="has name" alt="' . $name . '"> how_to_reg </span>';
				}

				if ( '' !== $linked ) {
					$linked = $this->format_url( $linked );
					$html  .= '<span id="has-linkedin" style="transform: rotate(90deg);font-size: 1.9rem;color: var(--green-500);" class="material-icons" title="has linkedin link" alt="' . $linked . '"> link </span>';
				} else {
					$html .= '<span id="has-linkedin" style="transform: rotate(90deg);font-size: 1.9rem;color: var(--gray-500);" class="material-icons" title="has linkedin link" alt="' . $linked . '"> link </span>';
				}
				break;
			default:
				$html = '';
		}

		echo $html;
	}

	/**
	 * Retrieve the postmeta for the year Jones Sign Co started doing Business With this client
	 *
	 * @param int $id Client Post id.
	 * @return string 4 digit year wherein our relationship with the client began.
	 */
	public function get_client_since( $id ) {
		$key    = 'client[since]';
		$single = true; // If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		return get_post_meta( $id, $key, $single );
	}

	/**
	 * Gets an array of the client's projects with Jones Sign Company -- as long as they are within the project post type.
	 *
	 * @param int $post_id The ID of the post.
	 */
	public function get_client_project_ids( $post_id ) {
		return get_post_meta( $post_id, 'clientProjectHidden', false ); // false means it will return an array with all items.
	}

	/**
	 * Determine which projects are selected.
	 *
	 * @param int $post_id ID of the post.
	 */
	public function get_client_project_links( $post_id ) {
		$projects = $this->get_client_project_ids( $post_id );
		$links    = [];
		foreach ( $projects as $project ) {
			$links[] = get_permalink( $project );
		}
		return $links;
	}
	/**
	 * Enqueue the javascript to populate the post type's quickedit fields.
	 */
	public function enqueue_quickedit() {
		global $post_type;
		global $pagenow;
		// Ensure that we don't bother using this javascript unless we are in the client post type on the admin end.
		if ( ! ( is_admin() && 'client' === $post_type ) ) {
			return;
		}
		// Within the development environment, use the non-minified version.
		$script_uri = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/client_quickedit.js' ) : get_theme_file_uri( '/assets/js/client_quickedit.min.js' );
		$handle     = 'client-quickedit-script';
		$depend     = [ 'jQuery', 'inline-edit-post' ];
		$version    = '19';
		$footer     = true;

		wp_enqueue_script( $handle, $script_uri, $depend, $version, $footer );
	}



	/**
	 * Enqueues javascript that will allow me to access project posts.
	 */
	public function action_enqueue_client_script() {

		global $post_type;
		global $post;
		global $pagenow;
		$metakey = 'clientProjectHidden';
		// If the AMP plugin is active, return early.
		if ( ! ( 'post.php' === $pagenow && 'client' === $post_type ) ) {
			return;
		}
		$script_dependencies = [];
		wp_enqueue_script(
			'wp-rig-client',
			get_theme_file_uri( '/assets/js/client.min.js' ),
			$script_dependencies,
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/client.min.js' ) ),
			false
		);

		/*
		Allows us to add the js right within the module.
		Setting 'precache' to true means we are loading this script in the head of the document.
		By setting 'async' to true,it tells the browser to wait until it finishes loading to run the script.
		'Defer' would mean wait until EVERYTHING is done loading to run the script.
		*/
		wp_script_add_data( 'wp-rig-client', 'async', true );
		wp_script_add_data( 'wp-rig-client', 'precache', true );
		wp_localize_script(
			'wp-rig-client',
			'clientProjectsAll',
			[
				'projects' => $this->get_project_details(),
				'selected' => get_post_meta( $post->ID, $metakey, true ),
			]
		);
	}


}

