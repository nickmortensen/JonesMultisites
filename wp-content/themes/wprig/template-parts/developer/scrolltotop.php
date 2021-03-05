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

<!-- <a title="scroll to top" id="scrollToTopIcon" class="material-icons button">search</a> -->

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

<!-- only show this button in the developer environment -->
<?php if ( 'development' === ENVIRONMENT ) : ?>

<a title="overlay information" id="revealIdentifiers" class="material-icons floating-btn">offline_bolt</a>

<script>

const revealGridIndentifiersButton = document.querySelector('#revealIdentifiers');

function revealGridInfo() {
	const dataIdentifiers = Array.from(document.querySelectorAll('[data-idr]')).slice(1);
	dataIdentifiers.forEach( area => {
		area.classList.toggle('showTheDetails');
	});
}

revealGridIndentifiersButton.addEventListener( 'click', revealGridInfo, true);


function testSupportsSmoothScroll() {
	var supports = false
	try {
		var div = document.createElement('div')
		div.scrollTo({
		top: 0,
		get behavior () {
			supports = true
			return 'smooth'
		}
		})
	} catch (err) {}
	return supports
}
console.log( 'supports smooth scroll', testSupportsSmoothScroll());
</script>

<?php endif; ?>
