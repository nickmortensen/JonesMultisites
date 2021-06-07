<?php
/**
 * Template part for displaying the testimonial videos on the frontpage
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

[ 'requested_by' => $request_from ] = $args;
$testimonial_videos = [];

$testimonial_videos[] = [
	'attach_id'  => 830,
	'webm_id'    => 829,
	'vimeo_id'   => 243190424,
	'title'      => 'Daytona Rising',
	'project_id' => 64,
	'poster'     => 'https://i.vimeocdn.com/video/667263854_640.jpg',
];
$testimonial_videos[] = [
	'attach_id'  => 828,
	'webm_id'    => 827,
	'vimeo_id'   => 172429535,
	'title'      => 'Hard Rock Stadium',
	'project_id' => 53,
	'poster'     => 'https://i.vimeocdn.com/video/667264571_640.jpg',
];
$testimonial_videos[] = [
	'attach_id'  => 825,
	'webm_id'    => false,
	'vimeo_id'   => 538793954,
	'title'      => 'FC Cincinnati',
	'project_id' => 18,
	'poster'     => 826,
];
$testimonial_videos[] = [
	'attach_id'  => 832,
	'webm_id'    => 833,
	'vimeo_id'   => 538797241,
	'title'      => 'Climate Pledge Arena',
	'project_id' => 40,
	'poster'     => 824,
];
$testimonial_videos[] = [
	'vimeo_id'   => 538798375,
	'title'      => 'Renewal By Andersen ',
	'project_id' => 40,
	'poster'     => 'https://i.vimeocdn.com/video/667264571_640.jpg',
];



/**
 * Output the proper HTML for a video that is hosted on Vimeo to play.
 *
 * @param array $video Array of information about the video.
 */
function output_video_html( array $video ) {
	$customizations = [
		'autoplay' => 1,
		'color'    => '0273b9',
		'title'    => 0,
		'byline'   => 0,
		'postrait' => 0,
		'loop'     => 0,
	];
$R = '<iframe src="https://player.vimeo.com/video/252382376" width="640" height="360" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';

	[
		'vimeo_id'   => $vimeo_id,
		'title'      => $video_title,
		'project_id' => $project_id,
		'poster'     => $poster,
	]        = $video;
	$output  = '';
	$output .= <<<VIDEOTESTIMONIAL

<div class="fit-videos">
<!-- TITLE THAT IS ALSO A LINK -->
<a class="no-margin-right popup-vimeo no-margin-bottom" target="_self" href="https://player.vimeo.com/video/$video_id?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ">
	<h2 class="white-text font-weight-700" style="font-size:22px;">Hard Rock Stadium</h2>
</a>

<!-- VIDEO STILL-->
<a
class="no-margin-right popup-vimeo no-margin-bottom video-thumbnail"
="_self"
href="https://player.vimeo.com/video/$video_id?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ">
	<img class="img-thumbnail img-responsive" src="$poster" alt="hard rock stadium testimonial video thumbnail">
</a>

</div><!-- end div#fit-videos -->
VIDEOTESTIMONIAL;
	return $output;
}

/**
 * Output the proper HTML for a video that is hosted on Vimeo to play.
 *
 * @param array $video Array of information about the video.
 */
function output_video_responsively( array $video ) {
	$customizations = [
		'autoplay' => 1,
		'color'    => '0273b9',
		'title'    => 0,
		'byline'   => 0,
		'postrait' => 0,
		'loop'     => 0,
	];

	[
		'vimeo_id'   => $video_id,
		'title'      => $video_title,
		'project_id' => $project_id,
		'poster'     => $poster,
	]        = $video;
	$output  = '';
	$output .= <<<VIDEOTESTIMONIAL
	<div class='embed-container'>
		<iframe src='https://player.vimeo.com/video/$video_id?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
	</div>
VIDEOTESTIMONIAL;
	return $output;
}



?>
<section id="testimonial-videos" style="display:none;" class="<?= $request_from; ?>">
		<div class="section-title"><h4>Video Testimonials</h4></div>

<!--BEGIN VIDEO INSTRUCTIONS-->
<div class="vc-column-innner-wrapper">
	<span class="white-text text-med"> Tap Image Below for a Short Video</span>
</div><!-- end div.vc-column-innner-wrapper -->
<!--END VIDEO INSTRUCTIONS-->

<div class="fit-videos">
<!-- TITLE THAT IS ALSO A LINK                     -->
<a class="no-margin-right popup-vimeo no-margin-bottom" target="_self" href="https://player.vimeo.com/video/243190424?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ">
	<h2 class="white-text font-weight-700" style="font-size:22px;">Hard Rock Stadium</h2>
</a>

<!-- VIDEO STILL-->
<a
class="no-margin-right popup-vimeo no-margin-bottom video-thumbnail"
="_self"
href="https://player.vimeo.com/video/243190424?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ">
	<img class="img-thumbnail img-responsive" src="https://i.vimeocdn.com/video/667264571_640.jpg" alt="hard rock stadium testimonial video thumbnail">
</a>

</div><!-- end div#fit-videos -->


<!-- END HARD ROCK TESTIMONIAL -->

<!-- BEGIN DAYTONA TESTIMONIAL -->
<div class="fit-videos">

<!--  DAYTONA TESTIMONIAL TITLE THAT IS ALSO A LINK -->
<a
class="no-margin-right popup-vimeo no-margin-bottom"
target="_self"
href="https://player.vimeo.com/video/172429535?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ">
	<h2 class="white-text font-weight-700" style="font-size:22px;">Daytona International Speedway</h2>
</a>
<!-- END DAYTONA TESTIMONIAL TITLE THAT IS ALSO A LINK -->

<!-- VIDEO STILL-->
<a
class="no-margin-right popup-vimeo no-margin-bottom video-thumbnail" t
arget="_self"
href="https://player.vimeo.com/video/172429535?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ">
<img class="img-thumbnail img-responsive" src="https://i.vimeocdn.com/video/667263854_640.jpg">
</a>
</div><!-- end div#fit-videos -->

</section>


<style>
	.embed-container {
		position: relative;
		padding-bottom: 56.25%;
		height: 0;
		overflow: hidden;
		max-width: 100%;
	}
	.embed-container iframe,
	.embed-container object,
	.embed-container embed {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
	}
</style>

<section id="testimonial-videos" >
		<div class="section-title"><h4>Video Testimonials</h4></div>
<div class="section-content">


<div class='embed-container'>
<iframe src='https://player.vimeo.com/video/538793954?autoplay=1&amp;color=0273b9&amp;title=0&amp;byline=0&amp;portrait=0&amp;loop=0 ' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div>


</div>

</section>
