<?php exit?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Numbers Example 2</title>
</head>
<body><?php

include 'Numbers.php';


try {
	// составление 
	$paged = paged\Numbers::create(10)
		// длинна списка страниц
		->setListLength(5);

	if (isset($_GET['page'])){
		$paged->setActive($_GET['page']);
	}

} catch (Exception $e){
	// при составлении структуры допущена ошибка
	exit('<p><strong>Error: '.$e->getMessage().'</strong></p>');
}



// список страниц не пустой
if (!$paged->isEmptyList()){

	// предыдущая страница
	if($paged->getPrevious()){
		echo "<a href=\"?page=".$paged->getPrevious()."\" title=\"Prev\">Prev</a>\n";
	} else {
		echo "<span>Prev</span>\n";
	}

	// список страниц
	foreach($paged->getList() as $num){
		// это активная страница
		if($num==$paged->getActive()){
			echo "<span>".$num."</span>\n";
		} else {
			echo "<a href=\"?page=".$num."\" title=\"Page ".$num."\">".$num."</a>\n";
		}
	}

	// следующая страница
	if($paged->getNext()){
		echo "<a href=\"?page=".$paged->getNext()."\" title=\"Next\">Next</a>\n";
	} else {
		echo "<span>Next</span>\n";
	}
}
?>
</body>
</html>