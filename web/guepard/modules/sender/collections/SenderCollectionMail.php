<?php

require_once 'SenderCollection.php';

/**
 * Коллекция сообщений на отправку
 * 
 * @package	Sender
 * @author	Peter Gribanov
 * @since	06.07.2011
 * @version	1.0
 */
class SenderCollectionMail extends SenderCollection {

	/**
	 * Кодировка отправляемых писем
	 *
	 * @var	string
	 */
	protected $charset = '';

	/**
	 * E-Mail отправителя
	 *
	 * @var	string
	 */
	protected $from = '';

	/**
	 * Имя отправителя
	 *
	 * @var	string
	 */
	protected $from_name = '';


	/**
	 * Устанавливает отправителя
	 *
	 * @param string $from
	 * @param string $from_name
	 * @return void
	 */
	public function setFrom($from, $from_name=''){
		$this->from = $from;
		$this->from_name = $name;
	}

	/**
	 * Устанавливает кодировку отправляемых писем
	 *
	 * @param string $charset
	 * @return void
	 */
	public function setCharset($charset){
		$this->charset = $charset;
	}

	/**
	 * Возваращает коллекцию сообщений
	 * 
	 * @return array
	 */
	public function export(){
		foreach ($this->messages as $i=>$m){
			// установка кодировки
			if ($this->charset)
				$this->messages[$i]->setCharset($this->charset);

			// установка отправителя
			if ($this->from)
				$this->messages[$i]->setFrom($this->from, $this->from_name);
		}

		return parent::export();
	}

}
?>