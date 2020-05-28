<?php
/**
 * WP_Rig\WP_Rig\RelatedPosts\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\RelatedPosts;

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
 * Class for related posts.
 *
 * Exposes template tags:
 * * `wp_rig()->the_comments( array $args = [] )`
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
		return 'related_posts';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_related_posts_script' ] );
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
			'display_related_posts' => [ $this, 'display_related_posts' ],
		];
	}

	/**
	 * Display the Related Posts.
	 */
	public function display_related_posts() {

	printf(
		'<h2 class="tracking-wider text-2xl text-center bloodymary-gradient text-yellow-400">%s</h2>
		<aside class="related-posts alignfull">
			<div class="related-spinner"></div>
		</aside>',
		esc_html( 'Related:', 'wp-rig' )
	);
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
	 * Enqueues a script that grabs related posts.
	 */
	public function action_enqueue_related_posts_script() {

		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}

		if ( is_single() ) {
			// Enqueue the related_posts script. The last element asks whether to load the script within the footer. We don't want that.
			wp_enqueue_script(
				'wp-rig-related_posts',
				get_theme_file_uri( '/assets/js/related_posts.min.js' ),
				[],
				wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/related_posts.min.js' ) ),
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
			wp_script_add_data( 'wp-rig-related_posts', 'defer', true );
			wp_localize_script(
				'wp-rig-related_posts',
				'postdata',
				[
					'post_ID'  => get_the_ID(),
					'cat_ids'  => $this->get_post_category_ids(),
					'rest_url' => rest_url( 'wp/v2/' ),
				]
			);
		}
	}
}
