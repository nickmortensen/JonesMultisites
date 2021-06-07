<?php
/**
 * Template part for displaying a post's footer
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<div class="entry-footer main-six">
	<?php

	if ( 'project' === get_post_type() ) {
		echo wp_rig()->get_project_taxonomies_aside();
	} else {
		get_template_part( 'template-parts/content/entry_taxonomies', get_post_type() );

	}
	?>


	<?php get_template_part( 'template-parts/content/entry_actions', get_post_type() ); ?>
</div><!-- .entry-footer -->
