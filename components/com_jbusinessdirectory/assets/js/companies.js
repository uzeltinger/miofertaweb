jQuery(document).ready(function () {
    if (jQuery("#descriptionCounter").val())
        jQuery("#descriptionCounter").val(parseInt(jQuery("#description").attr('maxlength')) - jQuery("#description").val().length);
    if (jQuery("#descriptionCounterShort").val())
        jQuery("#descriptionCounterShort").val(parseInt(jQuery("#short_description").attr('maxlength')) - jQuery("#short_description").val().length);
});

//************--Associated Events Section--*****************//
function showAppointmentDialog() {
    jQuery.blockUI({
        message: jQuery('#event-appointment'),
        css: {width: 'auto', top: '5%', left: "0", position: "absolute", cursor: 'default'}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#event-appointment").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
    jQuery('.timepicker').timepicker({'timeFormat': jbdUtils.timeFormat, 'minTime': '6:00am'});
}

function makeAppointment(eventId, eventStartDate, eventEndDate) {
    showAppointmentDialog();
    jQuery('#eventId-appoint').val(eventId);
    listAvailableDates(eventStartDate, eventEndDate);
}

function listAvailableDates(eventStartDate, eventEndDate) {
    var dStart;
    var dEnd;

    if (eventStartDate.length === 0 || eventStartDate == null || eventStartDate === "0000-00-00")
        dStart = new Date();
    else
        dStart = new Date(eventStartDate);

    if (eventEndDate.length === 0 || eventEndDate == null || eventEndDate === "0000-00-00") {
        dEnd = new Date();
        dEnd.setDate(dStart.getDate() + 20);
    }
    else
        dEnd = new Date(eventEndDate);

    var dNow = new Date();

    if(dNow > dStart && dNow < dEnd)
        dStart = dNow;

    var select = document.getElementById("date-appoint");

    var i = 0;
    while (dStart <= dEnd && i <= 20) {
        var opt = document.createElement('option');
        opt.value = dStart.toDateString();
        opt.innerHTML = dStart.toDateString();
        select.appendChild(opt);

        dStart.setDate(dStart.getDate() + 1);
        i++;
    }
}
//************--End Associated Events Section--*************//

//************--Company Services Section--*****************//
function selectService(id) {
    serviceId = id;
    providerId = 0;

    wizard.steps("next");
}

function selectProvider(id) {
    providerId = id;
    jQuery('#morning').empty();
    jQuery('#afternoon').empty();
    jQuery('#evening').empty();

    wizard.steps("next");
}

function selectHour(hour) {
    selectedHour = hour;

    wizard.steps("finish");
}

function renderProviders() {
    var urlGetProviders = url + '&task=companies.getServiceProvidersAjax';

    jQuery.ajax({
        type: "POST",
        url: urlGetProviders,
        data: {serviceId: serviceId},
        dataType: 'json',
        success: function (data) {
            jQuery('#providers-content').html(data);
        }
    });
}

function getVacationDates() {
    var urlGetDays = url + '&task=companies.getVacationDaysAjax';

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
    var urlGetHours = url + '&task=companies.getAvailableHoursAjax';

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
//************--End Company Services Section--*************//

//************--Company Layouts Section--*****************//
function initTabs(tabId) {
    jQuery("#tabs").tabs();

    jQuery("#dir-tab-2").click(function () {
        loadMapScript();
    });

    jQuery("#dir-tab-4").click(function () {
        slider.refresh();
    });

    jQuery(".dir-tabs-options").click(function () {
        jQuery(".dir-tabs-options").each(function () {
            jQuery(this).removeClass("ui-state-active");
        });
        jQuery(this).addClass("ui-state-active");
    });

    jQuery("#dir-tab-" + tabId).click();
}

function showCompanyMap() {
    jQuery("#company-map-holder").show();
    loadMapScript();
}

function hideMap() {
    jQuery("#company-map-holder").hide();
}

function readMore() {
    jQuery("#general-info").removeClass("collapsed");
    jQuery(".read-more").hide();
}

function showDetails(identifier) {
    var ids = ["company-details", "company-gallery", "company-videos", "company-sounds", "company-offers", "company-related", "company-services", "company-events", "events-associated", "company-testimonials", "company-reviews", "company-price-list", "company-projects"];
    var pos = ids.indexOf(identifier);

    jQuery(".company-menu a").each(function () {
        jQuery(this).removeClass("active");
    });

    if(identifier === "company-projects") {
        returnToProjects();
    }

    var linkIdentifier = identifier.substring(identifier.indexOf("-")+1, identifier.length);
    jQuery("#" + linkIdentifier + "-link").addClass("active");

    for (var i = 0; i < pos; i++) {
        jQuery("#" + ids[i]).slideUp();
    }

    for (var i = pos; i < ids.length; i++) {
        jQuery("#" + ids[i]).slideDown();
    }
}

function showTabContent(identifier) {
    var ids = ["company-details", "company-gmap", "company-testimonials", "company-services", "company-reviews","company-price-list", "company-projects", "company-products"];

    jQuery(".company-menu a").each(function () {
        jQuery(this).removeClass("active");
    });

    var linkIdentifier = identifier.substring(identifier.indexOf("-")+1, identifier.length);
    jQuery("#" + linkIdentifier + "-link").addClass("active");

    jQuery("#" + identifier).show();
    for (var i = 0; i < ids.length; i++) {
       if(ids[i] !== identifier)
            jQuery("#" + ids[i]).hide();
    }

    if(identifier === "company-projects") {
        returnToProjects();
    }

    if(identifier === "company-gmap") {
        loadMapScript();
    }

    if(identifier === "company-products") {
        goBack();
    }

    if (identifier === "company-offers"){
        jQuery('.offers-container').removeAttr("style");
    }
}

function renderUserAverageRating(averageRating, companyId, showNotice) {
    jQuery(".user-rating-avg").raty({
        half: true,
        precision: false,
        size: 24,
        readOnly: true,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        hintList: [Joomla.JText._('LNG_BAD'), Joomla.JText._('LNG_POOR'), Joomla.JText._('LNG_REGULAR'), Joomla.JText._('LNG_GOOD'), Joomla.JText._('LNG_GORGEOUS')],
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        start: averageRating,
        path: jbdUtils.componentImagePath,
        click: function (score, evt) {
            if (showNotice == 1) {
                jQuery(this).raty('start', jQuery(this).attr('title'));
                showLoginNotice();
            }
            else {
                updateCompanyRate(companyId, score);
            }
        }
    });
}
//************--End Company Layouts Section--**************//

//************--Company Reviews Section--*****************//
function showReviewForm(requiresLogin) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        jQuery("#add-review").slideDown(500);
    }
}

function renderRatingCriteria(calculate_review_criterias, companyId) {
    jQuery('.rating-criteria').raty({
        half: true,
        precision: false,
        size: 24,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        hintList: [Joomla.JText._('LNG_BAD'), Joomla.JText._('LNG_POOR'), Joomla.JText._('LNG_REGULAR'), Joomla.JText._('LNG_GOOD'), Joomla.JText._('LNG_GORGEOUS')],
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        click: function (score, evt) {
            jQuery(this).parent().children("input").val(score);

            if (calculate_review_criterias == 1) {
                var total = 0;
                var count = 0;
                jQuery(".review-criterias").each(function () {
                    count++;
                    total += parseFloat(jQuery(this).val());
                });
                if (!isNaN(total)) {
                    score = total * 1.0 / count;
                }
            }
            updateCompanyRate(companyId, score);
        },
        start: 0,
        path: jbdUtils.componentImagePath
    });
}

function renderRatingQuestions() {
    jQuery('.rating-question').raty({
        number: 10,
        half: true,
        precision: false,
        size: 24,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        click: function (score, evt) {
            jQuery(this).parent().children("input").val(score);
            document.getElementById('review-question').value = score;
        },
        start: 0,
        path: jbdUtils.componentImagePath
    });
}
//************--End Company Reviews Section--***************//

//************--Review Questions Section--*****************//
function showReviewQuestions(reviewId) {
    var maxLength = 100;
    jQuery("#show-questions" + reviewId).text(Joomla.JText._('LNG_HIDE_REVIEW_QUESTIONS'));
    jQuery("#show-questions" + reviewId).attr('onclick', 'hideReviewQuestions("' + reviewId + '")');
    jQuery("#review-questions" + reviewId).slideDown(500);
    jQuery('#review-questions' + reviewId).children('.review-question-answer').each(function () {
        if (jQuery(this).hasClass('star-rating'))
            showStarRating(jQuery(this).attr('id'));
        else
            jQuery(this).html(truncate(jQuery(this).text(), jQuery(this).attr('id'), maxLength));
    });
}

function hideReviewQuestions(reviewId) {
    jQuery("#show-questions" + reviewId).text(Joomla.JText._('LNG_SHOW_REVIEW_QUESTIONS'));
    jQuery("#show-questions" + reviewId).attr('onclick', 'showReviewQuestions("' + reviewId + '")');
    jQuery("#review-questions" + reviewId).slideUp(500);
}

function showStarRating(answerId) {
    var id = answerId.slice(15, answerId.length);
    jQuery('#' + answerId).empty();
    jQuery('#' + answerId).raty({
        number: 10,
        half: true,
        precision: false,
        size: 24,
        readOnly: true,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        start: jQuery('#star-rating-score' + id).val(),
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        path: jbdUtils.componentImagePath
    });
}

function editAnswer(answerId, answerType) {
    var answerDiv = jQuery("#question-answer" + answerId);
    var answer = answerDiv.text();
    var data;
    var score;

    if (answerType == 0) {
        showFullText(answerId);
        answer = answerDiv.text();
        data = '<textarea style="width:100%;" name="answer-' + answerId + '" id="answer-' + answerId + '" onblur="saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" >' + answer + '</textarea>';
    }
    else if (answerType == 1) {
        var yes = answer == Joomla.JText._('LNG_YES') ? 'checked="checked"' : "";
        var no = answer == Joomla.JText._('LNG_NO') ? 'checked="checked"' : "";
        data = '<input type="radio" id="answer-' + answerId + '" value="1" onclick="saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" name="answer-' + answerId + '"' + yes + '>' + Joomla.JText._("LNG_YES") + '</input>';
        data += ' <input type="radio" id="answer-' + answerId + '" value="0" onclick="saveAnswer(\'' + answerId + '\', \'' + answerType + '\')" name="answer-' + answerId + '"' + no + '>' + Joomla.JText._("LNG_NO") + '</input>';
    }
    else if (answerType == 2) {
        data = '<div class="rating-answer"></div>';
        score = parseFloat(answer);
    }
    jQuery("#question-answer" + answerId).attr('class', '');
    answerDiv.html(data);

    if (answerType == 2) {
        jQuery('.rating-answer').raty({
            number: 10,
            half: true,
            precision: false,
            size: 24,
            starHalf: 'star-half.png',
            starOff: 'star-off.png',
            starOn: 'star-on.png',
            noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
            click: function (score) {
                jQuery(this).parent().children("input").val(score);
                document.getElementById('star-rating-score' + answerId).value = score;
                saveAnswer(answerId, answerType);
            },
            start: score,
            path: jbdUtils.componentImagePath
        });
    }
}

function saveAnswer(answerId, answerType) {
    var data;
    if (answerType == 0)
        data = jQuery("#answer-" + answerId).val();
    else if (answerType == 1)
        data = jQuery("input[name='answer-" + answerId + "']:checked").val();
    else if (answerType == 2)
        data = jQuery("#star-rating-score" + answerId).val();

    var urlSaveAnswerAjax = url + '&task=companies.saveAnswerAjax';
    jQuery.ajax({
        type: "POST",
        url: urlSaveAnswerAjax,
        data: {answer: data, answerId: answerId},
        dataType: 'json',
        success: function () {
            jQuery("#question-answer" + answerId).empty();
            if (answerType == 1) {
                if (data == 0)
                    data = Joomla.JText._('LNG_NO');
                else if (data == 1)
                    data = Joomla.JText._('LNG_YES');
            }
            if (answerType != 2)
                jQuery("#question-answer" + answerId).text(data);
            else {
                showStarRating('question-answer' + answerId);
            }
        }
    });
    if (answerType != 2)
        jQuery("#question-answer" + answerId).attr('class', 'answer question-answer');
    else
        jQuery("#question-answer" + answerId).attr('class', 'answer star-rating');
}

function truncate(text, id, limit) {
    var truncatedText;

    if (id.length > 10)
        id = id.slice(15, id.length);

    if (text.length <= limit) {
        return text;
    }
    else if (text.length > limit) {
        truncatedText = text.slice(0, limit) + '<span>...</span>';
        truncatedText += '<a href="javascript:void(0)" onClick=\'showFullText("' + id + '")\' class="more" id="more' + id + '">' + Joomla.JText._("LNG_READ_MORE") + '</a>';
        truncatedText += '<span style="display:none;" id="more-text">' + text.slice(limit, text.length) + '</span>';

        return truncatedText;
    }
}

function showFullText(id) {
    jQuery('#more' + id).next().show();
    jQuery('#more' + id).prev().remove();
    jQuery('#more' + id).remove();
}
//********--End Review Questions Section--*********//

//************--Gallery Section--*****************//
function magnifyImages(htmlClass) {
    jQuery('.' + htmlClass).magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 2] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
                return item.el.attr('title');
            }
        }
    });
}
//************--End Gallery Section--*******************//

//************--Company Util Section--*****************//
function printCompany(companyId, url) {
    var winref = window.open(url + '&companyId=' + companyId + '&layout=print', 'windowName', 'width=1050,height=700');
    if (window.print) winref.print();
}
function renderAverageRating(averageRating) {
    jQuery('#rating-average').raty({
        half: true,
        precision: false,
        size: 24,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        readOnly: true,
        hintList: [Joomla.JText._('LNG_BAD'), Joomla.JText._('LNG_POOR'), Joomla.JText._('LNG_REGULAR'), Joomla.JText._('LNG_GOOD'), Joomla.JText._('LNG_GORGEOUS')],
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        start: averageRating,
        path: jbdUtils.componentImagePath
    });
}

function renderUserRating(start, showNotice, companyId) {
    jQuery('#rating-user').raty({
        half: true,
        precision: false,
        size: 24,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        hintList: [Joomla.JText._('LNG_BAD'), Joomla.JText._('LNG_POOR'), Joomla.JText._('LNG_REGULAR'), Joomla.JText._('LNG_GOOD'), Joomla.JText._('LNG_GORGEOUS')],
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        start: start,
        path: jbdUtils.componentImagePath,
        click: function (score, evt) {
            if (showNotice == 1) {
                jQuery(this).raty('start', jQuery(this).attr('title'));
                showLoginNotice();
            }
            else {
                updateCompanyRate(companyId, score);
            }
        }
    });
}

function renderReviewRating() {
    jQuery('.rating-review').raty({
        half: true,
        size: 24,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        hintList: [Joomla.JText._('LNG_BAD'), Joomla.JText._('LNG_POOR'), Joomla.JText._('LNG_REGULAR'), Joomla.JText._('LNG_GOOD'), Joomla.JText._('LNG_GORGEOUS')],
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        start: function () {
            return jQuery(this).attr('title')
        },
        path: jbdUtils.componentImagePath,
        readOnly: true
    });
}

function showTab(tabId) {
    jQuery("#tabId").val(tabId);
    jQuery("#tabsForm").submit();
}

function claimCompany(requiresLogin) {
    if (requiresLogin) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        showClaimDialog();
    }
}

function showClaimDialog() {
    jQuery.blockUI({
        message: jQuery('#company-claim'),
        css: {width: 'auto', top: '5%', left: "0", position: "absolute", cursor: 'default'}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#company-claim").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}

function showDirTab(tab) {
    jQuery(".dir-tab").each(function () {
        jQuery(this).hide();
    });

    jQuery(tab).show();
    jQuery(".track-business-details").each(function () {
        jQuery(this).parent().removeClass("active");
    });

    var number = tab.substr(tab.indexOf("-") + 1, tab.length);
    jQuery("#dir-tab-" + number).parent().addClass("active");

    if (tab === "#tabs-15"){
        applyIsotope();
    }

    if (tab === "#tabs-17"){
        returnToProjects();
    }

}

function updateCompanyOwner(companyId, userId) {
    jQuery.blockUI({
        message: '<span class="loading-message"> Please wait...</span>',
        css: {
            border: 'none',
            padding: '15px',
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius': '10px',
            opacity: .6,
            color: '#fff'
        }
    });

    var form = document.reportAbuse;
    var postParameters = '';
    postParameters += "&companyId=" + companyId;
    postParameters += "&userId=" + userId;
    var postData = '&controller=companies&task=companies.updateCompanyOwner' + postParameters;
    jQuery.post(jbdUtils.baseUrl, postData, processUpdateCompanyOwner);
}

function processUpdateCompanyOwner(responce) {
    var xml = responce;
    jQuery(xml).find('answer').each(function () {
        var message = '';
        if (jQuery(this).attr('result') == true) {
            message = Joomla.JText._('LNG_CLAIM_SUCCESSFULLY');
            jQuery("#claim-container").hide();
        } else {
            message = Joomla.JText._('LNG_ERROR_CLAIMING_COMPANY');
            //alert('notsaved');
        }
        jQuery.blockUI({
            message: '<span class="loading-message">' + message + '</span>',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .6,
                color: '#fff'
            }
        });
        setTimeout(jQuery.unblockUI, 1500);
    });
}

function showReportAbuse() {
    jQuery.blockUI({
        message: jQuery('#reportAbuseEmail'),
        css: {width: 'auto', top: '5%', left: "0", position: "absolute"}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#reportAbuseEmail").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}

//************--End Company Util Section--*****************//

//************--Companies/Search Section--*****************//
function renderGridReviewRating(id) {
    jQuery('.rating-review-' + id).raty({
        half: true,
        size: 24,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        hintList: [Joomla.JText._('LNG_BAD'), Joomla.JText._('LNG_POOR'), Joomla.JText._('LNG_REGULAR'), Joomla.JText._('LNG_GOOD'), Joomla.JText._('LNG_GORGEOUS')],
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        start: function () {
            return jQuery(this).attr('title')
        },
        path: jbdUtils.siteRoot + 'components/com_jbusinessdirectory/assets/images/',
        readOnly: true
    });
}

function showQuoteCompanyForm(companyId) {
    jQuery("#company-quote #companyId").val(companyId);
    jQuery.blockUI({
        message: jQuery('#company-quote'),
        css: {width: 'auto', top: '10%', left: "0", position: "absolute"}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#company-quote").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}

function showQuoteCompany(companyId,showData) {
    if (showData == 0) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        showQuoteCompanyForm(companyId);
    }
}


function showContactCompanyList(companyId,showData) {
    if (showData == 0) {
        showLoginNotice();
    } else {
        jQuery(".error_msg").each(function () {
            jQuery(this).hide();
        });
        jQuery("#company-contact #companyId").val(companyId);
        showContactCompany();
    }
}

function requestQuoteCompany(baseurl) {
    var isError = jQuery("#quoteCompanyFrm").validationEngine('validate');
    if (!isError)
        return;

    var postData = "";
    postData += "&firstName=" + jQuery("#company-quote #firstName-quote").val();
    postData += "&lastName=" + jQuery("#company-quote #lastName-quote").val();
    postData += "&email=" + jQuery("#company-quote #email-quote").val();
    postData += "&description=" + jQuery("#company-quote #description-quote").val();
    postData += "&companyId=" + jQuery("#company-quote #companyId").val();
    postData += "&category=" + jQuery("#company-quote #category").val();
    postData += "&recaptcha_response_field=" + jQuery("#company-quote #recaptcha_response_field").val();
    postData += "&g-recaptcha-response=" + jQuery("#company-quote #g-recaptcha-response-1").val();

    jQuery.post(baseurl, postData, processContactCompanyResult);
}

function contactCompanyList() {
    var isError = jQuery("#contactCompanyFrm").validationEngine('validate');
    if (!isError)
        return;

    var postData = "";
    postData += "&firstName=" + jQuery("#company-contact #firstName").val();
    postData += "&lastName=" + jQuery("#company-contact #lastName").val();
    postData += "&email=" + jQuery("#company-contact #email").val();
    postData += "&description=" + jQuery("#company-contact #description").val();
    postData += "&companyId=" + jQuery("#company-contact #companyId").val();
    postData += "&recaptcha_response_field=" + jQuery("#captcha-div-contact #recaptcha_response_field").val();
    postData += "&g-recaptcha-response=" + jQuery("#captcha-div-contact #g-recaptcha-response").val();

    jQuery.post(contactListUrl, postData, processContactCompanyResult);
}

function processContactCompanyResult(responce) {
    var xml = responce;
    jQuery(xml).find('answer').each(function () {
        if (jQuery(this).attr('error') == '1') {
            jQuery.blockUI({
                message: '<strong>' + Joomla.JText._("COM_JBUSINESS_ERROR") + '</strong><br/><br/><p>' + jQuery(this).attr('errorMessage') + '</p>'
            });
            setTimeout(jQuery.unblockUI, 2000);
        } else {
            jQuery.blockUI({
                message: '<h3>' + Joomla.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_CONTACTED") + '</h3>'
            });
            setTimeout(jQuery.unblockUI, 2000);
        }
    });
}

function renderSearchAverageRating() {
    jQuery('.rating-average').raty({
        half: true,
        precision: false,
        size: 24,
        readOnly:true,
        starHalf: 'star-half.png',
        starOff: 'star-off.png',
        starOn: 'star-on.png',
        hintList: [Joomla.JText._('LNG_BAD'), Joomla.JText._('LNG_POOR'), Joomla.JText._('LNG_REGULAR'), Joomla.JText._('LNG_GOOD'), Joomla.JText._('LNG_GORGEOUS')],
        noRatedMsg: Joomla.JText._('LNG_NOT_RATED_YET'),
        start: function () {
            return jQuery(this).attr('title')
        },
        path: jbdUtils.componentImagePath
    });
}
var issetCategory = false;
var cat_id;

function setCategoryStatus(status, categoryId) {
    issetCategory = status;
    cat_id = categoryId;
}

function saveSelectedCategory() {
    var catId;
    var checked = jQuery("#filterCategoryItems input[type='checkbox']:checked");
    catId = checked.attr('id');

    if (issetCategory)
        catId = cat_id;

    jQuery("#adminForm #categoryId").val(catId);
    jQuery("#adminForm input[name=limitstart]").val(0);
}

function changeOrder(orderField) {
    jQuery("#orderBy").val(orderField);
    jQuery("#adminForm").submit();
}

function showList() {
    jQuery("#results-container").show();
    jQuery("#jbd-grid-view").hide();

    jQuery("#grid-view-link").removeClass("active");
    jQuery("#list-view-link").addClass("active");
}

function showGrid() {
    jQuery("#results-container").hide();
    jQuery("#jbd-grid-view").show();
    applyIsotope();
    jQuery(window).resize();

    jQuery("#grid-view-link").addClass("active");
    jQuery("#list-view-link").removeClass("active");
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

function addFilterRule(type, id) {
    var val = type + '=' + id + ';';
    if (jQuery("#selectedParams").val().length > 0) {
        jQuery("#selectedParams").val(jQuery("#selectedParams").val() + val);
    } else {
        jQuery("#selectedParams").val(val);
    }
    if (!issetCategory) {
        jQuery("#filter_active").val("1");
    }
    jQuery("#adminForm input[name=limitstart]").val(0);
    saveSelectedCategory();
    jQuery("#adminForm").submit();
}

function removeFilterRule(type, id) {
    var val = type + '=' + id + ';';
    var str = jQuery("#selectedParams").val();
    jQuery("#selectedParams").val((str.replace(val, "")));
    jQuery("#filter_active").val("1");
    saveSelectedCategory();

    if (type == "city")
        jQuery("#adminForm #city-search").val("");
    if (type == "region")
        jQuery("#adminForm #region-search").val("");
    if (type == "country")
        jQuery("#adminForm #country-search").val("");
    if (type == "type")
        jQuery("#adminForm #type-search").val("");
    if (type == "province")
        jQuery("#adminForm #province-search").val("");

    jQuery("#adminForm").submit();

}

function resetFilters(resetCategories) {
    jQuery("#selectedParams").val("");
    if (resetCategories)
        jQuery("#categories-filter").val("");
    else
        saveSelectedCategory();
    jQuery("#adminForm #categoryId").val("");

    jQuery("#adminForm #searchkeyword").val("");
    jQuery("#adminForm #zipcode").val("");
    jQuery("#adminForm #city-search").val("");
    jQuery("#adminForm #region-search").val("");
    jQuery("#adminForm #country-search").val("");
    jQuery("#adminForm #type-search").val("");
    jQuery("#adminForm #province-search").val("");
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
    
    jQuery("#categorySearch").val("");
    
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

function setRadius(radius) {
    jQuery("#adminForm > #radius").val(radius);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
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

function filterByFavorites(requiresLogin) {
    if (!requiresLogin) {
        jQuery("#adminForm #filter-by-fav").val("1");
        jQuery("#adminForm").submit();
    } else {
        showLoginNotice();
    }
}

function collapseSearchFilter() {
    //searchFilter collapse
    var headers = ["H1", "H2", "H3", "H4", "H5", "H6"];

    jQuery(".accordionCollapse").click(function (e) {
        var target = e.target,
            name = target.nodeName.toUpperCase();

        if (jQuery.inArray(name, headers) > -1) {
            var subItem = jQuery(target).next();

            //slideUp all elements (except target) at current depth or greater
            var depth = jQuery(subItem).parents().length;
            var allAtDepth = jQuery(".accordion li, .accordion div").filter(function () {
                if (jQuery(this).parents().length >= depth && this !== subItem.get(0)) {
                    return true;
                }
            });
            jQuery(allAtDepth).slideUp("fast");

            //slideToggle target content and adjust bottom border if necessary
            subItem.slideToggle("fast", function () {
                jQuery(".accordionCollapse :visible:last").css("border-radius", "0 0 10px 10px");
            });
            jQuery(target).css({"border-bottom-right-radius": "0", "border-bottom-left-radius": "0"});
        }
    });
}
//************--End Companies/Search Section--**************//

//************--Manage Companies Section--*****************//
function deleteDirListing(id) {
    if (confirm(Joomla.JText._('COM_JBUSINESS_DIRECTORY_COMPANIES_CONFIRM_DELETE'))) {
        jQuery("#cid").val(id);
        jQuery("#task").val("managecompanies.delete");
        jQuery("#adminForm").submit();
    }
}
//***********--End Manage Companies Section--***************//

//************--Manage Company Messages Section--**********//
function deleteMessage(id) {
    if (confirm(Joomla.JText._('COM_JBUSINESS_DIRECTORY_COMPANY_MESSAGE_CONFIRM_DELETE'))) {
        jQuery("#id").val(id);
        jQuery("#task").val("managecompanymessages.delete");
        jQuery("#adminForm").submit();
    }
}
//***********--End Manage Messages Section--**************//

//***********--Manage Company Services Section--*********//
function addService() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyservice.add");
    jQuery("#adminForm").submit();
}

function deleteService(serviceId) {
    if (confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_SERVICE_CONFIRM_DELETE"))) {
        jQuery("#id").val(serviceId);
        jQuery("#task").val("managecompanyservices.delete");
        jQuery("#adminForm").submit();
    }
}

function duplicateService(serviceId) {
    jQuery("#id").val(serviceId);
    jQuery("#task").val("managecompanyservice.duplicate");
    jQuery("#adminForm").submit();
}
//***********--End Manage Company Services Section--************************//

//************--Manage Company Service Providers Section--*****************//
function selectServiceProviders() {
    jQuery("#adminForm").submit();
}

function addServiceProvider() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyserviceprovider.add");
    jQuery("#adminForm").submit();
}

function deleteServiceProvider(serviceId) {
    if (confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_COMPANY_SERVICE_PROVIDER_CONFIRM_DELETE"))) {
        jQuery("#id").val(serviceId);
        jQuery("#task").val("managecompanyserviceproviders.delete");
        jQuery("#adminForm").submit();
    }
}
//************--End Manage Company Service Providers Section--****************//

//************--Manage Company Service Reservations Section--****************//
function selectReservation() {
    jQuery("#adminForm").submit();
}

function deleteReservation(bookingId) {
    if (confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_SERVICE_RESERVATION_CONFIRM_DELETE"))) {
        jQuery("#id").val(bookingId);
        jQuery("#task").val("managecompanyservicereservations.delete");
        jQuery("#adminForm").submit();
    }
}
//************--End Manage Company Service Reservations Section--**************//

//************--Manage Project Section--*****************//
function editProject(projectId){
    jQuery("#id").val(projectId);
    jQuery("#task").val("managecompanyproject.edit");
    jQuery("#adminForm").submit();
}

function addProject(){
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyproject.add");
    jQuery("#adminForm").submit();
}

function deleteProject(projectId){
    if(confirm(Joomla.JText._('COM_JBUSINESS_DIRECTORY_PROJECT_CONFIRM_DELETE'))){
        jQuery("#id").val(projectId);
        jQuery("#task").val("managecompanyprojects.delete");
        jQuery("#adminForm").submit();
    }
}

function showProjectDetail(project) {

    var baseUrl = jbdUtils.siteRoot + 'index.php?option=com_jbusinessdirectory&task=companies.getProjectDetailsAjax';
    baseUrl = baseUrl + "&projectId=" + project;
    jQuery.ajax({
        type: "POST",
        url: baseUrl,
        dataType: 'json',
        success: function (data) {
            jQuery('#project-name').html(data.name);
            jQuery('#project-name-link').html(data.breadCrumbsName);
            jQuery('#project-description').html(data.description);
            jQuery('#project-gallery').html(data.projectGalleryImages);
            if (data.nrPhotos === 0){
                jQuery('#project-image-container').css("display","none");
            }else{
                jQuery('#project-image-container').css("display","");
            }
            jQuery("#company-projects-container").hide(500);
            jQuery("#project-details").show(500);
            applyLighSlider();
        }
    });
}

function applyLighSlider(){
    setTimeout(function() {
        jQuery('#projectImageGallery').unitegallery({
            gallery_theme: "default",
            gallery_height:550,
        	theme_enable_text_panel: true,	
            slider_control_zoom: false,
            slider_enable_zoom_panel: false,		
            thumb_fixed_size: false
        });
    }, 2000);
}

function returnToProjects(){
    jQuery("#project-details").hide(500);
    jQuery("#company-projects-container").show(500);
}

//***************--End Manage Project Section--******************//