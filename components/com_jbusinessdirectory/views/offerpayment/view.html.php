<?php
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

JBusinessUtil::includeValidation();

class JBusinessDirectoryViewOfferPayment extends JViewLegacy
{

    function __construct()
    {
        parent::__construct();
    }

    function display($tpl = null){

        $layout = JRequest::getVar('layout',null);
        if(isset($layout)){
            $tpl = $layout;
        }

        $this->paymentMethods =  $this->get('paymentMethods');
        $this->orderDetails = $this->get('orderDetails');
        $this->state = $this->get('State');
        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        
        if($tpl=="confirmation"){
        	$this->order = $this->get("CompleteOrderDetails");
        }

        parent::display($tpl);
    }

    function getPaymentMethodFormHtml($paymentMethod){
        return JText::_("LNG_PAYMENT_REDIRECT");
    }
}
?>
