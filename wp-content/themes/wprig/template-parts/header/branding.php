<?php
/**
 * Template part for displaying the header branding
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;


?>

<style>

.branding {
	display: flex;
	flex-flow: row nowrap;
	justify-content: flex-end;
	align-items: center;
}
img.clogo {
	--blendmode: hard-light;
	--filter: drop-shadow(5px 5px 5px #fff);
	--filter: drop-shadow(5px 5px 5px #fff) drop-shadow(-5px -5px 5px var(--yellow-300)) ;
	--backdrop-filter: brightness(60%);
		height: clamp(44px, 7.4vw, 240px);
	width: clamp(44px, 7.4vw, 240px);
	filter: var(--filter);
	mix-blend-mode: var(--blendmode);

}

</style>


<?php
	$titletext = 'Go to the Jones Sign Company Homepage';
if ( is_home() ) {
	$titletext = 'You are on the Jones Sign Homepage';
}

?>
<div class="branding">
	<a href="<?= esc_url( home_url() ); ?>" title="<?= $titletext; ?>">
		<img src="<?= get_theme_file_uri( '/assets/images/jonessign_circular_symbol.png' ); ?>" alt="jones sign company" class="clogo" />
	</a>
</div><!-- .site-branding -->
