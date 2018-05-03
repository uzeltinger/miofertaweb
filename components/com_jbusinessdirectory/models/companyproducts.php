<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JBusinessDirectoryModelCompanyProducts extends JModelList
{
    /**
     * JBusinessDirectoryModelCompanyProducts constructor.
     *
     * @since 4.9.0
     */
    function __construct()
    {
        parent::__construct();

        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
    }

    /**
     * Returns a Table object, always creating it
     *
     * @param  $type   string  The table type to instantiate
     * @param  $prefix string  A prefix for the table class name. Optional.
     * @param  $config array   Configuration array for model. Optional.
     * @return JTable  A database object
     *
     * @since 4.9.0
     */
    public function getTable($type = 'Offer', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method that retrieves all products belonging to a specific category
     *
     * @return mixed
     *
     * @since 4.9.0
     */
    public function getProducts() {
        $table = $this->getTable();
        $details = array();

        $catId = JRequest::getVar('categoryId');
        if(!empty($catId))
            $catId = (int)$catId;

        $companyId = JRequest::getVar('companyId');
        if(!empty($companyId))
            $companyId = (int)$companyId;

        $categoryIds = array();
        $categoryIds[] = $catId;

        $details["type"] = OFFER_TYPE_PRODUCT;
        $details["categoriesIds"] = $categoryIds;
        $details["companyId"] = $companyId;
        $products = $table->getOffersByCategories($details);

        return $products;
    }

    /**
     * Method that retrieves the category based on it's ID
     *
     * @return null | $category
     *
     * @since 4.9.0
     */
    public function getCategory() {
        $catId = JRequest::getVar('categoryId');
        if(!empty($catId))
            $catId = (int)$catId;
        else
            return null;

        $table = $this->getTable('Category', 'JBusinessTable');
        $category = $table->getCategoryById($catId);

        return $category;
    }

    /**
     * Method to retrieve a product with all the needed details
     *
     * @return null | $product
     *
     * @since 4.9.0
     */
    public function getProduct() {
        $productId = JRequest::getVar('productId');
        if(!empty($productId))
            $productId = (int)$productId;
        else
            return null;

        $offersTable = $this->getTable();
        $product = $offersTable->getActiveOffer($productId);
        if(empty($product))
            return $product;

        $product->pictures = $offersTable->getOfferPictures($productId);

        return $product;
    }
}