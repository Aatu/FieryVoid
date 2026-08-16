<?php
// global.php handles output buffering and compression
require_once 'global.php';

header('Content-Type: application/json; charset=utf-8');
// A poll response is never reusable, and global.php's session_cache_limiter('')
// means PHP sends no cache headers of its own — say so explicitly rather than
// leave it to an intermediary to guess.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

/*
 * SESSION LOCK — released before anything else happens.
 *
 * global.php has ALREADY called session_start(), so by the time this file runs the
 * per-session file lock is held; PHP would not drop it until shutdown. That matters
 * because a game.php tab runs THREE pollers against one session (this file twice —
 * global chat and game chat — plus gamedata.php), and the file session handler
 * serialises them all on that lock. gamedata.php closes at its top for exactly this
 * reason; chatdata.php used to close further down, AFTER the fast-poll below had
 * already exit()ed, so the cheapest request in the app was the one holding the lock
 * longest. $_SESSION stays readable after the close — it simply stops being written.
 */
$playerid = $_SESSION['user'] ?? null;
session_write_close();

/*
 * BATCHED POLL — ?chats=<gameid>:<lastid>,<gameid>:<lastid>
 *
 * game.php shows two chats (global and this game) and used to poll each with its own
 * request. They are answered from the same APCu segment by the same process, so the
 * second request bought nothing but a second round trip and a second slot in the
 * client's serialised request queue. This form asks for both at once and replies with
 * an object keyed by gameid: {"0":[...],"45":[...]}.
 *
 * The single-chat ?gameid=&lastid= form below is KEPT, and must stay: a browser that
 * had game.php open across a deploy goes on running the previously served inline JS
 * against this newly deployed file. (The reverse skew cannot happen — the JS is inline
 * in chat.php, so new JS only ever arrives with a page load that also brings this.)
 *
 * Pairs are capped because the parameter is attacker-controlled; a real page sends two.
 */
$fvChats = null;
if (isset($_GET['chats']) && is_string($_GET['chats'])) {
    $fvChats = [];
    foreach (explode(',', $_GET['chats']) as $pair) {
        if (substr_count($pair, ':') !== 1) continue;
        list($g, $l) = explode(':', $pair, 2);
        // ctype_digit rejects "-1", "1e3" and "" — all of which (int) would happily
        // turn into something plausible-looking.
        if (!ctype_digit($g) || !ctype_digit($l)) continue;
        $fvChats[(int)$g] = (int)$l;
        if (count($fvChats) >= 8) break;
    }
    if (!$fvChats) $fvChats = null;
}

// APCu Fast Poll, batched: exempt only if EVERY requested chat is unchanged, since one
// response covers them all. A single miss falls through, and ChatManager's own APCu
// check then still spares the chats that were individually up to date.
if ($fvChats !== null && function_exists('apcu_fetch')) {
    require_once dirname(__DIR__) . '/server/varconfig.php';
    $prefix = ($database_name ?? 'default') . '_';
    $allExempt = true;
    foreach ($fvChats as $g => $l) {
        $cached = apcu_fetch("{$prefix}chat_last_id_{$g}");
        if ($cached === false || $l < $cached) { $allExempt = false; break; }
    }
    if ($allExempt) {
        if(ob_get_length()) ob_clean();
        // Assembled by hand rather than json_encode()d: an array keyed [0 => []] is
        // sequential from PHP's point of view and would encode as [[]] — an ARRAY —
        // while [0 => [], 45 => []] encodes as an object. The client must not have to
        // cope with the shape changing depending on which chats happen to be open.
        $parts = [];
        foreach ($fvChats as $g => $l) $parts[] = json_encode((string)$g) . ':[]';
        echo '{' . implode(',', $parts) . '}';
        exit;
    }
}

// APCu Fast Poll: answer an unchanged chat without a DB connection at all. Key and
// prefix must match ChatManager (which writes it on every submit and re-seeds it
// after every DB read) — deliberately NOT deploy-versioned like Manager's gamedata
// prefix, because a chat message id means the same thing before and after a patch,
// so surviving a deploy is the desired behaviour here.
if (function_exists('apcu_fetch') && isset($_GET['gameid'], $_GET['lastid'])) {
    require_once dirname(__DIR__) . '/server/varconfig.php';
    $prefix = ($database_name ?? 'default') . '_';
    $gameid = (int) $_GET['gameid'];
    $lastid = (int) $_GET['lastid'];

    $lastMsgId = apcu_fetch("{$prefix}chat_last_id_{$gameid}");
    if ($lastMsgId !== false && $lastid >= $lastMsgId) {
        //error_log("Chatdata: Fast Poll EXEMPT (lastid={$lastid}, cached={$lastMsgId}, game={$gameid}) - " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        if(ob_get_length()) ob_clean();
        echo "[]";
        exit;
    }
}

// --- Required classes ---
require_once dirname(__DIR__) . '/server/server_load_guard.php';
require_once dirname(__DIR__) . '/server/controller/ChatManager.php';
require_once dirname(__DIR__) . '/server/controller/DBManager.php';
require_once dirname(__DIR__) . '/server/model/ChatMessage.php';
require_once dirname(__DIR__) . '/server/lib/Debug.php';

if (!$playerid) {
    http_response_code(401);
    if(ob_get_length()) ob_clean();
    echo json_encode(['error' => 'Not logged in.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$ret = ['error' => 'Omitting required data'];

try {
    // --- POST: Submit new message ---
    if (isset($_POST['gameid'], $_POST['message'])) {
        $gameid = (int) $_POST['gameid'];
        $message = trim((string)$_POST['message']);

        // Keep old behavior: allow gameid=0 for global
        if ($message === '') {
            throw new InvalidArgumentException('Message cannot be empty.');
        }

        $ret = ChatManager::submitChatMessage($playerid, $message, $gameid);

    // --- GET: Poll several chats at once ---
    } elseif ($fvChats !== null) {
        // getChatMessages already returns a JSON string and does its own APCu check
        // and re-seed per chat, so the batch is just those strings under their gameid.
        // A chat that errors reports it in its own slot; the others still answer.
        $parts = [];
        foreach ($fvChats as $g => $l) {
            $parts[] = json_encode((string)$g) . ':' . ChatManager::getChatMessages($playerid, $l, $g);
        }
        $ret = '{' . implode(',', $parts) . '}';

    // --- GET: Poll one chat (legacy shape; see the note above) ---
    } elseif (isset($_GET['gameid'], $_GET['lastid'])) {
        $gameid = (int) $_GET['gameid'];
        $lastid = (int) $_GET['lastid'];

        // Allow 0 for global chat and first poll
        if ($gameid < 0 || $lastid < 0) {
            throw new InvalidArgumentException('Invalid game ID or last message ID.');
        }

        $ret = ChatManager::getChatMessages($playerid, $lastid, $gameid);
    }

    // --- Output JSON ---
    if(ob_get_length()) ob_clean();
    if (is_string($ret)) {
        // ChatManager already returned JSON
        echo $ret;
    } else {
        echo json_encode($ret, JSON_NUMERIC_CHECK | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

} catch (Throwable $e) {
    $logid = Debug::error($e);
    http_response_code(500);
    if(ob_get_length()) ob_clean();
    echo json_encode([
        'error' => $e->getMessage(),
        'code'  => $e->getCode(),
        'logid' => $logid,
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}


exit;


/*
ob_start("ob_gzhandler"); 
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    require_once dirname(__DIR__) . '/server/controller/ChatManager.php';
    require_once dirname(__DIR__) . '/server/controller/DBManager.php';
    require_once dirname(__DIR__) . '/server/model/ChatMessage.php';
    require_once dirname(__DIR__) . '/server/lib/Debug.php';
    session_start();
        
    if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
//      print('{}');
    }else{
    
    
		if (isset($_POST["gameid"]) && isset($_POST["message"])){
			$playerid = $_SESSION["user"];
			$gameid = $_POST["gameid"];
			$message = $_POST["message"];
			
            
			$ret = ChatManager::submitChatMessage($playerid, $message, $gameid);
			
			
		}else if (isset($_GET["gameid"]) && isset($_GET["lastid"])){
			$playerid = $_SESSION["user"];
			$gameid = $_GET["gameid"];
			$lastid = $_GET["lastid"];

			$ret = ChatManager::getChatMessages($playerid, $lastid, $gameid);
		
		}else{
            $ret = '{"error":"Omitting required data"}';
		}
		
		print($ret);
	}
    
?>
*/