/*
 * Project Quickedit & Bulk Edit Script
 * Hooks into the inline post editor functionality to extend it to our custom metadata
 *
 * @link https://www.sitepoint.com/extend-the-quick-edit-actions-in-the-wordpress-dashboard/
 */
/*eslint-disable no-undef, no-unused-vars*/
document.addEventListener( 'DOMContentLoaded', function() {
	// Prepopulating our quick-edit post info -- set the original from WordPresses 'inline-edit-post' script to a variable prior to altering it in my own way.
	const inlineEditor   = inlineEditPost.edit;
	inlineEditPost.edit = function( id ) {
		// Call old copy.
		inlineEditor.apply( this, arguments );

		// Custom functionality for inlineEditPost.edit below.
		let postId = 0; // Set this to 0 , so we can know whether we are on a post or not.
		if ( typeof ( id ) === 'object' ) {
			postId = parseInt( this.getId( id ), 10 );
		}

		// If we have a post, postId will be assigned a number !== zero.
		if ( 0 !== postId ) {
			// Find row to edit.
			const row = document.getElementById( `edit-${postId}` );
			// This projects additional field -must also be a column.
			// // Project ID Field
			const jobID = document.querySelector( `#job_id_${postId}` ).textContent;
			if ( '' !== jobID ) {
				const jobIdQuickedit = row.querySelector( '#job_id' );
				jobIdQuickedit.value = jobID;
			}

			// // The client for this project
			const client = document.querySelector( `#client_${postId}` ).textContent;
			if ( '' !== client ) {
				const clientQuickedit = row.querySelector( '#client' );
				clientQuickedit.value = client;
			}

			/// The locally hosted directory that contains more information on this project.
			const localDir = document.getElementById( `local_folder_${postId}` ).dataset.folder;
			if ( '' !== localDir ) {
				const localDirQuickedit   = row.querySelector( '#local_folder' );
				localDirQuickedit.value = localDir;
			}

			// Teaser text
			const tease = document.getElementById( `tease_${postId}` ).textContent;
			if ( '' !== tease ) {
				const teaseQuickedit = row.querySelector( '#tease' );
				teaseQuickedit.value = tease;
			}

			// Year Completed
			const yearComplete = document.getElementById( `yearComplete_${postId}` ).textContent;
			if ( '' !== yearComplete ) {
				const yearCompleteQuickedit = row.querySelector( '#yearComplete' );
				yearCompleteQuickedit.value = yearComplete;
			}
		}
	};
});
