<?php
// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase, Squiz.Commenting.InlineComment.InvalidEndChar
//phpcs:disable WordPress.Arrays.CommaAfterArrayItem.CommaAfterLast, PHPCompatibility.Lists.NewShortList.Found, PHPCompatibility.Lists.NewKeyedList.Found
/**
 * The template for displaying all pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();
wp_rig()->print_styles( 'wp-rig-content' );

$images = [];
$open   = wp_rig()->open_glide();
$close  = wp_rig()->close_glide();

?>


<?php

$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3ehfSk6nYvY4BIe530xc8HqXzLJRlzvrMv_QtsRNP5gG_k2EAztg0dOH99Kni6ITu3TRXAwwkoHD41kvmnT9wqCjPEUFu08iPdirkrmj6Y8YxRj36yla5G4zX_XIAUhVyJDwsWEDRDJC_Myw_-7flrM8A=w1431-h1074-no',
	'caption' => '76 - Calimesa, CA',
	'caption' => [
		'type'     => '',
		'client'   => '76',
		'location' => 'Calimesa, CA',
		'overlay'  => 'bottom',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3c3WACTEqANDvScSI9ImACj3k49zycNqjtoff6YB8XPSlgmPwI1VYk5eAhTCrwiaPcskhfDvpoXxCoIGWjo023S0buekA55wNCSFge6WjBBIjVNjSGFiLzovusB3jsM6QU6eygoX1mdnA_9ZzQ0sYCCjA=w1214-h684-no',
	'caption' => [
		'type'     => 'Digital Signage + Wayfinding',
		'client'   => 'Vertical Circulation',
		'location' => 'MSP Airport',
		'overlay'  => 'bottom',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3cRJVwrCKWDW8-FznCae9HfIciSVgacFVvUeiXQUqjxBpoOiMf-rf1O04gW38aa3OY8et1eNWZbOdbnIODpTgVX-LwCRPXlfGEUFcgL-mWXhnh-0ScCIr5--U5JnDSZtpVfRuQcHajdCGUX7UJ3PSdTCQ=w1214-h683-no',
	'caption' => [
		'type'     => 'Wayfinding Graphics',
		'client'   => 'Vertical Circulation',
		'location' => 'MSP Airport',
		'overlay'  => 'bottom',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3eQQC_b1M9Rv5K5zq3GZjTINvxU08O_F53xwwxg76q-lQDsr-p5naA1zs7dgJBhBiX2BxmX0MwqYpddlpNzCrwWzKvw-GIuBHnuRRBjXZvC9PbxvMjNb-30DACGyR4cG7_v8OsEms6JZeSPDozhZasm0w=w1071-h603-no',
	'caption' => [
		'type'     => 'Routed Aluminum',
		'client'   => 'Renaissance Hotel',
		'location' => 'Wauwatosa, WI',
		'overlay'  => 'bottom',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3cjXxfmSL9VjF6D6Xgi_tFqMm6RrCQgIjFoCV3kO3eRUXnCEyBanSdoWNVoGqYGFFIX8kR_QSyXNJVF0sH7EDPBOAS8roBAHjEOwPGUWdPiUh04-H3UXSN3zQ3Eu-IBeaR8sz6NXZNUlVn_Q5JzC1fp2w=w1071-h603-no',
	'caption' => [
		'type'     => 'Halo Lit Letters / Specialty Fabrication Segmented Wall',
		'client'   => 'Populous',
		'location' => 'New York City',
		'overlay'  => 'bottom',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3dSC89jutNJbPMFApOZ4N5F4Mdn4ojB407Hq6Ko2GiI2pQmXbg03LAgfc1FofMYZzWRLqNStkvWjP2qlDBWGRpbQi2GIHSl55U92SmtpOEggEOcL8W9MWD1MYbSPsBxMiQCutX3c-T1TIkFTZ0xRBk-XA=w1071-h603-no',
	'caption' => [
		'type'     => 'Specialty Fabrication: Entryway Overhand',
		'client'   => 'Four Seasons Hotel',
		'location' => 'Los Angeles',
		'overlay'  => 'bottom',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3dI7W9iHE1SW_6cfvdXqIiQt6DTHb6EYhm-V-dvdsWQknM3Ck-DB1D_z4WIJ35QdYbG4L8lYhSHWyXNd96nMdNu_GOcLcF9eqDrIJk5kfUKA38t3zpu6_PazJnTpIHYHJDpctGyjGBQ7oJ9TcM1q8tnEA=w925-h1264-no',
	'caption' => [
		'type'     => 'Blade Sign + Digital Graphics + Screen',
		'client'   => 'Hope + Flower',
		'location' => 'Los Angeles',
		'overlay'  => 'left',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3c38KOqgMs6lMqJE0ZqmVyZcyYSEp74dX87jMOF2Yg41dpGNhvGownO-MmbFBVi09SrcrdVXFSyLoItImyeYCuENPbvGvTMvzoNyrzJTCYiP1LCxohmc3ElF7cTLBvyKJqpFFMjG7-el0_eGhW0odeeOQ=w1071-h521-no',
	'caption' => [
		'type'     => 'Lit Letters + Canopy',
		'client'   => 'Dollar Tree',
		'location' => 'Green Bay',
		'overlay'  => 'right',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3e0iks3Fc6ESw9AsPgW9wB_OTfFu9DJ1g6pf5X6AaSV88JrwbJty1PyI07-XGGv5b8Hf6Q6MDcwGzGAdeypc5Np1ldmhkgxd2siJza-KCQ-_3Ko8bMx6MITCCdwZdOAvTgsJFrAM_0NyfptNtqFZe57MQ=w1071-h603-no',
	'caption' => [
		'type'     => 'Exterior Sign Package',
		'client'   => 'Buffalo Wild Wings',
		'location' => 'Roseville, MN',
		'overlay'  => 'right',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3c2tkx08Z5eKQW7k8NJFuxm6NxuLDVYDDRpiXaugaSN9bcJZAvWCl5fxOGnzVU73NHtgCqrgL6fXUUNGuBoZQLZJ2iXc0g8ZCznyRYq0J7v3Oxlg3LjE21F5xnkeKmVcpSDndy1Ta4lTbmMryuHYDJszQ=w948-h1264-no',
	'caption' => [
		'type'     => 'Lit Letters',
		'client'   => "Macco's Floor Covering",
		'location' => 'Appleton, WI',
		'overlay'  => 'right',
	],
];
$images[] = [
	'source'  => 'https://lh3.googleusercontent.com/pw/ACtC-3do2NQTJ20yxWll7BfpU1rBo2uIuGGAe1MEWERnn1I3pN4S35fNIhAm23Fjw2an7D8uIeD2uF-iFfF0JXFT3Ykg1bgmr6c_FjoGNbGmiq4_N1fsxgG6lAb-tPrE1dPg_Sje6luXGo6q_GMJZgKcBRCkLg=w1071-h603-no',
	'caption' => [
		'type'     => 'Sign Package',
		'client'   => 'Denver Broncos',
		'location' => 'Empower Field',
		'overlay'  => 'left',
	],
];

/**
 * Output HTML for the client.
 *
 * @param string $image The information about the slide.
 */
function install_slide( $image ) {

	[
		'source'  => $source,
		'caption' => $caption,
	] = $image;
	[
		'type'     => $package,
		'client'   => $client,
		'location' => $location,
		'overlay'  => $overlay,
	] = $caption;
	preg_match( '/(^[^\s]+=w)(\d+)(-h)(\d+)(-no)/', $image['source'], $info ); // sets an array called $info based on the regexp;
	[ $source, , $width, , $height, ] = $info;

	$orientation = ( 1 < ( $width / $height ) ) ? 'landscape' : 'portrait';

	$output  = "\r" . str_repeat( "\t", 2 ) . '<div class="slide-entry">';
	$output .= "\r" . str_repeat( "\t", 3 ) . '<div class="card-wrapper slide-content">';
	$output .= "\r" . str_repeat( "\t", 4 ) . '<div class="client-w-image" data-orientation="' . $orientation . '">';
	$output .= "\r" . str_repeat( "\t", 5 ) . '<div class="client">';
	$output .= "\r" . str_repeat( "\t", 6 ) . '<div class="client-title">' . $client . '</div><!--/.client-title-->';
	$output .= "\r" . str_repeat( "\t", 6 ) . '<div class="client-title-shadow">' . $client . '</div><!--/.client-title-shadow-->';
	$output .= "\r" . str_repeat( "\t", 5 ) . '</div><!--/.client-->';
	$output .= "\r" . str_repeat( "\t", 5 ) . '<div class="image">';
	$output .= "\r" . str_repeat( "\t", 6 ) . '<img class="install-photo" src="' . $source . '"/>';
	$output .= "\r" . str_repeat( "\t", 5 ) . '</div><!--/.image-->';
	$output .= "\r" . str_repeat( "\t", 4 ) . '</div><!--/.client-w-image-->';
	$output .= "\r" . str_repeat( "\t", 4 ) . '<div class="details">';
	$output .= "\r" . str_repeat( "\t", 5 ) . '<div class="package">' . $package . '</div>';
	$output .= "\r" . str_repeat( "\t", 5 ) . '<div class="location">' . $location . '</div>';
	$output .= "\r" . str_repeat( "\t", 4 ) . '</div><!--/.details-->';
	$output .= "\r" . str_repeat( "\t", 3 ) . '</div><!--/.card-wrapper-->';
	$output .= "\r" . str_repeat( "\t", 2 ) . '</div><!--/.slide-entry-->';
	return $output;
}

?>



<section id="controls">
	<button id="copy_button">Copy code
	</button>
</section>


<?php
/**
 * Output Tabs and newline
 *
 * @param int $tabs Quantity of tabs I want.
 * @param int $newlines Quantity of newlines before the tabs - default is one.
 */
function newline( $tabs = 1, $newlines = 1 ) {
	return str_repeat( "\r", $newlines ) . str_repeat( "\t", $tabs );
}

/**
 * Secondary open HTML for slideshow.
 *
 * @param string $orientation The orientation of the photo.
 */
function secondary_open( $orientation = 'landscape' ) {
	$open  = newline( 2 ) . '<div class="container">';
	$open .= newline() . '<div data-orientation="' . $orientation . '" class="card-wrapper">';
	$open .= newline( 2 );
	return $open;
}
$glider       = '<div class="glider-contain">' . newline() . '<div id="glider" class="glider">' . newline( 1, 2 );
$glider_close = newline( 1, 2 ) . '</div><!--/glider-->' . newline( 0 ) . '</div><!-- /glider-contain-->';

/**
 * Secondary close HTML for slideshow.
 */
function secondary_close() {
	$close  = newline( 2 ) . '</div><!-- end div.card-wrapper -->';
	$close .= newline( 0, 2 );
	$close .= '</div><!-- end div.container -->';
	return $close;
}

/**
 * Output the next and previous icon HTML for slideshow.
 *
 * @param string $icons The icon set to utilize.
 */
function prev_next( $icons = 'material' ) {
	$slide_nav  = newline( 2 ) . '<ul class="slide-nav">';
	$slide_nav .= newline( 3 ) . '<li id="prev-slide"><span class="material-icons">fast_rewind</span></li>';
	$slide_nav .= newline( 3 ) . '<li id="next-slide"><span class="material-icons">fast_forward</span></li>';
	$slide_nav .= newline( 2 ) . '</ul><!-- end .slide-nav -->';
	$slide_nav .= newline();
	return $slide_nav;
}
/**
 * OPTIONAL Single SLIDE HTML BASED AROUND TUTORIAL.
 *
 * @param string $image The information about the slide.
 * @link https://codepen.io/CheeStudio/pen/MWgeZJw
 * @link https://cheewebdevelopment.com/boilerplate-vanilla-javascript-content-slider/
 */
function secondary_slide( $image ) {

	// Destructure the named array: $image.
	[
		'source'  => $source,
		'caption' => $caption,
	] = $image;
	[
		'type'     => $package,
		'client'   => $client,
		'location' => $location,
		'overlay'  => $overlay,
	] = $caption;
	preg_match( '/(^[^\s]+=w)(\d+)(-h)(\d+)(-no)/', $image['source'], $info ); // sets an array called $info based on the regexp;
	[ $source, , $width, , $height, ] = $info;

	$orientation = ( 1 < ( $width / $height ) ) ? 'landscape' : 'portrait';

	$output  = newline( 2, 1 ) . '<div class="container">';
	$output .= newline( 3, 2 ) . '<div data-orientation="' . $orientation . '" class="card-wrapper">';
	$output .= newline();
	$output .= newline( 4 ) . '<div class="greeting"> Greetings from </div>';
	$output .= newline( 4, 2 ) . '<div class="city">';
	$output .= newline( 6 ) . '<div class="city-title">' . strtoupper( $location ) . '</div>';
	$output .= newline( 6 ) . '<div class="city-title-shadow">' . strtoupper( $location ) . '</div>';
	$output .= newline( 4 ) . '</div><!--/city-->';
	$output .= newline( 4, 2 ) . '<div class="install-photo" style="background: url(' . $source . ') no-repeat center center/cover"></div><!--/image-->';
	$output .= newline( 4, 2 ) . '<div class="supplemental-info">';
	$output .= newline( 5 ) . '<div>' . $client . '</div><!--/client-->';
	$output .= newline( 5 ) . '<div>' . $package . '</div><!--/package-->';
	$output .= newline( 4 ) . '</div><!-- /supplemental-info-->';
	$output .= newline( 3, 2 ) . '</div><!-- end div.card-wrapper -->';
	$output .= newline( 2, 2 ) . '</div><!-- end div.container (slide) -->';

	return $output;

}//end secondary_slide()

?>

<?php
	$show_html = false;
	$slides    = [];
	foreach ( $images as $image ) {
		$output   = secondary_slide( $image );
		$slides[] = $output;
	}//end foreach.
	$output = implode( "\n", $slides );
	$output = $glider . $output . $glider_close;
	if ( ! ( $show_html ) ) {
		echo $output;
		echo '<pre id="pull-this" hidden>' . htmlspecialchars( $output ) . '</pre>';
	} else {
	// phpcs:disable WordPress.WhiteSpace.OperatorSpacing.NoSpaceAfter
	echo '<pre id="pull-this">' . htmlspecialchars( $output ) . '</pre>';
}
?>



</section><!-- end section#ipslider -->




<script>
const glider = new Glider(document.getElementById('glider'));

function sliderAuto( slider, miliseconds ) {
	const slidesCount = slider.track.childElementCount;
	let slideTimeout  = null;
	let nextIndex     = 1;

	function slide () {
		slideTimeout = setTimeout (
			function () {
				if (nextIndex >= slidesCount ) {
					nextIndex = 0;
				}
				slider.scrollItem(nextIndex++);
			},
			miliseconds
		);
	}

	slider.ele.addEventListener('glider-animated', function() {
		window.clearInterval(slideTimeout);
		slide();
	});

	slide();
}

sliderAuto( glider, 8000 );


</script>

<script>

// set clipboard text
const setClipboard = text => {
	navigator.clipboard.writeText(text).then( function() {
	console.log( 'slideshow HTML copied' );
		}, function() {
			console.log( 'text failed' );
		});
};
	// decode html entities
	const decodeHTML = html => {
		const text = document.createElement( 'textarea' );
		text.innerHTML = html;
		return text.value;
	}

	const sliderCode = document.getElementById( 'pull-this' );

	/* Click Button, Copy Slideshow HTML Code */
	const copyButton = document.getElementById( 'copy_button' );

	copyButton.addEventListener( 'click', () => {
		// const sliderCode    = document.getElementById( 'pull-this' );
		const slider        = sliderCode.innerHTML;
		const sliderDecoded = decodeHTML( slider );
		setClipboard( sliderDecoded );
	}, false );

</script>

<?php
// get_sidebar();
// get_footer();
