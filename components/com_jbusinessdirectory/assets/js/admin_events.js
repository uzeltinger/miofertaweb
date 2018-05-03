//************--Event Section--*****************//
function showSaveDialog(task) {
    jQuery("#task").val(task);
    jQuery.blockUI({message: jQuery('#edit-event-dialog'), css: {width: 'auto', top: '10%', position: 'absolute'}});
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);

}

function repeatNone() {
    jQuery("#repeat-options").hide();
}

function repeatDaily() {
    console.debug("repeat daily");
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").hide();
    jQuery("#monthly-repeat").hide();
}
function repeatWeekly() {
    jQuery("").hide();
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").show();
    jQuery("#monthly-repeat").hide();
}
function repeatMonthly() {
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").hide();
    jQuery("#monthly-repeat").show();
}
function repeatYearly() {
    jQuery("#repeat-options").show();
    jQuery("#week-days-group").hide();
    jQuery("#monthly-repeat").hide();
}

function endsOnOccurances() {
    jQuery("#rend_date").prop('disabled', true);
    jQuery("#occurrences").prop('disabled', false);
}

function endsOnDate() {
    jQuery("#rend_date").prop('disabled', false);
    jQuery("#occurrences").prop('disabled', true);
}

function editEvent() {
    jQuery("#edit_mode").val(1);
    Joomla.submitform(jQuery("#task").val(), document.getElementById('item-form'));
    jQuery.unblockUI();
}

function editAllFollowignEvents() {
    jQuery("#edit_mode").val(2);
    console.debug(jQuery("#task").val());
    Joomla.submitform(jQuery("#task").val(), document.getElementById('item-form'));
    jQuery.unblockUI();
}

function editAllSeriesEvents() {
    jQuery("#edit_mode").val(3);
    Joomla.submitform(jQuery("#task").val(), document.getElementById('item-form'));
    jQuery.unblockUI();
}

function checkAll() {
    jQuery('#associated-listings option').attr('selected', true);
    jQuery('#associated-listings').trigger('liszt:updated');
}

function uncheckAll() {
    jQuery('#associated-listings option').attr('selected', false);
    jQuery('#associated-listings').trigger('liszt:updated');
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

function saveDates() {
    var start_time = jQuery('#start_time').val();
    var end_time = jQuery('#end_time').val();
    var doors_open_time = jQuery('#doors_open_time').val();
    var booking_open_time = jQuery('#booking_open_time').val();
    var booking_close_time = jQuery('#booking_close_time').val();

    if (start_time == '')
        jQuery('#start_time').attr('value', '');
    if (end_time == '')
        jQuery('#end_time').attr('value', '');
    if (doors_open_time == '')
        jQuery('#doors_open_time').attr('value', '');
    if (booking_open_time == '')
        jQuery('#booking_open_time').attr('value', '');
    if (booking_close_time == '')
        jQuery('#booking_close_time').attr('value', '');
}

function updateAttributes(categoryId, eventId) {
    if (jbdUtils.isProfile != 1) {
        var attributesUrl = jbdUtils.siteRoot + 'administrator/index.php?option=com_jbusinessdirectory&task=event.getAttributesAjax';
    } else {
        var attributesUrl = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=managecompanyevent.getAttributesAjax';
    }

    jQuery.ajax({
        type: "POST",
        url: attributesUrl,
        data: {categoryId: categoryId, eventId: eventId},
        dataType: 'json',
        success: function (data) {
            jQuery('#customFieldsContent').html(data);
            jQuery(".chosen-select").chosen({width:"95%", disable_search_threshold: 5,search_contains: true});
        }
    });
}


function loadAddress(){
    var companyId = jQuery('select#company_id option:selected').val();
    if (companyId == ''){
        alert(Joomla.JText._("LNG_MISSING_EVENT_COMPANY"));
        return;
    }
    if (jbdUtils.isProfile == 1) {
        var eventUrl = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=managecompanyevent.getListingAddressAjax';
    } else {
        var eventUrl = jbdUtils.siteRoot + 'administrator/index.php?option=com_jbusinessdirectory&task=event.getListingAddressAjax';
    }

    jQuery.ajax({
        type: "POST",
        url: eventUrl,
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
//************--End Event Section--*****************//