module.exports = {
  plugins: [
    require('postcss-import'),
    require( 'tailwindcss' )('gulp/tailwind.js'),
    require( 'autoprefixer' ),
  ],
};
