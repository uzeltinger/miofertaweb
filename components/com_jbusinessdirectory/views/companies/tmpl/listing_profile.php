<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

if(!empty($this->company->userId) && isset($this->claimDetails) && ($this->claimDetails->status == 1) ){
    switch($appSettings->social_profile){
    	case 1:
    		require "profile_easysocial.php";
    		break;
    	case 2:
    		require "profile_jomsocial.php";
    		break;
    	case 3:
    		require "profile_cbuilder.php";
    		break;
    }
}

?>