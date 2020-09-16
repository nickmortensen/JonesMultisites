/** For using SVG in media uploads
 * https://wordpress.stackexchange.com/questions/252256/svg-image-upload-stopped-working/305177#305177
*/
const mediaGridObserver = new MutationObserver( function ( mutations ) {

	for ( let i = 0; i < mutations.length; i++ ) {

		for ( let j = 0; j < mutations[i].addedNodes.length; j++ ) {
			element = jQuery( mutations[i].addedNodes[j] );
			// console.log( element );
			if ( element.attr( 'class' ) ) {
				elementClass = element.attr( 'class' );
				if ( element.attr( 'class' ).indexOf( 'attachment' ) != -1 ) {

					attachmentPreview = element.children( '.attachment-preview' );
					if ( attachmentPreview.length != 0 ) {
						if ( attachmentPreview.attr( 'class' ).indexOf( 'subtype-svg+xml') != -1 ) {
							let handler = function ( element ) {

								// let request = new XMLHttpRequest();
								// request.open( 'POST', script_vars.AJAXurl, true );
								jQuery.ajax({
									// script_vars is part of the localize_script function in \WP_Rig\WP_Rig\Media\Component class.
									url: script_vars.AJAXurl,
									type: "POST",
									dataType: 'html',
									data: {
										'action': 'svg_get_attachment_url',
										'attachmentID': element.attr('data-id')
									},
									success: function (data) {
										if (data)
										{
											element.find('img').attr('src', data);
											element.find('.filename').text('SVG Image');
										}
									}
								});

							}(element);

						}
					}
				}
			}
		}
	}
});

const attachmentPreviewObserver = new MutationObserver( function ( mutations ) {
	for ( let i = 0; i < mutations.length; i++ ) {
		for ( let j = 0; j < mutations[i].addedNodes.length; j++ ) {
			var element = jQuery(mutations[i].addedNodes[j]);
			var onAttachmentPage = false;
			if ( ( element.hasClass( 'attachment-details' ) ) || element.find( '.attachment-details' ).length != 0) {
				onAttachmentPage = true;
			}

			if ( onAttachmentPage == true ) {
				const urlLabel = element.find( 'label[data-setting="url"]' );
				if ( urlLabel.length != 0 ) {
					let value = urlLabel.find('input').val();
					element.find( '.details-image' ).attr( 'src', value );
				}
			}
		}
	}
});

// $(document).ready(function () {

// 	mediaGridObserver.observe(document.body, {
// 		childList: true,
// 		subtree: true
// 	});

// 	attachmentPreviewObserver.observe(document.body, {
// 		childList: true,
// 		subtree: true
// 	});


// });

// Trigger
document.addEventListener( 'DOMContentLoaded', function(e) {
	mediaGridObserver.observe( document.body, { childList: true, subtree: true } );
	attachmentPreviewObserver.observe( document.body, { childList: true, subtree: true } );
}, false );
