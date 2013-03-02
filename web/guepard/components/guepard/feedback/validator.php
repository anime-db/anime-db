<?php
try {
	$form->valid();

	require ROOT.'inc/feedback/send.php';

	$reply = array(
		'mess'		=> 'Ваше сообщение успешно отправлено. Благодарю Вас за внимание.',
		'status'	=> 2 // нет ошибок
	);
} catch (FormManagerFilterException $e){
	$reply = array(
		'field'		=> $e->getElement()->getName(),
		'mess'		=> $e->getMessage(),
		'status'	=> 1 // ошибка
	);
}