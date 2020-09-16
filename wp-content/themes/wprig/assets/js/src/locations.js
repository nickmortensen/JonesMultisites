/**
 * Data for the locations of Jones Sign Company -- according to the localize script, the main object is called 'locationInfo'
 *
 */
//eslint:ignore
let locations = JSON.parse( locationInfo.locations ); // the PHP outputs the data as json-like, but it is really a string.
// console.table( locations );
// console.table( Object.keys(locations[1]));
// locations.forEach( location => {
// 	let {id, name, slug, tax_id, description, location_image_id: imageID, city_image_id: cityImageID, blog_id: blogID, subdomain, nimble: nimbleURL, adress, capabilities: capability } = location;
// 	console.log( name );
// })
