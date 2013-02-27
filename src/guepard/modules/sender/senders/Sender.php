<?php

/**
 * Интерфейс отправителей сообщений
 * 
 * @package	Sender
 * @author	Peter Gribanov
 * @since	06.07.2011
 * @version	1.0
 */
interface Sender {

	/**
	 * Отправляет сообщение
	 * 
	 * @param SenderMessage $message
	 * @return boolen
	 */
	public function send(SenderMessage $message);

	/**
	 * Возвращает текущий статус работы объекта
	 *
	 * @return	integer
	 */
	public function getStatus();

}