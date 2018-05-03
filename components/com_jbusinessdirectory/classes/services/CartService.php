<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');


JTable::addIncludePath('/administrator/components/com_jbusinessdirectory/tables');

class CartService
{
    /**
     * Method to initialize or retrieve the cartData in the session. If cartData is initialized, it will
     * return the data itself, and if not, it will initialize the data in the session and return an empty array.
     *
     * @return array|null
     */
    public static function initializeCartData()
    {
        $cartData =  isset($_SESSION['cartData'])?$_SESSION['cartData']:null;
        if(!isset($cartData)){
            $cartData = array();
            $_SESSION['cartData'] = $cartData;
        }

        return $cartData;
    }

    /**
     * Get's the offer id and the quantity for the offer that is being added to the cart, retrieves all the data
     * and creates a new object containing all the offer fields needed to be rendered on the cart view.
     *
     * After creating the object, it checks if the current offer is already on the session. If not, it creates the offer
     * in the session.
     * If the offer is already present, it updates the quantity of that offer by adding to it the new quantity.
     *
     * @param $offerId int Id of the offer
     * @param $quantity int Quantity of the offer
     * @return array|null
     */
    public static function addToCart($offerId, $quantity)
    {
        $offerTable = JTable::getInstance('Offer','JTable', array());
        $offer = $offerTable->getOffer($offerId);
        $cartData = self::initializeCartData();

        if(!isset($cartData['items'][$offerId])){
            $item = new stdClass();
            $item->name = $offer->subject;
            $item->description = $offer->short_description;
            $item->price = $offer->price;
            $item->quantity = $quantity;
            $item->total_quantity = $offer->quantity;
            if(!empty($offer->specialPrice))
                $item->price = $offer->specialPrice;

            $item->id = $offer->id;
            $item->companyId = $offer->companyId;
            $item->link = JBusinessUtil::getOfferLink($offer->id, $offer->alias);
            $item->currencyId = $offer->currencyId;
            $offerPictures = $offerTable->getOfferPictures($offerId);
            $item->picture = '/no_image.jpg';
            if(!empty($offerPictures))
                $item->picture = $offerPictures[0]->picture_path;

            $item->min_purchase = $offer->min_purchase;
            $item->max_purchase = $offer->max_purchase;

            $cartData['items'][$offerId] = $item;
        }
        else{
            $newQuantity = $cartData['items'][$offerId]->quantity + $quantity;

            // do not allow the user to add more items to the cart than the actual limit
            $maxAllowedQuantity = $offer->max_purchase<$offer->quantity?$offer->max_purchase:$offer->quantity;
            if($newQuantity <= $maxAllowedQuantity) {
                $cartData['items'][$offerId]->quantity = $newQuantity;
            }
            // if exceeded, the item quantity will be set equal to the limit, and the user cannot
            // add more than that
            else {
                $cartData['items'][$offerId]->quantity = $maxAllowedQuantity;
            }
        }

        $cartData = self::saveToSession($cartData);

        return $cartData;
    }

    /**
     * Method that removes the offer from the cartSession based on the offerId it receives.
     *
     * @param $offerId int Id of the offer that will be removed
     * @return array|null
     */
    public static function removeFromCart($offerId)
    {
        $cartData = self::initializeCartData();
        unset($cartData['items'][$offerId]);

        $cartData = self::saveToSession($cartData);

        return $cartData;
    }

    /**
     * Method that updates the quantity of an offer that is already in the session. Retrieves the offerId and quantity,
     * and updates the quantity for that particular offer
     *
     * @param $offerId int ID of the offer
     * @param $quantity int New quantity value that will be updated in the session
     * @return array|null
     */
    public static function editCartItem($offerId, $quantity)
    {
        $cartData = self::initializeCartData();
        $cartData['items'][$offerId]->quantity = $quantity;

        $cartData = self::saveToSession($cartData);

        return $cartData;
    }

    /**
     * Method to reset the session data for the cart items. Used for the empty cart functionality.
     *
     */
    public static function resetSession()
    {
        unset($_SESSION['cartData']);
    }

    /**
     * Method that retrieves the cart data from the session.
     *
     * @return null
     */
    public static function getCartData()
    {
        $cartData =  isset($_SESSION['cartData'])?$_SESSION['cartData']:null;

        return $cartData;
    }

    /**
     * Method that retrieves the cartData after changes have been performed onto them, calculates global properties for
     * all the items (and for each of them) like totalPrice, and saves them in the session
     *
     * @param $cartData array of objects that holds all cart data
     * @return mixed
     */
    private static function saveToSession($cartData)
    {
        $totalPrice = 0;
        $currencyId = 0;
        foreach($cartData['items'] as $item)
        {
            $totPrice = $item->price * $item->quantity;
            $item->totalPrice = JBusinessUtil::getPriceFormat($totPrice, $item->currencyId);
            $item->totPrice = $totPrice;

            $totalPrice += $totPrice;
            $currencyId = $item->currencyId;
        }

        $cartData["totalPrice"] = JBusinessUtil::getPriceFormat($totalPrice, $currencyId);

        $_SESSION['cartData'] = $cartData;

        return $cartData;
    }

    /**
     * Create the buyer details summary
     *
     * @param $buyerDetails object containing the buyer details
     * @return string
     */
    public static function getBuyerDetailsSummary($buyerDetails)
    {
        $result = "";
        $result.="<div class=\"offer-order-details\">";
        $result.="<table>";
        $result.="<tr><td colspan=2><strong>".JText::_("LNG_BUYER_DETAILS")."<strong></td></tr>";
        $result.="<tr><td>".JText::_("LNG_NAME")."</td><td>".$buyerDetails->first_name." ".$buyerDetails->last_name."</td></tr>";
        $result.="<tr><td>".JText::_("LNG_ADDRESS")."</td><td>". JBusinessUtil::getAddressText($buyerDetails)."</td></tr>";
        $result.="<tr><td>".JText::_("LNG_EMAIL")."</td><td>".$buyerDetails->email."</td></tr>";
        $result.="<tr><td>".JText::_("LNG_PHONE")."</td><td>".$buyerDetails->phone."</td></tr>";
        $result.="</table>";
        $result.="</div>";

        return $result;
    }

    /**
     * Create the purchased items summary
     *
     * @param $purchasedItems array containing the items that have been bought
     * @return string
     */
    public static function getPurchasedItemsSummary($purchasedItems)
    {
        $result = "";
        $result.="<div class=\"offer-order-items\">";
        $result.="<table style='padding:3px'>";
        $result .= "<tr><td colspan=2><strong>" . JText::_("LNG_ITEMS_DETAILS") . "<strong></td></tr>";
        $total = 0;
        foreach($purchasedItems as $item)
        {
            $result .= '<tr>';
            $result .= "<td>" . $item->name ."</td>";
            $result .= "<td>x" . $item->quantity ."</td>";
            $result .= "<td>" . JBusinessUtil::getPriceFormat($item->quantity * $item->price ,$item->currencyId) . "</td>";
            $result .= '</tr>';
            $total += $item->quantity * $item->price;
        }
        
        $result.="</table>";
        $result.="</div>";
        
        return $result;
    }

    /**
     * Method that saves all the order details and information in the database
     *
     * @param $orderDetails object containing all order details
     * @return bool|int false if there's an error saving the data, otherwise it returns the id of the order that was saved
     * @throws Exception
     */
    public static function saveOrder($orderDetails){
        $offerOrdersTable = JTable::getInstance('OfferOrders', 'JTable');
        
        // Create an order record on the table
        $offerOrdersTable->first_name = $orderDetails->buyerDetails->first_name;
        $offerOrdersTable->last_name = $orderDetails->buyerDetails->last_name;
        $offerOrdersTable->address = $orderDetails->buyerDetails->address;
        $offerOrdersTable->city = $orderDetails->buyerDetails->city;
        $offerOrdersTable->region = $orderDetails->buyerDetails->county;
        $offerOrdersTable->country = $orderDetails->buyerDetails->country_name;
        $offerOrdersTable->postal_code = $orderDetails->buyerDetails->postalCode;
        $offerOrdersTable->phone = $orderDetails->buyerDetails->phone;
        $offerOrdersTable->email = $orderDetails->buyerDetails->email;

        $totalPrice = 0;
        foreach($orderDetails->reservedItems as $item){
        	$totalPrice += $item["totalPrice"];
        }
        
        $offerOrdersTable->amount = $totalPrice;
        $offerOrdersTable->status = OFFER_ORDER_CREATED;

        if(!$offerOrdersTable->store()) {
            $application = JFactory::getApplication();
            $application->enqueueMessage($offerOrdersTable->getDbo()->getErrorMsg(), 'error');
            return false;
        }
        $orderId = $offerOrdersTable->id;
        // Save all the purchased offers data
        $orderProductsTable = JTable::getInstance('OfferOrderProducts', 'JTable');

        // iterate for each order group (orders are grouped by companies)
        foreach($orderDetails->reservedItems as $itemr){
            // iterate over each offer order
        	foreach($itemr as $key=>$item) {
        	    // skip 'totalPrice' array index, iterate only over the items
        	    if(is_numeric($key)) {
                    $orderProductsTable->offer_id = $item->id;
                    $orderProductsTable->order_id = $orderId;
                    $orderProductsTable->quantity = $item->quantity;
                    $orderProductsTable->price = $item->price;
                    $orderProductsTable->currencyId = $item->currencyId;
                    if (!$orderProductsTable->store()) {
                        $application = JFactory::getApplication();
                        $application->enqueueMessage($orderProductsTable->getDbo()->getErrorMsg(), 'error');
                        return false;
                    }
                }
            }
        }

        return $orderId;
    }

    /**
     * Method that groups all cart items by their company, organizes them in an array and returns it.
     *
     * @return array arranged array of cart items
     */
    public static function getItemsGroupedBySeller()
    {
        $arrangedItems = array();
        $items = self::getCartData();
        $items = $items['items'];

        foreach($items as $item)
        {
            $arrangedItems[$item->companyId][] = $item;
            if(!isset($arrangedItems[$item->companyId]['totalPrice'])){
                $arrangedItems[$item->companyId]['totalPrice'] = 0;
            }
            $arrangedItems[$item->companyId]['totalPrice'] += $item->totPrice;
        }

        return $arrangedItems;
    }
}