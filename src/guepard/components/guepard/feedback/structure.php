<?php
require ROOT.'inc/formmanager/FormManagerFacade.php';

$form = FormManagerFacade::Form()
	->setLangID('ru')
	->setSubmitTitle('Отправить сообщение')
	->add(FormManagerFacade::Text('name', 'Имя')
		->setFilter('empty')
		->setFilter('length', array('max'=>128)))
	->add(FormManagerFacade::Email('mail', 'E-mail для связи')
		->setFilter('empty')
		->setFilter('length', array('max'=>128)))
	->add(FormManagerFacade::Select('subject', 'Тема сообщения', array(
		'options' => array(
			1 => 'Общий вопрос',
			2 => 'Вопрос по моим исходникам',
			3 => 'Предложение по доработке моих исходников',
			4 => 'Вопрос по моим проектам',
//			5 => 'Предложение работы',
			6 => 'Обнаружена ошибка'
		)
	)))
	->add(FormManagerFacade::TextArea('post', 'Ваше сообщение')
		->setView('textarea', array('cols'=>60, 'rows'=>10))
		->setFilter('empty'))
	->add(FormManagerFacade::Captcha('key', 'Код подтверждения')
		->setView('myCaptcha'));
