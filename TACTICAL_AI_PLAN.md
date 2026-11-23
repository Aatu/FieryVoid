# Tactical AI Plan (CPU opponent for Fiery Void battles)

Status: **Stage T0 COMPLETE (committed 2026-11-13, 5807979b8, play-tested in game 4568; as-built
notes: section 10.1). Stage T1a COMPLETE (committed 2026-11-13, 9537a8b72, Auto-Deploy play-tested
by the user; section 10.2). Stage T1b COMPLETE (2026-11-13, uncommitted; harnesses and self-play green, play-tested by the user in game 4626; section 10.3). Stage T2 COMPLETE (committed 15624ab89; play-tested by the user in game 4679; one part of its "done when" is still open - section 10.4). Stage T3 COMPLETE and Stage T4 COMPLETE (committed together as 501272f04; play-tested by the user in games 4698 and 4701; sections 10.5, 10.6). Stage T5 STARTED 2026-11-16 (section 10.7).** T6 onward not started. Companion plans: `CAMPAIGN_PLAN.md` (the campaign
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

Out of scope for v1: boarding and marines, ramming as a tactic, mine laying, called shots, hangar
recovery mid-battle, jump-point tactics other than leaving, Fighter Missions, extended turns,
gravitic drive rotation, officers' active abilities, and every faction special not on the readiness
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

* When the bot's remaining fleet value falls below `WITHDRAW_RATIO` of the enemy's and the trend is
  negative, posture becomes withdraw: open range, keep firing. v1 then surrenders through the
  ordinary surrender path once out of effective range or when nothing can still fight.
* Stage T6 adds leaving by jump point for jump-capable ships (the existing jump-out flow), which is
  what the campaign prefers (survivors return to the map).
* The bot never surrenders on turn 1-2.

### 5.8 Difficulty and personality

| Level | Behaviour |
|---|---|
| Easy | score noise high; no focus-fire bonus; simple EW (half OEW on nearest, half DEW); fires ballistics whenever legal |
| Normal | as designed above |
| Hard | one-ply look-ahead in movement (assume each enemy's best reply), EW optimised per point, ammunition discipline, target priority by value x vulnerability, withdraws in good order |

Personality is a weight preset (`aggressive`, `balanced`, `cautious`, `skirmisher`) that changes
range band and withdrawal threshold. A faction has a default (also used by the strategic AI's
flavour table). All randomness comes from the seat's seed, so a game is reproducible.

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
drive needs its mover rules), Dilgar, then raiders and civilians (needed by the campaign's Raider
faction - prioritise `raiders` right after EA), then Minbari (jammer, stealth handling), Abbai
(shields), Vree, and onward. Ancients and custom universes last.

The Create Game dialog offers only ready factions for a CPU slot; the campaign asks the same list
(section 9).

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

## 9. Campaign wiring (stage T9)

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
| **T5 Movement search** | `MoveDecider` scoring, range bands, terrain (drifting too) and mine avoidance, pivots and rolls, gravitic hulls; closing with the enemy (the T2 pursuit question, ruled into T5); moving to bring the most direct-fire weapons into arc within the turn AND the most ballistic launchers to bear for next turn's launch (user, game 4701) | Hard beats Normal beats Easy in self-play ladders at >= 60% |
| **T6 Withdrawal** | posture logic, surrender, jump-out | No game runs past the turn cap for lack of a decision |
| **T7 Specials** | handler registry; handlers faction by faction; readiness list grows | Each added faction meets the section 7 criteria |
| **T8 Skirmish release** | server `FleetChecker` port + parity test; `FleetComposer` (auto-compose as the default for an empty CPU slot); level and personality pickers; lobby and games-list display of CPU seats; docs page | Every auto-composed fleet across all ready factions and a range of point values passes the Fleet Checker; Beta on live via `betaTesters`, then the flag |
| **T9 Campaign wiring** | section 9 | A campaign battle against a Raider fleet is played manually and harvested |

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

### 10.7 Stage T5 - movement search (started 2026-11-16)

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
| T5c | Terrain that drifts (next position predicted) and mines the bot can see, avoided. Drift (`TerrainDrift`): from turn 2, at the START of each turn a one-hex asteroid or meteor swarm stays (25%) or moves one hex straight on or 60 degrees either side (25% each), and a unit in the hex it enters is struck in Pre-Firing - so an end hex in a field's three forward hexes is a 25% overrun each, and next turn's continuation must miss all four of its possible hexes. Mines: read their trigger rules first. |
| T5d | Gravitic hulls in `ShipMover` (mirror + oracle). |
| T5e | Pivots and rolls for the damaged-ship uses above (mirror + oracle). |
| T5f | Levels (Easy: noise; Hard: one-ply look-ahead, the enemy's best reply) and the ladder harness - the stage's "done when". |

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
    ships. Not seen to misbehave yet; not changed (T4 code).
  * `EWDecider` still judges the bot's own ships where flying straight on takes them.
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

---

## 11. Testing

* **Self-play harness** (`tests/ai/selfPlayHarness.php`, CLI): creates a game with two or more bot
  seats and given or composed fleets, loops the driver until FINISHED or a turn cap, and asserts: no
  rejected submission, no validator finding, no PHP warning, the game ended. Run in batches across
  faction pairs and seeds. This doubles as a fuzz test of the engine itself - expect it to find real
  FV bugs; report them, do not fold fixes into AI work without asking.
* **`shipMoverHarness.php`**: section 5.4 (flights since 10.6). **`fireHarness.php` + `fireOracle.js`**:
  section 10.4; `--phase=1` for launches and magazines, and the interception parity check, 10.6.
  **`ordersHarness.php` + `ordersOracle.js`**: section 10.5.
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
