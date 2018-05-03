<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/offers.js');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_COUPONS_CONFIRM_DELETE');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageCompanyOfferCoupons extends JBusinessDirectoryFrontEndView {

	function __construct() {
		parent::__construct();
	}
	
	function display($tpl = null) {
		$this->offer_Id 	= $this->get('OfferId');
		$this->items		= $this->get('OfferCoupons');
		$this->pagination	= $this->get('Pagination');
		$this->total		= $this->get('Total');
		$this->actions = JBusinessDirectoryHelper::getActions();

		parent::display($tpl);
	}
}
?>
