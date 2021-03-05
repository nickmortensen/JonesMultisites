<?php
/**
 * Template part for displaying an icon that floats on top of the page and keeps me abreat of what the width is.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$options    = wp_rig()->get_blend_modes();
$label      = 'Choose Blend Mode';
$identifier = 'blend-modes';

$blend_select = wp_rig()->select_html( $identifier, $label, $options );
$color_select = wp_rig()->select_html( $identifier = 'color-options', $label = 'Choose a Color', array_flip( wp_rig()->color_options() ) );
?>


<style>
.dev_selects_container {
	min-height: 240px;
	width: 100%;
	box-shadow: var(--section-shadow);
	display: flex;
	flex-flow: row wrap;
	justify-content: space-around;
	align-items: center;
	align-content: start;
}

.dev_selects_container label {
	margin-right: 2vw;
}

.dev_selects_container select {
	width: 100%;
}

.dev_select_container {
	display: flex;
	flex-flow: column nowrap;
	justify-content: space-between;
	align-items: stretch;
	align-content: flex-end;
	border: 1px solid var(--gray-400);

}
</style>

<div class="dev_selects_container">
	<div class="dev_select_container"><?= $blend_select; ?></div>
	<div class="dev_select_container"><?= $color_select; ?></div>
</div>

<script>
	const blendSelect = document.querySelector( 'select#<?= $identifier; ?>' );
	blendSelect.addEventListener( 'change', function( e ) {
		let blendMode = e.target.value;
		console.log( 'project header background blend mode is now', blendMode );
		document.querySelector( '.project_initial_image' ).style.backgroundBlendMode = blendMode;
		// document.querySelector( '#vertical' ).style.backgroundBlendMode = blendMode;
	} );

</script>



