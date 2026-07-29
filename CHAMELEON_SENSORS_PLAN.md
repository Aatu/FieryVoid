# Chameleon Sensor Suite (CSS) — Implementation Plan

Implement the B5W Chameleon Sensors rules on the Centauri **Dargan Strike Cruiser**: an ELINT array that additionally disguises its ship as a different vessel, so that enemy players see a *different unit* on the map, in the ship window, in their targeting maths and **in the damage it appears to take** — until the deception is broken.

> Test unit: `Dargan` ([dargan.php](source/server/model/ships/centauri/dargan.php)) — currently carries the note *"Chameleon Sensors (no effect in game)."*
> Test container: gameID `3730` (`TacGamedata::$safeGameID`) or a fresh Docker game. Needs **two accounts on opposing teams** — every behaviour in this plan is invisible from the owning seat.

---

## 0. Guiding constraints

- **Server-side masking only.** The client is fully inspectable (`window.gamedata`, devtools). Anything the enemy's browser receives is public knowledge. The disguise must be applied while building the outgoing per-player JSON, *never* by hiding things client-side.
- **Hard gating.** One ship in ~2000 has this system. The whole feature sits behind a single per-load boolean; a game with no CSS ship pays for one `false` check. No per-ship, per-system or per-shot work in the common path.
- **`autoload.php` entries are added by the user**, never by the assistant. Two new classes are expected (`ChameleonSensors` PHP + JS).
- **No bundle commits** (`game.legacy.bundle.js` / `gamelobby.legacy.bundle.js` / `UI.bundle.js` are build artefacts).
- **Positional system ids.** `ChameleonSensors` must replace `ElintScanner` *in place* at [dargan.php:35](source/server/model/ships/centauri/dargan.php#L35) — same `addPrimarySystem` position, same constructor arity — or every stored `systemid` for that ship shifts.
- **Static JSON regen** after any class-default change: `php generateStaticShipFile.php` inside the php container.
- Each stage is a commit boundary and independently testable.

---

## 1. Current-state audit — what already exists that this can ride on

Verified against the working tree, 2026-07-29:

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

---

## 2. Design decisions

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
shipSizeClass 0..3, and NOT: FighterFlight, $base, $smallBase, $osat, $mine, Terrain, $Enormous
same faction as the disguising ship          (see D10a)
excluding the ship's own phpclass
```

**D10a — Same faction only (recommended).** The rules permit any type, but FV colours icons by *team*, so an enemy-faction disguise renders in the wrong colour and defeats itself. Same-faction also keeps the blueprint preload cheap. Widen later behind a flag.

**D10b — Storage: `numbertaken` must carry the choice.** Because of finding #15, encode the selection as a **1-based index into the legal list sorted by phpclass**, *and* store the phpclass in `enhname`. On load prefer the name, fall back to the index. Add the two-line fix at [Manager.php:912](source/server/controller/Manager.php#L912):
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
| `systems` | **yes** (names, arcs, ELINT presence, damage, fire orders) | the **phantom's** systems, carrying the phantom's damage (D1) |
| fire orders on those systems | **yes** — real weapon ids, real notes | remap per D7 |
| `notes` | **yes** — currently literally says *"Chameleon Sensors"* | the fake class's `notesFill()` output |
| `enhancementTooltip`, `pointCostEnh` | **yes** (spend + the disguise line) | drop / send 0 |
| `combatValue` | leaks real damage state | compute from the **phantom** |
| `iniative`, `unmodifiedIniative`, `iniativeadded`, `currentturndelay` | mildly | **send real** (D13) |
| `movement` | must stay real (position/facing are observable) | pass through |
| `EW` | real EW is observable in play | pass through |
| `name` | player-chosen | pass through — warn the player in the buy dialog |
| `id`, `userid`, `team`, `slot`, `slotid` | needed for targeting/teams | pass through (the phantom wears the **real** id) |
| `destroyed`, `unavailable`, `spawned`, `removed` | destruction ends the deception anyway | pass through |
| `hangarRequired`, `hasAttached`/`attached`, `skinDancing`, `integratedFighterCount` | situational | drop unless the fake class would have them |

---

## 4. Stages

### Stage 0 — Baseline (shippable alone)
1. Fix the dangling-`.` notes concatenation at [dargan.php:28-33](source/server/model/ships/centauri/dargan.php#L28-L33).
2. Null-guard the damage and critical loaders (finding #7) — defensive, independent of this feature.
3. `ChameleonSensors extends ElintScanner` (PHP): `$name = "chameleonSensors"`, `$displayName = "Chameleon Sensors"`, `$specialAbilities[] = "ChameleonSensors"`, rules text in `setSystemDataWindow`. Same ctor signature.
4. Matching JS class in [baseSystems.js](source/public/client/model/system/baseSystems.js) — required, `createSystemFromJson` does `new window[Name]`.
5. Swap into [dargan.php:35](source/server/model/ships/centauri/dargan.php#L35) **in place**; regenerate static ships.
6. `BaseShip::hasChameleonSensors()` + the `TacGamedata` static gate (false for every existing game).

*Test:* Dargan loads, ELINT behaviour unchanged, new system in the ship window, nothing else moves.

### Stage 1 — Buy-time disguise choice
`CHAM_DISG` option (D10), choice list from `ShipLoader::getAllShips($faction)`, `<select>` widget in all four `confirm.js` loops (finding #18), `setEnhancementsShip` case storing `$ship->chameleonDisguiseClass`, saved-fleet fix (D10b).

*Test:* buy a Dargan, pick "Demos", save the fleet, reload it, start the game — the choice survives lobby → DB → game-load. Every other ship's buy dialog is byte-identical.

### Stage 2 — Reveal state machine
`$active` + `$revealedTeams`, note keys `Disguised`/`Undisguised`/`revealedNow`/`revealedNextTurn`, `ShadingField`-style note sorting, destroyed/deactivated checks, client toggle per D8, and both Movement-advance checks:
- proximity sweep (5 / 2 hexes, LoS-aware), plus a Deployment-advance pass for turn-1 deployments inside 5 hexes;
- **thrust plausibility (D6b)** — needs only the disguise class blueprint, not the phantom sheet, so it belongs here rather than with the weapon check.

*Test:* two accounts. State flips at 5 hexes, at 2 for a fighter, on killing the array, on switching it off, on out-accelerating the simulacrum — and never flips back. Turn 1 and a stationary ship do not trigger the thrust check; a real Involuntary Acceleration crit does not self-reveal. Team A revealing does not reveal team B. Nothing is visibly disguised yet.

### Stage 3 — The identity swap
`BaseShip::stripForJsonDisguised()` per §3 (systems still from the **static blueprint**, pristine — the phantom arrives in Stage 4), `TacGamedata::applyChameleonDisguise()` from `deleteHiddenData()`, `chameleonRevealTag` + reload (D14).

*Test:* enemy sees a Demos icon, ship window, point cost and notes; owner and teammates see the Dargan. `window.staticShips` on the enemy page has no `Dargan` key. Enemy closes to 5 hexes → reload → real Dargan with correct armour/arcs everywhere.

### Stage 4 — The phantom sheet
Phantom construction (`-realId`, D2), the gated negative-shipid loaders, `stripForJsonDisguised` switching to phantom systems + phantom damage + phantom-derived `combatValue`, phantom power synthesis (D12).
No mirrored resolution yet — the phantom exists and persists but is undamaged. This stage is about proving the plumbing: build → persist → reload → serve.

*Test:* hand-insert a `tac_damage` row at `shipid = -N` and confirm it renders on the enemy's Demos and is invisible to the owner, survives a reload, and never reaches `$gamedata->ships`.

### Stage 5 — Mirrored resolution
The `Firing::fire()` wrapper (D3): one roll, one damage amount, two allocations. Fake-profile `needed` on the server (D4). Called-shot translation (D9). Divergent-destruction clamp + reveal (D3a).

*Test:* fire a volley at a disguised Dargan. Owner's log: real systems, real damage. Enemy's log: the Demos taking the same shot count and comparable totals on Demos systems. Hit chance preview equals resolved `needed` on the enemy client — **this is the acceptance test for the stage**. Damage to the real ship is byte-identical to a run with the CSS switched off.

### Stage 6 — Fire orders from the disguised ship (D7) + weapon-plausibility reveal (D6)
Weapon remapping for outgoing orders, `notes`/`pubnotes` scrub, class-presence mismatch test at Firing advance writing `revealedNextTurn`. Client-side warning at declaration ("firing this weapon will expose your disguise next turn") is the natural companion and belongs here.

*Test:* Dargan-as-Demos fires a Twin Array (Demos has them) → no reveal, enemy log shows a Demos Twin Array. Fires a Battle Laser → enemy log shows the substitute weapon this turn, disguise drops at the start of the next turn.

### Stage 7 — Arming mask (D11), then optional refinements: weapon count/arc mismatch (D6 B/C), phantom criticals (D3c), dual-threshold resolution (D3b).

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
- [Enhancements.php](source/server/model/ships/Enhancements.php) — `CHAM_DISG`
- [Manager.php](source/server/controller/Manager.php) — saved-fleet `enhname` fix
- [confirm.js](source/public/client/UI/confirm.js) — choice-type enhancement widget (×4 loops)
- [weaponManager.js](source/public/client/weaponManager.js) — declaration-time warning (Stage 6)
- [gamedata.js](source/public/client/gamedata.js) / [ajaxInterface.js](source/public/client/ajaxInterface.js) — reveal-tag reload

**Untouched by design:** the DB schema, `tac_damage`/`tac_critical` write paths, criticals resolution, movement resolution, the React UI.

---

## 6. Risks & open questions

1. **Scope.** The phantom sheet roughly doubles this feature. Stages 0–3 are a complete, shippable "cosmetic disguise"; Stages 4–5 are the mirror sheet; Stage 6 is the firing behaviour. Each is independently useful, and the cut line after Stage 3 is real if the mirror proves troublesome.
2. **Negative shipids are load-bearing.** Anything that iterates `tac_damage`/`tac_critical` without going through `getShipById` (reporting, admin tools, replay exports, the combat-log printer) must tolerate them. Audit for direct queries before Stage 4.
3. **Two known, accepted tells** (both resolved, both revisitable): the phantom never rolls criticals (D3c), and initiative/turn delay are sent real (D13).
4. **Thrust-check false positives** (D6b) are the likeliest source of "why did my disguise just drop?" complaints. Hence the deliberately generous threshold and the forced-movement exclusion — but it wants a playtest pass, and the CSS tooltip should state the limit in plain numbers so the player can plan around it.
5. **Enhancement tuple index 7** is a new convention. Additive and default-empty, but it touches the shared buy dialog — the regression surface is *every* ship's purchase flow. Test the four loops deliberately.
6. **Does the disguise cost points?** Assumed no — the CSS is baked into the Dargan's 750. Confirm before Stage 1; changing it later invalidates saved fleets.
7. **Finished-game replay** (D15).
