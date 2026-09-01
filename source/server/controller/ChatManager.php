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
     * Latched connect failure for THIS request. See Manager::$dbUnavailable for the
     * full reasoning; ChatManager is the most exposed of the three because
     * chatdata.php calls getChatMessages() once per requested chat (capped at 8, and
     * client-supplied), so an unlatched outage costs up to 8 connect attempts per
     * poll instead of one. The live log of 2026-09-01 caught pid 509813 logging two
     * identical "Too many connections" frames 1.1ms apart -- two chats, one request.
     */
    private static $dbUnavailable = null;

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
        if (self::$dbUnavailable !== null)
            throw self::$dbUnavailable;

        if (self::$dbManager == null) {
            try {
                self::$dbManager = new DBManager($database_host ?? "localhost", 3306, $database_name, $database_user, $database_password);
            } catch (Throwable $e) {
                self::$dbUnavailable = $e;
                throw $e;
            }
        }
    }
    
    /**
     * Rewrites every character above U+FFFF as an HTML numeric reference, so that
     * "🙂" is stored as "&#128578;".
     *
     * WHY THIS EXISTS
     * ---------------
     * DBManager opens its connection with mysqli_set_charset($c, 'utf8'), which is
     * MySQL's THREE-byte utf8 — it cannot represent anything outside the BMP, and
     * most emoji live outside it. Sent raw, an emoji either raises "Incorrect string
     * value" or (on a non-strict server) truncates the message at the emoji, so what
     * the player gets back is the first half of what they typed.
     *
     * The alternative is migrating the connection AND the chat.message column to
     * utf8mb4. That is the better long-term answer, but it is a schema change on a
     * live database and the connection is shared by every table in the game; this is
     * scoped to chat and needs no migration. It also stays correct AFTER such a
     * migration — the entities keep rendering, since the client already builds each
     * message as HTML.
     *
     * Runs AFTER htmlspecialchars, so a player who literally types "&#128578;" has
     * already had their ampersand escaped and sees their own text back, not a face.
     *
     * BMP characters are three bytes and pass through untouched — which covers the
     * older emoji (❤ U+2764, ✌ U+270C) and, importantly, the two joiners that hold a
     * compound emoji together: the variation selector U+FE0F and the ZWJ U+200D. The
     * astral halves either side of them are encoded, so 👨‍👩‍👧 and 👍🏽 both survive
     * intact rather than being flattened.
     */
    private static function encodeAstralCharacters($message)
    {
        // Every code point >= U+10000 is a four-byte sequence in UTF-8, so the bytes
        // can be recombined directly rather than reaching for mb_ord().
        $encoded = preg_replace_callback(
            '/[\x{10000}-\x{10FFFF}]/u',
            function ($match) {
                $bytes = $match[0];
                $codepoint = ((ord($bytes[0]) & 0x07) << 18)
                           | ((ord($bytes[1]) & 0x3F) << 12)
                           | ((ord($bytes[2]) & 0x3F) << 6)
                           |  (ord($bytes[3]) & 0x3F);
                return '&#' . $codepoint . ';';
            },
            $message
        );

        // preg_replace_callback returns null on malformed UTF-8 (the /u flag). Keep
        // the original in that case: a mangled message beats a silently empty one.
        return $encoded === null ? $message : $encoded;
    }

    public static function submitChatMessage($userid, $message, $gameid = 0)
    {
        try
        {
            $message = trim($message);
            if ($message == "")
                return "{}";
            
            $message = htmlspecialchars($message);
            $message = self::encodeAstralCharacters($message);

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