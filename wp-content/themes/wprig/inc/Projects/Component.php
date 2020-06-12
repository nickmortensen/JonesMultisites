<?php
/**
 * WP_Rig\WP_Rig\Projects\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Projects;

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
 *
 * TOC
 * 1. get_slug()
 * 2. template_tags()
 * 3. initialize()
 * add_metabox_to_project()
 * display_project_metabox_output()
 * get_project_address_field_label_cb()
 * additional_fields()
 * make_new_admin_columns()
 * manage_new_admin_columns
 *
 *
 * get_project_info()
 * render_projectinfo_field_callback()
 * action_enqueue_projects_script()
 * enqueue_project_post_admin_javascript()
 * create_project_posttype()
 * get_project_tease()
 * get_project_id()
 * get_project_folder()
 * get_project_address()
 * get_structured_project_address()
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'project';
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
			'get_additional_project_info'    => [ $this, 'get_additional_project_info' ],
			'get_project_address'            => [ $this, 'get_project_address' ],
			'get_footer_project_address'     => [ $this, 'get_footer_project_address' ],
			'get_project_tease'              => [ $this, 'get_project_tease' ],
			'get_project_id'                 => [ $this, 'get_project_id' ],
			'get_structured_project_address' => [ $this, 'get_structured_project_address' ],
			'get_project_svg'                => [ $this, 'get_project_svg' ],
			'the_svg'                        => [ $this, 'the_svg' ],
			'get_project_detail'             => [ $this, 'get_project_detail' ],
			'get_project_slideshow'          => [ $this, 'get_project_slideshow' ],
			'get_all_projects'               => [ $this, 'get_all_projects' ],
		];
	}
	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'cmb2_render_projectlocation', [ $this, 'render_projectlocation_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_partner', [ $this, 'render_partner_field_callback' ], 10, 5 );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		add_action( 'init', [ $this, 'create_posttype' ], 11 );
		// CMB2 field specifically for a project address.
		// Enqueue a frontend script to utilize for project post types.
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_projects_script' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_metabox_to_project' ], 10, 2 );
		add_action( 'quick_edit_custom_box', [ $this, 'display_quick_edit_custom' ], 10, 2 );
		// Add empty columns for the admin edit screens.
		add_action( 'manage_project_posts_columns', [ $this, 'make_new_admin_columns' ], 10, 1 );
		// Add data to the new admin columns.
		add_action( 'manage_project_posts_custom_column', [ $this, 'manage_new_admin_columns' ], 10, 2 );
		add_action( 'save_post', [ $this, 'save_post' ], 10, 1 ); // call on save, to update metainfo attached to our metabox.
		// Load the javascript admin script for prepopulating project quickedit fields on the admin end.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts_and_styles' ] );
	}


	/**
	 * Get all the published projects.
	 */
	public function get_all_projects() {

		$args = [
			'post_type'   => 'project',
			'post_status' => 'publish',
		];

		return new \WP_QUERY( $args );
	}

	/**
	 * Add a new metabox on the post type's edit screen. Shows up on the side.
	 *
	 * @param string $post_type The Type of post - in our case.
	 * @param int    $post The dentifier of the post - the number.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Types
	 * @link https://ducdoan.com/add-custom-field-to-quick-edit-screen-in-wordpress/
	 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
	 */
	public function add_metabox_to_project( $post_type, $post ) {
		$id       = 'projectinfo-short-details';
		$title    = 'Project Information';
		$callback = [ $this, 'display_project_metabox_output' ];
		$screen   = $this->get_slug();
		$context  = 'side';
		$priority = 'high';
		add_meta_box( $id, $title, $callback, $screen, $context, $priority );
	}

	/**
	 * Displays additional fields within the project post type, populating as needed.
	 *
	 * @param int $post The post ID.
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_project_metabox_output( $post ) {
		$html = '';
		wp_nonce_field( 'post_metadata', 'project_metadata_field' );
		$project_info = $this->get_additional_project_info( $post->ID );
		$job_id       = $project_info['job_id'] ?? '';
		$client       = $project_info['client'] ?? '';
		$local_folder = $project_info['local_folder'] ?? '';
		$complete     = $project_info['year_complete'] ?? '2020';
		$tease        = $project_info['tease'] ?? '7 word teaser';

		$html .= '<style> #project-side-metadata { background: var(--indigo-200); display: flex; flex-flow: column nowrap; } #project-side-metadata > div { padding-top: 10px; display: flex; flex-flow: column nowrap; justify-content: flex-start; } </style>';

		$html .= '<div id="project-side-metadata" class="inline-edit-group wp-clearfix">';

		$html .= '<div>';
		$html .= sprintf( '<label for="projectInfo[client]">%s</label>', 'Client' );
		$html .= sprintf( '<input type="text" class="regular_text" name="projectInfo[client]" id="projectInfo[client]" value="%s"/>', $client );
		$html .= '</div>';
		$html .= '<div>';
		$html .= sprintf( '<label for="projectInfo[job_id]">%s</label>', 'Job Number' );
		$html .= sprintf( '<input type="text" class="regular_text" name="projectInfo[job_id]" id="projectInfo[job_id]" value="%s"/>', $job_id );
		$html .= '</div>';
		$html .= '<div>';
		$html .= sprintf( '<label for="projectInfo[year_complete]">%s</label>', 'Year Complete' );
		$html .= sprintf( '<input type="text" class="text_small" name="projectInfo[year_complete]" id="projectInfo[year_complete]" value="%s"/>', $complete );
		$html .= '</div>';
		$html .= '<div>';
		$html .= sprintf( '<label for="projectInfo[tease]">%s</label>', 'Tease' );
		$html .= sprintf( '<input type="text" class="text_small" name="projectInfo[tease]" id="projectInfo[tease]" value="%s"/>', $tease );
		$html .= '</div>';
		$html .= '<div>';
		$html .= sprintf( '<label for="projectInfo[local_folder]">%s</label>', 'Local Folder' );
		$html .= sprintf( '<input type="text" class="text_small" name="projectInfo[local_folder]" id="projectInfo[local_folder]" value="%s"/>', $local_folder );
		$html .= '</div>';

		$html .= '</div> <!-- end div.inline-edit-group wp-clearfix-->';
		echo $html;
	}

	/**
	 * Saving meta info (used for both traditional and quick-edit saves)
	 *
	 * @param int $post_id The id of the post.
	 */
	public function save_post( $post_id ) {

		$post_type = get_post_type( $post_id );

		if ( 'project' === $post_type ) {
			// wp_nonce_field( 'post_metadata', 'project_metadata_field' );
			// check nonce set.
			if ( ! isset( $_POST['project_metadata_field'] ) ) return false;

			// verify nonce.
			if ( ! wp_verify_nonce( $_POST['project_metadata_field'], 'post_metadata' ) ) return false;

			// If not autosaving.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

			$project_info = $_POST['projectInfo'];

			$client        = isset ( $project_info['client'] ) ? sanitize_text_field( $project_info['client'] ) : '';
			$job_id        = isset ( $project_info['job_id'] ) ? sanitize_text_field( $project_info['job_id'] ) : '';
			$local_folder  = isset ( $project_info['local_folder'] ) ? wp_kses_normalize_entities( $project_info['local_folder'] ) : '';
			$year_complete = isset ( $project_info['year_complete'] ) ? sanitize_text_field( $project_info['year_complete'] ) : '';
			$tease         = isset ( $project_info['tease'] ) ? $project_info['tease'] : '';

			$newdata = [
				'client'        => $client,
				'job_id'        => $job_id,
				'local_folder'  => $local_folder,
				'year_complete' => $year_complete,
				'tease'         => $tease,
			];

			// This field is saved as serialized data, so I need to use wp_parse_args to get to it.
			update_post_meta( $post_id, 'projectInfo', wp_parse_args( $newdata, get_post_meta( $post_id, 'projectInfo', true ) ) );
		}

	}//end save_post()

	/**
	 * Displays our custom content on the quick-edit interface, no values can be pre-populated (all done in JS)
	 *
	 * @param string $column Name of column.
	 * @param string $post_type Name of post type.
	 *
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_quick_edit_custom( $column, $post_type ) {
		$html = '';
		wp_nonce_field( 'post_metadata', 'project_metadata_field' );
		switch ( $column ) {
			case 'year_complete':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[year_complete]">';
				$html .= '<span class="title">Completion</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[year_complete]" class="ptitle" id="year_complete"/><span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'client':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[client]">';
				$html .= '<span class="title">Client</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[client]" class="ptitle" id="client"/><span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'local_folder':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[local_folder]">';
				$html .= '<span class="title">Directory</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[local_folder]" class="ptitle" id="local_folder"/></span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'job_id':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="projectInfo[job_id]">';
				$html .= '<span class="title">Job ID</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="projectInfo[job_id]" class="ptitle" id="job_id"/></span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'tease':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[tease]">';
				$html .= '<span class="title">Tease</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[tease]" class="ptitle" id="tease"/></span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			default:
				$html .= '';
		} // End switch.
		echo $html;
	}

	/**
	 * Get the label for the project address field;
	 *
	 * @return string HTML for the label on the address field within the project post type.
	 */
	private function get_project_address_field_label_cb() {
		return '<span class="indigo" style="font-size:2.5rem;">Project Location Data </span><hr>';
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
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since    1.0.0
	 */
	public function additional_fields() {
		$metabox_args = [
			'context'      => 'normal',
			'id'           => 'project-information-metabox',
			'object_types' => [ $this->get_slug() ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Project Overview',
			'show_title'   => false,
			'cmb_styles'   => false,
		];
		$metabox = new_cmb2_box( $metabox_args );

		/**
		 * Get the label for the project address field;
		 */
		function get_label_cb() {
			$html  = '<style>.label_callback {color:white; font-weight: 600;background: var(--indigo-600);font-size: 2.5rem; padding-left: 1ch; margin-bottom: 1ch;}</style>';
			$html .= '<div class="label_callback">Project Information</div>';
			return $html;
		}

		/**
		 * Get general label for the project address field;
		 */
		function get_general_label_cb( $text ) {
			return '<div class="label_callback">' . ucwords( $text ) . '</div>';
		}

		/**
		 * Location of the project - address plus latitude and longitude.
		 */
		$args = [
			'name'       => 'Project',
			'id'         => 'projectLocation', // Name of the custom field type we setup.
			'type'       => 'projectlocation',
			'label_cb'   => get_label_cb(),
			'show_names' => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'    => [ 'project_fields' ],
			'after_row'  => '<hr>',
			'priority'   => 'high',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectAltName: Returns string.
		 * The alternative name for this project.
		 */
		$args = [
			'classes'        => ['input-full-width'],
			'name'         => 'Alt',
			'desc'         => 'Is there an alternate name or client for this project?',
			'default'      => '',
			'id'           => 'projectAltName',
			'type'         => 'text',
		];
		$metabox->add_field( $args );

		/**
		 * Group field for General Contractors and partners.
		 */
		$args = [
			'id'          => 'projectPartners',
			'type'        => 'group',
			'description' => 'Partners',
			'repeatable'  => true,
			'show_names'  => false,
			'button_side' => 'right',
			'options'     => [
				'group_title'       => 'Partner {#}', // since version 1.1.4, {#} gets replaced by row number
				'add_button'        => 'Add Partner',
				'remove_button'     => 'Remove Partner',
				'sortable'          => true,
				'closed'            => true, // true to have the groups closed by default.
				'remove_confirm'    => 'Are you sure you want to remove?', // Performs confirmation before removing group.
			],
		];

		$partners = $metabox->add_field( $args );
		/**
		 * ID projectGC: Returns string.
		 * The general contractor.
		 */
		$args = [
			'id'           => 'entry',
			'class'        => 'input-full-width',
			'name'         => 'Partner',
			'show_names'   => false,
			'desc'         => '',
			'default'      => '',
			'type'         => 'partner',
		];
		$metabox->add_group_field( $partners, $args );

		/**
		 * SVG Image or Logo of the Client
		 */
		$args = [
			'id'   => 'projectSVGLogo',
			'name' => 'SVG Logo',
			'type' => 'file',
			'text' => [
				'add_upload_file_text' => 'add svg logo',
			],
			'query_args' => [
				'type' => 'image/svg',
			],
		];
		$metabox->add_field( $args );
		/**
		 * Images 4x3 for slideshow.
		 */
		$args = [
			'show_names'   => false,
			'classes'      => [ 'make-button-centered' ],
			'before'       => get_general_label_cb( 'rectangular images' ),
			'id'           => 'projectImagesSlideshow',
			'name'         => 'slideshow',
			'type'         => 'file_list',
			'preview_size' => [ 200, 150 ],
			'query_args'   => [
				'type' => 'image',
				// figure out a way you only get images that have a size ration of 4x3
			],
			'text'         => [
				'add_upload_files_text' => 'add images (4x3)',
				'remove_image_text'     => 'remove',
				'file_text'             => 'image',
				'file_download_text'    => 'download',
				'remove_text'           => 'standard',
			],
		];
		$metabox->add_field( $args );
		/**
		 * Vertical 3x4 image for tablet display;
		 */
		$args = [
			'show_names'   => false,
			'classes'      => [ 'make-button-centered' ],
			'before'       => get_general_label_cb( 'vertical image ' ),
			'id'           => 'projectVerticalImage',
			'name'         => 'Vertical Img',
			'button_side'  => 'right',
			'type'         => 'file',
			'preview_size' => [ 150, 200 ],
			'query_args'   => [
				'type' => 'image',
				// figure out a way you only get images that have a size ration of 4x3
			],
			'text'         => [
				'add_upload_file_text' => 'add vertical image',
				'remove_image_text'     => 'remove',
				'file_text'             => 'image',
				'file_download_text'    => 'download',
				'remove_text'           => 'standard',
			],
		];
		$metabox->add_field( $args );

		/**
		 * Square 1x1 image for use within the open graph tags.
		 */
		$args = [
			'show_names'   => false,
			'classes'      => [ 'make-button-centered' ],
			'before'       => get_general_label_cb( 'Square Images' ),
			'id'           => 'projectSquareImages',
			'name'         => 'Square Images',
			'desc'         => 'needs at least one',
			'button_side'  => 'right',
			'type'         => 'file_list',
			'preview_size' => [ 150, 150 ],
			'query_args'   => [
				'type' => 'image',
				// figure out a way you only get images that have a size ration of 4x3
			],
			'text'         => [
				'add_upload_files_text' => 'add image(s)',
				'remove_image_text'     => 'remove',
				'file_text'             => 'image',
				'file_download_text'    => 'download',
				'remove_text'           => 'standard',
			],
		];
		$metabox->add_field( $args );

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
		global $post_type;
		$adminurl = admin_url( '/wp-admin/edit.php?post_type=' . $post_type . '&orderby=identifier&order=asc' );
		unset( $columns['taxonomy-location'] );
		unset( $columns['date'] );
		$new = [
			'cb'       => array_slice( $columns, 0, 1 ),
			'id'       => 'ID',
			'job_id'   => 'Job #',
			'client'   => 'Client',
			'tease'    => 'Tease',
			'title'    => array_slice( $columns, 0, 1 ),
		];
		$new_columns['year_complete'] = '<span title="Year Completed?" class="material-icons">calendar_today</span>';
		$new_columns['local_folder']  = '<span title="Local Folder" class="material-icons">apps</span>';

		return array_merge( $new, $columns, $new_columns );
	}

	/**
	 * Get data to include in the new admin columns for this post.
	 *
	 * @param string $column_name Name of the column;
	 * @param int $post_id ID of the post;
	 */
	public function manage_new_admin_columns( $column_name, $post_id ) {
		global $post_type;
		$html         = '';
		$project_info = $this->get_additional_project_info( $post_id );
		$job_id       = $project_info['job_id'] ?? '';
		$client       = $project_info['client'] ?? '';
		$local_folder = $project_info['local_folder'] ?? '';
		$complete     = $project_info['year_complete'] ?? '2016';
		$tease        = $project_info['tease'] ?? '';


		switch ( $column_name ) {
			case 'id':
				$html .= $post_id;
				break;
			case 'job_id':
				$id = 'job_id_' . $post_id;
				$html .= sprintf( '<div id="%s"><strong>%s</strong></div>', $id, $job_id );
				break;
			case 'client':
				$id = 'client_' . $post_id;
				$html .= sprintf( '<div id="%s">%s</div>', $id, $client );
				break;
			case 'tease':
				$id = 'tease_' . $post_id;
				$html .= sprintf( '<div data-tease="%s" id="%s">%s</div>', $tease, $id, wp_trim_words( $tease, 2 ) );
				break;
			case 'year_complete':
				$id = 'year_complete_' . $post_id;
				$html .= sprintf( '<div id="%s">%s</div>', $id, $complete );
				break;
			case 'local_folder':

				$color      = 'var(--gray-500)';
				$icon       = 'image_not_suppported';
				if ( has_post_thumbnail( $post_id ) ) {
					$color = 'var(--green-500)';
					$icon  = 'image';
				}
				$has_img    = sprintf( '<span style="color:%s" class="material-icons">%s</span>', $color, $icon );
				$id         = 'local_folder_' . $post_id;
				$icon_color = '' !== $local_folder ? 'var(--green-500)' : 'var(--gray-500)';
				$icon_text  = '' !== $local_folder ? 'work' : 'work_off';
				$icon       = sprintf( '<span style="color:%s" class="material-icons">%s</span>', $icon_color, $icon_text );
				$html .= sprintf( '<div id="%s" data-folder="%s">%s%s</div>', $id, $local_folder, $icon, $has_img );
				break;
			default:
				$html .= '';
		}
		echo $html;
		// print_r( $project_info );
	}

	/**
	 * Retrieve the postmeta for the year this project completed, started, or is expected to complete.
	 *
	 * @param int $post_id Project post type id.
	 * @return string 4 digit year cmb_styles that the post either completes, was started, or begins.
	 */
	public function get_project_slideshow( $post_id ) {
		return get_post_meta( $post_id, 'projectImagesSlideshow', true ); // false is default, true if I want only the first value within the array.
	}

	/**
	 * Retrieve the postmeta for the year this project completed, started, or is expected to complete.
	 *
	 * @param int $post_id Project post type id.
	 * @return string 4 digit year that the post either completes, was started, or begins.
	 */
	public function get_project_address( $post_id ) {
		return get_post_meta( $post_id, 'projectLocation', true ); // false is default, true if I want only the first value within the array.
	}

	/**
	 * Retrieve the postmeta for the year this project completed, started, or is expected to complete.
	 *
	 * @param int $post_id Project post type id.
	 * @return string 4 digit year that the post either completes, was started, or begins.
	 */
	public function get_additional_project_info( $post_id ) {
		return get_post_meta( $post_id, 'projectInfo', true ); // false is default, true if I want only the first value within the array.
	}

	/**
	 * Retrieve the postmeta from the projectInfo postmeta array - by field.
	 *
	 * @param int $post_id Project post type id.
	 * @param string $field which field from projectInfo - choose among job_id|tease|client|year_complete|local_folder.
	 * @return string 4 digit year that the post either completes, was started, or begins.
	 */
	public function get_project_detail( $post_id, $field = 'all' ) {
		$info = $this->get_additional_project_info( $post_id );
		if ( 'all' === $field ) {
			return $info;
		} else {
			return $info[$field];
		}
	}

	/**
	 * Enqueues javascript that will allow me to access project posts.
	 */
	public function action_enqueue_projects_script() {

		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		wp_register_script( 'jQuery', 'https://code.jquery.com/jquery-3.5.1.slim.min.js' );
		// Enqueue the navigation script. The last element asks whether to load the script within the footer. We don't want that.
		wp_enqueue_script(
			'flickity',
			get_theme_file_uri( '/assets/js/flickity.min.js' ),
			['jQuery'],
			wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/flickity.min.js' ) ),
			false
		);
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
	public function enqueue_admin_scripts_and_styles() {
		global $post_type;
		// Ensure that we don't bother using this javascript unless we are in the project post type on the admin end.
		if ( is_admin() && 'project' === $post_type ) :
		// Within the development environment, use the non-minified version.
		$script_uri = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/project_quickedit.js' ) : get_theme_file_uri( '/assets/js/project_quickedit.min.js' );
		$dependencies = [ 'jquery', 'inline-edit-post' ]; // location: wp-admin/js/inline-edit-post.js.
		$version    = '19';
		$footer     = true;
		wp_enqueue_script( 'project-quickedit', $script_uri, $dependencies, $version, $footer );
		endif;
	}

	/**
	 * Creates the custom post type: 'Project'.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/
	 */
	public function create_posttype() {
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
			'supports'              => [ 'title', 'thumbnail', 'excerpt', 'editor' ],
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
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		];
		register_post_type( 'project', $args );
	}

	/**
	 * Get the svg logo from the project.
	 *
	 * @param int $post_id Identifier of the post.
	 * @param string $type Should output the image id or the image url - default is url.
	 * @return string HTML for the label on the address field within the project post type.
	 */
	public function get_project_svg( $post_id, $type = 'url' ) {
		$metafield = 'projectSVGLogo';
		if ( 'id' === $type ) {
			$metafield .= '_id';
		}
		return get_post_meta( $post_id, $metafield, true );
	}

	/**
	 * Output the svg logo from the project.
	 *
	 * @param int $post_id Identifier of the post.
	 * @param string $type Should output the image id or the image url - default is url.
	 * @return string HTML for the label on the address field within the project post type.
	 */
	public function the_svg( $url, $echo = true, $width = 30 ) {
		$max = (int) $width + 5;
		$output = "<img class=\"svg_logo\" src=\"$url\"/ style=\"min-width: {$width}vmin;max-width: {$max}vw;\">";
		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}

	}

	/**
	 * Get the tease field from project info.
	 *
	 * @param int $post_id Identifier of the post.
	 * @return string HTML for the label on the address field within the project post type.
	 */
	public function get_project_tease( $post_id ) {
		$info  = $this->get_additional_project_info( $post_id );
		$tease = $info['tease'];
		return $tease;
	}

	/**
	 * Get the job_id field from project info.
	 *
	 * @param int $post_id Identifier of the post.
	 * @return string HTML for the label on the address field within the project post type.
	 */
	public function get_project_id( $post_id ) {
		$info  = $this->get_additional_project_info( $post_id );
		$output = $info['job_id'];
		return $output;
	}
	/**
	 * Get the local folder on the jones sign company drives with more information about the project.
	 *
	 * @param int $post_id Identifier of the post.
	 * @return string HTML for the label on the address field within the project post type.
	 */
	public function get_project_folder( $post_id ) {
		$info  = $this->get_additional_project_info( $post_id );
		$output = $info['local_folder'];
		return $output;
	}
	/**
	 * Get the address of the project.
	 *
	 * @param int $post_id Identifier of the post.
	 * @return string HTML for the label on the address field within the project post type.
	 */
	public function get_footer_project_address( $post_id ) : array {
		$info                    = $this->get_project_address( $post_id );
		$output['title']         = get_the_title( $post_id );
		$output['address']       = $info['address'];
		$output['city']          = $info['city'];
		$output['state']         = $info['state'];
		$output['zip']           = $info['zip'];
		$output['latitude']      = $info['latitude'];
		$output['longitude']     = $info['longitude'];
		return $output;
	}

	/**
	 * Output the address of the project using structured data for better recognition by search engines.
	 *
	 * @param int $post_id Identifier of the post.
	 * @return string HTML for the label on the address field within the project post type.
	 */
	public function get_structured_project_address( $post_id ) {
		$info        = $this->get_project_address( $post_id );
		// $info        = $this->get_footer_project_address( $post_id );
		$structured  = '<div itemscope itemtype="http://schema.org/PostalAddress">';
		$structured .= sprintf( '<span itemprop="name">%s</span><br>', get_the_title( $post_id ) );
		$structured .= sprintf( '<span itemprop="streetAddress">%s</span><br>', $info['address'] );
		$structured .= sprintf( '<span itemprop="addressLocality">%s</span>,', $info['city'] );
		$structured .= sprintf( '<span itemprop="addressRegion">%s</span>', $info['state'] );
		$structured .= sprintf( '<span itemprop="postalCode">%s</span><br>', $info['zip'] );
		$structured .= '<span itemprop="addressCountry">United States</span>';
		$structured .= '</div>';

		return $structured;
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
	public function render_projectlocation_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_values = [
			'address'   => '',
			'url'       => '',
			'city'      => '',
			'state'     => '',
			'zip'       => '',
			'latitude'  => '',
			'longitude' => '',
			'alternate' => '',
		];
		$value = wp_parse_args( $value, $new_values );
		?>
		<div class="alignleft">
			<p> <label for="<?php echo $field_type->_id( '_address' ); ?>">Address</label> </p>
			<?php echo $field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[address]' ),
				'id'    => $field_type->_id( '_address' ),
				'value' => $value['address'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p> <label for="<?php echo $field_type->_id( '_url' ); ?>">Website</label> </p>
			<?php echo $field_type->input( [
				'name'  => $field_type->_name( '[url]' ),
				'id'    => $field_type->_id( '_url' ),
				'value' => $value['url'],
				'desc'  => '',
			] ); ?>
		</div>
		<br class="clear">
		<div class="alignleft">
			<p>
				<label for="<?php echo $field_type->_id( '_city' ); ?>'">City</label>
			</p>
			<?php echo $field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[city]' ),
				'id'    => $field_type->_id( '_city' ),
				'value' => $value['city'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p>
				<label for="<?php echo $field_type->_id( '_state' ); ?>'">State</label>
			</p>
			<?php echo $field_type->select( [
				'name'    => $field_type->_name( '[state]' ),
				'id'      => $field_type->_id( '_state' ),
				'options' => AdditionalFields::get_state_options( $value['state'] ),
				'desc'    => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p>
				<label for="<?php echo $field_type->_id( '_zip' ); ?>'">Zip</label>
			</p>
			<?php echo $field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[zip]' ),
				'id'    => $field_type->_id( '_zip' ),
				'value' => $value['zip'],
				'type'  => 'number',
				'desc'  => '',
				] ); ?>
		</div>
		<br class="clear">
		<div class="alignleft">
			<p>
				<label for="<?php echo $field_type->_id( '_latitude' ); ?>'">Latitude</label>
			</p>
			<?php echo $field_type->input( [
						'class' => 'cmb_text_small',
						'name'  => $field_type->_name( '[latitude]' ),
						'id'    => $field_type->_id( '_latitude' ),
						'value' => $value['latitude'],
						'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p><label for="<?php echo $field_type->_id( '_longitude' ); ?>'">Longitude</label></p>
			<?php echo $field_type->input( array(
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[longitude]' ),
				'id'    => $field_type->_id( '_longitude' ),
				'value' => $value['longitude'],
				'desc'  => '',
			) ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p><label for="<?php echo $field_type->_id( '_alternate' ); ?>'">Project Alt Name</label></p>
			<?php echo $field_type->input( array(
				'class' => 'cmb_text_medium',
				'name'  => $field_type->_name( '[alternate]' ),
				'id'    => $field_type->_id( '_alternate' ),
				'value' => $value['alternate'],
				'desc'  => '',
			) ); ?>
		</div>
		<br class="clear">
	<?= $field_type->_desc( true );
	}//end render_address_field_callback()

	/**
	 * Create & Render Partner Information Fields
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function render_partner_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_values = [
			'partner' => '',
			'type'    => '',
			'url'     => '',
		];
		$value = wp_parse_args( $value, $new_values );
		?>
		<div class="alignleft">
			<p> <label for="<?php echo $field_type->_id( '_partner' ); ?>">Partner</label> </p>
			<?php echo $field_type->input( [
				'class' => 'cmb_text_medium',
				'name'  => $field_type->_name( '[partner]' ),
				'id'    => $field_type->_id( '_partner' ),
				'value' => $value['partner'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="margin-left: 14px;">
			<p> <label for="<?php echo $field_type->_id( '_type' ); ?>'">Type</label> </p>
			<?php echo $field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[type]' ),
				'id'    => $field_type->_id( '_type' ),
				'value' => $value['type'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="margin-left: 14px;">
			<p> <label for="<?php echo $field_type->_id( '_url' ); ?>'">URL</label> </p>
			<?php echo $field_type->input( [
						'class'  => 'cmb_text_small',
						'name'  => $field_type->_name( '[url]' ),
						'id'    => $field_type->_id( '_url' ),
						'value' => $value['url'],
						'desc'  => '',
			] ); ?>
		</div>
		<br class="clear">
	<?= $field_type->_desc( true );
	}//end render_address_field_callback()


}//end class definition.
