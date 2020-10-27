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
		$info['Indepth']           = get_term_meta( $term_id, 'locationinDepth', true ) || '';
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

	 * @param array $except The term_id of the location that I don't want to include. Default 75 is 'Denver'..
	 */
	public function get_location_taxonomy( $except = [ 75 ] ) : array {
		return Taxonomies::get_all_terms_in_taxonomy( 'location', false );
	}

	/**
	 * Get all location term identifiers.
	 *
	 * @param array $except The term_id of the location that I don't want to include. Default 75 is 'Denver'..
	 */
	public function get_location_ids( $except = [ 75 ] ) : array {
		return array_diff( Taxonomies::get_all_term_ids_from_slug( 'location' ), $except );
	}

	/**
	 * Get a single location link.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_single_location_link( $term_id ) {
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
		] = wp_rig()->get_location_info( $term_id );
		$output  = '';
		$output .= wp_sprintf( '<a class="location_link" title="%s" data-blog-identifier="%s" href="%s">%s</a>', $description, $blog, $subdomain, ucwords( explode( ' ', $name, 2 )[1] ) );
		return $output;
	}


	/**
	 * Get a single location address as an html element.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_location_option( $id ) {
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
		]       = wp_rig()->get_location_info( $id );
		$name   = 72 === $id ? 'Jones Sign Company' : $name;
		$output = wp_sprintf( '<option value="%s" data-location-id="%d"%s>%s</option>', $slug, $id, selected( $id, 72, false ), ucwords( $name ) );
		return $output;
	}

	/**
	 * Get a single location address as an html element.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function output_single_location_address( $term_id ) {
		[
			'id'           => $id,
			'name'         => $name,
			'slug'         => $slug,
			'blog_id'      => $blog,
			'address'      => $address,
			'capabilities' => $capabilities,
		] = wp_rig()->get_location_info( $term_id );
		$output  = '';
		$output .= wp_sprintf( '<address><span itemprop="streetAddress">%s </span>', $address['address'] );
		$output .= wp_sprintf( '<span itemprop="addressLocality">%s</span>, ', $address['city'] );
		$output .= wp_sprintf( '<span itemprop="addressRegion">%s</span>', $address['state'] );
		$output .= wp_sprintf( '<span itemprop="postalCode"> %s</span>', $address['zip'] );
		$output .= '</address>';

		return $output;

	}

	/**
	 * Get a single locations details and output as html.
	 *
	 * @param int $term_id The term id of the location.
	 */
	public function get_single_location_details( $term_id ) {
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
		$output .= wp_sprintf( '<h2>%s</h2>', ucwords( $name ) );
		$output .= $this->output_single_location_address( $term_id );
		$output .= wp_sprintf( '<a href="tel:+1-%s" itemprop="telephone">%s</a>', $address['phone'], $address['phone'] );
		$output .= '</div><!-- end div.location_details -->';
		return $output;
	}

	/**
	 * Get links to all locations - for footer.
	 *
	 * @param array $except The term_id of the location that I don't want to include. Default 75 is 'Denver' and 72 is National.
	 */
	public function get_location_links( $except = [ 75, 72 ] ) {
		global $blog_id;
		$all_links = [];
		// Don't want Jones National, Jones Denver, or the current site.
		$except[] = $this->get_terms_blogs_array( 'blog' )[ $blog_id ];
		// Utilize array_unique to ensure no duplicate term_ids.
		$locations = $this->get_location_ids( array_unique( array_values( $except ) ) );
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
		wp_script_add_data( $handle, 'defer', true ); // wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
		wp_localize_script( $handle, 'jonesignInfo', [
			'locations' => $loc_data,
		] );
	}

	/**
	 * Output entire rich snippet for a location.
	 *
	 * @link https://search.google.com/structured-data/testing-tool
	 * @param int $location Term id of the location. Defaults to 60 - which is Jones Green Bay
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
