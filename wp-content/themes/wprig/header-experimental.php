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

	$header_imgs = [
		'https://jonessign.io/wp-content/uploads/2021/02/american_family_field-rectangular-002.jpg',
		// 'https://jonessign.io/wp-content/uploads/2021/01/2020_climate_pledge_arena_rectangular_14.jpg',
		// 'https://jonessign.io/wp-content/uploads/2021/01/2020_climate_pledge_arena_rectangular_13.jpg',
		'https://jonessign.io/wp-content/uploads/2021/01/2020_climate_pledge_arena_rectangular_06.jpg',
		'https://jonessign.io/wp-content/uploads/2020/10/chase_center_rooftop_10.jpg',
	];

	$headers = [];

	foreach ( $header_imgs as $photo ) {
		$headers[] = explode( '.', $photo, -1 );
	}
	$random_integer = random_int( 0, count( $headers ) - 1 );


	$style = <<<STY

STY;

// echo '<pre style="grid-area: header; z-index: 5;">';
// print_r($headers);

// print_r($random_integer);
// print_r( $headers[$random_integer][0] . $headers[$random_integer][1] . '-1000x750.jpg' );
// echo '</pre>';

	?>

	<?php wp_head(); ?>
</head>
<?php wp_rig()->print_styles( 'wp-rig-front-page' ); ?>
<body>

<?php wp_body_open(); ?>

<style>
	:root {
		<?php if ( 'development' === ENVIRONMENT ) : ?>
		--body-grid-template-rows: 50vmax 10vmax 8fr 3fr;
		--body-grid-template-areas:
			"header header header"
			"dev dev dev"
			"main main main"
			"footer footer footer";
		<?php endif; ?>
		--gradient-bottom: #0273b9;
		--gradient-overlay: linear-gradient(to top, var(--gradient-bottom) 4%, var(--gradient-bottom) 75% );
		--frontpage-header-image: url(<?= $headers[ $random_integer ][0] . '.' . $headers[ $random_integer ][1] . '-1000x750.jpg';?>);
		--frontpage-header-blendmode: multiply;
	}

	<?php if ( 'development' === ENVIRONMENT ) : ?>
		.developer {
			grid-area: dev;
			display: flex;
			justify-content: space-around;
			align-items: center;
			background: var(--indigo-700);
		}
	<?php endif; ?>
</style>


<?php
	// if ( 'development' === ENVIRONMENT ) {
	// 	get_template_part( 'template-parts/header/navigation' );
	// }
	?>
<!-- <?php //get_template_part( 'template-parts/header/custom_header' ); ?> -->



<header data-gridarea="header" data-idr="header" class="banner" id="main-header" role="banner">
	<?php get_template_part( 'template-parts/header/navigation' ); ?>
	<?php get_template_part( 'template-parts/header/branding' ); ?>

	<!------------------------------------------------
	------ Navigation Buttons FLOAT ------------------
	------------------------------------------------->
	<div data-idr="floating-nav-buttons" class="floating-navigation-buttons" style="--flexflow: column nowrap;">
		<a href="#frontpage-email-cta" title="Connect with us!" id="sidemenu-mail-button" class="material-icons floating-btn"> mail_outline </a>
		<a id="sidemenu-search-button" class="material-icons floating-btn" title="Search the site." > search </a>
		<a id="sidemenu-toggle-button" title="Open/Close side navigation menu" class="material-icons floating-btn"> menu </a>

		<!---------------- DEVELOPMENT ONLY NAV BUTTONS ------------------------>
		<?php
		if ( 'development' === ENVIRONMENT ) {
			wp_rig()->print_styles( 'wp-rig-developer' );
			get_template_part( 'template-parts/developer/scrolltotop' ); // Scroll to top Button Currently Does Not work.
			get_template_part( 'template-parts/developer/screenwidth' ); // shows the screenwidth at the bottom.
		}
		?>
		<!---------------- END DEVELOPMENT ONLY ------------------------>
	</div>
	<!------------------------------------------------
	------ END Navigation Buttons ------------------
	------------------------------------------------->


</header>


<!----------------------------------------------------------------------
-------- -------- DEVELOPMENT ONLY -------------------------------------
----------------  ADD DIV WITH BLEND AND COLOR OPTIONS FOR THE MAIN IMAGE ------
---------------------------------------------------------------------->
<?php if ( 'development' === ENVIRONMENT ) : ?>
	<section data-gridarea="dev" class="developer developer-only">
		<div class="blendmodes"> <?= wp_rig()->select_html( 'blendmodeOptions', 'bgBlend' ); ?> </div>
		<div class="colors"> <?= wp_rig()->select_html( 'colorOptions', 'bgColor', wp_rig()->color_options() ); ?> </div>
	</section>

	<script>
		const bgBlends = document.querySelector( '#blendmodeOptions' );
		const bgColors = document.querySelector( '#colorOptions' );

		/** Set HTML inline style property to redefine css variable to override previously declared css variable */
		function handleBlendUpdate() {
			const cssVariable = '--frontpage-header-blendmode';
			let newValue      = event.target.options[bgBlends.selectedIndex].value;
			document.documentElement.style.setProperty( cssVariable, newValue );
		}

		/** Set HTML inline style property to redefine css variable to override previously declared css variable */
		function handleColorUpdate() {
			const cssVariable = '--gradient-bottom';
			const newValue    = event.target.options[bgColors.selectedIndex].value;
			document.documentElement.style.setProperty( cssVariable, newValue );
		}

		bgBlends.addEventListener( 'change', handleBlendUpdate );
		bgColors.addEventListener( 'change', handleColorUpdate );

	</script>
<?php endif; ?>
<!----------------------------------------------------------------------
---------------- DEVELOPMENT ONLY -------------------------------------
------- END DIV WITH BLEND AND COLOR OPTIONS FOR THE MAIN IMAGE -------
---------------------------------------------------------------------->



<script>
	const emailButton = document.querySelector( '#sidemenu-mail-button' );
	/**
	 * Glide down to the section with the contact form upon pressing the email button.
	 *
	 */
	function onClickEmailButton() {
		console.log( 'this should take you to the email call to action');
		document.querySelector( '#contact-form' ).scrollIntoView( {
			behavior: 'smooth',
			block: 'center'
		} );
	}

	emailButton.addEventListener( 'click', onClickEmailButton );
</script>
