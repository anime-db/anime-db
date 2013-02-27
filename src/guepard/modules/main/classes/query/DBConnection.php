<?php

require 'DBQuery.php';

/**
 * Адаптор для класса PDO
 * 
 * @license GNU GPL Version 3
 * @copyright 2011, Peter Gribanov
 * @link http://peter-gribanov.ru/license
 * @package	DB
 * @author	Peter Gribanov
 * @since	14.09.2011
 * @version	1.3
 */
final class DBConnection extends PDO {

	/**
	 * Creates a PDO instance representing a connection to a database
	 * 
	 * @see PDO::prepare()
	 * @param dsn
	 * @param username
	 * @param passwd
	 * @param options
	 * @return	void
	 */
	public function __construct($dsn, $username, $passwd, array $options = array()){
		try {
			@parent::__construct($dsn, $username, $passwd, $options);
		} catch (PDOException $e){
			@ob_end_flush();
			@ob_clean();
			$result['msg'] = $e->getMessage();
			$result['code'] = $e->getCode();
			require ROOT.G_ROOT.'modules/main/includes/dbconn_error.php';
			exit(1);
		}
	}

	/**
	 * Prepares a statement for execution and returns a statement object
	 * 
	 * @see PDO::prepare()
	 * @param statment
	 * @param options[optional]
	 * @return DBQuery
	 */
	public function prepare($statement, $driver_options=null){
		return new DBQuery(parent::prepare($statement, $driver_options ? $driver_options : array()));
	}

	/**
	 * Executes an SQL statement, returning a result set as a PDOStatement object
	 * 
	 * @see PDO::query()
	 * @param statment
	 * @return DBQuery
	 */
	public function query($statement){
		return new DBQuery(parent::query($statement));
	}

}