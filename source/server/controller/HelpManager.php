<?php

require_once dirname(__DIR__) . '/varconfig.php'; 

set_error_handler(
		function ($errno, $errstr, $file, $line)
		{
			throw new ErrorException($errstr, $errno, 1, $file, $line);
		}
);

class HelpManager{
	private static $dbManager = null;

	private static function initDBManager() {
	    global $database_host;
	  	global $database_name;
		global $database_user;
		global $database_password;
		if (self::$dbManager == null)
			self::$dbManager = new DBManager($database_host ?? "localhost", 3306,  $database_name, $database_user, $database_password);
	}
	
	public static function getHelpMessage($gamehelpmessagelocation)
	{
		$message = array ('message'=>"",'helpimg'=>"./../img/greyvir.jpg",'nextpageid'=>"0");
		try {
			self::initDBManager();
			$message = self::$dbManager->getHelpMessage($gamehelpmessagelocation);
		}
		catch(Exception $e) {
			$logid = Debug::error($e);
			return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
		}
        return $message;
	}
	
}	
	