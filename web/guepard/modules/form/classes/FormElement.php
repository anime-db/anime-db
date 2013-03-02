<?php

require 'FormDB.php';

/**
 * Класс описывает элемент ввода формы
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	09.09.2011
 * @version	1.18
 */
class FormElement implements FormItem, Serializable {

	/**
	 * Опции поля
	 * 
	 * @var	array
	 */
	protected $options = array(
		'name'		=> '',		// Имя поля
		'title'		=> '',		// Заголовок для поля
		'comment'	=> '',		// Комментарий к полю
		'default'	=> '',		// Значение по умолчанию
		'view'		=> array('text', array()),	// Вид поля
		'filters'	=> array(),	// Фильтры проверки поля
		'required'	=> false,	// Обязательное для заполнения
	);

	/**
	 * Объект формы
	 * 
	 * @var	Form
	 */
	protected $form;

	/**
	 * Итератор запуска фильтров при проверки поля
	 * 
	 * @var	integer
	 */
	private $filter_iterator;


	/**
	 * Устанавливает форму к которой пренадлежыт коллекция
	 * Метод предназначен для внутреннего использования
	 * 
	 * @param Form $form
	 * @return FormElement
	 */
	public function setForm(Form $form){
		$this->form = $form;
		if ($this->options['required']){
			$this->form->required();
		}
		return $this;
	}

	/**
	 * Устанавливает имя поля
	 * 
	 * @param string $name
	 * @throws InvalidArgumentException
	 * @return FormElement
	 */
	public function setName($name){
		if (!is_string($name) || !trim($name))
			throw new InvalidArgumentException('Element name must be not empty string');

		$this->options['name'] = $name;
		return $this;
	}

	/**
	 * Возвращает имя поля
	 * 
	 * @return string
	 */
	public function getName(){
		return $this->options['name'];
	}

	/**
	 * Устанавливает заголовок для поля
	 * 
	 * @param string $title
	 * @throws InvalidArgumentException
	 * @return FormElement
	 */
	public function setTitle($title){
		if (!is_string($title) || !trim($title))
			throw new InvalidArgumentException('Element title must be not empty string');

		$this->options['title'] = $title;
		return $this;
	}

	/**
	 * Возвращает заголовок для поля
	 * 
	 * @return string
	 */
	public function getTitle(){
		return $this->options['title'];
	}

	/**
	 * Устанавливает комментарий для поля
	 * 
	 * @param string $comment
	 * @throws InvalidArgumentException
	 * @return FormElement
	 */
	public function setComment($comment){
		if (!is_string($comment) || !trim($comment))
			throw new InvalidArgumentException('Element comment must be not empty string');

		$this->options['comment'] = $comment;
		return $this;
	}

	/**
	 * Возвращает комментарий для поля
	 * 
	 * @return string
	 */
	public function getComment(){
		return $this->options['comment'];
	}

	/**
	 * Устанавливает значение поля
	 * 
	 * @param mixed $val
	 * @return FormElement
	 */
	public function setDefaultValue($val){
		$this->options['default'] = $val;
		return $this;
	}

	/**
	 * Возвращает значение поля
	 * 
	 * @return string
	 */
	public function getDefaultValue(){
		return $this->options['default'];
	}

	/**
	 * Возвращает значение поля
	 * 
	 * @return string
	 */
	public function getValue(){
		// значение указанное пользователем
		$value = & $this->getSentValue();

		// получение значения для checkbox
		if (is_bool($this->getDefaultValue())){
			if ($value=='on'){
				$value = true;
			} elseif ($value===null && $this->form->isAlreadySent()){
				$value = false;
			}
		}

		return $value!==null ? $value : $this->getDefaultValue();
	}

	/**
	 * Возвращает значение указанное пользователем
	 * 
	 * @return string
	 */
	public function & getSentValue(){
		return $this->form->getSentValue($this->getName());
	}

	/**
	 * Устанавливает вид для поля
	 * 
	 * @param string $name
	 * @param array $params
	 * @throws InvalidArgumentException
	 * @return FormElement
	 */
	public function setView($name, $params=null){
		if (!is_string($name) || !trim($name))
			throw new InvalidArgumentException('Element view name must be not empty string');

		$params = $params ? $params : array();
		$this->setViewParams($params);

		$this->options['view'][0] = $name;
		return $this;
	}

	/**
	 * Устанавливает параметры вывода
	 * 
	 * @param array $params
	 * @return FormElement
	 */
	public function setViewParams($params=array()){
		if (!is_array($params))
			throw new InvalidArgumentException('Element view parametrs should be an array');

		$this->options['view'][1] = array_merge($this->options['view'][1], $params);

		return $this;
	}

	/**
	 * Возвращает параметры вывода
	 * 
	 * @param string $param
	 * @return mixid
	 */
	public function getViewParams($param=null){
		if ($param===null){
			return $this->options['view'][1];
		} elseif (isset($this->options['view'][1][$param])){
			return $this->options['view'][1][$param];
		} else {
			return null;
		}
	}

	/**
	 * Выводит поле
	 * 
	 * @return void
	 */
	public function draw(){
		include Form::getTemplatePath('element.php');
	}

	/**
	 * Выводит поле
	 * 
	 * @return void
	 */
	public function drawField(){
		include Form::getTemplatePath('fields/'.$this->options['view'][0].'.php');
	}

	/**
	 * Устанавливает фильтр для поля
	 * 
	 * @param string $name
	 * @param array $params
	 * @throws InvalidArgumentException
	 * @return FormElement
	 */
	public function setFilter($name, $params=null){
		if (!is_string($name) || !trim($name))
			throw new InvalidArgumentException('Element filter name must be not empty string');

		$params = $params ? $params : array();
		if (!is_array($params))
			throw new InvalidArgumentException('Element filter parametrs should be an array');

		if (!file_exists(FORM_PATH.'/filters/'.$name.'.php'))
			throw new InvalidArgumentException('File of element filter ('.$name.') do not exists');

		$this->options['filters'][] = array($name, $params);
		// Обязательное для заполнения
		if ($name=='empty'){
			$this->required();
		}
		return $this;
	}

	/**
	 * Производит проверку переданных данных по полю 
	 * 
	 * @return void
	 */
	public function valid(){
		// не проверять отключенные поля 
		if (isset($this->options['view'][1]['disabled'])
			&& $this->options['view'][1]['disabled']) return;

		$this->filter_iterator = 0;
		while (isset($this->options['filters'][$this->filter_iterator])){
			$params = $this->options['filters'][$this->filter_iterator][1];
			include FORM_PATH.'/filters/'.$this->options['filters'][$this->filter_iterator][0].'.php';
			$this->filter_iterator++;
		}
		$this->filter_iterator = null;
	}

	/**
	 * Генерирует исключение при проверки поля фильтром
	 * 
	 * @param string $post
	 * @param array $params
	 * @throws LogicException
	 * @throws FormFilterException
	 * @return void
	 */
	public function error($post, $params=array()){
		if (!is_integer($this->filter_iterator)){
			throw new LogicException('Validate field is not running');
		}
		// добавление сообщения из языковой темы и название поля
		array_unshift($params, $this->getLangPost($post), $this->getTitle());
		// создание исключения
		throw new FormFilterException(call_user_func_array('sprintf', $params), $this,
			$this->options['filters'][$this->filter_iterator]);
	}

	/**
	 * Устанавливает что поле является обязательным для заполнения
	 * 
	 * @return FormElement
	 */
	public function required(){
		$this->options['required'] = true;
		if ($this->form){
			$this->form->required();
		}
		return $this;
	}

	/**
	 * Проверяет является ли поле обязательным для заполнения
	 * 
	 * @return boolen
	 */
	public function isRequired(){
		return $this->options['required'];
	}

	/**
	 * Возвращает сообщение из языковой темы
	 * 
	 * @param string $post
	 * @return string
	 */
	public function getLangPost($post){
		return $this->form->getLangPost($post);
	}

	/**
	 * Метод для сериализации класса
	 *
	 * @return string
	 */
	public function serialize(){
		return serialize($this->options);
	}

	/**
	 * Метод для десериализации класса
	 *
	 * @param string $data
	 * @return FormElement
	 */
	public function unserialize($data){
		$this->options = unserialize($data);
		return $this;
	}

}
?>