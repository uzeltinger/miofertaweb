<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

/**
 * Model for Service Guest Details
 *
 */
class JBusinessDirectoryModelServiceGuestDetails extends JModelItem
{

    function __construct()
    {
        parent::__construct();
        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

    }

    function getGuestDetails()
    {
        $userData = CompanyUserDataService::getUserData();

        return $userData->buyerDetails;
    }

    function getServiceDetails()
    {
        $serviceData = CompanyUserDataService::getUserData();

        return $serviceData->serviceDetails;
    }
}
