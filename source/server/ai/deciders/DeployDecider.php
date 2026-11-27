<?php
/**
 * CPU opponent - Deployment (TACTICAL_AI_PLAN.md section 5.2, "Caller 1").
 *
 * The bot deploys with the same service the player's Auto-Deploy button uses (FleetDeployer):
 * battlegroups in formation, facing the enemy. What the formation does not place - bases, OSATs,
 * mines, a unit it found no room for - keeps the fallback's hex (AIFallback::deployment).
 *
 * How many battlegroups is a matter of personality:
 *   cautious                 1
 *   balanced                 1 per 2 capital ships
 *   aggressive, skirmisher   1 per capital ship
 * (Only `balanced` exists before stage T8; the others are read so a seat set later needs no change.)
 */
class DeployDecider
{
    //The bot's Deployment entries, one per unit it owns. $seat is its DBManager::getCpuSeats row.
    public static function orders(TacGamedata $gamedata, $botId, array $seat)
    {
        $personality = isset($seat['cpu']['personality']) ? $seat['cpu']['personality'] : AIConfig::DEFAULT_PERSONALITY;
        $seed = isset($seat['cpu']['seed']) ? (int)$seat['cpu']['seed'] : 0;
        $view = new AIView($gamedata, $botId, AIConfig::seatLevel($seat), $seed);

        /* Stage T9, M2: flights that must start in a hangar (the Orieni Hunter-Killers) go into the carriers, spread over them
           (HangarDecider) - not into the formation. One with no room anywhere is placed with the rest. */
        $docked = HangarDecider::dockPlan($gamedata, $botId);

        $planned = array();
        foreach ($gamedata->getSlotsByPlayerId($botId) as $slot) {
            $unitIds = array();
            foreach (FleetDeployer::eligibleUnits($gamedata, $botId, $slot->slot) as $unit) {
                if (!isset($docked[(int)$unit->id])) $unitIds[] = (int)$unit->id;
            }
            $anchors = FleetDeployer::maxGroups($gamedata, $botId, $slot->slot, $unitIds);

            if ($personality === 'cautious') $groups = 1;
            else if ($personality === 'aggressive' || $personality === 'skirmisher') $groups = $anchors;
            else $groups = (int)ceil($anchors / 2);

            $options = array('groups' => $groups, 'seed' => $seed, 'unitIds' => $unitIds);
            //stage T9: spread out against an enemy carrying an area-effect weapon (the skill 'aoeSpread' - Normal and Hard)
            if ($view->skill('aoeSpread') && self::aoeThreat($gamedata, $slot->team)) $options['spread'] = AIConfig::AOE_SPACING;
            $plan = (count($unitIds) > 0) ? FleetDeployer::plan($gamedata, $botId, $slot->slot, $options) : array();
            foreach ($plan as $place) $planned[$place['shipid']] = $place;
        }

        return HangarDecider::dock(AIFallback::deployment($gamedata, $botId, $planned), $docked);
    }

    /* Stage T9 (the user, 2026-11-23: "Normal and Hard CPU should recognise when energy mines (in a way that can be extended later to
       cover other AoE weapons) are present and deploy in a more spread out way"): does a unit of another team carry a weapon of
       AIConfig::AOE_WEAPONS - a ship's, or a fighter's? A fleet's make-up is on every player's screen (its ships and their systems), so
       no mask is broken by asking. */
    public static function aoeThreat(TacGamedata $gamedata, $team)
    {
        foreach ($gamedata->ships as $unit) {
            if ($unit->team == $team || $unit->isDestroyed() || $unit->isTerrain()) continue;
            $systems = array();
            foreach ($unit->systems as $system) {
                if ($system instanceof Fighter) foreach ($system->systems as $sub) $systems[] = $sub;
                else $systems[] = $system;
            }
            foreach ($systems as $system) {
                if (!($system instanceof Weapon) || $system->isDestroyed()) continue;
                foreach (AIConfig::AOE_WEAPONS as $class) {
                    if ($system instanceof $class) return true;
                }
            }
        }
        return false;
    }
}
