<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>


<section id="modal-messaround">


<div class="body-blackout"></div>

<button
	data-popup-trigger="one"
	id="add-to-favorites"
	class="mdc-icon-button shadow popup-trigger"
	aria-label="Add to favorites"
	aria-pressed="false">
	<i class="material-icons mdc-icon-button__icon mdc-icon-button__icon--on">favorite</i>
	<i class="material-icons mdc-icon-button__icon">favorite_border</i>
</button>

<button
	type="button"
	class="btn btn-sm btn-primary  p-2 px-3 shadow popup-trigger"
	data-popup-trigger="one">
	Popup Trigger One
</button>
<button
	type="button"
	class="btn btn-sm btn-primary shadow p-2 px-3 popup-trigger"
	data-popup-trigger="two">
	Popup Trigger Two
</button>

<div
	class="popup-modal shadow"
	data-popup-modal="one">
	<span class="large-text material-icons popup-modal__close"> cancel </span>
	<h1 class="font-weight-bold"> Modal One Title </h1>
</div>
<div
	class="popup-modal shadow"
	data-popup-modal="two">
		<span class="large-text material-icons popup-modal__close"> cancel </span>
		<h1 class="font-weight-bold"> Modal Two TITLE </h1>
</div>


</section>

<script>

const modalTriggers     = document.querySelectorAll('.popup-trigger')
const modalCloseTrigger = document.querySelector('.popup-modal__close')
const bodyBlackout      = document.querySelector('.body-blackout')

modalTriggers.forEach(trigger => {
	trigger.addEventListener('click', () => {
		const { popupTrigger } = trigger.dataset
		const popupModal = document.querySelector(`[data-popup-modal="${popupTrigger}"]`)

		popupModal.classList.add('is--visible')
		bodyBlackout.classList.add('is-blacked-out')

		popupModal.querySelector('.popup-modal__close').addEventListener('click', () => {
			popupModal.classList.remove('is--visible')
			bodyBlackout.classList.remove('is-blacked-out')
		})

		bodyBlackout.addEventListener('click', () => {
			// TODO: Turn into a function to close modal
			popupModal.classList.remove('is--visible')
			bodyBlackout.classList.remove('is-blacked-out')
		})
	})
})

</script>
