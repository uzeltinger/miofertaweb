<?php
/**
 * @package    JBusinessDirectory
 *
 * @author CMSJunkie http://www.cmsjunkie.com
 * @copyright  Copyright (C) 2007 - 2017 CMS Junkie. All rights reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pagination');

class JBusinessDirectoryPagination extends JPagination{

    /**
     * Creates a dropdown box for selecting how many records to show per page.
     *
     * @return  string  The HTML for the limit # input box.
     *
     * @since   1.5
     */
    public function getLimitBox()
    {
        $limits = array();
        
        // Make the option list.
        for ($i = 5; $i <= 30; $i += 5)
        {
            $limits[] = \JHtml::_('select.option', "$i");
        }
        
        $limits[] = \JHtml::_('select.option', '50', \JText::_('J50'));
        $limits[] = \JHtml::_('select.option', '100', \JText::_('J100'));
        
        $selected = $this->viewall ? 0 : $this->limit;
        
        // Build the select list.
        if ($this->app->isClient('administrator'))
        {
            $html = \JHtml::_(
                'select.genericlist',
                $limits,
                $this->prefix . 'limit',
                'class="inputbox input-mini" size="1" onchange="Joomla.submitform();"',
                'value',
                'text',
                $selected
                );
        }
        else
        {
            $html = \JHtml::_(
                'select.genericlist',
                $limits,
                $this->prefix . 'limit',
                'class="inputbox input-mini" size="1" onchange="this.form.submit()"',
                'value',
                'text',
                $selected
                );
        }
        
        return $html;
    }
}
