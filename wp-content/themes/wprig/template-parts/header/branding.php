<?php
/**
 * Template part for displaying the header branding
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

$site_icon_svg     = wp_get_attachment_image_src( 272 )[0];
$site_icon_letters = wp_get_attachment_image_src( 434 )[0];
?>

<style>
.site-title {
	color: var(--color-theme-white);
	font-size: clamp(1rem, 2.5vw, 2rem);
}
.site-branding {
	width: 20vw;
}
.jonessign {
	fill: var(--blue-600);
}
.tagline {
	fill: var(--gray-600);
}


</style>
<div class="site-branding" style="border: 2px solid var(--red-300);">

	<div class="site-icon">
		<a class="show-on-small-only" title="jonessign.com home" href="<?=esc_url( home_url( '/' ) ); ?>" rel="home">
			<img src="<?= $site_icon_svg; ?>" alt="jones sign company logo">
		</a>
	</div>

	<div class="jonessign_logo">
		<a class="show-on-larger-only" title="jonessign.com home" href="<?=esc_url( home_url( '/' ) ); ?>" rel="home">
			<!-- Jones Sign Text In Bank Gothic font as an SVG rather than including a call to a font -->
			<svg version="1.1" id="jones_logo" xmlns="http://www.w3.org/2000/svg" x="0" y="0" viewBox="0 0 854.527 169.727" xml:space="preserve">
				<g id="jones_sign_text">
					<path id="j"   class="jonessign" d="M23.594 72.295h33.4V1h23.428v69.36c0 5.767-.678 13.514-4.902 17.737-4.229 4.311-12.096 4.928-17.657 4.928H22.564c-5.571 0-13.473-.606-17.712-4.928C.683 83.849 0 76.098 0 70.361V51.324h23.594v20.971z"/>
					<path id="o"   class="jonessign" d="M94.016 36.656c0-4.913.633-11.253 4.265-14.81 3.707-3.632 10.599-4.119 15.396-4.119h52.252c4.788 0 11.77.479 15.457 4.124 3.597 3.562 4.21 9.916 4.21 14.805v37.388c0 4.934-.639 11.317-4.306 14.874-3.727 3.606-10.559 4.108-15.361 4.108h-52.252c-4.797 0-11.688-.486-15.396-4.118-3.638-3.562-4.265-9.946-4.265-14.864V36.656zm20.69 38.347h50.192v-39.81h-50.192v39.81z"/>
					<path id="n-1" class="jonessign" d="M267.448 62.415v-45.17h19.149v75.781h-14.256l-54.286-45.39v45.39h-19.23V17.728h14.166z"/>
					<path id="e"   class="jonessign" d="M321.423 34.687v10.995h32.662v16.652h-32.662V75.31h56.693v17.716h-77.55V17.728h76.701v16.959z"/>
					<path id="s-1" class="jonessign" d="M403.985 75.31h45.435V63.56h-41.643c-4.838 0-11.721-.502-15.468-4.134-3.691-3.571-4.355-9.906-4.355-14.869v-7.932c0-4.948.679-11.202 4.351-14.765 3.752-3.626 10.641-4.134 15.473-4.134h40.109c4.812 0 11.539.508 15.377 3.939 3.696 3.305 4.53 8.854 4.53 13.673v3.391l-18.506.025v-3.897h-42.266v10.52h41.874c4.763 0 11.715.462 15.396 4.054 3.646 3.531 4.27 9.94 4.27 14.844v9.745c0 4.958-.647 11.349-4.36 14.898-3.712 3.597-10.508 4.108-15.306 4.108H405.06c-4.853 0-11.783-.497-15.582-4.094-3.747-3.552-4.396-9.94-4.396-14.914v-3.776l18.902-.03v5.099h.001z"/>
					<path id="s-2" class="jonessign" d="M594.025 21.676H544.31v14.12h49.253c5.597 0 13.573.623 17.817 4.958 4.215 4.29 4.908 11.936 4.908 17.737V70.36c0 5.792-.703 13.423-4.908 17.707-4.244 4.33-12.222 4.958-17.817 4.958h-50.895c-5.596 0-13.573-.628-17.817-4.958-4.205-4.284-4.907-11.915-4.907-17.707v-3.158l21.179-.012v5.104h53.989V57.029h-49.253c-5.586 0-13.468-.638-17.713-4.968-4.148-4.305-4.843-11.95-4.843-17.727v-9.55c0-5.776.693-13.423 4.843-17.732 4.245-4.329 12.127-4.963 17.713-4.963h46.781c5.525 0 13.115.647 17.436 4.731 4.244 4.015 5.118 10.816 5.118 16.482l-.075 7.871-21.093.016v-9.513h-.001z"/>
					<path id="i"   class="jonessign" d="M630.203 93.026V17.195h20.609v75.831z"/>
					<path id="n-2" class="jonessign" d="M835.373 62.415v-45.25l18.557.056.597 75.805h-14.261l-54.288-45.39v45.39h-19.227V17.728h14.16z"/>
					<path id="g"   class="jonessign" d="M732.698 35.193h-47.164v39.81h47.164V64.374l-20.786-.025-.156-17.175h41.312v26.869c0 4.934-.628 11.317-4.306 14.874-3.728 3.606-10.555 4.108-15.36 4.108h-48.896c-4.798 0-11.634-.502-15.352-4.108-3.678-3.557-4.306-9.94-4.306-14.874V36.656c0-4.929.628-11.272 4.306-14.818 3.718-3.607 10.554-4.109 15.352-4.109h48.896c4.762 0 11.567.487 15.291 4.004 3.632 3.4 4.375 9.259 4.375 14.085l.266 4.839-20.635-.127-.001-5.337z"/>
				</g>
				<path id="tagline" class="tagline" d="M35.403 120.177l-12.773 22.6v16.21h-9.293v-15.713L.347 120.177h10.579l7.258 13.854 6.978-13.854h10.241zm29.638 24.186c0 4.817-1.256 8.615-3.763 11.39-2.512 2.763-6.058 4.153-10.649 4.153-4.561 0-8.092-1.392-10.624-4.153-2.526-2.773-3.787-6.571-3.787-11.39 0-4.866 1.261-8.68 3.787-11.433 2.532-2.753 6.063-4.129 10.624-4.129 4.576 0 8.123 1.387 10.64 4.154 2.516 2.778 3.772 6.577 3.772 11.408zm-8.921.051c0-1.733-.136-3.186-.407-4.34-.276-1.155-.653-2.085-1.13-2.773-.513-.729-1.101-1.235-1.749-1.532-.643-.291-1.376-.427-2.205-.427-.773 0-1.477.121-2.114.382-.628.251-1.206.729-1.738 1.441-.492.674-.895 1.606-1.19 2.793-.291 1.186-.447 2.668-.447 4.456 0 1.804.141 3.239.422 4.35.281 1.105.648 1.984 1.096 2.647a4.09 4.09 0 001.737 1.478 5.593 5.593 0 002.306.481c.698 0 1.412-.171 2.13-.481a3.879 3.879 0 001.724-1.402c.517-.742.909-1.643 1.17-2.701.259-1.052.395-2.514.395-4.372zm39.447 14.572h-8.69v-3.225c-1.507 1.315-2.894 2.315-4.145 3.009-1.266.679-2.768 1.029-4.506 1.029-2.768 0-4.917-.919-6.47-2.752-1.552-1.834-2.325-4.592-2.325-8.273V129.72h8.745v14.518c0 1.513.03 2.737.11 3.702.075.949.257 1.759.532 2.421a2.894 2.894 0 001.231 1.377c.558.313 1.331.473 2.32.473.623 0 1.347-.16 2.181-.473a9.406 9.406 0 002.325-1.27v-20.746h8.69v29.265h.002zm25.403-20.65h-.723c-.332-.121-.814-.206-1.442-.276a21.785 21.785 0 00-2.084-.085c-.915 0-1.864.146-2.879.416-.999.271-1.974.605-2.913.985v19.609h-8.69V129.72h8.69v4.185c.396-.382.949-.854 1.652-1.44.703-.583 1.347-1.05 1.929-1.393.628-.401 1.361-.737 2.19-1.029.839-.291 1.628-.433 2.376-.433.281 0 .603.011.954.025.347.015.658.045.939.085l.001 8.616zm49.639-18.159l-12.719 38.809h-9.694l-12.714-38.809h9.64l8.047 26.332 8.058-26.332h9.382zm11.629 38.809h-8.695V129.72h8.695v29.266zm.257-33.439h-9.192v-7.118h9.192v7.118zm14.903 34.253c-2.059 0-4.003-.235-5.821-.703-1.834-.472-3.341-1.02-4.541-1.646v-7.711h.723c.417.331.88.688 1.417 1.091.527.396 1.261.817 2.211 1.256.809.396 1.724.738 2.743 1.029 1.015.285 2.125.427 3.311.427 1.23 0 2.311-.195 3.255-.593.929-.396 1.396-1.04 1.396-1.908 0-.68-.211-1.186-.648-1.537-.427-.347-1.256-.685-2.507-.995a50.859 50.859 0 00-2.511-.562 20.302 20.302 0 01-2.678-.688c-2.19-.703-3.823-1.763-4.928-3.164-1.105-1.401-1.652-3.24-1.652-5.52 0-1.291.286-2.527.854-3.684.572-1.149 1.406-2.184 2.521-3.089 1.11-.885 2.481-1.593 4.109-2.11 1.627-.521 3.471-.782 5.525-.782 1.96 0 3.753.206 5.39.617 1.638.401 3.02.898 4.149 1.468v7.397h-.693c-.301-.24-.778-.562-1.416-.979a18.758 18.758 0 00-1.889-1.045 12.169 12.169 0 00-2.446-.863 11.31 11.31 0 00-2.769-.342c-1.256 0-2.32.226-3.185.678-.874.452-1.306 1.05-1.306 1.804 0 .663.211 1.176.638 1.559.438.382 1.371.758 2.808 1.119.754.2 1.607.388 2.572.562.97.186 1.898.436 2.803.748 2 .679 3.517 1.673 4.552 2.979 1.034 1.316 1.547 3.064 1.547 5.233a8.86 8.86 0 01-.909 3.924 8.932 8.932 0 01-2.617 3.141c-1.19.919-2.592 1.627-4.204 2.135-1.619.503-3.553.754-5.804.754zm26.383-.814h-8.69V129.72h8.69v29.266zm.256-33.439h-9.192v-7.118h9.192v7.118zm33.194 18.816c0 4.817-1.256 8.615-3.768 11.39-2.512 2.763-6.054 4.153-10.645 4.153-4.562 0-8.098-1.392-10.624-4.153-2.527-2.773-3.793-6.571-3.793-11.39 0-4.866 1.266-8.68 3.793-11.433 2.526-2.753 6.062-4.129 10.624-4.129 4.571 0 8.117 1.387 10.634 4.154 2.524 2.778 3.779 6.577 3.779 11.408zm-8.921.051c0-1.733-.136-3.186-.412-4.34-.281-1.155-.647-2.085-1.135-2.773-.508-.729-1.096-1.235-1.738-1.532-.643-.291-1.381-.427-2.205-.427-.778 0-1.482.121-2.115.382-.633.251-1.21.729-1.743 1.441-.492.674-.889 1.606-1.19 2.793-.291 1.186-.441 2.668-.441 4.456 0 1.804.141 3.239.417 4.35.281 1.105.652 1.984 1.095 2.647.462.674 1.045 1.171 1.743 1.478a5.593 5.593 0 002.306.481c.698 0 1.406-.171 2.125-.481a3.822 3.822 0 001.723-1.402c.518-.742.909-1.643 1.176-2.701.258-1.052.394-2.514.394-4.372zm39.678 14.572h-8.745v-14.518c0-1.17-.04-2.36-.136-3.531-.096-1.165-.262-2.044-.513-2.592-.291-.653-.723-1.13-1.296-1.427-.558-.281-1.311-.427-2.25-.427-.714 0-1.437.146-2.165.417-.733.285-1.507.729-2.341 1.33v20.746h-8.69v-29.266h8.69v3.23c1.427-1.268 2.812-2.262 4.159-2.98 1.351-.708 2.843-1.064 4.49-1.064 2.849 0 5.028.944 6.525 2.828 1.512 1.89 2.271 4.621 2.271 8.192l.001 19.062zm15.447 0h-8.79v-10.241h8.79v10.241zm18.31 0l12.497-38.81h10.138l12.503 38.81h-9.563l-2.326-7.927H333.38l-2.311 7.927h-9.324zm21.223-14.959l-3.776-12.789-3.782 12.789h7.558zm30.777 15.744c-2.306 0-4.415-.302-6.324-.915-1.919-.603-3.576-1.537-4.987-2.811-1.387-1.271-2.477-2.858-3.244-4.787-.784-1.909-1.172-4.185-1.172-6.787 0-2.798.423-5.179 1.262-7.146.85-1.959 1.994-3.571 3.436-4.849 1.386-1.205 3.028-2.104 4.898-2.691a19.937 19.937 0 015.882-.874c1.542 0 3.079.189 4.622.558 1.542.377 3.089.975 4.646 1.793v7.821h-1.12a17.668 17.668 0 00-1.28-1.15 10.74 10.74 0 00-1.647-1.14 10.275 10.275 0 00-2.135-.936c-.799-.257-1.689-.382-2.657-.382-2.23 0-3.938.81-5.164 2.441-1.211 1.622-1.824 3.808-1.824 6.555 0 2.934.653 5.134 1.954 6.571 1.297 1.441 3.029 2.159 5.181 2.159 1.095 0 2.055-.125 2.883-.388a9.427 9.427 0 002.13-.943 9.186 9.186 0 001.492-1.125 27.7 27.7 0 001.069-1.064h1.12v7.826c-.503.221-1.069.481-1.732.772-.653.302-1.316.549-1.989.733-.849.241-1.638.427-2.376.562-.743.131-1.723.197-2.924.197zm26.83 0c-2.307 0-4.416-.302-6.324-.915-1.919-.603-3.572-1.537-4.988-2.811-1.387-1.271-2.472-2.858-3.245-4.787-.783-1.909-1.171-4.185-1.171-6.787 0-2.798.429-5.179 1.268-7.146.844-1.959 1.989-3.571 3.431-4.849 1.392-1.205 3.029-2.104 4.902-2.691a19.928 19.928 0 015.877-.874c1.543 0 3.08.189 4.622.558 1.542.377 3.089.975 4.646 1.793v7.821h-1.12a18.608 18.608 0 00-1.275-1.15 11.041 11.041 0 00-1.647-1.14 10.32 10.32 0 00-2.14-.936c-.794-.257-1.684-.382-2.657-.382-2.226 0-3.938.81-5.159 2.441-1.211 1.622-1.823 3.808-1.823 6.555 0 2.934.652 5.134 1.949 6.571 1.296 1.441 3.028 2.159 5.184 2.159 1.095 0 2.05-.125 2.884-.388a9.57 9.57 0 002.13-.943 9.681 9.681 0 001.491-1.125 31.36 31.36 0 001.065-1.064h1.12v7.826c-.503.221-1.069.481-1.732.772-.648.302-1.312.549-1.989.733-.849.241-1.638.427-2.371.562-.743.131-1.723.197-2.928.197zm39.93-15.408c0 4.817-1.261 8.615-3.763 11.39-2.518 2.763-6.059 4.153-10.649 4.153-4.561 0-8.098-1.392-10.624-4.153-2.525-2.773-3.793-6.571-3.793-11.39 0-4.866 1.268-8.68 3.793-11.433 2.526-2.753 6.063-4.129 10.624-4.129 4.571 0 8.118 1.387 10.641 4.154 2.51 2.778 3.771 6.577 3.771 11.408zm-8.922.051c0-1.733-.141-3.186-.411-4.34-.275-1.155-.644-2.085-1.131-2.773-.507-.729-1.095-1.235-1.743-1.532-.638-.291-1.381-.427-2.205-.427-.777 0-1.48.121-2.114.382-.628.251-1.205.729-1.742 1.441-.487.674-.891 1.606-1.187 2.793-.297 1.186-.447 2.668-.447 4.456 0 1.804.142 3.239.423 4.35.275 1.105.644 1.984 1.09 2.647.463.674 1.045 1.171 1.743 1.478a5.59 5.59 0 002.306.481c.693 0 1.401-.171 2.125-.481a3.865 3.865 0 001.729-1.402c.518-.742.909-1.643 1.165-2.701.258-1.052.399-2.514.399-4.372zm46.888 14.572V144.37c0-1.447-.024-2.657-.074-3.648-.052-.999-.201-1.803-.452-2.426-.251-.628-.634-1.08-1.15-1.377-.518-.281-1.266-.427-2.2-.427-.658 0-1.326.155-1.964.457-.647.302-1.355.742-2.115 1.291v20.745h-8.739v-14.616c0-1.423-.025-2.644-.097-3.639-.05-.999-.2-1.811-.452-2.436-.251-.628-.628-1.08-1.159-1.377-.518-.281-1.251-.427-2.17-.427-.715 0-1.407.186-2.085.526-.688.353-1.356.759-1.994 1.221v20.746h-8.695v-29.266h8.695v3.23c1.427-1.268 2.763-2.262 4.022-2.98 1.267-.708 2.673-1.064 4.25-1.064 1.708 0 3.21.423 4.501 1.281 1.286.85 2.28 2.119 2.958 3.797 1.668-1.627 3.245-2.883 4.728-3.762 1.485-.88 2.987-1.316 4.511-1.316 1.291 0 2.456.217 3.476.647a6.76 6.76 0 012.617 1.96c.774.929 1.356 2.061 1.743 3.345.393 1.303.588 2.984.588 5.068v19.059l-8.743.004zm42.526-15.038c0 4.676-1.13 8.434-3.391 11.312-2.256 2.869-5.049 4.301-8.358 4.301-1.392 0-2.597-.171-3.627-.498-1.024-.331-2.114-.839-3.285-1.542v12.207h-8.69v-40.006h8.69v3.044a20.495 20.495 0 013.903-2.793c1.331-.708 2.857-1.063 4.597-1.063 3.234 0 5.741 1.341 7.51 4.044 1.772 2.694 2.651 6.352 2.651 10.994zm-8.891.185c0-2.849-.423-4.923-1.267-6.204-.824-1.291-2.135-1.929-3.902-1.929-.778 0-1.554.131-2.326.392-.773.257-1.521.648-2.275 1.171v14.729a6.31 6.31 0 001.532.438c.558.085 1.216.137 1.975.137 2.124 0 3.691-.733 4.711-2.182 1.03-1.454 1.552-3.634 1.552-6.552zm22.253 14.853h-8.695v-40.558h8.695v40.558zm15.362 0h-8.694V129.72h8.694v29.266zm.256-33.439h-9.191v-7.118h9.191v7.118zm14.909 34.253c-2.063 0-4.009-.235-5.827-.703-1.838-.472-3.34-1.02-4.535-1.646v-7.711h.714c.422.331.884.688 1.421 1.091.527.396 1.262.817 2.216 1.256.808.396 1.718.738 2.731 1.029 1.024.285 2.125.427 3.315.427 1.235 0 2.312-.195 3.255-.593.93-.396 1.393-1.04 1.393-1.908 0-.68-.206-1.186-.639-1.537-.438-.347-1.266-.685-2.517-.995a50.452 50.452 0 00-2.502-.562 20.487 20.487 0 01-2.684-.688c-2.188-.703-3.821-1.763-4.928-3.164-1.1-1.401-1.657-3.24-1.657-5.52 0-1.291.286-2.527.864-3.684.562-1.149 1.401-2.184 2.511-3.089 1.115-.885 2.487-1.593 4.119-2.11 1.623-.521 3.473-.782 5.521-.782 1.954 0 3.753.206 5.386.617 1.643.401 3.023.898 4.148 1.468v7.397h-.698c-.291-.24-.77-.562-1.406-.979a19.037 19.037 0 00-1.884-1.045 12.485 12.485 0 00-2.446-.863 11.331 11.331 0 00-2.771-.342c-1.256 0-2.321.226-3.186.678-.874.452-1.312 1.05-1.312 1.804 0 .663.216 1.176.643 1.559.438.382 1.372.758 2.813 1.119.748.2 1.603.388 2.565.562a20.5 20.5 0 012.805.748c1.993.679 3.511 1.673 4.556 2.979 1.025 1.316 1.547 3.064 1.547 5.233a8.73 8.73 0 01-.924 3.924 8.775 8.775 0 01-2.602 3.141c-1.201.919-2.599 1.627-4.216 2.135-1.61.503-3.544.754-5.789.754zm43.828-.814h-8.751v-14.518c0-1.17-.045-2.36-.136-3.531-.095-1.165-.267-2.044-.507-2.592-.291-.653-.729-1.13-1.296-1.427-.567-.281-1.316-.427-2.267-.427-.703 0-1.427.146-2.16.417-.718.285-1.502.729-2.336 1.33v20.746h-8.689v-40.558h8.689v14.522c1.428-1.268 2.818-2.262 4.164-2.98 1.348-.708 2.844-1.063 4.491-1.063 2.849 0 5.028.943 6.53 2.827 1.502 1.89 2.266 4.621 2.266 8.192l.002 19.062zm20.706.785c-5.265 0-9.299-1.315-12.102-3.959-2.812-2.642-4.221-6.403-4.221-11.287 0-4.781 1.308-8.595 3.935-11.447 2.612-2.854 6.269-4.274 10.979-4.274 4.267 0 7.485 1.211 9.641 3.622 2.149 2.416 3.229 5.882 3.229 10.383v3.28h-19.067c.085 1.355.36 2.491.828 3.399a5.772 5.772 0 001.839 2.176c.737.548 1.611.925 2.603 1.161.994.24 2.079.36 3.255.36 1.016 0 2.016-.12 2.989-.36a14.364 14.364 0 002.682-.926 18.56 18.56 0 001.976-1.068 20.219 20.219 0 001.48-1.016h.909v7.529c-.703.302-1.361.578-1.943.819-.588.256-1.4.517-2.422.769-.943.267-1.919.472-2.918.617-1.005.156-2.221.222-3.672.222zm2.888-19.185c-.045-1.929-.472-3.392-1.279-4.41-.805-1.005-2.029-1.512-3.688-1.512-1.703 0-3.009.531-3.974 1.597-.949 1.056-1.473 2.502-1.577 4.325h10.518zm38.413 18.4h-8.694v-3.049c-.562.487-1.205.999-1.93 1.567-.724.553-1.352.975-1.898 1.256-.693.347-1.388.598-2.06.773-.679.171-1.492.266-2.432.266-3.18 0-5.701-1.381-7.561-4.148-1.854-2.763-2.782-6.455-2.782-11.075 0-2.559.315-4.768.943-6.646.634-1.873 1.507-3.516 2.607-4.902a11.195 11.195 0 013.677-3.004 9.91 9.91 0 014.571-1.114c1.456 0 2.651.16 3.597.481.929.321 2.024.818 3.266 1.502V118.43h8.694l.002 40.556zm-8.696-8.077v-14.356c-.438-.232-.989-.422-1.689-.567-.692-.145-1.286-.217-1.788-.217-2.085 0-3.657.765-4.712 2.291-1.05 1.532-1.577 3.667-1.577 6.409 0 2.884.417 4.98 1.262 6.249.822 1.296 2.188 1.939 4.062 1.939.759 0 1.521-.166 2.301-.479a8.053 8.053 0 002.141-1.269zm24.369 8.077h-8.791v-10.241h8.791v10.241z" jonessign="#f2f2f2"/>
			</svg>
		</a>
	</div>

	<?php
	if ( is_front_page() && is_home() ) {
		?>
		<h1 class="site-title"><a href="<?= esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		<?php
	} else {
		?>
		<p class="site-title"><a href="<?= esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
		<?php
	}
	?>


</div><!-- .site-branding -->
