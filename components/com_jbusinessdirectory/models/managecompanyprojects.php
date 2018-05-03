<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
JTable::addIncludePath(DS . 'components' . DS . JRequest::getVar('option') . DS . 'tables');
require_once(JPATH_COMPONENT_ADMINISTRATOR . DS . 'models' . DS . 'projects.php');

class JBusinessDirectoryModelManageCompanyProjects extends JBusinessDirectoryModelProjects
{
    /**
     * JBusinessDirectoryModelManageCompanyServices constructor.
     */
    function __construct()
    {
        parent::__construct();
        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $this->_total = 0;
    }

    /**
     * Returns a Table object, always creating it
     *
     * @param   type    The table type to instantiate
     * @param   string    A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable    A database object
     */
    public function getTable($type = 'Projects', $prefix = 'JTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  string  An SQL query
     *
     * @since   1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        $companyIds = $this->getCompaniesByUserId();

        $ids = array();
        foreach ($companyIds as $company) {
            $ids[] = $company->id;
        }

        $ids = implode(",", $ids);

        // Select all fields from the table.
        $query->select($this->getState('list.select', 'pr.*'));
        $query->from($db->quoteName('#__jbusinessdirectory_company_projects') . ' AS pr');

        // Join over company table
        $query->select('cp.name as companyName');
        $query->join('LEFT', $db->quoteName('#__jbusinessdirectory_companies') . ' AS cp on cp.id = pr.company_id');

        // Join over the offer pictures
        $query->select('cpp.picture_path, cpp.picture_enable');
        $query->join('LEFT', $db->quoteName('#__jbusinessdirectory_company_projects_pictures').' AS cpp ON cpp.projectId=pr.id
				and
						(cpp.id in (
							select  min(pp1.id) as min from #__jbusinessdirectory_company_projects pr1
							left join  #__jbusinessdirectory_company_projects_pictures pp1 on pr1.id=pp1.projectId
							where pp1.picture_enable=1
							group by pr1.id
						)
					)
				
				');

        // Filter by selected company
        $companyId = $this->getState('filter.company_id');
        if (!empty($companyId)) {
            $query->where("pr.company_id = " . $companyId);
        }

        if(empty($ids))
            $ids = -1;
        $query->where("pr.company_id in (" . $ids . ")");

        $query->group('pr.id');

        // Add the list ordering clause.
        $query->order($db->escape($this->getState('list.ordering', 'pr.id')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

        return $query;
    }

    /**
     * Method to get all companies belonging to the logged user
     *
     * @return mixed
     */
    function getCompaniesByUserId()
    {
        $user = JFactory::getUser();
        $companiesTable = $this->getTable("Company");
        $companies = $companiesTable->getCompaniesByUserId($user->id);

        return $companies;
    }
}

?>