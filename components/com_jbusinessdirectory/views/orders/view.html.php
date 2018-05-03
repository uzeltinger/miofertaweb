<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewOrders extends JBusinessDirectoryFrontEndView
{

	function __construct()
	{
		parent::__construct();
	}
	
	
	function display($tpl = null)
	{
		$this->orders =  $this->get('Orders');
		$this->appSettings =  JBusinessUtil::getInstance()->getApplicationSettings();
		$this->pagination	= $this->get('Pagination');
		
		parent::display($tpl);
	}
}
?>
