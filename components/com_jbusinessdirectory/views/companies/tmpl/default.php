<?php // no direct access
/**
 * @copyright	Copyright (C) 2008-2009 CMSJunkie. All rights reserved.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

$listing_layout = JRequest::getVar('listing_layout');
if(!empty($listing_layout)) {
    $this->appSettings->company_view = $listing_layout;
}

$tpl = '';
if($this->appSettings->company_view==1){
    $tpl = $this->loadTemplate("style_1");
}else if($this->appSettings->company_view==2) {
    $tpl = $this->loadTemplate("style_2");
}else if($this->appSettings->company_view==4){
    $tpl = $this->loadTemplate("style_4");
}else if($this->appSettings->company_view==5){
    $tpl = $this->loadTemplate("style_5");
}else if($this->appSettings->company_view==6){
    $tpl = $this->loadTemplate("style_6");
}else if($this->appSettings->company_view==7){
    $tpl = $this->loadTemplate("style_7");
}else{
    $tpl = $this->loadTemplate("style_3");
}

$user = JFactory::getUser();

if($user->id!=$this->company->userId && (empty($this->company) || $this->company->state == 0
    || $this->company->approved== COMPANY_STATUS_DISAPPROVED
    || ($this->company->approved== COMPANY_STATUS_CREATED && $this->appSettings->show_pending_approval == 0 )
    || ($this->appSettings->enable_packages && empty($this->package))
    || (!JBusinessUtil::checkDateInterval($this->company->publish_start_date, $this->company->publish_end_date, null, true, true)))){
    $tpl = $this->loadTemplate("inactive");
}

echo $tpl;