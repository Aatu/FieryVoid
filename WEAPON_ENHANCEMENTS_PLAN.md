# System Enhancements — Implementation Plan

Status: **BUILT 2026-08-15 — Stages 0–7 complete, automated tests green. Play-tested; refinement
round 1 applied (see below).**
Written 2026-08-12 against the tree at `d24a27ec7`; open questions O1–O6 answered by the user the
same day and folded in (§9). Built against `e5ce444bb`.

### What was verified, and how

| Stage | Exit test | Result |
|---|---|---|
| 0 | scratch round-trip, FK cascade, DECIMAL-as-string | 17/17 |
| 1 | offers per faction age, D6, exclusions, **Twin Array 32 / 48**, arc counting, payload size | 17/17 |
| 2 | apply, shield penalties, mode switch, null FC, 10 doctored payloads | 26/26 |
| 3 | pricing, idempotence, **two identical hulls**, copy, clear, D11 sweep (node) | 44/44 |
| 4 | `renderToString` of both components + the combined menu (esbuild + evaluate) | 14/14 |
| 5 | POST → sanitise → DB → game load → `onConstructed` applies | 17/17 |
| 6 | save/reload, **and the three §4.7.1 drift cases** | 17/17 |
| — | replay harness `check` (Stage 2 changes serialised fields) | **161 passed, 0 failed** |

Manual Docker play-through is still the real gate (§8, last row) — nothing here tests the *rules*,
only that the machinery does what it says.

### ⚠️ Three deviations from this document, all deliberate

1. **§2.1 is out of date: `interceptArray` DOES exist.** Missile launchers derive a per-mode
   intercept rating from loaded ammo ([missile.php:1350](source/server/model/weapons/missile.php#L1350))
   and `changeFiringMode` re-reads it over `->intercept`
   ([weapon.php:2978](source/server/model/weapons/weapon.php#L2978)) — the same evaporate-on-mode-switch
   hazard §2.2 flags for Gunsights. Measured over the whole corpus: **no currently-eligible weapon has
   one**, so it is inert today. `SYS_ADT` mirrors the bump across modes anyway and says why.
2. **The offer tuple is 5 slots, not 8.** §3.2 assumed offers would share the purchase shape and set
   "under 1 KB per ship" as the condition for keeping the human name on the wire. Measured: the full
   shape cost **1,921 bytes/ship** (2,557 classes, 85,025 offers). Dropping the label (the client holds
   `LABELS`) and `sysname` (D13's check applies to a stored *purchase*, not an offer) gives **736
   bytes/ship**, i.e. **+1.97% on `Earth Alliance.json`** — confirmed against the regenerated statics.
3. **`SYS_GSGT` has a deny-list.** §2.2's rule offered Gunsights to 498 zero-damage mounts across 30
   classes at the 4pt floor. Most of those *do* roll to hit and want it; a few are utility mounts that
   would get nothing. Per the user's call, `Enhancements::$gunsightExcluded` names eight classes
   (`AbbaiShieldProjector`, `AegisSensorPod`, `CombatTransporter`, `GrapplingClaw`, `GraviticShifter`,
   `GromeTargetingArray`, `MicroJumpSystem`, `NexusChaffLauncher`) rather than a blanket
   `maxDamage > 0` test, which would have taken the Burst Beams and Tractor Beams out with them.

### One thing §5.2 did not anticipate

`tac_saved_ship.enhvalue` stores all **three** cost buckets added together, and `getSavedShips` hands
the lot back as `pointCostEnh` — so `loadSavedFleet` has to **split the stored total apart again**
before setting `pointCostSysEnh`, or every refit is counted twice the moment the client adds the third
bucket on. See the comment at that site.

### Refinement round 1 — 2026-08-15, after the first play-test

Five reports, all display-layer except the last. Verified: replay harness `check` **161 passed, 0
failed**; a scratch build of a young hull with `SYS_ADT` + `SYS_GSGT` round-tripped through
`onConstructed` → `beforeTurn` → `stripForJson` for an owner and for an enemy (17/17).

| # | Report | Cause | Fix |
|---|---|---|---|
| 1 | The ✦ badge is too small, **and absent in game.php** | 7px on a 32px icon; and `stripForJson` never sent `systemEnhancements`, so `hasAny()` was false for every ship in game | 11px; `ShipClasses::stripForJson` sends the purchase rows, **own team only** via `isRevealedToCurrentViewer()` — which is also what keeps `SystemIcon` free of a client-side userid test |
| 2 | Improved Thrust Rating's row price lags a step | The price column switched to the row's **running total** the moment a level was bought (12, then 12, 26, 42 for a 12/14/16 refit), which reads as a lagging price | The column now always quotes **the next level's cost**; at the limit it falls back to the spend, dimmed and labelled. The running total already had two homes (the section's "Refits: n pts" and the points panel) |
| 3 | Hardened Shields / Armour / Thrust show in the ship window, **ADT and Gunsights do not** | ⭐ The user's own guess was right. `output` and `armour` are displayed from the **live field**; `intercept` and `fireControl` are quoted only through `$system->data`, which is baked into the static blueprint at generation time | Lobby: `systemEnhancements.syncInterceptData` / `syncFireControlData` rewrite the two entries in `apply()` (new object — `data` is shared by reference) |
| 4 | ADT does not change the intercept rating in game.php either | Same cause. The rating **was** applied — `data` is even rebuilt from the enhanced value in `beforeTurn` — but `stripForJson` does not send `data` for an ordinary system, so the browser kept the blueprint's copy | `data` added to `SYS_ADT` / `SYS_GSGT`'s registry `serialise` list. Exactly the precedent `HyachSpecialists` sets at [weapon.php:455](source/server/model/weapons/weapon.php#L455) — *"Defence modifies intercept rating, show in system window"* |
| 5 | "System Enhancements (n)" should not appear to other teams | D8 made the **badge** own-team-only and left the **summary line** public, which is the same tell in words | `Enhancements::stripSystemEnhancementSummary`, applied in `stripForJson`. The ship-**level** lines stay public as they always were; if that empties the tooltip the ship sends none, which is what a hull carrying only refits did before this feature existed |
| 6 | Damage and Critical Effects are the same bar | §6.1 gave the gold section its own colour and left the two blue sections sharing one `SectionHeader` | **Three tints of one geometry**, all three now declared together in `system/menuControls.js`: bronze (bought) / teal `#23506b` (structure) / indigo `#23386b` (malfunctions). The two blue bars share their red and blue channels and differ almost entirely in green — two sections, not two windows. `CriticalEffectsSection` no longer re-exports the damage bar |
| 6b | Both blue bars too dim; no break between them | The first pass pitched them at the old `#1b3b50`, which sank into the menu body and read as a caption; and three editors need **two** breaks, not one | Both lifted a stop (fills and hairlines), still below the menu's own `#215a7a` title bar. `SectionDivider` gained a `$chrome` tone for a break between two peer sections, and `CriticalEffectsSection` renders its own — at the call sites it would dangle over an empty gap whenever that component decides to render nothing |
| 6c | The menu's own title bar now clashes | Brightening the sections left the `#215a7a` title within ~12° of hue and a hair of luminance of the Damage bar — a fourth section rather than the window's name | Title moved to `#3f5a7d`: the **hue midpoint of its two blue children** (202° / 225°), low chroma beside them, brighter than all three bars, and roughly the **complement of the bronze** — which is what makes the gold pop. It is also the family `theme.colors.line` already belongs to (215°), so frame and title are siblings for free. Title + fill + border extracted to `MenuHeader` / `MENU_CHROME` and adopted by **all three** lobby damage editors, which were carrying three copies of one title bar. ⚠️ Element `opacity` dropped from all three containers — it compounded with the fill's alpha and faded the TEXT; `ApplyDamageMenu`'s `opacity: 1 !important` on the header was a no-op, since a child cannot opt out of an ancestor's group opacity |
| 7 | "System Enhancements (n)" counts refits | It counted purchase ROWS | It counts **distinct systems**, both sides. The number is the count of ✦ badges on the ship window — the thing the player can go and look at — and a gun wearing both Gunsights and ADT is still one enhanced gun. `systemEnhancements.count()` keeps meaning rows (its two callers are `> 0` tests); the new `systemsEnhanced()` drives the line |

⚠️ **Items 3–5 do not change §6.3's ceiling.** The enemy still receives the enhanced `intercept` and
`fireControl` (they were in `serialise` from day one) and now sees them in the tooltip too, so a
veteran comparing a mount against the published SCS can still infer the refit. Item 5 hides the
**label**, not the fact. Real masking is still §12, and the registry is still what makes it one line
per entry.

Extends the lobby's `ApplyDamageMenu` (PREBATTLE_DAMAGE_PLAN.md §5.2, §11) from a damage editor
into an **"Add Enhancements & Damage"** editor: a gold enhancements section above the existing blue
damage/criticals sections, offering five **per-system** refits on young-tech hulls.

The single new capability is that an enhancement can now be attached to **one system** rather than
to the whole ship. Everything else — pricing, persistence, the buy POST, saved fleets, the in-game
application — is a parallel track alongside the existing ship-level machinery, deliberately kept
*beside* it rather than folded *into* it. §0 D1/D2 explain why that separation is the safe choice
and not merely a conservative one.

> ### The feature is called **System** Enhancements, not Weapon Enhancements
> Three of the five refits are not on weapons at all — shields, armour, thrust. Naming it for the
> general case costs nothing now and saves a migration later. Every identifier is therefore
> `systemEnhancement*` / `tac_sys_enhancements` / `SYS_*`, never `weapon*`.
>
> ### ⚠️ Officers are NOT part of this plan and need nothing from it
> An earlier draft built an "officer seam" into v1 — a `funding` discriminator, an assignment
> pool, a two-group section component. **That was wrong and has been removed.** Once officers are
> always assigned to a fixed system *by type* rather than by player choice, they are indis-
> tinguishable from enhancements the codebase has shipped for years. See **§11**, which is now
> two paragraphs and a recipe rather than a design. Nothing in v1 is shaped by them.

---

## 0. Decisions

| # | Decision | Rationale |
|---|---|---|
| **D1** | **New tables, NOT a primary-key migration.** `tac_sys_enhancements` (games) and `tac_saved_sysenh` (fleet lists), each mirroring its existing twin plus a `systemid` column. | `tac_enhancements` PK is `(gameid, shipid, enhid)` ([emptyDatabase.sql:140](db/emptyDatabase.sql#L140)) and `tac_saved_enh` PK is `(listid, shipid, enhid)` ([:514](db/emptyDatabase.sql#L514)). A per-system enhancement needs `systemid` in the key — six Twin Arrays each with Gunsights is six rows, not one. Adding a column and rebuilding the PK on the two tables that **every existing game and every saved fleet reads on every load** is the largest-blast-radius change available for the smallest gain. Additive tables leave every existing query, the replay corpus and the deploy path untouched. |
| **D2** | **A separate array `$ship->systemEnhancements`, NOT index 8 on `enhancementOptions`.** | `confirm.js` renders one buy-dialog row per `enhancementOptions` **index** ([confirm.js:1042](source/public/client/UI/confirm.js#L1042)), and `gamedata.readBulkPurchase` walks `.selectAmount.shpenh<N>` by that same index **until the first gap** ([gamelobby.js:2761-2777](source/public/client/gamelobby.js#L2761)). Filtering per-system entries out of the dialog makes the class indices sparse, the `while` loop stops at the first skipped row, and **every enhancement after it silently prices as zero and is cleared from the ship**. Separate arrays means zero change to the most fragile pricing code in the lobby. |
| **D3** | **The offer list is generated server-side, behind a gate.** `Enhancements::$offerSystemEnhancements` (default `true`), set `false` on the in-game blueprint path. | Only the lobby needs "what *may* be bought"; in game only the bought rows matter, and they come from the DB. Exactly the precedent `$offerChoiceLists` already sets at [Enhancements.php:16](source/server/model/ships/Enhancements.php#L16), for exactly the same reason. `ShipSystem::stripForJson` calls `addSystemEnhancementsForJSON` for **every system of every ship on every load** ([ShipSystem.php:283](source/server/model/systems/ShipSystem.php#L283)) — the new code there must short-circuit on an empty array before it does anything else. |
| **D4** | **Prices are re-derived server-side at buy time; the client's claim is discarded.** One function, `Enhancements::systemEnhancementPrice($ship, $system, $enhID, $level)`, is called by the offer generator, by the buy-time validator and by the saved-fleet write. | Ship enhancements today trust the client's `pointCostEnh` wholesale ([Manager.php:1216](source/server/controller/Manager.php#L1216)). That trust is inherited, not endorsed; do not extend it to a new surface. Re-deriving is one arithmetic pass per bought ship — free — and it is also what makes a *stale saved fleet* re-price correctly when a hull's blueprint changes between sessions. |
| **D5** | **A third cost bucket, `ship.pointCostSysEnh`.** Folded into `pointCost` by every path that folds the other two; never written by the dialog readers. | `readBulkPurchase` and `doEditShip` **rewrite `pointCostEnh`/`pointCostEnh2` from zero** off the dialog spinners each time they run ([gamelobby.js:2755](source/public/client/gamelobby.js#L2755)). System enhancements are not in that dialog, so folding them into either bucket means **an Edit silently refunds every system enhancement while leaving them applied**. A third field is the only version that survives an edit. `getPristinePointCost` must peel all three. |
| **D6** | **Gate on `$ship->factionAge <= 2`, and additionally skip a weapon whose own `$system->factionAge >= 3`.** *(O1 — confirmed: hull age is the primary gate, the weapon check does no harm.)* | Ship-level age is the requested gate. But `factionAge` **also exists on weapon classes** — `customDevelopment.php` sets `public $factionAge = 3` on ~15 weapons ([:432 onward](source/server/model/weapons/customDevelopment.php#L432)) — and those are Ancient guns bolted onto young hulls. A young-tech refit on an Ancient weapon is the one case the ship-level gate lets through by accident. ⚠️ `factionAge` is **not declared on `ShipSystem`**, only on the handful of weapon classes that set it — so the test must be `isset($system->factionAge) && $system->factionAge >= 3`, never a bare read. |
| **D7** | **Ships only. No flights, no mines, no terrain.** | A flight's per-fighter system ids **do not exist in the lobby** (PREBATTLE_DAMAGE_PLAN.md §1.1) and a bulk mine's clone rebuilds `systems` **0-indexed**, so `getSystemById` resolves the wrong system on it ([BuyingGamePhase.php:463-467](source/server/Phase/BuyingGamePhase.php#L463)). `canApplyPreBattleDamage` already excludes both for precisely these reasons ([SystemInfoButtons.js:791](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L791)) — reuse that exclusion rather than re-deriving it. |
| **D8** | **The gold star is own-team-only, decided server-side.** A per-system boolean on the stripped payload, not a client-side `userid` comparison. *(O3 — confirmed sufficient for v1; true per-viewer masking recorded as a later revision, §12.)* | Matches the `isRevealedToCurrentViewer` pattern ([[project_kirishiac_orbitals]]). ⚠️ **This is cosmetic hiding only** — see §6.3: several of the enhanced *stat values* must reach the enemy for their own hit-chance/damage previews to be right, so the badge hides the label, not the fact. Say so out loud rather than implying a masking guarantee the feature does not make. |
| **D9** | **`SYS_` prefix on every new enhID.** `SYS_ADT`, `SYS_GSGT`, `SYS_HSHLD`, `SYS_HARM`, `SYS_THR`. | `enhid` is `varchar(10)` in both tables — all five fit (7–9 chars). One prefix test distinguishes system-level from ship-level anywhere, and it sidesteps the collision with the **existing `GUNSIGHT`** (Repeater Gunsights, [Enhancements.php:287](source/server/model/ships/Enhancements.php#L287)), which is a *different* enhancement with the same English name. |
| **D10** | **Offer prices and limits are computed from BLUEPRINT values, before any ship-level enhancement is applied.** | `ELITE_CREW` raises every thruster's output ([lobbyEnhancements.js:153](source/public/client/lobbyEnhancements.js#L153)) and `VOR_AZURS` raises every EM Shield's. If the offer were priced after them, buying Elite Crew would retroactively change what a thruster refit costs and how many levels the "up to double" cap allows. Price once, from the hull as designed, and **store the price with the purchase** so it can never drift. |
| **D11** | **A destroyed system offers nothing, and a refit on a system that becomes destroyed is REMOVED and REFUNDED.** Damage wins; the removal is one-way. *(O6.)* | You cannot refit a wreck. The two editors live in the same menu, so the player can destroy a system they just refitted — the points must come back the moment they do, visibly, not silently persist into a POST that will apply a refit to a destroyed box. **Un-destroying does not restore the refit**: it is gone, and the player re-buys it. One-way is honest and matches how a flight-size change clears pre-battle damage rather than trying to re-map it (PREBATTLE_DAMAGE_PLAN.md §5.3). ⚠️ "Destroyed" here **includes the structure cascade** — destroying a Structure block destroys every system in its location, so the sweep must run off the same `system.destroyed` the preview writes, not off the payload's `k` flags. |
| **D12** | **One registry, not five switch statements.** `Enhancements::$systemEnhancementRegistry` maps each enhID to a descriptor: label, and the callables for eligibility / price / limit / apply, plus the list of fields to serialise. | Every existing enhancement is a `case` in four separate `switch` blocks spread over 1,900 lines of `Enhancements.php` — adding one means finding all four, and the fourth (`addSystemEnhancementsForJSON`) is the one that gets forgotten, because forgetting it is invisible until someone reads a tooltip. A registry makes "add an enhancement" a single literal and makes the client's label map a mechanical mirror of one array. It also turns §12's masking work into one line per entry. **Scoped to the five new IDs — this is not a refactor of the existing ship-level switches**, which work and are not worth the churn. |
| **D13** | **Store the system's `name` alongside its id, and verify both on load.** | A stored `systemid` is a *positional* id ([[arch_positional_system_id_trap]]). Saved fleets outlive the blueprint they were priced against (§4.7.1), and if a contributor inserts a system mid-constructor, id 14 silently becomes a different system. Verifying the name turns a **mis-applied refit** into a **dropped** one. This is exactly the reasoning already written into `getStoredEnhancementName` for `CHAM_DISG` — store the name as well as the index, because the list is rebuilt from disk each time ([Enhancements.php:2015-2030](source/server/model/ships/Enhancements.php#L2015)). |

---

## 1. Survey — what exists

### The enhancement machinery (all ship-level today)

| Concern | Where |
|---|---|
| Offer generation | `Enhancements::setEnhancementOptionsShip` ([Enhancements.php:191](source/server/model/ships/Enhancements.php#L191)) — builds `$ship->enhancementOptions[]` tuples `[id, name, taken, limit, price, priceStep, isOption, choices?]` |
| Application (server, in game) | `Enhancements::setEnhancements` ([:1984](source/server/model/ships/Enhancements.php#L1984)) → `setEnhancementsShip` ([:2213](source/server/model/ships/Enhancements.php#L2213)), called from `BaseShip::onConstructed` **before** the per-system `onConstructed` loop ([ShipClasses.php:1511-1519](source/server/model/ships/ShipClasses.php#L1511)) |
| Systems an enhancement MOUNTS | `Enhancements::addEnhancementSystems` ([:1911](source/server/model/ships/Enhancements.php#L1911)), called early from `DBManager::getTacShips` |
| Ship-level JSON fixups | `addUnitEnhancementsForJSON` ([:3067](source/server/model/ships/Enhancements.php#L3067)) — only called when `enhancementTooltip !== ''` |
| **System-level JSON fixups** | `addSystemEnhancementsForJSON` ([:3079](source/server/model/ships/Enhancements.php#L3079)) — **already exists**, already the canonical "this enhancement changed a system, re-send the changed fields" hook. Called unconditionally per system from [ShipSystem.php:283](source/server/model/systems/ShipSystem.php#L283) |
| DB write (game) | `DBManager::submitEnhancement` ([:174](source/server/controller/DBManager.php#L174)), called from `BuyingGamePhase::process` ([:448](source/server/Phase/BuyingGamePhase.php#L448)) |
| DB read (game) | `getEnhancementsForGame` ([:3316](source/server/controller/DBManager.php#L3316)) → `getEnhancementsForShips` ([:2750](source/server/controller/DBManager.php#L2750)), which rebuilds tuples as `[enhid, enhname, numbertaken, 0,0,0]` and runs **deliberately early**, before criticals/damage |
| DB write/read (saved fleets) | `submitSavedEnhancement` ([:301](source/server/controller/DBManager.php#L301)) / `getSavedEnhancementsForShip` ([:440](source/server/controller/DBManager.php#L440)), driven from `Manager::submitSavedFleet` ([:787](source/server/controller/Manager.php#L787)) and `loadSavedFleet` ([:957](source/server/controller/Manager.php#L957)) |
| Client application (lobby) | `lobbyEnhancements.apply(ship)` ([lobbyEnhancements.js:21](source/public/client/lobbyEnhancements.js#L21)) — a full JS mirror of `setEnhancementsShip`, once-per-build via `ship.enhancementsApplied` |
| Client display | `EnhancementsPanel` (gold box) ([ShipNotesPanel.js:370](source/public/client/UI/reactJs/shipWindow/ShipNotesPanel.js#L370)); fleet-list card lines in `updateFleet` ([gamelobby.js:532-552](source/public/client/gamelobby.js#L532)) |
| Wire | `newShip.enhancementOptions = ship.enhancementOptions` — sent **verbatim, whole tuples** by both `construcGamedata` ([ajaxInterface.js:1049](source/public/client/ajaxInterface.js#L1049)) and `constructSavedShips` ([:644](source/public/client/ajaxInterface.js#L644)); read back at [Manager.php:1913](source/server/controller/Manager.php#L1913) and [:1228](source/server/controller/Manager.php#L1228) |

### The menu being extended

`ApplyDamageMenu` ([ApplyDamageMenu.js](source/public/client/UI/reactJs/system/ApplyDamageMenu.js)) —
blue chrome, `CritSectionHeader` section bars, `CriticalEffectsSection` beneath. Gated by
`canApplyPreBattleDamage` ([SystemInfoButtons.js:791](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L791)),
which already encodes lobby-phase + own-ship + not-committed + not-flight + not-mine +
not-pseudo-system. Its `max-width: 300px` is **load-bearing**, not cosmetic — read the comment at
[:30](source/public/client/UI/reactJs/system/ApplyDamageMenu.js#L30) before adding any `<select>`.

### The gold theme to reuse

`theme.colors.enhText` `#d8be86` ([theme.js:27](source/public/client/UI/reactJs/styled/theme.js#L27)),
title `#e8cf93` on `rgba(169,128,56,0.30)`, border `#8a6d3b` — all in `EnhTitle` / `Block $gold`
([ShipNotesPanel.js:206](source/public/client/UI/reactJs/shipWindow/ShipNotesPanel.js#L206)).
⚠️ Those are literals in that file, not tokens. Extract them into `styled/theme.js` as
`colors.enhTitle` / `colors.enhBg` / `colors.enhLine` **first**, and repoint `ShipNotesPanel` at
them, so the new menu and the existing panel cannot drift ([[project_visual_unification]]).

---

## 2. The five enhancements

All five are **per-system**: one row per (ship, system, enhID). All five require
`$ship->factionAge <= 2` (D6).

Prices below use the existing tuple convention, which `confirm.js` and the new menu both total as
`price + i * priceStep` for level *i* (0-based).

### 2.1 `SYS_ADT` — Advanced Defensive Targeting

| | |
|---|---|
| Eligible | `$system instanceof Weapon && $system->intercept > 0` |
| Effect | `intercept += level` |
| Limit | `max(0, 4 - $system->intercept)` — the resulting rating caps at 4 |
| Price | `8 * $system->intercept * $system->guns` |
| Step | `8 * $system->guns` |

The **cost is per resulting rating, multiplied by the mount's gun count** (O2). Rating is a single
per-weapon value — there is no per-gun rating — and it reads as `-5%` per point to the incoming
to-hit, so a Twin Array at 2 is `-10%`.

> **Worked example — Twin Array** (`intercept = 2`, `guns = 2`).
> Level 0 takes it to rating **3**, priced at the rating-3 tier of 16, doubled for two guns:
> `8 × 2 × 2 = 32 pts`. Level 1 takes it to rating **4** at the rating-4 tier of 24, doubled:
> `price + 1×step = 32 + 16 = 48`, i.e. `24 × 2` ✓. Limit is `4 − 2 = 2` levels.

The same formula reproduces the requested 8 / 16 / 24 for a single-gun weapon starting at 1, and
starts at the right tier for one starting at 2 or 3 — the current rating *is* the tier index.

* There is **no `interceptArray`** — verified by grep; intercept is not per-firing-mode. Nothing to
  mirror across modes.
* `$system->guns` defaults to 1 ([weapon.php:145](source/server/model/weapons/weapon.php#L145)) but
  is **mutated at runtime** by several classes (`$this->guns = 2 + $this->getBoostLevel($turn)`,
  [defensive.php:432](source/server/model/weapons/defensive.php#L432); Trek weapons assign it from
  shot counts). Price off the **blueprint declaration**, per D10 — read it at offer time on a
  freshly-constructed ship, never in game.
* `gunsArray` (per-mode gun counts) exists on ~8 classes. Use the **maximum** across modes for
  pricing so a mode switch cannot make a purchase retroactively cheap.
* Consumers to sanity-check: `Weapon::getInterceptRating`
  ([weapon.php:768](source/server/model/weapons/weapon.php#L768), [:1987](source/server/model/weapons/weapon.php#L1987)).

### 2.2 `SYS_GSGT` — Gunsights

| | |
|---|---|
| Eligible | `$system instanceof Weapon` and at least one entry of `fireControl` is not `null` |
| Effect | every **non-null** entry of `fireControl` +1, **and** every non-null entry of **every mode** in `fireControlArray` +1 |
| Limit | 1 |
| Price | `max(4, ceil($maxDamage * 0.25)) * $guns` |
| Step | 0 |

> ### ⚠️ The single most important trap in this feature
> `Weapon::changeFiringMode` **re-reads `fireControlArray[$mode]` straight over `fireControl`**
> ([weapon.php:2929 region](source/server/model/weapons/weapon.php#L2929) — same block that
> re-reads `gunsArray`). A refit that bumps only `fireControl` **evaporates on the player's first
> mode switch**, silently, mid-game. Both must be bumped. Stage 3's exit test is a multi-mode
> weapon, switched modes and switched back.

* `null` means "cannot target this size class" and **must stay null** — `null + 1` is `1` in PHP,
  which would silently give a Piercing missile a fighter-targeting capability it does not have.
  Guard with `!== null`, not with truthiness (0 is a legal fire control).
* `maxDamage` is **0 on many weapons** (variable-damage ones carry `maxDamageArray`; support
  weapons genuinely deal none). Price as
  `max($system->maxDamage, max($system->maxDamageArray ?: array(0)))`, then the floor of 4.
* **Reuse `$system->isModified`** to force re-serialisation
  ([weapon.php:68](source/server/model/weapons/weapon.php#L68), consumed at
  [:477-481](source/server/model/weapons/weapon.php#L477)) — it already re-sends `fireControl` *and*
  `fireControlArray`. Do **not** invent a second flag; the Gravitic Augmenter established this one
  ([[project_gravitic_augmenter]]).

### 2.3 `SYS_HSHLD` — Hardened Shields

| | |
|---|---|
| Eligible | `$system instanceof EMShield \|\| $system instanceof GraviticShield` |
| Effect | `output += level` |
| Limit | 1 (per emitter) |
| Price | `10 * $blueprintOutput * $arcs`, where `$arcs = arcWidth / 60` |
| Step | 0 |

* `arcWidth = (($endArc - $startArc) % 360 + 360) % 360`; **`0` means a full circle**, so treat
  `0` as `360` → 6 arcs. Getting this wrong makes an omni-shield free.
* **Shield Projector is excluded for free.** `AbbaiShieldProjector extends Weapon implements
  DefensiveSystem` ([supportWeapons.php:5](source/server/model/weapons/supportWeapons.php#L5)) — it
  is *not* a `Shield` subclass, so the `instanceof` test above already skips it. No special case
  needed, but **assert it in a test** so a future reparenting does not quietly make it eligible.
* "Shield Projector uses base shield value": its boost is `$system->output += $this->output`
  ([:183](source/server/model/weapons/supportWeapons.php#L183)) — the projector's **own** output,
  which no enhancement touches. Already correct; add a comment there so nobody "fixes" it.
* **Ordering is already right and must stay right.** `Shield::onConstructed` derives
  `tohitPenalty`/`damagePenalty` from `getOutput()`
  ([baseSystems.php:916-920](source/server/model/systems/baseSystems.php#L916)), and
  `Enhancements::setEnhancements` runs at [ShipClasses.php:1511](source/server/model/ships/ShipClasses.php#L1511)
  **before** the `$system->onConstructed` loop at [:1513](source/server/model/ships/ShipClasses.php#L1513).
  Call the new applier from the same place. If it were called later, the shield would absorb the
  bonus damage but not confer the bonus to-hit penalty — a half-working shield, which is far worse
  than a broken one because nobody notices.
* Stacks with `VOR_AZURS` (ship-level +1 to all EM Shields). Fine, and correctly priced by D10.

### 2.4 `SYS_HARM` — Hardened Armour

| | |
|---|---|
| Eligible | `$system->armour > 0` and not a pseudo-system |
| Effect | `armour += 1` |
| Limit | 1 |
| Price | `ceil($system->maxhealth * max(2, $system->armour) / 2)` |
| Step | 0 |

* `ShipSystem::getArmourBase` returns `$this->armour` verbatim
  ([ShipSystem.php:1256-1259](source/server/model/systems/ShipSystem.php#L1256)), so a `+= 1`
  works generically for every system, including Structure blocks (`IPSH_ESSAN` already does this
  ([Enhancements.php:3147](source/server/model/ships/Enhancements.php#L3147))).
* **Pseudo-system filter (shared by all five, §4.3):** skip `isTargetable === false`,
  `hideInShipWindow`, `maxhealth <= 0`. That is what excludes `InvulnerableThruster`, whose
  `getArmourInvulnerable` returns 99 regardless
  ([baseSystems.php:3570](source/server/model/systems/baseSystems.php#L3570)) — enhancing it would
  charge points for nothing.
* `addSystemEnhancementsForJSON` must re-send `armour` (there is a `MINE_ARM` precedent at
  [:3152](source/server/model/ships/Enhancements.php#L3152)). Display-only on the client —
  `damageManager.getDamage` subtracts the **per-damage-row** `armour`
  ([damage.js:13](source/public/client/damage.js#L13)), not the system's — but `SystemInfo` shows
  it and it must not lie.
* Worth one explicit test on an **outer-structure-ring hull** (Vree) — no interaction expected, but
  that hull's damage path is the unusual one ([[arch_outer_structure_ring]]).

### 2.5 `SYS_THR` — Improved Thrust Rating

| | |
|---|---|
| Eligible | `$system instanceof Thruster` and not a pseudo-system |
| Effect | `output += level` |
| Limit | `$blueprintOutput` — "up to double the rating" |
| Price | `2 * Σ(blueprint output of every Thruster on this ship with the same `direction`)` |
| Step | `+2` per level — the direction's total rating rises by 1 with each level bought (O4, confirmed) |

* `direction` is 1 retro / 2 main / 3 port / 4 starboard, set in the ctor
  ([baseSystems.php:3517](source/server/model/systems/baseSystems.php#L3517)).
* ⚠️ `ELITE_CREW` adds `enhCount` to **every** thruster's output — server
  ([Enhancements.php](source/server/model/ships/Enhancements.php)) and client
  ([lobbyEnhancements.js:153-157](source/public/client/lobbyEnhancements.js#L153)). D10 pins price
  and limit to the blueprint, so the two are independent; state that in a comment at both sites or
  someone will "fix" one of them.
* `addSystemEnhancementsForJSON` already re-sends `Thruster->output` under the `ELITE_CREW` case
  ([:3086-3090](source/server/model/ships/Enhancements.php#L3086)). Adding a second assignment of
  the same field from the new path is harmless (assignment, not accumulation) but **verify** — this
  is the one place two enhancement systems write the same stripped field.
* `project_server_thrust_validation` reads thruster output for its log-only check. Because the
  refit mutates `$system->output` during `onConstructed`, it is visible there with no change. Worth
  a line in the log-review after the first game.

---

## 3. Data model

### 3.1 The tuple

`$ship->systemEnhancements` — a **flat array on the ship**, one entry per purchase:

```php
array($enhID, $humanName, $count, $limit, $price, $priceStep, $systemid, $sysname)
//      0          1         2       3       4        5           6          7
```

Indices 0–5 are deliberately the **same shape** as `enhancementOptions`, so `describeTaken` and
every price-totalling helper work unchanged on both. Index 6 is the systemid, index 7 its name
(D13). Index 6 on a ship-level tuple is `isOption`, which is why these live in a **separate array**
(D2) rather than sharing one — the two shapes diverge at exactly the index the lobby's pricing loop
reads.

**A `systemEnhancements` entry is never an "option"** — all five cost points and all five are
enhancements. Nothing needs an `isOption` slot.

### 3.2 The offer list (lobby only, D3)

`$ship->systemEnhancementOffers` — same tuple shape, `count` always 0, one entry per
(eligible system × eligible enhancement). Emitted in the static blueprint JSON; **absent in game**.

Rough size: an average hull offers ~12–18 entries (weapons with fire control dominate). At ~55
bytes each that is under 1 KB per ship — acceptable, but **measure it on the largest faction JSON
before Stage 2 signs off**, and drop `$humanName` from the wire (client holds a static label map)
if it is not.

### 3.3 Tables (D1)

```sql
CREATE TABLE `tac_sys_enhancements` (
  `gameid`      INT(11)     NOT NULL,
  `shipid`      INT(11)     NOT NULL,
  `systemid`    INT(11)     NOT NULL,
  `sysname`     VARCHAR(50) NOT NULL,          -- D13: the system's ->name, verified on load
  `enhid`       VARCHAR(10) NOT NULL,
  `numbertaken` INT(11)     NOT NULL,
  `enhname`     VARCHAR(50) NOT NULL,
  `enhvalue`    DECIMAL(10,2) NOT NULL DEFAULT 0,   -- price PAID, see D4/D10
  PRIMARY KEY (`gameid`,`shipid`,`systemid`,`enhid`),
  KEY `idx_shipid` (`shipid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tac_saved_sysenh` (
  `listid`      INT(11)     NOT NULL,
  `shipid`      INT(11)     NOT NULL,
  `systemid`    INT(11)     NOT NULL,
  `sysname`     VARCHAR(50) NOT NULL,
  `enhid`       VARCHAR(10) NOT NULL,
  `numbertaken` INT(11)     NOT NULL,
  `enhname`     VARCHAR(255) NOT NULL,
  `enhvalue`    DECIMAL(10,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`listid`,`shipid`,`systemid`,`enhid`),
  KEY `idx_shipid` (`shipid`),
  KEY `idx_listid` (`listid`),
  CONSTRAINT `fk_sysenh_ship` FOREIGN KEY (`shipid`) REFERENCES `tac_saved_ship` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sysenh_list` FOREIGN KEY (`listid`) REFERENCES `tac_saved_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
```

`systemid` is in the PK but `sysname` is not — the id is the identity, the name is the **integrity
check** (D13). A row whose name no longer matches is dropped, never repaired by searching for the
name elsewhere: after an insertion the *right* system may well still exist at a new id, but
silently relocating a purchase is exactly the guess §4.7.1 refuses to make.

`enhvalue` is `DECIMAL`, matching `tac_saved_ship.enhvalue` and its documented reason
([emptyDatabase.sql:491](db/emptyDatabase.sql#L491)). ⚠️ **mysqli returns DECIMAL as a PHP
string** ([[arch_fractional_enhancement_value]]) — cast on read.

New file `db/systemEnhancements.sql` plus a fold-in to `db/emptyDatabase.sql`, mirroring how
`db/prebattleDamage.sql` was done. Game deletion needs one more `DELETE` beside the
`tac_enhancements` one ([DBManager.php:4278](source/server/controller/DBManager.php#L4278)); saved
fleets cascade for free.

### 3.4 Positional-id safety

Per PREBATTLE_DAMAGE_PLAN.md §1.2, non-flight system ids are pure constructor order and are
identical across the static blueprint, the buy-POST reconstruction and the game load. **One
exception now exists that did not when that was written:** `Enhancements::addEnhancementSystems`
*appends* systems for `SHAD_TEND` ([:1908-1910](source/server/model/ships/Enhancements.php#L1908)).
Appending is safe — nothing already on the hull moves — but note it, and re-validate every stored
`(systemid, sysname)` pair against the freshly-built ship at **every** read boundary. A stale id
must be **dropped, never guessed at** ([[arch_positional_system_id_trap]]). §4.7.1 enumerates the
ways a stored id goes stale and gives the resolution order.

---

## 4. Server

### 4.1 The registry (D12)

One array is the whole definition of a system enhancement. Adding one is a single literal; nothing
else in the file grows a `case`.

```php
/* ⭐ THE definition of every SYSTEM-level enhancement. Scoped to these five: the existing
   ship-level switches are not being refactored.

   serialise  the stripped-payload fields addSystemEnhancementsForJSON must re-send. Naming
              them here rather than in a switch is what stops the "applied but not sent"
              class of bug, which is invisible until someone reads a tooltip. */
private static $systemEnhancementRegistry = array(
    'SYS_ADT' => array(
        'label'     => 'Advanced Defensive Targeting',
        'eligible'  => 'eligibleADT',   //static method names, called via self::$fn()
        'price'     => 'priceADT',
        'limit'     => 'limitADT',
        'apply'     => 'applyADT',
        'serialise' => array('intercept'),
    ),
    'SYS_GSGT'  => array(/* … 'serialise' => array('fireControl','fireControlArray','isModified') */),
    'SYS_HSHLD' => array(/* … 'serialise' => array('output') */),
    'SYS_HARM'  => array(/* … 'serialise' => array('armour') */),
    'SYS_THR'   => array(/* … 'serialise' => array('output') */),
);
```

Two consequences, each removing a class of future bug:

* **`addSystemEnhancementsOwnForJSON` becomes generic** — it walks the ship's purchased rows,
  looks up `serialise`, and copies those fields. No per-enhancement branch, so a new enhancement
  cannot ship with its client-side half forgotten.
* **The client label map mirrors ONE array**, not four switch statements
  ([[project_dev_roadmap]] item 13). Consider emitting it into the static blueprint payload once
  per faction file rather than hand-maintaining it in JS — cheaper and undriftable.

### 4.2 `Enhancements` — new members

```php
public static $offerSystemEnhancements = true;   // D3, mirrors $offerChoiceLists

/* THE eligibility question. One place. Returns the list of enhIDs this system may be
   offered, or an empty array. Pure — no ship mutation, no DB. */
public static function systemEnhancementsFor($ship, $system): array;

/* THE pricing question (D4). $level is 0-based; total for N levels is
   sum(price(0..N-1)). Pure. */
public static function systemEnhancementPrice($ship, $system, string $enhID, int $level): float;

/* THE limit question. */
public static function systemEnhancementLimit($ship, $system, string $enhID): int;

/* Build $ship->systemEnhancementOffers. Gated on $offerSystemEnhancements AND on
   $ship->factionAge <= 2 AND on !($ship instanceof FighterFlight) AND !$ship->mine. */
public static function setSystemEnhancementOptions($ship): void;

/* APPLY $ship->systemEnhancements. Called from BaseShip::onConstructed immediately after
   setEnhancements(), i.e. BEFORE the per-system onConstructed loop (§2.3). */
public static function setSystemEnhancements($ship): void;

/* Validate a client-submitted list against a freshly built ship: resolve each systemid,
   re-check eligibility, clamp count to the limit, and REPLACE the price with the
   server's own (D4). Returns the clean list plus the authoritative total. Never throws. */
public static function sanitiseSystemEnhancements($ship, array $raw): array;

/* Per-system JSON fixups for the new track. Called from the existing
   addSystemEnhancementsForJSON, behind an early `if (empty($ship->systemEnhancements)) return`. */
public static function addSystemEnhancementsOwnForJSON($ship, $system, $strippedSystem);
```

`setSystemEnhancementOptions` is called from the same four sites as
`setEnhancementOptions` ([shipLoader.php:179](source/server/controller/shipLoader.php#L179),
[:342](source/server/controller/shipLoader.php#L342), [:399](source/server/controller/shipLoader.php#L399),
[Manager.php:957](source/server/controller/Manager.php#L957)) — cleanest as one extra line inside
`setEnhancementOptions` itself, so the two can never be called apart.

### 4.3 The shared eligibility filter

```php
private static function systemMayBeEnhanced($ship, $system): bool {
    if ($ship->factionAge > 2) return false;
    if ($ship instanceof FighterFlight) return false;
    if ($ship instanceof Mine) return false;
    if (!($system->maxhealth > 0)) return false;
    if ($system->isTargetable === false) return false;
    if (!empty($system->hideInShipWindow)) return false;
    if (($system instanceof Weapon) && isset($system->factionAge) && $system->factionAge >= 3) return false; //D6
    return true;
}
```

This deliberately mirrors the client's `isPseudoSystem`
([SystemInfoButtons.js:805](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L805)).
**Note the mirror on both sides** ([[project_dev_roadmap]] item 13).

### 4.4 `BaseShip` / `ShipClasses`

* `public $systemEnhancements = array();`
* `public $systemEnhancementOffers = array();`
* `public $pointCostSysEnh = 0;` (D5), sent in `stripForJson` beside `pointCostEnh`
  ([ShipClasses.php:731](source/server/model/ships/ShipClasses.php#L731)).
* `onConstructed`: `Enhancements::setSystemEnhancements($this);` immediately after
  `Enhancements::setEnhancements($this);` ([:1511](source/server/model/ships/ShipClasses.php#L1511)).

### 4.5 Buy path

`Manager::getShipsFromJSON` ([:1913](source/server/controller/Manager.php#L1913)) — beside
`enhancementOptions`:

```php
$ship->systemEnhancements = $value["systemEnhancements"] ?? array();
```

`BuyingGamePhase::process`, right after the existing enhancement loop
([:447-455](source/server/Phase/BuyingGamePhase.php#L447)):

```php
$clean = Enhancements::sanitiseSystemEnhancements($ship, $ship->systemEnhancements);
foreach ($clean['rows'] as $row) {
    $dbManager->submitSystemEnhancement($gameData->id, $id, $row[6], $row[0], $row[2], $row[1], $row[4]);
}
```

⚠️ **Resolve systems against `$ship`, not `$savedShip`** — the exact warning already written into
that method for pre-battle damage ([:463-467](source/server/Phase/BuyingGamePhase.php#L463)): a
bulk-bought mine's `$savedShip` is a clone whose `systems` array was rebuilt 0-indexed. Mines are
excluded anyway (D7), so this cannot bite today — but the OSAT bulk path is not, so write it
correctly the first time.

⚠️ `enhname` is interpolated unescaped in `submitEnhancement`
([DBManager.php:176](source/server/controller/DBManager.php#L176)) and reaches it via `DBEscape`.
The new writer must do the same, and its name must come from the **server-side label table**, never
from the client tuple's index 1.

### 4.6 Game load

`DBManager::getEnhancementsForShips` ([:2750](source/server/controller/DBManager.php#L2750)) gains
a second bulk fetch (`getSystemEnhancementsForGame($gameID)`, modelled on
[:3316](source/server/controller/DBManager.php#L3316) — **one query for the whole game**, not per
ship) and fills `$ship->systemEnhancements` with rebuilt tuples:

```php
$ship->systemEnhancements[] = array($enhid, $enhname, (int)$numbertaken, 0, (float)$enhvalue, 0, (int)$systemid);
```

Same early position in `getTacShips` as the existing call, for the same reason.

### 4.7 Saved fleets

* **Write** — `Manager::submitSavedFleet`, beside the enhancement loop
  ([:787](source/server/controller/Manager.php#L787)). Re-sanitise first (D4). Store the system
  **name** alongside the id (D13).
* **Read** — `Manager::loadSavedFleet` ([:957](source/server/controller/Manager.php#L957)), after
  `setEnhancementOptions`: fetch rows, **re-validate every `systemid` against the ship just built**,
  **re-price**, and return `systemEnhancements` + the recomputed `pointCostSysEnh` in the response.
* If anything was dropped or re-priced, return a `systemEnhancementNotice` string so the lobby can
  say so once — silent point changes on a loaded fleet are the kind of thing that gets noticed
  three battles later.

#### 4.7.1 How a saved fleet goes stale (O5)

The answer to "how could this happen" is: **routinely, and mostly not through anything the player
did.** `tac_saved_list` rows have no expiry and are meant to persist across campaigns; the
blueprint they were priced against is PHP source that changes under them.

| Trigger | Effect on a stored row |
|---|---|
| A contributor edits a ship class — the routine case, since contributors' whole remit is adding and revising ships and weapons ([[project_fv_contributors]]) | Any of the inputs below moves |
| `$guns` changed on a weapon | `SYS_ADT` and `SYS_GSGT` prices move |
| `maxDamage` / `maxDamageArray` rebalanced | `SYS_GSGT` price moves |
| A shield's `output` or arc changed | `SYS_HSHLD` price moves |
| A system's `armour` or `maxhealth` changed | `SYS_HARM` price moves |
| A thruster added, removed or re-rated | `SYS_THR` price **and limit** move for every thruster in that direction |
| The base `intercept` raised to 4 | `SYS_ADT` limit becomes 0 — the refit is no longer purchasable at all |
| **A system INSERTED mid-constructor** | ⚠️ **the dangerous one** — ids are pure construction order, so every id after the insertion point shifts by one and the stored `systemid` now names a *different system* ([[arch_positional_system_id_trap]]) |
| A system removed from a hull | The stored id resolves to nothing, or to a shifted neighbour |
| `Enhancements::addEnhancementSystems` mounts `SHAD_TEND` tendrils | Appends only, so nothing already on the hull moves — safe, but the reason it is safe is worth the comment |

Only the last row of that table is a *correctness* hazard rather than a pricing one, and D13 is
what defuses it: a stored `(systemid, sysname)` pair that resolves to a system of a different name
is **dropped**, not applied. Without the name, a Twin Array's Gunsights would be silently applied
to whatever now sits at id 14.

**Resolution order on load, per row:**

1. `getSystemById(systemid)` — no system ⇒ **drop**.
2. Name mismatch ⇒ **drop** (D13).
3. `systemMayBeEnhanced` + registry `eligible` ⇒ no longer eligible ⇒ **drop**.
4. Count above the current `limit` ⇒ **clamp**.
5. Re-price through `systemEnhancementPrice` ⇒ **use the new price**, whatever the stored one said.
6. Recompute `pointCostSysEnh` as the sum, and report every drop / clamp / re-price in
   `systemEnhancementNotice`.

Dropping is always the safe direction: the player loses points they get straight back and can
re-buy the refit in two clicks. Guessing is not recoverable, because nobody notices.

---

## 5. Client

### 5.1 New legacy file `source/public/client/systemEnhancements.js` → `window.systemEnhancements`

Add its `<script>` to the debug lists in **both** `gamelobby.php` and `game.php` so
`scripts/bundle-legacy.js` picks it up (the `battleDamage.js` precedent).

```
systemEnhancements = {
  LABELS                          // enhID -> human name; mirrors the PHP label table
  offersFor(ship, system)         // [] of offer tuples for this system
  taken(ship, systemid, enhID)    // current count
  set(ship, systemid, enhID, count)   // writes ship.systemEnhancements, re-prices, no apply
  totalCost(ship)                 // -> ship.pointCostSysEnh
  count(ship)                     // how many purchases on this ship (drives the summary line)
  hasAny(ship, systemid)          // drives the gold star
  clear(ship)                     // phpclass change
  clone(payload)                  // DEEP - Copy must never share the array
  apply(ship)                     // mirror of Enhancements::setSystemEnhancements
  revert(ship)                    // undo, for the edit/rebuild path
  dropDestroyed(ship)             // D11 - remove + refund refits on destroyed systems; returns
                                  //       the removed rows so the caller can say so once
}
```

`apply` is the JS mirror of the PHP applier. **This is a mirror pair — say so in a comment on
both** ([[project_dev_roadmap]] item 13). It follows `lobbyEnhancements`' established shape but
must NOT go inside it: `lobbyEnhancements.apply` is guarded by a once-per-build
`ship.enhancementsApplied` flag ([lobbyEnhancements.js:23](source/public/client/lobbyEnhancements.js#L23))
and system enhancements are bought *after* that has already fired.

> ### ⚠️ Re-application is the hazard here, not application
> Every effect is a cumulative `+=`. The player will buy, un-buy and re-buy from an open menu
> repeatedly. `apply` must therefore be **idempotent from the blueprint**, not incremental: keep
> the original value per (systemid, field) on first touch and always write
> `original + totalLevels`, never `current + 1`. `lobbyEnhancements` gets away with `+=` only
> because it runs once per build.

> ### ⚠️ Shared system references
> Client system objects share field references across same-phpclass instances
> ([[arch_client_system_shared_reference]]) — and lobby ships are `jQuery.extend` clones, so
> **every `instanceof` fails** ([[arch_lobby_ship_objects]]). Duck-type on `system.name ===
> 'thruster'`, `system.weapon`, `system.name === 'eMShield' || system.name === 'graviticShield'`.
> Stage 2's exit test is **two identical hulls in one fleet**: enhancing one must leave the other
> untouched.

### 5.2 Pricing integration (D5)

* `gamedata.rowPointCost` ([gamelobby.js:363](source/public/client/gamelobby.js#L363)) is already
  `ship.pointCost * bulkCount` and needs **no change** — provided `pointCost` carries the new
  bucket.
* `readBulkPurchase` ([:2782](source/public/client/gamelobby.js#L2782)) and the two `doEdit*` /
  `doBuy*` equivalents: `pointCost = baseCost + pointCostEnh + pointCostEnh2 + pointCostSysEnh`.
* `getPristinePointCost` must peel all three.
* `copyBulk` / `copyShip` must `systemEnhancements.clone()` — sharing the array would make editing
  one row edit the other, exactly the bug `cloneEnhancementOptions` was written to fix
  ([:2728](source/public/client/gamelobby.js#L2728)).
* Every write from the menu runs an affordability check through `gamedata.fleetCost(ship, ship.id)`
  and **refuses, restoring the previous count**, rather than letting the fleet go over budget.
  Then `gamedata.calculateFleet()` + the fleet-list row refresh.
* **Edit invalidation:** editing a ship rebuilds it from the blueprint. Carry
  `systemEnhancements` across when the phpclass is unchanged, **clear and refund** otherwise —
  mirroring the pre-battle-damage rule (PREBATTLE_DAMAGE_PLAN.md §5.3).

### 5.2.1 Destroyed systems (D11)

The two editors share one menu, so a player can destroy a system they refitted thirty seconds ago.

* `ApplyDamageMenu.refresh()` — which every damage and critical write already funnels through
  ([ApplyDamageMenu.js:263](source/public/client/UI/reactJs/system/ApplyDamageMenu.js#L263)) — calls
  `systemEnhancements.dropDestroyed(ship)` **after** `battleDamage.applyToShip(ship)` and before
  the repaint. That ordering is not incidental: the refit sweep must read the `system.destroyed`
  the preview just wrote, so it catches the **structure cascade** (destroying a Structure block
  destroys every system in its location — `applyToShip` mirrors `ShipSystem::isDestroyed`) and not
  merely the box the player clicked.
* Anything removed is refunded into `pointCostSysEnh`, `gamedata.calculateFleet()` re-runs, and a
  single `confirm.warning` names what went — *"Twin Array #14 was destroyed; its Gunsights refit
  (12 pts) has been removed."* A refund the player does not see reads as a pricing bug.
* **`MineDamageMenu` is not a route into this** — mines are excluded (D7) — but
  `FighterDamageMenu` and any future editor must call the same sweep. Put the call in the shared
  refresh path, not in each menu.
* **One-way, by decision (D11).** Un-ticking Destroy does not restore the refit. State it in the
  warning text (*"…has been removed"*, not *"suspended"*) so the behaviour is not a surprise.
* Server side: `sanitiseSystemEnhancements` runs at buy time **after** `PreBattleDamage::sanitise`
  has resolved what is destroyed, and drops any refit on a system the payload marks `k`, or in a
  destroyed structure block. The client sweep is a courtesy; this is the guarantee.

### 5.3 Wire

`construcGamedata` ([ajaxInterface.js:1049](source/public/client/ajaxInterface.js#L1049)) and
`constructSavedShips` ([:644](source/public/client/ajaxInterface.js#L644)), beside the
`enhancementOptions` line:

```js
if (window.systemEnhancements && systemEnhancements.count(ship) > 0) {
    newShip.systemEnhancements = ship.systemEnhancements;
}
```

`systemEnhancementOffers` is **never sent** — it is blueprint data, regenerated server-side.

---

## 6. UI

### 6.1 `ApplyDamageMenu` → "Add Enhancements & Damage"

```
┌ Add Enhancements & Damage ───────────────┐
├ ✦ ENHANCEMENTS ──────────────────────────┤  ← gold bar: #e8cf93 on rgba(169,128,56,.30),
│ Gunsights            [−] [ 1 ] [+]   12p │    1px #8a6d3b top+bottom, body text #d8be86
│ Hardened Armour      [−] [ 0 ] [+]   18p │
│                          Refits: 30 pts  │
╞══════════════════════════════════════════╡  ← 2px divider, the hard visual break
├ Damage ──────────────────────────────────┤
│ Twin Array #14  [−] [  8 ] [+] ☐ Destroy │
├ Critical Effects ────────────────────────┤
│ …                                        │
└──────────────────────────────────────────┘
```

* New component **`system/SystemEnhancementsSection.js`**, taking
  `{ ship, system, rows, onChange }` — its own file, in the shape `CriticalEffectsSection`
  established, so it can be dropped into a future flight/mine menu unchanged. Takes `rows`
  (`[{enhID, label, count, max, price}]`) rather than raw offers, so it is a *renderer* and the
  "what may this system have" question stays in one place.
* Renders nothing at all when `rows` is empty — the common case: most systems on most hulls, plus
  every destroyed system (D11).
* Ticker + wheel + keyboard from the existing `ActionButton` / `ValueInput` / `nonPassiveWheel`
  primitives already in `ApplyDamageMenu` — **do not fork them**; lift them into
  `system/menuControls.js` and import from both, since a gold variant is just a prop.
* ⭐ **All three section bars live in `menuControls.js` too** (refinement round 1) — one
  geometry, three tints, declared next to each other so they cannot drift. The three-way split
  is the point: the player needs to know at a glance which editor they are typing into, and the
  gold bar is the only one that costs points.
* ⚠️ **`preventDefault()` in React's `onWheel` is a no-op** — use the existing `nonPassiveWheel`
  ref helper ([[arch_react18_passive_wheel]]).
* ⚠️ The container's `max-width: 300px` is load-bearing
  ([ApplyDamageMenu.js:30](source/public/client/UI/reactJs/system/ApplyDamageMenu.js#L30)). The
  longest label ("Advanced Defensive Targeting") must be `text-overflow: ellipsis` with a `title`,
  or the menu stretches to the 500px ceiling exactly as the crit picker once did.
* Gating: a new `canApplySystemEnhancements(ship, system)` beside `canApplyPreBattleDamage`
  ([SystemInfoButtons.js:791](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L791)),
  = the same predicate **plus** `factionAge <= 2` **plus** at least one offer **plus
  `!shipManager.systems.isDestroyed(ship, system)`** (D11 — no refits on a wreck). The menu opens
  if **either** predicate passes; both `canDoAnything` and `hasStyledMenu` need the `||`
  ([:819](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L819),
  [:923](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L923)).
* ⚠️ The destroyed test makes the two predicates **deliberately asymmetric**:
  `canApplyPreBattleDamage` must keep passing for a destroyed system (you have to be able to
  un-destroy what you just destroyed —
  [SystemIcon.js:431-437](source/public/client/UI/reactJs/system/SystemIcon.js#L431)) while
  `canApplySystemEnhancements` must not. Do not "tidy" them into one predicate.
* ⚠️ `fleetIsCommitted()` already closes these menus after Ready
  ([:788](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L788)). Inherit it — a refit
  bought after Ready is never submitted but *is* charged locally.

### 6.2 The gold star

A gold ✦ absolutely positioned `top:0; left:1px` inside `System`
([SystemIcon.js:43](source/public/client/UI/reactJs/system/SystemIcon.js#L43)), `z-index:1`,
`pointer-events:none`, `text-shadow: black 0 0 3px`. **11px** — it shipped at 7px, which was
legible only if you already knew to look for it (refinement round 1).

⚠️ It is driven by `ship.systemEnhancements`, so the array has to **reach the browser** — in the
lobby it is local to that player, but in game it only arrives because `stripForJson` sends it, own
team only. That was missed first time round and the badge was lobby-only.

⚠️ `SystemIcon` has **two return paths** — the destroyed short-circuit at
[:435-441](source/public/client/UI/reactJs/system/SystemIcon.js#L435) and the interactive one. A
destroyed-but-enhanced system should still show it (you paid for it). Put the star in both, or
better, extract a `renderBadges(system)` helper called from both.

#### 6.2.1 ⭐ `ShipSystem::$enhancementMarks` — one marker channel, not two

*(From the user's `public $officer` suggestion, 2026-08-12 — the part of it that is right, and
generalised.)*

```php
public $enhancementMarks = array();   //short labels this system carries because of an enhancement
```

Pushed by **any** enhancement that touches a system, ship-level or system-level. `stripForJson`
sends it **only when non-empty** (so it costs one `empty()` per system on the hot path, and zero
bytes on the overwhelming majority).

It then answers three questions that would otherwise each need their own mechanism:

| Question | Answer |
|---|---|
| Does this icon get a gold star? | `enhancementMarks.length > 0` — no per-enhancement knowledge in `SystemIcon` at all |
| What does `SystemInfo` list under the system? | The marks, verbatim |
| Where do officers show up? | `$engine->enhancementMarks[] = 'Expert Engineer'` — §11, and that one line is the whole display half of the feature |

The win is that the **existing** ship-level enhancements can adopt it incrementally and for free:
`IMPR_ENG` today silently adds +1 to the strongest Engine and the player has no way to see *which*
Engine got it. One `enhancementMarks[]` push in that `case` fixes a long-standing papercut, and
does so through the same channel this feature already needs. **Not required for v1** — build the
channel, use it for the five refits, and let the ship-level cases adopt it when convenient.

⚠️ `$system->data[...]` is the *other* per-system display channel and is **not** the right home:
it is rebuilt per turn by `setSystemDataWindow` and is keyed prose for the info window, whereas
marks are a stable list the icon layer reads. Keep them separate.

### 6.3 ⚠️ What the star does and does not hide (D8)

The badge is own-team-only. **The enhanced stat values are not hidden**, and mostly cannot be:

| Field | Reaches the enemy? | Why |
|---|---|---|
| `fireControl` | Could be hidden | Hit chance is computed by the **shooter**; an enemy never needs your weapon's FC |
| shield `output` | **Must reach them** | The enemy's own hit-chance and damage preview reads your shields |
| `armour` | **Must reach them** | Same — damage preview |
| thruster `output` | **Must reach them** | Movement/thrust display |
| `intercept` | Could be hidden | Interception is resolved server-side |

So the honest framing is: *the badge is a convenience marker for your own fleet, not an
information-hiding mechanism.* If real masking is wanted for FC and intercept, that is a separate
piece of work through the per-viewer machinery ([[arch_info_bleed_masking]]) — **Open question
O3**. Do not imply otherwise in the tooltip text.

### 6.4 The summary lines (request item 5)

* **Ship-window gold box** — `lobbyEnhancements.apply` appends **one** line to
  `enhancementTooltip`: `"System Enhancements (n)"`, where `n = systemEnhancements.count(ship)`.
  Placed last, after the ship-level lines. Twelve separate lines would push the `enh` grid panel
  past the section cluster it is meant to sit beside
  ([ShipWindow.js:1788-1794](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L1788)).
* **Fleet-list card** — one extra `.ship-enhancement-entry` in `updateFleet`
  ([gamelobby.js:532-552](source/public/client/gamelobby.js#L532)) **and** in the
  `constructFleetList` rebuild. ⚠️ Those are two separate builders and the pre-battle-damage badge
  was written into only one of them and vanished on the next rebuild
  (PREBATTLE_DAMAGE_PLAN.md §6). Use one shared helper, as `damagedShipBadge` now does.
* **Detail on demand:** the per-system detail lives in the system's own `SystemInfo` tooltip, one
  line per purchased refit — the natural place, and it costs nothing to render.
* In **game.php**, the ship's `enhancementTooltip` is built server-side
  ([Enhancements.php:2226 region](source/server/model/ships/Enhancements.php#L2226)) — add the
  same one-line summary there so lobby and game agree, and note the mirror.
* ⚠️ **The summary line is OWN-TEAM ONLY** (refinement round 1). It is written at construction
  time, when there is no viewer to ask, and taken back off in `BaseShip::stripForJson` via
  `Enhancements::stripSystemEnhancementSummary`. The string therefore exists once, as
  `Enhancements::SYS_ENH_SUMMARY_PREFIX` — a literal at each end would drift the first time the
  wording is nudged, and the only symptom would be an enemy quietly seeing it again.

---

## 7. Stages & exit criteria

| Stage | Content | Exit test |
|---|---|---|
| **0** | `db/systemEnhancements.sql` + `emptyDatabase.sql` fold-in; DBManager CRUD (bulk read, single write, game-delete); `BaseShip` fields | Tables create and FK-cascade off `tac_saved_ship`/`tac_saved_list`; a scratch PHP script round-trips rows **including `sysname`**. `php -l` on **`/usr/src/current/...`**, not `/usr/src/fieryvoid` ([[howto_docker_db_access]]) |
| **1** | The registry (D12) + `Enhancements`: eligibility, pricing, limits, offer generation, `$offerSystemEnhancements` gate | A scratch script prints the offer list for one hull per faction age; a `factionAge 3` hull offers **nothing**; an Ancient weapon on a young hull offers nothing (D6); `AbbaiShieldProjector` is **not** offered `SYS_HSHLD`; `InvulnerableThruster` is not offered `SYS_THR`. **A Twin Array prices at 32 / 48** (§2.1 worked example). Measure the static-JSON size delta (§3.2) |
| **2** | `Enhancements::setSystemEnhancements` (apply) + generic `addSystemEnhancementsOwnForJSON` (registry `serialise`) + `sanitiseSystemEnhancements` | Constructed ship shows the bumped values; **a shield's `tohitPenalty` and `damagePenalty` both move** (§2.3 ordering); a doctored payload naming a bogus enhID / a nonexistent systemid / **a systemid whose `sysname` disagrees** / a count above the limit / a price of 1 is **dropped or re-priced**, never applied. **Replay harness `check` — this stage changes serialised fields** ([[project_replay_harness]]) |
| **3** | `systemEnhancements.js` + pricing integration (D5) | Console-buy a refit in the lobby: fleet points move, Edit does **not** refund it, Copy does not share it, phpclass change clears it. **Two identical hulls: enhancing one leaves the other untouched.** **A multi-mode weapon with `SYS_GSGT`, switched modes and back, keeps its +1** (§2.2) |
| **4** | `SystemEnhancementsSection` + menu rename + gating + gold star + the D11 sweep | Gold section appears only on eligible, **undestroyed** systems; the two sections read as visually distinct; store (left) windows and all of game.php unchanged; menu does not open after Ready; the star shows on own ships only; a **destroyed** enhanced system still shows the star. **Refit a gun, then destroy it: the refit goes, the points come back, one warning names it. Destroy a Structure BLOCK: every refit in that location goes** (cascade, D11). Un-destroying brings back neither |
| **5** | Buy POST → `BuyingGamePhase` write; game load → apply | Ready a fleet with refits in a scratch game; `tac_sys_enhancements` holds the right rows; Deployment and turn 1 show the enhanced values; `tac_ship.enhvalue` includes the new bucket; **the same POST in phase 1 writes nothing**; a crafted POST refitting a system it also marks destroyed writes no refit row |
| **6** | Saved fleets: write, read, re-validate, re-price, notice | Save a refitted fleet, reload it: same refits, same points. Then, per §4.7.1: (a) **hand-edit a blueprint to remove a system** — orphan dropped with a notice; (b) **insert a system mid-constructor** so ids shift — the refit is **dropped on the name check**, not applied to the neighbour (D13, the one that matters); (c) **change a weapon's `guns`** — refit survives, re-priced, notice shown |
| **7** | Summary lines (§6.4) in all four places | Lobby gold box, fleet card, fleet-card rebuild, and game.php tooltip all say "System Enhancements (n)" with the same n |

`yarn build` / `fvbuild.ps1` is available to me since 2026-08-11; **legacy bundles are never
committed** ([[feedback_fv_workflow]]). React edits verify via bundle-and-evaluate, not an esbuild
parse — a parse does **not** catch a missing import ([[howto_verify_react_bundle]]).

---

## 8. Risk register

| Risk | Mitigation |
|---|---|
| **`changeFiringMode` wipes the Gunsights bonus on the first mode switch** (§2.2) — silent, mid-game, and only on multi-mode weapons | Bump `fireControlArray` as well as `fireControl`; Stage 3 exit test is a mode switch there and back |
| **`null` fire control becomes `1`**, giving a weapon a targeting class it does not have | `!== null` guards, never truthiness; a Piercing missile (`fireControl = [null,3,3]`) is the test case |
| **Sparse `shpenh<N>` indices truncate `readBulkPurchase`**, zeroing every enhancement after the gap | D2 — the arrays are never merged. Regression-test the ordinary buy dialog on a ship with 6+ enhancements |
| **Edit silently refunds refits** while leaving them applied | D5 — third bucket, never written by the dialog readers. Stage 3 exit test |
| **Re-application compounds** as the player buys/un-buys from an open menu | `apply` is idempotent from a remembered blueprint value, never `current + 1` (§5.1) |
| **Shared client system references** — enhancing one Omega enhances every Omega | Copy-on-first-write; two-identical-hulls exit test ([[arch_client_system_shared_reference]]) |
| **`instanceof` on lobby clones always fails** | Duck-type throughout ([[arch_lobby_ship_objects]]) |
| **Stale `systemid` after a blueprint change** mis-applies a refit to a neighbouring system — and §4.7.1 shows this is *routine*, not an edge case, because contributors revise hulls continually | D13: store and verify `sysname`; re-validate at every read boundary; drop, never guess (§3.4, §4.7.1, Stage 6(b) exit test) |
| **A refunded refit that the player never sees** reads as a pricing bug and will be reported as one | D11's sweep raises exactly one named `confirm.warning`, and the refund runs through `calculateFleet` so the panel moves in the same tick (§5.2.1) |
| **The D11 sweep misses the structure cascade** — refits survive on systems in a destroyed block | Sweep off `system.destroyed` *after* `battleDamage.applyToShip` has written the cascade, never off the payload's `k` flags. Stage 4 exit test destroys a whole block |
| **Speculative generality for features that turn out not to need it** — the earlier officer "seam" was three unused abstractions | §11.3. Nothing in v1 is now shaped by a feature that does not exist. When the next follow-up is proposed, ask whether the *existing* machinery already covers it before extending the new machinery |
| **Client-supplied prices** | D4 — server re-derives and overwrites at every write boundary |
| **`addSystemEnhancementsForJSON` runs per system per ship per load** and is about to get a second loop | Early `if (empty($ship->systemEnhancements)) return;` before anything else (D3). Profile a large game with `?perf` ([[arch_gamescreen_load_perf]]) |
| **Static JSON growth** on the lobby's per-faction payloads | Measure at Stage 1; drop the name from the wire if it is not comfortably under budget. Nothing here enumerates ship classes, so the LiteSpeed memory ceiling is not in play ([[reference_fv_live_litespeed]], [[arch_static_generator_streaming]]) |
| **Thruster output written by two enhancement paths** (`ELITE_CREW` and `SYS_THR`) into the same stripped field | Both are assignments, not accumulations — verified at [Enhancements.php:3086](source/server/model/ships/Enhancements.php#L3086). Test a ship with both |
| **DECIMAL returns as a PHP string** from mysqli | Cast on read ([[arch_fractional_enhancement_value]]) |
| **The badge implies hiding it does not deliver** | §6.3 states the limit explicitly; tooltip wording must not overclaim |
| **No automated coverage for enhancement effects** | The replay harness proves *inertness for existing games*, which is the real question for Stage 2 — it does not test the new rules. Manual Docker play-through is the gate ([[project_replay_harness]]) |

---

## 9. Open questions — RESOLVED 2026-08-12

| # | Question | Answer | Landed in |
|---|---|---|---|
| **O1** | Ancient weapons on young hulls | **Hull age is the primary gate**; checking the weapon's own `factionAge` as well does no harm, so keep it | D6 |
| **O2** | `SYS_ADT` "1 per gun" | **Price × `guns`.** The rating is a single per-weapon value (`-5%` per point). Twin Array: `intercept 2 → 3` at the rating-3 tier of 16, doubled for 2 guns = **32 pts** | §2.1 + worked example |
| **O3** | Real per-viewer masking | **Own-team badge is enough for v1.** Per-viewer masking recorded as a planned later revision | D8, §12 |
| **O4** | `SYS_THR` step | **`+2` per level** — the direction's total thrust rating rises as you buy | §2.5 |
| **O5** | Re-costing a loaded fleet | **Re-price on load.** ⚠️ This is not an edge case — see §4.7.1 for the nine ways it happens and the resolution order. It is also what motivated D13 | §4.7.1, D13 |
| **O6** | Interaction with pre-battle damage | **No refit options on a destroyed system**; a refit on a system that then gets destroyed is **removed and refunded**, one-way | D11, §5.2.1, §6.1 |

No open questions remain for v1. §11 and §12 are scoped follow-ups, not blockers.

---

## 10. Out of scope for v1

* Flights, mines, terrain (D7).
* Refits on **enemy** or **store blueprint** ships.
* Any change to the existing ship-level enhancement dialogs or their pricing.
* Refits bought or changed **in game** — lobby-only, exactly like pre-battle damage.
* Retro-fitting the existing `GUNSIGHT` (Repeater Gunsights) onto the new per-system track. It is
  ship-level, it works, and moving it buys nothing.
* **Officers — §11.** Deferred, and deliberately **not** designed for: they are an ordinary
  ship-level enhancement and need nothing from this plan.
* **True per-viewer masking of enhanced stats — §12**, deferred.
* **Refactoring the existing ship-level `switch` blocks onto the registry.** They work. The
  registry is scoped to the five new IDs (D12).

---

## 11. FOLLOW-UP — Officers (needs nothing from this plan)

**Officers are an ordinary ship-level enhancement. They require no part of §§3–6.** Recorded here
so the next person does not re-derive it — and because an earlier draft of this plan got it wrong
in an instructive way.

### 11.1 Why there is nothing to build

An officer type is always stationed in the same kind of system, and the player does not choose
where. That single fact removes every reason for per-system storage: **if the target is
deterministic, recompute it at apply time instead of storing it.** And the codebase has done
exactly that for years:

| Existing enhancement | What it does | Line |
|---|---|---|
| `IMPR_ENG` | finds the **strongest Engine**, `output += count` | [Enhancements.php:2443](source/server/model/ships/Enhancements.php#L2443) |
| `IMPR_SENS` | finds the **strongest Scanner**, `output += count` | [:2489](source/server/model/ships/Enhancements.php#L2489) |
| `VOR_CRIMS` | finds the **PowerCapacitor by name**, bumps output and capacity | [:2764](source/server/model/ships/Enhancements.php#L2764) |
| `ELITE_CREW` | strongest Scanner + strongest Engine + strongest Reactor + **every** Thruster | [:2226 onward](source/server/model/ships/Enhancements.php#L2226) |

"Expert Engineer: bought at ship level, sits in the Engine, grants +N" **is `IMPR_ENG` with a
different label, price and bonus set.** Nothing more.

### 11.2 The recipe

1. An `enhancementOptions` entry in `setEnhancementOptionsShip` — name, limit, price, and the
   `if (found) { … }` guard so a ship with no Engine is never offered an Engineer.
2. A `case` in `setEnhancementsShip` that locates the system the same way `IMPR_ENG` does and
   applies the bonuses.
3. A `case` in `addSystemEnhancementsForJSON` re-sending whatever fields changed.
4. The mirror `case` in `lobbyEnhancements.setEnhancementsShip` for the lobby preview.
5. `$system->enhancementMarks[] = 'Expert Engineer'` (§6.2.1) — the whole display half, one line,
   and the player finally sees *which* Engine got the officer.

Steps 1–4 are the standard four-switch dance every existing enhancement already does. Step 5 is
the only thing this plan contributes, and it is a channel, not architecture.

### 11.3 What was wrong with the earlier draft — worth keeping

The first version of this section built a `funding: 'system' | 'ship'` discriminator into the
registry, an `unassigned()` state primitive, and a two-group section component, on the strength of
"officers are bought at ship level and assigned to systems". **Every one of those was speculative
generality.** The phrase "assigned to a specific system" was read as *the player assigns*, and an
assignment UI implies a stored assignment, which implies a per-system row, which implies a
discriminator to tell the two funding models apart. None of it followed once "assignment" turned
out to mean "always the same system, by type".

Two things to take from it:

* **A discriminator with one live value is worse than no discriminator** — it advertises a tested
  second branch that does not exist.
* The correct question was never "how do officers fit the new machinery" but **"do officers need
  new machinery at all"**. They do not. `IMPR_ENG` was sitting in the same file the whole time.

---

## 12. FOLLOW-UP — per-viewer masking (O3)

Deferred; recorded so the decision is not re-litigated from scratch. Per §6.3, the split is fixed
by what each client needs to compute:

* **Maskable** — `fireControl`, `intercept`. Hit chance is computed by the shooter and
  interception is resolved server-side, so an enemy never needs either.
* **Not maskable** — shield `output`, `armour`, thruster `output`. The enemy's own damage and
  hit-chance previews read all three off the payload.

The work is therefore in `addSystemEnhancementsOwnForJSON`: consult the viewer before copying a
maskable field, the way the existing per-viewer rules do ([[arch_info_bleed_masking]]). ⚠️ The
registry's `serialise` list is currently unconditional — masking means splitting it into
`serialise` and `serialiseOwnerOnly`, which is a **one-line-per-entry** change *because* the
registry exists. Doing it any other way means auditing five bespoke branches instead.

⚠️ `$enhancementMarks` (§6.2.1) would need the same treatment — it names the enhancement in plain
English, so sending it to an enemy defeats the badge entirely. Mask the marks, not just the stats.

Note the honest ceiling: a masked weapon reveals its enhanced fire control the first time it fires
and its to-hit is logged. The masking buys pre-battle uncertainty, not secrecy.
