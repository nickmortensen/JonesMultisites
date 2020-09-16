/**
 * Fetch 3 or less related posts based on post categories using the WP Rest API.
 * Posts are only fetched when the user scrolls down to where the container is in the viewport.
 */

// Variables passed by Related_Posts Component via wp_localize_script.

const postID  = postdata.post_ID; /* eslint-disable-line */
const catIDS  = postdata.cat_ids; /* eslint-disable-line */
const restURL = postdata.rest_url; /* eslint-disable-line */

// Build query URL for the REST API. Note '&_embed=true' at the end. Means we get featured images as well.
const queryURL = `${restURL}posts?per_page=3&categories=${catIDS}&exclude=${postID}&_embed=true`;

const sendRESTquery = () => {
	fetch( queryURL )
		.then( info => info.json() )
		.then( data => displayRelatedPosts( data ) )
		.then( document.querySelector( '.related-spinner' ).remove() );
};

/**
 * Generate the HTML for the individual related post.
 *
 * @param {Object} postObject The post object.
 */
const thePost = postObject => {
	const postElement     = document.createElement( 'div' );
	postElement.className = 'related-post';

	// get Meaningful output for the date of the post.
	const date = new Date( postObject.date );

	// HTML Template for the individual post.
	const postContent = `
		<a href="${postObject.link}">
		${getFeaturedImage( postObject )}
			<h3 class="related-post__title">
			${postObject.title.rendered}
			</h3>
			<div hidden>
				Published <time class="entry-date published" datetime="${date}">${date.toDateString()}</time>
			</div>
		</a>
		`;
	postElement.innerHTML = postContent;

	return postElement;
};

/**
 * Place related post into the DOM.
 *
 * @param {Object} data Contains related posts data.
 */
const displayRelatedPosts = data => {
	const relatedContainer = document.querySelector( '.related-posts' );
	data.forEach( postObject => {
		relatedContainer.append( thePost( postObject ) );
	});
};

// Get featured image if there is one.
const getFeaturedImage = postObject => {
	if ( postObject.featured_media === 0 ) {
		return '';
	}
	const featuredObject = postObject._embedded[ 'wp:featuredmedia' ][ 0 ];
	const imgWidth  = featuredObject.media_details.sizes[ 'small-landscape' ].width;
	const imgHeight = featuredObject.media_details.sizes[ 'small-landscape' ].height;
	const altText = featuredObject.alt_text;
	return `
	<figure class="related-post__image shadow-outline">
		<img src="${featuredObject.media_details.sizes[ 'small-landscape' ].source_url}"
		width="${imgWidth}"
		height="${imgHeight}"
		alt="${altText}" />
	</figure>`;
};

// Trigger event only when related posts are scrolled into view.
window.addEventListener(
	'load',
	function( event ) { /* eslint-disable-line */
		const observer = new IntersectionObserver( function( entries, self ) {
			entries.forEach( entry => {
				if ( entry.isIntersecting ) {
					sendRESTquery();

					// disconnect after first reveal.
					self.disconnect();
				}
			});
		});
		observer.observe( document.querySelector( '.related-posts' ) );
	},
	false
);

