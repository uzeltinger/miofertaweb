<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controllerform');

/**
 * The Offer Buyer Details Controller
 *
 */
class JBusinessDirectoryControllerOfferBuyerDetails extends JControllerForm
{
    /**
     * Dummy method to redirect back to standard controller
     *
     */
    public function display($cachable = false, $urlparams = false)
    {
        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=offerbuyerdetails', false));
    }

    /**
     * Method to add a new record.
     *
     * @return  mixed  True if the record can be added, a error object if not.
     */
    public function add()
    {
        $app = JFactory::getApplication();
        $context = 'com_jbusinessdirectory.edit.offerbuyerdetails';

        $result = parent::add();
        if ($result)
        {
            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=offerbuyerdetails'. $this->getRedirectToItemAppend(), false));
        }

        return $result;
    }


    /**
     * Method to cancel an edit.
     *
     * @param   string  $key  The name of the primary key of the URL variable.
     *
     * @return  boolean  True if access level checks pass, false otherwise.

     */
    public function cancel($key = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $eventId = $this->state->get('offerbuyerdetails.offer_id');
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=offer&id='.$eventId, false));
    }

    /**
     * Method to edit an existing record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key
     * (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if access level check and checkout passes, false otherwise.
     *
     */
    public function edit($key = null, $urlVar = null)
    {
        $app = JFactory::getApplication();
        $result = parent::edit();

        return true;
    }

    /**
     * Method to save buyer details (and redirect to main page)
     *
     * @return void
     */
    function addBuyerDetails()
    {
        // Check for security token.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = JRequest::get("post");
        OfferUserDataService::addBuyerDetails($data);

        $appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $ssl = 0;
        if($appSettings->enable_https_payment)
            $ssl = 1;

        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=offerpayment', false, $ssl));
    }
}
