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
use function add_filter;


/**
 * TABLE OF CONTENTS.
 * 1. get_state_options()
 * 2. get_slug()
 * 3. initialize()
 * 4. cmb2_render_rating_field_callback() -- Star Rating.
 * 5. render_address_field_callback().
 * 7. render_jonesaddress_field_callback().
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
	 * The 50 United States.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type The arguments of this taxonomy..
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
	 * States workaround
	 */

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
	 * The job statuses
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type The arguments of this taxonomy..
	 */
	public static $statuses = [
		'complete' => 'Complete',
		'ongoing'  => 'Ongoing',
		'upcoming' => 'Upcoming',
	];
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
			$options .= "<input type=\"radio\" name=\"status\" id=\"status_$a\" value=\"$a\" $selected><label for=\"status_$a\">$b</label>";
		}
		return $options;
	}


	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'additionalfields';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_filter( 'cmb2_render_jonesaddress', [ $this, 'render_jonesaddress_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_rating', [ $this, 'cmb2_render_rating_field_callback' ], 10, 5 );
		// add_action( 'cmb2_admin_init', [ $this, 'register_dynamic_fields_box' ] );
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
	public function add_fields_dynamically_to_box( $cmb ) {

	}

	/**
	 * Render 'STAR RATING' custom field type
	 *
	 * @since 0.1.0
	 *
	 * @param array  $field              The passed in `CMB2_Field` .
	 * @param mixed  $value              The value of this field escaped. It defaults to `sanitize_text_field`.
	 * @param int    $object_id          The ID of the current object.
	 * @param string $object_type        The type of object you are working with. Most commonly, `post` (this applies to all post-types),but could also be `comment`, `user` or `options-page`.
	 * @param object $field_type_object  The `CMB2_Types` object.
	 */
	public function cmb2_render_rating_field_callback( $field, $value, $object_id, $object_type, $field_type_object ) {
			?>
				<section id="cmb2-star-rating-metabox">
					<fieldset>
						<span class="star-cb-group">
							<?php
								$y = 5;
								while ( $y > 0 ) {
									?>
										<input type="radio" id="rating-<?php echo $y; ?>" name="<?php echo $field_type_object->_id( false ); ?>" value="<?php echo $y; ?>" <?php checked( $value, $y ); ?>/>
										<label for="rating-<?php echo $y; ?>"><?php echo $y; ?></label>
									<?php
									$y--;
								}
							?>
						</span>
					</fieldset>
				</section>
			<?php
			echo $field_type_object->_desc( true );

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
	 * Use the id to get the post title - the name of the staffmember - and output a best guess for the email address.
	 *
	 * @param int $id The id of the post.
	 */
	public function get_email_default( $id ) {
		$name    = preg_split( '#\s+#', get_the_title( $id ), 2 );
		$default = strtolower( substr( $name[0], 0, 1 ) . str_replace( ' ', '', $name[1] ) . '@jonessign.com' );
		return $default;
	}

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

}//end class
