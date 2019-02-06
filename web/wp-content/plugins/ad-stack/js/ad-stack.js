/* global tmbi_ad_data postscribe ads_global_targeting*/
/*eslint no-console: "error"*/
var googletag = googletag || {};
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

	var breakpoints = {
		large_screen: 1024,
		desktop: 769,
		tablet: 481,
		mobile: 0
	};

	/*get the current browser width*/
	function getCurrentBreakPoint() {
		var viewport_width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
		if (viewport_width > breakpoints.large_screen) {
			return 'large_screen';
		} else if (viewport_width < breakpoints.tablet) {
			return 'mobile';
		} else if (viewport_width > breakpoints.desktop) {
			return 'desktop';
		} else {
			return 'tablet';
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
			googletag.pubads().enableSingleRequest();
			googletag.enableServices();
		});
	};

	// Render a single ad.
	self.fetch_and_render = function( ad_id, options ){
		googletag.cmd.push(function () {
			var device = getCurrentBreakPoint(),
				responsiveSize = options['adResponsiveSizes'] && JSON.parse(options['adResponsiveSizes'] || '{}');
			if (responsiveSize && responsiveSize[device]) {
				var slot = '/' + ads_global_targeting['property'] + '/' + ads_global_targeting['siteId'] + '_' + (device === 'mobile' ? 'mobile' : 'desktop') + options.adSlotName;
				var the_ad = googletag.defineSlot(slot, responsiveSize[device], ad_id);
				var targeting = JSON.parse(options.adTargeting || '{}');
				adSetTargeting(targeting, the_ad);
				the_ad.addService(googletag.pubads());
				googletag.display(ad_id);
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
var ad_stack = new TMBI_Ad_Stack();

window.addEventListener('load', function() {
	postscribe('#gpt-postcribe','<script src="https://www.googletagservices.com/tag/js/gpt.js"></script>',function () {
		//global targeting params
		window.ads_global_targeting = typeof tmbi_ad_data !== 'undefined' &&  tmbi_ad_data || {};
		ad_stack.init();
		ad_stack.fetch_and_render_all();
	});
});