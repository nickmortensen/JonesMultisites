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

$term = 5;
$term = wp_rig()->get_all_info( $term );
echo '<pre>';
print_r( $term );
echo '</pre>';
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


<?php


//phpcs:disable
// $info = [];
// foreach( $locations as $location ) {

// 	$info['gtid']              = $term_id;
// 	$info['id']                = $location->term_id;
// 	$info['name']              = $location->name;
// 	$info['slug']              = $location->slug;
// 	$info['tax_id']            = $location->term_taxonomy_id;
// 	$info['description']       = $location->description;
// 	$info['location_image_id'] = get_term_meta( $info['id'], 'locationImage_id', true ) ?? 55;
// 	$info['city_image_id']     = get_term_meta( $info['id'], 'cityImage_id', true ) ?? 55;
// 	$info['blog_id']           = get_term_meta( $info['id'], 'locationBlogID', true );
// 	$info['subdomain']         = preg_replace( '/^http:/i', 'https:', get_term_meta( $info['id'], 'subdomainURL', true ) );
// 	$info['nimble']            = preg_replace( '/^http:/i', 'https:', get_term_meta( $info['id'], 'locationURL', true ) );
// 	$info['address']           = get_term_meta( $info['id'], 'jonesLocationInfo', true );
// 	$info['capabilities']      = get_term_meta( $info['id'], 'locationCapabilities', true );

// }



// $i = 14;
// $loc = $terms_ids[$i];
// $address                = get_term_meta( $loc, 'locationAddress', true );
// $locationCity           = get_term_meta( $loc, 'locationCity', true );
// $locationState          = get_term_meta( $loc, 'locationState', true );
// $locationPhone          = get_term_meta( $loc, 'locationPhone', true );
// $locationZip            = get_term_meta( $loc, 'locationZip', true );
// $locationLatitude       = get_term_meta( $loc, 'locationLatitude', true );
// $locationLongitude      = get_term_meta( $loc, 'locationLongitude', true );
// $locationGoogleCID      = get_term_meta( $loc, 'locationGoogleCID', true );
// $locations['address']   = $address ?? '';
// $locations['city']      = $locationCity ?? '';
// $locations['state']     = $locationState ?? '';
// $locations['zip']       = $locationZip ?? '';
// $locations['phone']     = $locationPhone ?? '';
// $locations['fax']       = '111-111-1111';
// $locations['email']     = 'info@jonessign.com';
// $locations['latitude']     = $locationLatitude ?? '';
// $locations['longitude']     = $locationLongitude ?? '';
// $locations['googleCID'] = $locationGoogleCID ?? '';
// $data[] = $locations;
// $output = 'SELECT * FROM `jco_termmeta` WHERE `term_id` = ' . $loc . ';';
// $output .= "\n";
// $output .= 'UPDATE ';
// $output .= '`jco_termmeta`';
// $output .= "\n\t";
// $output .= 'SET `meta_value` = \'';
// // $output .= serialize( $data);
// $output .= serialize( $locations);
// $output .= '\' ';
// $output .= "\n\t\t";
// $output .= 'WHERE `meta_key` = "locationAddress" AND `term_id` = ' . $loc . ';';
// echo $output;
// echo "\n";
// echo 'SELECT * FROM `jco_termmeta` WHERE `term_id` = ' . $loc . ';';


// echo '<br>';
// print_r(get_declared_classes() );
// echo '<br>';
// echo dirname( __FILE__, 1 );
// echo '<br>';
// $plugins_url = plugins_url();
// echo $plugins_url;
// echo '<br>';
// if (class_exists('Jones_Multisite_Deactivator')) {
// 	echo 'projecttwocalss exists';
// } else {
// 	echo 'class does not exist';
// }

// print_r(get_declared_classes());
// print_r( $sites );
?>
	</main><!-- #primary -->

<?php
get_footer();
