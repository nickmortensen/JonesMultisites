<?php
/**
 * Template part for displaying an icon that floats on top of the page and keeps me abreat of what the width is.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

wp_rig()->print_styles( 'wp-rig-developer' );
?>

<section id="developer" class="development-only">


<pre>

<?php
global $post;
global $page;
global $wp_query;

$projects_archive = get_post_type_archive_link( 'project' );
$taxid = 82;
// $projects = (int) get_post_meta( $item, 'clientProjects', true )[0];
// print_r( wp_rig()->get_backend_link( 733 ) );
// print_r($_SERVER['HTTP_USER_AGENT'] );
// print_r( $projects_archive );




print_r( get_queried_object() );
// print_r( get_post_meta( 53, 'projectLocation', true ) );

if ( in_array( 'single', get_body_class(), true ) ) {
	echo 'single YES';
}

?>


</pre>

</section>


<script type="text/javascript">

const devToggleContainer = document.querySelector( ".navigation-icons > #moreToggleContainer" );


/**
 * If the sidebar is open, close it before opening up the development panel.
 */
devToggleContainer.addEventListener( 'click', function( e ) {
	let d = document.documentElement;
	e.preventDefault();
	d.classList.contains( 'sidebar-open' ) ? d.classList.replace( 'sidebar-open', 'dev-open' ) : d.classList.toggle( 'dev-open' );


	console.log('DEV TOGGLE CLICKED');
});

</script>
