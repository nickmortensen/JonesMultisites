/*
 * Project Quickedit & Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to our custom metadata
 *
 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
 */

document.addEventListener( 'DOMContentLoaded', function() {
	// Prepopulating our quick-edit post info -- set the original from WordPresses 'inline-edit-post' script to a variable prior to altering it in my own way.
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

			// // Project ID Field
			let jobID = document.querySelector( `#job_id_${post_id}` ).textContent;
			if ( '' !== jobID ) {
				let jobID_quickedit = row.querySelector( '#job_id' );
				jobID_quickedit.value = jobID;
			}

			// // The client for this project
			let client = document.querySelector( `#client_${post_id}` ).textContent;
			if ( '' !== client ) {
				let client_quickedit   = row.querySelector( '#client' );
				client_quickedit.value = client;
			}

			// // The locally hosted directory that contains more information on this project.
			let localDir = document.getElementById( `local_folder_${post_id}`).dataset.folder;
			if ( '' !== localDir ) {
				let localDir_quickedit   = row.querySelector( '#local_folder' );
				localDir_quickedit.value = localDir;
			}

			// TEaser text
			let tease = document.getElementById( `tease_${post_id}`).textContent;
			if ( '' !== tease ) {
				let teaseQuickedit = row.querySelector( '#tease' );
				teaseQuickedit.value = tease;
			}

			// Year Completed
			let year_complete = document.getElementById( `year_complete_${post_id}`).textContent;
			if ( '' !== year_complete ) {
				let year_completeQuickedit = row.querySelector( '#year_complete' );
				year_completeQuickedit.value = year_complete;
			}

		}

	}

});
