<?php
/**
 * @package    JBusinessDirectory
 * @subpackage  com_jbusinessdirectory
 *
 * @copyright   Copyright (C) 2007 - 2015 CMS Junkie. All rights reserved.
 * @license     GNU General Public License version 2 or later;
 */

defined('_JEXEC') or die('Restricted access');

/**
 * The HTML  View.
 */
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery.upload.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/joomlatabs.css');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/chosen.jquery.min.js');
JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/chosen.css');

JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/dropzone.js');
JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/dropzone/jbusinessImageUploader.js');

JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/dropzone.css');
JHTML::_('stylesheet', 	'components/com_jbusinessdirectory/assets/css/basic.css');

JHtml::_('stylesheet', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/ui-lightness/jquery-ui.css');

if(!defined('J_JQUERY_UI_LOADED')) {
    JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/jquery-ui.css');
    JHtml::_('script', 'components/com_jbusinessdirectory/assets/js/jquery-ui.js');

    define('J_JQUERY_UI_LOADED', 1);
}

JHtml::_('stylesheet', 'components/com_jbusinessdirectory/assets/css/font-awesome.css');

// following translations will be used in js
JText::script('LNG_IMAGE_SIZE_WARNING');

JBusinessUtil::includeValidation();

require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php';

class JBusinessDirectoryViewManageCompanyProject extends JViewLegacy
{
    protected $item;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null){

        $this->companies = $this->get('UserCompanies');
        $this->item	 = $this->get('Item');
        $this->state = $this->get('State');
        $this->translations = JBusinessDirectoryTranslations::getAllTranslations(PROJECT_DESCRIPTION_TRANSLATION,$this->item->id);
        $this->languages = JBusinessUtil::getLanguages();

        $this->appSettings = JBusinessUtil::getInstance()->getApplicationSettings();

        $this->actions = JBusinessDirectoryHelper::getActions();

        parent::display($tpl);
    }

}
