<?php

require 'FormItem.php';
require 'FormCollection.php';

/**
 * Класс описывает форму и позволяет ее динамически составлять
 * 
 * @license GNU GPL Version 3
 * @copyright 2009, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	FormManager
 * @author	Peter Gribanov
 * @since	14.09.2011
 * @version	3.27
 */
class Form implements Serializable {

	/**
	 * Опции формы
	 * 
	 * @var	array
	 */
	private $options = array(
		'action'		=> '',		// Адрес обработчика формы
		'method'		=> 'post',	// Метод передачи данных
		'name'			=> '',		// Название формы
		'required'		=> false,	// Есть поля обязательны для заполнения
		'submit_title'	=> '',		// Заголовок для кнопки отправки формы
		'buttons'		=> array(),	// Список кнопок у формы
	);

	/**
	 * Шаблон вида формы
	 * 
	 * @var	string
	 */
	private static $template = '.default';

	/**
	 * Список элементов
	 *
	 * @var	FormCollection
	 */
	private $collection;

	/**
	 * Список переданных параметров
	 * 
	 * @var	array
	 */
	private $inputs = array();

	/**
	 * Список загруженных сообщений активной языковой темы
	 * 
	 * @var	array
	 */
	private $lang_posts = array();


	/**
	 * Конструктор
	 *
	 * @return	void
	 */
	public function __construct(){
		$this->collection = new FormCollection();
		$this->collection->setForm($this);
		$this->setMethod('post');
		$this->loadLangPosts();
	}

	/**
	 * Вставляет один или более элементов в конце списка
	 *
	 * @param FormItem $item
	 * @return Form
	 */
	public function add(FormItem $item){
		$this->collection->add($item);
		return $this;
	}

	/**
	 * Разбирает строку запроса и добавляет скрытые поля с переменными из запроса
	 * Пример строки запроса: a=foo&b=bar
	 *
	 * @param string $query
	 * @throws InvalidArgumentException
	 * @return Form
	 */
	public function addByQuery($query){
		if (!$query) return $this;

		$query = explode('&', $query);
		foreach ($query as $var){
			if (substr_count($var, '=') != 1)
				throw new InvalidArgumentException('Cant add element because of improper URL query');

			$var = explode('=', $var);
			$this->add(FormFacade::Hidden($var[0])->setDefaultValue($var[1]));
		}
		return $this;
	}

	/**
	 * Вставляет кнопку на форму
	 *
	 * @param string $title
	 * @param array $params
	 * @return Form
	 */
	public function addButton($title, $params=null){
		if (!is_string($title) || !trim($title))
			throw new InvalidArgumentException('Title of button should not be an empty string');
		if ($params && !is_array($params))
			throw new InvalidArgumentException('Button parametrs should be an array');
		if (isset($params['type']) && !in_array($params['type'], array('button', 'reset', 'submit')))
			throw new InvalidArgumentException('Unsupported type of button');

		$this->options['buttons'][] = array($title, $params ? $params : array());
		return $this;
	}

	/**
	 * Рисует кнопку на форму
	 * 
	 * @return void
	 */
	public function drawButtons(){
		foreach ($this->options['buttons'] as $button)
			$this->drawButton($button[0], $button[1]);
	}

	/**
	 * Рисует кнопку на форму
	 * 
	 * @param string $title
	 * @param array $params
	 * @return void
	 */
	private function drawButton($title, $params=array()){
		include self::getTemplatePath('fields/button.php');
	}

	/**
	 * Производит проверку всех полей
	 * Псевдоним для FormCollection::valid()
	 *
	 * @return void
	 */
	public function valid(){
		$this->collection->valid();
	}

	/**
	 * Возвращает коллекцию элиментов формы
	 * 
	 * @return FormCollection
	 */
	public function getCollection(){
		return $this->collection;
	}

	/**
	 * Устанавливает флаг что есть поля обязательные для заполнения
	 * Метод предназначен для внутреннего использования
	 * 
	 * @return void
	 */
	public function required(){
		$this->options['required'] = true;
	}

	/**
	 * Проверяет есть ли поля обязательные для заполнения
	 * 
	 * @return boolen
	 */
	public function isRequired(){
		return $this->options['required'];
	}

	/**
	 * Возвращает значение указанное пользователем
	 * 
	 * @param string $name
	 * @return string
	 */
	public function & getSentValue($name){
		return $this->inputs[$name];
	}

	/**
	 * Очищает отправленные данные
	 * 
	 * @return Form
	 */
	public function clearSentValues(){
		$method = '_'.strtoupper($this->options['method']);
		unset($GLOBALS[$method]);
		// для корректной работы методов isAlreadySent
		// создается не пустой массив
		$GLOBALS[$method] = array(0);
		$this->inputs = & $GLOBALS[$method];
		return $this;
	}

	/**
	 * Форма уже отправлена
	 * 
	 * @return boolen
	 */
	public function isAlreadySent(){
		if (!isset($_SERVER['HTTP_REFERER'])
			|| !count($GLOBALS['_'.strtoupper($this->options['method'])])
			// должен быть установлен уникальный ключ,
			// но он не обнаружен в полученных данных
			|| ($this->options['method'] == 'get'
				&& $this->getSentValue('unique_key_already_sent')
					!=='4ab24a54898e90ea76f23afc36a81819')){

			return false;
		}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * отключено, так как принимает формы только с той же страницы, с которой они были отправлены
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		// получение тикущей страници
		$current = ($_SERVER['SERVER_PROTOCOL'][4]=='S' ? 'https' : 'http').'://'
			.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

		$referer = $_SERVER['HTTP_REFERER'];

		// игнорировать GET параметры при отправке формы методом GET
		if ($this->options['method'] == 'get'){
			list($current, ) = explode('?', $current.'?', 2);
			list($referer, ) = explode('?', $referer.'?', 2);
		}

		return $current==$referer;
*/

		// получение тикущего хотса
		$current = ($_SERVER['SERVER_PROTOCOL'][4]=='S' ? 'https' : 'http').'://'
			.$_SERVER['HTTP_HOST'].'/';
		// разрешен прием форм в пределах одного хоста
		return strpos($_SERVER['HTTP_REFERER'], $current)!==false;
	}

	/**
	 * Устанавливает шаблон вида формы
	 * 
	 * @param string $template
	 * @throws InvalidArgumentException
	 * @return Form
	 */
	public function setTemplate($template){
		if (!is_string($template) || !trim($template))
			throw new InvalidArgumentException('Display form must be not empty string');

		if (!file_exists(FORM_PATH.'/templates/'.$template.'/template.php'))
			throw new InvalidArgumentException('File of display ('.$template.') form do not exists');

		self::$template = $template;
		return $this;
	}

	/**
	 * Возвращает шаблон вида формы
	 * 
	 * @return string
	 */
	public function getTemplate(){
		return self::$template;
	}

	/**
	 * Выводит форму по шаблону
	 * 
	 * @return void
	 */
	public function draw(){
		include self::getTemplatePath('template.php');
	}

	/**
	 * Возвращает реальный путь к шаблону
	 * 
	 * @param string $path
	 * @return string
	 */
	public static function getTemplatePath($path){
		if (file_exists(FORM_PATH.'/templates/'.self::$template.'/'.$path)){
			return FORM_PATH.'/templates/'.self::$template.'/'.$path;
		} elseif (file_exists(FORM_PATH.'/templates/.default/'.$path)){
			return FORM_PATH.'/templates/.default/'.$path;
		} else {
			throw new InvalidArgumentException('Template file ('.$path.') do not exists');
		}
	}

	/**
	 * Возвращает сообщение из языковой темы
	 * 
	 * @param string $post
	 * @throws InvalidArgumentException
	 * @return string
	 */
	public function getLangPost($post){
		if (!isset($this->lang_posts[$post]))
			throw new InvalidArgumentException('Selected message is not found in the language theme');

		return $this->lang_posts[$post];
	}

	/**
	 * Устанавливает адрес обработчика формы
	 *
	 * @param string $action
	 * @throws InvalidArgumentException
	 * @return Form
	 */
	public function setAction($action){
		if (!is_string($action) || !trim($action))
			throw new InvalidArgumentException('Form action must be not empty string');

		$this->options['action'] = $action;
		return $this;
	}

	/**
	 * Возвращает адрес обработчика формы
	 *
	 * @return string
	 */
	public function getAction(){
		return $this->options['action'];
	}

	/**
	 * Устанавливает метод передачи данных
	 *
	 * @param string $method
	 * @throws UnexpectedValueException
	 * @return Form
	 */
	public function setMethod($method){
		$method = strtolower($method);
		if (!in_array($method, array('post', 'get')))
			throw new UnexpectedValueException('Form method must be POST or GET');

		$this->options['method'] = $method;
		if ($method=='post'){
			$this->inputs = & $_POST;
		} else {
			$this->inputs = & $_GET;
			// добавление скрытого поля
			$this->add(
				FormFacade::Hidden('unique_key_already_sent')
					->setDefaultValue('4ab24a54898e90ea76f23afc36a81819')
			);
		}
		return $this;
	}

	/**
	 * Возвращает метод передачи данных
	 *
	 * @return string
	 */
	public function getMethod(){
		return $this->options['method'];
	}

	/**
	 * Устанавливает название формы
	 *
	 * @param string $name
	 * @throws InvalidArgumentException
	 * @return Form
	 */
	public function setName($name){
		if (!is_string($name) || !trim($name))
			throw new InvalidArgumentException('Form name must be not empty string');

		$this->options['name'] = $name;
		return $this;
	}

	/**
	 * Возвращает название формы
	 *
	 * @return string
	 */
	public function getName(){
		return $this->options['name'];
	}

	/**
	 * Устанавливает заголовок для кнопки отправки формы
	 *
	 * @param string $title
	 * @throws InvalidArgumentException
	 * @return Form
	 */
	public function setSubmitTitle($title){
		if (!is_string($title) || !trim($title))
			throw new InvalidArgumentException('Form submit title must be not empty string');

		$this->options['submit_title'] = $title;
		return $this;
	}

	/**
	 * Возвращает заголовок для кнопки отправки формы
	 *
	 * @return string
	 */
	public function getSubmitTitle(){
		return $this->options['submit_title'];
	}

	/**
	 * Заружает языковые сообщения
	 * 
	 * @throws InvalidArgumentException
	 * @return void
	 */
	private function loadLangPosts(){
		if (!file_exists(FORM_LANG_PATH))
			throw new InvalidArgumentException('Language theme for this id is not found');

		// загрузка списка сообщений
		include FORM_LANG_PATH;
		$this->lang_posts = & $lang;
	}

	/**
	 * Метод для сериализации класса
	 *
	 * @return string
	 */
	public function serialize(){
		return serialize(array($this->options, $this->collection, self::$template));
	}

	/**
	 * Метод для десериализации класса
	 *
	 * @param string $data
	 * @return Form
	 */
	public function unserialize($data){
		list($this->options, $this->collection, self::$template) = unserialize($data);
		$this->collection->setForm($this);
		$this->loadLangPosts();
		return $this;
	}

}
?>