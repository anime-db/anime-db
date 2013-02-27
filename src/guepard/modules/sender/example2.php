<?php exit?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SendMail - Example 2 SMTP</title>
</head>
<body><?php

include 'SenderFacade.php';


// инициализируем коллекции сообщений
$coll = SenderFacade::Collection('mail')
	// устанавливаем кодировку
	->setCharset('windows-1251')
	// устанавливаем E-mail адрес отправителя и его имя 
	->setFrom('sender@domain.ru', 'Sender')
	// добавляем в коллекцию писем
	->add(SenderFacade::Message('user1@domain.ru', 'Текст сообщения 1', 'mail')
		->setSubject('Заголовок 1'))
	->add(SenderFacade::Message('user2@domain.ru', 'Текст сообщения 2', 'mail')
		->setSubject('Заголовок 2'))
	->add(SenderFacade::Message('user3@domain.ru', 'Текст сообщения 3', 'mail')
		->setSubject('Заголовок 3'));

// инициализация объект для отправки через SMTP протокол
$sender = SenderFacade::Sender('smtp://username:password@server:port');


// проходим по коллекции сообщений
while ($mess=$coll->get()){
	// отправляем текущее сообщение
	$result = $sender->send($mess);
	// выводим результат отправки
	var_dump($result);

	// произошла ошибка при отправке
	if (!$result){
		// выводим текст ошибки
		echo $sender->errstr;
		break;
	}
	// выводим текст диалога с сервером
	echo '<pre>'.$sender->log.'</pre>';
}

?>
</body>
</html>