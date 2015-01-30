<?php

// No direct access
defined( '_JEXEC' ) or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
class HypercommentsModelJcomments extends JModelList
{

	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__hypercomments_export')
			->order('`status` ASC, `filename` ASC');
		return $query;
	}
}