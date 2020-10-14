<?php
/**
 * WP_Rig\WP_Rig\EnhancedMedia\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\EnhancedMedia;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Query;
use function add_action;
use function get_terms;
use function get_term;
use function get_term_meta;
use function register_taxonomy;

/**
 * NOTE: See description on the google rich snippet for product.
 *
 * @link https://developers.google.com/search/docs/data-types/product
 */
class Component implements Component_Interface, Templating_Component_Interface {

}

