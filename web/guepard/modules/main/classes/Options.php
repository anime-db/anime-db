<?php

/**
 * Enter description here ...
 * @author gribape
 */
class Options {

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $options = array();

	/**
	 * Enter description here ...
	 * @var unknown_type
	 */
	private static $default_options = array();

	
	/**
	 * Enter description here ...
	 * @param unknown_type $module_id
	 * @param unknown_type $option_id
	 * @param unknown_type $default_value
	 * @return unknown
	 */
	public static function getOption($module_id, $option_id, $default_value=false){
		$options =& self::getOptionList($module_id);

		// возвращаем результат если есть
		if (isset($options[$option_id]))
			return $options[$option_id];

		// возвращаем значение по умолчанию переданое в параметре
		if ($default_value!==false)
			return $default_value;

		$options =& self::getOptionList($module_id, true);
		
		return $options[$option_id];
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $module_id
	 * @param unknown_type $option_id
	 * @param unknown_type $value
	 * @param unknown_type $description
	 */
	public static function setOption($module_id, $option_id, $value=''/*, $description=false*/){
		$options =& self::geOptionList($module_id);
		$options[$module_id][$option_id] = $value; 
		return file_put_contents(ROOT.G_ROOT.'modules/'.$module_id.'/options.php', "<?php\n\$options = ".var_export($options).';');
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $module_id
	 * @param unknown_type $option_id
	 */
	public static function removeOption($module_id, $option_id=''){
		$options =& self::geOptionList($module_id);
		if ($option_id==''){
			$options[$module_id] = array();
		} else {
			unset($options[$module_id][$option_id]);
		} 
		return file_put_contents(ROOT.G_ROOT.'modules/'.$module_id.'/options.php', "<?php\n\$options = ".var_export($options).';');
	}


	/**
	 * загружаем основные опции
	 * 
	 * @param unknown_type $module_id
	 * @param unknown_type $isDefault
	 * @return multitype:
	 */
	private static function & getOptionList($module_id, $isDefault=false){
		$path = ROOT.G_ROOT.'modules/'.$module_id;

		if ($isDefault){
			$option_list =& self::$default_options;
			$path .= '/default_options.php';
		} else {
			$option_list =& self::$options;
			$path .= '/options.php';
		}

		if (!isset($option_list[$module_id])){
			$options = array();
			if (file_exists($path)) require $path;
			$option_list[$module_id] = $options;
		}

		return $option_list[$module_id];
	}
	
}