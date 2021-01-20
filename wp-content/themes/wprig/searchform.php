<?php
/**
 * The searchform.php template.
 *
 * Used any time that get_search_form() is called.
 *
 * @link https://developer.wordpress.org/reference/functions/wp_unique_id/
 * @link https://developer.wordpress.org/reference/functions/get_search_form/
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/*
 * Generate a unique ID for each form and a string containing an aria-label
 * if one was passed to get_search_form() in the args array.
 */
$search_unique_id  = wp_unique_id( 'jones-search-form-' );
$search_aria_label = ! empty ( $args['aria-label'] ) ? 'aria-label="' . esc_attr( $args['aria_label'] ) . '"' : '';

?>
<form
	role="search"
	<?= $search_aria_label; ?>
	method="get"
	action="<?= esc_url( home_url( '/' ) ); ?>"
	>
	<input type="checkbox" id="trigger" class="checkbox" />
	<label class="label-initial" title="click here to open a search form" for="trigger"></label>
	<label class="label-active" title="hit 'enter' to search or click the 'x' to close" for="trigger"></label>
	<div class="border"></div>
	<input
	class="input"
	type="search"
	id="<?= esc_attr( $search_unique_id ); ?>"
	value="<?= get_search_query(); ?>" name="s"
	/>
	<div class="close"></div>
</form>
