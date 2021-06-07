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
$action            = "esc_url( home_url( '/' ) )";
$types             = [ 'project', 'client', 'staffmember', 'inquiry' ];
?>
<form
name="sitesearch"
id="single-search-form"
	role="search"
	<?= $search_aria_label; ?>
	method="get"
	class="mono"
	>
	<input type="search" name="s" class="search_input" placeholder="What are you looking for?" id="<?= esc_attr( $search_unique_id ); ?>" value="<?= get_search_query(); ?>" />
	<input type="hidden" name="post_type[]" value="project">
	<input type="hidden" name="post_type[]" value="client">
	<input type="hidden" name="post_type[]" value="staffmember">
	<input type="hidden" name="post_type[]" value="inquiry">
	<input name="submit" id="searchformsubmit" class="searchbutton material-icons" type="submit" value="search" content="pageview">
	<div class="searchexplainer"></div>
</form>


