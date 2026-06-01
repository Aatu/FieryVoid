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
		//Fighter Rails get the SAME treatment as catapults here: their boxes are
		//structure-coupled HP that hold combat fighters (auto-deploying to space
		//at turn 1), NOT default-shuttle slots. Partition them out of the shuttle
		//pool and skip their fighter category from $totalDeclared below, exactly
		//as 'superheavy' is skipped for catapults. $railCategories is the set of
		//$ship->fighters keys (e.g. 'light') that ride this ship's rails.
		$hasCatapult   = false;
		$shuttleHangars = array();
		foreach ($hangars as $h){
			if (!empty($h->isCatapult)) { $hasCatapult = true; continue; }
			if (!empty($h->isRail))     { continue; }
			$shuttleHangars[] = $h;
		}
		$railCategories = self::railFighterCategories($ship);

		//Step 1: explicit shuttle / minesweeping-shuttle / cargo-shuttle declarations
		//get auto-filled (combat fighter declarations are NOT auto-filled here —
		//those auto-deploy to space at turn 1 via the existing fleet-builder flow.)
		$totalDeclared = 0;
		if (is_array($ship->fighters)) {
			foreach ($ship->fighters as $category => $declaredCount){
				//Stage 16: superheavy fighters live in the catapult, not the
				//shuttle-pool hangars — don't count them toward $totalDeclared
				//(which would shrink the leftover shuttle fill) and never try to
				//auto-fill them. Only special-cased when the ship actually has a
				//catapult, so catapult-less ships behave exactly as before.
				if ($hasCatapult && strtolower(trim((string)$category)) === 'superheavy') continue;
				//Rail-borne fighter categories live on the rail, not the shuttle-
				//pool hangars — exclude them from $totalDeclared so the real
				//Hangar's leftover shuttle fill isn't shrunk by rail capacity.
				if (isset($railCategories[strtolower(trim((string)$category))])) continue;

				$count = (int)$declaredCount;
				$totalDeclared += self::shuttlePoolBoxesFor($category, $count);
				$shuttleClass = self::shuttlePhpclassForCategory($category, $ship);
				if ($shuttleClass === null) continue;   //not a shuttle category

				$shuttleName = self::shuttleDisplayNameFor($shuttleClass);

				while ($count > 0){
					$hangar = self::pickHangarForShuttle($shuttleHangars, 1);
					if (!$hangar) break;

					$free = (int)$hangar->maxhealth - self::occupiedBoxes($hangar);
					$take = min($count, $free, self::fairShareCap($shuttleHangars, $count));

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
			//Explicit "minesweeping shuttles" in $ship->fighters is the designer's
			//authoritative MSW count — leftover falls through to the faction shuttle
			//even on minesweeper-bonus carriers, so the remaining boxes don't double
			//up as more MinesweepingShuttles. Ships with no shuttle declarations on a
			//minesweeper-bonus carrier keep the original "all leftover = MSW" default.
			$hasExplicitMsw = is_array($ship->fighters)
				&& !empty($ship->fighters['minesweeping shuttles']);
			$baseClass = (!$hasExplicitMsw && isset($ship->minesweeperbonus) && $ship->minesweeperbonus > 0)
				? self::factionMinesweepingShuttleClass($ship)				
				: self::factionShuttleClass($ship);
			$baseCategory = self::isMinesweepingShuttleClass($baseClass) ? 'minesweeping shuttles' : 'shuttles';			
			$baseName = self::shuttleDisplayNameFor($baseClass);

			//HANG_MSW retype count — only meaningful when the carrier's default pool
			//is NOT already MinesweepingShuttle. Capped at the leftover total.
			$hangMswRetype = 0;
			if (!self::isMinesweepingShuttleClass($baseClass) && isset($ship->enhancementOptions) && is_array($ship->enhancementOptions)) {			
				foreach ($ship->enhancementOptions as $opt) {
					if (($opt[0] ?? '') === 'HANG_MSW' && (int)($opt[2] ?? 0) > 0) {
						$hangMswRetype = min((int)$opt[2], $leftover);
						break;
					}
				}
			}

			//Place MinesweepingShuttle records first (one per box, so flightSize=1)
			$mswRemaining = $hangMswRetype;
			$mswClass = self::factionMinesweepingShuttleClass($ship);
			$mswName  = self::shuttleDisplayNameFor($mswClass);				
			while ($mswRemaining > 0){
				$hangar = self::pickHangarForShuttle($shuttleHangars, 1);			
				if (!$hangar) break;
				$free = (int)$hangar->maxhealth - self::usageCountFor($hangar);
				$take = min($mswRemaining, $free, self::fairShareCap($shuttleHangars, $mswRemaining));
				$hangar->hangarUsage[] = array(
					'phpclass' => $mswClass,
					'name' => $mswName,
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
				$take = min($count, $free, self::fairShareCap($shuttleHangars, $count));

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

	/* Hangar boxes consumed by a given ship->fighters declaration. Per
	 * B5W §10.1, ultralight fighters fit two per box (0.5 each); every
	 * other category is one box per craft. ceil() so an odd ultralight
	 * count doesn't yield a free half-box of default-shuttle capacity.
	 * Used by populateInitialHangarUsage and getDefaultShuttles so the
	 * leftover-shuttle math matches the gamelobby's hangar-space check.
	 */
	public static function shuttlePoolBoxesFor($category, $count){
		$count = (int)$count;
		if ($count <= 0) return 0;
		if (strtolower(trim((string)$category)) === 'ultralight') {
			return (int)ceil($count / 2);
		}
		return $count;
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
				//return 'MinesweepingShuttle';
				return self::factionMinesweepingShuttleClass($ship);				
			case 'cargo shuttles':
				//Opt-in only: never auto-populates leftover capacity. Declared
				//count in $ship->fighters becomes that many auto-filled CargoShuttle
				//records via step 1; leftover hangar boxes go to the faction shuttle
				//(or MinesweepingShuttle for minesweeper-bonus carriers) instead.
				return 'CargoShuttle';
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

/* Faction-aware minesweeping shuttle class for a carrier. Mirrors
	 * factionShuttleClass() but returns the faction's *minesweeping* variant.
	 * class_exists() falls back to generic 'MinesweepingShuttle' when a faction
	 * has no shuttle, or its minesweeping variant hasn't been defined — so this
	 * degrades safely rather than producing an unloadable phpclass. */
	public static function factionMinesweepingShuttleClass($ship){
		$base = self::factionShuttleClass($ship);
		if ($base === 'Shuttle') return 'MinesweepingShuttle';
		$candidate = 'Minesweeping' . $base;
		return class_exists($candidate) ? $candidate : 'MinesweepingShuttle';
	}

	/* By-faction-name variant, for the game.php blueprint preload pass.
	 * Returns null when nothing extra beyond the generic is needed. */
	public static function minesweepingShuttleClassForFactionName($faction){
		$base = self::shuttleClassForFactionName($faction);
		if ($base === null) return null;
		$candidate = 'Minesweeping' . $base;
		return class_exists($candidate) ? $candidate : null;
	}

	/* True for the generic MinesweepingShuttle and every faction variant — they
	 * all share the 'Minesweeping' name prefix, so this classifies a stored
	 * phpclass string without instantiating it. */
	public static function isMinesweepingShuttleClass($phpclass){
		return is_string($phpclass) && strpos($phpclass, 'Minesweeping') === 0;
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
	if (self::isMinesweepingShuttleClass($phpclass)) return 'Minesweeping Shuttle';	
		switch ($phpclass) {
			case 'MinesweepingShuttle':
				return 'Minesweeping Shuttle';
			case 'CargoShuttle':
				return 'Cargo Shuttle';
			case 'Flyer':
				return 'Flyer';
			case 'FlyerProtectorate':
				return 'Flyer';
			case 'Shuttle':
			default:
				return 'Shuttle';
		}
	}

	/* Picks the hangar that should receive the next default-shuttle box —
	 * shuttles per B5W §10.1 may use any fighter box. (A future "shuttle-only"
	 * hangar type would still match here, since it accepts shuttles by definition.)
	 *
	 * Distribution prefers the "distribution set" (see distributionHangars): the
	 * primary-structure hangars if the ship has any, otherwise every hangar.
	 * Within that set we pick the hangar with the lowest current usage so the
	 * pool splits EVENLY rather than piling into whichever appears first — a ship
	 * with several primary hangars (e.g. Pirocia's three) gets 2+2+2, and a ship
	 * with side hangars only (e.g. Marata) gets 3+3. A lone primary hangar is the
	 * whole set, so it still swallows the pool exactly as before. Once the
	 * preferred set is full, overflow falls back to any remaining hangar in
	 * encounter order. Paired with fairShareCap() in the caller to bound how much
	 * each write takes.
	 */
	public static function pickHangarForShuttle($hangars, $flightSize){
		$best = null;
		$bestUsage = PHP_INT_MAX;
		foreach (self::distributionHangars($hangars) as $h){
			//Free-space gate uses occupiedBoxes (whole boxes); the least-used
			//ordering can stay on the raw (possibly fractional) usage.
			if ((int)$h->maxhealth - self::occupiedBoxes($h) < $flightSize) continue;
			$used = self::usageCountFor($h);
			if ($used < $bestUsage){
				$bestUsage = $used;
				$best = $h;
			}
		}
		if ($best !== null) return $best;
		//Preferred set full — overflow to any remaining hangar in encounter order.
		foreach ($hangars as $h){
			if ((int)$h->maxhealth - self::occupiedBoxes($h) >= $flightSize) return $h;
		}
		return null;
	}

	/* The subset of $hangars the default-shuttle pool is distributed across
	 * first: the primary-structure (location 0) hangars if the ship has any,
	 * otherwise every hangar. Multiple primary hangars therefore share the pool
	 * evenly (Pirocia's three → 2+2+2) while a single primary still takes the
	 * whole pool; side hangars on a ship that has primaries get only overflow
	 * once the primaries are full. Mirrored client-side in systems.js
	 * getDefaultShuttleCompositionForHangar so lobby tooltips match the in-game
	 * initial population. */
	public static function distributionHangars($hangars){
		$primary = array();
		foreach ($hangars as $h){
			if ((int)$h->location === 0) $primary[] = $h;
		}
		return !empty($primary) ? $primary : $hangars;
	}

	/* Per-write fair-share cap on $take during initial hangar population.
	 *
	 * Counts the hangars in the distribution set (distributionHangars) that
	 * still have room and returns ceil($remainingTotal / thatCount), so each
	 * loop iteration writes no more than its fair share and the pool rounds out
	 * evenly across them. Returns PHP_INT_MAX when only one such hangar remains
	 * (a lone primary, or the last hangar still taking overflow) — it simply
	 * takes the rest, preserving the historical single-hangar fill. Combined
	 * with pickHangarForShuttle's least-used preference, this yields 2+2+2 across
	 * Pirocia's three primary hangars and 3+3 across Marata's two side hangars. */
	public static function fairShareCap($hangars, $remainingTotal){
		$hangarsWithRoom = 0;
		foreach (self::distributionHangars($hangars) as $h){
			if ((int)$h->maxhealth - self::occupiedBoxes($h) > 0) $hangarsWithRoom++;
		}
		if ($hangarsWithRoom <= 1) return PHP_INT_MAX;
		return (int)ceil($remainingTotal / $hangarsWithRoom);
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

	/* Fighter Rails: just the FighterRail instances on a ship (a subset of
	 * collectHangars, since FighterRail extends Hangar). Used by the rail crit
	 * dedup / pickRailToDestroy and the shuttle-pool exclusion below. */
	public static function collectRails($ship){
		$rails = array();
		if (!isset($ship->systems) || !is_array($ship->systems)) return $rails;
		foreach ($ship->systems as $sys){
			if (!empty($sys->isRail)) $rails[] = $sys;
		}
		return $rails;
	}

	/* True if $ship mounts any FighterRail. Mirror of the client systems.js
	 * shipHasRail() so the shuttle-pool math stays in lockstep across the
	 * server/client divide. */
	public static function shipHasRail($ship){
		foreach (self::collectRails($ship) as $r) return true;
		return false;
	}

	/* Lowercased set of fighter-category keys (e.g. 'light') that belong to
	 * this ship's rails. Rail-borne fighters auto-deploy to space at turn 1
	 * like any combat fighter, so their declared $ship->fighters capacity must
	 * be excluded from the default-shuttle leftover math (parallel to the
	 * catapult 'superheavy' exclusion). Returns an associative set for O(1)
	 * membership tests. */
	public static function railFighterCategories($ship){
		$cats = array();
		foreach (self::collectRails($ship) as $r){
			$cat = strtolower(trim((string)$r->hangarType));
			if ($cat !== '') $cats[$cat] = true;
		}
		return $cats;
	}

	/* Hangar boxes a single craft of $phpclass occupies. Per B5W §10.1 craft come
	 * in two non-standard sizes:
	 *   - unitSize < 1 (Vorlon Assault Fighter, Drakh Heavy Raider, Nausicaan
	 *     Glider SHF, …): one craft needs MORE than one hangar box
	 *     (e.g. unitSize 0.5 → 2 boxes), so this returns the integer ceil(1/unitSize).
	 *   - unitSize > 1 (ultralights: Zorth, VreeSalvageZorth, LtViper): two (or more)
	 *     craft pack into a single box, so one craft costs a FRACTIONAL 1/unitSize
	 *     boxes (e.g. unitSize 2 → 0.5). The per-box hangarUsage sum is therefore
	 *     fractional; every capacity/free-box gate rounds the hangar's TOTAL
	 *     occupancy UP to a whole box (see freeBoxesByCategory / canShipReceive), so
	 *     12 boxes hold 24 Zorth but a lone Zorth still consumes a whole box.
	 * Ordinary craft (unitSize == 1) occupy exactly one box. Cached per phpclass to
	 * avoid re-instantiating blueprints on every call. Return type is int|float. */
	public static function boxesPerCraftForClass($phpclass){
		static $cache = array();
		if (!is_string($phpclass) || $phpclass === '') return 1;
		if (isset($cache[$phpclass])) return $cache[$phpclass];
		$boxes = 1;
		if (class_exists($phpclass)) {
			try {
				$probe = new $phpclass(0, 0, '', 0);
				$u = isset($probe->unitSize) ? (float)$probe->unitSize : 1.0;
				if ($u > 0 && $u < 1)      $boxes = (int)ceil(1 / $u);  //superheavy: >1 box/craft
				else if ($u > 1)           $boxes = 1 / $u;             //ultralight: fractional box/craft
			} catch (Exception $e) {}
		}
		$cache[$phpclass] = $boxes;
		return $boxes;
	}

	/* Boxes per craft for a stored hangarUsage entry. Prefers the boxesPerCraft
	 * value stamped at dock time (performLand / performDeployStartDock) so we
	 * avoid re-instantiating the blueprint; falls back to the phpclass lookup
	 * for legacy entries written before the field existed (those resolve to 1
	 * unless the class is a unitSize<1 superheavy or unitSize>1 ultralight).
	 * Read as a FLOAT so a stamped fractional cost (ultralight, 0.5) round-trips. */
	public static function boxesPerCraftForEntry($entry){
		if (isset($entry['boxesPerCraft'])) {
			$bpc = (float)$entry['boxesPerCraft'];
			return $bpc > 0 ? $bpc : 1;
		}
		return self::boxesPerCraftForClass($entry['phpclass'] ?? '');
	}

	/* Hangar boxes occupied by a single stored entry (craft count × per-craft boxes). */
	public static function boxesForEntry($entry){
		$flightSize = (int)($entry['flightSize'] ?? 1);
		if ($flightSize <= 0) return 0;
		return $flightSize * self::boxesPerCraftForEntry($entry);
	}

	/* === Stage 21: occupancy (no-split docking) ========================== */
	/*
	 * A docked flight is ONE ship with ONE stash entry on its PRIMARY bay. When
	 * the flight is too big for that bay, the entry carries an `occupancy` list
	 *   occupancy: [ {systemId, boxes}, … ]
	 * naming every bay (incl. the primary) that holds boxes for it. The sibling
	 * bays do NOT get their own stash entry — their boxes are accounted as
	 * occupied by reading the primary entry's occupancy from the OTHER hangars.
	 *
	 * Legacy compatibility: an entry with NO `occupancy` is a single-bay dock on
	 * its own hangar (the Stage-19 / live shape) — boxesForEntry covers it; it
	 * places no boxes on sibling bays.
	 */

	/* Boxes that entries on OTHER hangars of $ship place on $hangar via their
	 * occupancy lists. Added to $hangar's own usage so free-box math sees a bay
	 * filled by a multi-bay flight whose primary entry lives elsewhere.
	 * Catapults never participate in occupancy (single-fighter, 1:1). */
	public static function foreignOccupancyBoxesOn($hangar, $ship){
		if (!empty($hangar->isCatapult)) return 0;
		if (!$ship || !isset($ship->systems) || !is_array($ship->systems)) return 0;
		$myId = (int)$hangar->id;
		$boxes = 0;
		foreach ($ship->systems as $sys){
			if (!($sys instanceof Hangar)) continue;
			if ($sys === $hangar) continue;                 //own usage counted separately
			if (!is_array($sys->hangarUsage)) continue;
			foreach ($sys->hangarUsage as $entry){
				if (empty($entry['occupancy']) || !is_array($entry['occupancy'])) continue;
				foreach ($entry['occupancy'] as $occ){
					if ((int)($occ['systemId'] ?? 0) === $myId){
						$boxes += max(0, (int)($occ['boxes'] ?? 0));
					}
				}
			}
		}
		return $boxes;
	}

	/* Boxes a single entry occupies ON A SPECIFIC hangar id. For an entry with an
	 * occupancy list, the boxes recorded for that systemId; for a legacy/no-occ
	 * entry, its full boxesForEntry only if it lives on that hangar. */
	public static function entryBoxesOnHangar($entry, $hangarId){
		$hangarId = (int)$hangarId;
		if (!empty($entry['occupancy']) && is_array($entry['occupancy'])){
			$b = 0;
			foreach ($entry['occupancy'] as $occ){
				if ((int)($occ['systemId'] ?? 0) === $hangarId) $b += max(0, (int)($occ['boxes'] ?? 0));
			}
			return $b;
		}
		return 0;   //no-occ entries are counted on their own hangar by boxesForEntry, not here
	}

	/* Total occupied boxes across this hangar. A unitSize<1 craft consumes more
	 * than one box per craft, and a unitSize>1 ultralight consumes a FRACTIONAL
	 * box (0.5) per craft (see boxesPerCraftForClass), so this is NOT simply the
	 * stored craft count and may be fractional. Callers comparing against integer
	 * maxhealth / remaining health round this UP to whole boxes (see
	 * freeBoxesByCategory / canShipReceive); the raw fractional sum is returned
	 * here so multi-bay occupancy math stays exact.
	 *
	 * Catapults are exempt from the per-craft box multiplier: a catapult is a
	 * single-fighter rail that holds exactly ONE craft regardless of unitSize
	 * (its extra boxes are structural HP, and effectiveCapacity() is a flat 1),
	 * so it counts craft 1:1. Without this a unitSize<1 superheavy could never
	 * fit the catapult's single slot. */
	public static function usageCountFor($hangar, $ship = null){
		$n = 0;
		if (!is_array($hangar->hangarUsage)) return 0;
		$isCatapult = !empty($hangar->isCatapult);
		$myId = (int)$hangar->id;
		foreach ($hangar->hangarUsage as $entry){
			if ($isCatapult){
				$n += (int)($entry['flightSize'] ?? 1);
				continue;
			}
			//Stage 21: an occupancy-bearing entry (multi-bay no-split dock) counts
			//only the boxes its occupancy assigns to THIS (primary) hangar; the
			//sibling-bay boxes are counted on those bays via foreignOccupancyBoxesOn.
			//A legacy / single-bay entry (no occupancy) counts its full boxes here.
			if (!empty($entry['occupancy']) && is_array($entry['occupancy'])){
				$n += self::entryBoxesOnHangar($entry, $myId);
			} else {
				$n += self::boxesForEntry($entry);
			}
		}
		//Stage 21: add boxes that OTHER bays' multi-bay entries place on this one.
		if (!$isCatapult && $ship === null) $ship = $hangar->getUnit();
		if (!$isCatapult && $ship) $n += self::foreignOccupancyBoxesOn($hangar, $ship);
		return $n;
	}

	/* Occupied boxes ROUNDED UP to whole boxes — the conservative "round up for
	 * safety" capacity unit. With ultralights (fractional per-craft cost) a hangar
	 * can hold 0.5/1.5/… raw boxes of usage; comparing that against the integer
	 * maxhealth / getRemainingHealth must round UP so a partly-filled box still
	 * reserves a whole box (3 Zorth = 1.5 → 2 boxes consumed). For integer usage
	 * (the common case and the unitSize<1 superheavy case) this is a no-op. */
	public static function occupiedBoxes($hangar, $ship = null){
		return (int)ceil(self::usageCountFor($hangar, $ship));
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
		//the ship's $fighters declaration (Stage 16). Fighter Rails carry an
		//explicit category from the ship file (load-bearing for launch/dock
		//eligibility) — leave them untouched too.
		if (!empty($hangar->isCatapult) || !empty($hangar->isRail)) return;
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
			if (!empty($h->isRail))     { continue; }   //rail boxes are structure HP, not shuttle pool
			$capacity += (int)$h->maxhealth;
		}
		if ($capacity <= 0) {
			return array('count' => 0, 'type' => 'Shuttles', 'key' => 'shuttles');
		}
		$railCategories = self::railFighterCategories($ship);
		$declared = 0;
		if (isset($ship->fighters) && is_array($ship->fighters)) {
			foreach ($ship->fighters as $category => $count) {
				if ($hasCatapult && strtolower(trim((string)$category)) === 'superheavy') continue;
				if (isset($railCategories[strtolower(trim((string)$category))])) continue;
				$declared += self::shuttlePoolBoxesFor($category, $count);
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

	/* Evict craft until at least $boxesToFree hangar boxes are freed (lost-box
	 * damage handling: a damaged hangar drops stored craft to fit its reduced
	 * capacity). Priority: shuttles first (cheapest, most fungible), then
	 * anything else in stored order. For partial-flight evictions (e.g. 2 boxes
	 * lost from a stored 6-fighter flight), the record's $flightSize is reduced
	 * rather than the whole record dropped.
	 *
	 * Box-aware: a unitSize<1 craft frees boxesPerCraft boxes per craft evicted,
	 * and the craft count needed is rounded UP — so a 2-box craft is evicted as
	 * soon as even one of its boxes can no longer be held (stored craft never
	 * exceed remaining capacity). Returns the number of CRAFT evicted.
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
	public static function evictCraftFromHangar($hangar, $boxesToFree, $gamedata){
		if ($boxesToFree <= 0 || empty($hangar->hangarUsage)) return 0;

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

		$freed = 0;      //boxes freed so far
		$evicted = 0;    //craft evicted so far (return value)
		foreach ($indexed as $row){
			if ($freed >= $boxesToFree) break;
			$idx = $row['idx'];
			$entry = $hangar->hangarUsage[$idx];
			$available = (int)($entry['flightSize'] ?? 1);
			if ($available <= 0) continue;
			$bpc = self::boxesPerCraftForEntry($entry);
			//Craft needed to cover the remaining box shortfall, rounded UP so a
			//multi-box craft is evicted the moment one of its boxes can't fit.
			$needCraft = (int)ceil(($boxesToFree - $freed) / $bpc);
			$take = min($available, $needCraft);
			if (isset($entry['dockedFlightId']) && $take > 0) {
				$flight = $gamedata->getShipById((int)$entry['dockedFlightId']);
				if ($flight instanceof FighterFlight) {
					self::disengageFighters($flight, $take, $gamedata);
				}
			}
			$hangar->hangarUsage[$idx]['flightSize'] = $available - $take;
			$freed   += $take * $bpc;
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
		//if ($phpclass === 'MinesweepingShuttle') return 20;
		if (self::isMinesweepingShuttleClass($phpclass)) return 20;		
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

		$remaining = (int)$hangar->getRemainingHealth();
		//Occupied boxes round UP (ultralights leave fractional usage): a half-filled
		//box still needs a whole box to live in, so it's lost when capacity drops to it.
		$stored = self::occupiedBoxes($hangar);
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
		$isRail = !empty($hangar->isRail);
		foreach ($hangar->hangarUsage as $entry){
			if (!isset($entry['dockedFlightId'])) continue;
			//Must have been docked since BEFORE this turn (no rearm on dock turn).
			if (isset($entry['dockedTurn']) && (int)$entry['dockedTurn'] >= $gamedata->turn) continue;
			//Fighter Rails: hangar ops through the narrow airlocks take TWICE as
			//long (B5W §10.1). A docked-turn-T flight services on T+2, T+4, …
			//(elapsed even and > 0) — half the cadence of an ordinary hangar,
			//which services every turn from T+1. Gating the whole entry here
			//halves the rate uniformly across every whileDocked reload hook
			//(matter ammo, ordnance-pool missiles, marines).
			if ($isRail && isset($entry['dockedTurn'])){
				$elapsed = $gamedata->turn - (int)$entry['dockedTurn'];
				if ($elapsed <= 0 || ($elapsed % 2) !== 0) continue;
			}
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

	/* === Fighter Rails: structure-coupled destruction (B5W §10.1) ========= */

	/* The "owning" rail for a structure block: the lowest-id FighterRail mounted
	 * on $structure. Deterministic across requests (system ids are stable), so a
	 * replay re-derives the same owner. Only the owner rolls/persists the
	 * per-structure 1d20, guaranteeing exactly ONE roll per structure-damage
	 * event even when several rails share a structure. Returns null if no rail
	 * is attached to $structure. */
	public static function railCritOwner($structure, $ship){
		if (!$structure) return null;
		$owner = null;
		foreach (self::collectRails($ship) as $rail){
			if ($rail->getStructureSystem() !== $structure) continue;
			if ($owner === null || (int)$rail->id < (int)$owner->id) $owner = $rail;
		}
		return $owner;
	}

	/* End-of-turn rail crit check, called from Hangar::criticalPhaseEffects for
	 * every rail. When the rail's parent structure took damage this turn, the
	 * OWNING rail (railCritOwner) rolls an unmodified 1d20 — on a natural 16-20
	 * one ENTIRE rail on that structure is destroyed (auto-picked) and its
	 * fighters attempt escape. Non-owning rails on the same structure no-op
	 * (the owner already covered the structure's single roll). There is NO
	 * per-structure-point box attrition — the only in-battle box-loss paths are
	 * this 1d20 and full structure-block destruction (the inherited fall-off).
	 *
	 * Replay-deterministic: the rolled value is read from a railCritRoll note
	 * via FighterRail::onIndividualNotesLoaded (railCritLoadedTurn/Value) when
	 * present for this turn, and only rolled fresh + persisted on the live Fire
	 * Phase advance. */
	public static function onRailStructureDamage($rail, $ship, $gamedata){
		$structure = $rail->getStructureSystem();
		if (!$structure) return;

		//Only the owning rail rolls for its structure (per-structure dedup).
		$owner = self::railCritOwner($structure, $ship);
		if ($owner !== $rail) return;

		//Roll only if the structure took post-armour damage THIS turn.
		if ($structure->damageReceivedOnTurn($gamedata->turn) <= 0) return;

		//Replay-deterministic roll: reuse the stored value when this turn's
		//railCritRoll note was loaded; otherwise roll fresh and persist.
		if ($rail->railCritLoadedTurn === (int)$gamedata->turn){
			$roll = (int)$rail->railCritLoadedValue;
		} else {
			$roll = Dice::d(20);   //NO crit modifiers (B5W: ignore weapon bonuses)
			self::recordRailCritRoll($rail, $roll, $gamedata);
			//Stamp the loaded fields so a second pass in the same request (or a
			//re-entry) treats the roll as already taken.
			$rail->railCritLoadedTurn  = (int)$gamedata->turn;
			$rail->railCritLoadedValue = $roll;
		}
		//$roll = 17; //For debugging reasons.		
		if ($roll >= 16){
			$target = self::pickRailToDestroy($structure, $ship);
			if ($target) self::destroyEntireRail($target, $ship, $gamedata);
		}
	}

	/* Full-structure-block loss: when a rail's parent (external) structure is
	 * itself destroyed this turn, EVERY rail on that block is destroyed with it
	 * (the inherited isDestroyed fall-off drops them a turn later, but the boxes
	 * are gone now) and all their docked fighters attempt escape — the second of
	 * the two box-loss paths (the 1d20 whole-rail crit being the first). The
	 * carrier itself is NOT destroyed here (only PRIMARY structure loss kills a
	 * ship; an external block dying leaves the carrier alive) — the destroyed-
	 * CARRIER case is owned by Stage 18's processCarrierDestructionEscapes, which
	 * already covers rails via collectHangars. Called for every rail; per-
	 * structure dedup via railCritOwner so the block's rails are processed once.
	 *
	 * Replay-safe WITHOUT a roll note: railBoxEscape clears each rail's hangarUsage
	 * after spawning, so a replay scrub re-enters with empty usage → zero escape
	 * candidates → no double-spawn (same idempotency the 1d20 path relies on). The
	 * per-rail escape d20 is re-rolled on replay but never reached (no candidates).
	 *
	 * Returns true if the structure was destroyed (so the caller skips the 1d20
	 * crit — the rails are already gone). */
	public static function onRailStructureLost($rail, $ship, $gamedata){
		$structure = $rail->getStructureSystem();
		if (!$structure) return false;

		//Only the owning rail processes the block (per-structure dedup), so N
		//rails on one destroyed block escape exactly once each, not N×.
		$owner = self::railCritOwner($structure, $ship);
		if ($owner !== $rail) {
			//Non-owner: report whether the block is gone so the caller still
			//skips this rail's 1d20 (the owner did the escape work).
			return $structure->isDestroyed($gamedata->turn);
		}

		//Structure intact → nothing to do here; the 1d20 path handles damage.
		if (!$structure->isDestroyed($gamedata->turn)) return false;

		//Every rail on the dead block loses all its boxes. shedBoxesFromBay sheds
		//each rail's craft (escape per d20) and trims the host entries; a flight
		//spanning two rails on THIS block is shed across both per-rail passes (its
		//flightSize reaches 0 and the entry compacts). Re-homing onto a sibling is
		//suppressed for a sibling that is itself a destroyed-block rail (shedBoxes
		//FromBay's getRemainingHealth<=0 guard), so survivors don't migrate onto a
		//dying bay. Stage 21.3: replaces the pre-no-split railBoxEscape + blanket
		//hangarUsage clear, which lost foreign-occupancy survivors hosted elsewhere.
		foreach (self::collectRails($ship) as $r){
			if ($r->getStructureSystem() !== $structure) continue;
			self::shedBoxesFromBay($r, $ship, $gamedata);
		}
		return true;
	}

	/* Which rail on $structure the 16-20 crit destroys. The tabletop lets the
	 * OWNING PLAYER choose; a player-choice dialog is a future polish item
	 * (mirrors the Stage 18.6 auto-pick-then-optional-override note). Auto-pick
	 * heuristic (Stage 21.3, player-favourable):
	 *   1. Prefer an EMPTY rail (no stored craft — own or foreign occupancy): a
	 *      player would sacrifice a bare rail before one carrying fighters. Among
	 *      empties, pick the smallest remaining boxes.
	 *   2. If every rail on the block is occupied, fall back to the smallest
	 *      remaining-boxes rail (smallest sacrifice — e.g. a StrikeCarrier loses a
	 *      3-box rail before a 6-box one).
	 * Ties broken by encounter (collectRails) order for replay determinism. */
	public static function pickRailToDestroy($structure, $ship){
		$emptyTarget = null;  $emptyBoxes = PHP_INT_MAX;
		$anyTarget   = null;  $anyBoxes   = PHP_INT_MAX;
		foreach (self::collectRails($ship) as $rail){
			if ($rail->getStructureSystem() !== $structure) continue;
			$rem = (int)$rail->getRemainingHealth();
			if ($rem <= 0) continue;
			if ($rem < $anyBoxes){ $anyTarget = $rail; $anyBoxes = $rem; }
			//usageCountFor is occupancy-aware: empty means no own craft AND no foreign
			//occupancy boxes from a sibling-hosted multi-bay flight spilling here.
			//<= 0 (not === 0): a rail holding a single ultralight has fractional usage
			//(0.5) and is NOT empty — it must stay out of the empty-preferred set so a
			//crit sacrifices a bare rail before one carrying a fighter. A strict === 0
			//would also break on the float/int mismatch (0.0 === 0 is false in PHP).
			if (self::usageCountFor($rail) <= 0 && $rem < $emptyBoxes){
				$emptyTarget = $rail; $emptyBoxes = $rem;
			}
		}
		return $emptyTarget !== null ? $emptyTarget : $anyTarget;
	}

	/* Destroy an entire rail: mark all its remaining boxes destroyed via a single
	 * DamageEntry on the rail itself (replay-safe, no new note state — getRemaining
	 * Health recomputes capacity to 0 on reload), then shed every craft that lived
	 * in those boxes (escape attempt + host-entry shrink).
	 *
	 * Stage 21.3 (no-split): the boxes on this rail may belong to entries hosted on
	 * a SIBLING bay (occupancy spilled onto this rail) as well as to entries hosted
	 * here. shedBoxesFromBay handles both — it sheds from the correct host flight
	 * and trims the host entry's occupancy, leaving survivors on intact bays as ONE
	 * ship. Any own-entry stash that survives the shed is then cleared (its boxes are
	 * gone); foreign-occupancy entries were trimmed in place on their host bay. */
	public static function destroyEntireRail($rail, $ship, $gamedata){
		$remaining = (int)$rail->getRemainingHealth();
		if ($remaining > 0){
			$entry = new DamageEntry(-1, $ship->id, -1, $gamedata->turn, $rail->id, $remaining, 0, 0, -1, true, false, "Rail destroyed", "RailCrit");
			$entry->updated = true;
			$rail->damage[] = $entry;
		}

		//Shed the craft that occupied this rail's boxes (escape per d20) and trim /
		//re-home the host entries. shedBoxesFromBay owns the stash mutation now (it
		//re-homes a surviving multi-bay entry whose host WAS this rail onto a
		//surviving sibling), so we do NOT blanket-clear $rail->hangarUsage here —
		//that would drop survivors accounted via a sibling's occupancy.
		self::shedBoxesFromBay($rail, $ship, $gamedata);
	}

	/* Stage 21.3 — occupancy-aware lost-box shedding. A bay (rail) lost all its
	 * boxes; shed exactly the craft those boxes held, wherever their host entry
	 * lives. Under the no-split model a flight is ONE entry on its primary bay with
	 * an occupancy list spanning bays, so the boxes on $rail may belong to:
	 *   (a) an entry HOSTED on $rail (with or without occupancy), or
	 *   (b) an entry hosted on a SIBLING bay whose occupancy spills onto $rail.
	 * Either way we shed only the craft on THIS rail's boxes, run the escape d20 on
	 * them, and trim the host entry (occupancy line for $rail + flightSize) — the
	 * survivors stay docked on intact bays as the same ship. A multi-bay entry whose
	 * HOST was this rail but which keeps boxes on a sibling is re-homed onto that
	 * sibling (so it isn't lost when the rail's stash is gone).
	 *
	 * Replay-safe: the trim drops this rail's occupancy AND clears the destroyed
	 * rail's own hangarUsage of anything fully shed, so a replay scrub re-enters
	 * with the boxes already gone → zero candidates → no double spawn (same
	 * idempotency the old whole-rail clear relied on). */
	public static function shedBoxesFromBay($rail, $carrier, $gamedata){
		$railId = (int)$rail->id;
		//PASS 1 — identify every entry holding boxes on this rail, keyed by a STABLE
		//identity (host hangar id + dockedFlightId|phpclass|index) so the later trim
		//re-resolves the live index (a compaction on the same host shifts indices).
		//Walk ALL of the carrier's hangars (foreign-occupancy hosts live elsewhere);
		//also catch own no-occupancy entries hosted directly on the destroyed rail.
		$affected = array();
		foreach (self::collectHangars($carrier) as $h){
			if (!is_array($h->hangarUsage)) continue;
			foreach ($h->hangarUsage as $idx => $entry){
				$boxesHere = 0;
				if (!empty($entry['occupancy']) && is_array($entry['occupancy'])){
					$boxesHere = self::entryBoxesOnHangar($entry, $railId);
				} elseif ((int)$h->id === $railId){
					//Legacy / single-bay entry hosted directly on the destroyed rail.
					$boxesHere = self::boxesForEntry($entry);
				}
				if ($boxesHere <= 0) continue;
				//Don't clamp bpc to >=1: ultralights cost 0.5 box each, so a 1.5-box
				//rail holds 3 of them — ceil(1.5/0.5)=3 craft to shed, not ceil(1.5/1)=2.
				$bpc   = self::boxesPerCraftForEntry($entry);
				if ($bpc <= 0) $bpc = 1;
				$craftToShed = min((int)($entry['flightSize'] ?? 0), (int)ceil($boxesHere / $bpc));
				if ($craftToShed <= 0) continue;
				$affected[] = array(
					'host'       => $h,
					'dockedId'   => isset($entry['dockedFlightId']) ? (int)$entry['dockedFlightId'] : 0,
					'phpclass'   => (string)($entry['phpclass'] ?? ''),
					'origIdx'    => $idx,
					'entry'      => $entry,
					'shed'       => $craftToShed,
				);
			}
		}
		if (empty($affected)) return;

		//PASS 2 — escape attempt per affected entry (touches only flights/criticals,
		//never the hangarUsage arrays, so indices stay valid through this pass).
		foreach ($affected as $a){
			self::escapeShedCraft($carrier, $rail, $a['entry'], $a['shed'], $gamedata);
		}

		//PASS 3 — trim each host entry (re-resolved by identity, compaction-safe) and
		//re-home a surviving multi-bay entry whose host WAS the destroyed rail.
		foreach ($affected as $a){
			self::trimHostEntryAfterShed($a['host'], $a['dockedId'], $a['phpclass'], $a['origIdx'], $railId, $a['shed']);
		}
	}

	/* Run the escape d20 over the $k craft shed off a destroyed bay for one host
	 * entry: the first $maxEscape (most-ammo / least-damaged first) spawn as a
	 * "<name> - Split" flight at the carrier's hex with the rail-direction facing +
	 * the -50 next-turn penalty (rule 3: a forced evac IS penalised). Every shed
	 * craft (escapee or not) is disengaged on the docked ship so its roster shrinks
	 * by $k. Mirrors the partial-launch state-copy path (launchWholeFlight) but with
	 * an escape roll gating which of the $k fly free. The host-entry trim is done
	 * separately in PASS 3 (trimHostEntryAfterShed). */
	private static function escapeShedCraft($carrier, $rail, $entry, $k, $gamedata){
		$dockedId = isset($entry['dockedFlightId']) ? (int)$entry['dockedFlightId'] : 0;
		$flight   = $dockedId > 0 ? $gamedata->getShipById($dockedId) : null;
		if (!($flight instanceof FighterFlight)) return;   //anonymous/auto entry: no per-craft escape, trim only

		$roll = random_int(1, 20);	
		$maxEscape = self::computeEscapeCount($roll, $k);

		//Pre-shed active count for the proportional enhancement-value split below.
		$activeBefore = $flight->countActiveCraft($gamedata->turn);

		$shed = self::selectFightersForExtraction($flight, $k, $gamedata);
		if (empty($shed)) return;

		$escapees = ($maxEscape > 0) ? array_slice($shed, 0, $maxEscape) : array();
		if (!empty($escapees)){
			self::spawnRailEscapeFlight($carrier, $rail, $entry, $flight, $escapees, $gamedata);
		}
		//Every shed craft (escapee or not) leaves the docked ship's active roster.
		$shedCount = 0;
		foreach ($shed as $f){
			if ($f->isDestroyed($gamedata->turn)) continue;
			$crit = new DisengagedFighter(-1, $flight->id, $f->id, 'DisengagedFighter', $gamedata->turn);
			$crit->updated = true; $crit->newCrit = true;
			$f->criticals[] = $crit;
			$shedCount++;
		}

		//Stage 21.5 (value): the shed craft (escaped onto a "- Split" flight, or
		//dead) leave the docked remnant — drop the remnant's enhancement cost by
		//their proportional share so a surviving docked row doesn't keep the full
		//pointCostEnh (which would double-count the escape flight's carried share).
		//If the whole flight was shed the remnant disappears and this is moot.
		if ($shedCount > 0 && $activeBefore > 0 && $shedCount < $activeBefore){
			$totalEnh = (int)round((float)($flight->pointCostEnh ?? 0) + (float)($flight->pointCostEnh2 ?? 0));
			if ($totalEnh > 0){
				$remnantEnh = (int)round($totalEnh * ($activeBefore - $shedCount) / $activeBefore);
				$flight->pointCostEnh  = $remnantEnh;
				$flight->pointCostEnh2 = 0;
				Manager::insertSingleEnhValue($flight->id, $remnantEnh);
			}
		}
	}

	/* PASS 3 of shedBoxesFromBay: re-resolve the host entry by identity (its live
	 * index may have shifted from a prior trim's compaction), trim it by $k craft +
	 * drop the destroyed bay's occupancy line, then re-home it if it was hosted ON
	 * the destroyed rail but still has boxes on a surviving sibling. */
	private static function trimHostEntryAfterShed($host, $dockedId, $phpclass, $origIdx, $lostBayId, $k){
		$idx = self::resolveEntryIndex($host, $dockedId, $phpclass, $origIdx);
		if ($idx === null) return;

		//If the destroyed bay IS this entry's host, capture where the survivor will
		//re-home BEFORE the trim (which may collapse + unset the occupancy list).
		//The re-home target is the first surviving (boxes>0, not-destroyed) sibling
		//bay in the entry's occupancy other than the dead host. Full-block loss
		//destroys sibling rails too — skip any candidate with 0 remaining health so a
		//survivor never migrates onto a dying bay (a later per-rail pass sheds it).
		$reHomeTo = null;
		$hostIsLostBay = ((int)$host->id === (int)$lostBayId);
		if ($hostIsLostBay){
			$e0 = $host->hangarUsage[$idx];
			if (!empty($e0['occupancy']) && is_array($e0['occupancy'])){
				foreach ($e0['occupancy'] as $occ){
					$sid = (int)($occ['systemId'] ?? 0);
					if ($sid === (int)$lostBayId || (int)($occ['boxes'] ?? 0) <= 0) continue;
					$cand = self::hangarById($host->getUnit(), $sid);
					if (!$cand || $cand === $host || (int)$cand->getRemainingHealth() <= 0) continue;
					$reHomeTo = $cand;
					break;
				}
			}
		}

		self::trimEntryForLostBay($host, $idx, $lostBayId, $k);

		if (!$hostIsLostBay) return;   //sibling-hosted entry: trimmed in place, done.

		//The entry (if it survived) is still physically in the dead rail's stash.
		//Move it to the captured surviving sibling; if none survives, drop the residue
		//(its only remaining boxes were on bays that are all gone).
		$idx = self::resolveEntryIndex($host, $dockedId, $phpclass, $idx);
		if ($idx === null) return;   //fully shed (flightSize hit 0) — already compacted.
		$e = $host->hangarUsage[$idx];
		array_splice($host->hangarUsage, $idx, 1);
		$host->hangarUsage = array_values($host->hangarUsage);
		if ($reHomeTo) $reHomeTo->hangarUsage[] = $e;
	}

	/* Find a stash entry's current index on $host by (dockedFlightId, phpclass),
	 * preferring $hintIdx when it still matches (cheap fast path). Returns null if
	 * the entry is gone (fully shed/compacted). */
	private static function resolveEntryIndex($host, $dockedId, $phpclass, $hintIdx){
		if (!is_array($host->hangarUsage)) return null;
		$matches = function($e) use ($dockedId, $phpclass){
			if (($e['phpclass'] ?? '') !== $phpclass) return false;
			return (int)($e['dockedFlightId'] ?? 0) === (int)$dockedId;
		};
		if (isset($host->hangarUsage[$hintIdx]) && $matches($host->hangarUsage[$hintIdx])) return $hintIdx;
		foreach ($host->hangarUsage as $i => $e){
			if ($matches($e)) return $i;
		}
		return null;
	}

	/* Hangar (incl. rail/catapult) on $ship by system id, or null. */
	private static function hangarById($ship, $id){
		if (!$ship) return null;
		foreach (self::collectHangars($ship) as $h){
			if ((int)$h->id === (int)$id) return $h;
		}
		return null;
	}

	/* Spawn a "<name> - Split" escape flight carrying the chosen escapee fighters'
	 * state, at the carrier's current hex with (facing + rail direction) and the
	 * -50 LaunchedThisTurn penalty (rule 3: a forced rail evac IS penalised next
	 * turn, unlike a clean rail launch). Mirrors spawnLaunchedKFlight + the partial
	 * launch state-copy, but applies escape (penalised) crits. Returns the new id. */
	private static function spawnRailEscapeFlight($carrier, $rail, $entry, $sourceFlight, $escapees, $gamedata){
		$phpclass = (string)($entry['phpclass'] ?? '');
		$count = count($escapees);
		if ($phpclass === '' || !class_exists($phpclass) || $count <= 0) return null;

		$lastMove = $carrier->getLastMovement();
		if (!$lastMove) return null;
		$escDir = (int)$rail->direction;
		if (is_array($rail->directions) && !empty($rail->directions)) $escDir = (int)$rail->directions[0];
		$spawnPos = $lastMove->position;
		$heading  = (int)$lastMove->heading;
		$facing   = ((((int)$lastMove->facing + $escDir) % 6) + 6) % 6;
		$speed    = (int)$lastMove->speed;

		$flightName = self::dockedSplitName($entry);
		if ($flightName === null) $flightName = self::flightNameFor($phpclass, $carrier);
		$escapeFlight = self::spawnLaunchedKFlight($phpclass, $count, $flightName, $carrier, $spawnPos, $heading, $facing, $speed, $sourceFlight, $gamedata);
		if (!$escapeFlight) return null;

		//Copy the chosen escapees' ammo/damage/crit state onto the new flight.
		self::copyFlightAmmoEnhancements($sourceFlight, $escapeFlight, $gamedata);
		$targetFighters = array();
		foreach ($escapeFlight->systems as $f){ if ($f instanceof Fighter) $targetFighters[] = $f; }
		for ($i = 0; $i < count($escapees); $i++){
			if (!isset($targetFighters[$i])) break;
			self::copyFighterStateToTarget($escapees[$i], $targetFighters[$i], $escapeFlight, $gamedata);
		}

		//A forced rail evac IS penalised next turn (NOT a clean rail launch).
		self::applyEscapeCrits($escapeFlight, $gamedata);
		self::writeEscapeEventNote($carrier, $rail, $escapeFlight, $count, 'railEvac', $gamedata);
		return $escapeFlight->id;
	}

	/* Trim a host entry because bay $lostBayId was destroyed: flightSize -$k and
	 * remove $k*bpc boxes from the occupancy line for $lostBayId SPECIFICALLY (the
	 * destroyed bay), unlike shrinkDockedEntry which trims smallest-bay-first. If the
	 * entry empties, compact it away. */
	private static function trimEntryForLostBay($host, $entryIdx, $lostBayId, $k){
		if (!is_array($host->hangarUsage) || !isset($host->hangarUsage[$entryIdx])) return;
		$e = $host->hangarUsage[$entryIdx];
		//Fractional-safe bpc (ultralight 0.5); free only whole boxes the removed
		//craft fully vacate (floor) so a leftover half-box stays reserved.
		$bpc = self::boxesPerCraftForEntry($e);
		if ($bpc <= 0) $bpc = 1;
		$e['flightSize'] = max(0, (int)($e['flightSize'] ?? 0) - (int)$k);
		$boxesToFree = (int)floor((int)$k * $bpc);

		if (!empty($e['occupancy']) && is_array($e['occupancy'])){
			$occ = array();
			foreach ($e['occupancy'] as $o){
				$b = (int)($o['boxes'] ?? 0);
				if ((int)($o['systemId'] ?? 0) === (int)$lostBayId && $boxesToFree > 0){
					$drop = min($b, $boxesToFree);
					$b -= $drop; $boxesToFree -= $drop;
				}
				if ($b > 0) $occ[] = array('systemId' => (int)$o['systemId'], 'boxes' => $b);
			}
			//A single surviving bay no longer needs an occupancy list (it's a plain
			//single-bay entry again); >1 keeps the multi-bay shape.
			if (count($occ) > 1) $e['occupancy'] = $occ; else unset($e['occupancy']);
		}

		if ((int)$e['flightSize'] <= 0){
			array_splice($host->hangarUsage, $entryIdx, 1);
			$host->hangarUsage = array_values($host->hangarUsage);
		} else {
			$host->hangarUsage[$entryIdx] = $e;
		}
	}

	/* Persist the rail's 1d20 result on the owning rail for replay determinism.
	 * Written ONLY from setCriticals (the live Fire Phase advance), never from a
	 * player submission, so the Stage-15 POST-side-reconstruction clobber trap
	 * doesn't apply. notekey_human kept <= 40 chars (tac_individual_notes column). */
	private static function recordRailCritRoll($rail, $roll, $gamedata){
		$ship = $rail->getUnit();
		$shipId = $ship ? $ship->id : 0;
		$payload = json_encode(array('roll' => (int)$roll, 'turn' => (int)$gamedata->turn));
		$note = new IndividualNote(
			-1,
			$gamedata->id,
			$gamedata->turn,
			$gamedata->phase,
			$shipId,
			$rail->id,
			'railCritRoll',
			'Fighter rail critical roll',
			$payload
		);
		Manager::insertIndividualNote($note);
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
		//The primary hangar carries the carrier-level ordnance/marine pools
		//(Stage 15/17.1) and the Stage 18 escape-roll note. A FighterRail is
		//structure-coupled and frequently destroyed, so it must never become the
		//primary — pick the first NON-rail hangar regardless of ship-systems
		//order. (Falls back to the first rail only on a hypothetical rails-only
		//carrier; no such ship exists today — the StrikeCarrier has a real Hangar.)
		$firstAny = null;
		foreach (self::collectHangars($carrier) as $h){
			if ($firstAny === null) $firstAny = $h;
			if (empty($h->isRail)) return $h;
		}
		return $firstAny;
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
	public static function performLaunch($hangar, $carrier, $phpclass, $launchSize, $gamedata, $directionOverride = null){
		if (!is_string($phpclass) || $phpclass === '') return null;
		if (!class_exists($phpclass)) return null;
		$launchSize = max(1, (int)$launchSize);

		$lastMove = $carrier->getLastMovement();
		if (!$lastMove) return null;

		//Stage 8.5: per-launch direction override (from the multi-direction
		//picker) wins over the hangar's static $direction. Caller is
		//responsible for validating the override is one of $hangar->directions.
		$effDir = ($directionOverride !== null) ? (int)$directionOverride : (int)$hangar->direction;
		$spawnPos = $lastMove->position;
		$heading  = (int)$lastMove->heading;
		$facing   = ((int)$lastMove->facing + $effDir) % 6;
		if ($facing < 0) $facing += 6;
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
			//carrier). Fighter Rails skip ONLY the flight-side -50 (independent
			//launch, no day-after penalty) but the carrier still eats the -20.
			//Ordinary hangar launches apply both.
			if (empty($hangar->isCatapult)) self::applyLaunchCrits($resurrectedFlight, $carrier, $gamedata, !empty($hangar->isRail));
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
		//Fighter Rails skip only the flight-side -50, keep the carrier -20.
		if (empty($hangar->isCatapult)) self::applyLaunchCrits($flight, $carrier, $gamedata, !empty($hangar->isRail));
		return $shipid;
	}

	/* Apply initiative-penalty criticals after a successful launch:
	 * - LaunchedThisTurn (-50 ini) on the new flight's first fighter
	 * - HangarOperations (-20 ini) on the carrier's CnC (via shared helper)
	 */
	/* Apply launch-time initiative criticals.
	 *
	 * Two penalties exist (B5W §10.1):
	 *   - LaunchedThisTurn (-50) on the launched flight, expiring next turn.
	 *   - HangarOperations (-20) on the carrier's CnC (idempotent per turn).
	 *
	 * $skipFlightCrit suppresses ONLY the flight-side LaunchedThisTurn — used
	 * by Fighter Rails, whose fighters launch independently with NO day-after
	 * penalty, while the carrier STILL takes the normal launch/land -20 that
	 * turn. (Catapults skip BOTH penalties; their call sites suppress this
	 * whole call instead.)
	 */
	private static function applyLaunchCrits($flight, $carrier, $gamedata, $skipFlightCrit = false){
		if (!$skipFlightCrit && $flight instanceof FighterFlight) {
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

	/* === Stage 21: no-split whole-flight launch ========================= */

	/*
	 * Carrier-level launch coalescer (once per carrier per turn). A docked flight
	 * is ONE entry (on its primary bay, occupancy spanning bays), so a launch
	 * order queued on ANY bay must be resolved against the entry wherever it
	 * lives. Each order carries the docked flight's id (dockedFlightId) so it
	 * targets the exact flight (disambiguating two same-class docked flights).
	 *
	 *  - Full launch (size >= entry flightSize): resurrect the docked ship
	 *    (un-remove, deploy move, drop the whole entry incl. occupancy).
	 *  - Partial launch (size < flightSize): spawn a fresh "<source> - Split"
	 *    K-flight with the most-ammo/least-damaged K fighters' state, and shrink
	 *    the original docked ship IN PLACE to N-K (disengage those K fighters on
	 *    it; reduce the entry's flightSize + trim its occupancy boxes). No
	 *    fragment ship — the remnant IS the original docked ship.
	 *
	 * Budget (launchedThisTurn) is charged across the entry's occupancy bays.
	 * Guarded by $carrier->launchCoalesceDone (transient).
	 */
	public static function processWholeFlightLaunches($carrier, $gamedata){
		if (!empty($carrier->launchCoalesceDone)) return;
		$carrier->launchCoalesceDone = true;
		if (!self::isFlowEnabled($gamedata->id)) return;

		$hangars = self::collectHangars($carrier);
		if (empty($hangars)) return;

		//Gather every non-catapult bay's launch orders (catapults launch via their
		//own per-bay processLaunchOrders). Preserve order so direction picks apply.
		$orders = array();
		foreach ($hangars as $hangar){
			if (!empty($hangar->isCatapult)) continue;
			if (empty($hangar->pendingLaunchOrder)) continue;
			foreach ($hangar->pendingLaunchOrder as $o){ $orders[] = array('hangar' => $hangar, 'order' => $o); }
			$hangar->pendingLaunchOrder = null;   //consumed by the coalescer
		}

		foreach ($orders as $oi){
			$orderHangar = $oi['hangar'];
			$o = $oi['order'];
			$phpclass = isset($o['phpclass']) ? (string)$o['phpclass'] : '';
			$size     = isset($o['size'])     ? (int)$o['size']        : 0;
			$dfid     = isset($o['dockedFlightId']) ? (int)$o['dockedFlightId'] : 0;
			if ($phpclass === '' || $size <= 0) continue;

			//Direction override validated against the ORDER's hangar advertised dirs.
			$dirOverride = null;
			if (isset($o['direction']) && is_array($orderHangar->directions) && !empty($orderHangar->directions)){
				$d = ((((int)$o['direction']) % 6) + 6) % 6;
				if (in_array($d, array_map('intval', $orderHangar->directions), true)) $dirOverride = $d;
			}

			//ANONYMOUS stash (no dockedFlightId) = auto-filled shuttles / orphans.
			//These aren't a resurrectable docked ship — fresh-spawn a new flight
			//and decrement ONLY the specific anonymous entry (never a docked one).
			if ($dfid <= 0){
				self::launchAnonymousStash($carrier, $orderHangar, $phpclass, $size, $dirOverride, $gamedata);
				continue;
			}

			//TARGETED docked flight — resolve by dockedFlightId across all bays.
			$loc = self::findDockedEntry($carrier, $dfid, $phpclass);
			if (!$loc){
				self::launchFailNote($carrier, $orderHangar, $phpclass, $size, 'no such docked flight', $gamedata);
				continue;
			}
			$entryHangar = $loc['hangar'];
			$entry       = $loc['entry'];
			if (!empty($entry['cannotLaunch'])){
				self::launchFailNote($carrier, $orderHangar, $phpclass, $size, 'craft wrecked — cannot relaunch', $gamedata);
				continue;
			}

			$reason = null;
			if (!self::canLaunchWholeFlight($carrier, $entry, $size, $gamedata, $reason)){
				self::launchFailNote($carrier, $orderHangar, $phpclass, $size, $reason, $gamedata);
				continue;
			}

			self::launchWholeFlight($carrier, $entryHangar, $entry, $size, $dirOverride, $gamedata);
		}
	}

	/* Find the first ANONYMOUS (no dockedFlightId) launchable stash entry of
	 * $phpclass across $carrier's bays — auto-filled shuttles / orphans that
	 * fresh-spawn on launch. Returns {hangar, entry, idx} or null. */
	public static function findAnonymousStash($carrier, $phpclass){
		foreach (self::collectHangars($carrier) as $h){
			if (!is_array($h->hangarUsage)) continue;
			foreach ($h->hangarUsage as $idx => $entry){
				if (($entry['phpclass'] ?? '') !== $phpclass) continue;
				if (!empty($entry['cannotLaunch'])) continue;
				if ((int)($entry['dockedFlightId'] ?? 0) > 0) continue;   //skip docked flights
				return array('hangar' => $h, 'entry' => $entry, 'idx' => $idx);
			}
		}
		return null;
	}

	/* Launch $size craft of an ANONYMOUS $phpclass stash (auto-shuttles/orphans)
	 * as a fresh flight. Decrements ONLY anonymous entries (never a docked one),
	 * spawns a clean flight, charges launch budget on the draining bay, and
	 * applies launch crits. */
	public static function launchAnonymousStash($carrier, $orderHangar, $phpclass, $size, $dirOverride, $gamedata){
		$size = max(1, (int)$size);

		//Total available anonymous craft of this phpclass + first holding bay.
		$avail = 0; $firstBay = null;
		foreach (self::collectHangars($carrier) as $h){
			if (!is_array($h->hangarUsage)) continue;
			foreach ($h->hangarUsage as $e){
				if (($e['phpclass'] ?? '') !== $phpclass) continue;
				if (!empty($e['cannotLaunch'])) continue;
				if ((int)($e['dockedFlightId'] ?? 0) > 0) continue;
				$avail += (int)($e['flightSize'] ?? 1);
				if ($firstBay === null) $firstBay = $h;
			}
		}
		if ($firstBay === null || $avail <= 0){
			self::launchFailNote($carrier, $orderHangar, $phpclass, $size, 'no stored craft', $gamedata);
			return;
		}
		if (Movement::isPivoting($carrier, $gamedata->turn, $gamedata) || Movement::isRolling($carrier, $gamedata->turn, $gamedata)){
			self::launchFailNote($carrier, $orderHangar, $phpclass, $size, 'carrier pivoting/rolling', $gamedata);
			return;
		}
		if ($size > $avail) $size = $avail;
		//Budget on the draining bay.
		$head = (int)$firstBay->output - ((int)$firstBay->launchedThisTurn + (int)$firstBay->landedThisTurn);
		if ($head < $size){
			self::launchFailNote($carrier, $orderHangar, $phpclass, $size, 'launch rate exceeded', $gamedata);
			return;
		}

		//Spawn geometry from the carrier's last move + the draining bay's direction.
		$lastMove = $carrier->getLastMovement();
		if (!$lastMove) return;
		$effDir   = ($dirOverride !== null) ? (int)$dirOverride : (int)$firstBay->direction;
		$spawnPos = $lastMove->position;
		$heading  = (int)$lastMove->heading;
		$facing   = ((((int)$lastMove->facing + $effDir) % 6) + 6) % 6;
		$speed    = (int)$lastMove->speed;

		$flightName = self::flightNameFor($phpclass, $carrier);
		$flight = self::spawnLaunchedKFlight($phpclass, $size, $flightName, $carrier, $spawnPos, $heading, $facing, $speed, /*sourceDocked*/ $carrier, $gamedata);
		if (!$flight) return;

		//Decrement $size from anonymous entries (in encounter order), dropping
		//emptied ones. Charge budget on the bays drained.
		$remaining = $size;
		foreach (self::collectHangars($carrier) as $h){
			if ($remaining <= 0) break;
			if (!is_array($h->hangarUsage)) continue;
			$newUsage = array();
			foreach ($h->hangarUsage as $e){
				if ($remaining > 0 && ($e['phpclass'] ?? '') === $phpclass
					&& empty($e['cannotLaunch']) && (int)($e['dockedFlightId'] ?? 0) <= 0){
					$es = (int)($e['flightSize'] ?? 1);
					$take = min($es, $remaining);
					$remaining -= $take;
					$h->launchedThisTurn += $take;
					$es -= $take;
					if ($es > 0){ $e['flightSize'] = $es; $newUsage[] = $e; }
					//emptied → dropped
				} else {
					$newUsage[] = $e;
				}
			}
			$h->hangarUsage = $newUsage;
		}

		$note = new IndividualNote(
			-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
			$carrier->id, $firstBay->id,
			'hangarLaunchEvent', 'Hangar launched flight',
			$flight->id . ':' . $phpclass . ':' . $size
		);
		Manager::insertIndividualNote($note);

		//Shuttles launch from ordinary bays/rails → rail skips flight-side -50.
		self::applyLaunchCrits($flight, $carrier, $gamedata, !empty($firstBay->isRail));
	}

	/* Find the docked-flight stash entry across all of $carrier's bays. Matches
	 * by dockedFlightId when $dfid > 0; otherwise the first df-linked entry of
	 * $phpclass (legacy order with no id). Returns {hangar, entry, idx} or null. */
	public static function findDockedEntry($carrier, $dfid, $phpclass){
		foreach (self::collectHangars($carrier) as $h){
			if (!is_array($h->hangarUsage)) continue;
			foreach ($h->hangarUsage as $idx => $entry){
				if (($entry['phpclass'] ?? '') !== $phpclass) continue;
				$eId = isset($entry['dockedFlightId']) ? (int)$entry['dockedFlightId'] : 0;
				if ($dfid > 0){
					if ($eId === $dfid) return array('hangar' => $h, 'entry' => $entry, 'idx' => $idx);
				} else {
					if ($eId > 0) return array('hangar' => $h, 'entry' => $entry, 'idx' => $idx);
				}
			}
		}
		return null;
	}

	/* Validate a whole-flight launch of $size craft from a docked $entry RIGHT
	 * NOW: carrier not pivoting/rolling, the entry holds >= $size craft, and the
	 * shared launch+land budget across the entry's occupancy bays has headroom. */
	public static function canLaunchWholeFlight($carrier, $entry, $size, $gamedata, &$reason = null){
		$size = (int)$size;
		if ($size <= 0){ $reason = 'invalid size'; return false; }
		if (Movement::isPivoting($carrier, $gamedata->turn, $gamedata) || Movement::isRolling($carrier, $gamedata->turn, $gamedata)){
			$reason = 'carrier pivoting/rolling'; return false;
		}
		$have = (int)($entry['flightSize'] ?? 0);
		if ($have < $size){ $reason = 'not enough stored craft'; return false; }

		//Budget: a launch of $size DRAWS $size craft, distributed smallest-bay-
		//first across the entry's occupancy (matching the fighter drain + the
		//client display). Each bay is charged only what is drawn FROM it, and must
		//have that much launch-rate headroom.
		$bays = self::occupancyBaysFor($entry, $carrier);
		if (empty($bays)) return true;   //legacy single-bay; checked in launchWholeFlight
		$bpc = self::boxesPerCraftForEntry($entry);   //fractional-safe (ultralight 0.5)
		$charge = self::distributeCraftAcrossBays($bays, $size, $bpc);   //hangarId => craft
		foreach ($bays as $b){
			$h = $b['hangar'];
			if (!empty($h->isCatapult)) continue;
			$need = (int)($charge[(int)$h->id] ?? 0);
			if ($need <= 0) continue;
			$head = (int)$h->output - ((int)$h->launchedThisTurn + (int)$h->landedThisTurn);
			if ($head < $need){ $reason = 'launch rate exceeded'; return false; }
		}
		return true;
	}

	/* Distribute $k craft across $bays (each {hangar, boxes}) smallest-bay-first,
	 * returning hangarId => craft drawn. Matches shrinkDockedEntry's drain order
	 * and the client's live-budget display. */
	private static function distributeCraftAcrossBays($bays, $k, $bpc){
		//bpc may be fractional (ultralight, 0.5) — DON'T clamp to an int, or a bay's
		//box→craft conversion (floor(boxes / bpc)) would halve an ultralight bay's
		//capacity. Only guard a non-positive value.
		$bpc = (float)$bpc;
		if ($bpc <= 0) $bpc = 1;
		$sorted = $bays;
		usort($sorted, function($a, $b){ return (int)$a['boxes'] - (int)$b['boxes']; });
		$out = array();
		$remaining = (int)$k;
		foreach ($sorted as $b){
			if ($remaining <= 0) break;
			$bayCraft = (int)floor((int)$b['boxes'] / $bpc);
			$take = min($remaining, $bayCraft);
			if ($take <= 0) continue;
			$out[(int)$b['hangar']->id] = ($out[(int)$b['hangar']->id] ?? 0) + $take;
			$remaining -= $take;
		}
		return $out;
	}

	/* Resolve an $entry's occupancy bays to [{hangar, boxes}]. For a legacy
	 * single-bay entry (no occupancy) returns [] (caller treats its own hangar
	 * as the sole bay). */
	public static function occupancyBaysFor($entry, $carrier){
		if (empty($entry['occupancy']) || !is_array($entry['occupancy'])) return array();
		$out = array();
		$byId = array();
		foreach (self::collectHangars($carrier) as $h){ $byId[(int)$h->id] = $h; }
		foreach ($entry['occupancy'] as $occ){
			$id = (int)($occ['systemId'] ?? 0);
			if (isset($byId[$id])) $out[] = array('hangar' => $byId[$id], 'boxes' => (int)($occ['boxes'] ?? 0));
		}
		return $out;
	}

	private static function launchFailNote($carrier, $hangar, $phpclass, $size, $reason, $gamedata){
		$note = new IndividualNote(
			-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
			$carrier->id, ($hangar ? $hangar->id : 0),
			'hangarLaunchEvent', 'Hangar launch failed',
			'fail:' . $phpclass . ':' . $size . ':' . ($reason ?? 'unknown')
		);
		Manager::insertIndividualNote($note);
	}

	/* Launch $size craft of the docked flight described by $entry (which lives on
	 * $entryHangar->hangarUsage). Full launch = resurrect the original ship and
	 * drop the entry; partial = spawn a "<source> - Split" K-flight + shrink the
	 * original docked ship + entry in place. Charges budget across occupancy bays
	 * and applies launch crits. */
	public static function launchWholeFlight($carrier, $entryHangar, $entry, $size, $dirOverride, $gamedata){
		$phpclass   = (string)($entry['phpclass'] ?? '');
		$dockedId   = isset($entry['dockedFlightId']) ? (int)$entry['dockedFlightId'] : 0;
		$flightSize = (int)($entry['flightSize'] ?? 0);
		$size = max(1, min((int)$size, $flightSize));
		if ($dockedId <= 0 || $size <= 0) return null;

		$docked = $gamedata->getShipById($dockedId);
		if (!($docked instanceof FighterFlight)) return null;

		$lastMove = $carrier->getLastMovement();
		if (!$lastMove) return null;
		//Spawn geometry. Rails advertise per-launch direction; a multi-bay launch
		//uses the override (validated) or the entry-hangar's static direction.
		$effDir   = ($dirOverride !== null) ? (int)$dirOverride : (int)$entryHangar->direction;
		$spawnPos = $lastMove->position;
		$heading  = (int)$lastMove->heading;
		$facing   = ((((int)$lastMove->facing + $effDir) % 6) + 6) % 6;
		$speed    = (int)$lastMove->speed;

		//Rail discriminator for the day-after init penalty: a launch is rail-only
		//(skips the flight-side -50) when EVERY occupancy bay is a rail. A legacy
		//single-bay entry uses its own hangar's flag.
		$bays = self::occupancyBaysFor($entry, $carrier);
		if (empty($bays)) $bays = array(array('hangar' => $entryHangar, 'boxes' => self::boxesForEntry($entry)));
		$allRail = true;
		foreach ($bays as $b){ if (empty($b['hangar']->isRail)) { $allRail = false; break; } }

		$full = ($size >= $flightSize);

		if ($full){
			//FULL LAUNCH — resurrect the original docked ship; drop the whole entry.
			$docked->removed = false;
			$docked->removedTurn = null;
			$docked->spawned = $gamedata->turn;
			$deployMove = new MovementOrder(null, "deploy", $spawnPos, 0, 0, $speed, $heading, $facing, false, $gamedata->turn, 0, 0);
			Manager::insertSingleMovement($gamedata->id, $docked->id, $deployMove);

			self::removeEntryFromHangar($entryHangar, $dockedId, $phpclass);
			self::chargeLaunchBudget($bays, $size, $entry);

			$note = new IndividualNote(
				-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
				$carrier->id, $entryHangar->id,
				'hangarLaunchEvent', 'Hangar relaunched docked flight',
				$docked->id . ':' . $phpclass . ':' . $size . ':resurrected'
			);
			Manager::insertIndividualNote($note);

			self::applyLaunchCrits($docked, $carrier, $gamedata, $allRail);
			return $docked->id;
		}

		//PARTIAL LAUNCH — spawn a fresh K-flight; shrink the original docked ship.
		$flightName = self::dockedSplitName($entry);
		$launched = self::spawnLaunchedKFlight($phpclass, $size, $flightName, $carrier, $spawnPos, $heading, $facing, $speed, $docked, $gamedata);
		if (!$launched) return null;

		//Choose the K fighters that leave (most-ammo / least-damaged first), copy
		//their state onto the launched flight, and disengage them on the docked
		//ship so it shrinks to N-K active craft (the remnant stays the same ship).
		self::copyFlightAmmoEnhancements($docked, $launched, $gamedata);
		$chosen = self::selectFightersForExtraction($docked, $size, $gamedata);
		$launchedFighters = array();
		foreach ($launched->systems as $f){ if ($f instanceof Fighter) $launchedFighters[] = $f; }
		for ($i = 0; $i < count($chosen); $i++){
			if (!isset($launchedFighters[$i])) break;
			self::copyFighterStateToTarget($chosen[$i], $launchedFighters[$i], $launched, $gamedata);
		}
		//Disengage the chosen K on the docked ship (they left). Stamp DockedFighter
		//is wrong here — they DEPARTED, so DisengagedFighter marks them gone from
		//the docked ship's active roster (its flightSize shrinks accordingly).
		foreach ($chosen as $f){
			if ($f->isDestroyed($gamedata->turn)) continue;
			$crit = new DisengagedFighter(-1, $docked->id, $f->id, 'DisengagedFighter', $gamedata->turn);
			$crit->updated = true; $crit->newCrit = true;
			$f->criticals[] = $crit;
		}

		//Shrink the entry: flightSize -K, trim occupancy boxes K*bpc smallest-bay-first.
		self::shrinkDockedEntry($entryHangar, $dockedId, $phpclass, $size, $entry);
		self::chargeLaunchBudget($bays, $size, $entry);

		//Stage 21.5 (value): the launched K-flight took a proportional share of the
		//enhancement cost (spawnLaunchedKFlight set its pointCostEnh = round(total *
		//K/N)). Subtract exactly that from the docked remnant so the enhancement
		//value isn't double-counted across the two rows (remnant kept the FULL
		//pointCostEnh before this — fleet-list showed the carrier's docked row + the
		//launched row each carrying the whole enhancement). Conserve by remainder
		//(total - launched share) rather than re-rounding, so the two always sum back.
		$totalEnh = (int)round((float)($docked->pointCostEnh ?? 0) + (float)($docked->pointCostEnh2 ?? 0));
		$remnantEnh = max(0, $totalEnh - (int)($launched->pointCostEnh ?? 0));
		if ($remnantEnh !== $totalEnh){
			$docked->pointCostEnh  = $remnantEnh;
			$docked->pointCostEnh2 = 0;
			//Persist on the existing ship row (enhvalue column is the load source).
			Manager::insertSingleEnhValue($docked->id, $remnantEnh);
		}

		$note = new IndividualNote(
			-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
			$carrier->id, $entryHangar->id,
			'hangarLaunchEvent', 'Hangar launched partial flight',
			$launched->id . ':' . $phpclass . ':' . $size . ':split'
		);
		Manager::insertIndividualNote($note);

		self::applyLaunchCrits($launched, $carrier, $gamedata, $allRail);
		return $launched->id;
	}

	/* Name for a partial-launched detachment: "<docked name> - Split" (deduped). */
	private static function dockedSplitName($entry){
		$base = (string)($entry['name'] ?? '');
		if ($base === '') return null;
		if (strpos($base, ' - Split') !== false) return $base;
		return $base . ' - Split';
	}

	/* Charge $size against launchedThisTurn on each occupancy bay, capped by the
	 * craft that bay physically holds (boxes / boxesPerCraft). */
	private static function chargeLaunchBudget($bays, $size, $entry){
		$bpc = self::boxesPerCraftForEntry($entry);   //fractional-safe (ultralight 0.5)
		//Charge each bay only the craft DRAWN from it (smallest-bay-first),
		//matching the fighter drain + the client's live budget display.
		$charge = self::distributeCraftAcrossBays($bays, $size, $bpc);
		foreach ($bays as $b){
			$h = $b['hangar'];
			$c = (int)($charge[(int)$h->id] ?? 0);
			if ($c > 0) $h->launchedThisTurn += $c;
		}
	}

	/* Drop the whole docked entry (by dockedFlightId) from $entryHangar. */
	private static function removeEntryFromHangar($entryHangar, $dockedId, $phpclass){
		if (!is_array($entryHangar->hangarUsage)) return;
		foreach ($entryHangar->hangarUsage as $idx => $e){
			if (($e['phpclass'] ?? '') !== $phpclass) continue;
			if ((int)($e['dockedFlightId'] ?? 0) !== (int)$dockedId) continue;
			array_splice($entryHangar->hangarUsage, $idx, 1);
			$entryHangar->hangarUsage = array_values($entryHangar->hangarUsage);
			return;
		}
	}

	/* Shrink the docked entry by $k craft: flightSize -k and trim k*bpc boxes off
	 * the occupancy (smallest-bay-first, so small rails free up whole). */
	private static function shrinkDockedEntry($entryHangar, $dockedId, $phpclass, $k, $entryHint){
		if (!is_array($entryHangar->hangarUsage)) return;
		foreach ($entryHangar->hangarUsage as $idx => $e){
			if (($e['phpclass'] ?? '') !== $phpclass) continue;
			if ((int)($e['dockedFlightId'] ?? 0) !== (int)$dockedId) continue;

			//bpc may be fractional (ultralight 0.5). Free only WHOLE boxes that the
			//removed craft fully vacate: floor(k*bpc). A leftover half-box (odd
			//ultralight count) stays reserved until its last craft leaves (removeEntry
			//FromHangar handles full removal), so a box is never freed while it still
			//holds a craft.
			$bpc = self::boxesPerCraftForEntry($e);
			if ($bpc <= 0) $bpc = 1;
			$e['flightSize'] = max(0, (int)$e['flightSize'] - (int)$k);
			$boxesToFree = (int)floor((int)$k * $bpc);

			if (!empty($e['occupancy']) && is_array($e['occupancy'])){
				//Smallest-bay-first so a small rail clears entirely.
				usort($e['occupancy'], function($a, $b){ return (int)$a['boxes'] - (int)$b['boxes']; });
				$occ = array();
				foreach ($e['occupancy'] as $o){
					$b = (int)$o['boxes'];
					if ($boxesToFree > 0){
						$drop = min($b, $boxesToFree);
						$b -= $drop; $boxesToFree -= $drop;
					}
					if ($b > 0) $occ[] = array('systemId' => (int)$o['systemId'], 'boxes' => $b);
				}
				if (count($occ) > 1) $e['occupancy'] = $occ; else unset($e['occupancy']);
			}

			if ((int)$e['flightSize'] <= 0){
				array_splice($entryHangar->hangarUsage, $idx, 1);
				$entryHangar->hangarUsage = array_values($entryHangar->hangarUsage);
			} else {
				$entryHangar->hangarUsage[$idx] = $e;
			}
			return;
		}
	}

	/* Spawn a fresh K-fighter launched flight (partial launch). Mirrors the
	 * new-spawn block of performLaunch: insert ship + flightsize + deploy move +
	 * per-system data (weapons fully charged). Carries pointCostEnh proportionally
	 * from the docked source so fleet-list value is right. */
	private static function spawnLaunchedKFlight($phpclass, $k, $flightName, $carrier, $spawnPos, $heading, $facing, $speed, $sourceDocked, $gamedata){
		if (!is_string($phpclass) || $phpclass === '' || !class_exists($phpclass)) return null;
		$k = max(1, (int)$k);
		if ($flightName === null || $flightName === '') $flightName = self::flightNameFor($phpclass, $carrier);

		$flight = new $phpclass($gamedata->id, $carrier->userid, $flightName, $carrier->slot);
		$flight->team = $carrier->team;
		//Proportional enhancement cost from the source docked flight, when there
		//is one (partial launch). Anonymous shuttle launches pass no real source
		//(no enhancements to carry) — guarded by the FighterFlight check.
		if ($sourceDocked instanceof FighterFlight){
			$srcSize = (int)$sourceDocked->flightSize;
			$srcEnh  = (float)($sourceDocked->pointCostEnh ?? 0) + (float)($sourceDocked->pointCostEnh2 ?? 0);
			if ($srcSize > 0 && $srcEnh > 0){
				$flight->pointCostEnh = (int)round($srcEnh * $k / $srcSize);
				$flight->pointCostEnh2 = 0;
			}
		}
		if ($k > $flight->flightSize){
			$flight->flightSize = $k;
			if (method_exists($flight, 'populate')) $flight->populate();
		}

		$shipid = Manager::insertSingleShip($gamedata, $flight, $carrier->userid);
		$flight->id = $shipid;
		$flight->spawned = $gamedata->turn;
		Manager::insertSingleFlightSize($gamedata->id, $shipid, $flight->flightSize);

		$deployMove = new MovementOrder(null, "deploy", $spawnPos, 0, 0, $speed, $heading, $facing, false, $gamedata->turn, 0, 0);
		Manager::insertSingleMovement($gamedata->id, $shipid, $deployMove);

		SystemData::initSystemData($gamedata->turn, $gamedata->id);
		foreach ($flight->systems as $craft){
			if (!($craft instanceof Fighter)) continue;
			$craft->setInitialSystemData($flight);
			if (!isset($craft->systems) || !is_array($craft->systems)) continue;
			foreach ($craft->systems as $sys){
				$sys->setInitialSystemData($flight);
				if ($sys instanceof Weapon){
					$load = $sys->getStartLoading();
					if ($load){ $load->loading = $sys->loadingtime; SystemData::addDataForSystem($sys->id, 0, $shipid, $load->toJSON()); }
				}
			}
		}
		Manager::insertSystemData(SystemData::getAndPurgeAllSystemData());
		return $flight;
	}

	/* Resolve queued launch orders for a hangar at end of turn.
	 * Reads $hangar->pendingLaunchOrder (populated from the latest
	 * 'hangarLaunchOrder' note in onIndividualNotesLoaded) and calls
	 * performLaunch for each entry that still passes canLaunch validation.
	 *
	 * Stage 21: this per-bay path is retained ONLY for catapults (single-fighter,
	 * never multi-bay). Ordinary hangars + rails launch via the carrier-level
	 * processWholeFlightLaunches coalescer instead.
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
			//Stage 8.5: per-launch direction override is only honoured when the
			//hangar actually advertises a multi-direction picker; an unsolicited
			//direction field from a stale/forged client payload is ignored.
			$dirOverride = null;
			if (isset($entry['direction']) && is_array($hangar->directions) && !empty($hangar->directions)) {
				$d = (int)$entry['direction'];
				$d = (($d % 6) + 6) % 6;
				if (in_array($d, array_map('intval', $hangar->directions), true)) {
					$dirOverride = $d;
				}
			}
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
			self::performLaunch($hangar, $carrier, $phpclass, $size, $gamedata, $dirOverride);
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
		//Jump-sequencing exception: a carrier killed this turn by its own jump
		//(HyperspaceJump damage class) or jump failure (JumpFailure damage class)
		//can still receive a dock — the fighter completes its dock mid-jump and
		//either jumps along with the carrier or is destroyed with it. Ordinary
		//wreck (no jump-class damage this turn) still rejects.
		if ($carrier->removed) { $reason = 'carrier not in play'; return false; }
		if ($carrier->isDestroyed() && !self::hasJumpDamageThisTurn($carrier, $gamedata)) {
			$reason = 'carrier not in play'; return false;
		}

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

		//Hangar must have a compatible category with room for the $count craft.
		//A unitSize<1 craft (Vorlon Assault Fighter) needs >1 box each; a unitSize>1
		//ultralight (Zorth) needs a FRACTIONAL box each, so several pack into one box.
		//Use the flight's TRUE size (not carrier-mapped category) so a heavy flight is
		//rejected when the carrier only has medium slots, etc. Pass $carrier so
		//universal hangars derive their permissions from the ship's $fighters
		//declaration (Decurion-style multi-category).
		$category = self::trueSizeOf($flight);
		if (!self::hangarAcceptsCategory($hangar, $category, $carrier)) { $reason = 'hangar full'; return false; }

		if ($isCatapult) {
			//Catapults count craft 1:1 (single-fighter rail).
			$free = self::effectiveCapacity($hangar) - (int)self::usageCountFor($hangar);
			if ($free < $count) { $reason = 'hangar full'; return false; }
		} else {
			//Pack against the TOTAL: round up (existing fractional usage + new craft's
			//fractional cost) and compare to integer capacity. Rounding the TOTAL (not
			//each dock separately) is what lets a half-box already in use share with a
			//new half-box, so 12 boxes really hold 24 Zorth rather than 12.
			$bpc = self::boxesPerCraftForClass($flight->phpclass);
			$projected = (int)ceil(self::usageCountFor($hangar, $carrier) + $count * $bpc);
			if ($projected > self::effectiveCapacity($hangar)) { $reason = 'hangar full'; return false; }
		}

		//Shared launch+land budget vs hangar output. The output budget is in
		//CRAFT (one recovered fighter = one against the rate, regardless of how
		//many boxes it occupies). Catapults have no budget (one fighter, no
		//initiative cost) — skip the gate for them.
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
		//Stage 21: usageCountFor now also subtracts boxes that other bays' multi-bay
		//(occupancy) entries place on this hangar — pass $ship so it can see them.
		//Round usage UP to whole boxes (occupiedBoxes): with ultralights the raw
		//usage can be fractional (0.5), and free space is whole boxes — a partly
		//filled box is not free. canShipReceive does the finer-grained packing check
		//(it knows the new craft's fractional cost can share the same partial box).
		return max(0, $max - self::occupiedBoxes($hangar, $ship));
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

		//Shuttles, minesweeping shuttles & cargo shuttles can use any combat-fighter slot per B5W §10.1.
		if (($cat === 'shuttles' || $cat === 'minesweeping shuttles' || $cat === 'cargo shuttles') && isset($sizeRank[$hType])) {
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
			if ($cat === 'shuttles' || $cat === 'minesweeping shuttles' || $cat === 'cargo shuttles') return true;

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
		//Returned capacity is in CRAFT, so convert free boxes via the flight's
		//per-craft box cost (unitSize<1 craft fit fewer per box).
		$bpc = self::boxesPerCraftForClass($flight->phpclass);

		//Exact match first. Catapults (hangarType 'superheavy') land here for a
		//superheavy flight; they ignore their own damage and have no output budget.
		foreach ($hangars as $h){
			if ($h->hangarType !== $category) continue;
			$isCat = !empty($h->isCatapult);
			if (!$isCat && $h->isDestroyed()) continue;
			$free = self::freeBoxesByCategory($h, $category, $carrier);
			if ($isCat) {
				$capacity = $free;   //catapult counts craft 1:1; no launch+land budget
			} else {
				$budget = max(0, (int)$h->output - ((int)$h->launchedThisTurn + (int)$h->landedThisTurn));
				$capacity = min((int)floor($free / $bpc), $budget);
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
			$capacity = min((int)floor($free / $bpc), $budget);
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

	/* === Stage 21: no-split whole-flight dock ============================ */

	/*
	 * Dock $count craft of $flight into $carrier as ONE ship — no fragments.
	 * $bays is an ordered list of [hangar, freeBoxes] (fill order, primary first)
	 * across which the flight's boxes are spread. Writes ONE stash entry on the
	 * primary bay with an `occupancy` list naming every bay it actually fills.
	 *
	 * Full dock ($count >= active): the flight ship IS the docked unit — mark it
	 * removed, link the entry via dockedFlightId/flightId. Relaunch resurrects
	 * the same ship with all its damage/ammo intact (no copy, no spawn).
	 *
	 * Partial dock ($count < active): the docked $count fighters leave the flight
	 * (DisengagedFighter) and the entry is NOT flightId-linked (relaunch spawns
	 * fresh) — same as the Stage-19 partial semantics, but still ONE entry with
	 * occupancy, never a fragment ship. (Under no-split, a partial relaunch is
	 * handled in 21.2; for 21.1 the partial-dock entry is anonymous as before.)
	 *
	 * Returns the number actually docked. Caller validates fit beforehand.
	 */
	public static function performWholeFlightDock($carrier, $flight, $count, $bays, $gamedata){
		if (!($flight instanceof FighterFlight)) return 0;
		if (empty($bays)) return 0;
		$activeCount = $flight->countActiveCraft($gamedata->turn);
		if ($activeCount <= 0) return 0;
		$count = max(1, (int)$count);
		if ($count > $activeCount) $count = $activeCount;
		$partial = ($count < $activeCount);

		$category = self::trueSizeOf($flight);
		//Fractional-safe per-craft box cost (ultralight 0.5, superheavy >1). Don't
		//clamp to 1 or ultralights would be re-priced at a full box each.
		$bpc = self::boxesPerCraftForClass($flight->phpclass);
		if ($bpc <= 0) $bpc = 1;

		//Distribute craft across the bays in fill order (primary first), reserving
		//WHOLE boxes per bay (occupancy boxes are read as ints everywhere). Each bay
		//takes min(craft-that-fit, remaining) where craft-that-fit = floor(freeBoxes /
		//bpc); its reserved boxes is ceil(craftHere * bpc) so an odd ultralight count
		//rounds its half-box up to a whole reserved box.
		$occupancy = array();
		$remaining = $count;   //craft still to place
		$primaryHangar = null;
		foreach ($bays as $b){
			if ($remaining <= 0) break;
			$h = $b['hangar'];
			$freeBoxes = (int)$b['freeBoxes'];
			if ($freeBoxes <= 0) continue;
			$craftFit = (int)floor($freeBoxes / $bpc);
			$craftHere = min($remaining, $craftFit);
			if ($craftHere <= 0) continue;
			$remaining -= $craftHere;
			$occupancy[] = array('systemId' => (int)$h->id, 'boxes' => (int)ceil($craftHere * $bpc));
			if ($primaryHangar === null) $primaryHangar = $h;
		}
		if ($primaryHangar === null || $remaining > 0){
			//Couldn't place all boxes — caller should have validated. Bail safely.
			return 0;
		}

		$entry = array(
			'phpclass'   => $flight->phpclass,
			'name'       => $flight->name,
			'flightSize' => $count,
			'hangarType' => $category,
			'dockedTurn' => $gamedata->turn,
		);
		if (!empty($flight->customFtrName)) $entry['customFtrName'] = $flight->customFtrName;
		if ($bpc != 1) $entry['boxesPerCraft'] = $bpc;   //stamp fractional (0.5) and >1 alike
		//Stage 21: occupancy is only recorded for true multi-bay docks; a
		//single-bay dock leaves it off so legacy readers + the common path are
		//untouched (the entry's boxes are simply counted on its own hangar).
		if (count($occupancy) > 1) $entry['occupancy'] = $occupancy;

		if ($partial){
			//Partial: the docked fighters leave the flight; entry stays anonymous
			//(no flightId link) exactly as Stage 19, so 21.1 doesn't change the
			//partial-relaunch behaviour yet (21.2 handles partial-shrink).
			self::dockFighters($flight, $count, $gamedata);
		} else {
			//Full: the flight ship IS the docked unit.
			$entry['dockedFlightId'] = $flight->id;
			$flight->removed = true;
			$flight->removedTurn = $gamedata->turn;
		}

		$primaryHangar->hangarUsage[] = $entry;
		$primaryHangar->landedThisTurn += $count;

		$note = new IndividualNote(
			-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
			$carrier->id, $primaryHangar->id,
			'hangarDockEvent',
			$partial ? 'Hangar partial dock' : 'Hangar received flight',
			$flight->id . ':' . $flight->phpclass . ':' . $count . ($partial ? ':partial' : '')
		);
		Manager::insertIndividualNote($note);

		self::applyHangarOperationsCrit($carrier, $gamedata);
		return $count;
	}

	/* Carrier-level dock coalescer (Stage 21, approach B). Runs once per carrier:
	 * gathers every hangar's pendingDockOrder, groups by flightId (summing the
	 * per-bay counts the client queued), and docks each WHOLE flight once via
	 * performWholeFlightDock — spreading its boxes across the bays the player
	 * targeted (and, if those don't have room, any other eligible bay). This
	 * replaces the Stage-19 per-bay performLand calls that produced fragments.
	 *
	 * Guarded by $carrier->dockCoalesceDone so only the first hangar to reach the
	 * dock step runs it; the rest no-op. Mirrors the once-per-carrier pattern.
	 */
	public static function processWholeFlightDocks($carrier, $gamedata){
		if (!empty($carrier->dockCoalesceDone)) return;
		$carrier->dockCoalesceDone = true;
		if (!self::isFlowEnabled($gamedata->id)) return;

		$hangars = self::collectHangars($carrier);
		if (empty($hangars)) return;

		//Gather per-flight: total requested count + the ordered list of bays that
		//ordered it (preserving the player's bay preference as fill order).
		$byFlight = array();   //flightId => {count, bays:[hangar,…]}
		foreach ($hangars as $hangar){
			//Catapults dock their single fighter through their own per-hangar path
			//(processDockOrders) — exclude from whole-flight coalescing.
			if (!empty($hangar->isCatapult)) continue;
			if (empty($hangar->pendingDockOrder)) continue;
			foreach ($hangar->pendingDockOrder as $order){
				$fid = isset($order['flightId']) ? (int)$order['flightId'] : 0;
				$cnt = isset($order['count'])    ? (int)$order['count']    : 0;
				if ($fid <= 0 || $cnt <= 0) continue;
				if (!isset($byFlight[$fid])) $byFlight[$fid] = array('count' => 0, 'bays' => array());
				$byFlight[$fid]['count'] += $cnt;
				$byFlight[$fid]['bays'][spl_object_hash($hangar)] = $hangar;   //dedup, keep order
			}
			$hangar->pendingDockOrder = null;   //consumed by the coalescer
		}

		foreach ($byFlight as $flightId => $info){
			$flight = $gamedata->getShipById((int)$flightId);
			if (!($flight instanceof FighterFlight)) continue;

			//Validate the WHOLE-flight dock and build the fill-order bay list with
			//live free boxes. Start from the bays the player targeted, then top up
			//from any other eligible bay so the flight always lands as one ship.
			$wanted = (int)$info['count'];
			$reason = null;
			$bays = self::buildDockBays($carrier, $flight, array_values($info['bays']), $wanted, $gamedata, $reason);
			if (empty($bays)){
				$failNote = new IndividualNote(
					-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
					$carrier->id, ($hangars[0]->id ?? 0),
					'hangarDockEvent', 'Hangar dock failed',
					'fail:' . $flightId . ':' . $wanted . ':' . ($reason ?? 'no room')
				);
				Manager::insertIndividualNote($failNote);
				continue;
			}
			self::performWholeFlightDock($carrier, $flight, $wanted, $bays, $gamedata);
		}
	}

	/* Build the fill-order bay list (each {hangar, freeBoxes}) for docking
	 * $count craft of $flight, starting from $preferredHangars (the bays the
	 * player ordered) and topping up from any other eligible bay until the
	 * flight's box requirement is met. Returns [] (and sets $reason) if the
	 * carrier can't hold $count after all. */
	public static function buildDockBays($carrier, $flight, $preferredHangars, $count, $gamedata, &$reason = null){
		$category = self::trueSizeOf($flight);
		//Fractional-safe per-craft box cost; don't clamp bpc to 1 or 24 Zorth (12
		//boxes) would be mis-priced as 24 boxes and rejected on a 12-box hangar.
		$bpc = self::boxesPerCraftForClass($flight->phpclass);
		if ($bpc <= 0) $bpc = 1;
		$boxesNeeded = max(1, (int)$count) * $bpc;   //fractional for ultralights

		//Validate basic eligibility (hex/heading/speed/pivot/customcap) once via
		//canShipReceive against the carrier's best bay — it short-circuits the
		//common rejects without per-bay noise.
		if (!self::canShipReceive(self::primaryHangar($carrier), $carrier, $flight, 1, $gamedata, $reason)){
			//primary bay may not accept this category; fall through to the
			//eligible-bay scan which applies hangarAcceptsCategory per bay.
		}

		$seen = array();
		$ordered = array();
		//Preferred bays first (player intent / fill order).
		foreach ($preferredHangars as $h){
			if (!($h instanceof Hangar)) continue;
			$ordered[] = $h; $seen[spl_object_hash($h)] = true;
		}
		//Then any other eligible bay on the carrier.
		foreach (self::collectHangars($carrier) as $h){
			if (!empty($h->isCatapult)) continue;
			if (isset($seen[spl_object_hash($h)])) continue;
			$ordered[] = $h;
		}

		$bays = array();
		$boxesAvail = 0;
		foreach ($ordered as $h){
			if (!empty($h->isCatapult)) continue;
			if ($h->isDestroyed()) continue;
			if (!self::hangarAcceptsCategory($h, $category, $carrier)) continue;
			$free = self::freeBoxesByCategory($h, $category, $carrier);
			//Respect the shared launch+land budget per bay. Budget is in CRAFT;
			//convert to its box-equivalent with the real (fractional) bpc so an
			//ultralight bay isn't over-restricted (24-craft budget = 12 boxes).
			$budget = max(0, (int)$h->output - ((int)$h->launchedThisTurn + (int)$h->landedThisTurn));
			$free = min($free, (int)floor($budget * $bpc));
			if ($free <= 0) continue;
			$bays[] = array('hangar' => $h, 'freeBoxes' => (int)$free);
			$boxesAvail += (int)$free;
			if ($boxesAvail >= $boxesNeeded) break;
		}

		if ($boxesAvail < $boxesNeeded){
			$reason = $reason ?? 'not enough free boxes on carrier';
			return array();
		}
		return $bays;
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
	 *
	 * Stage 21: this per-bay primitive is no longer the dock entry point — the
	 * carrier-level processWholeFlightDocks coalesces all of a flight's orders
	 * and calls performWholeFlightDock instead (no fragments). performLand is
	 * retained only for the jumping-carrier dock path until 21.4 reworks it.
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
		//Stamp per-craft box cost for non-standard craft — unitSize<1 superheavy
		//(>1 box each) AND unitSize>1 ultralight (fractional 0.5 box each) — so
		//usageCountFor / eviction can charge the right number of boxes without
		//re-instantiating the blueprint. Omitted for ordinary 1-box craft to keep
		//the persisted note lean (boxesPerCraftForEntry defaults to 1).
		$bpc = self::boxesPerCraftForClass($flight->phpclass);
		if ($bpc != 1) $entry['boxesPerCraft'] = $bpc;

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
				//A fragment is born $removed at the dock turn — it never existed
				//on the board as its own flight. Mark it so onIndividualNotesLoaded
				//can restore $spawned == dockedTurn on reload (spawned isn't a
				//tac_ship column); the replay uses spawned==removedTurn to hide
				//the fragment on its dock turn instead of rendering a phantom
				//detachment alongside the still-full source flight.
				$entry['fragment'] = true;
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
	/* Stage 21 (no-split): coalesce a flight's per-bay deploy-dock orders — which
	 * may be spread across several POST-side hangars — into ONE occupancy entry,
	 * built from the CLIENT's per-bay counts (the client distributed them against
	 * true capacity; the POST-side sibling hangars have empty hangarUsage, so the
	 * server must NOT re-distribute or recompute free boxes here).
	 *
	 * $hangar is the POST-side hangar currently in generateIndividualNotes (its
	 * usage is already DB-seeded by the caller). $dbCarrier is the DB-loaded ship
	 * (true sibling usage) — used only to seed a sibling primary bay if the
	 * occupancy's primary turns out to be a different bay than $hangar.
	 *
	 * Gathers orders for each flight from $hangar AND its POST-side siblings,
	 * consuming them so the sibling passes no-op.
	 */
	public static function processDeployStartTransfer($hangar, $carrier, $gamedata, $dbCarrier = null){
		if (empty($hangar->pendingDeployStartTransfer)) {
			$hangar->pendingDeployStartTransfer = null;
			return;
		}

		//Process ONLY the flights THIS bay ($hangar) ordered — its entry is written
		//here, on the bay whose generateIndividualNotes snapshot is still pending,
		//so it reliably persists. For each such flight, gather its per-bay counts
		//across ALL POST-side bays (the client may have spread it across siblings)
		//and consume those sibling orders so they don't double-dock.
		$myFlightIds = array();
		foreach ($hangar->pendingDeployStartTransfer as $order){
			$fid = isset($order['flightId']) ? (int)$order['flightId'] : 0;
			if ($fid > 0) $myFlightIds[$fid] = true;
		}

		$postHangars = self::collectHangars($carrier);
		foreach (array_keys($myFlightIds) as $flightId){
			//Gather this flight's bay orders across all POST-side bays, in bay order,
			//and remove them from each bay's pending list.
			$bayOrders = array();   //[ {hangar, count}, … ]
			foreach ($postHangars as $h){
				if (!is_array($h->pendingDeployStartTransfer)) continue;
				$kept = array();
				foreach ($h->pendingDeployStartTransfer as $order){
					$fid = isset($order['flightId']) ? (int)$order['flightId'] : 0;
					if ($fid === (int)$flightId){
						$cnt = (isset($order['count']) && (int)$order['count'] > 0) ? (int)$order['count'] : null;
						$bayOrders[] = array('hangar' => $h, 'count' => $cnt);
					} else {
						$kept[] = $order;
					}
				}
				$h->pendingDeployStartTransfer = empty($kept) ? null : $kept;
			}
			if (empty($bayOrders)) continue;

			$flight = $gamedata->getShipById((int)$flightId);
			$reason = null;
			if (!$flight || !self::validateDeployBayOrders($carrier, $dbCarrier, $flight, $bayOrders, $gamedata, $reason)){
				$failNote = new IndividualNote(
					-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
					$carrier->id, $hangar->id,
					'hangarDeployStartEvent', 'Hangar deploy-start dock failed',
					'fail:' . $flightId . ':' . ($reason ?? 'unknown')
				);
				Manager::insertIndividualNote($failNote);
				continue;
			}
			//Primary = $hangar (the in-flight bay). Ensure $hangar is in the
			//occupancy list (it ordered this flight, so it is).
			self::performDeployStartDockFromOrders($hangar, $carrier, $flight, $bayOrders, $gamedata, $dbCarrier);
		}

		//Any orders left on THIS bay (none, normally) are cleared.
		$hangar->pendingDeployStartTransfer = null;
	}

	/* Build ONE occupancy deploy-dock entry, server-authoritatively. The client's
	 * per-bay order list is only a PREFERENCE for fill order — the server re-homes
	 * the whole flight across bays using TRUE (DB-side) free boxes, clamping each
	 * bay to what it can actually hold and topping up from any other eligible bay.
	 * This is robust against a stale/over-optimistic client distribution (e.g. one
	 * that offered a bay already full of auto-filled shuttles). The entry is
	 * written to the in-flight $preferredPrimary (snapshot still pending) so it
	 * persists. Returns the number docked (0 on total failure). */
	public static function performDeployStartDockFromOrders($preferredPrimary, $carrier, $flight, $bayOrders, $gamedata, $dbCarrier = null){
		if ($flight->removed) return 0;
		$activeCount = $flight->countActiveCraft($gamedata->turn);
		if ($activeCount <= 0) return 0;

		//Per-craft box cost: <1 for ultralights (Zorth, 0.5), >1 for superheavy
		//(Vorlon, 2). Do NOT clamp to a minimum of 1 — that would silently re-price
		//ultralights at a full box each and defeat the 2-per-box packing on the
		//deploy path. Only guard against a non-positive lookup.
		$bpc = self::boxesPerCraftForClass($flight->phpclass);
		if ($bpc <= 0) $bpc = 1;
		$category = self::trueSizeOf($flight);

		//Fill order: the client's chosen bays first (dedup), then any other
		//eligible bay on the carrier. Free boxes are read from the DB-side bay
		//PLUS this-pass POST-side commits (entries the coalescer already appended).
		$seen = array();
		$ordered = array();
		foreach ($bayOrders as $bo){
			$h = $bo['hangar'];
			$k = spl_object_hash($h);
			if (isset($seen[$k])) continue;
			$seen[$k] = true; $ordered[] = $h;
		}
		foreach (self::collectHangars($carrier) as $h){
			$k = spl_object_hash($h);
			if (isset($seen[$k])) continue;
			if (!empty($h->isCatapult)) continue;
			$seen[$k] = true; $ordered[] = $h;
		}

		//Occupancy is tracked in WHOLE boxes per bay (every consumer reads
		//occ['boxes'] as an int). For ultralights we therefore reserve craft in
		//bay-sized whole boxes: $remaining counts craft, and each bay takes
		//min(craft-that-fit, remaining) where craft-that-fit = floor(freeBoxes / bpc).
		//A bay's stored boxes is the whole-box ceiling of the craft it actually holds
		//(ceil(craftHere * bpc)) — so a half-box from an odd ultralight count rounds
		//up to a reserved whole box (the "round up for safety" rule).
		$occupancy = array();
		$remaining = $activeCount;   //craft still to place
		foreach ($ordered as $h){
			if ($remaining <= 0) break;
			if (!empty($h->isCatapult)) continue;
			if ($h->isDestroyed()) continue;
			if (!self::hangarAcceptsCategory($h, $category, $carrier)) continue;

			//TRUE free boxes for this bay = effective capacity − max(DB usage,
			//POST-side this-pass usage). The POST-side bay accumulates entries the
			//coalescer appends this pass; the DB-side bay has pre-existing usage
			//(auto-shuttles, prior docks). Take the larger so neither is missed.
			$cap = self::effectiveCapacity($h);
			$dbUsed = 0;
			if ($dbCarrier && is_array($dbCarrier->systems)){
				foreach ($dbCarrier->systems as $dbSys){
					if (!($dbSys instanceof Hangar) || (int)$dbSys->id !== (int)$h->id) continue;
					$dbUsed = self::usageCountFor($dbSys, $dbCarrier);
					break;
				}
			}
			$postUsed = 0;
			if (is_array($h->hangarUsage)){
				foreach ($h->hangarUsage as $e){ $postUsed += self::boxesForEntry($e); }
			}
			//Free WHOLE boxes in this bay = capacity − existing usage rounded UP (a
			//partly-filled box can't be shared with this flight on the deploy path).
			$free = max(0, $cap - (int)ceil(max($dbUsed, $postUsed)));
			if ($free <= 0) continue;
			//Craft that fit in those whole boxes, then the whole boxes they actually
			//reserve (ceil so an odd ultralight count rounds its half-box up).
			$craftFit = (int)floor($free / $bpc);
			$craftHere = min($remaining, $craftFit);
			if ($craftHere <= 0) continue;
			$boxesHere = (int)ceil($craftHere * $bpc);
			$occupancy[] = array('systemId' => (int)$h->id, 'boxes' => $boxesHere);
			$remaining -= $craftHere;
		}

		if ($remaining > 0 || empty($occupancy)){
			//Carrier truly can't hold the whole flight — fail cleanly, leave it on
			//the map (don't mark removed).
			$note = new IndividualNote(
				-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
				$carrier->id, $preferredPrimary->id,
				'hangarDeployStartEvent', 'Hangar deploy-start dock failed',
				'fail:' . $flight->id . ':carrier full'
			);
			Manager::insertIndividualNote($note);
			return 0;
		}

		$totalCraft = $activeCount;

		//Entry host = the in-flight $preferredPrimary ($hangar), whose snapshot is
		//still pending this request so the entry reliably persists. The host need
		//NOT be one of the occupancy bays: usageCountFor counts an occupancy
		//entry's boxes only on the bays named in its occupancy list (via
		//entryBoxesOnHangar / foreignOccupancyBoxesOn), so hosting the metadata
		//record on a bay that contributes 0 boxes is correct (it adds 0 to that
		//bay's own usage). This avoids ever writing to a bay whose snapshot may
		//have already run.
		$entryHost = $preferredPrimary;

		//Seed the entry host's POST-side usage from DB so its existing entries
		//(auto-shuttles, prior docks) survive its snapshot write.
		if ($dbCarrier && is_array($dbCarrier->systems) && empty($entryHost->hangarUsage)){
			foreach ($dbCarrier->systems as $dbSys){
				if (!($dbSys instanceof Hangar) || (int)$dbSys->id !== (int)$entryHost->id) continue;
				if (is_array($dbSys->hangarUsage)) $entryHost->hangarUsage = $dbSys->hangarUsage;
				break;
			}
		}

		$entry = array(
			'phpclass'       => $flight->phpclass,
			'name'           => $flight->name,
			'flightSize'     => $totalCraft,
			'hangarType'     => $category,
			'dockedTurn'     => $gamedata->turn,
			'dockedFlightId' => $flight->id,
		);
		if (!empty($flight->customFtrName)) $entry['customFtrName'] = $flight->customFtrName;
		if ($bpc != 1) $entry['boxesPerCraft'] = $bpc;   //stamp fractional (0.5) and >1 alike; 1 stays lean
		if (count($occupancy) > 1) $entry['occupancy'] = $occupancy;

		$flight->removed = true;
		$flight->removedTurn = $gamedata->turn;

		if (!is_array($entryHost->hangarUsage)) $entryHost->hangarUsage = array();
		$entryHost->hangarUsage[] = $entry;

		$note = new IndividualNote(
			-1, $gamedata->id, $gamedata->turn, $gamedata->phase,
			$carrier->id, $entryHost->id,
			'hangarDeployStartEvent', 'Hangar received flight (deployment)',
			$flight->id . ':' . $flight->phpclass . ':' . $totalCraft
		);
		Manager::insertIndividualNote($note);
		return $totalCraft;
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
	/* Stage 21: validate a coalesced multi-bay deploy-dock against the client's
	 * per-bay distribution. The per-flight gates (slot / owner / both-deploying-
	 * this-turn / category fit / customFighter cap) are checked once; each bay's
	 * slice is checked against THAT bay's TRUE (DB-side) free boxes — the POST-
	 * side carrier's sibling bays have empty hangarUsage so we must read capacity
	 * from $dbCarrier. Returns false + sets $reason on the first failing gate.
	 *
	 * Boxes already committed to OTHER flights earlier in THIS deploy pass are
	 * tracked via the POST-side bays' hangarUsage (which the coalescer appends
	 * to as it docks), so two big flights deploying into the same bays this pass
	 * don't both think the bay is empty. */
	public static function validateDeployBayOrders($carrier, $dbCarrier, $flight, $bayOrders, $gamedata, &$reason = null){
		if (!($flight instanceof FighterFlight)) { $reason = 'not a flight'; return false; }
		if ($flight->removed || $flight->isDestroyed()) { $reason = 'flight already removed'; return false; }
		if ($carrier->isDestroyed() || $carrier->removed) { $reason = 'carrier not in play'; return false; }
		if ((int)$flight->slot   !== (int)$carrier->slot)   { $reason = 'slot mismatch';  return false; }
		if ((int)$flight->userid !== (int)$carrier->userid) { $reason = 'owner mismatch'; return false; }
		if ($flight->getTurnDeployed($gamedata) != $gamedata->turn) { $reason = 'flight not deploying this turn'; return false; }
		if ($carrier->getTurnDeployed($gamedata) != $gamedata->turn) { $reason = 'carrier not deploying this turn'; return false; }

		$activeCount = $flight->countActiveCraft($gamedata->turn);
		if ($activeCount <= 0) { $reason = 'flight has no craft'; return false; }

		//customFighter cap (shared across bays) against the whole-flight count.
		$customName = isset($flight->customFtrName) ? (string)$flight->customFtrName : '';
		if ($customName !== ''){
			$remaining = self::customFighterRemaining($carrier, $customName);
			if ($remaining < $activeCount) { $reason = 'customFighter cap exceeded'; return false; }
		}

		//Per-BAY capacity is NOT checked here — performDeployStartDockFromOrders is
		//server-authoritative: it re-homes the whole flight across bays using TRUE
		//(DB-side) free boxes, drops full/incompatible bays, tops up elsewhere, and
		//fail-notes only if the carrier genuinely can't hold the flight. This keeps
		//the validation robust against a stale/over-optimistic client distribution.
		return true;
	}

	public static function canDeployStartDock($hangar, $carrier, $flight, $gamedata, &$reason = null, $count = null){
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

		//$count is the number of craft this hangar will take. NULL = the whole
		//flight (legacy single-hangar dock). A multi-hangar auto-distribute
		//(rails: a 9-flight spread across a 6-box + 3-box rail) passes the
		//per-hangar slice — partial deploy-docks split the flight into fragments
		//exactly like the Firing-Phase splitter (performLand).
		$size = ($count === null) ? (int)$flight->flightSize : (int)$count;
		if ($size <= 0) { $reason = 'flight has no craft'; return false; }

		$category = self::trueSizeOf($flight);
		$free = self::freeBoxesByCategory($hangar, $category, $carrier);
		//unitSize<1 craft need more than one box each (see boxesPerCraftForClass);
		//catapults are exempt (single-fighter rail, counts craft 1:1).
		$boxesNeeded = !empty($hangar->isCatapult) ? $size : $size * self::boxesPerCraftForClass($flight->phpclass);
		if ($free < $boxesNeeded) { $reason = 'hangar full'; return false; }

		//Stage 10.6.2: per-ship customFighter cap. Checked against the per-hangar
		//slice; the aggregate across hangars is bounded by the cap because each
		//slice consumes from the same shared remaining (entries stamped as they
		//are written within this processing pass).
		$customName = isset($flight->customFtrName) ? (string)$flight->customFtrName : '';
		if ($customName !== '') {
			$remaining = self::customFighterRemaining($carrier, $customName);
			if ($remaining < $size) { $reason = 'customFighter cap exceeded'; return false; }
		}

		return true;
	}

	/* (Stage 21: the old per-bay performDeployStartDock — which re-distributed
	 * boxes server-side and, pre-21.1, split into fragments — was replaced by the
	 * coalescing processDeployStartTransfer → performDeployStartDockFromOrders
	 * pair, which builds ONE occupancy entry from the client's per-bay counts.
	 * Server-side re-distribution was wrong on the POST-side because sibling bays
	 * have empty hangarUsage there, so it couldn't see their true occupancy.) */

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
							//This fragment is born $removed THIS (launch) turn —
							//like the partial-dock fragment, it never existed on
							//the board on its own. Stamp the marker + the current
							//turn so onIndividualNotesLoaded restores spawned ==
							//removedTurn == this turn and the replay hides it on
							//the turn it was split off. (Inherited dockedTurn from
							//the original dock would be the WRONG turn here.)
							$newEntry['fragment']   = true;
							$newEntry['dockedTurn'] = $gamedata->turn;
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

	/* === Jump-sequencing fix: dock onto a jumping/JumpFailed carrier ====== */

	/* True when $carrier's primary structure has a damage entry of class
	 * 'HyperspaceJump' or 'JumpFailure' dated to the current turn — i.e. the
	 * destruction this turn was a jump event, not ordinary fire. Used by
	 * canShipReceive and processJumpingCarrierDockOrders to identify carriers
	 * that should still complete pending docks despite being isDestroyed().
	 */
	public static function hasJumpDamageThisTurn($carrier, $gamedata){
		$struct = $carrier->getStructureSystem(0);
		if (!$struct || !is_array($struct->damage)) return false;
		foreach ($struct->damage as $entry) {
			if ((int)$entry->turn !== (int)$gamedata->turn) continue;
			if ($entry->damageclass === 'HyperspaceJump' || $entry->damageclass === 'JumpFailure') {
				return true;
			}
		}
		return false;
	}

	/* True when $carrier was destroyed this turn specifically by JumpFailure
	 * (damaged Jump Drive rolled a failure in doHyperspaceJump). Used by
	 * processCarrierDestructionEscapes to force 0 escape — every craft in
	 * the hangar dies with the ship, no d20 roll, no survivors.
	 */
	public static function hasJumpFailureDamage($carrier, $gamedata){
		$struct = $carrier->getStructureSystem(0);
		if (!$struct || !is_array($struct->damage)) return false;
		foreach ($struct->damage as $entry) {
			if ((int)$entry->turn !== (int)$gamedata->turn) continue;
			if ($entry->damageclass === 'JumpFailure') return true;
		}
		return false;
	}

	/* Resolve pending dock orders on carriers that jumped (or JumpFailed) this
	 * turn. Firing::fireWeapons applies the jump damage entry at the END of
	 * weapon fire (firing.php:1015-1028), so by the time Criticals::setCriticals
	 * runs the jumping carrier already reads isDestroyed() and is excluded from
	 * the $activeShips snapshot. That means Hangar::criticalPhaseEffects — the
	 * usual entry point for processDockOrders — never fires on a jumping
	 * carrier's hangars, and any queued hangarDockOrder is silently dropped.
	 *
	 * Per B5W §10.1 and the project spec: a fighter ordered to dock with a
	 * carrier on the turn it jumps SHOULD complete the dock. Outcome:
	 *   - HyperspaceJump (successful) → fighter is in the hangar and jumps
	 *     along with the carrier (fleetList's getJumpedDockedFlightIds path
	 *     preserves its CV, escape pass is skipped via hasJumped()).
	 *   - JumpFailure (damaged Jump Drive) → fighter is in the hangar at the
	 *     moment of destruction, then dies with the ship (no escape roll —
	 *     see processCarrierDestructionEscapes' JumpFailure handling).
	 *
	 * Carriers killed by ordinary fire on the same turn are NOT covered here
	 * (no jump-class damage entry); their pending dock orders remain ignored
	 * so the docking flight stays in space, matching the "don't dock with a
	 * normal wreck" rule.
	 *
	 * Called from Criticals::setCriticals BEFORE the $activeShips snapshot, so
	 * processCarrierDestructionEscapes' Pass-3 sweep sees the post-dock state.
	 */
	public static function processJumpingCarrierDockOrders($gamedata){
		foreach ($gamedata->ships as $carrier) {
			if ($carrier instanceof FighterFlight) continue;
			if (!$carrier->isDestroyed()) continue;
			if (!self::hasJumpDamageThisTurn($carrier, $gamedata)) continue;

			foreach (self::collectHangars($carrier) as $hangar) {
				self::processDockOrders($hangar, $carrier, $gamedata);
			}
		}
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
			$totalDocked = count($virtuals);

			//JumpFailure short-circuit: a carrier destroyed by its own jump
			//failure leaves NO chance to escape for craft in the hangar — they
			//die with the ship. Skip the d20 roll, force $maxEscape = 0, and
			//explicitly disengage every dockedFlightId-linked source flight so
			//their combat value folds to 0 (the regular destroyed-carrier path
			//only disengages non-escapees via performCarrierEscapeSpawns, which
			//we're not running). Roll persists as 0 in the replay note so the
			//forensic record shows "no roll, JumpFailure" rather than a real d20.
			$isJumpFailure = self::hasJumpFailureDamage($carrier, $gamedata);
			if ($isJumpFailure) {
				$roll = 0;
				$maxEscape = 0;
				self::markAllDockedFlightsDestroyed($hangars, $gamedata);
			} else {
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
				$maxEscape = self::computeEscapeCount($roll, $totalDocked);
			}

			$escapedNames = array();
			if ($maxEscape > 0) {
				$chosen = self::chooseEscapees($virtuals, $maxEscape);
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

	/* Disengage every fighter on every dockedFlightId-linked source flight
	 * across $hangars. Used by the JumpFailure path in processCarrierDestructionEscapes
	 * where no escape roll is rolled and every craft in the hangar dies — the
	 * normal markNonEscapeesDestroyed helper is bypassed (it runs from inside
	 * performCarrierEscapeSpawns), so this guarantees the combat-value fold
	 * still happens for the destroyed source flights.
	 */
	private static function markAllDockedFlightsDestroyed($hangars, $gamedata){
		foreach ($hangars as $hangar) {
			if (!is_array($hangar->hangarUsage)) continue;
			foreach ($hangar->hangarUsage as $entry) {
				if (!isset($entry['dockedFlightId'])) continue;
				$flightId = (int)$entry['dockedFlightId'];
				if ($flightId <= 0) continue;
				$flight = $gamedata->getShipById($flightId);
				if (!($flight instanceof FighterFlight)) continue;
				self::disengageFighters($flight, PHP_INT_MAX, $gamedata);
			}
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

	/* Choose which $maxEscape of the $virtuals fly free, SPREAD across the docked
	 * flights rather than draining one flight before touching the next (user call
	 * 2026-06-01). Two identical 12-flights with a 12-escape budget each contribute
	 * ~6 (one "- Split" per flight) instead of one whole resurrect + one whole loss.
	 *
	 * Method: bucket virtuals by source stash entry ("{hangarId}:{entryIdx}"),
	 * priority-sort WITHIN each bucket (pointCost DESC, damage ASC — best craft of
	 * each flight escape first), order the buckets by their top craft's priority
	 * (so when the budget divides unevenly the more valuable flight gets the spare),
	 * then round-robin one craft per bucket until $maxEscape is filled. */
	private static function chooseEscapees($virtuals, $maxEscape){
		if ($maxEscape <= 0 || empty($virtuals)) return array();

		$prio = function($a, $b){
			if ($a['pointCost'] !== $b['pointCost']) return $b['pointCost'] - $a['pointCost'];
			if ($a['damage']    !== $b['damage'])    return $a['damage']    - $b['damage'];
			return 0;
		};

		//Bucket by stash entry. Key matches performCarrierEscapeSpawns' bucketing so
		//each docked flight is one bucket (a multi-bay no-split flight is one entry).
		$buckets = array();
		foreach ($virtuals as $v){
			$key = (int)$v['hangarRef']->id . ':' . (int)$v['entryIdx'];
			$buckets[$key][] = $v;
		}
		foreach ($buckets as &$b){ usort($b, $prio); } unset($b);

		//Order buckets by their best (first, post-sort) craft so an uneven split
		//favours the higher-value flight.
		$ordered = array_values($buckets);
		usort($ordered, function($x, $y) use ($prio){ return $prio($x[0], $y[0]); });

		//Round-robin draw across buckets until the budget is spent.
		$chosen = array();
		$cursor = array_fill(0, count($ordered), 0);
		$progress = true;
		while (count($chosen) < $maxEscape && $progress){
			$progress = false;
			foreach ($ordered as $i => $bucket){
				if (count($chosen) >= $maxEscape) break;
				$c = $cursor[$i];
				if ($c >= count($bucket)) continue;
				$chosen[] = $bucket[$c];
				$cursor[$i] = $c + 1;
				$progress = true;
			}
		}
		return $chosen;
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

	/* Spawn the escape from a single stash entry. Three paths (Stage 21.4
	 * no-split rewrite — partial escape no longer spawns a fragment ship):
	 *
	 *  - dockedFlightId AND chosen-count >= source-flight active count →
	 *    resurrect the linked flight directly (clear $removed, deploy
	 *    MovementOrder, LaunchedThisTurn). Source flight IS the escape flight.
	 *  - dockedFlightId AND partial → spawn ONE "<name> - Split" K-flight via
	 *    spawnLaunchedKFlight (proportional pointCostEnh) carrying only the
	 *    escapees' ammo/damage/crit state — exactly like the rail-escape path
	 *    (spawnRailEscapeFlight). NO fragment ship: the source flight dies with
	 *    the carrier, so markNonEscapeesDestroyed disengages its whole roster
	 *    (CV → 0); the K escapees live on the new K-flight.
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
		//Stage 8.5: multi-direction hangar — players never choose for escape
		//(carrier is gone), so pick directions[0] as a sensible default. A
		//side-launch hangar (eg. Hyperion, directions = [1,5]) keeps its $direction
		//at 0 by default; without this fallback escape pods would eject forward
		//out of a bay that doesn't open forward.
		$escDir = (int)$hangar->direction;
		if (is_array($hangar->directions) && !empty($hangar->directions)) {
			$escDir = (int)$hangar->directions[0];
		}
		$spawnPos = $lastMove->position;
		$heading  = (int)$lastMove->heading;
		$facing   = ((int)$lastMove->facing + $escDir) % 6;
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

				//Partial escape — spawn ONE "<name> - Split" K-flight carrying
				//only the chosen escapees' state (Stage 21.4: no fragment ship,
				//mirrors spawnRailEscapeFlight). The non-escapees die with the
				//carrier — markNonEscapeesDestroyed disengages the whole source
				//roster afterwards so its row folds to CV 0.
				$chosenFighters = array();
				foreach ($virtuals as $v) {
					if ($v['fighter'] instanceof Fighter) $chosenFighters[] = $v['fighter'];
				}
				if (empty($chosenFighters)) return null;

				$flightName = self::dockedSplitName($entry);
				if ($flightName === null) $flightName = self::flightNameFor($phpclass, $carrier);
				$escapeFlight = self::spawnLaunchedKFlight($phpclass, $count, $flightName, $carrier, $spawnPos, $heading, $facing, $speed, $sourceFlight, $gamedata);
				if (!$escapeFlight) return null;

				//Copy the chosen escapees' ammo/damage/crit state onto the new
				//K-flight (same state-copy the rail-escape + partial-launch use).
				self::copyFlightAmmoEnhancements($sourceFlight, $escapeFlight, $gamedata);
				$targetFighters = array();
				foreach ($escapeFlight->systems as $f){ if ($f instanceof Fighter) $targetFighters[] = $f; }
				for ($i = 0; $i < count($chosenFighters); $i++){
					if (!isset($targetFighters[$i])) break;
					self::copyFighterStateToTarget($chosenFighters[$i], $targetFighters[$i], $escapeFlight, $gamedata);
				}

				self::applyEscapeCrits($escapeFlight, $gamedata);
				self::writeEscapeEventNote($carrier, $hangar, $escapeFlight, $count, 'split', $gamedata);
				return $escapeFlight->name;
			}
			//Linked flight missing — fall through to new-spawn.
		}

		//New-spawn path (anonymous orphan, auto-shuttle, or missing-linked-flight):
		//no source flight to copy state from, so spawn a fresh K-flight via the
		//shared spawner (Stage 21.4 — same no-split helper the partial path uses;
		//null source = no pointCostEnh carry, matching the old hand-rolled spawn).
		$flightName = self::flightNameFor($phpclass, $carrier);
		$flight = self::spawnLaunchedKFlight($phpclass, $count, $flightName, $carrier, $spawnPos, $heading, $facing, $speed, null, $gamedata);
		if (!$flight) return null;

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
	 * Partial-escape entries (Stage 21.4 no-split): the chosen escapees' state
	 * was copied onto a separate "<name> - Split" K-flight (spawnLaunchedKFlight).
	 * The source flight is NOT the escape flight and dies with the carrier, so
	 * disengage its ENTIRE roster here (CV → 0). No fragment ship is left behind;
	 * the escapees live only on the new K-flight. */
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

	/* Per-escapee replay note. $kind ∈ {'resurrected', 'split', 'newspawn',
	 * 'railEvac'} for forensic clarity in DB inspection. */
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
