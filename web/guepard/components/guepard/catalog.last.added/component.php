<?php

require 'classes/Guepard_CatalogLastAdded.php';

$gcla = new Guepard_CatalogLastAdded($params);



if ($parentComponent){
	$gcla->setParentComponent($parentComponent);

} else {

	if (!$gcla->isEmptyList()){
		$gcla->drow();

	} else {
		$lang = Main::getComponentLang('guepard:catalog.last.added');

		Main::getComponentTemplate('guepard:catalog.last.added', 'warning.php', array(
			'title'	=> $lang['error_title'],
			'msg'	=> $lang['error_list_empty']
		));
	}

}