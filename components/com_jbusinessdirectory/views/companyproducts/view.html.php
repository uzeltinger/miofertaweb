<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

defined('_JEXEC') or die('Restricted access');

class JBusinessDirectoryViewCompanyProducts extends JViewLegacy
{
    function __construct() {
        parent::__construct();
    }

    function display($tpl = null) {
        $this->productId = JRequest::getVar('productId');
        if(!empty($this->productId)) {
            $this->product = $this->get('Product');
            $tpl = 'details';
        }
        else
            $this->products = $this->get('Products');

        $this->category = $this->get('Category');

        parent::display($tpl);
    }
}
?>
