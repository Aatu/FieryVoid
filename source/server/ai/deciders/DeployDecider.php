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

        $planned = array();
        foreach ($gamedata->getSlotsByPlayerId($botId) as $slot) {
            $anchors = FleetDeployer::maxGroups($gamedata, $botId, $slot->slot);

            if ($personality === 'cautious') $groups = 1;
            else if ($personality === 'aggressive' || $personality === 'skirmisher') $groups = $anchors;
            else $groups = (int)ceil($anchors / 2);

            $plan = FleetDeployer::plan($gamedata, $botId, $slot->slot, array('groups' => $groups, 'seed' => $seed));
            foreach ($plan as $place) $planned[$place['shipid']] = $place;
        }

        return AIFallback::deployment($gamedata, $botId, $planned);
    }
}
