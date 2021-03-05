<?php
/**
 * Template part for displaying an icon that floats on top of the page and keeps me abreat of what the width is.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<style>
.screenwidth-container {
	position: fixed;
	top: 92%;
	left: 93%;
	background: rgba(40,10,210,0.7);
	padding: 1.9vw;
	transform: translate(-100%, -100%);
	mix-blend-mode: multiply;
	border-radius: 50% 50% 0 0;
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 100px;
	transform-origin: center center;
	transform: rotateZ(-45deg);
	box-shadow: -3px -2px 4px var(--gray-900);
	}
#devScreenWidth {
	font-size: 22px;
	line-height: 0.9;
	letter-spacing: 1px;
	color: var(--foreground);
	transform-origin: center center;
	transform: rotateZ(45deg);
}

</style>

<div class="screenwidth-container">
	<span title="width" id="devScreenWidth" class=""></span>
</div>

<script>
	function reportWindowSize() {
		const content = `${window.innerWidth}`;
		devScreenWidth.textContent = content;
	}
	window.addEventListener( 'resize', reportWindowSize );
	window.addEventListener( 'DOMContentLoaded', reportWindowSize );

</script>
