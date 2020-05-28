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

$termid = get_queried_object()->term_id;
wp_rig()->print_styles( 'wp-rig-content' );
$body = get_body_class();

?>

<section class="flex col-nw justify-center align-center">
	<div id="signtype-in-depth" class="w-3/5 p-6">
		<?= wp_rig()->get_signtype_indepth( get_queried_object()->term_id ); ?>
	</div>
</section>

<section id="signtype-links" class="flex-row nw justify-between align-center">
	<pre>
		<?php print_r( get_queried_object() ); ?>
	</pre>
<a title="link to the previous sign type" href=""></a>
<a title="link to the next sign type" href=""></a>

</section><!-- end section#signtype-links -->
<div id="signtype-primary" class="w-screen flex col-nw justify-around">
<style>
	div#signtype-primary {
		height: 1900px;
	}
	section[id^="signtype"] {
		background: var(--orange-200);
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


<section id="signtype-header">

</section><!-- end section#signtype-header -->

<section id="signtype-description">
	<h2>description of the sign type</h2>
</section><!-- end section#signtype-description -->

<section id="signtype-gallery"> <div>images of this sign type</div> </section><!-- end section#signtype-gallery -->

<section id="signtype-project-links"></section><!-- end section#signtype-project-links -->

</div><!-- end div.signtype-primary -->

<h2>taxonomy signtype page first edit</h2>
	<main id="primary" class="site-main">
		<?php
		if ( have_posts() ) {

			get_template_part( 'template-parts/content/page_header' );

			while ( have_posts() ) {
				the_post();

				get_template_part( 'template-parts/content/entry', get_post_type() );
			}

			if ( ! is_singular() ) {
				get_template_part( 'template-parts/content/pagination' );
			}
		} else {
			get_template_part( 'template-parts/content/error' );
		}
		?>
	</main><!-- #primary -->
<?php
get_sidebar();
get_footer();
