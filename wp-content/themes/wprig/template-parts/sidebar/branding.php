<?php
/**
 * Template part for displaying the header branding
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<?php
	$titletext = 'Go to the Jones Sign Company Homepage';
	if ( is_home() ) {
		$titletext = 'You are on the Jones Sign Homepage';
	}
?>



<a class="sidebar light-links"style="width: 80px;" href="<?= esc_url( home_url() ); ?>" title="<?= $titletext; ?>">
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" viewBox="0 0 500 500" style="enable-background:new 0 0 500 500;" xml:space="preserve">
		<circle cx="250" cy="250" r="245"/>
		<path d="M450.5,215c8.6,0,15.5-8,15.5-16.6v-59.7c0-8.6-6.9-15.7-15.5-15.7H258.2c-8.6,0-15.2,7.1-15.2,15.7 V166h-29.8c-2.6-13-12.9-21-25.3-21c-14.3,0-25.9,11.2-25.9,25.5c0,7.5,3.2,13.8,8.3,18.6L43.8,382.7c4.3,6.5,8.8,12.8,13.7,18.8 L188.9,196c10.8-0.4,20-8,23.5-17H243v19.4c0,8.6,6.6,16.6,15.2,16.6L450.5,215L450.5,215z"/>
	</svg>
</a>
<!-- END JONESSIGN ICON OF A PYLON -->
