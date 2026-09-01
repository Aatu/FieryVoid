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

	/**
	 * Latched connect failure for THIS request -- see Manager::$dbUnavailable for the
	 * reasoning. HelpManager is the least exposed of the three (one entry point, no
	 * per-item loop), but it is the same static-stays-null-on-failure pattern and
	 * leaving one of the three half-done is how this comes back.
	 */
	private static $dbUnavailable = null;

	private static function initDBManager() {
	    global $database_host;
	  	global $database_name;
		global $database_user;
		global $database_password;
		if (self::$dbUnavailable !== null)
			throw self::$dbUnavailable;

		if (self::$dbManager == null) {
			try {
				self::$dbManager = new DBManager($database_host ?? "localhost", 3306,  $database_name, $database_user, $database_password);
			} catch (Throwable $e) {
				self::$dbUnavailable = self::asUnavailable($e);
				throw self::$dbUnavailable;
			}
		}
	}

	/**
	 * Stamp a failed connect with code 300 -- see Manager::asUnavailable for why the
	 * marker DBManager throws never survives to the client. Kept in step with the other
	 * two managers so all three report a database outage identically.
	 */
	private static function asUnavailable(Throwable $e)
	{
		if ($e instanceof Exception && $e->getCode() === 300) {
			return $e;
		}

		return new Exception('Database unavailable', 300, $e);
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
	