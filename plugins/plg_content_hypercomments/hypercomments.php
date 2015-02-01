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
require_once JPATH_ROOT . '/components/com_hypercomments/helpers/hypercomments.php';


class PlgContentHypercomments extends JPlugin
{
	public function onContentAfterDisplay($context, &$article, $params, $page = 0)
	{
		$return = '';
		switch(JFactory::getApplication()->input->getCmd('option')){
			case 'com_content':
				$return = $this->getContentData($context, $article, $params, $page);
				break;
			case 'com_virtuemart':
				$return = $this->getVirtuemartData($context, $article, $params, $page);
				break;
			default:
				break;
		}
		return $return;
	}

	private function getContentData($context, &$article, $params, $page = 0)
	{
		$config = HypercommentsHelper::getComponentParams();

		if($this->params->get('allow_in_category',0))
		{
			$allowed_contexts = array
			(
				'com_content.category',
				'com_content.article',
				'com_content.featured'
			);
		}
		else
		{
			$allowed_contexts = array
			(
				'com_content.article'
			);
		}

		if (!in_array($context, $allowed_contexts))
		{
			return '';
		}

		// Return if we don't have a valid article id
		if (!isset($article->id) || !(int) $article->id)
		{
			return '';
		}

		$excludeCats = $this->params->get('exclude_categories');

		if(is_array($excludeCats) && count($excludeCats) && in_array($article->catid, $excludeCats)){
			return '';
		}

		if($context == 'com_content.article')
		{
			if(JString::strpos('{hc_off}', $article->text) !== false)
			{
				$article->text = JString::str_ireplace('{hc_off}', '', $article->text);
			}
			else
			{
				return HypercommentsHelper::getWidgetHtml('com_content', $article->id, $article->title);
			}
		}
		else
		{
			$counter_selector = $this->params->get('counter_selector');
			$counter_label = $this->params->get('counter_label');
			return HypercommentsHelper::getCounterWidgetHtml($counter_selector, $counter_label);
		}

		return '';
	}

	private function getVirtuemartData($context, &$article, $params, $page = 0)
	{
		$config = HypercommentsHelper::getComponentParams();

		if($this->params->get('vm_allow_in_category',0))
		{
			$allowed_contexts = array
			(
				'com_virtuemart.productdetails',
				'com_virtuemart.category'
			);
		}
		else
		{
			$allowed_contexts = array
			(
				'com_virtuemart.productdetails'
			);
		}

		if (!in_array($context, $allowed_contexts))
		{
			return '';
		}

		$excludeCats = $this->params->get('exclude_vm_categories');

		if(is_array($excludeCats) && count($excludeCats) && in_array($article->virtuemart_category_id, $excludeCats)){
			return '';
		}

		if($context == 'com_virtuemart.productdetails')
		{
			if(JString::strpos('{hc_off}', $article->product_desc) !== false)
			{
				$article->text = JString::str_ireplace('{hc_off}', '', $article->product_desc);
			}
			else
			{
				return HypercommentsHelper::getWidgetHtml('com_virtuemart', $article->virtuemart_product_id, $article->product_name);
			}
		}
		else
		{
			$counter_selector = $this->params->get('vm_counter_selector');
			$counter_label = $this->params->get('vm_counter_label');
			return HypercommentsHelper::getCounterWidgetHtml($counter_selector, $counter_label);
		}
	}
}
