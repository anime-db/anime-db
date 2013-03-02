<?php

require_once ROOT.G_ROOT.'modules/form/classes/FormComplex.php';


/**
 * Компонент автозаполнения описания элимента каталога
 * 
 * @author	Peter Gribanov
 * @since	12.09.2011
 * @version	1.1
 */
class Guepard_CatalogItemAutoFill implements FormComplex {

	/**
	 * Enter description here ...
	 * 
	 * @var Form
	 */
	private $form;

	/**
	 * Enter description here ...
	 * 
	 * @var Guepard_CatalogItemAutoFillDriver
	 */
	private $driver;

	/**
	 * Enter description here ...
	 * 
	 * @var array
	 */
	private $lang = array();

	/**
	 * Enter description here ...
	 * 
	 * @var array
	 */
	private $params = array(
		'link'		=> '',
		'driver'	=> ''
	);
	


	/**
	 * Enter description here ...
	 * 
	 * @return void
	 */
	public function __construct($params){
		Page::setTitle('Автозаполнение');

		$this->lang = Main::getComponentLang('guepard:catalog.item.autofill');
		$this->params = array_merge($this->params, $params);

		if (!is_string($this->params['driver']) || !trim($this->params['driver']))
			throw new InvalidArgumentException('Not selected driver for autofill');

		$driver_name = 'Guepard_CatalogItemAutoFillDriver'.$this->params['driver'];

		if (!file_exists(dirname(__FILE__).'/'.$driver_name.'.php'))
			throw new LogicException('Selected driver autofill is not supported');

		require $driver_name.'.php';
		$this->driver = new $driver_name();
	}

	/**
	 * Устанавливает объект формы
	 * 
	 * @param Form $form
	 * @return void
	 */
	public function setForm(Form $form){
		$this->form = $form;
	}

	/**
	 * Enter description here ...
	 * 
	 * @throws FormFilterException
	 * @throws Exception
	 * @return void
	 */
	public function valid(){
		$this->form->valid();

		if ($this->form->getSentValue('external_id')){
			$this->confirmAutoFillData($this->form->getSentValue('external_id'));
		} elseif ($this->form->getSentValue('name')){
			$this->getIDByName($this->form->getSentValue('name'));
		} else {
			throw new Exception($this->lang['no_name_and_id']);
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

	/**
	 * Enter description here ...
	 * 
	 * @return boolen
	 */
	public function isAlreadySent(){
		return $this->form->isAlreadySent();
	}

	/**
	 * Enter description here ...
	 * 
	 * @throws Exception
	 * @return void
	 */
	public function getIDByName($name){
		if (!$this->driver->search($name))
			$this->exception(new Exception($this->lang['error_search']));

		if (($id=$this->driver->isDesiredResult())!==false){
			$this->confirmAutoFillData($id);
		} else {
			$out = $this->driver->getSearchResult();
			if (!$out){
				Main::getComponentTemplate('guepard:catalog.item.autofill', 'error.php', array(
					'title'	=> $this->lang['error_title'],
					'msg' 	=> sprintf($this->lang['error_search_nothing'], $name),
				));
				$this->draw();
			} else {
				Main::getComponentTemplate('guepard:catalog.item.autofill', 'search_list.php', array(
					'title'		=> $this->lang['error_search_results'],
					'list'		=> $out,
					'return'	=> $this->lang['error_search_return'],
				));
			}
		}
	}

	/**
	 * Enter description here ...
	 */
	public function loadForm(){
		Page::IncludeComponent('guepard:form', '', array(
			'form_name'		=> 'catalog_item_autofill'
		), $this);
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $id
	 * @return void
	 */
	public function confirmAutoFillData($id){
		$data = $this->driver->getData($id);
		if ($data){
			Page::IncludeComponent('guepard:form', '', array(
				'form_name'	=> 'catalog_item_added',
				'fill'		=> $data
			), $this);
			$this->form->setAction($this->params['added_link']);
			$this->draw();
		} else {
			Main::getComponentTemplate('guepard:catalog.item.autofill', 'error.php', array(
				'msg' => sprintf($this->lang['error_search_by_id'], $id)
			));
		}
	}

	/**
	 * Enter description here ...
	 * 
	 * @param Exception $e
	 * @return void
	 */
	public function exception(Exception $e){
		Main::getComponentTemplate('guepard:catalog.item.autofill', 'error.php', array(
			'title'	=> $this->lang['error_title'],
			'msg'	=> $e->getMessage(),
			'code'	=> $e->getCode(),
		));
	}

	/**
	 * Enter description here ...
	 * 
	 * @param string $msg
	 * @return void
	 */
	public function successful($msg=null){
		$this->form->clearSentValues();
		Main::getComponentTemplate('guepard:catalog.item.autofill', 'successful.php', array(
			'msg' => $this->lang[$msg ? $msg : 'successful']
		));
	}
}