<?php
/**
 * Template part for displaying a project's metadata - meaning the date last modified.
 *
 * @modified 06_October_2020.
 *
 * @package wp_rig
 *
 * @link https://www.php.net/manual/en/datetime.format.php.
 */

namespace WP_Rig\WP_Rig;

$post_type_obj = get_post_type_object( get_post_type() );

$time_string = '';

// Show date only when the post type is 'post' or has an archive.
if ( 'post' === $post_type_obj->name || $post_type_obj->has_archive ) {
	$time_string   = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	$datetime_full = get_the_modified_date( 'c' ); // ISO 8601 date -- 2025-02-20T15:25:59+00:00.
	$datetime      = get_the_modified_date( 'l - F jS' ); // Thursday October 1st.
	$time_string   = sprintf( $time_string, esc_attr( $datetime_full ), esc_html( $datetime ) );

	$time_string = '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>';
}


$parent_string = '';

// Show parent post only if available and if the post type is 'attachment'.
if ( ! empty( $post->post_parent ) && 'attachment' === get_post_type() ) {
	$parent_string = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url( get_permalink( $post->post_parent ) ),
		esc_html( get_the_title( $post->post_parent ) )
	);
}

?>
<div class="entry-meta">
	<?php if ( ! empty( $time_string ) ) : ?>
	<span class="modified-on">
		<?php
		printf(
			/* translators: %s: modified date */
			esc_html_x( 'Updated: %s', 'post date', 'wp-rig' ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		?>
	</span>
	<?php endif; ?>

	<?php if ( ! empty( $parent_string ) ) : ?>
	<span class="posted-in">
		<?php
		/* translators: %s: post parent title */
		$parent_note = _x( 'In %s', 'post parent', 'wp-rig' );
		if ( ! empty( $time_string ) || ! empty( $author_string ) ) {
			/* translators: %s: post parent title */
			$parent_note = _x( 'in %s', 'post parent', 'wp-rig' );
		}
		printf(
			esc_html( $parent_note ),
			$parent_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
		?>
	</span>
	<?php endif; ?>
</div><!-- .entry-meta -->
<?php
