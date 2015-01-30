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
class HypercommentsViewJcomments extends JViewLegacy
{

	protected $items;
	public $sidebar;

	/**
	 * Method to display the current pattern
	 * @param type $tpl
	 */
	public function display( $tpl = null )
	{

		$this->loadHelper( 'hypercomments' );

		$this->config 		= JComponentHelper::getParams('com_hypercomments');
		$this->items  		= $this->get( 'Items' );
		$this->folder 		= JPATH_ROOT.'/components/com_hypercomments/xml';
		$this->url 			= JUri::root().'components/com_hypercomments/xml/';
		$this->pagination   = $this->get('Pagination');
		$this->state        = $this->get('State');
		$this->user 		= JFactory::getUser();

		$this->setToolBar();
		hypercommentsHelper::addSubmenu( 'jcomments' );
		$this->sidebar = JHtmlSidebar::render();
		parent::display( $tpl );
	}

	/**
	 * Method to display the toolbar
	 */
	protected function setToolBar()
	{
		JToolBarHelper::title( JText::_( 'COM_HYPERCOMMENTS_IMPORT' ) );
		$canDo = hypercommentsHelper::getActions( 'jcomments' );

		if ( $canDo->get( 'core.create' ) )
        {
			JToolBarHelper::addNew( 'file.add' );
		}

		if ( $canDo->get( 'core.delete' ) )
		{
			JToolBarHelper::deleteList( 'DELETE_QUERY_STRING', 'file.delete', 'JTOOLBAR_DELETE' );
			JToolBarHelper::divider();
		}

		if ( $canDo->get( 'core.manage' ) )
		{
			JToolBarHelper::unarchiveList( 'file.upload' , 'COM_HYPERCOMMENTS_IMPORT_START');
		}

		if ( $canDo->get( 'core.admin' ) )
		{
			JToolBarHelper::preferences( 'com_hypercomments' );
			JToolBarHelper::divider();
		}
	}
}