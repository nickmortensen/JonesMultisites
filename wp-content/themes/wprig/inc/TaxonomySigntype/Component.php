<?php
/**
 * WP_Rig\WP_Rig\TaxonomySigntype\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\TaxonomySigntype;

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
	private $slug = 'signtype';


	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'signtype';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_head', [ $this, 'add_signtype_rich_snippet_to_head' ] );
		add_action( 'cmb2_init', [ $this, 'create_extra_fields' ] );
		// Admin set post columns - put additional columns into the admin end for the location taxonomy.
		add_filter( 'manage_edit-' . $this->slug . '_columns', [ $this, 'set_' . $this->slug . '_admin_columns' ], 10, 1 );
		add_filter( 'manage_edit-signtype_sortable_columns', [ $this, 'make_signtype_columns_sortable' ] );
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
			'get_all_' . $this->slug . '_ids'  => [ $this, 'get_all_' . $this->slug . '_ids' ],
			'get_all_' . $this->slug . '_info' => [ $this, 'get_all_' . $this->slug . '_info' ],
			'get_' . $this->slug . '_images'   => [ $this, 'get_' . $this->slug . '_images' ],
			'get_' . $this->slug . '_links'    => [ $this, 'get_' . $this->slug . '_links' ],
			'get_' . $this->slug . '_terms'    => [ $this, 'get_' . $this->slug . '_terms' ],
		];
	}

	/**
	 * Output the taxonomy term names as an array.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 * @param bool   $hide_empty    Whether we should retreive the terms that have nothing assigned to them.
	 */
	public function get_signtype_terms( $taxonomy_slug = 'signtype', $hide_empty = false ) : array {
		return Taxonomies::get_all_terms_in_taxonomy( $taxonomy_slug, $hide_empty );
	}

	/**
	 * Get all taxonomy term ids by providing the slug.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 */
	public function get_all_signtype_ids( $taxonomy_slug = 'signtype' ) : array {
		return Taxonomies::get_all_term_ids_from_slug( $taxonomy_slug );
	}

	/**
	 * Get links to all 'signtype' terms.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 * @param array  $except Array of ids from the taxonomy I don't want to get.
	 */
	public function get_signtype_links( $taxonomy_slug = 'signtype', $except = [] ) {
		return Taxonomies::get_taxonomy_term_links( $taxonomy_slug, $except = [] );
	}

	/**
	 * Determine whether there is an image for each type within this taxonomy term.
	 *
	 * @param string $term_id The ID of the term you'd like to check on the image needs of.
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 */
	public function check_signtype_images( $term_id, $taxonomy_slug = 'signtype' ) {
		return Taxonomies::check_taxonomy_term_images( $term_id, $taxonomy_slug );
	}

	/**
	 * Determine whether there is an image for each type within this taxonomy term.
	 *
	 * @param string $term_id The ID of the term you'd like to check on the image needs of.
	 * @param bool   $output_as_id Should we get the image id? Default is true. If False, returns image url.
	 */
	public function get_signtype_images( $term_id, $output_as_id = true ) {
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
	 * Set up some new columns in the admin screen for the taxonomy.
	 *
	 * @param array $columns The existing columns before I monkeyed with them.
	 * @link https://shibashake.com/wordpress-theme/modify-custom-taxonomy-columns
	 */
	public function set_signtype_admin_columns( $columns ) {
		// Remove the checkbox that comes with $columns.
		unset( $columns['cb'] );
		unset( $columns['description'] );
		unset( $columns['posts'] );
		// Add the checkbox back in so it can be before the ID column.
		$new['cb']         = '<input type = "checkbox" />';
		$new['id']         = 'ID';
		$new['images']     = '<span style="color: var(--table-header-color);" title="has all photos?" class="material-icons">view_carousel</span>';
		$columns['counts'] = '<span style="color: var(--table-header-color);" title="quantities of both photos and projects with this signtype tag" class="material-icons">class</span>';
		return array_merge( $new, $columns );
	}//end set_signtype_admin_columns()

	/**
	 * Make new column sortable within the admin area.
	 *
	 * @param array $columns The new columns to make sortable.
	 * @return array $columns All the columns you want sortable.
	 */
	public function make_signtype_columns_sortable( $columns ) {
		$columns['id'] = 'ID';
		return $columns;
	}

	/**
	 * Count the photos that have this signtype tag attached.
	 *
	 * @param int    $term_id The term id.
	 * @param string $post_type The post type. Defaults to 'attachment', but could be 'project', 'client', 'page', 'post', 'staffmember', 'nav_menu_item'.
	 *
	 * @return int The quantity of photos that have this taxonomy term applied to them.
	 */
	public function count_signtype_photos( $term_id, $post_type = 'attachment' ) {
		return Taxonomies::count_term_entries( $term_id, $post_type );
	}

	/**
	 * Count the projects that have this signtype tag attached.
	 *
	 * @param int $term_id The term id.
	 *
	 * @return int The quantity of photos that have this taxonomy term applied to them.
	 */
	public function count_signtype_projects( $term_id ) {
		return Taxonomies::count_term_entries( $term_id, 'project' );
	}

	/**
	 * Add the correct data to the newly attached custom admin columns for this taxonomy.
	 *
	 * @param  string $content Already existing content for the already existing rows.
	 * @param  string $column_name As instantiated in the 'set_location_admin_columns' function.
	 * @param  int    $term_id Term in quation.
	 * @echo   string $output The content for the columns.
	 */
	public function set_data_for_custom_admin_columns( $content, $column_name, $term_id ) {

		$taxonomy = get_term( $term_id )->taxonomy;
		$argument = [
			'taxonomy' => $taxonomy,
			'term'     => get_term( $term_id )->name,
		];
		[
			'color'   => $color,
			'message' => $message,
		]         = Taxonomies::check_term_images( $term_id, $taxonomy );

		$icon = wp_sprintf( '<span style="color:%s;" class="material-icons"> local_library </span>', $color );
		switch ( $column_name ) {
			case 'counts':
				$photos   = $this->count_signtype_photos( $term_id );
				$projects = $this->count_signtype_projects( $term_id );
				$style    = 'color: var(--indigo-600); font-weight: 900;';
				$output   = wp_sprintf( '<span style="%s" title="%s has %d photos">Photos: %d</span>', $style, get_term( $term_id )->name, $photos, $photos );
				if ( 0 < $this->count_signtype_projects( $term_id ) ) {
					$output .= '<br>';
					$output .= wp_sprintf( '<span style="%s" title="%s has %d projects attached">Projects: %d</span>', $style, get_term( $term_id )->name, $projects, $projects );
				}
				break;
			case 'images':
				$output = wp_sprintf( '<span style="color:var(%s);" title="%s" class="material-icons">view_carousel</span>', $color, $message );
				break;
			default:
				$output = $term_id;
		}
		echo $output;
	}

	/**
	 * Query for all the data and metadata assigned to any of the taxonomy.
	 *
	 * @param int    $term_id The id for the taxonomy term.
	 * @param string $taxonomy The taxonomy the term resides in -- in this case the 'signtype'.
	 *
	 * @return array $info An associative array of all the data and metadata assigned to this taxonomy item.
	 */
	public function get_all_signtype_info( $term_id, $taxonomy = 'signtype' ) {
		$base_info          = (array) get_term( $term_id, $taxonomy, ARRAY_A );
		$info               = [];
		$info['alt_names']  = Taxonomies::get_term_aliases( $term_id, $taxonomy );
		$info['indepth']    = Taxonomies::get_term_indepth( $term_id, $taxonomy );
		$info['images_id']  = Taxonomies::get_term_images( $term_id, $taxonomy );
		$info['images_url'] = Taxonomies::get_term_images( $term_id, $taxonomy, false );
		return array_merge( $base_info, $info );
	}

	/**
	 * Output JSONLD Data for this taxonomy term.
	 * Type to have Rich Text from Google in the header of a page.
	 *
	 * @see Must use get_queried_object() instead of a specific term_id as it goes inside of the head.
	 * @return string The Rich snippet to be embedded in the head of a signtype taxonomy page.
	 * @link https://developers.google.com/search/docs/data-types/product
	 */
	private function get_signtype_rich_snippet() : string {
		$term_id = get_queried_object()->term_id;
		[
			'description' => $description,
			'name'        => $name,
			'slug'        => $slug,
			'alt_names'   => $alt_names,
			'indepth'     => $indepth,
			'images_id'   => $images_id,
			'images_url'  => $images_url,
		]        = $this->get_all_signtype_info( $term_id );
		$output  = '<script type = "application/ld+json">';
		$output .= "\r";
		$output .= <<<JSONLD
		{
			"@context": "https://schema.org/",
			"@type": "Product",
			"name": "$name",
			"image": [
				"{$images_url['square']}",
				"{$images_url['rectangular']}",
				"{$images_url['cinematic']}"
			],
			"description": "$description",
			"sku": "$slug",
			"mpn": "signtype-$slug",
			"brand": {
				"@type": "Brand",
				"name": "$name"
			},
			"review": {
				"@type": "Review",
				"reviewRating": {
					"@type": "Rating",
					"ratingValue": "4",
					"bestRating": "5"
				},
				"author": {
					"@type": "Person",
					"name": "Sarajane Herbert"
				}
			},
			  "aggregateRating": {
				"@type": "AggregateRating",
				"ratingValue": "4.9",
				"reviewCount": "89"
			}

		}
JSONLD;
		$output .= "\r";
		$output .= '</script>';
		return $output;
	}

	/**
	 * Put Rich Snippet for the signtype in the head.
	 *
	 * @see Must use get_queried_object() instead of a specific term_id as it goes inside of the head.
	 */
	public function add_signtype_rich_snippet_to_head() {
			if ( is_tax( 'signtype' ) ) {
				echo self::get_signtype_rich_snippet();
			}
	}

	/**
	 * Place Open Graph Data for this taxonomy term into the head of the webpage.
	 */



}//end class
