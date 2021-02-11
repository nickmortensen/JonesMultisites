<?php
/**
 * All projects will go on this page
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @link https://codepen.io/julesforrest/pen/QBzaQR
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();

wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-project' );

$projects = wp_rig()->get_recent_project_ids( 12 );
$ddlinks = wp_rig()->make_drilldown_links( 'industry' );

?>

<style>
.sorting-link {
	font-size: var(--fontsize-xs);
	margin: 8px;
	background: red;
	padding: 0.4vw;
	color: var(--foreground);
	text-decoration: none;
}

</style>

	<main id="projects" class="all_projects">

<div>
<?php
	foreach ( $ddlinks as $item ) {
		echo wp_sprintf( '<a class="sorting-link" href="%s">%s</a>', $item[1], ucwords( preg_replace( '/and/i', '&', $item[0] ) ) );
	}
?>

</div>



<div class="container">

<?php

foreach ( $projects as $key => $project_id ) {
	echo wp_rig()->get_secondary_project_card( $project_id );
}

?>


</div><!-- end div.container -->




	</main><!-- #projects.all_projects -->
<?php

get_footer();
