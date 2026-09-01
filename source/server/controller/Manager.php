<?php

require_once dirname(__DIR__) . '/varconfig.php'; 

set_error_handler(
    function ($errno, $errstr, $file, $line)
    {
        throw new ErrorException($errstr, $errno, 1, $file, $line);
    }
);

class Manager{

    /**
     * @var DBManager null
     */
    private static $dbManager = null;

    /**
     * Latched connect failure for THIS request.
     *
     * WHY THIS EXISTS
     * ---------------
     * initDBManager() used to assign $dbManager only on success, so a failed connect
     * left the static null. Every entry point catches the exception and returns an
     * error string rather than rethrowing, so the caller carries on and calls in
     * again -- and each call re-entered mysqli_connect. During the 2026-09-01
     * "Too many connections" bursts that meant the app hammered an already-exhausted
     * connection pool once per call instead of once per request: the one behaviour
     * here that actively deepened the outage rather than merely suffering it.
     *
     * With the latch, the first failure is remembered and every later call in the
     * same request rethrows it immediately without touching the network.
     *
     * WARNING: this must stay a PHP static and must NEVER be cached in APCu. Its
     * correct lifetime is exactly one request. A shared latch would let a single
     * unlucky request lock every other process out of a database that had already
     * recovered -- turning a blip into a self-inflicted outage.
     *
     * The identical exception OBJECT is rethrown, not a copy, so its trace still
     * points at the real connect failure; Debug::error dedupes on object identity so
     * this still produces one log frame per request, not one per call.
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
                self::$dbManager = new DBManager($database_host ?? "mariadb", 3306, $database_name, $database_user, $database_password);
            } catch (Throwable $e) {
                self::$dbUnavailable = self::asUnavailable($e);
                throw self::$dbUnavailable;
            }
        }
    }

    /**
     * Give a failed connect the stable, machine-readable code 300 the client can act on.
     *
     * WHY THIS IS NEEDED
     * ------------------
     * DBManager deliberately throws code 300 for "cannot reach the database" -- but that
     * line never ran. DBManager.php opens with mysqli_report(MYSQLI_REPORT_ERROR), and
     * this file registers a global set_error_handler that turns warnings into
     * ErrorException, so the failure is thrown from INSIDE mysqli_connect() before
     * DBManager can test its own return value. What reached the client was code 2
     * (E_WARNING), indistinguishable from any other error -- so the chat client had no
     * way to tell "database at capacity, this will pass" from a real fault, and showed a
     * modal dialog for every one. See CHAT_DB_RESILIENCE_PLAN.md item 7.
     *
     * Narrowing that set_error_handler was the alternative and was rejected: it is
     * registered at include time and converts every PHP warning in the whole request, in
     * any file, so changing it reaches far beyond this problem.
     *
     * The message is replaced with a fixed string rather than passed through. The catch
     * sites in this class interpolate getMessage() straight into a hand-built JSON
     * string, so a driver message containing a quote would produce a malformed body --
     * and the client would get a parse error instead of the marker this exists to send.
     * A fixed string is also one less piece of database detail on the wire. The original
     * is kept as the previous exception, and Debug::error logs the whole chain.
     */
    private static function asUnavailable(Throwable $e)
    {
        if ($e instanceof Exception && $e->getCode() === 300) {
            return $e;
        }

        return new Exception('Database unavailable', 300, $e);
    }

    // Lightweight APCu debug logger, gated on FV_APCU_DEBUG (defined local-only in
    // varconfig.php). No-op on live. Prefixes a tag so the lines are greppable.
    public static function apcuLog($msg) {
        if (defined('FV_APCU_DEBUG') && FV_APCU_DEBUG) {
            error_log('[APCu] ' . $msg);
        }
    }

    // Public so other entry points (e.g. gamedata.php's APCu fast-poll) can build
    // APCu keys with the exact same prefix — including the deploy-version suffix —
    // rather than hardcoding "<db>_" and silently reading the wrong keys after a deploy.
    public static function getCachePrefix() {
        global $database_name;
        // Use a safe fallback if for some reason db name is missing, though strictly it should be there.
        // Include the deploy version so a code patch automatically orphans all
        // APCu entries produced by the previous code. Without this, the per-game
        // JSON cache (validated only against the game's last_update timestamp) can
        // survive a deploy and keep serving old-shape gamedata — e.g. system ids
        // not matching the new system count — until the game is next touched.
        // AssetLoader is loaded by global.php on every live request; guard anyway
        // so any non-web context that lacks it degrades to the old prefix instead
        // of fataling (it just won't get deploy-scoped invalidation).
        $deployVersion = class_exists('AssetLoader') ? AssetLoader::getDeployVersion() : 'v0';
        return ($database_name ?? 'default') . '_' . $deployVersion . '_';
    }

    public static function setDBManager(DBManager $dbManager) {
        self::$dbManager = $dbManager;
    }

    private static function deleteOldGames()
    {
        try {
            self::initDBManager();
            self::$dbManager->startTransaction();
            $ids = self::$dbManager->getGamesToBeDeleted();
            self::$dbManager->deleteGames($ids);
                
            self::$dbManager->endTransaction(false);
        }catch(exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            throw $e;
        }
    }
    
    public static function leaveLobbySlot($user, $gameid, $slotid = null){
        try {
            self::initDBManager();
            self::$dbManager->leaveSlot($user, $gameid, $slotid);
            self::$dbManager->deleteEmptyGames();
            self::touchGame($gameid);
        }
        catch(exception $e) {
            throw $e;
        }
    
    }
    
    public static function getGameLobbyData($userid, $targetGameId = false){
        try {
            self::initDBManager();
            
            if ($targetGameId && is_numeric($targetGameId) && $targetGameId > 0) {
                return self::getTacGamedata($targetGameId, $userid, 0, 0, -1);
            }
        } catch(Exception $e) {
            Debug::error($e);
        }
    
        return null; // Always return *something*
    }
    
    public static function getGameLobbyDataJSON($userid, $gameid){
        try {
            $timestamp = 0;
            $prefix = self::getCachePrefix();
            $cacheKey = "{$prefix}gamelobby_{$gameid}_user_{$userid}_json";

            if (function_exists('apcu_fetch')) {
                $timestamp = apcu_fetch($prefix . 'game_' . $gameid . '_last_update');
                if (!$timestamp) {
                    // Heal cold cache: If no timestamp exists, create one so we can start caching this lobby
                    $timestamp = microtime(true);
                    if (function_exists('apcu_store')) {
                        apcu_store($prefix . 'game_' . $gameid . '_last_update', $timestamp, 3600);
                    }
                }

                $cached = apcu_fetch($cacheKey);
                if ($timestamp > 0 && $cached && isset($cached['ts']) && abs($cached['ts'] - $timestamp) < 0.001) {
                     //error_log("Manager: LOBBY JSON Cache HIT for Game $gameid User $userid");
                     return $cached['json'];
                }
            }

            // Lock to prevent stampede (User F5 spam)
            $lockKey = "{$prefix}gamelobby_lock_{$gameid}_{$userid}";
            if (function_exists('apcu_add') && !apcu_add($lockKey, 1, 10)) {
                // Lock held by another request (same user/game)
                 return '{"status": "GENERATING"}';
            }

            try {
                $lobbymodel = self::getGameLobbyData($userid, $gameid);
                
                if (!$lobbymodel) {
                    return "{}";
                }

                // getGameLobbyData (via getTacGamedata) returns a JSON error string if an exception occurs
                if (is_string($lobbymodel)) {
                    return $lobbymodel;
                }

                $data = $lobbymodel->stripForJson();
                unset($lobbymodel);

                if ($timestamp > 0) {
                    $data->last_update = $timestamp;
                }

                $json = json_encode($data, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR);
                unset($data);

                if ($timestamp > 0 && function_exists('apcu_store') && $json) {
                    //error_log("Manager: LOBBY JSON Cache STORE/MISS for Game $gameid User $userid");
                    apcu_store($cacheKey, ['ts' => $timestamp, 'json' => $json], 3600);
                }
            } finally {
                if (function_exists('apcu_delete')) {
                    apcu_delete($lockKey);
                }
            }

            return $json;

        } catch(Exception $e) {
            $logid = Debug::error($e);
            return json_encode([
                "error" => $e->getMessage(),
                "code" => $e->getCode(),
                "logid" => $logid,
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ]);
        }
    }

    public static function getTacGames($userid){
        
        if (!is_numeric($userid))
			return null;
        
        try {
            // Caching for Games List (Short TTL to prevent stampede)
            $prefix = self::getCachePrefix();
            $cacheKey = "{$prefix}gameslist_" . $userid;
            if (function_exists('apcu_fetch')) {
                 $cached = apcu_fetch($cacheKey);
                 if ($cached) return $cached;
                 
                 // Generation Lock
                 $lockKey = "{$prefix}gameslist_lock_{$userid}";
                 if (function_exists('apcu_add') && !apcu_add($lockKey, 1, 10)) {
                     // Return array with status object, game.php will need to handle this structure
                     return [['status' => "GENERATING"]]; 
                 }
            }

            self::initDBManager();
        
            $games = array_merge(
                self::$dbManager->getPlayerGames($userid),
                self::$dbManager->getLobbyGames($userid)
            );

            // Cache result
            if (function_exists('apcu_store')) {
                apcu_store($cacheKey, $games, 2); // 2 seconds cache
                apcu_delete("{$prefix}gameslist_lock_{$userid}");
            }
            
            return $games;
      
        }
        catch(exception $e) {
            throw $e;
        }
        
        return $games;
    }
	
	
	public static function getPlayerName($userid){
		if (!is_numeric($userid)) return 'NONNUMERIC';
		$playerName = '';
        try {
            self::initDBManager();        
            $playerName = self::$dbManager->getPlayerName($userid);
        }
        catch(exception $e) {
            $playerName = 'EXCEPTION';
        }        
        return $playerName;		
	}


    public static function getFirePhaseGames($userid){
        try {
            self::initDBManager();
            
            $games = self::$dbManager->getFirePhaseGames($userid);

            if ($games == null){
                return null;
            }            
        }
        catch(exception $e) {
            throw $e;
        }
        

        return $games;


    }

    /**
     * Recent Games window (games.php). Site-wide list of games with activity in the last
     * $days days; $userid only flags which rows are the caller's. Not cached — it is
     * fetched on demand when the player opens the window, not on every page load.
     */
    public static function getRecentGames($userid, $days = 7){
        if (!is_numeric($userid))
            return array();

        try {
            self::initDBManager();
            return self::$dbManager->getRecentGames((int)$userid, $days);
        }
        catch(exception $e) {
            throw $e;
        }
    }
    
    public static function shouldBeInGame($userid){
		if (!is_numeric($userid))
			return null;
			
        try {
            self::initDBManager();
            return self::$dbManager->shouldBeInGameLobby($userid);
        }
        catch(exception $e) {
            throw $e;
        }
    }
    
    public static function createGame($userid, $data){
        $data = json_decode($data, true);

        //var_export($data);
        
        $gamename = $data["gamename"];
        $background = $data["background"];
        $gamespace = $data["gamespace"];
        $description = $data["description"];
        $slots = array();
        $pointsA = $data["slots"][0]["points"];
        $poinstB = $data["slots"][1]["points"];
        $rules = new GameRules(isset($data["rules"]) ? $data["rules"] : []) ;

        foreach ($data["slots"] as $slot){
            $slots[] = new PlayerSlotFromJSON($slot);
        }
        
        try {
            self::initDBManager();
            self::$dbManager->startTransaction();
            $gameid = self::$dbManager->createGame($gamename, $background, $slots, $userid, $gamespace, $description, json_encode($rules));
            //SystemData::initSystemData(0, $gameid);
            self::takeSlot($userid, $gameid, 1);
            self::$dbManager->endTransaction(false);
            return $gameid;
        }
        catch(exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            throw $e;
        }
    
    }
    
    public static function takeSlot($userid, $gameid, $slot){
        
        try {
            self::initDBManager();
            //self::$dbManager->startTransaction();
            $ret = self::$dbManager->takeSlot($userid, $gameid, $slot);
            self::touchGame($gameid);
            return $ret;
            //self::$dbManager->endTransaction();
            
        }
        catch(exception $e) {
            throw $e;
        }
        
    }
    
    public static function getMapBackgrounds(){
        $handle = opendir("img/maps/");
        $list = array();
        while (false !== ($entry = readdir($handle))) {
        
            if (preg_match("/.*\.(bmp|jpeg|gif|png|jpg)$/i", $entry))
                $list[] = $entry;
        }
        
	sort($list);//alphabetical sort
        return $list;
    }
    
    public static function getAllFactions(){
    	return ShipLoader::getAllFactions();
    }
    
    public static function canCreateGame($userid){
        return true;
    }
    //Usernames are rendered into HTML by the legacy lobby and chat code, which
    //builds markup by string concatenation (e.g. gamelobby.js .playername, chat.php).
    //Keeping HTML metacharacters out of the name at the door is what makes those
    //call sites safe - do not relax this without escaping them first.
    const USERNAME_MIN_LENGTH = 3;
    const USERNAME_MAX_LENGTH = 45; //player.username is varchar(45)
    const USERNAME_PATTERN = '[\p{L}\p{N} ._\-]+'; //shared with the pattern= attribute in reg.php

    //Returns null when the name is acceptable, otherwise a message for the player.
    //reg.php prints the message as raw HTML, so it is pre-encoded here.
    public static function validateUsername($username) {
        if ($username !== trim($username))
            return "Username cannot start or end with a space.";

        $length = mb_strlen($username);
        if ($length < self::USERNAME_MIN_LENGTH || $length > self::USERNAME_MAX_LENGTH)
            return "Username must be between " . self::USERNAME_MIN_LENGTH . " and "
                 . self::USERNAME_MAX_LENGTH . " characters long.";

        if (!preg_match('/^' . self::USERNAME_PATTERN . '$/u', $username))
            return "Usernames may only contain letters, numbers, spaces and . _ - "
                 . "&mdash; these characters are not allowed: &amp; &#039; &quot; &lt; &gt; \\";

        return null;
    }

    public static function registerPlayer($username, $password) {
        try {
            //Enforced here rather than in reg.php so no future caller can bypass it.
            $invalid = self::validateUsername($username);
            if ($invalid !== null)
                return $invalid;

            self::initDBManager();
            $ret =  self::$dbManager->registerPlayer($username, $password);

            return $ret;
        }
        catch(exception $e) {
            Debug::error($e);
            return null;
        }
    }
    
    public static function changePassword($username, $passwordold, $passwordnew) {
        try {
            self::initDBManager();
            $ret =  self::$dbManager->changePassword($username, $passwordold, $passwordnew);
                      
            return $ret;
        }
        catch(exception $e) {
            Debug::error($e);
            return null;
        }
        
    }
    
    
    public static function authenticatePlayer($username, $password) {
        try {
            self::initDBManager();
            $ret =  self::$dbManager->authenticatePlayer($username, $password);

            return $ret;
        }
        catch(exception $e) {
            Debug::error($e);
            return false;
        }

    }

    // --- Discord turn notifications: profile.php backend ---

    private static $discordVerifyMaxAttempts = 5;

    // Page-safe row: never expose the pending verification CODE to the client.
    public static function getPlayerDiscordRow($playerid) {
        try {
            self::initDBManager();
            $row = self::$dbManager->getPlayerDiscordRow($playerid);
            if ($row) unset($row->discord_verify_code);
            return $row;
        }
        catch(exception $e) {
            Debug::error($e);
            return null;
        }
    }

    // Begin ownership verification: DM a one-time code to the entered Discord ID.
    // Binding only happens once that code is returned (verifyDiscordCode), so a
    // player can never claim a Discord ID they don't control.
    // Returns 'sent' | 'invalid' | 'cooldown' | 'dm_failed' | false.
    public static function startDiscordVerification($playerid, $discordId) {
        try {
            if (!class_exists('DiscordNotifier')) return false;
            $discordId = trim($discordId);
            if (!preg_match('/^\d{17,20}$/', $discordId)) return 'invalid';
            self::initDBManager();

            // Per-account cooldown so the code sender can't be looped to DM-bomb a
            // target ID. Best-effort (APCu); absent APCu just means no cooldown.
            if (function_exists('apcu_enabled') && apcu_enabled()) {
                global $database_name;
                $cdKey = ($database_name ?? 'default') . '_dverify_cd_' . (int)$playerid;
                if (!apcu_add($cdKey, 1, 30)) return 'cooldown';
                apcu_delete(($database_name ?? 'default') . '_dverify_att_' . (int)$playerid); // fresh code, reset attempts
            }

            $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $expires = time() + 600;   // 10 minutes
            self::$dbManager->setPlayerDiscordVerification($playerid, $discordId, $code, $expires);

            if (!DiscordNotifier::sendVerificationCode($discordId, $code)) {
                return 'dm_failed';
            }
            return 'sent';
        }
        catch(exception $e) {
            Debug::error($e);
            return false;
        }
    }

    // Complete verification. Returns 'verified' | 'expired' | 'mismatch' | 'none' | false.
    public static function verifyDiscordCode($playerid, $code) {
        try {
            self::initDBManager();
            $code = trim($code);
            $row = self::$dbManager->getPlayerDiscordRow($playerid);
            if (!$row || empty($row->discord_verify_id) || empty($row->discord_verify_code)) return 'none';

            if ($row->discord_verify_expires === null || (int)$row->discord_verify_expires < time()) {
                self::$dbManager->clearPlayerDiscordVerification($playerid);
                return 'expired';
            }

            if (!hash_equals((string)$row->discord_verify_code, (string)$code)) {
                // Bound-attempt limiter: a wrong guess can't be repeated forever
                // against one code (brute-force defence). Clear the challenge after
                // a few failures; no APCu -> clear immediately.
                $tooMany = true;
                if (function_exists('apcu_enabled') && apcu_enabled()) {
                    global $database_name;
                    $attKey = ($database_name ?? 'default') . '_dverify_att_' . (int)$playerid;
                    $ok = false;
                    $n = apcu_inc($attKey, 1, $ok, 900);
                    $tooMany = ($n >= self::$discordVerifyMaxAttempts);
                }
                if ($tooMany) self::$dbManager->clearPlayerDiscordVerification($playerid);
                return 'mismatch';
            }

            // Proven ownership — bind (transfers off any other account) + clear challenge.
            self::$dbManager->bindVerifiedDiscordId($playerid, $row->discord_verify_id);
            if (function_exists('apcu_enabled') && apcu_enabled()) {
                global $database_name;
                apcu_delete(($database_name ?? 'default') . '_dverify_att_' . (int)$playerid);
            }
            return 'verified';
        }
        catch(exception $e) {
            Debug::error($e);
            return false;
        }
    }

    // Cancel a pending challenge (keeps any existing verified binding).
    public static function cancelDiscordVerification($playerid) {
        try {
            self::initDBManager();
            self::$dbManager->clearPlayerDiscordVerification($playerid);
            return true;
        }
        catch(exception $e) {
            Debug::error($e);
            return false;
        }
    }

    // Full opt-out: unlink the Discord account entirely.
    public static function unlinkDiscord($playerid) {
        try {
            self::initDBManager();
            self::$dbManager->clearPlayerDiscord($playerid);
            return true;
        }
        catch(exception $e) {
            Debug::error($e);
            return false;
        }
    }

    // Post-verification connectivity test. Returns 'dm', 'channel', or false.
    public static function sendDiscordTestPing($playerid) {
        try {
            if (!class_exists('DiscordNotifier')) return false;   // autoload map not regenerated yet
            self::initDBManager();
            $row = self::$dbManager->getPlayerDiscordRow($playerid);
            if (!$row || empty($row->discord_id)) return false;
            return DiscordNotifier::sendTestPing(self::$dbManager, $playerid, $row);
        }
        catch(exception $e) {
            Debug::error($e);
            return false;
        }
    }
    
    public static function getTacGamedata($gameid, $userid, $turn, $phase, $activeship){
    
	    if (!is_numeric($gameid) || (!is_numeric($userid) &&  $userid !== null) || ($turn !== null && !is_numeric($turn)) || !is_numeric($phase) )
            return null;
        
        $gamedata = null;

        try {
            self::initDBManager();
            self::$dbManager->startTransaction();

            if ($turn === null)
                self::deleteOldGames ();

            //Todo: this should propably happen after submit game data...
            self::advanceGameState($userid, $gameid);

            if (self::$dbManager->isNewGamedata($gameid, $turn, $phase, $activeship)){
                //Debug::log("GAME: $gameid Player: $userid requesting gamedata, new found.");
                $gamedata = self::$dbManager->getTacGamedata($userid, $gameid);
                if ($gamedata == null)
                    return null;
                //print(var_dump($gamedata));
                $gamedata->prepareForPlayer();
                //Line below skips deployment Phase on Turn 1 for late-deploying slots - DK                
                if($gamedata->turn == 1 && $gamedata->phase == -1) Manager::updateLateDeployments($gamedata);  

            }else{              
                return null;
            }
            self::$dbManager->endTransaction(false);          
            return $gamedata;
        }catch(exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            $logid = Debug::error($e);
            return json_encode([
                "error" => $e->getMessage(),
                "code" => $e->getCode(),
                "logid" => $logid,
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ]);
        }
    }
    
    public static function updateAmmoInfo($shipid, $systemid, $gameid, $firingmode, $ammoAmount, $turn){		
		//as there is no separate ammo tracks - assume always first mode is used for ammo tracking purposes
        //self::$dbManager->submitAmmo($shipid, $systemid, $gameid, $firingmode, $ammoAmount, $turn);
		self::$dbManager->submitAmmo($shipid, $systemid, $gameid, 1, $ammoAmount, $turn);
    }
    
    public static function touchGame($gameid) {
        $prefix = self::getCachePrefix();
        if (function_exists('apcu_store')) {
            $ts = microtime(true);
            apcu_store($prefix . 'game_' . $gameid . '_last_update', $ts);
            self::apcuLog("TOUCH game=$gameid → last_update=$ts (invalidates cache) prefix=$prefix");
        }
    }

    public static function getTacGamedataJSON($gameid, $userid, $turn, $phase, $activeship, $force = false){
        
        try{
            // APCu Optimization: Inject Timestamp & Check JSON Cache
            $prefix = self::getCachePrefix();
            $timestamp = 0;
            $cacheKey = "{$prefix}game_{$gameid}_user_{$userid}_json";
            
            if (function_exists('apcu_fetch')) {
                 $timestamp = apcu_fetch($prefix . 'game_' . $gameid . '_last_update');
                 if (!$timestamp) {
                     $timestamp = microtime(true);
                     self::touchGame($gameid);
                 }
                 
                 // Check if we have a valid cached JSON for this user
                 // We only use cache if we are NOT forcing a refresh
                 if (!$force) {
                     $cached = apcu_fetch($cacheKey);
                     if ($cached && isset($cached['ts']) && abs($cached['ts'] - $timestamp) < 0.001) {
                         self::apcuLog("JSON HIT game=$gameid user=$userid ts=$timestamp");
                         return $cached['json'];
                     }
                     self::apcuLog("JSON MISS game=$gameid user=$userid ts=$timestamp" . ($cached ? " (stale cached ts=" . ($cached['ts'] ?? 'n/a') . ")" : " (no entry)"));
                 }
            
                // Lock to prevent stampede (User F5 spam)
                // Only lock if we are about to do the heavy lifting (Not hitting cache)
                $lockKey = "{$prefix}game_lock_{$gameid}_{$userid}";
                if (function_exists('apcu_add') && !apcu_add($lockKey, 1, 10)) {
                     return '{"status": "GENERATING"}';
                }
            }
    
            try {
                $t_start_getTacGamedata = microtime(true);
                $gdS = self::getTacGamedata($gameid, $userid, $turn, $phase, $activeship);
                $GLOBALS['dbg_getTacGamedata'] = microtime(true) - $t_start_getTacGamedata;
    
                if (!$gdS)
                    return "{}";
    
                //getTacGameData trying to return error string
                if (gettype($gdS) == "string") {
                    return $gdS;
                }
    
                if (!$force && $gdS->waiting && !$gdS->changed && $gdS->status != "LOBBY") {
                    if ($timestamp > 0) {
                        self::apcuLog("NO-CHANGE game=$gameid user=$userid → {last_update:$timestamp}");
                        return json_encode(["last_update" => $timestamp]);
                    }
                    return "{}";
                }
                
                //NEW VERSION FOR PHP 8 - Aug 2025
                $t_start_strip = microtime(true);
                $data = $gdS->stripForJson();
                $GLOBALS['dbg_stripForJson'] = microtime(true) - $t_start_strip;
    
                if ($timestamp > 0) {
                     $data->last_update = $timestamp;
                }
    
                unset($gdS); // Free the massive logic object memory BEFORE encoding
                
                $t_start_encode = microtime(true);
                $json = json_encode($data, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR);
                $GLOBALS['dbg_json_encode'] = microtime(true) - $t_start_encode;
                
                // Store in Cache
                if ($timestamp > 0 && function_exists('apcu_store') && $json) {
                    self::apcuLog("JSON STORE game=$gameid user=$userid ts=$timestamp bytes=" . strlen($json));
                    apcu_store($cacheKey, ['ts' => $timestamp, 'json' => $json], 3600);
                }
                
                unset($data); // free memory early
                return $json;
            } finally {
                if (function_exists('apcu_delete')) {
                    apcu_delete($lockKey);
                }
            }

        }
        catch(Exception $e) {
            $logid = Debug::error($e);
            return json_encode([
                "error" => $e->getMessage(),
                "code" => $e->getCode(),
                "logid" => $logid,
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ]);
        }
    
    }

    public static function getReplayGameData($userid, $gameid, $turn) {
        try{
            self::initDBManager();
            $game = self::$dbManager->getTacGame($gameid, 0);
            if (!$game) {
                return null;
            }

            $actualTurn = $game->turn;
            $gamedata = self::$dbManager->getTacGamedata($userid, $gameid, $turn);

            if ($gamedata == null)
                return null;

            $gamedata->prepareForPlayer($actualTurn > $turn);
            $gamedata->setTurn($turn);

            $json = json_encode($gamedata->stripForJson(), JSON_NUMERIC_CHECK);
            return $json;

        }
        catch(Exception $e) {
            $logid = Debug::error($e);
            return json_encode([
                "error" => $e->getMessage(),
                "code" => $e->getCode(),
                "logid" => $logid,
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ]);
        }
    }
       
    public static function submitSavedFleet($name, $userid, $points, $isPublic, $ships) {
        try {
            self::initDBManager();  
            $starttime = time();

            // ✅ Decode ships first, before DB work
            $ships = self::getSavedShipsFromJSON($ships, $userid);
            if (sizeof($ships) == 0) {
                throw new Exception("Ship data missing");
            }

            // ✅ Only start the DB transaction once we know we have valid data
            self::$dbManager->startTransaction();

            // Save fleet
            $listId = self::$dbManager->submitSavedList($name, $userid, $points, $isPublic);

            /* Battle damage & criticals are collected across the WHOLE fleet and written
               in two statements after the loop (PREBATTLE_DAMAGE_PLAN.md §4.6). A fleet
               saved out of a bloody 20-ship battle is several hundred damaged systems,
               and a row-at-a-time writer was a prepare/execute round trip for each of
               them inside one request. */
            $damageRows = array();
            $critRows   = array();
            //Per-system enhancements, batched the same way and for the same reason
            //(WEAPON_ENHANCEMENTS_PLAN.md §4.7).
            $sysEnhRows = array();

            // ✅ Now you can associate ships, enhancements, ammo with $listId
            foreach ($ships as $ship) {
                /* Re-sanitise BEFORE submitSavedShip, not after: that writer reads
                   $ship->pointCostSysEnh into tac_saved_ship.enhvalue, so the authoritative
                   total has to be on the ship by the time it runs. Re-derived rather than
                   trusted (D4) - and re-derived AGAIN on load (§4.7.1), because a saved fleet
                   outlives the blueprint it was priced against. */
                $cleanSysEnh = Enhancements::sanitiseSystemEnhancements(
                    $ship, $ship->systemEnhancements ?? array(),
                    PreBattleDamage::sanitise($ship, $ship->preBattleDamage ?? array()));
                $ship->pointCostSysEnh = $cleanSysEnh['total'];

                $shipId = self::$dbManager->submitSavedShip($listId, $userid, $ship);

                foreach ($cleanSysEnh['rows'] as $row) {
                    //[shipid, systemid, sysname, enhid, count, enhname, enhvalue]
                    $sysEnhRows[] = array($shipId, $row[6], $row[7], $row[0], $row[2], $row[1], $row[4]);
                }

                foreach($ship->enhancementOptions as $enhancementEntry){ //ID,readableName,numberTaken,limit,price,priceStep
                    $enhID = $enhancementEntry[0];
                    $enhName = Enhancements::getStoredEnhancementName($ship, $enhancementEntry); //choice-valued options store the PICK here, not the label
                    $enhNo = $enhancementEntry[2];
                    if ($enhNo > 0){ //actually taken
                        self::$dbManager->submitSavedEnhancement($listId, $shipId, $enhID, $enhNo, $enhName);
                    }
                }

                /* Battle damage & criticals carried by this fleet (PREBATTLE_DAMAGE_PLAN.md
                   §4.6). getSavedShipsFromJSON has already applied flightSize + populate(),
                   so fighter ordinals validate here too. Rows are accumulated, not written:
                   the two batch writers run once, after the loop. */
                $cleanDamage = PreBattleDamage::sanitise($ship, $ship->preBattleDamage ?? array());
                foreach (PreBattleDamage::BUCKETS as $bucket => $kind) {
                    foreach (($cleanDamage[$bucket] ?? array()) as $ref => $damageEntry) {
                        if (!empty($damageEntry['d']) || !empty($damageEntry['k'])) {
                            $damageRows[] = array(
                                $shipId, $kind, $ref,
                                $damageEntry['d'] ?? 0, $damageEntry['k'] ?? 0
                            );
                        }
                        foreach (($damageEntry['c'] ?? array()) as $critType => $critCount) {
                            //param is set only for the param-carrying classes and sanitise
                            //has already bounded it to an integer; null for everything else.
                            $critRows[] = array(
                                $shipId, $kind, $ref,
                                $critType, $critCount, $damageEntry['p'][$critType] ?? null
                            );
                        }
                    }
                }

                if($ship instanceof FighterFlight){
                        $firstFighter = $ship->systems[1];
                        $ammo = false;

                        foreach ($firstFighter->systems as $weapon){
                            if(isset($weapon->missileArray)){
                                $ammo = $weapon->missileArray[1]->amount;
                                break;
                            }
                        }

                        if ($ammo){
                            foreach($ship->systems as $fighter){
                                foreach ($fighter->systems as $weapon){
                                    if(isset($weapon->missileArray)){
                                        $weapon->missileArray[1]->amount = $ammo;
                                         self::$dbManager->submitSavedAmmo($listId, $shipId, $weapon->id, $weapon->firingMode, $ammo);
                                    }
                                }
                            }
                        }
                        else{//Marcin Sawicki: generalized version of gun ammo initialization for fighters (not for missile launchers!)
                            foreach($ship->systems as $fighter){
                                foreach($fighter->systems as $weapon){
                                    if(isset($weapon->ammunition) && (!isset($weapon->missileArray)) && ($weapon->ammunition > 0) ){
                                         self::$dbManager->submitSavedAmmo($listId, $shipId, $weapon->id, $weapon->firingMode, $weapon->ammunition);
                                    }
                                }
                            }
                        }
                    }else{
                        foreach($ship->systems as $systemIndex=>$system){
                            if(isset($system->missileArray)){
                                // this system has a missileArray. It uses ammo
                                foreach($system->missileArray as $firingMode=>$ammo){
                                     self::$dbManager->submitSavedAmmo($listId, $shipId, $system->id, $firingMode, $ammo->amount);
                                }
                            }
                            else if($system instanceof Weapon) { //count ammo for other weapons as well!
                                if(isset($system->ammunition) && ($system->ammunition > 0)){
                                     self::$dbManager->submitSavedAmmo($listId, $shipId, $system->id, $system->firingMode, $system->ammunition);
                                }
                            }
                        }
                    }
                }

                //Two statements for the whole fleet, however many wounds it carries.
                if ($damageRows) self::$dbManager->submitSavedDamageRows($listId, $damageRows);
                if ($critRows)   self::$dbManager->submitSavedCritRows($listId, $critRows);
                if ($sysEnhRows) self::$dbManager->submitSavedSystemEnhancementRows($listId, $sysEnhRows);

                self::$dbManager->endTransaction(false);

                $endtime = time();
                return json_encode([
                    'listId' => $listId,
                    'success' => true
                ]);

            } catch (Exception $e) {
                if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
                $logid = Debug::error($e);
                return json_encode([
                    "error" => $e->getMessage(),
                    "code" => $e->getCode(),
                    "logid" => $logid,
                    "file" => $e->getFile(),
                    "line" => $e->getLine()
                ]);
            }
    }


    public static function getSavedFleets($userid) {
        try {
            self::initDBManager(); 
            self::$dbManager->startTransaction();

            $fleets = self::$dbManager->getSavedFleets($userid);

            self::$dbManager->endTransaction(false);
            return $fleets;

        } catch (Exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            $logid = Debug::error($e);
            return [];
        }
    }   


    /**
     * $includeDamage / $includeCriticals (D3): the two INDEPENDENT load toggles. All four
     * combinations are valid - a fleet saved from a bloody battle can be reloaded pristine,
     * damage-only, crits-only, or fully. Both default true, so an old caller that passes
     * neither gets the whole fleet.
     */
    public static function loadSavedFleet(int $listid, bool $includeDamage = true, bool $includeCriticals = true): array
    {

        $fleet = [];
        $enhancementsByShip = [];
        $ammoByShip = [];
        $damageByShip = [];
        $critsByShip = [];
        $critDesc = [];
        $critTransient = [];   //{critClass => true} for the one-turn ones, for the lobby's label
        //Per-system refits that were dropped, clamped or re-priced on the way in (§4.7).
        $sysEnhNotices = [];
        //$fleetPoints = 0;

        try {
            self::initDBManager();
            self::$dbManager->startTransaction();

            $list = self::$dbManager->getSavedFleet($listid);
            // Load all ships for this fleet
            $ships = self::$dbManager->getSavedShips($listid);

            //Battle damage & criticals: ONE query each for the whole fleet, keyed by
            //listid, rather than two more per ship on top of the two below. Both tables
            //carry listid with an index on it (db/prebattleDamage.sql).
            $damageByShip = self::$dbManager->getSavedDamageForList($listid);
            $critsByShip  = self::$dbManager->getSavedCritsForList($listid);

            //Per-system enhancements: ONE query for the whole fleet, like the damage above.
            $sysEnhByShip = self::$dbManager->getSavedSystemEnhancementsForList($listid);

            // Load enhancements and ammo for all ships
            foreach ($ships as $ship) {
                $enhancementsByShip[$ship->id] = self::$dbManager->getSavedEnhancementsForShip($ship->id);
                $ammoByShip[$ship->id] = self::$dbManager->getSavedAmmoForShip($ship->id);
            }

            self::$dbManager->endTransaction(false);

            foreach ($ships as $ship) {
                //$shipCost = $ship->pointCost + $ship->pointCostEnh + $ship->pointCostEnh2;
                //if($ship instanceof FighterFlight) $shipCost = $shipCost/$ship->flightSize;
                //$fleetPoints += $shipCost;

                // Add enhancements
                Enhancements::setEnhancementOptions($ship);
                $shipEnh = $enhancementsByShip[$ship->id] ?? [];
                foreach ($shipEnh as $enhEntry) {
                    $enhID       = $enhEntry[0];
                    $numberTaken = $enhEntry[1];
                    foreach ($ship->enhancementOptions as &$option) {
                        if ($option[0] === $enhID) {
                            $option[2] = $numberTaken;
                            //Choice-valued options (index 7 = the list of things that can be picked)
                            //store the PICK in enhname and only an INDEX into that list in numbertaken.
                            //The list is rebuilt from disk on every load, so a ship added to or retired
                            //from the faction silently renumbers it - re-derive the index from the name,
                            //which is stable. An unresolvable name falls back to index 0 = "None".
                            if (!empty($option[7]) && isset($enhEntry[2]) && $enhEntry[2] !== '') {
                                $option[2] = 0;
                                foreach ($option[7] as $choiceIndex => $choice) {
                                    if ($choice[0] === $enhEntry[2]) {
                                        $option[2] = $choiceIndex;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    unset($option);
                }

                /* Per-system enhancements (WEAPON_ENHANCEMENTS_PLAN.md §4.7.1). AFTER
                   setEnhancementOptions above, which is what builds the offer list the
                   re-validation prices against.

                   ⭐ Re-validated and RE-PRICED, never trusted. A saved fleet has no expiry and
                   the blueprint it was priced against is PHP source that changes under it - a
                   contributor revising a hull is the ROUTINE case, not an edge case. Resolution
                   order, per row: no such systemid -> drop; name mismatch -> drop (D13); no
                   longer eligible -> drop; over the current limit -> clamp; then re-price.
                   Dropping is always the safe direction - the player loses points they get
                   straight back and can re-buy in two clicks, whereas silently relocating a
                   refit to whatever now sits at id 14 is not recoverable, because nobody
                   notices. Stored rows arrive as
                   [systemid, sysname, enhid, numbertaken, enhname, enhvalue]. */
                $storedSysEnh = array();
                $storedSysEnhTotal = 0;
                foreach (($sysEnhByShip[$ship->id] ?? array()) as $entry) {
                    $storedSysEnh[] = array(
                        $entry[2], $entry[4], (int)$entry[3], 0, (float)$entry[5], 0, (int)$entry[0], $entry[1]
                    );
                    $storedSysEnhTotal += (float)$entry[5];
                }
                if ($storedSysEnh) {
                    $cleanSysEnh = Enhancements::sanitiseSystemEnhancements($ship, $storedSysEnh);
                    $ship->systemEnhancements = $cleanSysEnh['rows'];
                    $ship->pointCostSysEnh    = $cleanSysEnh['total'];
                    /* ⚠️ SPLIT THE BUCKETS APART AGAIN. tac_saved_ship.enhvalue stores all THREE
                       added together (submitSavedShip), and getSavedShips hands the lot back as
                       pointCostEnh - so leaving it would double-count every refit the moment
                       doLoadFleet adds pointCostSysEnh on as well. Subtract what the refits
                       contributed WHEN SAVED, then let the freshly re-derived total stand beside
                       it: the difference between the two is exactly the re-pricing, and it lands
                       in the right bucket. */
                    $ship->pointCostEnh = max(0, (float)$ship->pointCostEnh - $storedSysEnhTotal);
                    //Report every drop / clamp / re-price ONCE, fleet-wide. Silent point changes
                    //on a loaded fleet are the kind of thing that gets noticed three battles later.
                    foreach ($cleanSysEnh['notices'] as $notice) {
                        $sysEnhNotices[] = $ship->name . ': ' . $notice;
                    }
                }

                // Add Ammo
                $shipAmmo = $ammoByShip[$ship->id] ?? [];
                foreach ($shipAmmo as $ammoEntry) {
                    list($systemid, $firingmode, $amount) = $ammoEntry;
                    $system = $ship->getSystemById($systemid);
                    if ($system) {
                        $system->setAmmo($firingmode, $amount);
                    }
                }

                /* Battle damage & criticals this fleet carries (PREBATTLE_DAMAGE_PLAN.md §4.7).
                   ⚠️ The toggle must prune the PAYLOAD, not just the preview:
                   $ship->preBattleDamage is what the client carries and re-POSTs at buy time,
                   so a declined kind left in it would be written to tac_damage/tac_critical
                   anyway and the toggle would be a lie. Hence filter() runs BEFORE the payload
                   is handed over, and applyToShip simply renders whatever survived.
                   Ordinals validate against $ship->flightSize - getSavedShips deliberately
                   leaves a flight at one fighter (§1.1), and the server populates from the
                   stored size at buy time. */
                $fullDamage = PreBattleDamage::sanitiseSavedRows(
                    $ship,
                    $damageByShip[$ship->id] ?? [],
                    $critsByShip[$ship->id] ?? []
                );
                $available = PreBattleDamage::contents($fullDamage);   //what the fleet HAS - messaging only
                $effective = PreBattleDamage::filter($fullDamage, $includeDamage, $includeCriticals);

                $ship->preBattleDamage    = $effective;   //the EFFECTIVE payload - this is what gets re-POSTed
                $ship->preBattleAvailable = $available;   //display-only, never submitted
                PreBattleDamage::applyToShip($ship, $effective);
                $critDesc      += PreBattleDamage::describeCriticals($effective);
                $critTransient += PreBattleDamage::transientCriticals($effective);

				foreach ($ship->systems as $system){
					$system->beforeTurn($ship, 0, 0);
				}

                $fleet[] = $ship; // store ship directly, no extra 'ship' key
            }

        } catch (Exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            Debug::error($e);
            return []; // safe fallback
        }

        // Return top-level array with points and ships
        // critDesc: {critClass => description} for the criticals actually applied, so the
        // lobby's SystemInfo popup can name a carried critical rather than showing its raw
        // class name. Per-ship preBattleDamage / preBattleAvailable ride on the ship objects.
        return [
            'list' => $list,
            'ships'  => $fleet,
            'critDesc' => $critDesc,
            //which of those classes are one-turn effects, so the lobby's editable critical
            //list can say so rather than showing them as permanent
            'critTransient' => $critTransient,
            //One string per per-system refit that was dropped, clamped or re-priced against the
            //CURRENT blueprint (§4.7.1). Empty on the ordinary case; the lobby says it once.
            'systemEnhancementNotice' => $sysEnhNotices
        ];
    }


    /**
     * Per-system critical CATALOGUE + cascade traits for ONE ship class, for the
     * gamelobby's pre-battle damage editor (PREBATTLE_DAMAGE_PLAN.md §11.2).
     *
     * Two things the lobby cannot get any other way:
     *
     *  1. `crits` — what a system's hit chart can produce. $possibleCriticals is
     *     PROTECTED, so it is in neither the static ship JSON (the generators json_encode
     *     the object) nor stripForJson. Only the derived, storable-filtered list is
     *     exposed, through ShipSystem::getPossibleCriticalTypes().
     *  2. `ssd` — survivesStructureDestruction, likewise protected. It reaches the static
     *     blueprint through ShipCompactor::annotateSystems, but only after a static
     *     REGEN; serving it here as well means the lobby's structure cascade is right on
     *     a tree whose bundle has not been rebuilt yet (a shield projection was going
     *     dark with its section - user report 2026-08-08).
     *
     * ⚠️ ONE ship class per request, and never getAllShipsStatic(null), which is the
     * documented cause of the deploy 503 on the live LiteSpeed workers.
     * ⚠️ The class name comes STRAIGHT OFF THE WIRE, and ShipLoader::getShipsByClass does
     * `new $name(...)` on whatever it is handed - so it is checked to be a BaseShip
     * subclass first, not merely a syntactically valid identifier. Same rule, and the same
     * reason, as the is_subclass_of($type, 'Critical') guard on the critical classes read
     * out of tac_critical: never construct an arbitrary class from client or DB input.
     */
    public static function getSystemCriticals($phpclass, $flightSize = 1)
    {
        if (!is_string($phpclass) || !preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $phpclass)
            || !is_subclass_of($phpclass, 'BaseShip')) {
            return ['error' => 'Unknown ship class'];
        }

        $flightSize = max(1, min(24, (int)$flightSize));

        $prefix = self::getCachePrefix();
        $cacheKey = "{$prefix}syscrits_{$phpclass}_{$flightSize}";
        if (function_exists('apcu_fetch')) {
            $cached = apcu_fetch($cacheKey);
            if ($cached) return $cached;
        }

        $byFaction = ShipLoader::getShipsByClass([$phpclass]);
        $ship = null;
        foreach ($byFaction as $ships) {
            if (isset($ships[$phpclass])) { $ship = $ships[$phpclass]; break; }
        }
        if (!$ship) return ['error' => 'Unknown ship class'];

        $types = [];
        $out = [
            'phpclass'   => $phpclass,
            'flightSize' => $flightSize,
            'systems'    => new stdClass(),
            'fighters'   => new stdClass(),
        ];

        if ($ship instanceof FighterFlight) {
            //Ordinals, matching the `ftr` bucket: in the lobby a flight is one sample
            //fighter plus a number, so every ordinal offers the same list. populate()
            //first so fighterByOrdinal has something to walk.
            $ship->flightSize = $flightSize;
            $ship->populate();

            $fighters = [];
            for ($ordinal = 1; $ordinal <= $flightSize; $ordinal++) {
                $fighter = PreBattleDamage::fighterByOrdinal($ship, $ordinal);
                if (!$fighter) continue;
                $crits = PreBattleDamage::offerableCriticalTypes($fighter);
                $types = array_merge($types, $crits);
                $fighters[(string)$ordinal] = $crits;
            }
            //(object) so the wire shape is always a MAP: system ids run from 0, so a PHP
            //array keyed 0,1,2… json_encodes as a JSON ARRAY and the client's
            //catalogue.systems[String(id)] lookup would be reading positions, not ids.
            if ($fighters) $out['fighters'] = (object)$fighters;
        } else {
            $systems = [];
            foreach ($ship->systems as $system) {
                $entry = [];
                $crits = PreBattleDamage::offerableCriticalTypes($system);
                if ($crits) {
                    $types = array_merge($types, $crits);
                    $entry['crits'] = $crits;
                }
                //written only when TRUE, like ShipCompactor::annotateSystems
                if (method_exists($system, 'getSurvivesStructureDestruction')
                    && $system->getSurvivesStructureDestruction()) {
                    $entry['ssd'] = true;
                }
                if ($entry) $systems[(string)$system->id] = $entry;
            }
            if ($systems) $out['systems'] = (object)$systems;   //map, not array - see above
        }

        //`all` is the editor's "every effect" expander - the narrow per-system list is
        //what B5W would actually roll, but a scenario author reproducing a specific
        //battle needs the crits that bespoke code applies (AmmoExplosion, LimpetBore …)
        //and which appear in no possibleCriticals table.
        $out['all'] = PreBattleDamage::allCriticalTypes();
        $out['meta'] = PreBattleDamage::criticalCatalogueMeta(array_merge($types, $out['all']));

        if (function_exists('apcu_store')) {
            apcu_store($cacheKey, $out, 3600);
        }

        return $out;
    }

    public static function deleteSavedFleet($id) {
        try {
            self::initDBManager(); 
            self::$dbManager->startTransaction();

            self::$dbManager->deleteSavedFleet($id);

            self::$dbManager->endTransaction(false);
            return json_encode([
                'id' => $id,
                'success' => true
            ]);

        } catch (Exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            $logid = Debug::error($e);
            return [];
        }
    }   

    public static function changeAvailabilityFleet($id): array {
        try {
            self::initDBManager(); 
            self::$dbManager->startTransaction();

            $newStatus = self::$dbManager->changeAvailabilityFleet($id);

            self::$dbManager->endTransaction(false);
            return [
                'id'        => $id,
                'success'   => true,
                'newStatus' => $newStatus
            ];
        } catch (Exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            $logid = Debug::error($e);
            return [
                'id'      => $id,
                'success' => false,
                'error'   => 'Failed to toggle fleet availability.'
            ];
        }
    }


    private static function getSavedShipsFromJSON($json, $userid) {

        $ships = array();
        $array = json_decode($json, true);
        if (!is_array($array)) return $ships;
    
        foreach ($array as $value) {
           
            $className = $value["phpclass"] ?? null;
            if (!$className) continue; // skip if class not defined
    
            /** @var BaseShip $ship */
            $ship = new $className(
                $value["id"] ?? -1,
                $userid ?? -1,
                $value["name"] ?? "Unnamed",
                $value["slot"] ?? 0
            );
    
            $ship->pointCostEnh = ($value["pointCostEnh"] ?? 0) + ($value["pointCostEnh2"] ?? 0);
    
            if ($ship instanceof FighterFlight) {
                $ship->flightSize = $value["flightSize"] ?? 1;
                $ship->populate();
            }

            //Pre-battle damage (PREBATTLE_DAMAGE_PLAN.md §4.6). Raw here, validated by
            //PreBattleDamage::sanitise in submitSavedFleet. Must stay AFTER populate():
            //flight ordinals resolve by position in $ship->systems.
            $ship->preBattleDamage = $value["preBattleDamage"] ?? array();

            $ship->enhancementOptions = $value["enhancementOptions"] ?? [];

            //Per-system enhancements (§4.7). Raw here, re-validated and RE-PRICED by
            //submitSavedFleet - a saved fleet outlives the blueprint it was priced against.
            $ship->systemEnhancements = $value["systemEnhancements"] ?? array();

            // Map Mine deployment properties from frontend payload
            $ship->bulkBuy = $value["bulkBuy"] ?? 1;

            /* REINFORCEMENTS_PLAN.md §0 - a saved fleet REMEMBERS which units were bought as
               reinforcements (user request 2026-08-28). constructSavedShips emits the key only
               when true, so an ordinary fleet's payload is unchanged and an older client that
               sends nothing simply saves everything front-line.

               ⚠️ THE REAL PROPERTY, not $reinforcementClaim, and that is safe HERE and only
               here. The claim exists because a POST-side ship built by getShipsFromJSON is put
               into a live TacGamedata, where a bare $reinforcement makes getTurnDeployed and
               getTurnPlaced answer 999 in every phase (plan §4 trap 14). These ships never see
               a TacGamedata at all: they are built, sanitised, written to tac_saved_ship and
               thrown away, and both turn accessors require a $gamedata argument nothing on this
               path can supply. */
            $ship->reinforcement = !empty($value["reinforcement"]);
    
            $systems = $value["systems"] ?? [];
            foreach ($systems as $i => $system) {
                $sys = $ship->getSystemById($i);
                
                if (isset($system["systems"]) && is_array($system["systems"])) {
                    foreach ($system["systems"] as $fightersys) {
                        $fig = $sys ? $sys->getSystemById($fightersys["id"] ?? -1) : null;
                        if (!$fig) continue;
                        
                            // ammo transfer
                            if (isset($fightersys["ammo"])) {
                                foreach ($fightersys["ammo"] as $i => $ammo) {
                                    if (isset($ammo)) {
                                        $fig->setAmmo($i, $ammo);
                                    }
                                }
                            }
    
                        
                    }
                } 
            }
    
            $ships[(int)($value["id"] ?? count($ships))] = $ship;
        }
    
        return $ships;
    }


    public static function submitTacGamedata($gameid, $userid, $turn, $phase, $activeship, $ships, $status, $slotid = 0){
        try {
        
            //    file_put_contents('/tmp/fierylog', "Gameid: $gameid submitTacGamedata ships:". var_export($ships, true) ."\n\n", FILE_APPEND);
            self::initDBManager();  
            $starttime = time();
            
            $ships = self::getShipsFromJSON($ships);
            
            if (sizeof($ships)==0)
				throw new Exception("Gamedata missing");
            //print(var_dump($ships));
            //$gamedata = new TacGamedata($gameid, $turn, $phase, $activeship, $userid, "", "", 0, "", 0);
            //$gamedata->ships = $ships;
            
            if (!self::$dbManager->getPlayerSubmitLock($gameid, $userid))
                throw new Exception("Failed to get player lock");
            
            //Debug("GAME: $gameid Player: $userid starting submit of phase $phase");
            
            self::$dbManager->startTransaction();
            

            /** @var TacGamedata $gdS */
            $gdS = self::$dbManager->getTacGamedata($userid, $gameid);

            SystemData::initSystemData($gdS->turn, $gdS->id);

            /* //OLD APPROACH
            if ($status == "SURRENDERED" && $gdS->status !== "SURRENDERED"){
                self::$dbManager->updateGameStatus($gameid, $status);
            } else if ($gdS->status === "SURRENDERED") {
                return "{}";
            }
            */

            //New slot-based appraoch to surrendering - DK - Aug 2025
            $isSurrender = ($status == "SURRENDERED");
            $gameEndedNow = false; //set below if this surrender left one team or fewer standing

            if ($isSurrender) {
                // Step 1: Update this player's slot surrendered value with game turn when they surrender
                self::$dbManager->updateSlotSurrendered($gameid, $userid, $gdS->turn);
            }

            if ($gdS->status !== "SURRENDERED") {
                // Step 2: Track alive teams
                $aliveTeams = [];
                $slots = self::$dbManager->getSlotsInGame($gameid);

                foreach ($slots as $slot) {

                    if ($slot->team === null) {
                        continue; // skip unassigned (shouldn't happen)
                    }

                    if (!isset($aliveTeams[$slot->team])) {
                        $aliveTeams[$slot->team] = false; // assume dead until proven alive
                    }

                    if ($slot->surrendered === null) { //Null is default, indicates they've never surrendered.
                        $aliveTeams[$slot->team] = true; // team still alive
                    }
                }

                // Step 3: Count alive teams
                $aliveCount = 0;
                foreach ($aliveTeams as $isAlive) {
                    if ($isAlive) {
                        $aliveCount++;
                    }
                }

                // Step 4: End game if one or zero teams remain
                if ($aliveCount <= 1) {
                    $gameEndedNow = true;
                    self::$dbManager->updateGameStatus($gameid, "SURRENDERED");
                    // In-memory $gdS->status still holds the old value — flag the
                    // game explicitly so DiscordNotifier never pings a dead game.
                    if (class_exists('DiscordNotifier')) DiscordNotifier::suppressGame($gameid);

                    // --- LADDER LOGIC START ---
                    // Only process ladder results if it's NOT Turn 1 (prevents recording early surrenders/setup errors)
                    if ($gdS->turn > 1) {
                        $rules = $gdS->rules;
                        if ($rules->hasRule('ladder') && $rules->callRule('ladder', array())) {
                            // Game is a ladder game and has just finished via surrender.
                            // Winners: Teams that are still "alive" (or if everyone surrendered, the last one standing implicitly).
                            // Losers: Teams that have surrendered.
                            
                            // Re-evaluate slots to be sure we have latest state
                            $finalSlots = self::$dbManager->getSlotsInGame($gameid);
                            $winningTeam = null;

                            // Identify the winning team (the one not surrendered)
                            // If aliveCount is 1, find that team.
                            // If aliveCount is 0, arguably everyone lost, or the last one to surrender "won"? 
                            // Let's assume standard flow: one team remains.
                            foreach ($finalSlots as $slot) {
                                if ($slot->surrendered === null) {
                                    $winningTeam = $slot->team;
                                    break;
                                }
                            }
                            
                            if ($winningTeam !== null) {
                                foreach ($finalSlots as $slot) {
                                    if ($slot->team == $winningTeam) {
                                        self::$dbManager->registerLadderResult($gameid, $slot->playerid, 'WIN');
                                    } else {
                                        self::$dbManager->registerLadderResult($gameid, $slot->playerid, 'LOSS');
                                    }
                                }
                            }
                        }
                    }
                    // --- LADDER LOGIC END ---
                }
            } else {
                //Game is already over. Nothing left to record - but the transaction and the
                //player submit lock taken above are still ours, and a bare return leaked both
                //(the lock then sat until its 15-minute expiry).
                self::$dbManager->endTransaction(false);
                self::$dbManager->releasePlayerSubmitLock($gameid, $userid);
                return "{}";
            }

            /* Surrender is a phase-independent ACTION, not a set of orders, and since the button
               moved out of the Initial Orders header into the top-right HUD it can arrive from
               any phase. So it deliberately bypasses the submit gauntlet below:
                 - "Turn already submitted" would reject a player who surrenders while waiting on
                   opponents, which is now a perfectly normal thing to do;
                 - the turn/phase/active-ship match tests only make sense for orders, and would
                   reject a click made a moment after the phase rolled over;
                 - running $phase->process() would commit whatever half-finished orders the client
                   happened to be holding, and in the Movement phase would hand the activation on
                   even when it belongs to somebody else.
               completeSurrender() does the one thing that is actually required: stop the player
               who just left from blocking the phase they left in. */
            if ($isSurrender) {
                self::completeSurrender($gdS, $userid, $gameEndedNow);

                self::$dbManager->endTransaction(false);
                self::$dbManager->releasePlayerSubmitLock($gameid, $userid);
                self::touchGame($gameid);

                if (class_exists('DiscordNotifier')) DiscordNotifier::flush(self::$dbManager, $gdS, $userid);

                return '{}';
            }

            if ($gameid != $gdS->id || $turn != $gdS->turn || $phase != $gdS->phase)
                throw new Exception("Unexpected orders");

            $phase = $gdS->getPhase();

            if ($gdS->hasAlreadySubmitted($userid))
                throw new Exception("Turn already submitted or wrong user");
                
            if ($gdS->status == "FINISHED")
                throw new Exception("Game is finished");

            if ($activeship != $gdS->activeship && array_diff($gdS->activeship, $activeship)){
                throw new Exception("Active ship does not match");
            }
            //print(var_dump($ships));

            if ($phase instanceof BuyingGamePhase){
                $phase->process($gdS, self::$dbManager, $ships, $slotid); // slotid passed here
            } else if ($phase instanceof DeploymentGamePhase){
                $phase->process($gdS, self::$dbManager, $ships);
            } else if ($phase instanceof InitialOrdersGamePhase){
                $phase->process($gdS, self::$dbManager, $ships);
            }else if ($phase instanceof MovementGamePhase){
                $phase->process($gdS, self::$dbManager, $ships, $activeship);
            }else if ($phase instanceof PreFiringGamePhase){
                $phase->process($gdS, self::$dbManager, $ships);                
            }else if ($phase instanceof FireGamePhase){
                $phase->process($gdS, self::$dbManager, $ships);
            }
                        
            self::$dbManager->endTransaction(false);
            
            self::$dbManager->releasePlayerSubmitLock($gameid, $userid);
            
            //Debug("GAME: $gameid Player: $userid SUBMIT OK");
            
            self::touchGame($gameid);

            // Discord turn notifications — strictly after commit + cache touch.
            // Catches movement hand-offs too (setNextActiveShip ran inside process()).
            if (class_exists('DiscordNotifier')) DiscordNotifier::flush(self::$dbManager, $gdS, $userid);

            $endtime = time();
            //Debug::log("SUBMITTING GAMEDATA - GAME: $gameid Time: " . ($endtime - $starttime) . " seconds.");
            return '{}';

        }catch(exception $e) {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            if (self::$dbManager !== null) self::$dbManager->releasePlayerSubmitLock($gameid, $userid);
            if (class_exists('DiscordNotifier')) DiscordNotifier::clear();   // rolled-back state must not ping
            $logid = Debug::error($e);
            return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
        }


    }

    /* Stop a player who has just surrendered from blocking the phase they surrendered in.
       Records NO orders of any kind.

       Every phase EXCEPT Movement is slot-gated - DBManager::checkIfPhaseReady counts the slots
       whose lastturn/lastphase have caught up with the game - so marking the slot done is all it
       takes; the next poll's advanceGameState picks it up. Movement is gated on the ACTIVE SHIP
       instead (checkIfPhaseReady skips phase 2 outright), hence the hand-off below.

       The slot IS still marked when the surrender ended the game ($gameEnded), even though that
       looks pointless: if the opponents had already committed this phase, marking it is what lets
       the turn roll on to the Fire Phase and Manager::changeTurn, which is what converts the
       game's SURRENDERED status into FINISHED. Skipping it froze the game mid-phase instead - the
       pre-2026-08 code got this for free, because a phase-1 surrender fell through into
       InitialOrdersGamePhase::process() and that ends with exactly this updatePlayerStatus call.
       Only the movement hand-off is suppressed on a game-ending surrender: nothing should be
       activated on a dead board, and every client is already in replay mode.

       Only the phase the player was actually sitting in needs covering: the surrendered slot is
       auto-completed for every later phase by the advance() of the phase before it - see
       DeploymentGamePhase, MovementGamePhase and PreFiringGamePhase, which all skip a slot whose
       `surrendered` is set. */
    private static function completeSurrender(TacGamedata $gdS, $userid, $gameEnded)
    {
        /* Whether the activation is sitting on this player's ships has to be answered BEFORE the
           in-memory mirror below, which is precisely what makes their fleet invisible to
           getActiveships(). Movement is the only phase where this matters. */
        $activeBefore = ($gdS->phase == 2) ? $gdS->getActiveships() : array();
        $handOverMovement = (!$gameEnded && $gdS->phase == 2 && count($gdS->getMyActiveShips()) > 0);

        /* $gdS was loaded before updateSlotSurrendered() wrote the row, so mirror that write in
           memory. Not cosmetic: BaseShip::getTurnDeployed() returns 999 for a slot with
           `surrendered` set, and that single rule is what lifts the fleet out of every activation
           list, initiative sweep and end-of-phase loop for the remainder of this request. */
        foreach ($gdS->slots as $slot) {
            if ($slot->playerid == $userid) {
                $slot->surrendered = $gdS->turn;
            }
        }

        //May already be marked done - surrendering while waiting on opponents is now normal, and
        //a slot the previous advance() skipped forward must not be rolled backwards.
        if (!$gdS->hasAlreadySubmitted($userid)) {
            self::$dbManager->updatePlayerStatus($gdS->id, $userid, $gdS->phase, $gdS->turn);
        }

        self::$dbManager->setPlayerWaitingStatus($userid, $gdS->id, true);

        if ($handOverMovement) {
            self::handOverMovementActivation($gdS, $activeBefore);
        }
    }

    /* Pass the Movement Phase activation on from a fleet that has just surrendered mid-phase.
       $activeBefore is the active ship list as it stood before that fleet was flagged.

       One hand-off is enough in both movement modes: the surrendered fleet now reads as
       undeployed, so neither path can hand the activation straight back to it. */
    private static function handOverMovementActivation(TacGamedata $gdS, array $activeBefore)
    {
        $movementPhase = new MovementGamePhase();

        if (!$gdS->rules->hasRule("getNewActiveShip")) {
            //Classic movement: one ship at a time, in ship order. setNextActiveShip() skips
            //anything not deployed by this turn and advance()s the whole phase if nothing is left.
            $movementPhase->setNextActiveShip($gdS, self::$dbManager);
            return;
        }

        /* Simultaneous movement activates a whole initiative CATEGORY at a time. Anybody else in
           the current category simply carries on; only when the surrendered fleet WAS the whole
           category do we step down to the next one.

           $activeBefore is passed to getNewActiveShip rather than $gdS->getActiveships(), which is
           empty in exactly that case - and SimultaneousMovementRule::getNewActiveShip needs a
           non-empty list to know which category it is stepping down FROM (it throws otherwise).
           This is also why SimultaneousMovementRule::processMovement is not reused here. */
        $stillActive = array();
        foreach ($gdS->getActiveships() as $ship) {
            $stillActive[] = $ship->id;
        }

        if (count($stillActive) === 0) {
            $stillActive = $gdS->rules->callRule("getNewActiveShip", array($gdS, $activeBefore));
        }

        if (count($stillActive) > 0) {
            $gdS->setActiveship($stillActive);
            self::$dbManager->updateGamedata($gdS);
            self::$dbManager->setPlayersWaitingStatusInGame($gdS->id, true);
            $gdS->rules->callRule("setActiveShipPlayersNotWaiting", array($gdS, self::$dbManager));
        } else {
            $movementPhase->advance($gdS, self::$dbManager);
        }
    }

    /* //Old method that didn't skip if no deployed ships - DK 2/6/25
    public static function advanceGameState($playerid, $gameid){
        try{
            if (!self::$dbManager->checkIfPhaseReady($gameid))
                return;
		
            if (!self::$dbManager->getGameSubmitLock($gameid))
            {
                //Debug::log("Advance gamestate, Did not get lock. playerid: $playerid");
                return;
            }



            
            $starttime = time();
            
            //Debug("GAME: $gameid Starting to advance gamedata. playerid: $playerid");
            
            self::$dbManager->startTransaction();
		
            $gamedata = self::$dbManager->getTacGamedata($playerid, $gameid);

            SystemData::initSystemData($gamedata->turn, $gamedata->id);

            $phase = $gamedata->getPhase();

            if ($phase instanceof BuyingGamePhase){
                $phase->advance($gamedata, self::$dbManager);
                self::changeTurn($gamedata);
            } else if ($phase instanceof DeploymentGamePhase){
                $phase->advance($gamedata, self::$dbManager);
            } else if ($phase instanceof InitialOrdersGamePhase){
                $phase->advance($gamedata, self::$dbManager);
            }else if ($phase instanceof MovementGamePhase){
                $phase->advance($gamedata, self::$dbManager);
            }else if ($phase instanceof FireGamePhase){
                $phase->advance($gamedata, self::$dbManager);
                self::changeTurn($gamedata);
            }
            
            if (TacGamedata::$currentPhase > 0){
                foreach ($gamedata->ships as $ship){
                    foreach ($ship->systems as $system){
                        $system->onAdvancingGamedata($ship, $gamedata);
                    }
                }
                
                self::$dbManager->updateSystemData(SystemData::getAndPurgeAllSystemData());
            }
            self::$dbManager->endTransaction(false);
            self::$dbManager->releaseGameSubmitLock($gameid);
        }
        catch(Exception $e)
        {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            self::$dbManager->releaseGameSubmitLock($gameid);
            throw $e;
        }
    }
*/

    public static function advanceGameState($playerid, $gameid){
        try{
           if (!self::$dbManager->checkIfPhaseReady($gameid))
                return;
            
            if (!self::$dbManager->getGameSubmitLock($gameid))
            {
                //Debug::log("Advance gamestate, Did not get lock. playerid: $playerid");
                return;
            }
           
            $starttime = time();
            
            //Debug("GAME: $gameid Starting to advance gamedata. playerid: $playerid");
            
            self::$dbManager->startTransaction();
		
            $gamedata = self::$dbManager->getTacGamedata($playerid, $gameid);

            SystemData::initSystemData($gamedata->turn, $gamedata->id);

            $phase = $gamedata->getPhase();
            if (!$gamedata->areDeployedShips()) {               
                while (!$gamedata->areDeployedShips() && $gamedata->status != "FINISHED") {
                    $phase = $gamedata->getPhase();

                    if ($phase instanceof BuyingGamePhase){
                        $phase->advance($gamedata, self::$dbManager);
                        self::changeTurn($gamedata);                                            
                    } else if ($phase instanceof DeploymentGamePhase){
                        $phase->advance($gamedata, self::$dbManager);
                    } else if ($phase instanceof InitialOrdersGamePhase){
                        $phase->advance($gamedata, self::$dbManager);
                    } else if ($phase instanceof MovementGamePhase){
                        $phase->advance($gamedata, self::$dbManager);
                    } else if ($phase instanceof PreFiringGamePhase){
                        $phase->advance($gamedata, self::$dbManager);                        
                    } else if ($phase instanceof FireGamePhase){
                        $phase->advance($gamedata, self::$dbManager);
                        self::changeTurn($gamedata);
                    }

                    $phase = $gamedata->getPhase();

                    if ($gamedata->turn > 200) break; //Safety break just in case.
                }
            } else {
                if ($phase instanceof BuyingGamePhase){
                    $phase->advance($gamedata, self::$dbManager);
                    self::changeTurn($gamedata);
                } else if ($phase instanceof DeploymentGamePhase){
                    $phase->advance($gamedata, self::$dbManager);
                } else if ($phase instanceof InitialOrdersGamePhase){
                    $phase->advance($gamedata, self::$dbManager);
                } else if ($phase instanceof MovementGamePhase){
                    $phase->advance($gamedata, self::$dbManager);
                } else if ($phase instanceof PreFiringGamePhase){
                    $phase->advance($gamedata, self::$dbManager);                     
                } else if ($phase instanceof FireGamePhase){
                    $phase->advance($gamedata, self::$dbManager);
                    self::changeTurn($gamedata);
                }
            }  
            //if (TacGamedata::$currentPhase > 0){
            //Keep original logic here for Turn 1, but then adjust to accommodate Deployment phases AFTER Turn 1
            if ($gamedata->turn == 1 && TacGamedata::$currentPhase > 0 || $gamedata->turn > 1 && TacGamedata::$currentPhase >= -1){            
                foreach ($gamedata->ships as $ship){
                    foreach ($ship->systems as $system){
                        $system->onAdvancingGamedata($ship, $gamedata);
                    }
                }
                
                self::$dbManager->updateSystemData(SystemData::getAndPurgeAllSystemData());
            }
            self::$dbManager->endTransaction(false);
            self::$dbManager->releaseGameSubmitLock($gameid);

            self::touchGame($gameid); // Ensure APCu knows about the advance

            // Discord turn notifications — strictly after commit. Catches all
            // phase/turn boundaries incl. the multi-phase while-loop above (the
            // ops replay collapses it to one ping per player with the final state).
            // $playerid is the poller whose request performed the advance — online
            // by definition, and excluded as the triggering user.
            if (class_exists('DiscordNotifier')) DiscordNotifier::flush(self::$dbManager, $gamedata, $playerid);
        }
        catch(Exception $e)
        {
            if (self::$dbManager !== null) self::$dbManager->endTransaction(true);
            if (self::$dbManager !== null) self::$dbManager->releaseGameSubmitLock($gameid);
            if (class_exists('DiscordNotifier')) DiscordNotifier::clear();   // rolled-back state must not ping
            throw $e;
        }
    }
  

    //New function called in Manager::getTacGamedata() to search for slots that skip Deployment on Turn 1 - DK July 2025
    public static function updateLateDeployments($gamedata){
        foreach($gamedata->slots as $slot){
            if($slot->depavailable > 1){
                //PLACEMENT turn, not arrival turn: reinforcements pick their entry hexes a turn
                //early (BaseShip::getTurnPlaced). So a slot arriving on turn 2 places during
                //Turn 1's Deployment phase alongside the main fleets and must NOT be skipped
                //here; only turn-3-and-later arrivals still sit Turn 1 out.
                $placeTurn = $gamedata->getMinTurnPlacedSlot($slot->slot, $slot->depavailable);
                if($placeTurn > 1){ //Bases and Terrain will need to deploy on Turn 1 still
                    //Set lastphase, and lastTurn for slot to intial phase on next turn.
                    self::$dbManager->updatePlayerSlotPhase($gamedata->id, $slot->playerid, $slot->slot, -1, 1);
                }
            }
        }
    }


    private static function changeTurn($gamedata){

        $gamedata->setTurn( $gamedata->turn+1 );

        /* //Old method which only create Deployment Phases on Turn 1.
        if ($gamedata->turn === 1)
        {
            $gamedata->setPhase(-1);
        }else{
            $gamedata->setPhase(1);
        }
        */
        //Now we always try and make a Deployment Phase, but slots will be set to skip it in FireGamePhase if they are not are scheduled to deploy.
        $gamedata->setPhase(-1);

        $gamedata->setActiveship(-1);

        if (($gamedata->turn > 1 && $gamedata->isFinished()) || ($gamedata->status === "SURRENDERED")){
            $gamedata->status = "FINISHED";
        }
        else{
            $gamedata->status = "ACTIVE";
        }

        self::$dbManager->updateGamedata($gamedata);

       // if ($gamedata->turn > 1){
         //   self::checkRegen($gamedata);
        //}

        //Reload AFTER updateGamedata so the freshly-bumped turn/phase are
        //visible, and AFTER FireGamePhase has finished (which is the caller).
        //FireGamePhase advance operates on its own local $servergamedata, so
        //any ship spawned mid-phase (mines via missile.php, launched fighter
        //flights via Hangar::criticalPhaseEffects, etc.) is absent from the
        //outer $gamedata->ships. Reloading here ensures those spawned ships
        //get an iniative entry written for the new turn — otherwise they
        //load with the default "N/A" iniative next turn and the
        //SimultaneousMovementRule can't match them to any category, leaving
        //them visible in OOB but unselectable in the Movement Phase.
        $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);

        self::generateIniative($servergamedata);

        foreach ($servergamedata->ships as $key=>$ship){
            $movement = Movement::setPreturnMovementStatusForShip($ship, $servergamedata->turn, $servergamedata);
            self::$dbManager->submitMovement($servergamedata->id, $ship->id, $servergamedata->turn, $movement, true);
        }
    }

    private static function generateIniative(TacGamedata $gamedata){
        if ($gamedata->rules->hasRule("generateIniative")) {
            $gamedata->rules->callRule("generateIniative", $gamedata);
        } else {
            foreach ($gamedata->ships as $key=>$ship){
                $mod =  $ship->getCommonIniModifiers( $gamedata );
                $iniBonus =  $ship->getInitiativebonus($gamedata);
                $ship->iniative = Dice::d(100) + $iniBonus + $mod;    
            }
	    /*Initiative ties are displayed wrongly in the interface due to changes intended for simultaneous movement.
	    	to prevent this from happening, forcefully break ties here - sort ships appropriately and make their Ini different by adding appropriate value
	    	side effect would be that displayed Ini rolled would be wrong (possibly even out of bounds), but then absolute Ini has little meaning in game - and relative one will be correct 
	    */
	    $gamedata->doSortShips(); //make sure they're in proper order
            $addIni = 0;
	    $prevIni = null;
	    foreach ($gamedata->ships as $key=>$ship){
		    if(($prevIni !== null) && ($ship->iniative > $prevIni)){ //ship initiative is greater than previous even without modifier - modifier no longer needed, reset
			$addIni = 0;    
		    }
		    $ship->iniative += $addIni;
		    if ($ship->iniative == $prevIni){ //actual tie! increase Ini of tied unit (and all further ones) by 1
			$ship->iniative++;	
			$addIni++;
		    }
		    $prevIni = $ship->iniative;
            }
		
        }
	    
        self::$dbManager->submitIniative($gamedata->id, $gamedata->turn, $gamedata->ships);
    }
    
    private static function getShipsFromJSON($json) {

        // Defensive: if input is already an array, skip json_decode
        if (is_array($json)) {
            $array = $json;
        } else {
            $array = json_decode($json, true);
        }

        $ships = array();
        //$array = json_decode($json, true);
        if (!is_array($array)) return $ships;
    
        foreach ($array as $value) {
            $movements = array();
            if (isset($value["movement"]) && is_array($value["movement"])) {
                foreach($value["movement"] as $i => $move) {
                    $movement = new MovementOrder(
                        $move["id"] ?? -1,
                        $move["type"] ?? null,
                        new OffsetCoordinate($move["position"] ?? [0, 0]),
                        $move["xOffset"] ?? 0,
                        $move["yOffset"] ?? 0,
                        $move["speed"] ?? 0,
                        $move["heading"] ?? 0,
                        $move["facing"] ?? 0,
                        $move["preturn"] ?? false,
                        $move["turn"] ?? 0,
                        $move["value"] ?? 0,
                        $move["at_initiative"] ?? false
                    );
                    $movement->requiredThrust = $move["requiredThrust"] ?? 0;
                    $movement->assignedThrust = $move["assignedThrust"] ?? 0;
                    //Carry the 'forced' flag through the POST (Gravitic Augmenter free jinks are
                    //marked forced=true). submitMovement skips forced moves so they never persist -
                    //they are re-added transiently every load. Without this whitelist the flag is
                    //lost and the free jink would be written to the DB, doubling next load.
                    $movement->forced = $move["forced"] ?? false;

                    $movements[$i] = $movement;
                }
            }
    
            $EW = array();
            if (isset($value["EW"]) && is_array($value["EW"])) {
                foreach($value["EW"] as $i => $EWdata) {
                    $EWentry = new EWentry(
                        -1,
                        $EWdata["shipid"] ?? -1,
                        $EWdata["turn"] ?? 0,
                        $EWdata["type"] ?? "",
                        $EWdata["amount"] ?? 0,
                        $EWdata["targetid"] ?? null
                    );
                    $EW[$i] = $EWentry;
                }
            }
    
            $className = $value["phpclass"] ?? null;
            if (!$className) continue; // skip if class not defined
    
            /** @var BaseShip $ship */
            $ship = new $className(
                $value["id"] ?? -1,
                $value["userid"] ?? -1,
                $value["name"] ?? "Unnamed",
                $value["slot"] ?? 0
            );

			$ship->team = $value["team"] ?? 0;
    
            $ship->pointCostEnh = ($value["pointCostEnh"] ?? 0) + ($value["pointCostEnh2"] ?? 0);
            $ship->setMovements($movements);
            $ship->EW = $EW;
            
            if (isset($value["bulkBuy"])) {
                $ship->bulkBuy = $value["bulkBuy"];
            }

            if ($ship instanceof FighterFlight) {
                $ship->flightSize = $value["flightSize"] ?? 1;
                $ship->populate();
            }

            //Pre-battle damage (PREBATTLE_DAMAGE_PLAN.md §4.3). Carried RAW here and
            //validated by PreBattleDamage::sanitise at the one place it is consumed -
            //BuyingGamePhase::process. Every other phase ignores the field, so a client
            //cannot inject damage mid-game.
            //⚠️ Must stay AFTER the populate() above: flight ordinals resolve by position
            //in $ship->systems, so the fighters have to exist first.
            $ship->preBattleDamage = $value["preBattleDamage"] ?? array();

            /* Reinforcements (REINFORCEMENTS_PLAN.md §4 Stage 1). Carried RAW here and consumed by
               BuyingGamePhase::process alone, which checks the game rule and only then writes the
               real $ship->reinforcement. Every other phase ignores the field, so a client cannot
               flag a unit into hyperspace mid-game.
               ⚠️ DELIBERATELY NOT $ship->reinforcement. That property is read by getTurnDeployed
               and getTurnPlaced, and a POST-side ship carries no $arrivalTurn - so writing it here
               would make every POSTed reinforcement answer 999 to both accessors in every phase,
               silently early-returning Hangar::generateIndividualNotes and
               HangarOps::validateDeployBayOrders, neither of which resolves through
               $gamedata->getShipById() the way DeploymentGamePhase::validateDeployment now does.
               See BaseShip::$reinforcementClaim and plan trap 3.
               ⚠️ The saved-fleet parser above (getSavedShipsFromJSON) writes the REAL property from
               the same wire key, and the difference is the point: those ships are built, sanitised,
               written to tac_saved_ship and thrown away without ever entering a TacGamedata, so
               neither turn accessor can be reached. These ships do enter one. */
            $ship->reinforcementClaim = !empty($value["reinforcement"]);

            /* THE MANIFEST (REINFORCEMENTS_PLAN.md §3.5, Stage 5) - the ONE reinforcement field a
               client is trusted to send in a live game, and even then only as a claim.
               InitialOrdersGamePhase::process re-validates it against the SERVER-side ships and
               writes NULL for anything it does not believe.

               ⚠️ $arrivalTurn IS DELIBERATELY NOT WHITELISTED and must never be. It is written only
               by the end-of-formation-turn deviation sweep; a client that could set it could bring
               its own fleet out of hyperspace a turn early, at a hex of its choosing, with no
               exit in between. $reinforcement is not whitelisted here either - see the claim
               property above.

               (int) rather than a raw carry: mysqli and JSON disagree about number types, and the
               validation downstream compares it against real ship ids. 0 is normalised to null so
               "unassigned" has exactly one representation. */
            $arrivalVia = isset($value["arrivalVia"]) ? (int)$value["arrivalVia"] : 0;
            $ship->arrivalVia = ($arrivalVia > 0) ? $arrivalVia : null;

            $ship->enhancementOptions = $value["enhancementOptions"] ?? [];

            /* Per-system enhancements (WEAPON_ENHANCEMENTS_PLAN.md §4.5). Carried RAW here and
               validated by Enhancements::sanitiseSystemEnhancements at the one place it is
               consumed - BuyingGamePhase::process - which re-resolves every systemid, re-checks
               eligibility and REPLACES the client's prices with the server's own (D4). Every
               other phase ignores the field, so a client cannot inject a refit mid-game. */
            $ship->systemEnhancements = $value["systemEnhancements"] ?? array();

            $systems = $value["systems"] ?? [];
            foreach ($systems as $i => $system) {
                $sys = $ship->getSystemById($i);

                if (isset($system["power"]) && is_array($system["power"])) {
                    foreach ($system["power"] as $power) {
                        $powerEntry = new PowerManagementEntry(
                            $power["id"] ?? -1,
                            $power["shipid"] ?? -1,
                            $power["systemid"] ?? -1,
                            $power["type"] ?? "",
                            $power["turn"] ?? 0,
                            $power["amount"] ?? 0
                        );
                        if ($sys) {
                            $sys->setPower($powerEntry);
                        }
                    }
                }
    
                if (isset($system["fireOrders"]) && is_array($system["fireOrders"])) {
                    $fires = [];
                    foreach($system["fireOrders"] as $fo) {
                        $fireOrder = new FireOrder(
                            -1,
                            $fo["type"] ?? "",
                            $fo["shooterid"] ?? -1,
                            $fo["targetid"] ?? -1,
                            $fo["weaponid"] ?? -1,
                            $fo["calledid"] ?? -1,
                            $fo["turn"] ?? 0,
                            $fo["firingMode"] ?? 1,
                            0, 0, $fo["shots"] ?? 0, 0, 0,
                            $fo["x"] ?? 0,
                            $fo["y"] ?? 0,
                            $fo["damageclass"] ?? null
                        );
                        //Carry the client-supplied notes through. The FireOrder ctor doesn't
                        //take notes, and weapons that need to pass custom per-shot data to
                        //server-side resolution (e.g. Hypergraviton Blaster transfer-target
                        //list) encode it here. Safe: server-side notes writes happen later
                        //during firing resolution as appends/overwrites; nothing reads the
                        //incoming client notes value except weapons that opt in.
                        $fireOrder->notes = $fo["notes"] ?? "";
                        if ($sys) {
                            $fires[] = $fireOrder;
                        }
                    }
                    if ($sys) $sys->setFireOrders($fires);
                }
    
                if (isset($system["systems"]) && is_array($system["systems"])) {
                    foreach ($system["systems"] as $fightersys) {
                        $fig = $sys ? $sys->getSystemById($fightersys["id"] ?? -1) : null;
                        if (!$fig) continue;
    
                        if (isset($fightersys["fireOrders"]) && is_array($fightersys["fireOrders"])) {
                            $fires = [];
                            foreach($fightersys["fireOrders"] as $fo) {
                                $fireOrder = new FireOrder(
                                    -1,
                                    $fo["type"] ?? "",
                                    $fo["shooterid"] ?? -1,
                                    $fo["targetid"] ?? -1,
                                    $fo["weaponid"] ?? -1,
                                    $fo["calledid"] ?? -1,
                                    $fo["turn"] ?? 0,
                                    $fo["firingMode"] ?? 1,
                                    0, 0, $fo["shots"] ?? 0, 0, 0,
                                    $fo["x"] ?? 0,
                                    $fo["y"] ?? 0,
                                    $fo["damageclass"] ?? null
                                );
                                //Carry client-supplied notes through the FIGHTER branch too (the
                                //main-ship branch already does this). MinorThoughtPulsar encodes its
                                //free thrust allocation as "MTP|hit|shots|dmg" here; read in
                                //beforeFiringOrderResolution. Safe: server-side notes are written later
                                //during firing resolution; nothing reads incoming notes except opt-in weapons.
                                $fireOrder->notes = $fo["notes"] ?? "";
                                $fires[] = $fireOrder;
                            }
    
                            // ammo transfer
                            if (isset($fightersys["ammo"])) {
                                foreach ($fightersys["ammo"] as $i => $ammo) {
                                    if (isset($ammo)) {
                                        $fig->setAmmo($i, $ammo);
                                    }
                                }
                            }
    
                            $fig->setFireOrders($fires);
                        }
    
                        if (isset($fightersys["individualNotesTransfer"])) {
                            $fig->individualNotesTransfer = $fightersys["individualNotesTransfer"];
                            $fig->doIndividualNotesTransfer();
                        }

                        //Some fighter systems CAN be boosted now
                        // --- inside foreach ($system["systems"] as $fightersys) { ... }
                        if (isset($fightersys["power"]) && is_array($fightersys["power"])) {
                            $powers = []; // different name
                            foreach ($fightersys["power"] as $p) {
                                $powerEntry = new PowerManagementEntry(
                                    $p["id"] ?? -1,
                                    $p["shipid"] ?? -1,
                                    $p["systemid"] ?? -1,
                                    $p["type"] ?? "",
                                    $p["turn"] ?? 0,
                                    $p["amount"] ?? 0
                                );
                                $powers[] = $powerEntry;
                            }
                            $fig->setPower($powers);
                        }

                        //Some fighter systems CAN be boosted now
                        // --- inside foreach ($system["systems"] as $fightersys) { ... }
                        if (isset($fightersys["power"]) && is_array($fightersys["power"])) {
                            $powers = []; // different name
                            foreach ($fightersys["power"] as $p) {
                                $powerEntry = new PowerManagementEntry(
                                    $p["id"] ?? -1,
                                    $p["shipid"] ?? -1,
                                    $p["systemid"] ?? -1,
                                    $p["type"] ?? "",
                                    $p["turn"] ?? 0,
                                    $p["amount"] ?? 0
                                );
                                $powers[] = $powerEntry;
                            }
                            $fig->setPower($powers);
                        }
                    }
                }
    
                if (isset($system["individualNotesTransfer"])) {

                    if ($sys) {
                        $sys->individualNotesTransfer = $system["individualNotesTransfer"];
                        $sys->doIndividualNotesTransfer();
                    }
                }
            }
    
            //$ships[(int)($value["id"] ?? count($ships))] = $ship;
            $ships[$value["id"] ?? uniqid('ship_')] = $ship; //Apply a unique entry, as it seemed Loaded Fleets were overwriting a single ship sometimes.  
        }
    
        return $ships;
    }
  
    public static function insertIndividualNote($note){                
		self::$dbManager->insertIndividualNote($note);
    }  
    
    public static function insertSystemData($data) {
        self::$dbManager->insertSystemData($data);
    }
    
    public static function insertSingleMovement($gameid, $shipid, $movement){                
		self::$dbManager->insertMovement($gameid, $shipid, $movement);
    }        
    
    public static function insertSingleFiringOrder($gamedata, $fireOrder)
    {
		return self::$dbManager->submitSingleFireorder($gamedata->id, $fireOrder);

    }
    
    public static function insertSingleShip($gamedata, $ship, $userid){
		//submitShip returns LAST_INSERT_ID() via mysqli_fetch_object, which yields a
		//STRING. TacGamedata::getShipById compares $ship->id === $id with STRICT ===,
		//so a string id on a freshly-spawned ship fails that lookup for the rest of the
		//same advance (e.g. a Fighter Bomb flight spawned in fireWeapons is then unfindable
		//in setCriticals, so syncIntegratedStructureCoupling mistakes it for a combat loss
		//and shaves carrier structure). Cast to int so the in-memory id matches.
		$id = (int)self::$dbManager->submitShip($gamedata->id, $ship, $userid);
		$ship->id = $id;
		$gamedata->ships[$id] = $ship;
		return $id;
    }
    
    public static function insertSingleEnhancement($gameData, $id, $enhID, $enhNo, $enhName){
		self::$dbManager->submitEnhancement($gameData->id, $id, $enhID, $enhNo, $enhName);
    }

    public static function insertSingleFlightSize($gameid, $shipid, $flightSize){
		self::$dbManager->submitFlightSize($gameid, $shipid, $flightSize);
    }

    //Hangar Ops Stage 21.5: persist a reduced enhancement cost on an existing
    //ship row (partial-launch remnant gives up its launched share). Mutate
    //$ship->pointCostEnh in memory alongside this so the current request agrees.
    public static function insertSingleEnhValue($shipid, $enhValue){
		self::$dbManager->submitEnhValue($shipid, (int)$enhValue);
    }
                 

    //Used by Pakmara Plasma Web to retrieve fire orders in workflow and get most recent id etc
    public static function retrieveFiringOrdersForWeapon($gamedata, $shooterid, $weaponid)
    {	
		$fireOrders = self::$dbManager->getFireOrdersForWeapon($gamedata->id, $shooterid, $weaponid, $gamedata->turn);	
		return $fireOrders;
    }    

    //Used by systems that boost outside of Initial Orders to prevent duplication of power entries. Not used by anything at the moment since Torvalus Shading Field got changed to Notes
    public static function removePowerEntriesForTurn($gameid, $shipid, $systemid, $turn){              
		self::$dbManager->removePowerEntriesForTurn($gameid, $shipid, $systemid, $turn);	
    }

    public static function getLadderStandings()
    {
        self::initDBManager();
        return self::$dbManager->getLadderStandings();
    }

    public static function registerLadderPlayer($playerid)
    {
        self::initDBManager();
        return self::$dbManager->registerLadderPlayer($playerid);
    }

    public static function removeLadderPlayer($playerid)
    {
        self::initDBManager();
        return self::$dbManager->removeLadderPlayer($playerid);
    }

    public static function isLadderPlayer($playerid)
    {
        self::initDBManager();
        return self::$dbManager->isLadderPlayer($playerid);
    }


    public static function getLadderHistory($playerid)
    {
        self::initDBManager();
        return self::$dbManager->getLadderHistory($playerid);
    }

}
