/* eslint-env es6 */
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

// Root path is where npm run commands happen
export const rootPath = process.cwd();

export const gulpPath = `${ rootPath }/gulp`;

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
		dir: `${ rootPath }/BrowserSync`,
		cert: `${ rootPath }/BrowserSync/wp-rig-browser-sync-cert.crt`,
		caCert: `${ rootPath }/BrowserSync/wp-rig-browser-sync-root-cert.crt`,
		key: `${ rootPath }/BrowserSync/wp-rig-browser-sync-key.key`,
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
		editorSrc: [
			`${ assetsDir }/css/src/editor/**/*.css`,
			// Ignore partial files.
			`!${ assetsDir }/css/src/**/_*.css`,
		],
		editorSrcDir: `${ assetsDir }/css/src/editor`,
		editorDest: `${ assetsDir }/css/editor`,
		src: [
			`${ assetsDir }/css/src/**/*.css`,
			// Ignore partial files.
			`!${ assetsDir }/css/src/**/_*.css`,
			// Ignore editor source css.
			`!${ assetsDir }/css/src/editor/**/*.css`,
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
const filesToCopy = configValueDefined( 'export.filesToCopy' ) ? config.export.filesToCopy : [];
for ( const filePath of filesToCopy.concat( additionalFilesToCopy ) ) {
	// Add the files to export src
	paths.export.src.push( `${ rootPath }/${ filePath }` );
}

// Override paths for production
if ( isProd ) {
	paths.php.dest = `${ prodThemePath }/`;
	paths.styles.dest = `${ prodAssetsDir }/css/`;
	paths.styles.editorDest = `${ prodAssetsDir }/css/editor/`;
	paths.scripts.dest = `${ prodAssetsDir }/js/`;
	paths.images.dest = `${ prodAssetsDir }/images/`;
	paths.languages = {
		src: `${ prodThemePath }/**/*.php`,
		dest: `${ prodThemePath }/languages/${ config.theme.slug }.pot`,
	};
}

export { paths };

const stylelintOptions = {
	"rules": {
		"at-rule-empty-line-before": [ "always", {
				"except": [ "after-same-name", "blockless-after-same-name-blockless", "first-nested" ],
				"ignore": [ "after-comment", "inside-block" ]
			} ],
		"at-rule-name-case": "lower",
		"at-rule-name-space-after": "always-single-line",
		"at-rule-no-unknown": [ true, { "ignoreAtRules": [ "custom-media", "/^custom/", "/extend/" ] } ],
		"at-rule-semicolon-newline-after": "always",
		"block-closing-brace-newline-after": "always",
		"block-closing-brace-newline-before": "always",
		"block-no-empty": true,
		"block-opening-brace-newline-after": "always",
		"block-opening-brace-space-before": "always",
		"color-hex-case": "lower",
		"color-hex-length": "short",
		"color-named": "never",
		"color-no-invalid-hex": true,
		"comment-no-empty": true,
		"comment-empty-line-before": [ "always", {
			"ignore": ["after-comment", "stylelint-commands"]
		} ],
		"declaration-bang-space-after": "never",
		"declaration-bang-space-before": "always",
		"declaration-block-no-duplicate-properties": [ true, { "ignore": [ "consecutive-duplicates", "consecutive-duplicates-with-different-values" ] } ],
		"declaration-block-no-shorthand-property-overrides": true,
		"declaration-block-semicolon-newline-after": "always",
		"declaration-block-semicolon-space-before": "never",
		"declaration-block-trailing-semicolon": "always",
		"declaration-colon-newline-after": "always-multi-line",
		"declaration-colon-space-after": "always-single-line",
		"declaration-colon-space-before": "never",
		"declaration-property-unit-allowed-list": {
			"line-height": [],
			"/^animation/": ["s"],
			"font-size": [ "px", "em", "rem", "%", "ch", "vw", "vh", "vmax", "vmin"]
		},
		"font-family-name-quotes": "always-where-recommended",
		"font-family-no-duplicate-names": true,
		"font-family-no-missing-generic-family-keyword": true,
		"font-weight-notation": [ "numeric", { "ignore": [ "relative" ] } ],
		"function-calc-no-unspaced-operator": true,
		"function-linear-gradient-no-nonstandard-direction": true,
		"function-comma-space-after": "always",
		"function-comma-space-before": "never",
		"function-max-empty-lines": 1,
		"function-name-case": [ "lower", { "ignoreFunctions": [ "/^DXImageTransform.Microsoft.*$/" ] } ],
		"function-parentheses-space-inside": "never",
		"function-url-quotes": "never",
		"function-whitespace-after": "always",
		"indentation": "tab",
		"keyframe-declaration-no-important": true,
		"length-zero-no-unit": true,
		"max-empty-lines": 2,
		"max-line-length": [ 180, {
			"ignore": "comments",
			"ignorePattern": ["/(https?://[0-9,a-z]*.*)|(^description\\:.+)|(^tags\\:.+)/i"]
		} ],
		"media-feature-colon-space-after": "always",
		"media-feature-colon-space-before": "never",
		"media-feature-name-no-unknown": [ true, {"ignoreMediaFeatureNames": [ "/^custom-", "custom" ] }],
		"media-feature-range-operator-space-after": "always",
		"media-feature-range-operator-space-before": "always",
		"media-query-list-comma-newline-after": "always-multi-line",
		"media-query-list-comma-space-after": "always-single-line",
		"media-query-list-comma-space-before": "never",
		"no-descending-specificity": null,
		"no-eol-whitespace": true,
		"no-missing-end-of-source-newline": true,
		"no-duplicate-at-import-rules": true,
		"no-duplicate-selectors": null,
		"no-empty-source": true,
		"no-extra-semicolons": true,
		"no-invalid-double-slash-comments": true,
		"number-leading-zero": "always",
		"number-no-trailing-zeros": true,
		"property-case": "lower",
		"property-no-unknown": true,
		"rule-empty-line-before": [ "always", {
			"except": [ "after-single-line-comment" ],
			"ignore": ["after-comment", "first-nested", "inside-block"]
			} ],
		"selector-attribute-brackets-space-inside": "never",
		"selector-attribute-operator-space-after": "never",
		"selector-attribute-operator-space-before": "never",
		"selector-attribute-quotes": "always",
		"selector-class-pattern": [
			"^[a-z]+(-[a-z]+)*",
			{ "message": "Use lowercase and separate words with hyphens (selector-class-pattern)" }
		],
		"selector-id-pattern": [
			"^[a-z]+(-[a-z]+)*",
			{ "message": "Use lowercase and separate words with hyphens (selector-id-pattern)" }
		],
		"selector-combinator-space-after": "always",
		"selector-combinator-space-before": "always",
		"selector-list-comma-newline-after": "always",
		"selector-list-comma-space-before": "never",
		"selector-max-empty-lines": 0,
		"selector-pseudo-class-case": "lower",
		"selector-pseudo-class-no-unknown": true,
		"selector-pseudo-class-parentheses-space-inside": "never",
		"selector-pseudo-element-no-unknown": true,
		"selector-pseudo-element-case": "lower",
		"selector-pseudo-element-colon-notation": "double",
		"selector-type-case": "lower",
		"selector-type-no-unknown": true,
		"string-no-newline": true,
		"string-quotes": "double",
		"unit-case": "lower",
		"unit-no-unknown": true,
		"value-keyword-case": "lower",
		"value-list-comma-newline-after": "always-multi-line",
		"value-list-comma-space-after": "always-single-line",
		"value-list-comma-space-before": "never"
	}
};
