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
class HypercommentsTableHypercomments extends JTable
{

	/**
	 * Class constructor
	 * @param Object $db (database link object)
	 */
	function __construct( &$db )
	{
		parent::__construct( '#__hypercomments', 'local_id', $db );
	}
}