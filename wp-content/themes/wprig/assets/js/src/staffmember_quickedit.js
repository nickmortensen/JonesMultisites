/*
 * Post Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to our custom metadata
 *
 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
 */

document.addEventListener( 'DOMContentLoaded', function() {
	console.log( 'on staffmembers!');
	//Prepopulating our quick-edit post info
	var inline_editor   = inlineEditPost.edit;
	inlineEditPost.edit = function(id) {

		//call old copy.
		inline_editor.apply( this, arguments);

		//our custom functionality below
		var post_id = 0;
		if ( typeof( id ) === 'object' ){
			post_id = parseInt( this.getId( id ) );
		}

		// If we have our post, post_id will be assigned a number that is not zero.
		if ( 0 !== post_id ) {

			// Find our row.
			let row = document.getElementById( `edit-${post_id}` );
			// let row = document.querySelector( `#edit-${post_id}` );

			// This staffmembers data-status resolves to 'on' if the staffmember is managemtnent, 'off' if the staffmember isn't managemtnent.
			let isManagement = document.querySelector( `#staff_management_${post_id}` ).dataset.state;
			if ( 'on' === isManagement ) {
				let managerCheckbox = row.querySelector( '#staff_management' );
				managerCheckbox.checked = true;
			}

			// This staffmembers data-status resolves to 'on' if the staffmember is current, 'off' if the staffmember isn't current.
			let isCurrent = document.querySelector( `#staff_current_${post_id}` ).dataset.state;
			if ( 'on' === isCurrent ) {
				let currentCheckbox = row.querySelector( '#staff_current' );
				currentCheckbox.checked = true;
			}
			// jones sign company staff id number.
			let jonesID = document.getElementById( `staff_id_${post_id}`).textContent;
			if ( '' !== jonesID ) {
				let jonesID_quickedit = row.querySelector( '#staff_id' );
				jonesID_quickedit.value = jonesID;
			}
			// Full title for the staffmember.
			let fullTitle = document.getElementById( `full_title_${post_id}`).textContent;
			if ( '' !== fullTitle ) {
				let fullTitleQuickedit = row.querySelector( '#full_title' );
				fullTitleQuickedit.value = fullTitle;
				fullTitleQuickedit.setAttribute( 'default', fullTitle );
				fullTitleQuickedit.setAttribute( 'value', fullTitle );
			}
			// Shortened Title for Staffmember
			let shortTitle = document.getElementById( `short_title_${post_id}` ).textContent;
			if ( '' !== shortTitle ) {
				let shortTitleQuickEdit = row.querySelector( '#short_title' );
				shortTitleQuickEdit.value = shortTitle;
				shortTitleQuickEdit.setAttribute( 'default', shortTitle );
				shortTitleQuickEdit.setAttribute( 'value', shortTitle );
			}




		}

	}

});
