//************--Offer Section--*****************//
function saveDates() {
    var start_time = jQuery('#publish_start_time').val();
    var end_time = jQuery('#publish_end_time').val();

    if (start_time == '')
        jQuery('#start_time').attr('value', '');
    if (end_time == '')
        jQuery('#end_time').attr('value', '');
}

function updateAttributes(categoryId, offerId) {
    if (jbdUtils.isProfile != 1) {
        var attributesUrl = jbdUtils.siteRoot + 'administrator/index.php?option=com_jbusinessdirectory&task=offer.getAttributesAjax';
    } else {
        var attributesUrl = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=managecompanyoffer.getAttributesAjax';
    }

    jQuery.ajax({
        type: "POST",
        url: attributesUrl,
        data: {categoryId: categoryId, offerId: offerId},
        dataType: 'json',
        success: function (data) {
            jQuery('#customFieldsContent').html(data);
            jQuery(".chosen-select").chosen({width:"95%", disable_search_threshold: 5,search_contains: true});
        }
    });
}

function uncheckAllCategories() {
    jQuery('#categories option').attr('selected', false);
    jQuery('#main_subcategory').empty();
    jQuery('#main_subcategory option').trigger('liszt:updated');
    jQuery('#main_subcategory option').trigger("chosen:updated");
    jQuery('#categories').trigger('liszt:updated');
    jQuery('#categories').trigger("chosen:updated");

    if(jbdUtils.enable_attribute_category == 1) {
        resetAttributes();
    }
}

function showPriceBase(div,link){
    jQuery("#"+link).css("display", "none");
    jQuery("#"+div).removeAttr("style");
}

function lessPriceBase(div,link){
    jQuery("#"+div).css("display", "none");
    jQuery("#"+link).removeAttr("style");
}

function loadAddress(){
    var companyId = jQuery('select#companyId option:selected').val();
    if (companyId == ''){
        alert(Joomla.JText._("LNG_MISSING_OFFER_COMPANY"));
        return;
    }
    if (jbdUtils.isProfile == 1) {
        var offerUrl = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=managecompanyoffer.getListingAddressAjax';
    } else {
        var offerUrl = jbdUtils.siteRoot + 'administrator/index.php?option=com_jbusinessdirectory&task=offer.getListingAddressAjax';
    }

    jQuery.ajax({
        type: "POST",
        url: offerUrl,
        data: {companyId: companyId},
        dataType: 'json',
        success: function (data) {
            if (data == null) {
                alert(Joomla.JText._("LNG_MISSING_DELETED_COMPANY"));
            } else {
                jQuery('#route').val(data.address);
                jQuery('#street_number').val(data.street_number);
                jQuery('#area_id').val(data.area);
                jQuery('#locality').val(data.city);
                jQuery('#administrative_area_level_2').val(data.province);
                jQuery('#administrative_area_level_1').val(data.county);
                jQuery('#postal_code').val(data.postalCode);
                jQuery('#country').val(data.countryId);
                jQuery('#latitude').val(data.latitude);
                jQuery('#longitude').val(data.longitude);
            }
        }
    });

}
//************--End Offer Section--*****************//