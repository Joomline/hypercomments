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
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldVMcategories extends JFormField
{

    public $type = 'vmcategories';

    protected function getInput()
    {
        if(!is_file(JPATH_ADMINISTRATOR.'/components/com_virtuemart/virtuemart.php'))
        {
            return '';
        }

        include_once JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php';

        VmConfig::loadConfig();
        $lang = VmConfig::$vmlang;

        $value = empty($this->value) ? array() : $this->value;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('c.virtuemart_category_id as id, cl.category_name as title, cc.category_parent_id as parent_id')
            ->from('#__virtuemart_categories as c')
            ->from('#__virtuemart_categories_'.$lang.' as cl')
            ->from('#__virtuemart_category_categories as cc')
            ->where('c.virtuemart_category_id = cl.virtuemart_category_id')
            ->where('c.virtuemart_category_id = cc.category_child_id ')
            ->where('c.published = 1')
            ->order('parent_id, title')
            ;
        $mitems = $db->setQuery($query)->loadObjectList();

        $children = array();
        if ($mitems)
        {
            foreach ($mitems as $v)
            {
                $pt = $v->parent_id;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
                $children[$pt] = $list;
            }
        }

        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);

        $mitems = array();
        foreach ($list as $item)
        {
            $item->treename = JString::str_ireplace('&#160;', '-', $item->treename);
            $mitems[] = JHTML::_('select.option', $item->id, '   '.$item->treename);
        }
        $output = JHTML::_('select.genericlist', $mitems, $this->name, 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $value);
        return $output;
    }
}