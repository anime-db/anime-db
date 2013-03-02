<?php
//$lang = Main::getComponentLang('guepard:catalog.item.show');


Page::setTitle('Мое аниме');

Main::getComponentTemplate('guepard:catalog.item.show', 'admin.menu_tamplate.php', array(
	'item_id'	=> $params['item'],
	'link'		=> str_replace('#id#', $params['item'], $params['link']),
));