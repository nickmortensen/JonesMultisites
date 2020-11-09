/**
 * selectFx.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Codrops
 * http://www.codrops.com
 */
( function( window ) {
	'use strict';

	/**
	 * based on from https://github.com/inuyaksa/jquery.nicescroll/blob/master/jquery.nicescroll.js
	 */
	function hasParent( e, p ) {
		if ( ! e ) {
			return false;
		}
		let el = e.target || e.srcElement || e || false;
		while ( el && el !== p ) {
			el = el.parentNode || false;
		}
		return ( el !== false );
	}

	/**
	 * extend obj function.
	 * @link https://eslint.org/docs/rules/no-prototype-builtins
	 */
	function extend( a, b ) {
		for ( const key in b ) {
			const hasKeyProperty = Object.prototype.hasOwnProperty.call( b, key );
			if ( hasKeyProperty ) {
				a[ key ] = b[ key ];
			}
		}
		return a;
	}

	/**
	 * SelectFx function
	 */
	function SelectFx( el, options ) {
		this.el = el;
		this.options = extend( {}, this.options );
		extend( this.options, options );
		this._init();
	}

	/**
	 * SelectFx options
	 */
	SelectFx.prototype.options = {
		// If true - any links open in a new tab.
		// If we want to be redirected when we click an option, we need to define a data-link attribute on the option of the native select element
		newTab: true,
		// When opening the select element, the default placeholder (if any) is shown
		stickyPlaceholder: true,
		// Callback function when changing the value
		onChange( val ) {
			return false;
		},
	};

	/**
	 * Init function
	 * Initializes and caches some variabless
	 */
	SelectFx.prototype._init = function() {
		// Check if we are using a placeholder for the native select box
		// We assume the placeholder is disabled and selected by default
		const selectedOption          = this.el.querySelector( 'option[selected]' );
		this.hasDefaultPlaceholder    = selectedOption && selectedOption.disabled;

		// get selected option (either the first option with attr selected or just the first option)
		this.selectedOption = selectedOption || this.el.querySelector( 'option' );

		// create structure
		this._createSelectEl();

		// all options
		this.selectOptions = [].slice.call( this.selEl.querySelectorAll( 'li[data-option]' ) );

		// total options
		this.selectOptionsCount = this.selectOptions.length;

		// current index
		this.current = this.selectOptions.indexOf( this.selEl.querySelector( 'li.branch-selected' ) ) || -1;

		// placeholder elem
		this.selPlaceholder = this.selEl.querySelector( 'span.branch-placeholder.jones_location_option' );

		// init events
		this._initEvents();
	};

	/**
	 * creates the structure for the select element
	 */
	SelectFx.prototype._createSelectEl = function() {
		const self   = this;
		let options = '';

		const createOptionHTML = function( el ) {
			let optionClass = '';
			let classes     = '';
			let	link        = '';

			if ( el.selectedOption && ! this.foundSelected && ! this.hasDefaultPlaceholder ) {
				classes            += 'branch-selected ';
				this.foundSelected  = true;
			}

			// Extra classes
			if ( el.getAttribute( 'data-class' ) ) {
				classes += el.getAttribute( 'data-class' );
			}

			// Link options
			if ( el.getAttribute( 'data-link' ) ) {
				link = 'data-link=' + el.getAttribute( 'data-link' );
			}

			if ( '' !== classes ) {
				// optionClass = 'class="' + classes + '" ';
				optionClass = `class="${classes}" `;
			}

			// return '<li ' + optionClass + link + ' data-option data-value="' + el.value + '"><span>' + el.textContent + '</span></li>';
			return `<li ${optionClass} ${link} data-option data-value="${el.value}"><span>${el.textContent}</span></li>`;
		};

		// [].slice.call( this.el.children ).forEach( function( el ) {
		Array.prototype.slice.call( this.el.children ).forEach( function( el ) {
			if ( el.disabled ) {
				return;
			}

			const tag = el.tagName.toLowerCase();

			if ( tag === 'option' ) {
				options += createOptionHTML( el );
			} else if ( tag === 'optgroup' ) {
				// options += '<li class="branch-optgroup"><span>' + el.label + '</span><ul>';
				options += `<li class="branch-optgroup"><span>${el.label}</span><ul>`;
				// [].slice.call( el.children ).forEach( function( opt ) {
				Array.prototype.slice.call( el.children ).forEach( function( opt ) {
					options += createOptionHTML( opt );
				});
				options += '</ul></li>';
			}
		});

		// const optionsElement = '<div class="branch-options"><ul>' + options + '</ul></div>';
		const optionsElement = `<div class="branch-options"><ul>${options}</ul></div>`;
		this.selEl           = document.createElement( 'div' );
		this.selEl.className = this.el.className;
		this.selEl.tabIndex  = this.el.tabIndex;
		this.selEl.innerHTML = `<span class="branch-placeholder jones_location_option">${this.selectedOption.textContent}</span>${optionsElement}`;
		this.el.parentNode.appendChild( this.selEl );
		this.selEl.appendChild( this.el );
	};

	/**
	 * initialize the events
	 */
	SelectFx.prototype._initEvents = function() {
		const self = this;

		// open/close select
		this.selPlaceholder.addEventListener( 'click', function() {
			self._toggleSelect();
		});

		// clicking the options
		this.selectOptions.forEach( function( opt, idx ) {
			opt.addEventListener( 'click', function() {
				self.current = idx;
				self._changeOption();
				// close select elem
				self._toggleSelect();
			});
		});

		// close the select element if the target it´s not the select element or one of its descendants..
		document.addEventListener( 'click', function( ev ) {
			const target = ev.target;
			if ( self._isOpen() && target !== self.selEl && ! hasParent( target, self.selEl ) ) {
				self._toggleSelect();
			}
		});

		// Control Keyboard Navigation Events
		// USE UP & DOWN ARROWS TO GO THROUGH CHOICES
		// USE SPACE AND ENTER TO SELECT
		// USE ESCAPE TO TOGGLE THE SELECTIONS OFF
		this.selEl.addEventListener( 'keydown', function( ev ) {
			const keyCode = ev.key || ev.code;

			switch ( keyCode ) {
			// Up arrow key - 38.
			case 'ArrowUp':
				ev.preventDefault();
				self._navigateOpts( 'prev' );
				break;
				// Down down key - 40
			case 'ArrowDown':
				ev.preventDefault();
				self._navigateOpts( 'next' );
				break;
				// Space key - 32
			case 'Space':
				ev.preventDefault();
				if ( self._isOpen() && typeof self.preSelCurrent !== 'undefined' && self.preSelCurrent !== -1 ) {
					self._changeOption();
				}
				self._toggleSelect();
				break;
				// Enter key - 13
			case 'Enter':
				ev.preventDefault();
				if ( self._isOpen() && typeof self.preSelCurrent !== 'undefined' && self.preSelCurrent !== -1 ) {
					self._changeOption();
					self._toggleSelect();
				}
				break;
				// esc key 27
			case 'Escape':
				ev.preventDefault();
				if ( self._isOpen() ) {
					self._toggleSelect();
				}
				break;
			}
		});
	};

	/**
	 Navigate With Up/Down Keys.
	 @link https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/dir
	 */
	SelectFx.prototype._navigateOpts = function( dir ) {
		if ( ! this._isOpen() ) {
			this._toggleSelect();
		}

		const tmpcurrent = typeof this.preSelCurrent !== 'undefined' && this.preSelCurrent !== -1 ? this.preSelCurrent : this.current;

		// if ( dir === 'prev' && tmpcurrent > 0 || dir === 'next' && tmpcurrent < this.selectOptionsCount - 1 ) {
		if (
			( dir === 'prev' && 0 < tmpcurrent ) ||
			( dir === 'next' && ( this.selectOptionsCount - 1 ) > tmpcurrent )
		) {
			// save pre selected current - if we click on option, or press enter, or press space this is going to be the index of the current option
			this.preSelCurrent = dir === 'next' ? tmpcurrent + 1 : tmpcurrent - 1;
			// remove focus class if any..
			this._removeFocus();
			// add class focus - track which option we are navigating
			classie.add( this.selectOptions[ this.preSelCurrent ], 'branch-focus' );
		}
	};

	/**
	 * open/close select
	 * when opened show the default placeholder if any
	 */
	SelectFx.prototype._toggleSelect = function() {
		// remove focus class if any..
		this._removeFocus();

		if ( this._isOpen() ) {
			if ( this.current !== -1 ) {
				// update placeholder text
				this.selPlaceholder.textContent = this.selectOptions[ this.current ].textContent;
			}
			classie.remove( this.selEl, 'branch-active' );
		} else {
			if ( this.hasDefaultPlaceholder && this.options.stickyPlaceholder ) {
				// everytime we open we wanna see the default placeholder text
				this.selPlaceholder.textContent = this.selectedOption.textContent;
			}
			classie.add( this.selEl, 'branch-active' );
		}
	};

	/**
	 * change option - the new value is set
	 */
	SelectFx.prototype._changeOption = function() {
		// if pre selected current (if we navigate with the keyboard)...
		if ( typeof this.preSelCurrent !== 'undefined' && this.preSelCurrent !== -1 ) {
			this.current = this.preSelCurrent;
			this.preSelCurrent = -1;
		}

		// current option
		const opt = this.selectOptions[ this.current ];

		// update current selected value
		this.selPlaceholder.textContent = opt.textContent;

		// change native select element´s value
		this.el.value = opt.getAttribute( 'data-value' );

		// remove class branch-selected from old selected option and add it to current selected option
		const oldOpt = this.selEl.querySelector( 'li.branch-selected' );
		if ( oldOpt ) {
			classie.remove( oldOpt, 'branch-selected' );
		}
		classie.add( opt, 'branch-selected' );

		// if there´s a link defined
		if ( opt.getAttribute( 'data-link' ) ) {
			// open in new tab?
			if ( this.options.newTab ) {
				window.open( opt.getAttribute( 'data-link' ), '_blank' );
			} else {
				window.location = opt.getAttribute( 'data-link' );
			}
		}

		// callback
		this.options.onChange( this.el.value );
	};

	/**
	 * returns true if select element is opened
	 */
	SelectFx.prototype._isOpen = function( opt ) {
		return this.selEl.classList.contains( 'branch-active' );
	};

	/**
	 * removes the focus class from the option
	 */
	SelectFx.prototype._removeFocus = function( opt ) {
		const focusElement = this.selEl.querySelector( 'li.branch-focus' );
		if ( focusElement ) {
			// classie.remove( focusElement, 'branch-focus' );
			focusElement.classList.remove( 'branch-focus' );
		}
	};

	/**
	 * add to global namespace
	 */
	window.SelectFx = SelectFx;
}( window ) );
