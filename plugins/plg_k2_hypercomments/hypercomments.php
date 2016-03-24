<?php
/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\String\StringHelper;
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR . '/components/com_k2/lib/k2plugin.php');
require_once JPATH_ROOT . '/components/com_hypercomments/helpers/hypercomments.php';

class plgK2Hypercomments extends JPlugin
{

    // Some params
    var $pluginName = 'Hypercomments';
    var $pluginNameHumanReadable = 'Hypercomments';


    function onK2AfterDisplay(&$item, &$params, $limitstart)
    {
        if(JFactory::getApplication()->isAdmin())
        {
            return '';
        }

        $excludeCats = $this->params->get('exclude_categories');
        $input = JFactory::getApplication()->input;

        if (defined('HYPERCOMMENTS_COUNTER_LOADED')
            || $input->getString('view', '') == 'itemlist'
            || (is_array($excludeCats) && count($excludeCats) && in_array($item->catid, $excludeCats)))
        {
            return '';
        }

        if (StringHelper::strpos('{hc_off}', $item->fulltext) !== false)
        {
            $item->fulltext = StringHelper::str_ireplace('{hc_off}', '', $item->fulltext);
            return '';
        }
        else if (StringHelper::strpos('{hc_off}', $item->introtext) !== false){
            $item->introtext = StringHelper::str_ireplace('{hc_off}', '', $item->introtext);
            return '';
        }
        else
        {
            return HypercommentsHelper::getWidgetHtml('com_k2', $item->id, $item->title);
        }
    }


    function onK2CategoryDisplay(&$category, &$params, $limitstart)
    {
        if(JFactory::getApplication()->isAdmin() || !$this->params->get('allow_in_category'))
        {
            return '';
        }

        $excludeCats = $this->params->get('exclude_categories');

        if (is_array($excludeCats) && count($excludeCats) && in_array($category->id, $excludeCats)) {
            return '';
        }

        $selector = $this->params->get('counter_selector');
        $label = $this->params->get('counter_label');

        return HypercommentsHelper::getCounterWidgetHtml($selector, $label);
    }
}