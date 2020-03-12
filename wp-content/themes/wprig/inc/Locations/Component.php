<?php
/**
 * WP_Rig\WP_Rig\Locations\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Locations;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;

use function add_action;
use function get_terms;
use function register_taxonomy;

/**
 * Class to create the location taxonomy,
 *
 * @since 1.0.0
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * The slug of this taxonomy.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slug The slug of this taxonomy..
	 */
	private $slug = 'location';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'location';
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
			'get_jones_locations'       => [ $this, 'get_jones_locations' ],
			'location_links'            => [ $this, 'location_links' ],
			'get_location_info_by_id'   => [ $this, 'get_location_info_by_id' ],
			'get_city_image_url'        => [ $this, 'get_city_image_url' ],
			'get_blend_modes'           => [ $this, 'get_blend_modes' ],
			'get_location_taxonomy'     => [ $this, 'get_location_taxonomy' ],
			'get_location_subdomain'    => [ $this, 'get_location_subdomain' ],
			'get_location_url'          => [ $this, 'get_location_url' ],
			'get_location_capability'   => [ $this, 'get_location_capability' ],
			'get_location_image'        => [ $this, 'get_location_image' ],
			'get_location_city_photo'   => [ $this, 'get_location_city_photo' ],
			'get_location_address_info' => [ $this, 'get_location_address_info' ],

		];
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'init', [ $this, 'create_location_taxonomy' ] );
		add_action( 'cmb2_init', [ $this, 'create_location_taxonomy_extra_fields' ] );
		// Admin set post columns - put additional columns into the admin end for the location taxonomy.
		add_filter( 'manage_edit-' . $this->slug . '_columns', [ $this, 'set_location_admin_columns' ], 10, 1 );
	}

	/**
	 * Jones Location information.
	 *
	 * @var array An array of associative arrays containing location information.
	 */
	protected $locations = [
		//phpcs:disable
		[ 'city_image' => 7, 'blog_id' => 1, 'location' => 'national', 'url' => 'www.jonessign.io', 'slug' => 'nat', 'term_id' => 71 ],
		[ 'city_image' => 8, 'blog_id' => 2, 'location' => 'green bay', 'url' => 'greenbay.jonessign.io', 'slug' => 'grb', 'term_id' => 72 ],
		[ 'city_image' => 7, 'blog_id' => 3, 'location' => 'philadelphia', 'url' => 'philadelphia.jonessign.io', 'slug' => 'phl', 'term_id' => 64 ],
		[ 'city_image' => 7, 'blog_id' => 4, 'location' => 'denver', 'url' => 'denver.jonessign.io', 'slug' => 'den', 'term_id' => 75 ],
		[ 'city_image' => 10, 'blog_id' => 5, 'location' => 'los angeles', 'url' => 'losangeles.jonessign.io', 'slug' => 'lax', 'term_id' => 70 ],
		[ 'city_image' => 14, 'blog_id' => 6, 'location' => 'san diego', 'url' => 'sandiego.jonessign.io', 'slug' => 'san', 'term_id' => 69 ],
		[ 'city_image' => 11, 'blog_id' => 7, 'location' => 'miami', 'url' => 'miami.jonessign.io', 'slug' => 'mia', 'term_id' => 73 ],
		[ 'city_image' => 12, 'blog_id' => 8, 'location' => 'minneapolis', 'url' => 'minneapolis.jonessign.io', 'slug' => 'msp', 'term_id' => 74 ],
		[ 'city_image' => 13, 'blog_id' => 9, 'location' => 'richmond', 'url' => 'richmond.jonessign.io', 'slug' => 'ric', 'term_id' => 62 ],
		[ 'city_image' => 15, 'blog_id' => 10, 'location' => 'tampa', 'url' => 'tampa.jonessign.io', 'slug' => 'tpa', 'term_id' => 68 ],
		[ 'city_image' => 9, 'blog_id' => 11, 'location' => 'las vegas', 'url' => 'vegas.jonessign.io', 'slug' => 'las', 'term_id' => 61 ],
		[ 'city_image' => 13, 'blog_id' => 12, 'location' => 'virginia beach', 'url' => 'virginiabeach.jonessign.io', 'slug' => 'vab', 'term_id' => 67 ],
		[ 'city_image' => 13, 'blog_id' => 13, 'location' => 'juarez', 'url' => 'juarez.jonessign.io', 'slug' => 'mxj', 'term_id' => 66 ],
	];



	/**
	 * Output the locations array.
	 */
	public function get_jones_locations() {
		$locations = $this->locations;
		return $locations;
	}

	/**
	 * Get the array of information about the location from the $locations array.
	 *
	 * @param int $id Blog ID.
	 */
	public function get_location_info_by_id( $id ) : array {
		$locations      = $this->get_jones_locations();
		$location_index = array_search( $id, array_column( $locations, 'blog_id' ), false );
		return $locations[$location_index];
	}

	/**
	 * Retrieve the url of the city background image.
	 *
	 * @param string $size Wordpress image size slug, options are: full | 2048x2048 | 1536x1536| large | medium_large | wp-rig-featured | medium | thumbnail
	 */
	public function get_city_image_url( $size = 'wp-rig-featured' ) {
		 $location      = $this->get_location_info_by_id( get_current_blog_id() );
		 $city_image_id = $location['city_image'];
		 return wp_get_attachment_image_url( $city_image_id, $size, false );
	}

	/**
	 * Gets taxonomy term information by blog id.
	 */
	public function get_location_taxonomy_by_blog_id() {
		return null;
	}

	/**
	 * Gets the css options for Background Blend Mode as an array;
	 */
	public function get_blend_modes() {
		$modes = [ 'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity' ];
		return $modes;
	}

	/**
	 * Gets the css options for Background Filter as an array;
	 */
	public function getbackground_filters() {
		$background_filters = [ 'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity' ];
		return $background_filters;
	}


	//phpcs:enable

	/**
	 * Gets the locations array.
	 *
	 * @return string Component slug.
	 */
	public function get_locations() : array {
		return $this->locations;
	}

	/**
	 * Grab all tha taxonomy information for the 'location' taxonomy.
	 */
	public function get_location_taxonomy() {
		$locations = get_terms(
			[
				'taxonomy' => 'location',
				'hide_empty' => false,
			]
		);
		return $locations;
	}

	/**
	 * Creates the custom taxonomy: 'Location'.
	 *
	 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
	 */
	public function create_location_taxonomy() {
		$singular      = 'location';
		$plural        = ucfirst( $singular ) . 's';
		$labels        = [
			'name'                       => $plural . '- jones sign co',
			'singular_name'              => $singular,
			'menu_name'                  => $plural,
			'all_items'                  => 'All' . $plural,
			'parent_item'                => 'Main',
			'parent_item_colon'          => 'Main ' . $singular,
			'new_item_name'              => 'New ' . $singular,
			'add_new_item'               => 'Add New ' . $singular,
			'edit_item'                  => 'Edit ' . $singular,
			'update_item'                => 'Update ' . $singular,
			'view_item'                  => 'View ' . $singular,
			'separate_items_with_commas' => 'Separate locations with commas',
			'add_or_remove_items'        => 'Add or remove ' . $plural,
			'choose_from_most_used'      => 'Frequently Used ' . $plural,
			'popular_items'              => 'Popular ' . $plural,
			'search_items'               => 'Search ' . $plural,
			'not_found'                  => 'Not Found',
			'no_terms'                   => 'No ' . $plural,
			'items_list'                 => $plural . ' list',
			'items_list_navigation'      => $plural . ' list navigation',
			'back_to_terms'              => 'Back to ' . $singular . ' Tags',
		];
		$rewrite       = [
			'slug'         => $singular,
			'with_front'   => true,
			'hierarchical' => false,
		];
		$args          = [
			'labels'             => $labels,
			'description'        => 'Covers Various Jones Sign Company Locations around North America',
			'hierarchical'       => false,
			'public'             => true,
			'show_ui'            => true,
			'show_in_quick_edit' => true,
			'show_in_menu'       => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => true,
			'query_var'          => $singular,
			'rewrite'            => $rewrite,
			'show_in_rest'       => true,
			'rest_base'          => $singular,
		];
		$objects_array = [
			'post',
			'page',
			'attachment',
			'nav_menu_item',
		];
		register_taxonomy( 'location', $objects_array, $args );
	}//end create_location_taxonomy()

	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since  1.0.0
	 * @link   https://github.com/CMB2/CMB2/wiki/Box-Properties
	 */
	public function create_location_taxonomy_extra_fields( $states ) {

		$prefix = $this->get_slug();

		$args = [
			'id'           => $prefix . 'edit',
			'title'        => 'Location Taxonomy Extra Info',
			'object_types' => [ 'term' ],
			'taxonomies'   => [ 'location' ],
			'cmb_styles'   => true,
			'show_in_rest' => \WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::EDITABLE, // Determines which HTTP methods the box is visible in.
		];
		$metabox = new_cmb2_box( $args );

		/* BlogID */
		$args = [
			'name'        => 'blog id',
			'description' => 'blog_id',
			'id'          => 'locationBlogID',
			'type'        => 'text_small',
			'show_names'  => true,
		];
		$metabox->add_field( $args );

		/* SUBDOMAIN URL */
		$args = [
			'name'        => 'Subdomain Website URL',
			'description' => 'subdomain website url',
			'id'          => 'subdomainURL',
			'type'        => 'text_url',
			'show_names'  => true,
			'protocols'   => [ 'http', 'https' ],
		];
		$metabox->add_field( $args );

		/* NIMBLE URL */
		$args = [
			'name'        => 'Website URL',
			'description' => 'nimble website url',
			'id'          => 'locationURL',
			'type'        => 'text_url',
			'show_names'  => true,
			'protocols'   => [ 'http', 'https' ],
		];
		$metabox->add_field( $args );

		/* CAPABILITIES OF THE LOCATION */
		$args = [
			'name'              => 'Capability',
			'desc'              => 'check all that apply',
			'id'                => 'locationCapabilities',
			'type'              => 'multicheck',
			'inline'            => true,
			'select_all_button' => false,
			'options'           => [
				'Fabrication'        => 'Fab',
				'Installation'       => 'Install',
				'Project Management' => 'PM',
				'Sales'              => 'Sales',
			],
		];
		$metabox->add_field( $args );

		/* Location Image */
		$args = [
			'name'         => 'Location Image',
			'show_names'   => true,
			'id'           => 'locationImage',
			'type'         => 'file',
			'options'      => [ 'url' => false ], // No box that allows for the url to be typed in as I want to use the image ids.
			'text'         => [ 'add_upload_file_text' => 'Upload or Find Location Image' ],
			// query_args are passed to wp.media's library query.
			'query_args'   => [
				'type' => [ 'image/jpg', 'image/jpeg' ],
			],
			'preview_size' => 'medium',
		];
		$metabox->add_field( $args );
		/* CITY IMAGE */
		$args = [
			'name'         => 'City Image',
			'show_names'   => true,
			'id'           => 'cityImage',
			'type'         => 'file',
			'options'      => [ 'url' => false ],
			'text'         => [ 'add_upload_file_text' => 'Upload or Find City Image' ],
			'query_args'   => [
				'type' => [ 'image/jpg', 'image/jpeg' ],
			],
			'preview_size' => 'medium',
		];
		$metabox->add_field( $args );

		/* JONES LOCATION DATA */
		$args = [
			'name'         => 'Location',
			// 'desc'         => 'Address Info',
			'id'           => 'jonesLocationInfo', // Name of the custom field type we setup.
			'type'         => 'jonesaddress',
			'object_types' => [ 'staff' ], // Only show on project post types.
			'show_names'   => false, // false removes the left cell of the table -- this is worth understanding.
			'after_row'    => '<hr>',
		];
		$metabox->add_field( $args );

	}//end create_location_taxonomy_extra_fields()

	/**
	 * Set up some new columns in the admin screen for the location taxonomy.
	 *
	 * @param array $columns The existing columns before I monkeyed with them.
	 * @link https://shibashake.com/wordpress-theme/modify-custom-taxonomy-columns
	 */
	public function set_location_admin_columns( $columns ) {
		// Remove the checkbox that comes with $columns.
		unset( $columns['cb'] );
		// Add the checkbox back in so it can be before the ID column.
		$new['cb'] = '<input type="checkbox" />';
		$new['id'] = 'ID';
		return array_merge( $new, $columns );
	}

	/**
	 * Retrieve the taxonomy meta for 'subdomainURL' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_subdomain( $term_id ) {
		$key    = 'subdomainURL';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}
	/**
	 * Retrieve the taxonomy meta for 'locationURL' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_url( $term_id ) {
		$key    = 'locationURL';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'locationCapabilities' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_capability( $term_id ) {
		$key    = 'locationCapabilities';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}
	/**
	 * Retrieve the taxonomy meta for 'locationImage' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_image( $term_id ) {
		$key    = 'locationImage';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}
	/**
	 * Retrieve the taxonomy meta for 'cityImage' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_city_photo( $term_id ) {
		$key    = 'cityImage';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}
	/**
	 * Retrieve the taxonomy meta for 'jonesLocationInfo' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return array $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_address_info( $term_id ) {
		$key    = 'jonesLocationInfo';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * List of locations with links. Appears along bottom of the footer.
	 */
	public function location_links() {
		$locations = $this->get_locations();
		$current   = get_current_blog_id();
		$classes   = [
			'bg-blue-100',
			'text-blue-400',
			'font-bold',
			'py-2',
			'px-4',
			'border-b-4',
			'hover:border-b-2',
			'border-blue-200',
			'hover:border-blue-400',
			'rounded',
		];
		$classes   = implode( ' ', $classes );
		$items     = [];
		foreach ( $locations as $location ) {
			$city     = $location['location'];
			$cityname = 'national' !== $city ? ucwords( $city ) : 'Company';
			$blogid   = $location['blog_id'];
			$url      = 'nat' !== $location['slug'] ? 'https://' . $location['url']: 'https://jonessign.io';
			$termid   = $location['term_id'];
			$slug     = $location['slug'];
			$name     = 'nat' !== $slug ? ucwords( $location['location'] ) : 'Co.';
			// no need to link to the subdomain site we are currently on.
			if ( 'Philadelphia' === $name ) continue;
			if ( $current !== $blogid ) {
				$items[] = "<a title=\"link to the homepage for Jones Sign $cityname\" data-tax=\"$termid\" class=\"$classes\" href=\"$url\">$name</a>";
			}
		}
		$output  = '<div class="flex justify-around">';
		$output .= implode( '', $items );
		$output .= '</div>';
		return $output;
	}

	/**
	 * Standard description of the company.
	 */
	public function get_boilerplate_company_description() {
		$locations       = $this->get_location_taxonomy();
		$total_locations = count( $locations );
		$description     = 'Jones Sign Company is headquartered in Green Bay, Wisconsin. We are a national company with locations across North America. Our focus is delivering signage solutions to large scale environments such as stadiums, shopping malls, and campuses.
		Our expert staff is capable of servicing signs made by any manufacturer in the United States. Our reputation for creating unique and custom designed signs sets us apart from the competition.  As a full service company, we can manage every aspect of your project, and welcome the accountability expected from clients.';
		return $description;
	}


}//end class
