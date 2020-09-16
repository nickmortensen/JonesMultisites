// console.info( installPhotos );


/**
 *
 * 1. go to console.developers.google.com/apis/dashboard
 * @link https://developers.google.com/sheets/api/reference/rest/v4/spreadsheets.values/append
 */

const key = 'AIzaSyB2ASOHSYlXGr0mq4UGBeXU1wc5BQTPfvo';
const {credentials, scopes} = installPhotos;

// create XHR REQ to GOOGLE SHEET
/**
 * ID ATTRIBUTES FROM INPUT FIELDS MUST MATCH COLUMN HEADERS IN THE GOOGLE SPREADSHEET
 * Located at: https://docs.google.com/spreadsheets/d/1j0DPxwtyLbL3tkCkRefY-wQeTJdx4gzgZZdxLswrHUc/edit#gid=0
 */
const fieldNames   = [ 'photoUrl', 'client', 'signtype', 'location' ];
const photoUrl     =  document.getElementById( 'photoUrl' );
const client       =  document.getElementById( 'client' );
const signtype     =  document.getElementById( 'signtype' );
const location     =  document.getElementById( 'location' );

function submit_form() {
	// Check Fields.
	let complete    = true;
	let error_color = '#FF6600';

	let row = '';
	let i;

	for ( i = 0; i < fields.length; ++i ) {
		let field = fields[i];
		document.getElementById( field ).style.backgroundColor = 'inherit';
		let value = field.value;
		// Validate Current Field.
		if ( ! value ) {
			if ( field != 'message' ) {
				document.getElementById( field ).style.backgroundColor = error_color;
				complete = false;
			}
		} else {
			// Sheet Data
			row += `"${value}",`;
		}
	}

	// Submission.
	if ( complete ) {
		// Clean Row (removes final comma).
		let row         = row.slice( 0, -1 );
		// Configurations
		const sheetId              = '1j0DPxwtyLbL3tkCkRefY-wQeTJdx4gzgZZdxLswrHUc'; // Google Sheet ID
		const clientId             = '265155243495-q3scnfqdqilrf09sdn3e8pfj5kbu48b8.apps.googleusercontent.com'; // Enter your API Client ID here
		const sheetSecret          = 'Rfyon42N1GV2BCJNzBqqJ4VG'; // Enter your API Client Secret here
		const refreshToken         = '1/H_MuKSZMGCJzdqHYMH2V2j94N4zfpOoPV65C7XhFp0Q'; // Enter your OAuth Refresh Token here
		let googleSheetsAsyncToken = false;
		const sheetUrl             = `https://sheets.googleapis.com/v4/spreadsheets/${sheetId}/values/A1:append?includeValuesInResponse=false&insertDataOption=INSERT_ROWS&responseDateTimeRenderOption=SERIAL_NUMBER&responseValueRenderOption=FORMATTED_VALUE&valueInputOption=USER_ENTERED`;
		const sheetBody            = `{ "majorDimension": "ROWS", "values": [ [ ${row} ] ] }`;
		// HTTP Request Token Refresh
		const xhr     = new XMLHttpRequest();
		xhr.open( 'POST', `https://www.googleapis.com/oauth2/v4/token?client_id=${clientId}&client_secret=${sheetSecret}&refresh_token=${refreshToken}&grant_type=refresh_token` );
		xhr.setRequestHeader( 'Content-type', 'application/x-www-form-urlencoded' );
		xhr.onload = function() {
			let response = JSON.parse( xhr.responseText );
			let googleSheetsAsyncToken  = response.access_token;
			// HTTP Request Append Data
			if ( googleSheetsAsyncToken ) {
				const xxhr = new XMLHttpRequest();
				xxhr.open( 'POST', sheetUrl );
				xxhr.setRequestHeader( 'Content-length', sheetBody.length );
				xxhr.setRequestHeader( 'Content-type', 'application/json' );
				xxhr.setRequestHeader( 'Authorization', `OAuth ${googleSheetsAsyncToken}`  );
				xxhr.onload = function() {
					const message = document.getElementById( 'message' );
					if ( xxhr.status == 200 ) {
						// Success
						message.innerHTML( `<p class="add-another">Row Added to Sheet | <a href="https://jonessign.com/sales_presentation/data_capture/data_capture.php">Add Another &raquo;</a></p><p class="response">Response:<br/>${xxhr.responseText}</p>` );
						} else {
						// Fail
						message.innerHTML( `<p>Row Not Added</p><p>Response:<br/>${xxhr.responseText}</p>` );
					}
				};
				xxhr.send( sheetBody );
			}
		};
		xhr.send();
	}
} //end submit_form()

// Validation
const fieldHasEntry         = field => 0 < field.value.length;
const validatePhotoField    = () => 0 < photoUrl.value.length; // Photo URL field has some entry in it.
const validateClientField   = () => 0 < client.value.length; // Client field has some entry in it.
const validateSigntypeField = () => 0 < signtype.value.length; // signtype field has some entry in it.
const validateLocationField = () => 0 < location.value.length; // Client field has some entry in it.
const validatePhoneNumber   = entry => entry.match( /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/ );
const validateEmail         = entry => entry.match( /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i )
