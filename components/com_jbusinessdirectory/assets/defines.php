<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

if( !defined( 'COMPANY_DESCRIPTIION_MAX_LENGHT') )
	define( 'COMPANY_DESCRIPTIION_MAX_LENGHT',1200);
if( !defined( 'CATEGORY_DESCRIPTIION_MAX_LENGHT') )
	define( 'CATEGORY_DESCRIPTIION_MAX_LENGHT',500);
if( !defined( 'COMPANY_SHORT_DESCRIPTIION_MAX_LENGHT') )
	define( 'COMPANY_SHORT_DESCRIPTIION_MAX_LENGHT',250);
if( !defined( 'OFFER_DESCRIPTIION_MAX_LENGHT') )
	define( 'OFFER_DESCRIPTIION_MAX_LENGHT',1200);
if( !defined( 'COMPANY_SLOGAN_MAX_LENGHT') )
	define( 'COMPANY_SLOGAN_MAX_LENGHT',250);
if( !defined( 'EVENT_DESCRIPTION_MAX_LENGHT') )
	define( 'EVENT_DESCRIPTION_MAX_LENGHT',1200);

if( !defined( 'COMPANY_PICTURES_PATH') )
	define( 'COMPANY_PICTURES_PATH','/companies/');
if( !defined( 'OFFER_PICTURES_PATH') )
	define( 'OFFER_PICTURES_PATH','/offers/');
if( !defined( 'CATEGORY_PICTURES_PATH') )
	define( 'CATEGORY_PICTURES_PATH','/categories/');
if( !defined( 'BANNER_PICTURES_PATH') )
	define( 'BANNER_PICTURES_PATH','/upload/images/banners/');
if( !defined( 'EVENT_PICTURES_PATH') )
	define( 'EVENT_PICTURES_PATH','/events/');
if( !defined( 'COUNTRIES_PICTURES_PATH') )
	define( 'COUNTRIES_PICTURES_PATH','/countries/');
if( !defined( 'APP_PICTURES_PATH') )
	define( 'APP_PICTURES_PATH','/app/');
if( !defined( 'MEMBERSHIP_PICTURES_PATH') )
    define( 'MEMBERSHIP_PICTURES_PATH','/memberships/');
if( !defined( 'PROJECT_PICTURES_PATH') )
    define( 'PROJECT_PICTURES_PATH','/project/');

if( !defined( 'PICTURES_PATH') )
	define( 'PICTURES_PATH', 'media/com_jbusinessdirectory/pictures');
if( !defined( 'ATTACHMENT_PATH') )
	define( 'ATTACHMENT_PATH', 'media/com_jbusinessdirectory/attachments');
if( !defined( 'ATTACHMENT_ICON_PATH') )
	define( 'ATTACHMENT_ICON_PATH', 'media/com_jbusinessdirectory/attachments/icons/');

if( !defined( 'BUSINESS_ATTACHMENTS_PATH') )
	define( 'BUSINESS_ATTACHMENTS_PATH','/companies/');
if( !defined( 'OFFER_ATTACHMENTS_PATH') )
	define( 'OFFER_ATTACHMENTS_PATH','/offers/');
if( !defined( 'EVENT_ATTACHMENTS_PATH') )
	define( 'EVENT_ATTACHMENTS_PATH','/events/');

//types for translation
if( !defined( 'BUSSINESS_ATTACHMENTS'))
	define('BUSSINESS_ATTACHMENTS',1);
if( !defined( 'OFFER_ATTACHMENTS'))
	define('OFFER_ATTACHMENTS',2);
if( !defined( 'EVENTS_ATTACHMENTS'))
	define('EVENTS_ATTACHMENTS',3);

if( !defined( 'MAX_COMPANY_PICTURE_WIDTH') )
	define( 'MAX_COMPANY_PICTURE_WIDTH', 1000);
if( !defined( 'MAX_COMPANY_PICTURE_HEIGHT') )
	define( 'MAX_COMPANY_PICTURE_HEIGHT', 800);

if( !defined( 'MAX_LOGO_WIDTH') )
	define( 'MAX_LOGO_WIDTH', 800);
if( !defined( 'MAX_LOGO_HEIGHT') )
	define( 'MAX_LOGO_HEIGHT', 800);

if( !defined( 'MAX_OFFER_PICTURE_WIDTH') )
	define( 'MAX_OFFER_PICTURE_WIDTH', 800);
if( !defined( 'MAX_OFFER_PICTURE_HEIGHT') )
	define( 'MAX_OFFER_PICTURE_HEIGHT', 800);

if( !defined( 'MAX_GALLERY_WIDTH') )
    define( 'MAX_GALLERY_WIDTH', 800);
if( !defined( 'MAX_GALLERY_HEIGHT') )
    define( 'MAX_GALLERY_HEIGHT', 800);

if( !defined( 'PICTURE_TYPE_COMPANY') )
	define( 'PICTURE_TYPE_COMPANY', 'picture_type_company');
if( !defined( 'PICTURE_TYPE_OFFER') )
	define( 'PICTURE_TYPE_OFFER', 'picture_type_offer');
if( !defined( 'PICTURE_TYPE_LOGO') )
	define( 'PICTURE_TYPE_LOGO', 'picture_type_logo');
if( !defined( 'PICTURE_TYPE_EVENT') )
	define( 'PICTURE_TYPE_EVENT', 'picture_type_event');
if( !defined( 'PICTURE_TYPE_GALLERY') )
    define( 'PICTURE_TYPE_GALLERY', 'picture_type_gallery');

if( !defined( 'ICON_SIZE') )
	define( 'ICON_SIZE', 300);	 

if( !defined( 'EMAIL_FIRST_NAME') )
	define( 'EMAIL_FIRST_NAME','[first_name]');
if( !defined( 'EMAIL_CATEGORY') )
	define( 'EMAIL_CATEGORY','[category]');
if( !defined( 'EMAIL_LAST_NAME') )
	define( 'EMAIL_LAST_NAME','[last_name]');
if( !defined( 'EMAIL_REVIEW_LINK') )
	define( 'EMAIL_REVIEW_LINK','[review_link]');
if( !defined( 'EMAIL_COMPANY_NAME') )
    define( 'EMAIL_COMPANY_NAME','[company_name]');
if( !defined( 'EMAIL_COMPANY_NAMES') )
    define( 'EMAIL_COMPANY_NAMES','[company_names]');
if( !defined( 'EMAIL_BUSINESS_NAME') )
	define( 'EMAIL_BUSINESS_NAME','[business_name]');
if( !defined( 'EMAIL_BUSINESS_ADDRESS') )
	define( 'EMAIL_BUSINESS_ADDRESS','[business_address]');
if( !defined( 'EMAIL_BUSINESS_WEBSITE') )
	define( 'EMAIL_BUSINESS_WEBSITE','[business_website]');
if( !defined( 'EMAIL_BUSINESS_LOGO') )
	define( 'EMAIL_BUSINESS_LOGO','[business_logo]');
if( !defined( 'EMAIL_BUSINESS_CATEGORY') )
	define( 'EMAIL_BUSINESS_CATEGORY','[business_category]');
if( !defined( 'EMAIL_BUSINESS_CONTACT_PERSON') )
	define( 'EMAIL_BUSINESS_CONTACT_PERSON','[business_contact_person]');
if( !defined( 'EMAIL_BUSINESS_REFFERED_BY') )
	define( 'EMAIL_BUSINESS_REFFERED_BY','[business_referred_by]');
if( !defined( 'EMAIL_OFFER_NAME') )
	define( 'EMAIL_OFFER_NAME','[offer_name]');
if( !defined( 'EMAIL_EVENT_NAME') )
	define( 'EMAIL_EVENT_NAME','[event_name]');
if( !defined( 'EMAIL_EVENT_LINK') )
	define( 'EMAIL_EVENT_LINK','[event_link]');
if( !defined( 'EMAIL_EVENT_START_DATE') )
	define( 'EMAIL_EVENT_START_DATE','[event_start_date]');
if( !defined( 'EMAIL_EVENT_ADDRESS') )
	define( 'EMAIL_EVENT_ADDRESS','[event_address]');
if( !defined( 'EMAIL_EVENT_EMAIL') )
	define( 'EMAIL_EVENT_EMAIL','[event_email]');
if( !defined( 'EMAIL_EVENT_PHONE') )
	define( 'EMAIL_EVENT_PHONE','[event_phone]');

if( !defined( 'EMAIL_BOOKING_DATE') )
	define( 'EMAIL_BOOKING_DATE','[event_booking_date]');
if( !defined( 'EMAIL_BOOKING_ID') )
	define( 'EMAIL_BOOKING_ID','[event_booking_id]');

if( !defined( 'EMAIL_EVENT_PICTURE') )
	define( 'EMAIL_EVENT_PICTURE','[event_picture]');

if( !defined( 'EMAIL_BOOKING_DETAILS') )
	define( 'EMAIL_BOOKING_DETAILS','[booking_details]');
if( !defined( 'EMAIL_BOOKING_GUEST_DETAILS') )
	define( 'EMAIL_BOOKING_GUEST_DETAILS','[booking_guest_details]');


if( !defined( 'EMAIL_CLAIMED_COMPANY_NAME') )
	define( 'EMAIL_CLAIMED_COMPANY_NAME','[claimed_company_name]'); 
if( !defined( 'EMAIL_CONTACT_CONTENT') )
	define( 'EMAIL_CONTACT_CONTENT','[contact_email_content]');
if( !defined( 'EMAIL_CONTACT_EMAIL') )
	define( 'EMAIL_CONTACT_EMAIL','[contact_email]');
if( !defined( 'EMAIL_ABUSE_DESCRIPTION') )
	define( 'EMAIL_ABUSE_DESCRIPTION','[abuse_description]');
if( !defined( 'EMAIL_REVIEW_NAME') )
	define( 'EMAIL_REVIEW_NAME','[review_name]');


if( !defined( 'EMAIL_CUSTOMER_NAME') )
	define( 'EMAIL_CUSTOMER_NAME','[customer_name]');
if( !defined( 'EMAIL_SITE_ADDRESS') )
	define( 'EMAIL_SITE_ADDRESS','[site_address]');
if( !defined( 'EMAIL_UNIT_PRICE') )
	define( 'EMAIL_UNIT_PRICE','[unit_price]');	
if( !defined( 'EMAIL_SERVICE_NAME') )
	define( 'EMAIL_SERVICE_NAME','[service_name]');
if( !defined( 'EMAIL_TAX_AMOUNT') )
	define( 'EMAIL_TAX_AMOUNT','[tax_amount]');
if( !defined( 'EMAIL_ORDER_ID') )
	define( 'EMAIL_ORDER_ID','[order_id]');
if( !defined( 'EMAIL_PAYMENT_METHOD') )
	define( 'EMAIL_PAYMENT_METHOD','[payment_method]');
if( !defined( 'EMAIL_ORDER_DATE') )
	define( 'EMAIL_ORDER_DATE','[order_date]');
if( !defined( 'EMAIL_TOTAL_PRICE') )
    define( 'EMAIL_TOTAL_PRICE','[total_price]');
if( !defined( 'EMAIL_SUBTOTAL_PRICE') )
	define( 'EMAIL_SUBTOTAL_PRICE','[subtotal_price]');
if( !defined( 'EMAIL_BILLING_INFORMATION') )
	define( 'EMAIL_BILLING_INFORMATION','[billing_information]');
if( !defined( 'EMAIL_EXPIRATION_DAYS') )
	define( 'EMAIL_EXPIRATION_DAYS','[exp_days]');
if( !defined( 'EMAIL_PAYMENT_DETAILS') )
	define( 'EMAIL_PAYMENT_DETAILS','[payment_details]');
if( !defined( 'EMAIL_DIRECTORY_WEBSITE') )
	define( 'EMAIL_DIRECTORY_WEBSITE','[directory_website]');
if( !defined( 'EMAIL_COMPANY_SOCIAL_NETWORKS') )
	define( 'EMAIL_COMPANY_SOCIAL_NETWORKS','[company_social_networks]');
if( !defined( 'EMAIL_COMPANY_LOGO') )
	define( 'EMAIL_COMPANY_LOGO','[company_logo]');

if( !defined( 'EMAIL_OFFER_ORDER_ID') )
	define( 'EMAIL_OFFER_ORDER_ID','[offer_order_id]');
if( !defined( 'EMAIL_OFFER_ORDER_DATE') )
	define( 'EMAIL_OFFER_ORDER_DATE','[offer_order_date]');
if( !defined( 'EMAIL_OFFER_ORDER_DETAILS') )
	define( 'EMAIL_OFFER_ORDER_DETAILS','[offer_order_details]');
if( !defined( 'EMAIL_OFFER_ORDER_BUYER_DETAILS') )
	define( 'EMAIL_OFFER_ORDER_BUYER_DETAILS','[offer_order_buyer_details]');

if( !defined( 'EMAIL_APPOINTMENT_DATE') )
    define( 'EMAIL_APPOINTMENT_DATE','[appointment_date]');
if( !defined( 'EMAIL_APPOINTMENT_TIME') )
    define( 'EMAIL_APPOINTMENT_TIME','[appointment_time]');
if( !defined( 'EMAIL_APPOINTMENT_STATUS') )
    define( 'EMAIL_APPOINTMENT_STATUS','[appointment_status]');
if( !defined( 'EMAIL_EMAIL') )
    define( 'EMAIL_EMAIL','[email]');
if( !defined( 'EMAIL_PHONE') )
    define( 'EMAIL_PHONE','[phone]');

if( !defined( 'EMAIL_SERVICE_BOOKING_ID') )
    define( 'EMAIL_SERVICE_BOOKING_ID','[service_booking_id]');
if( !defined( 'EMAIL_SERVICE_BOOKING_DATE') )
    define( 'EMAIL_SERVICE_BOOKING_DATE','[service_booking_date]');
if( !defined( 'EMAIL_SERVICE_BOOKING_DETAILS') )
    define( 'EMAIL_SERVICE_BOOKING_DETAILS','[service_booking_details]');
if( !defined( 'EMAIL_SERVICE_BUYER_DETAILS') )
    define( 'EMAIL_SERVICE_BUYER_DETAILS','[service_buyer_details]');
if( !defined( 'EMAIL_SERVICE_BOOKING_NAME') )
    define( 'EMAIL_SERVICE_BOOKING_NAME','[service_booking_name]');

if( !defined( 'COMPANY_STATUS_CLAIMED') )
define( 'COMPANY_STATUS_CLAIMED',-1);

if( !defined( 'COMPANY_STATUS_CREATED') )
define( 'COMPANY_STATUS_CREATED',0);

if( !defined( 'COMPANY_STATUS_DISAPPROVED') )
define( 'COMPANY_STATUS_DISAPPROVED',1);

if( !defined( 'COMPANY_STATUS_APPROVED') )
define( 'COMPANY_STATUS_APPROVED',2);

if( !defined( 'COMPANY_STATUS_CLAIMED_APPROVED') )
	define( 'COMPANY_STATUS_CLAIMED_APPROVED',3);

if( !defined( 'EVENT_CREATED') )
	define( 'EVENT_CREATED',0);
if( !defined( 'EVENT_APPROVED') )
	define( 'EVENT_APPROVED',1);

if( !defined( 'OFFER_CREATED') )
	define( 'OFFER_CREATED',0);
if( !defined( 'OFFER_APPROVED') )
	define( 'OFFER_APPROVED',1);

if( !defined( 'DESCRIPTION') )
	define( 'DESCRIPTION',"description");
if( !defined( 'HTML_DESCRIPTION') )
    define( 'HTML_DESCRIPTION',"html_description");
if( !defined( 'FEATURED_COMPANIES') )
	define( 'FEATURED_COMPANIES',"featured_companies");
if( !defined( 'SHOW_COMPANY_LOGO') )
	define( 'SHOW_COMPANY_LOGO',"company_logo");
if( !defined( 'WEBSITE_ADDRESS') )
	define( 'WEBSITE_ADDRESS',"website_address");

if( !defined( 'MULTIPLE_CATEGORIES') )
	define( 'MULTIPLE_CATEGORIES',"multiple_categories");
if( !defined( 'IMAGE_UPLOAD') )
	define( 'IMAGE_UPLOAD',"image_upload");
if( !defined( 'VIDEOS') )
	define( 'VIDEOS',"videos");
if( !defined( 'GOOGLE_MAP') )
	define( 'GOOGLE_MAP',"google_map");
if( !defined( 'CONTACT_FORM') )
	define( 'CONTACT_FORM',"contact_form");
if( !defined( 'COMPANY_OFFERS') )
	define( 'COMPANY_OFFERS',"company_offers");
if( !defined( 'FEATURED_OFFERS') )
    define( 'FEATURED_OFFERS',"featured_offers");
if( !defined( 'SOCIAL_NETWORKS') )
	define( 'SOCIAL_NETWORKS',"social_networks");
if( !defined( 'COMPANY_EVENTS') )
	define( 'COMPANY_EVENTS',"company_events");
if( !defined( 'PHONE') )
	define( 'PHONE',"phone");
if( !defined( 'CUSTOM_TAB') )
	define( 'CUSTOM_TAB',"custom_tab");
if( !defined( 'ATTACHMENTS') )
	define( 'ATTACHMENTS',"attachments");
if( !defined( 'OPENING_HOURS') )
	define( 'OPENING_HOURS',"opening_hours");
if( !defined( 'SECONDARY_LOCATIONS') )
	define( 'SECONDARY_LOCATIONS',"secondary_locations");
if( !defined( 'COMPANY_SERVICES') )
    define( 'COMPANY_SERVICES',"company_services");
if( !defined( 'SOUNDS_FEATURE') )
	define( 'SOUNDS_FEATURE',"company_sounds");


if(!defined( 'PAYMENT_REDIRECT'))
	define( 'PAYMENT_REDIRECT',1);
if(!defined( 'PAYMENT_SUCCESS'))
	define( 'PAYMENT_SUCCESS',2);
if(!defined( 'PAYMENT_WAITING'))
	define( 'PAYMENT_WAITING',3);
if(!defined( 'PAYMENT_ERROR'))
	define( 'PAYMENT_ERROR',4);
if(!defined( 'PAYMENT_CANCELED'))
	define( 'PAYMENT_CANCELED',5);

if( !defined( 'PAYMENT_STATUS_NOT_PAID') )
	define( 'PAYMENT_STATUS_NOT_PAID',"0");
if( !defined( 'PAYMENT_STATUS_PAID') )
	define( 'PAYMENT_STATUS_PAID',"1");
if( !defined( 'PAYMENT_STATUS_PENDING') )
	define( 'PAYMENT_STATUS_PENDING','2');
if( !defined( 'PAYMENT_STATUS_WAITING') )
	define( 'PAYMENT_STATUS_WAITING','3');
if( !defined( 'PAYMENT_STATUS_FAILURE') )
	define( 'PAYMENT_STATUS_FAILURE','4');
if( !defined( 'PAYMENT_STATUS_CANCELED') )
	define( 'PAYMENT_STATUS_CANCELED','5');


if( !defined( 'UPDATE_TYPE_NEW') )
	define( 'UPDATE_TYPE_NEW',"0");
if( !defined( 'UPDATE_TYPE_UPGRADE') )
	define( 'UPDATE_TYPE_UPGRADE',"1");
if( !defined( 'UPDATE_TYPE_EXTEND') )
	define( 'UPDATE_TYPE_EXTEND',"2");

if( !defined( 'LIST_VIEW') )
	define( 'LIST_VIEW',"list");
if( !defined( 'GRID_VIEW') )
	define( 'GRID_VIEW',"grid");


if( !defined( 'ATTRIBUTE_MANDATORY') )
	define( 'ATTRIBUTE_MANDATORY',1);
if( !defined( 'ATTRIBUTE_OPTIONAL') ) 
	define( 'ATTRIBUTE_OPTIONAL',2);
if( !defined( 'ATTRIBUTE_NOT_SHOW') )
	define( 'ATTRIBUTE_NOT_SHOW',3);

if( !defined( 'SEARCH_BY_DISTNACE') )
	define( 'SEARCH_BY_DISTNACE',0);

if( !defined( 'DS') )
	define( 'DS','/');

//types for translation
if( !defined( 'BUSSINESS_DESCRIPTION_TRANSLATION'))
	define('BUSSINESS_DESCRIPTION_TRANSLATION',1);

if( !defined( 'BUSSINESS_SLOGAN_TRANSLATION'))
	define('BUSSINESS_SLOGAN_TRANSLATION',2);

if( !defined( 'CATEGORY_TRANSLATION'))
	define('CATEGORY_TRANSLATION',3);

if( !defined( 'PACKAGE_TRANSLATION'))
	define('PACKAGE_TRANSLATION',4);

if( !defined( 'OFFER_DESCRIPTION_TRANSLATION'))
	define('OFFER_DESCRIPTION_TRANSLATION',5);

if( !defined( 'EVENT_DESCRIPTION_TRANSLATION'))
	define('EVENT_DESCRIPTION_TRANSLATION',6);

if( !defined( 'COUNTRY_TRANSLATION'))
	define('COUNTRY_TRANSLATION',7);

if( !defined( 'TYPE_TRANSLATION'))
	define('TYPE_TRANSLATION',8);

if( !defined( 'ATTRIBUTE_TRANSLATION'))
	define('ATTRIBUTE_TRANSLATION',9);

if( !defined( 'CONFERENCE_TRANSLATION'))
	define('CONFERENCE_TRANSLATION',10);

if( !defined( 'CONFERENCE_SESSION_TRANSLATION'))
	define('CONFERENCE_SESSION_TRANSLATION',11);

if( !defined( 'CONFERENCE_SPEAKER_TRANSLATION'))
	define('CONFERENCE_SPEAKER_TRANSLATION',12);

if( !defined( 'CONFERENCE_TYPE_TRANSLATION'))
	define('CONFERENCE_TYPE_TRANSLATION',13);

if( !defined( 'CONFERENCE_LEVEL_TRANSLATION'))
	define('CONFERENCE_LEVEL_TRANSLATION',14);

if( !defined( 'CONFERENCE_SPEAKER_TYPE_TRANSLATION'))
	define('CONFERENCE_SPEAKER_TYPE_TRANSLATION',15);

if( !defined( 'REVIEW_CRITERIA_TRANSLATION'))
	define('REVIEW_CRITERIA_TRANSLATION',16);

if( !defined( 'EMAIL_TRANSLATION'))
	define('EMAIL_TRANSLATION',17);

if( !defined( 'EVENT_TYPE_TRANSLATION'))
	define('EVENT_TYPE_TRANSLATION',18);

if( !defined( 'REVIEW_QUESTION_TRANSLATION'))
	define('REVIEW_QUESTION_TRANSLATION',19);

if( !defined( 'EVENT_TICKET_TRANSLATION'))
	define('EVENT_TICKET_TRANSLATION',20);

if( !defined( 'TERMS_CONDITIONS_TRANSLATION'))
	define('TERMS_CONDITIONS_TRANSLATION',21);

if( !defined( 'COMPANY_SERVICE_TRANSLATION'))
    define('COMPANY_SERVICE_TRANSLATION',22);

if( !defined( 'COMPANY_PROVIDER_TRANSLATION'))
    define('COMPANY_PROVIDER_TRANSLATION',23);

if( !defined( 'DOCUMENTATION_URL'))
	define('DOCUMENTATION_URL','http://cmsjunkie.com/docs/jbusinessdirectory/');

if( !defined( 'LANGUAGE_RECEIVING_EMAIL'))
	define('LANGUAGE_RECEIVING_EMAIL','support@cmsjunkie.com');

if( !defined( 'CATEGORY_URL_NAMING'))
	define('CATEGORY_URL_NAMING','category');

if( !defined( 'OFFER_CATEGORY_URL_NAMING'))
	define('OFFER_CATEGORY_URL_NAMING','offer-category');

if( !defined( 'EVENT_CATEGORY_URL_NAMING'))
	define('EVENT_CATEGORY_URL_NAMING','event-category');

if( !defined( 'OFFER_URL_NAMING'))
	define('OFFER_URL_NAMING','offer');

if( !defined( 'EVENT_URL_NAMING'))
	define('EVENT_URL_NAMING','event');

if( !defined( 'CITY_URL_NAMING'))
	define('CITY_URL_NAMING','city');

if( !defined( 'REGION_URL_NAMING'))
	define('REGION_URL_NAMING','region');

if( !defined( 'CONFERENCE_URL_NAMING'))
	define('CONFERENCE_URL_NAMING','conference');

if( !defined( 'CONFERENCE_SESSION_URL_NAMING'))
	define('CONFERENCE_SESSION_URL_NAMING','session');

if( !defined( 'SPEAKER_URL_NAMING'))
	define('SPEAKER_URL_NAMING','speaker');

if( !defined( 'SESSION_ATTACHMENTS'))
	define('SESSION_ATTACHMENTS',4);

if( !defined( 'CONFERENCE_PICTURES_PATH') )
	define( 'CONFERENCE_PICTURES_PATH','/conferences/');

if( !defined( 'SESSION_LOCATION_PICTURES_PATH') )
	define( 'SESSION_LOCATION_PICTURES_PATH','/session_location/');

if( !defined( 'SPEAKER_PICTURES_PATH') )
	define( 'SPEAKER_PICTURES_PATH','/speakers/');

if( !defined( 'SESSION_PICTURES_PATH') )
	define( 'SESSION_PICTURES_PATH','/sessions/');

if( !defined( 'SESSION_ATTACHMENTS_PATH') )
	define( 'SESSION_ATTACHMENTS_PATH','/sessions/');

if( !defined( 'NEWS_REFRESH_PERIOD'))
	define( 'NEWS_REFRESH_PERIOD',7);

if( !defined( 'ATTRIBUTE_TYPE_BUSINESS'))
	define('ATTRIBUTE_TYPE_BUSINESS',1);

if( !defined( 'ATTRIBUTE_TYPE_OFFER'))
	define('ATTRIBUTE_TYPE_OFFER',2);

if( !defined( 'ATTRIBUTE_TYPE_EVENT'))
	define('ATTRIBUTE_TYPE_EVENT',3);

if( !defined( 'CATEGORY_TYPE_BUSINESS') )
	define( 'CATEGORY_TYPE_BUSINESS',1);

if( !defined( 'CATEGORY_TYPE_OFFER') ) 
	define( 'CATEGORY_TYPE_OFFER',2);

if( !defined( 'CATEGORY_TYPE_EVENT') )
	define( 'CATEGORY_TYPE_EVENT',3);

if( !defined( 'CATEGORY_TYPE_CONFERENCE') )
	define( 'CATEGORY_TYPE_CONFERENCE',4);

if( !defined( 'EVENT_BOOKING_CREATED') )
	define( 'EVENT_BOOKING_CREATED',"0");
if( !defined( 'EVENT_BOOKING_CONFIRMED') )
	define( 'EVENT_BOOKING_CONFIRMED',"1");
if( !defined( 'EVENT_BOOKING_CANCELED') )
	define( 'EVENT_BOOKING_CANCELED',"2");

if( !defined( 'EVENT_PAYMENT_STATUS_NOT_PAID') )
	define( 'EVENT_PAYMENT_STATUS_NOT_PAID',"0");
if( !defined( 'EVENT_PAYMENT_STATUS_PAID') )
	define( 'EVENT_PAYMENT_STATUS_PAID',"1");

if( !defined( 'REVIEW_STATUS_DISAPPROVED') )
    define( 'REVIEW_STATUS_DISAPPROVED',1);

if( !defined( 'REVIEW_STATUS_APPROVED') )
    define( 'REVIEW_STATUS_APPROVED',2);

if( !defined( 'REVIEW_STATUS_CREATED') )
    define( 'REVIEW_STATUS_CREATED',0);

if ( !defined('EMAIL_NOTIFICATION_PERIOD') )
    define('EMAIL_NOTIFICATION_PERIOD',6);

if ( !defined('EMAIL_BUSINESS_ADMINISTRATOR_URL') )
    define('EMAIL_BUSINESS_ADMINISTRATOR_URL','[business_admin_path]');

if ( !defined('EMAIL_REPORT_CAUSE') )
    define('EMAIL_REPORT_CAUSE', '[report_cause]');

if( !defined( 'RELATED_COMPANIES') )
    define( 'RELATED_COMPANIES',"company_related");

if( !defined( 'MEMBERSHIPS') )
    define( 'MEMBERSHIPS',"Memberships");

if( !defined( 'REVIEW_TYPE_BUSINESS'))
    define('REVIEW_TYPE_BUSINESS',1);
if( !defined( 'REVIEW_TYPE_OFFER'))
    define('REVIEW_TYPE_OFFER',2);

if( !defined( 'OFFER_ORDER_CREATED') )
	define( 'OFFER_ORDER_CREATED',"0");
if( !defined( 'OFFER_ORDER_CONFIRMED') )
	define( 'OFFER_ORDER_CONFIRMED',"1");
if( !defined( 'OFFER_ORDER_SHIPPED') )
	define( 'OFFER_ORDER_SHIPPED',"2");
if( !defined( 'OFFER_ORDER_COMPLETED') )
	define( 'OFFER_ORDER_COMPLETED',"3");

if( !defined( 'EVENT_APPOINTMENT_UNCONFIRMED') )
    define( 'EVENT_APPOINTMENT_UNCONFIRMED',"0");
if( !defined( 'EVENT_APPOINTMENT_CONFIRMED') )
    define( 'EVENT_APPOINTMENT_CONFIRMED',"1");
if( !defined( 'EVENT_APPOINTMENT_CANCELED') )
	define( 'EVENT_APPOINTMENT_CANCELED',"2");

if( !defined( 'CREATION_LISTING_NOTIFICATION') )
  define( 'CREATION_LISTING_NOTIFICATION',"1");

if( !defined( 'STATISTICAT_EMAIL_NOTIFICATION') )
  define( 'STATISTICAT_EMAIL_NOTIFICATION',"2");

if( !defined( 'UPGRADE_LISTING_NOTIFICATION') )
  define( 'UPGRADE_LISTING_NOTIFICATION',"3");

if( !defined( 'BUSINESS_VIEW_COUNT') )
  define( 'BUSINESS_VIEW_COUNT', '[business_view_count]');

if( !defined( 'BUSINESS_RATING') )
  define( 'BUSINESS_RATING', '[business_rating]');

if( !defined( 'BUSINESS_REVIEW_NUMBER') )
  define( 'BUSINESS_REVIEW_NUMBER', '[business_review_count]');

if( !defined( 'EVENTS_DETAILS') )
  define( 'EVENTS_DETAILS', '[events_detail]');

if( !defined( 'OFFER_DETAILS') )
  define( 'OFFER_DETAILS', '[offers_detail]');

if( !defined( 'BUSINESS_REVIEW') )
  define( 'BUSINESS_REVIEW', '[business_reviews]');

if( !defined( 'BUSINESS_PATH_CONTROL_PANEL') )
  define( 'BUSINESS_PATH_CONTROL_PANEL', '[link_business_control_panel]');

if ( !defined('ALLOWED_FILE_EXTENSIONS') )
	define('ALLOWED_FILE_EXTENSIONS', 'zip,rar,tgz,tar.gz,bmp,jpg,jpeg,png,gif,xml,css,csv,xls,xlsx,zip,txt,pdf,doc,docx,mp3,mp4,mov,wma');

if( !defined( 'REVIEWS_TERMS_CONDITIONS_TRANSLATION'))
  define('REVIEWS_TERMS_CONDITIONS_TRANSLATION',23);

if( !defined( 'CONTACT_TERMS_CONDITIONS_TRANSLATION'))
    define('CONTACT_TERMS_CONDITIONS_TRANSLATION',24);

if( !defined( 'STAFF_WORK_HOURS'))
    define('STAFF_WORK_HOURS',0);
if( !defined( 'STAFF_BREAK_HOURS'))
    define('STAFF_BREAK_HOURS',1);

if( !defined( 'STAFF_HOURS'))
    define('STAFF_HOURS',0);
if( !defined( 'BUSINESS_HOURS'))
    define('BUSINESS_HOURS',1);

if( !defined( 'EMAIL_REVIEW_LINK_OFFER') )
    define( 'EMAIL_REVIEW_LINK_OFFER','[review_offer_link]');

if( !defined( 'SERVICE_BOOKING_CREATED') )
    define( 'SERVICE_BOOKING_CREATED',"0");
if( !defined( 'SERVICE_BOOKING_CONFIRMED') )
    define( 'SERVICE_BOOKING_CONFIRMED',"1");
if( !defined( 'SERVICE_BOOKING_CANCELED') )
    define( 'SERVICE_BOOKING_CANCELED',"2");

if( !defined( 'TAX_DESCRIPTION_TRANSLATION'))
    define('TAX_DESCRIPTION_TRANSLATION',24);

if( !defined( 'MEMBERSHIP_DESCRIPTION_TRANSLATION'))
    define('MEMBERSHIP_DESCRIPTION_TRANSLATION',25);

if( !defined( 'PROJECT_DESCRIPTION_TRANSLATION'))
    define('PROJECT_DESCRIPTION_TRANSLATION',26);

if( !defined( 'EMAIL_TAX_DETAIL') )
    define( 'EMAIL_TAX_DETAIL','[tax_detail]');

if( !defined( 'STATISTIC_ITEM_BUSINESS'))
    define('STATISTIC_ITEM_BUSINESS',1);
if( !defined( 'STATISTIC_ITEM_OFFER'))
    define('STATISTIC_ITEM_OFFER',2);
if( !defined( 'STATISTIC_ITEM_EVENT'))
    define('STATISTIC_ITEM_EVENT',3);

if( !defined( 'STATISTIC_TYPE_VIEW'))
    define('STATISTIC_TYPE_VIEW',0);
if( !defined( 'STATISTIC_TYPE_CONTACT'))
    define('STATISTIC_TYPE_CONTACT',1);
if( !defined( 'STATISTIC_TYPE_SHARE'))
    define('STATISTIC_TYPE_SHARE',2);
if( !defined( 'STATISTIC_TYPE_WEBSITE_CLICK'))
    define('STATISTIC_TYPE_WEBSITE_CLICK',3);
if( !defined( 'REVIEW_PICTURES_PATH') )
    define( 'REVIEW_PICTURES_PATH','/reviews/');

if( !defined( 'ALLOW_TO_CONTRIBUTE') )
	define( 'ALLOW_TO_CONTRIBUTE','0');

if( !defined( 'LINK_FOLLOW') )
    define( 'LINK_FOLLOW',"link_follow");
if( !defined( 'SERVICES_LIST') )
    define( 'SERVICES_LIST',"services_list");
if( !defined( 'TESTIMONIALS') )
    define( 'TESTIMONIALS',"testimonials");

if( !defined( 'MEMBERSHIP_TYPE_1'))
    define('MEMBERSHIP_TYPE_1',1);
if( !defined( 'MEMBERSHIP_TYPE_2'))
    define('MEMBERSHIP_TYPE_2',2);
if( !defined( 'MEMBERSHIP_TYPE_3'))
    define('MEMBERSHIP_TYPE_3',3);

if( !defined( 'OFFER_TYPE_OFFER'))
    define('OFFER_TYPE_OFFER',1);
if( !defined( 'OFFER_TYPE_PRODUCT'))
    define('OFFER_TYPE_PRODUCT',2);
if( !defined( 'MAX_MEMBERSHIPS'))
    define('MAX_MEMBERSHIPS',21);

if( !defined( 'BOOKMARK_TYPE_BUSINESS'))
    define('BOOKMARK_TYPE_BUSINESS',1);
if( !defined( 'BOOKMARK_TYPE_OFFER'))
    define('BOOKMARK_TYPE_OFFER',2);
    
if( !defined( 'MAX_FILENAME_LENGTH') )
    define( 'MAX_FILENAME_LENGTH', 120);

if( !defined( 'ORDER_ALPHABETICALLY') )
    define( 'ORDER_ALPHABETICALLY', 1);

if( !defined( 'ORDER_BY_ORDER') )
    define( 'ORDER_BY_ORDER', 2);

if( !defined( 'ADDRESS_STREET_NUMBER') )
    define( 'ADDRESS_STREET_NUMBER', '{street_number}');
if( !defined( 'ADDRESS_ADDRESS') )
    define( 'ADDRESS_ADDRESS', '{address}');
if( !defined( 'ADDRESS_AREA') )
    define( 'ADDRESS_AREA', '{area}');
if( !defined( 'ADDRESS_CITY') )
    define( 'ADDRESS_CITY', '{city}');
if( !defined( 'ADDRESS_POSTAL_CODE') )
    define( 'ADDRESS_POSTAL_CODE', '{postal_code}');
if( !defined( 'ADDRESS_REGION') )
    define( 'ADDRESS_REGION', '{region}');
if( !defined( 'ADDRESS_PROVINCE') )
    define( 'ADDRESS_PROVINCE', '{province}');
if( !defined( 'ADDRESS_COUNTRY') )
    define( 'ADDRESS_COUNTRY', '{country}');

if( !defined( 'ITEMS_BATCH_SIZE') )
    define( 'ITEMS_BATCH_SIZE',1000);
?>