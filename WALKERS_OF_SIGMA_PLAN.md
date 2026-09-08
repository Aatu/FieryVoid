# Walkers of Sigma-957 — Implementation Plan

New Ancient faction. Nine new systems, three of which need machinery FV does not have today.
This document is the long-form record; update it as stages land.

**Status: Stages 0–9 COMPLETE (Stages 8 and 9 on 2026-09-06). Stage 10 (EW Detector) remains.**
Written 2026-09-02 after a full survey of the existing seams.

**Faction string is `Walkers of Sigma-957`** — plural, hyphenated, exactly as spelled here. It is
the switch key in `gamelobby.js`, the directory-map key in `ShipLoader::getFactionDirMap()`, the
filename of the lobby's per-faction JSON, and (D7) the CPD adaptation key. Getting it wrong is
silent everywhere.

---

## 0. Decisions already taken (user, 2026-09-02)

| # | Question | Ruling |
|---|---|---|
| D1 | EDF attribute drain on capital ships | Thrust roll reduces **Engine output**; Energy roll reduces **Reactor output**. Thrusters untouched. Flights use their real `freethrust`. |
| D2 | Sensor Charge Transceiver interaction | **Hex-target weapon with `canSplitShots`**, shots = manoeuvres available this turn. Each pick is a waypoint; yellow arc sprites draw the confirmed path, blue arcs from the last waypoint show reachable next hexes. No new plotting engine. |
| D3 | Energy Draining Net geometry | **Pairwise links AND closed-area fill.** Reuse the Gravitic Mine zone code. |
| D4 | Control-sheet numbers | **User supplies per system, per stage** — as a **Walker test ship carrying a basic version of that system**, so the stats arrive as a real hull file rather than a table. Classes get complete mechanics with clearly-marked placeholder stat tables until then. |
| D5 | EDF and concealment | **Out of scope.** No Walker hull has a stealth function, so the "a concealed ship discloses itself by projecting a field" case cannot arise. See §2.1 for when it would become live again. |
| D6 | Wide-Beam declaration timing | **Firing mode, chosen in the Fire phase.** The Prepare-Weapons timing deviation is accepted. |
| D7 | CPD adaptation scope | **The raw `$ship->faction` string.** No faction families. |
| D8 | EDN corridor choice | **Deterministic, no UI** — but tie-broken toward the corridor a player would pick: prefer a hex containing an enemy unit. |
| D9 | CPD adaptation vs Shading Field (2026-09-03) | Adaptation **may eat into the shaded bonus** — the Shading Field's whole defensive contribution, doubling included, is fair game. It has **no effect on the field's stealth/detection mechanics**. §3.4. |
| D10 | CPD runtime cost (2026-09-03) | *"A rare weapon in the overall game"* — every process that makes it work sits **behind cheap gates**. One static boolean, `TacGamedata::$cpdAdaptationPresent`, and no autoload in a game without it. §3.4. |

Everything below assumes these.

---

## 1. What already exists — the seams we build on

This faction needs remarkably little new machinery, because four of the hardest requirements
already have a proven implementation in the tree. Verified 2026-09-02.

### 1.1 Split shots — already fully wired, server and client
`canSplitShots` + `maxVariableShots` + `specialHitChanceCalculation` is a complete, live feature
used by the Slicer Beams, Ballistic Torpedo and Vorlon Discharge Gun.

- Server reference implementation: `MolecularSlicerBeamL`, [molecular.php:834](source/server/model/weapons/molecular.php#L834) —
  read its class comment before writing any of ours, it is the best-documented weapon in the tree.
- Its `beforeFiringOrderResolution` shows the **allocation-token pattern**: the client encodes a
  per-shot payload into `FireOrder->notes` (`MSB|d:<dice>|s:<set>`), the server re-clamps it against
  the real pool and strips the token. Both the Lightning Array's combined fire and the SCT's
  waypoints use this channel.
- Client entry points: `weaponManager.targetShip` → `doMultipleFireOrders`, and — the one we need
  for the SCT — `weaponManager.targetHex` → **`doMultipleHexFireOrders`**,
  [weaponManager.js:3843](source/public/client/weaponManager.js#L3843). Three weapons already
  implement it (`GravityNet`, `ProximityLaserNew`, `EWGraviticTractingRod`).
- ⚠️ `$this->guns` padding in `beforeFiringOrderResolution` is load-bearing for
  `Firing::automateIntercept`. Copy the Slicer's comment and its **skip for manual `intercept`
  orders** — that exact bug produced 44 defensive shots against 4 missiles in game 4306.

### 1.2 Accelerator weapons — already a pattern, not a feature
Damage keyed off `$this->turnsloaded`, with `$normalload` as the charge ceiling.
Reference: `LaserAccelerator`, [lasers.php:1378](source/server/model/weapons/lasers.php#L1378)
(`loadingtime = 2, normalload = 4`, `getDamage`/`setMinDamage`/`setMaxDamage` switch on
`turnsloaded`, and `stripForJson` re-publishes the min/max arrays so the client tooltip tracks).

**"Does not begin the game fully charged"** is a one-method override:
`Weapon::getStartLoading()` ([weapon.php:1011](source/server/model/weapons/weapon.php#L1011))
returns `new WeaponLoading($this->getNormalLoad(), …)`. Return `0` in the first slot instead.
Nothing else needs to change — `onConstructed` writes that straight into `tac_systemdata`.

### 1.3 Stored, launch-some-or-all ballistics — already a pattern
The Energy Draining Mine's "store up to 3, launch any number" is exactly `BallisticTorpedo`,
[torpedo.php:79](source/server/model/weapons/torpedo.php#L79):
`loadingtime = 1, normalload = N, canSplitShots = true, ballistic = true, hextarget = true`,
with the client's `initializationUpdate` setting `maxVariableShots = this.turnsloaded`
([torpedo.js:15](source/public/client/model/weapon/torpedo.js#L15)) and `checkFinished`
closing the menu when the pool is spent.

### 1.4 A computed hex-set published to the client — already a pattern
`TacGamedata::$blockedHexes` ([TacGamedata.php:55](source/server/model/TacGamedata.php#L55)) is
built once by `setBlockedHexes()` ([TacGamedata.php:1915](source/server/model/TacGamedata.php#L1915))
during `onConstructed`, serialised at [TacGamedata.php:181](source/server/model/TacGamedata.php#L181),
and consumed by BOTH `Weapon::isLoSBlocked` ([weapon.php:3025](source/server/model/weapons/weapon.php#L3025))
and the client's identical `mathlib.isLoSBlocked` inside `targetHex`.

**The EDF hex map is the same shape and should be built the same way.** That gives the client a
free, exact mirror of the to-hit penalty with no new sync mechanism.

### 1.5 Hex-line tracing — already written
`getHexLine(OffsetCoordinate $start, OffsetCoordinate $end)`, a cube-interpolating static on the
Spatial Cutter, [specialWeapons.php:11101](source/server/model/weapons/specialWeapons.php#L11101).
Used by both the EDF targeting penalty and every SCT path segment. **DONE (Stage 0):** it now
lives on `HexZone` (`HexZone::line`), with `SpatialCutter::getHexLine` kept as a delegating alias.

### 1.6 The critical machinery already does everything the three crit tables ask for
`ShipSystem::testCritical` ([ShipSystem.php:1370](source/server/model/systems/ShipSystem.php#L1370))
already rolls `Dice::d(20) + floor(getTotalDamage()) + $add`, which is verbatim
"roll a d20 and add the number of damaged boxes". Successive-roll escalation is free:
`hasCritical($type, $turn)` returns a **count**, not a bool.

Criticals carry a `param` (`tac_critical.param varchar(200)`,
[emptyDatabase.sql:195](db/emptyDatabase.sql#L195)) and `ShipSystem::sumCriticalParam` sums it —
that is the persistence channel for the EDF's rolled drain magnitudes. **No schema change anywhere
in this project.**

### 1.7 The drain's four targets each already have exactly one choke-point
| Rules effect | Where it plugs in |
|---|---|
| Initiative | `BaseShip::getCommonIniModifiers` ([ShipClasses.php:317](source/server/model/ships/ShipClasses.php#L317)) — already reads a stack of crits off `CnC`. ⚠️ **FV initiative is d100: every modifier here is 5× its tabletop value.** |
| Total EW | `EW::getScannerOutput` ([EW.php:42](source/server/handlers/EW.php#L42)) — already subtracts `RestrictedEW` and `SensorLoss`. |
| Energy | `Reactor` ([baseSystems.php:1018](source/server/model/systems/baseSystems.php#L1018)) — `outputType = "power"`, honours `outputMod`. |
| Free thrust | `Engine` ([baseSystems.php:1495](source/server/model/systems/baseSystems.php#L1495)) — `outputType = "thrust"`, honours `outputMod`; flights use `$ship->freethrust`. |

### 1.8 Per-system enhancements — machinery exists, one gate blocks us
`Enhancements::setSystemEnhancementOptions` ([Enhancements.php:3441](source/server/model/ships/Enhancements.php#L3441))
plus the `eligible`/`limit`/`price`/`apply` registry quartet is exactly the right shape for
Wide-Beam and EDF Range — and the per-array pricing the rules demand ("the player pays to enhance
each one separately") is only possible per-system.

⚠️ **[Enhancements.php:3445](source/server/model/ships/Enhancements.php#L3445) is
`if($ship->factionAge > 2) return;`** — a blanket refusal for Ancients. See §3.3 for the fix.

### 1.9 Zone geometry — already written, in the wrong place
`GraviticMine` carries a proven, exhaustively-verified hex-zone implementation:
`convexHull` ([gravitic.php:2796](source/server/model/weapons/gravitic.php#L2796)),
`pointInPolygon` ([gravitic.php:2825](source/server/model/weapons/gravitic.php#L2825)),
`getHexTouchTolerance` ([gravitic.php:2773](source/server/model/weapons/gravitic.php#L2773)) and
`isUnitInShearingZone` ([gravitic.php:2706](source/server/model/weapons/gravitic.php#L2706)).
All `private`. The EDN needs them; Stage 0 extracts them (see §2.3).

### 1.10 Adding the faction itself is cheap
A ships directory `source/server/model/ships/walkers/`, `$this->faction = 'Walkers of Sigma-957'`
on each hull (`ShipLoader::getFactionDirMap` derives the mapping at runtime), one tier line in
`gamelobby.js`'s switch ([gamelobby.js:120](source/public/client/gamelobby.js#L120) neighbourhood —
`'Tier Ancients'`), plus optional prose in `factions-tiers.php` and
`ammo-options-enhancements.php`. `$factionAge = 3` (Ancient).

⚠️ Filename must equal class name or the ship **silently does not exist**.

**As built (Stage 1, 2026-09-03).** It was that cheap: `Traveler.php` already existed, so the whole
stage was the faction string (`Walker of` → `Walkers of Sigma-957`), the `gamelobby.js` tier case,
and a statics regen. Three things worth knowing next time:

- **The lobby reads `source/public/static/json/<faction>.json`**, written by
  `generateStaticShipFile.php` and keyed on the faction string. A faction **rename leaves the old
  file behind**, and the lobby then lists both spellings as separate factions with the same hull in
  each. `source/public/static/` is gitignored, so nothing warns you. Delete the stale file by hand.
- **`fvbuild.ps1 -Statics` is not optional** for a new hull — without it the ship exists on the
  server and is invisible in the lobby.
- **`Traveler`'s hit chart already names Lightning Array, Chromatic Pulse Driver and Energy
  Draining Field**, which do not exist yet, and the ship-data validator fails on all five entries
  ("every hit here is silently rerouted to Structure"). They are **commented out in the hull with
  `//STAGE n` markers**; each of Stages 2, 3 and 4 uncomments its own row when it adds the system.
  Restoring a row is a two-character edit — do not forget it, a system with no hit-chart entry can
  never be hit.

---

## 2. The three new shared abstractions

Deliberately only three. Every system below is built out of these plus existing patterns.

### 2.1 `EdfField` — the field as a published hex map

**A new interface, not a base class:**
```php
interface EdfSource {          // implemented by systems AND by the EDM terrain unit
    public function getEdfRadius($turn);   // 0 for a Net; the crit-reduced radius otherwise
    public function isEdfActive($turn);    // offline / destroyed / voluntarily deactivated
}
```

**`TacGamedata::$edfHexes`** — built once in `onConstructed` immediately after `setBlockedHexes()`,
same shape, same lifecycle, serialised in `stripForJson` alongside it:

```
$edfHexes = [ 'q,r' => [ 'teams' => [teamId => true], ... ], ... ]
```

Keyed by hex so overlapping fields collapse for free — which is precisely the rules requirement
that *"overlapping hexes are only counted once"* for the targeting penalty. `teams` records which
teams' fields cover the hex, which is what makes *"the rest of the fleet of the ship deploying the
EDF is immune"* a single array lookup.

**Why a map and not per-ship distance checks:** the targeting penalty has to be evaluated for
every fire order in the game, and the client has to mirror it exactly to predict hit chance.
`blockedHexes` proves the map-once-publish-once shape works and stays in sync.

**Three consumers:**
1. `Weapon::calculateHitBase` — walk `getHexLine(shooterPos, targetPos)`, count hexes in
   `$edfHexes` not covered by the shooter's own team, `×2` per hex when
   `$this->weaponClass` is `Plasma` or `Antimatter` **and** `$this->factionAge < 3`.
   Client mirror in `weaponManager.calculateHitChange`.
2. `Weapon::doCollateralDamage` ([weapon.php:2344](source/server/model/weapons/weapon.php#L2344)) —
   the flash-suppression and 25%/50% rules.
3. The renderer — a translucent field overlay, reusing `HexagonSprite`.

**Not per-viewer**, exactly like `blockedHexes` — which is correct, because an EDF is a visible
phenomenon. **D5: concealment is out of scope**; no Walker hull has a stealth function, so a
concealed ship disclosing itself through its own published field cannot happen.

⚠️ **That is a property of the FLEET, not of the design.** The moment an EDF is granted to a hull
that can conceal itself — a Walker refit, a custom hull, a `whatif` pack — the map discloses its
position to every viewer, silently and with no error anywhere. If that day comes the answer is that
a concealed ship projects no field, and the test belongs in `setEdfHexes()` next to the
`isReinforcement()` guard `setBlockedHexes()` already carries for exactly this class of leak
([TacGamedata.php:1921](source/server/model/TacGamedata.php#L1921)).

⚠️ **Per-load static reset.** Any memoisation added here must be reset in
`DBManager::getSystemDataForShips`, immediately before the `onIndividualNotesLoaded` sweep — one
HTTP request loads gamedata **twice** (`Manager::advanceGameState`, then the phase's own
`getTacGamedata`) and a guard that is not reset there leaks between the two. This is the single
most expensive trap in this codebase; it cost a whole investigation on the Gravitic Augmenter.

### 2.2 `EdfExposure` — the drain, as param-carrying one-turn criticals

**Where it runs:** `Criticals::setCriticals` pass 2 calls `criticalPhaseEffects($ship, $gamedata)`
on every system of every ship ([criticals.php:96](source/server/handlers/criticals.php#L96)) —
which *is* the rules' "Critical Hit Step". The **EDF system on the Walker ship** owns the sweep, in
the `GraviticMineHandler` shape: one resolver, guarded by a static `$resolvedTurn` so N fields
resolve once and *"additional fields do not provide cumulative modifiers"* is structural rather
than a special case.

**Six new `Critical` subclasses** in `cricialClasses.php`, all `oneturn = true`:

| Class | Lands on | Read by |
|---|---|---|
| `EdfThrustDrain` (param) | victim's `Engine` | `getOutput()` via `sumCriticalParam` |
| `EdfPowerDrain` (param) | victim's `Reactor` | `getOutput()` via `sumCriticalParam` |
| `EdfIniDrain` (param) | victim's `CnC` (flight: sample fighter) | `getCommonIniModifiers` — **×5**, floor −100 (= tabletop −20) |
| `EdfEwDrain` (param) | victim's `CnC` | `EW::getScannerOutput` |
| `EdfFighterGrounded` | flight's sample fighter | fire-order validation — *"will not be able to shoot the next turn"* |
| `EdfExposed` (`forInfo`, param = consecutive-turn count) | victim's `CnC` / sample fighter | the resolver itself |

**The escalation counter needs no new storage.** `EdfExposed` carries the running count in its
`param`; turn N+1 reads turn N's marker and adds 1, or starts at 1 if absent. Enormous units are
clamped: *"the modifiers are limited to the first die and do not increase"* → always roll one die,
never read the marker.

**Fighter dropout** is a separate branch, not a crit: `Fighter::testCritical`
([fighter.php:274](source/server/model/systems/fighter.php#L274)) rolls `Dice::d(10)` and compares
against remaining health. The EDF needs `2d10`, `+1d10` per successive turn. Cleanest without
touching the signature: set `$fighter->critRollMod += Dice::d(10) * $consecutiveTurns` from the
resolver — `critRollMod` is already summed into that roll and is described in-file as the
"one-time penalty to dropout roll".

⚠️ **`advance()` has already set the NEXT phase before its ship loop.** Never branch on
`$gamedata->phase` inside the resolver. `criticalPhaseEffects` is reached from
`FireGamePhase::advance`, which is safe (it re-loads gamedata), but pass an explicit checkpoint if
this ever needs to run from a second site.

### 2.3 `HexZone` — extracting what already works — **BUILT 2026-09-03 (Stage 0)**

Two pure moves into `source/server/lib/HexZone.php`, with the originals delegating:

- `HexZone::line($start, $end)` ← `SpatialCutter::getHexLine` (plus its three private cube
  helpers, which had no other caller)
- `HexZone::containsUnit()`, `::hull()`, `::pointInPolygon()`, `::touchTolerance()`,
  `::pointToSegmentDistance()` ← the five `GraviticMine` privates

⚠️ **This is the only place in the plan that touches existing, working, subtle code**, and the
Gravitic Mine geometry is subtle in ways that cost real investigation time — the per-angle
tolerance (0.866→1.000, not the constant `sqrt(3)/2` it started as) and a `1e-9` epsilon that is
load-bearing on its own (game 7027 T1 sheared on a `+1.1e-15` margin). It was moved **byte-for-byte
with zero logic edits**: only the method names, the `static`/visibility keywords and the `$this->`
→ `self::` calls changed. The comments and local names inside `containsUnit()` still speak of
"mines" for exactly that reason — read "mine" as "any hex position defining the zone".

**How it was proven, and why the harness alone was not enough.** The local replay corpus does
exercise the shearing path, but barely: exactly **one** game touches it — game 4299, with two
`graviticShear` fire orders (`grep -rl graviticShear tests/replay/baseline/`). One mine layout is
one sample of a geometry whose whole difficulty is the awkward cases — collinear mines, a unit
sitting exactly on the tolerance, a corner-clipping line — so a green `check` was necessary and
nowhere near sufficient. The real proof was a throwaway differential test: the pre-move bodies were
pulled out of `git show HEAD:` into a `HexZoneRef` class and run side by side with the moved ones
over 13,504 randomised cases — 4,000 multi-hex lines, 7,504 zone tests and 2,000 direct hull /
point-in-polygon / tolerance / segment-distance comparisons — with **zero** mismatches. The test
asserted its own branch coverage (2-point in *and* out, 3+-point in *and* out, collinear in *and*
out, plus the same-hex and 0/1-point degenerate inputs) and exited `INCONCLUSIVE` if any bucket
stayed empty, because a differential test that only ever takes one path passes while testing
nothing. **Repeat that method for any future move of this code.**

Replay harness before and after: identical — 133 passed, 1 failed (game 4325, a pre-existing
clean-tree failure; the known-failure game recorded in memory has drifted from 4318 to 4325).

---

## 3. Per-system design

### 3.1 / 3.2 Lightning Array + Medium Lightning Array — **BUILT 2026-09-03 (Stage 2)**

`class LightningArray extends Weapon` and `class MediumLightningArray extends LightningArray`, both
in `specialWeapons.php` (server) and `special.js` (client) — that is where Walker weaponry goes,
because it is all `weaponClass = "Electromagnetic"` and there is no `electromagnetic.php`.

**Stats are the real control-sheet numbers** (user, 2026-09-03). Still unconfirmed and marked so
in-file: `$damageType` ("Standard" — switch to Raking if the sheet says otherwise), `$priority` and
`$loadingtime`. Re-statting stays a table edit: no method hard-codes a game number, the tooltip rows
are generated from the tables, and `setMinDamage`/`setMaxDamage` are derived from them too.

⚠️ **`$fireControl` and `$rangePenalty` must equal ROW 1 of their combined tables.** They are the
single-discharge profile and they are what the "Fire control" and "Range penalty" tooltip lines
report. Because the combined tables override them per shot, a mismatch does **not** change what gets
rolled — it just makes the ship window quote numbers the weapon never uses, which is worse than a
visible bug. They were out of step when the control sheet first landed and are now aligned.

**Combined fire, as built — REVISED 2026-09-03 after play testing.** ⚠️ **The allocation dialog is
gone.** These are ordinary split-shot weapons now, exactly like the Vorlon Discharge Gun: a gun IS a
discharge, one click declares one, and in Combined Fire mode a further click on the **same target**
fuses another discharge into the shot already standing there rather than declaring a second one.
Four clicks on one ship produce ONE 4-discharge shot; four clicks on four ships produce four single
ones. Single Shots mode never fuses. (The first build asked with a `confirm.askForMultipleValues`
dialog borrowed from `MolecularSlicerBeamL`; the user's verdict was that the guns/discharges
distinction it implied was confusion, not a feature.)

The count rides to the server in `->shots` **and nowhere else** — the same field every other
split-shot weapon carries its shot count in, and a whitelisted `FireOrder` constructor argument, so
it survives the POST rebuild on both the ship and the fighter branch. ⭐ The old `LA|n:<count>` token
in `->notes` is **removed on both sides**, because `->notes` has a job already: `notes` must read
exactly `"Split"` and `damageclass` exactly `'Sweeping'`, or the shot never appears in the target's
INCOMING list (`weaponManager.getAllBallisticsAgainst` admits a type-`normal` order **only** on
`damageclass === 'Sweeping'`) and its ballistic line is updated rather than re-created
(`BallisticIconContainer` keys that on `notes === 'Split'`). Those two literals are the split-shot
contract, not decoration — a weapon that invents its own values silently vanishes from the list the
shooter uses to see what it has committed (see "AND THAT ROW IS FOR THE SHOOTER" below).
`beforeFiringOrderResolution` re-clamps `n` against the tabled rows and then against what is
left in the pool, stashes it per order id, and sets `->shots = 1`. An order the pool cannot pay for
gets **0 discharges and does 0 damage** — the Slicer's behaviour, chosen because a 0-damage line in
the combat log is a loud failure and a silently-dropped order is not.

⚠️ **Growing a shot works by REPLACING its order, not by mutating it.** `doMultipleFireOrders` splices
the standing order out and returns a replacement carrying one more discharge, so the declaration stays
on `targetShip`'s ordinary push → `checkFinished()` → unselect path. Pushing from inside the hook and
unselecting there — which the dialog version did — splices `gamedata.selectedSystems` while
`targetShip` is still iterating it. The regenerated id is identical, because the array is the same
length again. ⚠️ `checkFinished()` therefore also has to call `updateGunAccounting()`: it is the first
moment the pushed order is visible to the weapon, and `->guns` must be fresh before anything reads the
manual-intercept cap.

**Fusing moves THREE things, off three tables keyed by the fused count:** `$combinedDamageArray`,
`$combinedFireControlArray` and `$combinedRangePenaltyArray`. They are applied by two different
mechanisms, and the difference matters:

- **Fire control — a DELTA on `$fireOrder->needed`, not a swap of `$this->fireControl`.**
  `fireControl` is read in several places inside `parent::calculateHitBase()`, and a
  temporarily-mutated copy would be a per-instance mutation of a property the blueprint shares. So
  the parent runs untouched and `needed` is then adjusted by
  `(combinedFC[n][fcIndex] - fireControl[fcIndex]) * 5` (the ×5 is d20 table → d100 roll), guarded
  by `needed <= 0` so the parent's auto-miss marker is never accidentally un-missed.
- **Range penalty — a real override of `calculateRangePenalty($distance)`, NOT a delta.** ⭐ This one
  cannot be a delta: the parent derives the **no-lock and jammer** modifiers from the range penalty,
  and in the `doubleRangeIfNoLock` branch calls `calculateRangePenalty` a second and third time at
  modified distances ([weapon.php:1787](source/server/model/weapons/weapon.php#L1787)). A flat delta
  on `needed` would have moved the base penalty and left both derivatives computing off the
  single-discharge value. Overriding the method keeps all three consistent for free.

  `calculateRangePenalty` is handed **nothing but a distance** on the server, so the fused count
  reaches it out of band: `calculateHitBase` publishes `$activeCombinedCount` for the duration of the
  parent call and clears it in a **`finally`** — without that, a parent that threw would leave a stale
  count standing and silently apply it to the *next* shot the weapon resolved.

  ⭐ **The client mirror needed the argument threaded instead.** `calculateSpecialRangePenalty` used to
  get only a distance too, which was survivable while the dialog set `pendingCombinedCount` around the
  one call that mattered — but with the dialog gone, the number the player reads *before* clicking has
  to describe the shot the click will produce, and that is only knowable from the target. So
  `weaponManager.calculateRangePenalty(distance, weapon, target, calledid, fireOrder)` now threads all
  three down (`computeJammerNoLock` and `computeShotModifiers` too), and every other weapon simply
  ignores the extra arguments. Both mirrors then resolve the count the SAME way, via
  `resolveCombinedCount`, so the fire-control half and the range half always describe one shot:
  1. `pendingCombinedCount` — set only while a click is being turned into an order;
  2. the fire order passed in — the INCOMING list and `calculataBallisticHitChange` both supply one,
     and a **declared** shot must read at its own count, never at a look-ahead, because the ship being
     shot at is deciding what to spend on interception;
  3. otherwise `getPreviewCombinedCount(target)` — what the NEXT click on that ship would fire.

**The Medium's accelerator rule falls out of the pool.** Its pool is `turnsloaded` capped at
`normalload`, so at one turn of charge there is exactly one discharge and *"may not combine until
charged two turns"* needs no rule of its own. ⚠️ **`getStartLoading()` returns loading `1`, not `0`**
(fixed 2026-09-03, game 4329): "does not begin the scenario **fully** charged" means 1/2, and 0 is
below `getLoadingTime()`, so the array could not fire at all on turn 1 and read "0/2" in the ship
window. The full Array is untouched and still starts ready (`getNormalLoad()` falls back to
`getLoadingTime()` = 1 when `normalload` is 0).

**Two firing modes.** `1 Combined Fire` (default) fuses repeat clicks on one target; `2 Single Shots`
makes every click a separate one-discharge order, which is what you want against a fighter flight
where four small shots beat one large one — and the fire-control table makes that concrete, since
fusing costs 6 points against fighters (8 → 2) while *gaining* 2 against capitals (4 → 6).
`beforeFiringOrderResolution` forces a mode-2 order's count to 1 and **ignores** `->shots` on it
rather than clamping it, so a stale or hand-edited client cannot smuggle a fused shot through the
cheap mode. ⚠️ It reads `$order->firingMode`, never `$this->firingMode`: `prepareFiring` calls
`changeFiringMode` only *after* this method, so the weapon's own mode is still last turn's here —
the same trap the Slicer's class comment records. Wide-Beam therefore becomes mode **3** at Stage 8.

⚠️ **`canSplitShots` is now ALWAYS true** — both modes, every pool size, including a pool of 1. It is
what routes a click through `doMultipleFireOrders` at all; the moment it goes false,
`weaponManager.targetShip` falls through to the ordinary path, which declares `weapon.guns` orders of
`defaultShots` each and stamps neither `'Sweeping'` nor `"Split"` — so a one-discharge Medium Array
declared four shots *and* they were invisible to the target's INCOMING list. The first build turned it
off at a pool of 1 to mean "nothing to divide"; with the dialog gone there is nothing to divide
anyway, and the flag only ever meant "this weapon declares its own orders".

**The INCOMING row COUNTS the discharges.** A combined shot is one fire order carrying several
discharges, so the row would read "1x Lightning Array (Combined Fire)" for a 4-discharge bolt.
`ShipTooltipBallisticsMenu` gained `shotsInGroup()` beside the existing Slicer `diceSuffix()`: a
weapon may implement `getIncomingShotCount(fireOrder)` and everything else keeps counting members. It
now reads "**4x** Lightning Array (Combined Fire)". ⚠️ Opt-in rather than a sum of `->shots`, because
a Molecular Slicer order carries DICE there and must stay one shot; and the group's `amount` stays the
MEMBER count, since that is what the disclosure caret opens into.

⭐⭐ **AND THAT ROW IS FOR THE SHOOTER, NOT THE DEFENDER** (user's ruling, 2026-09-03). A direct-fire
weapon declares and resolves in the same phase, so the opponent's gamedata never carries the order in
time: **they cannot see it and cannot manually intercept it.** Manual interception is a BALLISTIC
affair — declared in Initial Orders, resolved a phase later, visible in between — and that gap is the
whole mechanism. The INCOMING list on an enemy tooltip during Firing is a tally of what YOU have
committed to that ship. **Do not over-build defender-facing explanation into a Firing-phase weapon's
row.** The first pass gave the array a `getIncomingLabelSuffix` hook that wrote " (3 discharges)" so
"the defender could price interception"; that audience does not exist, and it was replaced by the
counting hook above. The `'Sweeping'` damageclass is still required — it is what puts the row there
for the shooter at all.

**Withdrawing a shot PEELS one discharge.** ⭐ "Remove a firing order" on a 4-discharge combined shot
makes it a 3-discharge shot; it does not delete the order. Deleting it would hand four discharges back
for one click on a button that says "remove **a** firing order", and there is no way to put three of
them back except by re-declaring. The order's stored `chance` is re-priced at the new count, because
fusing moves the fire control and the range penalty — a peeled shot still quoting the 4-discharge
number would show a hit chance the server will not roll. A Single Shots order is one discharge
already, so it just goes.

### multiModeSplit — the mode is a per-SHOT choice

`protected $multiModeSplit = true`. Fuse a couple of combined shots, switch to Single Shots, pepper a
flight with the rest, switch back. Without it `weaponManager.onModeClicked` / `onSetModeClicked` and
`SystemInfoButtons.canChangeFiringMode` all lock the firing-mode selector the moment the first order
is declared, which would make the two modes an either/or choice for the whole turn.

⚠️ **It is `protected` on `Weapon` and the base `stripForJson` does NOT publish it** — the override
has to pass `$strippedSystem->multiModeSplit`, or the client never sees the flag and the lock stays
on. Nothing on the server reads it: `Firing::prepareFiring` already calls `changeFiringMode` per ORDER
before resolving each shot, which is exactly why `beforeFiringOrderResolution` must read
`$order->firingMode` and never `$this->firingMode`.

⚠️ **It also hands WITHDRAWAL to the weapon.** `weaponManager.removeFiringOrderMulti` and
`removeFiringOrder` divert to `removeMultiModeSplit(ship, target)` / `removeAllMultiModeSplit(ship)`
and **return immediately** — no `SystemDataChanged`, no `SplitOrderRemoved`, no flight-movement
redraw, so the weapon fires those itself. `Weapon.prototype`'s versions are **no-ops**, so setting the
flag without overriding both silently kills every remove button.

Withdrawal takes the shot from the mode the weapon is sitting in, matching
`weaponManager.hasOrderForMode`, which is what gates the ship-window button. ⚠️ But the ENEMY-TOOLTIP
button is gated on `hasTargetedThisShip` with **no mode test**, so a mode-only search would leave it
doing nothing at all when the shot at that ship was declared in the other mode — hence
`findWithdrawableOrder` prefers the current mode and falls back to any. It filters to `type ===
'normal'`: a `selfIntercept` marker has its own button and must not be eaten by this one.

### ⭐ Interception: the engine counts ORDERS, this weapon spends DISCHARGES

Both arrays have an intercept rating, and that makes the engine's gun accounting wrong unless it is
corrected — because one combined shot of four is a **single fire order** that empties the whole pool.
Left alone, `Firing::isValidInterceptor` and `Firing::automateIntercept` would both read three
discharges as still available. This is the Slicer's `$guns`-padding problem in a different currency.

Both sites do their arithmetic against `$this->guns`, so `beforeFiringOrderResolution` rewrites it
every turn:

```
guns = pool − dischargesSpentOffensively + numberOfOffensiveOrders
```

With `O` offensive orders, `M` manual `intercept` orders, `S` `selfIntercept` markers and
`D` discharges spent offensively, and taking a manual intercept as costing one discharge and a marker
as costing nothing:

| site | expression | with the rewrite |
|---|---|---|
| `isValidInterceptor` refuses when | `O + M >= guns` | ⟺ `M >= pool − D` ⟺ nothing left ✔ |
| `automateIntercept` grants | `guns − O − M` | `= pool − D − M` = discharges left ✔ |

`S` cancels out of both, which is *why* a marker is free. The client mirrors the identical formula in
`updateGunAccounting()`, because `weaponManager`'s manual-intercept cap
(`counts.offensive + counts.intercept >= weapon.guns`) counts orders the same way.

⚠️ **The client cannot wait for `initializationUpdate`** to do that — it only runs when a system icon
renders ([[arch_lazy_window_side_effects]]), and the player reaches the INCOMING list without
necessarily re-rendering anything. So `resolveFireOrder` and `doMultipleSelfIntercept` call it
directly too.

**Consent differs between the two, and only one of them needed hooks.** `isValidInterceptor` demands
a `selfIntercept` marker when `max(loadingtime, normalload) > 1`. That is 1 for the full Array — it is
simply auto-assigned with whatever it did not fire — and **2 for the Medium**, which therefore must
consent. Because `canSplitShots` is true, `weaponManager.canSelfInterceptSingle` routes that question
through `checkSelfInterceptSystem()`, which is **`false` on `ShipSystem.prototype`**: without the
override the Medium could never consent and so could never defend at all. One marker is consent for
the whole weapon (a second buys nothing, since `S` cancels), and defensive orders are stamped Single
Shots mode.


**How it was proven — FIRST BUILD.** Four throwaway functional tests. The two that survive
re-statting are the range-penalty spy test and the client mirror; the other two were re-run after
every change.

- **Server, mechanics (33 checks):** the hull mounts it and `getSystemsByNameLoc("Lightning Array", 1)`
  — the lookup the hit chart itself uses — finds it and does *not* find it in another section; the
  allocation parsed and stripped; `->shots` reset; over-allocation clamped mid-volley; a spent pool
  yields 0; `n=99` clamps to the largest tabled row; an order that never went through
  `beforeFiringOrderResolution` does 0 rather than firing free; the Medium starts uncharged while the
  full Array does not.
- **Server, range penalty (18 checks):** ⭐ **the structural check a table test cannot make** — a spy
  subclass recorded every `calculateRangePenalty` call the *parent* made during a real
  `calculateHitBase` on a two-ship gamedata, and confirmed the parent **does** reach the override,
  **with the order's fused count live**. The test reports INCONCLUSIVE rather than PASS if the parent
  short-circuits before getting there, because a spy that was never called proves nothing. Plus: the
  transient is cleared on return, and cleared again when the parent path aborts.
- **Server, refinements (44 checks):** Single Shots forced to one discharge even when the order claims
  four; mixed-mode turns; and ⭐ **the gun accounting checked against the REAL engine** —
  `Firing::isValidInterceptor` invoked by reflection across ten scenarios (idle, 1–4 single shots,
  combined 2 and 4, manual intercepts, marker-only, marker + combined) with `automateIntercept`'s
  budget expression reproduced verbatim beside it. Both must agree with the discharges actually left.
  A control case proves the rewrite is what fixes it: without it, a combined-4 shot leaves **3 phantom
  intercepts**; with it, 0.
- **Client (62 checks):** `special.js` **evaluated**, not merely parsed, against stubbed globals
  (a parse would not catch a broken prototype chain — `howto_verify_react_bundle`); both classes
  exist as `window` globals, which is what `SystemFactory`'s `new window[name]` needs; **all six JS
  tables and both mode constants compared field-for-field against the PHP side dumped by
  reflection**; the gun accounting replayed across the same eleven server scenarios with the manual-
  intercept cap agreeing every time; the Medium's guns tracking `turnsloaded`; and the consent hooks.

**How the 2026-09-03 REVISIONS were proven.** Two more throwaway tests, same method, grown as the
refinements landed: **39 server checks and 99 client checks**.

- **Server (39 checks):** the count read off `->shots` alone and `->notes` left as the plain `"Split"`;
  clamping against tables then pool; a spent pool giving 0 damage; ⭐ **a MIXED-MODE turn** — two
  combined and two single orders resolved together, each priced by ITS OWN `$order->firingMode`, with
  the mode-2 order claiming four discharges still firing one and the `guns` arithmetic landing on the
  right answer across the lot; `guns` landing on the discharges left for `isValidInterceptor` **and**
  `automateIntercept` at combined-4 and combined-2; the damage bands (⭐ still compared against the
  1-discharge **ceiling**, because rows 1 and 4 overlap); `getStartLoading()` seeding 1 — asserted
  three ways: below `getNormalLoad()`, at or above `getLoadingTime()`, and producing a pool of 1 —
  while the full Array still starts ready; row 1 of each table still equalling the weapon's own
  `fireControl` / `rangePenalty`; and ⭐ **`multiModeSplit` proven to REACH the client** by calling
  `stripForJson()` on the Lightning Array of a real `Traveler` (it needs a hull behind it — the base
  method asks the ship about Hyach Specialists), with an ordinary weapon on the same hull as the
  control that must NOT publish it.
- **Client (99 checks):** `special.js` evaluated again, then **the declaration flow driven exactly the
  way `weaponManager.targetShip` drives it** (call the hook, push what it returns, ask
  `checkFinished()`): three clicks on one ship producing ONE order carrying 3 with `damageclass
  'Sweeping'` and `notes "Split"`; a fourth fusing to 4; a fifth refused with a message; two targets
  staying two orders; Single Shots producing three separate one-discharge orders; ⭐ **both prediction
  mirrors moving together** — the fire-control delta and the range penalty both stepping from row 1 to
  row 2 as the shot grows, with an assertion that they really differ; ⭐ **a declared order reading at
  its OWN count while the look-ahead reads one higher**; another weapon's order ignored; the Medium
  refusing to combine at one turn of charge and fusing at two; the PHP tables re-compared
  field-for-field; and `confirm.askForMultipleValues` stubbed to **throw**, so reaching the end of the
  run is itself the proof that no dialog is opened any more.

  The refinements added: ⭐ **peeling** — 4 → 3 → 2 → 1 → gone, the order keeping its id throughout,
  one discharge handed back each time (not four), `guns` re-derived, and the stored `chance`
  **re-priced at the new count** and compared against what the mirrors give for that count; a Single
  Shots withdrawal removing a whole order; ⭐ **mixed modes coexisting** — a combined shot and a single
  shot standing together, withdrawal preferring the mode the weapon is in and leaving the other alone,
  and fusing resuming when the mode is switched back; the enemy-tooltip fallback firing when the
  current mode has nothing at that target (with a non-vacuity assertion that it really had nothing);
  a `selfIntercept` marker surviving "remove a firing order"; `removeAll` clearing everything; and the
  INCOMING row counting **discharges, not orders**, with `shotsInGroup` reproduced verbatim and a
  hookless weapon as the control so the Slicer's dice-in-`->shots` cannot start counting as shots.

⭐ **Every test asserts it is not vacuous** — that the two classes really carry different tables, that
the scenarios really produce different gun counts, that the mode constants differ, that the fighter
and capital columns differ, that the two prediction mirrors really move. This earned its keep twice in
one sitting:

1. The damage-band check quietly stopped discriminating once the real stats landed, because row 1
   (25–70) and row 4 (40–220) **overlap**. The non-vacuity assertion caught it; the fix was to sample
   400 rolls of each and compare against the 1-discharge ceiling instead.
2. The table comparison caught the JS mirror still holding the old placeholder numbers after the
   control sheet was applied to the PHP only — exactly the silent client/server drift it exists for.

Regression gate after the revisions: `checkShipData.php` PASS (0 new findings), replay harness
131 passed / 1 failed — game 4325, the known pre-existing clean-tree failure, and its diff is
movement/`waiting` fields, nothing to do with weapons.

**Left for the user:** a hull mount for the Medium Array — the Traveler's hit chart never named one,
so it is built and unplaced. `Traveler` carries one `LightningArray` in the front section and its
chart row is restored. Icons landed 2026-09-03 (`LightningArray.png`, `LightningArrayMed.png`).

### 3.3 Wide-Beam Lightning Array (system enhancement) — **BUILT 2026-09-06 (Stage 8)**

Two registry entries — `SYS_WBLA` (300 pts) and `SYS_WBMLA` (200 pts) — following the existing
`eligible`/`limit`/`price`/`apply` quartet. `limit` is 1 (single-level refit, so `priceStep` is 0).
`eligible` = `$system instanceof LightningArray` (resp. `MediumLightningArray`) and
`!($ship instanceof FighterFlight)` — the latter is already guaranteed by the caller, but state it.

⚠️⚠️ **`MediumLightningArray EXTENDS LightningArray`, so that eligibility test as written offers
BOTH refits on every Medium** — at 300 points and at 200, on the same mount, each adding the same
firing mode. `sysEnhEligibleWBLA` therefore ends `return !($system instanceof MediumLightningArray);`.
Written as an instanceof rather than a `get_class()` equality so a future LightningArray subclass
that is *not* a Medium still gets the full-array offer, which is what the rules describe.

**As built, the purchase is the CAPABILITY and nothing else.** `sysEnhApplyWIDEBEAM` calls
`LightningArray::enableWideBeam()`, which sets `$wideBeamFitted` on that one instance - a plain
instance property, so two arrays on one hull refit independently, exactly what *"the player pays to
enhance each one separately"* asks for. `serialise` names `wideBeamFitted` alone (the class own
`stripForJson` was already republishing `data`, `minDamage`, `maxDamage` and both damage arrays per
instance for Stage 2 reasons, and it adds `active` for the arm). Arming is a per-turn toggle carried
by an individual note, NOT by the purchase - see below.

**The `factionAge > 2` gate** — **NARROWED 2026-09-04 (Stage 5).** It had to become per-enhancement
rather than whole-ship, or no Walker hull could ever be offered a per-system refit. **Do not simply
delete it** — that opens Gunsights, Hardened Armour and the rest to every Shadow, Vorlon and
Kirishiac hull in the game, which is a balance change nobody asked for.

**As built**, and it is smaller than the shape this section recommended because the registry was
already the right place for it:

- a new **`ages` registry slot** — `array(1, 2)` written out loud on each of the five existing
  refits, and **defaulting to `array(1, 2)` when absent**, so an entry added without thinking about
  age keeps the behaviour this file had before the slot existed;
- `systemEnhancementAllowsAge($ship, $enhID)` asks it, and is called from `systemEnhancementsFor`
  **and** from `sanitiseSystemEnhancements` (buy-time validation re-checks it independently);
- `hullAgeHasAnySystemEnhancement($ship)` replaces the two cheap whole-ship exits
  (`systemMayBeEnhanced` and `setSystemEnhancementOptions`), derived from the registry so it cannot
  disagree with the per-enhancement answer.

Stage 8's `SYS_WBLA` / `SYS_WBMLA` therefore need `'ages' => array(3)` and nothing else.

⚠️⚠️ **THE ANCIENT-WEAPON TEST NEEDS BOTH HALVES, and the corpus differential is the only thing
that catches it.** The old line was `$system->factionAge >= 3`, which was the right test only
because no Ancient hull could buy anything at all. Rewriting it as
`$system->factionAge > $ship->factionAge` — "a weapon from a more advanced tech base than its hull",
which is what the rule has always meant — looked obviously equivalent and **silently cost
`OmegaEpsilonDrakh` twelve systems' worth of Gunsights and Hardened Armour offers**: ~40 weapon
classes in `customs.php` declare `factionAge = 2` ("Middle-born"), and `2 > 1` refused every one of
them on a young hull. It is
`$system->factionAge >= 3 && $system->factionAge > $ship->factionAge`: the first half keeps it
about ANCIENT tech, the second stops it refusing an Ancient hull its OWN weapons.

⚠️ **Wide Beam is mode 3, not mode 2** — Stage 2 took mode 2 for `Single Shots`. Add
`MODE_WIDEBEAM = 3` alongside the existing constants in both `specialWeapons.php` and `special.js`,
and remember `beforeFiringOrderResolution` already branches on `$order->firingMode`.

**Per-turn declaration** is a **toggle**, not a purchase-time flag: the enhancement buys the
capability, the array is armed when firing. Effects, in either firing mode:
- `-2 per damage die, minimum 1 per die` — override `getDamage()`; ⚠️ the floor is **per die**, so
  it cannot be applied to the total. `Dice::d(10, $n)` returns a **sum** and cannot express it —
  five dice each floored at 1 can never total under 5, while a floor on their sum would allow 1 —
  so the roll is a loop of `max(1, Dice::d(10) - 2)`. The flat `+N` on the row is not a die and is
  untouched. ⭐ Because the arm is a property of the ARRAY rather than of the shot, `getDamage()`
  reads `isWideBeamShot()` and needs no fire order at all - which sidesteps the Stage 7 finding
  (`Firing::fireWeapons` never re-applies an order mode) rather than having to obey it.
- **The collateral, both halves.** Inside a field the general rule (§2.1) silences a flash weapon
  entirely; a wide beam is the exception the rules carve out, so inside a field it scores the
  ordinary **25%** and outside one it scores **50%**. ⭐ **As built this is two tiny hooks in
  `weapon.php`, not a rewrite of `doCollateralDamage`**: `edfSuppressesCollateral()` wraps the
  existing field test (LightningArray returns false for a wide beam) and `getFlashCollateralAmount()`
  wraps the `round($damage/4)` in `damageOneSheet` (LightningArray divides by 2 or 4). Both default
  to today's behaviour exactly, so every other flash weapon in the game is untouched.
  ⚠️⚠️ **The 50% must be computed from `$damage`, never by doubling the 25% figure** — rounding an
  already-rounded number is systematically high: 10 damage gives 3, doubled is 6, and 50% is 5.
  That is why the amount is a hook taking `$damage` rather than a scaling of the parameter
  `doCollateralDamage` is handed.
- **1-turn cooldown.** ⭐ Not a `$loadingtime` bump, and not the `overloadturns` machinery: it is
  **`calculateLoading()` returning `loading = 0` at the phase −1 turn advance** after a wide-beam
  shot. The parent hands an ordinary loading-1 weapon its charge straight back (fired on T →
  loading 0 → +1 → 1 → loaded on T+1); zeroing that one write is the whole cooldown, and the next
  advance finds no shot on T+1 and restores it. Raising `$loadingtime` instead would have been
  invisible to the client — `loadingtime` is a BLUEPRINT field riding the per-class static bundle,
  the exact trap Stage 7 hit with `powerReq` — whereas `turnsloaded` is already published per
  instance by `Weapon::stripForJson`.
  - ⚠️⚠️ **`overloadturns` has to be zeroed in the same write, and this is the finding to carry.**
    `weaponManager.isLoaded` is `loadingtime <= turnsloaded || loadingtime <= overloadturns`, and
    `calculateLoading`'s phase −1 branch does `overloadturns + 1` for **every** weapon, overloadable
    or not, clamped to `normalload`. A loading-1 weapon therefore sits at `overloadturns = 1`
    permanently (confirmed against `tac_systemdata` in games 4335 / 4337 / 4338), so a cooldown that
    zeroed only `turnsloaded` would have shown a fully loaded array for the whole cooldown turn.
  - ⚠️⚠️ **Nothing on the server refuses an offensive order from an unloaded weapon.** Both intercept
    gates in `firing.php` test `getTurnsloaded() < getLoadingTime()`, but `prepareFiring` /
    `fireWeapons` do not — the client's `isLoaded` is the only gate on that path. So the cooldown
    needed a server half: `beforeFiringOrderResolution` takes the discharge pool to **0** when
    `getTurnsloaded() < getLoadingTime()`, and every order then clamps to 0 discharges and 0 damage
    (this weapon's established loud failure). The two gates agree by construction, because
    `overloadturns` is 0 at the Fire phase for a non-overloading weapon.
  - The cooldown costs the array its **defence** as well, since both intercept gates read the same
    loading — which is what *"stressful on its systems"* ought to mean.

⭐⭐ **A PER-TURN TOGGLE, NOT A FIRING MODE** (user's ruling, 2026-09-06, after a four-mode build the
same day). The rules' *"in all modes"* means *whatever the shot's discharge count* — spreading the
beam and grouping the discharges are orthogonal choices. Modelling that as extra firing modes means
enumerating the product, and **four entries in the selector is what the player pays for it**; the
user's verdict on the working four-mode build was *"mechanically that all seems to work perfectly,
however the UI is a little bit clunky"*. The rules' own framing is a toggle anyway: *"the lightning
array **may be configured** to fire a wide beam"*, one declaration for the array for the turn.

So: two firing modes as before, plus a **"Wide Beam" / "Normal Beam" pair in the array's
`<SystemActivation>` box** during the Fire phase. Arming applies to every shot that array fires this
turn, in either mode. Two booleans carry it — `wideBeamFitted` (the refit, applied at construction
from the stored purchase) and `wideBeamArmed` (this turn's declaration, replayed from the notes) —
and `isWideBeamShot()` demands **both**, which is what makes a hand-written note on an unrefitted
array buy nothing.

**HOW THE TOGGLE REACHES THE SERVER, and why it needed a new hook.** The Fire phase is the one phase
that has never run the generic `generateIndividualNotes` sweep, and it must not start: **34 of the
~80 overrides in the codebase carry no phase guard at all** (one has an explicit `case 3`), so
switching that sweep on in phase 3 would wake every one of them in a phase they have never seen. The
route is therefore:

1. the client sets `->active` and posts `[1]`/`[0]` in the system's `individualNotesTransfer`;
2. `Manager::parseShips` calls `doIndividualNotesTransfer()` on **every** POST in **every** phase —
   this part needed no new plumbing at all — which stashes it on the POST-side array;
3. `FireGamePhase::process` calls the new **`ShipSystem::saveFirePhaseDeclaration()`**, a hook whose
   base version does nothing, inside the existing `$ship->userid != $gameData->forPlayer` guard, so
   a POST can only ever declare for its own units;
4. the advance re-loads gamedata and `onIndividualNotesLoaded()` sets `wideBeamArmed` before
   `prepareFiring` runs.

⚠️⚠️ **Step 3 cannot be gated on the refit.** A POST-side ship is rebuilt WITHOUT enhancements
([[arch_post_side_ship_reconstruction]]), so `wideBeamFitted` reads false there even on a fitted
array; gating the write on it would silently drop every declaration. The refit is re-checked at READ
time, on the real ship. This is the single most expensive recurring trap in the codebase and it bit
again here.

⚠️ **Both states are written, and the highest note id wins.** Writing only on arm leaves a
re-commit stuck on a stale 1. The load query is `turn <= current` and orders by turn and phase only,
so two notes written in the same phase come back in an order MySQL does not promise — ids are
auto-increment, so they are the write order. And the **turn equality test is what makes it a
per-turn declaration**: without it, arming once would arm the array for the rest of the game.

⚠️ **`system.active` is the field the generic activation box reads**, so that is what the arm is
published as (the same thing `ChameleonSensors` does). But that box also treats a *weapon's* "has a
fire order" as active, because for most weapons it IS the fire button — so the array sets
**`activationIsToggle`**, a one-word opt-out added to `SystemActivation.js`, or the toggle would
light up the moment anything was declared whichever way it was actually set.

⚠️ **A defensive order is stamped plain Single** (`getInterceptOrderMode`), and separately
`firedWideBeamOnTurn()` counts only `type == "normal"` orders — so intercepting never costs the
array the following turn.

⚠️⚠️ **THE ONE REAL BUG THE FOUR-MODE DETOUR EXPOSED, worth keeping even though its cause is gone.**
`getCombinableOrder` matched *"an order that is not Single Shots"*, which is the same test as *"an
order in this mode"* only while exactly ONE mode fuses. With Combined **and** Wide Combined both
fusing, a Wide Combined click fused into a standing ordinary Combined shot — and because growing a
shot **replaces its order** (§3.2), the discharge already committed was silently converted into a
wide beam, cooldown and all. It is now an **equality** against the weapon's current mode, which is
what the rule always was, and it stays that way: it generalises to any `multiModeSplit` weapon that
grows more than one kind of shot. ⭐ No test of the new mode in isolation would have found it; the
cross-product of "declare in mode A over a standing order in mode B" did, on the first run.

⚠️ **`isSingleShotMode()` is kept** on both sides even though it is now one comparison. It exists so
the grouping question is asked by name everywhere, precisely so the wide beam can never be confused
with it again.

**Timing deviation, accepted (D6, re-confirmed 2026-09-06):** the rules put this in Prepare Weapons
(Initial Orders); the toggle is in the Fire phase, alongside declaring the shots. The cooldown cost
keeps it a real decision rather than a free upgrade.

⚠️ **Initial Orders was offered and declined.** It is the faithful phase AND the cheap one — the
generic `generateIndividualNotes` sweep already runs there, so the note would have needed no new
hook and no change to any shared phase file at all. The user chose the Fire phase because it keeps
*when you click* unchanged, and the extra machinery (the narrow `saveFirePhaseDeclaration` hook) is
the price of that. If the phase is ever revisited, moving it to Initial Orders **removes** code.

**How it was proven.** Two throwaway tests, the Stage 2 method.
- **Server (143 checks):** the registry slots and the five older refits' `ages` left alone; the age
  gate both ways against a young control hull; the offers on a Traveler, including ⭐ the
  Medium-is-a-LightningArray subclass trap and a non-vacuity assertion that the two arrays really
  are different mounts; the prices, limit and zero `priceStep`; ⭐ **a 2,578-hull corpus scan** in
  which no non-Walker hull gains an offer; the buy applying to the ONE array and not its sibling,
  and `sanitiseSystemEnhancements` dropping `SYS_WBLA` bought on a Medium and re-pricing a legal
  row to 300; ⭐⭐ **the fitted × armed cross-product**, where three of the four combinations must
  do nothing — the one that matters is ARMED BUT NOT FITTED, which is what a hand-written note or a
  refunded refit looks like, and it is checked on the damage roll as well as on the predicate; the
  damage bounds and ⭐ its **mean** measured against `E[max(1,d10-2)] = 3.8` (a 20-dice row can
  never reach either cap in a few thousand samples, which the first draft failed on) in BOTH firing
  modes, with the per-die floor isolated on the 1-discharge row where the minimum IS reachable;
  ⭐⭐ **the toggle's whole round trip** — the POST-side stash on an array that reads as UNFITTED
  (the trap, asserted as such), the note it queues anyway, the un-armed note written too, a silent
  client writing nothing at all, the base hook proven inert on an ordinary system, and the replay
  with **highest-id-wins tested in both array orders** and the per-turn reset proven by a turn-4
  note failing to arm turn 5; the cooldown gated on armed AND fired, with unarmed, never-fired,
  unfitted, `intercept` and `selfIntercept` controls that must all reload normally; ⭐⭐ **the whole
  loading sequence driven the way `advanceGameState` drives it** — fire, advance, the phase-2 and
  phase-3 writes, advance again — asserting the CLIENT's `isLoaded` formula at every step, for both
  arrays, with an unarmed control and a standing assertion that `overloadturns` alone WOULD have
  read as loaded; the collateral at 25 / 50 / 25 with ⭐ the double-rounding case (10 damage → 5,
  not 6) and an armed-but-unfitted control; the damage span moving for BOTH modes at once (the arm
  is not per-mode); and `wideBeamFitted` + `active` reaching the client for the fitted mount only,
  with ⭐ an assertion that the refit publishes **no** per-instance `firingModes` — a per-instance
  mode list would mean the toggle had leaked back into the selector.
- **Client (84 checks):** `special.js` and `systemEnhancements.js` **evaluated**, not parsed,
  against stubbed globals; the constants compared against the PHP side dumped by reflection, plus
  an assertion that no `MODE_WIDE_*` constant survives; the fitted × armed cross-product again, on
  `isWideBeamArmed()` and on `getDieCeiling()`; ⭐ **all four damage spans distinct**, the firing
  mode picking the row and the arm picking the ceiling; the toggle's own controls — labels, the
  activate/deactivate pair flipping, and every gate (phase, ownership, destroyed, offline, and the
  cooldown itself) with a non-vacuity re-check after each; ⭐⭐ **`SystemActivation.js`'s active
  expression reproduced verbatim**, proving an unarmed array with a declared shot reads as INACTIVE
  while an ordinary weapon in the same state reads as active; the note post in phase 3 and its
  silence everywhere else, including that the buffer is cleared so an earlier phase cannot leak;
  ⭐ arming and un-arming RE-PRICING the shots already standing; the firing modes proven untouched
  by all of it (fusing, not fusing, cross-mode refusal, the INCOMING count, the defensive mode);
  and ⭐⭐ **the lobby applier leaving the shared `firingModes` object identical by reference** —
  the old build's whole failure mode was writing to it.

⚠️ Both harnesses stop at the database. The note's actual INSERT and re-load is the one link tested
only in play; the round trip is driven in memory on either side of it.

**Play-test fixes, 2026-09-06.** Two, both in the UI, neither in the rule.

- ⚠️⚠️ **A WEAPON WAS NEVER GIVEN A DEACTIVATE BUTTON AT ALL** — `SystemActivation`'s
  `showDeactivate` was `!system.weapon && …`, so the array could be armed and never un-armed. The
  rule it encodes is right for every other weapon (for them the box IS the fire button, and a shot
  is withdrawn with the top-row "remove fire order" control), so the fix is the same one-word
  opt-out the `isActive` line already uses: `(!system.weapon || system.activationIsToggle)`.
  ⭐ **The two halves of `activationIsToggle` are a pair and a new one must set both** — `isActive`
  answers *"is this lit?"* and `showDeactivate` answers *"is there an off switch?"*, and the first
  was written without noticing the second was gated on the same question.
- **The INCOMING list now names the arm.** `ShipTooltipBallisticsMenu` printed
  `firingModes[order.firingMode]` directly, so an armed and an unarmed shot read identically as
  "Combined" while rolling different dice. Now `Weapon.getFiringModeDisplayName(fireOrder)` - a
  plain `firingModes` lookup for every weapon in the game - with a LightningArray override
  appending `-Wide`, giving `Combined-Wide` / `Single-Wide`. ⭐ It reads the LIVE arm rather than
  anything stored on the order, which is correct by construction: arming covers every shot the
  array fires this turn, which is why `onWideBeamToggled` already re-prices the standing ones.
  ⚠️ It decorates the DISPLAY only - `firingModes` is a shared per-class object and the selector
  still offers exactly two entries.
- **Which mode is current now reads off the buttons.** An ordinary weapon's Activate button is
  orange whether or not it has fired - that colour is the "this menu belongs to a weapon" signal,
  not a state readout - so with both buttons showing, Wide Beam was lit even while the array was
  firing normally. `$isToggle` (`activationIsToggle` again) makes the orange conditional on
  `$active` for a toggle only, so the button that is NOT the current state falls through to the
  idle muted blue and exactly one of the pair is ever lit. Ordinary weapons are untouched.

### 3.4 Chromatic Pulse Driver — **BUILT 2026-09-03 (Stage 3)**

Accelerator (as §3.2) with a second firing mode, `'Scanning'`.

- Scanning mode: `getDamage()` returns 0, no pulse behaviour, but the to-hit roll resolves normally.
- On a hit against a unit carrying **any** `DefensiveSystem` whose `getDefensiveType()` is a shield
  type, record one point of adaptation.

**"The same race" is the raw `$ship->faction` string (D7)** — no faction families, no normalising.
Minbari Federation and Minbari Protectorate are two races and adapt separately; so does any custom
or `whatif` hull carrying its own faction string. ⚠️ Read `faction` off the **target hull**, never
off the ship directory name — [the two do not always match](source/server/controller/shipLoader.php),
which is the whole reason `getFactionDirMap()` exists.

**Persistence:** an `IndividualNote` on the CPD system, `notekey = "CPDSCAN"`,
`notekey_human = <faction>`, `notevalue = <count>`.
⚠️ **`notekey` and `notekey_human` are `varchar(40)` and overflow is a fatal that aborts the whole
player submission** — a 41-character value killed a movement submit once. `substr($x, 0, 40)`.

⚠️ Reset the registry in `DBManager::getSystemDataForShips` — §2.1's double-load trap.
⚠️ *"starting in the next Adjust Ship Systems segment"* — only count notes whose turn is **strictly
less than** the current turn.

---

**As built (Stage 3, 2026-09-03).** Stats for firing mode 1 are the control sheet, supplied per D4
as two rows keyed by turns charged: 1 turn = D3 pulses, max 4, 14 damage; 2 turns = D5 pulses,
max 8, 18 damage. Grouping 15, range penalty 0.5, FC 4/4/4 and intercept 1 are **identical on both
rows**, so they are plain properties rather than table columns. Still unconfirmed and marked so
in-file: `$priority` (5, the inherited Pulse default), the health/power defaults (24/12), the point
cost and the icon (`PulseAccelerator.png` as a placeholder — the class comment says which one line
to change). `$chargeProfile` is the sheet and nothing hard-codes a game number, so a re-stat is a
table edit.

⭐ **IT LIVES IN `pulse.php` / `pulse.js`, NOT in the Walker files.** Every other Walker weapon is in
`specialWeapons.php` / `special.js`; this one is not, and the reason is a hard constraint rather
than taste. It must extend `Pulse`, and **`game.php` and `gamelobby.php` both load `special.js`
BEFORE `pulse.js`** ([game.php:393](source/public/game.php#L393) vs
[:396](source/public/game.php#L396)), so `Object.create(Pulse.prototype)` evaluated inside
`special.js` would read `undefined` at load time and every Traveler would blow up in
`SystemFactory`'s `new window[name]`. The server half followed the client half so the pairing stays
symmetric; a pointer comment sits at the top of each class.

**The accelerator half.** `loadingtime 1 / normalload 2`. `getChargeRow()` is the single authority
and `getPulses`, `rollPulses`, `getDamage` and the tooltip all read it — but `$this->maxpulses` and
`$this->useDie` are ALSO kept in step by `applyChargeProfile()`, because **`Weapon::fire` reads
`$this->maxpulses` directly, twice** ([weapon.php:2125](source/server/model/weapons/weapon.php#L2125)),
for `->shots` and for the interception tally. `getStartLoading()` seeds **1**, not 0, exactly as
`MediumLightningArray` does and for the same reason (game 4329).

⭐ **The Scanning mode swap is `$damageTypeArray`, and it needs `changeFiringMode` inside `fire()`.**
`'Pulse'` → `'Standard'` is what stops `Weapon::fire` collapsing the volley and rewriting `->shots`
with `maxpulses`. But **`Firing::fireWeapons` does NOT re-apply an order's firing mode before
calling `fire()`** — only `prepareFiring` does, and it does so for ALL orders before ANY of them
resolve, leaving the weapon in whichever mode the LAST prepared order used. A driver that prepared
a Pulse order after a Scanning one would have resolved the scan as a pulse volley. `fire()` calls
`changeFiringMode($fireOrder->firingMode)` itself, the same idiom `AoE::fire` uses. This is the
Lightning Array's `$order->firingMode` trap in its second form: **`prepareFiring` sets the mode per
order and `fireWeapons` does not.**

**A scan is one shot, server-authoritatively.** `defaultShotsArray` gives the client `1` in Scanning
mode (rebuilt per instance in `setSystemDataWindow` and republished in `stripForJson`, because the
Pulse-mode entry tracks the charge), and `fire()` clamps `$fireOrder->shots = 1` regardless — a
forged 9-shot scan order banks exactly one point.

**Where a scan is recorded.** `beforeDamage()` — the per-hit hook — which **never calls the parent**
in Scanning mode, so nothing rolls a hit location, nothing touches armour and no `DamageEntry` is
created. It banks into `$pendingScans`, which `generateIndividualNotes` drains into notes;
`FireGamePhase::advance` calls that for every ship immediately after firing resolves. ⚠️ It branches
on *"do I have pending scans"*, **never on `$gamedata->phase`** — trap 3. Everywhere else the method
is reached (Movement, `generateAdditionalNotes`) the list is empty and it is a no-op.

⭐⭐ **PUBLICATION CHANGED FROM THE PLAN: the reduction is applied to the AGGREGATED BUCKET, not
inside each shield class.** The plan said `Shield::getDefensiveHitChangeMod` /
`getDefensiveDamageMod` "and their `EMShield` / `GraviticShield` siblings". That would have been
seven classes and seven client mirrors, and it would still have missed some: the tree has **nine**
systems answering `getDefensiveType() === "Shield"`, four of which are `Weapon` subclasses with
their own near-duplicate implementations (`AbbaiShieldProjector`, `FlareGenerator`,
`PakmaraPlasmaWeb`, `NexusWaterCaster`) plus `ShadingField` and `FlareShielding`.

`BaseShip::getHitChanceMod` / `getDamageMod` ([ShipClasses.php:2824](source/server/model/ships/ShipClasses.php#L2824))
and `FighterFlight`'s two redefinitions already collect every defensive system into
`$affectingSystems[<defensive type>]`, **keeping only the strongest single source per type**. One
line before each `array_sum` therefore:

- lands the reduction **exactly once** however many shields the target mounts — which is the rules'
  own "overlapping shields are not cumulative", for free;
- covers **every** shield-type system in the game with four edits instead of eighteen;
- is a **guaranteed no-op** for every game with no CPD in it (`CpdScanRegistry::$adaptation` is
  empty, and that is the first test).

`CpdScanRegistry::applyToShieldBucket()` holds the whole rule: only the `"Shield"` bucket, only when
it is positive, clamped at 0. The client mirrors it in exactly the same place —
`cpdApplyShieldAdaptation()` in [model/ship.js](source/public/client/model/ship.js), called from
`getHitChangeMod` and `getHitChangeModFlight` — off `gamedata.cpdAdaptation`, a
`{ teamId: { faction: points } }` map published from `TacGamedata::onConstructed`.

⚠️ **`cpdAdaptation` is NULL, never `array()`, when empty** — trap 9, an empty PHP array encodes as
JSON `[]` and the client indexes it as an object. `publishAll()` returns `stdClass` or null.

**Adaptation is published for EVERY team, not just the viewer's.** It is earned by a scanning shot
that resolves and is logged like any other, so it is public knowledge — and a defender needs to see
why their shields are reading low. A chameleon-disguised ship publishes its DISGUISED faction, so a
fleet that adapted to the disguise reads no benefit against the real hull: the deception working,
not a bug.

**A destroyed scanner keeps its knowledge.** The notes stay in the database and the team keeps the
adaptation — it was learned by the fleet, not stored in the hull.

**ShadingField — RULED 2026-09-03 (D9), and it is what the bucket already does.** The line falls
between the Shading Field's two jobs, not inside its shield maths:

- **Its defensive contribution is fair game, shading bonus included.** When the field is shaded its
  hit-chance mod is `output × 2`, and adaptation eats into that doubled figure point for point. The
  user's ruling: *"the CPD's scan reduction can eat into the Shading Field's bonus hit chance
  reduction when it's shaded"*. The alternative reading — cap the reduction at the base `output` so
  the shading bonus is untouchable — was considered and rejected.
- **Its stealth and detection mechanics are untouched, permanently.** *"it has no effect on the
  stealth/detection mechanics of the Shading Field"*. Nothing here may reach the Pre-Turn detection
  forecast, which `arch_stealth_toggle_forecast` requires to stay own-team-only.

That is exactly what falling out of the `"Shield"` bucket gives for free — the field's
`getDefensiveHitChangeMod` has already applied its own doubling by the time the bucket sees it, and
its detection function is not in the bucket at all. **No ShadingField-specific code exists or is
needed.** Read `CHAMELEON_SENSORS_PLAN.md` before touching it anyway.

⭐⭐ **ADVANCED SENSORS ALREADY EAT THE TO-HIT HALF, so half the scan is redundant for the Traveler**
(found investigating game 4332, 2026-09-03 — a user report that the "Defensive Systems" row was
missing from the CPD's hit-chance tooltip). `BaseShip::getHitChanceMod` zeroes any **positive**
defensive mod when the target's `factionAge < 3` and the shooter has `AdvancedSensors`
([ShipClasses.php:2836](source/server/model/ships/ShipClasses.php#L2836)) — the scanner's own
tooltip says it: *"Ignores any defensive systems lowering enemy profile (shields, EWeb…). All of the
above work as usual if operated by advanced races."* The `Traveler` calls `$scanner->markAdvanced()`,
so **against every young or middleborn race its shields contribute 0 to the goal already** — for the
Lightning Array, the Medium Array and all three CPDs alike. Verified on game 4332: all five weapons
read 0 against the Abbai and the Brakiri, all five read −4 against the Torvalus (Ancient), and
knocking `AdvancedSensors` out of `$trav->enabledSpecialAbilities` in memory brings all five to −2
(shield 3 − 1 adaptation) together.

Consequences worth knowing before re-statting:
- **The row's absence is not a CPD bug and not a regression.** Nothing in the hit-chance path is
  weapon-specific here; `pushIfNonZero` simply omits a zero.
- **Against young/middleborn races the scan buys DAMAGE ABSORPTION ONLY** (3 → 2 on the Lakara and
  the Tashkat in 4332). The to-hit half lands against **Ancient and Primordial** races, against any
  Walker hull whose scanner is not advanced, and for any ally the Walkers are shooting alongside.
**The hit-chance tooltip spells the discount out — BUILT 2026-09-04.** The first pass folded the
reduction into "Defensive Systems", which just read one point lower than the target's sheet says
with nothing to explain it. It is now two lines:

```
• Defensive Systems: -20%      <- the target's REAL shielding
• Shield Adaptation:  +5%      <- what the scan bought
```

⭐ **It is a REGROUPING, not a new term, and that is what keeps it honest.**
`Ship.prototype.getHitChangeMod` still returns the ADAPTED total — the number the server rolls,
untouched, for every one of its callers — and gained an optional `outDetail` out-parameter that the
helper fills with the reduction. `computeShotModifiers` adds that back onto `defensiveSystems` for
display and returns it separately; `calculateHitChange` then subtracts the full value and adds the
reduction back, so the goal is bit-identical and the sum-equals-`hitChance` invariant still holds
with both rows in the list. Nothing on the server changed: it builds no tooltips.

⚠️⚠️ **The helper reports what it ACTUALLY removed, never the points held.** The clamp at 0 means
3 points of adaptation against a 1-point shield removes 1 — reporting 3 would put a `+15%` line in a
tooltip whose total only moved 5% and would trip the invariant warning. Hence
`cpdApplyShieldAdaptation` now mutates the bucket in place and returns `before - after`, which is
also why it is shaped differently from its PHP twin (a PHP array is a value, a JS array is not).

Still gated (D10): the out-object is allocated only when `gamedata.cpdAdaptation` is present, so an
ordinary game pays one extra property read per hit-chance preview.

⭐⭐ **AND IT REACHED NOTHING UNTIL 2026-09-04, BECAUSE `gamedata` IS A HAND-MAINTAINED
SINGLETON.** The whole client half - the mirror, the gate, the tooltip split, its 35 green
checks - sat behind `gamedata.cpdAdaptation`, and `gamedata.js` never copied that key off the
payload. `parseServerData()` assigns the payload one NAMED key at a time
([gamedata.js:2703](source/public/client/gamedata.js#L2703), beside `blockedHexes` /
`isStealthPresent` / `areMinesPresent`); a new field on `stripForJson` reaches the page only
when a line is added there. So the server rolled adapted shields and the client previewed
un-adapted ones - the user report was exactly *"the server % seems correct, but the hit chance
tooltip is wrong"*, with no Shield Adaptation row, because the helper returned 0 every time.

The fix is one declaration and one assignment, `serverdata.cpdAdaptation || null`, assigned
**unconditionally** so a replay stepping back to a turn before the first scan clears it again.

⚠️ **Neither test suite could have caught it, and now both do.** Both drove the helper with a
hand-built `gamedata` stub - which is precisely the object the bug was that nothing built.
Three source-audit checks were added to `cpd_stage3_test.php`. **Any future field published
through `TacGamedata::stripForJson` needs the same line in `parseServerData` - check first.**

**"Scanned by Walkers" markers on the shield systems - BUILT 2026-09-04.** A hit-chance
tooltip only tells you about a shot you are already setting up; the defender wanted to see it
on the sheet. `ScannedByWalkers` (a `forInfo` `Critical`, `repairPriority` 0) is hung on every
shield-type system of every unit whose RACE any fleet has analysed, reading *"Scanned by
Walkers (-N shield effectiveness)"*. Public, like the adaptation itself.

⚠️⚠️ **NEVER PERSISTED.** `CpdScanRegistry::applyScanMarkers()` rebuilds them from the
registry on each load and leaves `updated` / `newCrit` false, so `getUpdatedCriticals()` cannot
list them and nothing reaches `tac_critical`; the CPDSCAN notes stay the single source of
truth. `forInfo` also keeps them out of pre-battle damage and Save Fleet, both of which refuse
`forInfo` classes generically. `markSystem()` is idempotent - a duplicate would render as
"(2 x) Scanned by Walkers".

⭐ **The call site is pinned by TWO orderings**, both inside `prepareForPlayer()`: before
`setPreTurnTasks()`, whose `beforeTurn()` sweep is what rebuilds `critData` out of `criticals`
(without it the marker rides the payload with no readable text), and before
`applyChameleonDisguise()`, so a marker can never land on a phantom sheet. `prepareForPlayer`
is also the right method rather than `onConstructed`: it runs only on the two READ paths, so
nothing in turn processing ever sees these objects. Gated on `$cpdAdaptationPresent` (D10).

⭐ **It exposed a real bug in `ShadingField::setSystemDataWindow`** - the one system in the
tree that never called its parent, so it had no `$critData` (and no ID, Arc or Power Used
either). ANY critical on a Torvalus Shading Field rendered as its raw phpclass. It now calls
the parent first.

⚠️ **Against a young or middleborn target neither line appears**, because Advanced Sensors zeroed
the shield before the bucket existed — see above. That is correct: nothing was discounted, because
there was nothing left to discount.

**The magnitude is one class constant.** `SCAN_POINTS_PER_HIT = 1`, and one point is one point of
shield — which is 1 off damage absorption and 5 off the d100 profile, because `getDefensiveHitChangeMod`
returns d20 units and the ×5 happens downstream. There is no explicit cap: the clamp at 0 is the cap.

⭐⭐ **COST: the whole feature hangs off ONE static boolean** (user's requirement, 2026-09-03 — *"this
is a rare weapon in the overall game, so as far as we can the processes to make it work should be
gated behind cheap checks"*). `TacGamedata::$cpdAdaptationPresent`, following the
`$chameleonPresent` precedent exactly. Set **only** by `CpdScanRegistry::record()` — i.e. once a
scan note has actually been replayed, not by the presence of a CPD on a hull, because an unfired
driver changes nothing:

| where | what an ordinary game pays |
|---|---|
| the four defensive-mod aggregators | one static-property read each. No call, no arguments. |
| `TacGamedata::onConstructed` | one property read (it tests the boolean, **not** `class_exists`). |
| `DBManager::getSystemDataForShips` | one assignment plus `class_exists('CpdScanRegistry', **false**)` — ⚠️ **autoloading OFF**, so the registry file is never read at all. If the class is not loaded its table is empty by definition, so there is nothing to reset. |
| client, both aggregators | one `window.gamedata && gamedata.cpdAdaptation` property read. The helper self-guards as a second line of defence, but it is never *reached*. |

⚠️ **Do not "simplify" any of these into an unconditional call.** `getHitChanceMod` /`getDamageMod`
run for every shot in every game of every faction. And do not restore autoloading on that
`class_exists` — that single character is what keeps the file out of every gamedata load in the
database. The test proves the gate is load-bearing by forcing it **false while the registry is still
full** and asserting the shields read un-adapted.

**Hull.** `Traveler` mounts three — front (270..90), left (180..360) and right (0..180) — and all
three of its `//STAGE 3` hit-chart rows are restored. ⚠️ That shifts every system id after the front
mount, which is the positional-id trap (trap 7): **a game in progress with a Traveler in it will
desync.** Same cost Stage 2 paid; `Firing::validateFireOrders` drops the stale orders rather than
crashing.

**How it was proven.** Two throwaway tests, kept in `c:\tmp\`: **165 server checks and 35 client
checks**, all green.

- **Server (`c:\tmp\cpd_stage3_test.php`, 165 checks):** the control sheet compared row by row with
  a non-vacuity assertion that the two rows really differ; over- and under-charge clamping;
  `getStartLoading` seeding 1 asserted three ways (at/above `getLoadingTime()`, below
  `getNormalLoad()`, and yielding the 1-turn profile); `$this->maxpulses` / `$this->useDie` tracking
  the charge, because `Weapon::fire` reads them; **800 rolls of `rollPulses` per charge level**
  proving the ceiling holds, the row's die is really used, and the two levels differ, plus the
  grouping bonus clamping at `maxpulses`; the mode swap in both directions with the ORDER's mode
  beating the weapon's own; `min/maxDamageArray` per mode; `unitHasShieldSystem` on plain, EM and
  unshielded targets; ⭐ **`beforeDamage` banking a point and creating NO damage entry, with a PULSE
  order on the same weapon still damaging as the non-vacuity control**; note round-trip including
  the varchar(40) truncation; ⭐ **the double-load trap demonstrated BOTH ways** — a second replay
  without a reset really does double the adaptation, and with the reset it does not; and ⭐ **the
  whole effect end to end through the REAL `BaseShip::getHitChanceMod` / `getDamageMod`** on a
  shielded hull, moving from −3/3 to −1/1 with 2 points and flooring at 0, with a same-team
  different-race bystander unaffected as the control. Plus the hull mounts, the three arcs, the
  restored chart rows, `stripForJson`, and a `ShipCompactor` audit that the blueprint keeps
  `damageTypeArray` / `min`+`maxDamageArray` / `firingModes`. ⭐ **And the D10 gate proved
  load-bearing rather than decorative** — forced FALSE while the registry is still full, the
  aggregators must read the shields un-adapted (−3, not −1), and re-arming must bring it back; plus
  a source audit that `class_exists` really has autoloading off, that both aggregator files carry
  two gates and exactly two `applyToShieldBucket` calls, and that a zero-point or teamless `record()`
  does not arm anything.
- **Client (`c:\tmp\cpd_client_test.js`, 35 checks):** `pulse.js` **evaluated**, not merely parsed,
  against stubbed globals — a parse would not catch a broken prototype chain
  (`howto_verify_react_bundle`) — with the instance checked to be a `ChromaticPulseDriver`, a
  `Pulse` and a `Weapon`, reachable as a `window` global (which is what `SystemFactory` needs) and
  with `VolleyLaser` / `EnergyPulsar` still building, since the file was appended to; the two mode
  constants compared against the PHP ones; and the adaptation mirror driven across the same nine
  cases the server test used, ending with ⭐ **the server's end-to-end number reproduced exactly**
  (bucket 3 − 2 points = 1). Plus the D10 gate: both call sites carry it, **no ungated call to the
  helper exists**, and the helper still self-guards for anything that calls it directly. And for
  the tooltip split: ⭐⭐ **the regrouping proven exact across 42 (shield, adaptation) combinations**
  — including negative and zero buckets and adaptation larger than the shield — asserting
  `−full + removed ≡ −adapted` every time, with a non-vacuity check that at least one case really
  produced two rows; plus the clamp trap asserted directly (3 points against a 1-point shield
  reports **1** removed, not 3).

Regression gate (re-run 2026-09-04 after the two fixes above): `checkShipData.php` PASS (0 new
findings, 235 accepted baseline), the throwaway suites now at **186 server checks + 35 client
checks**, all green, and the replay harness
**130 passed / 1 failed** - game 4325, the known pre-existing clean-tree failure, verified
byte-identical with the tree stashed.

⭐⭐ **CAPACITY-POOL SHIELDS — BUILT 2026-09-04, and this is the second half of the rule.**
The user's report: *"Thought Shields (and Thirdspace Shields and TrekShieldProjections) are able to
be scanned, but because their protection works in a different way I don't think the scanning has
any impact."* Correct, and the reason is exactly the bucket design above. Those four classes
(`ThirdspaceShield`, `ThoughtShield`, `TrekShieldProjection`, `TrekShieldProjectionKelly`) all
extend `Shield` and answer `getDefensiveType()` `"Shield"`, so a scanning hit banked a point off
them and they carried the marker — but their `getDefensiveHitChangeMod` / `getDefensiveDamageMod`
are **hard 0**. They do not reduce a shot's profile or its damage at all; they hold a **pool** and
absorb out of it in `doProtect()`. `applyToShieldBucket()` therefore reduced a number that was
already zero, and the whole thing was ceremony.

The ruling: **against an adapted fleet the pool's last N points are unspendable** —
`currentHealth − scannedAmount` is what the shield has to spend, per the user's wording.

- `CpdScanRegistry::applyToCapacity($capacity, $target, $shooter)` — the same `max(0, x − points)`
  clamp as the bucket twin, keyed the same `(shooter team, target faction)` way.
- Reached through **`Shield::getCapacityAgainstShooter()`**, one protected helper on the shared
  base class rather than four copies, carrying the `TacGamedata::$cpdAdaptationPresent` gate (D10).
  An ordinary `Shield` never calls it.
- Called from **both** hooks of each of the four classes: `doesProtectFromDamage()` (which ranks
  candidate protectors) and `doProtect()` (which actually absorbs). Eight call sites, two per class.

⭐ **`doesProtectFromDamage` GAINED A SIXTH PARAMETER, `$shooter = null`.** `doProtect` has always
been handed the shooter; the ranking hook never was, and without it an adapted-to-nothing pool
would still have won the "strongest protector" contest and then absorbed zero, silently shutting a
Bulkhead or a Diffuser out of a shot it should have taken. The parameter is **last and optional**,
so every existing caller and override stays valid; there are only **8 declarations and 4 call
sites** in the whole tree (`getSystemProtectingFromDamage` plus the three fighter-flight damage
estimators), all updated. ⚠️ A future override that copies the old five-argument signature is a
PHP declaration-compatibility fatal, not a silent miss.

⚠️ **COMPUTED PER SHOT, NEVER STORED.** It writes no `DamageEntry`, so the pool regenerates from
its **real** remaining health as usual, the ship sheet keeps showing the real number (the
`ScannedByWalkers` marker is what explains the gap), and the same shield reads at full strength
against every fleet that has not analysed the race. Two fleets with different adaptation shooting
it in the same turn each get their own figure.

⚠️ **A reinforced Thought Shield is the one system that feels the scan twice.** Its `defenceMod`
(the EM-Shield reinforcement layer) is a real, non-zero entry in the aggregated bucket, so the
points come off *that* through `applyToShieldBucket` **and** off the pool through
`applyToCapacity`. This is deliberate — they are two separate resources and the rule is "shields
are weaker" — but it is the only place a single point of adaptation buys two reductions, and it is
the thing to revisit first if the Mindriders read too soft in play.

**Out of scope, and why.** `TrekShieldFtr` (the Trek fighter's shield) and `DiffuserTendril` also
hold pools, but neither is a `DefensiveSystem` — `CpdScanRegistry::isShieldSystem()` rejects them,
so they are not scannable, carry no marker and are untouched. Trek's per-hit `output − armour` cap
is likewise untouched: the scan eats the **pool**, not the throughput.

**How it was proven.** `c:\tmp\cpd_capacity_test.php`, **56 checks green**: the registry
arithmetic including both clamps and the wrong-team / wrong-race / null controls; all four classes
driven through both hooks against an adapted and an unadapted shooter; ⭐ **the assertion that only
what was really absorbed reaches `$this->damage`** and that `getRemainingCapacity()` still reports
the true pool; the D10 gate forced false with the registry still full; ⭐ **the ranking flip proven
directly** — a 6-point pool with 6 points of adaptation loses `getSystemProtectingFromDamage` to a
4-point rival it beats against everyone else; non-regression for `Bulkhead`, a plain `Shield` and
both `applyToShieldBucket` clamps; and a source audit of the gate, the eight call sites and all
eight declarations. `cpd_stage3_test.php` re-run at **186 green** (one stale expectation fixed —
it still wanted the pre-ship marker wording `"Scanned by Walkers (-N shield effectiveness)"`; the
code says `"Scanned (-N effectiveness)"`, which is the right text now that the marker covers a
pool reduction as well as a modifier one). `checkShipData.php` PASS (0 new, 235 accepted) and the
replay harness **130 passed / 1 failed**, game 4325 again verified byte-identical with the tree
stashed.

**Left for the user:** a real `ChromaticPulseDriver.png` icon, and confirmation of `$priority`, the
health/power defaults and the point cost. Nothing else is open — D9 settled the Shading Field and
D10 settled the runtime cost.

### 3.5 Energy Draining Field / Variable EDF

`class EnergyDrainingField extends ShipSystem implements SpecialAbility, EdfSource`.

- `$radius` constructor arg (default from control sheet) plus a `$variable` flag.
- **Double power for extra radius** is the existing **boost** mechanism (`PowerManagementEntry`
  type 2, allocated in the Ship Power segment = Initial Orders = exactly where the rules put it).
  `boostable = true`, `maxBoostLevel = 1`, `getEdfRadius()` returns `$radius + ($boosted ? $bonus : 0)`.
  No new power concept.
- `canOffLine` — *"the player may deactivate the field"*, all-or-nothing, which is already what
  offlining a system means.
- Owns the §2.2 resolver.

**Criticals** — new classes + `$possibleCriticals`:
```php
// fixed-radius EDF
protected $possibleCriticals = array(21 => "EdfRadiusReduced");   // floor: radius 1
// variable EDF
protected $possibleCriticals = array(20 => "EdfBoostLost");       // then radius -1 each, floor 0
```
Both escalate on the `hasCritical()` **count**, so no per-crit bookkeeping.

**EDF Range enhancement** — **BUILT 2026-09-04 as the SHIP-level `EDF_RANGE`, not the per-system
`SYS_EDFR` this section assumed.** The user's call, and it was the right one: the Walkers now carry
their own `nonstandardEnhancementSet($this, 'WalkerShip')` set, and a ship-level entry can read the
hull's systems perfectly well (`CHAM_DISG` already does). Everything below is as built.

- **Human name "Extended Draining Field"**, enabled in the `WalkerShip` case and dropped again in
  `setEnhancementOptionsShip` on a hull that mounts no field.
- `price(level) = 300 * Σ(radius_i + level + 1)` over **every** EDF on the hull, i.e. 50 × the hexes
  added, summed. The rules' worked example (radius 5 → 6 = 6×6×50 = 1800) is `300 * (5+1)`.
  ⚠️ **Summed over the fields, not taken off the biggest.** It is a ship-level refit and the applier
  raises every field, so pricing off one would sell the second field's radius for nothing. With the
  usual single field it reduces to the rules' own formula exactly, and `priceStep` stays the
  constant `300 * fieldCount` that `enhancementOptions`' single step slot requires.
- **Limit 3.** Ours, not the rules' — the rules cap it nowhere and an offer tuple needs a number.
- ⭐⭐ **THE VARIABLE-FIELD RULE IS IMPLEMENTED BY SPENDING THE BOOST BONUS DOWN, and that is the
  one idea in this stage worth reusing.** "A vessel with a Variable EDF may only increase the radius
  of the normal-power field, and does not change the radius of the double-power field." The applier
  therefore does **two** things: `radius += n` **and**
  `boostRadiusBonus = max(0, boostRadiusBonus − n)`. The double-power radius is then unmoved by
  construction, with no second stored number, no clamp anywhere else, and no way for the two to
  drift; when the bonus reaches 0 the normal field has caught the boosted one up and boosting buys
  nothing, which is exactly what the rule describes. `getEdfBoostedRadius()` is the single place
  that arithmetic is written down.
- ⚠️ **Nothing may hard-code the `+3`.** `baseSystems.js`'s `initializationUpdate` did
  (`this.output + 3`, on a system whose `output` is always 0), so a refitted field would have shown
  a boosted radius it does not have. Fixed to `radius + boostRadiusBonus`.
- ⚠️ **Every number in the tooltip lives in its own `data` key and the prose carries none.** The
  refit is bought in the LOBBY, where there is no server round trip and `data` was baked into the
  blueprint long before the purchase, so `lobbyEnhancements.syncEdfFieldData` has to rewrite it —
  and rewriting one short numeric line is a mirror that survives, while re-deriving a number out of
  the middle of a four-sentence paragraph is one that rots. Hence `Field radius` **and** a new
  `Boosted radius` key, with `Special` reduced to "buys the boosted radius above".
- ⚠️ **`boostRadiusBonus` had to be added to `stripForJson`.** The client builds a system from the
  per-CLASS blueprint, which cannot know what one ship bought.

⚠️⚠️ **THE BUG THAT ACTUALLY SHIPPED, found in play (game 4336, 2026-09-05): the refit moved the
radius everywhere except the map that matters.** Three Travelers refitted to radius 3 / 4 / 5 all
still drained at 2. Every value was right — `$system->radius`, `getEdfRadius()`, `effectiveRadius`,
the tooltip, the enhancement box — and `TacGamedata::$edfHexes` was built from the BLUEPRINT radius
anyway, because `setEdfHexes()` was called beside `setBlockedHexes()` at the TOP of
`TacGamedata::onConstructed()` while `BaseShip::onConstructed()` — which is what applies
enhancements — runs in the per-ship loop **below** it. 29 hexes published where 91 were owed.

⭐ **The generalisation, and it is already written three lines above the loop for another
feature:** `markUnavailableSetMarkers()` sits below the loop *"because every ship is now fully
constructed - which the Chameleon gate requires, since onConstructed() is what applies
enhancements"*. **Anything that reads a number an enhancement can move belongs below that loop.**
`blockedHexes` genuinely does not (it reads only where ships ARE), which is exactly why the twin
looked safe beside it — the two maps are identical in shape, lifecycle and publication and differ
only in whether an enhancement can reach their input. `setEdfHexes()` now runs immediately after
`markUnavailableSetMarkers()`; nothing between the old and new call sites reads `$edfHexes` or
`$edfPresent` (every consumer is in firing / criticals / AoE, i.e. a later step of a later
request), so the call simply moved.

⚠️ **Stage 4 could not have caught this and neither could any unit test.** Without the refit the
blueprint radius and the effective radius are the same number, so every Stage 4 and Stage 5 check
passed on both sides of the bug. The guard is now **game 4336 in the replay corpus** — the snapshot
check is `stripForJson()`, which publishes `edfHexes`, so the baseline pins all 91 hexes and all
three post-refit radii. It is the first corpus game with an EDF at all.

⭐ **THE LIVE BOOST PREVIEW (user request 2026-09-05).** Double power is allocated in Initial
Orders and the server does not learn of it until the phase is SUBMITTED — so the map disc grew
only once it was too late to change your mind about paying for it. The fix publishes a SECOND
number, `boostedRadius` (from `getEdfBoostedRadius()`), beside `effectiveRadius`, and
`PhaseStrategy.getEdfRadiusForShip` **picks between the two** on the strength of the local,
uncommitted `system.power` entry that `shipManager.power.getBoost` reads. Unboosting picks the
first one again, so the disc shrinks. `showEdfField` already caches on radius + anchor hex, so a
changed radius redraws and an unchanged one is free; the seam that fires it is
`onSystemDataChanged`, which `power.clickPlus` / `clickMinus` both raise — **gated on
`system.name === 'EnergyDrainingField'`**, because that handler is one of the busiest in the file
and `syncAllEdfFields` walks every icon on the board.

⚠️⚠️ **IT PICKS, IT NEVER ADDS, and that is the whole reason for the second published number.**
The obvious client-side version — `radius + boostRadiusBonus` — is wrong in a case the client
cannot see: a field that has taken an `EdfBoostLost` critical can still have power allocated to
it (`hasMaxBoost()` does not test criticals and the crit is not readable from there), so the map
would promise hexes the server will never honour. `getEdfBoostedRadius()` already answers "the
boost is gone" by returning the unboosted radius, so the two numbers arrive EQUAL and clicking +
correctly moves nothing. Same rule as the note on `showEdfField`: **the radius is published,
never mirrored.** The SCS icon number now reads `boostedRadius` for the same reason, keeping the
blueprint sum only as the lobby fallback (where no critical has been rolled yet).

⭐ **DEACTIVATION previews the same way (user, 2026-09-05).** A field switched off projects
nothing, so its disc goes the moment `shipManager.power.isOffline` answers true and comes back if
the player changes their mind in the same phase. Two things about it are not obvious:

- **Offline is tested FIRST and wins outright.** Offlining does *not* clear a boost allocation, so
  a type-1 and a type-2 entry can sit on one system at once — and the server resolves that pair
  the same way, `getEdfRadius()` returning 0 on `!isEdfActive()` before it looks at anything else.
- ⚠️ **`onSystemDataChanged` arrives in TWO SHAPES and testing only the first misses half the
  feature.** The per-system power clicks send `{ship, system}`; `offlineAll` / `onlineAll` — the
  "all systems of this name" buttons in `SystemInfoButtons` and `SystemPowerSettings`, which an
  EDF is as eligible for as anything else — send `{ship}` alone. The gate is therefore
  `system ? system.name === 'EnergyDrainingField' : PhaseStrategy.shipCarriesEdf(ship)`, which
  stays exact instead of widening to every systemless event.

**What is deliberately NOT previewed: the to-hit penalty.** `weaponManager.getEdfPenaltyHexes`
reads `gamedata.edfHexes`, which is server-built and cannot know about an uncommitted change —
mirroring it would mean rebuilding the whole hex map client-side. It costs nothing here because
**a field never penalises its own fleet**: the only shots your own field's hexes affect belong to
the enemy, whose client cannot see your uncommitted allocation either way.

⚠️ **A Stage 4 BUG found while building this: the boosted radius was FREE.** The class declared
`boostEfficiency = 0` under a comment saying "double power" — but `boostEfficiency` is the EXTRA
power one boost level costs (`power.js countBoostReqPower`/`countBoostPowerUsed` multiply by it), so
0 means the boost costs nothing at all. It is now set from `$powerReq` in the constructor, which is
the only place it can be written because the requirement is a ctor argument. **This changes the
Traveler's power economy**: a boosted field now costs 32 rather than 16.

---

**As built — Stage 4a, the FIELD (2026-09-04). The DRAIN (§2.2) and the map OVERLAY are not
built yet; see "What is left" at the end of this block.**

Everything below is the field itself: the system, the published hex map, and the targeting
penalty on both sides. That is three of the stage's four exit criteria — *field map published*,
*targeting penalty matches server↔client to the point*, *own-fleet immunity and multi-field
non-stacking hold*. *Drawn*, *drain lands as crits* and *the Enormous clamp* belong to the
unbuilt half.

⚠️⚠️ **THE STATS ARE PLACEHOLDERS AND ARE MARKED SO IN-FILE.** D4 says the numbers arrive as a
Walker hull carrying the system; none has landed for the EDF. Six class constants at the top of
`EnergyDrainingField` carry every game number — `DEFAULT_RADIUS 2`, `BOOST_RADIUS_BONUS 1`,
`MIN_RADIUS_FIXED 1`, `MIN_RADIUS_VARIABLE 0`, `DEFAULT_HEALTH 12`, `DEFAULT_POWER 8` — and
nothing outside that block hard-codes one, so a re-stat is an edit to those six lines. Also
outstanding: a real `EnergyDrainingField.png` (it borrows `SparkField.png`, one line, marked), a
point cost, and the drain magnitudes §2.2 needs.

⭐ **IT LIVES IN `baseSystems.php` / `baseSystems.js`, and that is the SAME load-order rule §3.4
records for the CPD, applied in the other direction.** The EDF is a `ShipSystem`, not a `Weapon`,
so its client twin belongs in `client/model/system/baseSystems.js` — the FIRST model file both
`game.php` and `gamelobby.php` load, so nothing can be built before its prototype exists. A new
pair of files would have meant two `<script>` tags, two bundle rebuilds and a fresh chance to get
the ordering wrong. **Keep each server/client pair in matching files.**

**The system.** `EnergyDrainingField extends ShipSystem implements SpecialAbility, EdfSource`,
constructed `($armour, $maxhealth = 0, $powerReq = 0, $radius = null, $variable = false)` — 0/null
take the class defaults, the same convention the Walker weapons use so a hull can mount "a basic
version" without inventing numbers.

- **`interface EdfSource`** (`getEdfRadius($turn)` / `isEdfActive($turn)`) is the *only* thing
  `setEdfHexes()` consumes, so the Stage 6 mine terrain and the Stage 7 net plug in by
  implementing it and changing nothing else.
- **Variable fields are the existing BOOST mechanism** — `PowerManagementEntry` type 2, allocated
  in the Ship Power segment, which is exactly where the rules put the choice. No new power
  concept. Boost is per TURN, so a field boosted on turn 3 reads normal on turn 4.
- **Deactivation is `canOffLine`**, because all-or-nothing is already what offlining means.
- **Criticals escalate on the `hasCritical()` COUNT**, no per-crit bookkeeping: `EdfRadiusReduced`
  costs a hex each on a fixed field; on a variable field the FIRST `EdfBoostLost` costs the boost
  and every further one a hex. One crit class per field type, both ordinary persisted criticals
  (contrast `ScannedByWalkers`, which is rebuilt every load and never saved).
- ⭐ **ONE PHPCLASS, TWO CRIT TABLES.** The variable field swaps `$possibleCriticals` in the
  constructor rather than being a second class: `SystemFactory` builds the client twin with
  `new window[name]`, so a second phpclass would need a second client class and a second
  blueprint entry for one changed array. Safe on the server because `$possibleCriticals` is
  per-instance; on the CLIENT the same-phpclass reference sharing is real (trap 6), so the
  ctor clones `data` and the server republishes `data`/`radius`/`variable` per instance.
- ⚠️ **`startArc`/`endArc` are declared 0..360 on purpose.** A system whose arcs are both 0 has
  its SECTION's arc stamped on by `addSystem()` (`arch_addsystem_section_arc_trap`), so an
  aft-mounted EDF would advertise itself as aft-facing. The field is omnidirectional.
- ⚠️ **The radius floor is a floor on the CRITICAL REDUCTION, not on the blueprint.** A plain
  `max(1, …)` silently promotes a deliberately-designed radius-0 fixed field into a 7-hex one.
  It is `max(min($this->radius, MIN_RADIUS_FIXED), $reduced)`. The test caught this.

**The map.** `TacGamedata::$edfHexes`, `{ "q,r": { teams: { <teamId>: 1 } } }`, built by
`setEdfHexes()` in `onConstructed()` **immediately after `setBlockedHexes()`** and published
whole in `stripForJson()`. Same shape, same lifecycle, same call site, not per-viewer — because
`blockedHexes` has already proved that map-once/publish-once stays in sync with the client, which
is exactly what a penalty mirrored "to the point" needs.

- Keying by HEX makes *"overlapping hexes are only counted once"* and *"additional fields do not
  stack"* **structural** rather than special cases: two fields, one entry.
- `teams` being a SET turns *"the rest of the fleet of the ship deploying the EDF is immune"*
  into one array lookup instead of a per-shot sweep over every field in the game.
- ⚠️ **Three exclusions, each a bug if dropped:** destroyed units, units still in HYPERSPACE
  (`isReinforcement()` — worse here than in `setBlockedHexes` because this map goes to *every*
  viewer and would announce an arrival box a turn early), and units with no position yet.
- ⚠️ **NULL, never `array()`,** when nothing projects (trap 9).
- ⚠️ **There is ONE mid-request writer, added at Stage 6: `registerEdfField()`.** An Energy
  Draining Mine's probe lands during Firing, after this map was built, and has to drain at the
  Critical Hit step of its own turn — so it folds a single hex-disc in. It is **additive on
  purpose**: a rebuild would also re-test every existing field against post-firing state and drop
  the field of a Walker shot down in that same step. See §3.6.

⭐ **THE GATE NEEDS NO `DBManager` RESET, and that is a deliberate difference from
`CpdScanRegistry`.** `TacGamedata::$edfPresent` is cleared at the top of `setEdfHexes()`, which
rebuilds the whole map from scratch on every load — so the double gamedata load in one request
(trap 1) cannot double-count *by construction*. **Do not add an accumulating cache here without
also adding the reset**, which is the trap the Gravitic Augmenter investigation paid for.

**The penalty.** `-1` to hit per hex of somebody else's field the shot crosses, ×2 for Plasma and
Antimatter of a young or middleborn race. In d20 units like every other term, so the single ×5 at
the bottom of `calculateHitBase` converts it. `TacGamedata::getEdfPenaltyHexes()` is the server
authority; `weaponManager.getEdfPenaltyHexes()` / `getEdfPenalty()` mirror it, and the doubling
rule is duplicated in both — **change them together**. It uses `$launchPos`, so a ballistic is
drained along the line it actually flew.

⚠️ **`sPosLaunch`/`sPosTarget` are only filled in for BALLISTICS in `calculateHitChange`** —
direct fire leaves both null, and passing them straight through would have made the penalty
ballistic-only. Both ends are resolved locally instead, matching the server's `getFiringHex()`
(which answers the shooter's own hex for a non-ballistic).

⚠️ **It is gated on `TacGamedata::$edfPresent`, not on the map**, for the D10 reason: this runs
for every shot in every game of every faction. Client side, on `gamedata.edfHexes` being non-null.

⚠️ **`gamedata.js` had to be taught to copy `edfHexes` by name** — the same one-line requirement
that left the whole CPD client half dead for a day (`arch_gamedata_named_key_copy`). Declared at
the top of the singleton and assigned **unconditionally** in `parseServerData`, normalised to
null so a replay stepping back before the field clears it.

⭐⭐ **PHP's `round()` IS NOT JS's `Math.round()`, IN TWO WAYS, AND BOTH BIT.** The client needs
the identical hex line, so `mathlib.hexLine()` is a port of `HexZone::line()` — and a port of
`round()` with it, in `phpRound()`:

1. **Half away from zero.** PHP `round(-2.5) === -3`, JS `Math.round(-2.5) === -2`. Cube
   coordinates go negative all over a Fiery Void map. **843 of 4,000 corpus lines** disagree
   without this.
2. **PRE-ROUNDING.** PHP first rounds to `14 - floor(log10|v|)` decimal places, so a value
   floating-point error left at `-20.49999999999999644` is treated as `-20.5` and lands on `-21`.
   Fixing only (1) still left **13 of 4,000** lines wrong — the hardest kind of mismatch to
   notice, because it only appears when a line passes almost exactly through a hex corner.

Both halves have their own non-vacuity control in the test. ⚠️ Written against **PHP 8.2**; if FV
moves to a version that changes `round()`'s edge cases, re-run the differential test before
assuming the port still matches. **Any future JS port of a PHP geometry routine needs this.**

⚠️ It deliberately does NOT reuse `mathlib.isLoSBlocked`'s geometry, which asks a different
question (does the segment *clip* a hex, tested in pixels) and would count hexes the server's
line never enters. Line of sight and field crossing are two different rules.

**Hull.** The `Traveler` mounts one fixed field aft and its `//STAGE 4` chart row (Aft 9) is
restored. ⚠️ Positional-id trap again (trap 7): the new system is id 14 and everything after it
shifts, so **a game in progress with a Traveler in it will desync** — the same cost Stages 2 and
3 paid.

**How it was proven.** Two throwaway suites, kept in `c:\tmp\`: **71 server checks and 32 client
checks**, all green.

- **Server (`c:\tmp\edf_stage4_test.php`):** the system's shape, defaults and all-round arc; both
  crit ladders including each floor and the blueprint-radius-0 case that caught the floor bug;
  boost on and off and its per-turn scope; deactivation and the turn after; the hex map's disc
  size at radius 1 and radius 0; all three exclusions; ⭐ **overlap collapsing with both teams
  recorded once each, and two fields on ONE hull not stacking the map**; the penalty count,
  own-fleet immunity, the team-less shooter, and ⭐ **a hex covered by BOTH sides still being
  charged to each of them** (the exemption is "only my team covers it", not "my team covers
  it"); plus a source audit of the gate, the `onConstructed` ordering, the null-not-array
  publication, the `parseServerData` named-key line, the client mirror's three call sites, the
  `phpRound` pre-rounding, and the restored hit-chart row.
- **Client (`c:\tmp\edf_client_test.js`):** `mathlib.js` **evaluated**, not merely parsed, with
  `hexLine` asserted to be *on the object* (a helper that lands outside an object literal still
  parses) and `isLoSBlocked` asserted to have survived the insertion; then ⭐⭐ **the
  differential run: 4,000 lines / 128,745 hexes generated by the real `HexZone::line`, zero
  mismatches**, with non-vacuity assertions that the corpus really reached negative coordinates
  and spanned 20+ line lengths; **both rounding controls**; 500 penalty cases against the real
  `getEdfPenaltyHexes`, zero mismatches, with a check that they were not all trivially zero; the
  own-fleet and empty-map gates; all six doubling cases; and the client class evaluated as a
  `window` global with the trap-6 data clone and the compactor's `variable: undefined` case.

Regression gate: `checkShipData.php` **PASS** (0 new findings, 235 accepted baseline — the one new
warning it raised was the missing placeholder icon, which is why the class borrows an existing
one), the replay harness **130 passed / 1 failed** (game 4325, the known pre-existing clean-tree
failure, verified byte-identical with the tree stashed), autoload regenerated, statics
regenerated and the EDF confirmed present in `Walkers of Sigma-957.json`.

**What is left in Stage 4 (§2.2 and the overlay):**
1. **The drain resolver** — the six `EdfExposure` criticals, the `GraviticMineHandler`-shaped
   once-per-turn sweep owned by the EDF system, the four choke-point readers (§1.7), the
   `EdfExposed` escalation counter, the Enormous clamp and the `critRollMod` fighter-dropout
   branch. **Blocked on the control sheet:** the drain magnitudes, the dice and the per-turn
   escalation are not in this plan and D4 says they arrive with the hull.
2. **The renderer overlay** — a translucent field drawn from `gamedata.edfHexes`, reusing
   `HexagonSprite`. ⚠️ `requestRender()` (trap 10) or it silently will not appear.

---

**As built — Stage 4b, the DRAIN and the OVERLAY (2026-09-04). Stage 4 is now complete.**

The user supplied the field's own control-sheet numbers (radius 5, boost +3, 40 boxes, 16 power)
and then, the same day, the **full EDF rules text** - so nothing in Stage 4 is inferred any more.
Six constants at the top of `EdfExposure` still carry every game number, and nothing outside that
block hard-codes one; they are now quotes rather than guesses.

⭐⭐ **THE RESOLVER IS CALLED FROM THE TOP OF `Criticals::setCriticals`, NOT FROM
`criticalPhaseEffects()` — and the plan's own reasoning for the latter turned out to be wrong.**
§2.2 wanted the EDF system to own a sweep in `criticalPhaseEffects` (pass 2) and to feed the
fighter dropout by setting `$fighter->critRollMod`, which `Fighter::testCritical` already sums into
its roll. Two things kill that:

- **`testCritical` only runs on a craft that was DAMAGED this turn** — criticals.php pass 1 gates
  every call on `isDamagedOnTurn()`. A pristine flight parked in a field would never roll at all,
  so a `critRollMod` nudge would reach nothing in exactly the case the rule is about.
- `criticalPhaseEffects` is pass **2**, i.e. after the dropout rolls it was meant to influence.

So `EdfExposure` is a handler class (`source/server/handlers/EdfExposure.php`) called before pass 1,
in the shape `HkJamming` already uses from the tail of the same method, and it **rolls its own
dropout** — mirroring `testCritical`'s comparison (roll vs remaining health, plus the flight's
dropout bonus and any `critRollMod`) with `(consecutive turns + 1)` dice instead of one, which is
the plan's "2d10, +1d10 per successive turn". Running before pass 1 keeps the two rolls from
interleaving on one craft.

⭐⭐ **REPLAY DETERMINISM WITHOUT A ROLL NOTE, and this is the part worth reusing.** `setCriticals`
re-runs on replay and `Dice::d` is not deterministic, which is why `HkJamming` has to persist its
d20 in an `IndividualNote`. This resolver needs no note: **the RESULT is the record.** Every roll it
makes goes straight into a persisted critical's `param`, and the `EdfExposed` marker says "this unit
has already been resolved for turn N". A reload finds the marker and skips.

⚠️ The `$resolvedTurn` static only stops the sweep running twice inside ONE request. Idempotency
across requests is the marker, and reading it needs a **direct scan of `$system->criticals`, not
`hasCritical()`** — `EdfExposed` is `oneturn`, so `hasCritical` reports it on turn N+1 and asking
"is there one for THIS turn" always answers no. Get that wrong and every reload re-rolls the whole
drain.

⭐ **`oneturn` IS the "starting next turn" mechanism, and it also does the cleanup.**
`hasCritical`/`sumCriticalParam` report a `oneturn` critical only when `crit->turn + 1` equals the
turn being asked about, so a drain rolled in turn N's Critical Hit step is invisible on turn N,
lands on turn N+1, and expires by itself. Nothing sweeps them up.

⚠️⚠️ **NONE OF THE SIX CRITS MAY CARRY AN `$outputMod`, however tempting it looks.** Routing the
thrust and power drains through `outputMod` would have been free on both sides (the server's
`getOutput()` and the client's `shipManager.systems.getOutput` both already read it). But
`ShipSystem::effectCriticals()` sums `outputMod` across **every** critical with **no turn filter at
all** — it is built for permanent `OutputReduced*` crits and is called once from `onConstructed` —
so a one-turn crit with an `outputMod` would apply from the turn it was rolled and then for ever.
`Engine::getOutput()` and `Reactor::getOutput()` read `sumCriticalParam()` instead, which IS
turn-filtered, and publish the turn's figure as a separate `edfDrain` field for the client.

⚠️ **The four param crits are SUMMED, never counted.** One crit is a whole Nd10 roll, the same
convention `DamageReductionReduced` uses. `hasCritical()` would read every drain as 1.

**The four choke-points** (§1.7), all as the plan predicted:

| effect | where | note |
|---|---|---|
| thrust | `Engine::getOutput()` | flights have no Engine — see below |
| energy | `Reactor::getOutput()` | |
| initiative | `BaseShip::getCommonIniModifiers` | ⚠️ ×5 (plan trap 5) and the rules' −20 tabletop cap is applied to the EDF's own accumulated total, **not** to `$mod` — everything else in that method is a separate effect and must not be squeezed by it |
| total EW | `EW::getScannerOutput` | beside the existing `RestrictedEW` / `SensorLoss` terms |

⚠️⚠️ **THE READERS ARE GATED ON `!empty($this->criticals)`, NOT ON `TacGamedata::$edfPresent`** —
and that is not an oversight. `$edfPresent` means "a field is on the board **right now**". A Walker
destroyed on turn 5 leaves its turn-5 drain in effect through turn 6, when the gate is already
false. The criticals array is the correct and equally cheap test: no criticals, no drain, and an
undamaged system's array is empty anyway. The same reasoning ungates
`Firing::withdrawGroundedFighterFireOrders`.

**A FLIGHT is a different unit throughout, and every branch is deliberate:**
- **no Engine**, so the thrust crit rides the **sample fighter** and comes off `freethrust` via a
  new `FighterFlight::getEffectiveFreeThrust()` — which is also what `AutomatedMovement` now spends
  from, so an HK cannot plot a pursuit on thrust it does not have;
- **no Reactor and no EW-output system**, so those two drains are not rolled at all;
- the initiative crit rides the sample fighter, which is already where `getCommonIniModifiers`
  reads a flight's initiative criticals;
- `EdfFighterGrounded` withdraws its fire orders next turn.

⚠️ **`getEffectiveFreeThrust` had to resolve a null turn itself**, and the test is what found it:
`sumCriticalParam($type, $turn = false)` defaults to the current turn on **`=== false`**, not on
null. A null passed straight through makes every turn comparison fail and the drain silently reads
0 — while the same getter called with an explicit turn is correct, which is the only way the two
could disagree. **Check this on every `sumCriticalParam` caller with a nullable turn.**

⚠️ **The grounded-fighter check has to be on the ADVANCE path.** It lives in
`Firing::withdrawGroundedFireOrders`, called from both `prepareFiring` and the PreFiring path, in
the same withdraw-and-detach shape as the surrender sweep. **Not `validateFireOrders`**: a POST-side
ship is reconstructed with no criticals at all
([arch_post_side_ship_reconstruction](source/server/model/ships/ShipClasses.php)), so the same test
there would read "no crit" for every flight in the game and silently do nothing, for ever.

**The overlay.** A translucent disc on the projecting unit's own icon —
`ShipIcon.showEdfField(radius)` built from the existing `buildHexRegion` / `buildRegionOverlay` /
`addGridLockedOverlay` trio, driven by `PhaseStrategy.syncAllEdfFields()` from the same two call
sites as `syncAllDeclaredAreas` (a poll rebuilds every ship object, so a standing overlay has to be
re-read).

- ⚠️ **NOT drawn from `gamedata.edfHexes`.** That map is collapsed by hex and by team — it is built
  for the penalty arithmetic and cannot say which source a hex came from — so drawing from it would
  fuse two overlapping fields into one shapeless blob with no border between them. Each source
  draws its own disc and the fills compound where they overlap.
- ⚠️ **The radius is PUBLISHED, not mirrored.** `effectiveRadius` comes off `stripForJson` after
  criticals and boost; reimplementing the crit ladder client-side would be a second copy of a rule
  that changes. It reaches the client through the LIVE payload only — the static blueprint is
  `json_encode($ship)` and never calls `stripForJson`, which is correct, because the effective
  radius is per-turn state and a blueprint is not.
- ⚠️ `requestRender()` (trap 10), and **only when something changed** — a redraw of nothing must
  not wake the idle-gated loop on every poll of every game. `gamedata.edfHexes` being null is the
  cheap gate that skips the whole sweep in an ordinary game.

**How it was proven.** `c:\tmp\edf_drain_test.php`, **105 checks green**: all six crit classes
including ⭐ **an assertion that not one of them carries an `outputMod`**, and the `oneturn`
timing proved in all three turns (invisible, felt, gone); the resolver landing each drain on the
right system; ⭐ **idempotency demonstrated by re-resolving the same turn and asserting nothing
changed**; own-fleet immunity **and** the case that distinguishes it (a hex both sides cover still
drains an own-fleet ship); the three exclusions; the escalation counter driven over four turns with
⭐ **the Enormous clamp proved load-bearing** — 60 sampled runs showing an ordinary unit routinely
beats one die's ceiling where an Enormous one never does; all four readers including the ×5, the
−100 floor and the clamp at 0; the publication of `edfDrain` present-when-drained and absent-when-
not; the whole flight branch, with ⭐ **dropouts proved to happen on an UNDAMAGED flight** (the
whole reason the resolver rolls its own) and a tough-craft control proving the roll is a roll; and
a source audit of the call site's position relative to pass 1, the reset being inside the gate, the
two firing paths, the two ungated readers, and the client mirrors. Plus, for the corrections
below: ⭐ **300 sampled first-turn exposures proving EW never exceeds 6 while the other three
reach 10**; the blackout cascade reaching a powered system AND a powerReq-0 Weapon but NOT the
C&C, landing on turn+1 and not on the turn it was rolled, with a hardy-reactor control; and
`isHexInEdfField` asserted TEAM-BLIND.

Regression gate: `checkShipData.php` **PASS** (0 new findings, 235 accepted), the replay harness
**130 passed / 1 failed** (game 4325, the known clean-tree failure), autoload and statics
regenerated, and every changed client file `node --check` clean.

**THE REAL RULES ARRIVED THE SAME DAY (user, 2026-09-04) and corrected three things.** The build
above had been made against the plan's inferences; the rules text settled all of it. What changed:

⚠️⚠️ **THE EW DRAIN IS d6, NOT d10.** *"The ship's total EW is reduced by **1d6** for the next turn,
increased by a further 1d6 for every additional turn"* — while Free Thrust, Energy and Initiative
are all 1d10 escalating the same way. The first build used one die for all four and was wrong about
EW alone. `EdfExposure::EW_DIE` now sits beside `DIE`, and `roll()` takes the die **explicitly with
no safe default in practice** so the one exception cannot be forgotten again. The test samples 300
first-turn exposures and asserts the observed ranges — a single roll cannot tell 1d6 from 1d10.

✅ **Everything else the plan inferred was right:** d10 for the other three, dice = consecutive
turns, `ENORMOUS_DICE = 1` (*"the modifiers are limited to the first die (1d10 or 1d6) and do not
increase with every additional round"* — note it covers **both** dice), dropout at 2d10 +1d10 per
turn, the −100 FV initiative cap, and the own-fleet exemption. The `GROUND_FIGHTERS` flag is gone:
*"Even if the fighter/shuttle does not drop out, it will not be able to shoot the next turn, and
loses initiative and free thrust in the same manner as a ship"* is a rule, not an assumption, so it
is unconditional. That sentence also confirms a flight takes **initiative and thrust only** — no
energy, no EW — which is what the data model was already doing for want of a Reactor or an EW system.

⭐⭐ **THREE EFFECTS THE FIRST BUILD DID NOT HAVE, all now in:**

1. **The reactor blackout.** *"If the ship's reactor is completely drained of power, this will force
   the deactivation of everything on the ship that requires energy. This includes any weapon or
   system with a power diamond, even if that icon contains a zero (such as missile racks)."*
   ⭐ **That cascade already existed and did not need writing.** `Reactor::addCritical` propagates a
   `ForcedOfflineOneTurn` to every system with `powerReq > 0` **or** `instanceof Weapon` — which is
   precisely "a power diamond, even if that icon contains a zero", because a missile rack is a
   Weapon with `powerReq` 0. It is the same mechanism a reactor knockout already uses and it handles
   the StarBase per-section case for free. `EdfExposure::applyPowerBlackout()` is one call into it.
   ⚠️ The trigger is measured against the reactor's output **next** turn — raw output minus the
   whole `EdfPowerDrain` that will be in effect then, including the crit just added. `getOutput()`
   cannot answer that: it reads the CURRENT turn, where a `oneturn` crit is invisible by design.
   The timing needs nothing extra either — `ForcedOfflineOneTurn` is itself `oneturn`, so the
   blackout lands on exactly the turn the drain does.

2. **Flash weapons score no collateral inside a field.** *"If they strike a unit located within the
   field, they will only affect the target (collateral damage will not be scored). The first unit
   will still take full damage."* One early return in `Weapon::doCollateralDamage`.

3. **Proximity weapons lose their radius inside a field.** *"…only detonate in that hex, losing any
   explosion radius they might normally have. They will still cause their full damage within the
   target hex."* In `AoE::fire` that is exactly `$ships2 = $ships1` — and it is measured at the hex
   the shot **actually landed on, after deviation**, not at the declared one.

⚠️ **Both of those use `TacGamedata::isHexInEdfField()`, which is deliberately TEAM-BLIND** — unlike
`getEdfPenaltyHexes()`. The own-fleet exemption is about who the field is aimed *at*; these two are
the field dampening an explosion, which is a property of the hex, and the rules say of each that it
*"applies to advanced race weapons as well"*, i.e. no exemptions.

---

**As built — Stage 4c, PLAY-TEST FIXES (2026-09-04, game 4334).** Four reports off the first real
game with a Traveler in it. Three were one bug wearing three hats.

⚠️⚠️ **"THE REACTOR IS COMPLETELY DRAINED" IS NOT `output - drain <= 0`, AND THIS IS A FACT ABOUT
EVERY FV BLUEPRINT, NOT ABOUT THE EDF.** A Fiery Void `Reactor`'s constructor `output` is the
ship's **SPARE** power with every system powered up — not the reactor's generation. Virtually every
hull in the database is therefore built `new Reactor($armour, $maxhealth, 0, **0**)`: a Bin'Tak, a
Thoughtforce and an Abbai Bimith all read 0. Measuring the drain against that figure meant **one
point of drain blacked out any ship the field touched**, and took its owner's chance to trade
systems for power away with it. `EdfExposure::applyPowerBlackout` now measures against
`getMaxAvailablePower()` — the ceiling the owner could reach by switching off everything they are
allowed to — which is the user's ruling: *"the reactor can sustain as much negative power as the
total amount of power it provides to offline-able systems."* Anything less is an **ordinary
deficit**, handled where FV already handles one: the player powers systems down in Initial Orders
and `gamedata.doCommit` → `getShipsNegativePower` blocks the commit until they do.

`getMaxAvailablePower()` is the server-side mirror of the client's
`shipManager.power.getRemainingFreeablePower` (power.js) — **keep the two in step** — and it has
three special cases, all of which the user named in the report:

- ⚠️ **MAG-GRAV REACTORS (`$fixedPower`: Ipsha, and the Vorlon technical mount) ARE THE OTHER WAY
  ROUND.** Their `output` is the reactor's TOTAL generation, and every powered system is subtracted
  from it — which is exactly what *"provides fixed total power, regardless of destroyed systems"*
  means. So their ceiling is that total **minus only the draws that cannot be shed**; adding the
  freeable ones back would count the same power twice and make an Ipsha hull unblackout-able.
- ⚠️ **POWER CAPACITORS AND PLASMA BATTERIES (Vorlon, Pak'ma'ra) hold real spendable power that the
  SERVER OBJECT CANNOT SEE.** `PowerCapacitor.initializationUpdate` rewrites `powerReq` to
  **negative `powerCurr`** to inject the charge into the reactor display — a client-only rewrite.
  The stored charge is added here by class instead. Without it a Vorlon hull (whose Mag-Grav
  technical reactor generates **0** and runs entirely off its capacitor) blacks out with a full
  capacitor.
- ⚠️ **`powerLocked` IS `linkedOrbital !== null && !stowed`, NEVER `!stowed` ALONE.**
  `Weapon::$stowed` is **declared on `Weapon` and defaults to false**, so a bare `!stowed` test
  power-locks the entire armament of every hull in the game. The pairing is what makes a deployed
  Kirishiac orbital's beam unsheddable; a standard mount has `linkedOrbital === null`, which is
  what keeps the whole test free for everyone else.
- A StarBase's sweep is restricted to the drained reactor's own section, because
  `Reactor::addCritical` blacks out only that section.

⭐ **THE CLIENT NEVER SAW THE DRAIN AT ALL.** `shipManager.power.getReactorPower` reads
`reactor.output + reactor.outputMod` **directly** — it does not go through
`shipManager.systems.getOutput`, which is the only place the published `edfDrain` was being applied.
So a drained ship showed a perfectly balanced reactor and its owner was never asked to power
anything down. The subtraction is now in `getReactorPower` too, in **both** its branches (the
StarBase multi-reactor one and the ordinary one) and deliberately **NOT clamped at 0**: unlike an
output figure, a balance is meant to go negative, and that negative **is** the deficit.
**Generalises: `getReactorPower` is the ship's power BALANCE and `getOutput` is one system's
output — a rule that changes what a reactor supplies has to be applied in both.**

⭐ **THE THOUGHT SHIELD "REGENERATING ONLY THE SCANNED AMOUNT" WAS THIS SAME BUG, NOT A CPD BUG.**
Reported as a Chromatic Pulse Driver interaction; it is not. `ThoughtShield::criticalPhaseEffects`
**returns early when the Thought Shield Generator is offline**, and the blackout cascade had
forced it off. The reason it looked like a scan bug is arithmetic: `doProtect` absorbs
`min(capacityAfterScan, damage)`, so an **overwhelmed scanned pool always ends the turn holding
exactly the scan** (25 − 23 = 2 points, and the scan was 2). Nothing in the regeneration path is
scan-aware and nothing should be — the scan is computed per shot and never stored (§3.4). Proven
both ways: with the generator forced off, no regeneration entry at all; with it online, one entry
of −23 restoring the full 25, while a scanning fleet still only gets through 23 of it.

⭐ **A LOG-ONLY FIRE ORDER MUST CARRY A NON-ZERO `rolled` OR THE PRINTED COMBAT LOG SILENTLY DROPS
IT.** The EDF drain has always written its `pubnotes` row and it has never been visible.
`weaponManager.getAllFireOrdersForLogPrint` filters every order through `isResolvedFireOrder()`,
which is nothing but **`Number(fire.rolled) > 0`** — so the row was discarded before
`combatLog.logFireOrders` ever saw it, pubnotes and all. `HkJamming` passes its real d20 there and
so has always printed; `EdfExposure` spends all its rolls on criticals, so it now passes a bare `1`
as the "this order resolved" marker. `shots`/`shotshit` still stay 0 (`submitDamages` links unknown
damage by `shotshit > 0`). `"EdfExposure"` also joins `weaponManager.doShortLogText`'s list, or the
sentence prints behind *"firing 1x Ramming Attack at &lt;itself&gt;. 0/0 shots hit"*.
⚠️ Rows already in `tac_fireorder` keep their stored `rolled` 0 — turns played before this fix stay
blank in the log. **Check this on any future technical fire order.**

⭐ **THE GROUNDED FLIGHT NOW SAYS SO, IN PURPLE.** *"Even if the fighter/shuttle does not drop out,
it will not be able to shoot the next turn"* is enforced silently on the advance path
(`Firing::withdrawGroundedFighterFireOrders`), so a player declared a full turn's shooting and only
found out when the orders vanished. Two mirrors, both reading the `EdfFighterGrounded` `oneturn`
crit off the flight's **sample fighter** with the same `crit.turn + 1 === gamedata.turn` test
`ShipTooltip.js` already uses for `Uncontrolled`: a map-tooltip line and a ship-window status
banner (`getStatusBanners`, which the flight variant renders). The colour is `#d250ff`,
`EWIconContainer`'s existing `COLOR_JAM`, so the two "your unit is suppressed this turn" states
read as one family and are distinct from red damage.

**How it was proven.** Four throwaway suites in `c:\tmp\`, 62 checks, all green.
`edf_blackout_test.php` (26) drives the real `applyPowerBlackout` over the reported hull at drains
1 / 18 / 48 / 49 / 200 — with ⭐ **the pre-fix rule evaluated verbatim beside each one, asserting it
would have blacked out, so no case is vacuous** — plus the cascade actually landing, an Ipsha
Battleglobe proving the fixed-power branch differs from the standard one, a Vorlon Destroyer Escort
proving a charged capacitor raises the ceiling by exactly its charge, and the `$stowed`-defaults-
false trap asserted directly. `edf_shield_test.php` (12) reproduces game 4334's shield **both ways**.
`edf_log_test.js` (8) and `edf_power_test.js` (6) **evaluate the real `weaponManager.js` and
`power.js`** in a `vm` sandbox rather than testing a copy. `edf_grounded_test.js` (15) pulls the two
private helpers out of the real file source (the `HexZoneRef` trick) and drives all three turns.

Regression gate: `checkShipData.php` **PASS** (0 new findings, 235 accepted baseline), replay
harness **129 passed / 1 failed** — game 4325 only, the known pre-existing clean-tree failure — and
`yarn build` run for the React banner (`UI.bundle.js`) and the legacy bundle.

---

**As built — Stage 4d, THE DRAIN IN THE COMBAT LOG AND ON THE SCANNER (2026-09-04, game 4334).**
Three refinements off continued play-testing. Nothing about the drain's *magnitude* changed; this
is entirely about where the record lands.

⭐⭐ **A LOG-ONLY FIRE ORDER MUST HANG OFF THE SHOOTER'S OWN WEAPON, AND THAT DECIDES WHO THE
SHOOTER CAN BE.** The drain reported itself as a **self-targeted** order on the drained unit, so
the log read *"FIRE: &lt;your own ship&gt;"* in your own team's colour and every log filter — by
ship, by shooter, by team — filed the Walker's attack under its victim. It is now a normal
`Walker -> victim` order. The constraint that shapes the fix: the client resolves `fire.weaponid`
**against `gamedata.getShip(fire.shooterid)`** (`weaponManager.getAllFireOrdersLog`, and
`combatLog.logFireOrders`'s own `shipManager.systems.getSystem(ship, fire.weaponid)`), so the row
has to sit on a **Weapon belonging to the shooter** — the Walker's `RammingAttack`, not its
`EnergyDrainingField`, which is a `ShipSystem` and would send the log loop into
`weapon.changeFiringMode()` on an object that has none. **Generalises to every technical fire
order: pick the host off the SHOOTER, and pick a `Weapon`.**

⚠️ **A SHORT-FORM LOG ENTRY PRINTS ITS `pubnotes` ALONE.** `EdfExposure` is in
`weaponManager.doShortLogText`, and that branch is literally `html += notestext` — the *"at
&lt;target&gt;"* clause the long form would render is never built. So the moment the shooter stopped
being the victim, the **victim's name had to move into the pubnotes text**, or the entry named
only the Walker and left the player guessing which of their ships it meant.

⭐ **WHO IS DRAINING WHOM NEEDS A SECOND MAP, BECAUSE `$edfHexes` HAS THROWN THE ANSWER AWAY.**
`TacGamedata::$edfHexes` collapses every source over a hex into a *team set* — which is exactly
what makes overlap and own-fleet immunity structural (§2.1) and exactly why it cannot name a hull.
`setEdfHexes()` now fills a parallel `$edfSources` (`"q,r" => array(shipId => team)`) in the same
loop, and `getEdfSourceShip($pos, $victim)` returns the first source that is not on the victim's
team. ⚠️⚠️ **`$edfSources` is deliberately NOT in `stripForJson()`**: `$edfHexes` tells a viewer
that *some* enemy field covers a hex, which is all the targeting penalty needs, while this one
names the hull — a Walker outside everyone's scanner range would be announced by its own
footprint. It answers with **one** ship even where three fields overlap ("additional fields do not
provide cumulative modifiers"), and a null falls back to the old self-targeted row rather than
losing the entry.

⭐ **THE EW DRAIN MOVED FROM THE CnC TO THE SCANNER — AND THAT IS WHAT MADE THE CLIENT SEE IT.**
`EdfEwDrain` used to be parked on the CnC and subtracted inside `EW::getScannerOutput()`. No client
code could reach it: `ew.js getScannerOutput` sums `shipManager.systems.getOutput` over every
`outputType === "EW"` system and never looks at the CnC, so a drained ship let its owner allocate
EW the server would not honour — the **same class of bug as the reactor one in Stage 4c**, one
system further along. On the Scanner it needs *no new client code at all*: `Scanner::getOutput()`
subtracts it and `Scanner::stripForJson()` publishes `edfDrain`, which
`shipManager.systems.getOutput` **already** subtracts for the Engine and the Reactor. The three
drains are now one shape. ⚠️ The clamp is per-scanner rather than per-ship, which only differs on a
hull carrying a second EW source beside its Scanner; ⚠️ and a hull with no Scanner now loses no EW
at all, which the log no longer claims.

⭐ **AN EDF DROPOUT LEAVES NO DAMAGE ENTRY, SO THE LOG'S FIGHTER ROW NEVER SAW IT.**
`combatLog`'s *"Fighters disengaged / destroyed:"* row is built inside the **damage** loop
(`hasCrit && damageDone > 0`), and a craft the field drops out has a `DisengagedFighter` critical
and nothing else — so the flight's losses existed only as a count inside the drain sentence.
`combatLog.getEdfDropoutNames()` now emits the same row for an `EdfExposure` order.
⚠️ It tests `crit.turn == fire.turn` **exactly**, NOT `shipManager.criticals.hasCriticalOnTurn`:
a `DisengagedFighter` is permanent (`turnend` 0) and that helper's test is `crit.turn <= turn`, so
it would re-list the same craft under every later drain report. ⚠️ It dedupes through
`combatLog.critsShown`, the same tracker the damage path uses, so a craft that dropped out under
fire is never also claimed by the field.

**How it was proven.** `c:\tmp\edf_stage4d_test.php` — 42 checks, all green — drives the real
`Scanner`, `EW::getScannerOutput`, `TacGamedata::setEdfHexes` and `EdfExposure::resolve`: the
turn-filtered drain and its clamp, `edfDrain` published only when in effect, a drain on the CnC
now proven **inert**, `edfSources` covering exactly the same hexes as `edfHexes` and staying out of
`stripForJson`, the own-field-plus-enemy-field case resolving to the enemy, the whole order
(shooter, target, weaponid, `rolled`, zero shots, victim named) and the null-source fallback, plus
a flight's grounded crit, its dropouts and its row. `c:\tmp\edf_logrow_test.js` — 8 checks — renders
one entry through **the real `combatLog.logFireOrders`** and asserts the fighter row, the
this-turn-only filter, the dedupe on a second entry and that an ordinary order grows no such row.

Regression gate: `fvbuild.ps1 -Check` — autoload map up to date, `checkShipData.php` **PASS**,
replay harness **129 passed / 1 failed** (game 4325 only, the known pre-existing clean-tree
failure). ⭐ The 129 passes are themselves the proof that `Scanner::stripForJson()` adds nothing to
an undrained hull's payload: every ship in the corpus has a Scanner and every snapshot is
unchanged.

---

**As built — Stage 4e, THE RULES READ AGAIN (2026-09-04).** Six corrections off continued
play-testing. Three of them are the same shape: the first build read a rule as *"the same penalty,
doubled"* where the rules text actually says *"a different quantity, counted differently"*.

⚠️⚠️ **THE TARGETING PENALTY COUNTS INTERVENING HEXES ONLY — NOT THE SHOOTER'S HEX AND NOT THE
TARGET'S** (user ruling). `HexZone::line()` returns `i = 0 .. steps`, i.e. **both endpoints**, and
the first build counted every hex it returned. So a Walker shooting out of its own field paid for
the hex it was standing in, and a target sitting in one was charged for its own hex on top of the
crossing. `TacGamedata::getEdfPenaltyHexes()` now runs `for ($i = 1; $i < count($line) - 1; $i++)`
and `weaponManager.getEdfPenaltyHexes` mirrors it; adjacent and same-hex shots therefore cross
nothing at all. ⭐ **Worth remembering for any future "hexes between A and B" rule: `HexZone::line`
and `mathlib.hexLine` are inclusive at both ends, and almost every game rule phrased as "hexes the
shot passes through" is not.**

⭐⭐ **"ESPECIALLY DISRUPTIVE TO PLASMA AND ANTIMATTER" IS A DOUBLED HEX COUNT — AND PLASMA SPENDS
IT TWICE** (user ruling, simplified 2026-09-04 after a first pass over-complicated it). For a young
or middleborn race's Plasma and Antimatter, **each intervening field hex counts as two**:

| | EDF targeting penalty | `rangeDamagePenalty` |
|---|---|---|
| **Plasma**, young/middleborn | **×2** (-2 per hex) | **+1 hex of range per crossed hex** |
| **Antimatter**, young/middleborn | **×2** (-2 per hex) | — |
| everything else, and every advanced race | ×1 | — |

⚠️⚠️ **NOTHING HERE TOUCHES `$distanceForPenalty`.** The range the ordinary range penalty is
computed at is the real distance for every weapon in the game, EDF or no EDF — an intermediate
design that lengthened it for Antimatter was tried and rejected as more machinery than the rule
needs. Two helpers on `Weapon` carry the whole thing: `getEdfHitPenalty()` (the count, doubled for
the two classes) and `getEdfDamageRangeBonus()` (**Plasma only**, added to `$dis` in
`getDamageMod`), with `isEdfDisruptedClass()` holding the class + `factionAge` test in one place
and `getEdfCrossedHexes()` doing the gated lookup. The client mirrors the first in
`weaponManager.getEdfPenalty`; it needs no mirror of the second, because it draws no damage
preview.

⚠️ The damage half cannot carry the count over from `calculateHitBase`: damage is a separate pass
(`Firing::fireWeapons` → `getDamageMod`) with none of that method's locals, so it recomputes from
the `$pos` the caller already resolved — which is also the right hex for a ballistic.

⭐⭐ **THE DRAIN IS CUMULATIVE AND LOCKED IN, NOT RE-ROLLED** (user ruling). *"The ship loses 1d10
… increased by a further 1d10 for every additional turn ended in the EDF"* means turn one's roll
**stands**, and each further consecutive turn adds **one more die to the total already in force**.
The first build rolled N fresh dice every turn, which let a second turn in a field come out
**lighter** than the first — the opposite of what an escalation rule is for. A turn out of the
field resets `$consecutive` to 1 and the next entry starts again at one die.

⭐ **AND IT NEEDS NO NEW STORAGE, WHICH IS THE PART WORTH REUSING.** The running total *is* last
turn's `oneturn` critical: `sumCriticalParam($type, $turn)` reports a crit with `turn + 1 == $turn`,
so the accumulator and the reader are the same read. `EdfExposure::accumulate()` is the whole
mechanism — previous total, plus one die — and it takes the system the crit **rides** (Engine,
Reactor, Scanner, or the marker for initiative), because that is where the previous total was
written. ⚠️ An **Enormous** unit keeps its first roll and never adds to it: *"limited to the first
die (1d10 or 1d6) and do not increase"* is a statement about the total, so the roll is not repeated
either. ⚠️ The initiative figure is now clamped at `INI_CAP` **at write time as well as in the
reader**, or an accumulating total would climb for ever and the log would advertise a penalty the
ship never takes.

⭐ **A SERVER-AUTHORED `pubnotes` CANNOT COLOUR A SHIP NAME, AND THAT IS A GENERAL FACT.** A log
ship name is drawn in the **reader's** team colours (`gamedata.getShipLogColorCss`: mine green /
ally blue / enemy red for a 2-team participant, the absolute palette for an observer) and the
server has no idea who is reading. So `EdfExposure` emits the victim as a **bare link span** —
`<span class="shiplink" data-id="123">Name</span>`, no colour of its own — and the new
`combatLog.colourShipLinksInNotes()` fills the style in on the way to the DOM, for **any** pubnotes,
not just this one. It is gated on a single `indexOf('shiplink')`, so notes that name nobody cost
nothing, and an unknown id is left exactly as it came.

⚠️ **THE FIELD OVERLAY HOLDS AN ABSOLUTE WORLD z, NOT A LOCAL ONE.** The disc is a **child of the
ship's mesh** — it has to be, to follow the hull — and that mesh climbs the z ladder as the icon is
selected (`baseZ + 100`) or hovered (`+499`), so its local z rode up with it and washed purple over
the EW lines, which are drawn straight into the scene at **z -5** (`EWIconContainer`'s `LineSprite`).
`ShipIcon.updateEdfFieldZ()` now subtracts the parent's z to pin the disc at `EDF_FIELD_Z` (-20:
under the EW lines, over the hex grid at -500), and is called from `showEdfField` and from **both**
places that move `mesh.position.z`. ⭐ **Generalises to any standing overlay that belongs to the
BOARD rather than to the icon carrying it: parent it for position, compensate its z.**

⭐ **AND THE DISC IS NOW ONLY REBUILT WHEN IT CHANGES** (user: gate the expensive processes).
`syncAllEdfFields` runs on **every poll**, and `showEdfField` called `buildHexRegion` every time —
at radius 8 that is a 289-hex sweep, a fresh `BufferGeometry` and a disposal, for a disc identical
to the one already on screen; worse, it then reported `changed = true` and woke the idle-gated
render loop every poll for the whole game. A disc is fully described by its **radius and the hex it
is anchored on**, so those two are the cache key, `showEdfField`/`removeEdfField` return whether
they actually did anything, and `requestRender()` is called only when one of them did.
⚠️ **AND `setEdfHexes()` NOW ASKS ITS CHEAPEST QUESTION FIRST.** It runs on **every gamedata load
of every game**, and it was calling `isDestroyed()`, `isReinforcement()` and `getHexPos()` on every
ship in the fleet *before* discovering that none of their systems is an `EdfSource`. The system
sweep — a bare `instanceof` — is now the outer test and everything else is deferred behind it, with
the same three exclusions and no new state. The rest of the feature's gating was already right and
was re-asserted by test: `$edfPresent` (server) and `gamedata.edfHexes` (client) keep an ordinary
game to one boolean read per shot.

**How it was proven.** `c:\tmp\edf_stage4e_test.php` — **40 checks, all green**: the endpoint
exclusion from both ends and from both at once, adjacent and same-hex shots, own-fleet immunity
still applying to an interior hex; the whole plasma/antimatter table including `getDamageMod` end
to end (100 damage → 92 clear, **90 for Plasma** through one crossed hex, **92 for Antimatter**
through the same hex — the pair that proves the damage half is Plasma's alone) and ⭐ **the gate
asserted by flipping `$edfPresent` off and watching the same call return 0**;
the escalation driven over five turns with
⭐ **40 sampled 4-turn runs asserting the total never once dips**, which is exactly what a re-rolled
Nd10 would do routinely; the Enormous lock proved by equality across three turns; a turn out of the
field resetting it; the initiative clamp; and the log row's bare shiplink span.
`c:\tmp\edf_client4e_test.js` — **32 checks, all green** — evaluates the real `mathlib.js` and the
extracted `weaponManager` helpers against a **2,400-case corpus the PHP side generated**, zero
mismatches, with ⭐ **the old endpoint-inclusive count run beside it and shown to disagree on 250 of
them**, so the mirror test cannot pass against the bug it was written to catch; plus the doubling
table on both `factionAge` arms, the empty-map gate, and the ShipIcon / PhaseStrategy / combatLog
edits asserted in source.

Regression gate: `fvbuild.ps1 -Check` — autoload map up to date, `checkShipData.php` **PASS**
(0 new findings, 235 accepted), replay harness **128 passed / 1 failed** (game 4325 only, the known
clean-tree failure; game 4324 skipped as advanced-since-record). Legacy bundles rebuilt.



### 3.6 Energy Draining Mine — **BUILT 2026-09-05 (Stage 6)**

`class EnergyDrainingMine extends AoE` in [AoE.php](source/server/model/weapons/AoE.php) (the user's
instruction: *"Electromagnetic, but created in AoE files"*), client twin `EnergyDrainingMine` in
[aoe.js](source/public/client/model/weapon/aoe.js). Range 150, `factionAge = 3`, `weaponClass =
"Electromagnetic"`, `hidetarget`, no intercept rating, **and no damage of any kind** —
`getDamage`/`setMinDamage`/`setMaxDamage` all answer 0 and `fire()` never reaches `AOEdamage`.

Mounted on the Traveler in the **aft** section at arc 0..360, hit chart row 13 (placeholder
placement, like every other Walker stat, until a control sheet says otherwise — D4).

**Storage is `BallisticTorpedo`'s** (§1.3), with one change:
- `loadingtime = 1`, `normalload = 3`, `canSplitShots`, `hextarget`, `ballistic`,
  `defaultShots = 1`; `firedOnTurn()` sums `->shots`; `calculateLoading()` adds at phase 1 and
  deducts at phase 2.
- `getStartLoading()` returns **1**, not 0 and not `getNormalLoad()` — *"begins battles loaded with
  1 mine"*.
- ⚠️ **`turnsloaded` is the COUNT OF MINES HELD, so `loadingtime` must stay 1.** Both
  `Firing::getFireOrderBlock` and `weaponManager.isLoaded` answer "loaded" with
  `turnsloaded >= loadingtime`, so raising the loading time to model a slowed reload would mean a
  crippled launcher needed *two* mines in store before it could fire at all. The reload cadence is
  a separate number, `$reloadInterval`.

**The recharge critical is `IncreasedRecharge1`, reused as-is** (the plan's guessed
`EdfMineRechargeSlowed` was not needed): it already means "recharge increased by one turn" and it
is repeatable, so *"1 per 2 turns, then 1 per 3, and so on"* is the crit COUNT —
`getReloadInterval() = 1 + hasCritical("IncreasedRecharge1")`.

⭐ **THE CADENCE IS `turn % interval`, NOT A STORED COUNTER.** The obvious home for "turns since the
last mine" is the `WeaponLoading` overloading slot, which `BallisticTorpedo` leaves at 0 — but
`weaponManager.isLoaded()` answers `loadingtime <= turnsloaded || loadingtime <= overloadturns`, so
a counter sitting at 1 would make an **empty** launcher read as loaded on the client. The turn
number needs no storage, cannot drift across the double gamedata load (trap 1), and replays
identically. A test asserts `overloading` stays 0 and `loadingtime` stays 1 in every phase.
`reloadsOnTurn()` is the single authority.

⚠️⚠️ **TURN 1 MUST NOT RELOAD — `getStartLoading()` *IS* TURN ONE'S LOAD** (user report, game 4337:
the launcher opened the battle at 2/3 and Traveler #1 declared two probes on turn 1). The reload
branch fires when the game advances **out of DEPLOYMENT**, because
`Manager::advanceGameState` runs its `onAdvancingGamedata` sweep *after* the phase advance and
`DeploymentGamePhase::advance()` has already set phase 1 — so on turn 1 it lands **before** the
player's first Initial Orders. Read the two branch labels accordingly: `currentPhase == 1` means
"we just left Deployment" and `currentPhase == 2` means "we just left Initial Orders", which is
when ballistics commit. ⚠️ The fix is **not** to seed `getStartLoading()` with 0 and let the bump
make it 1 — the ship window would then read 0/3 for the whole Deployment phase, which is the
complaint `MediumLightningArray` already answered the same way (game 4329).

**Scatter: the rules' table, on the family's d100.** `d20` 1–15 on target, 16–20 → `d10` 1–6
scatters `d5` hexes / 7–10 no effect is *arithmetically identical* to `AoE::fire`'s shape —
75% / 15% / 10% — because `0.25 × 0.4 = 0.10`. So `fire()` rolls one `d100` against
`needed = 90` with the on-target threshold at 75, exactly like every other weapon in the family,
and `needed` is **derived from the two constants rather than written as a literal**
(`ON_TARGET_PCT`, `SCATTER_PCT`). The only real difference from the parent is `SCATTER_DIE = 5`
instead of 6. The direction is a separate uniform `d6` rather than being read off the same `d10`;
the distribution over the six facings is the same either way. *"Like Energy Mines, scatter rules
apply"* also brings the family's cap of the distance actually flown — a probe lobbed one hex cannot
land five hexes past its launcher. Measured over 40,000 resolutions: 74.55% / 15.22% / 10.23%, and
a flat d5 (1251/1187/1226/1254/1169).

⚠️ **No to-hit modifiers**, exactly as `AoE::calculateHitBase` — fire control, range and the EDF
penalty all sit a probe's scatter out.

**The field is a spawned Terrain unit,
[`SpawnEnergyDrainingMine`](source/server/model/ships/terrain/SpawnEnergyDrainingMine.php)**, using
the `spawnHyperspaceWaveform` recipe (`Manager::insertSingleShip` → `insertSingleMovement` deploy
order → `SystemData::initSystemData`/`insertSystemData` → `unset($gamedata->ships[$id])`).

⭐ **IT MOUNTS AN ORDINARY `EnergyDrainingField(0, 1, 1, 1, false)` — no bespoke `EdfSource`
class.** `TacGamedata::setEdfHexes()` collects anything implementing the interface, so the drain,
the targeting penalty, the overlap collapse, the own-fleet immunity and the map overlay all pick a
probe up with no code that knows what a mine is. A dedicated class would have needed a client twin
(`SystemFactory` builds with `new window[name]`), a blueprint entry, **and** its own name in
`PhaseStrategy.getEdfRadiusForShip` / `shipCarriesEdf`, which both match on
`system.name === 'EnergyDrainingField'` — three places to forget. Radius 1 is the rules' *"the
destination hex and those immediately surrounding it (seven hexes in total)"*.

⚠️ `Enormous = false`, like `SpawnJumpPoint` and unlike the waveform: Enormous terrain auto-rams
everything that flies through it and joins `blockedHexes`, and a drifting field does neither. Its
primary `Structure` is **indestructible** — the probe's life is one turn and letting damage end it
early would be a second, unwritten rule about when a field stops. One knock-on worth knowing: it is
Terrain, so a hex holding a probe refuses a Jump Engine's vortex declaration
(`Firing::getVortexDeclarationBlock` rejects any terrain-occupied hex).

⭐⭐ **THE ONE-TURN LIFE IS ENCODED IN THE SHIP'S NAME (`"EDM<turn>"`) and re-derived on every
load** in `SpawnEnergyDrainingMine::onConstructed()`. `tac_ship` has no spawn-turn column and the
waveform solves the same problem the same way. **This replaces the plan's `generateIndividualNotes`
cleanup sweep**, and is strictly better: there is nothing to persist, nothing to run, and the probe
still expires on time when the launcher that fired it has been destroyed. It lands during Firing on
turn N and is on the board for turns N and N+1.

⭐⭐ **IT DRAINS ON BOTH OF THEM — INCLUDING THE TURN IT LANDS** (user ruling 2026-09-05, revising
the first build). *"If enemy ships are caught in its AoE on the turn it lands then that counts as
the first turn they are in an EDF, then the field persists for another turn."* So a unit standing
in the seven hexes on turn N takes one die, and two (cumulative) if it is still there on N+1 — the
ordinary `EdfExposed` escalation, with nothing added to it.

Turn N+1 needs no machinery: the probe is an ordinary ship by then and `setEdfHexes()` finds its
`EnergyDrainingField` at load. Turn N does, because the map was built in `onConstructed()` long
before Firing. `EnergyDrainingMine::$pendingFields` queues each landed probe and
`Criticals::setCriticals` drains the queue through the new
**`TacGamedata::registerEdfField()`** — one hex-disc folded into the existing map.

- ⚠️⚠️ **ADDITIVE, NEVER A REBUILD.** Calling `setEdfHexes()` again after Firing would look like the
  obvious fix and is a trap: it skips `$ship->isDestroyed()` with no turn argument, so a Walker
  shot down in that same Firing step would silently stop draining on the turn it died — which
  contradicts `EnergyDrainingField::isEdfActive()`, whose `isDestroyed($turn - 1)` says a system
  killed this turn worked for this turn.
- ⚠️ **The queue is drained at the CRITICAL HIT STEP, not from `fire()`.** Registering at spawn
  time would make the field visible to weapons resolving LATER in the same Firing step —
  `AoE::fire` asks `isHexInEdfField()` to decide whether a proximity blast is contained — so
  whether an enemy energy mine kept its radius would depend on weapon resolution ORDER.
- ⚠️ **The flush sits BEFORE the `TacGamedata::$edfPresent` gate in `setCriticals`**, because
  `registerEdfField()` is what sets that static: inside the gate, a game whose only field is a
  probe that just landed would never reach the resolver. `class_exists('EnergyDrainingMine', FALSE)`
  keeps it to one hash lookup for every other game.
- The queued entries carry the **launcher's** team and id, not the orb's: the team is what makes
  the launching fleet immune, and the orb is not in `$gamedata->ships` this request so
  `getEdfSourceShip()` could not resolve it for the combat log.

⚠️⚠️ **`$removed` IS SET PER LOAD, NEVER ONCE AND FOR ALL.** `BaseShip::isDestroyed()` with no
argument answers **true for any unit whose `$removed` is set, whatever `$removedTurn` says** — so
stamping the flag at spawn time kills the field on the very turn it is meant to work.
`JumpEngine::restoreVortexState` sets it conditionally for the same reason. It is also set for
turns *before* the spawn, so a replay of an earlier turn cannot show the field a turn early
(`setEdfHexes()` skips destroyed units).

**The map marker is PURPLE** — `BallisticIconContainer`'s `modeMap` gains a
`'Energy Draining Mine': { type: 'hexPurple', text: 'Energy Drain Mine', color: '#7f00ff' }` row,
plus the mode name in the splash-hex list so the whole seven-hex disc is drawn rather than the
centre alone. Red is the map's colour for incoming fire and this probe deals none.
⚠️ **The `modeMap` key is the FIRING MODE NAME the server declares**
(`EnergyDrainingMine::$firingModes`), not the system name — a one-character drift between the two
files falls back to a plain red hex with no label and no error. A test compares the two sources.
⚠️ The splash radius is the switch's **default of 1**, which is `FIELD_RADIUS`; move them together.

**Client half.** `weaponManager.targetHex` routes a `canSplitShots` weapon to
`doMultipleHexFireOrders`; one right-click declares one mine, `checkFinished()` unselects the
launcher when the store is spent.
⚠️ **Remaining mines are DERIVED (`turnsloaded − fireOrders.length`), not counted down.**
`torpedo.js` keeps a decrement-only `maxVariableShots`, which the server re-publishes at its full
value on every poll — so a page reload with two mines already declared reports three still
available. `maxVariableShots` is still kept in step for the generic UI that reads it.

⚠️ Spawned ships: `LAST_INSERT_ID` returns a **string**. `Manager::insertSingleShip` casts it.
⚠️ A new shipid-keyed table would need adding to both `deleteGames()` and `leaveSlot()` — this
feature adds none, but the terrain rows follow the existing spawn cleanup.

**Stats as at 2026-09-05:** `maxhealth = 12`, `powerReq = 5` (user's numbers), mounted aft at
0..360 on hit-chart row 13. Art landed the same day —
`img/systemicons/EnergyDrainingMine.png` and `img/ships/WalkerEDMine.png`.

⭐ **THE FIGHTER DROPOUT WAS AUDITED AND IS CORRECT** (user query, game 4337: *"no Frazi fighters
dropped out on turn 1"*). §2.2's `rollDropouts` rolls `(consecutive + 1)d10` against each craft's
**remaining boxes**, the same comparison `Fighter::testCritical` makes with its single d10 — so an
undamaged Frazi's 12 boxes need a 13+. Driving the real resolver 20,000 times from a probe's field
measured **0.362 per craft on turn 1** against a closed-form 0.360, and **0.067 for zero-of-six**
against 0.0687. Game 4337 hit that ~1-in-15 tail on turn 1 and then lost 5 of 6 on turn 2, which is
the *modal* result at 3d10 (37.8%). Nothing to fix.

- ⚠️ **A healthy heavy fighter is 36% to drop out on its first turn in a field, and 78% on its
  second.** That is the rule as written (*"2d10 instead of the usual 1d10, also increased by an
  additional 1d10 for every successive turn"*), and it is brutal — but the die COUNT is the whole
  rule, so the harness now pins it deterministically: at 13 dice the minimum roll of 13 exceeds 12
  boxes, so every craft must drop.
- ⚠️ A craft that has already disengaged is skipped on later turns — `Fighter::isDestroyed()` folds
  `DisengagedFighter` in, and `rollDropouts` tests it. No double-rolling.

### 3.7 Energy Draining Net — **BUILT 2026-09-05 (Stage 7)**

`class EnergyDrainingNet extends ShipSystem implements SpecialAbility, EdfSource` in
`baseSystems.php` beside the field, client twin in `baseSystems.js` — `getEdfRadius()` returns 0.
Control sheet: **health 12, power 4, "Special Electromagnetic weapon"** (a classification, not a
gun: the Net has no fire order, no arc and no target). Linking lives in a new
`source/server/handlers/EdfNetLinks.php`.

⭐⭐ **LINKING RUNS IN `setEdfHexes()`, NOT IN THE §2.2 RESOLVER — the one substantive departure
from this section, and it is a correction rather than a preference.** The section was written
before Stage 4 built the map. The resolver runs at the Critical Hit step, *after* `$edfHexes` has
been built **and published**, so corridors computed there would have drained units while being
invisible to the targeting penalty, to the client's mirror of it and to the map overlay — three of
the four things a field is for. Built at map time instead, all four are served by construction and
the drain still sees them, because `EdfExposure` reads the same map. Everything below stands.

⭐ **THE PAYOFF IS THAT NO CONSUMER CHANGED.** The penalty, the drain, own-fleet immunity and
overlap collapse all key off `$edfHexes` and ask nothing about where a hex came from, so
contributing hexes *is* the whole integration — the same argument Stage 6's orb records.

⚠️ **ORDER IS FIXED: after the per-ship disc sweep, inside its `try`.** The fill cap's *"it is not
necessary to count those hexes in an EDF generated by another vessel"* is answered by asking
whether a hex is already in the map, so the map has to be complete first — including the Nets' own
hexes, which the disc sweep adds at radius 0.

Linking contributes hexes to `$edfHexes`:

1. **Pairwise:** every pair of active Nets **of the same team** at distance ≤ 3 links; the hexes
   between them join the field.

   ⚠️ **`HexZone::line()` is the WRONG tool here and was not used.** It answers *one* line and
   includes *both* endpoints — right for "which hexes does this shot cross", wrong for a rule that
   hands the player a **choice** between corridors whose endpoints are the Nets themselves.
   `corridorCandidates()` enumerates every shortest path instead (a hex is on one iff
   `dist(A,H) + dist(H,B) == dist(A,B)`, walked one step at a time); at range 3 that is at most
   three candidates of two hexes each, so exhaustive enumeration is cheaper than being clever.
   ⚠️ *"The two hexes between them"* is the **range-3 case, not a constant**: 2 hexes at distance
   3, 1 at distance 2, none at distance 1. Adjacent Nets therefore still **link** (they count
   towards a closed area) while contributing no corridor of their own.

   **Ties are resolved deterministically, no UI (D8)** — but toward the corridor a player would
   actually pick, since the field exists to drain enemies and to lengthen the targeting corridor
   through it. Rank candidate corridors by, in order:
   1. **most enemy units standing in the corridor's hexes** (the player's obvious choice);
   2. **most hexes not already in `$edfHexes`** (adds the most new field);
   3. **lowest `(q, r)`**, compared hex by hex — a pure stability tie-break, never a game rule.

   ⚠️ **The ranking must be a total order.** It runs on **both** of the request's two gamedata
   loads, on every poll of every viewer and again on every replay, so any residual tie left
   unbroken would let the corridor flip between them — and the client mirrors the to-hit penalty
   off the published map, so "the corridor moved" reads as *"the client's predicted hit chance
   disagrees with the dice"*. Rule 3 exists solely to guarantee it cannot, and the harness asserts
   it three ways (input order reversed, run twice, and rules 1 and 2 each shown to override it).
   ⚠️ Rule 3 compares coordinates **numerically**, never as a joined string: `"-10,0"` sorts before
   `"-2,0"` as text, which would make the tie-break depend on where on the map the fleet is flying.
   ⚠️ Rule 2 reads coverage **per team** and **live**, updated as pairs are walked — so a hex two
   pairs would both produce counts as new exactly once. That makes the pair order load-bearing;
   it is `usort` by ship id then system id.
2. **Closed-area fill:** the links must actually **close**. `HexZone::containsUnit()` over the
   bounding Nets' positions (which is `hull()` + the edge walk) decides membership, **capped at
   `2 × (bounding Nets) − 1` filled hexes** (*"less than double"*). Over the cap, fill
   nothing — silently filling a partial area is worse than filling none, because dropping *some*
   hexes needs a rule for *which*, and the sheet has no such rule.

   ⚠️⚠️ **A CHAIN IS NOT A CLOSED AREA — the bug play testing found in game 4338 (2026-09-05).**
   The first build filled any *connected* group of ≥ 3 Nets. Three Waymarkers stood at (1,2), (0,0)
   and (1,-3): #1–#2 linked at 2 hexes and #2–#3 at 3, but #1–#3 were **5 apart**, so the three
   were a **chain**, not a ring — and a chain encloses nothing. It filled anyway and painted hexes
   like **(1,0)** that lie beside the chain rather than inside anything. *"Several ships linking up
   in such a manner **form a closed area**"* requires a cycle, and a connected group of N Nets with
   only N−1 links is a tree.
   ⭐ **The area is the group's 2-CORE**: repeatedly drop every Net with fewer than two links until
   none is left to drop. A chain erases itself completely; a ring survives whole; a ring with a Net
   trailing off it keeps the ring and drops the trailer — right for the same reason, since the
   trailer reaches the area by corridor and is not part of its boundary. **One loop replaces both
   "is there a cycle" and "which Nets bound the area"**, with no bridge-finding or
   biconnected-component machinery. ⚠️ The pruning must **iterate**: dropping a chain's endpoints
   leaves its middle with no links, and a single pass would hand a one-Net "area" to the hull.
   ⚠️ **N is the surviving CORE's Net count** — those are the Nets that form the area, so neither a
   second formation elsewhere in the fleet nor a trailing Net buys this one a bigger fill.
   ⚠️ `convexHull` discards collinear middles, so three Nets in a line leave a 2-point hull that
   `pointInPolygon` rejects; the edge-walk inside `HexZone::containsUnit` handles this and is used
   as-is. It also gives "the hull *touches* the hex" for free, which is the reading that matches
   the corridor rule.
   ⚠️ **An over-cap area needs MORE NETS, not more spread** — three Nets at maximum spread have
   corridors along all three sides that swallow the interior, leaving 3 fill hexes against a cap of
   5. The harness had to go to a five-Net arc to exercise the refusal at all.
3. *"It is not necessary to count those hexes in an EDF generated by another vessel"* — hexes
   already in **this team's** field do not count against the cap and are not re-added. An
   *enemy's* field over the same hexes spares you nothing, which the harness asserts both ways.

Criticals: `20+ → EdnPowerDoubled`, escalating on count (×2, ×3, ×4 …). `powerReq` becomes
`base × (1 + count)`, applied in `onConstructed()` from a separate `$basePowerReq` so it is
idempotent.

⚠️⚠️ **`powerReq` IS A BLUEPRINT FIELD AND DOES NOT TRAVEL BY DEFAULT — the assumption that it
"is already published" was wrong, and the smoke test is what caught it.**
`ShipSystem::stripForJson` deliberately leaves it out of the poll payload
(`addBlueprintFieldsForJson` lists it) because it rides the **per-class static ship bundle**, which
was baked long before anybody rolled a critical. A Net the server had escalated to 12 went on
telling the client's power UI, its allocation arithmetic and its "Power Used" tooltip row that it
cost 4 — the player allocates 4, the server refuses, and nothing on screen says why.
`EnergyDrainingNet::stripForJson()` therefore republishes `powerReq` **and `data`** per instance
(`SystemFactory` merges the per-instance JSON over the blueprint, and trap 6 means two Nets on one
hull with different crit counts would otherwise share one tooltip). **This applies to any system
whose criticals move a blueprint number**, which is most of that field list.

**The overlay.** A Net's field is the one field shape with **no unit at its centre** — a corridor
between two hulls, an area between several — so `ShipIcon.showEdfField`'s per-source disc cannot
draw it. `TacGamedata::$edfNetHexes` publishes the hexes themselves as a flat `[{q,r}]` and
`BallisticIconContainer.generateEdfNetHexes` lays a purple blanket over them.
⚠️ **A rendering hint, never an authority**: every one of those hexes is also in `$edfHexes`,
team-tagged, and that is what every rule reads.
⚠️⚠️ **It is drawn as ONE OVERLAY PER CONNECTED CLUSTER, and that is a performance requirement.**
`HexRegion.buildRegionFromHexes` sizes its sweep from the farthest hex from the anchor and then
tests every hex in that square, so a single overlay spanning two Walkers 60 hexes apart would
sweep 121 × 121 = 14,641 hexes to draw a handful — and two lone unlinked Nets at opposite corners
of the board is an ordinary thing to happen, not a corner case.

**The live preview (user request, 2026-09-05).** A Net's whole tactical question is *where do I put
this ship*, and the server's answer is always one commit behind the player. So during **deployment
and movement** the client recomputes the field from **plotted** positions and draws that instead:
`model/EdfNetLinks.js` is a line-for-line port of the resolver, `PhaseStrategy.buildEdfNetPreview`
feeds it from icon positions, and `BallisticIconContainer.refreshEdfNetHexes` redraws.

- ⭐ **Why a mirror is acceptable here when the plan usually resists them:** it is **advisory
  only**. Nothing reads its output but the overlay — the to-hit penalty, the drain and own-fleet
  immunity all read `gamedata.edfHexes`, which is the server's alone. A divergence is a preview
  that redraws on commit, never a rule resolved two ways. **Keep it that way.**
- ⚠️ **Only in phases where positions are still moving** (`-1` and `2`). Everywhere else the
  server's answer is authoritative *and* current, so letting a mirror override it would turn any
  divergence into a permanent wrong picture instead of a self-correcting one.
- ⚠️ **Icon positions, never `shipManager.getShipPosition`** — that reads the last *committed*
  move and would preview the field the player is trying to move away from.
- ⚠️ **`refreshEdfNetHexes` has to prune its own stale clusters.** `consumeGamedata` clears `used`
  on everything and prunes at the end; nothing does that when a plotted step redraws outside a
  poll, so a cluster that *moved* — which is what happens on every step — would leave its previous
  shape behind and the field would smear along the whole plotted path.
- ⚠️ It is recomputed from `onShipMovementChanged`, gated on `PhaseStrategy.anyEdfNetPresent()`,
  because **any** ship's move can change the field — a Net's own, or a bystander's, since corridor
  tie-break rule 1 counts enemy units standing in the candidate hexes.
- ⭐ **Proven by differential, not by unit tests.** `tests/replay/ednDifferentialBoards.php` +
  `tests/replay/ednDifferential.js` run randomised boards through both implementations and compare
  hex, team and attribution. The ported geometry (per-angle touch tolerance, the load-bearing
  `1e-9`, a hull that discards collinear middles) fails on awkward cases nobody writes a unit test
  for — the method §2.3 records for the `HexZone` extraction, for the same reason.
  ⚠️ **The generator had to be taught to build rings deliberately.** Four seeds and 13,000 random
  layouts produced *one* cap refusal between them: six Nets landing on six exact hexes does not
  happen by accident in a 9×9 window. The JS half's **INCONCLUSIVE** exit on an empty coverage
  bucket is what made that visible rather than letting a green run mean nothing.

⚠️⚠️ **`Debug::log` IS NOT USABLE IN THIS PATH, and the reason generalises.** The plan said "fill
nothing **and log it**". `setEdfHexes()` runs on every gamedata load — every poll of every viewer,
twice per request — and `Debug::log` writes a timestamped block carrying the request method, URI,
IP, the whole `$_REQUEST` **and the whole `$_SESSION`** to `fieryvoid.log` per call. A fleet parked
in an over-cap formation is a *persistent state, not an event*, so logging it would dump every
watching player's session to disk every couple of seconds for as long as the ships sat there. The
refusal goes into `EdfNetLinks::$refusals` instead — an in-memory array, cleared at the top of
`resolve()`, which is also why it needs no `DBManager` reset (§2.1's per-load-static trap).

### 3.8 EW Detector

`class EWDetector extends ShipSystem implements SpecialAbility` — `specialAbilities[] = "EWDetector"`.

**Two stages, because the second one is expensive.**

- **Stage A — the allowance.** A fleet sweep computes the saved-EW budget: detectors 1–4 give 1
  each, 5–8 give ½ each, 9+ give ¼ each; round ¼ and ½ down, ¾ up. Ships must be in range of a
  detector both before and after movement (ELINT included). Display the allowance; nothing yet
  allocates it. Cheap, and immediately makes the system legible.
- **Stage B — late allocation.** EW is submitted **only** in `InitialOrdersGamePhase::process`
  ([InitialOrdersGamePhase.php:219](source/server/Phase/InitialOrdersGamePhase.php#L219)) and the
  client hard-gates the UI on `gamedata.gamephase != 1`
  ([ew.js:512](source/public/client/ew.js#L512)). Making one point allocatable at the end of
  Movement means: an EW write path in `MovementGamePhase::process` (budget-clamped, additive only —
  never allow a phase-2 submission to rewrite phase-1 allocations), the client gate relaxed to
  exactly the saved allowance, and a masking review, since EW visibility is phase-conditional
  (`deleteHiddenData`'s phase-1 guard).

⚠️ **Stage B changes a shared, load-bearing path for every faction in the game.** It is the
highest-blast-radius item in this plan. Budget a full `masking` + `snapshot` harness pass for it
alone. Without it the detector does nothing of value, so it cannot simply be dropped — but it
should land last among the non-SCT work.

### 3.9 Sensor Charge Transceiver — **BUILT 2026-09-06 (Stage 9)**

Per D2. Hex-target, split-shot weapon where **each declared shot is a waypoint**. The full rules
text arrived with the stage, so nothing here is inferred. Stats: health 8, power 5, FC 1/2/3,
Standard damage 6d10 with no overkill, loading time 3, weaponClass Electromagnetic, no range
penalty. Server: `SensorChargeTransceiver` in `specialWeapons.php`. Client:
`SensorChargeTransceiver` in `model/weapon/special.js`. Mounted on the **Scribe**, Front section,
**arc 300–60**, hit chart roll 6–8.

⭐⭐ **THE MOUNT AIMS THE LAUNCH, NOT THE COURSE** (user ruling 2026-09-06; this **supersedes** the
original "arc 0–360, the charge steers so the section is for damage, not coverage"). The arc is
real and it bounds the direction the charge **leaves on**: the first leg must run along one of the
hex lines the mount covers — for a 300–60 transceiver that is bearings 300, 0 and 60 — and after
that the charge steers wherever its manoeuvres will take it.

⚠️⚠️ **AND UNTIL THAT RULING THE ARC WAS BEING APPLIED TO EVERY WAYPOINT, SILENTLY.**
`weaponManager.targetHex` gates every hex click on `isPosOnWeaponArc(shooter, hex, weapon)` — the
mount's wedge measured from the **ship** — which is exactly right for a weapon that SHOOTS at a
hex and exactly wrong for one that flies a course through it. So as a course wandered out of the
Scribe's forward 120° the clicks were dropped, **with no message at all** (that `if` has no else),
while the charge's own read-out sat at 9/16 hexes and 2/4 manoeuvres. Reported as *"prevented from
travelling any further… despite only being at 9/16 hexes"* (game 4341); reproduced hex for hex, and
the split between the refused and accepted hexes was exactly the 300–60 wedge (67.6° and 68.9° out,
60°/60°/52.4° in). Fixed with a small generic hook: a weapon may now answer the arc question
itself (`weapon.isHexOnFiringArc`), and the transceiver answers *"only the launch"*.

⭐ **Both ends ask their OWN existing arc function about the hex ONE STEP along the launch bearing**
— `weaponManager.isPosOnWeaponArc` on the client, `$shooter->getBearingOnPos()` on the server —
rather than comparing degrees. That inherits facing arithmetic, the roll mirror, split arcs and a
jammed turret's reduced arc for free, and neither side ever has to assume a hex direction and a
compass heading are the same number.

⭐ **AND A REFUSED CLICK NOW SAYS NOTHING** (user request 2026-09-07). `isHexOnFiringArc` answers
**both** geometry questions — is the hex on a hex axis *from the head of the course*, and, on the
first leg only, is that axis one the mount covers — so `targetHex` drops the click in silence, the
way it refuses an out-of-arc hex for every other straight-arc weapon (that `if` has no else). The
two sentences `measureLeg` used to raise for these are gone: the **blue fan already draws the
answer**, and a pop-up repeating it on every stray click is spam. ⚠️ The two refusals stay in
`measureLeg` with `reason` left **null**, because `measureLeg` is also what replays the standing
orders in `getCoursePlan` — a course read back out of the database has to truncate at leg one
exactly where the server's resolver truncates it. A null reason is the signal to refuse
*quietly*, and `doMultipleHexFireOrders` guards on it rather than concatenating a null into the
player's face. ⭐ The anchor moved with it: the bearing is taken from **`plan.head`, not
`plan.origin`**, because after the launch it is the end of the course a new leg runs from — and
`getHexDirection` returns **0, not null**, for the head itself, so clicking the head still reaches
the one sentence it always had ("at least one hex further on"). The two refusals that are *not*
geometry — a zero-length leg and an unaffordable one — keep their sentences, because the fan
cannot express either.

⭐ **CONTACT WITH A RECEIVER FINISHES THE COURSE** (user ruling 2026-09-07; this **supersedes** the
original "deliberately NOT once the course has reached a receiver"). `checkFinished` now unselects
the transceiver the moment the last waypoint holds a friendly transceiver, exactly as it does when
the charge runs out of hexes — the charge is home, so the declaration is done and the weapon gets
out of the player's way. ⚠️ Unselecting is **not a lock**: the course lives in `->fireOrders`,
`getCoursePlan` rebuilds it from them, and nothing gates re-selecting a weapon on `checkFinished`,
so a player who does want to fly on past a receiver picks the transceiver up again and keeps
clicking. ⚠️ Both branches now go through one predicate, `isCourseFinished(plan)`, which the ship
window's `maxVariableShots` reads as well — two derived read-outs disagreeing about whether a
weapon is done reads as a bug, and the shots figure was the one that would have kept counting.

**The shape of it, and why it needs one:** this weapon does not shoot at a target. The player plots
a COURSE and the charge damages whatever enemy units it passes through on the way, so the
declaration and the shots are two different things:

1. the client declares one hex fire order **per waypoint**, each carrying a `SCT|w:<n>` token in
   `->notes` — the Slicer's allocation channel, used here to carry a path;
2. `beforeFiringOrderResolution()` re-walks the course from scratch, **detaches every waypoint
   order** from `$this->fireOrders`, and puts back one real damage order per enemy-occupied hex
   plus one informational order for the combat log;
3. `Firing::prepareFiring` rolls those the ordinary way.

⚠️ The waypoint orders are **already in the database** when step 2 runs (`FireGamePhase::process`
persisted them at the POST), so detaching is an in-memory act only — and that is what makes the
course re-derivable, which the recharge depends on. Detaching is not optional: left in place,
`prepareFiring` hands each waypoint to `calculateHitBase` with `targetid -1` and stamps *"ERROR:
Null target shot attempted in normal fire routines"* into the player's own combat log.

**The path model (the one simplification worth arguing about).** The rules move the charge "in a
manner similar to a fighter… speed 16 with 16 thrust and a 1/4 turn cost", i.e. 16 hexes and 4
manoeuvres, "with a manoeuvre counting as a turn or slide". A full fighter plotter would be a second
movement engine; instead **a leg between two waypoints must run along one of the six hex axes**, and
changing axis at a waypoint costs `mathlib::getHexTurnCost()` manoeuvres — 1 for 60°, 2 for 120°, 3
for a reversal — which is what the same course costs a fighter. A one-hex leg at 60° is a slide,
priced at a slide's own cost. **The first leg is free**: the charge is launched, not turned.

**⭐ The boost's split is inferred, not declared.** "Every additional 2 points of power applied as a
boost produces either 1 additional hex of range or… an extra manoeuvre." `boostEfficiency = 2` is
those two points and one level buys one point — but which axis it is spent on is worked out at
resolution from the course actually plotted: `max(0, hexes−16) + max(0, manoeuvres−4) <= boost`.
One expression, no Initial Orders allocation dialog, and the totals are identical to declaring the
split in advance. The boost itself is still bought in Initial Orders, which is what the rules'
"must be configured for firing" asks for. `maxBoostLevel = 4` is the one number the rules do not
give.

**Truncation, not skipping.** The legs of a course are a chain, so dropping one in the middle would
teleport the charge across the gap. The first leg the budget cannot pay for ends the course and
everything after it is rejected with it.

**Targets.** One shot per enemy-occupied hex, origin hex excluded (a charge has not "passed
through" the hex it launched from). The rules make hitting optional and that half is still resolved
server-side — declining a free hit is never a decision. The automatic pick is deterministic and
replay-stable: capitals before small craft, bigger before smaller, then lowest id.

⭐ **The choice between units sharing a hex is the PLAYER'S, and it needed no new gesture**
(user ruling 2026-09-06). It first shipped server-side too, on the grounds that a per-hex target
picker would be a whole second gesture on top of plotting — but it does not have to be one, because
a waypoint that carries straight on costs **no manoeuvre and no extra range beyond the hexes it was
going to cross anyway**. So dropping a waypoint on a crowded hex is already free, and the choice
rides on the waypoint: the ship tooltip's hex button reads **"Target Ship"** whenever a transceiver
is selected and the unit under the cursor is an enemy, and the id it names is appended to the
waypoint token as `SCT|w:<n>|t:<shipid>` (`TARGET_TOKEN`).

⚠️ **ADVISORY, NEVER AUTHORITATIVE.** `pickTargetInHex()` takes the named id as a hint and it wins
only by passing the same eligibility loop as every other candidate — enemy, alive, not terrain,
deployed, and actually standing on that hex. Anything that fails one of those is not an error: it
falls silently back to the automatic pick, so a stale or hostile POST can change WHICH legal enemy
in a hex is hit and nothing else. The client refuses the same cases before writing the token
(`resolveHexTargetChoice`), so the two ends agree, but the server never trusts that.

⚠️⚠️ **AND IT NEEDED A MASK.** `hidetarget` blanks `x`, `y` and `targetid` for an enemy viewer,
but it has never touched `->notes` — so the token would have named a ship standing on a course whose
hexes had just been blanked, which is exactly the information `hidetarget` exists to withhold. New
opt-in flag `Weapon::$hideNotesFromEnemies` (protected, with a getter, like `$alwaysHideFireOrders`)
blanks the notes in the same branch. Opt-in rather than blanket: `->notes` is a general-purpose
channel and most of what rides it is either public or written at resolution.

**The receiver.** The last waypoint must hold a friendly ship (same TEAM) with an undestroyed
transceiver — the firing ship's own hex counts, per "or possibly returning to the originator", and a
closed triangle of side 5 costs exactly 15 hexes and 4 manoeuvres, so a **single** transceiver is a
usable weapon. If nothing is there the charge is lost and does no damage at all, whatever it flew
over. A receiving transceiver takes 1 point per 2 full hexes of **base** range left unused (never
the boosted range: boost spent on manoeuvres has not lengthened the charge, and boost spent on hexes
has by definition been used), as an ordinary `DamageEntry`, so `Criticals::setCriticals` rolls its
critical in the same pass as everything else damaged that turn.

**The dual recharge.** 3 turns after a recovered charge, 1 turn when it was not recovered.
⚠️ **Interception is not modelled as an event of its own** — FV only intercepts ballistics and this
weapon declares and resolves inside the Fire phase, so "not recovered" is the general case the rules
give interception as one example of.

**The overlay** (`BallisticIconContainer.generateSensorChargeCourses` /
`refreshSensorChargeCourses`) — **four** scene objects, all of them built from the weapon's own
`getCoursePlan()` so what the player sees and what `doMultipleHexFireOrders` will accept can never
disagree. Reworked 2026-09-06 after play testing; what the first build got wrong is recorded with
each one.

- **The course**, one line per leg, **green** once it ends on a friendly transceiver and **yellow**
  until then — that colour change is the whole "or the charge is lost" rule, shown rather than
  explained. Now with **chevrons every 2 hexes**, walked BACKWARDS from each waypoint so every leg
  finishes with one pointing into its corner. A course crosses itself, doubles back and ends
  nowhere in particular, so which way round it is flown is not something the geometry says on its
  own — and it is what decides which end has to hold the receiver.
- **The reachable fan**, in the ordinary **weapon-arc blue** (`rgb(20,80,128)`, mirrored from
  `ShipIcon.showWeaponArc` — it makes the same statement a firing arc does, so it should not have
  a colour of its own; the cyan it launched with belongs to "not here yet" markers), drawn only
  while the transceiver is SELECTED. ⚠️ **This was six
  LINES and is now HEXES** (`getReachableHexes` → `buildHexRegionOverlay`). The original argument
  for lines was cost — ~96 hexes over a 41×41 sweep — and it was true but beside the point: a line
  asks the player to judge whether a hex centre sits on it, and the answer to *"can I click there?"*
  is a hex. The sweep only runs when the weapon is selected AND the course changed
  (`syncSceneObject`'s signature covers both), which is a handful of rebuilds per turn.
- **The markers**, green hexes, and NOT one per waypoint: a course run along one axis can hold a
  dozen and marking them all buries the line under hexes that say nothing. What earns a hex is a
  place where something HAPPENED — where the charge **manoeuvred**, the **head** (the last hex
  clicked so far), and any hex where the player **named a unit**. That third case is not decoration:
  a straight-through waypoint costs no manoeuvre, which is exactly what makes it the free way to
  pick a unit out of a crowded hex, and without a marker the choice would have nothing on screen to
  confirm it. Drawn as `BallisticSprite`s rather than one `HexRegion` blanket because they are
  scattered (a region would be one loop per hex anyway) and because a sprite can carry TEXT — which
  is how a named hex says whose name it is.
- **The budget**, two rows of figures at the head, SELECTED-only, in the LoS ruler's idiom
  (`mathlib.drawRuler`). Hexes and manoeuvres, each as **spent-of-total**. ⚠️ The totals are
  DERIVED (`used + left`), never the class constants: boost levels are bought in Initial Orders and
  go to either axis, so "16" stops being right the moment the player buys one.
  ⚠️⚠️ **AND A THIRD ROW, `+N`, WHENEVER THE WEAPON IS BOOSTED** — added 2026-09-06 after the
  report *"seems to run out of hexes after it goes above the normal 4 manoeuvres"* (game 4341,
  Scribe #2, boosted 3). **There was no arithmetic bug**: the two totals are each *"the most THIS
  axis could take if nothing further goes to the other"*, and boost is a **shared** pool — so they
  can never both be reached, and the hex total visibly shrinks 19 → 18 → 17 as manoeuvres eat
  levels off it. Carrying straight on really does still have the whole remainder (proved on both
  ends over the identical course). What was missing was any sign of the pool the two rows compete
  for; `+N` is that pool. The ship window's "Charge remaining" line says the same thing in words.
  ⭐ Generalises: **two "remaining" figures drawn from one pool will always contradict each other
  — show the pool, or show one figure.**
  ⚠️⚠️ It sits at **z 130**. The head of a course is exactly the sort of hex that holds a stack of
  ships and a selected or active-mover hull sits at +100 for a whole phase, so the requirement is
  z > 100, not z > 0 ([[arch_map_z_planes]]).

**Tuning it by hand.** Every number that sizes this overlay is one `const` in the block above
`buildLineChain`, and they are meant to be edited: `SCT_ARROW_SIZE` (barb length in hexes — the
arrow knob), `SCT_ARROW_EVERY` (hexes between chevrons), `SCT_ARROW_ANGLE` (how wide a chevron
opens), `SCT_LABEL_SCALE` (the whole read-out's size in hexes — the font knob), `SCT_LABEL_FONT_PX`
(glyph against padding inside the canvas) and `SCT_FAN_DIM` (how strongly the fan tints). All six
were halved or better on the first play test. Nothing else changes when one moves — they feed the
builders directly, and each is in hexes or canvas pixels so it holds at every zoom.

⚠️ **AND THE WAYPOINTS ARE NOW OFF THE BALLISTIC LAYER ENTIRELY.**
`getAllFireOrdersForAllShipsForTurn` admits any normal order with `targetid -1` as a ballistic, so
every waypoint had been drawing a red "incoming fire" hex and a white arrow back to the shooter —
a dozen of them fanning out of one hull, each describing a straight line to a corner of a course
that is not straight and does not start there. `consumeGamedata` now drops the order on its
`damageclass` (`'sensorcharge'`), at the LOOP level rather than inside `createBallisticIcon`, for
the same reason the Gravitic Mine suppression is there: `updateBallisticIcon` would otherwise keep
icons left over from a Replay session alive.

⚠️ `refreshSensorChargeCourses` marks and sweeps BY PREFIX and now owns **four** of them
(`SCT_KEY_PREFIXES`). It runs outside the `consumeGamedata` pass, which is the only thing that
clears `used` on everything — so a prefix left out of that list is an overlay that never goes away.

**The hover arc.** ⚠️ The transceiver drew **no arc at all** on hover until 2026-09-06.
`ShipIcon.showWeaponArc` sizes every arc through `getWeaponReachInHexes()`, which reads `range`
and `rangePenalty` — and this weapon carries **both at 0**, meaning "no range limit" and "no range
penalty", which works out to a reach of zero hexes and nothing drawn. Its `stripForJson` now also
publishes `shootsStraight` (so it takes `showStraightArcs`' **star of hex lines**, clipped to the
mount's arc — three arms for a 300–60 mount, which is precisely the set of hexes a charge can
launch onto) and `arcDisplayRange = CHARGE_RANGE` (the reach to draw, since `range` does not
describe it). Base range rather than boosted: a hover arc is a property of the mount and the boost
is bought per turn. ⭐ Both ride the **poll payload**, published per instance, so no static
regeneration is needed — the same fix shape as the Stage 7 `powerReq` trap.

**Deliberately not done:**
- the charge gets no hit section of its own — a shot resolves on the FIRING ship's bearing like any
  other direct-fire weapon, not on the bearing of the leg it arrived along. Doing it properly needs
  a ballistic-style hit LOCATION *and* a ballistic-style defence PROFILE, and the two have to move
  together or they describe different shots;
- no line-of-sight test along the course. A steered charge is not a beam, and the engine's only LoS
  test is a straight line from shooter to target hex, which a course is not.

**New shared geometry:** `mathlib::getHexDirection()` and `mathlib::getHexTurnCost()` (server) with
mirrors in `mathlib.js`. ⚠️ The client mirror uses **`hexagon.Offset`'s** neighbour table, which is
byte-for-byte the PHP `mathlib::$neighbours` one — *not* `mathlib.offsetNeighbors` underneath it,
which lists the same six hexes in a different order. The order of that table is the entire meaning
of a direction index, so the wrong one gives the client a different compass and every leg but due
east disagrees.

---

## 4. Stages & exit criteria

Ordered so that each stage is independently shippable and the risky shared-path work lands last.

| # | Stage | Exit criterion |
|---|---|---|
| **0** ✅ | `HexZone` extraction (§2.3) — **DONE 2026-09-03** | Replay harness identical before/after; 13,504-case differential test against the pre-move bodies, zero mismatches. |
| **1** ✅ | Faction skeleton — directory, tier line, and the **user's Walker test hull** (D4) — **DONE 2026-09-03** | `Traveler` generates into `Walkers of Sigma-957.json`; `checkShipData.php` clean (0 new findings, down from 5). |
| **2** ✅ | Lightning Array + Medium Lightning Array — **DONE 2026-09-03; targeting REVISED twice the same day after play testing** | 157 checks green on the first build, +138 across the revisions, incl. a JS-vs-PHP comparison of all six tables and both mode constants, and the intercept gun accounting verified against the REAL `Firing::isValidInterceptor` over ten scenarios. Two firing modes (Combined / Single), now `multiModeSplit` so both are usable in one turn; both weapons intercept. Revisions: **no allocation dialog** — one click = one discharge, repeat clicks on one target fuse; count rides in `->shots`; `'Sweeping'` + `"Split"` so the shot shows in the shooter's INCOMING list, which counts DISCHARGES not orders; withdrawing PEELS one discharge off a combined shot; Medium starts 1/2, not 0/2. |
| **3** ✅ | Chromatic Pulse Driver — **DONE 2026-09-03; CLOSED 2026-09-04** | 186 + 56 server checks and 35 client checks green (2026-09-04: the client half was DEAD until gamedata.js was taught to copy `cpdAdaptation` off the payload). Two firing modes (Pulse / Scanning), the Pulse profile keyed by turns charged; a Scanning hit reduces the target race's shields fleet-wide from the **next** turn, survives a reload, and the double-load trap is demonstrated BOTH ways. ⭐ Publication changed from the plan: the reduction lands on the AGGREGATED defensive bucket in `BaseShip::getHitChanceMod`/`getDamageMod` + FighterFlight's two, not inside each of the nine shield classes — four edits instead of eighteen, once-only by construction, no-op when no CPD is in the game. It lives in `pulse.php`/`pulse.js`, not the Walker files, because `special.js` loads BEFORE `pulse.js`. ⭐ Closed 2026-09-04 with the CAPACITY-POOL half: Thirdspace, Thought and both Trek shield projections hold a pool instead of a modifier, so the bucket reduction reached nothing on them; they now lose the same points off the pool via `Shield::getCapacityAgainstShooter()`, and `doesProtectFromDamage` gained an optional `$shooter`. |
| **4** ✅ | EDF + Variable EDF (§2.1 + §2.2) — **DONE 2026-09-04** | 71 + 105 server checks and 32 client checks green. The field (fixed and variable, both crit ladders, boost, deactivation) on the Traveler with its chart row restored; `TacGamedata::$edfHexes` published hex-keyed so overlap collapse and own-fleet immunity are structural; the targeting penalty server↔client, agreeing over a 4,000-line / 128,745-hex differential corpus; the drain landing as six one-turn param criticals read at four choke-points; and the map overlay. ⭐⭐ Two findings worth carrying: the client parity needed a port of PHP's `round()`, not `Math.round` (half-away-from-zero AND PHP's pre-rounding, worth 843 and 13 wrong lines), and the drain needs NO replay roll-note because the RESULT is the record — every roll lands in a persisted crit's param and an `EdfExposed` marker makes a reload idempotent. ⭐ The full rules text arrived the same day and corrected three things: the EW drain is **d6** not d10; a fully drained reactor blacks out every powered system, reusing `Reactor::addCritical`'s existing cascade; and flash/proximity weapons lose their collateral and their blast radius inside a field. Nothing in Stage 4 is inferred any more. |
| **5** ✅ | EDF criticals + EDF Range enhancement + the `factionAge` gate fix (§3.3) — **DONE 2026-09-04; the refit's RANGE fixed 2026-09-05 after play testing** | 98 server checks and 50 client checks green, plus a **2,573-hull corpus differential** on the offer tuples: the Traveler is the ONLY hull whose offers changed, and only by gaining `EDF_RANGE` + the user's `WalkerShip` set. The crit ladders were already built in Stage 4 and are confirmed against the rules text, which arrived afterwards and matches them exactly (fixed 21+, floor 1; variable 20+, boost first then a hex each, floor 0). ⭐ The refit went **ship-level** (`EDF_RANGE`), not per-system — see §3.5 — and its variable-field rule is implemented by **spending `boostRadiusBonus` down as `radius` goes up**, so the double-power radius is unmoved by construction. ⚠️⚠️ Two findings worth carrying: rewriting the Ancient-weapon gate as a bare `factionAge > $ship->factionAge` silently stripped every refit from the ~40 **middleborn** (`factionAge = 2`) weapon classes on young hulls — the corpus differential was the only thing that caught it, and the test needs BOTH halves (§3.3); and Stage 4 had shipped the variable field's **boost for free** (`boostEfficiency = 0`, which is the EXTRA power a boost level costs, not a flag), now set from `$powerReq` so double power really is double. |
| **6** ✅ | Energy Draining Mine — **DONE 2026-09-05; three play-test revisions the same day** | 215 server checks and 53 client checks green, including a 40,000-shot resolution run measuring 74.55% / 15.22% / 10.23% against the rules' 75 / 15 / 10 and a flat d5 scatter. Stores 3, begins with 1, launches any number at any hexes, spawns a 7-hex field that expires after one turn. ⭐ Two departures from the plan, both simplifications: the orb carries an **ordinary `EnergyDrainingField`** rather than a bespoke `EdfSource` (so the drain, the penalty and the map overlay needed no new code at all), and its one-turn life is **derived from the ship's name on every load** instead of a `generateIndividualNotes` cleanup sweep — which means it still expires on time when the launcher that fired it is dead. `IncreasedRecharge1` covered the crit ladder as-is. ⚠️⚠️ Two findings worth carrying: `turnsloaded` is the MINE COUNT, so `loadingtime` must stay 1 or a crippled launcher needs two mines in store to count as loaded at all — the slowed reload is a separate `turn % interval` cadence, deliberately NOT the `overloading` slot, because `weaponManager.isLoaded` also tests that and would call an empty launcher loaded; and `$removed` has to be re-decided on every load, because `isDestroyed()` with no argument is true for ANY removed unit whatever `removedTurn` says. **Play-test revisions (game 4337):** turn 1 must not reload — the reload branch fires when the game leaves DEPLOYMENT, which on turn 1 is before the player's first Initial Orders, so the launcher opened at 2/3; the map marker is now a PURPLE seven-hex disc labelled "Energy Drain Mine" rather than the default red hex; and ⭐⭐ **the field now drains on the turn it LANDS as well as the turn after**, via a queue flushed through the new `TacGamedata::registerEdfField()` at the top of `Criticals::setCriticals` — ⚠️ additive and never a `setEdfHexes()` rebuild (which would stop a Walker shot down in that same Firing step from draining on the turn it died), flushed at the Critical Hit step rather than from `fire()` (so a probe cannot contain an enemy proximity blast declared later in the same step, which would be resolution-order dependent), and placed BEFORE the `$edfPresent` gate because `registerEdfField()` is what sets it. Zero replay drift across the 128-game corpus. |
| **7** ✅ | Energy Draining Net — **DONE 2026-09-05; play-test fix + live preview the same day** | **Play-test revision (game 4338):** the fill treated any *connected* group of 3+ Nets as a closed area, so three Waymarkers in a **chain** (#1–#2 at 2 hexes, #2–#3 at 3, #1–#3 at 5) filled hexes beside the chain that nothing enclosed — the user reported (1,0). *"Form a closed area"* needs a **cycle**, and the fix is the group's **2-core**: iteratively drop every Net with fewer than two links. A chain erases itself, a ring survives whole, a ring with a trailer keeps the ring. **Live preview added the same day:** deployment and movement now recompute the field client-side from PLOTTED positions (`model/EdfNetLinks.js`, a ported resolver) so the corridors and the filled area form as the ship is dragged; **advisory only** — nothing but the overlay reads it. Proven by a **12,000-board differential** against the PHP across three seeds, hex, team and attribution, zero mismatches, with the generator taught to emit rings deliberately after the coverage guard caught that 13,000 random boards had produced a single cap refusal between them. 68 server checks, 16 preview checks and 7 clustering checks green. **As first built:** replay harness 128 passed / 1 failed, byte-identical to the same run on a stashed tree (game 4325, the known clean-tree failure), so zero drift. `checkShipData.php`: 0 new errors. Pairwise links at 1/2/3 hexes and not at 4; the closed-area fill capped at `2N−1` with the corridors isolated out first so the refusal is provable as the *empty set* rather than "fewer hexes"; three collinear Nets still link. ⭐⭐ **Two departures from §3.7, both corrections.** Linking runs in `setEdfHexes()`, not in the §2.2 resolver — the section predates Stage 4's map, and a corridor computed at the Critical Hit step would have drained units while being invisible to the targeting penalty, the client's mirror and the overlay. And `HexZone::line()` is the wrong tool: it answers ONE line including both endpoints, while the rule hands the player a CHOICE between corridors, so all shortest paths are enumerated instead. ⚠️⚠️ Three findings worth carrying: **`Debug::log` cannot be used anywhere `setEdfHexes()` reaches** — it dumps `$_REQUEST` and `$_SESSION` to disk per call, and a fleet parked in an over-cap formation is a persistent state polled every couple of seconds, so the refusal is an in-memory array instead; **the map overlay must be split into connected clusters** because `HexRegion.buildRegionFromHexes` sizes its sweep from the farthest hex, and two lone Nets at opposite corners of the board would sweep 14,641 hexes to draw two; and **an over-cap area needs more Nets, not more spread** — three Nets at maximum spread have corridors that swallow their own interior, leaving 3 fill hexes against a cap of 5, so the refusal test had to go to a five-Net arc. ⚠️⚠️ And one bug caught by a smoke test rather than by any of the 51 checks that preceded it: **`powerReq` is a BLUEPRINT field that rides the per-class static bundle, not the poll payload** — so the crit-escalated requirement never reached the client until `stripForJson()` republished it per instance. Applies to any system whose criticals move a blueprint number. |
| **8** ✅ | Wide-Beam enhancements — **DONE 2026-09-06; reworked twice the same day** | 143 server checks and 84 client checks green, plus a **2,578-hull corpus differential** on the offer tuples in which exactly TWO lines changed (Traveler gains one `SYS_WBLA` + one `SYS_WBMLA`; Waymarker gains one `SYS_WBMLA` per Medium array), and a replay-harness run of 127 passed / 1 failed that is byte-identical to the same run on a stashed tree (game 4325, the known clean-tree failure). `checkShipData.php` PASS, with the same 3 pre-existing warnings on both trees. Two registry entries at 300 / 200, `ages => array(3)`, `limit` 1; the per-die floor; the 50% / 25% collateral; the one-turn cooldown. ⭐⭐ **Reworked TWICE on the user's rulings.** It shipped as one extra firing mode; the user pointed out that the rules' *"in all modes"* means *whatever the discharge count*, so it became **four** modes (Combined / Single × normal / wide); then the four-entry selector was judged too clunky — *"mechanically that all seems to work perfectly, however the UI is a little bit clunky"* — and it is now **two firing modes plus a per-turn "Wide Beam" toggle** in the array's `<SystemActivation>` box, which is what the rules describe anyway (*"the lightning array may be configured to fire a wide beam"*). ⚠️⚠️ **Six findings worth carrying**, all in §3.3: **the Fire phase has never run the generic `generateIndividualNotes` sweep and must not start** — 34 of the ~80 overrides carry no phase guard at all — so the toggle's write is a new narrow `ShipSystem::saveFirePhaseDeclaration()` hook that does nothing by default; **that write cannot be gated on the refit**, because a POST-side ship is rebuilt without enhancements, so it writes unconditionally and the refit is re-checked at read time; **both toggle states are written and the highest note id wins**, since the load query cannot promise an order within a phase and writing only on arm strands a re-commit on a stale 1; the four-mode detour exposed a REAL bug — `getCombinableOrder` matched *"not Single Shots"* rather than *"this mode"*, so with two fusing modes a wide click silently converted a standing ordinary shot, cooldown and all, and the equality fix is kept; the cooldown had to zero **`overloadturns` as well as `turnsloaded`**, because `calculateLoading` increments `overloadturns` at every turn advance for EVERY weapon and `weaponManager.isLoaded` is an OR of the two; and **nothing on the server refuses an offensive order from an unloaded weapon at all**, so the cooldown needed a server half of its own. Plus two smaller ones: **`MediumLightningArray extends LightningArray`**, so the full array's refit needs an explicit subclass exclusion or every Medium is offered both at once on one mount; and **50% collateral must be computed from the damage**, never by doubling the 25% figure. |
| **9** ✅ | Sensor Charge Transceiver — **DONE 2026-09-06** | 72 checks green in one harness covering both ends, plus a replay run of 127 passed / 1 failed that is **identical to the same run on a stashed clean tree** (game 4325, the known clean-tree failure) with timings normalised, and `checkShipData.php` PASS with the same 3 pre-existing warnings. The harness proves four things nothing else would catch: a **1,080-case geometry differential** on the three new `mathlib` helpers, PHP against the JS mirror, with 495 of the pairs on a hex axis and turn costs 0–3 all present; the resolver over a **12-course corpus** written as (bearing, length) legs rather than hexes; `beforeFiringOrderResolution` end to end against a real `TacGamedata` with a stand-in DBManager — every shot claiming its own database id, the informational row at `rolled 1 / shots 0`, the receiver's `DamageEntry` filed against that row and visible to `isDamagedOnTurn`; and the client's `measureLeg` accepting **exactly** what the server's resolver accepts, over the same corpus. ⭐ Every group asserts its own non-vacuity. ⚠️⚠️ **The test found one real bug that no amount of reading would have**: `calculateLoading` asked `getChargeOutcome` AFTER `parent::calculateLoading`, which calls `setLoading()` and writes `turnsloaded` back to 0 — so `isReadyToFire()` read 0, every charge looked as though an unloaded transceiver had sent it, and the fast recharge could never fire. The outcome is now taken before the parent runs. ⚠️ The icon is a **placeholder** (a copy of `sensorSpike.png`) until real art lands. **Play-test revisions 2026-09-06 (§3.9):** the reachable fan is HEXES rather than six lines; the course carries direction chevrons and the waypoint orders are suppressed from the ballistic layer (they were drawing a red hex and a white arrow each); green markers at the manoeuvre points, the head and any hex where a unit was named; a two-row spent-of-total budget label at the head while the weapon is selected; and the **choice between units sharing a hex is now the player's**, carried as `SCT|w:<n>|t:<id>` from a "Target Ship" tooltip button, advisory-only at resolution, with a new opt-in `Weapon::$hideNotesFromEnemies` so `hidetarget` blanks the token along with the x/y it already blanked. 68 client + 47 server checks green in a throwaway harness covering both ends, and a replay-harness run of 127 passed / 1 failed that is **byte-identical to the same run on a stashed clean tree** (game 4325, the known clean-tree failure) with timings normalised; `checkShipData.php` 0 new errors, the same 3 pre-existing warnings. **Play-test revisions 2026-09-07 (§3.9), client-only:** a refused hex click is now **silent** — `isHexOnFiringArc` owns both geometry refusals (off-axis, and an off-arc launch), measured from the **head** rather than the origin, and `measureLeg` keeps them with `reason` null so a replayed course still truncates identically; and **contact with a receiver finishes the course**, unselecting the weapon and zeroing its shots read-out through one shared `isCourseFinished` predicate. 49 checks green in a throwaway harness, which **fails 11 of them on the pre-edit bodies** — all 11 exactly the changed behaviours, with the "ran out of hexes" branch passing both ways. No server change, no serialised field, so no replay-harness run. |
| **10** | EW Detector — Stage A then Stage B (§3.8) | Allowance correct at 1/4/5/8/9 detectors; phase-2 EW write is additive and budget-clamped; full `masking` + `snapshot` harness pass. |

**Every stage:** run `fvbuild.ps1 -Check` (ship-data validator + replay harness). ⚠️ The baseline
drifts on a clean tree — never read a pre-existing FAIL as your regression, and **never
blind-re-record**. The method that works: capture the check output, `git stash push -- source/`,
re-run, `git stash pop`, diff with timings normalised.

Stages 4, 5 and 10 each change a serialised property or a shared payload, so the harness `check`
is mandatory rather than advisory on those. ⚠️ **Stage 3 turned out to be one of them too** — it
added `TacGamedata->cpdAdaptation` and put a line into the four defensive-mod aggregators every
faction in the game runs through. It came out clean (130/1, the known 4325 failure), but the rule
should be read as "any stage that touches a shared path", not as a fixed list.

**Each stage opens on a control sheet (D4)** — the user lands a Walker hull carrying a basic version
of that stage's system, and the stats are read out of the hull file. Stages **0 and 1 need nothing**:
Stage 0 is a pure code move and Stage 1 *is* the first test hull.

---

## 5. Trap register

Collected from the survey; each one has bitten this codebase before.

1. **The double gamedata load.** `Manager::advanceGameState` loads, then the phase's `advance()`
   loads again — in one request. Any per-load static (§2.1, §3.4) must be reset in
   `DBManager::getSystemDataForShips` before the `onIndividualNotesLoaded` sweep.
2. **POST-side ships have no enhancements and no loaded notes.** Anything reading a Wide-Beam
   purchase or a CPD scan note inside `generateIndividualNotes` / `process()` reads a class default
   and fails **silently, forever**. Server-authoritative checks belong in `advance()`.
3. **`advance()` has already set the next phase** before its ship loop. Never branch on
   `$gamedata->phase` there; pass an explicit checkpoint.
4. **`notekey` / `notekey_human` are `varchar(40)`** and overflow is a fatal that aborts the whole
   submission. `substr($x, 0, 40)`.
5. **FV initiative is d100** — every modifier is 5× tabletop. The EDF's `−20` cap is `−100` here.
6. **Client system fields are shared by reference** across same-phpclass instances. Any per-instance
   mutation (a modified `data` tooltip, a per-array Wide-Beam flag) needs `isModified` handling or
   it bleeds onto every sibling array on the ship.
7. **Positional system ids.** Ids are construction order; a variant needs a **new phpclass**, never
   a reordered constructor.
8. **`ShipCompactor` strips blueprint keys.** Adding a public property to a system is not free —
   check whether the compactor's `$falseKeys` / `$emptyArrayKeys` / `$deadSystemKeys` would remove
   it, and whether every client read behaves identically when it is `undefined`
   (`=== false` does **not**).
9. **`empty array()` encodes as JSON `[]`, not `{}`** — the `$edfHexes` map must never be emitted
   empty as an array where the client expects an object.
10. **Render-loop idle gating** — any scene mutation outside the animation list needs
    `requestRender()`. The SCT arcs will silently not draw.
11. **`splitShots` `$guns` padding** must skip manual `intercept` orders (Slicer, game 4306).
12. **Filename must equal class name** or the ship silently does not exist.
13. **Regenerate autoload** (`fvbuild.ps1 -Autoload`) after every new class; never hand-edit
    `source/autoload.php`. Regenerate **statics** whenever something reaches the client through the
    blueprint rather than gamedata.
14. **Never commit the two `.legacy.bundle.js` files.**
15. **PHP's `round()` is not JS's `Math.round()` — in TWO ways.** Half away from zero
    (`round(-2.5) === -3`), *and* a pre-round to `14 - floor(log10|v|)` decimal places that turns
    `-20.49999999999999644` into `-20.5`. Any JS port of a PHP geometry routine needs both;
    `mathlib.phpRound` (Stage 4) is the reference, and the only way to know is a differential
    corpus generated by the PHP side.
16. **A new gamedata-LEVEL field reaches the client ONLY if `gamedata.js parseServerData()`
    copies it BY NAME.** Publishing it from `stripForJson` is half the job. It cost the CPD's
    whole client half a day (§3.4); `edfHexes` carries the same line for the same reason.
17. **A `oneturn` critical must NOT carry an `$outputMod`.** `ShipSystem::effectCriticals()` sums
    `outputMod` across every critical with **no turn filter** (it is built for permanent
    `OutputReduced*` crits and runs once from `onConstructed`), so a one-turn crit would apply
    from the turn it was rolled and then for ever. Read a per-turn magnitude through
    `sumCriticalParam()` in the system's own `getOutput()` instead — Stage 4b.
18. **`sumCriticalParam($type, $turn = false)` defaults on `=== false`, NOT on null.** A method
    with a `$turn = null` parameter that passes it straight through makes every turn comparison
    fail and reads 0, silently — while the same method called with an explicit turn is correct.
19. **`boostEfficiency` is the EXTRA POWER one boost level costs, not a flag and not an
    efficiency.** `power.js countBoostReqPower` / `countBoostPowerUsed` multiply by it, so `0`
    means the boost is **free**. A system that wants "double power" must set it to its own
    `$powerReq` — and since that is normally a constructor argument, it cannot be a declared
    property (Stage 5, found on the EDF).
20. **An enhancement that must not change a DERIVED number should spend the derived number's own
    input down, not clamp the result.** `EDF_RANGE` raises `radius` and lowers `boostRadiusBonus`
    by the same amount, so "the double-power radius does not change" holds with no second stored
    value and nothing left to keep in step (Stage 5, §3.5).
21. **A per-system enhancement's numbers must be kept OUT of prose `data` entries.** The lobby has
    no server round trip and has to rewrite `data` itself; one short numeric key per number is a
    mirror that survives, a number inside a paragraph is one that rots (Stage 5).
22. **A hull-age comparison needs a floor as well as a direction.** `>= 3 && > $ship->factionAge`,
    never one or the other — the ~40 `factionAge = 2` weapon classes make the difference invisible
    in every unit test and visible only in a corpus differential (Stage 5, §3.3).
23. ⭐⭐ **ANYTHING IN `TacGamedata::onConstructed()` THAT READS A NUMBER AN ENHANCEMENT CAN MOVE
    MUST RUN BELOW THE PER-SHIP LOOP.** `BaseShip::onConstructed()` — called *inside* that loop —
    is what applies enhancements, so the whole first half of the method sees blueprint values.
    `setBlockedHexes()` is safe up there because it reads only where ships ARE; `setEdfHexes()`
    was not, and published a refitted field at its unenhanced radius for a whole stage (§3.5).
    The comment on `markUnavailableSetMarkers()` already said this for the Chameleon gate.
24. **A stage whose numbers are equal by default cannot be tested by a unit test alone.** Without
    the refit, blueprint radius == effective radius, so every check passed on both sides of trap
    23. A recorded corpus game with the refit actually bought is the only guard — and the replay
    snapshot IS `stripForJson()`, so it pins published maps like `edfHexes` for free.
25. ⚠️⚠️ **`replayHarness.php record --games=<id>` REWRITES `manifest.json` FROM SCRATCH** with
    only the games it just recorded — it does not merge. The other ~130 baseline directories stay
    on disk but drop out of `check` silently, and `tests/replay/baseline/` is gitignored, so there
    is no copy to restore. It IS reconstructible without re-recording: each `snapshot_p*.json` is
    `stripForJson()` output and carries the game's `turn` and `status` **as recorded**, which with
    the on-disk report list is the whole manifest entry. Intersect with `discoverGames()`'s query
    (~90 of the directories are dead games that were never in the manifest), and fall back to the
    DB row for a baseline that recorded a `HARNESS-ERROR` and so has no snapshot to read.

---

## 6. Open questions — ALL RESOLVED

Kept as the record of what was asked and why; the rulings are D5–D10 in §0 and are folded into the
sections they affect.

**Q1 — EDF and concealment. → D5, out of scope.** The Walkers have no stealth function, so the
question is academic. §2.1 records what would make it live again and where the guard would go.

**Q2 — Wide-Beam declaration timing. → D6, firing mode in the Fire phase.** §3.3.

**Q3 — EDN corridor choice. → D8, deterministic with a player-preferring tie-break.** Prefer the
corridor containing an enemy unit, then the one adding the most new field, then lowest `(q, r)`
for stability. §3.7.

**Q4 — CPD adaptation scope. → D7, the raw `faction` string.** No faction families. §3.4.

**Q5 — Control sheets. → D4, supplied as a Walker test ship per system.** Each stage opens when the
user lands a hull carrying a basic version of that stage's system; the stats are then read out of
the hull file rather than transcribed. Stage 3 varied it — the sheet arrived as a per-firing-mode
stat table rather than a hull, which worked just as well. Still needed across the whole plan:
damage / range / fire-control / power / RoF for the remaining weapons, radii and power for the three
field systems, EW-Detector range, and point costs throughout.

**Q6 — CPD adaptation vs the Torvalus Shading Field (asked 2026-09-03). → D9.** Adaptation eats into
the field's whole defensive contribution, shaded doubling included, and never touches its
stealth/detection mechanics. §3.4 — the bucket already does exactly this, so no code was needed.

**Q7 — What does this cost every other game? (raised 2026-09-03). → D10.** One static boolean and no
autoload. §3.4 carries the table of what an ordinary game actually pays, and the gate has its own
load-bearing test.

---

## 7. What this plan deliberately does not do

- **No database schema change.** Everything persists through `tac_critical.param`,
  `tac_individual_notes` and `tac_systemdata`, all of which already exist.
- **No new phase**, no change to the turn loop.
- **No new plotting engine** — D2's design reuses `doMultipleHexFireOrders`.
- **No rewrite of the Gravitic Mine geometry** — Stage 0 moves it unchanged and proves it with the
  harness.
- **No blanket removal of the `factionAge` enhancement gate** — §3.3 narrows it instead, so no
  existing Ancient hull gains an enhancement it does not have today.
