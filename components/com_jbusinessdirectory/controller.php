<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_SITE.DS.'assets'.DS.'defines.php'; 
require_once JPATH_COMPONENT_SITE.DS.'assets'.DS.'logger.php'; 
require_once JPATH_COMPONENT_SITE.DS.'assets'.DS.'utils.php'; 


class JBusinessDirectoryController extends JControllerLegacy
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display( $cachable = false,  $urlparams = array())
	{
		parent::display($cachable, $urlparams);
	}
}

?>