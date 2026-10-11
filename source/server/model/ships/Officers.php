<?php
/* OFFICERS (OFFICERS_PLAN.md, repo root) - expert officers carried by a unit.

   An officer is an enhancement with a POST: the one system on the unit he lives in. While the post
   stands, his bonus applies. Two ways out of action, both permanent and both effective from the
   NEXT turn (D3):
     - DISABLED: the post was destroyed, by its own boxes or by its Structure block. Re-derived from
       the complete damage history at every load (the server loads every turn's damage rows), so
       nothing is stored. Self Repair reviving the post changes nothing: "undestroyed" is ignored.
     - KILLED: the post took damage through its armour and its critical roll came to 20 or more,
       modifiers included - the roll the post makes anyway, or one made for him when the post has
       just been destroyed. A dice result, so it IS stored: one OffKilled individual note per death,
       intercepted in DBManager's bulk note loop and never handed to any system (§4.4).

   THE ONE RUNTIME LIST. $ship->getOfficers() holds array('id', 'post', 'out', 'how') per officer:
   'post' is the post's system id, 'out' the turn he left action (null while he has not), 'how'
   'disabled' or 'killed'. He serves on turn T while out === null or out >= T. Every effect, the
   payload and (Stage 2) the insignia read that list and nothing else (§7, "two tracks drift").

   ⚠️ POST-side ships (Manager::getShipsFromJSON) never run onConstructed, so their list is empty:
   nothing in a phase's process() may ask about officers ([[arch_post_side_ship_reconstruction]]).

   Track 1 only so far (fixed-post ship officers, ship-level OFF_* rows): the Helmsman (Stage 1) and the
   Scanner, Technician, Geneticist and C&C Matter Weapons Expert (Stage 3). Placed officers (track 2,
   Stage 8) and fighter officers (Stage 4) are not built yet. */
class Officers{

	const ID_PREFIX = 'OFF_';
	const KILL_NOTE = 'OffKilled'; //tac_individual_notes.notekey, varchar(40)
	const KILL_ROLL = 20;          //"a '20' or greater (including any modifications)"

	/* One literal per officer (D6), walked in THIS order by apply() (D5).
	     label    the name in the buy dialog, the stored enhname and the kill note's notekey_human
	     post     a key into findPost()
	     pct      price in percent of the hull's blueprint pointCost, rounded up (D8)
	     apply    the static method that applies his stat changes while he serves, or null when every
	              rule he carries is asked for elsewhere (isActive)
	     publish  (optional) ship fields his apply moves that every viewer is sent while he serves: a
	              displayed stat is public, only the officer is hidden (user ruling 2026-10-10)
	     needs    (optional) a further test the hull must pass to be offered him, beyond having the post */
	private static $registry = array(
		/* Expert Helmsman: +5 initiative (FV units, QX1), wins initiative ties, and -1 on the die he
		   rolls on a meteor swarm's impact chart (user, 2026-10-10). The tie-break is
		   TacGamedata::sortShips; the chart is RammingAttack::resolveMeteors. */
		'OFF_HELM' => array('label' => 'Expert Helmsman', 'post' => 'cnc', 'pct' => 5, 'apply' => 'applyHelmsman',
			'publish' => array('iniativebonus')),
		/* Expert Scanner: "can save an EW point for after Initial Orders, but before firing - the same
		   mechanically as an EW Detector". One more point on top of the detector ladder (CUMULATIVE,
		   user ruling 2026-10-10), anywhere on the map - EW::getDetectorAllowance and its JS twin. */
		'OFF_SCAN' => array('label' => 'Expert Scanner', 'post' => 'scanner', 'pct' => 7, 'apply' => null),
		/* Expert Technician: "33% power of destroyed system (choose highest), or +1 power from a
		   deactivated system, or +2 power" - the BEST of the three, automatically, a third rounded down
		   (user rulings 2026-10-10). The +1 branch can never beat +2, so it is not computed. */
		'OFF_TECH' => array('label' => 'Expert Technician', 'post' => 'reactor', 'pct' => 9, 'apply' => 'applyTechnician'),
		/* Expert Geneticist: -1 to hit with this ship's own weapons, +2 to be hit (both profiles, the
		   Poor Crew and ProfileIncreased convention), and "enemies can ram this ship" - a tooltip line
		   only, as FV leaves ramming to the players. Offered on every hull with a C&C (user ruling
		   2026-10-10: "scenario only" is by agreement, not a gate). */
		'OFF_GENE' => array('label' => 'Expert Geneticist', 'post' => 'cnc', 'pct' => 5, 'apply' => 'applyGeneticist',
			'publish' => array('forwardDefense', 'sideDefense', 'toHitBonus')),
		/* Matter Weapons Expert (the C&C variant; the weapon-mounted one is Stage 8): +1 fire control and
		   +1 damage on every Matter-class weapon, per firing mode. Only for hulls carrying one. */
		'OFF_MWE'  => array('label' => 'Matter Weapons Expert', 'post' => 'cnc', 'pct' => 10, 'apply' => 'applyMatterExpert',
			'needs' => 'hasMatterWeapon'),
	);

	public static function isOfficerId($enhID){
		return strpos((string)$enhID, self::ID_PREFIX) === 0;
	}

	public static function getRegistry(){
		return self::$registry;
	}

	public static function getLabel($id){
		return isset(self::$registry[$id]) ? self::$registry[$id]['label'] : (string)$id;
	}

	/* D9: never on Ancient hulls, OSATs (mines extend OSAT) or terrain - one shared test in front of
	   every officer, not something each entry repeats. Bases, LCVs and the rest only where the post
	   exists, which findPost() decides. Flights wait for the fighter framework (Stage 4). */
	public static function mayCarryOfficers($ship){
		if ($ship instanceof FighterFlight) return false;
		if ((int)$ship->factionAge >= 3) return false;
		if ($ship instanceof OSAT) return false;
		if (!empty($ship->mine)) return false;
		if ($ship->isTerrain()) return false;
		return true;
	}

	/* ------------------------------------------------------------------ offers */

	/* Called at the end of Enhancements::setEnhancementOptionsShip, so the offers land in the static
	   blueprints (fvbuild -Statics follows any change here). Limit 1: "one of each type" (D9). */
	public static function addOffers($ship){
		if (!self::mayCarryOfficers($ship)) return;
		foreach (self::$registry as $id => $officer){
			if (in_array($id, $ship->enhancementOptionsDisabled)) continue;
			if (!self::isEligible($ship, $officer)) continue;
			$price = self::price($ship->pointCost, $officer['pct']);
			$ship->enhancementOptions[] = array($id, $officer['label'], 0, 1, $price, 0, false);
		}
	}

	/* ceil(pct% of the blueprint cost), in whole numbers: 0.07 x 100 is 7.000000000000001 in floats,
	   and ceil() would charge 8 for it (SHIP_ENHANCEMENTS_PLAN.md Stage 5 hit the same trap). */
	public static function price($pointCost, $pct){
		$hundredths = max(0, (int)round($pointCost * 100));
		return intdiv($hundredths * (int)$pct + 9999, 10000);
	}

	/* ------------------------------------------------------------------ posts */

	/* The system an officer lives in, or null when the hull has none (so he is not offered).
	   ⚠️ MIRROR PAIR with officers.js findPost.
	     cnc      the first C&C that is not a Flag Bridge; failing that, the first C&C of any kind.
	              SecondaryCnC is not a CnC subclass, so it is never picked.
	     scanner  the strongest Scanner by output (ELINT scanners included) - the one Elite Crew,
	              Poor Crew and Improved Sensors move, so they agree on a hull with two.
	     reactor  the biggest Reactor by maxhealth - Elite Crew's rule (a malus would sit on the main
	              reactor too); Mag-Grav reactors included.
	   ⚠️ Asked BEFORE any enhancement moves an output (D2): Poor Crew takes a point off the strongest
	   scanner, so on a hull with two equal ones asking afterwards would pick the other. */
	private static function findPost($ship, $post){
		switch ($post){
			case 'cnc':
				$any = null;
				foreach ($ship->systems as $system){
					if (!($system instanceof CnC)) continue;
					if (!($system instanceof FlagBridge)) return $system;
					if ($any === null) $any = $system;
				}
				return $any;
			case 'scanner':
				return self::strongest($ship, 'Scanner', 'output', -1);
			case 'reactor':
				return self::strongest($ship, 'Reactor', 'maxhealth', -1000);
		}
		return null;
	}

	//Enhancements::strongestSystem's semantics: the first in construction order wins a tie
	private static function strongest($ship, $class, $field, $floor){
		$best = null;
		$bestValue = $floor;
		foreach ($ship->systems as $system){
			if ($system instanceof $class && $system->$field > $bestValue){
				$bestValue = $system->$field;
				$best = $system;
			}
		}
		return $best;
	}

	//the hull has the post, and passes the officer's own test if he has one
	private static function isEligible($ship, $officer){
		if (self::findPost($ship, $officer['post']) === null) return false;
		if (empty($officer['needs'])) return true;
		$needs = $officer['needs'];
		return (bool)self::$needs($ship);
	}

	/* D2: posts are picked at the top of BaseShip::onConstructed, BEFORE any enhancement moves an
	   output, from the bought rows. A count above 1 still means one officer (D9), and a row the hull
	   would not have been offered (no post, or the officer's own test fails) buys nothing - the
	   server trusts the client's rows, so a doctored payload is answered here. */
	public static function pickPosts($ship){
		$list = array();
		if (self::hasOfficerRow($ship) && self::mayCarryOfficers($ship)){
			foreach (self::$registry as $id => $officer){
				if (!self::isBought($ship, $id)) continue;
				if (!self::isEligible($ship, $officer)) continue;
				$post = self::findPost($ship, $officer['post']);
				$list[] = array('id' => $id, 'post' => (int)$post->id, 'out' => null, 'how' => null);
			}
		}
		$ship->setOfficers($list);
	}

	//the cheap test that keeps every ordinary ship (a single 'NONE' row) out of the resolver
	private static function hasOfficerRow($ship){
		foreach ($ship->enhancementOptions as $entry){
			if ((int)$entry[2] > 0 && self::isOfficerId($entry[0])) return true;
		}
		return false;
	}

	private static function isBought($ship, $id){
		foreach ($ship->enhancementOptions as $entry){
			if ($entry[0] === $id && (int)$entry[2] > 0) return true;
		}
		return false;
	}

	/* ------------------------------------------------------------------ out of action */

	/* After the per-system onConstructed loop (structureSystem is filled there) and before
	   iniativeadded is computed (D5). Derives each officer's state, then applies the stat changes of
	   every officer serving on $turn, in registry order. */
	public static function apply($ship, $turn){
		$list = $ship->getOfficers();
		if (empty($list)) return;
		$kills = $ship->getOfficerKills();
		foreach ($list as $i => $entry){
			$post = $ship->getSystemById($entry['post']);
			$disabled = ($post !== null) ? self::disabledTurn($ship, $post) : 0; //a vanished post never served
			$killed = isset($kills[$entry['id']]) ? (int)$kills[$entry['id']] : null;
			if ($killed !== null && ($disabled === null || $killed <= $disabled)){
				$list[$i]['out'] = $killed;
				$list[$i]['how'] = 'killed'; //both in one turn reads as killed (§4.4)
			} else if ($disabled !== null){
				$list[$i]['out'] = $disabled;
				$list[$i]['how'] = 'disabled';
			}
		}
		$ship->setOfficers($list);

		foreach ($list as $entry){
			if (!self::serves($entry, $turn)) continue;
			$method = self::$registry[$entry['id']]['apply'];
			if ($method !== null) self::$method($ship, $entry, $turn);
		}
	}

	/* Out on turn N = serves N, gone from N + 1 (D3: the support-system convention, and it keeps
	   simultaneous fire simultaneous). Out at turn 0 is pre-battle damage: he never serves (§4.9). */
	private static function serves($entry, $turn){
		if ($entry['out'] === null) return true;
		return ($entry['out'] >= 1) && ($entry['out'] >= (int)$turn);
	}

	/* Is this officer aboard and serving on $turn (default: the turn this gamedata was loaded at)?
	   On a hot path, ask behind TacGamedata::$officersPresent. */
	public static function isActive($ship, $id, $turn = null){
		if ($turn === null) $turn = TacGamedata::$currentTurn;
		foreach ($ship->getOfficers() as $entry){
			if ($entry['id'] === $id) return self::serves($entry, $turn);
		}
		return false;
	}

	/* The earliest turn the post was destroyed: by a damage row of its own, or by its Structure block
	   (for a post spanning several locations, the turn the LAST of its blocks fell). null = never.
	   ⚠️ Mirrors ShipSystem::isDestroyed's cascade, but without "undestroyed": a revival by Self Repair
	   does not bring an officer back. The block's fall turn is the officer's out turn, which is right:
	   isDestroyed() lets a system fall off one turn AFTER its block, so he serves the turn it falls. */
	private static function disabledTurn($ship, $post){
		$turn = self::firstDestroyedTurn($post);
		if (!($post instanceof Structure) && !$post->getSurvivesStructureDestruction()){
			$blocks = $post->getStructureSystem();
			$cascade = null;
			if (is_array($blocks)){
				$all = true;
				foreach ($blocks as $block){
					if (!$block) continue; //isDestroyed() treats a missing block as fallen too
					$fell = self::structureFallTurn($ship, $block);
					if ($fell === null){ $all = false; break; }
					$cascade = ($cascade === null) ? $fell : max($cascade, $fell);
				}
				if (!$all) $cascade = null;
			} else if ($blocks){
				$cascade = self::structureFallTurn($ship, $blocks);
			}
			$turn = self::earlier($turn, $cascade);
		}
		return $turn;
	}

	//a section's block also falls with the PRIMARY Structure, as in ShipSystem::isDestroyed
	private static function structureFallTurn($ship, $block){
		$turn = self::firstDestroyedTurn($block);
		if ((int)$block->location !== 0){
			$primary = $ship->getStructureSystem(0);
			if ($primary && $primary !== $block) $turn = self::earlier($turn, self::firstDestroyedTurn($primary));
		}
		return $turn;
	}

	private static function firstDestroyedTurn($system){
		$first = null;
		foreach ($system->damage as $damage){
			if ($damage->destroyed) $first = self::earlier($first, (int)$damage->turn);
		}
		return $first;
	}

	private static function earlier($a, $b){
		if ($a === null) return $b;
		if ($b === null) return $a;
		return min($a, $b);
	}

	/* ------------------------------------------------------------------ the kill roll */

	/* Criticals::setCriticals, right after Pass 1 (which made the posts' own rolls) and before Pass 2's
	   Self Repair, behind TacGamedata::$officersPresent. Every post holding an officer still in action
	   - or put out of it THIS turn, since "make a roll even if the system is totally destroyed" - that
	   took damage through its armour this turn (isDamagedOnTurn, Pass 1's own trigger) is tested once,
	   and 20 or more kills every officer posted there.
	   The roll is the post's own from Pass 1 when it made one this turn; otherwise (Pass 1 skips a
	   destroyed system, and a missile launcher whose magazine exploded never reached the parent) the
	   same formula is rolled here.
	   ⚠️ Writes straight through Manager::insertIndividualNote - a sweep inside advance()
	   ([[arch_individual_notes_and_phase_hooks]]) - and updates the in-memory list, so anything later
	   in this request reads him as killed. */
	public static function rollKills($ships, $gamedata){
		$turn = (int)$gamedata->turn;
		foreach ($ships as $ship){
			$list = $ship->getOfficers();
			if (empty($list)) continue;
			$byPost = array();
			foreach ($list as $i => $entry){
				if ($entry['how'] === 'killed') continue;
				if ($entry['out'] !== null && $entry['out'] < $turn) continue; //out on an earlier turn
				$byPost[$entry['post']][] = $i;
			}
			$changed = false;
			foreach ($byPost as $postId => $indexes){
				$post = $ship->getSystemById($postId);
				if ($post === null || !$post->isDamagedOnTurn($turn)) continue;
				$roll = $post->getCritRollOnTurn($turn);
				if ($roll === null) $roll = self::rollFor($ship, $post);
				if ($roll < self::KILL_ROLL) continue;
				foreach ($indexes as $i){
					$id = $list[$i]['id'];
					$list[$i]['out'] = $turn;
					$list[$i]['how'] = 'killed';
					$changed = true;
					//notekey_human is varchar(40) and an overflow aborts the whole advance: the longest
					//planned label, "Expert Software Engineer killed", is 31
					Manager::insertIndividualNote(new IndividualNote(-1, $gamedata->id, $turn, $gamedata->phase,
						$ship->id, $post->id, self::KILL_NOTE, substr(self::getLabel($id) . ' killed', 0, 40), $id));
				}
			}
			if ($changed) $ship->setOfficers($list);
		}
	}

	/* ShipSystem::testCritical's formula for a post that made no roll this turn: d20 + floor(total
	   damage) + the system's and the ship's critRollMod - plus a Hyach scanner's "damage halved for
	   critical rolls", which its own testCritical adds to critRollMod around the parent's roll. Since
	   Stage 3 an Expert Scanner can be posted there, so a destroyed one must roll as a captured one
	   does. ⚠️ MIRROR of Scanner::testCritical and ElintScanner::testCritical (the ELINT one wraps the
	   Scanner one, so a hull with both abilities halves twice there, and here). */
	private static function rollFor($ship, $post){
		$mod = $post->critRollMod;
		if ($post instanceof Scanner){
			$half = round($post->getTotalDamage() / 2);
			if ($post instanceof ElintScanner && $ship->getSpecialAbilityValue("HyachELINTSensors")) $mod -= $half;
			if ($ship->hasSpecialAbility("HyachSensors")) $mod -= $half;
		}
		return Dice::d(20) + floor($post->getTotalDamage()) + $mod + $ship->critRollMod;
	}

	//DBManager's bulk note loop hands every OffKilled note here instead of to its system (§4.4)
	public static function isKillNote($note){
		return $note->notekey === self::KILL_NOTE;
	}

	/* ------------------------------------------------------------------ effects */

	/* Every apply method takes ($ship, $entry, $turn): the officer's runtime entry (his post) and the
	   turn the load is for. ⚠️ Each stat change is a MIRROR PAIR with the lobby's read-time preview in
	   officers.js (`stats`, reactorBonus, displayFireControl) - enhancementsDifferential.js compares them. */

	private static function applyHelmsman($ship, $entry, $turn){
		$ship->iniativebonus += 5;
	}

	//Expert Helmsman: "wins ties" - TacGamedata::sortShips, behind $officersPresent
	public static function winsInitiativeTies($ship){
		return self::isActive($ship, 'OFF_HELM');
	}

	//Expert Helmsman: -1 on the meteor impact chart's die - RammingAttack::resolveMeteors
	public static function meteorChartModifier($ship){
		return self::isActive($ship, 'OFF_HELM') ? -1 : 0;
	}

	/* Expert Scanner: the EW points he lets the ship hold back for the late window, ON TOP of the EW
	   Detector ladder (user ruling 2026-10-10) - EW::getDetectorAllowance. The pool clamp, the window
	   and the write are the detectors' own and need nothing. ⚠️ MIRROR PAIR with officers.savedEwBonus. */
	public static function savedEwBonus($ship, $turn = null){
		return self::isActive($ship, 'OFF_SCAN', $turn) ? 1 : 0;
	}

	/* Expert Technician: the reactor's output, raised at load. `output` is sent to every client
	   unconditionally and power.js reads it, so the client's power balance needs nothing (§4.6: it
	   reaches the enemy too, as the refit star's numbers do). */
	private static function applyTechnician($ship, $entry, $turn){
		$reactor = $ship->getSystemById($entry['post']);
		if ($reactor !== null) $reactor->output += self::technicianBonus($ship, $reactor, $turn);
	}

	/* The best of his three: +2, or a third (rounded down) of the largest powerReq among the systems
	   destroyed BEFORE this turn - the power a normal reactor lost with them. isDestroyed($turn - 1)
	   is the state at the end of the previous turn, Self Repair revivals included (a revived system
	   draws power again), and pre-battle damage counts on turn 1. The reactor itself is never counted.
	   ⚠️ MIRROR PAIR with officers.reactorBonus (the lobby, where only pre-battle damage exists). */
	public static function technicianBonus($ship, $post, $turn){
		$largest = 0;
		foreach ($ship->systems as $system){
			if ($system === $post || (int)$system->powerReq <= 0) continue;
			if (!$system->isDestroyed((int)$turn - 1)) continue;
			$largest = max($largest, (int)$system->powerReq);
		}
		return max(2, intdiv($largest, 3));
	}

	/* Expert Geneticist: +2 to be hit is +2 on both profiles, the way Poor Crew's +1 and the C&C's
	   ProfileIncreased critical are written - so every to-hit path, server and client, already has it. */
	private static function applyGeneticist($ship, $entry, $turn){
		$ship->forwardDefense += 2;
		$ship->sideDefense += 2;
		$ship->toHitBonus -= 1;
	}

	/* Matter Weapons Expert: +1 to every non-null fire control of every Matter MODE, in fireControlArray
	   AND in the live fireControl when the current mode is Matter - Weapon::changeFiringMode re-reads the
	   array straight over fireControl, so a bump to the scalar alone is gone at the first mode switch
	   (the Gunsights trap, §3.1). isModified makes weapon::stripForJson send both, and the client
	   rebuilds the tooltip's fire control line from them (shipManager.systems.initializeSystem). */
	private static function applyMatterExpert($ship, $entry, $turn){
		foreach ($ship->systems as $system){
			if (!($system instanceof Weapon) || !self::isMatterWeapon($system)) continue;
			if (!empty($system->fireControlArray) && is_array($system->fireControlArray)){
				foreach ($system->fireControlArray as $mode => $fc){
					if (is_array($fc) && self::isMatterMode($system, $mode)) $system->fireControlArray[$mode] = self::bumpFireControl($fc);
				}
			}
			if (self::isMatterMode($system, $system->firingMode)) $system->fireControl = self::bumpFireControl($system->fireControl);
			$system->isModified = true;
		}
	}

	//Matter Weapons Expert: +1 damage, read by Weapon::getBonusDamage for a shot in a Matter mode
	public static function matterDamageBonus($ship){
		return self::isActive($ship, 'OFF_MWE') ? 1 : 0;
	}

	/* "Only for factions with matter weapons" - asked of the hull, since a hull without one gains nothing
	   from him. A weapon counts when any of its modes is Matter. */
	private static function hasMatterWeapon($ship){
		foreach ($ship->systems as $system){
			if ($system instanceof Weapon && self::isMatterWeapon($system)) return true;
		}
		return false;
	}

	private static function isMatterWeapon($weapon){
		if ($weapon->weaponClass === 'Matter') return true;
		return is_array($weapon->weaponClassArray) && in_array('Matter', $weapon->weaponClassArray, true);
	}

	//a mode missing from weaponClassArray keeps the weapon's own class, as changeFiringMode leaves it
	private static function isMatterMode($weapon, $mode){
		$class = isset($weapon->weaponClassArray[$mode]) ? $weapon->weaponClassArray[$mode] : $weapon->weaponClass;
		return $class === 'Matter';
	}

	/* +1 to every NON-NULL entry. null means "cannot target this size class" and must stay null:
	   null + 1 is 1 in PHP (Enhancements::sysEnhBumpFireControl's warning). */
	private static function bumpFireControl($fireControl){
		if (!is_array($fireControl)) return $fireControl;
		foreach ($fireControl as $i => $fc){
			if ($fc !== null) $fireControl[$i] = (int)$fc + 1;
		}
		return $fireControl;
	}

	/* ------------------------------------------------------------------ payload */

	/* BaseShip::stripForJson, after the enhancement fields.
	   - The LIST goes to the owner's team, and to everybody once the game is over (D4), with out/how
	     only once he has left action.
	   - The STATS a serving officer moved (his registry `publish` list) go to EVERY viewer (user ruling
	     2026-10-10): the ship tooltip's "base" initiative, the profiles and the to-hit bonus have to be
	     the ship's real ones - a hidden +5 misleads more than it hides, and the shooter's own hit-chance
	     preview needs a Geneticist's profiles. So an enemy reads the numbers but not who is behind them.
	     A Technician's reactor output and a Matter expert's fire control need no line here: output and
	     an isModified weapon's fire control are sent by the systems themselves.
	   ⚠️ Cosmetic secrecy, like the refit star - §4.6 lists the numbers that reach the enemy. */
	public static function addForJson($ship, $strippedShip){
		$list = $ship->getOfficers();
		if (empty($list)) return;
		foreach ($list as $entry){
			if (!self::serves($entry, TacGamedata::$currentTurn)) continue;
			if (empty(self::$registry[$entry['id']]['publish'])) continue;
			foreach (self::$registry[$entry['id']]['publish'] as $field) $strippedShip->$field = $ship->$field;
		}
		if (!TacGamedata::$currentGameFinished && !$ship->isRevealedToCurrentViewer()) return;
		$out = array();
		foreach ($list as $entry){
			$row = array('id' => $entry['id'], 'post' => $entry['post']);
			if ($entry['out'] !== null){
				$row['out'] = $entry['out'];
				$row['how'] = $entry['how'];
			}
			$out[] = $row;
		}
		$strippedShip->officers = $out;
	}
}
?>
