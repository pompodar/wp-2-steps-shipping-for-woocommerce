jQuery(document).ready(function () {
    jQuery("select#delivery_method").change(function () {
        jQuery.ajax({
            type: "POST",
            url: apfajax.ajaxurl,
            data: {
                action: "tss_ajax_form",
                field_one: jQuery(this).val(),
            },
            success: function () {
                jQuery("body").trigger("update_checkout");
            },
            error: function () {
                console.log("error");
            },
        });
    });
});
