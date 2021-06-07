<?php
/**
 * Template part for displaying the 6 most recent projects as cards.
 *
 * @package wp_rig
 *
 * @todo Last Update 2020-10-04
 */

namespace WP_Rig\WP_Rig;

[ 'requested_by' => $request_from ] = $args;
$project_ids = [ 40, 66, 64, 51, 53, 56, 59, 60, 20, 61, 62, 21 ];
?>


<?php wp_rig()->print_styles( 'wp-rig-flickity' ); ?>

<section id="fp-projects" class="packery_projects main_projects <?= $request_from; ?>">
<div class="section-title">
<h4>Projects</h4>
</div>

<div class="section-content packery-container">

<?php

foreach ( [ 40, 66, 64, 53 ] as $project ) {

	echo wp_rig()->get_packed_project( $project );
}

?>

</div>
</section>
<script>
const fpGridElement = document.querySelector( '.packery_projects > .section-content' );
var pckry = new Packery( fpGridElement, {
	itemSelector: '.single_p',
	rowHeight: 320,
	transitionDuration: '0.2s',
	stagger: 30,
} );
</script>

