<?php exit?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Links Example</title>
</head>
<body><?php

include 'Links.php';


try {
	// составление 
	$paged = paged\Links::create(10, 'p')
		// длинна списка страниц
		->setListLength(5);

} catch (Exception $e){
	// при составлении структуры допущена ошибка
	exit('<p><strong>Error: '.$e->getMessage().'</strong></p>');
}



// список страниц не пустой
if (!$paged->isEmptyList()){

	// предыдущая страница
	if(!$paged->isVisible($paged->getFirst())){
		echo "<a href=\"".$paged->getFirstLink()."\" title=\"First page\">"
			.$paged->getFirst()."</a>\n...\n";
	}

	// список страниц
	foreach($paged->getListLinks() as $num=>$link){
		// это активная страница
		if($num==$paged->getActive()){
			echo "<span>".$num."</span>\n";
		} else {
			echo "<a href=\"".$link."\" title=\"Page ".$num."\">".$num."</a>\n";
		}
	}

	// следующая страница
	if(!$paged->isVisible($paged->getLast())){
		echo "...\n<a href=\"".$paged->getLastLink()."\" title=\"Last page\">"
			.$paged->getLast()."</a>\n";
	}
}
?>
</body>
</html>