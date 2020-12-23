<?php
/**
 * Template part for displaying the header searchbar
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( ! wp_rig()->is_primary_nav_menu_active() ) {
	return;
}

?>

	<div class="searchbar"> <?php get_search_form(); ?> </div><!-- end div.searchbar -->


