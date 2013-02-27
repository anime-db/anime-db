<?php
// component guepard:catalog.item.autofill

require_once 'classes/Guepard_CatalogItemAutoFill.php';
$cia = new Guepard_CatalogItemAutoFill($params);


if (!empty($_GET['id'])){
	$cia->confirmAutoFillData($_GET['id']);
} else {

	$cia->loadForm();
	if ($cia->isAlreadySent()){
		try {
			$cia->valid();
		} catch (FormFilterException $e){
			$cia->exception($e);
			$cia->draw();
		} catch (Exception $e){
			$cia->exception($e);
			$cia->draw();
		}
	} else {
		$cia->draw();
	}
}
