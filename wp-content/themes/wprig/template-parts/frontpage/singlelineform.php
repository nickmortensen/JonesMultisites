<?php
/**
 * Template part for displaying an experimental form
 *
 * @package wp_rig
 * @last update 27_January_2021
 */

namespace WP_Rig\WP_Rig;

?>

<style>

:root {
	--box-shadow:
		0 16px 24px 2px rgba(0, 0, 0, 0.14),
		0 6px 30px 5px rgba(0, 0, 0, 0.12),
		0 8px 10px -5px rgba(0, 0, 0, 0.3);
	--mail-unicode: "\2709";
	--arrow-unicode: "\2794";
	--person-unicode: "\1F464";
	--progress-color: var(--blue-800);
	--icon-color: var(--gray-400);
	--incorrect: var(--red-700);
	--contact-form-minheight: 600px;
	--contact-form-gridarea: fp-contactform;
	--label-moved-top: -36px;
	--label-moved-left: 36px
}

/* disappear the form html -- only needed for typing the send button into it */
form {
	position: absolute;
	top: 0;
	left: 0;
	margin: 0;
	overflow-x: hidden;
	background: #0273b9;
	background: var(--blue-600);
	font-family: -apple-system, system-ui, sans-serif;
	font-family: var(--font-stack);
	height: 0;
	z-index: 1;
}

.contact_form_frontpage {
	grid-area: var(--contact-form-gridarea);
	z-index: 20;
	position: relative;
	min-height: var(--contact-form-minheight);
	min-width: 100%;
	background: var(--blue-600);
}

#progressMeter {
	z-index: 30;
	position: absolute;
	top: 3%;
	left: 0;
	display: flex;
	min-height: 94%;
	min-width: 100%;
	background: var(--yellow-600);
	transform: translateX(-90%);
}

#singleLineForm {}

.contact_form_frontpage h1 {
	opacity: 0;
	color: var(--background);
}

.centered {
	position: absolute;
	z-index: 31;
	height: 90%;
	width: 90%;
	top: 5%;
	left: 5%;

	display: grid;
	grid-template-columns: 1fr 4fr 1fr;
	grid-template-rows: 2fr 1fr 2fr;
	grid-template-areas:
		"formabove formabove formabove"
		"formcontainerleft formcontainer formcontainerright"
		"formbelow formbelow formbelow";
}

#inputContainer {
	min-height: 220px;
	grid-area: formcontainer;
	grid-gap: 1px;
	background: var(--gray-100);
	box-shadow: var(--box-shadow);
	display: grid;
	grid-template-columns: 1fr 8fr 2fr;
	grid-template-rows: 2fr 3fr 3fr;
	grid-template-areas:
		"left inputtop next"
		"left input next"
		"left inputfeedback next"
}



#previousIcon {
	grid-area: inputtop;
	justify-self: start;
	align-self: center;

}

#nextButton {
	grid-area: next;
	place-self: center;
	font-size: 100px;
}

#containsInput {
	position: relative;
	height: 1.5rem;
	grid-area: input;
	width: 100%;
	height: 100%;
	border-bottom: 2px solid var(--red-500);
	background: var(--indigo-300);
}

#containsInput > input,
#containsInput > label {
	position: absolute;
	bottom: 0;
	left: 0.5rem;
}


#containsInput:focus-within > input {
	height: 100%;
	z-index: 44;
	transition: all 0.3s 0.4s ease;
}

#containsInput:focus-within > label {
	font-size: var(--fontsize-xs);
	top: -40px;
	left: 28px;
	transition: all 0.3s ease;
}


#containsInput > input {
	height: 0px;
	line-height: 1;
	outline: 0;
	border: none;
	z-index: 41;
	padding-bottom: 0;
	margin-bottom: 0;
}

#containsInput > label {
	z-index: 42;
	transition: all 0.3s ease;
}

/* #containsInput > input:focus + label,
#containsInput > input:valid + label {
	top: var(--label-moved-top);
	left: var(--label-moved-left);
	font-size: var(--fontsize-xs);
	transition: all 0.4s ease-out;

} */

span#progressInput {
	grid-area: inputfeedback;
	border-top: 4px solid var(--blue-600);
	width: 100%;
	height: 100%;
	z-index: 50;
}
.contact_form_frontpage {}
</style>

<script>
	const icons = [ 'person', 'keyboard_backspace', 'arrow_forward', 'send' ];
const sheetInfo = {
	googleWebApp: 'https://script.google.com/macros/s/AKfycbz6hC2Fw34eoOrnOwJ_V4cbAFDfeDe8niMTb1f1AV7btoeEpZsD/exec',
	sheetUrl: '',
};

let formData = [];
let questions = [
	{
		question: 'What is your name?',
		attributes: {
			autocomplete: 'given-name',
			form: 'singleLineForm',
			tabIndex: 10,
			name: 'fullName',
			title: 'Please input a first and last name.',
		},
		answer: '',
	},
	{
		question:"What is your zip code?",
		attributes: {
			autocomplete: 'postal-code',
			form: 'singleLineForm',
			tabIndex: 20,
			name: 'zip',
			title: 'Enter your 5 digit zipcode, we\'ll figure out your city and state based on it.',
		},
		answer: '54688',
	},
	{
		question:"What is your Company Name?",
		attributes: {
			autocomplete: 'organization',
			form: 'singleLineForm',
			tabIndex: 30,
			name: 'companyName',
			title: 'What is the name of the company you are with?'
		},
		answer: 'Top of the top BoxingFoxy Boxing Inc',
	},
	{
		question:"What is your Position?",
		attributes: {
			autocomplete: 'organization-title',
			form: 'singleLineForm',
			tabIndex: 40,
			name: 'jobTitle',
			title: 'Enter your job title or position within your company.',
		},
		answer: 'Head Man in Charge',
	},
	{
		question:"What is your email?",
		attributes: {
			autocomplete: 'email',
			form: 'singleLineForm',
			pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
			tabIndex: 50,
			name: 'emailAddress',
			title: 'Please enter a valid email address',
		},
		answer: 'moreover@boxing.com',
	},
	{
		question:"What is your message",
		attributes: {
			form: 'singleLineForm',
			tabIndex: 60,
			name: 'inquiry',
			title: 'Let us know what you are looking for.',
		},
		answer: 'A longer message would go here',
	}
];
</script>


<section class="contact_form_frontpage">
	<form action="get" name="singleLineForm" id="singleLineForm"></form>
	<div id="progressMeter"></div>
	<div class="centered">
		<div id="inputContainer">
			<span id="previousIcon" class="material-icons">person</span>
			<div id="containsInput">
				<input id="fieldInput" />
				<label id="fieldLabel" for="fieldInput"></label>
			</div>
			<span id="nextButton" class="material-icons">arrow_forward</span>
			<span id="progressInput"></span>
		</div>
	</div>



</section>

<script>

const register      = document.getElementById( 'inputContainer' );
const progress      = document.getElementById( 'progressMeter' );
const forwardButton = document.getElementById( 'nextButton' );



/* Do something after the questions have been answered */
let onComplete = function( formData ) {
	let newData    = new FormData();

	// @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/fromEntries
	// let dataToSend = JSON.stringify( Object.fromEntries( formData ) );

	formData.forEach( datum => {
		newData.append( datum[0], datum[1] );
	})

	fetch( sheetInfo.googleWebApp, { method: 'POST', body: newData } )
		.then( response => {
			const h1 = document.createElement('h1');
			// @see https://stackoverflow.com/questions/10272773/split-string-on-the-first-white-space-occurrence/10272822
			h1.appendChild( document.createTextNode( `Thanks for reaching out, ${questions[0].answer.split( /(?<=^\S+)\s/ )[0]}` ) );

			setTimeout( function() {
				register.parentElement.appendChild( h1 )
				setTimeout( function() {
					h1.style.opacity = 1;
				}, 50 )
			}, 500 );
		} )
		.catch( error => console.error( 'Error!', error.message ) );
}

;( function( questions, onComplete ) {

	const transformTime = 100 // transition transform time from #register in ms
	const waitTime      = 200 // transition width time from #register in ms
	const eTime         = 1000 // transition width time from inputLabel in ms

	// init
	// --------------
	// guard clause - if there are no questions, then return.
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

	// If you strike the enter key while you are focused in the input field, it will validate and advance to the next input
	inputField.addEventListener( 'keyup', function( e ) {
		transform( 0, 0 ); // ie hack to redraw
		// 13 is 'enter'
		13 === e.keyCode && validate();
	})

	// Go back a field if you hit the back arrow.
	previousIcon.addEventListener( 'click', function( e ) {
		// Gaurd clause - don't do anything if we are still on the first question and it is clicked -- ( though it should not be there anyway )
		if ( position === 0 ) {
			return;
		}
		// Set the position variable to one less than it currently is and move to that question as the input
		position -= 1;
		hideCurrent( putQuestion );
	} )


	// Functions
	// --------------

	// Load next question
	function putQuestion() {
		let inputLabel   = document.getElementById( 'fieldLabel' );
		let inputfield   = document.getElementById( 'fieldInput' );
		let fieldName    = questions[position].attributes.name;
		let tabIndex     = questions[position].attributes.tabIndex || 34;
		let formId       = questions[position].attributes.form || 'singleLineForm';
		let title        = questions[position].attributes.title || '';
		let autocomplete = questions[position].attributes.autocomplete || '';
		// const finalQuestion = isLastQuestion( position, questions );
		inputLabel.textContent = questions[position].question;
		inputField.type      = questions[position].type || 'text';
		inputField.value     = questions[position].answer || '';
		inputField.name      = fieldName;
		inputField.setAttribute( 'data-identifier', fieldName );
		inputField.setAttribute( 'tabindex', tabIndex );
		inputField.setAttribute( 'form', formId );
		inputField.setAttribute( 'title', title );
		inputField.setAttribute( 'autocomplete', autocomplete );

		// arrow fields for forward and backward should have tabindices
		let [ previous, next ] = [...inputField.parentNode.parentElement.querySelectorAll( 'span' ) ];
		previous.setAttribute( 'tabindex', tabIndex - 1 );
		next.setAttribute( 'tabindex', tabIndex + 1 );

		inputField.focus();

		// set the progress of the background
		progress.style.width     = position * 100 / questions.length + '%';
		previousIcon.className = position ? '' : 'person-symbol';

		showCurrent();


	}

// See whether we are on the last question or not
const isLastQuestion = () => Number( position + 1 ) === questions.length;

function makeResponseObject( formData ) {
	return Object.fromEntries(formData);
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

function addData() {
	let {name, value} = inputField;
	formData.push([name, value]);
	console.log(formData, 'is the current formData');
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





// let send = document.querySelector( '.sendToSpreadsheet' );
// send.addEventListener( 'click',  e => {
// 	e.preventDefault();
// 	fetch( sheetInfo.googleWebApp, { method: 'POST', body: formDataObject})
// 		.then(response => console.log('Success!', response ))
// 		.catch(error => console.error('Error!', error.message))
// })

// document.getElementById( 'submitFormButton').addEventListener('submit', e => {
// 	e.preventDefault();
// 	fetch( sheetInfo.googleWebApp, { method: 'POST', body: formDataObject})
// 		.then(response => console.log('Success!', response ))
// 		.catch(error => console.error('Error!', error.message))
// })
</script>
