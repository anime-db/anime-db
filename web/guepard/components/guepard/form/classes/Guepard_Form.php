<?php
require_once ROOT.G_ROOT.'modules/form/FormFacade.php';
require_once 'FormStore.php';
require_once 'FormDBDriverGuepard.php';
require_once ROOT.G_ROOT.'modules/form/classes/FormComplex.php';

// устанавливается драйвер для работы формы с БД
FormDB::setDBDriver(new FormDBDriverGuepard());

/**
 * Компонент форм
 * 
 * @author	Peter Gribanov
 * @since	10.09.2011
 * @version	1.2
 */
class Guepard_Form {

	/**
	 * Enter description here ...
	 * 
	 * @var Form
	 */
	private $form;


	/**
	 * Enter description here ...
	 * 
	 * @param array $params
	 * @return void
	 */
	public function __construct($params){
		$this->form = FormStore::load($params['form_name']);

		if (isset($params['fill'])){
			if (!is_array($params['fill']))
				throw new InvalidArgumentException('Ffill list should be an array');

			$method = '_'.strtoupper($this->form->getMethod());
			$GLOBALS[$method] = array_merge($GLOBALS[$method], $params['fill']);
		}
	}

	/**
	 * Enter description here ...
	 * 
	 * @param FormComplex $component
	 * @return void
	 */
	public function setParentComponent(FormComplex $component){
		$component->setForm($this->form);
	}

	/**
	 * Enter description here ...
	 * 
	 * @return void
	 */
	public function valid(){
		if ($this->form->isAlreadySent()){
			try {
				$this->form->valid();
				echo '<p><strong>Форма заполнена правильно.</strong></p>';
			} catch (FormFilterException $e){
				echo '<p><strong>Ошибка: '.$e->getMessage().'</strong></p>';
			}
		}
	}

	/**
	 * Enter description here ...
	 * 
	 * @return void
	 */
	public function draw(){
		$this->form->draw();
	}

}