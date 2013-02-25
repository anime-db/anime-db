<?php

/**
 * Enter description here ...
 * @author gribape
 */
class Page {

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $title = '';

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $css = array();

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $page_property = array();

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $dir_property = array();

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $buffer = array();

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $js = array();


	/***************************
	 * Заголовок страницы
	 ***************************/

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function showTitle(){
		self::addBufferContent('Page::getTitle');
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getTitle(){
		echo self::$title;
	}

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function setTitle($title){
		self::$title = $title;
	}

	/***************************
	 * CSS стили
	 ***************************/

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function showCSS($external=true, $inXhtml=true){
		self::addBufferContent('Page::getCSS', $external, $inXhtml);
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getCSS($external=true, $inXhtml=true){
		if ($external){
			foreach (self::$css as $path)
				self::getTemplate('css_link.php', array($path, $inXhtml));
		} else {
			$css = '';
			foreach (self::$css as $path)
				$css .= self::getFileContent($path);

			self::getTemplate('css_style.php', array($css));
		}
	}

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function setTemplateCSS($rel_path){
		$path = 'templates/'.TEMPLATE_ID.'/components/'.$rel_path;
		if (!file_exists(G_ROOT.$rel_path))
			$path = 'templates/.default/components/'.$rel_path;
		self::$css[] = '/'.$path;
	}

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function addCSS($path){
		if (file_exists(ROOT.G_ROOT.'templates/'.TEMPLATE_ID.'/'.$path))
			self::$css[] = '/'.G_ROOT.'templates/'.TEMPLATE_ID.'/'.$path;
	}

	/***************************
	 * Свойства страниц и разделов
	 ***************************/

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function showProperty($property_id, $default_value){
		self::addBufferContent('Page::getProperty', $property_id, $default_value);
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getProperty($property_id, $default_value=false){
		$property = array_merge(self::$dir_property, self::$page_property);
		return isset($property[$property_id]) ? $property[$property_id] : $default_value;
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getPageProperty($property_id, $default_value=false){
		return isset(self::$page_property[$property_id]) ? self::$page_property[$property_id] : $default_value;
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getDirProperty($property_id, $default_value=false){
		return isset(self::$dir_property[$property_id]) ? self::$dir_property[$property_id] : $default_value;
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getPagePropertyList(){
		return self::$page_property;
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getDirPropertyList(){
		return self::$dir_property[$property_id];
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function setPageProperty($property_id, $property_value){
		self::$page_property[$property_id] = $property_value;
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function setDirProperty($property_id, $property_value){
		self::$dir_property[$property_id] = $property_value;
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function showMeta($property_id, $meta_name=false, $inXhtml=true){
		self::addBufferContent('Page::getMeta', $property_id, $meta_name, $inXhtml);
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getMeta($property_id, $meta_name=false, $inXhtml=true){
		$property = array_merge(self::$dir_property, self::$page_property);
		$meta_name = $meta_name ? $meta_name : $property_id;
		if (isset($property[$property_id]))
			self::getTemplate('meta.php', array($property[$property_id], $meta_name, $inXhtml));
	}

	/***************************
	 * Файлы
	 ***************************/

	/**
	 * Возвращает содержимое файла
	 * 
	 * @param string $path
	 * @return mixed
	 */
	public static function getFileContent($path){
		if (!file_exists($path) || !is_readable($path)) return false;
		return file_get_contents($path);
	}

	/**
	 * Сохраняет файл на диске
	 * 
	 * @param string $path
	 * @param string $content
	 * @return boolen
	 */
	public static function saveFileContent($path, $content){
		if (file_exists($path) && !is_writable($path)) return false;
		return (bool)file_put_contents($path, $content);
	}

	/**
	 * Ищет файл с заданным именем последовательно вверх по иерархии папок
	 * 
	 * @param string $file
	 * @param string $dir
	 * @return mixed
	 */
	public static function getFileRecursive($file, $dir){
		// необходимо написать реализацию метода
		return false;
	}

	/***************************
	 * Компоненты
	 ***************************/

	/**
	 * Загружает компонент
	 * 
	 * @param string $componentName
	 * @param string $componentTemplate
	 * @param array $params
	 * @param object $parentComponent
	 * @param array $functionParams
	 * @return void
	 */
	public static function IncludeComponent($componentName, $componentTemplate='', $params=array(), $parentComponent=null, $functionParams=null){
		$componentTemplate = $componentTemplate ? $componentTemplate : '.default';

		$functionParams = array_merge(array(
			'ACTIVE_COMPONENT'	=> true,
		), $functionParams ? $functionParams : array());

		if ($functionParams['ACTIVE_COMPONENT']){
			try {
				list($ns, $componentName) = explode(':', $componentName, 2);
				include ROOT.G_ROOT.'components/'.$ns.'/'.$componentName.'/component.php';
			} catch (Exception $e){
				$lang = Main::getLang();
				self::getTemplate('component_error.php', array(
					$lang['component_error'],
					$ns.':'.$componentName,
					$e->getMessage(),
					$e->getCode()
				));
			}
		}
	}

	/**
	 * Загружает компонент
	 * 
	 * @param string $componentName
	 * @param string $componentTemplate
	 * @param array $params
	 * @param object $parentComponent
	 * @param array $functionParams
	 * @return void
	 */
	public static function IncludeFile($path, $params=array(), $functionParams=null){

		$functionParams = array_merge(array(
			'TEMPLATE'	=> G_ROOT.'templates/'.TEMPLATE_ID.'/page_templates/.content.php',
		), $functionParams ? $functionParams : array());

		if (file_exists(ROOT.G_ROOT.'templates/'.TEMPLATE_ID.'/'.$path)){
			$path = G_ROOT.'templates/'.TEMPLATE_ID.'/'.$path;
		} elseif (file_exists(ROOT.G_ROOT.'templates/.default/'.$path)){
			$path = G_ROOT.'templates/.default/'.$path;
		} else {
			$tpl = self::getFileContent(ROOT.$functionParams['TEMPLATE']);
			self::saveFileContent(ROOT.$path, $tpl);
			@chmod(ROOT.$path, FILE_CORE_ACCESS);
		}

		include ROOT.$path;
	}

	/***************************
	 * Прочие методы
	 ***************************/

	/**
	 * Enter description here ...
	 * 
	 * @param string $path
	 * @return void
	 */
	public static function addJS($path){
		if (file_exists(ROOT.G_ROOT.'templates/'.TEMPLATE_ID.'/'.$path))
			self::$js[] = '/'.G_ROOT.'templates/'.TEMPLATE_ID.'/'.$path;
	}

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function showJS(){
		self::addBufferContent('Page::getJS');
	}

	/**
	 * Enter description here ...
	 * @return void
	 */
	private static function getJS(){
		foreach (self::$js as $path)
			self::getTemplate('js.php', array($path));
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $inXhtml
	 */
	public static function showHead($inXhtml=true){
		self::getTemplate('head.php', array($inXhtml));
	}

	/**
	 * Enter description here ...
	 * @return void
	 */
	public static function addBufferContent(){
		self::$buffer[] = ob_get_contents();
		ob_clean();		
		$params = func_get_args();
		$callback = array_shift($params);
		self::$buffer[] = array($callback, $params);
	}

	/**
	 * Enter description here ...
	 */
	public static function execBufferContent(){
		self::$buffer[] = ob_get_contents();
//		ob_end_flush();
		ob_clean();
		foreach (self::$buffer as $content){
			if (is_string($content)){
				echo $content;
			} else {
				call_user_func_array($content[0], $content[1]);
			}
		}
	}

	/**
	 * Enter description here ...
	 * 
	 * @return boolen
	 */
	public static function isHTTPS(){
		return $_SERVER['SERVER_PROTOCOL'][4]=='S';
	}

	/**
	 * Подключает шаблон
	 * 
	 * @param string $path
	 * @param array $result
	 */
	private static function getTemplate($path, $result=null){
		Template::getTemplate('modules/main/interfaces/'.$path, $result);
	}

}