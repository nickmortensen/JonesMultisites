<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php
	if ( ! wp_rig()->is_amp() ) {
		?>
		<script>document.documentElement.classList.remove( 'no-js' );</script>
		<?php
	}
	?>

	<?php wp_head(); ?>
	<?php $blog = get_current_blog_id(); ?>
</head>



<?php
global $wp_query;
$term = get_queried_object();
$term = wp_rig()->get_all_info( get_queried_object()->term_id );
$short_desc = ( substr( $term['description'], -1 ) === '.') ? $term['description'] : $term['description'] . '.';
$long_desc = ( substr( $term['indepth'], -1 ) === '.') ? $term['indepth'] : $term['description'] . '.';

$uses = wp_rig()->get_all_info( get_queried_object()->term_id )['uses'];
$uses = $term['uses'];
$header_image = wp_rig()->get_sixteen_by_nine( $term['term_id'], false );
?>




<body <?php body_class( 'w-screen ml-0 bg-blue-100' ); ?>>

<style>
section.pre {
	min-width: 100vw;
	min-height: 25vw;
	background: var(--yellow-700);
}


#masthead {
	background: url(<?= $header_image; ?>), linear-gradient( -45deg, rgba(0, 0, 0, 0.15) 30%, rgba(0, 0, 0, 0.85) 65% );
	background-blend-mode: multiply;
	background-size: cover;
	background-repeat: no-repeat;
	min-height: 60vw;
}
#masthead > div:first-of-type {
	padding: 6vw;
	max-width: 60vw;
	/* background: rgba(255,255,255,0.6);
	backdrop-filter: drop-shadow(4px 4px 10px blue); */
}

#masthead div:first-of-type h1 {
	font: var(--highlight-font-family);
	font-size: calc( var(--global-font-size) * 0.4vw );
	font-weight: var(--extrabold);
	color: var(--gray-300);
	/* mix-blend-mode: difference; */
}


</style>


<?php wp_body_open(); ?>

<div id="page" class="site">

	<header id="masthead" class="text-white site-header flex col-nw justify-end align-start">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>

		<div>
			<h1> <?= get_queried_object()->name; ?> </h1>
			<span class=" text-3xl"><?= $short_desc ?></span>
			<div class="text-4xl mt-6 border-white"> Uses: </div>
			<ul style="list-style-type: circle;" class="ml-16 pt-2">
			<?php
			for ( $i=0; $i < count( $uses ); $i++ ) {
				$use = ( substr( $uses[$i], -1 ) === '.' ) ? $uses[$i] : $uses[$i] . '.';
				echo "<li class='pt-4 text-2xl'>$use</li>";
			}
			?>
			</ul>
		</div>


	</header>




		<?php

//phpcs:disable

		// get_template_part( 'template-parts/header/custom_header' );
		// get_template_part( 'template-parts/header/branding' );
		// get_template_part( 'template-parts/header/navigation' );

		?>



<section class="pre">
<pre>


</pre>
</section>
