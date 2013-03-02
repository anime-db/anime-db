<?php

require 'DBConnection.php';

/**
 * Адаптор для класса DBConnection
 * 
 * @license GNU GPL Version 3
 * @copyright 2011, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	DB
 * @author	Peter Gribanov
 * @since	06.08.2011
 * @version	2.1
 */
final class DB {

	/**
	 * Экземпляр класса DBConnection
	 * 
	 * @var	DBConnection
	 */
	private static $connection;

	/**
	 * Режим отладки
	 * 
	 * @var	boolen
	 */
	private static $debug_mode = false;


	/**
	 * Initiates a transaction
	 * 
	 * @return boolean
	 */
	public static function beginTransaction(){
		return self::getConnection()->beginTransaction();
	}

	/**
	 * Commits a transaction
	 * 
	 * @return boolean
	 */
	public static function commit(){
		return self::getConnection()->commit();
	}

	/**
	 * Вывод отладочной информации
	 * 
	 * @param string $statement
	 */
	private static function debug($statement){
		self::getConnection();
		if (self::$debug_mode)
			var_dump($statement);
	}

	/**
	 * Fetch the SQLSTATE associated with the last operation on the database handle
	 * 
	 * @return mixed
	 */
	public static function errorCode(){
		return self::getConnection()->errorCode();
	}

	/**
	 * Fetch extended error information associated with the last operation on the database handle
	 * 
	 * @return array
	 */
	public static function errorInfo(){
		return self::getConnection()->errorInfo();
	}

	/**
	 * Execute an SQL statement and return the number of affected rows 
	 * 
	 * @param string $statement
	 * @return integer
	 */
	public static function exec($statement){
		self::debug($statement);
		return self::getConnection()->exec($statement);
	}

	/**
	 * Retrieve a database connection attribute
	 * 
	 * @param integer $attribute
	 * @return mixed
	 */
	public static function getAttribute($attribute){
		return self::getConnection()->getAttribute($attribute);
	}

	/**
	 * Return an array of available PDO drivers
	 * 
	 * @return array
	 */
	public static function getAvailableDrivers(){
		return DBConnection::getAvailableDrivers();
	}

	/**
	 * Выполняет подключение к бд и возвращает объект подключения
	 * 
	 * @return DBConnection
	 */
	private static function getConnection(){
		if (!self::$connection){
			include ROOT.G_ROOT.'dbconn.php';
			self::$debug_mode = $db_debug;
			$options = array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
			);
			self::$connection = new DBConnection($db_dsn, $db_user, $db_pass, $options);
			require ROOT.G_ROOT.'after_conn.php';
		}
		return self::$connection;
	}

	/**
	 * Checks if inside a transaction
	 * 
	 * @return boolean
	 */
	public static function inTransaction(){
		return self::getConnection()->inTransaction();
	}

	/**
	 * Returns the ID of the last inserted row or sequence value
	 * 
	 * @param string $name
	 * @return string
	 */
	public static function lastInsertId($name=null){
		return self::getConnection()->lastInsertId($name);
	}

	/**
	 * Prepares a statement for execution and returns a statement object
	 * 
	 * @param string $statement
	 * @param array $driver_options
	 * @return DBQuery
	 */
	public static function prepare($statement, $driver_options=null){
		self::debug($statement);
		return self::getConnection()->prepare($statement, $driver_options);
	}

	/**
	 * Executes an SQL statement, returning a result set as a PDOStatement object
	 * 
	 * @param string $statement
	 * @param integer $fetch_mode
	 * @param mixed $value
	 * @param integer $ctorargs
	 * @return DBQuery
	 */
	public static function query($statement, $fetch_mode=null, $value=null, $ctorargs=null){
		self::debug($statement);
		return self::getConnection()->query($statement, $fetch_mode, $value, $ctorargs);
	}

	/**
	 * Quotes a string for use in a query.
	 * 
	 * @param string $string
	 * @param integer $parameter_type
	 * @return string
	 */
	public static function quote($string, $parameter_type=null){
		return self::getConnection()->quote($string, $parameter_type);
	}

	/**
	 * Rolls back a transaction 
	 * 
	 * @return boolean
	 */
	public static function rollBack(){
		return self::getConnection()->rollBack();
	}

	/**
	 * Set an attribute
	 * 
	 * @param integer $attribute
	 * @param mixed $value
	 * @return boolean
	 */
	public static function setAttribute($attribute, $value){
		return self::getConnection()->setAttribute($attribute, $value);
	}

}
?>