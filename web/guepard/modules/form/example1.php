<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Form - Example 1</title>
<style type="text/css">
/* скрыть поле с уникальным ключом создаваемое при отправки формы методом гет */
.field-hidden {display:none}
</style>
</head>
<body><?php
include('FormFacade.php');

//var_dump(is_dir('lang/en/'));

try {
	// составление формы
	$form = FormFacade::Form()
		// отправлять форму методом GET
		->setMethod('get')
		// добавление поля для ввода текстового сообщения
		->add(FormFacade::Text('mess', 'Ваше сообщение'));

} catch (Exception $e){
	// при составлении структуры формы допущена ошибка
	exit('<p><strong>Ошибка: '.$e->getMessage().'</strong></p>');
}


// форма заполнена и отправлена
if ($form->isAlreadySent()){
	// вывод отправленых данных
	var_dump($_GET);

	try {
		// проверка формы
		$form->valid();
		echo '<p><strong>Форма заполнена правильно.</strong></p>';

	} catch (FormFilterException $e){
		// в форме обнаружена ошибка
		echo '<p><strong>Ошибка: '.$e->getMessage().'</strong></p>';
	}
}


// вывод HTML структуры формы
echo $form->draw();
?>
</body>
</html>