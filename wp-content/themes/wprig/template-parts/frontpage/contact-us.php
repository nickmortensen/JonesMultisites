<?php
/**
 * Template part for displaying an experimental form
 * Last Update 29_April_2021.
 *
 * @link https://codepen.io/arcs/pen/rYXrNQ?editors=0110
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

[ 'requested_by' => $request_from ] = $args;

wp_rig()->print_styles( 'wp-rig-contact-form' );
?>

<!-- contact form -->
<section id="contact" class="highlight <?= $request_from; ?>" data-scrollto="contact">
	<div id="progress"></div>
	<div class="section-title"><h4>Connect</h4></div>

	<form action="get" name="experimentalForm" id="experimentalForm"></form>

		<div class="section-content center">
				<div id="register" class="">
					<span id="previousButton" class="material-icons">person</span>
					<span id="forwardButton" class="material-icons" title="advance to the next form field" >arrow_forward</span>
					<div id="inputContainer">
						<input name="" id="inputField" required />
						<label for="inputField" id="inputLabel"></label>
						<div id="inputProgress" class="underline"> </div>
					</div><!-- end div#inputcontainer -->
				</div><!-- end div#register -->
		</div><!-- end div.section-content.center -->
</section>

<script>

const material = [ 'arrow_forward', 'arrow_backward', 'person', 'send' ];

const inputField = document.querySelector( '#inputField' );
const inputLabel = document.querySelector( '#inputLabel' );


const register          = document.getElementById( 'register' );
const progress          = document.getElementById( 'progress' );
const previousButton    = document.getElementById( 'previousButton' );
const forwardButton     = document.getElementById( 'forwardButton' );
forwardButton.innerHTML = material[0];

// console.log( `register is `, register.getBoundingClientRect().height, 'px' );
// console.log( `inputContainer is `, inputContainer.getBoundingClientRect().height, 'px' );
// console.log( `inputField is `, inputField.getBoundingClientRect().height, 'px' );
// console.log( `inputLabel is `, inputLabel.getBoundingClientRect().height, 'px' );





const sheetInfo = {
	googleWebApp: 'https://script.google.com/macros/s/AKfycbz6hC2Fw34eoOrnOwJ_V4cbAFDfeDe8niMTb1f1AV7btoeEpZsD/exec',
	sheetUrl: '',
};

/**
 * Initialize an empty forData array to add to as we advance through the form.
 */
let formData = [];
let questions = [
	{
		question: 'What is your name?',
		attributes: {
			autocomplete: 'given-name',
			form: 'experimentalForm',
			tabIndex: 10,
			name: 'fullName',
			title: 'your first and last name.',
			description: 'Name',
		},
		answer: 'nick mortensen',
		isLast: false,
	},
	{
		question:"What is your zip code?",
		attributes: {
			autocomplete: 'postal-code',
			form: 'experimentalForm',
			tabIndex: 20,
			name: 'zip',
			title: 'a 5 digit postal code.',
			description: 'Zip Code',
		},
		answer: '54688',
		isLast: false,
	},
	// {
	// 	question:"What is your Company Name?",
	// 	attributes: {
	// 		autocomplete: 'organization',
	// 		form: 'experimentalForm',
	// 		tabIndex: 30,
	// 		name: 'companyName',
	// 		title: 'your company name.',
	// 		description: 'Company Name',
	// 	},
	// 	answer: 'General Industrial Inc',
	// 	isLast: false,
	// },
	// {
	// 	question:"What is your Position?",
	// 	attributes: {
	// 		autocomplete: 'organization-title',
	// 		form: 'experimentalForm',
	// 		tabIndex: 40,
	// 		name: 'jobTitle',
	// 		title: 'your job title.',
	// 		description: 'Position',
	// 	},
	// 	answer: 'Logistics Specialist',
	// 	isLast: false,
	// },
	// {
	// 	question:"What is your email?",
	// 	attributes: {
	// 		autocomplete: 'email',
	// 		form: 'experimentalForm',
	// 		pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
	// 		tabIndex: 50,
	// 		name: 'emailAddress',
	// 		title: 'a valid email address',
	// 		description: 'Email',
	// 	},
	// 	answer: 'twentyeight@january.com',
	// 	isLast: false,
	// },
	{
		question:"What is your message",
		attributes: {
			form: 'experimentalForm',
			tabIndex: 60,
			name: 'inquiry',
			title: 'a short message.',
			description: 'Message Input',
		},
		answer: 'A longer message would go here',
		isLast: true,
	}
];

const fullNameField = document.querySelector( 'input[name="fullName]' );

const createAcknowledgement = fullName => 'Thank you for Reaching out, ' + fullName.split( /(?<=^\S+)\s/ )[0].charAt(0).toUpperCase() + fullName.split( /(?<=^\S+)\s/ )[0].slice(1);


/* Do something after the questions have been answered */
let onComplete = function( formData ) {
	// Instantiate a FormData Object
	// @link https://developer.mozilla.org/en-US/docs/Web/API/FormData
	// @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/fromEntries
	let newData = new FormData();
	let firstNameInquiry = formData[0][1];
	console.log( firstNameInquiry );

	// Place newly collected data into the newData object.
	formData.forEach( datum => newData.append( datum[0], datum[1] ) );

	// Send the newData Object and all of the data to a Google Sheet desginated to collect this data
	fetch( sheetInfo.googleWebApp, { method: 'POST', body: newData } )
		// Once the data has been added to the spreadsheet, acknowledge the form was filled out to the user
		.then( response => {
			let acknowledgementItem = document.createElement( 'span' );
			acknowledgementItem.append( createAcknowledgement( questions[0].answer ) );
			document.querySelector( '#contact > .center').parentElement.appendChild( acknowledgementItem );
			document.getElementById( 'contact' ).classList.add( 'form_closed' );

		} )
		.catch( error => console.error( 'Error!', error.message ) );
}

;( function( questions, onComplete ) {

	const transformTime = 100  // transition transform time from #register in ms
	const waitTime      = 200  // transition width time from #register in ms
	const eTime         = 1000 // transition width time from inputLabel in ms

	let position        = 0;

	// init
	// --------------
	// guard clause - if there are no questions, then return.
	if ( 0 === questions.length ) {
		return;
	}

	const isLastQuestion = ( a = position, b = questions ) => Number( a + 1 ) === b.length;

	putQuestion();

	// If you directly click on the button, then validate and advance to the next field
	forwardButton.addEventListener( 'click', validate );

	// On striking the 'enter' or the 'tab' key, validate
	forwardButton.addEventListener( 'keydown', function( e ) {
		// If you tab key over to the forward arrow, another press of tab OR the 'enter' key will advance you forward
		// 9 is 'tab', 13 is 'enter'
		if ( [ 9, 13 ].includes( e.keyCode ) ) {
			e.preventDefault(); // otherwise striking the tab key sends you way down the page
			validate();
		}
	} );

	// If you strike the enter key while you are focused in the input field, it will validate and advance to the next input
	inputField.addEventListener( 'keyup', function( e ) {
		transform( 0, 0 ); // ie hack to redraw
		// 13 is 'enter'
		13 === e.keyCode && validate();
	})

	// Go back a field if you hit the back arrow.
	previousButton.addEventListener( 'click', function( e ) {
		// Back arrow should not exist on the initial form field (it would have a 'person' icon), but if it somehow should then we just return
		if ( position === 0 ) {
			return;
		}
		// Should a person want to go to the previous field when the current field has been determined as 'invalid', remove the wrong from the register div
		if ( register.classList.contains( 'wrong' ) ) {
			register.classList.remove( 'wrong' );
		}
		// Set the position variable to one less than it currently is and move to that question as the input
		position -= 1;
		hideCurrent( putQuestion );
	} )


	// Functions
	// --------------

	// Load next question
	function putQuestion() {

		let inputLabel         = document.getElementById( 'inputLabel' );
		let inputfield         = document.getElementById( 'inputfield' );
		let fieldName          = questions[position].attributes.name;
		let description        = questions[position].attributes.description;
		let tabIndex           = questions[position].attributes.tabIndex || 34;
		let formId             = questions[position].attributes.form || 'experimentalForm';
		let title              = questions[position].attributes.title || '';
		let autocomplete       = questions[position].attributes.autocomplete || '';
		let isLast             = questions[position].isLast;
		inputLabel.textContent = questions[position].question;
		inputField.type        = questions[position].type || 'text';
		inputField.value       = questions[position].answer || '';
		inputField.name        = fieldName;
		inputField.title       = `Please Input ${title}`;
		inputField.tabIndex    = tabIndex;

		inputField.setAttribute( 'data-identifier', fieldName );
		inputField.setAttribute( 'data-last', isLast );
		inputField.setAttribute( 'form', formId );
		inputField.setAttribute( 'autocomplete', autocomplete );


		/*====== PREVIOUS AND NEXT BUTTONS ======*/
		// Arrow fields for forward and backward should have tabindex that is one less and one more than the input, respectively


		[ previous, next ] = [ previousButton, forwardButton ];
		previous.setAttribute( 'tabindex', tabIndex - 1 );
		next.setAttribute( 'tabindex', tabIndex + 1 );
		// Next icon should be a mail on last input, a forward on all other inputs
		next.innerText     = isLastQuestion() ? 'send' : 'arrow_forward';
		next.title         = isLastQuestion() ? 'click this button to send your message' : `Click to advance to the ${questions[position + 1].attributes.description} input field.` ;
		// Previous icon should be a person on first input, back arrow on all others.
		previous.innerText = position === 0 ? 'person' : 'keyboard_backspace';
		previous.title     = position === 0 ? 'You are on the first field.' : `Click to revert to the ${questions[position - 1].attributes.description} input field.`;


		if ( position !== 0 ) {
			inputField.focus();
		}

		// set the progress of the background
		progress.style.width     = position * 100 / questions.length + '%';
		previousButton.innerText = position !== 0 ? 'arrow_backward' : 'person';
		showCurrent();
	}

	/**
	 * Create Object with field names as key and added responses as the values using the built in FormData object.
	 */
	function makeResponseObject( formData ) {
		return Object.fromEntries( formData );
	}

	/**
	 * As it is entered, add the inputted information to the FormData object
	 */
	function addData() {
		let {name, value} = inputField;
		formData.push([name, value]);

	}

	// Validate what the people have inputted
	function validate() {

		// @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/match
		let validateCore = function() {
			return inputField.value.match( questions[position].attributes.pattern || /.+/ );
		}

		// If the question object does not have a validate attribute (none do), then use the validateCore function.
		if ( ! questions[position].validate ) {
			questions[position].validate = validateCore;
		}

		// check if the pattern matches
		if ( ! questions[position].validate() ) {
			wrong( inputField.focus.bind( inputField ) );
			let spanInvalid       = inputProgress;
			spanInvalid.innerText = `Please input ${questions[position].attributes.title}`;
			spanInvalid.classList.remove( 'hide' );
			spanInvalid.classList.add( 'opacity-full' );
		}

		else ok( function() {

			questions[position].answer = inputField.value;
			++position;

			// if there is a new question, hide current and load next
			if ( questions[position] ) {
				addData();
				hideCurrent( putQuestion );
			} else {
				hideCurrent( function() {
					// remove the box if there is no next question
					register.className   = 'close';

					progress.style.width = '100%';
					addData();
					onComplete( formData );
				})
			}
		})

	} //end validate()

	/**
	 * Hide current field prior to advancing to the next
	 */
	function hideCurrent( callback ) {
		inputContainer.style.opacity   = 0;
		inputLabel.style.marginLeft    = 0;
		inputProgress.style.width      = 0;
		inputProgress.style.transition = 'none';
		inputContainer.style.border    = null;
		setTimeout( callback, waitTime );
	}

	/**
	 * Show the new field that has yet to have data inputted into it.
	 * Has a sort of fancy line drawn from left to right, courtesy of the inputProgress div's top border
	 */
	function showCurrent( callback ) {
		inputContainer.style.opacity   = 1;
		inputProgress.style.transition = '';
		inputProgress.style.width      = '100%';
		setTimeout( callback, waitTime );
	}

	/**
	 * Basis for the little wiggle we see when a field is advanced AND when the inputted data within the field is invalid
	 */
	function transform( x, y ) {
		register.style.transform = `translate(${x}px, ${y}px)`;
	}

	/**
	 * On OK, remove any class name added to the form (.wrong will be added when the inputted data isn't validated)
	 */
	function ok( callback, transformTime = 100 ) {
		register.className = '';
		setTimeout( transform, transformTime * 0, 0, 10 );
		setTimeout( transform, transformTime * 1, 0, 0 );
		setTimeout( callback, transformTime * 2 );
	}

	/**
	 * On wrong, add class of 'wrong' to the form and wiggle back and forth
	 * 'wrong' class reveals helper text in red and shifts the line under the input to red.
	 */
	function wrong( callback, transformTime = 100 ) {
		register.className = 'wrong';

		// shaking motion
		for ( var i = 0; i < 6; i++ ) {
			setTimeout( transform, transformTime * i, ( i % 2 * 2 - 1 ) * 20, 0 );
			setTimeout( transform, transformTime * 6, 0, 0 );
			setTimeout( callback, transformTime * 7 );
		}
	}

	/**
	 * @todo Get the city and the state based on the zip code and add to the form data
	 *
	 * @note We COULD just do this in the Google Apps Script function?
	 */


}( questions, onComplete ))


</script>
