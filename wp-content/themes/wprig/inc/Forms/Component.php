<?php
/**
 * WP_Rig\WP_Rig\Forms\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Forms;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\Global_Taxonomies\Component as Taxonomies;
use WP_Query;
use function add_action;
use function get_terms;
use function get_term;
use function get_term_meta;

/**
 * Class to create forms.
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
		return 'forms';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
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
			'install_photo_slideshow_form' => [ $this, 'install_photo_slideshow_form' ],
			'personal_finances_form'       => [ $this, 'personal_finances_form' ],
		];
	}

	/**
	 * Default arguments for a form field.
	 *
	 * @access   private
	 *
	 * @var array $form_defaults The default argument sfor a form field.
	 */
	private $form_defaults = [
		'tabindex'    => '0',
		'type'        => 'text',
		'id'          => 'input_id',
		'name'        => 'field_name',
		'default'     => 'default_value',
		'placeholder' => 'default',
	];

	/**
	 * Google Sheets WebAppURL
	 *
	 * @access   private
	 *
	 * @var array $google_sheet_link The default argument sfor a form field.
	 */
	private $google_sheet_link = [
		'days_since_injury'   => [
			'sheet' => 'https://docs.google.com/spreadsheets/d/16N9qk11V-Hd_VEp171MgQzSKGPuOwUBGcZcBZZ8yFTM/edit#gid=0',
			'app'   => 'https://script.google.com/macros/s/AKfycbw7AyRYqkQcPM_bUdpVbou26f4agivb2bT8gv8uc9eVDGi-lYg/exec',
		],
		'personal_finance'    => [
			'sheet' => '',
			'app'   => '',
		],
		'estimate_throughput' => [
			'sheet' => 'https://docs.google.com/spreadsheets/d/1JkoXp5PNSzxLcgN0RHhDY4k-8TF450yILtq-HltjuSc/edit#gid=0',
			'app'   => 'https://script.google.com/macros/s/AKfycbyvpTaK8WBKwYrHgpWkLs1uAsssdf5kffT2ofsjxeOqzWrSCQ/exec',
		],
		'install_slideshow'   => [
			'sheet' => 'https://docs.google.com/spreadsheets/d/1EZhUNNPx0gM-aYCYK-2biwVCv_HZT4xLj87iafqsFsM/edit#gid=0',
			'app'   => 'https://script.google.com/macros/s/AKfycbxPoqhQVBJQm8YDLGOfyCVddm2FEAgjRg_QEXAa7e3hfZVbiCsJ/exec',
		],
	];
	/**
	 * Output the estimate throughput form.
	 *
	 * @param string $name The name attribure of the form.
	 */
	public function output_form( $name ) : string {
		$form  = '';
		$form .= '<form>';
		$form .= '';
		$form .= '<button type="submit">Submit</button>';
		$form .= '</form>';
		return $form;
	}

	/**
	 * Output an input field.
	 *
	 * @param array $attributes The various attributes for the input.
	 */
	public function add_input( $attributes = [] ) {
		$input = '<input ';
		foreach ( $attributes as $key => $value ) {
			$input .= " $key='$value'";
		}

		$input .= '>';

		return $input;
	}

	/**
	 * Ouput installation photo slideshow form
	 */
	public function install_photo_slideshow_form() {
		return '
	<form name="installPhotosSlidesSubmit">
		<div class="field url-box">
			<input
				   inputmode="url"
				   tabindex="11"
				   type="text"
				   id="photoUrl"
				   default="https://nickmortensen.com"
				   name="photoUrl"
				   placeholder="Install Photo URL" />
			<label for="photoUrl">URL</label>
		</div>

		<div class="field client-box" >
			<input
				   tabindex="21"
				   type="text"
				   default="nickmortensen"
				   id="client"
				   name="client"
				   placeholder="Client Name" />
			<label for="client">Client</label>
		</div>

		<div class="field signtype-box">
			<input
				   tabindex="31"
				   type="text"
				   id="signtype"
				   name="signtype"
				   default="identity package"
				   placeholder="Sign, Service, or Sign Package Type" />
			<label for="signtype">Signtype</label>
		</div>

		<div class="field location-box">

			<input
				   tabindex="41"
				   type="text"
				   id="place"
				   name="place"
				   default="United States"
				   placeholder="City Project is Located" />
			<label for="place">Location</label>
		</div>

		<button
			tabindex="50"
			type="submit"
			id="submit-form"
			class="send_button">
			Submit
		</button>

	</form>';
	}

	/**
	 * Ouput estimate throughput form.
	 */
	public function estimate_throughput_form() {
		return '
	<form name="estimateThroughput">
		<input
			name="proposals"
			type="text"
			placeholder="total proposals" />
		<input
			name="turnaround"
			type="text"
			placeholder="turnaround time" />
		<button
			type="submit">
			Send
		</button>
	</form>';
	}

	/**
	 * Ouput personal finances form.
	 */
	public function personal_finances_form() {
		$fields  = [ 'kraken', 'coinbase', 'capital', 'nicolet', 'sia_value' ];
		$output  = '<div class="flex justify-center">';
		$output .= '<form class="flex justify-space-around">';
		foreach ( $fields as $field ) {
			$output .= '<label for="' . $field . '">' . ucfirst( $field ) . '</label>';
			$output .= '<input type="text" name="' . $field . '" />';
		}
		$output .= '<button
		type="submit">
		Send
	</button>
</form>';
		$output .= '</div>';
		return $output;
	}

	/**
	 * Ouput injury tracker form.
	 */
	public function injury_tracker_form() {
		return '
	<form class="horizontal-form" name="sinceTimeLossInjury">
		<input
			type="text"
			name="day"
			placeholder="day(number)"
			required />

		<!-- month dropdown -->
		<span>
			<select name="month">
				<option value="01">January</option>
				<option value="02">February</option>
				<option value="03">March</option>
				<option value="04">April</option>
				<option value="05">May</option>
				<option value="06">June</option>
				<option value="07">July</option>
				<option value="08">August</option>
				<option value="09">September</option>
				<option value="10">October</option>
				<option value="11">November</option>
				<option value="12">December</option>
			</select>
		</span> <!-- end month dropdown -->

		<input type="text" name="year" placeholder="year(number)" required>
		<!--<select name="year" required></select> -->
		<!--options are set in the javascript -->
		<input
			type="text"
			name="hour"
			placeholder="hour(number)" />

		<input
			type="text"
			name="minute"
			placeholder="minute(number)" />

		<input
			type="datetime-local"
			name="domstring"
			value="2020-06-01T08:30"
			hidden/>

		<button
			type="submit">
			Send
		</button>
	</form>';
	}


}
