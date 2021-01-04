<?php
/**
 * Template part for displaying an experimental form
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>
<section id="form-experiment" class="frontpage">
	<form action="get" name="experimentalForm" id="experimentalForm"></form>
	<div id="progress"></div>

	<div class="center">

		<div id="register">

			<span id="previousButton" class="person-symbol"></span>
			<span class="" id="forwardButton">&#x2794;</span>
			<!-- <button class="hideButton" type="submit" form="experimentalForm" id="submitFormButton" title="submit your inquiry now">&#9993;</button> -->


			<div id="inputContainer">
				<input data-identifier="inputField" id="inputField" required />
				<label data-identifier="inputLabel" for="inputField" id="inputLabel"></label>
				<div id="inputProgress"></div>
			</div><!-- end div#inputcontainer -->

		</div><!-- end div#register -->

	</div><!-- end div.center -->

</section><!-- end section#form-experiment -->

<script>
const mail  = '&#9993;';
const arrow = '&#x2794;';
let questions = [
	// {
	// 	question: 'What is your first name?',
	// 	attributes: {
	// 		autocomplete: 'given-name',
	// 		form: 'experimentalForm',
	// 		tabIndex: 10,
	// 		name: 'firstName',
	// 		title: 'enter your first name',
	// 	},
	// },
	// {
	// 	question:"What is your last name?",
	// 	attributes: {
	// 		autocomplete: 'family-name',
	// 		form: 'experimentalForm',
	// 		tabIndex: 20,
	// 		name: 'lastName',
	// 		title: 'enter your last name',
	// 	},
	// },
	// {
	// 	question:"What is your Company Name?",
	// 	attributes: {
	// 		autocomplete: 'organization',
	// 		form: 'experimentalForm',
	// 		tabIndex: 30,
	// 		name: 'companyName',
	// 		title: 'which company are you with?'
	// 	},
	// },
	// {
	// 	question:"What is your Position?",
	// 	attributes: {
	// 		autocomplete: 'organization-title',
	// 		form: 'experimentalForm',
	// 		tabIndex: 40,
	// 		name: 'jobTitle',
	// 		title: 'Enter your job title',
	// 	},
	// },
	{
		question:"What is your email?",
		attributes: {
			autocomplete: 'email',
			form: 'experimentalForm',
			pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
			tabIndex: 50,
			name: 'emailAddress',
			title: 'name@domain.com',
		},
	},
	{
		question:"What is your message",
		attributes: {
			form: 'experimentalForm',
			tabIndex: 60,
			name: 'inquiry',
			title: 'tell us a bit about what you are looking for',
		},
	}
];

/* Do something after the questions have been answered */
let onComplete = function() {
	let h1 = document.createElement('h1');
	h1.appendChild( document.createTextNode( `Thanks for reaching out, ${questions[0].answer}` ) );
	setTimeout( function() {
		register.parentElement.appendChild( h1 )
		setTimeout( function() {
			h1.style.opacity = 1;
		}, 50 )
	}, 1000 )
}

;( function( questions, onComplete ) {

	const transformTime = 100 // transition transform time from #register in ms
	const waitTime      = 200 // transition width time from #register in ms
	const eTime         = 1000 // transition width time from inputLabel in ms

	// init
	// --------------
	// guard clause - if we are on the first question or there are no questions, then return.
	if ( 0 == questions.length ) {
		return;
	}

	let position = 0;

	putQuestion()

	// If you directly click on the button, thenvalidate and advance to the next field
	forwardButton.addEventListener( 'click', validate );
	forwardButton.addEventListener( 'keydown', function( e ) {
		// If you tab key over to the forward arrow, another press of tab OR the 'enter' key will advance you forward
		// 9 is 'tab', 13 is 'enter'
		if ( [ 9, 13 ].includes( e.keyCode ) ) {
			e.preventDefault(); // otherwise striking the tab key sends you way down the page
			validate();
		}
	} );

	// If you strike the enter key while you are focused in the input field, it will validate and advance to th next input
	inputField.addEventListener( 'keyup', function( e ) {
		transform( 0, 0 ); // ie hack to redraw
		// 13 = enter
		13 == e.keyCode && validate();
	})

	// Go back a field if you hit the back arrow.
	previousButton.addEventListener( 'click', function( e ) {
		if ( position === 0 ) {
			return;
		}
		position -= 1;
		hideCurrent( putQuestion );
	})


	// Functions
	// --------------

	// Load next question
	function putQuestion() {

		const finalQuestion = isLastQuestion( position, questions );
		inputLabel.innerHTML = questions[position].question;
		inputField.type      = questions[position].type || 'text';
		inputField.value     = questions[position].answer || '';
		inputField.setAttribute( 'name', questions[position].attributes.name );
		let tabIndex = questions[position].attributes.tabIndex || 34;
		inputField.setAttribute( 'tabindex', tabIndex );
		let formId = questions[position].attributes.form || 'experimentalForm';
		inputField.setAttribute( 'form', formId );
		let title = questions[position].attributes.title || '';
		inputField.setAttribute( 'title', title );
		let autocomplete = questions[position].attributes.autocomplete || '';
		inputField.setAttribute( 'autocomplete', autocomplete );


		/**
		 * On the final question, change the forward button into a send mail button with the following attributes
		 * type="submit"
		 * form="experimentalForm"
		 * title="use this button to send an email"
		 */
		if ( finalQuestion ) {
			forwardButton.remove();
			const submitFormButton = document.createElement( 'button' );
			submitFormButton.innerHTML = mail;
			submitFormButton.setAttribute( 'id', 'submitFormButton' );
			submitFormButton.setAttribute( 'tabindex', '62' );
			submitFormButton.setAttribute( 'type', 'submit' );
			submitFormButton.setAttribute( 'class', '' );
			submitFormButton.setAttribute( 'form', 'experimentalForm' );
			submitFormButton.setAttribute( 'title', 'submit your message by pretting this button' );
			inputContainer.insertAdjacentElement( 'beforebegin', submitFormButton );

		} else {
			assignButtonTabIndices();
			console.log( 'this isnt the last question ');
		}


		inputField.focus();


		// set the progress of the background
		progress.style.width     = position * 100 / questions.length + '%';
		previousButton.className = position ? '' : 'person-symbol';

		showCurrent();


	}

// See whether we are on the last question or not
function isLastQuestion( position, questions ) {
	let question = position + 1; // Index of the question starting at 1
	return question === questions.length;
}


// Validate what the people have inputted
function validate() {

	let validateCore = function() {
		return inputField.value.match( questions[position].attributes.pattern || /.+/)
	}

	if ( ! questions[position].validate ) {
		questions[position].validate = validateCore;
	}

	// check if the pattern matches
	if ( ! questions[position].validate() ) {
		wrong( inputField.focus.bind( inputField ) );
	}
	else ok( function() {

		// execute the custom end function or the default value set
		if ( questions[position].done ) {
			questions[position].done();
		} else {
			questions[position].answer = inputField.value;
		}
		++position;

		// if there is a new question, hide current and load next
		if ( questions[position] ) {

			hideCurrent( putQuestion );

			if ( isLastQuestion( position, questions ) ) {
				forwardButton.classList.add( 'hidden' );
				// submitFormButton.classList.remove('hideButton');
				// submitFormButton.classList.add('showButton');
			}
		} else {
			hideCurrent( function() {
				// remove the box if there is no next question

				register.className = 'close';
				progress.style.width = '100%';

				onComplete();
			})
		}
	})

} //end validate()

	// give the previous and next arrows tab indexes in relation to the tabindex of the input field
	function assignButtonTabIndices() {
		let [ previous, next ] = [...inputField.parentNode.parentElement.querySelectorAll( 'span' ) ];
		// If we are on the first field, there is no need to set a tab index on the prior element, so just set a tabindex on the next element ( usually an arrow)
		if ( previous.classList.contains( 'person-symbol' ) ) {
			next.setAttribute( 'tabindex', Number( inputField.getAttribute( 'tabindex' ) ) + 1  );
		} else {
			next.setAttribute( 'tabindex', Number( inputField.getAttribute( 'tabindex' ) ) + 1 );
			previous.setAttribute( 'tabindex', Number( inputField.getAttribute( 'tabindex' ) ) - 1 );
		}
	}

	// Helpers
	function hideCurrent( callback ) {
		inputContainer.style.opacity   = 0;
		inputLabel.style.marginLeft    = 0;
		inputProgress.style.width      = 0;
		inputProgress.style.transition = 'none';
		inputContainer.style.border    = null;
		setTimeout( callback, waitTime );
	}

	function showCurrent( callback ) {
		inputContainer.style.opacity   = 1;
		inputProgress.style.transition = '';
		inputProgress.style.width      = '100%';
		setTimeout( callback, waitTime );
	}

	function transform( x, y ) {
		register.style.transform = `translate(${x}px, ${y}px)`;
	}

	function ok( callback, transformTime = 100 ) {
		register.className = '';
		setTimeout( transform, transformTime * 0, 0, 10 );
		setTimeout( transform, transformTime * 1, 0, 0 );
		setTimeout( callback, transformTime * 2 );
	}

	function wrong( callback, transformTime = 100 ) {
		register.className = 'wrong';
		// shaking motion
		for ( var i = 0; i < 6; i++ ) {
			setTimeout( transform, transformTime * i, ( i % 2 * 2 - 1 ) * 20, 0 );
			setTimeout( transform, transformTime * 6, 0, 0 );
			setTimeout( callback, transformTime * 7 );
		}
	}

}( questions, onComplete ))



console.log( ironic.classList.value );
</script>


<script>
/*!
 * Capture data from form into google sheet
 * @version   1.0.1
 * @requires  jQuery v1.6 or later
 * @requires  Sheetrock 1.6
 * @see       https://github.com/chriszarate/sheetrock
 * @see       https://dev.to/omerlahav/submit-a-form-to-a-google-spreadsheet-1bia
 * @author    Nick Mortensen <nmortensen at jonessign>
 */

/* contains the data for all the slides */
const mySpreadsheet = 'https: //docs.google.com/spreadsheets/d/1JkoXp5PNSzxLcgN0RHhDY4k-8TF450yILtq-HltjuSc/edit?usp = sharing#gid = 0';
const webAppURL     = 'https: //script.google.com/macros/s/AKfycbyvpTaK8WBKwYrHgpWkLs1uAsssdf5kffT2ofsjxeOqzWrSCQ/exec';
const form          = document.forms['experimentalForm'];
</script>
