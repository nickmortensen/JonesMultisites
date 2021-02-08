/* eslint-disable no-unused-vars, no-undef */
const CLIENT_SECRET = 'rY8_2iu6BAVo02T4DGPuDMEi';
const sheets = [
	{
		title: 'Time Loss Injury',
		id: '16N9qk11V-Hd_VEp171MgQzSKGPuOwUBGcZcBZZ8yFTM',
		publish: '2PACX-1vQ2EpAPm4w-kfB2933mCHdBGGxRg59dUfRRXrMpxGc6VRs6A37TqykfaystptD9iTPlRujZJHbihoho',
		pages: 1,
	},
	{
		title: 'Jonessign.io Inquiries',
		id: '1bt0CC5tmraOCEWL9ZDTAK4u72V432OPC8cj6Wv39VRQ',
		publish: '2PACX-1vS-8RSt8SwgdbLH8t2eTxRgzljulDpzZfiECdf9nKulAYbiCJFrRC16mUAsZwdTIZ_uRfDdAheRQpBn',
		pages: 1,
	},
	{
		title: 'Weekly Estimates',
		id: '1JkoXp5PNSzxLcgN0RHhDY4k-8TF450yILtq-HltjuSc',
		publish: '2PACX-1vTzgJB2g1kkWGBHp2k7mDFvM6I1xwueNjXe0tH8HIO9tNgUbjf2u7fEas4gl6xICeJpsrUt6NAsXpdl',
		pages: 1,
	},
	{
		title: 'Install Photos',
		id: '1EZhUNNPx0gM-aYCYK-2biwVCv_HZT4xLj87iafqsFsM/',
		publish: '2PACX-1vSFxJmAgzzEKiqLPDYWng5sZff4-iw5IEphP8xiL4CUxOuOeRXaYKa2ZgeO7DWhFHkIbO7T2OdxYIT5',
		pages: 1,
	},
	{
		title: 'title is empty',
		id: '1111',
		publish: '11111',
		pages: 1,
	},
];

const createRequest   = { id, pages } = sheetData => `https://spreadsheets.google.com/feeds/cells/${id}/${pages}/public/full?alt=json`;
const getPublishedUrl = { publish } = sheetData => `https://docs.google.com/spreadsheets/d/e/${publish}/pubhtml`;
const getsharedUrl    = { id } = sheetData => `https://docs.google.com/spreadsheets/d/${id}/edit?usp=sharing`;
const getEditUrl      = { id } = sheetData => `https://docs.google.com/spreadsheets/d/${sheetData.id}/edit#gid=0`;
const v4Request       = { id } = sheetData => `https://sheets.google.apis.com/v4/spreadsheets/${id}`;

console.log( '%c You are loading the sheets discovery js', 'color: #ffc600; background: #0273b9' );

function makeApiCall() {
	const params = {
		// The spreadsheet to request.
		spreadsheetId: sheets[ 0 ].id,  // TODO: Update placeholder value.

		// The ranges to retrieve from the spreadsheet.
		ranges: [ 'A2:F20' ],  // TODO: Update placeholder value.

		// True if grid data should be returned.
		// This parameter is ignored if a field mask was set in the request.
		includeGridData: false,  // TODO: Update placeholder value.
	};

	const request = gapi.client.sheets.spreadsheets.get( params );
	request.then( function( response ) {
		// TODO: Change code below to process the `response` object:
		console.log( response.result );
	}, function( reason ) {
		console.error( 'error: ' + reason.result.error.message );
	});
}

function initClient() {
	const API_KEY       = 'AIzaSyB2ASOHSYlXGr0mq4UGBeXU1wc5BQTPfvo';
	const CLIENT_ID     = '265155243495-d3gnnvr0t52u2pjmld911r0a6bae2uje.apps.googleusercontent.com';

	// TODO: Authorize using one of the following scopes:
	//   'https://www.googleapis.com/auth/drive'
	//   'https://www.googleapis.com/auth/drive.file'
	//   'https://www.googleapis.com/auth/drive.readonly'
	//   'https://www.googleapis.com/auth/spreadsheets'
	//   'https://www.googleapis.com/auth/spreadsheets.readonly'
	const SCOPE = 'https://www.googleapis.com/auth/spreadsheets.readonly';

	gapi.client.init({
		apiKey: API_KEY,
		clientId: CLIENT_ID,
		scope: SCOPE,
		discoveryDocs: [ 'https://sheets.googleapis.com/$discovery/rest?version=v4' ],
	}).then( function() {
		gapi.auth2.getAuthInstance().isSignedIn.listen( updateSignInStatus );
		updateSignInStatus( gapi.auth2.getAuthInstance().isSignedIn.get() );
	});
}

function handleClientLoad() {
	gapi.load( 'client:auth2', initClient );
}

function updateSignInStatus( isSignedIn ) {
	if ( isSignedIn ) {
		makeApiCall();
	}
}

function handleSignInClick( event ) {
	gapi.auth2.getAuthInstance().signIn();
}

function handleSignOutClick( event ) {
	gapi.auth2.getAuthInstance().signOut();
}

initClient();
makeApiCall();
