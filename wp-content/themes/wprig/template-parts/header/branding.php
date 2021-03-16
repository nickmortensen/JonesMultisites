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

<style>

a.svg_hyperlink {
	display: inline-block;
	width: clamp(40px, 8vmin, 160px);
}

</style>
<div id="branding" class="logo_container">
	<a class="svg_hyperlink" href="<?= esc_url( home_url() ); ?>" title="<?= $titletext; ?>">
		<?= wp_rig()->get_jones_icon( 'sign' ); ?>
	</a>
</div><!-- #branding -->
