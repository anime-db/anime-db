<?php
$form = FormFacade::Form()
	->setName('home_search')
	->setMethod('get')
	->setTemplate('home')
	->setSubmitTitle('Поиск')
	->setAction('/search.php')
	->add(FormFacade::Text('q', 'Поиск'));