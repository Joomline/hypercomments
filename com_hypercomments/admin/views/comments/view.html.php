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
class HypercommentsViewComments extends JViewLegacy
{
	public $params, $sidebar;

	public function display( $tpl = null )
	{
		$doc = JFactory::getDocument();
		$params = JComponentHelper::getParams('com_hypercomments');
		$config = JFactory::getConfig();

		$this->params = $params;
		$this->loadHelper( 'hypercomments' );

		$lang = (JFactory::getApplication()->getLanguage()->getTag() == 'ru-RU') ? 'ru' : 'en';
		$sitename = $config->get('sitename');
		$url = JUri::root();
		$notifyUrl = $url.'?option=com_hypercomments&task=notify.notify';
		$adminUrl = $url.'administrator/?option=com_hypercomments';
		$widget_id = $params->get('widget_id', '');
		$hcpUrl = ($widget_id != '') ? '//admin.hypercomments.com/comments/approve/'.$widget_id : '//admin.hypercomments.com';

		JHtml::_('jquery.framework');
		$doc->addStyleSheet(JUri::root().'administrator/components/com_hypercomments/assets/css/hypercomments.css');
		$doc->addScript(JUri::root().'administrator/components/com_hypercomments/assets/js/hypercomments.js');
		$doc->addScriptDeclaration("
			jQuery(document).ready(function(){
    			var posts = [];
    			var init_param = {
    				'widget_id'     : '$widget_id',
    				'hc_url'        : 'http://hypercomments.com',
					'hc_lang'       : '$lang',
					'hc_siteurl'    : '$url',
					'hc_blogname'   : '$sitename',
					'hc_notify_url' : '$notifyUrl',
					'hc_admin_url'  : '$adminUrl',
					'hc_posts'      : posts
				};
				HCmanage.init(init_param);
			});

			var _hcp = {};
			_hcp.append = '#hc_adm_widget';
			_hcp.height = jQuery(window).height() - 120;
			_hcp.url = document.location.protocol + '$hcpUrl';

			var hcc = document.createElement('script');
			hcc.type = 'text/javascript'; hcc.async = true;
	        hcc.src = document.location.protocol+'//widget.hypercomments.com/apps/js/hc0.js';
	        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(hcc, s.nextSibling);
		");
		$this->addToolbar();
		hypercommentsHelper::addSubmenu( 'comments' );
		$this->sidebar = JHtmlSidebar::render();
		parent::display( $tpl );
	}

	/**
	 * Method to display the toolbar
	 */
	protected function addToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_HYPERCOMMENTS' ) );
		$canDo = hypercommentsHelper::getActions( 'comment' );

		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( 'com_hypercomments' );
			JToolBarHelper::divider();
		}

	}
}