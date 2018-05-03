<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components/com_jbusinessdirectory/tables');

class JBusinessDirectoryModelOfferPayment extends JModelLegacy
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Populate state
     * @param unknown_type $ordering
     * @param unknown_type $direction
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication('administrator');

        $paymentMethod = JRequest::getVar('payment_method');
        $this->setState('payment.payment_method', $paymentMethod);

    }

    /**
     * Get payment methods
     *
     * @return multitype:unknown
     */
    function getPaymentMethods(){
        $paymentMethods = PaymentService::getPaymentProcessors();
        return $paymentMethods;
    }

    /**
     * Method that prepares the order details into a single object containing all the information needed
     *
     * $orderDetails->buyerDetails contains all the billing information
     * $orderDetails->reservedItems contains all items organized into an array in the following form:
     *
     * $reservedItems[] contains all company id's of the cart items
     * $reservedItems[companyId][] contains all the reserved items belonging to that specific company
     * $reservedItems[companyId]['totalPrice'] contains the total price for all the items that belong to that company
     *
     * @return stdClass
     */
    public function getOrder()
    {
        $orderDetails = new stdClass();

        $userData = OfferUserDataService::getUserData();
        $itemsDetails = CartService::getItemsGroupedBySeller();

        $orderDetails->buyerDetails = $userData->buyerDetails;
        $orderDetails->reservedItems = $itemsDetails;

        return $orderDetails;
    }

    /**
     * Prepares the summary with the billing info and details for the items that have been purchased
     *
     * @return stdClass
     */
    function getOrderDetails()
    {
        $orderDetails = new stdClass();

        $buyerDetails = OfferUserDataService::getUserData();
        $buyerDetails = $buyerDetails->buyerDetails;

        $itemsDetails = CartService::getCartData();
        $itemsDetails = $itemsDetails['items'];

        $cartData = CartService::getCartData();
        $orderDetails->buyerDetailsSummary = CartService::getBuyerDetailsSummary($buyerDetails);
        $orderDetails->itemsSummary = CartService::getPurchasedItemsSummary($itemsDetails);
        $orderDetails->itemsDetails = $itemsDetails;
        $orderDetails->buyerDetails = $buyerDetails;
        $orderDetails->totalPrice = $cartData["totalPrice"];

        return $orderDetails;
    }

    /**
     * Update offer order status
     *
     * @param object $paymentDetails
     */
    function updateOrderStatus($paymentDetails)
    {
        $offerOrdersTable = JTable::getInstance('OfferOrders','JTable', array());

        if($paymentDetails->status == PAYMENT_SUCCESS){
            $offerOrdersTable->updateOrderStatus($paymentDetails->order_id, OFFER_ORDER_CONFIRMED);
        }
    }

    /**
     * Method that prepares all the orders details once it's completed
     *
     * @return stdClass
     */
    function getCompleteOrderDetails($orderId=null){
    	if(empty($orderId)){
    		$orderId = JRequest::getVar("orderId");
    	}
      	
		$offerOrderTable = JTable::getInstance('OfferOrders','JTable', array());
		$offerOder = $offerOrderTable->getOrderDetails($orderId);

		$offerOrderProductsTable = JTable::getInstance('OfferOrderProducts','JTable', array());
		$offerOder->items = $offerOrderProductsTable->getItemsDetailsByOrder($orderId);
		
		$offerOder->buyerDetails = CartService::getBuyerDetailsSummary($offerOder);
		$offerOder->reservedItems = CartService::getPurchasedItemsSummary($offerOder->items);
		
		return $offerOder;
    }
}
?>