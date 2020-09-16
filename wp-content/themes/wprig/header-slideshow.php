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

	<?php
	$blog      = get_current_blog_id();
	$posttype = get_post_type();
	?>
</head>






<body <?php body_class( 'w-screen ml-0 bg-blue-100' ); ?>>
<?php wp_body_open(); ?>
<style type="text/css">
	:root {
		--header-blend-mode: <?= $headerblend; ?>;
		--textblend: <?= $textblend; ?>;
		--textblend: screen;
		--header-text-color: <?= $headertext; ?>;
	}
	#masthead {
		background-image: linear-gradient(rgba(40, 80, 120, 0.8), rgba(2, 155, 185, 0.9)), url('<?= $bg_image; ?>');
		background-blend-mode: var(--header-blend-mode);
		background-size: cover;
	}
	.location-header{
	}

	.location-header .bigtext {
		color: var(--header-text-color);
		mix-blend-mode: var(--textblend);
	}

	#page-topper {
		min-height: 18vh;
		min-width: 100vw;
		border-bottom: 4px solid var(--color-theme-white);
		display: flex;
		flex-flow: row nowrap;
	}
	#page-topper > div {
		border: 2px solid #fff;
		min-width: 20vw;
		padding: 1vw;
	}

</style>
<div id="page" class="site">




	<!-- #masthead -->
	<header id="masthead" class="site-header">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>

		<section id="page-topper">
			<?php get_template_part( 'template-parts/header/branding' ); ?>
			<?php get_template_part( 'template-parts/header/custom_header' ); ?>
			<?php get_template_part( 'template-parts/header/navigation' ); ?>
		</section>







<?php if ( is_front_page() ) : ?>
	<section class="location-header">
			<div class="checkout"></div>
			<span class="bigtext">JONES <?= $common_name; ?> </span>
		</section>

	<section class="flex justify-center align-middle">
		<h1> This is the front page!! </h1>
	</section>
<?php endif; ?>

</header>



