<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/owl.carousel.min.css');
JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/owl.theme.min.css');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/owl.carousel.min.js');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/magnific-popup.css');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.opacityrollover.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.magnific-popup.min.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/review.js');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/css/unite-gallery.css');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/themes/default/ug-theme-default.css');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/js/unitegallery.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/libraries/unitegallery/themes/default/ug-theme-default.js');

JHtml::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/jquery.timepicker.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.timepicker.min.js');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.steps.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery.steps.css');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/dropzone.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/jbusinessImageUploader.js');

JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/dropzone.css');

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.upload.js');

JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/imagesloaded.pkgd.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/jquery.isotope.min.js');
JHTML::_('script',  'components/com_jbusinessdirectory/assets/js/isotope.init.js');

if(!defined('J_JQUERY_UI_LOADED')) {
	JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
	JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');

	define('J_JQUERY_UI_LOADED', 1);
}

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/companies.js');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/map.js');



// following translations will be used in js
JText::script('LNG_BAD');
JText::script('LNG_POOR');
JText::script('LNG_REGULAR');
JText::script('LNG_GOOD');
JText::script('LNG_GORGEOUS');
JText::script('LNG_NOT_RATED_YET');
JText::script('LNG_HIDE_REVIEW_QUESTIONS');
JText::script('LNG_SHOW_REVIEW_QUESTIONS');
JText::script('LNG_READ_MORE');
JText::script('LNG_CLAIM_SUCCESSFULLY');
JText::script('LNG_ERROR_CLAIMING_COMPANY');
JText::script('LNG_YES');
JText::script('LNG_NO');
JText::script('LNG_PRODUCT_CATEGORIES');
JText::script('LNG_PRODUCTS');
JText::script('LNG_PRODUCT_DETAILS');
JText::script('LNG_SUBCATEGORIES');
JText::script('LNG_IMAGE_SIZE_WARNING');


JBusinessUtil::includeValidation();

class JBusinessDirectoryViewCompanies extends JViewLegacy
{
	private $defaultAttributes;

	function display($tpl = null)
	{
		$this->defaultAttributes = JBusinessUtil::getAttributeConfiguration();
		
		$tabId = JRequest::getVar('tabId');
		if(!isset($tabId))
			$tabId = 1;
		$this->tabId = $tabId;
			
		$this->company = $this->get('Company');
		$this->companyAttributes = $this->get('CompanyAttributes');
		$this->companyContactsEmail = $this->defaultAttributes['contact_person'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyContactsWithEmail'):array();
		$this->companyContacts = $this->defaultAttributes['contact_person'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyContacts'):array();
        $this->companyTestimonials = $this->defaultAttributes['testimonials'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyTestimonials'):array();
		$this->companyDepartments = $this->defaultAttributes['contact_person'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyDepartments'):array();
		$this->pictures = $this->defaultAttributes['pictures'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyImages'):array();
		$this->videos = $this->defaultAttributes['video'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanyVideos'):array();
		$this->sounds = $this->defaultAttributes['sounds'] != ATTRIBUTE_NOT_SHOW?$this->get('CompanySounds'):array();
		$this->offers = $this->get('CompanyOffers');
		$this->events = $this->get('CompanyEvents');
		$this->realtedCompanies = $this->get('RelatedCompanies');
		$this->associatedEvents = $this->get('AssociatedEvents');
        $this->memberships = $this->get('CompanyMemberships');
		//$this->services = $this->get('Services');
		//$this->services_list = $this->defaultAttributes['services_list'] != ATTRIBUTE_NOT_SHOW?$this->get('ServicesList'):array();
		$this->reviews = $this->get('Reviews');
		$this->reviewCriterias = $this->get('ReviewCriterias');
		$this->reviewQuestions = $this->get('ReviewQuestions');
		$this->reviewAnswers = $this->get('ReviewQuestionAnswers');
        //$this->companyProjects = $this->get('CompanyProjects');
        //$this->productCategories = $this->get('ProductCategories');
        $this->claimDetails = $this->get('ClaimDetails');


        $this->rating = $this->get('UserRating');
		$this->ratingCount = $this->get('RatingsCount');
		//$this->userCompany = $this->get('UserCompanies');
		$this->viewCount = $this->get('ViewCount');
		$this->package = $this->get('package');
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, REVIEWS_TERMS_CONDITIONS_TRANSLATION);
		}
		
		if($this->appSettings->enable_packages && !empty($this->package)){
			$this->videos = array_slice($this->videos,0, $this->package->max_videos);
			$this->pictures = array_slice($this->pictures,0, $this->package->max_pictures);
		}
		
		$maxAttach = !empty($this->package) && $this->appSettings->enable_packages ?$this->package->max_attachments :$this->appSettings->max_attachments;
		if(!empty($this->company->attachments))
			$this->company->attachments = array_slice($this->company->attachments,0, $maxAttach);
		
		$session = JFactory::getSession();
		$this->location = $session->get('location');

		$layout = JRequest::getVar('layout');
		if(!empty($layout)) {
			$tpl = $layout;
			if($layout == 'default') {
				$tpl = null;
			}
		}
		
		parent::display($tpl);
	}
}
?>
