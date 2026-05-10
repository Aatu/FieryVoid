<?php

/*
 * HangarOps — static helper for Hangar Operations (B5W §10.1).
 *
 * Stage 1: capacity tracking + initial hangar population.
 * Stage 4+: launch/land flow, damage handling.
 *
 * All hangar state lives on individual Hangar instances (see baseSystems.php),
 * persisted via individualNotes (notekey 'hangarUsage'). HangarOps holds the
 * cross-cutting math: which hangar a craft fits in, capacity per category, etc.
 */
class HangarOps {

	/* Initial population of hangar contents at game start.
	 * Walks $ship->fighters (declared capacity per category) and:
	 *   1. Docks any FighterFlights chosen to start in hangar (Stage 7 — empty for now).
	 *   2. Auto-fills declared shuttle/minesweeping-shuttle slots with shuttle records.
	 *   3. Auto-fills any leftover hangar capacity with shuttle records
	 *      (MinesweepingShuttle if $ship->minesweeperbonus > 0, else Shuttle).
	 *
	 * Called from Hangar::onIndividualNotesLoaded by the FIRST hangar on the ship
	 * (idempotency guard via $usagePopulated). All hangars on the ship are
	 * populated in a single pass and marked $usagePopulated = true.
	 */
	public static function populateInitialHangarUsage($ship, $gamedata){
		$hangars = self::collectHangars($ship);
		if (empty($hangars)) return;

		//Mark all hangars done up-front so re-entry guards short-circuit
		foreach ($hangars as $h) $h->usagePopulated = true;

		//Step 1: place flights the player chose to start in hangar (Stage 7; empty for now).
		$undeployedByCategory = self::collectUndeployedFlightsByCategory($ship, $gamedata);
		if (is_array($ship->fighters)) {
			foreach ($ship->fighters as $category => $declaredCount){
				if (empty($undeployedByCategory[$category])) continue;
				foreach ($undeployedByCategory[$category] as $flight){
					$hangar = self::pickHangarForCategory($hangars, $category, $flight->flightSize);
					if (!$hangar) break;
					$hangar->hangarUsage[] = array(
						'phpclass'    => $flight->phpclass,
						'name'        => $flight->name,
						'flightSize'  => $flight->flightSize,
						'hangarType'  => $category,
					);
				}
			}
		}

		//Step 2: explicit shuttle/minesweeping-shuttle declarations get auto-filled
		//(combat fighter declarations are NOT auto-filled here — those auto-deploy
		// to space at turn 1 via the existing fleet-builder flow.)
		$totalDeclared = 0;
		if (is_array($ship->fighters)) {
			foreach ($ship->fighters as $category => $declaredCount){
				$totalDeclared += (int)$declaredCount;
				$shuttleClass = self::shuttlePhpclassForCategory($category);
				if ($shuttleClass === null) continue;   //not a shuttle category
				for ($i = 0; $i < (int)$declaredCount; $i++){
					$hangar = self::pickHangarForShuttle($hangars, 1);
					if (!$hangar) break;
					$hangar->hangarUsage[] = array(
						'phpclass'    => $shuttleClass,
						'name'        => self::shuttleDisplayNameFor($shuttleClass),
						'flightSize'  => 1,
						'hangarType'  => $category,
					);
				}
			}
		}

		//Step 3: leftover capacity → fill with shuttles. Use MinesweepingShuttle
		//if the carrier has any minesweeper bonus declared.
		$totalCapacity = 0;
		foreach ($hangars as $h) $totalCapacity += (int)$h->maxhealth;
		$leftover = $totalCapacity - $totalDeclared;
		if ($leftover > 0){
			$leftoverClass = (isset($ship->minesweeperbonus) && $ship->minesweeperbonus > 0)
				? 'MinesweepingShuttle'
				: 'Shuttle';
			$leftoverCategory = ($leftoverClass === 'MinesweepingShuttle') ? 'minesweeping shuttles' : 'shuttles';
			for ($i = 0; $i < $leftover; $i++){
				$hangar = self::pickHangarForShuttle($hangars, 1);
				if (!$hangar) break;
				$hangar->hangarUsage[] = array(
					'phpclass'    => $leftoverClass,
					'name'        => self::shuttleDisplayNameFor($leftoverClass),
					'flightSize'  => 1,
					'hangarType'  => $leftoverCategory,
				);
			}
		}
	}

	/* Maps a $ship->fighters category key to a shuttle phpclass, or null
	 * if the category isn't a shuttle slot (i.e. it's a fighter category
	 * that auto-deploys instead of staying in hangar).
	 */
	public static function shuttlePhpclassForCategory($category){
		$normalized = strtolower(trim((string)$category));
		switch ($normalized) {
			case 'shuttles':
				return 'Shuttle';
			case 'minesweeping shuttles':
				return 'MinesweepingShuttle';
			default:
				return null;
		}
	}

	/* Display name for a shuttle phpclass — used in hangar tooltip aggregation. */
	public static function shuttleDisplayNameFor($phpclass){
		switch ($phpclass) {
			case 'MinesweepingShuttle':
				return 'Minesweeping Shuttle';
			case 'Shuttle':
			default:
				return 'Shuttle';
		}
	}

	/* Picks any hangar with at least $flightSize free boxes — shuttles per
	 * B5W §10.1 may use any fighter box. (A future "shuttle-only" hangar
	 * type would still match here, since it accepts shuttles by definition.)
	 */
	public static function pickHangarForShuttle($hangars, $flightSize){
		foreach ($hangars as $h){
			if ($h->maxhealth - self::usageCountFor($h) >= $flightSize) return $h;
		}
		return null;
	}

	/* Stage 1 placeholder. Stage 7 (deployment-phase docking) will return
	 * flights the player chose to start in hangar instead of on the map.
	 * Returns: ['categoryKey' => [FighterFlight, ...], ...]
	 */
	public static function collectUndeployedFlightsByCategory($ship, $gamedata){
		return array();
	}

	/* All Hangar systems on a given ship, in encounter order (primary, front, etc.) */
	public static function collectHangars($ship){
		$hangars = array();
		if (!isset($ship->systems) || !is_array($ship->systems)) return $hangars;
		foreach ($ship->systems as $sys){
			if ($sys instanceof Hangar) $hangars[] = $sys;
		}
		return $hangars;
	}

	/* Picks the first hangar that
	 *   (a) accepts $category (exact match or universal 'fighters' fallback)
	 *   (b) has at least $flightSize free boxes
	 * Returns null if no hangar fits.
	 */
	public static function pickHangarForCategory($hangars, $category, $flightSize){
		//Exact category match first
		foreach ($hangars as $h){
			if ($h->hangarType !== $category) continue;
			if ($h->maxhealth - self::usageCountFor($h) >= $flightSize) return $h;
		}
		//Fallback to universal hangars
		foreach ($hangars as $h){
			if ($h->hangarType !== 'fighters') continue;
			if ($h->maxhealth - self::usageCountFor($h) >= $flightSize) return $h;
		}
		return null;
	}

	/* Total occupied boxes across this hangar. */
	public static function usageCountFor($hangar){
		$n = 0;
		if (!is_array($hangar->hangarUsage)) return 0;
		foreach ($hangar->hangarUsage as $entry){
			$n += (int)($entry['flightSize'] ?? 1);
		}
		return $n;
	}

	/* Maps a FighterFlight to the carrier's $fighters category that should hold it.
	 * Order: explicit hangarRequired -> 'normal' -> 'fighters' -> first key.
	 */
	public static function categoryFor($flight, $carrier){
		if (!is_array($carrier->fighters) || empty($carrier->fighters)) return 'fighters';
		if (isset($carrier->fighters[$flight->hangarRequired])) return $flight->hangarRequired;
		foreach (array('normal', 'fighters') as $generic){
			if (isset($carrier->fighters[$generic])) return $generic;
		}
		$keys = array_keys($carrier->fighters);
		return $keys[0];
	}

	/* Total declared hangar capacity by category for a ship. */
	public static function capacityByCategory($ship){
		if (!is_array($ship->fighters)) return array();
		return $ship->fighters;
	}

	/* Currently-used boxes by category across all hangars on $ship. */
	public static function usageByCategory($ship){
		$out = array();
		foreach (self::collectHangars($ship) as $h){
			foreach ($h->hangarUsage as $entry){
				$cat = $entry['hangarType'] ?? $h->hangarType;
				if (!isset($out[$cat])) $out[$cat] = 0;
				$out[$cat] += (int)($entry['flightSize'] ?? 1);
			}
		}
		return $out;
	}

	/* Evict $count craft from a hangar (1 box of damage = 1 craft destroyed).
	 * Priority: shuttles first (cheapest, most fungible), then anything else
	 * in stored order. For partial-flight evictions (e.g. damage=2 from a
	 * stored 6-fighter flight), the record's $flightSize is reduced rather
	 * than the whole record dropped.
	 */
	public static function evictCraftFromHangar($hangar, $count){
		if ($count <= 0 || empty($hangar->hangarUsage)) return 0;

		$indexed = array();
		foreach ($hangar->hangarUsage as $idx => $entry){
			$indexed[] = array(
				'idx'      => $idx,
				'priority' => self::evictionPriorityFor($entry),
			);
		}
		usort($indexed, function($a, $b){
			if ($a['priority'] !== $b['priority']) return $a['priority'] - $b['priority'];
			return $a['idx'] - $b['idx'];   //stable: original order within same priority
		});

		$evicted = 0;
		foreach ($indexed as $row){
			if ($evicted >= $count) break;
			$idx = $row['idx'];
			$available = (int)($hangar->hangarUsage[$idx]['flightSize'] ?? 1);
			$take = min($available, $count - $evicted);
			$hangar->hangarUsage[$idx]['flightSize'] = $available - $take;
			$evicted += $take;
		}

		//Compact away records that were fully evicted
		$hangar->hangarUsage = array_values(array_filter($hangar->hangarUsage, function($e){
			return (int)($e['flightSize'] ?? 0) > 0;
		}));

		return $evicted;
	}

	/* Lower priority value = drop first. Stage 3 uses a coarse classifier;
	 * a future polish pass can switch to per-craft pointCost lookups.
	 */
	public static function evictionPriorityFor($entry){
		$phpclass = $entry['phpclass'] ?? '';
		if ($phpclass === 'Shuttle' || $phpclass === 'MinesweepingShuttle') return 0;
		if (stripos($phpclass, 'shuttle') !== false) return 1;
		return 10;
	}

	/* End-of-turn hook for a single Hangar — drop stored craft to fit
	 * remaining capacity, and reset per-turn launch/land counters.
	 * Called from Hangar::criticalPhaseEffects.
	 */
	public static function onHangarCriticalPhase($hangar){
		//Reset shared launch+land budget for next turn
		$hangar->launchedThisTurn = 0;
		$hangar->landedThisTurn = 0;

		if ($hangar->isDestroyed()){
			$hangar->hangarUsage = array();
			return 0;
		}

		$remaining = $hangar->getRemainingHealth();
		$stored = self::usageCountFor($hangar);
		if ($stored <= $remaining) return 0;

		return self::evictCraftFromHangar($hangar, $stored - $remaining);
	}

	/* === Stage 4: launch flow ============================================ */

	/* Spawn a new FighterFlight from a hangar's stored craft.
	 * Mirrors BallisticMineLauncher::createLoiteringMine (missile.php:2352-2412):
	 * insert ship row, set $spawned, write deploy MovementOrder, init
	 * per-system data, write replay note. Also persists tac_flightsize.
	 *
	 * On success: returns the new ship id (and $hangarUsage / $launchedThisTurn
	 * are mutated to reflect the launch). On failure: returns null and the
	 * hangar state is unchanged.
	 *
	 * NOTE: caller is responsible for validating launch eligibility BEFORE
	 * calling this — performLaunch trusts its inputs.
	 */
	public static function performLaunch($hangar, $carrier, $phpclass, $launchSize, $gamedata){
		if (!is_string($phpclass) || $phpclass === '') return null;
		if (!class_exists($phpclass)) return null;
		$launchSize = max(1, (int)$launchSize);

		$lastMove = $carrier->getLastMovement();
		if (!$lastMove) return null;

		$spawnPos = $lastMove->position;
		$heading  = (int)$lastMove->heading;
		$facing   = ($lastMove->facing + (int)$hangar->direction) % 6;
		$speed    = (int)$lastMove->speed;

		//Build the new flight. Constructor populates 1 fighter by default;
		//bump flightSize and re-populate so we get the requested size. The
		//autoid sequence is deterministic (1, 2, ...) so the same call at
		//load time will reproduce the same ids.
		$flightName = self::flightNameFor($phpclass, $carrier);
		$flight = new $phpclass($gamedata->id, $carrier->userid, $flightName, $carrier->slot);
		$flight->team = $carrier->team;
		if ($launchSize > $flight->flightSize) {
			$flight->flightSize = $launchSize;
			if (method_exists($flight, 'populate')) {
				$flight->populate();
			}
		}

		//Persist ship row and capture the assigned id.
		$shipid = Manager::insertSingleShip($gamedata, $flight, $carrier->userid);
		$flight->id = $shipid;
		$flight->spawned = $gamedata->turn;

		//Persist flight size so getFlightSize() repopulates correctly on reload.
		Manager::insertSingleFlightSize($gamedata->id, $shipid, $flight->flightSize);

		//Deploy MovementOrder at carrier's current hex with hangar-offset facing.
		$deployMove = new MovementOrder(null, "deploy", $spawnPos, 0, 0, $speed, $heading, $facing, false, $gamedata->turn, 0, 0);
		Manager::insertSingleMovement($gamedata->id, $shipid, $deployMove);

		//Initialize per-system data so weapons aren't uncharged on next turn.
		SystemData::initSystemData($gamedata->turn, $gamedata->id);
		foreach ($flight->systems as $craft) {
			$craft->setInitialSystemData($flight);
			if (!isset($craft->systems) || !is_array($craft->systems)) continue;
			foreach ($craft->systems as $sys) {
				$sys->setInitialSystemData($flight);
				if ($sys instanceof Weapon) {
					$load = $sys->getStartLoading();
					if ($load) {
						$load->loading = $sys->loadingtime;   //fully loaded on launch
						SystemData::addDataForSystem($sys->id, 0, $shipid, $load->toJSON());
					}
				}
			}
		}
		Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

		//Update hangar bookkeeping
		self::removeFromHangarUsage($hangar, $phpclass, $launchSize);
		$hangar->launchedThisTurn += $launchSize;

		//Replay note tied to the hangar so we can render the launch in history.
		$note = new IndividualNote(
			-1,
			$gamedata->id,
			$gamedata->turn,
			$gamedata->phase,
			$carrier->id,
			$hangar->id,
			'hangarLaunchEvent',
			'Hangar launched flight',
			$shipid . ':' . $phpclass . ':' . $launchSize
		);
		Manager::insertIndividualNote($note);

		return $shipid;
	}

	/* Decrement $hangarUsage by $count craft of $phpclass. Records of the
	 * matching phpclass are reduced (or removed when reaching zero) in stored
	 * order. Caller is responsible for first verifying enough are stored.
	 */
	public static function removeFromHangarUsage($hangar, $phpclass, $count){
		if ($count <= 0 || empty($hangar->hangarUsage)) return 0;
		$removed = 0;
		foreach ($hangar->hangarUsage as $idx => $entry){
			if ($removed >= $count) break;
			if (($entry['phpclass'] ?? '') !== $phpclass) continue;
			$available = (int)($entry['flightSize'] ?? 1);
			$take = min($available, $count - $removed);
			$hangar->hangarUsage[$idx]['flightSize'] = $available - $take;
			$removed += $take;
		}
		$hangar->hangarUsage = array_values(array_filter($hangar->hangarUsage, function($e){
			return (int)($e['flightSize'] ?? 0) > 0;
		}));
		return $removed;
	}

	/* Generates a name for a launched flight, e.g. "Aurora Flight 1".
	 * Counts existing same-phpclass ships from the same slot to pick the suffix.
	 */
	public static function flightNameFor($phpclass, $carrier){
		$base = $phpclass;
		//Try to use the static blueprint's shipClass for a friendlier name
		if (class_exists($phpclass)) {
			try {
				$probe = new $phpclass(0, $carrier->userid, '', $carrier->slot);
				if (!empty($probe->shipClass)) $base = $probe->shipClass;
			} catch (Exception $e) {
				//fall back to phpclass on construction errors
			}
		}
		return $base;
	}

	/* Stage 4 gate: launch flow is only enabled for the test playground game
	 * (TacGamedata::$safeGameID = 3730) and local dev games (id <= 0).
	 * Stage 9 removes this gate.
	 */
	public static function isFlowEnabled($gameid){
		$gameid = (int)$gameid;
		return $gameid <= 0 || $gameid >= TacGamedata::$safeGameID;
	}

	/* Resolve queued launch orders for a hangar at end of turn.
	 * Reads $hangar->pendingLaunchOrder (populated from the latest
	 * 'hangarLaunchOrder' note in onIndividualNotesLoaded) and calls
	 * performLaunch for each entry that still passes canLaunch validation.
	 *
	 * Failed entries are silently dropped with a 'hangarLaunchEvent' note
	 * recording the reason — players can see this in replay.
	 */
	public static function processLaunchOrders($hangar, $carrier, $gamedata){
		if (empty($hangar->pendingLaunchOrder)) return;
		if (!self::isFlowEnabled($gamedata->id)) {
			$hangar->pendingLaunchOrder = null;
			return;
		}

		foreach ($hangar->pendingLaunchOrder as $entry) {
			$phpclass = isset($entry['phpclass']) ? (string)$entry['phpclass'] : '';
			$size     = isset($entry['size'])     ? (int)$entry['size']        : 0;
			$reason   = null;
			if (!self::canLaunch($hangar, $carrier, $phpclass, $size, $gamedata, $reason)) {
				$failNote = new IndividualNote(
					-1,
					$gamedata->id,
					$gamedata->turn,
					$gamedata->phase,
					$carrier->id,
					$hangar->id,
					'hangarLaunchEvent',
					'Hangar launch failed',
					'fail:' . $phpclass . ':' . $size . ':' . ($reason ?? 'unknown')
				);
				Manager::insertIndividualNote($failNote);
				continue;
			}
			self::performLaunch($hangar, $carrier, $phpclass, $size, $gamedata);
		}

		$hangar->pendingLaunchOrder = null;   //consumed
	}

	/* Returns true if launching $size craft of $phpclass from $hangar is legal
	 * RIGHT NOW. Used by Hangar::doIndividualNotesTransfer to validate before
	 * queueing, and by criticalPhaseEffects before performing.
	 */
	public static function canLaunch($hangar, $carrier, $phpclass, $size, $gamedata, &$reason = null){
		$size = (int)$size;
		if ($size <= 0) { $reason = 'invalid size'; return false; }
		if ($hangar->isDestroyed()) { $reason = 'hangar destroyed'; return false; }

		//Carrier may not launch on a turn it pivoted or rolled
		if (Movement::isPivoting($carrier, $gamedata->turn, $gamedata) || Movement::isRolling($carrier, $gamedata->turn, $gamedata)) {
			$reason = 'carrier pivoting/rolling';
			return false;
		}

		//Shared launch+land budget vs hangar output
		$used = (int)$hangar->launchedThisTurn + (int)$hangar->landedThisTurn;
		if ($used + $size > (int)$hangar->output) { $reason = 'launch rate exceeded'; return false; }

		//Enough stored craft of this class
		$available = 0;
		if (is_array($hangar->hangarUsage)) {
			foreach ($hangar->hangarUsage as $entry) {
				if (($entry['phpclass'] ?? '') === $phpclass) {
					$available += (int)($entry['flightSize'] ?? 1);
				}
			}
		}
		if ($available < $size) { $reason = 'not enough stored craft'; return false; }

		return true;
	}
}
