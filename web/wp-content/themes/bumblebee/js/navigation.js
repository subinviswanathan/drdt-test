/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function () {
	var container, button, menu, links, i, len;

	container = document.getElementById( 'site-navigation' );
	if ( ! container ) {
		return;
	}

	button = container.getElementsByTagName( 'button' )[0];
	if ('undefined' === typeof button ) {
		return;
	}

	menu = container.getElementsByTagName( 'ul' )[0];

	// Hide menu toggle button if menu is empty and return early.
	if ('undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	menu.setAttribute( 'aria-expanded', 'false' );
	if (-1 === menu.className.indexOf( 'nav-menu' ) ) {
		menu.className += ' nav-menu';
	}

	button.onclick = function () {
		if (-1 !== container.className.indexOf( 'toggled' ) ) {
			container.className = container.className.replace( ' toggled', '' );
			button.setAttribute( 'aria-expanded', 'false' );
			menu.setAttribute( 'aria-expanded', 'false' );
		} else {
			container.className += ' toggled';
			button.setAttribute( 'aria-expanded', 'true' );
			menu.setAttribute( 'aria-expanded', 'true' );
		}
	};

	// Get all the link elements within the menu.
	links = menu.getElementsByTagName( 'a' );

	// Each time a menu link is focused or blurred, toggle focus.
	for ( i = 0, len = links.length; i < len; i++ ) {
		links[i].addEventListener( 'focus', toggleFocus, true );
		links[i].addEventListener( 'blur', toggleFocus, true );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus()
	{
		var self = this;

		// Move up through the ancestors of the current link until we hit .nav-menu.
		while ( -1 === self.className.indexOf( 'nav-menu' ) ) {

			// On li elements toggle the class .focus.
			if ('li' === self.tagName.toLowerCase() ) {
				if (-1 !== self.className.indexOf( 'focus' ) ) {
					self.className = self.className.replace( ' focus', '' );
				} else {
					self.className += ' focus';
				}
			}

			self = self.parentElement;
		}
	}

	/**
	 * Toggles `focus` class to allow submenu access on tablets.
	 */
	( function ( container ) {
		var touchStartFn, i,
			parentLink = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

		if ('ontouchstart' in window ) {
			touchStartFn = function ( e ) {
				var menuItem = this.parentNode, i;

				if ( ! menuItem.classList.contains( 'focus' ) ) {
					e.preventDefault();
					for ( i = 0; i < menuItem.parentNode.children.length; ++i ) {
						if (menuItem === menuItem.parentNode.children[i] ) {
							continue;
						}
						menuItem.parentNode.children[i].classList.remove( 'focus' );
					}
					menuItem.classList.add( 'focus' );
				} else {
					menuItem.classList.remove( 'focus' );
				}
			};

			for ( i = 0; i < parentLink.length; ++i ) {
				parentLink[i].addEventListener( 'touchstart', touchStartFn, false );
			}
		}
	}( container ) );
} )();
(function($){
	var mobile_width = 767;
	var mobile_inner_width = 480;

	//showing hamburger icon and 'MENU' text (only if JS is available)
	show_menu_icon();

	//reloading the page for mobiles with height more than 767px on orientation change
	var isMobile = /iPhone|Android/i.test(navigator.userAgent);
	if( isMobile && ( window.innerHeight > mobile_width || window.innerWidth > mobile_inner_width ) ) {
		$(window).on('orientationchange', function() {
			location.reload();
		});
	}


	var menu = $('.menu-toggle, .hamburger-close');
	menu.on('click', function( e ) {
		e.preventDefault();

		var show = $('.pure-menu-children.hamburger-menu-items').css('display') !== 'block';

		if ( show ) {
			$('.pure-menu-children.hamburger-menu-items').css('display', 'block').css('left', '0');
			$('.hamburger').toggleClass('hide-ham-sign');
			$('.hamburger-close').toggleClass('hide-ham-sign');
			var menu_wrapper = get_menu_wrapper();
			var drop_down_menu = jQuery('.menu-hamburger-menu-container');
			drop_down_menu.appendTo(menu_wrapper);
			if( ! $('.menu-hamburger-menu-container').hasClass('slinky-menu')) {
				$('.menu-hamburger-menu-container').slinky({
					title: true,
					resize: true,
					speed: 400,
				});
			}
		} else {
			$('.pure-menu-children.hamburger-menu-items').css('display', 'none');
			$('.hamburger').toggleClass('hide-ham-sign');
			$('.hamburger-close').toggleClass('hide-ham-sign');
		}
	});


	function get_menu_wrapper(){
		var menu_wrapper;
		if( window.innerWidth <= mobile_width) {
			menu_wrapper = $('.hamburger-wrapper.desktop-hide .pure-menu-item');
		} else {
			menu_wrapper = $('.hamburger-wrapper.mobile-hide .pure-menu-item');
		}
		return menu_wrapper;
	}

	function show_menu_icon(){
		var menu_wrapper_mobile = $('.hamburger-wrapper.desktop-hide');
		var menu_wrapper_desktop = $('.hamburger-wrapper.mobile-hide');
		if( window.innerWidth <= mobile_width) {
			menu_wrapper_mobile.css('display', 'flex');
			menu_wrapper_desktop.css('display', 'none');
		} else {
			menu_wrapper_desktop.css('display', 'flex');
			menu_wrapper_mobile.css('display', 'none');
		}
	}

	// Sticky Nav (Desktop)
	$(window).scroll(function() {
		var scroll = $(window).scrollTop();
		if (scroll >= 140) {
			$('nav').addClass('sticky');
		} else {
			$('nav').removeClass('sticky');
		}
	});
	/* global jQuery */
})(jQuery);
