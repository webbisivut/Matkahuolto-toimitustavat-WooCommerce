jQuery( document ).ready( function ($) {
	$('.loading-img-mh-sz').css('display', 'none');
	$('.js-ajax-php-json-sz-mh').click(function(){
		$('.loading-img-mh-sz').css('display', 'inline-block');

		var billing_address_1 = $('#billing_address_1').val();
		var billing_postcode = $('#billing_postcode').val();
		var billing_city = $('#billing_city').val();

		var shipping_address_1 = $('#shipping_address_1').val();
		var shipping_postcode = $('#shipping_postcode').val();
		var shipping_city = $('#shipping_city').val();

		var input_city = $('#mh_noutopiste-sz-mh').val();

		if(shipping_address_1 !== '') {
			var addressToSend = shipping_address_1;
		} else {
			var addressToSend = billing_address_1;
		}

		if(shipping_postcode !== '') {
			var postcodeToSend = shipping_postcode;
		} else {
			var postcodeToSend = billing_postcode;
		}

		var dataToSend = addressToSend + '|' + postcodeToSend + '|' + input_city;
		
		var data = {
			'action': 'noutopisteet_sz_mh',
			'senddata': dataToSend
		};
		
		data = $(this).serialize() + '&' + $.param(data);
		$.ajax({
			type: 'POST',
			url: mh_sz_Ajax.ajaxurl,
			data: data,
			success: function(data) {
				var noutopiste = data;
				var noutopiste = noutopiste.substr(0, noutopiste.length-1); 
				
				$('.mh-return-sz-mh').html(
					'<span class="required-pickup-point-sz-mh">Valitse haluamasi noutopiste:</span>' + noutopiste
				);
				$('.loading-img-mh-sz').css('display', 'none');
			}
		});
		return false;
	});

	$('#mh_noutopiste-sz-mh-mh').keypress(function(e){
        if(e.which == 13){ 
            $('.js-ajax-php-json-sz-mh').click(); 
        }
    });
});