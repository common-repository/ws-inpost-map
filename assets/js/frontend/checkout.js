jQuery(document).ready(function () {
    //jQuery(".select-paczkomat-button").text("Wybierz / Zmień paczkomat");
    jQuery('body').on('wc_fragments_refreshed ', function () {
        if (jQuery("#selected-paczkomat").text()) {
            changeInpostPaczkomatId(jQuery("#selected-paczkomat"));
        }
    });
    if (jQuery("#selected-paczkomat").length) {
        jQuery("#shipping_method button.select-paczkomat-button").clone().appendTo(".woocommerce-billing-fields__field-wrapper #billing__paczkomat_id_field");
        jQuery(".woocommerce-billing-fields__field-wrapper button.select-paczkomat-button").css({
            'margin-top': '10px'
        });
        if (jQuery("#selected-paczkomat").text()) {
            changeInpostPaczkomatId(jQuery("#selected-paczkomat"));
        }
    }
    // jQuery('body').on('DOMSubtreeModified', '#shipping_method li', function () {
    //     changeInpostPaczkomatId(jQuery("#selected-paczkomat"));
    // });


    let checkedCheckbox = "";
    if (jQuery('#shipping_method li').length > 1) {
        jQuery('#shipping_method li').each(function () {
            if (jQuery(this).children("input:checked")) {
                if ((jQuery(this).children("input:checked").val() !== undefined)) {
                    checkedCheckbox = jQuery(this).children("input:checked").val();
                }
            }
        }) 
    }else{
        checkedCheckbox = jQuery('#shipping_method li input').val();
    }

    if (checkedCheckbox == "wsim_inpost_shipping_method") {
        jQuery("#billing__paczkomat_id_field").show();
        jQuery('.select-paczkomat-button').show();
        jQuery("#inpost-info-box").show();
    } else {
        jQuery("#inpost-info-box").hide();
        jQuery("#billing__paczkomat_id_field").hide();
        jQuery('.select-paczkomat-button').hide();
    }

    jQuery(document.body).on('change', 'input.shipping_method', function () {
        if (jQuery(this).val() == "wsim_inpost_shipping_method") {
            jQuery("#billing__paczkomat_id_field").show();
            jQuery('.select-paczkomat-button').show();
            jQuery("#inpost-info-box").show();
            changeInpostPaczkomatId(jQuery("#selected-paczkomat"));
        } else {
            jQuery("#inpost-info-box").hide();
            jQuery("#billing__paczkomat_id_field").hide();
            jQuery('.select-paczkomat-button').hide();

        }
    });
})

function changeInpostPaczkomatId(element) {
    let inpostText = element.html();
    if (inpostText) {
        let inpostId = inpostText.trim().split(" ")[2].split("<br>")[1];
        if (inpostId) {
            jQuery("#billing__paczkomat_id").val(inpostId);
        }
    }
}
