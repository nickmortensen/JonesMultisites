'use strict';
/* eslint-disable max-len, no-unused-vars */
/**
 * last update September 30th, 2020
 * this is better found in htdocs/jonessign.io/wp-content/themes/wprig/inc/corona
 * Create a simplified array of information.
 */
const getSimplifiedData = data => {
	const info = [];
	data.features.forEach( day => {
		const unix        = day.attributes.LoadDttm;
		const formatted   = getFormattedDate( unix );
		const hyphenated  = getDateHyphenated( unix );
		const information = {
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
	});
	return info;
};

// FETCH
( function() {
	const county  = 55009; //Brown County GEOID

	// Brown County Corona Virus Information
	const corona  = `https://dhsgis.wi.gov/server/rest/services/DHS_COVID19/COVID19_WI/FeatureServer/10/query?where=GEOID%20%3D%20${county}&outFields=GEOID,GEO,NAME,NEGATIVE,POSITIVE,HOSP_YES,HOSP_NO,HOSP_UNK,POS_FEM,POS_MALE,POS_OTH,POS_10_19,POS_20_29,POS_30_39,POS_40_49,POS_50_59,POS_60_69,POS_70_79,POS_80_89,POS_90,DEATHS,DTHS_MALE,DTHS_OTH,DTHS_0_9,DTHS_10_19,DTHS_30_39,DTHS_40_49,DTHS_50_59,DTHS_60_69,DTHS_70_79,DTHS_80_89,DTHS_90,POS_AIAN,POS_ASN,POS_BLK,POS_WHT,POS_MLTOTH,POS_UNK,POS_E_HSP,POS_E_NHSP,POS_E_UNK,DTH_AIAN,DTH_ASN,DTH_BLK,DTH_WHT,DTH_MLTOTH,DTH_UNK,DTH_E_HSP,DTH_E_NHSP,DTH_E_UNK,POS_HC_Y,POS_HC_N,POS_HC_UNK,DTH_NEW,POS_NEW,NEG_NEW,TEST_NEW,DATE,OBJECTID&outSR=4326&f=json`;
	fetch( corona )
		.then( response => {
			if ( ! response.ok ) {
				throw Error( response.statusText );
			}
			return response.json();
		})
		.then( response => {
			const simplified = getSimplifiedData( response );
			// updateUISuccess( response );
			const totalDays = simplified.length;
			const yesterday = simplified[ totalDays - 2 ];
			const today     = simplified[ totalDays - 1 ];
			// const time       = today.time;
			const totalCases = today.positive;
			const newlyDead  = getNewDeaths( today, yesterday );
			const newCases   = getNewCases( today, yesterday );
			const totalDeaths = today.dead;

			console.log( 'total deaths:', totalDeaths );
			console.log( 'total cases: ', totalCases );
			console.log( 'new cases:', newCases );
			console.log( 'new dead: ', newlyDead );
			const html = createDateInput( today );
			document.getElementById( 'weather' ).innerHTML = html;
		})
		.catch( error => {
			console.log( error );
		}); //end fetch()
}() );

const getFormattedDate = unixTime => {
	const options = { weekday: 'long', year: 'numeric', month: 'long', day: '2-digit' };
	return new Date( unixTime ).toLocaleDateString( 'en-US', options );
};

/**
 * Returns a formatted date in the following format 'YYYY-MM-DD'
 *
 * @param int ticks Unix Time for the current day.
 */
const getDateHyphenated = ticks => {
	const d            = new Date( ticks );
	const day          = d.getDate() + 1;
	const dayLeading   = 9 < day ? day : `0${day}`;
	const month        = d.getMonth() + 1;
	const monthLeading = 9 < month ? month : `0${month}`;
	const year         = d.getFullYear();
	const dateArray = [ year, monthLeading, dayLeading ];
	return dateArray.join( '-' );
};

/**
 * Creates an input field of the type 'date'.
 *
 * @param int today Unix Time for the current day.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/date
 */
const createDateInput = today => {
	const mostRecent = getDateHyphenated( today.LoadDttm );
	return `
	<label for="start">Day</label>
	<input
	type="date"
	id="start"
	name="start"
	min="2020-03-15"
	value="${mostRecent}"
	max="${mostRecent}"/>`;
};

const getTotalDays = response => response.features.length(); // Total Amount of days that have been counted.
const getNewCases  = ( a, b ) => 1 > Math.abs( a.positive - b.positive ) ? Math.abs( a.positive - b.positive ) : 0; // new cases since yesterday
const getNewDeaths = ( a, b ) => 1 > Math.abs( a.dead - b.dead ) ? Math.abs( a.dead - b.dead ) : 0; // new deaths since yesterday

/**
 * Build html <options> for a datalist field.
 *
 * @param {array} inputArray An array within which I will build an html options array.
 */
const createDataListOptions = inputArray => {
	const options = [];
	inputArray.forEach( item => {
		const identifier = item.objectId;
		const dateArray  = getFormattedDate( item.time.unix ).split( ', ' );
		options.push( `<option data-id="${identifier}" value="${dateArray[ 1 ]}">` );
	});// end days.forEach
	return options.join( '\n\t\t' );
};

/**
 * Output a datalist field with options.
 *
 * @param {string} options The options for the datalist field.
 */
const createDataList = options => {
	const output = `
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
 *
 * @param {int} county The numeric ID of the county we want.
 */
function createRequest( county ) {
	// const fields        = [ 'GEOID', 'GEO', 'NAME', 'NEGATIVE', 'POSITIVE', 'HOSP_YES', 'HOSP_NO', 'HOSP_UNK', 'POS_FEM', 'POS_MALE', 'POS_OTH', 'POS_10_19', 'POS_20_29', 'POS_30_39', 'POS_40_49', 'POS_50_59', 'POS_60_69', 'POS_70_79', 'POS_80_89', 'POS_90', 'DEATHS', 'DTHS_MALE', 'DTHS_OTH', 'DTHS_0_9', 'DTHS_10_19', 'DTHS_30_39', 'DTHS_40_49', 'DTHS_50_59', 'DTHS_60_69', 'DTHS_70_79', 'DTHS_80_89', 'DTHS_90', 'POS_AIAN', 'POS_ASN', 'POS_BLK', 'POS_WHT', 'POS_MLTOTH', 'POS_UNK', 'POS_E_HSP', 'POS_E_NHSP', 'POS_E_UNK', 'DTH_AIAN', 'DTH_ASN', 'DTH_BLK', 'DTH_WHT', 'DTH_MLTOTH', 'DTH_UNK', 'DTH_E_HSP', 'DTH_E_NHSP', 'DTH_E_UNK', 'POS_HC_Y', 'POS_HC_N', 'POS_HC_UNK', 'DTH_NEW', 'POS_NEW', 'NEG_NEW', 'TEST_NEW', 'DATE', 'OBJECTID' ];
	return {
		initial: 'https://services1.arcgis.com/ISZ89Z51ft1G16OK/ArcGIS/rest/services/COVID19_WI/FeatureServer/10/query?where=',
		id: getEncodedCounty( county ),
		final: '&outFields=GEOID,GEO,NAME,NEGATIVE,POSITIVE,HOSP_YES,HOSP_NO,HOSP_UNK,POS_FEM,POS_MALE,POS_OTH,POS_10_19,POS_20_29,POS_30_39,POS_40_49,POS_50_59,POS_60_69,POS_70_79,POS_80_89,POS_90,DEATHS,DTHS_MALE,DTHS_OTH,DTHS_0_9,DTHS_10_19,DTHS_30_39,DTHS_40_49,DTHS_50_59,DTHS_60_69,DTHS_70_79,DTHS_80_89,DTHS_90,POS_AIAN,POS_ASN,POS_BLK,POS_WHT,POS_MLTOTH,POS_UNK,POS_E_HSP,POS_E_NHSP,POS_E_UNK,DTH_AIAN,DTH_ASN,DTH_BLK,DTH_WHT,DTH_MLTOTH,DTH_UNK,DTH_E_HSP,DTH_E_NHSP,DTH_E_UNK,POS_HC_Y,POS_HC_N,POS_HC_UNK,DTH_NEW,POS_NEW,NEG_NEW,TEST_NEW,DATE,OBJECTID&outSR=4326&f=json',
	};
}
/* eslint-enable max-len */
