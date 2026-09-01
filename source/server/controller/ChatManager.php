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
                self::$dbUnavailable = self::asUnavailable($e);
                throw self::$dbUnavailable;
            }
        }
    }

    /**
     * Stamp a failed connect with code 300, the marker chat.php uses to tell "the
     * database is briefly unreachable" (transient — stay quiet, keep polling) from a
     * real fault (worth a dialog). See Manager::asUnavailable for the full reasoning on
     * why DBManager's own code 300 never survives, and why the message is replaced
     * rather than passed through. This class is the one that matters for it: chatdata.php
     * is what the player has open when the database goes away.
     */
    private static function asUnavailable(Throwable $e)
    {
        if ($e instanceof Exception && $e->getCode() === 300) {
            return $e;
        }

        return new Exception('Database unavailable', 300, $e);
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
            // NOTE: this gate is why a "clamp $lastid to the cached value" fix in the
            // empty-result branch below would be dead code — everything with
            // $lastid >= $lastMsgId has already returned by then.
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
                    // No new messages. What gets cached here is the WATERMARK that every
                    // other player's fast poll is then tested against, so it must never
                    // be higher than an id that really exists.
                    //
                    // It used to be $lastid — which is the CLIENT'S CLAIM. chatdata.php's
                    // ctype_digit rejects "-1" and "1e3" but places no upper bound, so a
                    // logged-in player sending chats=7183:999999 wrote 999999 here with a
                    // one-hour TTL. That does not hide messages from anyone: the fast-poll
                    // test is $lastid >= $lastMsgId, which a real client holding a real id
                    // simply fails. It does something worse — it makes every one of them
                    // fail, so every poll from every player in that game falls through to
                    // MySQL for the life of the entry. One request could switch the
                    // DB-sparing fast path off for a whole game chat for an hour.
                    //
                    // ⚠️ The obvious cheap fix — "clamp to the previously cached value" —
                    // CANNOT WORK HERE, and it looks like it does. The fast-poll gate at
                    // the top of this method already returned for every case where
                    // $lastid >= $cachedLastId, so on arrival here a warm cache is ALWAYS
                    // higher than the claim and the clamp can never fire. The poisoning
                    // happens on a COLD cache, which is exactly what such a clamp has
                    // nothing to compare against.
                    //
                    // So ask the database instead. We are already connected and have
                    // already paid a round trip (this branch is only reachable on a
                    // fast-poll MISS), and MAX(id) is an index lookup on a table 3-day
                    // retention keeps at a few dozen rows. Cost lands once per TTL per
                    // active chat; correctness no longer depends on the client.
                    $trueMaxId = self::$dbManager->getMaxChatMessageId($gameid);

                    // Belt and braces: never publish a watermark above what the DB just
                    // vouched for. A stale-LOW value is harmless — it costs one extra DB
                    // read on the next poll and is corrected by it — so low is always the
                    // safe direction to err in.
                    $watermark = min($lastid, $trueMaxId);
                    if ($watermark < 0) $watermark = 0;

                    // Keep the original short TTL for a chat with no messages at all, so
                    // the very first message is picked up promptly rather than after an
                    // hour of fast-polled "[]".
                    $ttl = ($trueMaxId > 0) ? 3600 : 30;

                    $prefix = self::getCachePrefix();
                    apcu_store($prefix . 'chat_last_id_' . $gameid, $watermark, $ttl);
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