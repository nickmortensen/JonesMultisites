const randomNumber = ( min, max ) => {
	min = Math.ceil( min );
	max = Math.floor( max );
	return Math.floor( Math.random() * ( max - min ) ) + min;
};

const randomA = randomNumber( 1, 5 );
const randomB = randomNumber( 1, 5 );
// const formContainer          = document.getElementById( 'contact-form-holder' );
const testformButton         = document.getElementById( 'testformButton' );
const addTheseLabel = document.getElementById( 'addTheseLabel' ) || '';
const addTheseInput = document.getElementById( 'addthese' );
// do not display the send button until the math problem for #addthese is solved correctly;

// const phptest               = '\<\?php echo "<h1>USING</h1>"; \?\>';
const mathQuestion          = `Add ${randomA} + ${randomB}`;
const infoIcon              = `<i class = "pl-2 material-icons" data-tooltip = "we ask you to solve this problem to eliminate robot responses to this form"> info </i>`;
const details               = mathQuestion + infoIcon;
addTheseLabel.innerHTML     = details;
addTheseInput.setAttribute( 'placeholder', mathQuestion );
addTheseInput.classList.add( 'sm:w-screen' );
addTheseInput.classList.add( 'md:w-1/4' );
addTheseInput.classList.add( 'placeholder-transparent' );
addTheseInput.classList.add( 'md:placeholder-red-700' );
const correct           = Number( randomA ) + Number( randomB );

addTheseInput.addEventListener( 'focus', e => {
	e.target.classList.add( 'bg-blue-300' );
	addTheseInput.setAttribute( 'placeholder', '' );
});

function showButton( e ) {
	if ( Number( e.target.value ) === Number( correct ) ) {
		setTimeout( 1500, testformButton.classList.replace( 'button_hide', 'button_show' ) );
	}
}
addTheseInput.addEventListener( 'input', showButton, true );
