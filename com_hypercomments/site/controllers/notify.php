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

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once JPATH_ROOT.'/components/com_hypercomments/helpers/hypercomments.php';

class HypercommentsControllerNotify extends JControllerLegacy
{
    function notify()
    {
        $params = HypercommentsHelper::getComponentParams();
        $app = JFactory::getApplication();

        if(!$params->get('sync', 1))
        {
            $app->close();
        }

        $model = $this->getModel('Notify', 'HypercommentsModel');
        $error = 0;
        $msg = array();
        $data = $app->input->getString('data', '');
        $signature = $app->input->getString('signature', '');
        $time = $app->input->getInt('time', 0);

        $encodedData = json_decode($data, false);

        $options = array(
            'format' => '{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}',
            'text_file' => 'com_hypercomments.php'
        );

        JLog::addLogger($options, JLog::ERROR, array('jerror'));

        if(empty($data))
        {
            $msg[] = 'Empty data';
            $error = 1;
        }
        if(empty($signature))
        {
            $msg[] = 'Empty signature';
            $error = 1;
        }
        if(empty($time))
        {
            $msg[] = 'Empty time';
            $error = 1;
        }

        if(!isset($encodedData[0]) || !is_object($encodedData[0]))
        {
            ob_start();
            var_dump($encodedData);
            $ed = ob_get_clean();
            $msg[] = 'Error encoding data. Data = '.$data.' encodedData = '.$ed;
            $error = 1;
        }

        if(empty($encodedData[0]->id))
        {
            $msg[] = 'Empty message ID';
            $error = 1;
        }

        if(!$error)
        {
           if(!$model->checkSignature($signature,$data,$time))
           {
               $msg[] = 'Signature wrong';
               $error = 1;
           }
        }

        if(!$error)
        {
            switch($encodedData[0]->cmd){
                case 'streamMessage' :
                    if(!$model->streamMessage($encodedData[0], $time))
                    {
                        $msg[] = 'Error add comment';
                        $error = 1;
                    }
                    break;
                case 'streamEditMessage' :
                    if(!$model->streamEditMessage($encodedData[0]))
                    {
                        $msg[] = 'Error edit comment';
                        $error = 1;
                    }
                    break;
                case 'streamRemoveMessage' :
                    if(!$model->streamRemoveMessage($encodedData[0]))
                    {
                        $msg[] = 'Error remove comment';
                        $error = 1;
                    }
                    break;
                default:
                    break;
            }
        }

        if($error == 1 && count($msg))
        {
            $msg = implode(';  ', $msg);
            JLog::add($msg, JLog::ERROR, 'jerror');
        }

        $app->close();
    }

    function delete_xml()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $filename = $app->input->getCmd('xml', '');
        $filename = str_replace(array('../','./',), '', trim($filename));

        if(empty($filename))
        {
            echo 'fail';
        }
        else
        {
            $filePath = JPATH_ROOT . '/components/com_hypercomments/xml/'.$filename;

            $query->delete('#__hypercomments_export')
                ->where('`filename` = '.$db->quote($filename));
            $db->setQuery($query)->execute();

            if(JFile::delete($filePath))
            {
                echo 'ok';
            }
            else
            {
                echo 'fail';
            }
        }
        $app->close();
    }

    function changeXMLstatus()
    {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $filename = $app->input->getCmd('xml', '');

        $query->update('#__hypercomments_export')
            ->set('`status` = '.$db->quote('sent'))
            ->where('`filename` = '.$db->quote($filename));
        if($db->setQuery($query)->execute())
        {
            echo 'ok';
        }
        else{
            echo 'fail';
        }
        $app->close();
    }
}