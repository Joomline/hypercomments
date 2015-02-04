<?php
/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');


class hypercommentsControllerFile extends JControllerLegacy
{

	protected $folder = '';

	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_argensmedia'))
		{
			// User is not authorised
			JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'));
			return false;
		}

		return true;
	}

	public function delete()
	{
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$paths	= $this->input->get('cid', array(), 'array');
		$folder = $this->input->getString('folder', '');

		$this->setRedirect('index.php?option=com_hypercomments&view=jcomments');

		// Nothing to delete
		if (empty($paths))
		{
			return true;
		}

		// Authorize the user
		if (!$this->authoriseUser('delete'))
		{
			return false;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		$ret = true;

		foreach ($paths as $path)
		{
			if ($path !== JFile::makeSafe($path))
			{
				$this->setMessage(JText::_('COM_HYPERCOMMENTS_ERROR_UNABLE_TO_DELETE_FILE'), 'error');
				continue;
			}

			$fullPath = JPath::clean($folder.'/'.$path);

			if (is_file($fullPath))
			{
				$ret = JFile::delete($fullPath);
				$this->setMessage(JText::_('COM_HYPERCOMMENTS_DELETE_COMPLETE'));
			}

			$query->clear()
				->delete('#__hypercomments_export')
				->where('`filename` = '.$db->quote($path));
			$db->setQuery($query)->execute();
		}

		return $ret;
	}

	public function add()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$config = JComponentHelper::getParams('com_hypercomments');
		$model = $this->getModel('file');
		$data = $model->getData();

		if($data === false)
		{
			JError::raiseWarning(100, JText::_('COM_HYPERCOMMENTS_JCOMMENTS_NOT_FOUND'));
			JFactory::getApplication()->redirect('index.php?option=com_hypercomments&view=jcomments');
		}

		if(is_array($data) && count($data))
		{
			foreach($data as $k => $v)
			{
				$view = $this->getView("file", 'html');
				$view->setLayout("default");

				$view->data = $v;

				$html = '<?xml version="1.0" encoding="utf-8"?>'."\n";
				$html .= $view->loadTemplate();

				$filename = $v['object_group'].'_'.$k;
				$file = JPATH_ROOT . '/components/com_hypercomments/xml/'.$filename.'.xml';

				if(JFile::exists($file) && JFile::delete($file))
				{
					$query->clear()
						->delete('#__hypercomments_export')
						->where('`filename` = '.$db->quote($filename.'.xml'));
					$db->setQuery($query)->execute();
				}

				if(JFile::write($file, $html))
				{
					$query->clear()
						->insert('#__hypercomments_export')
						->columns('`filename`, `status`')
						->values($db->quote($filename.'.xml').', '.$db->quote('new'));
					$db->setQuery($query)->execute();
				}
			}
		}


		$errors = JFactory::getApplication()->getUserState('com_hypercomments.errors', array());

		foreach($errors as $error)
		{
			//$this->setMessage($error, 'error');
			JError::raiseWarning(100, $error);
		}

		JFactory::getApplication()->redirect('index.php?option=com_hypercomments&view=jcomments');
	}
}
