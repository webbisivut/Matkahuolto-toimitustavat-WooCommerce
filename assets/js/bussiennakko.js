jQuery(document).ready(function($) {
	function toggleCustomPtTrigger() {
		$( 'body' ).trigger( 'update_checkout' );
	}
	
	$(document).on('change', "*[id^='payment_method']", toggleCustomPtTrigger);

});