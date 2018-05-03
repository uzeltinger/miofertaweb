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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'companymessages.php');

class JBusinessDirectoryModelManageCompanyMessages extends JBusinessDirectoryModelCompanyMessages{

    function __construct()
    {
        parent::__construct();

        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }


    /**
     * Returns a Table object, always creating it
     *
     * @param   type	The table type to instantiate
     * @param   string	A prefix for the table class name. Optional.
     * @param   array  Configuration array for model. Optional.
     * @return  JTable	A database object
     */
    public function getTable($type = 'CompanyMessages', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     *
     * @return object with data
     */
    function getCompanyMessages()
    {
        // Load the data
        $companyMessagesTable = $this->getTable("CompanyMessages");
        if (empty( $this->_data ))
        {
            $this->_data = $companyMessagesTable->getUserCompanyMessages($this->getCompaniesByUserId(), $this->getState('limitstart'), $this->getState('limit'));
        }
        return $this->_data;
    }

    function getCompaniesByUserId(){
        $user = JFactory::getUser();
        $companiesTable = $this->getTable("Company");
        $companies =  $companiesTable->getCompaniesByUserId($user->id);
        $result = array();
        foreach($companies as $company){
            $result[] = $company->id;
        }
        return $result;
    }

    function getPagination()
    {
        // Load the content if it doesn't already exist
        $companyMessagesTable = $this->getTable("CompanyMessages");
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($companyMessagesTable->getTotalCompanyMessagesByUser($this->getCompaniesByUserId()),$this->getState('limitstart'), $this->getState('limit'));
        }
        return $this->_pagination;
    }
}