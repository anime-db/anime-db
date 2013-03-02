<?php

require_once 'Sender.php';

/**
 * Класс отправки E-mail сообщений через соединение с сервером почты
 * 
 * Модуль собран по стандартам протоколов SMTP и ESMTP
 * 
 * @package	Sender
 * @author	Peter Gribanov
 * @since	06.07.2011
 * @version	1.1
 */
class SenderSmtp implements Sender {

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
	 * Текст последнего диалога с сервером
	 *
	 * @var string
	 */
	public $log = '';

	/**
	 * Код ошибки, если таковая была. Иначе 0
	 *
	 * @var integer
	 */
	public $errno = 0;

	/**
	 * Текст ошибки, если таковая была
	 *
	 * @var string
	 */
	public $errstr = '';

	/**
	 * Время ожидания реакции от сервера
	 *
	 * @var integer
	 */
	protected $timeout = 0;

	/**
	 * Требуется ли именно безопасное соединение
	 * По умолчанию нет
	 *
	 * @var boolen
	 */
	protected $secure = false;

	/**
	 * Cоединения с SMTP сервером
	 *
	 * @var resource
	 */
	protected $connect;

	/**
	 * Параметры соединения
	 *
	 * @var array
	 */
	protected $options = array();


	/**
	 * Конструктор класса
	 *
	 * @param string $options
	 * @return void
	 */
	public function __construct($options=''){
		$this->status = 1;

		// настройки по умолчанию
		$default = array(
			'scheme'	=> 'smtp',
			'host'		=> 'localhost',
			'port'		=> 25,
			'user'		=> '',
			'pass'		=> '',
		);

		// составление набора параметров
		$this->options = array_merge($default,
			$options!='smtp' ?  parse_url($options) : array());

		// за таймаут берет 90% от max_execution_time
		$this->timeout = ini_get('max_execution_time') * 0.9;
	}
	
	/**
	 * Отправляет сообщение
	 * 
	 * @param SenderMessageMail $message
	 * @return boolen
	 */
	public function send(SenderMessage $message){
		$this->errno = 0;
		$this->errstr = '';
		// Обнуляем лог
		$this->log = '';
		$this->status = 2;

		// Установка соединения с SMTP сервером
		$this->connect = @fsockopen($this->options['host'],
			$this->options['port'], $this->errno, $this->errstr, $this->timeout);


		// Проверка, установлено ли SMTP соединение
		if (!is_resource($this->connect)){
			if ($this->errno==0)
				$this->errstr = 'Failed connect to: '.$this->options['host'];
				$this->status = 1;
			return false;
		}

		// SMTP-сессия установлена, можно отправлять запросы

		try {
			// Соединено с?
			$this->log = fgets($this->connect, 4096);

			// Говорим EHLO
			$reply =& $this->call('EHLO '.$_SERVER['HTTP_HOST']);

			// Это протокол ESMTP
			if ($this->isSuccess($reply)){
				// Если требуется, открываем TLS соединение, то открываем его
				if ($this->secure){
					$this->call('STARTTLS');
					// После старта TLS надо сказать еще раз EHLO
					$this->valid($this->call('EHLO '.$_SERVER['HTTP_HOST']));
				}
			} else {
				$this->valid($this->call('HELO '.$_SERVER['HTTP_HOST']));
			}
			

			if ($this->options['user'] && $this->options['pass']){
				// Запрос на авторизованный логин
				$this->call('AUTH LOGIN');
				// Отправка имени пользователя
				$this->call(base64_encode($this->options['user']));
				// Отправка пароля
				$this->valid($this->call(base64_encode($this->options['pass'])));
			}

			// отправителя
			$this->valid($this->call('MAIL FROM: '.$message['from']));


			// получателя
			$this->valid($this->call('RCPT TO: '.$message['to']));


			// готовимся к отправке данных
			$this->call('DATA');

			// Отправляем заголовок и само сообщение.
			// Точка в самом конце означает конец сообщения
			$this->valid($this->call($message['headers']
				."\r\n\r\n".$message['message']."\r\n."));

		} catch (Exception $e){
			$this->errno = $e->getCode();
			$this->errstr = $e->getMessage();
		}

		// Завершаем передачу данных
		$reply =& $this->call('QUIT');

		// Закрываем SMTP соединение
		fclose($this->connect);

		$this->status = 1;
		return $this->errno==0 && $this->isSuccess($reply);
	}

	/**
	 * Возвращает текущий статус работы объекта
	 *
	 * @return	integer
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 * Устанавливает максимальное время ожидания ответа от сервера 
	 * 
	 * @param integer $timeout
	 * @throws InvalidArgumentException
	 * @return void
	 */
	public function setTimeOut($timeout){
		if (!is_int($timeout) || $timeout<=0 || $timeout>=ini_get('max_execution_time')){
			throw new InvalidArgumentException('Incorrect maximum waiting time from the server.', 401);
		}
		$this->timeout = $timeout;
	}

	/**
	 * Стартовать безопасное соединение
	 * 
	 * @return void
	 */
	public function startSecure(){
		$this->secure = true;
	}

	/**
	 * Отправляет серверу запрос и возвращает ответ
	 *
	 * @param string $call
	 * @return string
	 */
	private function & call($call){
		fputs($this->connect, $call."\r\n");

		$reply = fread($this->connect, 4096);
		// Запись отладочной информации
		$this->log .= $call."\r\n".$reply;

		return $reply;
	}

	/**
	 * Проверяет, является ли ответ сервера успешным
	 * и в случае ошибки вызывает исключение
	 * 
	 * @param string $reply
	 * @throws Exception
	 * @return void
	 */
	private function valid( & $reply){
		if (!$this->isSuccess($reply)){
			list($code, $message) = $this->parse($reply);
			throw new Exception($message, $code);
		}
	}

	/**
	 * Разбирает ответ на код ответа и текст сообщения возвращая их
	 * 
	 * @param string $reply
	 * @return array array(code, message) 
	 */
	private function parse( & $reply){
		if (preg_match('/^(\d{3}).*?([-_ \.a-zA-Z]+)[\r|\n]/', $reply, $mat)){
			return array(intval($mat[1]), trim($mat[2]));
		}
		return array(1, trim($reply));
	}

	/**
	 * Проверяет, является ли ответ сервера успешным
	 * 
	 * @param string $reply
	 * @return void
	 */
	private function isSuccess( & $reply){
		// код положительного ответа начинается с двойки
		return $reply[0]==2;
	}

}
?>