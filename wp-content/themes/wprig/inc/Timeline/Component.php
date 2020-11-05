<?php
/**
 * WP_Rig\WP_Rig\Timeline\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Timeline;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Query;
use function add_action;
use function get_terms;
use function get_term;
use function get_term_meta;
use function register_taxonomy;

/**
 * NOTE: See description on the google rich snippet for product.
 *
 * @link https://developers.google.com/search/docs/data-types/product
 */
class Component implements Component_Interface, Templating_Component_Interface {
	/**
	 * The slug of this taxonomy.
	 *
	 * @access   private
	 * @var      string    $slug The slug of this taxonomy..
	 */
	private $slug = 'timeline';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return $this->slug;
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'cmb2_init', [ $this, 'create_timeline_post_extra_fields' ] );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [];
	}
	/**
	 * Filter to show only on a page with an id of 605 -- which is 'Jones Timeline'.
	 */
	public function only_show_on_timeline_post() {
		global $post;
		return 605 === $post->ID;
	}

	/**
	 * Event Repeater Field Label.
	 *
	 * @param string $content The content of the heading.
	 * @param string $color The color of the background.
	 */
	public function field_label( $content, $color = 'blue' ) {
		return '<span style="display: block; width: 100%; margin: 0; margin-left: -10px; padding: 0.5rem; color: #fff; background: var(--' . $color . '-600);">' . $content . '</span>';
	}

	/**
	 * Timeline post extra fields using custom timeline field.
	 */
	public function create_timeline_post_extra_fields() {
		// Create the metabox in which to display the timeline field.
		$args    = [
			'id'           => 'timeline-edit',
			'title'        => 'Timeline Event Entry',
			'object_types' => [ 'page' ],
			'show_on'      => [
				'id' => 605,
			],
			'closed'       => false,
		];
		$metabox = new_cmb2_box( $args );

		// Group name for event.
		$args     = [
			'name'             => 'Eventsy',
			'before_group'     => $this->field_label( 'Event Repeating Label Function Output HEre', 'red' ),
			'after_group'      => $this->field_label( 'After Group Label Here', 'red' ),
			'after_group_row'  => $this->field_label( 'End of Timeline Event Row', 'orange' ),
			'before_group_row' => $this->field_label( 'Begin Timeline Event Row', 'orange' ),
			'show_name'        => false,
			'description'      => 'Timeline Events',
			'id'               => 'event',
			'type'             => 'group',
			'repeatable'       => true,
			'button_side'      => 'right',
			'options'          => [
				'group_title'    => 'Event',
				'add_button'     => 'Add Another Event',
				'remove_button'  => 'Remove This Event',
				'sortable'       => true,
				'remove_confirm' => 'Are you certain you wish to remove this event?',
			],
		];
		$timeline = $metabox->add_field( $args );

		// Title of Event.
		$args = [
			'classes' => [ 'align-left' ],
			'name'    => 'Event Title',
			'desc'    => 'Event title',
			'default' => '',
			'id'      => 'eventTitle',
			'type'    => 'text',
		];
		$metabox->add_group_field( $timeline, $args );

		// Month and year.
		$args = [
			'classes'     => [ 'align-right' ],
			'name'        => 'Event Date',
			'id'          => 'eventDate',
			'desc'        => 'Month and year of Event',
			'type'        => 'text_date',
			// 'date_format' => 'm_yy',
			'attributes'  => [
				'data-datepicker' => wp_json_encode(
					[
						'yearRange' => '1918:+0',
					]
				),
			],
		];
		$metabox->add_group_field( $timeline, $args );

		// HTML Description of Event -- USES CODEMIRROR.
		$args = [
			'name'    => 'Description',
			'desc'    => 'Longer Description of the event - can use html tags',
			'default' => '',
			'id'      => 'eventDescription',
			'type'    => 'textarea_small',
		];
		$metabox->add_group_field( $timeline, $args );

		// Media Field.
		$args = [
			'name' => 'media',
			'id'   => 'eventMedia',
			'type' => 'file_list',
			'text' => [
				'add_upload_files_text' => 'Add Media Items',
				'remove_image_text'     => 'Remove Media',
				'file_text'             => 'File',
				'file_download_text'    => 'Download',
				'remove_text'           => 'Remove',
			],
		];
		$metabox->add_group_field( $timeline, $args );
	}

	/**
	 * Create Timeline Post Extra Fields.
	 */
	public function backup_create_timeline_post_extra_fields() {
		$prefix  = 'timeline';
		$args    = [
			'id'           => $prefix . '-edit',
			'title'        => 'timeline post fields',
			'object_types' => [ 'page' ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_on_cb'   => [ $this, 'only_show_on_timeline_post' ],
		];
		$metabox = new_cmb2_box( $args );

		// Common Name.
		$args = [
			'name'        => 'Event',
			'description' => 'A Timeline Event',
			'id'          => 'timelineEventGroup',
			'type'        => 'group',
			'repeatable'  => true,
			'button_side' => 'right',
			'options'     => [
				'group_title'    => 'Event',
				'add_button'     => 'Add Another Event',
				'remove_button'  => 'Remove This Event',
				'sortable'       => true,
				'remove_confirm' => 'Are you certain you wish to remove this event?',
			],
		];
		$timeline = $metabox->add_field( $args );

			// Title of Event.
			$args = [
				'classes' => [ 'input-full-width' ],
				'name'    => 'Event Title',
				'desc'    => 'Event title',
				'default' => '',
				'id'      => $prefix . 'EventTitle',
				'type'    => 'text',
			];
			$metabox->add_group_field( $timeline, $args );
			// Testimonial From the client.
			$args = [
				'classes' => [ 'input-full-width' ],
				'name'    => 'Event Title',
				'desc'    => 'Event title',
				'default' => '',
				'id'      => $prefix . 'EventTitle',
				'type'    => 'text',
			];
			$metabox->add_group_field( $timeline, $args );

			// Month and year.
			$args = [
				'name'        => 'Event Date',
				'id'          => $prefix . 'EventDate',
				'desc'        => 'Month and year of Event',
				'type'        => 'text_date',
				'date_format' => 'm_yy',
				'attributes'  => [
					'data-datepicker' => wp_json_encode(
						[
							'yearRange' => '1918:0',
						]
					),
				],
			];
			$metabox->add_group_field( $timeline, $args );
			$args = [
				'name'    => 'Description',
				'desc'    => 'field description (optional)',
				'default' => '',
				'id'      => $prefix . 'EventDescription',
				'type'    => 'textarea_code',
			];
			$metabox->add_group_field( $timeline, $args );
			$args = [
				'name'    => 'Images',
				'desc'    => 'Images to Attach',
				'default' => '',
				'id'      => $prefix . 'EventImages',
				'type'    => 'file_list',
			];
			$metabox->add_group_field( $timeline, $args );
	}

}

