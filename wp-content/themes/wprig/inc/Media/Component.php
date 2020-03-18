<?php
/**
 * WP_Rig\WP_Rig\Media\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Media;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function add_filter;
use network_site_url;
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
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'media';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'manage_media_columns', [ $this, 'add_tag_column' ] );
		add_action( 'manage_media_custom_column', [ $this, 'manage_attachment_tag_column' ], 10, 2 );
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
			'add_tag_column' => [ $this, 'add_tag_column' ],
		];
	}

	/**
	 * Force all network uploads to reside in "wp-content/uploads".
	 * Bypass "files" URL rewrite for site-specific directories.
	 *
	 * @link    http://wordpress.stackexchange.com/q/147750/1685
	 *
	 * @param   array $directory Folder to utilize.
	 * @return  array
	 */
	public function use_same_uploads_folder_for_all_sites( $directory ) {
		$directory['baseurl'] = network_site_url( '/wp-content/uploads' );
		$directory['basedir'] = ABSPATH . '/wp-content/uploads';
		$directory['path']    = $directory['basedir'] . $directory['subdir'];
		$directory['url']     = $directory['baseurl'] . $directory['subdir'];
		return $directory;
	}
	/**
	 * Remove certain default columns on the admin end for the media post type
	 *
	 * @link https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
	 * @param array $columns Existing column names within the media admin page.
	 */
	public function add_tag_column( $columns ) {
		// Delete an existing column.
		unset( $columns ['comments'] );
		unset( $columns ['author'] );
		unset( $columns ['tags'] );   // General Tags columnn.
		unset( $columns ['parent'] ); // 'Uploaded to' column.
		unset( $columns ['date'] ); // 'Data added' column.
		unset( $columns ['taxonomy-media_category'] ); // 'Data added' column.

		// Add a new column.
		$new['cb'] = '<input type = "checkbox">';
		$new['id'] = 'ID';
		// phpcs:ignore
		// $new['star_rating'] = '<i class = "text-red-600 material-icons">stars</i>';
		$columns = array_merge( $new, $columns );
		return $columns;
	}

	/**
	 * Remove certain default columns on the admin end for the media post type
	 *
	 * @link https://shibashake.com/wordpress-theme/add-admin-columns-in-wordpress
	 * @param string $column Newly added column names.
	 * @param int    $id     Newly added column names.
	 */
	public function manage_attachment_tag_column( $column, $id ) {
		switch ( $column ) {
			case 'id':
				$output = $id;
				break;
		default:
			$output = '<i class = "text-indigo-600 material-icons">stars</i>';
		}
		echo $output;
	}

}
