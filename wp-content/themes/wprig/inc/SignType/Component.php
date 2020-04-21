<?php
/**
 * WP_Rig\WP_Rig\SignType\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\SignType;

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
 * Class to create the signtype taxonomy,
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
			'get_use_cases'       => [ $this, 'get_use_cases' ],
			'get_aliases'         => [ $this, 'get_aliases' ],
			'get_all_info'        => [ $this, 'get_all_info' ],
			'get_one_by_one'      => [ $this, 'get_one_by_one' ],
			'get_four_by_three'   => [ $this, 'get_four_by_three' ],
			'get_sixteen_by_nine' => [ $this, 'get_sixteen_by_nine' ],
		];
	}

	/**
	 * Query for all the data and metadata assigned to any of the taxonomy.
	 *
	 * @param int $term_id The id for the taxonomy term.
	 * @return array $info An associative array of all the data and metadata assigned to thie taxonomy item.
	 */
	public function get_all_info( $term_id ) : array {
		$info                    = [];
		$signtype                = get_term( $term_id, 'signtype' );
		$uses                    = $this->get_use_cases( $term_id );
		$info['name']            = $signtype->name;
		$info['term_id']         = $term_id;
		$info['description']     = $signtype->description;
		$info['uses']            = $this->get_use_cases( $term_id );
		$info['aliases']         = $this->get_aliases( $term_id );
		$info['image_primary']   = $this->get_sixteen_by_nine( $term_id, true );
		$info['image_secondary'] = $this->get_four_by_three( $term_id, true );
		$info['image_square']    = $this->get_one_by_one( $term_id, true );
		$info['indepth']         = $this->get_signtype_indepth( $term_id );
		return $info;
	}

	/**
	 * Output the signtype term array.
	 */
	public function get_all_signtypes() : array {
		$terms = get_terms( 'signtype', [ 'hide_empty' => false ] );
		return $terms;
	}

	/**
	 * Get all signtype term identifiers.
	 */
	public function get_signtype_ids() : array {
		$ids       = [];
		$signtypes = $this->get_all_signtypes();
		foreach ( $signtypes as $signtype ) {
			$ids[] = $signtype->term_id;
		}
		return $ids;
	}

	/**
	 * Get links to all signtypes.
	 *
	 * @param array $except Array of ids from the signtype taxonomy I don't want to get.
	 */
	public function get_links( $except = [] ) {
		$types = $this->get_signtype_ids();
		$links = [];
		foreach ( $types as $type ) {
			if ( $except === $type ) continue;
			$name        = $type->name;
			$description = $type->description;
			$links[]     = "<a href=\"#\" title=\"$description\">$name</a>";
		}
		return implode( '', $links );
	}

	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since  1.0.0
	 * @link   https://github.com/CMB2/CMB2/wiki/Box-Properties
	 */
	public function create_extra_fields() {
		$prefix  = $this->get_slug();
		$args    = [
			'id'           => $prefix . 'edit',
			'title'        => 'Sign Type Additional Info',
			'object_types' => [ 'term' ],
			'taxonomies'   => [ 'signtype' ],
			'cmb_styles'   => true,
			'show_in_rest' => \WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::EDITABLE, // Determines which HTTP methods the box is visible in.
		];
		$metabox = new_cmb2_box( $args );

		// Uses List.
		$args = [
			'name'       => 'Instance',
			'desc'       => 'Scenario Wherein this type of sign is best.',
			'default'    => '',
			'id'         => 'signtypeUseCases',
			'type'       => 'text',
			'show_names' => true,
			'repeatable' => true,
			'attributes' => [],
			'text'       => [
				'add_row_text' => 'Add Use Case',
			],
		];
		$metabox->add_field( $args );

		/* Alternative Names */
		$args = [
			'name'        => 'Alternate Names',
			'description' => 'is this sign type sometimes referred to by a different name?',
			'id'          => 'signtypeAltNames',
			'type'        => 'text_small',
			'show_names'  => true,
			'repeatable'  => true,
			'attributes'  => [],
			'text'        => [
				'add_row_text' => 'Add Use Case',
			],
		];
		$metabox->add_field( $args );
		/* Sixteen By Nine Image */
		$args = [
			'name'         => 'Best Images: 16x9',
			'desc'         => '16x9 aspect ratio image',
			'id'           => 'signtypeImageSixteenNine',
			'type'         => 'file',
			'preview_size' => [ 320, 180 ],
			'query_args'   => [ 'type' => 'image/jpeg' ], // Only jpeg.
		];
		$metabox->add_field( $args );

		/* Four By Three Image */
		$args = [
			'name'         => 'Best Images: 4x3',
			'desc'         => '4x3 aspect ratio',
			'id'           => 'signtypeImageFourThree',
			'type'         => 'file',
			'preview_size' => [ 300, 225 ],
			'query_args'   => [ 'type' => 'image/jpeg' ], // Only jpeg.
		];
		$metabox->add_field( $args );

		/* Square Image */
		$args = [
			'name'         => 'Best Images: 1x1',
			'desc'         => '1x1 aspect ratio',
			'id'           => 'signtypeImageSquare',
			'type'         => 'file',
			'preview_size' => [ 300, 300 ],
			'query_args'   => [ 'type' => 'image/jpeg' ], // Only images attachment.
		];
		$metabox->add_field( $args );

		/* Longer Description */
		$args = [
			'name'       => 'longer description',
			'desc'       => 'a longer, keyword-laden description',
			'id'         => 'signtypeIndepth',
			'type'       => 'textarea_small',
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
	public function set_signtype_admin_columns( $columns ) {
		// Remove the checkbox that comes with $columns.
		unset( $columns['cb'] );
		unset( $columns['description'] );
		// Add the checkbox back in so it can be before the ID column.
		$new['cb'] = '<input type="checkbox" />';
		$new['id'] = 'ID';
		return array_merge( $new, $columns );
	}//end set_signtype_admin_columns()

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
	public function make_signtype_columns_sortable( $columns ) {
		$columns['id']   = 'ID';
		$columns['slug'] = 'Slug';
		return $columns;
	}

/**
 * This.
 * signtypeUseCases(group or multiple)
 * signtypeAltNames(group or multiple)
 * signtypeImageSixteenNine
 * signtypeImageFourThree
 * signtypeImageSquare
 * signtypeIndepth
 */

	/**
	 * Retrieve the taxonomy meta for 'signtypeUseCases' for the given sign type.
	 * This is always an array.
	 *
	 * @param int $term_id Signtype Taxonomy Id.
	 * @return array The sudomain of this location's homepage.
	 */
	public function get_use_cases( $term_id ) {
		$key    = 'signtypeUseCases';
		$single = false;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'signtypeAltNames' for the given sign type.
	 * This is always an array.
	 *
	 * @param int $term_id Signtype Taxonomy Id.
	 * @return array The sudomain of this location's homepage.
	 */
	public function get_aliases( $term_id ) {
		$key    = 'signtypeAltNames';
		$single = false;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}


	/**
	 * Retrieve the taxonomy meta for the 16 x 9 aspect image for the given sign type.
	 *
	 * @param int  $term_id Signtype Taxonomy Id.
	 * @param bool $id Should we get the image id. Default true.
	 * @return mixed if $id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public function get_sixteen_by_nine( $term_id, $id = true ) {
		$key    = $id ? 'signtypeImageSixteenNine_id' : 'signtypeImageSixteenNine';
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
	public function get_four_by_three( $term_id, $id = true ) {
		$key    = $id ? 'signtypeImageFourThree_id' : 'signtypeImageFourThree';
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
	public function get_one_by_one( $term_id, $id = true ) {
		$key    = $id ? 'signtypeImageSquare_id' : 'signtypeImageSquare';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'signtypeIndepth' for the given sign type.
	 * This is always an string.
	 *
	 * @param int $term_id Signtype Taxonomy Id.
	 * @return array The sudomain of this location's homepage.
	 */
	public function get_signtype_indepth( $term_id ) : string {
		$key    = 'signtypeIndepth';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}


	/**
	 * Retrieve a list of the ids of all the attachments that have this tag attached to them.
	 *
	 * @param int $term The id for a particular term.
	 *
	 * @return array $ids An array of the ids for the photos assigned to this signtype.
	 */
	public function get_signtype_image_ids( $term ) : array {

		$ids             = [];
		$query_arguments = [
			'tag'       => $term,
			'post_type' => 'attachment',
		];
		return $ids;

	}





}//end class
