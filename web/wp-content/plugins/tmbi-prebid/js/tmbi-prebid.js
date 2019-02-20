var prebid_timeout = 1000;
if (prebid_conf && prebid_conf.prebidjs_granularity && pbjs) {
	console.log('Ad Stack', 'Prebid', 'Setting price granularity to', prebid_conf.prebidjs_granularity);
	pbjs.setConfig({priceGranularity: prebid_conf.prebidjs_granularity});
}
wp.hooks.addFilter('header_bidders', function (bidders, tmbi_ad_stack) {
	var options = window.prebid_conf;

	var Prebid = function (prebid_service) {
		var self = this;
		var got_bids = false;

		function ad_slot_to_prebid_ad(ad_slot) {
			var prebid_ad = {
				code: ad_slot.slotName,
				mediaTypes: {
					banner: {
						sizes: ad_slot.sizes
					}
				},
				bids: []
			};
			prebid_ad = wp.hooks.applyFilters('ad_slot_to_prebid_ad', prebid_ad, ad_slot, options);
			return prebid_ad;
		}

		self.get_bids = function (slots) {
			return new Promise(function (resolve, reject, notify) {
				var pbto = setTimeout(function () {
					console.log('Ad Stack', 'Prebid get_bids timeout', prebid_timeout);
					resolve();
				}, prebid_timeout);


				pbjs.que = pbjs.que || [];
				pbjs.que.push(function () {
					console.log('Ad Stack', 'Prebid addAdUnits');
					pbjs.addAdUnits(slots.map(ad_slot_to_prebid_ad));
					console.log('Ad Stack', 'Prebid requestBids');
					pbjs.requestBids({
						bidsBackHandler: function (bids) {
							clearTimeout(pbto);
							console.log('Ad Stack', 'Got Prebid bids');
							got_bids = true;
							resolve({
								bidder: 'Prebid.js',
								bids: bids
							});
						},
						timeout: prebid_timeout
					});
				});
			});
		};

		self.set_bids = function () {
			console.log('Ad stack', 'Trying to set bids for Prebid');
			return new Promise(function (resolve, reject, notify) {
				if (got_bids) {
					pbjs.que.push(function () {
						console.log('Ad Stack', 'Prebid bids set');
						pbjs.setTargetingForGPTAsync();
						resolve();
					});
				} else {
					console.log('Ad Stack', 'Prebid bids not set');
					resolve();
				}
			});
		}
	};

	bidders.push(new Prebid(pbjs));

	return bidders;
});

/* @todo: separate this into different files for dev, then combine again for prod */
wp.hooks.addFilter('ad_slot_to_prebid_ad', function add_rubicon_bid_params(prebid_ad, ad_slot, options) {
	if (typeof prebid_conf !== 'undefined' && prebid_conf['rubicon_blocked_flag']) {
		console.warn('Rubicon blocked from url');
		return prebid_ad;
	}
	if (!(options.rubicon_site_id && options.rubicon_account_id && options.rubicon_atf_zone_id && options.rubicon_btf_zone_id)) {
		console.warn('Ad Stack', 'Prebid', 'Rubicon is not configured.');
		return prebid_ad;
	}
	console.log('Ad Stack', 'Prebid', 'Adding Rubicon bid data');
	prebid_ad.bids.push({
		bidder: 'rubicon',
		params: {
			accountId: options.rubicon_account_id,
			siteId: options.rubicon_site_id,
			zoneId: (ad_slot.targeting.tf[0] == 'atf' ? options.rubicon_atf_zone_id : rubicon.rubicon_btf_zone_id)
		}
	});
	return prebid_ad;
});

wp.hooks.addFilter('ad_slot_to_prebid_ad', function add_openx_bid_params(prebid_ad, ad_slot, options) {
	if (typeof prebid_conf !== 'undefined' && prebid_conf['openx_blocked_flag']) {
		console.warn('OpenX blocked from url');
		return prebid_ad;
	}
	if (!(options.openx_del_domain)) {
		console.warn('Ad Stack', 'Prebid', 'OpenX is not configured / is not enabled.');
		return prebid_ad;
	}
	console.log('Ad Stack', 'Prebid', 'OpenX call for', ad_slot);

	/* @todo: Find a sane way to make this matrix configurable */
	function openx_unit_id_mapping(site, platform, ad_unit_path) {
		var openx_ids = [];

		//@todo move this to settings page for prebid
		openx_ids['cpt'] = [];

		openx_ids['cpt']['desktop'] = [];
		openx_ids['cpt']['mobile'] = [];

		openx_ids['cpt']['desktop']['middle'] = 540379403;
		openx_ids['cpt']['desktop']['postarticle'] = 540379404;
		openx_ids['cpt']['desktop']['prearticle'] = 540379405;
		openx_ids['cpt']['desktop']['railmiddle'] = 540379406;
		openx_ids['cpt']['desktop']['railscroll'] = 540379407;
		openx_ids['cpt']['desktop']['railtop'] = 540379408;
		openx_ids['cpt']['desktop']['scroll'] = 540379409;
		openx_ids['cpt']['desktop']['top'] = 540379410;
		openx_ids['cpt']['mobile']['middle'] = 540379411;
		openx_ids['cpt']['mobile']['postarticle'] = 540379412;
		openx_ids['cpt']['mobile']['prearticle'] = 540379413;
		openx_ids['cpt']['mobile']['scroll'] = 540379414;
		openx_ids['cpt']['mobile']['top'] = 540379415;

		if (openx_ids[site] && openx_ids[site][platform] && openx_ids[site][platform][ad_unit_path]) {
			return openx_ids[site][platform][ad_unit_path];
		}
	}

	var site, platform, ad_unit_path, ad_unit_path_parts, site_and_platform;
	ad_unit_path_parts = ad_slot.slotName.split('/');
	site_and_platform = ad_unit_path_parts[2].split('_');
	site = site_and_platform[0];
	platform = site_and_platform[1];
	ad_unit_path = ad_unit_path_parts[ad_unit_path_parts.length - 1];

	var openx_unit_id = openx_unit_id_mapping(site, platform, ad_unit_path);
	if (!(openx_unit_id)) {
		console.warn('Ad Stack', 'Prebid', 'OpenX unit ID not found for ad slot ', ad_slot.slotName);
		return prebid_ad;
	}

	console.log('Ad Stack', 'Prebid', 'Adding OpenX bid data for ad slot', ad_slot.slotName, openx_unit_id);
	prebid_ad.bids.push({
		bidder: 'openx',
		params: {
			delDomain: options.openx_del_domain,
			unit: openx_unit_id
		}
	});
	return prebid_ad;
});

wp.hooks.addFilter('ad_slot_to_prebid_ad', function add_appnexus_bid_params(prebid_ad, ad_slot, options) {
	if (typeof prebid_conf !== 'undefined' && prebid_conf['appnexus_blocked_flag']) {
		console.warn('AppNexus blocked from url');
		return prebid_ad;
	}
	if (!options.appnexus_enabled) {
		console.log('Ad Stack', 'Prebid', 'AppNexus is not enabled');
		return prebid_ad;
	}

	console.log('Ad Stack', 'Prebid', 'AppNexus call for', ad_slot);

	/* @todo: Find a sane way to make this matrix configurable */
	function appnexus_placement_id_mapping(site, platform, ad_unit_path) {
		var appnexus_ids = [];

		//@todo move this to settings page
		appnexus_ids['cpt'] = [];

		appnexus_ids['cpt']['desktop'] = [];
		appnexus_ids['cpt']['mobile'] = [];

		appnexus_ids['cpt']['desktop']['middle'] = 14101431;
		appnexus_ids['cpt']['desktop']['postarticle'] = 14101433;
		appnexus_ids['cpt']['desktop']['prearticle'] = 14101434;
		appnexus_ids['cpt']['desktop']['railmiddle'] = 14101435;
		appnexus_ids['cpt']['desktop']['railscroll'] = 14101436;
		appnexus_ids['cpt']['desktop']['railtop'] = 14101437;
		appnexus_ids['cpt']['desktop']['scroll'] = 14101438;
		appnexus_ids['cpt']['desktop']['top'] = 14101440;
		appnexus_ids['cpt']['mobile']['middle'] = 14101441;
		appnexus_ids['cpt']['mobile']['postarticle'] = 14101443;
		appnexus_ids['cpt']['mobile']['prearticle'] = 14101444;
		appnexus_ids['cpt']['mobile']['scroll'] = 14101446;
		appnexus_ids['cpt']['mobile']['top'] = 14101447;

		if (appnexus_ids[site] && appnexus_ids[site][platform] && appnexus_ids[site][platform][ad_unit_path]) {
			return appnexus_ids[site][platform][ad_unit_path];
		}
	}

	var site, platform, ad_unit_path, ad_unit_path_parts, site_and_platform;
	ad_unit_path_parts = ad_slot.slotName.split('/');
	site_and_platform = ad_unit_path_parts[2].split('_');
	site = site_and_platform[0];
	platform = site_and_platform[1];
	ad_unit_path = ad_unit_path_parts[ad_unit_path_parts.length - 1];

	var appnexus_placement_id = appnexus_placement_id_mapping(site, platform, ad_unit_path);
	if (!(appnexus_placement_id)) {
		console.warn('Ad Stack', 'Prebid', 'AppNexus unit ID not found for ad slot ', ad_slot.slotName);
		return prebid_ad;
	}

	console.log('Ad Stack', 'Prebid', 'Adding AppNexus bid data for ad slot', ad_slot.slotName, appnexus_placement_id);
	prebid_ad.bids.push({
		bidder: 'appnexus',
		params: {
			placementId: appnexus_placement_id
		}
	});
	return prebid_ad;
});
