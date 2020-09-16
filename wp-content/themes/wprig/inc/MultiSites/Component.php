<?php
/**
 * WP_Rig\WP_Rig\MultiSites\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\MultiSites;

use WP_Rig\WP_Rig\Component_Interface;
use function WP_Rig\WP_Rig\wp_rig;
use WP_Post;
use function add_action;
use function add_filter;
use function wp_enqueue_script;

/**
 * Class for improving accessibility among various core features.
 *
 * @see wp-admin/includes/class-wp-ms-sites-list-table.php
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'multisites';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/wpmu_blogs_columns/
	 */
	public function initialize() {
		add_filter( 'wpmu_blogs_columns', [ $this, 'get_id' ] );
		add_filter( 'manage_sites-network_sortable_columns', [ $this, 'make_columns_sortable' ] );
		add_action( 'manage_sites_custom_column', [ $this, 'add_columns' ], 10, 2 );
		add_action( 'manage_blogs_custom_column', [ $this, 'add_columns' ], 10, 2 );
	}

	/**
	 * Output the mission statement.
	 *
	 * @param bool $whole_thing  Outputs the mission statement plus the writeup if true, default is false.
	 */
	public function mission_statement( $whole_thing = false ) {
		$description          = 'Jones Sign Company is easy to work with because we are flexible in the way our customers can engage us. Jones Sign provides Solutions, not just products. We are consultants for our customers. Our SSP process makes it easy for our customers to ensure their vision is documented before they start construction. Jones Sign delivers the Design/Build process as a menu of services with clear deliverables. Individual steps of the planning process can be purchased one at a time so there are no big commitments up front.';
		$jones['mission']     = 'Be the Easy-to-Work-With Solution Provider';
		$jones['description'] = $whole_thing ? $description : '';
		return $jones;
	}

	/**
	 * Output the tagline.
	 *
	 * @param bool $whole_thing  Outputs the tagline plus the writeup if true, default is false.
	 */
	public function tagline( $whole_thing = false ) {
		$description          = 'Jones Sign Company reps listen to & understand client goals. Our entire team\'s expertise & resources can then create, document, and execute the client vision. There are many ways to bring a project to life and Jones Sign Company knows them all';
		$jones['tagline']     = 'Your Vision. Accomplished';
		$jones['description'] = $whole_thing ? $description : '';
		return $jones;
	}

	/**
	 * Adds a column to the multisites admin table for blog_id.
	 *
	 * @param string $column_name Name of the column.
	 * @param int    $blog_id     Id of the site / blog.
	 * @link https://wpengineer.com/2188/view-blog-id-in-wordpress-multisite/
	 *
	 * @return string $column_name Name of the column.
	 */
	public function add_columns( $column_name, $blog_id ) {
		if ( 'blog_id' === $column_name ) {
			$url = esc_url( network_admin_url( 'site-info.php?id=' . $blog_id ) );
			echo "<a href='$url' class=\"edit\">$blog_id</a>";
		}

		return $column_name;
	}

	/**
	 * Retrieve the ID of the blog -- or site.
	 *
	 * @param  array $columns The existing columns, before adding new key/value(s).
	 * @return array $columns The existing columns, now with the new column added.
	 */
	public function get_id( $columns ) {
		unset( $columns['registered'] );
		$new['cb']      = array_slice( $columns, 0, 1 );
		$new['blog_id'] = 'ID';
		return array_merge( $new, $columns );
	}

	/**
	 * Ensure that the Blog ID Column is sortable.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/manage_this-screen-id_sortable_columns/
	 * @param array $columns Existing columns.
	 * @return array $columns Existing columns + any new columns you want to be sortable.
	 */
	public function make_columns_sortable( $columns ) {
		$columns['blog_id'] = 'ID';
		$columns['users']   = 'Users';
		return $columns;
	}

}
