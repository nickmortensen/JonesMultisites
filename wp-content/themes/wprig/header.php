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


	<?php if ( ! wp_rig()->is_amp() ) : ?>
		<script>document.documentElement.classList.remove( 'no-js' );</script>
	<?php endif; ?>
	<?php wp_head(); ?>
</head>

<?php
global $post;
global $post_type;
$header_arguments = [];
$request_from = '';

$preloads                        = [ 'wp-rig-content' ];
$body_classes                    = [];
$template_args                   = [];
$template_args['is_home']        = false;

if ( is_home() && is_front_page() ) {
	$template_args['is_home'] = ! $template_args['is_home'];
}

switch ( $post_type ) {
	case 'project':
		$preloads[]       = 'wp-rig-project';
		$post_information = wp_rig()->get_definitive_project_info( $post->ID );
		// $header_arguments['vertical_image_id'] =
		break;
	default:
		$preloads = $preloads;
}


foreach ( $preloads as $preload ) {
	wp_rig()->print_styles( $preload );
}


?>
<body id="ajaxified-body" <?php body_class( $body_classes ); ?>>

<?php wp_body_open(); ?>

<?php get_template_part( 'template-parts/sidebar/sidebar' ); ?>


<section class="off-canvas"> </section>
<?php
/**
 * DEVELOPER ONLY
 */
if ( 'development' !== ENVIRONMENT ) {
	get_template_part( 'template-parts/developer/developer', '', $template_args );
}








