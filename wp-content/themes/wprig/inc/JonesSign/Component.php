<?php
/**
 * WP_Rig\WP_Rig\JonesSign\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\JonesSign;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\TaxonomyGlobal\Component as Taxonomies;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function get_terms;

use function add_filter;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_enqueue_script;
use function wp_localize_script;

/**
 * TABLE OF CONTENTS.
 * === Properties Defined.
 * $slug
 * $about_jones -- Boilerplate
 * $leave_out -- What locations to leave out - currently 4, 75 Denver.
 * $default_jones_image_id 101.
 * $default_jones_url - not a location - the overall jonessign.com
 * $full_company_name  Defined and assigned to COMPANY in the config.
 * $slogan Company slogan.
 * $facebook Company facebook url.
 * $twitter_url Company twitter url.
 * $twitter_handle Company twitter handle (@jonessign.).
 * $linkedin Company linkedin URL.
 * === Methods.
 * get_slug()
 * intialize()
 * template_tags() - The tags to expose for use within the template.
 * get_location_info( $term_id ) Single location information array.
 * get_term_by_blog() -- TODO -- Take a look at this. Is it being utilized?
 * get_city_image_by_blog( $term_id, $return as url ) The location term image as id, $return as url to true if you want the url.
 * get_blend_modes() Output array of blend modes.
 * get_locations()
 * get_location_taxonomy() - Pretty much the same as get_jones_locations and get_locations.
 * get_location_ids() - Get all the location term ids.
 * get_location_links( $except ) Get the locations other than the ones you don't want.
 * create_location_taxonomy_extra_fields()
 * set_admin_columns( $columns )
 * set_data_for_custom_admin_columns()
 * make_columns_sortable( $columns ) - Which columns should be sortable on backend.
 * get_location_address( $term_id ) - @todo might be better suited to call this get_location_address() -- where is it used?
 * get_location_url( $term_id )  - Get the Nimble URL.
 * get_location_description( $term_id ) Description.
 * get_location_name( $term_id ) Name of taxonomy term.
 * get_location_capability( $term_id )
 * get_location_image( $term_id ) Image of the location. Outputs an id.
 * get_location_city_photo( $term_id ) Output image of city of location as id.
 * action_enqueue_locations_script() Ouput all location information to a javascript file as the variable 'jonessignInfo'.
 * get_location_schema( $location ) Rich snippet for google visibility.
 */

/**
 * Class to utilize the location taxonomy.
 *
 * @see WP_Rig\Wp_Rig\Global_taxonomies\Component to find where the taxonomy is created and added to WordPress.
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
	private $slug = 'location';

	/**
	 * An array wherein the blog ids are the value and the term_id is the key.
	 *
	 * @see Function array_flip() will flip the keys and values.
	 * @link https://www.php.net/manual/en/function.array-flip.php
	 *
	 * @access   private
	 * @var      array    $blogs_and_terms An array wherein the blog ids are the value and the term_id is the key.
	 */
	public $blogs_and_terms = [
		60 => 2,
		61 => 11,
		62 => 9,
		63 => 14,
		64 => 3,
		65 => 15,
		66 => 13,
		67 => 12,
		68 => 10,
		69 => 6,
		70 => 5,
		72 => 1,
		73 => 7,
		74 => 8,
		75 => 4,
	];

	/**
	 * The standard about us copy for Jones Sign Company.
	 *
	 * @access   public
	 * @var      string    $about_jones The standard about us copy for Jones Sign Company.
	 */
	public $about_jones = ABOUT_US;

	/**
	 * The ids for the often left out jones sign company locations.
	 *
	 * @access   public
	 * @var      string    $key is blog number and $value is the taxonomy term id.
	 */
	public $leave_out = [
		4 => 75,
	];

	/**
	 * The default location image id of Jones Sign Company .
	 *
	 * @access   public
	 * @var      int    $default_jones_image_id The ID of the default image for Jones Sign Company in the database.
	 */
	public $default_jones_image_id = 101;

	/**
	 * The URL of the primary Jones Sign Company Website.
	 *
	 * @access   public
	 * @var      string    $default_jones_url The url for Jones Sign Company.
	 */
	public $default_jones_url = WP_HOME;

	/**
	 * The full company name.
	 *
	 * @access   public
	 * @var      string  $full_company_name The url for Jones Sign Company.
	 */
	public $full_company_name = COMPANY;

	/**
	 * The Jones Sign Company slogan.
	 *
	 * @access   public
	 * @var      string  $slogan The Jones Sign Company slogan.
	 */
	public $slogan = SLOGAN;

	/**
	 * The Jones Sign Company facebook page url.
	 *
	 * @access   public
	 * @var      string    $facebook The Jones Sign Company facebook page url.
	 */
	public $facebook = FACEBOOK_URL;

	/**
	 * The Jones Sign Company twitter url.
	 *
	 * @access   public
	 * @var      string    $twitter_url The Jones Sign Company twitter url.
	 */
	public $twitter_url = TWITTER_URL;

	/**
	 * The Jones Sign Company twitter handle.
	 *
	 * @access   public
	 * @var      string    $twitter_handle The Jones Sign Company twitter handle.
	 */
	public $twitter_handle = TWITTER_HANDLE;

	/**
	 * The Jones Sign Company linkedIn url.
	 *
	 * @access   public
	 * @var      string    $slogan The Jones Sign Company linkedIn url.
	 */
	public $linkedin = LINKEDIN_URL;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'location';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'cmb2_init', [ $this, 'create_location_taxonomy_extra_fields' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_locations_script' ] );
		// Admin set post columns - put additional columns into the admin end for the location taxonomy.
		add_filter( 'manage_edit-' . $this->slug . '_columns', [ $this, 'set_admin_columns' ], 10, 1 );
		add_filter( 'manage_edit-' . $this->slug . '_sortable_columns', [ $this, 'make_columns_sortable' ], 10, 1 );
		add_filter( 'manage_' . $this->slug . '_custom_column', [ $this, 'set_data_for_custom_admin_columns' ], 10, 3 );
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
			'get_location_links'          => [ $this, 'get_location_links' ],
			'get_locations'               => [ $this, 'get_locations' ],
			'get_location_ids'            => [ $this, 'get_location_ids' ],
			'get_location_name'           => [ $this, 'get_location_name' ],
			'get_jones_locations'         => [ $this, 'get_jones_locations' ],
			'get_blend_modes'             => [ $this, 'get_blend_modes' ],
			'get_location_taxonomy'       => [ $this, 'get_location_taxonomy' ],
			'get_location_url'            => [ $this, 'get_location_url' ],
			'get_location_capability'     => [ $this, 'get_location_capability' ],
			'get_location_info'           => [ $this, 'get_location_info' ],
			'get_capability_icons'        => [ $this, 'get_capability_icons' ],
			'get_terms_blogs_array'       => [ $this, 'get_terms_blogs_array' ],
			'get_single_location_link'    => [ $this, 'get_single_location_link' ],
			'get_location_schema'         => [ $this, 'get_location_schema' ],
			'get_single_location_details' => [ $this, 'get_single_location_details' ],
			'get_location_option'         => [ $this, 'get_location_option' ],
			'get_single_location_address' => [ $this, 'get_single_location_address' ],
			'get_single_mapped_location'  => [ $this, 'get_single_mapped_location' ],
		];
	}

	/**
	 * Query for all the data and metadata assigned to any of the location taxonomy.
	 *
	 * @param int $term_id The id for the taxonomy term.
	 */
	public function get_location_info( $term_id ) {
		global $blog_id;
		$info                      = [];
		$location                  = get_term( $term_id );
		$info['id']                = get_term( $term_id )->term_id;
		$info['name']              = get_term( $term_id )->name;
		$info['slug']              = get_term( $term_id )->slug;
		$info['tax_id']            = get_term( $term_id )->term_taxonomy_id;
		$info['description']       = get_term( $term_id )->description;
		$info['common']            = $this->get_location_name( $location->term_id );
		$info['location_image_id'] = $this->get_location_image( $location->term_id );
		$info['city_image_id']     = $this->get_city_image( $location->term_id );
		$info['blog_id']           = get_term_meta( $term_id, 'locationBlogID', true );
		$info['indepth']           = get_term_meta( $term_id, 'locationinDepth', true ) || '';
		$info['subdomain']         = preg_replace( '/^http:/i', 'https:', $this->get_location_url( $location->term_id, false ) );
		$info['nimble']            = preg_replace( '/^http:/i', 'https:', $this->get_location_url( $location->term_id, true ) );
		$info['address']           = $this->get_location_address( $term_id );
		$info['capabilities']      = $this->get_location_capability( $term_id );
		return $info;
	}

	/**
	 * Output the locations array.
	 */
	public function get_jones_locations() {
		return Taxonomies::get_all_term_ids_from_slug( 'location' );
	}

	/**
	 * Gets the location terms and blog ids as an associative array.
	 *
	 * @param string $key Which item should be the key within the associtive arra, if 'term' or 'terms' then the term_id will be key - anything else and it will be the blog_id.
	 *
	 * @return array The associtive array of term_ids and blog_ids.
	 */
	public function get_terms_blogs_array( $key = 'term' ) : array {
		return in_array( $key, [ 'terms', 'term' ], true ) ? $this->blogs_and_terms : array_flip( $this->blogs_and_terms );
	}

	/**
	 * Get the city image based on the blog id.
	 *
	 * @param int  $blog The blog id. Default is 1 - which is the Jones Sign Company Blog.
	 * @param bool $return_as_url Whether to return the url of the image. Defaults to false.
	 * @return mixed Either the id of the image or the url of the image -- depending on the $return_as_url parameter.
	 */
	public function get_city_image_by_blog( $blog = 1, $return_as_url = false ) {
		$key             = false === $return_as_url ? 'locationCityImage_id' : 'locationCityImage';
		$term_identifier = $this->get_terms_blogs_array( 'blog' )[ $blog ];
		return get_term_meta( $term_identifier, $key, true );
	}

	/**
	 * Get the Jones Sign location image id based on the blog id.
	 *
	 * @param int  $blog The blog id. Default is 1 - which is the Jones Sign Company Blog.
	 * @param bool $return_as_url Whether to return the url of the image. Defaults to false.
	 * @return int $city_image_id The city image ID from the jco_termmeta table. Defaults to 1.
	 */
	public function get_location_image_by_blog( $blog = 1, $return_as_url = false ) {
		$key             = false === $return_as_url ? 'locationImage_id' : 'locationImage';
		$term_identifier = $this->get_terms_blogs_array( 'blog' )[ $blog ];
		return get_term_meta( $term_identifier, $key, true );
	}

	/**
	 * Get both the city image and the location image and output as an array with the keys 'city', and 'location'.
	 *
	 * @param int  $blog The blog id. Default is 1 - which is the Jones Sign Company Blog.
	 * @param bool $return_as_url Whether to return the url of the image. Defaults to false.
	 * @return array Associative array of either the id or the url of the location's attached images.
	 */
	public function get_images_array_by_blog( $blog = 1, $return_as_url = false ) {
		$output             = [];
		$output['city']     = get_city_image_by_blog( $blog, $return_as_url );
		$output['location'] = get_location_image_by_blog( $blog, $return_as_url );
		return $output;
	}

	/**
	 * Gets the css options for Background Blend Mode as an array;
	 */
	public function get_blend_modes() {
		$modes = [ 'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity' ];
		return $modes;
	}
	//phpcs:enable

	/**
	 * Gets the locations array.
	 *
	 * @return string Component slug.
	 */
	public function get_locations() : array {
		return $this->get_location_taxonomy();
	}

	/**
	 * Grab all the basic information [term_id, slug, description] for all of the locations within the 'location' taxonomy.
	 *
	 * @param bool $hide_empty Whether to hide the taxonomy terms that have no attachment or post assigned in the backend.
	 */
	public function get_location_taxonomy( $hide_empty = false ) : array {
		return Taxonomies::get_all_terms_in_taxonomy( 'location', $hide_empty );
	}

	/**
	 * Get all location term identifiers.
	 *
	 * @param int ...$except The term_ids of the location that I don't want to include. can add several, they will all become an array using the splat / spread operator(...).
	 *
	 * @link https://www.php.net/manual/en/migration56.new-features.php
	 */
	public function get_location_ids( ...$except ) : array {
		return array_diff( Taxonomies::get_all_term_ids_from_slug( 'location' ), $except );
	}

	/**
	 * Get a single location link.
	 *
	 * @param int $term_id The term id of the location.
	 *
	 * @return string $output An HTML string of a given link.
	 */
	public function get_single_location_link( $term_id ) : string {
		[
			'id'                => $id,
			'name'              => $name,
			'slug'              => $slug,
			'blog_id'           => $blog,
			'description'       => $description,
			'location_image_id' => $location_image,
			'city_image_id'     => $city_image,
			'subdomain'         => $subdomain,
			'nimble'            => $nimble,
			'address'           => $address,
			'capabilities'      => $capabilities,
		]        = wp_rig()->get_location_info( $term_id );
		$output  = '';
		$output .= wp_sprintf( '<a class="location_link" title="%s" data-blog-identifier="%s" href="%s">%s</a>', $description, $blog, $subdomain, ucwords( explode( ' ', $name, 2 )[1] ) );
		return $output;
	}

	/**
	 * Get a single location address as an html element.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_location_option( int $term_id ) {
		[
			'id'                => $id,
			'name'              => $name,
			'slug'              => $slug,
			'blog_id'           => $blog,
			'description'       => $description,
			'location_image_id' => $location_image,
			'city_image_id'     => $city_image,
			'subdomain'         => $subdomain,
			'nimble'            => $nimble,
			'address'           => $address,
			'capabilities'      => $capabilities,
		]         = wp_rig()->get_location_info( $term_id );
		$name     = 72 === $id ? 'Jones Sign Company' : $name;
		$selected = selected( $id, 72, false );
		$selected = '';
		$output   = wp_sprintf( '<option data-value="%s" value="%s" data-location-id="%d"%s>%s</option>', $slug, $slug, $id, $selected, ucwords( $name ) );
		return $output;
	}

	/**
	 * Get a single location address as an html element.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_single_location_address( int $term_id ) {
		[
			'id'           => $id,
			'name'         => $name,
			'slug'         => $slug,
			'blog_id'      => $blog,
			'address'      => $address,
			'capabilities' => $capabilities,
		]        = wp_rig()->get_location_info( $term_id );
		$output  = '';
		$output  = '<address>';
		$output .= "\n\t\t\t\t\t";
		$output .= wp_sprintf( '<span itemprop="streetAddress">%s </span>', $address['address'] );
		$output .= "\n\t\t\t\t\t";
		$output .= wp_sprintf( '<span itemprop="addressLocality">%s</span>, ', $address['city'] );
		$output .= "\n\t\t\t\t\t";
		$output .= wp_sprintf( '<span itemprop="addressRegion">%s</span>', $address['state'] );
		$output .= "\n\t\t\t\t\t";
		$output .= wp_sprintf( '<span itemprop="postalCode"> %s</span>', $address['zip'] );
		$output .= "\n\t\t\t\t\t";
		$output .= wp_sprintf( '<a href="tel:+1-%s" itemprop="telephone">%s</a>', $address['phone'], $address['phone'] );
		$output .= "\n\t\t\t\t";
		$output .= '</address>';
		$output .= "\n\t\t\t\t";

		return $output;
	}

	/**
	 * Get a single locations details and output as html.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_single_location_details( int $term_id ) {
		[
			'id'                => $id,
			'name'              => $name,
			'slug'              => $slug,
			'blog_id'           => $blog,
			'description'       => $description,
			'location_image_id' => $location_image,
			'city_image_id'     => $city_image,
			'subdomain'         => $subdomain,
			'nimble'            => $nimble,
			'address'           => $address,
			'capabilities'      => $capabilities,
		]        = wp_rig()->get_location_info( $term_id );
		$output  = '';
		$output .= wp_sprintf( '<div class="location_details" title="%s" data-show-location="%s" data-location-slug="%s" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">', $description, $blog, $slug );
		$output .= "\n\t";
		$output .= wp_sprintf( '<h2>%s</h2>', ucwords( $name ) );
		$output .= "\n\t";
		$output .= $this->get_single_location_address( $term_id );
		$output .= "\n\t";
		$output .= wp_sprintf( '<a href="tel:+1-%s" itemprop="telephone">%s</a>', $address['phone'], $address['phone'] );
		$output .= "\n";
		$output .= '</div><!-- end div.location_details -->';
		$output .= "\n";
		return $output;
	}

	/**
	 * Get a single locations details and output as html.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_single_mapped_location( int $term_id ) {
		[
			'id'                => $id,
			'name'              => $name,
			'slug'              => $slug,
			'blog_id'           => $blog,
			'description'       => $description,
			'location_image_id' => $location_image,
			'city_image_id'     => $city_image,
			'subdomain'         => $subdomain,
			'nimble'            => $nimble,
			'address'           => $address,
			'capabilities'      => $capabilities,
		]        = wp_rig()->get_location_info( $term_id );
		$output  = '';
		$output .= wp_sprintf( '<li class="map-marker %s map-marker-%s">', $slug, $slug );
		$output .= "\n\t";
		$output .= wp_sprintf( '<a href="#">%s</a>', $name );
		$output .= "\n\t";
		$output .= '<div class="map-marker-info">';
		$output .= "\n\t\t";
		$output .= '<div class="map-marker-info-inner animate-bounce-in">';
		$output .= "\n\t\t\t";
		$output .= wp_sprintf( '<div class="header"><h2>%s</h2></div>', ucwords( $name ) );
		$output .= "\n\t\t\t";
		$output .= '<main>';
		$output .= "\n\t\t\t\t";
		$output .= $this->get_single_location_address( $term_id );
		$output .= '</main>';
		$output .= "\n\t\t";
		$output .= '</div><!-- end div.map-marker-info-inner animate-bounce-in -->';
		$output .= "\n\t";
		$output .= '</div><!-- end div.map-marker-info -->';
		$output .= "\n";
		$output .= wp_sprintf( '</li><!-- end li.map-marker-%s -->', $slug );
		$output .= "\n";
		return $output;
	}

	/**
	 * Get the map of all the locations.
	 */
	public function get_locations_mapped() {
		$locations = $this->get_location_ids( 75, 72 );
		$total     = count( $locations );
		$output    = '
		<div id="jones_locations_svg_map_container">
			<div>
				<h2>Jones Sign Company</h2>
				<h3>' . $total . ' Locations across the United States</h3>
			</div>
			<div class="map">
				<ul class="map-markers">';
		foreach ( $locations as $location ) {
			$output .= $this->get_single_mapped_location( $location ) . "\n";
		}
		$output .= '
				</ul>
					<svg
					xmlns="http://www.w3.org/2000/svg"
					xmlns:xlink="http://www.w3.org/1999/xlink"
					version="1.1"
					id="usa"
					viewBox="0 0 612 378.2">
						<g>
							<path class="state" id="alabama" d="M423 307.7c0.6-2.1 0.8-4 0.3-4.6 -0.2-0.2-0.5-0.5-0.7-0.8 -1.6-1.6-3.5-3.6-3-5.2 0.2-0.7 0.9-1.2 1.9-1.4 3.6-0.8 26.5-2.3 31.2-2.6 -0.8-1.6-1.6-3.4-1.6-4.5 0-2.7-0.6-5.9-0.8-6.9 -0.2-0.9 0.2-2.1 0.7-3.4 0.5-1.4 1-3.1 0.6-3.9 -0.9-1.7-6.2-14.3-8.9-23.7 -2.2-7.8-3.9-13.3-4.4-14.8l-29.7 2.6 0.4 48.9c0.1 0.6 2.5 17.3 2.5 20.1 1.4 0 2.7 0 3.2 0.1 0.8 0.2 5.3 1.3 7.8 0.3C422.6 307.9 422.8 307.8 423 307.7z"/>
							<path class="state" id="arizona" d="M160.3 205.1l-57.2-8.6c-0.6 2.9-2.7 12.3-4.2 13.4 -1.1 0.7-2-0.2-2.6-1 -0.7-0.8-1.5-1.7-2.8-1.7 -0.1 0-0.2 0-0.4 0.2 -1.4 1.5-1 8.8-0.8 11.3 0 0.6 0.1 1 0.1 1.2 0 0.6-0.2 1.3-0.5 2.1 -0.5 1.3-1 2.7-0.7 4.2 0.2 0.8 0.6 2 1 3.3 1.1 3.3 1.8 5.6 1.2 6.6 -0.3 0.5-0.9 1.1-1.6 1.9 -1.2 1.3-2.8 3-2.8 4 0 1.3-1.1 2.9-2.1 4.5 -0.5 0.7-0.9 1.4-1.2 1.9 -0.4 0.9 0.2 2.9 0.6 4.5 0.3 1.1 0.6 2 0.6 2.6 0 1.1-2.4 2.6-4.7 3.7 9.8 5.9 40.7 25.2 42.7 25.7 1.4 0.4 15 2 25.2 3.1L160.3 205.1z"/>
							<path class="state" id="arkansas" d="M379.8 242.5c0.1-0.4 0.6-1 1.4-2.2 1.1-1.5 2.7-3.7 2.7-4.8 0-1 1-2.9 2.1-5 0.8-1.6 2.2-4.2 2-4.7 -0.3-0.3-2.1-0.1-3 0 -2.2 0.2-4.2 0.4-4.4-1 -0.1-0.9 0.6-1.8 1.4-2.8 1.2-1.4 1.7-2.1 1-2.8 -1.8-1.2-33 0.1-52.6 1.2 0.5 2.6 2.2 12.8 2.4 15.1 0.2 2.3 0.2 23.1 0.3 27 0.3 0.1 0.4 0.2 0.5 0.3l4.9 1 0 0.4c0 2.3-0.1 5-0.1 7.7l35.4-1.8c-0.3-1.4-0.5-2.7-0.6-3.8 -0.4-4.2-0.3-9.9 2.1-13.3C377.4 250.1 379.5 243.4 379.8 242.5z"/>
							<path class="state" id="california" d="M85.9 245.9c0.9-1.4 1.9-2.9 1.9-3.9 0-1.3 1.6-3.1 3-4.7 0.6-0.7 1.2-1.3 1.5-1.8 0.4-0.7-0.6-3.7-1.3-5.8 -0.4-1.3-0.9-2.5-1-3.5 -0.2-0.9-0.1-1.8 0.1-2.6l-49.3-71.1 0.1-0.2 10-43.6c-18.2-4.5-35.3-9.1-43.9-11.4 0.2 2.6 0.3 6.6-1.3 10.9 -2.1 5.6-5.9 8.3-5.9 11.3 0 3.1 3.2 6.2 3 10S0 141.1 0.7 143s4.8 8.4 4.6 9.5c-0.2 1.1-1 3.5 0.7 5.5 1.7 2 3.1 3.3 3 6.4s-1 6 0.2 8.4c1.1 2.5 5.5 3.1 4.6 5.6 -0.9 2.6-3.3 1.8-3 5.6 0.3 3.8 3 7.9 4.3 10.8 1.3 2.9 4.7 8.2 4.9 11.9s-1.8 6.4-0.5 8.4c1.4 2 10.1 3.5 12.1 5.6 2 2.1 1.9 4.2 3.9 5.4 1.9 1.2 5.9 1.2 6.5 2.1 0.6 0.9-1.3 3.4 0.6 4.6 1.9 1.2 3.7 1.3 7.1 5.7 3.4 4.4 3 12.9 3.5 14.5 0.6 1.6 24.2 3.3 26.1 4.3 0.2 0.1 0.8 0.5 1.8 1.1 2.7-1.3 4.6-2.6 4.7-3.1 0-0.5-0.3-1.4-0.6-2.3 -0.6-1.9-1.2-4-0.6-5.2C84.9 247.3 85.4 246.6 85.9 245.9z"/>
							<path class="state" id="colorado" d="M242.6 211.5l1.8-43.8h0l0-0.5v0l0.7-14.6 -21.9-1.8c-0.1 0-13.9-0.2-38.1-3 -6.2-0.7-11.7-1.4-16.6-2l-7.1 58.5 70 6.9C231.7 211.1 236.2 211.3 242.6 211.5z"/>
							<path class="state" id="connecticut" d="M574 103.1c-4.5 0.8-11 2.2-17.2 3.7 0 0.3 0 0.5 0 0.6 -0.1 0.5 0.6 2.5 1.1 4.1 1 3 1.5 4.8 1.2 5.6 -0.4 1-0.5 2.1-0.6 2.9l-0.1 0.5c0 0.2 0.7 0.8 1.4 1.2l3.3-1.1c0.5-0.8 1.4-1.8 2.2-2.5 1.8-1.6 4.6-3.3 10.9-5.5L574 103.1z"/>
							<path class="state" id="delaware" d="M544.5 143.4c-3.2 1.5-3.6 2.3-3.6 2.4 0 0.1-0.1 0.2-1 0.6l5 17.3 7.8-1.8c-0.1-0.3-0.2-0.6-0.3-1 -0.9-2.2-5.4-6.4-5.6-7.2C546.6 152.8 544.5 143.4 544.5 143.4z"/>
							<path class="state" id="florida" d="M516.3 326.6c-0.1-1.8 0.5-4.3-1.5-7.2 -2-2.9-9-10.3-11.3-15.5 -1.6-3.5-3.8-7.8-4.8-12 -3.3-0.6-6.2-1-6.9-0.9 -0.1 0.4 0.1 1.7 0.3 2.5 0.3 2.1 0.6 3.8-0.5 4.1 -0.8 0.2-1.3-0.3-1.7-0.7 -0.4-0.4-0.7-0.7-1.3-0.7 -1.2 0-22.1 0.9-33 1.5l-0.3 0 -0.2-0.2c-0.1-0.1-1-1.6-2-3.3 -3.1 0.2-27.9 1.9-31.5 2.7 -0.5 0.1-1.1 0.3-1.2 0.7 -0.3 1 1.6 3 2.8 4.2 0.3 0.3 0.5 0.5 0.7 0.8 0.8 0.9 0.7 2.8 0.2 4.8 3.3-1.4 9-3.7 13.3-2.8 5.2 1.1 13.2 9 14.5 9 1.3 0 5.6-1.6 7.7-3 2-1.4 4.8-5.2 7-5.3 2.3-0.1 12.7 10.9 14.8 11.4 2.1 0.5 4.5 0.2 5.7 2.3 1.2 2.1-0.6 16.6 0.1 17.7 0.7 1.1 6.6 9 11.8 14.8 5.2 5.8 5.9 9.6 7.3 9.9 1.4 0.3 4.2 0.7 5.8 2.6 1.7 1.9 3 6.2 3 6.9 0.1 0.7 2.9 1.5 6.1 0 3.2-1.5 5.1-4 5.3-6.4 0.2-2.5 1.1-17.4 0.8-18.2C527.1 345.1 516.4 328.4 516.3 326.6z"/>
							<path class="state" id="georgia" d="M498.5 290.8c-0.3-1.6-0.4-3.2-0.2-4.7 0.8-5.9 1.7-10 4.1-14.6 0.1-0.2 0.2-0.4 0.3-0.6 -0.7 0-1.5 0-2 0 -2.4-0.1-3.1-3.1-3.4-4.6 0-0.2-0.1-0.3-0.1-0.4 -0.1-0.4-1-1.1-1.6-1.6 -1.1-0.8-1.6-1.3-1.6-1.8 -0.1-0.6-2.5-5.1-4.8-6.2 -1.3-0.6-3.4-2.8-5.6-5.1 -2.1-2.2-4.2-4.4-5.6-5.2 -1.8-1.1-3.3-2.9-4.6-4.4 -0.8-1-1.6-1.9-2.1-2.1 -1.3-0.5-4.3-1.7-5.2-4.7 -0.1-0.5-0.1-1 0.2-1.5l-27.1 2.4c0.5 1.6 2.2 7.1 4.4 14.7 2.6 9.4 7.9 21.9 8.8 23.5 0.6 1.2 0.1 2.9-0.5 4.7 -0.4 1.1-0.8 2.3-0.6 2.9 0.2 1.1 0.9 4.3 0.9 7.1 0 1.8 2.6 6.2 3.8 8 3.4-0.2 31.4-1.5 32.9-1.5 1 0.1 1.5 0.6 1.9 1 0.4 0.4 0.5 0.5 0.7 0.4 0.1-0.3-0.2-1.9-0.3-2.9 -0.3-1.8-0.4-2.8-0.1-3.3C491.2 290.1 491.7 289.5 498.5 290.8z"/>
							<path class="state" id="idaho" d="M97.7 68.7c-2.6 3.4-8 10.4-7.7 11.6 0.2 0.4 0.4 0.8 0.6 1.1 0.8 1.4 1.5 2.8 1.5 4.3 0 1.8-6.6 26.5-7.3 29.3l0 0.7c29.5 6.4 58.8 10.4 62.9 10.9l5.6-36.9c-2.9-0.1-15.4-0.7-19.2-1.6 -3-0.7-4.7-7.7-6.2-14.8 -0.4-1.7-0.7-3.4-0.9-3.7 -0.3-0.3-1.9 0.1-2.8 0.4 -0.8 0.2-1.6 0.5-2.2 0.5 -0.4 0-0.8 0-1.1-0.4 -0.4-0.5-0.3-1.1-0.1-2.8 0.1-0.7 0.2-1.7 0.3-2.8 0.4-4.2 3.3-9.8 4.2-11.4 -1.2-0.6-4.3-2.1-5.7-3.9 -0.7-0.9-1.6-2.3-2.5-3.8 -1.1-1.8-2.3-3.7-3.1-4.5 -1.1-1.1-1.4-3.9-1.7-6.8 -0.2-1.6-0.3-3-0.6-4.1 -0.5-1.9 0.2-9.2 0.8-14.2 -2.4-0.5-4.8-1-7-1.4 -0.9 5.8-3 16.3-4.6 22.2 -2 7.1-3.1 19.2-3.3 21 1 1.1 4.3 4.7 3.3 6.6C100.6 64.9 99.4 66.5 97.7 68.7z"/>
							<path class="state" id="illinois" d="M399.3 205.5c0.7 0.2 1.6 0.4 1.9 0.3 0-0.9-0.1-3 1.5-3.9 0.6-0.3 1.4-2.4 1.8-4.2l0-1.9 0-1.9c0-1.9 1.8-5 3.4-7.8 0.4-0.8 0.9-1.5 1.2-2.2 1.1-2-0.4-4.9-1.1-6.4 -0.3-0.7-0.5-1-0.5-1.3 0-0.4 0.1-0.9 0.4-1.9 0.4-1.3 0.8-3 0.7-4.1 -0.4-2.1-2.2-23.6-2.4-26.4 -0.2-2.3-0.1-4.4 0-5.9 -3.6-1.7-3.2-6.5-3.5-7.5 -0.2-0.6-0.3-2.4-0.4-4.4l-29.5 3.5c1 1.5 2.8 4 4.8 6.6 0.7 1 0.9 2 0.5 3 -1.4 3.4-8.8 5.9-9.9 6.1 -0.4 0.5 0.2 2.7 0.5 4.1 0.3 1.2 0.6 2.3 0.6 3 0 1.9-3.8 6.7-4.6 7.6 0 1-0.2 6.6 1.2 9.6 0.8 1.8 3 3.5 4.8 4.8 1.7 1.3 2.8 2.1 2.8 3 0 0.2 0 0.4 0 0.6 0 1.1-0.1 3.3 0.5 3.8 0.1 0.1 0.2 0.1 0.3 0.1 0.4-0.1 1.1-0.3 1.7-0.5 1.6-0.5 3.2-1 4.1-0.4 0.4 0.3 0.6 0.7 0.6 1.4 0 1.2-0.6 2.9-1.2 4.7 -0.6 1.7-1.4 4.1-1 4.6 0.3 0.4 2 1.6 3.7 2.8 4.6 3.1 7.8 5.5 7.8 7.1 0 0.4 0 0.9 0 1.3 -0.1 2-0.2 4.8 0.9 5.5 0.9 0.7 1.8 0.8 2.3 0.8 0 0 0-0.1 0-0.1 -0.1-0.9-0.4-2.3 1.1-3.6C395.3 204.5 397.4 205 399.3 205.5z"/>
							<path class="state" id="indiana" d="M435.5 133.7L414 136c-1.3 1.6-2.9 2.7-4.8 2.7 -0.8 0-1.6-0.1-2.2-0.3 -0.1 1.4-0.1 3.4 0 5.5 0.3 4.2 2.1 24.4 2.4 26.3 0.2 1.4-0.3 3.2-0.7 4.5 -0.2 0.6-0.4 1.3-0.3 1.5 0 0.1 0.2 0.5 0.4 0.9 0.9 1.7 2.5 4.8 1.1 7.4 -0.4 0.7-0.8 1.4-1.3 2.2 -1.5 2.5-3.3 5.7-3.3 7.3v1.3c1.1-0.6 3.4-1.7 5.9-1.7 2.1 0 3.4 0.6 4.2 0.9 0.2 0.1 0.4 0.2 0.5 0.2 0-0.1 0.1-0.3 0.1-0.4 0.2-0.8 0.6-2.2 2.5-2 1.5 0.2 4-2 4.8-2.9l0.2-0.2 4.8 1.2c1.5-1.7 4.8-5.7 5.1-7.5 0.4-2.3 0-3.9 0-3.9l-0.2-0.7 0.7 0c3.7 0.2 7.2-0.1 7.6-0.6 -0.1-0.1-0.4-0.5-1-4.5C439 162.8 436 137.6 435.5 133.7z"/>
							<path class="state" id="iowa" d="M308.2 116.8h-1.8c0.2 1.6 0.7 6 0.7 6.5 0 0.6-0.9 4.6-1.2 5.8 0.7 1.1 4.4 7.1 5.3 9.8 0.9 2.8 4.3 17.6 4.9 20.4l45.1-2.3 2.8 2.1c1.7-2.1 4.2-5.6 4.2-6.7 0-0.6-0.3-1.7-0.6-2.8 -0.6-2.4-1-4-0.4-4.9 0.2-0.3 0.4-0.4 0.7-0.5 1.3-0.2 8-2.6 9.1-5.4 0.3-0.7 0.2-1.4-0.4-2 -2.9-3.7-5.2-7.2-5.7-8 -1.4-0.5-5-1.9-5.2-3.4 -0.2-1.7-1.4-9-1.9-9.7 -0.2-0.2-0.3-0.5-0.4-1.1L308.2 116.8 308.2 116.8z"/>
							<path class="state" id="kansas" d="M283.8 212.8h45.1l-0.8-30.9c-2.6-2.2-6.5-6.1-5.4-7.6 1-1.3 2-3.4 2-4 -1-0.3-4.5-1.4-5.1-1.6l-74.3-1 -1.8 43.8C258.1 212 280.8 212.8 283.8 212.8z"/>
							<path class="state" id="kentucky" d="M434.8 179.3c0.1 0.7 0.2 2-0.1 3.7 -0.5 2.6-5.4 8-5.6 8.2l-0.2 0.2 -4.8-1.2c-0.8 0.8-3.5 3.2-5.5 3 -1-0.1-1.2 0.4-1.4 1.3 -0.1 0.3-0.2 0.7-0.4 0.9 -0.4 0.5-1 0.3-1.6 0 -0.7-0.3-1.9-0.8-3.8-0.8 -2 0-3.9 0.8-5 1.3l-0.9 0.7v1.3c-0.2 0.8-1.1 4.3-2.3 5 -0.7 0.4-1 1.4-0.9 3 0 0.3-0.1 0.5-0.3 0.7 -0.5 0.4-1.5 0.2-2.9-0.1 -1.4-0.3-3.4-0.8-4.1-0.2 -1 1-0.9 2-0.8 2.7 0 0.3 0.1 0.6 0.1 0.8 0 0.1-0.1 0.4-0.1 0.7 -0.6 2.6-0.7 4.3-0.6 5 0.1 0.1 0.3 0.3 0.4 0.4 4.6 0.3 11.4 0.3 12.4-0.2 0.1-0.3 0.1-0.7 0.1-1.1 0-0.9 0.1-2.2 1.2-2.2 1.1 0 38.2-3.6 50-4.8 1-0.6 8.1-5.4 10.5-8.5 2.2-2.8 5.2-5.9 6.3-7 -0.6-0.6-1.2-1.1-1.8-1.6 -1-0.9-2-1.8-3.4-2.9 -2.2-1.7-2.5-5.9-2.6-6.8 -0.8-0.5-1.5-1.4-2.3-2.4 -0.9-1.1-1.9-2.5-2.6-2.3 -0.6 0.1-1.1 0.5-1.7 1 -1 0.8-2.3 1.7-4.4 1.5 -2.5-0.2-4.6-1-5.4-1.3 -0.3 0.2-1 0.5-1.7 0.3 -0.8-0.2-1.4-0.8-1.8-1.9 -0.3-0.7-0.5-1.2-0.6-1.7 -0.5-1.6-0.6-1.8-2.2-1.4l-2.3 0.5c0.5 3 0.7 3.6 0.8 3.7 0.3 0.5 0.2 0.8 0 1C441.9 179.4 437.3 179.4 434.8 179.3z"/>
							<path class="state" id="louisiana" d="M404.6 324.3c-4.1-1.8-8.1-3.6-7-5 1.1-1.4 2.8-5 1.9-6.3 -0.1-0.2-0.2-0.3-0.4-0.5 -0.3 0.1-0.6 0.1-0.9 0.1 -0.7-0.1-1.4-0.4-1.9-1.1l-0.7-0.8c-1.6-2-3.5-4.3-3.5-6.1 0-1.4 0-3.8 0.1-4.8l-23.4 1.4 0.1-0.6c0.1-0.4 1.7-9.5 3.6-12.7 0.4-0.7 0.8-1.3 1.2-1.9 1.4-2.2 2.6-4.1 2.3-5.2l-0.2-0.9c-0.5-1.8-1.4-5.2-2-8.5l-35.6 1.8c0 6.5 0 12.9 0.4 13.6 0.6 0.6 3.1 4.2 3.3 6.3 0.1 0.8 1.1 2.1 2 3.3 1.1 1.4 2.1 2.6 2.1 3.6 0 1-0.4 2.6-0.8 4.4 -0.3 1.4-0.7 2.9-1 4.4 -0.2 1.2-0.4 2.6-0.5 3.9 -0.3 2.7-0.6 5.3-1.2 6.3 -0.3 0.4-0.4 1.3-0.5 2.3 0.1 0 0.2-0.1 0.3-0.1 3.9-0.9 6.3-1 9.6-0.3 3.4 0.7 7.3 2.2 9.9 2.1s2.2-2.7 4.4-3.4 5.3 1.4 6.8 3.1c1.5 1.7 4.6 5.2 7.4 5.5s3.3-4.2 6.8-2.3c3.5 1.9 5 1.8 5-0.8 0-2.6 5.8 0.2 8.3 2.2C403.2 329 408.7 326.2 404.6 324.3z"/>
							<path class="state" id="maine" d="M612.3 48.7c-0.4-2-3.9-4.8-5.3-5.6 -1.5-0.9-0.2-2.7-1.7-4.2s-4.4-0.7-4.9-1.8c-0.5-1.1-2.5-9.5-4.2-13.6 -1.7-4.1-4.6-7.1-6.7-7.1 -2.1 0-4.4 4-6 3.5 -1.5-0.5-1.8-1.7-2.7-2.5s-2 0-2.9 2.4c-0.9 2.4-3 7.1-3.4 10.4 -0.4 3.3 0.1 10.5-0.3 12.9 -0.5 2.4-2.4 7.4-3.4 8.5 -0.2 0.3-0.6 0.5-0.9 0.8 0.4 1.8 0.9 3.9 1.6 6.1 0.4 1.4 1 3 1.5 4.8 2.1 6.5 4.4 13.8 4.7 15.2 0.3 1.8 2.2 4.7 5 5.2 0 0 0.1 0 0.1 0 1-2.2 2.2-5.1 2.1-6.4 -0.2-1.8-0.4-4.7 0.5-5.1 0.9-0.4 8.1-3.7 9-6.1 0.9-2.4 0-6.7 1.5-6.5 1.4 0.3 5.4 1.4 7.1-0.9s3.2-4.1 5.3-6C610.5 51.1 612.7 50.7 612.3 48.7z"/>
							<path class="state" id="maryland" d="M538.9 146.7c-3.5 1-13 3.1-39.3 8.1l0 1.8c0 1.6 0.3 4.4 0.6 5.1 0.1 0.1 0.6 0.1 2.1-2 1.1-1.5 7-4.8 8.1-4.8 0.3 0 0.6 0.2 1 0.5l0.1-0.3c0.1 0 3.7 1.1 6.1 3 0.7 0.5 0.9 0.5 0.9 0.4 0.2-0.1 0.4-0.8 0.5-1.3 0.1-0.6 0.3-1.2 0.5-1.6 0.1-0.3 0.4-0.4 0.7-0.5 0.9-0.2 2.1 0.6 4.1 1.8 1.3 0.8 2.6 1.7 3.6 2 0.9 0.2 1.4 0.6 1.7 1.2 0.6 1.3-0.4 3.2-1.9 6 -0.8 1.6-0.8 2-0.7 2.1 0.1 0.2 0.7 0.3 1.3 0.3 0.4 0.1 1 0.1 1.5 0.2 1.6 0.4 6.1 1.7 9.1 2.7 0.1-0.9 0.5-1.5 1.2-1.9 1.7-0.9 5.3 0.8 5.3 2.1 0 1.3 0 11.2 0.6 12 0.5 0.6 3.3 0.9 3.5 0 0.3-1.4-1.2-4.1-1.1-4.9 0.2-0.9 1.4-4.1 1.7-5.2 0.3-1.1 2.5-3.6 2.7-6.4 0.1-1.9 0.4-3.1 0.2-4.4l-8.6 2L538.9 146.7z"/>
							<path class="state" id="massachusetts" d="M589.5 96.1c-1.2 1.1 2.7 4-0.4 4.6 -1.5 0.3-2.9-3.8-4-4.4s-3.6-1.2-2.1-3c1.5-1.8 2.7-2.9 1.9-3.5 -0.6-0.4-2.2-1.1-2.9-2.1 -0.4 0.2-0.9 0.5-1.3 0.9 -3 2.6-4 2.9-10.6 4.3 -5.7 1.3-12.3 2.9-14 3.3 0.3 3.6 0.5 7.4 0.6 9.6 5.8-1.4 21.6-5 23.2-4.1 1 0.6 2.3 4.3 3.2 7.6 2.1-1.9 4.7-4.6 5.5-4.8 2-0.5 4.2-2.4 4.5-4.2C593.8 97.3 590.4 95.2 589.5 96.1z"/>
							<g class="state" id="michigan">
								<path d="M428.8 74.2c-1 0.1-4.1 3.2-3.8 3.6 0.3 0.4 0.8 2 0.1 2.7 -0.7 0.7-8.6 6.8-9.1 9.7 -0.5 2.9-0.7 6.2-1.2 7.4 -0.5 1.2-2 4.9-1.6 6 0.4 1.2-0.3 5.4 2 8.9 2.3 3.5 2.9 5.8 3.3 8.8 0.3 2-0.8 9-3.6 13.4l37-4.4c0.3-2.1 0.8-5.4 1.9-6.8 1.4-1.8 1.5-4.6 3.3-5 1.8-0.4 2.7-3 2-5.9 -0.7-2.9-1.4-4.4-2.8-8.7 -1.5-4.3-1.1-6.4-3.2-6.8 -2.1-0.4-6.6 6.3-8.2 6.5s-5.5 0.9-3.1-3.4c2.4-4.2 4.1-4.7 4.4-6.9 0.3-2.2 0.2-6.3-0.8-7.4 -0.9-1.1-1.1-1.6-1-2.7 0.1-1-1.4-4.3-5.4-5.6C435 76.5 429.8 74.1 428.8 74.2z"/>
								<path d="M370.5 67c0.6 0.9 1.1 1.7 1.6 2.1 2.3 2.1 10.5 4.6 13.7 4.9 3.4 0.4 9 2.3 10.2 4.6 0.4 0.8 0.7 1.7 1 2.8 0.6 1.7 1.2 3.6 2.2 4.7 0.4 0.4 0.8 0.6 1.3 0.5 1.1-0.1 2.4-1.4 3.1-2.6 0.6-0.9 1.8-1.1 3.1-1.1 0.4-1.7 1.2-3.7 2.9-5.6 2.9-3.1 7.1-4.8 9-5.5 1.9-0.7 6.5-0.3 8 0.2 1.5 0.6 1.5-1.3 3.6-1.3 2.2 0 9.2 1.1 9.7 0 0.5-1.1 2.1-2.7 0.7-3.4 -1.4-0.7-2.1 1.2-2.4 1.3 -0.3 0.1-1.9 0.5-3.1-1.1 -1.2-1.5-1.8-4.8-2.5-4.8 -0.7 0-7.7 0.6-7.7 0.6s0.4-3.5-0.5-4c-0.9-0.6-4.6 0.6-5.3 1.2 -0.7 0.6-5 0.8-5.9 1.4 -0.9 0.6-3 2.9-5 3.8 -2.1 0.9-3.1-0.5-3.8-0.4 -0.7 0-2.3 0.6-3.2 0.4 -0.9-0.2-1.8-3.7-4.1-4.3 -2.3-0.6-5.3-1.1-6.2-0.7s-1-2.7 0.9-4.2c1.8-1.6 4.8-3.8 3.9-4.4 -0.9-0.6-4.2-0.2-5.7 1.5 -1.5 1.7-5 5.3-7.2 7s-6.3 2.3-7.5 3C374.4 64.5 372.4 66 370.5 67z"/>
							</g>
							<path class="state" id="minnesota" d="M299.3 34.5c0.7 5.4 1.8 13.1 2.6 15.5 1.1 3.3 2 13.6 2.3 17.9 0.1 0.8 0.1 1.4 0.2 1.7 0.1 0.8 0.8 3.3 1.4 5.2 0.7 2.4 1 3.4 1 3.8 0 0.8-0.4 1.7-0.5 1.8 -1 1.3-2.5 3.6-2.4 4.6 0.1 0.6 1.4 1.7 2.3 2.5 1.3 1.1 2 1.8 2 2.4 -0.1 0.7 0 18.3 0 25.7l54.9-2.1c-0.5-1.9-1.2-5.1-2.6-6 -0.9-0.6-2.5-1.9-4.1-3.3 -2.4-2-5-4.3-6.3-4.8 -0.4-0.1-0.7-0.3-1-0.4 -1.6-0.6-2.7-1-3.1-2.6 -0.1-0.7-0.2-2.3-0.2-4.1 0-2.3-0.1-6.2-0.5-6.7 -0.4-0.5-1.8-2.2-1.7-4 0.1-0.9 0.6-1.7 1.4-2.3 1-0.7 2-1.2 2.8-1.5 1.4-0.6 1.9-0.9 1.9-1.8 0-5.9 0.4-9.1 1.1-9.6 0.3-0.2 2.2-1.3 4.6-2.5 1.3-2 3.6-4.7 6.4-7.5 4.9-4.9 4.8-4.8 7.2-5.5 2.4-0.7 7.1-3.2 6.9-3.8 -0.2-0.6-2.5-0.8-3.5-1.5 -1-0.7-3.2-0.6-4.7-0.2 -1.5 0.3-3 1-3.6 0s-0.8-1.9-1.7-1.8c-0.9 0.1-2.7 3.6-4.1 3.3s-4.8-1.7-6.2-2.9c-1.3-1.2-2.4-2.1-3.5-1.5 -1.2 0.6-2.8-1-4.2-2.2 -1.4-1.2-3.7-1.7-5.4-1.2 -1.7 0.5-1.7 1.5-3.5 1.5 -1.8 0-3.6-0.9-4.6-1.3 -1-0.4-4.8-0.3-5.4-1.3s-1.7-6.4-3.2-7.9 -2.5-1.5-2.7-0.8 0 4.9 0 4.9S312.1 34.5 299.3 34.5z"/>
							<path class="state" id="mississippi" d="M407.5 238.5c0 0-25.3 2-25.5 2.2 -0.5 0.7-1.3 1.8-1.3 2 -0.3 1.1-2.5 7.8-4.7 10.9 -2.3 3.2-2.3 8.6-2 12.6 0.4 4.1 2 10.5 2.7 13.2l0.2 0.9c0.4 1.6-0.8 3.5-2.4 6 -0.4 0.6-0.8 1.2-1.2 1.9 -1.5 2.6-2.9 9.6-3.3 11.8l23.3-1.4 0 0.6c0 0-0.1 3.6-0.1 5.4 0 1.4 1.9 3.7 3.2 5.4l0.7 0.8c0.3 0.4 0.7 0.7 1.2 0.7 0.1 0 0.2 0 0.3 0 -0.3-0.8-0.1-1.7 1.7-2.4 2.7-1.1 6-1.7 7.7-1.7 0.6 0 1.5 0 2.4 0 0 0 0 0 0 0 0-2.7-2.4-19.9-2.5-20L407.5 238.5z"/>
							<path class="state" id="missouri" d="M381.4 224.7c0.1 0.4 2.4 0.2 3.3 0.1 0.9-0.1 1.7-0.2 2.3-0.1 0.6-1.1 2.5-5 2.7-8 0.1-1.1 0.4-1.8 1.1-2.1 0.5-0.2 1 0 1.5 0.3 0-1.1 0.3-2.7 0.6-4.5 0 0 0 0 0-0.1 -0.7 0-1.7-0.2-2.8-1 -1.5-1.1-1.5-4.1-1.4-6.4 0-0.5 0-0.9 0-1.3 0-1.2-4.8-4.5-7.4-6.3 -2.3-1.6-3.6-2.5-4-3 -0.7-1 0-3.1 0.9-5.5 0.5-1.6 1.1-3.3 1.1-4.3 0-0.4-0.1-0.5-0.2-0.5 -0.5-0.4-2.1 0.2-3.2 0.5 -0.7 0.2-1.4 0.5-1.9 0.5 -0.4 0.1-0.8-0.1-1.2-0.3 -0.9-0.8-0.9-2.7-0.9-4.6 0-0.2 0-0.4 0-0.6 0-0.4-1.4-1.4-2.4-2.1 -1.9-1.4-4.2-3.1-5.1-5.2 -1.5-3.1-1.3-8.7-1.3-9.9l-2.7-2 -44.4 2.3 3.7 7.4c0.9 0.3 4.2 1.3 5 1.6 0.3 0.1 0.5 0.3 0.6 0.6 0.4 1.3-1.6 4.3-2.1 5 -0.3 0.5 2 3.3 5.4 6.3l0.2 0.1 0.8 31.9 0.2 6.2c12.8-0.7 51.7-2.8 53.5-0.9 1.4 1.4 0 3-1 4.2C382 223.4 381.4 224.2 381.4 224.7z"/>
							<path class="state" id="montana" d="M112.7 29.9c0.3 1.1 0.5 2.6 0.6 4.2 0.3 2.5 0.5 5.3 1.4 6.2 0.9 0.9 2.1 2.8 3.3 4.7 0.9 1.4 1.7 2.8 2.4 3.7 1.5 1.9 5.7 3.8 5.8 3.8l0.5 0.2 -0.3 0.5c0 0.1-4 6.8-4.4 11.5 -0.1 1.2-0.2 2.1-0.3 2.9 -0.1 1-0.2 1.8-0.1 2 0 0 0.1 0 0.3 0 0.4 0 1.2-0.2 1.9-0.4 1.5-0.5 3.1-0.9 3.9-0.1 0.3 0.3 0.5 1.3 1.1 4.2 1 4.7 2.8 13.4 5.4 14 3.7 0.9 16.6 1.5 19.1 1.6l1.4-6 0.5 0.1c0.2 0 23.6 3.8 38.5 5 13.6 1.2 30.6 2 33.8 2.2l3.7-57.7c-18.1-1.1-37.2-2.9-55.4-5.5 -24-3.5-45.2-7.3-62.4-10.7C112.9 21.4 112.3 28.2 112.7 29.9z"/>
							<path class="state" id="nebraska" d="M319 167.7l-3.6-7.3 -0.1 0 -0.1-0.4c0-0.2-3.9-17.8-4.9-20.7 -0.9-2.7-4.8-9-5.3-9.7 -0.3-0.2-1.7-1.2-4.5-2.8 -2-1.2-5.5-0.8-9.8 1 -2 0.8-3.8-2.6-4.4-3.9 -1.2 0-5.3 0.2-13.6 0 -9.3-0.2-42.2-1.8-46.6-2l-2.3 27.9 22.4 1.8 -0.8 15.1L319 167.7z"/>
							<path class="state" id="nevada" d="M79.9 115.8c-8.8-2-18.5-4.3-27.9-6.6l-10 43.3 48.5 70c0.1-0.3 0.2-0.6 0.3-0.8 0.3-0.7 0.5-1.3 0.5-1.7 0-0.2 0-0.6-0.1-1.1 -0.3-5-0.4-10.4 1.1-12 0.3-0.3 0.7-0.5 1.2-0.5 1.7 0 2.8 1.2 3.6 2 0.8 0.9 1 1 1.3 0.8 0.9-0.6 2.6-7.1 3.9-13.2h0l13.3-73.1C104.6 120.9 92.1 118.5 79.9 115.8z"/>
							<path class="state" id="new-hampshire" d="M566.4 62c0.9 1.6-0.7 5.7-2.1 8.9 -0.5 1-0.3 3.3-0.1 5.4 0.1 1.1 0.2 2.1 0.2 3.1 0 1.3-0.1 3.2-0.2 5.1 -0.1 2.1-0.2 4.4-0.2 5.1 0.1 1.1 0 2.8 0 3.7 1.9-0.5 4-0.9 6-1.4 6.7-1.5 7.4-1.7 10.2-4.1 0.6-0.5 1.2-0.9 1.7-1.1 0-0.1 0-0.3 0.1-0.4 0.1-0.4 0.3-0.8 0.5-1.4 -3.2-0.6-5.3-3.9-5.7-6 -0.3-1.4-2.7-9-4.6-15.1 -0.6-1.8-1.1-3.4-1.5-4.8 -0.7-2.1-1.1-4.1-1.5-5.8 -0.9 0.6-1.8 1-2 1.1 -0.3 0.1-0.6 2.3-1.3 3.3C565.9 59.7 566.1 61.4 566.4 62z"/>
							<path class="state" id="new-jersey" d="M553.6 130.6l2-7.3 -10.4-2.8c0.1 0.6-0.1 1-0.1 1l-1.1 9 4.8 6.7 -0.1 0.2c-0.1 0.5-1.3 4.2-3.2 5.5l2.1 9.5c0.8-0.7 2.2-1 3-0.6 1.4 0.8 0.8 3.3 1.5 3.6 0.7 0.3 4.5-5.1 5.4-10.7 0.9-5.6 0.7-12 0.9-12.8 0.1-0.2 0.6-0.6 1.3-1.1L553.6 130.6z"/>
							<path class="state" id="new-mexico" d="M180.9 281.4l44.1 2.8 5.8-72.1 -69.6-6.8 -10.3 82.9c5.9 0.7 10.4 1.2 10.4 1.2v-6c0 0 17 1.1 19.8 1.7L180.9 281.4z"/>
							<path class="state" id="new-york" d="M573.7 117c-2.7 0.6-6.6 3.6-9 4.7 -1.6 0.8-2.2 0.6-2.2 0.1l-2.8 1 -0.2-0.1c-0.5-0.3-2.2-1.3-2.1-2.3l0.1-0.5c0.1-0.9 0.3-2.1 0.6-3.1 0.2-0.5-0.7-3.3-1.2-4.9 -0.8-2.3-1.2-3.8-1.1-4.5 0.2-1.2-0.7-16.4-1.6-19.5 -0.7-2.2-1.7-3.6-2.6-4.8 -0.3-0.4-0.6-0.9-0.9-1.3 -0.6-1-0.9-3.7-1.2-6.9 -0.3-2.6-0.5-5.3-1-7 -0.4-1.4-1.1-2.9-1.7-4.2 -5.9 1.7-11.2 3.2-12.2 3.4 -2.5 0.7-7 4.3-9.1 8.4 -2.1 4.1-6.7 7-6 8.1 0.7 1.1 5 4.3 4 6.3 -1 2.1-4.6 4.5-5.5 5.7 -0.9 1.2-6.5 2.4-8.3 2.4s-13.2 0.6-14.3 2.8c-1.1 2.2 1.1 4.9 1.2 6.7 0.1 1.6-4.1 6.8-6.7 9.4l0.7 3.9c5.2-1 43.7-8.8 46.1-9.1 1.9-0.2 2.7 0.8 3.8 2.2 0.6 0.7 1.3 1.6 2.3 2.5 1.2 1.1 1.9 2.2 2.2 3l12 3.2 -1.9 7.1 6.5 0.3c1.8-1 4.2-2.3 5.9-3.4 4.4-2.9 9.3-6.6 9.3-8.6C576.6 117.5 575.7 116.6 573.7 117z"/>
							<path class="state" id="north-carolina" d="M553.8 206.1c0-2.7-2.7-8.5-4-10.2 -0.4-0.6-0.6-1.3-0.6-1.9 -0.8-0.2-1.5-0.4-2.2-0.3 -2.2 0.3-14.3 2.9-24.1 5 -6 1.3-11.1 2.4-13.3 2.8 -3.2 0.6-16.2 2.5-28 4.1 -0.4 1.5-2 6.6-4.3 7.6 -2.4 1-7.1 4.5-9.8 7.2 -1.5 1.6-4.4 2.5-6.6 3.3 -1.3 0.4-3.2 1.1-3.3 1.5 -0.2 1.4-2.1 4-3 4.7 0 0.5 0 3.4 0 3.4l12.4-1.1c2.4-2.6 8.5-5.3 10.7-5.8 3-0.7 14.8-1.6 16.4-1 1.5 0.5 2.6 2.3 3.1 3.2l0.1 0.1c0.8 0.1 4.8-0.4 7.5-0.8 4.2-0.6 5.8-0.8 6.3-0.6 0.9 0.3 10.5 8.1 14.6 11.3 0.6-0.5 1.3-0.9 2.1-1 2.4-0.6 5.8-1.2 5.9-2.9 0.2-1.8 1.7-6.9 5-9.5 3.3-2.6 11.2-6.7 10.9-7.7 -0.3-1-4.4-2.2-3-5.3C548.2 209.2 553.9 208.8 553.8 206.1z"/>
							<path class="state" id="north-dakota" d="M268.4 79.3c10.6 0.2 34.1 0.4 37.2 0.5 0.1-0.3 0.2-0.8 0.2-1.1 0-0.3-0.6-2.1-1-3.5 -0.7-2.2-1.4-4.5-1.5-5.4 0-0.3-0.1-0.9-0.2-1.7 -0.3-4-1.3-14.5-2.3-17.7 -0.8-2.5-1.9-10.4-2.7-15.8 -16.3 0-40.1-0.4-65.9-2l-2.8 44.4C233.3 77.1 257.9 79.1 268.4 79.3z"/>
							<path class="state" id="ohio" d="M451.6 131.9c0-0.1 0-0.2 0.1-0.4l-15.1 1.8c0.3 2.3 3.4 28.2 5.1 39.1l2.2-0.5c2.5-0.6 2.9 0.4 3.4 2.1 0.1 0.5 0.3 1 0.6 1.6 0.2 0.5 0.6 1.1 1.1 1.3 0.5 0.1 1-0.3 1.1-0.3l0.2-0.2 0.3 0.1c0 0 2.5 1.1 5.3 1.3 1.7 0.1 2.7-0.6 3.7-1.3 0.7-0.5 1.3-0.9 2.1-1.1 1.3-0.3 2.4 1.1 3.6 2.7 0.8 1 1.8 2.3 2.4 2.3 0.6 0 0.9-0.6 1.3-1.8 0.3-0.8 0.6-1.7 1.3-2.4 0.5-0.5 0.7-1.6 1-2.6 0.3-1.5 0.7-3 1.9-3.7 1.5-0.8 1.8-1.7 1.8-1.8 0.1-0.3 2.2-4.9 4.1-6.4 1.7-1.3 5.3-7.4 5.7-14 0.4-5.3-2.7-19.2-4-24.8 -2.6 1.6-4.9 3.2-5.6 3.9 -1.6 1.7-5 5.4-5.8 5.8 -0.7 0.4-3.9 0-4.7 0.8 -0.8 0.7-2 2-6.3 0.9C454 133.3 451.4 133 451.6 131.9z"/>
							<path class="state" id="oklahoma" d="M266 219.5v29c1.3 0.9 4.4 2.6 6.2 2.5 1.8-0.2 2.3 1.6 2.6 2.8 0.3 1 0.5 1.7 0.9 1.8 0.6 0.1 2.4 0 4.3-0.1 4.6-0.3 7.9-0.4 8.9 0.2 1.1 0.6 1.6 2 1.9 2.9 0.1 0.4 0.3 0.8 0.4 0.9 0.1 0 1 0 1.7-0.1 1.5-0.1 3.9-0.3 6.1 0 1.4 0.2 1.9-0.1 2.4-0.3 1-0.5 1.9-0.7 4.9 0.4 5.1 2 7 1.6 11.3 0 4.3-1.6 6.2-1.8 8.3-0.7 1.2 0.6 4.1 2.2 6 3.2 0-4.9-0.1-24.2-0.3-26.4 -0.2-2.4-2.5-15.4-2.5-15.5l-0.1-0.6 0.1 0 -0.1-5.8h-45.2c-4.2 0-46.8-1.4-51.9-1.6l-0.5 6L266 219.5z"/>
							<path class="state" id="oregon" d="M6.9 96.4c12.9 3.5 46.8 12.5 73.2 18.4 1.2 0.3 2.5 0.5 3.7 0.8v-0.7c2.9-11.1 7.3-27.9 7.3-29.2 0-1.3-0.7-2.5-1.4-3.8 -0.2-0.4-0.4-0.8-0.6-1.2 -0.7-1.3 2-5.1 7.8-12.6 1.5-2 2.9-3.7 3.1-4.2 0.5-1-1.5-3.8-3.2-5.5 -1.4-0.3-12.5-2.6-18.2-4 -5.9-1.4-24-0.5-24.2-0.5l-0.2 0L54 53.7c0 0-1.1-1.2-3.3-2.1 -0.9-0.3-2.4-0.2-4.1-0.1 -2.5 0.2-5.4 0.4-7.5-0.8 -2.2-1.3-2.1-3.1-2-4.9 0.1-1.2 0.1-2.5-0.5-3.9 -1.1-2.3-6.5-4.3-9.7-5.2 -0.9 4.3-3.2 14.8-5.8 21.1C17.8 66 9.1 80.9 8.6 82.8S6.5 93.5 6.8 95.5C6.8 95.7 6.9 96 6.9 96.4z"/>
							<path class="state" id="pennsylvania" d="M542.9 130.8l1.2-9.5c0-0.1 0.5-1.7-2.1-4.1 -1.1-1-1.8-1.9-2.4-2.6 -1-1.3-1.6-2-2.9-1.8 -2.5 0.2-46 9.1-46.5 9.1l-0.5 0.1 -0.8-4.3c0 0-0.1 0.1-0.1 0.1 -1.1 0.9-4.1 2.8-7 4.6 1.2 4.9 3.9 17.3 4.1 23.7l1.9 10c20.1-3.8 49.8-9.7 52.3-10.7 0.2-0.5 0.9-1.5 4.6-3.1 1.4-0.6 2.6-3.6 3.1-4.9L542.9 130.8z"/>
							<path class="state" id="rhode-island" d="M579.4 102.5c-0.4-0.2-2 0-4.4 0.4l2.1 9.3c1-0.4 2.2-0.7 3.4-1.1 0.4-0.1 1-0.6 1.7-1.2C581.2 106.1 580.1 102.8 579.4 102.5z"/>
							<path class="state" id="south-carolina" d="M520.1 250.6c0.7-2.2 0.9-6.1 2.1-7.9 0.7-1 1.7-2.3 2.8-3.4 -5.8-4.6-13.4-10.7-14.1-11 -0.4-0.1-3.4 0.3-5.8 0.6 -7.3 1-8.1 1-8.5 0.4l-0.2-0.3c-0.5-0.8-1.4-2.4-2.6-2.8 -1.2-0.4-12.6 0.3-15.9 1 -2.4 0.5-7.6 3-9.9 5.2 0 0-0.7 0.9-0.8 1 -0.2 0.4-0.3 0.7-0.2 1 0.7 2.5 3.3 3.5 4.5 4 0.7 0.3 1.5 1.2 2.5 2.4 1.2 1.4 2.7 3.2 4.4 4.2 1.5 0.9 3.7 3.2 5.8 5.4 2 2.1 4.2 4.4 5.3 4.9 2.6 1.3 5.3 6.2 5.3 7.1 0.1 0.1 0.8 0.7 1.2 1 0.9 0.7 1.8 1.4 2 2.1 0 0.1 0.1 0.3 0.1 0.5 0.3 1.2 0.8 3.7 2.4 3.7 0.8 0 1.7 0 2.7 0 2.9-4.3 8.1-9.7 10.2-12C516.1 255.3 519.3 252.8 520.1 250.6z"/>
							<path class="state" id="south-dakota" d="M228.6 91.2L228.6 91.2l-2.4 29.7c4.5 0.2 37.3 1.8 46.6 2 9.9 0.2 13.8 0 13.9 0l0.4 0 0.1 0.3c0.7 1.7 2.3 4.1 3.2 3.7 3.3-1.4 7.8-2.7 10.7-1 2 1.2 3.3 2 4 2.5 0.6-2.3 1.1-4.7 1.1-5 0-0.4-0.5-4.4-0.8-7l-0.1-0.6h1.9c0-3.7-0.1-24.9 0-25.8 -0.1-0.2-1-1-1.6-1.6 -1.3-1.1-2.5-2.2-2.7-3.2 -0.1-1.2 1.2-3.3 2-4.4 -4.9 0-26.6-0.3-36.6-0.5 -10.5-0.2-35.1-2.2-38.9-2.5L228.6 91.2z"/>
							<path class="state" id="tennessee" d="M454 229L454 229c0.5-0.3 2.5-2.8 2.6-4 0.1-1 1.7-1.5 4-2.3 2.2-0.7 4.9-1.6 6.2-3 2.8-2.8 7.6-6.3 10.1-7.4 1.6-0.7 3-4.4 3.6-6.5 -9.3 1.3-17.6 2.4-19.4 2.6 -3.9 0.5-52.4 5.1-53.5 5.1 -0.1 0.1-0.2 0.9-0.2 1.3 0 0.5-0.1 1.1-0.2 1.5 -0.3 0.9-4.9 1.2-13.5 0.8 -0.3 0-0.6-0.2-0.9-0.5 -0.1-0.1-0.1-0.2-0.2-0.4 -0.5-0.4-1.1-0.8-1.4-0.7 -0.2 0.1-0.4 0.5-0.4 1.2 -0.2 3-2 6.7-2.7 8.1 0.3 0.1 0.6 0.3 0.7 0.6 0.3 0.9-0.6 2.7-2 5.5 -0.9 1.8-2 3.8-2 4.6 0 1.1-1 2.6-2 4.1l70.8-6.3C453.7 233.4 453.4 229.3 454 229z"/>
							<path class="state" id="texas" d="M343.2 296.8c-1.1-1.4-2.1-2.7-2.2-3.8 -0.2-1.7-2.3-4.8-3.1-5.8 -0.8-0.9-0.6-14.3-0.5-22.6l-5.5-1.1v-0.4c-1.8-1-5.2-2.8-6.5-3.4 -1.6-0.8-3-0.9-7.5 0.8 -4.4 1.7-6.6 2.1-12 0 -2.8-1.1-3.4-0.8-4.1-0.5 -0.6 0.3-1.3 0.6-3 0.4 -2.2-0.3-4.4-0.1-5.9 0 -1.2 0.1-1.8 0.1-2.1 0 -0.5-0.2-0.7-0.8-1-1.6 -0.3-0.9-0.6-1.9-1.4-2.4 -0.9-0.5-5.4-0.2-8.4-0.1 -2.1 0.1-3.7 0.2-4.4 0.1 -1.2-0.1-1.5-1.4-1.8-2.5 -0.4-1.5-0.7-2.1-1.5-2.1 -2.7 0.3-6.9-2.7-7.1-2.8l-0.2-0.2v-28.5l-32.8-1.3h-0.8l-5.3 66.1 -44-2.8 0.3 3.2c2.3 2.1 7.9 9.2 10.1 11.3 2.5 2.4 7.9 6.5 8.7 7.6 0.8 1.1 1.7 10 2.8 11.8 1.1 1.8 15.2 15.1 17.9 13.5 2.7-1.6 6.6-10.6 8.3-10.8 1.7-0.2 10.9-2.9 17.8 3.9 6.8 6.8 8.8 14.4 10.2 17.2 1.4 2.7 5.4 8.3 6.7 9.6 1.3 1.3 3 2 3.6 2.8s-0.1 6.4 1.9 9.6c2.1 3.1 1.9 7 3.2 7.8s6.4 2.3 8.3 3.1c1.9 0.8 10 2.5 10.8 3s7.4 5.3 6.9 1.3 -4.3-11-2.7-18.4 6.7-14.8 9.1-16.4c2.5-1.5 9-2.9 12.3-4.9 3.3-2.1 9.8-9.5 12.3-11.1 2.2-1.4 6.8-3.8 10.5-5 0.1-1.3 0.3-2.5 0.6-3.2 0.5-0.9 0.8-3.4 1.1-5.9 0.2-1.4 0.3-2.8 0.5-4 0.3-1.5 0.6-3.1 1-4.4 0.4-1.8 0.8-3.3 0.8-4.2C345 299.2 344 297.9 343.2 296.8z"/>
							<path class="state" id="utah" d="M167.5 145.6c-13.5-1.7-21.3-2.8-21.4-2.8l-0.5-0.1 2-15c-2.6-0.4-15-2.1-31-4.8l-13.2 72.6 57.1 8.6L167.5 145.6z"/>
							<path class="state" id="vermont" d="M565.5 62.5c-0.4-0.6-0.6-2.4-0.7-4.3 -2.3 0.8-10 3.1-17.1 5.1 0.6 1.3 1.3 2.8 1.7 4.2 0.5 1.7 0.8 4.5 1 7.2 0.3 2.7 0.6 5.7 1.1 6.5 0.3 0.4 0.6 0.8 0.9 1.2 0.9 1.3 2 2.7 2.7 5.1 0.4 1.2 0.7 4.3 1 7.7 1.1-0.3 3.7-0.9 6.8-1.7 0-0.8 0.1-2.7 0-3.8 -0.1-0.8 0-3 0.2-5.3 0.1-1.9 0.2-3.8 0.2-5 0-0.9-0.1-1.9-0.2-3 -0.2-2.3-0.4-4.6 0.2-5.9C565.2 66.4 566.1 63.4 565.5 62.5z"/>
							<path class="state" id="virginia" d="M506.3 167.6c-0.2 0-0.3 0.9-0.3 1.6 -0.1 1-0.1 2.2-0.6 3.3 -0.3 0.7-0.7 1.1-1.3 1.2 -1 0.2-1.9-0.5-2.9-1.2 -0.3-0.2-0.6-0.4-0.9-0.6 -0.7 0.6-1.9 4.9-2.5 7.2 -0.5 2-1 3.5-1.3 4.2 -0.3 0.6-0.5 1.7-0.6 2.8 -0.3 2.1-0.6 4.3-2.1 4.6 -0.7 0.1-1.7 0.7-2.7 1.3 -1.3 0.8-2.8 1.7-4.4 2 -1.6 0.3-2.3 0.6-3.2 1.1 -0.6 0.3-1.3 0.6-2.3 1 -0.5 0.2-0.9 0.3-1.4 0.3 -1.5 0-2.4-1.1-3.5-2.2 -0.4-0.4-0.7-0.8-1.1-1.2 -1 1-4 4.2-6.2 7 -2 2.5-6.6 5.9-9.2 7.6 0.6-0.1 1-0.1 1.2-0.1 4-0.5 42.8-5.8 48.5-6.9 2.1-0.4 7.3-1.5 13.3-2.8 10.3-2.2 21.9-4.7 24.2-5 0.8-0.1 1.5 0 2.3 0.2 0-0.9 0-1.9-0.5-3 -1-2.4-4.1-1.6-4.7-2.6 -0.6-0.9-0.7-8.6-3.2-11.3 -1.1-1.2-1.6-2.3-1.9-3.3 -2.9-0.9-7.7-2.4-9.4-2.8 -0.5-0.1-1-0.2-1.4-0.2 -0.9-0.1-1.7-0.2-2.1-0.9 -0.3-0.6-0.1-1.5 0.7-3.1 1.2-2.3 2.3-4.2 1.9-5.1 0-0.1-0.2-0.4-1-0.7 -1.1-0.3-2.5-1.2-3.9-2.1 -1-0.7-2.9-1.9-3.3-1.7 -0.2 0.3-0.3 0.8-0.4 1.3 -0.2 0.9-0.4 1.8-1.1 2 -0.7 0.2-1.4-0.2-1.9-0.6 -1.5-1.2-3.6-2-4.8-2.5 0.5 0.8 0.8 1.9 0.7 2.8C512.7 160.9 507.9 167.5 506.3 167.6z"/>
							<path class="state" id="washington" d="M27.1 35.7c2.8 0.8 9.1 2.9 10.4 5.7 0.7 1.6 0.7 3 0.6 4.4 -0.1 1.7-0.2 3 1.5 4 1.8 1.1 4.5 0.9 6.9 0.7 1.9-0.1 3.5-0.3 4.6 0.2 2 0.8 3.1 1.8 3.5 2.2 2.2-0.1 18.5-0.8 24.3 0.5 5.3 1.3 15.3 3.4 17.7 3.9 0.3-2.7 1.4-14 3.3-20.8 1.7-6.2 3.7-16.6 4.6-22.1C80.1 9.2 65.8 5.4 65.8 5.4S54.4 0.8 52 0.6C49.6 0.4 50 1.1 50 1.1s1.2 3.5 1.3 4.4c0.1 1-2.2 4.6-2.7 5 -0.5 0.5-2.1 2.3-2.5 2.3 -0.3 0-5.6-1.3-9-3.5 -3.3-2.2-7.4-5.2-7.6-5.2 -0.3 0-1.9 4.2-1.5 5.5 0.4 1.3 1.5 8 1.5 9.8 0 1.8-2.2 15.2-2.2 15.2S27.2 35.1 27.1 35.7z"/>
							<path class="state" id="west-virginia" d="M473.6 170.8c-0.8 0.4-1.1 1.7-1.4 3 -0.3 1.2-0.5 2.4-1.2 3.1 -0.5 0.5-0.8 1.2-1 2 -0.4 1.1-0.9 2.4-2.2 2.5 0.1 1.3 0.5 4.5 2.2 5.7 1.4 1.1 2.4 2 3.5 2.9 0.6 0.6 1.3 1.2 2.1 1.9 0.6 0.5 1.1 1 1.6 1.6 1.4 1.5 2.2 2.3 3.7 1.7 0.9-0.4 1.6-0.7 2.2-1 1-0.5 1.8-0.9 3.5-1.2 1.3-0.2 2.8-1.1 4-1.8 1.1-0.7 2.1-1.3 3-1.5 0.8-0.1 1.1-2.2 1.3-3.7 0.2-1.2 0.4-2.4 0.7-3.1 0.3-0.5 0.7-2.2 1.2-3.9 1.3-4.7 2.1-7.5 3.2-8 0.3-0.1 0.5-0.1 0.8 0.1 0.3 0.2 0.7 0.5 1 0.7 0.7 0.5 1.5 1.2 2 1 0.2-0.1 0.4-0.3 0.6-0.6 0.4-0.9 0.5-2 0.5-2.9 0.1-1.2 0.1-2.4 1.2-2.5 1-0.2 5.5-6 5.7-7.4 0.2-1.3-1-3.1-1.4-3.2 -0.7 0.1-6.3 3-7.2 4.3 -1.6 2.3-2.4 2.6-2.9 2.5 -0.2 0-0.6-0.1-0.9-0.7 -0.6-1.2-0.7-5.1-0.7-5.6l0-1.7c-3.5 0.7-7.2 1.4-11.2 2.1l-0.5 0.1 -1.4-7.2c-1 6.1-4.2 11.2-5.9 12.5 -1.7 1.3-3.7 5.8-3.7 5.9 0 0 0 0 0 0C475.9 168.4 475.6 169.8 473.6 170.8z"/>
							<path class="state" id="wisconsin" d="M351.5 67.3c-0.3 0.4-0.7 3.6-0.7 8.8 0 1.7-1.2 2.2-2.5 2.8 -0.8 0.3-1.7 0.7-2.6 1.4 -0.6 0.5-0.9 1-1 1.6 -0.1 1.1 0.7 2.4 1.4 3.2 0.6 0.6 0.7 2.7 0.8 7.4 0 1.7 0.1 3.3 0.2 3.9 0.2 1 0.9 1.3 2.4 1.9 0.3 0.1 0.7 0.2 1 0.4 1.4 0.5 4 2.8 6.5 4.9 1.6 1.4 3.1 2.6 4 3.2 1.8 1.2 2.6 4.5 3.1 6.7 0.2 0.7 0.3 1.4 0.4 1.6 0.7 0.7 2.1 10.1 2.1 10.2 0.1 0.7 2.5 1.9 4.7 2.7l0.2 0.1 0.1 0.1c0 0 0.1 0.2 0.3 0.5l30.1-3.6c-0.1-1.8-0.2-3.6-0.5-4.3 -0.6-1.6-1.1-9.8-0.9-13.1 0.2-3.3 1.2-5.8 1.5-7.3 0.3-1.5 4.3-12.6 4.3-14.6 0-0.5 0-1.1 0.1-1.7 -1 0-1.8 0.1-2.1 0.6 -0.6 0.9-2.1 2.9-3.9 3.1 -0.8 0.1-1.5-0.2-2.1-0.9 -1.2-1.2-1.8-3.3-2.4-5.1 -0.3-1-0.6-1.9-1-2.6 -0.9-1.7-5.9-3.7-9.3-4.1 -3.3-0.3-11.8-2.8-14.3-5.2 -0.6-0.6-1.2-1.4-1.9-2.5 -0.7 0.3-1.3 0.4-1.9 0.3 -2.1-0.4-3.7-0.4-2.7-2.4s0.7-3-0.4-3c-1.1 0-9.5 5.2-10.3 4.5 -0.2-0.2-0.1-0.6 0.2-1.2C352.8 66.5 351.7 67.1 351.5 67.3z"/>
							<path class="state" id="wyoming" d="M155.6 83.9l-1.3 5.5 -5.7 37.9 -1.9 14.5c2.8 0.4 16.8 2.4 38.5 4.9 21.7 2.5 34.9 2.9 37.5 3l4.8-58.6c-3.2-0.2-20.2-1-33.8-2.2C180 87.7 159 84.5 155.6 83.9z"/>
						</g>
					</svg>
			</div><!-- end div.map -->
		</div><!-- end div#jones_locations_svg_map_container -->';
		return $output;
	}

	/**
	 * Get links to all locations - for footer.
	 *
	 * @param int ...$except One or more term_id of the location that I don't want to include. Default 75 is 'Denver' and 72 is National.
	 */
	public function get_location_links( ...$except ) {
		global $blog_id;
		$all_links = [];
		// Don't want Jones National, Jones Denver, or the current site.
		$except[] = $this->get_terms_blogs_array( 'blog' )[ $blog_id ];
		// Utilize array_unique to ensure no duplicate term_ids.
		$locations = $this->get_location_ids( ...$except );
		foreach ( $locations as $key => $value ) {
			$all_links[] = $this->get_single_location_link( $value );
		}

		return $all_links;
	}

	/**
	 * Create the extra fields for the taxonomy type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since  1.0.0
	 * @link   https://github.com/CMB2/CMB2/wiki/Box-Properties
	 * @param string $states - HTML for the state select field.
	 */
	public function create_location_taxonomy_extra_fields( $states ) {
		$prefix  = $this->get_slug();
		$args    = [
			'id'           => $prefix . 'edit',
			'title'        => 'Location Taxonomy Extra Info',
			'object_types' => [ 'term' ],
			'taxonomies'   => [ 'location' ],
			'cmb_styles'   => false,
			'show_in_rest' => \WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::EDITABLE, // Determines which HTTP methods the box is visible in.
		];
		$metabox = new_cmb2_box( $args );

		// Blog Id.
		$args = [
			'name'        => 'blog id',
			'description' => 'blog_id',
			'id'          => 'locationBlogID',
			'type'        => 'text_small',
			'show_names'  => true,
		];
		$metabox->add_field( $args );

		// Common Name.
		$args = [
			'name'        => 'general name',
			'description' => 'general name for this location',
			'id'          => 'locationCommonName',
			'type'        => 'text_small',
			'show_names'  => true,
		];
		$metabox->add_field( $args );

		// Subdomain Url.
		$args = [
			'name'        => 'Subdomain Website URL',
			'description' => 'subdomain website url',
			'id'          => 'locationSubdomain',
			'type'        => 'text_url',
			'show_names'  => true,
			'protocols'   => [ 'http', 'https' ],
		];
		$metabox->add_field( $args );

		// Nimble Url.
		$args = [
			'name'        => 'Website URL',
			'description' => 'nimble website url',
			'id'          => 'locationNimbleURL',
			'type'        => 'text_url',
			'show_names'  => true,
			'protocols'   => [ 'http', 'https' ],
		];
		$metabox->add_field( $args );

		// Capabilities Of The Location.
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
				'Service'            => 'Service',
			],
		];
		$metabox->add_field( $args );

		// Location Image.
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
		// City Image.
		$args = [
			'name'         => 'City Image',
			'show_names'   => true,
			'id'           => 'locationCityImage',
			'type'         => 'file',
			'options'      => [ 'url' => false ],
			'text'         => [ 'add_upload_file_text' => 'Upload or Find City Image' ],
			'query_args'   => [
				'type' => [ 'image/jpg', 'image/jpeg' ],
			],
			'preview_size' => 'medium',
		];
		$metabox->add_field( $args );

		// Jones Location Data.
		$args = [
			'name'       => 'Location',
			'id'         => 'locationAddress', // Name of the custom field type we setup.
			'type'       => 'jonesaddress',
			'show_names' => false, // false removes the left cell of the table -- this is worth understanding.
			'after_row'  => '<hr>',
		];
		$metabox->add_field( $args );
		/**
		 * Longer Description
		 */
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

	}//end create_location_taxonomy_extra_fields()

	/**
	 * Set up some new columns in the admin screen for the location taxonomy.
	 *
	 * @param array $columns The existing columns before I monkeyed with them.
	 * @link https://shibashake.com/wordpress-theme/modify-custom-taxonomy-columns
	 */
	public function set_admin_columns( $columns ) {
		// Remove the checkbox that comes with $columns.
		unset( $columns['cb'] );
		unset( $columns['description'] );
		// Add the checkbox back in so it can be before the ID column.
		$new['cb']         = '<input type = "checkbox" />';
		$new['id']         = 'ID';
		$new['blog_id']    = '<span style="color: var(--yellow-600);"title="go to the blog for this location" class="dashicons dashicons-external"></span>';
		$new['capability'] = '<span style="color: var(--yellow-600);" title="location capabilities" class="material-icons">rule</span>';
		return array_merge( $new, $columns );
	}

	/**
	 * Add the correct data to the custom columns.
	 *
	 * @param  string $content Already existing content for the already existing rows.
	 * @param  string $column_name As instantiated in the 'set_admin_columns' function.
	 * @param  int    $term_id Term in quation.
	 * @echo   string $output The content for the columns.
	 */
	public function set_data_for_custom_admin_columns( $content, $column_name, $term_id ) {
		$taxonomy = $this->get_slug();

		switch ( $column_name ) {
			case 'capability':
				$output = implode( '', $this->get_capability_icons( $term_id ) );
				break;
			case 'blog_id':
				$blogid     = (int) get_term_meta( $term_id, 'locationBlogID', true );
				$admin_link = preg_replace( '/^http: /i', 'https: ', get_term_meta( $term_id, 'locationSubdomain', true ) ) . '/wp-admin/';
				$output     = '<a href = "' . $admin_link . '" >' . $blogid . '<span class="dashicons dashicons-external"></span></a>';
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
	public function make_columns_sortable( $columns ) {
		$columns['id']      = 'ID';
		$columns['blog_id'] = 'blog_id';
		$columns['slug']    = 'Slug';
		return $columns;
	}

	/** RETRIEVE LOCATION INFORMATION  */

	/**
	 * Retrieve the taxonomy meta for 'locationAddress' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return array $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_address( $term_id ) {
		$key    = 'locationAddress';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'locationNimbleURL' for this jones sign location.
	 *
	 * @param int  $term_id Location Taxonomy id.
	 * @param bool $nimble If true, get the nimble made domain name, otherwise, get the subdomain.
	 * @return string $output The domain that nimble gave this website.
	 */
	public function get_location_url( $term_id, $nimble = true ) {
		$key    = $nimble ? 'locationNimbleURL' : 'locationSubdomain';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the location description.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The description of the specified location.
	 */
	public function get_location_description( $term_id ) {
		return get_term( $term_id )->description;
	}

	/**
	 * Retrieve the location common name meta.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The sudomain of this location's homepage.
	 */
	public function get_location_name( $term_id ) {
		$key    = 'locationCommonName';
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
	 * Output material icons to correspond with location capabilities.
	 *
	 * @param int $term_id Location taxonomy term_id.
	 */
	public function get_capability_icons( $term_id ) {
		$capabilities = $this->get_location_capability( $term_id );
		$icons        = [];
		foreach ( $capabilities as $capability ) {
			switch ( $capability ) {
				case 'Fabrication':
					$icons['build'] = 'has fabrication capabilities';
					break;
				case 'Project Management':
					$icons['analytics'] = 'has project management capbilties';
					break;
				case 'Sales':
					$icons['point_of_sale'] = 'has sales office';
					break;
				case 'Installation':
					$icons['construction'] = 'has installation team';
					break;
				case 'Service':
					$icons['construction'] = 'Maintenance';
					break;
				default:
					$icons[] = '';
			}
		}
		$output = [];
		foreach ( $icons as $key => $value ) {
			$output[] = wp_sprintf( '<span style="font-size: 18px; color: var(--indigo-600);" class="material-icons" title="%s">%s</span>', $value, $key );
		}
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'locationImage' for this jones sign location.
	 *
	 * @param int  $term_id Location Taxonomy id.
	 * @param bool $return_as_url Whether to return as the url or the id. Defaults to false, which is ID.
	 *
	 * @return int $output The id of the location's photo.
	 */
	public function get_location_image( $term_id, $return_as_url = false ) {
		$key    = $return_as_url ? 'locationImage' : 'locationImage_id';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'locationCityImage' for this jones sign location.
	 *
	 * @param int  $term_id Location Taxonomy id.
	 * @param bool $return_as_url Whether to return as the url or the id. Defaults to false, which is ID.
	 *
	 * @return int $output The id of the location's city photo.
	 */
	public function get_city_image( $term_id, $return_as_url = false ) {
		$key    = $return_as_url ? 'locationCityImage' : 'locationCityImage_id';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Output all the Jones Location information into a json-encoded array to use on any of the sites -- saves calls to the database.
	 *
	 * @see the only reason to run this, would be that I've added new information to any of the items within the location taxonomy.
	 */
	public function action_enqueue_locations_script() {
		$location_ids = $this->get_location_ids();
		$locations    = [];
		foreach ( $this->get_location_ids() as $identifier ) {
			$locations[] = $this->get_location_info( $identifier );
		}
		$loc_data  = wp_json_encode( $locations );
		$handle    = 'jones-locations-data'; // script handle.
		$path      = get_theme_file_uri( '/assets/js/jonessign.min.js' ); // path to script.
		$deps      = []; // dependencies.
		$version   = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/jonessign.min.js' ) ); // script version.
		$in_footer = false; // Do we enqueue the script into the footer -- no.
		wp_enqueue_script( $handle, $path, $deps, $version, $in_footer );
		wp_script_add_data( $handle, 'defer', false ); // if true - wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
		wp_localize_script( $handle, 'jonesignInfo', [
			'locations' => $loc_data,
		] );

		$handle      = 'jones-locations-classie'; // script handle.
		$script_path = get_theme_file_uri( '/assets/js/classie.min.js' ); // path to script.
		$deps        = []; // dependencies.
		$version     = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/classie.min.js' ) ); // script version.
		$in_footer   = false; // Do we enqueue the script into the footer.
		wp_register_script( $handle, $script_path, $deps, $version, $in_footer );

		$handle      = 'jones-locations-select'; // script handle.
		$script_path = get_theme_file_uri( '/assets/js/select_effects.min.js' ); // path to script.
		$deps        = [ 'jones-locations-classie' ]; // dependencies.
		$version     = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/select_effects.min.js' ) ); // script version.
		$in_footer   = false; // Do we enqueue the script into the footer.
		wp_enqueue_script( $handle, $script_path, $deps, $version, $in_footer );

		wp_script_add_data( $handle, 'defer', false ); // wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
	}

	/**
	 * Output all the Jones Location information into a json-encoded array to use on any of the sites -- saves calls to the database.
	 *
	 * @see the only reason to run this, would be that I've added new information to any of the items within the location taxonomy.
	 */


	/**
	 * Output entire rich snippet for a location.
	 *
	 * @param int $location Term id of the location. Defaults to 60 - which is Jones Green Bay.
	 * @link https://search.google.com/structured-data/testing-tool
	 */
	public function get_location_schema( $location = 60 ) {
		$jones_url     = $this->default_jones_url;
		$company_name  = $this->full_company_name;
		$facebook      = $this->facebook;
		$twitter_url   = $this->twitter_url;
		$linkedin      = $this->linkedin;
		$about_jones   = $this->about_jones;
		$slogan        = $this->slogan;
		$info          = $this->get_location_info( $location );
		$location_img  = $info['location_image_id'];
		$name          = $info['name'];
		$slug          = $info['slug'];
		$address       = $info['address'];
		$latitude      = $address['latitude'];
		$longitude     = $address['longitude'];
		$loc_phone     = $address['phone'];
		$loc_fax       = $address['phone'] || '';
		$locality      = $address['city'];
		$region        = $address['state'];
		$email         = $address['email'] || 'leads-signs@jonessign.com';
		$subdomain     = $info['subdomain'];
		$url           = $info['nimble'] || $subdomain;
		$street        = $address['address'];
		$zip           = $address['zip'];
		$photo_sixteen = wp_get_attachment_image_src( $location_img, 'wp-rig-featured' )[0];
		$photo_four    = wp_get_attachment_image_src( $location_img, 'medium_large' )[0];
		$photo_square  = wp_get_attachment_image_src( $location_img, 'medium' )[0];

		$output = <<<JSONLD
		<script type="application/ld+json">
		{
			"@context": {
				"@vocab": "http://schema.org"
			},
			"@graph": [
				{
					"@id": "$jones_url",
					"@type": "Organization",
					"name": "$company_name",
					"url": "$jones_url",
					"logo": "https://jonessign.com/wp-content/uploads/2017/05/2016_jones_yva_blue_grey_273x85_semibold.png",
					"sameAs": [
						"$facebook",
						"$twitter_url",
						"$linkedin"
					]
				},
				{
					"@type": "LocalBusiness",
					"parentOrganization": {
						"name": "$company_name"
					},
					"image": [
						"$photo_sixteen",
						"$photo_four",
						"$photo_square"
					],
					"address": {
						"@type": "PostalAddress",
						"addressLocality": "$locality",
						"addressRegion": "$region",
						"streetAddress": "$street",
						"postalCode": "$zip"
					},
					"openingHours": ["Mo-Fr 08:00-17:00"],
					"geo": {
						"@type": "GeoCoordinates",
						"latitude": "$latitude",
						"longitude": "$longitude"
					},
					"url": "$url",
					"name": "$name",
					"telephone": "$loc_phone",
					"priceRange": "$200-$1000000",
					"slogan": "$slogan",
					"alternateName": [
						"Jones Sign Company",
						"Jones Sign",
						"Jones Sign Co"
					],
					"paymentAccepted":"Cash, Credit Card, Check, Purchase Order",
					"branchCode": "$slug",
					"foundingDate": "1910",
					"foundingLocation": "Green Bay, WI",
					"email": "leads-signs@jonessign.com",
					"description": "$about_jones"
				}
			]
		}
		</script>

JSONLD;
		return $output;
	}


}//end class
