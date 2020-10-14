<?php
/**
 * WP_Rig\WP_Rig\TaxonomyExpertise\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\TaxonomyExpertise;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Query;
use WP_Rig\WP_Rig\TaxonomyGlobal\Component as Taxonomies;
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
	private $slug = 'expertise';


	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'expertise';
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
			'get_all_' . $this->slug . '_info' => [ $this, 'get_all_' . $this->slug . '_info' ],
			'get_' . $this->slug . '_images'   => [ $this, 'get_' . $this->slug . '_images' ],
			'get_expertise_links'              => [ $this, 'get_expertise_links' ],
			'get_expertise_terms'              => [ $this, 'get_expertise_terms' ],
			'get_all_expertise_ids'            => [ $this, 'get_all_expertise_ids' ],

		];
	}

	/**
	 * Query for all the data and metadata assigned to any of the taxonomy.
	 *
	 * @param int    $term The id for the taxonomy term.
	 * @param string $taxonomy The taxonomy - default is 'expertise'.
	 * @return array $info An associative array of all the data and metadata assigned to this taxonomy item.
	 */
	public function get_all_expertise_info( $term = '', $taxonomy = 'expertise' ) : array {
		$term_id = $term;
		$info    = [];
		$term    = get_term( $term_id, $taxonomy );
		return $info;
	}

	/**
	 * Output the taxonomy term names as an array.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 * @param bool   $hide_empty    Whether we should retreive the terms that have nothing assigned to them.
	 */
	public function get_expertise_terms( $taxonomy_slug = 'expertise', $hide_empty = false ) : array {
		return Taxonomies::get_all_terms_in_taxonomy( $taxonomy_slug, $hide_empty );
	}

	/**
	 * Get all taxonomy term ids by providing the slug.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 */
	public function get_all_expertise_ids( $taxonomy_slug = 'expertise' ) : array {
		return Taxonomies::get_all_term_ids_from_slug( $taxonomy_slug );
	}

	/**
	 * Get links to all 'expertise' terms.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 * @param array  $except Array of ids from the taxonomy I don't want to get.
	 */
	public function get_expertise_links( $taxonomy_slug = 'expertise', $except = [] ) {
		return Taxonomies::get_taxonomy_term_links( $taxonomy_slug, $except = [] );
	}

	/**
	 * Determine whether there is an image for each type within this taxonomy term.
	 *
	 * @param string $term_id The ID of the term you'd like to check on the image needs of.
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 */
	public function check_expertise_images( $term_id, $taxonomy_slug = 'expertise' ) {
		return Taxonomies::check_taxonomy_term_images( $term_id, $taxonomy_slug );
	}

	/**
	 * Determine whether there is an image for each type within this taxonomy term.
	 *
	 * @param string $term_id The ID of the term you'd like to check on the image needs of.
	 * @param bool   $output_as_id Should we get the image id? Default is true. If False, returns image url.
	 */
	public function get_expertise_images( $term_id, $output_as_id = true ) {
		return Taxonomies::get_term_images( $term_id, $output_as_id );
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
			'description' => 'is this expertise type sometimes referred to by a different name?',
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
			'id'           => $prefix . 'Cinematic',
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
			'id'           => $prefix . 'Rectangular',
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
			'id'           => $prefix . 'Square',
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
			'id'           => $prefix . 'Vertical',
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
		unset( $columns['posts'] );
		// Add the checkbox back in so it can be before the ID column.
		$new['cb']        = '<input type = "checkbox" />';
		$new['id']        = 'ID';
		$new['images']    = '<span style="color:var(--yellow-600);" title="has all photos?" class="material-icons">view_carousel</span>';
		$columns['total'] = '<span style="color:var(--table-header-color);" title="count of tagged photos" class="material-icons">camera_enhance</span>';
		return array_merge( $new, $columns );
	}//end set_expertise_admin_columns()

	/**
	 * Count the photos that have this expertise tag attached.
	 *
	 * @param int    $term_id The term id.
	 * @param string $post_type The post type. Defaults to 'attachment', but could be 'project', 'client', 'page', 'post', 'staffmember', 'nav_menu_item'.
	 *
	 * @return int The quantity of photos that have this taxonomy term applied to them.
	 */
	public function count_expertise_photos( $term_id, $post_type = 'attachment' ) {
		return Taxonomies::count_term_entries( $term_id, $post_type );
	}

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
		$argument = [
			'taxonomy' => $taxonomy,
			'term'     => get_term( $term_id )->name,
		];
		[
			'color'   => $color,
			'message' => $message,
		]         = Taxonomies::check_term_images( $term_id, $taxonomy );
		$icon     = wp_sprintf( '<span style="color:%s;" class="material-icons"> local_library </span>', $color );
		switch ( $column_name ) {
			case 'total':
				$output = $this->count_expertise_photos( $term_id );
				break;
			case 'images':
				$data   = $this->check_expertise_images( $term_id );
				$color  = str_word_count( term_description( $term_id ), 0 ) > 10 ? 'var(--indigo-600)' : 'var(--gray-500)';
				$output = '<span style="color:var(' . $data['color'] . ');" title="' . $data['message'] . '" class="material-icons">view_carousel</span>';
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
	 * Retrieve an array of the images attached to this taxonomy term.
	 *
	 * @param int  $term_id Service Taxonomy Id.
	 * @param bool $output_as_id Should we get the image id? Default is true. If False, returns image url.
	 * @return mixed if $id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public function get_all_expertise_images( $term_id, $output_as_id = true ) : array {
		return Taxonomies::get_term_images( $term_id, $output_as_id );
	}


	/**
	 * Output JSONLD Data for this taxonomy. type to have Rich Text from Google in the header of a page.
	 *
	 * @param int $taxonomy The term id for this taxonomy.
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
