<?php
/**
 * Template part for displaying the 6 most recent projects as cards.
 * Last Update 29_April_2021.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;


[ 'requested_by' => $request_from ] = $args;
?>


<?php wp_rig()->print_styles( 'wp-rig-flickity', 'wp-rig-project' ); ?>
	<!-- project cards -->
	<section id="fp-projects" class="frontpage-projects hide-flex-hide <?= $request_from; ?>">

	<div class="section-title">
		<h4>Recent Projects</h4>
	</div>

		<!-- graphic to demonstrate touch drag availability -->
		<?= wp_rig()->get_drag_svg(); ?>

		<div id="frontpage-draggable-slide-container" style="width: 100%;">
			<?php
				$project_ids = wp_rig()->get_recent_project_ids( 8 );

				foreach ( $project_ids as $project ) {
					echo wp_rig()->get_project_card( $project );
				}
			?>
		</div><!-- end div#frontpage-draggable-slide-container -->


	</section><!-- end section#fp-projects .frontpage-projects-->
	<!-- flickty slider javascript instantiation -->
	<script>
		const fpProject = document.querySelector( '#frontpage-draggable-slide-container' );
		const optional = {
			cellAlign: 'center',
			groupCells: true,
			contain: true,
			freeScroll: true,
			wrapAround: true,
			adaptiveHeight: false,
			arrowShape: 'M 0,50 L 60,00 L 50,30 L 80,30 L 80,70 L 50,70 L 60,100 Z',
		};
		const flickity = new Flickity( fpProject, optional );
	</script> <!-- END flickty slider javascript instantiation -->
