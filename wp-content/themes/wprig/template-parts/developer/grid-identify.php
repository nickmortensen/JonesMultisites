<?php
/**
 * Template part for displaying an icon that floats on top of the page and keeps me abreat of what the width is.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<a title="show or hide overlay information" id="revealIdentifiers" class="material-icons floating-btn">developer_board</a>

<script>

const revealGridIndentifiersButton = document.querySelector('#revealIdentifiers');

function revealGridInfo() {
	const dataIdentifiers = Array.from(document.querySelectorAll('[data-gridarea]')).slice(1);
	dataIdentifiers.forEach( area => {
		area.classList.toggle('showTheDetails');
		// setTimeout( () => area.classList.contains('showTheDetails') && area.classList.add( 'fadeDetailsIn' ), 200 );
	});
}

revealGridIndentifiersButton.addEventListener( 'click', revealGridInfo, true);

</script>

