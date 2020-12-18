<?php
/**
 * Tweaks for an admin
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<style>
	#pageoptions {
		display: flex;
		justify-content: space-evenly;
		align-items: flex-start;
		flex-flow: row nowrap;
		justify-content: center;
		min-width: 100vw;
	}

	#pageoptions > div {
		margin: 2vw;
	}

	#pageoptions > div > label {
		font-size: var(--fontsize-m );
		padding-right: 2vw;
	}

</style>

<section id="pageoptions">
<?php
	$options = [ 'screen', 'overlay', 'darken', 'lighten', 'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity', 'normal' ];
	echo '<div>';
	echo "<label for='blend'>Select Background Blend</label>";
	echo "<select id='blend'>";
	echo "<option value='multiply'>Multiply</option>";
	foreach ( $options as $option ) {
		echo "<option value='$option'>$option</option>";
	}
	echo '</select>';
	echo '</div>';
	$options = [ 'multiply', 'screen', 'overlay', 'darken', 'color-dodge', 'color-burn', 'lighten', 'soft-light', 'difference', 'exclusion', 'hue', 'saturation', 'color', 'luminosity', 'normal' ];
	echo '<div>';
	echo "<label for='headingblend'>Select Text Blend</label>";
	echo "<select id='headingblend'>";
	echo "<option value='hard-light'>Hard Light</option>";
	foreach ( $options as $option ) {
		echo "<option value='$option'>$option</option>";
	}
	echo '</select>';
	echo '</div>';
?>
</section>
