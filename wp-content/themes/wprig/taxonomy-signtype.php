<?php
/**
 * The main template file for the signtype taxonomy.
 *
 * Used to display a signtype
 *
 * Should Contain
 * 1. Nice Scheme.org compliant characterization of the product type.
 * 2. Description of the product
 * 3. Header Image of the best example of this particular sign type
 * 4. Description of the sign type that will be used for SEO purposes.
 * 5. Call-to-Action
 * 6. Link to projects that feature this sign type.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header( 'signtype' );
global $wp_query;
$termid = get_queried_object()->term_id;
$slug   = get_queried_object()->slug;

// Destructuring the array that returns from the get_all_info() function call.
[
	'uses'        => $uses,
	'name'        => $signtype,
	'description' => $description,
	'indepth'     => $long_desc,
] = wp_rig()->get_all_info( get_queried_object()->term_id );
[
	'vertical'    => $vertical,
	'cinematic'   => $cinematic,
	'rectangular' => $rectangular,
	'square'      => $square,
] = wp_rig()->get_signtype_images( get_queried_object()->term_id );

$related = [
	'images'   => wp_rig()->get_related( get_queried_object()->term_id, 'attachment' ),
	'projects' => wp_rig()->get_related( get_queried_object()->term_id ),
];



$projects = wp_rig()->get_related( get_queried_object()->term_id, 'project' );



wp_rig()->print_styles( 'wp-rig-content', 'signtype' );

/**
 * Output a list of uses with periods at the end.
 *
 * @param $array $uses The usage scenarios for this sign type.
 */
function uses_list( $uses ) {
	$output = '';
	$list   = [];
	if ( $uses ) {
		foreach ( $uses as $key => $use ) {
			$use    = substr( $use, -1 ) === '.' ? $use : $use . '.';
			$list[] = wp_sprintf( '<li class="header">%s</li>', $use );
		}
		$output  = '<ul style="list-style-type: circle;">';
		$output .= implode( '', $list );
		$output .= '</ul>';
	}
	return $output;
}


?>

<section id="masthead">
	<div>
		<h1><?= ucwords( get_queried_object()->name ); ?> </h1>
		<p><?= $description ?></p>
		<?= uses_list( $uses ); ?>
	</div>

	<div>
		<picture>
			<source srcset="<?= wp_get_attachment_image_srcset( $vertical, 'medium' ); ?> ">
			<img>
		</picture>
	</div>
</section><!-- end section#masthead -->









	<section id="signtype-links" class="flex-row nw justify-between align-center" style="min-height: 440px; background: var(--yellow-700);">
	<h2>links to projects with this signtype</h2>
	</section><!-- end section#signtype-links -->


	<!-- GALLERY of images using this term -->
	<section class="related_images">
		<h2> <?= ucwords( get_queried_object()->name ); ?> Image Gallery</h2>
		<div id="signtype-gallery"></div><!-- end section#signtype-gallery -->
	</section>


<style>
	div#signtype-primary {
		height: 1900px;
	}
	section[id^="term"] {
		min-height: 20%;
		min-width: 100vw;
		border-bottom: 18px solid var(--purple-600);
		margin-top: 15px;
		display: flex;
		flex-flow: row nowrap;
		justify-content: center;
		align-items: flex-end;
	}

</style>

<div id="signtype-primary" class="w-screen flex col-nw justify-around">

	<section id="term-description">
		<h2>description of the sign type</h2>
	</section><!-- end section#signtype-description -->




	<!-- projects that feature this term -->
	<section id="project-links">
		<h2> here you will put links to projects that have this term attached</h2>
	</section><!-- end section#signtype-project-links -->

	<!-- links to other terms in the same taxonomy -->
	<section id="other-terms">
		<a title="link to the previous sign type" href=""></a>
		<a title="link to the next sign type" href=""></a>
	</section><!-- end section#other-terms -->

</div><!-- end div.signtype-primary -->


<?php
get_sidebar();
get_footer();
