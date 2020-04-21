<?php
/**
 * WP_Rig\WP_Rig\Projects\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Projects;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\Projects\ProjectPost;
use WP_Rig\WP_Rig\AdditionalFields\Component as AdditionalFields;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function get_current_screen;
use function wp_enqueue_script;
use function get_post_meta;
use function wp_localize_script;
use function register_post_type;

/**
 * Class for improving accessibility among various core features.
 */
class Component implements Component_Interface, Templating_Component_Interface {


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
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// CMB2 field specifically for a project address.
		add_filter( 'cmb2_render_projectinfo', [ $this, 'render_projectinfo_field_callback' ], 10, 5 );
		// Enqueue a frontend script to utilize for project post types.
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_projects_script' ] );

		// Add empty columns for the admin edit screens.
		add_action( 'manage_project_posts_columns', [ $this, 'make_new_admin_columns' ], 10, 1 );
		// Add data to the new admin columns.
		add_action( 'manage_project_posts_custom_column', [ $this, 'manage_new_admin_columns' ], 10, 2 );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		add_action( 'init', [ $this, 'create_posttype' ] );

		// Load the javascript admin script for prepopulating project quickedit fields on the admin end.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_project_post_admin_javascript' ] );
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
	 * Get the label for the project address field;
	 *
	 * @return string HTML for the label on the address field within the project post type.
	 */
	private function get_project_address_field_label_cb() {
		return '<span class="indigo" style="font-size: 2.5rem;">Project Location Data </span><hr>';
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
	public function create_the_posttype() {
		return ProjectPost::create_posttype();
	}


	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since    1.0.0
	 */
	public function additional_fields() {
		return ProjectPost::additional_fields();
	}

	/**
	 * Set additional administrator columns based on postmeta fields that are added to the post type - columns will have no data just yet.
	 *
	 * @param array $columns Array of columns I have already within the quick edit screen. Existing options are 'cb', 'title', 'taxonomy-signtype', 'date'
	 * @return array The newly added columns plus the existing columns that I have within the quick edit screen.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 */
	public function make_new_admin_columns( $columns ) {
		return ProjectPost::make_new_admin_columns( $columns );
	}

	/**
	 * Get data to include in the new admin columns for this post.
	 *
	 * @param string $column_name Name of the column;
	 * @param int $post_id ID of the post;
	 */
	public function manage_new_admin_columns( $column_name, $post_id ) {
		return ProjectPost::manage_new_admin_columns( $column_name, $post_id );
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

	/**
	 * Create & Render a Project Address Field to use with CMB2
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function render_projectinfo_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		return ProjectPost::render_projectinfo_field_callback( $field, $value, $object_id, $object_type, $field_type );
	}//end render_address_field_callback()

	/**
	 * Enqueues javascript that will allow me to access project posts.
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
	 * Enqueue the javascript to populate the project post type's quickedit fields.
	 */
	public function enqueue_project_post_admin_javascript() {
		global $post_type;
		// Ensure that we don't bother using this javascript unless we are in the project post type on the admin end.
		if ( ! ( is_admin() && 'project' === $post_type ) ) {
			return;
		}
		// Within the development environment, use the non-minified version.
		$script_uri = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/project_quickedit.js' ) : get_theme_file_uri( '/assets/js/project_quickedit.min.js' );
		$handle     = 'projects-quickedit-script';
		$depend     = [ 'jQuery', 'inline-edit-post' ];
		$version    = '19';
		$footer     = true;

		wp_enqueue_script( $handle, $script_uri, $depend, $version, $footer );
	}

}//end class definition.
