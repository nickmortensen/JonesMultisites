<?php
/**
 * Template part for displaying an experimental form
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>


<script>
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
		answer: '',
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
	{
		question:"What is your Company Name?",
		attributes: {
			autocomplete: 'organization',
			form: 'experimentalForm',
			tabIndex: 30,
			name: 'companyName',
			title: 'your company name.',
			description: 'Company Name',
		},
		answer: 'General Industrial Inc',
		isLast: false,
	},
	{
		question:"What is your Position?",
		attributes: {
			autocomplete: 'organization-title',
			form: 'experimentalForm',
			tabIndex: 40,
			name: 'jobTitle',
			title: 'your job title.',
			description: 'Position',
		},
		answer: 'Logistics Specialist',
		isLast: false,
	},
	{
		question:"What is your email?",
		attributes: {
			autocomplete: 'email',
			form: 'experimentalForm',
			pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
			tabIndex: 50,
			name: 'emailAddress',
			title: 'a valid email address',
			description: 'Email',
		},
		answer: 'twentyeight@january.com',
		isLast: false,
	},
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
</script>

<section data-gridarea="fpcontact" data-scrollto="contact" id="contact-form">
	<div id="single-field-form">
		<h2 class="translate-center-x">Contact</h2>
		<!-- <form style="padding-left: 40%;" action="get" name="experimentalForm" id="experimentalForm"></form> -->
		<form action="get" name="experimentalForm" id="experimentalForm"></form>
		<div id="progress"></div>

		<div class="center flex justify-center align-items-center">
			<div id="register">

				<div class="register-first-child"></div>

				<div class="register-middle-child">
					<span id="previousButton" class="material-icons">person</span>
					<div id="inputContainer">
						<input data-identifier="" id="inputField" required />
						<label for="inputField" id="inputLabel"></label>
					</div><!-- end div#inputcontainer -->
					<div id="inputShowProgress">
						<!-- <span class="hide error-text"></span> -->
						<span class="hide error-text"></span>
					</div>
				</div>

				<div class="register-last-child flex justify-center align-items-center">
					<span id="forwardButton" class="material-icons" title="advance to the next form field" >arrow_forward</span>
				</div>

			</div><!-- end div#register -->

		</div><!-- end div.center -->
	</div>
</section>




<script>
const sheetInfo = {
	googleWebApp: 'https://script.google.com/macros/s/AKfycbz6hC2Fw34eoOrnOwJ_V4cbAFDfeDe8niMTb1f1AV7btoeEpZsD/exec',
	sheetUrl: '',
};
let formData = []; // will fill up as form is worked through; establishing right now

/**
 *
 *
 * @todo Set the fields so the text shrinks when x amount of characters are pressed
 * @todo Make the textarea field grow ad shrink
 */

const material = [ 'arrow_forward', 'arrow_backward', 'person' ];

const register          = document.getElementById( 'register' );
const progress          = document.getElementById( 'progress' );
const forwardButton     = document.getElementById( 'forwardButton' );

const previous = document.querySelector( 'span #previousButton' );
const next     = forwardButton;

const isLastQuestion = ( a = position, b = questions ) => Number( a + 1 ) === b.length;
const onComplete = formData => {

};

/* Do something after the questions have been answered */
let onComplete = function( formData ) {
	let firstName = '';
	firstName       = questions[0].answer.split( /(?<=^\S+)\s/ )[0];
	let messageSentText = `Thanks for reaching out, ${firstName}.`;
	let responseElement = document.createElement( 'h1' );
	// Instantiate a FormData Object
	// @link https://developer.mozilla.org/en-US/docs/Web/API/FormData
	// @link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/fromEntries
	let newData    = new FormData();

	// let dataToSend = JSON.stringify( Object.fromEntries( formData ) );

	// Place newly collected data into the newData object.
	formData.forEach( datum => newData.append( datum[0], datum[1] ) );

	// Send the newData Object and all of the data to a Google Sheet desginated to collect this data
	fetch( sheetInfo.googleWebApp, { method: 'POST', body: newData } )
		// Once the data has been added to the spreadsheet, acknowledge the form was filled out to the user
		responseElement.appendChild( document.createTextNode( `${messageSentText}` ) );
		// give it a second and then fade it in
		setTimeout( function() {
				register.parentElement.appendChild( responseElement );
				setTimeout( function() {
					responseElement.style.opacity = 1;
				}, 50 )
			}, 100 );
		.then( response => { console.log( 'data sent successfully!' ) } )
		.catch( error => console.error( 'Error!', error.message ) );
};

( function( questions, onComplete ) {

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
		// Gaurd clause - don't do anything if we are still on the first question and it is clicked -- ( though it should not be there anyway )
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
		// [ previous, next ] = inputField.parentNode.parentElement.querySelectorAll( 'span' );



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

function makeResponseObject( formData ) {
	return Object.fromEntries( formData );
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
		let spanInvalid       = inputShowProgress.querySelector( 'span' );
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

// Add each field and result as a multidimensional array to the already existing form object.
function addData() {
	let {name, value} = inputField;
	formData.push([name, value]);
	console.log(formData, 'is the current formData');
}

// Helpers
function hideCurrent( callback ) {
	inputContainer.style.opacity       = 0;
	inputLabel.style.marginLeft        = 0;
	inputShowProgress.style.width      = 0;
	inputShowProgress.style.transition = 'none';
	inputContainer.style.border        = null;
	setTimeout( callback, waitTime );
}

function showCurrent( callback ) {
	inputContainer.style.opacity       = 1;
	inputShowProgress.style.transition = '';
	inputShowProgress.style.width      = '100%';
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


</script>