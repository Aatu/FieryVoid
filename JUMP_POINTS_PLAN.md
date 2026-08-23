# Jump Points — B5W Vortex Mechanics

Replaces the current one-click "boost the Jump Engine, vanish at end of turn" escape with the
tabletop rules: a ship **projects a vortex** into a nearby hex, the vortex **forms**, **persists**,
can be **maintained**, and any unit that **flies into its mouth** leaves the battle.

Status: **ALL SIX STAGES BUILT (2026-08-21/22), plus §9 the legacy opt-out (2026-08-22).**
⚠️ Stage 2 retires the boost-to-jump path and Stage 4 adds the replacement, so **stages 2–5 must
ship as ONE live deploy** — deploying Stage 2 alone leaves a game with no way to leave a battle.
That deploy is now complete: 2–5 are all in the tree.
⚠️ **The "cleanup deploy one cycle later" promised at Stage 2 and §5 trap 11 is now VOID** — §9 put
195 hulls back on the boost path, so the boost framework is permanent. See §9 trap 3.

Decisions already taken (2026-08-17, user):

| Question | Ruling |
|---|---|
| How the Jump Engine targets a hex | **`JumpEngine extends Weapon`** — convert the class itself |
| Entry-direction rule | **Actual entry step**, not heading. Travel direction must be *opposite* the vortex facing |
| Fixed Jump Gates | **Phase 2** — ship vortices ship first |
| Jump-failure roll | Stays at **end of turn** (`criticalPhaseEffects`), because the vortex isn't open until then |

---

## 1. What exists today

| Piece | Where | Behaviour |
|---|---|---|
| `JumpEngine extends ShipSystem` | [baseSystems.php:5032](source/server/model/systems/baseSystems.php#L5032) | `boostable`, `maxBoostLevel = 1`. Boost = power type 2 = "jump this turn" |
| `PhasingDrive extends JumpEngine` | [baseSystems.php:8576](source/server/model/systems/baseSystems.php#L8576) | Shadow; adds the half-phase self-destruct |
| `doHyperspaceJump()` | [baseSystems.php:5063](source/server/model/systems/baseSystems.php#L5063) | d100 vs % of engine boxes lost → `HyperspaceJump` or `JumpFailure` damage entry on primary Structure, plus a `jumped` IndividualNote holding pre-jump CV |
| The trigger sweep | [firing.php:1277-1289](source/server/handlers/firing.php#L1277) | End of `Firing::fireWeapons`, after all fire |
| CV preservation | `hasJumped()` / `getCVBeforeJump()` + [ShipClasses.php:404](source/server/model/ships/ShipClasses.php#L404) | Jumped ship keeps its combat value instead of zeroing |
| Hangar interlock | [HangarOps.php:6758-6828](source/server/model/systems/HangarOps.php#L6758) | `hasJumpDamageThisTurn` / `hasJumpFailureDamage` / `processJumpingCarrierDockOrders` |
| Client mirrors | [ships.js:518](source/public/client/ships.js#L518), [gamedata.js:829](source/public/client/gamedata.js#L829), [SystemPowerSettings.js:293](source/public/client/UI/reactJs/system/SystemPowerSettings.js#L293) | `hasJumpedNotDestroyed`, the "jumping" commit warning, the Yes/No boost buttons |
| Half-phase dependency | [movement.js:1044](source/public/client/movement.js#L1044) | `canHalfPhase` requires an undamaged, powered `jumpEngine` — **unaffected by any of this** |
| `JumpgateNew` terrain | [terrain/jumpgateNew.php](source/server/model/ships/terrain/jumpgateNew.php) | Already exists, already mounts a `JumpEngine(6,50,20,20)`. Phase 2 hangs off this |

Machinery this plan reuses rather than reinvents:

- **Hex-targeted ballistic declaration** — `weapon.hextarget` + `weaponManager.targetHex`
  ([weaponManager.js:3136](source/public/client/weaponManager.js#L3136)) + the
  `targetWeaponsHex` button in [shipTooltipInitialOrdersMenu.js:38](source/public/client/UI/shipTooltipInitialOrdersMenu.js#L38).
- **Mid-game unit spawning** — `MissileLauncher::createLoiteringMine`
  ([missile.php:2440](source/server/model/weapons/missile.php#L2440)): `Manager::insertSingleShip`
  → deploy `MovementOrder` → `IndividualNote` recording the spawn turn → `onIndividualNotesLoaded`
  restores `$spawned`.
- **A weapon that relocates a ship** — `MicroJumpSystem`
  ([customTrek.php:3176](source/server/model/weapons/customTrek.php#L3176)) is a hex-targeted
  weapon that writes movement orders. Read it before writing the vortex resolution.
- **Declaration secrecy** — `TacGamedata::hideSystemFireOrders`
  ([TacGamedata.php:1157](source/server/model/TacGamedata.php#L1157)) already strips enemy
  ballistic orders during phase 1, so a declared vortex hex stays private until Initial Orders close.
- **Unit removal without destruction** — `$removed` / `$removedTurn`
  ([ShipClasses.php:3054](source/server/model/ships/ShipClasses.php#L3054)), the docked-flight pattern.

---

## 2. The FV ruleset (unambiguous statement)

### 2.1 Opening

- Declared in **Initial Orders** by targeting a hex with the Jump Engine. No power cost; the engine
  must not be offline and must not be destroyed.
- Target hex **within 4 hexes**. Legal target hex must **not** contain: any part of a Terrain unit,
  another vortex, a jump gate, or an Enormous unit. It *may* contain ships, friendly or enemy.
- The declaration also carries a **vortex facing** (0–5).
- One vortex per ship at a time.
- **Line of sight to the target hex is required** (user ruling 2026-08-21). Already the behaviour —
  `targetHex` runs the standard `mathlib.isLoSBlocked` test against `gamedata.blockedHexes` for
  every hex-targeted weapon, and `JumpEngine` does not set `$ignoresLoS`. No code change; recorded
  so nobody "fixes" it later.
- **A stealthed ship may open a jump point, and doing so reveals it** — exactly as using non-DEW EW
  does (user ruling 2026-08-21). Already the behaviour on both sides, and it needs to stay that way:
  - Server: `Stealth::isDetectedInitial` reveals on `firedOffensivelyOnTurn`, which counts any
    non-intercept order — so the vortex declaration triggers it, at phase 1, to every enemy team.
    It runs in `InitialOrdersGamePhase::process` off the POST-side ship, which is where the
    freshly-declared order lives.
  - Client: `Stealth.isDetectedStealth` ends with `if (weaponManager.shipHasFiringOrder(ship))
    return true`, so the ship-window banner flips to revealed the moment the hex is targeted.
  ⚠️ The `Stealth` class (Hyach subs and friends) is **not** the same population as
  `weaponManager.isHidden`, which covers only the Torvalus **Shading Field** and the
  **Cloaking Device**. Those two genuinely cannot fire while active and had no fire-order-driven
  reveal path at all — their detection is recomputed from range + LoS each turn. **They are covered
  by the same ruling** (user, 2026-08-21) and it took real code, unlike the `Stealth` case:
  - `targetHex`'s `isHidden` guard now exempts `jumpEngine`, alongside `TransverseDrive` and
    `MicroJumpSystem`, and warns the player before building the order.
  - `JumpEngine::vortexRevealNotes` writes the reveal, and `ShadingField` / `CloakingDevice` each
    call it from a new `case 1:` in `generateIndividualNotes`.
  - ⭐ **Both halves are required.** A `detected` note per enemy team is the reveal; the
    `Unshaded` / `Decloaked` note drops the concealment. Writing only the reveal does **not** work:
    `checkStealthNextPhase` re-runs at the end of Movement and, with `$active` still true, writes
    `undetected` again for every team out of range — silently undoing the reveal inside the same
    turn. Writing only the drop leaves the ship concealed until the next detection sweep.
  - Consequence to expect: `active` is false from the next load, so the ship also loses the shading
    /cloak **defensive** benefits for the rest of the turn (doubled EM shield, cloak shield
    suppression) and `isHidden` stops blocking its other weapons in the Fire phase. That is the
    intended reading of "breaks the cloak", but it does mean a jump-point declaration is a way to
    change a shading decision taken in Pre-Turn. Flag for playtest if it reads wrong.

### 2.2 The facing rule — write this down once and never re-derive it

> **The facing direction is the doorway into hyperspace.** To use the jump point, a unit must enter
> the hex through the hex side the vortex is facing.

Restated as the formula the code needs — the vortex's facing `F` names its **mouth**, and a unit
enters by crossing that side inbound, so it is **travelling in direction `(F + 3) % 6`**:

```
required travel direction  D = (F + 3) % 6
equivalently, entry side   S = (D + 3) % 6  must equal F
```

⚠️ **Direction 0 is EAST and direction increases CLOCKWISE on screen.** Both hex libraries agree —
`CubeCoordinate::NEIGHBOURS[0]` is `(x 1, y -1, z 0)`, which is offset `(q+1, r+0)`, and client
`Offset.neighbours[…][0]` is `{q: 1, r: 0}` for both row parities — and `mathlib.hexFacingToAngle(d)`
returns `d * 60`, which `getCompassHeadingOfPoint` measures clockwise from east. (Corrected
2026-08-21: this section's worked example used to read `(10,9)`, which is direction **2**. Only the
illustrative coordinates were wrong; the `D = (F+3)%6` formula was and is right.)

Worked example: vortex at `(10,10)` with `F = 0`. Direction 0's neighbour is `(11,10)`. A ship
sitting at `(11,10)` and moving to `(10,10)` travelled in direction 3 — and `3 == (0+3)%6`. ✔
A ship arriving at `(10,10)` from `(9,10)` travelled in direction 0 and is **refused**.

Because the rule is on the **actual movement step**, a sideslip into the hex is judged on the
slipped direction, not on heading — which is the whole point of choosing this reading.

**Ships already in the hex** when the vortex activates: judged on the last step that carried them
into the hex, walking back through prior turns if necessary. A unit with no such step at all (a
base, an OSAT, a unit that has never moved) **cannot** use the vortex.

### 2.3 Forming, open, closing

| Turn | State | Can units enter? | What is on the board |
|---|---|---|---|
| N — declared in Initial Orders | **Forming**. Unit is created at the end of `InitialOrdersGamePhase::advance` but is deliberately NOT drawn | No | a yellow **"Jump Point Forming"** hex + a facing arrow |
| End of N | **Activation.** Damaged-engine failure roll happens here | — | — |
| N+1 | **Open** | Yes | the **vortex unit**, with the same facing arrow over it |
| N+2, N+3, N+4 | Open **only if maintained each turn** | Yes | as above |
| End of N+4 | **Hard cap.** Closes unconditionally | — | — |

⭐ **FOUR TURNS OPEN, AND THE TURN IT WAS DECLARED IS NOT ONE OF THEM** (user ruling 2026-08-22,
correcting an earlier "4 turns counting N"). Declared on N, open on N+1 … N+4, gone on N+5. So the
last turn on which declaring Maintain can change anything is **N+3** — a Maintain on N+4 is
overtaken by the cap, which is why the control is not offered there. `JumpEngine::MAX_VORTEX_TURNS`
is the one place the 4 is written down; the closure sweep, the client's decision to offer the
toggle and the engine's N/4 icon counter all read it (the client derives it from `spawned`, which
is `openTurn + 1`, so its cap turn is `spawned + 3`).

⭐ **A forming vortex is a MARKER, not a unit** (user ruling 2026-08-21, revising the original
"visible to everyone from Movement onward"). Showing the unit on turn N read as "there is a jump
point here" when there is not yet one, so the unit stays off the board until the turn it OPENS and
the ballistic marker carries the turn instead. Mechanically this is one line — the vortex is spawned
with `spawned = N + 1`, and `shipManager.shouldBeHidden` already hides anything whose spawned turn
is later than the turn being viewed. Everything server-side still sees the unit on turn N, which is
what keeps a second declaration out of the same hex.

**The facing arrow is the same asset at the same size in all three places** — the Stage 2b preview,
the Forming marker, and the vortex unit — so it never changes appearance across a vortex's life.
Three constants, kept in step by hand: `UI.vortexFacing.MARKER_ARROW_SCALE/_OPACITY`,
`BallisticIconContainer.VORTEX_ARROW_SCALE/_OPACITY`, `ShipIcon.FACING_ARROW_SCALE/_OPACITY`.

A vortex also closes at the end of the current turn if **any** of these becomes true:

1. Its holder did not declare *Maintain* in Initial Orders.
2. Its holder is more than 4 hexes away at end of turn.
3. Its holder is destroyed.
4. Its holder entered the vortex itself (RAW: cannot be held open from the other side).
5. While maintaining, the holder had any power-absorbing system online other than **Scanner** and
   the **Jump Engine** itself.

Closure is always **end of turn, after Firing** — a vortex declared closed is still usable for the
whole of that turn.

### 2.4 Maintaining

Declared each Initial Orders after the opening turn. Requires every power-absorbing system except
Scanner and the Jump Engine to be **offline**. Violation does not block the commit; the vortex
simply closes at end of turn with a log note (this keeps the rule out of the submit path, where a
hard block would be a support burden).

⭐ **THE CONTROL (Stage 5, revised at build): a Maintain ON/OFF toggle in the Jump Engine's own
system menu**, alongside the ship's other per-system switches. Switching it ON does BOTH halves of
the rule in one click — it makes the declaration *and* powers down everything that has to be off.
While it stands, none of those systems can be powered back up.

⚠️ **The first cut had the player re-target the vortex's own hex with the Jump Engine each turn.**
It produced the right order and the server was happy, but it was the wrong shape: it asked for a
targeting ritual to express what is really a switch, and — decisively — a right-click on a hex
cannot take the ship dark, so the player was left to shut down thirty systems by hand and lose the
vortex at end of turn if they missed one. Replaced (user, 2026-08-22). The map path is gone;
targeting your own vortex hex now says so and points at the toggle.

**What did NOT change is the order it produces**: an ordinary ballistic declaration carrying
**firing mode 7** (`JumpEngine::MAINTAIN_MODE`) aimed at the vortex's own hex. It persists, masks
and replays exactly like the opening declaration, `Firing::getVortexDeclarationBlock` validates it
unchanged, and the yellow hex marker reads *Maintaining Jump Point* instead of *Jump Point Forming*.
⭐ **The order is also the STATE** — there is no `active` flag anywhere — which is what makes "am I
maintaining?" survive a poll, a page reload and the commit with nothing to keep in sync.

Other consequences, all deliberate:
- The 4-hex range test the declaration already runs **is** "the holder must be near its vortex",
  measured at Initial Orders. The end-of-turn closure test measures it again after Movement.
- The client needs to know **which ship** holds a given vortex, not merely which player. Hence
  `SpawnJumpPoint::$vortexHolderId`, stamped on every load from the state note's `shipid`.
- Maintaining reveals a stealthed/cloaked ship exactly as opening does (`hasVortexDeclaration`
  counts any un-rejected Jump Engine order). Not a special case: a ship maintaining a vortex has
  had to shut its Shading Field or Cloaking Device down anyway, since both draw power.
- ⭐ **The toggle is not offered on the vortex's LAST turn.** The cap closes it at the end of
  `openTurn + MAX_VORTEX_TURNS` whatever the player does, so a Maintain declaration there could not
  change the outcome. A control that cannot matter should not be shown. (`openTurn` is not sent to
  the client, but `spawned` is, and `spawned == openTurn + 1`, so the cap turn is `spawned + 3`.)
- ⭐ **Nor are the fire-order buttons**, on any turn the engine is holding a vortex: it cannot
  declare a second one, and the order it does hold is the Maintain declaration, which is cancelled
  by throwing the toggle to OFF — a generic "remove fire order" would strip the declaration and
  leave the ship's systems shut down with nothing holding them that way. On the turn a vortex is
  *declared* there is no unit yet, so the ordinary remove-and-redeclare idiom still re-aims it.
- ⭐ **The engine's icon counts the vortex while one stands, and its CHARGE the rest of the time**
  (revised in Stage 6, user ruling 2026-08-22). While a jump point is open the two numbers read
  **N/4** — turns open, out of the four it can have — with the declaring turn reading `0/4`. That
  counter travels in its own payload field (`vortexTurnsOpen` / `vortexMaxTurns`), because
  `$turnsloaded` now means on a Jump Engine exactly what it means on every other weapon:

  > **A jump engine STARTS a scenario fully charged, spends the whole charge opening a jump point,
  > and recharges at 1 per turn from the turn AFTER that jump point closes.** The recharge time is
  > the ship file's 4th constructor argument — the B5W jump delay, 0–65 across the fleet, 16 on a
  > Primus — and it is `$loadingtime`. So a Primus reads 16/16 on turn 1, 0/16 for the rest of the
  > turn it opens one, N/4 while that jump point stands, and 1/16, 2/16 … from the turn after it
  > closes. It cannot open another until it is full again.

  It is a **derived** value (`JumpEngine::getVortexRechargeLoad`, off the vortex note's open and
  close turns), not a stored one — see Stage 6 for why the ordinary Weapon loading machinery is out
  by a turn. And it does a second, wanted job for free: `weaponManager.isLoaded` is
  `loadingtime <= turnsloaded`, so a discharged or recharging engine reads NOT LOADED and drops out
  of `targetHex`'s weapon sweep — the one-vortex-per-ship rule and the recharge rule both
  expressing themselves in the UI rather than being restated there.
  (⚠️ Stage 5 put the vortex age in `$turnsloaded` and parked the loaded state in `$delay`. Stage 6
  undid that. `$delay` still holds the raw constructor value because that is the rulebook's name for
  it, but nothing reads it except the constructor — as a `Weapon` subclass it would be taken for an
  INITIATIVE delay if anything ever did.)

### 2.5 Jumping out

- During **Movement**, once a unit's plotted path enters an **open** vortex hex from the correct
  direction, a **Jump Out** button appears in a new movement-phase tooltip menu.
- Selecting it **ends that unit's movement immediately** — remaining hexes are forfeit.
- At the end of Movement the unit is removed from the game exactly as `HyperspaceJump` does today:
  primary structure destroyed with damageclass `HyperspaceJump`, `jumped` note written first so CV
  is preserved.
- Any unit may use any open vortex, including an enemy's. This is RAW and is deliberate — including
  units with no jump engine of their own, which is why CV preservation cannot live on the engine
  (see Stage 4).
- An **attached** pod or grapple is carried out with its host and can never use a vortex on its own:
  its movement rows mirror the host's, so it has no entry step of its own to judge.
- **Fighter flights may use one too, from Stage 6.** A flight has no primary Structure, so
  `Movement::applyJumpOut` takes it off the board by destroying every CRAFT in it with a
  `HyperspaceJump` entry instead, and hangs the CV note on the sample fighter. (Stage 4 blocked
  them on both sides because that path did not exist yet.)
- No failure roll on entering. The risk was taken when the vortex was opened.

### 2.6 Jump failure

Rolled at **end of turn** for a ship that opened or is maintaining a vortex, only if its Jump Engine
has damage. d100 ≤ (% of engine boxes lost) → the existing `JumpFailure` path: ship destroyed, no
hangar escape roll, and the vortex does not form / collapses immediately.

---

## 3. Architecture

### 3.1 `JumpEngine extends Weapon` — and the four things it breaks

The constructor keeps its 4-arg signature (`$armour, $maxhealth, $powerReq, $delay`) and passes
`0, 360` as arcs internally, so **none of the 610 ship files change**. What does change:

```php
class JumpEngine extends Weapon {
    public $name = "jumpEngine";
    public $displayName = "Jump Engine";
    public $ballistic  = true;    // declared in Initial Orders
    public $hextarget  = true;    // uses the existing targetHex pipeline
    public $range      = 4;
    public $loadingtime = 1;
    public $priority   = 1;
    public $doNotIntercept = true;
    public $uninterceptable = true;
    public $noProjectile = true;
    public $firingModes = array(   // modes 1-6 == facing 0-5; NOT player-visible
        1 => "Vortex 0°",  2 => "Vortex 60°",  3 => "Vortex 120°",
        4 => "Vortex 180°", 5 => "Vortex 240°", 6 => "Vortex 300°",
        7 => "Maintain Vortex",
    );
    public $hideFiringModeSelector = true;    // facing is set by the map arrow (§3.5)
    protected $possibleCriticals = array();   // keep today's behaviour
}
```

`firingMode` is the **storage** for the facing — it is what persists to `tac_fireorder.firingmode`,
so no schema change and no new column. It is **not** the input method: the mode dropdown is
suppressed for the Jump Engine and the facing is set by dragging an arrow on the map (§3.5), which
writes the mode under the hood. `changeFiringMode` is called in several places (including
[TacGamedata.php:1184](source/server/model/TacGamedata.php#L1184)); with seven functionally
identical modes and no per-mode arrays, every one of those calls is a no-op.

**Default facing: always 0 (east)** — user ruling 2026-08-21, superseding the original
`(heading + 3) % 6`. The heading-derived default meant the mouth pointed back at the declaring ship,
so a straight-ahead projection was OK-and-done; it was dropped because a fixed starting point that
never depends on how the ship happens to be pointing is easier to read and to teach, and the turn
buttons now flank the facing arrow (§3.5), so stepping round costs one or two clicks. Set in
`weaponManager.queueJumpPointOrder`.

**`$weaponClass = "JumpPoint"`** — `targetHex` writes `damageclass` from
`weapon.data["Weapon type"].toLowerCase()` ([weaponManager.js:3271](source/public/client/weaponManager.js#L3271)),
so this gives every vortex declaration the damageclass `jumppoint`, which doubles as the
discriminator the server sweep uses to find them.

**⚠️ Four consequences that must be handled, not discovered:**

1. **Combat value shifts on every jump-capable ship.**
   [`calculateCombatValue`](source/server/model/ships/ShipClasses.php#L499) buckets by
   `$system instanceof Weapon` (multiplier **3**) before it tests `$system->primary` (multiplier
   **0**). Today a Jump Engine is `primary` and contributes nothing; as a Weapon a 40-box engine
   would contribute 120 to both current and total. Worse, the "defanged" branch
   (`if ($weaponCurr == 0) $multiplier = $weaponMultiplierMax`) would never fire while the engine
   lives. **Fix in the classifier**, not on the class: exclude `JumpEngine` from the weapon bucket
   so it stays in `core`. Verify with the replay harness — CV is in the snapshot.

2. **It appears in the React weapon list.** `WeaponList` filters on `system.weapon`
   ([WeaponList.js:33](source/public/client/UI/reactJs/system/WeaponList.js#L33)). This is arguably
   *desirable* (that's where you click to open a vortex), but it is a visible change on every
   capital ship in the game — decide deliberately, don't let it happen by accident.

3. **Every `instanceof Weapon` sweep now visits it.** The live ones to check are
   [firing.php:54/152/169/705/781/927/1009/1168](source/server/handlers/firing.php),
   [criticals.php:78](source/server/handlers/criticals.php#L78) (the overload force-shutdown),
   `BuyingGamePhase.php:554` / `Manager.php:876` (ammo counting), and
   [DBManager.php:3773](source/server/controller/DBManager.php#L3773). None should misbehave with
   `loadingtime = 1` and no ammo, but each needs a read.

4. **All 610 static blueprints regenerate.** `Weapon::stripForJson` sends far more than
   `ShipSystem::stripForJson`. Run `scripts/fvbuild.ps1 -Statics` and expect a large but mechanical
   diff in `source/public/static/json/**` and `shipsCombined.js`.

`PhasingDrive extends JumpEngine` inherits all of it. Shadow ships get vortex mechanics too, which
is correct — but check that the half-phase self-destruct in its `criticalPhaseEffects` still runs
*before* any vortex bookkeeping the parent adds.

### 3.2 The vortex unit

New [`source/server/model/ships/terrain/SpawnJumpPoint.php`](source/server/model/ships/terrain/SpawnJumpPoint.php):

⚠️ **The filename MUST match the class name** (bar case). `ShipLoader::getShipClassnamesStatic`
enumerates ship classes by stripping `.php` off every file under `model/ships/` and calling
`class_exists()` on the result — a mismatched name is skipped **silently** by the static generator
and every lobby path built on it. This section originally said `jumpPointSpawn.php`; that produced
a class the autoload map knew about and `Terrain.json` did not. See Stage 3 in §4.

```php
class SpawnJumpPoint extends Terrain {
    // shipSizeClass 5 (inherited) → isTerrain() true → unselectable by players
    // Enormous = false            → does NOT auto-ram passing units (copy jumpgateNew)
    // base + smallBase + nonRotating
    // one indestructible Structure(0, 1, true); nothing targetable
    // pointCost 0; excluded from fleet value
}
```

- Spawned with `userid` = the opening ship's `userid` so ownership is knowable, but
  `isTerrain()` is true via `shipSizeClass == 5`, so `isMyShip` still returns false outside
  Deployment and no player can click it. That is what we want for Phase 1.
- **Facing lives in the deploy `MovementOrder`'s `facing` field** — free persistence, free
  rendering, free replay.
- Spawn path is `createLoiteringMine`'s, verbatim in shape:
  `Manager::insertSingleShip` → `$vortex->spawned = $turn` → deploy `MovementOrder` at the target
  hex with the chosen facing → `Manager::insertSingleMovement` → `IndividualNote`.
  ⚠️ `insertSingleShip` casts the id to int — do not bypass it
  ([the string-id trap](source/server/controller/Manager.php#L2168)).

### 3.3 Vortex state persistence

One `IndividualNote` per vortex, hung on the **opening ship's Jump Engine** (the mine pattern):

| field | value |
|---|---|
| `notekey` | vortex ship id |
| `notekey_human` | `"Vortex"` (⚠️ column is `varchar(40)`) |
| `notevalue` | `"<openTurn>,<closeTurn or -1>"` |

`JumpEngine::onIndividualNotesLoaded` rebuilds: `$this->activeVortexId`, `$this->vortexOpenTurn`,
`$this->vortexCloseTurn`; and when `closeTurn` is set and `turn > closeTurn`, sets
`$vortex->removed = true; $vortex->removedTurn = closeTurn;` so the vortex disappears from the
board but stays correct in replay.

Closure rewrites the note with a real `closeTurn`. Because the note is on the *ship*, it survives
the ship's destruction and the replay still renders the vortex's full life.

### 3.4 The jump-out movement order

New `MovementOrder` type **`jumpout`**, `value` = the vortex ship id, appended after the move that
entered the hex.

- `shipManager.movement.getRemainingMovement` returns **0** once a `jumpout` exists this turn.
- ⚠️ `Movement::validateThrustPayment`
  ([movement.php:100](source/server/handlers/movement.php#L100)) explicitly *"rebuilds the tail as
  straight-line move steps until it has covered its full speed"* — it must treat `jumpout` as a
  legal terminator and stop rebuilding, or every jump-out will be silently un-done by the server.

### 3.5 Setting the facing — `UI.vortexFacing`

**The facing is part of the declaration transaction. No fire order exists until OK is clicked.**

```
select Jump Engine (ballistic weapon)
  → right-click target hex → "Target selected weapons on hexagon"
      → preview marker drawn on the hex, arrow at facing 0
      → turn left / turn right step the facing, arrow and buttons redraw
          → OK  .............. creates the FireOrder and closes the control
          → click away ....... discards; no order, nothing to clean up
          → deselect engine .. discards; same as clicking away
```

**The whole control is rigid and swings with the facing.** Ok sits directly in front of the facing
arrow and the two turn buttons flank it at ±60°, each on the side it turns toward — so the arrow
always points at the button that accepts it, and the left button is always the anticlockwise one.
The two turn arrows are angled to the facing as well, so the control looks at every facing exactly
as it does at facing 0, just turned — the same effect `#shipMovementUI` gets by CSS-rotating its
whole container to the ship's heading. **The one exception is Ok**, which is the word in `--fv-warn`
yellow and stays upright at every facing.

⭐ **Nothing in this control is an image.** The turn arrows started as `img/vortex{left,right}.png`
spun by `drawUIElement`'s `drawAndRotate`, and you could *see* it: rotating a 40 px raster resamples
it, so every facing except 0 came out soft and the curve's edges crawled as it stepped round.
`UI.vortexFacing.drawCurvedArrow` draws each one as an arc plus a filled head straight into the
button's canvas at the target angle — rasterised fresh, so crisp at all six facings, HiDPI-backed
(`min(devicePixelRatio, 2)`, the cap `webglScene`'s renderer uses), no asset to ship, and the yellow
read once from `--fv-warn` so it cannot drift from the marker's. Angles are `graphics.js`'s
convention throughout (degrees clockwise from east, screen y down), which is also what canvas
`arc()` measures, so they pass through unconverted. The two PNGs are now unused.

**Retuning the glyph** is a block of six named constants on `UI.vortexFacing` (`ARROW_SWEEP`,
`ARROW_TILT`, `ARROW_RADIUS`, `ARROW_THICKNESS`, `ARROW_HEAD_LEN`, `ARROW_HEAD_HALF`), each
documented where it is declared. Nothing else reads them and no vortex *rule* depends on any of
them, so they are safe to change by eye. The one constraint: the head tip is the outermost part of
the glyph and the canvas clips at half the box, so `ARROW_RADIUS + ARROW_HEAD_LEN * 0.7` has to stay
under ~0.47. `BUTTON_SIZE` is the knob for making the whole thing bigger — `drawCurvedArrow` resizes
the canvas itself, so game.php's `width`/`height` attributes do not need to track it.

Their radius follows the zoom (`UI.vortexFacing.buttonDistance`, clamped 58–130 px) because they are
placed around the hex, which scales: `#shipMovementUI`'s fixed pixels would bury Ok inside the hex
when zoomed in and strand it in empty space when zoomed out, and the zoom range is 0.1–7.

Because the order is only born on OK, there is never a half-declared vortex, no incomplete state to
nag about, and no "did they finish?" check before the Initial Orders commit can arm. The facing
cannot be changed after the fact either, which matches RAW: *"the vortex facing cannot be altered
once the jump point begins to form."* Re-aiming follows the ordinary ballistic idiom — remove the
firing order and declare again, which simply re-runs the transaction
([weaponManager.js:3184](source/public/client/weaponManager.js#L3184) /
[:3255](source/public/client/weaponManager.js#L3255)).

**Multiple declarations in one phase are serialised by the transaction itself** — one is in progress
at a time, each ending in OK or discard. That is a stronger guarantee than a shared singleton
control, and it is the direct answer to "several jump points opening in a single phase".

#### The seam

`weaponManager.targetHex` normally builds the fire order synchronously at
[weaponManager.js:3253-3277](source/public/client/weaponManager.js#L3253). **`ShadowFighterBomb`
already establishes the async alternative** at
[weaponManager.js:3242-3252](source/public/client/weaponManager.js#L3242): it pops a picker and
`continue`s, skipping the synchronous build entirely. Mirror it exactly:

```js
} else if (weapon.name === 'jumpEngine') {
    //Vortex facing is part of the declaration: open the on-map facing control and let
    //its OK button build the order. Async (callback), so we DON'T fall through to the
    //synchronous order build below — same shape as ShadowFighterBomb above.
    weaponManager.queueJumpPointOrder(selectedShip, weapon, hexpos, type);
    continue;
}
```

⚠️ The `continue` also skips the loop tail, so the OK callback must do the tail's work itself.
`queueShadowFighterBombOrder` ([weaponManager.js:3403-3431](source/public/client/weaponManager.js#L3403))
is the checklist to copy: `removeFiringOrder` → push the order → `unSelectWeapon` →
`webglScene.customEvent('HexTargeted', …)`. Miss the last two and the weapon stays selected and the
map never refreshes.

The vortex unit does not exist yet at this point (it spawns at the end of
`InitialOrdersGamePhase::advance`), so the preview marker is drawn from the **pending declaration**,
and the saved marker from the **fire order** — the same source the client already uses for hex-target
markers. That is why the facing has to live on the fire order rather than on a unit.

No Cancel button in the first cut: clicking away discards, and since nothing has been committed there
is no consequence to discover. `#shipMovementUI` already carries a `#cancel` element and callback if
playtest wants one added.

**Drag was considered and rejected.** FV has no drag-to-rotate anywhere: map drag is owned entirely
by scrolling, and [webglScrolling.js:27](source/public/client/renderer/webglScrolling.js#L27) is the
*only* caller of the `payload.capture(callback)` hook in
[webglScene.js:390](source/public/client/renderer/webglScene.js#L390). Buttons reuse an idiom the
game already has, already works on touch, and needs no new interaction paradigm.

**This is a sibling of `UI.shipMovement`, not an extension of it.** `drawShipMovementUI` is ~400
lines keyed to a ship (`ship.movement`, `canTurn(ship)`, …); threading vortex-ness through it would
be the wrong shape. Build a small `UI.vortexFacing` that reuses the *techniques and the art*:

| Reused as-is | Where |
|---|---|
| `UI.shipMovement.drawUIElement(e, x, y, s, dis, angle, path, canvasid, rotation, hit)` | [shipMovement.js:707](source/public/client/UI/shipMovement.js#L707) — fully generic, no ship dependency |
| `img/turnleft.png`, `img/turnright.png`, `img/ok.png` | all three already exist |
| The container-anchoring pattern | `PhaseStrategy.positionMovementUI` ([PhaseStrategy.js:713](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L713)) converts a game position to viewport and re-runs on the zoom/scroll callback lists |
| The markup shape | `#shipMovementUI` in [game.php:594](source/public/game.php#L594) — a container div, `.movement-icon` children, one canvas each |

New markup, mirroring that shape:

```html
<div id="vortexFacingUI">
    <div id="vortexTurnLeft"  class="movement-icon"><canvas id="vortexTurnLeftCanvas"  width="40" height="40"></canvas></div>
    <div id="vortexTurnRight" class="movement-icon"><canvas id="vortexTurnRightCanvas" width="40" height="40"></canvas></div>
    <div id="vortexConfirm"   class="movement-icon"><canvas id="vortexConfirmCanvas"   width="40" height="40"></canvas></div>
</div>
```

Anchoring note (**superseded at build — see Stage 2b deviation 3**): the intent was to expose
`getPosition()` on the pending marker so `positionMovementUI` worked unchanged. It could not be
reused: it also reads `icon.getLastMovement().heading` to rotate the container, and this control is
deliberately never rotated. `PhaseStrategy.positionVortexFacingUI` is the four-line sibling, and
`UI.vortexFacing.getPosition()` is where the pending hex's game position lives.

---

## 4. Build stages

Each stage is independently testable. Do not start the next until the previous is verified in a
fresh local game.

### Stage 1 — `JumpEngine extends Weapon` (no new behaviour) — **BUILT 2026-08-21**
Convert server + client classes, keep the boost path working exactly as today. Fix the combat-value
classifier. Regenerate autoload + statics + bundles.
**Gate:** replay harness green (162/162); CV of a jump-capable ship unchanged before and after; an
existing boost-jump still removes the ship at end of Fire.

*This stage is pure refactor and should be committed/verified on its own. Every later stage depends
on it and none of them will make sense if this one is subtly wrong.*

**What it actually took**, beyond the four consequences listed in §3.1 — four properties hold the
class at its old behaviour, and each is a Stage 2 touch-point:

| Held by | Why | Stage 2 |
|---|---|---|
| `$autoFireOnly = true` | nothing may select or fire it while there is no declaration | **remove** |
| `protected $possibleCriticals = array()` | Weapon's ReducedRange/ReducedDamage chart describes a gun | keep |
| `$turnsloaded = 1` | ships bought before the conversion have no `tac_systemdata` loading row, so it would arrive `null` and the icon would render as recharging | keep |
| `ShipSystem::setSystemDataWindow($turn)` (not `parent::`) | Weapon's version appends Damage/Range/Weapon type/Fire control | §6 rewrites the text |

Three client sweeps treat `instanceof Weapon` as "is a gun" and had to exclude `jumpEngine`
explicitly, the same way they already exclude `RammingAttack`. All three are reachable — the hulls
in question carry jump engines:
- `power.js` `setOnline` — the Vorlon Power Capacitor block (11 Vorlon hulls have a Jump Engine).
- `baseSystems.js` `PowerCapacitor.doActivate` / `doDeactivate` — same rule, other half.
- `baseSystems.js` `HyachSpecialists` ×2 — would write `Damage: undefined-undefined` on the tooltip.

`ShipIcon.showWeaponArc` needs no guard: `getWeaponReachInHexes` returns 0 for `range = 0`, and the
callers draw nothing for 0. That changes the moment Stage 2 sets `$range = 4` — a 4-hex 360° circle
will then appear on hover, which is probably what you want but is a decision, not a side effect.

The replay-harness delta for this stage is exactly two additive payload keys on jump engines,
`turnsloaded` and `fireOrders` (both from `Weapon::stripForJson`), in every affected game. Nothing
else moved — no CV, damage, movement, to-hit or masking result. Re-record to accept it.

### Stage 2 — declaration — **BUILT 2026-08-21**
`hextarget`/`ballistic`/`range 4`/7 firing modes, mode selector suppressed.
Server-side legality in `Firing::validateFireOrders`: ≤4 hexes, hex not Terrain/vortex/gate/Enormous,
engine online and undestroyed, one vortex per ship.
**Gate:** declaring writes a `tac_fireorder` row with the right `x`, `y`, `firingmode`; the enemy's
gamedata during phase 1 does not contain it.

**How to declare:** select the ship, click the Jump Engine icon in the weapon bar, left-click a hex
within 4. `InitialPhaseStrategy.onHexClicked` routes straight to `weaponManager.targetHex` whenever
a ballistic weapon is selected — the same flow as a mine launcher. (Before Stage 2b landed this
pinned every declaration to mode 1, because nothing set the facing; the arrow control is what makes
the other five reachable, and the OK button is now what creates the row at all.)

**What it actually took**, beyond the properties listed above:

| Site | Why it needed touching |
|---|---|
| `JumpEngine::setSystemDataWindow` | ⭐ **`data["Weapon type"]` is load-bearing, not cosmetic.** `targetHex` stamps `damageclass` from `weapon.data["Weapon type"].toLowerCase()`, and Stage 1 routes this class to `ShipSystem::setSystemDataWindow`, which never sets it. Without the hand-added line the declaration throws on undefined and cannot be made at all. `data["Range"]` added alongside; the boost-era Special text rewritten (it told the player to do something now impossible) |
| `Firing::fireWeapons` ship loop | ⭐ That loop skips `intercept`/`selfIntercept`/`prefiring` but **NOT `ballistic`** — launched ballistics are meant to reach `fire()`. `Weapon::fire` early-returns on the null target, but only *after* rolling a d100 and stamping `->rolled`/`->notes`. Explicit `instanceof JumpEngine` skip so the declaration reaches Movement untouched |
| `hideFiringModeSelector` | New property, declared **on `JumpEngine` only**, never on `Weapon` — a base-class default would write `"hideFiringModeSelector":false` into all ~20k weapon blueprint entries (§8). Read at `SystemInfoButtons.canChangeFiringMode` and `SystemIcon` (the letter badge) |
| `weaponManager.getVortexHexBlock` | Client mirror of the server rule, called from `targetHex`. Sweeps `gamedata.ships` rather than `gamedata.blockedHexes`: blockedHexes holds **Enormous units only**, and the Stage 3 vortex is Terrain that is deliberately *not* Enormous |
| `BallisticIconContainer` | `case 'jumppoint'` — blue hex labelled with the mode name (= the facing). The default is `hexRed` with no text, which reads as incoming fire |
| `JumpEngine::hasVortexDeclaration` / `::vortexRevealNotes` + `ShadingField` / `CloakingDevice` `case 1:` + the `isHidden` exemption in `targetHex` | The concealment ruling (§2.1). `vortexRevealNotes` **returns** notes rather than pushing them, because `ShipSystem::$individualNotes` is `protected` |

**⚠️ The POST-side/real-load split inside `validateFireOrders`.** `InitialOrdersGamePhase::process`
passes POST-side fire orders with a **real** `getTacGamedata` load. So `$shooter` is the real ship
(which is what makes `getHexPos()` trustworthy — a POST-side ship has no movement history), but
`$shooter->systems[...]->fireOrders` are the **DB's**, and this turn's declarations are not in the DB
yet. The one-vortex-per-ship test therefore scans the `$fireOrders` array being submitted, and stops
at the order under test so the *first* declaration survives. For the same reason `detachFireOrder`
is not called on this path — it matches by object identity and would never match; `->rejected` alone
is what keeps an order out of `submitFireorders`.

Two consequences that fall out of the conversion and are correct, but are new:
- The engine now shows a **4-hex 360° reach overlay** on hover (`getWeaponReachInHexes` returns 4
  once `range` is set). Kept — it is exactly the "where can I put a vortex" answer.
- `firedOnTurn` counts the declaration, so the icon reads **discharged during Movement and Fire of
  the declaring turn** and reloads at turn advance (`normalload = loadingtime = 1`). Semantically
  right, and it leaves the engine loaded again for the next turn's Maintain.

**Both loose ends are now ruled on and closed (2026-08-21)** — **LoS is required** (no code; it was
already the behaviour) and **a concealed ship may declare, which reveals it**. The `Stealth` class
needed no code either; the Torvalus Shading Field and the Cloaking Device did. Full statement and
the reasoning in §2.1.

Gate result 2026-08-21: `fvbuild.ps1 -Check` green — autoload current, ship-data validator 0 new
findings, replay harness **163/163 with no diffs at all**. Nothing Stage 2 changed reaches
`Weapon::stripForJson`; it all travels by static blueprint, which the harness does not snapshot.

**Retiring the boost path — `boostable = false` ONLY (user ruling 2026-08-21).** Set
`$boostable = false` on `JumpEngine` and change *nothing else about boosting*: keep
`isOverloading()`, `doHyperspaceJump()`, the sweep at
[firing.php:1720](source/server/handlers/firing.php#L1720), the `isJumpEngine` branch in
[SystemPowerSettings.js:293](source/public/client/UI/reactJs/system/SystemPowerSettings.js#L293)
and the boost-driven `jumping[]` checklist in
[gamedata.js:868](source/public/client/gamedata.js#L868). That flag alone stops every NEW boost
while letting a boost already committed on the live server resolve normally. **Verified viable
2026-08-21** — the four facts it rests on:

1. `DBManager::submitPower` writes whatever it is handed and validates nothing against `boostable`;
   `getPowerForShips` → `ShipSystem::setPower` loads it back unconditionally. A `tac_power` type-2
   row written before the deploy therefore still lands on the engine after it, and
   `isOverloading($turn)` still returns true → the end-of-Fire sweep still jumps the ship.
2. The client's boost helpers (`getBoost`, `isBoosted`, `countBoostPowerUsed`, `unsetBoost`) never
   read `boostable` either, so an existing boost keeps its yellow icon, its `JUMP` output display
   and its commit-time warning.
3. `boostable` reaches the client ONLY through the static blueprint (`addBlueprintFieldsForJson`
   runs for enhancement-added systems only), which regenerates at deploy. An already-open tab keeps
   the old value until reload — so the stale-client window is one page load, not one turn.
4. The Power Settings panel does not vanish: `canOffline()` passes on `system.canOffLine ||
   system.powerReq > 0`, and every Jump Engine has a `powerReq`.

Two consequences to carry:
- **Stage 4 must NOT delete the old sweep** (see there). Both removal paths can coexist safely -
  Movement resolves first, so `doHyperspaceJump`'s `$primaryStruct->isDestroyed()` early return
  makes the end-of-Fire call a no-op for a ship that already jumped through a vortex.
- Not server-enforced: a stale client could still POST a type-2 row and get an old-style jump. That
  is the behaviour being preserved, so it is a feature for one deploy cycle - but it means the
  cleanup deploy is what actually closes the door.
  **⚠️ SUPERSEDED 2026-08-22: there is no cleanup deploy.** §9 puts 195 hulls back on the boost
  path deliberately, so the door stays open by design and `$boostable` is now a per-hull setting
  rather than a transition flag. See §9 trap 3.
- `canOffline()` also requires `!weaponManager.hasFiringOrder(ship, system)`, so once a vortex is
  declared the engine can no longer be powered down that phase. Correct (it matches every other
  weapon) but new, and worth a line in the FAQ at §6.

### Stage 2b — the facing control (§3.5) — **BUILT 2026-08-21**
The `queueJumpPointOrder` branch in `targetHex`, preview marker sprite exposing `getPosition()`,
`#vortexFacingUI` markup, `UI.vortexFacing` with turn-left/turn-right/OK, default facing from the
declaring ship's heading, anchoring wired into the zoom/scroll callback lists.
⚠️ Two things the async branch must not forget, both copied from `queueShadowFighterBombOrder`: the
OK callback owns `unSelectWeapon` + the `HexTargeted` event, and any sprite mutation outside the
animation list must call `requestRender()` or the arrow will not redraw.
**Gate:** OK creates exactly one `tac_fireorder` row with the chosen `firingmode`; clicking away
creates **none**; deselecting the engine creates **none**; the control opens on facing 0 every time;
two ships declaring in the same phase produce two correct rows; the control follows the map on zoom
and scroll (including the button ring re-laying out); the weapon deselects after OK.

*Split from Stage 2 deliberately — Stage 2 is server-truth and verifiable from a DB export, 2b is
pure interaction.* The seam was already in place: `getVortexHexBlock` is the legality check the OK
callback reuses, and the `weapon.name === 'jumpEngine'` guard in `targetHex` sat exactly where
`queueJumpPointOrder` went. **2b is also what makes the facing testable at all** — Stage 2 pinned
every declaration to mode 1.

**Seven files, no server-class changes, so no autoload and no statics** — only `yarn build`
(`fvbuild.ps1 -Client`):

| File | What it got |
|---|---|
| `client/UI/vortexFacing.js` (new) | `UI.vortexFacing` — the whole transaction: preview marker, three buttons, open/turn/confirm/close |
| `game.php` | `#vortexFacingUI` markup (three `.movement-icon` divs, one 40×40 canvas each) + the script tag |
| `styles/tactical.css` | `#vortexFacingUI` block, a copy of `#shipMovementUI`'s three rules minus the rotation |
| `weaponManager.js` | the `weapon.name === 'jumpEngine'` async branch; `queueJumpPointOrder` (raise) + `createJumpPointOrder` (the OK half) |
| `PhaseStrategy.js` | `onVortexFacingRequested` / `hideVortexFacingUI` / `positionVortexFacingUI`, the zoom+scroll callback lists, and a `close()` in `deactivate` |
| `firing.php` | `getVortexDeclarationBlock` now refuses a `firingMode` outside 1–6 (see below) |
| `JUMP_POINTS_PLAN.md` | this, plus the §2.2 direction-convention correction |

**Four things that landed differently from the §3.5 sketch, all deliberate:**

1. **The event seam is `webglScene.customEvent('VortexFacingRequested', …)`, not a direct call.**
   `weaponManager` has no handle on the phase strategy, and `PhaseStrategy.onEvent` already
   dispatches `this['on' + name]` — the same route `HexTargeted` and `SystemDataChanged` take. The
   payload carries its own `onConfirm` closure, so `weaponManager` still owns order construction and
   `PhaseStrategy` owns nothing but placement and lifetime.
2. **Click-away discard rides `onClickCallbacks`**, the one-shot list `showShipTooltip` and
   `showSelectFromShips` already use. ⭐ The ordering inside `onClickEvent` is what makes this work
   and is worth not re-deriving: the list is filtered **and run before** the click is dispatched to
   `onHexClicked`, so a click that opens a *new* declaration first discards the pending one, and the
   callback pushed during dispatch lands on the fresh array and survives to the next click. Matched
   by token identity (the payload object), so a stale callback cannot tear down a newer transaction.
3. **`positionMovementUI` is not reused and the marker does not carry `getPosition()`.** That
   function also reads `icon.getLastMovement().heading` to rotate the container, and the vortex
   control is deliberately **not** rotated — the turn arrows are screen gestures, and a rotated tick
   reads as broken. `positionVortexFacingUI` is four lines and asks `UI.vortexFacing.getPosition()`
   instead, which is where the pending hex's game position lives.
4. **No new art and no new sprite class.** The arrow is `img/directionOfMovement.png` — the same
   asset `ShipIcon` uses for a ship's heading, built with a bare `new window.webglSprite(path, size,
   z)` (ShipIcon does exactly that) at `z = -99`: above the ballistic hexes at −100, below terrain
   (−50) and ships (0). The hex itself is an ordinary `BallisticSprite` in the identical livery
   `createBallisticIcon` gives a committed `jumppoint` order — blue, labelled with the firing-mode
   name — so the preview and the saved marker look the same and OK is visually a no-op.

**What Stage 2b actually took, beyond the plan:**

| Site | Why |
|---|---|
| §2.2's worked example | ⭐ **Its hex coordinates were wrong.** Direction 0 is `(q+1, r+0)` — EAST — in both `CubeCoordinate::NEIGHBOURS` and client `Offset.neighbours`, and direction increases *clockwise* on screen; the example named `(10,9)`, which is direction 2. The `D = (F+3)%6` formula was unaffected, but Stage 4's entry test would have been written against the example. Corrected in place, with the two authorities cited |
| `firing.php` `getVortexDeclarationBlock` | The facing is player-settable from now on, so it is worth validating: modes 1–6 are the six facings, mode 7 is Stage 5's Maintain and is not a legal *opening*. Only a tampered client can produce anything else, but an out-of-range mode would reach the Stage 3 spawn sweep as a nonsense facing |
| `UI.vortexFacing.swallowDoubleEvent` | The buttons bind `"click touchstart"` because `UI.shipMovement` does — but a facing **steps**, and on touch `touchstart` plus the synthetic `click` 300 ms later would turn twice per tap. The movement UI tolerates that; a stepper cannot. 350 ms swallow, local to this module (`UI.shipMovement.checkUITimeout` exists for the same purpose and is called from nowhere) |
| `createJumpPointOrder` re-runs `getVortexHexBlock` | A server poll can land between picking the hex and clicking OK |
| Script-tag placement | ⚠️ `shipMovement.js` **assigns** `window.UI` wholesale, so `vortexFacing.js` must load after it or its module is silently wiped. Noted in both files; the module also opens with `window.UI = window.UI \|\| {}` so a future reordering degrades instead of exploding |

**Exactly ONE fire order per declaration**, not one per `weapon.guns` as the synchronous path builds:
a ship may hold one vortex, and `getVortexDeclarationBlock` rejects every declaration after the
first in the same submission anyway.

**Second pass, same day (user feedback):** the control opens on **facing 0 every time** rather than
on the ship's heading (§3.1); the whole control **swings rigidly with the facing** instead of sitting
above the hex — buttons orbiting at a **zoom-relative** radius and the arrows angled to match; Ok is
the **word in `--fv-warn` yellow**, not a tick, and is the one part that stays **upright**; and
**deselecting the Jump Engine discards the transaction**. The user also recoloured the whole vortex
livery from blue to `--fv-warn` yellow (`BallisticIconContainer`'s `case 'jumppoint'` and the
preview hex). Finally the turn arrows stopped being images at all — rotating a bitmap was visibly
resampling it, so they are **drawn** as arcs now (§3.5), and `img/vortex{left,right}.png`, added and
superseded the same afternoon, are unused.

Three notes from that pass worth keeping:
- **The deselect hook goes in `weaponManager.unSelectWeapon`, not in `SystemIcon`.** That is the
  choke point every deselect route funnels through — the weapon-list icon toggle, `deselectShip`'s
  sweep when another ship is selected, and the phase teardown — so one three-line guard covers all
  of them. Ordering is safe: `confirm()` closes the control *before* calling `onConfirm`, so the
  `unSelectWeapon` at the end of `createJumpPointOrder` finds nothing pending.
- **Ok cannot go through `drawUIElement`.** That function ends in `graphics.getCanvas(canvasid)` →
  `drawUIimage`, and a text button has no canvas: `getCanvas` returns null and `clearSmallCanvas`
  throws on it. `UI.vortexFacing.placeElement` is `drawUIElement` minus the bitmap step — and once
  the turn arrows became drawn arcs too, all three buttons went through it and the control stopped
  calling `drawUIElement` at all.
- **⭐ Rotating a small bitmap is visible; drawing at the angle is not.** The general lesson, not a
  vortex one: any FV chrome that has to appear at an arbitrary angle should be stroked into its
  canvas at that angle rather than fed to `drawAndRotate`. `graphics.js` already has the vocabulary
  (`drawCircleSegment`, `drawArrow`) and the same angle convention as `drawUIElement`, so the swap
  is local. Add a HiDPI backing store while you are there — `min(devicePixelRatio, 2)`, matching
  `webglScene`'s renderer — or the result is still soft and the exercise was pointless.

### Stage 3 — the vortex unit — **BUILT 2026-08-21**
`SpawnJumpPoint` + spawn sweep at the end of `InitialOrdersGamePhase::advance` + the note +
`onIndividualNotesLoaded`. Icon (`img/ships/JumpPoint.png`) and facing rendering.
⚠️ Do **not** branch on `$gamedata->phase` inside `advance()` — it already reads 2 by then.
**Gate:** a declared vortex exists as a `tac_ship` row with a `deploy` movement row carrying the
chosen facing, is visible to both players from Movement onward, and survives a page reload.

**Four files, all server-side plus one image — no JS, so no `yarn build`; autoload and statics
only (`fvbuild.ps1 -Server`):**

| File | What it got |
|---|---|
| `ships/terrain/SpawnJumpPoint.php` (new) | the vortex unit — Terrain, `Enormous = false`, `pointCost 0`, `variantOf 'NONE'`, one OSATCnC and one **indestructible** `Structure(0, 1, true)` |
| `baseSystems.php` `JumpEngine` | `$spawnableClasses`, the three state properties, `spawnDeclaredVortices` / `getVortexDeclaration` / `hasOpenVortex` / `openVortex` / `restoreVortexFromNote`, and the `notekey_human` branch in `onIndividualNotesLoaded` |
| `InitialOrdersGamePhase.php` | one call, **last** in `advance()` |
| `img/ships/JumpPoint.png` (new) | see below |

Plus, in the second pass below (five more files, all presentation): `ShipIcon.js`,
`BallisticIconContainer.js`, `UI/vortexFacing.js`, `game.php`, `styles/tactical.css`.

**What it actually took, beyond the sketch in §3.2–3.3:**

| Site | Why |
|---|---|
| ⭐ **The FILENAME** | This section used to name the file `jumpPointSpawn.php`. That is **silently fatal**: `ShipLoader::getShipClassnamesStatic` derives class names by stripping `.php` off every file under `model/ships/` and calling `class_exists()` on the result, so a file whose name does not match its class (bar case) is skipped by the static generator and every lobby path built on it — no error, the ship just does not exist. Verified: with `jumpPointSpawn.php` the class was **absent from `Terrain.json`**; renamed to `SpawnJumpPoint.php` it appears. phpab does not care (the autoload map was correct either way), which is exactly what makes it quiet |
| `$spawnableClasses = array('SpawnJumpPoint')` on `JumpEngine` | **Load-bearing, not a nicety.** `shipSizeClass` is NOT in `BaseShip::stripForJson` — it reaches the client through the STATIC blueprint, which `model/ship.js` merges by `faction` + `phpclass`. A vortex that appears on a *poll* (no page reload) with no blueprint would arrive with `shipSizeClass` undefined, so `gamedata.isTerrain` would return **false** and it would be a selectable, targetable ordinary ship that does not block a second vortex in its hex. game.php's `$spawnableClasses` sweep is what preloads it |
| The sweep runs **last** in `advance()` | `Manager::insertSingleShip` adds the vortex to `$gamedata->ships` immediately, and `SimultaneousMovementRule::getNewActiveShip`'s `array_filter` — unlike `hasShipsAtIniative` right next to it — does **not** exclude terrain. Spawning before the active-ship selection could hand a vortex to the Movement phase as an active unit. The two early returns above it cannot strand a declaration: a ship that just declared one is on the board, alive, not terrain and not a mine, which is exactly what `hasShipsAtIniative` looks for |
| No `SystemData` work in `openVortex` | `createLoiteringMine`'s weapon-loading block ends in `Manager::insertSystemData(SystemData::getAndPurgeAllSystemData())`, and **purging mid-advance would steal** the pending system data `advanceGameState` flushes after its `onAdvancingGamedata` sweep. A unit with no weapons needs none of it |
| `onIndividualNotesLoaded` branches on `notekey_human` | Two KINDS of note now hang on a Jump Engine. The old body assigned *every* note's value to `preJumpValue`; `'Vortex'` notes are peeled off first and everything else falls through unchanged, so the `jumped` CV-preservation path is untouched |
| `hasOpenVortex($turn)` | §2.1's "one vortex per ship **at a time**" across turns — `Firing::getVortexDeclarationBlock`'s test only covers a second declaration in the same submission. Consults the close turn, not just the id, so it is already the final Stage 5 rule |

**The note shape, and the Stage 5 seam.** `notekey` = the vortex ship id, `notekey_human` = `"Vortex"`
(6 of the column's 40), `notevalue` = `"<openTurn>,<closeTurn or -1>"`, stamped **turn 1 / phase 1**
exactly as the mine's spawn note is — `getIndividualNotesForGame` fetches `turn <= the turn being
viewed`, so a note stamped with the real open turn would be invisible to every earlier replay turn
and the vortex would lose its `spawned` marker there. There is **no UPDATE path for notes**
(`insertIndividualNote` refuses anything that already has an id), so Stage 5 records a closure by
**appending** a second note at turn 1 / phase 2: notes load ordered by turn then phase and the
restore is last-wins per vortex, so the later note simply overrides. Verified working.

**The icon is procedurally generated placeholder art** — a Node script (no image library in the
tree, so the PNG is encoded by hand) draws an amber ring with a dark throat, faint spiral arms and
a bright flared **mouth pointing EAST**, the ring fading toward the west so the facing reads at a
glance. East because facing 0 is east and `ShipIcon.setFacing` rotates the ship sprite to
`movement.facing` — which is the whole of "facing rendering"; no client code was needed. Replace it
with real art whenever you like: nothing depends on the drawing, only on the mouth being at +x.

**One thing to watch in playtest, deliberately left alone:** on the *declaring* turn both the vortex
unit AND the yellow `jumppoint` ballistic hex (labelled with the facing) are drawn on the same hex —
`TacGamedata::onConstructed` only builds ballistics for `$fire->turn == $this->turn` and from phase 2
on, so the marker vanishes at turn advance and the icon speaks alone from N+1. Reads as
"forming, not usable yet", which is right, but it is two things on one hex.
*Resolved in the same-day second pass below: the unit is no longer drawn on the forming turn at all,
so there is only ever one thing on the hex.*

#### Stage 3 second pass, same day (user feedback)

Five changes, four of them presentation. **No new server logic** — the one server change is a
constant and a `+ 1`.

1. **The vortex unit now carries a permanent facing arrow.** `ShipIcon` builds no direction sprites
   for Terrain (terrain does not move, so a heading arrow is meaningless), and reading the facing off
   the icon art alone was too subtle. New blueprint property **`$facingArrow`** — a path — makes
   `ShipIcon` lay that image over the unit, always visible (not hover-gated like a ship's
   prow/heading arrows) and rotated with it in `setFacing`. Declared on `SpawnJumpPoint` **only**,
   never on `BaseShip`: measured, it adds the key to exactly **1 entry across all 93 faction
   blueprint files** (§8's reasoning, same as `hideFiringModeSelector`).
2. **⭐ The unit is not drawn at all on the turn it forms** — see the revised §2.3. `spawned` becomes
   `openTurn + 1`; the ballistic marker now reads **"Jump Point Forming"** instead of the firing-mode
   name, and the facing it used to spell out is shown by an arrow over the hex instead.
   Two consequences worth keeping:
   - The arrow needed **its own sweep** (`generateJumpPointArrows`, next to `generateReinforcementHexes`),
     NOT a line inside `createBallisticIcon`. An existing ballistic icon is *updated*, not rebuilt,
     on later polls (`createOrUpdateBallistic`), so a `syncSceneObject` call inside the create path
     would run once and then let `pruneSceneObjects` reclaim the arrow on the very next poll. The
     orders are collected during the main `ballistics.forEach` so the arrows inherit its turn/phase
     and masking filters exactly.
   - **`removedTurn` is `closeTurn + 1`**, not `closeTurn`. Both `shouldBeHidden` and
     `ReplayAnimationStrategy` hide a unit outright when `spawned >= removedTurn`; with
     `spawned = openTurn + 1` that reduces to `openTurn >= closeTurn`, i.e. true only for a vortex
     that closed on the very turn it was declared — a Stage 5 jump-failure, which never formed and
     should indeed never be drawn. Using `closeTurn` itself would make an ordinary unmaintained
     vortex (open exactly one turn — the common case) vanish from its own replay.
3. **The turn arrows got `ARROW_OPACITY` and `ARROW_ROTATION` knobs.** Opacity is one `globalAlpha`
   over shaft, head and drop shadow together — fading only the fill leaves a solid black outline
   round a ghost, which reads as a rendering fault. `ARROW_ROTATION` rotates the pair **rigidly**
   and is deliberately distinct from the existing `ARROW_TILT`, which is *mirrored* (it splays them
   apart) and so cannot be used as a plain "turn them a bit" control.
4. **⭐ The button ring's upper distance cap was the zoom bug.** `buttonDistance` was
   `max(58, min(130, hexRadius + 30))`. Note **zoom is a DIVISOR** — `getHexHeightViewport()` is
   `hexHeight / zoom`, so zoomed IN is a *small* zoom value and a *huge* hex — and at zoom 0.3 the
   hex radius is 167px, so a ring capped at 130px sat entirely inside the hex. The cap is gone: the
   ring now always clears the rim by `BUTTON_GAP + BUTTON_SIZE/2 + TURN_BUTTON_INSET`, measured to
   the inner edge of the innermost button (a turn arrow, which tucks in behind Ok). The only cap
   left is `VIEWPORT_LIMIT` — 0.42 of the smaller viewport dimension — past which the hex is bigger
   than the screen and "outside the hex" would mean "off the screen"; on a 1000px-tall window that
   only bites below zoom ~0.12.
5. **Ok is now a drawn confirm glyph** — a filled `--fv-custom` disc with a dark tick punched out,
   `drawConfirmIcon`, same drawn-not-rotated-not-imaged approach as the turn arrows. A *solid disc*
   rather than a bare yellow tick on purpose: a thin stroked tick on a transparent ground is what
   the word replaced in the first place (it vanishes over bright terrain and gives the finger nothing
   to aim at), and the disc's silhouette is what distinguishes it from the two curved arrows when all
   three are the same colour. `#vortexConfirm` swapped its `<span>` for a `<canvas>`; the
   `.vortexOkLabel` CSS block is gone. Knobs: `CONFIRM_DISC / _RING / _TICK_LEN / _TICK_WIDTH /
   _INK / _OPACITY`.

Gate result 2026-08-21 (after the second pass): `fvbuild.ps1 -Check` **all green** — autoload
current, ship-data validator 0 new findings, replay harness **159 passed / 0 failed**. (The earlier
160/1 run's single failure was game 4302's usual baseline staleness, proved not ours by
`git stash` + re-run giving byte-identical output; it passes again now.) End-to-end verified against
a real local game inside a rolled-back transaction, **36 assertions**: the `tac_ship` row, the
`tac_shipmovement` deploy row at the declared hex with `facing = mode - 1`, the note, the reload
round-trip (`spawned = openTurn + 1`, `activeVortexId`, `vortexOpenTurn` restored; another ship's
Jump Engine unaffected), the double-sweep no-op, and all three closure cases Stage 5 will lean on
(ordinary unmaintained, never-formed, and still-open-on-its-closing-turn).

### Stage 4 — jumping out — **BUILT 2026-08-21**
`jumpout` movement type, `canJumpOut`/`doJumpOut` in `movement.js` with the §2.2 entry test,
a new `ShipTooltipMovementMenu` wired into
[MovementPhaseStrategy.js:95](source/public/client/renderer/phaseStrategy/MovementPhaseStrategy.js#L95),
the `validateThrustPayment` terminator, and removal in `MovementGamePhase::advance`
(`doHyperspaceJump` minus the roll → `submitDamages($id, $turn, $gd->getNewDamages())`, mirroring
[PreFiringGamePhase.php:25](source/server/Phase/PreFiringGamePhase.php#L25)).
**Do NOT delete the old sweep** at [firing.php:1720](source/server/handlers/firing.php#L1720) —
it is the transition path for boosts already committed on live when Stage 2 ships (see Stage 2).
Both removal paths coexist safely: Movement resolves before Firing, so `doHyperspaceJump`'s
`$primaryStruct->isDestroyed()` early return no-ops for a ship that already left through a vortex.
It comes out in the cleanup deploy one cycle later, together with `isOverloading()`, the
`isJumpEngine` branch in `SystemPowerSettings.js` and the boost-driven `jumping[]` checklist.
**⚠️ SUPERSEDED 2026-08-22: none of those five come out.** §9 makes the boost path supported
behaviour for 195 hulls. The sweep stayed, and was rewritten to select by `instanceof JumpEngine`
instead of by `displayName` — which fixed a pre-existing bug that had hidden 195 engines from it.
See §9 traps 2 and 3.
**Gate:** ship enters from the correct side → button appears, movement ends, ship gone before
Pre-Firing, CV preserved in the fleet list. Wrong side → no button. Wrong side via client tampering
→ server refuses.


**Nine files — five server, three client, one new client file, plus one new icon. No new PHP class,
so autoload and the static blueprints are untouched; `fvbuild.ps1 -Client` only.**

| File | What it got |
|---|---|
| `handlers/movement.php` | the whole rule: `getJumpOutOrder` / `getOpenVortexInHex` / `getVortexEntryDirection` / `getEntryDirection` / `getJumpOutVortex`, plus `validateJumpOutSubmission` (process-time) and `resolveJumpOuts` + `applyJumpOut` (advance-time). And the `jumpout` terminator inside `validateThrustPayment` |
| `Phase/MovementGamePhase.php` | two calls: `validateJumpOutSubmission` in `process()`, `resolveJumpOuts` **first** in `advance()` |
| `handlers/firing.php` | `Firing::isHyperspaceLogOrder` + **four** guards — see below |
| `ships/ShipClasses.php` | `BaseShip::hasJumpedToHyperspace` / `getCVBeforeJump` / `hasHyperspaceJumpDamage`, and `calculateCombatValue` asks the SHIP instead of the engine |
| `systems/baseSystems.php` | `Structure` learns the `jumped` note (private `$preJumpValue`, `onIndividualNotesLoaded`, `getCVBeforeJump`) |
| `client/movement.js` | the client mirror + `hasJumpedOut` guards on eleven manoeuvre gates and `getRemainingMovement` |
| `client/UI/shipTooltipMovementMenu.js` (new) | the Movement phase's own tooltip menu — one button |
| `client/renderer/phaseStrategy/MovementPhaseStrategy.js` | `selectShip` builds that menu instead of the base one |
| `client/ships.js` | `hasJumpedNotDestroyed` no longer requires a jump engine |
| `game.php`, `styles/shipTooltip.css`, `img/jumpOut.png` (new) | the script tag, the `.jumpOut` rule, the 40×40 icon |

**What it actually took, beyond the sketch above:**

| Site | Why |
|---|---|
| ⭐ **`Firing::isHyperspaceLogOrder`, and FOUR guards for it** | Leaving writes a RammingAttack fire order at 100/100 against the departing unit itself, purely so the combat log has a line to render (the log is fire-order-driven — `combatLog.js:408` already suppresses the structure damage). The boost path never needed guarding because `doHyperspaceJump` runs at the very END of `fireWeapons`, after every gather. A vortex jump-out resolves **a whole phase earlier**, so by the time Pre-Firing loads its orders this one is sitting in `tac_fireorder` looking exactly like a ram — and `firePreFiringWeapons`, `preparePreFiring`, `prepareFiring` and `fireWeapons` would each pick it up and ram the departed unit into itself. Matching on `damageclass` rather than on type keeps the guard independent of how the order was submitted |
| ⭐ **CV preservation could not stay on the jump engine** | §2.5's "any unit may use any open vortex" includes units that have no jump engine at all, and `calculateCombatValue` gated the whole jumped-not-destroyed branch on `getSystemByName("JumpEngine")`. So did `HangarOps::processCarrierDestructionEscapes`, which would have rolled a d20 escape for the hangar of an engineless carrier that left perfectly safely. Both now ask `BaseShip::hasJumpedToHyperspace()`, which **delegates to the engine when there is one** — the boost path's behaviour is bit-for-bit unchanged — and reads the primary structure's own `HyperspaceJump` entry when there is not. `Movement::applyJumpOut` picks the note's host to match |
| The engineless test has to be **stricter** than `JumpEngine::hasJumped` | That method never checks a jump entry is actually PRESENT; the engine's existence was doing the filtering. Without either, any unit destroyed by something other than damage to its primary structure — a collision, a captured hull — reads as having jumped. `hasHyperspaceJumpDamage` requires the entry *and* keeps the original "non-jump damage is short of destroying it" half |
| `getRemainingMovement` returns **0**, and eleven gates got a guard | "Ends that unit's movement immediately" is two separate things: the hexes are forfeit (the 0, which also arms Commit through `isMovementReady`) and no further manoeuvre may be plotted. `canMove`/`canSlip` fall out of the 0 and `canDetach` out of `hasDeletableMovements`, but turning, pivoting, rolling, jinking, speed changes, half-phasing and contraction all needed saying. The **cancel** button deliberately still works — deleting the order is the ordinary undo and puts everything back |
| ⭐ **The movement history the server can see is bounded** | `DBManager::getMovesForShips` fetches `turn = 1 OR turn = N-1 OR turn = N OR type IN (deploy,start)`. §2.2's "walk back through prior turns" therefore reaches exactly one turn back — but the CLIENT is fed the same array, so the two agree, which is what matters. A unit that has sat in the vortex hex for three turns cannot use it; flying one hex out and back in is the workaround, and it is also the more sensible reading of "entered the hex" |
| `validateJumpOutSubmission` needs BOTH movement arrays | The POST carries this turn's moves only ([ajaxInterface.js:950](source/public/client/ajaxInterface.js#L950)), so process-time validation is handed `$activeShipMovementBackup` — the authoritative ship's stored movement — for the earlier turns, exactly the same seam the thrust validator uses two lines above |
| The path is **truncated at the jump-out order** before the entry test | That order is where movement ends; anything a client appended after it is not part of how the unit got there |
| Attached units are taken **explicitly** | Plan §5 trap 7. A pod's rows are all type `attached` and mirror the host's positions, so it can never satisfy the entry test on its own — which is exactly why it would otherwise be left sitting on an empty hex. `resolveJumpOuts` walks `hasAttached` after the host |
| `resolveJumpOuts` runs **first** in `advance()` | A unit that has left then reads `isDestroyed()` for the rest of the method: no dummy `end` move, no post-move stealth check, no Pre-Firing slot held open for it. It is also what makes a double `advance()` a no-op |

**Two deliberate gaps, both reported rather than silently half-built — BOTH CLOSED IN STAGE 6:**

1. ~~**Fighter flights cannot jump out.**~~ A flight has no primary Structure, so the removal every
   other unit uses did not exist for it. **Stage 6 built it**: every craft takes a `HyperspaceJump`
   entry and the CV note goes on the sample fighter. A flight docked in a carrier that jumps still
   goes with it, unchanged, and that is still a different question with a different answer
   (`jumpedWithCarrier`) because nothing on that flight is damaged at all.
2. ~~**No commit-time warning.**~~ **Stage 6** added *"The following units will LEAVE THE BATTLE
   through a jump point"* to the Movement-phase commit dialogue.

**Gate result 2026-08-21:** `fvbuild.ps1 -Check` — autoload map current, ship-data validator 0 new
findings, replay harness **158 passed / 1 failed**, the 1 being game 4302's usual baseline staleness
(`overloadturns: added`, `EW/1: removed`, a shipid shifting by one and `masking.txt` going
`turn=4` → `turn=current` / `waiting=0` → `waiting=1` — all things only *data* drift can produce).
The direction convention and the entry walk are covered by two scratch harnesses run against the
real classes, **19 assertions each, server and client, identical results**: all six facings on both
row parities, wrong-side refusal, a sideslip judged on the STEP rather than the heading, a jump-out
order appended, a turn plotted after arrival, sitting in the hex since last turn, a unit that has
never moved, and a relocation whose source hex is not a neighbour.

#### Stage 4 second pass, same day (user feedback)

Two symptoms, **one cause**: the vortex unit was still in
`PhaseStrategy.getInterestingStuffInPosition`, the sweep that answers "what did the pointer just
click or hover?". A vortex is unselectable terrain with nothing to target and nothing to open, but
while it sat in that sweep it shared a hex with whatever flew into it and broke both halves of that
hex's interaction:

1. **It appeared in the hex picker.** Clicking your own ship in a vortex hex returned TWO icons, so
   `onShipsClicked` opened `SelectFromShips` with the Jump Point listed as a unit to choose between —
   instead of just selecting the ship.
2. **The Jump Out button flickered and was hard to hit.** Two paths, one at each end of the zoom
   range. Zoomed in past ~0.33, `getIconsInProximity` returns only the CLOSEST icon within 10 game
   units, so the pointer flipped between the ship and the vortex stacked in the same hex; every flip
   back to the ship ran `ShipTooltip.update`, which empties `.buttons` and rebuilds the menu — the
   button was being destroyed and recreated *under the cursor*. Zoomed out, a click that missed the
   button and landed on the canvas tore the tooltip down and popped the hex picker again, because
   the hex still read as a two-icon stack.

One line fixes both — the vortex is dropped from that sweep, so the hex reads as holding exactly the
units that are really in it. Consequences, all wanted: a hex holding only a vortex now falls through
to `onHexClicked` (so hex-targeting over it still works), and the vortex is no longer clickable to
open its SCS. Hovering it never showed a tooltip anyway — `onMouseOverShip` has always suppressed
terrain tooltips. `shipManager.movement.isJumpVortex()` holds the class name so `getVortexInHex` and
this sweep cannot drift apart.

**Verified in the user's real game 4302** (Omega, vortex at (-16,-4) facing 3, ship entering
eastbound along direction 0 — `(3+3)%6 = 0` ✔): `tac_shipmovement` holds the `jumpout` row with
`value` = the vortex ship id, `tac_damage` one `HyperspaceJump` entry of 60 destroying the primary
structure, `tac_fireorder` the single log order, and `tac_individual_notes` the `jumped` note at
value 100 — full CV preserved. And **exactly one** damage entry on that ship for the turn, through a
completed Pre-Firing *and* Fire phase, which is the four `isHyperspaceLogOrder` guards proving
themselves: without them the stored ram-shaped order would have been re-resolved.

**Gate after the second pass:** `fvbuild.ps1 -Check` **all green** — autoload current, ship-data
validator PASS (no new findings), replay harness **158 passed / 0 failed**.

**Left alone, reported not folded in:** `PhaseStrategy.onMouseOverShip` calls
`this.shipTooltip.update(...)` unconditionally whenever the hovered icon set re-enters a ship the
tooltip is already for, and `update()` always rebuilds the menu. That is the general mechanism
behind this flicker and it affects any stacked units, not just vortices; guarding it (only update
when `selectedShip` actually changed) is a separate change to a shared hover path.

#### Stage 4 third pass, same day (user feedback)

**A departure should read as a departure the moment it is COMMITTED**, not when the phase resolves.
The server still removes the unit at the end of Movement — nothing about the rules moved — but
movement is sequential, so between one player's commit and the phase advancing there is a long
window in which the unit sits in the vortex as a ghost and everyone plotting after it has no way to
know it has already gone.

New `shipManager.movement.hasCommittedJumpOut()` is the whole of it. The distinction it draws is the
movement row's **id**: a locally plotted order carries `-1` until it is submitted, and the reloaded
gamedata brings back its real database id. So nothing changes while the player is still deciding —
they can still delete the order — and everything changes at once when they commit. Three readers:

| Reader | Effect |
|---|---|
| `shipManager.shouldBeHidden` | the SPRITE goes. `IdleAnimationStrategy` hides any icon that answers true, so the hex empties for every player as soon as they can see the order — which is exactly when `TacGamedata::hideActiveShipMovement` stops masking it, i.e. once that ship's initiative bracket has passed |
| `fleetListManager.updateFleetList` | the ship's own row reads **Jumped** |
| `fleetListManager.getJumpedDockedFlightIds` | and so do the rows of every flight docked in it |

⚠️ **Live play only** — `!gamedata.replay`. In replay the unit has to be seen flying INTO the
vortex before it vanishes, which the ordinary destroyed path already handles. It is also
self-correcting rather than authoritative: the server re-checks the entry rule when the phase
resolves, so an order it refuses simply brings the unit back on the next load.

⭐ **And a genuine long-standing bug underneath the second half of it.** `updateFleetList` only ever
ADDED its state class, and the fleet list rows are rebuilt only at the start of a turn
(`displayFleetLists` rebuilds when `fleetListManager.reset()` has cleared `initialized`, which only
`initPhase` does, in phase 1). So a row that changed state mid-turn kept the class it was given
earlier as well. `.destroyed`, `.jumped` and `.docked` are the same specificity and `.docked` is
written **after** `.jumped` in `tactical.css` — so a docked flight whose carrier jumped had its text
changed to "Jumped" while the cascade kept painting it **docked blue**, and it only turned orange at
the next turn's rebuild. That is what "doesn't show as Jumped until the end of the turn" actually
was. All three states now go through one `fleetListManager.setRowState(ship, state, label)` that
clears the other two.

**Three files, all client:** `movement.js`, `ships.js`, `UI/fleetList.js`. Verified by a Node
harness over the REAL `ships.js` / `movement.js` / `fleetList.js` with a fake jQuery that records
classes per row — **16 assertions**: nothing moves while the order is only plotted; sprite, ship row
and both docked flights all flip together on commit; `.docked` is actually removed; replay still
shows the unit; and last turn's order does not count. `fvbuild.ps1 -Check` all green — autoload
current, validator PASS, harness **158 passed / 0 failed**.

#### Stage 4 fourth pass — the opponent could never see a docked flight jump out

Reported on game 4302: an opponent never saw the docked flights turn orange at all. **Cause:**
`getJumpedDockedFlightIds` walks each carrier's `hangarUsage` for `dockedFlightId` links, and
`Hangar::stripForJson` masks bay contents out of an opponent's payload (composition and free-box
count are intel — the ruling is in that method). Proven on 4302: player 210 receives all three
entries, player 211 receives `[]`. So the walk works for the fleet's OWNER and for nobody else, and
the opponent's row stayed **Docked blue** for the rest of the game — for a unit that is in hyperspace.

**Fixed on the server, without unmasking the bay.** `TacGamedata::markJumpedDockedFlights` sets one
boolean on the FLIGHT — `BaseShip::jumpedWithCarrier`, emitted only when true, so every other unit's
payload is unchanged — and `updateFleetList` ORs it with the local walk. It says "this unit is in
hyperspace" and nothing about what else the bay holds, and the flight already has its own row on that
screen, so no unit is disclosed either.

It runs **after** `deleteHiddenData`, deliberately: a jump-out the viewer is not entitled to see yet
(`hideActiveShipMovement`, while that ship's initiative bracket is still moving) is already gone from
the masked movement by then, so the flag inherits that masking instead of restating it.
`hasLeftThroughVortex` covers both halves of the departure — the committed-but-unresolved order, then
`isDestroyed() && hasJumpedToHyperspace()` once Movement has resolved it.

**Verified end to end** (game 4302 was rewound before this was written, so the two post-jump states
are reconstructed in memory and pushed through the real `stripForJson`, then through the real
`ships.js` / `movement.js` / `fleetList.js` with the real `staticShips` blueprints and a fake jQuery):

| state | owner | opponent |
|---|---|---|
| committed, jumping ship still the active mover | ship + both flights **Jumped**, sprite gone | unchanged — correct, `hideActiveShipMovement` is still masking the order |
| committed, activation moved on | Jumped | **Jumped** |
| end of Movement (Firing phase) | Jumped | **Jumped** |
| next turn | Jumped | Jumped |

Replay harness 158 passed / 0 failed.

⚠️ **Known, pre-existing — FIXED IN STAGE 6.** From **two turns after** the jump the carrier's own row
read "Destroyed" (red) rather than "Jumped" (orange). `ShipSystem::stripForJson` aggregates damage
older than `currentTurn - 1` into one synthetic `Historical` entry, which dropped the
`HyperspaceJump` damageclass that `shipManager.hasJumpedNotDestroyed` keys off; the summed damage
then read as a kill. It predated Stage 4 and applied to Firing-phase boost jumps too. `HyperspaceJump`
entries are now passed through the aggregation whole. The tell was that the FLIGHTS stayed orange —
the server answers for them off the jump engine's own note, which no aggregation touches.

⭐ **Fifth pass — the jumped ship's own row could not be painted at all, for a reason older than
this feature.** `gamedata.drawIniGUI` gives every Order of Battle `<tr>` the ship's **raw id**
(`tr.id = ships[i].id`), which is the same id the fleet list row span carries — and `#iniGui` is
written before `#gameinfo` in game.php. So `$("#" + ship.id)` (jQuery's `#id` fast path is
`getElementById`) returned the **Order of Battle** row, `addClass("jumped")` coloured a row nobody
styles, and `$("#id .initiative")` matched nothing at all, an OoB row holding `.iniOrder` /
`.iniInfo` instead.

It stayed invisible because `drawIniGUI` filters out anything `isDestroyed`, and the only two states
the fleet list ever painted before were docked flights and destroyed hulls — both already out of the
OoB, so their ids really were unique. **A jumped-out ship is the first unit that changes row state
while still listed in the Order of Battle**, because the server does not remove it until the end of
the Movement phase. Its docked flights' rows changed correctly around it the whole time, which is
exactly what the report described.

`fleetListManager.fleetRow(ship)` now scopes every row lookup to `$("#gameinfo")` with an attribute
selector (which also sidesteps `#123` not being a valid CSS identifier). Ids are left alone — the
OoB's own click handler reads `this.id`. Proved both ways with a Node harness that models the
duplicate id: with the old bare lookup the ship row reads `(unchanged)` mid-Movement while its
flights read `Jumped`; with the scoped lookup all three read `Jumped`.

**Investigated and deliberately NOT adopted:** making the row change at the instant of the commit.
It works, but it needs `hasCommittedJumpOut` to accept `gamedata.waiting` as a second proof and
`goToWaiting` to repaint the list, because **a committing player is served no ship data at all** —
`Manager::submitTacGamedata` answers the POST with a bare `{}`, and `Manager::getTacGamedataJSON`
answers a waiting player with the `last_update` timestamp alone (`$gdS->changed` is never set true
anywhere, so that branch reduces to "waiting → no ships"). Fresh ships arrive only at the next
activation or the phase boundary, so the plotted order keeps `id = -1` until then. User's call:
the fleet list changing at the end of the Movement phase is fine, and not worth that machinery.
The same fact is why `shouldBeHidden` cannot drop the jumping player's OWN sprite any sooner.

### Stage 5 — lifecycle — **BUILT 2026-08-22**
Maintain mode, the closure conditions, the 4-turn cap, the all-systems-offline check, and the
end-of-turn failure roll in `JumpEngine::criticalPhaseEffects`.
**Gate:** a vortex left unmaintained closes at end of its first full turn; a maintained one survives
to the cap; a holder that strays to 5 hexes closes it; a damaged engine rolls each turn. ✔ all four.

**Twelve files — four server, eight client (two of them new). No new PHP class, so the autoload map
is untouched; `SpawnJumpPoint` gains one public property, so the local statics want regenerating
(`fvbuild.ps1 -Statics`) — but `source/public/static` is gitignored, so nothing lands in the diff.
Two new React files, so this is a `yarn build` (or `fvbuild.ps1 -Client`), not a legacy-only one.**

| File | What it got |
|---|---|
| `systems/baseSystems.php` `JumpEngine` | `MAINTAIN_MODE`, `MAX_VORTEX_TURNS`, `getMaintainDeclaration`, `getVortexPowerViolations`, the closure sweep (`closeExpiredVortices` / `closeVortexIfDue` / `getVortexClosureReason` / `recordVortexClosure`), `criticalPhaseEffects` + `rollVortexJumpFailure`, `stripForJson` (the N/4 counter), `$delay` repurposed, and `restoreVortexFromNote` rewritten as `restoreVortexState` |
| `systems/baseSystems.php` `PhasingDrive` | one line: skip the half-phase self-destruct when the parent's failure roll has just destroyed the same ship in the same Critical phase |
| `handlers/firing.php` | `getVortexDeclarationBlock` learns mode 7 (its own rule list, and it must **not** run the obstruction sweep), plus the across-turns "one vortex per ship" test |
| `Phase/FireGamePhase.php` | one call, immediately after `Criticals::setCriticals` |
| `ships/terrain/SpawnJumpPoint.php` | `$vortexHolderId` and a `stripForJson` override to emit it |
| `client/movement.js` | `getVortexHeldBy` |
| `client/power.js` | `vortexMaintainExemptNames`, `isVortexExemptSystem`, `isMaintainingVortex`, `getVortexMaintainBlockers`, `isVortexLockedOffline` — and the lock itself, one guard inside `setOnline` |
| `client/model/system/baseSystems.js` `JumpEngine` | `getHeldVortex`, `isMaintainingVortex`, `removeVortexMaintainOrder`, `canMaintainVortex`, and the activation four (`canActivate` / `canDeactivate` / `doActivate` / `doDeactivate`) |
| `client/UI/reactJs/system/JumpEngineMenu.js` (new) | the toggle, one labelled row plus a note saying what switching it on will shut down |
| `client/UI/reactJs/system/activationMenu.js` (new) | the shared panel chrome, lifted out of `PowerCapacitor.js` so the two menus cannot drift apart |
| `client/UI/reactJs/system/PowerCapacitor.js` | imports that chrome instead of declaring it (and loses one unused `Value` styled-div) |
| `client/UI/reactJs/system/SystemInfoButtons.js` | renders the menu, excludes `jumpEngine` from the generic Activate/Deactivate buttons, adds the lock to `canOnline`, and hides the whole fire-order row behind `isHoldingVortex` |
| `client/UI/reactJs/system/SystemPowerSettings.js` | the same lock on its own `canOnline` |
| `client/weaponManager.js` | the map path for maintaining is GONE; targeting your own vortex hex now points at the toggle |
| `client/renderer/icon/BallisticIconContainer.js` | the *Maintaining Jump Point* label, and mode 7 skips the facing-arrow sweep |

**What it actually took, beyond the sketch above:**

| Site | Why |
|---|---|
| ⭐ **`onIndividualNotesLoaded` had to stop being last-note-wins** | Stage 3 recorded a closure by APPENDING a phase-2 note and relying on "notes load ordered by turn then phase, last wins". That is correct for ONE vortex and silently wrong for two. A closed vortex frees its engine to open another, so an engine accumulates several vortices' notes — all stamped turn 1 — and the load order becomes `A open (p1)`, `B open (p1)`, `A closed (p2)`: the engine would end up believing its current vortex is the long-dead A, and `hasOpenVortex` would say it holds nothing. Vortex notes are now collected into a map **keyed by vortex id** (last-wins *per vortex*, which is what the phase-2 override was always meant to be) and the engine then adopts the **latest-opened** one. The unit half — `spawned`, `removed`, `vortexHolderId` — is applied for *every* vortex in the map, not just the current one, so a replay turn still renders each of them |
| ⭐ **`$gamedata->turn` is a STRING** | `TacGamedata::setTurn` stores what mysqli handed it, with no cast, while `$vortexOpenTurn` is `(int)`-cast out of the note. The failure roll's `$this->vortexOpenTurn !== $turn` was therefore **true for the same turn**, which silently skipped the roll on the very turn a vortex is opened — the commonest case there is. Caught by the scratch harness, not by review. Everything else in the stage compares turns loosely; this was the only strict one |
| The closure sweep is its own pass, **not** `criticalPhaseEffects` | "Its holder is destroyed" is a closure condition, and `Criticals::setCriticals` iterates only the ships that were **alive when it started** — so a holder killed by fire this turn, or one that flew out through its own vortex during Movement, never reaches its own systems' hooks. The sweep walks `$gamedata->ships` unfiltered instead |
| …and it runs **after** `setCriticals`, not before | The failure roll is inside `criticalPhaseEffects` (plan §2.6), and a ship it has just destroyed has to read as destroyed when the sweep asks. On the OPENING turn that gives `closeTurn == openTurn`, i.e. a vortex that never forms — which falls out of the general rule rather than needing one of its own |
| The closure REASON goes in the note, and it can contain commas | `notevalue` is now `"<openTurn>,<closeTurn>,<reason>"`, parsed back with an `explode` **limit of 3**. The reason is free text ("systems left online: Heavy Laser; Interceptor II; …") because it is the only thing a player can act on. It reaches `source/logs/fieryvoid.log` via `Debug::log` today; rendering it in the combat log is Stage 6 |
| `getVortexPowerViolations` is **public static on `JumpEngine`** | The rule is about the SHIP, not about one engine, and three callers need it (the closure test, a future Stage 6 tooltip, Phase 2's fixed gates). It exempts `Scanner` **by `instanceof`**, which catches `ElintScanner` / `SWScanner` / `AntiquatedScanner` — the client, which has no class information on a system, has to list the four names instead, and says so |
| A `powerLocked` system is deliberately **not** exempt | A deployed Kirishiac orbital's beam draws power and cannot be switched off, so such a ship simply cannot hold a vortex open until it docks. That is a real rule interaction, not an oversight |
| `PhasingDrive` needed one line | Its `criticalPhaseEffects` calls `parent::` **first**, which is now also the failure roll, and would then write a second destruction entry and a second log line on the same hull. It asks about *this turn's roll specifically* (`hasAppliedVortexFailure`) rather than about the ship being destroyed at all, so no pre-existing behaviour changes |
| The new state fields are `protected` | `json_encode` serialises public properties only, so `$vortexCloseReason` and `$vortexFailureApplied` cost the static blueprints nothing. (`$activeVortexId` and friends are public and *do* appear in every blueprint with a jump engine — Stage 3's choice, left alone) |
| Warn on the client, close on the server | §2.4 says a violation must not block the commit. The server says nothing at submit time and closes the vortex at end of turn; the client makes the violation impossible in the first place by shutting the systems down itself. Neither side blocks the commit |
| ⭐ **The toggle does the shutdown, and the lock is in `setOnline`** | Two halves that have to happen together, so they happen in one click: `JumpEngine.doActivate` makes the declaration and then powers down every non-exempt system, withdrawing any fire order it holds first (the ordinary Off button refuses a weapon that has one, for the same reason). Keeping them on is enforced in **`shipManager.power.setOnline`** rather than only in the button gates, because that is the choke point every route funnels through — the Off/On toggle, `onlineAll`, and the forced-shutdown recovery. Exactly where the Vorlon Power Capacitor's own lock sits, three lines above it. The two `canOnline` gates get it as well, so the button reads unavailable instead of warning on click |
| `doDeactivate` removes the declaration BEFORE restoring anything | Otherwise `setOnline`'s lock — which reads that very declaration — refuses every restore, and turning Maintain off would leave the ship dark |
| The restore is deliberately broad | Everything non-exempt that is offline comes back on, so a system the player had *already* powered down by hand before switching Maintain on comes back too. Same broad brush the Power Capacitor's Double Recharge uses, and for the same reason: remembering exactly which systems the toggle switched off would not survive the gamedata poll that rebuilds a ship's systems. Visible on screen, one click to undo |
| `jumpEngine` joins the generic-activation exclusion list | `SystemInfoButtons`' bare Activate/Deactivate buttons fire off any system that defines `canActivate`. With the toggle defined that way, the engine would show the same switch twice — once labelled, once not. `powerCapacitor` and `GraviticAugmenter` were already excluded for the same reason |
| The panel chrome became a shared module | `PowerCapacitor.js`'s styled-components were this menu's private styling; two menus that are meant to read as the same control had no business owning two copies. Moved verbatim to `activationMenu.js`. ⚠️ It is **not** the same chassis as `menuControls.js`'s `MENU_CHROME` — that is the drained neutral frame the lobby damage editors use, this is the older saturated `#215a7a` header the activation menus have always had. Different families on purpose |

**Gate result 2026-08-22:** `fvbuild.ps1 -Check` **all green** — autoload map current, ship-data
validator PASS, replay harness **158 passed / 0 failed**. Two scratch harnesses against the real
classes and real local game 4302, inside rolled-back transactions, **54 + 19 assertions**:
the open/reload round trip including `vortexHolderId` through `stripForJson`; maintain accepted on
its own hex and refused everywhere else, on the forming turn, and for a ship holding no vortex; a
second opening refused; all six closure reasons (unmaintained, systems online, the cap, one turn
short of the cap, range, holder destroyed, holder left through a vortex); the closing turn still
usable and `Movement::getOpenVortexInHex` agreeing; a double sweep as a no-op; a comma-bearing
reason surviving the note round trip; the two-vortex note ordering; and the failure roll at both
deterministic ends of the d100, including the `isHyperspaceLogOrder` and `HangarOps` hand-offs.

**The client half is covered by two more harnesses, both against the real files.** A `vm` sandbox
loads the actual `power.js`, `movement.js` and `model/system/baseSystems.js` (its global is a Proxy
that hands out a stub constructor for anything those files reference but do not define, so the
whole dependency graph does not have to be present) and drives the toggle against a fake Omega —
**49 assertions**: every case where the control is and is not offered (forming turn, cap turn, one
turn short of the cap, out of range, somebody else's vortex, engine powered down, wrong phase); the
order it builds; which systems go dark and which four do not; the withdrawn fire order and the
released boost; `setOnline` and `onlineAll` both refusing while it stands; the restore; and that
last turn's declaration does not count. And `JumpEngineMenu` is bundled with esbuild — which
RESOLVES imports, unlike a parse-check — and `renderToString`d in both states, **8 assertions**.

**Two deliberate gaps, reported rather than silently half-built — BOTH CLOSED IN STAGE 6:**

1. ~~**The closure reason is not in the combat log yet.**~~ **Stage 6** writes it as a
   `JumpVortex` log order in `recordVortexClosure`, so the turn's combat log reads *"<ship> loses
   its jump point at the end of this turn — systems left online: Heavy Laser; …"*.
2. ~~**No commit-time warning for maintaining.**~~ **Stage 6** added *"The JUMP POINTS held by the
   following ships will CLOSE at the end of this turn"* to the Initial-Orders commit dialogue. It
   is asked of the VORTEX rather than of the toggle, so it also covers the cases where maintaining
   was never on offer — the holder is out of range, or the four-turn cap has been reached — which
   are exactly the ones a player had no other way of seeing coming.

### Stage 6 — polish — **BUILT 2026-08-22**
The plan's four polish items (replay effect, combat-log lines, `faq.php`, the Jump Engine tooltip)
plus an eleven-point list from the user. **Twenty files — eight server, twelve client, one doc — and
one new scratch harness. No new PHP class, so the autoload map is untouched; `JumpEngine`'s
`$loadingtime` / `$turnsloaded` / `$delay` are public, so the STATICS want regenerating, and two
React files changed, so this is a full `fvbuild.ps1` (autoload + statics + yarn build).**

| # | What the user asked for | Where it landed |
|---|---|---|
| 1 | Warn when a jump point is about to close unmaintained | `gamedata.js` Initial-Orders commit checklist |
| 2 | A jumped ship must stay "Jumped", not turn "Destroyed" two turns later | `ShipSystem::stripForJson` |
| 3 | A yellow *Jumping to Hyperspace* marker while the departure is only planned | `ships.js` + `ShipTooltip.js` + `ShipWindow.js` |
| 4 | The Jump Drive's map arc is yellow, not cobalt | `ShipIcon.js` |
| 5 | …and stays on screen while the system is SELECTED — also for Transverse Drive and Warp Drive | `PhaseStrategy.js` + `systems.js` |
| 6 | A jump point closes if its holder leaves the battle | **already true** — Stage 5's closure sweep; now covered by tests |
| 7 | The jump point's zoomed-out overlay is yellow, not terrain white | `gamedata.js` `getShipOverlayColor` |
| 8 | `$turnsloaded` means LOADING again; the vortex counter moves to its own field; `$delay` is a real recharge | `JumpEngine` ctor + `getVortexRechargeLoad` / `getVortexAge` / `stripForJson`, `firing.php`, `SystemIcon.js`, client `JumpEngine` |
| 9 | No red *Fire / Don't Fire* box on an engine holding a jump point | `SystemInfoButtons.canSystemActivation` |
| 10 | Fighter flights can use a jump point | `movement.php`, `FighterFlight.php`, `fighter.php`, `movement.js`, `ships.js` |
| 11 | Rewrite the FAQ's *Jump Drives* section | `faq.php` |
| — | Combat-log lines for a jump point opening and closing | `JumpEngine::writeVortexLogOrder`, `firing.php`, `weaponManager.js` |
| — | Replay: the jump point forming and collapsing | `ReplayAnimationStrategy.animateVortexLifecycle` |
| — | The Jump Engine's own tooltip | `JumpEngine::setSystemDataWindow` |

**What it actually took, beyond the sketch above:**

| Site | Why |
|---|---|
| ⭐ **The recharge is DERIVED, not stored** | Item 8 needs four states — full at scenario start, 0 the turn a jump point opens, flat while it stands, +1 per turn from the turn AFTER it closes. The ordinary Weapon loading machinery *almost* does it (a ballistic order zeroes the count at the phase-2 advance, phase -1 adds one per turn) and is out by a turn in every case where the vortex closes without a Maintain declaration on its last turn — unmaintained, out of range, the four-turn cap — because the recharge starts from the turn after the CLOSURE, not from the turn after the last order. `getVortexRechargeLoad` reads the two turns the vortex note already carries, which is both shorter and exact |
| `$loadingtime` is now the ship file's 4th argument | The B5W jump delay, 0–65 across the fleet, 16 on a Primus. It rides the STATIC blueprint like every other weapon's loading time — and `stripForJson` sends it as well, because `Weapon::stripForJson` does not, so a browser holding a pre-Stage-6 blueprint would otherwise read 1. One ship (the Drazi Jumphawk) passes 0, hence `max(1, …)` |
| ⭐ **It re-earns the one-vortex rule for free** | `weaponManager.isLoaded` is `loadingtime <= turnsloaded`, so an engine reads NOT LOADED while its jump point stands (charge 0) and until it is fully recharged. That is what keeps it out of `targetHex`'s weapon sweep — the same job Stage 5's N/4 counter was doing by accident, now done by the number that actually means it. `Firing::getVortexDeclarationBlock` gained the matching server-side refusal |
| The N/4 counter needed the icon branch it never had | Moving it out of `turnsloaded` meant `SystemIcon.getText` had to ask for it — and that exposed an existing bug: a MAINTAINING engine holds a mode-7 order all turn, so `getText`'s "has a firing order" branch caught it, found a weapon that cannot change shots and was not a called shot, and returned **undefined**. The icon was drawing blank. The vortex counter is checked before that branch |
| ⭐ **Item 2 was one `continue` in the damage aggregator** | `ShipSystem::stripForJson` folds damage older than `currentTurn - 1` into one synthetic `Historical` entry, summing it and dropping the damageclass. Both jumped-out tests (client `hasJumpedNotDestroyed`, server `hasHyperspaceJumpDamage`) work by SUBTRACTING the `HyperspaceJump` entry from the total and asking whether the rest would have killed the unit — so once it is folded in there is nothing left to subtract and the sum reads as a kill. `HyperspaceJump` entries now pass through whole. The tell was that docked FLIGHTS stayed orange: the server answers for them off the jump engine's own note, which no aggregation touches |
| ⭐ **Item 10 moved all three of `applyJumpOut`'s records one level down** | A flight has no primary Structure (so what takes it off the board is every CRAFT destroyed — one `HyperspaceJump` entry each, which is also what makes the test answerable) and no jump engine (so the CV note goes on the SAMPLE fighter). `Fighter` gained the third `preJumpValue`/`getCVBeforeJump` pair after `JumpEngine` and `Structure`; `FighterFlight` gained `hasJumpedToHyperspace`, `getCVBeforeJump` **and a jumped branch in `calculateCombatValue`** — it overrides the whole method, so `BaseShip`'s branch never applied to it and a jumped flight would have been scored as a kill |
| Breaching pods now ride their host out too | The attached sweep in `resolveJumpOuts` skipped `FighterFlight` for the same "no primary Structure" reason. With flights removable it does not have to |
| ⭐ **Item 9 was a THIRD gate nobody had found** | `SystemInfoButtons` carries `canActivate`/`canDeactivate` helpers that already excluded `jumpEngine` — but the `<SystemActivation>` component is gated on `canSystemActivation`, a *different* function that asks the system directly. So the engine's Maintain pair drew a second, unlabelled copy of the same switch, in the RED weapon chrome (a jump engine is a `Weapon` subclass). One line, in the gate that was missed |
| Item 5 turned three arc-clearing sites into one `refreshSystemArcs` | Arcs were a pure hover display: three call sites each cleared every icon's arcs and `onSystemMouseOver` drew the one under the pointer. Keeping a SELECTED system's arc up needs the hovered system to be remembered rather than passed, because the sweep also has to run from `onSystemDataChanged` — selecting and unselecting both land there — and must not tear down an arc the pointer is still sitting on. `shipManager.systems.ARC_VISIBLE_WHEN_SELECTED` is the short list, and it is short on purpose: every gun's arc left standing would paint the map solid on a selected broadside |
| Item 7 is on the ZOOMED-OUT overlay, and the direction is counter-intuitive | `ShipIconContainer.applyZoomToIcon` fades the overlay colour in at `zoom > 2`, and a LARGER `zoom` is a wider orthographic frustum — i.e. **zoomed out**. So `getShipOverlayColor` is exactly the right hook, and terrain's off-white is what a jump point was collapsing to at map scale |
| The combat-log lines are RammingAttack orders, not Jump Engine ones | The log is fire-order driven, so a non-shot event needs an order to hang a sentence on — the idiom `doHyperspaceJump`, `applyJumpOut` and the half-phase self-destruct all use. It has to sit on **RammingAttack**: an order on the engine's own `fireOrders` would be indistinguishable from a DECLARATION on the next load, because `getVortexDeclaration` matches on turn plus firing mode and mode 1 is a real facing. `damageclass 'JumpVortex'` then does two jobs — `Firing::isHyperspaceLogOrder` matches it so the four fire-order gathers skip it (the Stage 4 trap), and `weaponManager.doShortLogText` lists it so the log prints the sentence alone |
| …and the OPENING one needed its own submit | `InitialOrdersGamePhase::advance` has no `submitFireorders` at all, so `spawnDeclaredVortices` persists its own, narrowed to the `JumpVortex` orders it just wrote rather than to everything `getNewFireOrders` returns. The CLOSING one rides the submit `FireGamePhase::advance` already does after the closure sweep |
| The replay effect is off by one at both ends, deliberately | `spawned == openTurn + 1` and `removedTurn == closeTurn + 1` are each the FIRST turn their state is true, not the turn the event happened — so the forming animation belongs to turn `spawned - 1` and the closing one to turn `removedTurn - 1`. A vortex that never formed (a Stage 5 failure roll on the declaring turn) is born and removed on the same turn and is skipped outright |
| The Jump Engine dropped out of the "no ballistic launch declared" nag | It is a ballistic hex-target weapon, so every jump-capable hull in the game was carrying that line every turn its drive was charged. Pre-existing since Stage 2; its own warning is the jump-point one |

**Gate result 2026-08-22:** `fvbuild.ps1 -Check` **all green** — autoload map current, ship-data
validator PASS, replay harness **160 passed / 0 failed**. The harness baseline was re-recorded
first: every one of the 148 pre-record differences was `turnsloaded` or `loadingtime` on a jump
engine and nothing else — no movement, tohit, damage or masking drift anywhere in the corpus,
which is the whole of item 8 showing up and nothing else showing up with it.

Two scratch harnesses, both against the real classes:
- **`tests/replay/stage6harness.php`, 68 assertions** (in-memory, no DB): the recharge curve across
  a whole vortex lifecycle including a string turn and the delay-0 hull, `PhasingDrive` inheriting
  it, both `stripForJson` shapes off a real Primus, the preserved `HyperspaceJump` entry and the
  verdict the client reaches from it, all seven closure reasons including *holder left through a
  vortex*, a real Kotha flight through `Movement::applyJumpOut` (every craft, the CV note on the
  sample fighter, the read-back, and a shot-down flight that must NOT read as jumped), and the log
  order's damageclass, its `addToDB`, its invisibility to `getVortexDeclaration` and its being
  skipped by `isHyperspaceLogOrder`.
- **A `vm`-sandbox client harness, 26 assertions**, over the real `ships.js` / `systems.js` /
  `movement.js` / `model/system/baseSystems.js`: the icon counter in all four states plus the
  `spawned`-derived fallback, `isLoaded` agreeing at each, the marker's five accept/reject cases,
  `hasJumpedNotDestroyed` for a hull and a flight (and the aggregated payload that used to read
  Destroyed), and the arc-when-selected list.

**One thing NOT built, reported rather than half-done:** nothing renders the vortex's closure
REASON on the map or in a tooltip — it reaches the player as a combat-log line at the end of the
turn it happens, and as the pre-commit warning that is item 1. That is the whole of it.

---

## 5. Traps

1. **`advance()` has already set the next phase.** Inside `InitialOrdersGamePhase::advance`,
   `$gamedata->phase` reads **2**. Pass an explicit checkpoint name; never branch on phase.
2. **POST-side ships have no enhancements and no loaded notes.** Anything that reads
   `$this->activeVortexId` must live in `advance()` off a real `getTacGamedata` load, never in
   `generateIndividualNotes`.
3. **`notekey_human` is `varchar(40)`.** Overflow aborts the whole player submission with a
   mysqli 1406, not a truncation.
4. **Client system objects share fields across same-phpclass instances.** Vortex state must be
   per-instance on the client, or two ships with Jump Engines will show each other's vortex.
5. **`insertSingleShip` returns a string id from mysqli** — it already casts, so route every spawn
   through it rather than calling `submitShip` directly.
6. **Terrain is invisible to a lot of code.** `MovementGamePhase` skips `isTerrain()` units in three
   places, `setNextActiveShip` skips them, mine detection skips them. That is all correct for a
   vortex — but check the **fleet list** and any points/CV totals, which may not filter terrain.
7. **Attached units — RESOLVED 2026-08-21 (Stage 4).** Pod/grapple movement rows are all type
   `attached` and mirror the host, so an attached pod is invisible to any "entered a new hex" test.
   It therefore cannot jump independently for free — the entry test refuses it — and
   `Movement::resolveJumpOuts` walks `hasAttached` after removing a host so the pod goes with it.
8. **The replay harness does not cover masking or damage resolution.** The "enemy can't see my
   declared vortex hex during Initial Orders" rule and the failure roll are both unprotected by
   regression tests — hand-check from both seats.
9. **Hangar interlock ordering.** Moving the removal from `Firing::fireWeapons` to
   `MovementGamePhase::advance` means the jump damage entry now lands *two phases earlier*.
   `HangarOps::processJumpingCarrierDockOrders` still finds it (it only asks "is there jump-class
   damage this turn"), but re-read that comment block before changing the timing, and confirm a
   fighter that ordered a dock in Initial Orders onto a carrier that jumps at end of Movement still
   ends up in the hangar.
10. **`$gamedata->turn` IS A STRING — never compare it strictly. RESOLVED 2026-08-22 (Stage 5).**
    `TacGamedata::setTurn` stores mysqli's value with no cast, while every turn parsed out of an
    IndividualNote is `(int)`-cast. `$note-derived === $gamedata->turn` is therefore **false for the
    same turn**. It silently disabled the Stage 5 jump-failure roll on the opening turn until a
    scratch harness caught it. Compare loosely, or cast both sides.
11. **In-flight games — RESOLVED 2026-08-21.** Retire the boost path with `$boostable = false` and
    nothing else; the whole boost framework stays live for one deploy cycle so a boost already
    committed on the live server still resolves. Full reasoning and the four facts it rests on are
    in §4 Stage 2; the consequence for Stage 4 is recorded there. Do not "tidy up" the boost code
    in the same deploy that removes the flag - that is the cleanup deploy, one cycle later.
    **⚠️ SUPERSEDED 2026-08-22 (§9): the cleanup deploy will never happen.** `$boostable` is no
    longer a transition flag being wound down - `markLegacy()` sets it true permanently on 195
    hulls, and the whole boost framework is now supported behaviour. Never delete it.

---

## 6. Test plan

Local Docker, a **fresh game per scenario** (not `safeGameID`), verified against `tac_*` exports.

| # | Scenario | Expect |
|---|---|---|
| 1 | Declare vortex 4 hexes away | `tac_fireorder` row, x/y/firingmode correct; opponent's phase-1 payload has no such row |
| 2 | Declare 5 hexes away | Refused client-side; refused server-side if forced |
| 3 | Declare onto an asteroid / another vortex / an Enormous unit | Refused |
| 3a | Target any hex, click OK immediately | Control opened on facing 0; row lands with `firingmode = 1` |
| 3b | Step the facing all the way round, then OK | Arrow AND the button ring track each step; `firingmode` matches the submitted row; weapon deselects |
| 3c | Target a hex, then click away without OK | **No** `tac_fireorder` row; preview marker gone; weapon still selected and re-targetable |
| 3d | Two ships declare vortices in the same Initial Orders | Two correct rows; the second transaction does not disturb the first |
| 3e | Zoom and scroll mid-transaction | Control stays anchored to its hex; the button ring re-lays out on zoom so OK stays just outside the hex |
| 3f | Declare, then remove the firing order and declare again | Transaction re-runs cleanly; exactly one row survives |
| 3g | Target a hex, then deselect the Jump Engine in the weapon list | Control and preview marker gone; **no** `tac_fireorder` row |
| 4 | Turn N Movement | Vortex visible to both players; nobody can enter |
| 5 | Turn N+1, enter from the mouth side | Jump Out offered; movement ends; ship removed before Pre-Firing; CV preserved |
| 6 | Turn N+1, enter from any other side | No Jump Out button |
| 7 | Sideslip into the hex along the mouth axis | Offered (this is the case that distinguishes the entry-step rule from the heading rule) |
| 8 | Enemy ship enters my vortex | Allowed; escapes with CV preserved |
| 9 | Don't maintain | Closes at end of N+1; `removedTurn` set; replay still shows it |
| 10 | Maintain with a weapon left online | Closes at end of turn, log note explains why |
| 11 | Maintain correctly ×3 | Survives to end of N+3, closes at the cap |
| 12 | Holder moves to 5 hexes | Closes at end of that turn |
| 13 | Holder destroyed by fire | Closes at end of that turn |
| 14 | Holder enters its own vortex | Vortex collapses end of turn; a second ship entering the same turn still gets through |
| 15 | Damaged engine opens a vortex | Roll at end of turn; on failure `JumpFailure` damage, no vortex, docked craft die with no escape roll |
| 16 | Carrier with docked fighters jumps | Fighters go with it; CV preserved for both |
| 17 | Replay the whole game | Vortex appears, persists, closes at the right turns |

Plus `scripts/fvbuild.ps1 -Check` (map staleness + replay harness) green at the end of every stage.

---

## 7. Phase 2 — Fixed Jump Gates — **PLANNED 2026-08-23, see [JUMP_GATES_PLAN.md](JUMP_GATES_PLAN.md)**

⭐ **This section is now HISTORY.** Phase 2 has a plan of its own; that file supersedes everything
below and carries the user rulings of 2026-08-23 (no owner priority; the vortex facing is always the
gate's own and cannot be changed; no Maintain, no power shutdown and no signaller-range recheck for
a gate vortex; the jump-failure roll is KEPT). Two things below are wrong and are corrected there:

- **The "one structural blocker" is not the one named.** `isMyShip` needs no exception at all —
  clicking a terrain gate in Initial Orders already builds a `ShipTooltipInitialOrdersMenu`, and
  right-clicking one already opens its ship window. The real blocker is the **submit path**, on
  both sides: `ajaxInterface.construcGamedata` only pushes a ship into the POST when
  `ship.userid === gamedata.thisplayer`, and `InitialOrdersGamePhase::process` drops fire orders
  for any POST ship where `$ship->userid != $gameData->forPlayer`.
- **"Owner first"** was replaced by a flat nearest-then-roll-off.

Recorded here so the Phase 1 design does not paint it into a corner:

- Any unit within **10 hexes** may signal a gate in the Jump Point Formation segment; the vortex
  forms in the gate's own hex with the gate's own facing, and cannot be projected.
- Programmable open duration, max 4 turns; **20-turn** recharge afterwards.
- Reactor damage: every 3 boxes destroyed adds 1 turn to the recharge; every 15 points of damage
  removes 1 turn from the maximum hold; total reactor loss destroys the gate. No other criticals.
- Ownership / contested-claim resolution (owner first, then nearest, then roll off — **not**
  initiative).

~~**The one structural blocker to solve first:** `isMyShip` / `isMyorMyTeamShip` return false for
terrain outside Deployment ([gamedata.js:212](source/public/client/gamedata.js#L212)), so a neutral
gate is unclickable. Phase 2 needs a deliberate, narrow exception — not a general loosening of the
terrain gate.~~ **Wrong — see the note at the head of this section, and JUMP_GATES_PLAN.md §3.1.**

Phase 1's vortex unit is designed to be exactly what a gate opens, so nothing here is wasted.

---

## 8. Follow-up — shrinking the static blueprints (separate task, not this plan)

Stage 1 grew `source/public/static/json/**` because a jump-engine entry now serialises as a weapon:
**536 B → 2,899 B** each (an average real weapon is 3,026 B, so it is now simply normal-sized),
776 entries, **+1.8 MB on a 96.5 MB tree ≈ +2%**. That is acceptable on its own; it is written here
because measuring it turned up a much larger, pre-existing number.

**Measured 2026-08-21, across all 93 faction files and 56,610 system objects:**

| | bytes | share of tree |
|---|---|---|
| `false` booleans | 22.01 MB | 22.8% |
| empty arrays / objects | 14.38 MB | 14.9% |
| `null` values | 5.72 MB | 5.9% |
| `0` integers | 5.06 MB | 5.2% |
| empty strings | 0.03 MB | — |
| **total spent on default values** | **47.19 MB** | **48.9%** |

**Frame it correctly before spending effort on it.** gzip -6 takes the whole tree from 96.5 MB to
**6.90 MB (7.1%)**, and these defaults are the most repetitive bytes in the file, so the *wire* cost
of Stage 1's growth is on the order of +130 KB gzipped, not +1.8 MB. Live already serves the
per-faction JSON pre-compressed through `gamelobbyloader.php`. The real costs are **disk**,
**generation time on every deploy** (a web request against the lsphp memory hard limit — see
`arch_static_generator_streaming`), and **client JSON parse time in the lobby**.

Both generators go through `ShipCompactor` ([lib/ShipCompactor.php](source/server/lib/ShipCompactor.php)),
which already strips ~35 named keys and documents *why* each is safe. Everything below is an
extension of that one file, ranked by return over risk. **None of it is jump-point work** — do it
separately, and verify byte-identity of the client's behaviour, not of the file.

**A. Keys the client never reads at all (~4.8 MB).** The `$serverOnlyShipKeys` idea, applied at
system level. Grepped the client tree (excluding bundles) — zero hits for:
`overkillArcStructures` (1.59 MB), `addedByEnhancement` (1.48 MB), `hitChartName` (1.09 MB),
`revealAfterPreFire` (0.61 MB). Lowest risk of anything here: a key nothing reads cannot break
anything. Re-run the grep before acting; it is the whole test.

**B. Generic `false`-boolean strip (up to 22 MB).** Absent reads as `undefined`, which is falsy, so
every `if (sys.foo)` gate behaves identically. The audit is a grep for `=== false`, `!== false` and
`!= false` against system properties in the client — those are the only places where absent and
`false` differ. Safer as an allow-list grown from the measured top keys than as a blanket rule.

**C. Generic empty-array/object strip (up to 14.4 MB).** Same shape as the 13 array keys
`ShipCompactor` already strips, and the same audit: an absent array is fine where the code guards
with `mathlib.arrayIsEmpty` or `for…in`, and a TypeError where it calls `.length` or indexes
directly.

**D. `null` (5.7 MB) and `0` (5.1 MB) — allow-list only, never blanket.** Several zeros are load
bearing: `powerReq`, `armour`, `output` and `location` all mean 0, and `undefined` would produce
`NaN` or a mis-rendered section. The worked example to keep in mind is `boostEfficiency`
(1.15 MB of zeros, and tempting): `countBoostPowerUsed` calls
`system.boostEfficiency.toString()` — stripping it throws on every boostable system. That single
case is the argument for auditing each key rather than writing a rule.

**Not worth doing:** anything jump-engine-specific. Stage 2 makes it a genuine hex-targeting
weapon that needs its targeting surface, so the only durable fix is the generic one above.
---

## 9. The legacy one-click jump — the opt-out for custom factions — **BUILT 2026-08-22**

**User request 2026-08-21:** some custom factions play better with the old one-click escape, so they
need a way to keep `$boostable = true` and `$autoFireOnly = true` on chosen hulls. Deliberately
scheduled **after Stage 6** — it is a variant of the finished thing, and building it early would
have meant maintaining two engines through four more stages.

**⭐ BUILT AS A FLAG, NOT AS THE `LegacyJumpEngine extends JumpEngine` SUBCLASS THIS SECTION
ORIGINALLY SKETCHED.** `JumpEngine` carries a `protected $legacyJump`, set by `markLegacy()` and
read by `isLegacyJump()`. Both designs work; the flag is cheaper and safer in four specific ways:

- no new class means **no autoload regeneration**, which on live needs the maintenance gate;
- `phpclass` in every reverted blueprint stays `"JumpEngine"`, so a game in progress across the
  deploy sees no class identity change at all;
- every `instanceof JumpEngine` site in the codebase keeps its current answer with no audit, and no
  `get_class() ===` comparison anywhere can silently stop matching;
- it is **per-instance**, so one hull in a faction can be reverted without a class of its own.

The cost is that there is no `instanceof LegacyJumpEngine` to test — ask `isLegacyJump()`.

### The shape

```php
$hyperdrive = new JumpEngine(4, 12, 6, 20);
$hyperdrive->displayName = 'Hyperdrive';
$hyperdrive->markLegacy();          //returns $this, so the one-liner form works too
$this->addPrimarySystem($hyperdrive);
```

[`markLegacy()`](source/server/model/systems/baseSystems.php#L5287) flips exactly two things, in
opposite directions, and then repairs the recharge display:

1. **The boost path, back on** — `$boostable = true`, `$maxBoostLevel = 1`, `$boostEfficiency = 0`.
   `$boostable` is the whole of it, in the same way `$boostable = false` was the whole of retiring
   it at Stage 2: the only thing it drives is `showBoost` in `SystemPowerSettings.js`, a truthiness
   test, so setting it true restores the "Jump to Hyperspace: Yes/No" row and nothing else.
2. **The vortex declaration path, off** — `$autoFireOnly = true`, `$ballistic = false`,
   `$hextarget = false`, `$range = 0`, `$firingModes = array(1 => "Standard")`. The four per-mode
   arrays are pruned to mode 1 as well: `Weapon::__construct` has already filled seven entries in
   each by the time `markLegacy()` runs, and shrinking `$firingModes` alone would leave six dead
   entries per array in every reverted blueprint.
3. **`$loadingtime` / `$turnsloaded` forced to 1/1.** Stage 6 gave `$loadingtime` the B5W jump
   delay so the icon could count a charge the engine spends opening a vortex; a legacy engine never
   opens one, so the count would sit at N/N forever and read as a broken timer. 1/1 is what
   `SystemIcon` draws as nothing at all.

`setSystemDataWindow` takes an early branch for a legacy engine and describes the **boost** rules,
calling `ShipSystem::setSystemDataWindow` rather than `Weapon`'s — no Damage / Fire control /
Priority / Range rows, because this engine targets nothing.

**⭐ PROTECTED ON PURPOSE, so it is not serialised at all** — `json_encode` takes public properties
only, and a public default would cost 776 blueprint entries for nothing (§8). The client does not
need it: every switch it would drive is already carried by a property `markLegacy()` sets.
Confirmed in the generated output — a reverted hull reads
`boostable:true, autoFireOnly:true, hextarget:false, range:0, firingModes:{1:"Standard"}, 1/1`,
with **no `legacyJump` key**.

**⚠️ WHAT IT DOES NOT TOUCH: the vortex LIFECYCLE.** `restoreVortexState`, `closeExpiredVortices`,
`recordVortexClosure` and the jump-failure roll all still run on a legacy engine, so a vortex opened
**before** a hull was reverted still closes and still logs properly. Only the DECLARATION path is
shut off, which is the half that can create new state.

### What was reverted

| Family | Hulls | How |
|---|---|---|
| Star Wars Hyperdrive | 31 | `markLegacy()` per ship file |
| BSG FTL Drive (Colonials / Kobol / Cylons) | 31 | `markLegacy()` per ship file |
| `customs/VariableHangarSize` | 1 | `markLegacy()` per ship file |
| Trek Nacelle | ~60 | **class-level**, in `TrekWarpDrive::__construct` |

63 ship files carry the call, and that set is **exactly** the set of files naming a
Hyperdrive/FTL Drive/Nacelle — verified both directions, nothing missed and nothing over-reverted.
Trek is marked on the class because every instance of `TrekWarpDrive` is a nacelle.

### The four traps — all resolved

1. **⭐ `$name` stays `"jumpEngine"`. HOLDS.** `SystemFactory.createSystemFromJson` picks the client
   class from `systemJson.name`, **not** from phpclass — `new window[name](args, ship)`. Keeping the
   name means the existing client `JumpEngine` class is reused with **no new JS at all**, and its
   `initializationUpdate` (the `JUMP` output display) and `hasMaxBoost` are exactly what the boost
   path needs. It is also how `movement.js canHalfPhase` finds the drive, and how three client
   `instanceof Weapon` guards exclude it (§3.1). The flag design touches none of it. Confirmed in
   the regenerated blueprints: `"name":"jumpEngine"` on every reverted hull.
2. **⭐ The end-of-Fire sweep found engines by `displayName` and missed 195. FIXED 2026-08-22.**
   [firing.php:1699](source/server/handlers/firing.php#L1699) used
   `$ship->getSystemsByName('Jump Engine')` plus a Shadow-Association special case for
   `'Phasing Drive'`, and `getSystemsByNameLoc` matches `displayName` (or `hitChartName`). Measured
   across all 776 jump engines in the static tree:

   | displayName | count | found by the old sweep? |
   |---|---|---|
   | Jump Engine | 558 | yes |
   | Nacelle | 132 | **no** |
   | Hyperdrive | 50 | **no** |
   | Phasing Drive | 23 | yes (special-cased) |
   | FTL Drive | 13 | **no** |

   All three missing families carry `hitChartName: null`, so nothing rescued them. **Boost-to-jump
   had never worked on a Trek Nacelle, a Hyperdrive or a BSG FTL Drive** — a pre-existing bug, not
   one this plan introduced — and those are precisely the families this section puts back on the
   boost path, so the two had to be fixed together. The sweep now selects on `instanceof JumpEngine`
   and skips `isDestroyed()`, which also retires the Shadow special case. **Any future "find the
   jump engines" sweep should test `instanceof`, never a name.**

   ⚠️ It was deliberately **not** narrowed to `isLegacyJump()`: a boost committed on a NON-legacy
   engine before the Stage 2 deploy must still resolve. `isOverloading()` is the real gate, and a
   non-legacy engine can no longer be given a new boost, so it never fires for one by accident.
3. **This makes the boost framework PERMANENT. THE CLEANUP DEPLOY IS NOW VOID.** §4 Stage 2 and §5
   trap 11 promised a "cleanup deploy, one cycle later" deleting `isOverloading()`,
   `doHyperspaceJump()`, the end-of-Fire sweep, the `isJumpEngine` branch in
   `SystemPowerSettings.js` and the boost-driven `jumping[]` checklist in `gamedata.js`. **None of
   those five may be deleted** — 195 hulls now depend on the boost path, and it is supported
   behaviour. The two plans were always mutually exclusive; this section is the one that won.
4. **Stages 3–5 needed no guard. HOLDS, and it went further.** A legacy engine is `autoFireOnly`
   with no `hextarget`, so it can never produce a declaration. Belt and braces were added anyway:
   `getVortexDeclaration` and `getMaintainDeclaration` return `null` outright for a legacy engine,
   `Firing::getVortexDeclarationBlock` refuses one with a reason string, and — the one that matters
   — `hasVortexDeclaration` **skips legacy engines entirely**. That last is the **cloak guarantee**:
   it is what `CloakingDevice` / `ShadingField` ask before revealing their ship, and a legacy hull
   jumps by boost (a `tac_power` type-2 row, no fire order), so a cloaked Trek hull boosting its
   Nacelle keeps its cloak by construction rather than by coincidence. *(User ruling 2026-08-22:
   boosting to jump must NOT break concealment.)*

### Two incidental fixes that rode along

- **`TrekWarpDrive`'s 4th constructor argument is an IMPULSE RATING, not a jump delay** (it feeds
  `TrekImpulseDrive`). Stage 6's constructor read it as a delay, so a Nacelle rated 6 was claiming a
  6-turn jump recharge it never had. `markLegacy()` resets it to 1/1; `$delay` keeps the raw value
  and nothing reads it on a legacy engine.
- **A jump engine is not a gun.** Since Stage 1 made `JumpEngine` a `Weapon` subclass it passed
  `sysEnhEligibleGSGT`'s `instanceof Weapon` gate with `fireControl [0,0,0]`, so every jump-capable
  hull in the game was being offered a fire-control refit for a system that never rolls to hit.
  [Enhancements.php:3586](source/server/model/ships/Enhancements.php#L3586) now denies it by
  `instanceof JumpEngine` — not by a `$gunsightExcluded` class-name entry, because each subclass
  would need its own line and a missed one puts the bogus offer straight back.

### Side effect worth stating plainly

The Stage 2 declaration path is class-based and name-agnostic, so **all 776 jump engines could open
a vortex**, including the 195 that could never boost-jump. Reverting one of those hulls therefore
*removed* a capability Stage 2 had just given it and restored one that had never functioned — which
is exactly why trap 2 had to be fixed in the same change.

### Build steps and verification (2026-08-22)

- **`-Autoload`: NOT needed** — the flag design adds no class. (The subclass sketch would have
  required it; this is one of the four reasons the flag won.)
- **`-Statics`: run**, after the last source edit. Every reverted hull's blueprint changes shape.
  Swapping to the flag does **not** shift system ids — ids are construction-order and the position
  is unchanged.
- **PHP lint**: 64 modified ship files + `baseSystems.php`, `firing.php`, `fighter.php`,
  `customTrek.php`, `Enhancements.php` — all clean.
- **`fvbuild.ps1 -Check`**: autoload up to date · ship-data validator 234 findings, all in baseline,
  **0 new** · replay harness **161 passed, 0 failed** (33.4s).
- **Control check**: an Earth Alliance engine still reads all 7 vortex modes, range 4, 24-turn
  charge. The vortex path is untouched by any of this.
