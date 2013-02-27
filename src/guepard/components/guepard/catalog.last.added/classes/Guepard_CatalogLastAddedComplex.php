<?php

/**
 * Интерфейс для комплексного класса компонента списка последних добавленных эелементов
 * 
 * @author gribape
 */
interface Guepard_CatalogLastAddedComplex {

	/**
	 * Устанавливает список последних добавленных
	 * 
	 * @param	array	$list	Список последних добавленных
	 * @return	void
	 */
	public function setList($list);

}