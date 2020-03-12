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
// use WP_Post;
// use function wp_enqueue_script;
// use function get_theme_file_uri;
// use function get_theme_file_path;
// use function wp_script_add_data;
// use function wp_localize_script;


/**
 * Class for creating additional fields that are not part of the arsenal provided by CMB2.
 */
class Component implements Component_Interface {

	/**
	 * The 50 United States.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type The arguments of this taxonomy..
	 */
	public $states = [
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
	 * Return state options as HTML for the select field entitled 'State' in the address CMB2 field type.
	 *
	 * @link https://developer.wordpress.org/reference/functions/selected/
	 * @param string $value From the containing function.
	 * @return string $options The html for the options within a select field of 'State'.
	 */
	public function get_state_options( $value = '' ) {
		$states  = $this->states;
		$states  = [ '' => esc_html( 'Select a State' ) ] + $states;
		$options = '';
		foreach ( $states as $abbrev => $state ) {
			$selected = selected( $value, $abbrev, false );
			$options .= "<option value=\"$abbrev\" $selected>$state</option>";
		}
		return $options;
	}


	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */

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
		add_filter( 'cmb2_render_address', [ $this, 'render_address_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_jonesaddress', [ $this, 'render_jonesaddress_field_callback' ], 10, 5 );
		add_filter( 'cmb2_render_rating', [ $this, 'cmb2_render_rating_field_callback' ], 10, 5 );
	}


	/**
	 * Render 'star rating' custom field type
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
		$y      = 5;
		$name   = $field_type->_id( false );
		$inputs = [];
		while ( $y > 0 ) {
				$field_label  = "<input type=\"radio\" id=\"rating-$y\" name=\"$name\" value=\"$y" . checked( $value, $y ) . '/>';
				$field_label .= "<label for=\"rating-$y\"><?php echo $y; ?></label>";
				$inputs[]     = $field_label;
				$y--;
		}
		$fields = implode( '', $inputs );
		$output = <<<STARRATING
		<section id="cmb2-star-rating-metabox">
			<fieldset>
				<span class="star-cb-group">
				$fields
				</span>
			</fieldset>
		</section>
STARRATING;
		echo $output;
	} //end cmb2_render_rating_field_callback

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
			<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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

	<section class="jonesAddressFields">
		<!-- address -->
		<div class="field-div" data-fieldid="address">
			<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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
				<span class="innerlabel">
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


/*
CHANGE THESE IN DATABASE
locationAddress = address
locationCity = city
locationState = state
locationZip = zip
locationLatitude = latitude
locationLongitude = longitude
locationGoogleCID = googleCID
*/
