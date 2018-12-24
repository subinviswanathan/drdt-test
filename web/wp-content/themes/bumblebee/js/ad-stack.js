var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
function TMBI_Ad_Stack( options ) {
	var self = this;

	// Add GPT code.
	(function() {
		var gads = document.createElement("script");
		gads.async = true;
		gads.type = "text/javascript";
		var useSSL = "https:" == document.location.protocol;
		gads.src = (useSSL ? "https:" : "http:") + "//www.googletagservices.com/tag/js/gpt.js";
		var node =document.getElementsByTagName("script")[0];
		node.parentNode.insertBefore(gads, node);
	})();

	/**
	 * Converts a string of sizes to a DFP-suitable array.
	 *
	 * @param string sizes_str The string containing sizes. E.g.: 728x90,300x600.
	 * @return array           The list of sizes in array format. E.g.: [[728,90],[300,600]]
	 */
	function sizes_str_to_array( sizes_str ) {
		// sizes_str.split(',').map((size) => size.split('x').map(parseInt)) returns NULL for height
		return sizes_str.split(',').map(function(size){
			sizeParts = size.split('x');
			return [parseInt(sizeParts[0]),parseInt(sizeParts[1])]
		});
	}

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

	/*get the current browser width*/
	function getCurrentBreakPoint() {
		var viewport_width = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
		if (viewport_width > 1024) {
			return 'large_screen';
		} else if (viewport_width < 481) {
			return 'mobile';
		} else if (viewport_width > 768) {
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
			//global targeting params
			var global_targeting = typeof tmbi_ad_data !== 'undefined' &&  tmbi_ad_data || {};
			var the_ad = googletag.pubads();
			adSetTargeting(global_targeting, the_ad);
			// SRA
	    	googletag.pubads().enableSingleRequest();
			googletag.enableServices();
		})
	};

	// Render a single ad.
	self.fetch_and_render = function( ad_id, options ){
		googletag.cmd.push(function(){
			var sizes  = sizes_str_to_array( options.adSizes );
			var the_ad = googletag.defineSlot( options.adSlotName, sizes, ad_id );

			// @todo: add global targeting parameters
			var targeting = JSON.parse( options.adTargeting || "{}" );
			adSetTargeting(targeting, the_ad);
			the_ad.addService(googletag.pubads());
			googletag.display( ad_id );
		});
	};

	// Render a single batch.
	self.fetch_and_render_batch = function( batch ){};

	// Fetch and render all ads on page.
	self.fetch_and_render_all = function() {
		var ads = document.getElementsByClassName("ad");
		for (var i = 0; i < ads.length; i++) {
		    self.fetch_and_render( ads[i].id, ads[i].dataset );
		}
	};
}

var ad_stack = new TMBI_Ad_Stack();
document.addEventListener("DOMContentLoaded", function(event) {
	ad_stack.init();
	ad_stack.fetch_and_render_all();
});