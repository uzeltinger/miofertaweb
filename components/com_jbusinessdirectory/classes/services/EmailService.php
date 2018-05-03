<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

class EmailService{
	
	public static function sendPaymentEmail($company, $paymentDetails){
	
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$billingInformation = self::getBillingInformation($company);
		
		$templ = self::getEmailTemplate("Order Email");
		if(empty($templ))
			return false;
		
		$content = self::prepareEmail($paymentDetails, $company, $templ->email_content, $applicationSettings->company_name, $billingInformation, $applicationSettings->vat);
		$content = self::updateCompanyDetails($content);
		
		$subject = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_subject);
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendPaymentDetailsEmail($company, $paymentDetails){

		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$billingInformation = self::getBillingInformation($company);
	
		$templ = self::getEmailTemplate("Payment Details Email");
		if(empty($templ))
			return false;
		
		$content = self::prepareEmail($paymentDetails, $company, $templ->email_content, $applicationSettings->company_name, $billingInformation, $applicationSettings->vat);
		$content = str_replace(EMAIL_PAYMENT_DETAILS, $paymentDetails->details->details, $content);
		$content = self::updateCompanyDetails($content);
	
		$subject = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_subject);
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		
		$result = self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
		
		
		return $result;
	}
	
	public static function sendNewCompanyNotificationEmailToAdmin($company){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$templ = self::getEmailTemplate("New Company Notification Email");
		if(empty($templ))
			return false;
		
		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		
		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;

		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendNewCompanyNotificationEmailToOwner($company){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$templ = self::getEmailTemplate("Listing Creation Notification");
		if(empty($templ))
			return false;
		
		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		
		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
	
		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * Send statistics of each company
	 * @param $company
	 * @return bool|int|JException
	 */
	public static function sendStatisticsNotificationEmail($company){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$eventDetails = "";
		$offerDetails = "";
		$reviewDetails = "";

		$templ = self::getEmailTemplate("Business Statistics Email");
		if(empty($templ))
			return false;

		if (empty($company->events)){
			$eventDetails .= JText::_("LNG_NO_EVENTS_THIS_MONTH");
		}else {
			foreach ($company->events as $event) {
				if (!empty($event->picture_path)) {
					$eventDetails .= '<img height="111" style="width:165px" src="'.(JURI::root().PICTURES_PATH.$event->picture_path).'"/>';
				}else{
					$eventDetails .= '<img height="111" style="width:165px" src="'.(JURI::root().PICTURES_PATH.'/no_image.jpg').'"/>';
				}
				$eventDetails .= '<br/>';
                $eventDetails .= '<a title="'.$event->name.'" href="'.JBusinessUtil::getEventLink($event->id, $event->alias).'" >'.$event->name.'</a>';
				$eventDetails .= '<br/>';
				$eventDetails .= JText::_("LNG_TYPE").": " . $event->eventType;
				$eventDetails .= '<br/>';
				$eventDetails .= JText::_("LNG_VISITED").": " . $event->view_count ." ". JText::_("LNG_TIMES");
				$eventDetails .= '<hr />';
				$eventDetails .= '<br/><br/>';
			}
		}

		if (empty($company->offers)){
			$offerDetails .= JText::_("LNG_NO_OFFERS_THIS_MONTH");
		}else {
			foreach ($company->offers as $offer) {
				if (!empty($offer->picture_path)) {
					$offerDetails .= '<img height="111" style="width:165px" src="'.(JURI::root().PICTURES_PATH.$offer->picture_path).'"/>';
				}else{
					$offerDetails .= '<img height="111" style="width:165px" src="'.(JURI::root().PICTURES_PATH.'/no_image.jpg').'"/>';
				}
				$offerDetails .= '<br/>';
                $offerDetails .= '<a title="'.$offer->subject.'" href="'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'" >'.$offer->subject.'</a>';
				$offerDetails .= '<br/>';
				$offerDetails .= JText::_("LNG_VISITED").": " . $offer->viewCount ." ". JText::_("LNG_TIMES");
				$offerDetails .= '<hr />';
				$offerDetails .= '<br/><br/>';
			}
		}

		if (empty($company->reviews)){
			$reviewDetails .= JText::_("LNG_NO_REVIEW_THIS_MONTH");
		}else{
			foreach ($company->reviews as $review) {
				$reviewDetails .= $review->subject.'<br/>';
				$reviewDetails .= $review->description.'<br/>';
				$reviewDetails .= JText::_("LNG_LIKES")." : ".$review->likeCount . '<br/>';
				$reviewDetails .= JText::_("LNG_DISLIKES")." : ". $review->dislikeCount . '<br/>';
				$reviewDetails .= JText::_("LNG_RATING")." : ". $review->rating . '<br/>';
				$reviewDetails .= '<hr />';
				$reviewDetails .= '<br/>';
			}
		}

		$reviewNumber = count($company->reviews);

		$content = self::prepareNotificationEmail($company, $templ->email_content);

		$content = str_replace(BUSINESS_VIEW_COUNT, $company->viewCount, $content);
		$content = str_replace(BUSINESS_RATING, $company->review_score, $content);
		$content = str_replace(BUSINESS_REVIEW_NUMBER, $reviewNumber, $content);
		$content = str_replace(EVENTS_DETAILS, $eventDetails, $content);
		$content = str_replace(OFFER_DETAILS, $offerDetails, $content);
		$content = str_replace(BUSINESS_REVIEW, $reviewDetails, $content);

		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;

		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	/**
	 * For each business wich have a free plan will be send an notification for upgrade
	 * @param $company
	 * @return bool|int|JException
	 */
	public static function sendUpgradeNotificationEmail($company){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Business Upgrade Notification");
		if(empty($templ))
			return false;

		$link = JURI::root().'component/jbusinessdirectory/managecompany?layout=edit&id='.$company->id;

		$content = self::prepareNotificationEmail($company, $templ->email_content);
		$content = str_replace(BUSINESS_PATH_CONTROL_PANEL,$link,$content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;

		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}


	public static function sendNewOfferNotification($offer){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Creation Notification");
		if(empty($templ))
			return false;

		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$offerLink = '<a title="'.$offer->subject.'" href="'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'" >'.$offer->subject.'</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;

		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml,  $templ->send_to_admin);
	}
	
	public static function sendApproveOfferNotification($offer, $companyEmail){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$templ = self::getEmailTemplate("Offer Approval Notification");
		if(empty($templ))
			return false;
		
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$offerLink = '<a title="'.$offer->subject.'" href="'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'" >'.$offer->subject.'</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = self::updateCompanyDetails($content);
		
		$subject = $templ->email_subject;
		$toEmail = $companyEmail;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
	
		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOfferOrderNotification($orderDetails){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Order Notification");
		if(empty($templ))
			return false;

		$content = str_replace(EMAIL_OFFER_ORDER_DATE, JBusinessUtil::getDateGeneralFormatWithTime($orderDetails->created), $templ->email_content);
		$content = str_replace(EMAIL_OFFER_ORDER_DETAILS, $orderDetails->reservedItems, $content);
		$content = str_replace(EMAIL_OFFER_ORDER_BUYER_DETAILS, $orderDetails->buyerDetails, $content);
        $content = str_replace(EMAIL_OFFER_ORDER_ID, $orderDetails->id, $content);
		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_OFFER_ORDER_ID, $orderDetails->id, $templ->email_subject);
		$toEmail = $orderDetails->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

    public static function sendServiceBookingNotification($orderDetails){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Service Booking Notification");
        if(empty($templ))
            return false;

        $content = str_replace(EMAIL_SERVICE_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($orderDetails->created), $templ->email_content);
        $content = str_replace(EMAIL_SERVICE_BOOKING_DETAILS, $orderDetails->serviceDetails, $content);
        $content = str_replace(EMAIL_SERVICE_BUYER_DETAILS, $orderDetails->buyerDetails, $content);
        $content = str_replace(EMAIL_SERVICE_BOOKING_NAME, $orderDetails->serviceName, $content);
        $content = self::updateCompanyDetails($content);

        $subject = str_replace(EMAIL_SERVICE_BOOKING_ID, $orderDetails->id, $templ->email_subject);
        $toEmail = $orderDetails->email;
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array();

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }

    public static function sendCompanyAssociationNotification($event, $companyNames){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Company Association Notification");
        if(empty($templ))
            return false;

        $eventLink = '<a title="'.$event->name.'" href="'.JBusinessUtil::getEventLink($event->id, $event->alias).'" >'.$event->name.'</a>';
        $content = str_replace(EMAIL_EVENT_NAME, $eventLink, $templ->email_content);
        $content = str_replace(EMAIL_COMPANY_NAMES, $companyNames, $content);
        $content = self::updateCompanyDetails($content);

        $subject = str_replace(EMAIL_EVENT_NAME, $event->name, $templ->email_subject);
        $toEmail = $event->contact_email;
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array();

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }
	
	public static function sendNewEventNotification($event){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$templ = self::getEmailTemplate("Event Creation Notification");
		if(empty($templ))
			return false;
		
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$eventLink = '<a title="'.$event->name.'" href="'.JBusinessUtil::getEventLink($event->id, $event->alias).'" >'.$event->name.'</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $content);
		$content = self::updateCompanyDetails($content);
		
		$subject = $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;

		return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendApproveEventNotification($event, $companyEmail){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$templ = self::getEmailTemplate("Event Approval Notification");
		if(empty($templ))
			return false;
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$eventLink = '<a title="'.$event->name.'" href="'.JBusinessUtil::getEventLink($event->id, $event->alias).'" >'.$event->name.'</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $content);
		$content = self::updateCompanyDetails($content);
		
		$subject = $templ->email_subject;
		$toEmail = $companyEmail;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
	
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendEventPaymentDetails($bookingDetails){
	
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$templ = self::getEmailTemplate("Event Payment Details");
		if(empty($templ))
			return false;

		$content = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_content);
		
		$content = str_replace(EMAIL_CUSTOMER_NAME, $bookingDetails->first_name." ".$bookingDetails->last_name, $content);
		
		$eventAddress= JBusinessUtil::getAddressText($bookingDetails->event);
		$content = str_replace(EMAIL_EVENT_ADDRESS, $eventAddress, $content);
		$content = str_replace(EMAIL_PAYMENT_DETAILS, $bookingDetails->details->details, $content);
		$content = str_replace(EMAIL_EVENT_START_DATE, JBusinessUtil::getDateGeneralFormat($bookingDetails->event->start_date), $content);
		$content = str_replace(EMAIL_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($bookingDetails->created), $content);
		$content = str_replace(EMAIL_BOOKING_DETAILS, $bookingDetails->ticketDetailsSummary, $content);
		$content = str_replace(EMAIL_BOOKING_GUEST_DETAILS, $bookingDetails->guestDetailsSummary, $content);
		$content = str_replace(EMAIL_EVENT_PHONE, $bookingDetails->event->contact_phone, $content);
		$content = str_replace(EMAIL_EVENT_EMAIL, $bookingDetails->event->contact_email, $content);
		$content = str_replace(EMAIL_BOOKING_ID, $bookingDetails->id, $content);
		
		$siteAddress = JURI::root();
		$content = str_replace(EMAIL_SITE_ADDRESS, $siteAddress, $content);
		
		$content = self::updateCompanyDetails($content);
	
		$subject = str_replace(EMAIL_EVENT_NAME,  $bookingDetails->event->name, $templ->email_subject);
		$toEmail = $bookingDetails->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
	
		$result = self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
		return $result;
	}
	
	/**
	 * Prepare & send event reservation email 
	 * @param $bookingDetails
	 */
	public static function sendEventReservationNotification($bookingDetails){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$templ = self::getEmailTemplate("Event Reservation Notification");
		if(empty($templ))
			return false;

		$content = str_replace(EMAIL_EVENT_NAME, $bookingDetails->event->name, $templ->email_content);
		$eventLink = '<a title="'.$bookingDetails->event->name.'" href="'.JBusinessUtil::getEventLink($bookingDetails->event->id, $bookingDetails->event->alias).'" >'.JText::_('LNG_HERE').'</a>';
		$content = str_replace(EMAIL_EVENT_LINK, $eventLink, $content);
		
		$eventAddress= JBusinessUtil::getAddressText($bookingDetails->event);
		$content = str_replace(EMAIL_EVENT_ADDRESS, $eventAddress, $content);
		
		$content = str_replace(EMAIL_EVENT_START_DATE, JBusinessUtil::getDateGeneralFormat($bookingDetails->event->start_date), $content);
		$content = str_replace(EMAIL_BOOKING_DATE, JBusinessUtil::getDateGeneralFormatWithTime($bookingDetails->created), $content);
		$content = str_replace(EMAIL_BOOKING_DETAILS, $bookingDetails->ticketDetailsSummary, $content);
		$content = str_replace(EMAIL_BOOKING_GUEST_DETAILS, $bookingDetails->guestDetailsSummary, $content);
		$content = str_replace(EMAIL_EVENT_PHONE, $bookingDetails->event->contact_phone, $content);
		$content = str_replace(EMAIL_EVENT_EMAIL, $bookingDetails->event->contact_email, $content);
		
		
		$logoContent = '<img height="111" src="'.(JURI::root().PICTURES_PATH.'/no_image.jpg').'"/>';
		if(!empty($bookingDetails->event->pictures)){
			$bookingDetails->event->pictures[0]->picture_path = str_replace(" ","%20",$bookingDetails->event->pictures[0]->picture_path);
			$logoContent = '<img height="111" src="'.(JURI::root().PICTURES_PATH.$bookingDetails->event->pictures[0]->picture_path).'"/>';
		}
		
		$logoContent='<a href="'.JBusinessUtil::getEventLink($bookingDetails->event->id, $bookingDetails->event->alias).'">'.$logoContent.'</a>';
		$content = str_replace(EMAIL_EVENT_PICTURE, $logoContent, $content);

		$content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_EVENT_NAME,  $bookingDetails->event->name, $templ->email_subject);
		$toEmail = $bookingDetails->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function prepareNotificationEmail($company, $emailTemplate){

		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$emailContent = $emailTemplate;
		
		$emailContent = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $emailContent);
		$companyLink = '<a href="'.JBusinessUtil::getCompanyLink($company).'">'.$company->name.'</a>';
		$emailContent = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $emailContent);
		$emailContent = str_replace(EMAIL_BUSINESS_ADDRESS, JBusinessUtil::getAddressText($company), $emailContent);
		$emailContent = str_replace(EMAIL_BUSINESS_WEBSITE, $company->website, $emailContent);
		
		$emailContent = self::updateCompanyDetails($emailContent);
		
		$logoContent = '<img height="111" src="'.(JURI::root().PICTURES_PATH.'/no_image.jpg').'"/>';
		if(!empty($company->logoLocation)){
			$company->logoLocation = str_replace(" ","%20",$company->logoLocation);
			$logoContent = '<img height="111" src="'.(JURI::root().PICTURES_PATH.$company->logoLocation).'"/>';
		}
		
		$logoContent='<a href="'.JBusinessUtil::getCompanyLink($company).'">'.$logoContent.'</a>';

        $business_admin_area = JURI::root().'administrator/index.php?option=com_jbusinessdirectory&view=company&layout=edit&id='.$company->id;

        $emailContent = str_replace(EMAIL_BUSINESS_ADMINISTRATOR_URL, $business_admin_area, $emailContent );
		$emailContent = str_replace(EMAIL_BUSINESS_LOGO, $logoContent, $emailContent);
		$emailContent = str_replace(EMAIL_BUSINESS_CATEGORY, $company->selectedCategories[0]->name, $emailContent);
		$emailContent = str_replace(EMAIL_BUSINESS_CONTACT_PERSON, $company->contact->contact_name, $emailContent);
		
		return $emailContent;
	}
	
	public static function sendApprovalEmail($company){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$templ = self::getEmailTemplate("Approve Email");
		if(empty($templ))
			return false;
		
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
		$companyLink = '<a href="'.JBusinessUtil::getCompanyLink($company).'">'.$company->name.'</a>';
		$content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
		$content = self::updateCompanyDetails($content);
		
		$subject = $templ->email_subject;
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
	
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	
	public static function getBillingInformation($company){
		$user = JFactory::getUser($company->userId);
		$inf = $user->username."<br/>";
		$inf = $inf.$company->name."<br/>";
		$inf = $inf.JBusinessUtil::getAddressText($company);
	
		return $inf;
	}
	
	public static function getEmailTemplate($template){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$db =JFactory::getDBO();
		$query = ' SELECT * FROM #__jbusinessdirectory_emails WHERE email_type = "'.$template.'" and status=1 ';
		$db->setQuery($query);
		$templ= $db->loadObject();
		
		if($applicationSettings->enable_multilingual){
			$lang = JFactory::getLanguage()->getTag();
			$translation = JBusinessDirectoryTranslations::getObjectTranslation(EMAIL_TRANSLATION, $templ->email_id, $lang);
			
			if(!empty($translation)){
				if(!empty($translation->name)){
					$templ->email_subject = $translation->name;
				}
				if(!empty($translation->content)){
					$templ->email_content = $translation->content;
				}
			}
		}
		
		return $templ;
	}
	
	public static function prepareEmail($data, $company, $templEmail, $siteName=null, $billingInformation=null, $vat=null){
		$user = JFactory::getUser($company->userId);
		$customerName= $user->username;
		$templEmail = str_replace(EMAIL_CUSTOMER_NAME,$customerName, $templEmail);
	
		$siteAddress = JURI::root();
		$templEmail = str_replace(EMAIL_SITE_ADDRESS, $siteAddress,	$templEmail);
		$templEmail = str_replace(EMAIL_COMPANY_NAME, $siteName, $templEmail);
		$templEmail = str_replace(EMAIL_ORDER_ID,$data->order_id, $templEmail);
	
		$paymentMethod=$data->details->processor_type;
		$templEmail = str_replace(EMAIL_PAYMENT_METHOD, $paymentMethod, $templEmail);
		
		if(!empty($data->paid_at))
			$templEmail = str_replace(EMAIL_ORDER_DATE, JBusinessUtil::getDateGeneralFormat($data->paid_at), $templEmail);
		else
			$templEmail = str_replace(EMAIL_ORDER_DATE, JBusinessUtil::getDateGeneralFormat($data->details->payment_date), $templEmail);
		
		$templEmail = str_replace(EMAIL_SERVICE_NAME, $data->service, $templEmail);
		$templEmail = str_replace(EMAIL_UNIT_PRICE, JBusinessUtil::getPriceFormat($data->package->price), $templEmail);
		
		$totalAmount = $data->amount_paid;
		if(empty($data->amount_paid))
			$totalAmount = $data->amount;

        $templEmail = str_replace(EMAIL_TOTAL_PRICE, JBusinessUtil::getPriceFormat($totalAmount), $templEmail);
        $templEmail = str_replace(EMAIL_TAX_AMOUNT, JBusinessUtil::getPriceFormat($data->package->price * $vat/100), $templEmail);
        $templEmail = str_replace(EMAIL_SUBTOTAL_PRICE, JBusinessUtil::getPriceFormat($data->package->price), $templEmail);

		$templEmail = str_replace(EMAIL_BILLING_INFORMATION, $billingInformation, $templEmail);

        $templEmail = str_replace(EMAIL_TAX_DETAIL, $data->orderDetails, $templEmail);

        $companyLink = JBusinessUtil::getCompanyLink($company);
        $companyLink = '<a href="'.$companyLink.'">'.$company->name.'</a>';
        $templEmail = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $templEmail);

	
		return "<div style='width: 600px;'>".$templEmail.'</div>';
	}
	
	public static function prepareEmailFromArray($data, $company, $templEmail){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$fistName= isset($data["firstName"])?$data["firstName"]:"";
		$lastName=isset($data["lastName"])?$data["lastName"]:"";
		$description = isset($data["description"])?$data["description"]:"";
		$email = isset($data["email"])?$data["email"]:"";
		$abuseTxt = isset($data["description"])?$data["description"]:"";
		$expDays = isset($data["nrDays"])?$data["nrDays"]:"";
		$reviewName = isset($data["reviewName"])?$data["reviewName"]:"";
		$category = isset($data["category"])?$data["category"]:"";
		
		$templEmail = str_replace(EMAIL_CATEGORY, $category, $templEmail);
		$templEmail = str_replace(EMAIL_FIRST_NAME, $fistName, $templEmail);
		$templEmail = str_replace(EMAIL_LAST_NAME, $lastName, $templEmail);
		
		$companyLink = JBusinessUtil::getCompanyLink($company);
		$companyLink = '<a href="'.$companyLink.'">'.$company->name.'</a>';
		$templEmail = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $templEmail);
		
		$templEmail = str_replace(EMAIL_REVIEW_LINK, $companyLink, $templEmail);
		
		$templEmail = str_replace(EMAIL_CONTACT_EMAIL, $email, $templEmail);
		$templEmail = str_replace(EMAIL_CONTACT_CONTENT, $description, $templEmail);
		$templEmail = str_replace(EMAIL_ABUSE_DESCRIPTION,$description, $templEmail);
		$templEmail = str_replace(EMAIL_EXPIRATION_DAYS, $expDays, $templEmail);
		$templEmail = str_replace(EMAIL_REVIEW_NAME, $reviewName, $templEmail);
		
		$templEmail = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templEmail);
		$templEmail = str_replace(EMAIL_CLAIMED_COMPANY_NAME, $companyLink, $templEmail);
		
		return $templEmail;
	}
	
	public static function sendEmail($from, $fromName, $replyTo, $toEmail, $cc, $bcc, $subject, $content, $isHtml, $sendToAdmin=false){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		jimport('joomla.mail.mail');
	
		if(empty($toEmail)){
			return false;
		}
		
		try{
			$mail = JFactory::getMailer();
			$mail->setSender(array($from, $fromName));
			if(isset($replyTo))
				$mail->addReplyTo($replyTo);
			$mail->addRecipient($toEmail);
			if(isset($cc))
				$mail->addCC($cc);
			if(isset($bcc))
				$mail->addBCC($bcc);
			if($sendToAdmin)
				$mail->addBCC($applicationSettings->company_email);
	
			$mail->setSubject($subject);
			$mail->setBody($content);
			$mail->IsHTML($isHtml);
	
			$ret = $mail->send();
			
			$log = Logger::getInstance();
			$log->LogDebug("E-mail with subject ".$subject." sent from ".$from." to ".$toEmail." ".serialize($bcc)." result:".$ret);
		}catch(Exception $ex) {
				$log = Logger::getInstance();
				$log->LogDebug("E-mail with subject ".$subject." sent from ".$from." to ".$toEmail." failed");
				return 0;
		}

		return $ret;
	}
	
	public static function updateCompanyDetails($emailContent){
		$logo = self::getCompanyLogoCode();
		$socialNetworks = self::getCompanySocialNetworkCode();
		$emailContent = str_replace(EMAIL_COMPANY_LOGO, $logo, $emailContent);
		$emailContent = str_replace(EMAIL_COMPANY_SOCIAL_NETWORKS, $socialNetworks, $emailContent);
		$link='<a style="color:#555;text-decoration:none" target="_blank" href="'.JURI::root(false).'">'.JURI::root(false).'</a>';
		$emailContent = str_replace(EMAIL_DIRECTORY_WEBSITE, $link, $emailContent);
		
		return $emailContent;
	}
	
	public static function getCompanyLogoCode(){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$code ="";
		if(!empty($applicationSettings->logo)){
			$applicationSettings->logo = str_replace(" ","%20",$applicationSettings->logo);
			$logoLocaiton = JURI::root().PICTURES_PATH.$applicationSettings->logo;
			$link = JURI::root(false);
			$code='<a target="_blank" title"'.$applicationSettings->company_name.'" href="'.$link.'"><img height="55" alt="'.$applicationSettings->company_name.'" src="'.$logoLocaiton.'" ></a>';
		}
		
		return $code;
	}
	
	public static function getCompanySocialNetworkCode(){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$code="";
		if(!empty($applicationSettings->twitter)){
			$code.='<a href="'.$applicationSettings->twitter.'" target="_blank"><img title="Twitter" src="'.JURI::root().PICTURES_PATH.'/twitter.png'.'" alt="Twitter" height="32" border="0" width="32"></a>';
		}
			
		if(!empty($applicationSettings->facebook)){
			$code.='<a href="'.$applicationSettings->facebook.'" target="_blank"><img title="Facebook" src="'.JURI::root().PICTURES_PATH.'/facebook.png'.'" alt="Facebook" height="32" border="0" width="32"></a>';
		}
		
		if(!empty($applicationSettings->linkedin)){
			$code.='<a href="'.$applicationSettings->linkedin.'" target="_blank"><img title="LinkedIN" src="'.JURI::root().PICTURES_PATH.'/linkedin.png'.'" alt="LinkedIN" height="32" border="0" width="32"></a>';
		}
		
		if(!empty($applicationSettings->googlep)){
			$code.='<a href="'.$applicationSettings->googlep.'" target="_blank"><img title="Google+" src="'.JURI::root().PICTURES_PATH.'/googlep.png'.'" alt="Google+" height="32" border="0" width="32"></a>';
		}
		
		if(!empty($applicationSettings->youtube)){
			$code.='<a href="'.$applicationSettings->youtube.'" target="_blank"><img title="Youtube" src="'.JURI::root().PICTURES_PATH.'/youtube.png'.'" alt="Youtube" height="32" border="0" width="32"></a>';
		}
		
		return $code;
	}
	
	/**
	 * Send 
	 * @param unknown_type $company
	 * @param unknown_type $data
	 */
	static function sendContactCompanyEmail($company, $data){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$templ = self::getEmailTemplate("Contact Email");

		if(empty($templ))
			return false;
	
		$content =self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
	
		$subject=sprintf($templ->email_subject, $applicationSettings->company_name);

		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$sender = $data["firstName"]." ".$data["lastName"];
		$fromName = $sender;
		$isHtml = true;
		if(!empty($data["copy-me"])){
			$bcc = array($data["email"]);
		}
		
		return self::sendEmail($from, $fromName, $data["email"], $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

    /**
     * Send claim request email to site administrator
     *
     * @param $company object data of the company that has been claimed
     * @param $data array data of the form filled
     * @return boolean status of email
     *
     * @since 4.9.1
     */
	public static function sendClaimEmail($company,$data){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Claim Request Email");
        if( $templ ==null )
            return null;

        if(!isset($company->email))
            return;

        $content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
        $companyLink = '<a href="'.JBusinessUtil::getCompanyLink($company).'">'.$company->name.'</a>';
        $content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
        $content = self::updateCompanyDetails($content);

		$subject = str_replace(EMAIL_COMPANY_NAME, $company->name, $templ->email_subject);
		$toEmail = $data["email"];
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendExpirationEmail($company, $nrDays){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$templ = self::getEmailTemplate("Expiration Notification Email" );
		if( $templ ==null )
			return null;
	
		if(!isset($company->email))
			return;
	
		$data = array("nrDays"=>$nrDays);
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
	
		$subject=$templ->email_subject;
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

    public static function sendAbuseEmail($company, $email, $message ,$reportCause, $sendOnlyToAdmin=true){

        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Report Notification" );

        if( $templ ==null )
            return null;

        if(!isset($company->email))
            return;

        $content = str_replace(EMAIL_CONTACT_EMAIL, $email, $templ->email_content);
        $content = str_replace(EMAIL_ABUSE_DESCRIPTION, $message, $content);
        $companyLink = '<a href="'.JBusinessUtil::getCompanyLink($company).'">'.$company->name.'</a>';
        $content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
        $content = str_replace(EMAIL_COMPANY_NAME, $company->name, $content);
        $content = str_replace(EMAIL_REPORT_CAUSE, $reportCause, $content);

        $content = self::updateCompanyDetails($content);

        $from = $applicationSettings->company_email;
        $fromName = $email;
        $toEmail =  $applicationSettings->company_email;
        $subject=$templ->email_subject;

        $isHtml = true;
        $bcc = array();

        if($sendOnlyToAdmin){
            $bcc = null;
            $toEmail = $from;
        }

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);

    }

	public static function sendEventExpirationEmail($event, $nrDays){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$templ = self::getEmailTemplate("Event Expiration Notification Email" );
		if( $templ ==null )
			return null;

		if(!isset($event->contact_email))
			return;

        $eventLink = '<a title="'.$event->name.'" href="'.JBusinessUtil::getEventLink($event->id, $event->alias).'" >'.$event->name.'</a>';
		$content = str_replace(EMAIL_EVENT_NAME, $eventLink, $templ->email_content);
		$content = str_replace(EMAIL_EXPIRATION_DAYS, $nrDays, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = self::updateCompanyDetails($content);

		$subject=$templ->email_subject;
		$toEmail = $event->contact_email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();

		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

	public static function sendOfferExpirationEmail($offer, $nrDays){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		$templ = self::getEmailTemplate("Offer Expiration Notification Email" );
		if( $templ ==null )
			return null;

		if(!isset($offer->companyEmail))
			return;

        $offerLink = '<a title="'.$offer->subject.'" href="'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'" >'.$offer->subject.'</a>';
		$content = str_replace(EMAIL_OFFER_NAME, $offerLink, $templ->email_content);
		$content = str_replace(EMAIL_EXPIRATION_DAYS, $nrDays, $content);
		$content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);
		$content = self::updateCompanyDetails($content);

		$subject=$templ->email_subject;
		$toEmail = $offer->companyEmail;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();

		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendReviewEmail($company, $data){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$templ = self::getEmailTemplate("Review Email");
		if( $templ ==null )
			return null;
	
		if(!isset($company->email))
			return;
	
		$content = self::prepareEmail($data, $company, $templ->email_content);
        $companyLink = JBusinessUtil::getCompanyLink($company);
        $companyLink = '<a href="'.$companyLink.'">'.$company->name.'</a>';
        $content = str_replace(EMAIL_REVIEW_LINK, $companyLink, $content);
		$content = self::updateCompanyDetails($content);
		
		$subject=sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendReviewResponseEmail($company, $data){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$templ = self::getEmailTemplate("Review Response Email");
		if( $templ ==null )
			return null;
	
		if(!isset($company->email))
			return;
	
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		
		$subject=sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}	
	
	public static function sendReportAbuseEmail($data, $review, $company){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		$templ = self::getEmailTemplate("Report Abuse Email");
		if( $templ ==null )
			return null;
	
		if(isset($review)){
			$data["reviewName"]= $review->subject;
		}
		
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		
		$subject= $templ->email_subject;
		$toEmail = $applicationSettings->company_email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendRequestQuoteEmail($data, $company){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
			
		$templ = self::getEmailTemplate("Request Quote Email");
		if( $templ ==null )
			return null;
	
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
		
		$subject=sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}
	
	public static function sendClaimResponseEmail($company, $claimDetails, $template){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();
	
		$templ = self::getEmailTemplate($template);
		if( $templ ==null )
			return null;
		
		$data=array();
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
		$content = self::updateCompanyDetails($content);
	
		$subject = $templ->email_subject;
		$toEmail = $claimDetails->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();
		
		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

    public static function sendUpdateCompanyNotificationEmailToAdmin($company){

        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Business Update Notification");
        if(empty($templ))
            return false;

        $content = self::prepareNotificationEmail($company, $templ->email_content);
        $content = self::updateCompanyDetails($content);

        $subject = $templ->email_subject;
        $toEmail = $applicationSettings->company_email;
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;

        return self::sendEmail($from, $fromName, $from, $toEmail, null, null, $subject, $content, $isHtml, $templ->send_to_admin);
    }

    /**
     * Send email for every Response added on an offer review
     * @param $offer
     * @param $company
     * @param $data
     * @return bool|int|JException|null|void
     */
   public static function sendOfferReviewResponseEmail($offer, $company, $data){
		$applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

		$templ = self::getEmailTemplate("Offer Review Response Email");
		if( $templ ==null )
			return null;

		if(!isset($company->email))
			return;

        $offerLink = '<a title="'.$offer->subject.'" href="'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'" >'.$offer->subject.'</a>';
		$content = self::prepareEmailFromArray($data, $company, $templ->email_content);
        $content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
		$content = self::updateCompanyDetails($content);

		$subject=sprintf($templ->email_subject, $applicationSettings->company_name);
		$toEmail = $company->email;
		$from = $applicationSettings->company_email;
		$fromName = $applicationSettings->company_name;
		$isHtml = true;
		$bcc = array();

		return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
	}

    /**
     * Send email notification for reviews added on any offer
     * @param $offer
     * @param $company
     * @param $data
     * @return bool|int|JException|null|void
     */
    public static function sendOfferReviewEmail($offer, $company, $data){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Offer Review Email");
        if( $templ ==null )
            return null;

        if(!isset($company->email))
            return;

        $content = self::prepareEmail($data, $company, $templ->email_content);

        $offerLink = '<a title="'.$offer->subject.'" href="'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'" >'.$offer->subject.'</a>';
        $content = str_replace(EMAIL_OFFER_NAME, $offerLink, $content);
        $content = str_replace(EMAIL_REVIEW_LINK, $offerLink, $content);

        $content = self::updateCompanyDetails($content);

        $subject=sprintf($templ->email_subject, $applicationSettings->company_name);
        $toEmail = $company->email;
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array();

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }

    /**
     * Send email when an abuse is reported on an offer review
     * @param $data
     * @param $review
     * @param $company
     * @param $offer
     * @return bool|int|JException|null
     */
    public static function sendOfferReportAbuseEmail($data, $review, $company, $offer){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Report Abuse Offer Review");
        if( $templ ==null )
            return null;

        if(isset($review)){
            $data["reviewName"]= $review->subject;
        }

        $content = self::prepareEmailFromArray($data, $company, $templ->email_content);
        $content = str_replace(EMAIL_OFFER_NAME, $offer->subject, $content);

        $offerLink = '<a title="'.$offer->subject.'" href="'.JBusinessUtil::getOfferLink($offer->id, $offer->alias).'" >'.$offer->subject.'</a>';
        $content = str_replace(EMAIL_REVIEW_LINK_OFFER, $offerLink, $content);
        $content = self::updateCompanyDetails($content);

        $subject= $templ->email_subject;
        $toEmail = $applicationSettings->company_email;
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array();

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }

    /**
     * Send contact email to event
     * @param $company
     * @param $data
     * @return bool|int|JException
     */
    static function sendContactEventCompanyEmail($company, $data){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Event Contact Email");

        if(empty($templ))
            return false;

        $content =self::prepareEmailFromArray($data, $company, $templ->email_content);
        $content = self::updateCompanyDetails($content);

        $subject = str_replace(EMAIL_EVENT_NAME, $data["event_name"], $templ->email_subject);
        $toEmail = $company->email;
        $from = $applicationSettings->company_email;
        $sender = $data["firstName"]." ".$data["lastName"];
        $fromName = $sender;
        $isHtml = true;
        if(!empty($data["copy-me"])){
            $bcc = array($data["email"]);
        }

        return self::sendEmail($from, $fromName, $data["email"], $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }


    /**
     * Send contact email for offer
     * @param $company
     * @param $data
     * @return bool|int|JException
     */
    static function sendOfferContactCompanyEmail($company, $data){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Offer Contact Email");

        if(empty($templ))
            return false;

        $content =self::prepareEmailFromArray($data, $company, $templ->email_content);
        $content = self::updateCompanyDetails($content);

        $subject = str_replace(EMAIL_OFFER_NAME, $data["offer_name"], $templ->email_subject);
        $toEmail = $company->email;
        $from = $applicationSettings->company_email;
        $sender = $data["firstName"]." ".$data["lastName"];
        $fromName = $sender;
        $isHtml = true;
        if(!empty($data["copy-me"])){
            $bcc = array($data["email"]);
        }

        return self::sendEmail($from, $fromName, $data["email"], $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }

    public static function sendAppointmentEmail($event, $data, $companyEmail, $companyName){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Event Appointment Email");
        if(empty($templ))
            return false;

        $content = str_replace(EMAIL_EVENT_NAME, $event->name, $templ->email_content);
        $content = str_replace(EMAIL_APPOINTMENT_DATE, JBusinessUtil::getDateGeneralFormat($data['date']), $content);
        $content = str_replace(EMAIL_APPOINTMENT_TIME, JBusinessUtil::getTimeText($data['time']), $content);
        $content = str_replace(EMAIL_FIRST_NAME, $data['first_name'], $content);
        $content = str_replace(EMAIL_LAST_NAME, $data['last_name'], $content);
        $content = str_replace(EMAIL_EMAIL, $data['email'], $content);
        $content = str_replace(EMAIL_PHONE, $data['phone'], $content);
        $content = str_replace(EMAIL_BUSINESS_NAME, $companyName, $content);
        $content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

        $content = self::updateCompanyDetails($content);

        $subject = str_replace(EMAIL_EVENT_NAME, $event->name, $templ->email_subject);
        $subject = str_replace(EMAIL_APPOINTMENT_DATE, JBusinessUtil::getDateGeneralFormat($data['date']), $subject);
        $toEmail = $data["email"];
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array($companyEmail);

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }

    public static function sendAppointmentStatusEmail($appointment, $status){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Event Appointment Status Notification");
        if(empty($templ))
            return false;

        $statusText = '';
        if($status == EVENT_APPOINTMENT_CONFIRMED)
            $statusText = JText::_('LNG_CONFIRMED');
        else
            $statusText = JText::_('LNG_CANCELED');

        $content = str_replace(EMAIL_EVENT_NAME, $appointment->eventName, $templ->email_content);
        $content = str_replace(EMAIL_APPOINTMENT_DATE, JBusinessUtil::getDateGeneralFormat($appointment->date), $content);
        $content = str_replace(EMAIL_APPOINTMENT_TIME, JBusinessUtil::getTimeText($appointment->time), $content);
        $content = str_replace(EMAIL_APPOINTMENT_STATUS, $statusText, $content);
        $content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $content);

        $content = self::updateCompanyDetails($content);

        $subject= $templ->email_subject;
        $toEmail = $appointment->email;
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array();

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }


    public static function sendDisapprovalEmail ($company){
        $applicationSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $templ = self::getEmailTemplate("Disapprove Email");
        if(empty($templ))
            return false;

        $content = str_replace(EMAIL_COMPANY_NAME, $applicationSettings->company_name, $templ->email_content);
        $companyLink = '<a href="'.JBusinessUtil::getCompanyLink($company).'">'.$company->name.'</a>';
        $content = str_replace(EMAIL_BUSINESS_NAME, $companyLink, $content);
        $content = self::updateCompanyDetails($content);

        $subject = str_replace(EMAIL_BUSINESS_NAME, $company->name, $templ->email_subject);
        $toEmail = $company->email;
        $from = $applicationSettings->company_email;
        $fromName = $applicationSettings->company_name;
        $isHtml = true;
        $bcc = array();

        return self::sendEmail($from, $fromName, $from, $toEmail, null, $bcc, $subject, $content, $isHtml, $templ->send_to_admin);
    }

}

?>