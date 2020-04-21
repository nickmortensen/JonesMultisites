/*
 * Project Quickedit & Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to our custom metadata
 *
 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
 */

document.addEventListener( 'DOMContentLoaded', function() {

	//Prepopulating our quick-edit post info -- set the original from WordPresses 'inline-edit-post' script to a variable prior to altering it in my own way.
	const inline_editor   = inlineEditPost.edit;
	inlineEditPost.edit = function(id) {

		// Call old copy.
		inline_editor.apply( this, arguments );

		// Custom functionality for inlineEditPost.edit below.
		let post_id = 0; // Set this to 0 , so we can know whether we are on a post or not.
		if ( typeof( id ) === 'object' ){
			post_id = parseInt( this.getId( id ), 10 );
		}

		// If we have a post, post_id will be assigned a number !== zero.
		if ( 0 !== post_id ) {

			// Find row to edit.
			let row = document.getElementById( `edit-${post_id}` );

			// This projects additional field -must also be a column.

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
