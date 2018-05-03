<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'offercoupons.php');
require_once(JPATH_COMPONENT.DS.'libraries'.DS.'phpqrcode'.DS.'qrlib.php');
require_once(JPATH_COMPONENT.DS.'libraries'.DS.'tfpdf'.DS.'tfpdf.php');

class JBusinessDirectoryModelManageCompanyOfferCoupons extends JBusinessDirectoryModelOfferCoupons {

    function __construct(){
        parent::__construct();

        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');

        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
    }
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'OfferCoupons', $prefix = 'JTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	* Method that retrieves offer coupons that belong to the user
    *
	* @return object with data
	*/
	function getOfferCoupons() {
		// Load the data
		$user = JFactory::getUser();
		$offercouponsTable = $this->getTable("OfferCoupon");
		if (empty($this->_data)) {
			$this->_data = $offercouponsTable->getCouponsByUserId($user->id, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
	}

    function getTotal() {
        $user = JFactory::getUser();
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $offersTable = $this->getTable("OfferCoupon");
            $this->_total = $offersTable->getTotalUserOfferCoupons($user->id);
        }
        return $this->_total;
    }
}
?>