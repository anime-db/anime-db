<?php

/**
 * Класс описывает элемент ввода формы
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	07.09.2011
 * @version	1.1
 */
class FormText extends FormElement {
	

	/**
	 * Устанавливает фильтр для поля
	 * 
	 * @param string $name
	 * @param array $params
	 * @throws InvalidArgumentException
	 * @return FormElement
	 */
	public function setFilter($name, $params=null){
		if ($name=='length' && (isset($params['max']) || isset($params['eq'])))
			$this->setViewParams(array(
				'length' => isset($params['max']) ? $params['max'] : $params['eq']
			));
		
		return parent::setFilter($name, $params);
	}

}