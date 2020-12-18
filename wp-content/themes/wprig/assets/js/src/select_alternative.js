/**
 * SelectAlternative.js v1.0.0
 * http://www.nickmortensen.com.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 */

/**
 * Extend obj function
 * @link https://eslint.org/docs/rules/no-prototype-builtins
 */

/**
 * based on from https://github.com/inuyaksa/jquery.nicescroll/blob/master/jquery.nicescroll.js
 */

class SelectAlternative { // eslint-disable-line no-unused-vars
	/**
	 * Constructor for the SelectAlternative Element.
	 * @param {string} element The select element we want to turn into a new alternative select.
	 * @param {object} options The options for the new element.
	 */
	constructor( element, options ) {
		this.element = element;
		this.options = extend({}, {
			// If true - open any links in a new tab.
			// If we want to be redirected when we click an option, we need to define a data-link attribute on the option of the native select element
			newTab: true,
			// When opening the select element, the default placeholder (if any) is shown
			stickyPlaceholder: true,
			// Callback function when changing the value
			onChange( value ) { // eslint-disable-line no-unused-vars
				return false;
			},
		});

		extend( this.options, options );

		this.initiate();
	} //end constructor -- no comma is important <-- methods inside classes cannot hace commas

	/**
	 * Init function
	 * Initializes and caches some variabless
	 */
	initiate() {
		// Check if we are using a placeholder for the native select box
		// We assume the placeholder is disabled and selected by default
		const selectedOption       = this.element.querySelector( 'option[selected]' );
		this.hasDefaultPlaceholder = selectedOption && selectedOption.disabled;
		// Get the selected option (the first option with attribute 'selected' otherwise just the first option)
		this.selectedOption        = selectedOption || this.element.querySelector( 'option' ); // May want to use querySelectorAll and take the first item in the array.
		// Create structure.
		this.createSelectElement();
		// All the options in the select.
		this.selectOptions         = Array.prototype.slice.call( this.selectElement.querySelectorAll( 'li[data-option]' ) );
		// Total quantity of options.
		this.optionsCount          = this.selectOptions.length;
		// Current index
		this.current               = this.selectOptions.indexOf( this.selectElement.querySelector( 'li.location-selected' ) );
		// Placeholder element
		this.selectPlaceholder     = this.selectElement.querySelector( 'span.location-placeholder' );
		// Initialize Events
		this.initializeEvents();
	}//end method initiate()

	/**
	 * Initialize the events.
	 */
	initializeEvents() {
		const self = this;

		// Open and close the select element
		this.selectPlaceholder.addEventListener( 'click',
			function() {
				self.toggleSelect();
			});

		// When clicking on the options
		this.selectOptions.forEach( function( option, index ) {
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

			if ( self.isOpen() && target !== self.selectElement && ! hasParent( target, self.selectElement ) ) {
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
				if ( self.isOpen() && typeof self.preSelectCurrent != 'undefined' && self.preSelectCurrent !== -1 ) { //eslint-disable-line eqeqeq
					self.changeOption();
				}
				self.toggleSelect();
				break;
			// Enter key = 13
			case 'Enter':
				event.preventDefault();
				if ( self.isOpen() && typeof self.preSelectCurrent != 'undefined' && self.preSelectCurrent !== -1 ) { //eslint-disable-line eqeqeq
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
	}//end initializeEvents()

	/**
	 * Create Structure for the select element;
	 */
	createSelectElement() {
		const self  = this; // eslint-disable-line no-unused-vars
		let options = '';

		const createOptionHTML = function( element ) {
			let optionClass = '';
			let classes     = '';
			let link        = '';

			if ( element.selectedOption && ! this.foundSelected && ! this.hasDefaultPlaceholder ) {
				classes += 'location-selected ';
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

			if ( classes !== '' ) {
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
				options += `<li class="location-optgroup"><span>${element.label}</span><ul>`;
				Array.prototype.slice.call( element.children ).forEach( function( option ) {
					options += createOptionHTML( option );
				});
				options += `</ul></li>`;
			}
		});

		const optionsElement         = `<div class="location-options"><ul>${options}</ul></div>`;
		this.selectElement           = document.createElement( 'div' );
		this.selectElement.className = this.element.className;
		this.selectElement.tabIndex  = this.element.tabIndex;
		this.selectElement.innerHTML = `<span class="location-placeholder">${this.selectedOption.textContent}</span>${optionsElement}`;
		this.element.parentNode.appendChild( this.selectElement );
		this.selectElement.appendChild( this.element );
	}//end createSelectElement()

	/**
	 * Navigate the now open options box.
	 * @param {string} direction Which direction to go in traversing the options ('prev' or 'next')
	 */
	navigateOptions( direction ) {
		const d = direction;
		if ( ! this.isOpen() ) {
			this.toggleSelect();
		}

		const temporaryCurrent = typeof this.preSelectCurrent != 'undefined' && this.preSelectCurrent !== -1 ? this.preSelectCurrent : this.current; //eslint-disable-line eqeqeq, no-mixed-operators
		if ( d === 'prev' && temporaryCurrent > 0 || d === 'next' && temporaryCurrent < this.optionsCount - 1 ) { //eslint-disable-line no-mixed-operators
			// save pre selected current - if we click on option, or press enter, or press space this is going to be the index of the current option
			this.preSelectCurrent = d === 'next' ? temporaryCurrent + 1 : temporaryCurrent - 1;
			// Remove any focus class
			this.removeFocus();
			this.selectOptions[ this.preSelectCurrent ].classList.add( 'location-focus' );
		}
	}//end navigateOptions( direction )

	/**
	 * Open Close select
	 * When select element is opened show the default placeholder (if any) with a checkmark next to it;
	 */
	toggleSelect() {
		this.removeFocus(); // Remove focus, if any is appplied already

		if ( this.isOpen() ) {
			if ( this.current !== -1 ) {
				//update placeholder text to chosen element
				this.selectPlaceholder.textContent = this.selectOptions[ this.current ].textContent;
			}
			this.selectElement.classList.remove( 'location-active' );
		} else {
			if ( this.hasDefaultPlaceholder && this.options.stickyPlaceholder ) {
				// Each time we open, we want to rever to initial placeholder text
				this.selectPlaceholder.textContent = this.selectedOption.textContent;
			}
			this.selectElement.classList.add( 'location-active' );
		}
	}//end toggleSelect()

	/**
	 * Change Option - the new value is set
	 */
	changeOption() {
		// if pre selected current (if we navigate with the keyboard)...
		if ( typeof this.preSelectCurrent != 'undefined' && this.preSelectCurrent !== -1 ) { //eslint-disable-line eqeqeq
			this.current          = this.preSelectCurrent;
			this.preSelectCurrent = -1;
		}

		// The current option
		const currentOption                = this.selectOptions[ this.current ];
		// Update both current and selected value
		this.selectPlaceholder.textContent = currentOption.textContent;
		// Change the native select element's value
		this.element.value                 = currentOption.getAttribute( 'data-value' );
		/*** FLAGGING **/
		this.element.value                 = currentOption.dataset.value;
		// Remove class 'location-selected' from old selected optiona nd then add it to the current selection option
		const oldOption                    = this.selectElement.querySelector( 'li.location-selected' );

		if ( oldOption ) {
			oldOption.classList.remove( 'location-selected' );
		}
		currentOption.classList.add( 'location-selected' );

		// If there is a link defined using 'data-link' attribute
		if ( currentOption.getAttribute( 'data-link' ) ) {
			// If we specify that option links should open in a new tab, then let us open them in a new tab, otherwise open within the same window
			if ( this.options.newTab ) {
				window.open( currentOption.getAttribute( 'data-link' ), '_blank' ); // Open link in new tab
			} else {
				window.location = currentOption.getAttribute( 'data-link' ); // Open link in the same window
			}
		}

		// Callback
		this.options.onChange( this.element.value );
	} //end changeOption()

	/**
	 * Returns true if element is opened already
	 * @param {bool} option - An option.
	 */
	isOpen( option ) { // eslint-disable-line no-unused-vars
		return this.selectElement.classList.contains( 'location-active' );
	} //end isOpen()

	/**
	 * Removes focus class from the option
	 * @param {bool} option An Option
	 */
	removeFocus( option ) { // eslint-disable-line no-unused-vars
		const focusElement = this.selectElement.querySelector( 'li.location-focus' );
		if ( focusElement ) {
			focusElement.classList.remove( 'location-focus' );
		}
	}//end removeFocus()
}//end class SelectAlternative

/*
 * Determine whether an element has a parent
 */
function hasParent( e, p ) {
	if ( ! e ) {
		return false;
	}
	let element = e.target || e || false;
	while ( element && element !== p ) {
		element = element.parentNode || false;
	}
	return ( element !== false );
}

/**
 * Extend obj function
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

