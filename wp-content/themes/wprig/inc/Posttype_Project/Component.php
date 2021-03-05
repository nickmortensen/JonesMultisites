<?php
/**
 * WP_Rig\WP_Rig\Posttype_Project\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Posttype_Project;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\AdditionalFields\Component as AdditionalFields;
use WP_Rig\WP_Rig\Posttype_Global\Component as PostTypes;
use WP_Rig\WP_Rig\TaxonomyGlobal\Component as Taxonomies;

use function WP_Rig\WP_Rig\wp_rig;

use function add_action;
use function add_filter;
use function get_current_screen;
use function get_post_meta;
use function get_posts;
use function get_the_category;
use function get_theme_file_path;
use function get_theme_file_uri;
use function register_post_type;
use function wp_enqueue_script;
use function admin_enqueue_scripts;
use function wp_localize_script;

/**
 * Class to work with the post type or 'project'.
 *
 * @since 1.0.0
 */
class Component implements Component_Interface, Templating_Component_Interface {
	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'project';
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
			'get_project_card'            => [ $this, 'get_project_card' ],
			'get_secondary_project_card'  => [ $this, 'get_secondary_project_card' ],
			'get_all_projects'            => [ $this, 'get_all_projects' ],
			'get_project_slideshow'       => [ $this, 'get_project_slideshow' ],
			'get_project_square_images'   => [ $this, 'get_project_square_images' ],
			'get_project_vertical_image'  => [ $this, 'get_project_vertical_image' ],
			'the_projects_menu'           => [ $this, 'the_projects_menu' ],
			'the_project_menu_items'      => [ $this, 'the_project_menu_items' ],
			'get_recent_project_ids'      => [ $this, 'get_recent_project_ids' ],
			'get_additional_project_info' => [ $this, 'get_additional_project_info' ],
			'get_all_project_info'        => [ $this, 'get_all_project_info' ],
			'get_project_address'         => [ $this, 'get_project_address' ],
			'get_project_alt'             => [ $this, 'get_project_alt' ],
			'get_project_partners'        => [ $this, 'get_project_partners' ],
			'get_project_svg'             => [ $this, 'get_project_svg' ],
			'get_project_header_images'   => [ $this, 'get_project_header_images' ],
			'get_project_narrative'       => [ $this, 'get_project_narrative' ],
			'get_city_state'              => [ $this, 'get_city_state' ],
			'get_payment_link'            => [ $this, 'get_payment_link' ],
			'get_project_sidemenu_items'  => [ $this, 'get_project_sidemenu_items' ],
			'make_drilldown_links'        => [ $this, 'make_drilldown_links' ],
		];
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		// Enqueue a frontend script to utilize for project post types.
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_projects_script' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_metabox_to_project' ], 10, 2 );
		add_action( 'quick_edit_custom_box', [ $this, 'display_quick_edit_custom' ], 10, 2 );
		// Add empty columns for the admin edit screens.
		add_action( 'manage_project_posts_columns', [ $this, 'make_new_admin_columns' ], 10, 1 );
		// Add data to the new admin columns.
		add_action( 'manage_project_posts_custom_column', [ $this, 'manage_new_admin_columns' ], 10, 2 );
		add_action( 'save_post', [ $this, 'save_post' ], 10, 1 ); // call on save, to update metainfo attached to our metabox.
		// Load the javascript admin script for pre-populating project quickedit fields on the admin end.
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_project_quickedit' ] );
	}


	/**
	 * Get all the published projects.
	 */
	public static function get_all_projects() {
		return Posttypes::get_all_posttype( 'project' );
	}

	/**
	 * Retrieve the postmeta for the year this project completed, started, or is expected to complete.
	 *
	 * @param int $post_id Project post type id.
	 * @return string 4 digit year that the post either completes, was started, or begins.
	 */
	public function get_additional_project_info( $post_id ) {
		return get_post_meta( $post_id, 'projectInfo', true ); // false is default, true if I want only the first value within the array.
	}

	/**
	 * Output an internal payments link.
	 */
	public function get_payment_link() {
		return 'link goes in here';
	}

	/**
	 * Get Project Location -- address.
	 *
	 * @param int $project_id ID of the project post.
	 *
	 * @return array An array of data about the location - including url of the project.
	 */
	public function get_project_address( int $project_id ) {
		return get_post_meta( $project_id, 'projectLocation', true );
	}

	/**
	 * Get the city and state with a comma separating the two
	 *
	 * @param array $address Contains address information.
	 */
	public function get_city_state( $address ) {
		return $address['city'] . ', ' . $address['state'];
	}

	/**
	 * Get Project Narrative text.
	 *
	 * @param int $project_id ID of the project post.
	 *
	 * @return array An array of data about the location - including url of the project.
	 */
	public function get_project_narrative( int $project_id ) {
		return get_post_meta( $project_id, 'projectNarrative', true );
	}

	/**
	 * Get project alternate name.
	 *
	 * @param int $project_id The ID of the Project Post.
	 */
	public function get_project_alt( int $project_id ) : string {
		return get_post_meta( $project_id, 'projectAltName', true );
	}

	/**
	 * Get project partners information. This is a repeating field.
	 *
	 * @param int $project_id The ID of the Project Post.
	 *
	 * @return array An array of entries containing the name, url, and type of the partner.
	 */
	public function get_project_partners( int $project_id ) {
		return get_post_meta( $project_id, 'projectPartners', false );
	}
	/**
	 * Get project svg logo.
	 *
	 * @param int $project_id The ID of the Project Post.
	 *
	 * @return string The url of the SVG.
	 */
	public function get_project_svg( int $project_id ) : string {
		return get_post_meta( $project_id, 'projectSVGLogo', true );
	}

	/**
	 * Get images slideshow image ids by url.
	 *
	 * @param int $project_id ID of the project post.
	 *
	 * @return array An associative array of rectangular images with the id of the image being the key and the url being the value.
	 */
	public function get_project_slideshow( int $project_id ) {
		return get_post_meta( $project_id, 'projectImagesSlideshow', true );
	}

	/**
	 * Get square images slideshow image ids by url.
	 *
	 * @param int $project_id ID of the project post.
	 *
	 * @return array Associative array of squareimages $key is the post id of the photo and $value is the url.
	 */
	public function get_project_square_images( int $project_id ) {
		return get_post_meta( $project_id, 'projectSquareImages', true );
	}

	/**
	 * Get vertical image for post type.
	 *
	 * @param int  $project_id ID of the project post.
	 * @param bool $as_id Should we return the data as the images id or the url, default is true, so it will return the id instead a url for the image.
	 *
	 * @return string Vertical image.
	 */
	public function get_project_vertical_image( int $project_id, $as_id = true ) {
		return PostTypes::get_posttype_vertical_image( $project_id, 'project', $as_id );
	}

	/**
	 * Get all project information.
	 *
	 * @param int $project_id Project identification or post_id.
	 *
	 * @return array An array of the project data within the project post.
	 */
	public function get_all_project_info( int $project_id ) {
		$size                  = 'medium';
		$project               = [];
		$project['thumbnail']  = get_post_thumbnail_id( $project_id );
		$project['thumb_url']  = wp_get_attachment_image_src( get_post_thumbnail_id( $project_id ), $size )[0] ?? wp_get_attachment_image_src( 761, 'medium' )[0];
		$project['type']       = get_post( $project_id )->post_type;
		$project['signtypes']  = wp_get_post_terms( $project_id, 'signtype', [ 'fields' => 'ids' ] );
		$project['expertise']  = wp_get_post_terms( $project_id, 'expertise', [ 'fields' => 'ids' ] );
		$project['industries'] = wp_get_post_terms( $project_id, 'industry', [ 'fields' => 'ids' ] );
		$project['slug']       = get_post( $project_id )->post_name;
		$project['url']        = WP_HOME . '/' . $project['type'] . '/' . $project['slug'] . '/';
		$project['job']        = get_post( $project_id )->post_title;
		$project['ID']         = $project_id;
		$project['link']       = get_post( $project_id )->guid;
		$project['modified']   = get_post( $project_id )->post_modified;
		$project['excerpt']    = get_post( $project_id )->post_excerpt;
		$project['square']     = $this->get_project_square_images( $project_id );
		$project['vertical']   = $this->get_project_vertical_image( $project_id );
		$project['slideshow']  = $this->get_project_slideshow( $project_id );
		$project['svg']        = $this->get_project_svg( $project_id );
		$project['partners']   = $this->get_project_partners( $project_id );
		$project['address']    = $this->get_project_address( $project_id );
		$project['alt_name']   = $project['address']['alternate'] ?? '';
		$additional_info_array = $this->get_additional_project_info( $project_id ) ?? [];
		return array_merge( $project, $additional_info_array );
	}


	/**
	 * Get project card as poached from codepen.
	 *
	 * @param int $project_id Project identification or post_id.
	 *
	 * @link https://codepen.io/nickmortensen/pen/yLOrxbW?editors=1100
	 * @return string HTML for an individual project card.
	 */
	public function get_project_card( int $project_id ) : string {
		$output = '';
		[
			'signtypes'     => $signtypes,
			'expertise'     => $expertise,
			'industries'    => $industries,
			'thumb_url'     => $featured,
			'year_complete' => $year,
			'tease'         => $tease,
			'job_id'        => $jobid,
			'address'       => $address,
			'svg'           => $svg,
			'thumbnail'     => $thumbnail_id,
			'modified'      => $last_modified,
			'job'           => $project_name,
			'link'          => $link,
			'vertical'      => $vertical_img_url,
			'slideshow'     => $slides_array,
			'square'        => $square_images_array,
			'partners'      => $partners_array,
			'client'        => $client,
			'alt_name'      => $alternate_name,
		]       = $this->get_all_project_info( $project_id );
		if ( str_word_count( $project_name ) === 2 ) {
			$name         = explode( ' ', $project_name, 2 );
			$rest         = ltrim( $project_name, $name[0] . ' ' );
			$project_name = $name[0] . '<br />' . $name[1];
		}
		$output .= '<div data-project-id="' . $project_id . '" class="single-project-card">';
		$output .= '<div class="open"></div>';
		$output .= wp_sprintf( '<div class="project_completion"><strong>%s</strong></div>', $year );
		$output .= wp_sprintf( '<span class="project_title"><a href="%1$s" title="link to the project page for %2$s" >%2$s</a></span>', $link, $project_name );
		$output .= wp_sprintf( '<div class="project_tease">%s</div>', $tease );
		$output .= wp_sprintf( '<div class="project_image" style="background:center/cover no-repeat url(%s);"></div>', $featured );
		$output .= $signtypes ? Taxonomies::get_card_taxonomy_row( $signtypes ) : ''; // Class of 'project_signtype'.
		$output .= $expertise ? Taxonomies::get_card_taxonomy_row( $expertise ) : ''; // Class of 'project_expertise'.
		$output .= $industries ? Taxonomies::get_card_taxonomy_row( $industries ) : ''; // Class of 'project_industry'.
		$output .= wp_sprintf( '<div class="project_location"><span class="side-title">%s, %s</span></div>', $address['city'], $address['state'] );
		$output .= wp_sprintf( '<div class="project_footnote">%s</div>', $alternate_name );
		$output .= wp_sprintf( '<div class="more_information"><a href="%s">more info</a></div>', $link );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Get secondary project card as to appear on the all porojects page.
	 *
	 * @param int $project_id Project identification or post_id.
	 *
	 * @link https://codepen.io/nickmortensen/pen/yLOrxbW?editors=1100
	 * @link https://codepen.io/julesforrest/pen/QBzaQR
	 *
	 * @return string HTML for an individual project card.
	 */
	public function get_secondary_project_card( int $project_id ) : string {
		$signtypes = $industries = $expertise = '';

		[
			'signtypes'     => $signtypes,
			'expertise'     => $expertise,
			'industries'    => $industries,
			'thumb_url'     => $featured,
			'year_complete' => $year,
			'tease'         => $tease,
			'job_id'        => $jobid,
			'address'       => $address,
			'svg'           => $svg,
			'thumbnail'     => $thumbnail_id,
			'modified'      => $last_modified,
			'job'           => $project_name,
			'link'          => $link,
			'vertical'      => $vertical_img_url,
			'slideshow'     => $slides_array,
			'square'        => $square_images_array,
			'partners'      => $partners_array,
			'client'        => $client,
			'alt_name'      => $alternate_name,
		] = $this->get_all_project_info( $project_id );
		if ( str_word_count( $project_name ) === 2 ) {
			$name         = explode( ' ', $project_name, 2 );
			$rest         = ltrim( $project_name, $name[0] . ' ' );
			$project_name = $name[0] . '<br />' . $name[1];
		}
		if ( 'array' === gettype( $signtypes ) ) {
			$signterms = [];
			foreach ( $signtypes as $term ) {
				$signterms[] = get_term( $term )->slug;
			}
		}
		if ( 'array' === gettype( $industries ) ) {
			$industryterms = [];
			foreach ( $industries as $term ) {
				$industryterms[] = get_term( $term )->slug;
			}
		}
		if ( 'array' === gettype( $expertise ) ) {
			$expertiseterms = [];
			foreach ( $expertise as $term ) {
				$expertiseterms[] = get_term( $term )->slug;
			}
		}

		$allterms = array_merge( $signterms, $industryterms, $expertiseterms );

		$output  = '';
		$output .= wp_sprintf( '<figure data-tags="%s" data-project-id="%d" class="card secondary-project-card">', join( ' ', $allterms ), $project_id );
		$output .= wp_sprintf( '<a href="%s">', $link );
		$output .= wp_sprintf( '<img src="%s">', $featured ?? wp_get_attachment_image_src( 761, 'medium' )[0] );
		$output .= wp_sprintf( '<div data-project-id="%d" class="overlay"></div>', $project_id );
		$output .= wp_sprintf( '<figcaption data-project-id="%d">', $project_id );
		$output .= wp_sprintf( '<h3>%s</h3>', $project_name );
		$output .= wp_sprintf( '<span>%s</span>', $tease );
		$output .= '</figcaption><!-- end div.card-->';
		$output .= '</a>';
		$output .= '</figure><!-- end div.card-->';

		return $output;
	}

	/**
	 * Output the menu for the All Project Page.
	 *
	 * @param str $taxonomy_slug Either 'signtype', 'industry', or 'expertise'.
	 */
	public function make_drilldown_links( $taxonomy_slug ) {
		$terms = [];
		foreach ( Taxonomies::get_all_terms_in_taxonomy( $taxonomy_slug, true ) as $term ) {
			$terms[] = [ $term->name, $term->slug ];
		}
		return $terms;
	}

	/**
	 * Create the extra fields for the post type.
	 *
	 * Use CMB2 to create additional fields for the client post type.
	 *
	 * @since    1.0.0
	 */
	public function additional_fields() {
		$metabox_args = [
			'context'      => 'normal',
			'id'           => 'project-information-metabox',
			'object_types' => [ $this->get_slug() ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Project Overview',
			'show_title'   => false,
			'cmb_styles'   => false,
		];

		$metabox = new_cmb2_box( $metabox_args );

		/**
		 * Get the label for the project address field;
		 */
		function get_label_cb() {
			$html = '<div class="additional_fields project_post"><span>Project Information</span></div>';
			return $html;
		}

		/**
		 * Get general label for the project address field;
		 *
		 * @param string $text The text to markup.
		 */
		function get_general_label_cb( $text ) {
			return '<div class="additional_fields project_post"><span><span>' . ucwords( $text ) . '</span></div>';
		}

		/**
		 * Location of the project - address plus latitude and longitude.
		 */
		$args = [
			'name'       => 'Project',
			'id'         => 'projectLocation', // Name of the custom field type we setup.
			'type'       => 'projectlocation',
			'label_cb'   => get_label_cb(),
			'show_names' => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'    => [ 'project_fields' ],
			'after_row'  => '<hr>',
			'priority'   => 'high',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectAltName: Returns string.
		 * The alternative name for this project.
		 */
		$args = [
			'classes' => [ 'input-full-width' ],
			'name'    => 'Alt',
			'desc'    => 'Is there an alternate name or client for this project?',
			'default' => '',
			'id'      => 'projectAltName',
			'type'    => 'text',
		];
		$metabox->add_field( $args );

		/**
		 * ID projectNarrative: Returns string.
		 * The alternative name for this project.
		 */
		$args = [
			'classes'    => [ 'input-full-width', 'codemirror' ],
			'name'       => 'Narrative',
			'desc'       => 'Construct a project narrative - each new line is a new p element.',
			'default'    => '',
			'id'         => 'projectNarrative',
			'type'       => 'textarea_code',
			'attributes' => [
				'data-codeeditor' => wp_json_encode( [
					'codemirror' => [
						'indentWithTabs' => true,
						'autofocus'      => true,
						'spellcheck'     => true,
						'theme'          => 'jonessign',
					],
				] ),
			],
		];
		$metabox->add_field( $args );

		/**
		 * Group field for General Contractors and partners.
		 */
		$args = [
			'id'          => 'projectPartners',
			'type'        => 'group',
			'description' => 'Partners',
			'repeatable'  => true,
			'show_names'  => false,
			'button_side' => 'right',
			'options'     => [
				'group_title'    => 'Partner {#}', // since version 1.1.4, {#} gets replaced by row number!
				'add_button'     => 'Add Partner',
				'remove_button'  => 'Remove Partner',
				'sortable'       => true,
				'closed'         => true, // true to have the groups closed by default.
				'remove_confirm' => 'Are you sure you want to remove?', // Performs confirmation before removing group.
			],
		];

		$partners = $metabox->add_field( $args );
		/**
		 * ID projectGC: Returns string.
		 * The general contractor.
		 */
		$args = [
			'id'         => 'entry',
			'class'      => 'input-full-width',
			'name'       => 'Partner',
			'show_names' => false,
			'desc'       => '',
			'default'    => '',
			'type'       => 'partner',
		];
		$metabox->add_group_field( $partners, $args );

		/**
		 * SVG Image or Logo of the Client
		 */
		$args = [
			'id'         => 'projectSVGLogo',
			'name'       => 'SVG Logo',
			'type'       => 'file',
			'text'       => [
				'add_upload_file_text' => 'add svg logo',
			],
			'query_args' => [
				'type' => 'image/svg',
			],
		];
		$metabox->add_field( $args );
		/**
		 * Images 4x3 for slideshow.
		 */
		$args = [
			'show_names'   => false,
			'classes'      => [ 'make-button-centered' ],
			'before'       => get_general_label_cb( 'rectangular images' ),
			'id'           => 'projectImagesSlideshow',
			'name'         => 'slideshow',
			'type'         => 'file_list',
			'preview_size' => [ 200, 150 ],
			'query_args'   => [
				'type' => 'image',
			],
			'text'         => [
				'add_upload_files_text' => 'add images (4x3)',
				'remove_image_text'     => 'remove',
				'file_text'             => 'image',
				'file_download_text'    => 'download',
				'remove_text'           => 'standard',
			],
		];
		$metabox->add_field( $args );
		/**
		 * Vertical 3x4 image for tablet display;
		 */
		$args = [
			'show_names'   => false,
			'classes'      => [ 'make-button-centered' ],
			'before'       => get_general_label_cb( 'vertical image ' ),
			'id'           => 'projectVerticalImage',
			'name'         => 'Vertical Img',
			'button_side'  => 'right',
			'type'         => 'file',
			'preview_size' => [ 150, 200 ],
			'query_args'   => [
				'type' => 'image',
			],
			'text'         => [
				'add_upload_file_text' => 'add vertical image',
				'remove_image_text'    => 'remove',
				'file_text'            => 'image',
				'file_download_text'   => 'download',
				'remove_text'          => 'standard',
			],
		];
		$metabox->add_field( $args );

		/**
		 * Square 1x1 image for use within the open graph tags.
		 */
		$args = [
			'show_names'   => false,
			'classes'      => [ 'make-button-centered' ],
			'before'       => get_general_label_cb( 'Square Images' ),
			'id'           => 'projectSquareImages',
			'name'         => 'Square Images',
			'desc'         => 'needs at least one',
			'button_side'  => 'right',
			'type'         => 'file_list',
			'preview_size' => [ 150, 150 ],
			'query_args'   => [
				'type' => 'image',
				// @TODO: figure out a way you only get images that have a size ratio of 1x1.
			],
			'text'         => [
				'add_upload_files_text' => 'add image(s)',
				'remove_image_text'     => 'remove',
				'file_text'             => 'image',
				'file_download_text'    => 'download',
				'remove_text'           => 'standard',
			],
		];
		$metabox->add_field( $args );
	}//end additional_fields()

	/**
	 * Add a new metabox on the post type's edit screen. Shows up on the side.
	 *
	 * @param string $post_type The Type of post - in our case.
	 * @param int    $post The dentifier of the post - the number.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Types
	 * @link https://ducdoan.com/add-custom-field-to-quick-edit-screen-in-wordpress/
	 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
	 */
	public function add_metabox_to_project( $post_type, $post ) {
		$id       = 'project-information-side-metabox';
		$title    = 'Project Info';
		$callback = [ $this, 'display_project_metabox_output' ];
		$screen   = $this->get_slug();
		$context  = 'side';
		$priority = 'high';
		add_meta_box( $id, $title, $callback, $screen, $context, $priority );
	}

	/**
	 * Displays additional fields within the project post type, populating as needed.
	 *
	 * @param int $post The post ID.
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_project_metabox_output( $post ) {
		$html = '';
		wp_nonce_field( 'post_metadata', 'project_metadata_field' );
		$project_info = $this->get_additional_project_info( $post->ID );
		$job_id       = $project_info['job_id'] ?? '1111';
		$client       = $project_info['client'] ?? 'name of client';
		$local_folder = $project_info['local_folder'] ?? 'jobs/';
		$complete     = $project_info['year_complete'] ?? '2020';
		$tease        = $project_info['tease'] ?? '7 word teaser';

		$html .= '<div id="project-sideinfo" class="inline-edit-group wp-clearfix admin_side_information">';
		$html .= '<div class="label_left">';
		$html .= wp_sprintf( '<label for="projectInfo[client]">%s</label>', 'Client' );
		$html .= wp_sprintf( '<input type="text" class="regular_text" name="projectInfo[client]" id="projectInfo[client]" value="%s"/>', $client );
		$html .= '</div>';
		$html .= '<div class="label_left">';
		$html .= wp_sprintf( '<label for="projectInfo[job_id]">%s</label>', 'Job #' );
		$html .= wp_sprintf( '<input type="text" class="regular_text" name="projectInfo[job_id]" id="projectInfo[job_id]" value="%s"/>', $job_id );
		$html .= '</div>';
		$html .= '<div class="label_left">';
		$html .= wp_sprintf( '<label for="projectInfo[year_complete]">%s</label>', 'Complete' );
		$html .= wp_sprintf( '<input type="text" class="text_small" name="projectInfo[year_complete]" id="projectInfo[year_complete]" value="%s"/>', $complete );
		$html .= '</div>';
		$html .= '<div class="label_top">';
		$html .= wp_sprintf( '<label for="projectInfo[tease]">%s</label>', 'Tease' );
		$html .= wp_sprintf( '<input type="text" class="text_small" name="projectInfo[tease]" id="projectInfo[tease]" value="%s"/>', $tease );
		$html .= '</div>';
		$html .= '<div class="label_top">';
		$html .= wp_sprintf( '<label for="projectInfo[local_folder]">%s</label>', 'Local Folder' );
		$html .= wp_sprintf( '<input type="text" class="text_small" name="projectInfo[local_folder]" id="projectInfo[local_folder]" value="%s"/>', $local_folder );
		$html .= '</div>';

		$html .= '</div> <!-- end div#project-sideinfo.inline-edit-group wp-clearfix.admin_side_information-->';
		echo $html;
	}//end display_project_metabox_output()

	/**
	 * Enqueue the javascript to populate the project post type's quickedit fields.
	 */
	public function enqueue_admin_project_quickedit() {
		global $post_type;
		// Ensure that we don't bother using this javascript unless we are in the project post type on the admin end.
		if ( is_admin() && 'project' === $post_type ) :
		// Within the development environment, use the non-minified version.
		$script_uri   = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/quickedit_project.js' ) : get_theme_file_uri( '/assets/js/quickedit_project.min.js' );
		$dependencies = [ 'jquery', 'inline-edit-post' ]; // location: wp-admin/js/inline-edit-post.js.
		$version      = '19';
		$footer       = true;
		wp_enqueue_script( 'project-quickedit', $script_uri, $dependencies, $version, $footer );
		endif;
	}

	/**
	 * Get the 6 most recently modified project posts that are set to 'publish'.
	 *
	 * @param int $quantity How many projects do I want?  Default is 6.
	 */
	public function get_recent_projects( $quantity = 6 ) {
		$args = [
			'post_type'        => 'project',
			'post_status'      => 'publish',
			'posts_per_page'   => $quantity,
			'orderby'          => 'modified',
			'order'            => 'DESC',
			'suppress_filters' => true,
		];
		return new \WP_QUERY( $args );
	}

	/**
	 * Output a menu with the 6 most recently modified projects.
	 */
	public function the_project_menu_items() {
		return $this->get_recent_projects( 6 );
	}

	/**
	 * Output a menu with the 6 most recently modified projects.
	 */
	public function the_projects_menu() {
		$projects = $this->the_project_menu_items()->posts;
		$links    = [];
		foreach ( $projects as $item ) {
			$links[] = wp_sprintf( '<a href="%2$s" data-post="%2$s" title="%4$s">%1$s</a>', $item->post_title, $item->guid, $item->ID, $item->post_excerpt );
		}
		return join( "\r", $links );
	}

	/**
	 * Get information on recent projects.
	 *
	 * @param int $quantity The amount of recent projects.
	 *
	 * @return array An array of recent project ids.
	 */
	public function get_recent_project_ids( $quantity = 6 ) : array {
		$info = [];
		foreach ( $this->get_recent_projects( $quantity )->posts as $project ) {
			$info[] = $project->ID;
		}
		return $info;
	}

	/**
	 * Set additional administrator columns based on postmeta fields that are added to the post type - columns will have no data just yet.
	 *
	 * @param array $columns Array of columns I have already within the quick edit screen. Existing options are 'cb', 'title', 'taxonomy-signtype', 'date'.
	 * @return array The newly added columns plus the existing columns that I have within the quick edit screen.
	 *
	 * @link https://generatewp.com/managing-content-easily-quick-edit/
	 */
	public function make_new_admin_columns( $columns ) {
		global $post_type;
		$adminurl = admin_url( '/wp-admin/edit.php?post_type=' . $post_type . '&orderby=identifier&order=asc' );
		unset( $columns['taxonomy-location'] );
		unset( $columns['date'] );
		$new = [
			'cb'     => array_slice( $columns, 0, 1 ),
			'id'     => 'ID',
			'job_id' => 'Job #',
			'client' => 'Client',
			'tease'  => 'Tease',
			'title'  => array_slice( $columns, 0, 1 ),
		];

		$new_columns['year_complete'] = '<span title="Year Completed?" class="material-icons">calendar_today</span>';
		$new_columns['local_folder']  = '<span title="Local Folder" class="material-icons">apps</span>';

		return array_merge( $new, $columns, $new_columns );
	} // end make_new_admin_columns()

	/**
	 * Get data to include in the new admin columns for this post.
	 *
	 * @param string $column_name Name of the column.
	 * @param int    $post_id ID of the post.
	 */
	public function manage_new_admin_columns( $column_name, $post_id ) {
		global $post_type;
		$html         = '';
		$project_info = $this->get_additional_project_info( $post_id );
		$job_id       = $project_info['job_id'] ?? '';
		$client       = $project_info['client'] ?? '';
		$local_folder = $project_info['local_folder'] ?? '';
		$complete     = $project_info['year_complete'] ?? '2016';
		$tease        = $project_info['tease'] ?? '';

		switch ( $column_name ) {
			case 'id':
				$html .= $post_id;
				break;
			case 'job_id':
				$id    = 'job_id_' . $post_id;
				$html .= sprintf( '<div id="%s"><strong>%s</strong></div>', $id, $job_id );
				break;
			case 'client':
				$id    = 'client_' . $post_id;
				$html .= sprintf( '<div id="%s">%s</div>', $id, $client );
				break;
			case 'tease':
				$id    = 'tease_' . $post_id;
				$html .= sprintf( '<div data-tease="%s" id="%s">%s</div>', $tease, $id, wp_trim_words( $tease, 2 ) );
				break;
			case 'year_complete':
				$id    = 'year_complete_' . $post_id;
				$html .= sprintf( '<div id="%s">%s</div>', $id, $complete );
				break;
			case 'local_folder':
				$color = 'var(--gray-500)';
				$icon  = 'image_not_suppported';
				if ( has_post_thumbnail( $post_id ) ) {
					$color = 'var(--green-500)';
					$icon  = 'image';
				}
				$has_img    = sprintf( '<span style="color:%s" class="material-icons">%s</span>', $color, $icon );
				$id         = 'local_folder_' . $post_id;
				$icon_color = '' !== $local_folder ? 'var(--green-500)' : 'var(--gray-500)';
				$icon_text  = '' !== $local_folder ? 'work' : 'work_off';
				$icon       = sprintf( '<span style="color:%s" class="material-icons">%s</span>', $icon_color, $icon_text );
				$html      .= sprintf( '<div id="%s" data-folder="%s">%s%s</div>', $id, $local_folder, $icon, $has_img );
				break;
			default:
				$html .= '';
		}
		echo $html;
	} // end manage_new_admin_columns()

	/**
	 * Saving meta info (used for both traditional and quick-edit saves)
	 *
	 * @param int $post_id The id of the post.
	 */
	public function save_post( $post_id ) {
		if ( 'project' === get_post_type( $post_id ) ) {
			wp_nonce_field( 'post_metadata', 'project_metadata_field' );
			// check nonce set.
			if ( ! isset( $_POST['project_metadata_field'] ) ) return false;

			// verify nonce.
			if ( ! wp_verify_nonce( $_POST['project_metadata_field'], 'post_metadata' ) ) return false;

			// If not autosaving.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

			$project_info = $_POST['projectInfo'];

			$client        = isset ( $project_info['client'] ) ? sanitize_text_field( $project_info['client'] ) : '';
			$job_id        = isset ( $project_info['job_id'] ) ? sanitize_text_field( $project_info['job_id'] ) : '';
			$local_folder  = isset ( $project_info['local_folder'] ) ? wp_kses_normalize_entities( $project_info['local_folder'] ) : '';
			$year_complete = isset ( $project_info['year_complete'] ) ? sanitize_text_field( $project_info['year_complete'] ) : '';
			$tease         = isset ( $project_info['tease'] ) ? sanitize_text_field( $project_info['tease'] ) : '';

			$newdata = [
				'client'        => $client,
				'job_id'        => $job_id,
				'local_folder'  => $local_folder,
				'year_complete' => $year_complete,
				'tease'         => $tease,
			];

			// This field is saved as serialized data, so I need to use wp_parse_args to get to it.
			update_post_meta( $post_id, 'projectInfo', wp_parse_args( $newdata, get_post_meta( $post_id, 'projectInfo' ) ) );

		}
	} // end save_post()

	/**
	 * Displays our custom content on the quick-edit interface, no values can be pre-populated (all done in JS)
	 *
	 * @param string $column Name of column.
	 * @param string $post_type Name of post type.
	 *
	 * @link https://developer.wordpress.org/reference
	 */
	public function display_quick_edit_custom( $column, $post_type ) {
		$html = '';
		wp_nonce_field( 'post_metadata', 'project_metadata_field' );
		switch ( $column ) {
			case 'year_complete':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[year_complete]">';
				$html .= '<span class="title">Completion</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[year_complete]" class="ptitle" id="year_complete"/><span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'client':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[client]">';
				$html .= '<span class="title">Client</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[client]" class="ptitle" id="client"/><span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'local_folder':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[local_folder]">';
				$html .= '<span class="title">Directory</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[local_folder]" class="ptitle" id="local_folder"/></span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'job_id':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="projectInfo[job_id]">';
				$html .= '<span class="title">Job ID</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="projectInfo[job_id]" class="ptitle" id="job_id"/></span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			case 'tease':
				$html .= '<fieldset class="inline-edit-col-left inline-edit-project clear">';
				$html .= '<div class="inline-edit-group column-' . $column . ' wp-clearfix">';
				$html .= '<label for="jobInfo[tease]">';
				$html .= '<span class="title">Tease</span>';
				$html .= '<span class="input-text-wrap"><input type="text" name="jobInfo[tease]" class="ptitle" id="tease"/></span>';
				$html .= '</label>';
				$html .= '</div>';
				$html .= '</fieldset>';
				break;
			default:
				$html .= '';
		} // End switch.
		echo $html;
	} // end display_quickedit_custom()

	/**
	 * Get project Featured Image the way I want it to look.
	 *
	 * @param int $project_id The post id assigned to the project.
	 *
	 * @return array The IDs of the project thumbnail and the project vertical image.
	 */
	public function get_project_header_images( $project_id ) {
		return PostTypes::get_header_images_ids( $project_id, 'project' );
	}

	/**
	 * Enqueues javascript data that will allow me to access project posts over in assets/src/js/projects
	 */
	public function enqueue_projects_script() {
		// If the AMP plugin is active, return early.
		global $template;
		global $post;

		if ( wp_rig()->is_amp() ) {
			return;
		}

		if ( ! ( 'single-project.php' === basename( $template ) || is_front_page() ) ) {
			return;
		}
		wp_register_script( 'jQuery', 'https://code.jquery.com/jquery-3.5.1.slim.min.js', [], 9, false );

		$in_footer = false;
		// Enqueue the flickity script. The last element asks whether to load the script within the footer. We don't want that.
		$handle  = 'wp-rig-flickity';
		$source  = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/flickity.js' ) : get_theme_file_uri( '/assets/js/flickity.min.js' );
		$version = 'development' === ENVIRONMENT ? wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/src/flickity.js' ) ) : wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/flickity.min.js' ) );
		wp_enqueue_script( $handle, $source, [ 'jQuery' ], $version, $in_footer );

		$handle  = 'wp-rig-projects';
		$source  = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/project.js' ) : get_theme_file_uri( '/assets/js/project.min.js' );
		$version = 'development' === ENVIRONMENT ? wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/src/project.js' ) ) : wp_rig()->get_asset_version( get_theme_file_path( '/assets/js/project.min.js' ) );
		wp_enqueue_script( $handle, $source, [], $version, false );

		/*
		 * Allows us to add the js right within the module.
		 * ESSENTIALLY HANDS THE DATA OFF TO A JAVASCRIPT FILE.
		 * Setting 'precache' to true means we are loading this script in the head of the document.
		 * By setting 'async' to true,it tells the browser to wait until it finishes loading to run the script.
		 * 'Defer' would mean wait until EVERYTHING is done loading to run the script.
		 * We can pick 'defer' because it isn't needed until the visitor hits a scroll point.
		 * No need to precache this, either.
		 * @param string $handle The handle given to the javascript file when enqued.
		 * @param string $key   The name of an attribute to go in the opening <script> tag.
		 * @param string $value The value of the attribute that is denoted with the $key variable.
		 */
		$key = 'defer';
		wp_script_add_data( $handle, $key, true );
		wp_localize_script(
				$handle,
				'projectData',
				[
					'identifiers' => array_values( array_diff( $this->get_recent_project_ids( 8 ), array( $post->ID ) ) ),
					'current'     => $post->ID,
					'resturl'     => rest_url( 'wp/v2/' ),
				]
			);
	} // end enqueue_projects_script()

	/**
	 * Shows a side menu of several projects.
	 *
	 * @param int $projects The number of projects to fetch. Gets the most recently updated projects.
	 */
	public function get_project_sidemenu_items( $projects = 8 ) : array {
		global $template;
		$recent_project_ids = $this->get_recent_project_ids( $projects );

		// When on a project page, no need to have the current project in the side menu, right?
		if ( 'single-project.php' === basename( $template ) ) {
			global $post;  // The current post if we are on a post page.
			$current            = [ $post->ID ];
			$total              = (int) $projects + 1;
			$recent_project_ids = array_diff( $this->get_recent_project_ids( $total ), $current );
		}
		$project_links = [];
		foreach ( $recent_project_ids as $index => $project ) {
			$url             = get_post_permalink( $project );
			$excerpt         = get_post( $project )->post_excerpt;
			$title           = str_replace( '& Casino', '', get_post( $project )->post_title );
			$project_links[] = wp_sprintf( '<a href="%s" title="%s">%s</a>', $url, $excerpt, $title );
		}
		return $project_links;
	}

}//end class
