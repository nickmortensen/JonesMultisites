<?php
/**
 * WP_Rig\WP_Rig\AdditionalFields\Component class.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\AdditionalFields;

use WP_Rig\WP_Rig\Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function wp_sprintf;
use function add_filter;

/**
 * TABLE OF CONTENTS.
 * get_state_options()
 * get_slug()
 * initialize()
 * cmb2_render_rating_field_callback() -- Star Rating.
 * render_address_field_callback().
 * render_jonesaddress_field_callback().
 */

/**
 * Class for creating additional fields that are not part of the arsenal provided by CMB2.
 *
 * This class is used to create field types containing several inputs for use within custom post types, taxonomies, and attachments.
 *
 * @property array $states
 */
class Component implements Component_Interface {
	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'cmb2_render_testimonial', [ $this, 'render_testimonial_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_timeline', [ $this, 'cmb2_render_timeline_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_client', [ $this, 'render_client_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_partner', [ $this, 'render_partner_field_callback' ], 10, 5 ); // CMB2 field specifically for a project partner.
		add_filter( 'cmb2_render_projectlocation', [ $this, 'render_projectlocation_field_callback' ], 10, 5 ); // CMB2 field specifically for a project address.
		add_filter( 'cmb2_render_jonesaddress', [ $this, 'render_jonesaddress_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_rating', [ $this, 'cmb2_render_rating_field_callback' ], 10, 5 );
	}

	/**
	 * The job statuses
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $statuses The possible statuses for a project.
	 */
	public static $statuses = [
		'complete' => 'Complete',
		'ongoing'  => 'Ongoing',
		'upcoming' => 'Upcoming',
	];
	/**
	 * The 50 United States.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $states The 50states as an associative array using their abbreviations as key.
	 */
	public static $states = [
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'DC' => 'District Of Columbia',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
	];

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'additionalfields';
	}

	/**
	 * Return state options as HTML for the select field entitled 'State' in the address CMB2 field type.
	 *
	 * @link https://developer.wordpress.org/reference/functions/selected/
	 * @param string $value From the containing function.
	 * @return string $options The html for the options within a select field of 'State'.
	 */
	public static function get_state_options( $value = '' ) {
		$states  = self::$states;
		$states  = [ '' => esc_html( 'Select a State' ) ] + $states;
		$options = '';
		foreach ( $states as $abbrev => $state ) {
			$selected = selected( $value, $abbrev, false );
			$options .= "<option value=\"$abbrev\" $selected>$state</option>";
		}
		return $options;
	}

	/**
	 * Return Status options as HTML for the select field entitled 'State' in the address CMB2 field type.
	 *
	 * @link https://developer.wordpress.org/reference/functions/selected/
	 * @param string $value From the containing function.
	 * @return string $options The html for the options within a select field of 'State'.
	 */
	public static function get_status_options( $value = '' ) {
		$statuses = self::$statuses;
		$options  = '';
		foreach ( $statuses as $a => $b ) {
			$selected = checked( $value, $a, false );
			// phpcs:disable
			// $options .= "<input type=\"radio\" name=\"status\" id=\"status_$a\" value=\"$a\" $selected><label for=\"status_$a\">$b</label>";
			// phpcs:enable
			$options .= wp_sprintf( '<input type="radio" name="status" id="status_%0$s" value="%0$s" %1$s><label for="status_%0$s">%2$s</label>', $a, $selected, $b );
		}
		return $options;
	}

	/**
	 * Adding new fields dynamically.
	 *
	 * @link https://geek.hellyer.kiwi/2018/08/11/dynamically-controlling-cmb2-metaboxes/
	 */
	public function register_fields_dynamically() {}

	/**
	 * Adding fields dynamically to box.
	 *
	 * @param obj $cmb Object containing the fields.
	 * @link https://geek.hellyer.kiwi/2018/08/11/dynamically-controlling-cmb2-metaboxes/
	 */
	public function add_fields_dynamically_to_box( $cmb ) {}


	/**
	 * Render 'STAR RATING' custom field type
	 *
	 * @since 0.1.0
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function cmb2_render_rating_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$output  = '<section id="cmb2-star-rating-metabox">';
		$output .= '<fieldset>';
		$output .= '<span class="star-cb-group">';
		$y       = 5;
		while ( $y > 0 ) {
			$checked = checked( $value, $y, false ); // false option returns, true option echos.
			$output .= wp_sprintf( '<input type="radio" id="rating-%1$s" name="%2$s" value="%1$s" %3$s />', $y, $field_type->_id( false ), $checked );
			$output .= wp_sprintf( '<label data-wanker="yes" for="rating-%s">%s</label>', $y );
			$y--;
		}
		$output .= '</span>';
		$output .= '</fieldset>';
		$output .= '</section>';
		$output .= $field_type->_desc( true );
		echo $output;
	}

	/**
	 * Render 'Timeline' custom field type
	 *
	 * @since 0.1.0
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function cmb2_render_timeline_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new   = [
			'title' => '',
			'date'  => '',
			'media' => '',
		];
		$value = wp_parse_args( $value, $new );
		?>

		<style>
			#timeline_fields {

			}

			#timeline_fields > div {
				display: grid;
				grid-column: 1 / -1;
				grid-row: span 1;
				margin-top: 8px;
				grid-template-columns: 1fr 5fr;
				grid-template-rows: 1fr;
				justify-items: flex-start;

			}

			#timeline_fields > div > label {
				grid-column: 1 / 2;
			}

			input,
			textarea,
			select {
				grid-column: 2 / -1;
			}

			label {
				margin-top: 8px;
				font-weight: var(--semibold);
			}
		</style>
<section id="timeline_fields">

<div>
	<label for="<?= $field_type->_id( '_title' ); ?>">Event Title</label>
	<?= $field_type->input(
		[
			'name'  => $field_type->_name( '[title]' ),
			'id'    => $field_type->_id( '_title' ),
			'value' => $value['title'],
			'desc'  => '',
		]
	);
	?>
</div><!-- /event -->

<div>
	<label for="<?= $field_type->_id( '_media' ); ?>">Media</label>
	<?= $field_type->input(
		[
			'name'  => $field_type->_name( '[media]' ),
			'id'    => $field_type->_id( '_media' ),
			'value' => $value['media'],
			'desc'  => '',
		]
	); ?>
</div>

</section><!-- end section#timeline_fields -->
		<?php
	}


	/**
	 * Render Address Field
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function render_address_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_values = [
			'address-1' => '',
			'city'      => '',
			'state'     => '',
			'zip'       => '',
			'latitude'  => '',
			'longitude' => '',
		];
		$value      = wp_parse_args( $value, $new_values );
	?>

<style>
	.projectAddressFields {
		display: grid;
		background: var(--yellow-400);
		grid-template-columns: 1fr, 3fr;
		grid-template-rows: repeat( 10, 1fr );
	}
</style>

	<section class="projectAddressFields">
		<!-- address-1 -->
		<div class="field-div" data-fieldid="address1">
			<span>
				<label for="<?= $field_type->_id( '_address_1', false ); ?>">Address</label>
			</span>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[address-1]' ),
					'id'    => $field_type->_id( '_address_1' ),
					'value' => $value['address-1'],
					'desc'  => '',
				]
			);
			?>
		</div><!-- /address-1 -->

		<!-- city state zip -->
		<div class="citystatezip">
			<!-- city-->
			<div class="field-div" data-fieldid="city">
				<span>
					<label for="<?= $field_type->_id( '_city' ); ?>'">City</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[city]' ),
						'id'    => $field_type->_id( '_city' ),
						'value' => $value['city'],
						'desc'  => '',
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /city -->

			<!-- state -->
			<div class="field-div" data-fieldid="state">
				<span>
					<label for="<?= $field_type->_id( '_state' ); ?>'">State</label>
				</span>
				<?= $field_type->select(
					[
						'name'    => $field_type->_name( '[state]' ),
						'id'      => $field_type->_id( '_state' ),
						'options' => $this->get_state_options( $value['state'] ),
						'desc'    => '',
					]
				);
				?>
			</div><!-- /state -->

			<!-- /zip -->
			<div class="field-div" data-fieldid="zip">
				<span>
					<label for="<?= $field_type->_id( '_zip' ); ?>'">Zip</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[zip]' ),
						'id'    => $field_type->_id( '_zip' ),
						'value' => $value['zip'],
						'type'  => 'text_small',
						'desc'  => '',
					]
				);
				?>
			</div><!-- /zip -->
		</div><!-- /city state zip -->

		<!-- coordinates -->
		<div class="coordinates">
			<div data-fieldid="latitude" class="field-div">
				<span>
					<label for="<?=$field_type->_id( '_latitude' ); ?>'">Latitude</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[latitude]' ),
						'id'    => $field_type->_id( '_latitude' ),
						'value' => $value['latitude'],
						'desc'  => '',
					]
				);
				?>
			</div><!-- /latitude -->
			<div data-fieldid="longitude" class="field-div">
				<span>
					<label for="<?= $field_type->_id( '_longitude' ); ?>'">Longitude</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[longitude]' ),
						'id'    => $field_type->_id( '_longitude' ),
						'value' => $value['longitude'],
						'desc'  => '',
					]
				);
				?>
			</div><!-- /longitude -->
		</div><!-- /coordinates -->

	</section><!-- end section.projectaddressfields -->
	<?php
	}//end render_address_field_callback()

	/**
	 * Render Address Field For Jones Sign Company Location
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function render_jonesaddress_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_values = [
			'address'   => '',
			'city'      => '',
			'state'     => '',
			'phone'     => '',
			'fax'       => '',
			'email'     => '',
			'zip'       => '',
			'latitude'  => '',
			'longitude' => '',
			'googleCID' => '',
		];
		$value      = wp_parse_args( $value, $new_values );
	?>
	<style>
	.jonesAddressFields {
		display: grid;
		grid-template-columns: repeat(10, 1fr);
		grid-template-rows: repeat( 10, auto );
	}

	.field-div,
	.citystatezip,
	.phonefaxemail,
	.coordinates {
		grid-column: 1 / -1;
		grid-row: span 2;
	}

	.field-div {
		display: grid;
		margin-top: 8px;
		grid-template-columns: 1fr 5fr;
	}

	.field_div > span {
		grid-column: span 2;
	}

	.field_div > input,
	.field_div > select {
		grid-column: 2 / -1;
	}

	.field_div > select {
		margin-right: 24px;
	}
	select#locationAddress_state {
		max-width: 54%;
	}

	label {
		font-weight: var(--semibold);
	}
</style>

	<section class="jonesAddressFields">
		<!-- address -->
		<div class="field-div" data-fieldid="address">
			<span>
				<label for="<?= $field_type->_id( '_address', false ); ?>">Address</label>
			</span>
			<?= $field_type->input(
				[
					'name'  => $field_type->_name( '[address]' ),
					'id'    => $field_type->_id( '_address' ),
					'value' => $value['address'],
				]
			);
			?>
		</div><!-- /address-1 -->

		<!-- city state zip -->
		<div class="citystatezip">
			<!-- city-->
			<div class="field-div" data-fieldid="city">
				<span>
					<label for="<?= $field_type->_id( '_city' ); ?>'">City</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[city]' ),
						'id'    => $field_type->_id( '_city' ),
						'value' => $value['city'],
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /city -->

			<!-- state -->
			<div class="field-div" data-fieldid="state">
				<span>
					<label for="<?= $field_type->_id( '_state' ); ?>'">State</label>
				</span>
				<?= $field_type->select(
					[
						'name'    => $field_type->_name( '[state]' ),
						'id'      => $field_type->_id( '_state' ),
						'options' => $this->get_state_options( $value['state'] ),
					]
				);
				?>
			</div><!-- /state -->

			<!-- /zip -->
			<div class="field-div" data-fieldid="zip">
				<span>
					<label for="<?= $field_type->_id( '_zip' ); ?>'">Zip</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[zip]' ),
						'id'    => $field_type->_id( '_zip' ),
						'value' => $value['zip'],
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /zip -->
		</div><!-- /city state zip -->


		<!-- phone fax email -->
		<div class="phonefaxemail">
			<!-- phone-->
			<div class="field-div" data-fieldid="phone">
				<span>
					<label for="<?= $field_type->_id( '_phone' ); ?>'">Phone</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[phone]' ),
						'id'    => $field_type->_id( '_phone' ),
						'value' => $value['phone'],
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /phone -->

			<!-- /fax -->
			<div class="field-div" data-fieldid="fax">
				<span>
					<label for="<?= $field_type->_id( '_fax' ); ?>'">Fax</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[fax]' ),
						'id'    => $field_type->_id( '_fax' ),
						'value' => $value['fax'],
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /fax -->

			<!-- email -->
			<div class="field-div" data-fieldid="email">
				<span>
					<label for="<?= $field_type->_id( '_email' ); ?>'">Email</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[email]' ),
						'id'    => $field_type->_id( '_email' ),
						'value' => $value['email'],
						'type'  => 'text_email',
					]
				);
				?>
			</div><!-- /email-->
		</div><!-- /phone fax email -->

		<!-- coordinates+googleCID -->
		<div class="coordinates">
			<!-- latitude -->
			<div data-fieldid="latitude" class="field-div">
				<span>
					<label for="<?=$field_type->_id( '_latitude' ); ?>'">Latitude</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[latitude]' ),
						'id'    => $field_type->_id( '_latitude' ),
						'value' => $value['latitude'],
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /latitude -->
			<!-- longitude -->
			<div data-fieldid="longitude" class="field-div">
				<span>
					<label for="<?= $field_type->_id( '_longitude' ); ?>'">Longitude</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[longitude]' ),
						'id'    => $field_type->_id( '_longitude' ),
						'value' => $value['longitude'],
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /longitude -->
			<!-- googleCID -->
			<div data-fieldid="googleCID" class="field-div">
				<span>
					<label for="<?= $field_type->_id( '_googleCID' ); ?>'">googleCID</label>
				</span>
				<?= $field_type->input(
					[
						'name'  => $field_type->_name( '[googleCID]' ),
						'id'    => $field_type->_id( '_googleCID' ),
						'value' => $value['googleCID'],
						'type'  => 'text_small',
					]
				);
				?>
			</div><!-- /googleCID -->
		</div><!-- /coordinates+googleCID -->

	</section><!-- end section.jonesAddressFields -->
	<?php
	}//end render_jonesaddress_field_callback()

	/**
	 * Create & Render a Project Address Field to use with CMB2
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function render_projectlocation_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_values = [
			'address'   => '',
			'url'       => '',
			'city'      => '',
			'state'     => '',
			'zip'       => '',
			'latitude'  => '',
			'longitude' => '',
			'alternate' => '',
		];

		$value = wp_parse_args( $value, $new_values );
		?>
		<div class="alignleft">
			<p><label for="<?= $field_type->_id( '_address' ); ?>">Address</label> </p>
			<?=
			$field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[address]' ),
				'id'    => $field_type->_id( '_address' ),
				'value' => $value['address'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p> <label for="<?= $field_type->_id( '_url' ); ?>">Website</label> </p>
			<?=
			$field_type->input( [
				'name'  => $field_type->_name( '[url]' ),
				'id'    => $field_type->_id( '_url' ),
				'value' => $value['url'],
				'desc'  => '',
			] ); ?>
		</div>
		<br class="clear">
		<div class="alignleft">
			<p>
				<label for="<?= $field_type->_id( '_city' ); ?>'">City</label>
			</p>
			<?=
			$field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[city]' ),
				'id'    => $field_type->_id( '_city' ),
				'value' => $value['city'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p>
				<label for="<?= $field_type->_id( '_state' ); ?>'">State</label>
			</p>
			<?=
			$field_type->select( [
				'name'    => $field_type->_name( '[state]' ),
				'id'      => $field_type->_id( '_state' ),
				'options' => self::get_state_options( $value['state'] ),
				'desc'    => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p>
				<label for="<?= $field_type->_id( '_zip' ); ?>'">Zip</label>
			</p>
			<?=
			$field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[zip]' ),
				'id'    => $field_type->_id( '_zip' ),
				'value' => $value['zip'],
				'type'  => 'number',
				'desc'  => '',
			] ); ?>
		</div>
		<br class="clear">
		<div class="alignleft">
			<p>
				<label for="<?= $field_type->_id( '_latitude' ); ?>'">Latitude</label>
			</p>
			<?=
			$field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[latitude]' ),
				'id'    => $field_type->_id( '_latitude' ),
				'value' => $value['latitude'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p><label for="<?= $field_type->_id( '_longitude' ); ?>'">Longitude</label></p>
			<?=
			$field_type->input( array(
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[longitude]' ),
				'id'    => $field_type->_id( '_longitude' ),
				'value' => $value['longitude'],
				'desc'  => '',
			) ); ?>
		</div>
		<div class="alignleft" style="padding-left: 12px;">
			<p><label for="<?= $field_type->_id( '_alternate' ); ?>'">Project Alt Name</label></p>
			<?=
			$field_type->input( array(
				'class' => 'cmb_text_medium',
				'name'  => $field_type->_name( '[alternate]' ),
				'id'    => $field_type->_id( '_alternate' ),
				'value' => $value['alternate'],
				'desc'  => '',
			) ); ?>
		</div>
		<br class="clear">
	<?= $field_type->_desc( true );
	} // end render_projectlocation_field_callback()

	/**
	 * Create & Render Partner Information Fields
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type         The `CMB2_Types` object.
	 */
	public function render_partner_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_values = [
			'partner' => '',
			'type'    => '',
			'url'     => '',
		];

		$value = wp_parse_args( $value, $new_values );
		?>
		<div class="alignleft">
			<p> <label for="<?= $field_type->_id( '_partner' ); ?>">Partner</label> </p>
			<?=
			$field_type->input( [
				'class' => 'cmb_text_medium',
				'name'  => $field_type->_name( '[partner]' ),
				'id'    => $field_type->_id( '_partner' ),
				'value' => $value['partner'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="margin-left: 14px;">
			<p> <label for="<?= $field_type->_id( '_type' ); ?>'">Type</label> </p>
			<?=
			$field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[type]' ),
				'id'    => $field_type->_id( '_type' ),
				'value' => $value['type'],
				'desc'  => '',
			] ); ?>
		</div>
		<div class="alignleft" style="margin-left: 14px;">
			<p> <label for="<?= $field_type->_id( '_url' ); ?>'">URL</label> </p>
			<?=
			$field_type->input( [
				'class' => 'cmb_text_small',
				'name'  => $field_type->_name( '[url]' ),
				'id'    => $field_type->_id( '_url' ),
				'value' => $value['url'],
				'desc'  => '',
			] ); ?>
		</div>
		<br class="clear">
	<?= $field_type->_desc( true );
	} //end render_partner_field_callback

	/**
	 * Create & Render a Client Information Field for CMB2.
	 *
	 * @param array  $field       The passed in `CMB2_Field` .
	 * @param mixed  $value       The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id   The ID of the current object.
	 * @param string $object_type The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type  The `CMB2_Types` object.
	 */
	public function render_client_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
		$new_value = [
			'company' => '',
			'website' => '',
			'since'   => '',
		];

		$value = wp_parse_args( $value, $new_value );
		$html  = '';
		$html .= '<style>
		section.custom_field_section {
			min-height: 260px;
			display: flex;
			flex-flow: column nowrap;
			justify-content: space-around;
			align-items: stretch;
		}
		.admin-additional-field {
			padding-bottom: 1.6rem;
			display: flex;
			flex-flow: row nowrap;
			justify-content: flex-start;
			align-items: space-around;
		}
		.admin-additional-field > input {
			position: absolute;
			left: 20%;
		}
		</style>';

		$html .= '<section class="custom_field_section">';

		$field       = 'Company';
		$label       = $field_type->_id( '_company' );
		$name        = $field_type->_name( '[company]' );
		$val         = $value['company'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];

		$html .= '<div class="admin-additional-field">';
		$html .= '<label for="' . $label . '">' . $field . '</label>';
		$html .= $field_type->input( $field_array );
		$html .= '</div>';

		$field       = 'Website';
		$label       = $field_type->_id( '_website' );
		$name        = $field_type->_name( '[website]' );
		$val         = $value['website'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html       .= '<div class="admin-additional-field">';
		$html       .= '<label for="' . $label . '">' . $field . '</label>';
		$html       .= $field_type->input( $field_array );
		$html       .= '</div>';

		$field       = 'Since';
		$label       = $field_type->_id( '_since' );
		$name        = $field_type->_name( '[since]' );
		$val         = $value['since'];
		$field_array = [
			'name'  => $name,
			'id'    => $label,
			'value' => $val,
			'desc'  => '',
		];
		$html       .= '<div class="admin-additional-field">';
		$html       .= '<label for="' . $label . '">' . $field . '</label>';
		$html       .= $field_type->input( $field_array );
		$html       .= '</div>';

		$html .= '</section>';
		echo $html;
	} //end render_clientele_field_callback()



}
