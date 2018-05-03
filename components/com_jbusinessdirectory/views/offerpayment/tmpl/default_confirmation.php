<?php // no direct accesss
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

// Layout for the offer order review

defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();

$ssl = 0;
if($this->appSettings->enable_https_payment)
	$ssl = 1;

$menuItemId="";
if(!empty($this->appSettings->menu_item_id)){
	$menuItemId = "&Itemid=".$this->appSettings->menu_item_id;
}
?>

<div>
	<h3>
		<?php echo JText::_("LNG_PAYMENT_PROCESSED_SUCCESSFULLY")?>
	</h3>
</div>

<div id="payment-details" class="offer-order-info">
	<div class="offer-order-details">
		<div class="order-guest-details ">
			<?php echo $this->order->buyerDetails?>
		</div>
		<div class="order-item-details ">
			<?php echo $this->order->reservedItems?>
		</div>
	</div>
</div>
