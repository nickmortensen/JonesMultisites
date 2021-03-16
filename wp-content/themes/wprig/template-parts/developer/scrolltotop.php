<?php
/**
 * Template part for displaying an icon that floats on top of the page and keeps me abreat of what the width is.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<style>

/* #scrollToTopBtn {
	position: fixed;
	bottom: 1%;
	right: 8%;
	transition: all 0.3s ease;
	} */


</style>


<a title="scroll to top of page" id="scrollToTopBtn" class="material-icons floating-btn">upgrade</a>



<script>

const scrollToTopBtn = document.querySelector( '#scrollToTopBtn' );
function scrollToTop() {
	window.scrollTo({
		top: 100,
		left: 100,
		behavior: 'smooth'
	});
}
scrollToTopBtn.addEventListener( 'click', scrollToTop, true );

</script>
