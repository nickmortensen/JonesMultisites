const {
	identifiers,
	current,
	resturl: restURL,
} = projectData; //eslint-disable-line no-unused-vars, no-undef
const getRandom = ( smallest = 1, largest = 9 ) => Math.floor( Math.abs( Math.random() * ( smallest - largest + 1 ) + 1 ) );
// should be an array that does not include 53
// let projectOneIndex = getRandom( 1, projectData.identifiers.length );
const projectOne = getRandom( 1, projectData.identifiers.length );
const projectTwo = Math.abs( projectOne - 3 );
console.log( projectOne, projectTwo );

const relatedProjects = [ identifiers[ projectOne ], identifiers[ projectTwo ] ];
const queryURL        = `${restURL}project?include=${relatedProjects.join()}`;
console.log( 'querying the following URL using WP API:', queryURL );

function sendRESTquery() {
	fetch( queryURL )
		.then( response => response.json() )
		.then( data => console.log( data ) )
		.catch( error => console.error( error.message ) );
}

sendRESTquery();

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
