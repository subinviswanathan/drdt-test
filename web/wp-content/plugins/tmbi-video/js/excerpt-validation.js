/**
 * Created by Tanaya on 11/14/2017.
 */
jQuery(document).ready(function ($) {
    $('#publish').click(function( e ){
        var excerpt = $('#excerpt');
        var excerptmsg = $('#postexcerpt');
        var value = excerpt.val();
        if (!value) {
            excerpt.css('border', '#dc3232 1px solid');
            ScrollToClassname('postexcerpt');
            excerpt.focus();
            excerptmsg.append($('<p style="color:red;margin: 10px;">Please enter excerpt for video.</p>'));
            setTimeout(function () {
                $('[id^="excerpt"]').next('p').remove();
            }, 6000);
            return (false);
        }
        return (true);
    });
    function ScrollToClassname(id){
        $('html,body').animate({ scrollTop: $("#"+id).offset().top }, 'slow');
        return false;
    }

    $(function() {
		// Remove excerpt description
		$( "#postexcerpt > .inside textarea+p" ).remove();
	});
});