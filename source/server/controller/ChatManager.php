<?php

require_once dirname(__DIR__) . '/varconfig.php'; 

set_error_handler(
    function ($errno, $errstr, $file, $line)
    {
        throw new ErrorException($errstr, $errno, 1, $file, $line);
    }
);

class ChatManager{

    private static $dbManager = null;

    /**
     *  @return DBManager dbManager
     */
    private static function initDBManager() {
        global $database_host;
    	global $database_name;
    	global $database_user;
    	global $database_password;
        if (self::$dbManager == null)
            self::$dbManager = new DBManager($database_host ?? "localhost", 3306, $database_name, $database_user, $database_password);
    }
    
    public static function submitChatMessage($userid, $message, $gameid = 0)
    {
        try
        {
            $message = trim($message);
            if ($message == "")
                return "{}";
            
            $message = htmlspecialchars($message);
            
            self::initDBManager();
            self::$dbManager->submitChatMessage($userid, $message, $gameid);
            return "{}";
        }    
        catch(Exception $e) 
        {
            $logid = Debug::error($e);
            return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
        }
    }
    
    public static function getChatMessages($userid, $lastid, $gameid = 0)
    {
        try
        {
            self::initDBManager();
            self::$dbManager->deleteOldChatMessages();
            $messages = self::$dbManager->getChatMessages($lastid, $gameid);
            return json_encode($messages, JSON_NUMERIC_CHECK);
        }    
        catch(Exception $e) 
        {
            $logid = Debug::error($e);
            return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
        }
    }
    
    public static function getLastTimeChatChecked($userid, $gameid){
        try
        {
            self::initDBManager();
            // First do the game I am in
            $gameTime = self::$dbManager->getLastTimeChatChecked($userid, $gameid);
            return '{"lastCheckGame": "'.$gameTime.'"}';
        }    
        catch(Exception $e) 
        {
            $logid = Debug::error($e);
            return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
        }
    }
    
    public static function setLastTimeChatChecked($userid, $gameid){
        try
        {
            self::initDBManager();
            self::$dbManager->setLastTimeChatChecked($userid, $gameid);
            return "{}";
        }    
        catch(Exception $e) 
        {
            $logid = Debug::error($e);
            return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
        }
    }
}