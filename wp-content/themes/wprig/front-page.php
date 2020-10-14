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

<style>

	.item9 {
		grid-column: span 2
	}
</style>


<?php
$fp_photos = [ 661, 662 ];
?>
	<section class="item frontpage-masthead"> masthead</section>

	<main class="item frontpage-main"> main</main>


	<!-- project cards -->
	<section class="item frontpage-projects">
		<h2 class="section-title">Recent Projects</h2>
		<div>
			<?php
				$project_ids = wp_rig()->get_recent_project_ids( 2 );

				foreach ( $project_ids as $project ) {
					echo wp_rig()->get_project_card( $project );
				}
			?>
		</div><!-- end div#fp-project-cards -->
	</section>

	<div class="item item3"> item # 3</div>
	<div class="item item4"> item # 4</div>
	<div class="item item5"> item # 5</div>



	<footer class="item footer"> The footer</footer>

</div><!-- end div#page -->

</body>
</html>
