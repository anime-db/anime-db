<?php

/**
 * Интерфейс хранения объекта формы
 * 
 * @author	Peter Gribanov
 * @since	06.09.2011
 * @version	1.0
 */
interface FormStoreInterface {

	/**
	 * Загружает форму
	 * 
	 * @param string $form_name
	 * @return Form
	 */
	public function loadForm($form_name);

	/**
	 * Сохряняет форму
	 * 
	 * @param Form $form
	 * @param string $form_name
	 * @return boolen
	 */
	public function saveForm(Form $form, $form_name);

}
