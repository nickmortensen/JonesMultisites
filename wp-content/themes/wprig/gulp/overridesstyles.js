/* eslint-env es6 */
'use strict';

/**
 * External dependencies
 */
import { src, dest } from 'gulp';
import postCssFor from 'postcss-for';
import postcssPresetEnv from 'postcss-preset-env';
import AtImport from 'postcss-import';
import pump from 'pump';
import cssnano from 'cssnano';
import stylelint from 'stylelint';
import reporter from 'postcss-reporter';
import calc from 'postcss-calc';
import { pipeline } from 'mississippi';

/**
 * Internal dependencies
 */
import { rootPath, paths, gulpPlugins, isProd } from './constants';

import { server } from './browserSync';


let overrides = `${rootPath}/*.postcss`;
const postcssPlugins = [
	// stylelint(),
	postcssPresetEnv( {
		stage: 0,
		autoprefixer: {
			"grid": false
		},
		features: {
			'custom-media-queries': { preserve: false },
			'custom-properties': { preserve: false },
			'custom-selectors': { preserve: false },
			'nesting-rules': true,
		},
	} ),
	postCssFor(),
	calc( { preserve: false } ),
	// cssnano(),
];



export function processOverrides() {

	// Report messages from other postcss plugins
	postcssPlugins.push(
		reporter( { clearReportedMessages: true } )
	);

	// Return a single stream containing all the
	// after replacement functionality
	return pipeline.obj( [
		gulpPlugins.postcss( [
			AtImport( {
				path: [ overrides ],
			} ),
		] ),
		gulpPlugins.postcss( postcssPlugins ),
		gulpPlugins.if(
			config.dev.debug.styles,
			gulpPlugins.tabify( 4, true )
		),
		gulpPlugins.rename( {
			suffix: '.min',
		} ),
		server.stream( { match: '**/*.postcsscss' } ),
	] );
}

/**
* CSS via PostCSS + CSSNext (includes Autoprefixer by default).
* @param {function} done function to call when async processes finish
* @return {Stream} single stream
*/
export default function overrides( done ) {
	return pump( [
		src( overrides, { sourcemaps: ! isProd } ),
		processOverrides(),
		dest( rootPath, { sourcemaps: ! isProd } ),
	], done );
}
