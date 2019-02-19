jQuery( document ).ready( function ( $ ) {
	$( ".homepage_slots" ).change( function() {
		if($(this). prop("checked") != true) {
			$(this).prop( 'checked', false );
		} else{
			$( ".homepage_slots" ).prop( 'checked', false );
			$( this ).prop( 'checked', true );
		}
	});
});