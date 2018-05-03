<?php
/**
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');
JBusinessUtil::includeValidation();
/**
 * The HTML  View.
 */

class JBusinessDirectoryViewServiceGuestDetails extends JViewLegacy
{
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null){

        $this->state = $this->get('State');
        $this->guestDetails = $this->get('guestDetails');
        $this->serviceDetails = $this->get('serviceDetails');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }
}
