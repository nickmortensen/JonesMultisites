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
$attributes = [
	'logo'       => 'sign',
	'background' => '#e6e6e6',
	'foreground' => '#0273b9',
];

?>
<div id="branding" class="logo_container">
<a href="<?= esc_url( home_url() ); ?>" title="<?= $titletext; ?>">
<?= wp_rig()->get_jones_icon( $attributes ); ?>
</a>
</div><!-- .site-branding -->
