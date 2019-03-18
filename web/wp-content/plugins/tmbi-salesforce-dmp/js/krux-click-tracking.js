jQuery(document).ready(function($) {
	var $form = $('.gform_wrapper').find('.newsletter-signup');
	if( $form.length ) {
		$form.submit(function () {
			var track = new Image();
			track.src = "http://apiservices.krxd.net/click_tracker/track?kx_event_uid=MPeninnR&clk=https://www.rd.com/newsletter-confirmation/";
		});
	}
	var $cptform = $('.newsletter-wrapper');
	if( $cptform.length ) {
		$(document).on('click','[type="button"]',function (event) {
			Krux('ns:trustedmediabrandsinc','admEvent', 'MQgrJsb8', 'clk', {});
		});
	}

	var $form = $('#nl-signup-form');
	if( $form.length ) {
		$form.submit(function () {
			var track = new Image();
			if( $form.hasClass("fhm-nl-signup-form") ) {
				track.src = "http://apiservices.krxd.net/click_tracker/track?kx_event_uid=MPenPOB-&clk=https://www.familyhandyman.com/NewsletterSignUpPage/UpdatePreferences";
			}
			else if( $form.hasClass("toh-nl-signup-form") ) {
				track.src = "http://apiservices.krxd.net/click_tracker/track?kx_event_uid=MPenrTyB&clk=https://www.tasteofhome.com/newslettersignuppage/updatepreferences";
			}
		});
	}
});