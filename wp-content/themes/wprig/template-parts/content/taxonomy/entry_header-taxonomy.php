<?php
/**
 * Template part for displaying a post's header - used in a loop on a taxonomy page
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>


<section class="entry-header">

	<?php
	get_template_part( 'template-parts/content/entry_title', get_post_type() );

	/*
	 * NO REAL NEED FOR THE ENTRY META FROM WHAT I CAN TELL
	 */
	get_template_part( 'template-parts/content/entry_meta', get_post_type() );

	if ( ! is_search() ) {
		/* THUMBNAIL ONLY ON slim and WIDE Screens */
		get_template_part( 'template-parts/content/entry_thumbnail', get_post_type() );
	}
	?>
</section><!-- .entry-header -->

