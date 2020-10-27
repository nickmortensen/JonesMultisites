<?php
/**
 * Template part for displaying the footer widget area
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>


<div class="all_locations">
	<?= implode( "\r", wp_rig()->get_location_links() ); ?>
</div>

<?php
//get_sidebar();
