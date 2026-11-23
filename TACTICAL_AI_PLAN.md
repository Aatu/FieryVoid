# Tactical AI Plan (CPU opponent for Fiery Void battles)

Status: **Stage T0 COMPLETE (committed 2026-11-13, 5807979b8, play-tested in game 4568; as-built
notes: section 10.1). Stage T1a COMPLETE (committed 2026-11-13, 9537a8b72, Auto-Deploy play-tested
by the user; section 10.2). Stage T1b COMPLETE (2026-11-13, uncommitted; harnesses and self-play green, play-tested by the user in game 4626; section 10.3). Stage T2 COMPLETE (committed 15624ab89; play-tested by the user in game 4679; one part of its "done when" is still open - section 10.4). Stage T3 COMPLETE and Stage T4 COMPLETE (committed together as 501272f04; play-tested by the user in games 4698 and 4701; sections 10.5, 10.6). **Stage T5 COMPLETE (marked so by the user 2026-11-20, with T5f; uncommitted from T5e on, not yet play-tested from T5e on)** - started 2026-11-16: T5a + T5b committed as f1e2739af; T5c (with the user's feedback on game 4750) + T5d (gravitic hulls) committed together as 151cf072e; T5e (pivots and rolls) COMPLETE 2026-11-19 (uncommitted; every gate green, not play-tested); T5f (levels, ladder) STARTED 2026-11-19 with the user's feedback on game 4774 - sub-stages T5f-1 to T5f-3 BUILT the same
day; T5f-4 (Hard's power management) and T5f-5 (Hard's extended turns) BUILT 2026-11-20; T5f-6 (the ladder) run 2026-11-20 -
Hard leads Normal by ~54% on both EA and Brakiri fleets over 448 games, short of the 60% target; then, at the user's request the
same day, T5f-7 (the ENEMY FORECAST - Normal and Hard anticipate the moves of enemy ships yet to move) and T5f-8 (decision time,
brought forward from T8: two-pass judging and a CPU budget per ship) BUILT, and T5f-9 run - the ladder on the standard 42x30 map
(final build: Hard 50% on EA, 62% on Brakiri over 24 games each) and a forecast audit that changed Hard's forecast; Hard's lead
ACCEPTED for the first version as the user ruled; T5f COMPLETE (user, 2026-11-20: "let's mark Stage T5f (and Stage T5 overall)
as complete for now") - committed by the user as 074e28ff1; section 10.7.** **Stage T6 COMPLETE (marked so by the user 2026-11-21:
"I think it is now for v1 of CPU opponents"; committed by the user as 442121f12, with their own tuning of four constants)** - T6a (the
4774 notes), T6b (surrender), T6c (jumping out), T6e (Hard sheds a drive past half its boxes) and T6g (a side down to its fighters must
keep shooting) built; T6d (last-resort ramming) NOT built - deferred past v1; T6f's done-when met by the T6 batches; section 10.8.
**Stage T7 STARTED 2026-11-21**: T7a (the readiness list in the lobby - only Earth Alliance and Raiders offered for a CPU slot, the
units the CPU cannot fly hidden, a CPU fleet with either refused on Ready), T7b (EA Interceptors fire offensively only when boosted;
Hard boosts the spare ones against a fighter-heavy enemy) and T7b-2 (only Interceptors whose arc faces the fighters) BUILT - committed
by the user as 56b03b2d3; section 10.9.
**Stage T8 (the skirmish release) COMPLETE (the user, 2026-11-22), uncommitted** - decision time first, as the user asked (a
live-sized Movement activation 1.5-1.6x faster with the same decisions, a CPU budget per submission and request slicing for live), the
deferred T6d ramming, auto-compose (`FleetComposer`, proven against the REAL Fleet Checker under node - the port re-scoped to T10 by
the user), the level picker, CPU seats in the lobby and games list, the user's lobby extras, and a FAQ entry; timed on the live host by the
user (about twice the dev box's speed: a live-sized EA Movement 4.0 s Normal, 5.3 s Hard); the Beta is the user's; section 10.11.
**Stage T9 (more factions - section 10.10) PLANNED 2026-11-22** at the user's request, with the campaign paused;
the campaign wiring is now T10. Companion plans: `CAMPAIGN_PLAN.md` (the campaign
layer, which uses this for battles against NPCs - see its section 7) and `STRATEGIC_AI_PLAN.md` (what
NPC empires do on the campaign map).

This plan stands on its own. **The tactical AI may ship for ordinary skirmish games before the
campaign goes live**, so nothing here depends on campaign code. Section 9 is the only place the
campaign appears.

Reading order: section 1 (principles), 3 (how a CPU plays through the existing engine), 4 (the
driver), then the stage you are building in section 10.

---

## 1. Principles

| # | Principle |
|---|---|
| P1 | **The CPU is a player.** It is a real row in `player`, seated in an ordinary slot, and it plays by submitting the same order payload a browser POSTs, through the same `Phase::process` methods. No phase, handler or rule gets an "if CPU" branch. |
| P2 | **No cheating.** It decides from the gamedata its own player would be sent (`getTacGamedata` for its player id, after the per-viewer masking). It never reads hidden ships, hidden mines, enemy EW of the current phase or enemy orders. Difficulty comes from decision quality, never from hidden information or bonus dice. A points handicap is the only allowed thumb on the scale, and the lobby shows it. |
| P3 | **Always legal, always finishes.** An AI order that the server would reject is a bug of the first rank, because nobody is there to fix it and the game stalls. Every generated order set is checked before submission, and a per-phase fallback (the safest legal "do nothing") guarantees the phase can always be submitted. |
| P4 | **Unknown systems are left alone.** FV has ~90 factions with special systems. The AI uses only mechanics it has a handler for and leaves everything else at its default. A faction is offered as a CPU opponent only when it is on the readiness list (section 7). |
| P5 | **Lazy and bounded.** No daemon. The AI runs inside ordinary requests with a time budget and resumes on the next one. |
| P6 | **Inert when unused.** A game with no CPU slot executes none of this code beyond one cheap test, and its payload is byte-identical. The replay harness proves it. |
| P7 | **Readable decisions.** Every decision is scored by named terms and can be logged (`tac_ai_log`), so "why did it do that" is answerable from data. |

---

## 2. Scope, flag, and what already exists

* **The off switch (user requirement, 2026-11-12).** FV is patched weekly and this feature will be
  built across many of those patches, so half-built AI code will be on the live server for weeks.
  One setting keeps it dark: `$feature_flags['cpuOpponent']` in `source/server/varconfig.php` (per
  environment; the same `Features::enabled()` helper as the campaign, `CAMPAIGN_PLAN.md` section 8).
  Absent or false = off, so **live is off by default with no action needed**; local Docker and the
  test instance set it true. `Features` and the flag are the first thing built in T0 - whichever of
  this plan and the campaign starts first creates the helper.
  With the flag off, all of the following hold, and a harness asserts each:
  * the CPU option is not rendered in Create Game or the lobby (hidden, not greyed);
  * the server refuses to create or seat a CPU slot, whatever the POST says;
  * `AIDriver::run` returns on its first line, before any query;
  * bot accounts never appear in any player list, and no payload carries a CPU field;
  * ordinary games are byte-identical in the replay harness.
  An optional `betaTesters` list of player ids in the same config shows the option to chosen players
  on live while the flag is otherwise off. Turning the flag off again later is safe at any time:
  games already running with a CPU seat simply stop being driven (the human can surrender or wait),
  so say so in the release note if it is ever used that way.
* Migrations (`db/cpuOpponent.sql`) are additive and nullable, so they can be applied on live long
  before the flag is turned on.
* Existing structures this builds on (verified 2026-11-12):
  * `Manager::submitTacGamedata` -> `getShipsFromJSON` -> `Phase::process($gdS, $dbManager, $ships)`
    for each of the six phases; `Manager::advanceGameState` -> `DBManager::checkIfPhaseReady`
    (every slot's `lastphase` / `lastturn` equals the game's) -> `Phase::advance`.
  * `source/server/handlers/AutomatedMovement.php`: server-built `MovementOrder`s for a unit the
    player does not control (Uncontrolled Hunter-Killers). Intents `drift` and `seek`, **flights
    only** (no turn delay). Its header names itself the seam for CPU ships. Called from
    `MovementGamePhase::advance` / `process`.
  * `Weapon::calculateHitBase($gamedata, $fireOrder)`, `Weapon::getAvgDamage()`,
    `Weapon::calculateRangePenalty`, `Weapon::isInDistanceRange` - the server already answers "what
    is my chance to hit and what would it do".
  * `Firing::validateFireOrders`, `Firing::automateIntercept` (unassigned defensive weapons intercept
    by themselves), `EW::validateEW` and the `EW` getters, `Movement::validateMovement` and
    `Movement::validateThrustPayment` (the latter is log-only today:
    `$enforceThrustValidation = false`).
  * `SimultaneousMovementRule` (initiative categories, `getNewActiveShip`), `tac_game.activeship`.
  * `tests/replay/replayHarness.php` and its Dice stub pattern.
* Hard fact that shapes the plan: **movement legality lives mostly in the client**
  (`client/movement.js`, ~2,700 lines). The server validates a submitted move but cannot generate one
  for a ship. A server-side ship move generator is the largest single piece of new code here.

Out of scope for v1: boarding and marines, ramming as a tactic (but see stage T6: the user's
"last resort" ramming under the Desperate rule, and units designed to ram), mine laying, called shots, hangar
recovery mid-battle, jump-point tactics other than leaving, Fighter Missions, extended turns,
gravitic drive rotation (an Expert Graviton Controller's - gravitic hulls themselves are plotted since
stage T5d), officers' active abilities, and every faction special not on the readiness
list. Each is "leave at default", never "half use".

---

## 3. How the CPU plays through the existing engine

### 3.1 Bot accounts

* `player` gains `is_cpu tinyint NOT NULL DEFAULT 0` (migration `db/cpuOpponent.sql`). `player` is
  MyISAM with a fixed column list in a few queries: grep every `INSERT INTO player` and
  `SELECT ... FROM player` and confirm they name their columns before adding one.
* A small pool of bot rows is seeded (`CPU Alpha` ... `CPU Hotel`), with an unusable password hash so
  they can never log in, excluded from ladder, Discord pings, "players online" and player lists.
  One game may seat several different bots; one bot may sit in any number of games.
* The difficulty and personality of a seat are per game, not per account: stored in
  `tac_playeringame` as a new nullable column `cpu` (JSON: `{level, personality, seed}`). NULL = human.
  `PlayerSlot` gains the field; **do not send it to clients beyond a boolean `isCpu` and the level**
  (new gamedata fields need adding to `parseServerData`'s named-key copy).

### 3.2 Orders go through `process`

`TacticalAI::play($gameid, $botId)`:

1. `DBManager::getTacGamedata($botId, $gameid)` - the same load a human submit starts with.
2. Build a **masked decision view**: clone and run the per-viewer masking (`prepareForPlayer`) so the
   deciders cannot read what a human in that seat could not (P2). The unmasked object is kept only
   for constructing the submission.
3. The phase's decider (section 5) returns orders as plain data.
4. `AIOrderBuilder` turns them into the **same ship array `getShipsFromJSON` produces** (ship objects
   carrying `movement`, `EW`, `fireOrders` on systems, `power`, etc.). Extract the per-ship body of
   `getShipsFromJSON` into a reusable builder; `CAMPAIGN_PLAN.md` section 6.2 needs the same
   extraction - do it once, in whichever plan reaches it first.
5. Pre-flight: run the relevant validators on the built ships (`Movement::validateMovement` and
   `validateThrustPayment` in findings mode, `EW::validateEW`, `Firing::validateFireOrders`). Any
   finding -> log it to `tac_ai_log` as an AI defect and substitute the fallback orders for that unit.
6. Submit through a new `Manager::submitShipsForPlayer($gameid, $botId, $ships, ...)`: the body of
   `submitTacGamedata` after the JSON decode, split out so the human path and the bot path share
   every line (lock, transaction, `process`, cache touch, notifications).

Trap list for step 4, from existing project notes: POST-side ships carry no enhancements and no
loaded notes - build from the loaded gamedata's own ship objects rather than fresh instances;
`FireOrder` constructor argument order (see the how-to note); a server-made order with id -1 has no
identity; fire-order notes are dropped on POST; system ids are construction-order; the client has
commit-time side effects (`doIndividualNotesTransfer`, `construcGamedata`) that the browser applies
before POSTing - find each one the phase's `process` relies on and reproduce it in the builder.

### 3.3 Fallback orders per phase (P3)

| Phase | Fallback |
|---|---|
| Deployment (-1) | first free legal hex of the deployment zone, default facing, speed from the lobby default |
| Initial Orders (1) | no power changes beyond what is needed to clear negative power, all EW to DEW, no ballistics |
| Movement (2) | continue straight at current speed (the `drift` primitive), with a turn only if the straight path is blocked |
| Pre-Firing (5) | nothing |
| Firing (3) | no fire orders (automatic interception still happens server-side) |

---

## 4. The driver

`AIDriver::run($gameid, $budgetSeconds)` - the only entry point the rest of FV calls.

```
loop while time remains:
    load light game row (turn, phase, activeship, slots with cpu != NULL)
    if no CPU slot owes a submission for the current phase/active ship: break
    TacticalAI::play(gameid, thatBot)          // one submission
    Manager::advanceGameState(botId, gameid)   // exactly as the human path does after a submit
```

* **Where it is called.** In `gamedata.php`, after a human's `submitTacGamedata` returns (the common
  case: the human was the last to submit, the bot answers at once) and on a human's poll GET when the
  game row says a CPU slot is owed. Both gated on `Features::enabled('cpuOpponent')` and on the game
  having any CPU slot (one indexed query, cached in the existing APCu game key).
* **Budget.** Default 4 s per request, one submission at a time, so a long CPU turn spreads across
  polls. The client shows the bot as "thinking" using the existing waiting indicators.
* **Locking.** A per-game AI lock (`GET_LOCK('fv_ai_<gameid>', 0)`) so two polling humans cannot run
  the same bot twice. The existing player submit lock and game submit lock still apply inside.
* **Movement phase.** Movement is by initiative: the bot moves only when one of its units is the
  active ship (`activeship`), or its category is up under `SimultaneousMovementRule`. The driver's
  "owes a submission" test must mirror `MovementGamePhase` exactly, including the automated-control
  hand-off already in `process`.
* **All-CPU games** (self-play, section 11) are driven from the CLI harness, not from requests.
* **Stall guard.** If the same bot fails to complete the same phase three times, it submits the
  fallback; if that fails, the bot's slot surrenders and the defect is logged. A human is never left
  waiting on a broken bot.
* **State across phases.** `tac_ai_state (gameid, playerid, turn, data JSON)` holds the turn plan
  (target assignments, intended range band, withdrawal decision) so Firing agrees with what Movement
  set up. Deleted with the game (`deleteGames` - add the table there; it runs on every game.php load,
  so keep the delete keyed and cheap).

---

## 5. Decision design

Source layout: `source/server/ai/` - `AIDriver`, `TacticalAI`, `AIOrderBuilder`, `AIView` (masked
view + cached geometry), `ShipMover`, `ThreatMap`, `deciders/` (`DeployDecider`, `PowerDecider`,
`EWDecider`, `BallisticDecider`, `MoveDecider`, `FireDecider`), `handlers/` (per-system specials),
`AIConfig` (every weight and threshold), `FleetComposer`.

### 5.1 Shared evaluation: the engagement table

Built once per decision from the masked view and cached in `AIView`:

* For each (my weapon, visible enemy unit): in arc? in range? expected damage =
  `hit chance x getAvgDamage() x shots`, where hit chance comes from **`calculateHitBase` on a
  tentative `FireOrder`** - the real formula, including EW, range penalty, jinking, profile and
  terrain. Never re-derive hit chance in AI code.
* For each visible enemy weapon against each of my units: the same estimate, using only what the
  mask shows. Enemy EW of the current phase is hidden, so assume its last known split.
* Unit value = point cost x remaining combat value (`calculateCombatValue`).
* Threat of an enemy = expected damage it can deal to my fleet next turn; its vulnerability =
  expected damage I can deal to it divided by its remaining structure.

### 5.2 Deployment: one deployer, two users (`FleetDeployer`)

User direction, 2026-11-13: **the CPU's deployer doubles as an Auto-Deploy button for human
players.** So deployment is not written as AI code. It is a stand-alone server service,
`source/server/handlers/FleetDeployer.php`, with no dependency on anything else in this plan, and it
has two callers: `DeployDecider` (the CPU) and a player-facing endpoint.

**The service.** `FleetDeployer::plan($gamedata, $playerid, $slot, $options)` returns a list of
`{shipid, x, y, facing, speed}` for that player's undeployed units in that slot. It changes nothing.

* **Battlegroups.** A battlegroup is built around its capital ship(s). `options.groups` = how many
  to form, from 1 up to the number of capital ships (Capital and Enormous hulls). With no capital
  ships the largest hull size present anchors the groups instead. Capitals are dealt across groups
  by value, then every other unit is dealt to the group with the lowest total value so the groups
  come out balanced.
* **Formation inside a group**, centre outward (the user's sketch): capital ship(s) in the centre;
  HCVs flanking them; medium ships outside those; LCVs on the wings; fighter flights in a screen in
  front, or on their carrier's hex when the game expects them launched from it. Carriers and ELINT
  ships sit one row behind the centre. Groups are spread evenly along the deployment zone's long
  axis with a gap between them.
* **Zone and legality.** Positions come from the slot's own deployment zone and must satisfy
  exactly what `DeploymentGamePhase::validateDeploymentArea` enforces; terrain hexes and their
  footprints, already-placed units and stacking limits are avoided. Bases, OSATs and mines are not
  moved by it (mines keep their own Deploy Minefield tool); units arriving as reinforcements and
  units the player has already placed are left alone unless `options.replace` is set.
* **Facing and speed.** Facing toward the enemy zone. Speed is the fleet's common comfortable speed
  (a speed every unit in the group can hold with thrust to spare), so a group starts in formation.
* Deterministic for a given seed and options, so "Auto-Deploy" pressed twice gives the same layout
  and a "shuffle" option can vary it.

**Caller 1 - the CPU.** `DeployDecider` calls `plan` with a group count chosen by personality
(cautious: 1; balanced: 1 per 2 capitals; aggressive or skirmisher: 1 per capital) and submits the
result as its Deployment orders. Reinforcement groups are not used in v1.

**Caller 2 - the player's Auto-Deploy button.**

* A new **Auto-Deploy** button in the Deployment phase, placed at the bottom of the initiative GUI
  beside the existing Deploy Minefield button (that one is created in `gamedata.js` near the
  `mineBtn` code and driven by `renderer/phaseStrategy/MineDeployment.js` - follow the same
  pattern). Shown only when the player has undeployed units of their own this phase.
* Clicking it opens a small panel: number of battlegroups (1..capital count; hidden when the answer
  can only be 1), "Replace units I have already placed" (off by default), then Deploy.
* The client asks a new endpoint `autoDeploy.php` (`Manager::getAutoDeployPlan`, which checks the
  requester owns the slot and the game is in Deployment) and **applies the returned placements
  through the same client functions a manual placement uses** in `DeploymentPhaseStrategy`, so the
  units look and behave exactly as if the player had put them there.
* **It never commits the phase.** Nothing is written server-side by the endpoint. The player can
  drag any unit elsewhere, change facing and speed, press Auto-Deploy again, and commits by hand as
  usual. A passive notice says "Fleet auto-deployed - adjust and commit when ready".
* If Save Orders is in use, the auto-placed layout is saved as a draft like any manual one.
* The plan is computed on the server on purpose: one implementation for both callers, and the
  legality rules are not mirrored into the client.

**Independence from the CPU feature.** Auto-Deploy has its own flag, `$feature_flags['autoDeploy']`,
and does not need bot accounts, the driver or `ShipMover`. It can be built and released to players
as soon as `FleetDeployer` exists (stage T1a), long before the CPU opponent is ready - and player
use of it is the best test the deployer could get.

### 5.3 Initial Orders

* **Power.** Default configuration; if power is negative (damage), switch off systems in a fixed
  priority (non-firing-this-turn weapons, then secondary systems) until it is not. No boosting in v1
  except sensors where a surplus exists (later stage). Mind the lazy-window note: reactor state must
  be computed, not read from a UI-initialised field.
* **EW.** Pick primary targets from the engagement table. Split scanner output: DEW by threat faced,
  OEW on the targets this unit will fire at (largest expected gain per point, computed by evaluating
  the engagement table with and without the point), CCEW when enemy fighters are within reach.
  ELINT functions (SOEW / SDEW / disruption) get a handler at stage T7; before that an ELINT ship
  just runs OEW/DEW.
* **Ballistics.** Missiles and torpedoes are fire orders placed in this phase: launch at the best
  target in range when expected damage per round beats a threshold; conserve ammunition on Easy less,
  on Hard more (magazines are finite).
* **Fighters.** Launch from hangars on turn 1 if the fleet deployed them docked (v1: the CPU fleet
  deploys flights on the map, so no hangar operations are needed).

### 5.4 Movement

**`ShipMover` (legal move generator).** Given a ship and the game state it enumerates legal
manoeuvre sequences for one turn: speed change (accelerate / decelerate within thrust and
`Movement::canAccelerate`), straight moves, turns left / right where turn delay allows, slips where
legal, and the thrust channelled through the right thrusters for each (the allocation logic that
`Movement::calculateThrustStillReq` / `calculateAssignedThrust` / `sortThrustersAllocations` check).
Pivots and rolls arrive at stage T5; jinking for flights reuses `AutomatedMovement`.

* Start from `AutomatedMovement`'s primitives (`advanceHex`, `turnOrder`, `moveOrder`,
  `speedChangeOrder`, `slipOrder`, `isHexBlocked`) and generalise them from flights to ships: add
  turn delay (`Movement::calculateTurndelay`, `getTurnDelay`), turn cost by speed and class, thruster
  allocation, and critical effects on engines and thrusters.
* (As built, section 10.3: the server validator turned out to check far less than this
  paragraph assumes, and the real oracle is the client's `movement.js` run under node.)
  The oracle is the server validator: every sequence `ShipMover` emits must pass
  `Movement::validateMovement` and produce **zero findings** from `validateThrustPayment`. A harness
  (`shipMoverHarness.php`) fuzzes this across every ship class on the readiness list at many speeds
  and damage states. When this harness is clean the AI's moves are also a strong argument for finally
  flipping `$enforceThrustValidation` - but that is a separate decision.
* Flights: `AutomatedMovement` already builds legal flight moves; extend its intents rather than
  writing a second generator.

**`MoveDecider`.** For the unit to move:

1. Take its assignment from the turn plan (target, desired range band from its weapon profile,
   posture: close / hold range / kite / withdraw).
2. Ask `ShipMover` for candidate end states (position, facing, speed), pruned to a few dozen by
   beam search on distance-to-goal.
3. Score each: expected damage dealt next fire phase from that hex and facing (engagement table,
   arcs included) - expected damage received (enemy weapons that could bear, weighted by whether the
   enemy has yet to move this turn) + range-band term + terrain and collision avoidance (asteroids,
   moons, dust, mines it can see, map borders) + formation cohesion + keeping the strongest arc on
   the target and a damaged side away.
4. Take the best; Easy adds noise to the scores.

Initiative is respected for free: the bot moves when the engine says it is its unit's turn, with
whatever has already moved visible, exactly like a human.

### 5.5 Pre-Firing

Nothing in v1 (fallback). Systems that act in phase 5 get handlers with their faction at stage T7.

### 5.6 Firing

* For each ready direct-fire weapon list legal targets (arc, range, loading, line of sight - the
  same tests `Firing::validateFireOrders` applies) with expected damage from the engagement table.
* Assignment is greedy with a focus-fire bonus: prefer finishing a unit that the planned fire can
  destroy or cripple over spreading damage; prefer high value x vulnerability; fighters are engaged
  by anti-fighter weapons first.
* Weapons with interception ability and no good offensive shot are left unassigned so
  `Firing::automateIntercept` uses them. A defensive reserve fraction scales with incoming ballistics.
* Firing modes: choose the mode with the best expected damage at that range. Multi-turn charge and
  overload weapons: fire when ready, no holding in v1.
* Every order is built with the documented `FireOrder` constructor order and validated before submission.

### 5.7 Withdrawal and surrender

*Superseded by the user's rulings of 2026-11-21 (section 10.8): no fleet-wide withdrawal posture in v1 - "most ships fight until
they are destroyed or the game is won". A side surrenders only when none of its units can damage any enemy for 2 turns running
while it is behind on value (never on turns 1-2); a ship jumps out when it is expected to be destroyed next turn and its own fire
can't change that, has no usable weapon, or cannot get back into the fight (never with a Jump Engine more than 50% damaged).*

* ~~When the bot's remaining fleet value falls below `WITHDRAW_RATIO` of the enemy's and the trend is
  negative, posture becomes withdraw: open range, keep firing.~~ v1 then surrenders through the
  ordinary surrender path once out of effective range or when nothing can still fight.
* Stage T6 adds leaving by jump point for jump-capable ships (the existing jump-out flow), which is
  what the campaign prefers (survivors return to the map).
* The bot never surrenders on turn 1-2.

### 5.8 Difficulty and personality

| Level | Behaviour |
|---|---|
| Easy | score noise high; no focus-fire bonus; simple EW (half offensive - since 5826 the CCEW deterrent and OEW on the nearest ship - half DEW); fires ballistics whenever legal |
| Normal | as designed above |
| Hard | one-ply look-ahead in movement (assume each enemy's best reply), EW optimised per point, ammunition discipline, target priority by value x vulnerability, withdraws in good order |

Personality is a weight preset (`aggressive`, `balanced`, `cautious`, `skirmisher`) that changes
range band and withdrawal threshold. A faction has a default (also used by the strategic AI's
flavour table). All randomness comes from the seat's seed, so a game is reproducible.

**What tells the levels apart - from the user's notes on game 4750 (2026-11-19, section 10.7).
ACCEPTED by the user the same day ("Easy/Medium/Hard splits look good" - Medium is the code's
`normal`).** Each note is a player skill; Normal has those built in T5c (the numbers in brackets are
the notes), Easy lacks most of them, Hard adds what is still to build. Built at stage T5f.

| Skill (note) | Easy | Normal | Hard |
|---|---|---|---|
| 1-point CCEW deterrent, no more than 2 unless fighter-heavy (2) | yes, out of its offensive half (user, 5826: "Easy should just follow the same CCEW rule as Normal"; it put up none before) | yes | yes |
| No OEW on fighter flights unless only fighters are left beyond 10 hexes (user, 5826) | yes | yes | yes |
| No lock over half the points at 10-20 hexes unless no return fire can reach (user, 5826) | - (its lock is half its points anyway) | yes | yes |
| Fighters press home, priced from the enemy's real arcs and their own jinking (3) | T5c's pricing with the old damage count - bold (user, 4774: "fine for an Easy CPU"; replaces "the old caution") | yes, and warier (4774: 2 hexes astern of a CCEW ship, not 1) | yes, warier still and jinking at whatever level pays (4774: 3 off, jinking 4-6); flights work together - pile onto a damaged target, mutual support, by initiative (user, 2026-11-19) |
| A ship's damage counted against its primary structure plus the section a hit goes through (4774; was primary only) | - (bolder fighters, some wasted fire) | yes | yes |
| Fighters search for the hexes the enemy's guns cover least (4774) | - | a simpler search | the full search |
| Safe cruising speed (4) | half the overspeed cost | yes | yes |
| Concentrate the heavy guns (5) | spread (no heavy focus) | in Firing | in Firing, and Movement puts the fleet's heavy guns on one chosen target |
| Turn shortening (6) - "always useful (since any delay carries over to next turn as well) if you have the spare thrust on that turn, and you won't exceed thruster ratings" (user, 2026-11-19) | skips it | by default: the delay left at the end is always shortened with the thrust to spare within the ratings, and the search finds the double turns a shortened delay allows | the same |
| Extended turns, where possible (user, 2026-11-19) | - (an owed one completed) | - (an owed one completed) | yes - BUILT at T5f-5: ShipMover begins and completes one (mirror + oracle); never one next turn's engine could not complete |
| Slow pulse weapons wait for a better chance (7) | no premium | +10 | +10 |
| Soft locks and half DEW in close engagement (8) | half offensive (the deterrent, OEW on the nearest ship), half DEW | yes, and a soft lock on every enemy ship in reach (user, 5826) | yes, the same |
| An enemy moving after you is the more dangerous (9) | half weight (the T5a rule) | yes | yes, plus the one-ply reply |
| Anticipate where an enemy ship yet to move will end (user, 2026-11-20: "both Hard and Normal to have some level of movement predictions (to a simpler extent e.g. Normal might only consider that a ship might turn once)") - BUILT at T5f-7 | - (straight on) | straight on or ONE turn at any point of its move, whichever brings most of its guns' fire on the bot's units | the same measure over a wider set: Normal's plans and 80 of the enemy's own (speed changes, slips, two manoeuvres). (Weighing the enemy's trade instead was built first and dropped: the worst predictor in the T5f-9 audit) |
| Power: light weapons that cannot reach anything switched off early, the scanner or engine boosted, the weapons back on as the enemy nears (10) | - | - | yes - BUILT at T5f-4 (interceptors the enemy's shots need stay on) |
| Pivots and rolls (user, 2026-11-19: "still very rare, advanced types of movement ... probably only set Hard opponents to use Pivots or Rolls. Perhaps Normal could in the case of rolling to swap thrusters to turn in a direction it wanted next turn too, but otherwise no") - BUILT at T5e | stops one carried over, starts none | stops one carried over; rolls only to swap its side thrusters for the turn it wants next turn | stops one carried over; pivots (started and stopped the same turn) and rolls where the trade is worth it: hiding a damaged side, swinging guns round, the gravitic pivot, the thruster swap |
| Ramming (11) | never | last resort under the Desperate rule (T6): no usable weapon OR primary structure at or below 25%, only at a target it can reach this turn (user, 2026-11-21) - **NOT built: T6d deferred past v1** (the user marked T6 complete without it, 2026-11-21) | the same (not built) |
| EA Interceptors fired offensively - only when boosted, their fire control null against everything until then (user, 2026-11-21: "It's important that the AI follows this rule too") - BUILT at T7b | never boosts one, so never fires one | never boosts one, so never fires one | against a FIGHTER-HEAVY enemy (the CCEW rule's measure) with a flight in reach: one kept on interception per interceptable enemy-ship shot that could reach the ship, the rest boosted - never more than half, and only those whose arc faces a flight at the start of the turn, the most flights first (T7b-2) |

---

## 6. Special systems: the handler registry

* `AISystemRegistry` maps a system class (matched by `instanceof` on the server object) to a handler
  with optional hooks: `power()`, `ew()`, `initialOrders()`, `preFiring()`, `firing()`,
  `moveConstraints()`.
* No handler = the system is left at its default and never given orders (P4).
* A system may also be marked **blocking**: its faction cannot be played by the CPU without a
  handler because the default is illegal or self-destructive (for example a system that must be
  given a setting every turn). Blocking systems keep a faction off the readiness list.
* Handlers are small and independent; contributors who add a faction's systems can add its handler.

---

## 7. Faction readiness list

`AIConfig::$readyFactions`: faction string -> minimum stage and notes. A faction is on the list
when (a) `shipMoverHarness` is clean for all its hulls, (b) no blocking system lacks a handler,
(c) 20 self-play games against EA finish with no AI defect logged.

Suggested order, simplest technology first: Earth Alliance, Narn, Centauri, Drazi, Brakiri (gravitic
drive needs its mover rules - built at T5d, section 10.7), Dilgar, then raiders and civilians (needed by the campaign's Raider
faction - prioritise `raiders` right after EA), then Minbari (jammer, stealth handling), Abbai
(shields), Vree, and onward. Ancients and custom universes last.

The Create Game dialog offers only ready factions for a CPU slot; the campaign asks the same list
(section 9).

**As built (T7a, 2026-11-21 - section 10.9):** the list is `AIConfig::READY_FACTIONS` = **Earth Alliance, Raiders** (the user: "just
show EA and Raiders atm and add more as we tackle Stage T7"), spelt as the classes spell `$faction` - the factions the ShipMover, fire
and orders pairs were proven on by directory (EA, raiders; the other factions those directories hold are not on it). A faction is
chosen per slot in the lobby, not in Create Game: while a CPU player's slot is selected the Faction Picker lists only these, its
randomiser rolls only from them, the Store hides every unit ShipMover never plots for (bases, OSATs, mines, terrain, remote-controlled
flights, Mindrider drives - `ShipMover::unsupportedHullReason`), a saved fleet loads without the units a CPU cannot field, and the
server refuses a CPU fleet with any such unit when the slot is readied (`AIConfig::cpuUnitBlock`). Adding a faction = one string in
`READY_FACTIONS`, once it meets the criteria above - **stage T9 (section 10.10) is the plan for growing the list**, with the criteria
made a concrete gate and every faction's standing surveyed.

**Since T9 (2026-11-23, section 10.10 "T9 release"):** the list is Earth Alliance, Raiders and the Major factions - Dilgar Imperium,
Minbari Federation, Narn Regime, Centauri Republic, Centauri Republic (WotCR), Earth Alliance (Early), Orieni Imperium. A unit whose
role rests on a system the AI does not use yet is hidden as bases are (`AIConfig::CPU_MISSING_SYSTEMS` - breaching pods and other
boarding craft; a unit whose every gun is on the list, the Raiders' Jia) - the list of those systems, to work through one at a time,
is section 10.10 "Missing systems".

---

## 8. The CPU's fleet

Ruled by the user, 2026-11-12: **the game's creator may select a CPU slot in the lobby and build
its fleet by hand, or load a saved fleet into it; a CPU slot left empty is auto-composed, and an
auto-composed fleet must pass the Fleet Checker.**

### 8.1 Creator-built fleet (the lobby path)

* In the lobby the creator can select a CPU slot exactly as they select their own (the lobby
  already has a selected-slot concept, `gamedata.selectedSlot`). With a CPU slot selected, the
  ordinary buy dialog, Load Saved Fleet and Save Fleet all work on that slot; faction, points cap
  and ISD are the slot's. Only the creator sees those controls on a CPU slot; other players see the
  slot as "CPU (level)" with its fleet hidden, like any opponent's (A4).
* A CPU slot shows one of three states: **Auto-compose** (the default, nothing bought), **Custom
  fleet** (creator bought or loaded units), and a "Clear to auto-compose" action.
* Server: buying for a slot runs through `BuyingGamePhase::process`, which keys on
  `$gameData->forPlayer` and on each ship's `userid`. A new guarded entry
  (`Manager::submitFleetForCpuSlot`) accepts the creator's POST only if the requester is the game's
  creator, the slot's `cpu` column is set and the game is still in the lobby; it then stamps the
  ships with the bot's id and calls `submitShipsForPlayer` (section 3.2) **as the bot**. No other
  player's slot can ever be bought for this way.
* The CPU slot does not "ready" by itself while the creator is editing it. It is committed when the
  creator readies their own fleet: custom units are submitted as above; an empty CPU slot is
  auto-composed at that moment (8.2) and submitted the same way.
* Everything a human fleet may carry is allowed in a creator-built CPU fleet (enhancements, refits,
  officers, pre-battle damage). The AI ignores what it has no handler for (P4); the lobby shows a
  passive note listing bought items the CPU will not actively use.
* This is also the test path for stages T0-T7: no composer is needed to get two bots a fleet.

### 8.2 Auto-compose (`FleetComposer`, shared with the strategic AI)

`FleetComposer::compose($faction, $points, $isd, $seed, $profile)` returns a legal fleet as the same
ship array a lobby submit produces.

* Template-driven: per faction a few weighted templates (line fleet, carrier group, raider pack,
  defence group) expressed as role shares; roles are filled from the faction's classes by ISD and
  cost. Hull roles are derived from class data (size, hangar capacity, ELINT, weapon mix), with an
  optional hand-tuned override table.
* **It must pass the Fleet Checker.** Today the checker's rules run in the lobby client (fleet
  warnings are configured in `gamelobby.js`; `fleetchecker.php` is the reference page), so:
  * port the rules to a server class `FleetChecker::check($ships, $gameSettings)` returning the same
    list of problems the lobby shows (deployment-availability limits, hangar and fighter capacity,
    restricted and unique units, out-of-tier, required escorts, and whatever else the lobby checks -
    inventory the client rules first and port all of them, not a subset);
  * the composer is generate-and-test: build a candidate, run `FleetChecker`, repair the named
    problem (swap or drop the offending unit, add the hangar or escort it lacks) and repeat, up to a
    cap; fall back to the faction's simplest template if it cannot converge. It never returns a
    fleet with a checker problem, and spends within 2% of the points without exceeding them;
  * a parity test feeds a set of saved fleets to both the client checker (run under node) and the
    PHP port and fails on any difference, so the two cannot drift. Once the port exists the lobby
    may later ask the server for its verdict, but that is not part of this plan.
* Auto-composed fleets are plain: no enhancements, refits or officers.
* Carriers are composed with their fighters, and those flights fit the hangars (the same capacity
  rules, in boxes, that Hangar Operations uses).
* Used by `STRATEGIC_AI_PLAN.md` for Raider fleets, neutral garrisons and CPU empire purchases
  (there the result is turned into `camp_unit` rows and priced by `CampaignPricing`).

---

## 9. Campaign wiring (stage T10 - was T9 until 2026-11-22, when "more factions" took T9)

The campaign needs four things from this plan, and nothing else:

1. `TacticalAI::canPlay($faction)` - the readiness list plus the `cpuOpponent` flag. The campaign
   offers the Fight stance against an NPC only when this is true (`CAMPAIGN_PLAN.md` section 7).
2. A bot seat: `BattleBridge::createGame` calls `AISeats::seat($gameid, $slot, $level, $personality,
   $seed)`, which takes a free bot account, seats it with `takeSlot` and writes the `cpu` column.
   The NPC empire's fleet is injected through `BuyingGamePhase::process` for that bot id, exactly as
   for a human (campaign 6.2).
3. Campaign-aware behaviour, read from the game's `campaign` rule and nothing else:
   * withdrawal prefers jump-out over surrender, so survivors are harvested back to the map;
   * the 12-TT pause (campaign 6.3) needs no AI support: a paused game owes no submissions, so the
     driver idles;
   * units held in hyperspace as reinforcements: the strategic AI decides at battle set-up; in v1 the
     tactical AI brings them all in at the first legal opportunity.
4. `FleetComposer` (section 8).

Raider behaviour in battle is the `skirmisher` personality at the campaign's difficulty.

---

## 10. Stages

Nothing is committed by the implementer. Every stage runs the replay harness (ordinary games
unchanged) and the self-play harness.

| Stage | Deliverable | Done when |
|---|---|---|
| **T0 Skeleton** ✅ COMPLETE | `Features` helper and the `cpuOpponent` off switch (first), `db/cpuOpponent.sql` (bot accounts, `cpu` column, `tac_ai_state`, `tac_ai_log`), `submitShipsForPlayer` split, the extracted ship builder, CPU slot in Create Game and the creator-built fleet path (8.1) behind the flag, `AIDriver`, fallback orders for every phase, self-play CLI harness, the flag-off harness (section 2) | With the flag off nothing is visible or reachable and the replay harness is byte-identical; with it on, the creator buys a fleet for a CPU slot and two bots play a full game to FINISHED on fallbacks with zero rejected submissions |
| **T1a Deployer + Auto-Deploy** ✅ COMPLETE | `FleetDeployer` (battlegroups, formation, legality), `autoDeploy.php`, the Auto-Deploy button and panel behind the `autoDeploy` flag, `DeployDecider` calling the same service; `deployerHarness.php` (every plan across zone shapes, terrain and fleet mixes passes `validateDeploymentArea`; deterministic; never touches placed units unless asked) | A player clicks Auto-Deploy, gets a legal formation, adjusts it and commits by hand; the phase is never committed for them. **Releasable to players on its own** |
| **T1b ShipMover** ✅ COMPLETE (uncommitted) | `ShipMover` for standard hulls (speed, straight, turns, slips, thruster allocation) | `shipMoverHarness` clean for all EA and raider hulls |
| **T2 Firing** ✅ COMPLETE (10.4) | engagement table, `FireDecider`, interception reserve | Bots destroy each other; a human loses to an EA bot by standing still |
| **T3 EW and power** ✅ BUILT (uncommitted, 10.5) | `EWDecider`, `PowerDecider` (negative-power handling) | Bot EW passes `validateEW` in every self-play turn; hit rates rise measurably versus T2 |
| **T4 Ballistics and fighters** ✅ COMPLETE (uncommitted, 10.6) | `BallisticDecider`; flight tactics (escort, strike, intercept) - built on `ShipMover`, not `AutomatedMovement`, whose rows the page never checked | Missile and carrier fleets fight sensibly |
| **T5 Movement search** ✅ COMPLETE (user, 2026-11-20; from T5e on uncommitted, 10.7) | `MoveDecider` scoring, range bands, terrain (drifting too) and mine avoidance, pivots and rolls, gravitic hulls; closing with the enemy (the T2 pursuit question, ruled into T5); moving to bring the most direct-fire weapons into arc within the turn AND the most ballistic launchers to bear for next turn's launch (user, game 4701) | Hard beats Normal beats Easy in self-play ladders at >= 60% - Normal over Easy met (15 of 16); Hard over Normal a few points (50-62% on 42x30), ACCEPTED by the user for the first version (T5f-9) |
| **T6 Withdrawal** ✅ COMPLETE (user, 2026-11-21: "for v1 of CPU opponents"; committed 442121f12; 10.8) | surrender, jump-out (the user's rulings of 2026-11-21 - no fleet-wide withdrawal posture in v1); ~~last-resort ramming~~ (T6d - deferred past v1, then BUILT at T8 at the user's request, 10.11); the user's notes on game 4774 turns 3-4 (T6a); a side down to its fighters keeps shooting (T6g) | No game runs past the turn cap for lack of a decision - met by the T6 batches (10.8) |
| **T7 Specials** (STARTED 2026-11-21, 10.9 - T7a/T7b/T7b-2 BUILT; committed by the user as 56b03b2d3) | the readiness list in the lobby and on the server (T7a); the EA Interceptors' offensive mode (T7b, T7b-2). Since 2026-11-22 the faction-by-faction work - handlers, the weapon families new factions need, the readiness list growing - is stage T9 | T7a and T7b play-tested by the user |
| **T8 Skirmish release** ✅ COMPLETE (user, 2026-11-22; uncommitted, 10.11 - timed on the live host; the Beta is the user's; the `FleetChecker` port re-scoped to T10 by the user) | server `FleetChecker` port + parity test; `FleetComposer` (auto-compose as the default for an empty CPU slot); level and personality pickers; lobby and games-list display of CPU seats; docs page; **a decision-time budget for live** (user, 2026-11-19: "reasonable decision time is important for live server" - section 12; the per-ship budget and two-pass judging were BROUGHT FORWARD to T5f-8 on 2026-11-20); **a HOLISTIC REVIEW of decision efficiency** (user, 2026-11-20: "in Stage 8 plan a holistic review of how we can make AI decisions as efficient as possible to keep decisions times reasonable" - the plan for it is in section 12) | Every auto-composed fleet across all ready factions and a range of point values passes the Fleet Checker; every decision within the budget on live-sized fleets, timed on the live host; Beta on live via `betaTesters`, then the flag |
| **T9 More factions** (PLANNED 2026-11-22, 10.10 - the user, with the campaign paused) | the factions beyond EA and Raiders, group by group in the user's order: Major, League, Minor, Ancients, Custom - the gate tooling (T9a), the cross-faction enablers each group needs (E1-E8: split-shot arrays, ammunition weapons, special rounds, special hit chance, hex-targeted weapons, Pre-Firing, power models, system handlers - the section 6 registry is built with the first handler), and each faction's gate | Each faction released meets the 10.10 gate (the section 7 criteria made concrete) and is play-tested by the user before its string joins `READY_FACTIONS` |
| **T10 Campaign wiring** (was T9 until 2026-11-22) | section 9 | A campaign battle against a Raider fleet is played manually and harvested |

T0-T3 produce an opponent that is already useful for new-player onboarding (a roadmap item in its
own right: a first game with no human needed).

### 10.1 Stage T0 as built (2026-11-13) - COMPLETE, committed 5807979b8

Needs `db/cpuOpponent.sql` and `$feature_flags['cpuOpponent']` (or a beta list) to do anything.
Harnesses, all in `tests/ai/`: `selfPlayHarness.php` (two bots to FINISHED on fallbacks),
`cpuLobbyHarness.php` (the Create Game / lobby path below), `flagOffHarness.php`.

* **Create Game.** Each slot but slot 1 has a "CPU player" tick, printed only when
  `AISeats::allowedFor` the viewer and hidden in a ladder game. `Manager::createGame` hands the
  posted slots to `AISeats::seatMarkedSlots`; a slot that cannot be seated (no bot left - there are
  eight) is logged and stays an ordinary open slot. The tick is not part of a saved preset.
* **Lobby.** The payload gains `cpuSlots` (`{slot: {level}}`) only while the game is in its lobby
  and has a CPU seat (`Manager::addCpuSlots`); `gamelobby.js` copies it by name. The creator gets
  Select on a CPU slot and then buys, loads and readies it like their own
  (`gamedata.canEditSlot`). Units bought for it keep the creator's id in the page;
  `gamedata.php` sends a Ready with `cpuSlot` to `Manager::submitFleetForCpuSlot`, which keeps
  only that slot's units, stamps them with the bot's id and commits as the bot.
* **Differences from section 8.1 - all three ACCEPTED by the user, 2026-11-13:**
  * a CPU slot is readied by its own Ready press (select it, press Ready), the way a player
    holding two slots readies each - not together with the creator's own fleet. The combined
    commit needs auto-compose for an empty slot, which is T8;
  * so an empty CPU slot blocks the start until a fleet is bought for it. No "Auto-compose" /
    "Custom fleet" / "Clear" states, no note of items the CPU will not use, no level picker
    (every seat is `normal`), and a CPU slot cannot be added or removed in the lobby;
  * when the creator leaves the lobby, every bot leaves with them and the slots open up
    (`AISeats::releaseAbandoned`) - nobody else could commit their fleets.
* `cpu` is not on `PlayerSlot` or in its SELECTs (it would ship on every slot of every game, and
  break a database without the migration); AI code reads seats with its own query.
* `AIOrderBuilder` writes the plain array a browser posts and runs it through `ShipPayload`,
  rather than reusing loaded ship objects (section 3.2's trap list): `process()` is written for
  POST-side ships.
* No pre-flight validators yet (3.2 step 5) - the fallbacks cannot fail them; they come with the
  deciders. Fallback gaps left to their stages: negative power is not cleared (T3), movement never
  turns away from terrain or the map edge (T1b).

### 10.2 Stage T1a as built (2026-11-13) - COMPLETE, committed 9537a8b72

Flag: `$feature_flags['autoDeploy']` (on in `docker/php/varconfig.php`). No migration.
Harness: `tests/ai/deployerHarness.php` (188 checks over seven zone / terrain / fleet scenarios;
`--show` prints each layout as a text map).

* **`source/server/handlers/FleetDeployer.php`** - `plan($gamedata, $playerid, $slot, $options)`,
  `maxGroups`, `eligibleUnits`, `inZone`. Options: `groups`, `unitIds`, `occupied`, `seed`.
  Formation per group: line at depth 0 (lead anchor in the centre, then by hull size outward,
  2 hexes apart, 1 when the zone is short), carriers and ELINT hulls at depth -1, line overflow
  behind them, flights at depth +1. A wanted hex that is not acceptable becomes the nearest one
  that is; a unit with nowhere to go is left out of the plan.
* **Legality.** Each hex passes `validateDeploymentArea` and a server copy of the page's box test
  (`DeploymentPhaseStrategy` `getValidDeploymentCallback`), which is tighter at the edges - the two
  were compared over 43 million hex/zone cases and agree exactly. Terrain footprints are never
  stood on; two hulls never share a hex (the page's rule; flights may share).
* **The CPU.** `ai/deciders/DeployDecider` plans each of the bot's slots (groups by personality)
  and hands the result to `AIFallback::deployment`, which still places what the formation does
  not: bases, OSATs, mines and any unit left out. `TacticalAI::play` uses a decider only on a first
  attempt; a decider that throws, and every retry after a refusal, plays the plain fallback.
* **The player.** `autoDeploy.php` -> `Manager::getAutoDeployPlan` (flag, ACTIVE game, phase -1,
  holds a slot, not yet committed; writes nothing). `game.php` prints
  `window.fvFeatures = {autoDeploy: true}` only when the flag is on for the viewer.
  `client/renderer/phaseStrategy/AutoDeploy.js` draws nothing itself: `gamedata.drawIniGUI` adds
  the button after Deploy Minefield. The panel (battlegroup stepper, "Replace units I have already
  placed") is skipped when there is nothing to choose. Placements go through
  `shipManager.movement.deploy` + `onShipMovementChanged`, then facing and speed are written on the
  unit's `deploymove`; the commit button is armed by `validateAllDeploymentGlobal`, as a manual
  placement arms it. The page sends its own uncommitted placements as `occupied`.
* **Rulings, 2026-11-13 (after the user's play-test - "the autodeploy button seems to work well"):**
  * *Speed.* A group's hulls share the units' starting speed, lowered while any of them would
    need its whole engine output for one turn at it, but **never below 3** ("even cumbersome
    ships should start at speed 3 or 4, e.g. Hurr and Grome vessels"); never raised. **Flights
    start faster** ("often 6-9"): two thirds of the slowest flight's thrust, kept within 6..9.
    Constants `MIN_SPEED`, `FLIGHT_MIN_SPEED`, `FLIGHT_MAX_SPEED`.
  * *A dedicated carrier* is a hull with 36 or more fighter boxes
    (`FleetDeployer::CARRIER_MIN_FIGHTERS`); an Omega (24) stays on the line. A group's lead anchor
    is never moved to the support row. ACCEPTED.
  * *Long axis* decides the line even when the enemy lies along it (a tall zone with the enemy to
    the north gives a column, not a line abreast). ACCEPTED.
  * The deployer never reads where an opponent stands - only terrain and its own team.
* **Known gap, to fix later (user, 2026-11-13): Hunter-Killers.** Flights always form the screen;
  "on their carrier's hex when the game expects them launched from it" is not built. So a flight
  that should START IN A HANGAR - Orieni Hunter-Killers (`remoteControl`) above all - is put on
  the map by both callers unless the player has already queued its deploy-start dock (queued
  craft are left alone). On the map, HKs beyond the control nodes' capacity start Uncontrolled
  (`HkControlNodeOrieni::resolveDeploymentShortfall`). The fix is for the deployer to dock such
  flights (the deploy-start dock in `DeploymentDock.js` / `HangarOps`) instead of placing them.
* **Not decided: a "shuffle" control.** Pressing Auto-Deploy twice gives the same layout. The
  server can already produce variants (`seed`: mirrors the wings, changes which group stands
  where - the bot uses its seat's seed), but the panel has no button for it.
* **Sticky button footer (user request, same day).** `gamedata.drawIniGUI` now puts Deploy
  Minefield, Auto-Deploy and Manage Reinforcements in one `#iniGuiActions` footer, sticky to the
  bottom of `#iniGui` (tactical.css), so a long initiative list scrolls behind the buttons.

### 10.3 Stage T1b as built (2026-11-13) - COMPLETE, uncommitted

No flag of its own (it is reached only through a CPU seat), no migration. `source/autoload.php`
regenerated with phpab for the two new classes.

* **`source/server/ai/ShipMover.php`** - a port of the client's movement rules onto the server's
  ship objects. `forShip($ship, $gamedata)` (null = not a hull it handles), `options()`,
  `apply($action, $extraThrust)`, `finish()`, `rows()`, `state()`, `path()`, `enumerate($limit)` /
  `plans()`. Orders: `accel`, `decel`, `turnleft`, `turnright`, `slipleft`, `slipright`, `move`.
  Handles thruster criticals, lost thrusters, engine damage / boost / shutdown, agile snap turns, a
  rolled or stern-first ship, Elite / Poor Crew, a Quartermaster and docked LCVs. Never orders a
  pivot, roll, jink, extended turn or jump-out. Refuses (`unsupportedReason`): flights, bases,
  OSATs, mines, terrain, gravitic and Mindrider drives, a ship carrying or riding another unit.
  Its one policy: **it never overthrusts** a thruster (the page allows twice the rating).
* **⚠️ The oracle is the CLIENT, not the server validator** (this corrects section 5.4).
  `Movement::validateThrustPayment` only checks that the cost a row STATES was paid through
  thrusters that exist; `validateMovement` does nothing in the Movement phase. What a turn costs,
  turn delay, the engine's budget, thruster ratings and "speed changes first" are enforced by
  `movement.js` alone. So the harness has two halves:
  * `tests/ai/shipMoverHarness.php` (no database; runs in the php container or an `fv-phpcheck`
    image): every hull x 35 situations x random plans + the search, against all the server CAN
    check; writes the plans to a file.
  * `tests/ai/shipMoverOracle.js` (node, host): loads the REAL `movement.js`, `ships.js`,
    `systems.js`, `power.js`, `criticals.js`, `mathlib.js` and replays every plan as clicks. At each
    step the page's own `can*` answers, turn delay and thrust left must equal ShipMover's (both
    directions), the page's row must equal ShipMover's, and every point of thrust must be
    accepted by `assignThrust`.
  * Result, EA + raiders (`--factions=EA,raiders`, the default): **128 hulls, 4,480 situations,
    26,880 plans, 205,195 steps - all agree** (first run; final figures below). Not plotted there: 23 flights, 9 bases, 12 OSATs,
    6 gravitic hulls. Mutation pass (32 broken copies of ShipMover): 28 caught, 26 of them ONLY
    by the client replay; the 4 survivors are equivalent code.
* **`ai/deciders/MoveDecider.php`** - the T1b rule, not tactics: a ship holds its course unless
  that flies it into a blocked hex or off the map (this turn, or next turn's straight
  continuation), and then takes the plainest plan that does not. `TacticalAI::play` uses it for
  Movement on a first attempt; a refusal retries with `AIFallback` (straight on), which now
  shares `AIFallback::straightRows`. Weights in `AIConfig::MOVE_*`. The map edge is
  `gamespace` halved (no edge on an unlimited map). Checked DB-free on all 128 hulls (open space
  = straight on; a rock ahead is missed; the ship stays on the map when a plan allows).
* **Live data (section 13) used:** `tests/ai/analysis/hullTable.php` + `liveMovementCheck.js` read
  the export FILE directly (nothing imported). Of 20,852 real turn / slip / speed-change rows on
  hulls ShipMover plots, **all but 5 carry exactly the cost ShipMover charges** (4 on
  PolarenGratherin, 1 on gaimGeun - a changed hull or a docked LCV). How people fly, for T5's
  priors: 59.5% of ship-turns include a turn, 33.0% a sideslip, 35.4% a speed change; 22.7% of
  turns are paid over their cost to shorten the delay; 4.2% of ship-turns overthrust a thruster;
  mean speed 5.3 (medium 6.1, heavy 5.2, capital 4.7).
* **Played for real (same day, once the stack was up):** self-play in both movement modes, no
  refused submission and no defect; in a kept 31-turn game every hull turned back at the map edge
  (8 turns each), held speed 5 and never left the 42x30 map. `cpuLobbyHarness`, `flagOffHarness`,
  `deployerHarness` (195) green; replay check 203/205, the two failures being the played-on games
  4504 and 4507 as before. Final harness figures: **128 hulls, 4,867 situations, 29,202 plans,
  215,070 steps, all agree** (three Engine Shorted situations were added, see below).
* **Play-tested by the user, game 4626 (human Bintak v CPU G'Quan, 42x30, 10 turns):** the bot flew straight for six turns, then came round at the west and north edges with three 60-degree turns (4 thrust each: 2 aft, 2 side, within every rating, each after its turn delay), reached q -21 and r -15 - the edge itself - and never crossed it; 5 hexes every turn; no row in `tac_ai_log`. It also made two sideslips that gained it nothing (a tie in the T1b scoring; T5 replaces that scoring).
* **Two things the first real games showed, both fixed:**
  * *The search was biased.* Working on "fewest hexes left" first filled the 300-plan quota with
    slowed-down ships before one turn at cruising speed was tried, so the bots braked to a halt
    at the edge. `enumerate` is now PLAINEST FIRST (fewest manoeuvres, then nearest the end).
  * *Scoring.* `MoveDecider::score` counts next turn's overshoot per hex past the edge
    (`MOVE_COST_PER_HEX_OVER`), so a first 60-degree turn already scores better, and charges for
    speed given up (`MOVE_COST_SPEED_LOST`), so a ship that can turn does not brake instead.
* **The self-play harness's turn cap had never really been exercised:** T0 / T1a games ended
  because the fleets flew straight past each other until every enemy was 100+ hexes away
  (`TacGamedata::isFinished`). Ships that stay on the map never do that, and there is no firing
  before T2, so every game now runs to the cap. A game-ending surrender writes `SURRENDERED`
  (`FINISHED` only follows at a later turn change); the harness now accepts that after the cap.
  Use `--turns=14` or so until T2.
* **Client fix, at the user's request (2026-11-13): Engine Shorted and a second engine.**
  `deleteSpeedChange` (movement.js) treated ANY speed-change row of the turn as one the player
  could take back, including Engine Shorted's PRETURN row, whose `value` is the whole speed
  gained rather than 0 / 1 - so on such a turn an Accelerate or Decelerate click removed the
  ship's last row (that row itself, when it was the only one) instead of changing speed with the
  thrust of another engine. It now skips preturn and forced rows. ShipMover mirrors it, and the
  harness builds the row with `Movement::doStuckEngine` itself. (That push may run the main
  thrusters over their rating by the engine's own doing; the overthrust checks allow for it.)
  **Not tried in a browser.**
* **Left for later stages:**
  * No pre-flight validators (3.2 step 5): ShipMover's rows are proven by the harness instead.
  * The bot does not use extra thrust on a turn (ShipMover can).
  * **T5 (user, 2026-11-13):** gravitic hulls (Brakiri, and the 6 in the raider lists) together
    with pivots and rolls; avoiding mines the bot can see and terrain that moves (Terrain Drift);
    a search aimed at a goal instead of the 300 plainest plans. Terrain and Enormous units that
    stand still are avoided already (`blockedHexes`).
  * **T5 - OVERTHRUST (user ruling, 2026-11-13).** "No overthrusting could be limiting ... having
    to overthrust to turn to avoid leaving the map, or otherwise making a desired turn, would be
    preferable and is how players will generally use it." So the never-overthrust policy is a
    T1b default, not a rule: in T5 ShipMover takes an allowance (the page's own ceiling is twice a
    thruster's rating, already ported in `click()`; only `clickBest()`'s "never beyond its
    rating" line holds it back), plans say how far over each one goes, and `MoveDecider` weighs
    that against the critical roll it invites (`Criticals`: one test per overthrust thruster, the
    excess added to the roll). Order of preference stays: within rating first, overthrust only
    when the within-rating plans are worse by more than the risk is worth - always to stay on the
    map or off terrain. Live data: players overthrust in 4.2% of ship-turns. The harness's
    "within its rating" checks then become "within the allowance asked for".
  * Flights still fly straight (and so off the map): their moves are `AutomatedMovement`'s, T4.
  * ShipMover re-reads its rows on every question - simple, and slow for a deep search.

### 10.4 Stage T2 as built (2026-11-14) - COMPLETE, committed 15624ab89

**Play-tested by the user, game 4679 (human Hyperion v CPU Battlewagon + Dragonship, 48 asteroids,
3 turns), checked 2026-11-15:** 7 fire orders over three Fire phases, every one in arc, in range
and with a clear line of sight at the positions of its turn; a 2-turn Particle Cannon fired on
turn 1 was rightly silent on turn 2; no `defect` row in `tac_ai_log`; the two turns with few orders
are the Hyperion's interceptors and 6 DEW pulling most shots under the thresholds. (To re-check a
past turn, take positions from `tac_shipmovement`: the loader keeps only turn 1, N-1 and N.)

No flag of its own, no migration. `source/autoload.php` regenerated for the two new classes.

* **`source/server/ai/AIView.php`** - the engagement table. `myUnits()`, `enemies()`,
  `unitBlock()` / `weaponBlock()` (why the bot leaves a unit's or a weapon's guns alone, or null),
  `modes()`, `shot($unit, $weapon, $target, $mode)` (null, or chance / orders / shots / damage
  class / expected damage), `why()`, `value()`, `healthLeft()`, `threat()`. Hit chance is
  `Weapon::calculateHitBase` on a tentative order, never re-derived.
* **⚠️ Firing legality is the CLIENT's too** (the same finding as 10.3, for the other phase).
  `Firing::validateFireOrders` only checks that the weapon id names a weapon; arc, range, line of
  sight, "loaded", "switched on", "destroyed" and the fire-control nulls are asked by
  `weaponManager.selectWeapon` / `targetShip` alone, and the server resolves whatever it is sent.
  So `AIView::shot` is a PORT of those two functions, and the harness has two halves:
  * `tests/ai/fireHarness.php` (no database): every hull - flights, bases and OSATs included - at
    the origin with four enemies (Omega, OlympusBeta, Tethys, a Starfury flight) at random ranges
    (the same hex included), bearings and facings; EW as a commit leaves it; weapons empty, off or
    shot away; rolled; disabled; a rock in the line. Asks AIView about every weapon x mode x enemy,
    runs `FireDecider::plan`, and pushes its entries through `ShipPayload` + `validateFireOrders`.
  * `tests/ai/fireOracle.js` (node, host): loads the REAL client (`weaponManager.js`, `ew.js`,
    the `Ship` / system / weapon models and what they lean on), builds each game from the stripped
    gamedata + `BlueprintCache` blueprints as `model/ship.js` does, and clicks every pairing.
    A shot AIView has must come out of the page as the same orders (count, mode, shots, damage
    class) at the same hit chance; a pairing AIView refuses for geometry or weapon state the page
    must refuse; every PLANNED shot must be one the page builds.
  * Result, EA + raiders: **178 hulls, 1,068 situations, 54,300 pairings: 5,376 shots built
    identically, 34,195 refused by both, 2,888 planned shots all page-legal, 0 differences.**
    Hit chance agrees to the percent on 5,289 of 5,437 orders; the other 148 are the edge case below.
    Mutation pass (12 broken copies of AIView): 11 caught, every one ONLY by the client replay;
    the twelfth (a disabled ship firing) had no situation then and has one now.
  * **All 2,656 hulls of every faction (informational, `--factions=all --rounds=2`): 239,358
    pairings, 23,065 shots agree, 1,026 differences** - special weapons whose rules live in their
    own client class (Chaff Launcher, Quad Pulsar and others). That is what the readiness list
    (section 7) is for. ⚠️ Nothing enforces that list before T8: a creator can buy any faction for
    a CPU slot today, and a non-EA / raider bot will fire with a port that is not proven for it.
* **What the bot fires (principle P4).** Loaded direct-fire weapons with nothing special about
  them, uncalled, at an enemy, in the best of the weapon's firing modes. Left alone: ballistics
  (T4), ramming, boarding, jump engines, hex-targeted, pre-firing, split-shot, split / special
  arcs, linked groups, exclusive weapons, Support, limited ammunition, special hit or range
  calculation, fixed fire control, compound (dual / duo) weapons, anything overloading. Units
  left alone: disabled, stealth hulls, mines, Hunter-Killers, attached or skin-dancing units,
  units jumping out or riding a docking bay. A Heavy Laser that is not overloading is a plain gun.
* **Flags the page reads are often protected on the server**, and one has a default that means
  nothing (`Weapon::$specialArcs` is true on every weapon). `AIView::flag` reads a public
  property as it is and anything else only from the weapon's own `stripForJson` - what the page
  is actually sent. Some flags exist ONLY in the client's weapon classes (`hasSpecialTargeting`
  ...): the server cannot see those at all, and only the oracle finds them.
* **The edge case.** A shooter exactly on a line between two of the target's sections (every
  30 degrees round it): the defender chooses the section hit, so the server prices the shot on
  the section it picks while the page uses the forward / aft profile - up to 15 points apart,
  either way (a difference that exists for human players too). `AIView::onEdge` marks such
  shots and `FireDecider` wants `FIRE_MIN_CHANCE_EDGE` (20) for them, so it never posts one the
  page would have refused as "no chance".
* **Two tests for "disabled".** The server never calls an OSAT disabled; the page can. The page
  all but never calls a base disabled; the server can. The bot fires only when both let it.
* **`ai/deciders/FireDecider.php`** - greedy: the best shot on the board is taken, its expected
  damage booked on the target, the rest judged again. Score = worth (share of the target the
  volley should remove x the target's value) x focus (`FIRE_FOCUS`, until the plan kills the
  target `FIRE_OVERKILL` times over) x threat (`FIRE_THREAT_WEIGHT`). Thresholds
  `FIRE_MIN_CHANCE` 5, `FIRE_MIN_CHANCE_SLOW` 20 for a weapon that takes more than a turn to load.
  **Interception reserve:** a gun with an intercept rating that gets no order is used by
  `Firing::automateIntercept`, so holding back is not ordering - one interceptor per incoming
  ballistic shot (`FIRE_RESERVE_PER_BALLISTIC`), and any interceptor with no offensive shot of
  `FIRE_INTERCEPTOR_MIN_CHANCE` (30) or better.
* **The masked view (P2, 3.2 step 2) starts here:** `FireDecider::orders` runs
  `prepareForPlayer()` on the bot's own load before deciding (the object is thrown away after).
  Deployment and Movement still decide on the unmasked load, as in T1.
* `AIOrderBuilder::fire` writes the row `targetShip` builds (one per gun; `x` / `y` the strings
  "null", as the page sends them). `TacticalAI` logs one `decision` line per Fire phase to
  `tac_ai_log` ("12 weapons at 2 of 4 enemies; 6 held to intercept").
* **Gates, 2026-11-14:** self-play both movement modes with no refused submission and no defect;
  `cpuLobbyHarness`, `flagOffHarness`, `deployerHarness` (195) green; replay check 203/205
  (4504 and 4507, the played-on games, as before). `selfPlayHarness` gained `--map=WxH`.
* **⚠️ OPEN - the "done when" is half met.** The bots shoot each other whenever they are in
  range (a kept 42x30 game: 24 orders, 18 hits on turn 3; a Hyperion took 231 damage over the
  game), but T1b's movement only holds a course: the fleets pass, fly to opposite edges, come
  round and meet again about every 11 turns. Four 42x30 games all reached the 40-turn cap; on a
  24x16 map one of three was fought to a finish (54 turns). So "bots destroy each other" happens
  slowly, and "a human loses by standing still" has not been tried. Closing on the enemy is
  MoveDecider scoring - stage T5 in the table above. ~~Needs a ruling~~ **Ruled 2026-11-15: left to
  T5**, which is next.
* **Left for later stages:** called shots; holding a slow weapon for a better shot next turn
  (T5's look-ahead); levels (Easy's "no focus-fire" is one constant); the pre-flight validators of
  3.2 step 5 (the harness proves the orders instead); `threat()` prices an enemy weapon in its
  current mode and ignores whether it is loaded.
* **Amended 2026-11-15 by the user's play-test feedback - see 10.6 A:** firing floors by loading
  time and a one-turn look-ahead for slow guns (replacing `FIRE_MIN_CHANCE_SLOW`), plasma range
  damage priced, and interception modelled as the server allocates it (`InterceptModel`).

### 10.5 Stage T3 as built (2026-11-15) - BUILT, uncommitted, not yet play-tested

No flag of its own, no migration. `source/autoload.php` regenerated for the two new classes.

* **⚠️ Power and EW legality are the CLIENT's too** (the finding of 10.3 and 10.4, for the third
  phase). `EW::validateEW` returns true for anything, and the server has no twin of the power
  balance: "too many EW points", Restricted EW, an LCV's sensors, who may be locked on to, and
  "turn systems off before you commit" are all said by `ew.js`, `power.js` and the commit gate in
  `gamedata.js`. So both deciders are PORTS of those, with a harness in two halves:
  * `tests/ai/ordersHarness.php` (no database): every ship hull at the origin with four enemies,
    in six kinds of trouble (none; a reactor short by 2 to 34; Restricted EW and a gun shot away;
    the scanner hurt or dead with "sensors down"; weapons forced off and a deep deficit; a
    disabled ship or a reactor shut down). Takes the whole decision (`TacticalAI::initialOrders`)
    and pushes it through `ShipPayload`.
  * `tests/ai/ordersOracle.js` (node, host): loads the REAL client, starts the turn as the page
    does (`repeatLastTurnPower`), and makes the bot's orders as a player would. Every system the
    page forces off must be in the bot's rows; every other "off" row must be one the Off button
    allows, on a ship in deficit, and never one more than the deficit needs; the commit gate
    (`getShipsNegativePower` and its neighbours) must pass; every OEW / CCEW point is clicked in
    (`ew.AssignOEW`, `ew.assignEW`); `convertUnusedToDEW`, `checkRestrictedEW` and
    `checkLCVSensors` must pass; and the page's EW rows must then equal the bot's, DEW included.
  * Result, EA + raiders: **155 hulls, 930 situations, 0 differences** - 465 ships in deficit, 1,520
    systems switched off for it, 558 rows the page forces, 261 offensive EW rows (461 points).
    Its first run found two real differences, both fixed: mines get the page's forced-off rows
    too, and the scanner figure (next point).
* **`ai/deciders/PowerDecider.php`.** `plan()` returns the power rows and also writes them onto
  the loaded systems, so the EW decision sees a scanner that was switched off. Two things only:
  the "off" row the page writes for a system under a ForcedOffline critical (the server reads that
  critical by itself; the row is for agreement), and shedding load on a ship in deficit - the jump
  engine first (the bot does not jump before T6), then weapons still loading, other systems,
  loaded weapons (least fire per point of power first), the engine, the scanner. Never the
  reactor, the C&C or a shield generator. No boost, no overload. The bot posts only this turn's
  rows and carries nothing over, so the same deficit switches the same systems off each turn.
  `balance()` is `shipManager.power.getReactorPower` for a ship with one ordinary reactor.
  **Left alone (P4), no rows as before:** bases, Mag-Grav reactors, ships with a Power Capacitor
  or Plasma Battery. ⚠️ The oracle counts 30 situations (bases in deficit) where the page would
  have stopped a player and the bot commits anyway - a gap until those reactors get a handler.
* **`ai/deciders/EWDecider.php`.** Per ship every point starts as DEW; one point at a time moves to
  OEW on an enemy (or CCEW when enemy flights are in sight) while that gains at least
  `EW_MIN_GAIN`. Score = `EW_OFFENCE` x what the ship's loaded guns should do, each gun against
  the enemy it does most to with the OEW held there, minus `EW_DEFENCE` x what the enemy's guns
  should do to this ship at its DEW, shared over the bot's units. Both come from
  `Weapon::calculateHitBase` with the EW tried (`AIView::estimate`, now public, with an arc-free
  form) - never a formula of the bot's own. Two guesses, the ones a player makes: its own ships
  are judged where flying straight on takes them (what MoveDecider does), arcs are not asked,
  and the enemy is taken to repeat LAST turn's EW.
  * Limits ported from the page: Restricted EW keeps half the output in DEW; an LCV (`LCVSensors`)
    puts all but 2 points into OEW; no row at all in a nebula; DEW only for a unit riding a
    docking bay, a Kirishiac ELINT-module ship, and any unit `AIView::unitBlock` names. Targets:
    targetable enemies not riding a docking bay. The DEW row is always posted, 0 included, last.
  * **The scanner figure is the page's** (`EWDecider::points`, a port of `ew.getScannerOutput`),
    not `EW::getScannerOutput`: the server gives a base or OSAT with a disabled C&C no sensors and
    the page does, and only the page knows the "sensors down" critical.
  * **The mask (P2):** the decision runs on `prepareForPlayer()`'s view, which in Initial Orders
    wipes every other unit's EW - all turns. `EWDecider::remembered` therefore takes the enemy's
    rows of turn N-1 (public then) from the load BEFORE masking, and nothing of turn N.
  * Not used: ELINT functions (T7), Detect Stealth / Detect Mines, flight EW, levels.
* `TacticalAI::initialOrders` composes the two (power first) and logs one `decision` line per
  Initial Orders ("EW: 9 of 14 points offensive, on 2 enemies (2 ships); power: 1 systems switched
  off"). `AIOrderBuilder::power` writes the row; `AIConfig::EW_*` holds the weights.
  `TacticalAI::$without` (harness only) plays listed phases on the fallback.
* **"Hit rates rise measurably versus T2" - met.** `selfPlayHarness.php` now prints each game's
  firing figures and takes `--without=1`. Three 24x16 games each way, default EA fleets:

  | | orders | mean chance | hits | damage | games fought to a finish |
  |---|---|---|---|---|---|
  | T3 (EW + power) | 130-153 | 46-51% | 65-72 | 1,367-1,541 | 3 of 3 (17, 18, 31 turns) |
  | T2 (`--without=1`, all DEW) | 81-163 | 25-27% | 15-46 | 318-837 | 0 of 3 (turn cap) |

  41-70% of the bots' EW points went offensive in those games. "Passes `validateEW` in every self-play turn" is true
  and means nothing (it cannot fail); the oracle above is the real test.
* **Gates, 2026-11-15:** self-play both movement modes on 42x30 with no refused submission and no
  defect; `cpuLobbyHarness`, `flagOffHarness`, `deployerHarness` (195) green; fire harness pair at
  the T2 settings (`--rounds=6`) 0 differences; replay check 203/205 (4504 and 4507, as before).
* **Seen on the way, and fixed at the user's request (2026-11-15) - a rounding difference between
  page and server.** The fire harness at its DEFAULT `--rounds=4` drew a situation the T2 run
  never had: IMLAttackCruiser's Gravitic Cannons (0.33 a hex) at 10 hexes, page 18%, server 19%.
  The goal sat exactly on a half percent; the two sides add its fractional terms in different
  orders, so one had 18.4999... and the other 18.5. Both now go through one rule -
  `Weapon::goalToPercent` (PHP) and `weaponManager.goalToPercent` (JS), a MIRROR PAIR: snap to six
  decimals, then round a half away from zero. This is game code, not AI code, and applies to every
  player's shots. After it: fire pair 0 differences at `--rounds=4` and `--rounds=6`; replay check
  unchanged at 203/205 (no recorded shot moved).
* **Left for later stages:** sensor boosting where power is spare (5.3); Easy's simple split and
  Hard's per-point optimum (T8); the T2 open ruling on closing with the enemy still stands - with
  EW the small-map games now finish; the 42x30 ones were only run to a 14-turn cap here.
* **Amended 2026-11-15 by the user's play-test feedback - see 10.6 A:** soft locks on enemies that
  move after the ship.

### 10.6 Stage T4 as built (2026-11-15) - COMPLETE, uncommitted

**Play-tested by the user, game 4701 (human Hyperion v CPU Hyperion Missile Cruiser + Aurora
Starfury flight, 2 turns played), reviewed 2026-11-15:** every order the bot gave was legal at the
positions of its own turn (checked with the oracle-proven `AIView`, the game wound back to each
moment) - the turn-2 salvo of four Basic missiles at 17 hexes (Class-S launch range 20), each in
arc, one `AmmoUsed` note per round; the flight's six Uni-Pulse Cannon orders at 4 hexes, 50%. The
salvo resolved at 55% base against the Hyperion's DEW 6; the server put four interceptors on the
first missile and one on the second, exactly the allocation `InterceptModel` mirrors. EW all DEW
(a missile ship's offence uses no OEW); the flight escorted on turn 1 and struck on turn 2
(accelerated to 9, turned onto the target at the end). No defect in `tac_ai_log`.
**User's note for T5 (game 4701, turn 3):** the missile cruiser held course instead of turning to
bring its three unfired racks (arcs 120-240 and 180-0) to bear for turn 3's launch. Moving to
maximise the ballistic weapons that can fire next turn - and the direct-fire weapons in arc within
a turn - is an important player skill; T5's movement scoring must count it.

Started with the user's feedback on T2 and T3 (2026-11-15, after game 4698); that came first.
No flag of its own, no migration. `source/autoload.php` regenerated for the three new classes
(`BallisticDecider`, `FlightDecider`, `InterceptModel`).

**A. The user's feedback on T2 / T3, and what changed**

1. *"Link the hit chance a bot fires at to the weapon's loading time; range penalty and plasma
   range damage matter too - in game 4698 I wouldn't have fired the Hyperion's Medium Plasma or
   Medium Pulse Cannons until a turn closer."*
   * `FireDecider::minimumChance`: a 1-turn gun fires at 5% or better; a slow one at
     `AIConfig::FIRE_MIN_CHANCE_BY_LOAD` (2 turns 25%, 3-4 turns 34%) - or 15%
     (`FIRE_MIN_CHANCE_PARTING`) when its target is drawing away and nothing better is coming.
   * `FireDecider::holdSlow`: each slow gun's shot is priced again where both units will be if they
     fly straight on (`AIView::ahead`, the real formula, so the range penalty is in it); the gun
     WAITS when `now < next x FIRE_HOLD_CONFIDENCE (0.8) x (1 - 1/loading time)` - a turn's wait
     costs a 2-turn gun half a shot and a 4-turn gun a quarter. Only the same target a turn on.
   * `AIView::damagePerHit` takes `rangeDamagePenalty` off a hit (Plasma; doubled in a nebula),
     as `Weapon::getDamageMod` does.
   * **Game 4698, turn 1, replayed with these rules** (scratch script, positions and EW of that
     turn): the bot fires the Heavy Laser (70%) and the three Standard Particle Beams (35%), and
     holds both Medium Plasma Cannons (30%, 3-turn) and both Medium Pulse Cannons (35%, 2-turn) for
     a turn closer. T2 fired all eight at 20-40%.
2. *"Check we're not overcompensating for intercept - there are only so many interceptor shots, so
   it's possible to saturate the enemy with fire."*
   * Finding: the bot counted NO interception at all. The -15 / -20 on every shot is the
     Interceptor's **Energy Web** (`InterceptorMkI` / `MkII` as a `DefensiveSystem`): the engine
     takes it off every shot from the interceptor's arc and the page shows it to humans too, so the
     bot's chance was exactly the page's. **User ruling, 2026-11-15:** correct - the E-Web deducts
     from ALL hit chances while an online Interceptor is in arc of the shot, an effect separate from
     the interception shot itself.
   * But ignoring interception cost real damage: self-play game 4700 had six interceptors pile on a
     lone missile ("Interception: 90 sources: 6, final to hit: 0") - against a ballistic the server
     loses nothing for each extra interceptor. 206 of 321 single launches in the ballistic harness
     would have been intercepted.
   * **`ai/InterceptModel.php`**: a volley at one unit is valued as the server will leave it. The
     shots become throwaway fire orders and get that unit's interceptors exactly as
     `Firing::automateIntercept` gives them - `compareInterceptAbility` order, each gun to the shot
     where it stops most damage, the server's own `isLegalIntercept` and each weapon's real
     `getInterceptionMod`, `getBestInterception`'s damage estimate (ported in `weight()`; the
     selection loop is ported too, because the server's re-tests geometry on every call and a
     decision asks thousands of times). The interceptors are gathered by the bot's own copy of
     `getUnassignedInterceptors`, because the server's would leak hidden information (P2): it
     switches an enemy missile rack into Interceptor mode from the magazine's TRUE contents, and
     reads a fighter gun's ammunition. Racks and ammunition guns are therefore not counted.
     **Parity:** `fireHarness.php` runs the real `automateIntercept` on every planned volley -
     1,491 direct-fire orders (508 intercepted) and 299 launches (147 intercepted), every total
     identical.
   * `FireDecider`: a shot is worth what it ADDS to its target's volley (less while the target has
     interceptors to spare; more when it draws them off a shot already planned); the bot's own
     missiles of the turn are in the volleys from the start (`ownMissiles`). A SLOW gun's floor
     holds on the chance it keeps after interception - the log says "N slow weapons not fired into
     the interceptors"; a 1-turn gun fires for whatever it adds.
   * `BallisticDecider` launches by SALVOS (B below).
3. *"Harder locks on enemies that move before the ship (a better chance of a good arc), softer locks
   (1-2) on those that move after."* `EWDecider`: an enemy with the same or higher initiative (it
   moves after) gets at most `EW_SOFT_LOCK` (2) points of OEW, and the fire hoped for against it
   counts `EW_MOVES_AFTER_WEIGHT` (0.5); a base, OSAT or mine counts as having moved.

**Determinism - found while doing the above.** Two runs of the orders harness differed by a point
of OEW. Since 2026-01-12 a shot from exactly between two of the target's sections hits one of them
at RANDOM, weighted by profile (`BaseShip::doGetHitSectionBearing`, on `Dice::d` = `random_int`,
not seedable), and the section's profile is the shot's defence - so the bot's tentative
`calculateHitBase` rolled a real die, cached the section per shooter (`activeHitLocations`) and
added to the target's expected damage, making every answer depend on the questions asked before it
(the look-ahead would have been priced on this turn's section). **`AIView::hitBase`** now asks the
engine once per ENTRY of the section table the roll would choose between (a heavy combat vessel's
table holds its front section twice, at two profiles) - forced through the engine's own cache for
direct fire; for a ballistic (no cache) by making the other sections look destroyed and setting the
hull's two profile figures for the call, or, if the table does not read them, by not launching from
that exact line - weights them as the roll does, and puts the bookkeeping back after every call.
Two orders-harness runs are now byte-identical.

**B. Ballistics (`ai/deciders/BallisticDecider.php`), in Initial Orders**
* `AIView` has a ballistic mode (`$ballistics`, on in phase 1): ballistic weapons only, a launch at
  a unit in the same hex whatever the arc, a young race's launch at a stealth flight over 5 hexes
  refused (`checkIsInRange`), `FireOrder` type 'ballistic'.
* Magazine-fed racks (`AmmoMissileRackS` and kin, fighters' racks included) are judged by the ROUND
  (`rackBlock`, `modes`): only `AIConfig::BALLISTIC_ROUNDS` (Basic, LongRange, Heavy, Flash,
  Antifighter, D-Light, Dogfight) that the magazine still holds. Every special round stays in the
  magazine (an Interceptor is still used by the server). Other plain ballistics (Ion Torpedo) too.
* Ammunition: a ledger per magazine keeps the plan inside the page's two limits (rounds of each
  kind, the magazine's total); the entry posts one mode name per launch in the magazine's
  `individualNotesTransfer`, as `AmmoMagazine.doIndividualNotesTransfer` does - the server writes an
  `AmmoUsed` note for each and asks nothing.
* Salvos: for every target, the best 1, 2, 3... launches still to be had are valued together
  through `InterceptModel`; the salvo worth most per launch is taken if its launches keep, on
  average, `BALLISTIC_MIN_CHANCE` (35; 50 at a flight) of what each would do unintercepted.
* Priced against the enemy repeating last turn's EW (`EWDecider` leaves those rows on the enemy
  units) and the bot's own EW just decided (`TacticalAI::initialOrders` stands it on its ships).
* Harness: `fireHarness.php --phase=1` + `fireOracle.js` (phase 1): 1,528 launches built identically
  by the page, the whole plan clicked in and committed through the page's magazine check
  (`doVerifyAmmoUsage` / `...Fighter`), every round drawn as the bot posts it, 0 differences. The
  page prices a launch against the target's unseen DEW (all its unspent EW); the oracle puts that
  back before comparing.
* **PAGE DEFECT found, and FIXED at the user's request (2026-11-15) - game code, affects players:**
  the page derived a weapon's per-mode range / fire control / damage only when the mode CHANGED
  (`updateFiringModeData`); at load they came from the static blueprint (`Weapon::stripForJson`
  sends `range` only after a critical). A magazine-fed rack whose live first round differed from the
  blueprint's (a fighter that bought Heavy or LongRange missiles) was aimed with the wrong range
  until the player cycled the mode - a Heavy missile launchable out to 8 hexes instead of 5, a
  LongRange one refused past 8 instead of 15. **Fix:** the client `Weapon` constructor
  (`model/shipSystem.js`) calls `updateFiringModeData()` for a `checkAmmoMagazine` weapon - what a
  mode change does; each weapon has its own `data` copy by then, so no tooltip text leaks. The
  oracle now FAILS on any magazine-fed weapon that loads stale: 12 such racks in the earlier run,
  0 after the fix, on the same cases. Legacy bundles rebuilt by hand (`bundle-legacy.js`; the
  watcher had missed the edit) and synced.
* Self-play (missile fleet v gun fleet, 24x16): game 4703 - no launch "eaten" whole; turn 9's salvo
  4 of 4 hits, turn 16's 5 of 6 (game 4700, before salvos: 7 launches intercepted to nothing).

**C. Flights**
* **`ShipMover` plots fighter flights** (it is the movement.js mirror for them too): one pool of
  thrust (`freethrust`), no thrusters - each speed change, turn (ceil(speed x turn cost), never
  under 1) and sideslip (1) paid in full on its row and committed at once; facing kept on the
  heading; no alignment test on a turn, no thrust-per-5-speed test on a slip, no Quartermaster
  delay; JINKING a level at a time up to `jinkinglimit` (1 thrust a level, 2 in a nebula),
  `jinkLevels()` / `jinkRoom()`. `enumerate()` leaves jinks out (no hex changes). Refused:
  remote-controlled flights (Hunter-Killers), gravitic flights. Harness: `shipMoverHarness.php` +
  `shipMoverOracle.js` now include flights - 150 hulls (22 flights), 333 flight situations, 720
  jink steps; 32,766 plans and 247,508 steps replayed through movement.js, 0 differences.
* **`ai/deciders/FlightDecider.php`** chooses among those plans (grouped by end hex and facing, which
  is all fire and danger depend on), in "value removed": offence (the flight's guns, in their arcs,
  at this turn's Fire phase - `AIView::atFireTime` flies units yet to move straight on), minus
  danger (every enemy gun that can reach it there, `FLIGHT_DANGER_WEIGHT`), minus terrain and map
  edge (`MoveDecider::hazard`). With nothing in reach it CLOSES on its target (`FLIGHT_CLOSING_PER_
  HEX`) - or, the target more than two turns off, ESCORTS: stays within `FLIGHT_ESCORT_RANGE` of
  the fleet. Mission: INTERCEPT the enemy flights within `FLIGHT_INTERCEPT_RANGE` of the bot's ships
  when its guns can fire at fighters, else STRIKE. It jinks all it can where it would take more
  than it deals. Movement decisions now run on the masked view (P2), as Firing's do.
* Self-play, carrier fleet (Avenger, 3 Starfury flights, Tethys) v gun fleet, 24x16, game 4705: the
  flights did most of the carrier side's fighting (63 orders, 38 hits), striking on most turns and
  jinking on 28 flight-turns; no defect.

**Gates, 2026-11-15:** fire pair (phase 3) 0 differences at `--rounds=4` and `--rounds=6`;
ballistic pair 0 differences; orders pair 0 differences and two runs byte-identical; ShipMover pair
0 differences; `deployerHarness` (195), `flagOffHarness`, `cpuLobbyHarness` green; replay check
203/205 (4504 and 4507, the played-on games, as before); self-play with missiles, carriers and the
default fleets, no refused submission, no defect.

**⚠️ The cost, measured.** Three default EA games on 24x16 against T3's figures (10.5):

| | orders | mean chance | hits | damage | fought to a finish |
|---|---|---|---|---|---|
| T4 | 175-270 | 48.5-54.7% | 97-121 | 1,843-2,263 | 1 of 3 (52 turns; two at the 56 / 58-turn cap) |
| T3 | 130-153 | 46-51% | 65-72 | 1,367-1,541 | 3 of 3 (17, 18, 31 turns) |

Every shot is better and more damage is done, but over far more turns. A fourth game, kept and
read (4719, 37 turns, finished): of its 72 Fire decisions, 36 fired nothing at all - no enemy in
reach - while the new holds came to 34 weapon-turns (20 waiting for a closer turn, 14 kept out of
idle interceptors) against 223 weapons fired. So the time goes on fleets that only hold course
(T1b) passing out of each other's reach, and the flights now dogfight each other instead of
running into the enemy's ships; the firing rules are a small part. The knobs are
`FIRE_MIN_CHANCE_BY_LOAD`, `FIRE_HOLD_CONFIDENCE` and the slow-gun floor after interception; T5's
movement (closing to the range a fleet's guns want) is what should shorten games.

**Left for later:** closing with the enemy (the 10.4 pursuit question - **user ruling 2026-11-15:
T5 is next and takes it**), with moving to bring the most weapons and launchers to bear (the user's
4701 note above); flights do not pivot, dock or launch
(the bot's flights start on the map), fly Fighter Missions or coordinate; special rounds (T7);
holding missiles back by level (`BALLISTIC_RESERVE`, T8). Seen in the engine, not changed: a
ballistic's hit LOCATION and its PROFILE come from two separate rolls on an exact section line
(`calculateHitBase` calls `getHitSectionPos` and `getHitSectionProfilePos` separately).

### 10.7 Stage T5 - movement search (started 2026-11-16) - COMPLETE (user, 2026-11-20), T5e onward uncommitted

T3 and T4 were committed by the user as 501272f04.

**User rulings, 2026-11-16 (asked at the start of T5):**
* **Range: "best trade".** A ship goes where the damage it deals less the damage it takes is best,
  both from the real hit formula - so a laser ship stands off from a plasma ship and a short-ranged
  ship closes. Personality presets may tilt the balance later (T8).
* **Formation: loose.** Each ship takes its own best position but pays a small cost for straying
  from the rest of the fleet (mutual interceptor cover, concentrated fire).
* **Damaged sections: protect moderately.** "If the guns brought to bear are high damage with decent
  hit chances I might risk exposing my damaged side. Also, I would protect a destroyed section far
  more than a moderately damaged side." (A hit on a destroyed section goes to the primary structure:
  `BaseShip::getHitSection` answers 0.)
* **Pivots and rolls: "rarely worth it"** - built last. Their real use is a damaged ship's: "rolling
  to change side thrusters used for a planned turn, or pivot to decelerate after retro thrusters are
  destroyed."

**Measured before building:** `ShipMover::enumerate` is exhaustive well inside its quota for
capital and heavy hulls (Omega 39-97 end states, Hyperion 17-69, Olympus Beta 120-198 at speeds
4-8) and reaches 400 for nimble ones (Tethys, Hermes), in 11-250 ms; `AIView::estimate` costs
0.03-0.04 ms a call. So every end state can be scored with the real formula, and "a search aimed
at a goal" is not needed for breadth - the T1b bias was in the ORDER of a quota that big hulls
never fill.

**Sub-stages** (each one played and measured before the next):

| | Deliverable |
|---|---|
| T5a ✅ BUILT | `ShipDecider`: where a ship goes, scored in "value removed" like `FlightDecider` - this turn's direct fire from the end hex and facing, next turn's launchers from it (the user's 4701 note), the enemy's fire at it (damaged and destroyed sections weighted), next turn's trade a turn straight on, closing when nothing is in reach, loose formation, the T1b hazards. Every end state `ShipMover` finds. |
| T5b ✅ BUILT | Overthrust allowance (10.3 ruling): `ShipMover` may overthrust when asked, plans carry the excess, the scoring weighs the critical roll it invites. Both harness halves. |
| T5c ✅ BUILT | Terrain that drifts (next position predicted) and mines the bot can see, avoided. Drift (`TerrainDrift`): from turn 2, at the START of each turn a one-hex asteroid or meteor swarm stays (25%) or moves one hex straight on or 60 degrees either side (25% each), and a unit in the hex it enters is struck in Pre-Firing - so an end hex in a field's three forward hexes is a 25% overrun each, and next turn's continuation must miss all four of its possible hexes. Mines: read their trigger rules first. **With it, the user's feedback on game 4750 (below).** |
| T5d ✅ BUILT | Gravitic hulls in `ShipMover` (mirror + oracle). |
| T5e ✅ BUILT | Pivots and rolls for the damaged-ship uses above (mirror + oracle) - and, after the user's notes of 2026-11-19, the gravitic and thruster-swap uses, gated by level (5.8). |
| T5f (STARTED 2026-11-19 - sub-stages T5f-1..6 below the T5e record) | Levels (Easy: noise; Hard: one-ply look-ahead, the enemy's best reply; the skills table of 5.8, from the user's 4750 notes - Hard's power management and extended turns among them - and the user's 4774 notes: the health fix, warier fighters, the coverage search) and the ladder harness - the stage's "done when". |

**T5a as built (2026-11-16) - BUILT, uncommitted, not play-tested by the user.**
No flag, no migration; `source/autoload.php` regenerated for the one new class (phpab in the
`fv-phpcheck:8.4` image - one added line).
* **`ai/deciders/ShipDecider.php`** - `rows($view, $mover, $ship, $blocked, $bounds)`. Every end state
  `ShipMover::enumerate` finds (quota `SHIP_PLANS` 1500, never reached by a big hull) is scored; the
  fire terms are cached by end hex and facing. Terms, all from `AIView::estimate` / the engine:
  * *offence* - this turn's loaded direct-fire guns from the end state, arcs and line of sight asked,
    every other unit where `atFireTime` puts it; fire at an enemy yet to move x `MOVE_UNMOVED_OFFENCE`.
  * *launch* - next turn's launchers (`AIView::launcherNextTurn`) from the end hex at the enemy's
    end-of-turn hexes, priced by `AIView::launchEstimate`: a tentative ballistic order dated NEXT
    turn, so `Weapon::getFiringHex` takes this turn's last row as the launch hex.
  * *danger* - every enemy's plain guns at the ship: a unit that has moved with its arcs, one yet to
    move with all its guns at `MOVE_UNMOVED_DANGER`, each enemy weighed by `AIView::exposure` (the
    engine's section choice; a damaged section 1 + `MOVE_SECTION_DAMAGED` x share lost, all facing
    sections destroyed `MOVE_SECTION_DESTROYED`), x `MOVE_DANGER_WEIGHT` 0.75 against offence.
  * *next turn* - the same trade with everybody a turn straight on (`AIView::ahead`), x 0.5; a slow gun
    that fires this turn is not counted again.
  * *shaping*, in hexes of `MOVE_HEX_VALUE` (0.5%) of the ship's own value: closing on the nearest
    enemy SHIP (flights only when nothing else is left), this turn + half of next; formation (2.5 a hex
    beyond 4 of the nearest other ship of the bot); speed below 3, above the size's comfortable speed,
    and above `ShipMover::fastestTurnable` (the fastest at which a whole turn's thrust still pays for a
    turn); a little per manoeuvre (slips dearest). The T1b hazards (`MoveDecider::hazard`) outweigh all.
  * `$trace` (harness only) lists the five best plans with every term.
* **Two corrections the first self-play games forced:**
  * *Closing was an on/off term* (only with nothing in reach from any end state) - one end state with a
    token 8 points of fire turned it off for all, and ships slid off sideways. Now always counted, small.
  * *Uncapped worth.* `value / health` of a nearly dead unit is enormous: one volley at a crippled
    Hyperion scored 13,924, the crippled ship's own "danger" 11,458. In game 4720 that ship fled off the
    map (the 5,000 off-map hazard was cheaper) and an Omega chased it there at speed 9 - where it could
    no longer turn - and flew on to r -69. Now no target counts past the damage that finishes it
    (`removed()`, greedy, best gun first) and a ship's loss stops at its whole value (`lost()`).
* **`MoveDecider`** routes hulls to `ShipDecider`, flights to `FlightDecider` as before, and stands each
  decided unit at its end (`standAtEnd`) so units moving in the same activation see each other.
  `MoveDecider::choose` (the T1b rule) stays: the fallback, and `MoveDecider::$plainFor` (harness only).
  The Movement decision line in `tac_ai_log` now names each ship's plan and terms.
* **`selfPlayHarness.php`**: `--plain-move=2` (those slots move ships by the T1b rule) and a result
  line per game - each side's surviving value (point cost x combat value) and who is ahead.
* **Measured, 24x16, default EA fleets, T5 against the T1b mover (sides swapped half way):** T5 ahead in
  **6 of 6** games, keeping 34-85% of its value against 0-10%; four games fought to a finish (16, 26,
  47, 54 turns), two at the turn cap. No refused submission, no defect. Decisions take 50-600 ms a
  ship (DB-free scratch timing); a 24x16 game runs 35-128 s.
* **Gates, 2026-11-16:** ShipMover pair 32,766 plans / 247,508 steps, 0 differences; fire pair 0
  differences; `flagOffHarness`, `cpuLobbyHarness` green.
* **Seen, left for later stages:**
  * Both turn-capped games ended with one side down to a fighter flight that could not hurt the
    other's last ship, and with crippled ships drifting off the map - T6 ("no game runs past the turn
    cap for lack of a decision").
  * The drifters were real physics, not decisions: a Hyperion at speed 8 lost both retro thrusters
    (cannot brake, cannot pay a turn at 8), a Tethys four thrusters. Exactly the user's pivot use
    (T5e): "pivot to decelerate after retro thrusters are destroyed".
  * `FlightDecider` still prices danger with the uncapped `value / health` - the flaw fixed above for
    ships. Not seen to misbehave yet; not changed (T4 code). *(Fixed at T5c, 2026-11-19: it now prices
    through `ShipDecider::removed` / `lost`.)*
  * `EWDecider` still judges the bot's own ships where flying straight on takes them. *(T5c: its close-range
    test takes the nearer of that and where the ship stands.)*
  * Interception is not in the movement scores (it changes how much a volley does far more than where
    it is best fired from).
* **Performance fix the deployer harness showed:** `deployerHarness` lets `AIDriver::run(gameid, 3600)`
  play up to 200 submissions after deploying, so it now drives `ShipDecider` over its asteroid
  scenarios - and took 25 minutes (195 checks green). Line of sight (`Mathlib::isLoSBlocked`, which
  walks every blocked hex) was asked once per GUN per enemy per end state; now once per enemy per end
  state, each way (`lines()` - the engine asks it from the shooter). With 150 blocked hexes a decision
  takes 274 ms for an Omega, 1.1 s for a Tethys (with T5b's extra end states).

**T5b as built (2026-11-16) - BUILT, uncommitted.** The overthrust ruling of 10.3.
* **`ShipMover::allowOverthrust($points)`**: a thruster may carry up to that many points over its
  rating (default 0 = the T1b policy, byte-identical plans). A point goes over a rating only when no
  thruster facing the right way can take it within its own (`clickBest` sorts within-rating thrusters
  first); `click()` still holds the page's ceiling of twice the rating. **`overthrust()`** = thruster
  id => points over the rating, for each thruster the plan took over (or further over - Engine
  Shorted's push may already be); every plan carries it.
* **`ShipDecider::candidates`**: every end state within the ratings, then - allowance
  `MOVE_OVERTHRUST` (99, i.e. the page's ceiling) - only the end states nothing within the ratings
  reaches. Each is priced by the critical it invites (`overthrustRisk`): per thruster the end-of-turn
  roll d20 + damage + excess (Criticals: 15 First Thrust Ignored, 20 Half Efficiency as well), each
  expected critical `MOVE_COST_THRUSTER_CRIT` (10 hexes), Half Efficiency counted twice. One point over
  a sound thruster costs 4.5 hexes: always worth it to stay on the map (5,000), worth it for real
  fire, never for a tie. The decision line says `OVERTHRUST #id +n`.
* **Harness:** each situation gets one more random walk with the allowance, and one overthrusting plan
  of the search is replayed; the first step of such a case carries `allowance` and the oracle checks
  the thrusters against rating + allowance (the page refuses any point past twice the rating click by
  click). The server half also checks that `overthrust()` is exactly the excess Criticals rolls for.
  **150 hulls, 5,435 situations, 41,302 plans (8,692 allowed to overthrust, 4,950 thrusters taken over
  their rating), 314,217 steps replayed through movement.js, 0 differences; 707,736 server checks.**
* **Self-play with T5a + T5b (2026-11-16):** against the T1b mover on 24x16, T5 ahead in **6 of 6**
  again - and **all six fought to a finish** (23-36 turns; T5 kept 18-83% of its value, T1b 0-7%).
  T5 against T5 on the full **42x30** map: both games fought to a finish (32, 46 turns) - every 42x30
  game before T5 hit the 40-turn cap (10.4). No refused submission, no defect. The bots overthrew in 30
  of ~1,150 Movement decisions (under 3%; live players: 4.2% of ship-turns), mostly 1 point. One Omega
  crab-walked three turns of one game with four slips a turn, a side thruster 3 over its rating each
  time, to step out of the arcs of enemies that had moved - the risk priced at 6.5 hexes against a few
  hundred points of danger. That is the ruling's trade-off as written; `MOVE_COST_THRUSTER_CRIT` is the
  knob if it should be more conservative.

**The user's feedback on game 4750 (2026-11-19) - human G'Quan x2 + Frazi flight v CPU Omega x2 +
Aurora x2, 42x30, simultaneous movement (8 initiative categories), turns 1-5 reviewed.** Asked to
fold it into the bot or the plan, and to help tell Easy, Normal and Hard apart (that table is in
5.8). The user's four threshold choices were asked and answered the same day (all the recommended
options). Before changing anything the bot's own turn-5 decisions were replayed with a trace
(scratch script: the game wound back to each moment from `tac_shipmovement` - the loader keeps turn
1, N-1 and N - with the active ships set to the movers of that moment, or `hideActiveShipMovement`
hides the game's CURRENT active ship's rows). What it showed is in the table.

| # | The user's note | What the bot did, and why | Done |
|---|---|---|---|
| 1 | Game 4750 is the basis | - | - |
| 2 | One point of CCEW is a deterrent, even from some distance; more than 2-3 only against a fighter-heavy force or on a ship with a lot of light anti-fighter weaponry | Turn 4: an Omega had CCEW 5, OEW 3, DEW 0. CCEW was bought only for a gain, with no cap | **Built** (`EWDecider`): 1 point whenever an enemy flight could be within CCEW's 10 hexes this turn (10 + its speed) and the ship has a gun that can fire at fighters; never over `EW_CCEW_CAP` (2) unless a third of the enemy's visible value is in flights or at least half the ship's guns are anti-fighter guns (fire control better against fighters than against capitals - a Twin Array 6/5/4 is one, a Standard Particle Beam 4/4/4 is not, an Interceptor has no offensive fire at all) |
| 3 | Fighters far too cautious: Aurora retreated on turns 4-5 though moving last; parking 3-5 hexes off a G'Quan, or jumping the Frazi, was viable. Weigh nearby CCEW and how many light weapons the flight is in arc of | Replayed: the flight was on an INTERCEPT mission whose ONLY target was the Frazi, out of its reach everywhere, and its danger priced every G'Quan gun whatever its arc (28 expected damage at 2 hexes; 11 once arcs were asked - both G'Quans had moved). Nothing scored, so it fled | **Built** (`FlightDecider` reworked): every enemy is a target, raiders near the bot's ships worth `FLIGHT_INTERCEPT_WEIGHT` (1.5) more; an enemy that has moved fires from its ARCS, one yet to move with every gun; only guns loaded this turn; the enemy's EW is this turn's (in sight since Initial Orders closed - CCEW included); each place is priced as it is and with all the jinking the thrust left allows, the better kept; worth capped per target (`ShipDecider::removed` / `lost`, the T5a fix FlightDecider had lacked); `FLIGHT_DANGER_WEIGHT` 1.0 -> 0.75 as for ships. Replay: Aurora #5 now ends 2-3 hexes from both G'Quans and the Frazi; Aurora #4 (moving last) goes for the crippled G'Quan #1 in its own hex (range 0, where jinking would not help it - 447 offence against 133 danger), parking 1-2 hexes off scoring next. A `$trace` lists its six best places |
| 4 | The Omegas' speed 7 and 8 on turn 5 is too fast for thrust 8 - a rare move, to finish an opponent or escape certain death | Replayed: speed cost 5 hexes (~20 points) against a danger saving of 211 from running behind G'Quan #1's arcs | **Built** (`ShipDecider`, `ShipMover::safeSpeed`): above the speed at which one turn costs more than `MOVE_SAFE_TURN_SHARE` (0.75, the user's choice) of the ship's thrust - Omega 6, G'Quan 13 - each point of speed costs `MOVE_COST_OVERSPEED` 25 hexes (12.5% of the ship's value; 12 still let Omega #6 run at 7). Replay: Omega #6 holds speed 6 at 4 hexes, Omega #7 goes to 6 |
| 5 | Concentrate the fleet's heavy firepower to take a ship down rather than spread it | Turn 4: 30 weapons on all three enemies | **Built** (`FireDecider`): a HEAVY gun (loading over a turn, or `FIRE_HEAVY_DAMAGE` 15 a shot) is drawn to a target already booked by `FIRE_FOCUS_HEAVY` (2.5) instead of `FIRE_FOCUS` (0.5); light guns stay free for fighters. **Planned (T5f, Hard):** Movement putting the fleet's heavy guns on one chosen target (the turn plan of section 4) - each ship still moves for its own best trade |
| 6 | Turn shortening (extra thrust on a turn) is important; rarely overthrusting for it | Never used: ShipMover could shorten (oracle-proven in random walks) but the search did not try it | **Built** (`ShipMover::allowShortening`, `shortened`, plans carry `extras`): the search tries every turn with all the extra thrust it can take - the double turns only a shortened delay allows; an overthrust for it is priced as any other; and the plan chosen gets its LAST turn shortened with thrust that would otherwise go unused, within the ratings, so the next turn starts free to turn. Equally plain plans to one end state keep the one with less delay left. **Ruled the same day:** "always useful (since any delay carries over to next turn as well) if you have the spare thrust on that turn, and you won't exceed thruster ratings" - by default for Normal and Hard, Easy may skip it. So the last-turn shortening was made to hold back only what an order after the turn needs (a sideslip) instead of giving up: of 27,175 plans across the EA and raider hulls ending with a delay left, 2,051 had been left unshortened that a smaller extra could shorten |
| 7 | Pulse weapons do more at high chances (grouping): a premium on their minimum chance | - | **Built** (`FireDecider::minimumChance`): a slow pulse weapon's floors are `FIRE_PULSE_PREMIUM` (+10, the user's choice) higher - Medium Pulse 35%, Heavy Pulse 44%; one-turn pulse guns keep 5% |
| 8 | In close engagement players mainly use soft locks and keep a decent amount of DEW | Turn 5: both Omegas 8 OEW on one G'Quan, 0 DEW, at 1-4 hexes (the human: 2 + 2 OEW, 3-4 DEW). Found: a DEW point was valued at a QUARTER - every enemy gun's fire divided by the bot's unit count, its flights included - and every enemy gun counted, reloading or not, against only the bot's loaded ones | **Built** (`EWDecider`): within `EW_CLOSE_RANGE` (10, the user's choice) a lock stays soft (2), and with an enemy SHIP that close at least half the points stay DEW; each enemy gun counts by the share of its fire this ship would draw among the bot's units, and only guns loaded this turn (every player sees an enemy weapon's loading on its ship window). Self-play: 23% of EW offensive with the share fix alone, 32% with loaded guns only, soft locks of 1-2 and DEW 3-7 once engaged; a ship standing off beyond 10 hexes still snipes with a hard lock |
| 9 | Enemies moving after your ship are MORE dangerous (better shots, a damaged section) | `MOVE_UNMOVED_DANGER` was 0.5 | **Built** (`ShipDecider`, `FlightDecider`, `AIView::exposureUnmoved`): every gun of an enemy yet to move counts at 1.0, and the section it meets leans to the ship's worst one by how much of the distance its speed covers. Next turn's terms keep 0.5 for both sides (`MOVE_UNMOVED_DANGER_NEXT`) - nobody knows next turn's initiative. **Also fixed with it:** this turn's danger counts only enemy guns loaded now (next turn's, those loaded by then) - the Omegas had run from heavy guns that had just fired |
| 10 | Hard: switch off light weapons out of arc/range on early turns to boost Scanner or Engine; back on as the enemy nears | `PowerDecider` never boosts | **Built (T5f-4, Hard only):** `PowerDecider::boost` - one-turn guns that cannot reach anything this turn (and that the enemy's shots do not need for interception) pay for scanner or engine boosts, the page's prices, limits and buttons (proven by `ordersOracle`); "back on as the enemy nears" is this turn's reach test - a one-turn gun is loaded again the turn it is switched back on. Range only: arcs are not modelled |
| 11 | Ram when the Desperate rule applies to the CPU's team, only as a last resort (no weapons left, primary under some %); never as a standard attack except for units like Hunter-Killers marked `$designedToRam` | Never rams (`AIView::weaponBlock`: 'ramming') | **Planned (T6, table in section 10):** the Desperate rule is honour-based - `{"desperate": team or -1}`, `DesperateRule::$appliesToTeams`, nothing in the engine stops a ram - so the bot restrains itself: a unit of a team the rule covers with no usable weapon left, or its primary structure under a threshold, may plan into a target's hex and order its `RammingAttack`; a unit whose ram is `designedToRam` (the Orieni / Makar HKs) may ram in any game, as its own handler (T7) |

**T5c as built (2026-11-19) - committed by the user with T5d as 151cf072e, not play-tested.** No flag, no migration;
`source/autoload.php` regenerated for the one new class (`fvbuild -Autoload`, one line).
* **`ai/Hazards.php`** - the BOARD, built once per Movement decision from the bot's masked view and
  asked for every end state by `ShipDecider` and `FlightDecider` (`MoveDecider::orders` builds it;
  T1b's `MoveDecider::hazard` / `choose` stay the fallback's). Terms, priced in VALUE (expected damage x
  what a point of the unit is worth) except the fixed ones:
  * *board* - T1b's fixed costs exactly (proved below), except that a DRIFTING asteroid on next turn's
    straight continuation costs `MOVE_COST_NEXT_TURN` x the chance it stands there (25% for each of its
    four possible hexes the continuation crosses); a static rock (a 2- or 3-hex one, or any rock when
    the game's rule is off) still costs it all;
  * *drift* - ending in one of a drifting field's three forward hexes: 25% of its strike - an asteroid
    d10 x speed (the end speed standing in for next turn's; an Ancient 2 less; nothing at speed 0, as
    the engine makes no order), a swarm its meteors - weighed by the section facing the field
    (`AIView::exposureAt`);
  * *meteors* - a meteor swarm's hex ENTERED this turn (the expectation of
    `MeteorSwarm::rollMeteorChart`, with its speed modifier - ⚠️ `Hazards::METEOR_CHART` is a copy;
    the harness rolls the real one against it), and next turn's continuation through one by the chance
    it is there, at `MOVE_NEXT_TURN_WEIGHT`. Swarms are never blocked hexes (not Enormous) - until now
    the bot flew straight through them;
  * *mines* - the trigger rules read in `AoE.php` / `MineControllerDEW`: a Proximity (Pre-Firing,
    always hits) or Captor (Fire phase, rolled) mine attacks the first unit by initiative whose STARTING
    hex or any hex it enters is within its range for that kind of unit (its set range, else the full
    one; halved against a Jammer) with a clear line, then is gone; a DEW mine's guns keep firing. So a
    path into range pays the attack (`HAZARD_MINE_HIT` 0.75 for a rolled one); a unit that STARTS in
    range pays nothing more for any plan; a DEW mine also charges ending in range (next turn). Only
    mines the bot can see (an undetected one sits at -10000 in its view), its own side's only without
    IFF. A mine is no longer an "enemy gun" in the danger terms or EW's threats.
* **Gaps, left for later:** dust clouds, gravity wells and the Triad's spawned terrain are not priced
  (they block no line of fire, so `blockedHexes` never listed them either); the bot sweeps for no mine
  it cannot see; a mine's choice of the FIRST unit by initiative is not modelled.
* **A model limit met while tuning (not changed):** since T2 a point of damage is worth a unit's value
  over what is left of its PRIMARY structure (`AIView::healthLeft`), though most hits land on an outer
  section first - so offence and danger both read several times high for a fresh ship (turn 5 of 4750:
  "danger 446" for an Omega worth 802 at 4 hexes from two damaged G'Quans). The two sides inflate alike,
  but terms in hexes of the ship's own value (speed, closing, formation) look small beside them;
  `MOVE_COST_OVERSPEED` was set with this in view. A health that counts the sections a hit must go
  through would be the fix; it moves every weight, so it is for a stage that re-tunes them (T5f).
* **The rest of the 4750 notes** are in the table above; their code: `AIConfig` (every new weight and
  threshold, each with the ruling it carries), `ShipMover` (`allowShortening`, `extras`, `shortened`,
  `safeSpeed`), `ShipDecider` (rewritten around the same terms, `removed` / `lost` public),
  `FlightDecider` (rewritten), `EWDecider`, `FireDecider` (`isHeavy`, the pulse premium), `AIView`
  (`exposureAt`, `exposureUnmoved`, `worstExposure`).
* **Harnesses** (`tests/ai`, gitignored as before):
  * NEW `hazardsHarness.php` (no database; php container): A - on boards of static rocks every plan
    the search finds is priced exactly as `MoveDecider::hazard` prices it (4 hulls x 40 random boards);
    B - drift geometry for all six headings (forward hexes only, nothing at speed 0, the field's own hex
    blocked, a continuation through two of its hexes 50%, a 2-hex rock 100%); C - `meteorHits` against
    40,000 rolls of the real chart a column, swarm entry, continuation and overrun; D - mines: unseen ->
    free, seen -> its attack, start in range -> free, own side by IFF, a Captor's set range, line of
    sight; E - Omega, Hyperion, Tethys and an Aurora flight steer out of an overrun and out of a mine's
    range. **14,758 checks at the default 40 rounds.** Mutation pass: 12 broken copies of `Hazards`
    loaded ahead of it (`php -d auto_prepend_file=...`), **12 caught** (two only after the start-rule
    and Captor checks were given geometry that tells them apart).
  * `shipMoverHarness.php`: the search runs with `allowShortening()`, every replayed search plan
    carries its extras, a shortening plan is always among those replayed, and one plan per situation is
    replayed with its last turn shortened as `ShipDecider` does it.
  * `selfPlayHarness.php --terrain=asteroids:12,meteors:6,drift` - random terrain as Create Game places
    it, and the Drifts boxes.
* **Gates, 2026-11-19:** ShipMover pair, re-run after the last-turn shortening learned to hold back
  - **150 hulls, 5,435 situations, 45,890 plans (4,765 shortening a turn in the search; 1,486 with the
  last turn shortened as ShipDecider does it, 1,187 of them holding thrust back for an order after the
  turn; 8,616 allowed to overthrust), 347,767 steps replayed through movement.js, 0 differences;
  810,375 server checks** (slowest search 0.9-1.0 s at the harness's quota of 200, against 0.25 s
  before shortening); fire pair, phase 3 and phase 1,
  0 differences; orders pair 0 differences (936 situations; 32 bases / Mag-Grav ships the bot leaves
  alone that the page would stop, the known gap of 10.5); `hazardsHarness` 14,758 checks;
  `flagOffHarness`, `cpuLobbyHarness` green; `deployerHarness` 195 green (~30 min: it plays its
  asteroid scenarios on with the full driver). **Replay check 212 passed / 8 failed** - 4297, 4356, 4357,
  4359, 4361, 4440, 4456, 4519, every difference `/ships/N/systems/N: added`: the user's Kelly Trek
  commits of 2026-11-18 (244209010, 10d58637b). **Proved not this stage's by stash-and-compare**: with
  `source/server/ai`, `autoload.php` and this plan stashed the same eight fail identically. **The user
  re-recorded the corpus the same day: 228 passed, 0 failed with this stage in place** (the re-record
  adopted the kept self-play games 4767 and 4768 - deleting them now makes them SKIP).
* **The cost of turn shortening, measured** (scratch, DB-free, an open board with three enemies at
  fighting range): one ShipDecider decision takes Omega 0.15 s (39 end states), Hyperion 0.10-0.22 s,
  Olympus Beta ~0.5 s (120-200), Tethys 1.4-1.9 s (394-510 end states; the search alone 0.4-0.6 s) -
  roughly twice T5b's figure for the nimble hulls, from the end states only a shortened turn reaches.
  Fine for play; it matters to live's request budget (`AIConfig::BUDGET_SECONDS` 4 is checked between
  submissions, and one Movement submission can carry several ships). **Ruled a T8 deliverable by the
  user (2026-11-19) - section 12 has what to build.**
* **Self-play, 2026-11-19** (default EA fleets unless named; no refused submission, no defect in any):
  * T5c against the T1b mover, 24x16, sides swapped: T5c ahead in **4 of 4**, all fought to a finish
    (33, 28, 11, 22 turns), keeping 48-64% of its value against 0% (T5a/T5b: 6 of 6).
  * Drifting terrain, 24x16 (`--terrain=asteroids:10,meteors:6,drift` - 15 drifting fields): fought to
    a finish in 25 turns; mean chance 46%; 32% of EW points offensive (soft locks of 1-2, DEW 3-7 once
    engaged; one Omega standing off at 13-16 hexes sniped with a hard lock of 8, as allowed beyond close
    range). Shortened turns used throughout ("turnright (shortened)" in the decision lines).
  * Carriers v line (Avenger, 3 Aurora flights, Tethys v the default fleet), 24x16, simultaneous movement:
    fought to a finish in 14 turns.
  * T5c v T5c, **42x30 with 22 drifting fields and 8 meteor hexes**, simultaneous movement: reached the
    40-turn cap (44 turns; 33% v 22% left). In its last ten turns one side was down to an Omega duelling
    the other's Omega at 4-8 hexes, while the other's damaged Hyperion and Tethys stood off 12-23 hexes
    (fire 0, danger 0): each ship takes its own best trade, and a damaged ship will not walk into an intact
    Omega's guns to finish it. That is the case for T5f's fleet focus (Movement putting the fleet's guns on
    one chosen target - the user's note 5) and T6's finishing logic; one game, not tuned around.
  * No terrain, 24x16: reached the cap with one side at 0% value (crippled hulks the other could not or
    would not finish) - the T6 case T5a recorded.

**T5d as built (2026-11-19) - committed by the user with T5c as 151cf072e, not play-tested.** Gravitic hulls in
`ShipMover`. No flag, no migration, no new class (autoload unchanged).
* **What a gravitic drive changes in the page's rules** (every `ship.gravitic` in `movement.js` read): in
  four places, and only when the ship is out of alignment (a pivot made earlier: flying neither bow- nor
  stern-first), pivoting or rolling. `canChangeSpeed` - a gravitic ship may change speed in any attitude;
  `canTurn` - it may turn while rolling, pivoting or out of alignment; `canSlip` - while rolling;
  `thrusterDirectionRequired` - out of alignment a SPEED CHANGE is not reversed for a heading in the aft
  half but paid through the thrusters the pivot turned to the job (pivoted to port = going starboard-forward:
  port thrusters push along the heading, starboard ones against it), while a turn or slip keeps the plain
  rule. `assignThrust` and the turn delay have no gravitic rule. **Bow- or stern-first and neither pivoting
  nor rolling, a gravitic hull's rules are a plain hull's** - which is where the bot's gravitic ships always
  fly, as it never pivots or rolls them (T5e). The T1b refusal was for the other states.
* **`ShipMover`**: the refusal ('a gravitic drive') is gone; `$gravitic`; the three gate exceptions;
  `directionRequired($direction, $accel, $turnOrSlip)` now takes the page's two arguments (a speed change
  asks with accel TRUE for both arrows, a turn or slip with turnOrSlip true - a pivot, never ordered here,
  is the only caller with accel false) and has the gravitic branch, with `isPivotedPort`. Gravitic FLIGHTS
  (the Brakiri fighters, the Vree Salvage Zorth) are ordinary flights to it: the page's "gravitic turn" (a
  flight's turn keeping its facing, `canGraviticTurn`) is an order it does not give. Comments in
  `MoveDecider` / `ShipDecider` updated; no decider change - a gravitic hull is scored like any other.
* **Harness** (`shipMoverHarness.php`): the page is now told the truth about the drive (`clientShip` sent
  `gravitic: false` for every hull), about forced rows, and about a system's OUTPUT - what `stripForJson`
  sends, not the raw property (a Trek impulse drive sends its rating plus its nacelles': 10 where the property
  says 2; no EA, raider or Brakiri hull has such an engine); 19 new situations, for EVERY hull - pivoted to
  port / starboard, each with the heading in the aft half, a pivoted ship that turned last turn (shortened
  too), at a standstill, braking through it, accelerating, slowing, with damaged thrusters; still pivoting
  WITH the forced pivot the page adds at activation (`doForcedPivot`, client-side), also out of alignment;
  rolling out of alignment and stern-first; rolled and pivoted - and random situations now start out of
  alignment, pivoting or rolling some of the time. `--gravitic` (only gravitic hulls), `--part=K/N` (one N-th
  of the hulls, to run a set in parallel containers), a "gravitic hull is plotted" check and the count in the
  summary line.
* **Proved RED first:** against the unchanged ShipMover the harness failed on all 62 gravitic hulls of the
  Brakiri, Brakiri raider and raider lists (refused); with only the refusal lifted the server half passed
  (415,787 checks - it can check none of these rules) and the client replay failed **7,943 of 25,126 plans**
  (the page offered turns while rolling and speed changes while pivoted). The new situations on plain EA
  hulls passed before any ShipMover change (2,376 plans) - the plain out-of-alignment rules already held.
* **Gates, 2026-11-19:**
  * Gravitic hulls (`--factions=brakiri,brakiri_raiders,raiders --gravitic`): **62 hulls, 3,082
    situations, 27,595 plans, 206,768 steps replayed through movement.js, 0 differences; 458,068 server
    checks.**
  * The default set (EA + raiders, all situations, the raiders' 7 gravitic hulls now among them):
    **157 hulls, 7,917 situations, 64,035 plans (4,945 shortening a turn in the search, 1,534 with the last
    turn shortened, 1,208 of them holding thrust back; 11,735 allowed to overthrust), 450,770 steps, 0
    differences; 1,127,242 server checks** (slowest search 1.25 s; 5 parallel containers, 375 s).
  * Mutation pass: 16 broken copies of the port (each gate exception, the reversal rule, the pivot rotation,
    port/starboard swapped, the accel / turnOrSlip arguments at each caller, the pivot offsets, the rolled
    swap, the flag never set, the refusal put back), on ten hulls (Rakarta, Halik, Avioki, Brikorta, Kabrik,
    Vasy, Jia, Pikitos, Torsha, Vastar): **16 caught, 15 of them ONLY by the client replay**. Two are the same
    in effect (port/starboard swapped = accel false: a speed change is the only use).
  * `hazardsHarness` 14,758 checks; `flagOffHarness`, `cpuLobbyHarness` green; **replay check 228 passed,
    0 failed**. The fire and orders pairs load nothing that changed (no ShipMover).
  * **Every gravitic hull of every faction** (`--factions=all --gravitic`, informational): 366 hulls, 17,956
    situations, 155,550 plans, server half clean. **The 304 that are not Star Trek hulls: 0 differences**
    (Minbari, Vree, Hyach, Drakh, Yolu, Ipsha, Vorlons, Triad, Kirishiac, Walkers, Torvalus, Streib...). The 62
    Trek hulls differed - first because the harness sent the impulse drive's raw output (fixed, above), then in
    923 of 28,443 plans, every one with the IMPULSE DRIVE ITSELF damaged: the drive's `getOutput()` is its
    rating plus its nacelles' and nothing else, and the page adds the drive's own criticals to that
    (`systems.js getOutput`), so ShipMover read a damaged drive's thrust too high. All 63 impulse-drive hulls
    are gravitic - lifting the gravitic refusal is what exposed them. The Shadow hulls' `BioDrive` (its
    `getOutput()` sums the bio-thrusters; not gravitic, plotted since T1b and never in the harness) had the
    same gap: 312 of 8,238 plans, all with the drive damaged (page 25, ShipMover 27).
  * **Fixed at the user's request (2026-11-19):** `readSystems` now adds a `TrekImpulseDrive`'s or `BioDrive`'s
    own `outputMod`, less its one-turn drain (`EdfThrustDrain`), to its `getOutput()` - what the page does with
    the figure it is sent (the Kelly Constitution's drive keeps Engine's rules and needed nothing). **Trek: all
    63 hulls plotted, 28,429 plans, 0 differences; Shadows + customs: 49 hulls, 19,777 plans, 0 differences.**
* **Self-play, 24x16** (no refused submission, no defect in any):
  * Brakiri (Tashkat, Avioki, Halik, Falkosi) moved by ShipDecider against the same fleet moved by the T1b
    rule (`--plain-move`), both seat orders: **T5 ahead 2 of 2** (51% and 40% of its value kept against 0%,
    21 and 19 turns) - the movement search does for gravitic hulls what it did for plain ones (T5a 6 of 6).
  * Brakiri against the default EA fleet (2,303 v 2,347 points), both seat orders: EA ahead both times (45% v
    0% in 33 turns, 72% v 8% in 28). A raider gravitic fleet (Vastar, Jia, Vasy, Hybrid Saucer - the last is
    NOT gravitic, its flag is commented out) against Hyperion, Tethys and an Aurora flight, simultaneous
    movement: 0% v 97% in 24 turns. **Not a movement fault as far as one kept game shows** (4776: every
    Brakiri hull decided by ShipDecider; the Avioki closed at speed 7 on turn 1 - fire 705 against danger 725,
    the "best trade" call - and was destroyed): the Brakiri guns are short-ranged (Graviton Pulsar: range
    penalty 1 a hex) or slow (Graviton Beam: 4 turns to load), and the Tashkat fired each gun only 1-4 times
    in 16 turns. How the bot fights such a fleet - range, the slow-gun floors - is tuning for T5f, and a
    faction's readiness (section 7) asks for 20 games against EA without a defect, not wins.
* **Left for T5e (pivots and rolls):** the bot still never pivots or rolls, so it never puts a gravitic hull
  out of alignment - the gravitic way of fighting (pivot to keep the guns on the enemy while flying on)
  waits for T5e and a ruling there on whether gravitic hulls should pivot as a matter of course, beyond the
  damaged-ship uses ruled for all hulls. And when a bot ship is still pivoting at the start of its move,
  ShipMover must write the forced pivot row itself: the page adds it at activation (`doForcedPivot`), the
  server does not. *(Both done at T5e, below.)*

**T5e as built (2026-11-19) - COMPLETE, uncommitted, not play-tested by the user (final gates confirmed the same
evening, below).** Pivots and rolls. No flag,
no migration, no new class.
* **The user's notes, 2026-11-19 (the spec):** "Both are used to hide damaged sides, however there are some
  other niche uses. For example, a ship might pivot in a situation where it was desperate to bring heavy weapons
  into arc on an enemy (and perhaps expected to get destroyed next turn). Sometimes, especially Gravitic ships, a
  player will start and then stop a pivot on the same turn, providing they have enough thrust to do so in order to
  get an enemy in it's arcs, and then can turn while pivoted on following turns. In the case of rolls, the most
  common use after hiding damage is to swap side thrusters so you can turn in a desired direction the following
  turn after the roll completes. Both manoeuvres begin a pivoting / rolling motion and need to be manually stopped
  ... Otherwise they will simply continue indefinitely ... pivots and rolls are still very rare, advanced types of
  movement so I would gate them accordingly and probably only set Hard opponents to use Pivots or Rolls. Perhaps
  Normal could in the case of rolling to swap thrusters to turn in a direction it wanted next turn too, but
  otherwise no." The 5.8 table has the row.
* **What the page and server do** (read before building): the PIVOT ARROWS (`canPivot` / `doPivot`) start a
  pivot, or - while pivoting - STOP it (the same-side arrow writes the opposite pivot, facing unchanged); a non-agile
  hull starts and stops one a turn, an agile hull's every pivot just turns its facing. A pivot is paid any + rear +
  side through one of two thruster pairs (`calculateThrustStillReq` drops the other pair once one has a point) and is
  never auto-paid. The ROLL (`canRoll` / `doRoll`) starts one, or stops one carried over (a roll started this turn
  cannot be stopped this turn); a non-agile hull's sides swap at the turn change while rolling (the server flips
  "rolled"). A pivot carried over goes on a step at ACTIVATION - the page's `doForcedPivot`, client-side; the server
  KEEPS that forced row (only forced jinks are dropped). Fire: a pivot this turn, or rolling at fire time, takes 3 off
  every shot of the ship (`Weapon::calculateHitBase`); a rolled ship's bearings are mirrored (arcs and hit sections
  swap sides).
* **`ShipMover` (mirror + oracle):** orders `pivotleft` / `pivotright` (the arrows - `actions()` names the arrow
  clicked, which is the opposite of the row's type for a stop) and `roll`; `payPivot` (each pair in turn, its odd
  point through it too; the pair rule in `stillRequired`, gravitic pivot rotation included); `forcedPivot()` at
  construction (posted `forced`, not an order); `state()` carries `pivoting` / `rolling` (a pivot left running is part
  of the end state - the search's and `keepPlan`'s keys too); `allowPivots()` / `allowRolls()` (off by default: the
  search is otherwise unchanged); `plan()`; `turnPayableNextTurn($right, $speed)` (could next turn's turn to that side
  be paid, with the sides as a roll leaves them - a judgement aid).
* **`ShipDecider`, by the seat's level** (`MoveDecider` passes `$seat['cpu']['level']`; every seat is Normal until
  T8's picker - `selfPlayHarness --levels=hard,normal` sets them):
  * EVERY level stops a pivot or roll carried over (the search tries the stop; one left running costs
    `MOVE_COST_LEFT_RUNNING` 40 hexes) - before any turn or slip when the hull has no gravitic drive.
  * HARD tries, on the end of its 10 best plans (one per end hex, `MOVE_MANOEUVRE_SEEDS`): a pivot started and
    stopped the same turn either way (an agile hull: single and double pivots), and a roll. NORMAL tries only the roll,
    and keeps it only where it frees the turn it wants next turn (`turnFreed`: its quarry's side, more than 30 degrees
    off the bow; its side thrusters cannot pay that turn and can once the roll has swapped them -
    `MOVE_VALUE_TURN_FREED` 14 hexes). EASY tries neither.
  * Priced by: the engine's -3 (the plan's pivot / roll rows stood in while this turn's fire is asked);
    `MOVE_COST_PIVOT` 4 a row (start + stop 8), `MOVE_COST_ROLL` 8; ending out of alignment without a gravitic drive
    `MOVE_COST_MISALIGNED` **100** hexes - times the survival share only for a pivot started this turn (the "desperate"
    swing), and that share never below the share of combat value left; next turn with the sides a roll swaps.
  * **A pivot pays for itself THIS turn** (next turn judged from its seed's facing) and **a roll is judged on
    protection and the thruster swap** (next turn's fire is its seed's) - see the self-play findings.
* **Self-play findings that changed the pricing** (24x16, Hard v Normal, EA and Brakiri fleets, seats alternated):
  * Batch 1 (12 games): a fresh Tethys and an agile Halik pivoted on turn 1 with nothing in reach, and fresh Hyperions
    rolled on turns 1-2 - all bought by NEXT-turn terms, whose straight-on projection over-credits a turned facing or
    mirrored arcs (an aligned, unrolled ship would simply turn next turn). The Tethys also had the "desperate" discount
    because the danger term read it doomed at 13 hexes on turn 1. Fixed as above.
  * Batch 2 (8 games): game 4796 - a damaged Hard Omega pivoted on turn 19 (hiding a damaged side, sound) and then spent
    17 turns misaligned, weaving with slips from 22 to 46 hexes off the fight. Replayed (turn 25): 9 hexes off the map,
    unable to turn; the plans that realigned ended a hex further out and the off-map hazard (100 a hex) outweighed the
    old 12-hex misalignment. Hence 100, undiscounted for a ship that began its move misaligned.
  * Every roll was stopped the turn after - except by a ship whose engine died the turn it rolled (game 4785, physics).
* **Harnesses:**
  * `shipMoverHarness.php` / `shipMoverOracle.js`: walks use the arrows and the roll; the search with pivots and rolls
    replays a pivoting and a rolling plan per situation; the page is told each hull's `pivotcost` / `rollcost`; the
    oracle applies the page's `doForcedPivot` at activation and compares a row's `value` as text when it is not a
    number; new situations "still rolling, rolled" / "still rolling, turned last turn".
  * NEW `manoeuvreHarness.php` (DB-free): A/B a roll / pivot carried over is stopped at every level; C Easy and Normal
    never start a pivot and never roll with sound thrusters (random boards); D the thruster swap (Normal and Hard roll,
    Easy does not; nobody with sound thrusters); E Hard pivots to turn a destroyed side away (Corumai, no turn possible;
    danger 136 against Normal's 910); H Hard rolls for the same (Nova Alpha: next danger 519 against 913); F a pivot's
    next-turn danger is its seed's; G a fresh plain hull pays the whole misalignment; I a misaligned hull realigns, at the
    map edge too (the 4796 case); J a roll's next-turn fire is its seed's; K a gutted hull (every weapon and two sections
    gone) that BEGAN misaligned, under the fire of two Omegas that have moved, still pays the whole misalignment on every
    plan that stays so (added at the final gates: I has nothing in reach, so it could not tell the "desperate" discount's
    guard from none). **606 checks at the default 12 rounds.**
  * `selfPlayHarness.php --levels=`.
* **Gates, 2026-11-19:**
  * ShipMover pair (before the decision changes): EA + raiders **157 hulls, 79,939 plans (7,158 pivoting and 6,577
    rolling search plans), 592,371 steps, 0 differences; 1,418,677 server checks**; gravitic set **62 hulls, 33,573
    plans, 260,841 steps, 0 differences; 562,102 server checks**. The server's own pivot-pair check
    (`validateThrustPayment`, enforced in the harness) never disagreed with the page.
  * Mutation pass: mirror **17 of 17 caught** (16 only by the client replay); decisions - see the final figures below.
* **Final gates, 2026-11-19 (evening, the code as it stands):**
  * ShipMover pair: EA + raiders **157 hulls, 8,195 situations, 79,939 plans (7,154 pivoting and 6,584 rolling search
    plans replayed), 589,410 steps through movement.js, 0 differences; 1,419,796 server checks, 0 failed**; gravitic set
    (Brakiri, Brakiri raiders, raiders, `--gravitic`) **62 hulls, 3,170 situations, 33,573 plans (2,707 / 2,485), 260,841
    steps, 0 differences; 562,102 server checks, 0 failed**.
  * Decisions mutation pass (13 broken copies of `ShipDecider` / `ShipMover` loaded ahead of them, `manoeuvreHarness
    --rounds=4`, 234 checks): **12 of 13 caught**. Survivor: "Normal may pivot too" - equivalent, as Normal's filter keeps
    only a roll that frees a turn, so a pivot variant is dropped either way. Two needed work first: "a pivot keeps its
    pivoted facing next turn" (its first copy did not parse - a dangling `else` - and was rebuilt), and "the damage
    discount for a ship that began misaligned" survived until check K was added.
  * `manoeuvreHarness` **606 checks, 0 failed**; `flagOffHarness`, `cpuLobbyHarness` green; **replay check 228 passed,
    0 failed**.
  * Self-play, final batch (8 games, 24x16, Hard v Normal, EA and Brakiri fleets, seats alternated; no refused submission,
    no defect): **Hard ahead in 3 of 8** - EA 2 of 4 (4804 53% v 0%, 4810 73% v 10%; lost 4811 0% v 53% and 4808 13% v 22%
    at the turn cap), Brakiri 1 of 4 (4807 25% v 5%; lost 4806, 4809, 4805). Pivots and rolls are rare and small; telling
    the levels apart is T5f's job (its ladder), not this sub-stage's. Kept games: 4804-4811 (delete by id, not
    `--delete=all`: that would also take 4767 / 4768, which the replay corpus has adopted).

**T5f - levels and the ladder (started 2026-11-19; COMPLETE 2026-11-20 - the user: "let's mark Stage T5f (and Stage T5
overall) as complete for now").** The stage's "done when" (section 10): Hard beats Normal beats Easy in self-play ladders at
>= 60% - met for Normal over Easy; Hard over Normal reached a few points, which the user accepted for the first version (T5f-9). Its content is the 5.8 skills table, the user's feedback on game 4774 (below), and
the two things earlier sub-stages parked here: the health model (T5c, "a model limit met while tuning") and Hard's fleet
focus in Movement (4750 note 5).

**The user's feedback on game 4774 (2026-11-19)** - human G'Quan + Va'Kar Long-Range Scout + Frazi flight v CPU (Normal)
Omega x2 + Tethys + Aurora and Thunderbolt flights, 42x30, simultaneous movement: "The Aurora Starfuries probably didn't
respect the CCEW of the Va'Kar enough on Turn 2 and got quite close. This would be fine for an Easy CPU opponent, but a
Normal one would be slightly more wary (see position of Thunderbolts on the same turn) and a Hard one even more wary
might also jink to balance having reasonable hit chances vs reducing chances of being hit to near 0. Hard fighters could
also find hexes where there is little or no light weapon coverage from the enemy and try to position themselves there.
Normal mode could do a simpler search for this positioning too."
* **What the bot did, replayed** (scratch: the game wound back to the flights' activation, as for 4750): the Va'Kar
  (CCEW 6, DEW 3) had moved; the Aurora flight parked 1 hex DEAD ASTERN of it - the blind spot of all three Twin Arrays,
  where only the two aft Light Pulses bear. So it already used the ship's arcs; what it misjudged was the trade. Its
  chances (own guns / each Light Pulse at it): 1 hex astern 85 / 80 (chosen); 2 astern 75 / 60; the Thunderbolts' hex,
  3 off, 65 / 60; every 2 jinking levels take 10 off both (limit 6). Scored: fire 430 against danger 88 at 1 hex, 379
  against 63 at 2.
* **Why: the damage model.** Since T2 a point of damage is worth a unit's value over what is left of its PRIMARY
  structure (`AIView::healthLeft`) - the Va'Kar's 36 of ~213 structure - while a flight's health is every fighter's. So a
  flight valued its shots at a ship about twice too high against its own losses. The same flaw WASTES FIRE: FireDecider
  stops counting damage on a target past 1.5 x that health (`FIRE_OVERKILL`), and on turn 2 the Heavy Lasers and the
  Auroras had booked 53.4 of the Va'Kar's 54 - five of the six Thunderbolt Gatling Pulse Cannons (65% each) were never
  fired, at a ship that survived the turn and the next. (Replayed exactly: the turn's 22 orders, once the loader's
  CURRENT-turn weapon loading is put back to turn 2's.)
* **Flight search breadth:** a speed-9 flight can end in ~550-610 hexes; `FLIGHT_PLANS` (250, plainest first) reaches
  133 of them - the cone of near-straight paths. An exhaustive search takes 12-17 s. A blind spot that needs an early
  turn is never seen.
* **User rulings (asked and answered 2026-11-19):**
  * In that spot a NORMAL flight goes 2 hexes astern without jinking (75 / 60); a HARD one 3 hexes off, jinking 4-6
    (own 35-45, Light Pulses 30-40). Easy: today's boldness is fine.
  * The health fix - a ship's health is its primary structure plus the section a hit must go through - for NORMAL and
    HARD; EASY keeps the old count (the bolder fighters and some wasted fire are Easy weaknesses).
  * Hard's "flights work together" = a mix of piling onto a damaged target and mutual support, depending on initiative:
    a flight that moves after the enemy's fighters is the likelier to go for them.

**T5f plan - sub-stages** (each measured before the next; the ladder is the measure):

| | Deliverable |
|---|---|
| T5f-1 | Levels through every decider (a skills profile per level, read through `AIView`); EASY's column of 5.8 (no CCEW deterrent, simple EW, no focus, no shortening, no pulse premium, half the unmoved-enemy and overspeed weights, ballistics at any legal chance, score noise from the seat's seed); the HEALTH fix for Normal / Hard and the re-tune it forces (against a harness-only `legacy` profile = the T5e bot); FIGHTERS by level - Normal and Hard warier, Hard jinking at any level; the LADDER harness (levels alternated by seat, results summed) |
| T5f-2 | Flight search breadth: a goal-directed search in `ShipMover` towards the hexes around each target the enemy's guns cover least (mirror-safe - every plan is still `apply()`'s; replayed by the oracle) - Normal a few goals, Hard all of them |
| T5f-3 | HARD: flights work together (pile onto the damaged target, mutual support, by initiative); the fleet's heavy guns put on one chosen target in Movement (4750 note 5); the one-ply look-ahead - an enemy yet to move answered with its best reply |
| T5f-4 ✅ BUILT | HARD: power management (4750 note 10) - the page's boost rules ported to `PowerDecider`, proven by `ordersOracle` |
| T5f-5 ✅ BUILT | HARD: extended turns - `ShipMover` begins and completes one (EXTENDED_TURNS_PLAN.md; mirror + oracle) |
| T5f-6 (run) | The ladder to >= 60% both ways; play-test by the user - Hard ~54% on both fleets over 448 games, 60% not reached (the record below) |
| T5f-7 ✅ BUILT | The ENEMY FORECAST (user, 2026-11-20): Normal and Hard anticipate where each enemy ship yet to move will end - Normal one turn, Hard a wider set of the enemy's own plans, both by the enemy's fire on the bot's units (`EnemyForecast`) |
| T5f-8 ✅ BUILT | Decision time, brought forward from T8 to pay for the forecast: next turn's terms only for the best end states by this turn's score (two-pass judging); a CPU budget per ship decision and for the forecast; the self-play harness prints each level's mean and slowest decision |
| T5f-9 ✅ (run) | The ladder again, on the standard 42x30 map (user: "run on the Standard map size for a better comparison of a normal game"; "If we do the above changes and Hard is still returning the same results I'm ok with that for our first version") - final build: Hard 50% (EA) and 62% (Brakiri); a forecast audit dropped Hard's trade measure; ACCEPTED for v1 (the record below) |

**T5f-1 and T5f-2 as built (2026-11-19) - BUILT, uncommitted, not play-tested by the user.** No flag, no migration, no
new class (autoload unchanged).
* **Levels.** `AIConfig::SKILLS` - one row of player skills per level (and a harness-only `legacy` row = the Normal bot as
  it stood at T5e, which the ladder plays each change against); `AIConfig::seatLevel` / `seatSeed` read a seat. `AIView`
  takes the level and seed (`skill()`, and `noise()` - a normal draw fixed by seed, turn and key, so a game replays
  exactly); TacticalAI (Initial Orders), MoveDecider and FireDecider build their view from the seat. ShipDecider::rows lost
  its `$level` argument - the view carries it (`manoeuvres` replaces the T5e level names). Every T5f change sits behind a
  skill:

  | Skill | Easy | Normal | Hard | legacy (T5e) |
  |---|---|---|---|---|
  | `sectionHealth` - a ship's health is its primary plus the section a hit goes through | - | yes | yes | - |
  | `ccewDeterrent` / `simpleEW` (half OEW on the nearest, half DEW) | - / yes | yes / - | yes / - | yes / - |
  | `focus` (share of FIRE_FOCUS) / `heavyFocus` | 0 / - | 1 / yes | 1 / yes | 1 / yes |
  | `pulsePremium`, `shortening`, `ballisticFloor` | - | yes | yes | yes |
  | `unmovedDanger` / `overspeed` (share of the cost) | 0.5 / 0.5 | 1 / 1 | 1 / 1 | 1 / 1 |
  | `flightDanger` - a flight's own losses against the damage it deals | 0.75 | 1.5 | 3.0 | 0.75 |
  | `jink` | max | max | any | max |
  | `manoeuvres` (T5e) | none | swap | all | swap |
  | `noise` (sd, hexes of own value) | 10 | 0 | 0 | 0 |
  | `coverageGoals` (T5f-2) | 0 | 4 | 12 | 0 |
* **The health fix** (`AIView::healthLeft($unit, $shooter = null)`): primary structure plus, from a shooter's side, the
  section(s) its bearing hits shared by profile (as `exposureAt` picks them), else the mean of the outer sections; a
  destroyed section adds nothing. Movement prices each target from where the unit ends (`ShipDecider::targetsFrom`, asked
  by FlightDecider too) - so a target's destroyed side is worth more per point, rightly. Fire and launches use the mean.
  Replayed on 4774 turn 2: the Va'Kar's health 36 -> 80, the G'Quan's 50 -> 113; Normal's plan fires all six Gatlings (27
  orders, not 22). Easy keeps the old count by ruling.
* **Fighters (4774):** the danger weight by level; HARD prices every jinking level on its `FLIGHT_JINK_PLACES` (12) best
  places. Replayed: Easy ends 1 hex astern of the Va'Kar (as it did), Normal 2 astern without jinking (75 / 60 - the
  ruling), Hard 2 astern jinking 6 (own 45, Light Pulses 30 - the ruling's risk, one hex nearer, as that hex gives better
  own chances for the same incoming fire; 3 off jinking 6 scored next).
* **Easy's column** (5.8): EWDecider's half-and-half split (the page's limits kept: a Restricted EW half stays DEW, an LCV
  keeps its rule), no CCEW deterrent, no focus at all, heavy guns drawn as light ones, no pulse premium, no turn shortening
  (search or last turn), an enemy yet to move at half, half the overspeed cost, launches at any chance an order is given at,
  and score noise on every Ship / Flight decision.
* **T5f-2, the coverage search** (`ShipMover::enumerateToward`, `FlightDecider::coverageSearch`): the plainest-first search
  reaches 23 of the 36 hexes within 3 of the Va'Kar for the 4774 Aurora; the aimed search - the same search best-first by
  how well the hexes left fit the goal and how far the heading is off it and off the facing asked - reaches all 13 others,
  most at every facing, ~10-15 ms a hex and facing. The decider judges every hex within `FLIGHT_COVERAGE_RANGE` (4) of each
  target in reach, facing it, no further than the flight's speed less 1 (beyond that a plan rarely has the thrust to face
  it), as if the flight stood there; asks the best for plans until `coverageGoals` are reached (3 x as many tries at most);
  and judges what it finds with the rest. Replayed on 4774: Normal reached 4 places in 7 tries, Hard 12 in 26, and both
  went for the G'Quan's blind side (fire ~102, danger ~0 jinking 1-2; it ran no CCEW) rather than the Va'Kar's rear - the
  plainest search had never seen the G'Quan in reach. The decision line says "N of M less covered places reached".

**T5f-3 as built (2026-11-19) - BUILT, uncommitted, not play-tested.** Hard only, four more skills (Easy, Normal and legacy
have them off):
* `pileOn` (1.0) - flights pile onto a damaged target (the user's ruling): a target's worth to a flight x (1 + the share of
  its value already lost), the fleet's focus target x (1 + half the skill).
* `support` (2.0) - mutual support, by initiative: hexes of the flight's own value for each hex beyond `FLIGHT_SUPPORT_RANGE`
  (4) of the nearest other flight of the bot, while the enemy has flights - in full while they have yet to move (they could
  fall on a flight caught alone), a quarter once they have (a flight moving after the enemy's fighters knows where they are -
  "and therefore attack them").
* `fleetFocus` (0.5) - the fleet's heavy guns on one chosen target (4750 note 5): `MoveDecider::focusTarget` picks, each
  Movement phase, the enemy ship the fleet's heavy guns could most nearly finish as the units stand (expected damage over
  what finishing it takes, no more than all of it, times its value - damage in raises the same target's score, so the choice
  holds); a heavy gun's fire at it counts 1.5 in ShipDecider this turn and next (`focusFactor`), and flights pile on it.
* `bestReply` - the one-ply look-ahead (5.8: "assume each enemy's best reply"): an enemy yet to move is priced from the
  worse, for the ship, of flying straight on and closing as near as its speed and one more point allow, stopping a hex short
  (`ShipDecider::replyHex`) - all its guns, as before; the closer hex is the better shot. **Switched OFF at every level after
  the ladder** (below): it raises what an enemy closing does to the ship and not what the ship then does to it, so Hard
  stood off. Kept for a version that prices both sides of the reply.
* Harness tools: `selfPlayHarness --skill=hard:manoeuvres=swap` (`AIView::$overrides`, harness only) asks a tuning
  question without editing the levels.

**Gates, 2026-11-19 (T5f-1 to T5f-3):**
* ShipMover pair with the aimed search: EA + raiders **157 hulls, 8,206 situations, 80,619 plans (583 aimed-search plans,
  of 30,197 random goals asked - most random hexes a ship cannot end in), 595,328 steps, 0 differences; 1,429,124 server
  checks**; gravitic set **62 hulls, 33,872 plans (342 aimed), 262,127 steps, 0 differences; 565,294 server checks**.
* Fire pair at Normal and Easy (`fireHarness --level=`), phases 3 and 1: **0 differences** (the harness's own threshold
  check made level-aware: Easy has no pulse premium and no launch floor). Orders pair (`ordersHarness --level=`): Easy
  **0 differences** (46% of its EW points offensive - the half-and-half split), Normal 0 differences.
* NEW `tests/ai/levelsHarness.php` (DB-free): A the health model (Easy primary alone; Normal / Hard the outer mean, or the
  section facing the shooter, a destroyed one nothing; a flight's health whatever the level), B `targetsFrom` keeps a
  worth's weight, C Easy's EW (half OEW on the nearest, no CCEW, a Restricted EW half kept), D the coverage search (Easy
  never; Normal within its 4; Hard beyond it; what it reaches is new), E the overkill fix (Normal books more fire on a scout
  than Easy), F Easy's noise is the seed's (Normal seed-free), G the fleet focus picks the damaged ship and draws heavy guns
  only, H the one-ply reply's geometry. **132 checks.** Mutation pass: **13 of 13 caught** (one - Hard held to Normal's 4
  goals - only after D was given a per-decision check).
* `manoeuvreHarness` 662, `hazardsHarness` 14,758, `flagOffHarness`, `cpuLobbyHarness`: green; **replay check 228 passed,
  0 failed**.
* **The ladder** (`selfPlayHarness --alternate`, 24x16, mirror fleets, the levels swapping seats every other game; no
  refused submission, no defect in any game):

  | Pairing (code) | Fleets | Result |
  |---|---|---|
  | Normal v legacy (T5f-1) | EA default | Normal ahead 5 of 8 |
  | Normal v legacy (T5f-2) | EA default | Normal 6 of 8 - **11 of 16 overall (69%)** |
  | Normal v Easy (T5f-1) | EA default | Normal 7 of 8 |
  | Normal v Easy (T5f-3) | Brakiri | Normal 8 of 8 - **15 of 16 overall (94%)** |
  | Hard v Normal (T5f-1/2) | EA default | 4 of 8 |
  | Hard v Normal (T5f-3, no reply) | EA default | 6 of 8, and again 2 of 8 |
  | Hard v Normal (T5f-3, no reply) | EA, two flights a side | **8 of 8** |
  | Hard v Normal (T5f-3 with the reply) | EA default / Brakiri | 4 of 8 / 2 of 8 - the reply switched off |
  | Hard v Normal (T5f-3, no reply) - 12 processes x 3 | EA default | **16 of 33 - even** (3 games lost to a harness deadlock) |
  | Hard v Normal (T5f-3, no reply) | Brakiri | 2 of 8 |
  | Hard without pivots / rolls (`--skill=hard:manoeuvres=swap`) v Normal | Brakiri | 5 of 12 |
  | Hard without the fleet focus (`--skill=hard:fleetFocus=0`) v Normal | Brakiri | 6 of 12 |

  **Where the levels stand:** Normal beats Easy clearly (15 of 16) and the old T5e bot (11 of 16), so the health fix and
  Easy's column did what they were for. Hard's edge so far is its FIGHTER play (8 of 8 with two flights a side). In fights
  between ships Hard is level with Normal on EA fleets, and on Brakiri the full Hard (gravitic pivots + fleet focus) lost 4 of
  16 while either one taken away came out about even - both are suspects, neither proven at these sample sizes. **Batches of
  8 games are close to coin flips at this gap** (the same Hard code went 6-2 and then 2-6 on EA fleets); T5f-6 needs batches of
  30 or more. So the stage's "done when" is met for Normal over Easy and not yet for Hard over Normal.
* **Found on the way:** with 12 harness processes deleting games at once, `DBManager::deleteGames` hit a MariaDB DEADLOCK
  (40001) and the process died, leaving game 4911's other rows behind (cleaned since). The harness now retries the delete.
  Not changed in the engine: on live, deletes run one game load at a time (memory: deleteGames runs on every game.php load).
* `shipDanger` - the ships' danger weight (MOVE_DANGER_WEIGHT 0.75) as a skill, the same at every level: a knob for T5f-6.

**Next (T5f-4 to T5f-6):** Hard's power management - `PowerDecider` boosts the scanner or the engine with the power of
one-turn weapons that cannot reach anything this turn, switched back on a turn before the enemy can be in their reach (the
page's boost rules ported and proven by `ordersOracle`, as the deficit rules were at T3); Hard's extended turns (`ShipMover`,
EXTENDED_TURNS_PLAN.md, mirror + oracle); then the ladder in batches of 30+ - Hard's pivots on gravitic hulls, the fleet
focus and `shipDanger` first - until Hard leads Normal by 60% on EA and on Brakiri; then the user's play-test.
*(Done 2026-11-20 - the three records below.)*

**T5f-4 as built (2026-11-20) - Hard's power management (the user's 4750 note 10). BUILT, uncommitted, not play-tested.** No
flag, no migration, no new class. The skill `powerBoost` (Hard only; Easy, Normal and legacy have it off - their Initial Orders
are byte-identical: the orders harness writes the same decisions as before, file for file).
* **`PowerDecider::boost($view, $plan)`**, called by `TacticalAI::initialOrders` after `plan()` (the deficit rules, unchanged),
  on the MASKED view - it reads where the enemy is (P2) - and BEFORE `EWDecider::plan`, which then spends a boosted scanner. Per
  ship whose balance the port answers for (`understands()`) and that is not in deficit:
  * **Reach** (`reaches()`): each pair of the ship and an enemy is asked as close as both could come this turn - each at its
    speed plus all the acceleration its thrust buys, and `POWER_REACH_SLACK` (2) hexes for what the bot cannot see (an engine
    boost) - with the real hit formula (`AIView::estimate`, arc-free) and the BEST case for the shooter: every EW point of its
    ship locked on, no DEW against it. So a gun goes off only when it surely cannot fire this turn.
  * **Interception kept** (`idle()`): an interceptor stops one ORDER (`InterceptModel`) - one per enemy gun or launcher that
    could reach the ship (a pulse gun's `defaultShots` is its pulse count, not its orders) - so that many of the ship's one-turn
    interceptors stay on, the best intercept rating first. Ruled 2026-11-20 by the implementer: the user's note says nothing of
    interception, but switching off a Standard Particle Beam (intercept 2) that the enemy's shots need would be a Hard player's
    mistake.
  * **Idle guns**: every other one-turn gun the page's Off button allows (`canOffline`: drawing power, on, not locked, not
    boosted, no fire order; and here no ammunition, no compound mount, not overloading) that reaches no enemy. They pay for
    boosts, least useful first (damage a turn per point of power), only as many as a level needs - a gun is never switched off
    for nothing - and the reactor's spare power is spent first.
  * **What it buys**: a level of the SCANNER first while there is a fight this turn (a loaded gun of either side reaches, or a
    launcher of the ship's could launch), else the ENGINE; whichever cannot be bought, the other; until neither can. Prices and
    limits are the page's: `power.js countBoostPowerUsed` ('output+1' = output+1, output+2, ...; a number, that much a level - read
    from the payload, which an Advanced Engine Module changes), `clickPlus`'s limit (a Scanner's `maxBoostLevel` when above 0;
    an Engine has none), `canBoost` (switched on; a scanner only the highest-rated - `getHighestSensorsId`). Only an ordinary
    `Scanner` / `ElintScanner` (every EW system of the ship one of those) and an ordinary `Engine` are boosted (P4).
  * **"Back on as the enemy nears"** needs nothing more, and the horizon is THIS turn, not "a turn before" as the design text
    above said (ruled 2026-11-20 by the implementer): `Weapon::calculateLoading` gives a one-turn gun switched off at turn N
    `turnsloaded` 0, the turn change adds 1, so it is loaded again at N+1 when switched back on - nothing is lost by waiting
    until it can reach. The bot carries no row over: every turn starts with every gun on.
  * Not modelled: ARCS (a facing changes in Movement - the same guess EWDecider makes), so a gun out of arc but in range stays
    on; the JUMP ENGINE is not switched off to pay for boosts (the user's note is about light weapons; the deficit rules already
    shed it first).
  * The Initial Orders decision line adds `boosts: scanner +n, engine +n (k idle guns switched off)`.
* **A boost lasts a turn** (`ShipMover::safeSpeed($share, false)`, `fastestTurnable(false)`, `engineBoost`): ShipDecider judges the
  safe and the turnable speed on the thrust WITHOUT this turn's engine boost - a speed built on a boost must be handled next turn
  without it. Identical for every other level (no boost, no difference).
* **Gates:** `levelsHarness` section I (far: one-turn guns off, the engine boosted, never one gun more than the boosts need,
  the page balance at or above 0; at 55 hexes the Heavy Lasers reach both ways: the Standard Particle Beams go off, the
  Interceptors stay on, the scanner is boosted and the EW rows spend the boosted output; near nothing goes off; near a fighter
  flight no gun that reaches it goes off; the scanner chosen is the page's on hulls with two or three - Tonak, Fwellgon; the safe
  speed without a boost), 175 checks after T5f-4. Orders pair at Hard (`ordersHarness --level=hard` puts every enemy 30-120 hexes
  off in half its situations; `ordersOracle.js` clicks every boost in with the page's plus button, checks `canBoost` before each
  click, and lets an "off" row through on a ship not in deficit only when a boost pays for it - never one more than the deficit
  and the boosts need): **156 hulls, 936 situations, 187 ships boosted, 320 levels clicked in (33 on scanners), 0 differences**.
  Normal and Easy: **0 differences, and the decision files byte-identical to the previous session's**. Mutation pass: **8 of 8
  caught** (an over-buy, an extra gun, the lowest scanner, the reach ignored, a reactor "boost", a cheap price, a multi-turn gun,
  an already-off gun) - two of them (lowest scanner, reach ignored) only once levelsHarness got the flight and two-scanner cases.
* **Seen in self-play** (42x30, Hard v Normal, default EA fleets, 59 turns): almost no boosts - the fleets start inside each
  other's Heavy Laser reach (~90 hexes best case), so no light gun idles that the enemy's shots do not need; a damaged Omega
  late in the game spent the spare power its deficit left on an engine boost. Larger maps and slow fleets are where it shows.

**T5f-5 as built (2026-11-20) - Hard's extended turns (EXTENDED_TURNS_PLAN.md; plan 5.8: "Hard - yes, where possible"). BUILT,
uncommitted, not play-tested.** No flag, no migration, no new class.
* **`ShipMover`, a mirror of the page's extended turns** (movement.js "EXTENDED TURNS"): the BEGIN (`EXTEND_LEFT` /
  `EXTEND_RIGHT`, the row's own type) when `qualifiesForExtendedTurn` says so - canTurn's every condition but the thrust (a new
  `$ignoreThrust`), the delay met outright (no agile snap, D7), a cost of at least 2 and a quarter of it in engine thrust (D3);
  the page offers it by its map icon where a whole turn is unaffordable and by the Make Extended Turn box on a turn's panel
  otherwise - paid as far as the thrusters and engine allow but a point short of the whole (D2: `click()` now refuses the point
  that would leave nothing owed), at least a quarter, then committed with what is still owed BY ROLE as its value (the page's
  JSON). After it no turn, pivot, roll or second begin this turn - every gate asks `extendedForbids` with the row type it would
  create, against the server's own `Movement::$extendedTurnBlockedTypes` (the mirror pair's server half). The NEXT turn:
  * the page CANCELS it by itself at activation, after the forced pivot (T19), when it can no longer be completed -
    `forcedExtendedCancel()` at construction writes the same forced `extendTurnCancel` row ('engineShorted' first, D9; else the
    payability dry run - `canPayRequirement`, the page's greedy `assignThrust` loop, cheapest thrusters first, the page's ceiling
    of twice a rating - and then 'thrust' or 'thrusters');
  * otherwise the COMPLETION (`COMPLETE_EXTENDED`) is the only order until it is made: a turn row with value 'extendedTurn',
    what was owed mapped onto the thrusters doing each job NOW (a roll since swaps the side role), paid within the ratings if it
    can be and over them up to the page's ceiling if it cannot (it cannot be put off - R6, the page's test matrix #7; `overthrust()`
    reports it), shortened by extra thrust like any turn (R5);
  * no speed change all that turn (R4, `canChangeSpeed`).
  `unsupportedReason` no longer refuses a ship owing a turn; `state()` carries `'extending'` (a plan that begins one ends where
  flying on does, so the plan keys carry it); `thrustNextTurn()` is the engine's thrust without this turn's boost.
  **The searches ask only for what they try** (`searchOptions()`): `options()` - the page's whole list, which the harness compares
  - asked `can()` of every order on every mover, and the searches threw away the jinks, the pivots and rolls they were not
  allowed and now the begins: a whole begin payment worked out on every mover of every search, at every level. Same plans (a
  bench of 36 searches - Omega, Tethys, Olympus Beta, Hyperion at speeds 4-8: 6,549 plans either way), **41% less time** (24.9 s
  -> 14.6 s on a loaded machine) - so faster than before this stage, a down payment on the T8 decision-time deliverable.
* **`ShipDecider`** (skill `extendedTurns`, Hard only): the search may begin one (`allowExtendedTurns`), but a plan is dropped
  when what it leaves owed is more than next turn's engine can pay - the page would cancel it for want of thrust, the thrust paid
  lost, and the server's tripwire log a cancellation nothing forced (EXTENDED_TURNS_PLAN.md D12). A begin's next-turn terms (and
  its straight continuation on the board) are judged with the ship turned as the completion will turn it; it costs
  `MOVE_COST_EXTENDED_TURN` (1 hex) more than a turn, for the thrust owed and the speed it locks. Every level completes a turn
  owed (the page offers nothing else first) - and so does `AIFallback::straightRows`.
* **Gates:** `levelsHarness` section J (Hard has plans that begin one at speed 10, an Omega that cannot pay a whole turn; none
  at speed 20, where the remainder would exceed its 8 thrust; Normal never; a begin's next turn is judged turned): **201 checks**.
  ShipMover pair: the harness meets every hull with a turn OWED from last turn (payable; its side thrusters lost; the engine
  destroyed; the engine shorted; rolled since; stern first; thrusters damaged), the walks begin turns at random, the search is
  asked for one, and the server's own `Movement::validateExtendedTurn` runs ENFORCED on every plan (its tripwire is left out only
  for the situation's constructed history - a quarter paid whatever the engine, which it then rightly calls a cancellation
  nothing forced); the oracle replays a begin by whichever page path applies and compares the rows the page writes at activation.
  EA + raiders: **157 hulls, 9,333 situations, 96,389 plans, 729,994 steps replayed, 0 differences - 10,433 begins, 6,660
  completions, 4,399 page cancellations; 1,812,025 server checks, 0 failed** (the first run's server half also flagged the
  constructed history's tripwire and the completions' necessary overthrust - harness rules, both settled above). Gravitic set
  (Brakiri, Brakiri raiders, raiders): **62 hulls, 3,595 situations, 41,265 plans, 326,045 steps, 0 differences - 8,568 begins,
  2,913 completions, 1,619 page cancellations; 734,785 server checks, 0 failed**. `levelsHarness` **203 checks** (one more: the
  fallback completes an owed turn first, then flies its whole speed). Mutation pass: **12 of 14 caught** - ShipMover's quarter,
  speed lock, roll remap, forced cancel, Engine Shorted, blocked list, completion value, roles and delay by the pair; the payable
  filter, the turned next turn and the fallback by levelsHarness. Equivalent: `click()`'s "never the last point" (beginExtended's
  own loop stops a point short - the rule is a backstop), and the dry run's cheapest-thruster order (feasibility unchanged on the
  tested hulls).
* **Seen in self-play** (the T5f-6 ladder's games): Hard begins one in ~5% of its ship moves, most at the END of a move -
  "move x3, extendTurnLeft": the facing holds for this turn's fire and the turn is made first thing next turn, which no ordinary
  turn can do. Legal (the page's box offers it whenever the ship qualifies) and kept as an advanced move.

**T5f-6 - the ladder (2026-11-20).** `selfPlayHarness --alternate`, 24x16, MIRROR fleets (default EA: Omega, Hyperion, Tethys,
Aurora flight; Brakiri: Tashkat, Avioki, Halik, Falkosi flight), the levels swapping seats every other game, 8 processes x 4 games
= 32 games a faction a round; knobs asked with `--skill=hard:...` (AIView::$overrides - nothing in a game sets them). The user,
2026-11-20: EA and Brakiri are in different power tiers (EA the stronger) - so the two factions are separate targets, never pooled;
a mirror match cancels the tier itself. No refused submission and no AI defect in any round.
* **A seat effect** (reported, not investigated - outside this stage): on EA, SLOT 2 won 23 of 32 games of the first round
  whichever level sat in it (Hard 5 of 16 from slot 1, 12 of 16 from slot 2); on Brakiri 18 of 32. The deployment zones are mirror
  images (x = -12 facing east, +12 facing west); candidates are the order the bots deploy and submit in, initiative ties, and the
  hex rows' offset. The alternation cancels it; the tables below also give Hard's rate from each seat and their mean.

  | Round | Hard as | EA (Hard of games, from slot 1 / slot 2) | Brakiri |
  |---|---|---|---|
  | A | built (T5f-1..5) | 17 of 32 (53%; 5/16, 12/16) | 18 of 32 (56%; 8/16, 10/16) |
  | B | `shipDanger` 1.0 (Normal 0.75) - trades only for a profit | 13 of 32 (41%) | 6 of 21 (29% - stopped: clearly worse) |
  | C | `shipDanger` 0.6 - leans further into a fight | 17 of 32 (53%; 8/16, 9/16) | 19 of 32 (59%; 10/16, 9/16) |
  | D | C, and no pivots or rolls beyond Normal's swap (`manoeuvres` swap), no fleet focus | 15 of 32 (47%) | 17 of 32 (53%) |
  | E | C, and fire focus doubled (`focus` 2) | 17 of 32 (53%) | 19 of 32 (59%) |
  | F | C, and the one-ply reply priced BOTH sides (`bestReply`, rebuilt below) | 13 of 32 (41%) | 16 of 32 (50%) |
  | G | `shipDanger` 0.45 | **21 of 32 (66%; 11/16, 10/16)** | 18 of 32 (56%) |
  | H1 | G, and the fleet focus doubled (`fleetFocus` 1.0) | - | 18 of 32 (56%) |
  | H2 | G, and bolder Hard fighters (`flightDanger` 2.0; Normal 1.5, Hard 3.0) | - | 17 of 32 (53%) |
  | I | `shipDanger` 0.45 set in AIConfig (G again, no override) - the confirmation | 16 of 32 (50%) | 14 of 32 (44%) |

* **The one-ply reply, rebuilt BOTH sides** (`ShipDecider::worseReply` and its branch in `fireTerms`, the skill `bestReply` -
  still OFF at every level): an enemy yet to move flies on or closes as near as its speed and a point allow (`replyHex`), whichever
  is worse for the ship - the ship's fire at it (times what a point of it is worth) against its fire at the ship (times the danger
  weight and the ship's own worth), BOTH priced where it would end; flying on on a tie; the ship's offence at it and danger from it
  are then that one's. The T5f-3 version priced only the enemy's fire from the closer hex. `levelsHarness` section K (the rule, a
  flight that would fly into an Omega's anti-fighter guns flies on - its danger and the ship's offence as without the reply; an
  Omega closing on the ship's beam gains by it - the danger the reply's) - mutants caught: the T5f-3 rule and an inverted one.
  Round F: worse than without it (EA 41%, Brakiri 50%) - kept off, the code kept for whoever revisits it.
* **What the rounds say.** Only one setting is clearly WORSE - Hard trading only for a profit (`shipDanger` 1.0: EA 41%, Brakiri
  29%) - and the both-sided reply (F) looks worse too. Everything else is one plateau: the danger weight at 0.45 / 0.6 / 0.75
  gives **54% / 56% / 55%** over 128 / 128 / 64 games; round G's 66% on EA did not repeat in round I (50%) - a 32-game round
  wanders by about 9 points either way. Over every round but B and F, **Hard won 103 of 192 games on EA (54%) and 140 of 256 on
  Brakiri (55%) - 243 of 448 together (54%), a lead about two standard errors from even**: Hard IS better than Normal, by a few
  points, on both fleets. Neither pivots, the fleet focus (0, 0.5, 1), doubled fire focus, bolder fighters nor the reply moved it.
  The plan's suspects (gravitic pivots, the fleet focus) were not what held Hard back: taking them away cost a little on both.
* **The stage's "done when" is NOT met** (at T5f-6 - T5f-9 below closes it: the user accepted Hard's lead for the first version and
  marked T5f and T5 complete): 60% is not reached on either fleet by tuning. Hard and Normal share almost all of their
  decision code, and Hard's own skills - fighter teamwork, the coverage search, power management, extended turns, pivots - each
  change few decisions in a 24x16 fight between ships. A real gap needs a decision Normal does not make at all (candidates: the
  enemy's own decider used to predict where it ends, instead of "straight on"; larger maps, where positioning and power count for
  more) - a question for the user. **Nothing was changed in the levels by the ladder:** `shipDanger` was set to 0.45 for round I,
  and set back to 0.75 when round I did not bear it out.

* **Found on the way (reported, not fixed):** `yoluReevaluated/lacannaReev.php` declares `class LacannReeva` but sets `phpclass =
  "LacannaReev"`, a class that does not exist - so `ShipPayload::buildShip` dies on it (the orders harness with every faction, at
  every level; a game would too). The all-factions orders pair was run without that faction.

**T5f-7 as built (2026-11-20) - the ENEMY FORECAST. BUILT, uncommitted, not play-tested.** The user, after the T5f-6 report: "I
would actually love for both Hard and Normal to have some level of movement predictions (to a simpler extent e.g. Normal might only
consider that a ship might turn once) to anticipate Enemy movement in the way players would to an extent. Also ofc, we only have to
anticipate enemy ships that come after the ship in Initiative Order (or in same bracket if Sim. Move rule is in place) since we
already know where ships that moved before us will be." One new class, `EnemyForecast` (autoload regenerated - one line).
* **Who is forecast:** every enemy SHIP of the bot's masked view that has yet to move this turn (`getLastTurnMoved() < turn`, the
  test `AIView::atFireTime` uses - one that moved before the bot's ship in initiative stands where it ended; under simultaneous
  movement one in the same bracket has not moved) and that ShipMover plots. Flights keep "straight on" (FlightDecider's own model
  of them is unchanged); bases, OSATs, Mindrider drives stay where they are.
* **How, by the level's new skill `forecast`:** Easy `none` (straight on, as before); Normal `turn` - straight on, or ONE turn
  either way before any hex of its move or after the last (1 + 2 x (speed + 1) plans, built directly: ShipMover's plainest-first
  search does not list every one-turn plan before two-manoeuvre ones), whichever brings most of its loaded guns' fire on the bot's
  units (each gun at the unit it does most to - the real formula, `AIView::estimate`, arcs asked); Hard `full` - the same measure
  over a wider set: Normal's plans and the first 80 end states of the enemy's own ShipMover search (speed changes, slips, two
  manoeuvres). Never into a blocked hex or off the map while a plan avoids it (T1b's `MoveDecider::hazard`); ties keep the plainest,
  straight on first. An owed extended turn is completed first in every plan. `legacy` (the T5e bot) none. Each end hex and facing
  is priced once. **Hard's measure was first the enemy's TRADE** (its fire less MOVE_DANGER_WEIGHT of the bot's at it, as the bot
  weighs a move of its own): the T5f-9 audit found it the worst predictor of all and it was dropped - see T5f-9.
* **Where it is used:** `MoveDecider::orders` builds it once per Movement decision and sets `AIView::$forecast`; `atFireTime`
  stands each forecast ship at its forecast end instead of straight on - so this turn's fire both ways and next turn's straight
  continuation (`AIView::ahead` goes on from the forecast end) see it, for every unit the bot moves in that decision. An enemy yet
  to move still fires with EVERY gun at the ship (ruling 9) - the forecast says from WHERE. The decision line adds "forecast: N
  enemy ships yet to move, k of them manoeuvring".
* **An enemy is any hull a player brings** - ShipMover was proven on the bot's own (ready) factions. One it throws on keeps straight
  on and the others are still forecast (the forecast is advice: before this, an exception anywhere in MoveDecider sent the whole
  activation to the fallback); the decision line then says "NOT FORECAST (straight on): <hull>: <exception>".
* **What it does, seen on the test boards:** an enemy that can come about into the bot's own hex is forecast there, for point-blank
  fire; out of a passing enemy's reach both levels forecast a turn that brings its guns round - Hard, with a slip the one-turn set
  lacks, more of them.
* **Tests:** `levelsHarness` section L (Easy and legacy none; Normal and Hard forecast the ship yet to move but not a flight, an
  enemy that has moved or their own; Normal straight on or one turn, 15 plans for a speed-6 Omega, a turn that brings more fire than
  flying on; the forecast end blocked - elsewhere; Hard weighs more plans than Normal, finds more fire on the passing board and no
  less on the point-blank one (no stand-off - RED on the trade build); an enemy ShipMover throws on is left straight on; atFireTime
  uses it and puts it back) - mutants: the "has moved" test is equivalent (ShipMover refuses a ship with orders this turn,
  atFireTime asks the same); summing a gun over targets instead of its best one survives (it shows only with two of the bot's
  ships in reach - a known gap).
* **Time:** ~0.01 s an enemy ship yet to move at Normal, ~0.1 s at Hard (plan generation 0.05 s, then each end hex priced) - once
  per Movement decision. T5f-8 bounds it.

**T5f-8 as built (2026-11-20) - decision time, BROUGHT FORWARD from T8. BUILT, uncommitted.** The user, the same day: "Can we add
Predict enemy AND move forward the decision time-improvements currently in Stage T8 to compensate for the increased work." What
section 12 had for T8, built:
* **Profiled first** (Docker, one ship on a battle board with three enemies yet to move): capital hulls 0.2-0.5 s, an Olympus
  0.6-0.9 s, a Tethys 2.1 s at Normal (492 end states) and 3.4 s at Hard (840): the SEARCH 1.2 / 2.1 s of it (the overthrust
  search costs as much as the first for 2-35 end states more), this turn's terms 0.4 s, next turn's - a second board - 0.9 s.
  Next-turn positions are only 17-26% shared between end states, so caching them was not worth it.
* **Two-pass judging** (`ShipDecider::judgeAll` -> `judgeNow` + `judgeNext`): this turn's terms and the board for every end state,
  in the search's order; next turn's only for the `MOVE_NEXT_TURN_PLANS` (100) best by this turn's score; an end state not judged
  whole is no candidate (ties still go to the plainest). Measured on 36 boards (Tethys, Olympus, Hyperion x Normal, Hard x 6): a cap
  of 20 changed 2 choices (one by 17 hexes of the ship's value), 40, 60 and 100 none; judging time -18% at 100.
* **A CPU budget per ship decision** (`MOVE_DECISION_SECONDS` 2.5, CPU time by `getrusage` - `AIConfig::cpuSeconds()` - so a
  process waiting for a CPU is not charged; SMT siblings and all-core clocks still slow its work - see the ladder below): the search
  stops at 60% of it (`ShipMover::enumerate($limit, $deadline)`, `searchCut` - the
  plainest plans are found first, straight on always), this turn's terms at 90%, next turn's at 100% - every pass judges at least
  one plan. A decision cut says so in its tac_ai_log line ("OUT OF TIME (2.1 s): ..."). Hard Tethys 3.4 -> 2.1 s with the same
  choice (cut at 809 of 840 end states), Normal Tethys 2.1 -> 1.8 s.
* **The forecast's budget** (`MOVE_FORECAST_SECONDS` 1.0): enemies nearest the bot's units first; once Hard has spent it the rest get
  Normal's forecast; the first always the level's own.
* **The self-play harness** prints, per game and level, the ship decisions' count, mean and slowest CPU seconds, how many ran out of
  time, and the forecasts' mean and slowest. First game on 42x30 (EA, Hard v Normal): Normal 71 decisions, mean 0.74 s, slowest
  2.10 s (4 cut), forecast mean 0.01 s; Hard 81, mean 0.67 s, slowest 2.16 s (14 cut), forecast mean 0.18 s, slowest 0.35 s.
* **Harness overrides only:** `ShipDecider::$budget` / `$nextTurnPlans`, `EnemyForecast::$budget` (null = AIConfig's; a budget of 0
  = none - `levelsHarness` runs without one, so its plans do not depend on the machine).
* **Tests:** `levelsHarness` section M (a Tethys judged two-pass, an Omega under the cap judged whole as before, two-pass agreeing
  with judging everything on 24 boards and never a hex below it, a microsecond budget still gives a plan, judging out of time
  judges the first plan whole, a search out of time offers straight on, the forecast's budget nearest-first) - 8 mutants, all
  caught. ShipMover pair (Omega, Hyperion, Tethys): 2,140 plans, 0 differences; replay check: 226 passed, 0 failed (2 SKIPPED:
  games 4752 and 4753 were surrendered by their creator since the record - re-record them to bring them back).
* **Ruled by the implementer:** the cap at 100, not 60 (every cap of 40 or more agreed on every board; 100 keeps a margin for ~25 ms
  a decision); CPU time, not wall time (time spent waiting for a CPU is not the decision's); the budget is per SHIP decision
  (section 12's "a budget per decision") - a simultaneous-movement activation of many ships still adds them up, which T8's review
  takes on.
* **Gravitic hulls are the budget's real customers:** alone on the dev machine a Brakiri Tashkat's decision takes 4.0 s at Normal
  (767 end states) and 6.0 s at Hard (1,110), the search 3.5 / 5.5 s of it - so in a game the budget cuts EVERY Brakiri ship
  decision, and it flies on its plainest plans. Making the search cheaper is the first item of T8's review (section 12).
* **A LADDER RUNS WITHOUT THE BUDGET** (`selfPlayHarness --budget=0`, new). The first 42x30 round ran 16 processes on the dev
  machine (Ryzen 3700X: 8 cores, 16 SMT threads): CPU time per decision rose ~3x (Hard mean 1.8 s against 0.67 s alone) and the
  budget cut nearly every decision (one game: Hard 57 of 60, Normal 49 of 53; alone: 14 of 81 and 4 of 71) - a starved bot, not the
  one a game gets. It was stopped after 20 games and discarded. A ladder measures play; the budget's own effect on choices is
  measured on its own (a Tethys cut at 809 of 840 end states chose as uncut).

**T5f-9 - the ladder on the standard map, and a forecast audit (2026-11-20).** The user: "run on the Standard map size for a better
comparison of a normal game ... ok to reduce the number you are running as long as the result is still fairly indicative ... If we
do the above changes and Hard is still returning the same results I'm ok with that for our first version." 42x30, mirror fleets,
the levels swapping seats, `--budget=0`, 24-32 games a round:

  | Round | Hard as | EA (Hard of games; from slot 1 / slot 2) | Brakiri |
  |---|---|---|---|
  | J | T5f-7 + T5f-8 as first built (Hard forecasts the enemy's TRADE) | 17 of 32 (53%; 7/16, 10/16) | 12 of 32 (38%; 6/16, 6/16) |
  | K | J, Hard forecasting as Normal does (`forecast` turn) | - | 12 of 24 (50%) |
  | L | J, no forecast at either level | - | 12 of 24 (50%) |
  | M | the final build: Hard's wider forecast by the enemy's FIRE | 12 of 24 (50%; 8/16, 4/8) | **15 of 24 (62%; 10/16, 5/8)** |

  12 of J's 32 EA games and 6 of its Brakiri ones reached the 40-turn cap (a 42x30 approach is long); no defect in any round.
* **The forecast audit** (`tests/ai/forecastAudit.php`, DB-free - J's Brakiri drop was under one standard error, and more rounds
  would not settle it cheaply): 40 random 3-a-side battle boards a faction; each enemy ship's ACTUAL move is its own side's
  ShipDecider at its level, with its own forecast of ours; our side predicts it straight on, as Normal and as Hard. Mean error of
  the predicted end hex, over 120 predictions a cell:

  | | straight on | Normal (`turn`) | Hard as first built (trade) | Hard as built (fire, wider set) |
  |---|---|---|---|---|
  | Brakiri, Hard's moves | 5.0 | 2.99 (2.81 *) | 3.01 | **2.72** |
  | Brakiri, Normal's moves | 4.9 | 2.98 | 3.03 | **2.77** |
  | EA, Hard's moves | 4.5 | 4.19 (3.81 *) | 5.27 - worse than straight on | 3.84 |
  | EA, Normal's moves | 4.2 | **4.00** | 4.71 | 4.04 |

  (* the second run: Hard's own moves change with its forecast.) Within one hex: straight on 8-19%, Normal 32-39%, the trade
  22-25%, the final Hard 33-37%. **The trade was the worst predictor of all** - at 9-15 hexes real moves are shaped by closing in
  and next turn's terms, and the trade prefers stand-offs - so it was dropped; Hard keeps its wider set (the user's "Hard fuller")
  scored by the enemy's own fire, as accurate as Normal's or better. `levelsHarness` L: "no stand-off" (RED on the trade build) and
  "the wider set finds more fire than one turn"; mutants of the new scoring caught. The audit predicts the BOT's moves, not a
  player's - but a player turning to bring guns to bear is what Normal's measure models.
* **Outcome (the user's bar: "still ... the same results ... ok for our first version"):** with the final build Hard is where it
  was - **ahead of Normal by a few points, 60% not reliably reached**: EA 50% (M) and 53% (J), Brakiri 62% (M) - its first 60%+ on
  Brakiri, but one 24-game round wanders by ~10 points (K and L, the same fleets, 50%). The forecast did not cost Hard anything
  once it predicted by the enemy's fire. Accepted for the first version, as the user ruled; Hard stays apart from Normal by what
  Normal does not do at all (power, extended turns, pivots, fighter teamwork, the fleet focus, a wider forecast). Decision time in
  these rounds (CPU, 16 processes, no budget - ~3x a decision alone): EA ships mean 1.1-1.8 s, slowest 6.9 s; Brakiri mean
  2.8-7.7 s, slowest 17.8 s - the case for T8's search work (section 12).

### 10.8 Stage T6 - withdrawal (started 2026-11-21) - COMPLETE (user, 2026-11-21), committed 442121f12

T5e and T5f were committed by the user as 074e28ff1. The stage's "done when" (section 10): **no game runs past the turn cap for
lack of a decision**. Begun at the user's request together with their feedback on turns 3-4 of game 4774.

**Marked COMPLETE by the user, 2026-11-21** ("Please also mark Stage 6 as completed as I think it is now for v1 of CPU opponents"),
after committing T6a-T6c, T6e and T6g as 442121f12. **T6d (last-resort ramming under the Desperate rule) was never built and is
deferred past v1** - the rulings for it (Normal and Hard, no usable weapon or primary at or below 25%, a target it can reach this turn)
stand in the table below and in section 5.8 for whenever it is taken up. T6f's done-when is met by the batches recorded below (batch 3:
8 of 8 games ended on their own but one, which was the bot's own surrender due the next turn; the T6g batches: the one capped game a
fight, not a stalemate).
**The user's tuning at the commit** (the values below read as built before it): `FIRE_INTERCEPTOR_MIN_CHANCE` 5 (was 15 - so every gun
with a shot fires and only a gun with none stays on defence), `SURRENDER_EARLIEST_TURN` 5 (was 3), `FLIGHT_MAX_IDLE_TURNS` 4 (was 3),
`FLIGHT_LEASH` 11 (was 10); and, after it the same day, `JUMP_DAMAGED_SHARE` **0.15** (was the implementer's 0.5, "for the user to
confirm"): a ship jumps out as "expected to be destroyed" only with its primary down to 15% (withdrawalHarness D2 now follows it). Twelve harness checks still asserted the old figures (fireRules C2/C3, withdrawal B2/C2/F2/F5a/F5b); at
T7 they were brought to the committed ones - the surrender turn and idle turns now follow AIConfig, the interception bar is pinned to the
user's 5 (fireRulesHarness `USER_INTERCEPT_BAR`), and the two checks that need a bar above the floor (C's boards under it, F5b's guns
kept back) apply only when it is.

**The user's notes on game 4774, turns 3-4 (2026-11-21)** - human G'Quan + Va'Kar + Frazi v CPU (Normal) Omega x2 + Tethys +
Aurora and Thunderbolt flights; turns 3-4 were played on 2026-11-19 by the T5e / early-T5f bot. The user's "Turn 4" is turn 3's
Fire phase, which the page shows at the start of turn 4. Every point was replayed against the CURRENT code before anything changed
(scratch `g4774_fire.php` / `g4774_io.php`: the game wound back to the phase, each weapon's reload rebuilt from `tac_fireorder` -
the loader brings back only the turns it is asked for).

| # | The user's note | What the bot did, and why | Done |
|---|---|---|---|
| 1 | Game 4774 is the basis | - | - |
| 2 | The Thunderbolts didn't launch their missiles on turn 3 though they had the chance at the Va'Kar | Turns 3 and 4: 3-4 hexes off the Va'Kar, in arc, 2 Basic missiles a craft - and `AIView::rackBlock` answered "linked firing" for every rack: the Thunderbolt's twin racks share a `linkedFiringGroup` (both missiles of one craft at one unit - weaponManager.getLinkedFiringBlock), and the bot left every linked weapon alone (P4). BallisticDecider never saw them | **Built (T6a)**: a SAME-TARGET link (no `linkedFiringSpread`: the Thunderbolt and the Armed Missile Shuttle) is offered; `BallisticDecider::linkKey` keeps every launch of one craft's link on one unit. An angular-spread link (the Vree turrets) is still left alone. Replayed: turn 3, all 12 missiles at the Va'Kar |
| 3 | Omega #2 fired a Heavy Laser at 20% - lower than a 4-turn weapon should normally fire. Why? | Turn 3 Omega #2 at the G'Quan 20%, turn 4 Omega #1 at the Va'Kar 24%: `FireDecider::holdSlow` judged each target to be DRAWING AWAY (a turn on, the same shot worse), and a slow gun's floor then dropped to a flat `FIRE_MIN_CHANCE_PARTING` 15 - against the user's ruling of 2026-11-15 ("1-33% for a 3 or 4 turn recharge would be unwise") | **Built (T6a)**: the parting floor goes by loading time like the plain one - 2 turns 20%, 3 or more 34% (a slow pulse gun +10 on both, as before). Replayed: both Heavy Lasers hold |
| 4 | Only 2 of 12 Standard Particle Beams fired at the Va'Kar on turn 4 (turn 3); too many held for interception though the danger was low - lasers can't be intercepted; humans fire 1-turn weapons at 15%+ | Two causes. (a) Omega #2 fired 2 of its 6 port beams: the Va'Kar was "dead on paper" under the old primary-only health - **already fixed by T5f's health fix** (all 6 fire when replayed). (b) Omega #1's 6 beams (25%) and the Tethys's 2 Light Pulses (25%) were held by a flat `FIRE_INTERCEPTOR_MIN_CHANCE` 30, whatever was coming | **Built (T6a)**: 15% (the user's figure) - a gun with a shot that good always fires, missiles coming or not ("where Defensive and Offensive priorities are relatively balanced, choose offence"); a gun under it is held only for interceptable enemy fire that can reach its unit (`FireDecider::interceptableIncoming`: loaded guns in arc and range that are not `uninterceptable` / `doNotIntercept`, and ballistics launched at it), one gun per order it could stop from its arc (`Firing::isLegalIntercept`), the unit's interceptors with no shot covering first. Replayed: turn 3, 24 weapons, 0 held |

**The user's notes on jumping out (2026-11-21):** "Ships should jump to hyperspace when they can no longer contribute to the battle
effectively (e.g. lost all weapons or are so damaged they can easily be destroyed) to preserve their remaining points value, this is
however quite rare and most ships fight until they are destroyed or the game is won. A damaged Jump Engine trying to open a jump
point can also destroy the ship, so if more than 50% damage is taken the Jump should not be attempted and Jump Engine can be
deactivated for extra power by Hard CPU opponents." (The failure chance IS the share of the drive's boxes lost -
`JumpEngine::getJumpFailureChance`.)

**User rulings, 2026-11-21 (asked, all the recommended options):**
* **Surrender only when it can't fight:** a CPU side surrenders when none of its units can damage any enemy (no usable weapon, or
  nothing can get within reach) for 2 turns running, while it is behind on fleet value. Never on turns 1-2. Otherwise it fights
  on to the last unit.
* **Jump out on "expected kill next turn":** a ship leaves when the enemy guns that can reach it are expected to deal more than
  what is left of it (primary + the section facing them) and its own fire can't change that - or it has no usable weapon, or it
  cannot get back into the fight (off the map, cannot brake or turn). Never with a Jump Engine more than 50% damaged; Hard may
  switch such a drive off for power.
* **Ram (Desperate rule, Normal and Hard; Easy never) when it has no usable weapon OR its primary structure is at or below 25%** -
  and only at a target it can reach this turn.
* **No fleet-wide withdrawal posture in v1** (section 5.7's "open range, keep firing" is dropped): each ship keeps its own best
  trade; hopeless ships jump out one by one; a side that cannot fight surrenders.

**Seen first (self-play game 5749, kept; EA mirror with Thunderbolts on both sides, 24x16):** both flights launched all 12 missiles
on turn 2 through the real server path; the game PASSED but ran to the 40-turn cap. The losing side's Omega (primary 6 of 60, a
section gone, its forward thrusters destroyed so it could not brake, Jump Engine intact) was 190 hexes off the map at speed 6; its
Thunderbolts (2 craft, missiles spent) sat 10 hexes from the winner, jinking too hard for any of its guns (every chance under 1%),
for 20 turns, neither side firing. The engine's own end (every pair of enemies 100+ hexes apart, `TacGamedata::isFinished`) never
came: the flight was close. Exactly the case this stage is for.

**T6 sub-stages:**

| | Deliverable |
|---|---|
| T6a ✅ BUILT | The user's 4774 notes: twin racks (same-target firing links), parting floors by loading time, the interception reserve at 15% and only against interceptable fire it can stop |
| T6b ✅ BUILT | "Can it still fight?" per unit and side (`Posture` - the best-case reach of every usable gun, as PowerDecider's reach test); SURRENDER by the ruling above, through the ordinary surrender path (`TacticalAI::submit` with 'SURRENDERED'), counted across turns in `tac_ai_state` |
| T6c ✅ BUILT | JUMPING OUT by the ruling above: the decision in Initial Orders (JumpDecider); the vortex declared where a legal plan of this turn ends within the drive's 4 hexes, entered through its mouth (the page's vortex rules, ported and oracle-proven); that plan flown in Movement; the jump-out order first thing next turn (`ShipMover` mirrors the page's Jump Out button) |
| T6d ⏸ NOT BUILT (deferred past v1 - the user marked T6 complete without it, 2026-11-21) | LAST-RESORT RAMMING under the Desperate rule by the ruling above (ShipDecider plans into the target's hex, FireDecider orders the RammingAttack; oracle-proven) |
| T6e ✅ BUILT | Hard: a Jump Engine more than 50% damaged switched off for power (PowerDecider::boost) |
| T6f ✅ MET | The done-when: self-play batches on 24x16 and 42x30 - no game at the turn cap for lack of a decision; any cap that remains read and explained (batch 3 and the T6g batches below) |
| T6g ✅ BUILT | The user's ruling on 5749 (2026-11-21): a side down to its fighters - no flight goes three turns without a shot, and none flies further off than it must |

**T6a as built (2026-11-21) - BUILT, uncommitted, not play-tested.** No flag, no migration, no new class.
* `AIView::rackBlock`: an exclusive rack is "exclusive weapon"; a firing link with an angular spread is still "linked firing"; a
  same-target link passes. `BallisticDecider`: each launcher carries `linkKey` (the craft, or the ship, and the group's name); a
  salvo leaves out a launcher whose link is already committed to another unit, and booking a launch commits it. No order of this
  turn ever stands on a twin when the bot plans: `TacGamedata::hideSystemFireOrders` strips EVERY current-turn launch from Initial
  Orders, the owner's own included (so the page's "a sibling's standing order binds the rest" never arises for the bot).
* `AIConfig::FIRE_MIN_CHANCE_PARTING` is a table by loading time (2 => 20, 3 => 34); `FireDecider::minimumChance` reads it, and the
  floor holdSlow stamps on a parting shot now carries the slow-pulse premium too (it did not).
* `AIConfig::FIRE_INTERCEPTOR_MIN_CHANCE` 15; `FIRE_RESERVE_PER_BALLISTIC` gone. `FireDecider::holdBack`: candidates are the guns
  with an intercept rating and no shot of 15%+; `interceptableIncoming` lists the orders that could reach the unit (throwaway
  orders, one a gun; the real ballistic orders); the unit's idle interceptors cover what they legally can first (`cover`, the
  server's `Firing::isLegalIntercept`), then candidates, best rating first, each only for an order it can stop.
* Every level, `legacy` included: these are corrections, not skills.
* **Gates, 2026-11-21:** fire pair (EA + raiders, 179 hulls, 716 situations a run) at Easy, Normal and Hard, phases 3 and 1: **0
  differences in all six** - in Initial Orders 13 (Normal, Hard) and 28 (Easy) launches on a firing link, each committed with its
  twin through the page; the oracle now fails a planned launch the page will not make once the launches before it are in
  (`fireOracle.js commitLaunches`), and the PHP half checks every link keeps one target. NEW `tests/ai/fireRulesHarness.php`
  (DB-free): A twin racks (4774's board at every level; 24 random two-target boards; the Vree turret still left alone), B the
  parting floor (the table for every Omega / Va'Kar / Tethys gun; Heavy Lasers drawing away fire at 34-39% and never under 34;
  closing they wait for 40), C the reserve (lasers alone: nothing held; the Interceptors facing the fire cover first; no
  Interceptors: a beam for each order; reloading guns not counted; ballistics counted unless uninterceptable; a 15%+ shot always
  fired) - **296 checks**. Mutation pass **8 of 8 caught** (after one fix to the harness: it had split its boards by the very
  constant the mutant changed - it now uses the user's 15). A mutant BallisticDecider ignoring the link is caught by both fire-pair
  halves. `levelsHarness` 236, `hazardsHarness` 14,758: green. `manoeuvreHarness` 522 passed / **2 failed - the same 2 on HEAD's AI
  code** (section D: with its port thrusters dead a Hard ship now begins an extended turn, T5f-5, where the check expects the
  thruster-swap roll) - pre-existing, not this stage's; reported.
* Self-play 5749 (above): no refused submission, no defect; the Thunderbolts' launches went through `Phase::process`.
  `selfPlayHarness` now buys a flight with a missile magazine two Basic missiles a craft (AMMO_FB, as players do) - its
  magazine is empty otherwise.

**T6b as built (2026-11-21) - SURRENDER. BUILT, uncommitted, not play-tested.** No flag, no migration (the count rides
`tac_ai_state`'s JSON).
* NEW `source/server/ai/Posture.php`. `assess($view)` runs in Initial Orders on the bot's masked view: each own unit (mines
  left out either way) is asked `canFight` - not destroyed or disabled, at least one usable gun, and some visible enemy it could
  damage this turn with both as close as they could come (`PowerDecider::nearer` over `maxMove` both ways +
  `POWER_REACH_SLACK`), every EW point of its ship locked on: `AIView::estimate > 0`, or a launcher with fire control for the
  target's size and the target in launch range. `usableGuns`: not destroyed (nor its craft), not forced off by the game (one the
  bot switches off itself still counts), with ammunition or a plain round in its magazine, with fire control for some size of
  unit (a pure interceptor is not a gun); no jump engine, ram, hex-targeted or Support weapon. A gun counts whatever its loading -
  it fights again soon. Fleet value both ways (`AIView::value`).
* `Posture::surrender`: turn >= `SURRENDER_EARLIEST_TURN` 3, an enemy in sight, no unit can fight, behind on value, and the
  `SURRENDER_TURNS_UNABLE` 2nd turn running. `Posture::run`: a turn with no enemy in sight neither counts nor breaks the run (a
  cloaked enemy is not a beaten one).
* `TacticalAI::play` (phase 1) reads the run from `tac_ai_state['posture']` (`turn`, `run` - only if it is last turn's) and keeps
  the new one once the submission is in. A surrender submits every own unit's bare entry with status 'SURRENDERED' through the
  ordinary `Manager::submitShipsForPlayer` path; the result is 'surrendered', the log line `SURRENDERED: no unit could damage an
  enemy for N turns, ...`. Every Initial Orders line now ends `N of M units can damage an enemy; fleet value X against Y`.
* Gates: NEW `tests/ai/withdrawalHarness.php` (DB-free) A - can it fight (an armed Omega in reach can; every gun gone cannot;
  400 hexes off cannot; a reloading Heavy Laser counts; forced off by a critical does not, switched off by the bot does;
  disabled cannot; a Thunderbolt flight with its guns gone can only with missiles and a target in launch range), B - the rule
  (the second turn, behind, turn 3+; no enemy in sight neither counts nor breaks), C - the side (mines left out).
  `selfPlayHarness` accepts a CPU surrender as a finished game ("ended by a CPU surrender on turn N").

**T6c as built (2026-11-21) - JUMPING OUT. BUILT, uncommitted, not play-tested.** No flag, no migration.
* NEW `source/server/ai/deciders/JumpDecider.php`. `plan($view, $hazards, &$power)` in Initial Orders, for each own ship (not
  a flight, mine or terrain; not disabled):
  * `engine()`: a vortex drive (not legacy, not a gate), not destroyed, not forced off (switched off only by the bot's own
    deficit rows is allowed - see keepOn below), holding no jump point, charged (`getVortexRechargeLoad >= getLoadingTime` - the
    page's isLoaded reads the same two numbers), no more than `JUMP_MAX_DRIVE_DAMAGE` 0.5 of its boxes lost.
  * `reason()`: 'no usable weapon' (`Posture::usableGuns`), 'cannot get back into the fight' (`!Posture::canFight`), or
    'expected to be destroyed' - the enemies' LOADED plain guns, each enemy turned to the facing that brings most to bear where
    it stands, half its EW locked on and half the ship's own as DEW, are expected to deal at least what is left of the ship
    (`AIView::healthLeft`: primary + the section facing the worst of them); the ship is already "so damaged" - its PRIMARY
    structure down to `JUMP_DAMAGED_SHARE` 0.5 of its boxes (asked first; see the self-play batches below); and the ship's own
    loaded guns (best facing) would remove less than it is worth (`ShipDecider::removed`). (A full lock and no DEW called a fresh
    Omega facing one other doomed.)
  * The site: `ShipDecider::rows` with NEW `ShipDecider::$planFilter` (a filter on the candidates; null = as before) - the plans
    of this turn that END within the drive's range of where the ship stands, with an entry direction
    (`ShipMover::entryDirectionOf` over its earlier rows and the plan's), on a hex `legalSite()` allows with the mouth facing back
    along that entry: the drive's range, line of sight (`Mathlib::isLoSBlocked` unless `ignoresLoS`) and the server's own
    `Firing::getVortexDeclarationBlock` (no terrain footprint, no Enormous unit, charged, one a ship...) asked on a throwaway
    order through a bound closure. The best of them gives the hex and the mouth; log `NAME: JUMPING OUT (reason) - jump point
    at q,r, mouth facing f`. ShipDecider's log line and timings are put back after the question.
* The declaration (`JumpDecider::declaration` -> NEW `AIOrderBuilder::jumpPoint`) is the order
  `weaponManager.createJumpPointOrder` builds - ballistic, target -1, mode = facing + 1, x / y the hex, defaultShots,
  damageclass 'jumppoint' - posted on the engine's own system row (`TacticalAI::initialOrders`).
* POWER: the deficit rules shed a drive first (T3), so a leaving ship in deficit keeps it on: NEW `PowerDecider::keepOn`
  takes the drive's off row back and switches off the next `sheddable()` systems until `balance() >= 0`, returning an undo
  (used when no site is found); none -> "would jump out (...), but cannot spare the power". `JumpDecider::plan` runs BEFORE
  `PowerDecider::boost` - balance() counts no boosts; the other order failed 15 Hard orders-pair boards ("still -N power").
* MOVEMENT (`MoveDecider`, ships): the turn it is declared (`pending()`: the engine's `activeVortexId`, the vortex not open yet)
  `approachRows` flies the best plan ending in the vortex hex through the mouth (none -> "cannot reach its jump point through
  the mouth any more - the jump is off", and the ship decides as usual); the next turn, the vortex open, `jumpOutRows` is the
  jump-out first thing (an owed extended turn completed before it, as the page insists). A ship with a reason and a usable drive
  but no jump point (too fast to end within range) `brakeRows`: its slowest plans, so it can declare next turn - unless it
  cannot slow down.
* `ShipMover::JUMP_OUT` ('jumpout') mirrors the page's Jump Out button: `canJumpOut` = the vortex given (`vortexForJumpOut`),
  the ship standing in it, entered through the mouth (`entryDirectionOf(rows) === (facing + 3) % 6` - judged on the step that
  put it there, earlier turns included), extended turns allowing, the vortex open and not closed. After it nothing more (no
  speed change, turn, jink, pivot or roll; remaining movement 0). Offered by `options` / `clientOffers`, NOT searched
  (`searchOptions`) - only `jumpOutRows` applies it.
* Gates:
  * withdrawalHarness D: D1 the drive (40% lost usable, 60% not, destroyed, recharging); D2 the reasons (and "so damaged": an
    Omega sound, 40% lost, or with its forward section shot away and its primary whole fights on, 60% lost leaves - four fresh
    Omegas' fire lethal on paper on every board; a mutant without the gate fails all three); D3 a disarmed Omega
    declares at a legal site, the order exactly the page's, kept by `Firing::validateFireOrders`; D4 the approach through the
    mouth; D5 the jump-out at all six headings, kept by `Movement::validateJumpOutSubmission`; D6 entered from the wrong side:
    none; D7 too fast to declare: it brakes; D8 a deficit: the drive kept on, something else off, no off row for the drive.
  * ShipMover pair (157 hulls, 5 parts): 10,224 situations, 106,818 plans, 794,823 steps through movement.js, 2,005,483 server
    checks; 582 jump-out plans replayed and 1,456 jump-outs clicked through the page's Jump Out button: **0 differences**;
    mutants 4 of 4 caught by both halves.
  * Fire pair, NEW jump-point boards (`fireHarness.php --phase=1`, built after the Fire boards so their random sequence is
    unchanged; `fireOracle.js replayVortex`): a board per hull with a vortex drive - two pieces of terrain near it (rotated
    multi-hex asteroids, a moon's disc, dust clouds and meteor swarms, which are terrain but not Enormous), now and then a rock,
    the drive now and then switched off, shot away, recharging or hit - and EVERY hex within the drive's range + 1 selected,
    clicked (`weaponManager.targetHex`) and confirmed in the facing dialog (`VortexFacingRequested` -> onConfirm). EA + raiders,
    each level: 220 boards, 20,020 hexes, **6,779 declarations the page builds field for field as the bot does, all kept by
    `Firing::validateFireOrders`**; 5,506 hexes both refuse (range 4,050, line of sight 981, terrain 473, an Enormous unit 2);
    none the server alone refuses. Every faction but yoluReevaluated, one round: 533 boards, 62,231 hexes, 24,485 identical
    declarations, 0 differences on the jump points.
  * Orders pair Hard / Normal / Easy: 0 differences.

**T6e as built (2026-11-21) - HARD SHEDS A DRIVE PAST HALF ITS BOXES. BUILT, uncommitted, not play-tested.**
* `PowerDecider::idle` (what pays for Hard's boosts, least useful first): a jump engine past `JUMP_MAX_DRIVE_DAMAGE` that draws
  power, is on, not boosted, not power-locked and has no order this turn goes FIRST, ahead of the idle guns. A boost still takes
  idle systems only when the budget falls short. A drive the bot would jump with is never past half (`JumpDecider::engine`), so
  the two rules cannot meet. The Trek warp nacelles (JumpEngine subclasses whose rating feeds impulse thrust) all draw 0 power,
  so they are never shed.
* Gates: withdrawalHarness E (E1 Hard, the drive 60% lost: a boost bought and the drive switched off first - without the rule
  this Omega could buy none; E2 40% lost: it stays on; E3 Normal: no boost, nothing off); mutants 2 of 2 caught
  (the drive never offered; offered at any damage). `ordersHarness` now hits the drive 60% on its untouched boards (round % 6 ==
  0 - no draw on the random sequence) and checks every drive switched off outside a deficit is past half and pays for a boost: at
  Hard 45 drives switched off for a boost (168 in all, the rest the deficit rules'), each committed through power.js - **0
  differences** at Hard, Normal and Easy.

**Re-run after T6b-T6e and the "so damaged" gate (2026-11-21):** withdrawalHarness **85** (A-E), fireRulesHarness 296,
levelsHarness 236, hazardsHarness 14,758 - green; manoeuvreHarness 522 / the 2 pre-existing section-D failures (T6a above).
Fire pair 6 of 6 and orders pair 3 of 3 at 0 differences. Replay check **241 passed, 0 failed** (the corpus now holds 241 games,
kept self-play games 4810, 4811, 4845, 5641, 5743 and 5748 among them - never delete those).

**Self-play, T6 batch 1 (2026-11-21, BEFORE the "so damaged" gate):** EA mirror with Thunderbolts (Omega, Hyperion, Tethys,
Aurora, Thunderbolt a side), `--budget=0`, 2 games each of 24x16 Normal, 24x16 Hard, 42x30 Normal and 42x30 Hard v Normal: **8 of 8
PASS, 8 of 8 FINISHED (10-48 turns), none at the turn cap, no surrender, no defect.** But about 19 jump points were declared and
15 ships jumped out - nearly every game, several on turn 3 or 4. Replayed (scratch `g_jump.php`): game 5761's Hyperion at 88%
of its value (primary 54/54) left on turn 3 because the five enemy units' expected fire - every gun at that one ship - was 142
against 96 left of its primary and forward section; its Omega (primary 56/60, forward section 17/60) the same. Not the user's
"quite rare ... so damaged they can easily be destroyed": a `JUMP_DAMAGED_SHARE` gate was added (the user's own words), its
value 0.5 for the user to confirm.

**T6 batch 2 (the gate asked of the primary OR primary + the section facing the enemy):** 8 of 8 PASS; 7 FINISHED (17-55 turns),
1 ended at the turn cap (5764, 24x16 Normal: the loser down to 6% of its value). Still ~18 declared, 12 jumped out, one on turn 2.
Replayed: three of four had a WHOLE or nearly whole primary (78-100%) and had lost the one section facing the enemy (5771's
Hyperion: primary 54/54, starboard 0/60) - a ship that can turn a fresh side, not one "so damaged". The gate now asks the
PRIMARY alone (the boxes whose loss destroys a ship): of the four replayed, only the Omega at primary 60% / value 48% came near,
and it fights on too.

**T6 batch 3 (the gate as built - the primary alone):** **8 of 8 PASS, 7 FINISHED (18-34 turns), no defect.** 7 jump points
declared (4 "expected to be destroyed", 2 "cannot get back into the fight", from turn 5 on), **2 ships jumped out** - against 15
and 12 in batches 1 and 2: rare, as the user asked. One game at the turn cap (5775, 42x30 Hard v Normal) was NOT a stalemate:
the Hard side went on fighting and losing units (4 units, value 870 on turn 30; 1 unit, 205 on turn 46), and its last unit -
an Omega that wanted to leave from turn 38 but could reach no jump point - read "0 of 1 units can damage an enemy" on turn 46,
the first of the two turns the surrender rule asks: the cap came one turn before the bot's own surrender. (All three batches'
games deleted; the harness's cap is checked between driver runs, so a game may overrun turn 40 before it is applied.)

**Open (T6b-T6e):**
* ~~A unit that can still fight but never comes in blocks the surrender.~~ **Ruled 2026-11-21 and built as T6g** (below).
* A ship that should leave but can neither slow down nor turn (5749's Omega: forward thrusters gone, 190 hexes off) can
  neither declare nor brake: it flies on, counted as unable to fight (it surrenders with its side).
* "Expected to be destroyed" measures the enemy where it stands (turned, not closing) - errs toward fighting on, as asked.
* A jump point is declared only where a plan of this turn ENDS, within range of where the ship stands at Initial Orders. The
  Jump Accelerator doubles the failure chance on opening; the 50% bar is the user's on DAMAGE, and is kept as ruled.
* Fire pair over every faction (outside the EA + raiders gate): 11 failures on exotic hulls, the same 11 on HEAD's AI code -
  9 the oracle's sandbox (`playerManager` not defined on the Walkers of Sigma hulls, a null `active` on BlackDagger), 2 a Qom
  Yomin Aux Minesweeper Refit weapon id AIView and the page disagree on. Pre-existing; not this stage's.
* Ship data (not AI code): `yoluReevaluated/lacannaReev.php` declares `class LacannReeva` while its `phpclass` says
  `LacannaReev`, so ShipPayload cannot rebuild it from a posted order (the every-faction fire run stops on it).

**T6g - A SIDE DOWN TO ITS FIGHTERS. The user's ruling, 2026-11-21:** "Yes let's tone down AI fighters that stay too far away when
they are the only unit left for the AI player, as this will frustrate players. E.g. can't go three turns without firing a shot, and
maybe don't run as far way from the enemy."

**T6g as built (2026-11-21) - BUILT, uncommitted, not play-tested.** No flag, no migration (the count rides `tac_ai_state`).
* "The only unit left" = `Posture::lastStand($view)`: every unit of the bot that can still damage an enemy (`Posture::canFight`, the
  surrender rule's test) is a fighter flight, and there is one. A ship that can no longer fight does not count against it - 5749's
  losing side still had its Omega on the board, useless 190 hexes off the map. Once a view (a `WeakMap`).
* THREE TURNS WITHOUT A SHOT (`AIConfig::FLIGHT_MAX_IDLE_TURNS` 3): `TacticalAI::play` keeps the turn each flight on the board last
  fired or launched (`tac_ai_state['shots']`; the load brings back this turn's fire orders only) - `Posture::shots` (a flight first
  seen counts from the turn before; one gone is forgotten), updated from each phase's orders (`Posture::firing`: any fire order on
  the unit or one of its fighters) - and before the deciders run `Posture::$mustShoot` = the flights whose last shot is 3 turns back
  (turns T-2 and T-1 without one: turn T must not be a third).
* MOVEMENT (`FlightDecider`), while the side is down to its fighters - each rule ranked above every other term (`self::MUST`, 10^6):
  * "DON'T RUN AS FAR AWAY" - THE LEASH: the flight ends within `FLIGHT_LEASH` 10 hexes of the nearest target it closes on (where it
    will be at the Fire phase) whenever it can, else as near as it gets. Each hex short of its guns' reach and each point of speed
    past the CHASE SPEED (the target's speed + `FLIGHT_CHASE_MARGIN` 6) also cost `FLIGHT_CLOSING_PER_HEX`, at every distance.
  * A flight that MUST fire goes to the best place (usual terms) with a SOLID shot - `FLIGHT_MUST_CHANCE` 20% at the Fire phase as
    foreseen, its jinking counted; else any shot FireDecider takes (its floor, the 20% edge floor `AIView::onEdge`); else as near
    as it can get without outrunning the chase. NEW `AIView::chanceOf` (the hit table's weighted chance from where the unit
    stands; `estimate()` and it share `hitAnswers()`, `estimate()` unchanged) - `AIView::shot` cannot answer it, being cached by
    unit, gun and target, not position.
  * Why each piece (the T6g self-play batches below): plain "a shot over the 5% floor" had forced flights jink their chances down to
    the floor and lose them to enemies moving after them; plain "as near as it can get" ran flights up to speed 16-17 after a
    Hyperion doing 2, overshooting it far off the map; and a cost of 2 points a hex let the flights, between forced turns, fly 28-42
    hexes off a lone Hyperion to be safe from its guns.
* FIRE (`FireDecider`): a flight that must fire keeps no gun back to intercept (`holdBack` skipped) and fires every gun it has a
  shot for at the target it would do most to - into the interceptors too, which the greedy pick declines (a shot they swallow adds
  nothing). Logged: Movement "N MUST FIRE this turn (no shot for 2 turns, the side down to its fighters)", Fire "N fired by flights
  that must fire".
* Gates: withdrawalHarness F (DB-free) - F1 the last stand (flights alone yes; a ship that can fight no; a disarmed one doesn't
  count; no flight no), F2 the count (first seen; the third turn; reset by a shot; a new flight; one gone), F3 what fired (a
  fighter's order marks its flight), F4a made to fire, 2 Thunderbolts or 6 Auroras against 3 Omegas at Normal and Hard, moved or yet
  to move, it ends with a solid shot (20%+, jinking counted, at the Fire phase); F4b out of reach this turn, it ends as near as any
  plan can take it without outrunning the chase; F4c a ship of the bot still fighting: not made to; F4d left to itself it ends
  within the leash; F5a 10% shots into the Omega's Interceptors: left to itself none, made to fire all 6; F5b a torpedo coming: left
  to itself its guns are kept back, made to fire none is - **111 checks**. Mutants: 3 of 6 caught (FireDecider's forced shots, the
  kept-back guns, the count off by one); FlightDecider's must-fire term, its edge floor and the leash survive - on these small
  boards a lone flight left to itself already closes and shoots (30-75%, jinking counted), so those terms change nothing there.
  **On the real games** (scratch `g_flight.php GAME BOT`): 5749 (turn 41) - the losing side reads as down to its fighters; its
  Thunderbolts left to themselves stay at speed 0, jinking 6, 9 hexes off, best chance 0% (the hiding); made to fire they close to
  6 hexes, jinking 0, 25%. 5807 (a capped batch game, below) - left to themselves its flights flew 28-42 hexes off the Hyperion;
  with the leash 10-14. levels 236, hazards 14,758, fireRules 296, manoeuvre (the 2 known), fire pair 6 of 6 at 0 differences,
  orders pair 3 of 3 at 0 differences (before the flight terms, which it does not reach).
* **Self-play, a side down to its fighters from turn 1** (Omega + Hyperion against 2 Thunderbolts + 2 Aurora flights; 24x16 and
  42x30, Normal and Hard; `--budget=0`), each batch's games deleted after reading (scratch `g_idle.php`: each flight's longest run of
  turns without a shot, interception aside):

  | Batch | Rule as it stood | Games | Ended on their own | Turn cap |
  |---|---|---|---|---|
  | 1 | forced shot at the floor; closing cost | 8 | 8 | 0 |
  | 2 | + the solid shot (20%) | 8 | 6 | 2 (both 42x30 Hard: the off-map chase, 5795) |
  | 3 | + the chase speed | 12 | 10 | 2 (both 42x30 Hard: flights fleeing 28-42 hexes between forced turns, 5807) |
  | 4 | + the leash (as built) | 12 | 11 (one by the bot's own surrender) | 1 (5814, 42x30 Normal: NOT hiding - its last Aurora flight circled a Hyperion at speed 0, 4-8 hexes off, firing on turns 37 and 40 while the Hyperion fired every turn; both sides down to 10-18%) |

  The longest runs without a shot stay noisy (batch 4: 16 of 48 flights never more than 2 turns, 12 three, 20 four or more - most
  long runs a flight out of reach, chasing, or crippled and destroyed before it got its shot): **no flight ever hides as 5749's
  did, but "never three turns" is not a guarantee** - the enemy moving after a flight, or a flight out of reach, can take its shot
  away. Batch 1's rule had no cap in 8 games; its flights hid between forced turns as far as they liked - the leash is the user's
  "don't run as far away".

### 10.9 Stage T7 - specials (started 2026-11-21)

Begun with T6 marked complete. **The user's request (2026-11-21):**
1. "Hide Factions not cleared for CPU use in gamelobby Faction Picker when the player is manually selecting ships for CPU to use.
   Also, prevent CPU auto-choosing from uncleared factions when it auto-picks e.g. just show EA and Raiders atm and add more as we
   tackle Stage T7 in time. Hide any ships that are currently listed in unsupportedReason()."
2. "To make sure that EA interceptors systems can only be fired offensively by CPU players when boosted (their fire control against
   everything is null until then, preventing players from targeting a shot in the client). It's important that the AI follows this
   rule too. We can make it so only a Hard opponent boosts some Interceptors when facing a lot of enemy fighters, Easy and Normal just
   skip this mechanic."

**User rulings, 2026-11-21 (asked):**
* SAVED FLEETS (the gap in 1 - the saved-fleet list carries no faction): refuse such units on load AND on Ready - the readiness list
  is a server rule as well as a picker filter.
* WHEN HARD BOOSTS: the enemy is FIGHTER-HEAVY - EW_FIGHTER_HEAVY (a third) of its visible value in flights, the measure the CCEW rule
  uses - and some of its flights can reach the ship this turn.
* HOW MANY: one Interceptor kept on interception for each interceptable shot the enemy SHIPS could put on the ship this turn, the best
  ratings first; the rest boosted, but never more than half of the ship's Interceptors.

| | Deliverable |
|---|---|
| T7a ✅ BUILT | The readiness list in the lobby and on the server (section 7 "as built") |
| T7b ✅ BUILT | EA Interceptors: the fire-control rule pinned by harnesses; Hard's offensive boost against a fighter-heavy enemy |
| T7b-2 ✅ BUILT | The user, after 5824 (2026-11-21): "It is actually quite rare in practice to fire interceptors at fighters, but if we can prioritise Interceptors likely to have arcs for boost, that would be good" - boost only Interceptors with a flight in their arc |
| (next) | The handler registry (section 6) and further factions on the list (section 7) - not started |

**T7a as built (2026-11-21) - BUILT, uncommitted, not play-tested.** No new flag (all behind `cpuOpponent`), no migration.
* Server: `AIConfig::READY_FACTIONS` (Earth Alliance, Raiders) and `AIConfig::cpuUnitBlock($unit)` - "X is not a CPU faction yet", or
  `ShipMover::unsupportedHullReason` (extracted from `unsupportedReason`, which now asks it first: a remote-controlled flight, terrain,
  a base, an OSAT, a mine, a Mindrider drive). `Manager::addCpuSlots` sends `cpuFactions` beside `cpuSlots`, under the same gate
  (feature reachable, LOBBY, a CPU seat) - every other payload is unchanged (P6). `Manager::submitFleetForCpuSlot` refuses a fleet with
  any unit `cpuUnitBlock` names, each named: "The CPU player cannot use Babylon4 2 (a base), Gquan 3 (Narn Regime is not a CPU faction
  yet). Remove them from its fleet first."
* Lobby (`gamelobby.js`, `gamelobby.php`): `parseServerData` copies `cpuFactions` by name; `cpuSlotSelected`, `cpuFactionReady`,
  `cpuHullBlock` (⚠️ MIRROR of `unsupportedHullReason`) and `cpuUnitBlock` (the page's half of AIConfig's, same wording).
  `applyCpuSlotFilters` runs from `selectSlot` - which every poll repeats, so it acts only when the choice moves to or from a CPU slot:
  * the Faction Picker (`filterFactionList`) lists only ready factions while a CPU slot is selected, with a one-line note
    (`#factionListCpu`); its randomiser (`rollFaction`) and Enter-to-pick draw from the rows it shows, so they cannot suggest another;
  * a Store left on a faction the CPU cannot play goes back to "No faction chosen" (`clearStoreFaction`);
  * each Store row carries `data-cpu` (`cpuHullBlock`) and `applyCustomShipFilter` hides the false ones; `applyStoreCategory` treats a
    category with nothing a CPU can fly (Immobile Structures, Mines) as absent, so its chip is disabled like a faction's empty one;
  * `doLoadFleet` leaves out each unit a CPU player cannot field, as it leaves out mines and late ISDs, and `showLeftOutNotice` lists
    each with why; a fleet with no unit left loads nothing.
* **Ruling (implementer, for the user to confirm):** the saved-fleet refusal is PER UNIT, not the whole fleet (the asked option read
  "the lobby won't load such a fleet") - per unit is how `doLoadFleet` has treated mines and the ISD since the user's 2026-09-25 request.
  Five lines in `doLoadFleet` turn it into a whole-fleet refusal.
* "Prevent CPU auto-choosing from uncleared factions" is the picker's randomiser: nothing on the server picks a faction before T8's
  `FleetComposer`, which must draw from `READY_FACTIONS` when it is built.
* Gates: `cpuLobbyHarness` 29 of 29 (new: `cpuFactions` sent in the lobby, not in play, not with the flag off; a Narn unit refused and
  named; a base and a Narn unit refused, both named; nothing bought); `flagOffHarness` all passed. The mirror pair - `gamelobby.js`'s
  real `cpuHullBlock` (node) against `unsupportedHullReason` (PHP) over every static blueprint of every faction: **2,902 agree, 0
  differ** (2,368 flyable; 388 OSATs, 106 bases, 8 mines, 24 terrain, 5 Mindrider drives, 3 remote-controlled flights hidden - a
  scratch script, not kept in tests/). The picker and Store were not browser-tested: the user's play-test.

**T7b as built (2026-11-21) - BUILT, uncommitted, not play-tested.**
* THE RULE: an Interceptor (`InterceptorMkI` and its family - Interceptor II, Prototype, Heavy Interceptor Battery) has fire control
  null against everything until boosted; boosted (free, one level) its own `setSystemDataWindow` gives it 6 / 8 / 10 against fighters
  and takes its intercept rating away - run by prepareForPlayer's beforeTurn sweep, so it is what the page is sent and what AIView
  reads. **The bot already obeyed it** (`AIView::shot` refuses on fire control, 'fc', as the page refuses the click); nothing changed in
  the Fire phase. Now PINNED: `fireRulesHarness` D - unboosted, no shot at a flight or a ship (refused on 'fc') and no FireDecider order,
  at every level, an Omega (Interceptor IIs) and a Hyperion (Interceptor Is) 1-3 hexes from an Aurora flight; boosted, fire control for
  fighters alone and rating 0, no shot at a ship, and it fires at the flight. And the fire pair: one Fire-phase board in four boosts the
  hull's Interceptors (no draw - the other boards unchanged); the PHP half fails any Interceptor order that is not boosted or not at a
  flight; the page agrees on 43 boosted shots a level.
* HARD'S BOOST: `PowerDecider::interceptors($view, $plan)`, after `boost()` and before EW (`TacticalAI::initialOrders`), skill
  `interceptorBoost` (Hard only); `EWDecider::lockable` / `fighterHeavy` extracted (one measure for CCEW and this), and
  `FireDecider::interceptable` public. Candidates: an `InterceptorMkI` the page's plus button takes (boostable, not switched off, under
  its maxBoostLevel), loaded, with a rating, fire control null now and some once boosted (asked of a CLONE's `setSystemDataWindow` - it
  also rebuilds damage arrays and the firing mode, which the live weapon must not have done twice). Boosted: min(half the candidates,
  candidates - ship shots), the lowest ratings (equal ratings: the highest ids), where SHIP SHOTS = the enemy ships' loaded guns that can
  be intercepted and would have a chance at the ship, and their loaded launchers in range, both as close as they could come (boost()'s
  reach) - and only when a boosted candidate would have a chance at some enemy flight from as close as both could come. Only on a ship
  `understands()` answers for (P4) - the scanner and engine rows `boost()` just bought do not count against it (`$ownBoosts`). Each
  boost: the row, and the boosted fire control and rating written on the system so EW sees the ship's anti-fighter guns as they will be.
  The Initial Orders line: "N Interceptors boosted to fire at fighters".
* Gates: `levelsHarness` N (28 checks; 264 in all, 0 failed): a fighter strike - Hard boosts 3 of 6, Easy and Normal none; the server's
  offensive mode on each boosted one, the rest unchanged; flights 120 hexes off - none; one flight beside a whole Omega - not
  fighter-heavy, none; an enemy Omega with K interceptable guns - min(3, 6 - K) for K = 0, 2, 4, 5, 6, 9 (its Heavy Lasers and unboosted
  Interceptors not counted); two lost - 2; five lost - none; direct calls isolating the reach and fighter-heavy tests. Mutation pass:
  PowerDecider 10 of 10 caught; AIView's fire-control refusal 2 of 2 caught by fireRules D, the flight one by both fire-pair halves too
  (the page makes 0 orders of an unboosted Interceptor's shot). Orders pair: a third of Hard's boards (troubles 2 and 3) face their
  flights alone (the warships taken off after every draw, four Aurora flights 3-6 hexes off) - **228 Interceptor boosts on 123 hulls
  clicked in through power.js, 0 differences**; Easy and Normal 0 boosts, 0 differences.
* Self-play, through the real server path (24x16, `--budget=0`, both games deleted after reading): **5823** - Hard Omega + Hyperion v
  Normal Omega + four Aurora flights (fighter-heavy): PASS, 11 turns, NO boost - the enemy Omega's 14 interceptable guns are always in
  reach on that board, more than the six Interceptors, so every one stays on defence (the ruling, working as asked). **5824** - the same
  Hard fleet v four Aurora flights alone: PASS, 20 turns, Hard 88% of its value left v 0%; 4-5 Interceptors boosted every turn while a
  flight lived (the Omega 3, the Hyperion 2; 79 boost rows, every one a single level), none once the flights were gone; **11 offensive
  Interceptor orders, every one boosted and at a flight** (5 hit, 40% needed on average); 12 interceptions, all by unboosted
  Interceptors; no unboosted Interceptor ever fired offensively; no AI defect.
* Found and fixed in `ordersHarness` (a harness bug, not the game's): `unit()` never loaded a flight's guns (they sit in its craft), so
  Hard's `boost()` counted none of their shots and switched every Interceptor off as idle. In a real game the guns are loaded: `boost()`
  keeps the Interceptors for the fighters' shots, and `interceptors()` then boosts the spare half.

**T7b-2 as built (2026-11-21) - WHICH Interceptors. BUILT, uncommitted, not play-tested.** The user, after 5824: "It is actually quite
rare in practice to fire interceptors at fighters, but if we can prioritise Interceptors likely to have arcs for boost, that would be
good." `PowerDecider::interceptors` now asks which enemy flights a boosted Interceptor could reach (`flightsInReach`, was a yes/no) and
boosts ONLY Interceptors whose arc holds one of them - `AIView::onArc`, the ship at its facing of the start of the turn (the likeliest
it will have; arcs are not known before Movement) and the flight where it stands - those holding the most flights first, then the
lowest ratings (the best stay on interception), then the highest ids. **Ruling (implementer):** "prioritise" was built as "only those
with a flight in their arc, best first" - an Interceptor with none is never boosted, since that boost could only cost its interception
(cost if wrong: one line lets the rest fill the half after them). The count rule is unchanged.
* Gates: `levelsHarness` N 274 (0 failed): every boosted Interceptor holds a flight and none left unboosted holds more (judged with the
  relative bearing and the weapon's own arc numbers - `getBearingOnUnit`, `Mathlib::isInArc` - not the decider's onArc); flights astern
  (never a forward Interceptor), off either beam, split two ahead and one astern; the forward pair shot away with the flights just off
  the bow: one Interceptor holds them, one boosted. Mutants: arc filter dropped and coverage order dropped both caught; the rating
  tie-break reversed survives - equivalent on these boards (every Interceptor II is rated 4). Orders pair: 227 Interceptor boosts on 122
  hulls clicked in through power.js, 0 differences at all three levels.
* Self-play **5825** (the 5824 board again, deleted after reading): PASS, 17 turns, Hard 89% v 0%; 71 boosted Interceptor-turns, **13
  offensive shots** (8 hit, 31% needed on average) - every one boosted and at a flight - against 5824's 79 and 11: ~18% of boosts
  become a shot (was ~14%). The volume barely moved, for a plain reason: any bearing lies in at least three of an Omega's six 180-degree
  Interceptor arcs (two of a Hyperion's four), so the half is usually filled with Interceptors that do face the fighters. Making the
  boost itself RARER (the user: "quite rare in practice") would need a stricter trigger - see Open.

**Open (T7):**
* The boost is decided in Initial Orders with arcs unknown (a facing changes in Movement) and on the best-case reach. T7b-2 boosts only
  Interceptors whose arc faces the flights now; still ~4-5 a turn against four flights (5825). If the boost should be RARER, the
  trigger is the place: flights within a few hexes at the start of the turn rather than "could reach", or at most one Interceptor per
  flight in arc - for the user to rule.
* How the bot sees an enemy player's boosted Interceptors depends on what the mask shows of enemy power this turn - not examined.
* The handler registry and further factions: moved to stage T9 (section 10.10).

### 10.10 Stage T9 - more factions (PLANNED 2026-11-22; the Major factions RELEASED 2026-11-23 - "T9 release" below)

**The user (2026-11-21):** "now that we have a method of restricting CPU factions, how do we go about expanding this to factions beyond
EA and Raiders? Campaign work is paused at the moment, so can you make a realistic plan to add more factions as a new Stage T9 (push
current Stage T9 to T10). The preferred order is Major Factions->League of Non-Aligned Worlds->Minor Factions->Ancients->Custom."

T9 takes over T7's "handlers faction by faction; readiness list grows" (T7 keeps the readiness mechanism and EA's Interceptors). It
does not depend on T8; T8's `FleetComposer` must compose only from `READY_FACTIONS`, and T8's decision-time review should be run on the
factions T9 has added by then.

**How a faction is added - the gate (the section 7 criteria made concrete).** A faction's string joins `READY_FACTIONS` only when:
1. **Survey.** `tests/ai/factionSurvey.php` (T9a) lists, for every hull of the faction the lobby offers: the weapons the bot leaves
   alone and why (AIView's own gates), the systems that are not plain hull, the hulls whose power PowerDecider does not model. Every item
   is either HANDLED (an enabler below) or RULED harmless here, by family. **The bar (D1, ruled by the user 2026-11-22):** the bot can use at least 85% of the
   faction's weapon mounts (boarding parties and weapons that fire by themselves not counted - EA stands at 95%, Raiders at 88%), and no
   hull class is left with no usable direct-fire weapon.
2. **The three oracle pairs at 0 differences over EVERY class of the faction** - by faction STRING, not by directory (factions span
   directories: Raiders five, Custom Ships nine, Civilians twenty-six): ShipMover (default and gravitic situations), fire (phases 3 and
   1, every level, with boards for each weapon family an enabler added), orders (every level). This is what finds a BLOCKING system
   the survey cannot see: run on Abbai and Brakiri (2026-11-22) it found their shields stop the page's commit.
3. **Self-play:** 20 games (section 7) - the faction against EA, at Normal and at Hard, on 24x16 and 42x30, `--budget=0`: every game
   PASS, no AI defect, none at the turn cap for lack of a decision; two of them read in replay.
4. **The user's play-test** - one game - then the one-line change to `READY_FACTIONS`, and the lobby offers it.

**Where every faction stands (survey of 2026-11-22 - scratch `faction_survey.php`, to become `tests/ai/factionSurvey.php` in T9a).**
Every class the lobby offers (not `variantOf` NONE), of all 92 factions, on a small board (the hull at the origin, an enemy Omega 8
hexes off), asked through AIView's own gates. "Left alone" = weapon mounts the bot never gives an order today, by AIView's reason:
split = split-shot weapons (`canSplitShots`: the Twin Array family), ammo = an ammunition count without a magazine, rounds = a magazine
whose rounds are all outside `BALLISTIC_ROUNDS` (⚠️ for fighter racks this is mostly the survey board's doing - a flight bought without
missiles carries none; players buy them), special hit = a page-side hit-chance formula AIView has not ported, hex = hex-targeted,
pre-fire = a Pre-Firing (phase 5) weapon, exclusive = a fighter weapon that excludes its craft's others, turret link = an angular-spread
firing link, sustained = overload / sustained fire, auto = fires by itself (the server does it - nothing to add), boarding = left alone
on purpose, as for EA. "Needs" = the enablers below.

| Major Factions | Hulls flown | Weapons usable | Left alone (mounts) | Systems / power | Needs |
|---|---|---|---|---|---|
| Earth Alliance ✅ | 67 | 901/951 (95%) | rounds 42 (fighter racks), boarding 6, split 2 | DiffuserTendril 2, EnergyDiffuser 2 | (E1 E3 E8 polish) |
| Raiders ✅ | 101 (9 gravitic) | 582/665 (88%) | split 45, special hit 18, boarding 8, exclusive 6, rounds 6 | TractorBeam 1 | (E1 E4 E9 polish) |
| Dilgar Imperium | 46 | 413/436 (95%) | ammo 7, boarding 6, rounds 6, split 4 | - | gate (E1 E2 E3 polish) |
| Centauri Republic (WotCR) | 60 | 565/586 (96%) | split 9, exclusive 6, ammo 6 | - | gate (E1 E2 E9 polish) |
| Earth Alliance (Early) | 35 | 271/301 (90%) | rounds 24 (fighter racks), ammo 6 | - | gate (E2 E3 polish) |
| Minbari Federation | 36 (36 gravitic) | 407/434 (94%) | special hit 11, pre-fire 10 (Gravity Net), boarding 6 | TractorBeam 11, EMShield 2, AdaptiveArmorController 2, SelfRepair 1 | E8 rulings (E4 E6 later) |
| Orieni Imperium | 56 | 598/657 (91%) | ammo 53 (gatlings, missile racks), rounds 6 | HkControlNodeOrieni 16 (HK flights are not flown) | E2, E8 ruling |
| Narn Regime | 57 | 332/470 (71%) | split 106 (Twin Arrays), hex 12 (Energy Mine), boarding 12, ammo 6, pre-fire 2 | - | **E1** (E5 E6 later) |
| Centauri Republic | 51 | 182/413 (44%) | split 201 (Twin Arrays 184), ammo 12, exclusive 12, boarding 6 | TractorBeam 2, ChameleonSensors 1, Stealth 1 | **E1**, E8 rulings |

| League of Non-Aligned Worlds | Hulls flown | Weapons usable | Left alone (mounts) | Systems / power | Needs |
|---|---|---|---|---|---|
| Torata Regency | 15 | 112/112 (100%) | - | - | gate only |
| Drazi Freehold (WotCR) | 27 | 143/143 (100%) | - | - | gate only |
| Abbai Matriarchate (WotCR) | 28 | 205/223 (92%) | rounds 18 (mine launchers - D4) | ShieldGenerator 27, GraviticShield 27 | **E8 (shields - BLOCKING)** |
| Brakiri Syndicracy | 51 (50 gravitic) | 329/362 (91%) | pre-fire 8, hex 7, boarding 6, split 6, exclusive 6 | ShieldGenerator 16, GraviticShield 16 | **E8 (shields - BLOCKING)** |
| Hurr Republic | 15 | 114/126 (90%) | rounds 6, ammo 6 | - | gate (E2 E3 polish) |
| Cascor Commonwealth | 14 | 123/145 (85%) | ammo 12, exclusive 6, hex 4 | - | gate (E2 E5 E9 polish) |
| Drazi Freehold | 30 | 189/243 (78%) | rounds 30 (fighter racks), split 10, boarding 8, sustained 6 | - | E1, E3 check |
| Abbai Matriarchate | 20 | 86/151 (57%) | split 59 (Quad Arrays 47) | ShieldGenerator 18, GraviticShield 18 | **E1**, E8 |
| Grome Autocracy | 11 | 89/153 (58%) | ammo 64 (their rail guns) | AntiquatedScanner, ConnectionStrut | **E2** |
| Gaim Intelligence | 47 | 178/361 (49%) | split 92, special hit 60 (Packet Torpedo), ammo 12, rounds 12 | Bulkhead | **E1 E4** |
| Hyach Gerontocracy | 19 (19 gravitic) | 126/176 (72%) | auto 38 (Interdictors - fine), hex 6, boarding 6 | HyachComputer 12, HyachSpecialists 12, Stealth 3 (2 stealth units) | **E8** (E5) |
| Ipsha Baronies | 15 (15 gravitic) | 209/225 (93%) | hex 16 (Spark Field) | Mag-Grav reactor 13 | **E7** (E5) |
| Pak'ma'ra Confederacy | 18 | 88/137 (64%) | hex 43 (Plasma Web) | Plasma Battery 14 | **E5 E7** |
| Kor-Lyan Kingdoms | 24 | 122/258 (47%) | rounds 102 (D-racks 62, mine launchers 10), hex 16, special targeting 8 | - | **E3 E5** |
| Vree Conglomerate | 22 (22 gravitic) | 36/167 (22%) | special hit 104 (antimatter), turret link 16, hex 5 | - | **E4 E10** (E5) |

| Minor Factions | Hulls flown | Weapons usable | Left alone (mounts) | Systems / power | Needs |
|---|---|---|---|---|---|
| Usuuth Coalition | 10 | 75/75 (100%) | - | - | gate only |
| Descari Committees | 21 | 195/201 (97%) | boarding 6 | - | gate only |
| Balosian Underdwellers | 20 | 153/159 (96%) | boarding 6 | - | gate only |
| Yolu Confederation | 15 (15 gravitic) | 183/195 (94%) | exclusive 6, boarding 6 | - | gate (E9 polish) |
| Markab Theocracy | 13 | 86/92 (93%) | boarding 6 | - | gate only |
| Minbari Protectorate | 18 (18 gravitic) | 185/203 (91%) | pre-fire 8, special hit 4 | TractorBeam 3 | E8 ruling |
| Small Races | 18 | 141/161 (88%) | ammo 14, rounds 6 | - | gate (E2 polish) |
| Llort | 19 | 119/139 (86%) | split 8, rounds 6 | - | gate (E1 polish) |
| Belt Alliance | 23 | 137/161 (85%) | ammo 12, rounds 12 | - | gate (E2 E3 polish) |
| Alacan Republic | 7 | 48/64 (75%) | ammo 16 | - | **E2** |
| Rogolon Dynasty | 8 | 69/93 (74%) | ammo 12, rounds 12 | - | **E2** E3 |
| Corillani Theocracy | 18 | 84/145 (58%) | split 55 | - | **E1** |
| Deneth Tribes | 10 | 23/58 (40%) | split 35 | - | **E1** |

| Ancients | Hulls flown | Weapons usable | Left alone (mounts) | Systems / power | Needs |
|---|---|---|---|---|---|
| Torvalus Speculators | 7 (7 gravitic) | 25/30 (83%) | pre-fire 4, hex 1 | ShadingField 6, SelfRepair 5 - **6 stealth units: the bot never fires one today** (firing reveals it) | E8 (stealth), E6 |
| Vorlon Empire | 14 (14 gravitic) | 39/56 (70%) | split 16 | power capacitor 12, AdaptiveArmorController 14, SelfRepair 12, EMShield 12, petals | E1 E7 E8 |
| The Triad | 14 (13 gravitic) | 49/88 (56%) | split 29, hex 6, special hit 4 | SelfRepair 11, StructureSelfRepair 6, CoopStructureSelfRepair 5 | E1 E8 (E4 E5) |
| Walkers of Sigma-957 | 7 (7 gravitic) | 14/41 (34%) | turret link 12, hex 9, split 6 | SelfRepair 6, Energy Draining Field / Net | E1 E5 E8 E10 |
| Kirishiac Lords | 7 (7 gravitic) | 20/65 (31%) | split 35, exclusive 6, pre-fire 4 | orbitals (dock and deploy), SelfRepair 6 | E1 E8 (E6) |
| Shadow Association | 13 | 10/40 (25%) | hex 15, split 15 | EnergyDiffuser 13, DiffuserTendril 12, ShadowPilot 12, SelfRepair 12 | E1 E5 E8 |
| Mindriders | 0 of 5 | - | - | the Mindrider drive - ShipMover does not fly it | never, unless ShipMover learns the drive |

**Custom Factions (44)** - one line each (usable weapons, needs): gate only - Terran Federation 100%, Escalation Wars Support Units 100%,
Escalation Wars Kastan Monarchy 97%, Nexus Polaren Confederacy (early) 97%, Nexus Sal-bez Coalition 97% / (early) 96%, Escalation Wars
Blood Sword Raiders 92%; near-ready - Barada Imperium 99% (E1), Great Crusade Orieni Imperium 98% (E2, E8), Escalation Wars Circasian
Empire 97% (E2), StarTrek (early) Federation 95% (E11), The System 95% (E1 E8), Ch'Lonas Cooperative 94% (E2 E11), Yolu Confederation
(reevaluated) 94% (E9), StarTrek (TOS) Federation 93% (E2), Nexus Support Units 91% (E2 E5), BSG Colonials 90% (E2 E3), Escalation Wars
Sshel'ath Alliance 90% (E2 E4 E5), Nexus Brixadii Clans 89-90% (E2), Nexus Craytan Union 88-90% (E2), Escalation Wars Chouka Raider 86%
(E3), StarTrek Klingon 86% (E4 E6), 12 Colonies of Kobol 85% (E2 E9), Escalation Wars Chouka Theocracy 85% (E2 E3 E5 E6), StarWars 85%
(E2 E9), Star Wars Clone Wars 83% (E2 E8), Nexus Makar Federation 81-83% (E4 E5), Cascor Commonwealth (reevaluated) 82% (E2 E5 E9),
Custom Ships 80% (E1 E2 E5 E8), Drakh 100% but absorption shields and raider controllers (E8); far - Nexus Dalithorn 69-77%, Nexus
Velrax 70-77%, Trek Playtest 77%, StarTrek (early) Suliban 75%, Star Trek (Kelly) 73%, What If 62%, BSG Cylons 58%, Thirdspace 58% (E7),
House Valheru 46%.

**Other Factions** (not in the user's order - D5): Civilians 84% over 26 directories (E1 E2 E3 E4 E5 E7 E8 - one or two hulls each),
Streib 88% (E8 tractor beams). Data quirks seen, not fixed: one hull in `civilians/` declares faction "EA" (not "Earth Alliance"); a few
orphan static files ("Escalation Warss ...", "ZStarTrek ...") still list factions.

**The enablers** - cross-faction work, each its own sub-stage with harness boards, the page as oracle and a mutation pass, as every stage
before. Ordered by what they unlock in the user's order:

| # | Enabler | Mounts left alone today (Major, League, Minor) | The work |
|---|---|---|---|
| E1 | **Split-shot weapons** - the Twin Array family | Centauri 201, Narn 106, Gaim 92, Abbai 59 (Quad Arrays), Corillani 55, Raiders 45 (**already released: today their Twin Arrays never fire**), Deneth 35, Drazi 10, Centauri (WotCR) 9, Llort 8, Brakiri 6 (Gravitic Lance), Dilgar 4 | Port the page's split-shot orders (weaponManager) into AIView / FireDecider - every shot at one target first, splitting between targets later; fire-pair boards for them. The single largest unlock |
| E2 | **Ammunition weapons** (a count, no magazine) | Grome 64 (their rail guns), Orieni 53 (gatlings, SO racks), Alacan 16, Small Races 14, Centauri / Cascor / Gaim / Belt / Rogolon 12, Dilgar 7, fighters' torpedo and missile launchers everywhere | Track the count as the page does, never fire past it; the page as oracle |
| E3 | **Magazine rounds outside `BALLISTIC_ROUNDS`** | Kor-Lyan 102 (D-racks 62, mine launchers 10); fighter racks elsewhere (mostly the survey's empty magazines - first CHECK with missiles bought) | Model each round's rules (interceptor, multiwarhead, mines...) or rule it left alone |
| E4 | **Special hit chance** (`specialHitChanceCalculation`) | Vree 104 (antimatter - their arsenal), Gaim 60 (Packet Torpedo), Raiders 18 (Antiproton Defender), Minbari 11, Minbari Protectorate 4 | Port each family's page formula into AIView - the server's own `calculateHitBase`, as always - and an oracle board per family. **The ANTIMATTER family and the Point Pulsar are done (M3-M5, 2026-11-23: `AIView::PORTED_SPECIAL`)** - the Vree's antimatter guns now pass the fire pair; their turret links (E10) are what is left. Gaim's Packet Torpedo and the rest still to do |
| E5 | **Hex-targeted weapons** | Pak'ma'ra 43 (Plasma Web), Ipsha 16 (Spark Field), Kor-Lyan 16 (Proximity Laser), Narn 12 (Energy Mine), Brakiri 7 (Gravitic Mine), Hyach 6, Vree 5, Cascor 4 | A hex-choice per family; fire-pair hex boards on T6c's vortex pattern (every hex clicked through the page) |
| E6 | **A Pre-Firing decider** (phase 5 - "nothing in v1", 5.5) | Minbari 10 (Gravity Net), Brakiri 8 (Gravitic Shifter), Minbari Protectorate 8, Narn 2 (Pulsar Mine) | A new decider and a phase-5 oracle - or rule them left alone for v1 (D4) |
| E7 | **Power models** PowerDecider leaves alone | Pak'ma'ra 14 hulls (Plasma Battery), Ipsha 13 (Mag-Grav), Vorlons 12 (Power Capacitor) | Port each balance into PowerDecider; the orders oracle |
| E8 | **System handlers** - the section 6 registry, built with the first | shield generators and gravitic shields (Abbai 45 hulls, Brakiri 16) - **BLOCKING: the orders pair over abbai, abbai_old and brakiri (2026-11-22) fails ~400 of 756 situations at every level, every one listed on the page's commit gate "too many active shields" - a CPU fleet would commit a state no player can; a handler must choose the active shields** -, Hyach Computer and Specialists (12), tractor beams (Minbari 11, Streib 5), adaptive armour (Minbari 2, Vorlons 14), self-repair (Ancients), stealth units (Hyach 2, Torvalus 6), HK control nodes (Orieni 16), diffusers (EA 2, Shadows) | For each: a RULING "the default is fine" (most passive ones) or a small handler. Stealth needs a real decision (firing reveals the unit) |
| E9 | **Exclusive fighter weapons** | Centauri 12, Raiders / Brakiri / Cascor / Centauri (WotCR) / Yolu 6 | Choose one of a craft's exclusive weapons (weaponManager.checkConflictingFireOrder) |
| E10 | **Angular-spread turret links** | Vree 16, Walkers 12 | The page's linked-turret rule (getLinkedFiringBlock) - with E4, the Vree project |
| E11 | Odds and ends | Drazi Particle Cutter (sustained) 6, Kor-Lyan Limpet Bore (special targeting) 8, Gaim 1 special arc | One by one, when a faction needs them |

Not needed: boarding (Marines, grappling claws), weapons that fire by themselves (Hyach Interdictors: the server fires them), jammers and
other passive defences (the hit formula the bot asks already counts them), bases and OSATs (not offered for a CPU slot).

**The sub-stages, in the user's order** (quick releases first within a group - D2):

| | Deliverable |
|---|---|
| T9a | TOOLING and the two released factions finished: `tests/ai/factionSurvey.php` (from the scratch survey) and its report; a `--faction="Name"` filter on the ShipMover, fire, orders and self-play harnesses (classes by faction string); `selfPlayHarness --random-fleet=Faction:points` (a random pick of the faction's flyable hulls - `FleetComposer` stays T8); one gate script. **"Raiders" proven on all five of its directories - DONE 2026-11-22** (brakiri_, centauri_, drazi_, narn_raiders: 24 hulls - ShipMover 15,230 plans and 1,062 gravitic plans, fire phases 3 and 1 at every level (309 legal shots, 133 planned), orders at every level: 0 differences; the T7a lobby had offered them unproven); a self-play batch with those hulls still to run. EA's 42 fighter-rack rounds checked with missiles bought |
| T9b | MAJOR: Dilgar (95%), Centauri (WotCR) (96%), Earth Alliance (Early) (90%), Minbari Federation (94% - gravitic flown since T5d; rulings for tractor beams, adaptive armour, self-repair; Gravity Nets left alone until E6) - each through the gate. **Already at 0 differences in the fire and orders pairs** (2026-11-22, over dilgar, centauri_old, minbari, EA_old - 223 hulls: fire phase 3 at Normal and Hard and phase 1 at Normal, 4,365 legal shots and 1,998 planned; orders at Normal and Hard, 197 hulls, 1,182 situations, 28 Interceptor boosts at Hard): left for these four - the ShipMover pair, the rulings, the self-play batch and the play-test. Then **E1** (it also wakes the Raiders' Twin Arrays) -> Narn (71% -> ~93%, Energy Mines left alone until E5) and Centauri (44% -> ~93%). **E2** -> Orieni (91%; HK flights never flown) |
| T9c | LEAGUE: Torata (100%), Drazi (WotCR) (100%), Hurr (90%), Cascor (85%) - gate only; Brakiri (91%) and Abbai (WotCR) (92%) once E8's shield handler exists (their shields are BLOCKING - above); after E1 - Drazi (78%), Abbai (57%, shields too); after E2 - Grome (58%); E4 + E1 - Gaim (49%); E8 - Hyach; E7 (+E5) - Ipsha, Pak'ma'ra; E3 + E5 - Kor-Lyan; E4 + E10 - Vree (22%) last |
| T9d | MINOR: Usuuth (100%), Descari (97%), Balosian (96%), Yolu (94%), Markab (93%), Minbari Protectorate (91%), Small Races (88%), Llort (86%), Belt Alliance (85%) - gate only; Corillani (58%) and Deneth (40%) after E1; Alacan (75%) and Rogolon (74%) after E2 |
| T9e | ANCIENTS - each its own project (stealth for Torvalus; capacitor power, adaptive armour and petals for the Vorlons; orbitals for the Kirishiac; diffusers and phasing for the Shadows; the Walkers' everything); Mindriders never unless ShipMover learns their drive. Proposed: none in v1, then one at a time, the user choosing (D6) |
| T9f | CUSTOM - the user's choice of factions, the same gate; the near-ready ones above are quick once E1 / E2 exist |

**Effort - from the pace of T5-T7** (one session builds and proves one mechanism: harness boards, the page as oracle, mutants): T9a one
session; a gate-only faction about half a session (its gate runs in parallel in well under an hour; whatever it finds is fixed and
rerun); each enabler one or two sessions - E4 and E5 by family, so more. Roughly: Major 5-7 sessions (with E1, E2); League 8-12 (most of
E3-E8 land there); Minor 3-4; each Ancient 2-4; Custom per faction, mostly quick once E1 and E2 exist.

**Decisions - ALL RULED by the user 2026-11-22: "Yes to all these decision defaults."**

| # | Question | Ruling |
|---|---|---|
| D1 | The release bar | At least 85% of a faction's weapon mounts usable (boarding and auto-firing weapons not counted), and no hull class without a usable direct-fire weapon |
| D2 | Order within a group | Quick releases first (gate-only factions), then the ones an enabler unlocks, in the order the table lists |
| D3 | The variant factions (WotCR, Early, reevaluated) | Separate strings, each through its own gate |
| D4 | Weapon families a released faction may leave idle in v1 | Boarding; mines and Pre-Firing weapons until E5 / E6 exist |
| D5 | Civilians and Streib ("Other") | When the campaign needs them (neutrals) or the user asks |
| D6 | Ancients in v1 | None; after T9d, one at a time |

#### T9 started 2026-11-23 - the user's six notes, and what was built (uncommitted; ledger `.superpowers/sdd/TACTICAL_AI_PLAN/progress.md`)

**The user's notes (2026-11-23), verbatim in substance:** (1) Minbari: the Jammer prevents OEW locks on their ships, so they "use more OEW,
especially at the start of games"; (2) split weapons (Twin Arrays) "almost always fire all their shots at the same target ... using Firing
Mode 1": Easy and Normal ignore the split mode, Hard may split where it pays; where split IS the default mode (the Centauri Ballistic
Torpedo) Normal and Hard should consider splitting; (3) the Dargan's Chameleon Sensors: ignore for now (a disguise picked for a CPU fleet
will probably break by accident - fine); (4) area-effect weapons (Narn Energy Mines): aim with the movement predictions at a guessed hex
(usually a fighter flight's), and Normal/Hard deploy spread out when the enemy has them (extendable to other AoE weapons); (5) Dilgar and
EA (Early) straightforward; (6) Orieni Hunter-Killers deploy in hangars ($deploysInHangar) and ram by design ($designedToRam - so does the
Dilgar Delegor): they "can and should always Ram, and can ignore the normal conditions for making a ramming attack".

| Note | As built | Proof |
|---|---|---|
| (2) E1, Easy / Normal | `AIView::splitsIn` / `plainMode`: a weapon is left alone ('split shots') only when EVERY mode splits; `modes()` never offers a splitting mode - a Twin Array fires its Normal mode, one order a gun at one target, as the page's plain loop builds it | fireRulesHarness E1 (Primus, G'Quan, Secundus Heavy Array, Bimith Quad Array; GunLost); fire pair 0 diff |
| (2) E1, Hard | skill `splitShots` (Hard): `FireDecider::splitGuns` offers each gun of a split weapon to the greedy pick on its own (`AIView::splitShot` - one order, one shot, 'Sweeping', the page's doMultipleFireOrders); `mergeSplits` orders a weapon whose every gun went to one target in its plain mode, else one row per target in the split mode (notes 'Split', as the page). Hard re-plans with whole weapons and keeps the split plan only when `planWorth` is higher ("whether there's any advantage in damage potential") - on one harness board in eight the greedy's finer pieces came out worse | fireRulesHarness E2; the fire pair's new split boards (a hull with a split weapon meets a flight cut to 1-2 craft every other round) + `fireOracle.js replaySplits` clicks every split row through the page. Found by it: `FireDecider::entries` overwrote a split weapon's first row (fixed) |
| (2) default-split weapons | The Ballistic Torpedo BUILT at M8 (2026-11-23 - "M8 and M3-M5 as built" below); the slicers (2 EA) stay idle (M9); the Unreliable Twin Array fires whole only (its page prototype is plain Particle: a Split click makes no order - a page gap, not fixed) | - |
| (1) Minbari jammer | The pricing already knew the jammer (DEW is priced through the real hit formula): a Sharlin vs an Omega + Hyperion moving first goes 5 OEW at 22 hexes, 9 at 26, 12 of 12 from 30. Against enemies moving AFTER it the soft lock held it to 2 OEW / 10 DEW at every range. `EWDecider::jammed()` (the engine's Jammer ability against every non-mine enemy) lifts the soft caps beyond EW_CLOSE_RANGE (medium and close caps stay): 26 hexes 5 OEW, 30 -> 8, 36 -> 12 | levelsHarness P (mutant "always lift" caught by the disarmed-enemy board); orders pair 0 diff |
| (3) Chameleon | No code: the Dargan's ChameleonSensors is an ELINT scanner; the bot never toggles the disguise (an ELINT ship keeps its EW on DEW, as EA's do). The Rutarian's Stealth is the young-race fighter kind, not trueStealth | pairs over `centauri` |
| (4) AoE deploy spread | `AIConfig::AOE_WEAPONS` (EnergyMine, LightEnergyMine - instanceof, add a class to extend) + `AOE_SPACING` 3 + skill `aoeSpread` (Normal/Hard): `DeployDecider::aoeThreat` -> FleetDeployer option `spread` (line, screen and rank spacing 3 where the zone has room; 0 = the standard arithmetic, so Auto-Deploy is unchanged) | NEW `tests/ai/deploySpreadHarness.php` 66 checks (no two units within 2 hexes; every hex `inZone`; Easy / no AoE / Auto-Deploy unchanged; tight zone) |
| (4) Energy Mine aiming | NEW `AoEDecider` (Initial Orders, after the ballistics): each enemy's straight-on hex is a candidate, clear of the bot's own units (`AOE_OWN_MARGIN` 2, where they stand or straight on); worth = `AOE_LANDS` 0.75 x the guess's odds (`AOE_GUESS_FLIGHT` 0.35 / `AOE_GUESS_SHIP` 0.5) x what full damage does in the hex (every craft of a flight, armour off) and a third next to it; each loaded mine takes the best legal candidate left, no two at one hex. `AIView::hexShot` ports the page's targetHex tests (`onArcHex` - onArc's arc logic, at a hex). Every level aims. The level forecast is a Movement decision, so Initial Orders guesses straight on | fireRulesHarness F; fire pair phase 1 with mine boards + `fireOracle.js replayMines` (narn + narn_raiders: 5,516 hexes clicked through targetHex, 1,863 launches identical, 22 planned mines confirmed, 0 differences) |
| (6) designed to ram - Delegor | `Posture::rammer` returns a designedToRam ram first (every level, no Desperate rule needed); ShipDecider's ram targets add an enemy ship YET TO MOVE at its forecast hex (Easy: straight on); a designed rammer PURSUES (`AIConfig::RAM_PURSUIT` 10 x the closing term, `RAM_DANGER_SHARE` 0.25 of the danger) - before, out of reach it ran 12-20 hexes off | ramHarness D (D6: 10-18 hexes, moved and not, three levels) |
| (6) Hunter-Killers | NOT YET - still refused for a CPU fleet (`ShipMover::unsupportedHullReason`: remote-controlled flight); needs hangar deployment, launch, flight ram moves | - |
| (5) the gate | ShipMover pair: dilgar, EA_old, centauri_old, minbari 129,562 plans + Minbari gravitic 27,174; narn, centauri (+ their raider dirs) 94,424 - 0 differences. Fire pair (phases 3 and 1, every level) and orders pair (every level) over the six Major factions: 0 differences (the fire harness now adds planned RAMS as cases - the page found the bot ordering an UNLOADED ram: `Posture::rammer` now asks loaded / switched on, as the page does) | SELF-PLAY (2026-11-23, `--budget=0`, FleetComposer fleets at 2,500 pts against EA, Normal + Hard, 24x16 + 42x30): Dilgar, EA (Early), Centauri (WotCR), Minbari, Narn, Centauri 20/20 PASS each, plus kept G'Quan games (73 Energy Mines, 37 caught units) and Delegor games (3 rams, all hits) - 126 games, 0 AI defects. Left before release: the hull classes with no usable weapon (Raptor, Sitara, breaching pods; Orieni's Templars / Uplift), FleetComposer's 'S' filter (EA (Early) cannot be auto-composed), the user's play-test, then READY_FACTIONS - all but the play-test done in "T9 release" below (the user, 2026-11-23: add each faction once it is available) |

**Survey after E1 (2026-11-23):** Narn 71% -> 93%, Centauri 44% -> 92%, Raiders 88% -> 94%, Centauri (WotCR) 96% -> 98%; League / Minor
gains for free: Abbai 57% -> 96%, Corillani 58% -> 96%, Deneth 40% -> 100%, Llort 86% -> 91%, Drazi 78% -> 82%, Gaim 49% -> 75%.
Dilgar stays 95% (its 4 Point Pulsars are E4 - a special hit formula - not E1).

#### ⭐ MAJOR FACTIONS - AI STATUS AT A GLANCE (kept current; last updated 2026-11-23, after M8 and M3-M5)

**All nine are on `READY_FACTIONS`** - a CPU player can field them, the lobby offers them, and auto-compose builds their fleets.

**What every released faction gets (built, stages T1-T9):** deployment (formations, Auto-Deploy, spread out against enemy Energy Mines);
movement (ordinary and gravitic drives, overthrust, turn shortening, extended turns at Hard, pivots and rolls for damaged ships, terrain and
mine avoidance, enemy-move forecasting at Normal/Hard); power (deficits, Hard boosts); EW (OEW/DEW/CCEW, lock rules, jammer-aware); direct
fire with focus and interception modelled; ballistic launches (magazine racks, old-style racks, fighter racks with missiles aboard); Ballistic
Torpedoes (volleys priced as the server resolves them, backed by OEW - M8); antimatter weapons (damage priced by the roll - M3/M4) and Point
Pulsars (M5); fighter flights (strike / intercept / escort, jinking, coverage search); split-shot weapons (plain mode; Hard splits where it pays); limited-ammo
weapons (fire until empty); exclusive fighter guns (big gun at ships, paired guns at fighters); ramming (designed rammers always, the
Desperate rule otherwise); surrender and jump-out; Easy / Normal / Hard; auto-composed fleets (official and semi-custom units, never custom).

| Faction | Weapon mounts the AI fires | Special kit the AI uses | Units HIDDEN from CPU fleets | Idle (left alone) - still to build | Proof |
|---|---|---|---|---|---|
| Earth Alliance | 95% | Interceptors (Hard turns them offensive vs fighter-heavy fleets), magazine missile racks, Thunderbolt linked racks | Lamprey breaching pod (M1) | Molecular Slicer, 2 mounts (M9) | T2-T8 gates; released T7 |
| Earth Alliance (Early) | 92% | Raptor gatlings (E2); auto-compose fixed for its semi-custom jump-engine hulls | - | (fighter racks carry missiles only when bought - M12) | 20/20 + 4/4 self-play |
| Centauri Republic | 97% | Twin Arrays (E1), Sitara / Rutarian Ion Bolts (E9), Razarik torpedo racks when bought, Ballistic Torpedoes (M8: 1-3 a turn at one target, Hard two; 1-2 OEW behind them) | Scion breaching pod (M1) | split-arc Twin Array, 2 (M10); Unreliable Twin Array fires whole only (a page gap); Dargan Chameleon (ruled: ignore) | 20/20 + 4/4 + 2 targeted; M8 gates + 6 torpedo self-play games |
| Centauri Republic (WotCR) | 99% | Phalan Plasma Guns (E9), fighter missile racks when bought | - | - | 20/20 + 4/4 + 1 targeted |
| Dilgar Imperium | 97% | Delegor designed-to-ram, Bomb Racks (E2), fighter missile racks when bought, Point Pulsars (M5) | Jortavo breaching pod (M1) | (fighter racks carry missiles only when bought - M12) | 20/20 + 4/4; M5 gates + 1 Ochlavita game |
| Minbari Federation | 98% | gravitic hulls, Jammer-aware EW, EM Shield counted in damage estimates, Antimatter Converters (M4: damage by the roll, the pulse premium on its floors) | breaching pod (M1) | Gravity Net, 10 (M6 - ruled, not built); Adaptive Armour at defaults (M13); Tractor Beam (ruled: ignore) | 20/20 + 4/4; M4 gates + 1 Sharaal/Tigara game |
| Narn Regime | 96% | Twin Arrays (E1), Energy Mines (aimed with spacing), Tarza torpedo racks when bought | Tra'Kha breaching pods, armed and not (M1) | Pulsar Mine, 2 (M7) | 20/20 + 4/4 + 2 mine games |
| Orieni Imperium | 98% | gatlings on Templar / High Templar / Uplift (E2), Class-S / SO ship racks (E2) | Hunter-Killer flights (M2) - so its HK Control Nodes sit idle | (fighter racks only when bought - M12) | 20/20 + 2 targeted; ShipMover pair 56 hulls |
| Raiders | 99% | Twin Arrays (E1), Privateer Phalan Plasma Guns (E9), Antiproton Defenders (M3 - the Jia is back, Vastar / Vasy fire theirs) | breaching pod (M1) | Grappling Claw, 2 (M14) | T2-T8 gates; released T7; M3 gates + 1 Jia/Vastar/Vasy game |

("Weapon mounts the AI fires": the survey's share, hidden units left out, fighter racks counted EMPTY - with missiles bought they fire too.)

**Still to build for the Major factions, in a sensible order** (details and the user's rulings: "Missing systems" below):
1. ~~**M8 Ballistic Torpedo split**~~ - BUILT 2026-11-23 ("M8 and M3-M5 as built" below).
2. ~~**M4 / M5 / M3 special hit formulas**~~ - BUILT 2026-11-23, the Jia unhidden (same section).
3. **M6 Gravity Net + M7 Pulsar Mine** - a Pre-Firing decider (E6); both ruled (OEW-judged pull/push; Pulsar Mine against fighters).
4. **M2 Hunter-Killers** (Orieni) - hangar deployment, launch at once, rams at the largest ships first; unhides the HK flights.
5. **M1 Marines** - boarding; unhides 45 boarding craft across all factions.
6. **M13 Adaptive Armour** (Minbari), **M12 missiles for auto-composed fighters**, **M11 reloading at a carrier**, **M9 Molecular Slicer**, **M10 split-arc Twin Array**, **M14 Grappling Claw** - smaller items.
7. **The user's play-test** of the seven factions released in T9.

✅ Found at M8 and FIXED the same day: a **Hard extended-turn loop** on a ship that has lost or crippled side thrusters (begin, cancel for 'thrusters', begin again, every turn) - "M8 follow-ups" below; it was also manoeuvreHarness's two "known pre-existing" D failures.

#### T9 release (2026-11-23, second session) - the user's answers, what was built, and the Major factions released

**The user's answers (2026-11-23)** to the questions the first T9 session ended on - rulings, as all the user's notes:

| Question | The answer, in substance | Here |
|---|---|---|
| Breaching pods: hide them, or should the AI board? | "Hide for now, we will add Marines later. Start a list of missing systems for AI in Tactical AI Plan so we can tackle them one-by-one in future and unhide these units for ai use." | Hidden; the list is "Missing systems" below |
| Limited-ammo weapons (gatlings, fighter missile and torpedo racks) | "Just fire freely until empty for now"; ordering fighters with empty gatlings back to a carrier to reload would be good, but is very rare in games; ships with an ordnance supply could in theory reload fighters' missiles and torpedoes | E2 built; reloading listed |
| Exclusive fighter weapons (Sitara's Ion Bolt, Plasma Gun) | "these more powerful guns will be fired at ships, and the normal paired guns will be fired at fighters or when the larger gun is recharging" | E9 built |
| Ballistic Torpedo | 2-3 torpedoes at one or two ships, or 3-6 at a fighter flight (it targets separate fighters), backed by 1 or 2 OEW; Easy and Normal may simply fire 1-3 a turn at one target, Hard can consider splitting a bit more | Listed (not built) |
| Gravity Net: pull or push, S mode or P mode? | (the second round, same day) "Both are valid uses. Use OEW to judge this, e.g. bring an enemy ship into heavy weapon arcs of friendly ships that have a lock, or move enemy ship so it no longer has heavy weapon arcs on ships it has OEW lock on." | Listed (M6) - ruled, not built |
| Adaptive Armour | Pre-allocated points generally go to the most numerous weapon class in the enemy fleet (Ramming Attacks ignored); as the ship is hit, the rest go where they can, up to the maximum | Listed (defaults today) |
| Tractor Beam, EM Shield, Self-Repair | Ignore the Tractor Beam (it does nothing); an EM Shield "reduces potential hit chances and damage so possibly needs considered for gauging incoming damage from enemies"; Self-Repair is fine at its defaults | EM Shield built (damage); no code for the other two |
| Narn Pulsar Mine | "Generally to scare off fighters from getting too close, and charging into enemy fighter units when the G'Karith moves first, since it will then trigger when the fighters take their movement as they start in its range" | Listed |
| Hunter-Killers | "launch them immediately and they will usually target larger ships in enemy fleet due to their high damage potential, working their way down to HCV and MCV if no larger ships are available" | Listed; HK flights stay hidden |
| Energy Mine aiming | "Better to space out the mines a little within 1 or 2 hexes of each other to cover more space. Even the 10 damage from an indirect hit is lethal to fighters." | Built |
| (instruction) | "Once a faction is available (either because all it's ships are playable by ai or because we've hidden the ones that aren't) add it to READY_FACTIONS" | Seven factions added |

**As built:**

| Item | As built | Proof |
|---|---|---|
| Auto-compose and 'S' units | `FleetComposer` skipped every unit whose `unofficial` was set - the 102 SEMI-official ('S') ones included, which the lobby's checker counts as official (`lship.unofficial == true`); every jump-engine hull of Earth Alliance (Early) is 'S', so it could never be composed. Now only `=== true` (the static files hold false, true or "S") | fleetComposerHarness + fleetCheckOracle (below) |
| Hiding units (the missing systems) | `AIConfig::CPU_MISSING_SYSTEMS` (system name => why, and whether it hides every FLIGHT carrying it) + `missingSystemBlock`, inside `cpuUnitBlock`: a flight carrying a boarding system (Marines) - every breaching pod and assault shuttle, armed or not - and a unit with weapons, every one on the list (the Ramming Attack and the jump engine not counted - the Raiders' Jia). ⚠️ MIRROR: gamelobby.js `CPU_MISSING_SYSTEMS` / `cpuMissingSystemBlock` (the Store marks its rows when it builds them, possibly before a CPU slot exists, so the page keeps its own copy); `cpuBlueprintBlock` = hull kinds + missing systems, for the Store's `data-cpu` and `cpuUnitBlock`. Take a line out of BOTH when the AI learns a system and its units come back | Parity over every blueprint (2,736): 0 differences; hidden: 45 boarding craft across all factions, 4 Antiproton-Defender-only units (Raiders' Jia, Civilians' Vyshesh, Vree Xeon and a Vree mine) |
| E2 - limited ammunition | `AIView::weaponBlock`, as the page's `checkOutOfAmmo`: a weapon with an `ammunition` count (the gatlings) fires while it is above 0 ('out of ammunition' at 0); an OLD missile rack (`MissileLauncher`, its rounds in `missileArray`) fires on a ship freely - the page counts no ship rack's rounds and the server keeps none - and on a flight in a mode with a round left (`AIView::modes`). A magazine-fed direct-fire weapon (`AmmoDirectWeapon`) is still left alone. Unlocks the Raptor, Templar, High Templar, Uplift, the Orieni ship racks and the fighter racks a creator buys missiles for | fireHarness boards: gatlings empty on one board in three, fighter racks loaded on every other; the oracle now FAILS a pairing the page refuses for ammunition unless the bot refuses it too |
| E9 - exclusive fighter weapons | `weaponBlock` refuses only BALLISTIC exclusive weapons; its conflict test is the page's both ways (`checkConflictingFireOrder`). `FireDecider::conflicts` / `dropConflicts`: a craft's pick drops its conflicting guns (in the greedy pick and the T6g forced loop); `shipsOnly`: an exclusive gun takes no FLIGHT while its craft has another usable gun - while it reloads the paired guns take every target | fireOracle `commitFire`: the Fire plan's rows clicked in TOGETHER (a conflict the one-pairing clicks cannot see). Self-play 6010/6011: every Rutarian Ion Bolt at a ship, every paired gun at a fighter but one (a Hyperion, the Ion Bolt reloading); the Sitara at ships |
| Energy Mine spacing | `AoEDecider`: where an enemy ENDS is spread round its straight-on guess (`AIConfig::AOE_SPREAD` - flight 0.35 on the hex, 0.3 over the six next to it, 0.2 over the twelve two off; ship 0.5 / 0.25 / 0.1); a mine is valued by what it ADDS over the mines already planned (the most damage at each end counts once); greedy over (mine, hex), every hex within two of a guess a candidate. A second mine at a flight goes next to the first rather than idle or on it, when that is worth more than another enemy's hex. `AOE_GUESS_FLIGHT` / `AOE_GUESS_SHIP` retired | fireRulesHarness F4 (RED: 1 mine, the second idle -> GREEN: 10,2 + 11,2; with an Omega also in reach the second goes to the Omega's guess); self-play 6008/6009: paired mines on adjacent hexes (-11,-8 / -10,-8; -4,2 / -3,3) |
| Who can still fight | Found by Orieni self-play 6007: the beaten side's last flight (a High Templar, gatlings spent, rack bought empty) was counted able to fight - no surrender, the turn cap. `Posture::usableGuns` now asks `modes()` of old racks too, and `AIView::firesKind`: a gun of a kind the bot never fires (`AIView::NEVER_FIRED` - weaponBlock's P4 reasons) does not count - a ship left with only Antimatter Converters, Gravity Nets or Point Pulsars cannot fight | withdrawalHarness A8 (High Templar: empty / gatling rounds / rack missiles) + A9 (a Sharaal with only its Antimatter Converters): RED on the old Posture (a prepended mutant), GREEN |
| EM Shield | The hit chance always counted a target's shields (the server's formula); the damage they take off each hit now counts too: `AIView::damagePerHit` subtracts `getDamageMod` (the server's damage step - the strongest defence of each type, on arc from the shooter) for a target carrying a Shield-type defence (`AIView::shielded`, per unit: an EA ship's Interceptors are another type, so EA v EA decisions are unchanged) | fireRulesHarness G1 (RED 54.3 v 54.3 -> GREEN 53.7 v 54.3: a White Star's EM Shield takes 2 off every hit; it is hard to hit, ~20% for a Heavy Laser) |
| READY_FACTIONS | + Dilgar Imperium, Minbari Federation, Narn Regime, Centauri Republic, Centauri Republic (WotCR), Earth Alliance (Early), Orieni Imperium. FAQ ("What a CPU player can field") and the Starter Guide name them | the gates below |

**Gates (2026-11-23):**
- Fire pair over 13 directories (dilgar, EA_old, centauri_old, minbari, narn, centauri, orieni, EA, raiders and the four raider directories),
  phases 3 and 1 at Easy, Normal and Hard: 646 hulls, 121,076 pairings, 12,650 legal shots, 5,308 Fire-plan rows clicked in together (20 of them
  exclusive weapons), 889 launches and 48 Energy Mines planned, 26,009 jump-point declarations - **0 differences**. Orders pair, the same
  directories, three levels: 570 hulls, 3,420 situations - **0 differences**.
- ShipMover pair, Orieni (the one Major not flown before): 56 hulls, 40,152 plans through movement.js - **0 differences**; PHP half 901,499 checks.
- Harnesses: fireRules 915, levels 340, withdrawal 119, ram 60, hazards 14,758, deploySpread 66, budget 30, cpuLobby 51 (aligned: the list read
  from AIConfig, a Brakiri unit as the not-ready one, a breaching-pod refusal), flagOff - all PASS; manoeuvre 522 + its 2 known pre-existing
  "D" failures. FleetComposer + the real Fleet Checker: 384 fleets for every ready faction, all clean and all passed. Lobby parity of the
  missing systems: 2,736 blueprints, 0 differences.
- Self-play (`--budget=0`, FleetComposer fleets at 2,500 points against EA, Normal and Hard, 24x16 and 42x30): **Orieni 20/20**; Dilgar,
  Minbari, Narn, Centauri, Centauri (WotCR), EA (Early) 4/4 each (20/20 each in the first T9 session, before this session's changes); 8
  targeted games (exclusive weapons, gatlings and racks, spaced mines) 8/8 - 52 games, 0 AI defects. Turn caps are stalemates with both
  sides fighting (the known pattern). Kept for replay: 6007 (the High Templar game that found the "can still fight" gap), 6008-6015.
- Replay corpus: 243 passed, 0 failed (run after the registry fix below, which IS engine code). **Still owed: the user's play-test of the new factions.**

**Found while gating - and FIXED at the user's request the same day ("Please fix this leak"):** three engine registries were static
arrays every CONSTRUCTION appended to and nothing emptied - `GravityNetHandler::$gravityNets` (each Minbari Gravity Net),
`HkControlNodeOrieni::$nodeList` / `$hkList` and `HkControlNode::$nodeList` / `$hkList` (Nexus Makar; both cleared once per PROCESS,
`clearLists` / `$alreadyCleared`). Every game load, AI view and lobby probe added its objects, which kept their ships alive - a self-play
process grew ~13-27 MB a turn in Minbari and Orieni games (EA v EA and Centauri flat at ~30 MB) - and the handlers read entries of EARLIER
loads (a request that loads the game twice, as the AI's do, counted them again; an earlier load's Gravity Net could win "largest move"
and leave the resolving game's net with nothing to move). Now the game being resolved is asked: `GravityNetHandler::netsOf($gamedata)`,
`HkControlNode(Orieni)::nodesOf / flightsOf($gamedata)` - the flights by the CLASSES that register (`$hkClasses`, names only, never objects).
For a request that loads the game once this is exactly what `clearLists` left, launched flights included (`Manager::insertSingleShip`
puts them in `$gamedata->ships`). Proof: NEW `tests/ai/registryHarness.php` (RED 7 of 9 - 30 games kept 11 MB; the stale net won; the
stale Orieni nodes covered the shortfall; Nexus Makar share 0 instead of 0.2 -> GREEN 9/9, +0.05 MB); self-play probes flat (Minbari
34 -> 36 MB over 15 turns, was 238 MB peak; Orieni 42 -> 41 MB, was 411 MB by turn 13); the replay corpus 243 passed / 0 failed (it reloads, masks
and re-resolves damage for every game - 5 hold Orieni HKs, none a Gravity Net, and the crit-phase HK shortfall is not among its checks: those two
are the new harness's B and C). Other registries of
the same shape, fixed the same way at the user's next request ("let's pre-emptively fix these too"): the Nexus LCV, Polaren LCV and Drakh Raider
Controllers (`controllersOf($gamedata)`; `getIniBonus($unit, $gamedata)` - its 19 callers pass the game), the Mindrider Hangar (`hangarsOf` /
`projectionsOf`; a team checked once per game LOAD - a WeakMap - where it was once per process) and the Gravitic Mines
(`GraviticMineHandler::getAllMines($gamedata)`; its once-per-turn guard keyed by game AND turn). registryHarness E, F, G: RED 11 of 22
(30 dropped games kept 19.5 MB; stale controllers gave +15 initiative not +5; the second Mindrider game kept its excess Projections; 294 mines
found where the game had 7) -> GREEN 22/22 (+0.06 MB).

**Survey after the release work** (hidden units left out; fighter racks counted empty, as on the survey board - a creator buys their
missiles, FleetComposer does not): Centauri (WotCR) 99%, Orieni 98%, Dilgar 96%, Centauri 96%, Narn 96%, Minbari 95%, Earth Alliance (Early)
92% (Earth Alliance 95%, Raiders 97%). No hull class the lobby offers a CPU player is left without a gun it fires, but EA's Armed Missile
Shuttle on a board with no missiles (a 'shuttles' hangar: FleetComposer never buys it; a creator buys its missiles).

#### Missing systems - what the AI does not use yet (the user, 2026-11-23: "so we can tackle them one-by-one in future and unhide these units for ai use")

In the released factions first; the enablers E1-E11 above cover the rest. "Hidden" = on `AIConfig::CPU_MISSING_SYSTEMS` (and its lobby
mirror): taking the line out of both unhides the units.

| # | System | Today | Released factions (units hidden / mounts idle) | How players use it (the user) | The work |
|---|---|---|---|---|---|
| M1 | **Marines** (boarding) | hidden | 45 boarding craft - EA Lamprey, Raiders, Dilgar Jortavo, Minbari, Narn Tra'Kha (armed and not), Centauri Scion; every other faction's pods, the Trek Nausicaan gliders | "we will add Marines later" | a boarding decider (launch, approach, board) + the Marines' page rules; then the line out of both lists |
| M2 | **Hunter-Killers** (Orieni: remote-controlled flights, deploy in hangars, designed to ram) | hidden (`ShipMover::unsupportedHullReason`); HK Control Nodes idle | Orieni HK Shining Light / Shining Star | "launch them immediately ... usually target larger ships ... working their way down to HCV and MCV" | hangar deployment, launch from the hangars, flight rams (T9 note 6) |
| M3 | **Antiproton Defender** (special hit formula, E4) | ✅ BUILT 2026-11-23 - fired; the line OUT of both lists (the Jia is back) | Raiders' Jia (6 mounts) and Vastar / Vasy (12) | (no input needed) | done - "M8 and M3-M5 as built" |
| M4 | **Antimatter Converter** (special hit, E4) | ✅ BUILT 2026-11-23 - fired, damage priced by the roll, the pulse premium on its floors | Minbari 11 | the user, 2026-11-23: it "doesn't actually use the specialHitChanceCalculation ... in practice"; its damage "does benefit from better hit chances though, so just add a hit chance premium like we do with Pulse weapons" | done |
| M5 | **Point Pulsar** (special hit, E4) | ✅ BUILT 2026-11-23 - fired in its plain mode (three orders, one a gun) | Dilgar 4 | (no input needed) | done |
| M6 | **Gravity Net** (Pre-Firing, E6) | idle | Minbari 10 | both: judged by OEW - pull an enemy ship into the heavy-weapon arcs of the bot's ships that hold a lock on it, or push an enemy ship out of the heavy-weapon arcs it has on the bot's ships it holds a lock on (the user, 2026-11-23). S (largest move wins) / P (this net wins) not ruled separately | a Pre-Firing decider (E6): the net's move hexes priced by the bot's locked heavy guns gained / the enemy's locked heavy guns lost |
| M7 | **Pulsar Mine** (Pre-Firing, E6) | idle | Narn 2 (G'Karith) | scare fighters off; charge into enemy fighters when the G'Karith moves first - it triggers as they move, starting in its range | E6 |
| M8 | **Ballistic Torpedo** (splits by default) | ✅ BUILT 2026-11-23 | Centauri 3 | 2-3 at one or two ships, or 3-6 at a flight (separate fighters), backed by 1-2 OEW; Easy/Normal 1-3 a turn at one target, Hard splits more | done - "M8 and M3-M5 as built" |
| M9 | **Molecular Slicer** (splits by default) | idle | EA 2 | - | with M8 |
| M10 | **Twin Array with split arcs** | idle ('special arcs') | Centauri 2 | - | E11 |
| M11 | **Reloading at a carrier** (Hangar Ops) | gatlings and fighter racks run dry and stay dry | every gatling flight, every fighter rack | "if possible ... order fighters with empty gatling guns to go and reload in carriers but this is very rare"; ships with an ordnance supply could reload fighters' missiles and torpedoes | a FlightDecider "home to reload" + docking |
| M12 | **Fighter missiles in an auto-composed fleet** | `FleetComposer` buys plain units - a fighter rack carries nothing (a creator's fleet carries what was bought) | every rack-armed flight FleetComposer buys | - | buy missiles in FleetComposer (they are bought as enhancements / ammo) |
| M13 | **Adaptive Armour** | defaults (nothing allocated) | Minbari 2 hulls | points to the most numerous enemy weapon class (rams ignored), the rest as the ship is hit, up to the maximum | an Initial Orders handler |
| M14 | **Grappling Claw** (boarding) | idle | Raiders 2 | - | with M1 |
| M15 | **Exclusive BALLISTIC weapons** | idle (`weaponBlock`) | none in the released factions | - | a conflict rule in BallisticDecider (as E9's) |
| M16 | **Magazine-fed direct fire** (`AmmoDirectWeapon`) | idle | none in the released factions | - | E2's magazine half |
| - | Tractor Beam / Self-Repair / Chameleon Sensors | no code - ruled | Minbari, Raiders, Centauri | "ignore" / "fine at defaults" / "ignore for now" | none |
| - | Unreliable Twin Array | fires whole only - its page prototype is plain Particle, a Split click makes no order (page gap) | Centauri (one hull) | - | a page fix first |

#### M8 and M3-M5 as built (2026-11-23, fourth session) - uncommitted

**The user's request (2026-11-23):** "start working through the Missing Systems list ... starting with these: 1. M8 Ballistic Torpedo split
(Centauri) - ruled: Easy/Normal 1-3 a turn at one target, Hard splits more; 1-2 OEW behind it. 2. M4 / M5 / M3 special hit formulas - Antimatter
Converter (Minbari), Point Pulsar (Dilgar), Antiproton Defender (Raiders; unhides the Jia). Note - The Antimatter Converter doesn't actually use
the specialHitChanceCalculation that other Antimatter weapons do in practice (see its rngNormalPenalty and rngNormalPenalty settings). Its damage
does benefit from better hit chances though, so just add a hit chance premium like we do with Pulse weapons so we can change it from being idle on
ai ships. Also, this work will also be relevant for the Vree faction in future, with other relevant weapons being the Antiproton Gun, Antimatter
Torpedo and Antimatter Cannon."

**M3 / M4 / M5 - the special hit formulas.**

| Item | As built | Proof |
|---|---|---|
| Which special calculations are ported | `AIView::PORTED_SPECIAL` (class => the one kind it may carry): `AntimatterWeapon` => 'range' - its range brackets and "range doubled on no lock" are `AntimatterWeapon::calculateRangePenalty` (server) and antimatter.js `calculateSpecialRangePenalty` (page), a mirror pair, and the bot prices every shot with the server's own `calculateHitBase` anyway; `PointPulsar` => 'hit' - its page modifier is +4 for a CALLED shot at a fighter only, and the bot never calls a shot. `weaponBlock` refuses 'special hit calculation' only for a weapon outside the list, or carrying the other kind. The whole antimatter family is in: Converter, Antiproton Defender, and the Vree's Antiproton Gun, Antimatter Cannon and Antimatter Torpedo (ballistic - BallisticDecider's ordinary pass); the Shredder's hex mode and the Vree's turret links are still refused for their own reasons | Fire pair, every pairing's chance against the page's: 0 differences (below), Vree included |
| Damage by the roll | `AIView::rollScaled` (every AntimatterWeapon) + `rollDamage($weapon, $needed)`: the mean of the weapon's OWN `getDamage` over every d100 roll that hits (1..need, capped at 100; X = (needed - rolled) / 5, maxX, the Reduced Damage critical), remembered per weapon, mode and need. `damagePerHit` takes the need (price, estimate, launch estimates); `damageAfter` moves a hit's worth down with the need interception leaves (Weapon::fire measures X from the need AFTER interception). The plain mid-point priced a Converter (2..82) at 42 a hit: at 60% it does 18, at 20% 2 | fireRulesHarness H2 (the hit = 4X+2 at its need, by an independent sum, at 60% and 20%), H3 (a better chance is a better hit), H4 (an intercepted shot loses more than its share of hits) |
| The premium | `FireDecider::minimumChance`: a slow weapon whose hit grows with the roll gets FIRE_PULSE_PREMIUM on both floors, as a slow pulse gun does (skill pulsePremium - not Easy): the Converter (3 turns) 44 / 44 parting; a one-turn Antiproton Defender keeps the plain floor | H5 |
| Point Pulsar | plain mode only (`modes()` never offers Split): three orders, one a gun, at one unit | H6; fire pair |
| The Jia | `AntiprotonDefender` out of `AIConfig::CPU_MISSING_SYSTEMS` AND gamelobby.js `CPU_MISSING_SYSTEMS` (legacy bundle rebuilt). Also unhides (outside READY_FACTIONS, so no effect yet) Civilians' Vyshesh, Vree Xeon and a Vree mine | H7 (cpuUnitBlock null; its Defenders have shots; FireDecider takes them); the survey probe: Raiders hidden 4 -> 3 |
| Who can still fight | Posture::usableGuns asks firesKind - a ship left with only Converters, Point Pulsars or Antiproton Defenders CAN fight now | withdrawalHarness A9 moved to a Rogata (Gravity Nets - still never fired), A10 (a Sharaal with only its Converters can fight) |

**M8 - the Ballistic Torpedo.** How the server resolves it (read before pricing it): a launcher holds up to 6 and gains one a turn; each page
click is one order (`BallisticTorpedo.doMultipleFireOrders`, notes 'Split'); at a SHIP all of one launcher's torpedoes are merged into ONE order
before interception (`beforeFiringOrderResolution` in `Firing::prepareFiring`, then `automateIntercept`) - one roll, one interception, 1 + one
more torpedo for every 20 the roll beats the need by, up to the number fired; at a FLIGHT each is its own order and roll at a fighter of its own.

| Item | As built | Proof |
|---|---|---|
| Pricing | `AIView::isTorpedo` (BallisticTorpedo - the Kirishiac Phased Gravitic Torpedo extends it), `torpedoShot($unit, $weapon, $target, $count)`: 'orders' $count (posted rows, one shot each), 'pulses' (the most one roll lands: $count at a ship, 1 at a flight), 'merged' (a ship: InterceptModel makes ONE order of it, carrying all the torpedoes), 'damage' the whole volley. `shot()` of a torpedo = one torpedo (one click). `launched(..., $count)` for the Fire phase's own-missile volleys; `launchEstimate` (next turn, ShipDecider) prices the volley the bot would send | fireRulesHarness I1 (k = 1..4 at a ship lands exactly the saturation sum; never more than held), I2 (one merged order of three), I5 (two at a flight = twice one) |
| The decision | `BallisticDecider::torpedoes`, after the salvos, one launcher at a time (the one worth most first). A torpedo is worth what it ADDS to its target's volley, interception counted, and is launched only while what it adds keeps the launch floor (BALLISTIC_MIN_CHANCE 35 / _FLIGHT 50; Easy any chance worth an order) - at 80% with nothing intercepting the 1st lands 0.8, the 2nd 0.6, the 3rd 0.4: three; at 60%: two. Easy / Normal (skill `torpedoTargets` 1): the one unit whose whole volley is worth most. Hard (2): a torpedo at a time wherever it adds most, up to two units. At most `AIConfig::TORPEDO_SHIP_VOLLEY` (3) at ships; at a flight the level's `torpedoFlightVolley` (Easy/Normal 3, Hard 6) and no launcher more than its live craft - each on its own (`AIView::torpedoCap`; the user, 2026-11-23: launchers do not work together). Posted as the page clicks them (`systems()` keeps a two-target launcher's orders on its one system row) | I3 (a ship at its chance gets the floor-rule count, posted an order a torpedo, ballistic, one shot, 'Split'), I4 (two Omegas: Easy {3}, Normal {3}, Hard {2, 1}), I5 (a flight: Normal 3, Hard 6) |
| A full launcher's reload (implementer's call - to confirm) | Self-play 6146 (Demos + Elutarian v EA): the launchers sat at 6 of 6 for 23 turns, the Elutarian's never fired. The InterceptModel gives a target's EVERY interceptor to the volley, as if nothing else came at it (the same worst case as for missiles - but a merged volley cannot saturate them): an Omega's six take a 90% volley to nothing, a Hyperion's five 85% to 25%. On the board the interceptors were mostly elsewhere (15 points stopped on one of three launches, none on the others). So a launcher holding its normal load that would otherwise launch nothing sends its ONE torpedo - next turn's, which a full launcher loses - at the unit it is worth most at unintercepted, where its chance alone meets the launch floor; a torpedo that draws the interceptors is one fewer on the bot's guns (FireDecider counts its own missiles in the volleys it prices) | I6 (an armed Omega: holding 6 - one goes; holding 5 - kept), every level |
| "Backed by 1 or 2 OEW" | `EWDecider::allocate`: a ship whose torpedoes launch this turn (`AIView::launcherNow`) puts a point, BEFORE the search, on the enemy SHIP they would do most to with it (`AIView::torpedoEstimate` - the volley the bot would send, launched from the start-of-turn hex with the arc as the ship stands; only where the point adds something - a jammer denies it). `score()` counts each launcher's volley at its best enemy, so the search may add the second. Flights are CCEW's (noFlightOEW). Easy's simpleEW unchanged | J1 (a torpedo-only Demos and a G'Quan at 22: 1 OEW; the launcher gone too: 0), J2 (two armed G'Quans at 14 - the search alone keeps all 9 on DEW: still 1 OEW) |
| The oracle | fireHarness: a torpedo launcher holds 2, 4, 6, 2 by round (no die thrown - other hulls' boards unchanged); torpedo rows checked (one row a unit, what it holds, its units, the volley sizes, a volley the view prices; interception against the real automateIntercept on the merged order). fireOracle.js `commitLaunches`: a split-by-default weapon takes one click per order, the page's shot count re-read after each (`initializationUpdate`, as the ship window does on SystemDataChanged) | the fire pair below |

**Gates (2026-11-23):**
- Fire pair over the 13 released directories (dilgar, EA_old, centauri_old, minbari, narn, centauri, orieni, EA, raiders and the four raider
  directories), phases 3 and 1, Easy / Normal / Hard: Fire phase 646 hulls, 121,077 pairings, 12,732 legal shots (12,650 before - the Converters,
  Point Pulsars and Defenders), 12,718 of them built exactly by the page (the rest on an edge, counted); Initial Orders 42,108 pairings, 4,048 launches, torpedo rows Easy 8 / 18
  torpedoes, Normal and Hard 6 / 8 - **0 differences** at every level and phase. No pairing the page could fire is left for "special hit
  calculation" or "split shots" in these directories (HEAD: 334 and 12 over five of them). Vree (informational): 415 + 106 shots, 0 differences.
- Orders pair, the same directories, three levels: 570 hulls, 3,420 situations committed through power.js and ew.js - **0 differences**.
- fireRulesHarness 967 (H, I, J new); mutants - roll damage off, merge off, ship cap off, the torpedo lock off (caught by J2: the search alone
  kept every point on DEW), the premium off - all five caught. withdrawal 121, levels 340, ram 60, deploySpread 66, budget 30, registry 22,
  hazards 14,758 PASS; manoeuvre 522 + its 2 known pre-existing "D" failures.
- Survey (the status table's measure, reproduced exactly on HEAD first): Centauri 96 -> 97%, Dilgar 96 -> 97%, Minbari 95 -> 98%, Raiders
  97 -> 99% (the Jia back: hidden 4 -> 3); the rest unchanged.
- Self-play (`--budget=0`, 24x16, kept): 6145 Sharaal + Tigara (Converters fired), 6147 Ochlavitas (Point Pulsars - three orders a volley),
  6148 Jia + Vastar + Vasy (Defenders fired), Centauri 6144 / 6146 (before the reload rule: 3 torpedoes in 23 turns, the Elutarian's none) and
  6149 / 6151 (v EA) and 6150 / 6152 (v Narn, Hard and Normal) after it: 66, 10, 78 and 61 torpedoes, both Elutarian launchers firing all game
  (14-27 each), volleys of 1-3 - 11 games, all PASS (no refused submission, no AI defect).

**Found, reported, not fixed:**
- `BallisticTorpedo` (torpedo.php) described Saturation Mode as "one more for every 3 points it beats the needed number by" - the code's `grouping`
  is 20 (percent: every 4 points of a d20). FIXED by the user in the M8 commit (eab7370c0: "every 20% points").
- `BallisticTorpedo::beforeFiringOrderResolution` builds its fighter list with `array_unshift` OUTSIDE the "is it alive" test, so every craft -
  destroyed ones too - goes first: a torpedo may be aimed at a dead fighter. `FighterFlight::getHitSystem` then falls back to the ordinary
  allocation, so it still hits a live one - and "excess munitions are lost" (its description) is not what happens while any craft lives. Every
  launcher's torpedoes go down the SAME list, so two launchers at one flight start on the same craft. The bot never sends more than the live
  craft PER LAUNCHER (the user's ruling - below). The list itself is not changed.
- ⚠️ A HARD MOVEMENT LOOP (pre-existing, T5f-5 - ShipDecider and ShipMover are untouched here; the torpedo games just do more thruster damage):
  in 6149 the EA Omega, two thrusters lost (#30 turn 2, #22 turn 10), BEGAN a left extended turn every turn from 10 to 35 and cancelled it for
  'thrusters' the next - 27 `validateExtendedTurn[TRIPWIRE]` lines, facing stuck. ShipDecider's begin test compares the owed remainder (3 side
  thrust) with `ShipMover::thrustNextTurn()` - the TOTAL - not with the thrusters on the side that must pay; with criticals on them (6150's
  G'Quans: First Thrust Ignored / Half Efficiency) the same. HEAD v HEAD on the same fleets: 2 and 1 tripwires. FIXED the same day - "M8 follow-ups".
- The Vree: their antimatter guns pass the fire pair now (phase 3 415 shots, phase 1 106, 0 differences); what is left idle there is the turret
  link (E10: `linkedFiringSpread`, 115 page-fireable pairings).

#### M8 follow-ups (2026-11-23 - the user committed M8 / M3-M5 as eab7370c0, then asked the same day) - uncommitted

**The user's request:** "I fixed the Ballistic Torpedo description, can you fix the Hard Movement Loop bug and prevent ai from firing more torpedoes
than live fighters in a flight (since they would be wasted)." The full-launcher reload rule was ruled "fine".

| Item | As built | Proof |
|---|---|---|
| The Hard extended-turn loop | `ShipMover::extendedPayableNextTurn()` - the page's OWN activation test (`extendedCancelReason`: the engine shorted since the begin, the owed remainder put on the thrusters that must pay each role - the side role on the side the turn needs - paid greedily within the page's ceiling, criticals included, then the engine's total), asked of a copy of the mover moved on to next turn (no thrust spent, the engine without this turn's boost, `carried` 0). Every plan carries it (`plan()['extendedPayable']`); `ShipDecider::candidates` keeps a begin only where it is true - it compared the remainder with the engine's TOTAL (`thrustNextTurn`), which a lost side thruster never failed. Not played: next turn's forced pivot (doForcedPivot runs before the page's cancel); damage or criticals taken after the begin (Fire phase) cannot be foreseen and still cancel, as for a player | levelsHarness J2 (an Omega at speed 10, port / starboard / no thrusters lost: 14 / 14 / 20 begins, every one's flag equal to an INDEPENDENT next-turn mover - `new ShipMover($ship, TURN + 1)` built from the plan's rows, whose construction plays the page's forced cancel; 7 of 14 cancelled with a side lost; Hard keeps none of them; with the old totals test restored (a mutant) Hard keeps all 14 - RED). manoeuvreHarness D ("port thrusters dead: Hard rolls") - the two "known pre-existing" failures since T6 were THIS bug: 524 / 0 now. Self-play replays of the looping matchups: Centauri v EA (6157) 1 tripwire, Centauri v Narn (6156) 0 - were 43 each (6149, 6150); the one left is a Hyperion's turn-4 begin cancelled on turn 5 after a ShipDisabledOneTurn critical and a retro thruster hit in turn 4's Fire phase - unforeseeable, and it began six more after it without one |
| Torpedoes at a flight | Asked first as "never more than live fighters" and built across all launchers - then RULED (the user, the same day): "launchers shouldn't work together. As long as each individual launcher doesn't launch more torpedoes than a fighter flights has that's fine". So the cross-launcher cap came out again: `AIView::torpedoCap` holds EACH launcher to the flight's live craft (as built at M8) and two launchers may together send more | fireRulesHarness I7 (an Elutarian's two launchers at a disarmed flight of 3 and 6 craft: at most 3 / 3 / 6 from one launcher - Normal 3 craft, Normal 6, Hard 6 - 6 or 12 in all); fire pair Initial Orders over the Centauri directories, three levels, 0 differences |
| A warning from M8 | `EWDecider::score` read `$state['launchers']`, which levelsHarness section O's hand-built state does not carry ("Undefined array key", unseen behind a `grep ^PASS`) - now optional | levelsHarness 348, no warning |

**Gates (2026-11-23):** ShipMover pair, EA + raiders in five parts: 157 hulls, 106,817 plans through movement.js - **0 differences** (the same count
as T8's: the search unchanged); fire pair Initial Orders over the 13 released directories at Easy / Normal / Hard - **0 differences**; levelsHarness 348
(J2 new), fireRulesHarness 975 (I7 new - per launcher), manoeuvreHarness 524 / 0, withdrawal 121, ram 60 - no warnings. Kept: 6156, 6157.

**Left as it is (ruled):** `BallisticTorpedo::beforeFiringOrderResolution` gives EVERY launcher the same fighter list (dead craft first -
`array_unshift` outside the alive test), so two launchers at one flight start on the same craft - the user, 2026-11-23: launchers do not work
together; each keeping to the flight's live craft is enough. No server change.

### 10.11 Stage T8 - the skirmish release (2026-11-22) - COMPLETE (user, 2026-11-22), uncommitted

**The user's request (2026-11-22):** "Please begin implementing Stage T8 of the Tactical AI Plan - I'd like to include the following
other improvements in this Stage alongside the currently planned features:
* Give CPU player cards their own neutral colour border and background colours in gamelobby page (instead of the mix of red and green
  they have atm). Maybe one tone we use in the buyShip menu like the gunmetal etc.
* Don't show CPU games in the 'Join Games' section of the lobby if there are no actual slots for other players. E.g. a game with its
  creator and one AI slot currently appears in games.php screen under Join Games and it shouldn't.
* Using the 'Save Settings' button in Create Game and reloading those settings is not remembering when a Team was selected as a CPU
  player, it should.
* Build ramming (T6d) stage that was previously deferred.
* The most important part of this stage will likely be the optimisation of decision time, as the success of the CPU player project is
  based on it being functional on a live server, which I will test myself after this stage."

And later the same day: "The block to Leave Game when you've readied a fleet in gamelobby shouldn't apply when the only other
'players' in the gamelobby are AI opponents. E.g. if I've readied but the CPU players haven't I should be able to leave the game even
after I've readied a fleet."

**The user's ruling (asked 2026-11-22) on the Fleet Checker:** the server `FleetComposer` is built TO the checker's rules and proven
by a harness that runs the REAL client checker (`gamelobby.js checkChoices`) under node on every composed fleet across factions, points
and in-service dates - the oracle pattern of T1b-T7. **No PHP port of the checker now**: the campaign (T10), which must check fleets
it did not build, is where one is needed.

The as-built record. The ledger (`.superpowers/sdd/TACTICAL_AI_PLAN/progress.md`) holds every sub-stage and ruling in order.

#### Decision time (section 12's review - "the most important part")

* **Measured first** (12, item 1): `tests/ai/decisionBench.php` - DB-free boards of live size (the live export: median side 10
  units, 75th percentile 16), every phase and level, with `--budget=0` decision FINGERPRINTS so a speed-up is proven to choose the
  same orders as a snapshot of HEAD; a sampling profiler (excimer, in a throwaway `fv-phpprof` image). Movement was the whole cost
  (an EA live-size simultaneous activation: Easy ~8.8 s, Normal ~11.7 s, Hard ~15.5 s of CPU unbudgeted; Initial Orders 0.05-0.5 s;
  Firing under 0.1 s). Of a Movement decision, `AIView::estimate` took 55% (this turn's fire, the incoming fire, the next-turn terms,
  the flights) and `ShipMover::enumerate` 38% (its `can()` pre-checks 29% of that, `searchKey` 19%).
* **The search** (item 3, mirror-safe): no `can()` before an `apply()` (`searchActions` filters by the allow flags; an illegal step
  simply fails to apply); ONE search for the within-ratings and the overthrust plans - the overthrust search is seeded only from the
  states where a rating stopped an order (and from extended-turn begins), and a seed's child is kept only when it really overthrusts;
  `searchKey`'s thrust channels in one pass; every walk over this turn's movement rows starts at `turnStart`. Turn shortening is
  never bought over a rating, overthrust allowance or not (the user's ruling of 2026-11-19).
* **Judging** (item 4): a gun's fire at a target is priced ONCE PER HEX - `estimate` never depends on the shooter's facing, heading
  or speed (checked first, on 4,752 gun / target pairs), so the arc-free figure is cached per hex and the arc applied per facing
  (`onArc`), after a line-of-sight and range pre-check; flights the same, per hex and jinking level.
* **The engine, result-identical:** `BaseShip::getLastMovement` / `getCoPos` / `getHexPos` / `getFacingAngle` and
  `FighterFlight::getFacingAngle` in O(1) (`array_key_last`), `getStructureSystem` by a table (integer locations; anything else keeps
  the walk), `AIView::rolledSections` fills its table only for a bearing two entries share.
* **A whole turn, not one ship** (item 2): a Movement SUBMISSION has a CPU budget (`AIConfig::MOVE_SUBMISSION_SECONDS` 8), shared out
  between the units still to decide (each `min(MOVE_DECISION_SECONDS, max(MOVE_UNIT_FLOOR_SECONDS 0.3, left / units))`); flights are
  budgeted as well (search, coverage goals, judging). **REQUEST SLICING:** once a unit is decided and the request's wall deadline
  (`AIConfig::BUDGET_SECONDS`) has passed, `MoveDecider` stops; `TacticalAI::play` returns 'pending' with the decided rows kept in
  `tac_ai_state['moving']`, and the next request - any player's poll - decides the rest and submits. A live request is never held
  for a whole activation.
* **Guarded** (item 8): `selfPlayHarness --slice=S` (each driver run gets S seconds of wall, as a request does), `--max-decision`
  and `--mean-target` (a game FAILS past them); `tests/ai/budgetHarness.php`.
* **For the live host:** `AIBenchmark` + `public/aiBenchmark.php` (behind the maintenance gate, `?key=`) - one decision on a board in
  memory, its CPU and wall time, memory, RSS and the host's limits. The user's step.
* Not built: items 5-7 (the forecast across activations, the other phases, one gamedata load across submissions). Initial Orders and
  Firing take well under a second and the forecast 0.01-0.12 s a ship in self-play; the live timing will show whether loading needs it.

**Results** (dev box, one process, quiet, `--budget=0`, the same boards and the SAME decisions - every fingerprint identical; the one
exception, a Brakiri Hard board where the wider overthrust search found a better extended turn, was a superset effect, kept):

| Live-size simultaneous activation (10 units a side), CPU | HEAD 56b03b2d3 | T8 | | slowest ship HEAD -> T8 |
|---|---|---|---|---|
| EA (7 ships + flights), Normal | 13.05 s | 8.12 s | 1.61x | 3.05 -> 1.66 s |
| EA, Hard | 17.76 s | 10.84 s | 1.64x | 4.10 -> 2.11 s |
| Raiders (8 ships + flights), Normal | 8.36 s | 5.61 s | 1.49x | 2.00 -> 1.13 s |
| Raiders, Hard | 12.15 s | 7.96 s | 1.53x | 2.66 -> 1.51 s |

24 decision fingerprints (3 boards x 2 levels x 2 fleets x Movement and Firing) identical to HEAD.

With the budget on, no submission takes more than 8 s of CPU and no request more than `BUDGET_SECONDS` of wall. Self-play with
`--slice=0.5` (games 5847, 5848 and 5868, simultaneous): every game clean; in 5868, 18 Movement activations were finished across
requests ("N units decided in earlier requests of this activation").

#### The user's lobby extras

* **CPU cards:** `.lb-slot--cpu` in gunmetal (`gameLobby.css`, from `--fv-gunmetal`), the class taken off a freed slot.
* **Join Games:** `DBManager::getLobbyGames` leaves out a CPU game with no free slot for anyone not in it; its creator still finds it
  there (lobby games are listed only there, as Fleet Test games are). Ruled by the implementer - cost if wrong: one condition.
* **Save Settings:** Create Game's presets keep each slot's CPU tick and level (`readSettings`, `presetSlots`).
* **Leave Game:** `onLeaveClicked` no longer counts CPU slots as players a readied fleet would strand.

#### T6d - last-resort ramming

A ship of a team the **Desperate** rule covers (`Posture::desperate`) rams when it has no usable gun or its PRIMARY structure is down to
`AIConfig::RAM_PRIMARY_SHARE` (0.25) - Normal and Hard only (skill `ram`; Easy and the frozen 'legacy' never). `ShipDecider` scores a
ram term (`Posture::ramValue`: the damage it deals, capped at the target's health and priced at its worth, against the return damage
x its own worth x the danger weight) for end hexes holding an enemy SHIP whose Fire-phase hex is known (moved this turn, or never
moves); when a ram is worth making the rammer chooses among its ramming plans only (they are always judged whole). `FireDecider` then
orders the `RammingAttack` at the best enemy ship in its hex. Flights do not ram. Rulings (implementer): a ram only when its own trade
is positive (the "best trade" ruling of 2026-11-16), then committed to; ram targets only where their hex is known. Proven:
`tests/ai/ramHarness.php` (27 checks); self-play `--desperate`: in game 5849 a crippled Omega rammed on turns 8 and 9, both orders
resolved by the server (needed 65 / 55, rolled 100 / 81).

#### Levels, and CPU seats on the pages

* Create Game: a level picker beside each CPU tick (Easy / Normal / Hard, hidden until the tick is set; presets and the summary carry
  it). No personality picker - `AIConfig::PERSONALITIES` has one entry, and options nobody can take are not shown (the user's rule).
* The lobby card reads "CPU Alpha (Hard)"; the games list tags a CPU game "vs CPU" (`Manager::getTacGames`, `AISeats::gameHasCpu`).

#### Auto-compose (8.2)

* **`FleetComposer::compose($faction, $points, $isd, $seed)`** reads the faction's catalogue (`static/json/<faction>.json`, what the
  lobby's Store and checker read) and buys plain combat units: common and uncommon only, official, in service by the game's date, no
  base / OSAT / mine / out-of-tier / custom unit, no craft needing a hangar other than a fighter's, no hull whose hangars carry a
  minimum it would not fill (Fighter Squadrons, Mapmaker Probes), nothing `AIConfig::cpuUnitBlock` refuses; fighters in flights of six;
  no enhancements, refits or officers. Capital ships first (one per 5,000 points from 3,000) with a jump engine among them, ships to a
  share of the points drawn from the seed, flights until every carrier's hangars are at least half full (a carrier the points cannot
  fill goes), the dearest that still fits, then a bounded trade-up pass while under 98% of the points. `problems()` holds the
  checker's rules such a fleet can break; it returns the clean attempt spending most. Its own generator: a seed gives one fleet.
* **Proven against the real checker:** `tests/ai/fleetComposerHarness.php` composes for every ready faction x 14 point values
  (500-15,000) x 8 in-service dates x 8 seeds; `tests/ai/fleetCheckOracle.js` loads the real `gamelobby.js` (with `systems.js` and
  `hangarShared.js`), puts each fleet in `gamedata.ships` as the lobby's own rows and runs `checkChoices`. **All 1,490 fleets the
  composer called clean passed, with no caution** (EA 600, Raiders 890); 6 deliberately broken control fleets were all caught. 302
  could not be composed - EA by 2180 or 2200 (no jump-engine ship in service), EA at 500-1500 points by 2215, and some seeds at 500
  points - and are refused (nothing either faction fields is that cheap below ~450 points). Spending: 44
  of the 1,490 under 98% of their points (EA at 750: 86.7%, the catalogue's granularity; one EA 1000 at 95.7%; three Raiders 500).
* **The server:** `Manager::autoComposeCpuSlot` (the guards of `submitFleetForCpuSlot`; `gamedata.php`'s `autoCompose`) composes for
  the slot's points (`AIConfig::COMPOSE_UNLIMITED_POINTS` 3500 when unlimited) and the game's In-Service Date, of the Store's open
  faction when the creator readies the CPU slot itself and a CPU can field it (from the creator's own Ready the Store shows THEIR
  faction, so none is named), else of one drawn from the game and slot - trying the other ready factions before it refuses, naming
  why (the creator then buys by hand) - and commits it as the bot.
* **The lobby:** Ready on an EMPTY CPU slot offers to auto-compose it (it used to refuse); the creator's own Ready names every CPU slot
  still empty in its confirm and composes them after the submit (plan 8.1). A CPU slot with units bought still takes its own Ready (the
  2026-11-13 deviation stands). Like any opponent's, an auto-composed fleet is hidden until play (A4).
* Rulings (implementer, 2026-11-22): the creator's Ready composes EMPTY CPU slots only; the faction rule above; 3500 points for an
  unlimited slot.

#### Docs

A FAQ entry, **Playing Against the CPU** (`faq.php#cpu`), worded for the Beta: setting up, what a CPU player can field, auto-compose,
the levels, the game, leaving. Ruled by the implementer: a FAQ entry rather than a sixth DATA ARCHIVE document for a flag-gated
feature - cost if wrong: moving the section into its own document.

#### Gates (2026-11-22)

ShipMover pair on the final engine (EA + raiders): 2,005,468 PHP checks, **106,817 plans agreed with `movement.js`, 0 differed**
(22,210 overthrust plans; 1,456 jump-outs). DB-free: levels 274, withdrawal 114, hazards 14,758, fireRules 392, budget 30, ram 27 -
all pass; manoeuvre 522 with the same 2 failures as HEAD ("D ... port thrusters dead (hard): rolls" - pre-existing, not T8's).
`cpuLobbyHarness` all pass (with 15 auto-compose checks - a composed game starts and both CPU fleets deploy), `flagOffHarness` all
pass, node: `lobbyAutoCompose.js` 19, `lobbyLeaveCpu.js` 7, `createGamePresetCpu.js` 10 (HEAD fails each). Replay: 239 passed, 0
failed, 2 skipped (5641 and 5743 - the user's own test games, which need the user to re-record). Self-play: sliced and Desperate games
clean.

#### Timed on the live host (the user, 2026-11-22, `aiBenchmark.php`, board 1) - and COMPLETE

| Movement decision | CPU | wall | slowest ship | cut by the budget |
|---|---|---|---|---|
| EA live (10 a side), Normal | 4.02 / 3.89 s | 4.05 / 3.91 s | 0.78 / 0.76 s | 0 |
| EA live, Hard | 5.30 s | 5.33 s | 1.03 s | 0 |
| Raiders live, Hard | 3.95 s | 3.98 s | 0.74 s | 0 |
| Raiders large (16 a side), Hard, game budget | 7.60 s | 7.66 s | 0.55 s | 3 of 12 |
| Raiders large, Hard, no budget | 8.97 s | 9.03 s | 1.00 s | 0 |

The live host is about TWICE as fast as the dev box, wall equals CPU (no throttling), and memory is no concern: PHP 8.4.28
(litespeed), memory_limit 2048M, max_execution_time 120 s; the large Hard decision peaked at 22 MB in PHP, 66.5 MB resident. The
benchmark runs a whole activation in one call; in a game a request stops at `BUDGET_SECONDS` (4 s) and the next poll finishes it,
with a fresh submission budget for the units left - so the three cut ships above are fewer in play. The user played a Normal game
and found it plays fine. **Stage T8 marked COMPLETE by the user, 2026-11-22: "I think that concludes Stage T8."**

#### Open

* The Beta via `betaTesters` (db/cpuOpponent.sql on the live DB first), then the flag - the user's.
* Play-testing: a Desperate-rule game, an auto-composed slot, a sliced Movement on live.
* The `FleetChecker` port - T10.

### 10.12 Play-test 5826 - the user's notes (2026-11-22) - BUILT, uncommitted

Game 5826, Normal: Omega x2, Tethys, Aurora and Thunderbolt flights against the user's G'Quan, Va'Kar and Frazi (played with the T8
tree). The user: "Overall recent Stages have made a big improvement to CPU behaviour, since my last test game, 4774. Feels a lot more
like a real opponent." Three notes. Each decision was replayed from the database first and reproduced exactly (EW of turns 3-5, the
Aurora's turn-4 move). ⚠️ To rewind a past turn: a `tac_systemdata` loading row for turn t holds the loading AFTER turn t's Fire phase,
so the loading DURING turn T is row T-1's - re-apply those rows over the load.

| # | The user's note | Why the bot did it | Built |
|---|---|---|---|
| 1 | Turn 3: both Omegas had heavy guns ready but put no OEW on either Narn ship; CCEW (correct), and OEW on the Frazi. "A human player would likely have soft-locked both ships and allocated 1 CCEW on each Omega." | The enemy is taken to repeat last turn's EW, and on turn 2 the Va'Kar put DIST 3 on each Omega: one point of disruption, which the engine shares over ALL of a ship's OEW and CCEW rows (`EW::getDistruptionEW` divides by `getOEWTargetNum`). `EWDecider::score` priced each enemy with only its own row on the ship, so a lone first point on the G'Quan met the whole point (no lock, gain 0 - Heavy Laser 3.7 / 3.7 / 6.1 at OEW 0 / 1 / 2) and the point-at-a-time search stopped there. The Frazi was priced with its CCEW row beside its OEW (disruption halved), so it alone looked lockable. Omega #2: OEW 2 Frazi, CCEW 2, DEW 4; Omega #1: CCEW 1, DEW 7. (DEW was worth nothing: the only Narn guns in reach were ballistic - the Va'Kar's Ion Torpedo - which DEW's pricing leaves out; the two Heavy Lasers were reloading) | `score()` prices a split with every row of it on the ship. Where an enemy ELINT disrupts the ship, a step of the search may move 1 + that disruption's points onto one enemy at once (`lockStep`, the engine's own sum), judged by gain per point; with nothing disrupting it the search is exactly as before (orders harness: 936 situations a level, every row identical to HEAD with `closeLocks` off). Replay: Omega #2 OEW 2 G'Quan + 1 Va'Kar + 1 Frazi, CCEW 1, DEW 3; Omega #1 OEW 7 G'Quan, CCEW 1, DEW 0 - a hard lock: it moves after the G'Quan, 17 hexes off (the 2026-11-15 ruling). Superseded by the user's rulings below |
| 2 | Turn 4: both Omegas should have soft-locked the G'Quan "at these ranges" | Both put 2 (the close-range cap) on the Va'Kar and kept DEW 5 (floor 4). `score()` counts every gun against the ONE enemy it does most to, arcs unknown before Movement; with the Va'Kar (moving first, full weight) locked, no gun was ever expected to fire at the G'Quan (moving last, half weight), so a point there gained nothing. In the Fire phase 13 Omega guns fired at the G'Quan, none with a lock | Within `EW_CLOSE_RANGE`, before the search, one point on every enemy SHIP a loaded gun could reach, nearest first, while DEW keeps its close-range floor; the search adds the second point where it pays (skill `closeLocks`: Normal and Hard; Easy's simple split never reaches it, `legacy` stays as it was). Replay: both Omegas OEW 2 Va'Kar + 1 G'Quan, CCEW 1, DEW 4. Turn 5 unchanged |
| 3 | Turn 4: the Auroras flew far too far from the battle despite moving last; 4 or 5 hexes off and firing would have been better | Heading away from all three enemies at speed 9, all 250 of the flight's plainest plans flew on (230 of its 1,956 places, none near). The coverage search picked good places (scores 126-167; plans to them existed) but reached none: 80 movers a place were too few for the loop back, and on boards like it with the enemy facing the flight the best-scored places all lie beyond the enemy, facing back - a second turn-about no plan has - and those used up its 12 tries. Ended 9-11 hexes off, no shot (score 17) | `FLIGHT_COVERAGE_PLANS` 4 (160 movers a place, was 2) and `FLIGHT_COVERAGE_TRIES` 6 (places asked = 6 x `coverageGoals`, was 3); no place is asked once the flight's search time is spent. Measured on 5826 and four boards like it (scratch `flightsim.php`): 20 plans x 3 still failed one board at 0.95-1.0 s a Normal decision; 4 x 6 reached a good place on all at 0.59-0.60 s (HEAD 0.42). Replay: ends at (-7,0) facing the enemy - 2 hexes from the Va'Kar, 4 from the G'Quan, fire 272 against danger 81 |

**The user's rulings on the first fix (2026-11-22, asked).** The first fix left Omega #1 on turn 3 with all 7 points on the G'Quan and
DEW 0 (a hard lock: it moves after the G'Quan, 17 hexes off), Omega #2 with one OEW point on the Frazi, and the Aurora 2 hexes from the
Va'Kar rather than 4-5. Ruled:
* *"At range 10-20 hexes I think we should not go full EW with Normal or Hard CPU players, unless the danger of return fire is very low.
  50% EW max lock would be more appropriate."* Built (skill `mediumLocks`, Normal and Hard): an enemy beyond `EW_CLOSE_RANGE` and
  within `EW_MEDIUM_RANGE` (20) gets a lock of at most `EW_MEDIUM_LOCK_SHARE` (0.5) of the ship's points - per lock, as "max lock" reads
  (several locks may still add up past half). Implementer's reading of "very low": no loaded gun or launcher of any enemy could reach
  the ship this turn at all, both closing as fast as they can and the enemy's every point locked on - `PowerDecider::reaches`, Hard's
  power test (now public), not the threats DEW is priced against: those stand the enemy still and leave ballistics out, and on turn 3
  they were empty (the Narn Heavy Lasers reloading) though the Va'Kar's Medium Pulses and Ion Torpedo would reach a closing Omega.
* *"As a rule OEW should never be used on fighter flights, CCEW is generally enough by itself. The only caveat would be if there were only
  enemy fighters left and they are likely to be more than 10 hexes away."* Built (skill `noFlightOEW`, Normal and Hard): a flight takes
  OEW only when the enemy has no ship left and the flight is more than `EW_CCEW_REACH` from where the ship stands and where it flies
  straight on - its distance now standing in for "likely" (implementer's reading). An LCV, which the page makes put all but 2 points
  into OEW, may still lock a flight when nothing else can take them. Easy was left as it was at first (half OEW on the nearest
  enemy, a flight included - it never put up CCEW); the user ruled: *"Easy should just follow the same CCEW rule as Normal and not put
  OEW on fighters ever."* Built: Easy has `ccewDeterrent` and `noFlightOEW` (Normal's caveat included); its simple split is still half
  offensive, half DEW, nothing weighed, but the offensive half is now the CCEW deterrent first and the rest OEW on the nearest SHIP -
  or, with only fighters left within CCEW's reach, CCEW (no further than Normal's cap). 5826 at Easy, turn 3: each Omega OEW 3 G'Quan,
  CCEW 1, DEW 4 (was OEW 4 on the Frazi).
* *"Go with the more efficient route for now. But note the decision in the plan."* **Decision (the user's, 2026-11-22): the coverage
  search keeps 4 plans x 6 tries.** The 4-hexes-off place in 5826, (-8,-1) facing the enemy (score 155.3 against the 150.0 of the
  (-7,0) chosen), is reached only with 20 plans a place - about five times the search (0.95 s against 0.60 s a Normal decision on the
  dev box). Revisit if flights are seen parking closer than players would.

Turn 3 after the rulings (Normal and Hard alike): Omega #2 OEW 2 G'Quan + 1 Va'Kar, CCEW 1, DEW 4 (the user's "soft-locked both ships and
allocated 1 CCEW"); Omega #1 OEW 4 G'Quan (half its 8) + 1 Va'Kar, CCEW 1, DEW 2; the Tethys OEW 3 G'Quan (half its 6), CCEW 1, DEW 2
(was 5, DEW 0). Turn 4: the Omegas as above (OEW 2 Va'Kar + 1 G'Quan, CCEW 1, DEW 4); the Tethys's Frazi point goes to the G'Quan.
Turn 5 unchanged. Not built (too small to matter here): DEW's value leaves this turn's ballistic launches out (the Ion Torpedo: 0.13 a
DEW point).

**Gates (2026-11-22, final - the HEAD snapshot first):** levels 324 (50 new or rewritten: section O, and section C for Easy's new
split; on HEAD they fail as the game did - DEW 8 with no lock; OEW 2 Frazi, CCEW 2, DEW 4, 5826's turn-3 Omega #2 row for row; only the
Va'Kar locked up close; the Aurora 9 hexes off; Easy's whole half on the Frazi. Mutants, each caught by its own check: the lock step, the
whole-split pricing, `closeLocks`, the tries, `mediumLocks`, the danger test always on and always off, the band past 20 hexes,
`noFlightOEW`, the fighters-only caveat, Easy's old skills; 4 plans against 2 is a quality knob these boards do not pin). Orders pair
3/3 0 differences: Normal 677 -> 1,091 offensive EW points, Hard 867 -> 1,031, Easy the same 1,671 points split anew in 386 of 936
situations (598 -> 917 offensive rows: the deterrent, the ships); with the three Normal/Hard skills off, every one of 936 situations a
level is identical to HEAD (the pricing and the step change nothing where nothing disrupts), and Easy's change left Normal and Hard
untouched (936 of 936 identical to the run before it).
Fire pair 6/6 0 differences; fireRules 392, hazards 14,758, withdrawal 114; manoeuvre 522 with HEAD's 2 known failures.
`decisionBench` (live size, game budgets, against the HEAD snapshot): Initial Orders EA 0.28 / 0.35 s Normal / Hard (HEAD 0.27 / 0.33),
Raiders 0.19 / 0.25 s (HEAD 0.17 / 0.23); Movement (before the rulings, which touch only Initial Orders) EA 6.97 / 7.99 s (HEAD 7.12 /
8.00), the same ship decisions cut. Self-play on 24x16, all PASS - before the rulings: EA mirror with both flights, Normal v Normal (5870,
5872 - 41 and 35 turns) and 5826's own fleets, the bot flying the Narn side too (5869, 5871); after them: the mirror again (5873 - 29
turns, ended by a CPU surrender; 5876 - 17 turns; ship decisions mean 0.29-0.35 s, slowest 1.27 s) and EA Hard v Narn Normal (5874,
5875 - 9 turns each, EA keeping 95-96% of its value; Hard decisions mean 0.47-0.48 s, slowest 1.61 s); after the Easy ruling: Easy v
Normal, seats alternated (5877, 5878 - 15 and 17 turns, Normal ahead in both). The replay corpus was not re-run: no engine code changed.

---

## 11. Testing

* **Self-play harness** (`tests/ai/selfPlayHarness.php`, CLI): creates a game with two or more bot
  seats and given or composed fleets, loops the driver until FINISHED or a turn cap, and asserts: no
  rejected submission, no validator finding, no PHP warning, the game ended. Run in batches across
  faction pairs and seeds. This doubles as a fuzz test of the engine itself - expect it to find real
  FV bugs; report them, do not fold fixes into AI work without asking.
* **`shipMoverHarness.php`**: section 5.4 (flights since 10.6). **`fireHarness.php` + `fireOracle.js`**:
  section 10.4; `--phase=1` for launches and magazines, and the interception parity check, 10.6.
  **`ordersHarness.php` + `ordersOracle.js`**: section 10.5. **`hazardsHarness.php`** (the board: drift,
  meteors, mines; no database, no oracle - the engine's own rules are the reference): section 10.7, T5c.
* **Ladder harness**: win rates between levels and against a frozen previous AI version, to catch
  regressions in play strength.
* **Masking test**: run a decider with the unmasked object swapped in; decisions must not change
  unless a hidden unit is in play, and the test fails if any decider reads a field the mask removes
  (wrap the masked view so access to masked sentinels is logged).
* **Replay harness** stays green and byte-identical for the existing corpus (P6).
* Self-play games are ordinary games, so `replay.php` shows them - the fastest way to judge play quality.
* CLI harness bootstrap follows the replay harness (do not include `global.php`; the load guard
  exits silently on CLI). APCu is off on CLI.

---

## 12. Performance notes

* Loading gamedata is the dominant cost and is per-ship CPU; the driver loads once per submission.
* The engagement table is O(my weapons x enemy units); cache hit-chance results per (weapon, target,
  position) within a decision; in movement search, evaluate arcs and range with cheap geometry first
  and call `calculateHitBase` only for the pruned candidates.
* Live is lsphp with a memory limit that returns 503 when exceeded: keep per-request work to one
  submission, free the gamedata between loop iterations, and measure memory in the self-play harness.
* `tac_ai_log` is capped per game and off by default on live.
* **Decision time - a T8 deliverable (user ruling 2026-11-19: "reasonable decision time is important
  for live server").** Measured at T5c (10.7): one Movement decision takes 0.1-0.5 s for capital and
  heavy hulls and up to 1.9 s for a nimble one (Tethys, ~500 end states once turns may be shortened);
  one Movement submission carries every unit of the bot's activation, and `AIConfig::BUDGET_SECONDS`
  (4) is only checked BETWEEN submissions. **Turn shortening is not where the time comes back from**
  (user ruling, same day: "shortening turn delay is always useful (since any delay carries over to next
  turn as well) if you have the spare thrust on that turn, and you won't exceed thruster ratings. Normal
  and Hard CPU opponents should do it by default really (easy opponents can skip)") - it stays on for
  Normal and Hard. To build at T8, after profiling (Tethys: search 0.4-0.6 s, judging the end states the
  rest): a budget per decision (a ship that runs out takes the best plan scored so far - the
  plainest-first search order makes that a sound partial answer); the next-turn terms - a whole second
  board evaluation per end state - asked only of the end states whose this-turn score leaves them within
  reach of the best; timings taken on the test instance (live is lsphp on shared hosting, not the Docker
  stack) for fleets of live size, and the self-play harness printing the slowest decision so a regression
  shows. **BUILT at T5f-8 (2026-11-20, brought forward to pay for the enemy forecast - section 10.7):** the
  per-ship CPU budget, two-pass judging, the forecast's budget and the harness's timing line. Still for T8:
  the timings on the test instance for live-sized fleets.
* **T8: a HOLISTIC REVIEW of decision efficiency** (user, 2026-11-20: "in Stage 8 plan a holistic review
  of how we can make AI decisions as efficient as possible to keep decisions times reasonable"). The plan:
  1. **Measure before cutting.** A decision benchmark (DB-free, like `levelsHarness`'s boards) per phase -
     Deployment, Initial Orders (power, EW, ballistics), Movement (forecast, search, judging), Firing - per
     level and for live-sized fleets of every ready faction, run on the Docker stack AND the live host
     (lsphp; its CPU and its 503 memory limit), with a baseline table in this section and a target per
     phase. Profile with a sampling profiler (xdebug or excimer in a throwaway container), not guesses.
  2. **Whole-turn cost, not one ship.** What a player waits for is a SUBMISSION: under simultaneous movement
     one carries every ship of the bot's activation, and each ship has its own budget today. Give the
     submission a budget and share it between its ships (the T5f-8 per-ship budget is the floor); check
     `AIConfig::BUDGET_SECONDS` against live's request limits, and wall time against CPU time there.
  3. **The movement search - FIRST** (the largest single cost for nimble hulls - Tethys 1.2-2.1 s - and above
     all for GRAVITIC ones: alone on the dev machine a Brakiri Tashkat searches 3.5 s at Normal (767 end
     states) and 5.5 s at Hard (1,110), a Halik 2.9 / 5.0 s - 85-90% of their decision, so the T5f-8 budget
     cuts EVERY Brakiri ship decision at 1.5 s of search and the bot flies them on their plainest plans):
     the overthrust search repeats the whole within-ratings search for 2-35 end states more - one search
     that carries both, or an overthrust search only from states where a rating stopped a move (mirror-
     safe, proven by the ShipMover pair); why a gravitic hull's search costs ~2x a plain hull's per end
     state (the expansion cap, `limit x 12`, may be what bounds it); cheaper mover copies (clone and apply
     dominate); dominance pruning of end states that are the same but for more delay or less thrust left.
  4. **Judging and the hit formula:** cheap geometry first (range, arc) and `AIView::estimate` only where it
     can be non-zero; one cache of hit chances per (shooter position and facing, weapon kind, target
     position and facing) shared by every ship of a decision and by the forecast, which prices the same
     enemy guns at the same bot units again and again.
  5. **The forecast across a turn:** the enemies yet to move are forecast again at every bot activation;
     keep a forecast until something it priced has moved (the bot's own ships move between activations).
  6. **The other phases:** FireDecider's engagement table, InterceptModel, EWDecider and PowerDecider's
     reach test, BallisticDecider - profiled the same way; most were sized for small fleets.
  7. **Gamedata:** loading is per-ship CPU and the driver loads once per submission - measure the share it
     takes on live, and whether the AI can work on one load across submissions of the same request.
  8. **Guard it:** the self-play harness's timing line gets thresholds (the slowest decision over budget, a
     level's mean over its target) that fail a run, so a regression shows in every ladder.
  **Done at T8 (2026-11-22) - section 10.11:** items 1-4 and 8 built (a live-size Movement activation 1.5-1.6x faster with the same
  decisions, a submission budget and request slicing); 5-7 not needed on the dev-box figures; the live-host timing is the user's.

---

## 13. Live game data (offered by the user, 2026-11-13)

The user can export tables from the live database. This is worth having, with one expectation set
straight: **it is for calibrating and testing a rule-based AI, not for training a learned model.**
A neural or learned policy is out of reach here: the live server purges games idle for 3 months, so
the corpus is a few hundred recent games across ~90 factions; the state is huge and faction-specific;
and live is shared PHP hosting with nowhere to run a model. Human orders are also made with hidden
information the logs do not separate out. What the data is good for:

| Use | What is measured | Feeds |
|---|---|---|
| **AutoCombat calibration** | per finished game: each side's points, faction, hull mix; who won; share of value lost by winner and loser; turns to decision | `CAMPAIGN_PLAN.md` 6.5 - replaces guessed targets with real ones. The single most valuable use |
| **Fleet templates** | what players actually field per faction at each points level: hull-size shares, fighter share, common class combinations | `FleetComposer` templates (8.2), instead of hand-written ones |
| **Deployment priors** | where each hull size sits relative to the fleet centre, group counts, starting speeds | `FleetDeployer` defaults (5.2) |
| **Movement priors** | per class: usual speed, engagement range by turn, how often it turns | range bands and comfortable speeds in `AIConfig` |
| **EW priors** | OEW / DEW / CCEW split by ship role and range to nearest enemy | `EWDecider` defaults and Easy-level behaviour |
| **Targeting priors** | what humans shoot first (size, damage state, range), focus-fire rate, weapon-vs-target-type pairings | `FireDecider` weights |
| **Withdrawal** | fleet value ratio and turn at which players surrender | `WITHDRAW_RATIO` |
| **Agreement benchmark** | replay a human's situation, ask the AI for its order, compare | a regression score for play quality (11), alongside the ladder harness |
| **Self-play seeds** | real fleets and set-ups | varied, realistic inputs for the self-play harness |

Where ladder rankings exist, weight games by player skill so the priors come from good play.

### 13.1 What to export

Finished games only (`tac_game.status = 'FINISHED'` and `turn >= 4`), all rows for those game ids:

* Needed: `tac_game` (**without `password_hash`**), `tac_playeringame`, `tac_ship`,
  `tac_shipmovement`, `tac_fireorder`, `tac_damage`, `tac_critical`, `tac_ew`, `tac_iniative`,
  `tac_flightsize`, `tac_enhancements`.
* Useful: `tac_power`, `tac_ammo`, `tac_sys_enhancements`, `tac_individual_notes`, `tac_systemdata`,
  `tac_ladder_games`, `tac_ladder_rankings`, and the saved-fleet tables for PUBLIC lists only
  (`tac_saved_list` where `isPublic = 1`, with their `tac_saved_ship` / `_enh` / `_ammo` rows).
* **Do not export:** `player` (password hashes, Discord ids), `chat`, `player_chat`,
  `tac_savedorders`, `tac_minesweep`. Numeric player ids inside the game tables are all the analysis
  needs; no usernames.

### 13.2 Handling rules

* ⚠️ **Import into a SEPARATE local database** (for example `B5CGM_live`), never into the local
  `B5CGM`. The replay harness's corpus is "every game in the local DB"; importing live games there
  changes the corpus and its committed baseline.
* Keep the dump outside the repo (for example `c:\tmp`) - large files in the repo slow the Docker
  boot sync. The local client is `mariadb`, not `mysql`.
* Two kinds of analysis, with different reach:
  * **SQL-only** (points, outcomes, positions, ranges, EW splits, target choices by ship id): works
    on every exported game, whatever its age.
  * **Engine reload** (the agreement benchmark, anything needing arcs or hit chances): the game must
    load under today's ship classes. System ids are construction-order, so a game played before a
    class was reshaped may not reload faithfully; filter to games that pass a reload check and
    accept a smaller set.
* Analysis scripts live in `tests/ai/analysis/` (gitignored with the rest of `/tests`) and write
  their results into a committed, human-readable `source/server/ai/AIPriors.php` (plain arrays with
  the sample size beside each figure). The AI reads priors, never the raw data, so live never needs
  the dump. A prior with a small sample falls back to the hand-set default.
* This is a one-off export, repeatable whenever a fresher snapshot would help (for example before
  each faction joins the readiness list).

When to do it: before campaign Stage 6 (AutoCombat) or tactical stage T1a (deployer defaults),
whichever comes first. Nothing earlier depends on it.

**Received 2026-11-13:** `C:\FV_env\db_backups\liveDbTablesForAI\u253336_b5cgmAIData.sql` (93 MB,
16 tables). First use: section 10.3. Two things about this export:
* it has no `tac_playeringame` and no `tac_iniative`, so teams, slots, deployment zones,
  surrenders and initiative order cannot be read from it - needed for AutoCombat calibration,
  deployment priors and withdrawal;
* `tac_game` came with its `password_hash` column (3 games have one). Leave that column out next
  time, and keep this file out of the repo.
It also holds games of every status, not only FINISHED ones; filter on `tac_game.status` / `turn`
when it matters.

---

## 14. Open decisions (defaults will be built unless ruled otherwise)

| # | Question | Default |
|---|---|---|
| A1 | RULED 2026-11-12: CPU seats in ladder games | Never |
| A2 | Do CPU games count in player statistics / recent games? | Listed, marked as CPU games, no ladder effect |
| A3 | RULED 2026-11-12: fleet for a skirmish CPU | The creator selects the CPU slot and builds or loads a fleet for it; an empty slot is auto-composed (plain, and Fleet Checker clean). Section 8 |
| A4 | May players see the CPU's fleet in the lobby before the game? | The creator sees what they built; an auto-composed fleet and other players' view stay hidden until play, like a human opponent's |
| A9 | Who may build a CPU slot's fleet in a team game | Only the game's creator |
| A10 | Auto-Deploy: where do fighters go | A screen in front of their group; on the carrier's hex only where the game expects a launch from it |
| A11 | Auto-Deploy: starting speed | One common speed per battlegroup that every unit in it can hold comfortably; the player can change it before committing |
| A12 | Auto-Deploy with no capital ships | The largest hull size present anchors the groups, and the group-count choice is based on that |
| A13 | Auto-Deploy in games with simultaneous or hidden deployment | Works unchanged: it only fills in the player's own placements before they commit |
| A5 | Team games with CPU allies | Allowed; bots do not coordinate with humans beyond sharing targets by proximity |
| A6 | Handicap | Optional points bonus for the CPU, shown in the lobby; never hidden bonuses |
| A7 | First factions | EA and Raiders, then Narn and Centauri |
| A8 | Does the bot chat? | A one-line greeting and "gg" only; no taunts |
