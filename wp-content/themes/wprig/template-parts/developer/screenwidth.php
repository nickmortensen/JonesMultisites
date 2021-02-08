<?php
/**
 * Template part for displaying an icon that floats on top of the page and keeps me abreat of what the width is.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<a title="width" id="devScreenWidth" draggable="true"></a>

<script>
	function reportWindowSize() {
		devScreenWidth.textContent = `${window.innerWidth}px`;
	}
	window.addEventListener( 'DOMContentLoaded', event => {
		devScreenWidth.textContent = window.innerWidth + 'px';
	})
	window.addEventListener( 'resize', reportWindowSize )
</script>
