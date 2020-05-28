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
		add_action( 'cmb2_init', [ $this, 'create_extra_fields' ] );
		add_filter( 'manage_media_columns', [ $this, 'add_tag_column' ] );
		add_action( 'manage_media_custom_column', [ $this, 'manage_attachment_tag_column' ], 10, 2 );
		add_filter( 'upload_mimes', [ $this, 'allow_svg_uploads' ], 10, 1 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_media_scripts' ] );
		add_action( 'wp_ajax_svg_get_attachment_url', [ $this, 'get_attachment_url_media_library' ] );
	}

	/**
	 * Allow SVG to be uploaded.
	 *
	 * @param array $allowed The mime types that are already a part of WordPress.
	 * @return array $allowed The mime types plus what has been added via the function.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/upload_mimes/
	 */
	public function allow_svg_uploads( $allowed ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $allowed;
		}
		$allowed['svg'] = 'image/svg'; // not image/xml as originally thought.
		return $allowed;
	}

	/**
	 * Enqueue SVG js and stylesheet in the admin.
	 *
	 * @link https://wordpress.stackexchange.com/questions/252256/svg-image-upload-stopped-working/305177#305177
	 */
	public function enqueue_media_scripts() {
		$style_handle  = 'media_svg_style';
		$script_handle = 'media_svg_script';
		wp_enqueue_style( $style_handle, get_theme_file_uri( '/assets/css/src/_svg.css' ), [], '1', 'all' );

		wp_enqueue_script( $script_handle, get_theme_file_uri( '/assets/js/src/svg.js' ), 'jQuery', '1', false );
		wp_localize_script( $script_handle, 'script_vars', [ 'AJAXurl' => admin_url( 'admin-ajax.php' ) ] );
	}

	/**
	 * Ajax get_attachment_url_media_library.
	 */
	public function get_attachment_url_media_library() {
		$url = '';
		//phpcs:disable
		$attachmentID = isset( $_REQUEST['attachmentID'] ) ?? '';
		if ( $attachmentID ) {
			$url = wp_get_attachment_url( $attachmentID );
		}
		//phpcs:enable
		echo $url;
		die();
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
		$new['cb']     = '<input type="checkbox">';
		$new['id']     = 'ID';
		$new['rating'] = '<i style="color:var(--yellow-500)" class="material-icons">stars</i>';
		// phpcs:ignore
		// $new['star_rating'] = '<i class = "text-red-600 material-icons">stars</i>';
		$columns = array_merge( $new, $columns );
		return $columns;
	}

/**
 * Template tag for returning a star rating from the CMB2 star rating field type (on the front-end)
 *
 * @since  0.1.0
 *
 * @param  string  $metakey The 'id' of the 'star rating' field (the metakey for get_post_meta).
 * @param  integer $post_id (optional) post ID. If using in the loop, it is not necessary.
 */
protected function get_star_rating_field( $metakey, $post_id = 0 ) {
	$post_id         = $post_id ? $post_id : get_the_ID();
	$rating          = get_post_meta( $post_id, $metakey, 1 );
	$stars_container = '<section class = "cmb2-star-container">';
	$x               = 1;
	$total           = 5;
		while ( $x <= $rating ) {
			$stars_container .= '<span class="dashicons dashicons-star-filled"></span>';
			$x++;
		}
		if ( $rating < $total ) {
			while ( $rating < $total ) {
				$stars_container .= '<span class="dashicons dashicons-star-empty"></span>';
				$rating++;
			}
		}
	$stars_container .= '</section>';
	return $stars_container;
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
			case 'rating':
				$output = $this->get_star_rating_field( 'imageRating', $id );
				break;
			default:
				$output = '';
		}
		echo $output;
	}

	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since  1.0.0
	 * @link   https://github.com/CMB2/CMB2/wiki/Box-Properties
	 */
	public function create_extra_fields() {
		$args    = [
			'id'              => 'media_rating',
			'context'         => 'side',
			'title'           => 'Rating',
			'object_types'    => [ 'attachment' ],
			'cmb_styles'      => false,
			'remove_box_wrap' => true,
			'show_names'      => false,
			'show_in_rest'    => \WP_REST_Server::ALLMETHODS, // WP_REST_Server::READABLE|WP_REST_Server::EDITABLE, // Determines which HTTP methods the box is visible in.
		];
		$metabox = new_cmb2_box( $args );

		/* Rating */
		$args = [
			'name'    => 'Rating',
			'default' => '2',
			'id'      => 'imageRating',
			'type'    => 'rating',
		];
		$metabox->add_field( $args );
	}

}
