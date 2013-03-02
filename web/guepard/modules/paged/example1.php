<?php exit?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Numbers Example 1</title>
</head>
<body><?php

include 'Numbers.php';

// составление 
$paged = paged\Numbers::create(10);

if (isset($_GET['page']))
	$paged->setActive($_GET['page']);


// список страниц
foreach($paged->getList() as $num){
	// это активная страница
	if($num==$paged->getActive()){
		echo '<span>'.$num.'</span>'."\n";
	} else {
		echo '<a href="?page='.$num.'" title="Page '.$num.'">'.$num.'</a>'."\n";
	}
}

?>
</body>
</html>