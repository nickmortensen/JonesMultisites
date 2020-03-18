<?php
/**
 * WP_Rig\WP_Rig\Projects\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Projects;

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
	 * The plural of this posttype.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name The slug of this posttype..
	 */
	private $plural_name = 'projects';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'project';
	}

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_project() : string {
		return 'should push out project profile';
	}

	/**
	 * Get the label for the project address field;
	 *
	 * @return string HTML for the label on the address field within the project post type.
	 */
	private function get_project_address_field_label_cb() {
		return '<span class="indigo" style="font-size: 2.5rem;">Project Location Data </span><hr>';
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
			'get_project_status'    => [ $this, 'get_project_status' ],
			'get_project_year'      => [ $this, 'get_project_year' ],
			'get_project_tease'     => [ $this, 'get_project_tease' ],
			'get_project_location'  => [ $this, 'get_project_location' ],
			'get_project_narrative' => [ $this, 'get_project_narrative' ],
		];
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_projects_script' ] );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		add_action( 'init', [ $this, 'create_project_posttype' ] );
	}

	/**
	 * Enqueues a script that improves navigation menu accessibility.
	 */
	public function action_enqueue_projects_script() {

		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		// Enqueue the navigation script. The last element asks whether to load the script within the footer. We don't want that.
		wp_enqueue_script(
			'wp-rig-projects',
			get_theme_file_uri( '/assets/js/projects.min.js' ),
			[],
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/projects.min.js' ) ),
			false
		);

		/*
		Allows us to add the js right within the module.
		Setting 'precache' to true means we are loading this script in the head of the document.
		By setting 'async' to true,it tells the browser to wait until it finishes loading to run the script.
		'Defer' would mean wait until EVERYTHING is done loading to run the script.
		*/
		wp_script_add_data( 'wp-rig-projects', 'async', true );
		wp_script_add_data( 'wp-rig-projects', 'precache', true );
		wp_localize_script(
			'wp-rig-projects',
			'wpRigScreenReaderText',
			[
				'expand'   => __( 'Expand child menu', 'wp-rig' ),
				'collapse' => __( 'Collapse child menu', 'wp-rig' ),
			]
		);
	}

	/**
	 * List of states. To translate, pass array of states in the 'state_list' field param.
	 *
	 * @var array
	 */
	//phpcs:disable
	protected static $state_list = [ 'AL' => 'Alabama', 'AK' => 'Alaska', 'AZ' => 'Arizona', 'AR' => 'Arkansas', 'CA' => 'California', 'CO' => 'Colorado', 'CT' => 'Connecticut', 'DE' => 'Delaware', 'DC' => 'District Of Columbia', 'FL' => 'Florida', 'GA' => 'Georgia', 'HI' => 'Hawaii', 'ID' => 'Idaho', 'IL' => 'Illinois', 'IN' => 'Indiana', 'IA' => 'Iowa', 'KS' => 'Kansas', 'KY' => 'Kentucky', 'LA' => 'Louisiana', 'ME' => 'Maine', 'MD' => 'Maryland', 'MA' => 'Massachusetts', 'MI' => 'Michigan', 'MN' => 'Minnesota', 'MS' => 'Mississippi', 'MO' => 'Missouri', 'MT' => 'Montana', 'NE' => 'Nebraska', 'NV' => 'Nevada', 'NH' => 'New Hampshire', 'NJ' => 'New Jersey', 'NM' => 'New Mexico', 'NY' => 'New York', 'NC' => 'North Carolina', 'ND' => 'North Dakota', 'OH' => 'Ohio', 'OK' => 'Oklahoma', 'OR' => 'Oregon', 'PA' => 'Pennsylvania', 'RI' => 'Rhode Island', 'SC' => 'South Carolina', 'SD' => 'South Dakota', 'TN' => 'Tennessee', 'TX' => 'Texas', 'UT' => 'Utah', 'VT' => 'Vermont', 'VA' => 'Virginia', 'WA' => 'Washington', 'WV' => 'West Virginia', 'WI' => 'Wisconsin', 'WY' => 'Wyoming' ];
	//phpcs:

	/**
	 * Creates the custom post type: 'Project'.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/

	 */
	public function create_project_posttype() {
		$icon_for_posttype   = 'dashicons-admin-multisite';
		$taxonomies_to_apply = [ 'expertise', 'signtype', 'services' ];
		$singular            = 'project';
		$plural              = $singular . 's';
		$labels   = [
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
		$args = [
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
		register_post_type( 'project', $args );
	} // post type 'project' create.


	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since    1.0.0
	 */
	public function additional_fields() {
		$after        = '<hr>';
		$prefix       = 'project_';
		$metabox_args = [
			'context'      => 'normal',
			'classes'      => $prefix . '-posttype-metabox',
			'id'           => $prefix . '_metabox',
			'object_types' => [ 'project' ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Project Overview',
			'cmb_styles'   => true, // Disable cmb2 stylesheet by setting to false.
		];
		// Create the metabox to add fields to.
		$metabox = new_cmb2_box( $metabox_args );
		/**
		 * Get the label for the project address field;
		 */
		function get_label_cb() {
			return '<span class="indigo" style="font-size: 2.5rem;">Project Location Data </span><hr>';
		}

		/**
		 * Project Location Field. CUSTOM.
		 *
		 * @see render_address_field_callback()
		 */
//phpcs:disable
		/**
		 * Location of the project - address plus latitude and longitude.
		 */
		$args = [
			'name'                => 'Project Location',
			'desc'                => 'Address of the Project',
			'id'                  => 'projectLocation', // Name of the custom field type we setup.
			'type'                => 'address',
			'object_types'        => [ 'project' ], // Only show on project post types.
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
		 * Project local folder within the jones sign company shared servers setup.
		 */
		$args = [
			'name' => 'Local Folder',
			'id'   => 'projectLocalFolder',
			'type' => 'text',
			'desc' => 'Where is the archive on the Jones Sign Internal Servers?',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectJobNumber.
		 * Assigned Project Job Number.
		 */
		$args = [
			'name'         => 'Job#',
			'desc'         => 'Assigned Job Number',
			'default'      => '',
			'id'           => 'projectJobNumber',
			'type'         => 'text_small',
			'object_types' => [ 'project' ], // Only show on project post types.
			'before_row'   => '', // callback.
			'after_row'    => '<hr>',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectJobStatus.
		 * Status of job: upcoming, complete, ongoing.
		 */
		$args = [
			'name'         => 'Status',
			'id'           => 'projectJobStatus',
			'type'         => 'radio_inline',
			'options'      => [
				'complete' => 'Complete', // completion_year.
				'ongoing'  => 'Ongoing',  // completion_expected.
				'upcoming' => 'Upcoming', // year_started.
			],
			'default'      => '',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectCompletedYear.
		 * Year Job was completed - dependent on choosing the 'complete' option from the 'jobStatus' field.
		 */
		$args = [
			'before_row'   => '<div id="statusYearInputs">',
			'classes'      => 'statusDate',
			'name'         => 'Complete',
			'desc'         => '4 digit year representing when this project completed',
			'id'           => 'projectCompletedYear', // was 'jobYear' - alter that in your database.
			'type'         => 'text_small',
			'attributes'   => [
				'data-conditionalid'    => 'projectJobStatus', // the ID value of the field that needs to be selected in order for this one to show up.
				'data-conditionalvalue' => 'complete',
			],
		];
		$metabox->add_field( $args );

		/**
		 * ID projectExpectedCompletionYear.
		 * Year Job is expected to wrap.
		 * Dependent on choosing the 'ongoing' option from the 'projectJobStatus' field.
		 */
		$args = [
			'classes'      => 'statusDate hidden',
			'name'         => 'Expected',
			'desc'         => '4 digit year for when this upcoming project is expected to complete',
			'id'           => 'projectExpectedCompletionYear', // was 'jobYear' - alter that in your database.
			'type'         => 'text_small',
			'attributes'   => [
				'data-conditionalid'    => 'projectJobStatus', // the ID value of the field that needs to be selected in order for this one to show up.
				'data-conditionalvalue' => 'upcoming',
			],

		];
		$metabox->add_field( $args );

		/**
		 * ID projectYearStarted.
		 * Year Job was started - dependent on choosing the 'ongoing' option from the 'projectJobStatus' field.
		 */
		$args = [
			'after_row'    => '</div>',
			'classes'      => 'statusDate hidden',
			'name'         => 'Started',
			'desc'         => '4 digit year of when we started working on this ongoing project',
			'id'           => 'projectYearStarted',
			'type'         => 'text_small',
			'attributes'   => [
				'data-conditionalid'    => 'projectJobStatus', // the ID value of the field that needs to be selected in order for this one to show up.
				'data-conditionalvalue' => 'ongoing',
			],
		];
		$metabox->add_field( $args );

		/**
		 * ID projectTease Teaser Field: Text Field.
		 * 140 characters or less summing up the project.
		 */
		$args = [
			'class'        => 'input-full-width',
			'name'         => 'Tease',
			'desc'         => 'Brief Synopsis of the project. 140 characters or less.',
			'id'           => 'projectTease',
			'type'         => 'text',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectAltName: Returns string.
		 * The alternative name for this project.
		 */
		$args = [
			'class'        => 'input-full-width',
			'name'         => 'Alt',
			'desc'         => 'Is there an alternate name or client for this project?',
			'default'      => '',
			'id'           => 'projectAltName',
			'type'         => 'text',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectSVGLogo. Returns URL.
		 * A client logo. Should be square and ideally SVG.
		 */
		$args = [
			'name'         => 'SVG',
			'desc'         => 'Upload a client SVG logo.',
			'id'           => 'projectSVGLogo',
			'type'         => 'file',
			'options'      => [ 'url' => false ],
			'text'         => [ 'add_upload_file_text' => 'Add SVG' ],
			'query_args'   => [ 'type' => 'image/svg+xml' ],
			'preview_size' => 'medium',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectNarrative. Returns Text.
		 * 1 or 2 paragraphs about the project.
		 */
		$args = [
			'name'         => 'Narrative',
			'desc'         => 'Project Write Up / Narrative',
			'id'           => 'projectNarrative',
			'type'         => 'textarea_code',
		];
		$metabox->add_field( $args );

		/**
		 * Square Images for Slideshow?
		 *
		 * @todo It would be good to reduce the images to select from to ones that have height and width attributes that are equal. Figure that out.
		 */
		$args = [
			'name'         => 'Square',
			'desc'         => 'Add any images that have an equal height and width here.',
			'id'           => 'projectImagesSquare',
			'type'         => 'file_list',
			'preview_size' => [ 100, 100 ],
			'query_args'   => [ 'type' => 'image' ],
			'text'         => [
				'add_upload_files_text' => 'Add Images',
				'remove_image_text'     => 'Remove',
				'file_text'             => 'Files:',
				'file_download_text'    => 'DL',
				'remove_text'           => 'ReplaceREMOVE',
			],
		];
		$metabox->add_field( $args );

		/**
		 * 4x3 Images for Slideshow
		 */
		$args = [
			'name'         => '4x3',
			'desc'         => 'Typically 4x3',
			'id'           => 'projectImagesSlideshow',
			'type'         => 'file_list',
			'preview_size' => [ 100, 100 ],
			'query_args'   => [ 'type' => 'image' ],
			'text'         => [
				'add_upload_files_text' => 'Add slides',
				'remove_image_text'     => 'Remove',
				'file_text'             => 'Files:',
				'file_download_text'    => 'DL',
				'remove_text'           => 'ReplaceREMOVE',
			],
		];
		$metabox->add_field( $args );

	}//end additional_fields()

	/**
	 * Retrieve the postmeta for 'projectJobStatus' for this project.
	 *
	 * @param int $id Project post type id.
	 */
	public function get_project_status( $id ) {
		$key    = 'projectJobStatus';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the postmeta for the year this project completed, started, or is expected to complete.
	 *
	 * @param int $id Project post type id.
	 * @return string 4 digit year that the post either completes, was started, or begins.
	 */
	public function get_project_year( $id, $status ) {
		$status = $this->get_project_status( $id );
		switch ( $status ) {
			case 'ongoing':
				$key    = 'projectYearStarted';
				break;
			case 'upcoming':
				$key    = 'projectExpectedCompletionYear';
				break;
			default:
				$key    = 'projectCompletedYear';
		}

		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the postmeta for 'projectTease' for this project.
	 *
	 * @param int $id Project post type id.
	 */
	public function get_project_tease( $id ) {
		$key    = 'projectTease';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the postmeta for 'projectLocation' for this project.
	 *
	 * @param int $id Project post type id.
	 * @return object $output json data for the address of this location.
	 */
	public function get_project_location( $id ) {
		$key    = 'projectLocation';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the postmeta for 'projectNarrative' for this project.
	 *
	 * @param int $id Project post type id.
	 * @return string $output The text of the project Narrative postmeta field for this project.
	 */
	public function get_project_narrative( $id ) {
		$key    = 'projectNarrative';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the postmeta for 'projectLocalFolder' for this project.
	 *
	 * @param int $id Project post type id.
	 * @return string $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	private function get_project_local_folder( $id ) {
		$key    = 'projectLocalFolder';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the postmeta for 'projectJobNumber' for this project.
	 *
	 * @param int $id Project post type id.
	 * @return string $output The internal job number of the project.
	 */
	private function get_project_job_number( $id ) {
		$key    = 'projectJobNumber';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		$output = get_post_meta( $id, $key, $single );
		return $output;
	}


}//end class definition.
