<?php
/**
 * Icons to use as a topnav and eventually a sidenav on screens over 1400px;
 *
 * @updated 04_May_2021
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$titletext = 'Go to the Jones Sign Company Homepage';
if ( is_home() ) {
	$titletext = 'You are on the Jones Sign Homepage';
}
?>

	<!-- Company logo outline links to home -->
	<div data-opens="homepage" id="svgIconContainer" class="nav-icon-container">
		<a class="sidebar nav_branding" href="<?= esc_url( home_url() ); ?>" title="<?= $titletext; ?>">
			<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="pylon_circle" class="jones_icon" x="0px" y="0px" viewBox="0 0 500 500" style="enable-background:new 0 0 500 500;" xml:space="preserve">
				<circle cx="262.9" cy="242.5" r="215.3"/>
				<path d="M430.4,225.9H248.7c-8.1,0-14.8-6.6-14.8-14.8v-18h-18.6c-3.3,8.7-11.9,15.6-22.2,15.9L89.8,370.5 c-4.3-5.9-8.4-12-12.1-18.3l98.1-150c-4.8-4.5-7.8-10.6-7.8-17.7c0-13.5,10.9-24.3,24.5-24.3c11.7,0,21.5,7.8,23.9,19.3H234v-24.8 c0-8.1,6.6-14.8,14.8-14.8h181.6c8.1,0,14.8,6.6,14.8,14.8v56.3C445.1,219.3,438.5,225.9,430.4,225.9z"/>
			</svg>
		</a>
	</div><!-- end div#svgiconcontainer -->

	<!-- Three line icon toggle for the sidebar -->
	<div
	data-opens="sidebar"
	id="sidebarToggleContainer"
	class="nav-icon-container open_close_sidebar"
	title="open/close sidebar menu">


		<div>
			<span></span>
			<span></span>
			<span></span>
		</div>
	</div><!-- end div#sidebartogglecontainer -->

	<!-- Open Space -->
	<div id="open_space" class="nav-icon-container"></div><!-- end div#open_space -->

	<!-- show the search panel -->
	<div data-opens="search" id="searchToggleContainer" class="nav-icon-container">
		<a
			id="searchToggle"
			class="material-icons nav_search"
			title="Search jonessign.com"
			data-iconname="search"
			type="button">search</a>
	</div><!-- end div#searchtogglecontainer -->

	<!-- CONTACT (MAIL ICON) -->
	<div data-opens="sidebar-contact" id="mailIconContainer" class="nav-icon-container">
		<a
		id="contactUsToggle"
		class="material-icons nav_contact"
		title="Contact Us"
		data-iconname="mail_outline"
		type="button">mail_outline</a>
	</div><!-- end div#mailIconContainer -->

	<!-- More - (NO IDEA WHAT THAT ENTAILS) -->
	<div data-opens="sidebar-general" id="moreToggleContainer" class="nav-icon-container">
		<a
		class="material-icons nav_more"
		title="Search jonessign.com"
		data-iconname="more_vert"
		type="button">more_vert</a>
	</div><!-- end div#moretogglecontainer -->
</nav>


