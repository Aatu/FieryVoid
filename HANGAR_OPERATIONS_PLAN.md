# Hangar Operations — Implementation Plan

Add Babylon 5 Wars §10.1 hangar bay operations (launch / land fighters and shuttles in‑game) to Fiery Void with minimal blast radius on existing fighter handling.

> Test container: gameID `3730` (`TacGamedata::$safeGameID`). All staged work should be exercised here before being made available to live games.

---

## 0. Guiding constraints

- **Don't break existing fighters.** Hundreds of ship files set `$this->fighters = array(...)`. The current behaviour (fighters chosen in the lobby + deployed before turn 1 as separate `FighterFlight` ships) must remain the default. Hangar Ops layers on top of it.
- **Reuse the spawn‑in‑play pattern.** `BallisticMineLauncher` already spawns `spawnCaptor*` mines at runtime via `Manager::insertSingleShip` + `Manager::insertSingleMovement` + `IndividualNote` for replay (see [missile.php:2352](source/server/model/weapons/missile.php#L2352-L2412) `createLoiteringMine`). We mirror that flow for fighter launch.
- **Reuse the system‑level note pipeline.** `ShipSystem::generateIndividualNotes` / `saveIndividualNotes` / `onIndividualNotesLoaded` are already wired through `FighterFlight` and ordinary ships. The hangar's runtime state (which craft it currently holds) is persisted via these hooks, exactly like spawned mines store their `spawned` turn.
- **Don't hand‑edit `source/autoload.php`.** New classes (Shuttle, possibly per‑faction shuttle subclasses) need autoloader entries — append by the same convention used for other new classes, but treat that as a separate, mechanical step at the end of each stage.
- **No bundle commits.** `game.legacy.bundle.js` and `gamelobby.legacy.bundle.js` regenerate on deploy; never include them in PRs.
- **Stage with safety in mind.** Each stage below has a clear "rollback by reverting commit" boundary. Until Stage 7 ships, **launching/docking is gated to `safeGameID`** (or feature‑flag in `varconfig.php`).

---

## 1. Existing foundations we'll lean on

| Need | Existing primitive | File |
|---|---|---|
| Mid‑game spawn of a new ship | `Manager::insertSingleShip()` | [Manager.php:1489](source/server/controller/Manager.php#L1489) |
| Mid‑game movement order for the new ship | `Manager::insertSingleMovement()` | [Manager.php:1479](source/server/controller/Manager.php#L1479) |
| Per‑system persisted state across turns | `ShipSystem::generateIndividualNotes` / `onIndividualNotesLoaded` | [ShipSystem.php:874](source/server/model/systems/ShipSystem.php#L874-L897) |
| End‑of‑turn hook on a system | `ShipSystem::criticalPhaseEffects($ship, $gamedata)` | [ShipSystem.php:219](source/server/model/systems/ShipSystem.php#L219-L285) |
| Fighter flight wrapper | `FighterFlight` | [FighterFlight.php](source/server/model/ships/FighterFlight.php) |
| Hangar today (very thin) | `Hangar` | [baseSystems.php:2943](source/server/model/systems/baseSystems.php#L2943-L2959) |
| "Static blueprint preload" for runtime‑spawned classes | `$spawnableClasses` array on a system; consumed by `game.php:86` | [game.php:86](source/public/game.php#L86-L102) and [missile.php:2220](source/server/model/weapons/missile.php#L2220-L2224) |
| End‑of‑turn carrier checks | `Movement::isPivoting`, `Movement::isRolling` static helpers | [movement.php:51,155](source/server/handlers/movement.php#L51) |
| Per‑ship turn deploy check | `BaseShip::getTurnDeployed`, `BaseShip::$spawned` | [ShipClasses.php:37](source/server/model/ships/ShipClasses.php#L37) |
| Confirm dialog UI | `window.confirm` (`getMissileOptions`, `showConfirmOkCancel`, etc.) | [confirm.js](source/public/client/UI/confirm.js) |
| Tooltip button menu pattern | `ShipTooltipMenu`, `ShipTooltipFireMenu` (button list + per‑button `condition`/`action`) | [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js), [shipTooltipMenu.js](source/public/client/UI/shipTooltipMenu.js) |
| Hex deployment UI | `DeploymentPhaseStrategy.js`, `MineDeployment.js` | [DeploymentPhaseStrategy.js](source/public/client/renderer/phaseStrategy/DeploymentPhaseStrategy.js) |

The mid‑game mine‑spawn flow [missile.php:2352-2412](source/server/model/weapons/missile.php#L2352-L2412) is the closest analogue and should be the **structural reference** for fighter launch — it does all of: instance a new ship, INSERT it, set `->spawned`, write a starting movement order, init system data, and write a replay note. Read it once before starting Stage 4.

---

## 2. Data model overview (the things we add)

### 2.1 New properties on `Hangar` (and `Catapult`, since it's the same shape)

```php
class Hangar extends ShipSystem {
    public $hangarType   = 'fighters';   // matches FighterFlight->hangarRequired ('fighters'|'medium'|'heavy'|'superheavy'|'assault shuttles'|'Breaching Pods'|'minesweeping shuttles' …)
    public $direction    = 0;            // 0..5, hex direction OFFSET added to carrier's facing on launch
    public $hangarUsage  = [];           // see §2.2 — runtime‑mutated; persisted via individualNotes
    public $spawnableClasses = [];       // populated by ship file with phpclasses of every craft this hangar can hold
    public $launchedThisTurn = 0;        // resets each turn
    public $landedThisTurn   = 0;        // resets each turn
    // Per the rules, $output is the SHARED launch+land budget per turn:
    // (launchedThisTurn + landedThisTurn) <= $output. A 6-output hangar can
    // launch 6, OR land 6, OR any split (4 launch + 2 land, etc.).
    private $usagePopulated = false;     // §2.2 idempotency guard
}
```

`Catapult` should subclass / inherit the same fields — it behaves like a fast‑launch hangar. Keep its existing flavour but reuse the launch path.

### 2.2 `$hangarUsage` shape

`$hangarUsage` is an **ordered list of stored‑craft records**:

```php
[
    ['phpclass' => 'starfuryEA',         'name' => 'Aurora-1', 'flightSize' => 6, 'hangarType' => 'medium'],
    ['phpclass' => 'shuttleEA',          'name' => 'Shuttle',  'flightSize' => 1, 'hangarType' => 'shuttles'],
    ['phpclass' => 'breachingPodEA',     'name' => 'Lamprey',  'flightSize' => 2, 'hangarType' => 'Breaching Pods'],
]
```

- Capacity used by category = `array_sum(flightSize where hangarType == X)`.
- Free capacity by category = `($ship->fighters[$category] ?? 0) - usedInCategory`.
- Slot accounting matches exactly what `$ship->fighters` declares, so existing fleet checks keep working.

### 2.3 Initial population (one carrier → many hangars)

The user's design says "populate `$hangarUsage` once per ship, presumably on the first hangar that runs `onIndividualNotesLoaded()` for that ship".

Implementation:

```php
public function onIndividualNotesLoaded($gamedata) {
    parent::onIndividualNotesLoaded($gamedata);

    if ($this->usagePopulated) return;
    $ship = $this->getUnit();
    if (!$ship) return;

    // Idempotency: if any hangar on this ship already has stored entries, skip.
    foreach ($ship->systems as $sys) {
        if ($sys instanceof Hangar && !empty($sys->hangarUsage)) {
            $this->usagePopulated = true;
            return;
        }
    }

    HangarOps::populateInitialHangarUsage($ship, $gamedata);
    $this->usagePopulated = true;
}
```

`HangarOps::populateInitialHangarUsage` (a new helper class — Section 4.1) walks the ship's hangars in order and fills them from `$ship->fighters` plus the deployed `FighterFlight`s already spawned for that ship — i.e. only fighters that **haven't already deployed** consume hangar slots; the rest of the capacity is filled with shuttles (Section 5).

### 2.4 New `Shuttle` class hierarchy

```
FighterFlight
  └─ Shuttle                  ($shuttle = true; $isCombatUnit = false; $maxFlightSize = 6)
       ├─ ShuttleEA
       ├─ ShuttleCentauri
       ├─ ShuttleNarn
       ├─ … one per faction that auto‑populates …
       └─ MinesweepingShuttle   (overrides icon + adds minesweeping, $hangarRequired = 'minesweeping shuttles')
```

Shuttles are stored ONE-per-record in `$hangarUsage` (each record has `flightSize = 1`),
but when LAUNCHED can form a flight of 1‑6 craft (selectable in the launch dialog,
pulling N records out of the hangar to spawn a single FighterFlight with `flightSize = N`).

`Shuttle` lives in `source/server/model/ships/Shuttle.php` (root, like `FighterFlight.php`). Per‑faction subclasses go in each faction directory as needed.

`Shuttle` sets `$this->isCombatUnit = false` (existing flag on `BaseShip`, defaults `true`; many civilian/freighter ships already use it — see `civilians/skylark.php`, `centauri/emperorsTransport.php`). That keeps shuttles out of fleet point totals and battlegroup checks. We'll also add a small `$shuttle = true` marker for code paths that need to distinguish shuttles from non‑combat freighters.

### 2.5 New initiative criticals

Two new one‑turn critical effect classes in `cricialClasses.php`:

- `LaunchedThisTurn` — `-50` initiative, expires after 1 turn. Applied to the launched `FighterFlight`'s C&C/representative system (or directly on the `FighterFlight` via a critical on its `RammingAttack` system, which every fighter has).
- `JustLaunched` — `-20` initiative, expires after 1 turn. Applied to the carrier's CnC.

Both are read in `BaseShip::getInitiativebonus()` paths.

---

## 3. Server-side flow summary

### Launching (Firing Phase tooltip → resolved end‑of‑turn)

1. Player clicks **Launch** button on hangar ship in Firing Phase.
2. Confirm modal shows: per‑hangar dropdown of available stored fighters/shuttles, count to launch, target hex (default = carrier hex), facing (default = carrier facing + hangar `$direction`). Shuttles can be launched in flights of 1–6 (the dialog should expose the size selector).
3. Client posts a fire‑order‑adjacent payload (a new `IndividualNote` of kind `"hangarLaunch"`) attached to the chosen hangar system.
4. End‑of‑turn (`Hangar::criticalPhaseEffects`):
   - For each `hangarLaunch` note staged this turn:
     - Validate (still has stored craft? carrier didn't pivot/roll? `(launchedThisTurn + landedThisTurn + thisLaunchSize) <= $output`?). If invalid, drop and add a pubnote.
     - Decrement `$hangarUsage` for that craft record.
     - Spawn a new `FighterFlight` of the right phpclass via `Manager::insertSingleShip`.
     - Insert a `MovementOrder` at the carrier's last hex with `heading = carrier->heading`, `facing = carrier->facing + hangar->direction (mod 6)`, `speed = carrier->speed`, type `"deploy"`.
     - Set `$flight->spawned = $gamedata->turn`.
     - Apply `LaunchedThisTurn` crit to the new flight; apply `JustLaunched` crit to the carrier (idempotent — once per carrier per turn).
     - Write replay note ("X launched from Y").

### Landing (Firing Phase tooltip → resolved end‑of‑turn)

1. Player clicks **Dock** button on a `FighterFlight` in Firing Phase.
2. Client identifies eligible carriers: same hex, same `$heading`, `$speed >= flight->speed`, `flight->speed <= ship->speed + flight->thrust` (per rules), carrier hangar has free slot of compatible `hangarType`, carrier didn't pivot/roll, hangar not destroyed, and the hangar's shared `$output` budget for the turn (`launchedThisTurn + landedThisTurn`) has enough headroom for the incoming flight.
3. If multiple eligible carriers → **`SelectFromShips`** picker (already exists, used for targeting; reuse pattern).
4. If hangar(s) on chosen ship can't hold the full flight → split confirmation: "Split this flight across hangars / leave N fighters in space?"
5. Client posts `hangarDock` `IndividualNote` on the receiving hangar system.
6. End‑of‑turn (`Hangar::criticalPhaseEffects`):
   - Validate eligibility again server‑side.
   - For each fighter in the flight that's docking, append a record into `$hangarUsage` (`{phpclass, name, flightSize: 1}` per craft, or one record with the post‑landing `flightSize` if they all go to the same hangar).
   - Mark the flight ship destroyed‑equivalent. Cleanest path matching existing patterns: set the flight's primary structure to a "removed from play" state. **Recommendation: a new `removed` flag on `BaseShip`** set by the docking handler — checked everywhere that currently checks `isDestroyed()` for purposes of UI rendering / target lists. (Setting `isDestroyed` directly is wrong because we don't want a destruction explosion.)
   - Write replay note.

### Initial deployment (Deployment Phase)

1. Player can opt to start a fighter flight inside a hangar instead of in a hex.
2. New deployment‑phase tooltip on a hangar ship: "Dock pending flight here". UI presents flights belonging to the same slot/team that haven't been placed yet.
3. Selection writes a `hangarUsage` entry on the hangar (via a `hangarDeployStart` note) and removes the flight from the deployment grid.
4. Reinforcement case: if the carrier was deployed in a previous turn, the reinforcement flight can deploy directly into a hangar via the same UI, also routed through `hangarDeployStart`.

---

## 4. Stage‑by‑stage execution plan

Each stage is a single‑PR worth of work, independently mergeable to `master` once tested on `safeGameID`.

### Stage 1 — Hangar capacity tracking (no UI yet)

**Goal:** add the data model, persist state across turns, populate from existing `$ship->fighters` at game start. Visible only via debug logs / DB inspection.

Files to touch:
- [baseSystems.php:2943](source/server/model/systems/baseSystems.php#L2943) — `Hangar` class: new fields, `onIndividualNotesLoaded`, `generateIndividualNotes` (writes `hangarUsage`), `criticalPhaseEffects` skeleton.
- New `source/server/model/systems/HangarOps.php` — static helper class. Encapsulates: capacity math, slot search (which hangar fits this craft), category bucketing, initial population from `$ship->fighters`.
- `source/autoload.php` — append `HangarOps` entry.
- Mirror the new fields on the **client side** in `source/public/client/model/system/baseSystems.js` so the JSON round‑trips and the client can display capacity in the system info window.
- New React fragment in `source/public/client/UI/reactJs/system/SystemInfo.js` (or extend the existing system info renderer) to display "Carrying: 6/12 medium, 2/2 shuttles" inside the Hangar tooltip.

What to verify on Docker after Stage 1:
- Open a carrier in `safeGameID` → its hangars list current usage matching `$ship->fighters`.
- Refresh; usage persists. Run a turn end without doing anything — usage still persists.
- A ship with two hangars only populates once (no duplicate accounting).

**Rollback:** revert the commit; nothing else depends on it yet.

### Stage 2 — `Shuttle` class + auto‑fill empty hangar slots

**Goal:** any unfilled hangar capacity (from §2.2) gets an auto‑populated shuttle record. Minesweeper ships get minesweeping shuttles. No launch yet — shuttles just sit in the data model.

Files:
- New `source/server/model/ships/Shuttle.php` (base class + `$nonCombatant = true`, `$shuttle = true`, `$maxFlightSize = 1`).
- New per‑faction `Shuttle*.php` files (start with EA, Centauri, Narn, Minbari — extend later).
- New `MinesweepingShuttle.php` (subclass of Shuttle that adds the minesweeping system; matches existing `"minesweeping shuttles"` category used by Orieni `vigilant` ships).
- `HangarOps::populateInitialHangarUsage` extended: any leftover capacity becomes shuttles, and if `$ship->minesweeperbonus > 0` (or `$ship->fighters` already declares `"minesweeping shuttles"`) replace shuttle phpclass with minesweeping variant for that count.
- `source/autoload.php` — append new classes.

Notes:
- `BaseShip::$minesweeperbonus` exists ([ShipClasses.php:43](source/server/model/ships/ShipClasses.php#L43)) — use it. Fall back to `"minesweeping shuttles"` key in `$fighters` for ships like the Orieni Vigilant that declare the type explicitly.
- Don't add shuttle classes to fighters/`spawnableClasses` of every ship's hangars yet — that comes when launch lands in Stage 4.

What to verify after Stage 2:
- Vigilant‑class ship now reports its 6 minesweeping shuttles in `$hangarUsage`.
- Decurion (24 assault shuttles) reports 24 entries in correct categories.
- Standard carrier with `array("medium" => 12)` and 14 hangar boxes gets 2 shuttles auto‑added.
- Combat value calculation (already invoked in fleet list / damage allocation) still works.

### Stage 3 — Hangar damage destroys docked craft

**Goal:** when a hangar takes damage, decrement `$hangarUsage` 1 per damage point. Priority: empty/shuttle slots first, then cheapest fighter, then most expensive. Already‑destroyed hangar boxes don't get new fighters allocated to them.

Files:
- `Hangar::onConstructed` (or override of `addDamage` — investigate which hook fires per box of damage) → call `HangarOps::onHangarDamage($hangar, $damageThisTurn)`.
- `HangarOps::onHangarDamage`: walk `$hangarUsage`, sort by priority (shuttles cheapest → fighters by `pointCost` ascending), pop until `count(usage) <= remainingHealth`.

What to verify:
- Damage a hangar in `safeGameID`. Stored craft count drops correctly.
- Replay shows the hangar damage and storage drop in pubnotes.

### Stage 4 — Launch (Firing Phase)

**Goal:** end‑of‑turn launch a flight from a hangar. No initiative penalty yet (Stage 6). Limited to `safeGameID` via gate.

#### 4a — server

- `Hangar::criticalPhaseEffects` reads pending `hangarLaunch` notes, validates, calls `HangarOps::performLaunch`.
- `HangarOps::performLaunch($hangar, $craftRecord, $count, $gamedata)`:
  - Mirror `BallisticMineLauncher::createLoiteringMine` ([missile.php:2352](source/server/model/weapons/missile.php#L2352-L2412)) closely:
    - `new {$craftRecord['phpclass']}($gameid, $userid, $name, $slot)` — the carrier's slot/userid carry through.
    - `Manager::insertSingleShip($gamedata, $newFlight, $userid)`.
    - `$newFlight->spawned = $gamedata->turn`.
    - `MovementOrder` of type `"deploy"` at carrier's last hex, with carrier's heading and `(carrier->facing + hangar->direction) % 6`, speed = carrier speed.
    - `Manager::insertSingleMovement($gamedata->id, $newId, $deployMove)`.
    - Initial system data via `SystemData` (same as mine spawn).
    - Replay note: `"$flightname launched from {$ship->name}"`.
  - Decrement hangar `$launchedThisTurn`.

#### 4b — `$spawnableClasses` registration

Each hangar that can launch craft must list those phpclasses in `$spawnableClasses` so `game.php:86` preloads the static blueprints into `window.staticShips`. This happens in the **carrier's ship file** because the hangar instance is created there with knowledge of which fighters it can produce. Recommended pattern:

```php
// inside a ship's __construct, after addPrimarySystem(new Hangar(…)):
$hangar->spawnableClasses = ['starfuryEA','shuttleEA'];
```

Or, more cleanly, accept a 4th constructor arg:
```php
$this->addPrimarySystem(new Hangar(0, 14, null, ['starfuryEA','shuttleEA']));
```

Pick one convention and use it everywhere. The first form is less invasive (no constructor signature change).

#### 4c — client UI

- New tooltip button on hangar ships in Firing Phase. Add to `ShipTooltipFireMenu.buttons` (a new button object with `condition: [isMyShip, hasHangarWithSpace, hasStoredCraftLaunchable, notRolling, notPivoting]`, `action: openLaunchDialog`).
- `openLaunchDialog` opens a confirm dialog (extend `window.confirm` with a new `showHangarLaunch` method or build a sibling). Inside:
  - Per‑hangar list of stored craft.
  - Per‑craft "launch how many?" stepper, capped by `flightSize` (full flight) or by partial‑flight rules (1‑3 SHF, 1‑2 BPod, 1 shuttle).
  - OK button posts a `hangarLaunch` `IndividualNote` payload (server‑side: a regular `IndividualNote` written by the hangar's `generateIndividualNotes`).
- Client validation mirrors server's; server is authoritative.

What to verify:
- In `safeGameID`, click Launch on a carrier, choose 6 medium fighters, confirm.
- End the turn. New flight appears in the carrier's hex moving same heading, at carrier's facing + 0.
- Replay shows the launch note.
- `$hangarUsage` decreased correctly.
- Repeat with two flights launched in same turn from different hangars on the same ship.
- Try launching while pivoting → button disabled.

### Stage 5 — Land (Firing Phase) ✓ COMPLETE

**Goal:** end‑of‑turn dock a flight into a hangar. Still gated to `safeGameID`.

Files:
- `HangarOps::canShipReceive($carrier, $flight, $turn, $gamedata)` — applies all rules (same hex, same heading, speed within flight thrust, hangar not destroyed, free slot in compatible category, not pivoting/rolling).
- `HangarOps::performLand`:
  - Append record(s) to `$hangarUsage`.
  - Mark the flight `removed = true` (new flag on `BaseShip` — see §3 Landing). UI hides removed ships; combat value calc skips them; they stop appearing in target lists. **But** their record is preserved in DB so the replay can show them existing up to the docking turn.
  - Write replay note.
- `BaseShip::$removed` (default `false`) + `BaseShip::isOnBoard()` helper, used everywhere we currently care about "the ship is in play" (target list, deployment, EW, etc.). Easier than adding `removed` checks everywhere — refactor a few sites to call `isOnBoard()` instead of `!isDestroyed() && !isDisengaged()`.

Client UI:
- New tooltip button on `FighterFlight` in Firing Phase: **Dock**.
- `condition: [isMyShip, isFighterFlight, eligibleCarrierInHex]`. `eligibleCarrierInHex` enumerates carriers in the same hex that satisfy heading/speed/hangar rules.
- If exactly one carrier eligible: click → confirm dialog ("Dock this flight in <Ship>?"). If multiple: open `SelectFromShips` picker first.
- If a single carrier can take only part of the flight, dialog offers "Split: dock N here, leave M in space" (or "Split across hangars on this ship" if the ship has more than one hangar).
- OK posts a `hangarDock` `IndividualNote` on the receiving hangar.

What to verify:
- Friendly carrier in same hex, same heading, valid speed → Dock button appears.
- Dock; end of turn → flight disappears, hangar usage shows the new entries.
- Carrier in same hex but different heading → Dock disabled.
- Two friendly carriers in same hex → `SelectFromShips` opens.
- Damaged hangar (1 box) trying to dock 6 fighters → split dialog.

### Stage 6 — Initiative penalties ✓ COMPLETE

Two new criticals added to `cricialClasses.php`, both using `turnend = turn + 1` (explicit expiry, not the legacy `$oneturn` marker):

- `HangarOperations` — applied to carrier CnC, −20 initiative next turn. Covers both launch **and** dock events; idempotent within a turn so launching + docking in the same turn still yields exactly one −20.
- `LaunchedThisTurn` — applied to the launched flight's first fighter, −50 initiative next turn.

Both wired into `BaseShip::getCommonIniModifiers` (checked via `hasCritical()`). Both autoload entries added (`hangaroperations`, `launchedthisturn`).

`HangarOps` changes:
- New private `applyHangarOperationsCrit($carrier, $gamedata)` — scans CnC criticals for a same-turn `HangarOperations` before adding, ensuring dedup across multiple launch/dock calls in one turn.
- New private `applyLaunchCrits($flight, $carrier, $gamedata)` — applies `LaunchedThisTurn` to flight's first fighter then delegates to `applyHangarOperationsCrit`.
- Both `performLaunch` paths (new-spawn and resurrected-flight) call `applyLaunchCrits`.
- `performLand` calls `applyHangarOperationsCrit` directly (no flight-side penalty on landing).

What to verify:
- Turn after launch: launched flight shows ‑50 initiative; carrier shows ‑20.
- Turn after dock (no launch): carrier shows ‑20; no flight penalty.
- Turn after both launch and dock from same carrier: carrier shows exactly one ‑20.
- Two turns later: all penalties gone.
- Launch two flights from same carrier in one turn → carrier gets exactly one ‑20.

### Stage 7 — Deployment‑phase launching/docking ✓ COMPLETE

**Goal:** before the game starts, a player can park flights inside hangars instead of placing them on the map. Reinforcements arriving on later turns can do the same.

Files:
- `source/public/client/renderer/phaseStrategy/DeploymentPhaseStrategy.js` — add a "Dock here" tooltip on hangar ships when a same‑slot fighter flight is selected for deployment.
- `DeploymentGamePhase::process` — when a `hangarDeployStart` note is present on a flight, suppress its normal deployment movement and instead append the flight to the carrier's `$hangarUsage`. Mark the flight `removed = true`.
- Reinforcements: same flow on later turns. Detect by `$flight->getTurnDeployed($gameData) == $gameData->turn` and the carrier already deployed.

What to verify:
- New game, carrier with 12 medium slots and a 6‑fighter flight: place carrier on map, dock the flight. Flight disappears from board; carrier hangar shows 12/12 occupied.
- Turn 1 launch from full hangar still works.
- Reinforcement scenario: carrier deployed turn 1, reinforcement flight scheduled for turn 3. On turn 3, flight can be docked into carrier directly without ever being placed.

### Stage 8 — Multi‑direction launch arrows

Until now `$hangar->direction` defaults to 0 everywhere. Stage 8 exercises it for ships with side hangars.

Convention (confirmed against `MovementOrder::getFacingAngle` + `mathlib.hexFacingToAngle`): `facing` is 0-5 mapped to 0°/60°/120°/180°/240°/300° clockwise from forward. So a starboard (right-side) hangar wants `direction = 1` (launch at carrier facing + 60°), a port (left-side) hangar wants `direction = 5` (carrier facing + 300° = 60° to port). The arc-arg conventions in the same ship files corroborate this: `addLeftSystem(new TwinArray(..., 180, 0))` covers the 180→360 (port) arc and `addRightSystem(..., 0, 180)` covers the 0→180 (starboard) arc.

Files:
- [balvarin.php](source/server/model/ships/centauri/balvarin.php) — port hangar `direction = 5`, starboard hangar `direction = 1`. Primary hangar stays `direction = 0`.
- [balvarix.php](source/server/model/ships/centauri/balvarix.php) — same as Balvarin (it's the variant).
- [decurion.php](source/server/model/ships/centauri/decurion.php) — port hangar `direction = 5`, starboard hangar `direction = 1`.
- [confirm.js:1857](source/public/client/UI/confirm.js#L1857) `hangarLaunch` — header row now shows `(launches at: X°)` per hangar where `direction != 0`. Carrier facing is read from `ship.movement[last].facing` (locked in by Firing Phase) and combined with `hangar.direction` via `mathlib.hexFacingToAngle`. The suffix is suppressed for `direction = 0` to keep the legacy forward-launch header uncluttered.

Out of scope for Stage 8: wiring up `direction` on the many other side-hangar carriers in the fleet (Drazi Strikebird, Colonial Odin/Minerva, EA AthenaAM, etc.). Those can be picked off ship-by-ship as a polish pass; the mechanism is in place.

What to verify:
- Balvarin (test ship): primary hangar launches forward; port hangar launches at carrier facing + 5 (visible in the dialog header); starboard at carrier facing + 1.
- Decurion launches assault shuttles → they appear facing perpendicular to carrier.

### Stage 8.5 — Player‑selectable launch direction (multi‑direction hangars) ✓ COMPLETE

A single hangar can now advertise more than one allowed launch offset, and the player picks per launch from a dropdown in the launch dialog. Use this for carriers whose bay opens onto multiple arcs (e.g. EA Hyperion: ports out either side).

Convention: `Hangar->directions` is an array of int offsets (0..5). When non‑empty:
- The launch dialog renders a `<select>` next to the per‑hangar header label showing the resulting world facing for each option.
- The player's pick is attached to every order entry from that hangar as `{phpclass, size, direction}` and round‑trips through the `hangarLaunchOrder` note.
- `HangarOps::processLaunchOrders` validates the override is one of the allowed offsets (defence against stale/forged payloads) and passes it to `performLaunch` as a 6th arg, where it replaces `$hangar->direction` for the spawn facing.
- Carrier‑destruction escape picks `directions[0]` automatically (player can't choose — carrier is gone), so a side‑launch bay doesn't eject escapees forward.

The legacy single‑value `$direction` field is unchanged and still works for fixed‑arc hangars (Balvarin/Decurion). `$directions` overrides it only when non‑empty AND the player actually picked.

Files:
- [baseSystems.php](source/server/model/systems/baseSystems.php) Hangar — new `public $directions` field, `stripForJson` ships it, `doIndividualNotesTransfer` preserves per‑entry `direction` in the sanitised launch payload.
- [HangarOps.php](source/server/model/systems/HangarOps.php) — `performLaunch` takes optional `$directionOverride`; `processLaunchOrders` validates entry direction against `$hangar->directions` before forwarding; escape spawn defaults to `directions[0]` when present.
- [confirm.js](source/public/client/UI/confirm.js) `hangarLaunch` — per‑hangar `<select>` rendered when `hangar.directions.length > 1`, pre‑filled from any prior `pendingLaunchOrders[i].direction`; pick stashed in a `hangarDirChoice` Map and attached to every order entry on OK.
- [hyperion.php](source/server/model/ships/EA/hyperion.php) — primary hangar `directions = [1, 5]` (example carrier).

What to verify:
- Hyperion launch dialog shows a dropdown next to "Main Hangar" with two options (carrier facing + 60° and + 300°).
- Picking one and submitting → fighters spawn facing that direction at end of turn.
- Re‑opening the dialog later in the same phase pre‑selects the previously chosen direction.
- Hyperion destroyed with docked craft → escape pods exit at `directions[0]` (port side), not forward.

### Stage 9 — Polish & non‑critical edge cases ✓ COMPLETE

- Fleet list: a docked flight (`removed = true`) is rendered as a blue "Docked" row instead of the red "Destroyed" label it inherited from the Stage 5 `isDestroyed` folding. New `.fleetlistentry .docked` CSS rule mirrors `.destroyed`/`.jumped` styling.
- Fleet list: a docked flight whose fighters are all disengaged (combat value 0 — i.e. it lost its identity in a partial relaunch) is hidden entirely. Anonymous orphan stash records carry its remaining value on the carrier row instead.
- Carrier fleet-list value now includes the point cost of every anonymous `hangarUsage` entry (records without `dockedFlightId`). Auto-filled shuttles have `pointCost = 0` and contribute nothing; orphans created by partial relaunches and Stage-7 dock entries get credited.
- Source-flight sync on **partial new-spawn launch**: when `resurrectDockedFlight` rejects a partial / cross-record split, the new helper `syncSourceFlightsOnLaunch` walks every stash entry the launch will touch and disengages `$take` (not all) active fighters in each `dockedFlightId`-linked source flight. The link is **preserved** so `Hangar::onIndividualNotesLoaded` keeps re-applying `$removed = true` on the source flight across turn loads — without this, the source flight unflagged itself on the next load and resurfaced in the fleet list as a "destroyed" ghost row. A future exact-size relaunch can still resurrect it.
- Partial-launch flight naming: `splitFlightNameFor` checks for a matching `dockedFlightId` entry and names the new flight `"<source name> - Split"` (e.g. `"Sentri #3 - Split"`) so the player can see which docked flight the detachment came from. Falls through to the generic `phpclass`/`shipClass` name for anonymous-orphan or auto-filled-shuttle launches.
- Source-flight sync on **hangar damage eviction**: `evictCraftFromHangar` now takes `$gamedata` so it can disengage `$take` fighters in the source flight whenever it reduces a `dockedFlightId` entry. `onHangarCriticalPhase` plumbs `$ship`/`$gamedata` through from `Hangar::criticalPhaseEffects`, and the total-loss branch (`$hangar->isDestroyed()`) disengages every fighter in every linked source flight before clearing `hangarUsage`. Before this, hangar damage destroyed stored craft on paper but the docked flights kept their full combat value forever.
- `HangarOps::isFlowEnabled` and `shipTooltipFireMenu.js::isLaunchEnabledGame` both ungated — kept as stubs returning `true` so a future per-game feature flag can re-gate without touching the call sites.

Files: [HangarOps.php](source/server/model/systems/HangarOps.php), [baseSystems.php](source/server/model/systems/baseSystems.php), [fleetList.js](source/public/client/UI/fleetList.js), [tactical.css](source/public/styles/tactical.css), [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js).

What to verify:
- Carrier with full Stage-7 deployment dock → fleet list shows carrier row at carrier pointCost, separate "Docked" row(s) at the flight's pointCost in blue.
- Hangar damage evicts 2 craft from a `dockedFlightId` entry → the docked flight's row drops to ~67% combat value (2 of 6 fighters now disengaged).
- Partial relaunch of 3 from a stash entry of 6 with `dockedFlightId` → source flight row disappears (all 6 fighters disengaged), new flight shows half pointCost, carrier row gains half pointCost (3 anonymous orphans). Net fleet value unchanged.
- Total hangar destruction → all `dockedFlightId`-linked flights show 0 combat value (or vanish from the list if hidden).
- Open any non-safeGameID game with a carrier → launch/dock buttons available; orders go through end-of-turn.

### Stage 10 — Damage-aware docking (4 issues)

Four related polish items that all centre on **fighter state at the moment of dock**: ordering the critical-roll vs hangar-ops pipeline so doomed fighters don't get docked; showing scheduled docks in the carrier's hangar tooltip during the Firing Phase; choosing *which* fighters dock first when a partial-dock is forced; and preserving per-fighter damage across a partial dock + relaunch cycle.

The four can be implemented and merged independently. Recommended order is **10.1 → 10.2 → 10.3 → 10.4** because 10.4's design is the most invasive and benefits from the visibility and ordering fixes landing first, but no item is blocked on another.

---

#### 10.1 — Fighter dropout/destruction must resolve before dock ✓ COMPLETE

**Symptom (the bug).** A fighter that dropped out (or was destroyed) during the Firing Phase can still appear in the carrier's hangarUsage at end of turn, because `Hangar::criticalPhaseEffects` may fire *before* the source flight's fighter dropout rolls have run.

**Root cause.** [Criticals::setCriticals](source/server/handlers/criticals.php#L6-L96) is a single nested loop:

```
foreach ships:
    foreach systems on that ship:
        testCritical()              // ← dropout rolls happen here for Fighter subsystems
        criticalPhaseEffects()      // ← Hangar::processDockOrders runs here on the carrier
```

Per-ship is processed end-to-end. If the FighterFlight is processed *after* the carrier in `$gamedata->ships` (ship-id order, not deterministic relative to launch order), then:

1. Carrier's `Hangar::criticalPhaseEffects` runs first → `processDockOrders` → `performLand` → `countActiveCraft()` returns 6 (none rolled yet) → 6 fighters get DockedFighter crit and stored in hangarUsage.
2. FighterFlight's `Fighter::testCritical` runs next → one fighter rolls a dropout → it now has both `DockedFighter` and `DisengagedFighter` crits, but the carrier already counted it as docked.

The current `applyFighterStateCritical` does guard against already-destroyed fighters (`if ($fighter->isDestroyed($gamedata->turn)) continue;`), but a *future* dropout in the same turn is invisible to that check.

**Fix.** Split `Criticals::setCriticals` into **two passes**:

- **Pass 1**: every ship → every system → run the testCritical block (Thruster overthrust, damaged-this-turn testCritical, Weapon force-shutdown).
- **Pass 2**: every ship → every system → run `criticalPhaseEffects` + the EngineShorted post-check.

Pass 1 applies *every* fighter dropout roll across the game before any Hangar starts moving craft around. Pass 2's `processDockOrders` then sees the final post-dropout state. `applyFighterStateCritical`'s existing `isDestroyed` guard already handles the rest — a dropped-out fighter is `isDisengaged` → `isDestroyed` returns true → it's skipped, and the dock either short-counts or fails the `canShipReceive` capacity check.

**Risk assessment.** Most `criticalPhaseEffects` implementations are independent of other ships' `testCritical` results (limpet bores, marine missions, etc.). The Hangar dependency is the only known cross-ship one. The EngineShorted post-check at criticals.php:78-89 stays paired with `criticalPhaseEffects` in Pass 2, so its semantics are preserved. **Lower-risk fallback**: re-order `$gamedata->ships` so FighterFlights process before non-FighterFlight ships, leaving the inner loop untouched. This fixes the Hangar case specifically but won't help if some future system has a similar cross-ship dependency.

**Files**: [criticals.php:6-96](source/server/handlers/criticals.php#L6-L96).

**Verify**:
- Carrier with a 6-dock order on a flight whose remaining health rolls a dropout in the same turn → dropped fighter does not appear in hangarUsage; carrier dockedThisTurn drops by 1; replay shows DROPOUT for one fighter and DOCKED for the rest.
- Total flight dropout (all 6 fail rolls) → dock order fails with `not enough active craft` fail note; no entry written.
- A flight in a flight-fighters-then-Hangar order and a flight in the opposite order both behave identically.

**Implementation notes (2026-05-17).** Both passes iterate a single `$activeShips` snapshot taken at the top of `setCriticals`. The snapshot is needed because `ConnectionStrut::testCritical` cascades double-damage to primary structure and could destroy a ship mid-execution; the legacy single-pass loop only checked `$ship->isDestroyed()` at the top of the ship loop, so a ship destroyed mid-loop still had the rest of its systems processed. Without the snapshot, the new Pass 2 would skip such ships entirely — a behaviour change. Per-system `isDestroyed && !MissileLauncher` guard preserved at the start of Pass 1's inner loop. The EngineShorted post-check stays paired with `criticalPhaseEffects` inside Pass 2's per-system body so it can react to crits added by either pass.

The desired Hangar behaviour is now consistent regardless of ship-iteration order: fighters that roll a dropout in Pass 1 are `isDocked || isDisengaged || ... → isDestroyed` by the time Pass 2's `Hangar::processDockOrders` runs, and `applyFighterStateCritical`'s existing `isDestroyed` guard skips them naturally. Files: [criticals.php](source/server/handlers/criticals.php).

---

#### 10.2 — Hangar tooltip reflects scheduled Firing-Phase docks ✓ COMPLETE

**Symptom.** During Deployment Phase, [Hangar.refreshHangarTooltip](source/public/client/model/system/baseSystems.js#L293-L372) folds `pendingDeployStartOrders` into the "Carrying" / "Stored Craft" lines so the player sees the projected post-commit state. The Firing-Phase equivalents (`pendingDockOrders`, and arguably `pendingLaunchOrders`) are not folded in — the player commits a dock and the carrier's hangar tooltip still says e.g. "0 / 12 slots" until the next turn loads.

**Fix.** Extend `refreshHangarTooltip` to also include `pendingDockOrders` in the projection (and `pendingLaunchOrders` as a *subtractive* projection: launching consumes stored craft). Both go in via the same `_pending: true` marker the deployment path already uses, so the rendering code at lines 366-371 ("(Deploying)" suffix) just needs an extra suffix variant ("(Recovering)" / "(Launching)").

- `pendingDockOrders`: look up `flight = gamedata.getShip(order.flightId)`, push a synthetic display entry `{phpclass, name, flightSize: order.count, hangarType, _pending:'recovering'}`. Use `order.count` (not flight.flightSize) because a partial dock from a 6-flight only books 3 slots.
- `pendingLaunchOrders`: subtract from the per-phpclass bucket. Easier to render as a separate "Launching: 3 x Starfury" line beneath the stored-craft listing so the count math stays obvious; alternative is a second pass through `displayEntries` that decrements matching phpclass entries before rendering.

**Refresh trigger.** Deployment refreshes via `refreshDeploymentUIForDeployStart` (called from the dialog OK handlers). The Firing-Phase tooltip needs an equivalent — call `sys.refreshHangarTooltip()` on every affected hangar from the OK handlers of `hangarLaunch`, `hangarDock`, and `hangarRecover` in [confirm.js](source/public/client/UI/confirm.js). Optionally extract a small `refreshFiringHangarTooltips(carrier)` helper for reuse.

**Files**:
- [baseSystems.js:293-372](source/public/client/model/system/baseSystems.js#L293-L372) — projection logic for pendingDockOrders + pendingLaunchOrders.
- [confirm.js](source/public/client/UI/confirm.js) — OK handlers of `hangarLaunch`, `hangarDock`, `hangarRecover`.
- Stripping path: the existing `Hangar::stripForJson` already ships `pendingLaunchOrder` and `pendingDockOrder` (singular, latest-note form). The plural client-side arrays (`pendingLaunchOrders` / `pendingDockOrders`) are hydrated from those on load; no server changes needed.

**Verify**:
- Queue a 6-fighter dock to a 12-box hangar via either dialog → hangar tooltip updates immediately: "6 / 12 slots" with "(Recovering)" suffix on the line for that flight.
- Queue a 6-fighter launch from a stored Starfury slot → tooltip projects "Carrying: 0 / 12" with a "Launching: 6 x Starfury" line.
- Re-open the dialog and cancel → tooltip reverts to the pre-queue state.

**Implementation notes (2026-05-17).** `refreshHangarTooltip` was extended to fold both `pendingDockOrders` (additive, "(Recovering)" suffix per flight) and `pendingLaunchOrders` (subtractive, rendered as a separate "(Launching)" line per phpclass beneath the stored-craft listing). The launching count is reflected in the projected "Carrying" total but stored craft lines retain their committed count — separate-line rendering keeps the math obvious ("we have 6 x Aurora stored AND 6 x Aurora are about to launch" reads more clearly than "0 x Aurora stored, 6 x Aurora outgoing"). The `_pending` marker was generalised from boolean (`true` = deploying) to string variant (`'deploying'` / `'recovering'`) so the byClass bucketing keeps deploying and recovering rows visually separate.

Refresh triggers via a new sibling helper `window.refreshFiringHangarTooltips` in [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js) — same shape as `refreshDeploymentUIForDeployStart` but skips the deployment commit-gate re-check and the `consumeGamedata` icon-visibility nudge (queued firing orders don't hide flight icons; they resolve at end of turn). Keeps the `hideSystemInfo(true)` step so an open hangar tooltip re-mounts on next hover with the projected data. Called from the OK handlers of `hangarLaunch`, `hangarDock`, and `hangarRecover` in [confirm.js](source/public/client/UI/confirm.js).

Files: [baseSystems.js](source/public/client/model/system/baseSystems.js), [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js), [confirm.js](source/public/client/UI/confirm.js).

---

#### 10.3 — Dock selection order: most-damaged-first, then end-of-array ✓ COMPLETE

**Symptom.** When a player orders a partial dock (e.g. 3 of 6) or when active-count clamps the order down, `applyFighterStateCritical` ([HangarOps.php:1131-1143](source/server/model/systems/HangarOps.php#L1131-L1143)) walks `$flight->systems` from index 0 and takes the first N active Fighter subsystems. The selection is positional — front-of-array. Players want the badly damaged fighters pulled in first, then back-of-array next.

**Rule (from the user).** Priority:
1. **Most damage** — fighters with the highest accumulated damage on the Fighter ShipSystem itself (`$fighter->getTotalDamage()`).
2. **Tiebreak** — highest array index first (later fighters in the flight before earlier ones).

**Implementation.** Refactor `applyFighterStateCritical` to:

```
candidates = []
foreach ($flight->systems as $idx => $fighter):
    if ($fighter is Fighter && !$fighter->isDestroyed($turn)):
        candidates[] = ['fighter' => $fighter, 'idx' => $idx, 'dmg' => $fighter->getTotalDamage()]

usort(candidates, [dmg DESC, idx DESC])

apply crit to first $count candidates
```

`getTotalDamage()` already exists on ShipSystem and counts the fighter's own `$damage` array (not subsystem damage). That matches the B5W dropout-roll metric, which is the right "how hurt is this fighter" proxy. Subsystem damage is excluded by design — a fighter with a damaged weapon but full hull isn't a triage candidate. (If subsystem damage *should* count too, swap in `array_sum(map(getTotalDamage, $fighter->systems))` plus the fighter's own — easy follow-up.)

**Edge case.** A fighter that already has DockedFighter or DisengagedFighter crit from earlier this turn is `isDestroyed($turn) === true` and is naturally excluded — same as today.

**Files**: [HangarOps.php:1131-1143](source/server/model/systems/HangarOps.php#L1131-L1143). One function, ~15-line change. The two callers (`dockFighters`, `disengageFighters`) are unaffected.

**Verify**:
- Flight of 6, fighter at index 0 has 2 damage and fighter at index 5 has 5 damage → partial-dock 1 selects index 5.
- Flight of 6, all undamaged → partial-dock 3 selects indices 5, 4, 3 (end-of-array first per tiebreak).
- Flight of 6 with mixed damage (idx 0: 0 dmg, idx 1: 4 dmg, idx 2: 4 dmg, idx 3: 0 dmg) → partial-dock 2 selects idx 2 then idx 1 (most damage, then highest index among ties).

**Implementation notes (2026-05-17).** `applyFighterStateCritical` now builds a candidate list with `{fighter, idx, damage}` per active Fighter subsystem, sorts by `damage` DESC then `idx` DESC, and applies the crit to the first $count. Returns the list of chosen Fighter objects so Stage 10.4 can use it for damage-copy at fragment spawn time. The hangar-damage `disengageFighters` path inherits the same priority — for stash-eviction this is the "battered fighters die first" reading; flag for review if the rules-lawyer call is "random losses." Files: [HangarOps.php](source/server/model/systems/HangarOps.php).

---

#### 10.4 — Per-fighter damage persists across partial dock + relaunch ✓ COMPLETE

**Symptom.** A *full* dock (all active craft enter the hangar) preserves damage trivially: the FighterFlight ship row survives in the DB with `dockedFlightId` linking the stash record, and `resurrectDockedFlight` ([HangarOps.php:562-580](source/server/model/systems/HangarOps.php#L562-L580)) re-uses the same row on relaunch — all damage and crits are still there because they're tied to the same fighter system rows.

A *partial* dock (say 3 of 6) doesn't get that for free: the 3 stash-bound fighters get `DockedFighter` crit on their existing Fighter rows in the **source flight**, the stash record omits `dockedFlightId` (intentional — it'd point to a flight that's still in space), and on relaunch `performLaunch` falls through to the new-spawn path, creating fresh Fighter rows with **no damage history**. The player sees an undamaged 3-craft flight pop out of the hangar even though they docked it half-dead.

**Two viable designs.** Pick one at implementation time:

**Option A — fragment flight at dock time (recommended).**
- At partial-dock time, immediately spawn a new `FighterFlight` via `Manager::insertSingleShip` holding the docking craft (the "fragment"). Re-attribute existing damage / critical DB rows from the source-flight fighter system ids to the fragment-flight fighter ids (a couple of UPDATE statements over `tac_damage` and `tac_critical`).
- The fragment flight is born `$removed = true` / `$removedTurn = currentTurn` and the stash entry carries `dockedFlightId = fragment->id`. Source flight's stash-bound fighters get `DockedFighter` crit as today, but the `dockedFlightId` on the stash entry now points to the fragment, not back at the source.
- Relaunch hits the existing `resurrectDockedFlight` path → fragment flight resurfaces with all damage/crits intact.
- **Pros**: Reuses every existing mechanism (`dockedFlightId` resurrect, `removed` flag, DB persistence). No new snapshot format.
- **Cons**: One extra ship row per partial dock; one DB transaction to re-attribute damage rows; fragment-flight `name` needs to be auto-generated ("Aurora-1 - Detachment").

**Option B — snapshot in the stash record.**
- Extend each `$hangarUsage` partial-dock entry with a `fighterStates` payload: an array of `{damage: [...], criticals: [...], systems: [{damage: [...], criticals: [...]}, ...]}` snapshotted from the source-flight fighters at dock time.
- On relaunch, after `performLaunch` creates the fresh flight, walk the saved snapshots and write the damage / critical rows under the new fighter system ids via `submitDamages` / `submitCriticals`.
- **Pros**: No extra ship row. Inspectable in the DB without joining across ships.
- **Cons**: Two new serialise/deserialise pipelines (fighter system + subsystems both have damage/crits). Has to track ship-id rewrites at restore time. JSON payload size grows for heavily-damaged stashes. Doesn't naturally fit `tac_individual_notes` if the snapshot is large (varchar(4096) — already bumped in Stage 5, but multiplicative).

**Recommended: Option A**, because the resurrect mechanism is already wired and tested. Implementation slices cleanly:

1. New helper `HangarOps::spawnFragmentFlight($sourceFlight, $count, $gamedata)` — selects the docking-priority fighters (using 10.3's order), spawns a new flight of size $count, re-attributes damage/crit DB rows from source fighter ids to fragment fighter ids, marks fragment `$removed=true`/`$removedTurn=turn`.
2. `performLand` partial branch: instead of just crit'ing source fighters, calls `spawnFragmentFlight` first, then crits the source fighters (the relationship between the two now lives in `dockedFlightId`).
3. `resurrectDockedFlight` is unchanged — it just finds a `dockedFlightId`-matching stash entry and returns the existing ship object. Fragment flights look identical to fully-docked source flights from its perspective.

**Files**:
- [HangarOps.php](source/server/model/systems/HangarOps.php): `performLand` partial branch, new `spawnFragmentFlight` helper, possibly `splitFlightNameFor` (already exists for the inverse direction — partial launches).
- [Manager.php](source/server/controller/Manager.php): only if a new DB helper is needed for the damage/crit re-attribution (`UPDATE tac_damage SET system_id = ? WHERE system_id = ? AND ship_id = ?` — two statements per affected fighter).
- Possibly `tac_critical` and `tac_damage` schema — verify the existing indices cover `(ship_id, system_id)` filtered updates without table scans.

**Verify**:
- Flight of 6 takes 3 damage on fighter 5, 1 damage on fighter 4 → partial-dock 2 → relaunch on a later turn → flight of 2 emerges with 3+1 damage spread on its two fighters (per 10.3's priority order).
- Same scenario but fighter 5 also has a critical (e.g. EngineHit) → the critical re-appears on the relaunched fighter.
- Damage to a fighter's subsystem (e.g. its primary weapon takes 2 dmg) also persists.
- Hangar damage while a partially-docked stash entry is present → the existing `evictCraftFromHangar` path correctly evicts from the fragment flight (which is a real ship row, so `dockedFlightId`-evict semantics already apply — verify nothing new needs wiring).

**Implementation notes (2026-05-17).** Went with Option A (fragment flight at dock time). New helpers `spawnFragmentFlight` and `copyFighterStateToFragment` in [HangarOps.php](source/server/model/systems/HangarOps.php); `performLand`'s partial-dock branch now spawns a fragment immediately after `dockFighters` returns the 10.3-priority-ordered chosen list, copies their Fighter-level damage and damage-related crits, and sets `dockedFlightId` on the stash entry to point at the fragment. Resurrect-via-`resurrectDockedFlight` works unchanged: matches phpclass + exact flightSize + dockedFlightId, returns the fragment, performLaunch clears `$removed` and inserts a fresh deploy MovementOrder at the carrier's current hex. Damage carries through naturally because the same DB rows survive.

Spawn details:
- Fragment goes through `Manager::insertSingleShip` like new-spawn launches; persists `tac_ship` row, `tac_flightsize` size, a deploy MovementOrder at the carrier's current hex (purely for ship-state consistency — fragment is `$removed` so it never renders), and standard SystemData init for weapon loading state. Fragment is `$removed=true`/`$removedTurn=dockTurn`/`$spawned=dockTurn`, so the client's pre-spawn hide (`ship.spawned > gamedata.turn`) plus the Stage-7 isDestroyed fold both filter it from board / fleet list correctly.
- Damage copy: walks `$sourceFighter->damage[]`, creates a fresh `DamageEntry` with `$shipid=$fragment->id`, `$systemid=$fragmentFighter->id`, `$updated=true`. `FireGamePhase::advance`'s `submitDamages` call picks it up from `$gamedata->getNewDamages`.
- Critical copy: walks `$sourceFighter->criticals`, skips state-marker crits (`DisengagedFighter`, `DockedFighter`, `LaunchedThisTurn`) since those are source-flight flight-control markers, re-creates each remaining crit with `$updated=true` + `$newCrit=true`.
- Fighter pairing is by ORDER (`chosenFighters[i]` → `fragment->systems[i]`). Both flights are constructed from the same phpclass so structures are identical (same fighter count, same id-by-index per fighter).

Known limitations:
- **Subsystem-level damage on fighters is NOT copied** — only Fighter-level damage/crits. Fighter subsystems (e.g. a fighter's primary weapon) rarely take separate damage in B5W (fighters are usually destroyed wholesale), so this is unlikely to bite in practice; revisit if a Fighter-subsystem-crit-bearing dock is reported.
- ~~Partial relaunch from a partial-dock fragment loses damage on the launched fighters.~~ — addressed in [Stage 10.5](#stage-105--partial-launch-from-fragment-damage-transfer--ghost-cleanup) below.

Files: [HangarOps.php](source/server/model/systems/HangarOps.php).

---

#### 10.5 — Partial-launch from fragment: damage transfer + ghost cleanup ✓ COMPLETE

Two related issues surfaced in 10.4 testing:

1. **Damage loss on partial-launch from a fragment.** Player partial-docks 3 of 6 (with damage) → fragment_A of 3 carries the damage. Player later partial-launches 2 of the fragment's 3 → `resurrectDockedFlight` rejects the size mismatch → new-spawn path spawns 2 fresh undamaged fighters. The 2 launched fighters lose their damage history (the third fragment fighter still has its damage, but only because it stays put).

2. **Ghost docked fighters after the final relaunch.** Continuing the scenario: player launches the last 1 fighter next turn. `resurrectDockedFlight` matches (stash size 1, launch size 1) → fragment_A resurrects. But fragment_A's ship row still has 3 Fighter subsystems — 2 of which were marked `DockedFighter` by the previous turn's `syncSourceFlightsOnLaunch`. The flight window shows 1 active fighter + 2 cyan **DOCKED** ghosts, which reads as "this is a flight of 3 with 2 stashed in a hangar" when in reality they were launched away to a different flight.

Both stem from `syncSourceFlightsOnLaunch` mutating the fragment by marking fighters `DockedFighter` rather than transferring their state and shrinking the fragment.

**Fix.** Replace `syncSourceFlightsOnLaunch` + `removeFromHangarUsage` in `performLaunch`'s new-spawn path with a new `consumeStashesForLaunch` helper that:

- Walks every matching `phpclass` stash entry the launch consumes.
- For each `dockedFlightId`-linked entry:
  - **Priority-select** the `$take` fighters from the linked OLD fragment (10.3 order: most damage first, back-of-array tiebreak).
  - **Copy** each selected fragment fighter's damage + crits onto the next slice of launched-flight fighter slots (reuses the existing `copyFighterStateToFragment`/now-`copyFighterStateToTarget` helper).
  - If `$take < entrySize` (partial extract): spawn a **NEW** fragment (`fragment_A'`) carrying the *remaining* unfired fighters' damage state, then mark every fighter on the OLD fragment with `DisengagedFighter` so it's fully destroyed (FighterFlight::isDestroyed → true via the fighter-disengage fold) and won't show on the board / fleet list. Stash entry is rewritten in-place: `dockedFlightId` → new fragment's id, `flightSize` → `entrySize - take`, `name` → new fragment's name.
  - If `$take == entrySize` (full extract): no new fragment needed. OLD fragment fully destroyed, entry consumed.
- For non-fragment entries (anonymous orphan, auto-shuttle): just consume the stash count, advance the launched-fighter cursor without copying state (those entries never had damage history to transfer).

The OLD fragment ship row stays in the DB after partial-launch but is effectively dead (every fighter disengaged). It remains addressable for replay scrubbing of earlier turns when it was the live stash entry. No DB row deletion needed; the `DisengagedFighter` crits persist via the existing `submitCriticals` pipeline and the FighterFlight isDestroyed fold hides it everywhere.

**Why a new fragment instead of compacting the existing one?** Compacting the OLD fragment would mean re-indexing surviving fighters' `tac_damage` / `tac_critical` rows (since `populate()` re-creates fighters at sequential ids 0..N-1 on reload) AND deleting orphan rows for removed indices. That's three or four SQL surgery operations per partial-launch, ordering-sensitive. Spawning a new fragment + abandoning the old one is symmetric to 10.4's dock-time spawn path, reuses every existing primitive (`spawnFragmentFlight`, `copyFighterStateToTarget`, the resurrect path), and costs one extra `tac_ship` row per partial-launch — a bounded amount per game.

Name dedup: `spawnFragmentFlight` checks if the source name already contains `' - Detachment'` and skips appending another suffix, so chained partial-launches don't produce `"Sentinel-1 - Detachment - Detachment - Detachment"`.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php). `syncSourceFlightsOnLaunch` and `removeFromHangarUsage` become unused (no other callers) — leave them in place for now, mark deprecated; remove in a polish pass once 10.5 has burned in.

**Verify**:
- Source flight of 6 (damages [0, 0, 0, 2, 3, 5]) → partial-dock 3 → fragment_A of 3 carrying [5, 3, 2] (priority order). Next turn, partial-launch 2 of fragment_A → launched flight of 2 with damage [5, 3] on its two fighters; new fragment_A' of size 1 carrying damage [2] on its one fighter; OLD fragment_A is hidden (all fighters disengaged). Final relaunch of size 1 → fragment_A' resurrects as a flight of **one** fighter with damage 2 — no ghost DOCKED rows in the flight window.
- Stash with multiple matching entries (e.g. fragment_A: 3 + anonymous orphan: 3) launching size 4 → fragment_A fully extracted (its 3 fighters' damage transferred to launched fighters 0..2; OLD fragment_A destroyed), anonymous orphan partially consumed (launched fighter 3 stays fresh, no damage history available).
- Full relaunch of a partial-dock fragment (exact size match) still works unchanged via `resurrectDockedFlight` (10.4 behaviour preserved — no `consumeStashesForLaunch` involvement, because resurrect short-circuits new-spawn).
- Fleet list: OLD fragments after partial-launch don't appear (combat value 0 via all-disengaged → Stage-9 hide rule). New fragment appears as the live stash row.

**Implementation notes (2026-05-17).** Three new private helpers in [HangarOps.php](source/server/model/systems/HangarOps.php):
- `consumeStashesForLaunch($hangar, $phpclass, $launchCount, $launchedFlight, $carrier, $gamedata)` — replaces the legacy `syncSourceFlightsOnLaunch` + `removeFromHangarUsage` calls in `performLaunch`'s new-spawn path. Iterates hangarUsage entries, processes each `dockedFlightId`-linked entry by transferring damage state via `copyFighterStateToTarget` and either fully destroying the OLD fragment (full extract) or spawning a NEW fragment for the remainder (partial extract). Non-fragment entries (anonymous orphan / auto-shuttle) are just consumed.
- `selectFightersForExtraction($flight, $count, $gamedata)` — priority-ordered pick (10.3 order: most damage DESC, idx DESC) WITHOUT applying any crit. The selected fighters are subsequently either copied + destroyed (`consumeStashesForLaunch`'s full-extract path) or copied + preserved in a new fragment (`consumeStashesForLaunch`'s partial-extract path).
- `destroyAllFighters($flight, $gamedata)` — marks every active Fighter on a flight with `DisengagedFighter`. Used on OLD fragments after their state has been transferred; the fold in `FighterFlight::isDestroyed` hides the OLD fragment from board, target lists, and fleet list afterwards.

Renamed the existing `copyFighterStateToFragment` → `copyFighterStateToTarget` since 10.5 now also uses it to copy fragment-fighter state onto the launched flight's fighters. Parameter naming generalised (`$fragment`/`$fragmentFighter` → `$targetFlight`/`$targetFighter`). Function body is unchanged otherwise.

`spawnFragmentFlight` now dedups the `' - Detachment'` suffix in the fragment name so chained partial-launches (fragment_A → fragment_A' → fragment_A''…) don't produce ever-growing names.

`syncSourceFlightsOnLaunch` and `removeFromHangarUsage` are now unused — left in place as dead code until 10.5 has burned in; remove in a polish pass once partial-launch flows have been exercised across a few games. PHP syntax check passes.

Files: [HangarOps.php](source/server/model/systems/HangarOps.php).

---

#### 10.6 — Strict hangar-category gating + per-ship customFighter caps ✓ COMPLETE

Two related runtime gating gaps surfaced after the Stage 9 ungating let live games exercise dock against a wider variety of carrier types. Both are about *which carrier* a given flight is allowed to dock into — the size hierarchy was correct, but two whole classes of cross-contamination still slipped through:

1. **Universal `fighters`/`normal` slots accept Assault Shuttles and Breaching Pods.** [HangarOps::hangarAcceptsCategory](source/server/model/systems/HangarOps.php#L835-L869) short-circuits the moment `$hType === 'fighters' || $hType === 'normal'` and returns `true` for *any* category. The intent was "universal combat-fighter slot accepts any size of fighter, plus shuttles per §10.1." The implementation accepts everything — AS, BPs, custom Raiders, Vipers, Rutarian, Thunderbolt-named flights, the lot. So an Assault Shuttle currently docks into an Omega's `"normal" => 24` hangar; a Breaching Pod docks into a Hyperion's medium slots; etc.
2. **No per-ship enforcement of the `customFighter` cap.** A flight with `customFtrName = "Thunderbolt"` (jinkinglimit → medium) currently docks into ANY carrier with a medium-or-larger combat slot. The fleet builder's `checkChoices` ([gamelobby.js:1294-1376](source/public/client/gamelobby.js#L1294-L1376)) enforces the Thunderbolt cap **fleet-wide** by summing all `customFighter["Thunderbolt"]` across the fleet and checking the total Thunderbolt count fits — but at runtime each individual carrier needs to enforce its own `customFighter[$name]` cap, otherwise a Thunderbolt flight could dock into a Hyperion that has zero Thunderbolt-capable bays just because the *fleet* happens to have spare Thunderbolt capacity on an Omega elsewhere.

The two bugs are independent (either fix can land without the other) but they share the same call sites, share the same mirror surfaces on the client, and pair naturally for review. Recommended order is **10.6.1 → 10.6.2** because 10.6.1 is a one-line tightening of an existing branch and reduces the surface 10.6.2 has to think about (after 10.6.1, the only path a custom-named flight can reach `hangarAcceptsCategory == true` is via size hierarchy against a matching fighter slot, which is exactly the path 10.6.2 needs to gate further).

---

##### 10.6.1 — Tighten universal `fighters`/`normal` shortcut ✓ COMPLETE

**Fix.** Replace the unconditional shortcut with a category-aware allowlist:

```php
//Universal fighter slot accepts any combat fighter (heavy/medium/light/
//ultralight) plus shuttles/minesweeping shuttles per §10.1. Reject everything
//else (AS, BPs, custom-name flights like Raiders/Rutarian/Thunderbolt) —
//those need an exact-match slot or a customFighter declaration (10.6.2).
if ($hType === 'fighters' || $hType === 'normal') {
    static $combatSizes = array('heavy', 'medium', 'light', 'ultralight');
    if (in_array($cat, $combatSizes, true)) return true;
    if ($cat === 'shuttles' || $cat === 'minesweeping shuttles') return true;
    return false;
}
```

The downstream paths (exact-match, size hierarchy, shuttle compat, AS-BP bridge) stay unchanged. A custom-named flight whose `trueSizeOf` returns its custom name (e.g. `'Raiders'`, `'Rutarian'`, `'superheavy'`) now correctly falls through past the universal short-circuit and is rejected unless the carrier has an exact-match slot. Thunderbolts still pass the universal check via their jinkinglimit-derived `'medium'` category — that's 10.6.2's problem to gate further.

**Files**:
- [HangarOps.php:835-869](source/server/model/systems/HangarOps.php#L835-L869) — the one branch above.
- [shipTooltipFireMenu.js:300-312](source/public/client/UI/shipTooltipFireMenu.js#L300-L312) — `hangarAcceptsCategory` mirror.
- [shipTooltipFireMenu.js:445-456](source/public/client/UI/shipTooltipFireMenu.js#L445-L456) — `hangarAcceptsCategoryRecover` mirror (carrier-side Recover dialog).
- [DeploymentDock.js:180-191](source/public/client/renderer/phaseStrategy/DeploymentDock.js#L180-L191) — Deployment-phase dock mirror.

**Verify**:
- Decurion (`"assault shuttles"=>24, "Breaching Pods"=>4`) in same hex as a friendly Starfury flight → Dock disabled (no `medium` slot, no universal slot — fine). Same for a Sentri (medium) trying to dock into the Decurion → disabled.
- Omega (`"normal"=>24`) in same hex as a friendly Tortra (AS) → Dock disabled.
- Omega in same hex as a friendly Lamprey (BP) → Dock disabled.
- Omega in same hex as a Starfury (medium) → Dock allowed (universal → combat fighter, still OK).
- Omega in same hex as a shuttle → Dock allowed (universal → shuttle, per §10.1).
- llort Trathor (`"normal"=>12, "assault shuttles"=>12`) in same hex as an AS and a Starfury → Dock allowed into the AS slot, Dock allowed into the normal slot for the Starfury. The shared-universal-bay case is unaffected because the AS goes to the AS-typed Hangar.
- Centauri Balvarix (`"medium"=>36`) in same hex as a Sentri (medium) → Dock allowed via exact-match (regression guard — make sure the tightening didn't break legit dock).

---

##### 10.6.2 — Per-ship `customFighter` cap enforcement ✓ COMPLETE

Per-carrier rule: a flight with non-empty `customFtrName` may only dock into a carrier whose `customFighter[$name]` declaration is ≥ the post-dock count of that name in the carrier's hangars. The cap is the **count of that custom fighter type that can be stored across all hangars on this carrier**; it does NOT change the underlying combat-size slot the flight occupies (a Thunderbolt still takes a medium hangar box, just like any other medium fighter — `customFighter` is an additional gate, not a replacement).

**Storage shape.** `$hangarUsage` entries already carry `phpclass`, `name`, `flightSize`, `hangarType`. Add an optional `customFtrName` field that is stamped at dock time for any flight whose phpclass-instance has a non-empty `$customFtrName`. The lookup is `$flight->customFtrName` — the prototype is cheap to instantiate via the existing `phpclass`/`spawnableClasses` pattern (or read live off `$flight` at dock time, since we have the source flight in hand). Stamping the name onto the entry avoids re-instantiating fragments on every capacity check.

Entries created by Stage 7 deploy-dock + Stage 10.4 fragment spawn + Stage 10.5 partial-launch all flow through `performLand` / `spawnFragmentFlight` / `consumeStashesForLaunch`, so the stamp lives in one helper.

Auto-filled shuttles (Step 3 of [HangarOps::populateInitialHangarUsage](source/server/model/systems/HangarOps.php#L26-L111)) and explicitly-declared shuttle slots (Step 2) get `customFtrName = ''` — they don't consume custom caps. Initial population of `customFighter`-bearing carriers is unchanged: combat fighters still auto-deploy to space at turn 1 via the existing fleet-builder flow, so the hangar starts empty of customFighter-named flights until Stage 7 deploy-dock or Stage 5 in-game dock writes one.

**Helper**: new `HangarOps::customFighterRemaining($carrier, $name)`. Returns:
- `INF` if `$name === ''` (i.e. flight has no custom name — no gate to enforce).
- `0` if the carrier doesn't declare `$customFighter[$name]` at all.
- `$carrier->customFighter[$name] - <count of matching stamped entries>` otherwise.

The count walks every hangar on the carrier, summing `flightSize` for entries where `entry['customFtrName'] === $name`. Inactive shuttles auto-filled into customFighter-eligible slots don't match because their stamp is `''`. Pending dock orders (Stage 10.2's `pendingDockOrders` accumulator) need to be considered too — otherwise the dialog under-counts when a second dock is queued — so the helper takes an optional `$includePending = true`.

**Gate sites**:
- [HangarOps::canShipReceive](source/server/model/systems/HangarOps.php#L770-L823) — after the size-category check, before returning true:
  ```php
  if (!empty($flight->customFtrName)) {
      $remaining = self::customFighterRemaining($carrier, $flight->customFtrName);
      if ($remaining < $count) { $reason = 'customFighter cap exceeded'; return false; }
  }
  ```
- [HangarOps::eligibleHangarsForLanding](source/server/model/systems/HangarOps.php#L875-L904) — multiplexed across multiple hangars on the same carrier, so the cap is a per-CARRIER bound that clips the per-hangar capacity. After the loop builds `$out`, clamp the sum of capacities to `customFighterRemaining($carrier, $flight->customFtrName)`. If the cap is 0, return empty (no eligible hangars at all). If the cap is < total capacity, scale each entry proportionally or just clip the first-N preferred hangars.
- [HangarOps::canDeployStartDock](source/server/model/systems/HangarOps.php) (Stage 7) — same gate, since deploy-phase dock is a different entry path with the same end-of-turn handler. Verify by inspection.

**Stamp sites**:
- [HangarOps::performLand](source/server/model/systems/HangarOps.php) — the full-dock and partial-dock branches both append entries; stamp `customFtrName` in both.
- [HangarOps::spawnFragmentFlight](source/server/model/systems/HangarOps.php) (Stage 10.4) — the fragment's stash entry needs the stamp too.
- [HangarOps::consumeStashesForLaunch](source/server/model/systems/HangarOps.php) (Stage 10.5) — the NEW-fragment entry created on partial extract needs the stamp; the OLD fragment's entry is consumed so no action.
- Stage 7 deploy-dock helper — verify the entry shape and stamp the name there too.
- `populateInitialHangarUsage` Steps 2/3 — shuttle entries get `customFtrName = ''` (no change to current shape — `array_key_exists` checks treat missing as `''`).

**Client mirrors**:
- [shipTooltipFireMenu.js::findEligibleCarriersForDock](source/public/client/UI/shipTooltipFireMenu.js) (`collectReceivingHangars`) — after computing per-hangar `free`/`budget`, subtract from the carrier-level customFighter remaining and clip. Add a `customFighterRemainingFor(carrier, name)` helper that mirrors the PHP one, counting stamped entries in `sys.hangarUsage` plus `sys.pendingDockOrders` (look up the flight by ID to get its `customFtrName`; treat unknown as `''`).
- [shipTooltipFireMenu.js::findEligibleFlightsForDocking](source/public/client/UI/shipTooltipFireMenu.js) (`collectReceivingHangarsForRecover`) — same clamp, applied to the full-flight-size check (a flight whose customFighter cap on this carrier is < its size is ineligible for bulk recover).
- [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js) — same helper, same clamp in `collectUsableHangars` and wherever the deploy-dock dialog computes per-carrier capacity.
- `confirm.js::hangarDock` splitter — when computing the splitter row's `max` per hangar, additionally clamp the carrier's running customFighter remaining as the dropdowns/inputs change. This is the live-feedback equivalent of the OK-time aggregate check.

**Stripping**: `Hangar::stripForJson` already ships `hangarUsage` verbatim. The new `customFtrName` field rides on the entry shape and round-trips through JSON without further work. The carrier ship itself already exposes `$customFighter` via the default `BaseShip::stripForJson` (the lobby reads `lship.customFighter` directly, confirming this).

**Edge cases**:
- Carrier has `customFighter = ["Thunderbolt"=>24]`, current hangars stash 20 Thunderbolts via deploy-dock. Player tries to dock 6 more in Firing Phase → `canShipReceive` returns false with `reason = 'customFighter cap exceeded'`. Splitter dialog clamps to 4 max. Player commits 4. End-of-turn: 4 dock, the 2 stay in space.
- Carrier has `customFighter = ["Rutarian"=>12]`, no Thunderbolt declaration. Player tries to dock a Thunderbolt flight → not eligible. The Dock button doesn't list this carrier; bulk-Recover dialog excludes the flight from this carrier's perspective.
- Carrier has `customFighter = ["Thunderbolt"=>24]` AND `customFighter = ["Python"=>12]` (hypothetical). The two caps are independent — Thunderbolt usage doesn't consume Python capacity.
- Stage 10.4 fragment-flight scenario: partial-dock 3 Thunderbolts → fragment stash entry stamped `Thunderbolt`. Carrier-customFighter usage = 3. Future relaunch + re-dock: usage transitions cleanly because the stamp travels with the entry.
- Stage 10.5 partial-launch scenario: fragment of 6 Thunderbolts, launch 2 → consume 2 from stash, new fragment of 4 carries the stamp. Carrier usage drops by 2 (the 2 launched leave the carrier).
- Hangar damage that destroys a stamped entry: `evictCraftFromHangar` already mutates `$hangarUsage` via `flightSize` decrement / record removal. The customFighter usage naturally drops as the entry shrinks. No code change needed in the eviction path.
- A flight in space whose carrier was destroyed mid-game — the flight retains its `customFtrName` and the eligibility check correctly only allows it back into a different `customFighter`-capable carrier. No special handling.

**Files**:
- [HangarOps.php](source/server/model/systems/HangarOps.php) — new `customFighterRemaining` helper; gate in `canShipReceive`, `eligibleHangarsForLanding`, `canDeployStartDock`; stamp in `performLand`, `spawnFragmentFlight`, `consumeStashesForLaunch`.
- [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js) — new `customFighterRemainingFor` helper; clamp in `collectReceivingHangars` and `collectReceivingHangarsForRecover`.
- [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js) — same helper; clamp in `collectUsableHangars`.
- [confirm.js](source/public/client/UI/confirm.js) — splitter row `max` recomputation and live capacity readout incorporate the customFighter cap.

**Verify**:
- Omega + Warlock + 24 Thunderbolts + 24 Starfuries in fleet. Dock 24 Thunderbolts into the Omega → OK. Dock the 25th Thunderbolt into the Omega → rejected (cap = 24). Dock the 25th into the Warlock → OK (Warlock has its own 24 cap, currently 0 used). Dock a Starfury into the Omega → OK (Starfury has no customFtrName).
- Hyperion (no customFighter declaration) + a Thunderbolt flight in the same hex → Thunderbolt's Dock dialog excludes the Hyperion from the carrier picker. Bulk Recover from the Hyperion excludes the Thunderbolt flight.
- Balvarix (`"medium"=>36, customFighter=["Rutarian"=>12]`) + Rutarian flight (6) + Sentri flight (6) in the same hex → Both can dock; after Rutarian docks, Balvarix Rutarian-remaining drops to 6 (still room for one more Rutarian flight) while medium-slot remaining drops to 30.
- Stage 10.4 / 10.5 chained partial dock + partial launch on a Thunderbolt-stamped fragment → carrier customFighter usage tracks correctly through every transition; no negative remaining, no orphan stamp.
- Replay scrub through a turn that docked + launched + partial-docked Thunderbolts → tooltip's "Carrying" line shows the customFighter usage per name (folded into the existing Stage 10.2 stored-craft listing — the byClass bucket already groups by phpclass name which is what the player sees).

**Implementation notes (2026-05-18).** Landed in two passes:

*Initial implementation* followed the spec above: tightened the universal `'fighters'`/`'normal'` shortcut to combat fighters + shuttles only (10.6.1); added `customFighterRemaining` helper + stamps + gates + client mirrors (10.6.2). Both passed PHP lint and JS syntax checks; deployment testing confirmed customFighter caps (Thunderbolt/Rutarian) working as designed.

*Post-test refactor* — the universal-slot tightening turned out to break **multi-category carriers** like Decurion (`["assault shuttles"=>24, "Breaching Pods"=>4]`). `inferHangarType` only narrows when a ship declares EXACTLY ONE size-specific category; multi-category carriers keep their Hangar systems at universal `'fighters'` and lost AS/BP eligibility under the conservative tightening. The fix: `hangarAcceptsCategory($hangar, $category, $ship = null)` is now ship-aware. Universal hangars derive their permission set from `$ship->fighters`:

- Combat fighter in universal slot: accepted only if the ship declares some combat capacity (`normal`/`heavy`/`medium`/`light`/`ultralight`), with size hierarchy. Decurion (no combat declaration) correctly rejects fighters.
- AS in universal slot: accepted only if `$ship->fighters["assault shuttles"] > 0`.
- BP in universal slot: accepted if ship declares BP, AS, or any combat-fighter capacity.
- Shuttles / minesweeping shuttles: always accepted per §10.1.
- Other custom names: rejected (need exact-match or `customFighter`).

`freeBoxesByCategory` gained an optional `$ship` parameter forwarded to `hangarAcceptsCategory`; all three call sites (`canShipReceive`, `eligibleHangarsForLanding`, `canDeployStartDock`) plus the deploy-phase auto-route (`firstFittingHangar` in DeploymentDock) now pass `$carrier`/`$ship`. Client mirrors in `shipTooltipFireMenu.js` (two helpers — `hangarAcceptsCategory` and `hangarAcceptsCategoryRecover`) and `DeploymentDock.js` mirror the same shape.

*BP rule correction* — after a re-read of the actual rulebook, the BP slot hierarchy was loosened twice:
1. First pass: BP → BP slot, AS slot, OR medium-or-larger combat slot.
2. Final pass: BP → BP slot, AS slot, OR **any** combat-fighter slot (heavy/medium/light/ultralight). No minimum size threshold. Universal-slot BP acceptance also extended to ships declaring `light` or `ultralight` capacity.

Aggregate guards in `confirm.js`'s `hangarDeployDock` and `hangarRecover` OK handlers (`checkCustomFighterAggregate`) catch the rare case where multiple same-named flights individually pass eligibility but collectively exceed the carrier's cap.

Files: [HangarOps.php](source/server/model/systems/HangarOps.php), [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js), [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js), [confirm.js](source/public/client/UI/confirm.js). PHP lint + JS `node --check` clean on all four.

---

**Commit cadence**: one commit per sub-stage, message style matching the repo:

```
Hangar Ops Stage 10.1: two-pass criticals so dock sees post-dropout state
Hangar Ops Stage 10.2: project pendingDock/Launch into hangar tooltip
Hangar Ops Stage 10.3: dock most-damaged-first, then back-of-array
Hangar Ops Stage 10.4: fragment flight preserves damage on partial dock
Hangar Ops Stage 10.5: partial-launch transfers damage + shrinks fragment
Hangar Ops Stage 10.6.1: tighten universal hangar shortcut so AS/BP don't cross-dock
Hangar Ops Stage 10.6.2: per-ship customFighter cap enforcement at dock time
```

---

### Stage 12 — Shuttle-slot lobby enhancements (HANG_BP, HANG_MSW) ✓ COMPLETE

**Goal.** Two new lobby enhancements that let players convert default (auto-populated, leftover-capacity) shuttle slots before deployment:

- **HANG_BP** — 10pt per slot. Converts a default shuttle slot into a dedicated Breaching Pod slot. The carrier loses N default shuttles from auto-population and gains N "Breaching Pods" slots that a bought BP flight can deploy-dock into. Offered to any carrier with non-zero `HangarOps::getDefaultShuttles` count — including minesweeper-bonus carriers (where the conversion is from MinesweepingShuttle leftover → BP).
- **HANG_MSW** — 10pt per unit (B5W rule: "10pts or 20% of MinesweepingShuttle craft cost, whichever is higher" — floor of 10 dominates today since `Shuttle->pointCost = 0`). Retypes N of the auto-populated default shuttles as MinesweepingShuttle instead of the carrier's faction-default shuttle (Flyer for Minbari, Shuttle otherwise). Does **NOT** reduce shuttle capacity — minesweeping shuttles still count as default shuttles for armed-shuttle purchase headroom. Gated to non-minesweeper carriers (would be a no-op on a carrier whose default pool is already MinesweepingShuttle).

Both are modelled on the existing HANG_F / HANG_AS enhancements ([Enhancements.php](source/server/model/ships/Enhancements.php) — fighter↔assault-shuttle slot conversion), but with concrete server-side effects because they touch hangar categories that downstream code (HangarOps step 2/3) actually consumes.

**Implementation.**

Server-side ([Enhancements.php](source/server/model/ships/Enhancements.php)):
- `setEnhancementOptionsShip` registers HANG_BP / HANG_MSW guarded by `HangarOps::getDefaultShuttles($ship)['count'] > 0`. Pool is leftover hangar capacity — NOT an explicit `$ship->fighters["shuttles"]` key, which most ships don't declare.
- `setEnhancementsShip` for `HANG_BP` does `$ship->fighters["Breaching Pods"] += $enhCount` — adding to the destination category implicitly steals from the leftover pool via HangarOps step 3's `leftover = capacity - totalDeclared` math, so no separate "shuttles" decrement is needed.
- `setEnhancementsShip` for `HANG_MSW` is a no-op — see HangarOps section below.
- Both added to `blockStandardEnhancements` so non-standard sets (Mindrider, Shadow, etc.) don't see them.

Server-side ([HangarOps.php](source/server/model/systems/HangarOps.php)):
- New `HangarOps::getDefaultShuttles($ship)` — direct port of [systems.js getDefaultShuttles](source/public/client/systems.js#L425). Returns `{count, type, key}` from leftover hangar capacity, switching to `'minesweeping shuttles'` when `$minesweeperbonus > 0`.
- `populateInitialHangarUsage` Step 3 reads `HANG_BP` from `$ship->enhancementOptions` and subtracts the count from `$leftover` **before** computing how many shuttles to auto-place. This is necessary because `Enhancements::setEnhancements` runs **after** `Hangar::onIndividualNotesLoaded` at game load (TacGamedata::onConstructed fires after ship/note hydration in `getTacShips`), so `$ship->fighters["Breaching Pods"]` hasn't been mutated yet at populate time. Without the direct read, leftover slots would fully auto-fill with shuttles and the BP conversion would silently vanish at game-start.
- `populateInitialHangarUsage` Step 3 also reads `HANG_MSW` and seats that many MinesweepingShuttle records **first** (flightSize=1, hangarType='minesweeping shuttles'), then fills the remaining leftover with the carrier's faction-default shuttle. Gated to non-minesweeper carriers (where `$baseClass !== 'MinesweepingShuttle'`).

Client-side fleet check ([gamelobby.js](source/public/client/gamelobby.js)):
- Pre-loop block at the top of the per-ship `!lship.flight` section snapshots `lship._originalFighters` on first encounter and restores-then-reapplies on subsequent fleet-check passes (otherwise enhCount changes would stack). Then applies `HANG_BP` as `lship.fighters["Breaching Pods"] += N`. This propagates through every downstream consumer in the loop — `shipBPDedicated`, `shipSlots`, `totalBPDedicated`, `totalHangarOther`, the `getDefaultShuttles(lship)` call further down — automatically.
- `HANG_MSW` is **deliberately NOT** applied here. Minesweeping shuttles still count as default shuttle capacity for armed-shuttle purchase; only the auto-populated *type* changes at game-load (HangarOps step 3 above). Mutating `lship.fighters["minesweeping shuttles"]` would erroneously shrink the default-shuttle pool.

Client-side display ([lobbyEnhancements.js](source/public/client/lobbyEnhancements.js)):
- New `HANG_BP` / `HANG_MSW` cases in `setEnhancementsShip` (notes append) and `resetEnhancementMarkersShip` (marker reset). Notes read "Breaching Pod Conversion (N)" / "Minesweeping Shuttle Conversion (N)".

**Files**: [Enhancements.php](source/server/model/ships/Enhancements.php), [HangarOps.php](source/server/model/systems/HangarOps.php), [gamelobby.js](source/public/client/gamelobby.js), [lobbyEnhancements.js](source/public/client/lobbyEnhancements.js).

**Bug surfaced and fixed during Stage 12 testing — client-side shared-reference trap.**

During live-game testing, two same-faction Whitestars (one with HANG_MSW=1, one with HANG_BP=2) displayed identical hangar contents — whichever ship's Hangar `refreshHangarTooltip` ran last "won" for every other Whitestar on the board. Trace:

- [BaseShip::stripForJson](source/server/model/ships/ShipClasses.php#L574-L661) does NOT include `$this->data` in the live JSON payload (it's computed client-side).
- [SystemFactory.createSystemFromJson](source/public/client/model/systemFactory.js#L76-L100) uses `Object.assign({}, staticSystem)` — a **shallow** copy. `base.data` keeps the static-blueprint reference.
- `Object.assign(base, systemJson)` doesn't override `data` (server didn't send it).
- `ShipSystem` constructor's `for (var i in json) this[i] = json[i]` is also a shallow assign.
- Result: every Hangar instance built from the same `window.staticShips[faction][phpclass]` entry points at the *same* `data` object. `refreshHangarTooltip`'s `this.data["Capacity"] = ...` mutates the shared reference; last writer wins.

Fix at [baseSystems.js:225-239](source/public/client/model/system/baseSystems.js#L225-L239): Hangar constructor now deep-clones `this.data` via `JSON.parse(JSON.stringify(this.data))` immediately after `ShipSystem.call(this, json, ship)` — same pattern [ship.js:11-13](source/public/client/model/ship.js#L11-L13) uses at the ship level. The system-level constructor stack didn't have an equivalent guard; this trap will recur for any future ShipSystem subclass that mutates a static-only nested property. **See [arch_client_system_shared_reference.md](C:\Users\Dougie\.claude\projects\c--FV-env-FieryVoid\memory\arch_client_system_shared_reference.md) for the general pattern.**

**Catapult parity caveat.** [§2.1](#21-new-properties-on-hangar-and-catapult-since-its-the-same-shape) said Catapult should subclass / inherit the same fields as Hangar. The client-side [Catapult](source/public/client/model/system/baseSystems.js#L480-L484) is currently a stub — no client-side mutations, so the shared-reference trap doesn't bite yet. When the Catapult launch-path work lands, mirror the deep-clone fix in its constructor; the same Whitestar-pair symptom will otherwise appear on any pair of side-launch carriers sharing a phpclass.

**What to verify** (in a *new* game — existing games have stale `hangarUsage` notes saved from before the Stage 12 timing fix):
- Whitestar (Minbari, 2-box hangar) with HANG_MSW=1 → hangar tooltip reads `Capacity: 2 / 2 slots`, `Stored Craft: 1 x Flyer, 1 x Minesweeping Shuttle`.
- Whitestar with HANG_BP=2 → hangar tooltip reads `Capacity: 0 / 2 slots`, no Stored Craft line; lobby fleet check shows 2 dedicated Breaching Pod slots available for purchase.
- Two Whitestars on opposing teams, one each enhancement → each ship's hangar tooltip shows its OWN state (no shared-reference bleed).
- Toggle HANG_BP count in the lobby from 2 → 4 → 1 → fleet check reflects the latest value (the `_originalFighters` snapshot-and-restore is working).
- Orieni Vigilant (minesweeper-bonus carrier with 6-box hangar, declared `["minesweeping shuttles" => 6]`) → HANG_MSW does NOT appear in the option list (gated); HANG_BP appears with label "Minesweeping Shuttle slot to Breaching Pod slot" and conversion correctly steals from the MSW pool.

**Follow-up (2026-05-21) — HANG_BP must not show a phantom Breaching Pod in the in-game Hangar tooltip.**

`getDefaultShuttleComposition` ([systems.js:492](source/public/client/systems.js#L492)) builds the leftover-pool breakdown shown in two surfaces: the lobby loadout list ([shipwindow.js:416](source/public/client/UI/shipwindow.js#L416)) and the in-game Hangar system-info tooltip ([systemInfo.js:44](source/public/client/UI/systemInfo.js#L44)). It was unconditionally appending `{ type: "Breaching Pods", count: bp }`, so the HANG_BP conversion rendered identically in both — including `Breaching Pods: 1` in the Hangar tooltip, which reads as "a Breaching Pod unit is sitting in this hangar." That's wrong: HANG_BP only converts a default-shuttle slot into a BP-*capable* slot; the pod itself is purchased separately and deploy-docked later.

Fix:
- [systems.js:519-525](source/public/client/systems.js#L519-L525) — the BP row now carries `slotOnly: true`, flagging it as converted-but-empty capacity rather than a present unit. The pool math is unchanged (`afterBP = pool - bp` still removes those slots from the default-shuttle fill), so capacity accounting is identical; only the row's *meaning* is now tagged.
- [systemInfo.js:46-49](source/public/client/UI/systemInfo.js#L46-L49) — the in-game Hangar tooltip skips `slotOnly` rows, so HANG_BP no longer implies a docked pod.
- [shipwindow.js](source/public/client/UI/shipwindow.js) — left untouched (per design call): the lobby loadout still reflects the converted slot, since that surface is "available slots," not "units present."

Design decision (confirmed with the user): hide in the in-game tooltip, keep as-is in the lobby loadout. Net effect — the Hangar tooltip's listed shuttle counts now sum to `bp` *less* than total capacity, which is correct (those slots are empty BP slots, not auto-filled shuttles).

**Files**: [systems.js](source/public/client/systems.js), [systemInfo.js](source/public/client/UI/systemInfo.js).

**Follow-up (2026-06-07) — HANG_BP Breaching Pods rejected at deploy-dock ("carrier full"); landed on the map instead.** (gameID 4160, Urik'hal carrier)

A Breaching Pod that deploy-docked fine in the client was rejected by the server on commit and started the game on the map in its `start` position. Trace: the deploy-dock resolves in `DeploymentGamePhase::process` → `generateIndividualNotes` → `HangarOps::performDeployStartDockFromOrders`, which runs on the **POST-side carrier** (rebuilt by `Manager::getShipsFromJSON`). That builder never calls `Enhancements::setEnhancements()`, so the POST-side ship's `$ship->fighters` is still the constructor default — empty for the Urik'hal, which declares no native fighters. `hangarAcceptsCategory($hangar, 'Breaching Pods', $postCarrier)` reads that empty declaration → returns false → no bay accepts the pod → "carrier full" fail note (DB note `19361: fail:873357:carrier full`). The Omega in the same game docked its Lampreys fine because it declares `fighters = ["normal"=>24]` in its constructor (survives the POST round-trip); only **enhancement-granted** capacity (HANG_BP, and by the same logic HANG_AS) was broken.

Fix:
- [HangarOps.php:3900-3913](source/server/model/systems/HangarOps.php#L3900-L3913) — `performDeployStartDockFromOrders` now resolves `$capabilityCarrier = $dbCarrier ?: $carrier` and uses it for both `hangarAcceptsCategory` checks. `$dbCarrier` is the gamedata-loaded ship (`getTacGamedata` → `onConstructed` → `setEnhancements` ran), so its `$fighters` correctly shows the enhancement-granted `"Breaching Pods"` slot. Capacity/occupancy math is unchanged — only the "does this bay accept this category" question consults the enhancement-applied declaration.

Scope: the Fire-phase land/dock path (`processDockOrders` → `canShipReceive`) runs on gamedata ships in `criticals.php` and was never affected; the fix is limited to the deploy-start path. Same `$dbCarrier` fallback also covers HANG_AS carriers (identical latent bug).

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php).

---

### Stage 13 — Lobby fleet-check polish & shuttle/fighter overflow rule ✓ COMPLETE

**Goal.** Three independent quality-of-life changes to the lobby fleet check that surfaced once the default-shuttle pool became a first-class concept in Stage 12: consolidate shuttle reporting, encode a newly-clarified B5W overflow rule, and add a second READY button near the Load-a-Saved-Fleet dropdown.

**A. Always-on "Total Assault Shuttles" + "Shuttles" lines** ([gamelobby.js](source/public/client/gamelobby.js) `checkChoices`):

Under the existing "Breaching Pods & Shuttles" header, both lines now render unconditionally:

- `Total Assault Shuttles:` was previously gated on `totalFtrAS > 0 || totalHangarAS > 0`; the guard is removed so it always shows (e.g. `Total Assault Shuttles: 0 (allowed up to 0) OK`).
- New `Shuttles:` line aggregates the default shuttle/flyer pool across the whole fleet. Tracked via two new pre-loop vars near the other totals (~line 580):

  ```js
  var totalShuttleCapacity = 0;
  var defaultShuttleKeyList = []; // distinct lship.fighters keys used by default shuttle pools
  ```

  Accumulated inside the existing `defaultShuttles.count > 0 && key !== "minesweeping shuttles"` branch (~line 842). After the AS line, `totalShuttleUsage` is summed from `totalFtrOther` entries whose key is in `defaultShuttleKeyList` — capturing usage from any armed-shuttle variant the player has bought against any race's default pool.

**B. Single source of truth.** Default shuttle keys (`"shuttles"`, `"minbari flyers"`, …) are now skipped in the per-ship small-craft loop (`if (defaultShuttleKeyList.indexOf(scSize) !== -1) continue;`) so shuttle/flyer totals only appear once, in the BP & Shuttles section. Removes the duplicate row that previously showed when a player bought e.g. ArmedFlyers.

**C. New B5W rule — armed shuttle variants spill into fighter slots.** Rules clarification: units with `hangarRequired === 'shuttles'` may use any spare H/M/L/XL fighter slot, but NOT Assault Shuttle or Breaching Pod slots. Implemented as a three-state evaluation on the new Shuttles line:

| State | Condition | Display |
|---|---|---|
| Within pool | `usage ≤ capacity` | `Shuttles: 3 (allowed up to 5) OK` |
| Overflow absorbed | `overflow ≤ spareFighterSlots` | `Shuttles: 7 (allowed up to 5) (+2 fighter slots used) OK` |
| Insufficient overflow | `overflow > spareFighterSlots` | `Shuttles: 9 (allowed up to 5) (needs 4 fighter slots, only 2 spare) FAILURE!` |

Spare-fighter-slot math mirrors the BP free-pool calculation already in place above:

```js
var bpOverflowDemand = Math.max(0, totalBPUsage - totalBPDedicated);
var bpHMUsed = Math.min(Math.max(0, bpOverflowDemand - freeASForBP), freeHMForBP);
var spareHMForShuttle = Math.max(0, freeHMForBP - bpHMUsed);
var spareLForShuttle = Math.max(0, totalHangarL - totalFtrL);
var spareXLForShuttle = Math.max(0, totalHangarXL - totalFtrXL);
var spareFighterSlotsForShuttle = spareHMForShuttle + spareLForShuttle + spareXLForShuttle;
```

Allocation order is BPs first, then shuttles — BPs already consume HM as overflow per their existing capacity calc; shuttles get what's left. AS slots are deliberately excluded.

**D. Second READY button** ([gamelobby.php](source/public/gamelobby.php), [lobby.css](source/public/styles/lobby.css)).

A second READY button sits to the right of the "Load a Saved Fleet" dropdown inside `.fleet-loading-container`. Markup:

```html
<?php if(!$isFleetTest): ?>
<span class="readybutton readybutton-top">READY</span>
<?php endif; ?>
```

- **Wiring**: kept the `readybutton` class so the existing `$('.readybutton').on("click", gamedata.onReadyClicked)` at [gamelobby.php:253](source/public/gamelobby.php#L253) auto-binds both buttons — no JS changes needed.
- **Style isolation**: only style class is `readybutton-top` (intentionally NOT `btn` or `btn-success-lobby`), so the new button can be restyled without affecting the lower one. Initial `.readybutton-top` rule in [lobby.css](source/public/styles/lobby.css) clones the existing visual (green pill, hover lighten).
- **Fleet-test parity**: wrapped in the same `<?php if(!$isFleetTest): ?>` guard as the lower button.

**Files**: [gamelobby.js](source/public/client/gamelobby.js), [gamelobby.php](source/public/gamelobby.php), [lobby.css](source/public/styles/lobby.css).

**Verify**:
- Open lobby with an empty slot → "Total Assault Shuttles: 0 (allowed up to 0)" and "Shuttles: 0 (allowed up to 0)" both render under the BP & Shuttles header.
- EA fleet with leftover hangar capacity but no armed-shuttle units → "Shuttles: 0 (allowed up to N)" shows the default pool size; no duplicate "- Shuttles:" line above.
- Buy ArmedFlyers on a Minbari fleet → usage appears on the BP-section "Shuttles" line only; the per-ship small craft loop no longer prints a "- Flyers:" row.
- Mixed EA + Minbari fleet → single "Shuttles" line sums both shuttle and flyer pools (note: this collapses the two display labels — flag if you'd rather see them split per-race).
- Buy more armed shuttles than the default pool can hold but with spare H/M slots in the fleet → `(+N fighter slots used) OK`. Fill the fighter slots too → flips to `(needs N fighter slots, only M spare) FAILURE!`.
- Click the new top READY button → triggers the same confirm dialog as the bottom button; readies the slot identically.
- Open a fleet-test game → neither READY button shows.

---

### Stage 11 — Click the Hangar system icon to open the phase dialog ✓ COMPLETE

**Goal.** Today the hangar dialogs live on the ship tooltip (right-click): `Launch Fighters` in the Firing-Phase fire menu, `Deploy Flights in Hangar` in the Deployment-Phase tooltip. Hover over a hangar icon already shows the projected carrying / stored-craft tooltip (Stage 10.2). The missing piece is making the hangar icon itself a click target for "open the relevant dialog now" — the player's eye is on the system they want to act with, and clicking it should be the shortest path. game.php (React `ShipWindow`) only; gamelobby.php is out of scope.

**Implementation.** Short-circuit in [SystemIcon.clickSystem](source/public/client/UI/reactJs/system/SystemIcon.js#L140-L185) after the weapon-selection block, before the SystemClicked dispatch:

- If `gamedata.isMyShip(ship)` and `system.name === 'hangar'`:
  - **Deployment Phase (`gamephase === -1`)** — if `window.DeploymentDock.shipHasOpenableDockDialog(ship)` passes (which already gates on isMyShip / same-turn-deployed / at least one functional hangar), call `window.confirm.hangarDeployDock(ship)` and return.
  - **Firing Phase (`gamephase === 3`)** — if the carrier isn't rolling and isn't pivoting (mirrors `shipTooltipFireMenu.js`'s `carrierNotPivotingOrRolling`), call `window.confirm.hangarLaunch(ship)` and return.
- Otherwise fall through to the existing `SystemClicked` event so the system-info menu still opens for hangars that have no actionable dialog (empty firing-phase hangar, post-deployment turn carrier, etc.). Hover still shows the Stage 10.2 carrying tooltip in every case.

Per-design choices (confirmed with the user):
- Click does dialog OR info, never both — opening the dialog suppresses the SystemClicked popup so the two UIs don't overlap.
- No carrier-side Recover button from the hangar click; the Recover-Flights flow stays on the right-click tooltip (cross-flight bulk action, doesn't fit the per-hangar click idiom).
- Destroyed hangars never reach this code — the early `shipManager.isDestroyed(ship, system)` guard above the click handler returns first.

**Files**: [SystemIcon.js](source/public/client/UI/reactJs/system/SystemIcon.js).

**Verify**:
- Deployment-phase carrier deploying this turn with same-slot pending flights → click any hangar icon → `hangarDeployDock` dialog opens (same as clicking the tooltip button).
- Deployment-phase carrier deployed a previous turn → click hangar icon → falls through to system-info menu (no dialog applicable).
- Firing-phase own carrier, not pivoting/rolling, stored craft present → click hangar icon → `hangarLaunch` dialog opens.
- Firing-phase carrier mid-pivot → click hangar icon → falls through to system-info menu (gate blocks the launch dialog; the right-click tooltip already disables the button under the same condition).
- Enemy ship's hangar → click does nothing-new (existing `SystemTargeted` path runs as before).
- Movement / Initial-Orders / Pre-Firing phases → hangar click falls through to system-info menu unchanged.
- Hover over hangar icon in any phase → existing Stage 10.2 carrying/projected-stored tooltip still shows (this change only affects click, not mouseover).

---

### Stage 9.2 — Firing-phase carrier-side bulk recover dialog ✓ COMPLETE

Mirror of the Stage 7 deployment-phase dock dialog, exposed during the Firing Phase from the carrier's tooltip — players can now fill a carrier's hangars by ticking checkboxes for every same-hex friendly flight at once instead of clicking each flight separately.

Client-side only — server pipeline unchanged because the new dialog writes to the same `pendingDockOrders` array on each receiving hangar that the per-flight Dock dialog already used.

Files:
- [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js): new `recoverFlights` button between `launchFighters` and `dockFlight`; new `hasReceivableFlights` condition and `openHangarRecover` action; new `window.findEligibleFlightsForDocking(carrier)` helper that returns `[{flight, hangars: [{hangar, capacity}]}]` for every same-hex friendly flight whose **full** active count fits in at least one of the carrier's hangars. Eligibility mirrors the per-flight Dock path (same-hex/heading/speed-within-thrust) plus hangar size-hierarchy + shared launch+land budget.
- [confirm.js](source/public/client/UI/confirm.js): new `hangarRecover` dialog. One checkbox row per eligible flight with a hangar dropdown (single-option rows collapse to a static label); live per-hangar capacity readout (pills, red on overflow); OK validates aggregate per-hangar against both free boxes AND the shared launch+land budget. Pre-checks any flight currently queued in this carrier's hangars (re-edit case). Stale-eligibility flights (queued but no longer in hex) get a row labelled `— queued (no longer in hex)` so the player can un-check to cancel.
- [shipTooltip.css](source/public/styles/shipTooltip.css): new `.recoverFlights` class reusing `dockFlight.png` for the button icon (same conceptual action; the carrier-side button never coexists with the per-flight `dockFlight` button so the icon overlap is invisible to the player).

Compatibility with the existing per-flight Dock button (`hangarDock` dialog):
- Shared data model: both write `pendingDockOrders` on the receiving hangar with `{flightId, count}`; server's `processDockOrders` iterates each regardless of origin.
- **Re-edit from carrier-side after per-flight set a split**: the dialog detects the split via `queuedByFlight`, defaults the dropdown to the hangar with the largest existing allocation, and on OK consolidates the split into a single full-flight order on the chosen hangar (via `stripFlightFromCarrier` then push). Intentional — splits belong on the per-flight dialog.
- **Re-edit from per-flight after carrier-side set a full dock**: `hangarDock`'s pre-fill machinery already reads `pendingDockOrders` regardless of who wrote it; the splitter inputs pre-fill from the bulk-queued count and `replaceDockOrdersForFlight` strips atomically on OK. No special-casing needed.
- **Cross-carrier double-queue prevention**: the bulk dialog excludes flights already queued to a *different* carrier (`queuedOnOtherCarrier` set built from all other ships' hangars). Without this guard, the player could silently override a queue elsewhere; the server would only land the flight wherever resolved first and drop the other with a "flight already removed" fail note. Switching carriers stays on the per-flight Dock dialog, which shows every eligible carrier in one place.
- **Tooltip button mutual exclusion**: `dockFlight.condition` requires `isFighterFlight`, `recoverFlights.condition` requires the inverse — only one ever surfaces per tooltip.

What to verify:
- Right-click a carrier in same hex as 3 friendly flights → tooltip shows the new "Recover Flights" button next to "Launch Fighters".
- Click it → dialog opens with one checkbox per flight + hangar dropdown.
- Check 2 flights → live capacity readout updates; over-allocate → row pill turns red and OK shows the dim overflow state.
- OK → end-of-turn → both flights dock into chosen hangars; replay shows the dock notes.
- Queue a 3+3 split via the per-flight Dock dialog → open the carrier-side Recover dialog → flight is pre-checked with the larger-allocation hangar selected → OK consolidates into a single full-flight order on that hangar.
- Queue a full-flight dock via the carrier-side dialog → open the per-flight Dock dialog on that flight → splitter pre-fills with the bulk-queued count; adjust freely and OK → atomic replace.
- Queue a flight on carrier C1 via either path → open C2's Recover dialog → that flight is not listed (cross-carrier exclusion).
- Carrier pivoting/rolling → button disabled (same gate as Launch Fighters).

### Stage 9.1 — `DockedFighter` critical + label polish ✓ COMPLETE

Follow-up tightening after Stage 9 testing surfaced two UX gaps: docked fighters were rendering as "DESTROYED" in the flight window (sharing the legacy `DisengagedFighter` reuse from Stage 5), and the existing green DISENGAGED label was mislabelled — it's the B5W dropout mechanic, not a voluntary disengagement.

Server-side:
- New `DockedFighter` critical class in [cricialClasses.php](source/server/model/cricialClasses.php) — mirrors `DisengagedFighter`'s shape (permanent until cleared, `repairPriority = 0`) but with `description = "DOCKED"`. Autoload entry `'dockedfighter' => '/server/model/cricialClasses.php'` added manually.
- [Fighter::isDocked](source/server/model/systems/fighter.php) checks `hasCritical("DockedFighter")`; `Fighter::isDestroyed` now returns true for either disengaged or docked, so all existing `isDestroyed()` consumers transparently treat docked fighters as out-of-flight.
- `HangarOps`: extracted the common crit-applying loop into `applyFighterStateCritical($flight, $count, $critClass, $gamedata)` with thin wrappers `dockFighters()` and `disengageFighters()`. Switched the two **intentional-dock** call sites to `dockFighters`:
  - `performLand` partial-dock branch (fighters going into the hangar)
  - `syncSourceFlightsOnLaunch` (fighters left behind when a flight is split on launch)
  
  Hangar-damage paths (`evictCraftFromHangar`, `onHangarCriticalPhase` total-loss) still use `disengageFighters` since those fighters died, not docked. Worth revisiting if the damage paths should write real destruction entries instead — for now the player sees them as DROPOUT, which is approximately correct.

Client-side:
- [criticals.js](source/public/client/criticals.js): new `isDockedFighter` helper.
- [damage.js](source/public/client/damage.js): `getTurnDestroyed` for a fighter system now falls back to `DockedFighter` if no `DisengagedFighter` is present, so replay turn-of-removal lookups work for docked fighters too.
- [fleetList.js](source/public/client/UI/fleetList.js): the "Docked" row in the fleet list keeps its `.clickable` class so the player can open the flight window for a `removed=true` flight (since the ship isn't on the board to right-click). `doScrollToShip` routes removed flights to `flightWindowManager.open(ship)` directly instead of trying to scroll to a non-existent board position.
- **[FighterIcon.js](source/public/client/UI/reactJs/shipWindow/FighterIcon.js)** — the real fix lives here, since the in-game flight window is the React component (game.php), not the legacy template (gamelobby.php). Restructured so opacity/filter live on an inner `FadedContent` wrapper, with the new `OverlayLabel` as a sibling at full opacity. The label encodes state with both color and text:
  - **DOCKED** in cyan `#00b8e6` — `DockedFighter` critical present (in hangar).
  - **DROPOUT** in orange `#ff8c00` — `DisengagedFighter` critical (the B5W dropout-roll mechanic in [fighter.php::testCritical](source/server/model/systems/fighter.php#L178-L198), formerly mislabelled DISENGAGED in green).
  - **DESTROYED** in red `#ff5252` — actual structural destruction (no crit, parent `isDestroyed()`).
  
  Precedence is DOCKED > DROPOUT > DESTROYED because a docked fighter is also flagged destroyed server-side, so the labels narrow from most-specific to most-generic. Healthbar fill goes cyan when `$docked`, overriding the green/orange critical-state colors. Label has its own 1px cyan/orange/red border; the fighter card frame stays the standard `#496791` blue regardless of state.

Legacy paths kept for consistency but inert in-game (gamelobby never shows docked fighters): [flightwindow.js](source/public/client/UI/flightwindow.js) class wiring, `.dockedtext` divs in [game.php](source/public/game.php) / [gamelobby.php](source/public/gamelobby.php), and `.fighter.docked.destroyed` rules in [shipwindow.css](source/public/styles/shipwindow.css). Worth removing in a follow-up cleanup if no real consumer materialises.

What to verify:
- Partial-dock of 3 from 6 → the docked 3 in the flight window show faded icon + crisp cyan **DOCKED** label + cyan healthbar; the active 3 render normally.
- Partial-launch of 3 from a 6-fighter docked flight → click the source flight's "Docked" row in the fleet list → window opens → 3 of 6 fighters show cyan DOCKED, the other 3 still active.
- Fighter that fails a dropout roll in combat → shows orange **DROPOUT** instead of green DISENGAGED.
- Fighter destroyed by actual damage → shows red **DESTROYED** as before.

---

### Stage 14 — Reloadable weapons rearm while docked ✓ COMPLETE

**Goal.** A flight sitting docked in a hangar slowly rearms its limited-ammo weapons — 1 round/turn, up to the weapon's starting load. First pass covers the matter fighter guns: `SlugCannon`, `PairedGatlingGun`, `LtGatlingGun`. Missile rearm (needs purchased ordnance) is deferred to Stage 15.

**Hook.** New `ShipSystem::whileDocked($flight, $carrier, $hangar, $gamedata)` — default no-op ([ShipSystem.php](source/server/model/systems/ShipSystem.php), after `onConstructed`). Docked flights are `removed`, so they're dropped from `Criticals::setCriticals`'s `$activeShips` ([criticals.php:42-44](source/server/handlers/criticals.php#L42-L44)) and never self-tick — the live carrier's `Hangar::criticalPhaseEffects` is the only thing that processes them each turn. `HangarOps::serviceDockedFlights($hangar, $carrier, $gamedata)` walks the hangar's `dockedFlightId` stash entries, skips any docked THIS turn (`dockedTurn >= currentTurn` — the "full turn" rule), resolves each flight, and calls `whileDocked()` on every subsystem of every still-active fighter.

**Reload logic.** `Weapon::whileDocked()` (base, [weapon.php](source/server/model/weapons/weapon.php) after `setAmmo`) gated on a new `public $reloadable` property (0 = never reloads; >0 = base/starting ammo). It reloads `min($max, ammunition + 1)` where `$max = $reloadable + EXT_AMMO bonus` (summed from `$flight->enhancementOptions`), and persists via `Manager::updateAmmoInfo` saving `ammunition − bonus` — `setEnhancementsShip` re-adds the bonus on every load, exactly as `fire()` already does. The three weapons set `$reloadable = 6`.

**Order in `Hangar::criticalPhaseEffects`:** eviction → dock → **serviceDockedFlights** → launch.

**Files**: [ShipSystem.php](source/server/model/systems/ShipSystem.php), [weapon.php](source/server/model/weapons/weapon.php), [matter.php](source/server/model/weapons/matter.php), [HangarOps.php](source/server/model/systems/HangarOps.php), [baseSystems.php](source/server/model/systems/baseSystems.php).

**Two bugs found in first testing (2026-05-21), both fixed:**
1. *Relaunch the turn after docking didn't reload.* `serviceDockedFlights` ran AFTER `processLaunchOrders`, so the launch consumed (and removed) the stash entry before servicing saw it. Fixed by moving servicing **before** launches: a flight that spent the turn docked now earns its reload and carries it out on the relaunch turn.
2. *Split relaunch (6 → 3+3) reset ammo to max.* A split launches via the new-spawn path (`resurrectDockedFlight` rejects the size mismatch), which builds fresh full-ammo fighters. `copyFighterStateToTarget` transferred damage + crits but **not** ammo. Fixed by also copying scalar weapon ammo there (subsystems pair by order, persisted via `updateAmmoInfo`). Exact-size full relaunch was always correct because `resurrect` reuses the same ship object.

**Verify:**
- SlugCannon flight fires to 3/6, docks, waits a turn → 4/6; relaunch → the reloaded count carries out.
- The dock turn itself never reloads (full-turn gate).
- 6-flight at 3/6 docked, split-launched 3+3 → both splits at the depleted+rearmed count, not 6/6.
- Docked flight on a destroyed carrier → no reload (carrier dropped from `$activeShips`).
- Known limitation: a runtime-spawned split flight has no `enhancementOptions`, so it can't re-cap at base + EXT_AMMO — copied ammo may briefly exceed its own reload cap if the source had EXT_AMMO. Cosmetic; revisit if it bites.

---

### Stage 15 — Purchased missile reserve (HANG_ORD) ✓ COMPLETE

**Goal.** Extend Stage 14's reload mechanic to fighter **missiles**. Unlike matter rounds (free, capped at the starting clip), replacement missiles are a finite, pre-purchased resource. Per user answers (2026-05-24): **1 missile per fighter per turn**, fully automatic (the docked fighter's own `AmmoMagazine` enhancements drive what gets restocked — no in-game allocation menu needed), pool denominated in **missile-CP** (a restocked AmmoMissileFH costs its `enhancementPrice` PV from the pool), pool **shared per carrier**, **stored on the primary hangar's note**.

**Final design — carrier pool + per-fighter automatic restock.**
- **Purchase (lobby):** new `HANG_ORD` enhancement at 1 CP per point (limit 60), gated to carriers in factions that field missile-equipped fighters. The enhancement count IS the pool capacity — no separate scalar on the carrier; `HangarOps::reloadPoolCapacity($carrier)` reads it directly from `$ship->enhancementOptions` on every call.
- **Persistence:** spent total lives on the carrier's primary (first) hangar as `$reloadPoolSpent` (int), persisted via a `hangarOrdReserve` IndividualNote on that hangar — same change-detection pattern `hangarUsage` uses (Stage 1). One note per carrier, lifetime-cumulative.
- **Rearm (`AmmoMagazine::whileDocked`):** runs as part of `HangarOps::serviceDockedFlights`. Walks the docked FLIGHT's `enhancementOptions` for `AMMO_FB/FL/FH/FY/FD/DUM` entries — these mirror what `setEnhancementsFighter` originally loaded into every fighter's magazine. For each entry the magazine carries and is below the starting per-fighter count (`enhCount`), builds a candidate sorted by `enhancementPrice` DESC. Most-expensive-first, the loop calls `HangarOps::drawReload($carrier, $price)`; on success it bumps `ammoCountArray[mode]++`, decrements `ammoUsedTotal[mode]--`, writes an `AmmoReplenished` note attached to `$flight->id`/`$magazine->id`, and returns. **1 missile per fighter per turn** is enforced by the early-return.
- **Note replay:** `AmmoMagazine::onIndividualNotesLoaded` gains an `AmmoReplenished` case that mirrors `AmmoUsed` in reverse: `ammoCountArray[mode] += 1; ammoUsedTotal[mode] -= 1`. The load order is `addAmmoEntry(0)` in ctor → `onIndividualNotesLoaded` (Used/Replenished net) → `setEnhancementsFighter` `addAmmoEntry(enhCount)` later, giving the correct "starting − used + restocked" final count.

**Implementation pieces.**

Server-side [HangarOps.php](source/server/model/systems/HangarOps.php) — new section "Stage 15: ordnance reload pool":
- `reloadPoolCapacity($carrier)` — reads `HANG_ORD` count from `enhancementOptions`. Re-derived each call; no persistence.
- `reloadPoolSpent($carrier)` — primary hangar's `$reloadPoolSpent`.
- `reloadPoolRemaining($carrier)` — capacity − spent.
- `drawReload($carrier, $cost)` — if `remaining >= cost`, increment primary's `$reloadPoolSpent`, return true.
- `primaryHangar($carrier)` — first hangar in encounter order (matches `populateInitialHangarUsage`'s convention).

Server-side [baseSystems.php](source/server/model/systems/baseSystems.php) Hangar class:
- New `$reloadPoolSpent = 0;` + private `$lastSavedOrdReserve = null;` change-detection snapshot.
- `onIndividualNotesLoaded` reads `hangarOrdReserve` notes → `$reloadPoolSpent`.
- `generateIndividualNotes` Stage 15 block written **before** the `hangarUsage` early-return guard — this ordering is critical: while fighters sit docked with stable hangarUsage the guard fires before reaching the ordnance block; the fix is to persist the pool state first.
- Stage 15 block writes a `hangarOrdReserve` note ONLY on the primary hangar AND only when `$reloadPoolSpent` changed. Guard: `!($this->lastSavedOrdReserve === null && (int)$this->reloadPoolSpent === 0)` — mirrors the existing `hangarUsage` null/empty guard to prevent POST-side ship reconstruction (see below) from writing spurious "0" notes.
- `stripForJson` emits `reloadPoolCapacity` + `reloadPoolSpent` on the primary hangar so the client can render remaining without re-deriving enhancement totals.

Server-side [baseSystems.php](source/server/model/systems/baseSystems.php) AmmoMagazine class:
- New `whileDocked($flight, $carrier, $hangar, $gamedata)` — full algorithm above; uses static `$ammoMap = ['AMMO_FB' => 'AmmoMissileFB', ...]` to instantiate the right ammo class for `enhancementPrice` lookup.
- `onIndividualNotesLoaded` extended with `AmmoReplenished` case.
- `notekey_human` string for `AmmoReplenished`/`AmmoUsed` notes is `'Ammunition Magazine - a round restocked'` / `'Ammunition Magazine - a round is drawn'` — kept ≤ 39 chars to fit the `notekey_human` `varchar(40)` column.

Server-side [Enhancements.php](source/server/model/ships/Enhancements.php):
- `blockStandardEnhancements` adds `'HANG_ORD'` to the disabled list (non-standard sets opt-out by default).
- `setEnhancementOptionsShip` registers `HANG_ORD` after `HANG_MSW`. Eligibility: faction must be in `$missileFactionWhitelist` AND the ship must have at least one combat-fighter hangar slot (i.e. `array_sum` of `$ship->fighters` keys `normal/heavy/medium/light/ultralight` > 0). This correctly excludes pure-shuttle carriers and ships with only default-shuttle leftover capacity.
- Faction whitelist: `Earth Alliance`, `Earth Alliance (Early)`, `Centauri Republic`, `Centauri Republic (WotCR)`, `Narn Regime`, `Dilgar Imperium`, `Orieni Imperium`, `Gaim Intelligence`, `Hurr Republic`, `Cascor Commonwealth`, `Kor-Lyan Kingdoms`, `Belt Alliance`, `Rogolon Dynasty`, `Civilians`, `Raiders`, `Custom Ships`, plus `(defenses)` variants. Future missile-faction additions: add the `$this->faction` string to `$missileFactionWhitelist` in `Enhancements.php`.
- `setEnhancementsShip` adds a documented no-op `case 'HANG_ORD':` — the pool is consumed lazily via `enhancementOptions` reads at runtime; no `$ship` mutation needed.

Client-side [lobbyEnhancements.js](source/public/client/lobbyEnhancements.js):
- `setEnhancementsShip` case 'HANG_ORD' → appends "Extra Ordnance Reserve (N pts)" to the lobby ship notes, sets `ship.hangOrdEnh = true`.
- `resetEnhancementMarkersShip` case 'HANG_ORD' → clears the marker.

Client-side [baseSystems.js](source/public/client/model/system/baseSystems.js) Hangar tooltip:
- `refreshHangarTooltip` adds an "Ordnance Reserve: X / Y pts" line below "Capacity" when `reloadPoolCapacity > 0` (primary hangar only; non-primary hangars have `reloadPoolCapacity === undefined` and skip the line).

**Faction whitelist.** Sourced by grepping `FighterMissileRack`/`FighterTorpedoLauncher` references under `source/server/model/ships/` excluding non-B5 (BSG, Nexus, StarWars, Escalation*), then mapping directory names to `$this->faction` strings. Reference [project_fieryvoid_map.md](C:\Users\Dougie\.claude\projects\c--FV-env-FieryVoid\memory\project_fieryvoid_map.md).

**Why the magazine, not the launcher.** Modern missile fighters (post-Valkyrie pattern) use `AmmoMagazine` + `AmmoFighterRack`/`AmmoMissileRack*` — the magazine is the source of truth for `ammoCountArray[$modeName]` and the launcher pulls from it. Legacy `FighterMissileRack` fighters carry their own `$missileArray[$mode]->amount` and DON'T use enhancements — they're not in scope for HANG_ORD since the design hinges on the fighter's AMMO_F* enhancements as the guide for what's restockable.

**Bugs found and fixed during testing (2026-05-24).**

*1. Eligibility check too broad (initial implementation).* The first version gated HANG_ORD on `$ship->systems instanceof Hangar` — meaning any ship carrying a Hangar would see the option, including dedicated shuttle carriers and ships whose only hangar is a default-shuttle pool. Changed to the fighter-slot check (`array_sum` of combat-fighter keys `normal/heavy/medium/light/ultralight`), which correctly limits the option to ships that actually carry missile-capable fighters.

*2. `notekey_human` column overflow.* First `AmmoReplenished` notes used the string `'Ammunition Magazine - a round is restocked from carrier reserve'` (60 chars). The `tac_individual_notes.notekey_human` column is `varchar(40)`. DB threw: `(22001/1406): Data too long for column 'notekey_human' at row 1`. Fixed by shortening to `'Ammunition Magazine - a round restocked'` (39 chars) and `'Ammunition Magazine - a round is drawn'` (38 chars).

*3. Ordnance reserve not updating between turns.* The Stage 15 persist block was placed after the `hangarUsage` early-return guard in `generateIndividualNotes`. While fighters sat docked with a stable hangar state, `hangarUsage` was unchanged so the function returned early before reaching the ordnance block — the pool appeared to reset to 0 each turn without updating. Fixed by moving the Stage 15 block **before** the guard.

*4. POST-side ship reconstruction wrote spurious "0" notes (the pool-reset bug).* `Manager::getShipsFromJSON` reconstructs ship objects from POST JSON with `$lastSavedOrdReserve === null` and `$reloadPoolSpent === 0`. The change-detection `$currentOrd !== $this->lastSavedOrdReserve` comparison fired (`"0" !== null`) and wrote a "0" note at every player submission (DeploymentGamePhase, MovementGamePhase, InitialOrdersGamePhase). These "0" notes became the newest DB entry and overwrote the authoritative "16" value written by the Fire Phase advance. SQL forensics confirmed the pattern: turn N, phases 1/2/3 each wrote "0", phase 4 wrote "16". Pool appeared correct inside a turn but reset to 0 at the start of the next. Fixed with the null/default guard: `!($this->lastSavedOrdReserve === null && (int)$this->reloadPoolSpent === 0)` — mirrors the existing `hangarUsage` guard. See also [[arch-post-side-ship-reconstruction]] memory.

*5. Split-launch lost reloaded missiles.* After pool spending restocked missiles in a docked flight, partial relaunch via `consumeStashesForLaunch` selected fighters in the old priority order (damage DESC, index DESC) then tried to copy AmmoMagazine state — but the `copyFighterStateToTarget` function only copied weapon (non-magazine) ammo, not `ammoCountArray`. Two root causes:
  - **Selection order**: restocked fighters don't have higher damage, so the selection priority didn't reliably put them in the launched flight. Fixed by adding missile count as the first sort key: `ammo DESC → damage DESC → index DESC`. New `countLoadedMissiles($fighter)` helper sums `ammoCountArray`.
  - **Missing AmmoMagazine copy**: `copyFighterStateToTarget` only handled `Weapon`-subclass scalar ammo (Stage 14). Extended with a new AmmoMagazine block that: (a) calls `copyFlightAmmoEnhancements` to stamp AMMO_F* enhancements onto the target flight so magazines initialise correctly, then (b) walks paired `AmmoMagazine` subsystems and writes per-mode delta notes (`AmmoReplenished`/`AmmoUsed`) onto the target magazine to bring its count in line with the source's.
  - New `copyFlightAmmoEnhancements($sourceFlight, $targetFlight, $gamedata)` helper: copies AMMO_F* enhancements from source to target via `Manager::insertSingleEnhancement`, appends to `$targetFlight->enhancementOptions`, and calls `addAmmoEntry` on each target magazine (so the magazine's baseline initialises before the delta notes are applied).
  - `consumeStashesForLaunch` calls `copyFlightAmmoEnhancements($fragment, $launchedFlight, $gamedata)` once per launched flight before the per-fighter copy loop. `spawnFragmentFlight` also calls it so new fragments carry the right magazine baselines.

**Verify:**
- EA fleet with a Decurion carrier (HANG_ORD=20) + a Valkyrie flight (AMMO_FB=2). Fire all 12 Basic missiles, fully dock the flight. Wait one turn → hangar tooltip updates: pool spent 16 pts (2 fighters × 8 pts/Basic), 2 magazines restocked by 1. Remaining pool: 4 pts. Following turn: pool insufficient to restock another Basic (8 pts); pool stays at 16/20.
- HANG_ORD=48 → first full-turn-docked tick restocks 6 Basics (one per fighter × 8 pts = 48 pts), pool now 48/48. No further reloads possible — pool depletes one-way over the game.
- Mixed AMMO_FB + AMMO_FH flight (Basic 8 PV, Heavy higher): priciest-first sort restocks one Heavy first; if the pool can't afford the Heavy but can afford a Basic, it falls through to the Basic.
- Docked flight that spent a turn in hangar → relaunched as a split: the launched fighters include those with the restocked missiles (missile-count-first selection order).
- A non-missile-faction carrier (Minbari, Vorlon) → HANG_ORD doesn't appear in the lobby option list.
- A pure-shuttle carrier with no combat-fighter fighter-slots → HANG_ORD doesn't appear.
- Hangar primary destroyed: `serviceDockedFlights` early-returns on destroyed hangar, no reloads happen.
- Carrier destroyed: docked flights drop out of `$activeShips`; no reloads.
- Page reload mid-game: tooltip "Ordnance Reserve: X / Y pts" reflects post-restock state via `hangarOrdReserve` note.

**Known limitations / out of scope:**
- No mid-game allocation menu (deliberately removed — the fighter's own AMMO_F* enhancements drive what gets restocked, prioritised most-expensive-first automatically).
- Pool is one-way: spent points are gone for the game. No "refuel at base" mechanic.
- Partial-flight-still-in-space variant (the "stays out" case) remains out of scope — reload only applies while the flight is fully docked (`dockedFlightId` entry).
- Legacy `FighterMissileRack`-only fighters (pre-AmmoMagazine pattern) don't benefit from HANG_ORD; those use fixed loadouts via `missileArray[$mode]->amount` with no enhancement-driven config.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php), [baseSystems.php](source/server/model/systems/baseSystems.php), [Enhancements.php](source/server/model/ships/Enhancements.php), [lobbyEnhancements.js](source/public/client/lobbyEnhancements.js), [baseSystems.js](source/public/client/model/system/baseSystems.js).

**Relation to §7.** This supersedes the out-of-scope line "Reload ordnance from hangar partial-flight rule" for the full-flight-docked case; the partial-flight-stays-out variant remains out of scope.

---

### Stage 16 — Catapults (single-fighter superheavy launchers) — DESIGN SKETCH

**Goal.** Extend the whole HangarOps pipeline (capacity tracking, launch, land, damage, replay) to the `Catapult` system. A catapult is a hangar variant that holds exactly **one** superheavy fighter and behaves like a fixed, forward-firing launch rail. §2.1 already anticipated this ("`Catapult` should subclass / inherit the same fields … reuse the launch path") and Stage 12's *Catapult parity caveat* flagged the client-side deep-clone fix that lands with this work.

Today both `Catapult` classes are thin stubs that do nothing with HangarOps — server [baseSystems.php:3317](source/server/model/systems/baseSystems.php#L3317) (`extends ShipSystem`, `$name = "catapult"`, `$output` defaults to 1) and client [baseSystems.js:480-484](source/public/client/model/system/baseSystems.js#L480-L484) (`Object.create(ShipSystem.prototype)`).

**Behavioural differences from a Hangar (from the user's rules).** These are the things that make Catapult more than `class Catapult extends Hangar {}`:

1. **Counts toward the ship's hangar capacity, but only takes superheavy fighters.** A catapult contributes to the carrier's overall hangar allowance in the fleet builder (it's what lets the ship mount a superheavy fighter), and at runtime it stores craft only in the `superheavy` category.
2. **No initiative penalty for launching.** A catapult launch applies neither the `LaunchedThisTurn` (−50 on the flight) nor the `HangarOperations` (−20 on the carrier) crits that a Hangar launch applies via `applyLaunchCrits` (Stage 6).
3. **Multiple boxes, one fighter; extra boxes do NOT make default shuttles.** A catapult is e.g. a 3-box system but holds a single superheavy fighter — the extra boxes are structural HP only. Crucially, catapult boxes must be **excluded** from the leftover-capacity → auto-shuttle pool that `HangarOps::populateInitialHangarUsage` (step 3) and `HangarOps::getDefaultShuttles` compute. Only **Hangars** contribute default shuttles.
4. **Fixed forward launch; rear-only landing; single fighter type.** A catapult always launches at carrier facing (`direction = 0`, never side-launched per Stage 8). A fighter may only **land** if it enters the carrier's hex from the **rear**. And the catapult services exactly one fighter type — no other craft can launch from or land on it.
5. **Launch/land work regardless of catapult damage** — there is no `$output` budget gate and a destroyed/damaged catapult still launches and recovers. **But** a fighter landing on a *damaged* catapult takes damage equal to the number of marked boxes on the catapult. If that destroys the fighter, it still completes the landing (counts as recovered / stored) but **cannot launch again**.

The five differences map cleanly onto five independently-mergeable sub-stages. Recommended order **16.1 → 16.5** because each builds on the prior (data model → population → launch → land gating → land damage), but 16.5 is the only one that introduces genuinely new mechanics; 16.1–16.4 are mostly "teach the existing HangarOps call sites that a Catapult is a constrained Hangar."

---

#### 16.1 — Catapult inherits the HangarOps data model ✓ IMPLEMENTED (pending Docker test)

**Goal.** Make `Catapult` a first-class HangarOps participant so the Stage 1 note pipeline (`onIndividualNotesLoaded` / `generateIndividualNotes` / `stripForJson` / `criticalPhaseEffects`) drives it, without touching the dozens of `instanceof Hangar` sites individually.

**Design.** Reparent the server `Catapult` from `ShipSystem` to `Hangar`. It inherits every HangarOps field (`$hangarUsage`, `$spawnableClasses`, `$launchedThisTurn`/`$landedThisTurn`, the note hooks) and the whole criticalPhaseEffects launch/dock/service loop. Override only what differs:

```php
class Catapult extends Hangar {
    public $name        = "catapult";
    public $displayName = "Catapult";
    public $isCatapult  = true;        // single discriminator for HangarOps branch points
    public $hangarType  = 'superheavy';
    public $direction   = 0;           // fixed forward — never side-launched (Stage 8 skips it)
    public $primary     = false;
    public $repairPriority = 1;
    // $output is irrelevant for launch/land gating (see 16.3/16.4) but keep the
    // 3-arg ctor so existing ship files (new Catapult($armour,$maxhealth,$output))
    // keep working unchanged.
    function __construct($armour, $maxhealth, $output = 1){
        parent::__construct(0, $armour, $maxhealth, $output); // match Hangar ctor arg order
    }
}
```

- **Verify the constructor arg order** against the real `Hangar::__construct` before committing — Stage 5 round 3 documented that `Hangar` is `($direction, $maxhealth, $powerReq, $hangarType)`-ish and that legacy ships pass odd positional args; the Catapult ctor must forward correctly and keep `$hangarType = 'superheavy'` (don't let it get coerced to `'fighters'` by the Stage 5 `inferHangarType` hardening — `isCatapult` should short-circuit `inferHangarType`).
- **`instanceof Hangar` audit.** Because `Catapult extends Hangar`, every `$sys instanceof Hangar` site in HangarOps now also matches catapults. That's mostly what we want (note population, launch/dock processing) — but the capacity/shuttle-pool sites in 16.2 must explicitly *exclude* `isCatapult`. Grep all `instanceof Hangar` and `getSystemsByName('Hangar')` / `'hangar'` usages and classify each as "include catapults" or "exclude catapults"; this audit is the bulk of 16.1's risk.
- **Client mirror.** Reparent client `Catapult` from `ShipSystem.prototype` to `Hangar.prototype` so it inherits `refreshHangarTooltip`, `doIndividualNotesTransfer`, and the pending-order hydration. **Apply the Stage 12 deep-clone fix in the Catapult constructor** (`this.data = JSON.parse(JSON.stringify(this.data))`) — per the Stage 12 *Catapult parity caveat*, the shared-reference trap will otherwise bite any pair of same-phpclass catapult carriers. See [arch_client_system_shared_reference.md](C:\Users\Dougie\.claude\projects\c--FV-env-FieryVoid\memory\arch_client_system_shared_reference.md).

**Files**: [baseSystems.php:3317](source/server/model/systems/baseSystems.php#L3317), [baseSystems.js:480-484](source/public/client/model/system/baseSystems.js#L480-L484), [HangarOps.php](source/server/model/systems/HangarOps.php) (the `instanceof` audit), [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js) + [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js) (mirror the audit on the client iteration helpers).

**Verify**:
- A carrier with a Catapult opens in `safeGameID` → catapult shows a "Carrying" tooltip (inherits `refreshHangarTooltip`) instead of the bare system-info it shows today.
- `hangarUsage` persists across reload on a catapult exactly like a hangar (Stage 1 round-trip).
- A ship with both a Hangar and a Catapult populates each independently (no double-count); the Stage 2.3 idempotency guard still fires once per ship.

---

#### 16.2 — Initial population: catapult starts empty, zero shuttle contribution ✓ IMPLEMENTED (pending Docker test)

**Goal.** A catapult starts holding exactly one superheavy fighter (its designated type), and its boxes never spill into the auto-shuttle pool.

**Design.**
- **Catapult starts EMPTY at turn 1.** A superheavy fighter is a combat unit, so — exactly like any medium/heavy fighter — it auto-deploys to space at turn 1 via the existing fleet-builder flow, NOT pre-filled into the catapult. (Combat-fighter categories are never auto-filled by `populateInitialHangarUsage`; only shuttle categories are.) The fighter is recovered into the catapult in-game (16.4), or deploy-docked into it during the Deployment Phase (Stage 7 path). So 16.2 is **exclusion-only** — there is no "fill one superheavy" step.
- **Shuttle-pool exclusion.** Partition catapults out of the hangar list `populateInitialHangarUsage` works with: catapult boxes are excluded from the leftover-capacity total AND never auto-filled with shuttles. Additionally, when the ship has a catapult, the `superheavy` category is skipped in the `$totalDeclared` accumulation (it's catapult-destined, so it must not shrink the *real* hangars' leftover shuttle fill). Catapult-less ships are unaffected — and excluding catapults reproduces the PRE-Stage-16 behaviour, where catapults weren't `instanceof Hangar` and so were never in `collectHangars` / the shuttle pool anyway.
- **Client `getDefaultShuttles` / `getDefaultShuttleComposition`** ([systems.js](source/public/client/systems.js)) need the same exclusion so the lobby loadout and in-game tooltip don't credit catapult boxes as default-shuttle slots. **Landed (2026-05-22 follow-up).** `getTotalHangarCapacity` already excluded catapults (filters `name == "hangar"`), but both shuttle-pool functions summed *all* `ship.fighters` into `declared`, so a catapult ship's `superheavy => 1` ate one default-shuttle slot (Drazi Stormfalcon showed 1 shuttle instead of 2). Extracted a single `getShuttlePoolDeclared(fighters, ship)` helper that skips `superheavy` when `shipHasCatapult(ship)` (new helper), mirroring the server. Both `getDefaultShuttles` and `getDefaultShuttleComposition` now call it.

**Resolved decision (2026-05-22):** a catapult knows its fighter type via the ship file setting `$catapult->spawnableClasses = ['someSuperheavyFtr']` (single entry) — explicit, mirrors Stage 4b, and doubles as the launch/landing-eligibility key for 16.4. (Not yet consumed in 16.2 since the catapult starts empty; it becomes load-bearing in 16.3/16.4.)

**Open item flagged for the user:**
- **Fleet-builder capacity.** Difference #1 says catapults count toward hangar capacity for purchase purposes. `gamelobby.js::checkChoices` may not currently count catapult boxes toward superheavy capacity — superheavy fighters look like a fleet-builder gap. Out of scope for 16.1/16.2 (no superheavy fighters exist in the fleet builder to test against yet); revisit when a real superheavy-fighter + catapult ship is built.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php), [systems.js](source/public/client/systems.js) (shuttle-pool declared-fighter exclusion), [systemInfo.js](source/public/client/UI/systemInfo.js) (lobby Capacity fold). *(gamelobby.js `checkChoices` superheavy/catapult capacity counting still deferred — see the flagged fleet-builder item.)*

**Lobby "Capacity:" now previews default shuttles (2026-05-22 follow-up).** The fleet-selection Hangar tooltip ([systemInfo.js](source/public/client/UI/systemInfo.js), lobby-only) rendered the server blueprint's `Capacity` verbatim — which reads `0 / 14` pre-game because `hangarUsage` is empty — then listed the auto-filled shuttles as separate rows. Now, on the default-shuttle hangar, the Capacity line folds the non-`slotOnly` default-shuttle count into its stored number, so a 14-box hangar carrying 12 (auto-deploying) fighters + 2 default shuttles reads `2 / 14 slots` (combat fighters auto-deploy to space, so only the shuttles sit in the hangar in-game — matching the live tooltip). In-game (React `SystemInfo.js`) needs no change: `refreshHangarTooltip` already computes Capacity from the live `hangarUsage`, which holds the shuttles.

**Verify**:
- Carrier with a 3-box catapult → catapult tooltip reads `0 / 1 slots` (empty; one logical slot, not 3 boxes); the extra boxes do NOT add shuttles anywhere.
- Same carrier with a regular 14-box hangar declaring `["medium"=>12]` → still auto-fills 2 shuttles from the *hangar's* leftover; the catapult contributes nothing to that pool.
- Existing catapult-only ships (Drazi Strikehawk, Thirdspace Carrier, etc.) load unchanged: catapult shows `0 / 1`, no shuttles created from catapult boxes, no launch/dock buttons (16.3+).

**Implementation notes (2026-05-22, implemented — pending Docker test).** 16.1 + 16.2 landed together because reparenting (16.1) immediately subjects every catapult to the inherited population path, so the 16.2 exclusions must ship in the same commit. Server `Catapult` now `extends Hangar` with `$isCatapult = true`, `$hangarType = 'superheavy'`, fixed `direction = 0`; its 3-arg ctor `($armour, $maxhealth, $output)` forwards to the Hangar ctor unchanged so all ~10 existing catapult ship files keep working. Client `Catapult` now `extends Hangar` (inherits the Stage 12 deep-clone, pending-order hydration, `refreshHangarTooltip`), sets `isCatapult` client-side, and re-runs `refreshHangarTooltip` so capacity renders as `/ 1`. `Hangar::stripForJson` ships `isCatapult`; `setSystemDataWindow` + client `refreshHangarTooltip` display `/ 1` (and surface `(N destroyed)` for the 16.5 landing-damage rule). `populateInitialHangarUsage` partitions catapults into a `$shuttleHangars` subset used for all capacity/shuttle math and skips the `superheavy` category from `$totalDeclared` when a catapult is present. **Launch/dock are deliberately NOT yet enabled for catapults**: the client launch/dock/recover/deploy-dock helpers all gate on `name === 'hangar'` (a catapult's name is `'catapult'`), so no orders can be created; `Hangar::criticalPhaseEffects` early-returns for `isCatapult` as the matching server guard. PHP lint + JS `node --check` clean (php run in `fieryvoid-php-1`).

---

#### 16.3 — Launch: no initiative penalty, fixed forward, damage-agnostic ✓ IMPLEMENTED (pending Docker test)

> Per the user's start-state decision (2026-05-22, empty catapult), 16.3 also **enables Deployment-phase docking into catapults** so the load→launch cycle is testable: the superheavy auto-deploys to space at turn 1, the player deploy-docks it into the catapult during the Deployment Phase, then launches it. In-game landing (rear-approach, landing-damage) remains 16.4/16.5.

**Goal.** Launch the catapult's fighter with no init penalty, always at carrier facing, regardless of catapult damage.

**Design.** In `HangarOps::performLaunch` (and the resurrect path), branch on `$hangar->isCatapult`:
- **Skip `applyLaunchCrits`** entirely (no `LaunchedThisTurn` on the flight, no `HangarOperations` on the carrier). Difference #2.
- **Force `direction = 0`** for the deploy MovementOrder facing (it already inherits `$hangar->direction = 0` from 16.1, but assert it so a stray ship-file override can't side-launch a catapult). Stage 8's dialog header suffix is naturally suppressed for `direction = 0`.
- **No `$output` budget gate.** The Hangar path checks `(launchedThisTurn + landedThisTurn + size) <= $output`; for a catapult, skip that check — a catapult launches its one fighter even if damaged/destroyed (difference #5, launch half). A catapult holds at most one fighter so the "one launch per turn" cap is implicit.
- **Launch eligibility ignores catapult health.** The Stage 4 client gate `hasHangarWithSpace` / `notRolling` / `notPivoting` should still apply (pivot/roll is a carrier-movement constraint, unrelated to catapult damage), but the `system.isDestroyed()` filter that hides launch on a dead Hangar must be relaxed for catapults.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php), [baseSystems.php](source/server/model/systems/baseSystems.php) (criticalPhaseEffects runs launch for catapults), [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js), [confirm.js](source/public/client/UI/confirm.js), [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js), [SystemIcon.js](source/public/client/UI/reactJs/system/SystemIcon.js).

**Verify**:
- Deployment Phase: a superheavy flight on a catapult-carrier → click the catapult (or hangar) icon → deploy-dock dialog lists it with the catapult as target → OK → flight stored in the catapult (`1 / 1`).
- Firing Phase: catapult now launchable → Launch dialog shows the stored superheavy (max 1) → OK → end-of-turn the flight relaunches at carrier facing + 0.
- Launch from a catapult → next turn the launched flight has **no** −50; the carrier has **no** −20 from this launch (a concurrent Hangar launch on the same carrier still applies its own −20).
- Launched flight appears at carrier facing + 0 regardless of any `direction` set in the ship file.
- Damage the catapult to near-destruction, then launch → launch still succeeds.
- Launch from a fully-destroyed catapult that still holds its fighter → succeeds (difference #5).

**Implementation notes (2026-05-22, implemented — pending Docker test).** Server: `effectiveCapacity($hangar)` returns 1 for a catapult (its boxes are HP only) and feeds `freeBoxesByCategory`, so a catapult holds exactly one fighter; `canLaunch` skips the destroyed-hangar gate and the output-budget gate for catapults (launches regardless of damage, no shared budget); `performLaunch` skips `applyLaunchCrits` for catapults (no −50/−20); `Hangar::criticalPhaseEffects` now runs `processLaunchOrders` for catapults (other steps — eviction, in-game dock, servicing — stay gated for 16.4/16.5); `inferHangarType` short-circuits catapults; server `getDefaultShuttles` excludes catapults/superheavy (Enhancements gating). Deploy-dock works through the existing `canDeployStartDock`/`processDeployStartTransfer` (category `'superheavy'` exact-match + capacity 1) with no catapult-specific server code. Client: catapults are now included (name `'catapult'`) in the launch collection (`hasLaunchableHangar`, `confirm.js::hangarLaunch`) and the Deployment-dock path (`DeploymentDock.js` collectors + `confirm.js::hangarDeployDock`), via shared `isDockHangar`/`effectiveHangarBoxes` helpers that cap catapult capacity at 1; `SystemIcon.js` opens the phase-appropriate dialog on a catapult-icon click. **In-game dock/recover stays hangar-only** (16.4): those client collectors keep their `name === 'hangar'` filter and the server's `processDockOrders` is not run for catapults. The dead `window.DeploymentDock` copy in `DeploymentPhaseStrategy.js` (overwritten at load by `DeploymentDock.js`) was left untouched. Type-lock is currently category-level (`'superheavy'` only); the single-designated-phpclass refinement (`spawnableClasses[0]`) is deferred to 16.4. PHP lint + JS `node --check` clean.

---

#### 16.4 — Landing: rear-approach + single-fighter-type gating ✓ IMPLEMENTED (pending Docker test)

**Goal.** A fighter may dock into a catapult only if it's the catapult's designated type AND it enters the carrier's hex from the rear. Landing is allowed regardless of catapult damage.

**Design.**
- **Fighter-type lock.** In `HangarOps::canShipReceive` / `eligibleHangarsForLanding`, when the target hangar `isCatapult`, require the docking flight's phpclass (or `customFtrName`) to match the catapult's single designated type (its `spawnableClasses[0]` from 16.2). Reuse the Stage 10.6.2 customFighter machinery if the superheavy fighter is a custom-named type; otherwise an exact phpclass match. No other craft is eligible — this is stricter than the Stage 5 size hierarchy (a different superheavy fighter is still rejected).
- **Rear-approach gate.** "Enters the ship's hex from the rear" is implemented as interpretation **(a)** (confirmed with the user, 2026-05-22): the flight's heading equals the carrier's facing — the flight is travelling the same direction the carrier points, so it overtakes the carrier from behind and slots in from the rear. Checkable from `flight->getLastMovement()->heading` vs `carrier->facing` (a single comparison, mirroring the existing same-heading dock check). It **replaces** (for catapults) the Stage 5 generic same-heading eligibility, so the client mirror in `shipTooltipFireMenu.js::collectReceivingHangars` and the carrier-side Recover dialog must use the same rule or the buttons will disagree with the end-of-turn check. (Fidelity follow-up if ever needed: gate on the flight's pre-move hex being a rear hexside — deferred, needs the pre-move hex which the dock resolution doesn't surface cleanly.)
- **Damage-agnostic landing.** Remove the `$output`-budget and `system.isDestroyed()` gates for catapult landing (difference #5, land half). A damaged or destroyed catapult still accepts its fighter — the consequences are handled in 16.5.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php) (`canShipReceive`, `eligibleHangarsForLanding`, `canDeployStartDock`), [shipTooltipFireMenu.js](source/public/client/UI/shipTooltipFireMenu.js) (per-flight Dock + carrier-side Recover eligibility mirrors), [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js).

**Verify**:
- Designated superheavy flight approaching the carrier's hex from the rear, same hex/speed-within-thrust → Dock enabled.
- Same flight approaching from the front/flank → Dock disabled (rear-only).
- A *different* superheavy fighter (or any other craft) in the catapult's hex from the rear → Dock disabled (type lock).
- Dock into a damaged catapult → still allowed (16.5 then applies landing damage).

**Implementation notes (2026-05-22, implemented — pending Docker test).** Server: `canShipReceive` gains the catapult branch — rear-approach (flight `heading` == carrier `facing` instead of the ordinary same-heading check), skips the destroyed-hangar gate, skips the output-budget gate; capacity stays 1 via `effectiveCapacity`/`freeBoxesByCategory`; type-lock is category-level (`'superheavy'` exact-match via `hangarAcceptsCategory`). `eligibleHangarsForLanding`'s exact-match loop is catapult-aware (no isDestroyed/budget for catapults). `Hangar::criticalPhaseEffects` now runs `processDockOrders` (before launch) for catapults; `performLand`/`processDockOrders` are otherwise generic (full dock, flightSize 1, links the stash entry via `dockedFlightId` so the same fighter relaunches with state intact). Client mirror: the per-flight **Dock** path (`findEligibleCarriersForDock`/`collectReceivingHangars`) and the carrier-side **Recover** path (`findEligibleFlightsForDocking`/`collectReceivingHangarsForRecover` + `confirm.js::hangarRecover`) now move the heading check **per-hangar** (catapult → carrier facing) and treat catapults as 1-slot, damage-agnostic, budget-free; the `hangarDock` splitter consumes the helper's capacity unchanged. PHP lint + JS `node --check` clean.

**Landing initiative penalty (confirmed 2026-05-22).** Only *launching* is exempt, so a catapult **recovery applies the normal carrier −20 `HangarOperations` crit** (via `performLand` → `applyHangarOperationsCrit`), same as an ordinary hangar dock — kept as implemented. The recovered flight gets no flight-side penalty (it's removed/stashed).

**Catapult labels (confirmed 2026-05-22).** All four dialog label sites now render a catapult as **"Catapult"** (or "Catapult N" when a carrier has more than one), independent of ship location, instead of the location-based "Main Hangar" etc. Catapults are counted separately from hangars so they don't inflate hangar numbering, and the `hangarLabelFor` `siblings` tally is back to hangars-only. Sites: `confirm.js::hangarLaunch` (inline), `confirm.js::hangarDock` (inline), and the shared `hangarLabelFor` used by `hangarRecover` + `hangarDeployDock`.

**16.4 follow-up fixes (2026-05-22).** Two defects found in testing:
- **Catapult tooltip didn't reflect scheduled docks / deploy-docks** (deployment OR firing phase). Root cause: commit `a090f76af` (16.4) replaced the inline `sys.name === 'hangar'` check with `isDockHangar(sys)` in the two `window.*` refresh helpers (`refreshDeploymentUIForDeployStart`, `refreshFiringHangarTooltips`) in [DeploymentDock.js](source/public/client/renderer/phaseStrategy/DeploymentDock.js) — but `isDockHangar` is defined inside the IIFE above and is **out of scope** in those outer functions, so it threw a `ReferenceError` swallowed by the helpers' fail-soft `catch`. The live tooltip refresh silently did nothing (for hangars too — the bug was masked because hangars' committed/persisted state still renders on reload, whereas a deploy-dock is client-only pre-commit so the catapult showed nothing). Fixed by inlining `(sys.name === 'hangar' || sys.name === 'catapult')` at both sites (matching the pre-16.4 style + the established per-dialog inline pattern).
- **Carrier-side Recover dialog still labelled a catapult "Main Hangar"** etc. The "shared `hangarLabelFor`" claimed above is actually **duplicated** in two function scopes; only the `hangarDeployDock` copy got the catapult branch. Added the same branch to the `hangarRecover` copy in [confirm.js](source/public/client/UI/confirm.js).

---

#### 16.5 — Landing damage on a damaged catapult; "cannot launch again" flag ✓ IMPLEMENTED (pending Docker test)

**Goal.** A fighter landing on a damaged catapult takes damage equal to the catapult's marked boxes. If destroyed, it still counts as recovered/stored but can never launch from that catapult again. If it survives the damage, it relaunches normally (with the damage persisted).

**Design.**
- **Apply landing damage.** In `HangarOps::performLand`, when the target `isCatapult` and has destroyed boxes, deal that many damage points to the **landing fighter's** Fighter subsystem. The metric is the **number of destroyed boxes** on the catapult (confirmed with the user, 2026-05-22) — i.e. `$catapult->maxhealth - $catapult->getRemainingHealth()` expressed in boxes (NOT total accumulated damage points). Reuse the Stage 10.4 damage-write path (`DamageEntry` against the stored/fragment fighter, persisted via `submitDamages`) so it survives reload and shows in replay.
  - **Destroyed-on-landing → recovered-but-locked.** If the applied damage destroys the fighter:
  - It **still counts as recovered** — write the `hangarUsage` entry as normal (the flight is `removed`/stashed), so the carrier "has" it.
  - Set a new `cannotLaunch: true` flag on that `hangarUsage` entry. `HangarOps::performLaunch` / launch-eligibility refuses to launch a `cannotLaunch` record; the launch dialog greys it out with a reason ("destroyed on landing — cannot relaunch").
  - The entry still carries combat-value 0 (the fighter is dead) — reuse the Stage 9 fleet-list folding so it doesn't show as a live stored craft, but the carrier still "holds" the wreck (for replay / for the rule that it occupies the catapult).
- **Stamp the flag everywhere a catapult stash entry is created** — `performLand` is the only landing path for a catapult (no fragment-spawn, since a catapult holds one whole fighter and partial docks don't apply to a single-fighter flight), so this is a one-site stamp. Confirm a superheavy flight is always `flightSize = 1`; if a superheavy flight can be >1 craft, decide whether the catapult accepts only single-craft flights (likely yes — "carry only one fighter").

**Resolved decisions (2026-05-22):**
- **Landing-damage metric** = number of **destroyed boxes** on the catapult (`maxhealth - remainingHealth` in box terms), not total accumulated damage points.
- **A `cannotLaunch` wreck permanently ties up the catapult** for the rest of the battle — the destroyed fighter still occupies the bay and can never launch again, so the carrier cannot recover a replacement into that catapult. No clear/free path.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php) (`performLand` catapult branch, launch-eligibility `cannotLaunch` gate), [confirm.js](source/public/client/UI/confirm.js) (launch dialog greys out `cannotLaunch` rows), [baseSystems.js](source/public/client/model/system/baseSystems.js) (tooltip shows a destroyed-but-stored craft distinctly), [fleetList.js](source/public/client/UI/fleetList.js) (combat-value folding for the wreck).

**Verify**:
- Land the designated fighter on an **undamaged** catapult → no landing damage; fighter stores at full health; can relaunch next turn.
- Land on a catapult with 2 marked boxes → fighter takes 2 damage on landing; if it survives, relaunch works and the damage persists (Stage 10.4-style).
- Land a fighter whose remaining health ≤ the marked-box count → fighter is destroyed on landing but the catapult shows it as recovered/stored; launch dialog refuses to relaunch it ("cannot relaunch"); fleet list shows the carrier holding a 0-value wreck, not a live craft.
- Replay scrub shows the landing, the damage, and (if applicable) the destruction on the correct turn.

**Implementation notes (2026-05-22, implemented — pending Docker test).** Server: `performLand` gains a catapult branch (guarded `!$partial`, which always holds for a catapult — capacity 1) that computes `$markedBoxes = maxhealth - getRemainingHealth()` (destroyed boxes, not raw damage points) and calls the new `applyCatapultLandingDamage($flight, $markedBoxes, $gamedata)`. That helper writes a `DamageEntry` (armour 0 — the box metric is compared directly against remaining health per the confirmed rule; `destroyed=true` when `$markedBoxes >= remaining`) against each recovered Fighter, flagged `updated` so it persists via `FireGamePhase::advance`'s `submitDamages` and shows in replay (Stage 10.4 pattern). It returns true only when **every** recovered craft is destroyed; `performLand` then stamps `$entry['cannotLaunch'] = true`. A fighter that **survives** the damage keeps `cannotLaunch` unset and relaunches normally with its damage intact (corrected rule, 2026-05-22). The damage timing is correct because weapon damage is applied in `fireWeapons` before `Criticals::setCriticals` → `processDockOrders` → `performLand`, so same-turn catapult damage is already in `getRemainingHealth()`. Launch gating: `canLaunch` excludes `cannotLaunch` entries from the available count (reason "craft destroyed on landing — cannot relaunch"); `resurrectDockedFlight` and `consumeStashesForLaunch` skip them (belt-and-braces). The wreck still counts toward `usageCountFor`, so `freeBoxesByCategory` reports the catapult full → `canShipReceive` rejects a replacement ("permanently ties up the catapult"). `cannotLaunch` rides in the `hangarUsage` entry JSON, so it round-trips to the client with no `stripForJson` change. Client: `shipTooltipFireMenu.js::hasLaunchableHangar` ignores `cannotLaunch` entries (no Launch button on a wreck-only catapult); `confirm.js::hangarLaunch` buckets wrecks apart and renders a greyed-out, input-less row ("destroyed on landing — cannot relaunch"); `baseSystems.js::refreshHangarTooltip` appends a "(wrecked — cannot relaunch)" suffix. **fleetList.js needed no change** — the Stage 9 fold (`ship.removed && ship.flight && combatValue === 0`) already hides the 0-value wreck flight, and its `dockedFlightId` keeps the carrier from crediting it. PHP lint + JS `node --check` clean.

---

**Commit cadence**: one commit per sub-stage, message style matching the repo:

```
Hangar Ops Stage 16.1: Catapult inherits HangarOps data model
Hangar Ops Stage 16.2: Catapult population (no shuttle pool, starts empty)
Hangar Ops Stage 16.3: Catapult launch + deploy-dock (no init penalty, fixed forward)
Hangar Ops Stage 16.4: Catapult in-game landing (rear-approach, type-lock, damage-agnostic)
Hangar Ops Stage 16.4: Catapult landing (rear-approach + type lock)
Hangar Ops Stage 16.5: Catapult landing damage + cannot-relaunch flag
```

---

### Stage 17 — Legacy ballistic missile racks reload via ordnance pool

**Goal.** Extend the docked-flight reload mechanic to the legacy ballistic missile family ([FighterMissileRack](source/server/model/weapons/missile.php#L332) and its subclasses, notably [FighterTorpedoLauncher](source/server/model/weapons/missile.php#L459)). These pre-date the `AmmoMagazine` pattern Stage 15 hooked into: each launcher carries its own ammo via `$missileArray[$mode]->amount` capped at constructor-given `$maxAmount`, persisted directly through `Manager::updateAmmoInfo` rather than via IndividualNotes. Today a Caltus, Tarza, or Razarik flight that docks with empty torpedo racks stays empty on relaunch.

Mechanism mirrors Stage 14 (per-weapon `whileDocked` driven by `HangarOps::serviceDockedFlights`), but the cost crossover from Stage 15 is what makes it distinct: every restocked round draws its purchase price from the carrier's `HANG_ORD` reload pool, so replacement torpedoes are a finite shared resource rather than a free per-launcher tick.

**Scope.** Implementation lives on the parent `FighterMissileRack` class, so every subclass inherits it — `FighterTorpedoLauncher` (Caltus / Tarza / Razarik), the base `FighterMissileRack` (gammaStarfury, gaim/dilgar/hurr fighters, BSG Vipers / Cylons, Belt Alliance, customs… ~40 ship files total). All of these are legacy ballistic launchers whose ammo lives in `$missileArray[1]->amount` with a fixed `$maxAmount` cap — they have not been migrated to the `AmmoMagazine` pattern. Fighters that have already migrated (Valkyrie line) use the Stage 15 pipeline instead and are unaffected.

**Rate limit: 1 missile per fighter per turn.** Matches the Stage 15 AmmoMagazine cadence, deliberately tighter than Stage 14's per-launcher cadence (`SlugCannon` and friends restock each cannon independently). A torpedo fighter mounting two `FighterTorpedoLauncher` instances therefore restocks one torpedo per turn shared between its racks, not two — slower restock, lower per-fighter pool drain. The choice mirrors how `AmmoMagazine` already enforces "per fighter" via the magazine being a single chokepoint.

`HangarOps::serviceDockedFlights` calls `whileDocked` per-subsystem in `$fighter->systems` iteration order. Because legacy launchers have no equivalent of `AmmoMagazine`'s single chokepoint, the **first** `FighterMissileRack` subsystem encountered does the per-turn work for the **whole fighter**: it walks every `FighterMissileRack`-class sibling launcher on the same fighter, builds a candidate list across all of them (priciest ammo first, tiebreak most-missing first), and restocks the best single candidate. A transient `$missileRackReloadedTurn` field stamped on the parent `Fighter` instance gates the remaining sibling launchers' `whileDocked` calls into early-returns for the rest of the turn. The field is not persisted (it lives only for the duration of `Criticals::setCriticals`), so the next turn's load resets the gate naturally.

**Cost source.** Each `Ammo` subclass already declares `$cost` (PV per missile — `MissileFB`/`LightBallisticTorpedo`/`LightIonTorpedo` = 8, `MissileFY` = 2). The ordnance pool draw uses this directly: `HangarOps::drawReload($carrier, (int)$ammo->cost)` returns true if the pool has the headroom, false otherwise. There is no per-faction discount path on legacy ammo (`getPrice($unit)` only exists on the modern `AmmoMissileTemplate` family); the flat `$cost` is authoritative.

**Persistence.** Same path the legacy launcher's own `fire()` already uses: `Manager::updateAmmoInfo($flight->id, $launcher->id, $gamedata->id, $launcher->firingMode, $ammo->amount, $gamedata->turn)` writes a per-turn row into `tac_ammo` (primary key includes turn, so replay scrubbing is automatic — Stage 14 pattern). No IndividualNote needed because `tac_ammo` IS the persistence layer for this family. The pool-spend side rides on Stage 15's existing `hangarOrdReserve` note machinery via the `drawReload` mutation on the primary hangar's `$reloadPoolSpent`.

**Lobby eligibility unchanged.** `HANG_ORD` already gates on `$ship->faction` being in `$missileFactionWhitelist` AND `array_sum($ship->fighters[combat keys])` > 0 — the three example torpedo factions (Cascor, Centauri, Narn) are already in the whitelist, and any carrier with a combat-fighter slot already qualifies. No `Enhancements.php` or carrier-side eligibility changes; the integration is purely the launcher-side `whileDocked` override plus the per-fighter gate.

**Files**:
- [missile.php](source/server/model/weapons/missile.php) — override `whileDocked` on `FighterMissileRack` (covers `FighterTorpedoLauncher` via inheritance). Walks fighter siblings via `$this->getUnit()` for the per-fighter rate limit; iterates each rack's `$missileArray` for candidate rounds; sorts price DESC, then most-missing DESC; calls `HangarOps::drawReload`; persists via `Manager::updateAmmoInfo`; stamps `$fighter->missileRackReloadedTurn` to gate siblings.
- No client changes required — tooltip ammo display already reads `missileArray[mode].amount` from the stripped JSON, so the restocked count surfaces on next load without ceremony. Stage 15's "Ordnance Reserve: X / Y pts" tooltip line already shows the pool state for carriers with `HANG_ORD > 0`.
- No DB / migration changes — leverages existing `tac_ammo` per-turn rows and Stage 15's `hangarOrdReserve` note.

**Algorithm (pseudocode)**:

```
function FighterMissileRack::whileDocked($flight, $carrier, $hangar, $gamedata):
    if !($flight instanceof FighterFlight): return
    if $this->isDestroyed($gamedata->turn): return
    $fighter = $this->getUnit()        # the carrying Fighter, not the flight
    if !$fighter: return

    # Per-fighter rate gate (transient, resets each turn-load).
    if !empty($fighter->missileRackReloadedTurn)
       and (int)$fighter->missileRackReloadedTurn >= (int)$gamedata->turn:
        return

    # Build candidate list from EVERY FighterMissileRack-class sibling on $fighter.
    $candidates = []
    foreach $fighter->systems as $sib:
        if !($sib instanceof FighterMissileRack): continue
        if $sib->isDestroyed($gamedata->turn): continue
        $cap = (int)$sib->maxAmount
        if $cap <= 0: continue
        foreach $sib->missileArray as $mode => $ammo:
            if !$ammo or (int)$ammo->amount >= $cap: continue
            $candidates[] = { launcher: $sib, mode: $mode, ammo: $ammo,
                              price: (int)$ammo->cost, cap: $cap }

    if empty $candidates: return
    usort price DESC, tiebreak ($cap - $ammo->amount) DESC

    foreach $candidates as $cand:
        if !HangarOps::drawReload($carrier, $cand->price): continue
        $cand->ammo->amount = min($cand->cap, $cand->ammo->amount + 1)
        Manager::updateAmmoInfo($flight->id, $cand->launcher->id, $gamedata->id,
                                $cand->launcher->firingMode,
                                $cand->ammo->amount, $gamedata->turn)
        $fighter->missileRackReloadedTurn = (int)$gamedata->turn
        return   # 1 missile per fighter per turn
```

**Interaction with Stage 10.5 (partial-launch damage/state transfer).** Stage 14 had to extend `copyFighterStateToTarget` ([HangarOps.php](source/server/model/systems/HangarOps.php)) so that ordinary `Weapon`-scalar `$ammunition` transfers across a partial-relaunch fragment (otherwise restocked matter rounds would be lost when a launched detachment spawned via the new-spawn path). The legacy ballistic case needs the equivalent fix: the launcher's `$missileArray[$mode]->amount` must be copied onto the launched fragment's paired launcher, then persisted with `Manager::updateAmmoInfo` so the next load reads the right value.

Stage 14's missile-count-first selection priority in `consumeStashesForLaunch` (`ammo DESC → damage DESC → idx DESC`) was added to ensure restocked fighters end up in the launched flight when only part of a fragment is launched. The same priority applies cleanly to legacy ballistic launchers — `countLoadedMissiles($fighter)` already sums `ammoCountArray` for AmmoMagazine fighters; extend it to also fold `$missileArray[$mode]->amount` for FighterMissileRack-class launchers so a partially-restocked legacy fighter is preferentially selected on partial relaunch.

**Faction whitelist sanity check.** The three example ships' factions and the wider `FighterMissileRack` user base:

| Family | Ships | Faction(s) | In HANG_ORD whitelist? |
|---|---|---|---|
| FighterTorpedoLauncher | Caltus | Cascor Commonwealth | Yes |
| FighterTorpedoLauncher | Tarza | Narn Regime | Yes |
| FighterTorpedoLauncher | Razarik | Centauri Republic | Yes |
| FighterMissileRack | gammaStarfury, atlasStarfury(Refit) | Earth Alliance / EA Early | Yes |
| FighterMissileRack | gaimKastaFighter, gaimKrastFighter | Gaim Intelligence | Yes |
| FighterMissileRack | thorunHeavy | Dilgar Imperium | Yes |
| FighterMissileRack | hightemplar | Orieni Imperium | Yes |
| FighterMissileRack | koethY | Hurr Republic | Yes |
| FighterMissileRack | rogolonVasturSHF | Rogolon Dynasty | Yes |
| FighterMissileRack | KaltiAM | Kor-Lyan Kingdoms | Yes |
| FighterMissileRack | doubleV | Raiders | Yes |
| FighterMissileRack | sabreFtr | Civilians | Yes |
| FighterMissileRack | medViper, LtViper | Custom Ships | Yes |
| FighterMissileRack | baStarfoxFtr, baSentinelFtr | Belt Alliance | Yes |
| FighterMissileRack | phalanM | Centauri (WotCR) | Yes (Centauri Republic (WotCR)) |
| FighterMissileRack | sorithianzolorii | Sorithian (Zolorii?) | Verify before Stage 17 lands — add to whitelist if absent |
| FighterMissileRack | various BSG / EscalationWars | non-B5 | Out of scope (those games use a separate enhancement set) |

The non-B5 families (BSG Kobol/Colonials/Cylons, EscalationWars factions) carry the legacy launcher class but live in `$blockStandardEnhancements`-style enhancement sets where `HANG_ORD` is disabled anyway. Worth a one-line verification during implementation that they don't surface `HANG_ORD` as a phantom option.

**Out of scope (for Stage 17)**:
- Mid-game allocation menu (inherits the Stage 15 design call — automatic priciest-first selection, no UI).
- Partial-flight reload (the "stays out" case where some fighters dock and some don't). Same as Stage 15 — reload only applies while the flight is fully docked via a `dockedFlightId` entry.
- Per-launcher rate (a Caltus restocking 2 torpedoes/turn). Explicitly chosen against, see "Rate limit" above.
- Pool refill mid-game. The pool is one-way, same as Stage 15.

**Verify** (Docker, against gameID 3730):
- Caltus flight (2 FighterTorpedoLaunchers, each 3-round, both LIT) on a Cascor carrier with `HANG_ORD=24`. Fire 6 torpedoes (deplete both racks). Dock the flight. Wait one turn → exactly one rack restocks +1 → pool drops by 8 pts (16/24 remaining). Following turn → the other rack (or same rack with the lower count) gets +1 — priciest-first sort with most-missing tiebreak picks the right one.
- Caltus flight at 2/3 + 2/3 docks with `HANG_ORD=8`. After one full-turn dock → +1 to one rack, pool exhausted at 0/8 remaining. Following turn — no further restock (pool empty).
- Razarik flight (2 launchers @ 2 each, both LBT) on a Centauri carrier. Same cadence; verifies non-EA factions also hit the pool.
- Tarza flight (1 launcher @ 4 LIT) on a Narn carrier. Single-launcher case still rate-limited to 1/turn (matches per-launcher rate naturally — no behaviour difference from per-fighter rate when there's only one launcher).
- Mixed-cost loadout: hypothetical fighter with one FB (cost 8) launcher and one FY (cost 2) launcher → priciest-first picks the FB rack when both are missing rounds.
- Partial launch (Stage 10.5 path): dock a 6-fighter Caltus flight, restock several rounds across multiple turns, then launch a fragment of 3. The fragment's three fighters' damage AND `$missileArray->amount` for each launcher carry through to the launched flight (verifies the Stage 14 partial-launch copy fix extended to `missileArray`).
- Replay scrub through a turn where torpedoes were restocked — torpedo count in the legacy launcher tooltip reflects the post-restock value at that turn (verifies `tac_ammo` per-turn rows + `getSystemDataForShips` `WHERE turn <= ?` query).
- A non-missile-faction carrier (Minbari) with `HANG_ORD` not even available in lobby → confirms eligibility gating unchanged from Stage 15.
- Sorithian Zolorii flight on whichever faction's carrier — confirm whitelist entry exists or add it.

**Commit cadence**:

```
Hangar Ops Stage 17: legacy ballistic missile reload via ordnance pool
```

---

### Stage 17.1 — Extra Marine Contingents pool (Breaching Pod marine reload) ✓ COMPLETE

**Goal.** Extend Stage 17's docked-reload mechanic to the [`Marines`](source/server/model/weapons/specialWeapons.php#L7481) weapon on Breaching Pods, drawing replacement marine units from a *new* ship-level pool enhancement (`MAR_CONT`) — separate from the missile-only `HANG_ORD` pool. A Breaching Pod that fires its marines, boards an enemy, then docks back into a friendly carrier with an Extra Marine Contingents allocation will re-arm one marine unit per pod per turn while docked.

**New enhancement: `MAR_CONT` ("Extra Marine Contingents").** Ship-level, placed in [`setEnhancementsShip`](source/server/model/ships/Enhancements.php) near `HANG_ORD`. Available to **all ships** (no faction whitelist, no hangar gate per design — a non-carrier ship can buy it but never triggers drawdown, since `serviceDockedFlights` iterates hangar contents). Price: **10 CP per marine**, flat. Limit: `ceil($ship->pointCost / 100)` (1% of base PV, rounded up; minimum 1).

> **Pool-unit gotcha (caught in T7 testing).** Unlike `HANG_ORD` (pool denominated in **CP**; each missile draw spends that missile's `$cost` from the budget), `MAR_CONT`'s pool is denominated in **marine units** (1 enhancement point = 1 marine, the 10 CP being the lobby purchase price paid up-front). Each in-game restock therefore draws **1**, not 10. The first iteration drew 10 per restock and failed every check against pools of <10 marines (initial test had 9 contingents → `drawMarineReload(carrier, 10)` returned false every turn). Any future reload-pool feature should explicitly state which unit the pool counts and match the per-draw cost accordingly.

**Pool persistence.** Same primary-hangar pattern as Stage 15's `HANG_ORD`/`reloadPoolSpent` machinery:
- `Hangar::$marinePoolSpent` (public int) + `Hangar::$lastSavedMarinePool` (private snapshot)
- New note key `hangarMarineReserve` written/read alongside `hangarOrdReserve` (primary hangar only, same POST-side reconstruction `null && 0` guard)
- `Hangar::stripForJson` emits `marinePoolCapacity` + `marinePoolSpent` on the primary hangar only when capacity > 0

**HangarOps helpers** mirror the `reloadPool*` quartet exactly:
- `marinePoolCapacity($carrier)` — reads `MAR_CONT` count from `enhancementOptions`
- `marinePoolSpent($carrier)` — reads primary hangar's `$marinePoolSpent`
- `marinePoolRemaining($carrier)` — capacity − spent
- `drawMarineReload($carrier, $cost)` — boolean draw, increments `$marinePoolSpent` on success

**Rate limit: 1 marine per fighter per turn.** Each Fighter inside a Breaching Pod flight gets at most one marine restocked per turn, even if a fighter ever mounts multiple `Marines` weapons. Enforced by a new transient `Fighter::$marinesReloadedTurn` field stamped after a successful restock (parallel to Stage 17's `$missileRackReloadedTurn`), reset on each turn-load because fresh Fighter objects start with the field unset.

**`Marines::whileDocked` algorithm.**

```
function Marines::whileDocked($flight, $carrier, $hangar, $gamedata):
    if !($flight instanceof FighterFlight): return
    if $this->isDestroyed($gamedata->turn): return
    $fighter = $flight->getFighterBySystem($this->id)   # Marines->getUnit() returns the
    if !$fighter: return                                # flight, not the parent Fighter

    # Per-fighter rate gate (transient, resets each turn-load).
    if !empty($fighter->marinesReloadedTurn)
       and (int)$fighter->marinesReloadedTurn >= (int)$gamedata->turn:
        return

    # Cap = Marines class default (2) + flight's EXT_MAR bonus.
    # EXT_MAR is a per-flight enhancement that adds +N to every Marines weapon
    # on the flight; cap is the post-enhancement starting load.
    $bonus = sum of EXT_MAR counts on $flight->enhancementOptions
    $cap = 2 + $bonus

    # Build candidate list across every Marines sibling on this fighter
    # (rare to have >1, but matches Stage 17 pattern for robustness).
    $candidates = []
    foreach $fighter->systems as $sib:
        if !($sib instanceof Marines): continue
        if $sib->isDestroyed($gamedata->turn): continue
        if (int)$sib->ammunition >= $cap: continue
        $candidates[] = { weapon: $sib, missing: $cap - $sib->ammunition }
    if empty $candidates: return
    usort missing DESC                                  # prioritise emptiest

    # Pool is denominated in MARINES (not CP), so draw 1 per restock.
    foreach $candidates as $cand:
        if !HangarOps::drawMarineReload($carrier, 1): return   # pool exhausted
        $cand->weapon->ammunition = min($cap, $cand->weapon->ammunition + 1)
        # EXT_MAR bonus must be subtracted before saving — bonus is re-added
        # on load by setEnhancementsFlight's EXT_MAR case (mirrors fire()
        # at specialWeapons.php:7724).
        Manager::updateAmmoInfo($flight->id, $cand->weapon->id, $gamedata->id,
                                $cand->weapon->firingMode,
                                $cand->weapon->ammunition - $bonus,
                                $gamedata->turn)
        $fighter->marinesReloadedTurn = (int)$gamedata->turn
        return   # 1 marine per fighter per turn
```

**Marines' EXT_MAR persistence quirk.** Unlike most weapons, `Marines::fire()` *subtracts* the flight's `EXT_MAR` enhancement count from `$this->ammunition` immediately before saving, because the `setEnhancementsFlight` EXT_MAR case re-adds the bonus on every load ([Enhancements.php:1738](source/server/model/ships/Enhancements.php#L1738)). `whileDocked` mirrors this pattern exactly: save `(restocked ammo) − bonus` so the next load's `setAmmo + EXT_MAR add-back` math arrives at the correct value. Without this, EXT_MAR pods would balloon their marines count by `+bonus` every reload cycle.

**Unspent pool bolsters defenders.** Marines purchased but not yet drawn from the pool act as additional defenders against boarding actions. [`BaseShip::howManyMarines`](source/server/model/ships/ShipClasses.php#L527) is extended to add `HangarOps::marinePoolRemaining($this)` to its running total (`!$this instanceof FighterFlight` guard for symmetry with the GrapplingClaw section). The client-side mirror in [`CnC.prototype.initializationUpdate`](source/public/client/model/system/baseSystems.js) adds the `MAR_CONT` enhancement count during the buying phase (when `gamedata.gamephase == -2`) so the live marine total ticks up as players buy contingents in the lobby; during play, the client reads `this.marines` (server-supplied via `howManyMarines`).

**Files**:
- [Enhancements.php](source/server/model/ships/Enhancements.php) — new `MAR_CONT` entry in `setEnhancementsShip` + empty switch case in enhancement processing
- [HangarOps.php](source/server/model/systems/HangarOps.php) — four `marinePool*` helpers
- [baseSystems.php Hangar](source/server/model/systems/baseSystems.php) — `$marinePoolSpent`/`$lastSavedMarinePool` fields, `hangarMarineReserve` note load + write (primary-only with reconstruction guard), `stripForJson` emission
- [fighter.php](source/server/model/systems/fighter.php) — `$marinesReloadedTurn` transient gate
- [specialWeapons.php Marines](source/server/model/weapons/specialWeapons.php) — `whileDocked` override
- [ShipClasses.php](source/server/model/ships/ShipClasses.php) — `howManyMarines` adds `marinePoolRemaining`
- [baseSystems.js](source/public/client/model/system/baseSystems.js) — "Marine Contingents: X / Y" tooltip line on primary hangar; CnC live-count picks up `MAR_CONT` during buying phase
- [lobbyEnhancements.js](source/public/client/lobbyEnhancements.js) — ship-notes line and tooltip-reset case for `MAR_CONT`
- [faq.php](source/public/faq.php) + [ammo-options-enhancements.php](source/public/ammo-options-enhancements.php) — player-facing rules docs

**Out of scope**:
- Mid-game allocation choice (automatic — restocks any pod below cap, emptiest first; no UI).
- Pool refill (one-way, same as `HANG_ORD`).
- Per-faction marine pricing (flat 10 CP).
- Boarding weapons other than `Marines` itself. [`CombatTransporter`](source/server/model/weapons/customTrek.php#L2802) (Trek beam-boarding variant) has parallel EXT_MAR ammo handling and could later receive the same `whileDocked` override — explicitly deferred.

**Verify** (Docker, against gameID 3730 — confirmed working in T7 test 2026-05-25):
- Centauri Scion Breaching Pod flight (2 pods, both fired marines and boarded) docks T6 into a carrier with `MAR_CONT=9`. T7 service: both pods at ammo 0 → each restocks +1 → pool drops 9 → 7, Marine Contingents tooltip shows `7 / 9`. T8 service if still docked: ammo 1 → 2, pool 7 → 5, tooltip `5 / 9`. T9: both at cap, no further draw (correct).
- Pool exhaustion: same flight on a carrier with `MAR_CONT=1`. First service draws to 0, second pod's `drawMarineReload` returns false, no restock for that pod (asymmetric restock by service order is fine — fragmenting fix isn't worth the complexity).
- EXT_MAR=1 flight: pod fires once (ammo 3 → 2, saved as 1). Dock for one full turn → restocks to 3 (saved as 2), pool drops 1.
- Pool unspent bolsters defenders: ship with `MAR_CONT=5`, no pods docked → `howManyMarines()` includes +5 in defender count; pod docks and restocks 2 marines → defender count drops by 2 (now +3).
- Buying phase live count: in the lobby, the carrier's Marine Units stat updates in real time as `MAR_CONT` count changes (CnC tooltip).
- Cap calculation: 600 PV ship → max 6 contingents; 1000 PV ship → max 10 contingents; 50 PV ship → minimum 1.

**Commit cadence**:

```
Hangar Ops Stage 17 ext: Extra Marine Contingents pool for docked Breaching Pods
```

---

### Stage 18 — Hangar craft escape from a destroyed carrier — DESIGN SKETCH

**Goal.** When a carrier is destroyed by enemy fire (NOT by a successful jump to hyperspace), give its docked fighters and shuttles a chance to scramble out of the wreck. Roll one d20 per destroyed carrier:

| Roll | Outcome |
|---|---|
| 1–5 | 0% escape — all docked craft destroyed with the ship |
| 6–10 | 25% escape (drop fractions) |
| 11–18 | 50% escape (drop fractions) |
| 19–20 | 100% escape |

The fraction is taken over the carrier's **combined** stash across every Hangar / Catapult on the ship (a carrier with 12 docked craft on a roll of 14 escapes 6). Escapees spawn at the carrier's last-known hex/heading at the carrier's last speed, facing the carrier's last facing plus the originating hangar's `$direction` (Stage 8 mechanism reused). They appear on the board the turn after destruction and suffer the standard Stage 6 `LaunchedThisTurn` −50 initiative penalty on that first turn — as if freshly launched.

**Auto-pick (no player choice).** Confirmed with the user (2026-05-25): escapee selection is **automatic**, ordered by combat value DESC (most expensive craft escape first), tiebreak by least-damaged DESC (`$flight->getTotalDamage()` ASC for `dockedFlightId`-linked entries; 0 for anonymous orphans / auto-shuttles). Auto-shuttles (`Shuttle.pointCost = 0`) are eligible but rank last and only escape when nothing more expensive is left to lose first. Rationale: the player's tabletop instinct is "save the Thunderbolts before the auto-shuttles," and a UI prompt during the next turn's Initial Orders adds round-trip latency for a choice the priority order almost always gets right. A player-override dialog is flagged for §18.6 polish if demand surfaces.

**Rule clarifications baked in.**
- d20 roll is **per carrier**, not per stash entry or per hangar.
- **Jumped carriers are excluded.** The existing [getJumpedDockedFlightIds](source/public/client/UI/fleetList.js#L399) path already preserves docked-flight CV for carriers whose `JumpEngine::hasJumped()` returns true. Stage 18 only fires when destruction is structural. Per the user's spec: *"Fighters that were on a ship that jumped to hyperspace are still not counted as destroyed for fleet combat value."*
- **Catapult contents are eligible** (Stage 16.1 made Catapult a Hangar subclass, so its stored fighter sits in `hangarUsage` exactly like a Hangar's). A Stage 16.5 wreck entry (`cannotLaunch = true`) is excluded from the eligibility list — it's already CV=0 and can never launch again.
- **Non-escaping craft are properly destroyed** for both `BaseShip::calculateCombatValue` and the [fleetList.js](source/public/client/UI/fleetList.js) row state. Today (Stage 9) a `dockedFlightId`-linked flight on a destroyed carrier inherits some of its CV through the carrier-credit; Stage 18 disengages every non-escapee, zeroing that out so the dead carrier and its dead-with-it flights both report combat value 0.
- **Deterministic d20.** Seed with `gameID * 100000 + shipID * 100 + turn` (or similar) so replay scrubbing always shows the same roll. Multiple carriers destroyed in the same turn get independent rolls because the seed varies per ship.

---

#### 18.1 — Detect destruction + roll d20

**Hook point.** Stage 10.1 split [Criticals::setCriticals](source/server/handlers/criticals.php) into Pass 1 (testCritical / dropout rolls) and Pass 2 (`criticalPhaseEffects`). Add a **Pass 3** that iterates **every** ship in the original snapshot (NOT just the surviving `$activeShips`) and runs `HangarOps::processCarrierDestructionEscapes($ship, $gamedata)` for any ship where `damageManager::getTurnDestroyed($ship) === $gamedata->turn` AND the ship is not jumped (`JumpEngine::hasJumped()`). Pass 3 must run **before** the existing Stage 9 `evictCraftFromHangar` total-loss disengagement fires on the carrier's destroyed hangar systems — otherwise eviction would disengage every linked source flight before escape can read them. See §18.3 for the eviction-suppression detail.

**New helper: `HangarOps::processCarrierDestructionEscapes($carrier, $gamedata)`.**
1. Skip if `$carrier->isDestroyed() === false`, if `getTurnDestroyed !== currentTurn`, or if the carrier successfully jumped (`hasJumped()` true).
2. Walk every Hangar / Catapult on `$carrier`. Collect all `hangarUsage` entries into a flat list, skipping `cannotLaunch` wrecks. Remember which Hangar each entry belongs to (for the `$direction` lookup at spawn time).
3. If the list is empty, no-op.
4. Roll d20: `mt_srand($gamedata->id * 100000 + $carrier->id * 100 + $gamedata->turn); $roll = mt_rand(1, 20);`. Compute `$maxEscape` per the table.
5. If `$maxEscape > 0`, sort the list (CV DESC, damage ASC) and take the first `$maxEscape`. Mark each as `_escaped = true` (transient — not persisted to the entry; used only for the eviction-suppression pass).
6. Write a `hangarEscapeRoll` IndividualNote on the carrier's primary hangar capturing `{roll, maxEscape, totalDocked, escapedNames: [...], computedAt: turn}` for replay surfacing and for `getDestroyedCarrierDockedFlightIds` on the client to render the per-flight state.
7. Hand the chosen list to `performEscapeSpawn` (§18.2). Hand the non-escapee list to a new `markStashEntriesDestroyed` (§18.3).

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php), [criticals.php](source/server/handlers/criticals.php) (Pass 3 hook), [baseSystems.php](source/server/model/systems/baseSystems.php) (`Hangar::onIndividualNotesLoaded` reads `hangarEscapeRoll`; `Hangar::stripForJson` ships it so the client can render replay state).

---

#### 18.2 — Escapee spawn

**New helper: `HangarOps::performEscapeSpawn($carrier, $hangar, $entry, $gamedata)`.** Mirrors `performLaunch`'s new-spawn / resurrect branching but bypasses every carrier-still-alive validation (no pivot/roll check, no hangar-destroyed check, no `$output` budget — the carrier is dead, none apply). Specifically:

- Source state: the carrier's **last** `MovementOrder` supplies `heading`, `facing`, `speed`, and hex. (Carrier gets no new movement after destruction, so the last order is its final state.) Add `$hangar->direction` to the facing per Stage 8.
- `dockedFlightId`-linked entries: prefer the existing `resurrectDockedFlight` path (Stage 4b) so the original flight ship-row carries through with damage / crits / matter ammo / missile state intact — same machinery Stage 10.4 / 10.5 / 14 / 15 already rely on. Clear `$flight->removed` so it renders as a live row in the fleet list.
- Anonymous orphan entries (Stage 10.5 detachments, Stage 7 deploy-dock entries, auto-shuttles): spawn a fresh `FighterFlight` via the new-spawn path (`Manager::insertSingleShip` etc.), exactly like `performLaunch` for an anonymous-orphan launch. No state transfer (there's none to transfer).
- Apply `LaunchedThisTurn` (−50 init on the escapee's next turn) via `applyLaunchCrits` — but DON'T apply `HangarOperations` (−20 on carrier). The carrier is dead, it can't suffer an initiative penalty.
- Write a `hangarEscapeEvent` pubnote per escapee for replay: `"<flight name> escaped from the wreck of <carrier name>"`.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php) (new `performEscapeSpawn`, sort helper).

---

#### 18.3 — Non-escapee destruction + eviction suppression

After §18.2 spawns the escapees, walk the **remaining** stash entries on the destroyed carrier:

- For `dockedFlightId`-linked entries, call the existing Stage 10.5 `destroyAllFighters($linkedFlight, $gamedata)` to mark every active Fighter with `DisengagedFighter`. The flight's combat value collapses to 0 and the Stage 9 fleet-list fold (`removed && flight && combatValue === 0`) hides the row.
- Anonymous orphans / auto-shuttles need no per-entry action — they live only on the destroyed carrier's `hangarUsage`. `calculateCombatValue` already returns 0 for `isDestroyed()` ships, so the carrier reports 0. The Stage 9 anonymous-orphan **carrier credit** does need a guard (see §18.4) so it doesn't keep crediting dead orphans to a dead carrier.

**Eviction suppression.** The Stage 9 `evictCraftFromHangar` total-loss branch (`onHangarCriticalPhase` → `$hangar->isDestroyed()` → `destroyAllFighters` on every linked source flight) will fire during normal `Hangar::criticalPhaseEffects` whenever the carrier's hangars are wiped along with the ship. To avoid double-disengaging (and to make sure escapees aren't disengaged before they can spawn), Pass 3 runs **before** Pass 2's Hangar processing, AND `onHangarCriticalPhase`'s total-loss branch must learn to skip entries marked `_escaped = true` (transient) or already cleared by `performEscapeSpawn`. Simplest: `performEscapeSpawn` removes the entry from `$hangarUsage` in-place after spawning, so `evictCraftFromHangar` only sees the actual non-escapees.

**Files**: [HangarOps.php](source/server/model/systems/HangarOps.php) (`markStashEntriesDestroyed` helper, in-place hangarUsage removal in `performEscapeSpawn`).

---

#### 18.4 — Fleet-list + combat-value reconciliation

- **fleetList.js**: new `getDestroyedCarrierDockedFlightIds` sibling to `getJumpedDockedFlightIds`. Collects `dockedFlightId`-linked flights whose carrier `isDestroyed && !hasJumpedNotDestroyed`. These are CV=0 (per §18.3) and render as "Destroyed" in red — they're already filtered by the Stage 9 0-CV fold for hidden rows, so usually no row appears for them. Where a row does render (e.g. a partial-damage edge case where CV is non-zero), the destroyed-carrier id-set drives the red/destroyed styling, NOT blue/docked.
- **Escaped flights** are live again — `removed` cleared in `performEscapeSpawn`, so they no longer match the docked filter and render as a normal flight row with their own CV (matter ammo / missile state preserved per §18.2's resurrect / new-spawn split).
- **Anonymous-orphan carrier credit guard.** Stage 9 added the rule "carrier fleet-list value includes the point cost of every anonymous `hangarUsage` entry." On a destroyed carrier this credit must be skipped — the orphans are dead with the ship. Either gate the credit on `!carrier.isDestroyed`, or zero the entries in `hangarUsage` post-destruction. Recommended: gate in the fleetList rendering rather than mutating server state (the entries are still useful for replay scrubbing of earlier turns).
- Server `calculateCombatValue` needs no change — destroyed carriers already return 0 via the early branch (ShipClasses.php:279-294), and the escaped-flight side gets handled by clearing `removed` and the LaunchedThisTurn crit.

**Files**: [fleetList.js](source/public/client/UI/fleetList.js), [calculateCombatValue or its renderer in fleetList.js](source/public/client/UI/fleetList.js) (orphan-credit guard).

---

#### 18.5 — FAQ update

New bullet inside `<h3 id="hangar">Hangar Operations</h3>` in [faq.php](source/public/faq.php), positioned between the "Hangar damage" bullet (faq.php:331) and "Rearming docked craft" (faq.php:334):

```html
<li><b>Carrier destruction — hangar craft may escape:</b> When a carrier is destroyed (other than
    by successfully jumping to hyperspace), some of its docked fighters and shuttles may scramble
    out before the wreck goes up. The game rolls a d20:
    <ul class="circle-list">
        <li>1–5: no craft escape.</li>
        <li>6–10: one quarter of docked craft escape (round down).</li>
        <li>11–18: one half escape (round down).</li>
        <li>19–20: all docked craft escape.</li>
    </ul>
    Escapees are auto-selected by combat value (most expensive craft escape first; auto-shuttles
    last). They appear in the carrier's final hex with its heading and speed, facing the carrier's
    final facing plus the originating hangar's launch direction, and suffer the standard −50
    Initiative penalty on their first acting turn (as if freshly launched). Craft on a carrier
    that successfully jumped to hyperspace are NOT subject to this roll — they ride along with
    the jump and retain their full combat value.</li>
```

**Files**: [faq.php](source/public/faq.php).

---

#### 18.6 — (deferred polish) Player override of auto-pick

If players want to override the highest-CV-first default — e.g. they'd rather save a damaged Thunderbolt that's about to die anyway, or sacrifice their most expensive flight to save three shuttles — a one-shot dialog could surface during the destruction-turn replay or the next turn's Initial Orders, letting the player re-select within the same `$maxEscape` count.

Mechanically this means: hold the auto-pick spawn until the player either confirms it or substitutes a different selection (deadline = end of next turn's Initial Orders; if no input, auto-pick stands). The escapees then spawn at end of N+1 instead of end of N, delaying their on-board appearance by one turn relative to the §18.2 model. Not specced in detail here — flag for revisit if testing surfaces demand.

---

**Files (Stage 18 in aggregate)**:
- [HangarOps.php](source/server/model/systems/HangarOps.php) — `processCarrierDestructionEscapes`, `performEscapeSpawn`, eligibility sort, non-escapee destruction sweep.
- [criticals.php](source/server/handlers/criticals.php) — Pass 3 hook for this-turn destroyed carriers.
- [baseSystems.php](source/server/model/systems/baseSystems.php) — `Hangar` persistence + JSON shipping for the `hangarEscapeRoll` note.
- [fleetList.js](source/public/client/UI/fleetList.js) — `getDestroyedCarrierDockedFlightIds`, destroyed-carrier rendering, orphan-credit guard.
- [faq.php](source/public/faq.php) — Hangar Operations section new bullet.

**Verify** (Docker, against `safeGameID` 3730):
- 12-craft carrier destroyed with deterministic d20 roll of 14 → 6 escapees spawn at carrier's last hex/heading at carrier's last speed, facing carrier facing + each originating hangar's `$direction`; 6 non-escapees disengaged. Fleet list: carrier row red "Destroyed" CV 0; escapees rendered as live flight rows with full CV; non-escaping `dockedFlightId`-linked flights hidden by the Stage 9 0-CV fold.
- Same carrier with roll 3 → 0 escapees; all 12 stash entries treated as destroyed; no new flight rows appear.
- Same carrier with roll 20 → all 12 escape; no stash entries left dead.
- Carrier with 10 stash entries (8 `dockedFlightId`-linked Thunderbolts + 2 auto-shuttles) destroyed with roll 12 → 5 escapees. Auto-pick takes 5 Thunderbolts (CV ~60 vs shuttle CV 0). Thunderbolts retain matter ammo / missile state via `resurrectDockedFlight`. 3 Thunderbolts + 2 shuttles dead.
- Carrier with Catapult containing a live superheavy + Hangar containing 6 fighters, destroyed with roll 18 → 50% = 3 escapees. Auto-pick takes the superheavy first (highest CV) plus 2 fighters. Catapult wreck entry (`cannotLaunch=true`) excluded from eligibility list throughout.
- Carrier with a port-side hangar (`$direction = 5`, Stage 8) destroyed → escapees from THAT hangar appear facing carrier facing + 5; escapees from the primary hangar appear facing carrier facing + 0. Stage 8 mechanism unchanged.
- Carrier successfully jumps to hyperspace → no d20 roll; existing `getJumpedDockedFlightIds` path continues to preserve docked-flight CV in orange "Jumped" rows. Verify no `hangarEscapeRoll` note is written.
- Two carriers destroyed in the same turn → independent rolls (seed includes ship ID). One can roll 2 (no escape), the other 19 (full escape) in the same turn end.
- Replay scrub through the destruction turn twice → same d20 result both times (deterministic seed).
- Escapee `LaunchedThisTurn` crit applied → −50 init on first acting turn; carrier gets NO `HangarOperations` −20 (carrier is dead).
- Edge case: carrier destroyed in same turn as a different friendly carrier in the same hex docked an inbound flight via Stage 5 — the inbound dock resolves first (Hangar `criticalPhaseEffects` Pass 2), then Stage 18 Pass 3 fires on the destroyed carrier. Independent; no cross-talk.
- Edge case: a flight scheduled to dock into a carrier that gets destroyed before the dock resolves → dock fails per existing eligibility check (destroyed hangar). The flight stays in space; no Stage 18 interaction.

**Out of scope (for Stage 18)**:
- Player override of auto-pick (see §18.6).
- Custom per-faction or per-ship escape modifiers (e.g. a "veteran crew" enhancement that adds +2 to the d20).
- Re-rolling the escape outcome if the player doesn't like it (the d20 is final).
- A "panic launch" mechanic where the carrier launches some craft pre-destruction at half capacity but with a chance to survive — distinct mechanic, not in B5W as written.

**Commit cadence**:

```
Hangar Ops Stage 18: hangar craft escape from destroyed carriers
```

---

### Stage 19 — Fighter Rails (B5W §10.1 external launch rails) ✓ COMPLETE

**Goal.** Implement B5W "Fighter Racks" — external launch rails bolted to a structure block, each carrying one fighter on the outside of the hull. Unlike a standard hangar bay, rail boxes are part of the carrier's structure block and can be destroyed by structure hits. The Raiders `StrikeCarrier` is the test ship (TT layout: 4×3-box + 2×6-box rails = 24 boxes, all on the front structure).

**Behavioural differences from a Hangar:**

| Property | Hangar | Fighter Rail |
|---|---|---|
| HP track | Own independent boxes | Part of the attached structure block — no extra HP |
| Box loss | Hangar damage | (a) 1d20 ≥16 on any structure-damage turn → whole rail destroyed; (b) full structure-block destruction |
| Launch init penalty | −50 on flight, −20 on carrier | −20 on carrier only (no day-after penalty on flight) |
| Reload cadence | Every turn (T+1) | Half-rate: T+2, T+4, … (narrow airlocks) |
| Auto-populates with shuttles | Yes (leftover slots) | No — combat fighters only, excluded from shuttle pool |
| Capacity accounting | `maxhealth` = HP and capacity | `maxhealth` = capacity only (`doCountForCombatValue = false`) |

**Design.**

`FighterRail extends Hangar` with a single `$isRail = true` discriminator, parallel to `Catapult::$isCatapult`. The entire HangarOps pipeline (initial population, launch, dock, service, Stage 18 escape) runs through the ordinary Hangar path with rail-specific branches keyed on `$isRail`. `collectHangars` matches `instanceof Hangar` so rails are included everywhere automatically; `primaryHangar` skips rails (the ordnance/marine pool note anchor must never be a structure-coupled rail).

**Box loss — the two paths:**

1. **1d20 whole-rail crit** (`HangarOps::onRailStructureDamage`, called from `Hangar::criticalPhaseEffects` rail branch). When the rail's parent structure `damageReceivedOnTurn > 0`, the owning rail (lowest-id rail on that structure — `railCritOwner`, dedup) rolls an unmodified d20. On 16–20, `pickRailToDestroy` auto-picks the rail with fewest remaining boxes, `destroyEntireRail` writes a full-`maxhealth` `DamageEntry` (RailCrit damageclass, `destroyed=true`), and `railBoxEscape` fires the escape. Replay-deterministic: the roll is persisted as a `railCritRoll` IndividualNote on the owning rail, read back in `FighterRail::onIndividualNotesLoaded` before the parent clears `individualNotes`. Cleared `hangarUsage` makes escape-spawn idempotent on replay (empty stash → 0 candidates → no double-spawn).

2. **Full structure-block destruction** (`HangarOps::onRailStructureLost`, called first in the rail branch). When `$structure->isDestroyed($turn)` is true, the owner processes all rails on the block: each non-empty rail runs `railBoxEscape` and its `hangarUsage` is cleared. Returns `true` → caller skips the 1d20 (rails gone). Carrier-death (primary structure) is owned by Stage 18 — this handles the carrier-alive-but-external-block-destroyed case only. Replay-safe via the same cleared-hangarUsage idempotency.

**Escape** reuses the Stage 18 machinery scoped to a single rail: `buildEscapeCandidates([$rail])` + `computeEscapeCount(d20)` + `performCarrierEscapeSpawns`. Escapees **do** suffer the −50 `LaunchedThisTurn` penalty (forced evacuation ≠ clean launch). Non-escapees are disengaged.

**Multi-bay deployment dock** (auto-distribute). A flight larger than any single rail (e.g. a 12-fighter flight vs 6-box rails) auto-distributes across multiple bays. Client `DeploymentDock.distributeFlightAcrossHangars` computes a greedy biggest-free-first plan and `queueDeployStartDock` pushes per-bay `{flightId, count}` orders. Server `performDeployStartDock` mirrors `performLand`'s partial→fragment branch (each slice spawns a fragment). The deployment dialog's capacity pills and overflow guard both reflect the per-bay split. The firing-phase bulk-recover dialog (`hangarRecover`) has the matching auto-distribute path for flights docked across multiple rails. Both re-edit paths (deployment and firing phase) track `bayCount` to detect a previously-distributed flight and re-edit it as auto-distribute rather than collapsing it onto one bay.

**Deployment-phase critical persistence gap** (bug found and fixed). `DeploymentGamePhase::process` did not call `submitCriticals`, so the `DockedFighter` criticals added to source-flight fighters by `dockFighters` during the split were never persisted. On reload the source flight regained its full size (a 9-flight split 3+3+3 relaunched 9+3+3=15). Fixed by adding `$dbManager->submitCriticals(...)` at the end of `process`, mirroring `FireGamePhase::advance`.

**`$structureSystem` visibility** (bug found and fixed). `ShipSystem::$structureSystem` is `protected`. HangarOps is an external static class, so `$rail->structureSystem` threw `Error: Cannot access protected property`. Fixed by adding a public `ShipSystem::getStructureSystem()` accessor; all three HangarOps call sites use it.

**Key decisions confirmed with user:**
- Rail HP lives in the section's `Structure` (front Structure stays 78, includes rail boxes). Rails carry capacity only; `doCountForCombatValue = false` prevents double-counting.
- 1d20 + full-structure-loss only (no per-box attrition).
- Carrier-side escape uses the carrier-destruction d20 table; escapees get the −50 penalty.
- Auto-distribute only for deployment/recovery dock — no per-bay UI.
- Destroying an external structure never kills the ship (only primary structure loss does). Stage 18 covers the primary-death case; `onRailStructureLost` covers the external-block-death case.

**Files changed:**
- [`baseSystems.php`](source/server/model/systems/baseSystems.php) — new `FighterRail extends Hangar` class; `Hangar::criticalPhaseEffects` rail branch (R0/R1a/R1b/R2–R5); `stripForJson` ships `isRail`; deploy-start sanitiser preserves optional `count`.
- [`HangarOps.php`](source/server/model/systems/HangarOps.php) — `collectRails`, `shipHasRail`, `railFighterCategories`; `primaryHangar` skips rails; `populateInitialHangarUsage` / `getDefaultShuttles` exclude rails; `inferHangarType` skips rails; `serviceDockedFlights` half-cadence gate; `applyLaunchCrits` new `$skipFlightCrit` param; `performDeployStartDock` / `canDeployStartDock` new `$count` param; `onRailStructureDamage`, `onRailStructureLost`, `railCritOwner`, `pickRailToDestroy`, `destroyEntireRail`, `railBoxEscape`, `recordRailCritRoll`.
- [`ShipSystem.php`](source/server/model/systems/ShipSystem.php) — `public function getStructureSystem()` accessor.
- [`DeploymentGamePhase.php`](source/server/Phase/DeploymentGamePhase.php) — `submitCriticals` call after the notes loop.
- [`strikeCarrier.php`](source/server/model/ships/raiders/strikeCarrier.php) — 6 `FighterRail` systems added; front Structure kept at 78.
- [`autoload.php`](source/autoload.php) — `'fighterrail' => '/server/model/systems/baseSystems.php'`.
- [`baseSystems.js`](source/public/client/model/system/baseSystems.js) — `FighterRail extends Hangar`; `refreshHangarTooltip` deploy-slice fix.
- [`DeploymentDock.js`](source/public/client/renderer/phaseStrategy/DeploymentDock.js) — `isDockHangar` adds rail; `collectUsableHangars` / `hangarFreeBoxes` / `distributeFlightAcrossHangars` with optional `reclaimFlightId`; `queueDeployStartDock` / `autoQueueDockOnCarrier` use auto-distribute.
- [`confirm.js`](source/public/client/UI/confirm.js) — rail gate triage across all 6 dialog surfaces; `bayCount` re-edit tracking; `planForRow` / `distributeFlightAcrossBays` helpers; `computePerHangarUsage(forDisplay)` flag; rail labels.
- [`shipTooltipFireMenu.js`](source/public/client/UI/shipTooltipFireMenu.js) — rail gates in launch / dock / recover helpers; `collectReceivingHangarsForRecover` adds `combinedFit`.
- [`SelectFromShips.js`](source/public/client/UI/SelectFromShips.js) — deployment DOCK button falls back to combined capacity.
- [`SystemIcon.js`](source/public/client/UI/reactJs/system/SystemIcon.js) — click gate adds `'fighterRail'`.
- [`systems.js`](source/public/client/systems.js) — `shipHasRail`, `railFighterCategories`, `getShuttlePoolDeclared` exclusion.
- [`faq.php`](source/public/faq.php) — Fighter Rails section added to Hangar Operations.

**Verification (Docker, gameID 4135/4138):**
- Deploy 12-fighter flight onto StrikeCarrier → auto-distributes across rails; pills show correct per-rail split; re-opening dialog shows "→ across rails/bays" row (not 12/6 overflow).
- Launch fighters from rails → no −50 on flight, carrier takes −20; fires and relaunches correctly.
- Front structure damaged (not destroyed) → 1d20 rolled; on 16–20 one rail destroyed, fighters attempt escape; on <16 nothing happens.
- Front structure destroyed entirely → all four remaining rails escape their fighters independently (each rolls its own d20); carrier survives.
- `railCritRoll` note persisted; replay scrub reuses the stored value.

**Commit cadence:**

```
Fighter Rails: FighterRail class, structure-coupled destruction, escape, deploy-dock auto-distribute
```
```

---

### Stage 20 — Same-unit launch coalescing (rejoin a split flight on launch) ⟲ REVERTED → superseded by Stage 21

> **Status (2026-05-31): the Stage 20 implementation was reverted (uncommitted working-tree discard) in favour of the Stage 21 no-split model.** Stage 20 patched the *symptom* — a flight split across bays into overlapping source+fragment ships — by re-collecting them on launch, then needed six follow-up rounds (coalescing, husk-hiding, enh-carry, lost-unit consolidation) to chase the overlap through every accounting path. Stage 21 removes the *cause* (the split itself), so all of the Stage 20 code below is obsolete and was discarded back to the Stage 19 base. The design notes are retained only as a record of what the fragment model required; **do not re-implement them** — Stage 21 deletes the need for coalescing entirely. The detailed testing-round notes from the reverted implementation have been trimmed.

**Goal (original).** A flight docked across multiple bays (e.g. a 9-fighter unit spread over three 3-box Fighter Rails, or across two ordinary hangars) should **relaunch as a single flight**, not one flight per bay. Partial launches still split: launching 6 of the 9 spawns one 6-flight and leaves a 3-fighter remnant docked (existing Stage 5/10 behaviour, applied to the coalesced unit).

This is the launch-side mirror of the Stage 19 *multi-bay docking* work: docking already auto-distributes one flight **across** bays; Stage 20 makes launch auto-**re-collect** those bays into one flight.

**Root cause of the split.** [`Hangar::criticalPhaseEffects`](source/server/model/systems/baseSystems.php#L3293) runs **once per hangar system**. Each call invokes [`HangarOps::processLaunchOrders($this, …)`](source/server/model/systems/HangarOps.php#L1440) → [`performLaunch`](source/server/model/systems/HangarOps.php#L1162), which spawns **one fresh `FighterFlight` per hangar's order**. Three rails each holding 3 fighters of the same unit → three independent `performLaunch` calls → three 3-flights. Nothing coalesces them.

**The unit identity already exists.** Every docked stash record carries `dockedFlightId` (the id of the flight it came from — set in `performLand`/`performDeployStartDock`). A flight docked across N bays produces N stash entries that **share the same `dockedFlightId`**. Anonymous orphans (partial-relaunch leftovers) and auto-filled shuttles have **no** `dockedFlightId`. So "same unit" = "same `dockedFlightId`", and it is already in the data — Stage 20 just groups on it at launch time.

**Design — a carrier-level coalescing pre-pass.**

Rather than change the per-system dispatch in `criticals.php`, the **first** hangar on the carrier to reach the shared launch path runs a once-per-carrier coalescing pass (idempotency guard mirroring `usagePopulated`); the normal per-hangar loop then handles only the *uncoalesced remainder* (anonymous/shuttle orders).

New `HangarOps::processCoalescedLaunchOrders($carrier, $gamedata)`:

1. **Gather** every hangar's `pendingLaunchOrder` on the carrier. Build a map keyed by `dockedFlightId` → `{phpclass, totalRequested, perBay:[{hangar, available(=entry flightSize), direction}]}`, summing requested sizes across bays for the same unit. An order whose matching stash entry has **no** `dockedFlightId`, or a `cannotLaunch`/catapult entry, is left untouched (the per-hangar pass consumes it as today).
2. For each `dockedFlightId` group:
   - **Validate** the *combined* launch: `take = min(sum(requested), sum(available))`; per-bay output budget across the participating hangars (`launchedThisTurn+landedThisTurn+slice <= output`, charged on each bay as fighters are drained from it). Re-check `isDestroyed` per bay (a bay killed earlier in `criticalPhaseEffects` already cleared its `hangarUsage`). Clamp `take` to what budget allows; fail-note the shortfall via `hangarLaunchEvent`.
   - **Drain smallest-bay-first** (user decision): sort the unit's bays by current `flightSize` ascending and pull fighters until `take` is met. Tends to fully clear small rails first, freeing whole rails for reload sooner.
   - **Spawn ONE flight** for the whole group. **Identity: revive when possible** (user decision):
     - Pick the *largest* surviving fragment ship row among the unit's bays whose `dockedFlightId` resolves to a live `FighterFlight` AND whose `flightSize` slice is fully drained by this launch (so its ship identity is free to leave). Relaunch THAT row via the existing `resurrectDockedFlight` mechanics (clear `removed`/`removedTurn`, set `spawned`, write a deploy `MovementOrder`), preserving its name/id/damage.
     - Fold the *other* bays' drained fighters into the revived flight: bump its `flightSize` to `take`, `populate()` the extra Fighter slots, and `copyFighterStateToTargetBulk` the donor fragments' per-fighter damage/crits/ammo onto the new slots.
     - **Fallback to fresh spawn** when no fragment row qualifies for revival (all partial, or rows missing): use `performLaunch`'s new-spawn block (factored into `spawnLaunchedFlight(...)`), named via `splitFlightNameFor`.
   - **Shrink/destroy donor fragments**: each bay either fully drained → `destroyAllFighters` on its fragment + drop the stash entry; or partially drained → `spawnFragmentFlight` for the remnant, relink the bay's `dockedFlightId`/`name`/`fragment`/`dockedTurn` (exactly the partial branch in [`consumeStashesForLaunch`](source/server/model/systems/HangarOps.php#L2613), generalised across multiple bays). The *revived* row's own bay is consumed (its identity left with the launch).
   - **Init penalty**: the coalesced flight is ONE flight → exactly one `LaunchedThisTurn` (−50) unless **every** participating bay is a rail (rails skip the flight-side −50). Mixed rail+hangar unit → apply −50. Carrier −20 `HangarOperations` is idempotent per turn regardless. Reuse `applyLaunchCrits($flight,$carrier,$gamedata,$allRail)`.
   - **One replay note** (`hangarLaunchEvent`, `coalesced`) for the whole launch.
   - Remove the consumed entries from each bay's `pendingLaunchOrder` so the per-hangar pass that follows does not double-launch them.
3. Mark `carrier->coalescedLaunchDone = true` for the turn (reset alongside `launchedThisTurn`).

`Hangar::criticalPhaseEffects` shared path (today three `processLaunchOrders` call sites at [3304/3345/3376](source/server/model/systems/baseSystems.php#L3304)) gains a coalescing call guarded once-per-carrier *before* the per-hangar `processLaunchOrders`. The rail branch (R0–R3) is unchanged; rails fall through to this same shared path, so a rail-spread unit coalesces here too.

**Client UI — one grouped row per unit** (user decision). [`shipTooltipFireMenu.openHangarLaunch`](source/public/client/UI/shipTooltipFireMenu.js#L182) → [`confirm.hangarLaunch`](source/public/client/UI/confirm.js#L1857) currently builds one section per hangar with one stepper per stash record. Stage 20 changes the dialog model:
- Across all of the ship's hangars, group stored craft by `dockedFlightId`. Each group renders as **one row** labelled with the unit name and the *combined* docked count (e.g. "Sentri #3 (9 docked across 3 rails)") with a single 0–N stepper (N = combined `flightSize`).
- Records with no `dockedFlightId` keep one row each (today's behaviour) under their owning hangar.
- On OK, a grouped row's quantity is distributed back to the per-hangar `pendingLaunchOrders` smallest-bay-first (the same drain order the server uses), so the existing note schema (`{phpclass,size,direction}` per hangar) is **unchanged** and the server's coalescing pass re-joins them. The direction picker (Stage 8.5) attaches per participating hangar as today.
- Keeps the wire format and `doIndividualNotesTransfer`/`hangarLaunchOrder` note untouched — only the dialog's grouping and the OK-time distribution change.

**Scope** (user decision): applies to **all hangars**, not just rails — any flight split across multiple bays (rails OR ordinary hangars) re-merges on launch by `dockedFlightId`. The grouping logic is bay-type-agnostic. (Catapults are single-fighter and never split, so they are unaffected; their `isCatapult` entries are excluded from grouping.)

**Replay / idempotency.** The coalescing pass mutates `hangarUsage` (drains entries, relinks remnant `dockedFlightId`) and spawns/revives the flight via the same `Manager::insertSingleShip`/`insertSingleMovement`/note paths as `performLaunch`, which are already replay-deterministic (autoid sequence reproducible, consumed `pendingLaunchOrder` cleared). The once-per-carrier guard prevents a re-entry from re-coalescing. No new RNG → no roll note needed.

**Edge cases:**
- A unit partly launched in a *previous* turn already lives as anonymous orphans + a surviving fragment; only entries that still share a `dockedFlightId` coalesce. Orphans launch per-record as today.
- Output budget exhausted mid-drain across bays → launch as many as the combined budget allows (clamp `take`), leave the rest docked, fail-note the shortfall.
- A bay whose structure/rail was destroyed this turn already cleared its `hangarUsage` before the shared path → excluded automatically.
- Mixed-phpclass "same unit" cannot happen (a flight is one phpclass); grouping by `dockedFlightId` implies a single phpclass per group — assert and skip on mismatch defensively.
- Revival edge: if the would-be revived row is itself only *partially* drained (player launched fewer than its bay holds), it does NOT qualify for revival (its identity must stay with the docked remnant); fall through to fresh-spawn for the coalesced flight and shrink that bay as a normal partial donor.

**Files to change:**
- [`HangarOps.php`](source/server/model/systems/HangarOps.php) — new `processCoalescedLaunchOrders`; factor `performLaunch`'s new-spawn block into `spawnLaunchedFlight`; add `copyFighterStateToTargetBulk` (or loop `copyFighterStateToTarget`) across multiple donor bays; smallest-bay-first drain; revive-largest-fragment logic reusing `resurrectDockedFlight`.
- [`baseSystems.php`](source/server/model/systems/baseSystems.php) — `Hangar::criticalPhaseEffects` shared path calls the coalescing pass first (once-per-carrier guard); add `coalescedLaunchDone` carrier-turn guard reset where `launchedThisTurn` resets.
- [`FirePhaseStrategy.js`](source/public/client/renderer/phaseStrategy/FirePhaseStrategy.js) / [`shipTooltipFireMenu.js`](source/public/client/UI/shipTooltipFireMenu.js) / [`confirm.js`](source/public/client/UI/confirm.js) — grouped-by-`dockedFlightId` launch row; distribute grouped qty to per-hangar `pendingLaunchOrders` smallest-bay-first on OK.
- [`faq.php`](source/public/faq.php) — note that a split flight rejoins on launch.

**What to verify (Docker, StrikeCarrier gameID 3730):** *(verified 2026-05-31 unless noted)*
- [x] Dock a flight onto the StrikeCarrier so it auto-distributes across multiple rails. Launch dialog shows ONE "… (N docked across M bays)" row, not one per bay, with `max = N` and a live "Launching from — Rail 1: x, Rail 2: y" caption.
- [x] Launch the whole unit → a single N-flight spawns at the carrier's hex, **plain unit name** (no " - Split"), every fighter fully charged; the rail stash entries are gone.
- [ ] Launch a partial (e.g. 9 of 12) → one 9-flight named "… - Split" spawns; the remnant stays docked (smallest bays drained first). Net fighters conserved; per-fighter damage/ammo preserved.
- [ ] Two distinct flights docked on the same carrier → two grouped rows; launching one does not disturb the other.
- [ ] A unit split across one rail + one ordinary hangar → still coalesces into one flight on launch (scope = all hangars).
- [ ] Carrier −20 applied once; coalesced flight gets one −50 (or none if every source bay is a rail).
- [ ] Replay scrub reproduces the single coalesced launch (no double-spawn, no ghost rows).

**Commit cadence:**

```
Hangar Ops: coalesce a multi-bay docked flight back into one flight on launch
```

#### Stage 20 implementation notes (2026-05-31)

**Grouping key is `sourceFlightId`, not `dockedFlightId`.** The plan assumed the bays of one docked flight share a `dockedFlightId`. They don't: a multi-bay dock calls `performLand`/`performDeployStartDock` once per receiving hangar, and each PARTIAL bay spawns its OWN fragment, stamping THAT fragment's id as `dockedFlightId` (only the final, full bay carries the source flight's id). So a 9-flight across three 3-box rails leaves three entries with three different `dockedFlightId`s. Fix: stamp a new shared **`sourceFlightId`** (= the original flight's id) on every dock entry in `performLand` and `performDeployStartDock`. Partial-relaunch remnant entries inherit it automatically (`$newEntry = $entry` copies it), so a remnant re-coalesces with its siblings on a later launch. Each bay still keeps its own `dockedFlightId` for the revive/extract mechanics. **Backward compatible:** flights docked before this change have `dockedFlightId` but no `sourceFlightId`, so coalescing skips them and they relaunch one-bay-at-a-time via the unchanged per-hangar `resurrectDockedFlight` path until re-docked.

**Ordering: a carrier-level orchestrator, not a pre-pass that runs first.** Coalescing must see every bay's POST-eviction `hangarUsage` AND consume `sourceFlightId`-linked orders before any per-hangar launch fires them individually. But `criticalPhaseEffects` runs per-system, so the first hangar to reach its launch step hasn't had the other hangars' damage-eviction/dock/service run yet. Resolution: `HangarOps::processAllLaunches($carrier, $gamedata)` replaces the per-hangar `processLaunchOrders` call in both the ordinary and rail dispatch paths. Each hangar marks itself `hangarPhaseProcessed` (transient, per-request) after its damage/dock/service, then calls `processAllLaunches`, which **defers** until every non-catapult hangar is settled. Driven by whichever hangar settles last, it then runs the coalescing pre-pass (consumes `sourceFlightId` orders, rewrites each hangar's `pendingLaunchOrder` to its anonymous residual) and finally runs per-hangar `processLaunchOrders` for the residuals. `coalescedLaunchDone` (transient, on BaseShip) makes it once-per-carrier. The rail R1a early-return (`onRailStructureLost`) also marks-and-calls so the readiness gate can't stall on a destroyed rail. Catapults are excluded from both the gate and coalescing and keep their own direct `processLaunchOrders`.

**Identity + smallest-bay-first drain.** `launchCoalescedGroup` collapses the group's bays per `(hangar, dockedFlightId)`, sorts by live entry size ascending, and drains each under its own remaining output budget (clamps `take`, fail-notes the shortfall via `hangarLaunchEvent`). Identity (corrected from the original "revive largest fragment" design — see *Server follow-up* below): a **single-bay** fully-drained group reuses its existing ship row (the clean `resurrectDockedFlight` semantics — name/id/damage preserved); a **multi-bay** group always **fresh-spawns** a clean N-fighter flight via the factored-out `spawnLaunchedFlight` (the new-spawn block lifted out of `performLaunch`) and copies every bay's fighter state onto fresh, fully-charged slots with `copyFighterStateToTarget` (no separate `copyFighterStateToTargetBulk` helper was needed). Donor bays fully drained → `destroyAllFighters` + drop entry; partially drained → `spawnFragmentFlight` for the remnant + relink (the existing partial branch, generalised across bays). One `LaunchedThisTurn` (−50) unless every bay is a rail; carrier −20 idempotent. One `coalesced` replay note. The mutation loop re-resolves each entry index by `dockedFlightId` just before splicing (a prior same-hangar splice shifts indices).

**Client.** `confirm.hangarLaunch` now renders one grouped row per `sourceFlightId` across all non-catapult hangars (label = unit name + combined docked count + bay count), with a single 0–N stepper capped by combined bay budget. On OK the quantity distributes smallest-bay-first into the per-hangar `pendingLaunchOrders`, so the wire format (`{phpclass,size,direction}` per hangar) and `doIndividualNotesTransfer`/`hangarLaunchOrder` note are unchanged — the server's pass re-joins them. Anonymous orphans / auto-shuttles / catapult records keep their per-hangar rows.

**Client follow-up (testing round 1).** Two dialog fixes after a 12-across-two-6-box-rails test:
- The grouped row's `max` was clamped by `launchSizeMaxFor` (the per-flight *partial-launch* cap, 6), so 12 docked craft only offered 6. A coalesced unit docked AS one flight and relaunches up to its full docked size (`FighterFlight::$maxFlightSize` defaults to 12; the docked count is already self-limiting since it couldn't have docked larger than a legal flight). Fixed: grouped-row `max = min(combined docked count, combined bay budget)` — no per-flight cap.
- Wired the live "remaining" feedback: a grouped stepper now renders a "Launching from — Fighter Rail 1: 6, Fighter Rail 2: 6" caption (smallest-bay-first split, the same order the server drains and OK ships) and updates each participating bay's *Hangar Capacity* "remaining" readout as it changes (turning red on oversubscription). Shared `distributeCoalesced(rd)` helper drives the caption, the per-bay budget labels, and the OK distribution so all three agree; a `hangarLabelMap` built up front names the bays consistently across the coalesced captions and the per-hangar headers.

**Server follow-up (testing round 1).** Two bugs surfaced launching a 12-flight docked across two 6-box rails:
- *Folded-in donor fighters spawned with uncharged weapons (0/loadingtime).* The revive-grow path called `populate()` to add the extra Fighter slots but never initialised their per-system data — only the fresh-spawn path charged weapons. Fixed by factoring the charge loop out of `spawnLaunchedFlight` into `initLaunchedCraftSystemData($flight, $crafts, $gamedata)` and calling it for the newly-appended slots after a revive-grow (snapshotting pre-existing fighter ids so only the new ones are initialised; `FighterFlight::$autoid` advances past the existing fighters on load, so new ids never collide).
- *Full coalesced relaunch wrongly got a " - Split" suffix, and reviving a multi-bay unit lost fighters.* Root cause: **revival is unsafe for a multi-bay unit.** When a flight docks across bays, the original source row keeps the OTHER bays' fighters marked disengaged (and a per-bay fragment holds them separately) — reviving the source yields a flight short its folded-in fighters carrying ghost-disengaged ones; reviving a fragment inherits its " - Split" name (and ship names are write-once, never updated, so an in-memory rename wouldn't persist). Fix: **revive ONLY single-bay groups** (`count(drained) === 1 && fullyDrained` → clean reuse, the existing `resurrectDockedFlight` semantics); a multi-bay group always **fresh-spawns** a clean N-fighter flight and copies every bay's fighter state (damage/crits/ammo) onto fresh, fully-charged slots via `copyFighterStateToTarget`. Naming: strip any inherited " - Split" from the source name and re-append it only when part of the unit stays docked (`totalTake < groupTotalAvailable`), so a full relaunch keeps the plain unit name and a partial keeps the detachment label. AMMO_F* enhancement copy now runs once per target (not once per donor) since a coalesced unit is homogeneous; per-fighter Stage-15 balancing still corrects each slot to its donor's actual count.

**Reverted testing rounds (summary only).** The Stage 20 implementation needed six follow-up rounds, each chasing the source+fragment overlap through another accounting path: (2) per-unit launch targeting via `sourceFlightId` + unit-aware stored-craft tooltip; (3) fleet-list consolidation of multi-bay docked units (`buildDockedUnitGroups`); (4) hiding consumed-launch husks via `DockedFighter` + `isConsumedLaunchHusk`; (5) carrier-destruction escape — non-escapee husk hiding + proportional `pointCostEnh` carry on runtime-spawned flights; (6) server-side lost-unit consolidation note (`recordLostDockedUnits`/`hangarLostUnits`) for accurate "Destroyed" rows. **All discarded.** That this many rounds were needed is precisely why Stage 21 removes the split. (Two findings worth carrying forward to Stage 21: runtime-spawned flights must carry `pointCostEnh` proportionally so value is right; and the Stage-18 rail-loss disengage in Pass 2 runs *before* the escape candidate count in Pass 3, under-counting escape eligibility for rail-docked units — fix in 21.4.)

---

### Stage 21 — No-split docking: a docked flight stays ONE ship (model rewrite) — ✓ COMPLETE (21.1–21.6; live-DB run pending next deploy)

**Motivation.** Stages 5/10/19/20 each layered reconciliation onto a model where a multi-bay dock SPLITS a flight into overlapping ship rows: a full-size source `FighterFlight` (`removed=true`, some fighters `DockedFighter`-crit'd) PLUS separate per-bay fragment ships holding copies of the split-off fighters. A 6-fighter unit across two bays becomes **9 fighter subsystems across 2 ships** (6 + 3, with 3 overlapping ghosts). Every feature — fleet-list value, launch coalescing, carrier-destruction escape, husk hiding — must subtract that overlap, and each does it slightly differently, which is the source of the recurring "7 fighters for a 6-flight" / "44 not 76 CP" bugs. **Decision (user, after ~6 patch rounds): rewrite the model so a flight NEVER splits on dock.**

**Core model (per-bay-primary-with-occupancy — user choice 2026-05-31).** A docked flight is exactly ONE ship (`removed=true`), no matter how many bays its boxes span. The single stash entry lives on its **primary bay** (the hangar that holds the most of it — i.e. the bay the dock dialog/auto-distribute fills first), with an `occupancy` list naming every bay (incl. the primary) that holds boxes for it. This is deliberately the *less-disruptive* variant: the entry still lives on a real hangar system, so the many per-hangar readers (`setSystemDataWindow`, client `refreshHangarTooltip`, capacity display) keep working for the common single-bay case and only the multi-bay accounting consults `occupancy`.

```
// ONE stash entry per docked flight, on its PRIMARY bay's hangarUsage:
{
  phpclass, name, flightId,            // the single docked ship's id (== dockedFlightId)
  flightSize,                          // whole flight (e.g. 6)
  hangarType, dockedTurn,
  customFtrName?, boxesPerCraft?,      // (existing fields preserved)
  pointCostEnh,                        // carried so value is right (no spawn needed)
  occupancy: [ {systemId, boxes}, … ]  // every bay holding boxes for it, incl. the
                                       // primary; Σ boxes == flightSize × boxesPerCraft.
                                       // A single-bay dock has one entry: [{primary, n}].
}
```

`dockedFlightId` is kept (== `flightId`) so legacy readers and the existing `resurrectDockedFlight`/`onIndividualNotesLoaded` `$removed`-restore path keep functioning unchanged. The sibling bays named in `occupancy` do NOT get their own stash entry — their boxes are accounted as occupied via the primary's occupancy list (free-box math on a sibling bay subtracts any occupancy other entries place on it).

- **No `spawnFragmentFlight` on dock.** `performLand` / `performDeployStartDock` mark the WHOLE flight `removed=true` and write one occupancy-bearing stash entry on its primary bay. A flight too big for one bay records extra `occupancy` on sibling bays but stays one ship — no fragment ships, no `DockedFighter` split-crits, no overlap.
- **Capacity per bay unchanged; multi-bay dock spends sibling boxes via occupancy.** A bay's free boxes = its own capacity − (its own entries' boxes) − (boxes other bays' entries place on it via their `occupancy`). When a bay/rail is destroyed, its boxes drop out; if a docked unit's occupancy on the dead bay can't be re-homed within the carrier's remaining free boxes, **evict** the overflow — shrink that docked flight by the overflow count (disengage that many of its fighters in place; the flight stays one ship) and, for rail-box loss, the evicted fighters attempt escape per the rail rules.
- **Rails-as-one-hangar falls out for free.** Rails are just systems contributing boxes; a unit's `occupancy` can list several rail systemIds. No special rail-group abstraction needed beyond the existing per-rail 1d20 structure crit (which removes a rail's boxes → triggers the same overflow eviction).

**What this DELETES vs the Stage-19 base (net simplification).**
- `spawnFragmentFlight` and all fragment book-keeping (`fragment` flag, per-bay fragment ships). Multi-bay docks no longer create extra ships.
- The partial-launch fresh-spawn + `consumeStashesForLaunch` fragment machinery: a unit is one entry → launch is `resurrectDockedFlight` (un-remove the ship) for full, and an in-place shrink for partial. No fresh spawn, no `- Split` naming.
- (Stage 20 coalescing / fleet-list husk helpers were already reverted before Stage 21 — nothing to delete there.)

**Launch under the new model.** `resurrectDockedFlight` brings the one ship back: clear `removed`, deploy MovementOrder, drop/shrink the stash entry by the launched count. Partial launch shrinks the docked entry's `flightSize` + occupancy and re-engages only the launched fighters (the rest stay docked on the SAME ship). No fresh spawn, no name suffix, no fragments. Ammo/damage are already on the one ship — nothing to copy.

**Carrier-destruction escape under the new model.** One ship per unit: roll d20 over total docked craft; the escapees re-engage on a NEW small flight (single `spawnEscapeFlight`, the only place a spawn still happens) OR, cleaner, the parent flight is resized to the escapee count and un-removed, and the lost remainder simply dies with the carrier (parent ship gone). Either way: ONE escapee flight + the rest lost; no fragment husks, no lost-unit consolidation note.

**Live-compatibility (CRITICAL — see memory `project_hangar_live_compat`).** The live `DouglasChanges` server already runs the fragment model (stash entries with `dockedFlightId` + `fragment`, NO `sourceFlightId`, no rails). Refined, **lower-risk** approach (no destructive load-time merging of live data):
- **`dockedFlightId` is preserved** and remains the dock identity. A legacy entry with NO `occupancy` field is read as a single-bay dock on its own hangar (its boxes accounted on that hangar, exactly as today). The existing `resurrectDockedFlight` and `onIndividualNotesLoaded` `$removed`-restore paths are unchanged, so **every legacy single-bay full/partial dock keeps working with zero migration.**
- **Dual read path, coexisting indefinitely.** New docks WRITE `occupancy`; readers treat "no `occupancy`" as the legacy single-bay shape. No code forcibly converts legacy fragment ships.
- **Legacy multi-bay fragment docks** (a flight too big for one bay, split into fragment ships) only ever existed via the dock dialog's split; they are rare-to-nonexistent on live (no rails there, and ordinary multi-bay split is uncommon). They keep rendering exactly as the Stage-19 fragment model does — we do NOT retro-merge them. New multi-bay docks use occupancy. So both models coexist per-entry; nothing in flight breaks.
- Verify against a copy of a live game DB before deploy (regression, not migration).

> **Base for Stage 21:** the working tree was reset to the committed **Stage 19** state (Fighter Rails done, pre-Stage-20). So there is NO Stage 20 coalescing code to remove — Stage 21 builds the no-split model directly on Stage 19. The Stage 19 dock path still SPLITS (it's the fragment model); 21.1 replaces that.

**Staged rollout (each independently testable on gameID 3730 / 4143):**
1. **21.1 — Model + dock:** new occupancy stash; `performLand`/`performDeployStartDock` no-split (one ship, `occupancy` across systems, carry `pointCostEnh`); capacity/free-box math reads occupancy; legacy-load normalisation folds the Stage-19 fragment stash into the new shape. Delete `spawnFragmentFlight`-on-dock.
2. **21.2 — Launch.** A docked flight is ONE entry (on its primary bay, occupancy spanning bays). Launch resolution becomes **carrier-level** (like the dock coalescer), because a launch order may be queued on a bay that isn't where the entry lives:
   - **Full launch** (launch all N): `resurrectDockedFlight` already does this right — match the `dockedFlightId` entry, splice it (occupancy and all), un-remove the original ship. Ship returns with all fighters/damage/ammo intact. Reused as-is.
   - **Partial launch** (launch K of N): spawn a fresh K-flight named `"<source> - Split"` carrying the **most-ammo/least-damaged-first** K fighters' state (reuse `selectFightersForExtraction` + `copyFighterStateToTarget`); **shrink the original docked ship in place** to N−K (disengage those K fighters on it; reduce the entry's `flightSize` and trim occupancy boxes). NO fragment ship — the remnant IS the original docked ship.
   - **Budget across bays:** charge `launchedThisTurn` across the entry's occupancy bays (a multi-bay flight's launch consumes budget on each bay it occupied).
   - **Launch-order targeting:** the order is `{phpclass,size}` today, which can't disambiguate two same-class docked flights or locate a multi-bay entry. Add the docked flight's id (`dockedFlightId`) to the launch order so resolution targets the exact entry. (Client launch dialog already lists docked flights — send the id.)
   - Deletes `consumeStashesForLaunch`'s fragment-spawn path and `spawnFragmentFlight`-on-launch.
3. **21.3 — Rails contribute boxes:** ✓ DONE — rail systems feed carrier capacity; rail box/structure destruction → occupancy-aware overflow eviction + escape. StrikeCarrier (rails) and a 2-ordinary-hangar carrier (Ossari Kasta case) both behave. Catapults stay single-fighter, unchanged.
4. **21.4 — Escape rewrite:** ✓ DONE + VERIFIED (gameID 4149) — carrier-destruction partial escape spawns ONE "<name> - Split" K-flight per source flight (no fragment ship); escapees spread round-robin across flights; source flights die with the carrier (CV → 0). Fixed the destroyed-carrier hangarUsage-persist bug (escapees were re-marked removed on reload). Pass-2/Pass-3 ordering already correct via the `$ship->isDestroyed()` guards.
5. **21.5 — Fleet-list/value cleanup:** ✓ DONE — husk/consolidation helpers were already reverted pre-Stage-21 (nothing to remove). Fixed a latent `pointCostEnh` double-count: a partial launch / rail-shed left the docked remnant carrying the FULL enhancement cost while the launched/escape "- Split" flight also took a proportional share. Remnant now drops its share (persisted via new `Manager::insertSingleEnhValue` → `tac_ship.enhvalue`). `dockedCraftStashValue` KEPT as a legacy/orphan guard (contributes 0 under no-split) + documented.
6. **21.6 — Live-compat verification:** ✓ DONE (code audit — user opted out of a live-DB run; no local dump, remote unreachable). Proved every no-split read/write path degrades correctly to the legacy single-bay shape when `occupancy` is absent; legacy `fragment` + `dockedFlightId` entries restore + render exactly as the Stage-19/live model (no retro-merge, no double-count, no crash). The live-DB run is deferred to the user's next deploy.

**Fighter Rails — authoritative rules (B5W, reconcile against the no-split model in 21.3/21.4):**
- External launch rails are a row of detached boxes connected to a structure block; **one fighter per box**. Each can launch/land **independently**. **No initiative penalty on the fighter the turn after launch** (the ship still takes the normal launch/land penalty that turn). *(Already in Stage 19: rails skip the flight-side −50, carrier keeps −20.)*
- Rails are **part of the structure block for all purposes** — structure hits are common, so rails are destroyed far more often than a normal bay. If a **rail box is destroyed and a fighter is present**, that fighter may **attempt to escape** (existing escape rules); a rail-box escapee **IS subject** to the next-turn initiative penalty (a forced evac is not a clean launch).
- **Structure-block 1d20 crit:** if a structure block *with rails* takes damage in a turn, roll 1d20 with **no modifiers**; on a **natural 16–20, one entire rail is destroyed** (a rail = any row of external boxes on one straight segment). The **owning player chooses which rail** is destroyed. *(Stage 19 picks smallest automatically; revisit player-choice in 21.3.)*
- Rail fighters perform hangar ops through **narrow airlocks → all reload/hangar-ops take twice as long.** *(Already in Stage 19: rail half-cadence reload.)*
- Under no-split: rail boxes contribute to the carrier's capacity; destroying rail boxes (per-box or whole-rail crit) removes that capacity → the docked unit sheds the overflow (those fighters attempt escape per the rule above), but the unit stays **one ship**.

**What to verify (end state):**
- 6-fighter unit docked across 2 bays → ONE fleet-list row, ONE ship, correct value incl. enhancements. Launch all → one flight, plain name. Partial launch → one launched flight + one shrunken docked entry (still one ship). No "- Split" anywhere.
- Destroy a bay holding part of a unit → unit shrinks by the bay's boxes (overflow evicted); remaining stays one docked ship.
- Carrier destroyed, partial escape → one escapee flight (correct value) + parent gone; no husks, no "Destroyed 0/132" overcount, no 7-for-6.
- A legacy (live-shape) docked game loads and advances a turn without corruption; its docked units upgrade to the new shape.

**Commit cadence:** one commit per sub-stage (21.1 … 21.6), e.g. `Hangar Ops Stage 21.1: no-split docking model + legacy normalisation`.

#### 21.1 — ✓ VERIFIED + COMMITTED (`b85c49039 "21.1"`, plan `fef5f40e2`) 2026-05-31

Docker-tested clean across all three dock paths: single-bay deploy (Primus → one entry, no `occupancy`), multi-bay firing-phase dock (Strike Carrier → one entry `occupancy:[{11:3},{12:3},{13:6}]`), and multi-bay deploy dock (Strike Carrier → one entry `occupancy:[{13:6},{14:6}]`, correctly avoiding the full 2-box universal shuttle bay). No fragment ships, one `dockedFlightId` per docked flight, no spurious fails. Committed as its own sub-stage commit (bundles excluded per workflow).

#### 21.2 — ✓ FINISHED (server + client, Docker-tested across testing rounds 1–3) 2026-06-01

- **Server** (`HangarOps.php`): `processWholeFlightLaunches($carrier,$gamedata)` — once-per-carrier (`launchCoalesceDone`) coalescer; gathers all non-catapult bays' `pendingLaunchOrder`, resolves each by `dockedFlightId` via `findDockedEntry` (searches all bays). `canLaunchWholeFlight` (pivot/roll + stored-count + per-occupancy-bay budget). `launchWholeFlight`: **full** = resurrect the docked ship (un-remove, deploy move, drop the whole entry); **partial** = `spawnLaunchedKFlight` (fresh "<name> - Split" K-flight, proportional `pointCostEnh`, weapons charged) + copy the most-ammo/least-damaged K fighters' state (`selectFightersForExtraction`+`copyFighterStateToTarget`) + disengage them on the docked ship + `shrinkDockedEntry` (flightSize −K, trim occupancy smallest-bay-first). `chargeLaunchBudget` across occupancy bays. `occupancyBaysFor` resolves occupancy → hangars. Catapults still use the legacy per-bay `processLaunchOrders`.
- **Dispatch** (`baseSystems.php`): rail + ordinary launch steps → `processWholeFlightLaunches`; `doIndividualNotesTransfer` preserves order `dockedFlightId`.
- **Guard** (`ShipClasses.php`): `BaseShip::$launchCoalesceDone`.
- **Client** (`confirm.js`): launch dialog rewritten to **one row per docked flight** (keyed by `dockedFlightId`), 0–N stepper, sends `{phpclass,size,dockedFlightId,direction}` onto the entry's bay; catapults render single-fighter rows (no dockedFlightId, legacy path). Old per-phpclass dialog deleted. The whole `pendingLaunchOrders` object (incl. `dockedFlightId`) round-trips via `baseSystems.js`.
- PHP lint + JS `node --check` clean.

Not yet done: rails-as-boxes capacity + rail-destruction (21.3); escape rewrite (21.4); fleet-list cleanup (21.5). The once-per-carrier launch coalescer runs at the first bay's launch step without a full readiness gate (acceptable for 21.2; a stale per-bay budget charge is harmless since the coalescer consumes all orders).

**21.2 client follow-up (testing round 1).** The first launch-dialog rewrite lost four behaviours; all fixed in `confirm.js` (+ a matching server budget fix):
- *No pre-fill on re-open.* Rows now pre-fill from `hangar.pendingLaunchOrders` (matched by `dockedFlightId` for docked rows, phpclass for anon) — the queued order round-trips via the `hangarLaunchOrder` note → `pendingLaunchOrder` → client hydrate, already wired.
- *Capacity didn't tick down.* Each bay header now has a LIVE `launch budget: X/Y` span; `updateBudgets()` recomputes on every input change by charging each bay the craft drawn from it.
- *Carrier-wide budget ceiling was wrong* (blocked 12 rail + 2 shuttle = 14 vs a bogus "12" sum). Replaced with **per-bay** budget: each bay's `output` is its own launch rate; a multi-bay docked launch charges its occupancy bays **smallest-bay-first** (matching the fighter drain). OK validates per bay, not a carrier sum.
- *Bays full only via FOREIGN occupancy were hidden* (a rail holding a sibling-entry's boxes has empty own `hangarUsage`). Now every non-catapult bay gets a header (capacity counts foreign occupancy), so a 12-flight across two 6-box rails shows both rails at 6/6 + one "12 docked across 2 bays" launch row.
- *Server budget alignment.* `chargeLaunchBudget` + `canLaunchWholeFlight` now use a shared `distributeCraftAcrossBays` (smallest-bay-first) so the server charges each bay only what's drawn from it (the old `min(size,bayCraft)` per bay over-charged partial launches and disagreed with the client).

**21.2 tooltip follow-up (testing round 2) — Issue 4 (multi-bay capacity display).** A 12-flight across two 6-box rails showed (from Initial Orders onward) "6/6 + 12 x DeltaV" on the host rail and **0/6 on every other rail** — the sibling rail holding 6 boxes via foreign occupancy read empty. The DB occupancy was correct (`[{13:6},{14:6}]`) and launch behaviour was correct; purely a display bug. Root cause: `refreshHangarTooltip` runs in the **Hangar constructor**, before sibling hangars are built, so the foreign-occupancy lookup (boxes an entry on a sibling bay places on this one) had no siblings to find. Fixes:
- `baseSystems.js` `refreshHangarTooltip`: `totalStored` made occupancy-aware (`entryBoxesOnThis` counts only this bay's occupancy share; a post-loop scan adds boxes other bays' entries spill here). *(manual edit, CRLF file)*
- `ship.js`: re-run `refreshHangarTooltip` on every hangar/rail/catapult after ALL systems are built, so foreign occupancy resolves with all siblings present. **GOTCHA (round 3):** the first attempt edited a `Ship` constructor that turned out to be inside a `/*//OLD VERSION - CHANGED DEC 2025` block comment (dead code) — `ship.js` has the live constructor at the top (line 3) which builds `systems` via a LAZY getter (`Object.defineProperty`), and a commented-out old one below. The re-pass must go INSIDE the active getter, right after `createSystemsFromJson` populates `parsed` and the `systems` property is defined (so the hangar helpers can read `this.ship.systems`). Confirmed via breakpoints that `refreshHangarTooltip` ran once per system at load and never on hover, so the cached `this.data` was the stale (pre-sibling) computation.
- Split-count "Stored Craft" listing (user decision): `byClass` made occupancy-aware (each entry's craft counted on the bay its occupancy assigns them to + a foreign-occupancy block adds craft other bays' entries keep here). GOTCHA: the per-bay early-return `if (displayEntries.length === 0 && !hasLaunches) { delete data["Stored Craft"]; return; }` fired for a bay full ONLY via foreign occupancy (empty own hangarUsage) and skipped the listing — added a `hasForeignOccupancy` check to the guard so a 12-flight across two rails reads "6 x DeltaV" in BOTH.
- Launch dialog (`confirm.js`): (a) a bay full only via foreign occupancy was filtered out of the dialog (`hangarUsage.length === 0` skip) — now a bay is shown if it has own craft OR is named by any occupancy list, so both rails appear; (b) the docked-flight launch input was wedged between two bay headers — restructured into TWO passes: Pass 1 renders ALL bay capacity headers grouped, a "Launch" separator, then Pass 2 renders the launch rows (docked + anonymous). Anonymous rows now name their source bay.

**21.2 tooltip follow-up (testing round 3) — same-class flight name merge.** Two distinct flights of the **same phpclass** docked in one ordinary bay (gameid 4148: Sentri #2 + Sentri #3, both 6-craft, in a Primus) listed as a single "12 x Sentri #3" line — the count summed right but the label was whichever flight built the bucket first. Root cause: the "Stored Craft" `byClass` keyed buckets by **phpclass alone** while displaying the per-flight friendly name. Fix in `baseSystems.js` `refreshHangarTooltip`: bucket key is now `phpKey + '|' + displayName (+ pending/wreck markers)` so same-class flights list separately; each bucket stashes its `phpKey`. The foreign-occupancy bucket got the same key change. Launch subtraction (a launch order carries only a phpclass, no flight identity) now spreads the launching count across every committed (non-pending, non-wreck) bucket sharing that `phpKey` until exhausted, instead of looking up a single bare-phpclass bucket that no longer exists. `node --check` clean. *(single-source edit; legacy bundles untouched per workflow)*

#### 21.3 — ✓ FINISHED (server-only, Docker-tested) 2026-06-01

Rail-destruction reconciled with the no-split occupancy model. Rails already fed *docking* capacity (the free-box math reads `maxhealth` per bay regardless of `isRail`); they remain excluded only from the default-**shuttle pool** (correct). The real work was the destruction → overflow-eviction + escape path, which predated no-split and operated purely on each rail's OWN `hangarUsage` — so a destroyed rail holding only foreign-occupancy boxes (host entry on a sibling) shed nothing (phantom boxes survived), and a destroyed host rail over-evicted the whole flight incl. craft safe on intact siblings. All changes in `HangarOps.php` (PHP lint clean; no client/JS changes — destruction is server-authoritative):

- **`shedBoxesFromBay($rail,$carrier,$gamedata)`** (new core) — occupancy-aware lost-box shedding in 3 passes. PASS 1 walks ALL the carrier's hangars and identifies every entry holding boxes on the destroyed rail (occupancy entries via `entryBoxesOnHangar`; own no-occupancy entries via `boxesForEntry`), keyed by stable identity (host + dockedFlightId|phpclass|origIdx) so a later compaction-shifted index re-resolves. `craftToShed = min(flightSize, ceil(boxesOnRail / bpc))`. PASS 2 runs the escape attempt (mutates only flights/criticals, not the stash arrays). PASS 3 trims each host entry by identity.
- **`escapeShedCraft`** — escape d20 (`computeEscapeCount`, the carrier-destruction table) over the k shed craft of one entry; the first `maxEscape` (most-ammo/least-damaged via `selectFightersForExtraction`) spawn as a "<name> - Split" flight via `spawnRailEscapeFlight`; every shed craft (escapee or not) is `DisengagedFighter`-marked on the docked ship so its roster shrinks by k. Anonymous entries (no dockedFlightId) shed without per-craft escape (trim only).
- **`spawnRailEscapeFlight`** — reuses `spawnLaunchedKFlight` (proportional `pointCostEnh`) + `copyFighterStateToTarget`/`copyFlightAmmoEnhancements`, spawns at carrier hex with (facing + rail direction), applies `applyEscapeCrits` (the −50 next-turn penalty — rule 3: a forced rail evac IS penalised, unlike a clean rail launch). `railEvac` replay note.
- **`trimHostEntryAfterShed` + `trimEntryForLostBay`** — trim flightSize −k and drop the **destroyed bay's specific** occupancy line (NOT smallest-bay-first like the launch `shrinkDockedEntry`). If the destroyed rail WAS the entry's host and a sibling survives, **re-home** the survivor entry onto the first surviving (boxes>0, getRemainingHealth>0) sibling bay — captured BEFORE the trim collapses the occupancy. No surviving sibling (full-block loss destroys all rails) → drop the residue. Helpers `resolveEntryIndex` (identity re-lookup, compaction-safe) + `hangarById`.
- **`destroyEntireRail`** — keeps the `RailCrit` DamageEntry zeroing the rail; replaced the old `railBoxEscape($rail)` + blanket `$rail->hangarUsage = array()` with `shedBoxesFromBay` (the blanket clear lost foreign-occupancy survivors hosted elsewhere). **`railBoxEscape` deleted** (replaced by the occupancy-aware shed).
- **`onRailStructureLost`** (full external-block loss) — now sheds each rail on the dead block via `shedBoxesFromBay` (per-rail; a flight spanning two block rails is shed across both passes, flightSize→0, entry compacts); re-home is suppressed onto a sibling that is itself a destroyed-block rail (the `getRemainingHealth<=0` guard).
- **`pickRailToDestroy`** (user refinement) — the 16–20 whole-rail crit now prefers an **EMPTY** rail (occupancy-aware `usageCountFor($rail)===0` — no own craft AND no foreign occupancy), smallest empty first; only if every rail on the block is occupied does it fall back to the prior smallest-remaining heuristic. Player would sacrifice a bare rail before one carrying fighters. (Full player-choice UI remains deferred per roadmap.)
- **No-double-evict**: shedding trims occupancy + flightSize so `usageCountFor` already matches capacity before the dispatch's subsequent `onHangarCriticalPhase` runs → its eviction is a no-op safety net for ordinary hangars. Replay idempotent: the trim drops the destroyed rail's occupancy line, so a replay scrub re-enters with `boxesHere=0` → no affected entries → no re-spawn (the escape flight + trimmed `hangarUsage` are already persisted), same idempotency the old whole-rail clear relied on.

Not yet done: escape rewrite (21.4 — DONE below); fleet-list/value cleanup (21.5); live-compat verification (21.6). Player-choice rail-destroy UI and per-structure-point box attrition remain deferred per roadmap.

**21.3 Docker test scenarios (for the user to run):**
1. **Foreign-occupancy rail destroyed** — StrikeCarrier, dock a 12-flight across rail A (6) + rail B (6), host on A. Destroy rail B's structure (1d20 16–20 or force-destroy its boxes). Expect: ~6 craft shed from the host flight (escape per d20, rest disengaged), host entry trimmed to 6 with occupancy only on A, flight still ONE ship, no phantom boxes, both rails' capacity lines correct. Re-scrub replay → identical, no double-spawn / no 7-for-6.
2. **Primary (host) rail destroyed** — same flight, destroy rail A (the host). Expect: the 6 on A shed/escape; the 6 on B SURVIVE — the entry re-homes onto B (now a single-bay entry there), flight still one ship, survivors don't vanish.
3. **pickRailToDestroy empty-first** — StrikeCarrier, one empty rail + one occupied rail on the same block; trigger the 16–20 crit. Expect the EMPTY rail destroyed, the occupied one untouched.
4. **Full-block loss spanning two rails** — flight across two rails on the SAME external block; destroy the block. Expect the whole flight shed (escape per d20), no residue, no re-home onto a dying sibling.
5. **Ossari Kasta (2 ordinary hangars, no rails)** — regression: dock/launch/damage a hangar; ordinary eviction unchanged (rail paths never fire).
6. **Catapult** — regression: unchanged single-fighter behaviour.

#### 21.4 — ✓ VERIFIED (Docker-tested gameID 4149) 2026-06-01

Carrier-destruction escape rewritten to the no-split model — the last fragment-spawn in the escape path is gone. All changes in `HangarOps.php` (escape is server-authoritative; no client/JS changes):

- **`spawnEscapeForBucket` partial-escape path** no longer calls `spawnFragmentFlight`. A partial carrier-destruction escape now spawns **ONE `"<name> - Split"` K-flight** via `spawnLaunchedKFlight` (the same no-split spawner the partial-launch and rail-escape paths use), carrying proportional `pointCostEnh` + the chosen escapees' ammo/damage/crit state (`copyFlightAmmoEnhancements` + `copyFighterStateToTarget`). The source docked flight is NOT the escape flight — it dies with the carrier, so `markNonEscapeesDestroyed` disengages its **entire** roster (CV → 0). Result: one escapee flight + the rest lost, no fragment husk, no `removed` ghost. (Full escape still resurrects the source flight in place — that path was already no-split.)
- **New-spawn path** (anonymous orphan / auto-shuttle / missing-linked-flight) folded onto the same `spawnLaunchedKFlight` helper (`null` source = no `pointCostEnh` carry, matching the old hand-rolled spawn) — deletes ~40 lines of duplicated insert/initSystemData boilerplate.
- **Escape `$kind` note** now reads `'split'` (was `'fragment'`) for partial; `'resurrected'`/`'newspawn'`/`'railEvac'` unchanged. The kind is forensic-only (no replay/client consumer parses it).
- **Pass-2/Pass-3 ordering** (the plan's flagged concern): **already correct** — both `onHangarCriticalPhase` and the rail `criticalPhaseEffects` R0 step bail with `if ($ship->isDestroyed()) return;`, so a dying carrier's same-turn rail/hangar loss does NOT shed craft in Pass 2. The Pass-3 `processCarrierDestructionEscapes` roll therefore sees the FULL docked roster (no under-count). No reorder needed; verified the guards are in place.
- `spawnFragmentFlight` is **retained** — it still backs the partial-DOCK fragment path (`performLand`/relaunch split), which is a separate feature out of 21.4's scope. Only the escape path stopped using it.

**21.4 Docker test scenarios (for the user to run):**
1. **Partial carrier-destruction escape** — dock a 6-flight in a carrier, destroy the carrier, force the d20 into the 11–18 band (½ escape). Expect: ONE `"<name> - Split"` 3-flight in space at the carrier's hex (correct value incl. enhancements, −50 next-turn init), the original docked row gone (CV 0), no `removed` ghost, no "- Split" remnant docked anywhere. Re-scrub replay → identical (roll persists via `hangarEscapeRoll` note).
2. **Full escape (resurrect)** — same, force 19–20 (all escape). Expect the original flight back on the board in place (all damage/ammo intact), plain name, no Split.
3. **Zero escape** — force 1–5. Expect no escapee flight, docked row folds to CV 0, no ghost.
4. **Multi-bay docked flight** — 12-flight across two rails, host on rail A; destroy the carrier, ½ escape. Expect ONE 6-flight escapee (occupancy entry counted once on its primary bay — no double-count / 7-for-6), source row CV 0.
5. **Same-turn rail loss + carrier death** — destroy a rail AND the carrier the same turn. Expect the Pass-3 roll to cover the FULL docked count (rail-loss shed suppressed because the carrier is already destroyed), one escapee flight, no double-escape.
6. **JumpFailure / Ossari Kasta / Catapult** — regressions: JumpFailure still forces 0 escape (all docked die), ordinary 2-hangar carrier + catapult unchanged.

**21.4 testing round 1 (2026-06-01) — escapees vanished + lopsided selection (gameID 4149).** A StrikeCarrier with 24 DeltaV across 6 rails (two 12-flights) was destroyed; roll 13 → 12 of 24 escape, but on the next turn NO escapee flight appeared on the board. Two distinct bugs:

- **Escapees vanished on reload (persistence — the real bug, pre-existing since Stage 18).** `processCarrierDestructionEscapes` clears each hangar's `$hangarUsage` in memory after resurrecting/spawning escapees, but `Hangar::generateIndividualNotes` short-circuited with `if ($ship->isDestroyed() && !hasJumpDamageThisTurn(...)) return;` for a carrier destroyed by ordinary fire — so the **emptied hangarUsage was never persisted**. The latest `hangarUsage` note stayed the pre-destruction one (with its `dockedFlightId` entries), and on the next load `onIndividualNotesLoaded` re-applied `$removed=true` to the resurrected escapees → they disappeared. Fix in `baseSystems.php`: a destroyed carrier now **skips the launch/dock/pool order blocks but still reaches the hangarUsage snapshot tail** (wrapped the order/pool blocks in `if (!$destroyed){…}`; removed the blanket early-return). The snapshot's own change-detection (`$current === $this->lastSavedUsage`) keeps a carrier destroyed on a PRIOR turn from re-emitting. The jump path (`processJumpingCarrierDockOrders`) now persists via the same unconditional tail (strictly more permissive than the old `hasJumpDamageThisTurn` exception). PHP lint clean.
- **Lopsided selection (user call: spread across flights).** The old global priority-sort + `array_slice` drained one flight entirely before the next (stable PHP 8.2 `usort` + identical flights → all 12 escapees from flight #2, 0 from #3 → #2 resurrected whole, #3 killed whole). New `chooseEscapees($virtuals,$maxEscape)`: bucket by source stash entry, priority-sort **within** each bucket (best craft first), order buckets by their top craft, then **round-robin** one craft per bucket until the budget fills. Two identical 12-flights with a 12-escape budget now yield TWO `"- Split"` 6-flights (one per source), not one whole + one dead.
- **Debug `$roll = 13;` hardcodes** in `processCarrierDestructionEscapes` and the rail-escape `escapeShedCraft` overwrite the live `random_int(1,20)` (force every escape roll to 13). Removed during the fix; the user re-adds them ad hoc for testing and removes manually before deploy — **check these are gone before any commit/deploy.** (Commented-out `//$roll = 17;` in `onRailStructureDamage` is permanent scaffolding, left as-is.)

#### 21.5 — ✓ FINISHED + VERIFIED (full + partial escape rows correct in fleetList) 2026-06-01

Fleet-list / value cleanup. The "remove leftover docked-unit consolidation/husk helpers" part was a **no-op**: the Stage-20 coalescing / fleet-list husk helpers were already reverted before Stage 21 (confirmed — no `husk`/`consolidat`/`sourceFlightId`/`lostUnit` references remain in `fleetList.js` or anywhere client/server). Under no-split each docked flight already renders as ONE row from its own ship's `flightSize`/`pointCostEnh` ([fleetList.js](source/public/client/UI/fleetList.js) lines ~187-192), unchanged. The real work was a latent **value double-count**:

- **`pointCostEnh` double-count on partial launch / rail-shed (fixed).** A partial launch spawns a `"- Split"` K-flight carrying a proportional enhancement share (`spawnLaunchedKFlight` sets `pointCostEnh = round(total·K/N)`), but the docked **remnant** kept its FULL `pointCostEnh` — so the fleet list counted the enhancement ~1+K/N times across the two rows. Same shape in the rail-shed escape (`escapeShedCraft`): the surviving docked remnant kept full enh while the escape `"- Split"` flight took a share.
  - `launchWholeFlight` partial path: remnant enh = `total − launched.pointCostEnh` (exact conservation), persisted.
  - `escapeShedCraft`: remnant enh = `round(total·(N−shed)/N)` (proportional; handles the escape-vs-die mix where there isn't always one escape flight to subtract), persisted; skipped when the whole flight is shed (no remnant).
  - **Persistence:** `pointCostEnh` loads from the `tac_ship.enhvalue` column, so a remnant's reduced value needs an UPDATE on the existing row. New `DBManager::submitEnhValue($shipid,$enhValue)` (prepared `UPDATE tac_ship SET enhvalue`) + `Manager::insertSingleEnhValue` wrapper. Replay-safe: the launch/shed executes once on the live fire-phase advance (`setCriticals`), not on replay scrubs, so the UPDATE runs once — no compounding. (DeltaV test flights have `enhvalue=0`, so this game showed 0 impact; the fix matters for any flight that bought AMMO_F*/other enhancements.)
- **`dockedCraftStashValue` KEPT (user call: conservative legacy guard) + documented.** Under no-split every value-bearing docked flight has its own ship row, so the only anonymous (no-`dockedFlightId`) hangarUsage entries are auto-fill shuttles (pointCost 0) → the helper contributes 0 in practice. Left in place as an orphan/legacy-DB safety net (a pre-no-split fragment dock or future orphan entry carrying real value is still credited rather than dropped); the docstring now spells this out and points at Stage 21.6. Its Stage-18 "destroyed carrier loses stash" comment updated to note the 21.4 persist fix.

**21.5 testing follow-up (2026-06-01) — dead escape-source row value (decision: leave as-is).** On a 50% carrier-destruction escape, the dead source flight's fleet-list row shows `0/(fullValue)` (the escaped half is separately shown by the Split flight's `half/half` row). User asked whether the dead row should instead read `0/(halfValue)`. **Decision: leave it `0/(fullValue)`** — that's how EVERY destroyed flight in the game renders (base value = full as-paid roster; current value = 0 because dead; a normal 6-fighter flight wiped in combat shows `0/(full 6-CP)`, never a partial). Reducing the dead source's base value would (a) break that game-wide convention and (b) risk a real bug: cutting a flight's persisted `flightSize` orphans the per-fighter disengage crits (keyed by fighter system id, rebuilt by `populate()` from flightSize) and could resurface the dead flight on reload. No code change. NOTE: the 21.5 `pointCostEnh` fix above only ever reduces a **surviving** remnant's value (partial-launch / rail-shed survivor — a live "Docked" row that should show its true reduced enh), never a dead row; consistent with this decision. The remnant's `pointCost·flightSize/6` term still shows the full roster (flightSize unchanged for safety), same as any damaged-but-alive flight.

#### 21.6 — ✓ DONE (code audit; live-DB run deferred to next deploy) 2026-06-01

Live-compat verification. No local copy of a live `DouglasChanges` DB exists and the remote server is unreachable from here; the only `.sql` files under `C:\FV_env\*` are single-scenario debug snapshots, not a live hangar game. **User opted for a code audit** rather than hand-crafting a synthetic legacy fixture. The audit traces the live (Stage-19/fragment) shape — stash entries with `dockedFlightId` (+ `fragment` for partial docks), **NO `occupancy`**, no rails, possibly an overlapping full-size `removed=true` source flight with `DockedFighter` crits — through every no-split read/write path and confirms each degrades to the legacy single-bay behaviour:

| Path | Legacy (no-`occupancy`) handling | OK |
|---|---|---|
| `usageCountFor` / `entryBoxesOnHangar` / `foreignOccupancyBoxesOn` | no-occ entry → full `boxesForEntry` on its OWN bay, places 0 on siblings → no double-count | ✓ |
| `evictCraftFromHangar` (damage) | per-entry `flightSize` + `dockedFlightId`, `bpc` defaults 1; no occupancy dep | ✓ |
| `$removed`-restore + `fragment` `spawned`-restore (`Hangar::onIndividualNotesLoaded`) | keys on `dockedFlightId` (present in legacy); `fragment` flag still honoured | ✓ |
| Launch (`launchWholeFlight` / `occupancyBaysFor`) | empty occupancy → own-hangar fallback bay | ✓ |
| Carrier-destruction escape (`buildEscapeCandidates` → `chooseEscapees`) | entry-level fields only; one bucket per legacy entry | ✓ |
| Client `refreshHangarTooltip` (`baseSystems.js`) | mirrors server dual-read (`entryBoxesOnThis` no-occ → full size; foreign-occ only for occ entries) | ✓ |
| `DockedFighter` critical (live overlapping-row shape) | crit class (`cricialClasses.php`) + handler (`fighter.php`) retained → renders as on live, no retro-merge | ✓ |
| Catapults | short-circuit BEFORE all occupancy code (`usageCountFor`/`foreignOccupancyBoxesOn` early-return) | ✓ |
| Every `$entry['occupancy']` access (18 sites, server) | all guarded `!empty(...) && is_array(...)` — no unguarded read, legacy entry never crashes | ✓ |

**Conclusion:** the dual-read path the plan promised holds end-to-end — a legacy fragment dock loads, advances a turn (damage/eviction/launch/escape), and renders without double-count, orphan ghosts, or 7-for-6. Normal Hangars + Catapults never touch the occupancy code. No code change was required by the audit. **The empirical live-DB load+advance is deferred to the user's next deploy** (regression check, not migration — nothing forcibly converts legacy entries).

#### 21.7 — Partial dock now spawns a "- Split" fragment (fleet-list fix) 2026-06-01

**Symptom (user-reported).** A partial dock (e.g. 3 of 6 fighters land, 3 stay in space) left only the remnant flight in the fleet list — at its correct reduced value — but the docked half **vanished entirely**. A full dock displayed correctly (the flight ship itself becomes the `removed=true` "Docked" row). The asymmetry was the giveaway: the user explicitly noted it only happened on partial docks.

**Root cause.** `performWholeFlightDock`'s partial branch (the Stage 21 main dock path) disengaged the docked fighters off the source flight and wrote an **anonymous** `hangarUsage` entry (no `dockedFlightId`, no ship row). The docked craft therefore had no presence in `gamedata.ships`, so [fleetList.js](source/public/client/UI/fleetList.js) had nothing to render. The only fallback was `dockedCraftStashValue` folding the value silently into the carrier's row — which both looked wrong (the user wants a visible Split row mirroring the partial-*launch* UX) and, per the 21.5 docstring, contributes 0 in practice for the no-split model anyway.

**Fix.** Make the partial branch spawn a fragment exactly like the legacy `performLand` path already does: `dockFighters` returns the chosen craft → `spawnFragmentFlight($flight, $chosen, $carrier, $primaryHangar, $gamedata)` mints a `removed=true` "<source> - Split" flight holding their damage/crit/ammo state → stamp the entry `dockedFlightId = $fragment->id`, `name = $fragment->name`, `fragment = true`. The fragment is a real ship row (combatValue > 0, its own `pointCost`), so the fleet list renders it as a "Docked" Split row with the right CP; `dockedCraftStashValue` skips it (it has `dockedFlightId`) so the carrier isn't double-credited; relaunch routes through the standard `resurrectDockedFlight` / `processWholeFlightLaunches` paths; and `Hangar::onIndividualNotesLoaded` already restores `removed`/`spawned` for `fragment`-flagged `dockedFlightId` entries on reload. Base CP is conserved: the remnant lost K fighters' base value, the fragment gained exactly K. PHP lint clean. Single-file change ([HangarOps.php](source/server/model/systems/HangarOps.php), `performWholeFlightDock` partial branch); pure server-side, no bundle rebuild. (Supersedes the line in the 21.1 summary that said the partial branch keeps an anonymous entry / "No `spawnFragmentFlight`".)

**Follow-up A (2026-06-01): `pointCostEnh` travels with the docked split.** The first cut left the fragment at `pointCostEnh = 0` (matching the bare `spawnFragmentFlight` shape) and the remnant kept the FULL enh. But each half physically keeps the missiles it carries (a 3-of-6 dock of 4-missile Kasta → 12 missiles in space + 12 docked), so the enhancement *value* must split with them. Added an enh split mirroring the partial-**launch** side (Stage 21.5): `$totalEnh` read off the source flight (pre-mutation) → fragment gets `round(total*K/N)` (N = `$activeCount` pre-dock, K = `$count`), remnant gets the remainder (exact conservation), both persisted via `Manager::insertSingleEnhValue`. Verified in game 4148: original full Kasta flight had `enhvalue=192`; after dock both rows hold `enhvalue=96` (12 missiles each). **This part is correct and conserved** — but it was NOT the bug the user saw.

**Follow-up B (2026-06-01): the real value bug was the fleet-list combat-value math, fixed in fleetList only.** With the enh split correct, the user still saw the in-space remnant render **180** where a clean flight of 3 shows **228**. Diagnosis (game 4148, confirmed against the DB): the remnant keeps `flightSize=6` with 3 fighters carrying a `DockedFighter` crit (systemids 16/21/26). So `FighterFlight::calculateCombatValue` = `round(100*3/6)` = **50%**, while the fleet-list base still uses the full roster: `pointCost*flightSize/6 + enh` = `264 + 96` = **360**. Product `360*0.5` = **180**. The two errors compound and don't cancel, because the CV multiply also scales the enh term that shouldn't be discounted. **We deliberately did NOT shrink the persisted `flightSize` or touch `calculateCombatValue`** — a flight's per-fighter damage history lives in `tac_damage` keyed by system id and is replayed turn-by-turn, so re-keying/removing fighter slots would corrupt the pre-dock replay (this is exactly why the dock/launch code never mutates a live flight's size; it uses disengage markers + the fragment copy). Instead, [fleetList.js](source/public/client/UI/fleetList.js) gained `activeFlightValue(ship)`: for a flight whose active craft (`!destroyed`, no `DockedFighter`/`DisengagedFighter` crit) are fewer than its roster, it values the hull on the **active count** and recomputes CV over just those craft (same `>50% dmg → 0.75` rule as the server). Remnant now = `264*3/6 + 96` = `132 + 96` = **228** at 100% CV. Returns `null` (→ unchanged default path) for any ordinary/full flight. User's steer: keep the fix in fleetList, not in the more fundamental `calculateCombatValue`/`flightSize` layers. Client-only change; `yarn watch:legacy` rebuilds the bundle (do not hand-edit / commit the bundle). Also incidentally corrects combat-**disengaged** flights with enhancements (enh no longer spuriously scaled by the active ratio).

**Follow-up C (2026-06-01): same bug in reverse on the docked partial-LAUNCH remnant — same fix, removed-guard dropped.** Launching 6 of 12 (game 4151, Razarik off a Primus): the launched "- Split" flight showed the correct **402/402** (`210*6/6 + 192`), but the DOCKED remnant of 6 showed **306/612** instead of 402/402. Identical mechanism: the docked remnant (873319) keeps `flightSize=12` with the 6 launched fighters carrying `DisengagedFighter` crits (systemids 31/36/41/46/51/56), so base = `210*12/6 + 192` = **612** and CV = `round(100*6/12)` = **50%** → `612*0.5` = 306. (The enh was already correctly split — the remnant's `enhvalue` dropped from the full-12's 384 to **192** via the Stage 21.5 partial-launch subtraction, and the launched split also carries 192; sum 384 conserved. So enh was NOT the problem here either.) Fix: `activeFlightValue` now ALSO re-bases removed (docked) flights — the earlier `if (ship.removed) return null` guard was dropped. The fully-docked case stays correct for free: a full dock marks NO per-fighter crit (only the ship is `removed`), so all craft count as active → `active==total` → `null` → its full value stands. Only a docked remnant that launched some craft (per-fighter `DisengagedFighter`) re-bases. Docked remnant now reads **402/402**, summing with the launched split to the original full-12 value (804). The `>50% dmg → 0.75` weighting and the `active>=total → null` fallthrough are unchanged; user confirmed the expected number is 402/402 (symmetric with the launched half).

**Follow-up D (2026-06-02): corrected the re-base rule + added a `SplitLaunchedFighter` marker — SUPERSEDES the value-rule details in B & C.** Testing surfaced that the B/C rule (re-base out every `DockedFighter` + `DisengagedFighter`, in-space-only/then-removed-too) was BOTH too strict and too loose:
- **Too strict (the regression the user caught):** a legacy flight of 4/6 Rutarians that lost 2 to *combat* showed **300/300** instead of **300/450**. Combat-destroyed/dropped-out craft are "lost points" — they STAY in the flight's paid roster (full base, 0 current value), exactly as fighter value worked before Hangar Ops. The B/C rule wrongly re-based them out, shrinking the base.
- **The fix's core principle (user's framing):** `DisengagedFighter` must mean ONLY the B5W combat-dropout (took too much damage, left the game, value lost). It should NOT also tag fighters that *left to their own fleet-list row*. So the partial-launch departure now gets its own crit, **`SplitLaunchedFighter`** (new in [cricialClasses.php](source/server/model/cricialClasses.php); folds into `Fighter::isDestroyed` via `isSplitLaunched`, [fighter.php](source/server/model/systems/fighter.php); stamped by `launchWholeFlight` instead of `DisengagedFighter`; added to `copyFighterStateToTarget`'s source-only skip list). **Requires a hand-added `autoload.php` entry** `'splitlaunchedfighter' => '/server/model/cricialClasses.php'` (user adds; never auto-edited). No client crit class needed — the client matches crits by `phpclass` string.
- **Final `activeFlightValue` rule:** re-base a flight's value (base hull on retained count + CV recomputed over it) ONLY past craft that LEFT to their own row = `DockedFighter` ∪ `SplitLaunchedFighter`. `destroyed` and `DisengagedFighter` craft stay counted in the base and add 0 CV (lost points). Re-base triggers only when `departed > 0` (else `null` → default path, where destroyed/disengaged are handled by the server CV as before). Client crit recognition updated alongside: `getTurnDestroyed` ([damage.js](source/public/client/UI/../damage.js)) folds in `SplitLaunchedFighter`; `criticals.js` gained `isSplitLaunchedFighter`; `FighterIcon.js` (React) + legacy `flightwindow.js` render a launched craft as **LAUNCHED**/cyan (not DESTROYED).
- **Outcomes:** Rutarian combat loss → **300/450** (default path, unchanged). Partial dock 3/6 → **228/228** (DockedFighter re-base). Partial launch 6/12 docked remnant → **402/402** (SplitLaunchedFighter re-base) + launched split 402/402, sum 804. Fully-docked flight → full value (no per-fighter departed crit → `null`).
- **Rail-escape shed craft (done, 2026-06-02):** `escapeShedCraft` previously stamped `DisengagedFighter` on ALL shed craft, so a surviving rail-escape remnant double-counted the escapees (who also appear on the rail-escape "- Split" row). Now split by fate: ESCAPEES get `SplitLaunchedFighter` (excluded from the remnant base, value on the Split row); NON-ESCAPEES get a lethal `DamageEntry` (`destroyed=true`, armour 0, damageclass `RailCrit` — mirrors the rail's own destroy-entry + `applyCatapultLandingDamage`) so they are PROPERLY DESTROYED and stay in the remnant's paid roster at 0 combat value (the row reads `0 / their points`, per the user's spec). The remnant `pointCostEnh` subtraction now drops only the ESCAPEES' share (`round(total*E/N)`) — the dead craft keep their enh share on the remnant as lost points, not moved to another row. `countActiveCraft` is unchanged (both markers + destroyed all fold into `isDestroyed`), so relaunch sizing / box trim are unaffected. PHP lint clean. No fleetList change needed — `activeFlightValue` already excludes `SplitLaunchedFighter` and counts destroyed craft as in-roster/0-CV.

**Follow-up E (2026-06-02): off-by-one combat value — double-rounding fix (fleetList only).** Game 4150: a Frazi flight (6× ~58 = 348) with one fighter combat-disengaged showed **289/348** instead of **290/348**. Root cause is PRE-EXISTING and not Hangar-Ops: `FighterFlight::calculateCombatValue` returns an INTEGER percent (`round(100*5/6)` = `round(83.333)` = **83**), and the fleet list then did `round(348 * 83/100)` = `round(288.84)` = 289, vs the true `348 * 5/6` = 290. Two rounds, lost ~1.16 pts. Fix (user chose fleetList-only): `activeFlightValue` now returns for EVERY flight (not just departed>0) and yields an UNROUNDED combat-value fraction (`100 * cvAccum / roster`); the caller rounds ONCE after `base * cv/100`. The fraction matches `FighterFlight::calculateCombatValue` exactly (1 / 0.75 / 0 weights; that CV has no structure/engine/sensor modifiers, so it's faithful) — only the intermediate integer rounding is dropped. `null` now means only "not a flight / no Fighter subsystems" → default path. Net: Frazi → 290/348; all earlier outcomes unchanged (228/228, 402/402, 300/450 — those were exact fractions already). Server `combatValue`/`calculateCombatValue` untouched (still feeds damage allocation, AI, totals, replay); this is purely how the fleet-list row derives its own displayed current value. Client-only; `yarn watch:legacy`.



Server-side no-split dock landed on the Stage-19 base (PHP lint clean):
- **Occupancy accounting** (additive, legacy-safe): `foreignOccupancyBoxesOn`, `entryBoxesOnHangar`; `usageCountFor($hangar, $ship=null)` now counts an occupancy entry's boxes only on its primary bay and adds boxes other bays' occupancy place on this one (defaults `$ship` to `getUnit()` so existing call sites need no change); `freeBoxesByCategory` passes `$ship` through. An entry with **no** `occupancy` field is the legacy single-bay shape — counted on its own hangar exactly as before.
- **`performWholeFlightDock($carrier,$flight,$count,$bays,$gamedata)`** — docks the whole flight as ONE entry on the primary bay with `occupancy:[{systemId,boxes}]` spanning the fill-order bays (occupancy omitted for single-bay so the common/legacy path is untouched). Full dock → `dockedFlightId`+`removed`; partial → `dockFighters` (anonymous entry, unchanged 21.1 semantics; 21.2 does partial-shrink). No `spawnFragmentFlight`.
- **`processWholeFlightDocks($carrier,$gamedata)`** — once-per-carrier (`dockCoalesceDone` transient on BaseShip) Firing-Phase coalescer: gathers every non-catapult bay's `pendingDockOrder`, groups by `flightId`, docks each whole flight once via `buildDockBays` (player-preferred bays first, top up from any eligible bay). Catapults keep their own per-bay `processDockOrders`.
- **`performDeployStartDock` rewritten** to whole-flight no-split (primary = the processed bay, occupancy across siblings; bails if already `removed`).
- **Dispatch** (`Hangar::criticalPhaseEffects`): rail + ordinary dock steps now call `processWholeFlightDocks`; `performLand` retained only for the jumping-carrier path (reworked in 21.4).
- **`$removed`-restore on load unchanged** — keys on `dockedFlightId`, which the new entry carries, so multi-bay docks restore correctly from the single primary-bay entry. Legacy `fragment` entries still restore via the same loop.

Not yet done in 21.1: launch-side still uses the Stage-19 path (21.2); rails-as-boxes capacity + rail-destruction eviction (21.3); the once-per-carrier dock coalescer runs at the first bay's dock step without a full readiness gate (acceptable for 21.1; revisit in 21.3 if same-turn rail damage + dock interact).

**21.1 test round 2 (2026-05-31) — deploy-dock server-authoritative re-home.** With the round-1 fix deployed, a multi-bay deploy dock then failed outright (`hangar full`) because the validation checked "whole flight fits in ONE bay". Resolved by making the deploy dock **server-authoritative**: `validateDeployBayOrders` now checks only the per-flight gates (slot/owner/both-deploying/customcap), and `performDeployStartDockFromOrders` re-homes the whole flight across bays using TRUE (DB-side, via `$dbCarrier`) free boxes — preferring the client's chosen bays, **dropping full/incompatible ones** (e.g. a universal 2-box bay already full of auto-shuttles), topping up from any other eligible bay, tracking this-pass POST-side commits, and fail-noting only if the carrier genuinely can't hold the flight. The occupancy metadata entry is hosted on the in-flight bay (snapshot pending) even if that bay contributes 0 boxes (usageCountFor reads an occupancy entry's boxes from the bays its occupancy names, not its host). Firing-phase dock was already server-authoritative (`buildDockBays` on the DB-loaded ship), so it's unaffected. NOTE: the client's deploy-dock distribution can still over-optimistically offer a full universal bay — harmless now (server drops it) but worth a client-side tidy later.

**21.1 test round 1 (2026-05-31) — deploy-dock POST-side fix.** First Docker test: single-bay deploy dock ✓ (no occupancy), firing-phase multi-bay dock ✓ (one entry, occupancy 3+3+6, no fragments). BUG: a multi-bay DEPLOY dock spilled a light-fighter unit's boxes into a *shuttle* bay (sys 4) and emitted spurious `hangar full` / `flight already removed` fails. Root cause: deploy-dock resolves on the POST-side hangar objects, whose sibling bays have EMPTY `hangarUsage` — so the server's cross-bay free-box re-distribution saw full bays as empty and mis-placed boxes. Fix: the deploy path no longer re-distributes server-side; it **coalesces the client's per-bay `count` orders** (which were split against true capacity client-side) into ONE occupancy entry. `processDeployStartTransfer` now processes the flights THIS bay ordered, gathers each flight's per-bay counts across all POST-side bays (consuming them), and `performDeployStartDockFromOrders` writes one entry on the in-flight bay (snapshot still pending) with `occupancy` = per-bay client counts. The old re-distributing `performDeployStartDock` was deleted. The firing-phase coalescer is unaffected (it runs on the DB-loaded ship, where sibling capacity is correct).

---



## 5. Database changes

One migration: `tac_individual_notes.notevalue` was `varchar(100)` — too small to hold the JSON payload for multi-hangar carriers. Bumped to `varchar(4096)` via [`db/hangarOpsNoteValue.sql`](db/hangarOpsNoteValue.sql); the canonical schema in `db/emptyDatabase.sql` is updated to match.

Otherwise hangar state lives entirely in `tac_individual_notes` keyed on `(systemid, shipid, gameid, turn)`.

Two new note `notekey_human` strings (free‑form, only for human reading):
- `"hangarLaunch"` — the player‑submitted launch order, picked up at end of turn.
- `"hangarDock"` — the player‑submitted dock order.
- `"hangarDeployStart"` — initial dock done in deployment phase.
- `"hangarUsage"` — serialized JSON of `$hangarUsage` after each end‑of‑turn (so it's restorable across reload).

`notevalue` is the JSON payload. `notekey` is the relevant fighter flight id (or 0 if none).

If we end up wanting structured columns, that's a later refactor.

---

## 6. Risk register

| Risk | Mitigation |
|---|---|
| Existing fighters break because their flight ship is now also being created at deployment time, but `Hangar` thinks it should still be in storage | `HangarOps::populateInitialHangarUsage` reads `$gamedata->ships`, finds existing `FighterFlight`s belonging to this carrier's slot, and **subtracts** them from `$ship->fighters` before filling hangar — only the difference becomes shuttles/empty slots. |
| Replay shows ghost flights after they docked | Use `$removed = true` not `isDestroyed = true`; replay's frame‑by‑frame movement reads `getTurnDeployed` plus a new "removed on turn" marker so the flight visually vanishes on the right turn. |
| `$spawnableClasses` autoload omission causes runtime fatal | Add a CI sanity script (or a one‑shot manual check) that walks every ship file's hangars and verifies every listed phpclass exists in `autoload.php`. |
| Two players' clients fork on what's eligible to dock (lag) | Server is authoritative; eligibility re‑checked in `criticalPhaseEffects`. Ineligible orders dropped + pubnote'd. |
| Caching: APCu serves stale gamedata that doesn't include a launched flight | `Manager::insertSingleShip` already touches the cache key (verify in `Manager.php`); if not, call `Manager::touchGame($gameid)` after each launch/dock. |
| Hangar damage in same turn as a launch order from that hangar | Resolve damage first → hangar may be destroyed → launch order silently dropped with "Hangar destroyed before launch" pubnote. Order matters: damage application currently happens before `criticalPhaseEffects`, so this is automatic; verify with a deliberate test in Stage 4. |
| Auto‑added shuttles inflate point costs in fleet builder | `Shuttle.$isCombatUnit = false` → existing fleet check / battlegroup machinery already skips non‑combat units. **Verify** in the fleet builder code path. |
| Multi‑contributor file conflicts | Per FV contributors note — structural files (Hangar, ShipSystem, FighterFlight, Manager) are effectively single‑author, so this is low. New shuttle classes are mechanical, similar to "add a new ship". |
| Client-side shared-reference trap on system properties not sent in `stripForJson` | Any system field mutated client-side that the server doesn't transmit ends up shared across every same-phpclass instance (shallow `Object.assign` in [SystemFactory.createSystemFromJson](source/public/client/model/systemFactory.js#L76-L100)). Manifested as "two Whitestars show identical hangar contents" during Stage 12 testing. Fix landed in Hangar constructor (deep-clone `this.data`). For future HangarOps stages: prefer sending the field via `stripForJson` (server stays authoritative + protects all consumers automatically); fall back to a constructor deep-clone if you must keep the field client-only. See [memory: arch_client_system_shared_reference](C:\Users\Dougie\.claude\projects\c--FV-env-FieryVoid\memory\arch_client_system_shared_reference.md). |

---

## 7. Out‑of‑scope (call out for later)

- 5 CP fighter↔assault‑shuttle box swap in fleet builder.
- "Reload ordnance from hangar" partial‑flight rule (the rule snippet about reloading missiles while flight stays out).
- Custom launch arrow direction in fleet builder UI (set in code only for now via `$direction`).
- Animation of the launch / land event (currently inherits from generic deploy/destroy animations; a custom launch effect would be polish).

---

## 8. Testing schedule (Docker local, gameID 3730)

Each stage should be tested before moving to the next. The test schedule below assumes you run `docker-compose up -d --build` once and use `yarn watch` / `yarn watch:legacy` for iterative work.

> Reminder: I won't run `yarn build` for you (per your workflow) — `yarn watch:legacy` keeps the legacy bundle live; React updates ride on `yarn watch`.

### After Stage 1 — capacity tracking

- [ ] Start `safeGameID`, deploy a carrier with mixed `$fighters` (e.g. EA Olympus or Hyperion variant). Open hangar in system info → usage matches `$ship->fighters`.
- [ ] Submit a turn (no actions). Re‑open game. Hangar usage persists.
- [ ] Deploy a ship with 2 hangars (e.g. Centauri Balvarin: 36 medium, 3 hangars × 12). Verify only one population pass — total = 36, not 108.
- [ ] DB sanity: `SELECT * FROM tac_individual_notes WHERE notekey_human = 'hangarUsage'` returns expected rows.

### After Stage 2 — shuttles auto‑filled

- [ ] Carrier with `$fighters = ["medium"=>12]` and 14 hangar boxes → 2 shuttles auto‑filled.
- [ ] Orieni Vigilant → 6 minesweeping shuttles instead of plain shuttles.
- [ ] Combat value reported in fleet list unchanged from pre‑Stage‑2.

### After Stage 3 — hangar damage

- [ ] Damage a hangar with a Heavy Plasma in `safeGameID`. Storage count drops by exact damage amount.
- [ ] Empty/shuttle slots drop first; expensive fighters last.
- [ ] Damage > current storage → all storage gone; no negative numbers.

### After Stage 4 — launch

- [ ] Click Launch → confirm dialog opens with correct categories.
- [ ] Launch 6 medium fighters. End turn. New flight appears in carrier's hex, same heading, facing = carrier facing.
- [ ] Storage decreased by 6.
- [ ] Replay shows launch note.
- [ ] Two launches from two different hangars on same ship in same turn → both flights spawn correctly.
- [ ] Try to launch while pivoting (set carrier to pivot in initial orders) → button disabled.
- [ ] Launch with carrier moving speed 6 → new flight has speed 6 in its movement order.
- [ ] Cancel launch (close dialog) → no notes written.

### After Stage 5 — land

- [ ] Place a flight in same hex / heading as a friendly carrier with free hangar space → Dock button appears.
- [ ] Dock → end turn → flight gone from board, hangar storage incremented with the right phpclass.
- [ ] Different heading → Dock disabled.
- [ ] Speed too high (delta > flight thrust) → Dock disabled.
- [ ] Two friendly carriers in same hex → `SelectFromShips` picker opens.
- [ ] Damaged hangar that can fit only 3 of 6 → split dialog → 3 dock, 3 stay in space.
- [ ] Replay still shows the docked flight in earlier turns.

#### Stage 5 testing notes (2026-05-14)

Two bugs surfaced while testing Stage 4/5 on `safeGameID`; both fixed in-place rather than deferred.

**Partial dock orphaned the non-docking fighters.** The split dialog promised "any unallocated craft remain in space" but `HangarOps::performLand` unconditionally set `$flight->removed = true` for the entire flight regardless of `$count`. Fixed by branching `performLand` on `$count >= $flight->countActiveCraft($turn)`:
- Full dock keeps existing behaviour (`$removed = true`, stash record carries `dockedFlightId` for ship-id resurrection on relaunch).
- Partial dock applies the existing `DisengagedFighter` critical to N active Fighter subsystems via a new `disengageFightersForDock` helper, leaves `$flight->removed` alone, and omits `dockedFlightId` from the stash record so relaunch always spawns a fresh flight. Reusing `DisengagedFighter` (instead of introducing a new `DockedFighter` crit) means no client-bundle churn or autoload entry; the `hangarDockEvent` note now ends in `:partial` to distinguish the audit trail in replay.

Trade-off accepted: partially-docked fighters render as "destroyed" in the flight window (same as any disengaged fighter) — the hangar replay note is what tells the player they docked rather than disengaged. Worth revisiting only if Stage 9 polish wants a dedicated `DockedFighter` critical with its own CSS.

**Newly launched flights couldn't be selected on their first turn under Simultaneous Movement.** Root cause: `FireGamePhase::advance` does its work on a *local* `$servergamedata` (the fresh DB load), and `Manager::insertSingleShip` mutates that local copy — but the outer `$gamedata` that gets passed back to `Manager::changeTurn` never sees the spawned ships. `generateIniative` therefore iterated stale ships, no `tac_iniative` row was written for the new flight on the new turn, and `SimultaneousMovementRule` couldn't match its default `"N/A"` iniative to any category. The flight showed up in OOB (the OOB renderer doesn't filter on iniative-category match) but wasn't selectable in the Movement Phase. Self-resolved on turn 2 because by then the outer `$gamedata` of the *next* `changeTurn` call did include the flight.

Fixed by reordering `Manager::changeTurn`: persist the turn/phase/activeship/status to DB first, then reload the gamedata, then run `generateIniative` on the fresh copy. The follow-up `setPreturnMovementStatusForShip` loop now uses the same already-loaded gamedata instead of doing a second redundant `getTacGamedata`. The fix benefits any runtime-spawned unit, not just launched fighter flights — mines spawned via `missile.php::createLoiteringMine` go through the same path. Mines didn't expose this bug only because they're filtered out of movement at every step (`$ship->mine`), so the missing iniative row was harmless for them.

Risk register update: the parent risk row "Caching: APCu serves stale gamedata that doesn't include a launched flight" remains live but is upstream of this fix; the `changeTurn` reload is a synchronous DB hit, not APCu.

#### Stage 5 testing notes — round 2 (2026-05-14)

Three follow-up issues found while exercising the Firing Phase dialogs and size-mismatched docks.

**Launch dialog now persists/re-edits queued orders.** The dialog was a one-shot — reopen during the same Firing Phase and every input reset to 0, so players couldn't adjust a previous launch order without re-entering everything. Fix spans both sides:
- `Hangar::stripForJson` now ships `pendingLaunchOrder` (the latest hangarLaunchOrder note for the current turn, set by `onIndividualNotesLoaded`).
- `Hangar` constructor in client `baseSystems.js` hydrates `pendingLaunchOrders` (plural, client-side queue) from that on each gamedata load.
- `confirm.js::hangarLaunch` pre-fills each per-phpclass input from `hangar.pendingLaunchOrders`, no longer subtracts pendingLaunchOrders from the displayed budget (we're editing them), and on OK seeds every hangar shown — including hangars whose inputs all ended up at 0 — into a `byHangar` map so a "cleared everything" OK still ships an explicit empty list.
- OK now also validates the aggregate launch + dock total against the shared output budget and alerts/blocks if exceeded (the old per-input `max` couldn't catch oversubscription across multiple phpclasses).

The dirty-flag plumbing in `Hangar.doIndividualNotesTransfer` (`pendingLaunchOrdersDirty` / `pendingDockOrdersDirty`) is what makes "cancel everything" propagate. Without it the payload would silently skip an empty list and the server-side hangarLaunchOrder note from earlier in the Firing Phase would still fire at end-of-turn. The server side mirrors this — `Hangar::doIndividualNotesTransfer` now keys on `array_key_exists('launches', $payload)` instead of `!empty($cleanLaunches)` so an explicit empty array still produces a fresh `hangarLaunchOrder` note that the loader picks up as "latest wins."

**Dock dialog gains the same persistence and a cancel path.** Same shape: server stripForJson now ships `pendingDockOrder`, client constructor hydrates `pendingDockOrders`, `confirm.js::hangarDock` checks `findCarrierWithQueuedDocks` before falling through to the carrier picker so a re-edit jumps straight to the splitter for the carrier where the order lives. The splitter pre-fills each hangar's input from the queued counts, reports `(free: X+queued, max: ...)` so the displayed budget includes whatever's about to be replaced, and on OK calls `replaceDockOrdersForFlight` which atomically strips the flight's prior entries from every hangar shown before re-adding the new counts. `showSimpleConfirm` (the single-hangar shortcut) carries the preset through so the user can confirm the same value or change it.

`findEligibleCarriersForDock` was also tweaked so a carrier doesn't lose eligibility when the flight already has an order there — it skips THIS flight's own `pendingDockOrders` when computing `free`/`budget`, so the capacity reported on re-edit is what it was before queuing rather than zero.

Switching carriers without zero-ing first is not supported (deliberate UX simplification): clear the current carrier's allocation, OK to commit the cancel, click Dock again to see the picker. Worth adding a "switch carrier" button only if testing shows it being missed.

**Hangar slot size hierarchy now enforced at runtime.** Stage 5 was accepting heavy fighters into medium hangars because `HangarOps::hangarAcceptsCategory` only knew "exact match" + "universal `fighters` slot" + "shuttles fit anywhere". The fleet-builder validator (`checkChoices` in `gamelobby.js`) has always enforced the proper hierarchy through its capacity totals (a heavy slot also counts toward medium/light/ultralight capacity, a medium slot toward light/ultralight, etc.) — runtime docking just wasn't honouring it.

Fix:
- New `HangarOps::trueSizeOf($flight)` returns the flight's actual size category: explicit `$hangarRequired` wins for non-generic types (`'Breaching Pods'`, `'assault shuttles'`, `'Raiders'`, custom names…), generic `'fighters'`/`'normal'` falls back to `jinkinglimit` buckets matching `checkChoices` (≥99 ultralight, ≥10 light, ≥8 medium, ≥6 heavy).
- `hangarAcceptsCategory` extended with a fighter `sizeRank` table (heavy 4, medium 3, light 2, ultralight 1) — a slot accepts its rank or smaller. Shuttle/minesweeping-shuttle compatibility against any combat-fighter slot is preserved. Assault-shuttle slots now also explicitly accept Breaching Pods (mirrors the BP-compat list in `checkChoices`).
- `canShipReceive`, `eligibleHangarsForLanding`, and `performLand` all switched from `categoryFor($flight, $carrier)` (carrier-biased — would return `'medium'` for a heavy flight on a medium-only carrier) to `trueSizeOf($flight)` so a heavy fighter on a medium-only carrier is correctly rejected, while a medium fighter on a heavy-only carrier is correctly accepted.
- The client mirror (`shipTooltipFireMenu.js::collectReceivingHangars`) gained the same `hangarAcceptsCategory` helper so the eligibility gate and dock dialog don't disagree with the server's end-of-turn check.

`categoryFor` was removed once `trueSizeOf` replaced every fit-check call site (no remaining callers; if the bookkeeping use-case it was originally written for materialises, re-introduce a fresh helper rather than reviving the carrier-biased one).

#### Stage 5 testing notes — round 3 (2026-05-14)

Three follow-ups from a Var'Nic + Frazi test pass.

**Launch budget label now tracks current inputs live.** (Confirmed in retest.) The header `(launch budget: X / Y)` used to be a snapshot at dialog open. Now X is a live `<span class="launch-budget-remaining">` that recomputes from every `launchSize` input belonging to this hangar on `input`/`change`. Goes red when the user oversubscribes (the OK-time aggregate check still blocks submit, but the colour gives instant feedback). Seeded after the rows are built so a pre-filled preset shows the right remaining on open.

**Dock dialog always uses the splitter.** Per UX feedback the single-hangar / single-carrier auto-confirm shortcut (`showSimpleConfirm`) was confusing for re-edits and cancels. Removed entirely; every Dock click now opens the per-hangar splitter so the player has full visibility and can amend or cancel with the same dialog. Header comment on `hangarDock` updated to reflect the new flow. `showSimpleConfirm` function deleted (was only called from the now-removed shortcut).

Splitter input now starts at `0` for new docks and at the queued count for re-edits — the previous default of "max" was misleading because no fighters are actually docked until end-of-turn; the player should explicitly opt in. The `free:` label was also fixed: it was reading `h.capacity + preset` and reporting numbers like `free: 12` for a 6-box hangar with 6 already queued. Now shows `h.capacity` (true free / budget treating this flight's own queue as reclaimable), with `max:` still reflecting `min(h.capacity + preset, totalToDock)` so the input can grow into the reclaimable space.

**Hangar slot-size hierarchy actually enforced.** (Confirmed in retest: Frazis can no longer dock into a Var'Nic.) The previous round's `trueSizeOf` + `hangarAcceptsCategory` were correct, but the bug surfaced on the Var'Nic vs Frazi test because the Var'Nic's `Hangar` system was constructed via `new Hangar(5, 7)` — defaulting `$hangarType` to `'fighters'` (universal), even though the ship's `$fighters` declared `['medium' => 6]`. The size hierarchy was being bypassed by the universal-slot shortcut, so heavy Frazis happily docked into the Var'Nic's medium-only hangar.

Two fixes:
- `Hangar::__construct` coerces `$hangarType` to `'fighters'` when it isn't a non-empty string. Some legacy ship files (torata) pass a literal `0` as the 4th positional arg (a no-op left over from the pre-HangarOps signature); this was previously setting `$hangarType = 0`, which `hangarAcceptsCategory` rejected for everything — broken in the opposite direction. The coerce hardens both cases.
- New `HangarOps::inferHangarType($hangar, $ship)` called from `Hangar::onIndividualNotesLoaded` on every load. When the Hangar's `hangarType` is still `'fighters'` / `'normal'` / empty AND the ship's `$fighters` declares exactly one size-specific category (`heavy` / `medium` / `light` / `ultralight`), it narrows the Hangar's `hangarType` to that category. Multi-size carriers (Cylon Basestar with three categories) are left universal because we can't tell from here which `Hangar` instance corresponds to which size bay — those ships still need an explicit `hangarType` in the ship file. `'normal'` declarations are also left alone (intentional universal). Idempotent: only acts on universal/empty types, so once a hangar is narrowed it stays narrowed even after a reload.

### After Stage 6 — initiative penalties ✓ COMPLETE

- [x] Turn after launch, launched flight has -50 initiative; carrier has -20.
- [x] Two turns after launch, both back to normal.
- [x] Two flights launched from one carrier same turn → carrier still has exactly one -20.
- [x] Turn after dock (no launch) → carrier has -20; launched flight unaffected.
- [x] Launch and dock same turn from same carrier → carrier has exactly one -20.

### After Stage 7 — deployment hangars ✓ COMPLETE

- [x] In a fresh `safeGameID` deployment, dock a flight before placing it on the map.
- [x] Place carrier; verify flight is gone from deployment list and hangar usage shows it.
- [x] Turn 1: launch the docked flight → reappears as expected.
- [x] Reinforcement flight (deploy turn 3): on turn 3, dock into a previously deployed carrier without placing on board.
- [x] Same-hex pending flights cannot overfill a hangar via the dock dialog (Issue 1, round 1).
- [x] Docked flights are skipped by Movement-Phase active-ship selection under Standard and Simultaneous rules, and by area-weapon scans like PulsarMine (Issue 2, round 1).

#### Stage 7 testing notes — round 1 (2026-05-15)

Two bugs surfaced once the Stage 7 dock dialog was exercised on `safeGameID`.

**Dock dialog let the player overfill hangars from same-hex pending flights.** Each row in `confirm.js::hangarDeployDock` computed its own `eligibleHangarsForFlight` snapshot at dialog open, so three same-hex 6-fighter flights all individually saw "12 free" against a 12-box hangar and could be checked simultaneously; server's `processDeployStartTransfer` then committed the first that fit and silently dropped the rest with `hangarDeployStartEvent` fail notes — the player saw "I docked 18 fighters" but only 12 made it.

Fix is purely client-side (server validation was already correct):
- Pre-compute `baseFreeByHangar` once at dialog open. Reservations from flights NOT shown in the dialog stay counted as committed; reservations from flights IN the dialog are reclaimable because the dialog's OK will rewrite them.
- New `computePerHangarUsage` walks `rowData` on every checkbox/dropdown change and sums `flight.flightSize` per chosen hangar.
- New `recomputeCapacity` renders a live header (`Hangar X: used/avail`) that turns red on overflow and dims the OK button as a visual cue.
- OK click runs the same aggregation; if any hangar exceeds capacity it alerts (`Cannot dock: capacity exceeded in Hangar X (18/12) …`) and refuses to commit instead of letting the server silently drop the overflow.

Implemented via `$check.on('change', recomputeCapacity)` + `$hangarPick.on('change', recomputeCapacity)` per row, seeded once after build to render the pre-checked state. The OK validator runs `computePerHangarUsage` against `baseFreeByHangar` and bails before queueing if any hangar is over.

**Docked flights still got their own Movement-Phase turn — and could be targeted by area weapons.** Stage 5 added `removed` and `BaseShip::isOnBoard()` for "is this ship still in play," but the new helper was never adopted: the hot spots (and 379 other call sites across 52 PHP files) all check `!$ship->isDestroyed()`, which Stage 5 deliberately left returning `false` for docked flights. So under Standard rules `setNextActiveShip` picked the docked flight as next active ship; under Simultaneous rules `getNewActiveShip`/`hasShipsAtIniative` returned it in its initiative category. The IniGUI hid it client-side (client `shipManager.isDestroyed` HAS folded `removed` in since Stage 5), but the activeship pointer still landed on it and froze the phase waiting for orders. Same root cause: `PulsarMine::beforeFiringOrderResolution` (and similar weapon scans like `SparkField`) iterate `gamedata->ships` and use `if ($ship->isDestroyed()) continue;` to skip the dead — without folding in `removed`, a docked flight inside a friendly carrier was a valid pulsar-mine target.

Initial fix tried to add `!$ship->removed` at each Movement-Phase entry point individually. Backed out in favour of the central fix below — adding the same one-liner at 379+ call sites is impractical and the next dual-use bug like the pulsar mine would be one more such site to remember.

Centralised by folding `removed` into the three ship-level `isDestroyed()` overrides themselves so all 379 callers transparently get the right answer:
- `BaseShip::isDestroyed` ([ShipClasses.php:1922](source/server/model/ships/ShipClasses.php#L1922))
- `StarBase::isDestroyed` ([ShipClasses.php:3191](source/server/model/ships/ShipClasses.php#L3191)) — bases don't dock today, kept consistent with parent in case a future base-class carrier appears
- `FighterFlight::isDestroyed` ([FighterFlight.php:461](source/server/model/ships/FighterFlight.php#L461)) — the actual override that matters for docked flights

Each gains a one-line short-circuit at the top: `if ($this->removed && ($turn === false || $turn >= $this->removedTurn)) return true;`. The turn-aware form mirrors the inverse logic in `isOnBoard` so replay still gets the right answer for "was this ship docked AS OF turn N" — a flight removed on turn 5 stays "alive" for `isDestroyed(3)` and is "gone" for `isDestroyed(5)`. `isOnBoard` collapses to `return !$this->isDestroyed($turn);` and is kept as a positive-predicate alias for self-documenting call sites.

Safety notes:
- Destruction explosions are gated on `damageManager::getTurnDestroyed`, not `isDestroyed()`, so no false explosions fire for docked flights.
- Combat-value calculation now reports 0 for docked flights (`if($this->isDestroyed()) $effectiveValue = 0;` in `calculateCombatValue`). This matches the intuition that a stashed flight isn't combat-effective; Stage 9 polish was already scoped to credit the carrier with stash combat value.
- Faction initiative bonuses (`doCentauriInitiativeBonus` etc.) skip docked flights as fleet contributors, which is correct.
- HangarOps' own `if ($flight->removed || $flight->isDestroyed())` guards in `canDeployStartDock` / `canShipReceive` become tautological but stay harmless; left in place as belt-and-braces self-documentation.

### After Stage 8 — multi‑direction

- [ ] Decurion launches assault shuttles → facing offset by hangar's `$direction`.
- [ ] Balvarin's three hangars launch in their respective directions.

### After Stage 9 — polish + ungating ✓ COMPLETE

- [x] Docked flights labelled "Docked" in blue, not "Destroyed" in red.
- [x] Partial relaunch from a `dockedFlightId` stash entry: source flight row disappears (all fighters disengaged), launched flight value is correct, anonymous orphans add to carrier value, fleet total balances.
- [x] Carriers credited with point cost of every anonymous `hangarUsage` entry; auto-filled shuttles (pointCost 0) contribute nothing.
- [x] Hangar damage that evicts a `dockedFlightId` entry now disengages the matching number of fighters in the source flight (fleet-list value drops).
- [x] Total hangar destruction wipes every linked source flight's active fighters.
- [x] `safeGameID` gate removed (server + client); launch/dock available in all games.
- [ ] Partial flight (1 fighter) launch.
- [ ] Split a 6‑fighter flight across 2 hangars on same ship.
- [ ] Pre‑existing live‑game (non‑safe) gameID: open game with no hangar interaction → behaves identically to today (regression guard).
- [ ] Smoke‑test once on a non‑safe game now that the gate is gone.

---

## 9. Suggested commit cadence

One commit per stage, message style matching the repo:

```
Hangar Ops Stage 1: capacity tracking
Hangar Ops Stage 2: Shuttle class + auto-fill
Hangar Ops Stage 3: damaged hangars destroy stored craft
Hangar Ops Stage 4: launch in Firing Phase (gated to safeGameID)
Hangar Ops Stage 5: land in Firing Phase
Hangar Ops Stage 6: launch initiative penalties
Hangar Ops Stage 7: dock during Deployment phase
Hangar Ops Stage 8: hangar launch direction
Hangar Ops Stage 9: polish + remove safeGameID gate
Hangar Ops Stage 12: shuttle-slot enhancements (HANG_BP, HANG_MSW)
Hangar Ops Stage 13: lobby fleet-check polish + shuttle/fighter overflow rule
Hangar Ops Stage 14: reloadable weapons rearm while docked
Hangar Ops Stage 16: catapults (single-fighter superheavy launchers)
Hangar Ops Stage 17: legacy ballistic missile reload via ordnance pool
Hangar Ops Stage 18: hangar craft escape from destroyed carriers
```

Per `feedback_fv_workflow`: don't commit the regenerated `*.legacy.bundle.js` files in any of these.

---

## 10. Pre-deploy cleanup pass (2026-05-23)

A dead-code sweep performed before the first live-server deploy. Three files changed, net −303 / +21 lines. PHP lint and JS `node --check` clean on all three.

### HangarOps.php — 5 dead items removed (−61 lines net)

**Root cause.** Stage 1 added a `collectUndeployedFlightsByCategory($ship, $gamedata)` placeholder to support pre-populating the hangar with flights the player chose to start stored. The placeholder always returned `array()`. Stage 7 ended up using the `pendingDeployStartTransfer` path instead, so the placeholder — and everything that depended on it — became permanent dead code.

**Removed:**

| Item | Why dead |
|---|---|
| `collectUndeployedFlightsByCategory($ship, $gamedata)` | Stage 1 placeholder; always returned `array()`. Stage 7 used `pendingDeployStartTransfer` instead. |
| `pickHangarForCategory($hangars, $category, $flightSize)` | Only called from the Step 1 undeployed-flights loop; dead with it. |
| Step 1 of `populateInitialHangarUsage` (the undeployed-flights loop) | Driven by `collectUndeployedFlightsByCategory` — walked an always-empty array; zero-trip loop. |
| `capacityByCategory($ship)` | One-liner wrapper around `$ship->fighters`; never called from anywhere. |
| `usageByCategory($ship)` | Aggregated `hangarUsage` by category; never called from anywhere. |

**Docstring updates:** `populateInitialHangarUsage` steps renumbered (old Step 2 → Step 1, old Step 3 → Step 2). Added note that Stage 7 deploy-dock uses the `pendingDeployStartTransfer` path, not the removed Step 1. Updated cross-references in `getDefaultShuttles` docstring ("step 3" → "step 2").

Note: `syncSourceFlightsOnLaunch` and `removeFromHangarUsage` were already marked dead (per Stage 10.5 implementation notes) and are left in place pending burn-in. They are candidates for a second cleanup pass after a few live games.

### baseSystems.php — commented-out Stored Craft block removed (minor)

`Hangar::setSystemDataWindow` contained a 16-line commented-out block that would have computed a "Stored Craft" tooltip line server-side. It was commented out because the live implementation lives in `Hangar.refreshHangarTooltip` (client-side `baseSystems.js`) which has access to `pendingDockOrders`/`pendingLaunchOrders` for live projections. Replaced with a one-line comment pointing to the client-side implementation.

### DeploymentPhaseStrategy.js — dead `window.DeploymentDock` + 6 helpers removed (−207 lines)

`window.DeploymentDock` is defined in two places: an early Stage 7 implementation at the bottom of `DeploymentPhaseStrategy.js`, and the canonical IIFE-encapsulated rewrite in `DeploymentDock.js`. Per `game.php` load order (line 239: `DeploymentPhaseStrategy.js`; line 241: `DeploymentDock.js`), `DeploymentDock.js` loads last and overwrites the earlier definition.

**Removed from `DeploymentPhaseStrategy.js`:**
- The `window.DeploymentDock = { … }` block (~116 lines) — entirely superseded by `DeploymentDock.js`.
- 6 helper functions only used by that dead block: `flightHasCommittedPosition`, `flightQueuedToCarrier`, `carrierHasQueuedDocks`, `computeFreeBoxes`, `trueSizeOfFlightForDock`, `hangarAcceptsCategoryForDock`.

Replaced with an 8-line breadcrumb comment documenting that the canonical implementation is in `DeploymentDock.js`.

### Flagged for a future refactor (not done)

Significant helper duplication exists across three client files but was deliberately deferred — it is cosmetic, not a correctness issue, and is better addressed as a separate PR after live testing:

- **`confirm.js`** — `hangarLabelFor` + `hangarLabelByIdFor` duplicated inside both the `hangarRecover` (line ~2725) and `hangarDeployDock` (line ~3139) closures; inline `locationPrefixFor` logic repeated in both `hangarLaunch` and `hangarDock`.
- **`shipTooltipFireMenu.js`** — `categoryForFlight`/`categoryForFlightRecover`, `hangarAcceptsCategory`/`hangarAcceptsCategoryRecover`, `customFighterRemainingFor`/`customFighterRemainingForRecover`, `lowerKeys`/`lowerKeysR` each duplicated between the `findEligibleCarriersForDock` and `findEligibleFlightsForDocking` closures.
- **`DeploymentDock.js`** — some of the same helpers exist a third time.

These are candidates for a shared-helpers extraction PR once the feature has been stable in live games.

---

## Stage L (LCV Rails) — whole-ship dock/launch on DockingCollar — IMPLEMENTED 2026-06-04

B5W "LCV Rails" (FV class `DockingCollar`). Unlike every prior stage, the docked
unit is a **full `BaseShip`** (`LCV extends MediumShip`, `hangarRequired = 'LCVs'`),
NOT a `FighterFlight`. The entire FighterFlight stash pipeline (`hangarUsage`
occupancy, phpclass coalescing, escape buckets) is bypassed; LCV docking toggles
the LCV's own ship row (`removed`/`spawned` + a deploy `MovementOrder`) and records
the rail→LCV link in a per-rail `lcvDocked` note.

### Model
- `DockingCollar extends Hangar` + `public $isLCVRail = true` (single discriminator,
  parallel to `$isCatapult`/`$isRail`). ctor → `parent::__construct($armour,$maxhealth,$output,0,'LCVs')`.
  **Unlike FighterRail, an LCV rail is an ordinary targetable system with its own HP**
  (baLCVCarrier gives it a hit-chart entry), so it is NOT excluded from combat value.
- Capacity is 1 LCV per rail (boxes are HP). Enforced by the dock handler, not box math.
- Excluded from the fighter pipeline at every entry point: `populateInitialHangarUsage`,
  `getDefaultShuttles`, `primaryHangar`, `inferHangarType`, both `eligibleHangarsForLanding`
  loops. (The fighter dialogs/gates all enumerate `'hangar'/'catapult'/'fighterRail'`,
  so `'dockingCollar'` never leaks in.)

### State persistence (Hangar class, baseSystems.php)
- Per-rail `lcvDocked` note `{shipId, dockTurn, railDmgAtDock}` (null = empty),
  snapshot-persisted in `generateIndividualNotes` (runs for alive AND destroyed
  carriers — a cleared link must persist), reloaded in `onIndividualNotesLoaded`
  where the LCV's `$removed` flag is re-applied. **`removed`/`spawned` are NOT DB
  columns — they're re-derived from this note each load** (same model as fighter
  `dockedFlightId`). Orders: `lcvDockOrder`/`lcvLaunchOrder` notes from client
  `lcvDocks`/`lcvLaunches` payload keys (parsed in `doIndividualNotesTransfer`).

### HangarOps handlers (all whole-ship, none touch hangarUsage)
`collectLCVRails`, `shipHasLCVRail`, `lcvDockedOn`, `dockedLCVCount`, `isLCVUnit`,
`lcvRemainingThrust` (server backstop), `canLCVDock`/`performLCVDock`,
`canLCVLaunch`/`performLCVLaunch`, `processLCVDockOrders`/`processLCVLaunchOrders`
(driven from the new `isLCVRail` branch in `Hangar::criticalPhaseEffects`),
`applyLCVStructureDamage`, `reinitLaunchedShipSystems`, `applyLCVLaunchCrit`,
`onLCVRailDestroyed` (forced launch + railDmg + 2d10), `processLCVCarrierDestruction`
(Pass-3 sibling of `processCarrierDestructionEscapes`, wired into `criticals.php`),
`loadOrRollLCVFrag` (2d10; replay-safe via cleared-`lcvDocked` idempotency, NOT a
persisted roll — same idempotency as FighterRail escape).

### Dock conditions (server-enforced hard gate, `canLCVDock`)
carrier speed 0 · LCV ends in carrier's hex (`OffsetCoordinate::equals`) · matching
heading · ≥1 thrust unspent (**client sends `thrustLeft` from `getRemainingEngineThrust`;
server bounds-checks AND requires its own `lcvRemainingThrust` backstop ≥1**) · rail
free + has launch/land budget. Launch inherits carrier facing (direction 0) + speed.

### Penalties (FINAL values — see correction history in later sections)
- **−10 initiative per docked LCV:** `getInitiativebonus` computes `$lcvDockPenalty`
  (10 × dockedLCVCount) early and folds it into EVERY return (faction early-returns
  bypass the default tail). Gated on the `$LCVCarrier` flag (perf). NB: `dockedLCVCount`
  must NOT use `isDestroyed()` to exclude the occupant — a docked LCV is `$removed` so
  isDestroyed() is true; it checks the LCV's PRIMARY structure instead.
- **+1 turn cost & +1 turn delay per docked LCV — as a per-turn THRUST surcharge, NOT a
  stat change.** The ship's `turncost`/`turndelaycost` STATS are unchanged; `stripForJson`
  sends `ship.dockedLCVs` (count). Client `movement.js` `getDockedLcvTurnSurcharge(ship)`
  adds a FLAT `+dockedLCVs` to the final turn-cost value at every `Math.ceil(speed*rate)`
  site (incl. speed-0 + the requiredThrust builder) and to turn delay in `calculateTurndelay`
  / `calculateTurndelayAtMove`. `ShipTooltip.js` mirrors it (and was fixed to show the
  min-1 turn cost at speed 0). E.g. turn-cost-1 carrier at speed 5 + 4 docked LCVs = 9.
- **−50 initiative on a launched LCV** (same as a launched fighter flight): `LCVLaunchedThisTurn`
  crit (cricialClasses.php), stamped on the LCV's CnC by `applyLCVLaunchCrit`, applied in
  `BaseShip::getIniativeMod`'s CnC block. Cyan "Just Launched" tooltip flag via
  `hasCriticalInAnySystem`. **NEEDS hand-added autoload entry**
  `'lcvlaunchedthisturn' => '/server/model/cricialClasses.php'`.
- **Perf:** `BaseShip::$LCVCarrier` (set in `addSystem()` when a DockingCollar is mounted)
  gates the per-turn LCV checks so non-LCV-carrier ships skip them with one boolean.

### Damage rules (L6)
- Landing on a damaged rail → LCV Structure damage = rail's sustained damage
  (`performLCVDock`, persisted `DamageEntry` on the LCV's `getStructureSystem(0)`).
- Destroyed occupied rail → forced launch + Structure damage = railDmg + 2d10 Matter
  (`onLCVRailDestroyed`, fired from the `isLCVRail` branch in `criticalPhaseEffects`).
- Carrier destroyed with docked LCVs → each LCV escapes (forced launch + railDmg + 2d10),
  `processLCVCarrierDestruction` (LCVs aren't FighterFlights so the Stage-18 escape
  pass skips them).

### Client (L2/L5)
- `DockingCollar` mirror `extends Hangar` with `isLCVRail`, own `doIndividualNotesTransfer`
  (lcvDocks/lcvLaunches), own `refreshHangarTooltip` (0/1 readout). System `stripForJson`
  ships `isLCVRail` + `lcvDocked` + pending LCV orders. `SystemIcon` benign-crit cyan
  extended to `LCVLaunchedThisTurn`.
- **Reused the existing fire-menu buttons (no new buttons, per user):** "Enter Hangar"
  (`dockFlight`) now accepts an LCV via `isDockableUnit` → routes to `confirm.lcvDock`;
  "Recover Flights" → `confirm.lcvRecover` when only LCVs receivable; "Launch Fighters"
  → `confirm.lcvLaunch` when only LCV rails launchable. Both dock entry points (LCV side
  + carrier side) per user. Clicking an own LCV-rail icon in Firing → `lcvLaunch`.
- New minimal dialogs `confirm.lcvDock` / `lcvRecover` / `lcvLaunch` (one ship, one
  rail, one click). New `window` helpers: `isLCVRailSystem`, `lcvRailFree`,
  `lcvRailLaunchable`, `hasLaunchableLCVRail`, `hasLaunchableFighterHangar`,
  `lcvRemainingThrust`, `findEligibleLCVRailsForDock`, `findEligibleLCVsForRecover`.

### Files (final set across the whole stage)
Server: `baseSystems.php` (DockingCollar class + Hangar lcvDocked state/notes/strip +
criticalPhaseEffects branch + setSystemDataWindow + lcvDeployStarts parse), `HangarOps.php`
(all LCV handlers + exclusion guards + processLcvDeployStartTransfer + collectQueuedDeployStartFlightIds
LCV ids), `ShipClasses.php` (getInitiativebonus −10N gated on $LCVCarrier, stripForJson dockedLCVs
count + hangarRequired, getIniativeMod −50, $LCVCarrier flag + addSystem set), `cricialClasses.php`
(LCVLaunchedThisTurn −50), `criticals.php` (processLCVCarrierDestruction wiring), `DeploymentGamePhase`
(unchanged — exemption flows through collectQueuedDeployStartFlightIds).
Client: `baseSystems.js` (DockingCollar mirror + lcvDeployStarts + tooltip), `shipTooltipFireMenu.js`
(button reuse + helpers), `confirm.js` (lcvDock/lcvRecover/lcvLaunch + deploy carrier picker +
appendLcvRecoverSection/appendLcvLaunchSection), `SystemIcon.js` (click + cyan), `DeploymentDock.js`
(LCV deploy-dock helpers + tooltip refresh), `DeploymentPhaseStrategy.js` (click interception +
validation exemption), `PhaseStrategy.js` / `ships.js` / `gamedata.js` (pendingLcvDeployDock
visibility skips), `SelectFromShips.js` (LCV DOCK-TO button), `movement.js` (turn-cost/delay thrust
surcharge), `ShipTooltip.js` (turn-cost surcharge + speed-0 min-1 fix + "Just Launched"), `systems.js`
(LCVs excluded from shuttle pool), `confirm.css` (centred header).

### Verification status — COMMITTED 2026-06-04 (runtime-tested by user on game 4153)
Dock/launch (firing + deploy-dock), penalties (−10N ini, +N turn-cost/delay thrust, −50 launch),
fleet-list value for a docked LCV, landing-on-damaged-rail, destroyed-rail / carrier-death paths,
and the $LCVCarrier perf gate all verified. Non-LCV Hangar Ops (fighters/catapults/fighter-rails)
confirmed unaffected (StrikeCarrier + mixed BAEscortCarrierLCVRails regression checks). Legacy
bundles were NOT committed (regenerated artifacts). Autoload entry still required (below).

### Autoload (user must add — never edited here)
`'lcvlaunchedthisturn' => '/server/model/cricialClasses.php',`
(DockingCollar already had an autoload entry; it now extends Hangar in the same file,
so no change needed there.)

### Stage L — UI refinements (2026-06-04, post-first-test)

Three UX fixes after first hands-on dock test:
1. **`lcvDock` dialog now has an explicit "Dock this LCV" checkbox** (was a bare
   carrier label with no confirm/un-declare control). Pre-checked when an order is
   already queued for the LCV; OK clears-then-rewrites based on the checkbox so
   unchecking un-declares and the carrier dropdown re-routes. New helpers
   `window.lcvQueuedDockRail(lcv)` / `window.clearQueuedLcvDock(lcv)` (shipTooltipFireMenu.js).
   The LCV's "Enter Hangar" button now stays visible while an order is queued
   (`hasEligibleCarrierInHex` falls back to `lcvQueuedDockRail`) so it can be un-declared
   from the LCV side even when no free rail remains.
2. **LCV-rail tooltip "Stored Craft" line** — `DockingCollar.refreshHangarTooltip`
   now writes `data["Stored Craft"]` with the LCV name + `(Recovering)` / `(Launching)`
   projection (mirrors the fighter hangar line; rendered generically by SystemInfo.js).
3. **Combined recover dialog** — `confirm.appendLcvRecoverSection(container, carrier)`
   appends an LCV checkbox section (one row per eligible in-hex LCV, **self-committing**
   on change — independent of the fighter OK allocation). Injected into BOTH the
   fighter `hangarRecover` empty-flights branch AND the main flow, so a carrier with
   fighters AND LCVs shows them together. `openHangarRecover` always routes to
   `hangarRecover` now; `confirm.lcvRecover` kept as a thin standalone wrapper for the
   LCV-rail icon click. Greedy free-rail assignment; over-subscription auto-unchecks
   with an alert.

### Stage L — recover/launch dialog polish (2026-06-04, second test pass)

Two follow-ups after the combined-dialog test:
1. **LCV recover checkboxes now commit ONLY on OK** (were self-committing on
   change). `appendLcvRecoverSection` returns `{count, commit, hasOverflow}`; the
   host dialog's OK handler calls `commit()` (and `hasOverflow()` to bail with an
   alert when more LCVs are checked than free rails). Cancel discards. Wired into
   all three hosts: standalone `lcvRecover`, the fighter `hangarRecover` empty-flights
   branch, and its main flow (commit runs after the fighter allocation).
2. **Live LCV-rail capacity pill strip** in both the recover and launch dialogs
   (mirrors the fighter "Hangar capacity:" pills). Recover: each rail ticks 0/1 → 1/1
   as a box is checked (greedy free-rail assignment for the projection). Launch: each
   occupied rail ticks 1/1 → 0/1 as its launch box is checked. Pure display — rebuilt
   by `recompute()` on every checkbox change; the actual writes still happen on OK.

### Stage L — bug fixes from game 4153 test (2026-06-04, third test pass)

1. **Docked LCV showed 0/200 in the fleet list.** Root cause: a docked LCV is
   `$removed`, so `BaseShip::isDestroyed()` returns true (Stage-7 behaviour), and
   `calculateCombatValue()` zeroes any destroyed ship. But a docked LCV is a full
   ship parked on a rail that relaunches intact — it should keep its value (like a
   carried parasite, not a wreck). Fix: `calculateCombatValue` now detects a
   "docked LCV" (`$removed` + is an LCV/`hangarRequired==='LCVs'` + PRIMARY structure
   NOT damage-destroyed) and skips the destroyed-ship zeroing. A genuinely
   damage-destroyed LCV (primary structure gone) still zeroes. Verified:
   `BALightGunboat` docked → CV 100 (was 0).
2. **No launch option after a successful dock.** Root cause: the test carrier
   (`BAEscortCarrierLCVRails`) has BOTH fighter bays/rails (launchable Starfox +
   shuttles) AND LCV rails. `openHangarLaunch` only opened the LCV dialog when NO
   fighter hangar was launchable, so the fighter dialog opened and the docked LCV
   was invisible — same combining problem as recover. Fix: extracted
   `confirm.appendLcvLaunchSection(container, carrier)` (deferred-commit + 1/1→0/1
   pill strip, mirrors appendLcvRecoverSection) and injected it into
   `hangarLaunchNoSplit` (commit runs in the fighter OK handler). The standalone
   `lcvLaunch` is now a thin wrapper. So a carrier with both shows fighters AND LCVs
   in one launch dialog; `openHangarLaunch` only opens the standalone LCV dialog when
   there is no launchable fighter hangar at all.

### Stage L — LCV dialog CSS alignment (2026-06-04)

LCV dock/recover/launch sections now reuse the fighter dialog's confirm.css classes
so they look identical:
- **Launch** (appendLcvLaunchSection): `hangar-section-name` header + per-rail rows
  using `multi-value-label` > `hangar-craft-name` + `multi-value-max` ("1 docked, max 1")
  and a `0/1` number input styled `multiConfirmInput multi-value-input main-input launchSize`
  (was a bare checkbox + pill strip).
- **Recover** (appendLcvRecoverSection) + the LCV-side `lcvDock`: rows now use the
  fighter recover pattern — `[checkbox] + multi-value-label > hangar-craft-name`
  (was `<label><input></label>`). Recover keeps its live 0/1→1/1 capacity pill strip
  (`hangarCapacityHeader`/`hangar-capacity-pill`, already styled in confirm.css).
- Standalone `lcvRecover`/`lcvLaunch` dialogs now also carry the `hangarRecover` /
  `hangarLaunch` root classes so they pick up the dialog-specific CSS (e.g. the
  launch section-header row indent). All classes verified present in confirm.css.

### Stage L — LCV dialog styling tweaks (2026-06-04, per user)

- "LCV Rails" section header centred (`justify-content:center`) in both the launch
  and recover sections (was right/space-between).
- LCV launch row reverted to a CHECKBOX (each rail holds one LCV — a 0/1 number
  input was overkill) and the redundant "(1 docked, max 1)" note dropped. Label is
  just the LCV name (prefixed "LCV Rail N: " only when the carrier has >1 rail).

### Stage L — LCV deployment-phase deploy-dock (2026-06-04)

LCVs can't share a deploy hex with other ships, so the fighter "Deploy flights in
hangar" flow doesn't apply. Instead an LCV deploy-docks DIRECTLY onto a free LCV
rail: select the LCV, click an LCV-capable carrier (with a free rail), and pick
"DOCK <LCV> TO <CARRIER> (N free rails)" in the SelectFromShips popup.

State model (parallels the fighter deploy-dock):
- `rail.pendingLcvDeployStartOrders : [{shipId}]`  client queue
- `lcv.pendingLcvDeployDock : {carrierId, railId}`  client marker (hides the LCV
  from the board + exempts it from the deploy position/commit gate)

Client:
- `DeploymentDock`: `carrierAcceptsLcvDeployDock` / `freeLcvDeployRailCount` /
  `queueLcvDeployDock` / `unqueueLcvDeployDock` (exported). `lcvRailFreeForDeploy`
  excludes destroyed / occupied / already-queued rails.
- `SelectFromShips.create`: new LCV branch — one eligible carrier auto-queues,
  several open `confirm.lcvDeployDockCarrierPicker`. Button shows free-rail count.
- `pendingLcvDeployDock` added to every `pendingDeployDock` visibility/skip site
  (ships.js isHidden + hex-occupancy, gamedata.js auto-select, PhaseStrategy.js
  clickable-icon filter, DeploymentPhaseStrategy.js validateAllDeployment).
- `DockingCollar.doIndividualNotesTransfer` ships `lcvDeployStarts:[{shipId}]`;
  `refreshHangarTooltip` shows "(Deploying)".

Server:
- `Hangar::doIndividualNotesTransfer` parses `lcvDeployStarts` → public
  `$pendingLcvDeployStartTransfer` (DockingCollar inherits the base method).
- `generateIndividualNotes` calls `HangarOps::processLcvDeployStartTransfer` for an
  LCV rail during Deployment: marks the LCV `removed` + sets the rail's `lcvDocked`;
  the existing `lcvDocked` snapshot tail persists it (no new persistence path).
- `collectQueuedDeployStartFlightIds` now also collects LCV deploy-dock ship ids,
  so `DeploymentGamePhase::validateDeployment` exempts the deploy-docked LCV from
  the "must have a deploy movement" check (it has no board position).
- A later launch follows normal rail-launch rules (the -50 init that turn) — the
  LCV simply "started docked". No special first-launch handling.

All 2 PHP + 8 JS files lint clean; new HangarOps methods resolve at runtime.

### Stage L — deploy-dock click interception + penalty corrections (2026-06-04)

1. **Click interception fix.** Clicking an LCV-capable carrier with an LCV selected
   during Deployment just SELECTED the carrier — the SelectFromShips popup (which
   carries the DOCK-TO button) never opened, because `DeploymentPhaseStrategy.onShipClicked`
   only shows the popup when the selected ship is a mine/flight (line ~175 gate) and an
   LCV is neither. Added an LCV branch BEFORE that gate: when the selected ship is an LCV
   (`hangarRequired==='lcvs'`) and the clicked different ship is a friendly carrier that
   `DeploymentDock.carrierAcceptsLcvDeployDock`, call `showSelectFromShips([ship], payload)`
   directly (bypassing the mine/flight gate AND the deploy-position check — a docking LCV
   needs no board position).
2. **Penalty corrections (user: I had them wrong).** Launch penalty is **-50** (same as a
   launched fighter flight), not -10; per-docked-LCV initiative penalty is **-10**, not -2.
   Fixed in `getIniativeMod` (LCVLaunchedThisTurn -50), `getInitiativebonus` (10×docked),
   the `LCVLaunchedThisTurn` crit description, the rail tooltip text, HangarOps comments,
   and the [[arch_lcv_rails]] / MEMORY.md notes.

### Stage L — penalty model fixes (2026-06-04)

1. **Docked-LCV initiative penalty wasn't applying.** Root cause: `dockedLCVCount`
   excluded the docked LCV via `$lcv->isDestroyed()` — but a docked LCV is `$removed`,
   so isDestroyed() returns true (the same removed→isDestroyed trap that zeroed the
   fleet-list value). The count was always 0 → no penalty. Fixed: count an occupant
   unless its PRIMARY structure is actually destroyed (a genuine wreck), not merely
   removed. Penalty applies at the next initiative roll (`Manager::generateIniative`).
2. **Turn cost / turn delay penalty is a per-turn THRUST surcharge, not a stat change.**
   Per user: the ship's `turncost`/`turndelaycost` STATS must not change; instead each
   docked LCV adds +1 thrust to THIS turn's turn cost and +1 to the turn delay (e.g. a
   turn-cost-1 carrier at speed 5 with 4 docked LCVs pays 5+4=9). Removed the
   `stripForJson` stat bump; now send `ship.dockedLCVs` (count) only. Client
   `movement.js`: new `getDockedLcvTurnSurcharge(ship)` added as a FLAT surcharge to the
   final turn-cost value at every `Math.ceil(speed*baseTurnCost)` site (incl. the
   speed-0 case and the requiredThrust builder) and to turn delay in `calculateTurndelay`
   + `calculateTurndelayAtMove`.
3. **Penalty values corrected** (prior pass): launch -50 (not -10), docked -10/LCV (not -2).

### Stage L — $LCVCarrier flag (perf, 2026-06-04)

Very few ships mount LCV rails, but the per-turn LCV checks (getInitiativebonus
penalty, stripForJson docked count, processLCVCarrierDestruction sweep) ran
`HangarOps::shipHasLCVRail($ship)` — a full system-list scan — for EVERY ship.
Added `public $LCVCarrier = false` on BaseShip, set true in `addSystem()` the moment
a DockingCollar (`$system->isLCVRail`) is mounted (runs at construction AND POST-side
reconstruction, since both go through the ctor → addSystem). The three hot call sites
now gate on the `$LCVCarrier` boolean instead of scanning systems, so the vast
majority of ships skip the LCV path with a single flag check. `shipHasLCVRail` /
`collectLCVRails` remain for the genuine LCV-carrier paths. Server-only flag (client
has its own per-system isLCVRailSystem checks; not serialized).

### Stage L — deployment UX fixes + rail picker + labels (2026-06-05, test pass)

First runtime test of the deployment-phase LCV flow surfaced three UX gaps (all
client-only) plus one server crash, now fixed:

1. **Un-dock a deploy-docked LCV without a page refresh.** The "Deploy Flights in
   Hangar" button → `confirm.hangarDeployDock` handled only fighter flights. Added
   `confirm.appendLcvDeployDockSection(container, carrier)` (mirrors
   `appendLcvLaunchSection`): one pre-checked row per rail holding a pending
   `pendingLcvDeployStartOrders`; unchecking + OK calls
   `DeploymentDock.unqueueLcvDeployDock`. Folded into `hangarDeployDock` so the
   dialog stays open for LCV-only carriers (empty-state now also checks
   `lcvDeployRailCount`; fighter intro/capacity rows gated on `pending.length>0`).
   `DeploymentDock.shipHasOpenableDockDialog` extended (`shipHasDeployDockedLcv`) so
   a carrier with ONLY LCV rails still surfaces the button.
   `unqueueLcvDeployDock` now SNAPS the released LCV to the carrier's current hex
   (was: no re-deploy), mirroring fighter `unqueueDeployStartDock`.

2. **Same-hex deploy block relaxed for LCVs** (smallest vessels, must co-locate with
   the carrier). `selIsLcvUnit` exemption added to the isBlocked check in
   `DeploymentPhaseStrategy.onHexClicked`, `SelectFromShips.create`, AND the
   `onShipClicked` placed-ship popup gate (so clicking an occupied hex with an LCV
   selected surfaces SelectFromShips with DEPLOY + cyan DOCK). The SelectFromShips
   DEPLOY button now self-gates on new `window.validateDeploymentPositionForShip`
   (uses `window._deploymentSprites`) because the LCV dock shortcut bypasses upstream
   position validation.

3. **Rail choice (Firing + Deployment).** Dock dialogs auto-picked `rails[0]`. Now one
   radio row per FREE rail (mutually exclusive): `confirm.lcvDock` (firing) flattens
   `findEligibleLCVRailsForDock` to per-rail; `confirm.lcvDeployDockCarrierPicker`
   (deployment) enumerates `DeploymentDock.freeLcvDeployRails`; `queueLcvDeployDock`
   got an optional `targetRail`. SelectFromShips auto-queues only when exactly ONE
   free rail in the hex; else opens the picker. **Rail labels are now LOCATION-keyed**
   via `window.lcvRailLabel(carrier, rail)` ("LCV Rail Forward 1", "LCV Rail Port",
   "LCV Rail Starboard") — mirrors fighter `hangarLabelFor` location grouping
   (0 Main/1 Forward/2 Aft/3,31,32 Port/4,41,42 Starboard) with LCV terms; trailing N
   only when >1 rail shares a location. Used by every LCV dialog (dock/picker/un-dock/
   launch/recover). Launch section keys off the FULL rail list so its label stays
   correct despite `rails` being only the launchable subset.

4. **Server crash on destroyed occupied rail (PHP 8.2 dynamic-property deprecation).**
   `HangarOps::loadOrRollLCVFrag` set `$lcv->lcvFragRolledTurn/Value` on the LCV ship,
   but those weren't declared → the registered error handler promoted the deprecation
   to an `ErrorException`, aborting `onLCVRailDestroyed` mid-crit-resolution (trace:
   FireGamePhase→Criticals→Hangar::criticalPhaseEffects). Fixed by declaring
   `public $lcvFragRolledTurn = null; public $lcvFragRolledValue = 0;` on BaseShip
   (next to `$LCVCarrier`). Same root cause / pattern as FighterRail's
   `railCritLoadedTurn/Value`, which were already declared public — the general rule
   (see [[arch_fighter_rails]] "Protected $structureSystem"): external static
   coordinators like HangarOps must only touch DECLARED properties.

### Stage L — firing-phase recover/launch dialog bugs (2026-06-05, test pass)

Three carrier-side dialog bugs (all client-only, confirm.js + shipTooltipFireMenu.js):

1. **Recover capacity pills mis-attributed a per-rail dock.** Docking an LCV to a
   specific rail (e.g. Starboard) via the LCV's own dock menu, then opening the
   carrier's recover dialog, showed that LCV occupying "Forward 1" — the recover
   dialog re-assigned it greedily. `appendLcvRecoverSection` now builds
   `pinnedRailByLcv` (lcvId→railId, from each rail's `pendingLcvDockOrders`): a
   pinned LCV is excluded from `baseFreeRailIds`, the pill strip renders it on ITS
   rail (`pinnedFill`, gated on the checkbox), and `commit` leaves a checked+pinned
   LCV's order on its rail (only un-pinned checked LCVs are greedily assigned; only
   unchecked OR un-pinned LCVs get `clearQueuedLcvDock`). Removed the now-dead
   `railBaseOccupied` helper (its logic is inlined into the `baseFreeRailIds` filter).

2. **Destroyed rails listed as 1/1.** The pill loop now renders `railDestroyed(rail)`
   rails as "destroyed" (red), and destroyed rails are excluded from
   `baseFreeRailIds` so nothing is ever assigned to them.

3. **Launch menu listed a queued-but-not-docked LCV.** `lcvRailLaunchable` treated a
   rail with a `pendingLcvDockOrders` as launchable — but you can't dock AND launch
   in the same turn (server `canLCVLaunch` reads the COMMITTED `lcvDocked` link, set
   only at end-of-turn dock resolution / reload). Fixed to require `lcvDocked` set
   AND no pending dock order. Known pre-existing (NOT fixed, out of scope): a queued
   LCV *launch* can't be un-declared from the launch dialog because `lcvRailLaunchable`
   filters out rails with `pendingLcvLaunchOrders` (same as the original shape).

### Per-hangar default-shuttle opt-out — `$excludeFromDefaultShuttles` (2026-06-06)

**Fringe case.** ScoravarefittedAM has two location-0 (primary) hangars: a 26-box
medium-fighter bay and a 6-box heavy/normal-fighter bay. `fighters = medium 24 +
normal 6 = 30` declared against 32 boxes → **2 leftover default shuttles**. The
distribution logic (`distributionHangars` + `pickHangarForShuttle` least-used) split
those 2 shuttles 1+1 across the two primary bays, so a stray shuttle in the 6-box bay
blocked a full 6-flight of heavy fighters from docking.

**Fix (NOT a new exclusion class).** New Hangar constructor flag
`$excludeFromDefaultShuttles` (7th positional arg, after `$spawnableClasses`) +
public property of the same name. Unlike catapult/rail exclusion — which drops boxes
from the pool **wholesale** so the leftover COUNT shrinks — a flagged bay's boxes
**still count toward total capacity**, so `getDefaultShuttles` / `getTotalHangarCapacity`
are untouched and the ship still gets its 2 shuttles. Only the *placement* steers
away: the shuttles pile into the other hangar(s), leaving the flagged bay free.

- Server: `HangarOps::excludesDefaultShuttles($hangar)` (catapult/rail short-circuit
  to false, then reads the flag). `distributionHangars` drops flagged bays from the
  eligible set FIRST (before primary/shuttle-only narrowing), guarded so an
  all-flagged ship still places shuttles somewhere. `pickHangarForShuttle`'s overflow
  fallback skips flagged bays too (so they get nothing even once eligible bays fill).
- Client mirror (`systems.js`): `excludesDefaultShuttles` helper +
  `getDefaultShuttleCompositionForHangar` gains an `anyEligible`/`eligible()` filter
  feeding `hasPrimary`/`inSet`/`per[].excluded`, matching the server (incl. the
  all-flagged guard and the overflow skip).
- Serialized via Hangar `stripForJson` (`excludeFromDefaultShuttles` bool) so the
  client lobby tooltip matches the in-game initial population.
- Applied to ScoravarefittedAM's 6-box bay: `new Hangar(2, 6, 6, 0, 'fighters',
  array(), true)`. Result: 26-box bay holds 24 medium + 2 shuttles (full); 6-box bay
  holds a clean 6 heavy fighters.

Not yet Docker-tested (no local PHP; user tests via Docker).

---

## Stage S (Shadow Integrated Fighters / "Fighter Bomb") — PLAN (drafted 2026-06-10)

B5W "integrated fighters" + the "Fighter Bomb" delivery: certain advanced races
(Shadows) don't store fighters in a hangar — they form them out of their own
**Structure**. A formed fighter marks one structure box; recovering it erases the
mark; losing the box cuts the fighter off (can't land); losing an un-cut-off fighter
destroys the box (can kill the carrier). This layers on the existing `Hangar` Ops
pipeline via a new `ShadowHangar extends Hangar` subclass + a reworked `SHAD_FTRL`
enhancement that **buys** integrated fighters (priced as fighters) and **populates
`hangarUsage` at game start** — so they start docked in the ShadowHangar and launch
through the ordinary HangarOps launch path.

> Test container: gameID 3730 / a Shadow ship with a `ShadowHangar` (e.g. a
> `ShadowCarrier` variant). Gate launching/coupling to safeGameID until S-final.

### S0. Guiding constraints (Stage-S-specific)
- **Don't touch live in-flight Shadow games.** Shadow fighters today auto-deploy to
  space as separate `ShadowMediumFighterFlight` ships (lobby purchase + SHAD_CTRL
  "uncontrolled" OR SHAD_FTRL count). The integrated path is OPT-IN per ship via the
  presence of a `ShadowHangar` on the hull. A Shadow ship with a plain `Hangar`
  behaves exactly as today.
- **Positional system-id trap.** `ShadowHangar` must be a NEW phpclass, and on the
  existing shadow hulls it REPLACES the existing `new Hangar(...)` at the SAME
  construction position (same id) — swapping the class without moving the call keeps
  ids stable. Do NOT reorder. (See [[arch_positional_system_id_trap]].)
- **Structure coupling is replay-deterministic.** Box↔fighter marks must persist as
  notes/DamageEntries, never as live-only state — the cut-off/destroyed outcome has
  to survive a reload mid-game (same discipline as Stage 18 escape + rail crits).
- **No bundle commits. Don't edit autoload (user adds `shadowhangar` entry).**

### S1. Purchase model — rework `SHAD_FTRL` to BUY integrated fighters
Today `SHAD_FTRL` ("Spawn a Medium Fighter") is **free** (`$enhPrice = 0`), limited to
hangar capacity, and only reduces primary Structure `maxhealth` by `enhCount`
(Enhancements.php:692, :2330). The rules say integrated fighters are NOT free — they
cost a fighter's price. Rework:
- **Price = one ShadowMediumFighterFlight's per-craft cost.** `ShadowMediumFighterFlight->pointCost = 150*6` → **150 CP per fighter** (the flight is 6).
  Set `$enhPrice = 150`, `$enhPriceStep = 150`, `$enhLimit = ceil($capacity)` (unchanged
  cap = hangar icon number). Flip the trailing `true` ("not an enhancement") handling
  as needed so it's a real costed option. **Confirm 150 with the user** before coding —
  it's the one externally-visible number.
- **Keep the `-1 Structure maxhealth` per count? NO — reverse it.** Under integrated
  rules a formed fighter MARKS (occupies) a box, it doesn't permanently shrink the
  structure. The box is freed on recovery. So the structure penalty moves from a
  static `maxhealth -=` to the dynamic mark/occupancy model (S3). Remove the
  `maxhealth -= $enhCount` mutation (Enhancements.php:2330) for ShadowHangar ships;
  the count instead drives initial `hangarUsage` population (S2). **Legacy Shadow
  ships with a plain `Hangar` keep the old behaviour** — branch on whether the ship
  has a ShadowHangar.

### S2. Initial population — `enhCount` → `hangarUsage`
`HangarOps::populateInitialHangarUsage` currently auto-fills shuttles + leftover
capacity but explicitly does NOT auto-fill combat-fighter declarations (they
auto-deploy to space). For a `ShadowHangar`, integrated fighters should start
**docked in the ShadowHangar** (formed-but-held), so:
- Read the carrier's `SHAD_FTRL` enhCount (same direct-read pattern as HANG_BP at
  HangarOps.php:121-124, since `setEnhancements` runs after note hydration).
- Push `enhCount` `ShadowMediumFighterFlight` craft into the ShadowHangar's
  `hangarUsage` (as `flightSize`-1 entries or one sized entry — match how launch
  expects to draw them; single-craft entries are simplest for the per-fighter
  structure mark in S3). hangarType = the ShadowHangar's category (likely `'medium'`).
- Each formed/held fighter marks one structure box (S3). Cap at `ceil($capacity)`
  (the hangar icon max) — never exceed it even if enhCount somehow did.
- This REPLACES the auto-shuttle fill for the ShadowHangar (Shadows don't carry
  shuttles in it). Partition ShadowHangars out of the shuttle pool exactly like
  catapults/rails (`isCatapult`/`isRail` precedent at HangarOps.php:54-58) via a new
  `isShadowHangar` discriminator.

### S3. Structure coupling (FULL — confirmed with user)
The novel mechanic. A per-fighter ↔ per-structure-box binding:
- **Form / mark:** when an integrated fighter exists (held OR launched), one
  structure box is "marked" (occupied). Held fighters mark on initial population;
  launched fighters keep their mark while in space.
- **Box destroyed → fighter cut off:** if a marked structure box is destroyed in
  combat, the bound fighter is CUT OFF — it can no longer land/reabsorb (survives &
  fights, dies after scenario). Implement as a `ShadowFighterCutOff` flag/crit on the
  flight (parallels `DisengagedFighter`/`SplitLaunchedFighter`), set from the
  structure-damage path. A cut-off fighter is excluded from landing eligibility.
- **Fighter lost (not cut off) → box destroyed:** if an un-cut-off integrated fighter
  is destroyed, its bound structure box is marked destroyed (a `DamageEntry` on the
  carrier's Structure). **If it was the last box → carrier destroyed.** This is the
  rule that lets you kill a carrier by picking off its wounded fighters — so it MUST
  route through the normal structure-destruction → ship-destruction check.
- **Persistence:** the box↔fighter binding is a note on the ShadowHangar (e.g.
  `shadowFighterBinding` = list of `{flightId/fighterId, structBoxIndex, cutOff}`),
  snapshot-persisted like `hangarUsage`. Box destruction is a `DamageEntry` (replay-safe).
  Cut-off is a flight crit (replay-safe). NO live-only state.
- **Bookkeeping owner:** the PRIMARY ShadowHangar holds the binding list (same
  "primary hangar carries pool state" pattern as `reloadPoolSpent`).
- **Integrated fighters can't drop out / phase / jump** — already enforced on
  `ShadowMediumFighterFlight` (`critRollMod = -100`). They CAN flee/disengage to be
  recovered (so disengage ≠ cut-off; only box-destruction cuts off).

### S4. Launch — the "Fighter Bomb" + ordinary launch
Two launch flavours, both through `HangarOps::performLaunch`:
- **Ordinary integrated launch:** same as a normal hangar launch from the
  ShadowHangar (held fighter → space at carrier heading/speed, same-turn no-act via
  the existing `LaunchedThisTurn` crit). The structure mark persists across launch.
- **Fighter Bomb (B5W) — STRUCTURE DECIDED 2026-06-10 (user): a SEPARATE
  `ShadowFighterBomb extends Weapon` system, NOT bolted onto the ShadowHangar.**
  Rationale (user's finding): the Bomb is genuinely weapon-shaped — it fires in the
  Weapons Fire step, has an ARC (startArc/endArc, e.g. 300→60 for the test hull), a
  RANGE (≤10 hexes), no miss roll, and must be **player-selectable to target a hex in
  the Firing Phase**. `Hangar` has none of that (no arc/range, not fire-selectable),
  and `Weapon`/`Hangar` are SIBLING classes (both extend ShipSystem) so one system
  can't be both. So the Bomb is its OWN system mounted ALONGSIDE the ShadowHangar.
  - **Structural precedent: `BallisticMineLauncher extends Weapon`** ([missile.php:2301](source/server/model/weapons/missile.php#L2301))
    — a Weapon that SPAWNS units (loitering/captor mines) at a target location during
    the Fire step via `createLoiteringMine` (Manager::insertSingleShip + insertSingleMovement
    + IndividualNote). The Bomb mirrors this exactly, spawning `ShadowMediumFighterFlight`s
    at the target hex instead of mines. (Bomb is `ballistic`? — fires in the ballistic/
    weapons step; confirm which gamephase gate to use, mines use phase 1.)
  - **Shared pool:** the Bomb and the ShadowHangar draw from the SAME integrated-fighter
    maximum ("cannot operate more than the listed maximum"). The Bomb must read/decrement
    the ShadowHangar's `hangarUsage` (the held integrated-fighter count) so launching 3
    via the bay and 3 via the bomb can't exceed the cap. Cross-system coordination:
    `ShadowFighterBomb` locates the ship's ShadowHangar (HangarOps::collectShadowHangars)
    and consumes held entries from it on fire.
  - **Spawn geometry:** N fighters (≤ remaining pool) burst at the TARGET hex, all sharing
    the carrier's heading+speed, can't act until next turn (same `LaunchedThisTurn`/deploy
    pattern as an ordinary launch, but spawnPos = target hex not carrier hex).
  - **Client:** the Bomb appears as a fireable weapon (arc fan, range, hex-target picker)
    — reuse the standard weapon firing/targeting flow, NOT the hangar launch dialog.
  - **STILL DEFERRED to S-f** (after S-d/S-e). Build the integrated-fighter core +
    structure coupling + diffuser reabsorption first; add the Bomb weapon last.

### S5. Recovery — land then reabsorb (FULL — confirmed with user)
"Integrated hangars cannot recover fighters" = they don't use the hangar RELOAD/stash
semantics; recovery is the normal fighter-landing procedure, then **reabsorption**:
- Land via the standard HangarOps landing path INTO the ShadowHangar (it's still a
  Hangar, so `eligibleHangarsForLanding` finds it). A CUT-OFF fighter is NOT eligible
  to land (S3).
- On land, immediately **reabsorb**: erase the structure mark (free/heal the box —
  but only a box that was MARKED-occupied, never repair a combat-destroyed box),
  **repair the fighter fully** (all damage cleared — it re-forms next turn fresh),
  and **absorb the fighter's diffuser energy into one carrier `EnergyDiffuser`** of
  choice; **excess penetrates as damage to a random system, ignoring armor**. The
  diffuser-transfer + excess-penetration is the fiddly bit — reuse the existing
  diffuser/`getSystemProtectingFromDamage` plumbing and the "ignore armor" damage
  path. Can be reabsorbed → relaunched as soon as the following turn.
- **Recommend:** land+free-box+repair in S-Phase-1; the diffuser-energy transfer +
  random-system penetration as a small S-Phase-1.5 (it's self-contained).

### S6. `ShadowHangar extends Hangar` — the class
Mirror the Catapult/FighterRail/DockingCollar subclass pattern (baseSystems.php:3754+):
- `public $isShadowHangar = true;` — single discriminator, parallel to
  `$isCatapult`/`$isRail`/`$isLCVRail`. HangarOps call sites branch on the flag.
- **Crit-immune:** "Integrated fighter hangars do not suffer from critical hits."
  Override `criticalPhaseEffects` (and/or the crit-roll entry) so the ShadowHangar
  takes box damage but never rolls/applies a critical. (Confirm exactly where crits
  are generated for a Hangar — likely `setSystemDataArray`/the generic crit roll — and
  short-circuit for `isShadowHangar`.)
- ctor → `parent::__construct($armour, $maxhealth, $output, $direction, 'medium', ...)`
  (or whatever category the ship's `$fighters` declares).
- Excluded from the default-shuttle pool (S2). Included in `collectHangars`
  (`instanceof Hangar`) so launch/land/escape pipelines pick it up automatically.
- Client mirror in `baseSystems.js` (`ShadowHangar extends Hangar`, `isShadowHangar`),
  `stripForJson` ships `isShadowHangar` + the binding state for tooltip/icon.

### S7. Enhancement bookkeeping (`addShipEnhancementsForJSON` etc.)
- `SHAD_FTRL` already has a `stripForJson`-side case (Enhancements.php:2776) restoring
  `Structure->maxhealth` — that case becomes a no-op / is removed for ShadowHangar
  ships once the static maxhealth mutation is gone (S1). Audit BOTH the
  `setEnhancementsShip` apply (:2330) AND the strip restore (:2776) together.
- Existing Shadow fighter flights keep SHAD_CTRL ("uncontrolled", -2 OB / -3(15) ini,
  Enhancements.php:1819) as the scenario-only un-purchased path — unchanged.

### S8. Stage breakdown (suggested commit cadence)
- **S-a — ✓ IMPLEMENTED 2026-06-10 (not yet Docker-tested).** `ShadowHangar extends
  Hangar` (baseSystems.php, after FighterRail): `$isShadowHangar = true` discriminator,
  crit-immunity via `testCritical()` override → returns `$crits` untouched (no d20, no
  addCritical), `setSystemDataWindow` appends a crit-immune Special line. **KEY DIVERGENCE
  from Catapult/FighterRail/DockingCollar: ShadowHangar does NOT override `$name` — it
  keeps the base `'hangar'`** so it flows through the standard fighter launch/dock/recover
  UI (every client+server helper gates on `name === 'hangar'`). Consequence: the client
  factory instantiates a PLAIN `Hangar` (`new window['Hangar']`, keyed off `$name`), so
  **there is NO client subclass** — `isShadowHangar` round-trips as a DATA property via
  `Hangar::stripForJson` (added alongside isCatapult/isRail/isLCVRail at baseSystems.php
  ~3661) and is read off the live Hangar instance. Shuttle-pool exclusions added in
  HangarOps (`populateInitialHangarUsage` ~54, capacity-sum ~1284, `inferHangarType` guard
  ~1221) + client mirror (systems.js `getDefaultShuttleCompositionForHangar` ~651).
  ShadowCruiser swapped `new Hangar(5,6,6)` → `new ShadowHangar(5,6,6)` IN PLACE (same
  ctor position = same system id; positional-id-trap-safe). **No behaviour change beyond
  crit-immunity** — ShadowCruiser's bay already auto-filled 0 shuttles (6 declared = 6
  capacity → 0 leftover), so the shuttle-pool exclusion is a no-op there.
  **Autoload (user must add):** `'shadowhangar' => '/server/model/systems/baseSystems.php',`.
  Verify: load a ShadowCruiser game, confirm the Hangar renders/launches as before + the
  "does not suffer critical hits" tooltip line, and that a Hangar crit roll never lands on it.
- **S-b — ✓ IMPLEMENTED 2026-06-10 (not yet Docker-tested).** SHAD_FTRL reworked
  (Enhancements.php): the lobby-option branch now splits on `HangarOps::shipHasShadowHangar($ship)`
  — ShadowHangar ships get a COSTED "Integrated Fighter" option (price 150, step 150,
  the `false` "is-enhancement" flag, limit `ceil($capacity)`); legacy plain-Hangar
  Shadow ships keep the original FREE "Spawn a Medium Fighter" (price 0, `true`). The
  apply-side (`setEnhancementsShip`) `Structure->maxhealth -= $enhCount` is now SKIPPED
  for ShadowHangar ships (guarded by the same helper) — integrated fighters mark boxes
  dynamically (S-d), they don't statically shrink Structure. The strip-restore
  (`addShipEnhancementsForJSON` ~2798) is left as-is: it copies the live (unreduced)
  maxhealth → harmless no-op for the integrated case. New helpers
  `HangarOps::collectShadowHangars` / `shipHasShadowHangar`. Initial population
  (`populateInitialHangarUsage`): ShadowHangars are partitioned into `$shadowHangars`
  (out of the shuttle pool) and filled in a new **Step 3** with `enhCount` flightSize-1
  `ShadowMediumFighterFlight` entries (name 'Integrated Fighter'), enhCount read off
  `$ship->enhancementOptions[..][2]` (same load-time pattern as HANG_BP, since
  `setEnhancements` runs after), capped at bay box capacity (`maxhealth - occupiedBoxes`).
  `ShadowMediumFighterFlight` is unitSize 1 → 1 box/fighter, so 6 fill a 6-box bay.
  **KNOWN GAP for verification / a later decision:** `gamelobby.js checkChoices` does
  NOT reference SHAD_FTRL, so the integrated-fighter purchase is accounted purely as a
  priced enhancement, INDEPENDENT of the `$ship->fighters` slot pool. A player could
  therefore buy 6 integrated fighters AND still deploy 6 separate ShadowMediumFighterFlights
  to space — a double-spend vs. the "can't operate more than the listed maximum" rule.
  Enforcing the shared cap in the lobby is its own piece of work (deferred; flag to user).
  Verify: integrated fighters start DOCKED in the ShadowHangar (capacity reads N/6),
  lobby charges 150 each, Structure is NOT shaved, legacy Shadow hulls unchanged.
- **S-c — ✓ CLEARED 2026-06-10 (Docker-verified: launch, damage, land, relaunch-undamaged + destroyed-craft count all confirmed by user).** Launch + land-and-reabsorb
  (S4 first half + S5 first half), PLUS the lobby fighter-allowance accounting the user
  requested. **Launch needs NO new code** — a held integrated-fighter entry launches via
  the existing `performLaunch` new-spawn path (ShadowHangar keeps `$name='hangar'`, is in
  collectHangars, and `hangarAcceptsCategory` accepts 'medium' into its universal
  'fighters' bay because ShadowCruiser declares `normal:6`). **Land-and-reabsorb**
  (`performLand` ShadowHangar branch): an integrated fighter is always a single craft
  (flightSize-1) so a ShadowHangar land is always a FULL dock — instead of the
  dockedFlightId/resurrect link (which would bring the SAME damaged flight back), we
  `unset($entry['dockedFlightId'])` and store a PRISTINE held entry (phpclass + name
  'Integrated Fighter' + flightSize). The damaged flight row stays `$removed` and is never
  resurrected; the next launch spawns a brand-new undamaged fighter. `serviceDockedFlights`
  skips it cleanly (it `continue`s on entries with no dockedFlightId), so no ammo-reload
  weirdness. (S-e adds diffuser-energy reabsorption + excess penetration; S-d adds the
  per-fighter structure-box binding/freeing.)
  **Launch-class fix (2026-06-10, post-first-test):** first launch attempt spawned the
  WRONG class ("tried to launch a ship") — the ShadowHangar's `spawnableClasses` carried
  only the generic Shuttle/MinesweepingShuttle defaults, so `ShadowMediumFighterFlight`'s
  blueprint was never preloaded by game.php (which iterates each system's `spawnableClasses`
  into `window.staticShips`), and the client couldn't resolve the spawned flight's type.
  Fix: the `ShadowHangar` ctor now passes `array('ShadowMediumFighterFlight')` as
  `$spawnableClasses` UNCONDITIONALLY (a ShadowHangar only ever forms that one flight class;
  the base ctor merges it with the shuttle defaults). No ship-file change needed. This also
  covers the future Fighter Bomb (S-f), which launches the same flight class.
  **Reabsorption fix (2026-06-10, post-test ×2): relaunched fighters kept their damage.**
  Two wrong guesses before the real cause: (1) put reabsorption in `performLand` — but the
  firing-phase LANDING path is `performWholeFlightDock` (Stage-21 coalescer), not performLand.
  (2) Flagged the dock entry `reabsorbed` but kept its `dockedFlightId` and guarded
  `resurrectDockedFlight`/`consumeStashesForLaunch` — but the firing-phase RELAUNCH path is
  `processWholeFlightLaunches`→`launchWholeFlight`, which branches on the LAUNCH ORDER's
  `dockedFlightId` (`$dfid>0` → docked-resurrect, `$dfid<=0` → `launchAnonymousStash`
  fresh-spawn) and never touches `performLaunch`/`resurrectDockedFlight`. Because the docked
  entry still had a `dockedFlightId`, the client order carried it, so launchWholeFlight's
  FULL branch resurrected the SAME damaged flight (`$docked->removed=false`). **Final fix:**
  the ShadowHangar dock branch in `performWholeFlightDock` (and `performLand`) now stores a
  PRISTINE ANONYMOUS entry — `unset(dockedFlightId/occupancy/fragment/boxesPerCraft)`, set
  phpclass/name/flightSize/hangarType — IDENTICAL in shape to the S-b initial-population
  entries, so relaunch flows through `launchAnonymousStash`→fresh-spawn (the path the
  initial fighters already launched through fine). The old removed flight is retired in place
  via `destroyAllFighters` (combatValue→0, fleet-list hides it, no ghost). The earlier
  `reabsorbed`-flag guards in resurrectDockedFlight/consumeStashesForLaunch were reverted
  (no longer needed). (Damage-to-carrier transfer is S-e, not yet done.)
  **Destroyed-craft correctness:** `$entry['flightSize'] = $count`, and `performWholeFlightDock`
  clamps `$count` to `countActiveCraft` (non-destroyed only) — so a fighter killed in combat is
  NOT stored and never relaunches (a 6-flight that lost 1 → docks 5 → relaunches 5, not 6). The
  retire step destroys ONLY the docked unit (`$dockedUnit` = the whole flight on a full dock, or
  just the docked FRAGMENT on a partial dock) — the N-K remnant left in space is untouched. The
  first cut wrongly called `destroyAllFighters($flight)` in the partial case, which would have
  killed the flying remnant; fixed by tracking `$dockedUnit`.
  **Lobby accounting (gamelobby.js checkChoices — closes the S-b double-spend gap):**
  (1) integrated fighters now CONSUME the fleet fighter allowance — the carrier `if`-branch
  adds the SHAD_FTRL count to `totalFtrM` (one medium slot each; pools are aggregated in
  the totalFtrPresent vs totalHangarAvailable check, so charging medium is exact even though
  the hull declares `normal`). (2) Separate Shadow fighter flights (scenario-only after this
  patch) are EXEMPTED from the allowance tally via an `isShadowFighterFlight`
  (`faction == "Shadow Association"`) guard on the size-translation block — `totalShips++`
  still counts them as a unit. (3) **SHAD_CTRL forced on** — its lobby-option default
  `current` flipped 0→1 (Enhancements.php ~1566) so separate Shadow fighters are ALWAYS
  Uncontrolled (no longer optional); limit stays 1. NB user corrected S-b `enhPriceStep`
  0 (flat 150/fighter, not +150 cumulative). Verify: launch an integrated fighter, take
  damage, land it, relaunch next turn UNDAMAGED; lobby caps integrated+separate at the hull
  max; separate Shadow fighters cost 0 allowance + show Uncontrolled by default.
- **S-d — ✓ COMPLETE & Docker-verified 2026-06-10 (user confirmed: cut-off, structure-loss,
  per-fighter all working).** Count-based structure↔fighter coupling. FV reality forced
  reinterpreting S3's per-fighter↔per-box binding: Structure is ONE box pool (maxhealth +
  accumulated damage, no addressable boxes) and integrated fighters spawn FRESH each launch
  (no stable per-fighter id). Invariant: **carrier remaining-structure ≥ attached count**.

  **AS-BUILT (the authoritative description — supersedes the original design sketch below):**
  - **State** lives on the PRIMARY ShadowHangar (baseSystems.php), persisted via the
    `shadowIntegratedState` note (loaded in `onIndividualNotesLoaded`, saved in
    `generateIndividualNotes` OUTSIDE the `!$destroyed` guard so terminal state records):
    `{attached, cutOff, launched:{flightId=>baselineAttached}, structLost}`.
    - `shadowAttached` = integrated fighters still TETHERED (held in bay + launched-and-alive-and-
      not-cut-off). `shadowCutOff` = severed (fight on, can't land). Seeded in
      `populateInitialHangarUsage` (attached = placed integrated fighters; persists at game start).
    - `shadowLaunched` is a MAP `{flightId => last-known ATTACHED-fighter count}` — the carrier's
      back-link to its own launched fighters (NO flight-side stamp/note needed). Per-flight
      ATTACHED count (not total alive) so combat deaths, landings, and cut-offs are distinguished.
    - `shadowStructLost` = cumulative structure boxes permanently destroyed by fighter deaths.
  - **Engine `HangarOps::syncIntegratedStructureCoupling($ship,$gamedata)`** — runs once/carrier
    from `onHangarCriticalPhase` (gated `primaryShadowHangar === $hangar` + `isFlowEnabled`, which
    is currently always-true). Per turn:
    1. Walk `shadowLaunched`. For each flight: `deaths = baselineAttached − countAttachedFightersInFlight`
       (combat deaths only — landings decremented the baseline at dock time; new cut-offs apply
       AFTER this loop, prior cut-offs were already removed from the baseline). Sum `lostAttached`.
       Refresh the baseline to the current attached count; collect `$attachedFlights` (flights with
       ≥1 attached craft) for Direction 1. Prune fully-gone flights.
    2. **Direction 2 (runs FIRST so a death can cascade into a same-turn cut-off):** each lost
       attached fighter PERMANENTLY destroys 1 structure box = **reduce `Structure->maxhealth` by 1
       ONLY** (NOT a damage DamageEntry — that double-counts, since remaining = maxhealth − damage;
       and SelfRepair heals damage but can't raise maxhealth, so the box stays gone). Track in
       `shadowStructLost`; `attached -= lostAttached`. **Carrier death:** `Structure::isDestroyed`
       fires only on a `destroyed=true` entry (NOT remaining≤0), so when the loss drives
       `getRemainingHealth() ≤ 0` we write ONE `destroyed=true` DamageEntry to kill the carrier via
       the normal fold.
    3. Recompute `structRemaining`. **Direction 1:** if `structRemaining < attached`, cut off the
       difference PER-FIGHTER (a flight of N can have any number cut off). Order (user): launched-
       in-space first — `applyCutOffToFighters($flight,$count)` stamps a `ShadowFighterCutOff` crit
       on up to $count individual living non-cut-off fighters (each its own crit + CUT OFF badge),
       dropping each from the flight's baseline. Then held bay fighters via
       `removeHeldIntegratedFighters` (no crit — nothing flying, just removed from hangarUsage).
       `attached -= n; cutOff += n`.
  - **Launch/land bookkeeping:** `registerLaunchedIntegratedFlight` (in `launchAnonymousStash` when
    `firstBay->isShadowHangar`) seeds the baseline at launch. `decrementLaunchedIntegratedBaseline`
    (in BOTH reabsorption branches) drops the landed count so a dock isn't read as a death.
  - **maxhealth replay-safety:** maxhealth is a blueprint value rebuilt fresh each load, so
    `shadowStructLost` is re-applied to `Structure->maxhealth` ONCE per load — AFTER the note loop
    in `Hangar::onIndividualNotesLoaded` (the loop visits every per-turn note; a per-note
    subtraction would over-reduce), gated to the primary ShadowHangar. AND `Structure::stripForJson`
    now SENDS the live maxhealth (base stripForJson omits it → the client read it from the static
    blueprint → desync). Notes load turn-bounded (`turn <= ?`), so a replay scrub to turn T loads
    structLost-as-of-T and ships that turn's maxhealth. Replay-consistent end to end.
  - **Crit:** new `ShadowFighterCutOff` (cricialClasses.php, "CUT OFF", permanent — `repairPriority 0`,
    no turnend; lives on the FIGHTER, SelfRepair is a carrier system so can't reach it). Added to the
    `copyFighterStateToTarget` skip-list (source-only). **Client:** `criticals.js isCutOffFighter`
    + `FighterIcon.js` red **CUT OFF** badge on a LIVING fighter (per-fighter, so N fighters show N
    badges; a dead cut-off fighter shows DESTROYED instead).
  - **Landing gate:** `canShipReceive` rejects any flight with a `ShadowFighterCutOff` crit.
  - **Self-repair raising structure does NOT un-cut-off:** cut-off is permanent (crit never clears);
    Direction 1 has no re-attach branch; attached is monotonic-decreasing, cutOff monotonic-
    increasing — rising structure just means fewer FUTURE cut-offs. (User verified the concern.)
  - **Autoload (USER must add):** `'shadowfightercutoff' => '/server/model/cricialClasses.php'`.

  **KNOWN LIMITATIONS / future refinements (none blocking S-e):**
  - **Whole-flight landing rejection:** `canShipReceive` rejects the ENTIRE flight if ANY fighter
    in it is cut off — so a cut-off fighter's still-attached flight-mates can't land while it
    remains. Fine for the single-flight test hull; true partial-flight landing (land only the
    attached fighters) is a future refinement.
  - **Recover-dialog client exclusion deferred:** a cut-off flight may still appear selectable in
    the recover dialog; the server rejects the land with a reason note (authoritative). Low priority.
  - **Stale pre-S-d games** need their `shadowIntegratedState` note manually seeded (population is
    gated after game start); fresh games seed automatically.

  **Files touched (S-a→S-d):** server `Enhancements.php`, `baseSystems.php` (ShadowHangar class +
  state fields + persistence + `Structure::stripForJson`), `HangarOps.php` (all the helpers + the
  engine + seeding + launch/land bookkeeping), `cricialClasses.php` (ShadowFighterCutOff),
  `shadowCruiser.php` (Hangar→ShadowHangar). Client: `criticals.js`, `FighterIcon.js`, `gamelobby.js`,
  `systems.js`, `baseSystems.js`. (Bundles + autoload.php are user-side; not committed by us.)
- **S-e — ✓ CLEARED 2026-06-11 (Docker-verified by user: single-tendril absorb, DOCKED label, maxhealth-on-death, AND penetration-to-random-system all confirmed working after the 4 post-test fixes below). Diffuser-energy reabsorption + excess→random-system penetration.**
  SECOND HALF of land-and-reabsorb (S-c did the first half: free box + repair fighter). Rule (S5):
  "Any energy contained in [the landing fighter's] diffusers must be absorbed into a single diffuser
  of the carrier's choice, with any excess penetrating as damage to a random system, ignoring armor."

  **AS-BUILT:** one private helper `HangarOps::applyDiffuserReabsorption($carrier, $dockedUnit, $gamedata)`,
  called from BOTH ShadowHangar reabsorption branches — `performWholeFlightDock` (live landing) and
  `performLand` (jumping-carrier) — **BEFORE** that branch's `destroyAllFighters($dockedUnit/$flight)`,
  so the landed craft's damage + tendril energy are still readable. Gated by the existing
  `isShadowHangar` branch (no extra scope guard needed). Steps:
  1. **Read incoming energy = BOTH halves of the rule** ("any damage the fighter had when it landed,
     AND any energy contained in its diffusers"): per landed Fighter, sum `getTotalDamage()` (the
     fighter's accumulated armour/structure damage points) PLUS `getUsedCapacity()` over each of its
     `DiffuserTendrilFtr`s (the diffuser charge). Distinct, additive sources — fighter damage is in
     its DamageEntry stack, tendril energy is in `absorb` notes — so no double-count. (`$dockedUnit->systems`
     = the Fighters; each `$fighter->systems` holds its 2 tendrils.) Destroyed fighters skipped (they
     never docked). Bail if 0 (undamaged + empty diffuser, the common case).
  2. **"Single diffuser of the carrier's choice" = a SINGLE TENDRIL (user 2026-06-11, post-test fix #1).**
     Pick the carrier's single most-free live tendril across ALL its `EnergyDiffuser`s (the biggest
     available sink) and fill ONLY that one via the **carrier-side** `DiffuserTendril::absorbDamage(
     $carrier,$gamedata,$put)` — which writes a persisted `DamageEntry` (carrier tendrils store energy as
     damage, NOT notes like the fighter `DiffuserTendrilFtr`), so it shows on the diffuser icon and
     replays correctly. **NB the first cut filled the whole diffuser (all tendrils) — 46 incoming
     vanished into two tendrils (30+16) with NO hull damage, because a ShadowCruiser diffuser holds 150
     across 10 tendrils. Reading it as ONE tendril (overflow penetrates) matches in-combat `doProtect`,
     which also commits a hit to one tendril, not the bank.** (Confirmed via game 4165 tac_damage:
     systemid 14 got +30/−15, systemid 15 got +16, both `Tendril`/`Absorb!` — my own fill loop, NOT a
     `doProtect` interception; the penetration DamageEntry is written DIRECTLY so it never re-enters the
     firing/`getSystemProtectingFromDamage` path.)
  3. **Excess → random carrier system via the ship's OWN `getHitSystem`, ignoring armor (post-test fix
     #4).** The FIRST cut filtered `isTargetable && !isDestroyed` — but EVERY Shadow system is
     `isTargetable=false` ("cannot be targeted by called shots", set in shadowCruiser.php ctor), so the
     list was EMPTY and the penetration silently vanished (game 4167: 30 absorbed into the tendril, 12
     excess LOST — no `ShadowReabsorb` row). FIX (user direction): a Shadow hull defines a `hitChart`
     precisely so the engine can resolve a random internal hit; reuse the established self-ram convention
     — shooter = target = the carrier, weapon = its `RammingAttack` (every ship has one), section 0 —
     and call `$carrier->getHitSystem($carrier, $fireOrder, $rammingSystem, $gamedata, 0)`. That routes
     through `getHitSystemByTable` (hitChart) for Shadow hulls and `getHitSystemByDice` for ordinary
     ones. UNLIKE the marine WreakHavoc reroll we do NOT exclude Structure (a Structure hit is a valid
     armour-ignoring outcome). Write an **armour-0** `DamageEntry` (`"ShadowReabsorb"`, "Diffuser
     overflow", shooterid/weaponid = carrier/ramming) for the excess, CLAMPED to the target's remaining
     health + `destroyed` flagged when it meets/exceeds (the single-system, no-cascade convention
     `applyMarineDamage` uses — leftover overkill is NOT spilled into other systems). The d20 inside
     getHitSystem rolls once on live advance and bakes into the persisted entry (no replay re-roll).

  **Replay-determinism — NO roll-note needed (deliberate).** The dock branch runs ONCE in the live
  Fire-Phase advance (`criticalPhaseEffects → processWholeFlightDocks → performWholeFlightDock`); the
  resulting `DamageEntry`s (diffuser fill + penetration) are flagged `updated` and persisted via
  `getNewDamages()→submitDamages`. A replay scrub does NOT re-run dock resolution (order already
  consumed, flight already docked), so `Dice::d` never re-rolls — the persisted DamageEntry IS the
  record. Same lifecycle as `applyCatapultLandingDamage` (also a roll-free, self-persisting landing
  DamageEntry). The rail-crit roll-note exists ONLY because `onRailStructureDamage` re-runs on load;
  the dock path doesn't, so the rail pattern was correctly NOT copied here.

  **Files touched:** `HangarOps.php` only (helper `applyDiffuserReabsorption` + new retire helper
  `dockAllFighters` + 2 call sites). No client/lobby/autoload change — purely server bookkeeping.

  **POST-TEST FIXES (2026-06-11, after first Docker run on game 4165 — 6 launched, 2 killed t2, 4 docked t3):**
  - **#1 single-tendril absorb (above):** overflow now penetrates instead of spilling across the bank.
  - **#2 reabsorbed fighters showed DROPOUT, not DOCKED, in replay.** S-c retired the landed flight via
    `destroyAllFighters` → stamps `DisengagedFighter` (= combat dropout). A reabsorbed fighter LANDED, so
    it must read DOCKED. New `HangarOps::dockAllFighters` stamps `DockedFighter` instead (replaces
    `destroyAllFighters` in BOTH Shadow branches). Still hides the old removed flight with no ghost —
    `Fighter::isDestroyed` folds in `isDocked` exactly as `isDisengaged` (fighter.php), and `DockedFighter`
    is already in the `copyFighterStateToTarget` source-only skip-list. Client `criticals.js isDockedFighter`
    renders cyan DOCKED.
  - **#3 destroyed fighters did NOT reduce carrier maxhealth — NOT an S-e bug, a STALE-DATA artifact.**
    Game 4165 has NO `shadowIntegratedState` note at all (verified: only hangarUsage/launch/dock notes on
    systemid 11). The S-d coupling needs `shadowLaunched` seeded at population + registered at launch
    (`registerLaunchedIntegratedFlight`, only reachable via `launchAnonymousStash`); this game was
    created/launched BEFORE the S-d code existed, so its turn-1 launch never recorded a baseline and
    `syncIntegratedStructureCoupling` had nothing to detect the 2 deaths against. Confirmed the live launch
    path is correct: integrated held entries are anonymous (no dockedFlightId), so `launchWholeFlight`
    bails (`$dockedId<=0`) and they route through `launchAnonymousStash`→register. **A FRESH game seeds +
    registers correctly** — re-test maxhealth-on-death in a new game, not 4165. (Matches the known S-d
    limitation: pre-S-d games need the note manually seeded.)
  - **#4 penetration vanished on the ShadowCruiser (fresh game 4167: 30 absorbed, 12 excess LOST).** The
    `isTargetable` filter yielded an empty target list because ALL Shadow systems are `isTargetable=false`.
    Fixed to resolve the target via the carrier's own `getHitSystem` (hitChart) using the self-ram
    convention (see step 3 above). #1 and #2 (single-tendril + DOCKED) plus #3 (maxhealth-on-death)
    were all CONFIRMED working in 4167; only the penetration write was still broken.

  **Verify (Docker, FRESH game):** launch integrated fighters, take diffuser hits in space + some craft
  damage, land them; the carrier's single most-free tendril fills by the absorbed amount, any excess
  beyond THAT ONE tendril hits a random system (armour-ignoring) on the dock turn; landed craft show
  cyan DOCKED in replay; killed fighters drop carrier Structure maxhealth by 1 each; replay the turn and
  confirm the same penetration system/amount (no re-roll).
- **S-f — ✓ IMPLEMENTED 2026-06-11 (core Fighter Bomb; NOT yet Docker-tested). Deploy-undock split out to S-f.1 (below).**
  Fighter Bomb = a SEPARATE `ShadowFighterBomb extends Weapon` that bursts the carrier's
  held integrated fighters out at a TARGET HEX. NOT a Hangar trait (Weapon/Hangar are siblings).

  **KEY RULE CHANGE (user 2026-06-11) vs the original S4 spec:** once the Fighter Bomb exists,
  a ShadowHangar **does NOT launch via the ordinary bay path at all** — the Bomb is the bay's
  ONLY launch path. Landing/reabsorption (S-c/S-e) is unchanged (still uses the normal
  hangar-output budget). The bomb is **player-count-selectable per shot** (user 2026-06-11 #2):
  on hex-target it pops the standard numeric count picker (`confirm.askForMultipleValues`,
  1..fighters-held), and launches that many at the hex — leaving the remainder docked for a
  later bomb. (NOT output-capped: the count is bounded only by the held pool.) An empty bay
  (fighters lost a previous turn) ⇒ the bomb cannot fire. Same-turn hangar destruction does NOT
  stop the bomb (it fires simultaneously with incoming fire).

  **AS-BUILT:**
  - **`ShadowFighterBomb extends Weapon`** ([specialWeapons.php](source/server/model/weapons/specialWeapons.php),
    right after `VortexDisruptor` — the structural model, a non-ballistic hex-target zero-damage
    Shadow weapon, NOT the BALLISTIC mine launcher). `$name='ShadowFighterBomb'`, `$hextarget=true`,
    `$range=10`, `$ballistic=false`, `$loadingtime=0`, `powerReq=0`, arc from ctor (300→60 on the
    Cruiser), `$spawnableClasses=['ShadowMediumFighterFlight']` (auto-preloads the blueprint via
    game.php). Overrides: `getDamage/min/max`→0; `calculateHitBase`→needed=100 (skips the base
    null-target "ERROR" branch); **`fire()`** does the whole effect → calls
    `HangarOps::performBombLaunch($shooter, OffsetCoordinate(x,y), $gamedata)` and sets
    `$fireOrder->rolled=1`. The `rolled=1` trips `Firing::fire`'s `if ($fire->rolled>0) return;`
    guard ⇒ replay-idempotent with NO extra note (same self-persisting pattern as VortexDisruptor;
    NOT `beforeFiringOrderResolution`, which the ballistic mine launcher needs because ballistics
    resolve before the fire step).
  - **`HangarOps::performBombLaunch($carrier, $spawnPos, $gamedata, $count = null)`** (new, right
    after `performLaunch`). Locates `primaryShadowHangar`; held pool = SUM of held
    `ShadowMediumFighterFlight` entries (skip `cannotLaunch` wrecks); bail null if 0. **burst =
    min(held, $count)** ($count is the fire order's `shots`; <=0/null defaults to the whole pool).
    Spawns ONE flight of that size via the existing `spawnLaunchedKFlight` primitive (reused — it
    spawns at an arbitrary pos/heading/facing/speed) at the TARGET hex with carrier heading/facing/
    speed (no hangar-direction offset). `registerLaunchedIntegratedFlight` (coupling baseline —
    persists through the SAME `shadowIntegratedState` note as bay-launched integrated flights).
    **Partial pool drain** — consumes `burst` worth in encounter order, dropping fully-consumed
    entries (retiring any dockedFlightId fragment via `destroyAllFighters`) and keeping the remainder
    on a partially-consumed entry (held entries are flightSize-1, so partial-on-one doesn't arise in
    practice but is handled). Writes a `hangarLaunchEvent` note `id:phpclass:burst:bomb` (write-only
    replay record, like `:resurrected`). `applyLaunchCrits` (LaunchedThisTurn -50 + carrier -20).
    Transport: `ShadowFighterBomb::fire` passes `$fireOrder->shots` as `$count`; `shots` is a
    first-class persisted FireOrder column, so the count round-trips with NO schema change.
  - **Ordinary launch DISABLED for ShadowHangars** — server `canLaunch` early-return; the anonymous
    (`launchAnonymousStash`) + docked (`findDockedEntry`) whole-flight launch paths now `continue`
    past `isShadowHangar` bays (the integrated entries are anonymous, so the anonymous path was the
    real exposure). The dead `if ($firstBay->isShadowHangar) registerLaunchedIntegratedFlight` block
    in `launchAnonymousStash` was removed (firstBay can no longer be a ShadowHangar).
  - **Client:** `ShadowFighterBomb` weapon mirror in
    [electromagnetic.js](source/public/client/model/weapon/electromagnetic.js) (thin `Electromagnetic`
    subclass beside `VortexDisruptor`; hex-target firing UI is driven by the json blueprint fields —
    no behaviour override). **Count picker** in `weaponManager.targetHex`: a `weapon.name ===
    'ShadowFighterBomb'` branch calls new `queueShadowFighterBombOrder` → reads the held pool
    (`shadowFighterBombPool`, sums isShadowHangar `hangarUsage`), pops `confirm.askForMultipleValues`
    (1..pool), and on OK builds the single hex fire order with `shots = chosen`. ShadowHangar excluded
    from EVERY launch-source entry point: `hasLaunchableHangar` + `hasLaunchableFighterHangar`
    (shipTooltipFireMenu.js), the `confirm.hangarLaunch` `all`-list (confirm.js), and the Firing-Phase
    branch of the hangar icon-click (SystemIcon.js — but the **deployment** branch still fires for
    ShadowHangars, see S-f.1).
  - **Test hull mount:** `new ShadowFighterBomb(5, 4, 0, 300, 60)` appended **after** the Structure
    on ShadowCruiser. Added LAST on purpose — system ids are construction-order positional, so
    appending after Structure keeps every existing system id stable on in-use ShadowCruiser games
    (the positional-system-id trap). Not on the hitChart (Shadow systems are untargetable anyway).

  **Files touched (S-f core):** server `specialWeapons.php` (new weapon, passes `shots` as count),
  `HangarOps.php` (`performBombLaunch` + `$count` param + launch-disable guards), `shadowCruiser.php`
  (mount). Client `electromagnetic.js` (weapon mirror), `weaponManager.js` (count picker —
  `queueShadowFighterBombOrder` + `shadowFighterBombPool`), `shipTooltipFireMenu.js` + `confirm.js` +
  `SystemIcon.js` (hide ordinary launch for ShadowHangars). **autoload.php** — user added
  `'shadowfighterbomb'` line (user edits autoload, never the assistant). The bomb's `$name` is a
  Weapon name resolved by the client SystemFactory via `new window['ShadowFighterBomb']`, and the
  blueprint preloads via `$spawnableClasses`. **NB user runs yarn build / Docker test themselves.**

- **S-f.1 — DEFERRED (deploy-time undock of integrated fighters):** At DEPLOYMENT the player may want
  to pull integrated fighters OUT of the bay to start in space (the reverse of deploy-docking),
  STILL structure-coupled (attached, can reabsorb) — same as bomb-launched. **Why it's not trivial
  "reuse the release path":** the deploy-dock dialog's release path operates on FLIGHT SHIP objects
  on the map that the player docks; integrated fighters at deploy are ANONYMOUS `hangarUsage` stash
  entries with NO backing flight ship (auto-populated server-side from SHAD_FTRL), so they never
  appear in the dialog's pending-flight list and have nothing to "un-check". Needs net-new work:
  (1) enumerate held integrated stash entries as a new releasable-row type in `hangarDeployDock`
  (with a count picker), and (2) a deploy-time sibling of `performBombLaunch` that spawns them out
  at the carrier hex with coupling registered. The SystemIcon deployment branch already opens
  `hangarDeployDock` for ShadowHangars (left enabled) so the hook is in place. Build after the core
  bomb is Docker-verified. Two fighter populations to keep distinct: bought-as-UNITS (SHAD_CTRL
  "uncontrolled", deploy to space normally, CANNOT dock a ShadowHangar) vs bought-as-ENHANCEMENT
  (SHAD_FTRL, auto-start docked, this undock applies).

### S9. Risk register (Stage-S-specific)
- **Carrier-death-by-fighter-pickoff** (S3) routes fighter death into structure
  destruction — make sure it can't infinite-loop (destroying the box that kills the
  ship which destroys remaining fighters…). Single-pass, idempotent per fighter.
- **enhCount vs hangar cap mismatch** — clamp population to `ceil($capacity)`; a player
  buying more SHAD_FTRL than capacity must be rejected in the lobby (cap is already
  `ceil($capacity)`), but double-guard server-side in S2.
- **Legacy Shadow games** — any Shadow hull NOT converted to ShadowHangar must be
  byte-for-byte unchanged. Keep the old free-SHAD_FTRL + maxhealth path alive behind
  the `isShadowHangar` branch until all shadow hulls are migrated (separate decision).
- **Diffuser excess penetration** (S5) ignoring armor — verify it uses the same
  "ignore armor" path as other armor-piercing effects; don't hand-roll damage.

### S10. Files (anticipated)
Server: `Enhancements.php` (SHAD_FTRL rework: price + drop maxhealth mutation for
ShadowHangar + strip audit), `baseSystems.php` (`ShadowHangar` class + crit-immunity +
binding state/notes/strip + discriminator), `HangarOps.php` (population from enhCount,
shuttle-pool exclusion, structure coupling handlers, land-reabsorb, optional bomb
launch), the structure-damage / ship-destruction path (for fighter-lost→box and
box-lost→cutoff — likely `ShipSystem.php`/`criticals.php`/structure handling),
`cricialClasses.php` (`ShadowFighterCutOff` crit), one test shadow hull (`Hangar` →
`ShadowHangar`). Client: `baseSystems.js` (`ShadowHangar` mirror + strip + tooltip),
`SystemIcon.js` (crit-immune visual / cut-off marker), fire-menu + `confirm.js` for the
bomb hex-pick (deferred sub-stage). Autoload (user): `shadowhangar`, `shadowfightercutoff`.

### S11. Open questions for the user before coding
1. **Price per integrated fighter = 150 CP** (ShadowMediumFighterFlight 900/6)? Or a
   different listed cost?
2. **Which hull(s) to convert first** to `ShadowHangar` for testing — a throwaway
   variant, or `ShadowCarrier` directly?
3. **Migrate ALL shadow hulls now, or only the test hull** (leaving the rest on the
   legacy free-SHAD_FTRL path until later)?
4. **Fighter Bomb (distant-hex burst): in scope now or deferred to S-Phase-2?**
   (Recommend deferred.)

NOT yet implemented — plan only.
