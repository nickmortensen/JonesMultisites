/**
 * Data for the locations of Jones Sign Company -- according to the localize script, the main object is called 'locationInfo'
 *
 */

/* eslint-disable */
const locations = JSON.parse( jonesignInfo.locations ); // the PHP outputs the data as json-like, but it is really a string.


const outputAddress = input => {
	let { address, city, email, fax, googleCID, latitude, longitude, phone, state, zip } = input;
	let formattedAddress = address.replace( /-/, '<br>');
	let output = '';
	output    += '<address itemscope itemtype="https://schema.org/PostalAddress">';
	output    += `<span itemprop="streetAddress">${address.replace( /-/, '<br>')}</span>`;
	output    += `<span itemprop="addressLocality">${city}</span>, `;
	output    += `<span itemprop="addressRegion">${state} </span>`;
	output    += `<span itemprop="postalCode"> ${zip}</span>`;
	output    += '</address>';
	return output;
}

const outputPhone = input => `<a class="telephone_link" href="tel:+1-${input.phone}" itemprop="telephone">${input.phone}</a>`;

const eachAddress = [];
locations.forEach( location => {
	let {
		id,
		blog_id,
		name,
		description,
		address,
		common,
		slug,
	} = location;
	let output = `<div data-slug="${slug}" class="single_jones_address">`;
	output     += `<h2>${name.toUpperCase()}</h2>`;
	output     += `<div>`;
	output     += outputAddress( address );
	output     += outputPhone( address );
	output     += `</div>`;
	output     += '</div><!-- end div.single_jones_address -->';
	// const sentence = `The address is ${address} and the city is ${city} while the phone number is ${phone}`;
	eachAddress.push( output );
} );





