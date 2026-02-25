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

    private static function getCachePrefix() {
        global $database_name;
        // Use a safe fallback if for some reason db name is missing, though strictly it should be there.
        return ($database_name ?? 'default') . '_';
    }

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
            $msgId = self::$dbManager->submitChatMessage($userid, $message, $gameid);
            
            // APCu: Update last message ID!
            if (function_exists('apcu_store') && $msgId > 0) {
                $prefix = self::getCachePrefix();
                apcu_store($prefix . 'chat_last_id_' . $gameid, $msgId, 3600); // 1 hour TTL
            }
            
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
            // APCu Fast Poll - Check BEFORE DB connection!
            if (function_exists('apcu_fetch')) {
                 $prefix = self::getCachePrefix();
                 $lastMsgId = apcu_fetch($prefix . 'chat_last_id_' . $gameid);
                 if ($lastMsgId !== false && $lastid >= $lastMsgId) {
                     return "[]";
                 }
            }

            self::initDBManager();
            
            // Optimization: Only delete old messages 1% of the time
            if (mt_rand(0, 99) === 0) {
                self::$dbManager->deleteOldChatMessages();
            }
            $messages = self::$dbManager->getChatMessages($lastid, $gameid);
            
            // APCu: If we just fetched messages from DB, update the cache to ensure Fast Poll works next time
            if (function_exists('apcu_store')) {
                // If we got messages, the last one is the latest ID.
                $latestId = 0;
                $msgs = $messages; // assuming array of objects keyed by ID per DBManager
                 
                if (!empty($msgs)) {
                    // Get the last key (highest ID)
                    end($msgs);
                    $latestId = key($msgs);
                    $prefix = self::getCachePrefix();
                    apcu_store($prefix . 'chat_last_id_' . $gameid, $latestId, 3600);
                } else {
                    // No new messages: cache the current lastid (even 0) so subsequent polls
                    // are fast-polled without hitting the DB.
                    // Use a short TTL when empty (30s) so we periodically recheck for the first real message.
                    $prefix = self::getCachePrefix();
                    $ttl = ($lastid > 0) ? 3600 : 30;
                    apcu_store($prefix . 'chat_last_id_' . $gameid, $lastid, $ttl);
                }
            }

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