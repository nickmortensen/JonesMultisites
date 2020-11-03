<?php
/**
 * Template part for displaying the header branding
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<style>

	.site-branding {
		max-width: 20vw;
	}
#jones_logo,
#symbol_j {
	fill: var(--color-theme-white);
}

#symbol_circle,
.logo_circle_fill {
	fill: var(--gray-300);
}

#jones_symbol {
	display: none;
}

</style>

<div class="site-branding">
	<a
	class="jonessign_logo_link"
	title="jonessign.com home"
	href="<?=esc_url( home_url( '/' ) ); ?>"
	rel="home">


		<svg
		version="1.1"
		id="jones_symbol"
		xmlns="http://www.w3.org/2000/svg"
		xmlns:xlink="http://www.w3.org/1999/xlink"
		x="0px"
		y="0px"
		width="40%"
		viewBox="0 0 500 500"
		style="enable-background:new 0 0 500 500;"
		xml:space="preserve">
			<circle id="symbol_circle" class="logo_circle_fill" cx="251" cy="250" r="240"/>
			<path id="symbol_j" class="logo_letter_fill" d="M187.3,328.6h103.8V107H364v215.6c0,17.9-2.1,42-15.2,55.1c-13.1,13.4-37.6,15.3-54.9,15.3H184.1 c-17.3,0-41.9-1.9-55.1-15.3c-13-13.2-15.1-37.3-15.1-55.1v-59.2h73.3V328.6z"/>
		</svg>



		<!-- Jones Sign Text In Bank Gothic font as an SVG rather than including a call to a font -->

		<svg
		version="1.1"
		id="jones_logo"
		xmlns="http://www.w3.org/2000/svg"
		xmlns:xlink="http://www.w3.org/1999/xlink"
		x="0px"
		y="0px"
		width="100%"
		viewBox="0 0 490 52.8"
		style="enable-background:new 0 0 490 52.8;"
		xml:space="preserve">
			<path class="logo_letter_fill" id="n-2" d="M479,35.2V9.3l10.6,0l0.3,43.5h-8.2l-31.1-26v26h-11V9.6h8.1L479,35.2z"/>
			<path class="logo_letter_fill" id="n-1" d="M153.4,35.2V9.3h11v43.5h-8.2l-31.1-26v26h-11V9.6h8.1L153.4,35.2z"/>
			<path class="logo_letter_fill" id="g" d="M420.1,19.6h-27v22.8h27v-6.1l-11.9,0l-0.1-9.8h23.7v15.4c0,2.8-0.4,6.5-2.5,8.5c-2.1,2.1-6.1,2.4-8.8,2.4h-28 c-2.8,0-6.7-0.3-8.8-2.4c-2.1-2-2.5-5.7-2.5-8.5V20.4c0-2.8,0.4-6.5,2.5-8.5c2.1-2.1,6.1-2.4,8.8-2.4h28c2.7,0,6.6,0.3,8.8,2.3 c2.1,1.9,2.5,5.3,2.5,8.1l0.2,2.8l-11.8-0.1L420.1,19.6z"/>
			<path class="logo_letter_fill" id="i" d="M361.4,52.8V9.3h11.8v43.5H361.4z"/>
			<path class="logo_letter_fill" id="s-2" d="M340.6,11.9h-28.5V20h28.2c3.2,0,7.8,0.4,10.2,2.8c2.4,2.5,2.8,6.8,2.8,10.2v6.8c0,3.3-0.4,7.7-2.8,10.2 c-2.4,2.5-7,2.8-10.2,2.8h-29.2c-3.2,0-7.8-0.4-10.2-2.8c-2.4-2.5-2.8-6.8-2.8-10.2V38l12.1,0v2.9h31v-8.8H313 c-3.2,0-7.7-0.4-10.2-2.8c-2.4-2.5-2.8-6.9-2.8-10.2v-5.5c0-3.3,0.4-7.7,2.8-10.2c2.4-2.5,7-2.8,10.2-2.8h26.8c3.2,0,7.5,0.4,10,2.7 c2.4,2.3,2.9,6.2,2.9,9.5l0,4.5l-12.1,0L340.6,11.9L340.6,11.9L340.6,11.9z"/>
			<path class="logo_letter_fill" id="s-1" d="M231.7,42.6h26.1v-6.7h-23.9c-2.8,0-6.7-0.3-8.9-2.4c-2.1-2-2.5-5.7-2.5-8.5v-4.5c0-2.8,0.4-6.4,2.5-8.5 c2.2-2.1,6.1-2.4,8.9-2.4h23c2.8,0,6.6,0.3,8.8,2.3c2.1,1.9,2.6,5.1,2.6,7.8v1.9l-10.6,0v-2.2h-24.2v6h24c2.7,0,6.7,0.3,8.8,2.3 c2.1,2,2.4,5.7,2.4,8.5v5.6c0,2.8-0.4,6.5-2.5,8.5c-2.1,2.1-6,2.4-8.8,2.4h-25.1c-2.8,0-6.8-0.3-8.9-2.3c-2.1-2-2.5-5.7-2.5-8.6 v-2.2l10.8,0L231.7,42.6L231.7,42.6L231.7,42.6z"/>
			<path class="logo_letter_fill" id="e" d="M184.3,19.3v6.3H203v9.5h-18.7v7.4h32.5v10.2h-44.5V9.6h44v9.7H184.3z"/>
			<path class="logo_letter_fill" id="o" d="M53.9,20.4c0-2.8,0.4-6.5,2.4-8.5c2.1-2.1,6.1-2.4,8.8-2.4h30c2.7,0,6.7,0.3,8.9,2.4c2.1,2,2.4,5.7,2.4,8.5v21.4 c0,2.8-0.4,6.5-2.5,8.5c-2.1,2.1-6.1,2.4-8.8,2.4h-30c-2.8,0-6.7-0.3-8.8-2.4c-2.1-2-2.4-5.7-2.4-8.5L53.9,20.4L53.9,20.4z M65.8,42.4h28.8V19.6H65.8C65.8,19.6,65.8,42.4,65.8,42.4z"/>
			<path class="logo_letter_fill" id="j" d="M13.5,40.9h19.2V0h13.4v39.8c0,3.3-0.4,7.7-2.8,10.2c-2.4,2.5-6.9,2.8-10.1,2.8H12.9c-3.2,0-7.7-0.3-10.2-2.8 C0.4,47.5,0,43.1,0,39.8V28.9h13.5V40.9z"/>
		</svg>

	</a>

	<div class="toggle_menu_div cross menu--1">
	<label>
		<input type="checkbox">
		<svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
			<circle class="menu_toggle_background" cx="50" cy="50" r="30" />
			<path class="line--1" d="M0 40h62c13 0 6 28-4 18L35 35" />
			<path class="line--2" d="M0 50h70" />
			<path class="line--3" d="M0 60h62c13 0 6-28-4-18L35 65" />
		</svg>
	</label>
</div>

</div><!-- .site-branding -->
