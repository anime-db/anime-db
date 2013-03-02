<?php

require_once 'Sender.php';

/**
 * Класс отправки E-mail сообщений через PHP функцию mail()
 *
 * @package	Sender
 * @author	Peter Gribanov
 * @since	06.07.2011
 * @version	1.0
 */
class SenderMail implements Sender {

	/**
	 * Текущий статус объекта
	 * 0 - объект еще не создан
	 * 1 - создан, готов к работе
	 * 2 - отправляет сообщение
	 * 
	 * @var integer
	 */
	protected $status = 0;


	/**
	 * Конструктор класса
	 *
	 * @return void
	 */
	public function __construct(){
		$this->status = 1;
	}

	/**
	 * Отправляет сообщение
	 * 
	 * @param SenderMessageMail $message
	 * @return boolen
	 */
	public function send(SenderMessage $message){
		$this->status = 2;
		$message = $message->export();
		$result = @mail('', '', $message['message'], $message['headers']);
		$this->status = 1;
		return $result;
	}

	/**
	 * Возвращает текущий статус работы объекта
	 *
	 * @return	integer
	 */
	public function getStatus(){
		return $this->status;
	}

}
?>