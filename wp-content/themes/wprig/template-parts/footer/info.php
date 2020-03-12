<?php
/**
 * Template part for displaying the footer info
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<div class="site-info">
<?php
if ( 'development' === ENVIRONMENT ) {
	echo 'blog id = ' . get_current_blog_id();
}
?>
</div><!-- .site-info -->
