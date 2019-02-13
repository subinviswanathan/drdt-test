/* global tmbi_ad_data postscribe ads_global_targeting*/
/*eslint no-console: "error"*/
var googletag = googletag || {}, device;
googletag.cmd = googletag.cmd || [];
function TMBI_Ad_Stack( ) {
	var self = this;

	// Add GPT code.
	(function() {
		var gads = document.createElement('script'),
			postScribe = document.createElement('script');
		gads.async = true;
		gads.type = 'text/javascript';
		var useSSL = 'https:' == document.location.protocol;
		gads.src = (useSSL ? 'https:' : 'http:') + '//www.googletagservices.com/tag/js/gpt.js';
		postScribe.src = (useSSL ? 'https:' : 'http:') + '//cdnjs.cloudflare.com/ajax/libs/postscribe/2.0.8/postscribe.min.js';
		var node =document.getElementsByTagName('script')[0];
		//node.parentNode.insertBefore(gads, node);
		window.nodePostScribe = node;
		node.parentNode.insertBefore(postScribe, node);
		device = getCurrentBreakPoint();
	})();

	/**
	 * Converts a string of sizes to a DFP-suitable array.
	 *
	 * @param string sizes_str The string containing sizes. E.g.: 728x90,300x600.
	 * @return array           The list of sizes in array format. E.g.: [[728,90],[300,600]]
	 */
	/*function sizes_str_to_array( sizes_str ) {
		// sizes_str.split(',').map((size) => size.split('x').map(parseInt)) returns NULL for height
		return sizes_str.split(',').map(function(size){
			var sizeParts = size.split('x');
			return [parseInt(sizeParts[0]),parseInt(sizeParts[1])]
		});
	} */

	/**************
	 * common function to loop through object and add settargeting parameters
	 * */
	function adSetTargeting(values, _this) {
		for( var key in values ) {
			if ( values.hasOwnProperty(key) ) {
				_this.setTargeting( key, values[key] );
			}
		}
	}




	// Initialize the ad stack.
	self.init = function(){
		googletag.cmd.push(function() {
			// Lazy load when in view
			// @todo: check with Danny if we want to keep using custom implementation
			googletag.pubads().enableLazyLoad({
				fetchMarginPercent: 500,  // Fetch slots within 5 viewports.
				renderMarginPercent: 200,  // Render slots within 2 viewports.
				mobileScaling: 2.0  // Double the above values on mobile.
			});
			ads_global_targeting['refer'] = document.referrer;
			ads_global_targeting['random'] = Math.ceil(Math.random() * 100);
			var the_ad = googletag.pubads();
			adSetTargeting(ads_global_targeting, the_ad);
			// SRA
			googletag.pubads().disableInitialLoad();
			googletag.pubads().enableAsyncRendering();
			googletag.pubads().enableSingleRequest();
			googletag.enableServices();
		});
	};

	// Render a single ad.
	self.fetch_and_render = function( ad_id, options ){
		googletag.cmd.push(function () {
			var responsiveSize = options['adResponsiveSizes'] && JSON.parse(options['adResponsiveSizes'] || '{}');
			if (responsiveSize && responsiveSize[device]) {
				var slot = '/' + ads_global_targeting['property'] + '/' + ads_global_targeting['siteId'] + '_' + (device === 'mobile' ? 'mobile' : 'desktop') + options.adSlotName;
				//var sizeMap = get_size_mapping_from_sizes_array(responsiveSize);
				var the_ad = googletag.defineSlot(slot, responsiveSize[device], ad_id);
				var targeting = JSON.parse(options.adTargeting || '{}');
				adSetTargeting(targeting, the_ad);
				the_ad.addService(googletag.pubads());
				headerBidding(the_ad, {slotName : slot, size: responsiveSize[device]});
				//refresh_ads(the_ad);
			}
		});
	};

	// @todo Render a single batch.
	self.fetch_and_render_batch = function( ){};

	// Fetch and render all ads on page.
	self.fetch_and_render_all = function() {
		var ads = document.getElementsByClassName('ad');
		for (var i = 0; i < ads.length; i++) {
			self.fetch_and_render( ads[i].id, ads[i].dataset );
		}
	};
}

// dont load ad initially and refresh if we get any bids from other partners
function refresh_ads(ad_elements) {

	// make sure pubService is fully loaded before calling `refresh`
	if (window.googletag && googletag.pubadsReady) {
		console.log('Ad Stack', 'Fetching and rendering the ad(s)', ad_elements);
		googletag.pubads().refresh([ad_elements], {changeCorrelator: false});
	} else {
		setTimeout(function () {
			refresh_ads(ad_elements);
		},2000);
		console.log('Ad Stack', 'Pubads service not ready.So refresh the slot after 2 secs ', ad_elements);
	}
}

/**
 * Header Bidding
 *
 * We currently support A9 and Prebid.js running in parallel.
 * Each of them has a thin wrapper to unify interfaces, with get_bids returning a Promise
 * (that should not be rejected) and set_bids immediately applying their bids to DFP.
 * See tmbi-amazon-a9.js and tmbi-prebid.js
 */
function headerBidding(ad_elements, arr_ads) {
	var bidders = [];
	if ( typeof Promise !== "undefined" && typeof wp !== 'undefined') {
		bidders = wp.hooks.applyFilters( 'header_bidders', [], window.ads_global_targeting );
	} else {
		console.warn( 'Ad Stack', 'Browser does not support Promises! Can\'t run header bidders.' );
	}
	if ( bidders && bidders.length ) {
		console.log( 'Ad Stack', 'Calling header bidders for lazy-loaded unit with slot_data', arr_ads );
		Promise.all( bidders.map( function( bidder ) { return bidder.get_bids( arr_ads ); } ) ).then( function ( responses ) {
			console.log( 'Ad Stack', 'Got response from header bidders', responses );
			Promise.all( bidders.map( function( bidder ) { return bidder.set_bids( ad_elements ); } ) ).then( function()  {
				refresh_ads( ad_elements );
				bidders = null;
			}).catch( function( errors ) {
				console.error( 'Ad Stack', 'Error applying header bidders bids', errors )
			});
		} ).catch( function( errors ) {
			console.error( 'Ad Stack', 'Error getting headder bidders bids', errors )
		} );
	} else {
		console.log( 'Ad Stack', 'No header bidders available. Calling DFP directly' );
		refresh_ads( ad_elements );
	}
}

/*get the current browser width making more generic*/
function getCurrentBreakPoint() {
	var breakpoints = typeof tmbi_ad_data !== 'undefined' && tmbi_ad_data && tmbi_ad_data['breakpoint'],
		breakpoint_array = [], // This should hold all the breakpoint values
		viewport_width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
	for (var item in breakpoints) {
		if (breakpoints.hasOwnProperty(item)) {
			breakpoint_array.push([item, breakpoints[item]]);
		}
	}
	breakpoint_array.push(['current_breakpoint', viewport_width]);// adding our viewport to the array
	//sort the array based on width
	breakpoint_array.sort(function (a, b) {
		return a[1] - b[1];
	});
	var device_type = [];
	breakpoint_array.forEach(function (value, index) {
		if (value[0] === 'current_breakpoint') {
			device_type = breakpoint_array[index - 1];
		}
	});
	return device_type[0];
}

var ad_stack = new TMBI_Ad_Stack();

// event callback on load
window.addEventListener('load', function() {
	postscribe('#gpt-postcribe','<script src="https://www.googletagservices.com/tag/js/gpt.js"></script>',function () {
		//global targeting params
		window.ads_global_targeting = typeof tmbi_ad_data !== 'undefined' && tmbi_ad_data && (tmbi_ad_data.global_targeting || {});
		ad_stack.init();
		ad_stack.fetch_and_render_all();
	});
});