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
	 *   1. Auto-fills declared shuttle/minesweeping-shuttle slots with shuttle records.
	 *   2. Auto-fills any leftover hangar capacity with shuttle records
	 *      (MinesweepingShuttle if $ship->minesweeperbonus > 0, else Shuttle).
	 *
	 * Deployment-phase docking (Stage 7) is handled via a different path
	 * (Hangar::pendingDeployStartTransfer -> HangarOps::processDeployStartTransfer),
	 * not from here.
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

		//Stage 16: catapults are Hangars structurally, but their (multiple)
		//boxes are HP only — they hold ONE superheavy fighter and contribute
		//NOTHING to the default-shuttle pool. Partition them out so the shuttle
		//auto-fill (steps 1 & 2) and the leftover-capacity math below see only
		//real hangar boxes. Catapults themselves start EMPTY at turn 1: the
		//superheavy fighter is a combat unit that auto-deploys to space via the
		//fleet-builder flow (like any medium/heavy fighter), then gets recovered
		//into the catapult in-game (Stage 16.4). $shuttleHangars also matches the
		//PRE-Stage-16 behaviour, where catapults weren't `instanceof Hangar` at
		//all and so were never part of collectHangars / the shuttle pool.
		$hasCatapult   = false;
		$shuttleHangars = array();
		foreach ($hangars as $h){
			if (!empty($h->isCatapult)) { $hasCatapult = true; continue; }
			$shuttleHangars[] = $h;
		}

		//Step 1: explicit shuttle/minesweeping-shuttle declarations get auto-filled
		//(combat fighter declarations are NOT auto-filled here — those auto-deploy
		// to space at turn 1 via the existing fleet-builder flow.)
		$totalDeclared = 0;
		if (is_array($ship->fighters)) {
			foreach ($ship->fighters as $category => $declaredCount){
				//Stage 16: superheavy fighters live in the catapult, not the
				//shuttle-pool hangars — don't count them toward $totalDeclared
				//(which would shrink the leftover shuttle fill) and never try to
				//auto-fill them. Only special-cased when the ship actually has a
				//catapult, so catapult-less ships behave exactly as before.
				if ($hasCatapult && strtolower(trim((string)$category)) === 'superheavy') continue;

				$count = (int)$declaredCount;
				$totalDeclared += $count;
				$shuttleClass = self::shuttlePhpclassForCategory($category, $ship);
				if ($shuttleClass === null) continue;   //not a shuttle category

				$shuttleName = self::shuttleDisplayNameFor($shuttleClass);

				while ($count > 0){
					$hangar = self::pickHangarForShuttle($shuttleHangars, 1);
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

		//Step 2: leftover capacity → fill with shuttles. Minesweeper carriers get
		//MinesweepingShuttle (faction-agnostic for now); everyone else gets the
		//faction-appropriate shuttle subclass (Flyer for Minbari, Shuttle otherwise).
		//
		//Carriers with HANG_MSW enhancement: re-type that many of the auto-populated
		//default shuttles to MinesweepingShuttle (gated server-side to non-minesweeper
		//carriers, where it would otherwise be a no-op). Default shuttle CAPACITY is
		//unchanged — MinesweepingShuttle still counts as a default shuttle for
		//fleet-check purposes.
		$totalCapacity = 0;
		foreach ($shuttleHangars as $h) $totalCapacity += (int)$h->maxhealth;   //Stage 16: catapult boxes excluded from the shuttle pool

		//Account for HANG_BP enhancement before computing leftover. Enhancements::setEnhancementsShip
		//mutates $ship->fighters["Breaching Pods"] but runs AFTER onIndividualNotesLoaded at game
		//load (TacGamedata::onConstructed fires after ship/note hydration), so we read the
		//enhancement count directly here. Without this, leftover hangar capacity would auto-fill
		//with shuttles instead of leaving the converted slots open for bought Breaching Pods.
		$hangBpExtraDeclared = 0;
		if (isset($ship->enhancementOptions) && is_array($ship->enhancementOptions)) {
			foreach ($ship->enhancementOptions as $opt) {
				if (($opt[0] ?? '') === 'HANG_BP' && (int)($opt[2] ?? 0) > 0) {
					$hangBpExtraDeclared = (int)$opt[2];
					break;
				}
			}
		}

		$leftover = $totalCapacity - $totalDeclared - $hangBpExtraDeclared;
		if ($leftover > 0){
			$baseClass = (isset($ship->minesweeperbonus) && $ship->minesweeperbonus > 0)
				? 'MinesweepingShuttle'
				: self::factionShuttleClass($ship);
			$baseCategory = ($baseClass === 'MinesweepingShuttle') ? 'minesweeping shuttles' : 'shuttles';
			$baseName = self::shuttleDisplayNameFor($baseClass);

			//HANG_MSW retype count — only meaningful when the carrier's default pool
			//is NOT already MinesweepingShuttle. Capped at the leftover total.
			$hangMswRetype = 0;
			if ($baseClass !== 'MinesweepingShuttle' && isset($ship->enhancementOptions) && is_array($ship->enhancementOptions)) {
				foreach ($ship->enhancementOptions as $opt) {
					if (($opt[0] ?? '') === 'HANG_MSW' && (int)($opt[2] ?? 0) > 0) {
						$hangMswRetype = min((int)$opt[2], $leftover);
						break;
					}
				}
			}

			//Place MinesweepingShuttle records first (one per box, so flightSize=1)
			$mswRemaining = $hangMswRetype;
			while ($mswRemaining > 0){
				$hangar = self::pickHangarForShuttle($shuttleHangars, 1);
				if (!$hangar) break;
				$free = (int)$hangar->maxhealth - self::usageCountFor($hangar);
				$take = min($mswRemaining, $free);
				$hangar->hangarUsage[] = array(
					'phpclass'    => 'MinesweepingShuttle',
					'name'        => self::shuttleDisplayNameFor('MinesweepingShuttle'),
					'flightSize'  => $take,
					'hangarType'  => 'minesweeping shuttles',
				);
				$mswRemaining -= $take;
			}

			$leftoverClass = $baseClass;
			$leftoverCategory = $baseCategory;
			$leftoverName = $baseName;

			$count = $leftover - $hangMswRetype;
			while ($count > 0){
				$hangar = self::pickHangarForShuttle($shuttleHangars, 1);
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
	 *
	 * Passing $ship lets the 'shuttles' category resolve to a faction-specific
	 * subclass (e.g. Flyer for Minbari) via factionShuttleClass(). Omitting
	 * $ship — or passing one with no faction match — yields the generic
	 * 'Shuttle' class. 'minesweeping shuttles' is faction-agnostic for now.
	 */
	public static function shuttlePhpclassForCategory($category, $ship = null){
		$normalized = strtolower(trim((string)$category));
		switch ($normalized) {
			case 'shuttles':
				return self::factionShuttleClass($ship);
			case 'minesweeping shuttles':
				return 'MinesweepingShuttle';
			default:
				return null;
		}
	}

	/* Single source of truth for faction-specific default shuttle classes.
	 * Maps a ship faction → phpclass of its non-minesweeping default shuttle.
	 * Anything not listed falls through to the generic 'Shuttle' class.
	 *
	 * Consumed by:
	 *   - factionShuttleClass($ship)        — per-ship lookup at populate time
	 *   - shuttleClassForFactionName($name) — by-faction lookup at blueprint
	 *     preload time (game.php), so we only preload Flyer when a Minbari
	 *     carrier is actually present in the current game, not every game.
	 *
	 * Extend this map when adding a new faction default shuttle subclass;
	 * no other server-side wiring is required.
	 */
	private static $factionShuttleMap = array(
		'Abbai Matriarchate'  => 'ShuttleAbbai',
		'Abbai Matriarchate (WotCR)'  => 'ShuttleAbbai',
		'Alacan Republic'  => 'ShuttleAlacan',
		'Balosian Underdwellers'  => 'ShuttleBalosian',
		'Belt Alliance'  => 'ShuttleBelt',								
		'Brakiri Syndicracy'  => 'ShuttleBrakiri',
		'Cascor Commonwealth'  => 'ShuttleCascor',								
		'Centauri Republic'  => 'ShuttleCent',
		'Centauri Republic (WotCR)'  => 'ShuttleCentWotCR', 
		'Corillani Theocracy'  => 'ShuttleCorillani',
		'Deneth Tribes'  => 'ShuttleDeneth',
		'Descari Committees'  => 'ShuttleDescari',						
		'Dilgar Imperium'  => 'ShuttleDilgar',
		'Drazi Freehold'  => 'ShuttleDrazi',
		'Drazi Freehold (WotCR)'  => 'ShuttleDraziWotCR',		
		'Earth Alliance'  => 'ShuttleEA',
		'Earth Alliance (Early)'  => 'ShuttleEA',	
		'Gaim Intelligence'  => 'ShuttleGaim',	
		'Grome Autocracy'  => 'ShuttleGrome',		
		'Hurr Republic'  => 'ShuttleHurr',
		'Hyach Gerontocracy'  => 'ShuttleHyach',
		'Kor-Lyan Kingdoms'  => 'ShuttleKL',
		'Llort'  => 'ShuttleLlort',	
		'Markab Theocracy'  => 'ShuttleMarkab',																		
		'Minbari Federation'   => 'Flyer',
		'Minbari Protectorate' => 'FlyerProtectorate',
		'Narn Regime'  => 'ShuttleNarn',
		'Orieni Imperium'  => 'ShuttleOrieni',
		"Pak'ma'ra Confederacy"  => 'ShuttlePakMaRa',
		'Rogolon Dynasty'  => 'ShuttleRogolon',		
		'Torata Regency'  => 'ShuttleTorata',
		'Usuuth Coalition'  => 'ShuttleUsuuth',	
		'Vorlon Empire'  => 'ShuttleVorlons',									
		'Vree Conglomerate'  => 'ShuttleVree',	
		'Yolu Confederation'  => 'ShuttleYolu',								

	);

	/* Resolves the right shuttle phpclass for a carrier based on faction.
	 * Used by both shuttlePhpclassForCategory (for explicit 'shuttles' slots)
	 * and by populateInitialHangarUsage's leftover-capacity step. Minesweeping
	 * shuttles bypass this and stay 'MinesweepingShuttle' regardless of faction.
	 */
	public static function factionShuttleClass($ship){
		if (!$ship || !isset($ship->faction)) return 'Shuttle';
		if (isset(self::$factionShuttleMap[$ship->faction])) return self::$factionShuttleMap[$ship->faction];
		return 'Shuttle';
	}

	/* Faction-name variant of factionShuttleClass, for callers that don't have
	 * a ship instance (e.g. the blueprint preload pass in game.php).
	 * Returns null when the faction has no specific class — the generic 'Shuttle'
	 * is always part of Hangar's preload defaults, so a null return means
	 * "nothing extra needed beyond the generic". */
	public static function shuttleClassForFactionName($faction){
		if (!is_string($faction) || $faction === '') return null;
		return isset(self::$factionShuttleMap[$faction]) ? self::$factionShuttleMap[$faction] : null;
	}

	/* True when $phpclass is one of the auto-fill "default" shuttle classes —
	 * the generic Shuttle, every faction variant (ShuttleEA, ShuttleNarn,
	 * Flyer, FlyerProtectorate, ...), and MinesweepingShuttle. They all
	 * extend Shuttle (see source/server/model/ships/Shuttle.php), whereas
	 * armed-shuttle classes (e.g. genericArmedShuttle, drakhShuttle) extend
	 * FighterFlight directly. Stage 18 uses this to exclude free auto-fill
	 * shuttles from the carrier-destruction escape pool while still letting
	 * armed shuttles / Assault Shuttles / Breaching Pods escape normally.
	 */
	public static function isDefaultShuttleClass($phpclass){
		if (!is_string($phpclass) || $phpclass === '') return false;
		if ($phpclass === 'Shuttle') return true;
		if (!class_exists($phpclass)) return false;
		return is_subclass_of($phpclass, 'Shuttle');
	}

	/* Display name for a shuttle phpclass — used in hangar tooltip aggregation. */
	public static function shuttleDisplayNameFor($phpclass){
		switch ($phpclass) {
			case 'MinesweepingShuttle':
				return 'Minesweeping Shuttle';
			case 'Flyer':
				return 'Flyer';
			case 'FlyerProtectorate':
				return 'Flyer';				
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

	/* All Hangar systems on a given ship, in encounter order (primary, front, etc.) */
	public static function collectHangars($ship){
		$hangars = array();
		if (!isset($ship->systems) || !is_array($ship->systems)) return $hangars;
		foreach ($ship->systems as $sys){
			if ($sys instanceof Hangar) $hangars[] = $sys;
		}
		return $hangars;
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
		//Catapults are fixed at 'superheavy' by design — never narrow them from
		//the ship's $fighters declaration (Stage 16).
		if (!empty($hangar->isCatapult)) return;
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

	/* Server-side mirror of getDefaultShuttles() in client systems.js. Default
	 * shuttles aren't usually declared in $ship->fighters — they auto-fill any
	 * hangar capacity not claimed by other categories (see populateInitialHangarUsage
	 * step 2). Returns the leftover count, the matching $ship->fighters slot key
	 * ('shuttles' or 'minesweeping shuttles' when the carrier has minesweeperbonus),
	 * and a display label. Used by Enhancements to gate Shuttle-slot conversions
	 * on the actual auto-populated pool.
	 */
	public static function getDefaultShuttles($ship){
		//Stage 16: catapult boxes are HP only and never join the default-shuttle
		//pool, and the catapult-destined 'superheavy' fighters don't consume real
		//hangar boxes. Mirror populateInitialHangarUsage's exclusions so the
		//Enhancements (HANG_BP/HANG_MSW) gating sees the right pool size.
		$capacity = 0;
		$hasCatapult = false;
		foreach (self::collectHangars($ship) as $h) {
			if (!empty($h->isCatapult)) { $hasCatapult = true; continue; }
			$capacity += (int)$h->maxhealth;
		}
		if ($capacity <= 0) {
			return array('count' => 0, 'type' => 'Shuttles', 'key' => 'shuttles');
		}
		$declared = 0;
		if (isset($ship->fighters) && is_array($ship->fighters)) {
			foreach ($ship->fighters as $category => $count) {
				if ($hasCatapult && strtolower(trim((string)$category)) === 'superheavy') continue;
				$declared += (int)$count;
			}
		}
		$leftover = $capacity - $declared;
		if ($leftover < 0) $leftover = 0;
		$minesweeper = !empty($ship->minesweeperbonus) && (int)$ship->minesweeperbonus > 0;
		if ($minesweeper) {
			return array('count' => $leftover, 'type' => 'Minesweeping Shuttles', 'key' => 'minesweeping shuttles');
		}
		return array('count' => $leftover, 'type' => 'Shuttles', 'key' => 'shuttles');
	}

	/* Evict $count craft from a hangar (1 box of damage = 1 craft destroyed).
	 * Priority: shuttles first (cheapest, most fungible), then anything else
	 * in stored order. For partial-flight evictions (e.g. damage=2 from a
	 * stored 6-fighter flight), the record's $flightSize is reduced rather
	 * than the whole record dropped.
	 *
	 * $gamedata is REQUIRED: when an evicted entry has dockedFlightId, the
	 * same number of active Fighter subsystems are disengaged in the source
	 * flight so its rendered combat value drops to match — and so a fully-
	 * evicted entry leaves the flight hidden via the all-disengaged
	 * isDestroyed fold (see Hangar::onIndividualNotesLoaded) rather than
	 * resurfacing as a ghost row once its dockedFlightId entry compacts away.
	 * Dropping a dockedFlightId entry WITHOUT disengaging would reintroduce
	 * that ghost, so the param is mandatory, not optional.
	 */
	public static function evictCraftFromHangar($hangar, $count, $gamedata){
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
			$entry = $hangar->hangarUsage[$idx];
			$available = (int)($entry['flightSize'] ?? 1);
			$take = min($available, $count - $evicted);
			if (isset($entry['dockedFlightId']) && $take > 0) {
				$flight = $gamedata->getShipById((int)$entry['dockedFlightId']);
				if ($flight instanceof FighterFlight) {
					self::disengageFighters($flight, $take, $gamedata);
				}
			}
			$hangar->hangarUsage[$idx]['flightSize'] = $available - $take;
			$evicted += $take;
		}

		//Compact away records that were fully evicted
		$hangar->hangarUsage = array_values(array_filter($hangar->hangarUsage, function($e){
			return (int)($e['flightSize'] ?? 0) > 0;
		}));

		return $evicted;
	}

	/* Lower priority value = drop first.
	 * Order: empty slots (0), shuttles + faction shuttle variants (10),
	 * MinesweepingShuttle (20), fighters at 1000 + pointCost so cheapest
	 * fighters evict before expensive ones.
	 */
	public static function evictionPriorityFor($entry){
		$phpclass = $entry['phpclass'] ?? '';
		if ($phpclass === '') return 0;
		if ($phpclass === 'MinesweepingShuttle') return 20;
		if (stripos($phpclass, 'shuttle') !== false) return 10;
		if ($phpclass === 'Flyer' || $phpclass === 'FlyerProtectorate') return 10;
		if (class_exists($phpclass)) {
			try {
				$probe = new $phpclass(0, 0, '', 0);
				return 1000 + (int)($probe->pointCost ?? 0);
			} catch (Exception $e) {}
		}
		return 1000;
	}

	/* End-of-turn hook for a single Hangar — drop stored craft to fit
	 * remaining capacity, and reset per-turn launch/land counters.
	 * Called from Hangar::criticalPhaseEffects.
	 *
	 * $ship/$gamedata are REQUIRED. Eviction of a dockedFlightId stash record
	 * must disengage the matching fighters in the source flight, both to keep
	 * its rendered combat value in sync and so a fully-evicted entry leaves
	 * the flight hidden via the all-disengaged isDestroyed fold rather than
	 * resurfacing as a ghost row. The sole caller (Hangar::criticalPhaseEffects)
	 * always has both in hand.
	 */
	public static function onHangarCriticalPhase($hangar, $ship, $gamedata){
		//Reset shared launch+land budget for next turn
		$hangar->launchedThisTurn = 0;
		$hangar->landedThisTurn = 0;

		//Stage 18: defer everything for a destroyed carrier — the Pass 3 sweep
		//(processCarrierDestructionEscapes) owns the destroyed-carrier path. If
		//we let the total-loss disengage branch fire here, it would disengage
		//every dockedFlightId-linked source flight BEFORE Pass 3 had a chance
		//to mark some as escapees. (Normally an already-destroyed carrier isn't
		//in $activeShips and this hook isn't called for it; this guard covers
		//the cascade case where a ship in $activeShips at Pass 1 start gets
		//destroyed mid-Pass-1 by, e.g., ConnectionStrut, and still has its
		//Pass 2 hooks run on the original snapshot.)
		if ($ship->isDestroyed()) return 0;

		if ($hangar->isDestroyed()){
			//Total hangar loss: any dockedFlightId-linked craft also die.
			//Disengage every fighter in their source flights before clearing.
			if (!empty($hangar->hangarUsage)) {
				foreach ($hangar->hangarUsage as $entry) {
					if (!isset($entry['dockedFlightId'])) continue;
					$flight = $gamedata->getShipById((int)$entry['dockedFlightId']);
					if ($flight instanceof FighterFlight) {
						self::disengageFighters($flight, PHP_INT_MAX, $gamedata);
					}
				}
			}
			$hangar->hangarUsage = array();
			return 0;
		}

		$remaining = $hangar->getRemainingHealth();
		$stored = self::usageCountFor($hangar);
		if ($stored <= $remaining) return 0;

		return self::evictCraftFromHangar($hangar, $stored - $remaining, $gamedata);
	}

	/* Per-turn servicing of flights sitting docked in this hangar. Runs from
	 * Hangar::criticalPhaseEffects every turn. Walks the hangar's dockedFlightId
	 * stash entries, resolves each flight, and calls whileDocked() on every
	 * subsystem of every still-active fighter so reloadable weapons (SlugCannon
	 * et al.) can top up ammo. Docked flights are excluded from
	 * Criticals::setCriticals' $activeShips, so they cannot self-tick — the
	 * live carrier's hangar is the only thing that processes them each turn.
	 *
	 * A flight must be docked a FULL turn before it is serviced: an entry whose
	 * dockedTurn is the current turn (it just docked, or a fragment created this
	 * turn by a partial-launch split) is skipped. A destroyed hangar services
	 * nothing — and a destroyed carrier never reaches here, since it's dropped
	 * from $activeShips, so docked craft on a dead carrier neither rearm nor tick.
	 */
	public static function serviceDockedFlights($hangar, $carrier, $gamedata){
		if ($hangar->isDestroyed() || empty($hangar->hangarUsage)) return;
		foreach ($hangar->hangarUsage as $entry){
			if (!isset($entry['dockedFlightId'])) continue;
			//Must have been docked since BEFORE this turn (no rearm on dock turn).
			if (isset($entry['dockedTurn']) && (int)$entry['dockedTurn'] >= $gamedata->turn) continue;
			$flight = $gamedata->getShipById((int)$entry['dockedFlightId']);
			if (!($flight instanceof FighterFlight)) continue;
			foreach ($flight->systems as $fighter){
				if (!($fighter instanceof Fighter)) continue;
				if ($fighter->isDestroyed($gamedata->turn)) continue;
				if (!isset($fighter->systems) || !is_array($fighter->systems)) continue;
				foreach ($fighter->systems as $sys){
					$sys->whileDocked($flight, $carrier, $hangar, $gamedata);
				}
			}
		}
	}

	/* === Stage 15: ordnance reload pool =================================== */

	/* Total purchased reload-pool capacity for $carrier (read from the
	 * HANG_ORD lobby enhancement). Re-derived on every load; the spent
	 * portion persists separately via hangarOrdReserve notes. Returns 0
	 * for carriers that didn't buy any. */
	public static function reloadPoolCapacity($carrier){
		if (!isset($carrier->enhancementOptions) || !is_array($carrier->enhancementOptions)) return 0;
		foreach ($carrier->enhancementOptions as $opt){
			if (($opt[0] ?? '') === 'HANG_ORD') return max(0, (int)($opt[2] ?? 0));
		}
		return 0;
	}

	/* Total reload points spent across this carrier's lifetime. Read from
	 * the primary (first) hangar's $reloadPoolSpent field, which is
	 * persisted via the hangarOrdReserve note in generateIndividualNotes. */
	public static function reloadPoolSpent($carrier){
		$primary = self::primaryHangar($carrier);
		if (!$primary) return 0;
		return max(0, (int)$primary->reloadPoolSpent);
	}

	public static function reloadPoolRemaining($carrier){
		return max(0, self::reloadPoolCapacity($carrier) - self::reloadPoolSpent($carrier));
	}

	/* Try to draw $cost reload points from $carrier's pool. Returns true on
	 * success (with the primary hangar's $reloadPoolSpent incremented), false
	 * if not enough headroom. The mutation persists via the primary hangar's
	 * generateIndividualNotes which writes a hangarOrdReserve note when the
	 * counter changes — same change-detection pattern hangarUsage uses. */
	public static function drawReload($carrier, $cost){
		$cost = (int)$cost;
		if ($cost <= 0) return true;
		if (self::reloadPoolRemaining($carrier) < $cost) return false;
		$primary = self::primaryHangar($carrier);
		if (!$primary) return false;
		$primary->reloadPoolSpent = (int)$primary->reloadPoolSpent + $cost;
		return true;
	}

	/* The single hangar on $carrier responsible for holding the
	 * hangarOrdReserve note. By convention this is the first Hangar in
	 * encounter order — same one used by populateInitialHangarUsage. */
	public static function primaryHangar($carrier){
		foreach (self::collectHangars($carrier) as $h) return $h;   //first
		return null;
	}

	/* === Stage 17 ext: marine contingents pool ============================ */

	/* Total purchased marine-pool capacity for $carrier (read from the
	 * MAR_CONT lobby enhancement). Each point = 1 marine unit. Re-derived on
	 * every load; spent portion persists separately via hangarMarineReserve
	 * notes. Returns 0 for carriers that didn't buy any. */
	public static function marinePoolCapacity($carrier){
		if (!isset($carrier->enhancementOptions) || !is_array($carrier->enhancementOptions)) return 0;
		foreach ($carrier->enhancementOptions as $opt){
			if (($opt[0] ?? '') === 'MAR_CONT') return max(0, (int)($opt[2] ?? 0));
		}
		return 0;
	}

	/* Total marine pool points spent across this carrier's lifetime. Read
	 * from the primary hangar's $marinePoolSpent (persisted via the
	 * hangarMarineReserve note in generateIndividualNotes). */
	public static function marinePoolSpent($carrier){
		$primary = self::primaryHangar($carrier);
		if (!$primary) return 0;
		return max(0, (int)$primary->marinePoolSpent);
	}

	public static function marinePoolRemaining($carrier){
		return max(0, self::marinePoolCapacity($carrier) - self::marinePoolSpent($carrier));
	}

	/* Try to draw $cost marine-pool points from $carrier's pool. Returns
	 * true on success (with the primary hangar's $marinePoolSpent
	 * incremented), false if not enough headroom or no primary hangar
	 * exists. Mirrors drawReload exactly. */
	public static function drawMarineReload($carrier, $cost){
		$cost = (int)$cost;
		if ($cost <= 0) return true;
		if (self::marinePoolRemaining($carrier) < $cost) return false;
		$primary = self::primaryHangar($carrier);
		if (!$primary) return false;
		$primary->marinePoolSpent = (int)$primary->marinePoolSpent + $cost;
		return true;
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

			//Stage 16: a catapult launch carries NO initiative penalty (neither the
			//-50 LaunchedThisTurn on the flight nor the -20 HangarOperations on the
			//carrier). Ordinary hangar launches still apply both.
			if (empty($hangar->isCatapult)) self::applyLaunchCrits($resurrectedFlight, $carrier, $gamedata);
			return $resurrectedFlight->id;
		}

		//Build the new flight. Constructor populates 1 fighter by default;
		//bump flightSize and re-populate so we get the requested size. The
		//autoid sequence is deterministic (1, 2, ...) so the same call at
		//load time will reproduce the same ids.
		//
		//If this launch is splitting off from a dockedFlightId-linked stash
		//entry (resurrect rejected on size mismatch), name the new flight
		//after the source with a " - Split" suffix so the player can trace
		//where the detachment came from. Falls back to the generic name
		//(phpclass / shipClass) for launches from anonymous orphans or
		//auto-filled shuttle stash.
		$flightName = self::splitFlightNameFor($hangar, $phpclass)
			?? self::flightNameFor($phpclass, $carrier);
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

		//Stage 10.5: walk the stash entries this launch consumes; for every
		//dockedFlightId-linked entry, transfer the Fighter-level damage and
		//crits from the OLD fragment's priority-selected fighters onto the
		//freshly-spawned launched flight, then either fully destroy the OLD
		//fragment (full extract) or spawn a NEW fragment carrying the
		//remaining fighters' state (partial extract). Anonymous orphan /
		//auto-shuttle entries are simply consumed — they never had damage
		//history to transfer. Replaces the legacy syncSourceFlightsOnLaunch
		//+ removeFromHangarUsage pair which marked source fighters
		//DockedFighter and left ghost rows on a subsequent relaunch.
		self::consumeStashesForLaunch($hangar, $phpclass, $launchSize, $flight, $carrier, $gamedata);

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

		//Stage 16: catapult launches carry no initiative penalty (see resurrect path).
		if (empty($hangar->isCatapult)) self::applyLaunchCrits($flight, $carrier, $gamedata);
		return $shipid;
	}

	/* Apply initiative-penalty criticals after a successful launch:
	 * - LaunchedThisTurn (-50 ini) on the new flight's first fighter
	 * - HangarOperations (-20 ini) on the carrier's CnC (via shared helper)
	 */
	private static function applyLaunchCrits($flight, $carrier, $gamedata){
		if ($flight instanceof FighterFlight) {
			$firstFighter = $flight->getSampleFighter();
			if ($firstFighter) {
				$crit = new LaunchedThisTurn(-1, $flight->id, $firstFighter->id, 'LaunchedThisTurn', $gamedata->turn, $gamedata->turn + 1);
				$crit->updated = true;
				$firstFighter->criticals[] = $crit;
			}
		}
		self::applyHangarOperationsCrit($carrier, $gamedata);
	}

	/* Apply HangarOperations (-20 ini) to the carrier's CnC. Idempotent within
	 * a single turn: launching multiple flights AND/OR docking craft on the
	 * same turn produces exactly one critical, so the carrier eats -20, not
	 * -40+, regardless of how many launch/dock orders resolved.
	 */
	private static function applyHangarOperationsCrit($carrier, $gamedata){
		$cnc = $carrier->getSystemByName('CnC');
		if (!$cnc) return;
		foreach ($cnc->criticals as $c) {
			if ($c->phpclass === 'HangarOperations' && $c->turn === $gamedata->turn) {
				return; //already applied this turn
			}
		}
		$crit = new HangarOperations(-1, $carrier->id, $cnc->id, 'HangarOperations', $gamedata->turn, $gamedata->turn + 1);
		$crit->updated = true;
		$cnc->criticals[] = $crit;
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
			if (!empty($entry['cannotLaunch'])) continue;   //Stage 16.5: wrecked-on-landing — never relaunch
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

	/* If $hangar holds a dockedFlightId-linked stash entry matching $phpclass,
	 * return that source flight's name with a " - Split" suffix so the new
	 * flight is clearly a detachment of the original. Returns null when no
	 * df-linked entry of this phpclass exists (auto-filled shuttles, anonymous
	 * orphans, etc.) — caller should fall back to the generic flightNameFor.
	 *
	 * Picks the first df-linked entry of matching phpclass; if multiple docked
	 * flights of the same type exist on one hangar they'll all share the same
	 * detachment label, which is a minor cosmetic ambiguity but unlikely in
	 * practice (a carrier almost never docks two distinct same-class flights).
	 */
	public static function splitFlightNameFor($hangar, $phpclass){
		if (empty($hangar->hangarUsage)) return null;
		foreach ($hangar->hangarUsage as $entry) {
			if (($entry['phpclass'] ?? '') !== $phpclass) continue;
			if (!isset($entry['dockedFlightId'])) continue;
			$base = isset($entry['name']) ? (string)$entry['name'] : '';
			if ($base === '') return null;
			return $base . ' - Split';
		}
		return null;
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

	/* Hangar Operations is now generally available — the safeGameID gate
	 * (Stages 4–7) was lifted in Stage 9. Kept as a stub so existing call
	 * sites need no rewrite; future re-gating (e.g. a per-feature flag in
	 * varconfig.php) can be plumbed back through here.
	 */
	public static function isFlowEnabled($gameid){
		return true;
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
		//Stage 16: a catapult launches its fighter regardless of any damage it has
		//sustained (even when destroyed), so the destroyed-hangar gate is skipped.
		$isCatapult = !empty($hangar->isCatapult);
		if (!$isCatapult && $hangar->isDestroyed()) { $reason = 'hangar destroyed'; return false; }

		//Carrier may not launch on a turn it pivoted or rolled
		if (Movement::isPivoting($carrier, $gamedata->turn, $gamedata) || Movement::isRolling($carrier, $gamedata->turn, $gamedata)) {
			$reason = 'carrier pivoting/rolling';
			return false;
		}

		//Shared launch+land budget vs hangar output. Catapults have no output
		//budget (no initiative penalty, one fighter) — skip the gate for them.
		if (!$isCatapult) {
			$used = (int)$hangar->launchedThisTurn + (int)$hangar->landedThisTurn;
			if ($used + $size > (int)$hangar->output) { $reason = 'launch rate exceeded'; return false; }
		}

		//Enough stored craft of this class. Stage 16.5: a stash entry flagged
		//cannotLaunch (a fighter destroyed while landing on a damaged catapult)
		//is a permanent wreck — it occupies the bay but contributes nothing to
		//launchable craft.
		$available = 0;
		$blockedByWreck = false;
		if (is_array($hangar->hangarUsage)) {
			foreach ($hangar->hangarUsage as $entry) {
				if (($entry['phpclass'] ?? '') !== $phpclass) continue;
				if (!empty($entry['cannotLaunch'])) { $blockedByWreck = true; continue; }
				$available += (int)($entry['flightSize'] ?? 1);
			}
		}
		if ($available < $size) {
			$reason = $blockedByWreck ? 'craft destroyed on landing — cannot relaunch' : 'not enough stored craft';
			return false;
		}

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
		//Stage 16: a catapult recovers its fighter regardless of any damage it has
		//sustained (even destroyed), so the destroyed-hangar gate is skipped for it.
		$isCatapult = !empty($hangar->isCatapult);
		if (!$isCatapult && $hangar->isDestroyed()) { $reason = 'hangar destroyed'; return false; }
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

		//Heading. Ordinary hangars require the flight to match the carrier's
		//heading (same velocity vector). A catapult (Stage 16) recovers a fighter
		//only when it enters the carrier's hex from the REAR — i.e. the flight is
		//travelling in the direction the carrier points (flight heading == carrier
		//facing), so it overtakes the carrier from behind onto the launch rail.
		if ($isCatapult) {
			if ((int)$flightMove->heading !== (int)$carrierMove->facing) {
				$reason = 'must approach catapult from rear'; return false;
			}
		} else {
			if ((int)$carrierMove->heading !== (int)$flightMove->heading) {
				$reason = 'heading mismatch'; return false;
			}
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
		//Pass $carrier so universal hangars derive their permissions from
		//the ship's $fighters declaration (Decurion-style multi-category).
		$category = self::trueSizeOf($flight);
		$free = self::freeBoxesByCategory($hangar, $category, $carrier);
		if ($free < $count) { $reason = 'hangar full'; return false; }

		//Shared launch+land budget vs hangar output. Catapults have no budget
		//(one fighter, no initiative cost) — skip the gate for them.
		if (!$isCatapult) {
			$used = (int)$hangar->launchedThisTurn + (int)$hangar->landedThisTurn;
			if ($used + $count > (int)$hangar->output) { $reason = 'land rate exceeded'; return false; }
		}

		//Stage 10.6.2: per-ship customFighter cap. Thunderbolt-named flights
		//can only dock into carriers that declare $customFighter['Thunderbolt']
		//(and only up to that cap). Non-custom flights get PHP_INT_MAX so the
		//gate is a no-op for them.
		$customName = isset($flight->customFtrName) ? (string)$flight->customFtrName : '';
		if ($customName !== '') {
			$remaining = self::customFighterRemaining($carrier, $customName);
			if ($remaining < $count) { $reason = 'customFighter cap exceeded'; return false; }
		}

		return true;
	}

	/* Free boxes in $hangar that can hold $category. A 'fighters' (universal)
	 * hangar derives its permissions from the carrier $ship's $fighters
	 * declaration when provided (so multi-category ships like Decurion / Falenna
	 * apply the right gate). Other hangars only accept their declared type, with
	 * shuttles allowed everywhere per §10.1 and BPs in AS / medium+ per rules.
	 */
	public static function freeBoxesByCategory($hangar, $category, $ship = null){
		if (!self::hangarAcceptsCategory($hangar, $category, $ship)) return 0;
		$max = self::effectiveCapacity($hangar);
		return max(0, $max - self::usageCountFor($hangar));
	}

	/* Effective storage capacity in "craft slots". A Catapult holds exactly ONE
	 * fighter no matter how many (structural) boxes it has — its extra boxes are
	 * HP only — and it operates regardless of damage, so its capacity is a flat 1.
	 * Ordinary hangars use remaining (undamaged) health. (Stage 16.) */
	public static function effectiveCapacity($hangar){
		if (!empty($hangar->isCatapult)) return 1;
		return (int)$hangar->getRemainingHealth();
	}

	public static function hangarAcceptsCategory($hangar, $category, $ship = null){
		$hType = strtolower(trim((string)$hangar->hangarType));
		$cat   = strtolower(trim((string)$category));
		if ($hType === '' || $cat === '') return false;

		static $sizeRank = array(
			'ultralight' => 1,
			'light'      => 2,
			'medium'     => 3,
			'heavy'      => 4,
		);

		//Exact match (medium-medium, 'Breaching Pods'-'Breaching Pods', 'Raiders'-'Raiders', …)
		if ($hType === $cat) return true;

		//Combat-fighter size hierarchy: a slot accepts its declared size or smaller.
		//Mirrors checkChoices() in gamelobby.js, where heavy hangars also count
		//toward medium/light/ultralight capacity, medium hangars toward light/ultralight,
		//etc. — i.e. larger slots are strictly more permissive than smaller ones.
		if (isset($sizeRank[$hType]) && isset($sizeRank[$cat])) {
			return $sizeRank[$cat] <= $sizeRank[$hType];
		}

		//Shuttles & minesweeping shuttles can use any combat-fighter slot per B5W §10.1.
		if (($cat === 'shuttles' || $cat === 'minesweeping shuttles') && isset($sizeRank[$hType])) {
			return true;
		}

		//Breaching Pods (per rules): docked in dedicated BP slot, OR Assault
		//Shuttle slot, OR ANY combat fighter slot (heavy/medium/light/ultralight).
		if ($cat === 'breaching pods') {
			if ($hType === 'assault shuttles') return true;
			if (isset($sizeRank[$hType])) return true;
		}

		//Universal fighter slot: when ship context is available, derive the
		//slot's permissions from the ship's $fighters declaration (since
		//inferHangarType can't always narrow multi-category ships like Decurion
		//(AS+BP) or Falenna (heavy+AS)). Without ship context, fall back to
		//the conservative "combat fighters + shuttles only" set.
		if ($hType === 'fighters' || $hType === 'normal') {
			if ($cat === 'shuttles' || $cat === 'minesweeping shuttles') return true;

			if (!$ship || !is_array($ship->fighters)) {
				//No ship context: combat fighters allowed, AS/BPs/custom rejected.
				if (isset($sizeRank[$cat])) return true;
				return false;
			}

			$declared = array_change_key_case($ship->fighters, CASE_LOWER);

			if (isset($sizeRank[$cat])) {
				//Combat fighter: ship must declare some combat-fighter capacity
				//(heavy/medium/light/ultralight/normal). Apply size hierarchy so
				//a medium fighter is OK if the ship has heavy or normal slots.
				if (!empty($declared['normal'])) return true;
				foreach (array('heavy', 'medium', 'light', 'ultralight') as $size) {
					if (empty($declared[$size])) continue;
					if ($sizeRank[$cat] <= $sizeRank[$size]) return true;
				}
				return false;
			}

			if ($cat === 'assault shuttles') {
				return !empty($declared['assault shuttles']);
			}

			if ($cat === 'breaching pods') {
				//Already handled above for typed slots; this is the universal
				//case. BP allowed if ship declares BP, AS, or ANY combat fighter
				//capacity (heavy/medium/light/ultralight/normal).
				if (!empty($declared['breaching pods'])) return true;
				if (!empty($declared['assault shuttles'])) return true;
				if (!empty($declared['normal'])) return true;
				if (!empty($declared['heavy'])) return true;
				if (!empty($declared['medium'])) return true;
				if (!empty($declared['light'])) return true;
				if (!empty($declared['ultralight'])) return true;
				return false;
			}

			//Other custom names need explicit exact-match (typed hangar) or a
			//customFighter declaration (gated separately by Stage 10.6.2).
			return false;
		}

		return false;
	}

	/* Stage 10.6.2: per-ship customFighter cap remaining for $name on $carrier.
	 *
	 * A flight with $customFtrName != '' (e.g. Thunderbolt, Rutarian, Ok-chn)
	 * must dock into a carrier whose $customFighter[$name] declaration covers
	 * the count being docked. The cap is shared across all hangars on the
	 * carrier — Omega's 24 Thunderbolt cap applies to ALL its hangars, not
	 * 24 per hangar.
	 *
	 * Returns:
	 *  - PHP_INT_MAX if $name === '' (no gate to enforce — non-custom flight).
	 *  - 0 if the carrier doesn't declare $customFighter[$name].
	 *  - declared - stored otherwise, where "stored" is the sum of $flightSize
	 *    across every hangarUsage entry on every hangar of $carrier whose
	 *    stamped $customFtrName matches $name.
	 *
	 * The fleet-builder check (gamelobby.js checkChoices) is FLEET-WIDE — it
	 * sums $customFighter across all carriers vs total $customFtrName flights.
	 * This helper is per-CARRIER and enforced at dock time so individual ships
	 * don't accept flights they aren't equipped for.
	 */
	public static function customFighterRemaining($carrier, $name){
		if ($name === '' || $name === null) return PHP_INT_MAX;
		if (!is_array($carrier->customFighter) || empty($carrier->customFighter)) return 0;
		if (!isset($carrier->customFighter[$name])) return 0;
		$declared = (int)$carrier->customFighter[$name];

		$used = 0;
		foreach (self::collectHangars($carrier) as $h){
			if (!is_array($h->hangarUsage)) continue;
			foreach ($h->hangarUsage as $entry){
				if (!isset($entry['customFtrName'])) continue;
				if ($entry['customFtrName'] !== $name) continue;
				$used += (int)($entry['flightSize'] ?? 1);
			}
		}
		return max(0, $declared - $used);
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

		//Exact match first. Catapults (hangarType 'superheavy') land here for a
		//superheavy flight; they ignore their own damage and have no output budget.
		foreach ($hangars as $h){
			if ($h->hangarType !== $category) continue;
			$isCat = !empty($h->isCatapult);
			if (!$isCat && $h->isDestroyed()) continue;
			$free = self::freeBoxesByCategory($h, $category, $carrier);
			if ($isCat) {
				$capacity = $free;   //no launch+land budget for catapults
			} else {
				$budget = max(0, (int)$h->output - ((int)$h->launchedThisTurn + (int)$h->landedThisTurn));
				$capacity = min($free, $budget);
			}
			if ($capacity > 0) $out[] = array('hangar' => $h, 'capacity' => $capacity);
		}
		//Then any hangar that accepts the category via the size hierarchy
		//(larger fighter slots, universal slots, shuttle-compatible slots).
		foreach ($hangars as $h){
			if ($h->hangarType === $category) continue;            //already considered above
			if (!self::hangarAcceptsCategory($h, $category, $carrier)) continue;
			if ($h->isDestroyed()) continue;
			$free = self::freeBoxesByCategory($h, $category, $carrier);
			$budget = max(0, (int)$h->output - ((int)$h->launchedThisTurn + (int)$h->landedThisTurn));
			$capacity = min($free, $budget);
			if ($capacity > 0) $out[] = array('hangar' => $h, 'capacity' => $capacity);
		}

		//Stage 10.6.2: clamp aggregate capacity to the carrier's remaining
		//customFighter cap for this flight's name. The cap is shared across
		//hangars, so walk in fill order and truncate each entry's capacity
		//until the running total hits the cap; drop entries beyond it.
		$customName = isset($flight->customFtrName) ? (string)$flight->customFtrName : '';
		if ($customName !== '') {
			$cap = self::customFighterRemaining($carrier, $customName);
			if ($cap <= 0) return array();
			$running = 0;
			$clamped = array();
			foreach ($out as $entry) {
				if ($running >= $cap) break;
				$take = min((int)$entry['capacity'], $cap - $running);
				if ($take <= 0) continue;
				$entry['capacity'] = $take;
				$clamped[] = $entry;
				$running += $take;
			}
			return $clamped;
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
		//Stage 10.6.2: stamp the customFtrName (if any) so per-ship cap accounting
		//via customFighterRemaining() can find this entry without re-instantiating
		//the phpclass. Auto-filled shuttles and non-custom flights have '' and
		//are silently ignored by the cap helper.
		if (!empty($flight->customFtrName)) {
			$entry['customFtrName'] = $flight->customFtrName;
		}

		if ($partial) {
			//Stage 10.3: priority-ordered dockFighters returns the actual
			//Fighter objects chosen (most-damaged first, back-of-array tiebreak).
			$chosenFighters = self::dockFighters($flight, $count, $gamedata);

			//Stage 10.4: spawn a fragment FighterFlight holding the chosen
			//fighters' damage and crit state, link the stash entry to it via
			//dockedFlightId. Relaunch goes through the existing
			//resurrectDockedFlight path which brings the fragment back with
			//all its preserved state. Pre-10.4 partial docks dropped damage
			//state entirely on relaunch — fresh fighters spawned undamaged.
			$fragment = self::spawnFragmentFlight($flight, $chosenFighters, $carrier, $hangar, $gamedata);
			if ($fragment) {
				$entry['dockedFlightId'] = $fragment->id;
				$entry['name']           = $fragment->name;     //"$sourceName - Detachment"
			}
		} else {
			//Full dock: the source flight IS the docked unit. Stash entry
			//links to its existing ship row directly; resurrectDockedFlight
			//brings it back as-is with all damage/crits already on its
			//system rows.
			$entry['dockedFlightId'] = $flight->id;
			$flight->removed = true;
			$flight->removedTurn = $gamedata->turn;
		}

		//Stage 16.5: landing on a DAMAGED catapult deals damage to the recovered
		//fighter equal to the catapult's destroyed-box count (NOT total accumulated
		//damage points). The craft still counts as recovered (the stash entry is
		//written normally below), but if the landing damage destroys it the entry
		//is flagged cannotLaunch — the wreck permanently occupies the catapult and
		//can never launch again. A catapult only ever full-docks a single fighter
		//(capacity 1, so $count == activeCount, never partial), so this targets
		//$flight — which IS the stored craft via dockedFlightId. The !$partial
		//guard keeps the damage off the wrong ship in the (rules-impossible)
		//multi-craft case, where the stored unit would be a fragment, not $flight.
		if (!empty($hangar->isCatapult) && !$partial) {
			$markedBoxes = max(0, (int)$hangar->maxhealth - (int)$hangar->getRemainingHealth());
			if ($markedBoxes > 0 && self::applyCatapultLandingDamage($flight, $markedBoxes, $gamedata)) {
				$entry['cannotLaunch'] = true;
			}
		}

		$hangar->hangarUsage[] = $entry;
		$hangar->landedThisTurn += $count;

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

		self::applyHangarOperationsCrit($carrier, $gamedata);

		return $count;
	}

	/* Stage 16.5: deal $markedBoxes damage to every active fighter being recovered
	 * onto a damaged catapult. Damage is written with armour 0 — the destroyed-box
	 * metric is compared directly against the fighter's remaining health, per the
	 * confirmed rule ("remaining health <= marked boxes -> destroyed"). The
	 * DamageEntry is flagged updated (and destroyed, when fatal) so it persists via
	 * FireGamePhase::advance's submitDamages call and shows on the replay timeline,
	 * mirroring the Stage 10.4 persisted-damage pattern.
	 *
	 * A catapult holds exactly one fighter, so in practice this damages the single
	 * recovered craft; the loop is defensive against a >1 flight. Returns true when
	 * EVERY recovered craft is destroyed by the landing damage (caller flags the
	 * stash entry cannotLaunch so the wreck can never relaunch).
	 */
	private static function applyCatapultLandingDamage($flight, $markedBoxes, $gamedata){
		if (!($flight instanceof FighterFlight) || $markedBoxes <= 0) return false;
		$damagedAny   = false;
		$allDestroyed = true;
		foreach ($flight->systems as $fighter) {
			if (!($fighter instanceof Fighter)) continue;
			if ($fighter->isDestroyed($gamedata->turn)) continue;   //already gone — not a recovered craft
			$remaining = (int)$fighter->getRemainingHealth();
			$destroyed = ($markedBoxes >= $remaining);
			$dmg = new DamageEntry(
				-1, $flight->id, -1, $gamedata->turn, $fighter->id,
				(int)$markedBoxes, 0, 0, -1, $destroyed, false, "", "CatapultLanding"
			);
			$dmg->updated = true;
			$fighter->damage[] = $dmg;
			$damagedAny = true;
			if (!$destroyed) $allDestroyed = false;
		}
		return $damagedAny && $allDestroyed;
	}

	/* === Stage 7: deployment-phase dock ================================== */

	/* Resolve queued deployment-phase dock orders for a hangar.
	 *
	 * Unlike Fire-Phase docks (resolved at end-of-turn via criticalPhaseEffects),
	 * deploy-start docks are resolved immediately during DeploymentGamePhase::process
	 * — generateIndividualNotes invokes this so the resulting $hangarUsage
	 * mutation is included in the same hangarUsage snapshot the loop is about
	 * to write. The flight ship row is committed in the same request, so by
	 * the next request load the docked flight will be hidden via the existing
	 * dockedFlightId → $removed restoration in Hangar::onIndividualNotesLoaded.
	 *
	 * Failed entries are silently dropped with a hangarDeployStartEvent fail note.
	 */
	public static function processDeployStartTransfer($hangar, $carrier, $gamedata){
		if (empty($hangar->pendingDeployStartTransfer)) {
			$hangar->pendingDeployStartTransfer = null;
			return;
		}

		foreach ($hangar->pendingDeployStartTransfer as $entry) {
			$flightId = isset($entry['flightId']) ? (int)$entry['flightId'] : 0;
			$flight   = ($flightId > 0) ? $gamedata->getShipById($flightId) : null;
			$reason   = null;
			if (!$flight || !self::canDeployStartDock($hangar, $carrier, $flight, $gamedata, $reason)) {
				$failNote = new IndividualNote(
					-1,
					$gamedata->id,
					$gamedata->turn,
					$gamedata->phase,
					$carrier->id,
					$hangar->id,
					'hangarDeployStartEvent',
					'Hangar deploy-start dock failed',
					'fail:' . $flightId . ':' . ($reason ?? 'unknown')
				);
				Manager::insertIndividualNote($failNote);
				continue;
			}
			self::performDeployStartDock($hangar, $carrier, $flight, $gamedata);
		}

		$hangar->pendingDeployStartTransfer = null;   //consumed
	}

	/* Returns true if $flight can be docked into $hangar on $carrier during
	 * Deployment Phase. Lighter rules than canShipReceive (no same-hex/heading
	 * check — flight isn't placed yet, carrier may or may not be):
	 *   - Both ships belong to the same player slot
	 *   - Flight is a FighterFlight that isn't already removed/destroyed
	 *   - Flight AND carrier are both deploying THIS turn — a reinforcement
	 *     flight cannot dock into a previously-deployed carrier
	 *   - Carrier hangar isn't destroyed and has compatible free boxes for the
	 *     flight's full size
	 */
	public static function canDeployStartDock($hangar, $carrier, $flight, $gamedata, &$reason = null){
		if (!$flight instanceof FighterFlight) { $reason = 'not a flight'; return false; }
		if ($flight->removed || $flight->isDestroyed()) { $reason = 'flight already removed'; return false; }
		if ($hangar->isDestroyed()) { $reason = 'hangar destroyed'; return false; }
		if ($carrier->isDestroyed() || $carrier->removed) { $reason = 'carrier not in play'; return false; }

		if ((int)$flight->slot !== (int)$carrier->slot) { $reason = 'slot mismatch'; return false; }
		if ((int)$flight->userid !== (int)$carrier->userid) { $reason = 'owner mismatch'; return false; }

		if ($flight->getTurnDeployed($gamedata) != $gamedata->turn) {
			$reason = 'flight not deploying this turn'; return false;
		}
		//Carrier must ALSO be deploying this turn — fighters arriving on
		//turn N can only dock into ships also arriving on turn N. Previously
		//-deployed carriers are off-limits to late reinforcements.
		if ($carrier->getTurnDeployed($gamedata) != $gamedata->turn) {
			$reason = 'carrier not deploying this turn'; return false;
		}

		//Whole flight goes into the hangar — partial deploy-dock isn't a thing
		//(the player hasn't placed any of the flight yet).
		$size = (int)$flight->flightSize;
		if ($size <= 0) { $reason = 'flight has no craft'; return false; }

		$category = self::trueSizeOf($flight);
		$free = self::freeBoxesByCategory($hangar, $category, $carrier);
		if ($free < $size) { $reason = 'hangar full'; return false; }

		//Stage 10.6.2: per-ship customFighter cap.
		$customName = isset($flight->customFtrName) ? (string)$flight->customFtrName : '';
		if ($customName !== '') {
			$remaining = self::customFighterRemaining($carrier, $customName);
			if ($remaining < $size) { $reason = 'customFighter cap exceeded'; return false; }
		}

		return true;
	}

	/* Move $flight from "deploying to map" into $hangar's storage.
	 * Mirrors performLand's full-dock branch — stash entry carries
	 * dockedFlightId so a later launch (Stage 4) can resurrect this ship row.
	 *
	 * Mutates: $hangar->hangarUsage, $flight->removed/$flight->removedTurn.
	 * Writes a hangarDeployStartEvent note for replay/audit.
	 */
	public static function performDeployStartDock($hangar, $carrier, $flight, $gamedata){
		$count = (int)$flight->flightSize;
		$category = self::trueSizeOf($flight);

		$entry = array(
			'phpclass'       => $flight->phpclass,
			'name'           => $flight->name,
			'flightSize'     => $count,
			'hangarType'     => $category,
			'dockedTurn'     => $gamedata->turn,
			'dockedFlightId' => $flight->id,
		);
		//Stage 10.6.2: stamp customFtrName for per-ship cap accounting.
		if (!empty($flight->customFtrName)) {
			$entry['customFtrName'] = $flight->customFtrName;
		}
		$hangar->hangarUsage[] = $entry;

		//Flag removed-from-board. Loaders re-apply this via dockedFlightId, but
		//setting it here keeps the current request's downstream consumers (e.g.
		//the movement-insertion loop in DeploymentGamePhase) aware that the
		//flight isn't on the board.
		$flight->removed = true;
		$flight->removedTurn = $gamedata->turn;

		$note = new IndividualNote(
			-1,
			$gamedata->id,
			$gamedata->turn,
			$gamedata->phase,
			$carrier->id,
			$hangar->id,
			'hangarDeployStartEvent',
			'Hangar received flight (deployment)',
			$flight->id . ':' . $flight->phpclass . ':' . $count
		);
		Manager::insertIndividualNote($note);
	}

	/* Collect flight IDs queued for deployment-phase dock across every hangar
	 * on every ship in $ships. Used by DeploymentGamePhase::validateDeployment
	 * to exempt docked flights from the "must have a deploy MovementOrder"
	 * check — they don't get placed on the board.
	 */
	public static function collectQueuedDeployStartFlightIds(array $ships){
		$ids = array();
		foreach ($ships as $ship) {
			if (!isset($ship->systems) || !is_array($ship->systems)) continue;
			foreach ($ship->systems as $sys) {
				if (!($sys instanceof Hangar)) continue;
				if (empty($sys->pendingDeployStartTransfer)) continue;
				foreach ($sys->pendingDeployStartTransfer as $entry) {
					if (isset($entry['flightId'])) $ids[(int)$entry['flightId']] = true;
				}
			}
		}
		return $ids;
	}

	/* Apply $critClass critical to $count active Fighter subsystems of
	 * $flight. The crit is flagged updated so it persists via
	 * getUpdatedCriticals/submitCriticals at the end of FireGamePhase.
	 * Shared between the dock / launch-split / hangar-loss paths; the thin
	 * wrappers below pick the right crit class.
	 *
	 * Stage 10.3 priority order:
	 *   1. Most damage first (getTotalDamage DESC) — players save hurt
	 *      craft preferentially when partial-docking. The same priority is
	 *      applied to the hangar-damage disengage path so worst-off craft
	 *      die first (which matches the "they were already battered"
	 *      reading of stash eviction; if the rules-lawyer view is "random
	 *      losses," swap to shuffle here without touching callers).
	 *   2. Back-of-array tiebreak (highest array index first) so the
	 *      visual front of the flight stays intact for continuing combat
	 *      operations when only some craft can dock.
	 *
	 * Returns the list of chosen Fighter objects (ordered by application).
	 * Stage 10.4 uses the returned list to copy the same fighters' damage
	 * and crit history onto a freshly-spawned fragment FighterFlight so
	 * partial-dock damage persists through the dock/relaunch cycle.
	 */
	private static function applyFighterStateCritical($flight, $count, $critClass, $gamedata){
		if ($count <= 0) return array();

		$candidates = array();
		foreach ($flight->systems as $idx => $fighter) {
			if (!($fighter instanceof Fighter)) continue;
			if ($fighter->isDestroyed($gamedata->turn)) continue;   //already disengaged/docked/destroyed
			$candidates[] = array(
				'fighter' => $fighter,
				'idx'     => $idx,
				'damage'  => (int)$fighter->getTotalDamage(),
			);
		}

		usort($candidates, function($a, $b){
			if ($a['damage'] !== $b['damage']) return $b['damage'] - $a['damage'];   //damage DESC
			return $b['idx'] - $a['idx'];                                            //index DESC
		});

		$chosen = array();
		foreach ($candidates as $c) {
			if (count($chosen) >= $count) break;
			$fighter = $c['fighter'];
			$crit = new $critClass(-1, $flight->id, $fighter->id, $critClass, $gamedata->turn);
			$crit->updated = true;
			$fighter->criticals[] = $crit;
			$chosen[] = $fighter;
		}
		return $chosen;
	}

	/* Dock $count active fighters in $flight (intentionally entering a
	 * hangar — partial-dock keeps the rest in space; partial-launch leaves
	 * these behind as the dock-side stash). Renders as cyan "DOCKED" in
	 * the flight window and is distinguishable from combat disengagement
	 * in the replay audit trail.
	 *
	 * Returns the list of chosen Fighter objects (Stage 10.3 priority order).
	 */
	private static function dockFighters($flight, $count, $gamedata){
		return self::applyFighterStateCritical($flight, $count, 'DockedFighter', $gamedata);
	}

	/* Disengage $count active fighters in $flight (combat disengagement,
	 * or — currently — fighters killed by hangar damage when the carrier
	 * is hit). Renders as green "DISENGAGED" in the flight window. Hangar-
	 * damage callers may eventually want real destruction instead, but
	 * disengagement preserves the existing replay shape.
	 *
	 * Returns the list of chosen Fighter objects (Stage 10.3 priority order).
	 */
	private static function disengageFighters($flight, $count, $gamedata){
		return self::applyFighterStateCritical($flight, $count, 'DisengagedFighter', $gamedata);
	}

	/* === Stage 10.4: partial-dock damage preservation =================== */

	/* Spawn a fragment FighterFlight at partial-dock time so the docked
	 * fighters' damage and crit history can be preserved across the dock /
	 * relaunch cycle via the existing dockedFlightId resurrect mechanism.
	 *
	 * Before Stage 10.4, partial-dock stash entries had no dockedFlightId so
	 * relaunch fell through to the new-spawn path, producing a fresh flight
	 * with no damage history — a 3-of-6 partial dock of a battered Sentinel
	 * flight relaunched as pristine Sentinels. With this helper, partial-dock
	 * stash entries link to a fragment that holds the chosen fighters'
	 * state, and resurrectDockedFlight treats them identically to full-dock
	 * stash entries.
	 *
	 * The fragment is born $removed=true at the dock turn so it never appears
	 * on the board between spawn and relaunch (or game end). resurrectDockedFlight
	 * clears $removed and inserts a fresh deploy MovementOrder when the
	 * player relaunches at the matching exact size.
	 */
	private static function spawnFragmentFlight($sourceFlight, $chosenFighters, $carrier, $hangar, $gamedata){
		$phpclass = $sourceFlight->phpclass;
		$count = count($chosenFighters);
		if ($count <= 0 || !is_string($phpclass) || $phpclass === '') return null;
		if (!class_exists($phpclass)) return null;

		//Dedup the suffix so chained partial-extracts (Stage 10.5) don't end
		//up as "Sentinel-1 - Detachment - Detachment". Single suffix is enough
		//to tell the player a fragment is a partial; further splits inherit
		//the same name.
		$fragmentName = $sourceFlight->name;
		if (strpos($fragmentName, ' - Split') === false) {
			$fragmentName .= ' - Split';
		}
		$fragment = new $phpclass($gamedata->id, $sourceFlight->userid, $fragmentName, $sourceFlight->slot);
		$fragment->team = $sourceFlight->team;

		//Constructor populates the default flightSize (typically 1). Bump to
		//$count and re-populate so the fragment has exactly the right number
		//of Fighter subsystems. Mirrors performLaunch's new-spawn pattern.
		if ($count > $fragment->flightSize) {
			$fragment->flightSize = $count;
			if (method_exists($fragment, 'populate')) {
				$fragment->populate();
			}
		}

		//Persist ship row and capture the new id.
		$shipid = Manager::insertSingleShip($gamedata, $fragment, $sourceFlight->userid);
		$fragment->id = $shipid;
		$fragment->spawned = $gamedata->turn;

		//Persist flight size so getFlightSize() repopulates correctly on reload.
		Manager::insertSingleFlightSize($gamedata->id, $shipid, $fragment->flightSize);

		//Insert a deploy MovementOrder at the carrier's current hex so any
		//consumer that reads the fragment's position between dock and
		//relaunch (replay scrub, getTurnDeployed checks) gets a sensible
		//answer. The fragment is $removed so it never renders on the board;
		//the movement entry is purely for ship-state consistency.
		$lastMove = $carrier->getLastMovement();
		if ($lastMove) {
			$deployMove = new MovementOrder(
				null, "deploy", $lastMove->position, 0, 0,
				(int)$lastMove->speed, (int)$lastMove->heading, (int)$lastMove->facing,
				false, $gamedata->turn, 0, 0
			);
			Manager::insertSingleMovement($gamedata->id, $shipid, $deployMove);
		}

		//Initialize per-system data so weapons aren't uncharged on relaunch
		//(mirrors performLaunch's new-spawn path). The damage state will be
		//layered on by copyFighterStateToTarget below; setInitialSystemData
		//doesn't write damage records.
		SystemData::initSystemData($gamedata->turn, $gamedata->id);
		foreach ($fragment->systems as $craft) {
			$craft->setInitialSystemData($fragment);
			if (!isset($craft->systems) || !is_array($craft->systems)) continue;
			foreach ($craft->systems as $sys) {
				$sys->setInitialSystemData($fragment);
				if ($sys instanceof Weapon) {
					$load = $sys->getStartLoading();
					if ($load) {
						$load->loading = $sys->loadingtime;
						SystemData::addDataForSystem($sys->id, 0, $shipid, $load->toJSON());
					}
				}
			}
		}
		Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

		//Stage 15: carry the source flight's AMMO_F* enhancements onto the
		//fragment so its magazines have modes registered + base load applied
		//on next-turn relaunch. copyFighterStateToTarget below writes
		//per-magazine balancing notes for the actual current state.
		self::copyFlightAmmoEnhancements($sourceFlight, $fragment, $gamedata);

		//Copy damage and crits from each chosen source fighter onto the
		//corresponding fragment fighter. Pairing is BY ORDER: chosenFighters[i]
		//→ fragmentFighters[i]. Both flights are constructed from the same
		//phpclass so the structures are identical (same fighter count, same
		//subsystem layout per fighter, same id-by-index assignment within
		//FighterFlight->systems). System ids within a Fighter are not
		//set explicitly — Fighter-level damage is what matters for dropout
		//rolls and for the player's "this fighter is hurt" view, and that's
		//all we preserve.
		$fragmentFighters = array();
		foreach ($fragment->systems as $f) {
			if ($f instanceof Fighter) $fragmentFighters[] = $f;
		}
		$copies = min(count($chosenFighters), count($fragmentFighters));
		for ($i = 0; $i < $copies; $i++) {
			self::copyFighterStateToTarget($chosenFighters[$i], $fragmentFighters[$i], $fragment, $gamedata);
		}

		//Mark removed so the fragment is hidden from board / target lists.
		//The dockedFlightId stash entry's resurrectDockedFlight clears these
		//flags on relaunch. Hangar::onIndividualNotesLoaded also re-applies
		//$removed at load time by walking hangarUsage entries with
		//dockedFlightId, so the state survives reload between dock and
		//relaunch.
		$fragment->removed = true;
		$fragment->removedTurn = $gamedata->turn;

		return $fragment;
	}

	/* Copy a source fighter's damage history, damage-related criticals, and
	 * weapon ammo onto a target fighter (newly-spawned, no prior history). State
	 * markers used by the dock pipeline itself — DockedFighter,
	 * DisengagedFighter, LaunchedThisTurn — are intentionally NOT copied:
	 * the target is born "clean" of those flight-control crits and its
	 * parent's $removed flag (or own life status) handles in-play state.
	 *
	 * Used by:
	 *   - spawnFragmentFlight (Stage 10.4) — copying from source-flight
	 *     fighters to the dock-time fragment's fighters.
	 *   - consumeStashesForLaunch (Stage 10.5) — copying from an old
	 *     fragment's fighters to the freshly-spawned launched flight's
	 *     fighters (and to the new mini-fragment's fighters for partial
	 *     extracts).
	 *
	 * Damage entries are created with $updated=true so they persist via
	 * FireGamePhase::advance's submitDamages call; crits with $newCrit=true
	 * so submitCriticals inserts them even though their turn matches the
	 * current turn.
	 */
	private static function copyFighterStateToTarget($sourceFighter, $targetFighter, $targetFlight, $gamedata){
		foreach ($sourceFighter->damage as $src) {
			$newDmg = new DamageEntry(
				-1,
				$targetFlight->id,
				$src->gameid,
				$src->turn,
				$targetFighter->id,
				$src->damage,
				$src->armour,
				$src->shields,
				$src->fireorderid,
				$src->destroyed,
				$src->undestroyed,
				$src->pubnotes,
				$src->damageclass,
				$src->shooterid,
				$src->weaponid
			);
			$newDmg->updated = true;
			$targetFighter->damage[] = $newDmg;
		}

		static $skipPhpclasses = array(
			'DisengagedFighter' => true,    //combat-disengage state marker, source-only
			'DockedFighter'     => true,    //dock state marker, source-only
			'LaunchedThisTurn'  => true,    //initiative penalty, turn-specific event
		);
		foreach ($sourceFighter->criticals as $crit) {
			if (isset($skipPhpclasses[$crit->phpclass])) continue;
			$critClass = $crit->phpclass;
			if (!class_exists($critClass)) continue;
			$newCrit = new $critClass(
				-1,
				$targetFlight->id,
				$targetFighter->id,
				$critClass,
				(int)$crit->turn,
				(int)$crit->turnend
			);
			$newCrit->updated = true;
			$newCrit->newCrit = true;       //force DB insert
			$targetFighter->criticals[] = $newCrit;
		}

		//Copy weapon ammo so a split / partial relaunch reflects the docked
		//flight's depleted-and-rearmed ammo rather than a fresh full clip.
		//(The new-spawn launch path inits weapons to a full default load; this
		//runs afterwards and overrides it for dockedFlightId-linked launches.)
		//Subsystems pair by order — source and target share a phpclass, so the
		//layout is identical. Only scalar-ammo weapons (SlugCannon family) carry
		//over here; AmmoMagazine state is handled by the Stage 15 block below.
		//Persisted via the same updateAmmoInfo path fire()/whileDocked use; the
		//target is a runtime-spawned flight whose enhancementOptions were just
		//copied by copyFlightAmmoEnhancements, so there is no EXT_AMMO surprise
		//to strip before saving.
		$srcSubs = is_array($sourceFighter->systems) ? array_values($sourceFighter->systems) : array();
		$tgtSubs = is_array($targetFighter->systems) ? array_values($targetFighter->systems) : array();
		$pairs = min(count($srcSubs), count($tgtSubs));
		for ($s = 0; $s < $pairs; $s++) {
			$srcSub = $srcSubs[$s];
			$tgtSub = $tgtSubs[$s];
			if (!($tgtSub instanceof Weapon)) continue;
			if (!isset($srcSub->ammunition) || !isset($tgtSub->ammunition)) continue;
			$tgtSub->ammunition = $srcSub->ammunition;
			Manager::updateAmmoInfo($targetFlight->id, $tgtSub->id, $gamedata->id, $tgtSub->firingMode, $tgtSub->ammunition, $gamedata->turn);
		}

		//Stage 17: copy FighterMissileRack-class missileArray amounts. The
		//new-spawn path constructs racks with $missileArray[$mode]->amount = 0
		//(the legacy default), so without this a partial-relaunch from a
		//restocked-while-docked fragment would emerge with empty racks.
		//Persisted via the same updateAmmoInfo path fire() and whileDocked use,
		//mode-by-mode in case a ship file ever wires multi-mode racks (the
		//common case is a single mode 1 — fire() and updateAmmoInfo both treat
		//mode 1 specially, but we walk the array for full coverage).
		for ($s = 0; $s < $pairs; $s++) {
			if (!($srcSubs[$s] instanceof FighterMissileRack)) continue;
			if (!($tgtSubs[$s] instanceof FighterMissileRack)) continue;
			$srcRack = $srcSubs[$s];
			$tgtRack = $tgtSubs[$s];
			if (!is_array($srcRack->missileArray) || !is_array($tgtRack->missileArray)) continue;
			foreach ($srcRack->missileArray as $mode => $srcAmmo) {
				if (!$srcAmmo) continue;
				if (!isset($tgtRack->missileArray[$mode])) continue;
				$tgtAmmo = $tgtRack->missileArray[$mode];
				if (!$tgtAmmo) continue;
				$tgtAmmo->amount = (int)$srcAmmo->amount;
				Manager::updateAmmoInfo(
					$targetFlight->id,
					$tgtRack->id,
					$gamedata->id,
					$tgtRack->firingMode,
					$tgtAmmo->amount,
					$gamedata->turn
				);
			}
		}

		//Stage 15: balance target magazines to match source's CURRENT effective
		//missile count per mode. copyFlightAmmoEnhancements (called once per
		//flight pair before this loop) added the enhancement count to target's
		//magazines; per-mode delta = source_current - target_current. Positive
		//delta means source has MORE than the as-purchased baseline (impossible
		//absent restocks — write AmmoReplenished notes); negative means source
		//has spent some — write AmmoUsed notes. Pairing by subsystem order is
		//valid since source and target share phpclass / populate() layout.
		for ($s = 0; $s < $pairs; $s++) {
			if (!($srcSubs[$s] instanceof AmmoMagazine)) continue;
			if (!($tgtSubs[$s] instanceof AmmoMagazine)) continue;
			$srcMag = $srcSubs[$s];
			$tgtMag = $tgtSubs[$s];
			if (!is_array($srcMag->ammoCountArray)) continue;
			foreach ($srcMag->ammoCountArray as $modeName => $srcCount) {
				$srcCount = (int)$srcCount;
				$tgtCount = (int)($tgtMag->ammoCountArray[$modeName] ?? 0);
				$delta = $srcCount - $tgtCount;
				if ($delta === 0) continue;
				if ($delta > 0) {
					$noteKey   = 'AmmoReplenished';
					$noteHuman = 'Ammunition Magazine - a round restocked';
					$count     = $delta;
				} else {
					$noteKey   = 'AmmoUsed';
					$noteHuman = 'Ammunition Magazine - a round is drawn';
					$count     = -$delta;
				}
				for ($i = 0; $i < $count; $i++) {
					$note = new IndividualNote(
						-1,
						$gamedata->id,
						$gamedata->turn,
						$gamedata->phase,
						$targetFlight->id,
						$tgtMag->id,
						$noteKey,
						$noteHuman,
						$modeName
					);
					Manager::insertIndividualNote($note);
				}
				if (!array_key_exists($modeName, $tgtMag->ammoCountArray)) {
					$tgtMag->ammoCountArray[$modeName] = 0;
				}
				$tgtMag->ammoCountArray[$modeName] += $delta;
			}
		}
	}

	/* === Stage 10.5: partial-launch-from-fragment damage transfer ======== */

	/* Replaces the legacy syncSourceFlightsOnLaunch + removeFromHangarUsage
	 * pair for performLaunch's new-spawn path. Walks every stash entry the
	 * launch consumes; for dockedFlightId-linked entries, transfers Fighter-
	 * level damage and crits from the OLD fragment's priority-selected
	 * fighters onto the newly-spawned launched flight, then either fully
	 * destroys the OLD fragment (full extract) or spawns a NEW fragment
	 * carrying the remaining fighters' state (partial extract).
	 *
	 * Before Stage 10.5, the legacy path just marked source fighters
	 * DockedFighter and reduced the stash flightSize — partial-launch
	 * lost the launched fighters' damage AND left ghost DOCKED rows in
	 * the fragment for the next relaunch's flight window.
	 *
	 * For non-fragment stash entries (anonymous orphans, auto-shuttles),
	 * just consume the count — those entries never had damage history to
	 * transfer, so the launched flight's corresponding fighter slots stay
	 * fresh-spawned.
	 *
	 * Mutates $hangar->hangarUsage in place. Caller increments
	 * $hangar->launchedThisTurn afterwards.
	 */
	private static function consumeStashesForLaunch($hangar, $phpclass, $launchCount, $launchedFlight, $carrier, $gamedata){
		if ($launchCount <= 0 || empty($hangar->hangarUsage)) return;

		//Collect launched flight's Fighter subsystems in order — copy targets.
		$launchedFighters = array();
		foreach ($launchedFlight->systems as $f) {
			if ($f instanceof Fighter) $launchedFighters[] = $f;
		}
		$cursor = 0;

		$remaining = $launchCount;
		$newEntries = array();
		$ammoCopiedFromFlightId = 0;   //Stage 15: copy AMMO_F* once per launchedFlight

		foreach ($hangar->hangarUsage as $entry) {
			//Stage 16.5: a cannotLaunch wreck is never consumed by a launch — keep
			//it in place so it continues to occupy the catapult.
			if ($remaining <= 0 || ($entry['phpclass'] ?? '') !== $phpclass || !empty($entry['cannotLaunch'])) {
				$newEntries[] = $entry;
				continue;
			}

			$entrySize = (int)($entry['flightSize'] ?? 1);
			$take = min($entrySize, $remaining);
			$remaining -= $take;

			if (isset($entry['dockedFlightId']) && $take > 0) {
				$fragmentId = (int)$entry['dockedFlightId'];
				$fragment = $gamedata->getShipById($fragmentId);
				if ($fragment instanceof FighterFlight) {
					//Stage 15: copy AMMO_F* enhancements from the (first) fragment
					//to the launched flight ONCE so its magazines have the modes
					//registered + base load applied. Per-fighter balancing notes
					//written by copyFighterStateToTarget below adjust for actual
					//current state (restocked + spent).
					if ($ammoCopiedFromFlightId !== $fragment->id) {
						self::copyFlightAmmoEnhancements($fragment, $launchedFlight, $gamedata);
						$ammoCopiedFromFlightId = $fragment->id;
					}

					//Priority-select $take fighters from fragment (Stage 15:
					//missile-count DESC, then damage DESC, back-of-array tiebreak).
					$chosen = self::selectFightersForExtraction($fragment, $take, $gamedata);

					//Transfer damage/crits onto the next slice of launched
					//flight fighters. Pairing is by ORDER — chosen[i] maps to
					//launchedFighters[cursor + i].
					for ($i = 0; $i < count($chosen); $i++) {
						if (!isset($launchedFighters[$cursor])) break;
						self::copyFighterStateToTarget($chosen[$i], $launchedFighters[$cursor], $launchedFlight, $gamedata);
						$cursor++;
					}

					if ($take < $entrySize) {
						//Partial extract: the remaining (entrySize - take)
						//fighters keep their state via a brand-new fragment.
						$chosenIds = array();
						foreach ($chosen as $f) $chosenIds[$f->id] = true;
						$remainingFighters = array();
						foreach ($fragment->systems as $f) {
							if (!($f instanceof Fighter)) continue;
							if ($f->isDestroyed($gamedata->turn)) continue;
							if (isset($chosenIds[$f->id])) continue;
							$remainingFighters[] = $f;
						}
						$newFragment = self::spawnFragmentFlight($fragment, $remainingFighters, $carrier, $hangar, $gamedata);
						self::destroyAllFighters($fragment, $gamedata);

						$newEntry = $entry;
						$newEntry['flightSize'] = $entrySize - $take;
						if ($newFragment) {
							$newEntry['dockedFlightId'] = $newFragment->id;
							$newEntry['name']           = $newFragment->name;
						}
						$newEntries[] = $newEntry;
					} else {
						//Full extract: OLD fragment fully destroyed, stash
						//entry consumed (omitted from $newEntries).
						self::destroyAllFighters($fragment, $gamedata);
					}
				} else {
					//Fragment ship missing (shouldn't happen) — fall back to
					//the legacy reduce/drop behaviour so the launch isn't
					//completely stuck.
					$cursor += $take;
					if ($take < $entrySize) {
						$newEntry = $entry;
						$newEntry['flightSize'] = $entrySize - $take;
						$newEntries[] = $newEntry;
					}
				}
			} else if ($take > 0) {
				//Anonymous orphan or auto-filled shuttle — no damage state
				//to transfer. Just consume the count; the launched flight's
				//corresponding fighter slots stay fresh.
				$cursor += $take;
				if ($take < $entrySize) {
					$newEntry = $entry;
					$newEntry['flightSize'] = $entrySize - $take;
					$newEntries[] = $newEntry;
				}
			} else {
				$newEntries[] = $entry;
			}
		}

		$hangar->hangarUsage = $newEntries;
	}

	/* Priority-ordered fighter pick WITHOUT applying any crit. Used by
	 * consumeStashesForLaunch when extracting fighters from an OLD
	 * fragment — the OLD fragment's selected fighters are NOT crit'd
	 * directly; instead they're either fully destroyed (via
	 * destroyAllFighters) or their state is copied to a new fragment.
	 *
	 * Priority (Stage 15 update):
	 *   1. Loaded missile count DESC — fighters carrying carrier-pool
	 *      restocks are extracted first so split-launches actually carry
	 *      the restocked ordnance into combat (rather than leaving it in
	 *      the leftover stashed fragment, which is the user-facing rule).
	 *   2. Damage DESC — Stage 10.3 priority preserved as secondary.
	 *   3. Index DESC — back-of-array tiebreak.
	 */
	private static function selectFightersForExtraction($flight, $count, $gamedata){
		if ($count <= 0) return array();

		$candidates = array();
		foreach ($flight->systems as $idx => $fighter) {
			if (!($fighter instanceof Fighter)) continue;
			if ($fighter->isDestroyed($gamedata->turn)) continue;
			$candidates[] = array(
				'fighter' => $fighter,
				'idx'     => $idx,
				'damage'  => (int)$fighter->getTotalDamage(),
				'ammo'    => self::countLoadedMissiles($fighter),
			);
		}

		usort($candidates, function($a, $b){
			if ($a['ammo']   !== $b['ammo'])   return $b['ammo']   - $a['ammo'];     //missiles DESC
			if ($a['damage'] !== $b['damage']) return $b['damage'] - $a['damage'];   //damage DESC
			return $b['idx'] - $a['idx'];                                            //index DESC
		});

		$chosen = array();
		foreach ($candidates as $c) {
			if (count($chosen) >= $count) break;
			$chosen[] = $c['fighter'];
		}
		return $chosen;
	}

	/* Sum of all loaded missiles across a Fighter's AmmoMagazines and any
	 * legacy FighterMissileRack-class launchers (Stage 17). Used by
	 * selectFightersForExtraction to surface "has carrier-pool restocked
	 * missiles" as the primary extraction priority. */
	private static function countLoadedMissiles($fighter){
		if (!isset($fighter->systems) || !is_array($fighter->systems)) return 0;
		$total = 0;
		foreach ($fighter->systems as $sys) {
			if ($sys instanceof AmmoMagazine) {
				if (!is_array($sys->ammoCountArray)) continue;
				foreach ($sys->ammoCountArray as $count) {
					if ($count > 0) $total += (int)$count;
				}
			} elseif ($sys instanceof FighterMissileRack) {
				//Stage 17: each legacy launcher's $missileArray[$mode]->amount
				//is the per-launcher current round count; folding it in lets
				//selectFightersForExtraction prefer restocked legacy-rack
				//fighters at partial-launch time the same way it does for
				//AmmoMagazine fighters.
				if (!is_array($sys->missileArray)) continue;
				foreach ($sys->missileArray as $ammo) {
					if (!$ammo) continue;
					$amt = (int)$ammo->amount;
					if ($amt > 0) $total += $amt;
				}
			}
		}
		return $total;
	}

	/* Stage 15 carry-over: copies the source flight's purchased AMMO_F*
	 * enhancements onto a runtime-spawned target flight (split-launch in
	 * consumeStashesForLaunch, partial-dock/extract fragment in
	 * spawnFragmentFlight). Runtime spawns don't inherit tac_enhancements
	 * rows, so without this the target's magazines load with no modes
	 * registered beyond populate()'s default and setEnhancementsFighter
	 * applies nothing on next-turn load — any AMMO_F* state from the source
	 * (including carrier-pool restocks) would vanish.
	 *
	 * Three side effects, each load-stage critical:
	 *   1. Insert tac_enhancements rows so next-turn getEnhancementsForGame
	 *      rebuilds target->enhancementOptions correctly.
	 *   2. Mirror onto target->enhancementOptions in memory so current-turn
	 *      callers (HangarOps::reloadPoolCapacity, future feature checks)
	 *      see the same shape next-turn load will produce.
	 *   3. Apply the enhancement count to target's magazines in memory so
	 *      current-turn display + fire orders work — mirrors what
	 *      setEnhancementsFighter does at load time.
	 *
	 * copyFighterStateToTarget then writes per-magazine balancing notes so
	 * the rebuilt state reflects the source's *actual current* count
	 * (post-firing, post-restocks), not just the as-purchased baseline.
	 */
	private static function copyFlightAmmoEnhancements($sourceFlight, $targetFlight, $gamedata){
		if (!is_array($sourceFlight->enhancementOptions)) return;
		static $ammoFmap = array(
			'AMMO_FB'  => 'AmmoMissileFB',
			'AMMO_FL'  => 'AmmoMissileFL',
			'AMMO_FH'  => 'AmmoMissileFH',
			'AMMO_FY'  => 'AmmoMissileFY',
			'AMMO_FD'  => 'AmmoMissileFD',
			'AMMO_DUM' => 'AmmoMissileFDum',
		);
		foreach ($sourceFlight->enhancementOptions as $opt){
			$enhID    = (string)($opt[0] ?? '');
			$enhName  = (string)($opt[1] ?? '');
			$enhCount = (int)($opt[2] ?? 0);
			if ($enhCount <= 0)                continue;
			if (!isset($ammoFmap[$enhID]))     continue;
			$cls = $ammoFmap[$enhID];
			if (!class_exists($cls))           continue;

			Manager::insertSingleEnhancement($gamedata, $targetFlight->id, $enhID, $enhCount, $enhName);
			$targetFlight->enhancementOptions[] = array($enhID, $enhName, $enhCount, 0, 0, 0);

			foreach ($targetFlight->systems as $craft) {
				if (!($craft instanceof Fighter)) continue;
				if (!isset($craft->systems) || !is_array($craft->systems)) continue;
				foreach ($craft->systems as $sys) {
					if (!($sys instanceof AmmoMagazine)) continue;
					$sys->addAmmoEntry(new $cls(), $enhCount, true);
				}
			}
		}
	}

	/* Apply DisengagedFighter to every active fighter on $flight. Used by
	 * consumeStashesForLaunch on the OLD fragment after its state has been
	 * transferred elsewhere — once a fragment has been split-or-extracted,
	 * its ship row is effectively dead. FighterFlight::isDestroyed folds
	 * in fighter-disengage state, so the OLD fragment becomes hidden from
	 * the board, target lists, and (per Stage 9 polish) the fleet list.
	 *
	 * Crits are flagged updated + newCrit so submitCriticals inserts them
	 * even on the current turn.
	 */
	private static function destroyAllFighters($flight, $gamedata){
		foreach ($flight->systems as $f) {
			if (!($f instanceof Fighter)) continue;
			if ($f->isDestroyed($gamedata->turn)) continue;
			$crit = new DisengagedFighter(-1, $flight->id, $f->id, 'DisengagedFighter', $gamedata->turn);
			$crit->updated = true;
			$crit->newCrit = true;
			$f->criticals[] = $crit;
		}
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

	/* === Stage 18: hangar craft escape from a destroyed carrier =========== */

	/* Walks $gamedata->ships looking for destroyed carriers that still hold
	 * docked craft, rolls a d20 for each, spawns up to maxEscape craft as
	 * live FighterFlights at the carrier's last hex/heading/(facing +
	 * hangar->direction)/speed, and disengages the non-escapees so they
	 * properly count as destroyed for fleetList / calculateCombatValue.
	 *
	 * Triggered from Criticals::setCriticals as a Pass 3 sweep AFTER the
	 * standard $activeShips passes. The destroyed carrier isn't in
	 * $activeShips so its hangars never see Pass 2's Hangar::criticalPhaseEffects;
	 * Stage 18 owns the destroyed-carrier path end-to-end.
	 *
	 * One-shot per carrier: when the roll fires, a 'hangarEscapeRoll' note is
	 * written on the carrier's primary hangar and Hangar->escapeRolled is
	 * set. Subsequent loads reapply the flag from the note so this pass
	 * skips already-rolled carriers.
	 *
	 * Carriers that successfully jumped to hyperspace are excluded — the
	 * existing fleetList.getJumpedDockedFlightIds path preserves their
	 * docked flights' combat value verbatim (matches the user spec). */
	public static function processCarrierDestructionEscapes($gamedata){
		//Snapshot the ship list — performEscapeSpawns mutates $gamedata->ships
		//via Manager::insertSingleShip when spawning escape fragments / new flights.
		$ships = array();
		foreach ($gamedata->ships as $s) $ships[] = $s;

		foreach ($ships as $carrier) {
			if ($carrier instanceof FighterFlight) continue;
			if (!$carrier->isDestroyed()) continue;

			$jumpEngine = $carrier->getSystemByName('JumpEngine');
			if ($jumpEngine && $jumpEngine->hasJumped()) continue;

			$hangars = self::collectHangars($carrier);
			if (empty($hangars)) continue;

			$primary = self::primaryHangar($carrier);
			if (!$primary) continue;

			//One-shot gate. The flag is set when the note loads at the top of
			//each request (see Hangar::onIndividualNotesLoaded), so once we
			//roll for a given carrier we never roll again.
			if (!empty($primary->escapeRolled)) continue;

			$virtuals = self::buildEscapeCandidates($hangars, $gamedata);

			//Roll d20. random_int draws from the OS CSPRNG, independent of
			//any mt_srand state elsewhere in the request. The result is
			//persisted immediately via the hangarEscapeRoll note in
			//recordEscapeRoll (Manager::insertIndividualNote inserts to DB
			//synchronously), so replay scrubs read the stored value rather
			//than re-rolling. An earlier deterministic-seed (crc32) approach
			//produced a uniform AGGREGATE distribution but clustered nearby
			//seeds into the same outcome bucket — same carrier across
			//consecutive turns kept rolling in the 11-18 range — so the
			//player saw "half escape" every time. Rolling fresh fixes that.
			$roll = random_int(1, 20);
			$totalDocked = count($virtuals);
			$maxEscape = self::computeEscapeCount($roll, $totalDocked);

			$escapedNames = array();
			if ($maxEscape > 0) {
				//Priority sort: combat value DESC (Thunderbolts escape before
				//armed shuttles), then damage ASC (least-damaged first so
				//the healthiest survive).
				usort($virtuals, function($a, $b){
					if ($a['pointCost'] !== $b['pointCost']) return $b['pointCost'] - $a['pointCost'];
					if ($a['damage']    !== $b['damage'])    return $a['damage']    - $b['damage'];
					return 0;
				});
				$chosen = array_slice($virtuals, 0, $maxEscape);
				$escapedNames = self::performCarrierEscapeSpawns($carrier, $hangars, $chosen, $gamedata);
			}

			//The wreck holds nothing now — every escapee is in space and the
			//rest are dead with the ship. Default shuttles that were excluded
			//from the escape pool die here too (auto-fill shuttles don't get
			//to clutter the post-destruction picture per the user's call).
			//Clears even when nothing escaped so the destroyed carrier's row
			//doesn't keep stale stash data dangling.
			foreach ($hangars as $h) {
				$h->hangarUsage = array();
			}

			self::recordEscapeRoll($primary, $roll, $maxEscape, $totalDocked, $escapedNames, $carrier, $gamedata);
		}
	}

	/* B5W d20 table for carrier-destruction escape:
	 *   1–5   → 0 escape
	 *   6–10  → 1/4 (drop fractions)
	 *   11–18 → 1/2 (drop fractions)
	 *   19–20 → all escape
	 */
	public static function computeEscapeCount($roll, $totalDocked){
		if ($totalDocked <= 0) return 0;
		if ($roll >= 19) return $totalDocked;
		if ($roll >= 11) return (int)floor($totalDocked / 2);
		if ($roll >= 6)  return (int)floor($totalDocked / 4);
		return 0;
	}

	/* Per-craft virtual records used to drive the escape priority sort. Each
	 * stash entry contributes one virtual per still-active craft it holds.
	 * dockedFlightId-linked entries also carry the per-fighter damage (for
	 * least-damaged-first tiebreak); other entries (anonymous orphan,
	 * auto-shuttle) get damage 0 — they have no per-fighter granularity.
	 *
	 * cannotLaunch wrecks (Stage 16.5) are excluded from eligibility — they
	 * are already CV=0 and can never be in flight again. */
	private static function buildEscapeCandidates($hangars, $gamedata){
		$virtuals = array();
		foreach ($hangars as $hangar) {
			if (!is_array($hangar->hangarUsage)) continue;
			foreach ($hangar->hangarUsage as $entryIdx => $entry) {
				if (!empty($entry['cannotLaunch'])) continue;
				$phpclass = (string)($entry['phpclass'] ?? '');
				//Stage 18: default (auto-fill) shuttles don't count toward the
				//escape pool and can't escape. Per user call (2026-05-25): only
				//combat fighters, armed shuttle variants, Assault Shuttles, and
				//Breaching Pods are eligible — default shuttles that come free
				//with a ship shouldn't clutter the wreck with survivors.
				if (self::isDefaultShuttleClass($phpclass)) continue;
				$size = max(1, (int)($entry['flightSize'] ?? 1));
				$pcPerCraft = self::entryPointCostPerCraft($entry);

				if (isset($entry['dockedFlightId']) && (int)$entry['dockedFlightId'] > 0) {
					$flight = $gamedata->getShipById((int)$entry['dockedFlightId']);
					if ($flight instanceof FighterFlight) {
						foreach ($flight->systems as $idx => $f) {
							if (!($f instanceof Fighter)) continue;
							if ($f->isDestroyed($gamedata->turn)) continue;
							$virtuals[] = array(
								'hangarRef'  => $hangar,
								'entryIdx'   => $entryIdx,
								'phpclass'   => $phpclass,
								'pointCost'  => $pcPerCraft,
								'damage'     => (int)$f->getTotalDamage(),
								'flightId'   => (int)$entry['dockedFlightId'],
								'fighter'    => $f,
								'fighterIdx' => $idx,
							);
						}
						continue;
					}
					//Linked flight missing — fall through as if anonymous.
				}

				//Anonymous orphan / auto-shuttle / missing-linked-flight: one
				//virtual per declared craft, all with damage 0.
				for ($i = 0; $i < $size; $i++) {
					$virtuals[] = array(
						'hangarRef'  => $hangar,
						'entryIdx'   => $entryIdx,
						'phpclass'   => $phpclass,
						'pointCost'  => $pcPerCraft,
						'damage'     => 0,
						'flightId'   => 0,
						'fighter'    => null,
						'fighterIdx' => $i,
					);
				}
			}
		}
		return $virtuals;
	}

	/* Per-craft pointCost for an entry. fleetList.js renders flights as
	 * pointCost * flightSize/6, but per-craft sorting only needs the relative
	 * ranking — the absolute number doesn't matter, just that a 60-PV
	 * Thunderbolt ranks above a 0-PV auto-shuttle. We use the raw class
	 * pointCost directly. */
	private static function entryPointCostPerCraft($entry){
		$phpclass = (string)($entry['phpclass'] ?? '');
		if ($phpclass === '' || !class_exists($phpclass)) return 0;
		try {
			$probe = new $phpclass(0, 0, '', 0);
			return (int)($probe->pointCost ?? 0);
		} catch (Exception $e) {
			return 0;
		}
	}

	/* Group the chosen virtuals by stash entry, spawn one escape flight per
	 * bucket, then disengage every non-escapee source flight so the
	 * fleet-list 0-CV fold hides their rows.
	 *
	 * Returns the list of escaped flight display names (for the replay note). */
	private static function performCarrierEscapeSpawns($carrier, $hangars, $chosen, $gamedata){
		//Bucket key "{hangarId}:{entryIdx}" — chosen virtuals from the same
		//stash entry all spawn together (resurrect or single fragment).
		$buckets = array();
		foreach ($chosen as $v) {
			$key = (int)$v['hangarRef']->id . ':' . (int)$v['entryIdx'];
			if (!isset($buckets[$key])) {
				$buckets[$key] = array(
					'hangar'   => $v['hangarRef'],
					'entryIdx' => (int)$v['entryIdx'],
					'virtuals' => array(),
				);
			}
			$buckets[$key]['virtuals'][] = $v;
		}

		$names = array();
		foreach ($buckets as $bucket) {
			$hangar = $bucket['hangar'];
			$entry  = $hangar->hangarUsage[$bucket['entryIdx']] ?? null;
			if (!is_array($entry)) continue;
			$name = self::spawnEscapeForBucket($carrier, $hangar, $entry, $bucket['virtuals'], $gamedata);
			if ($name !== null) $names[] = $name;
		}

		self::markNonEscapeesDestroyed($hangars, $buckets, $gamedata);

		return $names;
	}

	/* Spawn the escape from a single stash entry. Three paths:
	 *
	 *  - dockedFlightId AND chosen-count >= source-flight active count →
	 *    resurrect the linked flight directly (clear $removed, deploy
	 *    MovementOrder, LaunchedThisTurn). Source flight IS the escape flight.
	 *  - dockedFlightId AND partial → spawn a fragment carrying only the
	 *    chosen fighters' damage state, clear its $removed flag (the
	 *    spawnFragmentFlight helper births it removed for the dock case),
	 *    overwrite its deploy move with hangar-direction-aware facing.
	 *    The OLD source flight is disengaged by markNonEscapeesDestroyed.
	 *  - anonymous orphan / auto-shuttle / missing-linked-flight → fresh
	 *    FighterFlight via the new-spawn path (mirror performLaunch).
	 *
	 * Returns the new flight's display name (for the replay note), or null
	 * on failure. */
	private static function spawnEscapeForBucket($carrier, $hangar, $entry, $virtuals, $gamedata){
		$phpclass = (string)($entry['phpclass'] ?? '');
		if ($phpclass === '' || !class_exists($phpclass)) return null;
		$count = count($virtuals);
		if ($count <= 0) return null;

		$lastMove = $carrier->getLastMovement();
		if (!$lastMove) return null;
		$spawnPos = $lastMove->position;
		$heading  = (int)$lastMove->heading;
		$facing   = ((int)$lastMove->facing + (int)$hangar->direction) % 6;
		if ($facing < 0) $facing += 6;
		$speed    = (int)$lastMove->speed;

		$flightId = isset($entry['dockedFlightId']) ? (int)$entry['dockedFlightId'] : 0;
		if ($flightId > 0) {
			$sourceFlight = $gamedata->getShipById($flightId);
			if ($sourceFlight instanceof FighterFlight) {
				$sourceActive = $sourceFlight->countActiveCraft($gamedata->turn);

				if ($count >= $sourceActive) {
					//Full escape — resurrect the source flight. The flight ship
					//row IS the escape flight (no new ship inserted), so all
					//its damage / crit state survives unchanged.
					$sourceFlight->removed     = false;
					$sourceFlight->removedTurn = null;
					$sourceFlight->spawned     = $gamedata->turn;

					$deployMove = new MovementOrder(null, "deploy", $spawnPos, 0, 0, $speed, $heading, $facing, false, $gamedata->turn, 0, 0);
					Manager::insertSingleMovement($gamedata->id, $sourceFlight->id, $deployMove);

					self::applyEscapeCrits($sourceFlight, $gamedata);
					self::writeEscapeEventNote($carrier, $hangar, $sourceFlight, $count, 'resurrected', $gamedata);
					return $sourceFlight->name;
				}

				//Partial escape — spawn a fragment carrying the chosen
				//fighters' state. Mirrors spawnFragmentFlight (Stage 10.4)
				//but the result is on-the-board, not in-hangar.
				$chosenFighters = array();
				foreach ($virtuals as $v) {
					if ($v['fighter'] instanceof Fighter) $chosenFighters[] = $v['fighter'];
				}
				if (empty($chosenFighters)) return null;

				$escapeFlight = self::spawnFragmentFlight($sourceFlight, $chosenFighters, $carrier, $hangar, $gamedata);
				if (!$escapeFlight) return null;

				//spawnFragmentFlight births the fragment $removed=true (for
				//the normal partial-dock case where it sits in a hangar).
				//For escape, the fragment is live in space — clear the flag
				//and overwrite the auto-inserted deploy move with the
				//hangar-direction-aware facing.
				$escapeFlight->removed     = false;
				$escapeFlight->removedTurn = null;

				$deployMove = new MovementOrder(null, "deploy", $spawnPos, 0, 0, $speed, $heading, $facing, false, $gamedata->turn, 0, 0);
				Manager::insertSingleMovement($gamedata->id, $escapeFlight->id, $deployMove);

				self::applyEscapeCrits($escapeFlight, $gamedata);
				self::writeEscapeEventNote($carrier, $hangar, $escapeFlight, $count, 'fragment', $gamedata);
				return $escapeFlight->name;
			}
			//Linked flight missing — fall through to new-spawn.
		}

		//New-spawn path (anonymous orphan, auto-shuttle, or missing-linked-flight).
		//Mirrors the second half of performLaunch's new-spawn branch.
		$flightName = self::flightNameFor($phpclass, $carrier);
		$flight = new $phpclass($gamedata->id, $carrier->userid, $flightName, $carrier->slot);
		$flight->team = $carrier->team;
		if ($count > $flight->flightSize) {
			$flight->flightSize = $count;
			if (method_exists($flight, 'populate')) {
				$flight->populate();
			}
		}

		$shipid = Manager::insertSingleShip($gamedata, $flight, $carrier->userid);
		$flight->id = $shipid;
		$flight->spawned = $gamedata->turn;

		Manager::insertSingleFlightSize($gamedata->id, $shipid, $flight->flightSize);

		$deployMove = new MovementOrder(null, "deploy", $spawnPos, 0, 0, $speed, $heading, $facing, false, $gamedata->turn, 0, 0);
		Manager::insertSingleMovement($gamedata->id, $shipid, $deployMove);

		SystemData::initSystemData($gamedata->turn, $gamedata->id);
		foreach ($flight->systems as $craft) {
			$craft->setInitialSystemData($flight);
			if (!isset($craft->systems) || !is_array($craft->systems)) continue;
			foreach ($craft->systems as $sys) {
				$sys->setInitialSystemData($flight);
				if ($sys instanceof Weapon) {
					$load = $sys->getStartLoading();
					if ($load) {
						$load->loading = $sys->loadingtime;
						SystemData::addDataForSystem($sys->id, 0, $shipid, $load->toJSON());
					}
				}
			}
		}
		Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());

		self::applyEscapeCrits($flight, $gamedata);
		self::writeEscapeEventNote($carrier, $hangar, $flight, $count, 'newspawn', $gamedata);
		return $flight->name;
	}

	/* Apply LaunchedThisTurn (-50 init next turn) to an escapee. UNLIKE
	 * applyLaunchCrits, this does NOT apply HangarOperations to the carrier:
	 * the carrier is dead, it can't take an initiative penalty. The flight
	 * gets the standard penalty so an escapee can't immediately seize the
	 * initiative on the turn after escaping. */
	private static function applyEscapeCrits($flight, $gamedata){
		if (!($flight instanceof FighterFlight)) return;
		$firstFighter = $flight->getSampleFighter();
		if (!$firstFighter) return;
		$crit = new LaunchedThisTurn(-1, $flight->id, $firstFighter->id, 'LaunchedThisTurn', $gamedata->turn, $gamedata->turn + 1);
		$crit->updated = true;
		$firstFighter->criticals[] = $crit;
	}

	/* For every dockedFlightId-linked stash entry on the destroyed carrier,
	 * disengage the source flight's fighters that did NOT escape (so the
	 * 0-CV fold in fleetList hides those rows). $escapeBuckets is keyed by
	 * "{hangarId}:{entryIdx}" and identifies which entries were resurrected
	 * (full escape — DON'T disengage; the source IS the escape flight).
	 *
	 * Partial-escape entries: the chosen fighters' damage was copied onto a
	 * NEW fragment via spawnFragmentFlight. Now disengage every original
	 * fighter on the source flight so the source's combat value collapses
	 * to 0 — the chosen fighters are still physically present on the source
	 * flight, just like the partial-dock case in Stage 10.4. */
	private static function markNonEscapeesDestroyed($hangars, $escapeBuckets, $gamedata){
		foreach ($hangars as $hangar) {
			if (!is_array($hangar->hangarUsage)) continue;
			foreach ($hangar->hangarUsage as $entryIdx => $entry) {
				if (!isset($entry['dockedFlightId'])) continue;
				$flightId = (int)$entry['dockedFlightId'];
				if ($flightId <= 0) continue;
				$flight = $gamedata->getShipById($flightId);
				if (!($flight instanceof FighterFlight)) continue;

				$key = (int)$hangar->id . ':' . (int)$entryIdx;
				$resurrected = false;
				if (isset($escapeBuckets[$key])) {
					$bucket = $escapeBuckets[$key];
					$sourceActive = $flight->countActiveCraft($gamedata->turn);
					$bucketCount = count($bucket['virtuals']);
					if ($bucketCount >= $sourceActive) {
						//Full extract via resurrect — source flight IS the
						//escape flight, already cleared $removed. Skip.
						$resurrected = true;
					}
				}
				if ($resurrected) continue;

				self::disengageFighters($flight, PHP_INT_MAX, $gamedata);
			}
		}
	}

	/* Persist the escape roll on the carrier's primary hangar and write a
	 * single 'hangarEscapeRoll' note for replay. The in-memory $escapeRolled
	 * flag short-circuits future Pass 3 sweeps in this request; the note
	 * carries the same gate across requests via Hangar::onIndividualNotesLoaded. */
	private static function recordEscapeRoll($primary, $roll, $maxEscape, $totalDocked, $escapedNames, $carrier, $gamedata){
		$primary->escapeRolled = true;
		$primary->escapeRoll   = (int)$roll;
		$primary->escapeMax    = (int)$maxEscape;
		$primary->escapeTotal  = (int)$totalDocked;
		$primary->escapeNames  = $escapedNames;

		$payload = json_encode(array(
			'roll'  => (int)$roll,
			'max'   => (int)$maxEscape,
			'total' => (int)$totalDocked,
			'names' => $escapedNames,
			'turn'  => (int)$gamedata->turn,
		));
		$note = new IndividualNote(
			-1,
			$gamedata->id,
			$gamedata->turn,
			$gamedata->phase,
			$carrier->id,
			$primary->id,
			'hangarEscapeRoll',
			'Hangar escape roll',
			$payload
		);
		Manager::insertIndividualNote($note);
	}

	/* Per-escapee replay note. $kind ∈ {'resurrected', 'fragment', 'newspawn'}
	 * for forensic clarity in DB inspection. */
	private static function writeEscapeEventNote($carrier, $hangar, $flight, $count, $kind, $gamedata){
		$note = new IndividualNote(
			-1,
			$gamedata->id,
			$gamedata->turn,
			$gamedata->phase,
			$carrier->id,
			$hangar->id,
			'hangarEscapeEvent',
			'Hangar craft escaped destroyed carrier',
			$flight->id . ':' . $flight->phpclass . ':' . $count . ':' . $kind
		);
		Manager::insertIndividualNote($note);
	}

}
