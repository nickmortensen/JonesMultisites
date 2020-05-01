'use strict';

/**
 * this is better found in htdocs/jonessign.io/wp-content/themes/wprig/inc/corona
 * Create a simplified array of information.
 */
const getSimplifiedData = data => {
	const info = [];
	data.features.forEach( day => {
		let unix        = day.attributes.LoadDttm;
		let formatted   = getFormattedDate( unix );
		let hyphenated  = getDateHyphenated( unix );
		let information = {
			time: {
				unix,
				formatted,
				hyphenated,
			},
			objectId: day.attributes.OBJECTID,
			negative: day.attributes.NEGATIVE,
			positive: day.attributes.POSITIVE,
			dead: day.attributes.DEATHS,
		};
		info.push( information );
	})
	return info;
}

// FETCH
( function() {
	const county  = 55009; //Brown County GEOID

	// Brown County Corona Virus Information
	const corona  = 'https://services1.arcgis.com/ISZ89Z51ft1G16OK/ArcGIS/rest/services/COVID19_WI/FeatureServer/10/query?where=GEOID%20%3D%20%2755009%27&outFields=OBJECTID,GEOID,GEO,NAME,LoadDttm,NEGATIVE,POSITIVE,DEATHS&outSR=4326&f=json';


	// const request = `${requestObject.initial}${requestObject.id}${requestObject.final}`;

	fetch( corona )
		.then( response => {
			if ( ! response.ok ) {
				throw Error( response.statusText );
			}
			return response.json();
		})
		.then( response => {

			let simplified = getSimplifiedData(response);
			// updateUISuccess( response );
			let totalDays  = simplified.length;
			let yesterday  = simplified[totalDays - 2];
			let today      = simplified[totalDays - 1];
			let time       = today.time;
			let newCases   = getNewCases( today, yesterday );
			let totalCases = today.positive;
			let newlyDead  = getNewDeaths( today, yesterday )
			let totalDeaths = today.dead;
			console.log( 'total deaths', totalDeaths );
			console.log( 'new cases', newCases );
			let selectOptions = createDataListOptions( simplified );
			// let html = createDataList(selectOptions);
			let html = createDateInput( today );
			document.getElementById( 'weather' ).innerHTML = html;
		} )
		.catch( error => {
			console.log( error );
		} ); //end fetch()



})();



/**
 * Handle the XHR success
 */
const updateUISuccess = response => {
	let days = response.features;
	let selectOptions = createDataListOptions( days );
	let html = createDataList(selectOptions);

	document.getElementById( 'weather' ).innerHTML = html;
};

/**
 * Handle XHR Error
 */
const updateUIError = () => document.getElementById( 'weather' ).className = 'hidden';


const getC    = input => input - 273.15; //Celsious from Kelvin input
const getF    = input => input * 1.8 + 32; //Farenheit from Celsius

const getFormattedDate = unixTime => {
	let options = { weekday: 'long', year: 'numeric', month: 'long', day: '2-digit' };
	return new Date( unixTime ).toLocaleDateString( 'en-US', options );
}

/**
 * Returns a formatted date in the following format 'YYYY-MM-DD'
 *
 * @param int ticks Unix Time for the current day.
 */
const getDateHyphenated = ticks => {
	let d            = new Date(ticks);
	let day          = d.getDate() + 1;
	let dayLeading   = 9 < day ? day    : `0${day}`;
	let month        = d.getMonth() + 1;
	let monthLeading = 9 < month ? month: `0${month}`;
	let year         = d.getFullYear();
	let dateArray = [ year, monthLeading, dayLeading ];
	return dateArray.join( '-' );
}

/**
 * Creates an input field of the type 'date'.
 *
 * @param int today Unix Time for the current day.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/date
 */
const createDateInput = today => {
	let mostRecent = getDateHyphenated( today.LoadDttm );
	return `
	<label for="start">Day</label>
	<input
	type="date"
	id="start"
	name="start"
	min="2020-03-15"
	value="${mostRecent}"
	max="${mostRecent}"/>`;
}


const getTotalDays = response => response.features.length(); // Total Amount of days that have been counted.
const getNewCases  = ( a, b ) => 1 > Math.abs( a.positive - b.positive ) ? Math.abs( a.positive - b.positive ) : 0; // new cases since yesterday
const getNewDeaths = ( a, b ) => 1 > Math.abs( a.dead - b.dead ) ? Math.abs( a.dead - b.dead ) : 0; // new deaths since yesterday

const getDead = input => `There are ${input} Dead in Brown County`;


/**
 * Build html <options> for a datalist field.
 *
 * @param {array} inputArray An array within which I will build an html options array.
 */
const createDataListOptions = inputArray => {
	const options = [];
	inputArray.forEach( item => {
		let identifier = item.objectId;
		let dateArray  = getFormattedDate( item.time.unix ).split( ', ' );
		options.push( `<option data-id="${identifier}" value="${dateArray[1]}">` );
	} ) // end days.forEach
	return options.join( "\n\t\t" );
};

/**
 * Output a datalist field with options.
 *
 * @param {string} options The options for the datalist field.
 */
const createDataList = options => {
	let output = `
	<label for="days_tracked">Pick a Date</label>
	<input list="corona_days" name="days_tracked"/>
	<datalist id="corona_days">
		${options}
	</datalist>`;
	return output;
};

/**
 * URL encode the county attribute in the fetch Request
 */
const getEncodedCounty = id => encodeURI( `GEOID = '${id}'` );

/**
 * Create the request url
 */

 const createRequest = ( county ) => {
	const fields = ['OBJECTID','GEOID','GEO','NAME','LoadDttm', 'NEGATIVE', 'POSITIVE', 'DEATHS']
	const requestObject = {
		initial: 'https://services1.arcgis.com/ISZ89Z51ft1G16OK/ArcGIS/rest/services/COVID19_WI/FeatureServer/10/query?where=',
		id: getEncodedCounty( county ),
		final: '&outFields=OBJECTID,GEOID,GEO,NAME,LoadDttm,NEGATIVE,POSITIVE,DEATHS&outSR=4326&f=json',
	};
	return requestObject;
 };

