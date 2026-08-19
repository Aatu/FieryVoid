# Manual Interception — reintroducing player-directed intercept assignment

Restores the pre-automation ability to **select intercept-capable weapons in the Firing phase and
click an incoming shot in the ship tooltip's INCOMING list** to commit those weapons against that
specific shot. Automated interception stays as the default for everything the player does not
hand-assign.

Status: **PLAN ONLY — nothing built.**

Decisions taken (2026-08-18, user):

| # | Question | Ruling |
|---|---|---|
| D1 | Manual interceptors are counted before automation, so they take the undegraded slots | **Keep.** Automated intercepts *should* degrade behind hand-picked ones. Niche today, but it future-proofs the design — see §2.1 |
| D2 | Grouped rows (`3x Missile`) — how does one click allocate? | **Greedy fill:** stack onto one shot until its hit chance reaches ≤ 0%, then move to the next member. **Plus** a per-row dropdown listing the individual shots for exact control |
| D3 | May a **ballistic** weapon (missile rack with Interceptor missiles) be manually assigned in phase 3? | **Yes**, if it has fired nothing this turn. It then cannot fire offensively. Needs real UI work — see §4.7 |
| D4 | One click to convert an existing `selfIntercept` marker into a targeted manual intercept? | **Yes** — same consent, same resulting order |
| D5 | Surface server-side drops to the player? | **No.** `Debug::log` only; make the client predicate strict enough that drops are near-impossible |

---

## 0. The headline finding

**The server already supports manual intercept orders end to end.** `Firing::automateIntercept`
opens by folding every pre-existing `type = 'intercept'` order into the intercepted shot's
`totalIntercept` / `numInterceptors` before it assigns anything itself
([firing.php:320-335](source/server/handlers/firing.php#L320)), and the comment on that loop
literally says *"manually assigned interception - no others exist at this point"*. The commit path
persists intercept orders ([DBManager.php:1361](source/server/controller/DBManager.php#L1361)), the
POST rebuilder carries them ([Manager.php:2018](source/server/controller/Manager.php#L2018)), and
`firedOnTurn()` already excludes a manually-committed weapon from the automation pool
([weapon.php:805](source/server/model/weapons/weapon.php#L805)).

So this is **not** a "build a new mechanic" job. It is:

1. a **client UI + declaration** job (the dormant half), and
2. a **server validation** job — because manual intercept orders are currently accepted with **no
   legality check whatsoever**. `Firing::isLegalIntercept` is only ever called from
   `getBestInterception`, i.e. the *automated* path. Today a hand-crafted intercept order would be
   honoured with zero arc, range, uninterceptable, skindancing, ammo, readiness or ownership
   checking. That hole is harmless only because nothing currently emits such orders. **Reintroducing
   the feature makes that path hot, so the validation must land first.**

---

## 1. What exists today

### 1.1 Client — the dormant feature

| Piece | Where | State |
|---|---|---|
| The row template | [ShipTooltipBallisticsMenu.js:6](source/public/client/UI/ShipTooltipBallisticsMenu.js#L6) | `.interception` span and `.intercept` button **removed** from the HTML string; the old string is kept commented above it |
| The kill switch | [ShipTooltipBallisticsMenu.js:11](source/public/client/UI/ShipTooltipBallisticsMenu.js#L11) | `this.allowIntercept = false; //obsolete, actually` — the ctor argument is accepted and then thrown away |
| The render block | [ShipTooltipBallisticsMenu.js:203-230](source/public/client/UI/ShipTooltipBallisticsMenu.js#L203) | Whole block commented out (the code highlighted in the request) |
| `weaponManager.targetBallistic` | [weaponManager.js:2554](source/public/client/weaponManager.js#L2554) | **Live and never called.** Builds `type: 'intercept'` orders. Needs work (§3) but is the right skeleton |
| `weaponManager.getInterception` | [weaponManager.js:1079](source/public/client/weaponManager.js#L1079) | Live, never called, and broken (§3 T10) |
| `getBallisticEntry` | [ShipTooltipBallisticsMenu.js:16](source/public/client/UI/ShipTooltipBallisticsMenu.js#L16) | Live — used by the hit-chance display. Produces exactly the `{weaponid, targetid, shooterid, fireOrderId, position}` shape `targetBallistic` expects |
| `isPosOnWeaponArc` | [weaponManager.js:947](source/public/client/weaponManager.js#L947) | Live; its split-arc branch was written **specifically** for "picking intercept targets off incoming ballistics" (see its comment at L961) |
| Call sites passing `allowIntercept: true` | [FirePhaseStrategy.js:60,71,86,99,105](source/public/client/renderer/phaseStrategy/FirePhaseStrategy.js#L60) and [PreFiringPhaseStrategy.js:62,77,90,96](source/public/client/renderer/phaseStrategy/PreFiringPhaseStrategy.js#L62) | Already pass `true`; the hover fallback in [PhaseStrategy.js:650](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L650) passes `false` |
| Weapon-selection gate | [SystemIcon.js:243](source/public/client/UI/reactJs/system/SystemIcon.js#L243) | `gamephase === 3 && !ballistic && !preFires` — **blocks D3 outright**; see §4.7 |
| CSS for `.intercept` / `.interception` | [styles/shipTooltip.css](source/public/styles/shipTooltip.css) | **Does not exist.** Legacy rules were lost in the visual-unification pass; new rules must be written |

### 1.2 Client — the self-intercept sibling (the pattern to copy)

Long-recharge weapons already opt into interception by hand, from the ship window:

| Piece | Where |
|---|---|
| Eligibility | `canSelfInterceptSingle` [weaponManager.js:2665](source/public/client/weaponManager.js#L2665) |
| Declare one / all | `onDeclareSelfInterceptSingle` [:2681](source/public/client/weaponManager.js#L2681), `…SingleAll` [:2710](source/public/client/weaponManager.js#L2710) |
| Withdraw one | `canRemInterceptSingle` [:2735](source/public/client/weaponManager.js#L2735) / `removeSelfInterceptSingle` [:2747](source/public/client/weaponManager.js#L2747) |
| Buttons | [SystemInfoButtons.js:252,260,908,912](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L252) |
| Split-weapon hooks | `doMultipleSelfIntercept` / `checkSelfInterceptSystem` / `recalculateForIntercept` / `checkFinished` — [shipSystem.js:52,56,468,485](source/public/client/model/shipSystem.js#L468), overridden in molecular.js, pulse.js, special.js, particle.js |

A **selfIntercept** order is only a *permission marker* — "this long-recharge weapon consents to be
auto-assigned". A **manual intercept** order is a real, targeted commitment. They are different
things and both must keep working (D4 converts one into the other).

### 1.3 Server — the live machinery

| Piece | Where | Relevance |
|---|---|---|
| `automateIntercept` | [firing.php:308](source/server/handlers/firing.php#L308) | Called once, from [FireGamePhase.php:16](source/server/Phase/FireGamePhase.php#L16), after `prepareFiring` |
| — manual-order totals loop | [firing.php:320-335](source/server/handlers/firing.php#L320) | **This is where manual orders are consumed. No validation.** |
| — automation pool | `getUnassignedInterceptors` [firing.php:141](source/server/handlers/firing.php#L141) | Excludes a weapon that `firedOnTurn()`; split weapons stay in with reduced gun count |
| — readiness | `isValidInterceptor` [firing.php:380](source/server/handlers/firing.php#L380) | **Automation only.** Requires a `selfIntercept` marker for `loadingTimeActual > 1` |
| — legality | `isLegalIntercept` [firing.php:494](source/server/handlers/firing.php#L494) | **Automation only.** Arc (incl. split arcs and turret jam), uninterceptable, doNotIntercept, ballisticIntercept, mines, skindancing, freeintercept geometry, fighter-escort geometry, and `$weapon->canInterceptAtAll()` — which is where a missile rack checks its magazine |
| — accounting | `addToInterceptionTotal` [firing.php:285](source/server/handlers/firing.php#L285) | Adds `getInterceptionMod`, bumps `numInterceptors`, calls `fireDefensively` (backlash — **and the interceptor-missile ammo draw**), optionally creates the order |
| Degradation | `getInterceptionMod` [weapon.php:777](source/server/model/weapons/weapon.php#L777) | See §2.1 |
| Final application | [weapon.php:2094](source/server/model/weapons/weapon.php#L2094) | `$fireOrder->needed -= $fireOrder->totalIntercept` |
| Cooldown bookkeeping | `firedOnTurn` [weapon.php:805](source/server/model/weapons/weapon.php#L805) | A `type='intercept'` order returns **true** from the first branch, so recharge/cooldown works for free — no `checkForSelfInterceptFire` entry needed (that class only serves `selfIntercept`) |
| Interceptor-missile mode | `MissileLauncher::switchModeForIntercept` [missile.php:1563](source/server/model/weapons/missile.php#L1563) | Automation picks the best intercept mode itself. The manual path must do the same client-side — see §4.7 |
| Interceptor-missile ammo | `canInterceptAtAll` [missile.php:1578](source/server/model/weapons/missile.php#L1578) → `AmmoMagazine::canDrawInterceptor` [baseSystems.php:10904](source/server/model/systems/baseSystems.php#L10904); drawn in `fireDefensively` [missile.php:1590](source/server/model/weapons/missile.php#L1590) | Checked and consumed **per order, in sequence** — which dictates where the new validation goes (§5 Stage 0) |
| Masking | `hideSystemFireOrders` [TacGamedata.php:1131](source/server/model/TacGamedata.php#L1131) | In phase 3 every current-turn non-ballistic order is stripped from the payload for **everyone**. Fire-phase orders live client-side until commit, so this is correct and needs no change |

`Weapon::getIntercept` ([weapon.php:1980](source/server/model/weapons/weapon.php#L1980)) is a dead
parallel implementation — nothing reads its return. Do not "fix" it into the path; `totalIntercept`
is authoritative.

---

## 2. Rules the feature must enforce

Stated once here so the code never re-derives them.

### 2.1 Interception degradation, in plain terms (D1)

Every interceptor has an **Intercept Rating** in d20 points, multiplied by 5 to give a to-hit
penalty. Rating 4 = **−20%** on the incoming shot.

**Degradation** is the rule that when several weapons pile onto the *same* shot, each one after the
first is worth 1 point (5%) less than its rating, cumulatively
([weapon.php:794](source/server/model/weapons/weapon.php#L794)):

> three rating-4 weapons on one shot → −20%, −15%, −10% = **−45%**, not −60%.

The important half: **degradation is switched off against ballistics.** The test is
`doInterceptDegradation || !(ballistic || noInterceptDegradation)`. A missile is `ballistic`, so
every interceptor assigned to it gives its **full** rating.

So D1 — "manual interceptors are counted first and therefore take the undegraded slots" — is
**nearly moot for this feature**, because the INCOMING list only ever shows ballistics and Sweeping
shots, and every Sweeping shot in the game today is a Molecular Slicer, which is `uninterceptable`.
The only shots where the ordering can bite are the two Nexus Laser Missiles, which set
`doInterceptDegradation = true` ([customNexus.php:350,453](source/server/model/weapons/customNexus.php#L350)).
Against those, hand-picked weapons get full value and the automation stacked on afterwards degrades.

**Ruling: keep the current ordering — automated intercepts degrade behind the hand-picked ones.**
No code change. It is the right principle regardless of how rarely it fires today: the player's
deliberate choice is the "first" interceptor, and the automation fills in behind it. Recording it
here means any future weapon that lifts `ballistic`/`noInterceptDegradation` — or any future
decision to make direct fire interceptable from this menu — inherits the correct behaviour instead
of re-opening the question.

### 2.2 The rules

**R1 — Phase.** Manual interception is declared in the **Firing phase (gamephase 3) only**. Not
Pre-Firing (5): interception is assigned in `FireGamePhase::advance`, and Pre-Firing resolves a
phase earlier with no intercept pass of its own.

**R2 — A committed weapon is spent.** A weapon that has manually intercepted has fired. It cannot
also fire offensively, and it is no longer in the automation pool. Already true mechanically:
`firedOnTurn()` sees the order, and client-side `hasFiringOrder`
([weaponManager.js:3555](source/public/client/weaponManager.js#L3555)) returns `true` for it.

**R3 — Split-shot weapons allocate per gun.** A `canSplitShots` weapon (Twin Array in *Split* mode,
Quad Array, Discharge Gun, Slicers…) spends **one gun per manual intercept**. Remaining guns may
fire offensively, manually intercept another shot, or be left in the automation pool. This falls out
of the existing accounting for free — see §4.3 — provided the client emits **one order per click**
for these weapons.

**R4 — Non-split weapons commit every gun to the shot they were pointed at.** One click =
`weapon.guns` intercept orders against the chosen shot. This mirrors the automation's per-gun loop
([firing.php:367](source/server/handlers/firing.php#L367)) in count, and differs from it only in
that automation may spread a weapon's guns across several shots while a manual assignment keeps them
together — which is the point of assigning by hand.

**R5 — Legality is the same test as automation.** Manual assignment does **not** relax
`isLegalIntercept`. It *does* relax `isValidInterceptor`'s `selfIntercept`-marker requirement for
long-recharge weapons — declaring by hand **is** the consent that marker stands in for.

**R6 — Interception rating is read in the order's firing mode.** Several weapons carry a per-mode
`interceptArray` ([shipSystem.js:245](source/public/client/model/shipSystem.js#L245),
[weapon.php:2978](source/server/model/weapons/weapon.php#L2978)). The rating that counts is the one
for `$fire->firingMode`, not whatever mode the object happens to be in when the loop reaches it.

**R7 — Ballistic and pre-firing weapons may intercept if they have fired nothing this turn (D3).**
A missile rack loaded with Interceptor missiles that made no launch in Initial Orders is a valid
manual interceptor in phase 3. Having declared *anything* offensively this turn disqualifies it, and
having manually intercepted disqualifies it from a later launch — R2 in both directions.

**R8 — No new information reaches the client.** The INCOMING list already shows public data
(ballistic launches, public once Initial Orders close). The only thing manual interception adds to
the tooltip is *the player's own uncommitted orders*, which live only in that browser. Nothing in
[arch_info_bleed_masking] changes.

---

## 3. What is wrong with `targetBallistic` today

It is the right skeleton, but it was written before several of the systems it now has to respect.
Reading [weaponManager.js:2554-2615](source/public/client/weaponManager.js#L2554):

| # | Problem | Fix |
|---|---|---|
| T1 | No check on the **incoming** weapon: `uninterceptable` (every Molecular Slicer is), `doNotIntercept` (fields, ramming), `hextarget`. The Sweeping rows in this very list are Slicers — i.e. **the most visible rows are the ones that must refuse** | Resolve the incoming weapon and test `uninterceptable && !weapon.canInterceptUninterceptable`, `doNotIntercept`, `hextarget` |
| T2 | No `weapon.ballisticIntercept` check (weapon that may only intercept ballistics) | Mirror [firing.php:538](source/server/handlers/firing.php#L538) |
| T3 | No `weapon.stowed` check (docked Kirishiac Orbital) | Add — `canSelfInterceptSingle` already does |
| T4 | No `shipManager.power.isOffline` check | Add |
| T5 | Emits `weapon.guns` orders **unconditionally** — wrong for split weapons (R3) | One order for `canSplitShots`, `guns` orders otherwise |
| T6 | Calls `removeFiringOrder` unconditionally, which on a split weapon wipes its offensive shots and on a `multiModeSplit` weapon detours through `removeAllMultiModeSplit` | Skip entirely for `canSplitShots`; keep the retarget semantics only for non-split |
| T7 | Free-intercept branch checks distance but **not team**; the server requires `$target->team == $interceptingShip->team` ([firing.php:659](source/server/handlers/firing.php#L659)) | Add the team check so the client cannot offer an order the server will drop |
| T8 | Fighter-escort branch uses `shipManager.isEscorting` ([ships.js:951](source/public/client/ships.js#L951)) which tests **start-of-turn position only**; the server requires same hex **now and at end of previous turn**, refuses fighter-on-fighter, and requires the incoming weapon to be ballistic ([firing.php:606-640](source/server/handlers/firing.php#L606)) | Tighten the client test to match |
| T9 | No cap: nothing stops a weapon accumulating more intercept orders than it has guns, or a missile rack committing more interceptors than the magazine holds | Cap on `guns` minus existing current-turn orders; plus the ammo mirror (§4.7) |
| T10 | `getInterception` is invoked in the dead code as `getInterception(ball.fireOrder)` — but the function reads `ball.fireOrderId`, which a `fireOrder` does not have. **It has always returned 0.** It also ignores degradation | Replace with a corrected helper (§4.5) |
| T11 | The legacy `hasIntrceptingWeaponsSelected` test is **global** ("is any selected weapon an interceptor at all"), so the button lit up on rows the click then silently no-opped on | Make the enable test per-row, using the same predicate the declaration uses (§4.1) |

---

## 4. Design

### 4.1 Where the control lives

Per **row** of the INCOMING list in the ship tooltip. The row template regains three children:

```
<div>
  <span class="ballexpand"></span>     <!-- ▶ / ▼ disclosure, grouped rows only -->
  <span class="weapon"></span>
  <span class="hitchange"></span>
  <span class="interception"></span>   <!-- "Committed −15%" -->
  <button class="intercept"></button>  <!-- INTERCEPT / CANCEL, disabled with a reason -->
</div>
```

Availability predicate for the controls, evaluated per row:

```
this.allowIntercept                      // ctor arg, honoured again
  && gamedata.gamephase === 3            // R1 — belt and braces, independent of the call site
  && this.selectedShip
  && gamedata.isMyShip(this.selectedShip)
  && !shipManager.isDestroyed(this.selectedShip)
  && rowIsInterceptable(ball)            // T1/T2 — the incoming shot admits interception at all
```

The button is **enabled** iff at least one currently-selected weapon can legally commit to this row
— the same per-weapon predicate the declaration loop applies (T11). When it is disabled, it carries
the reason as a `title` (`Uninterceptable`, `No interceptor selected`, `Out of arc`, `All shots
suppressed`) rather than vanishing, so rows do not reflow as the selection changes.

`PreFiringPhaseStrategy`'s four call sites keep passing `true`; the internal `gamephase === 3` gate
is what actually shuts it off there. That keeps the two strategies textually identical (they are
near-clones today) and means a future phase renumber cannot silently re-enable it.

### 4.2 Grouped rows: greedy fill plus a dropdown (D2)

`groupByOriginAndHitChange` ([ShipTooltipBallisticsMenu.js:26](source/public/client/UI/ShipTooltipBallisticsMenu.js#L26))
collapses identical shots into one row with an `amount`. It postdates the legacy intercept code, so
the old block never had to answer *"which of these 3 identical missiles does this button hit?"*.

**Change the grouper to keep its members.** Return `{ballistic, members: [...], amount}` where
`members` are the individual ballistic entries sorted by fire-order id — a two-line change (push
instead of `amount++`, derive `amount = members.length`). The group key already includes shooter,
weapon, firing mode and base hit chance, so members are interchangeable except for their ids.

**Collapsed row — greedy fill.** One click walks the eligible selection, strongest first, and
assigns each weapon to the **focus shot**: the lowest-id member whose post-interception hit chance
is still above 0. When that member reaches ≤ 0%, the focus advances to the next member and the
remaining weapons continue there. This is "stack on one shot until it hits 0% or lower" applied per
weapon rather than per click, so nothing is wasted on a shot that is already suppressed. Weapon
order is by intercept rating descending then system id, mirroring the server's
`compareInterceptAbility` ([firing.php:125](source/server/handlers/firing.php#L125)) so client and
server agree on what "strongest" means.

Allocation is at **weapon** granularity, not gun granularity: a non-split weapon's guns all follow it
onto the same shot (R4).

**Expanded row — exact control.** The `▶` toggle expands a group of 2+ into one sub-row per member:

```
▼  Sharlin, 3x Heavy Missile (Normal)     Between: 0% – 45%
     Shot 1    45% → 0%    (−45%, 3 weapons)    [CANCEL]
     Shot 2    45% → 45%                        [INTERCEPT]
     Shot 3    45% → 45%                        [INTERCEPT]
```

A sub-row's `INTERCEPT` commits the whole eligible selection to **that** member only — no greedy
fill, because the player has been explicit. `CANCEL` removes this ship's intercept orders against
that member.

Expansion state lives in `this.expandedGroups = {}` on the menu instance, keyed by group key. It
survives the in-place re-render after a click (the menu object outlives the render); it does not
survive re-selecting the ship, since the phase strategy constructs a new menu per `selectShip`.
That is acceptable and cheaper than a global.

Sub-rows sit inside the same `.incoming` container, so `attachHitChanceTooltipDelegation` covers
them for free.

### 4.3 Split weapons: why R3 is nearly free

Walk a Twin Array (`guns = 2`, `canSplitShots` only in mode 2, `intercept = 2` in both modes —
[particle.php:190-206](source/server/model/weapons/particle.php#L190)):

* Client emits **1** intercept order (R3). `TwinArray.checkFinished`
  ([particle.js:81](source/public/client/model/weapon/particle.js#L81)) tests
  `fireOrders.length >= guns` — the intercept order lives in the same array, so the weapon correctly
  reports 1 shot left. `doMultipleFireOrders` ([particle.js:29](source/public/client/model/weapon/particle.js#L29))
  guards on the same count, so the second gun can still take an offensive split shot.
* Server: `getUnassignedInterceptors` keeps split weapons in the pool even when `firedOnTurn`
  ([firing.php:180](source/server/handlers/firing.php#L180)); `isValidInterceptor` counts
  non-`selfIntercept` orders against `guns` ([firing.php:451-463](source/server/handlers/firing.php#L451));
  `automateIntercept` recomputes `$currGuns = guns - count(fireOrders)` ([firing.php:359](source/server/handlers/firing.php#L359)).
  A manual intercept order counts in all three. The spare gun is auto-assigned, exactly as asked.

**The only thing that has to change is that the client emits one order, not `guns`.**

The `initializationUpdate` "Shots Remaining" line on Twin/Quad Array reads
`this.guns - this.fireOrders.length`, so the ship window already counts a manual intercept against
the remaining shots with no edit.

### 4.4 Converting a self-intercept marker (D4)

A long-recharge weapon that already carries a `selfIntercept` order is offering itself to the
automation. Clicking a row with it selected should **remove the marker and push the targeted
order(s)** — one click, same consent, a strictly more specific commitment. Reuse
`removeSelfInterceptSingle` ([weaponManager.js:2747](source/public/client/weaponManager.js#L2747))
so split weapons still get their `recalculateForIntercept(false)` re-price, then declare normally.

The reverse (targeted → marker) is not offered; cancel and re-declare.

### 4.5 Client-side interception preview

Replace the broken `getInterception` with a helper that mirrors `getInterceptionMod`:

```
getDeclaredInterception(fireOrderId, interceptedWeapon)  ->  d20 points
```

* iterate ships → fire orders, collect `type == 'intercept' && targetid == fireOrderId && turn == gamedata.turn`
* per order: rating = the interceptor weapon's `interceptArray[order.firingMode] ?? intercept` (R6)
* degradation (§2.1): skip if `interceptedWeapon.ballistic || interceptedWeapon.noInterceptDegradation`,
  **unless** `interceptedWeapon.doInterceptDegradation`; otherwise −1 per prior interceptor
* clamp each contribution at 0, sum, ×5 for the display percentage

Keep the old `getInterception` as a thin wrapper or delete it — it has never returned a non-zero
value, so nothing depends on its behaviour.

**Label the number honestly.** It reflects *your declared orders only*. Automated interception is
computed server-side after commit and is deliberately not previewed; teammates' orders are not in
your payload. Use `Committed −15%` rather than `Interception 15%`, and put the caveat in the hover
tooltip.

### 4.6 Folding interception into the hit chance

The row prints `Approx: N%` / `Between: min% – max%` from `fireOrder.chance ?? fireOrder.needed` and
`calculataBallisticHitChange`. Subtract the committed interception, and add a
`Declared interception` line to the hover breakdown built by `buildHitChanceTooltipText`
([weaponManager.js:577](source/public/client/weaponManager.js#L577)).

Display `max(0, chance − committed)`. The server's `needed - totalIntercept` is **not** floored for
the roll itself, but a floored display is the honest read of "will it hit" and drives the greedy-fill
focus rule (§4.2). Because members of a group now differ only by committed interception, the
existing `Between: min – max` formatting becomes the natural summary of a partially-suppressed group
— reuse it rather than inventing a new string.

### 4.7 Ballistic interceptors (D3) — the one place that needs new UI

A missile rack with Interceptor missiles is intercept-capable but **cannot currently be selected in
phase 3 at all**. Three separate obstacles:

**(a) Selection is phase-gated.** [SystemIcon.js:243](source/public/client/UI/reactJs/system/SystemIcon.js#L243)
allows selecting a weapon only when `gamephase === 3 && !ballistic && !preFires` (or the matching
phase-1 / phase-5 clauses). Extend that condition with
`|| weaponManager.canManuallyInterceptWith(ship, system)` — a new predicate:

```
gamephase === 3
  && gamedata.isMyShip(ship) && !shipManager.isAdrift(ship)
  && system.weapon && !system.stowed && !system.autoFireOnly
  && !shipManager.systems.isDestroyed(ship, system)
  && !shipManager.power.isOffline(ship, system)
  && weaponManager.isLoaded(system)
  && weaponManager.getInterceptModeFor(system) !== null   // (b)
  && no current-turn order on this weapon other than intercept/selfIntercept   // R7
  && (canSplitShots ? ordersThisTurn < guns : ordersThisTurn === 0)
  && ammoAvailableForIntercept(ship, system)             // (c)
```

**(b) The intercept rating lives in a non-default firing mode**, and mode changing is phase-gated
the same way, so the player cannot switch to it. Mirror the server's
`MissileLauncher::switchModeForIntercept` ([missile.php:1563](source/server/model/weapons/missile.php#L1563))
with a client-side `weaponManager.getInterceptModeFor(weapon)`:

* `weapon.intercept > 0` → the current mode;
* else the highest-valued mode in `weapon.interceptArray` with a value > 0;
* else `null`.

Stamp that mode on the order's `firingMode`. **Do not cycle the weapon's actual mode** — it is shared
state across same-phpclass instances ([arch_client_system_shared_reference]) and would corrupt the
ship window's display. `AmmoMissileRackS.canWeaponInterceptAtAll`
([missile.js:260](source/public/client/model/weapon/missile.js#L260)) already scans `interceptArray`
this way; `getInterceptModeFor` is that logic returning the mode instead of a boolean, so the two
should share an implementation.

**(c) The magazine must be checked and debited.** Server-side this is already airtight —
`isLegalIntercept` calls `canInterceptAtAll` → `canDrawInterceptor`, and `fireDefensively` draws the
round, **per order in sequence**. Client-side there is a real gap:
`AmmoMagazine.doVerifyAmmoUsage` ([missile.js:161](source/public/client/model/weapon/missile.js#L161))
only counts orders where `gamephase == 3 && !currWeapon.ballistic`, so a phase-3 intercept order on a
*ballistic* rack is invisible to it. Two edits:

* extend `doVerifyAmmoUsage` and `doVerifyAmmoUsageFighter` to also count current-turn
  `type == 'intercept'` orders on ballistic weapons;
* add `ammoAvailableForIntercept` mirroring `canDrawInterceptor`: `remainingAmmo > 0` and
  `ammoCountArray[modeName] − (intercept orders already declared for that mode) >= 1`.

Without these the client would happily commit six interceptors from a magazine holding two, and the
server would silently drop four (D5 says nothing is surfaced, so the player would never know).

`preFires` weapons fall under the same predicate and are allowed on the same terms — the automation
pool has never excluded them either.

### 4.8 Withdrawing a manual intercept

Two paths, both needed:

* **Tooltip** — the `CANCEL` state of the row (collapsed: clears every order this ship has against
  the whole group; expanded: just that member). This is the only place the player can see *which*
  shot a weapon is on.
* **Ship window** — already works: `hasFiringOrder` returns `true` for an intercept order, so the
  existing "Remove all fire orders" button (`Hv` in
  [SystemInfoButtons.js:908](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L908))
  clears it. Consider re-wording the tooltip text; no logic change.

Both paths must call `recalculateForIntercept(false)` on split weapons, exactly as
`removeSelfInterceptSingle` does, so Slicers re-price their remaining shots.

---

## 5. Implementation stages

Each stage is independently shippable and independently testable.

### Stage 0 — Server validation (ship this first, on its own)

**Nothing user-visible. Hardens a path that is currently unguarded.**

Validate **inside** the totals loop at [firing.php:320-335](source/server/handlers/firing.php#L320),
per order, immediately before `addToInterceptionTotal` — **not** as a separate pass beforehand. This
is forced by the interceptor-missile magazine: `canInterceptAtAll` reads the magazine and
`fireDefensively` debits it, so N orders from one rack must be checked against the *running* ammo
state, exactly as the automation's per-gun loop does. A pre-pass would approve all N against the
undrawn magazine.

For each current-turn `type == 'intercept'` order:

1. Resolve the interceptor: must be a `Weapon`, not destroyed, not `isOfflineOnTurn`, not `stowed`,
   `getTurnsloaded() >= getLoadingTime()`.
2. Resolve the intercepted order **by id**; drop if not found, or if it is itself an
   intercept/selfIntercept.
3. Set the weapon to `$fire->firingMode`, assert `intercept > 0` in that mode (R6), run
   `isLegalIntercept($gamedata, $weapon, $intercepted)` — **restore the original mode afterwards**.
4. Per-weapon cap: current-turn orders on that weapon ≤ `guns`; and for a non-`canSplitShots`
   weapon, refuse a mix of offensive and intercept orders (R2/R7).
5. Drop failures with the established `->rejected = true` + `self::detachFireOrder($ship, $fire)`
   convention ([firing.php:90](source/server/handlers/firing.php#L90)) and a `Debug::log` naming
   game / ship / weapon / order and the reason (D5 — log only).

Deliberately **not** reusing `isValidInterceptor` wholesale: its `loadingTimeActual > 1` branch
demands a `selfIntercept` marker, which manual declaration replaces (R5).

Note on persistence: the order row was already written at commit time, so a detach here removes it
from resolution and the totals but leaves the row. Validation is deterministic given the same
gamedata, so a re-read reaches the same verdict. Deleting the row instead is possible but buys
nothing and costs a write.

Also extend `validateFireOrders` ([firing.php:28-30](source/server/handlers/firing.php#L28)) so that
intercept orders are still rejected when their `weaponid` resolves to a **non-weapon** — the stale
blueprint case the rest of that function exists for. Keep skipping the target-side checks, which
genuinely do not apply.

*Test:* insert intercept orders into `tac_fireorder` by hand — out of arc, against an uninterceptable
Slicer, and three from a two-round magazine — and confirm each is logged and dropped and that
`totalIntercept` reflects only the survivors.

### Stage 1 — Client declaration plumbing (no UI)

* Rewrite `weaponManager.targetBallistic` against T1–T9, taking a **single member** (not a group).
* `weaponManager.canInterceptBallistic(selectedShip, weapon, ball)` — the **single** per-weapon
  predicate, used by the button-enable test and the declaration loop alike, so the UI can never
  offer something the declaration will skip (T11).
* `weaponManager.getDeclaredInterception(fireOrderId, interceptedWeapon)` (§4.5).
* `weaponManager.getInterceptModeFor(weapon)` (§4.7b), shared with `canWeaponInterceptAtAll`.
* `weaponManager.allocateIntercept(selectedShip, members)` — the greedy-fill walk (§4.2).
* `weaponManager.removeManualIntercept(ship, fireOrderIds)` (§4.8).

*Test:* drive these from the console against a live ballistic entry; inspect `weapon.fireOrders`;
commit; check the DB rows and the resulting `totalIntercept` in the turn log.

### Stage 2 — The row UI

* Restore the template children; honour the ctor's `allowIntercept` (delete the `//obsolete`
  override) and add the `gamephase === 3` gate.
* Keep group members in `groupByOriginAndHitChange` (§4.2).
* Render collapsed rows, the disclosure toggle, sub-rows, and the INTERCEPT/CANCEL states.
* Re-render `.incoming` in place after a click. **Do not** widen
  `PhaseStrategy.onSystemDataChanged` ([PhaseStrategy.js:1046](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L1046))
  to refresh the tooltip — its narrow guard exists precisely so unrelated callers don't rebuild a
  tooltip out from under a click. The menu owns `element`; clear `.incoming` and re-run `renderTo`.
* Re-attach `attachHitChanceTooltipDelegation` on the rebuilt container (it `.off('.hitchance')`
  first, so re-attaching is safe).

### Stage 3 — Hit-chance integration

Fold committed interception into the row and sub-row percentages and add the breakdown line (§4.6).
Keep the existing Shadow "dice" and split-penalty branches intact — they are load-bearing and fiddly.

### Stage 4 — Ballistic and pre-firing interceptors (D3)

The three edits in §4.7: the `SystemIcon` selection gate, `getInterceptModeFor` wired into order
creation, and the two `AmmoMagazine` changes. Ship after Stage 3 so the common case is already
proven.

### Stage 5 — Self-intercept conversion (D4)

One-click marker → targeted order, via `removeSelfInterceptSingle` + normal declaration (§4.4).

### Stage 6 — CSS

New rules in [styles/shipTooltip.css](source/public/styles/shipTooltip.css) for `.incoming .intercept`,
`.incoming .interception`, `.incoming .ballexpand` and the sub-row indent. Tokens only, no new
`:root` block, no new `<link>` — see [project_visual_unification]. Real disabled state, not
`display: none`. Touch targets ≥ 32px on coarse pointers; the disclosure toggle must be tappable.

### Stage 7 — Per-weapon-class sweep

Verify against the classes that override the split/self-intercept hooks, since each has its own
`checkFinished` arithmetic: TwinArray / QuadArray / HeavyArray / QuadParticleBeam / ParticleRepeater
/ TelekineticCutter ([particle.js](source/public/client/model/weapon/particle.js)),
MolecularSlicerBeam L/M/H ([molecular.js:503,584,599](source/public/client/model/weapon/molecular.js#L503)),
PointPulsar ([pulse.js:149](source/public/client/model/weapon/pulse.js#L149)),
VorlonDischargeGun/Cannon ([special.js:382](source/public/client/model/weapon/special.js#L382)),
NeutronBlaster, GravityNet, GraviticLance, Med/AntigravityBeam, ProximityLaserNew, MultiphasedCutter,
PsionicConcentrator, EWGraviticTractingRod, BallisticTorpedo, and the AmmoMissileRack family.

---

## 6. Traps

1. **`getInterception(ball.fireOrder)` in the legacy block is a bug** — the function reads
   `.fireOrderId`, a `fireOrder` has `.id`. It has silently returned 0 for as long as it was
   commented out. Do not restore it verbatim.
2. **The tooltip is destroyed on the next canvas click**, via `onClickCallbacks`
   ([PhaseStrategy.js:656](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L656)).
   DOM clicks inside `.shipNameContainer` never reach `onClickEvent` because the tooltip
   `stopPropagation`s mouse events ([ShipTooltip.js:31](source/public/client/UI/ShipTooltip.js#L31)) —
   the same reason the existing fire-menu buttons work. Do not "fix" that by adding `click` to the
   tooltip element's stopPropagation list; `click` is deliberately absent.
3. **Firing-mode mutation.** Both the display path (`ShipTooltipBallisticsMenu` cycles
   `ball.weapon.changeFiringMode()` until it matches the order at
   [L69-75](source/public/client/UI/ShipTooltipBallisticsMenu.js#L69)) and the server's
   `hideSystemFireOrders` mutate weapon objects to match an order's mode. Anything the new code adds
   that reads a mode-dependent field must either restore the mode or read the array directly. This
   is why §4.7b stamps the intercept mode on the order instead of switching the weapon. See
   [arch_client_system_shared_reference].
4. **Positional system ids.** An intercept order's `weaponid` is positional
   ([arch_positional_system_id_trap]). Stage 0's non-weapon check exists for exactly the stale-client
   case `validateFireOrders` documents at [firing.php:11-20](source/server/handlers/firing.php#L11).
5. **`ball.position` is start-of-turn.** `getBallisticEntry` uses
   `getFirstMovementOnTurn(turn).position`, which is the launch hex for a ballistic (right) but the
   start-of-turn hex for a non-ballistic Sweeping shot — where the server bears on the shooter's
   **current** position ([weapon.php:1318](source/server/model/weapons/weapon.php#L1318)). Only
   matters for Sweeping rows, and those are uninterceptable today; note it so a future
   interceptable direct-fire row does not inherit a wrong arc test.
6. **Do not add fields to the posted order.** `Manager.php` rebuilds each `FireOrder` from a fixed
   arg list plus an explicit `notes` copy on the main-ship branch only; `chance`, `hitmod`,
   `pubnotes` and everything else are dropped, and the **fighter branch drops `notes` too**
   ([arch_fireorder_notes_dropped_on_post]). Manual interception needs no extra field — `type`,
   `targetid`, `weaponid`, `firingMode` are all whitelisted — so keep it that way.
7. **`getFiringOrder` returns intercept orders** ([weaponManager.js:3635](source/public/client/weaponManager.js#L3635)),
   so the ship window's `+/− shots` buttons would happily mutate an intercept order's `shots` on a
   `canChangeShots` weapon. Harmless (intercept resolution ignores `shots`) but scruffy — add a type
   guard to `_x`/`Ax`'s predicates in [SystemInfoButtons.js](source/public/client/UI/reactJs/system/SystemInfoButtons.js).
8. **Selecting a ballistic weapon in phase 3 is new (Stage 4)** and touches shared paths: `targetShip`
   already refuses ballistics outside phase 1 ([weaponManager.js:2897](source/public/client/weaponManager.js#L2897)),
   but check `targetHex`, "select all weapons of this type", and the phase-3 commit warnings in
   [gamedata.js:1185](source/public/client/gamedata.js#L1185) for anything that assumes a selected
   phase-3 weapon is direct-fire.
9. **Lobby ships are clones.** Nothing here runs in the lobby, but if any shared helper is touched,
   remember `instanceof` fails there ([arch_lobby_ship_objects]).
10. **Legacy bundle.** `ShipTooltipBallisticsMenu.js` and `weaponManager.js` are legacy (non-React)
    sources bundled into `game.legacy.bundle.js`; `SystemIcon.js` / `SystemInfoButtons.js` are React
    and land in `UI.bundle.js`. Rebuild via `fvbuild.ps1`; never commit a hand-edited bundle
    ([howto_minify_legacy_bundles], [howto_verify_react_bundle]).
11. **Replay corpus.** No serialized property changes here, but fire-order *flow* changes. Record a
    fresh case with manual interception and run the harness `check` ([project_replay_harness]).

---

## 7. Adjacent bugs — FIXED 2026-08-18, ahead of the feature

All three were found while reading for this plan, verified, and landed as standalone fixes. They are
independent of the unbuilt feature, but A1 in particular had to go in first: Stage 0 edits that exact
loop, and the feature makes it hot for the first time in years.

**A1 — FIXED. `selfIntercept` markers were credited as real interceptors.**
[firing.php:323](source/server/handlers/firing.php#L323), `automateIntercept`'s totals loop, tested
`$fireOrder->targetid == $intercepted->id` for *both* intercept types. The two live in different id
spaces: an `intercept` order's `targetid` is the id of the **fire order** being stopped, but a
`selfIntercept` order's `targetid` is the interceptor's **own ship id** — verified across all five
creation sites (`setSelfIntercept`, `onDeclareSelfInterceptSingle`, and the `doMultipleSelfIntercept`
overrides in molecular.js, pulse.js, special.js; every one sets `targetid: ship.id`, and nothing
server-side creates them). When a `tac_fireorder.id` collided with a `tac_ship.id` in the same game
it fired twice over: the marker was credited against an unrelated shot — bumping `totalIntercept`
and `numInterceptors` **and** running `fireDefensively`, so backlash triggered and an interceptor
missile was drawn — and the same weapon was *then* also given a genuine intercept order by the
automation below.

Fix: the loop now skips `selfIntercept` entirely. A marker is a **permission** to be auto-assigned,
not an assignment. Its real consumers — `isValidInterceptor`'s `loadingTimeActual > 1` gate and the
split-weapon gun refund at [firing.php:362](source/server/handlers/firing.php#L362) — are untouched,
so the weapon still enters the pool and still gets a real order.

**A2 — FIXED. `Weapon::getIntercept` commented out.**
[weapon.php:1980](source/server/model/weapons/weapon.php#L1980). Verified dead with certainty:
`getIntercept(` appears nowhere in the repo — server, client, legacy bundles or `.old` files —
except its own declaration. No overrides, no `call_user_func`, no variable-method dispatch, no bare
string reference. Commented rather than deleted because it records the original intent (interception
counts against a **fire order** id, with `type == "intercept"` only) that A1 and A3 both restore. It
must not be revived as-is: it never grew the `$doInterceptDegradation` clause that the live
`getInterceptionMod` has.

> Its sibling `getNumberOfIntercepts` ([weapon.php:2044](source/server/model/weapons/weapon.php#L2044))
> is dead by the identical check. **Left in place** — say the word and it goes the same way.

**A3 — FIXED, but my original diagnosis was wrong.** I called this a precedence bug; it is not.
`a && b && c || d && e && f` does group as `(a&&b&&c) || (d&&e&&f)`, but both disjuncts carried all
three tests, so the condition was merely redundant, not incorrect.

The real defect is A1's defect, on the client:
[weaponManager.js:3937](source/public/client/weaponManager.js#L3937) is called as
`getInterceptingFiringOrders(fireOrder.id)` — a **fire order** id — yet the second disjunct compared
it against a `selfIntercept` order's `targetid`, which is a **ship** id. That clause could never
legitimately match and fired only on collision, listing an unrelated weapon as an interceptor of the
shot in the combat log. Fix: match `type == "intercept"` alone, which is what the server's own
counters have always done. Nothing is lost — a marker that actually intercepted is given a real
intercept order by `automateIntercept`, and that order is what belongs in the list.

**Verification status:** `node --check` passes on `weaponManager.js`; both PHP regions are
brace-balanced with no stray comment terminators. `php -l` was **not** run — no PHP on PATH and no
Docker containers up at the time. Lint both PHP files once the dev env is running.

---

## 8. Test matrix

| Case | Expect |
|---|---|
| Non-split interceptor, in arc, vs incoming ballistic aimed at me | `guns` orders; row shows `Committed −N%`; hit chance drops; weapon gone from the automation pool |
| Same weapon then clicked on an enemy to fire offensively | Intercept orders replaced (retarget semantics); no double count server-side |
| Twin Array, **Split** mode: one manual intercept + one offensive split shot | Both resolve; "Shots Remaining" reads 0; no automated intercept added |
| Twin Array, **Split** mode: one manual intercept only | Second gun auto-assigned by `automateIntercept` |
| Twin Array, **Normal** mode | Both guns commit to the chosen shot |
| Grouped row `3x Missile`, one click with 6 interceptors selected | Greedy fill: shot 1 driven to 0%, remainder spill onto shot 2; nothing wasted |
| Same row expanded, `INTERCEPT` on Shot 3 | All eligible selection commits to Shot 3 only |
| Expanded sub-row `CANCEL` | Only that member's orders cleared; split weapons re-priced |
| Shadow Slicer (Sweeping, `uninterceptable`) row | Button disabled reading `Uninterceptable`; no order created; hand-inserted order dropped by Stage 0 |
| Out-of-arc incoming | Button disabled; hand-inserted order dropped by Stage 0 |
| Long-recharge weapon (MLPA-class), no marker | Order accepted despite no `selfIntercept`; cooldown applied next turn via `firedOnTurn` |
| Long-recharge weapon **with** a `selfIntercept` marker (D4) | One click replaces the marker with the targeted order |
| Missile rack, Interceptor missiles, **no launch this turn** (D3) | Selectable in phase 3; order stamped with the interceptor mode; magazine debited once per order |
| Same rack, three clicks against a **two-round** magazine | Client refuses the third; if forced, Stage 0 drops it and logs |
| Missile rack that **did** launch in Initial Orders | Not selectable in phase 3 |
| Rack that manually intercepted, then Initial Orders next turn | Normal launch available again |
| `freeintercept` weapon vs a shot aimed at an **ally** | Accepted only when the server's team + geometry test passes |
| `freeintercept` weapon vs a shot aimed at an **enemy** | Refused client-side (T7) and dropped server-side |
| Fighter flight escorting a ship, incoming ballistic at the ship | Accepted only under the server's same-hex-now-and-previous-turn rule (T8) |
| Nexus Laser Missile (`doInterceptDegradation`) with 3 manual interceptors | −20/−15/−10, not −60 (§2.1) |
| Commit → poll while waiting | Local orders vanish from the payload (phase-3 masking) exactly as offensive orders do; DB rows intact |
| Replay of the turn | Intercept lines render in the combat log; harness `check` passes |

---

## 9. Files touched

**Already changed (§7 fixes, landed 2026-08-18)**
- [source/server/handlers/firing.php](source/server/handlers/firing.php) — A1: totals loop skips `selfIntercept`
- [source/server/model/weapons/weapon.php](source/server/model/weapons/weapon.php) — A2: `getIntercept` commented out
- [source/public/client/weaponManager.js](source/public/client/weaponManager.js) — A3: `getInterceptingFiringOrders` matches `intercept` only

**Server**
- [source/server/handlers/firing.php](source/server/handlers/firing.php) — per-order validation inside `automateIntercept`'s totals loop; small extension to `validateFireOrders`

**Client — legacy bundle**
- [source/public/client/weaponManager.js](source/public/client/weaponManager.js) — `targetBallistic`, `canInterceptBallistic`, `getDeclaredInterception`, `getInterceptModeFor`, `allocateIntercept`, `removeManualIntercept`, `canManuallyInterceptWith`, hit-chance breakdown line
- [source/public/client/UI/ShipTooltipBallisticsMenu.js](source/public/client/UI/ShipTooltipBallisticsMenu.js) — template, `allowIntercept`, group members, disclosure + sub-rows, in-place refresh
- [source/public/client/model/weapon/missile.js](source/public/client/model/weapon/missile.js) — `AmmoMagazine.doVerifyAmmoUsage` / `…Fighter` count phase-3 intercept orders on ballistics; share `getInterceptModeFor` with `canWeaponInterceptAtAll`

**Client — React bundle**
- [source/public/client/UI/reactJs/system/SystemIcon.js](source/public/client/UI/reactJs/system/SystemIcon.js) — selection gate for ballistic/preFires interceptors (Stage 4)
- [source/public/client/UI/reactJs/system/SystemInfoButtons.js](source/public/client/UI/reactJs/system/SystemInfoButtons.js) — `+/− shots` type guard (trap 7); optional wording

**Styles**
- [source/public/styles/shipTooltip.css](source/public/styles/shipTooltip.css)

No blueprint, no schema, no serialized-property changes. `autoload.php` untouched (no new PHP class —
the validation is a static on `Firing`).
