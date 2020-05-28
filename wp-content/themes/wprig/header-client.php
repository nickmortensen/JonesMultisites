<?php
/**
 * The header for individual client post pages
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

</head>

<?php
get_template_part( 'template-parts/header/navigation/navigation-project' );
?>

<?php
$blog        = get_current_blog_id();
wp_rig()->print_styles( 'client' );




?>







<body <?php body_class( 'w-screen ml-0 bg-blue-100' ); ?>>
<?php wp_body_open(); ?>


<div id="page" class="site">

	<header id="masthead" class="site-header">

		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>

		<div id="project-header" class="w-screen flex col-nw justify-end align-center">
			<div id="project-title">
				<h2 class="project-title"><?= the_title(); ?></h2>
			</div><!-- end div#project-title -->
		</div><!-- end div#project-header -->

	</header><!-- #masthead -->




</div><!-- end div#page -->

<section>
<h2>CLIENT POSTTYPE</h2>
</section>

