<?php
/**
 * Hypercomments
 *
 * @version 	1.0
 * @author		Arkadiy Sedelnikov, JoomLine
 * @copyright	© 2015. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */
require_once JPATH_ROOT . '/components/com_hypercomments/helpers/hypercomments.php';

class ElementHypercomments extends Element implements iSubmittable {

	/*
		Function: hasValue
			Checks if the element's value is set.

	   Parameters:
			$params - render parameter

		Returns:
			Boolean - true, on success
	*/
	public function hasValue($params = array())
	{
		$value = $this->get('value', $this->config->get('default'));
		return !empty($value);
	}

	/*
		Function: render
			Override. Renders the element.

	   Parameters:
   			$params - render parameter

		Returns:
			String - html
	*/
	public function render($params = array())
	{
		if ($this->get('value', $this->config->get('default')))
		{
			if(JFactory::getApplication()->input->getCmd(task) == 'item')
			{
				return HypercommentsHelper::getWidgetHtml('com_zoo', $this->_item->id, $this->_item->name);
			}
			else
			{
				$item_route = JRoute::_($this->app->route->item($this->_item, false), true, -1);
				$selector = $this->config->get('counter_selector', '.zooHcCounter');
				$label = $this->config->get('counter_label');
				$html = '<a class="'.substr($selector, 1).'" data-xid="com_zoo_'.$this->_item->id.'" href="'.$item_route.'">Hypercomments</a>';
				return $html.HypercommentsHelper::getCounterWidgetHtml($selector, $label);
			}

		}
		return null;
	}

	/*
	   Function: edit
	       Renders the edit form field.

	   Returns:
	       String - html
	*/
	public function edit() {
		return $this->app->html->_('select.booleanlist', $this->getControlName('value'), '', $this->get('value', $this->config->get('default')));
	}

	/*
		Function: renderSubmission
			Renders the element in submission.

	   Parameters:
            $params - AppData submission parameters

		Returns:
			String - html
	*/
	public function renderSubmission($params = array()) {
        return $this->edit();
	}

	/*
		Function: validateSubmission
			Validates the submitted element

	   Parameters:
            $value  - AppData value
            $params - AppData submission parameters

		Returns:
			Array - cleaned value
	*/
	public function validateSubmission($value, $params) {
		return array('value' => $value->get('value'));
	}

}