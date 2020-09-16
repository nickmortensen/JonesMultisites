<?php
/**
 * WP_Rig\WP_Rig\Base_Support\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Base_Support;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function add_action;
use function add_filter;
use function add_theme_support;
use function is_singular;
use function pings_open;
use function esc_url;
use function get_bloginfo;
use function wp_scripts;
use function wp_get_theme;
use function get_template;

/**
 * TOC
 * #1  get_slug()
 * #2  initialize()
 * #3  action_essential_theme_support()
 * #4  action_add_pingback_header()
 * #5  filter_body_classes_add_hfeed()
 * #6  filter_embed_dimensions()
 * #7  filter_scandir_exclusions_for_optional_templates()
 * #8  filter_script_loader_tag( string $tag, string $handle )
 * #9  get_version()
 * #10 get_asset_version( $filepath )
 * #11 pr( $input )
 * #12 seconds_from_epoch()
 */

/**
 * Class for adding basic theme support, most of which is mandatory to be implemented by all themes.
 *
 * Exposes template tags:
 * * `wp_rig()->get_version()`
 * * `wp_rig()->get_asset_version( string $filepath )`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'base_support';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', [ $this, 'action_essential_theme_support' ] );
		add_action( 'wp_head', [ $this, 'action_add_pingback_header' ] );
		add_action( 'wp_head', [ $this, 'add_icons_to_header' ] );
		add_filter( 'body_class', [ $this, 'filter_body_classes_add_hfeed' ] );
		add_filter( 'embed_defaults', [ $this, 'filter_embed_dimensions' ] );
		add_filter( 'theme_scandir_exclusions', [ $this, 'filter_scandir_exclusions_for_optional_templates' ] );
		add_filter( 'script_loader_tag', [ $this, 'filter_script_loader_tag' ], 10, 2 );
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
			'get_version'        => [ $this, 'get_version' ],
			'get_asset_version'  => [ $this, 'get_asset_version' ],
			'seconds_from_epoch' => [ $this, 'seconds_from_epoch' ],
			'pr'                 => [ $this, 'pr' ],
			'user_agent_matches' => [ $this, 'user_agent_matches' ],
		];
	}


	/**
	 * Adds theme support for essential features.
	 */
	public function action_essential_theme_support() {
		// Add default RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		// Ensure WordPress manages the document title.
		add_theme_support( 'title-tag' );

		// Ensure WordPress theme features render in HTML5 markup.
		add_theme_support(
			'html5',
			[
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			]
		);

		// Add support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );
	}

	/**
	 * Adds a pingback url auto-discovery header for singularly identifiable articles.
	 */
	public function action_add_pingback_header() {
		if ( is_singular() && pings_open() ) {
			echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
		}
	}

	/**
	 * Determine whether the user agent is among one of the devices inputted into the function
	 *
	 * @param array $devices Devices to check.
	 *
	 * @return true If any of the devices are a match. False otherwise.
	 */
	public function user_agent_matches( $devices = [] ) {
		$user_agent       = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		$user_agent_match = false;
		foreach ( $devices as $device ) {
			if ( false !== stripos( $user_agent, $device ) ) {
				$user_agent_match = true;
				break;
			}
		}
		return $user_agent_match;
	}

	/**
	 * Add a site manifest file to the header.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_head/
	 */
	private function add_site_manifest() {
		return '<link rel="manifest" href="/site.webmanifest">';
	}

	/**
	 * Outputs the favicon to any public facing page load and the icons for Android and IOS only on the homepage
	 */
	public function add_icons_to_header() {
		$html = '';
		foreach ( [ '32x32', '16x16' ] as $favicon ) {
				$html .= '<link rel="icon" type="image/png" sizes="' . $favicon . '" href="/favicon-' . $favicon . '.png">';
		}

		if ( $this->user_agent_matches( [ 'iphone', 'ipod', 'ipad' ] ) ) {
			$html .= '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">';
		}
		if ( $this->user_agent_matches( [ 'android' ] ) ) {
			$html .= '<link rel="icon" type="image/png" sizes="192x192" href="/android-chome-192x192.png">';
		}

		$html .= $this->add_site_manifest();

		echo $html;
	}

	/**
	 * Adds a 'hfeed' class to the array of body classes for non-singular pages.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes_add_hfeed( array $classes ) : array {
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}
		return $classes;
	}

	/**
	 * Sets the embed width in pixels, based on the theme's design and stylesheet.
	 *
	 * @param array $dimensions An array of embed width and height values in pixels (in that order).
	 * @return array Filtered dimensions array.
	 */
	public function filter_embed_dimensions( array $dimensions ) : array {
		$dimensions['width'] = 720;
		return $dimensions;
	}

	/**
	 * Excludes any directory named 'optional' from being scanned for theme template files.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/theme_scandir_exclusions/
	 *
	 * @param array $exclusions the default directories to exclude.
	 * @return array Filtered exclusions.
	 */
	public function filter_scandir_exclusions_for_optional_templates( array $exclusions ) : array {
		return array_merge(
			$exclusions,
			[ 'optional' ]
		);
	}

	/**
	 * Adds async/defer attributes to enqueued / registered scripts.
	 *
	 * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12009
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 * @return string Script HTML string.
	 */
	public function filter_script_loader_tag( $tag, $handle ) : string {

		foreach ( [ 'async', 'defer' ] as $attr ) {
			if ( ! wp_scripts()->get_data( $handle, $attr ) ) {
				continue;
			}

			// Prevent adding attribute when already added in #12009.
			if ( ! preg_match( ":\s$attr(=|>|\s):", $tag ) ) {
				$tag = preg_replace( ':(?=></script>):', " $attr", $tag, 1 );
			}

			// Only allow async or defer, not both.
			break;
		}

		return $tag;
	}


	/**
	 * Gets the theme version.
	 *
	 * @return string Theme version number.
	 */
	public function get_version() : string {
		static $theme_version = null;

		if ( null === $theme_version ) {
			$theme_version = wp_get_theme( get_template() )->get( 'Version' );
		}

		return $theme_version;
	}

	/**
	 * Gets the version for a given asset.
	 *
	 * Returns filemtime when WP_DEBUG is true, otherwise the theme version.

	 * @see https://www.php.net/manual/en/function.filemtime.php
	 * @param string $filepath Asset file path.
	 * @return string Asset version number.
	 */
	public function get_asset_version( string $filepath ) : string {
		if ( WP_DEBUG ) {
			return (string) filemtime( $filepath );
		}

		return $this->get_version();
	}


	/**
	 * Helper function to get the result and output using 'print_r()' inside of '<pre>' tags.
	 *
	 * @param string $input What I would like to see wrapped in '<pre>' tags.
	 */
	public function pr( $input ) {
		echo '<pre>';
		print_r( $input ); //phpcs:ignore
		echo '</pre>';
	}

	/**
	 * Helper function to get seconds from the epoch.
	 * Useful when you need a unique version number when enqueueing or registering a script or style.
	 *
	 * @return int $datestring Seconds from the epoch.
	 */
	public function seconds_from_epoch() : string {
		$date_int = date( 'U' );
		return $date_int;
	}

	/**
	 * Understanding certain wordpress hooks and filters.
	 *
	 * @param string $redirect_url Exisitng $redirect_url to hook into.
	 */
	public function change_login_destination( $redirect_url ) {
		$redirect_url = '';
		return $redirect_url;
	}
}
