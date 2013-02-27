<?php

/**
 * Интерфейс комплексных компонентов
 * 
 * @author	Peter Gribanov
 * @since	10.09.2011
 * @version	1.0
 */
interface ComplexComponent {

	/**
	 * Подготавливает URL для работы в комплексных компонентах
	 * 
	 * @param string $url
	 * @return string
	 */
	public function prepareURL($url);

}