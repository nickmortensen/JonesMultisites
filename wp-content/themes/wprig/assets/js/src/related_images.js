/**
 * Fetch 15 or less related images based upon the tag name using the WP Rest API.
 * Posts are only fetched when the user scrolls down to where the container is in the viewport.
 */

// Variables passed by RelatedImages Component via wp_localize_script.

const termID          = termdata.post_ID; /* eslint-disable-line */
const slug            = termdata.cat_ids; /* eslint-disable-line */
const restURL         = termdata.rest_url; /* eslint-disable-line */
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
 * @param {object} data Individual Data
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
 */
const theFigure = imageObject => {
	if ( 5 === Number( imageObject.cmb2.media_rating.imageRating ) && ( 1.2 < ( imageObject.media_details.width / imageObject.media_details.height ) < 1.4 ) ) {
		const figure = document.createElement( 'figure' );
		figure.className = 'related_image';
		figure.setAttribute( 'title', Number( imageObject.cmb2.media_rating.imageRating ) );
		const image = document.createElement( 'img' );
		image.setAttribute( 'src', imageObject.source_url );
		image.setAttribute( 'alt', imageObject.alt_text );
		image.setAttribute( 'width', '200' );
		const figCaption = document.createElement( 'figcaption' );
		figCaption.textContent = imageObject.title.rendered.split( ':')[0];
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
}
sendExampleQuery();

function sendRESTquery() {
	fetch( queryURL )
		.then( response => response.json() )
		.then( data => displayGalleryImages( data ) )
		.catch( error => console.error( error.message ) );
};

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
 * @param {object} data All the data returned.
 */
const getMimeType = data => data.mime_type;

/**
 * Get the image ID for the image object
 * @param {object} data All the data returned.
 */
const getImageID = data => data.id;

/**
 * Get the image link for the image object
 * @param {object} data All the data returned.
 */
const getImageLink = data => data.link;

/**
 * Get the image url for the image object
 * @param {object} data All the data returned.
 */
const getImageUrl = data => data.source_url;

/**
 * Get the caption for the image object
 * @param {object} data All the data returned.
 */
const getCaption = data => stripHTML( data.caption.rendered ) ?? 'generic caption';

/**
 * Get the title for the image object
 * @param {object} data All the data returned.
 */
const getTitle = data => data.title.rendered !== '' ? data.title.rendered.split( ':')[0] : 'standard title';

/**
 * Get the title for the image object
 * @param {object} data All the data returned.
 */
const getAlt = data => data.alt_text ?? 'generic alt text';

/**
 * Get the image rating as an integer for the image object
 * @param {object} data All the data returned.
 */
const getRating = data => Number( data.cmb2.media_rating.imageRating );

/**
 * Pull the data that will be used to create a srcSet element from the data returned in the REST API Query.
 * @param {Object} sizes The sizes attribute pulled from the information.
 */
const getSourceSet = sizes => {
	const { medium, large, thumbnail, medium_large, 'rectangular-large': rectangularLarge, "rectangular-mid": rectangularMid } = sizes;
	let srcSet = {
		ratio: medium.width / medium.height,
		medium: medium.source_url,
		thumbnail: thumbnail.source_url,
		large: rectangularLarge.source_url,
	};
	return srcSet;
}

/**
 * Strips out any html tags.
 *
 * @param {string} html The text witht he html inside of it.
 */
const stripHTML = html => {
	const find = /<[^>]*>?/gm;
	const replace = '';
	return html.replace( find, replace );
};
