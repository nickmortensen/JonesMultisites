<?php
/**
 * WP_Rig\WP_Rig\JonesSign\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\JonesSign;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\Base_Support\Component as Baseline;
use WP_Rig\WP_Rig\TaxonomyGlobal\Component as Taxonomies;
use WP_Rig\WP_Rig\Posttype_Project\Component as Projects;
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
	 * The Jones Sign Company twitter url.
	 *
	 * @access   public
	 * @var      string    $twitter_url The Jones Sign Company twitter url.
	 */
	public $wescover_url = WESCOVER_URL;

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
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_copyright_notice() : string {
		return '&copy;' . date( 'Y' ) . ' Jones Sign Co., Inc.';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'cmb2_init', [ $this, 'create_location_taxonomy_extra_fields' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_locations_script' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_d3' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_smoothstate' ] );
		add_filter( 'manage_edit-' . $this->slug . '_columns', [ $this, 'set_admin_columns' ], 10, 1 );
		add_filter( 'manage_edit-' . $this->slug . '_sortable_columns', [ $this, 'make_columns_sortable' ], 10, 1 );
		add_filter( 'manage_' . $this->slug . '_custom_column', [ $this, 'set_data_for_custom_admin_columns' ], 10, 3 );
		add_filter( 'get_the_archive_title', [ $this, 'update_the_archive_title' ] );
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
			'get_location_links'                    => [ $this, 'get_location_links' ],
			'get_locations'                         => [ $this, 'get_locations' ],
			'get_location_ids'                      => [ $this, 'get_location_ids' ],
			'get_location_name'                     => [ $this, 'get_location_name' ],
			'get_jones_locations'                   => [ $this, 'get_jones_locations' ],
			'get_location_taxonomy'                 => [ $this, 'get_location_taxonomy' ],
			'get_location_url'                      => [ $this, 'get_location_url' ],
			'get_location_capability'               => [ $this, 'get_location_capability' ],
			'get_location_info'                     => [ $this, 'get_location_info' ],
			'get_capability_icons'                  => [ $this, 'get_capability_icons' ],
			'get_terms_blogs_array'                 => [ $this, 'get_terms_blogs_array' ],
			'get_single_location_link'              => [ $this, 'get_single_location_link' ],
			'get_location_schema'                   => [ $this, 'get_location_schema' ],
			'get_single_location_details'           => [ $this, 'get_single_location_details' ],
			'get_location_option'                   => [ $this, 'get_location_option' ],
			'get_single_location_address'           => [ $this, 'get_single_location_address' ],
			'get_location_pin'                      => [ $this, 'get_location_pin' ],
			'get_jones_icon'                        => [ $this, 'get_jones_icon' ],
			'get_jones_logo'                        => [ $this, 'get_jones_logo' ],
			'get_locations_mapped'                  => [ $this, 'get_locations_mapped' ],
			'get_single_location_details_frontpage' => [ $this, 'get_single_location_details_frontpage' ],
			'get_years_in_business'                 => [ $this, 'get_years_in_business' ],
			'get_copyright_notice'                  => [ $this, 'get_copyright_notice' ],
			'get_picture_element'                   => [ $this, 'get_picture_element' ],
			'get_company_aspects'                   => [ $this, 'get_company_aspects' ],
			'get_aspect_card'                       => [ $this, 'get_aspect_card' ],
			'get_frontpage_header'                  => [ $this, 'get_frontpage_header' ],
			'get_general_header'                    => [ $this, 'get_general_header' ],
			'get_masthead'                          => [ $this, 'get_masthead' ],
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
		$key             = false === $return_as_url ? 'locationCinematic_id' : 'locationCinematic';
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
		$key             = false === $return_as_url ? 'locationRectangular_id' : 'locationRectangular';
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
	 * ASpects of the company array is returned
	 */
	public function get_company_aspects() {
		return [
			[
				'title' => 'Large Venues',
				'cta'   => 'Learn More',
				'image' => get_template_directory_uri() . '/assets/images/icon-1.png',
				'desc'  => 'We work with Architects & General Contractors on their biggest jobs. Tell us about your project.',
				'url'   => '#',
				'style' => 'light',
			],
			[
				'title' => 'National Programs',
				'cta'   => 'Learn More',
				'image' => get_template_directory_uri() . '/assets/images/icon-2.png',
				'desc'  => 'An individual project, or your whole sign program. Try us at one location & see the difference',
				'url'   => '#',
				'style' => 'dark',
			],
			[
				'title' => 'Specialty Fabrication',
				'cta'   => 'Learn More',
				'image' => get_template_directory_uri() . '/assets/images/icon-3.png',
				'desc'  => 'We aren\'t limited to signage. Whatever you can dream up, we can make manifest.',
				'url'   => '#',
				'style' => 'highlight',
			],
			[
				'title' => 'National Maintenance',
				'cta'   => 'Learn More',
				'image' => get_template_directory_uri() . '/assets/images/icon-3.png',
				'desc'  => 'Any sign. Any manufacturer. Anywhere in North America. Sign Maintenance made easy.',
				'url'   => '#',
				'style' => 'dark',
			],
		];
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
		$output   = wp_sprintf( '<option class="list-item" data-value="%s" value="%s" data-location-id="%d"%s>%s</option>', $slug, $slug, $id, $selected, ucwords( trim( preg_replace( '/Jones/i', '', $name ) ) ) );
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
	 * Get a single locations details and output as html for the frontpage.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_single_location_details_frontpage( int $term_id ) {
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
		$div_id  = 'single-facility-information';
		$class   = 'nat' === $slug ? ' class=""' : ' class="remove"';
		$output  = '';
		$output .= wp_sprintf( '<div id="%s" title="%s" data-location-slug="%s" %s>', $div_id, $description, $slug, $class );
		$output .= "\n\t";
		$output .= wp_sprintf( '<a title="%s">%s</a>', $description, ucwords( $name ) );
		$output .= "\n\t";
		$output .= wp_sprintf( '<a href="tel:+1-%s" itemprop="telephone">%s</a>', $address['phone'], $address['phone'] );
		$output .= "\n";
		$output .= wp_sprintf( '</div><!-- end div.dataset.locationSlug%s -->', $slug );
		$output .= "\n";
		return $output;
	}

	/**
	 * Get a single locations details and output as html.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_location_pin( int $term_id ) {
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
		$branch  = 'nat' === $slug ? 'Jones Sign Co.' : ucwords( $name );
		$output  = '';
		$output .= wp_sprintf( '<a class="material-icons %s" data-slug="%s">location_on</a>', $slug, $slug );
		$output .= "\n";
		return $output;
	}

	/**
	 * Count the locations.
	 */
	public function count_jones_locations() {
		return count( $this->get_location_ids(75, 72 ) );
	}

	/**
	 * Get the map of all the locations.
	 */
	public function get_locations_mapped() {
		$locations = $this->get_location_ids( 75, 72 );
		$total     = count( $locations );

		$output  = '<div class="map">';
		$output .= '<div class="jones_facility_pins">';

		// Map Pushpins.
		foreach ( $locations as $location ) {
			$output .= $this->get_location_pin( $location ) . "\n";
		}
		$output .= '</div><!-- end div.jones_facility_pins -->';
		// SVG graphic of the United States.
		$output .= Baseline::get_map_usa();

		$output .= '</div><!-- end div.map -->';
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
			'id'           => 'locationRectangular',
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
		// Square Image.
		$args = [
			'name'         => 'Square Image',
			'show_names'   => true,
			'id'           => 'locationSquare',
			'type'         => 'file',
			'options'      => [ 'url' => false ],
			'text'         => [ 'add_upload_file_text' => 'Upload or Find Square Image' ],
			'query_args'   => [
				'type' => [ 'image/jpg', 'image/jpeg' ],
			],
			'preview_size' => 'medium',
		];
		$metabox->add_field( $args );

		$args = [
			'name'         => 'cinematic Image',
			'show_names'   => true,
			'id'           => 'locationCinematic',
			'type'         => 'file',
			'options'      => [ 'url' => false ],
			'text'         => [ 'add_upload_file_text' => 'Upload 16x9 location image' ],
			'query_args'   => [
				'type' => [ 'image/jpg', 'image/jpeg' ],
			],
			'preview_size' => 'medium',
		];
		$metabox->add_field( $args );

		$args = [
			'name'         => 'vertical Image',
			'show_names'   => true,
			'id'           => 'locationVertical',
			'type'         => 'file',
			'options'      => [ 'url' => false ],
			'text'         => [ 'add_upload_file_text' => 'Upload 3x4 location image' ],
			'query_args'   => [
				'type' => [ 'image/jpg', 'image/jpeg' ],
			],
			'preview_size' => 'medium',
		];
		$metabox->add_field( $args );
		$args = [
			'name'         => 'rectangular Image',
			'show_names'   => true,
			'id'           => 'locationRectangular',
			'type'         => 'file',
			'options'      => [ 'url' => false ],
			'text'         => [ 'add_upload_file_text' => 'Upload 4x3 location image' ],
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
			'type'       => 'wysiwyg',
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
	 * Retrieve the taxonomy meta for 'locationRectangular' for this jones sign location.
	 *
	 * @param int  $term_id Location Taxonomy id.
	 * @param bool $return_as_url Whether to return as the url or the id. Defaults to false, which is ID.
	 *
	 * @return int $output The id of the location's photo.
	 */
	public function get_location_image( $term_id, $return_as_url = false ) {
		$key    = $return_as_url ? 'locationRectangular' : 'locationRectangular_id';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'locationCinematic' for this jones sign location.
	 *
	 * @param int  $term_id Location Taxonomy id.
	 * @param bool $return_as_url Whether to return as the url or the id. Defaults to false, which is ID.
	 *
	 * @return int $output The id of the location's city photo.
	 */
	public function get_city_image( $term_id, $return_as_url = false ) {
		$key    = $return_as_url ? 'locationCinematic' : 'locationCinematic_id';
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
		$handle    = 'jonessign-general-scripts'; // script handle.
		$path      = get_theme_file_uri( '/assets/js/jonessign.min.js' ); // path to script.
		$deps      = []; // dependencies.
		$version   = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/jonessign.min.js' ) ); // script version.
		$in_footer = false; // Do we enqueue the script into the footer -- no.
		wp_enqueue_script( $handle, $path, $deps, $version, $in_footer );
		wp_script_add_data( $handle, 'defer', false ); // if true - wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
		wp_localize_script( $handle, 'jonessignInfo', [
			'locations' => $loc_data,
		] );

		$handle      = 'jones-locations-classie'; // script handle.
		$script_path = get_theme_file_uri( '/assets/js/classie.min.js' ); // path to script.
		$deps        = []; // dependencies.
		$version     = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/classie.min.js' ) ); // script version.
		$in_footer   = false; // Do we enqueue the script into the footer.
		wp_register_script( $handle, $script_path, $deps, $version, $in_footer );

		$handle      = 'jones-select-alternatives'; // script handle.
		$script_path = get_theme_file_uri( '/assets/js/select_alternative.min.js' ); // path to script.
		$deps        = []; // dependencies.
		$version     = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/select_alternative.min.js' ) ); // script version.
		$in_footer   = false; // Do we enqueue the script into the footer.
		wp_enqueue_script( $handle, $script_path, $deps, $version, $in_footer );
		wp_script_add_data( $handle, 'defer', false ); // wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
	}

	/**
	 * Enqueue D3 - a data visualization library for Javascript.
	 */
	public function action_enqueue_d3() {

		$handle    = 'd3-data-visualization'; // script handle.
		$path      = 'https://cdnjs.cloudflare.com/ajax/libs/d3/6.6.0/d3.min.js'; // path to script.
		$version   = 6;
		$in_footer = false; // Do we enqueue the script into the footer -- no.

		wp_register_script( $handle, $path, $deps = [], $version, $in_footer );
		wp_script_add_data( $handle, 'defer', false ); // if true - wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
		wp_enqueue_script( $handle, $path, [], $version, false );
	}

	/**
	 * EnqueueSmoothstate JS for page transitions.
	 */
	public function action_enqueue_smoothstate() {

		$handle    = 'smoothstate-js'; // script handle.
		$path      = 'https://cdnjs.cloudflare.com/ajax/libs/smoothState.js/0.7.2/jquery.smoothState.min.js'; // path to script.
		$version   = 7;
		$dependencies = [ 'jquery' ];
		$in_footer = false; // Do we enqueue the script into the footer -- no.

		wp_register_script( $handle, $path, $dependencies, $version, $in_footer );
		wp_script_add_data( $handle, 'defer', false ); // if true - wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
		wp_enqueue_script( $handle, $path, [], $version, false );
	}


	/**
	 * Output entire rich snippet for a location.
	 *
	 * @param int $location Term id of the location. Defaults to 60 - which is Jones Green Bay.
	 * @link https://search.google.com/structured-data/testing-tool
	 */
	public function get_location_schema( $location = 60 ) {
		$jones_url    = $this->default_jones_url;
		$company_name = $this->full_company_name;
		$facebook     = $this->facebook;
		$twitter_url  = $this->twitter_url;
		$linkedin     = $this->linkedin;
		$about_jones  = $this->about_jones;
		$slogan       = $this->slogan;
		$info         = $this->get_location_info( $location );
		[
			'location_image_id' => $location_img,
			'name'              => $name,
			'slug'              => $slug,
			'address'           => $address,
		]             = $this->get_location_info( $location );

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
	/**
	 * Output an SVG With the icon for Jones Sign Company.
	 *
	 * @param string $type Icon defaults to sign logo, but any other string gives us the letter option.
	 */
	public function get_jones_icon( $type = 'sign' ) {
		$style = '
		<style type="text/css">
		circle {
			fill: var(--circle-fill, #0273b9);
			stroke: var(--circle-stroke-color, #0273b9);
			stroke-width: var(--stroke-width, 10);
			stroke-miterlimit: var(--miterlimit, 10);
		}
		path {
			fill: var(--logo-fill, #fcdde6);
		}
		</style>';

		$letter = '
		<svg
		version="1.1"
		id="jones_icon"
		class="icon_letter"
		xmlns="http://www.w3.org/2000/svg"
		xmlns:xlink="http://www.w3.org/1999/xlink"
		x="0px"
		y="0px"
		width="40%"
		viewBox="0 0 500 500"
		style="enable-background:new 0 0 500 500;"
		xml:space="preserve">
		</svg>';
		$pylon  = '
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="pylon_circle" class="jones_icon" x="0px" y="0px" viewBox="0 0 500 500" style="enable-background:new 0 0 500 500;" xml:space="preserve">
		<circle id="background" class="circle_bg" cx="262.9" cy="242.5" r="215.3"/>
		<path id="pylon" class="pylon_icon" d="M430.4,225.9H248.7c-8.1,0-14.8-6.6-14.8-14.8v-18h-18.6c-3.3,8.7-11.9,15.6-22.2,15.9L89.8,370.5 c-4.3-5.9-8.4-12-12.1-18.3l98.1-150c-4.8-4.5-7.8-10.6-7.8-17.7c0-13.5,10.9-24.3,24.5-24.3c11.7,0,21.5,7.8,23.9,19.3H234v-24.8 c0-8.1,6.6-14.8,14.8-14.8h181.6c8.1,0,14.8,6.6,14.8,14.8v56.3C445.1,219.3,438.5,225.9,430.4,225.9z"/> </svg>
		';

		$icon = 'sign' === $type ? $pylon : $letter;
		return $style . $icon;
	}
	/**
	 * Output an SVG with Jones Sign Company written in outlined bank gothic font
	 */
	public function get_jones_logo() {
		$icon = <<<ICO
		<svg
		version="1.1"
		id="jones_logo"
		xmlns="http://www.w3.org/2000/svg"
		xmlns:xlink="http://www.w3.org/1999/xlink"
		x="0px"
		y="0px"
		width="100%"
		viewBox="0 0 490 52.8"
		style="enable-background:new 0 0 490 52.8;"
		xml:space="preserve">
			<path class="logo_letter_fill" id="j" d="M13.5,40.9h19.2V0h13.4v39.8c0,3.3-0.4,7.7-2.8,10.2c-2.4,2.5-6.9,2.8-10.1,2.8H12.9c-3.2,0-7.7-0.3-10.2-2.8 C0.4,47.5,0,43.1,0,39.8V28.9h13.5V40.9z"/>
			<path class="logo_letter_fill" id="o" d="M53.9,20.4c0-2.8,0.4-6.5,2.4-8.5c2.1-2.1,6.1-2.4,8.8-2.4h30c2.7,0,6.7,0.3,8.9,2.4c2.1,2,2.4,5.7,2.4,8.5v21.4 c0,2.8-0.4,6.5-2.5,8.5c-2.1,2.1-6.1,2.4-8.8,2.4h-30c-2.8,0-6.7-0.3-8.8-2.4c-2.1-2-2.4-5.7-2.4-8.5L53.9,20.4L53.9,20.4z M65.8,42.4h28.8V19.6H65.8C65.8,19.6,65.8,42.4,65.8,42.4z"/>
			<path class="logo_letter_fill" id="n-1" d="M153.4,35.2V9.3h11v43.5h-8.2l-31.1-26v26h-11V9.6h8.1L153.4,35.2z"/>
			<path class="logo_letter_fill" id="e" d="M184.3,19.3v6.3H203v9.5h-18.7v7.4h32.5v10.2h-44.5V9.6h44v9.7H184.3z"/>
			<path class="logo_letter_fill" id="s-1" d="M231.7,42.6h26.1v-6.7h-23.9c-2.8,0-6.7-0.3-8.9-2.4c-2.1-2-2.5-5.7-2.5-8.5v-4.5c0-2.8,0.4-6.4,2.5-8.5 c2.2-2.1,6.1-2.4,8.9-2.4h23c2.8,0,6.6,0.3,8.8,2.3c2.1,1.9,2.6,5.1,2.6,7.8v1.9l-10.6,0v-2.2h-24.2v6h24c2.7,0,6.7,0.3,8.8,2.3 c2.1,2,2.4,5.7,2.4,8.5v5.6c0,2.8-0.4,6.5-2.5,8.5c-2.1,2.1-6,2.4-8.8,2.4h-25.1c-2.8,0-6.8-0.3-8.9-2.3c-2.1-2-2.5-5.7-2.5-8.6 v-2.2l10.8,0L231.7,42.6L231.7,42.6L231.7,42.6z"/>
			<path class="logo_letter_fill" id="s-2" d="M340.6,11.9h-28.5V20h28.2c3.2,0,7.8,0.4,10.2,2.8c2.4,2.5,2.8,6.8,2.8,10.2v6.8c0,3.3-0.4,7.7-2.8,10.2 c-2.4,2.5-7,2.8-10.2,2.8h-29.2c-3.2,0-7.8-0.4-10.2-2.8c-2.4-2.5-2.8-6.8-2.8-10.2V38l12.1,0v2.9h31v-8.8H313 c-3.2,0-7.7-0.4-10.2-2.8c-2.4-2.5-2.8-6.9-2.8-10.2v-5.5c0-3.3,0.4-7.7,2.8-10.2c2.4-2.5,7-2.8,10.2-2.8h26.8c3.2,0,7.5,0.4,10,2.7 c2.4,2.3,2.9,6.2,2.9,9.5l0,4.5l-12.1,0L340.6,11.9L340.6,11.9L340.6,11.9z"/>
			<path class="logo_letter_fill" id="i" d="M361.4,52.8V9.3h11.8v43.5H361.4z"/>
			<path class="logo_letter_fill" id="g" d="M420.1,19.6h-27v22.8h27v-6.1l-11.9,0l-0.1-9.8h23.7v15.4c0,2.8-0.4,6.5-2.5,8.5c-2.1,2.1-6.1,2.4-8.8,2.4h-28 c-2.8,0-6.7-0.3-8.8-2.4c-2.1-2-2.5-5.7-2.5-8.5V20.4c0-2.8,0.4-6.5,2.5-8.5c2.1-2.1,6.1-2.4,8.8-2.4h28c2.7,0,6.6,0.3,8.8,2.3 c2.1,1.9,2.5,5.3,2.5,8.1l0.2,2.8l-11.8-0.1L420.1,19.6z"/>
			<path class="logo_letter_fill" id="n-2" d="M479,35.2V9.3l10.6,0l0.3,43.5h-8.2l-31.1-26v26h-11V9.6h8.1L479,35.2z"/>
		</svg>
ICO;
		return $icon;
	}

	/**
	 * Get years in business programatically, so you won't need to update this yearly.
	 *
	 * @param int $opened Year Jones Sign Company opened.
	 */
	public function get_years_in_business( $opened = 1910 ) {
		return date( 'Y' ) - $opened;
	}
	/**
	 *
	 * Return a picture html element from the id of the picture.
	 *
	 * @param int  $image_id The ID of the preferred image.
	 * @param bool $widescreens If true, picture gets a class of 'hide-until-wide', if false, class is 'hide-on-wide'.
	 *
	 * @return string $output - the HTML - will need to echo.
	 */
	public function get_picture_element( $image_id, $widescreens = false ) {
		$picture_class          = $widescreens ? 'hide-until-wide' : 'hide-on-wide';
		$picture_meta           = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
		$picture_cropped_info   = wp_get_attachment_image_src( $image_id, 'large' );
		$picture_breakpoint     = absint( $picture_cropped_info[1] ) + 1;
		$picture_full_srcset    = wp_get_attachment_image_srcset( $image_id, 'full' );
		$picture_full_sizes     = wp_get_attachment_image_sizes( $image_id, 'full' );
		$picture_cropped_srcset = wp_get_attachment_image_srcset( $image_id, 'medium' );
		$picture_cropped_sizes  = wp_get_attachment_image_sizes( $image_id, 'medium' );
		$output                 = <<<OUTP
		<picture class="$picture_class">
			<div class="picture_overlay"></div>
			<source
				media="(min-width: $picture_breakpoint)"
				srcset="$picture_full_srcset"
		 		sizes="$picture_full_sizes" />
			<img
				srcset="$picture_cropped_srcset"
				alt="$picture_meta"
				sizes="$picture_full_sizes" />

		</picture>
OUTP;
		return $output;

	}
	/**
	 * Company aspects component.
	 *
	 * @param array $aspect An array of a single jones sign company aspect.
	 */
	public function get_aspect_card( $aspect ) {
		[
			'title' => $title,
			'cta'   => $cta,
			'image' => $image,
			'desc'  => $desc,
			'url'   => $url,
			'style' => $style,
		]        = $aspect;
		$output  = '';
		$output .= <<<ASPCT
		<div class="aspect $style">
			<div class="aspect-image"> <img src="$image" alt="$title" /> </div>
			<div class="aspect-description">
				<h3 class="aspect-title">$title</h3>
				<p>$desc</p>
				<a class="readmore" data-itemid="" href="$url">$cta</a>
			</div><!-- end aspectdescription -->
		</div><!-- end aspect.$style -->
ASPCT;
		return $output;
	}

	/**
	 * Include a PayTrace link.
	 *
	 * @return string HTML for a link to paytrace.
	 */
	public function add_paytrace_link() {
		$paytrace = <<<PAYTRACE
		<section class="container-fluid">
		<div class="row">
			<button
			type="submit"
			onclick="window.location.href='https://paylink.paytrace.com?m=80574&amount=&invoice='"
			style='border: 2px solid #0273b9;
			border-radius: 7px;
			height: 38px;
			width: 160px;
			color: white;
			font-weight: bold;
			background-color:#0273b9;'>Make Payment</button><!--Thank you for using PayTrace.com-->
		</div>
	</section>
PAYTRACE;
		return $paytrace;
	}

	/**
	 * Following a tutorial to learn how to do ajax requests for pages.
	 *
	 * @link https://wpmudev.com/blog/load-posts-ajax/
	 */
/*
public function action_enqueue_ajax_experiment() {
		global $wp_query;
		$handle       = 'jones-ajax-pagination';
		$path         = get_theme_file_uri( '/assets/js/ajax-pagination.min.js' );
		$dependencies = [ 'jquery' ];
		$version      = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/ajax-pagination.min.js' ) ); // script version.
		$in_footer    = true;
		wp_enqueue_script( $handle, $path, $dependencies, $version, $in_footer );
*/
		/**
		 * Give the AJAX a url to work with.
		 */
/*
wp_localize_script(
			$handle,
			'ajaxpagination',
			[
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'query_vars' => json_encode( $wp_query->query )
			]
		);
	}
*/
	/**
	 * MORE AJAX TESTING.
	 *
	 * @link https://wpmudev.com/blog/load-posts-ajax/
	 * @note: The function itself can contain anything you’d like.
	 * You can log out users, grab their data, publish a post and so on.
	 * Whatever you want to return to Javascript YOU MUST ECHO.
	 */
/*	public function my_ajax_pagination() {
		$query_vars          = json_decode( stripslashes( $_POST['query_vars'] ), true );
		$query_vars['paged'] = $_POST['page'];

		$posts = new WP_Query( $query_vars );
		$GLOBALS['wp_query'] = $posts;
		add_filter( 'editor_max_image_size', [ $this, 'my_image_size_override' ] );

		if( ! $posts->have_posts() ) {
			get_template_part( 'content', 'none' );
		}
		else {
			while ( $posts->have_posts() ) {
				$posts->the_post();
				get_template_part( 'content', get_post_format() );
			}
		}
		remove_filter( 'editor_max_image_size',  [ $this, 'my_image_size_override' ] );

		the_posts_pagination( array(
			'prev_text'          => __( 'Previous page', 'twentyfifteen' ),
			'next_text'          => __( 'Next page', 'twentyfifteen' ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyfifteen' ) . ' </span>',
		) );

		die();
	}
*/
	/**
	 * USED WITHIN ABOVE FUNCTION
	 */
/*	public function my_image_size_override() {
		return array( 825, 510 );
	}
*/

/**
 * Swap out existing archive title to not include the taxonomy name beforehand.
 *
 * @param string $title The title you'd otherwise get from WordPress.
 * @link https://developer.wordpress.org/reference/functions/get_the_archive_title/
 * @note uses get_the_archive_title as a filter
 */
public function update_the_archive_title( string $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		}

		return ucwords( $title );
	}

	/**
	 * Get header_cta_button.
	 *
	 * @param string $url   Link for the button.
	 * @param string $front Front text for the button.
	 * @param string $back  Back text for the button.
	 */
	public function get_header_cta_button( $url = '#', $front = 'Learn More', $back = 'Within' ) {
		$output = <<<CTABUTTON
<div class="cta__button-container">
<a href="$url" class="btn-flip-wrap" title="$front">
<span class="btn btn-flip-front">$front</span>
<span class="btn btn-flip-back">$back</span>
</a>
</div><!-- end div.cta__button-container -->
CTABUTTON;
		return $output;
	}
	/**
	 * Get header figure caption heading.
	 */
	public function get_header_heading( $headline = 'We do the work', $nextline = 'Our Clients do our Advertising.' ) {
		$output = <<<FIGHEAD
		<div class="heading">
			<span>$headline</span>
			<span>$nextline</span>
		</div><!--end cta div.heading -->
FIGHEAD;
		return $output;
	}
/**
 * Get header figure figcaption.
 */
public function get_header_figcaption() {
	$header_heading    = $this->get_header_heading();
	$header_cta_button = $this->get_header_cta_button();
$output = <<<HEADFIGCAPTION
	<figcaption>
		<div class="call-to-action">
		$header_heading
		$header_cta_button
		</div><!-- end div.call-to-action -->
	</figcaption>
HEADFIGCAPTION;

	return $output;
}



	/**
	 * Get the header element for the front page of the website.
	 *
	 * @param int $horizontal_id ID of the horizontal image for the horizontal version of the header.
	 * @param int $vertical_id   ID of the horizontal image for the vertical version of the header.
	 */
	public function get_frontpage_header( $horizontal_id = 659, $vertical_id = 809 ) {
		$vertical_image   = wp_get_attachment_image( $vertical_id, 'medium_large', false, [ 'class' => 'vertical-header-image', 'loading' => false ] );
		$horizontal_image = wp_get_attachment_image( $horizontal_id, 'medium_large', false, [ 'class' => 'horizontal-header-image', 'loading' => false ] );
		$figcaption       = $this->get_header_figcaption();

		// Guard Return if it isn't the home page OR if it isn't the front page of the site.
		if ( ! is_home() OR ! is_front_page() ) {
			return;
		}

		$html = '';
		$html .= '<header class="frontpage">';
		// $html .= '<figure class="vertical-header grid-hide-grid">';
		$html .= '<figure>';
		$html .= $vertical_image;
		$html .= $horizontal_image;
		$html .= '<div class="figure_overlay"> </div>';
		$html .= $figcaption ;
		$html .= '</figure>';
		$html .= '<!-- only shows on small and wide screens -->';
		// $html .= '<figure class="horizontal-header hide-grid-hide">';
		// $html .= '<div class="figure_overlay"> </div>';
		// $html .= $figcaption ;
		// $html .= '</figure>';
		$html .= '</header>';

		return $html;
	}

	/**
	 * Default args for the header.
	 *
	 * @access   public
	 */
	public function get_header_default_args() {
		global $post;
		$is_homepage = 699 === $post->ID ? true : false;

		return [
			'vertical_image_id'   => 809,
			'horizontal_image_id' => 659,
			'cta_headline' => 'We do the Work',
			'cta_nextline' => 'Our Clients do our Advertising.',
			'button' => [
				'url'      => '#',
				'frontext' => 'Learn More',
				'backtext' => 'Continue',
			],
		];
	}
	/**
	 * Get a generic header element.
	 *
	 * @param array $args Array of arguments to put inside the header.
	 */
	public function get_general_header( $args ) {
		global $post;
		global $wp_query;

		$is_homepage  = 699 === $post->ID ? true : false;
		$post_type    = $post->post_type;

		$default_args = $this->get_header_default_args();
		$final_args   = wp_parse_args( $args, $default_args );

		[
			'vertical_image_id'   => $vertical_id,
			'horizontal_image_id' => $horizontal_id,
			'cta_headline'        => $headline,
			'cta_nextline'        => $nextline,
			'button' => [
				'url'      => $button_url,
				'frontext' => $frontext,
				'backtext' => $backtext,
			],
		] = $final_args;

		if ( 'project' === $post_type && is_single() ) {
			$vertical_id   = get_post_meta( $post->ID, 'projectVerticalImage_id', true ) ?? $vertical_id;
			$horizontal_id = get_post_thumbnail_id( $post->ID ) ?? $horizontal_id;
			$headline      = get_the_title( $post->ID );
			[
				'city' => $city,
				'state' => $state
			] = get_post_meta( $post->ID, 'projectLocation', true );
			$nextline = ucwords( $city ) . ', ' . strtoupper( $state );
		}

		// Worth editing to have a custom photo for each post type;
		if ( 'project' === $post_type && is_archive() ) {
			$vertical_id   = get_post_meta( $post->ID, 'projectVerticalImage_id', true ) ?? $vertical_id;
			$horizontal_id = get_post_thumbnail_id( $post->ID ) ?? $horizontal_id;
			$headline      = $wp_query->get_queried_object()->label;
			$nextline      = preg_replace( '/and/', '&amp;', $wp_query->get_queried_object()->description );
		}

		if ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			$tax = $term->taxonomy;
			$headline = ucwords( $term->name );
			$nextline = '';
			$vertical_id = get_term_meta( $term->term_id, $term->taxonomy . 'Vertical_id', true ) ?? $vertical_id;
			$horizontal_id = get_term_meta( $term->term_id, $term->taxonomy . 'Cinematic_id', true ) ?? $horizontal_id;
		}

		$vertical_image   = wp_get_attachment_image( $vertical_id, 'medium_large', false, [ 'class' => 'vertical-header-image', 'loading' => false ] );
		$horizontal_image = wp_get_attachment_image( $horizontal_id, 'medium_large', false, [ 'class' => 'horizontal-header-image', 'loading' => false ] );


		$html = '';
		$html .= '<header>';
		// $html .= '<figure class="vertical-header grid-hide-grid">';
		$html .= '<figure>';
		$html .= $vertical_image;
		$html .= $horizontal_image;
		$html .= '<div class="figure_overlay"> </div>';
$html .= '<figcaption>';
$html .= '<div class="call-to-action">';
$html .= '<div class="heading">';
$html .= "<span>$headline</span>";
$html .= "<span>$nextline</span>";
$html .= '</div><!--end cta div.heading -->';
$cta_button = '<div class="cta__button-container">';
$cta_button .= "<a href='$button_url' class='btn-flip-wrap' title='$frontext'>";
$cta_button .= "<span class='btn btn-flip-front'>$frontext</span>";
$cta_button .= "<span class='btn btn-flip-back'>$backtext</span>";
$cta_button .= '</a>';
$cta_button .= '</div><!-- end div.cta__button-container -->';
$html .= $is_homepage ? $cta_button : '';
$html .= '';
$html .= '</div><!-- end div.call-to-action -->';
$html .= '</figcaption>';
		$html .= '</figure>';
		$html .= '<!-- only shows on small and wide screens -->';
		$html .= '</header>';

		return $html;
	}

		/**
	 * Get the masthead for a given page.
	 *
	 * @param array $args Array of arguments to put inside the header.
	 */
	public function get_masthead( $args ) {
		global $post;
		global $wp_query;

		$is_homepage  = 699 === $post->ID ? true : false;
		$post_type    = $post->post_type;

		$default_args = $this->get_header_default_args();
		$final_args   = wp_parse_args( $args, $default_args );

		[
			'vertical_image_id'   => $vertical_id,
			'horizontal_image_id' => $horizontal_id,
			'cta_headline'        => $headline,
			'cta_nextline'        => $nextline,
			'button' => [
				'url'      => $button_url,
				'frontext' => $frontext,
				'backtext' => $backtext,
			],
		] = $final_args;

		if ( 'project' === $post_type && is_single() ) {
			$vertical_id   = get_post_meta( $post->ID, 'projectVerticalImage_id', true ) ?? $vertical_id;
			$horizontal_id = get_post_thumbnail_id( $post->ID ) ?? $horizontal_id;
			$headline      = get_the_title( $post->ID );
			[
				'city' => $city,
				'state' => $state
			] = get_post_meta( $post->ID, 'projectLocation', true );
			$nextline = ucwords( $city ) . ', ' . strtoupper( $state );
		}

		// Worth editing to have a custom photo for each post type;
		if ( 'project' === $post_type && is_archive() ) {
			$vertical_id   = get_post_meta( $post->ID, 'projectVerticalImage_id', true ) ?? $vertical_id;
			$horizontal_id = get_post_thumbnail_id( $post->ID ) ?? $horizontal_id;
			$headline      = $wp_query->get_queried_object()->label;
			$nextline      = preg_replace( '/and/', '&amp;', $wp_query->get_queried_object()->description );
		}

		if ( is_tax() ) {
			$term = $wp_query->get_queried_object();
			$tax = $term->taxonomy;
			$headline = ucwords( $term->name );
			$nextline = '';
			$vertical_id = get_term_meta( $term->term_id, $term->taxonomy . 'Vertical_id', true ) ?? $vertical_id;
			$horizontal_id = get_term_meta( $term->term_id, $term->taxonomy . 'Cinematic_id', true ) ?? $horizontal_id;
		}

		$vertical_image   = wp_get_attachment_image( $vertical_id, 'medium_large', false, [ 'class' => 'vertical-header-image', 'loading' => false ] );
		$horizontal_image = wp_get_attachment_image( $horizontal_id, 'medium_large', false, [ 'class' => 'horizontal-header-image', 'loading' => false ] );


		$html = '';
		// $html .= '<div class="header masthead">';
		$html .= '<figure>';
		$html .= $vertical_image;
		$html .= $horizontal_image;
		$html .= '<div class="figure_overlay"> </div>';
$html .= '<figcaption>';
$html .= '<div class="call-to-action">';
$html .= '<div class="heading">';
$html .= "<span>$headline</span>";
$html .= "<span>$nextline</span>";
$html .= '</div><!--end cta div.heading -->';
$cta_button = '<div class="cta__button-container">';
$cta_button .= "<a href='$button_url' class='btn-flip-wrap' title='$frontext'>";
$cta_button .= "<span class='btn btn-flip-front'>$frontext</span>";
$cta_button .= "<span class='btn btn-flip-back'>$backtext</span>";
$cta_button .= '</a>';
$cta_button .= '</div><!-- end div.cta__button-container -->';
$html .= $is_homepage ? $cta_button : '';
$html .= '';
$html .= '</div><!-- end div.call-to-action -->';
$html .= '</figcaption>';
		$html .= '</figure>';
		// $html .= '</div><!-- end div.header.masthead-->';

		return $html;
	}


}//end class
