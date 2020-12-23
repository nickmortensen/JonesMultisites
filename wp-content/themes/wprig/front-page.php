<?php
/**
 * Render your site front page, whether the front page displays the blog posts index or a static page.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();
?>


<?php
wp_rig()->print_styles( 'wp-rig-project', 'wp-rig-content', 'wp-rig-flickity' );
$fp_photos = [ 661, 662 ];
$locations = wp_rig()->get_location_ids( 75, 72 );



$bgsrcs = [];
foreach ( $fp_photos as $header_photo ) {
	$bgsrcs[] = wp_get_attachment_image_src( 662, 'medium_large' )[0];
}

$locations = wp_rig()->get_location_ids( 75 );
?>

<style>
	.contact-form-section,
	#housekeeping {
		grid-column: 1 / -1;
		display: grid;
		place-items: center;
	}

	#housekeeping {
		background-color: var(--indigo-400);
		align-items: flex-start;

	}
</style>

<section id="housekeeping">
	<h1>housekeeping</h1>

</section><!-- end section#housekeeping -->


	<main id="primary" class="site-main full-grid">


		<section style="display: none;" class="contact-form-section">
			<div class="contact-form-container">
				<?= wp_rig()->get_contact_form(); ?>
			</div>
		</section>


		<?php
		// phpcs:disable
		// while ( have_posts() ) {
		// 	the_post();

		// 	get_template_part( 'template-parts/content/entry', get_post_type() );
		// }
		// phpcs:enable
		get_template_part( 'template-parts/frontpage/form-experiment' );

		get_template_part( 'template-parts/frontpage/company-info' );
		get_template_part( 'template-parts/frontpage/project-cards' );
		?>



	</main><!-- #primary -->



<?php
get_footer();
