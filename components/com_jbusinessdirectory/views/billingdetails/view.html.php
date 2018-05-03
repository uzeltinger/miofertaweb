<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML  View.
 */
require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';
JBusinessUtil::includeValidation();

class JBusinessDirectoryViewBillingDetails extends JBusinessDirectoryFrontEndView
{
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null){
	
		$this->item	 = $this->get('Item');
		$this->state = $this->get('State');
        $this->company = $this->get('Company');
		$this->orderId = JRequest::getVar("orderId");
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);		
	}
}
