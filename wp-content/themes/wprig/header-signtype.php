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
$signtype     = wp_rig()->get_all_info( get_queried_object()->term_id );
$short_desc   = ( substr( $signtype['description'], -1 ) === '.' ) ? $signtype['description'] : $signtype['description'] . '.';
$long_desc    = ( substr( $signtype['indepth'], -1 ) === '.' ) ? $signtype['indepth'] : $signtype['description'] . '.';
$uses         = wp_rig()->get_all_info( get_queried_object()->term_id )['uses'];
$uses         = $signtype['uses'];
$backgrounds  = wp_rig()->get_header_backgrounds( get_queried_object()->term_id );
$horizontal   = wp_get_attachment_image_src( $backgrounds['horizontal'], 'rectangular-mid' )[0];
$vertical     = wp_get_attachment_image_src( $backgrounds['vertical'], 'vertical-mid' )[0];
$cinematic    = wp_get_attachment_image_src( $backgrounds['cinematic'], 'cinematic-mid' )[0];
?>




<body <?php body_class( 'w-screen ml-0 bg-blue-100' ); ?>>

<style>

#masthead {
	padding-bottom: unset;
	position: relative;
	width: 100vw;
	height: 60vw;
	color: var(--color-theme-white);
	display: flex;
	flex-flow: column nowrap;
}

#masthead > div.masthead {
	z-index: 20;
	position: absolute;
	top: 0;
	left: 0;
	display: flex;
	min-width: 100vw;
	height: 100%;
	background: url(<?= $cinematic; ?>) 10% 40% / cover no-repeat, linear-gradient( -90deg, rgba(157, 31, 90, 0.45) 30%, rgba(98, 31, 157, 0.65) 65% );
	background-blend-mode: exclusion;
	background-size: cover;
}

#masthead > div.masthead.wide {
	visibility: hidden;

}

@media all and ( min-width: 1299px ) {
.wide {
	display: flex;
	flex-flow: row nowrap;
	justify: end;
	min-width: unset;
	width: 100%;

}
.wide_image {
	width: 80vmax;
	max-width: 900px;
	min-height: 800px;
	height: 100%;
	background: url(<?= $vertical; ?>) 10% 40% / cover no-repeat, linear-gradient( -90deg, rgba(157, 31, 90, 0.45) 30%, rgba(98, 31, 157, 0.65) 65% );
	background-blend-mode: hard-light;
}

.wide_info {
	color: var(--gray-600);
	padding: 4vmin;
}

.wide_info p {
	font-size: clamp(24px, 2.3rem, 60px);
}
.wide_info h1 {
	font-size: clamp(34px, 4rem, 80px);

}

	#masthead > div.masthead {
		visibility: hidden;
	}



}


</style>


<?php wp_body_open(); ?>

<?php

/**
 * Output a list of uses with periods at the end.
 *
 * @param $array $uses The usage scenarios for this sign type.
 *
 *
 */
function uses_list( $uses ) {
	$output = '';
	$list   = [];
	if ( $uses ) {
		foreach ( $uses as $key => $use ) {
			$use    = substr( $use, -1 ) === '.' ? $use : $use . '.';
			$list[] = "<li class=\"header\">$use</li>";
		}
		$output  = '<ul style="list-style-type: circle;" class="ml-16 pt-2">';
		$output .= implode( '', $list );
		$output .= '</ul>';
	}
	return $output;
}

?>

<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'wp-rig' ); ?></a>
<div id="page" class="site">

	<header id="masthead" class="site-header">
		<div class="masthead">
			<div>
				<h1> <?= ucwords( get_queried_object()->name ); ?> </h1>
				<p><?= $short_desc ?></p>
				<div class="text-4xl mt-6 border-white"><?= uses_list( $uses ); ?> </div>
			</div>
		</div>

		<div class="wide">
			<div class="wide_image">

			</div>
			<div class="wide_info">
			<h1> <?= ucwords( get_queried_object()->name ); ?> </h1>
				<p><?= $short_desc ?></p>
				<div class="text-4xl"><?= uses_list( $uses ); ?> </div>
			</div>
		</div>

	</header>

		<div class="only-on-large">
			<h2>This should be here</h2>
		</div>



	<pre>
		<?php print_r( get_queried_object() ); ?>
		<?php print_r( get_body_class() ); ?>
		<?php print_r( $backgrounds ); ?>
		<?php print_r( $horizontal ); ?>
		<?php print_r( $vertical ); ?>
		<?php print_r( $cinematic ); ?>
	</pre>


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
