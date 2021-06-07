<?php
/**
 * Template part for displaying the social media links
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<nav class="social_media">
	<div class="social-media-icon-container">
		<!-- LINKEDIN ICON & LINK -->
		<a href="<?= LINKEDIN_URL; ?>" title="Find Jones Sign Company on linkedin" class="social-media-icon-link">
			<svg
			class="social-media-icon"
			id="linkedin-icon"
			data-name="Layer1"
			xmlns="http://www.w3.org/2000/svg"
			viewBox="0 0 500 500"
			style="enable-background:new 0 0 500 500;"
			>
				<circle class="svg_icon_background" cx="250" cy="250" r="240"/>
				<g>
					<path class="svg_icon_foreground" d="M397.888,373.24H340.52V283.346c0-21.437-.436-49.023-29.895-49.023-29.911,0-34.479,23.325-34.479,47.441V373.24H218.778V188.383h55.108v25.2h.743c7.7-14.528,26.424-29.863,54.4-29.863,58.127,0,68.878,38.256,68.878,88.054V373.24Z" transform="translate(-10 -10)"/>
					<path class="svg_icon_foreground" d="M153.984,163.089A33.317,33.317,0,1,1,187.3,129.756,33.284,33.284,0,0,1,153.984,163.089Z" transform="translate(-10 -10)"/>
					<rect class="svg_icon_foreground" x="115.22" y="178.383" width="57.53" height="184.857"/>
				</g>
			</svg>
		</a>
	</div>

	<!-- TWITTER ICON & LINK -->
	<div class="social-media-icon-container">
		<a href="<?= TWITTER_URL; ?>" title="Find Jones Sign Company on twitter" class="social-media-icon-link">
			<svg class="social-media-icon"
			version="1.0"
			id="twitter-icon"
			xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink"
			x="0px"
			y="0px"
			viewBox="0 0 500 500"
			style="enable-background:new 0 0 500 500;"
			xml:space="preserve">
				<circle class="svg_icon_background" cx="250" cy="250" r="240"/>
				<path class="svg_icon_foreground" d="M425,144.7c-12.5,5.5-25.9,9.3-40,11c14.4-8.6,25.4-22.3,30.6-38.6c-13.5,8-28.4,13.8-44.3,16.9 c-12.7-13.5-30.8-22-50.9-22c-45,0-78.1,42-67.9,85.6c-57.9-2.9-109.3-30.6-143.6-72.8c-18.3,31.3-9.5,72.3,21.6,93 c-11.4-0.4-22.2-3.5-31.5-8.7c-0.8,32.3,22.4,62.5,55.9,69.2c-9.8,2.7-20.6,3.3-31.5,1.2c8.9,27.7,34.6,47.8,65.1,48.4 c-29.3,23-66.2,33.2-103.2,28.9c30.8,19.8,67.5,31.3,106.8,31.3c129.4,0,202.5-109.3,198.1-207.3C403.8,170.9,415.6,158.6,425,144.7 z"/>
			</svg>
		</a>
	</div>

	<!-- FACEBOOK ICON & LINK -->
	<div class="social-media-icon-container">
		<a href="<?= FACEBOOK_URL; ?>" title="Find Jones Sign Company on Faceboook" class="social-media-icon-link">
			<svg class="social-media-icon"
			version="1.1"
			id="facebook-icon"
			xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink"
			x="0px"
			y="0px"
			viewBox="0 0 500 500"
			style="enable-background:new 0 0 500 500;"
			xml:space="preserve">
				<circle class="svg_icon_background" cx="250" cy="250" r="240"/>
				<path class="svg_icon_foreground" d="M318.3,154.4h-31.2c-12.4,0-15,5.1-15,18v28.2h46.2l-4.8,46.2h-41.4v161.8h-69.3V246.9h-46.2v-46.2h46.2v-53.3 c0-40.9,21.5-62.2,70-62.2h45.6V154.4z"/>
			</svg>
		</a>
	</div>

	<!-- WESCOVER ICON & LINK -->
	<div class="social-media-icon-container">
		<a href="<?= WESCOVER_URL; ?>" title="Find Jones Sign Company on WeScover" class="social-media-icon-link">
			<svg class="social-media-icon"
			version="1.1"
			id="wescover-icon"
			xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink"
			x="0px"
			y="0px"
			viewBox="0 0 500 500"
			>
				<circle class="svg_icon_background" cx="250" cy="250" r="240"/>
				<g class="svg_icon_foreground">
					<polygon class="svg_icon_foreground" points="157.6,158.1 193.5,391.2 229.4,391.2 249.9,252.4 250.4,252.4 270.6,391.2 306.5,391.2 342.4,157.8 307.1,157.8 286.7,312.4 286.1,312.4 266,157.8 234.6,157.8 214.5,312.4 213.9,312.4 193.5,157.8 	"/>
					<rect x="157.6" y="108.8" class="svg_icon_foreground" width="184.8" height="32.7"/>
				</g>
			</svg>
		</a>
	</div>
</nav>
