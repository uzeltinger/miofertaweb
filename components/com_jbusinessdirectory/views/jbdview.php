<?php
/**
 * Main view responsable for creating the extension menu structure and admin template
 *
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die('Restricted access');

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/font-awesome.css');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/metisMenu.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/metisMenu.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/map.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jbd-template.css');


require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JBusinessDirectoryFrontEndView extends JViewLegacy{

    var $section_name="";
    var $section_description = "";

    function __construct($config = array()){
        parent::__construct($config);
        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();
        $this->section_name= JText::_("LNG_".strtoupper($this->_name));
        $this->section_description = JText::_("LNG_".strtoupper($this->_name)."_HEADER_DESCR");
    }

    /**
     * Generate the main display for extension views
     *
     * @param unknown_type $tpl
     */
    public function display($tpl = null)
    {
        $content = $this->loadTemplate($tpl);

        if ($content instanceof Exception)
        {
            return $content;
        }

        $input = JFactory::getApplication()->input;
        if($input->get('hidemainmenu')){
            echo $content;
            return;
        }

        $template = new stdClass();
        $template->content = $content;
        $template->menus = $this->generateMenu();
        if($this->appSettings->front_end_acl){
        	$template->menus = $this->checkAccessRights($template->menus);
        }
        $this->setActiveMenus($template->menus, $this->_name);

        //include the template and create the view
        $path = JPATH_SITE.'/components/com_jbusinessdirectory/theme/template.php';
        $templateFileExists = JFile::exists($path);

        $templateContent = $content;

        if($templateFileExists){
            ob_start();

            // Include the requested template filename in the local scope
            // (this will execute the view logic).
            include $path;

            // Done with the requested template; get the buffer and
            // clear it.
            $templateContent = ob_get_contents();
            ob_end_clean();
        }

        echo $templateContent;
    }

    /**
     * Check for selected menu and set it active
     *
     */
    private function setActiveMenus(&$menus, $view){
        foreach($menus as &$menu){
            if($menu["view"] == $view){
                $menu["active"] = true;
            }
            if(isset($menu["submenu"])){
                foreach($menu["submenu"] as &$submenu){
                    if($submenu["view"] == $view){
                        $submenu["active"] = true;
                        $menu["active"] = true;
                    }
                }
            }
        }
    }

    /**
     * Check the access rights for the menu items
     * @param unknown_type $menus
     */
    private function checkAccessRights($menus){
        $actions = JBusinessDirectoryHelper::getActions();

        foreach($menus as $i=>$menu){
            if(!$actions->get($menu["access"])){
                unset($menus[$i]);
                continue;
            }
            
            if(isset($menu["submenu"])){
                foreach($menu["submenu"] as $j=>$submenu){
                    if(!$actions->get($submenu["access"])){
                        unset($menu["submenu"][$j]);
                        continue;
                    }
                }
            }
        }

        return $menus;
    }

    /**
     * Build the menu items with all subments
     *
     */
    private function generateMenu(){
        $menus = array();

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_DASHBOARD'),
            "access"=> "directory.access.directory.management",
            "link" => "index.php?option=com_jbusinessdirectory&view=useroptions",
            "view" => "useroptions",
            "icon" => "dir-icon-th-large");
        $menus[] = $menuItem;

        $menuItem  = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES'),
            "access"=> "directory.access.listings",
            "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'),
            "view" => "managecompanies",
            "icon" => "dir-icon-tasks");

        $smenuItem = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANIES'),
            "access"=> "directory.access.directory.management",
            "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanies'),
            "view" => "managecompanies");
        $submenu[] = $smenuItem;

        if($this->appSettings->enable_services==1 && file_exists(JPATH_ADMINISTRATOR.'/components/com_jbusinessdirectory/models/companyservice.php')) {
            $smenuItem = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICES'),
                "access" => "directory.access.directory.management",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyservices'),
                "view" => "managecompanyservices");
            $submenu[] = $smenuItem;

            $smenuItem = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICE_PROVIDERS'),
                "access" => "directory.access.directory.management",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyserviceproviders'),
                "view" => "managecompanyserviceproviders");
            $submenu[] = $smenuItem;

            $smenuItem = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_SERVICE_RESERVATIONS'),
                "access" => "directory.access.listings",
                "link" => "index.php?option=com_jbusinessdirectory&view=managecompanyservicereservations",
                "view" => "managecompanyservicereservations");
            $submenu[] = $smenuItem;
        }

        /*
        $smenuItem  = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_PROJECTS'),
                "access"=> "directory.access.listings",
                "link" => "index.php?option=com_jbusinessdirectory&view=managecompanyprojects",
                "view" => "managecompanyprojects");
        $submenu[] = $smenuItem;
        */

        $smenuItem = array(
            "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_COMPANY_MESSAGES'),
            "access"=> "directory.access.listings",
            "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanymessages'),
            "view" => "managecompanymessages");
        $submenu[] = $smenuItem;

        $menuItem["submenu"] = $submenu;
        $menus[] = $menuItem;

        if($this->appSettings->enable_offers){
            $submenu = array();
            $menuItem  = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS'),
                "access"=> "directory.access.offers",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers'),
                "view" => "managecompanyoffers",
                "icon" => "dir-icon-certificate");

            $smenuItem  = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERS'),
                "access"=> "directory.access.offers",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffers'),
                "view" => "managecompanyoffers");
            $submenu[] = $smenuItem;


            if($this->appSettings->enable_offer_selling && file_exists(JPATH_ADMINISTRATOR.'/components/com_jbusinessdirectory/models/offerorder.php')){
                $smenuItem  = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFER_ORDERS'),
                    "access"=> "",
                    "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyofferorders'),
                    "view" => "managecompanyofferorders",
                    "icon" => "dir-icon-cog");
                $submenu[] = $smenuItem;
            }

            $menuItem["submenu"] = $submenu;
            $menus[] = $menuItem;
        }

        if($this->appSettings->enable_offer_coupons){
            $menuItem  = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_OFFERCOUPONS'),
                "access"=> "directory.access.offercoupons",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyoffercoupons'),
                "view" => "managecompanyoffercoupons",
                "icon" => "dir-icon-ticket");
            $menus[] = $menuItem;
        }
        
        
        if($this->appSettings->enable_events) {
            $menuItem = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS'),
                "access" => "directory.access.events",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents'),
                "view" => "managecompanyevents",
                "icon" => "dir-icon-calendar");

                $submenu = array();
                $smenuItem = array(
                    "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENTS'),
                    "access" => "directory.access.events",
                    "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyevents'),
                    "view" => "managecompanyevents");
                $submenu[] = $smenuItem;

                if($this->appSettings->enable_event_reservation && file_exists(JPATH_ADMINISTRATOR.'/components/com_jbusinessdirectory/models/eventticket.php')) {
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_TICKETS'),
                        "access" => "directory.access.eventtickets",
                        "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyeventtickets'),
                        "view" => "managecompanyeventtickets");
                    $submenu[] = $smenuItem;


                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_RESERVATIONS'),
                        "access" => "directory.access.eventreservations",
                        "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyeventreservations'),
                        "view" => "managecompanyeventreservations");
                    $submenu[] = $smenuItem;
                }

                if($this->appSettings->enable_event_appointments && file_exists(JPATH_ADMINISTRATOR.'/components/com_jbusinessdirectory/models/eventappointment.php')) {
                    $smenuItem = array(
                        "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_EVENT_APPOINTMENTS'),
                        "access" => "directory.access.eventappointments",
                        "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyeventappointments'),
                        "view" => "managecompanyeventappointments");
                    $submenu[] = $smenuItem;
                }
                
                $menuItem["submenu"] = $submenu;
           
            $menus[] = $menuItem;
        }


        if($this->appSettings->enable_packages){
            $menuItem  = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_ORDERS'),
                "access"=> "directory.access.listings",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=orders'),
                "view" => "orders",
                "icon" => "dir-icon-cog");
            $menus[] = $menuItem;
        }

        if($this->appSettings->enable_bookmarks){
            $menuItem  = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BOOKMARKS'),
                "access"=> "directory.access.bookmarks",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=managebookmarks'),
                "view" => "managebookmarks",
                "icon" => "dir-icon-bookmark-o");
            $menus[] = $menuItem;
        }

        if($this->appSettings->enable_packages){
            $menuItem  = array(
                "title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_BILLING_DETAILS'),
                "access"=> "directory.access.listings",
                "link" => JRoute::_('index.php?option=com_jbusinessdirectory&view=billingdetails&layout=edit'),
                "view" => "billingdetails",
                "icon" => "dir-icon-list-alt");
            $menus[] = $menuItem;
        }
        
        $userToken = JSession::getFormToken();
        $return = base64_encode(JURI::root());
        $menuItem  = array(
        		"title" => JText::_('COM_JBUSINESS_DIRECTORY_SUBMENU_LOGOUT'),
        		"access"=> "directory.access.listings",
        		"link" => JRoute::_('index.php?option=com_users&task=user.logout&'.$userToken.'=1&return='.$return),
        		"view" => "logout",
        		"icon" => "dir-icon-sign-out");
        $menus[] = $menuItem;
        
        return $menus;
    }

    public function setSectionDetails($name, $description){
        $this->section_name = $name;
        $this->section_description = $description;
    }
}