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
?>

<div class="successful-payment-wrapper">
    <i class="successful-payment-icon dir-icon-check-circle-o"></i>
    <h2><?php echo JText::_('LNG_THANK_YOU') ?>!</h2>
    <h4><?php echo JText::_('LNG_ORDERED_COMPLETED_SUCCESSFULLY'); ?></h4><br/>
    <div class="successful-payment-text"><?php echo JText::_('LNG_ORDERED_COMPLETED_SUCCESSFULLY_TXT'); ?></div>
    <br/>
</div>

<div class="row-fluid">
    <div class="span3 offset3">
        <h4><?php echo JText::_('LNG_ORDER_DETAILS'); ?></h4><hr/>
        <table>
            <tr>
                <td><?php echo $this->order->service?></td>
            </tr>
            <tr>
                <td><?php echo $this->order->description?></td>
            </tr>
            <tr>
                <td><?php echo JText::_('LNG_PACKAGE_START_DATE');?>: <?php echo JBusinessUtil::getDateGeneralShortFormat($this->order->start_date); ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_('LNG_ORDER_CREATED');?>: <?php echo JBusinessUtil::getDateGeneralShortFormat($this->order->created); ?></td>
            </tr>
            <?php if(!empty($this->order->discount_amount)) { ?>
            <tr>
                <td><?php echo JText::_('LNG_DISCOUNT');?>: <?php echo JBusinessUtil::getPriceFormat($this->order->discount_amount); ?></td>
            </tr>
            <?php } ?>
            <?php if(!empty($this->order->vat_amount)) { ?>
                <tr>
                    <td><?php echo JText::_('LNG_VAT');?>: <?php echo JBusinessUtil::getPriceFormat($this->order->vat_amount); ?></td>
                </tr>
            <?php } ?>
            <?php if(!empty($this->order->taxes)) { ?>
                <?php foreach ($this->order->taxes as $tax) { ?>
                    <tr>
                        <td>
                            <?php echo $tax->tax_name ?>  <?php echo $tax->tax_type==2?"( ".$tax->percentage."% )":""?>&nbsp;&nbsp;
                            <?php echo JBusinessUtil::getPriceFormat($tax->tax_amount); ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            <tr> <!-- blank row -->
                <td style="line-height:10px;">&nbsp;</td>
            </tr>
            <tr>
                <td><b><?php echo JText::_('LNG_TOTAL');?>: <?php echo JBusinessUtil::getPriceFormat($this->order->amount); ?></b></td>
            </tr>
        </table>
    </div>
    <div class="span3">
        <h4><?php echo JText::_('LNG_GUEST_DETAILS'); ?></h4><hr/>
        <table>
            <tr>
                <td><?php echo $this->order->billingDetails->first_name.' '.$this->order->billingDetails->last_name?></td>
            </tr>
            <?php if(!empty($this->order->billingDetails->company_name)) { ?>
            <tr>
                <td><?php echo JText::_('LNG_COMPANY_NAME'); ?>:<?php echo $this->order->billingDetails->company_name?></td>
            </tr>
            <?php } ?>
            <tr>
                <td><?php echo $this->order->billingDetails->address?></td>
            </tr>
            <tr>
                <td><?php echo $this->order->billingDetails->city.' '.$this->order->billingDetails->postal_code.', '.$this->order->billingDetails->country?></td>
            </tr>
            <tr>
                <td><?php echo JText::_('LNG_EMAIL'); ?>: <?php echo $this->order->billingDetails->email?></td>
            </tr>
            <tr>
                <td><?php echo JText::_('LNG_PHONE'); ?>: <?php echo $this->order->billingDetails->phone?></td>
            </tr>
        </table>
    </div>
</div>

<br/><br/>
<div class="successful-payment-wrapper">
    <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=orders') ?>" class="ui-dir-button ui-dir-button-green">
        <span class="ui-button-text"><?php echo JText::_('LNG_RETURN_TO_ORDERS') ?></span>
    </a>
    <br/><br/>
    <a href="<?php echo JRoute::_('index.php?option=com_jbusinessdirectory&view=search') ?>"><i class="dir-icon-reply"></i> <?php echo JText::_('LNG_BACK_TO_HOME') ?></a>
</div>
