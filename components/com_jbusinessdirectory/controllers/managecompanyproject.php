<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');

JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'models');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'controllers'.DS.'project.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'project.php');
JTable::addIncludePath(DS.'components'.DS.JRequest::getVar('option').DS.'tables');

class JBusinessDirectoryControllerManageCompanyProject extends JBusinessDirectoryControllerProject
{
    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */

    function __construct()
    {
        parent::__construct();
        $this->log = Logger::getInstance();
    }


    /**
     * Dummy method to redirect back to standard controller
     *
     */
    public function display($cachable = false, $urlparams = false)
    {
        $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyprojects', false));
    }

    protected function allowEdit($data = array(), $key = 'id'){
        return true;
    }

    protected function allowAdd($data = array())
    {
        return true;
    }

    public function add()
    {
        $app = JFactory::getApplication();
        $context = 'com_jbusinessdirectory.edit.managecompanyproject';

        $result = parent::add();
        if ($result)
        {
            $this->setRedirect(JRoute::_('index.php?option=com_jbusinessdirectory&view=managecompanyproject'. $this->getRedirectToItemAppend(), false));
        }

        return $result;
    }


    /**
     * Method to cancel an edit.
     *
     * @param   string  $key  The name of the primary key of the URL variable.
     *
     * @return  boolean  True if access level checks pass, false otherwise.
     */
    public function cancel($key = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();
        $context = 'com_jbusinessdirectory.edit.managecompanyproject';
        $result = parent::cancel();

    }

    /**
     * Method to edit an existing record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key
     * (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if access level check and checkout passes, false otherwise.
     *
     */
    public function edit($key = null, $urlVar = null)
    {
        $app = JFactory::getApplication();
        $result = parent::edit();

        return true;
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function save($key = NULL, $urlVar = NULL){

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app      = JFactory::getApplication();
        $model = $this->getModel('project');
        $post = JRequest::get( 'post' );
        $post["pictures"] = $this->preparePictures($post);
        $post['description'] = JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWRAW);
        $recordId = $post["id"];
        $task     = $this->getTask();
        $context = 'com_jbusinessdirectory.edit.managecompanyproject';

        if (!$model->save($post)){
            // Save the data in the session.
            $app->setUserState('com_jbusinessdirectory.edit.project.data', $post);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

            return false;
        }

        $this->setMessage(JText::_('COM_JBUSINESSDIRECTORY_PROJECT_SAVE_SUCCESS'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':

                // Set the row data in the session.
                $recordId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $recordId);
                $app->setUserState('com_jbusinessdirectory.edit.project.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState('com_jbusinessdirectory.edit.project.data', null);

                // Redirect to the list screen.
                $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
                break;
        }

    }


    function preparePictures($post){
        //save images
        $pictures					= array();
        foreach( $post as $key => $value )
        {
            if(
                    strpos( $key, 'picture_info' ) !== false
                    ||
                    strpos( $key, 'picture_path' ) !== false
                    ||
                    strpos( $key, 'picture_enable' ) !== false
            ){
                foreach( $value as $k => $v )
                {
                    if( !isset($pictures[$k]) )
                        $pictures[$k] = array('picture_info'=>'', 'picture_path'=>'','picture_enable'=>1);
                    $pictures[$k][$key] = $v;
                }
            }
        }

        return $pictures;
    }

}