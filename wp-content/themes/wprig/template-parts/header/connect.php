<?php
/**
 * Template part for displaying the custom header media
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/*
 * NOT ENTIRELY SURE.
if ( ! has_header_image() ) {
	return;
}
*/

?>

<style>

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


<!-- <div id="connect" class="throbbing">
	<span class="material-icons"> mail_outline</span>
</div> -->


<a href="#" class="material-icons floating-btn" anim="ripple">mail_outline</a>


<script>

"use strict";
[].map.call( document.querySelectorAll('[anim="ripple"]'), el => {
	el.addEventListener('click', e => {
		e       = e.touches ? e.touches[0] : e;
		const r = el.getBoundingClientRect();
		const d = Math.sqrt( Math.pow(r.width, 2) + Math.pow(r.height, 2) ) * 2;
		el.style.cssText = `--s: 0; --o: 1;`;
		el.offsetTop;
		el.style.cssText = `
			--t: 1;
			--o: 0;
			--d: ${d};
			--x:${e.clientX - r.left};
			--y:${e.clientY - r.top};
			`;
	});
});


</script>
