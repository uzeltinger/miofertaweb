<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');



JHTML::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/categories.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.opacityrollover.js');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/magnific-popup.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.magnific-popup.min.js');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/review.js');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/dropzone.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/jbusinessImageUploader.js');

JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/dropzone.css');
JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/map.js');


JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.upload.js');

if(!defined('J_JQUERY_UI_LOADED')) {
	JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
	JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');

	define('J_JQUERY_UI_LOADED', 1);
}

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/font-awesome.css');

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/offers.js');

// following translations will be used in js
JText::script('LNG_PLEASE_SELECT_QUANTITY');
JText::script('LNG_ADDING_PRODUCT_TO_SHOPPING_CART');
JText::script('LNG_BAD');
JText::script('LNG_POOR');
JText::script('LNG_REGULAR');
JText::script('LNG_GOOD');
JText::script('LNG_GORGEOUS');
JText::script('LNG_NOT_RATED_YET');
JText::script('LNG_IMAGE_SIZE_WARNING');


JBusinessUtil::includeValidation();

class JBusinessDirectoryViewOffer extends JViewLegacy {

	function __construct() {
		parent::__construct();
	}
	
	function display($tpl = null) {
		$this->appSettings =  JBusinessUtil::getInstance()->getApplicationSettings();
		$this->defaultAttributes = JBusinessUtil::getAttributeConfiguration();
		$offerId= JRequest::getVar('offerId');
		$this->assignRef('offerId', $offerId);
		$this->offer = $this->get('Offer');
        $this->offerAttributes = $this->get('OfferAttributes');
		$this->reviews = $this->get('Reviews');
		$this->videos = $this->get('OfferVideos');
		if ($this->appSettings->apply_attr_offers) {
			$this->videos = $this->defaultAttributes['video'] != ATTRIBUTE_NOT_SHOW ? $this->videos: array();
		}

		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, TERMS_CONDITIONS_TRANSLATION);
			JBusinessDirectoryTranslations::updateEntityTranslation($this->appSettings, REVIEWS_TERMS_CONDITIONS_TRANSLATION);
		}
		
		if(empty($this->offer) || !$this->offer->state){
			$tpl="inactive";
		}

		
		
		parent::display($tpl);
	}
}
?>
