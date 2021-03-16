/**
 * An object to describe fields within the form that we'll use to get information from employees.
 */
const fields = [
	{
		type: 'text',
		name: 'staffName',
		label: 'First + Last Name',
		require: true,
	},
	{
		type: 'text',
		name: 'staffTitle',
		label: 'Position',
		require: true,
	},
	{
		type: 'text',
		name: 'staffId',
		label: 'Employee ID #',
		require: true,
	},
	{
		type: 'checkbox',
		name: 'location',
		label: 'location',
		require: true,
	},
	{
		type: 'text',
		name: 'staffPrior',
		label: 'Prior Occupation',
		require: true,
	},

];

console.log( fields.length );


