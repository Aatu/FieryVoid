<?php
/**
 * CPU opponent - turns decided orders into a submission (TACTICAL_AI_PLAN.md section 3.2 step 4).
 *
 * A bot's orders are written as THE SAME PLAIN ARRAY A BROWSER POSTS (ajaxInterface.construcGamedata)
 * and built into ships by the same code that builds a browser's (ShipPayload). That is deliberate:
 * every Phase::process() is written for POST-side ships and reads the unit's real state from the
 * loaded gamedata itself. Handing process() the LOADED ship objects instead would be wrong - for
 * one, Initial Orders stores every power entry a submitted system carries, so a loaded ship would
 * have its stored rows written a second time.
 *
 * So the rule for the bot is the browser's rule: an entry carries only THIS turn's new orders.
 */
class AIOrderBuilder
{
    /* The order-less entry for one of the bot's units: what a browser sends for a unit it gave no
       orders. $ship is the bot's own unit as loaded from the database. */
    public static function entry($ship)
    {
        $entry = array(
            'phpclass' => $ship->phpclass,
            'userid' => $ship->userid,
            'team' => $ship->team,
            'slot' => $ship->slot,
            'id' => $ship->id,
            'name' => $ship->name,
            'pointCostEnh' => 0,
            'pointCostEnh2' => 0,
            'movement' => array(),
            'EW' => array(),
            'systems' => array(),
            'enhancementOptions' => array()
        );

        //A flight's fighters exist only once it knows its size (ShipPayload calls populate()).
        if ($ship instanceof FighterFlight) $entry['flightSize'] = $ship->flightSize;

        return $entry;
    }

    //One movement row, in the shape movement.js posts it.
    public static function move($type, OffsetCoordinate $position, $speed, $heading, $facing, $turn, $atInitiative, $value = 0)
    {
        return array(
            'id' => -1,
            'type' => $type,
            'position' => array('q' => (int)$position->q, 'r' => (int)$position->r),
            'xOffset' => 0,
            'yOffset' => 0,
            'speed' => (int)$speed,
            'heading' => (int)$heading,
            'facing' => (int)$facing,
            'preturn' => false,
            'turn' => (int)$turn,
            'value' => $value,
            'at_initiative' => $atInitiative,
            'requiredThrust' => array(null, null, null, null, null),
            'assignedThrust' => array(),
            'forced' => false
        );
    }

    //One EW row. $targetid -1 = no target (DEW), as ew.js posts it.
    public static function ew($shipid, $turn, $type, $amount, $targetid = -1)
    {
        return array(
            'shipid' => (int)$shipid,
            'turn' => (int)$turn,
            'type' => $type,
            'amount' => (int)$amount,
            'targetid' => (int)$targetid
        );
    }

    /* One power row, in the shape power.js writes it. $type: 1 = switched off, 2 = boost,
       3 = overload. It goes on its system's entry: $entry['systems'][$systemid]['power'][]. */
    public static function power($shipid, $systemid, $turn, $type, $amount = 0)
    {
        return array(
            'id' => null,
            'shipid' => (int)$shipid,
            'systemid' => (int)$systemid,
            'type' => (int)$type,
            'turn' => (int)$turn,
            'amount' => (int)$amount
        );
    }

    /* One fire order, in the shape weaponManager.targetShip builds it. A weapon with several guns
       posts one of these per gun. The page sends the strings "null" for the hex of a shot aimed
       at a unit, and so does this. */
    public static function fire($shooterid, $targetid, $weaponid, $turn, $firingMode, $shots, $damageclass, $type = 'normal', $notes = null)
    {
        //$notes: what the page writes on the order beside the rest - 'Split' on a split-shot weapon's split orders (stage T9, E1), else none
        $order = array(
            'id' => -1,
            'type' => $type,
            'shooterid' => (int)$shooterid,
            'targetid' => (int)$targetid,
            'weaponid' => (int)$weaponid,
            'calledid' => -1,
            'turn' => (int)$turn,
            'firingMode' => (int)$firingMode,
            'shots' => (int)$shots,
            'x' => 'null',
            'y' => 'null',
            'damageclass' => $damageclass
        );
        if ($notes !== null) $order['notes'] = $notes;
        return $order;
    }

    /* A jump point declared with a Jump Engine (stage T6c), as weaponManager.createJumpPointOrder builds it: a ballistic order at
       no unit, the hex in x / y, and the FACING of the vortex's mouth in the firing mode (mode = facing + 1). */
    public static function jumpPoint($shooterid, $weaponid, $turn, $facing, OffsetCoordinate $hex, $shots, $damageclass)
    {
        $order = self::fire($shooterid, -1, $weaponid, $turn, (int)$facing + 1, $shots, $damageclass, 'ballistic');
        $order['x'] = (int)$hex->q;
        $order['y'] = (int)$hex->r;
        return $order;
    }

    //The entries -> the ship array a Phase::process() takes.
    public static function build(array $entries)
    {
        return ShipPayload::buildShips($entries);
    }

    /* A unit's place in the initiative order, as the client stamps it on each movement row
       (shipManager.getIniativeOrder): 1 for the lowest initiative on the board, units of equal
       initiative sharing a number. 0 when the unit is not in the order at all. */
    public static function initiativeOrder($ship, TacGamedata $gamedata)
    {
        $units = array();
        foreach ($gamedata->ships as $unit) {
            if ($unit->isDestroyed() || $unit->isTerrain() || !empty($unit->mine)) continue;
            if ($unit->getTurnDeployed($gamedata) > $gamedata->turn) continue;
            $units[] = $unit;
        }

        usort($units, function ($a, $b) {
            if ($a->iniative > $b->iniative) return 1;
            if ($a->iniative < $b->iniative) return -1;
            return $a->id - $b->id;
        });

        $order = 0;
        $previous = -100000;
        foreach ($units as $unit) {
            if ($unit->iniative > $previous) {
                $order++;
                $previous = $unit->iniative;
            }
            if ($unit->id === $ship->id) return $order;
        }

        return 0;
    }
}
