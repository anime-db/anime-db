<? require $_SERVER['DOCUMENT_ROOT'].'/guepard/header.php'?>
<?Page::setTitle('Главная страница')?>
<?Page::IncludeComponent('guepard:catalog.last.added', 'homedb', array(
	'number'		=> 6,
	'genre_link'	=> '/selection.php?g=#genre#',
	'type_link'		=> '/selection.php?t=#type#',
	'show_link'		=> '/#id#.html',
	'summary_wrap'	=> 300,
));?>
<? require $_SERVER['DOCUMENT_ROOT'].'/guepard/footer.php'?>