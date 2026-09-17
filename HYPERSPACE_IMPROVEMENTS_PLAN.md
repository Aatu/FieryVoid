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
| H5 | none unless a corpus game holds a blue doorway |
| H6 | new `deploy` movement rows for arrivals; `movement.txt` diffs on any corpus game with a wave in it. **Re-record only after reading them.** |

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
