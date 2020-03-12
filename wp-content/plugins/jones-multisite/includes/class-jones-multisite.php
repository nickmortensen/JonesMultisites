<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/user/nickmortensen
 * @since      1.0.0
 *
 * @package    Jones_Multisite
 * @subpackage Jones_Multisite/includes
 */

namespace JonesMulti;

use JonesMulti\Loader as Loader;
use JonesMulti\Internationalization as Internationalization;
use JonesMulti\PublicFacing as PublicFacing;
use JonesMulti\Admin as Admin;
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Jones_Multisite
 * @subpackage Jones_Multisite/includes
 * @author     Nick Mortensen <nmortensen@jonessign.com>
 */
class Jones_Multisite {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Jones_Multisite_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'JONES_MULTISITE_VERSION' ) ) {
			$this->version = JONES_MULTISITE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'jones-multisite';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		// $this->create_additional_taxonomies();
		// $this->create_additional_posttypes();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Jones_Multisite_Loader. Orchestrates the hooks of the plugin.
	 * - Jones_Multisite_i18n. Defines internationalization functionality.
	 * - Jones_Multisite_Admin. Defines all hooks for the admin area.
	 * - Jones_Multisite_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jones-multisite-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jones-multisite-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jones-multisite-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jones-multisite-public.php';

		$this->loader = new Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Jones_Multisite_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Internationalization();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new PublicFacing( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Jones_Multisite_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Load New Taxonomies.
	 *
	 * Loads the taxonomies unique to the project.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function create_additional_taxonomies() {

		/**
		 * The class responsible for defining the signtype taxonomy.
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/taxonomies/class-taxonomy-services.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/taxonomies/class-taxonomy-location.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/taxonomies/class-taxonomy-expertise.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/taxonomies/class-taxonomy-signtype.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/taxonomies/class-posttype-project.php';

	}

	/**
	 * Load Additional Post Types.
	 *
	 * Loads the additional post types unique to the multisite.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function create_additional_posttypes() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/posttypes/class-posttype-staff.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/posttypes/class-posttype-project.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/posttypes/class-posttype-client.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/posttypes/class-posttype-testimonial.php';
	}

}
