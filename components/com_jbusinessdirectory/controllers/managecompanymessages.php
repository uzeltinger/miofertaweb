<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'companymessages.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'companymessages.php');

class JBusinessDirectoryControllerManageCompanyMessages extends JBusinessDirectoryControllerCompanyMessages
{
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */

    function __construct()
    {
        parent::__construct();
        $this->log = Logger::getInstance();
    }

    /**
     * Removes an item
     */
    public function delete()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('id');

        $cid = intval($cid);

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__jbusinessdirectory_company_messages'))
            ->where('id = '. (int) $cid);

        $db->setQuery($query);
        $result = $db->execute();

        $this->setRedirect('index.php?option=com_jbusinessdirectory&view=managecompanymessages');
    }
}