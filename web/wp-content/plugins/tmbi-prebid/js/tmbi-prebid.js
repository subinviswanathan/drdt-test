var prebid_timeout = 1000;
if ( prebid_conf && prebid_conf.prebidjs_granularity && pbjs ) {
	console.log( 'Ad Stack', 'Prebid', 'Setting price granularity to', prebid_conf.prebidjs_granularity );
	pbjs.setConfig({ priceGranularity: prebid_conf.prebidjs_granularity });
}
wp.hooks.addFilter( 'header_bidders', function( bidders, tmbi_ad_stack ) {
	var options = window.prebid_conf;

	var Prebid = function( prebid_service ) {
		var self = this;
		var got_bids = false;

		function ad_slot_to_prebid_ad( ad_slot ) {
			var prebid_ad = {
				code: ad_slot.slotName,
				mediaTypes: {
					banner: {
						sizes: ad_slot.sizes
					}
				},
				bids: []
			};
			prebid_ad = wp.hooks.applyFilters( 'ad_slot_to_prebid_ad', prebid_ad, ad_slot, options );
			return prebid_ad;
		}

		self.get_bids = function( slots ) {
			return new Promise( function( resolve, reject, notify ) {
				var pbto = setTimeout( function(){
					console.log( 'Ad Stack', 'Prebid get_bids timeout', prebid_timeout );
					resolve();
				}, prebid_timeout );


				pbjs.que = pbjs.que || [];
				pbjs.que.push(function(){
					console.log( 'Ad Stack', 'Prebid addAdUnits' );
					pbjs.addAdUnits( slots.map( ad_slot_to_prebid_ad ) );
					console.log( 'Ad Stack', 'Prebid requestBids' );
					pbjs.requestBids({
						bidsBackHandler: function( bids ) {
							clearTimeout( pbto );
							console.log( 'Ad Stack', 'Got Prebid bids' );
							got_bids = true;
							resolve( {
								bidder: 'Prebid.js',
								bids: bids
							} );
						},
						timeout: prebid_timeout
					});
				});
			} );
		}

		self.set_bids = function() {
			console.log( 'Ad stack', 'Trying to set bids for Prebid' );
			return new Promise( function( resolve, reject, notify ) {
				if ( got_bids ) {
					pbjs.que.push( function(){
						console.log( 'Ad Stack', 'Prebid bids set' );
						pbjs.setTargetingForGPTAsync();
						resolve();
					} );
				} else {
					console.log( 'Ad Stack', 'Prebid bids not set' );
					resolve();
				}
			} );
		}
	};

	bidders.push( new Prebid( pbjs ) );

	return bidders;
} );

/* @todo: separate this into different files for dev, then combine again for prod */
wp.hooks.addFilter( 'ad_slot_to_prebid_ad', function add_rubicon_bid_params( prebid_ad, ad_slot, options ) {
	if ( typeof prebid_conf !== 'undefined' && prebid_conf['rubicon_blocked_flag'] ) {
		console.warn('Rubicon blocked from url');
		return prebid_ad;
	}
	if ( ! ( options.rubicon_site_id && options.rubicon_account_id && options.rubicon_atf_zone_id && options.rubicon_btf_zone_id ) ) {
		console.warn( 'Ad Stack', 'Prebid', 'Rubicon is not configured.' );
		return prebid_ad;
	}
	console.log( 'Ad Stack', 'Prebid', 'Adding Rubicon bid data' );
	prebid_ad.bids.push({
		bidder: 'rubicon',
		params: {
			accountId: options.rubicon_account_id,
			siteId: options.rubicon_site_id,
			zoneId: ( ad_slot.targeting.tf[0] == 'atf' ? options.rubicon_atf_zone_id : rubicon.rubicon_btf_zone_id )
		}
	});
	return prebid_ad;
} );

wp.hooks.addFilter( 'ad_slot_to_prebid_ad', function add_openx_bid_params( prebid_ad, ad_slot, options ) {
	if ( typeof prebid_conf !== 'undefined' && prebid_conf['openx_blocked_flag'] ) {
		console.warn('OpenX blocked from url');
		return prebid_ad;
	}
	if ( ! ( options.openx_del_domain ) ) {
		console.warn( 'Ad Stack', 'Prebid', 'OpenX is not configured / is not enabled.' );
		return prebid_ad;
	}
	console.log( 'Ad Stack', 'Prebid', 'OpenX call for', ad_slot );
	/* @todo: Find a sane way to make this matrix configurable */
	function openx_unit_id_mapping( site, platform, ad_unit_path ) {
		var openx_ids = [];

		openx_ids['toh']=[];
		openx_ids['rdg']=[];
		openx_ids['fhm']=[];
		openx_ids['cpt']=[];

		openx_ids['toh']['desktop']=[];
		openx_ids['toh']['mobile']=[];
		openx_ids['rdg']['desktop']=[];
		openx_ids['rdg']['mobile']=[];
		openx_ids['fhm']['desktop']=[];
		openx_ids['fhm']['mobile']=[];
		openx_ids['cpt']['desktop']=[];
		openx_ids['cpt']['mobile']=[];

		openx_ids['toh']['desktop']['middle']=540379364;
		openx_ids['toh']['desktop']['postarticle']=540379365;
		openx_ids['toh']['desktop']['prearticle']=540379366;
		openx_ids['toh']['desktop']['railmiddle']=540379367;
		openx_ids['toh']['desktop']['railscroll']=540379368;
		openx_ids['toh']['desktop']['railtop']=540379369;
		openx_ids['toh']['desktop']['scroll']=540379370;
		openx_ids['toh']['desktop']['top']=540379371;
		openx_ids['toh']['mobile']['middle']=540379372;
		openx_ids['toh']['mobile']['postarticle']=540379373;
		openx_ids['toh']['mobile']['prearticle']=540379374;
		openx_ids['toh']['mobile']['scroll']=540379375;
		openx_ids['toh']['mobile']['top']=540379376;
		openx_ids['rdg']['desktop']['middle']=540379377;
		openx_ids['rdg']['desktop']['postarticle']=540379378;
		openx_ids['rdg']['desktop']['prearticle']=540379379;
		openx_ids['rdg']['desktop']['railmiddle']=540379380;
		openx_ids['rdg']['desktop']['railscroll']=540379381;
		openx_ids['rdg']['desktop']['railtop']=540379382;
		openx_ids['rdg']['desktop']['scroll']=540379383;
		openx_ids['rdg']['desktop']['top']=540379384;
		openx_ids['rdg']['mobile']['middle']=540379385;
		openx_ids['rdg']['mobile']['postarticle']=540379386;
		openx_ids['rdg']['mobile']['prearticle']=540379387;
		openx_ids['rdg']['mobile']['scroll']=540379388;
		openx_ids['rdg']['mobile']['top']=540379389;
		openx_ids['fhm']['desktop']['middle']=540379390;
		openx_ids['fhm']['desktop']['postarticle']=540379391;
		openx_ids['fhm']['desktop']['prearticle']=540379392;
		openx_ids['fhm']['desktop']['railmiddle']=540379393;
		openx_ids['fhm']['desktop']['railscroll']=540379394;
		openx_ids['fhm']['desktop']['railtop']=540379395;
		openx_ids['fhm']['desktop']['scroll']=540379396;
		openx_ids['fhm']['desktop']['top']=540379397;
		openx_ids['fhm']['mobile']['middle']=540379398;
		openx_ids['fhm']['mobile']['postarticle']=540379399;
		openx_ids['fhm']['mobile']['prearticle']=540379400;
		openx_ids['fhm']['mobile']['scroll']=540379401;
		openx_ids['fhm']['mobile']['top']=540379402;
		openx_ids['cpt']['desktop']['middle']=540379403;
		openx_ids['cpt']['desktop']['postarticle']=540379404;
		openx_ids['cpt']['desktop']['prearticle']=540379405;
		openx_ids['cpt']['desktop']['railmiddle']=540379406;
		openx_ids['cpt']['desktop']['railscroll']=540379407;
		openx_ids['cpt']['desktop']['railtop']=540379408;
		openx_ids['cpt']['desktop']['scroll']=540379409;
		openx_ids['cpt']['desktop']['top']=540379410;
		openx_ids['cpt']['mobile']['middle']=540379411;
		openx_ids['cpt']['mobile']['postarticle']=540379412;
		openx_ids['cpt']['mobile']['prearticle']=540379413;
		openx_ids['cpt']['mobile']['scroll']=540379414;
		openx_ids['cpt']['mobile']['top']=540379415;

		if ( openx_ids[site] && openx_ids[site][platform] && openx_ids[site][platform][ad_unit_path] ) {
			return openx_ids[site][platform][ad_unit_path];
		}
	}

	var site, platform, ad_unit_path, ad_unit_path_parts, site_and_platform;
	ad_unit_path_parts = ad_slot.slotName.split('/');
	site_and_platform = ad_unit_path_parts[2].split('_');
	site = site_and_platform[0];
	platform = site_and_platform[1];
	ad_unit_path = ad_unit_path_parts[ ad_unit_path_parts.length - 1 ];

	var openx_unit_id = openx_unit_id_mapping( site, platform, ad_unit_path );
	if ( ! ( openx_unit_id ) ) {
		console.warn( 'Ad Stack', 'Prebid', 'OpenX unit ID not found for ad slot ', ad_slot.slotName );
		return prebid_ad;
	}

	console.log( 'Ad Stack', 'Prebid', 'Adding OpenX bid data for ad slot', ad_slot.slotName, openx_unit_id );
	prebid_ad.bids.push({
		bidder: 'openx',
		params: {
			delDomain: options.openx_del_domain,
			unit: openx_unit_id
		}
	});
	return prebid_ad;
} );

wp.hooks.addFilter( 'ad_slot_to_prebid_ad', function add_appnexus_bid_params( prebid_ad, ad_slot, options ) {
	if ( typeof prebid_conf !== 'undefined' && prebid_conf['appnexus_blocked_flag'] ) {
		console.warn('AppNexus blocked from url');
		return prebid_ad;
	}
	if ( ! options.appnexus_enabled ) {
		console.log( 'Ad Stack', 'Prebid', 'AppNexus is not enabled' );
		return prebid_ad;
	}

	console.log( 'Ad Stack', 'Prebid', 'AppNexus call for', ad_slot );

	/* @todo: Find a sane way to make this matrix configurable */
	function appnexus_placement_id_mapping( site, platform, ad_unit_path ) {
		var appnexus_ids = [];

		appnexus_ids['toh']=[];
		appnexus_ids['rdg']=[];
		appnexus_ids['fhm']=[];
		appnexus_ids['cpt']=[];

		appnexus_ids['toh']['desktop']=[];
		appnexus_ids['toh']['mobile']=[];
		appnexus_ids['rdg']['desktop']=[];
		appnexus_ids['rdg']['mobile']=[];
		appnexus_ids['fhm']['desktop']=[];
		appnexus_ids['fhm']['mobile']=[];
		appnexus_ids['cpt']['desktop']=[];
		appnexus_ids['cpt']['mobile']=[];

		appnexus_ids['toh']['desktop']['middle']=14101380;
		appnexus_ids['toh']['desktop']['postarticle']=14101381;
		appnexus_ids['toh']['desktop']['prearticle']=14101383;
		appnexus_ids['toh']['desktop']['railmiddle']=14101384;
		appnexus_ids['toh']['desktop']['railscroll']=14101385;
		appnexus_ids['toh']['desktop']['railtop']=14101386;
		appnexus_ids['toh']['desktop']['scroll']=14101388;
		appnexus_ids['toh']['desktop']['top']=14101390;
		appnexus_ids['toh']['mobile']['middle']=14101391;
		appnexus_ids['toh']['mobile']['postarticle']=14101393;
		appnexus_ids['toh']['mobile']['prearticle']=14101394;
		appnexus_ids['toh']['mobile']['scroll']=14101396;
		appnexus_ids['toh']['mobile']['top']=14101398;
		appnexus_ids['rdg']['desktop']['middle']=14101400;
		appnexus_ids['rdg']['desktop']['postarticle']=14101401;
		appnexus_ids['rdg']['desktop']['prearticle']=14101402;
		appnexus_ids['rdg']['desktop']['railmiddle']=14101403;
		appnexus_ids['rdg']['desktop']['railscroll']=14101404;
		appnexus_ids['rdg']['desktop']['railtop']=14101405;
		appnexus_ids['rdg']['desktop']['scroll']=14101406;
		appnexus_ids['rdg']['desktop']['top']=14101407;
		appnexus_ids['rdg']['mobile']['middle']=14101408;
		appnexus_ids['rdg']['mobile']['postarticle']=14101409;
		appnexus_ids['rdg']['mobile']['prearticle']=14101410;
		appnexus_ids['rdg']['mobile']['scroll']=14101411;
		appnexus_ids['rdg']['mobile']['top']=14101412;
		appnexus_ids['fhm']['desktop']['middle']=14101413;
		appnexus_ids['fhm']['desktop']['postarticle']=14101414;
		appnexus_ids['fhm']['desktop']['prearticle']=14101416;
		appnexus_ids['fhm']['desktop']['railmiddle']=14101417;
		appnexus_ids['fhm']['desktop']['railscroll']=14101419;
		appnexus_ids['fhm']['desktop']['railtop']=14101420;
		appnexus_ids['fhm']['desktop']['scroll']=14101422;
		appnexus_ids['fhm']['desktop']['top']=14101423;
		appnexus_ids['fhm']['mobile']['middle']=14101424;
		appnexus_ids['fhm']['mobile']['postarticle']=14101425;
		appnexus_ids['fhm']['mobile']['prearticle']=14101427;
		appnexus_ids['fhm']['mobile']['scroll']=14101428;
		appnexus_ids['fhm']['mobile']['top']=14101430;
		appnexus_ids['cpt']['desktop']['middle']=14101431;
		appnexus_ids['cpt']['desktop']['postarticle']=14101433;
		appnexus_ids['cpt']['desktop']['prearticle']=14101434;
		appnexus_ids['cpt']['desktop']['railmiddle']=14101435;
		appnexus_ids['cpt']['desktop']['railscroll']=14101436;
		appnexus_ids['cpt']['desktop']['railtop']=14101437;
		appnexus_ids['cpt']['desktop']['scroll']=14101438;
		appnexus_ids['cpt']['desktop']['top']=14101440;
		appnexus_ids['cpt']['mobile']['middle']=14101441;
		appnexus_ids['cpt']['mobile']['postarticle']=14101443;
		appnexus_ids['cpt']['mobile']['prearticle']=14101444;
		appnexus_ids['cpt']['mobile']['scroll']=14101446;
		appnexus_ids['cpt']['mobile']['top']=14101447;

		if ( appnexus_ids[site] && appnexus_ids[site][platform] && appnexus_ids[site][platform][ad_unit_path] ) {
			return appnexus_ids[site][platform][ad_unit_path];
		}
	}

	var site, platform, ad_unit_path, ad_unit_path_parts, site_and_platform;
	ad_unit_path_parts = ad_slot.slotName.split('/');
	site_and_platform = ad_unit_path_parts[2].split('_');
	site = site_and_platform[0];
	platform = site_and_platform[1];
	ad_unit_path = ad_unit_path_parts[ ad_unit_path_parts.length - 1 ];

	var appnexus_placement_id = appnexus_placement_id_mapping( site, platform, ad_unit_path );
	if ( ! ( appnexus_placement_id ) ) {
		console.warn( 'Ad Stack', 'Prebid', 'AppNexus unit ID not found for ad slot ', ad_slot.slotName );
		return prebid_ad;
	}

	console.log( 'Ad Stack', 'Prebid', 'Adding AppNexus bid data for ad slot', ad_slot.slotName, appnexus_placement_id );
	prebid_ad.bids.push({
		bidder: 'appnexus',
		params: {
			placementId: appnexus_placement_id
		}
	});
	return prebid_ad;
} );
