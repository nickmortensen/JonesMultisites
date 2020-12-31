<?php
/**
 * WP_Rig\WP_Rig\Search\Component class
 *
 * @package wp_rig
 */

/** TO DO FINISH - LAST UPDATE 21_December_2020 */
namespace WP_Rig\WP_Rig\Search;

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
	private $slug = 'search';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return $this->slug;
	}

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_search_symbol() : string {
		return '&#9906;';
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
			'alt_search_html' => [ $this, 'alt_search_html' ],
		];
	}

	/**
	 * Use an array to create html attributes with values.
	 *
	 * @param array $array The attributes array.
	 */
	public function create_html_attributes( $array = [] ) {
		$output = ' ';
		foreach ( $array as $attribute => $value ) {
			$output .= $attribute . '="' . $value . '" ';
		}
		return $output;
	}

	/**
	 * Create the Seach Field for the site.
	 *
	 * @returns string The HTML for the search field.
	 */
	public function create_search_form() {
		$form_attributes = [
			'name'   => 'jonessign_site_search',
			'id'     => 'jonessign_site_search',
			'role'   => 'search',
			'method' => 'get',
			'class'  => 'search-form',
			'action' => esc_url( home_url( '/' ) ),
		];
		$output          = '<form ';
		$output         .= $this->create_html_attributes( $form_attributes );
		$output         .= '>';
		$output         .= '</form>';

		return $output;
	}

	/**
	 * Search input surrounding HTML with input.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_search_form/
	 * @link https://webdevstudios.com/2015/09/01/search-everything-within-custom-post-types/
	 */
	public function output_search_html() {
		$args   = [
			'echo'       => false,
			'aria-label' => 'Jones Sign Website Search', // False first parameter returns 'True' echoes the field..
		];
		$output = get_search_form( $args );
		return $output;
	}

	/**
	 * Search input surrounding HTML with input.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_search_form/
	 * @link https://webdevstudios.com/2015/09/01/search-everything-within-custom-post-types/
	 */
	public function alt_search_html() {
		$args   = [
			'echo'       => false,
			'aria-label' => 'Jones Sign Website Search', // False first parameter returns 'True' echoes the field..
		];
		$output = <<<SEARCH
<div class="search_wrap">
	<div class="search" role="search">
		<input
		type="search"
		class="search_term"
		placeholder="Enter search term"
		aria-label="Search"
	/>
		<button type="submit" class="search_button">
			<i class="search-icon">&#9906;</i>
		</button>
	</div><!-- end .search-->
</div><!-- end .search_wrap-->
SEARCH;
		return $output;
	}

}

