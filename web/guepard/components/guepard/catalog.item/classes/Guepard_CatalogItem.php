<?php

/**
 * Комплексный компонент элимента каталога
 * 
 * @author	Peter Gribanov
 * @since	10.09.2011
 * @version	1.0
 */
class Guepard_CatalogItem implements ComplexComponent {

	/**
	 * Сылка для подставления
	 * 
	 * @var string
	 */
//	private $link = '';

	/**
	 * Сылка для подставления
	 * 
	 * @var array
	 */
	private $params = array(
		'action_var_name'		=> 'act',
		'id_var_name'			=> 'id',
		'default_action'		=> '',
		'action_added'			=> 'add',
		'action_autofill'		=> 'autofill',
		'action_conversion'		=> 'conversion',
		'action_change'			=> 'change',
		'action_remove'			=> 'remove',
		'action_show'			=> 'show',
		'autofill_driver'		=> '',
		'action_link'			=> '?act=#action#',
		'item_link'				=> '?act=#action#&id=#id#',
		'default_item_link'		=> '?id=#id#',
	);


	/**
	 * Конструктор
	 * 
	 * @param array $params
	 * @return void
	 */
	public function __construct( & $params){
		$params = array_merge($this->params, $params);
		$this->params = $params;
	}

	/**
	 * Преобразовывает ссылку в паттерн
	 * 
	 * @param string $link
	 * @return string
	 */
	private function linkToPattern($link){
		$link = str_replace(
			array('?',  '/',  '.',  '#action#', '#id#'),
			array('\?', '\/', '\.', '([a-z]+)', '(\d+)'),
			$link
		);
		return '/'.$link.'/';
	}

	/**
	 * Проверяет установлен ли ID элимента каталога
	 * 
	 * @return boole
	 */
	public function getItemId(){
		if (preg_match($this->linkToPattern($this->params['item_link']), $_SERVER['REQUEST_URI'], $mat)){
			return $mat[2];
		} elseif (preg_match($this->linkToPattern($this->params['default_item_link']), $_SERVER['REQUEST_URI'], $mat)){
			return $mat[1];
		} else {
			$lang = Main::getComponentLang('guepard:catalog.item');
			Main::getComponentTemplate('guepard:catalog.item', 'error.php', array(
				'msg' => $lang['not_id']
			));
			return false;
		}/*

		if (empty($_GET[$this->params['id_var_name']])){
			$lang = Main::getComponentLang('guepard:catalog.item');
			Main::getComponentTemplate('guepard:catalog.item', 'error.php', array(
				'msg' => $lang['not_id']
			));
			return false;
		}
		return $_GET[$this->params['id_var_name']];*/
	}

	/**
	 * Enter description here ...
	 * 
	 * @return string
	 */
	public function getAction(){
		if (preg_match($this->linkToPattern($this->params['item_link']), $_SERVER['REQUEST_URI'], $mat)){
			$action = $mat[1];
		} elseif (preg_match($this->linkToPattern($this->params['action_link']), $_SERVER['REQUEST_URI'], $mat)){
			$action = $mat[1];
		} else {
			$action = $this->params['default_action'];
			if (!$this->params['default_action']){
				$lang = Main::getComponentLang('guepard:catalog.item');
				Main::getComponentTemplate('guepard:catalog.item', 'error.php', array('msg' => $lang['no_action']));
			}
		}
//		var_dump($mat);
//		var_dump($_SERVER['REQUEST_URI']);
/*
		$action = $this->params['default_action'];

		if (empty($_GET[$this->params['action_var_name']])){
			if (!$this->params['default_action']){
				$lang = Main::getComponentLang('guepard:catalog.item');
				Main::getComponentTemplate('guepard:catalog.item', 'error.php', array('msg' => $lang['no_action']));
			}
		} else {
			$action = $_GET[$this->params['action_var_name']];
		}*/
		return $action;
	}

	/**
	 * Подготавливает URL для работы в комплексных компонентах
	 * 
	 * @param string $url
	 * @return string
	 */
	public function prepareURL($action=null, $id=null){
		$link = ($id!==null ? 'item' : 'action').'_link';
		
		if ($action==$this->params['default_action'])
			$link = 'default_'.$link;

		$url = $this->params[$link];
		if ($action!==null)
			$url = str_replace('#action#', $action, $url);
		if ($id!==null)
			$url = str_replace('#id#', $id, $url);

		return $url;
	}

}