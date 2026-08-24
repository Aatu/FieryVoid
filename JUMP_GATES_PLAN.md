# Jump Gates — B5W Fixed Gate Mechanics (Jump Points, Phase 2)

Follow-up to [JUMP_POINTS_PLAN.md](JUMP_POINTS_PLAN.md) §7. Phase 1 (Stages 1–6 + §9) is BUILT and
deployed; this plan is the second half it was designed not to paint into a corner.

**Read JUMP_POINTS_PLAN.md first.** Everything in its §2.2 (the facing rule), §3.2 (the vortex
unit), §3.3 (vortex state persistence), §3.4 (the jump-out movement order) and §5 (traps) applies
here unchanged and is not restated. This plan records only what a FIXED GATE adds or changes.

The promise §7 made — *"Phase 1's vortex unit is designed to be exactly what a gate opens, so
nothing here is wasted"* — holds. `SpawnJumpPoint`, `Movement::resolveJumpOuts`, `getUsableVortex`,
the entry-direction rule, the removal-not-destruction lifecycle and the whole replay story are
reused with **zero** changes. What is new is: who may open one, where the declaration lives, how a
contested claim is settled, and the gate's own damage model.

⭐ **THE GATE UNIT IS `JumpgateCapital`** ([terrain/JumpgateCapital.php](source/server/model/ships/terrain/JumpgateCapital.php),
added by the user 2026-08-23 — the official AoG gate), **and it is the only one.**

⚠️ **`JumpgateNew` (terrain) and `Jumpgate` (civilians) are OBSOLETE and explicitly OUT OF SCOPE**
(user ruling 2026-08-23). Both still mount a `JumpEngine`, so both will turn up in a grep; neither
gets `markGate()`, neither is retired here, and no stage below touches either file. If a game
already contains one it keeps whatever Phase 1 behaviour it has today.

---

## 0. Build status

| Stage | State |
|---|---|
| 1 — `markGate()` and the gate blueprint | ✅ **BUILT** 2026-08-23 |
| 2 — the two submit widenings | ✅ **BUILT** 2026-08-23 |
| 3 — declaration: the button, the panel, the validation | ✅ **BUILT** 2026-08-23 |
| 4 — resolution, the hold, the lifecycle | ✅ **BUILT** 2026-08-23 |
| 5 — polish | ✅ **BUILT** 2026-08-23 (the gate tooltip; the `convoyRaid` playthrough is §6's job) |

`fvbuild.ps1 -Check` green after each: autoload map current (no new classes — `markGate()` and the
gate branches are all flags and methods on existing ones), ship-data validator **0 new errors**,
replay harness **160 passed / 1 failed** where the 1 is game 4309, **confirmed pre-existing on a
clean tree** by `git stash push -- source/server` + re-run (byte-identical diff: a movement/phase
drift, nothing to do with gates). Statics regenerated after Stages 1 and 5 — Stage 5 because the
tooltip text rides the STATIC BLUEPRINT, see below. Legacy bundles rebuilt (`node
scripts/bundle-legacy.js`, minified as `yarn build` leaves them).

**Deliberate departures from the stage list, recorded here so the plan and the tree agree:**

1. **`gamedata.isJumpGate(unit)` landed in Stage 2, not Stage 3.** Stage 2's client widening has to
   answer "is this unit a jump gate?" before it can decide to POST it, so the predicate is a Stage 2
   dependency. `gamedata.canSignalJumpGate(gate)` is Stage 3's, as planned.
2. **`RammingAttack` belongs on Stage 2's `generateIndividualNotes` audit list.** The stage names
   Reactor, CnC, Scanner, Hangar, JumpEngine and Structure; `BaseShip` also auto-adds a
   `RammingAttack`, and it is one of only TWO systems on the hull that override
   `generateIndividualNotes` at all (the other is `Hangar`). Both were audited and both are safe on
   a POST-side gate — `RammingAttack` is guarded on `!empty($ship->skinDancing)` (a base never
   skindances), and every one of `Hangar`'s note writers is behind either a null `pending*Transfer`
   or a first-time-empty guard that exists for precisely this POST-side-reconstruction case. The
   other nine systems inherit `ShipSystem`'s empty stub and write nothing.
3. ⭐ **Stage 3 CLAMPS an over-long claim rather than rejecting it.** The stage list says the gate
   branch validates "mode 1–4 **and ≤ maxHold**", while §2.4/§3.4 put the clamp at resolution and
   test 18 expects "a 4-turn claim is **clamped**, with a log line". Rejecting at submit would
   throw the player's whole claim away over a number the UI never offered them, which is not what
   test 18 describes. So `Firing::getGateSignalBlock` rewrites `firingMode` down to the cap (with a
   `Debug::log` line) and lets it through; the `min()` in `resolveGateClaims` stays as belt and
   braces, and the combat log gets its own "reactor damage caps it at N turns" line. The invariant
   the stage list asked for — every persisted claim is within the gate's cap — holds in the DB as
   well as in memory.
4. ⭐ **`getVortexRechargeTime()` exists instead of a `getLoadingTime()` override**, and this one
   was forced by the harness. Stage 4 says "`getLoadingTime()` … read[s] reactor damage". Overriding
   it broke **two replay-corpus games** (`loadingtime: 20 → 1` and `16 → 1`): `Weapon::setLoading`
   overwrites `$loadingtime` from the stored `tac_systemdata` row on every load, and in a game
   recorded before Phase 1 Stage 6 that row still holds the pre-Stage-6 value of `1`. `$delay` is
   the only field that says what the ship file asked for — which is exactly why Stage 6's
   `stripForJson` sent `max(1, (int)$this->delay)` rather than `$this->loadingtime`. The new method
   is that expression plus the gate's damage term, it is used at the four vortex charge sites, and
   `getLoadingTime()` — asked by the whole generic weapon-loading machinery, on every weapon, in
   every phase — is left completely alone.
5. **`BallisticIconContainer` needed more than the label.** The stage list names the
   `'Jump Gate Signalled'` label and the `jumppointArrow` suppression. A gate claim also carries a
   REAL `targetid` (the claimant's nearest unit), and every ballistic icon path reads that as "hang
   the marker on that unit": left alone it drew the marker over the **signalling ship** and ran a
   bright line from the gate to it — the map half of the very leak §2.1 forbids, on the claimant's
   own screen, before the server has masked anything. A `'jumppoint'` order is now treated as
   `targetid = -1` throughout `createBallisticIcon` and `createBallisticLineIcon`, and a gate signal
   draws no launch sprite (its launch hex *is* its target hex).

**Verified at Stage 2** with a forged POST through `Manager::getShipsFromJSON`: the client's
object-keyed `systems` payload resolves to the gate's Jump Engine and carries the order; the
server-side filter returns the claim for a gate and `null` for a forged mode-7 (MAINTAIN), a stale
turn, any phase but Initial Orders, and for `jumpgateNew` (which is not a gate — trap 12 holds). The
client half was exercised the same way: `isJumpGate` matches `JumpgateCapital` alone, and
`getGateSignalOrders` accepts modes 1–4 in phase 1 only.

**Verified at Stages 3–5** with a no-DB scratch harness over a constructed `JumpgateCapital`
(25 assertions, all green): the blueprint (gate engine, signal range 10, modes 1–4, pruned per-mode
arrays, silenced Reactor crit chart, facing arrow); the damage model — undamaged 4-turn hold and
20-turn recharge, **D=9 → recharge 23, hold still 4** (test 17), **D=30 → recharge 30, hold 2**
(test 18), and a full charge that tracks the *damaged* target rather than stalling one short of it;
modes — 5 and 7 refused, 3 accepted, `getMaintainDeclaration` null; and a **ship** Jump Engine
untouched on every one of those (not a gate, range 4, 7 modes, recharge unchanged).

**The Stage 5 tooltip is a STATIC BLUEPRINT artefact, and that shaped what it may say.**
`ShipSystem::stripForJson` does not send `$data` at all, so `setGateSystemDataWindow`'s text reaches
the client on the static blueprint — generated once, at build time, on an undamaged hull, on turn 1.
It therefore states the RULES and carries **no live numbers**: a "charge: now 7/20" line would be
frozen at whatever the generator saw and would read as a lie for the rest of the game. The live
charge is on the system icon (`turnsloaded`/`loadingtime`, which `stripForJson` does send), and the
open counter reads `N/hold` rather than `N/4` while a gate vortex stands. ⚠️ **Statics must be
regenerated whenever this text changes** — that is what the Stage-1 gap ("the gate printed the SHIP
vortex rules") actually was.

**What is left is play, not code.** §6's twenty-two scenarios want a real game — the two-seat
lifecycle, the contested claim from two seats, the concealment cases (15/16) from the enemy seat,
and the `convoyRaid` scenario end to end with a `JumpgateCapital` bought into its 1000-point gate
slot. §2.5's ten-box Jump Engine (each point of engine damage = a flat 10% chance of losing the
whole gate) is still the one thing that wants judgement rather than a ruling.

**One adjacent Phase 1 defect found and deliberately NOT fixed** (it predates this plan and is a
ship rule): `Firing::getVortexDeclarationBlock`'s *ship* charge test asks `getLoadingTime()`, which
in a game whose `tac_systemdata` row predates Phase 1 Stage 6 reads `1` — so a ship declaration is
lenient there where the gate branch is not. Departure 4 above explains the mechanism. Left alone
under scope discipline; worth its own line if it ever matters.

---

## 1. What exists today

| Piece | Where | Behaviour |
|---|---|---|
| ⭐ `JumpgateCapital extends BaseShip` | [terrain/JumpgateCapital.php](source/server/model/ships/terrain/JumpgateCapital.php) | **The gate.** `pointCost` 10, faction Terrain, `shipSizeClass = 5` set by hand so `isTerrain()` is true while the hull keeps BaseShip's four side Structures. `base` + `smallBase` + `nonRotating`, `Enormous = false`. Mounts `Reactor(6,50,0,0)`, `CnC`, `Scanner`, `Hangar`, **`JumpEngine(8, 10, 20, 20)`**, four 200/240 side Structures + `Structure(3,160)` primary |
| The `convoyRaid` scenario | [createGame.js:666](source/public/client/UI/createGame.js#L666) | A 1000-point "Jumpgate" **slot** on team 1 — a name and a deployment zone, not a hull binding, so it needs no change: the player buys a `JumpgateCapital` into it |
| `SpawnJumpPoint` | [terrain/SpawnJumpPoint.php](source/server/model/ships/terrain/SpawnJumpPoint.php) | The vortex unit. **A gate opens exactly this**, unchanged |
| `JumpEngine::openVortex` | [baseSystems.php:5639](source/server/model/systems/baseSystems.php#L5639) | Spawn + deploy `MovementOrder` + `'Vortex'` note. The gate path is a sibling entry point, not a copy |
| `JumpEngine::getVortexRechargeLoad` | [baseSystems.php:5359](source/server/model/systems/baseSystems.php#L5359) | Derived charge state off the vortex note. **The gate's 20-turn recharge is already this, for free** |
| `JumpEngine::markLegacy()` | [baseSystems.php:5287](source/server/model/systems/baseSystems.php#L5287) | §9's flag-not-a-subclass precedent. `markGate()` copies its shape exactly |

⭐ **The gate's engine is already `JumpEngine(8, 10, 20, 20)` and that 4th argument is already the
recharge time.** Stage 6 gave `$delay` the meaning "turns to charge" and `getVortexRechargeLoad`
derives the whole state from the vortex note. §7's *"20-turn recharge afterwards"* therefore needs
**no new code at all** — it is what the existing constructor argument already says.

---

## 2. The FV ruleset for gates (unambiguous statement)

### 2.1 Signalling

- Declared in **Initial Orders**, by **clicking the gate**. No ship needs to be selected first.
- The player may signal if they have **at least one live, deployed, non-terrain unit within
  10 hexes** of the gate. ⭐ **Which unit does not matter** and is never chosen by the player
  (user ruling 2026-08-23) — the requirement is "you have a unit in range", not "this ship signals".
  Distance is measured to the player's **nearest** qualifying unit, because that is the number the
  contested-claim rule needs.
- ⭐ **NO LINE OF SIGHT IS REQUIRED** (user ruling 2026-08-23). Unlike a ship projecting its own
  vortex (Phase 1 §2.1), signalling a gate is a transmission, not an aimed effect. Nothing in the
  validation chain runs `mathlib.isLoSBlocked` or `$gamedata->blockedHexes`, on either side.
- ⭐ **SIGNALLING NEVER REVEALS A HIDDEN UNIT** (user ruling 2026-08-23). A stealthed, shaded or
  cloaked ship may signal a gate and keeps its concealment. This is the **opposite** of the Phase 1
  rule for a ship opening its own vortex, and it is deliberate: nothing about the gate declaration
  points at the signaller. See §3.3 — it is free on the server and costs exactly one masking line
  on the payload.
- The declaration carries a **programmed open duration of 1–4 turns**. It cannot be changed
  afterwards; there is no Maintain.
- One claim per player per gate per turn. A player with two claims on one gate keeps the first
  (same rule and same reason as Phase 1's one-vortex-per-ship: scanning the whole array would
  reject the first and keep the last, which is the wrong way round).
- Any player may signal any gate, including one bought by the enemy. That is the whole point of the
  contested-claim rule below.
- The gate must not already hold an open vortex, and must be **fully charged**
  (`getVortexRechargeLoad($turn) >= getLoadingTime()`).

### 2.2 The facing — there is nothing to choose, and that is the design

> ⭐ **THE VORTEX FACING IS ALWAYS THE GATE'S OWN FACING, AND CANNOT BE CHANGED** (user ruling
> 2026-08-23). It cannot be projected, aimed or re-aimed. The gate's mouth is the doorway.

> ⭐ **AND THE GATE'S FACING IS SET WHEN THE GATE IS PLACED, FOR THE REST OF THE GAME** (user ruling
> 2026-08-23). **There is therefore NO facing control anywhere in this plan** — no `UI.vortexFacing`
> for gates, no deployment facing ring, nothing. The gate is a fixed installation and its mouth is
> part of where it was put.

Everything else about entry is JUMP_POINTS_PLAN.md §2.2 verbatim: a unit must be **travelling in
direction `D = (F + 3) % 6`** on the step that carries it into the gate's hex, direction 0 is EAST
and increases clockwise. `getUsableVortex`, `getEntryDirection` and `Movement::applyJumpOut` need no
gate branch at all.

**What that means concretely, so nobody is surprised at the table.** A bought unit's facing comes
from [BuyingGamePhase.php:75/85](source/server/Phase/BuyingGamePhase.php#L75): **left-side teams
face 0 (east), right-side teams face 3 (west)**, and `nonRotating` means it stays there
([movement.js:928 canRotate](source/public/client/movement.js#L928) refuses it). So:

| Gate bought by | Gate faces | A unit must enter travelling | Reads as |
|---|---|---|---|
| a left-side (odd) team | 0 — east | direction 3, west | back toward its own edge |
| a right-side (even) team | 3 — west | direction 0, east | back toward its own edge |

⭐ **This falls out right.** Whoever uses the gate flies back the way they came, which is what
withdrawing through a jump gate should feel like — and an attacker escaping through the defender's
gate has to break off and run the same way. The facing is not arbitrary; it just is not chosen.

⚠️ **The gate itself draws NO mouth arrow** (user ruling 2026-08-24). It was built carrying
`$facingArrow` on the reasoning that a facing the player cannot pick must be readable off the map —
but a permanent yellow arrow over every gate, all game, read as clutter, and the 200px gate art
already points. **The arrow belongs to the jump point alone**: `SpawnJumpPoint` still declares
`$facingArrow`, so the mouth is drawn exactly when there is a vortex to fly into. The gate is
Terrain, so it gets no ordinary prow/heading arrows either — `ShipIcon.create` builds those only for
non-terrain — which means a gate now draws no arrow of any kind.

### 2.3 Forming, open, closing

Identical to §2.3 of the Phase 1 plan, with one substitution: **the programmed duration replaces
the Maintain declaration**, and the four-turn cap becomes the reactor-damaged cap.

| Turn | State | Can units enter? | What is on the board |
|---|---|---|---|
| N — signalled in Initial Orders | **Forming** | No | a yellow **"Jump Gate Signalled"** hex on the gate's hex |
| End of N | **Activation.** Damaged-engine failure roll happens here | — | — |
| N+1 … N+`hold` | **Open** | Yes | the vortex unit, over the gate |
| End of N+`hold` | **Closes.** Unconditionally | — | — |

`hold` is the programmed duration, 1–4, clamped down by reactor damage (§2.5). ⭐ **The turn it was
signalled is not one of the open turns** — the same rule and the same off-by-one as Phase 1.

A gate vortex ALSO closes at end of turn if:

1. The gate is destroyed (by fire, or by total reactor loss).
2. The vortex unit is gone (defensive; a recycled ship id or deleted game data).

⭐ **And by NOTHING ELSE** (user rulings 2026-08-23). Specifically **not** by:
- **no Maintain declaration** — a gate has no Maintain. The duration is programmed once.
- **the gate's systems being online** — a gate does not have to go dark to hold its own jump point
  open. `getVortexPowerViolations` is not consulted for a gate.
- **the signaller moving away, dying, or leaving through the vortex itself.** Once signalled the
  gate holds it. There is no signaller-range recheck at end of turn.

Closure is still **end of turn, after Firing** — a vortex closing this turn is usable for all of it.

⚠️ **This is the one place where doing nothing is a bug, not a default.** `closeExpiredVortices`
skips terrain, and `JumpgateCapital` is terrain. Trap 1 — confirmed as required work by the user
2026-08-23.

### 2.4 Contested claims

⭐ **NO OWNER PRIORITY** (user ruling 2026-08-23, superseding §7's "owner first"). A gate is
contested terrain, not a home-team asset. Resolution, in order:

1. **Nearest wins.** Each claiming player's distance is the distance from the gate to that player's
   **closest** qualifying unit (live, deployed, non-terrain, ≤ 10 hexes). Smallest wins.
2. **Roll off on a tie.** `Dice::d(100)` per tied claimant, highest wins; re-roll the tied subset,
   bounded at 10 rounds, then take the lowest player id so it can never hang.

The winner's programmed duration is used. Losers get a combat-log line saying they were beaten and
by how far; their claim costs them nothing (no charge is spent, nothing is on cooldown), so there is
no state to unwind.

⚠️ **Every log line names the PLAYER or the TEAM, never the signalling unit** — see §2.1's
concealment ruling and §3.3.

⚠️ **The roll must be persisted, not re-derived.** It happens once inside
`InitialOrdersGamePhase::advance` and its outcome is what the vortex notes record — the same
guarantee `rollVortexJumpFailure` already relies on. Never re-roll on load.

### 2.5 Gate damage — the reactor is the whole model

§7's rules, restated exactly. Let **D** = points of damage on the gate's Reactor
(`maxhealth - getRemainingHealth()`, the same measure `rollVortexJumpFailure` uses on the engine):

| Effect | Formula |
|---|---|
| Recharge time | `20 + floor(D / 3)` turns (base is the engine's 4th constructor argument) |
| Maximum programmable hold | `max(1, 4 - floor(D / 15))` turns |
| Total reactor loss (`D >= maxhealth`, i.e. 50) | **The gate is destroyed** |

**No other criticals** — the gate's Reactor rolls none. (Its `JumpEngine` already rolls none:
`$possibleCriticals = array()` since Stage 1 of Phase 1.)

⭐ **The jump-failure roll (§2.6 of the Phase 1 plan) IS kept for gates** (user ruling 2026-08-23 —
"No jump-failure roll" was offered as an exemption and deliberately not taken). A gate whose Jump
Engine is damaged rolls d100 ≤ (% of engine boxes lost) at end of the turn it opens a vortex; on a
failure the gate is destroyed and the vortex never forms. Only on the turn it OPENS — with no
Maintain declaration there is no second occasion to roll, so `rollVortexJumpFailure`'s existing
`(int)$this->vortexOpenTurn !== $turn && !$this->getMaintainDeclaration($turn)` guard already gives
exactly one roll per opening with no gate branch.

⚠️ **`JumpgateCapital`'s Jump Engine has TEN boxes.** The failure roll is a percentage of boxes
lost, so **each point of engine damage is a flat 10% chance of destroying the whole gate** the next
time it opens a vortex — 3 damage is a 30% loss, and the engine is on the d20 hit chart at 15. That
is a real balance decision hiding in a constructor argument; play it before deciding it is wrong,
but know that it is there.

### 2.6 Using a gate vortex

Nothing new. Any unit may fly into it under §2.2's entry rule and leave via the existing Jump Out
button, `jumpout` movement order and `Movement::resolveJumpOuts`. Fighter flights included
(Stage 6). Attached pods are carried out with their host and can never use one alone.

---

## 3. Architecture

### 3.1 THE STRUCTURAL FACTS — read these before anything else

§7 named one blocker. It was the wrong one. Measured 2026-08-23:

**1. `isMyShip` is NOT the blocker, and needs no exception.**
Clicking a terrain gate in Initial Orders already reaches
[`InitialPhaseStrategy.targetShip`](source/public/client/renderer/phaseStrategy/InitialPhaseStrategy.js#L146)
(because `isMyShip` is false, `onShipClicked` routes to `targetShip`, not `selectShip`) and that
builds a **`ShipTooltipInitialOrdersMenu`** — the same menu `targetWeaponsHex` lives on. Right-click
already opens the gate's ship window with no ownership test at all
([PhaseStrategy.js:394](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L394)). Terrain
is also already in the click sweep — `getInterestingStuffInPosition` excludes only the *vortex*.
**So the signal button is an entry in an existing button array, and
[gamedata.js:211](source/public/client/gamedata.js#L211) is not touched.** Do not loosen the terrain
gate; it is load-bearing everywhere else (fleet list, active ships, movement UI, commit checks).

**2. THE REAL BLOCKER IS THE SUBMIT PATH, and it is two-sided.**
- Client: [ajaxInterface.js:1001](source/public/client/ajaxInterface.js#L1001) — `tidyships.push(newShip)`
  at [:1139](source/public/client/ajaxInterface.js#L1139) is *inside* `if (ship.userid === gamedata.thisplayer)`.
  A ship you do not own is **not in the POST at all**, systems or otherwise.
- Server: [InitialOrdersGamePhase.php:200](source/server/Phase/InitialOrdersGamePhase.php#L200) —
  `if ($ship->userid != $gameData->forPlayer) continue;` gates the fire-order validate+submit loop.

Both must be widened, narrowly and deliberately, for a non-owner to carry an order on a gate. That
is the whole of the structural work, and §4 Stage 2 is where it is done.

**3. Terrain is skipped by both vortex sweeps — one correctly, one not.**
`spawnDeclaredVortices` opens with `if ($ship->isTerrain()) continue;` and its comment already says
*"Phase 2's fixed gates get their own path"* — correct, gates go through `openSignalledGates`.
`closeExpiredVortices` has the **same** skip, and there it is a bug: a gate vortex would never
close. Trap 1.

**4. `JumpgateCapital` is terrain by `shipSizeClass`, not by class.** It `extends BaseShip` and sets
`$shipSizeClass = 5` by hand, which is enough for `BaseShip::isTerrain()`
([ShipClasses.php:3181](source/server/model/ships/ShipClasses.php#L3181)) and for the client's
`gamedata.isTerrain`. So the fleet list, the active-ship sweep, `isMyShip` outside Deployment and
both vortex sweeps all treat it as terrain, exactly as they would a `Terrain` subclass.
The one thing to check rather than assume is `RammingAttack::getTerrainOccupiedHexes`, which the
vortex obstruction sweep calls for every `isTerrain()` unit — **verified safe**: it guards with
`property_exists($terrain, 'hexOffsets')` and falls back to `$terrain->Huge`, both of which a
BaseShip answers correctly.

### 3.2 `markGate()` — a flag, not a subclass

Exactly the §9 argument, and it applies with equal force: no new class means no autoload
regeneration (which on live needs the maintenance gate), `phpclass` stays `"JumpEngine"` so a game
in progress across the deploy sees no identity change, every `instanceof JumpEngine` site keeps its
answer without an audit, and it is per-instance.

```php
// baseSystems.php, alongside markLegacy()
protected $gateJump = false;

public function markGate()
{
    $this->gateJump = true;
    $this->range    = 10;                 //SIGNAL range, not a projection range
    $this->firingModes = array(
        1 => "Open 1 turn", 2 => "Open 2 turns",
        3 => "Open 3 turns", 4 => "Open 4 turns",
    );
    //$loadingtime / $turnsloaded are LEFT ALONE: the constructor already set them from the 4th
    //argument (20 on JumpgateCapital), which is the recharge rule §2.5 asks for.
    //$hideFiringModeSelector stays true - the duration is picked by the signal panel, not by
    //cycling a letter in the gate's ship window.
    return $this;
}

public function isGateJump(){ return $this->gateJump; }
```

⚠️ **Prune the per-mode arrays exactly as `markLegacy()` does.** `Weapon::__construct` has already
walked the seven vortex modes and filled `minDamageArray` / `maxDamageArray` / `priorityAFArray` /
`animationExplosionScaleArray`; shrinking `$firingModes` to four alone leaves three dead entries in
each. Copy that loop (keys 1–4 instead of key 1) and finish with `changeFiringMode(1)`.

⭐ **`$name` stays `"jumpEngine"`**, for the same reason `markLegacy()` keeps it:
`SystemFactory.createSystemFromJson` picks the client class from `systemJson.name`, so the existing
client `JumpEngine` is reused with no new JS. The client tells a gate engine from a ship engine by
its **ship** (`gamedata.isJumpGate(unit)`), never by the system.

`JumpgateCapital` then reads:

```php
$this->addPrimarySystem((new JumpEngine(8, 10, 20, 20))->markGate());
```

⚠️ **`$facingArrow` was declared here and has been REMOVED** (user ruling 2026-08-24, §2.2). It is
still the property `SpawnJumpPoint` declares — `ShipIcon` draws it permanently over any unit that
carries one, rotated to the unit's facing — but only the vortex carries it now, so the arrow appears
at one point of a gate vortex's life instead of three. Do not put it back on the gate.

⚠️ `Terrain.json` is **generated**: dropping the property does not change the static blueprint until
the static ship generator is re-run, so a stale `Terrain.json` keeps serving `facingArrow` to the
lobby.

### 3.3 The signal order — and why concealment is free

The declaration is an ordinary **ballistic hex-target FireOrder on the gate's own Jump Engine**,
aimed at the gate's own hex. That single choice buys the entire Stage 2/2b/5/6 pipeline:

| It gets, for free | Because |
|---|---|
| Secrecy until Initial Orders close | `TacGamedata::hideSystemFireOrders` strips every phase-1 ballistic order from every payload ([TacGamedata.php:1218](source/server/model/TacGamedata.php#L1218)) |
| A map marker | `BallisticIconContainer` already draws `damageclass === 'jumppoint'` ([:56](source/public/client/renderer/icon/BallisticIconContainer.js#L56), [:722](source/public/client/renderer/icon/BallisticIconContainer.js#L722)) |
| Replay | Fire orders replay |
| A combat-log line | `writeVortexLogOrder`'s RammingAttack / `JumpVortex` idiom |
| Server-side legality | `Firing::validateFireOrders` already routes `instanceof JumpEngine` to `validateVortexDeclaration` |

| Field | Value |
|---|---|
| `shooterid` | the **gate's** ship id |
| `weaponid` | the gate's Jump Engine |
| `x` / `y` | the gate's own hex (never anywhere else) |
| `firingMode` | **1–4 = the programmed open duration in turns** |
| `targetid` | the claiming player's nearest qualifying unit — a **claim**, re-derived server-side and masked on the way out (below) |
| `damageclass` | `jumppoint` (inherited from `$weaponClass = "JumpPoint"`) |

⭐ **`targetid` is how the claiming PLAYER is recorded, and the server must not trust it.**
`tac_fireorder` has no player column and the gate belongs to nobody in particular, so the order
alone cannot say who claimed it. The client fills `targetid` with its own nearest qualifying unit;
`Firing::validateVortexDeclaration` then **re-derives** the claimant's nearest unit from
`$gamedata->forPlayer` and **overwrites** `targetid` with it before the order is submitted. The
client's value is a hint, never an authority — a tampered POST cannot claim on someone else's
behalf or fake a distance, because the distance used at resolution time is recomputed from the DB
in `advance()` anyway.

⭐ **THE CONCEALMENT RULING (§2.1) IS TWO FACTS, ONE FREE AND ONE NOT.**

*Free:* `JumpEngine::hasVortexDeclaration($ship, $turn)` walks **that ship's own** engines' fire
orders, and a gate claim sits on the **gate's** engine. So `ShadingField` / `CloakingDevice` /
`Stealth::isDetectedInitial` never fire for a signaller, and `weaponManager.targetHex`'s `isHidden`
guard is never reached either. The ruling "a stealthed ship may signal without revealing itself" is
therefore what the code already does. **Write that down in `hasVortexDeclaration`'s comment** —
otherwise the next reader will "fix" it, exactly as Phase 1 §2.1 warns about the LoS rule.

*Not free — and it is the one leak this design creates:* `targetid` names a real unit, and fire
orders become public from phase 2 onward. Left alone, the enemy could read "the ship at X signalled
the gate" and pick a cloaked hull out of the payload. **`hideSystemFireOrders` must null `targetid`
on a gate signal order for any viewer the targeted unit does not belong to** — the same idiom the
`hidetarget` branch two lines below already uses. One condition, and it is not optional.

⚠️ **Modes 5, 6 and 7 must be refused on a gate engine.** Mode 7 is MAINTAIN, and a gate has no
Maintain (§2.3). `getMaintainDeclaration` must return null for a gate engine outright, the same way
it does for a legacy one — otherwise a tampered mode-7 order would put a gate on the ship closure
path and take the range and power tests with it.

### 3.4 Claim resolution and the hold note

`JumpEngine::openSignalledGates($gamedata, $dbManager)` — a sibling of `spawnDeclaredVortices`, run
immediately after it in `InitialOrdersGamePhase::advance` (same reasons: `advance()` runs off a real
`getTacGamedata` load, so the engine's vortex state and the units' positions are real; and
`$gamedata->phase` already reads 2, so never branch on it).

```
for each ship that isTerrain() and holds a gate engine:
    skip if the gate is destroyed, already holds an open vortex, or is not fully charged
    claims = this turn's un-rejected mode-1..4 orders on that engine
    group claims by claiming player (targetid -> unit -> userid)
    for each player: distance = min over their live, deployed, non-terrain units to the gate hex,
                     requiring <= 10 (NO LoS test - §2.1); drop the player if none qualifies
    winner = smallest distance; tie -> Dice::d(100) each, highest wins, bounded re-roll
    hold = min(winner's firingMode, maxHold(reactor damage))
    openVortexAtGate(gate, engine, hold, winnerUserId)
    log the winner and each loser BY PLAYER, never by unit (§2.4)
```

`openVortexAtGate` is a thin wrapper over the **existing** `openVortex` body: same
`Manager::insertSingleShip` → `$spawned = turn + 1` → deploy `MovementOrder` → `'Vortex'` note →
`writeVortexLogOrder`. Only three inputs differ — the hex is the gate's own, the facing is the
gate's own (from `getLastMovement()->facing`), and there is no projection range to measure.
**Refactor `openVortex` to take (hex, facing, holder) rather than copying it.** A second copy of the
spawn path is exactly how the `spawned = openTurn + 1` rule drifts.

**THE HOLD NOTE.** A gate vortex needs its programmed duration to survive a load, and the existing
`'Vortex'` note cannot carry it:

> ⚠️ `notevalue` is `"<openTurn>,<closeTurn>[,<reason>]"` and the closure reason is **free text that
> contains commas**, which is why `restoreVortexState` parses it with `explode(',', $v, 3)`. Adding
> a fourth field would silently swallow it into the reason on every existing note in every live
> game. **Do not touch that format.**

Instead write a **second, additive note** on the same engine, at the same turn 1 / phase 1 as the
opening note:

| field | value |
|---|---|
| `notekey` | vortex ship id (same key as the `'Vortex'` note) |
| `notekey_human` | `"VortexHold"` (10 chars — ⚠️ the column is `varchar(40)`) |
| `notevalue` | `"<hold>,<winning userid>"` |

`restoreVortexState` reads it in the same pass it already keys by vortex id, storing
`$this->vortexHoldTurns` and `$this->vortexClaimantId`. A vortex with no such note is a ship-opened
vortex and behaves exactly as it does today — which is what makes this change invisible to Phase 1.

### 3.5 The signal control — activation and duration, and nothing else

⭐ **No facing control exists anywhere in this plan** (§2.2). The whole UI is: *is it on, and for
how long.*

```
click the gate  (no ship selected, none needed)
  -> tooltip button "Signal Jump Gate"  (shown only if I have a unit within 10 hexes)
      -> a small panel anchors to the gate hex:
              Open for  (-)  3  (+)   turns
                     [ SIGNAL ]  [ x ]
          -> SIGNAL ..... creates the FireOrder on the gate and closes the panel
          -> x / click away  discards; no order, nothing to clean up
      -> while a claim stands, the button reads "Cancel Gate Signal"  (cancel.png)
          -> clicking it withdraws the claim AND redraws the tooltip in place,
             so the button toggles straight back to "Signal Jump Gate"
```

⭐ **The Cancel button toggles back without a re-click** (user request 2026-08-24). The two buttons
are already mutually exclusive on `hasGateSignal` / `noGateSignalYet`, but nothing re-evaluates a
menu's conditions on its own, and the tooltip *survives* its own button clicks — it swallows
`mousedown`/`mouseup`, so the click-away discard never fires for them. `cancelJumpGateSignal`
therefore calls `ShipTooltip.update()` (no arguments — a gate signal routinely has no selected
ship), which re-runs every condition and rebuilds the button row. `ShipTooltipMenu.renderTo` now
keeps the tooltip handle on `this.shipTooltip` for exactly this. It also sets `currentInfo` by hand,
because the pointer does not *move* across the swap, so no `mouseover` fires on the replacement
button and the info line would otherwise read "Cancel Gate Signal" beneath a Signal button.

⚠️ **`onVortexFacingRequested` closes the ship tooltip**, the way `onGateSignalRequested` already
did (user request 2026-08-24). A *ship's* vortex declaration can be started from the tooltip's
"Target selected weapons on hexagon" button, and that tooltip is anchored to a unit on or beside the
very hex `UI.vortexFacing`'s ring lays itself out around — so it covered the turn arrows and the OK
button and the ring could not be worked. Same one-line fix, same reason.

**Plain anchored HTML, not a canvas ring.** `UI.vortexFacing`'s ring exists because a *facing* has
to swing with the thing it sets and had to stay legible at six angles; a duration is a number and
needs none of that. Reuse only the **anchoring** —
`PhaseStrategy.positionVortexFacingUI`'s convert-to-viewport + re-run on the zoom/scroll callback
lists — and the markup shape of `#shipMovementUI` in [game.php:594](source/public/game.php#L594).
Nothing is drawn into a canvas, no glyph constants, no `drawCurvedArrow`.

Same transaction discipline as Stage 2b: **the order is born on SIGNAL.** No half-declared claim,
nothing to nag about before the commit, and re-aiming is remove-and-redeclare.

**If even that is too much UI**, the fallback with zero new files is four tooltip buttons
(`Signal 1 / 2 / 3 / 4 turns`) in the existing button array — but four near-identical icons in a
menu that already has twenty is worse, not simpler.

---

## 4. Build stages

Each stage ends green on `scripts/fvbuild.ps1 -Check` (map staleness + ship-data validator + replay
harness). ⭐ **The replay harness must be re-run after Stage 1**, because `markGate()` changes a
serialised property set on a blueprint and the harness covers exactly that.

### Stage 1 — `markGate()` and the gate blueprint (no new behaviour)

- `JumpEngine::markGate()` / `isGateJump()`, with the `markLegacy()` array-pruning loop copied.
- `getMaintainDeclaration()` returns null for a gate engine (§3.3).
- `getVortexDeclaration()` accepts modes 1–4 on a gate engine and refuses 5–7; unchanged (1–6) on a
  ship engine. The two are different questions and must not share a range test.
- The concealment comment in `hasVortexDeclaration` (§3.3) — a comment, but a load-bearing one.
- `JumpgateCapital` calls `markGate()`. (It also declared `$facingArrow` as built; that was removed
  on 2026-08-24 — see §2.2/§3.2.)
- `ShipSystem::clearPossibleCriticals()` — three lines, public, so `JumpgateCapital` can silence its
  Reactor's crit chart from outside. (`$possibleCriticals` is `protected` and never serialised, so
  this costs the static blueprints nothing.) `JumpgateCapital` calls it on its Reactor.
- Run `checkShipData.php` — `JumpgateCapital` is brand new and has never been through it.
- Regenerate statics (`fvbuild.ps1 -Statics`). Expect a small, mechanical diff.

**Verify:** the gate's Jump Engine reads **20/20** on its system icon on turn 1, the gate draws **no**
arrow of any kind (2026-08-24), and it can be bought and placed. Nothing else changes; the engine is inert because no
client can target it yet.

### Stage 2 — the two submit widenings (§3.1 fact 2)

The structural stage, and the only one with blast radius. Do it alone and read the diff twice.

**Client** — [ajaxInterface.js:1001](source/public/client/ajaxInterface.js#L1001):
```js
//A jump gate is the ONE unit a player may order without owning it (JUMP_GATES_PLAN.md §3.1).
//Send the gate itself, carrying its Jump Engine's fresh signal order and NOTHING ELSE - no
//movement, no EW, no power, no enhancements, no ammo. The server ignores all of those for a
//gate anyway (its power and EW loops are separately userid-gated), and sending them would be
//an invitation to trust them later.
```
Scoped to: `gamedata.gamephase === 1`, the unit is a jump gate, and it carries a mode-1..4 order on
its Jump Engine for this turn that this client created. No gate, no extra entry in the POST.

**Server** — [InitialOrdersGamePhase.php:200](source/server/Phase/InitialOrdersGamePhase.php#L200):
allow a POST ship through the fire-order loop when it is a jump gate, and pass **only** its Jump
Engine's orders to `Firing::validateFireOrders`. The other three loops in `process()` keep their
`userid != forPlayer` guard untouched.

⚠️ **The `generateIndividualNotes` / `saveIndividualNotes` loops at
[:137](source/server/Phase/InitialOrdersGamePhase.php#L137) are already NOT userid-gated**, so a
POSTed gate now runs note generation on a POST-side object with no enhancements and no loaded notes
(trap 2 of the Phase 1 plan). Audit every system `JumpgateCapital` mounts — Reactor, CnC, Scanner,
Hangar, JumpEngine, Structure — and confirm none of them writes a note off state a POST-side ship
does not have. This is a read, not a change, but it is not optional.

**Verify with a forged order before any UI exists:** hand-write a mode-2 order on a gate into a
POST and confirm it lands in `tac_fireorder` with `shooterid` = the gate; that the same order from
a player with no unit within 10 hexes is rejected with a `Debug::log` line; and that an order on
any *other* unowned unit is still dropped exactly as it is today.

### Stage 3 — declaration: the button, the panel, the validation

- `gamedata.isJumpGate(unit)` (`phpclass === 'JumpgateCapital'`, one place, mirroring
  `shipManager.movement.isJumpVortex`) and `gamedata.canSignalJumpGate(gate)` — range only, **no
  LoS test**.
- A `signalJumpGate` entry in
  [`ShipTooltipInitialOrdersMenu.buttons`](source/public/client/UI/shipTooltipInitialOrdersMenu.js#L12).
- `UI.gateSignal` (§3.5) and `weaponManager.queueGateSignalOrder`, mirroring
  `queueShadowFighterBombOrder`'s checklist: `removeFiringOrder` → push the order →
  `webglScene.customEvent('HexTargeted', …)`.
- `Firing::getVortexDeclarationBlock` gains a **gate branch, taken first**, with its own rule list:
  gate not destroyed / engine not destroyed or offline / target hex IS the gate's own hex / mode
  1–4 and ≤ maxHold / gate holds no open vortex / gate fully charged / the claiming player has a
  qualifying unit ≤ 10 hexes / no earlier un-rejected claim from this player this turn.
  Then it **overwrites `targetid`** with the re-derived nearest unit (§3.3).
  ⚠️ The existing ship rules — 4-hex range, LoS, the terrain-obstruction sweep, one-vortex-per-*ship*
  — must **not** run on a gate claim. The gate's own hex holds terrain (itself), so the obstruction
  sweep alone would reject every claim ever made.
- **`TacGamedata::hideSystemFireOrders`: null `targetid` on a gate signal order for any viewer the
  targeted unit does not belong to** (§3.3). This is the concealment ruling's only real cost.
- `BallisticIconContainer`: label `'Jump Gate Signalled'`, and ⚠️ **suppress the `jumppointArrow`
  sprite for a gate order** — that code draws an arrow at `firingMode - 1`, which on a gate would
  render the *duration* as a facing.

**Verify:** the DB row, the marker, that the enemy's phase-1 payload contains neither, and that from
phase 2 onward the enemy's payload carries the order with `targetid = -1`.

### Stage 4 — resolution, the hold, the lifecycle

- Refactor `openVortex` to take (hex, facing, holder) and add `openVortexAtGate` (§3.4).
- `JumpEngine::openSignalledGates`, called from `InitialOrdersGamePhase::advance` right after
  `spawnDeclaredVortices`, threading `$dbManager` for its log orders exactly as that one does.
- The `'VortexHold'` note; `restoreVortexState` reads it.
- ⚠️ **`closeExpiredVortices` must stop skipping terrain** (trap 1, confirmed by the user). Narrow
  the skip: terrain that does not carry a gate engine is still skipped; a gate is not.
- `getVortexClosureReason` gains a gate branch, taken first: unit gone / gate destroyed /
  `turn >= openTurn + hold`. No maintain, no power, no range (§2.3).
- Reactor damage (§2.5): `getLoadingTime()` and the hold cap read it; total loss destroys the gate.
- Client: `canJumpEngineMenu` must not offer the Maintain panel on a gate, and the engine's icon
  should read the vortex counter `N/hold` rather than `N/4` while one stands.

**Verify:** the full lifecycle from both seats, plus a replay of the whole game.

### Stage 5 — polish

Gate tooltip showing charge and current hold; the `convoyRaid` scenario played end to end with a
`JumpgateCapital` bought into its 1000-point gate slot.

---

## 5. Traps

1. ⭐ **`closeExpiredVortices` skips terrain.** [baseSystems.php:5753](source/server/model/systems/baseSystems.php#L5753)
   — `if ($ship->isTerrain()) continue;`. Left as-is, a gate vortex **never closes**: it is spawned
   by the gate sweep and then invisible to the only thing that can end it. Confirmed as required
   work by the user 2026-08-23. The single highest-consequence line in the plan.
2. **`spawnDeclaredVortices` skips terrain too — and that one is CORRECT.** Gates go through
   `openSignalledGates`. Do not "unify" the two sweeps; they resolve different rules (projection +
   facing + one-per-ship vs. own hex + own facing + contested claim).
3. **The gate's own hex holds terrain: itself.** Every obstruction test written for a projected
   vortex rejects a gate claim. `getVortexDeclarationBlock`'s gate branch must return before the
   sweep, exactly as the Maintain branch already does.
4. **`targetid` is a claim, not a fact — and it is also a leak.** Re-derive it server-side, and mask
   it on the way out (§3.3). It is the only field that names the signaller, and §2.1 says the
   signaller is never named.
5. ⚠️ **Do not "fix" `hasVortexDeclaration` to cover gates.** It walks the ship's own engines and
   therefore never reveals a gate signaller — which is the *ruling*, not an oversight (§2.1). This
   is the mirror image of Phase 1 §2.1's "LoS is already the behaviour; recorded so nobody fixes it
   later", and it will read exactly as tempting.
6. **`$gamedata->turn` is a STRING** (Phase 1 trap 10). Every new turn comparison here — hold
   expiry, charge, claim turn — casts both sides.
7. **`notekey_human` is `varchar(40)`** — an overflow is a mysqli 1406 that aborts the whole player
   submission, not a truncation. `"VortexHold"` is 10.
8. **The `'Vortex'` notevalue parser uses `explode(..., 3)` because the reason contains commas.**
   Never add a fourth field. The hold lives in its own note for this reason alone.
9. **Client system objects share fields across same-phpclass instances** (Phase 1 trap 4). Two gates
   in one game must not show each other's charge or hold. Anything mirrored to the client is
   per-instance, or it is derived from the *unit* rather than the system.
10. **`insertSingleShip` returns a string id** — route every vortex spawn through it, never
    `submitShip`.
11. **The tooltip may be built with no ship selected.**
    [`InitialPhaseStrategy.targetShip`](source/public/client/renderer/phaseStrategy/InitialPhaseStrategy.js#L149)
    opens with `shipManager.getTurnDeployed(this.selectedShip)`, which throws on `null` — and "click
    the gate with nothing selected" is now the *primary* gesture, not an edge case. Guard it.
12. **Two other hulls carry a `JumpEngine` and are NOT part of this plan.** `JumpgateNew` (terrain)
    and `Jumpgate` (civilians) are obsolete and out of scope (user ruling 2026-08-23). They are
    named here only so that finding them in a grep does not read as an omission. Neither gets
    `markGate()`; both keep their Phase 1 behaviour. ⚠️ **Trap 1's change must not catch them** —
    narrow the `closeExpiredVortices` skip on "carries a *gate* engine", which they do not, rather
    than on "is a jump gate by name".
13. **Points and CV.** `JumpgateCapital` costs 10 points and mounts a `JumpEngine`; Phase 1 already
    excluded `JumpEngine` from `calculateCombatValue`'s weapon bucket
    ([ShipClasses.php:499](source/server/model/ships/ShipClasses.php#L499)) and `markGate()` changes
    nothing there. Re-run the harness anyway — CV is in the snapshot.

---

## 6. Test plan

Local Docker, a fresh game per scenario, verified against `tac_*` exports. The `convoyRaid`
scenario already puts a gate on the board and is the natural harness.

| # | Scenario | Expect |
|---|---|---|
| 1 | Click a gate with a unit 10 hexes away | "Signal Jump Gate" offered; panel opens |
| 2 | Click a gate with the nearest unit 11 hexes away | Not offered; forced POST rejected with a log line |
| 3 | Nearest unit in range but LoS blocked by an asteroid | **Offered and legal** — no LoS test anywhere (§2.1) |
| 4 | Signal for 2 turns, commit | `tac_fireorder` row: shooterid = gate, x/y = gate hex, firingmode 2; **opponent's phase-1 payload has no such row** |
| 5 | Panel: step the duration, SIGNAL | Row's firingmode matches; discarding instead leaves **no** row |
| 6 | Turn N Movement | "Jump Gate Signalled" marker on turn N; the vortex unit appears on N+1 with the gate's facing |
| 7 | Enter from the gate's mouth side | Jump Out offered; movement ends; unit removed before Pre-Firing; CV preserved |
| 8 | Enter from any other side | No Jump Out button |
| 9 | Sideslip in along the mouth axis | Offered |
| 10 | Enemy unit uses the gate | Allowed |
| 11 | Programmed 2 turns | Open on N+1 and N+2, gone on N+3; nothing the owner does extends it |
| 12 | Signaller flies away / is destroyed / jumps out through it | Vortex **stands** for its full programmed hold (§2.3) |
| 13 | Two players signal, one clearly nearer | Nearer wins; loser gets a log line naming the PLAYER, not a unit; one vortex only |
| 14 | Two players signal at equal distance | Roll off; a reload does **not** re-roll |
| 15 | ⭐ A shaded / cloaked / stealthed ship is my only unit in range | Signal legal; ship **stays concealed**; no `detected` / `Unshaded` / `Decloaked` note is written |
| 16 | ⭐ Same, seen from the enemy seat in phases 2–6 | The gate order is visible, `targetid` is `-1`, and nothing in the payload or the log names the signaller |
| 17 | Gate reactor at 9 damage | Recharge reads 23; hold still 4 |
| 18 | Gate reactor at 30 damage | Hold capped at 2; a 4-turn claim is clamped, with a log line |
| 19 | Gate reactor destroyed (50) | Gate destroyed; any open vortex closes at end of turn |
| 20 | Jump Engine at 3 of 10 boxes lost, signal | 30% failure roll at end of turn; on failure the gate dies and no vortex forms (§2.5 — confirm this feels right) |
| 21 | Signal, then signal again next turn | Refused — still recharging (0/20), climbing from the turn after closure |
| 22 | Replay the whole game | Marker, vortex, closure and both log lines at the right turns |

Plus: a ship-projected vortex in the same game as a gate vortex, to prove the two lifecycles do not
interfere; and `fvbuild.ps1 -Check` green at the end of every stage.

---

## 7. Open decisions

**None.** All five were ruled on by the user 2026-08-23 and are written into §2 and §5:

1. **Concealment** — signalling a gate never reveals a stealthed, shaded or cloaked unit (§2.1).
2. **Line of sight** — not required for signalling (§2.1).
3. **The gate's facing** — set when the gate is placed, fixed for the game, no facing UI (§2.2).
4. **`closeExpiredVortices`** — gets its `JumpgateCapital` exception (§2.3, trap 1).
5. **`JumpgateNew` and `Jumpgate`** — obsolete, out of scope, untouched (trap 12).

The one thing left to *judge rather than decide* is the 10-box Jump Engine's failure odds (§2.5),
and that wants play, not a ruling.
