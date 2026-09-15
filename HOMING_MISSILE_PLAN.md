# HOMING MISSILE (Class HM) — Implementation Plan

**Status:** ✅ BUILT 2026-09-02. Autoload + statics regenerated, client bundles rebuilt, replay
harness 130/131 (the one FAIL, game 4305, fails identically on a clean tree — see §7).
**First play-test (game 4328) found two bugs, both fixed — see §2.3.**
**Second play-test of the same game found three more, all one cause — see §2.5,**
**and a fourth in the hit-chance preview, one function along — see §2.5a.**
**Date:** 2026-09-02, refined 2026-09-03
**Scope:** one new shipborne missile ammo type for `AmmoMagazine`-fed missile racks.

---

## 0. The rule being modelled

> The homing missile has the same combat statistics as the basic missile but costs 12 points and
> suffers the same availability restrictions as the stealth missile. It remains in play even if it
> misses, and comes around for another pass on the next turn, treating its target's previous
> location as its new launch hex for directional purposes. It keeps attacking every turn until it
> runs out of range or is shot down.
>
> To shoot it down, the defender must employ defensive fire *sufficient to cause it to miss* —
> i.e. the roll beat the un-intercepted threshold but not the intercepted one. Count all active
> defensive fire (guardian arrays, Class-I missiles); do **not** count passive defences (shields,
> energy webs).
>
> Fuel: each pass adds the distance already travelled to the distance it must move to the target's
> new hex. Exceed the distance range and the missile is lost.
>
> The defender does not know a missile is homing until it misses once and sticks around.

### User rulings taken 2026-09-02
| Question | Ruling |
|---|---|
| Price | **12 PV per round** (book value, as with Class J/KK/X/M/K). Deliberately more than double Stealth's FV price of 5. |
| Availability | **Kor-Lyan only — the 11 hulls with a live `AMMO_S` line.** Not the Triad hulls, not `technicalTargetDrone`. |
| Concealment | **Mask the mode from non-allies until the first miss**, then reveal. |

---

## 1. Why this maps onto FV almost for free

Three pieces of existing machinery do nearly all the work, and the fit is exact rather than
approximate:

**1. The intercept band already IS the "shot down vs clean miss" test.**
`Weapon::fire` ([weapon.php:2108](source/server/model/weapons/weapon.php#L2108)) subtracts
`totalIntercept` from `needed`, then counts a roll landing in `(needed, needed + totalIntercept]`
as `intercepted`. `totalIntercept` is *active* interception only — shields and energy webs never
enter it. So after resolution, with `shots == 1`, exactly one of three states holds:

| State | Test | Homing outcome |
|---|---|---|
| Hit | `shotshit > 0` | gone (it detonated) |
| Shot down | `intercepted > 0` | gone (removed from play) |
| Clean miss | neither | **stays in play, re-attacks next turn** |

This needs **no change at all** to `Weapon::fire`.

**2. Missiles have no range penalty in FV.**
`AmmoMissileTemplate::calculateRangePenalty` returns 0
([baseSystems.php:14687](source/server/model/systems/baseSystems.php#L14687)), and
`AmmoMissileRackS::calculateRangePenalty` delegates to it. So moving the launch hex to the target's
previous location — which is usually 1–10 hexes away — does **not** inflate the missile's accuracy.
The launch hex only feeds bearing/hit-section (`getHitSectionPos`/`getHitSectionProfilePos`,
[weapon.php:1864](source/server/model/weapons/weapon.php#L1864)) and the distance-range test. That is
precisely what "for directional purposes" means in the rules text.

**3. Basic and Homing are statistically identical.**
Same damage 20, same FC +3/+3/+3, same range 20/60. So the concealment mask can simply report the
**Basic** firing mode to non-allies and every downstream client calculation (hit-chance preview,
icon, tooltip, interception maths) produces the correct numbers *for the lie*. No second code path,
no divergent-threshold machinery of the kind Chameleon needed.

---

## 2. Architecture — where the state lives

A homing missile that survives a pass has to persist across a turn boundary. The chosen carrier is
**IndividualNotes on the launcher**, with the fire order for each new pass **rebuilt at gamedata
load time** from those notes. This is the PakmaraPlasmaWeb persistent-cloud pattern
([plasma.php:1677](source/server/model/weapons/plasma.php#L1677)) with the fragile half replaced.

### 2.1 The turn cycle

```
TURN N, phase 1   player declares a Homing launch (mode "Homing")
                  -> ordinary ballistic FireOrder, ordinary AmmoUsed note, ordinary magazine draw
                  -> masked to "Basic" for non-allies (§5)

TURN N, phase 4   FireGamePhase::advance
                  prepareFiring -> calculateHitBase -> fireWeapons -> Weapon::fire resolves it
                  THEN generateIndividualNotes (FireGamePhase.php:52) runs on the SAME objects,
                  which still carry rolled / needed / shotshit / intercepted / totalIntercept.
                  -> AmmoMissileRackS::generateIndividualNotes inspects each resolved Homing order:
                       hit or intercepted  -> write nothing (missile is gone)
                       clean miss + no fuel-> write nothing + pubnotes "ran out of fuel"
                       clean miss + fuel   -> INSERT next turn's re-attack row
                                              (persistNextPassOrder, turn = N+1), then write note
                                              'HomingMissile' = packed state INCLUDING that row id
                                              (§2.5)

TURN N+1, EVERY   DBManager::getSystemDataForShips -> onIndividualNotesLoaded
gamedata load     -> the row written above is already in $this->fireOrders (fire orders load BEFORE
                     system data — DBManager.php:2843-2844), so each note ADOPTS ITS OWN ROW BY ID
                     and registers its state in the rack's $homingStates map — identity, travel and
                     fuel are NEVER on the order (§2.3).
                  -> a note whose target has left the game DETACHES its row instead (§2.5).
                  -> legacy path only (orderid 0 — a note written before §2.5): rebuild an
                     in-memory order and let phase 4 give it an id, matching on the order's own
                     persisted fields.

TURN N+1, ph 1    VISIBLE, to both players, from the first load of the turn (§2.5). It is not a
                  launch declared this turn and is not a secret — it announced itself by surviving.
                  deleteHiddenData exempts damageclass 'HomingMissile' from the phase-1 ballistic
                  strip, and Firing::validateFireOrders rejects it if a client posts it back.
TURN N+1, ph 2/3  mode UNMASKED (it has missed once). The defender can assign manual interception
                  against it exactly like any incoming missile, and — because it now has a real
                  row id — the server can tell which missile that declaration named.

TURN N+1, ph 4    prepareFiring -> AmmoMissileRackS::beforeFiringOrderResolution is now only the
                  FALLBACK insert, for a missile whose note predates §2.5. Then the order resolves
                  normally, and the cycle repeats.
```

### 2.2 Why persist in `beforeFiringOrderResolution` and not at load

Two hard constraints force this:

- **A load-time insert would duplicate the row on every poll.** `onIndividualNotesLoaded` runs on
  every single gamedata build, for every viewer.
- **A damage-dealing synthetic order MUST have a real DB id before it deals damage.**
  `DBManager::submitDamages` back-fills a `-1` fire-order id by querying
  *same gameid+turn+shooterid+weaponid, shotshit > 0* and taking `$result[0]` with **no ORDER BY**
  — so a launcher that fires normally *and* carries a homing order in the same turn would silently
  log the damage against the wrong order. See `[[howto_create_fire_orders]]`.

`Firing::prepareFiring` calls `$system->beforeFiringOrderResolution($gamedata)` on every system of
every ship, exactly once per resolution
([firing.php:1624](source/server/handlers/firing.php#L1624)). That is the one hook that runs once,
runs early, and runs on the server-authoritative load.

### 2.3 ⚠️ What the first play-test found (game 4328, fixed 2026-09-02)

Two bugs, one reported and one found while chasing it. Both came from the same wrong assumption:
*that a fire order can carry state of its own through resolution.* It cannot.

#### Bug 1 — `$fireOrder->notes` is ASSIGNED, not appended, so the missile lost its identity

`Weapon::calculateHitBase` ends with `$fireOrder->notes = $notes;`
([weapon.php:1927](source/server/model/weapons/weapon.php#L1927)) — a plain assignment that wipes
whatever was there. The first cut carried a `HOM:key:travelled:pass` tag on `notes`, and it was
destroyed the instant the shot's hit chance was computed.

The evidence in 4328: the note written at turn 2 reads `497389|876749|3|4|12|2|1` — key **497389**,
which is the *re-attack order's own id* rather than the original launch's `497385`; travelled reset
to 3; pass back to **1**. So every pass began life as a brand-new pass-1 missile with an empty
tank — **it could never run out of fuel and would have flown for ever.**

**Fix:** the runtime carrier is now `AmmoMissileRackS::$homingStates`, an in-memory map keyed off
`spl_object_id($fireOrder)` and holding the order object alongside its state (which pins the object
so the id cannot be recycled, and lets a lookup prove identity instead of trusting the key). Scope
is one request — `onIndividualNotesLoaded`, `calculateHitBase` and `generateIndividualNotes` all run
inside the same `FireGamePhase::advance`, on the same objects. Between turns the IndividualNote
remains the carrier. `buildOrderTag`/`readOrderTag` are gone; **nothing** rides on `notes`.

Two consequences:
- **`addToDB` on a rebuilt order is now `false`.** Left true, `PreFiringGamePhase::advance` and the
  movement-phase jump-out sweep both submit `getNewFireOrders()` and would persist the order a phase
  early; every later load of that turn would then find a persisted order with no tag to recognise
  it by. `persistHomingOrders` is the only insert.
- **The anti-duplicate guard now matches on the order's own persisted fields** (targetid, x, y,
  firingMode) and *adopts* the order rather than skipping it, registering its state. Two identical
  missiles — same target, same launch hex, same mode — are indistinguishable to that match, which
  is exactly why it consumes one order per matching note rather than keying on identity.

#### Bug 2 (the reported one) — a Class-F rack resized a missile already in the air

`AmmoMissileRackF::recalculateFireControl` **rewrites its own `$rangeArray`/`$distanceRangeArray`**
(−20/−30) whenever the rack is short-loaded or fired in Rapid mode. `getFuelBudget` read those
arrays live, so a lingering missile's fuel tank shrank and grew with the launcher's loading state —
correct for a launch, wrong for something already flying.

**Fix:** the budget is captured **once**, on the pass that creates the missile, and carried in the
state (an 8th packed field) from then on. `unpackState` accepts a 7-field note and reports
`budget = 0`, meaning "not captured" — those fall back to the live figure exactly as before, so a
missile already in flight when the fix landed keeps working.

Both fixes are covered by harness assertions that were **checked in both directions** — reverting
either turns its tests red, restoring it turns them green.

### 2.4 Rejected alternatives

| Alternative | Why not |
|---|---|
| Persist the re-attack order at phase-1 commit, PakmaraPlasmaWeb style | That pattern relies on the **client posting the server-created order back** (`InitialOrdersGamePhase::process` → `$ship->getAllFireOrders()`), which is why `deleteHiddenData` carries a `PersistentEffectPlasma` exception. Acceptable for a cosmetic marker; unacceptable for a shot that deals 20 damage — it would be a player-forgeable path and would break for a disconnected player. |
| Create the re-attack order only in `beforeFiringOrderResolution` | Too late. Manual interception is declared in **phase 3**; an order that first exists in phase 4 can never be intercepted by hand. |
| Model the in-flight missile as its own unit (Mine/terrain style) | Enormously more code — deployment, initiative, movement, masking, the `tac_ship` lifecycle — for a two-turn-lived object. |
| Add a hidden "homing control" system to Kor-Lyan hulls | Trips `[[arch_positional_system_id_trap]]` (system ids are construction-order) and changes every affected blueprint. |

---

### 2.5 ⚠️⚠️ The second play-test (game 4328, fixed 2026-09-03) — three symptoms, ONE cause

The user reported three things after playing turns 1–3 of 4328:

1. a re-attack does not appear until **after** both players commit Initial Orders, though both
   should know about it from the start of the turn;
2. the **launch hex was drawn for one missile and not the other** — the pair chasing the Utan
   fighters showed one, the pair chasing the Udran Command Cruiser did not;
3. manual interception assigned to one of two homing missiles **was credited to both** on the
   defender's screen, and then the **server ignored it entirely** and auto-assigned instead.

Only (1) is a design decision. (2) and (3) are the same bug, and the database says so plainly:

```
id      turn type      shooter target weapon mode needed rolled x  y   damageclass
497389  2   ballistic  876747  876749 24     2    65     20     3  9   HomingMissile
497390  2   ballistic  876747  876748 28     2    40     58     4  -6  HomingMissile
497391  3   intercept  876748  -1     20     1                          molecular   <- player's, targetid -1
...497395 (5 of them, all targetid -1)
497397  3   ballistic  876747  876748 28     2    5      85     5  -3  HomingMissile
497398  3   ballistic  876747  876748 28     2    35     89     5  -3  HomingMissile
497399  3   intercept  876748  497397 17     1                          <- the AUTOMATION's, inserted after
```

**A re-attack order had no identity until the Firing phase resolved it.** It was built in memory
with `id = -1` and only got a row from `persistHomingOrders`, which runs inside
`Firing::prepareFiring` — *after* the client has seen the missile for three phases and after the
player has declared interception against it. Everything the client does with a shot is keyed on that
id:

- `BallisticIconContainer.createOrUpdateBallistic` looks up an existing icon by `ballistic.id`, so
  the **second** missile with id −1 found the first one's icon and was quietly merged into it —
  hence one launch hex for two missiles (symptom 2; rows 497389/497390 above, two different targets,
  two different launch hexes, one marker);
- an `intercept` order's `targetid` **is** the id of the order it intercepts, so a declaration
  against an id −1 missile was written as `targetid -1` and
  `weaponManager.getDeclaredInterception` credited it to **every** id −1 missile (symptom 3a,
  rows 497391–497395);
- and `Firing::automateIntercept` resolves that `targetid` against this turn's orders **by id**, so
  −1 named nothing, the five declarations were dropped, and the automation spent five different
  weapons of its own (symptom 3b, rows 497399–497403).

Note also that 497397 and 497398 are **indistinguishable**: same shooter, same weapon, same target,
same launch hex, same mode. Two missiles chasing one ship always share a launch hex — it is that
ship's previous position — so the field-based adoption match of §2.3 could never tell them apart.

#### The fix: write the row a turn early

`AmmoMissileRackS::persistNextPassOrder`, called from `generateIndividualNotes` at the moment the
survival note is written. It builds the next pass's `FireOrder` with **turn = N+1** and inserts it
immediately; the new row id becomes the note's **9th packed field** (`orderid`).

Why this is safe, and why turn N+1 rather than turn N:

- `DBManager::getFireOrdersForShips` only ever fetches the **current** turn, so the row is invisible
  for the remainder of turn N and appears, already loaded and attached to the launcher, on the first
  load of turn N+1.
- `generateIndividualNotes` at phase 4 runs exactly once per game turn, inside
  `FireGamePhase::advance`, on the server-authoritative load — the same single-shot guarantee
  `beforeFiringOrderResolution` was chosen for in §2.2.
- The order is deliberately **not** attached to `$this->fireOrders`: it does not belong to the turn
  being resolved, and every sweep that follows in `FireGamePhase::advance` walks that turn's orders.
- `persistHomingOrders` stays as the fallback, for a note written before this landed (`orderid` 0)
  or an insert that failed. `unpackState` reports `orderid = 0` for any note shorter than 9 fields,
  so a missile that was in the air when this shipped keeps flying on the old path.

#### And the row has to be withdrawable

`onIndividualNotesLoaded` has always refused to rebuild a missile whose target has left the game.
With the row written up front, "no target" no longer means "nothing exists" — the order is there
whatever happened — so the same branch now **detaches** it (`withdrawHomingOrder`). Left attached it
would reach `prepareFiring`, which hands a null target straight to `calculateHitBase`.

#### Visible during Initial Orders (user ruling 2026-09-03)

`deleteHiddenData` strips every current-turn ballistic order from every phase-1 payload, its
author's included, because a launch declared this turn is a secret until both sides commit. **A
missile already in the air is the opposite of a secret** — it revealed itself by surviving its last
pass — and both players need it on the board while they are deciding this turn's orders, not four
phases later. `damageclass 'HomingMissile'` is now exempted, exactly as
`'PersistentEffectPlasma'` is.

> **⚠️⚠️ PHASE 1 IS THE ONE PHASE THAT INSERTS A BALLISTIC ORDER WHATEVER `->addToDB` SAYS.**
> `DBManager::submitFireorders` skips a ballistic order without `addToDB` in every phase **except
> 1** — that clause is how a player's launch declarations get written at all. So anything made
> visible in Initial Orders is re-inserted verbatim when its owner commits: leaving this alone
> would have written a duplicate copy of every missile in the air, every turn, and left a
> hand-edited POST able to conjure 20 points of damage out of nothing.
> `Firing::validateFireOrders` now rejects and detaches any POSTed order with this damageclass,
> using the same `->rejected` convention as the corrupt-order path. The server writes these and
> nobody else does.

#### The client half

The order is now on the launcher from Initial Orders onward, and the player did not put it there and
cannot change it. Every predicate that asks *"has this weapon been given an order this turn"* has to
answer NO for it — `hasFiringOrder`, `hasOrderForMode`, `hasTargetedThisShip`,
`countCurrentTurnOrders`, `removeFiringOrder`, `removeFiringOrderMulti` — or the rack reads as
fired, cannot be taken offline or powered down, and, worst, **has its in-flight missile spliced away
by `removeFiringOrder`** the moment the player declares a fresh launch with the same rack (that call
is the first thing `targetShip` does on the non-split path). One predicate,
`weaponManager.isHomingReattack`, mirrors `AmmoMissileRackS::firedOnTurn` on the server, which
excludes the same orders from the same question for the same reason.

#### The incoming bearing (user's third point)

> *"perhaps we shouldn't assign the repeat Homing Missile to the firing ship any more, as the
> attacks will have different incoming bearings on the target ship (important for applying
> intercept)."*

Correct, and the server was already right: `AmmoMissileRackS::getFiringHex` returns the order's own
hex. The **client** was not — `ShipTooltipBallisticsMenu.getBallisticEntry` set every shot's
`position` from the shooter's icon, and that `position` is what `getIncomingSourcePos` feeds to the
arc test in `canInterceptBallistic` and to the escort geometry in `isBetweenShooterAndTarget`. Two
missiles from one rack chasing two different ships genuinely approach from two different directions.

Three changes, all per-ORDER:
- `weaponManager.getHomingLaunchHex(fire)` — the client mirror of the server's
  `getHomingLaunchHex`; `getIncomingSourcePos` prefers it over both `ball.position` and the shooter.
- the INCOMING list's group key now includes the launch hex. A grouped row runs **one** eligibility
  test against `members[0]` and reuses it for the whole row, which is only sound while the members
  share their geometry.
- `members.forEach(... member.position = getLaunchHex(member))` — resolved per member rather than
  copied from the group's representative.

> **⚠️ `hexagon.Offset` has `q`/`r` and NO `x`/`y`** (`model/hexagon/Offset.js`), and a movement
> row's `.position` is one. The first cut of that group key read `launchHex.x`, which is `undefined`
> for every hex in the game — a key suffix of `"undefined,undefined"` that differentiates nothing
> and throws no error. The node harness missed it because its stub `Offset` had been given `x`/`y`;
> the stub was cut back to match the real class and now asserts the absence.

#### 2.5a The hit-chance PREVIEW was reading the same wrong bearing (2026-09-03, second pass)

The user's next report: *"the Targeting tooltip is not fetching the correct defence profile for the
ship based on the actual bearing of the attack for repeat missiles"* — client 0–20% against the
server's −5%/15% in the same turn 3.

The database settles it. Turn 3 of 4328, all three shots from the same rack at the same ship:

```
id      damageclass    x,y     notes
497391  HomingMissile  5,-3    defence: 16, DEW: 13, F/C: 4, goal: 7, chance: 35
497392  HomingMissile  5,-3    defence: 16, DEW: 13, F/C: 4, goal: 7, chance: 35
497393  ballistic      0,0     defence: 17, DEW: 13, F/C: 4, goal: 8, chance: 40   <- fresh launch
```

**One point of defence profile, and nothing else.** The re-attacks come in over the target's beam
(side 16); the launcher is off its bow (front 17). The client previewed all three at 40%, so after
40 and 20 points of declared interception the INCOMING list read 0% and 20% where the server had
−5% and 15%.

The cause is the same shape as §2.5 and one function along:
`weaponManager.getFiringHex(shooter, weapon)` **is per-WEAPON**, while the server's
`Weapon::getFiringHex($gamedata, $fireOrder)` is **per-ORDER**. `calculateHitChange` called the
client one and fed its answer to `getShipDefenceValuePos` — the single largest term in the goal.

**Fix:** `getFiringHex` takes an optional third argument, the fire order, and returns that order's
own launch hex for a homing re-attack (the exact mirror of the server's override). `fireOrder` is
then threaded as an optional 5th argument through `calculateHitChange` and out to every caller that
actually has an order in hand — `calculataBallisticHitChange` (via `ball.fireOrder`),
`getIncomingShotHitChange`, the INCOMING list's live breakdown, and both sweeps in
`declarations.js`. **A caller with no order behaves exactly as before**, which is right: the player
targeting an enemy has no order yet and the shot really would launch from the ship.

⭐ **And the same hex feeds a second thing the server passes it to.** `calculateHitBase` hands
`$launchPos` to `getHitChanceMod` as `$posmod`, and that is what
`checkIsValidAffectingSystem` tests **arc-limited defensive systems** (shields, webs) against — so a
missile arriving over the beam must be judged against the beam shield, not the bow one. The client's
`Ship.getHitChangeMod` derived that hex from the shooter too. It now takes the same optional
`launchPos`, threaded through `computeShotModifiers`.

> **⚠️ `ship.js` CONTAINS TWO `Ship.prototype = {...}` BLOCKS AND THE SECOND ONE IS DEAD.** It is
> inside a `/* //OLD VERSION - CHANGED DEC 2025 */` comment, so the LIVE definitions of
> `getHitChangeMod` / `getHitChangeModFlight` are the FIRST pair (~line 100). Editing the second
> pair compiles, changes nothing, and looks correct in a diff.

---

## 3. Server-side work

### 3.1 `AmmoMissileHM` — the new ammo class
**File:** [baseSystems.php](source/server/model/systems/baseSystems.php), immediately after
`AmmoMissileS` (line 15023), so the Kor-Lyan pair sit together.

```php
class AmmoMissileHM extends AmmoMissileTemplate {
    public $name = 'ammoMissileHM';
    public $displayName = 'Homing Missile';
    public $modeName = 'Homing';
    public $size = 1;
    public $enhancementName = 'AMMO_HM';
    public $enhancementDescription = '(AMMO) Homing Missile';
    public $enhancementPrice = 12;          // book value

    public $rangeMod = 0;
    public $distanceRangeMod = 0;
    public $fireControlMod = array(3, 3, 3);
    public $minDamage = 20;
    public $maxDamage = 20;
    public $damageType  = 'Standard';
    public $weaponClass = 'Ballistic';
    public $priority   = 6;
    public $priorityAF = 5;
    public $hidetarget = false;

    public $isHoming = true;                // the single flag everything else keys off

    public function getDamage($fireOrder){ return 20; }
}
```

**Plus the homing behaviour itself, as public methods on this class** (the user's constraint:
keep it out of the main firing functions). The rack calls into these; none of them are known to
core code:

| Method | Job |
|---|---|
| `resolveHomingOutcome($gamedata, $weapon, $fireOrder)` | Read `shotshit` / `intercepted` off the resolved order, compute the new travelled total and the new launch hex, return a state array or `null` (missile gone). Writes the appropriate `pubnotes` line. |
| `packState($state)` / `unpackState($value)` | The note payload, `\|`-delimited: `missileKey\|targetid\|travelled\|launchQ\|launchR\|modeName\|passNo`. |
| `buildReattackOrder($gamedata, $weapon, $state)` | Construct the `FireOrder` for the next pass. |
| `isOutOfFuel($travelled, $extraDistance, $weapon)` | `travelled + extra > max($weapon->range, $weapon->distanceRange)` for the current mode. |

> **⚠️ The mode NAME goes in the note, not the mode INDEX.** `firingMode` is a positional index into
> `$firingModes`, rebuilt by `recompileFiringModes()` from whatever the magazine holds. Storing the
> name (`'Homing'`) and resolving the index at load is immune to any reordering.

> **⚠️ `missileKey` is the ORIGINAL launch order's DB id.** Player-submitted ballistic orders are
> inserted at phase-1 commit, so they carry a real id by the time `fire()` runs. That id stays the
> missile's identity for its whole life, which is what keeps two simultaneous homing missiles from
> the same rack apart.

### 3.2 `AmmoMissileRackS` — six thin overrides
**File:** [missile.php:1105](source/server/model/weapons/missile.php#L1105). Every derived rack
(L, LH, B, R, SO, O, G, Triad, F) inherits these; A and D reset `$ammoClassesArray` to their own
short lists and so will never see Homing — correct, those are anti-fighter/interceptor racks.

1. **Constructor** — one line added to `$ammoClassesArray`, after `new AmmoMissileS()`
   ([missile.php:1217](source/server/model/weapons/missile.php#L1217)):
   ```php
   $this->ammoClassesArray[] = new AmmoMissileHM();
   ```
   This only makes Homing a *potential* mode; `recompileFiringModes()` adds it as a real firing mode
   only when `AmmoMagazine::getAmmoPresence('Homing')` is true, which requires the `AMMO_HM`
   enhancement. Ships without it are byte-identical.

2. **`onIndividualNotesLoaded($gamedata)`** — walk `$this->individualNotes` for
   `notekey == 'HomingMissile'` with `turn == $gamedata->turn - 1`, unpack, and for each: verify the
   target still exists, then claim the persisted order the note names and register its state in
   `$this->homingStates`.
   **⚠️ REVISED TWICE — read §2.3 and §2.5, not this paragraph, for what it does now.** The original
   design rebuilt the order in memory here and carried the missile's identity in
   `$fireOrder->notes`; nothing survives there (§2.3), and since §2.5 the order is a real row
   claimed **by id** rather than rebuilt at all. The in-memory rebuild and the field-based match
   survive only as the legacy path for a note whose `orderid` is 0.
   **⚠️** Resolve the mode INDEX from the note, never by name — this runs before
   `Enhancements::setEnhancements` and the magazine holds no Homing rounds yet (§3.1).
   `isDestroyed()` is equally unreliable here and is left to phase 4.

3. **`generateIndividualNotes($gamedata, $dbManager)`** — phase 4 only. Walk this turn's fire
   orders with mode `'Homing'` (both a fresh launch and a re-attack), call `resolveHomingOutcome`,
   and for each survivor **insert next turn's row** (`persistNextPassOrder`, §2.5) before queueing
   its `IndividualNote`.
   **⚠️** `notekey`/`notekey_human` are `varchar(40)` and overflow is a **fatal that aborts the
   whole submission**, not a truncation (`[[arch_individual_notes_and_phase_hooks]]`). Use
   `notekey = 'HomingMissile'` (13) and `notekey_human = 'Homing missile still in flight'` (30).
   `notevalue` is `varchar(4096)` and holds the packed state.
   **⚠️** Phase 4 exists *only* inside `FireGamePhase::advance` (which `setPhase(4)` before its
   server-side load), so this cannot fire on a POST-side ship
   (`[[arch_post_side_ship_reconstruction]]`). This is the same trick `AmmoMagazine` uses with
   phases 1 and 3 — and it is also what makes the insert above safe, because it is the one hook
   that runs exactly once per game turn on the server-authoritative load.

4. **`beforeFiringOrderResolution($gamedata)`** — extend the existing method
   ([missile.php:1536](source/server/model/weapons/missile.php#L1536)). For each in-memory order
   with `damageclass === 'HomingMissile'` and `id == -1`:
   ```php
   $newId = Manager::insertSingleFiringOrder($gamedata, $fo);
   $fo->id = (int)$newId; $fo->addToDB = false; $fo->updated = true;
   ```
   **Since §2.5 this is the FALLBACK, not the normal path** — a re-attack arrives with a real id
   already on it, and this catches only a note that predates §2.5 or an insert that failed.

5. **`getFiringHex($gamedata, $fireOrder)`** — if the order is a homing re-attack, return
   `new OffsetCoordinate($fireOrder->x, $fireOrder->y)`; otherwise `parent::`. This is the
   ProximityLaser pattern ([specialWeapons.php:7318](source/server/model/weapons/specialWeapons.php#L7318)).
   `calculateHitBase`, `getIncomingBearing`, `getIncomingPos` and `Weapon::fire` all route through
   `getFiringHex`, so bearing, hit section and defence profile all come out right with no further
   change.

6. **`isInDistanceRange($shooter, $target, $fireOrder)`** — cumulative fuel. For a homing re-attack:
   `travelled` (parsed from the order's notes) `+ hexDist(launchHex, target)` against
   `max($this->range, $this->distanceRange)` for the current mode. Otherwise `parent::`.

7. **`firedOnTurn($turn)`** — **⚠️ this one is not optional.**
   `Weapon::calculateLoading` treats *any* fire order on a ballistic weapon this turn as "it fired"
   ([weapon.php:1072](source/server/model/weapons/weapon.php#L1072)), which would freeze the
   launcher's reload and block a fresh launch for as long as a homing missile was in the air. The
   override skips orders with `damageclass === 'HomingMissile'` — semantically exact, since the
   launcher genuinely did not fire.

> **LoS ALWAYS matters for the FC penalty (user ruling, 2026-09-02).**
> `calculateHitBase` docks an `AmmoMissileRackS`'s FC by `basicFC` when the **shooter** has no line
> of sight to the target ([weapon.php:1819](source/server/model/weapons/weapon.php#L1819)), gated on
> `!$this->hasSpecialLaunchHexCalculation`. A homing missile on pass 2+ is subject to this exactly
> like a fresh launch: the launching ship must keep line of sight to guide it.
> This needs **no code at all** — `hasSpecialLaunchHexCalculation` stays `false` on the rack, and
> the check reads the shooter's own hex (`$pos`), which is unaffected by the per-order launch hex
> override. Do not add an exemption here.

### 3.2a ⚠️ `AmmoMissileRackF` — two missing `parent::` calls, and they were fatal

**Found during implementation, and it would have killed the feature on six of the eleven hulls
without a single error.** `AmmoMissileRackF` overrides both note hooks for its own Rapid/Long-Range
mode tracking, and **neither override called `parent::`** — `onIndividualNotesLoaded` even ended by
clearing `$this->individualNotes` itself. So on a Class-F rack the survival note would never be
written and never be read: the missile would simply vanish after its first miss.

Which hulls mount what (`AmmoMissileRackD` resets its own ammo list to I/A/C/Z and can never carry
Homing, so it is never the answer):

| Rack | Hulls | Hooks |
|---|---|---|
| **Class-F** | Kalavar, Kolosk, Koskova, Leklant, Taloki, Verloka | ⚠️ **overrode both, fixed** |
| Class-L | KoskovaEarly, LeklantEarly, Raklavi, Solyrn, Soska | clean, inherits |
| Class-R | Solyrn | clean, inherits |

Both fixes are one line each: `parent::generateIndividualNotes(...)` at the top of RackF's version
(its own switch handles phases 1 and 3, the parent's only phase 4, so they cannot collide), and
`parent::onIndividualNotesLoaded($gamedata)` **in place of** its trailing
`$this->individualNotes = array()` — the parent must read the notes before the list is emptied, and
it clears them on the way out anyway.

**The general rule this leaves behind:** any `AmmoMissileRackS` subclass that overrides
`onIndividualNotesLoaded`, `generateIndividualNotes` or `beforeFiringOrderResolution` **must** chain
to its parent, or Homing dies silently on every hull mounting it. `BallisticMineLauncher` and `AbbaiMineLauncher` also override all three without chaining — deliberately left alone, since no
mine launcher's ship enables `AMMO_HM` and none can reach this code. Give one of them Homing and
this bites immediately.

### 3.3 `Enhancements.php`
**File:** [Enhancements.php](source/server/model/ships/Enhancements.php)

- **~line 1216**, immediately after the `AMMO_S` block — the option definition. Copy the `AMMO_S`
  block verbatim, including **`$enhLimit = $actualCapacity / 10;`** — the 10% magazine cap is the
  "same availability restrictions as the stealth missile" clause.
- **~line 2800**, the apply switch:
  ```php
  case 'AMMO_HM': //Homing Missile
      if($ammoMagazine) $ammoMagazine->addAmmoEntry(new AmmoMissileHM(), $enhCount, true);
      break;
  ```

### 3.4 The 11 Kor-Lyan hulls
Add one line beside the existing `AMMO_S` line in each:
```php
$this->enhancementOptionsEnabled[] = 'AMMO_HM';//add enhancement options for other missiles - Class-HM
```
`KalavarAMNew`, `KoloskAMNew`, `KoskovaAMNew`, `KoskovaEarlyAMNew`, `LeklantAM`, `LeklantEarlyAM`,
`RaklaviAM`, `SolyrnAM`, `SoskaAM`, `TalokiAMNew`, `VerlokaAM` — all in
[source/server/model/ships/korlyan/](source/server/model/ships/korlyan/).

Hulls whose `AMMO_S` line is **commented out** (`KalavarEarlyAMNew`, `KalavarOriginalAM`,
`KoloskEarlyAMNew`, `KoloskovaAM`, `KoshaAM`, `KoshaEarlyAM`, `SoskaAM`'s early sibling,
`TalokiEarlyAMNew`, `TalokiOriginalAM`, `VaklarAM`) are the pre-2252 era variants and must stay
without it. Follow the AMMO_S line's commented/uncommented state exactly — that is how FV models the
ISD availability date.

---

## 4. Client-side work

Deliberately small — a homing re-attack is an ordinary `type: 'ballistic'` fire order and the
existing icon, animation, interception and combat-log paths all handle it.

1. **[BallisticIconContainer.js:1140](source/public/client/renderer/icon/BallisticIconContainer.js#L1140)**
   — `launchPosition` defaults to `shooterIcon.getFirstMovementOnTurn(turn)`, which is wrong for a
   re-attack. Add a branch alongside the existing `PersistentEffectPlasma` / `jumppoint` ones:
   ```js
   if (ballistic.damageclass === 'HomingMissile') {
       launchPosition = this.coordinateConverter.fromHexToGame(new hexagon.Offset(ballistic.x, ballistic.y));
   }
   ```
   Same fix needed for the replay path in the same function.
   **⚠️** Do **not** route this through `weapon.hasSpecialLaunchHexCalculation` — that is a
   per-*weapon* flag and the launch hex here is per-*order*.

2. **[lobbyEnhancements.js:792](source/public/client/lobbyEnhancements.js#L792) and
   [:1663](source/public/client/lobbyEnhancements.js#L1663)** — two `case 'AMMO_HM':` blocks
   mirroring `AMMO_S`, using a fresh `ship.ammoHMEnh` flag.

3. **Bundle rebuild.** `game.legacy.bundle.js` and `gamelobby.legacy.bundle.js` must be rebuilt
   (`[[howto_minify_legacy_bundles]]`); **never commit the legacy bundles**
   (`[[feedback_fv_workflow]]`).

No client ammo class is needed — the client learns the mode purely from the launcher's
`firingModes` array in `stripForJson`.

### 4.1 Added 2026-09-03 (§2.5) — the order is now on the launcher from Initial Orders

4. **[weaponManager.js](source/public/client/weaponManager.js)** — one new predicate,
   `isHomingReattack(fire)` (`damageclass === 'HomingMissile'`), and one new resolver,
   `getHomingLaunchHex(fire)`. Applied in seven places:
   - `hasFiringOrder`, `hasOrderForMode`, `hasTargetedThisShip`, `countCurrentTurnOrders` — a
     missile in flight is not an order this player declared. The client mirror of
     `AmmoMissileRackS::firedOnTurn`.
   - `removeFiringOrder`, `removeFiringOrderMulti` — **the important one.** `targetShip` clears the
     weapon's orders before every new non-split declaration, so without this, launching a fresh
     missile made the one still chasing a target disappear from the map.
   - `getIncomingSourcePos` — a re-attack bears from **its own** launch hex, ahead of both
     `ball.position` and the shooter's start-of-turn hex.
   - `getDeclaredInterception` — refuses to credit interception to a `fireOrderId` that is
     numerically `<= 0` (a string local id is deliberately not caught by that test), and
     `getShotInterceptRefusal` says so on the row rather than letting the player spend weapons on a
     declaration the server will drop.

5. **[ShipTooltipBallisticsMenu.js](source/public/client/UI/ShipTooltipBallisticsMenu.js)** — a
   private `getLaunchHex(ball)` replaces the hard-wired "shooter's icon" launch hex in
   `getBallisticEntry`; it is also folded into the group key and applied per member. See the
   `hexagon.Offset` warning at the end of §2.5.

6. **[BallisticIconContainer.js](source/public/client/renderer/icon/BallisticIconContainer.js)** —
   the icon record's `position` field means *the hex this shot is aimed at* and is only read by
   `getByTargetIdOrTargetPosition`. A homing re-attack's `x`/`y` hold a real hex, but it is the
   LAUNCH hex, so it is stored as `null` for a unit-targeted re-attack rather than posing as a
   target hex. (The duplicate-icon bug of §2.5 symptom 2 is fixed by the stable id, not here.)

### 4.2 Added 2026-09-03 (§2.5a) — the hit-chance preview reads the same per-order hex

7. **`weaponManager.getFiringHex(shooter, weapon, fireOrder)`** — optional third argument, the exact
   mirror of the server's per-order `Weapon::getFiringHex`. Everything below is threading it to the
   callers that have an order; a caller with none is unchanged by construction.

8. **`weaponManager.calculateHitChange(shooter, target, weapon, calledid, fireOrder)`** — optional
   5th argument, used only in the ballistic geometry block, which sets `defence` (the target's
   profile for that bearing) and `distance`. The launch hex it resolves is also passed on to
   `computeShotModifiers` → `Ship.getHitChangeMod(shooter, weapon, launchPos)`, mirroring the
   server's `$posmod`, so arc-limited defensive systems are judged from the same bearing.

9. **Callers updated** (every one that holds an order): `calculataBallisticHitChange` reads
   `ball.fireOrder`; `getIncomingShotHitChange` and `ShipTooltipBallisticsMenu.getBallisticEntry`
   supply it; the INCOMING list's live breakdown passes `ball.fireOrder`; and both sweeps in
   `declarations.js` pass their `order`. Deliberately NOT changed: every `doMultipleFireOrders` /
   targeting-tooltip call in `model/weapon/*.js` and `SystemInfo.js`, which are pricing a shot the
   player has not declared yet and correctly launch from the ship.

---

## 5. Concealment — masking the mode until the first miss

**Ruling:** non-allied viewers see an un-missed homing missile as **Basic**.

**Site:** `TacGamedata::hideSystemFireOrders`
([TacGamedata.php:1453](source/server/model/TacGamedata.php#L1453)) — the per-order loop that
already computes `$isAlly` and already carries the `hidetarget` (Stealth missile) mask.

**Rule, stated precisely:**
> For a **current-turn** order on an `AmmoMissileRackS`-family weapon whose firing mode resolves to
> `'Homing'` and whose `damageclass` is **not** `'HomingMissile'` (i.e. this is pass 1), and where
> `!$isAlly`: rewrite `$fire->firingMode` to the index of the `'Basic'` mode in the same launcher's
> `firingModes`.

Consequences, all of which fall out for free because Basic and Homing are statistically identical:

- The enemy's hit-chance preview, ballistic icon, tooltip and interception maths all read "Basic"
  and produce numbers that are **correct for a Basic missile — and therefore also correct for the
  real missile.** No divergent-threshold machinery.
- A pass-2 order is not masked, so the missile reveals itself exactly when the rules say it does.
- Only the *current* turn is masked, per the standing `deleteHiddenData` convention. Once turn N is
  history the combat log and replay tell the truth — which is harmless: by then the missile has hit,
  been shot down, or come back and revealed itself. This is a deliberate choice **not** to follow
  the Chameleon precedent of masking on the `$all` replay path, because a permanent mask would make
  the combat log state something false about a resolved shot.
- Phase 1 needs nothing, and still does not since §2.5. A FRESH launch is stripped for everyone in
  Initial Orders, so there is nothing there to mask; the order §2.5 lets through is a RE-ATTACK,
  which this rule deliberately never masked anyway — surviving a pass is the reveal.

**⚠️ Fallback:** if the launcher somehow has no `'Basic'` mode (no ship in the tree is built this
way, but `recompileFiringModes` permits it), fall back to the lowest mode index rather than leaving
it unmasked.

**⚠️** The mask mutates the order object, but gamedata is built and APCu-cached **per player**
(`game_<gid>_user_<uid>_json`), so this is safe — it is exactly what every other rule in
`deleteHiddenData` does. See `[[arch_info_bleed_masking]]`.

---

## 6. Documentation (explicitly requested)

**File:** [ammo-options-enhancements.php](source/public/ammo-options-enhancements.php), the
*Shipborne Missiles* list. Inserted after the **Class H** line — the list runs roughly alphabetically
by class letter, so HM sits between H and I:

```html
<li><strong>Class HM - Homing Missile (2252)</strong> - Range 20 - Damage 20 - Fire Control:
+3/+3/+3 - Kor-Lyan only. If it misses without being shot down it stays in play and attacks again
next turn, launching from its target's previous hex. Defensive fire that would have turned a hit
into a miss destroys it instead. Lost when its cumulative travel exceeds its distance range.
The enemy is not told a missile is Homing until it has missed once.</li>
```

---

## 7. Build & verification

### What was actually run (2026-09-02)

| Gate | Result |
|---|---|
| `php -l` on all 16 edited PHP files, under `/usr/src/current` | clean |
| `fvbuild.ps1 -Autoload` | **+1 entry** (`ammomissilehm`), nothing else |
| `fvbuild.ps1 -Statics` | regenerated; **zero tracked-file changes** — statics are gitignored build artefacts, and the Homing mode only exists once a magazine actually holds Homing rounds, so no blueprint moved |
| `fvbuild.ps1 -Client` | both legacy bundles rebuilt; verified they carry the two `HomingMissile` and two `AMMO_HM` occurrences |
| Replay harness `check` | **131 passed, 0 failed** (game 4317 SKIP — advanced since record, unrelated) |
| Scratch harness 1 — unit (`c:\tmp\homing_test.php`) | **50/50 PASS** |
| Scratch harness 2 — integration (`c:\tmp\homing_test2.php`) | **48/48 PASS** |
| `fvbuild.ps1 -Check` (ship-data validator) | ⚠️ **1 pre-existing failure, not from this work** — see below |

### And again for §2.5 (2026-09-03)

| Gate | Result |
|---|---|
| `php -l` on the 4 edited PHP files, under `/usr/src/current` | clean |
| `node --check` on the 3 edited JS files | clean |
| `fvbuild.ps1 -Client` | both legacy bundles rebuilt; verified the game bundle carries `isHomingReattack`, `getHomingLaunchHex` and `getLaunchHex`, and that the **lobby** bundle carries none of them (nothing new is asked of the lobby's hand-written `weaponManager` stub — see `[[project_manual_interception]]`) |
| Replay harness `check` | **130 passed, 1 failed, 1 skipped.** The failure (game 4305) and the skip (4317) are both "the game advanced in the local DB since its baseline was recorded"; 4305 was re-run with all four homing PHP files reverted to HEAD and **fails identically on a clean tree**. Nothing in the `masking` check moved, which is the one this work could have disturbed. |
| Scratch harness 1 (`c:\tmp\homing_test.php`) | **51/51 PASS** (round-trip assertion extended for the 9th field) |
| Scratch harness 2 (`c:\tmp\homing_test2.php`) | **68/68 PASS** (20 new) |
| Scratch harness 3 — client (`c:\tmp\homing_client_test.js`) | **22/22 PASS** |
| `fvbuild.ps1 -Autoload` / `-Statics` | **not run — nothing to regenerate.** No new class, no blueprint change. |
| `fvbuild.ps1 -Check` (ship-data validator) | ⚠️ same single pre-existing `triadFiend` failure as below, unchanged |

### And once more for §2.5a (2026-09-03) — client only

| Gate | Result |
|---|---|
| `node --check` on the 4 edited JS files | clean |
| `fvbuild.ps1 -Client` | both bundles rebuilt and re-parsed; the **lobby** bundle still carries none of the new helpers, so the lobby's hand-written `weaponManager` stub needs nothing |
| Scratch harness 4 — hit chance (`c:\tmp\homing_hitchance_test.js`) | **14/14 PASS**, and it reproduces the reported numbers exactly |
| Harnesses 1–3 | unchanged and green |
| Replay harness / `-Check` | **not re-run — no PHP changed in this pass.** |

⚠️ **`checkShipData` fails on `triadFiend` on this tree, and it is nothing to do with the Homing
Missile.** [triadFiend.php:52](source/server/model/ships/triad/triadFiend.php#L52) has its
`SolarBlaster` system **commented out** while the hit chart at line 80 still routes roll 7 to
"Solar Blaster", so every hit there is silently rerouted to Structure. The baseline also shows
`triadArchangel` and `triadDevil` as *fixed since recorded* — the same bug, already corrected on
those two hulls. All three files are unmodified in this working tree. **The baseline was
deliberately NOT re-recorded**: doing so would fold somebody else's live bug into the accepted set.

### The scratch harnesses
Both live in `c:\tmp` (not added to the repo) and run with no database:
`docker exec -w /usr/src/current fieryvoid-php-1 php /usr/src/current/<copied file>` — they
`require` `source/autoload.php` directly, the way the replay harness does, so `global.php`'s load
guard never sees them. Harness 1 covers the ammo class and the six rack overrides; harness 2 covers
the note→order rebuild (including the anti-duplicate guard, the stale-note and dead-target cases)
and the per-viewer mode mask from all four viewpoints, plus §3.2a's derived-rack regression.

**A FOURTH harness landed with §2.5a**: `c:\tmp\homing_hitchance_test.js`, run the same way. It
loads the REAL `mathlib`, `hexagon.Cube/Axial/Offset`, `coordinateConverter` and `weaponManager` and
stubs only the game world around them, then reproduces game 4328 turn 3 exactly — a target whose
front/side profiles are 17/16, a launcher off its bow and a missile off its beam — and asserts 40%
and 35%, the two `chance:` figures in that turn's own fire-order notes. Reverting the per-order
launch hex turns 6 of its 14 assertions red, one of them reporting the literal 40% the player saw.

**A third harness landed with §2.5**, and this one is JavaScript: `c:\tmp\homing_client_test.js`,
run with plain `node` (no browser, no DOM). It loads the real `weaponManager.js` into a `vm` context
with a hand-built minimum world — `hexagon.Offset`, `gamedata`, `shipManager`, a stub jQuery — and
drives the predicates §2.5's client half changed. Run:
`node c:\tmp\homing_client_test.js c:\FV_env\FieryVoid`.

> **⚠️ Keep every stub NO RICHER THAN THE REAL THING.** The first version of this harness gave its
> `Offset` an `x`/`y` alongside `q`/`r`; the real class has only `q`/`r`, and the extra fields let a
> `launchHex.x` in the group key pass every test while reading `undefined` in the game.

**Six fixes now have proven teeth** — each was reverted, its tests watched go red, and restored:

| Revert | Goes red |
|---|---|
| `AmmoMissileRackF`'s two `parent::` calls (§3.2a) | "RackF rebuilds a missile in flight", "RackF writes the survival note" |
| the `$homingStates` carrier (§2.3 bug 1) | 8 assertions, incl. "an adopted order keeps its identity" reporting the re-attack's own id — the exact game-4328 symptom |
| the captured fuel budget (§2.3 bug 2) | "a missile in the air keeps its launch-time tank" |
| `persistNextPassOrder` (§2.5) | 7 assertions — no row inserted, no `orderid` in the note, no row for either of the two identical missiles |
| adoption by row id (§2.5) | "row 90501 carries the state of missile 111" — the field match pairs the notes with the wrong rows, and reports it |
| `withdrawHomingOrder` (§2.5) | "...and is detached when its target has gone" |
| the phase-1 exemption (§2.5) | "a missile in the air IS shown in Initial Orders" — both viewpoints |
| the POSTed-re-attack reject (§2.5) | "a POSTed re-attack is rejected", "...and detached from the launcher" |
| `getIncomingSourcePos`'s homing branch (§2.5, client) | "a re-attack bears from its launch hex", "...and the order wins over a stale ball.position" |
| `removeFiringOrder`'s guard (§2.5, client) | "clearing the weapon keeps the missile in flight" |
| `getFiringHex`'s per-order branch (§2.5a) | 6 assertions, incl. "a re-attack previews off ITS OWN launch hex" reporting **40** — the exact number the player was shown |
| the `launchPos` handed to `getHitChangeMod` (§2.5a) | "defensive systems are tested from the missile hex" |

### Standing gate list

| Step | Why |
|---|---|
| `php -l` every edited PHP file, **under `/usr/src/current`** | Linting `/usr/src/fieryvoid` reports a clean pass on the *pre-edit* file. `[[howto_docker_db_access]]` |
| `fvbuild.ps1 -Check` | Runs `checkShipData.php` + the replay harness. |
| Re-record `checkShipData.php` baseline | The 11 Kor-Lyan hulls gain an `enhancementOptions` entry. `[[project_ship_data_validator]]` |
| Replay harness `check`, then `record` | The `masking` check fingerprints `deleteHiddenData`; the corpus has no homing missile so the baseline should be **unchanged**. A diff here means the new mask leaked into unrelated orders. `[[project_replay_harness]]` |
| Regenerate the static ship file | Blueprints for the 11 hulls change. `[[arch_static_generator_streaming]]` |
| Rebuild legacy bundles, do not commit them | `[[howto_minify_legacy_bundles]]`, `[[feedback_fv_workflow]]` |

### Play-test script (needs two turns minimum)
1. Kor-Lyan ship with `AMMO_HM` bought, fire one Homing missile at a target that will move.
2. **Turn N phase 2/3:** confirm the *enemy* client labels the incoming missile **Basic**, and the
   owner's client labels it **Homing**.
3. Force a clean miss (long range / high jink / no interceptors). Confirm the combat log says the
   missile remains in play, and confirm the **launcher reloads normally** — this is the
   `firedOnTurn` override, and getting it wrong is silent.
4. **Turn N+1 phase 1 (§2.5):** the missile is on the board *before anybody commits* — launch hex,
   target marker and line — for **both** players, and its mode reads **Homing** to both.
   Then, still in Initial Orders, with the same rack: **launch a fresh missile.** The one in flight
   must stay on the map (that is `removeFiringOrder`'s guard), the rack must not read as already
   fired, and committing must **not** produce a duplicate row — check
   `SELECT id,turn,x,y,damageclass FROM tac_fireorder WHERE gameid=<g> AND damageclass='HomingMissile'`
   and confirm exactly one row per missile per turn.
5. **Turn N+1 phase 2:** the incoming line is drawn from the *target's previous hex*, not the
   shooter's.
6. **Two missiles at once is the case that was broken.** With two homing missiles chasing the same
   ship (launch on two consecutive turns and miss with both), confirm in phase 3 that:
   - **both** launch hexes are drawn, not one (§2.5 symptom 2);
   - the INCOMING list shows them as two shots, and interception declared on one moves **only that
     one's** hit chance (§2.5 symptom 3a);
   - after resolution the `tac_fireorder` intercept rows name a real order id in `targetid`, not
     `-1`, and the automation has not quietly replaced the player's assignment (§2.5 symptom 3b).
7. Assign an interceptor. Confirm a roll in the intercept band produces "shot down" and no note is
   written for the next turn.
8. Separately: let a missile chase until cumulative travel exceeds 60 and confirm it is lost with a
   fuel message.
9. Confirm the magazine is decremented **once**, at launch only — the re-attack must never draw a
   second round.

---

## 8. Traps, gathered

1. **`firedOnTurn` blocks the launcher.** §3.2 item 7. Silent; only shows up as "my rack never
   reloads".
2. **A synthetic damage-dealing order with `id == -1` mislogs its damage.** §2.2.
3. **`notekey_human` > 40 chars is a fatal that kills the whole submission**, not a truncation.
4. **`generateIndividualNotes` runs on POST-side ships** in phases 1 and 3, where enhancements are
   not applied and notes are not loaded. Phase-4 gating is what keeps this correct.
5. **`advance()` has already set the next phase** — but `FireGamePhase::advance` `setPhase(4)`
   *before* its server-side load, so `phase == 4` is a valid and unambiguous gate here. Do not
   generalise this to the other phases.
6. **Client fire-order `notes` are dropped on POST** for player-submitted orders on the fighter
   branch, and `pubnotes`/`chance`/`hitmod` on both. Irrelevant here — every order carrying homing
   state is server-created — but do not later "simplify" by having the client carry the state.
   `[[arch_fireorder_notes_dropped_on_post]]`
7. **`$gamedata->turn` is a STRING out of mysqli.** Cast both sides of every turn comparison in the
   note-matching code.
8. **`AmmoMissileRackA` / `AmmoMissileRackD` reset `$ammoClassesArray`** and must not be given
   Homing. Adding it to the parent constructor is automatically correct here — just do not "helpfully"
   add it to their lists too.
9. **`hasSpecialLaunchHexCalculation` is class-wide**, not per-order. Using it for the homing launch
   hex would also disable the LoS FC penalty for ordinary launches from the same rack.
10. **`mathlib::getDistanceHex` returns a FLOAT, not an int** — `CubeCoordinate` stores its axes as
    floats, so every hex distance in FV is `float(4)` rather than `int(4)`. Found by the scratch
    harness. The cumulative travel is round-tripped through a note, a `HOM:` tag and a
    player-visible message, so `resolveHomingOutcome` rounds it to a whole number of hexes ONCE,
    where it is computed. Do not remove that `(int)round(...)`; a bare cast further downstream
    could shave a hex off a `3.9999999`.
11. ⚠️⚠️ **`$fireOrder->notes` is ASSIGNED by `Weapon::calculateHitBase`, not appended**
    ([weapon.php:1927](source/server/model/weapons/weapon.php#L1927)) — so it can carry nothing of
    your own past the hit-chance maths. This is a general fact about fire orders, not a homing
    quirk: `pubnotes` is appended to and survives, `notes` does not. See §2.3 bug 1.
12. **A launcher's `$rangeArray`/`$distanceRangeArray` are MUTABLE at runtime.**
    `AmmoMissileRackF::recalculateFireControl` rewrites them as its loading state changes, so
    anything that must reflect launch conditions has to be captured at launch, not read back later.
    See §2.3 bug 2.
13. **A subclass that overrides a note hook without chaining to `parent::` kills the feature
    silently.** This is what `AmmoMissileRackF` did (§3.2a) — no error, no log line, the missile
    just never comes back. Any new `AmmoMissileRackS` subclass must chain
    `onIndividualNotesLoaded`, `generateIndividualNotes` and `beforeFiringOrderResolution`.
14. **The Ballistic record built at [TacGamedata.php:234](source/server/model/TacGamedata.php#L234)
    uses the shooter's movement for its launch position**, which is wrong for a re-attack. Harmless
    today — `$this->ballistics` is absent from `stripForJson` and its only consumer is the
    `hidetarget` mask — but do not start reading launch positions from it.
15. ⚠️⚠️ **A SERVER-GENERATED ORDER WITH `id == -1` HAS NO IDENTITY, AND SEVERAL CAN CARRY IT AT
    ONCE.** Three separate client and server mechanisms address a shot by its fire-order id — the
    ballistic icon's duplicate test, an `intercept` order's `targetid`, and
    `Firing::automateIntercept`'s `$ordersById` — so two id −1 shots are one shot to all of them,
    silently. This is not a homing quirk: it applies to anything synthesised server-side that a
    player is allowed to react to. Give it a row before it is shown. §2.5.
16. ⚠️ **PHASE 1 IS THE ONE PHASE `DBManager::submitFireorders` INSERTS A BALLISTIC ORDER REGARDLESS
    OF `->addToDB`.** Anything made visible during Initial Orders is therefore re-inserted verbatim
    when its owner commits, and could be forged in the POST. Make a server-generated ballistic
    visible there and you must also reject it in `Firing::validateFireOrders`. §2.5.
17. ⚠️ **`hexagon.Offset` has `q`/`r` and no `x`/`y`**, and a movement row's `.position` is one.
    Reading `.x` off a hex is `undefined`, throws nothing, and quietly makes any key or comparison
    built on it constant. A test stub that invents the missing fields will not catch it. §2.5.
18. ⚠️⚠️ **`weaponManager.getFiringHex` IS PER-WEAPON; `Weapon::getFiringHex` IS PER-ORDER.** The
    client one takes `(shooter, weapon)` and the server one takes `(gamedata, fireOrder)` — a
    divergence that is invisible until a weapon has an order whose launch hex is not its launcher's,
    and then shows up as a preview that quietly disagrees with the dice by exactly one defence
    profile. Pass the order (3rd argument) wherever you have one. The same hex has to reach
    `getHitChanceMod` as well: it is the server's `$posmod`, and it decides which ARC-LIMITED
    defensive systems cover the shot. §2.5a.
19. ⚠️ **`source/public/client/model/ship.js` has TWO `Ship.prototype = {...}` blocks and the
    SECOND is dead** (inside a `//OLD VERSION - CHANGED DEC 2025` comment). Editing the wrong copy
    of `getHitChangeMod` / `getHitChangeModFlight` parses, diffs convincingly, and does nothing.

---

## 9. Files touched

| File | Change |
|---|---|
| `source/server/model/systems/baseSystems.php` | new `AmmoMissileHM` class + its homing methods; **§2.5:** the 9th packed field (`orderid`) and a `$turn` argument on `buildReattackOrder` |
| `source/server/model/weapons/missile.php` | `AmmoMissileRackS`: 1 ctor line + 6 overrides; `AmmoMissileRackF`: 2 missing `parent::` calls (§3.2a); **§2.5:** `persistNextPassOrder`, `withdrawHomingOrder`, adoption by row id |
| `source/server/model/ships/Enhancements.php` | option definition + apply-switch case |
| `source/server/model/ships/korlyan/*.php` (×11) | one `enhancementOptionsEnabled` line each |
| `source/server/model/TacGamedata.php` | the pass-1 mode mask in `hideSystemFireOrders`; **§2.5:** the phase-1 strip exemption |
| `source/server/handlers/firing.php` | **§2.5 only:** `validateFireOrders` rejects a POSTed re-attack; `automateIntercept` will not index an order by a non-positive id |
| `source/public/client/renderer/icon/BallisticIconContainer.js` | per-order launch hex branch; **§2.5:** a re-attack's `position` is not a target hex |
| `source/public/client/weaponManager.js` | **§2.5:** `isHomingReattack` / `getHomingLaunchHex` and their seven call sites (§4.1); **§2.5a:** `getFiringHex`, `calculateHitChange` and `computeShotModifiers` take the fire order / its launch hex |
| `source/public/client/model/ship.js` | **§2.5a only:** `getHitChangeMod` / `getHitChangeModFlight` take an optional per-order launch hex (⚠️ the FIRST prototype block — the second is dead) |
| `source/public/client/declarations.js` | **§2.5a only:** both sweeps pass their `order` to `calculateHitChange` |
| `source/public/client/UI/ShipTooltipBallisticsMenu.js` | **§2.5:** per-order launch hex in the INCOMING list, the group key and each member; **§2.5a:** the order is carried on the ballistic entry and into the live breakdown |
| `source/public/client/lobbyEnhancements.js` | two `AMMO_HM` switch cases |
| `source/public/ammo-options-enhancements.php` | the doc entry (do this LAST, per the request) |

**Untouched, deliberately:** `Weapon::fire`, `FireGamePhase`, `InitialOrdersGamePhase`, `DBManager`,
`Manager`. All homing *behaviour* still lives on `AmmoMissileHM` and `AmmoMissileRackS`; the two
`Firing::` lines added in §2.5 are guards that refuse things, not rules.

---

## 10. Left alone, and worth a separate look

- **`AmmoMissileRackD` writes `$this->FiringModes` (capital F) when it has no ammo available**
  ([missile.php:1364](source/server/model/weapons/missile.php#L1364)) — a typo for `$firingModes`,
  so it creates a dynamic property. Harmless as a PHP 8.2 deprecation notice on its own, but
  `Manager.php` registers a file-scope `set_error_handler` that throws `ErrorException`
  **unconditionally**, so under a real request this is a fatal, not a notice. Surfaced by the §2.5
  harness (which loads `Manager` to install a stub DBManager) and is entirely pre-existing —
  nothing to do with Homing, which is why it was left alone.
