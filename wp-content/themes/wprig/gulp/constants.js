/* eslint-env es6 */
'use strict';

/**
 * External dependencies
 */
export const gulpPlugins = require( 'gulp-load-plugins' )();
import path from 'path';

let ignoreThisAsWell = '/Applications/MAMP/htdocs/jonessign.io/wp-content/themes/wprig/node_modules/stylelint/node_modules/flatted/php/flatted.php';
/**
 * Internal dependencies
 */
import { getThemeConfig, configValueDefined } from './utils';

// Root path is where npm run commands happen
export const rootPath     = process.cwd();
export const gulpPath     = `${ rootPath }/gulp`;
export const gulpTestPath = `${ rootPath }/gulp/tests`;

// Dev or production
export const isProd = ( process.env.NODE_ENV === 'production' );

// get the config
const config = getThemeConfig();

// directory for the production theme
export const prodThemePath = path.normalize( `${ rootPath }/../${ config.theme.slug }` );

// directory for assets (CSS, JS, images)
export const assetsDir = `${ rootPath }/assets`;

// directory for assets (CSS, JS, images) in production
export const prodAssetsDir = `${ prodThemePath }/assets`;

// PHPCS options
export const PHPCSOptions = {
	bin: `/Users/nickmortensen/.composer/vendor/bin/phpcs`,
	standard: `/Users/nickmortensen/utilities/mortensen`,
	warningSeverity: 0,
};

// Theme config name fields and their defaults
export const nameFieldDefaults = {
	author: 'The WP Rig Contributors',
	PHPNamespace: 'WP_Rig\\WP_Rig',
	slug: 'wp-rig',
	name: 'WP Rig',
	underscoreCase: 'wp_rig',
	constant: 'WP_RIG',
	camelCase: 'WpRig',
	camelCaseVar: 'wpRig',
};

// Project paths
const paths = {
	assetsDir,
	browserSync: {
		dir: '/Applications/MAMP/Library/OpenSSL/cert',
		cert: '/Applications/MAMP/Library/OpenSSL/cert/jonessign.io.crt',
		caCert: '/Applications/MAMP/Library/OpenSSL/cert/cacert.pem',
		key: '/Applications/MAMP/Library/OpenSSL/cert/jonessign.io.crt',
	},
	config: {
		themeConfig: `${ rootPath }/config/themeConfig.js`,
	},
	php: {
		src: [
			`${ rootPath }/**/*.php`,
			`!${ rootPath }/optional/**/*.*`,
			`!${ rootPath }/tests/**/*.*`,
			`!${ rootPath }/vendor/**/*.*`,
			`!${ rootPath }/node_modules/stylelint/node_modules/flatted/php/flatted.php`,
		],
		dest: `${ rootPath }/`,
	},
	styles: {
		editorSrc: [
			`${ assetsDir }/css/src/editor/**/*.css`,
			// Ignore partial files.
			`!${ assetsDir }/css/src/**/_*.css`,
		],
		editorSrcDir: `${ assetsDir }/css/src/editor`,
		editorDest: `${ assetsDir }/css/editor`,
		shared: [
			`${ assetsDir }/css/stc/_custom-*.css`,
		],
		adminSrcDir: `${ assetsDir }/css/src/admin_partials`,
		admin: [
			`${ assetsDir }/css/src/admin.css`,
		],
		adminDest: process.cwd(),
		src: [
			`${ assetsDir }/css/src/**/*.css`,
			`${ assetsDir }/css/src/_custom-*.css`,
			// Ignore partial files.
			`!${ assetsDir }/css/src/**/_*.css`,
			`!${ assetsDir }/css/src/admin.css`,
			// Ignore editor source css.
			`!${ assetsDir }/css/src/editor/**/*.css`,
			// ignore admin partials
			`!${ assetsDir }/css/src/admin_partials/_*.css`,
		],
		srcDir: `${ assetsDir }/css/src`,
		dest: `${ assetsDir }/css`,

	},
	scripts: {
		src: [
			`${ assetsDir }/js/src/**/*.js`,
			// Ignore partial files.
			`!${ assetsDir }/js/src/**/_*.js`,
		],
		srcDir: `${ assetsDir }/js/src`,
		dest: `${ assetsDir }/js`,
	},
	images: {
		src: `${ assetsDir }/images/src/**/*.{jpg,JPG,png,svg,gif,GIF}`,
		dest: `${ assetsDir }/images/`,
	},
	export: {
		src: [],
		stringReplaceSrc: [
			`${ rootPath }/style.css`,
			`${ rootPath }/languages/*.po`,
		],
	},
	languages: {
		src: [
			`${ rootPath }/**/*.php`,
			`!${ rootPath }/optional/**/*.*`,
			`!${ rootPath }/tests/**/*.*`,
			`!${ rootPath }/vendor/**/*.*`,
		],
		dest: `${ rootPath }/languages/${ nameFieldDefaults.slug }.pot`,
	},
};

// Add rootPath to filesToCopy and additionalFilesToCopy
const additionalFilesToCopy = configValueDefined( 'export.additionalFilesToCopy' ) ? config.export.additionalFilesToCopy : [];
const filesToCopy           = configValueDefined( 'export.filesToCopy' ) ? config.export.filesToCopy : [];
for ( const filePath of filesToCopy.concat( additionalFilesToCopy ) ) {
	// Add the files to export src
	paths.export.src.push( `${ rootPath }/${ filePath }` );
}

// Override paths for production
if ( isProd ) {
	paths.php.dest          = `${ prodThemePath }/`;
	paths.styles.dest       = `${ prodAssetsDir }/css/`;
	paths.styles.editorDest = `${ prodAssetsDir }/css/editor/`;
	paths.scripts.dest      = `${ prodAssetsDir }/js/`;
	paths.images.dest       = `${ prodAssetsDir }/images/`;
	paths.languages         = {
		src: `${ prodThemePath }/**/*.php`,
		dest: `${ prodThemePath }/languages/${ config.theme.slug }.pot`,
	};
}

export { paths };
