<?php exit?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SendMail - Example 3 PHP mail()</title>
</head>
<body><?php

include 'SenderFacade.php';


// инициализируем объект для отправки через PHP функцию mail()
$coll = SenderFacade::Collection('mail')
	// устанавливаем кодировку
	->setCharset('windows-1251')
	// устанавливаем E-mail адрес отправителя и его имя 
	->setFrom('sender@domain.ru', 'Sender')
	// добавляем в очередь письмо адресованое нескольким получателям
	->notification(array(
		'user1@domain.ru',
		'user2@domain.ru',
		'user3@domain.ru'
	), SenderFacade::Message('-', 'Текст сообщения 2', 'mail')
		->setSubject('Заголовок 2'))
	->add(SenderFacade::Message('admin@domain.ru',
		'<b>Test message.<b><br />You can remove this message.', 'mail')
		->setSubject('Example subject')
		// отправлять письмо в формате HTML
		->inHTML());


// инициализация объект для отправки через PHP функцию mail()
$sender = SenderFacade::Sender('mail');

// проходим по коллекции сообщений
while ($mess=$coll->get()){
	// отправляем текущее сообщение
	$sender->send($mess);
}
?>
</body>
</html>