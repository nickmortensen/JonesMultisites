<?php
/**
 * WP_Rig\WP_Rig\Testimonials\Component class
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig\Testimonials;

use WP_Rig\WP_Rig\Component_Interface;
use WP_Rig\WP_Rig\Templating_Component_Interface;
use WP_Rig\WP_Rig\AdditionalFields\Component as AdditionalFields;
use function WP_Rig\WP_Rig\wp_rig;
use function add_action;
use function get_current_screen;
use function wp_enqueue_script;
use function get_post_meta;
use function wp_localize_script;
use function register_post_type;

/**
 * Class for improving accessibility among various core features.
 */
class Testimonials implements Component_Interface, Templating_Component_Interface {


	/**
	 * The plural of this posttype.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name The slug of this posttype..
	 */
	private $plural_name = 'clientele';

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'client';
	}

	/**
	 * The testimonials
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $client_testimonials The slug of this posttype..
	 */
	public $client_testimonials = [
		[
			'identifier'  => 18711,
			'name'        => 'Rick Sconyers',
			'testimonial' => 'Jones Sign Company blew me away with how organized & clean it was: You could eat lunch off the floor.',
			'linkedin'    => 'linkedin.com/in/rick-sconyers-aia-8249655/',
			'position'    => 'Senior Director of Design & Memorabilia',
		],
		[
			'identifier'  => 18711,
			'name'        => 'Rick Sconyers',
			'testimonial' => "In the partnership between the Miami Dolphins and Hard Rock, there was some discussion about a color that was altered in the middle of the process. Jones Sign Company had already completed two signs & we changed the color of the blue background to a different shade of blue. Jones Sign Company reacted immediately, they painted a sample, I took a look at it to approve & the next morning I came back to their facility to find the signs had all been repainted to the new shade of blue. That is the kind of immediate reaction you can expect from their team to get the exact product you need at the end of the day. I look forward to future projects",
			'linkedin'    => 'linkedin.com/in/rick-sconyers-aia-8249655/',
			'position'    => 'Senior Director of Design & Memorabilia',
		],
		[
			'identifier'  => 18711,
			'name'        => 'Rick Sconyers',
			'testimonial' => "There were six logo badges on the building & then we did cross guitars at the entrance to Gate Four of the stadium. A typical build out in the sign industry takes about 12 weeks - that's what my experience has been. We had six weeks to complete our project before the first home game of the season. It needed to be done before that - that was mandatory regardless of when we started. In a typical process, you do the shop drawings, have them approved, and then the sign is built. In this instance, we were designing as Jones Sign Company was building. In effect, there were no real drawings - Jones Sign Company would put together the idea, we'd travel to Green Bay to look at it, approve, and they were building it that night",
			'linkedin'    => 'linkedin.com/in/rick-sconyers-aia-8249655/',
			'position'    => 'Senior Director of Design & Memorabilia',
		],
		[
			'identifier'  => 18418,
			'name'        => 'Danny Luu',
			'testimonial' => 'Webcor reached out to Jones Sign to provide a design build scope of work for the Metropolis Residence R1 and Hotel perforated corrugated metal panel system around the Phase 1 garage.  During our collaboration, we discovered that Jones Sign was capable of numerous other metal specialties and we also asked Jones Sign to design and build the R1 and Hotel drop off canopies as well as roof metal siding.  In working with Jones Sign we found that their crew work harder and was more diligent on completing the scopes of work than most other subcontractors.  It was a challenge to roll with the owner’s and architects’ changes during the design process.  It was also a challenge to work with a new Chinese developer to pay more efficiently and promptly.  Regardless of the challenges, we hope to grow our partnership with Jones Sign into future projects.',
			'linkedin'    => 'linkedin.com/in/danny-luu-37234ba9/',
			'position'    => 'Assistant Project Manager',
		],
		[
			'identifier'  => 18418,
			'name'        => 'Danny Luu',
			'testimonial' => 'During our collaboration, we discovered that Jones Sign was capable of numerous other metal specialties and we also asked Jones Sign to design and build the R1 and Hotel drop off canopies as well as roof metal siding.',
			'linkedin'    => 'linkedin.com/in/danny-luu-37234ba9/',
			'position'    => 'Assistant Project Manager',
		],
		[
			'identifier'  => 17304,
			'name'        => 'Lou DeAngelis',
			'testimonial' => 'We (Shake Shack) are a demanding client. Jones Sign has provided us with the project management, design, and fabrication expertise that we need in order to put our best foot forward as we expand into new markets.\r\nJones Sign is client-focused, solution-oriented, and always willing to explore new ways to help us make our signage more cost efficient and visually impactful. <br />We recommend Jones Sign without reservation to anyone who is looking for a sign company that can deliver service, and always stands behind their products.',
			'linkedin'    => 'linkedin.com/in/lou-deangelis-aa2765b',
			'position'    => 'Director of Consutruction & Facilities',
		],
		[
			'identifier'  => 17304,
			'name'        => 'Lou DeAngelis',
			'testimonial' => 'We recommend Jones Sign without reservation to anyone who is looking for a sign company that can deliver service, and always stands behind their products.',
			'linkedin'    => 'linkedin.com/in/lou-deangelis-aa2765b',
			'position'    => 'Director of Consutruction & Facilities',
		],
		[
			'identifier'  => 17306,
			'name'        => 'Warren Van Wees',
			'testimonial' => 'All of your signage looks awesome! The execution of the interior signage is first rate as well as the design, finishes, etc. I heard just the right comment a couple of times during the owner's event last week about the way you designed and executed.',
			'linkedin'    => '',
			'position'    => 'Project Architect',
		],
		[
			'identifier'  => 17307,
			'name'        => 'Pamela A. Smith',
			'testimonial' => 'We really appreciated the way Jones Sign got on the work & had all of our offices completed by Dec. 10th so we could pay them out of that year's budget. It made the job a little more difficult to be sure, but it was very much appreciated.',
			'linkedin'    => 'linkedin.com/in/pamela-smith-00a09442',
			'position'    => 'Administrator',
		],
		[
			'identifier'  => 18249,
			'name'        => 'Vonda Davis',
			'testimonial' => 'Project Managers at Jones Sign Co. should be applauded for their transparent & seamless communication, dedication to timeline expectations, & attention to detail.',
			'linkedin'    => 'linkedin.com/in/vonda-davis-b93b237a',
			'position'    => 'Project Manager',
		],
		[
			'identifier'  => 17632,
			'name'        => 'Jeff Hembree',
			'testimonial' => 'Jones Sign Company installation crew has a focus & commitment to safety unparalleled for most contracting companies. It is easy to spot a well-trained crew at the top of their industry by watching how they conduct themselves in a safe manner and Jones Sign Company is among the best.',
			'linkedin'    => 'linkedin.com/in/jeff-and-rhonda-hembree-76322172',
			'position'    => 'Facilities Manager',
		],
		[
			'identifier'  => 17632,
			'name'        => 'Jeff Hembree',
			'testimonial' => 'We want to express our appreciation for your recent work on our High-Rise Rooftop Signage project & convey how much we appreciate your company's top-to-bottom value as it applied to our project. From the beginning of the bid process through the installation of the signs, Jones Sign Company exemplified nothing but excellence in the way they approached the inquiry, design, build, & installation.',
			'linkedin'    => 'linkedin.com/in/jeff-and-rhonda-hembree-76322172',
			'position'    => 'Facilities Manager',
		],
		[
			'identifier'  => 17634,
			'name'        => 'Carl S. Hren',
			'testimonial' => 'Having worked with them many times in the past, I'd recommend Jones Sign Company to anyone. Take a tour of their facilities & you will see that it is a first class sign operation. Quite impressive.',
			'linkedin'    => 'linkedin.com/in/carl-hren-00b19724',
			'position'    => 'Vice President: Construction and Capital Assets',
		],
		[
			'identifier'  => 18430,
			'name'        => 'Shawn Clancy',
			'testimonial' => 'We enlisted the experts at Jones Sign Company to bring our concept to reality and they were able to deliver amazing results. I can\'t say enough good things.',
			'linkedin'    => 'linkedin.com/in/shawn-clancy-5278531',
			'position'    => 'Senior Director, Capital Projects & Retail Facilities',
		],
		[
			'identifier'  => 18430,
			'name'        => 'Shawn Clancy',
			'testimonial' => 'Potential clientele should know that Sephora has enjoyed a pleasant & professional relationship with Jones Sign since 2010. As part of our external identity, we have an architectural ribbon.  We'd gone through several iterations of how it should light up and never quite gotten it right.  We enlisted the experts at Jones Sign Company to bring our concept to reality and they were able to deliver amazing results. I can't say enough good things; I genuinely look forward to future opportunities.',
			'linkedin'    => 'linkedin.com/in/shawn-clancy-5278531',
			'position'    => 'Senior Director, Capital Projects & Retail Facilities',
		],
		[
			'identifier'  => 18478,
			'name'        => 'Joie Chitwood III',
			'testimonial' => 'We think we found a great partner in Jones Sign Company. They understood the pressure. Based on what I\'ve seen, they finished first with that checkered flag.',
			'linkedin'    => 'linkedin.com/in/joelchitwood',
			'position'    => 'Chief Operating Officer',
		],
		[
			'identifier'  => 18586,
			'name'        => 'Janice Buyer',
			'testimonial' => 'You have a GREAT team of installers. Very professional and dedicated to getting the job done.',
			'linkedin'    => 'linkedin.com/in/janice-buyer-659a0722',
			'position'    => 'Branch Manager',
		],
		[
			'identifier'  => 18492,
			'name'        => 'Jason McFadden',
			'testimonial' => 'Thank you for all of your assistance with our needs at Orlando MLS.  Your efforts were nothing short of miraculous & will not be forgotten by the Barton Malow Company family.',
			'linkedin'    => 'linkedin.com/in/jasonemcfadden/',
			'position'    => 'Project Director',
		],
	];
	/**
	 * The testimonials
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $client_info The slug of this posttype..
	 */
	public $client_info = [

		[ 'California Coast Credit Union', 'calcoastcu.org', 2014, 18586 ],
		[ 'California Coast Credit Union', 'calcoastcu.org', 2014, 17305 ],
		[ 'Barton Malow Company', 'bartonmalow.com', 2012, 18492 ],
		[ 'Concord Hospitality Enterprises Company', 'concordhotels.com/', 2014, 17634 ],
		[ 'CBRE', 'cbre.com/', 2014, 17632 ],
		[ 'Shaw Industries', 'shawinc.com', 2014, 18249 ],
		[ 'State Farm Isurance', 'statefarm.com/', 2014, 17307 ],
		[ 'Elkus | Manfredi Architects', 'elkus-manfredi.com', 2016, 17306 ],
		[ 'Webcor Builders', 'webcor.com', 2014, 18418 ],
	];
	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		// add_action( 'init', [ $this, 'create_posttype' ] );
		// add_filter( 'cmb2_render_clientele', [ $this, 'render_clientele_field_callback' ], 10, 5 );
		// add_filter( 'cmb2_render_testimonial', [ $this, 'render_testimonial_field_callback' ], 10, 5 );
		// add_action( 'cmb2_init', [ $this, 'additional_fields' ] );
		// add_filter( 'cmb2_sanitize_testimonial', [ $this, 'sanitize_testimonial_field' ], 10, 5 );
		// add_filter( 'cmb2_types_esc_testimonial', [ $this, 'types_esc_testimonial_field' ], 10, 4 );
		// add_action( 'manage_client_posts_columns', [ $this, 'make_new_admin_columns' ], 10, 1 );
		// add_action( 'manage_client_posts_custom_column', [ $this, 'manage_new_admin_columns' ], 10, 2 );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `wp_rig()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return [
			'get_client_info' => [ $this, 'get_client_info' ],
		];
	}






	public function additional_fields() {
		$metabox_args = [
			'context'      => 'normal',
			'id'           => 'client-information-metabox',
			'object_types' => [ $this->get_slug() ],
			'show_in_rest' => \WP_REST_Server::ALLMETHODS,
			'show_names'   => true,
			'title'        => 'Client Overview',
			'show_title'   => false,
			'cmb_styles'   => false,
		];
		$metabox = new_cmb2_box( $metabox_args );

		/**
		 * Get the label for the project address field;
		 */
		function get_label_cb( $field ) {
			return '<div style="color:white; font-weight: 600;background: var(--indigo-600);font-size: 2.5rem;">' . ucfirst( $field ) . ' Information</div>';
		}



	function get_testimonial_defaults() {
		$defaults =	[
			'name'        => '',
			'testimonial' => '',
			'position'    => '',
			'linkedin'    => '',
		];
		return $defaults;
	}
		/* Testimonial From the client */
		$args = [
			'name'           => 'Testimonial',
			'id'             => 'client_Testimonial', // Name of the custom field type we setup.
			'type'           => 'testimonial',
			'label_cb'       => get_label_cb( 'testimonial' ),
			'show_names'     => false, // false removes the left cell of the table -- this is worth understanding.
			'classes'        => [ 'testimonial_fields' ],
			'after_row'      => '<hr>',
			'default_cb'     => 'get_testimonial_defaults',
			'repeatable'     => true,
			'escape_cb'      => 'types_esc_testimonial_field',
			'santization_cb' => 'sanitize_testimonial_field',
			'button_side'    => 'right',
			'text'           => [
				'add_row_text'    => 'Add Testimonial',
				'remove_row_text' => 'Remove Testimonial',
			],
		];
		$metabox->add_field( $args );
	}


}//end class definition.
