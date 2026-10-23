<?php
/* FIGHTER MISSIONS (FIGHTER_MISSIONS_PLAN.md, repo root) - Kitchen Sink §6.12 for fighter flights.
 *
 * A flight flies one of five missions. The pick is free at every arrival on the map (R2); a change later costs
 * -60 Ini on the change turn, which then has no mission effects at all, and the new mission works from the next
 * turn (D2). Picked in Initial Orders only (D11), from the flight's tooltip.
 *
 * STORAGE (§3.1): one 'FtrMission' IndividualNote per change, value "MISSION:keyShipId:free", written by the server
 * only (saveClaim, after validation on the RELOADED load). `free` is decided ONCE, when the note is written - nobody
 * ever re-derives a past turn's arrival. DBManager's note sweep hands every note here first (takeNote): it is recorded
 * on the flight and never reaches a craft.
 *
 * ⭐ THE ONE RULE (§3.2) is missionOn($flight, $turn), and every effect asks it FOR THE TURN THE SHOT OR ORDER BELONGS
 * TO, never the load turn (D14) - so a replay or the printed log shows the mission that was in force then.
 * ⚠️ MIRROR PAIR with fighterMissions.missionOn (client/fighterMissions.js), which also counts the owner's pending pick.
 *
 * Every per-shot line elsewhere opens with TacGamedata::$fighterMissionsPresent (set by recordFighterMission, reset at
 * the top of the note sweep), so a game with no mission note pays one static read per shot.
 *
 * DEFENSIVE INTERCEPT REPLACES ESCORTING (user, 2026-10-22): the old fighter-escort rule (same hex now AND at the end
 * of last turn) is commented out in Firing::isLegalIntercept and weaponManager.canInterceptBallistic; a flight now
 * intercepts ballistics aimed at a friendly ship only while on this mission and in that ship's hex when fire resolves.
 */
class FighterMissions
{
	const NOTE = 'FtrMission';
	const CHANGE_PENALTY = -60; //Ini on the change turn (tabletop -12)
	const CSP_INI = 5;          //Combat Space Patrol: as an Expert Dogfighter (+1 tabletop), wins ties
	const CSP_RANGE = 10;       //hexes from the key ship
	const OB_PENALTY = 3;       //-15 shown
	const OB_FLOOR = 1;         //"minimum 5" shown - a penalty never takes OB below 1 point
	const ASA_OB = 1;           //Anti-Ship +5 shown, non-ballistic shots at ships
	const SCOUT_PROFILE = -1;   //Scout Penetration: both profiles -5%
	const SCOUT_JINK = 1;       //...and one extra jinking level (client only, D10)

	/* id => label, summary. ⚠️ MIRROR of fighterMissions.REGISTRY (JS), same order. Ids are never numeric strings
	   (JSON_NUMERIC_CHECK would rewrite them). */
	public static $registry = array(
		'NONE'   => array('label' => 'No Mission', 'summary' => 'No effects'),
		'CSP'    => array('label' => 'Combat Space Patrol', 'summary' => '+5 Ini within 10 hexes of carrier; -15 OB Vs. ships'),
		'ASA'    => array('label' => 'Anti-Ship', 'summary' => '+5 to hit ships (not with ballistics); -15 OB Vs. fighters'),
		'SCOUT'  => array('label' => 'Scout Penetration', 'summary' => 'Defence profiles -5, one extra jinking level; -15 OB'),
		'DEFINT' => array('label' => 'Defensive Intercept', 'summary' => 'Intercepts ballistics aimed at a ship in same hex; -15 OB'),
	);

	/* §4 / R4 - hangar categories (lowercased) that fly no mission: no pilots, or not a fighter. Excluding another
	   category is one line here. Stage 0 found the two drone categories ('system drone', 'stilettos'); every
	   shuttle category is caught by the "shuttle" substring test in isEligible. */
	public static $excludedHangarTypes = array('breaching pods', 'lifeboats', 'yacht', 'flyers', 'hunter-killers',
		'mapmaker probes', 'weapon platforms', 'system drone', 'stilettos');

	/* May this flight fly a mission at all? Static facts of the hull only, so the answer never changes in a game.
	   (R4 + the user's 2026-10-22 additions: drones, and every Marines-carrying craft.)
	   ⚠️ No client mirror: an eligible flight always carries `fighterMission` in its payload (addForJson). */
	public static function isEligible($flight){
		if (!($flight instanceof FighterFlight)) return false;
		if ($flight instanceof Shuttle) return false;                           //default shuttles, Flyers, minesweeping shuttles
		if (!empty($flight->remoteControl) || !empty($flight->osat) || !empty($flight->mine)) return false; //HKs, MicroSATs, mines
		$category = strtolower(trim((string)HangarOps::trueSizeOf($flight)));
		if (strpos($category, 'shuttle') !== false) return false;               //armed, assault, cargo, medical, Shuttlecraft...
		if (in_array($category, self::$excludedHangarTypes, true)) return false;
		if (stripos((string)$flight->shipClass, 'breaching pod') !== false) return false; //pods riding shuttle hangars
		/* ANY craft carrying Marines (user, 2026-10-22): breaching pods under every name, and the assault craft that board too -
		   the Dalithorn Assault Pinnace and Customs Vessel, the Nausicaan Gliders. Marines are a hull system (Marines /
		   ExtraMarines on the craft), never added by an enhancement, so this stays a fixed fact of the class. */
		foreach ($flight->systems as $craft){
			foreach ($craft->systems as $system){
				if ($system instanceof Marines) return false;
			}
			break; //every craft of a flight is the same
		}
		return true;
	}

	public static function getLabel($id){
		return isset(self::$registry[$id]) ? self::$registry[$id]['label'] : (string)$id;
	}

	/* ---------------------------------------------------------------- history */

	/* DBManager's bulk note loop: a FtrMission note is recorded on the flight and never handed to a craft. true = taken
	   (a note on anything else is swallowed too - it was never written by saveClaim). */
	public static function takeNote($ship, $note){
		if ($note->notekey !== self::NOTE) return false;
		if (!self::isEligible($ship)) return true;
		$parts = explode(':', (string)$note->notevalue);
		$mission = isset($parts[0]) ? (string)$parts[0] : '';
		if (!isset(self::$registry[$mission])) return true;
		$ship->recordFighterMission((int)$note->turn, $mission, isset($parts[1]) ? (int)$parts[1] : 0, !empty($parts[2]));
		return true;
	}

	//The flight's entry dated $turn exactly, or null. At most one per turn (saveClaim refuses a second).
	public static function entryOn($flight, $turn){
		foreach ($flight->getFighterMissions() as $entry){
			if ($entry['t'] === (int)$turn) return $entry;
		}
		return null;
	}

	//The latest entry dated <= $turn ($strict: < $turn), or null.
	private static function latest($flight, $turn, $strict = false){
		$best = null;
		foreach ($flight->getFighterMissions() as $entry){
			if ($strict ? ($entry['t'] >= (int)$turn) : ($entry['t'] > (int)$turn)) continue;
			if ($best === null || $entry['t'] >= $best['t']) $best = $entry;
		}
		return $best;
	}

	/* ⭐ THE MISSION IN FORCE ON TURN $turn (§3.2):
	     no entry                  -> NONE
	     entry dated $turn, !free  -> CHANGING: no mission effects, -60 Ini, the new mission from $turn + 1
	     otherwise                 -> that entry's mission (and key ship, for CSP)
	   Returns array('m' => id, 'k' => key ship id or 0, 'changing' => bool, 'to' => the mission being changed to). */
	public static function missionOn($flight, $turn){
		$e = ($flight instanceof FighterFlight) ? self::latest($flight, $turn) : null;
		if ($e === null) return array('m' => 'NONE', 'k' => 0, 'changing' => false, 'to' => null);
		if ($e['t'] === (int)$turn && !$e['f']) return array('m' => 'NONE', 'k' => 0, 'changing' => true, 'to' => $e['m']);
		return array('m' => $e['m'], 'k' => $e['k'], 'changing' => false, 'to' => null);
	}

	//What the flight had SELECTED before $turn - the yardstick for "is this pick a change?": array('m', 'k')
	public static function selectedBefore($flight, $turn){
		$e = self::latest($flight, $turn, true);
		return ($e === null) ? array('m' => 'NONE', 'k' => 0) : array('m' => $e['m'], 'k' => $e['k']);
	}

	/* §3.3 - the flight's latest arrival on the map: deployment, a late slot or a reinforcement (getTurnDeployed), or
	   the Initial Orders after its latest hangar launch or escape (a launch resolves at the end of its turn). The pick
	   is free on exactly this turn. Stage 0 proved every arrival path stamps one of the two. */
	public static function arrivalTurn($flight, $gamedata){
		$turn = (int)$flight->getTurnDeployed($gamedata);
		if ($flight->hangarLaunchTurn !== null) $turn = max($turn, (int)$flight->hangarLaunchTurn + 1);
		return $turn;
	}

	//On the board on $turn: deployed, not in hyperspace, not docked (isDestroyed covers $removed), not destroyed.
	public static function isOnBoard($unit, $gamedata, $turn){
		if (!$unit || $unit->isDestroyed()) return false;
		return (int)$unit->getTurnDeployed($gamedata) <= (int)$turn;
	}

	/* D7 - a CSP key ship: any unit of the flight's team on the board that is not a flight, terrain or a mine. */
	public static function keyShipValid($flight, $key, $gamedata, $turn){
		if (!$key || $key instanceof FighterFlight || $key->isTerrain() || !empty($key->mine)) return false;
		if ($key->team != $flight->team) return false;
		return self::isOnBoard($key, $gamedata, $turn);
	}

	//R3 / D3 - within 10 hexes of its key ship, both where they stand now; false while the key ship is not valid.
	public static function inZone($flight, $keyId, $gamedata, $turn){
		$key = self::findShipLoosely($gamedata, $keyId);
		if (!self::keyShipValid($flight, $key, $gamedata, $turn)) return false;
		return mathlib::getDistanceHex($flight, $key) <= self::CSP_RANGE;
	}

	//TacGamedata::getShipById compares with === and a note or JSON id is not an int on every path
	private static function findShipLoosely($gamedata, $shipId){
		if (!$shipId) return null;
		foreach ($gamedata->ships as $ship){
			if ($ship->id == $shipId) return $ship;
		}
		return null;
	}

	/* ---------------------------------------------------------------- the claim (§3.7) */

	/* InitialOrdersGamePhase::process: what the POST asked for ($claim = array('m' => id, 'k' => key ship id), raw),
	   checked against the RELOADED flight - the POST-side copy has no notes (arch_post_side_ship_reconstruction) - and
	   written as one note when it is a real change. true = written. */
	public static function saveClaim($flight, $claim, $gamedata){
		if (!($flight instanceof FighterFlight) || !is_array($claim)) return false;
		$turn = (int)$gamedata->turn;
		if ($flight->userid != $gamedata->forPlayer) return false;
		if (!self::isEligible($flight) || !self::isOnBoard($flight, $gamedata, $turn)) return false;
		if (self::entryOn($flight, $turn) !== null) return false; //one pick a turn

		$mission = strtoupper(trim((string)($claim['m'] ?? '')));
		if (!isset(self::$registry[$mission])) return false;
		$key = 0;
		if ($mission === 'CSP'){
			$ship = self::findShipLoosely($gamedata, $claim['k'] ?? 0);
			if (!self::keyShipValid($flight, $ship, $gamedata, $turn)) return false;
			$key = (int)$ship->id;
		}

		$selected = self::selectedBefore($flight, $turn);
		if ($selected['m'] === $mission && $selected['k'] === $key) return false; //no change - nothing to write

		$free = ($turn === self::arrivalTurn($flight, $gamedata));
		Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, $turn, 1, $flight->id,
			$flight->getSampleFighter()->id, self::NOTE, substr('Fighter mission: ' . self::getLabel($mission), 0, 40),
			$mission . ':' . $key . ':' . ($free ? 1 : 0)));
		$flight->recordFighterMission($turn, $mission, $key, $free);
		return true;
	}

	/* ---------------------------------------------------------------- initiative (§3.6) */

	/* ⭐ WHEN INITIAL ORDERS CLOSE - InitialOrdersGamePhase::advance, right after the Triad Command Node and before the
	   first active ship is picked. A flight changing its mission this turn takes -60 (and loses ties); a CSP flight
	   within 10 hexes of its key ship takes +5 (and wins ties). Shares the Triad's shiftIniative / tie-breaker, so
	   classic and simultaneous movement behave as a Command Node does. $gamedata->turn is the Initial Orders turn
	   (advance has not moved it); never branch on the phase here (arch_individual_notes_and_phase_hooks). */
	public static function applyInitiative(TacGamedata $gamedata, DBManager $dbManager){
		if (!TacGamedata::$fighterMissionsPresent) return;
		$turn = (int)$gamedata->turn;
		$rule = $gamedata->rules ? $gamedata->rules->getRuleByName('initiativeCategories') : null;
		$up = array();
		$down = array();

		foreach ($gamedata->ships as $ship){
			if (!($ship instanceof FighterFlight) || empty($ship->getFighterMissions())) continue;
			if (!self::isOnBoard($ship, $gamedata, $turn)) continue;
			$state = self::missionOn($ship, $turn);
			if ($state['changing']){
				CnC::shiftIniative($ship, self::CHANGE_PENALTY, $rule);
				$down[$ship->id] = $ship;
			} else if ($state['m'] === 'CSP' && self::inZone($ship, $state['k'], $gamedata, $turn)){
				CnC::shiftIniative($ship, self::CSP_INI, $rule);
				$up[$ship->id] = $ship;
			}
		}

		if (empty($up) && empty($down)) return;

		if (!$rule){ //D4 - classic movement keeps every total distinct: a -60 loses its ties, CSP wins its
			CnC::breakCommandNodeTies($gamedata, $down, -1);
			CnC::breakCommandNodeTies($gamedata, $up, 1);
		}

		foreach ($down + $up as $ship){
			$dbManager->updateIniative($gamedata->id, $turn, $ship->id, $ship->iniative, $ship->unmodifiedIniative);
		}
		$gamedata->doSortShips();
	}

	/* ---------------------------------------------------------------- combat effects (§3.5) */

	//"Minimum 5" - never below 1 point, and never raising an OB that is already below 1
	private static function penalised($ob){
		return max(min($ob, self::OB_FLOOR), $ob - self::OB_PENALTY);
	}

	/* The shooter's offensive bonus for this shot, BEFORE the tmpsensordown / Detect Mines deductions (D5). "Ship" = any
	   unit that is not a flight. Asked behind TacGamedata::$fighterMissionsPresent from Weapon::calculateHitBase.
	   ⚠️ MIRROR PAIR with fighterMissions.adjustOffensiveBonus, in weaponManager.computeOEW. */
	public static function adjustOffensiveBonus($shooter, $target, $weapon, $turn, $ob){
		$state = self::missionOn($shooter, $turn);
		$atFlight = ($target instanceof FighterFlight);
		switch ($state['m']){
			case 'CSP':
				return $atFlight ? $ob : self::penalised($ob);
			case 'ASA':
				if ($atFlight) return self::penalised($ob);
				return ($weapon && !empty($weapon->ballistic)) ? $ob : $ob + self::ASA_OB;
			case 'SCOUT':
			case 'DEFINT':
				return self::penalised($ob);
		}
		return $ob; //NONE, or changing
	}

	//Scout Penetration: both of the TARGET flight's profiles -1. ⚠️ MIRROR PAIR with fighterMissions.profileModifier.
	public static function profileModifier($target, $turn){
		if (!($target instanceof FighterFlight)) return 0;
		$state = self::missionOn($target, $turn);
		return ($state['m'] === 'SCOUT') ? self::SCOUT_PROFILE : 0;
	}

	/* Defensive Intercept - the flight may intercept ballistics aimed at a friendly ship in its hex (Firing::
	   isLegalIntercept). ⚠️ MIRROR PAIR with fighterMissions.isDefensiveIntercept, in canInterceptBallistic. */
	public static function isDefensiveIntercept($flight, $turn){
		if (!TacGamedata::$fighterMissionsPresent || !($flight instanceof FighterFlight)) return false;
		$state = self::missionOn($flight, $turn);
		return $state['m'] === 'DEFINT';
	}

	/* ---------------------------------------------------------------- payload and masking (§3.8) */

	/* FighterFlight::stripForJson. An ELIGIBLE flight always carries fighterMission: {h: [{t, m, k, f}]}, possibly
	   empty, and an ineligible one never does - so isEligible needs no client mirror. Missions are always known
	   (KS 6.12); the CSP key ship is own team only (D8) - publishing it would point at a shaded or cloaked ship. */
	public static function addForJson($flight, $strippedShip){
		if (!self::isEligible($flight)) return;
		$showKey = $flight->isRevealedToCurrentViewer() || TacGamedata::$currentGameFinished;
		$list = array();
		foreach ($flight->getFighterMissions() as $entry){
			$row = array('t' => $entry['t'], 'm' => $entry['m'], 'f' => $entry['f'] ? 1 : 0);
			if ($showKey && $entry['k'] > 0) $row['k'] = $entry['k'];
			$list[] = $row;
		}
		$strippedShip->fighterMission = (object)array('h' => $list);
	}

	/* TacGamedata::stripForJson (user, 2026-10-22: opponents see the CSP +5 too, "to prevent any confusion"). Where the key
	   ship is withheld (D8) the client cannot measure the zone, so the server says it: fighterMission.z = 1 when this turn's
	   Combat Space Patrol is within 10 hexes of its key ship where both stood at the START of the turn - as applyInitiative
	   measured it when Initial Orders closed. Reads the history as this viewer has it: an opponent's pick made in the Initial
	   Orders still open is already masked. ⚠️ MIRROR of fighterMissions.iniativeModifier, which measures it itself when it has
	   the key ship. */
	public static function addZoneForJson($flight, $strippedShip, $gamedata){
		if (!isset($strippedShip->fighterMission)) return; //ineligible
		if ($flight->isRevealedToCurrentViewer() || TacGamedata::$currentGameFinished) return; //the key ship is in the payload
		$turn = (int)$gamedata->turn;
		$state = self::missionOn($flight, $turn);
		if ($state['m'] !== 'CSP' || !self::isOnBoard($flight, $gamedata, $turn)) return;
		$key = self::findShipLoosely($gamedata, $state['k']);
		if (!self::keyShipValid($flight, $key, $gamedata, $turn)) return;
		if (mathlib::getDistanceHex(self::startOfTurnHex($flight, $turn), self::startOfTurnHex($key, $turn)) <= self::CSP_RANGE){
			$strippedShip->fighterMission->z = 1;
		}
	}

	//where a unit stood when this turn's Initial Orders closed: its last move of last turn, or its deployment
	private static function startOfTurnHex($unit, $turn){
		$move = $unit->getLastTurnMovement($turn);
		return $move ? $move->position : $unit->getHexPos();
	}

	/* TacGamedata::deleteHiddenData, phase 1, for a unit that is not the viewer's (the EW rule's own test): a pick made
	   in the Initial Orders still open reaches opponents only once the phase closes, as EW does. Live path only - a
	   replay of a past turn shows everything, correctly. Edits the load being built for this viewer, never resolved. */
	public static function hideCurrentTurnChoice($flight, $turn){
		if (!($flight instanceof FighterFlight)) return;
		$flight->dropFighterMissionsOn((int)$turn);
	}
}
