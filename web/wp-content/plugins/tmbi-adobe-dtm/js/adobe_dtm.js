/**
 * Created by jeysaravana on 2017-01-11.
 */

var qs = (function() {
	query_string_url = window.location.search.substr(1).split('&');
	if ( query_string_url === "" ) return {};
	var query_strings = {};
	for ( var i = 0; i < query_string_url.length; ++i ) {
		var parameter = query_string_url[i].split('=', 2);
		if ( parameter.length === 1 ) {
			query_strings[parameter[0]] = "";
		} else {
			query_strings[parameter[0]] = decodeURIComponent( parameter[1].replace( /\+/g, " " ) );
		}
	}
	return query_strings;
})();

//Nelio A/B Testing
if ( qs['nabt'] !== undefined ) {
	//TODO setup adobe variables.
	//for original => nabt = 0
	//for alternatice => nabt = 1
	var test_name = 'tohdesign:a_newdesign';
	if ( qs['nabt'] === '1' ) {
		test_name = 'tohdesign:b_currentdesign';
	}
	digitalData.ab = digitalData.ab||{};
	digitalData.ab.testing = test_name; //qs['nabt']
}

jQuery( document ).ready(function($) {

	if ( qs['ehid'] !== undefined ) {
		localStorage.newsletter 	= qs['ehid'];
		digitalData.newsletter      = digitalData.newsletter||{};
		digitalData.newsletter.ehid = qs['ehid'];
	}
	if ( typeof( localStorage.newsletter ) != 'undefined' && typeof qs['ehid'] == 'undefined' ){
		digitalData.newsletter      = digitalData.newsletter||{};
		digitalData.newsletter.ehid = localStorage.newsletter;
	}

	//RD Related changes
	$("#more-from-category").find('a').each(function() {
		replace_data_attr( $(this), 'main stage - list', 'more about' );
	});
	$('.menu-footer-social-container').find('a').each(function() {
		replace_data_attr( $(this), 'follow us', 'footer' );
	});

	$('.page-unsubscribe .ninja-forms-form').submit(function() {
		do_adobe_data_analytics( 'Save Preference', 'newsletter preferences', 'opt out page' );
	});

	$('.joke .read-more').on('click', function() {

		var joke_block = $(this).closest('.joke');
		if (typeof do_adobe_data_analytics === 'function') {
			var name = joke_block.find('.entry-header .entry-title a').text();
			do_adobe_data_analytics(name, 'content navigation', 'main content overlay');
		}
	});

    //TOH Related Changes
    var login_status = 'false';
	var userData = {"profile" : { "profileInfo" : {}}}
    if ( getCookie( 'ID' ) !== undefined ) {
        login_id = getCookie( 'ID' );
        login_status = 'true';
        userData["profile"]["profileInfo"]["profileID"] = login_id;

    }
    userData["profile"]["profileInfo"]["profileStatus"] = login_status;
    digitalData.user = userData;

	//FHM Related Changes
	$('#genesis-sidebar-primary').find('.menu-social-profiles-container a').each(function() {
		var social_profiles = $(this).attr('data-analytics-metrics');
		social_profiles = social_profiles.replace('footer', 'right rail');
		$(this).attr('data-analytics-metrics', social_profiles);
	});

	var $body = $("body");
	$body.on("click", "[data-analytics-metrics]", function (e) {
		e.stopPropagation();
		if ( $( this ).is( "form" ) ) {
			return true;
		}
		console.log('im here');
		adobe_data_analytics( $( this ) );
	});

	$body.on("submit", "[data-analytics-metrics]", function () {
		adobe_data_analytics( $( this ) );
	});

	/* fireupDirectcallRule "slide click"  */
	if ( digitalData.page.content.slideShowMulti === true && typeof ( _satellite ) != 'undefined' ) {

		//setting up event50 to page load event.
		_satellite.getToolsByType('sc')[0].events.push('event50');

		digitalData.click = digitalData.click||{};

		//toh recipe title
		var recipe_title = $('#collection-wrapper').find('h4');
		if ( recipe_title.length > 0 ) {
			digitalData.click.slideShowRecipeTitle = recipe_title.text();
			satellite_track('slideShowRecipeTitle');
		}

		var list_nav_top = $('#listicle-nav-top');
		var card_count = parseInt(list_nav_top.find('.rd-card-count').text());

		$('.js--next.js--listicle-nav').click(function (ev) {
			var card_no = parseInt(list_nav_top.find('.rd-card-index').text());
			var next_card = card_no + 1;
			set_click_total_slideshows( card_no, card_count );
			set_click_data( 'next', 'slideshows', 'header' );
			if ( ev.isDefaultPrevented() ) {
				next_card = card_no;
			}
			set_digitalData_slideshow_ad();

			if( next_card < card_count ) {
				satellite_track('slide click');
			} else if( next_card === card_count) {
				satellite_track('slide click completed');
			}
		} );
		$('.js--prev.js--listicle-nav').click(function (ev) {
			var card_no = parseInt(list_nav_top.find('.rd-card-index').text());
			set_click_total_slideshows( card_no, card_count );
			set_click_data( 'previous', 'slideshows', 'header' );
			var prev_card = card_no;
			if ( ev.isDefaultPrevented() ) {
				prev_card = card_no + 1;
			}
			if( prev_card > 1 ) {
				set_digitalData_slideshow_ad();
				satellite_track('slide click');
			}
		} );

		//fhm slideclick
		$('.carousel.slide .right.carousel-control').click( function (e) {
			set_digitalData_image_credits( $('div.active') );
			var pages_arr = $('.paginate').text().split( ' of ' );
			var current_card = parseInt(pages_arr[0]);
			var next_card = current_card + 1;
			var card_count = parseInt(pages_arr[1]);

			set_click_total_slideshows( current_card, card_count );
			set_click_data( 'next', 'slideshows', 'header' );

			if( next_card < card_count ) {
				if ( next_card % 4 === 0 ) {
					set_digitalData_slideshow_ad();
				}
				satellite_track('slide click');
			} else if( next_card === card_count) {
				satellite_track('slide click completed');
			}

			if( current_card === card_count) {
				satellite_track('slide click restart');
			}
		});

		$('.carousel.slide .left.carousel-control').click( function (e) {
			set_digitalData_image_credits( $('div.active') );
			var pages_arr = $('.paginate').text().split( ' of ' );
			var current_card = parseInt(pages_arr[0]);
			var card_count = parseInt(pages_arr[1]);

			set_click_total_slideshows( current_card, card_count );
			set_click_data( 'previous', 'slideshows', 'header' );

			if( current_card > 1 ) {
				if ( ( current_card - 1 ) % 4 === 0 ) {
					set_digitalData_slideshow_ad();
					//satellite_track('slideshowadevent');
				}
				satellite_track('slide click');
			}
		});
	}

	//toh re circ nav click next
	$('.single-recipe .recirc-item .owl-next').click(function() {
		do_adobe_data_analytics( 'next', 'content engagement', 'more from taste of home' );
	});

	//toh re circ nav click prev
	$('.single-recipe .recirc-item .owl-prev').click(function() {
		do_adobe_data_analytics( 'previous', 'content engagement', 'more from taste of home' );
	});

	//Added for WPDT-6535 & WPDT-6643
	//home page first slider
	$('.tmbi_hp_first_slider .owl-nav .owl-prev').click(function() {
		var slider_title = $('#tmbi_hp_first_slider h3').html();
		do_adobe_data_analytics( 'previous', 'content navigation', 'carousel: ' + slider_title );
	});

	$('.tmbi_hp_first_slider .owl-nav .owl-next').click(function() {
		var slider_title = $('#tmbi_hp_first_slider h3').html();
		do_adobe_data_analytics( 'next', 'content navigation', 'carousel: ' + slider_title );
	});

	//home page second slider
	$('.tmbi_hp_slider_second .owl-nav .owl-prev').click(function() {
		var slider_title = $('#tmbi_hp_slider_second h3').html();
		do_adobe_data_analytics( 'previous', 'content navigation', 'carousel: ' + slider_title );
	});

	$('.tmbi_hp_slider_second .owl-nav .owl-next').click(function() {
		var slider_title = $('#tmbi_hp_slider_second h3').html();
		do_adobe_data_analytics( 'next', 'content navigation', 'carousel: ' + slider_title );
	});

	//home page third slider
	$('.tmbi_hp_slider_third .owl-nav .owl-prev').click(function() {
		var slider_title = $('#tmbi_hp_slider_third h3').html();
		do_adobe_data_analytics( 'previous', 'content navigation', 'carousel: ' + slider_title );
	});

	$('.tmbi_hp_slider_third .owl-nav .owl-next').click(function() {
		var slider_title = $('#tmbi_hp_slider_third h3').html();
		do_adobe_data_analytics( 'next', 'content navigation', 'carousel: ' + slider_title );
	});

	//fhm re circ nav click next
	$('.single-project .recirc-item .owl-next').click(function() {
		do_adobe_data_analytics( 'next', 'content recirculation', 'similar projects' );
	});

	//fhm re circ nav click prev
	$('.single-project .recirc-item .owl-prev').click(function() {
		do_adobe_data_analytics( 'previous', 'content recirculation', 'similar projects' );
	});

	$('article.post .entry-content a').click(function() {
		do_adobe_data_analytics( $(this).html(), 'content recirculation', 'embedded' );
	});


	function replace_data_attr( attr, search, replace ) {
		if ( attr.attr('data-analytics-metrics') !== undefined ) {
			var data = attr.attr('data-analytics-metrics');
			data = data.replace( search, replace );
			attr.attr('data-analytics-metrics', data);
		}
	}

	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

});

// Trigger slide click on embedded slideshow swipe
function do_embedded_slide_click() {
	var s = _satellite.getToolsByType('sc')[0].getS();
	if ( s ) {
		s.clearVars();
		embedListicle_digitalData.click = {module: 'embedded slideshow', name: 'navigation', position: 'article page'};
		var digitalDataTemp = digitalData;
		digitalData = embedListicle_digitalData;
		_satellite.track('slide click');
		digitalData = digitalDataTemp;
	}
}

//toh collection analytics start
function do_collection_analytics( $currentTarget, $container ) {
	var clicked_button = $currentTarget.children()[0].className;
	var viewed_card    = $container.find( '.collection-pagination' ).text();
	var slide_title    = $container.find( 'h4' ).text();
	var current_card   = parseInt( viewed_card.split( ' of ' )[0] );
	var total_cards    = parseInt( viewed_card.split( ' of ' )[1] );
	var name;
	digitalData.click  = digitalData.click||{};
	digitalData.click.slideShowNumber      = viewed_card;
	digitalData.click.slideShowRecipeTitle = slide_title;

	if ( clicked_button === 'prev' ) {
		name = 'previous';
	} else if ( clicked_button === 'next' ) {
		name = 'next';
	}

	set_click_data( name, 'slideshows', 'header' );
	if ( clicked_button === 'next' ) {
		if ( current_card === total_cards ) {
			satellite_track('slide click restart');
		}
		if ( current_card === ( total_cards - 1 ) ) {
			satellite_track('slide click completed');
		}
		if ( current_card < total_cards ) {
			satellite_track('slide click');
		}
	}

	//set this to get recipetitle
	satellite_track('slideShowRecipeTitle');

}
//toh collection analytic end


function do_adobe_data_analytics( name, module, position ) {
	var data = {};
	data['attr'] = function( att_name ) {
		return '{"link_name":"' + name + '", "link_module":"' + module + '", "link_pos":"' + position + '"}';
	};
	adobe_data_analytics( data );
}

function adobe_data_analytics( data ) {
	if ( data.attr('data-analytics-metrics') !== undefined ) {
		var dataString;
		digitalData.click = {};
		dataString = data.attr('data-analytics-metrics');
		dataString = dataString.replace('link_name', 'name');
		dataString = dataString.replace('link_module', 'module');
		dataString = dataString.replace('link_pos', 'position');
		digitalData.click = JSON.parse(dataString);

		//Prevent link click not trigger on new slideshow next previous
		if ( digitalData.page.theme === 'tmbi-theme-v3'
			&& digitalData.click.module === 'slideshows'
			&& ( digitalData.click.name === 'next' || digitalData.click.name === 'previous' )
		) {
			return;
		}
		satellite_track("link click");
	}
}

function satellite_track( track_name ) {
	if( typeof _satellite !== 'undefined' && typeof _satellite.track === 'function' ) {
		_satellite.track( track_name );
	}
}

// Manually setting up click variables.
function set_click_data( name, module, position ) {
	digitalData.click = digitalData.click||{};
	digitalData.click.name = name;
	digitalData.click.module = module;
	digitalData.click.position = position;
}

//setting up total slides click variable
function set_click_total_slideshows( current_slide, total_slides ) {
	digitalData.click = digitalData.click||{};
	digitalData.click.slideShowNumber = current_slide + ' of ' + total_slides
}

//setting special variable for condition check inside adobe dtm
function set_digitalData_slideshow_ad() {
	digitalData.slideshowAdEvent = true;
}

//setting variable for image credits
function set_digitalData_image_credits( slide ) {
	var data_image_analytics = jQuery(slide).find('img').attr('data-image-analytics');
	if ( typeof data_image_analytics !== 'undefined' ) {
		digitalData.page.content.image = JSON.parse(data_image_analytics);
	}

}
