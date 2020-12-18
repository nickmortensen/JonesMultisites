<?php
/**
 * WP_Rig\WP_Rig\Contact Form\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\ContactForm;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function wp_enqueue_script;

/**
 * Class for adding in a contact form.
 *
 * Exposes template tags:
 * * `wp_rig()->return_contact_form_open_tag()`
 * * `wp_rig()->return_contact_form_closing_tag()`
 * * `wp_rig()->get_form_classes()`
 * * `wp_rig()->return_form_fields()`
 * * `wp_rig()->has_my_cpt_been_activated()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'contact_form';
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
			'get_contact_form'                  => [ $this, 'get_contact_form' ],
			'return_contact_form_open_tag'      => [ $this, 'return_contact_form_open_tag' ],
			'return_contact_form_closing_tag'   => [ $this, 'return_contact_form_closing_tag' ],
			'get_form_classes'                  => [ $this, 'get_form_classes' ],
			'return_field_details'              => [ $this, 'return_field_details' ],
			'has_my_cpt_been_activated'         => [ $this, 'has_my_cpt_been_activated' ],
			'return_contact_form_submit_button' => [ $this, 'return_contact_form_submit_button' ],
			'get_expected_fields'               => [ $this, 'get_expected_fields' ],
			'get_required_fields'               => [ $this, 'get_required_fields' ],
			'issue_warning'                     => [ $this, 'issue_warning' ],
			'retain_value'                      => [ $this, 'retain_value' ],
		];
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 * add_action( 'after_setup_theme', [ $this, 'create_contact_form' ] );
	 * add_action( 'wp_enqueue_scripts', [ $this, 'action_enqueue_secondary_contact_form_javascript' ] );
	 */
	public function initialize() {
		/**
		 * Comment this out.
		 * add_action( 'wp_enqueue_scripts', [ $this, 'get_contact_form_frontpage_script' ] );
		*/
	}

	/**
	 * Sanitize an inputs value so it can be reshown.
	 *
	 * @param string $value What the user has entered into the field.
	 */
	public function retain_value( $value ) {
		$value = stripslashes( esc_attr( $value ) );
		return ' value="' . $value . '"';
	}

	/**
	 * Output a warning span
	 *
	 * @param string $input Text of the 'Hint' or 'Error'.
	 */
	public function issue_warning( $input ) : string {
		return '<span class="warning">' . esc_html( $input ) . '</span>';
	}

	/**
	 * Post Type for the form.
	 *
	 * @var string Type of post that incoming forms should be considered.\
	 */
	public $post_type = 'inquiry';

	/**
	 * Form Classes
	 *
	 * @return string The css class for the form using tailwindcss.
	 */
	public function get_form_classes() : string {
		return 'contact';
	}
	/**
	 * Form Name
	 *
	 * @return string The css class for the form using tailwindcss.
	 */
	public function get_form_name() : string {
		return 'contact';
	}

	/**
	 * Get the class for the labels
	 *
	 * @return string the css classes for the label.
	 */
	public function get_label_class() : string {
		return 'contact';
	}

	/**
	 * Input Class
	 *
	 * @var string The css class for the form input fields.
	 */
	public $inputclass = 'contact';

	/**
	 * Input Class
	 *
	 * @var string form action
	 */
	protected function get_form_action() {
		return $_SERVER['PHP_SELF'];
	}

	/**
	 * ID of the form.
	 *
	 * @var string The id of the form.
	 */
	public $form_id = 'incoming-inquiry';

	/**
	 * Return all classes as a string
	 *
	 * @param array $classes Classes for the element.
	 * @return string the css classes
	 */
	private function get_classes_as_string( array $classes ) : string {
		return implode( ' ', $classes );
	}


	/**
	 * Determines whether the 'inquiries' post type is active and if it is, returns true.
	 *
	 * @return boolean
	 */
	public function has_my_cpt_been_activated() {
		if ( post_type_exists( $this->post_type ) ) {
			$output = site_url() . '<h3>Inquiry post Type exists</h3>';
		} else {
			$output = '<h3>Inquiry post type does not exist</h3>';
		}
		return $output;
	}



	/**
	 * Get the html attributes for the html '<input>' button.
	 */
	public function get_input_button_attributes() : array {
		$attributes = [
			'form'  => 'contact',
			'class' => 'button_hide btn',
			'type'  => 'submit',
			'name'  => 'submit',
			'id'    => 'contact_form_button',
			'value' => 'Submit',
		];
		return $attributes;
	}

	/**
	 * Return contact form button with attributes.
	 *
	 * @param array $overwrites The attributes that go insude the '<input>' tag.
	 */
	public function return_contact_form_submit_button( $overwrites = [] ) : string {
		$output     = '<input';
		$attributes = wp_parse_args( $overwrites, $this->get_input_button_attributes() );
		foreach ( $attributes as $attribute => $value ) {
			$output .= "\n";
			$output .= ' ' . $attribute . '="' . $value . '"';
			$output .= "\r";
		}
		$output .= '>';
		$output .= '</input> ';
		return $output;
	}


	/**
	 * Return opening html tag for the <form action="
	 *
	 * @param array $overwrites Details about the form field.
	 *
	 * @return string opening html tag for form.
	 */
	public function return_contact_form_open_tag( $overwrites = [] ) : string {
		$defaults = [
			'action' => $this->get_form_action(),
			'method' => 'POST',
			'class'  => $this->get_form_classes() ?? 'contact-form',
			'name'   => $this->get_form_name() ?? 'contact_us',
		];

		$attributes = wp_parse_args( $overwrites, $defaults );
		$form_open  = <<<FORMOPEN
		<form
		id="{$attributes['name']}"
		name="{$attributes['name']}"
		method="{$attributes['method']}"
		class="{$attributes['class']}"
		action="{$attributes['action']}"
		>
FORMOPEN;
		return $form_open;
	}

	/**
	 * Return closing html tag for the form
	 *
	 * @return string closing html tag for form.
	 */
	public function return_contact_form_closing_tag() : string {
		return '</form>';
	}

	/**
	 * Get entire contact form.
	 */
	public function get_contact_form() {
		$field_attributes = $this->return_field_details();
		$total            = count( $field_attributes );
		$output           = $this->return_contact_form_open_tag();

		for ( $i = 0; $i < $total; $i++ ) {
			$field_type = $field_attributes[ $i ]['type'];
			switch ( $field_type ) {
				case 'textarea':
					$output .= $this->get_growing_text_field( $field_attributes[ $i ] );
					break;
				default:
					$output .= $this->get_text_field( $field_attributes[ $i ] );
			}
		}
		$output .= $this->return_contact_form_closing_tag();
		$output .= $this->return_contact_form_submit_button( [ 'form' => 'contact' ] );
		return $output;
	}

	/**
	 * Returns the form fields and attributes as an array.
	 *
	 * @return array $form_fields The form fields and attributes.
	 */
	public function return_field_details() : array {
		$field_names = [
			[
				'class'        => $this->inputclass,
				'error'        => 'Enter First &amp; Last Name',
				'hint'         => 'Enter Your Full Name Here',
				'id'           => '',
				'label'        => 'Name',
				'mode'         => 'text',
				'name'         => 'fullname',
				'req'          => true,
				'type'         => 'text',
				'value'        => '',
				'autocomplete' => 'name',
			],
			[
				'class'        => $this->inputclass,
				'error'        => 'Enter Your Company Name',
				'hint'         => 'What Company are You With?',
				'id'           => '',
				'label'        => 'Company',
				'mode'         => 'text',
				'name'         => 'company',
				'req'          => true,
				'type'         => 'text',
				'value'        => '',
				'autocomplete' => 'organization',

			],
			[
				'class'        => $this->inputclass,
				'error'        => '',
				'hint'         => 'Enter Job Title Here',
				'id'           => '',
				'label'        => 'Title',
				'mode'         => 'text',
				'name'         => 'jobtitle',
				'req'          => false,
				'type'         => 'text',
				'value'        => '',
				'autocomplete' => 'organization-title',
			],
			[
				'class'        => $this->inputclass,
				'error'        => 'Enter Your Email Address',
				'hint'         => 'Enter Your Email Address',
				'id'           => '',
				'label'        => 'email',
				'mode'         => 'email',
				'name'         => 'email',
				'req'          => true,
				'type'         => 'email',
				'value'        => '',
				'autocomplete' => 'email',
			],
			[
				'div_class'    => 'grow-wrap',
				'class'        => $this->inputclass,
				'error'        => 'Please add your Message',
				'hint'         => 'Add Your Message Here',
				'id'           => '',
				'label'        => 'message',
				'mode'         => 'text',
				'name'         => 'message',
				'req'          => true,
				'type'         => 'textarea',
				'value'        => '',
				'on_input'     => 'this.parentNode.dataset.replicatedValue = this.value',
				'autocomplete' => '',
			],
			[
				'class'        => $this->inputclass,
				'error'        => 'Add these two numbers so we know you aren not a spambot',
				'hint'         => 'Add These Two Numbers',
				'id'           => 'addthese',
				'label'        => 'Security',
				'mode'         => 'text',
				'name'         => 'addthese',
				'req'          => true,
				'type'         => 'text',
				'value'        => '',
				'autocomplete' => '',
			],
		];
		return $field_names;
	}


	/**
	 * Output a text field based on an arrayof information.
	 *
	 * @param array $overwrites Details about the form field.
	 */
	public function get_text_field( $overwrites = [] ) {
		$defaults   = [
			'type'     => 'text',
			'on_input' => '',
		];
		$attributes = wp_parse_args( $overwrites, $defaults );
		$div_class  = array_key_exists( 'div_class', $attributes ) ? 'grow-wrap' : 'input_field_container';
		$required   = $attributes['req'] ? 'required' : '';
		$on_input   = array_key_exists( 'on_input', $attributes ) && '' !== $attributes['on_input'] ? 'onInput="{$attributes[\'on_input\']}"' : '';
		$output     = <<<FORMFIELD
<div class="$div_class">
<label
title="{$attributes['hint']}"
for="{$attributes['name']}"
>
{$attributes['label']}
</label>
<input
name="{$attributes['name']}"
id="{$attributes['name']}"
type="{$attributes['type']}"
class="{$attributes['class']}"
autocomplete="{$attributes['autocomplete']}"
$required />
</div>
FORMFIELD;
return $output;
	}

	/**
	 * Get the attributes for the 'message' textarea field that is replaced by a text field that grows.
	 *
	 * @param array $overwrites Details about the form field.
	 */
	public function get_growing_text_field( $overwrites = [] ) : string {
		$defaults = [
			'div_class' => 'grow-wrap',
			'class'     => $this->inputclass,
			'error'     => 'Please add your Message',
			'hint'      => 'Add Your Message Here',
			'id'        => '',
			'label'     => 'Message',
			'hint'      => 'What is Your Message?',
			'mode'      => 'text',
			'name'      => 'message',
			'req'       => true,
			'type'      => 'textarea',
			'value'     => '',
			'on_input'  => 'this.parentNode.dataset.replicatedValue = this.value',
		];
		$form     = wp_parse_args( $overwrites, $defaults );

		$field_html = <<<TEXTAREA
<div class="input_field_container">
		<label for="{$form['name']}" > {$form['label']} </label>
<div class="{$form['div_class']}">
	<textarea name="{$form['name']}" id="{$form['name']}" onInput="{$form['on_input']}">Enter Message Here</textarea>
</div>
</div>

TEXTAREA;
	return $field_html;
	}

	/**
	 * Get expected fields from the contact form.
	 */
	public function get_expected_fields() : array {
		$output = [];
		$fields = $this->return_field_details();
		foreach ( $fields as $field => $attribute ) {
			$output[] = $attribute['name'];
		}
		return $output;
	}

	/**
	 * Get required fields from the contact form.
	 */
	public function get_required_fields() : array {
		$output = [];
		$fields = $this->return_field_details();
		foreach ( $fields as $field => $attribute ) {
			if ( true === $attribute['req'] ) {
				$output[] = $attribute['name'];
			}
		}
		return $output;
	}

	/**
	 * Enqueue javascript for the frontpage contact form.
	 */
	public function get_contact_form_frontpage_script() {
		$handle       = 'contact-form';
		$src          = 'development' === ENVIRONMENT ? get_theme_file_uri( '/assets/js/src/contact_form.js' ) : get_theme_file_uri( '/assets/js/contact_form.min.js' );
		$path         = 'development' === ENVIRONMENT ? get_theme_file_path( '/assets/js/src/contact_form.js' ) : get_theme_file_path( '/assets/js/contact_form.min.js' );
		$version      = wp_rig()->get_asset_version( $path );
		$dependencies = [];
		$in_footer    = true;
		// Only get this javascript if it is the frontpage -- can and maybe should alter this to beging is_home() as well.
		if ( is_front_page() ) {
			wp_enqueue_script( $handle, $src, $dependencies, $version, $in_footer );
		}
	}
}
