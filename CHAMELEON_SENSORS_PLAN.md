# Chameleon Sensor Suite (CSS) — Implementation Plan

Implement the B5W Chameleon Sensors rules on the Centauri **Dargan Strike Cruiser**: an ELINT array that additionally disguises its ship as a different vessel, so that enemy players see a *different unit* on the map, in the ship window, in their targeting maths and **in the damage it appears to take** — until the deception is broken.

> Test unit: `Dargan` ([dargan.php](source/server/model/ships/centauri/dargan.php)).
> Test container: a fresh Docker game. Needs **two accounts on opposing teams** — every behaviour in this plan is invisible from the owning seat.

---

## STATUS — updated 2026-07-31

| Stage | State |
|---|---|
| **0 — Baseline** | ✅ Built, in-game tested, committed |
| **1 — Buy-time disguise choice** | ✅ Built, in-game tested, committed |
| **2 — Reveal state machine** | ✅ Built, committed (HEAD `1f7660c34`). Two-account playtest of the reveal triggers still outstanding |
| **3 — Identity swap** | ✅ **COMPLETE** — built 2026-07-31, in-game tested and signed off 2026-08-01 (game 4273). Two defects found in the playtest and fixed, both weapon-arming (decision 4 below) |
| **4 — Phantom sheet** | ✅ **COMPLETE** — built and in-game tested 2026-08-01 (game 4273: a heavy laser hit the real Dargan, the phantom did not, both logs correct) |
| **5 — Mirrored resolution** | ✅ **COMPLETE** — built and in-game tested 2026-08-01 (game 4273). Four playtest bugs found and fixed; see below |
| 6 — Fire orders FROM the disguised ship (D7) + weapon-plausibility reveal (D6) | Not started — **next** |
| 7 — Dual-threshold resolution (D3b) | Not started. **Promoted from "optional refinement" to a required balance fix, 2026-08-01** |
| 8 — Arming mask on the ship's own payload (D11), then optional refinements | Not started |

> ⚠️ **Known live balance issue until Stage 7 lands:** a disguised ship is resolved against its
> simulacrum's defence profile *and* simulacrum DEW, with a single threshold — so it is genuinely
> harder to hit than it should be whenever it wears a smaller hull. Accepted knowingly as an interim
> state; Stage 7 removes it.

**An enemy now sees a different ship.** What they see is still *pristine* — Stage 3 serves the
simulacrum's blueprint, so the hull looks undamaged and unpowered; the phantom sheet is Stage 4.

Acceptance tests: `c:\tmp\css_stage0.php` (30 assertions), `css_stage1.php` (47), `css_stage2.php` (55),
`css_stage3.php` (104, both seats of game 4272) + `css_stage3_reveal.php` (6, drives the state machine
in memory) + `css_stage3_leak.php` (encodes the real enemy payload and greps it for "Dargan",
"Chameleon", "ELINT" — 0 hits, with the owner's payload as the control at 10/15/15) +
`css_stage3_arming.php` (25, game 4273 — no weapon reads null, accelerators read full charge, every
firing mode reads fully loaded, and a real weapon forced to `turnsloaded = 0` does not change what
the enemy sees).
Stage 2's injects a stub `Manager::$dbManager` by reflection to capture the notes a sweep would write
with no database — reuse that pattern.

### What Stage 3 built on

- `BaseShip::isChameleonDisguisedFrom($team)` — **the** single question every masking site asks.
  All the ways a deception can end (destroyed, offline, switched off, revealed to that team) resolve
  inside it, **and as of Stage 3 so does the own-team check** — put there rather than at each call
  site so a site that forgot it cannot show a player their own ally as a stranger.
- `BaseShip::getChameleonBlueprint()` — pristine, per-load cached instance of the simulacrum's class.
- `TacGamedata::$chameleonPresent` — the per-load gate; tests the disguise CHOICE, not the live ability.

### Stage 3 decisions that differ from the text below

1. **The disguise is applied from `prepareForPlayer()`, not from `deleteHiddenData()`.**
   `deleteHiddenData` is skipped when `$all` is true, which is how a PAST turn is served
   (`Manager::getReplayGameData` passes `$actualTurn > $turn`). Every other kind of hidden data is
   public once its turn has resolved; a disguise is not, and scrubbing back a turn must not undress
   the ship. Tested (`past-turn replay stays disguised`).
2. **The payload is built by asking the BLUEPRINT to strip itself**, then patching the real
   identity/position fields back in — rather than by editing the real ship's payload. The default for
   any field is therefore "fake", not "real", so nothing leaks through a line somebody forgot to
   write. The whole method is ~40 lines.
3. **`chameleonRevealTag` was not needed.** The client compares each incoming ship's `phpclass` /
   `faction` against the one already on the page (`gamedata.hasShipIdentityChanged`) and reloads on a
   mismatch — no new field, and it covers every future cause of an identity change, not just reveals.
   Skipped in replay, where stepping across the reveal turn is *supposed* to change identity.
4. **NEW: weapon arming is masked (D11 brought forward).** Found in the game-4273 playtest: every
   simulacrum weapon displayed **`null/<loadingtime>`**. A pristine blueprint has never had its
   loading calculated — that runs off `tac_loading` against a real ship — so `turnsloaded` went out
   as literal `null` and the client's label (`turnsloaded + '/' + loadingtime`,
   [shipSystem.js:220](source/public/client/model/shipSystem.js#L220)) printed it.
   `armChameleonSimulacrumWeapons()` sets every simulacrum weapon fully loaded, which is both the fix
   and D11 itself: the only arming state that is plausible on every turn and tells the enemy nothing.
   **"Fully loaded" is `max(loadingtime, normalload)`, NOT `loadingtime`** — an accelerator fires at
   one turn of charge but only reaches full potential at `normalload` (a Plasma Accelerator is
   `loadingtime 1 / normalload 3`), which is the real ship's own charge cap
   ([weapon.php:1074](source/server/model/weapons/weapon.php#L1074)) and both client displays'
   denominator (`SystemIcon` uses `normalload` outright,
   [weaponManager.js:2292](source/public/client/weaponManager.js#L2292) reads the same `max`).
   Arming to `loadingtime` alone made every accelerator read **`1/3`** — charged, but visibly not
   full. Use the `normalload` *property*, not `getNormalLoad()`: boostable weapons override that
   method to return `loadingtime + maxBoostLevel`, and claiming maximum boost on a phantom that
   shows no power allocated (D12) would be a tell rather than a mask.
   **Multi-mode weapons need `turnsloadedArray` mirrored key-for-key from `loadingtimeArray`** — the
   client re-reads `turnsloaded` out of that array whenever it is present ([shipSystem.js:219](source/public/client/model/shipSystem.js#L219)),
   so a scalar alone is discarded the moment the viewer looks at any mode.
   What is left of D11 for Stage 7 is the *other* half: masking arming on a CSS ship that is **not**
   disguised (`None`) or **already revealed**, which is the real ship's own payload and therefore a
   different code path.
5. **NEW: the ship's NAME is masked.** §3 said "pass through — warn the player in the buy dialog",
   but the default name is generated from the hull ("Dargan Strike Cruiser #2"), so an untouched name
   hands the enemy the answer and the deception is over before it starts. `getChameleonMaskedName()`
   swaps any mention of the real `shipClass`/`phpclass` for the simulacrum's, keeping the player's own
   words and numbering: *"Dargan Strike Cruiser #2" → "Altarian Destroyer #2"*, while
   *"Lord Kiro's Revenge"* passes through untouched. This was the ONLY leak the payload grep found.

### Stage 4 decisions that differ from the text below

1. **D12 (phantom power synthesis) is DROPPED — its premise is false.** The plan assumed "a phantom
   with zero power everywhere is a tell". `tac_power` holds only *exception* records (1 offline,
   2 boost, 3 overload); **absence of entries is the normal, healthy state**. Measured on game 4273:
   every real ship, on both seats, carries **zero** power entries across **all** systems, and a
   system with none reads `isOfflineOnTurn() === false`. Synthesising anything would have made the
   phantom the only ship on the board with power rows — the exact opposite of the intent.
2. **Phantoms are built in `DBManager::getTacGamedata`, after `$gamedata->onConstructed()`** — not
   "at the end of `getSystemDataForShips`" as §D2 says. That method only loads the raw
   `tac_enhancements` rows; **`onConstructed()` is what applies them**, and the disguise class *is*
   an enhancement. Building any earlier reads `null` and silently produces no phantom at all.
3. **`BaseShip::finaliseChameleonPhantom()` is required and was not in the plan.** A phantom is not
   a working sheet until `ShipSystem::onConstructed()` has run on it: that is what links each system
   to its Structure block, applies criticals, and latches `$destroyed` off the damage list — and
   `$destroyed` is the only thing `stripForJson()` sends. Without it a phantom system absorbed a
   fatal damage entry and was still served to the enemy as intact (caught by the Stage 4 test).
   It runs **after** the phantom's damage/criticals load, mirroring the real-ship order. It
   deliberately does *not* call `$phantom->onConstructed()`, which would also run
   `Enhancements::setEnhancements` (the phantom must never inherit the real ship's enhancements)
   and recompute initiative (patched from the real ship anyway, D13).
4. **Phantoms need their own `beforeTurn` sweep** in `TacGamedata::setPreTurnTasks()` — they are not
   in `$this->ships` (D1), so the existing sweep misses them and their tooltips go out empty.

### Known gaps left open by Stage 3 (all sequenced, none accidental)

- **A disguised ship's own shots do not reach the enemy's combat log** — the systems served are the
  blueprint's, so they carry no fire orders. Fixed by the weapon remap (D7, Stage 6).
- **The hull looks undamaged and unpowered** to the enemy — no phantom sheet yet (Stage 4, D12).
- **`calledid` from an enemy called shot is still a raw simulacrum system id** (finding #16) and will
  land on an arbitrary real system. D9, Stage 5. Nothing new — Stage 3 is what makes it reachable.
- **Hit-chance preview vs resolution disagree**: the enemy's client reads the fake defence profile off
  the blueprint (finding #10) while the server still uses the real one. D4, Stage 5.

### Decisions taken during implementation that differ from the text below

1. **§D10b storage split.** The plan had the CLIENT write the chosen phpclass into
   `enhancementOptions[i][1]`. That slot is also the option's display label in all four buy/edit
   dialogs, so a pick rendered as `"kendariUpgraded (up to 42 levels, 0pts)"`. **The client now
   submits only the INDEX**, and `Enhancements::getStoredEnhancementName()` resolves it to a phpclass
   server-side — same bytes in `tac_enhancements`, correct label, and a doctored payload cannot name
   an arbitrary class. Saved-fleet reload converts **name → index** against the rebuilt list.
2. **§D8 toggle phase is 1 (Initial Orders)**, not Deployment/Firing — it sits alongside the power
   on/offline control. Client `isTogglePhase()` and server `generateIndividualNotes` must agree.
3. **§D8 shutdown is a PERMANENT reveal.** Dropped / offline / destroyed writes a real `revealedNow`
   note rather than only reporting "not disguised right now"; otherwise a player could drop the
   disguise for a turn and resume it. A **Firing-advance checkpoint** was added so a destroyed array
   is recorded before the owner can self-repair it.
4. **The toggle button is withdrawn once EVERY enemy team has seen through it** (in a 1v1, simply
   "revealed"). Not while only some teams know — the disguise still has value against the rest.
5. **§6.8 closed: the disguise costs 0 points.**
6. **Reveal checkpoints take an explicit checkpoint name**, never `$gamedata->phase` — see trap 2 below.

### Traps found while building (each one cost real time)

1. **`getAllShips()` → `setEnhancementOptions()` → the disguise option is INFINITE RECURSION.**
   `ShipLoader::getAllShips` calls `Enhancements::setEnhancementOptions` on every ship it builds, and
   that is where `CHAM_DISG` is defined. `ShipLoader::getDisguiseCandidates()` is therefore
   deliberately self-contained and must never call `getAllShips`.
2. **Every phase's `advance()` sets the NEXT phase before its ship loop.** At Deployment advance the
   gamedata already reads phase 1; at Initial Orders advance it already reads phase 2. Dispatching a
   check on the phase silently runs it at the wrong checkpoint.
3. **`generateIndividualNotes` runs in `process()`, on POST-side ships** — rebuilt from the POST with
   no enhancements applied and no notes loaded. A check there that reads `chameleonDisguiseClass`
   (set by an enhancement) returns early on every call and fails silently, forever. Server-authoritative
   checks belong in `advance()`; only the toggle transfer belongs in `process()`.
4. **`tac_individual_notes.notekey_human` is `varchar(40)`** and overflow **aborts the whole player
   submission** with `(22001/1406) Data too long for column`. Keep reasons short; clamp anyway.
5. **The lobby serves `source/public/static/json/<faction>.json`, not `getAllShips`** — any change to
   enhancement options needs `php generateStaticShipFile.php` or the buy dialog shows stale options.
6. **The gate must test the disguise CHOICE, not `isChameleonDisguised()`** — a destroyed array drops
   the `ChameleonSensors` special ability, which is precisely the case the shutdown reveal records.
7. **Three places render a bought enhancement as `name (count)`** — `lobbyEnhancements.apply()` plus
   **two byte-identical blocks in gamelobby.js differing only by one indent level**. All now go
   through `lobbyEnhancements.describeTaken()`.
8. **Offering the choice list at all is a game-load path** (found 2026-07-31, game 4272 fatal).
   `Enhancements::setEnhancementOptions` runs from `ShipLoader::getShipsByClass` on **every**
   `game.php` load, so the `CHAM_DISG` block was calling `getDisguiseCandidates` →
   `getShipClassnames($faction)` → **`getFactionDirMap()`, which constructs every ship class in the
   codebase: 6.4 s and 176 MB, per page load** (on localhost the map cache is never *read*, only
   written, so there is no warm path). On live that is an lsphp memory-limit 503 waiting to happen —
   see the LiteSpeed memory note. Fixed with `Enhancements::$offerChoiceLists`, set false around the
   `getShipsByClass` loop: only the buy dialogs need the list, and in game the stored pick already
   resolves by name via `getDisguiseLabel()` (one construction). **Any future choice-valued option
   inherits this — build pick lists only where they are rendered.**
   The crash itself was a second bug it exposed: `getFactionDirMap` wrote its cache to a *shared*
   `/tmp/fv_cache`, which CLI runs (`generateStaticShipFile.php`, the test harness — root under
   `docker exec`) create first, leaving php-fpm (www-data) unable to write. And `@file_put_contents`
   does **not** save you — `Manager.php` installs an error handler that throws `ErrorException` for
   every warning *without* checking `error_reporting()`, so a suppressed warning is still fatal in
   any request that has loaded Manager. The cache directory is now per-uid and writes are
   `is_writable`-checked.

### Corrections to the audit in §1

- **Finding #24 overstated.** Two of the six name-based scanner lookups
  ([baseSystems.js:1215](source/public/client/model/system/baseSystems.js#L1215),
  [customTrek.js:376](source/public/client/model/weapon/customTrek.js#L376)) sit inside `/* */`
  blocks, so the "Dargan contributes nothing to stealth detection" and "cannot see cloaked ships"
  failures were never live. All six were routed through `getScannerList()` regardless.
- **The suite cannot be voluntarily powered down.** `canOffLine` is `false` on `ShipSystem` and
  nothing in the Scanner chain overrides it (§D8 intends this). "Switch off" is the Drop Disguise
  toggle; the `isOfflineOnTurn` branch only catches a *forced* offline from a crit.

---

## 0. Guiding constraints

- **Server-side masking only.** The client is fully inspectable (`window.gamedata`, devtools). Anything the enemy's browser receives is public knowledge. The disguise must be applied while building the outgoing per-player JSON, *never* by hiding things client-side.
- **Hard gating.** One ship in ~2000 has this system. The whole feature sits behind a single per-load boolean; a game with no CSS ship pays for one `false` check. No per-ship, per-system or per-shot work in the common path.
- **`None` is the default disguise.** The suite ships set to *"no disguise — the Dargan appears as itself"* (D10). That is the state of every existing Dargan and of any newly bought one the player does not configure, and it leaves the gate `false`. Everything below describes the ship only once a player has actively chosen a simulacrum.
- **`autoload.php` entries are added by the user**, never by the assistant. Two new classes are expected (`ChameleonSensors` PHP + JS).
- **No bundle commits** (`game.legacy.bundle.js` / `gamelobby.legacy.bundle.js` / `UI.bundle.js` are build artefacts).
- **Positional system ids.** `ChameleonSensors` must replace `ElintScanner` *in place* at [dargan.php:35](source/server/model/ships/centauri/dargan.php#L35) — same `addPrimarySystem` position, same constructor arity — or every stored `systemid` for that ship shifts.
- **Static JSON regen** after any class-default change: `php generateStaticShipFile.php` inside the php container.
- Each stage is a commit boundary and independently testable.

---

## 1. Current-state audit — what already exists that this can ride on

Verified against the working tree, 2026-07-29 (rows 21–25 added 2026-07-31):

| # | Finding | Where |
|---|---|---|
| 1 | **Gamedata JSON is already built and cached PER PLAYER** (`game_{id}_user_{uid}_json`), and the model object is rebuilt fresh from DB per request — so per-viewer mutation of ship objects is safe and already the established pattern. | [Manager.php:635](source/server/controller/Manager.php#L635), [Manager.php:588](source/server/controller/Manager.php#L588) |
| 2 | **`prepareForPlayer()` → `deleteHiddenData()` is THE per-viewer masking hook**, already used to hide deployment moves, enemy EW/power, combat pivots, pending fire orders and stealth-ship positions. | [TacGamedata.php:664](source/server/model/TacGamedata.php#L664), [TacGamedata.php:689](source/server/model/TacGamedata.php#L689) |
| 3 | `TacGamedata::$currentForPlayer` / `$currentForPlayerTeam` + `ShipSystem::isRevealedToCurrentViewer()` give system-level "is this viewer allied" masking, already used by Shading Field and Kirishiac Orbitals. | [TacGamedata.php:14](source/server/model/TacGamedata.php#L14), [ShipSystem.php:241](source/server/model/systems/ShipSystem.php#L241) |
| 4 | **The client rebuilds a whole ship from `window.staticShips[faction][phpclass]`** and merges the JSON over it. Swapping two strings in the JSON (`faction`, `phpclass`) swaps the icon art, ship class name, point cost, defence profiles, size class, hit chart and notes — for free. | [ship.js:7-45](source/public/client/model/ship.js#L7-L45) |
| 5 | **`window.staticShips` is built per page load from the phpclasses in THAT player's already-masked gamedata** — an enemy's page will naturally ship the disguise blueprint and *not* the Dargan's, with no extra work. | [game.php:29](source/public/game.php#L29), [game.php:104-180](source/public/game.php#L104-L180) |
| 6 | **Damage has no hp field — it is a list of `DamageEntry` rows keyed by `(shipid, systemid)`, and destruction is derived from them.** A second, parallel "sheet" therefore needs no new tables: it needs a ship object and a distinct `shipid`. | [ShipSystem.php:1415](source/server/model/systems/ShipSystem.php#L1415), [BaseClasses.php:181](source/server/model/BaseClasses.php#L181) |
| 7 | **TRAP — the damage and critical loaders have no null guard**: `$gamedata->getShipById($shipid)->getSystemById(...)` fatals on an unknown shipid. Any synthetic shipid in `tac_damage`/`tac_critical` must be guarded here first. | [DBManager.php:2596](source/server/controller/DBManager.php#L2596), [DBManager.php:2639](source/server/controller/DBManager.php#L2639) |
| 8 | `ShadingField` is a complete working template for *"player-toggled concealment with per-team, range-based detection"*: notes-driven `$active`, `checkStealthNextPhase()` writing `detected`/`undetected` notes valued `Team:N`, `isDetected()` doing hex-range + LoS, viewer-masked `stripForJson`. | [baseSystems.php:8949](source/server/model/systems/baseSystems.php#L8949), [baseSystems.php:9154](source/server/model/systems/baseSystems.php#L9154) |
| 9 | Detection is already re-evaluated at the right cadence: `checkStealth()` is called from Deployment advance and Movement advance. | [ShipClasses.php:1143](source/server/model/ships/ShipClasses.php#L1143), [DeploymentGamePhase.php:18](source/server/Phase/DeploymentGamePhase.php#L18), [MovementGamePhase.php:75](source/server/Phase/MovementGamePhase.php#L75) |
| 10 | Target defence profile has exactly one server hook (`$target->getHitSectionProfile($shooter)` / `…ProfilePos()`) and one client source (`target.forwardDefense` / `sideDefense`). The client one resolves off the blueprint, so a disguised target's hit preview uses the fake numbers automatically. | [weapon.php:1798](source/server/model/weapons/weapon.php#L1798), [weaponManager.js:2221](source/public/client/weaponManager.js#L2221) |
| 11 | Fire resolution funnels through a single call site — `Firing::fireWeapons` → `Firing::fire()` → `$weapon->fire()` — which is the natural seam for a second, mirrored damage pass. | [firing.php:955](source/server/handlers/firing.php#L955), [firing.php:1153](source/server/handlers/firing.php#L1153) |
| 12 | **TRAP — the combat log resolves the firing weapon by id on the shooter**: `shipManager.systems.getSystem(ship, fire.weaponid)`. A fire order from a disguised ship carries real Dargan weapon ids that do not exist on the fake ship. | [combatLog.js:90](source/public/client/combatLog.js#L90), [combatLog.js:635](source/public/client/combatLog.js#L635) |
| 13 | **The activation UI is fully generic.** `SystemActivation` renders whenever a client system implements `canActivate()`/`canDeactivate()`, and already honours `getActivateLabel()`/`getDeactivateLabel()`. **No React work is needed for the in-game toggle.** | [SystemActivation.js:220-238](source/public/client/UI/reactJs/system/SystemActivation.js#L220-L238), [SystemInfoButtons.js:848](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L848) |
| 14 | Enhancements are `array(enhID, humanName, numberTaken, limit, price, priceStep, isOption)`, persisted to `tac_enhancements` at buy time and re-applied on every load. An `isOption` entry already renders differently in the buy dialog. | [Enhancements.php:167](source/server/model/ships/Enhancements.php#L167), [BuyingGamePhase.php:391](source/server/Phase/BuyingGamePhase.php#L391), [DBManager.php:2800](source/server/controller/DBManager.php#L2800) |
| 15 | **TRAP — saved fleets drop `enhname`.** Reloading a saved fleet copies only `numbertaken` (`$option[2] = $numberTaken`), discarding the stored description. A choice encoded as a *string* dies on saved-fleet reload unless this is fixed. | [Manager.php:909-917](source/server/controller/Manager.php#L909-L917) |
| 16 | **TRAP — called shots are id-positional.** `getSystemById($fire->calledid)` on the real ship *succeeds* for a fake system id (both ships have ids 0..N), silently landing a called shot on an arbitrary real system. | [ShipClasses.php:2511](source/server/model/ships/ShipClasses.php#L2511) |
| 17 | Weapon arming status is one field: `turnsloaded` (plus `turnsloadedArray`), sent to everybody. Masking "which guns are hot" is a two-line override. | [weapon.php:376](source/server/model/weapons/weapon.php#L376) |
| 18 | The buy dialog renders enhancements as +/- spinners in **four separate loops**. A new widget type has to be added to all of them. | [confirm.js:578](source/public/client/UI/confirm.js#L578), [confirm.js:902](source/public/client/UI/confirm.js#L902), [confirm.js:1136](source/public/client/UI/confirm.js#L1136), [confirm.js:1351](source/public/client/UI/confirm.js#L1351) |
| 19 | `ShipLoader::getAllShips($faction)` (already cached, already used to populate `gamedata.allShips[faction]` in the lobby) can supply the legal-disguise list with no new query. | [shipLoader.php:111](source/server/controller/shipLoader.php#L111), [gamelobby.js:3758](source/public/client/gamelobby.js#L3758) |
| 20 | **Pre-existing bug in the test unit.** [dargan.php:28-33](source/server/model/ships/centauri/dargan.php#L28-L33) has dangling `.` concatenation operators, so `$this->notes` and the first `addPrimarySystem(new Reactor(...))` are one expression. It works by accident. Fix while in the file. | [dargan.php:28](source/server/model/ships/centauri/dargan.php#L28) |
| 21 | **The Hyach subs are the exact precedent for an EW-triggered reveal.** `Stealth::isDetectedInitial` runs at Initial Orders advance and reveals to *every* enemy team if the ship fired **or** `getAllEWExceptDEW($turn) > 0`. The note-writing, the enemy-team loop and the timing can be copied wholesale. | [baseSystems.php:213](source/server/model/systems/baseSystems.php#L213), [ShipClasses.php:1151](source/server/model/ships/ShipClasses.php#L1151), [ShipClasses.php:2020](source/server/model/ships/ShipClasses.php#L2020) |
| 22 | **Enemy `EW` is blanked in phase 1 only.** From Movement onward every viewer receives the ship's real EW entries — *types* as well as amounts. ELINT operations on a disguised ship are therefore plainly visible to the enemy in the same turn they are declared. | [TacGamedata.php:709-730](source/server/model/TacGamedata.php#L709-L730) |
| 23 | `EW::getScannerOutput($ship, $turn)` is the single server-side EW-ceiling helper (sums every system with `outputType == "EW"`, applies RestrictedEW / SensorLoss). It takes any ship object, so it can be pointed straight at a disguise blueprint. | [EW.php:42](source/server/handlers/EW.php#L42) |
| 24 | **TRAP — the client identifies scanners by name string in six places** (`name == "scanner"` / `name == "elintScanner"`), where the server uses `instanceof Scanner` / `instanceof ElintScanner`. A system named `chameleonSensors` is invisible to all six. | [gamedata.js:773](source/public/client/gamedata.js#L773), [lobbyEnhancements.js:89](source/public/client/lobbyEnhancements.js#L89), [baseSystems.js:1208](source/public/client/model/system/baseSystems.js#L1208), [customTrek.js:315](source/public/client/model/weapon/customTrek.js#L315) |
| 25 | ELINT-ness is `hasSpecialAbility("ELINT")` — a lookup into `enabledSpecialAbilities`, merged from every system's `getSpecialAbilityList()` at construct time; the client mirror `shipManager.isElint()` reads the same JSON. Disguising the systems hides ELINT for free — and the enemy client then silently drops Disruption from its own hit-chance maths. | [ShipClasses.php:1108](source/server/model/ships/ShipClasses.php#L1108), [ships.js:944](source/public/client/ships.js#L944), [ew.js:752](source/public/client/ew.js#L752) |

---

## 2. Design decisions

### The system itself

**D0 — Chameleon Sensors *is* the Dargan's ELINT array, not an extra box.**
The CSS replaces `ElintScanner` in place (§0), so everything that makes the Dargan an ELINT ship has to survive the swap:

- **Keep `"ELINT"` in `$specialAbilities`.** `class ChameleonSensors extends ElintScanner`, and the ability array must be declared whole — `array("ELINT", "ChameleonSensors")` — or appended in the constructor after `parent::__construct()`. Redeclaring the property in a subclass *replaces* the parent's array; dropping `"ELINT"` silently strips SOEW / SDEW / Blanket Protection / Disruption / Jamming from the ship, because `hasSpecialAbility()` is a lookup into `enabledSpecialAbilities`, merged from each system's `getSpecialAbilityList()` at construct time (finding #25).
- **`$name` must be `"chameleonSensors"`.** The client instantiates `new window[ucfirst(json.name)]` ([systemFactory.js:78](source/public/client/model/systemFactory.js#L78)), so the distinct JS class the D8 toggle and the reveal notes need forces a distinct name. The JS class mirrors the PHP — `ChameleonSensors.prototype = Object.create(ElintScanner.prototype)` — which inherits `Scanner.prototype.isScanner() === true` ([baseSystems.js:51](source/public/client/model/system/baseSystems.js#L51)).
- **Server-side ELINT comes free.** Every server discriminator is `instanceof Scanner` / `instanceof ElintScanner` ([Enhancements.php:487](source/server/model/ships/Enhancements.php#L487), [ShipClasses.php:1417](source/server/model/ships/ShipClasses.php#L1417)), and `EW::getScannerOutput` keys off `outputType == "EW"`. All inherited, nothing to do. `notesFill()` will keep printing *"ELINT Sensors"* on the real ship — correct, and already masked by the fake class's own notes (§3).
- **Client-side ELINT does not** (finding #24). Six lookups match scanners by name string and would skip `chameleonSensors`:

| Site | What breaks if missed |
|---|---|
| [gamedata.js:773](source/public/client/gamedata.js#L773) | the "ship has no EW" warning list never clears for a Dargan |
| [baseSystems.js:1208](source/public/client/model/system/baseSystems.js#L1208) | Dargan contributes nothing to stealth-detection rating |
| [customTrek.js:315](source/public/client/model/weapon/customTrek.js#L315), [customTrek.js:377](source/public/client/model/weapon/customTrek.js#L377) | Dargan cannot see cloaked ships |
| [lobbyEnhancements.js:89](source/public/client/lobbyEnhancements.js#L89), [:293](source/public/client/lobbyEnhancements.js#L293), [:481](source/public/client/lobbyEnhancements.js#L481) | sensor enhancements bought in the lobby find no array to modify |

  Fix once: add `shipManager.systems.getScannerList(ship)` returning systems where `system.isScanner()`, and route all six through it. The one-line alternative (`|| system.name == "chameleonSensors"` at each site) works but leaves the next ELINT variant with the same bug.

### The core model

**D1 — One real ship + one phantom sheet.**
The disguise has two halves:

1. **Identity swap** — for non-allied viewers, the outgoing JSON's `phpclass`/`faction` are the disguise class, so the client builds the whole fake unit off the static blueprint (finding #4). Nearly free.
2. **The phantom sheet** — a real in-memory `BaseShip` instance of the disguise class that accumulates its own `DamageEntry` list, so the enemy sees a plausibly *damaged* ship, hit in plausible places, rather than an untouched hull.

The phantom is **not** a `tac_ship` row and never enters `$gamedata->ships` — it would then have to be excluded from initiative, the movement loop, targeting lists, CPV, `isFinished()` and replay. Instead it hangs off the real ship as `$ship->chameleonPhantom` and is built per load.

**D2 — Phantom identity: `id = -realShipId`.**
Finding #6 means the phantom's damage persists through the *existing* `tac_damage` machinery the moment its ship object reports a negative id: `assignDamageReturnOverkill` stamps `$target->id` into every `DamageEntry`, and `submitDamages` writes it positionally. Same for `tac_critical`. Negative ids cannot collide with real ones and are self-describing in the table.

Required support work (all small, all gated):
- Null-guard the two loaders (finding #7) so negative-shipid rows are skipped by the normal path. *This guard is a defensive improvement regardless of this feature.*
- One gated loader, `getChameleonPhantomDamage()` / `…Criticals()` (`WHERE gameid=? AND shipid<0 AND turn<=?`), run only when the CSS gate is on, applied to the phantoms.
- Phantom construction must happen **after** enhancements load (the disguise class comes from an enhancement at [DBManager.php:2800](source/server/controller/DBManager.php#L2800)) and **before** the phantom damage query — i.e. a new explicit step at the end of `getSystemDataForShips`.

**D3 — Resolution: one to-hit roll, one damage roll, two allocations.**
This is the tabletop rule read literally — *"damage to the disguised vessel should be rolled normally and marked publicly on the false control sheet, but secretly recorded (using the appropriate hit location rolls) on the CSS ship's sheet at the same time"* — and it is also the cheapest correct thing:

```
prepareFiring : needed  = computed against the PHANTOM's defence profile (D4)
fire()        : rolled  → shotshit                        (one roll, public and true)
damage()      : damage amount rolled ONCE
   pass 1 (secret) : allocate on the REAL ship   — real hit chart, real armour, real systems
   pass 2 (public) : allocate on the PHANTOM     — fake hit chart, fake armour, fake systems
```

Both passes see the same shots and the same damage totals, so the two sheets stay in lockstep; they diverge only in *where* the damage lands and how much armour eats. The enemy's combat log, damage bars and system-destroyed markers all read off pass 2 and are internally consistent. Nothing about pass 1 changes — the real ship takes exactly the damage it would have taken anyway.

Seam: wrap `Firing::fire()` (finding #11), not `Weapon::fire()` — the latter is overridden by dozens of subclasses.

**D3a — Divergent destruction: reveal. (Resolved.)**
Different armour and structure totals mean the phantom can die before or after the real ship. **If the phantom would be destroyed while the real ship lives, reveal instead** — clamp the fatal entry, write the reveal note, and let the enemy see the truth from that moment. A destroyed hull that keeps flying is a worse tell than anything else in this plan. (The converse — real ship dies first — is moot; destruction ends the deception.)

**D3b — The literal "hit the simulacrum but not the real ship" case is deliberately dropped.**
The rules also allow a shot to beat the fake profile but not the real one (and vice versa), which needs two `needed` thresholds, two `shotshit` counts, and therefore a *viewer-masked fire order* — the real values for the owner, the fake ones for everyone else. Since `tac_fireorder` stores one set, that means either a schema change or a per-fire-order note blob, plus divergent combat logs on both sides.
Recommendation: **ship the single-threshold model (D3).** Hit counts then agree for both players and only the damage narrative differs, which preserves everything the deception is actually for. If the full reading is wanted later, it is additive on the same seam — the real threshold goes in the DB, the fake one in a note keyed by fireorderid.

**RESOLVED 2026-08-01 — D3b IS NOW IN SCOPE, as Stage 7, and it is a balance FIX rather than a
refinement.** User's ruling: *"the intention of the Chameleon Sensors is absolutely not to make the
real Dargan harder to hit — the defensive bonus from mimicking a small ship would be too great to
allow."* That makes the single-threshold model incorrect, not merely incomplete: see Stage 7 below
for what it requires. The analysis that produced the ruling is kept here.
Observed: both players' combat logs showed the same hit chance, and the owner asked why they did not
see their *real* vulnerability (profile 16 / ~109%) instead of the simulacrum's (14 / ~104%).

The answer is that under D3 **there is no second number anywhere** — this is not a display mask. The
server computes `needed` exactly once, from the simulacrum's profile (D4, "resolve the fire using
the simulated ship's defense ratings"), and `Weapon::fire()` makes exactly one `$rolled <= $needed`
test. Both sheets then take the shot or neither does. So:

- **"Hit the real ship but not the phantom" (and vice versa) cannot currently occur.** The mirror in
  `Weapon::damage()` is downstream of the single hit test; it never re-rolls and never re-thresholds.
- **The disguise therefore confers real defensive benefit, not only information denial** — a Dargan
  (profile 16) wearing a Demos (14) is genuinely hit less often than a Dargan. That follows directly
  from the tabletop rule quoted above, but it is a balance effect worth stating plainly, and it is
  the strongest argument for eventually building D3b.
- The same is now true of DEW after the Stage 5 playtest fix: a smaller-sensor simulacrum costs the
  disguised ship defensive EW, a larger-sensor one gains it.

Building D3b means two `needed` values and two `shotshit` counts per order, a viewer-masked fire
order (real for the owner, fake for everyone else), and divergent combat logs — a schema change or a
note blob keyed by fireorderid. **Now scheduled as Stage 7.**

**D3c — The phantom takes damage but rolls no criticals. (Resolved.)**
`Criticals::setCriticals` walks `$gamedata->ships`, which the phantom is deliberately not in (D1), so it gets none by default and **we are keeping it that way for now**. Consequences to accept:
- the enemy sees a hull accumulating damage and losing systems to destruction, but never a critical result;
- no phantom critical can knock out a phantom system, so phantom system state stays a pure function of its damage list — which keeps Stage 4's persistence model simple.
A phantom that soaks 30 damage without a single critical is a mild tell to an attentive opponent. Revisiting means a gated critical pass over phantoms in `FireGamePhase::advance`, writing to `tac_critical` under the negative shipid exactly as damage does — additive, no redesign.

**D4 — To-hit uses the simulacrum's profile.**
*"Resolve the fire using the simulated ship's defense ratings."* The client already does this for free (finding #10 — it reads the fake blueprint), so the server must be made to agree or preview and resolution will silently disagree. Add to `BaseShip`:
```php
public function getDisguisedProfileFor($shooter) //null unless disguised from $shooter's team
```
consumed in `Weapon::calculateHitBase` where `$defence` is set ([weapon.php:1796-1800](source/server/model/weapons/weapon.php#L1796-L1800)), before the CnC `ProfileIncreased` modifier. Fake `forwardDefense`/`sideDefense`, **real** bearing (which way it points is observable). EW, jink, range and fire control all stay real.
Deliberate asymmetry: a ship revealed to team A but not team B uses different profiles against the two. That is the rule working.

### Reveal

**D5 — Two checkpoints, two timings, per enemy team, permanent.**
Reveal state lives in `IndividualNote`s on the CSS system, `ShadingField`-style, valued `Team:N`. Once written for a team it is never withdrawn.

| Checkpoint | Trigger | Effective |
|---|---|---|
| **End of Movement advance** | an enemy unit is within **5 hexes** (ships) / **2 hexes** (fighters & shuttles), LoS-permitting | **immediately** — note key `revealedNow`, masking test `note.turn <= gamedata.turn` |
| **End of Movement advance** | the ship changed speed by more than its simulacrum could manage (D6b) | **immediately** — same key, it is observed as it happens |
| **End of Initial Orders advance** | the ship ran ELINT operations its simulacrum could not run, or spent more non-DEW EW than the simulacrum owns (D6c) | **immediately** — same key; EW goes public at phase 2 of this same turn |
| **After Firing advance** | the ship fired a weapon its simulacrum **could not have fired** (D6) | **next turn** — note key `revealedNextTurn`, masking test `note.turn < gamedata.turn` |
| any load | CSS destroyed, or switched off by the player (D8) | immediately |
| Firing advance | phantom would be destroyed while the real ship lives (D3a) | immediately |

Two note keys rather than one flag is what buys the "next turn" delay for free — the firing check runs at the *end* of the turn, so a note stamped with the current turn is invisible until the turn rolls over.
A start-of-turn proximity check at Deployment advance is worth keeping for turn-1 deployments inside 5 hexes, but the end-of-Movement check is the primary one (positions do not change between Firing and the next Movement).

**D6 — "A weapon the simulacrum could not have fired."**
Not *any* fire order — the mismatch is what betrays you. Test, in increasing cost:
- **A (ship this):** weapon **class presence**. Compare `get_class($weapon)` against the set of weapon classes on the phantom blueprint. Dargan fires a `BattleLaser`, phantom is a Demos with none → mismatch → reveal next turn.
- **B (refinement):** **count** — firing more of a weapon type than the phantom mounts.
- **C (refinement):** **arc/location** — firing from an arc in which the phantom mounts no such weapon.
Ballistic launches declared in Initial Orders run through the same test when they resolve. The CSS system tooltip must spell out the rule and, ideally, the client should warn at declaration time (see Stage 6).

**D6b — "Acceleration the simulacrum could not have managed."**
*"…does nothing that the false image could not normally do (e.g., accelerate much faster than would normally be possible)."* The machinery already exists: `Engine::doStuckEngine` computes an involuntary acceleration as **`floor($maxThrust / $ship->accelcost)`** ([movement.php:846-870](source/server/handlers/movement.php#L846-L870)), and `MovementOrder->speed` carries the ship's speed at every step ([BaseClasses.php:101](source/server/model/BaseClasses.php#L101)).

At Movement advance, alongside the proximity sweep:
```
deltaV      = |lastMovement(turn N)->speed  −  lastMovement(turn N−1)->speed|
maxDeltaV   = floor( phantomThrustAvailable / phantom->accelcost )
if (deltaV > maxDeltaV) → revealedNow
```
`phantom->accelcost` is a blueprint property (Dargan 3). `phantomThrustAvailable` should be read **generously** — the phantom's *undamaged* engine `getOutput()` plus the maximum boost that engine allows — because the rule says *"much faster than would normally be possible"*, and a false positive here is far more annoying than a missed reveal. Using the phantom's mirror-damaged engine output instead would be more internally consistent (the enemy can see that damage) but makes the threshold tighten unpredictably mid-game; **recommend blueprint-generous, note the alternative**.

`abs()` covers deceleration as well as acceleration, which is what the rule intends — a hull that stops dead far faster than a Demos could is just as damning as one that sprints.

Timing is **immediate**, unlike the weapon check: the burn is watched as it happens, and it happens at the same checkpoint as proximity. If playtesting prefers parity with the weapon rule, switching to `revealedNextTurn` is a one-key change.

Edge cases to guard: turn 1 (no previous turn — skip); a ship that did not move; forced movement (`$move->forced`, e.g. an actual Involuntary Acceleration crit on the *real* ship) — that is not the player "doing" anything, so exclude it or the crit becomes a self-inflicted reveal.

**D6c — "ELINT the simulacrum could not have run." (The Hyach-sub checkpoint.)**
Modelled directly on the Hyach stealth ships, which are revealed the moment they use any EW other than DEW: `Stealth::isDetectedInitial` runs from `generateIndividualNotes` case 1 (Initial Orders advance) and reveals to every enemy team when `$ship->getAllEWExceptDEW($turn) > 0` or a weapon fired (finding #21). The CSS wants the same shape with a *conditional* threshold, because a Dargan disguised as another ELINT ship is entitled to run ELINT.

Why it must exist: from phase 2 onward the enemy receives the ship's **real EW entries — types and amounts** (finding #22). A "Haven" carrying a `BDEW` entry, or spending 10 points of OEW when a Haven's whole sensor suite is 7, is self-evidently not a Haven. Two tests:

- **E1 — ELINT operations while disguised as a non-ELINT ship.** Any EW entry this turn of an ELINT-only type — `SOEW`, `SDEW`, `BDEW`, `DIST`, `JAM` — when the disguise class mounts no `ElintScanner` → reveal.
- **E2 — More EW than the simulacrum owns.** `$ship->getAllEWExceptDEW($turn) > phantomEWCeiling` → reveal, whether or not the phantom is an ELINT ship.

`phantomEWCeiling = EW::getScannerOutput($disguiseBlueprint, $turn)` (finding #23), read **generously** on the same convention as D6b: the blueprint's *undamaged* scanner output plus the maximum boost that scanner allows. Blueprint, not the live phantom — the ceiling must not wander as the phantom accumulates mirrored damage, and the check has to work in Stage 2, before the phantom sheet exists.

**Timing is immediate (`revealedNow`)**, and the checkpoint is the Initial Orders *advance* — EW is blanked during phase 1 and public from phase 2 of the same turn, so a next-turn delay would leave the enemy staring for a full turn at evidence the game refuses to act on. Same reasoning as D6b; the opposite of the weapon test (D6), which resolves only after the enemy has already seen the shot land.

It also closes a preview/resolution desync: the enemy's client skips non-ELINT ships when subtracting Disruption from its own OEW (`getDistruptionEW` → `shipManager.isElint`, finding #25), so a disguised Dargan running `DIST` would be invisible to the enemy's hit-chance preview while the server applied it in full. Revealing on first ELINT use bounds that mismatch to the one phase in which EW is hidden from everybody anyway.

Edge cases: a disabled ship or dead scanners give `getScannerOutput` 0 and leave nothing to spend — skip. A CSS switched off (D8) is not disguised, so no test. Neither test looks at EW *targets*, only at the ship's own allocations, so the cost is one pass over `$ship->EW` per CSS ship per turn.

*Open refinement — DEW.* As specified, both tests count non-DEW EW only, matching the Hyach precedent. But the enemy sees the DEW entry too, so 5 DEW + 6 OEW = 11 points on a 7-EW Haven passes E2 while looking exactly as wrong. Testing *total* EW against the ceiling is a one-word change; recommend playtesting E2 as written first, since the non-DEW form can never fire on a ship that is merely turtling.

**D7 — Fire orders FROM the disguised ship must be remapped (finding #12).**
Because firing no longer auto-reveals, the enemy client receives fire orders whose `weaponid` points at real Dargan systems that do not exist on the phantom — the combat log and replay would break on `getSystem(ship, fire.weaponid)`.
Build a stable **real weapon → phantom weapon** map once per load (same class first, then same arc/location, then any weapon) and rewrite `weaponid` on outgoing enemy copies. When there is no counterpart, map to the nearest phantom weapon anyway: the enemy sees "Demos fires a Matter Cannon" for what was really a Battle Laser, and the damage discrepancy is *exactly* the tell that D6 turns into a reveal next turn.
Also scrub the fire order's `notes` / `pubnotes` for the same viewers — they carry the defence/EW/chance breakdown and weapon names.

**D8 — In-game toggle: notes-backed `$active`, no React work.**
Per finding #13, a client class implementing `canActivate()`/`canDeactivate()`/`doActivate()`/`doDeactivate()` gets the standard menu automatically; labels via `getActivateLabel() => "Disguise"` / `getDeactivateLabel() => "Drop Disguise"`. State round-trips through `doIndividualNotesTransfer` → `generateIndividualNotes` → `onIndividualNotesLoaded`, exactly as `ShadingField` does it.
Allow the toggle in the **Deployment/pre-turn phase** and the **Firing phase** (effective next turn — the FV convention used by Hangar Ops and Kirishiac Orbitals). Switching back on does **not** restore the disguise for a team already revealed.
Do **not** reuse power `canOffLine`: making the array offlineable would hand the player 4 free power and change ELINT balance.

**D9 — Called shots at a disguised ship (finding #16).**
Translate `calledid` **by system class/displayName** onto the real ship (a called shot at the phantom's Engine hits the real Engine); with no counterpart, force `calledid = -1` and resolve as an ordinary hit. Do it in `Firing::prepareFiring`/`validateFireOrders`, once, before hit-chance maths. Never let a raw phantom id reach `getSystemById`. The phantom's own pass-2 allocation uses the *original* `calledid`, which is valid on the phantom by construction.

### Presentation

**D10 — The disguise is chosen at buy time and stored as an enhancement OPTION.**
`CHAM_DISG`, price 0, `isOption = true`, offered only when the unit has the CSS ability (`enhancementOptionsEnabled` gating, exactly like `SHAD_FTRL`). Legal targets per the rules ("any other kind of ship … not a fighter … not a base or enormous unit"):

```
"None"  <-- DEFAULT, index 0: no disguise, the Dargan is itself
shipSizeClass 0..3, and NOT: FighterFlight, $base, $smallBase, $osat, $mine, Terrain, $Enormous
same faction as the disguising ship          (see D10a)
excluding the ship's own phpclass
```

**"None" is the default and it is load-bearing.** The first entry in the choice list is `None`, it is what an untouched buy dialog submits, and it is what every pre-existing Dargan resolves to. A Dargan bought with `None` behaves *exactly* as it does today: no phantom is built, no reveal checkpoint runs, `stripForJsonDisguised()` is never reached, and the ship shows as a Dargan to everybody. The system is still a live ELINT array with its ELINT abilities (D0) and still masks arming status (D11) — those are properties of the suite, not of the deception. Concretely this means:

- the per-load CSS gate (§0, Stage 0) tests *"a ship has a CSS **and** a disguise class other than `None`"*, so a game full of undisguised Dargans stays on the common path;
- `$ship->chameleonDisguiseClass` is `null`/`""` for `None`, and every consumer must treat that as "not disguised" rather than assuming a class string exists;
- a player who buys a Dargan and never opens the option gets a working ordinary ship, not a broken one — which is also the fallback if the stored choice fails to resolve (deleted ship class, changed faction list).

**D10a — Same faction only (recommended).** The rules permit any type, but FV colours icons by *team*, so an enemy-faction disguise renders in the wrong colour and defeats itself. Same-faction also keeps the blueprint preload cheap. Widen later behind a flag.

**D10b — Storage: `numbertaken` must carry the choice.** Because of finding #15, encode the selection as an **index into the legal list sorted by phpclass, with `0 = None`**, *and* store the phpclass in `enhname` (empty for `None`). On load prefer the name, fall back to the index; **anything unresolvable falls back to `None`**, never to an arbitrary ship. `numbertaken = 0` is also what the enhancement machinery already produces for "not taken", so the default costs no special case. Add the two-line fix at [Manager.php:912](source/server/controller/Manager.php#L912):
```php
if (!empty($option[7])) $option[1] = $enhEntry[2]; //choice-valued option: name IS the value
```
Tuple index 7 becomes the optional choice list — absent on every other enhancement.

**D11 — Arming status is masked, permanently.**
*"Chameleon suites mask the arming status of weapons … even after the deception is revealed."* For weapons on a CSS ship viewed by a non-ally, send `turnsloaded = loadingtime` (always ready) and drop `turnsloadedArray` / `overloadturns`. Keyed on *"ship has a live CSS"*, not *"is disguised"* — this is the one effect that survives the reveal.

**D12 — Phantom power display.** Enemy viewers can see power allocations from phase 2 onward; a phantom with zero power everywhere is a tell. Synthesise each phantom system at its `powerReq` (everything on). Cheap, plausible, no state.

**D13 — Initiative: send the real value. (Resolved.)** Faking the number while the ship still *moves* in its real initiative order is a worse tell than the truth. Same for `currentturndelay`. Accepted seam, documented.

**D14 — Mid-game reveal needs a client reload.**
`window.staticShips` is fixed at page load (finding #5). When a disguise breaks mid-session the enemy's page has the fake blueprint but not the Dargan's, and `SystemFactory.createSystemFromJson` would build systems from JSON alone — no armour, no arcs, no maxhealth ([systemFactory.js:76](source/public/client/model/systemFactory.js#L76)).
**Recommended:** send a monotonic `chameleonRevealTag` on the ship; the client snapshots it at load and calls `location.reload()` when it changes. Reveals happen at most once per CSS ship per game, at a turn boundary.
(Alternative: preload both blueprints — simpler, but a Dargan blueprint sitting in `window.staticShips` is itself a tell.)

**D15 — Observers and replay.** Observers (no team) see the disguise. Replays render from the same masked per-user payload, so each seat replays consistently. **Open question:** should a *finished* game drop all disguises for post-mortem? Recommend yes, gated on `$gamedata->status == 'FINISHED'`.

---

## 3. Leak audit — every field an enemy currently receives

`BaseShip::stripForJson` ([ShipClasses.php:620](source/server/model/ships/ShipClasses.php#L620)). `stripForJsonDisguised()` must decide on every one:

| Field | Leaks? | Action |
|---|---|---|
| `phpclass`, `faction` | **yes — the whole point** | disguise class / faction |
| `systems` | **yes** (names, arcs, ELINT presence, damage, fire orders) | the **phantom's** systems, carrying the phantom's damage (D1). This also removes `"ELINT"` from the enemy's view of the ship (finding #25) — intended, and precisely why D6c exists |
| fire orders on those systems | **yes** — real weapon ids, real notes | remap per D7 |
| `notes` | **yes** — currently literally says *"Chameleon Sensors"* | the fake class's `notesFill()` output |
| `enhancementTooltip`, `pointCostEnh` | **yes** (spend + the disguise line) | drop / send 0 |
| `combatValue` | leaks real damage state | compute from the **phantom** |
| `iniative`, `unmodifiedIniative`, `iniativeadded`, `currentturndelay` | mildly | **send real** (D13) |
| `movement` | must stay real (position/facing are observable) | pass through |
| `EW` | real EW is observable in play — blanked in phase 1 only (finding #22), so *types* and amounts both reach the enemy from Movement onward | pass through — this is the evidence D6c acts on; masking it instead would make the deception unbreakable |
| `name` | player-chosen | pass through — warn the player in the buy dialog |
| `id`, `userid`, `team`, `slot`, `slotid` | needed for targeting/teams | pass through (the phantom wears the **real** id) |
| `destroyed`, `unavailable`, `spawned`, `removed` | destruction ends the deception anyway | pass through |
| `hangarRequired`, `hasAttached`/`attached`, `skinDancing`, `integratedFighterCount` | situational | drop unless the fake class would have them |

---

## 4. Stages

> **Stages 0-2 are DONE — see the Status section at the top of this file for what actually shipped
> and where it differs from the text below. The stage descriptions here are the original design.**

### Stage 0 — Baseline (shippable alone) ✅ DONE
1. Fix the dangling-`.` notes concatenation at [dargan.php:28-33](source/server/model/ships/centauri/dargan.php#L28-L33).
2. Null-guard the damage and critical loaders (finding #7) — defensive, independent of this feature.
3. `ChameleonSensors extends ElintScanner` (PHP): `$name = "chameleonSensors"`, `$displayName = "Chameleon Sensors"`, **`$specialAbilities = array("ELINT", "ChameleonSensors")` — declared whole, so `"ELINT"` survives (D0)**, rules text in `setSystemDataWindow` (spell out the reveal rules: proximity, thrust, ELINT/EW, weapons).
4. Matching JS class in [baseSystems.js](source/public/client/model/system/baseSystems.js), extending `ElintScanner` so `isScanner()` stays true — required, `createSystemFromJson` does `new window[Name]`.
5. Widen the six client name-based scanner lookups to `isScanner()` (D0, finding #24) — the `getScannerList()` helper.
6. Swap into [dargan.php:35](source/server/model/ships/centauri/dargan.php#L35) **in place**; regenerate static ships.
7. `BaseShip::hasChameleonSensors()` + the `TacGamedata` static gate (false for every existing game — and false for a CSS ship whose disguise is `None`, D10).

*Test:* Dargan loads with the new system in the ship window and **nothing else moves**. Specifically, ELINT behaviour is unchanged: notes still say "ELINT Sensors", the ship can still allocate SOEW / SDEW / BDEW / DIST, it still boosts as the highest sensor, it still counts as ELINT for stealth-detection and cloak-detection ranges, and lobby sensor enhancements still find its array.

### Stage 1 — Buy-time disguise choice ✅ DONE
`CHAM_DISG` option (D10), choice list from `ShipLoader::getAllShips($faction)`, `<select>` widget in all four `confirm.js` loops (finding #18), `setEnhancementsShip` case storing `$ship->chameleonDisguiseClass`, saved-fleet fix (D10b).

*Test:* buy a Dargan, pick "Demos", save the fleet, reload it, start the game — the choice survives lobby → DB → game-load. **Leave a second Dargan on the default `None` and confirm it is byte-identical to a pre-Stage-1 Dargan** end to end. Every other ship's buy dialog is byte-identical.

### Stage 2 — Reveal state machine ✅ DONE
`$active` + `$revealedTeams`, note keys `Disguised`/`Undisguised`/`revealedNow`/`revealedNextTurn`, `ShadingField`-style note sorting, destroyed/deactivated checks, client toggle per D8, and the blueprint-only checks:
- proximity sweep at Movement advance (5 / 2 hexes, LoS-aware), plus a Deployment-advance pass for turn-1 deployments inside 5 hexes;
- **thrust plausibility (D6b)** at Movement advance;
- **ELINT / EW plausibility (D6c)** at Initial Orders advance — `generateIndividualNotes` case 1, the Hyach-sub hook.

All three need only the disguise class blueprint, not the phantom sheet, so they belong here rather than with the weapon check.

*Test:* two accounts. State flips at 5 hexes, at 2 for a fighter, on killing the array, on switching it off, on out-accelerating the simulacrum — and never flips back. Turn 1 and a stationary ship do not trigger the thrust check; a real Involuntary Acceleration crit does not self-reveal. For D6c: disguised as a non-ELINT ship, 1 point of SOEW flips it (E1) while any amount of DEW alone does not; disguised as another ELINT ship, ELINT ops are free until total non-DEW EW passes the simulacrum's ceiling (E2). A Dargan left on `None` runs no checks at all. Team A revealing does not reveal team B. Nothing is visibly disguised yet.

### Stage 3 — The identity swap ✅ DONE
`BaseShip::stripForJsonDisguised()` per §3 (systems still from the **static blueprint**, pristine — the phantom arrives in Stage 4), `TacGamedata::applyChameleonDisguise()` from `deleteHiddenData()`, `chameleonRevealTag` + reload (D14).
*(See the Stage 3 decisions at the top for the four places the build differs from this.)*

*Test:* enemy sees a Demos icon, ship window, point cost and notes; owner and teammates see the Dargan. `window.staticShips` on the enemy page has no `Dargan` key. Enemy closes to 5 hexes → reload → real Dargan with correct armour/arcs everywhere.

### Stage 4 — The phantom sheet ✅ DONE
Phantom construction (`-realId`, D2), the gated negative-shipid loaders, `stripForJsonDisguised` switching to phantom systems + phantom damage + phantom-derived `combatValue`. ~~phantom power synthesis (D12)~~ — dropped, premise false (see Stage 4 decisions above).
No mirrored resolution yet — the phantom exists and persists but is undamaged. This stage is about proving the plumbing: build → persist → reload → serve.

*Test:* `c:\tmp\css_stage4.php` (19) does exactly the plan's test — inserts `tac_damage` rows at `shipid = -N` and confirms they render on the enemy's Demos, are invisible to the owner, accumulate across a reload, mark a simulacrum system destroyed **without** destroying the real ship, never reach `$gamedata->ships`, and move `combatValue` on the enemy's sheet alone (71.2 vs 100). Plus `css_stage4_gate.php` (12) — one phantom per disguised ship and the gate provably OFF in a game with none.

> **Test trap:** pick a **Structure** (or weapon) for the combat-value assertion, never a Reactor or C&C. `calculateCombatValue` weights core systems at multiplier **0** by design ("functionality loss of key systems is noted" separately), so damaging one moves the CV not at all — which reads as a broken phantom when it is nothing of the kind.

### Stage 5 — Mirrored resolution ✅ DONE
One roll, one damage amount, two allocations (D3). Fake-profile `needed` on the server (D4). Called-shot translation (D9). Divergent-destruction clamp + reveal (D3a).

*Test:* `c:\tmp\css_stage5.php` (26). Measured on game 4273: real Dargan profile **16** vs simulacrum **14**; 15 of 20 simulacrum system ids translate to a same-class real system and 5 correctly become uncalled shots; **the untranslated id would have hit the wrong system 15 times**; 12 damage allocates on both sheets; phantom rows reach `getNewDamages` carrying `shipid = -realId`; and a phantom driven to destruction while the real ship lives is clamped and writes `revealedNow/Team:2`.

#### Stage 5 decisions that differ from the text above

1. **The mirror seam is `Weapon::damage()`, not `Firing::fire()`.** §D3 named `Firing::fire` to avoid the "dozens of subclasses" that override `Weapon::fire()` — but wrapping there would re-roll both to-hit *and* damage, which is precisely what D3 forbids. `damage()` receives the already-rolled amount, which is exactly the "one roll, two allocations" shape, and it has only **two** implementations (`Weapon` and `MatterCannon`). Split into `damage()` (entry point + mirror) and `damageOneSheet()` (allocation strategy), so the mirror is written once and both strategies inherit it. The 35 `beforeDamage` overrides are unaffected — they all reach `damage()`.
2. **The fire order must be saved and restored around pass 2.** It is one shared object threaded through the whole resolution and then persisted, so anything pass 2 leaves on it corrupts the REAL shot's record: `chosenLocation`, `linkedHit`, `armorIgnored`, `notes`, `pubnotes`, `updated`, `calledid`. `chosenLocation` is *cleared* rather than copied — the sheets have different sections and the phantom must pick its own.
3. **`getNewDamages()` had to be taught about phantoms — §D2's claim was half true.** `assignDamageReturnOverkill` does stamp `$target->id` and the negative id does persist correctly, but `getNewDamages()` walks `$gamedata->ships`, which the phantom is deliberately not in. Without the added sweep the mirrored damage was allocated in memory, rendered once, and silently lost on the next load.
4. **The two sheets are NOT expected to absorb identical totals.** They take the same incoming points and diverge by their own armour (measured: 19 vs 18 for a 12-point hit). An acceptance test asserting equality is testing the wrong invariant.
5. **`FireOrder::$chameleonCalledId`** carries the id the shooter actually aimed at, so pass 2 can land the called shot where the enemy called it while pass 1 uses the translated real id.

#### Stage 5 playtest fixes (game 4273) — three bugs found in play, all fixed

1. **The phantom must carry the real ship's `movement`.** Every geometric read goes through it, and
   **`getFacingAngle()` returns 0 for an empty movement list instead of failing** — so a phantom
   without movement resolved every incoming shot as though facing north at the origin, and picked a
   plausible but WRONG hit section (a laser that hit the real hull's starboard landed on the
   simulacrum's aft). Fixed in `buildChameleonPhantom()`.
2. **The payload must never carry the phantom's negative `shipid`.** That id is a persistence detail
   (D2). `combatLog.js:290` does `gamedata.getShip(d.shipid)` and hands the result straight to
   `sufferedCritThisTurn()`, which dereferences `.criticals` — so a negative id threw a TypeError
   that **killed the whole combat log**: an enemy who fired at a disguised ship saw no entry for
   their own shot, and Replay crashed. Fixed by `reassignChameleonSheetIds()`, plus a
   `if (!system) continue;` guard in `combatLog.js`.
3. **DEW must be resolved off the simulacrum too (D4, extended).** The client never reads the stored
   DEW entry — `ew.getDefensiveEW()` is an alias for `getEWLeft()`, i.e. *simulacrum capacity minus
   non-DEW spend*. A Dargan running OEW 8 + DEW 2 inside a 9-EW Demos displayed `DEW 1` while the
   server resolved against `2` (103% vs 99%). `BaseShip::getDisguisedDEWFor()` now mirrors the
   client formula term for term. **§6's "test total EW against the ceiling" was considered and
   rejected: a 10-EW Dargan would be exposed inside almost every Centauri hull**, so E2 stays
   `getAllEWExceptDEW`. Accepted consequence: a smaller-sensor simulacrum genuinely costs defensive
   EW, a larger one gains it — the same bargain D4 strikes on the defence profile.

> **Not ours:** a residual ±1 on hit chance is a pre-existing engine nit. Server `round($goal * 5)`
> = `103.5` → 104; client `Math.round(goal / 20 * 100)` = `103.49999999999998` → 103. Same
> arithmetic, two spellings, one ULP apart at an exact half. It affects every ship in the game.

### Stage 6 — Fire orders from the disguised ship (D7) + weapon-plausibility reveal (D6)
Weapon remapping for outgoing orders, `notes`/`pubnotes` scrub, class-presence mismatch test at Firing advance writing `revealedNextTurn`. Client-side warning at declaration ("firing this weapon will expose your disguise next turn") is the natural companion and belongs here.

*Test:* Dargan-as-Demos fires a Twin Array (Demos has them) → no reveal, enemy log shows a Demos Twin Array. Fires a Battle Laser → enemy log shows the substitute weapon this turn, disguise drops at the start of the next turn.

### Stage 7 — Dual-threshold resolution (D3b) — **the disguise must not make the real ship harder to hit**

User's ruling, 2026-08-01: the CSS is an information weapon, not a defensive one. A Dargan (profile
16) wearing a Demos (14) is currently hit *less often than a Dargan*, and that bonus is too large to
allow — so the single-threshold model has to go.

The shape:

```
one roll                     : $rolled = Dice::d(100)              (unchanged, public and true)
neededReal = real profile    + real DEW        -> governs the REAL hull
neededFake = simulacrum      + simulacrum DEW  -> governs the PHANTOM   (what the enemy previews)
   $rolled <= neededReal  -> pass 1 allocates on the real ship
   $rolled <= neededFake  -> pass 2 allocates on the phantom
```

Both directions then work by construction, which is what the current model cannot do:
- `neededReal > neededFake` (small simulacrum): a roll in between **hits the real ship while the
  enemy watches their shot miss** — the deception paying off, with no defensive discount.
- `neededFake > neededReal` (large simulacrum): the phantom takes a hit the real hull does not. The
  phantom is a fiction; that is exactly its job.

What it costs, and the traps already known:
- **Two `shotshit` counts.** `tac_fireorder` stores one. Put the REAL values in the DB (authoritative
  for the real ship) and the fake pair in a note keyed by fireorderid, per D3b's original sketch.
- **A NEW per-viewer masking site.** Fire orders live on the SHOOTER's weapon, and the shooter is
  usually not disguised — so this is masking keyed on the *target* being disguised from the viewer,
  which no existing site does. It belongs in the §3 leak audit and the info-bleed map.
- `Weapon::fire()` currently increments `$fireOrder->shotshit` once inside a single
  `if ($rolled <= $needed)`. That block is the seam; the mirror in `damage()` stays where it is but
  becomes conditional on its own threshold rather than riding pass 1's.
- D4 keeps the simulacrum profile for `neededFake` **only**. `neededReal` must go back to the real
  profile and the real stored DEW — i.e. `getDisguisedProfileFor()` / `getDisguisedDEWFor()` become
  inputs to the fake threshold, not overrides of the only one.

### Stage 8 — Arming mask (D11) **for the ship's own payload** — the disguised payload already has it (Stage 3, decision 4). What remains is a CSS ship the enemy sees as *itself*: `None`, or revealed. Then optional refinements: weapon count/arc mismatch (D6 B/C), phantom criticals (D3c).

---

## 5. Files touched

**New:** `ChameleonSensors` (PHP, next to `ElintScanner` in [baseSystems.php](source/server/model/systems/baseSystems.php)) + `ChameleonSensors` (JS, [baseSystems.js](source/public/client/model/system/baseSystems.js)). Two `autoload.php` lines **for the user to add**.

**Modified:**
- [dargan.php](source/server/model/ships/centauri/dargan.php) — system swap, notes fix
- [ShipClasses.php](source/server/model/ships/ShipClasses.php) — `hasChameleonSensors()`, `stripForJsonDisguised()`, `getDisguisedProfileFor()`, phantom construction + weapon map, reveal-sweep hook
- [TacGamedata.php](source/server/model/TacGamedata.php) — static gate, `applyChameleonDisguise()`
- [DBManager.php](source/server/controller/DBManager.php) — null guards, gated phantom damage/critical loaders, phantom build step
- [firing.php](source/server/handlers/firing.php) — mirrored resolution wrapper, called-shot translation, weapon-plausibility check
- [movement.php](source/server/handlers/movement.php) — thrust-plausibility helper (D6b), alongside the existing `canAccelerate` / `doStuckEngine` acceleration maths
- [weapon.php](source/server/model/weapons/weapon.php) — profile override, arming mask
- [Enhancements.php](source/server/model/ships/Enhancements.php) — `CHAM_DISG`, `None` default
- [Manager.php](source/server/controller/Manager.php) — saved-fleet `enhname` fix
- [confirm.js](source/public/client/UI/confirm.js) — choice-type enhancement widget (×4 loops), `None` as the first option
- [weaponManager.js](source/public/client/weaponManager.js) — declaration-time warning (Stage 6)
- [gamedata.js](source/public/client/gamedata.js) / [ajaxInterface.js](source/public/client/ajaxInterface.js) — reveal-tag reload; `gamedata.js` also carries one of the scanner-name lookups (D0)
- [ships.js](source/public/client/ships.js) or [systems.js](source/public/client/systems.js) — `getScannerList()` helper (D0)
- [lobbyEnhancements.js](source/public/client/lobbyEnhancements.js) (×3), [model/system/baseSystems.js](source/public/client/model/system/baseSystems.js), [model/weapon/customTrek.js](source/public/client/model/weapon/customTrek.js) (×2) — scanner lookups routed through the helper (D0)

**Read-only dependencies:** `EW::getScannerOutput` ([EW.php:42](source/server/handlers/EW.php#L42)) and `BaseShip::getAllEWExceptDEW` ([ShipClasses.php:2020](source/server/model/ships/ShipClasses.php#L2020)) are both used unchanged by D6c.

**Untouched by design:** the DB schema, `tac_damage`/`tac_critical` write paths, criticals resolution, movement resolution, the React UI.

---

## 6. Risks & open questions

1. **Scope.** The phantom sheet roughly doubles this feature. Stages 0–3 are a complete, shippable "cosmetic disguise"; Stages 4–5 are the mirror sheet; Stage 6 is the firing behaviour. Each is independently useful, and the cut line after Stage 3 is real if the mirror proves troublesome.
2. **Negative shipids are load-bearing.** Anything that iterates `tac_damage`/`tac_critical` without going through `getShipById` (reporting, admin tools, replay exports, the combat-log printer) must tolerate them. Audit for direct queries before Stage 4.
3. **Two known, accepted tells** (both resolved, both revisitable): the phantom never rolls criticals (D3c), and initiative/turn delay are sent real (D13).
4. **Thrust-check false positives** (D6b) are the likeliest source of "why did my disguise just drop?" complaints, with the EW-ceiling check (D6c/E2) close behind — a player who habitually maxes OEW will trip it on turn 1 without understanding why. Hence the deliberately generous thresholds and the forced-movement exclusion. Both want a playtest pass, and the CSS tooltip should state the simulacrum's actual EW ceiling and thrust limit **in plain numbers** so the player can plan around them rather than discover them.
5. **The ELINT-type list in D6c/E1 is hardcoded** (`SOEW`, `SDEW`, `BDEW`, `DIST`, `JAM`). Any future ELINT-only EW type must be added to it or it becomes a silent hole in the reveal. Deriving the set instead of listing it is not currently possible — EW types are bare strings with no registry.
6. **Losing `"ELINT"` on the CSS is a silent, total regression** (D0): the Dargan keeps working, just without half its purpose, and no error is raised. Worth an explicit assertion in the Stage 0 test rather than an eyeball check.
7. **Enhancement tuple index 7** is a new convention. Additive and default-empty, but it touches the shared buy dialog — the regression surface is *every* ship's purchase flow. Test the four loops deliberately, including that an untouched dialog submits `None`.
8. ~~**Does the disguise cost points?**~~ **CLOSED 2026-07-31: no, 0 points.**
9. **Finished-game replay** (D15).
