//************--Event Details Section--*****************//
function printEvent(eventId, url) {
    var winref = window.open(url + '&eventId=' + eventId, 'windowName', 'width=1050,height=700');
    if (window.print) winref.print();
}

function showCompanyListDialog() {
    jQuery.blockUI({
        message: jQuery('#company-list'),
        css: {width: 'auto', top: '5%', left: "0", position: "absolute"}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
    jQuery(document).scrollTop(jQuery("#company-list").offset().top);
    jQuery("html, body").animate({scrollTop: 0}, "slow");
}

function joinEvent(userId) {
    if (userId == 0) {
        showLoginNotice();
    } else {
        showCompanyListDialog();
    }
}

function associateCompanies(event_id) {
    var urlAssociateCompanies = url + '&task=event.associateCompaniesAjax';

    var eventId = event_id;
    var selectedValues = jQuery('#userAssociatedCompanies').val();
    var companyIds;
    if (Array.isArray(selectedValues))
        companyIds = selectedValues.join();
    else
        companyIds = -1;

    var successMessage = jQuery('#associated-companies-message').html();

    jQuery.ajax({
        type: "POST",
        url: urlAssociateCompanies,
        data: {companyIds: companyIds, eventId: eventId},
        dataType: 'json',
        success: function () {
            if (Array.isArray(selectedValues)) {
                jQuery.blockUI({
                    message: successMessage,
                    css: {backgroundColor: '#fff', height: 'auto'}
                });
                jQuery('.blockUI.blockMsg').center();
                jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
            }
            else {
                jQuery.unblockUI();
            }
        }
    });
}
//**********--End Event Details Section--*************//

//************--Event List Section--*****************//
function changeRadius(radius) {
    jQuery("#radius").val(radius);
    jQuery("#adminForm").submit();
}

function changeOrder(orderField) {
    jQuery("#orderBy").val(orderField);
    jQuery("#adminForm").submit();
}

function showList(listId) {
    if (listId == 1)
        showList_1();
    else if (listId == 2)
        showList_2();
}

function showList_1() {
    jQuery("#events-list-view").show();
    jQuery("#events-list-view-2").hide();
    jQuery("#events-calendar").hide();
    jQuery(".result-counter").show();
    jQuery(".pagination").show();
    jQuery(".search-toggles .sortby").show();
    jQuery(".search-toggles .orderBy").show();

    jQuery("#grid-view-link").removeClass("active");
    jQuery("#list-view-link_2").removeClass("active");
    jQuery("#list-view-link_1").addClass("active");
}
function showList_2() {
    jQuery("#events-list-view").hide();
    jQuery("#events-list-view-2").show();
    jQuery("#events-calendar").hide();
    jQuery(".result-counter").show();
    jQuery(".pagination").show();
    jQuery(".search-toggles .sortby").show();
    jQuery(".search-toggles .orderBy").show();

    jQuery("#grid-view-link").removeClass("active");
    jQuery("#list-view-link_1").removeClass("active");
    jQuery("#list-view-link_2").addClass("active");
}

function showGrid() {

    jQuery("#events-list-view").hide();
    jQuery("#events-list-view-2").hide();
    jQuery("#events-calendar").show();
    jQuery(".result-counter").hide();
    jQuery(".pagination").hide();
    jQuery(".search-toggles .sortby").hide();
    jQuery(".search-toggles .orderBy").hide();

    jQuery('#events-calendar').fullCalendar(calendarOptions);

    jQuery("#grid-view-link").addClass("active");
    jQuery("#list-view-link_1").removeClass("active");
    jQuery("#list-view-link_2").removeClass("active");
}

function chooseCategory(categoryId) {
    jQuery("#adminForm #categoryId").val(categoryId);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}
//************--End Event List Section--*****************//

//************--Manage Events Section--*****************//
function editEvent(eventId) {
    jQuery("#id").val(eventId);
    jQuery("#task").val("managecompanyevent.edit");
    jQuery("#adminForm").submit();
}

function addDirEvent() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyevent.add");
    jQuery("#adminForm").submit();
}

function deleteDirEvent(eventId) {
    jQuery("#id").val(eventId);
    //showDeleteDialog();

    if (confirm(Joomla.JText._('COM_JBUSINESS_DIRECTORY_EVENTS_CONFIRM_DELETE'))) {
        jQuery("#id").val(eventId);
        jQuery("#task").val("managecompanyevents.delete");
        jQuery("#adminForm").submit();
    }
}

function showDeleteDialog() {
    jQuery.blockUI({
        message: jQuery('#delete-event-dialog'),
        css: {width: 'auto', top: '10%', left: "0", position: "absolute"}
    });
    jQuery('.blockUI.blockMsg').center();
    jQuery('.blockOverlay').attr('title', 'Click to unblock').click(jQuery.unblockUI);
}

function deleteEvent() {
    jQuery("#delete_mode").val(1);
    Joomla.submitform('managecompanyevents.delete');
    jQuery.unblockUI();
}

function deleteAllFollowignEvents() {
    jQuery("#delete_mode").val(2);
    Joomla.submitform('managecompanyevents.delete');
    jQuery.unblockUI();
}

function deleteAllSeriesEvents() {
    jQuery("#delete_mode").val(3);
    Joomla.submitform('managecompanyevents.delete');
    jQuery.unblockUI();
}
//************--End Manage Events Section--*****************//

//************--Manage Events Appointments Section--*****************//
function selectAppointment() {
    jQuery("#adminForm").submit();
}

function deleteAppointment(appointmentId) {
    if (confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_EVENT_APPOINTMENT_CONFIRM_DELETE"))) {
        jQuery("#id").val(appointmentId);
        jQuery("#task").val("managecompanyeventappointments.delete");
        jQuery("#adminForm").submit();
    }
}

function confirmAppointment(appointmentId) {
    jQuery("#id").val(appointmentId);
    jQuery("#task").val("managecompanyeventappointments.confirm");
    jQuery("#adminForm").submit();
}

function denyAppointment(appointmentId) {
    jQuery("#id").val(appointmentId);
    jQuery("#task").val("managecompanyeventappointments.deny");
    jQuery("#adminForm").submit();
}
//************--End Manage Events Appointments Section--*****************//

//************--Manage Events Reservation Section--*****************//
function selectReservation() {
    jQuery("#adminForm").submit();
}

function deleteEventReservation(bookingId) {
    if (confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_EVENT_RESERVATION_CONFIRM_DELETE"))) {
        jQuery("#id").val(bookingId);
        jQuery("#task").val("managecompanyeventreservations.delete");
        jQuery("#ticketForm").submit();
    }
}
//*********--End Manage Events Reservation Section--*************//

//************--Manage Events Tickets Section--*****************//
function selectTicket() {
    jQuery("#adminForm").submit();
}

function addEventTicket() {
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyeventticket.add");
    jQuery("#adminForm").submit();
}

function deleteTicket(ticketId) {
    if (confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_EVENT_TICKET_CONFIRM_DELETE"))) {
        jQuery("#id").val(ticketId);
        jQuery("#task").val("managecompanyeventtickets.delete");
        jQuery("#ticketForm").submit();
    }
}

function duplicateTicket(ticketId) {
    jQuery("#id").val(ticketId);
    jQuery("#task").val("managecompanyeventticket.duplicate");
    jQuery("#ticketForm").submit();
}
//**********--End Manage Events Tickets Section--**************//