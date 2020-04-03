/* eslint-env 2018 */
/**TESTING */
'use strict';

/**
 * External dependencies
 */
export const gulpPlugins = require( 'gulp-load-plugins' )();
import path from 'path';

/**
 * Internal dependencies
 */
import {
	getThemeConfig,
	configValueDefined,
} from './utils';
// User path is /Users/nickmortensen -- alternately known as '~' in the command line.
export const userPath     = '/Users/nickmortensen';
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
	bin            : `/Users/nickmortensen/.composer/vendor/bin/phpcs`,
	standard       : `/Users/nickmortensen/utilities/mortensen/ruleset.xml`,
	warningSeverity: 0,
};

// Theme config name fields and their defaults
export const nameFieldDefaults = {
	author        : 'Nick Mortensen',
	PHPNamespace  : 'WP_Rig\\WP_Rig',
	slug          : 'wp-rig',
	name          : 'WP Rig',
	underscoreCase: 'wp_rig',
	constant      : 'WP_RIG',
	camelCase     : 'WpRig',
	camelCaseVar  : 'wpRig',
};

// Project paths
const paths = {
	assetsDir,
	browserSync: {
		dir   : `${ rootPath }/BrowserSync`,
		cert  : `${ rootPath }/BrowserSync/wp-rig-browser-sync-cert.crt`,
		caCert: `${ rootPath }/BrowserSync/wp-rig-browser-sync-root-cert.crt`,
		key   : `${ rootPath }/BrowserSync/wp-rig-browser-sync-key.key`,
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
		],
		dest: `${ rootPath }/`,
	},
	styles: {
		adminSrc:[
			`${ assetsDir }/css/src/admin/code-editor.css`,
			`${ assetsDir }/css/src/admin/color-picker.css`,
			`${ assetsDir }/css/src/admin/customize-controls.css`,
			`${ assetsDir }/css/src/admin/customize-nav-menus.css`,
			`${ assetsDir }/css/src/admin/customize-widgets.css`,
			`${ assetsDir }/css/src/admin/deprecated-media.css`,
			`${ assetsDir }/css/src/admin/farbtastic.css`,
			`${ assetsDir }/css/src/admin/ie.css`,
			`${ assetsDir }/css/src/admin/install.css`,
			`${ assetsDir }/css/src/admin/login.css`,
			`${ assetsDir }/css/src/admin/wp-admin.css`,
			`!${assetsDir}/css/**/*.min.css`,
		],
		adminPartials: [
			`${assetsDir}/css/src/admin/admin_partials/_l10n.css`,
			`${assetsDir}/css/src/admin/admin_partials/_list-tables.css`,
			`${assetsDir}/css/src/admin/admin_partials/_media.css`,
			`${assetsDir}/css/src/admin/admin_partials/_nav-menus.css`,
			`${assetsDir}/css/src/admin/admin_partials/_revisions.css`,
			`${assetsDir}/css/src/admin/admin_partials/_site-health.css`,
			`${assetsDir}/css/src/admin/admin_partials/_site-icon.css`,
			`${assetsDir}/css/src/admin/admin_partials/_themes.css`,
			`${assetsDir}/css/src/admin/admin_partials/_widgets.css`,
		],
		adminSrcDir: `${ assetsDir }/css/src/admin`,
		adminDest  : `${ assetsDir }/css/admin`,
		editorSrc: [
			`${ assetsDir }/css/src/editor/**/*.css`,
			// Ignore partial files.
			`!${ assetsDir }/css/src/**/_*.css`,
		],
		editorSrcDir: `${ assetsDir }/css/src/editor`,
		editorDest  : `${ assetsDir }/css/editor`,
		src         : [
			`${ assetsDir }/css/src/**/*.css`,
			// Ignore partial files.
			`!${ assetsDir }/css/src/**/_*.css`,
			// Ignore editor source css.
			`!${ assetsDir }/css/src/editor/**/*.css`,
			// Ignore the css files in the admin folder.
			`!${ assetsDir }/css/src/admin/**/*.css`,
			`!${ assetsDir }/css/src/admin/admin_partials/_*.css`,
			// ignore tailwindcss
			`!${ assetsDir }/css/src/tailwind/tailwind.css`,
		],
		tailwind: {
			source: `${ assetsDir }/css/src/tailwind/source/_tailwind.css`,
			dest  : `${ assetsDir }/css/src/tailwind/tailwind.css`,
		},
		srcDir: `${ assetsDir }/css/src`,
		dest  : `${ assetsDir }/css`,
	},
	scripts: {
		src: [
			`${ assetsDir }/js/src/**/*.js`,
			// Ignore partial files.
			`!${ assetsDir }/js/src/**/_*.js`,
		],
		srcDir: `${ assetsDir }/js/src`,
		dest  : `${ assetsDir }/js`,
	},
	images: {
		src : `${ assetsDir }/images/src/**/*.{jpg,JPG,png,svg,gif,GIF}`,
		dest: `${ assetsDir }/images/`,
	},
	export: {
		src             : [],
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
		src : `${ prodThemePath }/**/*.php`,
		dest: `${ prodThemePath }/languages/${ config.theme.slug }.pot`,
	};
}

export { paths };
