<?php

require 'Options.php';

/**
 * Enter description here ...
 * @author gribape
 */
class Main {

	/**
	 * Необходимо сжатие контента
	 * 
	 * @var boolen
	 */
	private static $toCompression = false;

	/**
	 * Языковые сообщения модуля Main
	 * 
	 * @var array
	 */
	private static $lang_mess = array();


	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getLangID(){
		return Options::getOption('main', 'lang_id');
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getLangCharset(){
		return Options::getOption('main', 'lang_charset');
	}

	/**
	 * Enter description here ...
	 * @return boolen
	 */
	public static function isPHPDebug(){
		return Options::getOption('main', 'php_debug');
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getCookieDomain(){
		return Options::getOption('main', 'cookie_domain');
	}

	/**
	 * Enter description here ...
	 * @return integer
	 */
	public static function getSessionLifeTime(){
		return Options::getOption('main', 'session_life_time');
	}

	/**
	 * Enter description here ...
	 * @return string
	 */
	public static function getTemplateID(){
		return Options::getOption('main', 'template');
	}

	/**
	 * Enter description here ...
	 */
	public static function PrologActions(){
		// сжатие контента страници
		if (Options::getOption('main', 'compression') && isset($_SERVER['HTTP_ACCEPT_ENCODING'])
			&& substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')
			&& extension_loaded('zlib')){
			HTTP::send('Content-Encoding: gzip');
			self::$toCompression = true;
		}
	}

	/**
	 * Enter description here ...
	 */
	public static function EpilogActions(){
		if (self::$toCompression){
			$content = ob_get_contents();
			ob_clean();
			echo gzencode($content, 9);
		}
	}

	/**
	 * Загружает языковые сообщения модуля Main
	 * 
	 * @return array
	 */
	public static function getLang(){
		if (!self::$lang_mess){
			include ROOT.G_ROOT.'modules/main/lang/'.LANG.'/.parameters.php';
			self::$lang_mess = $lang;
		}
		return self::$lang_mess;
	}

	/**
	 * Загружает языковые сообщения компонента
	 * 
	 * @param string $componentName
	 * @return array
	 */
	public static function getComponentLang($componentName){
		list($ns, $componentName) = explode(':', $componentName, 2);

		if (!file_exists(ROOT.G_ROOT.'components/'.$ns.'/'.$componentName.'/lang/'.LANG.'/.parameters.php'))
			throw new LogicException('File is missing language messages');

		$lang = array();
		include ROOT.G_ROOT.'components/'.$ns.'/'.$componentName.'/lang/'.LANG.'/.parameters.php';

		return $lang;
	}

	/**
	 * Загружает шаблон компонента
	 * 
	 * @param string $componentName
	 * @param string $path
	 * @param array $result
	 * @return void
	 */
	public static function getComponentTemplate($componentName, $path, $result=null){
		list($ns, $componentName) = explode(':', $componentName, 2);
		$com_path = 'components/'.$ns.'/'.$componentName.'/';

		if (file_exists(ROOT.G_ROOT.'templates/'.TEMPLATE_ID.'/'.$com_path.$path)){
			$path = 'templates/'.TEMPLATE_ID.'/'.$com_path.$path;
		} elseif (file_exists(ROOT.G_ROOT.'templates/.default/'.$com_path.$path)){
			$path = 'templates/.default/'.$path;
		} else {
			$path = $com_path.'templates/'.$path;
		}

		Template::getTemplate($path, $result);
	}

	/**
	 * HTTP редирект на другую страницу
	 * 
	 * @param string $url
	 * @return void
	 */
	public static function redirect($url){
		HTTP::send('Location: '.$url);
		echo 'Forwarding to: '.$url;
		exit;
	}

}