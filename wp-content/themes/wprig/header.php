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
	<?php $blog  = get_current_blog_id(); ?>
</head>



<?php
$blendmode   = wp_rig()->get_blend_modes();
$headerblend = 2 !== get_current_blog_id() ? $blendmode[4] : $blendmode[9];
$textblend   = 2 !== get_current_blog_id() ? $blendmode[3] : $blendmode[8];
$headertext  = 2 !== get_current_blog_id() ? 'var(--gray-900)' : 'var(--indigo-300)';
$sites       = get_sites();
$blog        = get_current_blog_id();
$location_id = wp_rig()->get_term_by_blog( $blog );
$term_id     = wp_rig()->get_term_by_blog( 1 );
$city_image  = get_term_meta( $term_id, 'locationImage', true );
$common_name = wp_rig()->get_location_name( $term_id );
$info        = wp_rig()->get_location_info( $location_id );

$capability = wp_rig()->get_location_capability( $term_id );
global $wpdb;

?>

<style>
	.location-header{
		display: flex;
		justify-content: space-around;
		align-items: space-around;
		position: relative;
		top: 0;
		left: 0;
		width: 100vw;
		min-height: 70vh;
		overflow-y: hidden;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: 60%;
		margin-top: 0;
		padding: 120px 50px 170px;
		background-image: linear-gradient(rgba(40, 80, 120, 0.8), rgba(2, 155, 185, 0.9)), url('<?= $city_image; ?>');
		background-blend-mode: <?= $headerblend; ?>;
		background-size: cover;
		min-width: 100%;
	}

	.checkout {
		position: absolute;
		padding-left: 4vw;
		top: 0;
		right: 0;
		min-width:100%;
		min-height: 100%;
		background: rgba(2,115,185,0.4);
		backdrop-filter: blur(2px) hue-rotate(60%);
	}

	.location-header .bigtext {
		text-transform: uppercase;
		max-width: 40vw;
		font-size: 12rem;
		font-weight: 900;
		color: <?= $headertext; ?>;
		padding: 0;
		margin: 0;
		line-height: 1.08;
		mix-blend-mode: <?= $textblend; ?>;
		-webkit-mix-blend-mode: <?= $textblend; ?>;
		/* mix-blend-mode: overlay; */
	}

</style>


<body <?php body_class( 'w-screen ml-0 bg-blue-100' ); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

	<header id="masthead" class="site-header">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>


		<!-- #masthead -->

		<section class="location-header">
			<div class="checkout"></div>
			<span class="bigtext">JONES <?= $common_name; ?> </span>
		</section>

	</header>


<?php if ( is_front_page() ) : ?>
	<section class="flex justify-center align-middle">
		<h1> This is the front page!! </h1>
	</section>
<?php endif; ?>


<h2>Capability: <?= count( $capability ); ?></h2>

		<?php

		echo '<pre>';
		print_r( wp_rig()->get_structured_project_address(66) );
		echo '</pre>';
		//phpcs:disable
		// get_template_part( 'template-parts/header/custom_header' );
		// get_template_part( 'template-parts/header/branding' );
		// get_template_part( 'template-parts/header/navigation' );

		?>

<div itemprop="location" itemscope itemtype="http://schema.org/Place">
	<div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
		<span itemprop="addressLocality">Philadelphia</span>,
		<span itemprop="addressRegion">PA</span>
	</div>
</div>

