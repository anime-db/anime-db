<?php

/**
 * Enter description here ...
 * @author gribape
 */
class HTTP {

	/**
	 * Enter description here ...
	 * @param unknown_type $status
	 * @return void
	 */
	public static function setStatus($status){
		self::send('HTTP/1.0 '.$status);
		self::send('HTTP/1.1 '.$status);
	}

	/**
	 * Enter description here ...
	 */
	public static function setCacheControl(){
			if ($cache=Options::getOption('main', 'http_cache')){
				self::send('Cache-Control: public,max-age='.$cache);
			} else {
				self::send('Cache-Control: no-store,no-cache,must-revalidate');
				self::send('Pragma: no-cache');
			}
	}

	/**
	 * Enter description here ...
	 * @param unknown_type $request
	 */
	public static function send($request){
		@header($request);
	}

}