<?php
/**
 * WP_Rig\WP_Rig\Clientele\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Clientele;

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
class Component implements Component_Interface, Templating_Component_Interface {


	/**
	 * The plural of this posttype.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name The slug of this posttype..
	 */
	private $plural_name = 'clientele';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'client';
	}


	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'init', [ $this, 'create_posttype' ] );
		add_filter( 'cmb2_render_clientele', [ $this, 'render_clientele_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_testimonial', [ $this, 'render_testimonial_field_callback' ], 10, 5 );
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		add_filter( 'cmb2_sanitize_testimonial', [ $this, 'sanitize_testimonial_field' ], 10, 5 );
		add_filter( 'cmb2_types_esc_testimonial', [ $this, 'types_esc_testimonial_field' ], 10, 4 );
		// Add empty columns for the admin edit screens.
		add_action( 'manage_client_posts_columns', [ $this, 'make_new_admin_columns' ], 10, 1 );
		// Add data to the new admin columns.
		add_action( 'manage_client_posts_custom_column', [ $this, 'manage_new_admin_columns' ], 10, 2 );
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
			'get_client_info' => [ $this, 'get_client_info' ],
		];
	}

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
	 * Creates the custom post type: 'client'.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_post_type/
	 */
	public function create_posttype() {
		$icon_for_posttype   = 'dashicons-admin-multisite';
		$taxonomies_to_apply = [ 'expertise', 'signtype', 'services' ];
		$singular            = $this->get_slug();
		$plural              = 'clientele';
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
		$rewrite             = [
			'slug'       => $singular,
			'with_front' => true,
			'pages'      => true,
			'feeds'      => true,
		];
		$args                = [
			'label'                 => ucfirst( $singular ),
			'description'           => ucfirst( $plural ) . ' and Details',
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
			'rest_base'             => $singular,
			'rest_controller_class' => 'WP_REST_Client_Controller',
		];
		register_post_type( 'client', $args );
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
			'id'           => 'client-information-metabox',
			'object_types' => [ $this->get_slug() ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Client Overview',
			'show_title'   => false,
			'cmb_styles'   => false,
		];
		$metabox = new_cmb2_box( $metabox_args );

		/**
		 * Get the label for the project address field;
		 */
		function get_label_cb( $field ) {
			return '<div style="color:white; font-weight: 600;background: var(--indigo-600);font-size: 2.5rem;">' . ucfirst( $field ) . ' Information</div>';
		}

		/* Basic information about the client */
		$args = [
			'name'       => 'Client',
			'id'         => 'clientInformation', // Name of the custom field type we setup.
			'type'       => 'clientele',
			'label_cb'   => get_label_cb( 'client' ),
			'show_names' => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'    => [ 'clientele_fields' ],
		];
		$metabox->add_field( $args );

		/* Company logo */
		$args = [
			'id' => 'clientInformationLogo',
			'name' => 'logo',
			'type' => 'file',
			'options' => [
				'url' => false,
			],
			'text' => [
				'add_upload_file_text' => 'add svg',
			],
			'query_args' => [
				'type' => 'image/svg',
			],
			'preview_size' => 'thumbnail',
		];
		$metabox->add_field( $args );

	function get_testimonial_defaults() {
		$defaults =	[
			'name'        => '',
			'testimonial' => '',
			'position'    => '',
			'linkedin'    => '',
		];
		return $defaults;
	}
		/* Testimonial From the client */
		$args = [
			'name'           => 'Testimonial',
			'id'             => 'clientTestimonial', // Name of the custom field type we setup.
			'type'           => 'testimonial',
			'label_cb'       => get_label_cb( 'testimonial' ),
			'show_names'     => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'        => [ 'testimonial_fields' ],
			'after_row'      => '<hr>',
			'default_cb'     => 'get_testimonial_defaults',
			'repeatable'     => true,
			'escape_cb'      => 'types_esc_testimonial_field',
			'santization_cb' => 'sanitize_testimonial_field',
			'button_side'    => 'right',
			'text'           => [
				'add_row_text'    => 'Add Testimonial',
				'remove_row_text' => 'Remove Testimonial',
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
	 * Clean a web address field to ensure it is always "https" in the event the scheme is left off or set to "http".
	 *
	 * @return string $input The url;
	 */
	public function format_url( $input ) : string {
		$url = '';
		if ( 4 === strpos( $input, ':' ) ) {
			$url = preg_replace( '/http:\\/\\//', 'https://', $input );
		}
		if ( ! isset( wp_parse_url( $input )['scheme'] ) ) {
			$url = "https://$input";
		}
		return $url;
	}

	/**
	 * Get data to include in the new admin columns for this post.
	 *
	 * @param string $column_name Name of the column;
	 * @param int $post_id ID of the post;
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
				} else if ( $logo_url ) {
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
					$html = '';
					$testimonial = get_post_meta( $post_id, 'clientTestimonial', false ); // false means entire array; True means just the first one;
					$name        = $testimonial['name'] ?? '';
					$test        = $testimonial['testimonial'] ?? '';
					$linked      = $testimonial['linkedin'] ?? '';
					if ( '' !== $testimonial ) {
						$html .= '<span id="has-testimonial" style="font-size: 1.9rem;color: var(--green-500);" class="material-icons" title="' . $test . '"> add_comment </span>';
					}
					 else {
						$html .= '<span style="font-size: 1.9rem;color: var(--gray-500);" class="material-icons" title=""> rate_review </span>';
					}
					if ( '' !== $name ) {
						$html .= '<span id="has-name" style="font-size: 1.9rem;color: var(--green-500);" class="material-icons" title="has name" alt="' . $name . '"> how_to_reg </span>';
					} else {
						$html .= '<span id="has-name" style="font-size: 1.9rem;color: var(--gray-500);" class="material-icons" title="has name" alt="' . $name . '"> how_to_reg </span>';
					}
					if ( '' !== $linked ) {
						$linked = $this->format_url( $linked );
						$html .= '<span id="has-linkedin" style="transform: rotate(90deg);font-size: 1.9rem;color: var(--green-500);" class="material-icons" title="has linkedin link" alt="' . $linked . '"> link </span>';
					} else {
						$html .= '<span id="has-linkedin" style="transform: rotate(90deg);font-size: 1.9rem;color: var(--gray-500);" class="material-icons" title="has linkedin link" alt="' . $linked . '"> link </span>';
					}

					// print_r( $testimonial );
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
		$key = 'client[since]';
		$single = true; //If true, returns only the first value for the specified meta key. This parameter has no effect if $key is not specified.
		return get_post_meta( $id, $key, $single );
	}

	/**
	 * Create & Render a Client Information Field for CMB2.
	 *
	 * @param array  $field       The passed in `CMB2_Field` .
	 * @param mixed  $value       The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id   The ID of the current object.
	 * @param string $object_type The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type  The `CMB2_Types` object.
	 */
	public function render_clientele_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_value = [
			'company' => '',
			'website' => '',
			'since'   => '',
		];

		$value = wp_parse_args( $value, $new_value );
		$html  = '';
		$html .= '<style>
		section.custom_field_section {
			min-height: 260px;
			display: flex;
			flex-flow: column nowrap;
			justify-content: space-around;
			align-items: stretch;
		}
		.admin-additional-field {
			padding-bottom: 1.6rem;
			display: flex;
			flex-flow: row nowrap;
			justify-content: flex-start;
			align-items: space-around;
		}
		.admin-additional-field > input {
			position: absolute;
			left: 20%;
		}
		</style>';

		$html .= '<section class="custom_field_section">';

		$field = 'Company';
		$label = $field_type->_id( '_company' );
		$name  = $field_type->_name( '[company]' );
		$val   = $value['company'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];

		$html .= '<div class="admin-additional-field">';
		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';

		$field = 'Website';
		$label = $field_type->_id( '_website' );
		$name  = $field_type->_name( '[website]' );
		$val   = $value['website'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html .= '<div class="admin-additional-field">';
		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';

		$field = 'Since';
		$label = $field_type->_id( '_since');
		$name  = $field_type->_name( '[since]' );
		$val   = $value['since'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html .= '<div class="admin-additional-field">';
		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';

		$html .= '</section>';
		echo $html;
	}//end render_clientele_field_callback()

	/**
	 * Create & Render a Testimonial Field for CMB2.
	 *
	 * @param array  $field       The passed in `CMB2_Field` .
	 * @param mixed  $value       The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id   The ID of the current object.
	 * @param string $object_type The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type  The `CMB2_Types` object.
	 */
	public function render_testimonial_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$html  = '';
		$new_value = [
			'name'        => '',
			'testimonial' => '',
			'position'    => '',
			'linkedin'    => '',
		];
		$value = wp_parse_args( $value, $new_value );

		$field = 'Name';
		$label = $field_type->_id( '_name ' );
		$name  = $field_type->_name( '[name]' );
		$val   = $value['name'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html .= '<section class="custom_field_section">';
		// $html .= '<div class="alignleft" style="padding-left: 12px;">';
		$html .= '<div class="admin-additional-field">';

		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';

		$field = 'Position';
		$label = $field_type->_id( '_position');
		$name  = $field_type->_name( '[position]' );
		$val   = $value['position'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html .= '<div class="admin-additional-field">';
		// $html .= '<div class="alignleft" style="padding-left: 12px;">';
		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';

		$field = 'Testimonial';
		$label = $field_type->_id( '_testimonial');
		$name  = $field_type->_name( '[testimonial]' );
		$val   = $value['testimonial'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html .= '<div class="admin-additional-field">';
		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';



		$field = 'Linkedin';
		$label = $field_type->_id( '_linkedin');
		$name  = $field_type->_name( '[linkedin]' );
		$val   = $value['linkedin'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html .= '<div class="admin-additional-field">';
		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';
		$html .= '</section>';

		echo $html;
	}//end render_testimonial_field_callback()

	/**
	 * Santize the fields for the testimonial.
	 *
	 * @param [type] $check
	 * @param array $meta_value The values for the fields (person, title, testimonial, linkedin),
	 * @param [type] $object_id
	 * @param array $field_args The arguments for the testimonial field when adding to the metabox.
	 * @param [type] $sanitize_object
	 *
	 *
	 */
	public function sanitize_testimonial_field( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {
		if ( $field_args['repeatable'] && is_array( $meta_value ) ) {
			foreach ( $meta_value as $key => $val ) {
				$val = array_filter( $val );
				if ( empty( $val ) ) {
					unset( $meta_value[ $key ] );
					continue;
				}
				$meta_value[ $key ] = array_map( 'sanitize_text_field', $val );
			}

			return $meta_value;
		}
		return $check;
	}


	public function dump_escape() {
		$html = $this->types_esc_testimonial_field();
	}

	/**
	 * Escape the inputs of the testimonial field.
	 *
	 * @param [type] $check
	 * @param array $meta_value The values for the fields (person, title, testimonial, linkedin),
	 * @param array $field_args The arguments for the testimonial field when adding to the metabox.
	 * @param [type] $field_object
	 *
	 *
	 */
	public function types_esc_testimonial_field( $check, $meta_value, $field_args, $field_object ) {

		if ( $field_args['repeatable'] && is_array( $meta_value ) ) {
			foreach ( $meta_value as $key => $val ) {
				$meta_value[ $key ] = array_map( 'esc_attr', $val );
			}
			return $meta_value;
		}
		return $check;
	}

	/**
	 * Enqueue the javascript to populate the post type's quickedit fields.
	 */
	public function action_enqueue_clientele_script() {
		global $post_type;
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

}//end class definition.
