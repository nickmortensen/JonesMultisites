<?php
/**
 * Template part for displaying the footer widget area
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<style>
	section.skin_chooser {
		background: var(--pink-600);
		min-height: 440px;
		display: none;
		grid-template-columns: 1fr;
		place-items: center center;
		grid-column: 1 / -1;
	}
</style>
<section class="skin_chooser hide_location"> <select name="skin_choice_selector" id="skin_choice_selector"></select> </section>

<script>
	// const locationSelect = document.querySelector('#location'); // Whole Location Select


	// remove 'overlay' & 'circular' options -- take a longer look at that boxes option.
	const skinChoices = [ 'border', 'underline', 'elastic', 'slide', 'overlay', 'rotate', 'boxes' ]; // Classes to apply to locationSelect.
	const skinChoiceSelector = document.querySelector('#skin_choice_selector');
	let skinOptions = [];
	skinChoices.forEach( option => {
		let singleOption = `<option value="cs-skin-${option}">${option.toUpperCase()}</option>`;
		skinOptions.push( singleOption );
	})
	skinChoiceSelector.innerHTML = skinOptions.join('');
	skinChoiceSelector.addEventListener( 'change', function() {
		let replaceThisClass = Array.from(document.querySelector('.cs-select').classList)[1];
		let newClass = this.options[this.options.selectedIndex].value;
		let locationSelect = document.querySelector('.cs-select');
		console.log( `Replace this class: ${replaceThisClass} with this new class: ${newClass}` );
		locationSelect.classList.replace(replaceThisClass, newClass);
	// initialClass = newClass;
	} );

</script>
