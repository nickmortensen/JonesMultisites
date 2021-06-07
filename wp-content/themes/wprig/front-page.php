<?php
/**
 * Render your site front page, whether the front page displays the blog posts index or a static page.
 * Last Update 29_April_2021.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();

// As this is the front page, load in the front page stylesheet.
wp_rig()->print_styles( 'wp-rig-front-page' );


$template_args = [
	'template'     => 'frontpage',
	'requested_by' => 'frontpage',
];

?>

<section class="content-wrapper">
	<!-- page loading feature for when we are using ajax to get new content -->
	<div class="load-indicator"></div>

	<section class="content-blocks">
		<div class="block-content block-left header masthead">
<?php

/**
 * Header function is defined in the JonesSign Component beginning on line 1564.
 * Default args as follow:
 *
 * [
 *    'vertical_image_id'   => 809,
 *    'horizontal_image_id' => 659,
 *    'cta_headline' => 'We do the Work',
 *    'cta_nextline' => 'Our Clients do our Advertising.',
 *    'button' => [
 *       'url'      => '#',
 *       'frontext' => 'Learn More',
 *       'backtext' => 'Continue',
 *     ],
 * ];
 */
$header_arguments = [];


/* navigation bar which stays affixed to top on scroll and moves to the left of the page on screens larger thant 1400px */
	echo wp_rig()->get_masthead( $header_arguments );
?>
		</div><!-- end .block-content.block-left -->

		<div class="block-content block-right">

			<main>
				<?php get_template_part( 'template-parts/frontpage/company', 'aspects', $template_args ); ?>
				<?php get_template_part( 'template-parts/frontpage/contact', 'us', $template_args ); ?>
				<?php get_template_part( 'template-parts/frontpage/projects-display', 'cards', $template_args ); ?>
				<?php //get_template_part( 'template-parts/frontpage/projects-display', 'grid', $template_args ); ?>
			</main>

		</div><!-- end .block-content.block-right -->

		<div class="block-content block-footer">
			<footer>
				<div class="section-title">
				<h4><?= esc_html( get_bloginfo( 'description', 'display' ) ); ?> </h4>
				</div>

				<div class="section-content">
				<span class="about_us"><?= ABOUT_US; ?></span>
				</div><!-- end div#company-info -->
			</footer><!-- #colophon -->
		</div><!-- end div.block-content block-footer -->

	</section><!-- end section.content-blocks -->

	<?php get_footer(); ?>

</section><!-- end section.content-wrapper -->

<?php wp_footer(); ?>
</body>
</html>

