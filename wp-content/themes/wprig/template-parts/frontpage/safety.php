<?php
/**
 * Template part for displaying an experimental form
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

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

	<script>

	const spreadsheet = {
		deploymentID: 'AKfycbyzqZytfLv_rOaJSj5GAgJ36FFAcbRCI9CI7QDf7HZhRKFjulJJwEMCFg',
		webApp: 'https://script.google.com/macros/s/AKfycbyzqZytfLv_rOaJSj5GAgJ36FFAcbRCI9CI7QDf7HZhRKFjulJJwEMCFg/exec',
		publishedURL: 'https://docs.google.com/spreadsheets/d/e/2PACX-1vS7nt8C9ylWMKl8-6RZXDlP7UlnsPWb2THG4Dbps2c2wGdlq4m-Vhb3IjFE3WWjJv3uffYhzb1X_6xd/pubhtml',
	};

	const scriptURL = spreadsheet.webApp;

	let timeLossForm          = document.forms.daySinceTimeLostByLocation;
	let timeLosttFormData = new FormData( timeLossForm );
	const {lastIncident, jonesLocation}   = timeLossForm.elements;
	const convertLastIncidentToUnix  = value => Date.parse( value );

	/**
	 * output an object witht he intervening time between now and the last injury recorded.
	 * @param {int} last Unix Timestamp of the last recorded injury date.
	 *
	 */
	const timeSinceInjury = last => {
		let now            = Date.now();
		let timeDifference = Math.abs( (now - last) / 1000 )

		const days = Math.floor( timeDifference / 86400 );
		timeDifference -= days * 86400;

		const hours = Math.floor( timeDifference / 3600 ) % 24;
		timeDifference -= hours * 3600;

		const minutes = Math.floor( timeDifference / 60 ) % 60;
		timeDifference -= minutes * 60;

		return {
			days: days > 0 ? days : 0,
			hours: hours,
			minutes: minutes,
		};
	}


	const convertLastIncident = event => {
		let last = convertLastIncidentToUnix( event.target.value );
		let now  = Date.now();
		let durationSinceInjury = Math.abs( ( Date.now() - Date.parse( event.target.value )) / 1000 );
		console.table( timeSinceInjury(convertLastIncidentToUnix( event.target.value )) );
	};


timeLossForm.addEventListener('submit', e => {
	e.preventDefault()
	fetch(scriptURL, { method: 'POST', body: new FormData(timeLossForm)})
		.then(response => console.log('Success!', response))
		.catch(error => console.error('Error!', error.message))
})








	</script>


