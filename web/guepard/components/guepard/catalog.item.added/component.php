<?php
// component guepard:catalog.item.added

require_once 'classes/Guepard_CatalogItemAdded.php';
$cia = new Guepard_CatalogItemAdded($params);

$cia->loadForm();
if (isset($_GET['secid']) && is_numeric($_GET['secid'])){
	$cia->successful($_GET['secid']);
}
if ($cia->isAlreadySent()){
	try {
//		var_dump($_POST);
//		var_dump($_FILES);
		$cia->valid();
		$cia->writeData();
	} catch (FormFilterException $e){
		$cia->exception($e);
	} catch (Exception $e){
		$cia->exception($e);
	}
}
$cia->draw();