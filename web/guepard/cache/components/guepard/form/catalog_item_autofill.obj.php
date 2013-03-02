<?php
$form = FormFacade::Form()
	->add(FormFacade::Text('external_id', 'Внешний ID')
		->setComment('ID во внешних базах данных, например на сайте world-art.ru. Укажите ID если он вам известен')
		->setDefaultValue(0)
		->setFilter('int')
		->setFilter('length', array('max'=>6)))
	->add(FormFacade::Text('name', 'Название аниме')
		->setComment('Для получения ID укажите русское или английское название')
		->setFilter('length', array('max'=>128)))
	->add(FormFacade::Select('driver', 'Драйвер', array(
			'use_key' => true,
			'options' => array('WorldArt' => 'Драйвер под сайта world-art.ru')
		))
		->setComment('Драйвер для получения информации, он же определяет источник')
	)
	->setName('catalog_item_add_autoаill')
	->setSubmitTitle('Получение данных');