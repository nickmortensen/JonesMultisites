<?php
/**
 * Template part for displaying an experimental form
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

wp_rig()->print_styles( 'wp-rig-safety' );

?>

<style>
	.formfield > label { color: var(--foreground) }

	#safety form {
		display: flex;
		flex-flow: row wrap;
		align-items: center;
		align-content: center;
		justify-content: center;
		margin-bottom: 4em;
	}

	#safety form > div {
		min-width: 48%;
	}

	#safety form > div > select,
	#safety form > div > input {
		height: 60px;
	}

	#sendbutton-container > button,
	#sendbutton-container > input[type="button"] {
		font-size: 22px;
		border: unset;
		border-radius: unset;
		position: absolute;
		padding: unset;
		margin-top: 3vmin;
		overflow: hidden;
		text-transform: uppercase;
		padding: 0.85vmin;
	}


	#sendbutton-container > button:hover {
		background: var(--red);
		color: var(--foreground);
		transition: all 0.3s ease;
	}
</style>

<section data-gridarea="safetyformplusviz" id="safety">
	<div data-gridarea="safetyform" class="safety-form-container">
		<form class="horizontal-form" action="" name="daySinceTimeLostByLocation">
			<div id="date-container" class="formfield">
				<label for="date-last-incident">Last Incident</label>
				<input
				type="datetime-local"
				min="2019-06-01T08:30"
				max="2025-06-30T16:30"
				pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}"
				id="date-last-incident"
				name="lastIncident"
				>
			</div>
			<div id="select-container" class="formfield">
				<label for="jones-location">Location</label>
				<select id="jones-location" name="jonesLocation">
					<option value='grb'>Jones Green Bay</option>
					<option value='mxz'>Jones Juarez</option>
					<option value='las'>Jones Las Vegas</option>
					<option value='msp'>Jones MSP</option>
					<option value='rno'>Jones Reno</option>
					<option value='ric'>Jones Richmond</option>
					<option value='mxt'>Jones Tijuana</option>
					<option value='inst'>Jones Install</option>
				</select>
			</div>
			<div id="sendbutton-container" class="formfield">
				<button class="button" type="submit">Send</button>
			</div>
		</form>
	</div><!-- .safety-form-container -->
	<div data-gridarea="safetyviz" id="safetyVisualizationBlock" class="safety-visualization">

	</div><!-- end div.safety-visualization -->
</section>

	<script>

	const safetyVisualizationBlock = document.querySelector( '#safetyVisualizationBlock' );

	const facilities = {
		las: 'Las Vegas',
		msp: 'Minneapolis/St. Paul',
		mxt: 'Tijuana',
		grb: 'Green Bay',
		rno: 'Reno',
		mxz: 'Juarez',
		inst: 'Install',
		ric: 'Richmond',
	};
	const spreadsheet = {
		deploymentID: 'AKfycbyzqZytfLv_rOaJSj5GAgJ36FFAcbRCI9CI7QDf7HZhRKFjulJJwEMCFg',
		webApp: 'https://script.google.com/macros/s/AKfycbyzqZytfLv_rOaJSj5GAgJ36FFAcbRCI9CI7QDf7HZhRKFjulJJwEMCFg/exec',
		publishedURL: 'https://docs.google.com/spreadsheets/d/e/2PACX-1vS7nt8C9ylWMKl8-6RZXDlP7UlnsPWb2THG4Dbps2c2wGdlq4m-Vhb3IjFE3WWjJv3uffYhzb1X_6xd/pubhtml',
		getDataURL: 'https://docs.google.com/spreadsheets/d/10E61O8aS6rgMkG0h3G1O92ScQ0N4ZGPPTzaTuhuGzZg/edit#gid=0',
	};

	let injuryData = [];

	const { webApp, getDataURL } = spreadsheet;

	let timeLossForm                      = document.forms.daySinceTimeLostByLocation;
	let injuryFormData                    = new FormData( timeLossForm );
	const { lastIncident, jonesLocation } = timeLossForm.elements;
	const convertDateToUnix               = value => Date.parse( value );

	/**
	 * output an object witht he intervening time between now and the last injury recorded.
	 * @param {int} last Unix Timestamp of the last recorded injury date.
	 *
	 */
	const timeSinceInjury = last => {
		let now            = Date.now();
		let timeDifference = Math.abs( (now - last) / 1000 )

		const days      = Math.floor( timeDifference / 86400 );
		timeDifference -= days * 86400;

		const hours     = Math.floor( timeDifference / 3600 ) % 24;
		timeDifference -= hours * 3600;

		const minutes   = Math.floor( timeDifference / 60 ) % 60;
		timeDifference -= minutes * 60;

		return {
			days: days > 0 ? days : 0,
			hours: hours,
			minutes: minutes,
		};
	}

	const convertLastIncident = dateOfInjury => {
		let last                = convertDateToUnix( dateOfInjury );
		let now                 = Date.now();
		let durationSinceInjury = Math.abs( ( Date.now() - Date.parse( dateOfInjury )) / 1000 );
		return timeSinceInjury( convertDateToUnix( dateOfInjury ) );
	};


// let onComplete = formData => {

// }

/**
 * Submit the data from the form to a Google Spreadsheet on click of the submit button;
 */
timeLossForm.addEventListener('submit', e => {
	e.preventDefault()
	fetch( webApp, { method: 'POST', body: formData } )
		.then( response => console.log( 'Success!', response ) )
		.catch( error => console.error( 'Error!', error.message ) )
})


function individualLocationHTML( injuryDetail ) {
	const { name, sinceInjury: { days, hours, minutes } , minutes: totalminutes } = injuryDetail;
	return `
		<div data-msli="${totalminutes}" class="single-location-injury-time-elapsed">
			<span class="light-text">${name}</span>
			<span class="light-text">${days} DAYS ${hours} HOURS ${minutes} MINUTES</span>
		</div>`;
}

let safetyLocations = [];

var publishInjuryData = function( error, options, response ) {
	let injuryDetails           = []; // Empty array to add information into
	let injuriesSpreadsheetData = []; // Empty array to add information into
	if ( ! error ) {
		let rows = response.rows.slice(1); // lose the header row from the spreadsheet
		/* Take the most recent injury from each location */
		// Reversing the array ensures we only select the most recent entry for a given city/location within the spreadsheet
		rows.forEach( row => injuriesSpreadsheetData.push( row.cellsArray.reverse() ) );

		const injuriesObject = Object.fromEntries( injuriesSpreadsheetData );
		const abbreviates    = Object.keys( facilities );
		abbreviates.forEach( abbrev => {
			let fullname          = facilities[ abbrev.toString() ];
			let lastInjury        = injuriesObject[ abbrev.toString() ];
			let intervalInMinutes = Math.floor( ( ( Date.now() - convertDateToUnix( lastInjury ) ) / 1000 ) / 60 );
			injuryDetails.push({
				name: fullname,
				sinceInjury: convertLastIncident( lastInjury ),
				minutes: intervalInMinutes,
			});
		})
	}

	let arrayOfInjuryHTML = [];
	injuryDetails.forEach( injuryDetail => arrayOfInjuryHTML.push( individualLocationHTML( injuryDetail ) ) );

	// Add each div with the respective location to the Visualization Block;
	safetyVisualizationBlock.innerHTML = arrayOfInjuryHTML.join('');
}


// https://docs.google.com/spreadsheets/d/10E61O8aS6rgMkG0h3G1O92ScQ0N4ZGPPTzaTuhuGzZg/edit?usp=sharing

/**
 * Call on the spreadsheet to output data from itself.
 */
$().sheetrock( {
	url: getDataURL,
	query: "select B,C",
	callback: publishInjuryData,
	labels: [ 'lastInjury', 'jonesLocation' ],
} );





	</script>


