var jw_instance = (function($) {

	function mergeObject(args) {
		var targetObj = this;
		for (var key in args) {
			if (args.hasOwnProperty(key)) {
				this[key] = args[key];
			}
		}
		return targetObj;
	}

	function initialize_jw_players() {
		var cs_c2 = '6034767';

		if( typeof comscore_vars != 'undefined' ) {
			cs_c2 = comscore_vars.c2;
			var c3;
			 if(tmbi_ad_data.siteId != 'undefined') {
				switch (tmbi_ad_data.siteId) {
					case 'toh':
						c3 = "taste of home";
						break;
					case 'rd':
						c3 = "reader's digest";
						break;
					case 'fhm':
						c3 = "family handyman";
						break;
					case 'cpt':
						c3 = "construction pro tips";
						break;
				}
			}
		}
		window.jw_videos.forEach(function(rd_video) {
			var player_id = rd_video.player_id;
			var video_id = rd_video.video_id;
			var is_playlist = rd_video.is_playlist;
			var sticky = rd_video.sticky;
			var autoplay = rd_video.autoplay;
			var mute = rd_video.mute;
			var comscore = rd_video.comscore;
			var ads = rd_video.ads;
			var native = rd_video.native;

			console.log('JW Player', rd_video)
			video_element = document.getElementById(player_id);
			if ( video_element ) {
				window.jw_players[player_id] = jwplayer( document.getElementById( player_id ) );
				if (autoplay == 'viewable' && sticky == 'false') {
					sticky = 'true';
				}
				var options = {
					playlist: 'https://cdn.jwplayer.com/v2/media/' + video_id,
                    floatOnScroll: sticky,
					autostart: autoplay,
					mute: mute,
				};

				if ( ads == 'true' && ( window.tmbi_ad_data && window.tmbi_ad_data.jwplayer_advertising ) ) {
					options.advertising = window.tmbi_ad_data.jwplayer_advertising;
					options.advertising = mergeObject.call(options.advertising, jw_settings.advertising_settings );
				} else {
					 options.advertising = {
						"preloadAds": "false"
					};
				}
				var jwp = window.jw_players[player_id];
				jwp.setup( options );

				if(c3 && (comscore == 'true')) {

					jwp.on('ready', function () {
						ns_.StreamingAnalytics.JWPlayer(jwp, {
							publisherId: cs_c2,
							labelmapping: "c3=\""+c3+"\",ns_st_ge=\"*null\",ns_st_ia=\"*null\",ns_st_ce=\"*null\",ns_st_ddt=\"*null\",ns_st_tdt=\"*null\""
						});
					});
				}

				function stopPlayers() {

					window.jw_videos.forEach(function(rd_video) {
						if( player_id != rd_video.player_id ) {
							window.jw_players[rd_video.player_id].pause();
							window.jw_players[rd_video.player_id].stop();
						}
					});
				}

				jwp.on('adImpression', function(event) {
					var $container = document.querySelector('#' + player_id + ' .jw-media.jw-reset');
					moatjw.add({
						partnerCode: "trustmediabrandsjwima632472782924",
						player: this,
						adImpressionEvent: event,
						container : $container
					});
					stopPlayers();
				});

				jwp.on('play', stopPlayers);

				jwp.on('adPlay',stopPlayers);

				jwp.on('float', function() {
					if (autoplay == 'viewable' && rd_video.sticky == 'false') {
						if ($('#'+player_id).hasClass('jw-flag-floating')) {
							$('#'+player_id).find('.jw-wrapper').hide();
						} else {
							$('#'+player_id).find('.jw-wrapper').show();
						}
					}
				});

				if ( native == 'true') {
					window.jw_players[player_id].resize(960, 540);
				}
			}
		});
	}
	return {
		jw_player_instance:initialize_jw_players
	}
})(jQuery);


jQuery(document).ready(function($){
	window.jw_videos = window.jw_settings.jw_videos || [];;
	window.jw_players = [];

	if( $('body').hasClass('single-lisitcle') ) {
		$('#collection-wrapper .listicle-page').css({'z-index':2})
		$('#collection-wrapper .blurred-background').css({'z-index':-1})
	}
	if ( ! ( typeof jwplayer === 'undefined' ) ) {
		jw_instance.jw_player_instance();
	}
	//WPDT-7597: Click on x close button//
	jQuery(document).on('click','.jw-float-icon', function(e){
		var target = $(e.currentTarget);
		var player = target.closest('.jwplayer').attr('id');
		window.jw_players[player].stop();
		window.jw_players[player].pause();
	})
})