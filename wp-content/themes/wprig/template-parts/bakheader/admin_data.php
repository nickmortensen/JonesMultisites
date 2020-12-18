<?php
/**
 * Template part for displaying stuff that only the site administrator would want to see.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$variable = 'show the variable.';


?>
<style>

a.topmost {
	margin-top: 1.5vw;
	font-size: clamp(44px, 4vw, 88px);
	transition: transform 0.7s ease;

}

.rotate-upward {
	transform: rotateZ(90deg);
	transition: transform 0.7s linear;
}

.hidden { display: none; }

.opened {
	padding: 1vw;
	min-height: 50vh;
	background: var(--red-200);
	height: 540px;
	visibility: visible;
	opacity: 1;
	transition: visibility 0s linear 0s, opacity 300ms;
}
</style>
<section id="frontend_administrator">

	<!-- <a href="" class="topmost material-icons"> arrow_downward </a> -->

</section>



