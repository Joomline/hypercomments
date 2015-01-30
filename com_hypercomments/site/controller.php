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
class HypercommentsController extends JControllerLegacy
{
	/**
	 * Methot to load and display current view
	 * @param Boolean $cachable
	 */
	function display( $cachable = false, $urlparams = array())
	{
		$this->default_view = 'ajax';
		parent::display( $cachable, $urlparams);
	}

}