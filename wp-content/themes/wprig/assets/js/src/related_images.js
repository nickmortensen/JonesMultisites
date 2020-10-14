/**
 * Fetch 15 or less related images based upon the tag name using the WP Rest API.
 * Posts are only fetched when the user scrolls down to where the container is in the viewport.
 */
/*eslint-disable no-undef, no-unused-vars*/
// Variables passed by RelatedImages Component via wp_localize_script.
const restURL         = termdata.rest_url;
const relatedImages   = termdata.related_images;
const relatedProjects = termdata.related_projects;
const includes = `${relatedImages.join()}`;

const relatedImagesElement = document.querySelector( '#signtype-gallery' );
console.log( relatedImagesElement );
console.table( 'gallery image ids: ', relatedImages );
console.table( 'related projects: ', relatedProjects );

// Build query URL for the REST API. Here we will query for all the images that have the tag.
const queryURL = `${restURL}media?include=${includes}`;
console.log( queryURL );

/**
 * Create an ImageObject of of the data used.
 * @param {Object} data Individual Data
 * @return {Object} Object containing data about an image.
 */
const createImageObject = data => {
	return {
		imageUrl: getImageUrl( data ),
		imageID: getImageID( data ),
		imageLink: getImageLink( data ),
		caption: getCaption( data ),
		title: getTitle( data ),
		alt: getAlt( data ),
		rating: getRating( data ),
	};
};

/**
 * Generate the HTML for the individual related image.
 *
 * @param {Object} imageObject The image object.
 * @return {string} The HTML for a figure containing the image.
 */
const theFigure = imageObject => {
	if ( Number( imageObject.cmb2.media_rating.imageRating ) === 5 && ( 1.4 > 1.2 < ( imageObject.media_details.width / imageObject.media_details.height ) ) ) {
		const figure = document.createElement( 'figure' );
		figure.className = 'related_image';
		figure.setAttribute( 'title', Number( imageObject.cmb2.media_rating.imageRating ) );
		const image = document.createElement( 'img' );
		image.setAttribute( 'src', imageObject.source_url );
		image.setAttribute( 'alt', imageObject.alt_text );
		image.setAttribute( 'width', '200' );
		const figCaption = document.createElement( 'figcaption' );
		figCaption.textContent = imageObject.title.rendered.split( ':' )[ 0 ];
		// figCaption.textContent = 'the figure caption';
		figure.append( image, figCaption );
		return figure;
	}
};

/**
 * Place related post into the DOM.
 *
 * @param {Object} data Contains related posts data.
 */
const displayGalleryImages = data => {
	const relatedContainer = document.querySelector( '#signtype-gallery' );
	data.forEach( imageObject => {
		relatedContainer.append( theFigure( imageObject ) );
	});
};

const sendExampleQuery = () => {
	fetch( 'https://jonessign.io/wp-json/wp/v2/media?include=492,493' )
		.then( response => response.json() )
		.then( data => data.forEach( entry => console.info( entry ) ) )
		.catch( error => console.error( error.message ) );
};
sendExampleQuery();

function sendRESTquery() {
	fetch( queryURL )
		.then( response => response.json() )
		.then( data => displayGalleryImages( data ) )
		.catch( error => console.error( error.message ) );
}

// Trigger event only when related posts are scrolled into view.
window.addEventListener(
	'load',
	function( event ) { /* eslint-disable-line */
		const observer = new IntersectionObserver( function( entries, self ) {
			entries.forEach( entry => {
				if ( entry.isIntersecting ) {
					sendRESTquery();
					console.log( entry );
					// disconnect after first reveal.
					self.disconnect();
				}
			});
		});
		observer.observe( document.querySelector( '#signtype-gallery' ) );
	},
	false
);

/**
 * Get the image ID for the image object
 * @param {Object} data All the data returned.
 * @return {string} The mime type
 */
const getMimeType = data => data.mime_type;

/**
 * Get the image ID for the image object
 * @param {Object} data All the data returned.
 * @return {number} The image id.
 */
const getImageID = data => Number( data.id );

/**
 * Get the image link for the image object
 * @param {Object} data All the data returned.
 * @return {string} The image link url.
 */
const getImageLink = data => data.link;

/**
 * Get the image url for the image object
 * @param {Object} data All the data returned.
 * @return {string} The image url.
 */
const getImageUrl = data => data.source_url;

/**
 * Get the caption for the image object
 * @param {Object} data All the data returned.
 * @return {string} The data without HTML tags.
 */
const getCaption = data => stripHTML( data.caption.rendered ) || 'generic caption';

/**
 * Get the title for the image object
 * @param {Object} data All the data returned.
 * @return {string} The title for the image.
 */
const getTitle = data => '' !== data.title.rendered ? data.title.rendered.split( ':' )[ 0 ] : 'standard title';

/**
 * Get the title for the image object
 * @param {Object} data All the data returned.
 * @return {string} The alt text for the image or generic alt text.
 */
const getAlt = data => data.alt_text || 'generic alt text';

/**
 * Get the image rating as an integer for the image object
 * @param {Object} data All the data returned.
 * @return {number} The star rating of an image.
 */
const getRating = data => Number( data.cmb2.media_rating.imageRating );

/**
 * Pull the data that will be used to create a srcSet element from the data returned in the REST API Query.
 * @param {Object} sizes The sizes attribute pulled from the information.
 * @return {Object} srcSet An object with sourceset data.
 */
const getSourceSet = sizes => {
	const { medium, large, thumbnail, 'rectangular-large': rectangularLarge, 'rectangular-mid': rectangularMid } = sizes;
	const srcSet = {
		ratio: medium.width / medium.height,
		medium: medium.source_url,
		thumbnail: thumbnail.source_url,
		large: rectangularLarge.source_url,
	};
	return srcSet;
};

/**
 * Strips out any html tags.
 *
 * @param {string} html The text with the html inside of it.
 * @return {string} The text without any html markup
 */
const stripHTML = html => {
	const find = /<[^>]*>?/gm;
	const replace = '';
	return html.replace( find, replace );
};
