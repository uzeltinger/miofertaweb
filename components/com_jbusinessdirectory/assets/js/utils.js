JBDUtils = function () {
    this.baseUrl = null;
    this.imageRepo = null;
    this.imageBaseUrl = null;
    this.siteRoot = null;
    this.componentName = null;
    this.timeFormat = null;
    this.dateFormat = null;
    this.mapMarker = null;
    this.isProfile = null;
    this.isMultilingual = null;
    this.langTab = null;
    this.url = null;
    this.componentImagePath = null;
    this.enable_attribute_category = null;
    this.enable_packages = null;
    this.logo_width = null;
    this.logo_height = null;
    this.cover_width = null;
    this.cover_height = null;
    this.gallery_width = null;
    this.gallery_height = null;
    this.enable_crop = null;
    this.enable_resolution_check = null;

    this.construct = function (settings) {
        var self = this;
        jQuery.each(settings, function (key, value) {
            self[key] = value;
        });
        var tmp = '';
        if (this.isProfile == 0)
            tmp = 'administrator/';
        this.url = this.siteRoot + tmp + 'index.php?option=com_jbusinessdirectory';
    };

    this.getDateWithFormat = function (date) {
        var format = this.dateFormat;
        var delimiter = '-';

        if (format.indexOf('/') > -1)
            delimiter = '/';
        else if (format.indexOf('\\') > -1)
            delimiter = '\\';

        var tmp = format.split(delimiter);

        var newDate = '';
        for (var i = 0; i < 3; i++) {
            if (tmp[i] === 'd')
                newDate += ("0" + date.getDate()).slice(-2);
            else if (tmp[i] === 'm')
                newDate += ("0" + (date.getMonth() + 1)).slice(-2);
            else if (tmp[i] === 'y' || tmp[i] === 'Y')
                newDate += date.getFullYear();

            if (i < 2)
                newDate += delimiter;
        }

        return newDate;
    }
};

function increaseWebsiteClicks(itemId) {
    var urlWebsiteCount = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=companies.increaseWebsiteCount&companyId=' + itemId;

    jQuery.ajax({
        type: "POST",
        url: urlWebsiteCount,
        success: function () {
        }
    });
}

function increaseShareClicks(itemId, itemType) {
    var urlShareCount = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=companies.increaseShareCount&itemId=' + itemId + '&itemType=' + itemType;

    jQuery.ajax({
        type: "POST",
        url: urlShareCount,
        success: function () {
        }
    });
}

function addCoordinatesToUrl(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    var newURLString = window.location.href;
    newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
    newURLString += "geo-latitude=" + latitude;
    newURLString += ((newURLString.indexOf('?') == -1) ? '?' : '&');
    newURLString += "geo-longitude=" + longitude;

    window.location.href = newURLString;    // The page will redirect instantly
}

function updateCompanyRate(companyId, rateScore) {
    var postParameters = "";
    ratingId = getRatingId(companyId);
    if (ratingId == undefined) {
        ratingId = 0;
    }
    postParameters += "&companyId=" + companyId;
    postParameters += "&rating=" + rateScore;
    postParameters += "&ratingId=" + ratingId;

    var postData = '&task=companies.updateRating' + postParameters;
    jQuery.post(jbdUtils.baseUrl, postData, processRateResult);
}

function processRateResult(responce) {
    var xml = responce;

    jQuery(xml).find('answer')
        .each(
            function () {
                jQuery("#rateNumber" + jQuery(this).attr('id')).html(
                    jQuery(this).attr('nrRatings'));
                jQuery("#rateNumber" + jQuery(this).attr('id'))
                    .parent().show();
                jQuery('#rating-average').raty('start', jQuery(this).attr('averageRating'));
                saveCookieRating(jQuery(this).attr('id'), jQuery(this).attr('ratingId'));
            });
}

function getRatingId(companyId) {
    var ratings = getCookie("companyRatingIds");
    if (ratings == undefined)
        return;
    var ratingsIds = ratings.split('#');
    for (i = 0; i < ratingsIds.length; i++) {
        temp = ratingsIds[i].split(',');
        if (temp[0] == companyId)
            return temp[1];
    }
}

function saveCookieRating(companyId, reviewId) {
    var ratings = getCookie("companyRatingIds");
    if (ratings == undefined)
        ratings = companyId + ',' + reviewId + '#';

    var ratingsIds = ratings.split('#');
    var found = false;
    for (i = 0; i < ratingsIds.length; i++) {
        temp = ratingsIds[i].split(',');
        if (temp[0] == companyId)
            found = true;
    }
    if (!found) {
        ratings = ratings + companyId + ',' + reviewId + '#';
    }
    setCookie("companyRatingIds", ratings, 60);
}


function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");
    for (i = 0; i < ARRcookies.length; i++) {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x == c_name) {
            return unescape(y);
        }
    }
}

function setCookie(c_name, value, exdays) {
    var exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value = escape(value)
        + ((exdays == null) ? "" : "; expires=" + exdate.toUTCString());
    document.cookie = c_name + "=" + c_value;
}

jQuery.fn.center = function () {
    this.css("left", ( jQuery(window).width() - this.width() ) / 2 + jQuery(window).scrollLeft() + "px");
    return this;
};

function renderRadioButtons() {
    //Turn radios into btn-group
    jQuery('.radio.btn-group label').addClass('btn');
    jQuery('.btn-group label:not(.active)').click(function () {
        var label = jQuery(this);
        var input = jQuery('#' + label.attr('for'));

        if (!input.prop('checked')) {
            label.closest('.btn-group').find('label').removeClass('active btn-success btn-danger btn-primary');
            if (input.val() == '') {
                label.addClass('active btn-primary');
            } else if (input.val() == 0) {
                label.addClass('active btn-danger');
            } else {
                label.addClass('active btn-success');
            }
            input.prop('checked', true);
        }
    });

    jQuery('.btn-group input[checked=checked]').each(function () {
        if (jQuery(this).val() == '') {
            jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-primary');
        } else if (jQuery(this).val() == 0) {
            jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-danger');
        } else {
            jQuery('label[for=' + jQuery(this).attr('id') + ']').addClass('active btn-success');
        }
    });
}

//************--Edit Views--*****************//
var placeSearch, autocomplete;
var component_form;

function initializeAutocomplete(preventSubmit, componentForm) {
    autocomplete = new google.maps.places.Autocomplete(document.getElementById('autocomplete'), {types: ['geocode']});
    google.maps.event.addListener(autocomplete, 'place_changed', function () {
        fillInAddress();
    });

    component_form = componentForm;

    if (preventSubmit) {
        var input = document.getElementById('autocomplete');
        google.maps.event.addDomListener(input, 'keydown', function (e) {
            if (e.keyCode == 13 && jQuery('.pac-container:visible').length) {
                e.preventDefault();
            }
        });
    }
}

function fillInAddress() {
    var place = autocomplete.getPlace();
    for (var component in component_form) {
        var obj = document.getElementById(component);
        if (typeof maybeObject != "undefined") {
            document.getElementById(component).value = "";
            document.getElementById(component).disabled = false;
        }
    }
    for (var j = 0; j < place.address_components.length; j++) {
        var att = place.address_components[j].types[0];
        if (component_form[att]) {
            var val = place.address_components[j][component_form[att]];
            jQuery("#" + att).val(val);
            if (att == 'country') {
                jQuery('#country option').filter(function () {
                    return jQuery(this).text() === val;
                }).attr('selected', true);
            }
        }
    }

    if (typeof map != "undefined") {
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        }

        map.setCenter(place.geometry.location);
        addMarker(place.geometry.location);
    }
}

function geolocate() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            var geolocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            autocomplete.setBounds(new google.maps.LatLngBounds(geolocation, geolocation));
        });
    }
}

var map;
var markers = [];

function initializeAdminMap(params) {
    var companyLocation = new google.maps.LatLng(params['map_latitude'], params['map_longitude']);
    var mapOptions = {
        zoom: params['map_zoom'],
        center: companyLocation,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var mapdiv = document.getElementById("company_map");
    mapdiv.style.width = '100%';
    mapdiv.style.height = '400px';
    map = new google.maps.Map(mapdiv, mapOptions);
    var latitude = params['latitude'];
    var longitude = params['longitude'];
    if (latitude && longitude)
        addMarker(new google.maps.LatLng(latitude, longitude));
    google.maps.event.addListener(map, 'click', function (event) {
        deleteOverlays();
        addMarker(event.latLng);
    });
}

// Add a marker to the map and push to the array.
function addMarker(location) {
    document.getElementById("latitude").value = location.lat();
    document.getElementById("longitude").value = location.lng();
    marker = new google.maps.Marker({
        position: location,
        map: map
    });
    markers.push(marker);
}

// Sets the map on all markers in the array.
function setAllMap(map) {
    for (var i = 0; i < markers.length; i++) {
        markers[i].setMap(map);
    }
}

// Removes the overlays from the map, but keeps them in the array.
function clearOverlays() {
    setAllMap(null);
}

// Shows any overlays currently in the array.
function showOverlays() {
    setAllMap(map);
}

// Deletes all markers in the array by removing references to them.
function deleteOverlays() {
    clearOverlays();
    markers = [];
}

function loadAdminMapScript(showMap, params) {
    if (showMap == 1) {
        initializeAdminMap(params);
    }
}

function validateCmpForm(validateNonVisible, validateRichText) {
    if (validateRichText) {
        validateRichTextEditors();
    }

    validateMultiSelects();
    var isError = jQuery("#item-form").validationEngine('validate', {validateNonVisibleFields: validateNonVisible});
    return !isError;
}

function showValidationError(){
	jQuery("#validation-error").show(500);
	
	var scrollTopOfset = jQuery("#validation-error").offset().top-50;
	jQuery('html,body').animate({scrollTop: scrollTopOfset},'slow');
	
	setTimeout(function(){
		jQuery("#validation-error").hide(500);
	}, 7000);
}

function validateTabs(validateNonVisible, validateRichText) {
	var validationResult = false;
	if (jbdUtils.isMultilingual) {
    	jQuery(".tab-"+jbdUtils.defaultLang).each(function(){
    		jQuery(this).click();
    	});
    }
		
	if (validateRichText) {
        validateRichTextEditors();
    }

    validateMultiSelects();
	jQuery("#item-form").validationEngine('attach', {
	    validateNonVisibleFields: validateNonVisible,
	});
	
	validationResult = jQuery("#item-form").validationEngine('validate');
		
	if(!validationResult){
		showValidationError();
	}
	
	return validationResult;
}

function validateRichTextEditors() {
	var lang = '';
    if (jbdUtils.isMultilingual) {
        lang += '_' + jbdUtils.langTab;
    	jQuery(".tab-"+jbdUtils.defaultLang).each(function(){
    		jQuery(this).click();
    	});

        jQuery(".tab_description_"+jbdUtils.defaultLang).click();
    }
    
    jQuery(".editor").each(function () {
        var textarea = jQuery(this).find('textarea');
        tinyMCE.triggerSave();
        if (textarea.attr('id') == 'description' + lang) {
            if (jQuery.trim(textarea.val()).length > 0) {
                if (jQuery(this).hasClass("validate[required]"))
                    jQuery(this).removeClass("validate[required]");
            }
            else {
                if (!jQuery(this).hasClass("validate[required]"))
                    jQuery(this).addClass("validate[required]");
            }
        }
    });
}

function showItem(link) {
    var win = window.open(link, '_blank');
    win.focus();
}

function applyReadMore() {
    var showChar = 70;  // How many characters are shown by default
    var ellipsestext = "...";
    var moretext = "Show more";
    var lesstext = "Show less";

    jQuery('.read-more').each(function () {
        var content = jQuery(this).html();

        if (content.length > showChar) {

            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="javascript:void(0)" class="morelink" >' + moretext + '</a></span>';

            jQuery(this).html(html);
        }

    });

    jQuery(".morelink").click(function () {
        if (jQuery(this).hasClass("less")) {
            jQuery(this).removeClass("less");
            jQuery(this).html(moretext);
        } else {
            jQuery(this).addClass("less");
            jQuery(this).html(lesstext);
        }
        jQuery(this).parent().prev().toggle();
        jQuery(this).prev().toggle();
        return false;
    });
}

function addVideo() {
    var count = jQuery("#video-container").children().length + 1;
    id = 0;
    var outerDiv = document.createElement('div');
    outerDiv.setAttribute('class', 'detail_box');
    outerDiv.setAttribute('id', 'detailBox' + count);

    var newLabel = document.createElement('label');
    newLabel.setAttribute("for", id);
    newLabel.innerHTML = Joomla.JText._('LNG_VIDEO');

    var newInput = document.createElement('textarea');
    newInput.setAttribute('name', 'videos[]');
    newInput.setAttribute('id', id);
    newInput.setAttribute('class', 'input_txt');
    newInput.setAttribute('rows', '1');

    var img_del = document.createElement('img');
    img_del.setAttribute('src', jbdUtils.siteRoot + "administrator/components/" + jbdUtils.componentName + "/assets/img/del_icon.png");
    img_del.setAttribute('alt', 'Delete option');
    img_del.setAttribute('height', '12px');
    img_del.setAttribute('width', '12px');
    img_del.setAttribute('align', 'left');
    img_del.setAttribute('onclick', 'removeRow("detailBox' + count + '")');
    img_del.setAttribute('style', "cursor: pointer; margin:3px;");

    var clearDiv = document.createElement('div');
    clearDiv.setAttribute('class', 'clear');

    outerDiv.appendChild(newLabel);
    outerDiv.appendChild(newInput);
    outerDiv.appendChild(img_del);
    outerDiv.appendChild(clearDiv);

    var facilityContainer = jQuery("#video-container");
    facilityContainer.append(outerDiv);

    checkNumberOfVideos();
}


function removeRow(id) {
    jQuery('#' + id).remove();
    checkNumberOfVideos();
    checkNumberOfSounds();
}

function checkNumberOfVideos() {
    var nrVideos = jQuery('textarea[name*="videos[]"]').length;
    if (nrVideos < maxVideos) {
        jQuery("#add-video").show();
    }
    else {
        jQuery("#add-video").hide();
    }
}


function addSound() {
    var count = jQuery("#sound-container").children().length + 1;
    id = 0;
    var outerDiv = document.createElement('div');
    outerDiv.setAttribute('class', 'detail_box');
    outerDiv.setAttribute('id', 'soundDetailBox' + count);

    var newLabel = document.createElement('label');
    newLabel.setAttribute("for", id);
    newLabel.innerHTML = Joomla.JText._('LNG_SOUND');

    var newInput = document.createElement('textarea');
    newInput.setAttribute('name', 'sounds[]');
    newInput.setAttribute('id', id);
    newInput.setAttribute('class', 'input_txt');
    newInput.setAttribute('rows', '3');

    var img_del = document.createElement('img');
    img_del.setAttribute('src', jbdUtils.siteRoot + "administrator/components/" + jbdUtils.componentName + "/assets/img/del_icon.png");
    img_del.setAttribute('alt', 'Delete option');
    img_del.setAttribute('height', '12px');
    img_del.setAttribute('width', '12px');
    img_del.setAttribute('align', 'left');
    img_del.setAttribute('onclick', 'removeRow("soundDetailBox' + count + '")');
    img_del.setAttribute('style', "cursor: pointer; margin:3px;");

    var clearDiv = document.createElement('div');
    clearDiv.setAttribute('class', 'clear');

    outerDiv.appendChild(newLabel);
    outerDiv.appendChild(newInput);
    outerDiv.appendChild(img_del);
    outerDiv.appendChild(clearDiv);

    var facilityContainer = jQuery("#sound-container");
    facilityContainer.append(outerDiv);

    checkNumberOfSounds();
}

function checkNumberOfSounds() {
    var nrVideos = jQuery('textarea[name*="sounds[]"]').length;
    if (nrVideos < 15) {
        jQuery("#add-sound").show();
    }
    else {
        jQuery("#add-sound").hide();
    }
}


function calculateLenght() {
    var obj = jQuery("#description");

    var max = parseInt(obj.attr('maxlength'));
    if (obj.val().length > max) {
        obj.val(obj.val().substr(0, obj.attr('maxlength')));
    }

    jQuery("#descriptionCounter").val((max - obj.val().length));
}

function calculateLenghtShort() {
    var obj = jQuery("#short_description");

    var max = parseInt(obj.attr('maxlength'));
    if (obj.val().length > max) {
        obj.val(obj.val().substr(0, obj.attr('maxlength')));
    }

    jQuery("#descriptionCounterShort").val((max - obj.val().length));
}

var cropper;

function showCropper(dataUri, type, picId) {
    if(typeof cropper !== 'undefined')
        cropper.destroy();
    cropped = false;

    if(picId === undefined || picId === null)
        picId= '';

    jQuery.blockUI({ message: jQuery('#cropper-modal'), centerX: true, centerY: false, css: {width: 'auto', left:"0", position:"absolute", cursor:'default'} });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI);

    jQuery('#cropper-image').attr('src', '');
    jQuery('#cropper-image').attr('src', dataUri);
    jQuery('#save-cropped').attr('onclick', 'saveCropped("'+type+'", "'+picId+'")');

    var width;
    var height;
    if(type.length == 0) {
        removeLogo();
        width = jbdUtils.logo_width;
        height = jbdUtils.logo_height;
    }
    else if(type === 'cover-') {
        width = jbdUtils.cover_width;
        height = jbdUtils.cover_height;
        removeCoverImage();
    }
    else if(type === 'service-') {
        width = jbdUtils.gallery_width;
        height = jbdUtils.gallery_height;
        removeServiceLogo(picId);
    }
    else {
        width = jbdUtils.gallery_width;
        height = jbdUtils.gallery_height;
    }

    var image = document.getElementById('cropper-image');
    cropper = new Cropper(image, {
        aspectRatio: width / height,
        cropBoxResizable: false,
        dragMode: 'move',
        scalable: true,
        crop: function(e) {
        },
        built: function () {
            jQuery(this).toCrop.cropper("setCropBoxData", {left:width, top:0, width: width, height: height });
        }
    });
}

function saveCropped(type, picId) {
    cropper.getCroppedCanvas().toBlob(function (blob) {
        var formData = new FormData();
        blob['name'] = 'cropped.'+blob['type'].substr(blob['type'].indexOf('/')+1, blob.type.length);
        formData.append('croppedimage', blob);

        var submitPath = '';
        if(type.length == 0)
            submitPath = companyFolderPath;
        else if(type === 'cover-')
            submitPath = companyFolderPathCover;
        else
            submitPath = companyFolderPathGallery;

        submitPath += '&crop=1';
        jQuery.ajax(submitPath, {
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (xml) {
                jQuery(xml).find("picture").each(function() {
                    if(jQuery(this).attr("error") == 0 ) {
                        setUpImage(
                            companyFolder + jQuery(this).attr("path"),
                            jQuery(this).attr("name"),
                            type,
                            picId
                        );
                        jQuery("#remove-image-loading").remove();
                    }
                    else if( jQuery(this).attr("error") == 1 )
                        alert(Joomla.JText._('LNG_FILE_ALLREADY_ADDED'));
                    else if( jQuery(this).attr("error") == 2 )
                        alert(Joomla.JText._('LNG_ERROR_ADDING_FILE'));
                    else if( jQuery(this).attr("error") == 3 )
                        alert(Joomla.JText._('LNG_ERROR_GD_LIBRARY'));
                    else if( jQuery(this).attr("error") == 4 )
                        alert(Joomla.JText._('LNG_ERROR_RESIZING_FILE'));
                });

                jQuery.unblockUI();
            },
            error: function () {
                console.log('Upload error');
            }
        });
    });
}

function validateMultiSelects() {
    jQuery('.chzn-container-multi').each(function () {
        var id = jQuery(this).attr('id');
        var selectId = id.substr(0, id.lastIndexOf("_"));

        if (jQuery('#' + selectId).hasClass('validate[required]') || jQuery('#' + id).hasClass('validate[required]')) {
            var values = jQuery('#' + selectId).chosen().val();
            if (typeof values === 'undefined' || values == null) {
                if (!jQuery('#' + id).hasClass('validate[required]'))
                    jQuery('#' + id).addClass('validate[required]');
                jQuery('#' + selectId).removeClass('validate[required]');
            }
            else {
                jQuery('#' + id).removeClass('validate[required]');
                jQuery('#' + selectId).addClass('validate[required]');
            }
        }

    });
}

//************--End Edit Views--***********************//

//************--Attribute Edit View--*****************//
function doAction(value) {
    disabled = false;
    if (value == 1 || value == 6 || value == 7 || value == 5)
        disabled = true;
    var optionsTemp = document.getElementsByName("option_name[]");
    for (i = 0; i < optionsTemp.length; ++i) {
        optionsTemp[i].disabled = disabled;
    }

    if (disabled) {
        jQuery(".attr-img").hide();
        jQuery('.attribute_icon_container').hide(500);
    }
    else {
        jQuery(".attr-img").show();
        showIconFields();
    }
}

function deleteAttributeOption(pos) {
    var lis=document.querySelectorAll('#list_feature_options li');

    if(lis==null) {
        alert('Undefined List, contact administrator !');
    }

    var count = jQuery('input[name="option_name[]"]').length;
    if(count == 1)
        return false;

    if(pos >= lis.length)
        pos = lis.length-1;
    lis[pos].parentNode.removeChild(lis[pos]);

    //return jQuery('#'+id).remove();// (elem = document.getElementById(id)).remove();
}

function addAttributeOption() {
    var attrType = document.getElementsByName("type");
    if (attrType[0].checked) {
        alert(Joomla.JText._("LNG_INPUT_TYPE_NO_OPTIONS"));
        return false;
    }
    var tb = document.getElementById('list_feature_options');
    if (tb == null) {
        alert('Undefined list, contact administrator !');
    }

    var li_new	= document.createElement('li');

    var div_new = document.createElement('div');

    var span_new = document.createElement('span');
    span_new.setAttribute('class','ui-icon ui-icon-arrowthick-2-n-s');
    jQuery(span_new).css("cssText", "margin-top: 8px; float: left");
    div_new.appendChild(span_new);

    var input_o_new = document.createElement('input');
    input_o_new.setAttribute('type', 'text');
    input_o_new.setAttribute('name', 'option_name[]');
    input_o_new.setAttribute('class','input_txt validate[required] attribute_option');
    input_o_new.setAttribute('id', 'option_name[]');
    input_o_new.setAttribute('size', '32');
    input_o_new.setAttribute('maxlength', '128');
    var d = new Date();
    var id = d.getTime();

    var icons_div = document.createElement('div');
    icons_div.setAttribute('class', 'input-group iconpicker-container attribute_icon_container');
    var iconsHtml = '';
    iconsHtml += '<input name="icon[]" data-placement="bottomRight" class="form-control icp icp-auto iconpicker-element iconpicker-input attribute_icon" value="dir-icon-500px" type="hidden">';
    iconsHtml += '<span class="input-group-addon"><i class="dir-icon-500px"></i></span>';
    icons_div.innerHTML = iconsHtml;

    var img_del = document.createElement('img');
    img_del.setAttribute('src', jbdUtils.imageRepo + '/assets/img/deleteIcon.png');
    img_del.setAttribute('class','attr-img');
    img_del.setAttribute('alt', 'Delete option');
    img_del.setAttribute('height', '12px');
    img_del.setAttribute('width', '12px');
    img_del.onclick = function() {
        var row = jQuery(this).parents('li:first');
        var row_idx = row.prevAll().length;
        jQuery('#crt_pos').val(row_idx);
        jQuery('#btn_removefile').click();
        deleteAttributeOption(jQuery('#crt_pos').val())
    };
    img_del.setAttribute('onmouseover', "this.style.cursor='hand';this.style.cursor='pointer'");
    img_del.setAttribute('onmouseout', "this.style.cursor='default'");

    div_new.appendChild(input_o_new);
    div_new.appendChild(icons_div);
    div_new.innerHTML = div_new.innerHTML + "&nbsp;&nbsp;";
    div_new.appendChild(img_del);
    li_new.appendChild(div_new);
    tb.appendChild(li_new);

    jQuery('.icp-auto').iconpicker({
        placement: 'topRightCorner'
    });
    showIconFields();
}
//************--End Attribute Edit View--*************//

//************--Packages Edit View--*****************//
function deleteProcessorOption(id) {
    var count = jQuery('.processor-fields');

    if (count.length > 1) {
        jQuery('#processor_field_' + id).remove();
    } else {
        var nameInput = jQuery('#processor_field_' + id + ' #column_name').find('input');
        nameInput.val('');
        nameInput.attr('type', 'text');
        nameInput.attr('placeholder', Joomla.JText._("LNG_COLUMN_NAME"));
        jQuery('#processor_field_' + id + ' .hasTooltip').empty();

        var valueInput = jQuery('#processor_field_' + id + ' #column_value').find('input');
        valueInput.val('');
        valueInput.attr('placeholder', Joomla.JText._("LNG_COLUMN_VALUE"));

        jQuery('#processor_field_' + id + ' #delete_processor_field_' + id).empty();
    }
}

function addProcessorOption(id) {
    id = parseInt(id);
    var newId = id + 1;

    var deleteButton = '';
    deleteButton += '<a href="javascript:void(0)" class="btn btn-xs btn-danger btn-panel" onclick="deleteProcessorOption(' + newId + ')">';
    deleteButton += '<i class="dir-icon-trash"></i>';
    deleteButton += '</a>';

    var fields = jQuery('.processor-fields');
    var lastId = jQuery(fields[fields.length - 1]).attr('id').slice(-1);

    jQuery('#processor_field_'+lastId).clone().prop('id', 'processor_field_' + newId).insertAfter('#processor_field_' + lastId);
    var newNameField = jQuery('#processor_field_' + newId + ' #column_name').find('input');
    newNameField.attr('id', 'column_name_' + newId);
    newNameField.attr('type', 'text');
    newNameField.val('');
    newNameField.attr('placeholder', Joomla.JText._("LNG_COLUMN_NAME"));

    jQuery('#processor_field_' + newId + ' .hasTooltip').empty();
    jQuery('#processor_field_' + newId + ' #column_name_'+lastId).find('button').attr('id', 'column_name_' + newId + '_img');
    var newValueField = jQuery('#processor_field_' + newId + ' #column_value').find('input');
    newValueField.attr('id', 'column_value_' + newId);
    newValueField.attr('placeholder', Joomla.JText._("LNG_COLUMN_VALUE"));
    newValueField.val('');
    jQuery('#processor_field_' + newId + ' #column_value_'+lastId).find('button').attr('id', 'column_value_' + newId + '_img');
    jQuery('#processor_field_' + newId + ' #delete_processor_field_'+lastId).prop('id', 'delete_processor_field_' + newId);
    jQuery('#add_processor_field').find('a').attr('onclick', 'addProcessorOption(\'' + newId + '\')');
    jQuery('#delete_processor_field_' + newId).html(deleteButton);

    jQuery('#add_processor_field' + parseInt(id)).remove();
}
//**********--End Packages Edit View--*************//

//************--Front End Views--*****************//
function saveForm(formId) {
    var isError = true;
    jQuery('#' + formId).validationEngine('detach');

    isError = jQuery('#' + formId).validationEngine('validate');
    jQuery('#' + formId).validationEngine('attach');
    if (!isError)
        return;

    document.getElementById(formId).submit();
}

function showLoginNotice() {
    jQuery.blockUI({
        message: jQuery('#login-notice'),
        css: {width: 'auto', top: '5%', left: "0", position: "absolute"}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#login-notice").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}

function showContactCompany() {
    jQuery.blockUI({
        message: jQuery('#company-contact'),
        css: {width: 'auto', top: '10%', left: "0", position: "absolute", cursor: 'default'}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#company-contact").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}

function contactCompany(showData) {
    if (showData == 0) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        showContactCompany();
    }
}

function showMap(display) {
    jQuery("#map-link").toggleClass("active");

    if (jQuery("#map-link").hasClass("active")) {
        jQuery("#companies-map-container").show();
        jQuery("#map-link").html(Joomla.JText._("LNG_HIDE_MAP"));
        loadMapScript();
    } else {
        jQuery("#map-link").html(Joomla.JText._("LNG_SHOW_MAP"));
        jQuery("#companies-map-container").hide();
    }
}


function saveSelectedCategory(categorySet, categId) {
    var catId;
    var checked = jQuery("#search-filter input[type='checkbox']:checked");
    catId = checked.attr('id');

    if (categorySet) {
        catId = categId;
    }

    jQuery("#adminForm #categoryId").val(catId);
    jQuery("#adminForm input[name=limitstart]").val(0);
}

function chooseCategory(categoryId) {
    if (categoryId.toString().substring(0, 3) == "chk") {
        categoryId = categoryId.substring(3);
    }
    categoryId = categoryId.toString().replace(";", "");
    jQuery("#adminForm #categoryId").val(categoryId);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}

function addFilterRule(type, id, categorySet, categId) {
    var val = type + '=' + id + ';';
    if (jQuery("#selectedParams").val().length > 0) {
        jQuery("#selectedParams").val(jQuery("#selectedParams").val() + val);
    } else {
        jQuery("#selectedParams").val(val);
    }

    if (categorySet) {
        jQuery("#filter_active").val("1");
    }

    jQuery("#adminForm input[name=limitstart]").val(0);
    saveSelectedCategory(categorySet, categId);
    jQuery("#adminForm").submit();
}

function removeFilterRule(type, id, categorySet, categId) {
    var val = type + '=' + id + ';';
    var str = jQuery("#selectedParams").val();
    jQuery("#selectedParams").val((str.replace(val, "")));
    jQuery("#filter_active").val("1");
    saveSelectedCategory(categorySet, categId);

    if (type == "city")
        jQuery("#adminForm #city-search").val("");
    if (type == "region")
        jQuery("#adminForm #region-search").val("");
    if (type == "country")
        jQuery("#adminForm #country-search").val("");
    if (type == "type")
        jQuery("#adminForm #type-search").val("");

    jQuery("#adminForm").submit();

}

function resetFilters(resetCategories, categorySet, categId) {
    jQuery("#selectedParams").val("");
    if (resetCategories)
        jQuery("#categories-filter").val("");
    else
        saveSelectedCategory(categorySet, categId);
    jQuery("#adminForm #categoryId").val("");

    jQuery("#adminForm #searchkeyword").val("");
    jQuery("#adminForm #zipcode").val("");
    jQuery("#adminForm #city-search").val("");
    jQuery("#adminForm #region-search").val("");
    jQuery("#adminForm #country-search").val("");
    jQuery("#adminForm #type-search").val("");
    jQuery("#adminForm #radius").val("");
    jQuery("#adminForm #startDate").val("");
    jQuery("#adminForm #endDate").val("");
    jQuery("#adminForm #filter-by-fav").val("");
    jQuery("#adminForm #resetSearch").val("1");

    jQuery("#adminForm").submit();
}

function addFilterRuleCategory(catId) {
    catId = catId + ";";
    if (jQuery("#categories-filter").val().length > 0) {
        jQuery("#categories-filter").val(jQuery("#categories-filter").val() + catId);
    } else {
        jQuery("#categories-filter").val(catId);
    }
    jQuery("#filter_active").val("1");
    jQuery("#adminForm input[name=limitstart]").val(0);
    chooseCategory(catId);
}

function removeFilterRuleCategory(catId) {
    var categoryId = catId + ";";
    var str = jQuery("#categories-filter").val();
    jQuery("#categories-filter").val((str.replace(categoryId, "")));
    jQuery("#filter_active").val("1");
    var checked = jQuery("#filterCategoryItems input[type='checkbox']:checked");
    if (checked.length > 0) {
        checked.each(function () {
            var id = jQuery(this).attr('id');
            if (id != catId) {
                chooseCategory(id);
                return false;
            }
        });
    }
    else if (checked.length == 0) {
        var categoryIds = jQuery("#categories-filter").val();
        var categoryId = categoryIds.slice(0, categoryIds.length - 1);
        var start = categoryId.lastIndexOf(';') + 1;
        if (start == -1)
            start = 0;

        categoryId = categoryId.slice(start, categoryId.length);
        chooseCategory(categoryId);
    }
}

function showMoreParams(div, lessButton) {
    var div = "#" + div;
    var less = "#" + lessButton;
    jQuery(div).removeAttr("style");
    jQuery(less).css("display", "none");
}

function showLessParams(div, moreButton) {
    var div = "#" + div;
    var more = "#" + moreButton;
    jQuery(div).css("display", "none");
    jQuery(more).removeAttr("style");
}



function initSlider(params) {
    var sliderId = '#slider';
    var sliderContentId = '#slider-content';
    if(typeof params['sliderId'] !== 'undefined') {
        sliderId += '-'+params['sliderId'];
        sliderContentId += '-'+params['sliderId'];
    }

    var autoplay = false;
    if(typeof params['autoplay'] !== 'undefined')
        autoplay = params['autoplay'];

    var autoplaySpeed = 0;
    if(typeof params['autoplaySpeed'] !== 'undefined')
        autoplaySpeed = params['autoplaySpeed'];

    var nrVisibleItems = 0;
    if(typeof params['nrVisibleItems'] !== 'undefined')
        nrVisibleItems = parseInt(params['nrVisibleItems']);

    var nrVisibleItems1024 =3;
    if(nrVisibleItems1024>nrVisibleItems)
    	nrVisibleItems1024 = nrVisibleItems;
    var nrVisibleItems600 =2;
    if(nrVisibleItems600>nrVisibleItems)
    	nrVisibleItems600 = nrVisibleItems;
    
    var nrItemsToScrool = 0;
    if(typeof params['nrItemsToScrool'] !== 'undefined')
        nrItemsToScrool = parseInt(params['nrItemsToScrool']);

    var rtl = false;
    if(typeof params['rtl'] !== 'undefined')
        rtl = params['rtl'];

    jQuery(sliderId).slick({
        dots: false,
        prevArrow: '<a class="controller-prev" href="javascript:;"><span><i class="dir-icon-angle-left"></i></span></a>',
        nextArrow: '<a class="controller-next" href="javascript:;"><span><i class="dir-icon-angle-right"></i></span></a>',
        customPaging: function(slider, i) {
            return '<a class="controller-dot" href="javascript:;"><span><i class="dir-icon-circle"></i></span></a>';
        },
        autoplay: autoplay,
        autoplaySpeed: autoplaySpeed,
        speed: 300,
        slidesToShow: nrVisibleItems,
        slidesToScroll: nrItemsToScrool,
        infinite: true,
        rtl: rtl,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: nrVisibleItems1024,
                    slidesToScroll: nrVisibleItems1024,
                    infinite: true,
                    dots: false
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: nrVisibleItems600,
                    slidesToScroll: nrVisibleItems600
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1
                }
            }]
    });

    jQuery(sliderContentId).each(function(){
    });
}

//************--End Front End Views--*****************//


function showProductCategories(parentId) {
    jQuery('.categories-level-1').hide(500);
    jQuery('.categories-level-'+parentId).show(500);
    var parent = jQuery('#parent-category-'+parentId+' h1').text();
    parent = parent+' - '+Joomla.JText._('LNG_SUBCATEGORIES');
    jQuery('#sub-categories').html('<a href="javascript:void(0);" onclick="showProductCategories('+parentId+')">'+parent+'</a>&raquo;');
    jQuery('#category-products').empty();
    jQuery('#product-details').empty();
    jQuery('#product-details-content').empty();
    jQuery('#product-list-content').empty();
}

function goBack() {
    jQuery('.grid4').hide(500);
    jQuery('.categories-level-1').show(500);
    jQuery('#sub-categories').empty();
    jQuery('#category-products').empty();
    jQuery('#product-details').empty();
    jQuery('#product-details-content').empty();
    jQuery('#product-list-content').empty();
}

function showProducts(catId, companyId) {
    jQuery('.grid4').hide(500);
    jQuery('#product-list-content').html('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>').load(jbdUtils.url+'&view=companyproducts #grid-content', {categoryId: catId, companyId: companyId}, function() {
        var categoryName = jQuery('#product-category h1').text();
        jQuery('#category-products').html('<a href="javascript:void(0);" onclick="goBackToProducts('+catId+', '+companyId+')">'+categoryName+'</a>&raquo;');
    });
    jQuery('#company-products-title').text(Joomla.JText._('LNG_PRODUCTS'));
    jQuery('#product-list-content').attr('style', 'display: block;');
    jQuery('#category-products').empty();
    jQuery('#product-details').empty();
    jQuery('#product-details-content').empty();
}

function goBackToCategories(catId) {
    jQuery('#product-list-content').empty();
    jQuery('#subcategory-'+catId).closest('.grid4').show(500);
    jQuery('#company-products-title').text(Joomla.JText._('LNG_PRODUCT_CATEGORIES'));
}

function showProductDetails(productId, catId) {
    jQuery('#product-list-content').hide(500);
    jQuery('#product-details-content').html('<p id="remove-image-loading" class="text-center"><span class="icon-refresh icon-refresh-animate"></span> Loading...</p>').load(jbdUtils.url+'&view=companyproducts #product-details', {productId: productId, categoryId: catId}, function() {
        var productName = jQuery('#product-name h2').text();
        jQuery('#product-details').html('<a style="color:black;">'+productName+'</a>');

    });
    jQuery('#company-products-title').text(Joomla.JText._('LNG_PRODUCT_DETAILS'));
    jQuery('#product-details-content').show(500);
}

function goBackToProducts(catId, companyId) {
    jQuery('#product-details-content').hide(500);
    jQuery('#product-details-content').empty();
    showProducts(catId, companyId);
    jQuery('#product-list-content').show(500);
    jQuery('#product-list-content').attr('style', 'display: block;');
}

function addBookmark(requiresLogin, customId) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        var id = 'add-bookmark';
        if(typeof customId != 'undefined')
            id = customId;

        jQuery.blockUI({
            message: jQuery('#'+id),
            css: {width: 'auto', top: '5%', left: "0", position: "absolute", cursor: 'default'}
        });
        jQuery('.blockUI.blockMsg').center();
        jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
        jQuery(document).scrollTop(jQuery("#add-bookmark").offset().top);
        jQuery("html, body").animate({scrollTop: 0}, "slow");
    }
}

function showUpdateBookmarkDialog(requiresLogin, customId) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        var id = 'update-bookmark';
        if(typeof customId != 'undefined')
            id = customId;

        jQuery.blockUI({
            message: jQuery('#'+id),
            css: {width: 'auto', top: '5%', left: "0", position: "absolute", cursor: 'default'}
        });
        jQuery('.blockUI.blockMsg').center();
        jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
        jQuery(document).scrollTop(jQuery("#update-bookmark").offset().top);
        jQuery("html, body").animate({scrollTop: 0}, "slow");
    }
}

function removeBookmark(type) {
    jQuery("#updateBookmarkFrm > #task").val(type+".removeBookmark");
    jQuery("#updateBookmarkFrm").submit();
}

//************--End Front End Views--*****************//

