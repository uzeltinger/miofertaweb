<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 

defined('_JEXEC') or die('Restricted access');

class ProcessorFactory{

	//get processor instance based on the class name 
	function getProcessor($processorType){
		
		if($processorType==""){
			$processorType=PROCESSOR_CASH;
		}
		
		if (class_exists($processorType)){
			$processor = new $processorType();
		}else{ 
			throw new Exception("Processor $processorType does not exist");
		}
		
		return $processor;
	}
		
}