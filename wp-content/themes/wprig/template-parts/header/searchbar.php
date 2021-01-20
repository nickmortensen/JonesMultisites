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
<style>
.searchbar {
	grid-area: search;
	background-color: var(--blue-400);
	opacity: 0.75;
	mix-blend-mode: luminosity;
}
</style>
	<span class="searchbar"> <?php get_search_form(); ?> </span><!-- end div.searchbar -->


