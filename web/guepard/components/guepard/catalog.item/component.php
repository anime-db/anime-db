<?php
// complex component guepard:catalog.item

require 'classes/Guepard_CatalogItem.php';

$ci = new Guepard_CatalogItem($params);

$action = $ci->getAction();

if ($action){
	switch ($action){
		// просмотр
		case $params['action_show']:
			if (($id=$ci->getItemId())!==false)
				Page::IncludeComponent('guepard:catalog.item.show', $componentTemplate, array(
					'item'			=> $id,
					'action'		=> $action,
					'link'			=> $ci->prepareURL(null, '#id#'),
				));
			break;

		// добавление новой записи
		case $params['action_added']:
			Page::IncludeComponent('guepard:catalog.item.added', $componentTemplate, array(
				'link'		=> $ci->prepareURL($params['action_added']),
				'show_link'	=> $ci->prepareURL($params['action_show'], '#id#'),
			));
			break;

		// автозаполнение
		case $params['action_autofill']:
			Page::IncludeComponent('guepard:catalog.item.autofill', $componentTemplate, array(
				'link' => $ci->prepareURL($params['action_autofill']),
				'driver' => $params['autofill_driver'],
				'added_link' => $ci->prepareURL($params['action_added']),
			));
			break;

		// конвертация файлов аниме
		case $params['action_conversion']:
			if (($id=$ci->getItemId())!==false)
				Page::IncludeComponent('guepard:catalog.item.conversion', $componentTemplate, array(
					'item' => $id,
					'link' => $ci->prepareURL($params['action_conversion'], $id),
				));
			break;

		// редактировать аниме
		case $params['action_change']:
			if (($id=$ci->getItemId())!==false)
				Page::IncludeComponent('guepard:catalog.item.change', $componentTemplate, array(
					'item' => $id,
					'link' => $ci->prepareURL($params['action_change'], $id),
				));
			break;

		// удаление аниме
		case $params['action_remove']:
			if (($id=$ci->getItemId())!==false)
				Page::IncludeComponent('guepard:catalog.item.remove', $componentTemplate, array(
					'item' => $id,
					'link' => $ci->prepareURL($params['action_remove'], $id),
				));
			break;

		default:
			$lang = Main::getComponentLang('guepard:catalog.item');
			Main::getComponentTemplate('guepard:catalog.item', 'error.php', array(
				'title'	=> $lang['error_title'],
				'msg'	=> $lang['nothing'],
			));

	}
}