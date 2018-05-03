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
 * Model for Offer Buyer Details.
 *
 */
class JBusinessDirectoryModelOfferBuyerDetails extends JModelItem
{

    function __construct()
    {
        parent::__construct();
        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

    }

    function getBuyerDetails()
    {
        $userData = OfferUserDataService::getUserData();

        return $userData->buyerDetails;
    }
}
