<?php

require_once 'messages/SenderMessage.php';
require_once 'senders/Sender.php';
require_once 'collections/SenderCollection.php';

/**
 * Класс отправки E-mail сообщений
 * 
 * @package	SendMail
 * @author	Peter Gribanov
 * @since	30.06.2011
 * @version	1.2
 */
class SenderFacade {

	/**
	 * Создает объект коллекцию сообщений
	 * 
	 * @return SenderCollection
	 */
	public static function Collection($type=''){
		if (!$type)	return new SenderCollection();

		// получение имени класса
		$className = 'SenderCollection'.ucfirst(strtolower($type));
		// проверка существования файла с классом
		if (!file_exists(dirname(__FILE__).'/collections/'.$className.'.php'))
			throw new LogicException('Selected collection type is not supported.', 101);
		// загрузка файла с классом
		require_once dirname(__FILE__).'/collections/'.$className.'.php';
		// проверка существования класса
		if (!class_exists($className))
			throw new LogicException('Class "'.$className.'" does not exist.', 102);
		// инициализация класса
		$coll = new $className();
		// проверка на наличие нужного родителя
		if (!is_a($coll, 'SenderCollection'))
			throw new LogicException('Class "'.$className.'" is incorrect.', 103);
		return $coll;
	}

	/**
	 * Создает объект отправителя сообщений
	 * 
	 * Параметром передается имя модуля или строка подключения
	 * mod://username:password@server:port/path
	 * 
	 * @param string $options
	 * @throws InvalidArgumentException
	 * @throws LogicException
	 * @return Sender
	 */
	public static function Sender($options){
		// проверка параметра
		if (!is_string($options) || !trim($options))
			throw new InvalidArgumentException('Not specify the sender\'s option.', 104);
		// получение имени модуля отправителя
		$mod = $options;
		if (strpos($mod, '://')!==false){
			$mod = parse_url($mod);
			$mod = $mod['scheme'];
		}
		// получение имени класса
		$className = 'Sender'.ucfirst(strtolower($mod));
		// проверка существования файла с классом
		if (!file_exists(dirname(__FILE__).'/senders/'.$className.'.php'))
			throw new LogicException('Selected sender module is not supported.', 105);
		// загрузка файла с классом
		require_once dirname(__FILE__).'/senders/'.$className.'.php';
		// проверка существования класса
		if (!class_exists($className))
			throw new LogicException('Class "'.$className.'" does not exist.', 106);
		// инициализация класса и передача ему параметров при необходимости
		$sender = $options==$mod ? new $className() : new $className($options);
		// проверка на наличие нужного родителя
		if (!is_a($sender, 'Sender'))
			throw new LogicException('Class "'.$className.'" is incorrect.', 107);

		return $sender;
	}

	/**
	 * Создает объект сообщения
	 * 
	 * @param string $type
	 * @param string $to
	 * @param string $message
	 * @throws InvalidArgumentException
	 * @throws LogicException
	 * @return SenderMessage
	 */
	public static function Message($to, $message, $type=''){
		if (!$type){
			$mess = new SenderMessage();
		} else {
			// получение имени класса
			$className = 'SenderMessage'.ucfirst(strtolower($type));
			// проверка существования файла с классом
			if (!file_exists(dirname(__FILE__).'/messages/'.$className.'.php'))
				throw new LogicException('Selected message type is not supported.', 109);
			// загрузка файла с классом
			require_once dirname(__FILE__).'/messages/'.$className.'.php';
			// проверка существования класса
			if (!class_exists($className))
				throw new LogicException('Class "'.$className.'" does not exist.', 110);
			// инициализация класса
			$mess = new $className();
			// проверка на наличие нужного родителя
			if (!is_a($mess, 'SenderMessage'))
				throw new LogicException('Class "'.$className.'" is incorrect.', 111);
		}
		// передача данных и возврат результата
		return $mess->setTo($to)->setMessage($message);
	}

}
?>