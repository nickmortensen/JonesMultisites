<?php
/**
 * Template part for displaying the custom header media
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/*
if ( ! has_header_image() ) {
	return;
}
*/

?>

<style>

:root {
	--mdc-ripple-fg-size: 33px;
	--mdc-ripple-fg-scale: 2.70290786342101;
	--mdc-ripple-fg-translate-start: 19.5px, 14.5px;
	--mdc-ripple-fg-translate-end: 11.5px, 11.5px;
}

.connect {
	padding-right: 5vw;
	grid-area: connect;
	display: flex;
	flex-flow: row nowrap;
	justify-content: flex-end;
	align-items: center;
	width: 100%;

}

.connect a[class^="material-icons"] {
	font-size: clamp(28px, 16vmin, 60px);
	color: #fff;

}

</style>


<div class="connect">
	<h3 class="light-text">connect</h3>
	<a id="open-contact-form" class="material-icons-sharp"> chat_bubble_outline </a>
</div>
