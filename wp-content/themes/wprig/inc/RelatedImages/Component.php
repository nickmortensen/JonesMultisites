<?php
/**
 * WP_Rig\WP_Rig\RelatedImages\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\RelatedImages;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use WP_Post;
use function add_action;
use function add_filter;
use function get_theme_file_uri;
use function get_theme_file_path;
use function get_the_category;
use function wp_enqueue_script;
use function wp_localize_script;

/**
 * BUILDING SQL QUERY
 *
 * SELECT ID FROM `jco_posts` WHERE `post_type` = 'attachment' ORDER BY `ID` DESC
 * SELECT * FROM `jco_term_relationships` WHERE object_id IN ( SELECT ID FROM jco_posts WHERe post_type='attachment') ORDER BY `object_id`  DESC
 * SELECT * FROM `jco_term_relationships` WHERE object_id IN ( SELECT ID FROM jco_posts WHERe post_type='attachment') AND term_taxonomy_id = 19 ORDER BY `object_id`  DESC
 * SELECT object_id FROM `jco_term_relationships` WHERE object_id IN ( SELECT ID FROM jco_posts WHERe post_type='attachment') AND term_taxonomy_id = 19 ORDER BY `object_id` DESC
 *
 * 11, 492, 493, 501-507
 */


/**
 * Class for related images.
 *
 * Exposes template tags:
 * * `wp_rig()->the_related_images( array $args = [] )`
 *
 * @link https://wordpress.org/plugins/amp/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'related_images';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_related_images_script' ] );
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
			'display_related_images' => [ $this, 'display_related_images' ],
			'get_related_images'     => [ $this, 'get_related_images' ],
			'get_related'            => [ $this, 'get_related' ],
		];
	}

	/**
	 * Query the database for all media items with the tag specific to what you are looking for.
	 *
	 * @param int    $term_id The term ID.
	 * @param string $posttype The type of post - default is 'attachment'.
	 * @return array An array of the image ID's.
	 */
	public function get_related_images( $term_id, $posttype = 'attachment' ) : array {
		global $wpdb;
		$term           = $term_id ?? get_queried_object()->term_id;
		$minimum_rating = 5;
		return $wpdb->get_col( $wpdb->prepare( "SELECT `post_id` FROM $wpdb->postmeta WHERE meta_key = 'imageRating' AND meta_value = %d AND post_id IN ( SELECT `object_id` FROM $wpdb->term_relationships WHERE `object_id` IN ( SELECT ID FROM $wpdb->posts WHERE post_type = %s ) AND term_taxonomy_id = %d )", $minimum_rating, $posttype, get_queried_object()->term_id ) );
	}

	/**
	 * Display the images that are related to this taxonomy.
	 *
	 * @param int $term_id The term ID.
	 */
	public function display_related_images( $term_id ) {
		$related_images = $this->get_related( $term_id, $post_type = 'attachment' );
		foreach ( $related_images as $index => $value ) {
			[
				'src'    => $source,
				'class'  => $class,
				'alt'    => $alt,
				'srcset' => $sourceset,
				'sizes'  => $sizes,
			] = wp_get_attachment_image( $value );
			return $source;
		}
	}

	/**
	 * Get the posts that are related to this taxonomy.
	 *
	 * @param int    $term_id The term ID.
	 * @param string $posttype  Type of post - presently my options are 'attachment', 'post', 'page', 'revision', 'staffmember', 'client' or 'project'. Default is 'project'.
	 */
	public function get_related( $term_id, $posttype = 'project' ) : array {
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
	}

	/**
	 * Return a comma-separated list of current post category IDs.
	 */
	public function get_post_category_ids() : string {
		$cats    = get_the_category();
		$cat_ids = [];

		if ( ! empty( $cats ) ) {
			foreach ( $cats as $category ) {
				$cat_ids[] = $category->cat_ID;
			}
		}
		return implode( ',', $cat_ids );
	}


	/**
	 * Enqueues a script that grabs related images.
	 */
	public function action_enqueue_related_images_script() {

		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		if ( is_tax( 'signtype' ) ) {
			// Enqueue the related_images script. The last element asks whether to load the script within the footer. We don't want that.
			wp_enqueue_script(
				'wp-rig-related_images',
				get_theme_file_uri( '/assets/js/src/related_images.js' ),
				[],
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/src/related_images.js' ) ),
				false
			);

			/*
			Allows us to add the js right within the module.
			Setting 'precache' to true means we are loading this script in the head of the document.
			By setting 'async' to true,it tells the browser to wait until it finishes loading to run the script.
			'Defer' would mean wait until EVERYTHING is done loading to run the script.
			We can pick 'defer' because it isn't needed until the visitor hits a scroll point.
			No need to precache this, either.
			*/
			wp_script_add_data( 'wp-rig-related_images', 'defer', true );
			wp_localize_script(
				'wp-rig-related_images',
				'termdata',
				[
					'term_id'          => get_queried_object()->term_id,
					'slug'             => get_queried_object()->slug,
					'rest_url'         => rest_url( 'wp/v2/' ),
					'related_images'   => $this->get_related( get_queried_object()->term_id, $post_type = 'attachment' ),
					'related_projects' => $this->get_related( get_queried_object()->term_id ),
				]
			);
		}
	}
}
