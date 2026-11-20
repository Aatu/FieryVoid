<?php
/**
 * CPU opponent - one bot makes one submission (TACTICAL_AI_PLAN.md section 3.2).
 *
 * The bot is a player: it loads the game as its own player would, decides, and submits through
 * Manager::submitShipsForPlayer - the body a browser's POST runs, line for line. Nothing in a
 * phase, handler or rule knows a bot is there (principle P1).
 *
 * A phase with a decider (Deployment, Initial Orders, Movement, Firing so far) plays its decision; a phase without
 * one plays the fallback (AIFallback), which is also what a refused decision is replaced with.
 *
 * A refusal is an AI defect and is logged as one (tac_ai_log, kind 'defect'). A bot refused
 * AIConfig::STALL_LIMIT times at the same point of the game surrenders its seat, so a human is
 * never left waiting on a broken bot (plan section 4, "stall guard").
 */
class TacticalAI
{
    /* Phases to play on the fallback although a decider exists. For the harnesses only (a ladder
       against "the bot without stage X", the hit-rate comparison of stage T3): nothing in the
       game sets it. */
    public static $without = array();

    //One line about the last Initial Orders decision, for tac_ai_log.
    public static $lastSummary = '';

    /* Play $seat's bot once, if it owes the game anything. $seat is a DBManager::getCpuSeats row.
       Returns array('status' => ..., 'error' => text or null):
         'idle'         nothing owed (not its turn, already submitted, game not running)
         'submitted'    one submission accepted
         'rejected'     the server refused the submission; logged, and counted towards the stall guard
         'surrendered'  the stall guard gave the seat up */
    public static function play($gameid, array $seat)
    {
        $gameid = (int)$gameid;
        $botId = (int)$seat['playerid'];
        $db = Manager::getDBManager();

        $gamedata = $db->getTacGamedata($botId, $gameid);
        if (!$gamedata || !self::owes($gamedata, $botId)) return self::result('idle');

        $turn = $gamedata->turn;
        $phase = $gamedata->phase;
        $activeship = $gamedata->activeship;
        $at = $turn . ':' . $phase . ':' . json_encode($activeship);

        $state = $db->getAiState($gameid, $botId);
        $refusals = (isset($state['stall']['at']) && $state['stall']['at'] === $at) ? (int)$state['stall']['n'] : 0;

        if ($refusals >= AIConfig::STALL_LIMIT) {
            $ships = AIOrderBuilder::build(array_map(array('AIOrderBuilder', 'entry'), AIFallback::ownUnits($gamedata, $botId)));
            unset($gamedata);
            $error = self::submit($gameid, $botId, $turn, $phase, $activeship, $ships, 'SURRENDERED');
            $db->aiLog($gameid, $botId, $turn, $phase, 'defect',
                'Gave the seat up after ' . $refusals . ' refused submissions.' . ($error === null ? '' : ' The surrender was refused too: ' . $error), AIConfig::LOG_CAP);
            return self::result($error === null ? 'surrendered' : 'rejected', $error);
        }

        /* The decision. A phase with a decider uses it; a phase without one, a decider that threw,
           and any retry after a refusal play the fallback (principle P3). Deciders so far:
           Deployment (stage T1a), Initial Orders (stage T3 - power and EW; masks $gamedata too),
           Movement (stage T1b - holds course, steers clear of terrain),
           Firing (stage T2 - direct fire; it masks $gamedata in place, as the view of the bot's
           own player, which is why the fallback below reads nothing a mask touches). */
        $entries = null;
        $summary = null;
        if ($refusals == 0 && !in_array((int)$phase, self::$without, true)) {
            try {
                if ($phase == -1) $entries = DeployDecider::orders($gamedata, $botId, $seat);
                else if ($phase == 1) {
                    $entries = self::initialOrders($gamedata, $botId, $seat);
                    $summary = self::$lastSummary;
                }
                else if ($phase == 2) {
                    $entries = MoveDecider::orders($gamedata, $botId, $seat);
                    $summary = MoveDecider::$lastSummary; //null unless a flight was moved
                }
                else if ($phase == 3) {
                    $entries = FireDecider::orders($gamedata, $botId, $seat);
                    $summary = FireDecider::$lastSummary;
                }
            } catch (Throwable $e) {
                Debug::error($e);
                $db->aiLog($gameid, $botId, $turn, $phase, 'defect', 'Decider threw: ' . get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')', AIConfig::LOG_CAP);
                $entries = null;
                $summary = null;
            }
        }
        if ($entries === null) $entries = AIFallback::orders($gamedata, $botId);
        $ships = AIOrderBuilder::build($entries);
        unset($gamedata, $entries); //the load is the big thing in memory, and process() makes its own

        $error = self::submit($gameid, $botId, $turn, $phase, $activeship, $ships, '');

        if ($error === null) {
            if ($summary !== null) $db->aiLog($gameid, $botId, $turn, $phase, 'decision', $summary, AIConfig::LOG_CAP);
            if ($refusals > 0) {
                unset($state['stall']);
                $db->setAiState($gameid, $botId, $turn, $state);
            }
            return self::result('submitted');
        }

        $state['stall'] = array('at' => $at, 'n' => $refusals + 1);
        $db->setAiState($gameid, $botId, $turn, $state);
        $db->aiLog($gameid, $botId, $turn, $phase, 'defect', 'Submission refused: ' . $error, AIConfig::LOG_CAP);

        return self::result('rejected', $error);
    }

    /* INITIAL ORDERS (stage T3): power first, then electronic warfare - a scanner switched off for
       want of power has no points to give. Returns the entries, one per unit the bot owns; the
       line for tac_ai_log is left in $lastSummary.

       Power is decided on the bot's own ships, which no mask touches. EW is decided on the MASKED
       load (principle P2); the one thing taken from before the mask is the enemy's EW of LAST
       turn, which every player was shown then (EWDecider::remembered). */
    public static function initialOrders(TacGamedata $gamedata, $botId, array $seat)
    {
        $remembered = EWDecider::remembered($gamedata, $botId);
        $power = PowerDecider::plan($gamedata, $botId);

        $gamedata->prepareForPlayer();
        $view = new AIView($gamedata, $botId, AIConfig::seatLevel($seat), AIConfig::seatSeed($seat));
        //Hard's boosts (stage T5f-4): on the masked view, as they depend on where the enemy is - and before EW, which spends a boosted scanner
        $power = PowerDecider::boost($view, $power);
        $ew = EWDecider::plan($view, $remembered);

        /* Ballistics (stage T4) last: a launch is priced with the EW just decided, so that split
           is stood on the bot's own ships first - what a player's page holds when he clicks a
           torpedo on to a target after setting his EW. */
        foreach (AIFallback::ownUnits($gamedata, $botId) as $unit) {
            $id = (int)$unit->id;
            if (!isset($ew[$id])) continue;
            $rows = array();
            foreach ($unit->EW as $entry) {
                if ((int)$entry->turn !== (int)$gamedata->turn) $rows[] = $entry;
            }
            foreach ($ew[$id] as $row) $rows[] = new EWentry(-1, $id, $row['turn'], $row['type'], $row['amount'], $row['targetid']);
            $unit->EW = $rows;
        }
        $launches = BallisticDecider::systems(BallisticDecider::plan($view), $gamedata->turn);

        $entries = array();
        foreach (AIFallback::ownUnits($gamedata, $botId) as $unit) {
            $entry = AIOrderBuilder::entry($unit);
            $id = (int)$unit->id;
            if (isset($ew[$id])) $entry['EW'] = $ew[$id];
            if (isset($power[$id])) {
                foreach ($power[$id] as $row) {
                    if (!isset($entry['systems'][$row['systemid']])) $entry['systems'][$row['systemid']] = array('id' => $row['systemid'], 'power' => array());
                    $entry['systems'][$row['systemid']]['power'][] = $row;
                }
            }
            if (isset($launches[$id])) {
                foreach ($launches[$id] as $systemId => $system) {
                    $entry['systems'][$systemId] = isset($entry['systems'][$systemId]) ? array_merge($entry['systems'][$systemId], $system) : $system;
                }
            }
            $entries[] = $entry;
        }

        $e = EWDecider::$last;
        $p = PowerDecider::$last;
        $b = BallisticDecider::$last;
        self::$lastSummary = 'EW: ' . $e['offensive'] . ' of ' . $e['points'] . ' points offensive, on ' . $e['targets'] . ' enemies (' . $e['ships'] . ' ships)'
            . ($p['off'] > 0 ? '; power: ' . $p['off'] . ' systems switched off' : '')
            . ($p['short'] > 0 ? ', ' . $p['short'] . ' ships still short' : '')
            . ($p['scanner'] + $p['engine'] > 0 ? '; boosts: scanner +' . $p['scanner'] . ', engine +' . $p['engine'] . ' (' . $p['idle'] . ' idle guns switched off)' : '')
            . ($b['launches'] > 0 ? '; ballistics: ' . $b['launches'] . ' launchers at ' . $b['targets'] . ' enemies' : '');

        return $entries;
    }

    /* Does this bot owe the current phase a submission? The same tests the game itself applies to
       a human: in Movement, holding the activation; in every other phase, a slot that has not
       caught up with the game (TacGamedata::hasAlreadySubmitted). A surrendered seat owes nothing. */
    public static function owes(TacGamedata $gamedata, $botId)
    {
        if ($gamedata->status !== 'ACTIVE') return false;

        $slots = $gamedata->getSlotsByPlayerId($botId);
        if (count($slots) == 0) return false;
        foreach ($slots as $slot) {
            if ($slot->surrendered !== null) return false;
        }

        switch ((int)$gamedata->phase) {
            case 2:
                return count($gamedata->getMyActiveShips()) > 0;
            case -1: case 1: case 5: case 3:
                return !$gamedata->hasAlreadySubmitted($botId);
            default:
                return false;
        }
    }

    /* Submit, and say what went wrong: null when accepted, otherwise the server's error text.
       Manager's own catch covers an Exception. An Error (a null dereference inside process(),
       say) is not caught there and would leave the transaction open and the bot's submit lock
       held for 15 minutes, so it is closed down here - a bot's failure must never take the
       request it rides in down with it. */
    private static function submit($gameid, $botId, $turn, $phase, $activeship, array $ships, $status)
    {
        try {
            $reply = Manager::submitShipsForPlayer($gameid, $botId, $turn, $phase, $activeship, $ships, $status, 0);
        } catch (Throwable $e) {
            $db = Manager::getDBManager();
            $db->endTransaction(true);
            $db->releasePlayerSubmitLock($gameid, $botId);
            if (class_exists('DiscordNotifier')) DiscordNotifier::clear();
            Debug::error($e);
            return get_class($e) . ': ' . $e->getMessage();
        }

        $decoded = json_decode((string)$reply, true);
        if (is_array($decoded) && isset($decoded['error'])) return (string)$decoded['error'];
        if (!is_array($decoded)) return 'Unreadable reply: ' . substr((string)$reply, 0, 300);

        return null;
    }

    private static function result($status, $error = null)
    {
        return array('status' => $status, 'error' => $error);
    }
}
