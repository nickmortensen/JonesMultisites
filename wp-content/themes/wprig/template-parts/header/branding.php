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
<div id="branding">
	<a href="<?= esc_url( home_url() ); ?>" title="<?= $titletext; ?>">
		<img src="<?= get_theme_file_uri( '/assets/images/jonessign_circular_symbol.png' ); ?>" alt="jones sign company" />
	</a>
</div><!-- .site-branding -->
