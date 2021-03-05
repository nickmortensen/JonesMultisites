<?php
/**
 * WP_Rig\WP_Rig\TaxonomyGlobal\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\TaxonomyGlobal;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Query;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function get_terms;
use function get_term;
use function get_term_meta;
use function term_description;
use function register_taxonomy;
use function wp_sprintf;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_enqueue_script;
use function wp_script_add_data;
use function wp_localize_script;

/**
 * Class to create and use custom taxonomy terms to Jones Sign Company.
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
	private $slug = 'globaltaxonomy';

	/**
	 * An Associative array containing all taxonomies using the singular name as the key.
	 *
	 * @access   public
	 * @var      array    $taxonomies All custom taxonomies using the singular name as the key.
	 */
	public $taxonomies = [
		'signtype'  => 'signtypes',
		'industry'  => 'industries',
		'location'  => 'locations',
		'expertise' => 'expertises',
	];

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'globaltaxonomy';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		/**
		 * If I ever decide to use this as a must use plugin, I will need this action.
		 * add_action( 'muplugins_loaded', [ $this, 'global_taxonomies' ] );
		 */
		add_action( 'plugins_loaded', [ $this, 'global_taxonomies' ] );
		add_action( 'wp_loaded', [ $this, 'global_taxonomies' ] );
		add_action( 'template_redirect', [ $this, 'global_taxonomies' ] );
		add_action( 'init', [ $this, 'global_taxonomies' ] );
		add_action( 'registered_taxonomy', [ $this, 'global_taxonomies' ] );
		add_action( 'switch_blog', [ $this, 'global_taxonomies' ] );
		add_action( 'init', [ $this, 'instantiate_the_taxonomies' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_related_images_javascript' ], 30 );
		add_action( 'edit-tags.php', [ $this, 'edit_signtype_taxonomy_onload' ] );
	}

	/**
	 * Gets template tags to EXPOSE as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [
			'get_all_terms_in_taxonomy'  => [ $this, 'get_all_terms_in_taxonomy' ],
			'get_all_term_ids_from_slug' => [ $this, 'get_all_term_ids_from_slug' ],
			'get_taxonomy_term_links'    => [ $this, 'get_taxonomy_term_links' ],
			'check_taxonomy_term_images' => [ $this, 'check_taxonomy_term_images' ],
			'get_card_taxonomy_checkbox' => [ $this, 'get_card_taxonomy_checkbox' ],
			'get_card_taxonomy_row'      => [ $this, 'get_card_taxonomy_row' ],
			'get_related_images'         => [ $this, 'get_related_images' ],
			'get_related'                => [ $this, 'get_related' ],
			'get_term_hyperlink'         => [ $this, 'get_term_hyperlink' ],
		];
	}

	/**
	 * Create the taxonomies: 'signtype', 'industries', 'expertise', & 'location'.
	 */
	public function instantiate_the_taxonomies() {
		$taxonomies = $this->create_taxonomies();
		foreach ( $taxonomies as $taxonomy ) {
			register_taxonomy( $taxonomy['singular'], $taxonomy['objects'], $taxonomy['args'] );
		}
	}

	/**
	 * Adds a pingback url auto-discovery header for singularly identifiable articles.
	 *
	 * Replaces the prefix jco2_, jco3_, etc. with 'jco_'.
	 */
	public function global_taxonomies() {
		global $wpdb;
		$wpdb->terms         = 'jco_terms';
		$wpdb->term_taxonomy = 'jco_term_taxonomy';
		$wpdb->termmeta      = 'jco_termmeta';
	}
	/**
	 * Output a term array for a given custom taxonomy.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 * @param bool   $hide_empty    Whether we should retreive the terms that have nothing assigned to them.
	 */
	public static function get_all_terms_in_taxonomy( $taxonomy_slug, $hide_empty = false ) : array {
		$terms = get_terms( $taxonomy_slug, [ 'hide_empty' => $hide_empty ] );
		return $terms;
	}

	/**
	 * Get all taxonomy term ids by providing the slug.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 */
	public static function get_all_term_ids_from_slug( $taxonomy_slug ) : array {
		$ids   = [];
		$items = static::get_all_terms_in_taxonomy( $taxonomy_slug );
		foreach ( $items as $item ) {
			$ids[] = $item->term_id;
		}
		return $ids;
	}

	/**
	 * Output a row for a single card within the project showcase area.
	 *
	 * @param int $term The term id.
	 * @link https://codepen.io/nickmortensen/pen/yLOrxbW?editors=1100.
	 */
	public static function get_card_taxonomy_checkbox( $term ) {
		$taxonomy    = get_term( $term )->taxonomy;
		$name        = get_term( $term )->name;
		$description = get_term( $term )->description;
		$slug        = get_term( $term )->slug;
		$link        = WP_HOME . '/' . $taxonomy . '/' . $slug . '/';
		$output      = '<div class="checkbox">';
		$output     .= wp_sprintf( '<input type="checkbox" data-taxid="%s" id="%s" checked="checked"/>', $slug, $name );
		$output     .= wp_sprintf( '<label for="%1$s"><a class="cb-label" href="%2$s" title="link to the %1$s archive page">%1$s</a></label>', $name, $link );
		$output     .= wp_sprintf( '</div><!-- end checkbox for %s -->', $name );

		return $output;
	}

	/**
	 * Output a row for a single card on the project showcase component.
	 *
	 * @param int $terms The term id.
	 * @link https://codepen.io/nickmortensen/pen/yLOrxbW?editors=1100.
	 * @todo Links don't work within labels, may have to use a different approach
	 */
	public static function get_card_taxonomy_row( $terms ) {
		$terms      = count( $terms ) > 2 ? array_slice( $terms, 0, 2 ) : $terms;
		$taxonomy   = get_term( $terms[0] )->taxonomy;
		$checkboxes = [];
		$output     = '';

		foreach ( $terms as $term ) {
			$checkboxes[] = self::get_card_taxonomy_checkbox( $term );
		}
		$output .= wp_sprintf( '<div class="project_%s contains-checkboxes">', $taxonomy );
		$output .= implode( '', $checkboxes );
		$output .= wp_sprintf( '</div><!-- end div.project_%s -->', $taxonomy );
		return $output;
	}


	/**
	 * Get links to all terms.
	 *
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 * @param array  $except Array of ids from the taxonomy I don't want to get.
	 */
	public static function get_taxonomy_term_links( $taxonomy_slug, $except = [] ) {
		$items = static::get_all_term_ids_from_slug();
		$links = [];
		foreach ( $items as $item ) {
			if ( $except === $item ) {
				continue;
			}
			$links[] = sprintf( '<a href="#" title="%s">%s</a>', $item->description, $item->name );
		}
		return implode( '', $links );
	}

	/**
	 * Check to see whether there are images attached to this taxonomy term in the given sizes.
	 *
	 * @param string $term_id The ID of the term you'd like to check on the image needs of.
	 * @param string $taxonomy_slug The slug of the particular taxonomy. 'signtype', 'expertise', 'location' are the choices.
	 */
	public static function check_taxonomy_term_images( $term_id, $taxonomy_slug ) {
		$options = [ 'vertical', 'cinematic', 'rectangular', 'square' ];
		$needs   = [];
		$return_ = [];
		foreach ( $options as $size ) {
			if ( ! get_term_meta( $term_id, $taxonomy_slug . ucfirst( $size ), true ) ) {
				$needs[] = $size;
			}
		}
		$return['color']   = '--indigo-600';
		$return['message'] = 'has all photos';
		if ( $needs ) {
			$return['color']   = '--gray-400';
			$return['message'] = 'need image types: ' . implode( ' | ', $needs );
		}
		return $return;
	}

	/**
	 * Build the labels for a taxonomy.
	 *
	 * @param string $singular The singular name for the taxonomy.
	 * @param string $plural The plural name for the taxonomy.
	 */
	private function build_taxonomy_labels( $singular, $plural ) : array {
		$p = ucfirst( $plural );
		$s = ucfirst( $singular );
		return [
			'name'                       => $p,
			'singular_name'              => $s,
			'menu_name'                  => $p,
			'all_items'                  => 'All' . $p,
			'parent_item'                => 'Main',
			'parent_item_colon'          => 'Main ' . $s,
			'new_item_name'              => 'New ' . $s,
			'add_new_item'               => 'Add New ' . $s,
			'edit_item'                  => 'Edit ' . $s . ' tag',
			'update_item'                => 'Update ' . $s,
			'view_item'                  => 'View ' . $s,
			'separate_items_with_commas' => 'Separate ' . $p . ' w/commas',
			'add_or_remove_items'        => 'Add or remove ' . $p,
			'choose_from_most_used'      => 'Frequently Used ' . $p,
			'popular_items'              => 'Popular ' . $p,
			'search_items'               => 'Search ' . $p,
			'not_found'                  => 'Not Found',
			'no_terms'                   => 'No ' . $p,
			'items_list'                 => $p . ' list',
			'items_list_navigation'      => $p . ' list navigation',
			'back_to_terms'              => 'Back to ' . $s . ' Tags',
		];
	}

	/**
	 * Build the rewrite options for a taxonomy.
	 *
	 * @param string $singular The singular name for the taxonomy.
	 */
	private function build_rewrite_array_for_taxonomy( $singular ) {
		return [
			'slug'         => $singular,
			'with_front'   => true,
			'hierarchical' => false,
		];
	}

	/**
	 * Build the argument options for a taxonomy.
	 *
	 * @param string $singular The singular name for the taxonomy.
	 * @param string $plural The plural name for the taxonomy.
	 * @param string $description The decscription of this taxonomy.
	 */
	private function build_args_for_taxonomy( $singular, $plural, $description = '' ) {
		return [
			'labels'                => $this->build_taxonomy_labels( $singular, $plural ),
			'public'                => true,
			'description'           => $description,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_quick_edit'    => true,
			'show_admin_column'     => true,
			'show_tagcloud'         => true,
			'rewrite'               => $this->build_rewrite_array_for_taxonomy( $singular ),
			'show_in_rest'          => true,
			'rest_base'             => $singular,
			'query_var'             => $singular,
			'update_count_callback' => '_update_generic_term_count',
		];
	}

	/**
	 * Create all the custom taxonomies needed for the multisite Jones Sign company website.
	 */
	public function create_taxonomies() {
		$count_callback = '_update_generic_term_count';

		$taxonomies = [
			[
				'singular'    => 'signtype',
				'plural'      => 'signtypes',
				'description' => 'Types of Signage',
				'objects'     => [ 'attachment', 'post', 'project' ],
			],
			[
				'singular'    => 'expertise',
				'plural'      => 'expertises',
				'description' => 'Various Expertise',
				'objects'     => [ 'attachment', 'post', 'project' ],
			],
			[
				'singular'    => 'industry',
				'plural'      => 'industries',
				'description' => 'Industries Served',
				'objects'     => [ 'attachment', 'client', 'project' ],
			],
			[
				'singular'    => 'location',
				'plural'      => 'locations',
				'description' => 'Company Locations',
				'objects'     => [ 'staffmember' ],
			],

		];
		$total = count( $taxonomies );
		for ( $i = 0; $i < 4; $i++ ) {
			$singular                   = $taxonomies[ $i ]['singular'];
			$plural                     = $taxonomies[ $i ]['plural'];
			$description                = $taxonomies[ $i ]['description'];
			$objects                    = $taxonomies[ $i ]['objects'];
			$taxonomies[ $i ]['labels'] = $this->build_taxonomy_labels( $singular, $plural );
			$taxonomies[ $i ]['args']   = $this->build_args_for_taxonomy( $singular, $plural, $description );
		}

		return $taxonomies;
	}

	/**
	 * Retrieve a list of the ids of all the attachments that have this tag attached to them.
	 *
	 * @param int $term_id The id for a particular term.
	 *
	 * @todo - implement the ability to only get images with this term that have a star rating of x or above
	 *
	 * @return array $ids An array of the ids for the photos assigned to this signtype.
	 */
	public function get_images_tagged( $term_id ) : array {
		$stars           = 5; // @todo - implement the ability to only get images with this term that have a star rating of x or above.
		$ids             = [];
		$query_arguments = [
			'tag'       => $term_id,
			'post_type' => 'attachment',
		];
		return $ids;
	}

	/**
	 * Count the words or characters in the taxonomy field.
	 *
	 * @param int  $term_id The ID number of the term you'd like to check.
	 * @param bool $indepth Do I want the taxonomy term's indepth field? Default is true.
	 * @param bool $words   Whether to count words, if not counts characters. Default is words.
	 */
	private static function count_description( $term_id, $indepth = true, $words = true ) : int {
		$taxonomy  = get_term( $term_id )->taxonomy;
		$field_key = $indepth ? $taxonomy . 'Indepth' : '';
		if ( $words ) {
			if ( $indepth ) {
				$output = str_word_count( get_term_meta( $term_id, $field_key, $single ) );
			} else {
				$output = str_word_count( term_description( $term_id ) );
			}
		} else {
			if ( $indepth ) {
				$output = strlen( get_term_meta( $term_id, $field_key, $single ) );
			} else {
				$output = strlen( term_description( $term_id ) );
			}
		}
		return $output;
	}

	/**
	 * Check to see whether there is a sign image of a certain dimension.
	 *
	 * @param int  $term_id The specific taxonomy term id.
	 * @param bool $indepth Whether it should be the in depth description added or the primary term description - default is true.
	 * @param int  $minimum The minimum amount of words a description should be.
	 */
	public static function does_description_have_enough_words( $term_id, $indepth = true, $minimum = 25 ) : array {
		$taxonomy = get_term( $term_id )->taxonomy;
		$field    = $indepth ? 'In Depth' : 'Primary';
		$output   = [
			'color' => '--indigo-600',
			'icon'  => 'assignment_turned_in',
			'title' => "$field description has more than $minimum words",
		];
		if ( $minimum > self::count_description( $term_id, $indepth, true ) ) {
			$output = [
				'color' => '--gray-500',
				'icon'  => 'assignment_late',
				'title' => "$field description is less than $minimum words",
			];
		}
		return $output;
	}

	/**
	 * Check to see whether there is a sign image of a certain dimension.
	 *
	 * @param int    $term_id The specific taxonomy term id.
	 * @param string $taxonomy The taxonomy the term resides in -- options are 'signtype', 'industry', 'location', 'expertise'.
	 * @param bool   $output_as_id Should we get the image id? Default is true. If False, returns image url.
	 *
	 * @return mixed if $output_as_id is true, retrieve an array of (int) image_ids, otherwise retrieve an array of image url.
	 */
	public static function get_term_images( $term_id, $taxonomy, $output_as_id = true ) : array {
		$output                = [];
		$output['square']      = self::get_square_image( $term_id, $taxonomy, $output_as_id );
		$output['vertical']    = self::get_vertical_image( $term_id, $taxonomy, $output_as_id );
		$output['cinematic']   = self::get_cinematic_image( $term_id, $taxonomy, $output_as_id );
		$output['rectangular'] = self::get_rectangular_image( $term_id, $taxonomy, $output_as_id );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 1 x 1 aspect image for the service.
	 *
	 * @param int    $term_id Term Taxonomy Id.
	 * @param string $taxonomy The taxonomy the term resides in -- options are 'signtype', 'industry', 'location', 'expertise'.
	 * @param bool   $output_as_id Should we get the image id. Default true.
	 *
	 * @return mixed if $output_as_id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public static function get_vertical_image( $term_id, $taxonomy, $output_as_id = true ) {
		$key    = $output_as_id ? $taxonomy . 'Vertical_id' : $taxonomy . 'Vertical';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 1 x 1 aspect image for the given sign type.
	 *
	 * @param int    $term_id Signtype Taxonomy Id.
	 * @param string $taxonomy The taxonomy the term resides in -- options are 'signtype', 'industry', 'location', 'expertise'.
	 * @param bool   $output_as_id Should we get the image id. Default true.
	 *
	 * @return mixed if $output_as_id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public static function get_square_image( $term_id, $taxonomy, $output_as_id = true ) {
		$key    = $output_as_id ? $taxonomy . 'Square_id' : $taxonomy . 'Square';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for 4 x 3 aspect image for the given sign type.
	 *
	 * @param int    $term_id Signtype Taxonomy Id.
	 * @param string $taxonomy The taxonomy the term resides in -- options are 'signtype', 'industry', 'location', 'expertise'.
	 * @param bool   $output_as_id Should we get the image id. Default true.
	 *
	 * @return mixed if $output_as_id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public static function get_rectangular_image( $term_id, $taxonomy, $output_as_id = true ) {
		$key    = $output_as_id ? $taxonomy . 'Rectangular_id' : $taxonomy . 'Rectangular';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Retrieve the taxonomy meta for the 16 x 9 aspect image for the given sign type.
	 *
	 * @param int    $term_id Signtype Taxonomy Id.
	 * @param string $taxonomy The taxonomy the term resides in -- options are 'signtype', 'industry', 'location', 'expertise'.
	 * @param bool   $output_as_id Should we get the image id. Default true.
	 *
	 * @return mixed if $output_as_id is true, retrieve the (int) id of the image, otherwise retrieve the url as a string.
	 */
	public static function get_cinematic_image( $term_id, $taxonomy, $output_as_id = true ) {
		$key    = $output_as_id ? $taxonomy . 'Cinematic_id' : $taxonomy . 'Cinematic';
		$single = true;
		$output = get_term_meta( $term_id, $key, $single );
		return $output;
	}

	/**
	 * Count the photos that have this taxonomy tag attached.
	 *
	 * @param int    $term_id The term id.
	 * @param string $post_type The post type. Defaults to 'attachment', but could be 'project', 'client', 'page', 'post', 'staffmember', 'nav_menu_item'.
	 *
	 * @return mixed The quantity of photos that have this taxonomy term applied to them or false.
	 */
	public static function count_term_entries( $term_id, $post_type = 'attachment' ) {
		global $wpdb;
		$count = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT COUNT(*)
				FROM $wpdb->posts
				WHERE `post_type` = %s
				AND ID IN (
					SELECT object_id
					FROM $wpdb->term_relationships
					WHERE `term_taxonomy_id` = %d
					)
				", $post_type, $term_id ) );
		return $count;
	}

	/**
	 * Check to see whether there is a sign image of a certain dimension.
	 *
	 * @param int    $term_id The specific taxonomy term id.
	 * @param string $taxonomy The name of the taxonomy tag.
	 */
	public static function check_term_images( $term_id, $taxonomy ) {
		$size_options = [ 'vertical', 'cinematic', 'rectangular', 'square' ];
		$still_need   = [];
		// set output defaults to assume all images are set for this taxonomy term.
		$output = [
			'color'   => '--indigo-600',
			'message' => 'all photo sizes attached',
		];
		foreach ( $size_options as $size ) {
			if ( ! get_term_meta( $term_id, $taxonomy . ucfirst( $size ), true ) ) {
				$still_need[] = $size;
			}
		}

		// Should there be any sizes still needed, then do this - change the color of the icon to gray. and the title of the icon to indicate what is issing.
		if ( $still_need ) {
			$output = [
				'color'   => '--gray-600',
				'message' => 'Needs the Following Sizes: ' . implode( '|', $still_need ) . '.',
			];
		}
		return $output;
	} // end check_term_images()

	/**
	 * Retrieve the taxonomy meta for 'term' . 'AltNames' for the given taxonomy term.
	 * This is always an array.
	 *
	 * @param int    $term_id Taxonomy term Id.
	 * @param string $taxonomy The taxonomy the term resides in -- in this case the 'signtype'.
	 *
	 * @return array An Array of all the alternate names for this term.
	 */
	public static function get_term_aliases( $term_id, $taxonomy ) {
		$field = $taxonomy . 'AltNames';
		return get_term_meta( $term_id, $field, true );
	}

	/**
	 * Retrieve the entry for the additional term field '{taxonomy}Indepth' .
	 *
	 * @param int    $term_id Taxonomy term Id.
	 * @param string $taxonomy The taxonomy the term resides in -- in this case the 'signtype'.
	 *
	 * @return string The entry for the taxonomy term's "indepth" field.
	 */
	public static function get_term_indepth( $term_id, $taxonomy ) {
		return get_term_meta( $term_id, $taxonomy . 'Indepth', true );
	}

	/**
	 * Query the database for all media items with the tag specific to what you are looking for.
	 *
	 * @param int $term_id The term ID.
	 *
	 * @return array An array of the image ID's.
	 */
	public function get_related_images( $term_id ) : array {
		return $this->get_related( $term_id, 'attachment' );
	}

	/**
	 * Get the posts or the attachments that are related to this taxonomy.
	 *
	 * @param int    $term_id The term ID.
	 * @param string $posttype  Type of post - presently my options are 'attachment', 'post', 'page', 'revision', 'staffmember', 'client' or 'project'. Default is 'project'.
	 *
	 * @return array An array of post_ids that are related to the term.
	 */
	public function get_related( int $term_id, $posttype = 'project' ) : array {
		global $wpdb;
		$output = '';
		if ( 'attachment' === $posttype ) {
			$minimum_rating = 5;
			$output         = $wpdb->get_col(
				$wpdb->prepare(
					"
					SELECT `post_id` FROM $wpdb->postmeta
						WHERE meta_key = 'imageRating'
							AND meta_value = %d
								AND post_id IN (
									SELECT `object_id`
										FROM $wpdb->term_relationships
											WHERE `object_id` IN ( SELECT ID FROM $wpdb->posts WHERE post_type = %s )
											AND term_taxonomy_id = %d
									)
					", $minimum_rating, $posttype, $term_id ) );
		} else {
			$status = [ 'publish', 'private' ];
			$output = $wpdb->get_col(
				$wpdb->prepare(
					"
						SELECT ID FROM $wpdb->posts
							LEFT JOIN $wpdb->term_relationships
								ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id )
									WHERE 1 = 1
										AND (
											$wpdb->term_relationships.term_taxonomy_id IN ( %d )
										) AND
											$wpdb->posts.post_type = %s
												AND (
													$wpdb->posts.post_status = %s OR $wpdb->posts.post_status = %s
												) GROUP BY $wpdb->posts.ID
													ORDER BY $wpdb->posts.post_date DESC
					", $term_id, $posttype, $status[0], $status[1]
				)
			);
		}
		return $output;
	} // end get_related

	/**
	 * Enqueue Janascript to get related images -- that is attachments that have been given the same tag.
	 */
	public function action_enqueue_related_images_javascript() {

		// Just return if the AMP plugin is active -- which it most likely will not be as of 1.0.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		if ( is_tax( 'signtype' ) ) {
			// Once we know thether the taxonomy is signtype, we can load the related images script in the footer.
			$handle  = 'wp-rig-related-images';
			$deps    = [];
			$footer  = false; // Do not include in footer - include in header.
			$uri     = get_theme_file_uri( '/assets/js/related_images.min.js' );
			$version = wp_rig()->get_version( get_theme_file_path( '/assets/js/related_images.min.js' ) );
			if ( 'development' === ENVIRONMENT ) {
				$uri     = get_theme_file_uri( '/assets/js/src/related_images.js' );
				$version = wp_rig()->get_version( get_theme_file_path( '/assets/js/src/related_images.js' ) );
			}
			wp_register_script( $handle, $uri, $deps, $version, $footer );
			wp_enqueue_script( $handle, $uri, $deps, $version, $footer );

			/**
			 * Allows us to add the js right within the module.
			 * Setting 'precache' to true means we are loading this script in the head of the document.
			 * By setting 'async' to true, it tells the browser to wait until it finishes loading to run the script.
			 * 'defer' would mean wait until EVERYTHING is done loading to run the script.
			 * We can pick 'defer' because it isn't needed until the visitor hits a scroll point using intersection observer.
			 * No need to precache this, either.
			 *
			 * @link https://developer.wordpress.org/reference/functions/wp_script_add_data.
			 */
			wp_script_add_data( $handle, 'defer', true );
			wp_localize_script(
				$handle,
				'termData',
				[
					'term_id'          => get_queried_object()->term_id,
					'slug'             => get_queried_object()->slug,
					'rest_url'         => rest_url( 'wp/v2/' ),
					'related_images'   => $this->get_related_images( get_queried_object()->term_id ),
					'related_projects' => $this->get_related( get_queried_object()->term_id, 'project' ),

				]
			);

		} // end is_tax()
	}

	/**
	 * Ouput a term html tag.
	 *
	 * @param int $term_id The term ID.
	 *
	 * @return string The HTML for the link to a term page.
	 */
	public function get_term_hyperlink( $term_id ) {
		$link = get_term_link( $term_id );
		//phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( get_object_vars( get_term( $term_id ) ) );
		return "<a href=\"$link\" class=\"taxonomy_tag\" data-slug=\"$slug\"><span class=\"material-icons\">sell</span>$name</a>";
	}


}//end class
