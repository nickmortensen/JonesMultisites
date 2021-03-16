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

<nav id="site-navigation" class="header-navigation" aria-label="Main Menu" >

<?php
	$site_menu_arguments = [
		'menu'                 => 'primary-menu',
		'container'            => '',
		'container_class'      => '',
		'container_id'         => '',
		'container_aria_label' => '',
		'menu_class'           => 'headmenu',
		'menu_id'              => '',
		'echo'                 => true,
		'fallback_cb'          => 'wp_page_menu',
		'before'               => '',
		'after'                => '',
		'link_before'          => '',
		'link_after'           => '',
		'items_wrap'           => '<ul id="%1$s" class="%2$s">%3$s</ul>',
		'item_spacing'         => 'preserve',
		'depth'                => 0,
		'walker'               => '',
		'theme_location'       => '',
	];
?>
	<?php wp_rig()->display_primary_nav_menu( $site_menu_arguments ); ?>

<!-- KEEP THIS </nav>-- it is needed, the Walker Class isn't -->
</nav>

