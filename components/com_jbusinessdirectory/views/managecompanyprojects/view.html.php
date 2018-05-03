<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('script', 'components/com_jbusinessdirectory/assets/js/companies.js');

// following translations will be used in js
JText::script('COM_JBUSINESS_DIRECTORY_PROJECT_CONFIRM_DELETE');

require_once JPATH_COMPONENT_SITE.'/views/jbdview.php';

class JBusinessDirectoryViewManageCompanyProjects extends JBusinessDirectoryFrontEndView
{
    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');

        $this->actions = JBusinessDirectoryHelper::getActions();

        parent::display($tpl);
    }
}
?>
