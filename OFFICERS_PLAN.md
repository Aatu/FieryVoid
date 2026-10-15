# Officers — Implementation Plan

Status: **Stages 0–2a committed (`7800ab827`), Stage 3 committed (`1714c3651`), Stage 4 committed
(`ab0025aba`, `4738173cc`; the Motivator re-priced to 50 % in `32c9f2a11`); Stage 5 committed (`85c30272c`,
§9.8); fighter officer prices scaled by flight size + saved fleets re-priced committed (`75dc7d68d`, §9.9 - ⚠️
db/savedOfficerPrice.sql before deploying); the Technician rebuilt from his full rules and the Scavenger's save
widened (§9.10), and Stage 6, the Expert Navigator (§9.11), committed (`6dd2f740e`). Faction rules for officers (§9.12)
and Stage 7, the Expert Jump Officer (§9.13), BUILT 2026-10-15, uncommitted.** Written against `dab951bf5`. The user
ruled D1–D12 and QX1–QX5 on 2026-10-09; three rulings changed the draft (D3 kill roll, D9 eligibility and
one officer per flight, D10 filter chips → new D13), logged at the top of §1. On 2026-10-10 the user added
a third Helmsman effect (−1 on the meteor impact chart) and play-tested Stage 1 in game 4457 (confirmed
correct), then ruled the Scanner, Technician and Geneticist questions for Stage 3 (§6). On 2026-10-11 they
ruled the fighter-officer questions (§6), made the old fighter enhancement **Expert Motivator** an officer,
and asked for the ship window's officers as one **"Officers (n)"** row in the gold Enhancements block
(§9.6). The same day they ruled out officers on remotely controlled craft (the Hunter-Killers) and had a
fighter officer **follow his fighter** onto a split-off flight row, cost included (§9.7). On 2026-10-15 they
supplied the Engineer's and Scavenger's rules text and ruled their questions (§6), including an **automatic**
once-per-game save for the Scavenger (§9.8). Later that day they widened the save to Scanners and Engines,
supplied the Technician's full rules (all three skills at once, §9.10) and the Navigator's (Stage 6, §9.11), and
ruled the three questions those raised - then put the Hyach Maneuvering Specialist's turn cost on the Navigator's
ladder, applied after him (§9.11 follow-up). Later the same day they asked for faction availability and faction
prices as one easily edited table, with a README how-to for custom factions - Geneticist Dilgar only, Matter Weapons
Expert Grome only, the Scavenger at 15% for the Descari and 20% for Balosians, Raiders and Llort (§9.12) - and
supplied the Jump Officer's rules text (Stage 7, §9.13). Each officer's own rules questions still only need ruling
when that officer's stage starts. Next step: Stage 8 (placed officers: Gunner core, weapon-mounted Matter Weapons
Expert).

> ### Relationship to WEAPON_ENHANCEMENTS_PLAN.md §11
> §11 concluded that officers "are an ordinary ship-level enhancement … and need nothing from this
> plan", because every officer was assumed to sit in a fixed system by type. Two facts in the new
> brief change that: officers can be **killed** by damage to their system, and two of them (Expert
> Gunner, and the Matter Weapons Expert when mounted on a weapon) are **placed by the player** on a
> system of their choice. The rest of §11 still holds. The fixed-post officers really are
> `IMPR_ENG`-shaped and are built that way here. §11.3's warning about speculative machinery is why
> this plan adds no table, no column and no stored "assignment" state.

---

## 0. Summary

**What an officer is.** An enhancement with a **post**: the one system on the unit that he lives in.
While the post stands, his bonus applies. If the post is destroyed he is **disabled** for the rest of
the battle, even if Self Repair later revives the system. Every time the post takes damage, its normal
critical roll also decides whether he is **killed**: 20 or more, modifiers included (D3). His own team
sees a gold rank insignia on the post and a line in its System Info tooltip. Nobody else sees anything
until the game is over (or, in a later stage, an Expert Analyst looks).

**How it is built.**

- Officers with a fixed or random post, and every fighter officer, are ordinary **ship-level
  enhancement rows** with an `OFF_` id: `enhancementOptions`, `tac_enhancements`, `tac_saved_enh`,
  and the buy dialog's **Officers** section, which is already reserved and stays hidden while empty
  ([confirm.js:837](source/public/client/UI/confirm.js#L837)). Their post is never stored. One
  resolver picks it from the blueprint at every load, the way `IMPR_ENG` picks its Engine.
- The two officers the player places are entries in the existing **per-system** registry
  ([Enhancements.php:3645](source/server/model/ships/Enhancements.php#L3645)), so they get
  `tac_sys_enhancements`, the D13 name check, saved-fleet re-validation and the destroyed-system
  refund for free.
- Both tracks feed one runtime list, `$ship->officers`, holding *(officer, post, out-of-action turn,
  disabled or killed)*. Every effect, insignia, tooltip line and visibility rule reads that list and
  nothing else.
- **Disabled** needs no storage. The server loads every damage row of every turn
  ([DBManager.php:3391](source/server/controller/DBManager.php#L3391)), so "the post was destroyed
  on turn N" is re-derived at each load. **Killed** is a dice result, so it is stored: one
  `OffKilled` individual note per death, taken out of the note stream at load so that no system class
  ever sees it (§4.4).
- **No schema change anywhere.**

**Ranking at a glance** (reasons in §3):

| Easy | Medium | Hard |
|---|---|---|
| Expert Helmsman | Expert Engineer | Expert Quartermaster |
| Expert Scanner | Expert Scavenger | Breaching Expert |
| Expert Technician ¹ | Expert Navigator | Expert Evangelist |
| Matter Weapons Expert (C&C) | Expert Jump Officer | Expert Laser Technician |
| Expert Geneticist | Expert Analyst | Expert Software Engineer |
| Expert Dogfighter ² | Expert Coordinator ² | Expert Ion Technician |
| Expert Missileer ² | Expert Gunner, core ³ | Expert Surge Officer |
| Expert Evader ² ⁴ | Matter Weapons Expert (weapon) ³ | Expert Ballistic Officer |
| | Loading time −1, shared ⁵ | Expert Anticipator |
| | | Expert Gunner, once-per-battle abilities |
| | | Expert Electrician ² |

¹ On the "best of the three, automatically" reading (§6, Technician), as Stage 3 built him. His full rules
(§9.10) make all three cumulative, which adds a client-only term in power.js: Easy-Medium. ² After the fighter-officer framework
(Stage 4). ³ After the placed-officer framework (Stage 8). ⁴ If always on; Medium as a per-turn
toggle. ⁵ Medium-Hard; one build serves both Gunner and the weapon-mounted Matter Weapons Expert.

**Recommended order:** the framework, with Expert Helmsman as its test vehicle (Stages 1–2); the rest
of the Easy tier (3); fighters (4); then the Medium tier one officer per stage. Each Hard officer gets
its own short plan and ruling round when it is reached. Most of them have a cheap half that could
ship earlier (§3.3, right-hand column). The buy dialog's **filter chips** (D13) depend on nothing
here and can be built first, on their own (Stage 2a).

---

## 1. Decisions

> **Rulings, 2026-10-09.** D1, D4–D8, D11 and D12 accepted as drafted; D2 and QX5 accepted without
> comment. Changed or added:
> - **D3**: the rulebook's own text. A destroyed post **disables** the officer. Any damage to the post
>   triggers its normal critical roll, and 20 or more (modifiers included) **kills** him. The roll is
>   made even when the system is destroyed.
> - **D9 / QX3 / QX4**: no officers on Ancient hulls, OSATs (mines included) or terrain. Bases only
>   where the post exists. Ships: one of each type, "except as noted". Flights: one officer in total.
> - **D10**: plus filter chips in the buy dialogs → new **D13**.
> - **QX1**: the Evangelist's −2 is tabletop, so −10 in FV. The others are FV units as drafted.
> - **QX2**: yes, a critical roll of 20+ kills (D3).
>
> D11 and D12 were accepted before D3 changed; their rows below now show D3's two states.

| # | Decision | Why |
|---|---|---|
| **D1** | **Two purchase tracks, one runtime list.** Fixed-post and random-post officers, and all fighter officers, are ship-level rows. Officers whose post the player picks are per-system registry rows. `Officers::resolve()` merges both into `$ship->officers`. | 22 of the 25 officers have a post the rules set, by system type or by random draw. Putting them on the per-system track would mean buying a Helmsman by finding the C&C in the ship window, while the reserved Officers section stays empty. Putting the 2 placed officers on ship-level rows would mean a weapon dropdown per Gunner slot (three choice-valued rows on a capital, the `CHAM_DISG` widget shape) and re-inventing the D13 name check. *Alternative, not recommended:* everything per-system. One track, but worse purchase UX and a per-weapon offer tuple for every officer in every static blueprint. Breaching Expert is the 25th and fits neither track (§3.3). |
| **D2** | **Track-1 posts are resolved, never stored.** They are picked from **blueprint** values at the top of `BaseShip::onConstructed`, before any enhancement moves an output (§4.3). A post destroyed by pre-battle damage leaves its officer disabled from the start (§4.9). | `ADV_ENG` set the precedent ([Enhancements.php:2516](source/server/model/ships/Enhancements.php#L2516)). Poor Crew takes thrust off the strongest Engine, so on a two-engine hull "strongest" depends on row order unless it is asked before any row runs. The lobby mirror picks the same way, so lobby and game agree. |
| **D3** | **RULED: two ways out of action.** The rulebook: *"Officers are considered disabled (and unusable for the rest of the scenario) if the system they are in is destroyed, but there is also a chance they will be killed by any damage to the system. Use the normal critical hit roll to determine this (make a roll even if the system is totally destroyed). If this roll results in a '20' or greater (including any modifications), the officer is killed. This is in addition to any other critical hit effects."*<br>**Disabled** = the post is destroyed, by its own boxes or by its Structure block. **Killed** = the post took damage through its armour this turn, and its critical roll came to 20 or more. That is the roll the post makes anyway, or one made for him when the post was just destroyed (§4.4). The weapon-mounted Matter Weapons Expert is killed by **any** damage through armour, as his own text says. Both states are **permanent** (Self Repair reviving the post changes nothing) and both take effect **next turn**. | Next-turn is the convention every support system follows, `isDestroyed($turn - 1)` ([baseSystems.php:20494](source/server/model/systems/baseSystems.php#L20494)), and it keeps simultaneous fire simultaneous: the Evangelist's +1 cannot vanish halfway through the shots of the turn he falls. FV's critical roll is d20 + the system's **total** damage + both crit modifiers ([ShipSystem.php:1421](source/server/model/systems/ShipSystem.php#L1421)), so on a battered post any further hit is close to certain death. *Disabled* can be derived from the complete damage history. *Killed* is a dice result, so it has to be stored (§4.4). The two states differ for the Evangelist, whose penalty is worded "if killed" (§6). |
| **D4** | **Own team only; the game's end discloses everything; no public log line.** *(Amended 2026-10-10: an officer's effect on the published `iniativebonus` is shown to every viewer - §4.6.)* The insignia, the tooltip and the `officers` payload reach the owner and teammates (`isRevealedToCurrentViewer`), and everybody once `TacGamedata::$currentGameFinished`. Officer names never enter the public `enhancementTooltip`. | Same rule and same mechanism as the ✦ ([ShipClasses.php:985](source/server/model/ships/ShipClasses.php#L985)). ⚠️ **This is cosmetic, exactly like the ✦** (WEAPON_ENHANCEMENTS_PLAN.md D8 / §6.3). A few officers change numbers that reach the enemy anyway. §4.6 lists them, so the tooltip never claims a secrecy the feature does not deliver. |
| **D5** | **Effects run in ONE post-pass**, `Officers::apply()`, after enhancements and after the per-system `onConstructed` loop, and before `iniativeadded` is filled ([ShipClasses.php:1779-1789](source/server/model/ships/ShipClasses.php#L1779)). Never as a `case` in `setEnhancementsShip`. | (a) That switch runs rows in `enhid` order, and an order-sensitive effect belongs in a fixed post-pass on both ends ([[arch_enhancement_storage_and_order]]). (b) `isDestroyed()` needs `structureSystem`, which `ShipSystem::onConstructed` fills ([ShipSystem.php:473](source/server/model/systems/ShipSystem.php#L473)). (c) None of the 25 has to precede `Shield::onConstructed`, which is the reason system refits run earlier. |
| **D6** | **A new class, `Officers`** (`source/server/model/ships/Officers.php`), holds the registry and the resolver. `Enhancements.php` gains hook calls only. | `Enhancements.php` is 4,789 lines. WEAPON plan D12's argument applies: one literal per officer, so nothing gets added in three places out of four. |
| **D7** | **ids are `OFF_*`, at most 10 characters** (table in §4.2). | `enhid` is `varchar(10)` in both tables. The prefix routes a row to the Officers section, and keeps it out of the public tooltip, with one test each. It also avoids the existing fighter `NAVIGATOR`. |
| **D8** | **Price = ceil(pct × blueprint `pointCost`)**, the base `ELITE_CREW` uses ([Enhancements.php:244](source/server/model/ships/Enhancements.php#L244)). **Fighter officers are priced per FLIGHT**, not per craft (*re-ruled 2026-10-15, §9.9: every fighter officer is pct of one craft for a flight of six, scaled by the flight's size*). **Fleet-% officers** (Quartermaster, Evangelist) are priced off the **game's points limit**, not the live fleet total. | The lobby multiplies every fighter enhancement by flight size ([confirm.js:215](source/public/client/UI/confirm.js#L215), [gamelobby.js:4203](source/public/client/gamelobby.js#L4203) and five more sites). "1 per flight, 50 % of one fighter" is not that shape, so it needs a per-row exception (§4.10). Evader's "× number of fighters" is the one fighter officer that already fits. A price on the live fleet total is circular (the officer is part of the fleet) and changes with every purchase. The game's limit is fixed and known in the lobby. |
| **D9** | **RULED.** **Ships: one officer of each type**, several different types per ship, "except as noted" (Gunner up to 3 / 2 / 1 by size; Breaching Expert up to 20 % of pods). **Flights: one officer in total.** **Never on Ancient hulls (factionAge ≥ 3), OSATs (mines extend OSAT) or terrain.** Bases, LCVs and everything else only where the post exists, so a base without a jump drive is never offered a Jump Officer. Faction gates use the faction string. | User ruling (QX3, QX4) and the rulebook: a unit "may not benefit from more than one expert officer of the same type (except as noted)". The flight cap makes a flight's officer rows mutually exclusive in the buy dialog (§4.10). ⚠️ FV_factions.txt:438 still promises Shadow ships "enhancements relating to special officers"; that line now contradicts the ruling. Corillani CPN and OSF exist only in `$notes` prose ([VostovaAMCPN.php:15](source/server/model/ships/corillani/VostovaAMCPN.php#L15)), so the Evangelist needs a per-hull opt-in (`enhancementOptionsEnabled[] = 'OFF_EVAN'`), which is a contributor-area edit. |
| **D10** | **Where they are bought:** track-1 officers in the buy dialog's Officers section; placed officers in the system's own *Add Enhancements & Damage* menu, under an **Officers** sub-heading of the gold section. **Plus filter chips in the buy dialogs (D13).** | One `buySectionOf` change lights the reserved section ([confirm.js:852](source/public/client/UI/confirm.js#L852)). A placed officer is bought where his post is. |
| **D11** | **The insignia** is a small gold double chevron (inline SVG, `theme.colors.enhTitle`) in a top-left **badge row** shared with the ✦. One insignia per post, however many officers are posted there. **Disabled** = grey; **killed** = grey with a red slash. Both stay shown. | Top-right belongs to the meteor badge ([SystemIcon.js:69](source/public/client/UI/reactJs/system/SystemIcon.js#L69)). A shared row keeps ✦ and insignia from overlapping on a gun with both Gunsights and a Gunner. Showing a fallen officer is what tells the player why a bonus stopped. |
| **D12** | **The tooltip** gets a gold **Officers** block in `SystemInfo`, after *Special* and before *Criticals*: one line per officer with his name and a one-line effect, plus "(disabled, turn N)" or "(killed, turn N)" once he is out of action. The text comes from a client mirror of the registry labels. | The lobby has no server round trip, so the text must exist client-side anyway. `system.data` is the wrong channel: it is rebuilt per turn, replaced wholesale by a server `data`, and shared by reference across same-class systems ([[arch_system_info_tooltip_data_flow]]). |
| **D13** | **Filter chips in the buy dialogs** (user request with D10). A row of chips above the sections: **All**, **Ordnance**, **Enhancements**, **Options**, **Officers**, with a chip only for a section that has rows, and no chip row at all when fewer than two sections do. Picking a chip shows only that section; **All** shows them all. The picked chip wears its section's own colours: the `--buy-sec-*` tokens its head already uses (gunmetal, bronze, verdigris, and the default blue that Officers keeps). Built in `confirm.js` and `confirm.css` only, so it does not depend on officers. | A chip **hides** sections and never removes a row. That is the promise the text filter already makes ([confirm.js:811-813](source/public/client/UI/confirm.js#L811)): a hidden row is still bought, counted and read back by gamelobby.js. Reusing the section tokens keeps one colour definition per section ([confirm.css:1885-1914](source/public/styles/confirm.css#L1885)). The text filter box is currently commented out of the shell ([confirm.js:965-967](source/public/client/UI/confirm.js#L965)), so the chips take its place above the sections; if the box ever returns, the two combine. |

---

## 2. What already exists

Every officer below reuses at least one of these. Line numbers verified against `dab951bf5` plus the
current working tree.

| Need | Existing machinery | Where |
|---|---|---|
| A buy-dialog section | `confirm.BUY_SECTIONS` already holds `officers`; a section with no rows stays hidden; rows are filed by `buySectionOf` | [confirm.js:833-857](source/public/client/UI/confirm.js#L833) |
| "The strongest X on the hull" | `Enhancements::strongestSystem`; ADV_ENG's pick-before-the-loop | [Enhancements.php:2452](source/server/model/ships/Enhancements.php#L2452), [:2516](source/server/model/ships/Enhancements.php#L2516) |
| Per-system purchases | the registry; `sanitiseSystemEnhancements` (D13 name check); the D11 destroyed-system refund; own-team ✦ | [Enhancements.php:3645](source/server/model/ships/Enhancements.php#L3645), [:4620](source/server/model/ships/Enhancements.php#L4620), [:4743](source/server/model/ships/Enhancements.php#L4743); [SystemIcon.js:612](source/public/client/UI/reactJs/system/SystemIcon.js#L612) |
| Damage history | every turn's damage rows load server-side; criticals load only while in effect | [DBManager.php:3391](source/server/controller/DBManager.php#L3391), [:3423](source/server/controller/DBManager.php#L3423) |
| The critical roll (D3) | `ShipSystem::testCritical`: d20 + floor(total damage) + system and ship `critRollMod`. Scanner and ELINT Scanner wrap it and call the parent; a missile launcher calls the parent unless its magazine explodes; a fighter's override is a dropout roll instead. Pass 1 skips a destroyed system | [ShipSystem.php:1392-1443](source/server/model/systems/ShipSystem.php#L1392); [criticals.php:84-113](source/server/handlers/criticals.php#L84) |
| Storing a dice result per system | individual notes, distributed to systems in ONE bulk loop at load. ⚠️ Some systems misread a note they do not know: a Jump Engine "takes any note it does not recognise for the pre-jump combat value" | [DBManager.php:3797-3806](source/server/controller/DBManager.php#L3797); [baseSystems.php:8539](source/server/model/systems/baseSystems.php#L8539) |
| Own-team and post-mortem gates | `isRevealedToCurrentViewer`, `isDisclosedToCurrentViewer` | [ShipSystem.php:404-423](source/server/model/systems/ShipSystem.php#L404) |
| Per-game markers | static booleans, reset and set in `markUnavailableSetMarkers()` once every ship is constructed | [TacGamedata.php:426](source/server/model/TacGamedata.php#L426), [:486](source/server/model/TacGamedata.php#L486) |
| Initiative | the d100 roll and tie separator; the sort comparator; common modifiers | [Manager.php:2598](source/server/controller/Manager.php#L2598); [TacGamedata.php:597](source/server/model/TacGamedata.php#L597); [ShipClasses.php:397](source/server/model/ships/ShipClasses.php#L397) |
| Late EW | the EW Detector's saved-EW allowance, whose rules text **is** Expert Scanner | [baseSystems.php:20447](source/server/model/systems/baseSystems.php#L20447); [EW.php:547](source/server/handlers/EW.php#L547), [:615](source/server/handlers/EW.php#L615); [ew.js:1417](source/public/client/ew.js#L1417) |
| +1 damage per die, capped | `Dice::$perDieBonus`, set around one `getDamage()` call | [weapon.php:3103-3132](source/server/model/weapons/weapon.php#L3103); [dice.php:25](source/server/lib/dice.php#L25) |
| Flat bonus damage | `Weapon::getBonusDamage` | [weapon.php:1372](source/server/model/weapons/weapon.php#L1372) |
| +1 fire control | `sysEnhApplyGSGT`: `fireControl` and `fireControlArray`, plus `isModified` | [Enhancements.php:4185](source/server/model/ships/Enhancements.php#L4185) |
| Repairs | `ShipSystem::repairCritical`; Self Repair's C&C-costs-4 rule; setCriticals Pass 2 | [ShipSystem.php:1456](source/server/model/systems/ShipSystem.php#L1456); [baseSystems.php:14433](source/server/model/systems/baseSystems.php#L14433); [criticals.php:115](source/server/handlers/criticals.php#L115) |
| Jump drives | `JumpEngine::$range`; jump-failure % computed at three sites; the arrival scatter and its initiative penalty | [baseSystems.php:6125](source/server/model/systems/baseSystems.php#L6125), [:9648](source/server/model/systems/baseSystems.php#L9648), [:10419](source/server/model/systems/baseSystems.php#L10419), [:10856](source/server/model/systems/baseSystems.php#L10856), [:8481](source/server/model/systems/baseSystems.php#L8481); [ShipClasses.php:497](source/server/model/ships/ShipClasses.php#L497) |
| Power balance | client only: "there is no server twin of getReactorPower anywhere in the tree" | [power.js:555](source/public/client/power.js#L555), [:713](source/public/client/power.js#L713) |
| Weapon families | `$weaponClass` (Laser 49 classes, Ion 17, Matter 33, Electromagnetic 57, Ballistic 46), per mode through `weaponClassArray` | [weapon.php:175](source/server/model/weapons/weapon.php#L175), [:3362](source/server/model/weapons/weapon.php#L3362) |
| Flight modifiers | `FighterFlight::getDamageMod` / `getHitChanceMod`; `isModified` publishes OB and thrust | [FighterFlight.php:374](source/server/model/ships/FighterFlight.php#L374), [:188](source/server/model/ships/FighterFlight.php#L188) |
| Ramming | every non-flight unit already carries a `RammingAttack`; using it is by consent | [ShipClasses.php:2433](source/server/model/ships/ShipClasses.php#L2433); [specialWeapons.php:2438](source/server/model/weapons/specialWeapons.php#L2438) |
| Capture | Marines' `CaptureShip` missions | [ShipSystem.php:951](source/server/model/systems/ShipSystem.php#L951) |
| Buy-dialog sections and colours (D13) | one shell for every buy, edit and bulk dialog; each section's colour as scoped `--buy-sec-*` tokens; a text filter that only hides rows (its box is commented out today) | [confirm.js:833-857](source/public/client/UI/confirm.js#L833), [:920-998](source/public/client/UI/confirm.js#L920), [:1265-1351](source/public/client/UI/confirm.js#L1265); [confirm.css:1880-1914](source/public/styles/confirm.css#L1880) |

---

## 3. The ranking

**Easy:** one or two hooks into a choke point that already exists. No new player input, no new
persisted state, and a client mirror of one line or none.

**Medium:** several hooks, or a rule the client must mirror (movement, to-hit), or a new automated
end-of-turn process, or per-viewer disclosure. Still no new per-turn player input.

**Hard:** needs at least one of: a new per-turn player decision (UI plus a persisted choice), lobby
pricing beyond a flat price, fleet-wide state, a new movement or firing capability, or a post inside
a fighter flight.

Every ranking assumes the framework (Stages 1–2) already exists. The framework itself is roughly a
Medium-sized job (§3.4).

### 3.1 Easy

| Officer | Post, cost | What it touches | Why it is easy |
|---|---|---|---|
| **Expert Helmsman** | C&C, 5 % | +5 `iniativebonus`; one clause in `TacGamedata::sortShips` so a live Helmsman sorts ahead on equal initiative; **−1 on the d20 his ship rolls on a meteor swarm's impact chart** (user, 2026-10-10: one term in `RammingAttack::resolveMeteors`, which serves the Meteor Swarm and the Triad's spawned meteoroids alike) | Initiative is rolled server-side ([Manager.php:2605](source/server/controller/Manager.php#L2605)), and `generateIniative` already turns sort order into distinct values ([:2611-2624](source/server/controller/Manager.php#L2611)). So the tie-break needs no client change. The meteor chart is rolled server-side in Pre-Firing, on a full load. |
| **Expert Scanner** | Sensor Array, 7 % | `EW::getDetectorAllowance` and its JS twin become max(detector ladder, 1 for a live officer). The "no detectors on the board → return" early exits (two server-side, one client-side) must not skip a ship that has one | FV already built this exact rule. The EW Detector's own text says it grants "the enhancement of Expert Scanner" ([baseSystems.php:20447](source/server/model/systems/baseSystems.php#L20447)). The late-EW window, budget and DB write all key off `getSavedEwAllowance`, so they need nothing. |
| **Expert Technician** | Reactor, 9 % | The reactor's `output` at load: + max(2, ⅓ of the largest `powerReq` among systems destroyed before this turn) | Server-side `output` already reaches every client ([ShipSystem.php:319](source/server/model/systems/ShipSystem.php#L319)) and power.js reads `reactor.output`, so there is no client logic. Easy **only** on the best-of reading: under it the "+1 from a deactivated system" branch can never win. If the player chooses each turn, it becomes Medium (a live mirror in power.js). *Rebuilt 2026-10-15 from the full rules (§9.10): +2, plus ⅓ of EVERY destroyed system, plus +1 while a system is switched off (power.js), and −2 on the reactor's criticals.* |
| **Matter Weapons Expert, C&C** | C&C, 10 % | Every Matter-class weapon: fire control +1 (`fireControl` **and** `fireControlArray`, `isModified`), and +1 damage in `getBonusDamage` | A copy of `SYS_GSGT`, including its trap: a bump to `fireControl` alone evaporates on the first mode switch ([weapon.php:3349](source/server/model/weapons/weapon.php#L3349)). |
| **Expert Geneticist** | C&C, 5 %, scenario only | `toHitBonus` −1 (exists). +2 to be hit: one public field, one term in `calculateHitBase` and one in weaponManager's mirror. *As built: +2 on both profiles instead, the Poor Crew / ProfileIncreased convention, so no new field or term (§9.5).* Ramming: a tooltip line | Ramming is already allowed against anything by consent ([specialWeapons.php:2438](source/server/model/weapons/specialWeapons.php#L2438)), so that clause needs no code. "Scenario only" = never offered unless a hull opts in. |
| **Expert Dogfighter** | flight, 50 % of one craft | The flight's `iniativebonus` +5 | The line `ELITE_SW` already uses ([Enhancements.php:2282](source/server/model/ships/Enhancements.php#L2282)). Easy once the fighter framework exists. |
| **Expert Missileer** | flight, 50 % + 1 per missile carried | +N to the OB that the fighter-ballistic branch grants ([weapon.php:1927](source/server/model/weapons/weapon.php#L1927)), plus the weaponManager twin | One term on each side. The price needs the magazine capacity, which `setEnhancementOptionsFighter` already computes ([Enhancements.php:1930](source/server/model/ships/Enhancements.php#L1930)). "+5 OB" needs a ruling (§6, Fighter officers). |
| **Expert Evader** | flight, 100 % × craft count | The flight's OB −1; +1 in `FighterFlight::getDamageMod` | That is the defensive choke-point for flights ([FighterFlight.php:374](source/server/model/ships/FighterFlight.php#L374)), and no client damage mirror exists. Easy if always on; Medium as a per-turn toggle like jinking. |

### 3.2 Medium

| Officer | Post, cost | What it touches | Why it is Medium |
|---|---|---|---|
| **Expert Engineer** | Engine, 5 % | A new automated end-of-turn repair pass after setCriticals Pass 2: one critical per turn, a d20 of 16+ for a C&C critical | `repairCritical` exists. What is new is the pass itself and choosing deterministically. The replay harness does not cover `criticalPhaseEffects` (it stops at Pass 1), so this needs its own tests. Build together with the Scavenger. |
| **Expert Scavenger** | Engine, 25 % | The same pass: one critical, else 4 boxes, never on a destroyed system | As above. |
| **Expert Navigator** | C&C, 7 % | Turn cost one step lower; pivot and roll −1 (min 1); accel/decel −25 % | Every one of these costs is used twice, in movement.js and movement.php, plus the ship window and tooltip readouts and the lobby. Turn cost has no defined "step" ladder in the code (§6, Navigator). *Built in Stage 6 (§9.11): the rules text gives the ladder; movement.php recomputes none of these costs, so the only movement change is one client helper for the accel rounding.* |
| **Expert Jump Officer** | Jump Drive, 5 % | Opening range +1; failure chance −20 points; arrival scatter −1 hex and −1 facing step, initiative penalty −20 | Three separate, well-localised hooks. The failure % is computed at three sites ([:9648](source/server/model/systems/baseSystems.php#L9648), [:10419](source/server/model/systems/baseSystems.php#L10419), [:10856](source/server/model/systems/baseSystems.php#L10856)) that should become one helper first. `JUMP_ACC` already shows how to publish a changed range ([Enhancements.php:3497](source/server/model/ships/Enhancements.php#L3497)). |
| **Expert Analyst** | Sensors, 10 %, capitals and ELINT | A per-load "the viewer's team has a live Analyst" static that widens `isDisclosedToCurrentViewer()` (ammo types, hangar contents) and the officer payload | The masks exist. Per-viewer state has to be computed per load and tested per viewer. Two of his clauses are moot in FV: weapon arming is already sent to everyone ([weapon.php:453](source/server/model/weapons/weapon.php#L453)), and cargo contents are not modelled (`CargoBay` is a plain box). |
| **Expert Coordinator** | flight, 50 % | A combat pivot costs 1; the −1 to-hit only from 180° | The cost lives in movement.js `doPivot` ([:1436](source/public/client/movement.js#L1436)) and the penalty in [weapon.php:1872](source/server/model/weapons/weapon.php#L1872) plus its client twin. "180°" needs net rotation counted, not pivot rows. |
| **Expert Gunner, core** | a chosen weapon, 10 %, max 3 / 2 / 1 by size | +1 fire control; +1 per die through `Dice::$perDieBonus` | The effects are Easy. The cost is that he is the first **placed** officer: a registry entry, a ship-wide cap in sanitise and on the client, the menu sub-heading, and the insignia instead of the ✦. |
| **Matter Weapons Expert, weapon** | a chosen weapon | Dies on any penetrating damage; loading −1 (next row) | A placed officer plus the "any damage" kill rule. |
| **Loading time −1** (Gunner and the weapon-mounted MWE) | — | — | Medium-Hard. `loadingtime` is overwritten from the stored loading row on every load ([weapon.php:1189](source/server/model/weapons/weapon.php#L1189)) and from `loadingtimeArray` on every mode switch ([:3350](source/server/model/weapons/weapon.php#L3350)). The client reads it from the blueprint, because `stripForJson` does not send it. Losing the officer mid-charge changes whether the weapon is loaded. One build serves both officers. |

### 3.3 Hard

| Officer | Why it is hard | The cheap half, if wanted early |
|---|---|---|
| **Expert Quartermaster** | Makes this ship's other enhancements and ammo free, which touches every price display, total and the saved-fleet bucket split; a fleet-% price; a random post; "+10 turn delay" is unclear | −30 initiative; the C&C critical modifier |
| **Breaching Expert** | His post is **one pod inside a flight**, and per-craft ids do not exist in the lobby (WEAPON plan D7). Adds a fleet-wide "20 % of all pods" cap, a change to the capture roll ([ShipSystem.php:951](source/server/model/systems/ShipSystem.php#L951)), and "+1 thrust" for one craft of a flight that moves as one | — |
| **Expert Evangelist** | Fleet-wide state: +1 to hit while he serves, then −10 initiative (QX1) and −1 to hit if he is **killed**, for every ship, with a client to-hit mirror. Also a fleet-% price, a random post, and the CPN/OSF gate exists only in notes prose | — |
| **Expert Laser Technician** | "+1 damage per power point sent, up to +50 %" needs a new per-turn allocation UI, persistence and a cap | +3 Spinal / +2 Blast / +1 other lasers, through `getBonusDamage` (`SpinalLaser`, `BlastLaser`, `ImprovedBlastLaser`, `LtBlastLaser` exist) |
| **Expert Software Engineer** | Bonus fire-control points spendable in the Firing phase need a new allocation window (`HyachComputer` only allocates in Initial Orders, [baseSystems.php:12862](source/server/model/systems/baseSystems.php#L12862)). Also a "first point of damage ignored" rule in the damage path, a submarine detection range, and a self-contradictory specialist clause (§6, Software Engineer) | — |
| **Expert Ion Technician** | "3 thrust → 1d10+1 on one ion weapon" is a per-turn spend with UI | +1 per die and +1 flat on Ion-class weapons; +4 on C&C crits; the Rad Cannon's flat 12 and forced crits are Medium |
| **Expert Surge Officer** | Six unrelated hooks: EM damage, combined Surge Cannon shots, capacitor power by ship size, EM-hardening −3, a d6 to resist deactivation, Spark Field −1 | +1 damage on Electromagnetic-class weapons |
| **Expert Ballistic Officer** | "+1 magazine round per launcher" moves the lobby's ammo limits, which are derived from magazine capacity at offer time. Free missile swaps are lobby ammo pricing | +5 launch range (`range` **and** `rangeArray` — the same mode-switch trap — and sent to the client) |
| **Expert Anticipator** | A free **turn** for a ship in the Firing phase is a new phase-3 movement for ships (today only flights move there, as combat pivots), with masking, validation and animation | Rolling initiative twice is Easy. Re-rolling the lowest die is Medium: a `Dice`-level flag, which the replay harness's own `Dice` stub must mirror ([[project_replay_harness]]) |
| **Expert Gunner, once-per-battle abilities** | A Piercing mode injected into a weapon that lacks one, and +15 % on one called shot, each once per battle and not combinable: firing-mode injection, a persisted "used", and UI | — |
| **Expert Electrician** | Per turn: deactivate the flight's weapons, convert their max damage ÷ flight size into thrust or OB, then a d6 to see whether they stay offline | — |

### 3.4 Framework costs (the part that is not any one officer)

| Framework | Size | Content |
|---|---|---|
| Ship officers (Stages 1–2) | Medium, the upper end since D3's ruling | Registry, offers, post resolver, the *disabled* derivation, the **kill roll** (capture the post's critical roll, roll for a just-destroyed post, write and intercept the `OffKilled` note), apply post-pass, payload, insignia, tooltip, buy section, lobby mirror, tests |
| Fighter officers (Stage 4) | Medium | The per-flight price exception at seven lobby sites; **one officer per flight**, so a flight's officer rows are mutually exclusive in the buy dialog; post = the lead craft; loss that counts dropout but not docking or split-launch ([fighter.php:220](source/server/model/systems/fighter.php#L220) reports all three as "destroyed"); the insignia on the craft's icon |
| Placed officers (Stage 8) | Medium | An `officer` flag on registry entries, ship-wide caps, insignia instead of ✦, the menu sub-heading, and their exclusion from the "System Enhancements (n)" count |
| Buy-dialog filter chips (Stage 2a, D13) | Easy | `confirm.js` + `confirm.css` only: a chip row in the shell, show/hide by section, chips coloured by the section tokens. Independent of officers |

---

## 4. Design

### 4.1 Data

| | Shape | Storage |
|---|---|---|
| Track 1 row (fixed or random post, fighters) | `enhancementOptions`: `[OFF_HELM, label, 1, limit, price, 0, false]` | `tac_enhancements` / `tac_saved_enh`, unchanged |
| Track 2 row (placed) | `systemEnhancements`: `[OFF_GUN, label, 1, 1, total, 0, systemid, sysname]` | `tac_sys_enhancements` / `tac_saved_sysenh`, unchanged |
| Runtime | `$ship->officers`: list of `array('id' => 'OFF_HELM', 'post' => 12, 'out' => null, 'how' => null)`; `how` is `'disabled'` or `'killed'` | rebuilt every load |
| Kill record | individual note `OffKilled` (value = officer id) on the post | `tac_individual_notes`, unchanged (§4.4) |
| Payload | `officers: [{id, post, out?, how?}]`, own team only, omitted when empty | `BaseShip::stripForJson` |

`$officers` is **protected**, with a getter. A public empty default would add a dead key to roughly
2,500 static blueprints. That is the `$crewQuality` precedent ([[project_crew_quality_enhancements]]).

### 4.2 The registry

One literal per officer in `Officers::$registry`: `label`, `post` (a key into the resolver table in
§4.3), `pct` (or a `price` callable), `limit`, `available` (faction, size, "has the post"), `apply`,
`killedByAnyDamage` (true only for `OFF_MWEW`), `placed` (lives in the system registry instead),
`flight`. The D9 exclusions (factionAge ≥ 3, OSATs, terrain) are one shared test in front of every
`available`, not something each entry repeats.

| Officer | id | Post | Track |
|---|---|---|---|
| Expert Helmsman | `OFF_HELM` | C&C | 1 |
| Expert Engineer | `OFF_ENG` | Engine | 1 |
| Expert Scanner | `OFF_SCAN` | Scanner | 1 |
| Expert Navigator | `OFF_NAV` | C&C | 1 |
| Expert Technician | `OFF_TECH` | Reactor | 1 |
| Expert Jump Officer | `OFF_JUMP` | Jump Drive | 1 |
| Expert Quartermaster | `OFF_QM` | random primary | 1 |
| Expert Geneticist | `OFF_GENE` | C&C | 1 |
| Expert Scavenger | `OFF_SCAV` | Engine | 1 |
| Breaching Expert | `OFF_BRCH` | one pod | — (§3.3) |
| Expert Analyst | `OFF_ANLY` | Scanner | 1 |
| Expert Evangelist | `OFF_EVAN` | random primary | 1 |
| Expert Laser Technician | `OFF_LASR` | Reactor | 1 |
| Expert Software Engineer | `OFF_SOFT` | Computer | 1 |
| Expert Ion Technician | `OFF_ION` | Engine | 1 |
| Expert Surge Officer | `OFF_SURG` | Mag-Grav Reactor | 1 |
| Expert Ballistic Officer | `OFF_BALL` | C&C | 1 |
| Expert Anticipator | `OFF_ANTI` | C&C | 1 |
| Matter Weapons Expert, C&C | `OFF_MWE` | C&C | 1 |
| Matter Weapons Expert, weapon | `OFF_MWEW` | chosen weapon | 2 |
| Expert Gunner | `OFF_GUN` | chosen weapon | 2 |
| Expert Dogfighter | `OFF_DOG` | lead craft | 1 (flight) |
| Expert Missileer | `OFF_MSL` | lead craft | 1 (flight) |
| Expert Evader | `OFF_EVAD` | lead craft | 1 (flight) |
| Expert Coordinator | `OFF_COOR` | lead craft | 1 (flight) |
| Expert Electrician | `OFF_ELEC` | lead craft | 1 (flight) |

Offers come from one call, `Officers::addOffers($ship)`, made at the end of
`setEnhancementOptionsShip` and of `setEnhancementOptionsFighter`. That puts them in the static
blueprints, so `fvbuild.ps1 -Statics` follows any offer change. Measure the faction-JSON growth at
Stage 1, the way WEAPON plan §3.2 did. Rough estimate: +1 % on `Earth Alliance.json`. If it is over
budget, drop the label from the wire, since the client holds the labels anyway.

### 4.3 Posts

| Post in the rules | Resolver | Notes |
|---|---|---|
| C&C | the first `instanceof CnC` that is not a `FlagBridge`; failing that, the first `CnC` of any kind | `SecondaryCnC` is not a `CnC` subclass ([baseSystems.php:3837](source/server/model/systems/baseSystems.php#L3837)), so it is never picked. `ShadowPilot` is one (:15079), but it only sits on Ancient hulls, which D9 excludes. |
| Engine | the strongest `Engine` by `output` | `strongestSystem()` semantics: the first in construction order wins a tie |
| Sensor Array, Sensors | the strongest `Scanner` by `output` | includes `ElintScanner` |
| Reactor | the biggest `Reactor` by `maxhealth` | `ELITE_CREW`'s rule ([Enhancements.php:2485](source/server/model/ships/Enhancements.php#L2485)); `MagGravReactor` extends `Reactor` |
| Mag-Grav Reactor | the first `MagGravReactor` | Surge Officer |
| Jump Drive | the first engine `JumpEngine::getUnitJumpEngines($ship)` returns that is neither legacy nor a gate | never a bare sweep of `$ship->systems` ([[project_jump_points]]) |
| Computer | the `HyachComputer` | |
| Random primary system | a hash of (game id, ship id, officer id), **salted with `$secret_phrase`**, over location-0 systems that are targetable, not Structure and not pseudo-systems | FV is open source: an unsalted formula would let an opponent work out where the Evangelist sits and call-shot it. In the lobby there is no game id yet, so it reads "posted at battle start". Positional-id caveat in §7. |
| A fighter | the flight's first craft | §4.10 |
| Placed | the stored `systemid`, verified by `sysname` | the existing D13 path |

Posts are picked **before** `Enhancements::setEnhancements` runs (D2), by `Officers::pickPosts($ship)`
at the top of `BaseShip::onConstructed`. The lobby picks with the same rules, from the same blueprint
values, **before** `lobbyEnhancements.apply` ([[arch_lobby_ship_objects]]: duck-type there, never
`instanceof`).

### 4.4 Out of action: disabled or killed

Two states, both permanent and both effective from the next turn (D3). Each runtime entry carries
`out` (the turn) and `how` (`'disabled'` or `'killed'`). He serves on turn T if
`out === null || out >= T`. When both happen in one turn, `how` is `'killed'`. *A third `how`,
`'moved'`, was added in §9.7: a fighter officer who left this flight row with his fighter. He is not out of
action, only on another row, so he serves this row to the end of that turn and is not listed on it after.*

**Disabled: derived at every load, nothing stored.** `out` is the earliest of:

1. a damage row on the post with `destroyed` set → that row's turn;
2. unless the post is a Structure block, or `survivesStructureDestruction`: the turn its Structure
   block was destroyed (for an array location, the turn the last of its blocks fell);
3. for a lead-craft post: that craft's `destroyed` damage row, or the turn of a `DisengagedFighter`
   critical. **Not** `DockedFighter` or `SplitLaunchedFighter`, which `Fighter::isDestroyed` also
   reports as destroyed ([fighter.php:220-229](source/server/model/systems/fighter.php#L220)).

The weapon-mounted Matter Weapons Expert is also derived, but as **killed**: the first damage row on
his post with damage greater than armour. No roll is involved, so nothing needs storing.

**Killed: rolled, then stored.**

- **When.** A new step in `Criticals::setCriticals`, `Officers::rollKills($activeShips, $gamedata)`,
  right after Pass 1 and before Pass 2's Self Repair ([criticals.php:83-118](source/server/handlers/criticals.php#L83)),
  behind `TacGamedata::$officersPresent`.
- **Who rolls.** Every post that still holds a serving officer and took damage through its armour
  this turn (`isDamagedOnTurn`, the same trigger Pass 1 uses).
- **Which roll.** The post's own critical roll from Pass 1. `ShipSystem::testCritical` keeps it in a
  protected `$lastCritRoll`: one assignment beside [ShipSystem.php:1421](source/server/model/systems/ShipSystem.php#L1421),
  protected so no blueprint ever carries it. Scanner and ELINT Scanner wrap the parent, so their Hyach
  halving is included ("including any modifications"). When the post made no roll this turn, the step
  rolls the same formula itself: d20 + floor(total damage) + the system's and the ship's
  `critRollMod`. That happens when Pass 1 skipped a destroyed system ([criticals.php:86](source/server/handlers/criticals.php#L86))
  and when a missile launcher's magazine exploded before it reached the parent. It is the rulebook's
  "make a roll even if the system is totally destroyed".
- **Result.** 20 or more kills every officer posted there.
- **Storage.** One individual note per death, written straight through with
  `Manager::insertIndividualNote`, the pattern for a sweep inside `advance()`
  ([[arch_individual_notes_and_phase_hooks]]). `notekey` is `OffKilled`, `notevalue` the officer id,
  and `notekey_human` "Expert Engineer killed". The longest, "Expert Software Engineer killed", is 31
  of the 40 characters allowed. Hosted on the post.
- ⚠️ **Read back by interception, never delivered.** In DBManager's bulk note loop
  ([DBManager.php:3800-3804](source/server/controller/DBManager.php#L3800)), an `OffKilled` note is
  recorded onto its ship and **not** handed to the system. Delivery would be unsafe: a Jump Engine,
  the Jump Officer's post, reads any note it does not recognise as its pre-jump combat value
  ([baseSystems.php:8539](source/server/model/systems/baseSystems.php#L8539)), and
  `HyachComputer::onIndividualNotesLoaded`, the Software Engineer's post, never calls its parent. The
  cost is one string compare per note, and no system class has to know officers exist.
- **Fighter officers make no kill roll** unless ruled otherwise (§6). The lead craft has no d20
  critical roll: its override is a d10 dropout test ([fighter.php:274](source/server/model/systems/fighter.php#L274)).
  A fighter officer is lost (disabled) when his craft is destroyed or drops out.

**For both states:**

- Computed **server-side only**. The client derives neither. `stripForJson` folds damage older than
  turn − 1 into one "Historical" row ([ShipSystem.php:262](source/server/model/systems/ShipSystem.php#L262)),
  which loses the turn, and notes never reach the client at all. The payload carries `out` and `how`.
- Pre-battle damage rows sit before turn 1. A post wrecked before battle leaves its officer disabled
  from the start, with no kill roll, since there was no battle turn to roll in.
- Replays load damage and notes up to the viewed turn, so a replay shows each officer as he was.
- `undestroyed` rows (Self Repair revival) are deliberately ignored.
- The replay harness's `damage` check mirrors Pass 1 with seeded dice. The new assignment in
  `testCritical` uses no dice, so no corpus line moves, and the harness never runs the kill step.
  `officersHarness` covers it (§5, Stage 1).

### 4.5 Applying effects

- `Officers::pickPosts($ship)` runs at the top of `BaseShip::onConstructed` (D2).
- `Officers::apply($ship, $gamedata)` runs after the per-system loop and before `iniativeadded`
  (D5). It walks live officers in **registry order**, calling each one's `apply`.
- Rules that are evaluated elsewhere (the EW allowance, jump failure, the repair pass, the initiative
  roll, the die bonus) ask `Officers::isActive($ship, 'OFF_X', $turn)`. On a hot path (to-hit,
  damage) that question goes behind **`TacGamedata::$officersPresent`**, reset and set in
  `markUnavailableSetMarkers()` the way `$chameleonPresent` is ([TacGamedata.php:488](source/server/model/TacGamedata.php#L488),
  [:519](source/server/model/TacGamedata.php#L519)). An ordinary game then pays one static read per
  shot ([[arch_defensive_mod_aggregation]], "gate it on a static boolean").
- POST-side ships (`Manager::getShipsFromJSON`) never run this path, so `officers` is empty on them.
  Nothing in a phase's `process()` may read officers
  ([[arch_post_side_ship_reconstruction]], [[arch_individual_notes_and_phase_hooks]]).

### 4.6 Visibility, and what still leaks

`BaseShip::stripForJson` sends `officers` only when `isRevealedToCurrentViewer()`, or
`TacGamedata::$currentGameFinished`, or (from the Analyst's stage) the viewer's team has a live
Analyst. That is the shape of the existing `systemEnhancements` send
([ShipClasses.php:985](source/server/model/ships/ShipClasses.php#L985)).

Officer rows are kept out of the public ship tooltip: add the `OFF_` prefix to the skip lists at
[Enhancements.php:2540](source/server/model/ships/Enhancements.php#L2540) (ships) and
[:2258](source/server/model/ships/Enhancements.php#L2258) (flights). `OffKilled` notes need no
masking: individual notes never reach any client.

| Officer | Changed number | Reaches the enemy? |
|---|---|---|
| Helmsman | `iniativebonus` | **Yes, to every viewer** (user ruling 2026-10-10, overruling the drafted own-team-only). The ship tooltip's "base" initiative must be the ship's real figure: a hidden +5 misleads more than it hides. The officer list itself stays own-team |
| Navigator | turn, pivot, roll and accel costs | **Yes, to every viewer** (Stage 6, §9.11): the 2026-10-10 default for a displayed stat, through his `publish` list. The draft said own-team only |
| Jump Officer | the drive's `range` and tooltip; the jump point counter's `vortexMaxTurns`; `arrivalIniPenalty` | **Yes, to every viewer** (Stage 7, §9.13): `JumpEngine::stripForJson` sends range and data while he serves; the counter and the arrival penalty were public already. The −1 scatter shift and the facing cancel show only as where and how the doorway formed |
| Technician | reactor `output` | **Yes.** `output` is sent unconditionally. Accept, as the ✦ does. *§9.10: the reactor's −2 critical modifier is not sent (nothing displays it), and Efficient Transfers' +1 is worked out by the owner's client alone* |
| Matter Weapons Expert, Gunner | `fireControl` via `isModified` | **Yes**, exactly like Gunsights |
| Geneticist | both profiles +2, `toHitBonus` −1 | **Must**: the shooter's own hit-chance preview needs the profiles. Published to every viewer through the registry's `publish` list (§9.5), like the Helmsman's `iniativebonus` |
| Scanner | the late-EW allowance | Own-team only: the client asks `officers.listFor`, which an enemy payload never carries |
| Every officer | `pointCostEnh` | **Yes**, as for every enhancement already |

### 4.7 UI

- **Insignia (D11).** In `SystemIcon`, `renderBadges` becomes a small top-left flex row holding the
  ✦ and the insignia. `renderBadges` already serves both return paths, the destroyed short-circuit
  and the interactive one ([SystemIcon.js:604-616](source/public/client/UI/reactJs/system/SystemIcon.js#L604)),
  so a destroyed post keeps its insignia. Three looks: **serving** gold; **disabled** grey;
  **killed** grey with a red slash. When one post holds officers in different states, the insignia
  shows the best of them (any serving officer keeps it gold). Use an inline SVG,
  `pointer-events: none`, with a black `drop-shadow` filter for legibility over light hull art. The
  `title` names each officer and his state.
- **Placed officers must not light the ✦.** `systemEnhancements.hasAny`, `systemsEnhanced` and the
  "System Enhancements (n)" summary must skip registry entries flagged `officer`.
- **SystemInfo (D12).** A gold **Officers** block after *Special*, before *Criticals*
  ([SystemInfo.js:183-188](source/public/client/UI/reactJs/system/SystemInfo.js#L183)), built from
  `officers.listFor(ship)` filtered to this post.
- **Ship-level.** An own-team **Officers** list in `ShipInfo` and in the ship window's gold
  Enhancements box. Cheap, and it is the only place a random-post officer's post can be read off at a
  glance. *Changed 2026-10-11 (user request): the gold box shows one **"Officers (n)"** row, like
  "System Enhancements (n)"; the names live on each post's insignia and tooltip (§9.6).*
- **Buy dialog (D10).** `buySectionOf` returns `'officers'` for an `OFF_` id. Optionally, an icon:
  the user's art as `img/Officers.png`, masked like the other three sections
  (SHIP_ENHANCEMENTS_PLAN.md §5).
- **System menu (D10).** `SystemEnhancementsSection` renders officer rows under an **Officers**
  sub-heading.
- **Filter chips (D13).** §4.12.

### 4.8 Lobby

- A new legacy file, `client/officers.js` → `window.officers`, added to the debug script lists of
  **both** `game.php` and `gamelobby.php` (the `battleDamage.js` precedent). It holds:
  - `LABELS` / `SUMMARIES`, a mirror of the registry;
  - `pickPosts(ship)`, a mirror of §4.3;
  - `listFor(ship)`: the payload in game, derived from `enhancementOptions` plus `systemEnhancements`
    in the lobby;
  - `isActive(ship, id)`.
- `lobbyEnhancements` skips `OFF_` rows in the tooltip it builds, and previews only the officer
  effects a lobby window actually displays (fire control, turn costs, initiative). Each preview is a
  mirror pair and goes into `enhancementsDifferential.js` ([[project_ship_enhancements]]).

### 4.9 Saved fleets and pre-battle damage

- **Track 1** uses the `tac_saved_enh` path unchanged. ⚠️ Never withdraw an `OFF_` offer without a
  conversion or refund in `loadSavedFleet`: an unmatched saved row drops silently while its cost stays
  inside `pointCostEnh` ([[arch_retiring_ship_enhancement]]).
- **Track 2** uses `tac_saved_sysenh`, the D13 name check and re-pricing, unchanged.
- **Pre-battle damage (QX5, default accepted).** A track-2 officer on a destroyed post is removed and
  refunded by the existing D11 sweep, for free. A track-1 officer whose post is destroyed is
  **disabled from the start**: the lobby shows a warning on the post and in his tooltip, and the
  player un-buys him through Edit. There is no automatic refund, because changing `pointCostEnh` from
  outside the buy dialog is exactly what WEAPON plan D5 warns against.

### 4.10 Fighters

- The post is the flight's **first craft**. The insignia sits on that craft's `FighterIcon`.
- He is lost (disabled) by §4.4's rule 3, with no kill roll unless §6 rules one in.
- **One officer per flight (D9).** In the buy and edit dialogs a flight's officer rows are mutually
  exclusive: **+** on a second officer is refused with a passive notice ("A flight may carry only one
  officer"), never a confirm ([[feedback_passive_notices]]). The server does not trust that: on a
  flight, `Officers::resolve()` serves only the first officer row in registry order, so a doctored
  payload buys nothing extra.
- **The price exception.** A per-flight officer row must not be multiplied by flight size at
  [confirm.js:215](source/public/client/UI/confirm.js#L215) or at
  [gamelobby.js:4203, 4209, 4414, 4420, 4634, 4640](source/public/client/gamelobby.js#L4203). Use
  one predicate, `officers.isPerFlight(row)`, at all seven sites, never seven copies of an id test.
- Hangar split-launch can carry the lead craft onto a "- Split" row. §6 (Fighter officers) decides
  whether he goes with it. *Ruled 2026-10-11: he does not; he stays on the row he was bought on (§9.6).
  Re-ruled the same day: he **follows his fighter**, and his cost goes with him (§9.7).*

### 4.12 Buy-dialog filter chips (D13)

Pure `confirm.js` + `confirm.css`. No officer code is involved, so this can be built first.

- **Markup.** `buyDialogShell` gets a chip row, `<div class="buyChips" hidden>`, above
  `.buyDialogSections`, in the slot the commented-out filter box occupies
  ([confirm.js:965-968](source/public/client/UI/confirm.js#L965)). It holds **All** first, then one
  `<button class="buyChip" data-section="…" aria-pressed>` per entry of `BUY_SECTIONS`, in that
  order. `BUY_SECTIONS` gains a `chip` label per section, since the Ammo section's title is
  "Ammo & Ordnance" and its chip says **Ordnance**.
- **Which chips show.** `openBuySections` already computes `used`, the sections that got rows
  ([confirm.js:1250-1259](source/public/client/UI/confirm.js#L1250)). Only their chips show, and the
  whole row stays hidden when fewer than two sections are used: a chip that filters down to the only
  section there is does nothing.
- **Behaviour.** A section chip sets `hidden` on every other used section; **All** clears it.
  Sections are hidden, never emptied. Every row stays in the DOM, so the `.shpenh<N>` spinners, the
  totals and gamelobby.js's read-back are untouched — the text filter's own promise. The choice lasts
  for that dialog only.
- **Colour.** The four section colour blocks in confirm.css
  ([:1895-1914](source/public/styles/confirm.css#L1895)) move from
  `.confirm.buyDialog .buySection[data-section="ammo"]` to `.confirm.buyDialog [data-section="ammo"]`,
  so one set of `--buy-sec-*` properties colours both the section and its chip. A picked chip uses
  the head's own recipe: `--buy-sec-bar` border, `rgba(var(--buy-sec-rgb), 0.22)` fill,
  `--buy-sec-title` text. An unpicked chip is neutral. **All** uses the dialog's neutral accent, and
  **Officers** keeps the default blue its section already falls back to.
  ⚠️ Move only the colour tokens to the shared selector. A layout rule written against a bare
  `[data-section]` would start hitting the chips too.
- **All three dialogs** (`showShipBuy`, `showShipEdit`, `showBuyBulk`) share the shell, so all of
  them get the chips with no per-dialog code.
- Chips wrap at phone width. Check at 360 px, headless ([[howto_headless_chrome_phone_width]]).

### 4.11 Placed officers

- Registry entries in `$systemEnhancementRegistry` gain `'officer' => true`, `'ages' => array(1, 2)`
  (D9 rules out Ancients, which is also the registry's own default), and a `shipLimit` callable.
  Their `eligible` must also refuse OSATs: `systemMayBeEnhanced` keeps out flights and mines but lets
  an OSAT through. `sanitiseSystemEnhancements` enforces `shipLimit` after its group pass
  ([Enhancements.php:4688](source/server/model/ships/Enhancements.php#L4688)), and the client's
  `systemEnhancements.set()` refuses past the cap.
- Offers go out per eligible weapon, which grows the static JSON. Measure it before signing the stage
  off.
- `systemMayBeEnhanced` already keeps flights and mines out
  ([Enhancements.php:3890](source/server/model/ships/Enhancements.php#L3890)), which is right: no
  placed officer is a fighter officer.

---

## 5. Stages and exit criteria

Every stage keeps these green: replay harness `check` (125 / 0 on 2026-10-08,
[[arch_replay_corpus_known_failures]]); `enhancementsHarness.php` and `enhancementsDifferential.js`
([[project_ship_enhancements]]); and, from Stage 1, a new `tests/replay/officersHarness.php`.
React edits are verified by bundle-and-evaluate, not an esbuild parse ([[howto_verify_react_bundle]]).
`fvbuild.ps1 -Autoload` follows the new class; `-Statics` follows any offer change. Nothing is
committed.

| Stage | Content | Exit test |
|---|---|---|
| **0** | ~~Rule D1–D12 and QX1–QX5~~ (done 2026-10-09). Rule the Helmsman's question (§6). Record the three baselines | Numbers written down. **DONE 2026-10-10** (§9.1) |
| **1** | Server framework with **Expert Helmsman** (and its meteor clause). **BUILT 2026-10-10** (§9.2) | `officersHarness`: ids ≤ 10; offers (no C&C → no Helmsman; an Ancient hull, an OSAT, a mine, terrain → none); post picks (two equal Engines with Poor Crew bought → the same engine as the lobby picks); **disabled** (post destroyed on turn N → serves N, out N+1; block cascade → disabled, no roll; Self Repair revival changes nothing; destroyed pre-battle → never serves); **killed** (a damaged post rolling 20+ → killed, 19 → not, through a seeded `Dice`; a post destroyed this turn still rolls; the Hyach scanner halving reaches the roll; the note is written, read back on the next load, and never reaches the post's own `onIndividualNotesLoaded`, proven on a Jump Engine post); the Helmsman's +5 and tie-break on a forced tie; payload per viewer (owner, teammate, enemy, spectator, finished game). Replay `check` byte-identical, since no corpus game has an officer |
| **2a** | Buy-dialog filter chips (D13), independent of everything else here, so it can go first. **BUILT 2026-10-10** (§9.4) | Every buy, edit and bulk dialog: chips only for sections with rows, none at all with a single section; a picked chip shows its section's colours; hiding a section changes no total and no saved row (buy with **Enhancements** picked, then reload the fleet); 360 px wrap. Headless |
| **2** | Client framework. **BUILT 2026-10-10** (§9.3) | Lobby: buy a Helmsman, the Officers section shows, points move, the insignia lands on the C&C, the tooltip line appears. Destroy the C&C in the pre-battle editor → the warning shows. Game: own team sees the insignia in all three looks (serving, disabled, killed), an enemy browser sees nothing, a finished game shows all. Headless, driving the real site ([[howto_headless_chrome_phone_width]]) |
| **3** | Easy ship officers: Scanner, Technician, Matter Weapons Expert (C&C), Geneticist. **BUILT 2026-10-10** (§9.5) | Scanner: the late-EW window opens on a ship with no EW Detector on the board. MWE: switch modes and back, the +1 stays. Technician: power balance after a system dies |
| **4** | Fighter framework; Dogfighter, Missileer, Evader; the Expert Motivator made an officer. **BUILT 2026-10-11** (§9.6) | A per-flight price is identical across buy, edit, copy and save/reload on 1-, 3- and 6-craft flights. A second officer on one flight is refused in the dialog, and a doctored payload with two serves only one. A lead-craft dropout loses him; a dock does not |
| **5** | Repair pass: Engineer and Scavenger; the Scavenger's once-per-game save. **BUILT 2026-10-15** (§9.8) | Deterministic pick; the C&C d20; no repair of this turn's crits (if so ruled); no revival of a destroyed system; runs after Self Repair |
| **6** | Navigator. **BUILT 2026-10-15** (§9.11) | Server and client thrust agree on every cost change; the ship window shows the new numbers ~~own-team only~~ (published to every viewer, §4.6) |
| **7** | Jump Officer. **BUILT 2026-10-15** (§9.13), with the faction rules (§9.12) | Range +1 (with and without the Jump Accelerator); failure −20 at all three sites through one helper; arrival scatter and initiative |
| **8** | Placed-officer framework; Gunner core; weapon-mounted Matter Weapons Expert (without loading) | The cap per size; a Gunner and Gunsights on one gun show ✦ and insignia side by side; "any damage" kills; the destroyed-host refund |
| **9** | Coordinator; Analyst | The Analyst's disclosure per viewer, and its harness `masking` check |
| **10** | Loading time −1 | A mode switch keeps it; the officer lost mid-charge; the client's `isLoaded` agrees with the server |
| **11+** | Hard officers, one at a time, each starting with its own short plan and ruling round | — |

---

## 6. Rules questions

### Cross-cutting — RULED 2026-10-09

| # | Question | Ruling |
|---|---|---|
| **QX1** | Initiative units. FV rolls d100 and writes tabletop × 5: Elite Crew's "+5" is one tabletop point. Are the brief's numbers FV units? | Yes: Helmsman +5, Dogfighter +5, Quartermaster −30 and the Jump Officer's "−20" are FV units. The Evangelist's "−2" is tabletop, so **−10** in FV |
| **QX2** | Does a critical on his post kill an officer? | **Yes**, on a critical roll of 20 or more, modifiers included (D3) |
| **QX3** | A cap on officers per unit, beyond one of each? | None for ships. **A flight may buy one officer in total** (D9) |
| **QX4** | Which units may carry officers: bases, OSATs, LCVs, Ancient hulls? | **Not** Ancient hulls, OSATs (mines included) or terrain. Bases and LCVs only where the officer's post exists, so a base without a jump drive gets no Jump Officer (D9) |
| **QX5** | Track-1 officer whose post was destroyed pre-battle: disabled with a warning, or refunded automatically? | Disabled from the start, with a warning (§4.9). Default accepted without comment |

### Per officer (needed when that stage starts)

| Officer | Questions |
|---|---|
| Helmsman | Does the tie-break also apply under the simultaneous-movement initiative categories? (Default: no; ties there are categories, not orderings.) **Built with the default, 2026-10-10** (not ruled by the user; nothing in a category is ordered, so there is no tie to break; his +5 still counts toward the category, through `getInitiativebonus`). Reverse it if ruled otherwise. |
| Engineer | Reading: "once per turn, repair one critical anywhere; a C&C critical needs 16+ on a d20"? Which critical: the highest `repairPriority` system's oldest? Never a critical from this turn, as Self Repair? On a failed roll, try another or stop? **RULED 2026-10-15** (the user supplied the rules text, §9.8): one critical a turn, automatic, except a C&C critical, repaired only on a d20 of 16+; a failed attempt ends his repairs that turn. Not until the ship has suffered it a full turn (a critical from turn 3 acts on 4 and may be repaired at the end of 4) - Self Repair's own filter. The pick: **Self Repair's default order** (the critical's priority plus its system's; no player overrides). |
| Scanner | Cumulative with EW Detectors, or the same point? (Default: max(1, ladder), not cumulative.) **RULED 2026-10-10: cumulative** - the ladder + 1. |
| Navigator | (a) The turn-cost ladder. The hulls use 0.25, 0.33, 0.5, 0.66, 0.75, 1, 1.25, 1.33, 1.5, 1.66, 1.75, 2 and 3. Which of those are "steps"? (b) Turn delay unchanged? (c) Accel/decel: × 0.75 per change, or on the turn's total? Which rounding? **RULED 2026-10-15** (the user supplied the rules text, §9.11): (a) the rulebook's ladder 2, 1.75, 1.66, 1.5, 1.33, 1.25, 1.0, 0.83, 0.75, 0.66, 0.5, 0.33, 0.25, 0.16, 0.1; (b) **turn delay unchanged** (asked: the text names turn cost only); (c) −25 % with the fractions kept until the turn's full cost is known, then rounded up. Pivot and roll −1, minimum 1; 7 % "dropping fractions". |
| Technician | "Or" = the best of the three automatically, the player's choice each turn, or a fixed priority? How is 33 % rounded? **RULED 2026-10-10: the best of the three, automatically; a third, rounded down.** **RE-RULED 2026-10-15** (the full rules text, §9.10): all three at once - +2 power and −2 on reactor criticals; +1 from one system shut down for power, once a turn (a system forced offline by a critical does not count - asked); a third, rounded down, of EVERY destroyed system's power requirement, added together (asked). |
| Jump Officer | (a) +1 over the drive's own range (4 → 5, or 6 → 7 with the Jump Accelerator)? (b) −20 before or after the Accelerator's doubling? (c) On arrival: the officer of the ship that OPENED the doorway, or each arriving rider's own? (d) Does −20 also cover the hyperspace-jump failure roll? **Rules text supplied 2026-10-15** (§9.13), which settles most of it: 5 hexes not 4; held one turn longer; −20 on the chance of the drive destroying the ship ("half the boxes ... 30%, not 50%"), never usable with every box lost; −1 on the scatter chart when HIS ship opens the vortex; 60° of unintended facing change cancelled, never past the original facing; the scatter initiative penalty −20 for HIS unit only, never a bonus; 5%; not Raiders or Belt Alliance, not without a jump drive. Built with defaults, stated in §9.13 and not yet ruled: (a) +1 on whatever the drive has (6 → 7 with an Accelerator); (b) after the doubling; (c) the facing cancel turns the DOORWAY (so every rider), the initiative relief is each unit's own officer; (d) one helper serves all three rolls, but a legacy drive is never his post, so in practice he never meets the boost-path roll. |
| Quartermaster | "Free" = this ship's own other enhancements and ammo? What is "+10 turn delay"? How much on C&C crits? Post: random primary? Price: 5 % of the game's points limit? |
| Geneticist | "Scenario only" = never offered unless a hull opts in, or a Create Game rule? "−1 to hit other ships" = this ship's own weapons? **RULED 2026-10-10: offered on every hull with a C&C** ("scenario only" is by agreement, as the doc card says). The −1 was built with its default: this ship's own weapons. **RE-RULED 2026-10-15: Dilgar Imperium only** (§9.12). |
| Scavenger | A critical first, else 4 boxes? Which system first? May he repair Structure? **RULED 2026-10-15** (§9.8): **a critical first** (as the Engineer, same order and C&C roll), else 4 boxes, by Self Repair's default system order; **Structure yes**, never a destroyed system or anything on a destroyed section, never this turn's damage. His rules text adds a **once-per-scenario save** - "up to six points of damage can be ignored in any single volley" - ruled **automatic**: spent on the first hit that would destroy the C&C, a reactor or a Structure block when ignoring up to 6 points saves it ("note the Gaim Bulkhead already has a similar function"). The campaign clauses (permanent repairs after the battle) are not built. **Widened 2026-10-15 (§9.10): Scanners and Engines too.** **Faction prices 2026-10-15 (§9.12): 15% for the Descari, 20% for Balosians, Raiders and Llort.** |
| Breaching Expert | "+1 thrust" for one pod of a flight that moves as one? "20 % of total pods": fleet-wide, rounded down? What are "3 attack rolls on the first boarding turn" in FV's capture-mission terms? |
| Analyst | Does he need range, line of sight or an OEW lock? Every enemy ship, or only those in range? Weapon arming is already public in FV: leave it so? |
| Evangelist | The fleet = this player's ships, or the whole team? His penalty is worded "if killed": does a merely **disabled** Evangelist (post destroyed, kill roll survived) cost the fleet its +1 without the −10 initiative / −1 to hit? (Default: yes, the penalty needs a kill.) Is the penalty permanent? Do reinforcements that arrive later get the bonus? |
| Laser Technician | Lasers = `weaponClass` Laser (49 classes)? Is the power boost declared in Initial Orders? Is the +50 % cap on base damage? |
| Software Engineer | "Lets the computer specialist use their ability twice" and "Incompatible with Computer Specialist" contradict each other. Which one stands? "Submarine" = the Hyach stealth hulls (Alichi Kav and Tal)? Whose detection range gets +5? |
| Ion Technician | Rad Cannons always score 12, with no roll? Forced crits on Sensors, Engine and Reactor on every hit? When is the thrust boost declared? Is "+4 on C&C crits" a penalty? |
| Surge Officer | Which systems are the "caps" that get +2 / +1 power? Is "EMHardened gives −3" a change for the whole ship? |
| Ballistic Officer | "+5 distance range" = launch range of every ballistic weapon, torpedoes included? How is "+1 total magazine rounds per missile weapon" counted? How are the free swaps priced (+2 % each)? |
| Anticipator | Free turn: a full turn (not a pivot) in the Firing phase, paying its thrust? "Half price for some factions": which factions? |
| Matter Weapons Expert | May a ship carry both variants? Does the weapon variant also give that weapon the +1 fire control and damage? (Stage 8.) The C&C variant's "only for factions with matter weapons" was built with a default, not ruled: offered to a hull carrying at least one weapon with a Matter mode (§9.5). **RULED 2026-10-15: Grome Autocracy only** (§9.12); the Matter-weapon hull test is kept beside it. |
| Gunner | Size caps for LCVs and bases? Which weapons are eligible: any with fire control, ballistics too? Does he stack with Gunsights? |
| Fighter officers | Post = the first craft? A craft has no d20 critical roll (its test is a d10 dropout), so: no kill roll at all, or a d20 + damage roll on any damage to his craft, 20+ kills? (Default: none; he is lost with his craft.) Does he go with that craft on a split-launch? Missileer's "+5 OB": is that +1 OB (5 %)? Evader: always on, or toggled like jinking? Coordinator's "180°+" = three combat pivots in one turn? **RULED 2026-10-11:** the post is the first craft; he is lost **with his craft** (destroyed or dropped out), with **no kill roll**; a dock, a partial dock or a split launch never loses him, and he **stays on the flight row he was bought on** (a "- Split" row never carries him). Missileer: **+1 OB** on a launch that gets the flight's OB, priced **half a craft once, plus 1 per missile the whole flight can carry**. Evader: **always on**. Expert Motivator: **a full officer** (one per flight, lost with his craft, hidden), under his old id, effect and price. Coordinator: Stage 9. **RE-RULED 2026-10-11 (round 2, §9.7):** a **remotely controlled** unit (`$remoteControl`, the Orieni Hunter-Killers) carries no officer of any kind - "they have no pilots". An officer's abilities apply only to the part of the flight that holds his fighter, so when the flight splits **he follows his fighter**: the new row gets him, posted in its first craft, and his cost; the old row loses him from the next turn. The Evader's −1 OB also lowering mine detection is accepted. |

---

## 7. Risks

| Risk | Mitigation |
|---|---|
| **The insignia implies a secrecy it does not deliver** | D4 plus the §4.6 table, which is honest about what leaks; tooltip wording must not overclaim |
| **Two tracks drift**: an effect, insignia or tooltip that reads one track but not the other | Everything reads `$ship->officers` / `officers.listFor()`; nothing reads the rows directly after `resolve()` |
| **Positional ids**: a hull edited mid-game moves a random post, or makes a stored placed post point at another system | Placed: D13 drops a mismatched row. Random: acceptable, and the same exposure every positional id already has ([[arch_positional_system_id_trap]]) |
| **Order-dependent effects** differ between lobby and game | D5's fixed post-pass, the same order on both ends; the differential harness |
| **Out-of-action read off the wrong data**: the aggregated client damage, or the in-effect-only criticals | Disabled is derived server-side from the full damage history, killed from the stored note (§4.4); the client only reads `out` and `how` |
| **The kill note reaches a system that misreads it**: a Jump Engine takes an unknown note for its pre-jump combat value | Intercepted in DBManager's bulk note loop and never delivered (§4.4); the Stage 1 test puts the note on a Jump Engine post |
| **The kill roll is not the post's real roll**: an override that skips the parent leaves `$lastCritRoll` stale | Read `$lastCritRoll` only when it was set **this turn**; otherwise roll the formula (§4.4). Overrides today: Scanner and ELINT Scanner call the parent; a missile launcher skips it only when its magazine explodes; a fighter never posts a ship officer |
| **Hot-path cost** in to-hit, damage and the sort comparator | `TacGamedata::$officersPresent`; test the gate by forcing it false |
| **The fighter price exception** missed at one of seven sites | One tuple flag, and a Stage 4 exit test across buy, edit, copy and save/reload |
| **A Dice-level change** (Anticipator's re-roll) breaks every damage line in the harness | Add the same static to the harness's own `Dice` stub ([[project_replay_harness]]) |
| **Lobby re-application compounds** a stat bump | Idempotent from a remembered blueprint value, as `systemEnhancements.apply` is |
| **Shared client system references**: bumping one Matter gun bumps every gun of that class | Copy on first write ([[arch_client_system_shared_reference]]) |
| **A withdrawn offer** silently drops saved officers while keeping their cost | §4.9; never retire an `OFF_` id without a `loadSavedFleet` conversion |
| **Static JSON growth** | Measured at Stages 1 and 8 |
| **A filter chip loses a purchase**: a section hidden by a chip drops out of the totals or the read-back | Chips set `hidden` only; no row ever leaves the DOM. Stage 2a buys with one chip picked and reloads the fleet |

---

## 8. Out of scope

- Officers gaining experience, being promoted, or being swapped between posts mid-battle.
- Buying or moving officers in game. Lobby only, like every other enhancement.
- A distinct insignia per officer type (one insignia in v1; the tooltip names them).
- A combat-log line announcing an officer's death (D4).
- Updating FV_factions.txt:438, which still offers Shadow ships officer enhancements that D9 now
  rules out. Flagged to the user, not changed here.
- Enforcing ramming consent. FV leaves it to the players today (the Geneticist only adds a tooltip line).

---

## 9. As built

### 9.1 Stage 0: baselines (2026-10-10)

HEAD `e09c2aa7b`. "Clean" = this work stashed, nothing else touched (the three dirty bundles are not
server code). Every number below was reproduced with Stage 1 in place.

| Gate | Clean tree | With Stage 1 |
|---|---|---|
| Replay harness `check` | **120 passed / 5 failed / 7 SKIP.** The 5 (4297, 4356, 4357, 4359, 4361) differ only in `/ships/N/systems/N/name: TrekPhaser -> TrekPhaserKelly`, i.e. the recent Phaser commits, not re-recorded. The 7 SKIP are idle-deleted games. | Byte-identical, timings stripped |
| `enhancementsHarness.php check` | 257 / 0, fingerprint 13,030 lines | 12 lines **added** (one `OFF_HELM` offer per test hull), 0 removed, every payload md5 unchanged. Re-recorded: 13,042 lines, 257 / 0. The previous file is kept as `baseline_enhancements/fingerprint.before-officers.txt` |
| `enhancementsDifferential.js` | 263 cases (25 skipped), 0 differing; 909 jump delays, 0 differing | Same |
| `profileCoatingClientHarness.js` | 2,901 / 0 | Same |

The Helmsman's §6 question was built with its default (no tie-break inside a simultaneous-movement
category), not ruled.

### 9.2 Stage 1: server framework and Expert Helmsman

**Files.** New: `source/server/model/ships/Officers.php` (registry, offers, posts, out-of-action,
kill roll, effects, payload) and `tests/replay/officersHarness.php`. Hooks: `ShipClasses.php`
(`$officers` / `$officerKills`, both protected; `pickPosts` at the top of `onConstructed`; `apply`
after the per-system loop; `addForJson` in `stripForJson`), `ShipSystem.php` (`$lastCritRoll`,
protected, written by `testCritical`; `getCritRollOnTurn`), `criticals.php` (`rollKills` after
Pass 1), `DBManager.php` (`OffKilled` intercept in the bulk note loop), `TacGamedata.php`
(`$officersPresent`; the tie-break clause), `specialWeapons.php` (the meteor term),
`Enhancements.php` (`addOffers`; `OFF_` kept out of both public tooltip lists). `autoload.php`
regenerated (+1 line). Statics regenerated.

**As the plan said, with these specifics:**
- Registry entries are `label`, `post`, `pct` (whole percent) and `apply` (a static method name, or
  null). Price is `ceil(pct % of pointCost)` in whole numbers, so 7 % of 100 is 7, not 8.
- Post `cnc`: the first C&C that is not a Flag Bridge, else the first Flag Bridge. Three hulls have
  only a Flag Bridge (PrimusMaximus, Nakarsa, GarasochD), so the fallback is real.
- "Out" at turn 0 (pre-battle damage) never serves, even in the lobby, which sits at turn 0.
- The kill roll groups officers by post: one roll per post, one note per officer. Eligible: not
  already killed, and either still in action or put out of it this turn. The in-memory list is
  updated too, so later code in the same request sees the death.
- `$lastCritRoll` is set by every `testCritical` call, including the forced crit tests some weapons
  make outside Pass 1. If Pass 1 skipped a post (destroyed) but such a forced roll happened earlier
  that turn, the kill reads that roll. That counts as the post's own roll, so it was accepted.
- Payload, own team or a finished game: `officers: [{id, post, out?, how?}]`, plus `iniativebonus`
  whenever a serving officer moved it. Anyone else gets no list, and an `iniativebonus` that Elite
  Crew, Poor Crew, Markab Fervor, the Ipsha refit or Sluggish publishes has the officer's share taken
  back out. A Helmsman-only hull publishes no `iniativebonus` at all, as before.

**Measured.** Statics +83,618 B, which is exactly the size of the offers (0.133 %; Earth Alliance
+0.105 %). 1,820 of 2,670 hulls are offered a Helmsman, all factionAge 1 or 2; none is an OSAT, mine,
terrain or flight.

**Harness.** `officersHarness.php` runs 103 checks (95 with `--no-db`). The DB section writes an
`OFF_HELM` row and an `OffKilled` note hosted on a real Jump Engine inside a rolled-back
transaction. Mutation-tested with 11 deliberate breaks, each of which fails the harness: the
tie-break, the meteor term, the roll capture, the kill threshold, the `setCriticals` hook, the enemy
subtraction, kill read-back, the turn-0 rule, the structure cascade, the ship tooltip skip and the DB
intercept. A 12th, removing the FLIGHT tooltip skip, passes unnoticed: no flight can carry an officer
before Stage 4, which must add that test.

**Known gaps and deferrals:**
1. ~~The fresh roll for a post destroyed this turn omits the Hyach "damage halved for critical rolls"
   (it lives in `Scanner::testCritical`). A captured roll includes it (tested). No Stage 1 post is a
   scanner; decide it in the Scanner officers' stage.~~ Resolved by Stage 3 (§9.5): `rollFor` mirrors it.
2. The exit test "two equal Engines with Poor Crew → the same engine as the lobby picks" has no
   Stage 1 subject (the Helmsman's post is the C&C). It moves to Stage 2 (lobby mirror) and Stage 5
   (the first Engine post).
3. Two loaders read notes straight from the DB by ship id and bypass the intercept:
   `MindriderEngine` (Movement phase) and `JumpEngine::readArrivalOrder`. Both filter by system or
   key, so an `OffKilled` note is harmless to them today. Re-check this when an officer is posted on
   a Mindrider Engine (age 3, so never under D9) or on a Jump Engine (Stage 7).
4. In a replay view of turn N, "serving" is judged at the game's current turn, as `isDestroyed()` is.
   So the replayed `iniativebonus` shows the +5 only if he still serves now; `out` and `how` are
   correct for the viewed turn.
5. ~~Until Stage 2, the offer shows in the lobby buy dialog's Enhancements section and in
   `lobbyEnhancements`' own tooltip.~~ Resolved by Stage 2 (§9.3).

**Play-test (user, 2026-10-10, game 4457).** G'Quan #1 (player 210) and an Omega with Elite Crew (player
211) each bought a Helmsman. A server load as each player confirmed: posted on the C&C, serving,
`iniativebonus` 5 and 10, each list sent only to its owner, and the enemy's published `iniativebonus` (the
Omega's Elite Crew line) without his +5. The game uses `initiativeCategories: 1`, so every ship's
displayed initiative is the one bracket (−20); the +5 is in `unmodified_iniative` (49 / 59 / 106).

### 9.3 Stage 2: client framework

**Files.** New: `source/public/client/officers.js` (`window.officers`), on both pages' script lists after
`systemEnhancements.js` (`game.php`, `gamelobby.php`; the legacy bundler scrapes them).
Changed: `UI/confirm.js` (`buySectionOf` files `OFF_` rows into the reserved Officers section),
`lobbyEnhancements.js` (`OFF_` kept out of the lobby tooltip), `reactJs/system/SystemIcon.js` (badge row
plus insignia), `reactJs/system/SystemInfo.js` (the Officers block; it now imports `theme`),
`reactJs/shipWindow/ShipNotesPanel.js` (Officers lines in the gold panel; the lobby Initiative row),
`reactJs/shipWindow/ShipWindow.js` (the gold panel shows for an officer-only hull). Bundles rebuilt
(`fvbuild -Client`). No server code changed.

**`window.officers`**, one API for every surface:
- `listFor(ship)`: `ship.officers` when the server sent it (own team, or a finished game). Otherwise it
  is derived from the bought `OFF_` rows, the way `Officers::pickPosts` derives it. That derivation is
  safe from leaks: only the buyer's browser holds non-zero counts, server payloads never carry
  `enhancementOptions`, and a static blueprint's rows are all 0, so an enemy ship in game derives
  nothing. In the lobby a post destroyed by the pre-battle editor gives `out: 0, how: 'disabled'`, which
  is what the server will rule.
- `findPost` mirrors `Officers::findPost` by duck-typing (`name === 'cnC'`; a Flag Bridge is a `cnC`
  carrying the `FlagBridge` special ability). `serves` mirrors `Officers::serves`.
- `postState` (the best state on a post, D11), `stateText`, `describePost`.
- ⭐ `displayIniativeBonus(ship)`: the lobby's +5 is added **at read time**, never written into the ship.
  That way a C&C destroyed in the pre-battle editor takes the +5 away on the next render, and an edit
  cannot compound it. This deviates from §4.8 ("lobbyEnhancements previews"), deliberately.
  `enhancementsDifferential.js` compares initiative through this function.

**UI as built.**
- **Insignia:** an inline-SVG double chevron in a top-left `BadgeRow` that it shares with the ✦ (star
  first). Gold is `theme.colors.enhTitle`; out of action is light grey `#c4c4c4`; killed adds a red slash
  `#ff4d4f`. ⚠️ The grey is light because a destroyed icon is blurred as a whole by `System`'s
  `filter`, which no child can escape: a mid grey vanished. On a destroyed post it is still soft, as the
  star is; the tooltip and the gold panel carry the state in words. Like the star, its `title` is not
  hoverable (`pointer-events: none`).
- **System Info:** a gold "Officers" block between Special and Criticals: name, effect line, and
  "(disabled, turn N)", "(killed, turn N)" or "(post destroyed before battle - he will not serve)".
- **Gold panel:** an "Officers" sub-title under Enhancements, one line per officer, "Expert Helmsman -
  C&C" plus his state, dimmed once he is out of action. `withEnhPanel` counts officers.
- **Buy dialog:** the Officers section keeps the default blue and no icon until there is art
  (`img/Officers.png`). The lobby fleet list names him with his price, which is the player's own fleet.
- **Not done:** `ShipInfo` (it shows enhancements only for flights, mines and terrain, none of which can
  carry an officer yet). That belongs to Stage 4.

**Verified.**
- `tests/replay/officersClientHarness.js`: 24 renderToString checks (three looks, the destroyed-system
  path, enemy ship, star alongside, System Info block, gold panel, lobby initiative incl. pre-battle).
  Before them it requires the bundle in a fresh process, with a **negative control**: the same bundle
  with `SystemInfo.js`'s `theme` import stripped must fail with a ReferenceError, and does.
- Real site, headless, read-only (`tests/replay/officersE2E/`, README there):
  - `game.php`, game 4457, as each player: 13 / 13 each. Own payload, insignia, gold panel, a real
    mouse hover giving the Officers tooltip, the disabled and killed looks (payload states set on the
    page), the enemy window showing nothing, no POST, no page error.
  - `gamelobby.php`, a seeded and then deleted lobby: 16 / 16. The Officers section appears and the
    Helmsman is not under Enhancements; **+** adds ceil(5 % of 925) = 47; the bought row carries 47
    points; insignia, gold panel, Initiative 5 and the hover tooltip; then the C&C destroyed through
    the pre-battle editor's own calls turns the insignia grey with the warning and Initiative 0.
- Gates: enhancementsHarness 258 / 0 (three Helmsman cases added to `$CASES` / `$DIFF_CASES`; `applied()`
  now runs `pickPosts → setEnhancements → apply` like a load; re-recorded, additions only, 1,524 lines),
  differential 299 cases / 0 differing, officersHarness 103 / 0.
- "A finished game shows all" is proven server-side (officersHarness: the finished-game payload). The
  client draws a sent list the same way, whoever sends it.

### 9.4 Refinements round 1 (user, 2026-10-10) and Stage 2a

The user also shortened the Helmsman's effect line in `officers.js` to "+5 initiative, wins ties, -1 on
meteor chart". The tests now read it from `officers.get('OFF_HELM').summary`, so a reword cannot break
them.

**1. The document: renamed "Ship Options", with an Officers group.** The Ammo, Options & Enhancements
document is now titled **Ship Options** everywhere a player reads it: the viewer tab and heading
(`docViewer.js`), the page title (`docPage.php`), game.php's USEFUL LINKS button, the gamelobby and
games.php links, and the cross-document links in the FAQ and Factions & Tiers. The URL
(`ammo-options-enhancements.php`), the doc key (`ammo`) and the file (`docs/ammo-options.html`) are
deliberately unchanged, so every bookmark, Discord link and `data-fvdoc="ammo"` still lands. A new
**Officers** group at the end has two entries:
- **About Officers** (`#officers`): buying, posts, eligibility, losing an officer (disabled or killed,
  the next-turn rule, pre-battle damage), the insignia, the tooltip and the gold box, and what the
  opponent can still see.
- **Ship Officers** (`#shipofficers`): one card per officer, Expert Helmsman so far.

About Enhancements links to it. Later stages add a card each, and Stage 4 adds Fighter Officers.

**2. Stage 2a, the buy-dialog filter chips (D13), built as §4.12 described.**
- `confirm.js`: `BUY_SECTIONS` gains a `chip` label (Ammo's reads "Ordnance"). The shell puts a
  `.buyChips` toolbar in the commented-out filter box's slot: All, then one chip per section.
  `addBuyChips` (called from `openBuySections`) shows only the chips whose section got rows, and the
  whole row only with two or more. `pickBuyChip` sets `hidden` on the other sections and opens the
  picked one; All shows them all.
- `confirm.css`: the section colour tokens moved from `.buySection[data-section=…]` to a bare
  `[data-section=…]`, so the chip, which carries the same attribute, wears the same tokens. Defaults are
  on `.buySection, .buyChip`. A picked chip uses the head's recipe (bar border, 0.22 wash, title colour);
  All uses `--fv-accent`. Coarse pointers get 34 px chips.
- ⭐ A chip only HIDES sections. Rows never leave the DOM, so totals and the gamelobby.js read-back are
  untouched; the lobby driver buys with the Officers section hidden and the officer is still bought.

**3. Initiative is public (user ruling; D4 and the §4.6 Helmsman row amended).** "Not showing the real
initiative value of the ship is more misleading." `Officers::addForJson` now publishes
`iniativebonus` to **every** viewer whenever a serving officer has moved it, and no longer subtracts the
officer's share from an enhancement-published value. The officer list itself stays own-team. So an
enemy's ship tooltip reads the ship's real "base", e.g. 10 for the Omega with Elite Crew and a Helmsman,
without being told who is behind it.

**Verified.**
- officersHarness 101 / 0: the per-viewer checks now expect the real bonus everywhere.
- enhancementsHarness re-recorded, 258 / 0. Twelve `helm`-case payload hashes moved at an identical
  length: `iniativebonus` is now written before `officers`, the same keys in another order.
- Differential 299 / 0 differing; officersClientHarness 24 / 0.
- Real-site drivers (`tests/replay/officersE2E/`): game 15 / 15 per player, including the enemy's real
  `ShipTooltip` showing "base 10" and "base 5"; lobby 25 / 25, including the chips: which ones show,
  section isolation, colours, a purchase surviving a chip switch, all 83 Earth Alliance units (70 with a
  chip row, 13 single-section ones without), and the 360 px wrap; the Ship Options document 8 / 8.

### 9.5 Stage 3: Expert Scanner, Expert Technician, Expert Geneticist, Matter Weapons Expert (C&C)

**Rulings (user, 2026-10-10, §6).** Scanner: **cumulative** with EW Detectors (the ladder + 1). Technician:
the **best of the three, automatically**, a third **rounded down**. Geneticist: **offered on every hull with
a C&C** ("scenario only" is by agreement). Built with defaults, not ruled: the Geneticist's −1 is this ship's
own weapons; the Matter Weapons Expert is offered to a hull carrying at least one weapon with a Matter mode.

**Files.** Server: `Officers.php` (four registry entries; posts `scanner` and `reactor`; the `needs` test;
the effects; the generic `publish` list; the Hyach halving in `rollFor`), `EW.php` (`getDetectorAllowance`
adds the officer's point and no longer exits on an empty board when he is aboard; `submitLateEw`'s empty-board
exit is behind `$officersPresent`), `weapon.php` (`getBonusDamage`: +1 on a Matter-mode shot). Client:
`officers.js` (registry, posts, `passesNeeds`, `isActive`, `savedEwBonus`, `displayStat`, `reactorBonus`,
`displayFireControl`, `adjustDataDisplay`), `ew.js` (the allowance twin), `power.js` (`getReactorPower` adds the
lobby Technician), `SystemInfo.js` (its data hook passes through `officers.adjustDataDisplay`),
`ShipNotesPanel.js` (the lobby Profile row through `officers.displayStat`). Docs: four cards in Ship Options
→ Ship Officers, and the About Officers "what the opponent sees" line. Statics and bundles rebuilt
(`fvbuild -Statics`, `-Client`); no new class, so autoload is unchanged.

**As built:**
- **Registry.** Two optional keys: `publish` (the ship fields his apply moves that every viewer is sent while
  he serves, which replaced the Helmsman-only `initiativeShare`) and `needs` (a further offer test, checked
  by `addOffers` and `pickPosts` through `isEligible`, so a doctored row buys nothing). Apply methods now take
  `($ship, $entry, $turn)`. Order: Helmsman, Scanner, Technician, Geneticist, Matter Weapons Expert.
- **Posts.** `scanner` = the strongest Scanner by output (ELINT included); `reactor` = the biggest Reactor by
  maxhealth (Elite Crew's rule). ⭐ The lobby must pick from the **store's pristine hull**
  (`gamedata.allShips`), because `lobbyEnhancements.apply` has moved the fleet ship's outputs by the time the
  insignia draws: on pirocia's two equal scanners Poor Crew lowers the first, and a pick on live values chose
  the second while the server (D2) chose the first. `officers.strongest` reads the store copy; the
  differential now gives its sandbox one and compares every post.
- **Scanner.** `EW::getDetectorAllowance` = ladder + `Officers::savedEwBonus` (1 while he serves). The pool
  clamp, the window and the write are the detectors' own. The client twin asks `officers.savedEwBonus`, which
  reads the own-team list, so an enemy hull answers 0.
- **Technician.** At load the post's `output` += max(2, ⌊largest powerReq / 3⌋) over the systems destroyed
  BEFORE this turn (`isDestroyed($turn - 1)`: Self Repair revivals excluded, pre-battle damage counts on turn 1,
  the reactor itself never). `output` reaches every client already. The lobby adds the same at read time in
  `getReactorPower` (`officers.reactorBonus`, only for his post and only for a derived list).
- **Geneticist.** `forwardDefense` and `sideDefense` +2, `toHitBonus` −1, all three published to every viewer.
  **+2 on the profiles, not a new to-hit field** (§3.1 had drafted one): the profiles are what Poor Crew and the
  C&C's ProfileIncreased critical move for "easier to hit", so every to-hit path, server and client, already
  has it, and the ship window's Profile row shows the true figure. The ramming clause is the doc card's note
  only. The client's hit-chance breakdown labels `toHitBonus` "Crew Quality", so his −1 appears under that name.
- **Matter Weapons Expert.** +1 on every non-null entry of every Matter **mode** of every weapon: in
  `fireControlArray`, and in the live `fireControl` when the current mode is Matter; `isModified` sends both.
  A mode missing from `weaponClassArray` keeps the weapon's own class, as `changeFiringMode` leaves it. The
  client needs nothing in game: `shipManager.systems.initializeSystem` already rebuilds an `isModified`
  weapon's fire-control tooltip line, and a mode switch rebuilds it from the bumped array. +1 damage rides
  `getBonusDamage` (behind `$officersPresent`, read off the fired mode's `weaponClass`); every Matter weapon's
  damage passes through it (the `getDamageMod` overrides that skip it are all Plasma). In the lobby,
  SystemInfo's data hook rewrites the fire-control row at read time (`officers.displayFireControl`).
- **The fresh kill roll** for a scanner post destroyed this turn now includes the Hyach halving (§9.2 gap 1):
  `rollFor` mirrors `Scanner::testCritical` and `ElintScanner::testCritical`.

**Measured.** Statics +277,813 B (+0.41 %), exactly the new offers: 1,820 Technician, 1,820 Geneticist, 1,819
Scanner and 416 Matter Weapons Expert offers across 2,670 hulls.

**Verified.**
- `officersHarness.php` 189 / 0 (181 with `--no-db`): offers and prices, the four posts, D2 on pirocia, a
  doctored Matter row, the Scanner's allowance (no detector; cumulative with a Waymarker's; the DEW clamp; the
  gate; disabled) and a real `submitLateEw` write with no detector on the board, the Technician's bonus
  (+2; powerReq 7/8/9/12; this turn not counted; revived not counted; pre-battle counted; killed), the
  Geneticist's profiles, to-hit and per-viewer payload, the Matter Weapons Expert on the Altarian plus a
  `GromeLgtRailgun` (Matter modes 1, 3, 4 and a Plasma mode 2: switch to Plasma and back, the +1 stays), null
  fire control, the payload, the gate, and the Hyach fresh roll. Two Stage 1 checks were reworded ("no C&C →
  no Helmsman" and the disabled-offer check now look for that one id). Mutation-tested: 15 breaks, all caught.
- `officersClientHarness.js` 56 / 0: lobby profile row, the Technician preview (+2, a third rounded down, his
  post only, not in game), the Matter fire-control row (Plasma mode, null, not in game), the `needs` gate, the
  pristine scanner pick, and the real `ew.getDetectorAllowance` and `power.js` `getReactorPower`. Mutation-tested:
  8 breaks, all caught.
- `enhancementsHarness.php`: Altarian added to the hulls, eight officer cases added; re-recorded, **additions
  only** (0 lines removed, 5,167 added; the old file is `baseline_enhancements/fingerprint.before-officers-stage3.txt`),
  258 / 0. `enhancementsDifferential.js`: 408 cases, 0 differing; it now compares profiles and to-hit "as shown",
  reactor output with the lobby preview, every weapon's fire control, and the posts. Four drift mutations caught.
- `fvbuild -Check`: autoload up to date, ship-data validator clean, replay corpus 140 / 0. The neighbouring EW
  harnesses: `walkersStage12Harness.php` 33 / 0, `kirishiacRefits` 168 / 0 and 88 / 0, `profileCoatingClientHarness`
  2,901 / 0; `lateEwElintHarness.js` and `walkersStage12ClientHarness.js` fail exactly as on HEAD (stale stubs,
  [[arch_stale_stage_harnesses]]).
- Real site, headless (`tests/replay/officersE2E/`): game 4457 18 / 18 per player (adds the Scanner with no
  detector on the board: allowance 0 → 1, the enemy 0, the Firing-phase window open with him and shut without,
  and the ship window's "Saved EW 1"); lobby 25 / 25; the new `lobby3.mjs` 19 / 19 (offers and prices, three
  gold insignia, Profile 90/100 on the Omega, the reactor icon +2, the pre-battle editor taking the Geneticist's
  +2 away, the Altarian's Matter Cannon tooltip −5/20/20 over −10/15/15); docs 10 / 10.

**Observations, not changed:**
1. On a Mag-Grav (fixed-power) reactor a destroyed system draws nothing anyway, so the Technician's "third of
   a destroyed system" there is pure gain rather than recovered power. Built as the brief reads; rule it if it
   matters for the Ipsha.
2. The buy dialog lists officer rows alphabetically (Geneticist first), not in registry order. The Stage 2
   lobby driver had clicked the section's first **+**; it now picks the Helmsman's own row.
3. On a base, `getReactorPower` reads only the first reactor's `output`; if the biggest reactor (the
   Technician's post) is not first, his bonus is invisible client-side. Elite Crew and Improved Reactor have the
   same exposure today.

### 9.6 Stage 4: fighter officers, and the Expert Motivator made one

**Rulings (user, 2026-10-11, §6).** The post is the flight's first craft. He is lost **with his craft**
(destroyed or dropped out), with **no kill roll**; a dock, a partial dock or a split launch never loses him,
and he stays on the flight row he was bought on (*re-ruled in §9.7: he follows his fighter*). Missileer: **+1 OB** on a launch that gets the flight's OB,
priced **half a craft once, plus 1 per missile the whole flight can carry**. Evader: **always on**. Two
requests with them: the fighter enhancement **Expert Motivator** is an officer (a full one: one per flight,
lost with his craft, hidden from the opponent), and the ship window shows its officers as **one "Officers
(n)" row** in the gold Enhancements block, like "System Enhancements (n)".

Built with defaults, not ruled (stated to the user before building): **eligibility** is D9 read literally -
every flight except Ancient ones (factionAge ≥ 3), flight-shaped mines (MineClass) and MicroSATs (`$osat`),
so shuttles, breaching pods and the Orieni Hunter-Killers are offered the new three; the Motivator keeps his
old availability (*the Hunter-Killers were then ruled out, §9.7*). The Dogfighter is priced half a craft **once**. Every fighter ballistic launch counts as a
"missile" (torpedo launchers too). The Evader's "−1 to hit" is **−1 OB**, not `toHitBonus`: the client's
flight hit-chance path never reads a flight's `toHitBonus`, and OB is what every fighter to-hit path shares.

**Files.** Server: `Officers.php` (four fighter registry entries with `flight`, `per`, `perMissile`;
`isOfficerId` takes registry ids, so EXP_MOTIV; `mayCarryOfficers` lets flights in; `craftPrice`;
`missilesPerCraft`; post `craft`; one officer per flight in `pickPosts`; `craftLostTurn`; no kill roll; the
effects), `Enhancements.php` (EXP_MOTIV's offer, effect and JSON case removed - the registry owns them;
`Officers::addOffers` at the end of `setEnhancementOptionsFighter`), `FighterFlight.php` (`getDamageMod`: the
Evader, behind `$officersPresent`), `weapon.php` (`$ballisticOB` in the fighter-ballistic branch). Client:
`officers.js` (the registry mirror; `firstCraft`, `missilesPerCraft`, the `craft` post, flight lists of one,
`isPerFlight` / `perCraftExtra` / `flightCost` / `priceNote`, `missileObBonus`, `summaryLine`), `confirm.js`
(`enhRowCost`; each row's `perCraftExtra` and price note; the one-officer line and `officerBlocked`),
`confirm.css` (the line's flash), `gamelobby.js` (the six read-backs and `enhancementLines`),
`lobbyEnhancements.js` (the Motivator's notes line gone), `weaponManager.js` (`computeOEW`), and React:
`FighterIcon` (the card's insignia), `SystemIcon` (`OfficerInsignia` exported), `ShipInfo` (the flight's
OFFICERS lines; OB and initiative as shown), `SystemInfo` (OB as shown), `ShipNotesPanel` ("Officers (n)" in
both gold blocks; the flight stats as shown; `officerLinesFor` removed), `ShipWindow`. Docs: Ship Options
gains **Fighter Officers** (four cards; the Motivator's card moved there, with a pointer left in Fighter
Enhancements) and About Officers covers flights. Statics and bundles rebuilt; no new class, autoload unchanged.

**As built:**
- **Prices.** `craftPrice` = ceil(pct % of one craft) in whole numbers, a craft being pointCost / 6 (a
  superheavy's pointCost is its own) - the convention every fighter enhancement uses, and exactly the
  Motivator's old ceil(pointCost / 60) and ceil(pointCost / 10). `per: 'flight'` officers (Dogfighter,
  Missileer) are charged once, `per: 'craft'` ones (Evader, Motivator) per craft. The Missileer's offer carries
  only his per-flight part; the lobby adds 1 per missile a craft carries, per craft, when it prices the row
  (`officers.perCraftExtra`, the mirror of `Officers::missilesPerCraft`: magazine capacity plus legacy launchers'
  `maxAmount`). ⭐ **One rule, `officers.flightCost`, at every lobby site that turns a fighter row into points**:
  `confirm.getTotalCost` and `paintBuySummary` (through `confirm.enhRowCost`), `doBuyShip`, `doCopyShip` and
  `doEditShip` (both buckets each, through `confirm.enhRowCost`), and `enhancementLines` (the fleet list) - nine
  sites, not §4.10's seven: `paintBuySummary` and `enhancementLines` had been missed. Save / reload re-prices
  nothing (`enhvalue` is stored); an edit re-prices from the reloaded offer, by the same rule.
- **One officer per flight.** On a flight the Officers section opens with "A flight may carry only one
  officer."; **+** on a second one, a count typed into one, or the mouse wheel (it fires `input`) is refused and
  the line flashes in the warning yellow - passive, never a dialog. `pickPosts` serves only the first row in
  registry order (Dogfighter, Missileer, Evader, Motivator), so a doctored payload buys one.
- **Lost with his craft.** `craftLostTurn`: the craft's earliest destroyed damage row, or its DisengagedFighter
  critical (permanent, so loaded every turn) - never DockedFighter or SplitLaunchedFighter, which
  `Fighter::isDestroyed()` also reports. A spawned "- Split" row gets only the ammo rows
  (`HangarOps::copyFlightAmmoEnhancements`), so he stays where he was bought, as ruled. `rollKills` skips
  fighter officers: `rollFor` would otherwise roll the ship formula for a craft.
- **Effects.** Dogfighter: +5 `iniativebonus`, published. Evader: −1 `offensivebonus`, published, and +1 in
  `FighterFlight::getDamageMod`. Motivator: −2 `critRollMod`, as EXP_MOTIV always was (unpublished, as before).
  Missileer: +1 on `$ballisticOB`, the OB a fighter launch gets when it gets one at all, and its twin in
  `weaponManager.computeOEW`.
- **The Motivator.** EXP_MOTIV is kept, since live games and saved fleets hold rows under it. Being an officer
  id, it leaves both public tooltips (the server's FLIGHT skip list and `lobbyEnhancements`), lands in the
  Officers buy section, and loses its lobby notes line. No local game or saved fleet held one, so the replay
  corpus is untouched.
- **UI.** The insignia sits on the first craft's card, on the card's own top-left corner (just inside it, it
  sat on the first weapon icon and read as that gun's). Hovering a craft shows the flight's `ShipInfo`, which
  gets an OFFICERS line per officer with his state. ⭐ Both gold blocks (the ship window's `EnhancementsPanel`
  and the lobby flight rail) show one **"Officers (n)"** row after the enhancement lines - every officer aboard,
  fallen ones included - in place of Stage 2's "Officers" sub-title and per-officer lines.

**Measured.** Statics +35,862 B (+0.02 %): 359 Dogfighter, 359 Evader and 84 Missileer offers; the 342
Motivator offers are byte-identical to before (17 Shuttles-set flights still have none).

**Verified.**
- `officersHarness.php` 261 / 0 (244 with `--no-db`): offers and prices (Aurora; Gamma's legacy racks;
  Thunderbolt's magazine; a superheavy; the Ancient, mine and MicroSAT flights; the Shuttles set), the post,
  one officer on a doctored payload, rows that buy nothing, lost with the first craft (destroyed, dropped out)
  and not (docked, split-launched, another craft), pre-battle, no kill roll, every effect - including the real
  `Weapon::calculateHitBase` (a Missileer launch 105 % against 100 %, nothing for a target out of arc) and a
  real dropout roll on its threshold - the payload per viewer, and the FLIGHT tooltip skip (the Stage 1
  mutation nothing could catch). Three DB round trips, rolled back: a real flight loading EXP_MOTIV, the Jump
  Engine note (its game scan used to give up after the first game whenever more than 40 were active, so it
  had been skipping), and a saved fleet reloaded through the real `Manager::loadSavedFleet` - the exit test's
  save / reload. Mutation-tested: 25 breaks, all caught.
- `officersClientHarness.js` 85 / 0: the derived flight list, `missilesPerCraft`, `flightCost` and `priceNote`,
  `confirm.enhRowCost`, the stats as shown, `missileObBonus`, `FighterIcon`, `ShipInfo`, both gold blocks and the
  real `weaponManager.computeOEW`. Mutation-tested: 24 breaks, 23 caught; the 24th is the "fighter officer only
  on a flight" guard in `listFor`, which nothing can trip (a ship has no first craft, a flight no C&C) - like
  its server twin in `isEligible`.
- `enhancementsHarness` 265 / 0, fingerprint unchanged (its hulls are ships). `enhancementsDifferential`: 443
  cases (35 new flight cases), 0 differing; it now compares a flight's OB and `critRollMod` as shown. Two drift
  mutations caught.
- `fvbuild -Check`: autoload up to date, validator clean, replay corpus 140 / 0.
- Real site, headless (`tests/replay/officersE2E/`): the new `lobby4.mjs` 27 / 27 (buy dialog, prices at 6 / 3
  / 1 craft, the limit, buy / copy / edit, the card insignia, "Officers (1)" and "(2)", the Evader's OB, the
  Motivator's notes line gone); `lobby` 25 / 25 and `game` 18 / 18 per player (both now expect "Officers (1)");
  `lobby3` 19 / 19; `docs` 14 / 14.

**Observations, not changed** (*1-3 answered in round 2, §9.7*):
1. A split launch moves a proportional share of the flight's `pointCostEnh` onto the "- Split" row
   (HangarOps Stage 21.5), the officer's included, although he stays behind. Fleet-list valuation only.
   *§9.7: his cost now goes whole with him, and he goes with his fighter.*
2. The Evader's −1 OB also trims what a flight can spend on mine detection, which OB funds. *Accepted.*
3. Shuttles, breaching pods and the unmanned Orieni Hunter-Killers are offered the Dogfighter and the Evader
   (D9 read literally), while the Shuttles fighter set still withholds the Motivator. Rule it if they should not be.
   *§9.7: remotely controlled units carry none; shuttles and breaching pods keep theirs.*
4. Not run: a real-site game.php check of a flight officer, since no local game holds one. The client harness
   renders the in-game card and tooltip from the server's payload shape, and the server harness checks that
   payload.

### 9.7 Refinements round 2 (user, 2026-10-11): no officers on remote-controlled craft; he follows his fighter

**Rulings.** "Hunter-Killers should be excluded from all Officer options, as they have no pilots. So anything
with `$remoteControl = true` should be excluded." "The officer's abilities should only affect the part of the
flight containing the fighter they are based in, not any fighters that are split off." Asked what happens
when that first fighter is itself split off: **he follows his fighter** - the new row gets him, posted in its
first fighter, and his cost; the old row loses him from the next turn. The Evader's −1 OB lowering mine
detection: "Ok, since the OB is lower."

**Files.** `Officers.php` (`mayCarryOfficers`: `$remoteControl`; the `'moved'` state; `rowPrice`,
`shareWithCraft`, `officerAmong`, `followFighter`; the payload), `ShipClasses.php` (`$officerMoves`,
`recordOfficerMove`, `getOfficerMoves` - the twin of the kill list), `DBManager.php` (the `OffMoved`
interception beside `OffKilled`), `HangarOps.php` (five spawn sites, three value splits), `Manager.php`
(`loadSavedFleet`'s refund), `gamelobby.js` (the saved-fleet notice heading), docs (About Officers). Statics and
bundles rebuilt; no new class, autoload unchanged.

**As built:**
- **No officers on a remote-controlled unit.** `mayCarryOfficers` returns false for `$remoteControl`, so no
  offer (the three classes: `HkShiningLight`, `HkShiningStar`, `hkBlazingStarGC`) and, for a doctored or old
  row, no officer at load. Statics −414 B: their Dogfighter, Evader and Motivator offers. Shuttles and breaching
  pods are manned and keep theirs.
- ⚠️ **The refund.** An HK could buy the Expert Motivator while it was an enhancement, and a saved fleet may
  hold one (none locally). An unmatched saved row drops silently while `enhvalue` stays, so the fleet would
  keep paying for him ([[arch_retiring_ship_enhancement]]). `Manager::loadSavedFleet` now refunds an officer
  row the unit is no longer offered - `Officers::rowPrice`, the lobby's rule - and lists it in the reload notice
  ("Enhancements changed since this fleet was saved:"). A **running** game with an HK Motivator loses his −2
  dropout from its next load (he is no longer an officer it may carry); its points stay spent.
- **He follows his fighter.** Every HangarOps path that moves craft from one flight row to a new one calls
  `Officers::followFighter($source, $target, $crafts, $gamedata)` just before the craft state is copied, in the
  order of `$crafts`: a partial launch (`launchWholeFlight`), a partial dock's fragment (`spawnFragmentFlight`),
  a rail escape (`spawnRailEscapeFlight`), a carrier-death escape (`spawnEscapeForBucket`), and a catapult's
  stash extraction (`consumeStashesForLaunch`). If his serving craft is among them it moves to the **front**, so
  its state lands in the new row's first craft - his post there by the ordinary rule; the new row buys his row
  (`insertSingleEnhancement`, mirrored in memory and re-posted); and the old row gets one **`OffMoved`** note on
  his old post (`notekey_human` "Expert Dogfighter left with his fighter").
- **`'moved'`.** Read back by interception in the bulk note loop, like `OffKilled`, into the ship's move list;
  `apply` takes the earliest of killed / moved / disabled, so a move beats a disengage on the same turn (the
  craft left serving). He serves the old row to the end of that turn (the next-turn rule), is listed in its
  payload up to then without `out`/`how` (he did not fall), and not at all after. The client needs nothing new.
- **His cost goes with him.** `Officers::shareWithCraft($flight, $total, $k, $n, $withOfficer)` is the one rule
  for splitting a flight's `pointCostEnh`: his own `rowPrice` whole to the side that has him, the rest by craft
  as before - exactly `round($total * $k / $n)` on a flight without one. Used by `spawnLaunchedKFlight`,
  `performWholeFlightDock` (fragment and remnant) and `escapeShedCraft` (the remnant), with `officerAmong`
  deciding the side.

**Verified.**
- `officersHarness.php` 292 / 0 (31 new): no offers on the three HKs and no officer from a doctored HK row;
  `rowPrice` (Aurora Dogfighter 29, Evader 342, Motivator 36; Thunderbolt Missileer 58; Omega Helmsman 47);
  `followFighter` on fake flights (transfer and new post, a staying craft, a lost officer, the moved state at T
  and T+1, a move beating a same-turn disengage, the payload); `shareWithCraft` (33 / 53 / 24); the real hangar
  paths with a DB recorder - `performWholeFlightDock` with and without his craft docking (fragment 53 / remnant
  47 against 24 / 76), `launchWholeFlight` launching 1 of 2 from a fragment (41), and `spawnRailEscapeFlight` /
  `spawnEscapeForBucket` by reflection (53 / 24); a saved fleet's HK Motivator refunded through the real
  `loadSavedFleet`; an `OffMoved` note read back through the real load, rolled back. Mutation-tested: 19 new
  breaks (the exclusion, each step of `followFighter`, `'moved'`, the payload, the cost split, each HangarOps
  transfer and cost site, the refund, the interception), all caught. The first run found an in-memory move
  record in `followFighter` that nothing depended on; removed.
- `officersClientHarness.js` 85 / 0, `enhancementsHarness` 265 / 0 (fingerprint unchanged),
  `enhancementsDifferential` 443 cases / 0 differing, `fvbuild -Check` all passed, replay corpus 140 / 0.
- Real site (`officersE2E/`): `lobby4` 30 / 30 (a manned Templar keeps its Officers section; both Orieni HKs
  have none), `docs` 15 / 15, `lobby` 25 / 25, `lobby3` 19 / 19, `game` 18 / 18 per player.

**Observations, not changed:**
1. `consumeStashesForLaunch` moves him only when his fragment heads the launched flight (the first fragment
   consumed). That per-bay path serves catapults, one craft at a time, so a second fragment in one launch is not
   met today. It also prices nothing: the catapult path never split `pointCostEnh`, so his cost stays where it was.
2. A per-craft officer (Evader, Motivator) is re-priced by `rowPrice` from the row's own `flightSize` at each
   later split, not from what was originally paid. Fleet-list valuation only.
3. A flight row he has left still holds his `tac_enhancements` row: harmless, since the `OffMoved` note wins at
   every load (HangarOps has no path that moves craft back into an existing row).

**Follow-up (user, 2026-10-11): the flight tooltip's OFFICERS block in gold.** `ShipInfo` (hovering a craft, and
game.php's Notes popup) now renders it with System Info's own styled parts, exported from `SystemInfo.js`:
`OfficerHeading` (the "OFFICERS:" heading), `OfficerEntry` (gold text, dimmed once he is out) and `OfficerName`.
Their colours are tripled-class (`&&&`), because the Notes popup's compact mode recolours every Entry and Header
through a two-class descendant selector. Checked: client harness 88 / 0 (three new: the shared parts on both
tooltips, and the dimmed line), and computed colours in headless Chrome, normal and compact (heading and name
#e8cf93, line #d8be86, a lost officer's line #7f9bb8, other headings unchanged). Both places are game.php-only,
and the lobby suppresses the fighter hover tooltip.

**Follow-ups (user, 2026-10-11):** a flight's gold Enhancements block (in the blue rail) read blue - `Block $gold` now
sets the gold body text itself, so it matches the grid's `EnhancementsPanel` (lobby4 compares the computed colours).
And since a flight carries one officer, **in the lobby** its gold line names him ("Expert Missileer") instead of
"Officers (1)" (`officers.summaryLine`, lobby = `gamephase === -2`); ships, and flights in game, keep the count.

### 9.8 Stage 5: Expert Engineer and Expert Scavenger (2026-10-15)

**Rulings (user, 2026-10-15, §6).** The user supplied both officers' rules text. Engineer (Engine, 5 %): one
critical a turn, automatic except a C&C critical (d20 of 16+; a failed attempt ends his repairs that turn),
never one the ship has not yet suffered for a full turn. Scavenger (Engine, 25 %): each turn one critical as
the Engineer does, **else** 4 boxes, Structure included; and **once per scenario** "up to six points of damage
can be ignored in any single volley" - ruled **automatic**. The pick: **Self Repair's default order**.

Built with defaults, stated before building and not contested: the pass runs after every Self Repair; never a
destroyed system or anything on a destroyed section; an officer still repairs at the end of the turn he falls
(D3's next-turn rule); no log line (D4); the campaign clauses (permanent repairs, rebuilding a system after the
battle) are not built.

**Files.** `Officers.php` (registry `OFF_ENG`, `OFF_SCAV`; post `engine` = the strongest Engine by output;
`runRepairs` / `repairOneCritical` / `criticalQueue` / `repairBoxes`; `scavengerSave`, `SAVE_NOTE` 'OffSaved',
`isSaveNote`; the payload's `used`), `ShipClasses.php` (`$officerSaves`, `getOfficerSaves`, `recordOfficerSave` -
the twin of kills and moves), `DBManager.php` (the `OffSaved` interception beside `OffKilled`/`OffMoved`),
`criticals.php` (`Officers::runRepairs` after Pass 2, behind `$officersPresent`), `ShipSystem.php` (one line in
`assignDamageReturnOverkill`, behind `$officersPresent`). Client: `officers.js` (registry rows, the `engine` post
on the store's pristine outputs, `stateText`'s "(save used, turn N)"). Docs: two Ship Officers cards and two
About Officers lines. Statics and legacy bundles rebuilt; no React change, no new class, autoload unchanged.

**As built:**
- **The repair pass** (`Officers::runRepairs`, Criticals::setCriticals after Pass 2). Per ship: a serving Engineer
  repairs the first critical of `criticalQueue`; then a serving Scavenger does the same, and only if there was
  NO critical to try does he repair 4 boxes. `criticalQueue` is a MIRROR of Self Repair's step 2 - repairable
  critical on a repairable undestroyed system, `turn < now`, not `oneturn`, `turnend == 0`; priority = the
  critical's own plus its system's below 10; then system id, then critical id. A C&C critical (`instanceof CnC`,
  so never SecondaryCnC) needs `Dice::d(20) >= 16`; a failure ends that officer's turn, so a Scavenger who fails
  repairs no boxes, and a Scavenger after a failed Engineer tries the same critical again. `repairBoxes` mirrors
  Self Repair's step 1 without its destroyed-system bump: undestroyed, not on a section whose block is destroyed
  at this turn (`isDestroyed` alone would let its systems through until next turn), damage minus
  `damageReceivedOnTurn`; spilled by repairPriority, then id. Saved as Self Repair's work is: `repairCritical`
  (turnend + forceModify) and a negative `DamageEntry` (damageclass `'Repair'`, never `undestroyed`).
- **The save** (`Officers::scavengerSave`, from `assignDamageReturnOverkill` AFTER the protector step). On a C&C,
  Reactor or Structure: if the hit's damage through armour would destroy it (`dealt >= health`) and 6 points
  would stop that (`dealt - 6 < health`), it ignores `min(6, dealt)`, marks the save spent in memory
  (`recordOfficerSave`) and writes one `OffSaved` note on his Engine straight through
  `Manager::insertIndividualNote`. ⭐ It runs on what any protector left (user: "the Gaim Bulkhead already has a
  similar function"), so a Bulkhead or a shield pool spends itself first and the save is the last resort; it
  never spends itself on a hit the Bulkhead already stopped. One hit only: a volley's other rakes and pulses land.
- **Disclosure.** The owner's payload row carries `used` (the turn); `officers.stateText` words it, so SystemInfo,
  ShipInfo and the insignia title need nothing new. Repairs themselves are public (criticals and damage rows),
  which the About Officers list of "numbers everyone can read" now says.

**Measured.** Statics: 3,440 new offer tuples (1,720 hulls x 2), about 163 KB.

**Verified.**
- `officersHarness.php` 353 / 0 (332 with `--no-db`): offers and posts; the Engineer (priority order, one a turn,
  no die, this turn's critical waits, the C&C 16 / 15 with nothing after a failure, a destroyed system skipped,
  his Engine destroyed last turn vs this turn, a Helmsman repairs nothing, and the REAL `Criticals::setCriticals`
  with the gate on and off); the Scavenger (critical first, 4 boxes spilling reactor 2 + Structure 2, this turn's
  damage left, a destroyed weapon and a weapon on a block destroyed this turn skipped, a failed C&C roll leaves no
  boxes, with an Engineer two criticals, the Engineer's failed C&C retried); the save (the note and memory, once
  only, survives-anyway / 6-cannot-stop / not-key / the edge, a hit under 6 ignored whole, reactor and Structure,
  disabled, spent earlier, an Engineer has none, the real `assignDamageReturnOverkill` with the gate on and off,
  a Gaim Rafa whose Bulkhead absorbs first and whose save stops the rest, the payload per viewer); and a DB
  round trip (an `OffSaved` note hosted on a Jump Engine reads back as spent and never reaches the engine).
  Mutation-tested: 28 breaks, all caught (the first run found a gap - "ignore 6 even of a smaller hit" - closed
  with the under-6 check).
- `officersClientHarness.js` 97 / 0 (the registry rows, the derived list on the strongest engine, D2 on the
  store's pristine outputs, pre-battle disabled, `stateText` and the System Info line). 4 client mutations caught.
- `enhancementsHarness` 267 / 0 after re-recording the fingerprint; with the two offers withheld it matched the old
  one exactly, and the re-recorded diff is 24 added offer lines plus payload hashes (old copy kept as
  `fingerprint.before-officers-stage5.txt`). `enhancementsDifferential` 443 / 0. Replay corpus 146 / 0.
- `fvbuild -Check`: all three checks pass (the validator after the user re-recorded its baseline for another
  developer's hit-chart fixes, `8454aec91`).
- Real site, headless (`officersE2E/`): `lobby4` 35 / 35 (new: the Omega's Engineer 47 / Scavenger 232 rows, the
  279 total, both posted on the strongest engine, one insignia naming both; the Motivator at 50 %), `lobby` 25 / 25,
  `lobby3` 19 / 19, `docs` 16 / 16. `game.mjs` 13 / 18 per player: game 4457 has since been SURRENDERED (turn 1,
  phase 1), so D4 now reveals its Helmsman to the enemy and the Firing-phase Scanner checks cannot run - the game's
  state, not a regression; that driver needs a fresh ACTIVE officer game.

**Follow-up (user, 2026-10-15): the Expert Motivator at 50 %.** Confirmed (`32c9f2a11`); the harness, the lobby4
driver, the registry comment and the Fighter Officers card now say 50 % ("same effect but a lower price" before).
Observation, not changed: `Manager::loadSavedFleet` refunds an unoffered officer row at today's `rowPrice`, so a saved
Hunter-Killer fleet whose Motivator was bought at 10 % would be refunded at 50 % - the refund clamps the fleet's
enhancement value at 0, eating into its other enhancements. None exists locally.

### 9.9 Fighter officer prices scaled by flight size; saved fleets re-priced (2026-10-15)

**Rulings (user, 2026-10-15).** "The current prices for Fighter Officers are based on a standard flight of 6
fighters": each one is pct % of ONE craft for a flight of six, and a flight of another size pays it **scaled by
crafts / 6**. The Evader (100 %) and the Expert Motivator (50 %) are no longer per craft - "neither should be
costed on a flight basis". The Missileer's +1 per missile per craft is unchanged. Saved fleets bought under the
old prices are corrected on reload, by **storing each officer row's price**. ("HKs are a bad example": they carry
no officers - the §9.8 follow-up's refund case is now simply "refund what was paid".)

Built with defaults, stated here: the scaling is of the **listed** six-craft price, `ceil(listed x crafts / 6)`, so
the dialog's "29pts per 6 craft" can be checked by hand (an Aurora Dogfighter is 15 / 29 / 44 / 58 at 3 / 6 / 9 /
12 craft); a single superheavy (maxFlightSize 1) is its own standard flight, unscaled (none is officer-eligible
today: a harness probe covers it).

**Files.** `Officers.php` (`per` gone from the registry; `standardFlight`; `craftPrice` on it; `flightPrice`, THE
rule; `rowPrice` through it; `legacyPrice` + `$legacyFighterPrices`), `Manager.php` (`submitSavedFleet` stores an
officer row's `rowPrice`; `loadSavedFleet` re-prices every officer row), `DBManager.php` (`submitSavedEnhancement`
/ `getSavedEnhancementsForShip` carry `enhvalue`), **`db/savedOfficerPrice.sql`** (new; `emptyDatabase.sql` too).
Client: `officers.js` (`per` gone, `isPerFlight` gone, `standardFlight`, `flightCost` scaled, `priceNote` "per 6
craft"), `confirm.js` (the row's `standardFlight`, read by `enhRowCost`), `gamelobby.js` (`enhancementLines`).
Docs: the Fighter Officers lede and four cards. Statics and bundles rebuilt; no new class.

**As built:**
- **The rule.** `Officers::flightPrice($flight, $id, $crafts)` = `ceil(craftPrice x crafts / standard)` + the
  Missileer's `perMissile x missilesPerCraft x crafts`; ⚠️ MIRROR PAIR `officers.flightCost(enhID, count, enhCost,
  size, extra, standard)`. Every lobby site already went through `flightCost` (nine, §9.6), so only its two direct
  callers needed `standard`: `confirm.enhRowCost` (from the row's `data('standardFlight')`, set where the row's
  `perCraftExtra` is) and `enhancementLines`.
- ⚠️ **`standardFlight` is `maxFlightSize === 1`, NOT `instanceof SuperHeavyFighter`.** The lobby prices a craft as
  pointCost / 6 unless maxFlightSize is 1. Five SuperHeavyFighters (FangedSerpentAM, SkySerpentAM, the three Rogolon
  SHFs) fly up to 3 with a pointCost for six, so the old test priced their officers off all six craft: a
  FangedSerpentAM's Dogfighter offer was 435, now 73. Every fighter ENHANCEMENT in Enhancements.php still asks
  `instanceof SuperHeavyFighter` (observation 1).
- **Saved fleets.** `tac_saved_enh.enhvalue` (DECIMAL, NULL): `submitSavedFleet` writes `rowPrice` for every officer
  row (ship officers too) and NULL for every other row. `loadSavedFleet`, for each officer row: paid = the stored
  value, or for NULL the price the row was charged - `legacyPrice`, the Stage 4 rules live ran until now (Dogfighter
  / Missileer 50 % once per flight, Evader 100 % per craft, Motivator 10 % per craft - also his price as an
  enhancement - with live's `instanceof SuperHeavyFighter` craft, quirk included); today's `rowPrice` for a ship
  officer. Offered: `pointCostEnh - paid + now`, and a notice line "<name>: Expert Evader now costs 57 points (was
  342)." under the existing "Enhancements changed since this fleet was saved:" heading. Not offered: refunded `paid`.
  A fleet saved after this change reloads untouched (stored = today's).

**Verified.**
- `officersHarness.php` 382 / 0 (339 with `--no-db`): `flightPrice` tables (Aurora Dogfighter 5 / 10 / 15 / 29 / 44 /
  58 at 1 / 2 / 3 / 6 / 9 / 12, Evader 10 / 29 / 57 / 86 / 114, Motivator 15 / 29 / 58; Thunderbolt Missileer 38 / 76 /
  152), the FangedSerpentAM offers (73) and a single-superheavy probe (435 unscaled), `rowPrice`, `legacyPrice`
  (incl. a FangedSerpentAM trio's Motivator 261, the old quirk); a REAL save / reload: eight saved units (five NULL
  rows re-priced 58→38, 36→29, 342→57, 29→15, 174→25; a stored 57 untouched; a stored 25 re-priced to 29; a
  Helmsman untouched), each lump's other 5 points kept, each notice line exact, the HK Motivator refunded its
  enhancement-era 36; and the real `submitSavedFleet` storing 44 for a 9-craft Dogfighter, 47 for a Helmsman and
  NULL for an ordinary row, reloading with no notice. Mutation-tested: 15 server breaks, all caught.
- `officersClientHarness.js` 100 / 0: the same tables through `officers.flightCost`, `standardFlight`, `priceNote`,
  `confirm.enhRowCost`. 6 client breaks, all caught.
- `enhancementsHarness` 267 / 0 (fingerprint unchanged: ships only); `enhancementsDifferential` 443 / 0;
  `fvbuild -Check` all passed (replay corpus 146 / 0).
- Real site (`officersE2E/`): `lobby4` 35 / 35 (notes "per 6 craft"; Thunderbolt Dogfighter 520 / 260 / 87 at 6 / 3 /
  1, Missileer 556 / 278 / 93, bought / copied at 3 = 38, edited to 6 = 76; Aurora Evader 57 / 29; Motivator 29),
  `docs` 16 / 16, `lobby` 25 / 25, `lobby3` 19 / 19.

**Deploy:** ⚠️ apply `db/savedOfficerPrice.sql` on live BEFORE deploying - the new save names the column (a save
fails without it) and the new load selects it (a load would come back with no enhancements). Safe to apply while
the old code runs: it never names the column, so its rows stay NULL and read as Stage 4 prices.

**Observations, not changed:**
1. The same SuperHeavyFighter craft bug prices EVERY fighter enhancement on those five hulls off six craft
   (Enhancements.php's `instanceof SuperHeavyFighter` branches), e.g. a FangedSerpentAM's enhancements cost 6x.
2. §9.7 observation 2 now covers every fighter officer: HangarOps' `shareWithCraft` re-derives his price at the
   row's CURRENT size, so a 2-craft fragment's Dogfighter counts 10 (not the 29 paid at six) when it splits again
   (the harness's launched / fragment split is 32 / 21). Fleet-list value only; the total is unchanged.
3. `tac_saved_list.points` is stored, so the saved-fleet dropdown shows a re-priced fleet's OLD total until it is
   saved again (as with §4.7.1's per-system re-pricing).
4. No local saved fleet holds an officer, so the live data path is proved only by the harness's rolled-back fixtures.

### 9.10 Refinements round 3 (user, 2026-10-15): the Scavenger's save widened; the Technician rebuilt from his full rules

**Rulings.** The Scavenger's once-per-game save also covers **Scanners and Engines**. The Technician: the user
supplied his full rules text - the Stage 3 build (best of three, from a summary) was "simplified and ultimately
wrong". All three skills apply together:
- **Improved Power:** +2 power, and −2 on any critical roll involving the reactor;
- **Efficient Transfers:** a system shut down for extra power gives 1 more point than usual - one system a turn, and
  never one with no power requirement;
- **Improved Circuit Breakers:** a destroyed system gives back 33 % of its power requirement (fractions dropped, the
  transfer point not included) - "a destroyed battle laser would provide 2 points".

Asked and ruled: **every** destroyed system counts, added together (not only the largest); a system **forced
offline by a critical** (a cooldown, Forced Offline) returns its usual power but does not earn the transfer point -
only a player's switch-off does.

**Files.** `Officers.php` (`isSavedSystem`; `TECH_POWER`, `TECH_CRIT_MOD`; `applyTechnician` also moves the
reactor's `critRollMod`; `technicianBonus` sums), `officers.js` (`reactorBonus` sums; new `efficientTransferBonus`;
the two summaries), `power.js` (`getReactorPower` adds `officers.efficientTransferBonus`), docs (the Technician and
Scavenger cards). No offer changed and no new class.

**As built:**
- **The save.** `Officers::isSavedSystem` = C&C, Reactor, Scanner (ELINT and the other subclasses), Engine,
  Structure. Nothing overrides `ShipSystem::assignDamageReturnOverkill`, so every scanner and engine passes the hook.
- **Output.** `technicianBonus` = 2 + Σ ⌊powerReq / 3⌋ over the systems destroyed before this turn
  (`isDestroyed($turn - 1)`: a Self Repair revival stops counting, pre-battle damage counts on turn 1, a system lost
  with its Structure block counts, the reactor itself never). "33 %" is read as a third: the rules' Battle Laser
  (powerReq 6, the value on 74 of 77 hulls) gives 2, where 33 % would drop to 1.
- **−2.** `$reactor->critRollMod += TECH_CRIT_MOD`. `testCritical` reads it, so it also eases his own kill roll, which
  is that same roll "including any modifications" (D3). Not published: no client display reads a system's
  `critRollMod`.
- **Efficient Transfers is client-only**, like the whole power balance (no server twin, §2).
  `officers.efficientTransferBonus(ship)` = 1 while he serves and at least one undestroyed system with powerReq > 0
  is offline this turn and not `shipManager.power.isForcedOffline`. In game only (the server's list, so own team;
  the lobby switches nothing off). Added at the end of `getReactorPower`, before the docking-bay share, so the commit
  gate, the reactor icon and the surplus notice all see it. No switch-on gate reads the balance, so switching the
  only system back on just drops the point, and the commit gate sees the true deficit.

**Verified.**
- `officersHarness.php`: the Technician section rewritten - 2 + a third for powerReq 9 / 8 / 7 / 6 / 12 / 2; three
  systems (9, 6, 5) = +8; this turn not counted; revived not counted; pre-battle +6; killed: no power, no modifier;
  the −2 through a real `testCritical` + `rollKills` (a roll of 20 without him reads 18 and he lives; 22 reads 20 and
  he dies). The save: scanner and engine (his own post) added to the key-systems loop, and an ELINT scanner (Balosian
  Athasa) through the real `assignDamageReturnOverkill`.
- `officersClientHarness.js`: the lobby `reactorBonus` tables and three destroyed systems; Efficient Transfers through
  the real `power.js` (+1; none off 0; three off still 1; powerReq 0, destroyed, forced, switched off last turn only,
  killed, no Technician, enemy, lobby all 0; a forced one beside a player's switch-off 1; `getReactorPower` +1).
- `enhancementsHarness`: the Technician cases' reactor lines now read `crit=-2` (39 lines: 13 hulls × `tech`,
  `tech+rea+poor1`, `all5`), checked line for line before re-recording (§9.11).
- Replay corpus: **game 4461** (the user's Stage 3 test game) moved, as it should: Hyperion reactor 2 → 7 (+2, two
  Heavy Lasers 2 + 2 - one lost with its block - and a Medium Pulse 1), G'Quan 2 → 8 (+2, two Heavy Lasers, two Energy
  Mines 1 + 1). Checked by hand against a real load, then accepted with `record --games=4461` merged back into the
  manifest (the narrowing trap): 146 / 0.
- `lobby3.mjs`: the pre-battle check now expects 2 + a third (+4 for a Heavy Laser).

**Observations, not changed:**
1. The breakers are uncapped, as the text reads: a ship that loses many powered systems gains a third of each.
2. Efficient Transfers is advisory like the rest of the balance: the server trusts the power rows it is handed.

### 9.11 Stage 6: Expert Navigator (2026-10-15)

**Rulings (user, 2026-10-15, §6).** The rules text: turn cost one level lower on the ladder 2, 1.75, 1.66, 1.5, 1.33,
1.25, 1.0, 0.83, 0.75, 0.66, 0.5, 0.33, 0.25, 0.16, 0.1 ("the existing Hyach Maneuvering Specialist already has a
similar function"); pivot and roll costs −1 (minimum 1; a cost of 0 gains nothing); accel/decel −25 %, fractions kept
until the full cost is determined and then rounded up (a cost of 2 becomes 1.5: one change still costs 2, two cost
3); 7 % of the base cost, dropping fractions; post C&C. Asked and ruled: **turn delay unchanged**.

Built with defaults, stated here: a cost of 99 or more is a hull's "cannot" (a base's turn 99, a 999 pivot) and is
left alone; a turn cost off the ladder drops to the next level below it (Gemini AM's 0.8 → 0.75; the two hulls at 3
are Ancient); 0.1 stays 0.1. The lowered costs are **published to every viewer**, the 2026-10-10 default for a
displayed stat (§4.6 had drafted own-team only).

**Files.** `Officers.php` (`OFF_NAV`; `roundDown`, `price(..., $down)`, `shipPrice`; `$turnCostLadder`, `NAV_CANNOT`;
`applyNavigator`, `navigatorTurnCost` - since renamed `turnCostLevelDown`, see the follow-up - `navigatorThrustCost`,
`navigatorAccelCost`), `officers.js` (the registry row
with `adjust`; `TURN_COST_LADDER`, `navigatorStat`; `displayStat` runs an officer's `adjust`), `movement.js`
(`getSpeedChangeCost`, read by `canChangeSpeed` and `changeSpeed`), `ShipNotesPanel.js` (Ship Stats' turn, accel,
pivot and roll rows through `statOf`), docs (the Navigator card; About Officers' list of public numbers). Statics and
bundles rebuilt; no new class, so autoload is unchanged.

**As built:**
- **Price.** 7 % rounded **down**, the only officer whose text says so: Omega 925 → 64 (65 rounded up). `rowPrice`
  (saved fleets, refunds) goes through the same `shipPrice`; the lobby reads the offer tuple's price, so there is no
  client mirror.
- **Effects**, in `apply` (registry order: after the Scavenger): turncost one level down (compared in hundredths, so 1
  and 1.0 are one level); pivotcost and rollcost max(1, x − 1) (the five 1.5-roll hulls → 1); accelcost × 0.75 kept
  fractional (a whole cost × 0.75 is always a quarter, exact in floats; PHP gives 4 → int 3). `publish` sends the four
  fields while he serves, as Hyach's Maneuvering Specialist publishes turncost.
- ⭐ **The accel rounding lives in ONE client helper**, `shipManager.movement.getSpeedChangeCost(ship)`: the k-th speed
  change of a turn costs ⌈k·c⌉ − ⌈(k − 1)·c⌉, so a turn's n changes cost ⌈n·c⌉ together (2, 1, 2, 1… at 1.5; 3, 2, 2, 2
  at 2.25). A whole cost returns itself, so no hull without him changes. It holds because speed changes are always
  undone from the LAST row (`deleteSpeedChange`) and deployment speed is not a row. `canChangeSpeed`'s thrust gate asks
  it too (a second change at 1.5 needs 1 thrust, not 1.5).
- **Server.** Nothing recomputes a speed change's cost (the thrust validator checks the submitted `requiredThrust`),
  and `Movement::doStuckEngine`'s ⌊thrust / accelcost⌋ is already exact for a fractional cost (⌈n·c⌉ ≤ T ⟺ n·c ≤ T
  for a whole T).
- **Lobby.** Ship Stats shows the costs at read time through `officers.displayStat` → `navigatorStat` (MIRROR PAIR with
  the three PHP functions); a C&C destroyed in the pre-battle editor brings the hull's own costs back. In game the
  published fields are read as they are.

**Verified.**
- `officersHarness.php` **417 / 0** (374 with `--no-db`; 35 new across §9.10 and §9.11): the offer at 64 and
  `rowPrice`, no C&C / Ancient / terrain / flight → no offer, the three functions' tables, the Omega's costs (turn 0.83,
  pivot 2, roll 1, accel 2.25, turn delay 1) against its twin, disabled last turn vs this turn, the payload per viewer
  (an enemy reads the four costs and survives `json_encode`; a disabled Navigator publishes nothing), and the stuck
  engine's arithmetic.
- `officersClientHarness.js` **133 / 0** (33 new): the registry row and order, the `navigatorStat` tables, the lobby
  `displayStat` and `ManoeuvreStats` rows, the pre-battle editor, no double lowering in game, and the real
  `movement.js` `getSpeedChangeCost` (1.5, 2.25, 0.75, whole costs, last turn's rows, other row types).
- **Mutation-tested**: 32 breaks across §9.10 and §9.11 (15 server, 14 client, 3 against the differential), 31 caught.
  The miss was an int-normalising line in `navigatorAccelCost` that PHP's own `/` made dead: removed.
- `enhancementsHarness` **268 / 0** after re-recording; the diff, read as a unified diff (the check compares by
  position, so one inserted line cascades), is exactly two new cases (`nav`, `nav+elite1`) on the 13 hulls, 13
  `OFF_NAV` offer lines, and §9.10's 39 reactor lines. Old copy: `fingerprint.before-officers-stage6.txt`.
  `enhancementsDifferential` **469 / 0 differing** (26 new cases, none skipped); it now compares every ship's turn,
  turn delay, pivot, roll and accel cost as shown.
- `fvbuild -Check`: all three pass (replay corpus 146 / 0, after §9.10's game 4461).
- Real site (`officersE2E/`): `lobby3` **24 / 24** (adds the Navigator: **+** adds 64; Ship Stats 0.83 / 2.25 / 2 / 1
  with turn delay 1.00; his line on the C&C; the pre-battle editor bringing back 1.00 / 3 / 3 / 2), `docs` **18 / 18**
  (eight Ship Officers cards; the Technician's three skills; the save's scanners and engines; the Navigator card),
  `lobby` 25 / 25, `lobby4` 35 / 35. Not run: `game.mjs` (game 4457 is surrendered, §9.8); the in-game movement path is
  the client harness's, on the real `movement.js`.

**Observations:**
1. ~~Hyach Maneuvering Specialist + Navigator: the specialist's halving table missed every value a Navigator had
   lowered.~~ **Ruled and built the same day** (follow-up below).
2. Ship Stats and the ship tooltip show a fractional accel cost as it is (2.25); each speed change in the movement
   window asks for its rounded share.

**Follow-up (user, 2026-10-15): the Hyach Maneuvering Specialist on the Navigator's ladder.** "The Specialist's Turn
Cost steps should be changed to mirror the Expert Navigator steps so they become compliant with one another. Applying
Navigator first as a passive buff, then Specialist later as an active buff is correct." The ruling covers turn cost
only, so the Specialist still **halves turn delay** with its own table.
- **One step, two users.** `Officers::navigatorTurnCost` is renamed **`Officers::turnCostLevelDown`** (MIRROR PAIR
  `officers.turnCostLevelDown`, which `navigatorStat` now calls). `HyachSpecialists::onIndividualNotesLoaded`
  (baseSystems.php) replaces its 0.5 / 0.66 / 1 / 1.5 halving table with it. The order is already the ruled one: the
  Navigator in `onConstructed` (`Officers::apply`), the Specialist at `onIndividualNotesLoaded`, which runs after it.
- **Client.** `HyachSpecialists.doUse` (model/system/baseSystems.js) previews with `officers.turnCostLevelDown` on the
  `ship.turncost` the server published (the Navigator's step already in it); `doDecrease` undoes it with
  `officers.turnCostLevelUp` (new: the lowest level above). That inverse is exact for everything the Specialist meets:
  the Specialist hulls' turn costs are 0, 0.5, 0.66, 1 and 1.5, and the level a Navigator puts each at is on the
  ladder above 0.1.
- **Effect on every Specialist hull**, Navigator or not: 1 → 0.83 (was 0.5), 0.5 → 0.33 (was 0.25), 0.66 → 0.5 (was
  0.33), 1.5 → 1.33 (was 0.75); with a Navigator, two levels (1 → 0.83 → 0.75). Tooltip text (both lists in
  `setSystemDataWindow`) and the Hyach entry in Factions & Tiers say "Turn Cost one level lower, halves Turn Delay".
- The old client undo table also cascaded (0.25 → 0.5 → 1, so undoing a 0.5 hull's Specialist left it at 1); the
  replacement removes that.
- **Verified:** `officersHarness` 426 / 0 (383 with `--no-db`; nine new: four Specialist hulls alone and with a
  Navigator, their turn delay still halved, and the payload); `officersClientHarness` 138 / 0 (five new, including the
  REAL `doUse` / `doDecrease` loaded from baseSystems.js: 1 → 0.83 and back, a Navigator's 0.83 → 0.75 and back, 0.5 →
  0.33 and back); 6 mutations, all caught; enhancementsHarness 268 / 0, differential 469 / 0, `fvbuild -Check` all pass
  (replay 146 / 0 - no local game has an `allocated;Maneuvering` note at all), `docs` 18 / 18.

### 9.12 Faction rules: which factions may buy an officer, and at what price (user, 2026-10-15)

**Request.** "The Scavenger should be discounted for certain Factions. 15% for Descari, 20% for Balosians, Raiders and
Llort of the ship's base value, everyone else pays the normal 25% cost. Other future officers will also have
faction-specific price changes so good to have a robust system ... easy for other devs to add their own Custom Factions
to an Officer's availability (adding a quick 'how to' in ReadMe file). Expert Geneticist should be restricted to Dilgar
Imperium only. The Matter Weapon Expert should be Grome Autocracy only." Then the Jump Officer (§9.13) added the third
shape: "any race except Raiders, Belt Alliance".

**Built.** One table, `Officers::$factionRules`, keyed by officer id, three keys: `only` (offered to these factions
only), `never` (never to these), `pct` (this faction's price in place of the registry's). Faction = the hull's
`$faction` string, exact. Entries: `OFF_GENE` only Dilgar Imperium; `OFF_MWE` only Grome Autocracy; `OFF_SCAV` pct
Descari Committees 15, Balosian Underdwellers 20, Raiders 20, Llort 20; `OFF_JUMP` never Raiders, Belt Alliance.
- **Availability** is one test in `isEligible` (`factionAllows`), so it answers both the offers (`addOffers`, hence the
  static blueprints) and a doctored row at load (`pickPosts`), exactly as `needs` does. The Matter expert keeps his
  `needs` (a Matter weapon aboard) beside the faction rule: a Grome hull without one gains nothing from him.
- **Price**: `pctFor($ship, $id)` feeds `shipPrice` (offers, `rowPrice`), and `craftPrice` / `flightPrice` for a
  fighter officer, so a future fighter-officer discount needs nothing more. The registry's rounding still applies
  (the Navigator's round-down).
- **No client mirror, on purpose.** The offer list and its prices reach the lobby in the static blueprint, and a lobby
  row exists only for an offer, so the table is the one place a developer edits. `officers.js` says so.
- **Saved fleets** need nothing new: `loadSavedFleet` already refunds an officer the unit is no longer offered and
  re-prices one whose price moved (§9.9). One change: `legacyPrice` (a NULL `enhvalue` row, saved before rows kept
  their price) now prices a ship officer at the REGISTRY pct - every such row predates faction prices, so a Descari
  Scavenger saved then was paid 25%.
- **README.md** gained "Officers: faction availability and prices (10.2026)": the three keys, adding a custom faction to
  a list, the exact-name rule, `enhancementOptionsDisabled` for one hull, `fvbuild -Statics`, and what happens to saved
  fleets and running games.

**Effect, measured.** Statics: Geneticist offers 1,820 → 39 (the Dilgar file), Matter Weapons Expert 416 → 11 (the Grome
file); the Rulnar's Scavenger 157 → 94, Areko's (Raiders) 119 → 95. ⚠️ **A running game loses a refused officer at its
next load, points spent**: locally games 4461 (an EA Hyperion's Geneticist, an EA Artemis's Matter expert) and 4467 (a
Commune's Matter expert) moved in the replay corpus - checked by shape (only those officers' published profiles, to-hit,
fire control and list entries; the Hyperion's shots +5, shots at it -10) and re-recorded with `record --games=4461,4467`
merged back into the manifest (146 games).

**Observations, not changed:**
1. "Raiders" is the faction string of the 105 Raiders hulls only. The custom "Escalation Wars Blood Sword Raiders" and
   "Escalation Wars Chouka Raider" factions are not included in the Raiders entries; add them to the table if they
   should be.
2. There is no per-HULL opt-in (a single non-Dilgar ship allowed a Geneticist): only faction lists, plus the existing
   per-hull opt-out. The Evangelist (§3.3, Corillani CPN/OSF hulls only) will need a hull-level opt-in when he is built.

### 9.13 Stage 7: Expert Jump Officer (2026-10-15)

**Rules text (user, 2026-10-15).** "He permits his ship to open a jump gate up to 5 hexes away (not 4 hexes) from his
ship, and can hold it open for one turn longer than normal. If the jump engine takes damage, he reduces the chance of a
catastrophic explosion by 20%. For example, if exactly half the boxes of the jump drive are destroyed, the chance of
detonation would be 30%, not 50%. The jumpdrive still cannot be used if all boxes are lost, however. The jump officer
provides a -1 shift to the vortex scatter chart (as listed in the modifiers) if his ship is the one opening the vortex.
He can cancel 60 degrees of unintended facing change caused by a vortex shift (this does not allow him to alter the
facing from its original position, however). Any initiative penalty produced by scatter is reduced by 20 for his unit
only. Note that this is not an initiative bonus, only a reduction of an initiative penalty! Location: Jump drive. Cost:
5% of the ship's base value. Availability: Any race except Raiders, Belt Alliance, and any ship that does not possess a
jump drive."

**Built with defaults, stated here and not yet ruled** (§6 has them as the open questions):
- Range +1 on whatever the drive has: 4 → 5, and 6 → 7 with a Jump Accelerator. Like the Accelerator's +2, it also
  lengthens the distance the holder may stray before the jump point closes (`getVortexClosureReason` reads `$range`).
- The −20 comes off the chance the roll is made against, so AFTER the Accelerator's opening-turn doubling (half the
  boxes lost, opening with an Accelerator: 100% → 80%), and never below 0. "Cannot be used if all boxes are lost":
  a drive with no box left gets no reduction (he is disabled from the next turn anyway, but serves the turn it falls).
- The facing cancel turns the DOORWAY one step back towards the declared facing, so every unit riding it comes out
  facing that way; a random facing 180° off comes back 60° (to 120°). The initiative relief is each arriving unit's OWN
  officer ("his unit only"): a rider through his doorway keeps its full penalty, a rider with an officer of its own has
  it reduced through anyone's doorway.
- His post is the first drive that opens jump points: never a legacy drive (Trek nacelle, BSG FTL, Star Wars
  hyperdrive, The System's, every Ancient's) nor a gate's - plan §4.3. So a custom faction whose only drive is a legacy
  one is not offered him. No officer-eligible hull carries two vortex drives (measured over every hull), so "his ship's
  drive" and "the drive he sits in" never disagree.

**Files.** Server: `Officers.php` (`OFF_JUMP`, post `jump`, `applyJumpOfficer`, `exitScatterShift`, `cancelsFacingShift`,
`arrivalPenaltyRelief`, the `JUMP_*` constants), `baseSystems.php` (JumpEngine: `$jumpOfficer`, `applyJumpOfficer`,
`hasJumpOfficer`, `getMaxVortexTurns`, `getJumpFailureChance`; the three failure rolls, `getVortexAge`,
`getVortexClosureReason`, `stripForJson`, `getExitDeviationModifier`, `openExitVortex`), `ShipClasses.php`
(`getReinforcementArrivalIniModifier`). Client: `officers.js` (registry row, post `jump`, `jumpRangeBonus`,
`adjustDataDisplay` for the drive's Range row and "within N hexes"), `model/system/baseSystems.js`
(`JumpEngine.canMaintainVortex` reads the payload's cap), `SystemInfo.js` (the Special text goes through the same
data hook). Docs: the Jump Officer card, three cards' faction lines, two About Officers lines. Statics and bundles
rebuilt; no new class, autoload unchanged.

**As built:**
- **The drive's rules live on the drive.** `Officers::apply` calls `JumpEngine::applyJumpOfficer` on his post while he
  serves (the Jump Accelerator's shape): `$range` +1 and a protected flag. `getMaxVortexTurns()` = 4, 5 with the flag,
  read by the closure ("five-turn limit reached"), `getVortexAge` and the payload's `vortexMaxTurns`; a gate's
  programmed hold and a Vorlon's open-ended hold never ask it.
- ⭐ **ONE failure helper**, `getJumpFailureChance($ship, $gamedata, $opening)` - boxes lost %, Ancient halving, the
  Accelerator's doubling on an opening turn, the officer's −20, the Walker zeroing, in the order the three sites always
  used - now serves `openVortex` (the quoted chance), `rollVortexJumpFailure` and `doHyperspaceJump`. Byte-identical
  without him: the jump harnesses' outcomes are unchanged (`walkersStage15Harness`'s two source-text checks, which
  looked for the halving inside each roll, now follow it into the helper).
- **The unit's rules ask Officers.** `getExitDeviationModifier` adds −1 when the opener's officer serves (behind
  `$officersPresent`); `openExitVortex` steps the facing back after every die is drawn and before the scatter note and
  the log, so the note - and the arrival penalty read from it - records the doorway's real facing;
  `getReinforcementArrivalIniModifier` adds +20 to a penalty and caps it at 0.
- **Payload.** `JumpEngine::stripForJson` sends `range` and the rebuilt `data` (Range row, "within 5 hexes") to every
  viewer while he serves - a displayed stat is public (the Accelerator's case sends the same two). In the lobby both are
  read-time previews (`officers.jumpRangeBonus`, `adjustDataDisplay`); the client's Maintain toggle cuts off at
  `spawned + vortexMaxTurns - 1` (4 when absent: unchanged for everyone else).

**Verified.**
- `officersHarness.php` **502 / 0** (446 with `--no-db`; 76 new across §9.12 and §9.13): the faction table's names
  against the factions that exist; `only` / `never` / `pct` on real hulls (AthraskalaAM, GromeTrokanAM, Altarian, Dux,
  BALCVCarrier, Rulnar, athasa, erlorra, Omega); `legacyPrice`; doctored rows; the Jump Officer's offers (a hull
  without a drive, Ancients, OSATs, terrain, a drive marked legacy or gate), post and order; range 4/5/7, disabled,
  killed; the real `getVortexClosureReason` (cap and distance); the payload per viewer; the failure table through the
  helper and a real `rollVortexJumpFailure` with a d100 between the two chances; the scatter modifier; the real
  `openExitVortex` (a 1d10-band shift cancelled, a 2d10+2 random facing 180° → 120°); the arrival penalty (−40 → −20,
  −10 → 0, rider vs opener both ways); and a real saved-fleet reload (Descari / Llort Scavengers re-priced with notices,
  a Dilgar Geneticist untouched, an EA Geneticist refunded 47). The Geneticist and Matter sections moved onto a Dilgar
  hull and a Grome-flagged Altarian, plus a real GromeTrokanAM.
- `officersClientHarness.js` **152 / 0** (14 new): the registry row, the `jump` post past a legacy drive, the Range /
  Special preview (with an Accelerator, destroyed pre-battle, in game), the REAL SystemInfo render, the REAL
  `JumpEngine.canMaintainVortex` at caps 4, 5 and absent.
- **Mutation-tested**: 38 server/client breaks, all caught (three first crashed the harness - the error handler makes a
  null access fatal - so those checks were made null-safe and re-run: caught by assertion); 4 differential breaks
  caught.
- `enhancementsHarness` **269 / 0** after re-recording: the unified diff was exactly 12 Geneticist and 2 Matter offers
  gone (Tratharti, a Dilgar hull, keeps its Geneticist), Areko's Scavenger 119 → 95, 9 Jump Officer offers, the Geneticist
  cases' unmoved stats, 13 hulls × 2 new cases (`jump`, `jump+jacc`), and two new hulls (AthraskalaAM, GromeTrokanAM).
  Old copy `fingerprint.before-officers-stage7.txt`. `enhancementsDifferential` **526 / 0 differing** (the drive's range
  and tooltip now compared as shown, through the lobby's hook; `jump` cases compared on 10 hulls).
- `fvbuild -Check`: all three pass (replay 146 / 0 after 4461 and 4467, §9.12). Jump-point harness sweep (23 PHP, 14 Node)
  identical to before but for the officers' and enhancements' own counts.
- Real site (`officersE2E/`): `lobby3` **34 / 34** (the Omega's Jump Officer at 5%, insignia on the drive, Range 5 and
  "within 5 hexes", back to 4 with the drive destroyed pre-battle; the Geneticist on a Dilgar hull, the Matter expert
  on a Grome hull's railgun; none for the Altarian or the Raiders Dux; the Descari Scavenger at 15%), `docs` **21 / 21**,
  `lobby` 25 / 25, `lobby4` 35 / 35. `game.mjs` not run (4457 surrendered, §9.8).

**Observations, not changed:**
1. Not tested in a real game: opening a jump point 5 hexes out, holding it a fifth turn, and a reinforcement exit with
   him aboard. Worth a play test (Initial Orders declaration at 5 hexes; Maintain still offered on the fifth turn).
2. The −1 scatter shift and the facing cancel are visible to the opponent only as where and how the doorway formed - the
   log quotes the modified roll, as it does for every other modifier.
