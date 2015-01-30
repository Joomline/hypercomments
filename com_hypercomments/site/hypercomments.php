<?php
defined( '_JEXEC' ) or die; // No direct access
/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
$controller = JControllerLegacy::getInstance( 'hypercomments' );
$controller->execute( JFactory::getApplication()->input->get( 'task' ) );
$controller->redirect();