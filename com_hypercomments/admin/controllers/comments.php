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
class HypercommentsControllerComments extends JControllerAdmin
{

	function save_wwidget_id()
	{
		$app = JFactory::getApplication();

		$wid = $app->input->getInt('wid', 0);
		$secret_key = $app->input->getString('secret_key', '');

		$responce = array
		(
			'error' => 0,
			'msg' => 'Widget ID saved'
		);

		if($wid == 0)
		{
			$responce['error'] = 1;
			$responce['msg'] = 'Widget ID undefined';
		}
		else if($secret_key == '')
		{
			$responce['error'] = 1;
			$responce['msg'] = 'Secret key undefined';
		}
		else
		{
			// получаем параметры компонента
			$params = JComponentHelper::getParams('com_hypercomments');
			// устанавливаем требуемое значение
			$params->set('widget_id', $wid);
			$params->set('secret_key', $secret_key);
			// записываем измененные параметры в БД
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params') . '= ' . $db->quote((string)$params));
			$query->where($db->quoteName('element') . ' = ' . $db->quote('com_hypercomments'));
			$query->where($db->quoteName('type') . ' = ' . $db->quote('component'));
			$db->setQuery($query)->execute();

			if ($db->getErrorNum())
			{
				$responce['error'] = 1;
				$responce['msg'] = $db->getErrorMsg();
			}
		}
		echo json_encode($responce);
		$app->close();
	}
}