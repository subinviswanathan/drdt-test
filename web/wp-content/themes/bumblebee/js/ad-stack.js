var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
function TMBI_Ad_Stack( options ) {
	var self = this;
	(function() {
		var gads = document.createElement("script");
		gads.async = true;
		gads.type = "text/javascript";
		var useSSL = "https:" == document.location.protocol;
		gads.src = (useSSL ? "https:" : "http:") + "//www.googletagservices.com/tag/js/gpt.js";
		var node =document.getElementsByTagName("script")[0];
		node.parentNode.insertBefore(gads, node);
	})();

	// Initialize the ad stack.
	this.init = function(){
		googletag.cmd.push(function() {
			// Lazy load when in view
			// @todo: check with Danny if we want to keep using custom implementation
			googletag.pubads().enableLazyLoad({
				fetchMarginPercent: 500,  // Fetch slots within 5 viewports.
				renderMarginPercent: 200,  // Render slots within 2 viewports.
				mobileScaling: 2.0  // Double the above values on mobile.
			});
	    	googletag.pubads().enableSingleRequest();
			googletag.enableServices();
		})
	};

	// Render a single ad.
	this.fetch_and_render = function( ad_id, options ){
		googletag.cmd.push(function(){
			sizes = options.adSizes.split(',').map(function(size){
				sizeParts = size.split('x');
				return [parseInt(sizeParts[0]),parseInt(sizeParts[1])]
			});
			var the_ad = googletag.defineSlot( options.adSlotName, sizes, ad_id);

			var targeting = JSON.parse( options.adTargeting || "{}" );
			for( var key in targeting ) {
				if ( targeting.hasOwnProperty(key) ) {
					the_ad.setTargeting( key, targeting[key] );
				}
			}

			the_ad.addService(googletag.pubads());
			googletag.display( ad_id );
		});
	};

	// Render a single ad.
	this.fetch_and_render_batch = function( batch ){};

	// Fetch and render all
	this.fetch_and_render_all = function() {
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