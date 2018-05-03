<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved. 
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */ 
defined('_JEXEC') or die('Restricted access');



class JBusinessDirectoryControllerManageBookmarks extends JControllerLegacy
{
	/**
	 * Display the view
	 *
	 * @param   boolean			If true, the view output will be cached
	 * @param   array  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController		This object to support chaining.
	 * @since   1.6
	 */
	public function display($cachable = false, $urlparams = false){
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'ManageBookmarks', $prefix = 'JBusinessDirectoryModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function back(){
		$this->setRedirect('index.php?option=com_jbusinessdirectory');
	}
	
	/**
	 * Removes an item
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = JRequest::getVar('cid', array(), '', 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			JError::raiseWarning(500, JText::_('COM_JBUSINESSDIRECTORY_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel("ManageBookmark");

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);

			// Remove the items.
			if (!$model->delete($cid))
			{
				$this->setMessage($model->getError());
			} else {
			$this->setMessage(JText::plural('COM_JBUSINESS_DIRECTORY_N_ITEMS_DELETED', count($cid)));
			}
		}

		$this->setRedirect('index.php?option=com_jbusinessdirectory&view=managebookmarks');
	}

	/**
	 * Exports bookmarks in a csv file
	 *
	 * @since 4.9.0
	 */
	public function exportBookmarks()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('ManageBookmarks');
		$model->exportBookmarksCSV();
		exit;
	}

	/**
	 * Generates a PDF with the bookmarks
	 *
	 * @since 4.9.0
	 */
	public function generatePDF()
	{
		$model = $this->getModel('ManageBookmarks');
		$model->generatePDF();
		exit;
	}

	public function reOrderList(){
        $newOrder = JRequest::getVar('newOrder');
        $model = $this->getModel('ManageBookmarks');
        $result = $model->reOrderList($newOrder);

        /* Send as JSON */
        header("Content-Type: application/json", true);
        echo json_encode($result);
        exit;
    }
}
