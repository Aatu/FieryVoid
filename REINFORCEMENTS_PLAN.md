# Reinforcements — Jump Point ENTRANCES

Jump Points Phase 3. Phases 1 and 2 gave a ship, and then a fixed gate, a way to open a vortex and
**leave** the battle ([JUMP_POINTS_PLAN.md](JUMP_POINTS_PLAN.md),
[JUMP_GATES_PLAN.md](JUMP_GATES_PLAN.md)). This one is the other direction: a player buys part of
their fleet as **reinforcements**, those units wait in hyperspace, and during the battle the player
opens an **entrance** vortex and brings them onto the map through it.

Status: **STAGES 0–5 BUILT 2026-08-27, all awaiting the by-hand verification below. Stages 6-9 not
started.** A player can now buy reinforcements, they are concealed from the enemy, and they can
**declare** a jump point entrance and name its manifest — the declaration reaches `tac_fireorder`
with `damageclass='jumpentry'` and the manifest reaches `tac_ship.arrivalvia`. What is still
missing is Stage 6: **nothing yet turns a declaration into a vortex**, so no unit has ever actually
arrived. Until Stage 6 lands, a declared entrance is a blue marker and a database row and nothing
else.

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
- **An empty `SpawnJumpPointEntrance` subclass is a silent bug**, and §3.3 sketched exactly that.
  The parent's constructor sets `$this->phpclass = "SpawnJumpPoint"`, and **phpclass is the
  persisted identity**: `submitShip` writes the property (not `get_class()`), the reload does
  `new $phpclass(...)`, and it is the only route by which the client learns the class. Inherit it
  and the entrance is an entrance for one request, then reloads as an ordinary exit anything can
  jump out through. The subclass constructor must set it.
- **`JumpEngine::$spawnableClasses` is a third channel §3.3 did not mention.** `BlueprintCache::build`
  reads it to preload `window.staticShips`, so without `'SpawnJumpPointEntrance'` on that list the
  first entrance to appear on a **poll** (no page reload) renders as an empty hex until F5.
  Regenerating `Terrain.json` does not help — that file is the *lobby* catalogue; `game.php` builds
  its blueprints from `BlueprintCache`.
- **`isJumpVortex` must stay EXIT-ONLY on the client.** Its callers disagree: `getVortexInHex`,
  `getVortexHeldBy` and everything downstream (the Jump Out button, Maintain, the "already holding a
  jump point open" refusal, the closing-vortex commit warning) are rules an entrance must **fail**,
  while the icon z-plane, the map overlay colour, the hex-stack sweep and the replay lifecycle
  animation must **match**. Widening the one predicate silently flips the first group. Two siblings
  were added instead — `isJumpVortexEntrance` and `isAnyJumpVortex`.

⚠️ **STAGES 0–8 ARE ONE LIVE DEPLOY.** A lobby that can sell reinforcements without the runtime
that delivers them strands a player's points in hyperspace for the whole game. Local testing is
stage by stage; the deploy is not.

⭐ **THE REPLAY HARNESS BASELINE NEEDS RE-RECORDING ONCE, for Stage 2.** `PlayerSlot` gained two
public fields, so every `snapshot_*` report in the corpus now differs by exactly
`/slots/N/reinforcementCount: added (0)` and `/slots/N/reinforcementPoints: added (0)` — 1238 diff
lines across 152 games, and **nothing else whatsoever**. The four behavioural checks are unaffected:
`--checks=movement,tohit,damage,masking` gives **155 passed, 0 failed** (game 4309 included, since
its [[arch_replay_corpus_known_failures]] failure is a snapshot one). Accept with
`replayHarness.php record`; until then use the `--checks=` form, or every future check in this
feature drowns in the additive field.

---

## 0. Decisions already taken (2026-08-27, user)

| Question | Ruling |
|---|---|
| Several units through one entrance in one turn | **All stack in the vortex hex.** They separate on their first movement |
| A SHIP's entrance lifetime | **One-shot.** Forms turn N, delivers turn N+1, closes end of N+1 |
| A GATE's entrance lifetime | The gate's **existing programmed hold** (1–4 turns); a wave may come through on each |
| Where an entrance may be opened | **Anywhere legal on the map** — no projection range, because there is no ship on the board to measure from |
| What other teams see | **A count and a point total.** Never classes, never names |
| Facing | Entrance facing `F` is the **doorway out**. An arriving unit is placed on heading `F` — the mirror of the exit rule, and why the arrow is reversed |
| One-way | A blue entrance can never be jumped out of; a yellow exit can never be arrived through |
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
| `validateVortexDeclaration` | [firing.php:112](source/server/handlers/firing.php#L112) | A dedicated hook off `validateFireOrders`, already branching ship-vs-gate. The entrance is a third branch |
| The slot loop in `FireGamePhase::advance` | [FireGamePhase.php:76](source/server/Phase/FireGamePhase.php#L76) | Grants next turn's Deployment phase. One extra clause is the whole "reinforcements are arriving" trigger |
| `generateJumpPointHexes` / `markReinforcementHex` | [BallisticIconContainer.js:400](source/public/client/renderer/icon/BallisticIconContainer.js#L400) | The blue `hexBlue` marker **already exists and is already the reinforcement colour** (`#00b8e6`) |
| `UI.vortexFacing` | [UI/vortexFacing.js](source/public/client/UI/vortexFacing.js) | The whole facing transaction. Needs a colour parameter and a reversed arrow, nothing more |
| `UI.gateSignal` | [UI/gateSignal.js](source/public/client/UI/gateSignal.js) | The gate panel. Needs a blue variant and an entrance flavour on the claim |
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
  arrives through an entrance.
- **Warning at Ready**: if the reinforcement group contains no unit with a usable Jump Engine and
  no `JumpgateCapital` is present on the map, those units can never reach the battle. The player is
  told and must confirm.

### 2.2 Opening an entrance

- Declared in **Initial Orders** by a reinforcement unit that is **still in hyperspace** and mounts
  an undestroyed, non-legacy Jump Engine — or by signalling a fixed gate.
- **No range test and no line-of-sight test.** There is no ship on the board to measure from. This
  is the single largest departure from the exit rules and it is why the entrance cannot ride
  `weaponManager.targetHex` (§3.4).
- The target hex must be **legal**: on the map, not holding any part of a Terrain unit, not holding
  another vortex or a gate, not holding an Enormous unit.
- The declaration carries a **facing** (0–5), set by the same on-map arrow control the exit uses,
  drawn blue and pointing **outward**.
- Then the player names the **manifest** — which of their hyperspace units ride this jump point.
  Any number, including none but the opener, and including units with no jump engine of their own.
- **One entrance per jump-drive-equipped reinforcement unit**, enforced by the existing
  one-vortex-per-ship rule.

### 2.3 Forming, arriving, closing

| Turn | State | What is on the board |
|---|---|---|
| N — declared in Initial Orders | **Forming** | a **blue** "Jump Point Forming" hex + a reversed facing arrow, drawn from the declaration. Nothing else exists |
| End of N | **Deviation is rolled** and the vortex unit is created at its true hex | — |
| N+1 | **Open.** The owner gets a Deployment phase; the manifest arrives | the blue vortex unit with its outward arrow |
| End of N+1 | **A ship's entrance closes.** A gate's runs on its programmed hold | — |

⭐ **THE DEVIATION IS ROLLED AT THE END OF THE FORMATION TURN, AND THAT IS A CONCEALMENT RULE, NOT
A FLAVOUR ONE.** If the vortex unit were created at the end of Initial Orders (where an exit's is),
its deploy movement row would carry the *deviated* hex in every viewer's payload for the whole of
turn N — `shouldBeHidden` suppresses the icon on the client but the JSON still holds the truth. By
creating the unit in `FireGamePhase::advance` instead, the only thing that exists during turn N is
the ballistic marker at the **declared** hex, and the real one cannot leak because it has not been
decided yet. See [[arch_info_bleed_masking]].

**A gate entrance does not deviate**, so it keeps the existing end-of-Initial-Orders timing and the
existing `openSignalledGates` path. The two sweeps stay separate for the same reason
`spawnDeclaredVortices` and `openSignalledGates` do.

### 2.4 Arriving

- On turn N+1 every player with a unit whose arrival turn is N+1 gets a **Deployment phase**.
- Those units may be placed **only** in the hex of an open entrance they are assigned to. They
  **stack** there freely — the one-ship-per-hex deployment block does not apply.
- Heading and facing are **forced** to the vortex facing. Speed is the player's to choose.
- **Placement is optional.** A player may bring some units through and leave the rest in hyperspace.
  An unplaced unit keeps its berth if the entrance will still be open next turn (a gate), and
  otherwise goes back to unassigned and waits for another entrance.

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
(Stage 6 test). If it returns 0, every entrance falls into the worst band and the feature is broken
in a way that looks like bad luck rather than a bug.

### 2.6 One-way

- A `SpawnJumpPointEntrance` never offers the Movement-phase **Jump Out** button and
  `Movement::applyJumpOut` refuses it.
- A `SpawnJumpPoint` (exit) is never a legal arrival hex.
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

### 3.3 The entrance vortex unit

New [`source/server/model/ships/terrain/SpawnJumpPointEntrance.php`](source/server/model/ships/terrain/SpawnJumpPointEntrance.php):

```php
class SpawnJumpPointEntrance extends SpawnJumpPoint {
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

The declaration is stored exactly as an exit's is — a `FireOrder` on the unit's `JumpEngine`,
`type='ballistic'`, `firingmode = facing + 1`, `x`/`y` = the hex — with **`damageclass='jumpentry'`**
as the discriminator, mirroring `'jumppoint'`.

But it is **not built by `weaponManager.targetHex`**. That pipeline measures range from the
shooter's hex and runs `mathlib.isLoSBlocked` from it; the opener has no hex. It would reject every
legal entrance, or divide by a position that does not exist. The entrance gets a **bespoke map
click mode**, a sibling of `MineDeployment`, and builds its own order — the same relationship
`UI.gateSignal` has to `UI.vortexFacing`.

`Firing::getVortexDeclarationBlock` gets a third branch, taken first and returning, alongside the
gate branch that is already there for exactly this reason:

```
if ($fire->damageclass === 'jumpentry') return self::getEntranceDeclarationBlock(...);
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
`jumpentry` this turn (or is a gate they claimed). Anything else is set to `NULL`.

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

### Stage 3 — the entrance vortex unit ✅ BUILT 2026-08-27
`SpawnJumpPointEntrance`; autoload + statics; the one-way `instanceof` guards in
`Movement::applyJumpOut` and the movement-phase tooltip; the blue outward `$facingArrow` on
`ShipIcon`.

**As built.** Four one-way guards rather than the two §2.6 counted, and they are cheap:
`Movement::getOpenVortexInHex` (what the client's `getVortexInHex` mirrors — the Jump Out button
never appears), `Movement::getJumpOutVortex` (every jump-out path funnels through it, so a forged
order naming an entrance by id is refused), `Firing::getVortexDeclarationBlock`'s maintain branch
(an entrance is one-shot and has no Maintain), and `JumpEngine::getVortexClosureReason` — which is
**trap 5**, and goes *before* the gate branch because entrance-ness belongs to the vortex while
gate-ness belongs to the engine, and the one-shot rule must win.

`spawnVortexUnit` took the `$class = 'SpawnJumpPoint'` parameter as designed; the name stays the
literal `"Jump Point"` there and the subclass constructor overwrites it, so there is no second
string to keep in step.

⚠️ **The two vortex ART assets are named the other way round from what you expect**, and both
predate this feature: `img/ships/JumpPointEntrance.png` is YELLOW and is worn by the **exit**;
`img/ships/JumpPointExit.png` is BLUE and is worn by the **entrance**. The colour carries the
meaning; do not rename the files ([[arch_image_cache_busting]]).

**Deviation from §3.7 — no blue twins of the three arrow constants.** `img/directionOfVortexEntry.png`
is the yellow asset mirrored **within its own alpha bounding box** (411,197)–(511,314) and recoloured
`#ffd12b→#00b8e6` / `#7b6415→#005870`. It therefore occupies exactly the same 101×118 pixels of the
same 512×512 canvas, so it shares `ShipIcon.FACING_ARROW_SCALE` with the yellow one and Stage 4 can
share the other two. Three more numbers kept in step by eye is precisely trap 7; identical geometry
makes them unnecessary. (Mirroring the *whole canvas* would be wrong — it puts the arrow on the
opposite side of the hex.)

**Verify:** spawn one by hand (or temporarily point an exit declaration at the new class). It must
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
  when the click landed on *no* icon — but a hex holding a ship is a perfectly legal entrance hex
  (§2.2 forbids terrain, gates, vortices and Enormous units and nothing else) and a wave arriving on
  top of somebody is the ordinary case. Hooking the later method would have silently refused every
  occupied hex.
- ⚠️ **An entrance order takes NO part in the ballistic icon or line pipeline.** It is collected into
  its own list and drawn by `generateEntranceHexes`. `createBallisticIcon` opens with
  `if (!shooterIcon) return;` and the shooter is in hyperspace, so it would drop the marker outright
  — and if the opener ever *did* have an icon, the launch sprite and the ballistic line would be
  drawn from its `'start'` row at the deployment-box centre: a bright line, on the map, from a ship
  that is not there. Trap 10 turns out to be bigger than `targetid`.
- **The legal-hex test reuses `weaponManager.getVortexHexBlock`** — the same sweep an exit uses, so
  terrain footprints, gates and existing vortices are all caught for free — plus a map-bounds test
  the exit path never needed. There is **no range and no LoS test**, by rule.

**Verify:** the order reaches `tac_fireorder` with `damageclass='jumpentry'`, the right `x`/`y` and
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

**The greyed state** (`ridingWith`, user request 2026-08-28). A jump-capable unit that is on
another ship's manifest is spoken for: opening a second doorway with it would have its drive
holding one entrance open while it arrives through a different one, and the declaration and the
manifest would disagree. The menu refuses the choice rather than letting it be made and then
unpicked.

- ⚠️ **`arrivalVia == its OWN id` is not riding with anybody** — that is what `createEntranceOrder`
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

⚠️ **Still deliberately not offered: re-editing a STANDING entrance's manifest.** A unit already
assigned to entrance A is not listed in entrance B's manifest dialog, and the menu does not reopen
A's — moving it means withdrawing A. That refusal predates this rework and is documented on
`showManifestDialog` ("silently moving it would undo a choice the player has already made"), but
multi-entrance turns make it far easier to reach, so it is a candidate if it starts to bite.

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
  entrance it names has to still exist, and a declaration can be replaced as well as withdrawn.
- ⚠️ **It reads the module, never `myShips`.** That list drops everything whose `getTurnDeployed` is
  later than this turn, and a unit in hyperspace answers 999.
- ⚠️ **It does not know about jump gates, and does not need to yet** — `canOpen()` excludes them
  because gate ENTRANCE signalling is Stage 8 and is not built. When that lands, a gate this player
  could signal **must** count as a remaining opener here or this will warn about fleets that are fine.

**Already proven headless** (`vm`-evaluated against a stubbed client world, which is the
[[howto_verify_react_bundle]] "bundle and evaluate" discipline — a parse check would not catch a
missing global or a wrong predicate): eligibility across seven cases; the legacy / stale-blueprint /
fixed-gate drive tests all refuse; the off-map bound accepted at q=21 and refused at q=22 of a 42x30
map; an obstructed hex refused; right-click cancels; **and the mode still works after a simulated
poll has replaced every ship object.** The order it builds carries `damageclass='jumpentry'`,
`type='ballistic'`, `firingMode = facing+1`, the chosen x/y, and **`targetid = -1`**.

### Stage 5 — server-side declaration validation ✅ BUILT 2026-08-27
`getEntranceDeclarationBlock`; the `arrivalVia` whitelist + validation + `setShipArrivalVia`.

✅ **THE STAGE-3 HAZARD IS NOW FIXED, BOTH HALVES.** `JumpEngine::getVortexDeclaration` did not
filter `damageclass` and would have accepted an entrance order as an exit declaration — so
`spawnDeclaredVortices` would have put a **yellow exit vortex at the entrance hex** at the end of
Initial Orders, and `hasOpenVortex` would then have made Stage 6's own sweep return `null` at
`spawnVortexUnit`'s first line. Both guards are in, deliberately redundant: the declaration reader
skips a `'jumpentry'` order, and the sweep skips `isReinforcement()` units outright. Either alone
would do; both together mean a future order shape cannot reintroduce it by accident.

⚠️ Still unfixed and deliberately so: **`getMaintainDeclaration` cannot see the vortex class** (it
takes only `$turn` and has no gamedata to resolve `activeVortexId` with), so the entrance's
no-Maintain rule is enforced at `Firing::getVortexDeclarationBlock` and again in
`getVortexClosureReason`, which returns before the maintain test is ever reached. That is two gates;
a third would need a signature change for no new coverage.

**As built**, with three things §3.5 did not anticipate:

- ⭐⭐ **THE ENEMY'S BLUE MARKER NEEDED A CHANNEL OF ITS OWN, and §2.3 and §3.6 did not reconcile.**
  §2.3 says the forming marker is what an opponent sees on turn N, and §3.6's ⭐ says the disclosure
  is *"three units are coming, 1250 points, **somewhere near here**"* — but §3.6 also **deletes the
  declaring ship from the enemy's payload, orders and all**, so there is nothing left to draw the
  marker from. `PlayerSlot` therefore gained `formingEntrances` — a list of `{x, y, facing}` and
  **nothing else**, never the opener, never the manifest — filled by the same sweep that removes the
  ship. The owner draws the identical marker from its own fire order; `generateEntranceHexes` folds
  both sources into one drawing. ⚠️ **Never published in phase 1**: a declaration is secret while
  Initial Orders are open, which is the rule `hideSystemFireOrders` already enforces on the order.
- **The rule check in `persistManifest` is an efficiency guard, not only a correctness one.** The
  sweep needs a fresh gamedata load to answer anything, and that would be a **third** full load on
  every Initial Orders commit of every game in the system. `hasRuleName('allowReinforcements')`
  first means every game without the rule pays nothing at all.
- **The map-bounds test is new** — the exit path never needed one, because a 4-hex projection range
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
`getVortexDeclaration` ignoring a `'jumpentry'` order.

⚠️ **One harness lesson worth keeping:** the first version of that test picked its "free hex" by
looking for a hex with no ship *centre* in it, and every case passed on a game with no terrain while
the happy path FAILED on one with moons. Terrain occupies a whole **footprint**
(`RammingAttack::getTerrainOccupiedHexes`), so a hex with no centre in it can still be solid rock. A
test that cannot tell "correctly refused" from "wrongly refused" is testing nothing.

### Stage 6 — the entrance forms, and it deviates
`JumpEngine::spawnEntranceVortices($servergamedata, $dbManager)` in `FireGamePhase::advance`, after
`closeExpiredVortices` and **before** the slot loop. Per declaration: roll the deviation, clamp to
a legal hex, `spawnVortexUnit(..., 'SpawnJumpPointEntrance')`, stamp `arrivalturn = turn + 1` on
every ship whose `arrivalvia` matches, clear `arrivalvia` on any manifest whose vortex did not
open, write the log order and the additive `'VortexScatter'` note (`"<hexes>,<facingSteps>"`).

⚠️ Runs off a real `getTacGamedata` load, never off POST-side ships. ⚠️ Do not branch on
`$gamedata->phase` — `advance()` has already set the next one. Both traps are documented on the two
sibling sweeps.

**Verify:** the sensor rating is non-zero for a hyperspace unit (§2.5); each band scatters the
right distance and the log line **names the band it landed in**; the clamp never lands on terrain,
a gate, another vortex or off-map; and — the concealment test — the enemy's turn-N payload contains
**no** entrance unit at all.

### Stage 7 — arriving
The `hasReinforcementsArriving($playerid, $turn)` clause in the slot loop; the reinforcement branch
in `validateDeploymentArea` (hex == the assigned vortex's hex, facing == the vortex facing); the
partial-commit exemption in `validateDeployment`; the unplaced-unit reset. Client:
`DeploymentPhaseStrategy` valid-area override, the stacking bypass in `onHexClicked`, the forced
facing, and `validateAllDeployment` not demanding placement.

⚠️ **Check `Hangar::generateIndividualNotes`' `getTurnDeployed > turn` guard** before assuming a
reinforcement carrier's deploy-start dock works. It was trap 3 of
[[arch_placement_turn_vs_deploy_turn]] and it is the same shape of bug here.

**Verify:** three units through one entrance stack correctly and separate on their first movement;
leaving one behind works and it is still there next turn; a forced facing cannot be overridden;
speed is free.

### Stage 8 — gates
The blue `UI.gateSignal` variant; the entrance flavour on the claim; the refund when an enemy exit
claim wins the contest (the manifest is simply never stamped, so this is mostly a matter of
clearing `arrivalvia`); waves on each turn of a gate's programmed hold.

⚠️ **A JumpEngine recharge or duration rule must read `$delay`, never `$loadingtime`** — the
harness proved this on Phase 2 ([[project_jump_gates]]).

**Verify:** both `convoyRaid`-style scenarios — a gate claimed for entry uncontested, and a gate
claimed for entry and lost to a nearer enemy exit claim.

### Stage 9 — optional, after playtest
The scatter initiative penalty (scatter hexes + 2 per 60° of facing shift, applied to units arriving
through that vortex, on their arrival turn only) read from the `'VortexScatter'` note.

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
   parsed with `explode(',', $v, 3)`. Entrance-ness rides the **class**; scatter rides a new
   additive `'VortexScatter'` note. ⚠️ `notekey_human` is `varchar(40)` and an overflow is a mysqli
   1406 that aborts the whole submission, not a truncation.
5. **A one-shot entrance must release its engine.** `spawnVortexUnit` opens with
   `if ($this->hasOpenVortex($gamedata->turn)) return null;`. If the entrance does not close at the
   end of the arrival turn, the ship that opened it can never open an exit for the rest of the
   game. `closeExpiredVortices` needs an entrance clause that disturbs neither the ship branch nor
   the `holdsGateEngine` narrowing.
6. **Removing ships from the payload** must re-index `$this->ships` and rebuild `$shipsById`. §3.6.
7. **The three facing-arrow constants are kept in step by hand** and now there are six. §3.7.
8. **`generateIndividualNotes` deploy guards.** Only three exist
   (`grep -rn getTurnDeployed source/server | grep return`); Hangar's is the one that matters. §4
   Stage 7.
9. **The static generator skips a class whose filename does not match, silently.** §3.3.
10. **A ballistic order's `targetid` is read as "hang the marker on that unit"** by every
    ballistic-icon path — `'jumppoint'` orders are already forced to `targetid = -1` throughout
    `createBallisticIcon`. `'jumpentry'` must be too, or the blue marker will be drawn over the
    opener, which is in hyperspace ([[project_jump_gates]], trap 2).
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
    `$ballistic`, so an entrance declaration is an ordinary ballistic fire order — but its author is
    in hyperspace, and `getLastTurnMovement()` skips every `'start'` row, so it answers **null**.
    `array("x" => $movement->position->q, …)` was then a fatal `ErrorException` on **every gamedata
    load from phase 2 of the turn the entrance was declared**, for both players, on every poll:

    ```
    Attempt to read property "position" on null … TacGamedata.php (171)
    ```

    ⚠️ **Phase-1 secrecy hid it.** `hideSystemFireOrders` strips every current-turn ballistic order
    from every phase-1 payload *including its author's*, so the declaration is invisible to this loop
    until Initial Orders are committed — which is exactly when the error appeared, and why it read as
    "committing orders breaks the game" rather than "declaring an entrance does".

    The guard is `if ($movement === null) continue;` — the general rule, deliberately not a
    `damageclass === 'jumpentry'` test. Nothing wants the record either: `$this->ballistics` is
    absent from `stripForJson` so it never reaches the client, the marker is drawn from the fire
    order itself (`entranceOrders`, or the slot's `formingEntrances` for an enemy viewer), and the
    only server-side consumer is the `hidetarget` mask — which an entrance is not subject to,
    `hideHyperspaceReinforcements` having deleted the whole unit first.

---

## 6. Test plan

Local, two seats, in a game with the rule on:

| # | Scenario | Expect |
|---|---|---|
| 1 | Buy 3 front-line + 2 reinforcements | Same point pool; turn-1 Deployment offers only the 3 |
| 2 | Enemy view, turn 1 | "Reinforcements — 2 units, N pts" and nothing else. Check the raw JSON |
| 3 | Declare an entrance with an undamaged high-sensor ship | Roll lands in the 0–1d6 bands most of the time |
| 4 | Declare with a low-sensor ship | Reaches the 1d10 and 2d10+2 bands; facing shifts |
| 4b | Declare with a Vorlon/Shadow hull (`factionAge >= 3`) | Precise roughly a quarter of the time; never worse than 1d6 on a roll under 13 |
| 5 | Declare next to the map edge / a moon | Clamp finds a legal hex, direction before distance |
| 6 | Enemy view, turn N (formation) | Blue marker at the **declared** hex; no entrance unit in the payload |
| 7 | Turn N+1 | Deployment phase granted; 2 units stack in the vortex hex on the vortex facing |
| 8 | Place one, leave one | Commit succeeds; the unplaced unit is still in hyperspace next turn |
| 9 | Try to Jump Out through a blue entrance | No button, and a hand-built order is refused |
| 10 | Arrive, then declare an **exit** with the same ship | Allowed once the entrance has closed |
| 11 | Gate entrance, uncontested | Opens at end of Initial Orders, no deviation, waves on each held turn |
| 12 | Gate entrance, lost to a nearer enemy exit claim | Manifest refunded; units still in hyperspace, still unassigned |
| 13 | Reinforcement carrier with a queued deploy-start dock | Fighters end up in the bay, not at `x = ±30` |
| 14 | A reinforcement group with no jump drive, no gate on the map | Warned at Ready |
| 15 | Surrender a slot holding reinforcements | They vanish with the rest of the fleet |
| 16 | `replayHarness.php check` | Green, or failing only on [[arch_replay_corpus_known_failures]] |
| 17 | Commit Initial Orders on the turn an entrance is declared | No `ErrorException` in the PHP log for either player (trap 19) |
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
