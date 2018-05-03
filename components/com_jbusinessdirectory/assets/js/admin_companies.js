var editUrl;
var editUrlFront;
var deleteUrl;
var deleteUrlFront;

function setLocationUrl(eurl, eurlf, durl, durlf) {
    editUrl = eurl;
    editUrlFront = eurlf;
    deleteUrl = durl;
    deleteUrlFront = durlf;
}

//************--Company Section--*****************//
function uncheckAll() {
    jQuery('#related-listings option').attr('selected', false);
    jQuery('#related-listings').trigger('liszt:updated');
}

function extendPeriod() {
    if (jbdUtils.isProfile != 0) {
        jQuery("#task").val("managecompany.extendPeriod");
    } else {
        jQuery("#task").val("company.extendPeriod");
    }
    jQuery("#item-form").submit();
}

function checkAllActivityCities() {
    uncheckAllActivityCities();
    jQuery(".cities_ids-select option").each(function () {
        if (jQuery(this).val() != "") {
            activityCitiesList.add(jQuery(this));
        }
    });
    jQuery("#activity_cities option").each(function () {
        jQuery(this).attr("selected", "selected");
    });
}

function uncheckAllCategories() {
    jQuery('#selectedSubcategories option').attr('selected', false);
    jQuery('#mainSubcategory').empty();
    jQuery('#mainSubcategory option').trigger('liszt:updated');
    jQuery('#mainSubcategory option').trigger("chosen:updated");
    jQuery('#selectedSubcategories').trigger('liszt:updated');
    jQuery('#selectedSubcategories').trigger("chosen:updated");

    if(jbdUtils.enable_attribute_category == 1) {
        resetAttributes();
    }
}

function uncheckAllTypes() {
    jQuery('#companyTypes option').attr('selected', false);
    jQuery('#companyTypes').trigger('liszt:updated');
    jQuery('#companyTypes').trigger("chosen:updated");
}

function uncheckAllMemberships() {
    jQuery('#selectedMemberships option').attr('selected', false);
    jQuery('#selectedMemberships').trigger('liszt:updated');
}

function uncheckAllActivityCities() {
    jQuery("#activity_cities option").each(function () {
        jQuery(this).removeAttr("selected");
    });
    activityCitiesList.remove();
}

function editLocation(locationId,identifier) {
    var baseUrl = editUrl;
    if (jbdUtils.isProfile == 1) {
        baseUrl = editUrlFront;
    }
    baseUrl = baseUrl + "&locationId=" + locationId+ "&identifier=" + identifier;
    jQuery("#location-frame").attr("src", baseUrl);
    jQuery.blockUI({
        message: jQuery('#location-dialog'),
        css: {width: 'auto', top: '10%', left: "0", position: "absolute", cursor: 'default'}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#locationD").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}

function deleteLocation(locationId) {
    if (!confirm(Joomla.JText._("LNG_DELETE_LOCATION_CONF"))) {
        return;
    }
    var baseUrl = deleteUrl;
    if (jbdUtils.isProfile == 1) {
        baseUrl = deleteUrlFront;
    }

    var postData = "&locationId=" + locationId;
    jQuery.post(baseUrl, postData, processDeleteLocationResult);
}

function processDeleteLocationResult(response) {
    var xml = response;
    jQuery(xml).find('answer').each(function () {
        if (jQuery(this).attr('error') == '1')
            jQuery("#location-box-" + jQuery(this).attr('locationId')).remove();
        else {
            jQuery.blockUI({
                message: '<h3>' + Joomla.JText._("LNG_LOCATION_DELETE_FAILED") + '</h3>'
            });
            setTimeout(jQuery.unblockUI, 2000);
        }
    });
}

function updateLocation(id, name, streetNumber, address, city, county, country) {
    if (jQuery("#location-0").length > 0) {
        jQuery("#location-0").html(name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + ", " + country);
        jQuery("#location-0").attr("id", "#location-" + id);
    } else if (jQuery("#location-" + id).length > 0) {
        jQuery("#location-" + id).html(name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + ", " + country);
    }
    else {
        var locationContainer = '<div id="location-box-' + id + '" class="detail_box">';
        locationContainer += '<div id="location-' + id + '">' + name + " - " + streetNumber + ", " + address + ", " + city + ", " + county + " ," + country + '</div>';
        locationContainer += '</div>';
        jQuery("#company-locations").append(locationContainer);
    }
}

function closeLocationDialog() {
    jQuery.unblockUI();
}

function showTerms() {
    jQuery.blockUI({
        message: jQuery('#conditions'),
        css: {width: 'auto', top: '10%', left: "0", position: "absolute", cursor: 'default'},
        overlayCSS: {backgroundColor: '#000', opacity: 0.7, cursor: 'pointer'}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#termsc").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}




var currentTab = 1;
var currentTabIndex = 0;
var maxTabs = 6;
var tabMapInitialized = 0;

function setMaxTabs(max_tabs) {
    maxTabs = max_tabs;
}

function openTab(tab){
	if (jbdUtils.isMultilingual) {
    	jQuery(".tab-"+jbdUtils.defaultLang).each(function(){
    		jQuery(this).click();
    	});
    }

	jQuery("#item-form").validationEngine('detach');
	if (jbdUtils.validateRichTextEditors) {
		validateRichTextEditors();
	}
	
	validateMultiSelects();	
	var validationResult = jQuery("#item-form").validationEngine('validate');
	
	if(!validationResult){
		return;
	}
	
	showTab(tab);
}

function showTab(tab) {
	jQuery(".edit-tab").each(function () {
        jQuery(this).hide();
    });
    
    jQuery(".process-step").each(function () {
        jQuery(this).hide();
        jQuery(this).removeClass("active");

    });
    
    jQuery(".process-tab").each(function () {
        jQuery(this).removeClass("active");
    });
    
    if (currentTabIndex == 0) {
        jQuery("#prev-btn").hide();
    }
    else {
        jQuery("#prev-btn").show();
    }
    
    if ((currentTabIndex + 1) == maxTabs) {
        jQuery("#next-btn").hide();
        jQuery("#save-btn").show();
        jQuery("#term_conditions").show();
    }
    else {
        jQuery("#next-btn").show();
        jQuery("#save-btn").hide();
        jQuery("#term_conditions").hide();
    }
    
    jQuery("#edit-tab" + tab).show();
    jQuery("#step" + tab).show();
    
    if(tab!=1){
	    var scrollTopOfset = jQuery("#tab" + tab).offset().top-150;
	    jQuery('html,body').animate({scrollTop: scrollTopOfset},'slow');
	}else{
		 jQuery(window).scrollTop(10);
	}

    jQuery("#step" + tab).addClass("active");
    jQuery("#tab" + tab).addClass("active");
    jQuery("#active-step-number").html(tab);
    if (tab == 3 && tabMapInitialized == 0) {
        initializeMap();
        tabMapInitialized = 1;
    }
}

function nextTab() {
	if (jbdUtils.isMultilingual) {
    	jQuery(".tab-"+jbdUtils.defaultLang).each(function(){
    		jQuery(this).click();
    	});
    }

	if (jbdUtils.validateRichTextEditors) {
		validateRichTextEditors();
	}
	
    var validationResult = jQuery("#item-form").validationEngine('validate');
    if (validationResult) {
        if (currentTabIndex < presentTabs.length-1){
        	currentTabIndex ++;
        	currentTab = presentTabs[currentTabIndex];
        }
        showTab(currentTab);
    }
}

function previousTab() {
    if (currentTabIndex > 0){
    	currentTabIndex--;
    	currentTab = presentTabs[currentTabIndex];
    }
    
    showTab(currentTab);
}

function addNewContact(index) {
    var newIndex = parseInt(index) + 1;
    jQuery('#contact-form-box1').clone().prop('id', 'contact-form-box' + newIndex).appendTo('#contact_details');
    jQuery("#contact-form-box" + newIndex).find('h3').text(Joomla.JText._('LNG_CONTACT') + ' ' + newIndex);
    jQuery('#contact-form-box' + newIndex + ' input').each(function () {
        jQuery(this).val('');
    });

    jQuery("#contact-form-box" + newIndex + " .remove-contact").attr('href', 'javascript:removeContact(\'' + newIndex + '\')').show();
    jQuery('#add_contact').attr('onclick', 'addNewContact(\'' + newIndex + '\')');

}

function removeContact(index) {
    if (index < 2)
        return;

    index = parseInt(index);
    jQuery('#contact-form-box' + index).remove();
}

function addNewTestimonial(index) {
    var newIndex = parseInt(index) + 1;
    jQuery('#testimonial-form-box1').clone().prop('id', 'testimonial-form-box' + newIndex).appendTo('#testimonial_details');
    jQuery("#testimonial-form-box" + newIndex).find('h3').text(Joomla.JText._('LNG_TESTIMONIAL') + ' ' + newIndex);
    jQuery('#testimonial-form-box' + newIndex + ' input').each(function () {
        jQuery(this).val('');
    });

    jQuery('#testimonial-form-box' + newIndex + ' textarea').each(function () {
        jQuery(this).html('');
    });
    jQuery('#testimonial-form-box' + newIndex + ' textarea').val('');

    jQuery("#testimonial-form-box" + newIndex + " .remove-testimonial").attr('href', 'javascript:removeTestimonial(\'' + newIndex + '\')').show();
    jQuery('#add_testimonial').attr('onclick', 'addNewTestimonial(\'' + newIndex + '\')');

}

function removeTestimonial(index) {
    if (index < 2)
        return;

    index = parseInt(index);
    jQuery('#testimonial-form-box' + index).remove();
}

function addNewService(index,ServiceFolder,ServiceFolderPath) {
    var newIndex = parseInt(index) + 1;
    jQuery('#service-form-box1').clone().prop('id', 'service-form-box' + newIndex).appendTo('#service_details');
    jQuery("#service-form-box" + newIndex).find('h3').text(Joomla.JText._('LNG_SERVICE') + ' ' + newIndex);
    jQuery('#service-form-box' + newIndex + ' input').each(function () {
        jQuery(this).val('');
    });
    jQuery('#service-form-box' + newIndex + ' textarea').each(function () {
        jQuery(this).html('');
    });
    jQuery('#service-form-box' + newIndex + ' textarea').val('');
    jQuery('#service-form-box' + newIndex + ' .input-imageLocation').prop('id', 'service-imageLocation' + newIndex);
    jQuery('#service-form-box' + newIndex + ' .input-imageLocationSize').prop('id', 'service-imageUploader' + newIndex);

    jQuery('#service-form-box' + newIndex + ' .services').prop('id', 'service-picture-preview' + newIndex);
    jQuery('#service-picture-preview' + newIndex).html('');

    jQuery('#service-form-box' + newIndex + ' #imageSelection').find('a').prop('href', 'javascript:removeServiceLogo('+ newIndex +')');

    jQuery("#service-form-box" + newIndex + " .remove-service").attr('href', 'javascript:removeService(\'' + newIndex + '\')').show();
    jQuery('#add_service').attr('onclick', 'addNewService(\'' + newIndex + '\',\'' + ServiceFolder + '\',\'' + ServiceFolderPath + '\')');

    imageUploader(ServiceFolder, ServiceFolderPath, 'service-',newIndex);

}

function removeService(index) {
    if (index < 2)
        return;

    index = parseInt(index);
    jQuery('#service-form-box' + index).remove();
}

function removeServiceLogo(id) {
    jQuery('#service-imageLocation'+id).val("");
    jQuery('#service-picture-preview'+id).html("");
    jQuery('#service-imageUploader'+id).val("");
}

function disableWorkPeriod(day, mandatory, multiple) {
    var status;
    var checked = jQuery('#work_status_check_' + day).is(":checked");
    var rows = jQuery('#break_period_' + day).find('td');
    var button;

    if (checked) {
        status = 1;
        jQuery('#work_start_hour_' + day).prop('readonly', false);
        if (mandatory) {
            jQuery('#work_start_hour_' + day).addClass('validate[required]');
            jQuery('#work_end_hour_' + day).addClass('validate[required]');
        }
        jQuery('#work_end_hour_' + day).prop('readonly', false);
        button = jQuery('#break_period_' + day).find('a').removeAttr('disabled');
        button.text(Joomla.JText._('LNG_ADD_BREAK'));
        button.attr('onclick', 'addBreak(' + day + ', ' + multiple + ')');
    }
    else {
        status = 0;
        jQuery('#work_start_hour_' + day).prop('readonly', true);
        if (mandatory) {
            jQuery('#work_start_hour_' + day).removeClass('validate[required]');
            jQuery('#work_end_hour_' + day).removeClass('validate[required]');
        }
        jQuery('#work_end_hour_' + day).prop('readonly', true);
        button = jQuery('#break_period_' + day).find('a');
        button.attr('disabled', true);
        button.text(Joomla.JText._('LNG_CLOSED'));
        button.attr('onclick', '');
        jQuery('#break_hours_day_' + day).empty();
        jQuery('#break_periods_count_' + day).val(0);
    }

    jQuery('#work_status_' + day).val(status);
}

function addBreak(day, multiple) {
    var id = parseInt(jQuery('#break_periods_count_' + day).val());
    var newId = id + 1;

    var html = '';
    html += '<div class="row-fluid" id="break_hour_period_' + day + '_' + newId + '">';
    html += '<div class="span4"><input type="text" name="break_start_hour[]" class="timepicker" value="01:00 PM" /></div>';
    html += '<div class="span4"><input type="text" name="break_end_hour[]" class="timepicker" value="02:00 PM" /></div>';
    if (multiple)
        html += '<div class="span2"><a href="javascript:void(0)" class="btn btn-xs btn-danger btn-panel" onclick="deleteBreak(' + day + ', ' + newId + ')">';
    else
        html += '<div class="span2"><a href="javascript:void(0)" class="btn btn-xs btn-danger btn-panel" onclick="deleteCompanyBreak(' + day + ', ' + newId + ')">';
    html += '<i class="dir-icon-trash"></i>';
    html += '</a></div>';
    html += '<input type="hidden" name="break_ids[]" id="break_ids" value="" />';
    html += '</div>';

    jQuery('#break_hours_day_' + day).append(html);
    jQuery('#break_periods_count_' + day).val(newId);
    if (!multiple) {
        jQuery('#break_period_' + day).find('.break-button').attr('disabled', true);
        jQuery('#break_period_' + day).find('.break-button').attr('onclick', '');
    }

    jQuery('.timepicker').timepicker({'timeFormat': jbdUtils.timeFormat, 'minTime': '6:00am',});
}

function deleteCompanyBreak(day, id) {
    jQuery('#break_hour_period_' + day + '_' + id).remove();

    jQuery('#break_period_' + day).find('.break-button').removeAttr('disabled');
    var count = jQuery('#break_periods_count_' + day).val();
    if(count>0)
        count--;
    jQuery('#break_periods_count_' + day).val(count);
    jQuery('#break_period_' + day).find('.break-button').attr('onclick', 'addBreak(' + day + ', false)');
}

function deleteBreak(day, id) {
    jQuery('#break_hour_period_' + day + '_' + id).empty();
    var count = parseInt(jQuery('#break_periods_count_' + day).val());
    var newCount = count - 1;

    jQuery('#break_periods_count_' + day).val(newCount);
}

function updateAttributes(categoryId, companyId) {
    if (jbdUtils.isProfile != 1) {
        var attributesUrl = jbdUtils.siteRoot + 'administrator/index.php?option=com_jbusinessdirectory&task=company.getAttributesAjax';
    } else {
        var attributesUrl = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=managecompany.getAttributesAjax';
    }

    var packageId = 0;
    if(jbdUtils.enable_packages == 1)
        packageId = jQuery('#filter_package_select').val();

    jQuery.ajax({
        type: "POST",
        url: attributesUrl,
        data: {categoryId: categoryId, companyId: companyId, packageId: packageId},
        dataType: 'json',
        success: function (data) {
            jQuery('#customFieldsContent').html(data);
            jQuery(".chosen-select").chosen({width:"95%", disable_search_threshold: 5,search_contains: true});
        }
    });
}

function displaySubcategories(id, level, maxLevel) {
    var categoryId = jQuery("#" + id).val();

    if (!categoryId)
        categoryId = 0;
    //invalidate subcategories level
    for (var i = level + 1; i <= maxLevel; i++) {
        jQuery("#company_categories-level-" + i).html('');
    }
    jQuery("#company_categories-level-" + (level + 1)).html("<div style='width:20px;margin: 0 auto;'><img align='center' src='" + jbdUtils.imageRepo + "/assets/img/loading.gif'  /></div>");

    var postParameters = '';

    postParameters += "&categoryId=" + categoryId;

    var postData = '';
    if (jbdUtils.isProfile == 0)
        postData = '&option=com_jbusinessdirectory&task=company.getSubcategories' + postParameters;
    else
        postData = '&option=com_jbusinessdirectory&task=managecompany.getSubcategories' + postParameters;
    jQuery.post(jbdUtils.baseUrl, postData, processDisplaySubcategoriesResponse);
    //jQuery('#frmFacilitiesFormSubmitWait').show();
}

function processDisplaySubcategoriesResponse(responce) {
    var xml = responce;
    //jQuery('#frmFacilitiesFormSubmitWait').hide();
    jQuery(xml).find('answer').each(function () {
        if (jQuery(this).attr('error') == '1') {
            jQuery('#frm_error_msg_facility').className = 'text_error';
            jQuery('#frm_error_msg_facility').html(jQuery(this).attr('errorMessage'));
            jQuery('#frm_error_msg_facility').show();

        }
        else if (jQuery(this).attr('error') == '0') {

            jQuery("#subcategories").html(jQuery(this).attr('content_categories'));
            removeSelectedCategories();
            //clear current level
            jQuery("#company_categories-level-" + jQuery(this).attr('category-level')).html('');
            //clear next level
            level = 1 + parseInt(jQuery(this).attr('category-level'));
            jQuery("#company_categories-level-" + level).html('');
            if (jQuery(this).attr('isLastLevel') != '1') {
                jQuery("#company_categories-level-" + jQuery(this).attr('category-level')).html(jQuery(this).attr('content_select_categories'));

            }
        }
    });
}

function removeSelectedCategories() {
    jQuery("#mainSubcategory > option").each(function () {
        jQuery("#subcategories option[value=" + jQuery(this).val() + "]").remove();
    });
}

function hideDisapprovalBox() {
    jQuery("#disapprovalBox").hide();
}

function showDisapprovalBox() {
    jQuery("#disapprovalBox").show();
}
//*******************--End Company Section--************************//

//************--Company Service Provider Section--*****************//
function addVacation(id) {
    id = parseInt(id);
    var newId = id + 1;

    var deleteButton = '';
    deleteButton += '<br/>';
    deleteButton += '<a href="javascript:void(0)" class="btn btn-xs btn-danger btn-panel" onclick="deleteVacation(' + newId + ')">';
    deleteButton += '<i class="dir-icon-trash"></i>';
    deleteButton += '</a>';

    jQuery('#vacation_0').clone().prop('id', 'vacation_' + newId).insertAfter('#vacation_' + id);
    jQuery('#vacation_' + newId + ' #start_calendar_0').find('input').attr('id', 'start_date_' + newId);
    jQuery('#vacation_' + newId + ' #start_calendar_0').find('button').attr('id', 'start_date_' + newId + '_img');
    jQuery('#vacation_' + newId + ' #end_calendar_0').find('input').attr('id', 'end_date_' + newId);
    jQuery('#vacation_' + newId + ' #end_calendar_0').find('button').attr('id', 'end_date_' + newId + '_img');
    jQuery('#vacation_' + newId + ' #delete_vacation_0').prop('id', 'delete_vacation_' + newId);
    jQuery('#add_vacation').find('a').attr('onclick', 'addVacation(\'' + newId + '\')');
    jQuery('#delete_vacation_' + newId).html(deleteButton);

    initCalendar(newId);
    jQuery('#add_vacation_' + parseInt(id)).remove();
}

function deleteVacation(id) {
    var html = '';
    jQuery('#vacation_' + id).empty();
}

function updateServices() {
    var companyId = jQuery('#company_id').find(":selected").val();
    if (jbdUtils.isProfile == 0) {
        var urlGetServices = jbdUtils.url + '&task=companyserviceprovider.getServicesAjax';
    } else {
        var urlGetServices = jbdUtils.url + '&task=managecompanyserviceprovider.getServicesAjax';
    }

    jQuery.ajax({
        type: "POST",
        url: urlGetServices,
        data: {companyId: companyId},
        dataType: 'json',
        success: function (data) {
            jQuery('#services').html(data);
            jQuery("#services").trigger("liszt:updated");
            jQuery("#services").trigger("chosen:updated");
        }
    });
}

function initCalendar(id) {
    Calendar._DN = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
    Calendar._SDN = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    Calendar._FD = 0;
    Calendar._MN = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    Calendar._SMN = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    Calendar._TT = {
        "INFO": "About the Calendar",
        "ABOUT": "DHTML Date\/Time Selector\n(c) dynarch.com 2002-2005 \/ Author: Mihai Bazon\nFor latest version visit: http:\/\/www.dynarch.com\/projects\/calendar\/\nDistributed under GNU LGPL.  See http:\/\/gnu.org\/licenses\/lgpl.html for details.\n\nDate selection:\n- Use the \u00ab and \u00bb buttons to select year\n- Use the < and > buttons to select month\n- Hold mouse button on any of the buttons above for faster selection.",
        "ABOUT_TIME": "\n\nTime selection:\n- Click on any of the time parts to increase it\n- or Shift-click to decrease it\n- or click and drag for faster selection.",
        "PREV_YEAR": "Select to move to the previous year. Select and hold for a list of years.",
        "PREV_MONTH": "Select to move to the previous month. Select and hold for a list of the months.",
        "GO_TODAY": "Go to today",
        "NEXT_MONTH": "Select to move to the next month. Select and hold for a list of the months.",
        "SEL_DATE": "Select a date.",
        "DRAG_TO_MOVE": "Drag to move.",
        "PART_TODAY": " Today ",
        "DAY_FIRST": "Display %s first",
        "WEEKEND": "0,6",
        "CLOSE": "Close",
        "TODAY": "Today",
        "TIME_PART": "(Shift-)Select or Drag to change the value.",
        "DEF_DATE_FORMAT": "%Y-%m-%d",
        "TT_DATE_FORMAT": "%a, %b %e",
        "WK": "wk",
        "TIME": "Time:"
    };
    Calendar.setup({
        // Id of the input field
        inputField: "start_date_" + id,
        // Format of the input field
        ifFormat: "%d-%m-%Y",
        // Trigger for the calendar (button ID)
        button: "start_date_" + id + "_img",
        // Alignment (defaults to "Bl")
        align: "Tl",
        singleClick: true,
        firstDay: 0,
        defaultDate: new Date()
    });
    Calendar.setup({
        // Id of the input field
        inputField: "end_date_" + id,
        // Format of the input field
        ifFormat: "%d-%m-%Y",
        // Trigger for the calendar (button ID)
        button: "end_date_" + id + "_img",
        // Alignment (defaults to "Bl")
        align: "Tl",
        singleClick: true,
        firstDay: 0,
        defaultDate: new Date()
    });
}
//************--End Company Service Provider Section--*****************//

//************--Company Service Reservation Section--*****************//
var todayDate = new Date();
var maxDate = new Date();
maxDate.setMonth(todayDate.getMonth() + 6);

function updateProviders() {
    var serviceId = jQuery('#service_id').find(":selected").val();
    var urlGetProviders = url + '&task=companyservicereservation.getProvidersAjax';

    jQuery('#time-text').empty();
    jQuery('#date-text').empty();

    jQuery.ajax({
        type: "POST",
        url: urlGetProviders,
        data: {serviceId: serviceId},
        dataType: 'json',
        success: function (data) {
            jQuery('#provider_id').html(data);
        }
    });
}

function updateDates() {
    var urlGetDays = url + '&task=companyservicereservation.getVacationDaysAjax';
    var providerId = jQuery('#provider_id').find(":selected").val();

    jQuery.ajax({
        type: "POST",
        url: urlGetDays,
        data: {providerId: providerId},
        dataType: 'json',
        success: function (data) {
            jQuery('#datepicker').datepicker('destroy');
            jQuery('#datepicker').datepicker({
                beforeShowDay: function (date) {
                    var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                    return [data.indexOf(string) == -1]
                },
                onSelect: getAvailableHours,
                minDate: todayDate,
                maxDate: maxDate
            });
        }
    });
}

function getAvailableHours(date) {
    var urlGetHours = url + '&task=companyservicereservation.getAvailableHoursAjax';
    var serviceId = jQuery('#service_id').find(":selected").val();
    var providerId = jQuery('#provider_id').find(":selected").val();

    jQuery('#date').val(date);
    jQuery('#date-text').html(date);
    jQuery('#time-text').empty();

    jQuery.ajax({
        type: "POST",
        url: urlGetHours,
        data: {serviceId: serviceId, providerId: providerId, date: date},
        dataType: 'json',
        success: function (data) {
            jQuery('#morning').empty();
            jQuery('#afternoon').empty();
            jQuery('#evening').empty();

            jQuery('#morning').append(data.morning);
            jQuery('#afternoon').append(data.afternoon);
            jQuery('#evening').append(data.evening);

            selectedDate = date;
        }
    });
}

function selectHour(time) {
    jQuery('#time').val(time);
    jQuery('#time-text').html(time);
}
//************--End Company Service Reservation Section--*****************//