<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.loadCss', true);

class JBusinessDirectoryViewInvoice extends JViewLegacy
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null)
	{
		$this->item =  $this->get('Invoice');
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		parent::display($tpl);
	}
}
?>
