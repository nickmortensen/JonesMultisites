<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

if ( ! wp_rig()->is_primary_nav_menu_active() ) {
	return;
}

?>

<nav
	id="site-navigation"
	class="main-navigation nav--toggle-sub nav--toggle-small"
	aria-label="<?php esc_attr_e( 'Main menu', 'wp-rig' ); ?>"

	<?php if ( wp_rig()->is_amp() ) : ?>
		[class]=" siteNavigationMenu.expanded ? 'main-navigation nav--toggle-sub nav--toggle-small nav--toggled-on' : 'main-navigation nav--toggle-sub nav--toggle-small' "
	<?php endif; ?>

> <!-- end nav opening tag -->


	<?php if ( wp_rig()->is_amp() ) : ?>

	<amp-state id="siteNavigationMenu">
		<script type="application/json">
			{
				"expanded": false
			}
		</script>
	</amp-state>

	<?php endif; ?>

	<!-- <button
		class="menu-toggle box-shadow-menu"
		aria-label="<?php esc_attr_e( 'Open menu', 'wp-rig' ); ?>"
		aria-controls="primary-menu"
		aria-expanded="false"

		<?php if ( wp_rig()->is_amp() ) : ?>
			on="tap:AMP.setState( { siteNavigationMenu: { expanded: ! siteNavigationMenu.expanded } } )"
			[aria-expanded]="siteNavigationMenu.expanded ? 'true' : 'false'"
		<?php endif; ?>

	>
		<?php esc_html_e( '', 'wp-rig' ); ?>
	</button>-->




		<?php
			$menu_arguments = [
				'menu_id'    => 'primary-menu',
				'menu_class' => 'dark_background',
			];
			wp_rig()->display_primary_nav_menu( $menu_arguments );
		?>
</nav><!-- end div#site-navigation -->
