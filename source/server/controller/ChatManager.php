<?php
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
        if (self::$dbManager == null)
            self::$dbManager = new DBManager("localhost", 3306, "B5CGM", "aatu", "Kiiski");
    }
    
    public static function submitChatMessage($userid, $message, $gameid = 0)
    {
        try
        {
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
}