# Reinforcements — Jump Point EXITS

Jump Points Phase 3. Phases 1 and 2 gave a ship, and then a fixed gate, a way to open a vortex and
**leave** the battle ([JUMP_POINTS_PLAN.md](JUMP_POINTS_PLAN.md),
[JUMP_GATES_PLAN.md](JUMP_GATES_PLAN.md)). This one is the other direction: a player buys part of
their fleet as **reinforcements**, those units wait in hyperspace, and during the battle the player
opens an **exit** vortex and brings them onto the map through it.

Status: **STAGES 0–7 COMPLETE AND SIGNED OFF 2026-08-28 — Stage 6 verified in play in game 4317,
Stage 7 in game 4318. STAGE 8 BUILT 2026-08-28 and FIRST PLAY-TESTED 2026-08-29 in game 4319: the
gate signal, the manifest and the waves all worked, and the two faults that came back were both
OUTSIDE the gate code — see "Stage 8 — first play test". Both fixed and harness-proven; the rest of
Stage 8 is still untested in play. STAGES 9 AND 10 BUILT 2026-08-29, untested in play.**

⚠️⚠️ **STAGE 10a MOVED THE DEVIATION ROLL AND REWROTE §2.3.** From 2026-08-29 the roll happens at the
**end of Initial Orders** on the formation turn, not at the end of the Firing phase, and the blue
marker both players see is moved to the hex the doorway actually formed at. Every Stage 3–9 note that
mentions the timing, and the concealment argument behind it, is superseded by §2.3.

---

## ⚠️⚠️ READ THIS FIRST — THE TWO WORDS WERE SWAPPED ON 2026-08-29 (Stage 9)

Up to Stage 8 this document and the whole codebase called the **blue** vortex an *entrance* and the
**yellow** one an *exit*. That was backwards, and the artwork had always said so — `JumpPointExit.png`
has been the blue file since long before this feature existed. Stage 9 swapped both words, in the
code and **throughout this file**, so:

| colour | the word, from **2026-08-29** | class | declaration `damageclass` | what it is |
|---|---|---|---|---|
| 🔵 blue | jump point **EXIT** | `SpawnJumpPointExit` | `jumpexit` / `gateexit` | a doorway **out of** hyperspace — reinforcements arrive |
| 🟡 yellow | jump point **ENTRANCE** | `SpawnJumpPoint` | `jumppoint` | a doorway **into** hyperspace — units leave the battle |

⚠️ **The yellow class was deliberately NOT renamed.** It stays the bare `SpawnJumpPoint`. Renaming it
to `SpawnJumpPointEntrance` would have been symmetrical *and* would have made one string mean the
opposite vortex either side of a deploy — in the database, in a stale branch, in a half-updated file.
Nothing was renamed **into** a name that already existed.

⚠️ **Every stage entry below was originally written with the words the other way round.** The swap was
mechanical and the meanings were preserved, but if you are reading a Stage 3–8 note beside a git
history or a screenshot from before that date, the older text will say the opposite word. The
migration for the two persisted strings is `db/reinforcementsRename.sql`.

⚠️ **`JUMP_POINTS_PLAN.md` and `JUMP_GATES_PLAN.md` were NOT swapped** — they predate reinforcements
and are almost entirely about the yellow vortex, which they call "the jump point" rather than either
word. Where they do say *exit*, read *entrance*.

**The whole loop closes.** A player buys reinforcements; they are concealed from the enemy down to a
count and a point total; they **declare** a jump point exit in Initial Orders and name its
manifest; at the end of that turn the exit **forms** — the deviation is rolled, a blue
`SpawnJumpPointExit` goes onto the board at the hex it lands on, and every unit riding it is
stamped with `tac_ship.arrivalturn = turn + 1`; and on that turn the owner gets a **Deployment
phase** in which the wave **places itself** in the doorway on the vortex's facing, stacking freely,
leaving the player only its speed to set (Stage 7a). Reinforcements have arrived under fire in a real
game, with three units through one doorway and three different speeds among them.

⚠️ **THREE OF STAGE 7'S FIXES ARE HARNESS-PROVEN BUT NOT YET SEEN IN PLAY** — the two Stage 7a
fallout fixes (the deploy-start dock dialog that explained nothing, and the opponent's missing
blueprints) and the lobby header restyle. Everything else in Stages 0–7 has been exercised in
4317/4318.

✅ **THE REPLAY BASELINE HAS BEEN RE-RECORDED** (2026-08-28, before Stage 8). It had needed its
single re-record since Stage 2 and was reporting **149 failures** — every one of them the same three
Stage 2/5 payload additions (`reinforcementCount`, `reinforcementPoints`, `formingExits`), which
was enough noise to hide a real regression completely. `check` on the Stage 8 tree is now **155
passed, 0 failed**, with game 4318 SKIPping because it has advanced since it was recorded. Note the
baseline is **gitignored** and therefore per-working-copy: `DouglasChanges` has its own and will want
its own `record` ([[reference_fv_repo]]).

Stages 9 (the scatter initiative penalty) and 10 (the roll's timing, the penalty's UI, the legacy
drives' recharge, and the blast-radius pass) are both built. **What remains is play-testing.**

Three things Stage 0 needed that §3 did not anticipate, all recorded here because they are the shape
of the next surprise too:

- **`stripForJson` is a WHITELIST, not a property dump.** §3.1 says the three fields are public "so
  they reach every ship JSON" - they do not. `BaseShip::stripForJson` hand-builds a `stdClass`, so
  the three had to be added there (emitted only on a reinforcement, so every other payload is
  byte-identical) and again in `stripForJsonDisguised`, which rebuilds from a Chameleon sheet.
- **The fleet list has no deploy-turn filter at all** and §3.2 said it did. See the ⚠️ in §3.2:
  a fourth `setRowState` state now labels a hyperspace unit **Hyperspace** in the established cyan.
- **Trap 3 bites one stage early.** `DeploymentGamePhase::validateDeployment` calls `getTurnPlaced`
  on the POSTED ship and carried a comment saying that was safe because it read only the slot. The
  moment `getTurnPlaced` learned about `$reinforcement` that stopped being true, and a hyperspace
  unit would have been asked for a deploy move it cannot have - killing the whole submission with
  "Entry not found". It now resolves through `$gamedata->getShipById()`.

And five more from Stages 1–3, same reason:

- **`$reinforcement` is NOT an inert POST field, so the lobby's claim needed its own property.**
  §3.5 whitelists only `arrivalVia`, and trap 3 says every server rule must resolve through
  `getShipById` — but `submitShip` reads `$ship->reinforcement` off the POSTed object at buy time,
  so *something* has to travel. Writing it straight onto `$ship->reinforcement` would make **every
  POST-side reinforcement answer 999 to both turn accessors in every phase**, because a POST-side
  ship never carries `arrivalTurn`. Two live call sites would have broken silently:
  `Hangar::generateIndividualNotes` (`if ($ship->getTurnPlaced($gamedata) > $gamedata->turn) return;`
  — reached from Deployment, Initial Orders **and** Movement with the POSTed ships) and
  `HangarOps::validateDeployBayOrders`' POST-side `$carrier`. Neither resolves through
  `$gamedata->getShipById()` the way `DeploymentGamePhase` now does. The fix is a separate
  `BaseShip::$reinforcementClaim`, carried raw by `getShipsFromJSON` and promoted to the real flag
  by `BuyingGamePhase::process` alone — the `preBattleDamage` / `systemEnhancements` idiom.
  ⭐ **Those two POST-side `getTurnPlaced` sites are still landmines for Stage 7**, which is where a
  reinforcement carrier's deploy-start dock actually runs.
- **Four `standIn` sites poison the lobby's blueprint cache.** `doEditShip`, `doEditBulk`,
  `copyShip` and `copyBulk` each register a `jQuery.extend` copy of the edited unit as the class
  blueprint when a loaded fleet has no faction registered, and `new Ship(json)` copies **every** key
  — so an ad-hoc `reinforcement:true` would have been minted onto every future purchase of that
  class, silently, and only ever on a **loaded** fleet, which is exactly the case §0 says must be
  re-flagged by hand. All four now `delete standIn.reinforcement`, beside the existing
  `standIn.pointCost` line that exists for the identical reason. `doCopyShip` additionally rebuilds
  the ship a *second* time and needed the capture-and-re-apply the pre-battle damage uses.
- **An empty `SpawnJumpPointExit` subclass is a silent bug**, and §3.3 sketched exactly that.
  The parent's constructor sets `$this->phpclass = "SpawnJumpPoint"`, and **phpclass is the
  persisted identity**: `submitShip` writes the property (not `get_class()`), the reload does
  `new $phpclass(...)`, and it is the only route by which the client learns the class. Inherit it
  and the exit is an exit for one request, then reloads as an ordinary entrance anything can
  jump out through. The subclass constructor must set it.
- **`JumpEngine::$spawnableClasses` is a third channel §3.3 did not mention.** `BlueprintCache::build`
  reads it to preload `window.staticShips`, so without `'SpawnJumpPointExit'` on that list the
  first exit to appear on a **poll** (no page reload) renders as an empty hex until F5.
  Regenerating `Terrain.json` does not help — that file is the *lobby* catalogue; `game.php` builds
  its blueprints from `BlueprintCache`.
- **`isJumpVortex` must stay ENTRANCE-ONLY on the client.** Its callers disagree: `getVortexInHex`,
  `getVortexHeldBy` and everything downstream (the Jump Out button, Maintain, the "already holding a
  jump point open" refusal, the closing-vortex commit warning) are rules an exit must **fail**,
  while the icon z-plane, the map overlay colour, the hex-stack sweep and the replay lifecycle
  animation must **match**. Widening the one predicate silently flips the first group. Two siblings
  were added instead — `isJumpVortexExit` and `isAnyJumpVortex`.

⚠️ **STAGES 0–8 ARE ONE LIVE DEPLOY.** A lobby that can sell reinforcements without the runtime
that delivers them strands a player's points in hyperspace for the whole game. Local testing is
stage by stage; the deploy is not.

⭐ **THE REPLAY HARNESS BASELINE NEEDS RE-RECORDING ONCE, for Stage 2 — AND STAGE 7 IS THE MOMENT
TO DO IT.** `PlayerSlot` gained two public fields and Stage 5 a third, so every `snapshot_*` report
in the corpus now differs by exactly `/slots/N/reinforcementCount: added (0)`,
`/slots/N/reinforcementPoints: added (0)` and `/slots/N/formingExits: added (<0 item array>)` —
and **nothing else whatsoever**, re-confirmed after Stage 7 (which adds no payload difference of any
kind). As of Stage 7 that is **149 of 152 games failing**, which is more than enough noise to hide a
real regression: the harness has stopped being a regression detector until it is re-recorded.

The four behavioural checks are unaffected and remain usable in the meantime:
`--checks=movement,tohit,damage,masking` gives **155 passed, 0 failed** (game 4309 included, since
its [[arch_replay_corpus_known_failures]] failure is a snapshot one). Accept with
`replayHarness.php record` on a tree with Stages 0–7 in it; until then use the `--checks=` form.

---

## 0. Decisions already taken (2026-08-27, user)

| Question | Ruling |
|---|---|
| Several units through one exit in one turn | **All stack in the vortex hex.** They separate on their first movement |
| A SHIP's exit lifetime | **One-shot.** Forms turn N, delivers turn N+1, closes end of N+1 |
| A GATE's exit lifetime | The gate's **existing programmed hold** (1–4 turns); a wave may come through on each |
| Where an exit may be opened | **Anywhere legal on the map** — no projection range, because there is no ship on the board to measure from |
| What other teams see | **A count and a point total.** Never classes, never names |
| Facing | Exit facing `F` is the **doorway out**. An arriving unit is placed on heading `F` — the mirror of the entrance rule, and why the arrow is reversed |
| One-way | A blue exit can never be jumped out of; a yellow entrance can never be arrived through |
| Arriving units on their arrival turn | **Act normally.** They deploy in that turn's Deployment phase and then move and fire like anything else |
| Ancient (`factionAge >= 3`) deviation modifier | **−5** |
| Saved fleets | **Remember reinforcement status** (user request 2026-08-28, reversing the original ruling). One `reinforcement` tinyint on `tac_saved_ship`, carrying the **purchase-time flag only** — `arrivalturn` / `arrivalvia` are in-play state and are never saved, so a reloaded reinforcement is back in hyperspace exactly as a freshly bought one is. Loading into a game *without* the rule still lands everything in the main fleet |

Notes on two of those:

- **Arriving units acting normally is FV's existing reinforcement behaviour** (`depavailable`
  slots), not RAW — the tabletop keeps arrivals inert on the turn they appear. The warning the
  opponent gets is the blue Jump Point marker on the *previous* turn, which is exactly the trade
  [[arch_placement_turn_vs_deploy_turn]] already made for late slots.
- **Two of the tabletop's deviation modifiers have no FV equivalent and are omitted**: the Jump
  Accelerator (no such system in the tree) and "arriving in a nebula" (no nebula terrain —
  `model/ships/terrain/` has asteroids, moons, shipyards, gates and mines, and nothing else).

**Still open, and proposed rather than ruled:** the scatter **initiative penalty** is Stage 9 and
optional. The scatter distance and facing shift are recorded on a note when the vortex forms, so it
can be added at any later date without re-working anything before it.

---

## 1. What already exists, and what each piece buys us

| Piece | Where | What it gives this feature |
|---|---|---|
| `getTurnDeployed()` | [ShipClasses.php:3205](source/server/model/ships/ShipClasses.php#L3205) | ~80 call sites already mean "is this unit on the board?" — one branch here makes a hyperspace unit inert everywhere at once |
| `getTurnPlaced()` | [ShipClasses.php:3235](source/server/model/ships/ShipClasses.php#L3235) | The "is it being placed right now?" twin. Reinforcements need it to **not** subtract 1 |
| The vortex unit | [terrain/SpawnJumpPoint.php](source/server/model/ships/terrain/SpawnJumpPoint.php) | Subclass it; facing already lives in the deploy `MovementOrder` |
| `spawnVortexUnit()` | [baseSystems.php:6253](source/server/model/systems/baseSystems.php#L6253) | Deliberately shared between ships and gates. Takes one more parameter (the class) and is shared three ways |
| `validateVortexDeclaration` | [firing.php:112](source/server/handlers/firing.php#L112) | A dedicated hook off `validateFireOrders`, already branching ship-vs-gate. The exit is a third branch |
| The slot loop in `FireGamePhase::advance` | [FireGamePhase.php:76](source/server/Phase/FireGamePhase.php#L76) | Grants next turn's Deployment phase. One extra clause is the whole "reinforcements are arriving" trigger |
| `generateJumpPointHexes` / `markReinforcementHex` | [BallisticIconContainer.js:400](source/public/client/renderer/icon/BallisticIconContainer.js#L400) | The blue `hexBlue` marker **already exists and is already the reinforcement colour** (`#00b8e6`) |
| `UI.vortexFacing` | [UI/vortexFacing.js](source/public/client/UI/vortexFacing.js) | The whole facing transaction. Needs a colour parameter and a reversed arrow, nothing more |
| `UI.gateSignal` | [UI/gateSignal.js](source/public/client/UI/gateSignal.js) | The gate panel. Needs a blue variant and an exit flavour on the claim |
| `MineDeployment` + the `#iniGui` button | [gamedata.js:2358](source/public/client/gamedata.js#L2358) | The pattern for "a button in the panel puts the map into a bespoke click mode" |
| `AllowMinesRule` | [model/AllowMinesRule.php](source/server/model/AllowMinesRule.php) | A four-file game-rule template |
| `OffsetCoordinate::moveToDirection($dir, $steps)` | [OffsetCoordinate.php:61](source/server/model/OffsetCoordinate.php#L61) | The deviation walk, already written |
| `EW::getScannerOutput($ship, $turn)` | used at [ShipClasses.php:1988](source/server/model/ships/ShipClasses.php#L1988) | The B5W **sensor rating** |

---

## 2. The FV ruleset (unambiguous statement)

### 2.1 Buying reinforcements

- Game rule **Allow Reinforcements** (`allowReinforcements`), set at Create Game — **and always on in
  a Fleet Builder (`fleetTest`) lobby** (user request 2026-08-28). A Builder exists to compose and
  save fleets, and a saved fleet now remembers the flag (§0), so it has to be able to author one and
  load one back. Derived in `GameRules::getAllowReinforcementsRules` rather than written into
  `games.js`'s rules object: a game's rules JSON is stored once at creation and never rewritten, so
  adding the key there would leave every Fleet Builder lobby that already exists without it — and
  deriving it serves client and server from one decision, since the client reads
  `gamedata.rules.allowReinforcements`, which is that object's `jsonSerialize`. Off ⇒ nothing in
  this document exists.
- In the lobby a unit is bought either as **front-line** or as a **reinforcement**. Same shared
  point pool, same cap, same fleet-composition checks — a reinforcement is an ordinary purchase
  with a flag on it.
- A reinforcement never appears in the turn-1 Deployment phase and never occupies a hex until it
  arrives through an exit.
- **Warning at Ready**: if the reinforcement group contains no unit with a usable Jump Engine and
  no `JumpgateCapital` is present on the map, those units can never reach the battle. The player is
  told and must confirm.

### 2.2 Opening an exit

- Declared in **Initial Orders** by a reinforcement unit that is **still in hyperspace** and mounts
  an undestroyed, non-legacy Jump Engine — or by signalling a fixed gate.
- **No range test and no line-of-sight test.** There is no ship on the board to measure from. This
  is the single largest departure from the entrance rules and it is why the exit cannot ride
  `weaponManager.targetHex` (§3.4).
- The target hex must be **legal**: on the map, not holding any part of a Terrain unit, not holding
  another vortex or a gate, not holding an Enormous unit.
- The declaration carries a **facing** (0–5), set by the same on-map arrow control the entrance uses,
  drawn blue and pointing **outward**.
- Then the player names the **manifest** — which of their hyperspace units ride this jump point.
  Any number, including none but the opener, and including units with no jump engine of their own.
- **One exit per jump-drive-equipped reinforcement unit**, enforced by the existing
  one-vortex-per-ship rule.

### 2.3 Forming, arriving, closing

⚠️ **THE TIMING BELOW CHANGED ON 2026-08-29 (Stage 10a).** Anything written before that date says
the deviation is rolled at the **end of the formation turn**, and gives a concealment argument for
it. It is now rolled at the **end of Initial Orders** on the formation turn, and the declaration is
moved onto the hex it landed on, so both players can see where the doorway really is and play the
rest of turn N against it. The old argument, and why it was traded away, is kept below.

| Turn | State | What is on the board |
|---|---|---|
| N — declared in Initial Orders | **Declared** | nothing yet — the declaration is secret while Initial Orders are open |
| End of Initial Orders, N | **Deviation is rolled**, the vortex unit is created at its true hex, and the declaration is MOVED there | a **blue** "Jump Point Forming" hex + a reversed facing arrow **at the true hex**, public to both sides for the rest of turn N |
| N+1 | **Open.** The owner gets a Deployment phase; the manifest arrives | the blue vortex unit with its outward arrow |
| End of N+1 | **A ship's exit closes.** A gate's runs on its programmed hold | — |

⭐ **THE DEVIATION IS ROLLED AFTER INITIAL ORDERS CLOSE ON THE FORMATION TURN** (user ruling
2026-08-29, from the tabletop rules). Both players have committed their orders by then, so nobody
can aim a jump point at what they have just seen the enemy do; and from the Movement phase of turn N
onward **both sides can see where the exit will actually be, and react to it** — instead of finding
out at the start of the arrival turn with nothing left to do about it.

⭐ **THE DOORWAY IS SHOWN BY MOVING THE DECLARATION, NOT BY REVEALING THE UNIT.** `openExitVortex`
rewrites its own fire order's `x`, `y` and `firingMode` to the hex and facing the vortex actually
got, and persists that with `updateFireOrders`. Both viewers already draw the blue marker from that
one order — the owner reads it directly, and an opponent gets it republished onto their `PlayerSlot`
by `TacGamedata::republishFormingExits`, which lifts the same three fields — so one write moves both
markers to the same hex with **no client change at all**. The vortex UNIT still carries
`spawned = openTurn + 1` and still appears on the board on the arrival turn and not before, so
nothing in the lifecycle moved with it.

⚠️ **THIS REVERSES THE ORIGINAL CONCEALMENT ARGUMENT DELIBERATELY.** It used to read: *a vortex unit
created at the end of Initial Orders would publish its deviated hex in every viewer's payload for
the whole of turn N — `shouldBeHidden` suppresses the icon on the client but the JSON still holds
the truth — so create it in `FireGamePhase::advance` instead and the real hex cannot leak, because
it has not been decided yet.* All of that is still true; it is simply no longer wanted. The hex is
now **published on purpose**, and nothing new leaks with it: the vortex row carries the same slot
and facing the republished `formingExits` entry already did. See [[arch_info_bleed_masking]].

⚠️⚠️ **ONLY THE SPAWN HALF MOVED — THE MANIFEST HALF STAYED AT THE END OF THE FIRING PHASE**, and
that split is load-bearing rather than tidy. `JumpEngine::stampExitManifests` asks *will this doorway
still be open next turn*, and `closeExpiredVortices` is what decides it:

- a **gate** on the last turn of its programmed hold closes at the end of this turn. Asked before
  that sweep, `$vortexCloseTurn` is still −1 and `holdsExitOpenOn` says yes — so a whole wave would
  be stamped for a turn its doorway does not exist on, get a Deployment phase with no legal hex, and
  stick there;
- a **ship's** one-shot exit closes at the end of the arrival turn. Asked before the closure, a rider
  that stayed behind would have its berth renewed instead of refunded.

`$opened` is therefore **rebuilt from the board** in the Firing phase rather than carried across
from the spawn — the doorway itself is the record, which is what `holdsExitOpenOn` reads, and which
makes the whole thing idempotent for free.

⚠️⚠️ **AND THE EXIT SWEEP RUNS ON `InitialOrdersGamePhase::advance`'s TWO EARLY RETURNS TOO.** The
existing note on `spawnDeclaredVortices` argues those returns cannot strand a declaration, because a
ship that just declared one is on the board, alive and not terrain — which is exactly what makes it
an active ship. **That argument does not hold for an exit**: its opener is in hyperspace and is on
nobody's initiative list, so a turn with nothing to activate would leave the declaration unresolved
and the wave stranded permanently. The call sits just inside each early return, *after* the
active-ship selection has already concluded there is nothing to activate — which is what keeps the
freshly inserted vortex out of `SimultaneousMovementRule::getNewActiveShip`.

⚠️⚠️ **AN EXIT MUST NOT ROLL FOR JUMP FAILURE, AND THAT USED TO FALL OUT FOR FREE.** Stage 6 recorded
that a damaged drive costs a reinforcement nothing, because `rollVortexJumpFailure` runs inside
`Criticals::setCriticals` — which used to be *before* the exit existed. Moving the spawn up put a
vortex in front of that roll, and a hyperspace opener can perfectly well carry pre-battle damage on
its Jump Engine, so the wave would have started being destroyed before it ever reached the map.
`rollVortexJumpFailure` now returns on a `SpawnJumpPointExit` held by a **ship**; a gate still rolls
on the turn it opens a jump point of either flavour, exactly as `getVortexClosureReason` splits them.

**A gate exit does not deviate**, so it keeps the existing end-of-Initial-Orders timing and the
existing `openSignalledGates` path. The two sweeps stay separate for the same reason
`spawnDeclaredVortices` and `openSignalledGates` do.

### 2.4 Arriving

- On turn N+1 every player with a unit whose arrival turn is N+1 gets a **Deployment phase**.
- Those units may be placed **only** in the hex of an open exit they are assigned to. They
  **stack** there freely — the one-ship-per-hex deployment block does not apply.
- Heading and facing are **forced** to the vortex facing. Speed is the player's to choose.
- **Placement is optional.** A player may bring some units through and leave the rest in hyperspace.
  An unplaced unit keeps its berth if the exit will still be open next turn (a gate), and
  otherwise goes back to unassigned and waits for another exit.

### 2.5 The deviation table

`S` = the opening unit's **sensor rating** = `EW::getScannerOutput($opener, $turn)`.

```
roll = Dice::d(20) + modifiers

modifiers   Minbari Federation ................ -1
            factionAge >= 3 (Ancient) ......... -5
            a friendly base or OSAT on the map  -3        (same team, undestroyed, on the board)
            a friendly ELINT vessel on the map  -1        (ElintScanner, undestroyed, on the board)

roll < 1              ->  0 hexes. Precise placement
roll 1..3             ->  1d3 hexes, random direction
roll 4..S             ->  1d6 hexes, random direction
S < roll < 2S         ->  1d10 hexes, random direction; then 1d6: 1-2 facing -60 deg, 5-6 facing +60 deg
roll >= 2S            ->  2d10+2 hexes, random direction, and a completely random facing
```

Random direction is `Dice::d(6) - 1`, walked with `OffsetCoordinate::moveToDirection($dir, $steps)`.

⭐ **−5 makes Ancients precise most of the time, and that is the intent — do not "fix" it later.**
A bare Ancient rolls 1–20 and subtracts 5, so **1–5 (25%) is a precise arrival** and 6–8 is 1d3
hexes; with a friendly base on the map that becomes **1–8 (40%) precise** and nothing worse than
1d6 until a 13. A Vorlon or Shadow force effectively arrives where it says it will. By contrast a
Young race with a sensor rating of 10 is precise only on a natural 1, scatters 1d6 on 4–10, and
starts shifting its facing from 11. Worth stating in the log line the sweep writes, so a player can
see which band they landed in rather than inferring it from the distance.

**The clamp.** If the scattered hex is illegal (§2.2), rotate the scatter direction alternately
left and right at the **same distance** until a legal hex is found; only if the whole ring fails,
reduce the distance by one and try again. Direction before distance, exactly as the tabletop says.
Distance 0 is always the last resort and the declared hex is legal by construction, so the search
always terminates.

⚠️ **`S` must be the blueprint sensor rating.** A hyperspace unit has no power allocation and no EW
entries, so `getScannerOutput` should return the scanner's raw output — **verify this explicitly**
(Stage 6 test). If it returns 0, every exit falls into the worst band and the feature is broken
in a way that looks like bad luck rather than a bug.

### 2.6 One-way

- A `SpawnJumpPointExit` never offers the Movement-phase **Jump Out** button and
  `Movement::applyJumpOut` refuses it.
- A `SpawnJumpPoint` (entrance) is never a legal arrival hex.
- Both rules are one `instanceof` each, on both sides.

---

## 3. Architecture

### 3.1 Where "this unit is in hyperspace" lives — three columns on `tac_ship`

```sql
-- db/reinforcements.sql
ALTER TABLE `tac_ship`
  ADD COLUMN `reinforcement` tinyint(1) NOT NULL DEFAULT 0,
  ADD COLUMN `arrivalturn`   int(11)    DEFAULT NULL,
  ADD COLUMN `arrivalvia`    int(11)    DEFAULT NULL;
```

| column | meaning |
|---|---|
| `reinforcement` | bought as a reinforcement. Fixed at purchase, never changes |
| `arrivalturn` | `NULL` = still in hyperspace. `N` = arrives and places on turn N |
| `arrivalvia` | the **opener unit's id** (a reinforcement ship, or a gate) this unit is riding through. `NULL` = unassigned |

**Why columns and not IndividualNotes.** Each value is written once and never revised, so there is
no history to reconstruct; `getTurnDeployed` sits on ~80 hot call sites and must not parse a note
list to answer; and POST-side ships carry no loaded notes at all
([[arch_post_side_ship_reconstruction]]), which is precisely where `getTurnPlaced` is called from
today. `slot` and `enhvalue` are the precedent.

**Why the opener id and not the vortex id.** The vortex does not exist yet when the manifest is
named — it is created two phases later, and for a gate it may never be created at all if the claim
is lost. Keying on the opener makes the refund automatic: a manifest that never gets a vortex is
simply never stamped.

⚠️ **THE TWO POSITIONAL INSERTS AT [DBManager.php:162](source/server/controller/DBManager.php#L162)
AND [:169](source/server/controller/DBManager.php#L169) BREAK THE MOMENT A COLUMN IS ADDED.** Both
are `INSERT INTO tac_ship VALUES(null, …)` with no column list. Convert them to named-column form
in the same commit as the migration, or every ship insert in the game fails with a column-count
error. Readers to extend: `getTacShips` and `getShipByIdFromDB` (both select an explicit list, so
they fail safe by simply not loading the new fields).

**On `BaseShip`:**

```php
public $reinforcement = false;   //bought as a reinforcement
public $arrivalTurn   = null;    //null while in hyperspace
public $arrivalVia    = null;    //opener unit id

public function isReinforcement(){ return $this->reinforcement && $this->arrivalTurn === null; }
```

⚠️ These are **public**, so they reach every ship's JSON — which the client needs. They are
per-instance, not blueprint, so the static blueprints and `ShipCompactor` are untouched. Confirm
with a `-Statics` regeneration and an empty diff ([[arch_shipcompactor_key_stripping]]).

### 3.2 The one load-bearing change — `getTurnDeployed`

```php
public function getTurnDeployed($gamedata){
    if ($this->osat || $this->base || $this->isTerrain()) return 1;

    $slot = $gamedata->getSlotById($this->slot);
    $depTurn = $slot->depavailable;

    //A reinforcement's arrival turn is decided IN PLAY, not by its slot. Null means it is still
    //in hyperspace, which reads here as "not on the board" - and that single sentence is what
    //makes it inert to firing, movement, EW, power, masking and the unavailable flag at once.
    if ($this->reinforcement) $depTurn = ($this->arrivalTurn === null) ? 999 : $this->arrivalTurn;

    if ($slot->surrendered !== null && $slot->surrendered <= $gamedata->turn) $depTurn = 999;

    return $depTurn;
}
```

**And `getTurnPlaced` must NOT subtract one for a reinforcement:**

```php
public function getTurnPlaced($gamedata){
    //A reinforcement places and arrives on the SAME turn: the early warning is the jump point
    //that formed last turn, not an early placement. Subtracting one here would grant it a
    //Deployment phase a turn before its vortex exists, with nowhere legal to stand.
    if ($this->reinforcement) return $this->getTurnDeployed($gamedata);
    $depTurn = $this->getTurnDeployed($gamedata);
    return ($depTurn > 1) ? ($depTurn - 1) : $depTurn;
}
```

Mirror both in [ships.js:1171](source/public/client/ships.js#L1171) / [:1220](source/public/client/ships.js#L1220).

⭐ **This is the whole reason the feature is tractable.** Turn-1 deployment, THE ORDER OF BATTLE'S
`getTurnDeployed <= turn` filter, `hasShipsToDeployThisTurn`, `validateAllDeployment`,
`markUnavailableSetMarkers`, `shouldBeHidden`, every firing and EW gate — all of them do the right
thing for a hyperspace unit with **no further change**.

⚠️ **THE FLEET LIST IS THE ONE LIST THIS DOES NOT COVER, and an earlier draft of this section
wrongly claimed it did.** `UI/fleetList.js` has no `getTurnDeployed` test anywhere in it — it shows a
fleet in FULL by design, which is how a late slot's ships appear under a `[Deploys on Turn N]` header
from turn 1. So a hyperspace unit sat in it as an ordinary row (user report, game 4315). Fixed in
Stage 0 by a fourth `setRowState` state, `hyperspace`, labelled **Hyperspace** in the same `#00b8e6`
as `.docked`. The row deliberately STAYS for the owner — they paid for those units and its points are
already in the fleet totals; it is the ENEMY's copy that must not show them, and §3.6 does that a
layer down by dropping the ship from the payload, so no row can be built in the first place.

⚠️ **999 is the existing surrender sentinel and is reused deliberately** — both mean "not on the
board". Before building, grep for `== 999` and `=== 999`: anything that reads it as *specifically*
"surrendered" needs a second condition.

### 3.3 The exit vortex unit

New [`source/server/model/ships/terrain/SpawnJumpPointExit.php`](source/server/model/ships/terrain/SpawnJumpPointExit.php):

```php
class SpawnJumpPointExit extends SpawnJumpPoint {
    //Everything is inherited. The CLASS is the discriminator - there is no note to parse and no
    //flag to keep in step, and it reaches the client for free in ship.phpclass.
}
```

⚠️ **The filename must match the class name.** `ShipLoader::getShipClassnamesStatic` enumerates
classes by stripping `.php` and calling `class_exists()`; a mismatch is skipped **silently** by the
static generator ([[arch_ship_hiding_variantof]]). This cost Stage 3 of the original plan a
debugging session.

Regenerate the autoload map and `Terrain.json` (`scripts/fvbuild.ps1 -Autoload -Statics`).

`spawnVortexUnit` gains a class parameter, defaulting to `SpawnJumpPoint` so both existing callers
are unchanged:

```php
protected function spawnVortexUnit($holder, OffsetCoordinate $hex, $facing, $gamedata,
                                   $class = 'SpawnJumpPoint')
```

Its own comment says it is shared rather than copied so that `$spawned = openTurn + 1` cannot drift
between the two kinds of vortex. That reasoning holds for a third kind, so it is shared three ways.

### 3.4 The declaration, and why it cannot be `targetHex`

The declaration is stored exactly as an entrance's is — a `FireOrder` on the unit's `JumpEngine`,
`type='ballistic'`, `firingmode = facing + 1`, `x`/`y` = the hex — with **`damageclass='jumpexit'`**
as the discriminator, mirroring `'jumppoint'`.

But it is **not built by `weaponManager.targetHex`**. That pipeline measures range from the
shooter's hex and runs `mathlib.isLoSBlocked` from it; the opener has no hex. It would reject every
legal exit, or divide by a position that does not exist. The exit gets a **bespoke map
click mode**, a sibling of `MineDeployment`, and builds its own order — the same relationship
`UI.gateSignal` has to `UI.vortexFacing`.

`Firing::getVortexDeclarationBlock` gets a third branch, taken first and returning, alongside the
gate branch that is already there for exactly this reason:

```
if ($fire->damageclass === 'jumpexit') return self::getExitDeclarationBlock(...);
```

Its list is short and almost none of it overlaps the ship list: the opener must be the submitting
player's, must be `reinforcement` with `arrivalTurn === null`, must mount an undestroyed
non-legacy Jump Engine, must not already have declared this turn, and the hex must be legal by
§2.2. **No range. No LoS. No offline test** — a unit in hyperspace has no power allocation to be
offline in.

### 3.5 The manifest

The client sets `ship.arrivalVia = <opener id>` locally on each chosen unit. `getShipsFromJSON`
whitelists **one** new field:

```php
$ship->arrivalVia = isset($value["arrivalVia"]) ? (int)$value["arrivalVia"] : null;
```

`InitialOrdersGamePhase::process` validates it against the server-side ships and persists with a
new `DBManager::setShipArrivalVia($shipid, $openerid)`. The rules: the unit must be the submitting
player's, must be in hyperspace, and `arrivalVia` must name a unit of theirs that declared a legal
`jumpexit` this turn (or is a gate they claimed). Anything else is set to `NULL`.

⚠️ **The client is never trusted with `arrivalturn`.** That is written only by the server, only in
the Stage 6 sweep, and never appears in the POST whitelist.

⚠️ `FireOrder::$notes` is **dropped on POST** ([[arch_fireorder_notes_dropped_on_post]]), which is
why the manifest travels on the ships and not on the order.

### 3.6 Concealment — a count and a point total

`TacGamedata::deleteHiddenData` gains `hideHyperspaceReinforcements()`:

- Skips the replay/`$all` path and a finished game (`self::$currentGameFinished`), matching how
  post-mortem disclosure already works.
- For every ship with `reinforcement && arrivalTurn === null` whose team is not the viewer's:
  **remove it from `$this->ships` entirely**, and add its point cost to a running total on its
  `PlayerSlot`.
- `PlayerSlot` gains `public $reinforcementCount = 0; public $reinforcementPoints = 0;` — populated
  only in the masked payload, so the owner's own copy shows the real ships and nothing else.

⚠️ **`$shipsById` is a private cache and `$this->ships` is a positional array.** Removing entries
must `array_values()` the list and rebuild or invalidate the cache — `doSortShips` is the model.

⭐ **A unit stops being concealed the moment it is assigned** (`arrivalturn` set at the end of the
formation turn), which is the same turn its jump point becomes a public blue marker. So the
disclosure is: *turn N — "three units are coming, 1250 points, somewhere near here"; turn N+1 — the
ships themselves.* That is deliberately a step **more** concealed than today's `[Deploys on Turn N]`
late slots, which show their full composition from turn 1.

### 3.7 Where the blue comes from

`#00b8e6` — `hexBlue` in [BallisticIconContainer.js:224](source/public/client/renderer/icon/BallisticIconContainer.js#L224),
`statusPending` in [theme.js:31](source/public/client/UI/reactJs/styled/theme.js#L31), and already
the colour of the existing reinforcement markers and the fleet list's `[Deploys on Turn N]`. It is
FV's established "not here yet" cyan; do not introduce a second blue. ⚠️ Read
[[project_visual_unification]] before adding any stylesheet or `<link>` — `styles/tokens.css` is
the only `:root` block.

The arrow asset is a **new** `img/directionOfVortexEntry.png`, not a rotation of the existing one:
the yellow asset must not change, and the three scale/opacity constants that are kept in step by
hand (`UI.vortexFacing.MARKER_ARROW_SCALE`, `BallisticIconContainer.VORTEX_ARROW_SCALE`,
`ShipIcon.FACING_ARROW_SCALE`) gain blue twins that must be kept in step with each other too.

---

## 4. Build stages

Each stage is independently testable in a fresh local game. **Do not start the next until the
previous is verified.** Stages 0–8 deploy together (§0).

### Stage 0 — the flag and the rule
Migration; named-column INSERTs; `getTacShips` / `getShipByIdFromDB`; `BaseShip` fields;
`getTurnDeployed` / `getTurnPlaced` both sides; `AllowReinforcementsRule` (mirror `AllowMinesRule`:
[creategame.php:178](source/public/creategame.php#L178), [createGame.js:184](source/public/client/UI/createGame.js#L184),
`GameRules::getAllowReinforcementsRules`, the model class, the `'REINF'` chip at
[DBManager.php:2283](source/server/controller/DBManager.php#L2283)).

**Verify:** set `reinforcement = 1` on one ship of a live local game by hand. It must vanish from
the map, the Order of Battle, the fleet list's deployable set and the turn-1 Deployment phase, and
the game must play on normally. Then run `replayHarness.php check` — a change to a serialized
property's visibility is exactly what that harness exists for ([[project_replay_harness]]).

### Stage 1 — the lobby ✅ BUILT 2026-08-27
A **Reinforcements** toggle in the buy panel; bought units carry the flag; the fleet list groups
them under a sub-header; `submitShip` writes the column; the no-jump-drive warning at Ready; the
"rule is off" strip that mirrors [gamelobby.js:4064](source/public/client/gamelobby.js#L4064).

⚠️ Lobby ship objects are `jQuery.extend` clones, so **every `instanceof` fails** and there is no
`window.staticShips` ([[arch_lobby_ship_objects]]). The flag must be a plain property.

**As built**, with three deviations worth stating:

- **A BUY MODE plus a per-row re-flag link, not a control in the buy dialog.** `#reinforcementModeToggle`
  in the store's filter strip flags everything bought while it is ticked (and lights the label cyan
  so it cannot be left on unnoticed); each fleet row then carries its own **Reinforce / Main Fleet**
  link, which changes one row's mind afterwards. Keeping the control out of the buy/edit dialogs keeps
  it out of `confirm.snapshotShip`, whose fixed field list would otherwise have to learn about it or
  silently restore the old value on a cancelled edit.
- **`gamedata.applyFleetGrouping()` writes and REMOVES both group headers**, and is called at the end
  of *both* row-writing paths. `constructFleetList` clears the list with `$(".ship.bought").remove()`
  — there is no `$("#fleet").empty()` anywhere — so a header it did not remove itself would survive
  every rebuild and accumulate once per poll, forever. The headers deliberately carry no `ship` class
  and no action-link class, because `#fleet`'s handlers are delegated by those and resolve their row
  with `closest(".ship")`.
- **The Ready warning can only see the player's own purchases, and says so.** A lobby client is served
  **no ships at all** — `prepareForPlayer` empties the list for a LOBBY game and `gamelobby.js`
  discards `serverdata.ships` anyway — so an ally's gate is invisible. Hence a warning the player
  confirms, never a refusal. The "does this hull mount a usable jump drive" test is
  `gamedata.hasVortexJumpEngine`: `name === 'jumpEngine' && ballistic && hextarget && range > 0`.
  ⚠️ **All three**, because `markLegacy()` clears `ballistic`/`hextarget` (and ShipCompactor strips a
  `false` key outright, so both read `undefined`) *and* zeroes `range`. `range > 0` alone lets through
  the nine engines in the stale uncompacted `Earth Alliance (Custom).json`, which carry none of the
  three keys; `ballistic && hextarget` alone lets through `JumpgateCapital`, whose `isGateJump()` is
  invisible to the client. Gates are excluded by the caller instead — a gate is exactly what makes a
  jump-drive-less group legal.

**Verify:** buy a mixed fleet, reload the lobby, confirm the flag survives and the points cap counts
reinforcements against the same pool. Then: **copy** a reinforcement (the copy must be one too),
**edit** one (it must stay one), **load a saved fleet** (the flags must come back — see Stage 1b —
and buying that class again afterwards must NOT be pre-flagged, which is the `standIn` trap), and turn
the rule off in a second game (no toggle, no link, no headers).

### Stage 1b — the header selector, MAIN FLEET, and saved-fleet memory ✅ BUILT 2026-08-28

Three refinements the user asked for after playing Stage 1, all in the lobby.

- **"FRONT LINE" is now "MAIN FLEET"**, in the group header and in the row link (`Front-line` →
  `Main Fleet`). Text only; no class, selector or stored value changed, so nothing had to migrate.
- **Both headers are written whenever the game carries the rule** — empty group or not, and on an
  empty fleet. They used to appear only once something was flagged, which is no longer possible: an
  EMPTY group's header is the click target that fills it, so hiding it would hide the control.
- **The two headers ARE the buy-target selector.** `gamedata.setBuyTarget` writes
  `#reinforcementModeToggle` and `applyFleetGrouping` reads it back to decide which header lights up,
  so the checkbox stays the single source of truth and `buyingReinforcement()` is unchanged. The
  header click is **delegated on `#fleet`** for the same reason the re-flag link is, only more so:
  `applyFleetGrouping` destroys and rewrites both headers on every poll, every purchase and every
  re-flag. A `change` handler on the checkbox itself repaints the highlight when the player uses the
  filter-strip control instead.
- **Saved fleets remember the flag** (§0, reversed). `tac_saved_ship.reinforcement`;
  `constructSavedShips` emits the key **only when true** (so an ordinary fleet's payload is
  byte-identical to before, and an older client simply saves everything front-line);
  `getSavedShipsFromJSON` reads it, `submitSavedShip` writes it, `getSavedShips` reads it back onto
  the ship — where it rides `loadSavedFleet.php`'s `json_encode` for free, `$reinforcement` being an
  ordinary public `BaseShip` property.

  ⚠️ **`$reinforcement`, not `$reinforcementClaim`, and that is safe on this path only.** The claim
  exists because a POST-side ship from `getShipsFromJSON` is put into a live `TacGamedata`, where a
  bare flag makes both turn accessors answer 999 in every phase (trap 14). Saved-fleet ships never
  see a `TacGamedata`: they are built, sanitised, written and thrown away, and both accessors need a
  `$gamedata` argument nothing on that path can supply.

  ⚠️ `groupSaveableShips` now keys its mine merge on **class + flag**. The group takes the first
  member's flag, so merging hyperspace and front-line mines of one class would silently re-flag half
  of them on reload.

  ⚠️ The lobby restore is **gated on the rule** (`gamedata.reinforcementsAllowed()`), so a fleet
  saved from a game that had it loads entirely into the main fleet in one that does not.

**Verify:** save a mixed fleet, load it into another Allow-Reinforcements game (flags come back,
groups correct, points identical), then load the same fleet into a game without the rule (everything
in the main fleet, no headers, no links).

#### Stage 1b refinement — the headers read as BANDS ✅ 2026-08-28, untested in play

MAIN FLEET "blends too well into the wider container" (user report). `#fleet` is one flat
`--fv-well` slab and the header was a 13px caption over a hairline — near the visual weight of the
ship rows it was supposed to be heading. Three changes, and **no new colour**: a gradient washing
the group's own colour in behind the title and fading out to the left (`#fleet` is right-aligned, so
the substance belongs where the eye is), a solid 3px right border flush against the text, and more
size, letter-spacing and separation.

⚠️ **The band is a `background-IMAGE`, and that is what keeps the buy-target state machine working.**
Hover and selected paint `background-COLOR`; the gradient composites over it, so the two layers add
up instead of overwriting each other. Painting the band as a colour would have meant restating it in
all four state rules. Both state tints were raised (0.10/0.16 → 0.14/0.22) because against a header
carrying 0.20 of its own colour the old ones stopped reading as a change of state, and `selected`
gained a fatter accent bar so it still answers "where does the next purchase land?" three ways over.

⚠️ **No `text-transform`.** The titles are already uppercase in the markup, and
`.fleet-group-note` is a sentence living INSIDE the element — it would be shouted at.

### Stage 2 — concealment ✅ BUILT 2026-08-27
`hideHyperspaceReinforcements` + the slot aggregate + the fleet-list placeholder row.

**As built.** The sweep is the FIRST statement of `deleteHiddenData`, so every later mask
(`hideDeploymentDocks`, `hideActiveShipMovement`, `hideEnemyCombatPivots`, the fire-order sweep,
`hideStealthShipMovement`) walks the shortened list and cannot write to a ship about to vanish —
and living inside `deleteHiddenData` inherits the `$all` skip for free. Three things §3.6 did not
spell out:

- **The predicate is `isReinforcement()` AND `getTurnDeployed() > turn`, not the first alone.**
  `getTurnDeployed` returns **1** for an OSAT, a base or terrain *before* it reaches the
  reinforcement branch, so such a row carrying the flag is on the board on turn 1 while
  `isReinforcement()` still answers true. Masking it would delete a visible unit — and change what
  `getMinTurnDeployedSlot` tells `Manager::updateLateDeployments`, which **writes to the database**.
- **`$this->ships` is replaced wholesale with an appended list, not `unset()` from.** `stripForJson`
  maps it with `array_map`, which **preserves keys** given a single array, so a gap makes
  `json_encode` emit a JSON *object* where the client requires a real array. `$shipsById` is then
  dropped entirely: it is populated for **every** ship in the game long before this runs, because
  `getMovesForShips` resolves every id during the DB load.
- **The points go into the header total.** The owner's own copy already counts these units (the
  `shipArray` loop has no deploy-turn filter, by design), so leaving them out of the enemy's copy
  would make the two players' headers disagree about the same fleet — a louder tell than the number.
  Same arithmetic as every other row: `pointCost` (×`flightSize/6` for a flight) `+ pointCostEnh
  + pointCostEnh2`. ⚠️ **Never `pointCostSysEnh`** — `tac_ship.enhvalue` already holds all three
  buckets and `getTacShips` reads it into `pointCostEnh`, so adding it double-counts.
  ⚠️ A reinforcement **MINE** would be under-counted: `fleetList.js` prices mines with a fleet-wide
  100pt premium and 10% per extra class, which this deliberately does not reproduce. Buying a mine
  as a reinforcement is a nonsense purchase (it cannot arrive through a vortex it has no drive to
  reach) but nothing forbids it yet.

**Two residual leaks §3.6 did not list, both closed here, both outside `deleteHiddenData` because
they are computed ONCE in `onConstructed` and are not per-viewer:**

- **`setBlockedHexes`** would plant a phantom line-of-sight blocker for an **Enormous** reinforcement
  at its slot's deployment-box centre — its only movement row is the `'start'` one every ship is
  given — for *every* player including its owner, and it would survive the masking sweep. Now skips
  `isReinforcement()`. ⚠️ Keyed on that specifically, **not** on a general `getTurnDeployed` test: a
  late-SLOT Enormous unit has blocked its box's hex since long before this feature, and changing
  that is a LoS rule change for existing games rather than a concealment fix.
- **`isStealthPresent` / `areMinesPresent`** are one-bit broadcasts to every viewer ("somebody has a
  cloak", "enemy mines are out there"). A hyperspace unit must not set either — the flag would
  outlive the ship the sweep deletes a moment later. Now gated on `!isReinforcement()`.

**Checked and clean, recorded so nobody re-derives it:** `Manager::updateLateDeployments` runs
*after* `prepareForPlayer` and **writes to the database** off the per-viewer masked list — the first
time in FV that a masked ship list drives a persistent write. It is unaffected, because
`getMinTurnDeployedSlot` only reads the list to look for terrain/OSATs/bases, and the predicate keeps
every one of those. Every `getShipById` that runs after the sweep (`hideDeploymentDocks`,
`markJumpedDockedFlights`, `hideSystemFireOrders`, the gate-signal mask, the attached-mirror mask) is
already null-safe, which matters because clearing `$shipsById` turns a formerly-cached hit into a
`null`.

⚠️ **KNOWN GAP, deferred to Stage 4:** the React ship window shows **no banner** for a unit in
hyperspace. `ShipWindow.js`'s "Deploying on Turn N" banner is gated `deployTurn < 999`, a guard
written for the surrender sentinel that now also swallows this case — so an owner opening a
hyperspace unit's window sees an ordinary-looking sheet. The fleet list does label it (Stage 0), so
this is a second-order gap; fixing it means a React source edit and a `UI.bundle.js` rebuild
([[howto_verify_react_bundle]]), which is Stage 4's territory.

**Verify from BOTH seats, by hand.** The harness's `masking` check does not cover this rule
([[arch_placement_turn_vs_deploy_turn]]), so this stage has no regression net. Check the raw JSON,
not just the rendered list — the whole point is that the ship object is *absent*, not hidden.

**Already proven against real local games (in memory, no DB writes — flag a ship on the object
returned by `DBManager::getTacGamedata`, then call `prepareForPlayer()` yourself, the way the replay
harness does).** On game 3671 (a 850pt Vorlon Battle Destroyer) and game 4311 (a Gorith flight), for
owner / enemy / observer / replay: owner's list unchanged and both slot fields 0; enemy's list
exactly one ship shorter with the id **absent from the encoded JSON**, `"ships":[` still an array,
and the slot reporting 1 unit at the right cost; observer (team `null`) masked like an enemy; the
`$all` replay path unmasked. ⚠️ **The flight test needed forcing** — every flight in the corpus is
six craft, so the `flightSize / 6` branch multiplies by exactly 1 and proves nothing left alone
(the [[arch_blueprint_cache]] self-test trap). Forced to 3 it gives 120 and to 1 it gives 40, with
enhancements added on top, so the branch really divides.

### Stage 3 — the exit vortex unit ✅ BUILT 2026-08-27
`SpawnJumpPointExit`; autoload + statics; the one-way `instanceof` guards in
`Movement::applyJumpOut` and the movement-phase tooltip; the blue outward `$facingArrow` on
`ShipIcon`.

**As built.** Four one-way guards rather than the two §2.6 counted, and they are cheap:
`Movement::getOpenVortexInHex` (what the client's `getVortexInHex` mirrors — the Jump Out button
never appears), `Movement::getJumpOutVortex` (every jump-out path funnels through it, so a forged
order naming an exit by id is refused), `Firing::getVortexDeclarationBlock`'s maintain branch
(an exit is one-shot and has no Maintain), and `JumpEngine::getVortexClosureReason` — which is
**trap 5**, and goes *before* the gate branch because exit-ness belongs to the vortex while
gate-ness belongs to the engine, and the one-shot rule must win.

`spawnVortexUnit` took the `$class = 'SpawnJumpPoint'` parameter as designed; the name stays the
literal `"Jump Point"` there and the subclass constructor overwrites it, so there is no second
string to keep in step.

⚠️ **The two vortex ART assets are named the other way round from what you expect**, and both
predate this feature: `img/ships/JumpPointEntrance.png` is YELLOW and is worn by the **entrance**;
`img/ships/JumpPointExit.png` is BLUE and is worn by the **exit**. The colour carries the
meaning; do not rename the files ([[arch_image_cache_busting]]).

**Deviation from §3.7 — no blue twins of the three arrow constants.** `img/directionOfVortexEntry.png`
is the yellow asset mirrored **within its own alpha bounding box** (411,197)–(511,314) and recoloured
`#ffd12b→#00b8e6` / `#7b6415→#005870`. It therefore occupies exactly the same 101×118 pixels of the
same 512×512 canvas, so it shares `ShipIcon.FACING_ARROW_SCALE` with the yellow one and Stage 4 can
share the other two. Three more numbers kept in step by eye is precisely trap 7; identical geometry
makes them unnecessary. (Mirroring the *whole canvas* would be wrong — it puts the arrow on the
opposite side of the hex.)

**Verify:** spawn one by hand (or temporarily point an entrance declaration at the new class). It must
render blue with an outward arrow, sit *behind* units standing in its hex, stay out of the hex-stack
picker, refuse a Jump Out, and — the trap-5 test — **close at the end of the turn after it opened**,
freeing its opener's engine.

### Stage 4 — the Manage Reinforcements client flow ✅ BUILT 2026-08-27, reworked 2026-08-28
The `#iniGui` button; the opener menu; the bespoke hex click mode with the legal-hex test; blue
`UI.vortexFacing`; the manifest dialog; the blue "Jump Point Forming" marker and reversed arrow in
`BallisticIconContainer`.

⚠️ `window.UI` is **created** by `shipMovement.js`, so any new module must load after it in
`game.php` or the assignment wipes it — the hazard both `vortexFacing.js` and `gateSignal.js`
document at the top of themselves.

**As built** — `client/renderer/phaseStrategy/ReinforcementEntry.js`, a sibling of
`MineDeployment.js`. It only *calls* `UI.vortexFacing` and never assigns `window.UI`, so it has no
ordering constraint of its own. Four things worth recording:

- ⚠️⚠️ **THE MODE HOLDS A SHIP ID, NEVER A SHIP OBJECT, and this is not a style preference.**
  `gamedata.setShipsFromJson` REPLACES every entry of `gamedata.ships` with a fresh `new Ship(...)`
  on each poll that carries ship data — and the mode is armed across exactly the window in which a
  poll lands (the player is looking at the map choosing a hex; the facing control is open while they
  turn the doorway). Pushing the order onto a captured `engine` would push it onto a discarded copy:
  **the declaration would silently not happen**, with no error anywhere. The `onConfirm` closure
  re-resolves through `gamedata.getShip(id)` at the moment of the tick for the same reason.
- **The click is intercepted at `onClickEvent`, not `onHexClicked`.** `onHexClicked` is only reached
  when the click landed on *no* icon — but a hex holding a ship is a perfectly legal exit hex
  (§2.2 forbids terrain, gates, vortices and Enormous units and nothing else) and a wave arriving on
  top of somebody is the ordinary case. Hooking the later method would have silently refused every
  occupied hex.
- ⚠️ **An exit order takes NO part in the ballistic icon or line pipeline.** It is collected into
  its own list and drawn by `generateExitHexes`. `createBallisticIcon` opens with
  `if (!shooterIcon) return;` and the shooter is in hyperspace, so it would drop the marker outright
  — and if the opener ever *did* have an icon, the launch sprite and the ballistic line would be
  drawn from its `'start'` row at the deployment-box centre: a bright line, on the map, from a ship
  that is not there. Trap 10 turns out to be bigger than `targetid`.
- **The legal-hex test reuses `weaponManager.getVortexHexBlock`** — the same sweep an entrance uses, so
  terrain footprints, gates and existing vortices are all caught for free — plus a map-bounds test
  the entrance path never needed. There is **no range and no LoS test**, by rule.

**Verify:** the order reaches `tac_fireorder` with `damageclass='jumpexit'`, the right `x`/`y` and
`firingmode`, and the manifest reaches `tac_ship.arrivalvia`. Discarding the control leaves nothing
behind.

#### Stage 4b — one menu, and the stranding warning ✅ BUILT 2026-08-28

⭐ **THE BUTTON'S "HAS AN ORDER" STATE WAS A DEAD END, and that is the whole of this rework.**
`buttonLabel()` flipped the `#iniGui` button to **Withdraw Jump Point** the instant any declaration
stood, and `onButtonClicked` routed to `chooseWithdraw()` — so a fleet with three jump-capable
hulls could open **exactly one doorway per turn**, and the only way to reach the second was to take
the first one back. Reported 2026-08-28.

The button now reads **Manage Reinforcements** (or **Cancel Jump Point** while the map is armed) and
opens **one menu listing every jump-capable unit still in hyperspace**, declared or not:

| Row state | Marked with | Primary button becomes |
|---|---|---|
| No declaration | its ship class | **Choose Hex** → arms the map |
| Already holding one | `OPENING` tag, cyan row tint, `hex q,r — N units` | **Withdraw Jump Point** |
| Already riding someone else's | `RIDING` tag, greyed, `riding <name>`, radio **disabled** | *not selectable* |
| No jump drive at all (a passenger) | listed in a second group below the drives, greyed, radio in its OWN group | *not selectable* |

**Both tags share a left edge** (user request 2026-08-28), and it is a CSS-only pair in
`tactical.css`: `order: 1` anchors the tag past `.reinforcementRowClass` to the row's right edge,
and a common `min-width` makes the two badges the same width. Neither works alone, and neither is
tidying — the row packs `[radio][name grows][tag][detail]`, so a tag left to sit before the detail
measures its position from free text (`hex q,r — N units` on one row, `riding <name>` on the next)
and no two rows agree. The full reasoning is on the rule itself.

**THE MENU LISTS THE WHOLE WAVE, IN TWO GROUPS** (user request 2026-08-28). The jump-capable units
above, everything else — a reinforcement with **no drive of its own** — underneath, greyed, under a
`No jump drive — they arrive as passengers` heading. They were invisible here before, which made the
menu quietly misreport the fleet: a player looking at two hulls had no way to see the four fighters
waiting behind them, and the **stranding warning at commit time was the first mention they ever
got**. They are listed for awareness, never for choice, and each says which state it is in in the
same words the group above uses (`RIDING` + `riding <name>`, or its class).

- ⭐ **A DIFFERENT RADIO GROUP, not merely `disabled`.** The OK handler reads
  `input[name='reinforcementOpener']:checked`, and a passenger must never be able to answer that
  question whatever a browser does with a disabled control — so `passengerRowsHtml` names them
  `reinforcementPassenger` and they are not in that group at all. They keep a radio rather than
  dropping it because the row is a flex line that measures from the control; without one every name
  in the second group would hang a control-width left of the names above it.
- The heading appears only when both groups exist, and `pickIndex` is decided over the
  jump-capable rows **before** the passenger block is appended, so it can never shift the selection.

**AND SELECTING A DECLARED ROW HIGHLIGHTS ITS OWN JUMP POINT ON THE MAP** (user request
2026-08-28). Three drives put three identical blue "Jump Point Forming" markers out there, and a row
reading `hex 4,-2 — 3 units` could not tell you which of them belonged to the unit you were about to
withdraw. The selected opener's marker now draws with **its name in the hex, in white, arrow at full
opacity**; every other marker keeps the generic blue.

- `ReinforcementEntry.highlight()` holds an **opener id** (never a ship object — every poll replaces
  every entry of `gamedata.ships`) and `BallisticIconContainer.generateExitHexes` asks
  `getHighlightedOpener()` as it draws, matching on the ORDER's `shooterid`. The republished enemy
  entries carry no shooter and so can never be highlighted, which is right: their menu is not open
  and the units are hidden from them anyway.
- The label goes into the sprite's **`syncSceneObject` signature**, so exactly the two hexes whose
  state changed are rebuilt and every other marker's texture is left alone.
- ⚠️ **THE REDRAW EVENT CARRIES NO SHOOTER.** Firing `HexTargeted` is how this asks for the ballistic
  icons to be rebuilt (the same event `withdraw()` uses), but `PhaseStrategy.onHexTargeted` then
  compares `payload.shooter` with the **selected** ship — and `selectedShip` is `null` when nothing
  is selected, so a `shooter: null` would rebuild the weapon list for a ship that does not exist. An
  absent property matches neither a null nor a ship.
- It clears itself on every entrance: `deactivate()` (above its early return, so the Choose Hex path is
  covered), the Cancel button, and a selection moving to a row with no declaration. `syncLabel` owns
  the whole decision, so a withdrawal drops the highlight in the same breath as the `OPENING` tag.

**The greyed state** (`ridingWith`, user request 2026-08-28). A jump-capable unit that is on
another ship's manifest is spoken for: opening a second doorway with it would have its drive
holding one exit open while it arrives through a different one, and the declaration and the
manifest would disagree. The menu refuses the choice rather than letting it be made and then
unpicked.

- ⚠️ **`arrivalVia == its OWN id` is not riding with anybody** — that is what `createExitOrder`
  stamps on an opener, because a drive always comes through its own doorway. Without that line
  every opener would grey *itself* out the instant it declared and **Withdraw would be
  unreachable**.
- Nothing has to un-grey a row by hand: `withdraw()` clears the whole manifest it carried, so the
  next render simply finds no standing declaration to ride.
- ⚠️ **A greyed row is never pre-checked.** Its radio is `disabled`, so a player who wanted a
  different unit could not move the selection off it — the dialog would be stuck. `openerRowsHtml`
  picks the first *selectable* row, or the one it was asked to keep if that is still selectable.

**Withdrawing keeps the window open** (user request 2026-08-28). It used to close the whole
dialog, which made "move my jump point" three gestures — withdraw, reopen the menu, find the unit
again — and hid the one thing the withdrawal had just changed: the passengers it freed. The list is
now **re-rendered in place** (`render(keepSelectedId)`): the row loses its `OPENING` mark, the
button reverts to **Choose Hex**, anything the withdrawal un-booked stops being grey, and the same
unit stays selected, so re-placing it is the very next click. Declaring still closes the dialog —
the map has to be visible for the hex click.

- ⚠️ **The `change` handler is DELEGATED on the dialog root and bound once.** `render()` replaces
  every row, so a handler bound to the inputs themselves would die with the markup it was attached
  to — and the label would stop following the selection after the first withdrawal.
- `syncLabel` tolerates nothing being checked (`.attr()` on an empty set is `undefined`, not a
  throw) and the OK handler returns early on it, because in principle every row can be greyed.

- **The primary button's label follows the radio selection**, re-synced on `change` *and* once up
  front (the first row is pre-checked and may already be a declared unit). `fleetDialogShell` renders
  that label from `data-label` through `content: attr(data-label)`, so setting the attribute is all
  it takes; the button's own width is `auto` with a `min-width`, so the longer label fits.
- ⚠️ **`data-declared` on the row is for the LABEL ONLY.** The click re-resolves the ship through
  `gamedata.getShip` and re-reads `declarationOn` from the live object, because a poll can land while
  the dialog is open — the same hazard the mode's ship-id rule exists for.
- **The single-candidate shortcut survives, but only one way round.** One candidate with nothing
  declared still goes straight to the map. One candidate that *is* declared always shows the menu:
  the only thing it could do is withdraw, and withdrawing a jump point on an unconfirmed button click
  is not something a player should be able to trip over.
- `availableOpeners()` (which filtered declared units out) became `openerCandidates()` (which does
  not); `chooseOpener` and `chooseWithdraw` are gone, replaced by `manageReinforcements`.

⚠️ **Still deliberately not offered: re-editing a STANDING exit's manifest.** A unit already
assigned to exit A is not listed in exit B's manifest dialog, and the menu does not reopen
A's — moving it means withdrawing A. That refusal predates this rework and is documented on
`showManifestDialog` ("silently moving it would undo a choice the player has already made"), but
multi-exit turns make it far easier to reach, so it is a candidate if it starts to bite.

**The stranding warning** (also user-reported 2026-08-28). A reinforcement with no jump drive of its
own only ever arrives as a passenger. If the unit that opened this turn's doorway leaves it off the
manifest and jumps in alone, **nobody able to open the next one is left in hyperspace** — those units
are paid for and unusable for the rest of the battle, and nothing said so until the phase after the
commit, by which time the order could not be taken back.

`ReinforcementEntry.strandedByCommit()` owns the whole test and
`gamedata.onCommitClicked`'s phase-1 block only renders it, beside the `vortexClosing` warning it is
the exact sibling of. It is narrow on purpose and never cries wolf:

| Situation | Says |
|---|---|
| Nothing departing this turn | *nothing* — the player can still call everybody in later |
| Somebody able to open the next doorway stays behind | *nothing* — keeping a drive in reserve is a plan, not a mistake |
| Otherwise | names the units that can never be called in |

- ⚠️ **`ridingOut` tests the ORDER, not `arrivalVia`.** Being on a manifest is not enough — the
  exit it names has to still exist, and a declaration can be replaced as well as withdrawn.
- ⚠️ **It reads the module, never `myShips`.** That list drops everything whose `getTurnDeployed` is
  later than this turn, and a unit in hyperspace answers 999.
- ✅ **It knows about jump gates from Stage 8**, which is the change this entry asked for in advance:
  `anyLiveGate()` silences the whole warning while any undestroyed `JumpgateCapital` is on the map,
  because a gate can be signalled by anybody on any later turn and so nothing is stranded *for good*.
  The gate test is deliberately **loose** — "a live gate exists", not "I could signal it right now" —
  since charge, signal range and a rival claim all move between turns and the question here is about
  the whole rest of the battle. Loose errs towards silence, which is the right direction for a nag.

**Already proven headless** (`vm`-evaluated against a stubbed client world, which is the
[[howto_verify_react_bundle]] "bundle and evaluate" discipline — a parse check would not catch a
missing global or a wrong predicate): eligibility across seven cases; the legacy / stale-blueprint /
fixed-gate drive tests all refuse; the off-map bound accepted at q=21 and refused at q=22 of a 42x30
map; an obstructed hex refused; right-click cancels; **and the mode still works after a simulated
poll has replaced every ship object.** The order it builds carries `damageclass='jumpexit'`,
`type='ballistic'`, `firingMode = facing+1`, the chosen x/y, and **`targetid = -1`**.

### Stage 5 — server-side declaration validation ✅ COMPLETE 2026-08-28 (built 2026-08-27)
`getExitDeclarationBlock`; the `arrivalVia` whitelist + validation + `setShipArrivalVia`.

✅ **THE STAGE-3 HAZARD IS NOW FIXED, BOTH HALVES.** `JumpEngine::getVortexDeclaration` did not
filter `damageclass` and would have accepted an exit order as an entrance declaration — so
`spawnDeclaredVortices` would have put a **yellow entrance vortex at the exit hex** at the end of
Initial Orders, and `hasOpenVortex` would then have made Stage 6's own sweep return `null` at
`spawnVortexUnit`'s first line. Both guards are in, deliberately redundant: the declaration reader
skips a `'jumpexit'` order, and the sweep skips `isReinforcement()` units outright. Either alone
would do; both together mean a future order shape cannot reintroduce it by accident.

⚠️ Still unfixed and deliberately so: **`getMaintainDeclaration` cannot see the vortex class** (it
takes only `$turn` and has no gamedata to resolve `activeVortexId` with), so the exit's
no-Maintain rule is enforced at `Firing::getVortexDeclarationBlock` and again in
`getVortexClosureReason`, which returns before the maintain test is ever reached. That is two gates;
a third would need a signature change for no new coverage.

**As built**, with three things §3.5 did not anticipate:

- ⭐⭐ **THE ENEMY'S BLUE MARKER NEEDED A CHANNEL OF ITS OWN, and §2.3 and §3.6 did not reconcile.**
  §2.3 says the forming marker is what an opponent sees on turn N, and §3.6's ⭐ says the disclosure
  is *"three units are coming, 1250 points, **somewhere near here**"* — but §3.6 also **deletes the
  declaring ship from the enemy's payload, orders and all**, so there is nothing left to draw the
  marker from. `PlayerSlot` therefore gained `formingExits` — a list of `{x, y, facing}` and
  **nothing else**, never the opener, never the manifest — filled by the same sweep that removes the
  ship. The owner draws the identical marker from its own fire order; `generateExitHexes` folds
  both sources into one drawing. ⚠️ **Never published in phase 1**: a declaration is secret while
  Initial Orders are open, which is the rule `hideSystemFireOrders` already enforces on the order.
- **The rule check in `persistManifest` is an efficiency guard, not only a correctness one.** The
  sweep needs a fresh gamedata load to answer anything, and that would be a **third** full load on
  every Initial Orders commit of every game in the system. `hasRuleName('allowReinforcements')`
  first means every game without the rule pays nothing at all.
- **The map-bounds test is new** — the entrance path never needed one, because a 4-hex projection range
  from a ship on the board cannot reach off-map. `-1x-1` ("unlimited") is **not** unbounded: it is
  the 60x40 default `BuyingGamePhase::getGamespace` substitutes, and both ends use that.

**Verify:** a tampered POST (an opener that is not yours, one that is already on the board, an
illegal hex) is rejected and logged, and never reaches the DB.

**Already proven against three real local games** (3671, 4277 with 59 terrain rows, 4256), driving
`Firing::validateFireOrders` directly with hand-built orders and in-memory reinforcement flags, no
DB writes: the happy path and all six facings accepted; **refused** for a non-reinforcement, a
reinforcement that already has an arrival turn, a submission by a player who does not own the unit,
facing modes 0 and 7, an off-map hex, a missing hex, and a destroyed jump engine; a hex holding a
moon and one holding an asteroid field both refused; **a hex far beyond the engine's projection
range accepted**, which is the rule §2.2 states as an absence; the map bound accepted at q=21 and
refused at q=22; a second declaration in one submission dropped while the first survives; and
`getVortexDeclaration` ignoring a `'jumpexit'` order.

⚠️ **One harness lesson worth keeping:** the first version of that test picked its "free hex" by
looking for a hex with no ship *centre* in it, and every case passed on a game with no terrain while
the happy path FAILED on one with moons. Terrain occupies a whole **footprint**
(`RammingAttack::getTerrainOccupiedHexes`), so a hex with no centre in it can still be solid rock. A
test that cannot tell "correctly refused" from "wrongly refused" is testing nothing.

### Stage 6 — the exit forms, and it deviates ✅ COMPLETE 2026-08-28

`JumpEngine::spawnExitVortices($servergamedata, $dbManager)` in `FireGamePhase::advance`, after
`closeExpiredVortices` and **before** the slot loop. Per declaration: roll the deviation, clamp to
a legal hex, `spawnVortexUnit(..., 'SpawnJumpPointExit')`, stamp `arrivalturn = turn + 1` on
every ship whose `arrivalvia` matches, clear `arrivalvia` on any manifest whose vortex did not
open, write the log order and the additive `'VortexScatter'` note (`"<hexes>,<facingSteps>"`).

⚠️ Runs off a real `getTacGamedata` load, never off POST-side ships. ⚠️ Do not branch on
`$gamedata->phase` — `advance()` has already set the next phase. Both traps are documented on the two
sibling sweeps.

**As built**, with six things §2.5 and §4 did not anticipate:

- ⭐⭐ **A NEW NOTE KIND IS A SILENT BUG UNLESS `onIndividualNotesLoaded` CLAIMS IT.** The
  JumpEngine's loader ends in a fall-through that treats **any** note it does not recognise as the
  legacy `jumped` note and assigns its value to `$preJumpValue` — so an unclaimed `'VortexScatter'`
  would have quietly overwritten the pre-jump combat value of the very ship that opened the
  exit. `'Vortex'` and `'VortexHold'` are each `continue`d for exactly this reason and it does
  not read as a hazard until you add a fourth. The scatter now has its own branch, its own map, and
  its own restore (`restoreVortexScatter`, run **after** `restoreVortexState` because which vortex
  is the current one is settled in there — the same ordering the hold note needs).
- ⚠️⚠️ **`OffsetCoordinate::distanceTo` RETURNS A FLOAT.** `CubeCoordinate::__construct` runs every
  ordinate through `round(..., 4)` and `round()` returns a float in PHP, so `distanceTo` answers
  `3.0` and **`$d === 3` is false**. Nothing in this stage's shipped code compares one strictly (the
  clamp returns its own int), but two assertions written the obvious way failed on it, and any
  Stage 7/9 rule of the shape "arrived in the vortex's hex" is one `===` away from the same bug.
  Compare with `==`, or cast.
- **The deviation table had to be split from the roll to be testable.** `rollExitDeviation`
  reads the sensor rating and the modifiers and then calls `rollExitScatter($roll, $sensors)`,
  which is the whole table and nothing else. Everything that can be *wrong* is in the second half,
  and a band that is one comparison out looks exactly like bad luck in play — so the test drives
  every boundary pair directly rather than rolling d20s and hoping.
- **The hex-legality test is now SHARED with the submit path** (`JumpEngine::getExitHexBlock`,
  called by `Firing::getExitDeclarationBlock`), and it has to be: the clamp asks the identical
  question of every hex the scatter could land on. If the two ever disagreed, either a declaration
  would be accepted onto a hex the clamp then refuses to use, or the clamp would put a doorway
  somewhere the declaration rules forbid.
- **An exit never rolls for jump failure, and that falls out for free** —
  `rollVortexJumpFailure` runs in `criticalPhaseEffects` (inside `setCriticals`, i.e. *before* this
  sweep) and needs `hasOpenVortex` plus either `vortexOpenTurn == turn` or a Maintain declaration.
  On turn N the exit does not exist yet; on N+1 it was opened on N and has no Maintain. So a
  damaged drive costs a reinforcement nothing. Deliberate — do not "fix" it into existence.
- **The freshly spawned exit blocks its own hex immediately.** It is Terrain and
  `Manager::insertSingleShip` puts it in `$gamedata->ships` at once, so a *second* exit formed
  later in the same sweep clamps around the first for free. ⚠️ The one hole is the **distance-0 last
  resort, which is unconditional** (§2.5): two openers that declared the same hex and both rolled a
  precise arrival get two doorways in one hex. Accepted — the alternative is refusing to open, which
  strands a paid-for wave in hyperspace for the rest of the game, and units arriving through either
  vortex stack there by design anyway.

⚠️ **IDEMPOTENCY IS TWO RULES, NOT ONE.** `hasOpenVortex` stops the second `advance()` re-spawning,
but the manifest half needed its own: a second pass must record an **already-open** exit as
*opened*, or the "berth that never formed" branch would clear the whole wave's `arrivalvia` on the
very units it had just admitted. A stamped unit stops answering `isReinforcement()`, which is what
makes the stamping half idempotent by itself.

**Verify:** the sensor rating is non-zero for a hyperspace unit (§2.5); each band scatters the
right distance and the log line **names the band it landed in**; the clamp never lands on terrain,
a gate, another vortex or off-map; and — the concealment test — the enemy's turn-N payload contains
**no** exit unit at all.

**Proven headless against three real local games** (4277 with its 59 terrain rows, 4311, 4307),
driving the sweep with `Manager::$dbManager` swapped for a stub through reflection, so the whole
spawn path — `insertSingleShip`, the deploy `MovementOrder`, all three notes — runs with **zero
database writes**. 177 assertions, all passing:

- **The sensor rating in hyperspace is 14 / 8 / 10 on the three openers**, never 0 — §2.5's headline
  worry, and the one failure mode that would have looked like bad luck rather than a bug.
- Every band boundary: `roll < 1`, 1–3, 4..S, S<roll<2S, roll>=2S, the empty `4..S` on a low-sensor
  ship falling through to the 1d10 band, and the S=0 degenerate case landing everything in the worst
  band. Each band's distance stays inside its own dice range over 400 iterations, the direction is
  always 0–5, and the 1d10 band produces −60°, 0 and +60° shifts.
- The clamp returns a legal hex over 72 direction/distance pairs; **a scatter aimed straight at a
  terrain footprint hex rotates off it at the same distance in all 14–18 cases per game**; a 5-hex
  scatter off the east edge rotates rather than shortening; the reported distance is always the real
  one; distance 0 stays on the declared hex.
- The unit created is a `SpawnJumpPointExit` whose **`phpclass` is the subclass**, `spawned` is
  `turn + 1` (so it is hidden on the forming turn), its id is an `int` and it carries the opener's
  team; the deploy movement row is written; the `'Vortex'` and `'VortexScatter'` notes are written,
  keyed by the vortex id, stamped turn 1 / phase 1, and the scatter note's two fields agree with the
  hex and facing the vortex actually got.
- The opener **and** its manifest are stamped `turn + 1` in the database *and* in memory (so the slot
  loop that follows sees it), the opener stops answering `isReinforcement()`, and `getTurnDeployed`
  switches from 999 to the arrival turn; **a berth on an exit that never formed is cleared, not
  stamped**, and that unit keeps its reinforcement status with nothing spent.
- A **second** `advance()` spawns nothing, writes no second note, and does not clear the berths it
  has already honoured; with the rule **off** the sweep does nothing at all.
- `getExitDeclaration` finds this turn's order and refuses a mode-7, a rejected and a stale one,
  while `getVortexDeclaration` (the entrance reader) still ignores it entirely.
- The modifier table: Minbari −1, Ancient −5 on top (the Vorlon opener in 4277 reads −5 with no
  friendly base on the board).

**Replay harness:** `check` shows **only** the three known Stage 2/5 baseline additions
(`reinforcementCount`, `reinforcementPoints`, `formingExits`) across all 611 diffs — Stage 6
adds no payload difference of any kind, which is what protected fields and a rule-gated sweep should
look like. The baseline still needs its one re-record.

**VERIFIED IN PLAY — game 4317, turn 1 (2026-08-28).** Two exits declared in one turn, by a
Primus and an Octurion, and both formed:

- `tac_ship` holds two `SpawnJumpPointExit` rows with deploy movement at `-2,-1` facing 1 and
  `-3,4` facing 5.
- `arrivalturn = 2` on both openers and on all three of the Primus's riders (Sentri, Vorchan,
  Razik); the Centurion, bought front-line, is untouched.
- Two `'Vortex'` notes (`1,-1`) and two `'VortexScatter'` notes (`6,0` and `1,0`), keyed by their
  own vortex ids.
- The two log lines name their bands: *"sensors 10, roll 15 (1d10 band). It forms 6 hexes from the
  declared hex"* and *"sensors 10, roll 1 (1d3 band). It forms 1 hex…"* — and the 1d10 band's
  facing roll came up 3–4, which is why that one scattered six hexes with its facing intact.

The one thing turn 1 of 4317 does not exercise is Stage 7: those five units now have an arrival turn
and still have no Deployment phase to use it in.

### Stage 7 — arriving ✅ COMPLETE 2026-08-28 — VERIFIED IN PLAY (game 4318), then refined by 7a
The `hasReinforcementsArriving($playerid, $turn)` clause in the slot loop; the reinforcement branch
in `validateDeploymentArea` (hex == the assigned vortex's hex, facing == the vortex facing); the
partial-commit exemption in `validateDeployment`; the unplaced-unit reset. Client:
`DeploymentPhaseStrategy` valid-area override, the stacking bypass in `onHexClicked`, the forced
facing, and `validateAllDeployment` not demanding placement.

**As built**, with nine things §4 did not anticipate — three of them silent bugs:

- ⭐⭐ **THE JOIN NEEDED NO NEW STATE OF ANY KIND.** "Which doorway does this unit arrive through?"
  is `arrivalVia` → `vortexHolderId`, and **both halves already existed and already reached the
  client**: `arrivalVia` names the OPENER (§3.1) and `JumpEngine::restoreVortexState` has stamped
  every vortex unit with its holder's ship id since Jump Points Stage 5, which is what the client's
  `getVortexHeldBy` reads. No column, no note, no payload field, and `JumpEngine::getArrivalVortex`
  / `shipManager.movement.getArrivalVortex` are the two sides of the same three-line lookup.
- ⚠️ **`vortexHolderId` IS STAMPED ON LOAD, NOT AT SPAWN.** `spawnVortexUnit` sets the engine's
  `activeVortexId` and writes the note; the reverse link is only made when the note is read back.
  So `getArrivalVortex` answers **null inside the request that created the doorway** — harmless,
  because arrivals happen a turn later in a different request, but it is the one thing the Stage 7
  harness has to simulate, and any future rule that wants to reach the vortex from the opener *in
  the forming request* must go through `$system->activeVortexId` instead.
- ⚠️ **`DBManager::setShipArrivalTurn` HAD TO LEARN NULL, and its own comment said it never
  would.** §2.6's one-way rule is about the DOORWAY (an exit cannot be jumped out of) and says
  nothing about a unit that declined to walk through one. Clearing `arrivalvia` alone would leave
  an unplaced unit reading as an ordinary ship that deployed on a turn now past — on the board,
  shootable, EW-relevant, and standing at its off-map `start` marker for the rest of the game.
  Both fields are cleared, which puts it back to exactly `isReinforcement()`.
- ⚠️⚠️ **THE DEPLOY-START-DOCK LANDMINE IS NOT THE ONE §4 FLAGGED.**
  `Hangar::generateIndividualNotes`' guard turns out to be safe *by accident*: a POST-side ship has
  `reinforcement === false` (`getShipsFromJSON` writes `$reinforcementClaim`), so it answers with
  its slot's placement turn and the guard passes. The two that **did** break are
  `HangarOps::validateDeployBayOrders` and `HangarOps::processLcvDeployStartTransfer`, which compare
  a **POST-side carrier's** `getTurnPlaced`/`getTurnDeployed` against a **server-side** flight's or
  LCV's. An arriving carrier answered 1 while its flight answered 3, so *every* deploy-start dock
  onto a reinforcement carrier was refused — silently, with the fighters left at their off-board
  `start` markers. Both now resolve the carrier through the `$dbCarrier` / `$dbShip` they already
  had in hand. ⭐ Grep for any other site comparing a POST-side ship's turn accessor against a
  DB-side one; this is the shape.
- ⚠️ **THE STACKING BYPASS IN `onHexClicked` IS NOT WHERE THE WAVE GETS STUCK.** From the SECOND
  unit onward every click on the doorway lands on a *shipmate already standing in it*, and
  `onShipClicked` routes that to plain ship-selection several branches before `onHexClicked` is
  ever reached — so units 2..n could not be placed at all. The exemption has to go on the
  `SelectFromShips` popup gate too, **and on that file's own copy of the `isBlocked` test**, which
  is a duplicate of the strategy's and must be kept in step with it.
- ⚠️ **THE COMMIT BUTTON HAD EXACTLY ONE ARMING SITE AND IT WAS A DEAD END.** It is shown only
  after a successful placement, which is correct while every unit in the phase must be placed and
  unfinishable the moment one need not be: a player who brings **none** of a wave through had no
  way out of the Deployment phase. `onlyOptionalPlacementsRemain` arms it at activate instead —
  and deliberately **not** `validateAllDeployment`, which on turn 1 validates every ship at its
  slot's box centre and so would arm the button before anybody had placed anything.
- **`showDeploymentArea` had to be silenced for an arrival.** Its slot box is wherever the fleet
  started, usually the far side of the map from the only hex it may stand in, so lighting it up
  points the player at the one place they definitely cannot go. The blue exit vortex with its
  outward arrow is the cue.
- **`getFirstFriendlyShipDeployment` selected the wrong ship.** It returns the first own unit with
  `getTurnPlaced <= turn`, which on any Deployment phase after the first is the first ship of the
  turn-1 fleet — already on the board, nothing to do. A player would have had to hunt the arrival's
  icon down at its off-map `start` marker before they could place anything. A new first pass
  prefers something that actually places *this* turn; on turn 1 the two passes are identical.
- **A commit warning names what is being left behind**, in the phase -1 `onCommitClicked` block
  beside the mine-range and pre-order nags. Placement being optional is a decision with an
  invisible cost: a ship's exit closes at the end of the turn it opened for, so the berth is
  not waiting next turn.

⚠️ **KNOWN AND ACCEPTED: an unplaced arrival is visible at its `start` marker for the length of the
Deployment phase**, and stays disclosed even if the player then releases it back to hyperspace.
`hideHyperspaceReinforcements` keys on `arrivalTurn === null`, which is set for the whole of the
arrival turn, and §3.6 says that disclosure is the intent. The residue is that an opponent who also
has a Deployment phase that turn can see a unit that ends up not coming. Same shape as turn-1
deployment, where every unplaced ship sits in its box in plain sight; not worth machinery.

**Verify:** three units through one exit stack correctly and separate on their first movement;
leaving one behind works and it is still there next turn; a forced facing cannot be overridden;
speed is free.

**Proven headless** by `tests/replay/reinforcementsStage7Harness.php`, which builds its board by
running the **real Stage 6 sweep** on real local games (4277, 4307; 4311 has too few units and
skips) so the exit it validates against is one the deviation table actually produced. **80
assertions, all passing, zero database writes** — `Manager::$dbManager` is stubbed by reflection and
the three private statics are reached the same way. The POST-side ships are deliberately
`stdClass` stand-ins carrying only what `validateDeployment` reads off a posted object, so any rule
that reaches for a reinforcement field on `$ship` instead of `$servership` fatals rather than
quietly passing:

- the predicate (`isArrivingReinforcement`): true on the arrival turn for the opener and for its
  manifest, **false on the turn after** (or the unit would be re-placed every turn for the rest of
  the game), false for a front-line ship;
- the lookup (`getArrivalVortex`): the opener resolves through a **NULL `arrivalVia`** to its own
  doorway; riders resolve through the holder id; a berth naming a unit that opened nothing, a vortex
  that lost its note, one that has not formed, and one already closed all resolve to **null rather
  than to the wrong doorway**; an **ENTRANCE** vortex held by the same ship is ignored (§2.6);
- the vortex is still usable for the whole of the turn it **closes** on, which is the rule Stage 7
  leans on hardest;
- `hasReinforcementsArriving`: true for the owner on N+1 only, and never for another player;
- one hex and one facing: the exit hex on the exit facing passes; the next hex, a chosen
  facing, and a **mismatched heading** each fail; a unit with no doorway has no legal hex anywhere
  and does **not** fall back to its slot box;
- the whole submission: a **partial commit** (three of a four-unit wave) is accepted, **three units
  stack in one hex with none blocking another**, and a placement off the doorway still throws
  `Illegal placement`;
- the release: both fields cleared in DB and in memory, `isReinforcement()` true again,
  `getTurnDeployed` back to 999, the units that DID arrive untouched, a second pass writing nothing,
  and a unit queued for a deploy-start hangar dock **exempted** (with the exemption removed, the
  same unit is released — so the guard is proven to be doing the work).

**Replay harness:** `check` shows **only** the three known Stage 2/5 baseline additions
(`reinforcementCount`, `reinforcementPoints`, `formingExits`). Stage 7 adds no payload
difference of any kind — every new field is derived, and the two hangar fixes are no-ops for a
non-reinforcement carrier (a POST-side and a DB-side ship agree on slot, osat and base). The
baseline still needs its one re-record.

**VERIFIED IN PLAY — game 4318, turn 2 (2026-08-28).** Two exits formed at the end of turn 1
and both waves came through them. `tac_shipmovement` holds the proof: the Primus opener, the Demos
and the Rutarian all deployed at `-7,4` facing 1; the Centurion, the Sentri and the Razik all at
`1,-5` facing 5 — three units stacked in each doorway, every one on its vortex's own facing, and
**three different speeds among them (0 and 5)**, so the "speed is free" half is exercised too.

#### Stage 7a — the wave places itself ✅ COMPLETE 2026-08-28, verified in play (user request)

Two things came out of that game, and the second made the first moot:

- **"You cannot deploy on terrain." fired on every unit of every wave.** `shipManager.getShipsInSameHex`
  is a query with a UI side effect: it pops that toast whenever the hex holds Terrain, and an
  exit vortex *is* Terrain. Placement went through regardless (the arrival bypasses the block),
  so the message was pure noise — but it was noise on every single click. It is now suppressed for
  an arriving reinforcement, in both of that function's two error sites. ⚠️ Only the TOAST is
  suppressed; the vortex still goes into the returned list, which callers use for their own
  occupancy decisions.
- ⭐ **AND THEN THE CLICK ITSELF WENT.** There was never a choice to make: the deviation roll fixed
  the hex and the facing a turn earlier, so "click the one legal hex" was ceremony.
  `DeploymentPhaseStrategy.autoPlaceArrivingReinforcements` runs on phase activate, before
  `selectFirstOwnShipOrActiveShip`, and puts every arrival in its doorway. The player keeps the one
  decision that was ever real — **speed, 0–10, on the ordinary Deployment accel arrows** — and the
  header says `DEPLOYMENT: REINFORCEMENTS ARRIVED` when that is the whole of the phase.

⚠️ **THE DUPLICATE-DEPLOY-ROW TRAP.** The deploy move is not persisted until commit, so
auto-placement runs against a ship carrying only its off-board `start` row — but `activate` can run
again (a re-activate in the same page load, or a reload after the phase was committed, where the row
*is* persisted and `ship.deploymove` is not). Two guards, and both are needed:
`ship.deploymove` for the first case and a scan of `ship.movement` for `type == "deploy" && turn ==
gamedata.turn` for the second. A second deploy row makes `validateDeployment` throw *"Found more
than one deployment entry"* and takes the whole submission down.

⚠️ **NOTHING SERVER-SIDE CHANGED, and that is the point** — auto-placement produces exactly the
POST the manual click produced, so `validateReinforcementArrival` still checks the hex, the facing
and the heading. The client is a convenience, never the authority.

**The optional-placement path is deliberately still open**, and is now the FAILURE path rather than
a player choice: a unit whose doorway cannot be resolved is left alone by auto-placement,
`validateAllDeployment` skips it so the commit button still arms, the phase -1 commit warning names
it ("no open jump point was found for them"), and `releaseUnplacedReinforcements` sends it back to
hyperspace with nothing spent. ⭐ **§2.4's "a player may bring some units through and leave the rest"
is therefore no longer reachable through the UI** — auto-placement supersedes it. If it is ever
wanted back it is a per-unit control in the Deployment phase, not a change to any of this
machinery, which already handles the unplaced case end to end.

**Proven** by a node harness driving the *shipped* `ships.js` / `movement.js` over game 4318's real
rows (17 assertions): all six reinforcements join to the doorway the database says they arrived
through, each takes that doorway's facing (1 and 5) rather than its reverse, `movement.deploy`
stamps facing **and** heading while leaving speed alone, a unit reloaded after commit is not given a
second deploy row, the closed / not-yet-formed windows both refuse, and an ENTRANCE vortex held by the
same ship is still ignored.

#### Stage 7a fallout — the deploy-start hangar dock ✅ FIXED 2026-08-28, untested in play (user report, game 4318)

"Deploy Flights in Hangar" on a reinforcement carrier appeared to do nothing. Two separate causes,
both found by driving the real `DeploymentDock.js` over 4318's own payload:

- ⭐⭐ **`confirm.hangarDeployDock` BUILT THE DIALOG AND THEN THREW IT AWAY.** It has TWO empty
  states and only one of them was visible. The first (`pending.length === 0`) renders *"No Hangar
  Operations available."*; the second — flights ARE pending but every row was skipped for want of
  space — was a bare `e.remove(); return;`. A click into that path produced no dialog, no message
  and no error. 4318's **Centurion Attack Cruiser is exactly it**: `new Hangar(7, 2, 1)` is a
  **2-box shuttle bay**, both boxes taken by its default shuttles, and the Sentri and Razik flights
  riding its jump point need six boxes each. It now renders the reason, names the flights that are
  stuck, and prints each bay's free-box count — because "0 free" IS the explanation and is
  otherwise a tooltip away. ⚠️ This is a **pre-existing Hangar Ops bug**, not a Stage 7 one; the
  reinforcement wave is simply the first fleet likely to meet a bay with no room in it.
  (The same game's **Primus**, `new Hangar(7, 14)` with 12 boxes free, took its Rutarian flight
  through the identical code path without complaint — so the button was never broken, only mute.)
- ⚠️ **AUTO-PLACEMENT BROKE THE OTHER ROUTE IN.** "Select the flight, click the carrier" reaches
  the DOCK button through `onShipClicked`'s SelectFromShips branch — which is only entered when the
  two are in **different hexes**. Before Stage 7a an arriving flight sat at its own off-map `start`
  marker, so it always was; now the whole wave is auto-placed in one doorway, every click on the
  carrier lands on the hex the flight is already standing in, and the `else` branch swallowed it
  into a plain selection swap. Fixed by taking the picker in that branch too — scoped to an
  arriving reinforcement FLIGHT, since two units that merely happen to share a hex on turn 1 should
  keep the selection swap nobody asked to change.

#### Stage 7a fallout — the opponent had no blueprints for the wave ✅ FIXED 2026-08-28, untested in play (user report)

On the opponent's screen, every arriving reinforcement rendered with **no ship information at all —
except the Primus**, and a page reload fixed it. The user's own diagnosis was right: the Primus was
the one class already on the board front-line, so its blueprint had come along at page load.

⭐⭐ **`window.staticShips` IS BUILT ONCE, FROM THIS VIEWER'S PAYLOAD, AT PAGE LOAD** —
`game.php` collects `$serverdata->ships[]->phpclass` and hands it to `BlueprintCache`. §3.6 removes
a hyperspace reinforcement from an opponent's payload **outright**, so its class is not in that
list, and when the wave arrives mid-session the client is handed ship JSON for hulls it has no
blueprint for. `Ship()` then builds them from the live JSON alone: no armour, no arcs, no
maxhealth, no systems.

⚠️ **THIS IS THE PRICE OF §3.6 AND IT CANNOT BE PAID BY PRELOADING.** Putting the concealed
classes into the opponent's `staticShips` would hand the whole composition of the hidden wave to
anyone who opens devtools — which is the exact disclosure §3.6 exists to prevent, and a step
*worse* than the `[Deploys on Turn N]` late slots it was written to improve on. The blueprint must
arrive when the ships do, not before.

⭐ **THE SAME BUG ALREADY HAD AN ANSWER, AND STAGE 7 NEEDED A DIFFERENT ONE.**
`gamedata.hasShipIdentityChanged` (the Chameleon reveal, D14) hits this and **reloads the page**.
That is right for it: a broken disguise changes the identity of a ship the page has already built,
so its icon, ship window and every cached reference are made of the old hull and nothing short of a
rebuild is honest. Here the ships do not exist yet — only the blueprint is missing — so
`gamedata.ensureBlueprintsFor` **fetches the faction and re-enters**, and the player keeps their
camera, their selection and their turn.

- It **defers the whole update** rather than patching afterwards: returning true leaves
  `parseServerData` having touched nothing, so the re-entry is an ordinary first pass over the same
  payload. No half-applied turn, no ship built from a blueprint that had not landed yet.
- ⚠️ **Every faction is asked for at most once.** A class still missing after the fetch must not
  loop — and *must not freeze*: polling stops once it is this player's move, so a permanently
  deferred update is a dead screen, not a slow one. After one attempt the payload is applied
  whatever happened, which is exactly the old behaviour.
- ⚠️ **The merge ADDS ONLY, never overwrites.** `gamelobbyloader.php` serves the lobby's copies;
  diffed against `BlueprintCache`'s own output for Primus and Sentri they are identical bar the
  blueprint's ship-level `id`/`flightid` (overwritten from the live JSON by `Ship()` anyway), one
  tooltip `data` value that is a string on one side and a number on the other, and the lobby-only
  `systemEnhancementOffers`. Close enough to add, not close enough to swap out from under units
  already on the board.
- `gamelobbyloader.php` rather than a new endpoint: it already serves per-faction blueprints with
  gzip and ETag revalidation, from the same `static/json/<faction>.json` the generator writes.

**Proven** by a node harness driving the shipped `gamedata.js` over 4318's turn-2 payload from
player 211's starting position (19 assertions): it reproduces the report exactly — *"classes with no
blueprint at load: Centurion, Demos, Rutarian, Sentri, Razik"*, the Primus conspicuously absent —
then defers the update, fires **one** request for the **one** faction that is short, ignores a poll
that lands mid-fetch without firing a second, re-enters with the very payload it deferred, leaves
the Primus blueprint and the Narn faction untouched, and afterwards applies normally. A failed fetch
still re-enters and the retry applies the payload instead of deferring again.

⭐ **AND THE RULE ANSWER, WHICH IS NOT A BUG:** a 2-box shuttle bay cannot hold a 6-fighter flight,
with or without its shuttles, so 4318's Sentri and Razik genuinely cannot start aboard the
Centurion. `findPendingFlightsForCarrier` requires the flight to share the carrier's **hex**, so
they cannot reach the Primus's spare 12 boxes either — it is at the other doorway. A wave that wants
its fighters stowed has to ride the jump point of a carrier with the boxes for them.

### Stage 8 — gates ✅ BUILT 2026-08-28, untested in play
The blue `UI.gateSignal` variant; the exit flavour on the claim; the refund when an enemy entrance
claim wins the contest (the manifest is simply never stamped, so this is mostly a matter of
clearing `arrivalvia`); waves on each turn of a gate's programmed hold.

⚠️ **A JumpEngine recharge or duration rule must read `$delay`, never `$loadingtime`** — the
harness proved this on Phase 2 ([[project_jump_gates]]).

**Verify:** both `convoyRaid`-style scenarios — a gate claimed for entry uncontested, and a gate
claimed for entry and lost to a nearer enemy entrance claim.

**As built.** The whole stage is one new `damageclass`, `'gateexit'`, and everything else follows
from it — the same shape `'jumpexit'` gave a ship's exit in §3.4, one step along. Two user
requests set the gestures:

> "When a player signals the jump gate to open an entrance from Hyperspace and clicks Signal Gate,
> this should open the same Jump Point Manifest window."
>
> "In cases where a jump gate is already open with a jump point allowing units to enter from
> Hyperspace, then it should be listed as a Jump Drive ship in Manage Reinforcements, but instead of
> 'Choose Hex' the same button should say 'Select Reinforcements' and likewise bring up the 'Jump
> Point Manifest' window."

**The gesture, end to end.** Click the gate in Initial Orders → a **second** tooltip button,
**Signal Gate for Arrival** (blue vortex icon), offered only when this player has something in
hyperspace → `UI.gateSignal` in its cyan livery, commit reading **Signal for Arrival** → the claim
is built with `damageclass='gateexit'` → **the Jump Point Manifest opens immediately**. On any
later turn of the hold the doorway is picked up again from **Manage Reinforcements**, where the gate
is a row like any drive but its button reads **Select Reinforcements**.

- ⭐ **A SECOND TOOLTIP BUTTON, NOT A TOGGLE IN THE PANEL.** A gate holds one jump point and it is
  one-way, so departure and arrival are two answers to one question — and the arrival one is
  meaningless unless something is waiting behind it, which is a condition a *button* can simply not
  meet. A toggle would sit greyed out on every gate in every game without the rule; this way the
  feature is invisible until it applies, which is the shape the two existing mutually-exclusive gate
  buttons already have.
- ⭐ **THE LIVERY IS A TOKEN OVERRIDE AND NOTHING ELSE.** Every `#gateSignalUI` rule is written
  against the `--gs-*` custom properties, so `.gateSignalExit` redefines six of them and
  re-liveries chrome, steppers, field, focus ring and commit button with no duplicated selector.
  Add a hard-coded colour to the gold rules and this silently stops working. The class is set with
  an explicit second argument to `toggleClass` on **every** open, both ways — the panel is a
  singleton reused across transactions, so a bare `add` would leave the blue behind on the next
  ordinary signal.
- ⭐⭐ **§0's GATE RULING FORCED A REVERSAL IN `getVortexClosureReason`.** Stage 7 put the
  `SpawnJumpPointExit` branch *before* the gate branch, reasoning that exit-ness belongs to
  the vortex while gate-ness belongs to the engine, so the one-shot rule wins. §0 says a gate's
  exit runs for its **programmed hold**. Left alone, a gate signalled for four turns of arrivals
  would have slammed shut after one — silently, and looking exactly like a working feature. The
  branch is now `&& !$this->isGateJump()`; trap 5 is unaffected, because a gate's engine is released
  by the hold expiring and a gate that never closed its jump point would be broken for Phase 2 entrances
  too.
- ⭐ **THE WAVES ARE ONE LINE ADDED TO `$opened`, NOT A SWEEP OF THEIR OWN.**
  `JumpEngine::collectGateExits` walks the gates at the end of `spawnExitVortices` and adds
  every one holding a doorway-in that survives into next turn. Everything downstream is then already
  right: `stampArrivingReinforcements` stamps a berth naming an id **in** that list and **clears**
  one naming an id that is not — which *is* the refund, for both of its causes (an enemy entrance claim
  won the contest; the hold ran out). A second sweep would have meant writing that refund twice. The
  terrain skip that used to sit in `stampArrivingReinforcements` is gone; it existed precisely so
  Stage 8 could remove it.
- ⚠️ **THE WINDOW IS `hasOpenVortex($turn + 1)`, NOT `($turn)`.** A manifest named this turn arrives
  in the **Deployment phase of next turn**, so the question is whether the doorway is there *then*.
  `closeExpiredVortices` runs before this sweep in `FireGamePhase::advance` and writes
  `$vortexCloseTurn` in memory as well as to the notes, so the answer is right on the very turn the
  hold expires rather than a turn late.
- ⭐ **THE CLIENT KNOWS WHICH TURN IS THE LAST ONE, AND NEEDED NO NEW PAYLOAD.**
  `JumpEngine::stripForJson` has sent `vortexTurnsOpen` / `vortexMaxTurns` since Phase 2 to draw the
  system icon's "2/4 turns open" counter, and **on a gate `vortexMaxTurns` *is* the programmed hold**
  (`MAX_VORTEX_TURNS` only on a ship's). So `age < hold` is exactly "one more wave to give", and the
  menu greys the row with **`jump point closes this turn`** instead of quietly taking a manifest that
  can never be honoured. Listing it greyed rather than hiding it is the point — the player can see
  *why*.
- ⭐ **ONE AUTHORITY FOR "IS THIS BERTH STILL GOOD?", AND IT IS THE END-OF-TURN SWEEP.** Two other
  places could have decided it and both are deliberately lenient instead:
  `DeploymentGamePhase::releaseUnplacedReinforcements` **keeps** a gate berth on the flat test "is the
  opener terrain?" (§2.4's "keeps its berth if the exit will still be open next turn"), and
  `InitialOrdersGamePhase::collectGateOpeners` accepts a doorway that expires tonight, because during
  Initial Orders `$vortexCloseTurn` has not been written yet. Both defer to the sweep. A berth written
  and cleared four phases later costs the player nothing; a strict test in either place that got the
  arithmetic wrong would silently drop a legitimate wave.
- ⚠️ **`arrivalTurn` IS STILL CLEARED FOR AN UNPLACED UNIT EVEN WHEN THE BERTH IS KEPT.** Keeping a
  berth is not the same as staying an arrival: the unit has to go back to `isReinforcement()` —
  concealed, 999 to both turn accessors, re-stampable — or Stage 7's ⚠️ comes true and it reads as a
  ship that deployed on a turn now past.
- ⚠️ **`'gateexit'` HAD TO BE ADDED TO `isVortexDeclaration` IN `BallisticIconContainer`, and that
  is the information leak, not a cosmetic miss.** An arrival claim carries the same `targetid` as an
  entrance claim — the claiming player's nearest qualifying unit — so without it the marker is hung on
  **that ship** and a bright line drawn to it from the gate, on the claimant's own screen, the
  instant the order is built and before the server has masked anything. Which unit signalled is never
  revealed ([[project_jump_gates]] §2.1). It also gets a `case` of its own in the label switch, in
  `hexBlue`, or it falls through to the default **red** hex and reads as incoming fire at a gate
  nobody is shooting.
- ⚠️ **`gamedata.canSignalJumpGate` WAS BLIND TO A GATE HOLDING AN EXIT.** `getVortexHeldBy` is
  entrance-only by design, so on its own it answers "free to signal" for a gate that demonstrably is not.
  The server refuses such a claim (`hasOpenVortex` knows nothing of flavour), so the blindness would
  have shown up as a button offered and then silently rejected at commit — the exact "worst of both"
  that function's charge note exists to avoid. `getExitHeldBy` is now asked beside it.
- **`data-declared` became `data-action`**, and it is not a rename: there were two states and two
  labels, and there are now four (Choose Hex, Withdraw Jump Point, Select Reinforcements, Withdraw
  Gate Signal). A boolean cannot carry four. The row that knows its own state writes the label it
  wants and `syncLabel` only moves it onto the button.
- **A gate row never highlights the map**, deliberately. `highlight()` drives the blue Forming
  markers of `'jumpexit'` declarations; a gate's claim is not one, and the gate is a permanent,
  named, plainly visible unit on its own hex. The highlight exists to tell three *identical* markers
  apart — a gate has no twin to be confused with.
- **`SIGNALLED`, not `OPENING`.** A drive is *holding* a doorway of its own open; a gate has been
  *asked* to open one and may still lose the contest to a nearer enemy claim. Both share the cyan
  row tint, which is right — each is a doorway this player is counting on. `SIGNALLED` is wider than
  the tag's `min-width` and grows, which is what that floor-not-`width` choice was for; the shared
  **left** edge is what the user asked for and it is unaffected.
- **`removeGateSignalOrder` now drops the berths with the claim**, guarded on the gate not already
  holding a doorway from an earlier turn — cancelling this turn's claim on a gate that is already
  open takes nothing away, and clearing there would cancel a wave nobody asked to cancel.

**Two rulings made here, both open to reversal:**

1. **A gate's log line says "opens an ARRIVAL jump point", one turn before the blue vortex appears.**
   The claim itself stays secret (`hideSystemFireOrders`), so this brings forward exactly one turn of
   "somebody is coming through here" — the same trade §2.3 already made for a ship's exit, whose
   blue Forming marker is public for the whole of its declaration turn. It still names only the
   **player**, never a unit and never a count.
2. **Anyone may ride an open gate exit, including one an opponent paid to open.** A gate is
   contested ground with no owner priority and *any* unit of *any* side may use an open gate vortex
   ([[project_jump_gates]] §2.6); this is that rule read in the other direction. Both
   `collectGateOpeners` (server) and `gateCandidates` (client) list an open exit whoever claimed
   it. Narrowing it to the claimant is a one-line change in each if the user would rather.

**Still not offered:** re-aiming or re-timing a standing gate claim (cancel and re-signal, exactly as
Phase 2 has always worked), and a gate exit is **not** a way out — `Movement::getOpenVortexInHex`
and its client mirror are exit-blind by design (§2.6), so a gate signalled for arrival offers no
Jump Out button for the turns it stands.

**Proven headless.** `tests/replay/reinforcementsStage8Harness.php` — **103 assertions across games
4277/4307/4311, all passing, zero database writes** (`Manager::$dbManager` stubbed by reflection; the
six private methods reached the same way). It builds a `JumpgateCapital` on top of a real player's
unit in a real recorded game, so the signal-range and distance rules are the live ones:

- the claim rules: an arrival claim is legal with the rule on and a wave waiting; refused with the
  rule off, refused with nothing in hyperspace, refused outright on a **ship's** Jump Engine — and a
  **departure** claim is unaffected by all three, so the rule gate is on the flavour and not the gate;
  ⚠️ **the "nothing in hyperspace" refusal was dropped on 2026-09-02** and that assertion is now
  inverted — see Stage 11b;
- the class: an arrival claim opens a `SpawnJumpPointExit` (`phpclass` included, which is the
  identity that survives the reload); a departure claim still opens a plain `SpawnJumpPoint`;
- the contest: my arrival claim against an enemy entrance claim **0 hexes** from the gate opens **yellow**,
  and my whole manifest is refunded — still in hyperspace, still unassigned, nothing spent;
- closure: hold 3 stands on turns N…N+2 and expires on N+3, while a **ship's** exit on the same
  vortex class is still one-shot (trap 5);
- the waves: `collectGateExits` puts the gate in `$opened` and leaves out a gate holding an entrance
  (proven with the entrance vortex **actually on the board**, so the assertion is about the class and not
  about a lookup returning null); every manifest unit is stamped for next turn in memory *and* in the
  database; on the last turn of the hold the gate is not in `$opened` and every berth is refunded;
- the release: an unplaced arrival clears `arrivalTurn` and **keeps** a gate berth with no
  `arrivalvia` write at all, while a **ship** berth is dropped exactly as Stage 7 left it.

**And headless on the client** — `tests/replay/reinforcementsStage8ClientHarness.js`, run with plain
`node` from anywhere, `vm`-evaluated against a stubbed client world because a parse check cannot
catch a missing global or a wrong predicate ([[howto_verify_react_bundle]]). It injects one export
line before the module's `return {` to reach the private row builders and changes nothing else.
**34 assertions**. The module evaluates clean; a gate
holding an exit is listed and one holding an entrance is not; a destroyed gate is not; `age < hold`
across all three states including the no-vortex NaN case; the row offers **Select Reinforcements**
badged **OPEN** and sorts **before** the hyperspace drives; on the last turn it says
`jump point closes this turn`, its radio is `disabled`, and the pre-checked row is the **drive**, so
the dialog can never open stuck on a row nobody can move off; a claimed gate offers **Withdraw Gate
Signal** badged **SIGNALLED** *before its vortex exists*; a `'jumppoint'` claim on a gate is **not** an
arrival doorway; a drive booked on the gate is greyed as `riding <gate>` and freed again the moment
the doorway stops taking waves; `clearGateManifest` drops every berth; and `strandedByCommit` is
**silent while any live gate is on the map** and names the stranded fighter again the moment there is
none — which is the change §4 Stage 4b wrote down in advance as the one Stage 8 would have to make.

**Replay harness: `check` is 155 passed, 0 failed on the full local corpus.** Stage 8 adds **no
payload difference of any kind** — no new column, no new note, no new serialised field; the flavour
rides an existing `damageclass` and the client's last-turn test reads a pair of fields Phase 2 has
sent since 2026-08-23. (Game 4318 SKIPs: it advanced since it was recorded and wants its own
re-record.) Stage 6 (177) and Stage 7 (80) both still pass unchanged.

⚠️ **The legacy bundles need regenerating** (`scripts/fvbuild.ps1`) before this is exercised in
production mode — six client files changed and `game.php` serves them from `game.legacy.bundle.js`
outside dev mode. *(Done 2026-08-29, together with the fixes below.)*

### Stage 8 — first play test (game 4319, 2026-08-29): two bugs, neither of them in the gate code

Both reports came from one session and neither was a mistake in what Stage 8 built. One was a claim
the menu file made about itself that had never been true; the other was a purchase the lobby should
never have allowed. They are recorded at length because they are the same *shape* — **a state that
was theoretically reachable all along and that reinforcements made ordinary**.

> "You can't click on a Jump Gate or Open Ship Details buttons unless you have a ship selected in
> Initial Orders."

⭐⭐ **THE MENU'S OWN COMMENT SAYS `this.selectedShip` IS "ROUTINELY NULL HERE", AND ONE CONDITION IN
THE ARRAY IT SITS IN COULD NOT SURVIVE THAT.** `isEnemyEW` reaches
`shipManager.hasSpecialAbility(this.selectedShip, 'alliedEW')` whenever the game has friendly fire
OFF, and `getSpecialAbilitySystem` opens with `for (var i in ship.systems)` — which on `null`
**throws**. It is the third entry in `ShipTooltipInitialOrdersMenu.buttons` (`addOEW`), reached as
soon as `notSelf` is true, which it always is when nothing is selected.

⚠️ **AND A THROW THERE COSTS THE WHOLE PANEL, NOT ONE BUTTON.** The conditions run inside
`ShipTooltipMenu.renderTo`'s `.filter()`, which runs inside `ShipTooltip.createForSingleShip`, which
runs inside the **constructor, before `show()`**. So a TypeError in a condition that was never going
to add a button took the name, the stats, the ballistics list, both gate signal buttons and **Open
Ship Details** down with it. Nothing appeared at all, which is exactly what the report describes.

It stayed hidden for the whole of Jump Gates Phase 2 because `InitialPhaseStrategy.activate` calls
`selectFirstOwnShipOrActiveShip`, so something is essentially always selected — Stage 3's "click the
gate with nothing selected" gesture was only ever exercised with a ship still selected from the
auto-select. **Reinforcements are what made an empty selection ordinary**: a fleet held entirely in
hyperspace has nothing for `getFirstFriendlyShip` to return.

- **`isEnemyEW` now answers a plain `false` with no source, BEFORE the friendly-fire shortcut** —
  which would otherwise say "yes, EW is legal" with no unit to assign it from.
- **Two entries gained a `hasOrderSource` condition**: `targetWeaponsHex` and `removeMultiOrder`
  asked only about the *weapon selection*, never about the shooter, so they could be offered with a
  null source and hand `weaponManager` a null. Same fix on the Firing menu's `targetWeaponsHex` and
  `targetSuppWeapons`. Every other entry already failed closed through `isSelf` / `isEnemy` /
  `isElint` / `isFriendly`.
- ⭐ **AND A SECOND, INDEPENDENT WAY TO LOSE THE SAME PANEL.** `InitialPhaseStrategy.targetShip` (and
  its twins in `FirePhaseStrategy` / `PreFiringPhaseStrategy`) refused a selected unit that is not on
  the board yet by calling `showShipTooltip(ship, payload, menu, false)` — with `menu` still
  **undefined**, because the `var` is hoisted and assigned two lines below. That is not "a menu with
  no orders in it": `ShipTooltip` renders no button row without a menu and tears itself down on the
  first mouse event. All three now compute an `orderSource` — the selected ship, or `null` when it is
  not deployed yet — and build the menu from that. The refusal is real and unchanged; what it costs
  is the ORDER SOURCE, not the panel. (`FirePhaseStrategy` / `PreFiringPhaseStrategy` also had no
  null guard at all on `getTurnDeployed(this.selectedShip)`, so an empty selection threw there too —
  the same crash, one phase along.)

**Proven headless**: `tests/replay/initialOrdersTooltipHarness.js`, plain `node`, **14 assertions**.
It re-runs `renderTo`'s *own* condition filter — same `[].concat(condition)`, same
`.every(c => c.call(menu))` — because the bug was never in a button's verdict but in a condition
throwing on the way to one, and a test that only asked "is the gate button offered?" would have
passed on the broken code by never reaching the throw. Removing the null guard turns 14 passed into
7 passed / 7 failed, the first of them naming the real message
(`Cannot read properties of null (reading 'systems')`).

> "Turn 3 triggered Pre-Turn Actions gamephase, and I'm not sure why."

⭐⭐ **A GATE IS IN `$opened` AS THE DOORWAY'S HOLDER, NOT AS A UNIT RIDING IT — AND IT STAMPED
ITSELF.** `stampArrivingReinforcements` has three populations and the first is "the OPENER itself,
which always arrives through its own doorway", keyed on `isset($opened[$unit->id])`. That sentence is
true of a **ship** and false of a **gate**: `collectGateExits` files the gate under its own id
precisely so the *manifest* test below it works. So a gate that also carried `reinforcement` matched
the opener test **on itself** and was given `arrivalturn = turn + 1` on every turn its jump point
stood. `TacGamedata::hasReinforcementsArriving` reads exactly that column, so `FireGamePhase::advance`
granted the owner a Deployment phase for a unit already on the board — and the client, correctly, had
nothing to place, so it announced itself as **PRE-TURN ACTIONS**. It then repeated: that phase's
`releaseUnplacedReinforcements` cleared `arrivalturn` again, which made the gate `isReinforcement()`
once more, and the end-of-turn sweep re-stamped it.

⚠️ **THE FLAG SHOULD NEVER HAVE BEEN ON THE GATE.** Both gates in 4319 were bought with the lobby's
**REINFORCEMENTS** group selected, and nothing on either side refused it. A base, an OSAT and Terrain
all answer **1** to `getTurnDeployed` before it ever looks at the reinforcement branch, so the flag
can never come true for them — but `isReinforcement()` means "flagged **and** no arrival turn yet",
which such a unit answers **true** while standing in plain sight on the map.
`hideHyperspaceReinforcements` already carried a ⚠️ about exactly this ("BOTH TESTS, not
`isReinforcement()` alone"), which is what kept the gate visible to the enemy; the stamp had no such
guard.

- ⭐ **ONE NAME FOR THE RULE, THREE READERS.** `BaseShip::alwaysDeploysTurnOne()` is the first line of
  `getTurnDeployed` lifted into a method, and it is now also asked by `BuyingGamePhase::process` (the
  flag is refused outright on such a hull) and by `stampArrivingReinforcements` (no arrival turn,
  ever). Trap 24's rule applied to a predicate rather than to a decision: three sites re-listing
  `osat || base || isTerrain()` is three chances to disagree.
- **The lobby refuses the same purchase up front** — `gamedata.canBeReinforcement`, asked by
  `doBuyShip`, `doBuyBulk`, `isReinforcementRow`, `toggleReinforcement` and the row's Reinforce link,
  so the group a player sees and the flag the server writes cannot disagree. The toggle can still
  clear a stale flag on a loaded fleet, which is the only useful direction it has there.
- **Kept at the stamp as well as at the buy**, deliberately: a fleet bought before the fix carries the
  flag in the database, and the stamp is what every later phase believes.

**Proven headless**: six new assertions in `tests/replay/reinforcementsStage8Harness.php` (**130
passed, 0 failed**, was 103). The gate is given the flag by hand, the way an already-bought fleet
carries it, and the harness asserts it is still in `$opened` (it *is* holding the doorway), is not
stamped, produces no database write, grants **no** Deployment phase — and that its manifest is stamped
exactly as before, because the refusal is about the gate alone. ⚠️ The gate is re-owned by the real
player for that block: the harness's default gate is `userid -5`, against which the phantom-phase
assertion is **vacuous**. Disabling the guard turns those six into four failures.

**And the toast that came with it.** The rest of the same report — *"whenever I click on a Jump Gate
I get the 'You cannot deploy on Terrain' warning as if I have a ship selected for deployment, but
there's no ship"* — is `DeploymentPhaseStrategy.onHexClicked`'s entry guard, which read
`getTurnPlaced(this.selectedShip) < gamedata.turn`. That is right as "already placed on an earlier
turn, so no re-placement" and wrong as the rule it stands in for: a unit that places **later** passes
it, and a unit still in hyperspace answers **999**. The click then ran the whole placement path —
`validateDeploymentPosition`, then `getShipsInSameHex`, whose terrain branch is where that toast comes
from — for a unit with no business being placed at all. It is now `!=`: this turn or nothing. (With
the gate no longer an "arriving reinforcement" the phase that made this reachable is gone as well, so
both ends are closed.)

**Data repair for 4319**: `UPDATE tac_ship SET reinforcement=0, arrivalturn=NULL, arrivalvia=NULL WHERE
tacgameid=4319 AND phpclass='JumpgateCapital'` — `hasReinforcementsArriving(210, 3)` goes from `true`
to `false`. Any other game whose lobby put a base, an OSAT or a gate in the reinforcements group wants
the same two columns cleared; nothing else about such a unit is wrong.

**Replay harness: `check` is 155 passed, 0 failed**, unchanged — none of this touches a serialised
field. Stage 6 (177), Stage 7 (80) and the Stage 8 client harness (34) all still pass.

### Stage 9 ✅ BUILT 2026-08-29, untested in play
The scatter initiative penalty (scatter hexes + 2 per 60° of facing shift, applied to units arriving
through that vortex, on their arrival turn only) read from the `'VortexScatter'` note — **plus four
refinements the user asked for at the same time** (2026-08-29), which turned out to be the larger
half of the stage.

#### 9a — the terminology swap
See the banner at the top of this file for the mapping and for why the yellow class kept its bare
name. Mechanics of the change:

- **~200 identifiers and their prose**, swapped in both directions in one pass over a 35-file
  whitelist. ⚠️ It had to be a **simultaneous swap, not two renames**: the codebase used *exit* for
  yellow, so `entrance → exit` on its own would have collided with 60-odd existing correct uses of
  *exit*. A handful of unrelated idioms (`early exit`, `exit loop`, `exitSourceOverride`,
  `exitFullscreen`) were sentinel-protected first, as were the four artwork filenames — which were
  already named the NEW way and must not move ([[arch_image_cache_busting]]).
- **`db/reinforcementsRename.sql`** migrates the two persisted strings: `tac_ship.phpclass`
  `SpawnJumpPointEntrance → SpawnJumpPointExit` (3 rows locally, game 4318) and
  `tac_fireorder.damageclass` `jumpentry → jumpexit`, `gateentry → gateexit` (5 rows). ⚠️ **Run it
  with the deploy, not after** — `phpclass` is what `new $phpclass(...)` is called with on reload, so
  a stale row is a fatal on every load of that game.
- **`source/autoload.php` and the static ship JSONs regenerate** (`fvbuild.ps1 -Server`). The class
  file was renamed too, because a filename that does not match its class is **silently** skipped by
  the static generator (trap 9).
- **The one player-facing label that had to be rewritten rather than swapped** is the gate's
  departure button: "Signal Jump Gate for Exit" would have become "…for Entrance", which is correct
  under the new convention and reads like a trap to a player. It is **"Signal Jump Gate for
  Departure"**, paired with the existing "Signal Gate for Arrival". Departure/Arrival is the verb
  pair for a player; Entrance/Exit is the noun pair for the vortex.

#### 9b — Shadows and other legacy drives phase IN
> "Shadows and other legacy jump drive factions should phase in in the same manner they phase out
> e.g. no visible jump point terrain is created. But we can still have blue ballisticSprite in hex
> just with 'Reinforcements' as a general message."

⭐⭐ **THE WHOLE FEATURE WAS ONE LINE MOVED PAST ANOTHER.** `Firing::getVortexDeclarationBlock` opened
with `if ($weapon->isLegacyJump()) return …`, and the arrival branch sat *below* it. The arrival rule
list has **no range, line-of-sight, offline or charge test** (§3.4 — the opener is in hyperspace and
has no hex to measure from), which is exactly the list `markLegacy()` makes unanswerable — so there
was nothing left for the flag to protect. The branch moved above the refusal; the refusal stayed.

- ⚠️ **THE ASYMMETRY IS THE RULE.** `getVortexDeclaration` (the departure reader) still refuses a
  legacy engine outright, and `getExitDeclaration` (the arrival reader) no longer does. A Shadow hull
  may come back and may still never open a way out. A test that only asserted "the arrival is
  accepted" would pass on a build that had simply deleted the legacy rule, which is why the harness
  asserts both directions on the same engine.
- ⭐⭐ **THE DOORWAY IS STILL A UNIT — `SpawnJumpPointPhaseIn extends SpawnJumpPointExit`.** Every
  Stage 6/7/8 rule is anchored on the vortex object (where the wave stands, which way it faces, when
  it closes, whether an unplaced unit keeps its berth, whether a second doorway may clamp onto the
  same hex). Deleting the object for one faction would have forked all of that in two. The client
  draws nothing for it instead.
- ⚠️ **A SUBCLASS AND NOT A FLAG, because of persistence.** A public property set at spawn does not
  survive the round trip — `DBManager` writes the columns and the reload is `new $phpclass(...)` — so
  a boolean would be true for exactly one request and false for the rest of the game, silently.
- ⭐ **ONE LINE OF SUPPRESSION: `shipManager.shouldBeHidden`.** It is what the icon, the facing arrow,
  the click/hover sweep, the hex ship-list and the replay lifecycle animation all consult, so hiding
  the unit there hides it everywhere at once. A second `if (isPhaseInVortex)` anywhere else is a sign
  the suppression is in the wrong place.
- ⚠️⚠️ **`isJumpVortexExit` HAD TO LEARN THE SECOND CLASS NAME** — trap 23's shape exactly. On the
  server one `instanceof` catches the subclass for free; on the client the test is a phpclass
  **string**, so the phase-in doorway would have stopped being a doorway on that side only: no
  arrival hex, and a Jump Out button offered on a blue vortex.
- ⚠️ **AND IT NEEDS ITS BLUEPRINT ANYWAY.** `SpawnJumpPointPhaseIn` is in
  `JumpEngine::$spawnableClasses` despite never being drawn: `model/ship.js` merges the live payload
  against the blueprint by faction + phpclass, and a unit with no blueprint is a unit with no
  phpclass — which is the very thing `isJumpVortexExit` tests. "Invisible" is a rendering rule, not
  an excuse to skip trap 16.
- **`canOpen` / `jumpEngineOf` / the lobby's `hasVortexJumpEngine` are now "any Jump Engine"**, and
  the last is renamed `hasArrivalJumpEngine` so the next reader cannot mistake it for the departure
  test. The three-property legacy sniff was right while only a B5 vortex could bring a wave in; kept,
  it would have put the lobby's "none of your reinforcements can arrive" WARNING on a legal Shadow
  fleet — a scary wrong message, which is worse than none.
- **The marker says `Reinforcements`** rather than "Jump Point Forming", because for a phasing hull
  nothing is forming. Two sources, as always: the owner's own order can ask the drive directly
  (`shipManager.movement.isLegacyJumpEngine`, the client-readable trace of `markLegacy()`), and the
  republished slot entry gained a third field, `phase`, because that viewer has no opening ship left
  to ask. It discloses nothing the next turn does not — either terrain appears there or it does not.
- **The deviation still applies**, and the log line's verb changes to "phases in from hyperspace".
  Navigating out of hyperspace is the same job however the hull does it, and an Ancient's −5 already
  makes a Shadow arrival precise most of the time (§2.5).

#### 9c — on a contested gate, only the nearest team's signal is drawn
> "When two or more teams signal a Jump Gate to open, we should only show the ballistic hex icon for
> the closest team at the moment of signalling (to show who will win). If it's tied we can show both
> as it's still unclear."

A gate claim is a fire order on the **gate's** engine, and fire orders become public from phase 2 —
so two teams signalling one gate put two markers on one hex, in whatever colours they happened to be,
one of them already dead. `JumpEngine::maskLosingGateClaims` now thins them, called from
`TacGamedata::deleteHiddenData`.

- ⭐ **IT IS A MASK, NOT A RENDERING RULE, AND IT COULD NOT HAVE BEEN ANYTHING ELSE.** The distance
  that settles the contest is measured to the claimant's nearest qualifying unit, which the claim
  records in `targetid` — and `targetid` is masked to −1 for every viewer it does not belong to
  ([[project_jump_gates]] §2.1: which unit signalled is never revealed). A client cannot measure a
  distance from a unit it has been told nothing about.
- ⚠️ **IT MUST RUN BEFORE `hideSystemFireOrders`**, which is what makes `targetid` readable at all.
  Run it after and every enemy claim looks like a claim naming nothing — which the mask reads as
  "cannot win" — so a player would see only their own marker on every contested gate, always,
  whoever was nearer.
- ⭐ **IT LEAKS STRICTLY LESS THAN BEFORE.** Every claim it drops was already public; nothing it keeps
  was not. A viewer learns exactly one new thing — that somebody nearer has claimed the same gate —
  which is the thing the user asked to be told.
- ⚠️ **BY TEAM, NOT BY PLAYER.** Two allies may both signal one gate; between them they hold one
  position on the map. The RESOLUTION is still per player (`resolveGateClaims` takes one claim per
  userid and rolls off ties between userids); this is a drawing rule and it deliberately does not try
  to predict the roll-off, which is why a distance tie shows both.
- ⚠️ **COUNTED IN CLAIMS, NOT IN TEAMS.** The first cut was `count($bestByTeam) < 2`, and a lapsed or
  unresolvable claim has no team — so a contest between one live claim and one lapsed one looked
  *uncontested* and both markers stood, the far one included. The harness caught it.
- **Replay is unaffected**, deliberately: `deleteHiddenData` does not run for a past turn
  (`$all = true`), so a post-mortem still shows every claim that was made.
- **Not gated on the reinforcements rule**, deliberately: a contested gate is a Phase 2 situation that
  predates reinforcements, and both flavours of claim are masked by the same rule. The efficiency
  gate is the terrain test — a game with no jump gate pays one pass over its terrain and stops.

#### 9d — the arrival initiative penalty (the stage as originally scoped)
`BaseShip::getReinforcementArrivalIniModifier`, added to `getCommonIniModifiers` so it reaches both
initiative generators (`Manager::generateIniative` and `SimultaneousMovementRule`).

**−5 per hex of scatter, −10 per 60° of facing shift, on the arrival turn only.**

- ⚠️⚠️ **THE ×5 IS NOT DECORATION.** FV initiative is d100 and **every** modifier in `ShipClasses` is
  five times its tabletop value — the crit lines say so in their own comments (`-5` is written "−1 Ini
  per crit", `-20` is "−4 Ini per hit"), as does `-10` per point of speed below 5. Written ×1 this
  rule would be worth a fifth of a point and would still **look** implemented.
- ⭐ **THE SCATTER BELONGS TO THE DOORWAY, NOT TO THE UNIT.** `JumpEngine::getArrivalScatter` resolves
  `arrivalVia` to the opener and reads `getVortexScatter()` off its engine, so every unit riding one
  doorway takes the same disorder. That is the rule: a wave that comes out four hexes off course and
  turned sideways is disordered *as a wave*, and the rider's own sensors had nothing to do with it.
- **A GATE'S DOORWAY COSTS NOTHING**, and that is a rule rather than a gap: a gate's jump point does
  not deviate (§2.4), so `getVortexScatter()` is null on it and null is the whole of "no penalty".
- ⚠️ **ARRIVAL TURN ONLY** (`isArrivingReinforcement`, i.e. `arrivalTurn === this turn`). Not "has
  arrived": a unit that came through a badly-scattered doorway three turns ago is an ordinary ship.
- **`abs()` on the facing steps.** `openExitVortex` stores them signed and shortest-way-round (−2..3)
  because the log line reads better for it; turning left is exactly as disordering as turning right.
- **Stage 6 recorded the roll for exactly this**, in the `'VortexScatter'` note, because a d20 and two
  dice cannot be re-derived afterwards. Stage 9 is the reader that note was written for.
- ⚠️ **KNOWN AND NOT FIXED:** an arriving reinforcement is *also* still paying the ordinary
  `speed < 5` penalty (−50 at speed 0), because initiative for turn N+1 is rolled at the end of turn N
  and the wave does not set its speed until the Deployment phase of N+1. That is pre-existing
  behaviour shared with every Delayed Deployment Slot, it is arguably right (they arrive slow), and it
  is out of Stage 9's scope — but it means the scatter penalty **stacks on top of** a −50 that is
  already there. Worth a look after the play test if arrivals feel over-punished.

#### 9e — the efficiency gates ("blast radius")
> "Ensure we've gated any intensive checks in the code with a check for the Reinforcement rule being
> in place, as best we can."

Audited every sweep the feature added. Most were already cheap — `hasReinforcementsArriving`,
`releaseUnplacedReinforcements` and `stampArrivingReinforcements` all open on the plain
`$ship->reinforcement` property read, and `spawnExitVortices` and `persistManifest` already carried
the rule gate. Three did not:

- **`TacGamedata::hideHyperspaceReinforcements`** — the per-viewer load path, the hottest sweep the
  feature touches. Gated **after** the defensive slot zeroing so that still runs in every game.
  ⚠️ **It fails OPEN**: the test is "rules exist AND say no", never "rules do not say yes". A load
  that somehow arrives without a `GameRules` object still does the masking — a missing object must
  never become a concealment failure ([[arch_info_bleed_masking]]).
- **`ReinforcementEntry.myHyperspaceUnits`** — every dialog, `isOffered`, `canSignalJumpGateForArrival`
  and `strandedByCommit` reach hyperspace through it, so one test switches the module off wholesale
  instead of filtering every ship on every UI refresh for the whole battle.
- **`BallisticIconContainer.generateExitHexes`** — runs on every ballistic redraw and walks
  `gamedata.slots`.
- **`BaseShip::getReinforcementArrivalIniModifier`** puts the cheap flag test *first* and the rule
  test second, on purpose: `getCommonIniModifiers` runs for every ship at every turn advance, so an
  ordinary game pays one boolean per ship per turn and nothing else.

`gamedata.reinforcementsAllowed()` is the new game.php twin of the lobby's helper. ⚠️ **It is an
efficiency gate and never a security one** — every rule behind it is also enforced server-side.

**Proven headless.**
- `tests/replay/reinforcementsStage9Harness.php` — **108 assertions across games 4277/4307/4312, all
  passing, zero database writes** (`Manager::$dbManager` stubbed by reflection; the two private
  methods reached the same way). It builds a real `PhasingDrive` and a real `JumpgateCapital` on top
  of real units in real recorded games, and fabricates the contested geometry by giving two of the
  player's hulls userids and teams that exist nowhere else in the game plus a deploy row at a hex the
  file chooses — which is the only way the **tie** case is reachable at all. ⚠️ Two traps the harness
  itself hit and now documents: a unit left with `reinforcement = true, arrivalTurn = null` answers
  **999** to `getTurnDeployed`, so its claim lapses and the whole contested section goes quietly
  vacuous; and a `FighterFlight`'s `->systems` are FIGHTERS, so hanging a synthetic Jump Engine on one
  fatals in `getSystemByName`.
- `tests/replay/reinforcementsStage9ClientHarness.js` — plain `node`, **26 assertions**, evaluated
  against the **real** `movement.js` and `ships.js` rather than stubs of them (the Stage 8 client
  harness stubs `shipManager.movement` on purpose — it is testing ReinforcementEntry's questions; this
  one is testing movement's answers). It proves the two class names, that `shouldBeHidden` hides the
  phase-in doorway and **not** the ordinary one, that `canOpen` accepts a legacy drive and still
  refuses a wreck and a gate, and that the rule gate empties the module without touching
  `gamedata.ships`.
- **Replay harness: 157 passed, 0 failed** after re-recording. The only difference the swap produced
  anywhere in the corpus was the payload key `formingEntrances → formingExits`, on 619 **empty**
  arrays — no value changed anywhere. (The re-record also picked up game 4318, which had advanced
  since it was last recorded, and game 4319.)
- Stage 6 (177), Stage 7 (80), Stage 8 (130) and the Stage 8 client harness (34) all still pass.

⚠️ **The legacy bundles need regenerating** (`scripts/fvbuild.ps1 -Client`) before this is exercised in
production mode — nine client files changed and `game.php` serves them from `game.legacy.bundle.js`
outside dev mode.

### Stage 10 ✅ BUILT 2026-08-29, untested in play
Four refinements asked for immediately after Stage 9, all of them small in code and two of them
load-bearing in rules. Read §2.3 first — 10a rewrote it.

#### 10a — the deviation is rolled after Initial Orders, and the marker moves to the real hex
> "I've re-read the tabletop rules and realised that the vortex deviation roll (and any movement of
> the jump exit) should be after Initial Orders on its formation turn, instead of the very end of
> the formation turn. This way, the player has already committed the ships and both players can see
> where the actual exit will be on formation turn to react to it, instead of having to wait until
> the start of the arrival turn."

§2.3 carries the whole ruling, the reversed concealment argument and the three traps. In code:

- **`JumpEngine::spawnExitVortices` moved to `InitialOrdersGamePhase::advance`**, third of the three
  vortex sweeps — after `spawnDeclaredVortices` and `openSignalledGates`, which means the clamp that
  keeps the rolled hex legal already sees this turn's entrances and gate vortices for free.
- **It was SPLIT.** `JumpEngine::stampExitManifests` is the manifest half and stays at the end of
  `FireGamePhase::advance`, where `closeExpiredVortices` has already run. §2.3's ⚠️⚠️ says why that
  is a rule and not a tidy-up, and why `$opened` is now rebuilt from the board with
  `holdsExitOpenOn` rather than carried across.
- **`openExitVortex` gained two out-parameters** (`$logOrders`, `$moved`) and a
  `rewriteDeclaration` call. Initial Orders' `advance()` has no submit of its own, so the sweep
  persists exactly the rows it wrote — the log line through `submitFireorders(..., 2)` and the moved
  declaration through `updateFireOrders`. ⚠️ Collected, never re-scanned out of `getNewFireOrders()`:
  `spawnDeclaredVortices` runs first in the same `advance()` and has already submitted its own, and
  a second scan would insert every one of them twice. This is the trap `openSignalledGates`
  documents, now with a third sweep behind it.
- ⚠️⚠️ **The two early returns in `InitialOrdersGamePhase::advance` had to call the sweep too**, and
  the exit sweep is the only one of the three that needs it — see §2.3.
- ⚠️⚠️ **`rollVortexJumpFailure` needed an explicit exit refusal**, because the "it falls out for
  free" that Stage 6 recorded was a statement about *ordering* and the ordering just changed — see
  §2.3.
- **Nothing on the client changed.** That is the design, not luck: both viewers draw the marker from
  the one fire order the sweep rewrites.

⚠️ **The DECLARED hex is no longer recoverable** once the roll has happened — the `'VortexScatter'`
note records the distance and the facing steps, not the origin. Nothing needs it (the log line
quotes the distance, Stage 9's penalty reads the note), but a future "show me where they aimed"
feature would have to widen the note.

**Idempotency is unchanged and now covers one more thing.** A second `advance()` breaks out on
`hasOpenVortex` before reaching `openExitVortex`, which is what stops the ALREADY-MOVED hex being
put through the deviation table a second time — a compounding scatter, every time an advance ran
twice. The Stage 6 harness asserts it directly.

#### 10b — the arrival initiative penalty is visible
> "For the Stage 9 Initiative Penalty, can we have a shipTooltip message and a shipstatus banner in
> shipWindow, both using the same styles as Hangar initiative penalty notification and using the
> same cyan colour styles."

A cyan `Arrival Scatter (-25 Ini)` line in `ShipTooltip.js`, beside Hangar Operations and Just
Launched, and a cyan `ARRIVAL SCATTER -25 INI` banner in `ShipWindow`'s `getStatusBanners`, straight
after Deploying. Both read one function, `shipManager.getArrivalIniPenalty`.

- ⭐ **THE NUMBER IS THE SERVER'S, NOT A CLIENT RE-DERIVATION.** `TacGamedata::stripForJson` attaches
  `arrivalIniPenalty` from `BaseShip::getReinforcementArrivalIniModifier` — the same method both
  initiative generators call — so the UI cannot quote a figure the roll did not apply. On a d100
  roll that is exactly the disagreement nobody would notice and everybody would argue about.
- ⚠️ **IT IS ATTACHED IN `TacGamedata::stripForJson`, NOT IN `BaseShip::stripForJson`** where every
  other reinforcement field lives, and that is forced: the answer needs a **gamedata** (the scatter
  is rebuilt from an `IndividualNote` on the OPENER's engine, so it takes a walk to another ship),
  and a ship's `stripForJson` is handed none. The obvious alternative — a public
  `$arrivalIniPenalty` on `BaseShip` — would ride the static blueprint of every hull in the tree,
  because `ShipCompactor` walks the RAW object rather than that method
  ([[arch_shipcompactor_key_stripping]]).
- **Emitted only when non-zero**, so an ordinary payload is byte-identical and the key's presence is
  the client's whole test. A precise arrival and a gate's doorway both produce 0, and both are the
  good outcome — neither should carry a banner.
- **It discloses nothing new.** `iniative` / `iniativeadded` already go out real to every viewer
  (the Chameleon sheet says so in as many words), the arriving unit is on the board, and its
  doorway's hex has been public since it formed.
- The `array_map` over `$this->ships` became a `foreach`, which now re-indexes unconditionally.
  ⚠️ That does **not** make `hideHyperspaceReinforcements`' rebuild redundant — it also drops the
  `$shipsById` cache.

#### 10c — legacy jump drives show their real recharge
> "Shadow PhasingDrives and other markLegacy Jump Drives, such as Hyperdrives and FTL Drives on Star
> Wars and BSG factions respectively, are not correctly showing their recharge time like normal
> JumpEngines, they just show 1/1. Star Trek Nacelles are out of scope of this fix."

⭐⭐ **ONE DISTINCTION, SEPARATED.** `markLegacy()` forced `loadingtime`/`turnsloaded` to 1/1 and
`JumpEngine::stripForJson` short-circuited on `$legacyJump`, on the reasoning that a legacy drive
never opens a jump point and so never spends a charge. **Stage 9 made that false** — a Shadow, Star
Wars or BSG hull now phases IN through a doorway of its own and spends the charge doing it — so the
flag was hiding a rule that only ever applied to one family.

- **`JumpEngine::$hasJumpRecharge`** now means "my 4th constructor argument is a jump delay". True on
  every engine in the tree except the Trek Nacelle, whose 4th argument is an **impulse rating**
  (`TrekImpulseDrive` sums the nacelles' outputs for sublight thrust) — a Nacelle rated 6 would
  otherwise claim a 6-turn jump recharge it does not have.
- **`markLegacy($keepsRecharge = true)`**, and the ONE caller that passes `false` is
  `TrekWarpDrive::__construct`. The default keeps all 88 ship-file calls byte-identical in intent:
  they always passed a real B5W jump delay.
- **`stripForJson` now tests `!$hasJumpRecharge`** instead of `$legacyJump`, so a phasing hull sends
  the real loading pair — and the vortex counter block below it, which is right for it too: a
  phase-in doorway is a vortex, invisible or not, and its holder's icon should count it.
- ⚠️ **NOTHING ELSE ABOUT `markLegacy` MOVED.** The boost path back on and the declaration path off
  are what the flag is for; the harness asserts both are identical either way.
- ⚠️ **THE STATIC BLUEPRINTS CHANGE** for the ~195 hulls that mount one — `loadingtime`/`turnsloaded`
  are public `Weapon` properties and `ShipCompactor` strips neither. `fvbuild.ps1 -Server` is
  required, and the lobby reads the blueprint alone.
- **`weaponManager.isLoaded` now answers false on a recharging legacy drive.** Audited: the engine is
  already out of `weaponManager.targetHex` (`autoFireOnly` / `hextarget` false), the ballistic
  "unfired launchers" checklist skips `name === 'jumpEngine'` outright, `canBoost` never consults
  loading, and the server's only charge test lives in the ENTRANCE branch that refuses a legacy drive
  above it. The one visible effect is the intended one: `SystemIcon` dims a recharging drive.
- **The boost jump is deliberately NOT gated on the charge.** A phased-in hull can still boost out
  the same turn if it wants to; that is pre-existing legacy behaviour and this was a display fix.

#### 10d — the blast-radius pass
> "Can you do a Blast radius check for all REINFORCEMENTS changes for safety and efficiency e.g.
> Ensure we've gated any intensive checks in the code with a check for the Reinforcement rule being
> in place, as best we can. Plus, make extra sure we're not breaking any existing game mechanics
> with these expansive changes."

Stage 9e did the sweep audit; this one re-ran it over the Stage 10 additions and then went looking
for **mechanics** rather than for cost.

**Gating.** Every sweep Stage 10 adds opens on the rule: `spawnExitVortices` and
`stampExitManifests` both, and the payload half in `TacGamedata::stripForJson` resolves the rule
**once outside the loop** and then tests the plain `$reinforcement` property per ship — so an
ordinary game pays one boolean per ship per payload. The `rollVortexJumpFailure` refusal sits
*after* that method's two cheap tests, so the ship lookup is only paid on a turn a vortex was
actually opened or maintained. `shipManager.getArrivalIniPenalty` is a property read.

**Mechanics.** The window that changed is turn N's Movement, Pre-Firing and Firing phases, which now
contain an exit vortex unit that did not exist there before. Everything that could see it was walked:

| asked | answer |
|---|---|
| Can a ship collide with it? | No. `SpawnJumpPoint` sets `Enormous = false` deliberately, which is also what keeps it out of `blockedHexes` — the note in that class says so. |
| Can a ship jump OUT through it? | No. `Movement::getOpenVortexInHex` and `applyJumpOut` both `instanceof SpawnJumpPointExit` → refuse (§2.6), on both sides. |
| Can it be MAINTAINED? | No. `Firing::getVortexDeclarationBlock`'s mode-7 branch refuses an exit explicitly. |
| Does it close early? | No. `getVortexClosureReason`'s one-shot branch is `turn > openTurn`, and `openTurn` is still N. |
| Does it change the JUMP-FAILURE roll? | It would have. Fixed — see §2.3. |
| Does it block LoS / firing? | No — not `Enormous`, so it never reaches `blockedHexes`. |
| Does a second exit clamp around it? | Yes, and better than before: it is inserted a phase earlier, so a same-turn second exit and next turn's declaration test both see it. |
| Is it new information? | No. The vortex row carries the slot and facing the republished `formingExits` entry already published from phase 2. |

**All of it is the entrance vortex's existing situation**, which has spawned at the end of Initial
Orders since Jump Points Stage 3 — which is the reassuring half of the answer: Stage 10a did not
invent a state, it put the exit into one the codebase has been living with for months.

**Proven headless.**
- `tests/replay/reinforcementsStage6Harness.php` — **192 assertions** (was 177). The new ones: the
  declaration is moved onto the hex the doorway formed at, its firing mode carries the facing the
  doorway actually got, the sweep submits its own log order, a declaration with no DB id is not
  queued for an UPDATE that would match nothing, and a second `advance()` does **not** re-roll the
  moved hex.
- `tests/replay/reinforcementsStage9Harness.php` — **123** (was 108), the new ones on the payload
  field: it is emitted for the opener and every rider, for **no** other unit in the game, not at all
  on a precise arrival, and not at all without the rule. ⚠️ Two synthetic engines the file hangs on
  real hulls now need `setUnit()`: `ShipSystem::stripForJson` reaches back through `$this->unit`, so
  any payload built from that gamedata fatals without it.
- `tests/replay/legacyRechargeHarness.php` — **NEW**, 33 assertions over a real Shadow, Star Wars,
  BSG and Trek hull plus the flag itself.
- `tests/replay/reinforcementsStage9ClientHarness.js` — **32** (was 26), the new ones on
  `getArrivalIniPenalty`, including that 0 reads as "no banner".
- Stage 7 (80), Stage 8 (130) and the Stage 8 client harness (34) unchanged and still passing.
- `checkShipData.php`: **236 findings, all in baseline, 0 new.**
- **Replay harness: every diff in the corpus is one of the three expected payload keys** —
  `loadingtime` (52), `turnsloaded` (48) and `arrivalIniPenalty` (4). No movement, to-hit, damage,
  critical or masking difference anywhere. Baseline re-recorded to accept them.

⚠️ **`fvbuild.ps1 -Server` IS REQUIRED** for 10c (the static blueprints carry the legacy drives'
loading pair), and the legacy + React bundles need rebuilding for 10b.

---

### Stage 11 — four refinements ✅ BUILT 2026-09-02, untested in play
> "1. Can you add a 'Jump Manifest' button in the Manage Reinforcement menu, between the current
> action button and cancel buttons, and only show it when a selected unit already has a jump point
> scheduled. 2. Clicking the new button opens the normal Jump Manifest menu and lets the player make
> changes without having to cancel and re-do their jump point. Teams with no Reinforcements can still
> signal a Jump Gate to open a Jump Exit."

and, once 11a/11b were in play:

> "That's working great, can we also add a 'Back to Manage Reinforcements' button to Jump Manifest
> next to the Confirm button. Also, I've noticed the ticker buttons for Jump Gate signal menu are a
> little sticky to press, i.e. if I press twice in quick succession the 'Open for' value only changes
> once."

**The theme of the first three is that the menu and the manifest are now a LOOP** rather than a
one-way funnel: 11a is the way in, 11c is the way back out, and between them a wave can be re-named
as many times as the player likes without a single declaration being withdrawn. 11d is unrelated — a
bug in the gate Signal panel's stepper, reported alongside.

#### 11a — the manifest gets a way back in

Up to here a manifest was named **exactly once**, on the way out of the transaction that made the
doorway: `createExitOrder` ends with `showManifestDialog`, and so does `createGateSignalOrder`
(Stage 8). There was no second door. Changing one's mind about a single passenger therefore meant
withdrawing the jump point, re-picking the hex, re-turning the facing and naming the whole list
again — four gestures to undo one tick, and three of them re-decisions the player did not want to
make.

**A third button on the Manage Reinforcements menu**, between the row's own action and Cancel:

| the selected row | primary | Jump Manifest |
|---|---|---|
| a drive with no declaration | Choose Hex | — no doorway yet |
| a drive holding its own jump point | Withdraw Jump Point | **yes** |
| a gate this client signalled this turn | Withdraw Gate Signal | **yes** |
| a gate already holding a doorway | Select Reinforcements | — the primary button *is* the manifest |

⭐ **And it hides when there is nobody left to offer.** `manifestRiders()` — split out of
`showManifestDialog` so the button and the dialog ask one question rather than two — is the list of
hyperspace units not already spoken for by a *different* doorway. Empty means the only outcome of
pressing the button is a notice, so the button is not there: a lone drive that has declared its own
exit has nothing to manage.

Mechanically it is `data-manifest` on the row's radio, written by `openerRow`/`gateRow` beside
`data-action` and `data-gate`, and `syncLabel` moves it onto the button on every selection change and
every re-render — so a withdrawal takes the button away in the same breath as the OPENING tag. The
click **re-derives the doorway from live gamedata** (a poll can land while the dialog stands, trap
17) and closes the menu before opening the manifest, exactly as the gate's own Select Reinforcements
has always done. The paint is a new generic `.confirmalt` in the `.fleetDialog` button row — neutral
rather than accented, because the affirmative action is still the one on its left.

#### 11b — an arrival claim no longer needs anything of your own in hyperspace

Stage 8 gave a `'gateexit'` claim two extra rules: the game must have the reinforcements rule, **and**
the claiming player must have something still waiting. The second is gone (user ruling).

**Why it was wrong in two directions at once.** A gate exit stands for the whole of its programmed
hold and *any* unit of *any* side may ride it (JUMP_GATES_PLAN.md §2.6). So the test barred a player
from opening a doorway their **teammate's** wave would come through, and barred opening one this turn
for a wave that would only be ready to ride it on a later one.
`InitialOrdersGamePhase::collectGateOpeners` has always accepted **anybody's** standing gate exit as
an opener a manifest may name — that half of the feature already worked — so this test was the only
thing stopping the door being opened in the first place. Whether a door is worth the gate's charge is
the player's call.

**What did *not* change:** the rule gate. A game without `allowReinforcements` still refuses an
arrival claim on both sides, and the client predicate had to state it explicitly
(`gamedata.reinforcementsAllowed()`) now that it no longer gets it for free from an empty
`myHyperspaceUnits()`. The **departure** claim is untouched, as it has been since Phase 2.

Three sites: `gamedata.canSignalJumpGateForArrival`, `Firing::getGateSignalBlock` (and its now-unused
`hasHyperspaceReinforcements` helper, deleted), and the wording of the `createGateSignalOrder` error,
which is now about the rule rather than about the fleet. `showManifestDialog` grew a third empty-list
message for the state this makes reachable — a gate doorway with nothing at all in hyperspace behind
it, as against "they are all riding something else".

#### 11c — and the manifest gets a way back OUT
> "Can we also add a 'Back to Manage Reinforcements' button to Jump Manifest next to the Confirm
> button."

The other half of 11a: a fleet with two drives and a gate is three doorways to name, and every one of
them used to end by dropping the player back onto the map to find the menu again. `.confirmalt` a
second time, beside Confirm.

⚠️ **It COMMITS the ticks; it does not discard them.** This dialog has no discarding half at all —
its Cancel was deliberately removed, because "closing with nothing ticked" is a legal answer and a
Cancel could not say whether it also undid the declaration. "Back" therefore means *"and now show me
the menu"*, exactly as Confirm means *"and now let me get on"*; a Back that silently threw the ticks
away would be that same ambiguity wearing a different word.

Offered on **every** path that opens the manifest, the two that were not reached from the menu
included (a fresh `createExitOrder` declaration, and the gate Signal panel). "Name the wave, then set
up the next doorway" is the same workflow whichever door was just opened, and
`manageReinforcements()` always has at least the row this manifest belongs to — so it can never land
on its empty-handed error, and its single-candidate shortcut can never fire either (that candidate
now holds a declaration).

#### 11d — the signal panel's stepper was sticky
> "the ticker buttons for Jump Gate signal menu are a little sticky to press, i.e. if I press twice
> in quick succession the 'Open for' value only changes once."

⭐⭐ **A de-duplication guard that only looks at the clock cannot tell a second press from a ghost.**
The panel's buttons are bound to `"click touchstart"` because a touch fires touchstart and then a
synthetic click ~300ms behind it, which would step the duration twice per tap. The guard refused any
second event on the same control inside **350ms** — which did de-duplicate the tap, and also ate the
second of two deliberate mouse clicks. On a stepper, where pressing repeatedly *is* the gesture, that
is the wrong half to throw away.

**The fix is to look at the event TYPE**, which separates them exactly: a `touchstart` always acts and
stamps the window; a `click` acts unless it is the tail of a touch on this same control — which is
precisely what the synthetic one is, and what a real mouse click can never be. Two rapid taps still
behave, because the second touchstart re-stamps the window and it is that tap's own ghost click the
window then swallows. `swallowDoubleEvent` → `swallowSyntheticClick`, and `lastActionTime/Key` →
`lastTouchTime/Key`, since the stamp now means something narrower.

⚠️ **`UI.vortexFacing` has the same defect and a worse version of it** — its guard is a single
*unkeyed* 350ms stamp shared by all three buttons, so rotating twice quickly, or rotating and
immediately confirming, is swallowed. Not touched here; it is the facing control, not the reported
control.

**Proven headless.** `tests/replay/reinforcementsStage8Harness.php` — **130 passed**, with the
"refused with nothing in hyperspace" assertion **inverted rather than deleted**, so the absence of the
rule is proven rather than assumed. `tests/replay/reinforcementsStage8ClientHarness.js` — **39** (was
34), the five new ones on `data-manifest` across all four row states.
`tests/replay/gateSignalTickerHarness.js` — **NEW**, 13 assertions, and ⭐ **it was run against the
pre-fix file and fails exactly the three double-click assertions there**, so it is proving the change
rather than passing vacuously. It works because `UI.gateSignal` is a plain object literal whose every
jQuery reach is inside a method: the module loads in a `vm` with nothing but a `window`, and `Date` is
replaced so a "rapid" press is 40ms rather than something the file has to sleep for. Stage 6 (192),
7 (80), 9 (123), the Stage 9 client harness (32), the tooltip harness (14) and the Vortex Disruptor
client harness (30) all unchanged and still passing.

⚠️ **Legacy bundles need rebuilding** (`node scripts/bundle-legacy.js`, or `fvbuild.ps1 -Client`) —
this is legacy JS and CSS only, so no autoload, no statics and no React build.

---

## 5. Traps

1. **The two positional `tac_ship` INSERTs.** §3.1. This one will bite on the first commit.
2. **`getTurnPlaced` must not subtract 1.** §3.2. Silent either way: the wrong twin makes the
   Deployment phase unusable (nothing selectable, commit never arms) or makes a non-existent unit
   act. Trap 1 of [[arch_placement_turn_vs_deploy_turn]], and it is still the most expensive
   mistake available here.
3. **POST-side ships have no `reinforcement` or `arrivalTurn`** unless whitelisted, and only
   `arrivalVia` is. Every server rule must resolve through `$gamedata->getShipById($ship->id)`
   ([[arch_post_side_ship_reconstruction]]).
4. **The `'Vortex'` note format is untouchable** — its third field is free text full of commas,
   parsed with `explode(',', $v, 3)`. Exit-ness rides the **class**; scatter rides a new
   additive `'VortexScatter'` note. ⚠️ `notekey_human` is `varchar(40)` and an overflow is a mysqli
   1406 that aborts the whole submission, not a truncation.
5. **A one-shot exit must release its engine — and ONE-SHOT IS A SHIP'S RULE, NOT THE VORTEX'S.**
   `spawnVortexUnit` opens with `if ($this->hasOpenVortex($gamedata->turn)) return null;`. If the
   exit does not close at the end of the arrival turn, the ship that opened it can never open an
   entrance for the rest of the game. `closeExpiredVortices` needs an exit clause that disturbs
   neither the ship branch nor the `holdsGateEngine` narrowing.
   ⚠️ **Stage 7 wrote that clause ahead of the gate branch and Stage 8 had to reverse it**
   (`&& !$this->isGateJump()`): §0 gives a GATE'S exit the gate's **programmed hold**, not one
   turn. Both orderings are defensible from first principles and only the user ruling settles it —
   which is why the reversal is a rule, not a tidy-up. Getting it wrong slams a 4-turn doorway shut
   after one turn, silently, while looking exactly like a working feature.
6. **Removing ships from the payload** must re-index `$this->ships` and rebuild `$shipsById`. §3.6.
7. **The three facing-arrow constants are kept in step by hand** and now there are six. §3.7.
8. **`generateIndividualNotes` deploy guards.** Only three exist
   (`grep -rn getTurnDeployed source/server | grep return`); Hangar's is the one that matters. §4
   Stage 7.
9. **The static generator skips a class whose filename does not match, silently.** §3.3.
10. **A ballistic order's `targetid` is read as "hang the marker on that unit"** by every
    ballistic-icon path — `'jumppoint'` orders are already forced to `targetid = -1` throughout
    `createBallisticIcon`. `'jumpexit'` must be too, or the blue marker will be drawn over the
    opener, which is in hyperspace ([[project_jump_gates]], trap 2).
    ⚠️ **And `'gateexit'` (Stage 8) is the sharper case, because there the leak is real rather than
    merely wrong.** A gate claim's `targetid` carries the CLAIMING PLAYER, recorded as their nearest
    qualifying unit — so a flavour left out of `isVortexDeclaration` hangs the marker on **that ship**
    and runs a bright line to it from the gate, on the claimant's own screen, before the server has
    masked anything. Which unit signalled is never revealed ([[project_jump_gates]] §2.1). **Every
    new vortex-ish `damageclass` must be added to that test and given a `case` in the label switch**;
    without the second it falls through to the default **red** hex and reads as incoming fire.
11. **Hex direction 0 is EAST and increases CLOCKWISE.** Both libraries agree. Do not re-derive it
    ([JUMP_POINTS_PLAN.md §2.2](JUMP_POINTS_PLAN.md)).
12. **`Manager::insertSingleShip` casts the id to int** — do not bypass it
    ([[arch_spawned_ship_string_id_trap]]).
13. **Leaving a slot recycles `tac_ship` ids.** No new shipid-keyed table is added here, so
    `deleteGames()` / `leaveSlot()` need nothing — but the three new columns live on `tac_ship`
    itself and go with the row ([[arch_orphan_shipid_rows_recycled_ids]]).
14. **`$reinforcement` on a POST-side ship is a landmine, and only `$reinforcementClaim` may cross
    the wire.** Stage 1's entry above has the reasoning; the two live sites are
    `Hangar::generateIndividualNotes` and `HangarOps::validateDeployBayOrders`, and **Stage 7 runs
    straight through both**. Fix them there by resolving through `$gamedata->getShipById($ship->id)`,
    the way `DeploymentGamePhase::validateDeployment` already does.
15. **`phpclass` is the persisted class identity, not `get_class()`.** Any future vortex subclass
    must set it in its own constructor or it reloads as its parent. §3.3 and Stage 3.
16. **A new spawnable class needs THREE registrations, not one:** the autoload map (or
    `class_exists()` is false and the static generator skips the file *silently*),
    `static/json/<faction>.json` for the lobby, and `JumpEngine::$spawnableClasses` for
    `BlueprintCache` — which is what `game.php` actually reads, and the only one that matters for a
    unit that appears mid-game on a poll.
17. ⚠️⚠️ **A HELD SHIP OBJECT GOES STALE ON EVERY POLL.** `gamedata.setShipsFromJson` replaces every
    entry of `gamedata.ships` with a fresh `new Ship(...)`, so any client state that outlives a
    single event — a bespoke map mode, a dialog, a pending transaction — must hold an **id** and
    re-resolve through `gamedata.getShip()`. The failure is silent: an order pushed onto a discarded
    object's `fireOrders` never reaches the POST. Stage 4.
18. **`Manager::updateLateDeployments` runs AFTER `prepareForPlayer` and writes to the database off
    a per-viewer masked ship list** — the only such write in the game. It is safe today only because
    `getMinTurnDeployedSlot` reads that list solely to look for terrain/OSATs/bases, every one of
    which §3.6's predicate keeps. Any future masking rule that removes one of *those* breaks another
    slot's Deployment scheduling, per viewer, in the database.

19. **A REINFORCEMENT'S BALLISTIC ORDER HAS NO SHOOTER POSITION, and `TacGamedata::onConstructed`
    assumed every ballistic shooter had one.** (Fixed 2026-08-28, user report.) The Jump Engine is
    `$ballistic`, so an exit declaration is an ordinary ballistic fire order — but its author is
    in hyperspace, and `getLastTurnMovement()` skips every `'start'` row, so it answers **null**.
    `array("x" => $movement->position->q, …)` was then a fatal `ErrorException` on **every gamedata
    load from phase 2 of the turn the exit was declared**, for both players, on every poll:

    ```
    Attempt to read property "position" on null … TacGamedata.php (171)
    ```

    ⚠️ **Phase-1 secrecy hid it.** `hideSystemFireOrders` strips every current-turn ballistic order
    from every phase-1 payload *including its author's*, so the declaration is invisible to this loop
    until Initial Orders are committed — which is exactly when the error appeared, and why it read as
    "committing orders breaks the game" rather than "declaring an exit does".

    The guard is `if ($movement === null) continue;` — the general rule, deliberately not a
    `damageclass === 'jumpexit'` test. Nothing wants the record either: `$this->ballistics` is
    absent from `stripForJson` so it never reaches the client, the marker is drawn from the fire
    order itself (`exitOrders`, or the slot's `formingExits` for an enemy viewer), and the
    only server-side consumer is the `hidetarget` mask — which an exit is not subject to,
    `hideHyperspaceReinforcements` having deleted the whole unit first.

20. ⚠️⚠️ **A NEW `IndividualNote` KIND ON THE JUMP ENGINE MUST BE CLAIMED IN
    `onIndividualNotesLoaded`, OR IT CORRUPTS `$preJumpValue`.** That loop ends in a fall-through
    which treats every note it does not recognise as the legacy `jumped` note and assigns its value
    to `$preJumpValue` — the ship's pre-jump combat value. `'Vortex'` and `'VortexHold'` each
    `continue` for exactly this reason, and Stage 6's `'VortexScatter'` had to as well. It fails
    silently, on the ship that opened the doorway, and only for games that use the feature.

21. ⚠️ **`OffsetCoordinate::distanceTo` RETURNS A FLOAT, so `=== <int>` IS ALWAYS FALSE.**
    `CubeCoordinate::__construct` rounds every ordinate with `round(..., 4)` and PHP's `round()`
    returns a float. Stage 6 caught it in test assertions only; **Stage 7 is where it can reach
    shipped code** — "the unit was placed in the vortex's hex" is exactly the shape of comparison
    that will be written with a `===`. Use `==`, or cast. (`equals()` on the coordinate objects is
    safe: it compares `q`/`r`, which are `(int)` cast in the constructor.)

22. **A vortex spawned mid-sweep is Terrain and is on the board IMMEDIATELY.**
    `Manager::insertSingleShip` appends to `$gamedata->ships`, so anything asking a hex question
    later in the same request sees it — which is what makes a second exit in one sweep clamp
    around the first, and what makes any "is this hex free" test written for Stage 7 need to think
    about whether it means *before* or *after* this turn's doorways exist.

23. ⚠️ **`getVortexHeldBy` / `isJumpVortex` ARE ENTRANCE-ONLY BY DESIGN, so any rule that means "is this
    unit holding a jump point at all" must ask `getExitHeldBy` beside them.** (Stage 8, client.)
    The two predicates were deliberately kept separate because their callers do not agree on the
    verdict — `canJumpOut`, the Maintain control and the closing-vortex warning are all rules an
    exit must FAIL, while the icon's z-plane and the hex-stack sweep are things it must MATCH —
    so the temptation is to widen `isJumpVortex` and the correct move is never to. The bite:
    `gamedata.canSignalJumpGate` asked only `getVortexHeldBy` and so read a gate holding an EXIT
    as free to signal, while the server's `hasOpenVortex` knows nothing of flavour and refuses. That
    shape — **client predicate narrower than its server twin** — always surfaces as a button offered
    and then silently rejected at commit, which is worse than either answer alone.

24. ⭐ **WHEN TWO PLACES COULD DECIDE THE SAME THING, PICK ONE AND MAKE THE OTHER LENIENT.** (Stage 8.)
    "Is this berth still good?" is asked in the Deployment phase (`releaseUnplacedReinforcements`),
    in Initial Orders (`collectGateOpeners`) and at end of turn (`collectGateExits`). Only the
    last one runs at a moment when the answer is settled — closure is not written until the end of
    Firing — so the other two keep the berth optimistically and defer. A berth written and cleared
    four phases later costs the player nothing; a strict test at either earlier point that got the
    arithmetic wrong would silently drop a legitimate wave. Say which one is the authority **in the
    comment at every site**, or the next change will "fix" a leniency that is load-bearing.

25. ⭐⭐ **A PREDICATE THAT SAYS "NOT ON THE BOARD" IS NOT THE SAME AS ONE THAT SAYS "CANNOT BE ON
    THE BOARD".** (Stage 8 play test.) `isReinforcement()` is *flagged and no arrival turn yet* — a
    statement about **where a unit is**. A base, an OSAT and Terrain answer `1` to `getTurnDeployed`
    before it looks at the flag at all, so such a hull can carry the flag, stand in plain sight on
    the map, and answer `true` to `isReinforcement()` forever. `hideHyperspaceReinforcements` knew
    this and tested both; `stampArrivingReinforcements` did not, and stamped a signalled jump gate
    as arriving through its own doorway — which bought its owner an empty Deployment phase on every
    turn the doorway stood. **Anything keying off `isReinforcement()` must decide whether it also
    needs `alwaysDeploysTurnOne()`**, which is now the one name for the hull half of the question.
    And the flag has no business being on such a hull at all: refuse it at the buy, on **both**
    sides.

26. ⚠️ **A CONDITION THAT THROWS TAKES THE WHOLE TOOLTIP, NOT ITS OWN BUTTON.** (Stage 8 play test,
    client.) `ShipTooltipMenu.renderTo` evaluates every button's conditions in one `.filter()`, and
    that runs inside the `ShipTooltip` **constructor**, before `show()`. So one TypeError in an EW
    predicate that was going to answer `false` anyway deletes the name, the stats, the ballistics
    list and **Open Ship Details** along with it — and it looks exactly like "clicking that unit
    does nothing". The trigger here was `this.selectedShip === null`, which the gate buttons were
    explicitly designed for and which the array around them had never survived. **Any predicate in
    a tooltip menu that dereferences `this.selectedShip` needs a null guard, whatever the button
    beside it claims** — and a button whose ACTION needs a shooter needs one as a condition, or it
    is offered with nothing to fire it from.

27. ⭐⭐ **SWAPPING TWO WORDS IS NOT TWO RENAMES.** (Stage 9.) The codebase used *entrance* for blue
    and *exit* for yellow, and the user wanted them the other way round. `entrance → exit` run on its
    own would have collided with every existing correct use of *exit* and produced a file where both
    words meant blue. It has to be **one simultaneous pass** with a lookup table, with the unrelated
    idioms (`early exit`, `exit loop`, `exitSourceOverride`, `exitFullscreen`) and the artwork
    filenames sentinel-protected first. **And never rename a class INTO a name that already exists**:
    `SpawnJumpPoint` kept its bare name precisely so no string ever means the opposite vortex either
    side of a deploy — in the database, in a stale branch, in a half-updated file.

28. ⭐⭐ **THE ORDER OF TWO GUARD LINES CAN BE A WHOLE FEATURE.** (Stage 9.) "Shadows phase in" is
    `Firing::getVortexDeclarationBlock`'s arrival branch moved **above** its legacy refusal, and
    nothing else. The refusal stays where it is and still means what it meant. If you are tempted to
    widen `isLegacyJump()` — or to delete it — the rule you actually want is one line further up or
    down. ⚠️ **The asymmetry it produces is the ruling**: such a hull may come back and may still
    never open a way out, so a test must assert BOTH directions on the same engine or it will pass on
    a build that simply deleted the rule.

29. ⚠️ **A UNIT LEFT `reinforcement = true, arrivalTurn = null` ANSWERS 999 TO `getTurnDeployed`, AND
    EVERY "IS IT ON THE BOARD?" SWEEP THEN SKIPS IT.** (Stage 9, caught by its own harness.)
    `getNearestGateSignaller` reads it as "not on the board yet", so a gate claim naming such a unit
    LAPSES — which made the whole contested-gate section of the harness pass vacuously, because a
    contest with only one live claimant is correctly left alone. Any test that flags a unit as a
    reinforcement must un-flag it before reusing that unit for anything positional, and any *rule*
    that means "somewhere on the map" must expect 999 rather than a real turn.

30. ⭐ **"COUNT THE THINGS YOU MIGHT DROP, NOT THE GROUPS YOU RESOLVED THEM INTO."** (Stage 9.)
    `maskLosingGateClaims` first tested `count($bestByTeam) < 2` for "is this contested?" — and a
    lapsed or unresolvable claim has no team, so it never reached that map. One live claim against one
    lapsed one therefore looked uncontested and **both** markers stood. The test has to be over the
    claims themselves, with the unusable ones recorded as losers rather than skipped.

---

## 6. Test plan

Local, two seats, in a game with the rule on:

| # | Scenario | Expect |
|---|---|---|
| 1 | Buy 3 front-line + 2 reinforcements | Same point pool; turn-1 Deployment offers only the 3 |
| 2 | Enemy view, turn 1 | "Reinforcements — 2 units, N pts" and nothing else. Check the raw JSON |
| 3 | Declare an exit with an undamaged high-sensor ship | Roll lands in the 0–1d6 bands most of the time |
| 4 | Declare with a low-sensor ship | Reaches the 1d10 and 2d10+2 bands; facing shifts |
| 4b | Declare with a Vorlon/Shadow hull (`factionAge >= 3`) | Precise roughly a quarter of the time; never worse than 1d6 on a roll under 13 |
| 5 | Declare next to the map edge / a moon | Clamp finds a legal hex, direction before distance |
| 6 | Enemy view, turn N (formation) | Blue marker at the **declared** hex; no exit unit in the payload |
| 7 | Turn N+1 | Deployment phase granted; 2 units stack in the vortex hex on the vortex facing |
| 8 | Place one, leave one | Commit succeeds; the unplaced unit is still in hyperspace next turn |
| 9 | Try to Jump Out through a blue exit | No button, and a hand-built order is refused |
| 10 | Arrive, then declare an **entrance** with the same ship | Allowed once the exit has closed |
| 11 | Gate exit, uncontested | Opens at end of Initial Orders, no deviation, waves on each held turn |
| 12 | Gate exit, lost to a nearer enemy entrance claim | Manifest refunded; units still in hyperspace, still unassigned |
| 13 | Reinforcement carrier with a queued deploy-start dock | Fighters end up in the bay, not at `x = ±30` |
| 14 | A reinforcement group with no jump drive, no gate on the map | Warned at Ready |
| 15 | Surrender a slot holding reinforcements | They vanish with the rest of the fleet |
| 16 | `replayHarness.php check` | Green, or failing only on [[arch_replay_corpus_known_failures]] |
| 17 | Commit Initial Orders on the turn an exit is declared | No `ErrorException` in the PHP log for either player (trap 19) |
| 18 | Save a mixed fleet, load it into another Allow-Reinforcements game | Flags come back; the groups and the points total match what was saved |
| 19 | Load that same fleet into a game *without* the rule | Everything in the main fleet; no headers, no toggle, no Reinforce links |
| 20 | Click MAIN FLEET / REINFORCEMENTS, then buy | The unit lands in the clicked group with no re-flag; the filter-strip tick agrees |
| 21 | A Fleet Builder lobby | Headers, toggle and Reinforce links all present; save a mixed fleet and load it back into the Builder with the flags intact |
| 22 | Two jump-capable reinforcements, declare with one | Manage Reinforcements still lists BOTH; the declared one is marked `OPENING` and offers Withdraw, the other offers Choose Hex |
| 23 | Withdraw from the menu, then declare again | The order and the manifest both clear, and the second declaration is accepted |
| 24 | Declare with your only opener, tick nobody onto the manifest, commit | Initial Orders confirm names the units that can never be called in |
| 25 | Same, but a second jump-capable unit stays behind | No stranding warning |
| 26 | Put a second jump-capable unit on the first one's manifest | It is greyed and `RIDING` in the menu, and cannot be selected |
| 27 | Withdraw that first jump point | The menu stays open, the row reverts to Choose Hex, and the greyed unit is selectable again |
| 28 | Lobby: tick Show Custom | The customs dropdown appears immediately right of the checkbox, before Buy as Reinforcement |
| 29 | Declare an exit and play the turn out (Stage 6) | At the end of Firing a blue Jump Point Exit appears — usually NOT on the declared hex — and the combat log names the band and the distance |
| 30 | Check `tac_ship` for the riders | `arrivalturn` = the next turn on the opener AND on every unit on its manifest; `arrivalvia` untouched |
| 31 | An Ancient (Vorlon/Shadow) opener vs a Young one | The Ancient is precise far more often; the log line's roll differs by 5 |
| 32 | Declare next to a moon or an asteroid field | The doorway never forms inside terrain, however the dice fall |
| 33 | Declare with two openers in one turn, then withdraw one | Only the standing declaration forms; the withdrawn one's manifest has its `arrivalvia` cleared and nothing else changes |

**Stage 8 (gates), all still to be done in play** — 11 and 12 above are the two the plan named; these
are the gestures the build added:

| # | Scenario | Expect |
|---|---|---|
| 34 | Click a gate in Initial Orders with nothing in hyperspace | ~~Only **Signal Jump Gate**. No arrival button at all~~ — **superseded by Stage 11b:** both buttons, and the manifest that opens says there is nothing to bring through |
| 35 | Same with a wave waiting | **Signal Gate for Arrival** too, blue vortex icon |
| 36 | Press it | The panel is **cyan**, the commit reads **Signal for Arrival**, and the Jump Point Manifest opens the moment it is pressed |
| 37 | Close the panel by clicking away instead | No claim, no berths, nothing to clean up |
| 38 | Look at the gate's hex after signalling | A **blue** "Arrival Gate Signalled" marker, not the yellow one and not a red hex |
| 39 | Manage Reinforcements on that turn | The gate is a row, badged **SIGNALLED**, offering **Withdraw Gate Signal**, listed above the drives |
| 40 | Withdraw it there | The row goes, the berths clear, the menu stays open |
| 41 | Cancel Gate Signal from the tooltip instead | Same: claim and berths both gone |
| 42 | Commit, play the turn, look at turn N+1 | A **blue** exit vortex on the gate's hex; the wave deploys into it on the gate's own facing |
| 43 | Turn N+1, Manage Reinforcements | The gate is a row badged **OPEN**, offering **Select Reinforcements**; it opens the manifest |
| 44 | Name a second wave and commit | It arrives on N+2 — a wave per turn of the hold (§0) |
| 45 | Do the same on the **last** turn of the hold | The row is greyed and says `jump point closes this turn`; the drives are still selectable |
| 46 | Signal a gate for 1 turn, bring nobody through | The berth is refunded at end of turn; the units are unassigned and concealed again next turn |
| 47 | Arrive through a gate, leave one unit unplaced | It goes back to hyperspace but **keeps** its berth, and rides the next wave without being re-named |
| 48 | Try to Jump Out through a gate's blue exit | No button; the gate is a way in only while it is signalled that way |
| 49 | Try to signal a gate that is already holding an exit | The button is not offered (the charge reads 0 and `getExitHeldBy` also refuses) |
| 50 | A game **without** the reinforcements rule | The arrival button never appears, and a hand-built `gateexit` claim is refused server-side |

**From the first play test (2026-08-29, game 4319)** — the two fixes above:

| # | Scenario | Expect |
|---|---|---|
| 51 | Initial Orders with **nothing** selected, click a gate | Full tooltip: Open Ship Details and both gate signal buttons, all clickable |
| 52 | Same, click an ordinary enemy ship | Full tooltip, Open Ship Details only — no EW or targeting buttons offered with no source |
| 53 | Select a ship, select some weapons, then deselect | The hex/split buttons drop out rather than being offered with nothing to fire |
| 54 | Lobby: select REINFORCEMENTS, buy a jump gate / a base / an OSAT | It lands in **MAIN FLEET**, with no Reinforcement link on its row |
| 55 | Signal a gate for arrival and play three turns out | No Deployment phase appears on any turn for the gate itself; only a real wave brings one |
| 56 | Click a gate during a Deployment or Pre-Turn phase | No "You cannot deploy on terrain" toast unless a unit that actually places this turn is selected |

**Stage 9, all still to be done in play:**

| # | Scenario | Expect |
|---|---|---|
| 57 | Open game 4318 (it holds a live blue vortex from Stage 7) after the deploy | It loads. If `db/reinforcementsRename.sql` was not run, it is a fatal on every load — that is the migration's whole point |
| 58 | Declare an arrival and let it scatter 3+ hexes, then look at the arrival turn's initiative | Every unit that rode it is that much lower — 5 per hex, 10 per 60° — and only on that turn |
| 59 | Arrive precisely (an Ancient with a friendly base on the map) | No penalty at all |
| 60 | Arrive through a **gate** | No penalty at all, however far away the gate is |
| 61 | Check the same units on the turn AFTER they arrived | The penalty is gone; they roll like anything else |
| 62 | Buy a Shadow fleet as reinforcements and open a doorway with a Phasing Drive | It is offered in Manage Reinforcements, the hex picker works, and the marker reads **Reinforcements** rather than "Jump Point Forming" |
| 63 | Play that turn out | **No terrain appears** on the arrival hex — for either player — and the wave is still placed there on the doorway's facing next turn |
| 64 | Try to Jump Out with that same Shadow ship once it has arrived | The old one-click Jump to Hyperspace, and no vortex declaration offered |
| 65 | Look at the enemy's screen on the declaration turn of a Shadow arrival | The same blue **Reinforcements** hex, from the republished slot entry — no ship, no count, no manifest |
| 66 | Two teams signal one gate, one clearly nearer | Both players see ONE marker, the nearer team's, in that team's flavour |
| 67 | Two teams signal one gate from the same distance | Both markers, both flavours — it is genuinely unclear |
| 68 | Replay that turn | Every claim that was made is shown again; the mask is live-play only |
| 69 | A game with the rule OFF | No Manage Reinforcements button, no arrival gate button, no reinforcement markers, and nothing in the payload |
| 70 | The FAQ | Delayed Deployment Slot and Jump Drives → Reinforcements each point at the other, and the two vortex colours are named |

---

## 7. Known bug, carried in from the report

> Observing a game that has not yet had Turn 1 Initial Orders shows a screen full of Jump Point
> ballistic hex icons where players are going to deploy.

**Not reproduced from reading, and I do not want to guess at it.** `generateReinforcementHexes`
early-returns on `gamephase == -1`, and `generateJumpPointHexes` only fires on
`getTurnDeployed(ship) == turn + 1`, which on turn 1 means slots with `depavailable == 2` — so
either the observer is being served a different phase than the Deployment phase the players are in,
or `deleteHiddenData`'s phase `-1` deploy-move strip is not covering the observer (it keys on
`$ship->userid == $this->forPlayer`, and an observer owns nothing, so it *should*).

Diagnostic recipe, before any fix: open such a game as an observer and log `gamedata.gamephase`,
`gamedata.turn`, and for each marked ship its `slot.depavailable`, `spawned` and committed `deploy`
move. That distinguishes the three candidates in one look. Worth doing **before Stage 2**, since
whatever it turns out to be is in the same masking path this feature extends.
