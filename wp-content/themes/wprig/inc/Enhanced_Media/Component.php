<?php
/**
 * WP_Rig\WP_Rig\Enhanced_Media\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Enhanced_Media;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Query;
use function add_action;
use function get_terms;
use function get_term;
use function get_term_meta;

use function register_taxonomy;

/**
 * NOTE: See description on the google rich snippet for product.
 *
 * @link https://developers.google.com/search/docs/data-types/product
 */

/**
 * Class to create the capability taxonomy,
 *
 * @since 1.0.0
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * The slug of this taxonomy.
	 *
	 * @access   private
	 * @var      string    $slug The slug of this taxonomy..
	 */
	private $slug = 'enhanced_media';

	/**
	 * Version.
	 *
	 * @var string
	 */
	public $version = '4.0.1.1';

	/**
	 * Options.
	 *
	 * @var array
	 */
	public $options = null;

	/**
	 * Taxonomies.
	 *
	 * @var array
	 */
	public $taxonomies = null;

	/**
	 * Shortcodes.
	 *
	 * @var array
	 */
	public $shortcodes = null;

	/**
	 * Shortcodes.
	 *
	 * @var array
	 */
	public $license = 'B3JkZXI9MTA2MDY3OTAwMTk5fGRhdGU9MjAxNi0xMS0xNCAxNTo0MzowNw==';

	/**
	 * Options.
	 *
	 * @var array
	 */
	public $options = [
		'name'        => 'Enhanced Media',
		'dir'         => [],
		'basename'    => [],
		'slug'        => [],
		'file'        => [],
		'taxonomies'  => [ 'industry', 'signtype', 'expertise' ],
		'lib_options' => [],
		'tax_options' => [],
		'mime_types'  => [],
	];

	/**
	 * Settings.
	 *
	 * @var array
	 */
	public $settings = null;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'enhanced_media';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_head', [ $this, 'add_' . $this->slug . '_rich_snippet_to_head' ] );
		add_action( 'cmb2_init', [ $this, 'create_extra_fields' ] );
		// Admin set post columns - put additional columns into the admin end for the location taxonomy.
		add_filter( 'manage_edit-' . $this->slug . '_columns', [ $this, 'set_' . $this->slug . '_admin_columns' ], 10, 1 );
		add_filter( 'manage_edit-' . $this->slug . '_sortable_columns', [ $this, 'make_' . $this->slug . '_columns_sortable' ], 10, 1 );
		add_filter( 'manage_' . $this->slug . '_custom_column', [ $this, 'set_data_for_custom_admin_columns' ], 10, 3 );
	}

	// EXPOSE TEMPLATE TAGS.
	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [
			'get_all_' . $this->slug . '_info'   => [ $this, 'get_all_' . $this->slug . '_info' ],
			'get_all_' . $this->slug . '_images' => [ $this, 'get_all_' . $this->slug . '_images' ],
		];
	}

	/**
	 * Query for all the data and metadata assigned to any of the taxonomy.
	 *
	 * @param int $term_id The id for the taxonomy term.
	 * @return array $info An associative array of all the data and metadata assigned to thie taxonomy item.
	 */
	public function get_all_expertise_info( $term = '', $taxonomy = 'expertise' ) : array {
		$term_id = $term;
		$info    = [];
		$term    = get_term( $term_id, $taxonomy );
		return $info;
	}

	/**
	 * Output the taxonomy term array.
	 */
	public function get_all_expertises() : array {
		$terms = get_terms( $this->get_slug(), [ 'hide_empty' => false ] );
		return $terms;
	}

	/**
	 * Get all taxonomy term ids.
	 */
	public function get_expertise_ids() : array {
		$ids       = [];
		$items = $this->get_all_expertises();
		foreach ( $items as $item ) {
			$ids[] = $item->term_id;
		}
		return $ids;
	}

	/**
	 * Get links to all terms.
	 *
	 * @param array $except Array of ids from the taxonomy I don't want to get.
	 */
	public function get_expertise_links( $except = [] ) {
		$items = $this->get_expertise_ids();
		$links = [];
		foreach ( $items as $item ) {
			if ( $except === $item ) continue;
			$name        = $item->name;
			$description = $item->description;
			$links[]     = "<a href=\"#\" title=\"$description\">$name</a>";
		}
		return implode( '', $links );
	}

	/**
	 * Create the extra fields for this custom taxonomy.
	 *
	 * Use CMB2 to create additional fields for the taxonomy.
	 *
	 * @since  1.0.0
	 * @link   https://github.com/CMB2/CMB2/wiki/Box-Properties
	 */
	public function create_extra_fields() {
		$prefix  = $this->get_slug();
		$args    = [
			'id'           => $prefix . 'edit',
			'title'        => ucfirst( $prefix ) . ' Additional Info',
			'object_types' => [ 'term' ],
			'taxonomies'   => [ $prefix ],
			'cmb_styles'   => false,
			'show_in_rest' => \WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::EDITABLE, // Determines which HTTP methods the box is visible in.
		];
		$metabox = new_cmb2_box( $args );


		/* Alternative Names */
		$args = [
			'classes'     => [ 'align-button-right' ],
			'name'        => 'Alternate Names',
			'description' => 'is this sign type sometimes referred to by a different name?',
			'id'          => $prefix . 'AltNames',
			'type'        => 'text',
			'show_names'  => true,
			'repeatable'  => true,
			'attributes'  => [],
			'text'        => [
				'add_row_text' => 'Add Alternate Name',
			],
		];
		$metabox->add_field( $args );

		/* Sixteen By Nine Image */
		$args = [
			'classes'      => [ 'align-button-right' ],
			'name'         => 'Cinematic Image',
			'desc'         => 'aspect:16x9',
			'id'           => $prefix . 'ImageCinematic',
			'type'         => 'file',
			'preview_size' => [ 320, 180 ],
			'text'         => [
				'add_upload_file_text' => 'add',
			],
			'query_args'   => [ 'type' => 'image/jpeg' ], // Only jpeg.
		];
		$metabox->add_field( $args );

		/* Four By Three Image */
		$args = [
			'classes'      => [ 'align-button-right' ],
			'name'         => 'Rectangular Image',
			'desc'         => 'aspect:4x3',
			'id'           => $prefix . 'ImageRectangular',
			'type'         => 'file',
			'preview_size' => [ 300, 225 ],
			'text'         => [
				'add_upload_file_text' => 'add',
			],
			'query_args'   => [ 'type' => 'image/jpeg' ], // Only jpeg.
		];
		$metabox->add_field( $args );

		/* Square Image */
		$args = [
			'classes'      => [ 'align-button-right' ],
			'name'         => 'Square Image',
			'desc'         => 'aspect:1x1',
			'id'           => $prefix . 'ImageSquare',
			'type'         => 'file',
			'preview_size' => [ 300, 300 ],
			'text'         => [
				'add_upload_file_text' => 'add',
			],
			'query_args'   => [ 'type' => 'image/jpeg' ], // Only images attachment.
		];
		$metabox->add_field( $args );

		/* Vertical image */
		$args = [
			'classes'      => [ 'align-button-right' ],
			'name'         => 'Vertical Image',
			'desc'         => 'aspect:3x4',
			'id'           => $prefix . 'ImageVertical',
			'type'         => 'file',
			'preview_size' => [ 300, 400 ],
			'text'         => [
				'add_upload_file_text' => 'add',
			],
			'query_args'   => [ 'type' => 'image/jpeg' ], // Only images attachment.
		];
		$metabox->add_field( $args );


		/**
		 * Projects With This Type of Sign.
		 *
		 * @link https://github.com/polupraneeth/cmb2-extension/wiki/Field-Types
		 * @see plugins/cmb2-extensions
		 */
		$args = [
			'name'       => 'Example Projects',
			'desc'       => '(Start typing project title)',
			'id'         => $prefix . 'Project',
			'type'       => 'ajax_search',
			'tab'        => 'search',
			'multiple'   => true,
			'limit'      => 10,
			'sortable'   => true, // Allow selected items to be sortable (default false).
			'search'     => 'post',
			'query_args' => [
				'post_type'   => [ 'project' ],
				'post_status' => [ 'publish', 'pending' ],
			],

		];
		// $metabox->add_field( $args );

		/**
		 * Allow to be used.
		 */
		$args = [
			'name'    => 'Allow',
			'desc'    => 'Allow',
			'id'      => $prefix . 'Allow',
			'type'    => 'switch_button',
			'default' => 'on' //If it's checked by default.
		];
		// $metabox->add_field( $args );

		/* Longer Description */
		$args = [
			'name'       => 'longer description',
			'desc'       => 'a longer, keyword-laden description -- may use html markup',
			'id'         => $prefix . 'Indepth',
			'type'       => 'textarea_code',
			'attributes' => [
				'data-richsnippet' => 'long-description',
			],
		];
		$metabox->add_field( $args );
	}//end create_extra_fields()

	/**
	 * Set up some new columns in the admin screen for the location taxonomy.
	 *
	 * @param array $columns The existing columns before I monkeyed with them.
	 * @link https://shibashake.com/wordpress-theme/modify-custom-taxonomy-columns
	 */
	public function set_expertise_admin_columns( $columns ) {
		// Remove the checkbox that comes with $columns.
		unset( $columns['cb'] );
		unset( $columns['description'] );
		// Add the checkbox back in so it can be before the ID column.
		$new['cb']    = '<input type = "checkbox" />';
		$new['id']    = 'ID';
		$new['allow'] = 'Allow?';
		return array_merge( $new, $columns );
	}//end set_set_capability_admin_columns()

	/**
	 * Add the correct data to the custom columns.
	 *
	 * @param  string $content Already existing content for the already existing rows.
	 * @param  string $column_name As instantiated in the 'set_location_admin_columns' function.
	 * @param  int    $term_id Term in quation.
	 * @echo   string $output The content for the columns.
	 */
	public function set_data_for_custom_admin_columns( $content, $column_name, $term_id ) {
		$taxonomy = $this->get_slug();

		switch ( $column_name ) {
			case 'id':
				$output = $term_id;
				break;
			case 'allow':
				$output = '<span class="material-icons">pan_tool</span>';
				break;
			default:
				$output = $term_id;
		}
		echo $output;
	}

	/**
	 * Make new column sortable within the admin area.
	 *
	 * @param array $columns The new columns to make sortable.
	 * @return array $columns All the columns you want sortable.
	 */
	public function make_expertise_columns_sortable( $columns ) {
		$columns['id']   = 'ID';
		$columns['slug'] = 'Slug';
		return $columns;
	}

	/**
	 * Retrieve the taxonomy meta for 'expertise AltNames' for the given sign type.
	 * This is always an array.
	 *
	 * @param int $term_id Taxonomy term Id.
	 * @return array The sudomain of this location's homepage.
	 */
	private function get_expertise_aliases( $term_id ) {
		$prefix = $this->get_slug();
		$key    = $prefix . 'AltNames';
		$single = false;
		$output = get_term_meta( $term_id, $key, $single );
		return $output[0];
	}



	/**
	 * Retrieve the taxonomy meta for the 16 x 9 aspect image for the given sign type.
	 *
	 * @param int  $term_id Signtype Taxonomy Id.
	 * @param bool $id Should we get the image id. Default true.
	 * @return mixed if $id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	private function get_cinematic_image( $term_id, $id = true ) {
		$prefix = $this->get_slug();
		$key    = $id ? $prefix . 'ImageCinematic_id' : $prefix . 'ImageCinematic';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 4 x 3 aspect image for the given sign type.
	 *
	 * @param int  $term_id Signtype Taxonomy Id.
	 * @param bool $id Should we get the image id. Default true.
	 * @return mixed if $id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	private function get_rectangular_image( $term_id, $id = true ) {
		$prefix = $this->get_slug();
		$key    = $id ? $prefix . 'ImageRectangular_id' : $prefix . 'ImageRectangular';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 1 x 1 aspect image for the given sign type.
	 *
	 * @param int  $term_id Signtype Taxonomy Id.
	 * @param bool $id Should we get the image id. Default true.
	 * @return mixed if $id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	private function get_square_image( $term_id, $id = true ) {
		$prefix = $this->get_slug();
		$key    = $id ? $prefix . 'ImageSquare_id' : $prefix . 'ImageSquare';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 1 x 1 aspect image for the service.
	 *
	 * @param int  $term_id Signtype Taxonomy Id.
	 * @param bool $id Should we get the image id. Default true.
	 * @return mixed if $id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	private function get_vertical_image( $term_id, $id = true ) {
		$prefix = $this->get_slug();
		$key    = $id ? $prefix . 'ImageVertical_' : $prefix . 'ImageVertical';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve an array of the images attached to this taxonomy term.
	 *
	 * @param int  $term_id Service Taxonomy Id.
	 * @param bool $id Should we get the image id. Default true.
	 * @return mixed if $id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public function get_all_expertise_images( $term_id, $id = true ) : array {
		$output                = [];
		$output['square']      = self::get_square_image( $term_id, $id );
		$output['vertical']    = self::get_vertical_image( $term_id, $id );
		$output['cinematic']   = self::get_cinematic_image( $term_id, $id );
		$output['rectangular'] = self::get_rectangular_image( $term_id, $id );
		return $output;
	}


	/**
	 * Retrieve the taxonomy meta for taxonomyInDepth field.
	 * This is always an string.
	 *
	 * @param int $term_id Taxonomy term Id.
	 * @return string HTML for the taxonomy in depth.
	 */
	public function get_expertise_indepth( $term_id ) : string {
		$prefix = $this->get_slug();
		$key    = $prefix . 'Indepth';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Output JSONLD Data for this taxonomy. type to have Rich Text from Google in the header of a page.
	 *
	 * @param int $term_id The term id for this taxonomy.
	 * @link https://developers.google.com/search/docs/data-types/product
	 */
	private function get_rich_snippet( $taxonomy = 'expertise' ) {
		$term_id = get_queried_object()->term_id;
		$term    = get_term( $term_id, $taxonomy );
		$slug    = $term->slug;
		$desc    = $term->description;
		$name    = $term->name;
		$info    = $this->get_all_expertise_info( $term_id );
		$output  = ' <script type = "application/ld+json">';
		$output .= <<<JSONLD
		{
			"@context": "https://schema.org/",

JSONLD;
		$output .= '</script>';
		return $output;
	}

	/**
	 * Put Rich Snippet for the signtype in the head.
	 */
	public function add_expertise_rich_snippet_to_head() {
		if ( is_tax( 'expertise' ) ) {
			echo self::get_rich_snippet();
		}
	}



}//end class
