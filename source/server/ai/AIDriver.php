<?php
/**
 * CPU opponent - the driver (TACTICAL_AI_PLAN.md section 4). The ONLY entry point the rest of
 * Fiery Void calls.
 *
 * There is no daemon. The driver runs inside an ordinary request - a human's submit, or a human's
 * poll - with a time budget, lets each bot that owes the game a submission make it, advances the
 * game exactly as the human path does, and carries on at the next request if it ran out of time.
 *
 * INERT WHEN UNUSED (principle P6):
 *   - feature off: run() returns on its first line, before any query;
 *   - feature on, game without a CPU seat: one APCu read (or one indexed query) and out.
 */
class AIDriver
{
    /* Let the bots of game $gameid play until none owes anything, the budget runs out, or the
       game stops. Returns the number of submissions accepted. Never throws: a bot's failure is
       logged and the request that carried it goes on. */
    public static function run($gameid, $budgetSeconds = null)
    {
        if (!Features::reachable(AIConfig::FLAG)) return 0;

        $gameid = (int)$gameid;
        if ($gameid <= 0) return 0;

        try {
            if (!AISeats::gameHasCpu($gameid)) return 0;

            $db = Manager::getDBManager();
            if (!$db->aiTryLock($gameid)) return 0; //another request is driving this game right now
        } catch (Throwable $e) {
            Debug::error($e);
            return 0;
        }

        $deadline = microtime(true) + ($budgetSeconds === null ? AIConfig::BUDGET_SECONDS : (float)$budgetSeconds);
        $submitted = 0;
        $pending = false;

        try {
            $idle = array();     //bots that had nothing to do at the current point of the game
            $lastPoint = null;

            for ($i = 0; $i < AIConfig::MAX_SUBMISSIONS_PER_RUN; $i++) {
                $game = $db->getGameRowLight($gameid);
                if (!$game || ($game['status'] !== 'ACTIVE' && $game['status'] !== 'LOBBY')) break;

                $point = self::pointOf($game);
                if ($point !== $lastPoint) {
                    $idle = array();
                    $lastPoint = $point;
                }

                $seats = $db->getCpuSeats($gameid);
                if (count($seats) == 0) break;

                $seat = ($game['status'] === 'ACTIVE') ? self::nextOwing($db, $game, $seats, $idle) : null;

                if ($seat === null) {
                    /* Nobody to play. The phase may still be ready to advance - a bot may have
                       been the last to submit, or the last fleet of the lobby may just have been
                       committed - and no human poll need be on its way. Advance as a poll would,
                       and look again only if that moved the game. */
                    $first = reset($seats);
                    self::advance($db, $first['playerid'], $gameid);
                    $after = $db->getGameRowLight($gameid);
                    if (!$after || self::pointOf($after) === $point) break;
                    continue;
                }

                if (microtime(true) >= $deadline) {
                    $pending = true;
                    break;
                }

                $result = TacticalAI::play($gameid, $seat);

                if ($result['status'] === 'idle') {
                    $idle[$seat['playerid']] = true;
                    continue;
                }
                if ($result['status'] === 'submitted') $submitted++;

                self::advance($db, $seat['playerid'], $gameid);
            }
        } catch (Throwable $e) {
            Debug::error($e);
        } finally {
            try {
                $db->aiUnlock($gameid);
            } catch (Throwable $e) {
                Debug::error($e);
            }
            self::setPending($gameid, $pending);
        }

        return $submitted;
    }

    /* Is a bot of this game still waiting to play, left over from a run that ran out of time?
       gamedata.php asks before its fast-poll exit: that exit answers "nothing new" without ever
       reaching Manager, and a waiting human's polls are what the unfinished run resumes on. */
    public static function isPending($gameid)
    {
        if (!Features::reachable(AIConfig::FLAG)) return false;
        if (!function_exists('apcu_fetch')) return false;

        return apcu_fetch(self::pendingKey($gameid)) !== false;
    }

    private static function setPending($gameid, $pending)
    {
        if (!function_exists('apcu_store')) return;

        if ($pending) apcu_store(self::pendingKey($gameid), 1, AIConfig::PENDING_TTL);
        else apcu_delete(self::pendingKey($gameid));
    }

    private static function pendingKey($gameid)
    {
        return Manager::getCachePrefix() . 'game_' . (int)$gameid . '_ai_pending';
    }

    //Where the game stands: a change in any of these means there may be something new to do.
    private static function pointOf(array $game)
    {
        return $game['status'] . ':' . $game['turn'] . ':' . $game['phase'] . ':' . $game['activeshipRaw'];
    }

    /* The first CPU seat that looks as if it owes the current phase a submission, from the game
       row and the slot rows alone - no ships loaded. TacticalAI::owes() is the authority, on the
       loaded game; a seat it turns away lands in $idle and is not offered again until the game
       moves. The tests are the ones TacGamedata::hasAlreadySubmitted and getMyActiveShips make. */
    private static function nextOwing($db, array $game, array $seats, array $idle)
    {
        $phase = $game['phase'];
        $owners = ($phase == 2) ? array_flip($db->getShipOwners($game['activeship'])) : array();

        foreach ($seats as $seat) {
            if ($seat['surrendered'] !== null) continue;
            if (isset($idle[$seat['playerid']])) continue;

            if ($phase == 2) {
                if (isset($owners[$seat['playerid']])) return $seat;
                continue;
            }

            if ($phase != -1 && $phase != 1 && $phase != 5 && $phase != 3) continue;

            if ($seat['lastturn'] < $game['turn'] || $seat['lastphase'] < $phase || ($seat['lastphase'] == 5 && $phase == 3)) {
                return $seat;
            }
        }

        return null;
    }

    /* Manager::advanceGameState, as the human path calls it after a submit. It catches an
       Exception itself and rethrows; an Error would leave its transaction open and the game's
       submit lock held, so both are closed here before the failure is passed up. */
    private static function advance($db, $playerid, $gameid)
    {
        try {
            Manager::advanceGameState($playerid, $gameid);
        } catch (Error $e) {
            $db->endTransaction(true);
            $db->releaseGameSubmitLock($gameid);
            if (class_exists('DiscordNotifier')) DiscordNotifier::clear();
            throw $e;
        }
    }
}
