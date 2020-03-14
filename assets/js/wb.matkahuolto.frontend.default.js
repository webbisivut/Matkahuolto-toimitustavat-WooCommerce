jQuery(document).ready(function($) {
    // Create reusable function to show or hide .lahipaketti-wrap
    function toggleCustomBox_mh_sz() {
        // Get id of selected input
        var selectedMethod = $('input:checked', '#shipping_method').attr('id');
        var hiddenOrNot = $('.shipping_method').attr('type');
        var getVal = $('.shipping_method').val();

        var target_method = $("*[id^='shipping_method_0_sz_wb_mh_lahipaketti_shipping_method']").attr('id');
        var target_getVal = $("*[value^='sz_wb_mh_lahipaketti_shipping_method']").attr('value');

        // Toggle .lahipaketti-wrap depending on the selected input's id
        if (selectedMethod === target_method && typeof target_method != 'undefined'|| hiddenOrNot == 'hidden' && getVal == target_getVal && typeof getVal != 'undefined') {
            $('.lahipaketti-wrap-sz-mh').show();
            $('#mh_noutopiste-sz-mh').val('');
        } else {
            $('.lahipaketti-wrap-sz-mh').hide();
            $('#mh_noutopiste-sz-mh').val('Ei käytössä');
        };
    };

    // Fire our function on page load
    $(document).ready(toggleCustomBox_mh_sz);
    $(document).ajaxStop(toggleCustomBox_mh_sz);

    // Fire our function on radio button change
    $(document).on('change', '#shipping_method input:radio', toggleCustomBox_mh_sz);

    $(document).on('change', '#billing_country', function() {
        $( document ).ajaxComplete(function() {
          toggleCustomBox_mh_sz();
        });
    });

	setTimeout( toggleCustomBox_mh_sz, 2000 );
});
