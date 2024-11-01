jQuery(window).ready(function () {
    if (jQuery("#billing__paczkomat_id").length) {
        jQuery("#billing__paczkomat_id").prop('readonly', true);
    }
    jQuery("#geomap-modal").on("click", ".close-modal", function () {
        jQuery("#geomap-modal").removeClass("active");
        jQuery("#geomap-modal .close-modal").toggleClass("active");
    })
    jQuery("body").on("click", ".select-paczkomat-button", function () {
        window.easyPackAsyncInit = function () {
            easyPack.init({
                defaultLocale: 'pl',
                points: {
                    types: ['parcel_locker']
                },
                map: {
                    useGeolocation: true,
                    initialZoom: 13,
                    detailsMinZoom: 15, // minimum zoom after marker click
                    autocompleteZoom: 14,
                    visiblePointsMinZoom: 13,
                    defaultLocation: [52.229807, 21.011595],
                    initialTypes: ['pop', 'parcel_locker'],
                }
            });
            var map = easyPack.mapWidget('easypack-map', function (point) {
                jQuery("#billing__paczkomat_id").val(point.name);
                if (jQuery("#billing__billing_id_przewoznika").length) {
                    jQuery("#billing__billing_id_przewoznika").val("3060");
                    jQuery("#billing__billing_id_przewoznika").prop('readonly', true);
                }
                jQuery("#geomap-modal").removeClass("active");
            });
        };
        if (!jQuery("#geomap-modal .close-modal").hasClass("active")) {
            jQuery("#geomap-modal .close-modal").addClass("active");
        }
        if (jQuery("#geomap-modal").is(":hidden")) {
            jQuery("#geomap-modal").addClass("active");
        }
    })
})

