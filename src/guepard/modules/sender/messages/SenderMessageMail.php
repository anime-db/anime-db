<?php

require_once 'SenderMessage.php';

/**
 * Класс описывающий E-mail сообщение
 * 
 * @package	Sender
 * @author	Peter Gribanov
 * @since	06.07.2011
 * @version	1.0
 */
class SenderMessageMail extends SenderMessage {

	/**
	 * Параметры письма
	 * 
	 * @var array
	 */
	protected $options = array(
		'charset'	=> 'koi8-r',			// Кодировка отправляемых писем
		'from'		=> '',					// E-Mail отправителя
		'from_name'	=> 'Administration',	// Имя отправителя
		'to'		=> '',					// E-mail получателя
		'subject'	=> '',					// Заголовок
		'message'	=> '',					// Текст сообщение
		'in_html'	=> false,				// Собщение в формате HTML
	);


	/**
	 * Конструктор
	 * 
	 * @return void
	 */
	public function __construct(){
		$this->options['from'] = 'admin@'.$_SERVER['HTTP_HOST'];
	}

	/**
	 * Устанавливает E-mail получателя
	 * 
	 * @param string $to
	 * @throws InvalidArgumentException
	 * @return SenderMessageMail
	 */
	public function setTo($to){
		if (!is_string($to) || !trim($to))
			throw new InvalidArgumentException('Recipient E-Mail must not be empty.', 311);

		$this->options['to'] = $to;
		return $this;
	}

	/**
	 * Устанавливает тело сообщения
	 * 
	 * @param string $message
	 * @throws InvalidArgumentException
	 * @return SenderMessageMail
	 */
	public function setMessage($message){
		if (!is_string($message) || !trim($message))
			throw new InvalidArgumentException('Mail message must not be empty.', 312);

		$this->options['message'] = $message;
		return $this;
	}
	/**
	 * Устанавливает заголовок сообщения
	 * 
	 * @param string $subject
	 * @throws InvalidArgumentException
	 * @return SenderMessageMail
	 */
	public function setSubject($subject){
		if (!is_string($subject) || !trim($subject))
			throw new InvalidArgumentException('Mail subject must not be empty.', 313);

		$this->options['subject'] = $subject;
		return $this;
	}

	/**
	 * Устанавливает кодировку отправляемых писем
	 * 
	 * @param string $charset
	 * @throws InvalidArgumentException
	 * @return SenderMessageMail
	 */
	public function setCharset($charset){
		if (!is_string($charset) || !trim($charset))
			throw new InvalidArgumentException('Incorrect message charset.', 314);

		$this->options['charset'] = $charset;
		return $this;
	}

	/**
	 * Устанавливает E-mail и имя отправителя
	 * 
	 * @param string $from
	 * @param string $from_name
	 * @throws InvalidArgumentException
	 * @return SenderMessageMail
	 */
	public function setFrom($from, $from_name=''){
		if (!is_string($from) || !trim($from) || !is_string($from_name))
			throw new InvalidArgumentException('Sender E-Mail must not be empty.', 315);

		$this->options['from'] = $from;
		$this->options['from_name'] = $from_name;
		return $this;
	}

	/**
	 * Устанавливает что сообщение в формате HTML
	 * 
	 * @return SenderMessageMail
	 */
	public function inHTML(){
		$this->options['in_html'] = true;
		return $this;
	}

	/**
	 * Возваращает данные сообщения
	 * 
	 * @return array
	 */
	public function export(){
		$this->options['headers'] = $this->getHeaders();
		return $this->options;
	}

	/**
	 * Составляет заголовки и возвращает их
	 * 
	 * @return string
	 */
	private function getHeaders(){
		$conttype = 'Content-type: text/'.($this->options['in_html'] ? 'html' : 'plain')
			.'; charset="'.$this->options['charset']."\"\r\n";

		// составление заголовков
		return $conttype.'Subject: '.$this->options['subject']."\r\n"
			. 'MIME-Version: 1.0'."\r\n"
			. $conttype
			. 'To: '.$this->options['to']."\r\n"
			. 'From: '.$this->options['from_name'].' <'.$this->options['from'].'>'."\r\n"
			. 'X-Sender: '.$_SERVER['HTTP_HOST']."\r\n"
			. 'X-Mailer: PHP/'.PHP_VERSION."\r\n";
	}

}