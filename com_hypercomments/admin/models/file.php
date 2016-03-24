<?php

// No direct access
defined( '_JEXEC' ) or die;
require_once JPATH_ROOT.'/components/com_jcomments/jcomments.php';
use Joomla\String\StringHelper;

/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
class HypercommentsModelFile extends JModelList
{
	public function getData()
	{
		try
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('j.`id`, j.`parent`, j.`object_id`, j.`path`, j.`object_group`, j.`userid`,
				j.`name`, j.`username`, j.`email`, j.`comment`, j.`ip`, j.`isgood`, j.`ip`, j.`ispoor`, j.`date`')
				->select('o.`title`, o.`link`')
				->from('#__jcomments as j')
				->leftJoin('#__jcomments_objects as o USING ( `object_id` )')
				->where('j.published = 1')
			;
			$result = $db->setQuery($query)->loadObjectList();

			if(!is_array($result) || !count($result))
			{
				$result = false;
			}
			else
			{
				JPluginHelper::importPlugin('jcomments', 'avatar');
				$app = JFactory::getApplication();
				$app->triggerEvent('onPrepareAvatars', array(&$result));

				$tz = JFactory::getConfig()->get('offset');
				$url = rtrim(str_replace(array('http://', 'https://'), '', JUri::root()), '/');
				$tmp = array();

				foreach ($result as $v)
				{
					$v->avatar = $this->clearAvatar($v->avatar);

					$root = 0;
					if(!empty($v->path)){
						$path = explode(',', $v->path);
						$root = (int)$root;
					}

					if(!isset($tmp[$v->object_id]))
					{
						$tmp[$v->object_id] = array(
							'object_id' => $v->object_id,
							'object_group' => $v->object_group,
							'link' => $url.$v->link,
							'title' => $v->title,
							'comments' => array()
						);
					}
					$date = new JDate($v->date, $tz);
					$date = $date->format('D, d M Y H:i:s O', false, false);
					$tmp[$v->object_id]['comments'][] = array(
						'id' => $v->id,
						'root' => $root,
						'parent' => $v->parent,
						'userid' => $v->userid,
						'name' => $v->name,
						'username' => $v->username,
						'email' => $v->email,
						'ip' => $v->ip,
						'date' => $date,
						'avatar' => $v->avatar,
						'comment' => htmlspecialchars(strip_tags($v->comment)),
						'isgood' => $v->isgood,
						'ispoor' => $v->ispoor,
					);
				}
				$result = $tmp;
			}
		}
		catch (Exception $e)
		{
			$result = false;
		}

		return $result;
	}

	private function clearAvatar($avatar)
	{
		$return = '';
		if(empty($avatar))
		{
			return $return;
		}

		if(StringHelper::strpos($avatar, '<img') !== false)
		{
			preg_match_all('/<img[^>]+src=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/', $avatar, $matches);
			$return = !empty($matches[2]) && !empty($matches[2][0]) ? $matches[2][0] : '';
		}

		if(!empty($return) && StringHelper::strpos($return, '/administrator/') !== false)
		{
			$return = StringHelper::str_ireplace('/administrator/', '/', $return);
		}

		return $return;
	}
}

