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
				$count = (int)$declaredCount;
				$totalDeclared += $count;
				$shuttleClass = self::shuttlePhpclassForCategory($category);
				if ($shuttleClass === null) continue;   //not a shuttle category
				
				$shuttleName = self::shuttleDisplayNameFor($shuttleClass);

				while ($count > 0){
					$hangar = self::pickHangarForShuttle($hangars, 1);
					if (!$hangar) break;
					
					$free = (int)$hangar->maxhealth - self::usageCountFor($hangar);
					$take = min($count, $free);
					
					$hangar->hangarUsage[] = array(
						'phpclass'    => $shuttleClass,
						'name'        => $shuttleName,
						'flightSize'  => $take,
						'hangarType'  => $category,
					);
					$count -= $take;
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
			$leftoverName = self::shuttleDisplayNameFor($leftoverClass);

			$count = $leftover;
			while ($count > 0){
				$hangar = self::pickHangarForShuttle($hangars, 1);
				if (!$hangar) break;

				$free = (int)$hangar->maxhealth - self::usageCountFor($hangar);
				$take = min($count, $free);

				$hangar->hangarUsage[] = array(
					'phpclass'    => $leftoverClass,
					'name'        => $leftoverName,
					'flightSize'  => $take,
					'hangarType'  => $leftoverCategory,
				);
				$count -= $take;
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

	/* Narrows a Hangar's hangarType from its ship's $fighters declaration when
	 * the Hangar is still using the universal default ('fighters' / 'normal' /
	 * legacy 0). Only runs when the ship declares EXACTLY ONE size-specific
	 * fighter category — multi-size carriers (Cylon Basestar etc.) need
	 * explicit per-Hangar hangarType in the ship file because we can't tell
	 * which Hangar belongs to which size bay from here.
	 *
	 * Without this, ships like Var'Nic (declared $fighters = ['medium'=>6] but
	 * no explicit Hangar hangarType) keep the universal default and accept any
	 * size of fighter through hangarAcceptsCategory's 'fighters' shortcut.
	 */
	public static function inferHangarType($hangar, $ship){
		$hType = strtolower(trim((string)$hangar->hangarType));
		//Only narrow universal slots. Anything specific (medium, heavy, shuttles,
		//assault shuttles, BPods, custom 'Raiders', etc.) was an intentional
		//ship-file choice and stays.
		if ($hType !== '' && $hType !== 'fighters' && $hType !== 'normal') return;

		if (!isset($ship->fighters) || !is_array($ship->fighters) || empty($ship->fighters)) return;

		$sizes = array('heavy', 'medium', 'light', 'ultralight');
		$declared = array();
		foreach ($ship->fighters as $cat => $count){
			$catLower = strtolower(trim((string)$cat));
			if (in_array($catLower, $sizes, true)) $declared[] = $catLower;
		}
		if (count($declared) !== 1) return;     //ambiguous — leave universal

		$hangar->hangarType = $declared[0];
	}

	/* Returns the size category a flight physically requires, independent of
	 * which carrier it's docking into. Mirrors the classification used in
	 * checkChoices() (gamelobby.js): explicit $hangarRequired wins; generic
	 * 'fighters'/'normal' falls back to jinkinglimit-based sizing.
	 *
	 * Returned values are the canonical hangarType strings:
	 *   'heavy' | 'medium' | 'light' | 'ultralight'
	 *   'shuttles' | 'minesweeping shuttles' | 'assault shuttles' | 'Breaching Pods'
	 *   'superheavy' | <custom>
	 */
	public static function trueSizeOf($flight){
		$req = isset($flight->hangarRequired) ? $flight->hangarRequired : 'fighters';
		$reqLower = strtolower(trim((string)$req));
		if ($reqLower === '' || $reqLower === 'fighters' || $reqLower === 'normal') {
			$jink = (int)($flight->jinkinglimit ?? 0);
			if ($jink >= 99) return 'ultralight';
			if ($jink >= 10) return 'light';
			if ($jink >= 8)  return 'medium';
			if ($jink >= 6)  return 'heavy';
			return 'medium';                   //safe-ish default for unclassifiable
		}
		return $req;                           //preserve casing for 'Breaching Pods', 'Raiders', etc.
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
		if ($phpclass === 'Shuttle') return 0;
		if ($phpclass === 'MinesweepingShuttle') return 1;
		if (stripos($phpclass, 'shuttle') !== false) return 2;
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

		//Resurrect path: if the launched stash entry matches a previously docked
		//flight (carries dockedFlightId AND the launch size matches the entry's
		//flightSize), reuse that flight instead of cloning a fresh one. This
		//preserves ship identity across dock/launch cycles and avoids the DB
		//growing a new row on every relaunch. Falls through to the new-spawn
		//path when no dockedFlightId entry matches the requested phpclass+size.
		$resurrectedFlight = self::resurrectDockedFlight($hangar, $phpclass, $launchSize, $gamedata);
		if ($resurrectedFlight) {
			$resurrectedFlight->removed = false;
			$resurrectedFlight->removedTurn = null;
			//Mark spawned so replay treats this turn as the (re-)appearance.
			$resurrectedFlight->spawned = $gamedata->turn;

			$deployMove = new MovementOrder(null, "deploy", $spawnPos, 0, 0, $speed, $heading, $facing, false, $gamedata->turn, 0, 0);
			Manager::insertSingleMovement($gamedata->id, $resurrectedFlight->id, $deployMove);

			$hangar->launchedThisTurn += $launchSize;

			$note = new IndividualNote(
				-1,
				$gamedata->id,
				$gamedata->turn,
				$gamedata->phase,
				$carrier->id,
				$hangar->id,
				'hangarLaunchEvent',
				'Hangar relaunched docked flight',
				$resurrectedFlight->id . ':' . $phpclass . ':' . $launchSize . ':resurrected'
			);
			Manager::insertIndividualNote($note);

			return $resurrectedFlight->id;
		}

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

	/* Look for a hangarUsage entry matching $phpclass with a dockedFlightId
	 * AND flightSize equal to $launchSize. If found, removes the entry from
	 * hangarUsage and returns the existing flight ship object. Returns null
	 * if no such entry exists (caller falls through to the new-spawn path).
	 *
	 * Constraint: only resurrects when launchSize matches the docked entry's
	 * flightSize exactly. Splitting a docked flight on relaunch is deferred
	 * to Stage 9 polish — for now a partial relaunch creates a fresh flight
	 * and leaves the original docked.
	 */
	public static function resurrectDockedFlight($hangar, $phpclass, $launchSize, $gamedata){
		if (empty($hangar->hangarUsage)) return null;
		foreach ($hangar->hangarUsage as $idx => $entry){
			if (($entry['phpclass'] ?? '') !== $phpclass) continue;
			$flightId = isset($entry['dockedFlightId']) ? (int)$entry['dockedFlightId'] : 0;
			if ($flightId <= 0) continue;
			$entrySize = (int)($entry['flightSize'] ?? 0);
			if ($entrySize !== $launchSize) continue;

			$flight = $gamedata->getShipById($flightId);
			if (!$flight) continue;

			//Drop the entry; rebuild keys so json_encode keeps array form.
			array_splice($hangar->hangarUsage, $idx, 1);
			$hangar->hangarUsage = array_values($hangar->hangarUsage);
			return $flight;
		}
		return null;
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

	/* === Stage 5: dock flow ============================================== */

	/* Returns true if $flight can dock $count craft into $hangar on $carrier
	 * RIGHT NOW. Per B5W §10.1.3: same hex, same heading, carrier speed within
	 * the flight's thrust window, hangar healthy, hangar has compatible free
	 * boxes, output budget has headroom, and carrier didn't pivot/roll.
	 */
	public static function canShipReceive($hangar, $carrier, $flight, $count, $gamedata, &$reason = null){
		if (!$flight instanceof FighterFlight) { $reason = 'not a flight'; return false; }
		if ($flight->removed || $flight->isDestroyed()) { $reason = 'flight already removed'; return false; }
		if ($hangar->isDestroyed()) { $reason = 'hangar destroyed'; return false; }
		if ($carrier->isDestroyed() || $carrier->removed) { $reason = 'carrier not in play'; return false; }

		$count = (int)$count;
		if ($count <= 0) { $reason = 'invalid count'; return false; }

		//Carrier may not land on a turn it pivoted or rolled
		if (Movement::isPivoting($carrier, $gamedata->turn, $gamedata) || Movement::isRolling($carrier, $gamedata->turn, $gamedata)) {
			$reason = 'carrier pivoting/rolling'; return false;
		}

		$carrierMove = $carrier->getLastMovement();
		$flightMove  = $flight->getLastMovement();
		if (!$carrierMove || !$flightMove) { $reason = 'movement missing'; return false; }

		//Same hex
		$carrierPos = $carrierMove->position;
		$flightPos  = $flightMove->position;
		if (!isset($carrierPos->q) || !isset($flightPos->q)
			|| $carrierPos->q != $flightPos->q || $carrierPos->r != $flightPos->r) {
			$reason = 'not in same hex'; return false;
		}

		//Same heading
		if ((int)$carrierMove->heading !== (int)$flightMove->heading) {
			$reason = 'heading mismatch'; return false;
		}

		//Speed gap must be within flight thrust budget. Fighters can decelerate
		//up to their freethrust to match a slower carrier, or accelerate up to
		//freethrust to match a faster one. Per the rules dive: |delta| <= thrust.
		$thrust = (int)($flight->freethrust ?? 0);
		$delta = abs((int)$carrierMove->speed - (int)$flightMove->speed);
		if ($delta > $thrust) {
			$reason = 'speed delta exceeds flight thrust'; return false;
		}

		//Hangar must have a compatible category with $count free boxes.
		//Use the flight's TRUE size (not carrier-mapped category) so a heavy
		//flight is rejected when the carrier only has medium slots, etc.
		$category = self::trueSizeOf($flight);
		$free = self::freeBoxesByCategory($hangar, $category);
		if ($free < $count) { $reason = 'hangar full'; return false; }

		//Shared launch+land budget vs hangar output
		$used = (int)$hangar->launchedThisTurn + (int)$hangar->landedThisTurn;
		if ($used + $count > (int)$hangar->output) { $reason = 'land rate exceeded'; return false; }

		return true;
	}

	/* Free boxes in $hangar that can hold $category. A 'fighters' (universal)
	 * hangar accepts any category. Other hangars only accept their declared
	 * type, with shuttles allowed everywhere per §10.1.
	 */
	public static function freeBoxesByCategory($hangar, $category){
		if (!self::hangarAcceptsCategory($hangar, $category)) return 0;
		$max = (int)$hangar->getRemainingHealth();
		return max(0, $max - self::usageCountFor($hangar));
	}

	public static function hangarAcceptsCategory($hangar, $category){
		$hType = strtolower(trim((string)$hangar->hangarType));
		$cat   = strtolower(trim((string)$category));
		if ($hType === '' || $cat === '') return false;

		//Universal fighter slot accepts anything (any size, plus shuttles).
		if ($hType === 'fighters' || $hType === 'normal') return true;

		//Exact match (medium-medium, 'Breaching Pods'-'Breaching Pods', 'Raiders'-'Raiders', …)
		if ($hType === $cat) return true;

		//Combat-fighter size hierarchy: a slot accepts its declared size or smaller.
		//Mirrors checkChoices() in gamelobby.js, where heavy hangars also count
		//toward medium/light/ultralight capacity, medium hangars toward light/ultralight,
		//etc. — i.e. larger slots are strictly more permissive than smaller ones.
		static $sizeRank = array(
			'ultralight' => 1,
			'light'      => 2,
			'medium'     => 3,
			'heavy'      => 4,
		);
		if (isset($sizeRank[$hType]) && isset($sizeRank[$cat])) {
			return $sizeRank[$cat] <= $sizeRank[$hType];
		}

		//Shuttles & minesweeping shuttles can use any combat-fighter slot per B5W §10.1.
		if (($cat === 'shuttles' || $cat === 'minesweeping shuttles') && isset($sizeRank[$hType])) {
			return true;
		}

		//Assault shuttle slots also hold breaching pods (per checkChoices BP-compat list).
		if ($hType === 'assault shuttles' && $cat === 'breaching pods') return true;

		return false;
	}

	/* Pick hangars on $carrier that can receive $flight, in fill order. The
	 * search prefers exact-category matches first, then universal hangars.
	 * Returns a list of [hangar, freeBoxes] pairs.
	 */
	public static function eligibleHangarsForLanding($carrier, $flight, $gamedata){
		$out = array();
		$hangars = self::collectHangars($carrier);
		if (empty($hangars)) return $out;
		//Match against the flight's TRUE size so size hierarchy is honoured
		//regardless of how the carrier's $fighters declaration looks.
		$category = self::trueSizeOf($flight);

		//Exact match first
		foreach ($hangars as $h){
			if ($h->hangarType !== $category) continue;
			if ($h->isDestroyed()) continue;
			$free = self::freeBoxesByCategory($h, $category);
			$budget = max(0, (int)$h->output - ((int)$h->launchedThisTurn + (int)$h->landedThisTurn));
			$capacity = min($free, $budget);
			if ($capacity > 0) $out[] = array('hangar' => $h, 'capacity' => $capacity);
		}
		//Then any hangar that accepts the category via the size hierarchy
		//(larger fighter slots, universal slots, shuttle-compatible slots).
		foreach ($hangars as $h){
			if ($h->hangarType === $category) continue;            //already considered above
			if (!self::hangarAcceptsCategory($h, $category)) continue;
			if ($h->isDestroyed()) continue;
			$free = self::freeBoxesByCategory($h, $category);
			$budget = max(0, (int)$h->output - ((int)$h->launchedThisTurn + (int)$h->landedThisTurn));
			$capacity = min($free, $budget);
			if ($capacity > 0) $out[] = array('hangar' => $h, 'capacity' => $capacity);
		}
		return $out;
	}

	/* Materialise $count craft from $flight into $hangar's storage.
	 * Returns the actual number docked (capped at the flight's current active
	 * craft count). Caller should validate via canShipReceive first.
	 *
	 * Two paths:
	 *  - Full dock ($count >= active craft): the flight ship is flagged
	 *    $removed/$removedTurn so it vanishes from board/target lists, and
	 *    the stash record carries $dockedFlightId so a future relaunch can
	 *    resurrect the same ship id (preserving damage state across the
	 *    dock cycle).
	 *  - Partial dock ($count < active craft): N individual Fighters are
	 *    flagged DisengagedFighter so they leave the flight, the remainder
	 *    continues in space, and the stash record is NOT linked to a flight
	 *    id (relaunch from this record spawns a fresh flight). Re-using
	 *    DisengagedFighter avoids new client-side critical classes; the
	 *    hangarDockEvent note distinguishes the actual fate in replay.
	 *
	 * Mutates: $hangar->hangarUsage (appends entry), $hangar->landedThisTurn,
	 * and either $flight->removed/$flight->removedTurn (full) or per-fighter
	 * criticals (partial).
	 */
	public static function performLand($hangar, $carrier, $flight, $count, $gamedata){
		if (!$flight instanceof FighterFlight) return 0;
		$count = max(1, (int)$count);

		//Clamp to whatever craft are still active in this flight (other dock
		//orders processed earlier this turn may have already disengaged some).
		$activeCount = $flight->countActiveCraft($gamedata->turn);
		if ($activeCount <= 0) return 0;
		if ($count > $activeCount) $count = $activeCount;

		$partial = ($count < $activeCount);

		//Bucket the entry under the flight's TRUE size, not the carrier-mapped
		//category — this keeps the hangarUsage record accurate even when the
		//flight is docked in a larger slot via size hierarchy.
		$category = self::trueSizeOf($flight);
		$entry = array(
			'phpclass'    => $flight->phpclass,
			'name'        => $flight->name,
			'flightSize'  => $count,
			'hangarType'  => $category,
			'dockedTurn'  => $gamedata->turn,
		);
		if (!$partial) {
			//Only full docks link the stash record to the source flight; partial
			//docks intentionally drop the link so a relaunch spawns fresh.
			$entry['dockedFlightId'] = $flight->id;
		}
		$hangar->hangarUsage[] = $entry;
		$hangar->landedThisTurn += $count;

		if ($partial) {
			self::disengageFightersForDock($flight, $count, $gamedata);
		} else {
			//Flag the flight removed-from-board. $removedTurn lets replay show
			//the flight up to and including this turn before it disappears.
			$flight->removed = true;
			$flight->removedTurn = $gamedata->turn;
		}

		//Replay note tied to the receiving hangar.
		$note = new IndividualNote(
			-1,
			$gamedata->id,
			$gamedata->turn,
			$gamedata->phase,
			$carrier->id,
			$hangar->id,
			'hangarDockEvent',
			$partial ? 'Hangar partial dock' : 'Hangar received flight',
			$flight->id . ':' . $flight->phpclass . ':' . $count . ($partial ? ':partial' : '')
		);
		Manager::insertIndividualNote($note);

		return $count;
	}

	/* Apply DisengagedFighter critical to $count active Fighter subsystems of
	 * $flight, in fighter-id order. The crit is flagged updated so it persists
	 * via getUpdatedCriticals/submitCriticals at the end of FireGamePhase.
	 */
	private static function disengageFightersForDock($flight, $count, $gamedata){
		$applied = 0;
		foreach ($flight->systems as $fighter) {
			if ($applied >= $count) break;
			if (!($fighter instanceof Fighter)) continue;
			if ($fighter->isDestroyed($gamedata->turn)) continue;   //already disengaged or destroyed
			$crit = new DisengagedFighter(-1, $flight->id, $fighter->id, "DisengagedFighter", $gamedata->turn);
			$crit->updated = true;
			$fighter->criticals[] = $crit;
			$applied++;
		}
		return $applied;
	}

	/* End-of-turn: walks $hangar->pendingDockOrders, validates each, and lands
	 * eligible flights. Failed entries are dropped with a 'hangarDockEvent'
	 * note recording the reason — players see this in replay.
	 *
	 * Pending orders are pre-attached to a single hangar; cross-hangar splits
	 * are encoded as separate orders on each receiving hangar by the client.
	 */
	public static function processDockOrders($hangar, $carrier, $gamedata){
		if (empty($hangar->pendingDockOrder)) return;
		if (!self::isFlowEnabled($gamedata->id)) {
			$hangar->pendingDockOrder = null;
			return;
		}

		foreach ($hangar->pendingDockOrder as $entry) {
			$flightId = isset($entry['flightId']) ? (int)$entry['flightId'] : 0;
			$count    = isset($entry['count'])    ? (int)$entry['count']    : 0;
			//$gamedata->ships is numerically-indexed when loaded from DB; only
			//newly-spawned ships (Manager::insertSingleShip) are keyed by id.
			//Always go through getShipById for correctness.
			$flight   = ($flightId > 0) ? $gamedata->getShipById($flightId) : null;
			$reason   = null;
			if (!$flight || !self::canShipReceive($hangar, $carrier, $flight, $count, $gamedata, $reason)) {
				$failNote = new IndividualNote(
					-1,
					$gamedata->id,
					$gamedata->turn,
					$gamedata->phase,
					$carrier->id,
					$hangar->id,
					'hangarDockEvent',
					'Hangar dock failed',
					'fail:' . $flightId . ':' . $count . ':' . ($reason ?? 'unknown')
				);
				Manager::insertIndividualNote($failNote);
				continue;
			}
			self::performLand($hangar, $carrier, $flight, $count, $gamedata);
		}

		$hangar->pendingDockOrder = null;
	}

}
