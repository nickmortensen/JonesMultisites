<?php
/**
 * Template part for displaying the 6 most recent projects as cards.
 *
 * @package wp_rig
 *
 * @todo Last Update 2020-10-04
 */

namespace WP_Rig\WP_Rig;

?>

<!-- 6 project cards -->
<section id="fp-projects" class="frontpage">
	<h2 class="section-title">Recent Projects</h2>
	<div id="fp-project-cards">
		<?php
			$project_ids = wp_rig()->get_recent_project_ids();
			foreach ( $project_ids as $project ) {
				echo wp_rig()->get_project_card( $project );
			}
		?>
	</div><!-- end div#fp-project-cards -->
</section>
