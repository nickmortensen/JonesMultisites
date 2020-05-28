<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header( 'project' );

wp_rig()->print_styles( 'wp-rig-content' );


?>




	<main id="primary">


	</main><!-- #primary -->

<?php

get_template_part( 'template-parts/content/project/entry' );
?>

<?php
get_footer();
