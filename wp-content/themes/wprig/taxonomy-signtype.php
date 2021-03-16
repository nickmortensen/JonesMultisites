<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

/**
 * Show inside of pre tags
 *
 * @param [string] $content The content to show.
 *
 */
function display_in_pre( $content ) {
	return "<pre>$content</pre>";
}

$signtype = get_queried_object();

[
	'images_url'  => $images,
	'images_id'   => $images_id,
	'indepth'     => $indepth,
	'description' => $description,
	'alt_names'   => $alternate_names,
	'indepth'     => $indepth,

] = wp_rig()->get_all_signtype_info( get_queried_object()->term_id );

$related_images   = wp_rig()->get_related_images( get_queried_object()->term_id );
$related_projects = wp_rig()->get_related( get_queried_object()->term_id, 'project' );

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

get_header( 'experimental' );

wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-taxonomy', 'wp-rig-project' );



?>


	<main data-gridarea="main" id="single-item" class="taxonomy signtype">

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
			<h2>Related Images</h2>
			<?php
				foreach ( $related_images as $index => $identifier ) {
					// $size = 'medium';
					// print_r( wp_get_attachment_image_src( $identifier, $size )[0] );
					// echo '<img src="' . wp_get_attachment_image_src( $identifier, $size )[0] . '"/>'; full stop.
				}
			?>
		</section>


	</main><!-- #single-item -->

<?php if ( $related_images ) : ?>
<section id="related_images">
	<h2>related images</h2>
	<?php wrap( $related_images ); ?>
</section>
<?php endif; ?>

<?php if ( $related_projects ) : ?>
	<section id="related_projects">
		<h2>related projects</h2>
		<?php wrap( $related_projects ); ?>
	</section>
<?php endif; ?>


<?php
// echo '<h2> get_queried_object() data -></h2>';
// wrap( get_queried_object() );
// echo '<h2> Signtype Data -></h2>';
// wrap( wp_rig()->get_all_signtype_info( get_queried_object()->term_id ) );
// echo '<h2> Related Images By ID</h2>';




if ( is_super_admin() ) {
	edit_tag_link();
}
get_footer();
