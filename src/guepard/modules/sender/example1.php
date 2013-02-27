<?php exit?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>SendMail - Example 1 PHP mail()</title>
</head>
<body><?php

include 'SenderFacade.php';

// отправка одного сообщения через PHP функцию mail()
SenderFacade::Sender('mail')
	->send(SenderFacade::Message('user@domain.ru', 'Текст сообщения', 'mail')
		->setSubject('Заголовок'));
?>
</body>
</html>