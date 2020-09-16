<?php
/**
 * WP_Rig\WP_Rig\GooglePhotos\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\GooglePhotos;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use Google\Auth\Credentials\UserRefreshCredentials;
use Google\Photos\Library\V1\PhotosLibraryClient;
use Google\Photos\Library\V1\PhotosLibraryResourceFactory;
use Google\Auth\OAuth2 as OAuth2;
use function json_decode;
use function file_get_contents;
use function wp_enqueue_script;
use function wp_localize_script;


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
		return 'googlephotos';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_style' ], 30 );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_install_photos_script' ], 30 );
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
			'output_oauth'           => [ $this, 'output_oauth' ],
			'get_google_credentials' => [ $this, 'get_google_credentials' ],
			'open_glide'             => [ $this, 'open_glide' ],
			'close_glide'            => [ $this, 'close_glide' ],
		];
	}

	/**
	 * Output the opening of the glide gallery.
	 */
	public function open_glide() {
		$output = '<div class="slide-wrap">' . "\n\t" . '<div class="slideshow"' . "\n\t\t";
		return $output;
	}

	/**
	 * Output the closing HTML of the glide gallery.
	 */
	public function close_glide() : string {
		return "\n\n\t" . '</div><!-- end div.slideshow-->' . "\n" . '</div><!-- end div.slide-wrap -->';
	}

	/**
	 * Adds theme support for essential features.
	 */
	public function output_oauth() {
		$info = [
			'name'   => OAUTH_CLIENT_NAME,
			'id'     => OAUTH_CLIENT_ID,
			'secret' => OAUTH_CLIENT_SECRET,
		];
		return $info;
	}

	/**
	 * Get the credentials stored at the base of this installation for jonesinstallphotos.
	 * Output as an array.
	 */
	public function get_google_credentials() {
		return json_decode( file_get_contents( ABSPATH . '/jonesinstallphotos.json' ), true )['web'];
	}

	/**
	 * Enqueues a stylesheet that styles recent install photos.
	 */
	public function action_enqueue_style() {
		// I only need to add this script to certain pages - 'install photosgraphy.
		if ( ! is_page( 605 ) ) {
			return;
		}
		$handle   = 'wprig-install-photos-styles';
		$location = ( 'development' === ENVIRONMENT ) ? get_theme_file_uri( '/assets/css/src/glide.css' ) : get_theme_file_uri( '/assets/css/glide.min.css' );
		$deps     = [];
		$version  = wp_rig()->get_asset_version( trailingslashit( get_theme_file_path() ) . 'assets/css/src/glide.css' );
		$media    = 'all';

		wp_enqueue_style( $handle, $location, $deps, $version, $media );
	}

	/**
	 * Enqueues a script that grabs recent install photos.
	 */
	public function action_enqueue_install_photos_script() {
		// If the AMP plugin is active, return early.
		if ( wp_rig()->is_amp() ) {
			return;
		}
		// I only need to add this script to certain pages - 'install photosgraphy.
		if ( ! is_page( 605 ) ) {
			return;
		}


		$handle = 'glider';
		$cdn    = 'https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.3/glider.min.js';
		$deps   = [];
		$ver    = false;
		$footer = false;
		// phpcs:disable WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_register_script( $handle, $cdn, $deps, false, false );
		wp_enqueue_script(
			$handle,
			$cdn,
			$deps,
			false,
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
		wp_script_add_data( 'wp-rig-install_photos', 'defer', true );

		wp_localize_script(
			'wp-rig-install_photos',
			'installPhotos',
			[
			]
		);

	}


}
