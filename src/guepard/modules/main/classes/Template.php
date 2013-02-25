<?php
/**
 * Enter description here ...
 * 
 * @author gribape
 */
class Template {

	/**
	 * Подключает шаблон
	 * 
	 * @param string $path
	 * @param array $result
	 */
	public static function getTemplate($path, $result=null){
		$result = $result ? $result : array();
		if (!file_exists(ROOT.G_ROOT.$path))
			throw new Exception('Template file ('.$path.') is not installed');

		require ROOT.G_ROOT.$path;
	}

}