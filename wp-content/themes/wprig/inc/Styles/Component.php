<?php
/**
 * WP_Rig\WP_Rig\Styles\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Styles;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function remove_action;
use function remove_filter;
use function add_filter;
use function trailingslashit;
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
 * TOC
 * #1 get_slug()
 * #2 initialize()
 * #3 template_tags()
 * #4 action_enqueue_styles()
 * #5 action_preload_styles()
 * #6 action_add_editor_styles()
 * #7 filter_resource_hints()
 * #8 print_styles()
 * #9 preloading_styles_enabled()
 * #10 get_css_files()
 * #11 get_google_fonts()
 * #12 get_google_fonts_url()
 * #13 disable_the_goddamned_emoji()
 * #14 use_tailwind_styles()
 * #15 get_material_icon_font_url()
 */
/**
 * Class for managing stylesheets.
 *
 * Exposes template tags:
 * * `wp_rig()->print_styles()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

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
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'styles';
	}

	/**
	 *
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'init', [ $this, 'disable_the_goddamned_emoji' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_styles' ], 30 );
		add_action( 'login_enqueue_scripts', [ $this, 'custom_login_stylesheet' ], 30 );
		add_action( 'admin_enqueue_scripts', [ $this, 'custom_admin_style' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'add_material_icons' ] );
		add_action( 'wp_head', [ $this, 'action_preload_styles' ] );
		add_action( 'after_setup_theme', [ $this, 'action_add_editor_styles' ] );
		add_filter( 'wp_resource_hints', [ $this, 'filter_resource_hints' ], 10, 2 );
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
		$version            = wp_rig()->seconds_from_epoch();
		$material_icons_url = $this->get_material_icon_font_url();
		if ( ! empty( $material_icons_url ) ) {
			wp_enqueue_style( 'wp-rig-material-icons', $material_icons_url, [], '9', 'all' ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		}
		// Enqueue Google Fonts.
		$google_fonts_url = $this->get_google_fonts_url();
		if ( ! empty( $google_fonts_url ) ) {
			wp_enqueue_style( 'wp-rig-fonts', $google_fonts_url, [], $version, null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
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
			'project'           => [
				'file'             => 'project.min.css',
				'preload_callback' => function() {
					return 'project' === get_post_type();
				},
			],
			'client'            => [
				'file'             => 'client.min.css',
				'preload_callback' => function() {
					return 'client' === get_post_type();
				},
			],
			'signtype' => [
				'file'             => 'signtype.min.css',
				'preload_callback' => function() {
					return is_tax( 'signtype' );
				},
			],
			'flickity'          => [
				'file'             => 'flickity.min.css',
				'preload_callback' => function() {
					return 'project' === get_post_type();
				},
			],
			'wp-rig-global'     => [
				'file'   => 'global.min.css',
				'global' => true,
			],
			'wp-rig-flex'       => [
				'file'   => 'flex.min.css',
				'global' => true,
			],
			'wp-rig-content'    => [
				'file'             => 'content.min.css',
				'preload_callback' => '__return_true',
			],
			'wp-rig-sidebar'    => [
				'file'             => 'sidebar.min.css',
				'preload_callback' => function() {
					return wp_rig()->is_primary_sidebar_active();
				},
			],
			'wp-rig-widgets'    => [
				'file'             => 'widgets.min.css',
				'preload_callback' => function() {
					return wp_rig()->is_primary_sidebar_active();
				},
			],
			'wp-rig-front-page' => [
				'file'             => 'front-page.min.css',
				'preload_callback' => function() {
					global $template;
					return 'front-page.php' === basename( $template );
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
			'Yellowtail' => [],
			'Tillana'    => [ '400' ],
			'Oswald'     => [ '700' ],
			'Roboto'     => [ '300', '400', '400i', '500', '700' ],
			'Ubuntu'     => [ '300', '400', '400i', '500', '700' ],
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
	 * Use Tailwind for CSS.
	 */
	public function use_tailwind_styles() {
		$uri        = trailingslashit( get_theme_file_uri( 'assets/css/src/tailwind' ) );
		$dir        = trailingslashit( get_theme_file_path( '/assets/css/src/tailwind' ) );
		$handle     = 'tailwind';
		$dependency = [];
		$src        = $uri . 'tailwind.css';
		$media      = 'all';
		$version    = wp_rig()->seconds_from_epoch();
		wp_enqueue_style( $handle, $src, $dependency, $version, $media );
	}

	/**
	 * Custom Admin Style.
	 */
	public function custom_admin_style() {
		global $pagenow;
		global $post_type;
		$admin_css = get_theme_file_path( '/assets/css/admin.min.css' ); // outputs a path --needed for asset version.
		$version   = wp_rig()->get_asset_version( $admin_css );
		wp_enqueue_style( 'admincss', get_theme_file_uri( '/assets/css/admin.min.css' ), [], $version, 'all' );
	}

	/**
	 * Custom login Style.
	 */
	public function custom_login_stylesheet() {
		$login_css = get_theme_file_path( '/assets/css/src/_login.css' );
		$version   = wp_rig()->get_asset_version( $login_css );
		wp_enqueue_style( 'logincss', get_theme_file_uri( '/assets/css/src/_login.css' ), [], $version, 'all' );
	}

	/**
	 * Output the url for the Material Icons.
	 */
	protected function get_material_icon_font_url() : string {
		$base_url   = 'https://fonts.googleapis.com/icon';
		$family     = 'Material Icons';
		$query_args = [
			'family' => str_replace( ' ', '+', $family ),
		];
		return add_query_arg( $query_args, $base_url );
	}

	/**
	 * Add Material Icons to the site.
	 *
	 * @see Use admin_enqueue_scripts hook.
	 */
	public function add_material_icons() {
		$icons_url = $this->get_material_icon_font_url();
		wp_enqueue_style( 'wp-rig-material-icons', $icons_url, [], '9', 'all' );
	}


}
