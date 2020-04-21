<?php
/**
 * WP_Rig\WP_Rig\Projects\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Projects\ProjectPost;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
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
class ProjectPost {

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
	public function get_project_post() : string {
		return 'frontend display of a project post';
	}

	/**
	 * The job completion statuses
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type The arguments of this taxonomy..
	 */
	public $statuses = [
		'complete' => 'Complete',
		'ongoing'  => 'Ongoing',
		'upcoming' => 'Upcoming',
	];



	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [
			'get_project_post' => [ $this, 'get_project_post' ],

		];
	}

	/**
	 * Get the label for the project address field;
	 *
	 * @return string HTML for the label on the address field within the project post type.
	 */
	private function get_project_posttype_additional_info_label_cb() {
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
	public static function create_posttype() {
		$icon_for_posttype   = 'dashicons-admin-multisite';
		$taxonomies_to_apply = [ 'expertise', 'signtype', 'services', 'location' ];
		$singular            = 'project';
		$plural              = $singular . 's';
		$labels              = [
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
			'description'           => 'Jones Sign ' . ucfirst( $plural ) . ' and Details',
			'labels'                => $labels,
			'supports'              => [ 'title', 'thumbnail', 'excerpt', 'post-formats', 'page-attributes' ],
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
			'rest_base'             => 'project',
			'rest_controller_class' => 'WP_REST_Client_Controller',
		];
		register_post_type( 'project', $args );
	} // Project post type is created.


	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since    1.0.0
	 */
	public static function additional_fields() {
		$after        = '<hr>';
		$prefix       = 'project_';
		$metabox_args = [
			'context'      => 'normal',
			'classes'      => $prefix . 'meta',
			'id'           => $prefix . 'metabox',
			'object_types' => [ 'project' ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Project Overview',
			'show_title'   => false,
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

		/**
		 * Location of the project - address plus latitude and longitude.
		 */
		$args = [
			'name'                => 'Project Info',
			'desc'                => 'Additional Information on this Project',
			'id'                  => 'projectInfo', // Name of the custom field type we setup.
			'type'                => 'projectinfo',
			'object_types'        => [ 'project' ], // Only show on project post types.
			'options'             => [],
			'label_cb'            => $this->get_project_address_field_label_cb(),
			'repeatable'          => false,
			'text'                => ['add_row_text' => 'Add another location' ],
			'show_names'          => false, // false removes the left cell of the table -- this is worth understanding.
			'after_row'           => '<hr>',

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
			'id'           => ' ',
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
	 * Set additional administrator columns based on postmeta fields that are added to the post type - columns will have no data just yet.
	 *
	 * @param array $columns Array of columns I have already within the quick edit screen. Existing options are 'cb', 'title', 'taxonomy-signtype', 'date'
	 * @return array The newly added columns plus the existing columns that I have within the quick edit screen.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 */
	public static function make_new_admin_columns( $columns ) {
		unset( $columns['date'] );
		unset( $columns['taxonomy-signtype'] );
		$new['cb']         = array_slice( $columns, 0, 1 );
		$new['id']         = 'ID';
		$new['job_number'] = 'Job #';
		return array_merge( $new, $columns );
	}

	/**
	 * Get data to include in the new admin columns for this post.
	 *
	 * @param string $column_name Name of the column;
	 * @param int $post_id ID of the post;
	 */
	public function manage_new_admin_columns( $column_name, $post_id ) {
		global $post_type;
		$project_info = get_post_meta( $post_id, 'project_information', true );
		switch ( $column_name ) {
			case 'id':
				$output = $post_id;
				break;
			case 'job_number':
				$jobnumber = $project_info['job_id'] ?? 0;
				$output = "<div id=\"job_number_$post_id\">$jobnumber</div>";
				break;
			default:
				$output = '';
		}

		echo $output;

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
	public static function render_projectinfo_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_values = [
			'tease'         => '',
			'job_id'        => '',
			'local_folder'  => '',
			'year_complete' => '',
			'streetaddress' => '',
			'city'          => '',
			'state'         => '',
			'zip'           => '',
			'latitude'      => '',
			'longitude'     =>  '',
		];
		$value      = wp_parse_args( $value, $new_values );
	?>

	<section class="project_data">
		<!-- tease line -->
		<div>
			<label for="<?= $field_type->_id( '_tease', false ); ?>">Tease</label>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[tease]' ),
					'id'    => $field_type->_id( '_tease' ),
					'value' => $value['tease'],
					'desc'  => '7-12 word sentence to get a person interested',
				]
			);
			?>
		</div><!-- /tease line -->
		<!-- job number -->
		<div>
			<label for="<?= $field_type->_id( '_job_id', false ); ?>">Job Number</label>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[job_id]' ),
					'id'    => $field_type->_id( '_job_id' ),
					'value' => $value['job_id'],
					'desc'  => '',
				]
			);
			?>
		</div><!-- /job number -->
		<!-- localfolder -->
		<div>
			<label for="<?= $field_type->_id( '_local_folder', false ); ?>">Local Folder</label>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[local_folder]' ),
					'id'    => $field_type->_id( '_local_folder' ),
					'value' => $value['local_folder'],
					'desc'  => 'Drive Containing Information on the job',
				]
			);
			?>
		</div><!-- /localfolder -->
		<!-- status -->

		<div class="radio_group">

		<label for="<?= $field_type->_id( '_status', false ); ?>">Job Status</label>
			<?= $field_type->input(
				[
					// 'name'    => $field_type->_name( '[status]'),
					'id'      => $field_type->_id( '_status' ),
					'value'   => $value['status'],
					'type'    => 'radio',
					'options_cb' => $this->get_job_completion_status_options(),
				]
			); ?>


			<label for="<?= $field_type->_id( '_year_complete', false ); ?>">Year</label>
			<?= $field_type->input(
				[
					'name' => $field_type->_name( '[year_complete]'),
					'id'   => $field_type->_id( '_year_complete' ),
					'value' => $value['year_complete'],
				]
			); ?>
		</div>
		<!-- /status -->
		<!-- streetaddress -->
		<div>
			<label for="<?= $field_type->_id( '_streetaddress', false ); ?>">Address</label>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[streetaddress]' ),
					'id'    => $field_type->_id( '_streetaddress' ),
					'value' => $value['streetaddress'],
					'desc'  => '',
				]
			);
			?>
		</div><!-- /streetaddress -->
		<div>
				<label for="<?= $field_type->_id( '_city' ); ?>'">City</label>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[city]' ),
						'id'    => $field_type->_id( '_city' ),
						'value' => $value['city'],
						'desc'  => '',
					]
				);
				?>
		</div><!-- /city -->

		<!-- state -->
		<div id="state">
			<label for="<?= $field_type->_id( '_state' ); ?>'">State</label>
			<?= $field_type->select(
				[
					'name'    => $field_type->_name( '[state]' ),
					'id'      => $field_type->_id( '_state' ),
					'options' => AdditionalFields::get_state_options( $value['state'] ),
					'desc'    => '',
				]
			);
			?>
		</div><!-- /state -->

		<!-- /zip -->
		<div>
			<label for="<?= $field_type->_id( '_zip' ); ?>'">Zip</label>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[zip]' ),
					'id'    => $field_type->_id( '_zip' ),
					'value' => $value['zip'],
					'desc'  => '',
				]
			);
			?>
		</div><!-- /zip -->

		<!-- coordinates -->
		<div data-fieldid="latitude">
			<label for="<?=$field_type->_id( '_latitude' ); ?>'">Latitude</label>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[latitude]' ),
					'id'    => $field_type->_id( '_latitude' ),
					'value' => $value['latitude'],
					'desc'  => '',
					'class' => 'double-barrel-daryl',
				]
			);
			?>
		</div><!-- /latitude -->
		<div data-fieldid="longitude">
			<label for="<?= $field_type->_id( '_longitude' ); ?>'">Longitude</label>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[longitude]' ),
					'id'    => $field_type->_id( '_longitude' ),
					'value' => $value['longitude'],
					'desc'  => '',
					'class' => 'double-barrel-daryl',
				]
			);
			?>
		</div><!-- /longitude -->


	</section><!-- end section.projectaddressfields -->
	<?php
	}//end render_address_field_callback()

	/**
	 * Enqueues javascript that will allow me to access project posts.
	 *
	 * This will be more of a frontend thing.
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





}//end class definition.
