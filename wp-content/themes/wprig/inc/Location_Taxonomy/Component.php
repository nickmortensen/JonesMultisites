<?php
/**
 * WP_Rig\WP_Rig\Location_Taxonomy\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Location_Taxonomy;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function get_terms;

use function add_filter;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_enqueue_script;
use function wp_localize_script;

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
	 * The standard about us copy for Jones Sign Company.
	 *
	 * @access   public
	 * @var      string    $about_jones The standard about us copy for Jones Sign Company.
	 */
	public $about_jones = ABOUT_US;

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
			'get_corresponding_location'   => [ $this, 'get_corresponding_location' ],
			'get_location_city_photo_url'  => [ $this, 'get_location_city_photo_url' ],
			'get_location_description'     => [ $this, 'get_location_description' ],
			'get_location_links'           => [ $this, 'get_location_links' ],
			'get_locations'                => [ $this, 'get_locations' ],
			'get_location_ids'             => [ $this, 'get_location_ids' ],
			'get_location_name'            => [ $this, 'get_location_name' ],
			'get_term_by_blog'             => [ $this, 'get_term_by_blog' ],
			'get_jones_locations'          => [ $this, 'get_jones_locations' ],
			'get_blend_modes'              => [ $this, 'get_blend_modes' ],
			'get_location_taxonomy'        => [ $this, 'get_location_taxonomy' ],
			'get_location_subdomain'       => [ $this, 'get_location_subdomain' ],
			'get_location_url'             => [ $this, 'get_location_url' ],
			'get_location_capability'      => [ $this, 'get_location_capability' ],
			'get_location_info'            => [ $this, 'get_location_info' ],
			'get_city_image_by_blog'       => [ $this, 'get_city_image_by_blog' ],
			'get_location_image_by_blog'   => [ $this, 'get_location_image_by_blog' ],
			'get_all_possible_information' => [ $this, 'get_all_possible_information' ],
			'get_location_schema'          => [ $this, 'get_location_schema' ],
		];
	}

	/**
	 * Query for all the data and metadata assigned to any of the location taxonomy.
	 *
	 * @param int $term_id The id for the taxonomy term.
	 */
	public function get_location_info( $term_id ) {
		$info                      = [];
		$location                  = get_term( $term_id );
		$info['id']                = $location->term_id;
		$info['name']              = $location->name;
		$info['slug']              = $location->slug;
		$info['tax_id']            = $location->term_taxonomy_id;
		$info['description']       = $location->description;
		$info['common']            = $this->get_location_name( $location->term_id );
		$info['location_image_id'] = $this->get_location_image_by_blog( $location->term_id, false );
		$info['city_image_id']     = $this->get_city_image_by_blog( $location->term_id, false );
		$info['blog_id']           = get_term_meta( $info['id'], 'locationBlogID', true );
		$info['subdomain']         = preg_replace( '/^http:/i', 'https:', $this->get_location_subdomain( $location->term_id ) );
		$info['nimble']            = preg_replace( '/^http:/i', 'https:', $this->get_location_url( $location->term_id ) );
		$info['address']           = $this->get_location_information( $location->term_id );
		$info['capabilities']      = $this->get_location_capability( $location->term_id );
		return $info;
	}

	/**
	 * Output entire rich snippet for a location.
	 *
	 * @link https://search.google.com/structured-data/testing-tool
	 * @param int $location Term id of the location.
	 */
	public function get_location_schema( $location ) {
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

	/**
	 * Output the locations array.
	 */
	public function get_jones_locations() {
		$locations = get_terms( 'location', [ 'hide_empty' => false ] );
		return $locations;
	}

	/**
	 * Gets taxonomy term id by blog id.
	 *
	 * @return int The taxonomy id for location that is equivalent to this blog.
	 */
	public function get_term_by_blog() {
		$blog_id   = get_current_blog_id();
		$locations = $this->get_location_taxonomy();
		$terms     = [];
		$blogs     = [];
		foreach ( $locations as $location ) {
			$terms[] = $location->term_id;
			$blogs[] = get_term_meta( $location->term_id, 'locationBlogID', true );
		}
		$terms_by_blog = array_combine( $blogs, $terms );
		return $terms_by_blog[ $blog_id ];
	}

	/**
	 * Get the city image based on the blog id.
	 *
	 * @param int  $blog The blog id. Default is 1 - which is the Jones Sign Company Blog.
	 * @param bool $return_as_url Whether to return the url of the image. Defaults to false.
	 * @return mixed Either the id of the image or the url of the image -- depending on the $return_as_url parameter.
	 */
	public function get_city_image_by_blog( $blog = 1, $return_as_url = false ) {
		$key = false === $return_as_url ? 'cityImage_id' : 'cityImage';
		$id  = $this->get_term_by_blog( $blog );
		return get_term_meta( $id, $key, true );
	}

	/**
	 * Get the Jones Sign location image id based on the blog id.
	 *
	 * @param int  $blog The blog id. Default is 1 - which is the Jones Sign Company Blog.
	 * @param bool $return_as_url Whether to return the url of the image. Defaults to false.
	 * @return int $city_image_id The city image ID from the jco_termmeta table. Defaults to 1.
	 */
	public function get_location_image_by_blog( $blog = 1, $return_as_url = false ) {
		$key = false === $return_as_url ? 'locationImage_id' : 'locationImage';
		$id  = $this->get_term_by_blog( $blog );
		return get_term_meta( $id, $key, true );

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
	 * Grab all tha taxonomy information for the 'location' taxonomy.
	 */
	public function get_location_taxonomy() : array {
		$locations = get_terms(
			[
				'taxonomy'   => 'location',
				'hide_empty' => false,
			]
		);
		return $locations;
	}

	/**
	 * Get all location term identifiers.
	 */
	public function get_location_ids() : array {
		$ids       = [];
		$locations = $this->get_location_taxonomy();
		foreach ( $locations as $location ) {
			$ids[] = $location->term_id;
		}
		return $ids;
	}

	/**
	 * Get links to all locations - for footer.
	 *
	 * @param array $except The term_id of the location that I don't want to include. Default is empty.
	 */
	public function get_location_links( $except = [] ) {
		$locations = $this->get_location_ids();
		$locations = array_diff( $locations, $except );
		$dont_show = $this->get_term_by_blog( get_current_blog_id() );
		// locations I don't want.
		$links = [];
		foreach ( $locations as $location ) {
			if ( $dont_show === $location ) continue;
			$subdomain   = $this->get_location_subdomain( $location );
			$name        = $this->get_location_name( $location );
			$name        = preg_replace( '/^jones /i', '', get_term( $location )->name );
			$description = $this->get_location_description( $location );
			$links[]     = "<a href=\"$subdomain\" title=\"$description\">$name</a>";
		}

		return implode( '', $links );
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
		/* BlogID */
		$args = [
			'name'        => 'blog id',
			'description' => 'blog_id',
			'id'          => 'locationBlogID',
			'type'        => 'text_small',
			'show_names'  => true,
		];
		$metabox->add_field( $args );

		/* Common Name */
		$args = [
			'name'        => 'general name',
			'description' => 'general name for this location',
			'id'          => 'locationCommonName',
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
			'name'       => 'Location',
			'id'         => 'jonesLocationInfo', // Name of the custom field type we setup.
			'type'       => 'jonesaddress',
			'show_names' => false, // false removes the left cell of the table -- this is worth understanding.
			'after_row'  => '<hr>',
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
		$new['cb']      = '<input type="checkbox" />';
		$new['id']      = 'ID';
		$new['blog_id'] = 'Blog#';
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
			case 'id':
				$output = $term_id;
				break;
			case 'blog_id':
				$blogid     = (int) get_term_meta( $term_id, 'locationBlogID', true );
				$admin_link = preg_replace( '/^http: /i', 'https: ', get_term_meta( $term_id, 'subdomainURL', true ) ) . '/wp-admin/';
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
	 * Retrieve the taxonomy meta for 'jonesLocationInfo' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return array $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_location_information( $term_id ) {
		$key    = 'jonesLocationInfo';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'locationURL' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The domain that nimble gave this website.
	 */
	public function get_location_url( $term_id ) {
		$key    = 'locationURL';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'subdomainURL' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return string $output The sudomain of this location's homepage.
	 */
	public function get_location_subdomain( $term_id ) {
		$key    = 'subdomainURL';
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
	 * Retrieve the taxonomy meta for 'locationImage' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return int $output The id of the location's photo.
	 */
	public function get_location_image( $term_id ) {
		$key    = 'locationImage_id';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'cityImage' for this jones sign location.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return int $output The id of the location's city photo.
	 */
	public function get_location_city_photo( $term_id ) {
		$key    = 'cityImage_id';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 'cityImage' for this jones sign location.
	 *
	 * @param int $blog_identifier Blog id.
	 * @return array $output The data for the location's city photo.
	 */
	public function get_location_city_photo_url( $blog_identifier ) {
		$attachment_id = $this->get_city_image_by_blog( $blog_identifier, false ) ?? 8;
		$size          = 'wp-rig-featured';
		$size          = 'medium';
		$output        = wp_get_attachment_image_src( $attachment_id, $size );
		return $output;
	}

	/**
	 * Get 'location' taxonomy term_id by blog_id.
	 *
	 * @param int $blog_id Blog id.
	 */
	public function get_corresponding_location( $blog_id ) {
		global $wpdb;
		$field_value = 'locationBlogID';
		return $wpdb->get_var( $wpdb->prepare(
			"
				SELECT term_id
				FROM $wpdb->termmeta
				WHERE meta_key = %s
				AND meta_value = %s
			", $field_value, $blog_id
		) );

	}

	/**
	 * Retrieve the taxonomy information - includes the custom taxmeta for a term.
	 *
	 * @param int $term_id Location Taxonomy id.
	 * @return array $output The text of the directory of the project in our Jobs server. - not for public consumption.
	 */
	public function get_all_possible_information( $term_id ) {
		$base_info   = get_term( $term_id );
		$unset_these = [ 'term_group', 'taxonomy', 'previous_term_id', 'prev_termid', 'parent', 'filter' ];
		foreach ( $unset_these as $item ) {
			unset ( $base_info->$item );
		}
//phpcs:disable
		$base_info->jonesLocationInfo    = get_term_meta( $term_id, 'jonesLocationInfo', true );
		$base_info->cityImage            = get_term_meta( $term_id, 'cityImage_id', true );
		$base_info->locationImage        = get_term_meta( $term_id, 'locationImage_id', true );
		$base_info->locationCapabilities = get_term_meta( $term_id, 'locationCapabilities', true );
		$base_info->locationCommonName   = get_term_meta( $term_id, 'locationCommonName', true );
		$base_info->locationURL          = get_term_meta( $term_id, 'locationURL', true );
		$base_info->subdomainURL         = get_term_meta( $term_id, 'subdomainURL', true );
//phpcs:enable

		return $base_info;
	}


	/**
	 * Output all the Jones Location information into a json-encoded array to use on any of the sites -- saves calls to the database.
	 *
	 * @see the only reason to run this, would be that I've added new information to any of the items within the location taxonomy.
	 */
	public function action_enqueue_locations_script() {
		$location_ids = $this->get_location_ids();
		$locations    = [];
		foreach ( $location_ids as $identifier ) {
			$locations[] = $this->get_location_info( $identifier );
		}
		$loc_data  = wp_json_encode( $locations );
		$handle    = 'jones-locations-data'; // script handle.
		$path      = get_theme_file_uri( '/assets/js/locations.min.js' ); // path to script.
		$deps      = []; // dependencies.
		$version   = wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/locations.min.js' ) ); // script version.
		$in_footer = false; // Do we enqueue the script into the footer -- no.
		wp_enqueue_script( $handle, $path, $deps, $version, $in_footer );
		wp_script_add_data( $handle, 'defer', true ); // wait until everything loads -- since this will be in the footer (locations data), I would think I could wait to load it.
		wp_localize_script( $handle, 'locationInfo', [
			'locations' => $loc_data,
		] );
	}


}//end class
