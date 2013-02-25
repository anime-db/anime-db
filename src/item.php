<?require $_SERVER['DOCUMENT_ROOT'].'/guepard/header.php'?>
<?
Page::addCSS('css/form.css');
Page::IncludeComponent('guepard:catalog.item', 'homedb', array(
	'action_var_name'		=> 'do',
	'id_var_name'			=> 'id',
	'default_action'		=> 'show',
	'action_added'			=> 'added',
	'action_autofill'		=> 'autofill',
	'action_conversion'		=> 'conversion',
	'action_change'			=> 'change',
	'action_remove'			=> 'remove',
	'action_show'			=> 'show',
	'autofill_driver'		=> 'WorldArt',
	'action_link'			=> '/item-#action#.html',
	'item_link'				=> '/#id#-#action#.html',
	'default_item_link'		=> '/#id#.html',
));
?>
<?require $_SERVER['DOCUMENT_ROOT'].'/guepard/footer.php'?>