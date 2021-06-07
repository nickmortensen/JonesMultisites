/* eslint-disable no-unused-vars, no-undef */
const {
	identifiers,
	current,
	resturl: restURL,
	gridData,
} = projectData;

// console.log(gridData);

// console.log(restURL);
const getRandom = ( smallest = 1, largest = 9 ) => Math.floor( Math.abs( ( Math.random() * ( smallest - largest + 1 ) ) + 1 ) );

// console.log( getRandom( 4, 14 ) );
// should be an array that does not include 53
// let projectOneIndex = getRandom( 1, projectData.identifiers.length );
const projectOne = getRandom( 1, projectData.identifiers.length ); //eslint-disable-line no-undef
const projectTwo = Math.abs( projectOne - 3 );
// console.log( projectOne, projectTwo );

const relatedProjects = [ identifiers[ projectOne ], identifiers[ projectTwo ] ];
const recentProjectsQueryURL        = `${restURL}project?include=${relatedProjects.join()}`;

// console.log(projectData.identifiers.join());
const projectGridQuery  = `${restURL}project?_embed&include=${projectData.identifiers.join()}`;

function sendProjectGridQuery() {
	fetch( projectGridQuery )
		.then( response => response.json() )
		.then( data => outputProjectGrid( data ) )
		.catch( error => console.error( error ) );
}

/*
* Output a more manageable object from a  single project JSON data
 * @param {Object} data Individual Data
 * @return {Object} about an image.
 */
const getProjectObject = entry => {
	const {
		id,
		slug,
		link,
		title,
		excerpt,
		featured_media: featuredImageId,
		cmb2,
	}                     = entry;
	const additionalInfo    = cmb2.projectInfoMetabox;
	const embedded          = entry._embedded[ 'wp:featuredmedia' ][ 0 ];
	const featuredImg       = embedded.media_details.sizes;
	featuredImg.full      = featuredImg.full.source_url;
	featuredImg.thumbnail = featuredImg.thumbnail.source_url;
	featuredImg.medium    = featuredImg.medium.source_url;
	featuredImg.large     = featuredImg.large.source_url;

	const {
		projectLocation: locationDetails,
		projectNarrative,
		projectVerticalImage: verticalImage,
		projectVerticalImage_id: verticalImageId,
		projectImagesSlideshow: slideshowObject,
	} = additionalInfo;

	const slideShowIds      = Object.keys( slideshowObject );
	const slideshowURLs     = Object.values( slideshowObject );

	return {
		name: title.rendered,
		id,
		slug,
		link,
		excerpt: excerpt.rendered,
		cmb2,
		additionalInfo,
		featuredImg,
		featuredImageId,
		locationDetails,
		projectNarrative,
		slideShowIds,
		slideshowURLs,
		verticalImage,
		verticalImageId,
		embedded,
	};
};

function sendProjectDevQuery() {
	fetch( projectGridQuery )
		.then( response => response.json() )

		.then( data => console.log( data ) )
		.catch( error => console.error( error ) );
}
// sendProjectDevQuery();

// create section with an id of related-projects

/*
const relatedProjectsSection = document.querySelector( '.related-projects' ) || '';

relatedProjectsSection.style.backgroundColor = 'var(--indigo-700)';
*/
/**
 * Get the relevant project data for the next and previous project.
 */
// function outputRelatedProject( project ) {}

/**
 * Choose two project ids that are not the current project and are distinct from one another.
 */
