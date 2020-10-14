/*
 * Post Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to our custom metadata
 *
 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
 */

document.addEventListener( 'DOMContentLoaded', function() {
	console.log( 'on staffmembers!' );
	//Prepopulating our quick-edit post info
	/* eslint-disable no-undef */
	const inlineEditor   = inlineEditPost.edit;
	inlineEditPost.edit = function( id ) {
		/* eslint-enable no-undef */
		//call old copy.
		inlineEditor.apply( this, arguments );
		//our custom functionality below
		let postId = 0;
		if ( typeof ( id ) === 'object' ) {
			postId = parseInt( this.getId( id ) );
		}

		// If we have our post, postId will be assigned a number that is not zero.
		if ( 0 !== postId ) {
			// Find our row.
			const row = document.getElementById( `edit-${postId}` );
			// let row = document.querySelector( `#edit-${postId}` );

			// This staffmembers data-status resolves to 'on' if the staffmember is managemtnent, 'off' if the staffmember isn't managemtnent.
			const isManagement = document.querySelector( `#staff_management_${postId}` ).dataset.state;
			if ( isManagement === 'on' ) {
				const managerCheckbox = row.querySelector( '#staff_management' );
				managerCheckbox.checked = true;
			}

			// This staffmembers data-status resolves to 'on' if the staffmember is current, 'off' if the staffmember isn't current.
			const isCurrent = document.querySelector( `#staff_current_${postId}` ).dataset.state;
			if ( isCurrent === 'on' ) {
				const currentCheckbox = row.querySelector( '#staff_current' );
				currentCheckbox.checked = true;
			}
			// jones sign company staff id number.
			const jonesID = document.getElementById( `staff_id_${postId}` ).textContent;
			if ( '' !== jonesID ) {
				const jonesIdQuickedit = row.querySelector( '#staff_id' );
				jonesIdQuickedit.value = jonesID;
			}
			// Full title for the staffmember.
			const fullTitle = document.getElementById( `full_title_${postId}` ).textContent;
			if ( '' !== fullTitle ) {
				const fullTitleQuickedit = row.querySelector( '#full_title' );
				fullTitleQuickedit.value = fullTitle;
				fullTitleQuickedit.setAttribute( 'default', fullTitle );
				fullTitleQuickedit.setAttribute( 'value', fullTitle );
			}
			// Shortened Title for Staffmember
			const shortTitle = document.getElementById( `short_title_${postId}` ).textContent;
			if ( '' !== shortTitle ) {
				const shortTitleQuickEdit = row.querySelector( '#short_title' );
				shortTitleQuickEdit.value = shortTitle;
				shortTitleQuickEdit.setAttribute( 'default', shortTitle );
				shortTitleQuickEdit.setAttribute( 'value', shortTitle );
			}
		}
	};
});
