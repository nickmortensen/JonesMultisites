/**
 * Data for the locations of Jones Sign Company -- according to the localize script, the main object is called 'locationInfo'
 *
 */

/* eslint-disable */
const locations = JSON.parse( jonesignInfo.locations ); // the PHP outputs the data as json-like, but it is really a string.
const locationsArray = [
	{
	"id": 75,
	"name": "jones denver",
	"slug": "den",
	"tax_id": 75,
	"description": "Jones Sign Location in Denver, Colorado.",
	"common": "Denver",
	"location_image_id": "104",
	"city_image_id": "7",
	"blog_id": "4",
	"indepth": false,
	"subdomain": "https://denver.jonessign.io",
	"nimble": "https://jonesdenver.com",
	"address": {
		"address": "14135 East 42nd Ave, Suite 60",
		"city": "Denver",
		"state": "CO",
		"zip": "80239",
		"phone": "303-975-1772",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "39.6711182",
		"longitude": "-105.0087123",
		"googleCID": "15431462219622802608"
	},
	"capabilities": [
		"Fabrication",
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 60,
	"name": "jones green bay",
	"slug": "grb",
	"tax_id": 60,
	"description": "Jones Sign Company Sales, Project Management, & Manufacturing facility in Green Bay, WI",
	"common": "Green Bay",
	"location_image_id": "101",
	"city_image_id": "8",
	"blog_id": "2",
	"indepth": false,
	"subdomain": "https://greenbay.jonessign.io",
	"nimble": "https://jonesgreenbay.com",
	"address": {
		"address": "1711 Scheuring Road",
		"city": "DePere",
		"state": "WI",
		"zip": "54115",
		"phone": "1-800-536-7446",
		"fax": "920-983-9145",
		"email": "leads-signs@jonessign.com",
		"latitude": "44.429640",
		"longitude": "-88.117770",
		"googleCID": "10408662568689642143"
	},
	"capabilities": [
		"Fabrication",
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 66,
	"name": "jones juárez",
	"slug": "mxz",
	"tax_id": 66,
	"description": "Jones Sign Company Manufacturing Facility in Juárez Mexico",
	"common": "Juarez",
	"location_image_id": "107",
	"city_image_id": "14",
	"blog_id": "13",
	"indepth": false,
	"subdomain": "https://juarez.jonessign.io",
	"nimble": "https://jonesjuarez.com",
	"address": {
		"address": "Manuel Quiñones Ponce # 1655",
		"city": "Juárez",
		"state": "",
		"zip": "CH 32670",
		"phone": "656-682-0228",
		"fax": "656-682-4076",
		"email": "leads-signs@jonessign.com",
		"latitude": "31.685091",
		"longitude": "-106.462251",
		"googleCID": "2525750914980347419"
	},
	"capabilities": [
		"Fabrication"
	]
	},
	{
	"id": 61,
	"name": "jones las vegas",
	"slug": "las",
	"tax_id": 61,
	"description": "Jones Sign Company Sales, Manufacturing, & Installation Depot in Las Vegas, Nevada.  Doing Business as (dba) Las Vegas Sign.",
	"common": "Las Vegas",
	"location_image_id": "110",
	"city_image_id": "9",
	"blog_id": "11",
	"indepth": false,
	"subdomain": "https://vegas.jonessign.io",
	"nimble": "https://joneslasvegas.com",
	"address": {
		"address": "5860 La Costa Canyon Court",
		"city": "Las Vegas",
		"state": "NV",
		"zip": "89139",
		"phone": "702-506-0933",
		"fax": "702-431-3585",
		"email": "leads-signs@jonessign.com",
		"latitude": "36.031122",
		"longitude": "-115.220413",
		"googleCID": "7637448676473703614"
	},
	"capabilities": [
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 70,
	"name": "jones los angeles",
	"slug": "lax",
	"tax_id": 70,
	"description": "Jones Sign Company location in sunny Los Angeles, California",
	"common": "LA",
	"location_image_id": "106",
	"city_image_id": "10",
	"blog_id": "5",
	"indepth": false,
	"subdomain": "https://losangeles.jonessign.io",
	"nimble": "https://jonessdla.com",
	"address": {
		"address": "2705 Pomona Blvd",
		"city": "Pomona",
		"state": "CA",
		"zip": "91768",
		"phone": "909-992-0504",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "34.055206",
		"longitude": "-117.799643",
		"googleCID": "7837313966334986462"
	},
	"capabilities": [
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 73,
	"name": "jones miami",
	"slug": "mia",
	"tax_id": 73,
	"description": "Jones Sign Company Offices in Pompano Beach, Florida",
	"common": "Miami",
	"location_image_id": "109",
	"city_image_id": "11",
	"blog_id": "7",
	"indepth": false,
	"subdomain": "https://miami.jonessign.io",
	"nimble": "https://jonesmiami.com",
	"address": {
		"address": "1301 West Copans Road G5",
		"city": "Pompano Beach",
		"state": "FL",
		"zip": "33064",
		"phone": "954-973-7700",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "26.259975",
		"longitude": "-80.166240",
		"googleCID": "10564790390358369661"
	},
	"capabilities": [
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 74,
	"name": "jones minneapolis",
	"slug": "msp",
	"tax_id": 74,
	"description": "Jones Sign Company manufacturing, sales, project management, install, & service facility in St. Paul, Minnesota - serving the Minnesota and Western Wisconsin areas.",
	"common": "MSP",
	"location_image_id": "86",
	"city_image_id": "12",
	"blog_id": "8",
	"indepth": false,
	"subdomain": "https://minneapolis.jonessign.io",
	"nimble": "https://jonesmsp.com",
	"address": {
		"address": "1065 Phalen Blvd, Suite 100",
		"city": "St. Paul",
		"state": "MN",
		"zip": "55106-2395",
		"phone": "651-488-6711",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "44.967178",
		"longitude": "-93.059453",
		"googleCID": "2654236200603957709"
	},
	"capabilities": [
		"Fabrication",
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 72,
	"name": "jones national HQ",
	"slug": "nat",
	"tax_id": 72,
	"description": "National Headquarters",
	"common": "Sign Co",
	"location_image_id": "101",
	"city_image_id": "8",
	"blog_id": "1",
	"indepth": false,
	"subdomain": "https://jonessign.io",
	"nimble": "https://jonessign.io",
	"address": {
		"address": "1711 Scheuring Road",
		"city": "DePere",
		"state": "WI",
		"zip": "54115",
		"phone": "1-800-536-7446",
		"fax": "920-983-9145",
		"email": "leads-signs@jonessign.com",
		"latitude": "44.429640",
		"longitude": "-88.117770",
		"googleCID": "10408662568689642143"
	},
	"capabilities": [
		"Fabrication",
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 64,
	"name": "jones philadelphia",
	"slug": "phl",
	"tax_id": 64,
	"description": "Jones Sign Company Project Management and Sales office in Croydon, Pennsylvania",
	"common": "Philadelphia",
	"location_image_id": "86",
	"city_image_id": "14",
	"blog_id": "3",
	"indepth": false,
	"subdomain": "https://philadelphia.jonessign.io",
	"nimble": "https://jonesphiladelphia.com",
	"address": {
		"address": "400 Mack Drive",
		"city": "Croydon",
		"state": "PA",
		"zip": "19021",
		"phone": "215-788-3898",
		"fax": "215-788-7588",
		"email": "leads-signs@jonessign.com",
		"latitude": "40.094744",
		"longitude": "-74.883423",
		"googleCID": "1143172498049237817"
	},
	"capabilities": [
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 63,
	"name": "jones reno",
	"slug": "rno",
	"tax_id": 63,
	"description": "Jones Sign Company Manufacturing and Sales Facility in Reno, Nevada",
	"common": "Reno",
	"location_image_id": "108",
	"city_image_id": "108",
	"blog_id": "14",
	"indepth": false,
	"subdomain": "https://reno.jonessign.io",
	"nimble": "https://jonesreno.com",
	"address": {
		"address": "2080 Brierley Way Suite 101",
		"city": "Reno",
		"state": "NV",
		"zip": "89434",
		"phone": "775-351-1700",
		"fax": "804-798-5582",
		"email": "leads-signs@jonessign.com",
		"latitude": "39.528466",
		"longitude": "-119.702799",
		"googleCID": "2624821484068289793"
	},
	"capabilities": [
		"Fabrication",
		"Installation",
		"Project Management"
	]
	},
	{
	"id": 62,
	"name": "jones richmond",
	"slug": "ric",
	"tax_id": 62,
	"description": "Jones Sign Company Manufacturing, Sales, & Project Management Facility in Ashland, Virginia",
	"common": "Richmond",
	"location_image_id": "109",
	"city_image_id": "13",
	"blog_id": "9",
	"indepth": false,
	"subdomain": "https://richmond.jonessign.io",
	"nimble": "https://JonesRichmond.com",
	"address": {
		"address": "11046 Leadbetter Road",
		"city": "Ashland",
		"state": "VA",
		"zip": "23005",
		"phone": "804-798-5533",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "37.701925",
		"longitude": "-77.442987",
		"googleCID": ""
	},
	"capabilities": [
		"Fabrication",
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 69,
	"name": "jones san diego",
	"slug": "san",
	"tax_id": 69,
	"description": "Jones Sign Company Manufacturing, Sales, & Project Management Facility in San Diego, California",
	"common": "San Diego",
	"location_image_id": "111",
	"city_image_id": "14",
	"blog_id": "6",
	"indepth": false,
	"subdomain": "https://sandiego.jonessign.io",
	"nimble": "https://joneslasd.com",
	"address": {
		"address": "9025 Balboa Avenue",
		"city": "San Diego",
		"state": "CA",
		"zip": "92123",
		"phone": "858-569-1400",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "32.821283",
		"longitude": "-117.134101",
		"googleCID": "6788374194138788142"
	},
	"capabilities": [
		"Fabrication",
		"Installation",
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 68,
	"name": "jones tampa",
	"slug": "tpa",
	"tax_id": 68,
	"description": "Jones Sign Company satellite sales office in Tampa, Florida",
	"common": "Tampa",
	"location_image_id": "106",
	"city_image_id": "15",
	"blog_id": "10",
	"indepth": false,
	"subdomain": "https://tampa.jonessign.io",
	"nimble": "https://jonestampa.com",
	"address": {
		"address": "503 SOUTH US 301",
		"city": "Tampa",
		"state": "FL",
		"zip": "33619",
		"phone": "813-517-1613",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "27.945770",
		"longitude": "-82.35389",
		"googleCID": ""
	},
	"capabilities": [
		"Project Management",
		"Sales"
	]
	},
	{
	"id": 65,
	"name": "jones tijuana",
	"slug": "mxt",
	"tax_id": 65,
	"description": "Jones Sign Company Manufacturing Facility in Tijuana, MX",
	"common": "Tijuana",
	"location_image_id": "111",
	"city_image_id": "14",
	"blog_id": "15",
	"indepth": false,
	"subdomain": "https://tijuana.jonessign.io",
	"nimble": "https://jonestijuana.com",
	"address": {
		"address": "Calle Emilio Flores #2469 - Interior A - Col. Cañon del Padre",
		"city": "Tijuana",
		"state": "",
		"zip": "2203",
		"phone": "664-623-8082",
		"fax": "664-647-5630",
		"email": "leads-signs@jonessign.com",
		"latitude": "32.515933",
		"longitude": "-116.922919",
		"googleCID": ""
	},
	"capabilities": [
		"Fabrication",
		"Project Management"
	]
	},
	{
	"id": 67,
	"name": "jones virginia beach",
	"slug": "vab",
	"tax_id": 67,
	"description": "Jones Sign Company offices in Virginia Beach, VA",
	"common": "Virginia Beach",
	"location_image_id": "109",
	"city_image_id": "13",
	"blog_id": "12",
	"indepth": false,
	"subdomain": "https://virginiabeach.jonessign.io",
	"nimble": "https://jonesvirginiabeach.com",
	"address": {
		"address": "760 Lynnhaven Pkwy Suite 100",
		"city": "Virginia Beach",
		"state": "VA",
		"zip": "23452-7325",
		"phone": "757-264-6470",
		"fax": "",
		"email": "leads-signs@jonessign.com",
		"latitude": "36.817748",
		"longitude": "-76.065736",
		"googleCID": ""
	},
	"capabilities": [
		"Project Management",
		"Sales"
	]
	}
];

/**
 * Output an address object as an address html element
 * @param {object} location An object containing the Jones Sign Company location data
 */
const outputAddress = location => {
	let { address, city, email, fax, googleCID, latitude, longitude, phone, state, zip } = location;
	let output = `
<address itemscope itemtype="https://schema.org/PostalAddress">
	<span itemprop="streetAddress">${address.replace( /-/, '<br>')}</span>
	<span itemprop="addressLocality">${city}</span>,
	<span itemprop="addressRegion">${state} </span>
	<span itemprop="postalCode"> ${zip}</span>
</address>
`;
	return output;
}

/**
 * Output a telephone number as a link item in HTML.
 * @param {string} phone The phone number - with hyphens.
 */
const outputPhone = input => `<a class="telephone_link" href="tel:+1-${input.phone}" itemprop="telephone">${input.phone}</a>`;

const eachAddress = [];
locations.forEach( location => {
	let { id, blog_id, name, address, slug } = location;
	let streetAddress                        = outputAddress( address );
	let telephone                            = outputPhone( address );

	let output = `
<div data-location-id="${id}" data-blog-id="${blog_id}" data-slug="${slug}" class="single_jones_address">
	<h2>${name.toUpperCase()}</h2>
	<div>
		${streetAddress}
		${telephone}
	</div>
</div><!-- end div.single_jones_address -->
	`;
	eachAddress.push( output );
} );




