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
 * Class for media items -- adding media mime types, etc.
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
	 * Gets the unique identifier for the defaujlt image.
	 *
	 * @return int.
	 */
	public static function get_img_default() : int {
		return 761;
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'cmb2_init', [ $this, 'create_extra_fields' ] ); // create extra media fields.
		add_filter( 'manage_media_columns', [ $this, 'add_tag_column' ] ); // add administrator columns on backend.
		add_action( 'manage_media_custom_column', [ $this, 'manage_attachment_tag_column' ], 10, 2 ); // manage the data to be included in the newly-added columns.
		add_filter( 'manage_upload_sortable_columns', [ $this, 'make_columns_sortable' ], 10, 1 ); // makes the newly added columns sortable.
		// add_filter( 'upload_mimes', [ $this, 'allow_additional_file_formats' ], 10, 1 ); // can be used to remove mime types, use 'mime types' filter to ADD.
		add_filter( 'mime_types', [ $this, 'allow_additional_file_formats' ], 10, 1 ); // adds new filetypes as mimes (SVG, Webp, etc.).
		add_action( 'wp_ajax_svg_get_attachment_url', [ $this, 'get_attachment_url_media_library' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_media_scripts' ] );
	}//end initialize()

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [
			'add_tag_column'   => [ $this, 'add_tag_column' ],
			'get_image_rating' => [ $this, 'get_image_rating' ],
			'get_img_default'  => [ $this, 'get_img_default' ],
			'get_backend_link' => [ $this, 'get_backend_link' ],
			'get_image_assigned_post' => [ $this, 'get_image_assigned_post' ],
		];
	}

	/**
	 * Allow SVG to be uploaded.
	 *
	 * @param array $allowed The mime types that are already a part of WordPress.
	 * @return array $allowed The mime types plus what has been added via the function.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/upload_mimes/
	 */
	public function allow_additional_file_formats( $allowed ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $allowed;
		}
		$allowed['svg']  = 'image/svg'; // not image/xml as originally thought.
		$allowed['webp'] = 'image/webp'; // not image/xml as originally thought.
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
		wp_enqueue_script( $script_handle, get_theme_file_uri( '/assets/js/svg.min.js' ), 'jQuery', '1', false );
		wp_localize_script( $script_handle, 'script_vars', [ 'AJAXurl' => admin_url( 'admin-ajax.php' ) ] );
	}

	/**
	 * Ajax get_attachment_url_media_library.
	 */
	public function get_attachment_url_media_library() {
		$url = '';
		//phpcs:disable
		$attachment_id = isset( $_REQUEST['attachmentID'] ) ?? '';
		if ( $attachment_id ) {
			$url = wp_get_attachment_url( $attachment_id );
		}
		//phpcs:enable
		echo $url;
		die();
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
	 * Gets the image Rating - a number from 1-5. Defaults to one.
	 *
	 * @param integer $id the ID of the image.
	 */
	public function get_image_rating( $id ) : int {
		$field = 'imageRating';
		return (int) get_post_meta( $id, $field, true );
	}

	/**
	 * Gets assigned post for the media item.
	 *
	 * @param integer $media_id the ID of the image.
	 */
	public function get_image_assigned_post( int $media_id ) {
		$field = 'clientProjects';
		$project = get_post_meta( $media_id, $field, true );

		$output = false;

		if ( is_array( $project ) ) {
			$output = $project[0];
		}
		return $output;
	}

	/**
	 * Produce a url to the backend editor for this project
	 *
	 * @param integer $media_id the ID of the image.
	 */
	public function get_backend_link( $media_id ) : string {
		$post_id = $this->get_image_assigned_post( $media_id );
		$project = [
			'link' => '#',
			'title' => 'highlight_off',
			'class' => 'material-icons',
		];

		if ( $post_id ) {
			$project['link'] = WP_HOME . '/wp-admin/post.php?post=' . $post_id . '&action=edit';
			$project['title'] = get_post( $post_id )->post_title;
			$project['class'] = '';
		}
		return wp_sprintf( '<a class="%s" href="%s">%s</a>', $project['class'], $project['link'], $project['title'] );
	}

	/**
	 * Remove certain default columns on the admin end for the media post type
	 *
	 * @link https://www.smashingmagazine.com/2017/12/customizing-admin-columns-wordpress/
	 * @param array $columns Existing column names within the media admin page.
	 */
	public function add_tag_column( $columns ) {
		$columns_to_unset = [
			'comments',
			'author',
			'tags',
			'parent',
			'date',
			'taxonomy-media_category',
		];
		// Delete the existing columns from the above array.
		foreach ( $columns_to_unset as $column ) {
			unset( $columns[ $column ] );
		}

		// Add a new column.
		$new['cb']      = '<input type="checkbox">';
		$new['id']      = 'ID';
		$new['rating']  = '<i style="color:var(--yellow-500)" class="material-icons star-rating">stars</i>';
		$new['related'] = 'Related';

		return array_merge( $new, $columns );
	}

	/**
	 * Template tag for returning a star rating from the CMB2 star rating field type (on the front-end)
	 *
	 * @since  0.1.0
	 *
	 * @param  integer $post_id (optional) post ID. If using in the loop, it is not necessary.
	 * @param  integer $total (optional) Highest rating option.
	 */
	protected function get_star_rating_field( $post_id = 0, $total = 5 ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		$rating  = $this->get_image_rating( $post_id );
		$x       = 1;
		$empty   = '<span class="dashicons dashicons-star-empty"></span>';
		$filled  = '<span class="dashicons dashicons-star-filled"></span>';
		$stars   = '<section data-id="' . $post_id . '" id="admin-upload-star-rating" class = "cmb2-star-container" data-rating="' . $rating . '">';
		if ( $x <= $rating ) {
			$stars .= str_repeat( $filled, $rating );
		}
		if ( $rating < $total ) {
			$repeat = $total - $rating;
			$stars .= str_repeat( $empty, $repeat );
			}
		$stars .= '</section>';
		return $stars;
	}

	/**
	 * Output a material icon star
	 *
	 * @param int  $quantity How many? Default is 5.
	 * @param bool $filled Whether the star should be filled or not - default is false.
	 */
	public function get_star( $quantity = 5, $filled = false ) {
		$fill  = $filled ? 'var(--yellow-600)' : 'var(--gray-200)';
		$style = "color: $fill;";
		return str_repeat( '<span class="material-icons star-rating" style="' . $style . '"> star_rate </span>', $quantity );
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
				$total   = 5;
				$rating  = $this->get_image_rating( $id );
				$filled  = '<span class="dashicons dashicons-star-filled"></span>';
				$empty   = '<span class="dashicons dashicons-star-empty"></span>';
				$output  = '<section style="padding-top: 1rem;" data-id="' . $id . '" id="admin-upload-star-rating" data-rating="' . $rating . '">';
				$output .= $this->get_star( $rating, true );
				$output .= $this->get_star( $total - $rating, false );
				$output .= '</section>';
				break;
			case 'related':
				$output = $this->get_backend_link( $id );
				break;
			default:
				$output = '';
		}
		echo $output;
	}

	/**
	 * Make new column sortable within the admin area.
	 *
	 * @param array $columns The new columns to make sortable.
	 * @return array $columns All the columns you want sortable.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/manage_this-screen-id_sortable_columns/
	 */
	public function make_columns_sortable( $columns ) {
		$columns['id']     = 'ID';
		$columns['rating'] = '<span class="dashicons dashicons-star-filled"></span>';
		return $columns;
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

	/**
	 * Create quickedit star rating field.
	 *
	 * @todo Complete this function.
	 */
	public function create_quickedit_star_rating_field() {
		return '';
	}


}//end class
