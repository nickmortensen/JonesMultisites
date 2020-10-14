/*eslint-disable no-undef, no-unused-vars*/

const projectsBox          = document.getElementById( 'client_projects' );
const allProjectCheckboxes = document.querySelectorAll( 'input[name = "clientProjects"' );
let selected               = clientProjectsAll.selected;
const projects             = clientProjectsAll.projects;
const startSelect          = [];

selected = selected.split( ',' ).map( item => parseInt( item, 10 ) );
console.log( selected );

selected.forEach( item => allProjectCheckboxes.querySelectorAll( `#${item}` ).checked = true );
