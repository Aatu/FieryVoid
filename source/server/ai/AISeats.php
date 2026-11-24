<?php
/**
 * CPU opponent - which slots of a game a bot sits in (TACTICAL_AI_PLAN.md sections 3.1 and 9).
 *
 * A bot is an ordinary row in `player` (is_cpu = 1) holding an ordinary slot. What makes the slot
 * a CPU seat is tac_playeringame.cpu: JSON {level, personality, seed}, per game and per seat.
 *
 * ⚠️ ONE BOT PER SEAT WITHIN A GAME. "Has this player finished the phase" is recorded per player
 * (DBManager::updatePlayerStatus), so two seats held by the same bot would be one player with two
 * slots. That works for a human who plans both fleets at once; the bot plays one seat at a time.
 *
 * Every entry point refuses when the feature is off - the server never seats a bot, whatever a
 * request says (plan section 2).
 */
class AISeats
{
    //May $playerid create or change CPU seats on this server?
    public static function allowedFor($playerid)
    {
        return Features::enabledFor(AIConfig::FLAG, $playerid);
    }

    /* Seat a free bot in slot $slot of lobby game $gameid. Returns the bot's player id.
       Throws when it cannot: feature off, game not in its lobby, a ladder game (ruling A1: never),
       slot taken, or no bot left that is not already in this game. */
    public static function seat($gameid, $slot, $level = null, $personality = null, $seed = null)
    {
        if (!Features::reachable(AIConfig::FLAG)) throw new Exception("CPU opponents are not available.");

        $gameid = (int)$gameid;
        $slot = (int)$slot;
        $db = Manager::getDBManager();

        $game = $db->getGameRowLight($gameid);
        if (!$game || $game['status'] !== 'LOBBY') throw new Exception("A CPU player can only be seated while the game is in its lobby.");

        $rules = json_decode($game['rules'], true);
        if (is_array($rules) && !empty($rules['ladder'])) throw new Exception("A ladder game cannot have a CPU player.");

        $taken = array();
        foreach ($db->getCpuSeats($gameid) as $seat) $taken[$seat['playerid']] = true;

        $botId = null;
        foreach ($db->getBotIds() as $candidate) {
            if (!isset($taken[$candidate])) { $botId = $candidate; break; }
        }
        if ($botId === null) throw new Exception("No CPU player is free for this game.");

        if (!$db->takeSlot($botId, $gameid, $slot)) throw new Exception("That slot cannot be given to a CPU player.");

        $settings = array(
            'level' => in_array($level, AIConfig::LEVELS, true) ? $level : AIConfig::DEFAULT_LEVEL,
            'personality' => in_array($personality, AIConfig::PERSONALITIES, true) ? $personality : AIConfig::DEFAULT_PERSONALITY,
            'seed' => ($seed === null) ? random_int(1, 2000000000) : (int)$seed
        );
        $db->setSlotCpu($gameid, $slot, json_encode($settings));

        self::forget($gameid);
        Manager::touchGame($gameid);

        return $botId;
    }

    //Empty a CPU seat again: the bot's fleet goes with it, exactly as a human's does on leaving.
    public static function unseat($gameid, $slot)
    {
        if (!Features::reachable(AIConfig::FLAG)) throw new Exception("CPU opponents are not available.");

        $gameid = (int)$gameid;
        $slot = (int)$slot;
        $db = Manager::getDBManager();

        $seats = $db->getCpuSeats($gameid);
        if (!isset($seats[$slot])) return false;

        $game = $db->getGameRowLight($gameid);
        if (!$game || $game['status'] !== 'LOBBY') throw new Exception("A CPU player can only be removed while the game is in its lobby.");

        $db->leaveSlot($seats[$slot]['playerid'], $gameid, $slot);
        $db->setSlotCpu($gameid, $slot, null);

        self::forget($gameid);
        Manager::touchGame($gameid);

        return true;
    }

    /* Create Game (plan section 8.1): seat a bot in every slot of the posted list marked `cpu`.
       Slot 1 is the creator's own and is never one. A slot that cannot be seated - a ladder game,
       no bot left - is logged and simply stays open for a human: a game is never refused over it. */
    public static function seatMarkedSlots($creatorId, $gameid, $slots)
    {
        if (!self::allowedFor($creatorId) || !is_array($slots)) return;

        foreach ($slots as $slot) {
            if (!is_array($slot) || empty($slot['cpu']) || !isset($slot['id']) || (int)$slot['id'] <= 1) continue;

            try {
                self::seat($gameid, (int)$slot['id'], isset($slot['cpuLevel']) ? $slot['cpuLevel'] : null);
            } catch (Exception $e) {
                Debug::error($e);
            }
        }
    }

    /* A slot has just been left in lobby game $gameid. The bots go too when nobody is left who
       could commit their fleets: the creator holds no slot any more, or no human does. Their
       slots become ordinary open ones. Never throws - a player must always be able to leave. */
    public static function releaseAbandoned($gameid)
    {
        if (!Features::reachable(AIConfig::FLAG)) return;

        try {
            $gameid = (int)$gameid;
            $db = Manager::getDBManager();

            $seats = $db->getCpuSeats($gameid);
            if (count($seats) == 0) return;

            $game = $db->getGameRowLight($gameid);
            if (!$game || $game['status'] !== 'LOBBY') return;
            if ($db->holdsSlot($gameid, $game['creator'])) return;

            foreach ($seats as $slot => $seat) self::unseat($gameid, $slot);
        } catch (Throwable $e) {
            Debug::error($e);
        }
    }

    /* Does this game have a CPU seat at all? Asked on every full gamedata request of every game
       while the feature is on, so the answer is kept in APCu; without APCu it is one indexed query. */
    public static function gameHasCpu($gameid)
    {
        $gameid = (int)$gameid;
        $key = self::cacheKey($gameid);

        if (function_exists('apcu_fetch')) {
            $cached = apcu_fetch($key);
            if ($cached === 'y') return true;
            if ($cached === 'n') return false;
        }

        $has = count(Manager::getDBManager()->getCpuSeats($gameid)) > 0;
        if (function_exists('apcu_store')) apcu_store($key, $has ? 'y' : 'n', 300);

        return $has;
    }

    private static function forget($gameid)
    {
        if (function_exists('apcu_delete')) apcu_delete(self::cacheKey($gameid));
    }

    private static function cacheKey($gameid)
    {
        return Manager::getCachePrefix() . 'game_' . (int)$gameid . '_hascpu';
    }
}
