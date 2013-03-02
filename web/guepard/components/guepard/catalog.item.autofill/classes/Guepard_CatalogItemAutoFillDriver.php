<?php

/**
 * Интерфейс для драйвера автозаполнения
 * 
 * @author	Peter Gribanov
 * @since	10.09.2011
 * @version	1.0
 */
interface Guepard_CatalogItemAutoFillDriver {

	/**
	 * Поиск по имени
	 * 
	 * @param string $name
	 * @return boolen
	 */
	public function search($name);

	/**
	 * Проверяет является результатом поиска искомый элимент
	 * 
	 * @return mixid
	 */
	public function isDesiredResult();

	/**
	 * Получает список результатов поиска
	 * 
	 * @return array
	 */
	public function getSearchResult();

	/**
	 * Получает данные по элименту
	 * 
	 * @param string $id
	 * @return array
	 */
	public function getData($id);

}