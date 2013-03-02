<?php
require_once 'FormStoreInterface.php';

/**
 * Хранение объекта формы в файле
 * 
 * @author	Peter Gribanov
 * @since	06.09.2011
 * @version	1.2
 */
class FormStoreFile implements FormStoreInterface {

	/**
	 * Загружает форму
	 * 
	 * @param string $form_name
	 * @return Form
	 */
	public function loadForm($form_name){
		$form = false;
		$file = ROOT.G_ROOT.'cache/components/guepard/form/'.$form_name.'.php';
		$file_obj = ROOT.G_ROOT.'cache/components/guepard/form/'.$form_name.'.obj.php';

		if (file_exists($file_obj) && (!file_exists($file) || filemtime($file)<filemtime($file_obj))){
			if (!is_readable($file_obj))
				throw new LogicException('Файл '.$file_obj.' недоступен для чтения.');
			require $file_obj;
			$this->saveForm($form, $form_name);
		} elseif (file_exists($file)){
			if (!is_readable($file))
				throw new LogicException('Файл '.$file.' недоступен для чтения.');
			require $file;
			$form = unserialize($form);
		}

		return $form;
	}

	/**
	 * Сохряняет форму
	 * 
	 * @param Form $form
	 * @param string $form_name
	 * @return boolen
	 */
	public function saveForm(Form $form, $form_name){
		$file = ROOT.G_ROOT.'cache/components/guepard/form/'.$form_name.'.php';

		Page::saveFileContent($file, "<?php\n\$form = '".serialize($form)."';");

		@chmod($file, FILE_CACHE_ACCESS);
		return true;
	}

}