<?php
/**
 * All projects will go on this page
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 * @link https://codepen.io/julesforrest/pen/QBzaQR
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

get_header();

wp_rig()->print_styles( 'wp-rig-content', 'wp-rig-project' );

$projects = wp_rig()->get_recent_project_ids( 12 );


?>
	<main id="projects" class="all_projects">


<div class="container">

<?php

foreach ( $projects as $key => $project_id ) {
	echo wp_rig()->get_secondary_project_card( $project_id );
}

?>

	<div class="card">
		<img src="https://images.unsplash.com/photo-1533658925625-2f94d23fc425?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=894c97adfd68510170db9ae89704c92e&auto=format&fit=crop&w=800&q=60" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>

	<div class="card">
		<img src="https://images.unsplash.com/photo-1494122353634-c310f45a6d3c?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=ecd4e63ddbfbc55465d3d0abc16ec5c5&auto=format&fit=crop&w=2850&q=80" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>

	<div class="card">
		<img src="https://images.unsplash.com/photo-1503867913710-3f1656e94ac9?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=4ec81722964d9d4b89884c82bcb11642&auto=format&fit=crop&w=1027&q=80" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1533756972958-d6f38a9761e3?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=d710493bc1818842db8ce47f887708f2&auto=format&fit=crop&w=2089&q=80" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1502957291543-d85480254bf8?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=220d7cdb8357ec667ed2c4d4a3de3eb0&auto=format&fit=crop&w=1567&q=80" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1533770699395-5761e5d08106?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=363f39b71ee57e936da6f04a7830895f&auto=format&fit=crop&w=1949&q=80" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1533806481099-93f1242aea1e?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=0f2ba53e0ed1f9ecd16acb04ad0f70f7&auto=format&fit=crop&w=1950&q=80" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1533738630286-f1f4a61705f8?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=5e2bc6bb33302df3a49d62983fe535ef&auto=format&fit=crop&w=800&q=60" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1533762385849-5aa14c83dbaf?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=040eec84d37ae33312c1bda4d0dfb0f6&auto=format&fit=crop&w=800&q=60" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1533754434873-c60f30acf0ca?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=7fd34022d3c026a5cdebba00b4fa89a4&auto=format&fit=crop&w=800&q=60" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/photo-1533695355243-80ed280a24c1?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=f3b50d474b326a034ccf751b3355704a&auto=format&fit=crop&w=800&q=60" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>
	<div class="card">
		<img src="https://images.unsplash.com/uploads/1413259835094dcdeb9d3/6e609595?ixlib=rb-0.3.5&ixid=eyJhcHBfaWQiOjEyMDd9&s=c82fb4e7054d9fb4211938bf9431ccdf&auto=format&fit=crop&w=1952&q=80" />
		<h3>Card title</h3>
		<p>Card description</p>
	</div>


</div><!-- end div.container -->




	</main><!-- #projects.all_projects -->
<?php

get_footer();
