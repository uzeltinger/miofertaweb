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
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/library/category_lib.php');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');
/**
 * List Model.
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 */
class JBusinessDirectoryModelManageCompanies extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'bc.id',
				'registrationCode', 'bc.registrationCode',
				'address', 'bc.address',
				'type', 'ct.name',
				'viewCount', 'bc.viewCount',
				'contactCount', 'bc.contactCount',
				'state', 'bc.state',
				'approved', 'bc.approved'
			);
		}
		
		$this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
		
		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type	The table type to instantiate
	 * @param   string	A prefix for the table class name. Optional.
	 * @param   array  Configuration array for model. Optional.
	 * @return  JTable	A database object
	 */
	public function getTable($type = 'ManageCompany', $prefix = 'JTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		$packagesTable = $this->getTable("Package");
		$freePackage = $packagesTable->getFreePackage();
		
		// If empty or an error, just return.
		if (empty($items)) {
			return array();
		} else {
			foreach($items as $company) {

				$company->expired = false;

				if( !(!empty($company->active) && $company->active == 1) &&  ($this->appSettings->enable_packages && empty($freePackage)))
					$company->expired = true;

			    $company->checklist = JBusinessUtil::getCompletionProgress($company, 1);
			    $company->progress = 0;

			    if(count($company->checklist) > 0) {
                    // calculate percentage of completion
                    $count = 0;
                    $completed = 0;
                    foreach ($company->checklist as $key => $val) {
                        if ($val->status)
                            $completed++;
                        $count++;
                    }
                    $company->progress = (float)($completed / $count);
                }

                $company->progress = round($company->progress, 4);
			}
		}
		
		if($this->appSettings->enable_multilingual){
			JBusinessDirectoryTranslations::updateBusinessListingsTranslation($items);
		}
		
		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
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
		$app = JFactory::getApplication();
		$value = $app->input->get('limit', $app->getCfg('list_limit', 0), 'uint');
		$this->setState('list.limit', $value);
		// Create a new query object.
		$db = $this->getDbo();
		
		$query = "	SELECT bc.*,ct.name as typeName,cnt.country_name as country, clm.id as claimId, inv.state = 1 as paid,
   					(p.expiration_type=1 or (now() > (inv.start_date) and (now() < (inv.start_date + INTERVAL p.days DAY)) and inv.start_date!='0000-00-00') or (now() > (inv.start_trial_date) and (now() < (inv.start_trial_date + INTERVAL p.days DAY))) and inv.start_trial_date!='0000-00-00') as active, 
                    (inv.start_date + INTERVAL p.days DAY) as expirationDate, (now() < inv.start_date and inv.start_date!='0000-00-00') as futureDate,
   					p.name as packageName, p.id as packageId, p.expiration_type as packageType
					FROM `#__jbusinessdirectory_companies` AS bc
					LEFT JOIN `#__jbusinessdirectory_company_types` AS ct ON bc.typeId=ct.id
					LEFT JOIN `#__jbusinessdirectory_countries` AS cnt ON bc.countryId=cnt.id
					LEFT JOIN `#__jbusinessdirectory_company_claim` AS clm ON bc.id=clm.companyId
					LEFT JOIN #__jbusinessdirectory_packages p on bc.package_id=p.id and p.status=1
					left join (
						SELECT t1.* FROM `#__jbusinessdirectory_orders` t1
						JOIN (SELECT company_id, MAX(id) as id FROM `#__jbusinessdirectory_orders` where start_date <= DATE(now()) or start_trial_date <= DATE(now()) GROUP BY company_id ) t2
							 ON t1.company_id = t2.company_id AND t1.id = t2.id
						where t1.start_date <= DATE(now())  or t1.start_trial_date <= DATE(now())
					)inv on inv.package_id = p.id and inv.company_id = bc.id
				";

		$where = " where 1 ";
		
		$user = JFactory::getUser();
		$where.=' and bc.userId ='.$user->id;
		
		$groupBy = " group by bc.id";

		// Add the list ordering clause.
		$orderBy = " order by ". $db->escape($this->getState('list.ordering', 'bc.id')).' '.$db->escape($this->getState('list.direction', 'ASC'));
		
		$query = $query.$where;
		$query = $query.$groupBy;
		$query = $query.$orderBy;
		
		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');
		
		// Check if the ordering field is in the white list, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.ordercol', 'filter_order', $ordering);
		$this->setState('list.ordering', $value);

		// Check if the ordering direction is valid, otherwise use the incoming value.
		$value = $app->getUserStateFromRequest($this->context.'.orderdirn', 'filter_order_Dir', $direction);
		$this->setState('list.direction', $value);	

		// List state information.
		parent::populateState('bc.id', 'desc');
	}
	
	
	function getCompanyTypes(){
		$companiesTable = $this->getTable("Company");
		return $companiesTable->getCompanyTypes();
	}

	function getTotal(){
		// Load the content if it doesn't already exist
		if (empty($this->_total)) {
			$user = JFactory::getUser();
			$companiesTable = $this->getTable("Company");
			$this->_total = $companiesTable->getTotalListings($user->id);
		}
		return $this->_total;
	}
		
	function getStates(){
		$states = array();
		$state = new stdClass();
		$state->value = 0;
		$state->text = JTEXT::_("LNG_INACTIVE");
		$states[] = $state;
		$state = new stdClass();
		$state->value = 1;
		$state->text = JTEXT::_("LNG_ACTIVE");
		$states[] = $state;
	
		return $states;
	}
	
	function getStatuses(){
		$statuses = array();
		$status = new stdClass();
		$status->value = COMPANY_STATUS_CLAIMED;
		$status->text = JTEXT::_("LNG_NEEDS_CLAIM_APROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_CREATED;
		$status->text = JTEXT::_("LNG_NEEDS_CREATION_APPROVAL");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_DISAPPROVED;
		$status->text = JTEXT::_("LNG_DISAPPROVED");
		$statuses[] = $status;
		$status = new stdClass();
		$status->value = COMPANY_STATUS_APPROVED;
		$status->text = JTEXT::_("LNG_APPROVED");
		$statuses[] = $status;
	
		return $statuses;
	}
}
