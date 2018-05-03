//************--Offer Details Section--*****************//
function printOffer(offerId, offerUrl) {
    var winref = window.open(offerUrl + '&offerId=' + offerId, 'windowName', 'width=1050,height=700');
    if (window.print) winref.print();
}

function addToCart(offerId) {
    var urlAddToCart = jbdUtils.url + '&task=cart.addToCartAjax';
    var quantity = jQuery('#quantity').val();
    var cartDialogHtml = jQuery('#cart-dialog').html();

    if (quantity == 0) {
        alert(Joomla.JText._('LNG_PLEASE_SELECT_QUANTITY'));
        return;
    }

    jQuery.blockUI({
        message: '<h3 style="color:#000">' + Joomla.JText._('LNG_ADDING_PRODUCT_TO_SHOPPING_CART') + '</h3>',
        css: {backgroundColor: '#fff', height: '70px'}
    });
    jQuery.ajax({
        type: "POST",
        url: urlAddToCart,
        data: {offerId: offerId, quantity: quantity},
        dataType: 'json',
        success: function () {
            jQuery.blockUI({
                message: cartDialogHtml,
                css: {backgroundColor: '#fff', height: 'auto'}
            });
        }
    });
}

function renderOfferRatingCriteria(imagePath) {
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
        },
        start: 0,
        path: imagePath
    });
}

function renderOfferReviews(imagePath) {
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
        path: imagePath,
        readOnly: true
    });
}
//***********--End Offer Details Section--************//

//************--Offer List Section--*****************//
function changeRadius(radius){
    jQuery("#radius").val(radius);
    jQuery("#adminForm").submit();
}

function changeOrder(orderField){
    jQuery("#orderBy").val(orderField);
    jQuery("#adminForm").submit();
}

function showList(){
    jQuery("#offer-list-view").show();
    jQuery("#layout").hide();

    jQuery("#grid-view-link").removeClass("active");
    jQuery("#list-view-link").addClass("active");
}

function showGrid(){
    jQuery("#offer-list-view").hide();
    jQuery("#layout").show();
    applyIsotope();

    jQuery("#grid-view-link").addClass("active");
    jQuery("#list-view-link").removeClass("active");
}

function chooseCategory(categoryId) {
    jQuery("#adminForm #categoryId").val(categoryId);
    jQuery("#adminForm input[name=limitstart]").val(0);
    jQuery("#adminForm").submit();
}
//************--End Offer List Section--*****************//

//************--Manage Offers Section--*****************//
function editOffer(offerId){
    jQuery("#id").val(offerId);
    jQuery("#task").val("managecompanyoffer.edit");
    jQuery("#adminForm").submit();
}

function addOffer(){
    jQuery("#id").val(0);
    jQuery("#task").val("managecompanyoffer.add");
    jQuery("#adminForm").submit();
}

function deleteOffer(offerId){
    if(confirm(Joomla.JText._('COM_JBUSINESS_DIRECTORY_OFFERS_CONFIRM_DELETE'))){
        jQuery("#id").val(offerId);
        jQuery("#task").val("managecompanyoffers.delete");
        jQuery("#adminForm").submit();
    }
}
//***************--End Manage Offers Section--******************//

//************--Manage Offer Orders Section--******************//
function deleteOrder(orderId) {
    if(confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_OFFER_ORDER_CONFIRM_DELETE"))) {
        jQuery("#id").val(orderId);
        jQuery("#task").val("managecompanyofferorders.delete");
        jQuery("#adminForm").submit();
    }
}
//************--End Manage Offer Orders Section--*****************//

//************--Manage Offer Coupons Section--********************//
function deleteCoupon(couponId) {
    if(confirm(Joomla.JText._("COM_JBUSINESS_DIRECTORY_COUPONS_CONFIRM_DELETE", true))) {
        jQuery("#id").val(couponId);
        jQuery("#task").val("managecompanyoffercoupons.delete");
        jQuery("#adminForm").submit();
    }
}
//************--End Manage Offer Coupons Section--****************//