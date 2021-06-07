<?php
/**
 * Template part for displaying a post's taxonomy terms
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$taxonomies = wp_list_filter(
	get_object_taxonomies( $post, 'objects' ),
	array(
		'public' => true,
	)
);

?>
<aside class="entry-taxonomies">
	<dl>
	<?php
	// Show terms for all taxonomies associated with the post.
	foreach ( $taxonomies as $taxonomy ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		/* translators: separator between taxonomy terms */
		$separator = _x( ', ', 'list item separator', 'wp-rig' );

		switch ( $taxonomy->name ) {
			case 'category':
				$class = 'category-links term-links';
				$list  = get_the_category_list( esc_html( $separator ), '', $post->ID );
				/* translators: %s: list of taxonomy terms */
				$placeholder_text = __( 'Posted in %s', 'wp-rig' );
				break;
			case 'post_tag':
				$class = 'tag-links term-links';
				$list  = get_the_tag_list( '', esc_html( $separator ), '', $post->ID );
				/* translators: %s: list of taxonomy terms */
				$placeholder_text = __( 'Tagged %s', 'wp-rig' );
				break;
			default:
				$class    = str_replace( '_', '-', $taxonomy->name ) . '-links term-links';
				$list     = get_the_term_list( $post->ID, $taxonomy->name, '', esc_html( $separator ), '' );
				$quantity = get_the_terms( $post->ID, $taxonomy->name ) ? count ( get_the_terms( $post->ID, $taxonomy->name ) ) : 1;
				// If it has more than one tag, the taxonomy should be in the plural format.
				$placeholder_text = ( 1 < $quantity ) ? ucwords( $taxonomy->label ) : ucwords( $taxonomy->name );
		}

		if ( empty( $list ) ) {
			continue;
		}
		?>
	<div class="inner_taxonomy_definition_list">
		<dt class="<?= esc_attr( $class ); ?>"><?= esc_html( $placeholder_text ); ?></dt>
		<dd><?php printf( $list ); ?></dd>
	</div>
		<?php
	}
	?>
	</dl>
</aside><!-- .entry-taxonomies -->
