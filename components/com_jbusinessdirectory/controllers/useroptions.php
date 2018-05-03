<?php
/*------------------------------------------------------------------------
# JBusinessDirectory
# author CMSJunkie
# copyright Copyright (C) 2012 cmsjunkie.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cmsjunkie.com
# Technical Support:  Forum - http://www.cmsjunkie.com/forum/j-businessdirectory/?p=1
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JBusinessDirectoryControllerUserOptions extends JControllerLegacy
{
    function __construct()
    {
        parent::__construct();
    }

    public function newCompanies() {
        $model = $this->getModel('UserOptions');
        $result = $model->getNewCompanies();

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }

    public function newOffers() {
        $model = $this->getModel('UserOptions');
        $result = $model->getNewOffers();

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }

    public function newEvents() {
        $model = $this->getModel('UserOptions');
        $result = $model->getNewEvents();

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }

}
?>