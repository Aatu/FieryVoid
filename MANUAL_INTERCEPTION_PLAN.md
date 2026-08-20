# Manual Interception — reintroducing player-directed intercept assignment

Restores the pre-automation ability to **select intercept-capable weapons in the Firing phase and
click an incoming shot in the ship tooltip's INCOMING list** to commit those weapons against that
specific shot. Automated interception stays as the default for everything the player does not
hand-assign.

Status: **BUILT — Stages 0-7 all landed (Stage 7 on 2026-08-20); revised three times after
play-testing.** See §10 for what is in and what is not, §11 for the first play-test's changes, §12
for the second's, §13 for the third round of refinements and §14 for the Stage 7 sweep.

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

### Stage 0 — Server validation (ship this first, on its own)  ✅ BUILT

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

### Stage 1 — Client declaration plumbing (no UI)  ✅ BUILT

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

### Stage 2 — The row UI  ✅ BUILT

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

### Stage 3 — Hit-chance integration  ✅ BUILT

Fold committed interception into the row and sub-row percentages and add the breakdown line (§4.6).
Keep the existing Shadow "dice" and split-penalty branches intact — they are load-bearing and fiddly.

### Stage 4 — Ballistic and pre-firing interceptors (D3)  ✅ BUILT

The three edits in §4.7: the `SystemIcon` selection gate, `getInterceptModeFor` wired into order
creation, and the two `AmmoMagazine` changes. Ship after Stage 3 so the common case is already
proven.

### Stage 5 — Self-intercept conversion (D4)  ✅ BUILT

One-click marker → targeted order, via `removeSelfInterceptSingle` + normal declaration (§4.4).

### Stage 6 — CSS  ✅ BUILT

New rules in [styles/shipTooltip.css](source/public/styles/shipTooltip.css) for `.incoming .intercept`,
`.incoming .interception`, `.incoming .ballexpand` and the sub-row indent. Tokens only, no new
`:root` block, no new `<link>` — see [project_visual_unification]. Real disabled state, not
`display: none`. Touch targets ≥ 32px on coarse pointers; the disclosure toggle must be tappable.

### Stage 7 — Per-weapon-class sweep  ✅ BUILT 2026-08-20

Verified against the classes that override the split/self-intercept hooks, since each has its own
`checkFinished` arithmetic. Three defects found and fixed, the Molecular Slicer given its bespoke
manual path. Full results in **§14**.

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
12. **The lobby has its own hand-written `weaponManager`.** ⚠ **Cost a live crash, 2026-08-20 — see
    §15.** `gamelobby.php` does not bundle `weaponManager.js`; it defines a small stub object inline
    with just the predicates the React ship window calls. The ship window renders on BOTH pages
    ([project_shipwindow_redesign]), so **every new `weaponManager.x()` call added to
    `reactJs/system/*` must be added to that stub too**, unless it sits behind a
    `gamedata.gamephase === -2` early return. `SystemInfoButtons` is safe by construction —
    `canDoAnything`, `hasStyledMenu` and `render` all return at -2 before touching any of it.
    **`SystemIcon` is not**: it calls straight through on every render.

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
- [source/public/client/model/weapon/molecular.js](source/public/client/model/weapon/molecular.js) — Slicer `canDeclareManualIntercept` / `declareManualIntercept` / `getSpareInterceptCapacity`; `recalculateForIntercept` skips `intercept` orders (Stage 7, §14.4)
- [source/public/client/model/weapon/pulse.js](source/public/client/model/weapon/pulse.js) — `PointPulsar.getInterceptOrderMode` (Stage 7, §14.3)
- [source/public/client/model/weapon/special.js](source/public/client/model/weapon/special.js) — `VorlonDischargeGun.getInterceptOrderMode` (Stage 7, §14.3)

**Client — React bundle**
- [source/public/client/UI/reactJs/system/SystemIcon.js](source/public/client/UI/reactJs/system/SystemIcon.js) — selection gate for ballistic/preFires interceptors (Stage 4)
- [source/public/client/UI/reactJs/system/SystemInfoButtons.js](source/public/client/UI/reactJs/system/SystemInfoButtons.js) — `+/− shots` type guard (trap 7); optional wording

**Styles**
- [source/public/styles/shipTooltip.css](source/public/styles/shipTooltip.css)

No blueprint, no schema, no serialized-property changes. `autoload.php` untouched (no new PHP class —
the validation is a static on `Firing`).


---

## 10. Build record — 2026-08-19

### What landed

**Stage 0 — server validation.** [firing.php](source/server/handlers/firing.php)

* `validateFireOrders` no longer skips `intercept` orders. They carry an ordinary positional
  weaponid, so they now run the same stale-blueprint weapon check as everything else.
  `selfIntercept` is still skipped: it is a permission marker with no targeting of its own.
* `automateIntercept`'s totals loop indexes this turn's orders by id (`$ordersById`) and keeps a
  per-weapon tally of orders it has ACCEPTED (`$manualInterceptsAccepted`), so a surplus drops the
  extras rather than the whole set.
* New `Firing::validateManualIntercept` runs per order, immediately before `addToInterceptionTotal`,
  with the weapon already switched into the ORDER's firing mode (restored afterwards). It checks:
  interceptor resolves / is a `Weapon` / `getWeaponForIntercept()` / known mode / not destroyed /
  not offline / not stowed / loaded / `intercept > 0` in that mode; the intercepted order resolves
  by id, is not itself an intercept, has a resolvable weapon and target unit, and is not
  hex-targeted (after `notActuallyHexTargeted`); the gun cap and the R2/R7 offensive-vs-intercept
  rule; then `isLegalIntercept`. Failures take the established `->rejected = true` +
  `detachFireOrder` route with a `Debug::log` naming the reason (D5 — log only).

**Stages 1-5 — client.** [weaponManager.js](source/public/client/weaponManager.js) gained
`getModeFlag`, `getIncomingSourcePos`, `getInterceptModeFor`, `getInterceptRatingInMode`,
`countCurrentTurnOrders`, `getShotInterceptRefusal`, `ammoAvailableForIntercept`,
`sharesHexNowAndAtStartOfTurn`, `isBetweenShooterAndTarget`, `canInterceptBallistic`,
`getSelectedInterceptorsFor`, `getInterceptDisabledReason`, `getDeclaredInterception`,
`getRemainingHitChance`, `declareInterceptWith`, `isWeaponSpentForIntercept`,
`allocateIntercept`, `removeManualIntercept`, `getOwnInterceptOrdersAgainst` and
`canManuallyInterceptWith`; `targetBallistic` was rewritten against T1-T9 and `getInterception`
is now a thin wrapper over `getDeclaredInterception` (T10).
[ShipTooltipBallisticsMenu.js](source/public/client/UI/ShipTooltipBallisticsMenu.js) restored the
row controls, honours `allowIntercept`, keeps group members, renders the disclosure toggle and
sub-rows, and refreshes `.incoming` in place.
[SystemIcon.js](source/public/client/UI/reactJs/system/SystemIcon.js) ORs `canManuallyInterceptWith`
into the selection gate; [missile.js](source/public/client/model/weapon/missile.js) counts phase-3
intercept orders on ballistics in both `doVerifyAmmoUsage` variants; trap 7's type guard went into
[SystemInfoButtons.js](source/public/client/UI/reactJs/system/SystemInfoButtons.js).

**Stage 6 — CSS.** New `.ballrow` / `.ballexpand` / `.interception` / `.intercept` / `.ballsub`
rules at the foot of [shipTooltip.css](source/public/styles/shipTooltip.css). Tokens only, no new
`:root`, no new `<link>` (game.php already links the file). Real `:disabled` state; 32px targets
under `@media (pointer: coarse)`.

### Deviations from the plan, and why

**D-1. `usesCustomInterceptAllocation` — Molecular Slicer and Hyperplasma Cutter are excluded.**
Both price interception PER DIE out of a pool the player splits in a dialog, not per gun:
`MolecularSlicerBeamL.recalculateForIntercept` charges every offensive shot 5% for each defensive
order, and `HyperplasmaCutter` reads `getRemainingDice()`. The generic "one order per gun"
allocation in §4.3 would misprice all three Slicer sizes and the Cutter, and a generic intercept
order would corrupt their pool arithmetic. They carry a new `usesCustomInterceptAllocation = true`
prototype flag, which `canInterceptBallistic` and `canManuallyInterceptWith` refuse on; the row's
disabled title reads *"Use this weapon's own intercept declaration"*. Self-intercept and the
automation are untouched for them. Wiring these two into the manual path properly is Stage 7 work.

**D-2. `freeinterceptspecial` weapons cannot be hand-assigned to THIRD-PARTY shots.**
Their legality lives in a server-side `canFreeInterceptShot` with no client mirror, and D5 asks for
a predicate strict enough that drops are near-impossible. They still intercept fire aimed at their
own ship by hand, and the automation is unchanged. (The most visible one, the Interdictor, is
`autoFireOnly` and was never hand-assignable anyway.)

**D-3. The button prefers INTERCEPT over CANCEL.** §4.8 describes a CANCEL state whenever this ship
has orders against the row. As written that would make a shot un-stackable once committed. The
button now shows INTERCEPT whenever a legal declaration is available with the current selection and
CANCEL otherwise. Because declaring unselects a spent weapon, the button flips to CANCEL on its own
right after a click, and re-selecting weapons offers INTERCEPT again.

**D-4. A non-split DIRECT-FIRE weapon has no intercept cap in the client predicate**, because the
click wipes its own uncommitted phase-3 orders and re-declares (T6 retarget semantics), exactly as
`targetShip` does in the opposite direction. Ballistic and `preFires` weapons DO get the cap: their
orders were committed in an earlier phase and nothing may be wiped to make room (R7).

**D-5. The collapsed group row's "Committed" label reads `Committed on X/Y`**, not a percentage.
Summing a percentage across several different shots would not mean anything; single-shot rows and
sub-rows show `Committed −N%` as §4.5 specifies.

**D-6. `getIncomingSourcePos` bears on the shooter's CURRENT hex for a non-ballistic shot**, which
is what the server does — trap 5 notes `ball.position` is the wrong answer there. Moot while every
Sweeping weapon is uninterceptable, but it is no longer wrong to inherit.

### Verification

* `php -l` clean on firing.php; `node --check` clean on every touched legacy source; esbuild
  (jsx loader) parses both React sources.
* A sandbox smoke test drove the pure client helpers — 34 assertions covering
  `getInterceptModeFor` (including the `canModesIntercept` gate), per-mode flag reads, order
  counting, degradation (off vs ballistic, on vs direct fire, forced on by
  `doInterceptDegradation`), the row-level refusals, and `canInterceptBallistic` across
  uninterceptable / `canInterceptUninterceptable` / `ballisticIntercept` / stowed / autoFireOnly /
  custom-allocation / split-gun caps / ballistic-rack R7 / third-party geometry. All pass.
* `fvbuild.ps1 -Check`: autoload map up to date; ship-data validator PASS (248 findings, all in
  baseline, 0 new); replay harness **160 passed, 4 failed**. The four (games 4126, 4140, 4143, 4148)
  fail identically on a stashed, clean tree — pre-existing corpus drift (a positional system-id
  shift and a missing `gaimOssari` class), not this work.
* Bundles rebuilt with `fvbuild.ps1 -Client`.

### Still to do

1. ~~**Stage 7 — the per-weapon-class sweep.**~~ DONE 2026-08-20 — §14. Nothing in the class list has
   been exercised in a live game yet, though: the Twin/Quad Array `checkFinished` arithmetic is the
   one to try first (R3), then the Slicer's paired marker/engagement flow.
2. ~~**Slicer / Cutter** — decide whether they get a bespoke manual path or stay on self-intercept
   (D-1).~~ DECIDED and BUILT — Slicer has its own path (§14.4), Cutter stays out (§11.5).
3. **Replay corpus** — record a fresh case with a manual intercept order and re-run `check`
   (trap 11). No serialized property changed, but the fire-order flow did.
4. **Live test matrix** — §8 has not been walked through in a running game.


---

## 11. First live test — game 4305, 2026-08-19

Five findings, all addressed. Two were real bugs with one root cause; three were design changes.

### 11.1 The root cause: a ballistic in flight has NO stored hit chance

`tac_fireorder` **has no `chance` column**. `chance` is a client-side field that
`weaponManager.targetShip` stamps on an order at declaration and that never leaves the browser —
`Manager.php`'s rebuild drops it (trap 6), and it is not persisted. A ballistic order's `needed`
stays **0** until the shot actually resolves. Confirmed directly against the game:

    id      turn  type       shooterid  targetid  weaponid  needed  rolled
    497312  1     ballistic  876551     876550    28        0       0
    497313  1     ballistic  876550     876551    21        0       0
    497314  1     ballistic  876550     876551    28        0       0

So `fireOrder.chance ?? fireOrder.needed` — which §4.6 specified and which the row's own *"normal"*
branches use quite correctly for direct fire — evaluates to **0 for every missile on the board**.
The INCOMING row never hit this because a ballistic falls through to its `else` branch and prints
`calculataBallisticHitChange`, a LIVE recompute. Everything I added on top read the stored value.

Two symptoms, both reported:

* **Expanded sub-rows showed 0%** (finding 4). They printed the stored base directly.
* **A grouped row could not be intercepted at all** (findings 3 and 4). `allocateIntercept`'s greedy
  fill advances past any member whose remaining hit chance is `<= 0` — with every member reading 0
  it walked off the end of the group and `break`ed before declaring anything. A single-shot row and
  an expanded sub-row both route through `targetBallistic`, which has no such check, which is
  exactly why those worked and the collapsed multi-shot row did not.

**Fix:** new `weaponManager.getIncomingShotHitChance(ball)` — the stored chance for a direct-fire
order, the live `calculataBallisticHitChange` for anything else (the same call the row headline and
the grouping key already make, so the numbers agree by construction). `getRemainingHitChance` and
every display path now go through it. Covered by four regression assertions in the smoke test.

### 11.2 The button is gone — the hit chance IS the control (findings 1 and 2)

An INTERCEPT/CANCEL button on every row squashed the layout. Replaced per user direction:

* **Declare** by clicking the hit chance itself, which is already the number the player is reading.
  It carries a dotted underline and a pointer cursor when a legal declaration is available with the
  current selection, and turns green once this side has committed interception against it.
* **The number drops** as interception is committed, and the hover tooltip gains its
  `Declared interception` line (as §4.6 always intended).
* **The refusal reason moved into that tooltip's footer** (`result.note`, the existing free-text
  hook) — *"Cannot intercept: Out of arc"* — rather than a disabled button's `title`. Nothing about
  the row moves as the selection changes, which was the point of the disabled state in §4.1.
* **No CANCEL.** A manual intercept is an ordinary fire order, so the ship window's existing remove
  button clears it. `removeManualIntercept` and `getOwnInterceptOrdersAgainst` were deleted.
* The row went back to plain block flow — no flex, no reserved columns. The only structural addition
  is the 12px disclosure caret.

This supersedes §4.1's control list, §4.8's tooltip half, and D-3 in §10.

### 11.3 Green system icon for a defensive commitment (finding 2)

A weapon whose entire contribution this turn is interception — one or more `intercept` orders, or a
`selfIntercept` marker, and nothing aimed at anyone — now paints its
[SystemIcon](source/public/client/UI/reactJs/system/SystemIcon.js) **green** (`#52b352` border and
glow, `#2f7a3a` fill) instead of the offensive orange. New predicate
`weaponManager.isInterceptOnly(ship, weapon)`; the icon still reads as `$firing`, so nothing else
about its behaviour changes. A split-shot mount spending one gun offensively and one on interception
stays orange — it *is* shooting at someone.

### 11.4 Expanded sub-rows match the row above them (finding 4)

Sub-rows print `- Approx: N%` in the same wording as the collapsed row, N being the hit chance after
declared interception. The per-shot detail (base chance, interception applied, what a click will do)
moved into their own hover tooltip.

### 11.5 Slicer stays in scope, Cutter does not (finding 5)

`usesCustomInterceptAllocation` still excludes both from the generic per-gun path, but they now mean
different things and say so in their comments:

* **Molecular Slicer** — *deferred to Stage 7, and BUILT there on 2026-08-20 (§14.4)*. It needed a
  bespoke manual path that spends dice and set damage out of its pool rather than guns; it now has
  one, and `usesCustomInterceptAllocation` has become "not the generic per-gun path" rather than a
  blanket refusal.
* **Hyperplasma Cutter** — *out of scope for this project*, permanently. Its 1-point-per-d10
  allocation is a different mechanic and is already well served by its own self-intercept dialog.

### 11.6 Still to verify in play

The revised UI has not been through a game yet. If a row still refuses when it should not, **hover
the hit chance** — the tooltip footer now names the reason, which is the diagnostic the first test
lacked.


---

## 12. Second live test — game 4305, 2026-08-19

### 12.1 Selecting a ship threw away its weapon selection

`PhaseStrategy.setSelectedShip` opened with:

    if (this.selectedShip) { this.deselectShip(this.selectedShip); }

with no check that the ship being selected was a DIFFERENT ship — so re-clicking the ship you already
had selected tore it down and rebuilt it, and `deselectShip` unselects every weapon on the way
through. Clicking your own ship is how you open its tooltip, so the INCOMING list could only ever be
opened with an empty selection.

**Fix:** `setSelectedShip` passes `keepWeapons = (this.selectedShip === ship)` to `deselectShip`,
which skips **only** the weapon-unselect loop. The icon, weapon list, movement UI, EW and LoS sprite
all still tear down and rebuild exactly as before.

**Blast radius.** The new behaviour is reachable in one situation only: selecting the ship that is
already selected.

* Every direct `deselectShip` caller — [PhaseStrategy:171](source/public/client/renderer/phaseStrategy/PhaseStrategy.js#L171),
  DeploymentPhaseStrategy (x2), MineDeployment (x2), SelectFromShips — omits the new argument and
  keeps the original clear-everything behaviour.
* The four `deselectShip` overrides (Fire, Initial, PreFiring, Deployment) now forward the flag;
  Movement has no override and inherits the base.
* `FirePhaseStrategy.onWeaponSelected` is already guarded by `if (this.selectedShip !== ship)`, so it
  can never pass `keepWeapons`.
* Of the remaining `setSelectedShip` callers, the affected ones are the per-phase `selectShip`
  handlers plus `onShipRightClicked`, `onScrollToShip`, the Deployment dock and the mine-deployment
  helper. In all of them the weapon selection was being dropped incidentally, never deliberately —
  nothing reads `gamedata.selectedSystems` expecting it to have been emptied by a re-select.

### 12.2 The INCOMING list answered with a stale weapon selection

Every clickable hit chance is computed against `gamedata.selectedSystems` **at render time**, and
nothing re-rendered the list when that selection changed. So the sequence *open tooltip → select an
interceptor → click the hit chance* asked a row that had been built before the weapon existed, and it
answered *"Cannot intercept: No interceptor selected"*. Toggling a group's disclosure caret called
`refresh()`, which is why that — and only that — unstuck it, and why a shot with no group to expand
could not be intercepted at all.

This is also what the first play-test reported as a *"greyed out"* button (§11): the button's state
was computed at the same moment, for the same reason.

**Fix, in two halves:**

1. `PhaseStrategy.onSystemDataChanged` re-renders the ballistics menu. Both selection paths land
   there — `selectWeapon` fires `WeaponSelected`, which `FirePhaseStrategy.onWeaponSelected`
   forwards, and `unSelectWeapon` fires `SystemDataChanged` directly. Kept as narrow as the stealth
   forecast beside it: Firing phase only, and it calls the menu's own `refresh()` rather than
   `ShipTooltip.update()`, so only `.incoming` is rebuilt and the rest of the tooltip never moves
   under a click (Stage 2's warning stands).
2. `makeInterceptable` now re-asks `getInterceptDisabledReason` **at click time** and declares on
   that answer. The render-time answer only drives the affordance. A row that has gone stale for any
   other reason therefore still does the right thing instead of firing on a stale verdict.

### 12.3 The list is grouped by shooter (user choice, from three options)

The run-on row — *"Sharlin War Cruiser, 3x Heavy Missile (Normal) - Approx: 45%"* — wrapped onto a
second line as soon as a name got long. The list is now:

    Apollo Bombardment Cru…
      ▶ 2x Missile Rack (Class-B)     45%
        1x Heavy Missile (Class-H)    30%

    Sharlin War Cruiser
        1x Molecular Slicer           55%

* **The ship name is a heading, written once per shooter**, not repeated on every row.
* **Rows are three fixed parts** — caret, what is incoming, hit chance. Both text cells ellipsise
  instead of wrapping and carry the full string as a `title`; the number column is fixed-width and
  right-aligned so the percentages form a column. `.incoming` is capped at 340px, which is what
  gives `text-overflow` something to work against.
* **"Approx:" and "Between:" are gone.** Every number in that column is a hit chance, and a range
  says "between" by being a range. They were costing about a third of the row's width.
* **Expanded shots name the weapon** rather than "Shot 1 / Shot 2" — which shot it is only matters
  inside its own tooltip, which still says *"Shot 2 of 3"*.
* The count is always written, "1x" included, so weapon names line up down the column.

This supersedes the row description in §4.1 and §4.2's mock.


---

## 13. Third refinement round — 2026-08-20

Four items, none of which changed a rule. Three are display, one is documentation only.

### 13.1 Interception is no longer floored at 0%

`getRemainingHitChance` and the row's per-member `remaining` both did `Math.max(0, base - committed)`.
The floor is gone (user direction): an over-intercepted shot now reads **`-25%`**, which is the only
way that column can say *"you have already spent more than this shot was worth — stop feeding it."*
A floored `0%` is indistinguishable from *"exactly suppressed"*.

The server has always behaved this way — `automateIntercept` drops any shot whose
`needed - totalIntercept` is `<= 0`, so an over-intercepted shot is simply gone — so nothing about
the outcome changed, only what the player is told.

Consumers that had to cope:

* **The greedy fill.** `allocateIntercept` advances its focus while `getRemainingHitChance(...) <= 0`.
  A negative still satisfies `<= 0`, so it walks past an over-killed member exactly as before. No
  change needed, but it is the one caller that reads the number as a *decision* rather than as text.
* **Range formatting.** `"30-45%"` with a negative low end would render `"-30--10%"` — two hyphens,
  one of them a minus. New `joinRange(lo, hi)` in the menu repeats the `%` sign when `lo < 0`,
  giving `"-30%--10%"`, and keeps the compact `"30-45%"` otherwise. (It first spelled the negative
  case out as `"-30% to -10%"`; the user asked for the shorter form on 2026-08-20 — the words cost
  the width the row spent §12.3 recovering.) It deliberately does **not** collapse equal ends: the
  Shadow "Offensive Dice" branch has always printed both and its callers decide.
* **The hover tooltip.** `hitChance` and the `Declared interception` line already carried signed
  values through `buildHitChanceTooltipText`; nothing there needed touching.

### 13.2 The shooter heading is `--fv-accent`

The three tiers of the INCOMING list are told apart by colour alone, so they have to read as a
ladder. They did not:

| Tier | Was | Now |
|---|---|---|
| Shooter heading (`.shipname`) | `--fv-text-accent` **#C6E2FF** | `--fv-accent` **#8bcaf2** |
| Shot row (`.ballrow .weapon`) | `--fv-text` #deebff (inherited) | unchanged |
| Expanded sub-row (`.ballsub`) | `--fv-text-dim` #8ca5c0 | unchanged |

#C6E2FF against #deebff is two near-identical bluish whites — the ship name did not read as a
heading at all. `--fv-accent` is the same token the active disclosure caret already uses, so the
row's structural chrome stays on one colour, and it is a token, not a new value
(see `project_visual_unification`).

### 13.3 The dotted underline sits under the number, not under the column

**The underline was never a `text-decoration`.** It is
`.hit-chance-tooltip { border-bottom: 1px dotted green }` in
[tactical.css:2106](source/public/styles/tactical.css#L2106) — the site-wide *"there is a breakdown
behind this number"* cue that `weaponManager` attaches to whatever element carries a hit chance. In
the TARGETING list that element is an inline `<span>`, so the border is exactly as wide as the text.
In the INCOMING list it is `.hitchange`, a **blockified, fixed-width, right-aligned flex item**
(`min-width: 44px`, wider on a range) — so the same declaration drew a rule the full width of the
column, in front of the number.

The first attempt at this moved `text-decoration: underline dotted` off `.hitchange`, which was
correct in itself and changed nothing visible, because it was not the property doing the drawing.
Recorded because it is an easy trap to fall into twice: **before moving an underline, check whether
it is a border.**

Fix: the number goes into its own inline `<span class="hitvalue">` (new `setChanceText()` helper),
`.incoming .hitchange.hit-chance-tooltip` explicitly gives the border up, and `.hitvalue` takes it.
`.hitchange` keeps the classes, the `data-tooltip`, the hover colour and the click handler, so the
whole column stays clickable and the delegated tooltip still fires — `.hitvalue` is a descendant.

One line, with colour carrying the state (a second dotted rule under a 12px `45%` would only read as
a thicker one):

| State | Underline | Cursor |
|---|---|---|
| Has a breakdown | `green` — tactical.css's own value | `help` |
| …and interception can be declared | `--fv-accent` | `pointer` |

### 13.4 Where the server auto-uses Interceptor missiles (documentation only — no behaviour change)

Reported: *"the server is using Interceptor missiles automatically for launchers with 1-turn loading
time."* Correct, and the chain is now annotated in place under one greppable marker —
`grep -rn "AUTO-INTERCEPTOR-MISSILES" source/server`.

| Site | Where | What it does |
|---|---|---|
| **1 of 3 — the switch itself** | `Firing::getUnassignedInterceptors`, [firing.php](source/server/handlers/firing.php) — the `canModesIntercept && !firedOnTurn` line | **The kill switch.** `switchModeForIntercept()` walks `$interceptArray`, picks the highest-rated mode (i.e. Interceptor), *changes the weapon into it* and sets `$weapon->intercept` — which is what puts the launcher in the automation pool. Nothing else opts it in. |
| **2 of 3 — why nobody is asked** | `Firing::isValidInterceptor`, the `$loadingTimeActual > 1` block | The only consent gate in the automated path: a weapon that loads in more than one turn must carry a player-placed `selfIntercept` marker. It keys on **loading time**, not on ammo use. |
| **3 of 3 — the ammo check** | `MissileLauncher::canInterceptAtAll` → `AmmoMagazine::canDrawInterceptor`, debited by `fireDefensively` → `doDrawAmmo` | Yes, ammo **is** verified before each intercept is allowed, and `interceptorUsed` is bumped per draw so the Nth intercept this turn sees the N−1 rounds already spent. |

Why site 2 never fires for these racks: there is **no per-firing-mode loading time**.
`MissileLauncher`'s constructor copies range, damage, intercept and the rest out of the ammo class
but not `loadingtime`, and `$normalload` is 0 on every rack. So the rack's own `$loadingtime` decides
— and `AmmoMissileRackD`, the Class-D rack that carries Interceptor as its *default* round, declares
`$loadingtime = 1`. `$loadingTimeActual == 1`, the gate is skipped, the launcher is enrolled. A rack
with `$loadingtime = 2` does fall into the gate and does demand the marker.

**If it is to become opt-in**, site 1 is the narrow lever: guard that one line (with the same
`selfIntercept` test, or a new per-weapon flag) and the launcher stays in its offensive mode,
`$weapon->intercept` stays 0, and it never reaches the pool. Site 2 is the wrong place — raising the
threshold there would start demanding markers from every ordinary 1-turn gun on the board. **Manual**
interception is unaffected either way: the manual path sets the order's firing mode itself, inside
`automateIntercept`, and never passes through `getUnassignedInterceptors`.

### 13.5 Verification

* `node --check` clean on `ShipTooltipBallisticsMenu.js` and `weaponManager.js`; `php -l` clean on
  `firing.php` and `missile.php`.
* No build needed — legacy JS (the `yarn watch:legacy` watcher covers it) and a plain stylesheet.
  No React source, no server property reaching the client through a static blueprint.
* Not yet play-tested. §11.6's diagnostic still applies: if a row refuses when it should not, hover
  the hit chance and read the tooltip footer.


---

## 14. Stage 7 — the per-weapon-class sweep (2026-08-20)

The Hyperplasma Cutter stays permanently out of scope (§11.5). The Molecular Slicer is **in**, and
this is what it took.

### 14.1 What the sweep actually checks

A weapon reaches the manual path only if `getInterceptRatingInMode` is above 0, so the first pass was
to throw out everything with `$intercept = 0`: **GraviticLance, PhotonicPrismBeam, MolecularSlicerBeamL**
(the Light Slicer cannot intercept at all), plus **GravityNet, EWGraviticTractingRod, BallisticTorpedo,
MultiphasedCutter, ProximityLaserNew**, which declare no rating. **ShadeModulator** is `autoFireOnly`
and `canInterceptBallistic` refuses it a line earlier.

For everything left, three questions:

1. **Does `declareInterceptWith` emit the right number of orders?** A non-split weapon commits every
   gun (R4), so `->guns` has to be the count for the mode it is actually in.
2. **Does `checkFinished()` agree?** It counts `fireOrders.length` — which now includes intercept
   orders — so its cap has to be the same number.
3. **Does anything else on the class read a fire order per-order?** This is where the real bug was.

`->guns` is mode-correct for every one of them: the multi-mode classes all carry a `$gunsArray`
(`QuadArray` 4/3/2/1/4, `PsionicConcentrator` 4/2/1/4/2, `NeutronBlaster` 1/1/1, `MedAntigravityBeam`
1/2, `AntigravityBeam` 1/3) and `changeFiringMode` re-derives `$guns` from it. `TwinArray`,
`HeavyArray`, `TelekineticCutter` (2), `QuadParticleBeam`, `VorlonDischargeGun`/`Cannon` (4) and
`PointPulsar` (3) are flat.

| Class | guns (per mode) | Split in | `checkFinished` cap | Verdict |
|---|---|---|---|---|
| TwinArray | 2 | 2 | `>= guns` in mode 2 | ok |
| QuadArray | 4/3/2/1/4 | 5 | `>= guns` in mode 5 | ok |
| HeavyArray | 2 | 2 | `> 1` in mode 2 | ok |
| QuadParticleBeam | 4 | 2 | `> 3` in mode 2 | ok |
| TelekineticCutter | 2 | 2 | `> 1` in mode 2 | ok |
| ParticleRepeater | 1-2 (`$baseGuns`) | with Gunsights | `getShotsFired() == "Number of shots"` | ok — see 14.2 |
| PointPulsar | 3 | 2 | `> 2` | **fixed** — 14.3 |
| VorlonDischargeGun/Cannon | 4 | always | `> 3` | **fixed** — 14.3 |
| PsionicConcentrator | 4/2/1/4/2 | 4, 5 | `> 3` / `> 1`, none in 4-5 | ok — see 14.2 |
| NeutronBlaster | 1 | 2, 3 | `>= guns` | ok |
| MedAntigravityBeam | 1/2 | 2 | `>= guns` in mode 2 | ok |
| AntigravityBeam | 1/3 | 2 | `>= guns` in mode 2 | ok |
| MolecularSlicerBeam M/H | n/a | always | pools empty | **built** — 14.4 |
| AmmoMissileRack family | per rack | — | — | ok (Stage 4, R7) |

### 14.2 Two things that look wrong and are not

**ParticleRepeater** counts `order.shots` across *every* fire order in `getShotsFired()`, so a manual
intercept eats one of its "Number of shots". That is correct — the Repeater trades shots against
intercept rating (`this.intercept = 1 + boost`) by design, and `->guns` (1, or 2 on the Mine version)
is the cap on both sides: `automateIntercept`'s per-weapon loop runs `for i < $currInterceptor->guns`.

**PsionicConcentrator** has no `checkFinished` branch for modes 4 and 5, its two split modes, so it
never reports finished there. `isWeaponSpentForIntercept`'s `(offensive + intercept) >= guns` fallback
is what catches it — that fallback exists for exactly this shape. (Its `$canSplitShotsArray` also has
a duplicate `1 =>` key and no entry for mode 3; PHP keeps the last write, so mode 3 reads as
non-split, which is what mode 3 — `$gunsArray[3] = 1` — wants anyway. Noted, not touched.)

### 14.3 Defect: a manual intercept was billed at the wrong firing mode

`VorlonDischargeCannon.initializationUpdate` bills the ship's power budget
`powerReq += 5 * fireOrder.firingMode` **per order**, and `hasFiringOrder` returns `true` for an
`intercept` order (it only special-cases `selfIntercept`). So a manual intercept declared while the
Cannon sat in mode 3 was charged **15 power instead of 5** — enough to fail an end-of-turn power
check on an otherwise legal declaration.

The weapons already knew the answer and nothing was asking them: both `PointPulsar` and
`VorlonDischargeGun` stamp `firingMode: 1` in their `doMultipleSelfIntercept`, with the comment *"So
that powerReqd display accurately always"*. A defensive shot is priced in mode 1 whatever mode the
weapon is set to.

**Fix:** new optional hook `getInterceptOrderMode()`, asked by `declareInterceptWith` right after
`getInterceptModeFor`. `PointPulsar` and `VorlonDischargeGun` (the Cannon inherits) return 1. Safe
because both carry a flat `->intercept` with no `interceptArray`, so narrowing the mode cannot change
the rating — and the server re-reads the rating in the order's mode anyway
([firing.php](source/server/handlers/firing.php), the `changeFiringMode` sandwich around
`validateManualIntercept`).

### 14.4 The Molecular Slicer's manual path

**The server's arithmetic is what dictates the shape.**
`MolecularSlicerBeamL::getInterceptionMod` ([molecular.php](source/server/model/weapons/molecular.php))
counts `selfIntercept` orders as how many shots the Slicer is *allowed* to engage and distinct
`intercept` targetids as how many it *has*, and pays the full rating only while engaged <= allowed. **A
bare intercept order with no marker behind it is worth exactly nothing** — which is why the Slicer
could not simply be let onto the generic per-gun path, and why Stage 7 was the right place for it.

So one manual Slicer intercept is **two orders**:

* a **selfIntercept marker**, which spends one damage die (or one whole `SLICER_SET_DAMAGE_BLOCK` of
  set damage) and buys one engagement — written by `addSelfInterceptOrders`, the *same* call the
  self-interception dialog makes, so it is unshifted to the front of the array (index order drives
  the cumulative -5%) and runs `recalculateForIntercept(true)` to charge the offensive shots for it;
* the **targeted intercept order**, naming the shot, carrying `shots: 0` and `setDam: 0`.

Those two zeroes matter: `getShotsUsed` / `getSetDamageUsed` sum **every** order in the array, so a
`1` there would charge the pool twice and silently eat a die out of the offensive volley.

**Capacity already bought is reused.** An unmatched marker is spare capacity, so a manual intercept
consumes it rather than paying again — D4's conversion in this weapon's own currency: the generic
path *deletes* the marker it finds, this one *spends* it. The client counts capacity per intercept
**order** rather than per distinct target — one order is one engagement and costs one die.

> ⚠ This paragraph originally went on to say that, since the server counts distinct targets, stacking
> two engagements on one shot leaves it "a marker to spare rather than short", and that erring in that
> direction was deliberate. **A marker to spare is not the safe side** — a spare marker is budget the
> automation spends, and that is exactly the game-4306 double-interception in §16.

~~**Zero server changes.**~~ **WRONG — see §16.** The claim was that `getInterceptionMod` returning 0
would stop the automation on its own. It does not: that function compares **distinct** targetids
against the marker count, so engagements stacked on one shot read as barely any used. One server
line was needed after all — `beforeFiringOrderResolution` must not pad `$guns` for an `intercept`
order, or the automation re-spends every marker the player already hand-assigned. Found in game 4306.

**Withdrawal is paired.** `removeSelfInterceptSingle` takes one `intercept` order with the marker when
there is no spare capacity — otherwise removing the last marker would strand an engagement that is
worth 0 server-side while still looking committed in the ship window.

**`recalculateForIntercept` now skips `intercept` orders as well as `selfIntercept`.** A manual
intercept order carries neither `->chance` nor `->hitmod`, so the re-price loop would have written
`NaN` into it — harmless in itself (nothing reads it, and `Manager.php` drops the field on POST) but
it would surface the moment anything did.

### 14.5 How the hooks are wired

`usesCustomInterceptAllocation` keeps its meaning — *"not the generic per-gun path"* — and is still
set on both the Slicer and the Cutter. What changed is that it is no longer a blanket refusal: a
weapon carrying it now has to implement the pair of hooks to be offered at all.

| Hook | On | Asked by |
|---|---|---|
| `canDeclareManualIntercept(ship)` | MolecularSlicerBeamL | `canInterceptBallistic` (in place of gun accounting), `isWeaponSpentForIntercept`, `getInterceptDisabledReason` |
| `declareManualIntercept(ship, ball, mode)` | MolecularSlicerBeamL | `declareInterceptWith` — returns straight out, owning marker reuse |
| `getSpareInterceptCapacity()` | MolecularSlicerBeamL | itself, and `removeSelfInterceptSingle`'s pairing guard |
| `getInterceptOrderMode(mode)` | PointPulsar, VorlonDischargeGun | `declareInterceptWith` |

The Hyperplasma Cutter implements none of them, so every one of those sites refuses it and it still
answers *"Use this weapon's own intercept declaration"*.

### 14.6 Known limitation, recorded not fixed

`getDeclaredInterception` (the row's preview) applies interception **degradation** generically, but
the Slicer's server-side `getInterceptionMod` override does not degrade — *"Slicers can freely
combine their self-intercepts into a single strong intercept or multiple small ones."* The two would
disagree only when a Slicer is stacked with other interceptors on a **direct-fire** shot, because
degradation is switched off against ballistics (§2.1) and every Sweeping weapon is currently
`uninterceptable`. It is unreachable today; expressing a per-*interceptor* degradation exemption
client-side would need a new serialized flag, which is not worth it until something can reach it.

### 14.7 Verification

* `node --check` clean on `weaponManager.js`, `molecular.js`, `pulse.js`, `special.js`,
  `ShipTooltipBallisticsMenu.js`.
* Three sandbox smoke tests, **49 assertions, all passing**, driving the real sources in a `vm`
  context rather than a reimplementation:
  * **Slicer pool arithmetic (28)** — marker written first, one die charged *once*, `shots`/`setDam`
    zero on the intercept order, set-damage fallback when the dice run out, offensive orders
    re-priced by 5%, refusal when both pools are empty, Light Slicer refused, no `NaN` out of
    `recalculateForIntercept`.
  * **Hook wiring (10)** — delegation to `declareManualIntercept` with the generic path writing
    nothing, `getInterceptOrderMode` narrowing mode 3 to 1 (and a weapon without it keeping 3),
    `isWeaponSpentForIntercept` deferring to the weapon, and the Cutter still refused by name.
  * **Withdrawal pairing (11)** — marker and engagement withdrawn together when there is no spare, a
    *spare* marker removed on its own leaving the paid engagement standing, a generic weapon
    untouched by the new branch, and the offensive 5% refunded correctly either way.
* **No server file changed** in this stage (the only PHP edits this session are the
  `AUTO-INTERCEPTOR-MISSILES` comments of §13.4), so the replay harness cannot be affected and was
  not re-run. Trap 11's "record a fresh case with a manual intercept" is still worth doing once the
  Slicer path has been through a live game.


---

## 15. Regression: the lobby ship window died on `isInterceptOnly` (2026-08-20)

    Ship window render failed for Ochlavita Destroyer
    TypeError: weaponManager.isInterceptOnly is not a function

**Not a Stage 7 bug.** It had been live since §11.3 (2026-08-19) and would fire for *every* ship in
the lobby, not just one with a Point Pulsar — that was simply the ship being looked at.

**Cause.** `gamelobby.php` does not bundle `weaponManager.js`. It defines a small stub object inline,
listing only the predicates the React ship window needs, because the lobby is read-only and
everything should read as idle. §11.3 added `isInterceptOnly` to `SystemIcon`'s render — as an
unconditional prop, `$intercepting={isIntercepting(ship, system)}` — and the stub was never given it.

`SystemInfoButtons` escaped because it is guarded by construction: `canDoAnything`, `hasStyledMenu`
and `render` all return at `gamedata.gamephase === -2` before reading anything off `weaponManager`,
and the comment above `canDoAnything` says exactly why. **`SystemIcon` has no such guard** — every
predicate in its prop list runs on every render, in every phase, on both pages.

**A second, latent one from Stage 4.** `SystemIcon`'s weapon-select gate ends
`… || weaponManager.canManuallyInterceptWith(ship, system)` (§4.7a). `&&` binds tighter than `||`, so
once the three phase clauses ahead of it are false — which at gamephase −2 they always are — the
`||` chain reaches that call. Clicking any system in the lobby ship window threw the same way. Fixed
in the same edit, before it was ever reported.

**Fix.** Both added to the stub, returning `false` (nothing is declared pre-game), with a warning
above the object stating the invariant: *any new `weaponManager.x()` call in `reactJs/system/*` that
is not behind a −2 early return has to be added here too.* Now trap 12 in §6.

`selectWeapon` / `unSelectWeapon` are deliberately left **out**. They sit inside the gate that is now
provably false at −2, and if someone later widens that gate a crash is a better outcome than a
silent no-op that quietly does nothing.

**Verified** by extracting the stub out of `gamelobby.php`, evaluating it, and diffing its keys
against every `weaponManager.*` call in `SystemIcon.js`: ten of twelve stubbed, the two omissions
being the deliberate ones above. `php -l` clean; no BOM introduced ([arch_php_entry_bom_trap] — this
is a PHP entry file).

**Why the sweep did not catch it:** Stage 7 swept *weapon classes*, and this is a page-level plumbing
gap. Nothing about the Slicer, the Point Pulsar or any other class was involved.


---

## 16. The Slicer intercepted twice — game 4306, 2026-08-20

*"I set 22 defensive shots with one slicer, and when the server resolved the firing there was 44
defensive shots against the 4 incoming missiles."*

### 16.1 What the database said

    type           shots  damageclass   n   distinct targets
    selfIntercept    1    molecular     16        1
    selfIntercept    0    molecular      6        1
    intercept        0    molecular     22        2      <- client, manual
    intercept        1    (empty)       22        3      <- SERVER, automated

The client was **correct**. 22 markers — 16 dice plus 6 whole blocks out of a Medium Slicer's
16-dice / 36-set pool, exactly the whole volley — and 22 manual intercept orders, one per marker,
carrying `shots: 0` and `damageclass: molecular` as §14.4 specifies.

The other 22 are the server's own shape: `defaultShots` and no damageclass, which is what
`Firing::addToInterceptionTotal` writes. **The automation spent all 22 markers a second time.**

### 16.2 Why: `getInterceptionMod` counts distinct targets, `$guns` counted orders

`MolecularSlicerBeamL::getInterceptionMod` gates payout on
*distinct `intercept` targetids* ≤ *`selfIntercept` count*. Those 22 engagements were stacked on
**two** shots, so the server read them as "2 of 22 used" and never refused.

That alone would not have created orders — `getBestInterception` skips any shot whose mod is `<= 0`.
What created them was the **gun budget**. `automateIntercept` derives a split weapon's remaining
budget as:

    currGuns = guns - count(fireOrders);   then +1 per selfIntercept order

and `MolecularSlicerBeamL::beforeFiringOrderResolution` synthesises `$guns` by adding 1 for *every*
fire order (a deliberate hack, so that expression collapses to `markers + spare` — the Slicer's real
allowance). A manual intercept order landed in **both terms** and cancelled itself out:

    guns = 44 (22 markers + 22 intercepts) + 0 spare
    currGuns = 44 - 44 = 0,  then +22 for the markers  =  22

Twenty-two guns, twenty-two more intercepts. Each one passed `getInterceptionMod`'s
distinct-target test, so each paid out its full −10%.

### 16.3 The fix — one line, server side

`beforeFiringOrderResolution` no longer pads `$guns` for an `intercept` order:

    if ($order->type != "intercept") $this->guns++;

Now `guns = markers + normals + spare`, and the budget collapses to what is actually left:

    currGuns = (markers + normals + spare) - (markers + normals + manual) + markers
             =  markers - manual intercepts + spare

Game 4306's case gives **0**. A Slicer with no manual orders gets exactly the number it always did.

**Why the fix is on the server and not the client.** Counting distinct targets client-side instead
would "agree" with `getInterceptionMod`, but it would undercharge the pool — one die would buy any
number of engagements against the same shot. One order is one engagement and costs one die; the
server had to be taught to subtract them.

### 16.4 Where §14.4's reasoning was wrong

§14.4 said, of counting capacity per order rather than per distinct target:

> the server counts distinct targets, which can only ever be fewer, so stacking two engagements on
> the same shot leaves the server's test satisfied with a marker to spare rather than short. Erring
> in that direction is deliberate.

The premise was right and the conclusion was backwards. **A marker to spare is not the safe side** —
a spare marker is budget the automation will spend. The comment in `molecular.js` has been corrected
in place, and points here.

### 16.5 Verification

* Scratch PHP against the real classes inside the container, asserting the invariant
  `budget == markers - manual + spare` across seven shapes: game 4306's exact set (budget **0**),
  markers-only (**22**, unchanged from before the fix), partly hand-assigned, mixed offence and
  defence with pool left over, purely offensive, minimum charge, and a two-turn charge. All pass,
  and four manual orders reduce the budget by exactly four.
* `php -l` clean; legacy bundles rebuilt un-minified.
* **Replay harness `check`: 159 passed, 1 failed (46.0s).** The single failure, game 4297
  (`/ships/1/systems/2/output: 6 -> 8`), is **pre-existing blueprint drift** — verified by
  `git stash push -- source/server source/public/gamelobby.php`, re-running, and getting the byte-identical
  diff on a clean tree. Expected: the harness never calls `prepareFiring`, and both the `tohit` and
  `damage` checks skip `intercept`/`selfIntercept` orders outright, so `beforeFiringOrderResolution`
  is not on any path it exercises.

### 16.6 Still worth doing

Trap 11 now has a real case to record: game 4306 carries the first manual intercept orders in the
corpus. Re-record it once the turn count is stable so a future change to this arithmetic has
something to fail against.

---

## 17. Fourth refinement round — 2026-08-20

Two display fixes on the INCOMING row and the first player-facing documentation of the feature.

### 17.1 A Slicer row now says what the shot is MADE OF

Reported: *"Slicer hit chances are displaying 93-93% for a single shot."*

Two separate things were wrong with that cell, and they had the same cause — the Shadow branch of
`chanceText` was written before the rest of the column was, and never caught up:

```js
if (ball.weapon.data["Offensive Dice"]) {          // Molecular Slicer
    chanceText = joinRange(displayMin, displayMax) + ' (' + (amount == 1 ? ball.fireOrder.shots : shots) + ' dice)';
}
```

* **It always printed a range**, with no `displayMin !== displayMax` guard — the guard every other
  branch has. A lone Slicer shot has one hit chance, so it read `93-93%`.
* **`shots` was summed across GROUPS.** It came from the `hitchanceLists` loop, which adds
  `b.fireOrder.shots` for every ballistic sharing this row's shooter and weapon *regardless of
  which group it fell into*. A Slicer that put two shots on the same fighter flight (the only unit
  that may be targeted more than once) therefore reported the whole volley's dice on both rows.
* **It named dice only.** A Slicer shot is dice **and** set damage (§14.4); the set-damage half was
  invisible, and the number sat after the percentage where it read as part of it.

**The whole branch is gone.** A Slicer order is `type: 'normal'` with `damageclass: 'Sweeping'`
(that is what `getAllBallisticsAgainst` filters on), so with the dice text removed it lands on the
`type == "normal"` branch below and is formatted exactly like every other split weapon —
single-shot rows print one number, groups print a range only when their ends differ.

The allocation moved onto the weapon label, after the firing mode, as new `diceSuffix()`:

    Sharlin War Cruiser
        1x Molecular Slicer (Sweeping) (3d + 12)      93%

    ▼ 2x Molecular Slicer (Sweeping) (4d + 18)        88%
        Molecular Slicer (Sweeping) (3d + 12)         93%
        Molecular Slicer (Sweeping) (1d + 6)          88%

`diceSuffix` sums over the **row's own members** — the group for a collapsed row, the single member
for a sub-row — which is what the row describes and what the old cross-group sum got wrong. Set
damage comes from `MolecularSlicerBeamL.getOrderSetDamage`, so the "committed before the page was
reloaded" case (only the encoded `MSB|d:x|s:y` token in `->notes` survives) is handled by the
weapon, not re-implemented in the menu.

`joinRange` also collapses equal ends now. Every caller guards on `lo !== hi` itself, so it is belt
and braces — but a range of one is never the right thing to print, and the caller that printed one is
how this was found.

### 17.2 The feature is documented for players

* **[faq.php](source/public/faq.php)** — new *Interception* section (`#interception`, listed in the
  contents between Hangar Operations and Jump Drives). Covers the rating → −5%/point arithmetic,
  degradation and the ballistic exemption (§2.1), what the automation does and the
  loading-time-over-one-turn marker rule (§13.4), the four-step manual flow, the *"declared orders
  only, not floored at zero"* caveat on the number (§4.5, §13.1), greedy fill and the caret (§4.2),
  R2/R3/R4/R7, and the geometry of who may cover whom.
* **[factions-tiers.php](source/public/factions-tiers.php)** — the Shadow Association *Molecular
  Slicer Beam* entry gains the manual path (§14.4): what an engagement costs, that already-bought
  capacity is re-used rather than paid for twice, that it counts as a shot for the −5%, and that
  withdrawal hands the die back. Also records that the Light Slicer has no intercept rating at all,
  and explains the new `(3d + 12)` label. Two typos in the adjacent self-intercept bullet fixed in
  passing (*"1d10 dice of 6 set-damage"* → *or*; a stray `".`).

### 17.3 Limitation found while writing the FAQ — recorded, not fixed

**Third-party manual interception is only reachable in friendly-fire games.** The INCOMING list is
built only by `FirePhaseStrategy.selectShip`, which `PhaseStrategy.onShipClicked` reaches only for
`isMyShip(ship)` — and outside the `friendlyFire === 1` branch `selectShip` ends with
`this.setSelectedShip(ship)`. So the interceptor passed to the menu is always the ship whose tooltip
you are looking at, and there is no way to keep ship A selected while opening ship B's list.

That makes two of §4/§10's cases hand-unassignable in an ordinary game: a `freeintercept` weapon
covering a friendly, and a fighter flight covering the ship it escorts. Both still work — the
automation places them exactly as before — and both are reachable when friendly fire is on, where
`selectShip` deliberately keeps the current selection and offers a *Select ship* button instead.
The FAQ says covering someone else is normally left to the automation rather than describing a flow
that does not exist.

### 17.4 Verification

* `node --check` clean on `ShipTooltipBallisticsMenu.js`; both legacy bundles rebuilt.
* 13 assertions over the extracted `joinRange` / `diceSuffix` / `getOrderSetDamage` helpers, all
  passing: equal and negative ranges, a null low end, single shot, a two-member group, dice-only,
  set-damage-only, the `MSB` notes fallback, and the three not-a-Slicer paths.
* `php -l` clean on `faq.php` and `factions-tiers.php`, both still CRLF and BOM-free
  ([arch_php_entry_bom_trap] — written through node, not PowerShell).
* Not play-tested.
