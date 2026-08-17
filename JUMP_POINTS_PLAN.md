# Jump Points — B5W Vortex Mechanics

Replaces the current one-click "boost the Jump Engine, vanish at end of turn" escape with the
tabletop rules: a ship **projects a vortex** into a nearby hex, the vortex **forms**, **persists**,
can be **maintained**, and any unit that **flies into its mouth** leaves the battle.

Status: **PLAN ONLY — nothing built.**

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

### 2.2 The facing rule — write this down once and never re-derive it

> **The facing direction is the doorway into hyperspace.** To use the jump point, a unit must enter
> the hex through the hex side the vortex is facing.

Restated as the formula the code needs — the vortex's facing `F` names its **mouth**, and a unit
enters by crossing that side inbound, so it is **travelling in direction `(F + 3) % 6`**:

```
required travel direction  D = (F + 3) % 6
equivalently, entry side   S = (D + 3) % 6  must equal F
```

Worked example: vortex at `(10,10)` with `F = 0`. Direction 0's neighbour is `(10,9)`. A ship
sitting at `(10,9)` and moving to `(10,10)` travelled in direction 3 — and `3 == (0+3)%6`. ✔
A ship arriving at `(10,10)` from `(10,11)` travelled in direction 0 and is **refused**.

Because the rule is on the **actual movement step**, a sideslip into the hex is judged on the
slipped direction, not on heading — which is the whole point of choosing this reading.

**Ships already in the hex** when the vortex activates: judged on the last step that carried them
into the hex, walking back through prior turns if necessary. A unit with no such step at all (a
base, an OSAT, a unit that has never moved) **cannot** use the vortex.

### 2.3 Forming, open, closing

| Turn | State | Can units enter? |
|---|---|---|
| N — declared in Initial Orders | **Forming**. Unit spawns at the end of `InitialOrdersGamePhase::advance`, visible to everyone from Movement onward | No |
| End of N | **Activation.** Damaged-engine failure roll happens here | — |
| N+1 | **Open** | Yes |
| N+2, N+3 | Open **only if maintained each turn** | Yes |
| End of N+3 | **Hard cap.** Closes unconditionally (4 turns counting N) | — |

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

### 2.5 Jumping out

- During **Movement**, once a unit's plotted path enters an **open** vortex hex from the correct
  direction, a **Jump Out** button appears in a new movement-phase tooltip menu.
- Selecting it **ends that unit's movement immediately** — remaining hexes are forfeit.
- At the end of Movement the unit is removed from the game exactly as `HyperspaceJump` does today:
  primary structure destroyed with damageclass `HyperspaceJump`, `jumped` note written first so CV
  is preserved.
- Any unit may use any open vortex, including an enemy's. This is RAW and is deliberate.
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

**Default facing:** `(declaring ship's heading + 3) % 6`, i.e. the mouth faces back toward the ship
that opened it, so a ship that projects a vortex straight ahead can fly straight into it. This is the
value the facing control opens on (§3.5), so most declarations are OK-and-done with no adjustment.

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

New `source/server/model/ships/terrain/jumpPointSpawn.php`:

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
      → preview marker drawn on the hex, arrow at the default facing
      → turn left / turn right step the facing, arrow redraws
          → OK  ......... creates the FireOrder and closes the control
          → click away .. discards; no order, nothing to clean up
```

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

Anchoring note: `positionMovementUI` reads `this.movementUI.icon.getPosition()`. The pending vortex
marker needs to expose `getPosition()` so the same anchoring path works unchanged; do that rather
than special-casing the position lookup.

---

## 4. Build stages

Each stage is independently testable. Do not start the next until the previous is verified in a
fresh local game.

### Stage 1 — `JumpEngine extends Weapon` (no new behaviour)
Convert server + client classes, keep the boost path working exactly as today. Fix the combat-value
classifier. Regenerate autoload + statics + bundles.
**Gate:** replay harness green (162/162); CV of a jump-capable ship unchanged before and after; an
existing boost-jump still removes the ship at end of Fire.

*This stage is pure refactor and should be committed/verified on its own. Every later stage depends
on it and none of them will make sense if this one is subtly wrong.*

### Stage 2 — declaration
`hextarget`/`ballistic`/`range 4`/7 firing modes, mode selector suppressed. Remove the boost path:
`boostable = false`, delete the `isJumpEngine` branch in
[SystemPowerSettings.js:293](source/public/client/UI/reactJs/system/SystemPowerSettings.js#L293),
replace the boost-driven `jumping[]` checklist in
[gamedata.js:829](source/public/client/gamedata.js#L829) with a fire-order-driven one.
Server-side legality in `Firing::validateFireOrders`: ≤4 hexes, hex not Terrain/vortex/gate/Enormous,
engine online and undestroyed, one vortex per ship.
**Gate:** declaring writes a `tac_fireorder` row with the right `x`, `y`, `firingmode`; the enemy's
gamedata during phase 1 does not contain it.

### Stage 2b — the facing control (§3.5)
The `queueJumpPointOrder` branch in `targetHex`, preview marker sprite exposing `getPosition()`,
`#vortexFacingUI` markup, `UI.vortexFacing` with turn-left/turn-right/OK, default facing from the
declaring ship's heading, anchoring wired into the zoom/scroll callback lists.
⚠️ Two things the async branch must not forget, both copied from `queueShadowFighterBombOrder`: the
OK callback owns `unSelectWeapon` + the `HexTargeted` event, and any sprite mutation outside the
animation list must call `requestRender()` or the arrow will not redraw.
**Gate:** OK creates exactly one `tac_fireorder` row with the chosen `firingmode`; clicking away
creates **none**; default facing lets the declaring ship fly straight in untouched; two ships
declaring in the same phase produce two correct rows; the control follows the map on zoom and scroll;
the weapon deselects after OK.

*Split from Stage 2 deliberately — Stage 2 is server-truth and verifiable from a DB export, 2b is
pure interaction.*

### Stage 3 — the vortex unit
`SpawnJumpPoint` + spawn sweep at the end of `InitialOrdersGamePhase::advance` + the note +
`onIndividualNotesLoaded`. Icon (`img/ships/JumpPoint.png`) and facing rendering.
⚠️ Do **not** branch on `$gamedata->phase` inside `advance()` — it already reads 2 by then.
**Gate:** a declared vortex exists as a `tac_ship` row with a `deploy` movement row carrying the
chosen facing, is visible to both players from Movement onward, and survives a page reload.

### Stage 4 — jumping out
`jumpout` movement type, `canJumpOut`/`doJumpOut` in `movement.js` with the §2.2 entry test,
a new `ShipTooltipMovementMenu` wired into
[MovementPhaseStrategy.js:95](source/public/client/renderer/phaseStrategy/MovementPhaseStrategy.js#L95),
the `validateThrustPayment` terminator, and removal in `MovementGamePhase::advance`
(`doHyperspaceJump` minus the roll → `submitDamages($id, $turn, $gd->getNewDamages())`, mirroring
[PreFiringGamePhase.php:25](source/server/Phase/PreFiringGamePhase.php#L25)).
Delete the old sweep at [firing.php:1277-1289](source/server/handlers/firing.php#L1277).
**Gate:** ship enters from the correct side → button appears, movement ends, ship gone before
Pre-Firing, CV preserved in the fleet list. Wrong side → no button. Wrong side via client tampering
→ server refuses.

### Stage 5 — lifecycle
Maintain mode, the four closure conditions, the 4-turn cap, the all-systems-offline check, and the
end-of-turn failure roll in `JumpEngine::criticalPhaseEffects`.
**Gate:** a vortex left unmaintained closes at end of its first full turn; a maintained one survives
to the cap; a holder that strays to 5 hexes closes it; a damaged engine rolls each turn.

### Stage 6 — polish
Replay (`ShipJumpAnimation` / the existing `ShipJumpPoint` particle effect for formation and
closure), combat-log lines, `faq.php` section, tooltip text on the Jump Engine
(`setSystemDataWindow` — the current text at
[baseSystems.php:5172](source/server/model/systems/baseSystems.php#L5172) describes the boost method
and must be rewritten).

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
7. **Attached units.** Pod/grapple movement rows are all type `attached` and mirror the host, so an
   attached pod is invisible to any "entered a new hex" test. Decide explicitly whether an attached
   pod jumps with its host (it should) and whether it can jump independently (it should not).
8. **The replay harness does not cover masking or damage resolution.** The "enemy can't see my
   declared vortex hex during Initial Orders" rule and the failure roll are both unprotected by
   regression tests — hand-check from both seats.
9. **Hangar interlock ordering.** Moving the removal from `Firing::fireWeapons` to
   `MovementGamePhase::advance` means the jump damage entry now lands *two phases earlier*.
   `HangarOps::processJumpingCarrierDockOrders` still finds it (it only asks "is there jump-class
   damage this turn"), but re-read that comment block before changing the timing, and confirm a
   fighter that ordered a dock in Initial Orders onto a carrier that jumps at end of Movement still
   ends up in the hangar.
10. **In-flight games.** Removing the boost path strands any live game where a player has already
    boosted a Jump Engine for the current turn. Either keep the `isOverloading` sweep alive for one
    deploy cycle behind a "legacy" comment, or confirm with the community first.

---

## 6. Test plan

Local Docker, a **fresh game per scenario** (not `safeGameID`), verified against `tac_*` exports.

| # | Scenario | Expect |
|---|---|---|
| 1 | Declare vortex 4 hexes away | `tac_fireorder` row, x/y/firingmode correct; opponent's phase-1 payload has no such row |
| 2 | Declare 5 hexes away | Refused client-side; refused server-side if forced |
| 3 | Declare onto an asteroid / another vortex / an Enormous unit | Refused |
| 3a | Target a hex straight ahead, click OK immediately | Default facing already lets the declaring ship fly straight in |
| 3b | Step the facing all the way round, then OK | Arrow tracks each step; `firingmode` matches the submitted row; weapon deselects |
| 3c | Target a hex, then click away without OK | **No** `tac_fireorder` row; preview marker gone; weapon still selected and re-targetable |
| 3d | Two ships declare vortices in the same Initial Orders | Two correct rows; the second transaction does not disturb the first |
| 3e | Zoom and scroll mid-transaction | Control stays anchored to its hex |
| 3f | Declare, then remove the firing order and declare again | Transaction re-runs cleanly; exactly one row survives |
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

## 7. Phase 2 — Fixed Jump Gates (not in this plan's scope)

Recorded here so the Phase 1 design does not paint it into a corner:

- Any unit within **10 hexes** may signal a gate in the Jump Point Formation segment; the vortex
  forms in the gate's own hex with the gate's own facing, and cannot be projected.
- Programmable open duration, max 4 turns; **20-turn** recharge afterwards.
- Reactor damage: every 3 boxes destroyed adds 1 turn to the recharge; every 15 points of damage
  removes 1 turn from the maximum hold; total reactor loss destroys the gate. No other criticals.
- Ownership / contested-claim resolution (owner first, then nearest, then roll off — **not**
  initiative).

**The one structural blocker to solve first:** `isMyShip` / `isMyorMyTeamShip` return false for
terrain outside Deployment ([gamedata.js:212](source/public/client/gamedata.js#L212)), so a neutral
gate is unclickable. Phase 2 needs a deliberate, narrow exception — not a general loosening of the
terrain gate.

Phase 1's vortex unit is designed to be exactly what a gate opens, so nothing here is wasted.
