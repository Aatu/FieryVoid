<?php
/* ================================================================================================
   KIRISHIAC ELINT SENSOR MODULES - THE ARC RULE (KIRISHIAC_ORBITAL_REFITS_PLAN.md §8)

   A Mastership refitted with ELINT Sensor Modules (KirishiacElintModule) is an ELINT ship, but only
   toward units inside a live module's arc, and its EW comes from TWO pools (rulings R7, R8):

     N  the scanner      OEW, DEW, CCEW, Detect Mines - never an ELINT function
     M  the modules      OEW and the six ELINT functions, only toward units in that module's arc

   EW is allocated in Initial Orders with OEW paid from the scanner first, module points carrying only
   in-arc OEW the scanner has no room for (the client, ew.getElintModuleSplit - plan D15: OEW is a
   normal function, and scanner-paid OEW survives the target leaving the arc). Nothing here depends on
   that split - Navail below is read off the committed DEW row. Validity is judged ONCE, at the end of Movement, by
   validateAfterMovement: whatever the live modules covering a row's subject can no longer carry is
   simply LOST (R9) - the row is cut to what is still carried and deleted at 0, and DEW is never
   touched in either direction. Every later reader (fire-time OEW, SOEW, SDEW, DIST, HK jamming,
   the client, replays) then reads validated rows with no change of its own (D11).

   Two effects have no single subject and are gated where they are USED instead (D12, R10): BDEW
   (EW::getBlanketDEW, client ew.getSupportedBDEW) and Detect Stealth (stealth detection). Each unit
   gets only the points of the modules whose arc holds it - areaPointsReaching.

   ⚠️ MIRROR SET with ew.js: ELINT_MODULE_ONLY, getElintModuleSplit, isCoveredByElintModule,
   route/maxFlow/findPath (ew.routeElintModuleRows) and areaPointsReaching (ew.getElintAreaPointsReaching).
   ⚠️ EVERYTHING HERE IS BEHIND TWO GATES: TacGamedata::$elintModulesPresent (one static boolean for
   the whole game) and $ship->getElintModules() (an empty array on every other ship).
   ================================================================================================ */
class ElintModules{

	//R8 - only module points may pay for these; the scanner cannot
	private static $moduleOnlyTypes = array('SOEW' => true, 'SDEW' => true, 'BDEW' => true, 'DIST' => true, 'Detect Stealth' => true, 'JAM' => true);
	//R10 - no single subject: any live module may pay, and the effect is arc-checked per unit where it is used
	private static $subjectlessTypes = array('BDEW' => true, 'Detect Stealth' => true);

	public static function isModuleOnlyType($type){
		return isset(self::$moduleOnlyTypes[$type]);
	}

	//the ship's modules that are alive and switched on, in system-id order
	public static function liveModules($ship, $turn = null){
		$live = array();
		foreach ($ship->getElintModules() as $module){
			if ($module->isDestroyed()) continue;
			if ($module->isOfflineOnTurn($turn)) continue;
			$live[] = $module;
		}
		return $live;
	}

	/* Does any live module of $ship cover $unit? (plan §8.3) The ship itself always counts - its own
	   modules sit on it. Only meaningful for a ship that HAS modules; callers test getElintModules()
	   first. */
	public static function coversUnit($ship, $unit, $turn = null){
		if ($unit === null) return false;
		if ((int)$unit->id === (int)$ship->id) return true;
		foreach (self::liveModules($ship, $turn) as $module){
			if ($module->coversUnit($unit, $turn)) return true;
		}
		return false;
	}

	/* The use-time gate for BDEW and Detect Stealth (D12, R10), in one place: does $elint's ELINT
	   reach $unit? True for every ELINT ship without modules - their ELINT has no arc. */
	public static function elintReaches($elint, $unit, $turn = null){
		if (empty($elint->getElintModules())) return true;
		return self::coversUnit($elint, $unit, $turn);
	}

	/* ============================================================ validation after movement */

	/* Hook: MovementGamePhase::advance, after every unit has moved and before Pre-Firing. */
	public static function validateAfterMovement($gamedata, $dbManager){
		foreach ($gamedata->ships as $ship){
			if (empty($ship->getElintModules())) continue;
			if ($ship->isDestroyed()) continue;
			if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;
			$result = self::computeEffective($ship, $gamedata);
			if ($result['lost'] > 0) self::writeResult($ship, $result, $gamedata, $dbManager);
		}
	}

	/* PURE: what each of this turn's OEW and module-only rows is still worth, given where everything
	   ended its movement. Returns array(
	     'rows'      => array of array(EWentry, effective) for the rows that are cut,
	     'lost'      => total points lost,
	   ). A row is worth what route() can still carry of it. */
	public static function computeEffective($ship, $gamedata){
		$out = array('rows' => array(), 'lost' => 0);
		$routing = self::route($ship, $gamedata);
		foreach ($routing['rows'] as $i => $entry){
			$effective = array_sum($routing['flow'][$i]);
			if ($effective < (int)$entry->amount){
				$out['rows'][] = array($entry, $effective);
				$out['lost'] += (int)$entry->amount - $effective;
			}
		}
		return $out;
	}

	/* R10, refined by the user 2026-10-08: an AREA function (BDEW, Detect Stealth) reaches a unit with
	   only the points of the modules whose arc holds it - modules add up only where their arcs overlap
	   (E4). BDEW 8 paid by a port and a starboard module of 4 each is 1 BDEW to a friendly on either
	   side, and 2 to the ship itself, which sits on every module. Which module carries which point is
	   route()'s answer for the rows as they stand, so once Movement has validated them every reader
	   gets the same split; the modules carry an area function in system-id order, which only matters
	   while it leaves module points unspent.
	   ⚠️ MIRROR of ew.getElintAreaPointsReaching. Only for a ship WITH modules - callers test
	   getElintModules() first. */
	public static function areaPointsReaching($elint, $unit, $type, $gamedata){
		if ($unit === null) return 0;
		if (!$elint->getEWbyType($type, $gamedata->turn)) return 0; //nothing of this type: no routing to run (every hit at a friendly asks)
		$routing = self::route($elint, $gamedata);
		$isSelf = ((int)$unit->id === (int)$elint->id);
		$points = 0;
		foreach ($routing['rows'] as $i => $entry){
			if ($entry->type !== $type) continue;
			foreach ($routing['modules'] as $k => $module){
				if ($isSelf || $module->coversUnit($unit, $gamedata->turn)) $points += $routing['flow'][$i][$k + 1];
			}
		}
		return $points;
	}

	/* Which pool carries each point of this turn's OEW and module-only rows, given where everything
	   stands NOW. Returns array('rows' => EWentry list, 'modules' => the live modules, 'flow' =>
	   $flow[row][sink]) - sink 0 is the scanner, sink k + 1 the k-th live module. Plan §8.4:

	     N      = scanner output (already -2 per module, R14)
	     S      = CCEW + Detect Mines rows - scanner-only, never cut
	     D      = the committed DEW row - UNTOUCHABLE (R9)
	     Navail = N - S - D        what the scanner paid toward OEW when the orders were committed
	     a max-flow from the rows to {scanner (OEW only, capacity Navail), each live module covering
	     the row's subject (capacity = its output)}; BDEW and Detect Stealth reach every live module.

	   ⚠️ DETERMINISTIC by construction: rows are augmented one point at a time in row-id (= allocation)
	   order, and each search tries the scanner first and then the modules in system-id order, so the
	   EARLIEST allocations are the last to lose points. A later row's search may reroute an earlier
	   row's points between sinks but never reduces them.
	   ⚠️ MIRROR of ew.routeElintModuleRows - the client splits BDEW with it.
	   ⚠️ EW-boosted systems (Particle Impeders) also count against N on the client; no Kirishiac hull
	   carries one, so they are not modelled here. */
	private static function route($ship, $gamedata){
		$turn = $gamedata->turn;
		$modules = self::liveModules($ship, $turn);

		$scannerOnly = 0;
		$dew = 0;
		$rows = array();
		foreach ($ship->EW as $entry){
			if ((int)$entry->turn !== (int)$turn) continue;
			if ((int)$entry->amount <= 0) continue;
			if ($entry->type === 'DEW'){ $dew += (int)$entry->amount; continue; }
			if ($entry->type === 'OEW' || self::isModuleOnlyType($entry->type)){ $rows[] = $entry; continue; }
			$scannerOnly += (int)$entry->amount;
		}
		if (empty($rows)) return array('rows' => array(), 'modules' => $modules, 'flow' => array());

		$scannerFree = max(0, EW::getScannerOutput($ship, $turn) - $scannerOnly - $dew);

		//sinks: 0 = the scanner, 1.. = the live modules
		$capacity = array($scannerFree);
		foreach ($modules as $module) $capacity[] = max(0, (int)$module->getOutput());

		//which sinks each row may draw on, and the step it is carried in
		$distStep = $ship->hasSpecialAbility("ConstrainedEW") ? 4 : 3;
		$reach = array();
		$steps = array();
		foreach ($rows as $i => $entry){
			$sinks = array();
			if ($entry->type === 'OEW') $sinks[] = 0;
			if (isset(self::$subjectlessTypes[$entry->type])){
				foreach ($modules as $k => $module) $sinks[] = $k + 1;
			}else{
				$subject = $gamedata->getShipById((int)$entry->targetid);
				foreach ($modules as $k => $module){
					if ($subject !== null && $module->coversUnit($subject, $turn)) $sinks[] = $k + 1;
				}
			}
			$reach[$i] = $sinks;
			$steps[$i] = ($entry->type === 'DIST') ? $distStep : 1;
		}

		return array('rows' => $rows, 'modules' => $modules, 'flow' => self::maxFlow($rows, $reach, $capacity, $steps));
	}

	/* Unit-at-a-time augmenting paths over a bipartite graph (rows -> sinks). Small: at most five
	   sinks and a handful of rows, a few dozen points. Returns $flow[row][sink].
	   A row is carried in whole STEPS: Disruption is bought 3 points at a time (4 for ConstrainedEW)
	   and a part-step does nothing, so a step that cannot be carried in full is rolled back and its
	   points stay free for the rows after it. (Game 4452: DIST 6 on a unit only a 4-point module
	   covered, then SOEW 1 on that module's side. Rounding DIST down AFTER the flow kept its stray 4th
	   point on the module, so the SOEW was cut too - 4 lost where 3 was right.) */
	private static function maxFlow($rows, $reach, $capacity, $steps){
		$sinkCount = count($capacity);
		$flow = array();
		foreach ($rows as $i => $entry) $flow[$i] = array_fill(0, $sinkCount, 0);
		$used = array_fill(0, $sinkCount, 0);

		foreach ($rows as $i => $entry){
			$step = $steps[$i];
			$wholeSteps = intdiv(max(0, (int)$entry->amount), $step);
			for ($s = 0; $s < $wholeSteps; $s++){
				$flowBefore = $flow; //arrays copy on assignment: the roll-back point for this step
				$usedBefore = $used;
				for ($unit = 0; $unit < $step; $unit++){
					$path = self::findPath($i, $reach, $flow, $capacity, $used);
					if ($path === null){ //nothing left that can carry this step in full - undo its part
						$flow = $flowBefore;
						$used = $usedBefore;
						break 2;
					}
					foreach ($path['moves'] as $move){
						list($row, $sink, $delta) = $move;
						$flow[$row][$sink] += $delta;
					}
					$used[$path['free']] += 1; //every other sink on the path gained one point and lost one
				}
			}
		}
		return $flow;
	}

	/* Breadth-first from row $start over sinks: a sink with spare capacity ends the search; a full
	   sink hands the search on to every row already drawing on it (which may move a point to
	   another of ITS sinks). Returns array('moves' => [row, sink, +1/-1]..., 'free' => sink), or null. */
	private static function findPath($start, $reach, $flow, $capacity, $used){
		$parent = array(); //sink => array(previous sink or -1, the row that moves a point onto this sink)
		$queue = array();
		foreach ($reach[$start] as $sink){
			if (isset($parent[$sink])) continue;
			$parent[$sink] = array(-1, $start);
			$queue[] = $sink;
		}
		while (!empty($queue)){
			$sink = array_shift($queue);
			if ($used[$sink] < $capacity[$sink]){
				//walk back: each hop moves one point of `row` onto `at`, off the previous sink
				$moves = array();
				$at = $sink;
				while ($at !== -1){
					list($prev, $row) = $parent[$at];
					$moves[] = array($row, $at, +1);
					if ($prev !== -1) $moves[] = array($row, $prev, -1);
					$at = $prev;
				}
				return array('moves' => $moves, 'free' => $sink);
			}
			foreach ($flow as $row => $bySink){
				if ($bySink[$sink] <= 0) continue;
				foreach ($reach[$row] as $next){
					if (isset($parent[$next])) continue;
					$parent[$next] = array($sink, $row);
					$queue[] = $next;
				}
			}
		}
		return null;
	}

	/* Cut each row to what is still carried (deleting it at 0 - the client counts an OEW row as a
	   target whatever its amount, D11), in the database and in memory, and leave a note of the
	   points lost on the ship's first module for the panel and the log. */
	private static function writeResult($ship, $result, $gamedata, $dbManager){
		$deleted = array();
		foreach ($result['rows'] as $pair){
			list($entry, $effective) = $pair;
			if ($effective <= 0){
				$dbManager->deleteEwEntryById($gamedata->id, $entry->id);
				$deleted[spl_object_id($entry)] = true;
			}else{
				$dbManager->setEwAmountById($gamedata->id, $entry->id, $effective);
				$entry->amount = $effective;
			}
		}
		if (!empty($deleted)){
			$kept = array();
			foreach ($ship->EW as $entry) if (!isset($deleted[spl_object_id($entry)])) $kept[] = $entry;
			$ship->EW = $kept;
		}

		$modules = $ship->getElintModules();
		$note = new IndividualNote(-1, $gamedata->id, $gamedata->turn, $gamedata->phase, $ship->id, $modules[0]->id,
			'ElintLost', 'ELINT points lost out of arc', (int)$result['lost']);
		$dbManager->insertIndividualNote($note);
	}
}
?>
