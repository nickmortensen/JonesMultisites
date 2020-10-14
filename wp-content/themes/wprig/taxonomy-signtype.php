<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$signtype = get_queried_object();

[
	'images_url'  => $images,
	'images_id'   => $images_id,
	'indepth'     => $indepth,
	'description' => $description,
	'alt_names'   => $alternate_names,
	'indepth'     => $indepth,

] = wp_rig()->get_all_signtype_info( get_queried_object()->term_id );

$related_images = wp_rig()->get_related_images( get_queried_object()->term_id );

[
	'square'      => $square_id,
	'vertical'    => $vertical_id,
	'cinematic'   => $cinematic_id,
	'rectangular' => $rectangular_id,
] = $images_id;

$size = 'large';

$vertical_src     = wp_get_attachment_image_src( $vertical_id, $size )[0];
$cinematic_src    = wp_get_attachment_image_src( $cinematic_id, $size )[0];
$vertical_srcset  = wp_get_attachment_image_srcset( $vertical_id, $size )[0];
$cinematic_srcset = wp_get_attachment_image_srcset( $cinematic_id, $size )[0];

get_header();

wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-taxonomy', 'wp-rig-project' );


?>


	<main id="single-item" class="taxonomy signtype">

		<header class="entry-header">
			<div class="single-header hide-on-wide" style="background: var(--blue-900) center / cover no-repeat url( <?= $cinematic_src; ?>);">

			</div><!-- end div.single-header.hide-on-wide -->

			<div class="single-header:wide">
				<div id="vertical" style="background: var(--blue-900) center / cover no-repeat url( <?=$vertical_src; ?> );"></div>
				<div>
					<h1 class="title"><?= ucwords( get_queried_object()->name ); ?></h1>
					<article class="narrative"><?= get_queried_object()->description; ?></article>
				</div>
			</div><!-- end div.single-header:wide -->
		</header>

		<section class="single-item-content hide-on-wide">
			<article class="narrative"></article>
		</section>


		<section id="related_images">
			<?php
				foreach ( $related_images as $index => $identifier ) {
					$size = 'medium';
					echo '<img src="' . wp_get_attachment_image_src( $identifier, $size )[0] . '"/>';
				}
			?>
		</section>

	</main><!-- #single-item -->
<?php

wrap( get_queried_object() );
wrap( wp_rig()->get_all_signtype_info( get_queried_object()->term_id ) );
wrap($related_images);
if ( is_super_admin() ) {
	edit_tag_link();
}
get_footer();
