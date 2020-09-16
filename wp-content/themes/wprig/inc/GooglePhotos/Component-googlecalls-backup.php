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
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_install_photos_script' ] );
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
		];
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

		$credentials = $this->get_google_credentials();
		$glide = 'https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.3.0/glide.min.js';

		// This is the google api script.
		// phpcs:disable WordPress.WP.EnqueuedResourceParameters.NoExplicitVersion
		wp_register_script( 'glide', $glide, [], false, false );
		wp_enqueue_script(
			'glide',
			$glide,
			[ 'https://cdn.jsdelivr.net/npm/glidejs@2/dist/glide.min.js' ],
			false,
			false
		);
		// wp_register_script( 'google-photos-api', 'https://apis.google.com/js/api.js', [], false, false );
		// // Enqueue the google install photos script. The last element asks whether to load the script within the footer. We don't want that.
		// wp_enqueue_script(
		// 	'wp-rig-install_photos',
		// 	get_theme_file_uri( '/assets/js/install_photos.min.js' ),
		// 	[ 'google-photos-api' ],
		// 	wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/install_photos.min.js' ) ),
		// 	false
		// );


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
				'credentials'   => $credentials,
				'scopes'        => [ 'https://www.googleapis.com/auth/photoslibrary.readonly', 'https://www.googleapis.com/auth/photoslibrary' ],
				'googleAuthUri' => 'https://accounts.google.com/o/oauth2/v2/auth',
				'tokenUri'      => 'https://www.googleapis.com/oauth2/v4/token',
			]
		);

	}


}
