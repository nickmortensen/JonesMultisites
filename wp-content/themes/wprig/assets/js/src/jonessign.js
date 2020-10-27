/**
 * Data for the locations of Jones Sign Company -- according to the localize script, the main object is called 'locationInfo'
 *
 */

/* eslint-disable */
const locations = JSON.parse( jonesignInfo.locations ); // the PHP outputs the data as json-like, but it is really a string.
// console.table( locations );
// Setup
var outerWear = "T-Shirt";

function timesFive( num ) {
	return num + 3;
}

myOutfit();
