<?php

/**
 * Класс описывающий сообщение
 * 
 * @package	Sender
 * @author	Peter Gribanov
 * @since	06.07.2011
 * @version	1.0
 */
class SenderMessage {

	/**
	 * Параметры письма
	 * 
	 * @var array
	 */
	protected $options = array(
		'to'		=> '',
		'message'	=> '',
	);


	/**
	 * Устанавливает получателя
	 * 
	 * @param string $to
	 * @throws InvalidArgumentException
	 * @return SenderMessageSimple
	 */
	public function setTo($to){
		if (!is_string($to) || !trim($to))
			throw new InvalidArgumentException('Recipient must not be empty.', 301);

		$this->options['to'] = $to;
		return $this;
	}

	/**
	 * Устанавливает тело сообщения
	 * 
	 * @param string $message
	 * @throws InvalidArgumentException
	 * @return SenderMessageSimple
	 */
	public function setMessage($message){
		if (!is_string($message) || !trim($message))
			throw new InvalidArgumentException('Message must not be empty.', 302);

		$this->options['message'] = $message;
		return $this;
	}

	/**
	 * Возваращает данные сообщения
	 * 
	 * @return array
	 */
	public function export(){
		return $this->options;
	}

}