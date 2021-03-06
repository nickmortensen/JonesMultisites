<?php
/**
 * WP_Rig\WP_Rig\Styles\Component class
 *
 * Last Update 5-18-2021
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Styles;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function wp_register_style;
use function wp_style_add_data;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_styles;
use function esc_attr;
use function esc_url;
use function wp_style_is;
use function _doing_it_wrong;
use function wp_print_styles;
use function post_password_required;
use function is_singular;
use function comments_open;
use function get_comments_number;
use function apply_filters;
use function add_query_arg;

/**
 * Class for managing stylesheets.
 *
 * Exposes template tags:
 * * `wp_rig()->print_styles()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	const MATERIAL_ICONS_VERSION = '9';

	/**
	 * Associative array of CSS files, as $handle => $data pairs.
	 * $data must be an array with keys 'file' (file path relative to 'assets/css' directory), and optionally 'global'
	 * (whether the file should immediately be enqueued instead of just being registered) and 'preload_callback'
	 * (callback function determining whether the file should be preloaded for the current request).
	 *
	 * Do not access this property directly, instead use the `get_css_files()` method.
	 *
	 * @var array
	 */
	protected $css_files;


	/**
	 * Associative array of Google Fonts to load, as $font_name => $font_variants pairs.
	 *
	 * Do not access this property directly, instead use the `get_google_fonts()` method.
	 *
	 * @var array
	 */
	protected $google_fonts;

	/**
	 * Associative array of Material Icon Css to load to load, as $font_name => $font_variants pairs.
	 *
	 * Do not access this property directly, instead use the `get_gmaterial_icons()` method.
	 *
	 * @var array
	 */
	protected $material_icons;


	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'styles';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'init', [ $this, 'disable_the_goddamned_emoji' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_jonessign_style' ], 9 );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_styles' ], 10 );
		add_action( 'wp_enqueue_scripts', [ $this, 'add_material_icons_frontend' ], 12 );
		// add_action( 'wp_enqueue_scripts', [ $this, 'add_style_overrides' ], 15 );
		// add_action( 'wp_enqueue_scripts', [ $this, 'add_mono_overrides' ], 18 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_sheetrock' ], 16 );
		add_action( 'wp_head', [ $this, 'action_preload_styles' ] );
		add_action( 'after_setup_theme', [ $this, 'action_add_editor_styles' ] );
		add_filter( 'wp_resource_hints', [ $this, 'filter_resource_hints' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_material_icons_backend' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_custom_admin_style' ] );
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
			'print_styles' => [ $this, 'print_styles' ],
		];
	}

	/**
	 * Registers or enqueues stylesheets.
	 *
	 * Stylesheets that are global are enqueued. All other stylesheets are only registered, to be enqueued later.
	 */
	public function action_enqueue_styles() {

		// Enqueue Google Fonts.
		$google_fonts_url = $this->get_google_fonts_url();
		if ( ! empty( $google_fonts_url ) ) {
			wp_enqueue_style( 'wp-rig-fonts', $google_fonts_url, [], 9, 'all' );
		}

		$css_uri = get_theme_file_uri( '/assets/css/' );
		$css_dir = get_theme_file_path( '/assets/css/' );

		$preloading_styles_enabled = $this->preloading_styles_enabled();

		$css_files = $this->get_css_files();
		foreach ( $css_files as $handle => $data ) {
			$src     = $css_uri . $data['file'];
			$version = wp_rig()->get_asset_version( $css_dir . $data['file'] );

			/*
			 * Enqueue global stylesheets immediately and register the other ones for later use
			 * (unless preloading stylesheets is disabled, in which case stylesheets should be immediately
			 * enqueued based on whether they are necessary for the page content).
			 */
			if ( $data['global'] || ! $preloading_styles_enabled && is_callable( $data['preload_callback'] ) && call_user_func( $data['preload_callback'] ) ) {
				wp_enqueue_style( $handle, $src, [], $version, $data['media'] );
			} else {
				wp_register_style( $handle, $src, [], $version, $data['media'] );
			}

			wp_style_add_data( $handle, 'precache', true );
		}
	}

	/**
	 * Preloads in-body stylesheets depending on what templates are being used.
	 *
	 * Only stylesheets that have a 'preload_callback' provided will be considered. If that callback evaluates to true
	 * for the current request, the stylesheet will be preloaded.
	 *
	 * Preloading is disabled when AMP is active, as AMP injects the stylesheets inline.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Preloading_content
	 */
	public function action_preload_styles() {

		// If preloading styles is disabled, return early.
		if ( ! $this->preloading_styles_enabled() ) {
			return;
		}

		$wp_styles = wp_styles();

		$css_files = $this->get_css_files();
		foreach ( $css_files as $handle => $data ) {

			// Skip if stylesheet not registered.
			if ( ! isset( $wp_styles->registered[ $handle ] ) ) {
				continue;
			}

			// Skip if no preload callback provided.
			if ( ! is_callable( $data['preload_callback'] ) ) {
				continue;
			}

			// Skip if preloading is not necessary for this request.
			if ( ! call_user_func( $data['preload_callback'] ) ) {
				continue;
			}

			$preload_uri = $wp_styles->registered[ $handle ]->src . '?ver=' . $wp_styles->registered[ $handle ]->ver;

			echo '<link rel="preload" id="' . esc_attr( $handle ) . '-preload" href="' . esc_url( $preload_uri ) . '" as="style">';
			echo "\n";
		}
	}

	/**
	 * Enqueues WordPress theme styles for the editor.
	 */
	public function action_add_editor_styles() {

		// Enqueue Google Fonts.
		$google_fonts_url = $this->get_google_fonts_url();
		if ( ! empty( $google_fonts_url ) ) {
			add_editor_style( $this->get_google_fonts_url() );
		}

		// Enqueue block editor stylesheet.
		add_editor_style( 'assets/css/editor/editor-styles.min.css' );
	}

	/**
	 * Adds preconnect resource hint for Google Fonts.
	 *
	 * @param array  $urls          URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed.
	 * @return array URLs to print for resource hints.
	 */
	public function filter_resource_hints( array $urls, string $relation_type ) : array {
		if ( 'preconnect' === $relation_type && wp_style_is( 'wp-rig-fonts', 'queue' ) ) {
			$urls[] = [
				'href' => 'https://fonts.gstatic.com',
				'crossorigin',
			];
		}

		return $urls;
	}

	/**
	 * Prints stylesheet link tags directly.
	 *
	 * This should be used for stylesheets that aren't global and thus should only be loaded if the HTML markup
	 * they are responsible for is actually present. Template parts should use this method when the related markup
	 * requires a specific stylesheet to be loaded. If preloading stylesheets is disabled, this method will not do
	 * anything.
	 *
	 * If the `<link>` tag for a given stylesheet has already been printed, it will be skipped.
	 *
	 * @param string ...$handles One or more stylesheet handles.
	 */
	public function print_styles( string ...$handles ) {

		// If preloading styles is disabled (and thus they have already been enqueued), return early.
		if ( ! $this->preloading_styles_enabled() ) {
			return;
		}

		$css_files = $this->get_css_files();
		$handles   = array_filter(
			$handles,
			function( $handle ) use ( $css_files ) {
				$is_valid = isset( $css_files[ $handle ] ) && ! $css_files[ $handle ]['global'];
				if ( ! $is_valid ) {
					/* translators: %s: stylesheet handle */
					_doing_it_wrong( __CLASS__ . '::print_styles()', esc_html( sprintf( __( 'Invalid theme stylesheet handle: %s', 'wp-rig' ), $handle ) ), 'WP Rig 2.0.0' );
				}
				return $is_valid;
			}
		);

		if ( empty( $handles ) ) {
			return;
		}

		wp_print_styles( $handles );
	}

	/**
	 * Determines whether to preload stylesheets and inject their link tags directly within the page content.
	 *
	 * Using this technique generally improves performance, however may not be preferred under certain circumstances.
	 * For example, since AMP will include all style rules directly in the head, it must not be used in that context.
	 * By default, this method returns true unless the page is being served in AMP. The
	 * {@see 'wp_rig_preloading_styles_enabled'} filter can be used to tweak the return value.
	 *
	 * @return bool True if preloading stylesheets and injecting them is enabled, false otherwise.
	 */
	protected function preloading_styles_enabled() {
		$preloading_styles_enabled = ! wp_rig()->is_amp();

		/**
		 * Filters whether to preload stylesheets and inject their link tags within the page content.
		 *
		 * @param bool $preloading_styles_enabled Whether preloading stylesheets and injecting them is enabled.
		 */
		return apply_filters( 'wp_rig_preloading_styles_enabled', $preloading_styles_enabled );
	}

	/**
	 * Gets all CSS files.
	 *
	 * @return array Associative array of $handle => $data pairs.
	 */
	protected function get_css_files() : array {
		if ( is_array( $this->css_files ) ) {
			return $this->css_files;
		}

		$css_files = [
			// Only need to load this on certain pages, but how is a fella supposed to do that?
			'wp-rig-global'       => [
				'file'   => 'global.min.css',
				'global' => true,
			],
			// 'wp-rig-safety'       => [
			// 	'file'             => 'safety.min.css',
			// 	'preload_callback' => function() {
			// 		global $template;
			// 		return 'front-page.php' === basename( $template );
			// 	},
			// ],
			'wp-rig-front-page'   => [
				'file'             => 'front-page.min.css',
				'preload_callback' => function() {
					global $template;
					return 'front-page.php' === basename( $template );
				},
			],
			'company-aspects'   => [
				'file'             => 'company_aspects.min.css',
				'preload_callback' => function() {
					global $template;
					return 'front-page.php' === basename( $template );
				},
			],
			'wp-rig-sidebar'    => [
				'file'   => 'sidebar.min.css',
				'global' => true,
			],
			'wp-rig-contact-form' => [
				'file'             => 'contact.min.css',
				'preload_callback' => function() {
					global $template;
					return 'front-page.php' === basename( $template );
				}
			],
			'wp-rig-content'      => [
				'file'             => 'content.min.css',
				'preload_callback' => '__return_true',
			],
			'wp-rig-taxonomy'     => [
				'file'             => 'taxonomy.min.css',
				'preload_callback' => function() {
					global $template;
					return 'taxonomy.php' === basename( $template );
				},
			],
			'wp-rig-project'      => [
				'file'             => 'project.min.css',
				'preload_callback' => function() {
					global $template;
					return is_singular( 'project' ) || 'front-page.php' === basename( $template );
				},
			],
			// Preload only on project post types using the single-project.php template.
			'wp-rig-flickity'     => [
				'file'             => 'flickity.min.css',
				'preload_callback' => function() {
					global $template;
					return 'project' === get_post_type() && 'single-project.php' === basename( $template ) || 'front-page.php' === basename( $template );
				},
			],
			// Preload only in the development environment.
			'wp-rig-developer'    => [
				'file'             => 'developer.min.css',
				'preload_callback' => function() {
					return 'development' === ENVIRONMENT;
				},
			],
		];

		/**
		 * Filters default CSS files.
		 *
		 * @param array $css_files Associative array of CSS files, as $handle => $data pairs.
		 *                         $data must be an array with keys 'file' (file path relative to 'assets/css'
		 *                         directory), and optionally 'global' (whether the file should immediately be
		 *                         enqueued instead of just being registered) and 'preload_callback' (callback)
		 *                         function determining whether the file should be preloaded for the current request).
		 */
		$css_files = apply_filters( 'wp_rig_css_files', $css_files );

		$this->css_files = [];
		foreach ( $css_files as $handle => $data ) {
			if ( is_string( $data ) ) {
				$data = [ 'file' => $data ];
			}

			if ( empty( $data['file'] ) ) {
				continue;
			}

			$this->css_files[ $handle ] = array_merge(
				[
					'global'           => false,
					'preload_callback' => null,
					'media'            => 'all',
				],
				$data
			);
		}

		return $this->css_files;
	}

	/**
	 * Returns Google Fonts used in theme.
	 *
	 * @return array Associative array of $font_name => $font_variants pairs.
	 */
	protected function get_google_fonts() : array {
		if ( is_array( $this->google_fonts ) ) {
			return $this->google_fonts;
		}

		$google_fonts = [
			'Oswald'      => [ '700' ],
			'Roboto Mono' => [ '300', '400', '400i', '500', '700' ],
			'Ubuntu'      => [ '300', '400', '400i', '500', '700' ],
		];

		/**
		 * Filters default Google Fonts.
		 *
		 * @param array $google_fonts Associative array of $font_name => $font_variants pairs.
		 */
		$this->google_fonts = (array) apply_filters( 'wp_rig_google_fonts', $google_fonts );

		return $this->google_fonts;
	}

	/**
	 * Returns Material Icons used in theme.
	 *
	 * @return array Associative array of $font_name => $font_variants pairs.
	 */
	protected function get_material_icon_names() : array {
		if ( is_array( $this->material_icons ) ) {
			return $this->material_icons;
		}

		$material_icons       = [ 'Material Icons', 'Material Icons Outlined', 'Material Icons Round', 'Material Icons Sharp', 'Material Icons Two Tone' ];
		$this->material_icons = (array) apply_filters( 'wp_rig_material_icons', $material_icons );
		return $this->material_icons;
	}

	/**
	 * Returns the Google Fonts URL to use for enqueuing Google Fonts CSS.
	 *
	 * Uses `latin` subset by default. To use other subsets, add a `subset` key to $query_args and the desired value.
	 *
	 * @return string Google Fonts URL, or empty string if no Google Fonts should be used.
	 */
	protected function get_google_fonts_url() : string {
		$google_fonts = $this->get_google_fonts();

		if ( empty( $google_fonts ) ) {
			return '';
		}

		$font_families = [];

		foreach ( $google_fonts as $font_name => $font_variants ) {
			if ( ! empty( $font_variants ) ) {
				if ( ! is_array( $font_variants ) ) {
					$font_variants = explode( ',', str_replace( ' ', '', $font_variants ) );
				}

				$font_families[] = $font_name . ':' . implode( ',', $font_variants );
				continue;
			}

			$font_families[] = $font_name;
		}

		$query_args = [
			'family'  => implode( '|', $font_families ),
			'display' => 'swap',
		];

		return add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}


	/**
	 * Output the url for the Material Icons.
	 */
	public function get_material_icon_fonts_url_array() : array {
		$base_url   = 'https://fonts.googleapis.com/icon';
		$icon_fonts = $this->get_material_icon_names();
		$output     = [];
		foreach ( $icon_fonts as $iconset ) {
			$query_args = [
				'family' => str_replace( ' ', '+', $iconset ),
			];

			$output[] = add_query_arg( $query_args, $base_url );
		}
		return $output;
	}

	/**
	 * Add Material Icons to the frontend of the site.
	 *
	 * @see Use admin_enqueue_scripts hook.
	 */
	public function add_material_icons_frontend() {
		$urls     = $this->get_material_icon_fonts_url_array();
		$handles  = $this->get_material_icon_names();
		$iconsets = array_combine( $handles, $urls );
		$version  = static::MATERIAL_ICONS_VERSION;
		foreach ( $iconsets as $handle => $url ) {
			wp_enqueue_style( $handle, $url, [], $version, 'all' );
		}
	}

	/**
	 * Add Material Icons to the admin end of the site.
	 *
	 * @see Use admin_enqueue_scripts hook.
	 */
	public function add_material_icons_backend() {
		$material_icons = $this->get_material_icon_fonts_url_array();
		$version        = static::MATERIAL_ICONS_VERSION;
		wp_enqueue_style( 'wp-rig-material-icons', $material_icons[0], [], $version, 'all' );
	}

	/**
	 * Add Admin Stylesheet to site.
	 *
	 * @see Use admin_enqueue_scripts hook.
	 */
	public function add_custom_admin_style() {
		$filepath = get_theme_file_path( '/admin.min.css' ); // outputs a path --needed for asset version.
		$fileurl  = get_theme_file_uri( '/admin.min.css' ); // outputs a path --needed for asset version.
		$version  = wp_rig()->get_asset_version( $filepath );
		wp_enqueue_style( 'wp-rig-admin-styles', $fileurl, [], $version, 'all' );
	}

	/**
	 * Disable the emoji that are built into WordPress.
	 */
	public function disable_the_goddamned_emoji() {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}

	/**
	 * Registers or enqueues stylesheets for Material design.
	 *
	 * Stylesheets that are global are enqueued. All other stylesheets are only registered, to be enqueued later.
	 */
	public function action_enqueue_material_design_styles() {
		$url = 'https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css';
		wp_enqueue_style( 'material-design', $url, [], 9, 'all' );
	}

	/**
	 * Add material design javascript.
	 */
	public function enqueue_sheetrock() {
		$handle       = 'sheetrock';
		$script_uri   = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-sheetrock/1.1.4/dist/sheetrock.min.js';
		$dependencies = [ 'wp-rig-flickity' ]; // location: wp-admin/js/inline-edit-post.js.
		$version      = '9';
		$footer       = false;

		wp_enqueue_script( $handle, $script_uri, $dependencies, $version, $footer );
	}

	/**
	 * Enqueue style.min.css.
	 *
	 * @see These are just the stylesheet files that change very rarely.
	 */
		public function enqueue_jonessign_style() {
		$handle   = 'baseline';
		$filepath = get_theme_file_path( '/style.min.css' ); // outputs a path --needed for asset version.
		$fileurl  = get_theme_file_uri( '/style.min.css' ); // outputs a path --needed for asset version.
		$version  = wp_rig()->get_asset_version( $filepath );
		wp_enqueue_style( $handle, $fileurl, [], $version, 'all' );
	}

	/**
	 * Add Overrides CSS to the frontend of the site.
	 *
	 * @see Use admin_enqueue_scripts hook.
	 */
	public function add_style_overrides() {
		$handle       = 'overrides';
		$filepath     = get_theme_file_path( '/assets/css/overrides.min.css' );
		$fileurl      = get_theme_file_uri( '/assets/css/overrides.min.css' );
		$dependencies = [ 'baseline' ];
		$version      = wp_rig()->get_asset_version( $filepath );
		wp_enqueue_style( $handle, $fileurl, $dependencies, $version, 'all' );
	}

	/**
	 * Add MONO Overrides CSS to the frontend of the site.
	 *
	 * @see Use admin_enqueue_scripts hook.
	 */
	public function add_mono_overrides() {
		$handle       = 'mono';
		$filepath     = get_theme_file_path( '/assets/css/mono_overrides.min.css' );
		$fileurl      = get_theme_file_uri( '/assets/css/mono_overrides.min.css' );
		$dependencies = [ 'baseline' ];
		$version      = wp_rig()->get_asset_version( $filepath );
		wp_enqueue_style( $handle, $fileurl, $dependencies, $version, 'all' );
	}

}
