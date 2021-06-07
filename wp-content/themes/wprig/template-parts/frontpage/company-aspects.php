<?php
/**
 * Template part for displaying the aspects of the company info
 * Last Update 29_April_2021.
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;


[ 'requested_by' => $request_from ] = $args;
wp_rig()->print_styles( 'company-aspects' );
$aspects = wp_rig()->get_company_aspects();
?>

<!--

	ASPECTS IN SEPARATE PAGE

	-->
<section id="aspects" class="<?= $request_from; ?>">
	<div class="section-title"> <h4>Aspects</h4> </div>

	<div class="section-content company_aspects">
		<?php
			foreach ( $aspects as $company_aspect ) {
				echo wp_rig()->get_aspect_card( $company_aspect );
			}
		?>
	</div><!-- end div.company_aspects -->

	</section><!-- end section#aspects -->
