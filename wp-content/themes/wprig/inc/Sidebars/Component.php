<?php
/**
 * WP_Rig\WP_Rig\Sidebars\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Sidebars;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use function add_action;
use function add_filter;
use function register_sidebar;
use function esc_html__;
use function is_active_sidebar;
use function dynamic_sidebar;




/**
 * Class for managing sidebars.
 *
 * Exposes template tags:
 * * `wp_rig()->is_primary_sidebar_active()`
 * * `wp_rig()->display_primary_sidebar()`
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/
 */
class Component implements Component_Interface, Templating_Component_Interface {

	const PRIMARY_SIDEBAR_SLUG = 'sidebar-1';
	const HAMBURGER_MENU_SLUG  = 'hamburger-menu';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'sidebars';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'widgets_init', [ $this, 'action_register_sidebars' ] );
		add_filter( 'body_class', [ $this, 'filter_body_classes' ] ); // Add hide-sidenav class to body.
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [
			'is_primary_sidebar_active' => [ $this, 'is_primary_sidebar_active' ],
			'display_primary_sidebar'   => [ $this, 'display_primary_sidebar' ],
			'is_hamburger_menu_active'  => [ $this, 'is_hamburger_menu_active' ],
			'display_hamburger_menu'    => [ $this, 'display_hamburger_menu' ],
			'get_navbar_icons'          => [ $this, 'get_navbar_icons' ],
		];
	}

	/**
	 * Get the title attribute text for the Jones Sign Company icon
	 */
	public function get_icon_titletext() {
		$titletext = 'Go to the Jones Sign Company Homepage';
		if ( is_home() ) {
			$titletext = 'You are on the Jones Sign Homepage';
		}
		return $titletext;
	}

	/**
	 * Navigation Bar Icons.
	 *
	 * @param bool $top Are these icons to go on top?
	 */
	public function get_navbar_icons( $top = true ) {

		$html = '';
		$html .= '<nav class="navigation-icons">';
		$html .= $this->get_nav_logo();
		$html .= $this->get_sidebar_toggle();
		$html .= $this->get_blank_space();
		$html .= $this->get_search_icon();
		$html .= $this->get_mail_icon();
		$html .= $this->get_more_icon();
		$html .= '</nav>';

		return $html;

	}

/**
 * Styles for the svg logo.
 */

public function get_logo_styles() {
	$styles = <<<SVG
<style>


a.svgLogoLink {
	display: inline-block;
	height: 100%;
	width: 100%;
	max-height: 80px;
	max-width: 80px;

	display: flex;
	flexc-flow: column nowrap;
	justify-content: center;
	align-items: center;
}



</style>



SVG;

return $styles;
}

	/**
	 * The company logo.
	 */
	public function get_nav_logo() {
		$titletext = $this->get_icon_titletext();
		$html = $this->get_logo_styles();
		$html .= '<div data-opens="homepage" id="svgIconContainer" class="nav-icon-container">';
		$html .= '<a class="svgLogoLink" href="' . home_url() . '" title="' . $titletext . '">';
		// $html .= '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="pylon_circle" class="jones_icon" x="0px" y="0px" viewBox="0 0 500 500" style="enable-background:new 0 0 500 500;" xml:space="preserve">';
		$html .= '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="pylon_circle" class="jones_icon" x="0px" y="0px" viewBox="0 0 500 500" xml:space="preserve">';
		// $html .= '<circle cx="262.9" cy="242.5" r="215.3"/>';
		$html .= '<circle cx="250" cy="250" r="240" stroke-width="15" stroke="white" fill="transparent"/>';
		$html .= '<path stroke="white" fill="white" stroke-width="20"class="pylon" d="M430.4,225.9H248.7c-8.1,0-14.8-6.6-14.8-14.8v-18h-18.6c-3.3,8.7-11.9,15.6-22.2,15.9L89.8,370.5 c-4.3-5.9-8.4-12-12.1-18.3l98.1-150c-4.8-4.5-7.8-10.6-7.8-17.7c0-13.5,10.9-24.3,24.5-24.3c11.7,0,21.5,7.8,23.9,19.3H234v-24.8 c0-8.1,6.6-14.8,14.8-14.8h181.6c8.1,0,14.8,6.6,14.8,14.8v56.3C445.1,219.3,438.5,225.9,430.4,225.9z"/>';
		$html .= '</svg>';
		$html .= '</a>';
		$html .= '</div><!-- end div#svgiconcontainer -->';

		return $html;
	}

	/**
	 * Output the menu icon that toggles to a back arrow on click arrows.
	 */
	public function get_sidebar_toggle() {
		$html = '';
		$html .= '<div data-opens="sidebar" id="sidebarToggleContainer" class="nav-icon-container open_close_sidebar" title="open/close sidebar menu">';
		$html .= "\n\t";
		$html .= '<div>';
		$html .= str_repeat( "\n\t\t<span></span>", 3 );
		$html .= "\n\t";
		$html .= '</div>';
		$html .= "\n";
		$html .= '</div><!-- end div#sidebartogglecontainer -->';
		// $html .= $this->sidebarToggleScript();
		return $html;
	}

	public function sidebarToggleScript() {

		$sidebar_toggle_script = <<<SDTGS
<script type="text/javascript">
const sidebarToggleContainer = document.querySelector( ".navigation-icons > #sidebarToggleContainer" );
sidebarToggleContainer.addEventListener( 'click', function( e ) {
	e.preventDefault();
	document.documentElement.classList.toggle( 'sidebar-open' );
});
</script>
SDTGS;

		return $sidebar_toggle_script;

	}


	/**
	 * Gets the html for the blank space in the navbar.
	 */
	public function get_blank_space() {
		return '<div id="open_space" class="nav-icon-container"></div><!-- end div#open_space -->';
	}

	/**
	 * Gets the html for the mail icon.
	 */
	public function get_mail_icon() {
		$html = '';
		$html .= '<div data-opens="sidebar-contact" id="mailIconContainer" class="nav-icon-container">';
		$html .= '<a id="contactUsToggle" class="material-icons nav_contact" title="Contact Us" data-iconname="mail_outline" type="button">mail_outline</a>';
		$html .= '</div><!-- end div#mailIconContainer -->';
		return $html;
	}

	/**
	 * Gets the html for the search icon.
	 */
	public function get_search_icon() {
		$html = '';
		$html .= '<div data-opens="search" id="searchToggleContainer" class="nav-icon-container">';
		$html .= '<a id="searchToggle" class="material-icons nav_search" title="Search jonessign.com" data-iconname="search" type="button">search</a>';
		$html .= '</div><!-- end div#searchtogglecontainer -->';
		return $html;
	}

	/**
	 * Gets the html for the more icon.
	 */
	public function get_more_icon() {
		$icontext = 'development' !== ENVIRONMENT ? 'more_vert' : 'developer_mode';
		$html = '';
		$html .= '<div data-opens="sidebar-general" id="moreToggleContainer" class="nav-icon-container">';
		$html .= '<a class="material-icons nav_more" title="Search jonessign.com" data-iconname="' . $icontext . '" type="button">' . $icontext . '</a>';
		$html .= '</div><!-- end div#moretogglecontainer -->';
		return $html;
	}
	/**
	 * Registers the sidebars.
	 */
	public function action_register_sidebars() {
		$sidebars = [
			[
				'name'          => esc_html__( 'Sidebar', 'wp-rig' ),
				'id'            => static::PRIMARY_SIDEBAR_SLUG,
				'description'   => esc_html__( 'Add widgets here.', 'wp-rig' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			],
			[
				'name'          => esc_html__( 'Hamburger Menu', 'wp-rig' ),
				'id'            => static::HAMBURGER_MENU_SLUG,
				'description'   => esc_html__( 'Add menu here.', 'wp-rig' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			],
		];
		foreach ( $sidebars as $sidebar ) {
			register_sidebar( $sidebar );
		}
	}

	/**
	 * Adds custom classes to indicate whether a sidebar is present to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 * @return array Filtered body classes.
	 */
	public function filter_body_classes( $classes ) {
		global $template;
		$newclasses = $classes;
		if ( $this->is_primary_sidebar_active() ) {
			if ( ! in_array( basename( $template ), [ '404.php', '500.php', 'offline.php' ], true ) ) {
				$newclasses[] = '';
			}
		}

		if ( $this->is_hamburger_menu_active() ) {
			if ( ! in_array( basename( $template ), [ '404.php', '500.php', 'offline.php' ], true ) ) {
				$newclasses[] = 'hamburger_menu';
			}
		}
		return $newclasses;
	}

	/**
	 * Checks whether the primary sidebar is active.
	 *
	 * @return bool True if the primary sidebar is active, false otherwise.
	 */
	public function is_primary_sidebar_active() : bool {
		return (bool) is_active_sidebar( static::PRIMARY_SIDEBAR_SLUG );
	}
	/**
	 * Checks whether the primary sidebar is active.
	 *
	 * @return bool True if the primary sidebar is active, false otherwise.
	 */
	public function is_hamburger_menu_active() : bool {
		return (bool) is_active_sidebar( static::HAMBURGER_MENU_SLUG );
	}

	/**
	 * Displays the primary sidebar.
	 */
	public function display_primary_sidebar() {
		dynamic_sidebar( static::PRIMARY_SIDEBAR_SLUG );
	}
	/**
	 * Displays the primary sidebar.
	 */
	public function display_hamburger_menu() {
		dynamic_sidebar( static::HAMBURGER_MENU_SLUG );
	}
}
