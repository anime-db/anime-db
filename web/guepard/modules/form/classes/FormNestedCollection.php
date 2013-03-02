<?php

/**
 * Вложенная коллекция элиментов формы
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	09.09.2011
 * @version	1.3
 */
class FormNestedCollection extends FormCollection {

	/**
	 * Устанавливает форму к которой пренадлежыт коллекция
	 * Метод предназначен для внутреннего использования
	 * 
	 * @param Form $form
	 * @return FormNestedCollection
	 */
	public function setForm(Form $form){
		parent::setForm($form);
		foreach ($this as $item){
			$item->setForm($form);
		}
		return $this;
	}

	/**
	 * Добавляет элемент
	 *
	 * @param FormItem $item
	 * @return FormNestedCollection
	 */
	public function add(FormItem $item){
		$this->items[] = $item;
		return $this;
	}

	/**
	 * Рисует коллекцию элиментов
	 * 
	 * @return void
	 */
	public function draw(){
		if (!$this->isEmpty()){
			include Form::getTemplatePath('nested.collection.php');
		}
	}

}