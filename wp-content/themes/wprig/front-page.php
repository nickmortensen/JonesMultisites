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
$blog_identifier = get_current_blog_id();

// Use grid layout if blog index is displayed.
if ( is_home() ) {
	wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-front-page' );
} else {
	wp_rig()->print_styles( 'wp-rig-content' );
}
?>
<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/content/entry', get_post_type() );
	}
	get_template_part( 'template-parts/content/pagination' );
	?>
</main><!-- #primary -->

<pre>
<?php

$args = [
	'post_type'      => 'project',
	'posts_per_page' => -1,
	'orderby'        => 'title',
	'order'          => 'ASC',
];


$projects  = wp_rig()->get_project_details();
$id_labels = [];
$total     = count( $projects );
for ( $i = 0; $i < $total; $i++ ) {
	$project = explode( ' ', $projects[ $i ]->post_title, 3 )[0];
	if ( isset( explode( ' ', $projects[ $i ]->post_title, 3 )[1] ) ) {
		$project .= ' ' . explode( ' ', $projects[ $i ]->post_title, 3 )[1];
	}
	$id_labels[] = $project;

}

?>
</pre>

<!--
<style>

	#select-container {
		min-height: 600px;
	}

	select#project-select {
		display: block;
		font-family: sans-serif;
		font-weight: var(--superbold);
		color: #444;
		line-height: 1.3 !important;
		padding: .6em 1.4em .5em .8em;
		width: 50%;
		max-width: 800px;
		box-sizing: border-box;
		margin: 0;
		border: 1px solid #aaa;
		box-shadow: 0 1px 0 1px rgba(0,0,0,.04);
		border-radius: .5em;
		-moz-appearance: none;
		-webkit-appearance: none;
		appearance: none;
		background-color: #fff;
		background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23007CB2%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'),
		linear-gradient(to bottom, #ffffff 0%,#e5e5e5 100%);
		background-repeat: no-repeat, repeat;
		background-position: right .7em top 50%, 0 0;
		background-size: .65em auto, 100%;
	}

	#project-select::-ms-expand {
		display: none;
	}

	select#project-select[multiple]:hover {
		background-color: #0273b9;
	}

	select#project-select[multiple] {
		height: 2.6rem;
		transition: height 0.61s cubic-bezier(0.51, 1.03, 0.75, 1.19);
	}

	select#project-select[multiple]:focus {
		height: 20vmax;
		min-height: 6rem;
		border-color: #aaa;
		box-shadow: 0 0 1px 3px rgba(59, 153, 252, .7);
		box-shadow: 0 0 0 3px -moz-mac-focusring;
		color: #222;
		outline: none;

	}

	select#project-select > option {
		font-weight: var(--superbold);
	}

	select#project-select > option:not(:first-of-type) {
		margin-top: 0.4rem;

	}
</style> -->
<section id="select-container" class="w-100">

<?= wp_rig()->personal_finances_form(); ?>

</section>

<?php
get_footer();

/**
 * LCA ARENA PHOTO https://commons.wikimedia.org/wiki/User:Adam_Bishop
 * evening
 * https://commons.wikimedia.org/wiki/User:Michael_Barera
 */
