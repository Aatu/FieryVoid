# Reinforcements — Jump Point ENTRANCES

Jump Points Phase 3. Phases 1 and 2 gave a ship, and then a fixed gate, a way to open a vortex and
**leave** the battle ([JUMP_POINTS_PLAN.md](JUMP_POINTS_PLAN.md),
[JUMP_GATES_PLAN.md](JUMP_GATES_PLAN.md)). This one is the other direction: a player buys part of
their fleet as **reinforcements**, those units wait in hyperspace, and during the battle the player
opens an **entrance** vortex and brings them onto the map through it.

Status: **STAGE 0 BUILT 2026-08-27, awaiting the by-hand verification below. Stages 1-9 not started.**

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

⚠️ **STAGES 0–8 ARE ONE LIVE DEPLOY.** A lobby that can sell reinforcements without the runtime
that delivers them strands a player's points in hyperspace for the whole game. Local testing is
stage by stage; the deploy is not.

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
| Saved fleets | **Do not remember reinforcement status.** `tac_savedships` gets no new column; a reloaded fleet buys everything front-line and the player re-flags |

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

- Game rule **Allow Reinforcements** (`allowReinforcements`), set at Create Game. Off ⇒ nothing in
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

### Stage 1 — the lobby
A **Reinforcements** toggle in the buy panel; bought units carry the flag; the fleet list groups
them under a sub-header; `submitShip` writes the column; the no-jump-drive warning at Ready; the
"rule is off" strip that mirrors [gamelobby.js:4064](source/public/client/gamelobby.js#L4064).

⚠️ Lobby ship objects are `jQuery.extend` clones, so **every `instanceof` fails** and there is no
`window.staticShips` ([[arch_lobby_ship_objects]]). The flag must be a plain property.

**Verify:** buy a mixed fleet, reload the lobby, confirm the flag survives and the points cap counts
reinforcements against the same pool.

### Stage 2 — concealment
`hideHyperspaceReinforcements` + the slot aggregate + the fleet-list placeholder row.

**Verify from BOTH seats, by hand.** The harness does not cover masking
([[arch_placement_turn_vs_deploy_turn]]), so this stage has no regression net. Check the raw JSON,
not just the rendered list.

### Stage 3 — the entrance vortex unit
`SpawnJumpPointEntrance`; autoload + statics; the one-way `instanceof` guards in
`Movement::applyJumpOut` and the movement-phase tooltip; the blue outward `$facingArrow` on
`ShipIcon`.

**Verify:** spawn one by hand (or temporarily point an exit declaration at the new class). It must
render blue with an outward arrow and refuse a Jump Out.

### Stage 4 — the Call Reinforcements client flow
The `#iniGui` button; the opener picker; the bespoke hex click mode with the legal-hex test; blue
`UI.vortexFacing`; the manifest dialog; the blue "Jump Point Forming" marker and reversed arrow in
`BallisticIconContainer`.

⚠️ `window.UI` is **created** by `shipMovement.js`, so any new module must load after it in
`game.php` or the assignment wipes it — the hazard both `vortexFacing.js` and `gateSignal.js`
document at the top of themselves.

**Verify:** the order reaches `tac_fireorder` with `damageclass='jumpentry'`, the right `x`/`y` and
`firingmode`, and the manifest reaches `tac_ship.arrivalvia`. Discarding the control leaves nothing
behind.

### Stage 5 — server-side declaration validation
`getEntranceDeclarationBlock`; the `arrivalVia` whitelist + validation + `setShipArrivalVia`.

**Verify:** a tampered POST (an opener that is not yours, one that is already on the board, an
illegal hex) is rejected and logged, and never reaches the DB.

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
