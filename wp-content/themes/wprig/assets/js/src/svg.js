"use strict";
/**
 * https://wordpress.stackexchange.com/questions/252256/svg-image-upload-stopped-working/305177#305177
 * https://www.sitepoint.com/wordpress-svg/
 */
/**
 * Imports data as script_vars from Media/Component.php
 */
/* eslint-disable */
const incomingData = script_vars;
const fetchAddress = script_vars.AJAXurl;

console.table( 'incoming data is: ', incomingData );
const mediaGridObserver = new MutationObserver( function( mutations ) {
	for ( let i = 0; i < mutations.length; i++ ) {
		for ( let j = 0; j < mutations[ i ].addedNodes.length; j++ ) {
			element = document.querySelectorAll( mutations[ i ].addedNodes[ j ] );

			if ( element.attr( 'class' ) ) {
				elementClass = element.attr( 'class' );

				if ( element.attr( 'class' ).indexOf( 'attachment' ) !== -1 ) {
					attachmentPreview = element.children( '.attachment-preview' );

					if ( attachmentPreview.length !== 0 ) {
						if ( attachmentPreview.attr( 'class' ).indexOf( 'subtype-svg+xml' ) !== -1 ) {
								$.ajax({
									url: script_vars.AJAXurl,
									type: 'POST',
									dataType: 'html',
									data: {
										action: 'svg_get_attachment_url',
										attachmentID: element.getAttribute( 'data-id' ),
									},
									success: data => {
										if ( data ) {
											element.querySelectorAll( 'img' ).setAttribute( 'src', data );
											element.querySelectorAll( '.filename' ).textContent( 'SVG Image' );
										}
									},
								});
							}( element );
						}
					}
				}
			}
		}
	}
);

const attachmentPreviewObserver = new MutationObserver( function( mutations ) {
	for ( let i = 0; i < mutations.length; i++ ) {
		for ( let j = 0; j < mutations[ i ].addedNodes.length; j++ ) {
			const element = document.querySelectorAll( mutations[ i ].addedNodes[ j ] );
			let onAttachmentPage = false;
			if ( ( element.hasClass( 'attachment-details' ) ) || element.find( '.attachment-details' ).length !== 0 ) {
				onAttachmentPage = true;
			}

			if ( onAttachmentPage === true ) {
				const urlLabel = element.querySelectorAll( 'label[data-setting="url"]' );
				if ( urlLabel.length !== 0 ) {
					const value = urlLabel.querySelectorAll( 'input' ).value;
					element.querySelectorAll( '.details-image' ).setAttribute( 'src', value );
				}
			}
		}
	}
});

window.addEventListener( 'load', function() {
	mediaGridObserver.observe( document.body, {
		childList: true,
		subtree: true,
	});

	attachmentPreviewObserver.observe( document.body, {
		childList: true,
		subtree: true,
	});
});
