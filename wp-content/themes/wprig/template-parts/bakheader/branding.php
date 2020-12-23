<?php
/**
 * Template part for displaying the header branding
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>


<?php
$icon = wp_rig()->get_jones_icon();
$logo = wp_rig()->get_jones_logo();

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






	</a>
<!--
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
	</div> -->

</div><!-- .site-branding -->
