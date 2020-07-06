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
wp_rig()->print_styles( 'wp-rig-content' );

$signtype     = wp_rig()->get_all_info( get_queried_object()->term_id );
$short_desc   = ( substr( $signtype['description'], -1 ) === '.' ) ? $signtype['description'] : $signtype['description'] . '.';
$long_desc    = ( substr( $signtype['indepth'], -1 ) === '.' ) ? $signtype['indepth'] : $signtype['description'] . '.';
$uses         = wp_rig()->get_all_info( get_queried_object()->term_id )['uses'];
$uses         = $signtype['uses'];

?>

<section class="flex col-nw justify-center align-center">
	<div id="signtype-in-depth" class="w-3/5 p-6">
		<?= wp_rig()->get_signtype_indepth( get_queried_object()->term_id ); ?>
	</div>
</section>

<section id="signtype-links" class="flex-row nw justify-between align-center">

</section><!-- end section#signtype-links -->
<div id="signtype-primary" class="w-screen flex col-nw justify-around">
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


	<section id="term-header">

	</section><!-- end section#signtype-header -->

	<section id="term-description">
		<h2>description of the sign type</h2>
	</section><!-- end section#signtype-description -->


	<!-- gallery of this term -->
	<section id="type-gallery">
		<h2> gallery with images of this sign type<
	</section><!-- end section#signtype-gallery -->

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
