<?php
require_once ROOT.'inc/sender/SenderFacade.php';

$cont = ob_get_contents();
ob_clean();
require 'mail.php';
$mess = ob_get_contents();
ob_clean();
echo $cont;

// параметры соединения
require ROOT.'inc/global_config.php';
if ($conf['mail_type']=='mail'){
	$type = $conf['mail_type'];
} else {
	$type = $conf['mail_type'].'://'.$conf['mail_user'].':'
		.$conf['mail_pass'].'@'.$conf['mail_host'].':'.$cont['mail_port'];
}

SenderFacade::Sender($type)
	->send(SenderFacade::Message('pgribanov@businessfm.ru', $mess, 'mail')
		->setSubject('Форма обратной связи. '.$form->getSentValue('subject').'.')
		->setCharset($conf['mail_charset']));

unset($conf);