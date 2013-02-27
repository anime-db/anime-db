<?php

/**
 * Класс описывает элемент ввода формы
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	12.09.2011
 * @version	1.2
 */
class FormSelect extends FormElement {

	/**
	 * Устанавливает параметры вывода
	 * 
	 * @param array $params
	 * @return FormSelect
	 */
	public function setViewParams($params=array()){
		// установка ключа
		if (!isset($this->options['view'][1]['use_key']))
			$this->options['view'][1]['use_key'] = isset($params['use_key']) ? $params['use_key'] : false;

		// заполнить опции интервалом чисел
		if (isset($params['optionsByRange'])){
			if (!is_array($params['optionsByRange']))
				throw new InvalidArgumentException('Range is not an array');
			if (count($params['optionsByRange'])<2)
				throw new InvalidArgumentException('Range shall consist of a start and end values');

			$params['options'] = range($params['optionsByRange'][0], $params['optionsByRange'][1]);
			$this->options['view'][1]['use_key'] = false;
		}

		// заполнить опции из sql запроса
		if (isset($params['optionsByQuery'])){
			if (!is_string($params['optionsByQuery']))
				throw new InvalidArgumentException('SQL request is not a string.');

			$db = FormDB::prepare($params['optionsByQuery']);

			$params['options'] = array();

			while ($option=$db->fetch())
				$params['options'][$option->key] = $this->options['view'][1]['use_key'] ? $option->value : $option->key;
		}

		return parent::setViewParams($params);
	}

	/**
	 * Метод для десериализации класса
	 *
	 * @param string $data
	 * @return FormSelect
	 */
	public function unserialize($data){
		parent::unserialize($data);
		$this->setViewParams($this->options['view'][1]);
		return $this;
	}

}