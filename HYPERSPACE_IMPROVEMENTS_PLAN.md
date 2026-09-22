# Hyperspace Improvements — Jump Points / Reinforcements, Phase 4

Follow-up to [JUMP_POINTS_PLAN.md](JUMP_POINTS_PLAN.md) (Phase 1, the ship-opened vortex),
[JUMP_GATES_PLAN.md](JUMP_GATES_PLAN.md) (Phase 2, fixed gates) and
[REINFORCEMENTS_PLAN.md](REINFORCEMENTS_PLAN.md) (Phase 3, arriving from hyperspace). Six user
requests, 2026-09-17.

**Read those three first.** Nothing they establish is restated here: the hex-direction convention,
the vortex unit lifecycle, the `getTurnPlaced` vs `getTurnDeployed` split, the exit/entrance word
swap of 2026-08-29, the one-vortex-per-shooter rule and the `markLegacy` / `markAncient` /
`markWalker` flag ladder all apply unchanged.

⚠️⚠️ **THE WORDS ARE THE OTHER WAY ROUND IN THE CODE THAN IN THE REQUEST.** Since the 2026-08-29
swap the codebase calls the **blue arrivals** doorway an **exit** (`SpawnJumpPointExit`,
`damageclass 'jumpexit'`) and the **yellow departures** doorway an **entrance**
(`SpawnJumpPoint`, `damageclass 'jumppoint'`). The request uses both words in their plain-English
sense. This plan uses the CODE's sense throughout, and says **yellow** / **blue** wherever the
colour is what matters. Confirmed with the user 2026-09-17: the log colours follow the map markers
— `#e1b000` on everything yellow, `#00b8e6` on everything blue.

---

## 0. Rulings settled before build (user, 2026-09-17)

| # | Ruling |
|---|---|
| R1 | The combat-log header word is **HYPERSPACE**, coloured **yellow for leaving, cobalt for arriving**, matching the existing map markers exactly. |
| R2 | The jump-out boost **stays on `boost`**. It is disambiguated from the new recharge boost **by charge state**, not by moving to `doActivate`. |
| R3 | Recharge arithmetic: **boost N ⇒ N+1 turns of charging**, capped at 4 turns in any one turn ⇒ **`maxBoostLevel = 3`**. |
| R4 | A Vorlon paying its jump-drive upkeep **replaces** the all-systems-dark Maintain rule — it does not pay both. |
| R5 | Paying the upkeep **overrides `MAX_VORTEX_TURNS`** — a Vorlon jump point is held indefinitely while it is paid. |
| R6 | **Failure to pay closes the jump point**, with its own combat-log closure reason. |
| R7 | The per-hull Vorlon `powerReq` numbers come from the user. **Supplied 2026-09-17 — see §10 A1.** |

---

## 1. Build order, and why this order

The six items are not independent. Three dependencies decide the order:

1. **Item 6 is a prerequisite for Item 2 being visible on Vorlons.** The recharge boost costs "the
   same as their normal power required" — and every Vorlon jump drive's `powerReq` is `0`, so on
   the eleven Vorlon hulls the new boost would be free until Item 6 gives the drive a real number.
2. **Item 3 must land before Item 4.** Item 4 removes the DEPLOYMENT: REINFORCEMENTS phase by
   pre-setting arrival speed and hangar berths at manifest time; Item 3 makes a blue doorway
   re-usable on later turns, which multiplies the number of manifests. Building 4 first means
   building its manifest UI twice.
3. **Item 5 is independent of all five others** and is one line plus its consequences. It is the
   cheapest thing on the list and should go first, so that the Item 4 work can be tested with a
   hyperspace unit whose power controls actually respond.

| Stage | Item | Size | Server / client |
|---|---|---|---|
| **H1** | 1 — HYPERSPACE log header | XS | client only |
| **H2** | 5 — power management from hyperspace | S | client only (+1 server audit) |
| **H3** | 6 — Vorlon jump-drive upkeep | M | both |
| **H4** | 2 — recharge boosting | M | both |
| **H5** | 3 — maintaining a blue doorway | M | both |
| **H6** | 4 — retire DEPLOYMENT: REINFORCEMENTS | **L** | both |

H1–H2 are safe to ship together in one deploy. H3–H4 belong together (H4 is inert on Vorlons
without H3). H5 and H6 each want a deploy of their own.

---

## 2. Stage H1 — "HYPERSPACE" in the combat log (Item 1)

### What it is

Every hyperspace event in the log is a `RammingAttack` fire order at 100/100 against the ship
itself, carrying its sentence in `pubnotes` — `JumpEngine::writeVortexLogOrder`
([baseSystems.php:8591](source/server/model/systems/baseSystems.php#L8591)) writes the vortex ones,
`JumpEngine::doHyperspaceJump` and `Movement::applyJumpOut` the departure ones. They are already
listed in `weaponManager.doShortLogText`
([weaponManager.js:5562](source/public/client/weaponManager.js#L5562)) so the log prints the
sentence alone. All that is missing is the header word and its colour.

### The problem the current code has

All vortex open/close lines — yellow AND blue, ship AND gate — carry the **same** `damageclass`,
`'JumpVortex'`. The colour split needs a discriminator.

### Design

**Split the damageclass, do not parse the sentence.** `writeVortexLogOrder` gains a 4th argument
and stamps `'JumpVortexExit'` when the vortex it is reporting is a `SpawnJumpPointExit` (which
covers `SpawnJumpPointPhaseIn` by inheritance), `'JumpVortex'` otherwise. Then one table on the
client decides both the word and the colour:

```
yellow  #e1b000   JumpVortex, HyperspaceJump, JumpFailure
cobalt  #00b8e6   JumpVortexExit
```

`HyperspaceJump` / `JumpFailure` are yellow because they are always a departure — the
phase-in arrival path writes a `jumpexit` declaration and a `JumpVortexExit` line, never a
`HyperspaceJump` one.

### Files

| File | Change |
|---|---|
| [baseSystems.php](source/server/model/systems/baseSystems.php) | `writeVortexLogOrder($ship, $gamedata, $pubNotes, $isExit = false)`; pass `true` from the exit-side callers (`openExitVortex`, the exit branch of `recordVortexClosure`, `openVortexAtGate` when the gate opened blue) |
| [firing.php](source/server/handlers/firing.php) | add `'JumpVortexExit'` to `Firing::isHyperspaceLogOrder` |
| [weaponManager.js](source/public/client/weaponManager.js) | add `'JumpVortexExit'` to `shortLogTypes` in `doShortLogText` |
| [combatLog.js:278](source/public/client/combatLog.js#L278) | the header: word + colour from the table above |
| [logPanel.css](source/public/styles/logPanel.css) | `#log .logheader.hyperspace` variants, so the colour is a class not an inline literal |

### Traps

- ⚠️ **`isHyperspaceLogOrder` is what stops a log order being re-resolved FOUR TIMES.** A new
  damageclass that is not on that list is gathered by `preparePreFiring`, `firePreFiringWeapons`,
  `prepareFiring` AND `fireWeapons`, and the unit rams itself. This is the Stage 4 trap from
  JUMP_POINTS_PLAN.md; it applies to every new hyperspace damageclass, forever.
- ⚠️ **The header colour is currently the SHOOTER'S team colour**
  (`gamedata.getShipLogColorCss`), and replacing it with a fixed yellow/cobalt removes the per-viewer
  allegiance signal from that one span. That is acceptable **only because the 3px allegiance rail on
  the entry comes from the same source and stays** ([combatLog.js:105](source/public/client/combatLog.js#L105)).
  Do not also restyle the rail.
- ⚠️ **Ship names inside `pubnotes` must stay bare `shiplink` spans.** `colourShipLinksInNotes`
  colours them per viewer; a hard-coded colour in a sentence would be wrong for the opponent.
- ⚠️ **Old games keep the old damageclass.** Every `JumpVortex` row already in `tac_fireorder` for a
  blue doorway will read yellow in replay. That is cosmetic and affects replay only; do **not**
  write a migration for it (the same `damageclass` column was migrated once already, by
  `db/reinforcementsRename.sql`, and the cost/benefit is nothing like the same here).

### Test

Replay harness is blind to this (no serialised property moves). Verify by eye in game 4302 and by a
node check that `doShortLogText` returns true for the new class. `fvbuild.ps1 -Client`.

---

## 3. Stage H2 — power management from hyperspace (Item 5)

### The single blocker

```php
// PhaseStrategy.js:1765
if (shipManager.getTurnDeployed(ship) > gamedata.turn) return;
```

`PhaseStrategy.prototype.onSystemClicked`
([PhaseStrategy.js:1765](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L1765))
swallows the click before `showSystemInfo` ever runs, so the system menu — Power Settings,
Overcharge, everything — never opens. A unit in hyperspace answers **999** to `getTurnDeployed`, so
it is caught by the same guard that stops you fiddling with a late-slot ship standing at its entry
hex.

**The server is already willing.** `InitialOrdersGamePhase::process`
([InitialOrdersGamePhase.php:164](source/server/Phase/InitialOrdersGamePhase.php#L164)) walks every
ship of the posting player and calls `$dbManager->submitPower(...)` with **no deploy-turn filter at
all**, and `ajaxInterface` posts `system.power` for every own ship. So the rows already travel and
already persist. This is a client-side gate and nothing else.

### Design

Replace the blanket guard with a narrower one. The rule we want is *"a unit that is not on the board
cannot be given ORDERS, but may be given a POWER ALLOCATION"* — which is exactly the distinction the
Walker Docking Bay already draws for a stowed ship.

```js
// new: shipManager.canManagePowerFromHyperspace(ship)
//   true  for my own reinforcement waiting in hyperspace, in Initial Orders
//   false for everything else that is not yet on the board
```

`onSystemClicked` then reads
`if (shipManager.getTurnDeployed(ship) > gamedata.turn && !shipManager.canManagePowerFromHyperspace(ship)) return;`

Everything downstream of that already fails closed correctly for a unit with no hex:

- `SystemPowerSettings.canOffline / canOnline / canBoost / canOverload` are all gated on
  `gamedata.gamephase === 1` and read only the system, never a position.
- `weaponManager.selectWeapon` / `targetHex` already refuse a unit with no hex (they measure range
  from `getHexPos`), so no fire order can be produced from hyperspace by accident.
- `InitialPhaseStrategy.targetShip` already passes `orderSource = null` for an undeployed selection,
  so the tooltip's order buttons stay off.

### What must be widened alongside it

| Site | Why |
|---|---|
| `power.getShipsNegativePower` ([power.js:341](source/public/client/power.js#L341)) | the Initial Orders commit gate. A hyperspace ship that the player has just over-allocated must be named, or the deficit is invisible until it arrives. Widen the `deployTurn > turn` skip with the same predicate. |
| `power.getShipsGraviticShield` ([power.js:311](source/public/client/power.js#L311)) | same reasoning, same predicate |
| `gamedata.js` commit checklist (≈L1121, L1492, L1596, L1917, L2066, L2138, L2207) | ⚠️⚠️ **seven separate `deployTurn > gamedata.turn` skips, and only the POWER ones move.** User ruling 2026-09-17: **a unit in hyperspace manages power and NOTHING else — no EW.** So the negative-power and gravitic-shield arms widen; the OEW/DEW arms, the movement arms, the deployment-box arms and the initiative arms all stay closed. |

### The overload question

"Let them overload weapons so they are fully charged in Sustain mode when they arrive" works as soon
as the click lands: `onOverloadClicked` writes a type-3 power row, `submitPower` stores it, and
`Weapon`'s overload machinery is turn-indexed, not position-indexed. **No server change.** Verify in
play that `overloadturns` accumulates across the hyperspace turns — if it does not, the cause will be
the per-turn advance sweep skipping undeployed ships, not this stage.

### Traps

- ⚠️ **`getTurnDeployed` throws outright on `null`.** Every new call site needs the null guard the
  Jump Gates work added (JUMP_GATES_PLAN.md trap 11).
- ⚠️ **Do not widen `PhaseStrategy.isOffBoardForEdf`**
  ([PhaseStrategy.js:372](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L372)). A
  unit in hyperspace must still project no Energy Draining Field, and it reads the same accessor.
- ⚠️⚠️ **NO EW FROM HYPERSPACE** (user ruling 2026-09-17). Power allocation only. Nothing in `ew.js` moves, and the EW arms of the commit checklist stay closed.
- ⚠️ **`shouldBeHidden` stays as it is.** The unit has no icon; the ship window is reached from the
  fleet list, which is the route the user is already using.

---

## 4. Stage H3 — the Vorlon jump-drive upkeep (Item 6)

**The numbers arrived 2026-09-17 — see §10 A1: 8 on the Planet Killer, 6 on the Strike Cruiser /
Heavy Cruiser / Heavy Carrier, 5 on the other seven, and the Transport has no drive at all.**

### The current workaround

All eleven Vorlon hulls plus the Planet Killer construct their drive as
`new JumpEngine($armour, $maxhealth, 0, $delay, 12)` with the comment *"Vorlon Jump Engines normally
do use power … in FV I simplify to 0 power requirement"*. The 5th argument (projection range 12) is
already a Vorlon-specific number passed per hull, so **the power requirement follows exactly the
same pattern** — a per-hull 3rd argument, no new mechanism.

### Design — three pieces

**(a) The number.** Replace the `0` in each of the 12 Vorlon ship files with the real value. Nothing
else changes about construction: `powerReq` is a `ShipSystem` property the power icon already draws
and `getReactorPower` already subtracts.

**(b) The draw.** A Vorlon has no Reactor — `PowerCapacitor` injects its stored power by publishing a
**negative** `powerReq`, and `power.getReactorPower`
([power.js:493](source/public/client/power.js#L493)) sums the hull. So the client side of "pay from
the capacitor" is **free**: a non-zero `powerReq` on the drive is subtracted by the existing loop and
shows as a deficit the player must cover, exactly as any other Vorlon system does.

The **upkeep while the point stands** is not free, and the pattern to copy is the one Stage 20
established for the Extra-Dimensional Jump Drive:

```js
// power.js getReactorPower, beside the existing abduction draw
if (system.name === 'jumpEngine' && typeof system.getVortexUpkeepDraw === 'function') {
    output -= system.getVortexUpkeepDraw();
}
```

Server side, `PowerCapacitor::canDrawPower` / `doDrawPower` already exist and are already called from
weapon fire; the vortex upkeep calls the same pair from the closure sweep.

**(c) The rules changes (R4, R5, R6).** All three live in one method,
`JumpEngine::getVortexClosureReason`
([baseSystems.php:8698](source/server/model/systems/baseSystems.php#L8698)):

```
if (this drive charges upkeep) {
    if (!capacitor can pay powerReq)  return 'jump point not powered';   // R6
    // R4: skip getVortexPowerViolations entirely
    // R5: skip the MAX_VORTEX_TURNS cap
    // everything else on the ship list still applies: destroyed, range, Maintain declared
}
```

⭐ **"Charges upkeep" is a property of the DRIVE, not of the faction string.** Add
`JumpEngine::$vortexUpkeep = false` (protected — it must not reach a static blueprint) plus
`markCapacitorFed()`, set from the Vorlon ship files the same way `markAncient()` is. Testing
`$ship->faction === "Vorlon Empire"` would be the third such string test in the tree and would break
the moment somebody builds a custom Vorlon-derived faction.

### Traps

- ⚠️⚠️ **R5 removes the cap the client uses to hide the Maintain toggle.** `JumpEngineMenu` hides it
  at `gamedata.turn >= vortex.spawned + 3` because the 4-turn cap overtakes a Maintain declaration on
  N+4. With no cap that test must not run for an upkeep drive, or the player loses the control that
  keeps the point open on turn 5.
- ⚠️⚠️ **`stripForJson`'s `vortexMaxTurns` has no value to send.** The icon reads `N/4`. With no cap
  it must read `N` alone, or a bounded-looking counter will claim the point is about to close every
  turn from 4 onwards. Send `vortexMaxTurns` **only when there is a cap**, and teach
  `JumpEngine.getVortexIconLoad` to draw an open-ended counter when it is absent — the same
  "emitted only when it means something" convention `arrivalIniPenalty` and `abductionLastHold` use.
- ⚠️ **R4 makes `getVortexPowerViolations` unreachable for these ships — check the CLIENT mirror
  too.** `shipManager.power.getVortexMaintainBlockers` and `isVortexLockedOffline` are what shut the
  ship down and lock it down on `doActivate`. Both must no-op for an upkeep drive, or the one-click
  Maintain will still black the ship out and then refuse to give the power back.
- ⚠️ **A statics regeneration is required.** `powerReq` is a public `ShipSystem` property that
  `ShipCompactor` does not strip, and the LOBBY reads the blueprint alone. `fvbuild.ps1 -Server`.
- ⚠️ **Check the point cost.** If `powerReq` feeds any cost formula, twelve hulls change price.
  Verify with `checkShipData.php`'s committed baseline before and after — a diff there is the tell.
- ⚠️ **`getVortexUpkeepDraw` must answer 0 on every other jump engine in the game**, the way
  `getAbductionPowerDraw` does, or 776 drives grow a draw.

---

## 5. Stage H4 — boosting the recharge (Item 2)

### The collision, stated precisely

| Population | `factionAge` | drive | `boostable` today | boost means |
|---|---|---|---|---|
| Vorlons, The System | 3+ | ordinary vortex engine | **false** | — (nothing) |
| Kirishiac, Mindriders, Torvalus, Triad, Thirdspace, Shadows, Walkers | 3+ | `markAncient()` / `markWalker()` legacy | **true** | Jump to Hyperspace |
| Trek, BSG, Star Wars | 1–2 | `markLegacy()` | **true** | Jump to Hyperspace |

So the collision exists on **exactly one group**: age 3+ legacy drives. On Vorlons and The System the
boost carrier is completely free today.

### Design (R2 — gate by charge state)

**Three narrow rules, and one new derivation.**

**(1) The meaning of a boost on a jump engine is decided by the drive's charge at the START of the
turn.**

```
getVortexRechargeLoad(turn) == getVortexRechargeTime()   ->  boost = JUMP TO HYPERSPACE
getVortexRechargeLoad(turn) <  getVortexRechargeTime()   ->  boost = EXTRA CHARGING
```

The two states are mutually exclusive by construction, which is what makes one carrier safe. It is
also the rule the user asked for independently: *"Boosting should only be available when the Jump
Engine is on cooldown."*

**(2) The charge credit is applied at TURN END, never within the turn that spent it.**
`getVortexRechargeLoad($turn)` counts boosts from turns **strictly before** `$turn`. This is not a
detail — it is what makes rule (1) unambiguous. If a boost counted toward the same turn's charge, a
boost that completed the charge would flip its own meaning from "charging" to "jumping" mid-turn.
It also matches the rulebook: *"each time the listed amount of extra power is applied, the jump
engine is considered to have been operating for an additional turn."*

The derivation replaces the closed form in `getJumpPointRechargeLoad`
([baseSystems.php:6668](source/server/model/systems/baseSystems.php#L6668)) with a forward walk:

```php
$load = 0;                                     // at the close turn
for ($t = $closeTurn + 1; $t < $turn; $t++){   // strictly before $turn
    if ($load >= $charge) break;               // full: later boosts are jumps, not charge
    $load = min($charge, $load + 1 + $this->getRechargeBoostLevel($t));
}
return min($charge, $load + max(0, $turn - max($closeTurn + 1, $t)));
```

⚠️ **Keep the no-boost answer bit-for-bit identical to today's `min($charge, $turn - $closeTurn)`.**
A drive that never boosts must produce the same number it produces now, or 148 of 160 replay-harness
snapshots move.

**(3) `maxBoostLevel = 3`** (R3), and **only on drives that may charge this way**.

### Who may charge this way

`factionAge >= 3` — but `factionAge` is a **ship** property and `maxBoostLevel` / `boostable` are
**system** properties read from the static blueprint. Resolve it the way `getVortexRechargeTime`
resolves the gate term: **ask at read time, via `getUnit()`**, and publish the answer from
`stripForJson`:

```php
public function hasAdvancedCharging(){
    $unit = $this->getUnit();
    return $unit && (int)$unit->factionAge >= 3;
}
```

`stripForJson` emits `advancedCharging = true` **only when set** (so 700-odd payloads stay
byte-identical), and the client reads that one key.

⭐ **This deliberately leaves young-race legacy drives alone.** A Trek Nacelle, a BSG FTL Drive and a
Star Wars Hyperdrive keep `boostable = true`, `maxBoostLevel = 1`, boost-means-jump, unconditionally
— **zero behaviour change for 195 hulls**, because rule (1) only ever runs on a drive that
`hasAdvancedCharging()`.

### ⚠️⚠️ THE ONE TRAP THAT WILL BITE

`JumpEngine::getUnitJumpingEngine($unit, $turn)`
([baseSystems.php:6312](source/server/model/systems/baseSystems.php#L6312)) is *"the engine taking
this unit out of the battle at the end of this turn"*, and it is a bare
`if ($system->isOverloading($turn)) return $system;` — **no legacy test, no charge test**. Turn
`boostable` on for Vorlons so they can charge faster, and **every Vorlon that boosts its drive
leaves the battlefield at the end of the Fire phase.**

The fix is one new reader, and every current caller of `isOverloading` on a jump engine moves to it:

```php
public function isJumpOutBoost($turn){
    if (!$this->legacyJump) return false;                       // only a legacy drive leaves this way
    if ($this->hasAdvancedCharging()
        && $this->getVortexRechargeLoad($turn) < $this->getVortexRechargeTime()) return false;
    return $this->isOverloading($turn);
}
```

**Callers to move (server):**

| Site | File |
|---|---|
| `getUnitJumpingEngine` | [baseSystems.php:6312](source/server/model/systems/baseSystems.php#L6312) |
| the end-of-Fire jump sweep | [firing.php:2273](source/server/handlers/firing.php#L2273) |
| `withdrawFireFromJumpingUnits` | [firing.php:1985](source/server/handlers/firing.php#L1985) |
| `automateIntercept`'s `isJumpingUnarmed` skip | [firing.php](source/server/handlers/firing.php) |
| `InitialOrdersGamePhase::dropFireOfJumpingShip` | [InitialOrdersGamePhase.php:149](source/server/Phase/InitialOrdersGamePhase.php#L149) — ⚠️ reads the **POST-side** ship's power, the one place this turn's boost is visible at submit. It has no gamedata, so `getVortexRechargeLoad` must be safe on a POST-side ship (it reads `$this->vortexOpenTurn` / `$this->vortexCloseTurn`, which a POST-side ship does **not** have). **Pass the turn's charge in, or ask the DB copy.** |
| `EdjdAbduction::isDriveWorking` | [EdjdAbduction.php:291](source/server/handlers/EdjdAbduction.php#L291) |

**Callers to move (client):**

| Site | Change |
|---|---|
| `JumpEngine.prototype.onBoostIncrease` ([baseSystems.js:1301](source/public/client/model/system/baseSystems.js#L1301)) | withdraws every fire order on the unit. Must run **only** when the boost is a jump, or a Vorlon that speeds its recharge silently loses all its shots. |
| `JumpEngine.prototype.initializationUpdate` ([baseSystems.js:1160](source/public/client/model/system/baseSystems.js#L1160)) | shows `"JUMP"` whenever boosted — must show the charge level instead when charging |
| `JumpEngine.prototype.hasMaxBoost` ([baseSystems.js:1169](source/public/client/model/system/baseSystems.js#L1169)) | returns `true` unconditionally; must return `boost >= maxBoostLevel` |
| `JumpEngine.prototype.mirrorFlightBoost` | a Mapmaker flight mirrors the **jump**; a charge boost is per-craft nonsense on a flight — refuse charging on a flight-mounted drive outright, the way `getMaintainDeclaration` refuses Maintain |
| `shipManager.isJumpingToHyperspace`, `movement.isJumpFireForbidden` | same predicate as the server's `isJumpOutBoost` |
| `SystemPowerSettings.render` ([SystemPowerSettings.js:295](source/public/client/UI/reactJs/system/SystemPowerSettings.js#L295)) | the `isJumpEngine` branch currently always draws "Jump to Hyperspace: Yes/No". It must draw the **stepper** (`- 0 +`) with the label **"Extra Charging"** when the drive is charging, and the Yes/No when it is ready |

### The power cost

⚠️ **The current jump boost costs ZERO power.** `countBoostPowerUsed`
([power.js:984](source/public/client/power.js#L984)) returns `boostEfficiency * boost`, and
`JumpEngine::$boostEfficiency = 0`. So *"the boost cost is the same as their normal power required"*
is a **new** cost, and it must apply to the charging boost **only** — setting
`boostEfficiency = powerReq` would silently start charging 195 young-race hulls for a jump that has
always been free.

Use the `getAbductionPowerDraw` pattern again: a `getChargeBoostPowerDraw()` on the client,
subtracted in `getReactorPower` beside the abduction draw, answering `0` on every drive that is not
charging this turn. Server mirror for the commit gate.

### Traps

- ⚠️ **`getVortexRechargeLoad` is already `min(jumpPoint, abduction)`.** The new walk goes inside
  `getJumpPointRechargeLoad` only. `getAbductionRechargeLoad` is a separate cooldown with a separate
  rule and must not inherit charging.
- ⚠️ **Adding the charge gate to jump-out is a behaviour change.** Today an Ancient drive can
  boost-jump-out while recharging (there is no charge test anywhere). After this, an age-3+ legacy
  drive that phased in recently, or a Walker that just abducted, **cannot leave until it is charged**
  — which is the rule, and is what makes the recharge boost worth having, but it will surprise a
  player mid-game. Worth a line in the tooltip.
- ⚠️ **`$gamedata->turn` is a STRING.** Every turn comparison in the new walk needs `(int)` on both
  sides (JUMP_POINTS_PLAN.md §5 trap 10).
- ⚠️ **The replay harness will move.** `turnsloaded` is in the payload for 148 of 160 games. Check by
  SHAPE before re-recording: a correct build moves `turnsloaded` **only on drives that boosted**,
  which in a corpus with no boosts is **nothing at all**. If the whole corpus moves, rule (2) has
  been implemented as "credit within the turn".

---

## 6. Stage H5 — maintaining a blue doorway (Item 3)

> **BUILT 2026-09-19 — see §11.** Four rulings were added at build time (H5-1 … H5-4), and several
> statements below turned out wrong (the snippet, "`getHeldVortex` already finds a blue one", where the ship
> sweep goes). The build log is the authority.

### What changes

One branch. `getVortexClosureReason`
([baseSystems.php:8749](source/server/model/systems/baseSystems.php#L8749)) currently closes a
ship-held exit unconditionally the turn after it forms:

```php
if ($vortex instanceof SpawnJumpPointExit && !$this->isGateJump()){
    return ($turn > (int)$this->vortexOpenTurn) ? 'reinforcements have arrived' : null;
}
```

It becomes: **fall through to the ordinary ship list** once the opener is on the board. The ship list
already contains everything Item 3 asks for — `getMaintainDeclaration`, `getVortexPowerViolations`,
the range test, the four-turn cap, the destroyed test — and all of it is now answerable, because the
opener **arrived through its own doorway** and has a hex.

```php
if ($vortex instanceof SpawnJumpPointExit && !$this->isGateJump()){
    //The opener is still in hyperspace on the forming turn: nothing to maintain with.
    if ($turn <= (int)$this->vortexOpenTurn) return null;
    if ($ship->getTurnDeployed($gamedata) > $turn) return 'reinforcements have arrived';
    //From the turn it arrives, an exit is maintained exactly as an entrance is.
}
```

⚠️ **The `getTurnDeployed` line is load-bearing and is the whole of "from the turn they arrive".** An
opener that failed to arrive (its own placement was released) is still in hyperspace and has no hex —
the range test below would throw on `getHexPos()`.

### The rest of it

| Piece | Where |
|---|---|
| **Server legality of a mode-7 order on an exit holder** | `Firing::getVortexDeclarationBlock` — its Maintain arm currently assumes a yellow vortex. It must accept a Maintain aimed at a `SpawnJumpPointExit`'s hex. |
| **The Maintain toggle offered for a blue doorway** | `JumpEngine.canMaintainVortex` / `getHeldVortex` ([baseSystems.js](source/public/client/model/system/baseSystems.js)) — `getHeldVortex` resolves the vortex by id, so it already finds a blue one; the gate is whatever narrows it to yellow. |
| **The doorway offered for a NEW wave on later turns** | `ReinforcementEntry.openerRow` ([ReinforcementEntry.js:1020](source/public/client/renderer/phaseStrategy/ReinforcementEntry.js#L1020)) — today only `gateRow` offers "Select Reinforcements" on a standing doorway. A ship-held exit now behaves like a gate's: offer the Jump Manifest on every turn it is held. |
| **The server accepting a second wave through it** | `InitialOrdersGamePhase::persistManifest` ([InitialOrdersGamePhase.php:453](source/server/Phase/InitialOrdersGamePhase.php#L453)) builds `$openers` from **this turn's `jumpexit` declaration**. It must also accept an opener holding a *standing* exit — the same widening `collectGateOpeners` is for gates. |
| **The manifest turning into arrivals** | `collectGateExits` / `holdsExitOpenOn` ([baseSystems.php:7321](source/server/model/systems/baseSystems.php#L7321)) — `holdsExitOpenOn` already answers correctly for a ship engine; only `collectGateExits`' `isGateJump()` filter excludes it. Add a ship sweep beside it. |

### ⚠️⚠️ The ordering problem the user identified

*"Since both decisions are made in the same Initial Orders Phase, we'd have to check it's being
currently maintained, and cancel any reinforcement orders if the player chooses to not maintain the
jump point after already selecting reinforcements to come through it next turn."*

This is real and it has a clean answer, because **both halves already land in the same POST**: the
Maintain declaration is a fire order on the drive, and the manifest is `arrivalVia` on each rider.
`persistManifest` runs on the server with both in hand.

**Make the server the authority and the client lenient**, which is the rule REINFORCEMENTS_PLAN.md
already states for "is this berth still good?":

- **Server (authority):** in `persistManifest`, an opener holding a standing exit qualifies as an
  opener **only if** it also carries this turn's Maintain declaration. A berth naming an opener that
  did not re-declare is cleared — which is the existing refund path, reached by the existing code,
  with nothing spent.
- **Client (feedback, not enforcement):** `JumpEngine.doDeactivate` (turning Maintain off) sweeps
  `gamedata.ships` for riders whose `arrivalVia` names this unit and clears them, with a
  `confirm.warning` naming them. And `ReinforcementEntry`'s opener row shows the doorway as
  unavailable while Maintain is off.

⚠️ **Do not implement this only on the client.** A client predicate narrower or wider than its
server twin surfaces as a button offered and then silently rejected at commit — the exact bug shape
that bit `canSignalJumpGate` and `isJumpVortexExit`.

### Traps

- ⚠️ **Trap 5 from REINFORCEMENTS_PLAN.md still governs: the exit MUST eventually close.**
  `spawnVortexUnit` opens with `if ($this->hasOpenVortex(...)) return null;` — an exit that never
  closes locks its holder out of ever opening anything again, including an entrance to leave by. The
  four-turn cap is what guarantees closure once the exit joins the ship list. **Do not exempt a
  ship-held exit from `MAX_VORTEX_TURNS`.** (A Vorlon's upkeep drive does get R5's exemption — and
  that exemption is bounded by R6, the upkeep it must keep paying.)
- ⚠️ **The opener must be maintaining from a hex within `$range` of its own doorway** — and it comes
  out of that doorway standing **in** it, so distance 0. But it may then move. That is the rule, and
  the existing range branch expresses it; do not special-case it.
- ⚠️ **An Ancient / legacy phase-in doorway (`SpawnJumpPointPhaseIn`) is a subclass of
  `SpawnJumpPointExit`** and would be swept up by this change. It is never drawn and carries only
  fighters docked aboard the opener — a Maintain on one is meaningless. **Refuse it explicitly**
  (`$vortex instanceof SpawnJumpPointPhaseIn` → keep the old one-shot answer), or a Shadow hull will
  be offered a Maintain toggle for an invisible doorway.

---

## 7. Stage H6 — retire DEPLOYMENT: REINFORCEMENTS (Item 4)

> **BUILT 2026-09-19 — see §11.** One structural deviation: the placement sweep runs from
> `Manager::changeTurn` at the start of turn N+1, **not** from `FireGamePhase::advance` at the end of turn N
> as H6c below says, and the reason is in the build log. The hangar-berth intent lives in the same
> `ArrivalOrder` note as the speed, on the RIDER, not on the opener's engine.

The largest of the six, and the one with the most ways to strand a unit off-board permanently.
Build it **last** and in three sub-stages that each leave the tree shippable.

### What the phase is actually for today

`DeploymentPhaseStrategy.autoPlaceArrivingReinforcements`
([DeploymentPhaseStrategy.js:148](source/public/client/renderer/phaseStrategy/DeploymentPhaseStrategy.js#L148))
already removed the click: the wave places itself the moment the phase activates. The phase survives
for exactly **three** remaining jobs:

1. the player sets each arrival's **speed** (`canChangeSpeed` allows the 0–10 accel arrows during
   Deployment);
2. flights riding a **legacy** opener are force-docked (`queueDeployStartDock` + `forcedDeployDock`);
3. a unit whose doorway vanished is **released back to hyperspace** with nothing spent
   (`DeploymentGamePhase::releaseUnplacedReinforcements`).

Pre-set (1) and (2) at manifest time and the phase has nothing left to do — which is exactly the
user's reasoning.

### H6a — arrival speed in the Jump Manifest

**Where it goes.** A speed control per ticked row in `showManifestDialog`
([ReinforcementEntry.js:1135](source/public/client/renderer/phaseStrategy/ReinforcementEntry.js#L1135)),
including for the opener itself (which is never in its own rider list — add a fixed first row).

**How it travels.** `arrivalVia` is already a POST-side ship property, whitelisted in
`Manager::getShipsFromJSON` ([Manager.php:2104](source/server/controller/Manager.php#L2104)) and
persisted by `persistManifest`. **`arrivalSpeed` rides the identical channel** — one more whitelisted
integer, one more thing `persistManifest` writes.

**Where it is stored.** Two options, and the second is recommended:

| Option | Cost |
|---|---|
| a new `tac_ship.arrivalspeed` column | a migration that must run **with** the deploy, on a remote shared MariaDB; matches `arrivalturn` / `arrivalvia` exactly |
| an `IndividualNote` on the rider's own primary `Structure`, notekey `'ArrivalSpeed'` | **no schema change**; same precedent as the `jumped` note. ⚠️ `notekey_human` is varchar(40). |

⚠️ **Whichever is chosen, `arrivalSpeed` must be clamped server-side.** It is player-supplied and it
becomes a movement row's `speed`; an unclamped value is a tampered-POST hole. Clamp to the same 0–10
`canChangeSpeed` allows, and to 0 for `osat`/`base` (which `movement.deploy` already forces).

⚠️ **This changes an existing initiative interaction.** REINFORCEMENTS_PLAN.md Stage 9 notes the
arrival penalty *"stacks on top of the pre-existing −50 for speed 0, because initiative for turn N+1
is rolled before the wave sets its speed."* With the speed pre-set a turn earlier, **the −50 no
longer applies** to a wave that chose a non-zero speed. That is an improvement, and it is a balance
change: flag it before it is noticed in play.

### H6b — "Start in hangar" in the Jump Manifest

**Eligibility is a function of the whole manifest, not of one row** — the user is explicit about
this, and it is the hard part. A checkbox on row R is legal only if R fits in the *cumulative*
remaining hangar capacity of the opener **plus every other ticked ship that carries hangars**.

The machinery exists on both sides and is already cumulative:

- client: `DeploymentDock.planFlightsIntoCarrier`
  ([DeploymentDock.js:306](source/public/client/renderer/phaseStrategy/DeploymentDock.js#L306)) —
  walks a list in order, reserving boxes, returning the ids that fit;
- server: `InitialOrdersGamePhase::legacyBerthFits`
  ([InitialOrdersGamePhase.php:402](source/server/Phase/InitialOrdersGamePhase.php#L402)) — the same
  walk with a `&$reserved` accumulator.

And the dialog **already has the re-fit loop**: the `legacy` branch's `refit()` greys every row that
would no longer fit beside the ones already ticked, re-running on every `change`. **H6b is that loop,
generalised from "legacy opener only" to "any opener, over the set of ticked hosts".** That is the
right shape and it is already proven in play.

⚠️ **Changing the manifest must re-validate the hangar plan, not just the row that changed.**
Un-ticking a carrier can evict craft that were riding *its* hangars. `refit()` currently only
disables un-ticked rows; it must also **un-tick and warn** when a previously legal choice becomes
illegal.

**Where the intent is stored.** A `'HangarBerth'` IndividualNote on the **opener's jump engine**,
one entry per rider (`riderId:carrierId:hangarId`), written by `persistManifest`. The engine is
already the thing that carries per-vortex state notes and already has an
`onIndividualNotesLoaded` branch to extend.

### H6c — the server places the wave, and the phase is skipped

**One new sweep, at one seam.** `FireGamePhase::advance`
([FireGamePhase.php:80](source/server/Phase/FireGamePhase.php#L80)) already runs
`stampExitManifests` — the moment a manifest becomes an arrival turn — and then the slot loop that
grants the Deployment phase. The new sweep goes **between** them:

```
JumpEngine::stampExitManifests(...)          // existing: berths -> arrivalturn = N+1
JumpEngine::placeArrivingReinforcements(...) // NEW: write each arrival's deploy row for turn N+1
                                             //      and perform each 'start in hangar' dock
... slot loop ...                            // and drop the hasReinforcementsArriving clause
```

For each unit stamped with `arrivalTurn = N+1`:

1. resolve its doorway (`JumpEngine::getArrivalVortex`) — **the hex is final by now**, the deviation
   roll happened at the end of Initial Orders on the forming turn;
2. if it has a `'HangarBerth'` note, call `HangarOps::performDeployStartDockFromOrders` and write
   **no** movement row;
3. otherwise write a `deploy` movement row at `turn = N+1`, position = the vortex's hex, facing =
   heading = the vortex's facing, speed = the clamped `arrivalSpeed`;
4. if the doorway is gone or the dock refuses, **release it**: `arrivalTurn = null`, `arrivalVia`
   cleared (unless the opener is a gate) — i.e. `releaseUnplacedReinforcements`' body, moved here.

Then, in the slot loop, **delete the `hasReinforcementsArriving` clause**
([FireGamePhase.php:134](source/server/Phase/FireGamePhase.php#L134)). That clause is the *only*
thing that grants a Deployment phase for an arrival; every other clause (`depavailable`, the legacy
fallback, `checkDeploymentPhaseForPlayer`) is about something else and must stay.

### ⚠️⚠️ Traps — this is where units get stranded

- ⚠️⚠️ **A slot can have BOTH an arrival and a real deployment on the same turn.** It still gets a
  Deployment phase, and `autoPlaceArrivingReinforcements` will run. It must not write a **second**
  deploy row — `validateDeployment` throws *"Found more than one deployment entry"* and takes the
  whole submission down. The guard already exists and already reads the committed rows
  (`hasDeployMoveThisTurn`, [DeploymentPhaseStrategy.js:217](source/public/client/renderer/phaseStrategy/DeploymentPhaseStrategy.js#L217)).
  **Verify it fires against a server-written row**, which is a different provenance from the one it
  was written for.
- ⚠️⚠️ **`releaseUnplacedReinforcements` must not run twice.** It currently runs in
  `DeploymentGamePhase::process`. After H6c the server has already placed or released everything, so
  the Deployment-phase copy will find a committed deploy row and skip — but only if the row is
  visible to it. **Check it reads `$gamedata->ships`, not the POST-side array.**
- ⚠️ **Writing a movement row for turn N+1 during turn N's advance is new.** `applyJumpOut` writes
  same-turn rows; `spawnVortexUnit` writes a `spawned = N+1` *ship*, not a *move*. Confirm
  `DBManager::getMovesForShips` (which fetches only `turn = 1 OR N-1 OR N OR type IN (deploy,start)`)
  returns it — the `type = deploy` clause says it will, and that clause is exactly why this is safe.
- ⚠️ **Initiative for turn N+1 is generated at the end of turn N.** The deploy row and the arrival
  initiative penalty must be written in an order that leaves `getReinforcementArrivalIniModifier`
  answering the same thing it answers today. Place the sweep **after** whatever generates initiative,
  or assert the penalty is unchanged in the harness.
- ⚠️ **The REPLAY.** `DeploymentPhaseStrategy` carries a note about replaying a
  DEPLOYMENT: REINFORCEMENTS phase on turn 5. Removing the phase makes replay simpler, but a game
  recorded **before** this deploy still has those phases in its history. Confirm the replay walker
  tolerates a turn whose phase -1 has no rows for a slot.
- ⚠️ **Do not delete the phase header string.** `"DEPLOYMENT: REINFORCEMENTS"` is still correct for
  the mixed case above.
- ⚠️ **The `forcedDeployDock` refusals stay.** The dock dialog, the map DOCK button and un-queue all
  refuse to undo a forced dock. Those units now never see a Deployment phase, so the refusals are
  belt-and-braces — leave them, they are what makes the mixed case correct.

---

## 8. Cross-cutting

### Serialisation discipline

Four new payload keys across the six items: `advancedCharging`, `vortexMaxTurns` (now conditional),
`arrivalSpeed` and whatever `getVortexUpkeepDraw` needs. **Every one is emitted only when it means
something**, so an untouched ship's payload stays byte-identical — the convention `ancientJump`,
`walkerJump`, `abductionLastHold` and `arrivalIniPenalty` all follow.

⚠️ **No new PUBLIC property on `JumpEngine` or `Weapon` unless it is genuinely needed by the
client.** `$vortexUpkeep`, the charge-walk state and the hangar-berth map are all `protected`:
`json_encode` takes public properties only, and the static generator encodes the constructed ship, so
a public default costs 776 blueprint entries. Measure with
`grep -c '"<key>"' source/public/static/json/*.json` after `fvbuild.ps1 -Server`.

⚠️ **No `public static` on any `Weapon` subclass, ever.** `MissileRack::stripForJson` reflects
`IS_PUBLIC` and reads a static as an instance property, which kills every missile ship's payload. Use
`const`, as `ABDUCTION_CLASS` does.

### The three sweeps that must never regress

- **Never `foreach ($ship->systems as $s) if ($s instanceof JumpEngine)`.** A flight's `systems` are
  `Fighter` objects. Ask `JumpEngine::getUnitJumpEngines($unit)` (all of them) or
  `getUnitJumpingEngine($unit, $turn)` (the one leaving). Five sweeps got this wrong before.
- **On the client, a fighter subsystem's `this.ship` is the CRAFT.** Use
  `JumpEngine.prototype.getOwningUnit()`.
- **`$gamedata->turn` is a STRING.** `(int)` both sides of every turn comparison.

### Documentation

Each stage that lands appends its record here, in the house style: what was built, which files, what
the gate said, and the traps the plan did not list. Then one memory update per item, pointing back.

---

## 9. Test plan and gate

`fvbuild.ps1 -Check` after every stage: autoload map current, ship-data validator **0 new**, replay
harness green.

**Expected harness movement per stage** — check by SHAPE before re-recording:

| Stage | Expected diff |
|---|---|
| H1 | none — CONFIRMED 2026-09-17: nothing reaches `stripForJson`, and no corpus game exercises the new class |
| H2 | none (client only) |
| H3 | `powerReq` on 12 Vorlon hulls in the **statics**; harness only if a Vorlon is in the corpus |
| H4 | `turnsloaded` **only on drives that boosted** — in a corpus with no boosts, **nothing at all**. A whole-corpus move means the charge credit was implemented within-turn instead of at turn end. |
| H5 | none unless a corpus game holds a blue doorway — CONFIRMED 2026-09-19: none (4317-4319 hold exits and pass) |
| H6 | ~~new `deploy` movement rows for arrivals~~ — CONFIRMED 2026-09-19: **none**. The harness replays recorded state and never crosses a turn boundary, and the sweep runs only in `Manager::changeTurn`. 4297 / 4349 fail byte-identically with `source/server` stashed. |

**Existing harnesses to re-run, all eight:**
`tests/replay/reinforcementsStage{6,7,8,9}Harness.php`, `legacyRechargeHarness.php`,
`stage6harness.php` (68 assertions) — run in the container,
`docker exec -w /usr/src/current fieryvoid-php-1 php tests/replay/<file>` — plus the node ones,
`reinforcementsStage{8,9}ClientHarness.js` and `initialOrdersTooltipHarness.js`.

⚠️ `legacyRechargeHarness.php` is the one that will catch an H4 regression. **Extend it rather than
replacing it**, and assert **both directions on the same engine**: a drive that boosts charges
faster, and a drive that does not charges at exactly today's rate.

**New harnesses worth writing:**

- **H4:** a PHP harness over `getVortexRechargeLoad` with a table of (close turn, boost turns,
  query turn) → expected load, including the no-boost column asserted **equal to the old closed
  form**. This is the cheapest possible guard on the one derivation that can move 148 snapshots.
- **H4:** assert `isJumpOutBoost` is **false** for a boosted Vorlon drive that is recharging and
  **true** for a boosted Ancient drive that is charged. A test that only asserts the second passes
  on a build that never fixed `getUnitJumpingEngine`.
- **H6:** a PHP harness over `placeArrivingReinforcements` covering: doorway present, doorway gone
  (released), hangar berth that fits, hangar berth that no longer fits (released), and a slot with
  both an arrival and a real deployment (exactly one deploy row).

**Play-test games:** 4302 is the jump-point playtest game and its baseline goes stale constantly — a
failure there is stale DATA until `git diff source/server` says otherwise. Do not re-record while it
is mid-turn (`waiting=1`).

---

## 10. Answers to the open questions (user, 2026-09-17)

All four are settled; nothing on this plan is blocked any more.

**A1 — the Vorlon jump-drive power requirements (was R7).** Eleven hulls, not twelve: the
**Transport has no Jump Engine at all** and is off the list.

| powerReq | hulls |
|---|---|
| **8** | `VorlonPlanetKiller` |
| **6** | `vorlonStrikeCruiser`, `vorlonHeavyCruiser`, `vorlonHeavyCarrier` |
| **5** | `vorlonBattleDestroyer`, `vorlonDestroyerEscort`, `vorlonDreadnought`, `vorlonHeavyDestroyer`, `vorlonLightCarrier`, `vorlonLightCruiser`, `vorlonScout` |

⚠️ The number replaces the `0` in the **3rd** constructor argument. The **5th** (projection range
12) stays exactly as it is — `new JumpEngine($armour, $maxhealth, <power>, $delay, 12)`.

**A2 — The System is OUT OF SCOPE, and its drives get `markLegacy()`.** Not the upkeep rule, and not
the vortex mechanics either: the five hulls that carry a drive
([systemDestroyer](source/server/model/ships/theSystem/systemDestroyer.php#L65),
[systemFrigate](source/server/model/ships/theSystem/systemFrigate.php#L64),
[systemLiberator](source/server/model/ships/theSystem/systemLiberator.php#L68),
[systemLiberatorAlt](source/server/model/ships/theSystem/systemLiberatorAlt.php#L68),
[systemLightCruiser](source/server/model/ships/theSystem/systemLightCruiser.php#L53)) take
`(new JumpEngine(...))->markLegacy()` so they leave B5W jump-point mechanics altogether.

⚠️⚠️ **This interacts with H4, and the interaction is the whole reason for noticing it.** The System
is `factionAge >= 3`, so after `markLegacy()` its drives land in the *age-3+ legacy* group — the one
group where boost means two things. They will therefore acquire H4's charge gate: a System hull
cannot jump out while its drive is recharging, and may boost to charge faster. That is consistent
and almost certainly wanted, but it is a consequence of A2 rather than something A2 asked for.
**Do `markLegacy()` in the same stage as H4**, not earlier, so the two land and are play-tested
together.

⚠️ `markLegacy()` keeps `$hasJumpRecharge = true` by default, which is right here — the 4th
constructor argument on all five is a real jump delay (8–15), not a Trek impulse rating.

**A3 — arrival speed rides an `IndividualNote`, and the deploy move is the authority afterwards.**
No schema change. The note is read **once**, by `placeArrivingReinforcements` (H6c), to stamp
`speed` onto the `deploy` movement row it writes; from that moment every speed check in the game
reads the movement row as it does for any other unit, and the note is inert history.

⭐ That ordering is what makes the note safe: it is a one-shot instruction, not a parallel source of
truth. **Nothing but `placeArrivingReinforcements` may ever read it** — a second reader would be a
second authority for a value the movement row already owns, which is the shape
REINFORCEMENTS_PLAN.md warns against ("when two places could decide the same thing, pick one
authority and make the others lenient"). It also means H6c's movement row is not optional: without
it there is nothing for the speed to live on.

**A4 — confirmed: a recharging jump engine cannot be used until it is fully recharged.** The charge
gate in H4 is wanted for jumping OUT as well as for opening a jump point. Say so in the drive's
tooltip (`setSystemDataWindow`) so the restriction is discoverable rather than surprising.

---

## 11. Build log

### Stage H6 — retire DEPLOYMENT: REINFORCEMENTS (Item 4) — BUILT 2026-09-19

A wave arriving through a jump point no longer gets a Deployment phase. Its speed and "start in hangar" are
chosen in the **Jump Manifest** (H6a/H6b), and the server places, docks or sends back every arrival itself
at the start of the arrival turn (H6c). Plus a user addition made at build time: **right-click a row of
either reinforcement dialog, or its ⓘ, to open that unit's ship window** — the fleet list's two gestures,
through the same `fleetListManager.openShipWindowFor`.

**⚠️⚠️ THE ONE STRUCTURAL DEVIATION — THE SWEEP RUNS FROM `Manager::changeTurn`, NOT `FireGamePhase::advance`.**
§7 H6c put `placeArrivingReinforcements` between `stampExitManifests` and the slot loop, i.e. on the turn-N
load. Every question the sweep asks is "arriving on THIS turn": `getArrivalVortex` (`isArrivingReinforcement`
and the doorway's `spawned <= turn` window), and the hangar dock's own gates
(`HangarOps::validateDeployBayOrders`: flight and carrier both *placing this turn*, and
`Hangar::generateIndividualNotes` returning early for a carrier placed after the current turn — which would
have silently dropped the hangar snapshot). All answer no a turn early. `changeTurn` reloads the game at
turn N+1, phase −1, arrivals stamped: **the exact load the retired phase acted on**. So the deploy rows, the
hangar entries and their notes come out byte-shaped as the phase wrote them (`hangarDeployStartEvent` at
N+1/−1, `dockedTurn` N+1), replay reads them unchanged, and §7's "a movement row for turn N+1 written during
turn N is new" trap is gone. It runs **before** `generateIniative`, off the same object, with the deploy
row pushed into memory, so initiative sees the chosen speed.

**What was built, and where:**

| Piece | Site |
|---|---|
| The arrival order | One `'ArrivalOrder'` note per rider per IO commit, value `"speed:carrierId"` (0 = the map). Written by `InitialOrdersGamePhase::persistArrivalOrders`, read once by `JumpEngine::readArrivalOrder` straight from the DB (`getIndividualNotesForShip`), latest by turn then id. ⚠️ **Hosted on Structure 0, or on a flight's first craft, NEVER on a Jump Engine**: the engine's loader takes any unknown note for the pre-jump CV, while Structure and Fighter ignore unknown keys. The direct DB read means no loader has to claim the key. |
| Speed (H6a) | `JumpEngine::clampArrivalSpeed` (0–10, 0 for base/OSAT), clamped when written AND when read. Default when the manifest said nothing = `getDefaultArrivalSpeed` = the unit's last movement row's speed, i.e. its `start` row's 5 — what `movement.deploy` copied in the retired phase. |
| Hangar (H6b), server | `persistArrivalOrders` re-fits every carrier claim with `legacyBerthFits` (cumulative, `$gd->ships` order). A claim is only honoured for a carrier that is the rider's own, arrives through the **same doorway** (the opener itself when it is coming out of hyperspace, or another rider), and is not itself asking for a hangar. A failed claim is **not** a refusal of the berth: the rider comes out onto the map. A legacy opener's riders get the opener written as their carrier by rule. `legacyBerthFits` keeps its name: the two Walkers harnesses reflect on it. |
| Hangar (H6b), client | `ReinforcementEntry.planManifestHangars` (pure, exported): hosts = the opener if it is arriving + ticked riders not themselves asking for a hangar; passengers packed in list order into the first host with room via `planFlightsIntoCarrier`. The dialog re-runs it on every tick. A passenger that no longer fits is **un-ticked and said inline** (plan: "un-tick and warn"). On a legacy opener it comes off the manifest, since there is no map for it. The old legacy-only `refit()` is folded into this. |
| The manifest dialog | A fixed first row for a hyperspace opener (its own speed), so the dialog now opens even with nobody else to name. Per rider: ride tick, a **Hangar** tick (only when some carrier on the doorway could ever take it), a **Speed** box. Rows are a `<div>` with a `<label>` for the ride tick alone: a click anywhere in a label activates its FIRST control, so Hangar/Speed would otherwise toggle the ride. A declared drive's menu row now always offers **Jump Manifest**, since its own speed can change with nobody left to name. |
| The POST | `ajaxInterface` sends `arrivalSpeed` (+ `arrivalHangar`) beside `arrivalVia`, only when the manifest set them. `Manager::getShipsFromJSON` → `BaseShip::setArrivalOrderClaim`, a **protected** `$arrivalOrderClaim` (a public null would ride every static blueprint — `"arrivalVia":null` already does). |
| Placement (H6c) | `JumpEngine::placeArrivingReinforcements`: (1) onto the map — deploy row at the doorway hex, on its facing (heading too), at the chosen speed; doorway gone → `returnToHyperspace`; (2) into a carrier — ships before flights (the Deployment phase's order), through `validateDeployBayOrders` + `performDeployStartDockFromOrders` / `processBayShipDeployStartTransfer`; the carrier must have come out in pass 1 through the same doorway; refused → back to hyperspace, as the phase's dock did; (3) each touched carrier's hangars run their own change-detected `generateIndividualNotes` tail + `saveIndividualNotes`. Idempotent (a unit with this turn's deploy row, or removed, is skipped). |
| The release | `JumpEngine::returnToHyperspace`, moved out of `DeploymentGamePhase::releaseUnplacedReinforcements` (which now calls it), so the two ways a unit can fail to arrive cannot disagree. Gate berths and held-exit berths kept, as before. |
| No phase for a wave | `FireGamePhase::advance`'s `hasReinforcementsArriving` clause deleted. `TacGamedata::hasReinforcementsArriving` kept: no game caller any more, but the Stage 7/8 harnesses probe the stamp with it. |
| The mixed case | A slot that also has a real placement still gets its phase. `DeploymentGamePhase::validateDeployment` skips an arrival whose server-side ship already has this turn's deploy row: the client posts every row dated this turn, so without it the committed row was **re-inserted**. The client's `autoPlaceArrivingReinforcements` skips a unit already aboard (`removed`), and the phase −1 commit warning no longer names server-placed or docked arrivals as "could not be brought out". |
| Ship window (user) | `bindShipWindowGestures` on both dialogs: `contextmenu` on any `.reinforcementRow[data-shipid]`, click on `.reinforcementRowInfo` (the ⓘ, hidden until hover, always shown on coarse pointers, as `.fleetlist .rowinfo`). ⚠️ The ⓘ click `preventDefault`s: the menu rows are `<label>`s, and without it opening a window would also pick the row. |
| Docs | `faq.php`: "Calling them in" (+ⓘ), a new "How they come out" bullet, "Arriving" (no phase; hangar), the Shadows bullet. |

**⚠️ Things §7 had wrong or did not list:**

- **The seam** — above.
- **"`HangarBerth` note on the opener's jump engine"** — a jump engine is the one host whose loader misreads an unknown note. And a gate's engine belongs to whoever bought the gate, possibly the enemy, with anybody's riders on it. Both halves of the order now live in one note on the rider.
- **"Verify `hasDeployMoveThisTurn` fires against a server-written row"** — it does on the client. The real hole was the SERVER: `validateDeployment` would have re-inserted the posted copy, and its `if ($found) throw` fires on any row after a deploy.
- **"`releaseUnplacedReinforcements` must not run twice"** — it reads `$gamedata->ships` (the real load), so a placed arrival has its deploy row and a docked one is `removed`. Both are skipped. Confirmed; nothing needed.
- **An opener's forged carrier claim barred it from carrying its own riders** (caught by the new harness). The no-nesting rule counted it as "wanting a hangar" although its claim is ignored. The opener is excluded now.

**⭐ Observations, NOT changed (scope):**

- ~~**An arriving HYACH reinforcement can no longer choose its Specialists.**~~ RESOLVED in the follow-up below: its slot gets a Deployment phase on the arrival turn (user ruling).
- ~~**Every unit's `deploy` row is inserted TWICE**~~ — FIXED in the follow-up below (user: "ok to fix if we are sure it's a bug").
- **Balance, as §7 H6a warned**: a wave that chose a speed of 5+ no longer takes the −50 initiative for speed 0 on its arrival turn. The arrival *penalty* (scatter/facing) is unchanged; `getArrivalScatter` reads the unit's `arrivalTurn`, not the load.

**Harnesses** (gitignored, local only):
- NEW `tests/replay/arrivalPlacementHarness.php` — **75**, real ship files (Primus 14-box hangar, Sentri, Traveler + MapmakerProbes + Pathfinder), constructor-less `TacGamedata`, a `DBManager` stub recording every write. The note (host, clamp both ways, latest wins, both loaders ignore it); onto the map (hex, facing+heading, speed, default speed, in-memory push → `getSpeed`, idempotency, the rule gate); doorway gone; hangar (docked, snapshot persisted at phase −1, a third flight refused and sent back, a carrier sent back takes its passenger, another doorway's or another player's carrier refused); legacy (aboard with no note, ships first); the mixed case in both directions; `persistArrivalOrders` (clamp, cumulative, co-rider host, no nesting, wrong doorway, silence, legacy forced); the POST whitelist (and the claim never json-encoded); the two seams. **Self-tested by deletion seven ways**: the validateDeployment skip, the in-memory push, the legacy host, ships-first, the cumulative reserve, the opener exclusion, the changeTurn call. Each fails 1–3, or fatals.
- NEW `tests/replay/arrivalManifestClientHarness.js` — **26**, `node`, over the real `hangarShared.js`, `DeploymentDock.js` and `ReinforcementEntry.js`: the planner (list order, cumulative, a second carrier, un-ticking evicts, no nesting, a gate's doorway, legacy hosts only itself even when told otherwise, the "pretend" question), `arrivalSpeedOf`, un-booking clears speed and hangar, the POST branch. **Self-tested by deletion four ways.**
- `reinforcementsStage7Harness.php` **82 ⇒ 84**. Its "off the doorway is refused" check ran on a server ship its own previous call had just pushed a deploy row onto, which under H6c reads as "already placed — ignore the posted copy". The row is stripped for that check, and the H6 direction is asserted beside it: once placed, a tampered row is ignored, no throw.
- Unchanged and identical to the H5 run: Stage 6 180/6, 8 130, 9 132, legacy recharge 36, held exit 41, recharge boost 108, Vorlon upkeep 120, hyperspace log 12, Walkers 13 46/13, 15 87/5, 16 141, 19 142, 20 137/1, Vortex Disruptor 58. Client: held exit 29, recharge boost 49, Vorlon upkeep 65, hyperspace log 13, gate ticker 13, Vortex Disruptor 30, Walkers 15/16/19/20 green. Stage 8/9 client ("export marker not found"), `initialOrdersTooltipHarness.js` 4/10 and Walkers 13 client (a circular-JSON TypeError) fail **identically with the four client files stashed**.

**Gate** (`fvbuild.ps1 -Check`): autoload **up to date**, validator **0 new**, replay **123 passed / 2 failed / 3 skipped**. The two are **4297** and **4349**, byte-identical with `source/server` stashed (the known re-records). The skips are 4348 (advanced), and **3955 / 4189, no longer in the local database** — that, not code, is the 124 ⇒ 123. `php -l` clean on all seven server files and `faq.php`; `node --check` clean on the four client files; legacy bundles rebuilt (`yarn build:legacy`); statics unaffected (no public property, no tooltip change); `UI.bundle.js` untouched.

**Not verified here, needs a play test:** declare an exit from hyperspace. The manifest now opens with the opener's own Speed row even alone. Name a wave with speeds and tick **Hangar** on a fighter flight riding a carrier (the opener, or a second carrier riding with it). Commit. Next turn there must be **no Deployment phase**: the wave stands on the doorway at the chosen speeds, the flight is docked (fleet list: scrolls to its carrier), and `tac_individual_notes` holds one `ArrivalOrder` per rider on the manifest turn plus the carrier's `hangarUsage` at phase −1. Initiative should reflect the chosen speeds. A legacy opener (Shadow / Traveler) should arrive with its fighters aboard and no phase. Then a slot with BOTH a late deployment and an arrival on the same turn: the phase appears, the arrival is already placed, and the commit carries exactly one new deploy row for it. Right-click and the ⓘ on the menu rows should open ship windows, the reinforcement in hyperspace included.

#### H6 follow-up — 2026-09-19 (user review of the build)

**1. Hyach Specialists (user ruling, final): a slot whose jump-point wave brings a Hyach unit with Specialists
still to choose GETS A DEPLOYMENT PHASE on the arrival turn** — the one arrival that still does. The unit is
already standing on its doorway when the phase opens (placement runs first, from `Manager::changeTurn`); the
phase exists only for the choice.
- ⚠️ **First built as "choose in the arrival turn's Initial Orders" and REVERTED the same day** (user: the
  Initial Orders Specialists — Computer, Power, Repair, Sensor — must be USABLE on the arrival turn, and a
  choice made in Initial Orders is not loaded back until the next phase). A Deployment-phase choice is written
  at phase −1 and is live by Initial Orders. The client selection gates, `getUnusedSpecialists`, the commented
  Initial Orders commit block and the server's phase-1 path are all back to HEAD byte for byte.
- Server: `TacGamedata::hasArrivingSpecialistChoices($slotid, $turn)` — per SLOT (a player's other slots have
  nothing to do), a `reinforcement` whose `arrivalTurn` is `$turn`, alive, with a `HyachSpecialists` system
  (`getSystemByName` — ⚠️ never `getHyachSpecialists()`, which reads a property only Hyach hulls set, and the
  global error handler rethrows the warning) and nothing chosen. `FireGamePhase::advance`'s slot loop asks it
  of `$servergamedata` beside the other clauses.
- **Nothing else needed changing, and that is H6's mixed case paying off**: a jump-point arrival's placement
  turn IS its arrival turn, so the unchanged "placement turn && phase −1" gates open selection to it and the
  unchanged Deployment commit block (`getUnusedSpecialists`) holds the player to it; `validateDeployment`
  ignores its posted deploy row, `autoPlaceArrivingReinforcements` skips it, and the phase's commit button arms
  because an arrival is an optional placement. One cosmetic touch: the header counts server-placed arrivals, so
  such a phase reads **DEPLOYMENT: REINFORCEMENTS**.
- ⚠️ Worth knowing from the reverted attempt: `HyachSpecialists.canSelectAnything` never terminates on a system
  OUTSIDE its selection window whose `availableSpec` is empty (`nextCurrClass` walks into `undefined`).
  `getUnusedSpecialists`' placement-turn test is what keeps such systems away from it — do not loosen it.

**2. The duplicate deploy row, FIXED.** `DBManager::submitMovement` skips a `deploy` row that already has a
database id: it can only be the echo of a committed row. Every deploy row in the server is written through
`insertMovement` / `Manager::insertSingleMovement` (checked: Deployment, H6 placement, the ten HangarOps
spawns, the mines, the vortex), none through `submitMovement`, whose every caller passes either the POST or
brand-new server rows (id −1 / 0). Recorded games keep their duplicates; nothing reads the count, and games
with a single deploy row (4317, 4319, 4349) were already normal.

**3. The manifest's Hangar control is a DROPDOWN of carriers, default None (user request).** The player picks
the SHIP; which of its hangars is still the dock's auto-allocation. `planManifestHangars` now takes the
PICK (`chosenCarrier`) instead of a want-a-hangar flag, and checks it — in list order, cumulatively, no
carrier in a carrier, a pick of something that is no host evicted — and returns `hosts`, the dropdown's
candidates. Each option is offered only if picking it evicts nobody (else "(no room)", disabled); a rider
carrying others cannot pick at all. The pick lives in the select's `data-picked`, because `refit()`
rebuilds the options on every change. The server needed nothing: `persistArrivalOrders` always took an
explicit carrier.

**4. Speed = small − / + buttons either side of the box**, the native spinner hidden (`.stepper-input`'s two
resets). A `<span>`, not a `<label>`: a label's control is its first LABELABLE descendant, and a `<button>` is
one, so clicking "Speed" would have clicked the minus. **5. Rows vertically centred** — the manifest row and
its controls `align-items: center` instead of the family's `baseline`, which pinned them to the name's
first line.

**6. An H5 bug, found in the lobby (user report 2026-09-19).** Opening a Vorlon's ship window from the lobby's
Details threw `Cannot read properties of null (reading 'depavailable')`: the system icon asks
`JumpEngine.getVortexIconLoad` → `getHeldVortex` → `shipManager.movement.getMaintainableExitHeldBy`, which
asked `getTurnDeployed` FIRST — and the lobby has no game slot, so `playerManager.getSlotById` is null (the
JUMP_GATES_PLAN.md trap 11 shape, which §3 of this plan also warned about). It now looks for the exit first
(there is never one in a lobby) and asks whether the ship is on the board only once one exists. Reproduced in
node against HEAD's `movement.js` (throws) and the fix (null); held-exit client harness unchanged at 29.

**Harnesses:** `arrivalPlacementHarness.php` 75 ⇒ **86** (§10 `hasArrivingSpecialistChoices` in both
directions — other turn, other slot, no Specialists, a late-slot Hyach, already chosen, still in hyperspace —
the Deployment path writing the choice, Initial Orders still writing none, and the `FireGamePhase` clause;
§11 the echo skip through the real `submitMovement`); `arrivalManifestClientHarness.js` 26 ⇒ **37** (§1
rewritten for picks, incl. "the pick wins over first fit"; §5 a jump arrival and a late-slot ship both
choosing in Deployment, held by the commit block until they have, and the choice USABLE in the arrival
turn's Initial Orders). **Self-tested by deletion**: the `FireGamePhase` clause, the Specialists test, the
already-chosen test and the per-slot filter each fail 1 (the reverted attempt's five also failed or hung,
before it was reverted), plus the echo skip. All other harnesses and the gate unchanged: 123 / 2 (4297, 4349)
/ 3. `yarn build` run.

---

### Stage H5 — maintaining a blue doorway (Item 3) — BUILT 2026-09-19

From its **arrival turn** a ship's blue exit is held **exactly as an entrance is**: Maintain on the Jump
Engine, the range test, the four-turn cap, the all-systems-dark rule — and on a **Vorlon** the upkeep with
**no cap** (user, 2026-09-19: *"Vorlons … should act in the same way as Younger Races in that regard,
albeit without the 4 turn limit since they can maintain a jump point so long as they have power to draw
from the capacitor"*). Every turn it is held it brings **another wave** through, like a gate's.

**Four rulings made at build time (user, 2026-09-19)** — none was in the plan:

| # | Question | Ruling |
|---|---|---|
| H5-1 | Does maintaining an exit roll for jump failure? | **Yes, on every Maintain turn.** The forming turn stays exempt (the opener is in hyperspace). The old "an exit never rolls" was written when an exit could not be maintained — its own reasoning was *"on N+1 it was opened on N and has no Maintain"*. |
| H5-2 | Does a later wave take the arrival initiative penalty? | **First wave only.** A later wave comes out of a doorway that has already formed, as through a gate. |
| H5-3 | Does an unplaced rider keep its berth on a held ship exit? | **Yes, like a gate berth** — optimistically; the two authorities below clear it if the exit is not held. |
| H5-4 | Does a Vorlon pay to FORM its exit? | **Yes — reverses H3's free exit.** Forming is using the drive, whichever way the doorway faces. |

**What was built, and where:**

| Piece | Site |
|---|---|
| The closure rule | `getVortexClosureReason`'s exit branch: forming turn → null (a Vorlon pays here, H5-4, and an unpaid one closes on its forming turn = "never forms"); a `SpawnJumpPointPhaseIn` or an opener still in hyperspace → the old one-shot `'no longer maintained'`; otherwise **fall through to the ship list**. |
| One shared predicate | `JumpEngine::holdsMaintainableExit` + `getHeldExitEngine($unit, $gd, $turn)` — a formed, open, non-phase-in, non-gate exit whose opener is ON THE BOARD. Client twin `shipManager.movement.getMaintainableExitHeldBy`. |
| Submit legality | `Firing::getVortexDeclarationBlock`'s Maintain arm: the blanket exit refusal narrows to a phase-in doorway and an opener still in hyperspace. |
| The next wave, IO | `InitialOrdersGamePhase::collectHeldExitOpeners` — an own on-board unit holding its exit **with THIS turn's Maintain in the same POST** is an opener. No Maintain ⇒ every berth on it is written NULL. **The server is the authority.** |
| The next wave, end of turn | `stampExitManifests`' ship loop admits an on-board holder (`getHeldExitEngine`), so a doorway that survived `closeExpiredVortices` stamps its riders for next turn, and one that did not refunds them. |
| Unplaced riders (H5-3) | `DeploymentGamePhase::releaseUnplacedReinforcements` keeps a berth whose opener holds an exit. |
| Jump failure (H5-1) | `rollVortexJumpFailure`: the exit exemption applies only with no Maintain declaration — i.e. the forming turn. |
| First wave only (H5-2) | `getArrivalScatter` answers null unless the unit's `arrivalTurn == vortexOpenTurn + 1`. Compared on the UNIT's turn, not the loaded one, so it cannot depend on which side of the turn boundary initiative asks from. |
| H4's note | `getCertainCloseTurn` narrowed to `SpawnJumpPointPhaseIn` (`$vortexIsExit` → `$vortexIsPhaseIn`), as the H4 follow-up asked. A Vorlon arriving through its exit is no longer offered charging on the arrival turn — it may maintain. |
| Client: Maintain | `JumpEngine.getHeldVortex` asks `getVortexHeldBy` (entrance) **then** the held exit, so the toggle, its carry-forward, the icon fallback and `SystemInfoButtons`' "holding a vortex" are right unchanged. |
| Client: Vorlon forming charge (H5-4) | `JumpEngine.isUsingVortexThisTurn` counts `'jumpexit'` too; the H3 add-back in `PowerCapacitor.doIndividualNotesTransfer` puts it back for the server to take, unchanged. |
| Client: Maintain OFF cancels the wave | `JumpEngine.releaseHeldExitManifest` (from `doDeactivate`) → `ReinforcementEntry.releaseManifest`, with a `confirm.warning` naming who was taken off. Feedback, not enforcement. |
| Client: Manage Reinforcements | A third row kind, `heldExitRow`, beside the gates: **Select Reinforcements** tagged OPEN while Maintain is on; greyed "Maintain the jump point to bring a wave" while it is off; greyed "jump point closes this turn" when it cannot be maintained. Not auto-maintained from the menu — on a young race Maintain takes the ship dark. `ridingOut`, `strandedByCommit` (silent while a held exit takes a wave), the one-candidate shortcut and the manifest dialog's wording all know it. |
| Client: commit warning | `gamedata.js`'s "JUMP POINTS … will CLOSE" list includes an unmaintained held exit. |
| Client: map | `BallisticIconContainer`: a Maintain on a blue exit draws **blue** (`hexBlue`, `#00b8e6`), per the yellow-leaving / blue-arriving rule. |
| Docs | `faq.php` (arriving, the penalty line), `factions-tiers.php` (Vorlon Jump Drive: exits). The Jump Engine tooltip was left alone — it points at the FAQ, and a change there regenerates every faction's statics. |

**⚠️ Things §6 had wrong or did not list:**

- **§6's code snippet was stale.** The exit branch returned `'no longer maintained'`, not `'reinforcements have arrived'`.
- **"`getHeldVortex` resolves the vortex by id, so it already finds a blue one" — FALSE.** It went through
  `getVortexHeldBy`, which is entrance-only by design (`isJumpVortex`; its callers disagree on the verdict).
  Widening that would have flipped `canJumpOut` and friends. The exit is asked **beside** it, second.
- **The ship sweep does not belong in `collectGateExits`** (§6's table). `stampExitManifests` already has a
  ship loop; it was gated on `isReinforcement()`, and widening that one line is the whole change.
  `collectGateExits` stays gates-only.
- **The Maintain marker was yellow on a blue doorway** — not in §6.
- **The jump-failure exemption keyed on the vortex class, not the turn** — invisible until an exit could
  carry a Maintain.
- **`reinforcementsStage9Harness.php` built an impossible engine** (a vortex id and a scatter, no open turn —
  `restoreVortexState` always sets both). H5-2 reads the open turn, so the fixture now sets it.

**Harnesses** (gitignored, local only):
- NEW `tests/replay/heldExitHarness.php` — **41**, real ship files (Primus, Vorlon Heavy Cruiser), a
  constructor-less real `TacGamedata`. Closure in both directions (dark rule non-vacuous, cap, range,
  released opener, phase-in, Vorlon no-cap + R6), the forming-turn charge read off the capacitor's own
  counter, the jump-failure roll with a 100%-failure drive (so "did it roll" is exact), submit legality, the
  manifest openers and the end-of-turn stamp. **Self-tested by deletion six ways** (exit branch reverted; the
  forming charge; the roll's Maintain test; the stamp widening; the manifest's Maintain test; the Firing
  refusal) — each fails 1–12.
- NEW `tests/replay/heldExitClientHarness.js` — **29**, `node`, over the real `movement.js`, `baseSystems.js`
  and `ReinforcementEntry.js`. Self-tested by deletion five ways.
- **Inverted, not deleted:** `reinforcementsStage7Harness.php` (the stayer now KEEPS its berth, + a control
  where its opener went back to hyperspace: 80 ⇒ 82), `rechargeBoostHarness.php` (a Vorlon arriving through
  its exit is no longer offered charging), `vorlonUpkeepClientHarness.js` (an exit IS charged, + the
  add-back for it: 63 ⇒ 65). `reinforcementsStage9Harness.php` gains the second-wave assertions (123 ⇒ 132).
- Unchanged and identical to the pre-H5 run, failures included: Stage 6 (180/6, the H1 rename), Walkers 13
  (46/13), 15 (87/5), 20 (137/1); the Stage 8/9 / Walkers 13 client harnesses still crash on
  "export marker not found", and `initialOrdersTooltipHarness.js` is still 4/10 — all as at HEAD.

**Gate** (`fvbuild.ps1 -Check`): autoload **up to date**, validator **0 new** (237 baselined), replay
**124 passed / 2 failed / 2 skipped**. No failure is H5's: **4349** is the known H4 re-record (phase-in
doorways, `chargeBoostMax` added / counter removed — H5 leaves phase-in doorways certain), and **4297** is a
KellyTrek Constitution whose system list shifted in HEAD commits `452803519` / `6a87ccf40` (2026-09-19 01:00,
after the baseline was recorded 2026-09-18 18:55) — `TrekPhaser → TrekPhaserKellyType7` at index 11, every
later id moved. No ship file is in the H5 diff. `php -l` clean on all four server files; legacy bundles
rebuilt; statics unaffected (no public property, no tooltip change).

**Not verified here, needs a play test:** a young-race opener arrives, turns Maintain ON (the ship goes
dark), Manage Reinforcements lists it with **Select Reinforcements**, a wave is named; next turn that wave
gets a Deployment phase through the same doorway with **no** arrival penalty. Turning Maintain OFF after
naming the wave should warn and un-book it. On the cap turn the toggle vanishes and the row greys. A
Vorlon should show its drive's power reserved on the turn it declares the exit from hyperspace, and
`tac_individual_notes` should show that turn's phase-4 `powerStored` exactly that much lower.

#### H5 follow-up — 2026-09-19 (play test 4367)

- **Manage Reinforcements now follows Maintain while it is open.** The dialog is not modal, and toggling
  Maintain in the ship window left the held-exit row stale until the menu was reopened. The open dialog
  registers its own re-render (`ReinforcementEntry.refreshMenu`, keeping the selected row), and
  `InitialPhaseStrategy.onSystemDataChanged` calls it — Maintain on/off and a drive powered down all
  raise that event. "Still open" is asked of the DOM, because every close path is a bare `e.remove()`.
- **The Vorlon Jump Engine's yellow REACH ARC stayed on the map into DEPLOYMENT: REINFORCEMENTS** until a
  system was hovered (not the blue Maintain marker — the first report said "ballistic icon"). Not an H5
  bug, and not jump-engine specific: `PhaseStrategy.deactivate` sets `inactive` and THEN deselects, so the
  deselect's `SystemDataChanged` is dropped by `PhaseDirector.relayEvent`, and its only other arc sweep
  sits inside `hideSystemInfo(true)` behind "is an info panel open". A SELECTED arc-when-selected system (a
  Jump Engine clicked in Initial Orders) therefore kept its arc through every later phase until the next
  hover. `deactivate` now clears `hoveredArcSystem` and every icon's weapon arcs unconditionally.
  Reproduced against the real `PhaseStrategy.js` in node before the fix, clean after.

---

### Stage H4 follow-up — the Vorlon charge cost, and boosting on the arrival turn — 2026-09-18

Two user reports from play tests **4348** and **4349**.

**1. "3 levels should cost 18 but cost 22, and turn 5 opens on 32 with only 14 recharge" (4348, Vorlon
Heavy Cruiser: capacitor 32, recharge 14, drive 6).** Two bugs, and the second is a RULING that reverses
the H3 follow-up's "economy unchanged":
- **The "22" was a double subtraction after the commit.** The capacitor's stored figure is net of this
  turn's boosts once committed, so in Movement onwards `PowerCapacitor.initializationUpdate` adds each
  system's `countBoostPowerUsed` back. The charge cost was charged beside the abduction draw instead,
  so nothing added it back: 28 − 18 = 10 displayed. Fix: `countBoostPowerUsed` now returns
  `getChargeBoostPowerDraw()` for a jump engine (0 for the jump, which stays free), and the separate
  line in `getReactorPower` is gone. ⭐ **A per-level cost on a boost belongs in `countBoostPowerUsed`**
  — it is the only thing the capacitor's post-commit add-back reads.
- **⭐⭐ THE CAPACITOR NOW BANKS WHAT IS LEFT (user ruling: "32 − 18 on turn 4, then 14 + 14 = 28 at the
  start of turn 5").** `PowerCapacitor.doIndividualNotesTransfer` used to post
  `balance − topUp + recharge`, i.e. the pre-H3 `min(stored − allocations + recharge, max)`, which on a
  full or nearly full capacitor let Initial Orders spending up to the recharge cost nothing (32 − 18
  banked 28, and the top-up then opened turn 5 on 32). It now posts the balance as displayed, which
  already holds this turn's recharge capped at the maximum: `min(stored + recharge, max) − allocations`
  (+ the H3 upkeep reservation, unchanged). **This is a Vorlon-wide economy change, not an H4-only
  one** — every Initial Orders allocation on a full capacitor now costs in full. The two rules agree
  whenever the recharge fits under the cap, which is why the H3 play test never told them apart.
  `vorlonUpkeepClientHarness.js` §6 asserted the old rule and now asserts this one, with two cases where
  they differ.
- ⚠️ Game 4348 already banked 28 on turn 4, so ITS turn 5 still opens on 32. From turn 5 on it banks
  correctly; a fresh game shows the whole sequence.

**2. "The Traveler and Mastership can't boost their Jump Engines" (4349, both phased in on turn 2).**
On the arrival turn the phase-in doorway still stands — its closure is only written at the end of that
turn — so `getChargeBoostMax` answered 0 and the drive offered no boost at all. But a one-shot doorway
IN closes at the end of the arrival turn whatever anybody declares. So:
- `getCertainCloseTurn()`: the recorded closure, or — for a ship-held `SpawnJumpPointExit`
  (`SpawnJumpPointPhaseIn` included), known from a new protected `$vortexIsExit` set by
  `restoreVortexState` — `openTurn + 1`. Any other standing jump point may yet be maintained and answers
  null. ⚠️ **H5 makes ship-held blue exits maintainable: narrow this to `SpawnJumpPointPhaseIn` then.**
- `getChargeBoostMax` offers from the closing turn on (`$turn >= closeTurn`), not only after it.
- The walk now starts AT the closing turn with 0, each turn adding `1 + boost(t)`. With no boosts that
  is still exactly `turn − closeTurn` (identity grid unchanged); what changes is that a boost ON the
  closing turn is credited — the turn after arrival reads 1 + levels.

**3. "The Traveler and Mastership's drive shows 1/4 on the arrival turn" (4349).** The phase-in doorway
still stands on that turn, so `stripForJson` sent the Maintain counter (`vortexTurnsOpen` 1,
`vortexMaxTurns` 4) and the icon drew it instead of the charge. An Ancient — any legacy drive — holds no
jump point open, so **a legacy drive now sends no vortex counter at all** and the icon falls through to
the charge, 0/N on arrival. This reverses the Stage 9 note that "the vortex counter block is right for
them too". Nothing else reads the counter for a ship drive (`ReinforcementEntry` reads it for GATES
only), and a real entrance held by a drive reverted to legacy mid-game (The System, H4) still draws its
counter through the client's fallback, which derives it from the vortex unit and finds entrances only.

**Harnesses:** `rechargeBoostClientHarness.js` §6 replays 4348 (42 ⇒ 49); `rechargeBoostHarness.php` §3b
replays 4349 (95 ⇒ 108, the last three for item 3). All four fixes self-tested by deletion. Replay harness **125 passed / 1 failed
/ 2 skipped** against the corpus as you re-recorded it: the one failure is **4349**, whose only diff is
`chargeBoostMax: added (3)` on the Mastership's and Traveler's drives — this fix, intended. Legacy
bundles rebuilt; statics unaffected (no public property or tooltip change).

---

### Stage H4 — boosting the recharge (Item 2) — BUILT 2026-09-18

Built to §5's design — R2 (one boost carrier, disambiguated by the charge at the START of the turn),
R3 (boost N ⇒ N+1 turns, at most 3 levels), A4 (no use at all until charged, jumping out included)
and A2 (The System's five drives `markLegacy()`, in this stage as §10 asked). **Five things §5 did not
have**, two of which would have been silent in play:

**⚠️⚠️ 1. THE BOOST ROWS ARE NOT THERE TO WALK.** §5 derives the load from each turn's boost, but
`DBManager::getPowerForShips` loads `tac_power` for **this turn and last turn only** — a boost bought
three turns ago simply does not exist on a loaded ship. So the credit is **persisted**: a
`'ChargeBoost'` IndividualNote on the drive (turn ⇒ levels), written by
`JumpEngine::generateIndividualNotes` at **phase 4** (`FireGamePhase::advance`, the one note hook that
runs on a real reload with notes loaded and this turn's power present), restored into
`$chargeBoostNotes` by `onIndividualNotesLoaded`. The EDJD's abduction record is the same shape of
answer to the same problem.
- ⭐ **The server decides what the boost was worth**: the note is `min(boost, getChargeBoostMax())`, so a
  forged row cannot buy more than 3 levels or charge that would not count, and a CHARGED drive's boost
  (a jump) writes nothing. Offline / destroyed drives write nothing. Idempotent on a double advance.
- ⚠️ **The note must be CLAIMED on load** — the fall-through at the foot of `onIndividualNotesLoaded`
  takes any unknown note for the pre-jump combat value (the trap the scatter and EDJD notes already
  record). Asserted.

**⚠️ 2. §5's WALK WAS OFF BY ONE.** As written it answers **0** on the turn after the closure, where the
closed form answers 1, so it would have moved every recharging drive in the corpus. Built as: 1 on
`closeTurn + 1`, then `+1 + boost(t)` for each `t` strictly before the query turn — and with **no**
notes the closed form itself is returned untouched. `rechargeBoostHarness.php` asserts the no-boost
walk equals the old closed form over a 175-cell grid.

**⚠️⚠️ 3. `copyLastTurnPower` WOULD HAVE TURNED A CHARGE BOOST INTO A JUMP (not in §5).** It repeats
EVERY power row into the new turn, boosts included — and on an Ancient-charging drive a charge boost
carried onto the turn the drive finishes charging **is Jump to Hyperspace**: the ship leaves the battle
on a click made for something else. `JumpEngine.getRepeatableBoost` now decides: carried (clamped)
while still charging, **dropped** otherwise; every other system copies exactly as before.

**4. A second payload key, `chargeBoostMax`.** `advancedCharging` alone cannot tell the client whether a
boost buys anything: `turnsloaded` is the MIN of the jump-point recharge and the EDJD abduction
cooldown (the latter is not chargeable, §5 trap), and it is 0 while a jump point stands (where the
charge does not move). So the server publishes `getChargeBoostMax($turn)` — non-zero only while the
jump-point recharge is actually running, capped at 3 **and at what finishes the charge by next turn**
(at 7/8 the drive is full next turn unboosted, so a boost would be power for nothing and is not
offered). Flights never get one.

**5. `hasMaxBoost` did not need changing.** §5 reads it as "is at max"; `power.clickPlus` uses it as
"has a cap" and then tests `maxBoostLevel`. It stays `true`, and `boostable` / `maxBoostLevel` are set
**per instance** in the client constructor from the two keys (the shape `AmmoMissileRackTriad` already
uses; primitives, so no shared-reference trap):

| start-of-turn state | `boostable` | `maxBoostLevel` | control |
|---|---|---|---|
| recharging, `chargeBoostMax` sent | true | `chargeBoostMax` | **Extra Charging** `- N +` |
| recharging, nothing to buy | **false** | — | none: it cannot jump (A4) and cannot charge |
| charged | blueprint (true on legacy, false on a Vorlon) | 1 | Jump to Hyperspace Yes/No (legacy only) |

**Who charges the Ancient way** — `JumpEngine::hasAdvancedCharging()`: unit `factionAge >= 3`, a real
jump recharge (not the Trek Nacelle), not a gate. That is Vorlons, The System, Shadows, Kirishiac,
Mindriders, Torvalus, Triad, Thirdspace and the Walkers. Young-race drives — including every BSG / Star
Wars / Trek legacy drive — keep boost-means-jump unconditionally.
- ⚠️ **Fighter flights (the Mapmakers): the A4 gate applies, charging does not.** The gate reads the
  charge off the flight's vortex-holding engine (`getChargeSource()` → `getFlightJumpEngine`, the same
  routing `stripForJson` uses), so a Mapmaker that phased in cannot jump out for its 10-turn delay and
  has no way to speed it up. That is A4 read literally; if Mapmakers should be exempt, the one line is
  `if ($this->isFlightMounted()) return true;` near the top of `isJumpOutBoost`, plus its client mirror
  in `isChargedForJump`.
- A charge boost also withdraws the drive's abduction order, as a jump boost does: `canSelectForAbduction`
  already refuses on ANY boost, so the drive does one thing a turn either way.

**The gate, both ends.** Server: `JumpEngine::isJumpOutBoost($turn)` = legacy **and** boosted **and**
(young-race **or** fully charged), and `getUnitJumpingEngine` asks it instead of the bare
`isOverloading`. That one change moves the end-of-Fire sweep, `withdrawFireFromJumpingUnits`,
`automateIntercept`'s `isJumpingUnarmed` and `EdjdAbduction::isDriveWorking`.
`InitialOrdersGamePhase::dropFireOfJumpingShip` now asks the **DB copy** (`$gd`, reloaded after
`submitPower`, so it has this POST's boost AND the notes) — the POST-side ship reads every drive as
charged and would have dropped a charging Ancient's orders as a jump; asserted in both directions.
Client: `JumpEngine.isJumpBoost()` behind `movement.getJumpingOutEngine` (so `isJumpingToHyperspace`
and `isJumpFireForbidden`), the `jumping[]` commit checklist in `gamedata.js`, `onBoostIncrease` (fire
orders and the flight mirror only for a jump) and the "JUMP" read-out (a charge boost keeps the
`N/M` counter).

**The cost** is `JumpEngine.getChargeBoostPowerDraw()` = levels × `powerReq`, in `getReactorPower` beside
the abduction draw. ⭐ **One payer, the front end:** there is no server draw, so on a Vorlon it is spent
by the capacitor storing less at the commit — and deliberately NOT in `getVortexUpkeepReserved`'s
add-back (asserted). `boostEfficiency` stays 0, so the JUMP is still free.

**Files:**

| File | Change |
|---|---|
| [baseSystems.php](source/server/model/systems/baseSystems.php) | `$chargeBoostNotes`, `CHARGE_BOOST_MAX`, `CHARGE_BOOST_NOTE`; `hasAdvancedCharging`, `getChargeSource`, `getChargeBoostMax`, `getRechargeBoostLevel`, `isJumpOutBoost`; `getUnitJumpingEngine` on it; the walk in `getJumpPointRechargeLoad`; note claim + `generateIndividualNotes` (phase 4); `stripForJson` (`advancedCharging`, `chargeBoostMax`); `getAdvancedChargingText` in all three tooltip branches |
| [InitialOrdersGamePhase.php](source/server/Phase/InitialOrdersGamePhase.php) | `dropFireOfJumpingShip` asks `$gd` |
| [firing.php](source/server/handlers/firing.php) | comment only — the "NOT narrowed to isLegacyJump()" note was no longer true |
| 5 System ship files | `(new JumpEngine(...))->markLegacy()` (A2) |
| [model/system/baseSystems.js](source/public/client/model/system/baseSystems.js) | per-instance `boostable` / `maxBoostLevel`; `hasAdvancedCharging`, `getChargeBoostMax`, `isChargedForJump`, `isChargeBoost`, `isJumpBoost`, `getChargeBoostPowerDraw`, `getRepeatableBoost`; `initializationUpdate`, `onBoostIncrease` |
| [power.js](source/public/client/power.js) | the draw in `getReactorPower`; the filter in `copyLastTurnPower` |
| [movement.js](source/public/client/movement.js) | `getJumpingOutEngine` → `isJumpBoost` |
| [gamedata.js](source/public/client/gamedata.js) | `jumping[]` commit checklist → `isJumpBoost` |
| [SystemPowerSettings.js](source/public/client/UI/reactJs/system/SystemPowerSettings.js) | "Extra Charging" stepper while charging |

**Harnesses added** (both in `tests/`, which is gitignored — local only, like every other harness):
- `tests/replay/rechargeBoostHarness.php` — **95 assertions**, real ship files, notes through the real
  `onIndividualNotesLoaded`. The identity grid, the hand-worked boosted table, rule 2 (a boost never
  moves its own turn), `getChargeBoostMax` in every state, `isJumpOutBoost` / `getUnitJumpingEngine`
  in BOTH directions (boosted recharging Vorlon ⇒ nobody leaves; boosted charged Ancient ⇒ it does),
  the note (phase, clamp, idempotency, claim), the payload, the tooltip, and the POST-side trap.
  Self-tested by deletion four ways (bare `isOverloading` back in the sweep; no phase guard; credit
  within the turn; note not claimed) — each fails 3-11 assertions.
- `tests/replay/rechargeBoostClientHarness.js` — **42 assertions**, `node`, over the real `power.js`,
  `movement.js` and `baseSystems.js`. Self-tested by deletion three ways (withdrawal on any boost; the
  `copyLastTurnPower` filter; `getJumpingOutEngine` on a bare boost).
- `legacyRechargeHarness.php` **extended** (33 ⇒ 36): the same Shadow drive boosted and unboosted.
- `SystemPowerSettings.js` verified without touching `UI.bundle.js`: esbuild bundle + `renderToString`
  in four states (charging stepper, charged Yes/No, a pre-H4 payload, the reactor).

**Gate:** ship-data validator **0 new** (237, all baselined). Replay harness **73 passed / 53 failed / 4
skipped**, and every failure was read by SHAPE with no diff limit: **no `turnsloaded` path anywhere**
(the corpus holds no boosts, as §9 predicted); 51 games gain only `advancedCharging: added (true)`, game
4350 also `chargeBoostMax: added (1)` on one recharging drive, and **4302** is the stale playtest game —
its diff is **byte-identical** with the H4 server files swapped back to HEAD, apart from the new key.
⚠️ **NOT re-recorded**: the merge-record (back up, `record --games=<the 52>`, merge the other 78
manifest entries back) was blocked by the session's permission guard. Do it by hand, or run a full
`record` and restore `baseline/game_4302/` as H3 did.
⚠️ **Autoload check: STALE, and not H4's** — `TrekPhaserKellyType7` (commit `827326597`, "Update
customTrek.php") is not in the map. `fvbuild.ps1 -Autoload` fixes it.
Other harnesses unchanged from their H3 state (Stage 6 still 180/6 on the H1 rename; the two Stage 8/9
client harnesses and `initialOrdersTooltipHarness.js` fail identically at HEAD). `php -l` clean on all
eight server files; legacy bundles rebuilt (`yarn build:legacy`); statics regenerated — the RECHARGE
tooltip reaches exactly the ten age-3+ faction files, and `advancedCharging` / `chargeBoostNotes` reach
none (payload-only / protected).

**Not verified here, and it needs a play test:** a Vorlon that closes a jump point should, next turn,
offer **Extra Charging** `- 0 +` (up to 3) and lose 5–8 power per level; the turn after, its charge
should read 1 + 1 + levels higher, and its fire orders must survive the boost. A Shadow/Kirishiac that
phased in should show NO boost row until charged (or while it can buy nothing), and then Jump to
Hyperspace as before. Set a charge boost, commit, and check next turn that it carried (while
charging) or vanished (once charged) — and check `tac_individual_notes` for the `ChargeBoost` row at
phase 4.

---

### Stage H3 follow-up — the recharge display and carrying Maintain forward — BUILT 2026-09-18

Two user reports from play test **4348** (a Vorlon Heavy Cruiser: capacitor 32, recharge 14, drive 6).

**1. "Turn 2 opens on 26 instead of 32, and so does turn 3."** The capacitor arithmetic was right —
`tac_individual_notes` for the hull reads 32 → (open) 26 → 32 → (maintain) 26 → 32 at every commit,
exactly 6 per used turn — but **the capacitor refills at the COMMIT of Initial Orders, not at its
start.** `PowerCapacitor.doIndividualNotesTransfer` posts `balance + regeneration` (the 2021 "Power
Capacitor rework", which moved the recharge there out of the Initial Orders display). So for the whole
of Initial Orders the reactor showed **last turn's leftovers**, and anything drawn at the end of a turn
read as missing at the start of the next one. Not new with H3: a Vorlon that fired weapons has always
opened the next Initial Orders on the drained figure. H3 only made it every turn and very visible.

The fix shows the recharge from the start of Initial Orders, matching the rules text ("New power is
produced in the Initial Orders phase"), **without changing what is banked**:

- `PowerCapacitor.getTurnStartTopUp()` = `min(stored + recharge, max) - stored`, never negative, and
  **0** outside Initial Orders or once this turn's recharge is banked. `initializationUpdate` adds it to
  the power the capacitor injects.
- `doIndividualNotesTransfer` **takes it back out** before adding the full recharge, so the committed
  figure is EXACTLY the pre-change `min(stored − allocations + recharge, max)`. A full capacitor still
  absorbs Initial Orders spending up to its recharge for free, as it always has.
- ⭐ **The one genuine widening is the commit gate**: `getCapacitorShipsNegativePower` reads this
  balance, so a player may now allocate this turn's recharge. It cannot drive the stored figure below
  zero — the top-up never exceeds the recharge it is replaced by.
- ⚠️ **Why a server flag.** After a commit the stored figure already includes the recharge, and a
  player who reloads while waiting would see it added twice. `PowerCapacitor::$rechargedThisTurn`
  (protected; published only when true) is set from the same notes that set the stored power: a
  `powerStored` note from THIS turn, phase ≥ 1. The turn-1 Deployment fill is phase −1, so the first
  Initial Orders still tops up. ⚠️ It compares against `$gamedata->turn` at note load, which is the
  CURRENT turn on a live load; a historical `getTacGamedata($turn)` reload reads later turns' notes and
  the game's current turn, so it cannot reproduce a past turn's pre-commit state — the same limitation
  the capacitor's existing `doubled` flag has.

**2. "Maintain should carry on automatically until I turn it off."**
`JumpEngine.continueVortexMaintain()`, swept by `JumpEngine.continueVortexMaintains()` from
`InitialPhaseStrategy.activate` beside `continueAbductions` (after `repeatLastTurnPower`, so a drive left
offline reads offline). `doActivate` was split: `declareVortexMaintain()` makes the order and the
shutdown and draws nothing, so it is safe before the phase strategy activates (the marker is rebuilt from
the fire orders by `ballisticIconContainer.consumeGamedata`).

⭐ **Last turn's Maintain order is not available, and is not needed.** `getFireOrdersForShips` loads ONE
turn, so the mode-7 order cannot be read back. It is derived, exactly: a jump point still open now that
was not opened last turn survived last turn only by being maintained (on any turn after its opening
turn, an undeclared jump point closes). Client-side, `spawned == openTurn + 1`, so the rule is
`gamedata.turn > vortex.spawned`.

- ⚠️ **The first Maintain is always the player's** — on the first open turn nothing is re-declared;
  opening is not a decision to hold.
- **Applies to every vortex-opening drive, not only Vorlons.** Everything else is `canActivate`'s, so an
  ordinary drive stops on its four-turn cap turn exactly as the manual toggle does, and on an ordinary
  ship the re-declaration takes the ship dark again (which `repeatLastTurnPower` has mostly done
  already). If only Vorlons should carry it, the guard is one `chargesVortexUpkeep()` test.
- ⚠️ A page reload during Initial Orders re-seeds it — nothing server-side records the OFF until the
  commit, exactly as for `continueAbduction`.

**Harnesses:** `vorlonUpkeepClientHarness.js` §6 replays the 4348 sequence (opens on 32, maintain → 26,
commit banks 32, next turn opens on 32) and asserts the committed figure equals the pre-change formula
across six states; §7 covers the carry-forward. `vorlonUpkeepHarness.php` §9 covers the server flag.
Both new guards were self-tested by deletion (the take-back line; the first-open-turn rule). **62** and
**120** assertions, all green.

**Gate:** autoload current, validator 0 new, replay **127 passed / 1 failed / 2 skipped**. The failure is
4302 as ever. Six corpus games moved by `rechargedThisTurn: added (true)` on a Vorlon capacitor and
nothing else, and were re-recorded **by merging the manifest** (backed up, recorded the six, merged the
old entries back — see the `--games=` trap below). The two skips are **4348 and 4354**, both played
since they were recorded; left alone, as 4302 is.

---

### Stage H3 — the Vorlon jump-drive upkeep (Item 6) — BUILT 2026-09-18

Built as §4 designed it, with **one piece of arithmetic §4 did not have** — see "the double charge"
below. The eleven Vorlon hulls got their §10 A1 numbers, `markCapacitorFed()` marks the drive, and
R4/R5/R6 all landed where §4 said they would.

**The predicate, and why it is not the flag.** `JumpEngine::chargesVortexUpkeep()` is what every
reader asks — server and client — and it is three questions, not one: the drive is marked, its
`powerReq` is non-zero, **and the hull actually has a `PowerCapacitor` to pay from**. A drive marked
on a hull with no capacitor falls back to the ORDINARY B5 rules (cap, all-systems-dark and all),
which is the only fallback that neither hands it a free indefinite jump point nor closes it every
turn for a reason its owner cannot act on. `stripForJson` publishes `vortexUpkeep` off that same
method, so the two ends cannot disagree about *which* drives the rule covers.

⚠️ **The FITTING and the STATE are separate questions on purpose.** `chargesVortexUpkeep()` asks
"does this hull have a capacitor" (a build fact); `payVortexUpkeep()` asks "can it pay right now"
(play). A capacitor that has just been shot out must read as **cannot pay** — not as "this hull was
never capacitor-fed", which would silently hand the four-turn cap and the dark rule back in the
middle of a game.

**⚠️⚠️ THE DOUBLE CHARGE — the one thing §4 got wrong, and it would have been invisible.**
§4(b) asks for BOTH a client draw in `getReactorPower` AND a server draw through
`canDrawPower` / `doDrawPower`. Those are not independent: `PowerCapacitor.doIndividualNotesTransfer`
posts `getReactorPower(...) + getRegeneration()` as the capacitor's **stored power**
(`powerReceivedFromFrontEnd` → `setPowerHeld`), so subtracting the upkeep there already spends it —
and then the closure sweep spends it again. A Vorlon would have paid twice a turn, and nothing on
screen would have said so.

The split that resolves it is the one the Vorlon economy already uses for **weapon fire**:

| | who | when |
|---|---|---|
| **RESERVATION** | client, `JumpEngine.getVortexUpkeepDraw` off `getReactorPower`, on any turn `isUsingVortexThisTurn()` | Initial Orders — constrains what may be ALLOCATED, and the commit gate (`getCapacitorShipsNegativePower`) names a player who allocated it away |
| **PAYMENT** | server, `JumpEngine::payVortexUpkeep` → `PowerCapacitor::doDrawPower` | end of turn, in the closure sweep |

…plus **one line** in `PowerCapacitor.doIndividualNotesTransfer` that adds the reservation back into
the figure it stores. That line is not a refund: it is what leaves the power IN the capacitor for
the server to take. `tests/replay/vorlonUpkeepClientHarness.js` §3 is its whole guard, and it was
self-tested by deleting the line (stored power then dropped by exactly the upkeep).

⭐ **The draw lands because `closeExpiredVortices` runs BEFORE the `generateIndividualNotes` loop in
`FireGamePhase::advance`** — `doDrawPower` adds to `powerReceivedFromBackEnd`, which the phase-4
capacitor note turns into the stored figure a few lines later. Reverse that order and the upkeep
would be charged to a note that had already been written.

⭐ **Idempotent by inheritance, not by a new guard.** `getVortexClosureReason` has exactly ONE caller,
`closeVortexIfDue`, which is already guarded three ways over (`hasOpenVortex`, a real
`$vortexCloseTurn`, and an open turn not yet reached), so a double `advance()` cannot draw twice.

**Where the three rulings landed:**

| Ruling | Site | Shape |
|---|---|---|
| R4 — upkeep replaces all-systems-dark | `getVortexClosureReason` | the upkeep branch **returns**, so `getVortexPowerViolations` is never reached |
| R5 — no `MAX_VORTEX_TURNS` | `getVortexClosureReason`, `getVortexAge`, `stripForJson`, `JumpEngine.canMaintainVortex`, `getVortexIconLoad` | five sites, and the client's `canMaintainVortex` is the one that matters — without it the toggle vanishes on `spawned+3` and the player loses the control that keeps a paid jump point open |
| R6 — failure to pay closes it | `payVortexUpkeep`, called from both the opening-turn branch and the maintain branch | `'jump point not powered'` |

**⭐⭐ R4 IS INERT ON TODAY'S ELEVEN HULLS, and that was measured rather than assumed.** After H3 the
Jump Engine is the **only** system on any Vorlon hull with a non-zero `powerReq` — exactly what the
ship files have always said ("the only system onboard that does so") — and
`getVortexPowerViolations` skips jump engines. So a Vorlon's violation list was already empty, with
or without R4: **a Vorlon has always been able to hold a jump point open for four turns for free**,
the same vacuous pass §3.12 of WALKERS_OF_SIGMA_PLAN.md caught on a Mapmaker.

That makes the real content of H3 **R5 + R6**: a Vorlon now *pays* 5–8 a turn for a doorway it can
hold *indefinitely*. R4 is still worth stating — a future Vorlon system with a power requirement, or
an enhancement that adds one, would otherwise start closing jump points — but it changes nothing
today. The PHP harness asserts the vacuity directly (first line of its §4) so that the day it stops
being true, something says so.

**⚠️⚠️ AND A SECOND THING THE SAME MEASUREMENT EXPOSED — CORRECTED 2026-09-18 ON THE USER'S RULING.**
Built §4(b) literally, the drive's `powerReq` became a **standing** cost: `MagGravReactorTechnical`
sets `fixedPower`, so `getReactorPower` subtracts every online system's `powerReq`, and the drive is
the only Vorlon system with a number. An idle Vorlon holding **no jump point at all** silently lost
5–8 power every turn.

**The ruling: the jump drive draws power ONLY on a turn it is actually used** — the turn it **opens**
a jump point, and every turn it **maintains** one. Every other turn it costs the ship nothing. So:

- `getReactorPower` **gives back** what `fixedPower` took (guarded on `fixedPower`, inside the online
  branch, so an ordinary-reactor hull gets no gift and an offline drive is not credited twice), then
  subtracts the use;
- `getVortexUpkeepDraw` fires on `JumpEngine.isUsingVortexThisTurn()` — **any `jumppoint` order this
  turn**, opening (modes 1–6) or Maintain (mode 7) — not on `isMaintainingVortex()` alone;
- `getVortexClosureReason` charges the **opening turn** too, in the `$turn == $vortexOpenTurn`
  branch. ⚠️ That branch's existing exemption is from MAINTAIN and from the all-systems-dark rule,
  which is a *different* bill and still applies to everyone; only the power changed.

⭐ **A blue EXIT is deliberately NOT charged, and the two ends agree by construction.** The server's
one-shot exit branch returns near the top of `getVortexClosureReason`, long before the upkeep; the
client tests `damageclass === 'jumppoint'`, and an exit declaration carries `'jumpexit'`. Testing the
damageclass rather than the firing mode is what makes that automatic.

⚠️ A declaration the server later refuses (illegal hex) is **reserved for but never spent** — the
player keeps the power and is out by one turn's reservation. That is the safe direction; reserving
nothing until the doorway exists would let them allocate away the very power the opening needs.

⭐ Guarded by `vorlonUpkeepClientHarness.js` §2, which asserts the idle balance is **unchanged from
pre-H3** and was self-tested by deleting the give-back (idle dropped from 18 to 13).

**Files:**

| File | Change |
|---|---|
| [baseSystems.php](source/server/model/systems/baseSystems.php) | `$vortexUpkeep`, `markCapacitorFed()`, `chargesVortexUpkeep()`, `getVortexUpkeepCost()`, `getUpkeepCapacitor()`, `payVortexUpkeep()`; R4/R5/R6 in `getVortexClosureReason`; `getVortexAge` uncapped; `stripForJson` (`vortexUpkeep` / `vortexUpkeepCost` in, conditional `vortexMaxTurns`); the tooltip |
| 11 Vorlon ship files | the 3rd constructor argument + `->markCapacitorFed()`; the 5th (range 12) untouched |
| [power.js](source/public/client/power.js) | in `getReactorPower`, the `fixedPower` **give-back** plus the per-use draw; `hasVortexUpkeepDrive`, `getVortexUpkeepReserved`; `getVortexMaintainBlockers` and `isVortexLockedOffline` no-op for an upkeep hull |
| [model/system/baseSystems.js](source/public/client/model/system/baseSystems.js) | `chargesVortexUpkeep` / `getVortexUpkeepCost` / `isUsingVortexThisTurn` / `getVortexUpkeepDraw`; open-ended `getVortexIconLoad`; the cap test out of `canMaintainVortex`; `doActivate` / `doDeactivate` guards; **the add-back in `PowerCapacitor.doIndividualNotesTransfer`** |
| [JumpEngineMenu.js](source/public/client/UI/reactJs/system/JumpEngineMenu.js) | the Maintain note text for an upkeep drive |
| [factions-tiers.php](source/public/factions-tiers.php) | a new **Jump Drive** section under VORLON EMPIRE, and the two Mag-Gravitic Reactor bullets that said the drive used no power |

**Traps §4 listed — which mattered and which did not:**

- ✅ **`canMaintainVortex`'s `spawned + 3`** — real, and the highest-consequence line of the client
  half.
- ✅ **`vortexMaxTurns` with no value to send** — done as "emitted only when it means something";
  `getVortexIconLoad` draws `"5"` instead of `"5/4"`. A published denominator still wins, so a gate
  is unaffected.
- ⚪ **`getVortexMaintainBlockers` / `isVortexLockedOffline`** — guarded, but belt-and-braces: a
  Vorlon's only powered system is the drive and the drive is on `vortexMaintainExemptNames`, so both
  already answered harmlessly. Kept, because the exemption should be a statement and not an accident.
- ⚪ **"check the point cost"** — `powerReq` feeds no cost formula; `pointCost` is set by hand per
  ship file. Ship-data validator: **0 new findings**, 237 all in the committed baseline.
- ✅ **`getVortexUpkeepDraw` answers 0 on every other drive** — asserted in both harnesses.

**Harnesses added** (run both after any change to this family):

- `tests/replay/vorlonUpkeepHarness.php` — **115 assertions**, in memory, no DB, against the REAL
  ship files (half of what H3 changed IS the ship files, and a hand-built engine would not see them).
  `docker exec -w /usr/src/current fieryvoid-php-1 php tests/replay/vorlonUpkeepHarness.php`
  ⭐ R4 and R5 are asserted in **both directions on the same fixture** — the Vorlon is exempt, an
  ordinary ship in the identical state is not — because an exemption that is too wide looks exactly
  like a pass. And every "was it charged" assertion reads the **capacitor's own counter**, never the
  return value: a method that answered true and drew nothing would pass on the return alone.
- `tests/replay/vorlonUpkeepClientHarness.js` — **37 assertions**, `node`, over the REAL `power.js`
  and `model/system/baseSystems.js`. Its §2 is the no-standing-cost guard and its §3 the
  double-charge guard; both were self-tested by deleting the line each one protects.

**Gate after H3** (`fvbuild.ps1 -Check`): autoload map **up to date**, ship-data validator **0 new
errors / 0 new warnings** (237 findings, all baselined), replay harness **129 passed / 1 failed** —
game 4302 again, with a diff containing **no `vortex*` paths at all** and the same shape H1 and H2
recorded. `php -l` clean on all 13 server files; legacy bundles rebuilt (`yarn build:legacy`, all
five new symbols verified present); `UI.bundle.js` deliberately untouched, with the React tree
verified instead by esbuild bundle + vm evaluation (self-tested against a deliberately broken module
first).

**⚠️⚠️ `replayHarness.php record --games=…` REWRITES THE WHOLE MANIFEST, IT DOES NOT MERGE.**
Re-recording the 15 corpus games whose only diff was the new payload keys silently shrank the corpus
from 130 games to 15 — `cmdRecord` builds a fresh `manifest['games']` from the games it recorded and
writes it over the old one, and the next `check` then runs only those. Recovered by backing up
`baseline/game_4302/`, running a full `record`, and restoring 4302's files — so the corpus is 130
again and 4302 keeps the stale baseline H1 and H2 declined to accept. **Never use `--games=` to
record unless you mean to narrow the corpus.**

**Two pre-existing failures found while running the plan's eight harnesses, neither of them H3's**
(both confirmed by putting the 12 changed server files back to HEAD and re-running):

- `reinforcementsStage6Harness.php` — **180 passed, 6 failed.** It asserts
  `damageclass === 'JumpVortex'` on an EXIT doorway's log order, which **Stage H1 renamed** to
  `JumpVortexExit`. A stale expectation left over from H1; the fix is one string in the test.
- `initialOrdersTooltipHarness.js` (4/10, `shipManager.isTargetable is not a function`) and
  `reinforcementsStage{8,9}ClientHarness.js` (both `Error: export marker not found`) fail identically
  at HEAD.

**Not verified here, and it needs a play test:** that the reservation and the payment really do meet
in a live game — a Vorlon **with no jump point** should show its **full** power available, one
opening or maintaining a jump point should show its balance 5–8 lower in Initial Orders, and after
committing the capacitor should end the turn exactly that much lower and **no more**. Create a game
with a Vorlon, check its idle balance first, then open a jump point, Maintain it on the next turn,
and read `tac_individualnote` for each turn's phase-1 and phase-4 `powerStored` rows.

---

### Stage H2 — power management from hyperspace (Item 5) — BUILT 2026-09-17

⭐⭐ **POWER ONLY, NEVER EW (user ruling 2026-09-17).** A unit in hyperspace manages its power
allocation and nothing else: no OEW, no DEW, no EW-supported anything. That ruling is what keeps
this a single narrow widening rather than "undeployed units are half-active now", and it is why the
EW sites §3 listed as candidates were deliberately **left closed**.

**One predicate, five readers.** `shipManager.canManagePowerFromHyperspace(ship)`
([ships.js](source/public/client/ships.js)) is the whole of the widening, and every site that
consults it is a POWER site:

| Site | File | Why |
|---|---|---|
| `onSystemClicked` | [PhaseStrategy.js](source/public/client/renderer/phaseStrategy/PhaseStrategy.js) | the blocker — it swallowed the click before `showSystemInfo` ran, so no system menu ever opened |
| `getShipsNegativePower` | [power.js](source/public/client/power.js) | the Initial Orders commit gate; a hyperspace ship over-allocated must be named or the deficit is invisible until it arrives |
| `getShipsGraviticShield` | [power.js](source/public/client/power.js) | same gate, same reasoning |
| `getCapacitorShipsNegativePower` | [power.js](source/public/client/power.js) | the Vorlon arm of the same gate — and the one H3 will lean on |
| `getPlasmaBatteryShipsNegativePower` | [power.js](source/public/client/power.js) | the Pak'ma'ra arm of the same gate |

**Deliberately NOT widened**, and each for a stated reason:

- every EW site in `gamedata.js` and `ew.js`, including the "CHECK for NO EW" arm of the commit
  checklist — the ruling above;
- the FIRE-ORDER, AMMO, MOVEMENT and THRUST arms of the commit checklist (the other five
  `deployTurn > gamedata.turn` skips §3 flagged). On inspection **none of them is a power gate** —
  they are "no pre-fire orders", "no fire orders", `AmmoMagazine` usage and `mustPivot`/thrust. The
  power gates all live in `power.js` and are merely *called* from `gamedata.js`, so `gamedata.js`
  itself needed no edit at all;
- `PhaseStrategy.isOffBoardForEdf` — a unit in hyperspace must still project no Energy Draining
  Field, and it reads the same accessor;
- `shipManager.shouldBeHidden` — the unit has no icon and must not get one; the ship window is
  reached from the fleet list, which is the route that was already working;
- `weaponManager.selectWeapon` / `targetHex` — they measure range from `getHexPos()` and a
  hyperspace unit has no hex, so they already fail closed. **No fire order can be produced from
  hyperspace**, which is what keeps this a power change and not an orders change.

**No server change.** `InitialOrdersGamePhase::process` already walks every ship of the posting
player and calls `submitPower` with no deploy-turn filter at all, and `ajaxInterface` already posts
`system.power` for every own ship — verified before building. The rows have always travelled and
always persisted; only the click was missing.

**Gate after H2** (`fvbuild.ps1 -Check`, 2026-09-17): autoload map **up to date**, ship-data
validator **0 new errors / 0 new warnings** (237 findings, all in the committed baseline), replay
harness **128 passed / 1 failed**.

The 1 is **game 4302 — the jump-point playtest game — and it is stale DATA, not a regression.** The
cross-check is conclusive: `git diff source/server` is **empty**, and a client-only change cannot
move a server-side replay. The diff also has the exact shape [[arch_replay_corpus_known_failures]]
describes for "the user played this game since it was recorded", holding things code cannot invent:

```
/ships/0/EW/1: added (<6 item array>)
/ships/0/systems/*/overloadturns: removed (was 1)      x ~50
masking.txt  p210 ph2  waiting=1 -> waiting=0
masking.txt  p211 ph2  ship876534 mv=4 -> mv=3
```

Not re-recorded: it is the user's playtest game and re-recording accepts whatever drift is in it.
`waiting=0` in the current run means it is now safe to re-record when they want to
(`replayHarness.php record --games=4302`).

**Legacy bundle rebuilt** with `yarn build:legacy` — legacy client files only, so `UI.bundle.js` was
deliberately left alone (it is already dirty from the user's own work and must not be churned).
Verified the symbol reaches the bundle: 6 occurrences = 1 definition + 5 call sites.

**Not verified here, and it needs a play test:** that `overloadturns` actually accumulates across
the hyperspace turns, so a weapon overloaded in hyperspace really does arrive fully charged in
Sustain mode. The machinery is turn-indexed rather than position-indexed and nothing skips an
undeployed ship on the paths that matter, but that is an argument, not a measurement. Create a game
with a reinforcement, overload one of its weapons in Initial Orders while it waits, commit, and
check `tac_power` for a type-3 row and the arrival turn's `overloadturns`.

---

### Stage H1 — "HYPERSPACE" in the combat log (Item 1) — BUILT 2026-09-17

Built as §2 designed it: `writeVortexLogOrder` gains a 4th argument and stamps **`JumpVortexExit`**
on a blue doorway's line, `JumpVortex` on a yellow one, and the client heads every hyperspace entry
**`HYPERSPACE:`** in the map marker's own colour — `#e1b000` leaving, `#00b8e6` arriving.

**The six callers, and how each one knows which it is:**

| Caller | Doorway | How it decides |
|---|---|---|
| `openVortex` | 🟡 yellow | the default — it is the yellow sweep |
| `openExitVortex` | 🔵 blue | `true`, literal; it only ever opens blue (a legacy hull phasing in included) |
| `openVortexAtGate` | either | the `$exit` flag its own sentence already branches on |
| `resolveGateClaims` ×2 (claim refused, hold clamped) | either | `$exit` — see below |
| `recordVortexClosure` | either | **derives it**, `instanceof SpawnJumpPointExit` on its own `$activeVortexId` |

⭐ **Two callers write their line BEFORE any vortex exists** — the gate claim refusal and the
reactor-damage hold clamp — so there is nothing to ask `instanceof` of. That is why the flag is
*passed* rather than derived everywhere: a signature that could only be derived would have no
answer at those two sites. They take the colour of the doorway the gate actually opened, which is
what the refusal is a consequence of; there is no vortex of the *loser's* to colour by.

**⚠️⚠️ TWO CLIENT SITES THIS PLAN DID NOT LIST, and both would have been silent.**
`AllWeaponFireAgainstShipAnimation.js` matches the class name **twice** — once to suppress the
explosion and damage on a log-only order, once to keep "Primary Structure destroyed" out of the
replay caption. A blue doorway's line would have been animated as a real hit on its own opener.
The general rule, now stated at both sites: **`JumpVortexExit` is `JumpVortex`'s twin and every
site that names one must name the other.** The full list as of today is four: those two,
`Firing::isHyperspaceLogOrder`, and `weaponManager.doShortLogText`.

⚠️ **`spawnDeclaredVortices`' submit scan was deliberately left `'JumpVortex'`-only.** It runs
FIRST of the three sweeps in `InitialOrdersGamePhase::advance`, so no exit or gate line exists yet
to pick up, and both later sweeps submit through their own `$logOrders` array. Widening it would
duplicate every one of their rows — the trap the comment above it already describes. Commented so
it is not "fixed".

⭐ **The colour lives in `logPanel.css`, not in the JS.** `combatLog.js` emits a class and an
**empty** `style` attribute; the two literals were already owned by the stylesheets. That empty
attribute is load-bearing: every other entry's header is team-coloured *inline*, which no rule can
beat, so writing nothing is what lets the stylesheet win for these four classes and only these.

⚠️ **Accepted trade: the hyperspace header is the ONE header that is not team-coloured.** The 3px
allegiance rail on the entry comes from the same `getShipLogColorCss` and is untouched, so the
owner is still readable. If that rail is ever restyled, revisit this.

⚠️ **Known side effect, one line to reverse if unwanted.** `destroyGateOnReactorLoss` *reuses*
`'JumpFailure'` for "loses its reactor entirely — the jump gate collapses" (deliberately, for three
behaviours the comment there lists). That line now heads `HYPERSPACE:` in yellow, which is not
really a hyperspace event. Either drop `JumpFailure` from `HYPERSPACE_LOG_KINDS` (and a real
jump-drive detonation goes back to reading `FIRE:`, which is worse), or give the gate collapse a
damageclass of its own. Left as-is pending a call.

**Harnesses added** (run both after any change to this family):

- `tests/replay/hyperspaceLogHarness.php` — **12 assertions**, in memory, no DB.
  `docker exec -w /usr/src/current fieryvoid-php-1 php tests/replay/hyperspaceLogHarness.php`
  Reaches the protected static by reflection, and drives the 3-argument call **variadically** so it
  exercises the *method's* default rather than the harness's.
- `tests/replay/hyperspaceLogClientHarness.js` — **13 assertions**, `node`, over the REAL
  `combatLog.js` + `weaponManager.js` (`global.window = global`, plus a Proxy jQuery so the
  document-ready block at the foot of `combatLog.js` evaluates).
  ⭐ Its last section is driven off `HYPERSPACE_LOG_KINDS`' **keys**, so the
  "HYPERSPACE: implies short-log" invariant covers any class added later without editing the test.
  It also asserts the opposite direction — an ordinary shot still heads `FIRE:`, and a near-miss
  name like `JumpVortexOther` does **not** match — because a table that was too broad would look
  correct in a test that only checked the four classes.

**Gate after H1** (`fvbuild.ps1 -Check`): autoload map **up to date**, ship-data validator **0 new**,
replay harness **128 passed / 1 failed** — the same game 4302, with a **byte-identical** diff to the
H2 run and **no `damageclass` paths in it at all**, which is the proof that this server change moved
nothing in the corpus. `php -l` clean on both server files. Legacy bundle rebuilt
(`yarn build:legacy`); `UI.bundle.js` untouched.
