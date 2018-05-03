<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');
class JBusinessDirectoryControllerDirectoryRSS extends JControllerLegacy {
	
	/**
	 * constructor (registers additional tasks to methods)
	 * @return void
	 */
	function __construct() {
		parent::__construct();
	}

	public function getCompaniesRss() {
		$model = $this->getModel('DirectoryRSS');
		$companies = $model->getCompaniesRss();
		exit();
	}

	public function getEventsRss() {
		$model = $this->getModel('DirectoryRSS');
		$events = $model->getEventsRss();
		exit();
	}

	public function getOffersRss() {
		$model = $this->getModel('DirectoryRSS');
		$offers = $model->getOffersRss();
		exit();
	}
}