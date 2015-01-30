<?php

defined( '_JEXEC' ) or die;

/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
class HypercommentsHelper
{
	/**
	 * Добавление подменю
	 * @param String $vName
	 */
	static function addSubmenu( $vName )
	{
		JHtmlSidebar::addEntry(
			JText::_( 'COMMENTS_SUBMENU' ),
			'index.php?option=com_hypercomments&view=comments',
			$vName == 'comments' );
		JHtmlSidebar::addEntry(
			JText::_( 'JCOMMENTS_SUBMENU' ),
			'index.php?option=com_hypercomments&view=jcomments',
			$vName == 'jcomments' );
	}

	/**
	 * Получаем доступные действия для текущего пользователя
	 * @return JObject
	 */
	public static function getActions()
	{
		$user = JFactory::getUser();
		$result = new JObject;
		$assetName = 'com_hypercomments';
		$actions = JAccess::getActions( $assetName );
		foreach ( $actions as $action ) {
			$result->set( $action->name, $user->authorise( $action->name, $assetName ) );
		}
		return $result;
	}
}