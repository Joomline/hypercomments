<?php

// No direct access
defined( '_JEXEC' ) or die;

/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

class HypercommentsModelNotify extends JModelLegacy
{
    private $table;

    function __construct($config = array())
    {
        $this->addTablePath(JPATH_ROOT.'/components/com_hypercomments/tables');
        $this->table = $this->getTable('Hypercomments', 'HypercommentsTable');
        parent::__construct($config);
    }

    public function streamMessage($data, $timestamp)
    {
        $data->date = date('Y-m-d h:i:s', $timestamp);
        $table = $this->table;
        $table->load();
        if (!$table->bind($data))
            return false;
        if (!$table->store())
            return false;
        return true;
    }

    public function streamEditMessage($data)
    {
        if(empty($data->id))
            return false;
        $table = $this->table;
        if (!$table->load(array('id' => $data->id)))
            return false;
        if (!$table->bind($data))
            return false;
        if (!$table->store())
            return false;
        return true;
    }

    public function streamRemoveMessage($data)
    {
        if(empty($data->id))
            return false;
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->delete('#__hypercomments')->where('`id` = '.$db->quote($data->id));
        return $db->setQuery($query)->execute();
    }

    public function checkSignature($signature,$data,$time)
    {
        $params = HypercommentsHelper::getComponentParams();
        $secret_key = $params->get('secret_key', '');
        return $signature == md5($secret_key.$data.$time);
    }
}