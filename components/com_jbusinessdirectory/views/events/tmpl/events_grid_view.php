<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');

$calendarSource = html_entity_decode(JRoute::_('index.php?option=com_jbusinessdirectory&task=events.getCalendarEvents'));
require_once JPATH_COMPONENT_SITE.'/libraries/calendar/calendar.php';

?>