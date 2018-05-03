function saveReview(formId) {

	var form_id = '#'+formId;
	var isError = jQuery(form_id).validationEngine('validate');
	if(!isError)
		return;

	document.getElementById(formId).submit();
}

function cancelSubmitReview(){
	jQuery("#add-review").slideUp(500);
}

function addNewReview(requiresLogin){
	if(requiresLogin){
		showLoginNotice();
	}else{
		showDetails('company-reviews');
		window.location.hash = '#reviews';
		jQuery("#add-review").slideDown(500);
	}
}

function addNewReviewOnTabs(requiresLogin){
    if(requiresLogin){
        showLoginNotice();
    }else{
        showDirTab("#tabs-3");
        window.location.hash = '#reviews';
        jQuery("#add-review").slideDown(500);
        showTabContent("company-reviews");
    }
}

function reportReviewAbuse(reviewId){
	var form = document.getElementById('reportAbuse');
	form.elements["reviewId"].value=reviewId;
	jQuery.blockUI({ message: jQuery('#report-abuse'), css: {width: 'auto',top: '5%', left:"0", position:"absolute",cursor:'default'} }); 
	jQuery('.blockUI.blockMsg').center();
	jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI); 
}

function respondToReview(reviewId){
	var form = document.reviewResponseFrm;
	form.elements["reviewId"].value=reviewId;
	jQuery.blockUI({ message: jQuery('#new-review-response'), css: {width: 'auto',top: '5%', left:"0", position:"absolute",cursor:'default'} }); 
	jQuery('.blockUI.blockMsg').center();
	jQuery('.blockOverlay').attr('title','Click to unblock').click(jQuery.unblockUI); 
}

function saveReviewAbuse(){
	if(!validateReportAbuseForm()){
		return;
	}
	
	var form = document.reportAbuse;
	form.submit();
}

function saveReviewResponse(){
	//alert('save');
	
	if(!validateReviewResponseForm()){
		return;
	}
	var form = document.reviewResponseFrm;
	form.submit();
}

function closeDialog(){
	jQuery.unblockUI();
}

function increaseReviewLikeCount(reviewId){
	var postParameters='';
	postParameters +="&reviewId=" + reviewId;
	var postData='&task=companies.increaseReviewLikeCount&view=companies'+postParameters;
	jQuery.post(jbdUtils.baseUrl, postData, processIncreaseLikeResult);
}

function increaseOfferReviewLikeCount(reviewId){
	var postParameters='';
	postParameters +="&reviewId=" + reviewId;
	var postData='&task=offer.increaseReviewLikeCount&view=offers'+postParameters;
	jQuery.post(jbdUtils.baseUrl, postData, processIncreaseLikeResult);
}

function processIncreaseLikeResult(responce){
	var xml = responce;
	//alert(xml);
	//jQuery('#frmFacilitiesFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if(jQuery(this).attr('result')==true){
			jQuery("#like"+jQuery(this).attr('reviewId')).text(parseInt(jQuery("#like"+jQuery(this).attr('reviewId')).text())+1);
			saveCookieLikeId(jQuery(this).attr('reviewId'));
			jQuery("#like"+jQuery(this).attr('reviewId')).parent().parent().children().attr('onclick','');
			jQuery("#like"+jQuery(this).attr('reviewId')).parent().parent().addClass('reduceOpacity');

		}else{
			//alert('notsaved');
		}
	});
}

function saveCookieLikeId(reviewId){
	var ids= getCookie("likeIds");
	if(ids==undefined)
		ids='';
	ids+=','+reviewId;
	setCookie("likeIds", ids, 60);
	//alert(ids);
}

function increaseReviewDislikeCount(reviewId){
	var postParameters='';
	postParameters +="&reviewId=" + reviewId;
	var postData='&task=companies.increaseReviewDislikeCount&view=companies'+postParameters;
	jQuery.post(jbdUtils.baseUrl, postData, processIncreaseDislikeResult);
}

function increaseOfferReviewDislikeCount(reviewId){
	var postParameters='';
	postParameters +="&reviewId=" + reviewId;
	var postData='&task=offer.increaseReviewDislikeCount&view=offers'+postParameters;
	jQuery.post(jbdUtils.baseUrl, postData, processIncreaseDislikeResult);
}

function processIncreaseDislikeResult(responce){
	var xml = responce;
	//alert(xml);
	//jQuery('#frmFacilitiesFormSubmitWait').hide();
	jQuery(xml).find('answer').each(function()
	{
		if(jQuery(this).attr('result')==true){
			jQuery("#dislike"+jQuery(this).attr('reviewId')).text(parseInt(jQuery("#dislike"+jQuery(this).attr('reviewId')).text())+1);
			saveCookieDislikeId(jQuery(this).attr('reviewId'));
			jQuery("#dislike"+jQuery(this).attr('reviewId')).parent().parent().children().attr('onclick','');
			jQuery("#dislike"+jQuery(this).attr('reviewId')).parent().parent().addClass('reduceOpacity');
		}else{
			//alert('notsaved');
		}
	});
}

function saveCookieDislikeId(reviewId){
	var ids= getCookie("dislikeIds");
	if(ids==undefined)
		ids='';
	ids+=','+reviewId;
	setCookie("dislikeIds", ids, 60);
	//alert(ids);
}


function checkLikeStatus(){
	var ids= getCookie("likeIds");
	if(ids==undefined)
		ids='';
	ids = ids.split(',');
	//alert(ids);
	for(var i=0;i<ids.length;i++){
		jQuery("#like"+ids[i]).parent().parent().children('a:first-child').attr('onclick','');
		jQuery("#like"+ids[i]).parent().parent().addClass('reduceOpacity');
	}
}

function checkDislikeStatus(){
	var ids= getCookie("dislikeIds");
	if(ids==undefined)
		ids='';
	ids = ids.split(',');
	for(var i=0;i<ids.length;i++){
		jQuery("#dislike"+ids[i]).parent().parent().children('a:first-child').attr('onclick','');
		jQuery("#dislike"+ids[i]).parent().parent().addClass('reduceOpacity');
	}
}


function validateReportAbuseForm(){

	var form = document.reportAbuse;
	var isError = false;
	
	jQuery(".error_msg").each(function(){
		jQuery(this).hide();
	});
	
	if( !validateField( form.elements['email'], 'email', false, null ) ){
		jQuery("#reportAbuse #frmEmail_error_msg").show();
		if(!isError)
			jQuery("#reportAbuse #email").focus();
		isError = true;
	}

	if( !validateField( form.elements['description'], 'string', false, null ) ){
		jQuery("#reportAbuse #frmDescription_error_msg").show();
		if(!isError)
			jQuery("#reportAbuse #frmDescription_error_msg").focus();
		isError = true;
	}

	return !isError;
}

function validateReviewResponseForm(){

	var form = document.reviewResponseFrm;
	var isError = false;
	
	jQuery(".error_msg").each(function(){
		jQuery(this).hide();
	});
	
	if( !validateField( form.elements['firstName'], 'string', false, null ) ){
		jQuery("#reviewResponseFrm #frmFirstName_error_msg").show();
		if(!isError)
			jQuery("#firstName").focus();
		isError = true;
	}

	if( !validateField( form.elements['lastName'], 'string', false, null ) ){
		jQuery(" #reviewResponseFrm #frmLastName_error_msg").show();
		if(!isError)
			jQuery("#lastName").focus();
		isError = true;
	}
	
	if( !validateField( form.elements['email'], 'email', false, null ) ){
		jQuery("#reviewResponseFrm #frmEmail_error_msg").show();
		if(!isError)
			jQuery("#email").focus();
		isError = true;
	}
	
	if( !validateField( form.elements['response'], 'string', false, null ) ){
		jQuery("#reviewResponseFrm #frmDescription_error_msg").show();
		if(!isError)
			jQuery("#reviewResponseFrm #frmDescription_error_msg").focus();
		isError = true;
	}
	

	return !isError;
}


jQuery(document).ready(function(){
	checkLikeStatus();
	checkDislikeStatus();
});
	