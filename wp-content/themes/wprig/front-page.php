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
?>
<style>

pre {
	display: block;
	min-width: 100vw;
	background: #0273b9;
	color: #fff;
	padding-left: 10vw;
}
.location-header{
	position: absolute;
	top: 0;
	left: 0;
	width: 100vw;
	display: flex;
	justify-content: space-around;
	align-items: space-around;
	position: relative;
	top: -14vw;
	min-height: 59vw;
	overflow-y: hidden;
	background-repeat: no-repeat;
	background-size: cover;
	background-position: 60%;
	padding: 120px 50px 170px;
	background-image: linear-gradient(rgba(40, 80, 120, 0.8), rgba(2, 155, 185, 0.9)),url('<?= wp_rig()->get_city_image_url(); ?>');
	background-blend-mode: <?= $headerblend; ?>;
	background-size: cover;
	min-height: 40vw;
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
.checkout dl {
	color: var(--color-theme-white);
}

.location-header .bigtext {
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
small.dev-only {
	padding-top: 1vw;
	display: block;
	font-size: 1.6rem;
	color: var(--color-theme-white);
}
</style>
<?php

$tax_locations = wp_rig()->get_location_taxonomy();
$location      = wp_rig()->get_location_info_by_id( get_current_blog_id() );
$term_id       = $location['term_id'];
$city          = 'national' === $location['location'] ? 'Co.'         : strtoupper( $location['location'] );
$blendmodes    = wp_rig()->get_blend_modes();
$textblend     = 2 !==  get_current_blog_id() ? $blendmodes[3]: $blendmodes[8];
$headerblend   = 2 !==  get_current_blog_id() ? $blendmodes[1]: $blendmodes[9];
$headertext    = 2 !==  get_current_blog_id() ? 'var(--gray-900)' : 'var(--indigo-300)';
$tax_locations = get_terms( array(
	'taxonomy'   => 'location',
	'hide_empty' => false,
) );
$sites = get_sites();



// print_r( $tax_locations );

?>


<?php
// Use grid layout if blog index is displayed.
if ( is_home() ) {
	wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-front-page' );
} else {
	wp_rig()->print_styles( 'wp-rig-content' );
}

?>




<section
 class="location-header"
>
	<div class="checkout">
	<!-- these are just helpers to let me know what sort of filters I am using -->
<?php if ( 'development' === ENVIRONMENT ) : ?>
<dl>
	<dt> textblend: </dt> <dd><?= $textblend; ?></dd>
	<dt> headerblend: </dt> <dd><?= $headerblend; ?></dd>
	<dt> total locations: </dt> <dd><?= count( get_sites() ); ?></dd>
	<dt>locations url: </dt> <dd> <?= $location['url']; ?> </dd>
	<dt>locations city_image: </dt> <dd> <?= $location['city_image']; ?> </dd>
	<dt>city_image_url: </dt> <dd> <?= wp_rig()->get_city_image_url(); ?> </dd>
	<dt>locations blog_id: </dt> <dd> <?= $location['blog_id']; ?> </dd>
	<dt>locations slug: </dt> <dd> <?= $location['slug']; ?> </dd>
	<dt>location: </dt> <dd> <?= $location['location']; ?> </dd>
	<dt>locations term_id: </dt> <dd> <?= $location['term_id']; ?> </dd>
</dl>
<?php endif; ?>

	</div>

	<span class="bigtext">JONES SIGN <?= $city; ?> </span>

</section>


	<main id="primary" class="site-main">
		<?php

		while ( have_posts() ) {
			the_post();

			get_template_part( 'template-parts/content/entry', get_post_type() );
		}

		get_template_part( 'template-parts/content/pagination' );
		?>


<pre>
<?php
$terms = get_terms( 'location', array(
    'hide_empty' => false,
) );
// print_r( $terms[2] );
print_r( $terms );


$terms_ids = [75,60,66,61,70,73,74,64,72,63,62,69,68,65,67];
$i = 14;
$loc = $terms_ids[$i];
$address                = get_term_meta( $loc, 'locationAddress', true );
$locationCity           = get_term_meta( $loc, 'locationCity', true );
$locationState          = get_term_meta( $loc, 'locationState', true );
$locationPhone          = get_term_meta( $loc, 'locationPhone', true );
$locationZip            = get_term_meta( $loc, 'locationZip', true );
$locationLatitude       = get_term_meta( $loc, 'locationLatitude', true );
$locationLongitude      = get_term_meta( $loc, 'locationLongitude', true );
$locationGoogleCID      = get_term_meta( $loc, 'locationGoogleCID', true );
$locations['address']   = $address ?? '';
$locations['city']      = $locationCity ?? '';
$locations['state']     = $locationState ?? '';
$locations['zip']       = $locationZip ?? '';
$locations['phone']     = $locationPhone ?? '';
$locations['fax']       = '111-111-1111';
$locations['email']     = 'info@jonessign.com';
$locations['latitude']     = $locationLatitude ?? '';
$locations['longitude']     = $locationLongitude ?? '';
$locations['googleCID'] = $locationGoogleCID ?? '';
$data[] = $locations;
$output = 'SELECT * FROM `jco_termmeta` WHERE `term_id` = ' . $loc . ';';
$output .= "\n";
$output .= 'UPDATE ';
$output .= '`jco_termmeta`';
$output .= "\n\t";
$output .= 'SET `meta_value` = \'';
// $output .= serialize( $data);
$output .= serialize( $locations);
$output .= '\' ';
$output .= "\n\t\t";
$output .= 'WHERE `meta_key` = "locationAddress" AND `term_id` = ' . $loc . ';';
echo $output;
echo "\n";
echo 'SELECT * FROM `jco_termmeta` WHERE `term_id` = ' . $loc . ';';


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
</pre>
	</main><!-- #primary -->

<?php
get_footer();
