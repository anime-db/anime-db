<?php

/**
 * Коллекция сообщений на отправку
 * 
 * @package	Sender
 * @author	Peter Gribanov
 * @since	06.07.2011
 * @version	1.0
 */
class SenderCollection {

	/**
	 * Коллекция сообщений
	 *
	 * @var	array
	 */
	protected $messages = array();


	/**
	 * Добовляет сообщение в коллекцию
	 *
	 * @param SenderMessage $message
	 * @return SenderCollection
	 */
	public function add(SenderMessage $message){
		$this->messages[] = $message;
		return $this;
	}

	/**
	 * Добовляет в коллекцию сообщение адресованное списку пользователей
	 * 
	 * @param array $recipients
	 * @param SenderMessage $message
	 * @throws InvalidArgumentException
	 * @return SenderCollection
	 */
	public function notification($recipients, SenderMessage $message){
		if ($recipients && is_array($recipients))
			throw new InvalidArgumentException('Incorrect list of recipients.', 201);

		// дублирование сообщения с разными получателями
		foreach ($recipients as $recipient){
			$message->setTo($recipient);
			$this->messages[] = $message;
		}
		return $this;
	}

	/**
	 * Возваращает сообщение из коллекции
	 * 
	 * @return SenderMessage
	 */
	public function get(){
		return array_shift($this->messages);
	}

	/**
	 * Возваращает коллекцию сообщений
	 * 
	 * @return array
	 */
	public function export(){
		return $messages;
	}

}
?>