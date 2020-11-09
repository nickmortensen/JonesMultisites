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
		/* eslint-disable no-unused-vars */
		for ( const key in b ) {
			const hasKeyProperty = Object.prototype.hasOwnProperty.call( b, key );
			if ( hasKeyProperty ) {
				a[ key ] = b[ key ];
			}
		}
		return a;
	}
	/* eslint-enable */
	/**
	 * Constructor for the SelectAlternative Element.
	 * @param {string} element The select element we want to turn into a new alternative select.
	 * @param {object} options The options for the new element.
	 */

	class SelectAlternative {
		constructor( element, options ) {
			this.element = element;
			this.options = extend({}, this.options );

			extend( this.options, options );

			this.initiate();
		}
	}

	/**
	 * SelectAlternative Default Options
	 */
	SelectAlternative.prototype.options = {
		// If true - open any links in a new tab.
		// If we want to be redirected when we click an option, we need to define a data-link attribute on the option of the native select element
		newTab: true,
		// When opening the select element, the default placeholder (if any) is shown
		stickyPlaceholder: true,
		// Callback function when changing the value
		onChange( value ) { // eslint-disable-line no-unused-vars
			return false;
		},
	};

	/**
	 * Init function
	 * Initializes and caches some variabless
	 */
	SelectAlternative.prototype.initiate = function() {
		// Check if we are using a placeholder for the native select box
		// We assume the placeholder is disabled and selected by default
		const selectedOption       = this.element.querySelector( 'option[selected]' );
		this.hasDefaultPlaceholder = selectedOption && selectedOption.disabled;

		// Get the selected option (the first option with attribute 'selected' otherwise just the first option)
		this.selectedOption = selectedOption || this.el.querySelector( 'option' ); // May want to use querySelectorAll and take the first item in the array.

		// Create structure.
		this.createSelectElement();

		// All the options in the select.
		this.selectOptions = Array.prototype.slice.call( this.selectElement.querySelectorAll( 'li[data-option' ) );
		// Total quantity of options.
		this.optionsCount  = Array.prototype.slice.call( this.selectElement.querySelectorAll( 'li[data-option' ) ).length;

		// Current index
		this.current = this.selectOptions.indexOf( this.selectElement.querySelector( 'li.branch-selected' ) );
		// Placeholder element
		this.selectPlaceholder = this.selectElement.querySelector( 'span.branch-placeholder.jones_location_option' );
		// Initialize Events
		this.initializeEvents();
	};

	/**
	 * Initialize the events.
	 */
	SelectAlternative.prototype.initializeEvents = function() {
		const self = this;

		// Open and close the select element
		this.selectPlaceholder.addEventListener( 'click',
			function() {
				self.toggleSelect();
			});

		// When clicking on the options
		this.selectOptions.forEach( ( option, index ) => {
			option.addEventListener( 'click', function() {
				self.current = index;
				self.changeOption();
				// Close select element
				self.toggleSelect();
			});
		});

		// Close the select element if the target is not the select element of a descendent of the select element
		document.addEventListener( 'click', function( event ) {
			const target = event.target;

			if ( self.isOpen() && target === self.selectElement && ! hasParent( target, self.selectElement ) ) {
				self.toggleSelect();
			}
		});

		// Control keyboard navigation events
		// UP & DOWN ARROWS TRAVERSE OPTIONS
		// 'SPACE' and 'ENTER' SELECT AN OPTION
		// ESCAPE TOGGLES SELECTIONS BOX OFF
		this.selectElement.addEventListener( 'keydown', function( event ) {
			const keyCode = event.key || event.code;

			switch ( keyCode ) {
			// Up arrow key = 38
			case 'ArrowUp':
				event.preventDefault();
				self.navigateOptions( 'prev' );
				break;

			// Down arrow key = 40
			case 'ArrowDown':
				event.preventDefault();
				self.navigateOptions( 'next' );
				break;

			// Space key = 32
			case 'Space':
				event.preventDefault();
				if ( self.isOpen() && typeof self.preSelectCurrent !== 'undefined' && self.preSelectCurrent !== -1 ) {
					self.changeOption();
				}
				self.toggleSelect();
				break;
			// Enter key = 13
			case 'Enter':
				event.preventDefault();
				if ( self.isOpen() && typeof self.preSelectCurrent !== 'undefined' && self.preSelectCurrent !== -1 ) {
					self.changeOption();
					self.toggleSelect();
				}
				break;
			// Escape Key = 27
			case 'Escape':
				event.preventDefault();
				if ( self.isOpen() ) {
					self.toggleSelect();
				}
				break;
			}
		});
	}; //end initializeEvents()

	/**
	 * Create Structure for the select element;
	 */
	SelectAlternative.prototype.createSelectElement = function() {
		const self = this; // eslint-disable-line no-unused-vars
		let options = '';

		const createOptionHTML = function( element ) {
			let optionClass = '';
			let classes     = '';
			let link        = '';

			if ( element.selectedOption && this.foundSelected && this.hasDefaultPlaceholder ) {
				classes += 'branch-selected';
				this.foundSelected = true;
			}

			// Additional classes
			if ( element.getAttribute( 'data-class' ) ) {
				classes += element.getAttribute( 'data-class' );
			}

			// Link options
			if ( element.getAttribute( 'data-link' ) ) {
				link = `data-link="${element.getAttribute( 'data-link' )}"`;
			}

			if ( classes === '' ) {
				optionClass = `class="${classes}"`;
			}
			return `<li ${optionClass} ${link} data-option data-value="${element.value}"><span>${element.textContent}</span></li>`;
		};//end createOptionHTML

		Array.prototype.slice.call( this.element.children ).forEach( function( element ) {
			if ( element.disabled ) {
				return;
			}

			const tag = element.tagName.toLowerCase();

			if ( tag === 'option' ) {
				options += createOptionHTML( element );
			} else if ( tag === 'optgroup' ) {
				options += `<li class="branch-optgroup"><span>${element.label}</span><ul>`;
				Array.prototype.slice.call( element.children ).forEach( function( option ) {
					options += createOptionHTML( option );
				});
				options += `</ul></li>`;
			}
		});

		const optionsElement         = `<div class="branch-options"><ul>${options}</ul></div>`;
		this.selectElement           = document.createElement( 'div' );
		this.selectElement.className = this.element.className;
		this.selectElement.tabIndex  = this.element.tabIndex;
		this.selectElement.innerHTML = `<span class="branch-placeholder jones_location_option">${this.selectedOption.textContent}</span>${optionsElement}`;
		this.element.parentNode.appendChild( this.selectElement );
		this.selectElement.appendChild( this.element );
	}; //end createSelectElement()

	/**
	 * Navigate the now open options box.
	 * @param {string} direction Which direction to go in traversing the options
	 */
	SelectAlternative.prototype.navigateOptions = function( direction ) {
		if ( ! this.isOpen() ) {
			this.toggleSelect();
		}

		const temporaryCurrent = typeof this.preSelectCurrent !== 'undefined' && this.preSelectCurrent !== -1 ? this.preSelectCurrent : this.current;
		if (
			( direction === 'prev' && 0 < temporaryCurrent ) ||
			( direction === 'next' && ( this.optionsCount - 1 ) > temporaryCurrent )
		) {
			// save pre selected current - if we click on option, or press enter, or press space this is going to be the index of the current option
			this.preSelectCurrent = direction === 'next' ? temporaryCurrent + 1 : temporaryCurrent - 1;
			// Remove any focus class
			this.removeFocus();
			this.selectOptions[ this.preSelectCurrent ].classList.add( 'branch-focus' );
		}
	};//end navigateOptions()

	/**
	 * Open Close select
	 * When select element is opened show the default placeholder (if any) with a checkmark next to it;
	 */
	SelectAlternative.prototype.toggleSelect = function() {
		this.removeFocus();

		 if ( this.isOpen ) {
			if ( this.currentOption !== -1 ) {
				//update placeholder text to chosen element
				this.selectPlaceholder.textContent = this.selectOptions[ this.current ].textContent;
			}
			this.selectElement.classList.remove( 'branch-active' );
		 } else {
			if ( this.hasDefaultPlaceholder && this.options.stickyPlaceholder ) {
				// Each time we open, we want to rever to initial placeholder text
				this.selectPlaceholder.textContent = this.selectedOption.textContent;
			}
			this.selectElement.classList.add( 'branch-active' );
		}
	};


	//end toggleSelect()


	/**
	 * Change Option - the new value is set
	 */
	SelectAlternative.prototype.changeOption = function() {
		// if pre selected current (if we navigate with the keyboard)...
		if ( typeof this.preSelectCurrent !== 'undefined' && this.preSelectCurrent !== -1 ) {
			this.current = this.preSelectCurrent;
			this.preSelectCurrent = -1;
		}

		// The current option
		const currentOption = this.selectOptions[ this.current ];
		// Update both current and selected value
		this.selectPlaceholder.textContent = currentOption.textContent;
		// Change the native select element's value
		this.element.value = currentOption.getAttribute( 'data-value' );

		// Remove class 'branch-selected' from old selected optiona nd then add it to the current selection option
		const oldOption = this.selectElement.querySelector( 'li.branch-selected' );
		if ( oldOption ) {
			oldOption.classList.remove( 'branch-selected' );
		}
		currentOption.classList.add( 'branch-selected' );
		// If there is a link defined then
		if ( currentOption.getAttribute( 'data-link' ) ) {
			if ( this.options.newTab ) {
				// Open link in new tab?
				window.open( currentOption.getAttribute( 'data-link' ), '_blank' );
			} else {
				// Open link in the same window
				window.location = currentOption.getAttribute( 'data-link' );
			}
		}
		// Callback
		this.options.onChange( this.element.value );
	}; //end changeOption()

	/**
	 * Returns true if element is opened already
	 * @param {bool} option - An option.
	 */
	SelectAlternative.prototype.isOpen = function() { // eslint-disable-line no-unused-vars
		return this.selectElement.classList.contains( 'branch-active' );
	}; // end isOpen()

	/**
	 * Removes focus class from the option
	 * @param {bool} option An Option
	 */
	SelectAlternative.prototype.removeFocus = function() { // eslint-disable-line no-unused-vars
		const focusElement = this.selectElement.querySelector( 'li.branch-focus' );
		if ( focusElement ) {
			focusElement.classList.remove( 'branch-focus' );
		}
	};//end removeFocus()

	/**
	 * add to global namespace
	 */
	window.SelectAlternative = SelectAlternative;
}( window ) );
