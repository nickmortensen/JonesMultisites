const projectsBox = document.getElementById( 'client_projects' );
const allProjectCheckboxes = document.querySelectorAll( 'input[name="clientProjects"');
let {projects, selected} = clientProjectsAll;
let startSelect = [];
selected = selected.split( ',' ).map( item => parseInt( item, 10 ) );
console.log( selected );

// selected.forEach( item => );

selected.forEach( item => allProjectCheckboxes.querySelectorAll( `#${item}`).checked = true );
