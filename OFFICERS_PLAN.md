# Officers — Implementation Plan

Status: **Stages 0–2a committed (`7800ab827`), Stage 3 committed (`1714c3651`), Stage 4 committed
(`ab0025aba`, `4738173cc`; the Motivator re-priced to 50 % in `32c9f2a11`); Stage 5 committed (`85c30272c`,
§9.8); fighter officer prices scaled by flight size + saved fleets re-priced committed (`75dc7d68d`, §9.9 - ⚠️
db/savedOfficerPrice.sql before deploying); the Technician rebuilt from his full rules and the Scavenger's save
widened (§9.10), and Stage 6, the Expert Navigator (§9.11), committed (`6dd2f740e`). Faction rules for officers (§9.12)
and Stage 7, the Expert Jump Officer (§9.13), committed (`6996071b9`). Stage 8, the placed officers - Expert Gunner (core)
and the weapon-mounted Matter Weapons Expert - and the lobby system menu opening beside the ship window (§9.14),
committed (`9ecfd16d4`). Stage 9, the Expert Coordinator, with three system-menu refinements (§9.15), committed
(`0436100e3`); the Expert Analyst moved to the Hard tier the same day. Stage 10 - faster reloads, the Expert Gunner's
once-per-battle abilities and MOVING officers between posts in game (§9.16) - committed (`eae0de604`). Stage 11, the first
Hard officer, the Expert Laser Technician (§9.17) - committed (`4f3a359cc`). Stage 12, the Expert Plasma Scientist, an
officer the user added beyond the original 25 (§9.18) - committed (`a80430710`). Stage 13, the Expert Security Officer,
a second added officer (§9.19) - committed (`f17a680b8`). Stage 14, the Expert Religious Leader, a third added officer
(§9.20), and Stage 15, moving officers in Fleet Selection (free) with the Religious Leader's placed twin, and every officer on
a post destroyed by pre-battle damage refunded (§9.21) - committed (`68f1c82b3`). Stage 16, the Expert Surge Officer (Ipsha),
with FV's EM hardening rebuilt to the tabletop rules first (§9.22) - committed (`318058ac0`). Stage 17, the Expert Ion Technician
(Cascor), whose thrust boost rides the generic boost the way the Hypergraviton Blaster's does (§9.23) - committed
(`a83c3d6ae`). Stage 18, the Expert Pilot, the first officer bought as a COUNT - up to four on one flight, one per craft (§9.24) -
BUILT 2026-10-19, uncommitted.** Written
against `dab951bf5`. The user
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
supplied the Jump Officer's rules text (Stage 7, §9.13). On 2026-10-16 they supplied the Expert Gunner's and the
Matter Weapons Expert's full rules and ruled Stage 8's four questions (§6, §9.14), and asked for the lobby's system
menu to stop covering the ship window. Later that day they supplied the Expert Coordinator's rules text (Stage 9,
§9.15), moved the Expert Analyst into the Hard tier ("more complex than I first described"), and asked for three system
menu refinements: no gap beside the ship window, the edited system outlined, and an Officers section bar. Later that
day they began Stage 10 with three simplifications of the Gunner's once-per-battle abilities (all automatic) and a new
feature, moving officers between posts in Initial Orders, and ruled the four questions it raised (§9.16). On 2026-10-17
they supplied the Expert Laser Technician's rules text with a design note ("laser weapons essentially become boostable
with this Officer at 1 power = 1 damage, with no maxBoostLevel but a cap at the point of damage") and ruled the four
questions it raised (Stage 11, §9.17). The same day they added a 26th officer, the Expert Plasma Scientist, with his rules
text, and ruled the three questions he raised (Stage 12, §9.18). Later that day they added a 27th, the Expert Security
Officer, with his rules text and a design note (his "shift by up to 2, as the defender chooses" is a fixed 2 in the
defender's favour), and ruled the four questions he raised (Stage 13, §9.19). Later that day they added a 28th, the Expert
Religious Leader, with his rules text, and ruled the four questions he raised (Stage 14, §9.20); the same request sorted the
Ship Officers and Fighter Officers cards of the Ship Options document alphabetically. Then they asked for officers to be
moved freely in Fleet Selection - the Religious Leader to any viable system, and the weapon-placed officers when the player
changes their mind - with a Move button that keeps the System Enhancements menu's format, ruled such a move free, and re-ruled
QX5: any officer whose post pre-battle damage destroys is refunded (Stage 15, §9.21). On 2026-10-18 they supplied the Expert
Surge Officer's rules text; the first ruling round showed FV's EM hardening to be a simplification (treated as Advanced Armor,
plus a flat -1 on every critical), so they supplied the tabletop hardening rules and asked for those to be built first, the
Ring Base hardened, and the officer on top - ruling five questions on the way (Stage 16, §9.22). Later that day they supplied the
Expert Ion Technician's rules text, pointing at the Hypergraviton Blaster's thrust boost as the way to build his, and ruled the three
questions it raised: the boost repeatable (then, once built, re-ruled to one level per gun), the discharge of a boosted gun that does
not fire built, and a Sub Reactor counted as the reactor (Stage 17, §9.23). On 2026-10-19 they added the Expert Pilot with his rules
text and notes (up to four, two for Raiders, each in his own craft; his free jinking level beyond the limit), and the Spark Field
clauses of the Evader and the Pilot, and ruled four questions: pilots ARE the flight's one officer, priced by the §9.9 scaled rule,
the Pilot's Spark Field relief automatic on a turn his flight neither jinks nor fires, and "Raiders" the Raiders faction (Stage 18,
§9.24). Each officer's own rules questions still only need ruling when that officer's stage starts. Next step: the
rest of the Hard tier, one officer at a time.

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
| Expert Geneticist | Expert Coordinator ² | Expert Software Engineer |
| Expert Dogfighter ² | Expert Gunner, core ³ | Expert Ion Technician |
| Expert Missileer ² | Matter Weapons Expert (weapon) ³ | Expert Surge Officer |
| Expert Evader ² ⁴ | Loading time −1, shared ⁵ | Expert Ballistic Officer |
| | | Expert Anticipator |
| | | Expert Gunner, once-per-battle abilities |
| | | Expert Electrician ² |
| | | Expert Analyst ⁶ |

¹ On the "best of the three, automatically" reading (§6, Technician), as Stage 3 built him. His full rules
(§9.10) make all three cumulative, which adds a client-only term in power.js: Easy-Medium. ² After the fighter-officer framework
(Stage 4). ³ After the placed-officer framework (Stage 8). ⁴ If always on; Medium as a per-turn
toggle. ⁵ Medium-Hard; one build serves both Gunner and the weapon-mounted Matter Weapons Expert. ⁶ Moved from
Medium on 2026-10-16: the user found his full rules "more complex than I first described" (§3.3).

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
| **D9** | **RULED.** **Ships: one officer of each type**, several different types per ship, "except as noted" (Gunner up to 3 / 2 / 1 by size; Breaching Expert up to 20 % of pods). **Flights: one officer in total** *(Stage 18, §9.24: up to four Expert Pilots, bought as a count, are that one officer)*. **Never on Ancient hulls (factionAge ≥ 3), OSATs (mines extend OSAT) or terrain.** Bases, LCVs and everything else only where the post exists, so a base without a jump drive is never offered a Jump Officer. Faction gates use the faction string. | User ruling (QX3, QX4) and the rulebook: a unit "may not benefit from more than one expert officer of the same type (except as noted)". The flight cap makes a flight's officer rows mutually exclusive in the buy dialog (§4.10). ⚠️ FV_factions.txt:438 still promises Shadow ships "enhancements relating to special officers"; that line now contradicts the ruling. Corillani CPN and OSF exist only in `$notes` prose ([VostovaAMCPN.php:15](source/server/model/ships/corillani/VostovaAMCPN.php#L15)), so the Evangelist needs a per-hull opt-in (`enhancementOptionsEnabled[] = 'OFF_EVAN'`), which is a contributor-area edit. |
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
| ~~**Expert Analyst**~~ | *moved to Hard, 2026-10-16 (§3.3)* | | |
| **Expert Coordinator** | flight, 50 % | A combat pivot costs 1; the −1 to-hit only from 180° | The cost lives in movement.js `doPivot` ([:1436](source/public/client/movement.js#L1436)) and the penalty in [weapon.php:1872](source/server/model/weapons/weapon.php#L1872) plus its client twin. "180°" needs net rotation counted, not pivot rows. *Built in Stage 9 (§9.15).* |
| **Expert Gunner, core** | a chosen weapon, 10 %, max 3 / 2 / 1 by size *(rules text 2026-10-16: 5% doubled off the Yolu, max 1 / 2 / 3 / 4 for medium / heavy / capital / enormous - §9.14)* | +1 fire control; +1 per die through `Dice::$perDieBonus` | The effects are Easy. The cost is that he is the first **placed** officer: a registry entry, a ship-wide cap in sanitise and on the client, the menu sub-heading, and the insignia instead of the ✦. *Built in Stage 8 (§9.14).* |
| **Matter Weapons Expert, weapon** | a chosen weapon | Dies on any penetrating damage; loading −1 (next row) *(rules text: disabled by any damage, killed when destroyed - §9.14)* | A placed officer plus the "any damage" kill rule. *Built in Stage 8 (§9.14), loading in Stage 10 (§9.16).* |
| **Loading time −1** (Gunner and the weapon-mounted MWE) | — | — | Medium-Hard. `loadingtime` is overwritten from the stored loading row on every load ([weapon.php:1189](source/server/model/weapons/weapon.php#L1189)) and from `loadingtimeArray` on every mode switch ([:3350](source/server/model/weapons/weapon.php#L3350)). The client reads it from the blueprint, because `stripForJson` does not send it. Losing the officer mid-charge changes whether the weapon is loaded. One build serves both officers. *Built in Stage 10 (§9.16) without touching `loadingtime`: one more turn of loading at the start of each reload, in the loading row the turn change writes - which the client reads.* |

### 3.3 Hard

| Officer | Why it is hard | The cheap half, if wanted early |
|---|---|---|
| **Expert Quartermaster** | Makes this ship's other enhancements and ammo free, which touches every price display, total and the saved-fleet bucket split; a fleet-% price; a random post; "+10 turn delay" is unclear | −30 initiative; the C&C critical modifier |
| **Breaching Expert** | His post is **one pod inside a flight**, and per-craft ids do not exist in the lobby (WEAPON plan D7). Adds a fleet-wide "20 % of all pods" cap, a change to the capture roll ([ShipSystem.php:951](source/server/model/systems/ShipSystem.php#L951)), and "+1 thrust" for one craft of a flight that moves as one | — |
| **Expert Evangelist** | Fleet-wide state: +1 to hit while he serves, then −10 initiative (QX1) and −1 to hit if he is **killed**, for every ship, with a client to-hit mirror. Also a fleet-% price, a random post, and the CPN/OSF gate exists only in notes prose | — |
| **Expert Laser Technician** | "+1 damage per power point sent, up to +50 %" needs a new per-turn allocation UI, persistence and a cap. *Built in Stage 11 (§9.17) with no new UI or persistence: the user's design note made every laser use the existing BOOST (power entries type 2, the generic boost buttons), so the allocation, its storage and the power balance all came free; what was new is the cap, a per-turn pool, and hiding the boosts from opponents.* | +3 Spinal / +2 Blast / +1 other lasers, through `getBonusDamage` (`SpinalLaser`, `BlastLaser`, `ImprovedBlastLaser`, `LtBlastLaser` exist) |
| **Expert Software Engineer** | Bonus fire-control points spendable in the Firing phase need a new allocation window (`HyachComputer` only allocates in Initial Orders, [baseSystems.php:12862](source/server/model/systems/baseSystems.php#L12862)). Also a "first point of damage ignored" rule in the damage path, a submarine detection range, and a self-contradictory specialist clause (§6, Software Engineer) | — |
| **Expert Ion Technician** | "3 thrust → 1d10+1 on one ion weapon" is a per-turn spend with UI. *Built in Stage 17 (§9.23) with no new UI or storage: the user's note pointed at the Hypergraviton Blaster, whose boost is the generic one priced in thrust - so the Laser Technician's payload pattern with `thrustBoosted` gave the buttons, the thrust balance and the commit check for free; what was new is the discharge of a boosted gun that does not fire, at the turn change.* | +1 per die and +1 flat on Ion-class weapons; +4 on C&C crits; the Rad Cannon's flat 12 and forced crits are Medium |
| **Expert Surge Officer** | Six unrelated hooks: EM damage, combined Surge Cannon shots, capacitor power by ship size, EM-hardening −3, a d6 to resist deactivation, Spark Field −1. *Built in Stage 16 (§9.22): the rules text made it reactor power by ship size; his two hardening clauses needed FV's EM hardening rebuilt to the tabletop rules first (WeaponEM), the bigger half of the stage.* | +1 damage on Electromagnetic-class weapons |
| **Expert Ballistic Officer** | "+1 magazine round per launcher" moves the lobby's ammo limits, which are derived from magazine capacity at offer time. Free missile swaps are lobby ammo pricing | +5 launch range (`range` **and** `rangeArray` — the same mode-switch trap — and sent to the client) |
| **Expert Anticipator** | A free **turn** for a ship in the Firing phase is a new phase-3 movement for ships (today only flights move there, as combat pivots), with masking, validation and animation | Rolling initiative twice is Easy. Re-rolling the lowest die is Medium: a `Dice`-level flag, which the replay harness's own `Dice` stub must mirror ([[project_replay_harness]]) |
| **Expert Gunner, once-per-battle abilities** | A Piercing mode injected into a weapon that lacks one, and +15 % on one called shot, each once per battle and not combinable: firing-mode injection, a persisted "used", and UI. *Built in Stage 10 (§9.16), all three automatic (user simplifications), with moving officers.* | — |
| **Expert Electrician** | Per turn: deactivate the flight's weapons, convert their max damage ÷ flight size into thrust or OB, then a d6 to see whether they stay offline | — |
| **Expert Analyst** | *Moved from Medium, 2026-10-16:* the user found his full rules "more complex than I first described". The Medium reading was a per-load "the viewer's team has a live Analyst" static widening `isDisclosedToCurrentViewer()` (ammo types, hangar contents) and the officer payload, per viewer; two clauses moot in FV (weapon arming already public, [weapon.php:453](source/server/model/weapons/weapon.php#L453); cargo not modelled). Needs his full rules text and its own ruling round | — |

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
| Expert Plasma Scientist *(added 2026-10-17, §9.18)* | `OFF_PLAS` | Engine | 1 |
| Expert Security Officer *(added 2026-10-17, §9.19)* | `OFF_SECU` | C&C | 1 |
| Expert Religious Leader *(added 2026-10-17, §9.20)* | `OFF_RELI` | C&C, movable to any primary non-weapon system | 1 |
| Expert Religious Leader, elsewhere *(Stage 15, §9.21)* | `OFF_RELIP` | a chosen primary non-weapon system | 2 |
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
| Expert Pilot *(added 2026-10-19, §9.24)* | `OFF_PILOT` | one per craft, from the first | 1 (flight), bought as a count |
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
| Mag-Grav Reactor | the first `MagGravReactor` that can be hit (never a `MagGravReactorTechnical`) | Surge Officer (Stage 16: every Ipsha hull has exactly one) |
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
| Gunner, weapon-mounted MWE (Stage 10) | an added Piercing mode (the weapon's mode list and its per-mode fields); a weapon's `turnsloaded` one higher after a faster reload | The added mode: **own team only** (user request 2026-10-16 - it would point at the Gunner), and to anyone else only in the data of the turn it was FIRED (a replay, the printed log, which label the shot by it) or once the game is over; the opponent's fire control list is sent without its entry. Loading: **yes**, public anyway |
| Moving officers (Stage 10) | where an officer is moving (`moving`), the posts he may move to (`targets`), a Gunner's spent abilities and added mode number | Own team only, in the officer payload |
| Security Officer (Stage 13) | `iniativebonus`; the C&C's `critRollMod`; the modifier on boarding rolls against his ship | `iniativebonus`: **yes, to every viewer** (the Helmsman's `publish`). The critical modifier is never sent (nothing displays one). The boarding shift is server-only; the combat log prints a mission roll as Roll(Mod), so an opponent can see a larger modifier than he expected, never who caused it |
| Plasma Scientist (Stage 12) | a Plasma Web's `range` / `rangeArray`; a Plasma Battery's capacity, charge and output | **Yes, to every viewer** (user ruling 2026-10-17: a displayed stat is public). The battery's line and output were sent to everyone already; the web's range is sent while he serves (`webForJson`). His damage bonus and range relief show only as damage dealt |
| Surge Officer (Stage 16) | the Mag-Gravitic Reactor's `output` | **Yes**, as the Technician's: `output` is sent unconditionally. His +1 per EM hit, the hardening he eases and the Spark Field relief show only as their results (damage dealt, criticals rolled, a "shutdown resisted" line in the log - which a hardened ship's own d6 also writes) |
| Ion Technician (Stage 17) | an ion gun's `boostable` / `boostEfficiency` / `thrustBoosted` / `thrustPerBoost`, its boost (type-2 power entries), and the thrust it takes off the engine | **Own team only**, as the Laser Technician's: "Weapons being charged in this manner will be detected (and their strength known) by anything that detects special weapon arming modes, such as the Expert Analyst" - by nothing else until the Analyst's stage. An opponent's payload carries the gun's power entries without its boosts while he is aboard; game over discloses all. His +1s, the boost and the Rad Cannon's improved effects show only as their results; a discharge shows as the gun's loading, which is public |
| Expert Pilot (Stage 18) | a flight's `offensivebonus` (+1 a serving pilot); its jinking (free levels beyond the limit) | **Yes, to every viewer**: the OB through his `publish`, as the Evader's; the jinking shows as the flight's jinking, which is public. Who and how many stay own-team; the Spark Field relief shows only as the damage dealt |
| Laser Technician (Stage 11) | a laser's `boostable` / `boostEfficiency`, and its boost (type-2 power entries) | **Own team only** (user ruling 2026-10-17): no laser is boostable without him, so a boosted one would name him - an opponent's payload carries the laser's power entries without its boosts while he is aboard, until the game is over. His flat bonus and the boost show only as damage dealt, in the log |
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
  **RE-RULED 2026-10-17 (Stage 15, §9.21): any officer whose post pre-battle damage destroys is removed and refunded**,
  track 1 too ("for simplicity"). D5's warning is about putting system enhancements INTO the dialog's buckets; a track-1
  row zeroed together with its own `pointCostEnh` share is exactly what the next Edit would compute anyway
  (`officers.setShipLevelRow`).

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
- *Stage 18 (§9.24): the Expert Pilot is bought as a COUNT (registry `multi`) - up to four, one in each of the flight's first
  crafts, every one an entry of the runtime list - and together they are still the flight's one officer.*
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
| **8** | Placed-officer framework; Gunner core; weapon-mounted Matter Weapons Expert (without loading). **BUILT 2026-10-16** (§9.14) | The cap per size; a Gunner and Gunsights on one gun show ✦ and insignia side by side; "any damage" ~~kills~~ disables (his full rules text; destroyed kills); the destroyed-host refund |
| **9** | Coordinator. **BUILT 2026-10-16** (§9.15), with three system-menu refinements. ~~Analyst~~ moved to the Hard tier (11+) | The halved cost on the row `doPivot` writes and in `canPivot`; the −1 waived short of a full 180, on both ends |
| **10** | Loading time −1. *Plus (user, 2026-10-16) the Gunner's once-per-battle abilities and moving officers.* **BUILT 2026-10-16** (§9.16) | A mode switch keeps it; the officer lost mid-charge; the client's `isLoaded` agrees with the server |
| **11** | Expert Laser Technician, the first Hard officer. **BUILT 2026-10-17** (§9.17) | The flat bonus per laser class and laser mode only; the boost capped at half the roll, one pool a turn over a laser's hits; the Gunner's laser; hidden from opponents; the client's Damage line and power balance |
| **12** | Expert Plasma Scientist, added by the user (not one of the original 25). **BUILT 2026-10-17** (§9.18) | +1/+2 per class with the light ones excluded; the first two hexes of range damage ignored by every plasma rule, the Bolters' and Nexus overrides included; Plasma Batteries +1 (none destroyed), full from the deployment charge, −3 on criticals, the reactor's Initial Orders share; Plasma Webs +1 range, crippled too |
| **13** | Expert Security Officer, the second officer the user added. **BUILT 2026-10-17** (§9.19) | +5 initiative to every viewer; -2 on his own C&C's critical rolls, his and its other officers' kill roll included; +2 on every boarding d10 against his ship (pod, claw and transporter delivery; Wreak Havoc, Sabotage, Rescue and the boarders' Capture roll), -2 on his defenders' Capture rolls; 5% EA / 7.5% elsewhere, every whole-percent price unchanged |
| **14** | Expert Religious Leader, the third officer the user added. **BUILT 2026-10-17** (§9.20) | +10 initiative to every viewer; +1 on every hit (a pulse, a gun's shot, a raking shot once), never on a roll of 0 or a ram; once he is disabled or killed in battle, from the next turn: +20 initiative, both profiles +1, the strongest standing Scanner +1, the +1 per hit kept, the profiles published; moves to any targetable primary non-weapon system, the jump drive included; Minbari Federation, Markab, Corillani and Yolu only, 15% |
| **15** | Moving officers in Fleet Selection, free; the Religious Leader's placed twin; any officer on a post destroyed by pre-battle damage refunded. **BUILT 2026-10-17** (§9.21) | Move from a system's menu (its Officers footer), the pick outlining every post he could be bought on; placed to placed, C&C to twin and back, points unchanged; occupied, destroyed and unoffered posts refused; the twin offered on every primary non-weapon system but the C&C, at his own price, and every effect his; a C&C destroyed in the pre-battle editor refunds its ship officers with a warning, an edit and a saved fleet too; the menu rows keep their one shape |
| **16** | FV's EM hardening rebuilt to the tabletop rules, the Ring Base hardened; the Expert Surge Officer on top. **BUILT 2026-10-18** (§9.22) | Hardened units no longer immune: -2 once a turn on the critical (dropout) roll of a system an EM weapon affects, through the real `doDamage`; a forced dropout ignored on 1-2, a forced shutdown resisted on 1, power losses one less, at the Burst Beam family, Shock Cannon and others; other EM effects and armour-ignoring damage as against anyone; the flat -1 stand-in gone from every Ipsha hull. The officer: Ipsha only, 10%, the Mag-Gravitic Reactor; +2/+1 power by size, previewed in the lobby; +1 per EM hit, +N on an N-cannon Surge Cannon shot, his own Spark Field too; -3 and 1-2 on his ship; Spark Fields 1 weaker against him |
| **17** | Expert Ion Technician (Cascor), his thrust boost on the Hypergraviton Blaster's path. **BUILT 2026-10-18** (§9.23) | +1 per die (capped at the face) on rolled ion weapons, +1 a hit on fixed ones (the Ion Torpedo, a Ballistic weapon in FV, included), nothing on a roll of 0; stacking with Elite Crew, replaced by a Gunner's; Rad Cannons 12 boxes, the C&C at +4, criticals forced on Scanner, Engine, Reactor and Sub Reactor; the boost - 3 thrust for 1d10+1, one level per gun (a second refused in the real power.js), once a turn, a loaded rolled gun only, hidden from opponents, the engine's thrust lowered in the real movement.js; a boosted gun that does not fire discharges at the turn change; Cascor only, 10%, in the Engine |
| **18** | Expert Pilot, added by the user - the first officer bought as a COUNT. **BUILT 2026-10-19** (§9.24) | Up to 4 (Raiders 2), never more than the flight's craft, each in his own craft from the first; +1 OB each; one free jinking level each, beyond the jinking limit, in the real movement.js (`jinkinglimit` itself untouched); each lost with his own craft, and following it on a split, his share of the cost with him; Spark Fields 1 weaker per pilot on a turn the flight neither jinks nor fires (an interception not counting); the Evader's -1 already reaching Spark Fields; pilots the flight's one officer; 100% of a craft each for a flight of six, scaled; the buy dialog holding the count to the flight size |
| **19+** | The other Hard officers (the Expert Analyst among them since 2026-10-16), one at a time, each starting with its own short plan and ruling round | — |

---

## 6. Rules questions

### Cross-cutting — RULED 2026-10-09

| # | Question | Ruling |
|---|---|---|
| **QX1** | Initiative units. FV rolls d100 and writes tabletop × 5: Elite Crew's "+5" is one tabletop point. Are the brief's numbers FV units? | Yes: Helmsman +5, Dogfighter +5, Quartermaster −30 and the Jump Officer's "−20" are FV units. The Evangelist's "−2" is tabletop, so **−10** in FV |
| **QX2** | Does a critical on his post kill an officer? | **Yes**, on a critical roll of 20 or more, modifiers included (D3) |
| **QX3** | A cap on officers per unit, beyond one of each? | None for ships. **A flight may buy one officer in total** (D9) |
| **QX4** | Which units may carry officers: bases, OSATs, LCVs, Ancient hulls? | **Not** Ancient hulls, OSATs (mines included) or terrain. Bases and LCVs only where the officer's post exists, so a base without a jump drive gets no Jump Officer (D9) |
| **QX5** | Track-1 officer whose post was destroyed pre-battle: disabled with a warning, or refunded automatically? | Disabled from the start, with a warning (§4.9). Default accepted without comment. **RE-RULED 2026-10-17: refunded, as every officer on a destroyed post is (§9.21)** |

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
| Analyst | Does he need range, line of sight or an OEW lock? Every enemy ship, or only those in range? Weapon arming is already public in FV: leave it so? **Moved to the Hard tier 2026-10-16** (the full rules are more complex than the brief): his own plan and ruling round when he is reached. |
| Coordinator | **Rules text 2026-10-16** (§9.15): "may make a combat pivot for half the normal thrust cost (i.e., 1 point instead of 2) and with no weapons fire penalties as a result, unless the combat pivot was a full 180°, in which case a penalty of −1 is applied"; one-half the base cost of the fighter; one coordinator per flight. Built with defaults, not ruled: (a) "a full 180°" = this turn's combat pivots, rights against lefts, have turned the flight 3 hex sides or more (R-L-R is 60°, no penalty; four one way passed through 180°, penalty); (b) half rounded **up** (a shuttle's 3 → 2, a pivot-cost-3 flight's 5 → 3); (c) the −1 is waived on every shot of the flight that turn, ballistic launches included (the penalty's own scope). |
| Evangelist | The fleet = this player's ships, or the whole team? His penalty is worded "if killed": does a merely **disabled** Evangelist (post destroyed, kill roll survived) cost the fleet its +1 without the −10 initiative / −1 to hit? (Default: yes, the penalty needs a kill.) Is the penalty permanent? Do reinforcements that arrive later get the bonus? |
| Laser Technician | Lasers = `weaponClass` Laser (49 classes)? Is the power boost declared in Initial Orders? Is the +50 % cap on base damage? **Rules text and rulings 2026-10-17 (§9.17):** the text settles the first three - laser-category weapons (any weapon with a Laser mode; only a shot in that mode gains), the boost through the ordinary boost in Initial Orders (the user's design note: "boostable ... 1 power = 1 damage, with no maxBoostLevel but a cap"), the cap on the roll ("if the base roll was 12 damage, it could be raised to 18"). **RULED:** (1) a laser that hits more than once a turn shares its boost as **one pool** for the turn; (2) on a serving Expert Gunner's laser the Gunner's +1 per die **replaces the flat bonus only** - the boost still works there; (3) boosts **hidden from opponents**; (4) the owner's tooltip **Damage line shows** the flat bonus and the boost, with a **Laser boost** line. |
| Plasma Scientist | *Added 2026-10-17 with his rules text (§9.18).* Which plasma weapons are "medium or heavy"? Does a battery start the battle holding the extra point? Are the web's range and the battery's capacity shown to opponents? **RULED 2026-10-17:** every plasma weapon but the light ones (`Officers::$plasmaClassBonus`); yes, a battery still holding its deployment charge starts full; yes, to every viewer (a displayed stat is public). |
| Security Officer | *Added 2026-10-17 with his rules text (§9.19).* The user's note settles the main one: "shift all die rolls by up to 2 in any direction desired by the defending player" is a fixed **+2** ("higher rolls are almost always worse for marine attackers ... no need to build in a player choice"). Asked: do the defenders' own Capture rolls (5 or less hits) shift too? Breaching pods only, or Grappling Claws and transporters as well? 7.5% exactly, or 8%? On a hull with two C&Cs, which gets the -2? **RULED 2026-10-17 (all as recommended):** defenders **-2** too (Elite Crew's pattern, doubled); **every boarding party**; **7.5% exactly** (a percent may now carry one decimal place); **his own C&C only**. |
| Religious Leader | *Added 2026-10-17 with his rules text (§9.20).* Are "+10 initiative" and "+5 to its defense profiles" FV's displayed units or tabletop? What is a "volley" for his +1 damage? Does the ship keep the +1 damage once he is lost? Is the Minbari Protectorate one of the "Minbari" factions? **RULED 2026-10-17:** **FV display units** (+10, then +20; the profiles +1 point each, +5%); **+1 per hit** (each pulse, each gun's shot, a raking shot once, never per die); the frenzy **keeps** the +1 damage; **Minbari Federation only** - the Protectorate is not included. |
| Software Engineer | "Lets the computer specialist use their ability twice" and "Incompatible with Computer Specialist" contradict each other. Which one stands? "Submarine" = the Hyach stealth hulls (Alichi Kav and Tal)? Whose detection range gets +5? |
| Ion Technician | Rad Cannons always score 12, with no roll? Forced crits on Sensors, Engine and Reactor on every hit? When is the thrust boost declared? Is "+4 on C&C crits" a penalty? **Rules text 2026-10-18 (§9.23)** settles all four: 12 Structure boxes instead of 10 (the Rad Cannon rolls nothing anyway); a forced critical roll on a sensors, engine or reactor hit; the boost "is made at the same time systems are activated or deactivated for extra power" - Initial Orders; the C&C's roll "at +4 on the die instead of the listed +2" - his ship's Rad Cannons hitting an enemy C&C. Asked: may one gun take more than one boost? A boosted gun that does not fire - build the discharge? Does a Sub Reactor count as the reactor? **RULED 2026-10-18:** the boost was first ruled **repeatable** (every 3 thrust another 1d10+1), then, once built, **re-ruled to one level per gun** a turn (the recommendation); the **discharge is built**, with a tooltip line; a **Sub Reactor counts**. |
| Surge Officer | Which systems are the "caps" that get +2 / +1 power? Is "EMHardened gives −3" a change for the whole ship? **Rules text 2026-10-18 (§9.22)** settles the first: "Capital ships and larger receive +2 points of power, while medium ships and HCVs get +1" - the reactor. **RULED 2026-10-18:** the −3 is "−1 on EM-hit systems" (the tabletop −2 → −3 step, where the rule says); then, the user finding FV's hardening to be a simplification, **FV's EM hardening rebuilt to the tabletop rules** (supplied) and the Ring Base hardened, the officer on top; the −2 **once per system per turn**; EM effects the tabletop hardening does not list **apply normally** (the old immunity lines commented out with a note, to revisit); armour-ignoring EM weapons **ignore Ipsha armour fully**; the flat −1 stand-in **removed** from every Ipsha hull; **his own Spark Field gains the +1**. |
| Expert Pilot | *Added 2026-10-19 with his rules text and notes (§9.24).* Several pilots beside another officer (D9 says one; the Spark Field text pictures "four pilots and an evader")? 100% of a fighter each, or the §9.9 scaled rule? The Spark Field relief "if he forgoes all his other benefits for the entire turn" is a per-turn choice: build a toggle? Which factions are "Raiders"? **RULED 2026-10-19:** pilots **ARE the flight's one officer** (1-4 of them, never beside another); **scaled** by §9.9 (100% of a craft each, for a flight of six); the relief **automatic** - "if flight doesn't jink or fire that turn they can get the damage reduction"; **the Raiders faction only**. |
| Ballistic Officer | "+5 distance range" = launch range of every ballistic weapon, torpedoes included? How is "+1 total magazine rounds per missile weapon" counted? How are the free swaps priced (+2 % each)? |
| Anticipator | Free turn: a full turn (not a pivot) in the Firing phase, paying its thrust? "Half price for some factions": which factions? |
| Matter Weapons Expert | May a ship carry both variants? Does the weapon variant also give that weapon the +1 fire control and damage? (Stage 8.) The C&C variant's "only for factions with matter weapons" was built with a default, not ruled: offered to a hull carrying at least one weapon with a Matter mode (§9.5). **RULED 2026-10-15: Grome Autocracy only** (§9.12); the Matter-weapon hull test is kept beside it. **Rules text and rulings 2026-10-16 (§9.14):** "moved into one specific weapon instead of his normal station" - **one or the other, never both**; that weapon gets the +1 / +1 "but no others do", and its rate of fire +1 (Stage 10); "disabled if the weapon takes any damage at all (after armor), and automatically killed if the weapon is destroyed" - **and the ordinary kill roll too**. |
| Gunner | Size caps for LCVs and bases? Which weapons are eligible: any with fire control, ballistics too? Does he stack with Gunsights? **Rules text and rulings 2026-10-16 (§9.14):** +1 to hit (not EW) and +1 per die capped at the face, always; 5% of the base cost, doubled on non-Yolu ships; at most 1 on a medium ship or smaller, 2 heavy, 3 capital, 4 enormous; **any weapon Gunsights may be bought for** (ballistics included), stacking with Gunsights and with Elite Crew; "does not combine any to-hit or damage bonuses with those of other elite officers" - **on his weapon his own replace the Matter Weapons Expert's**. Once-per-scenario rate of fire, piercing mode and +3 called shot: not combinable, Hard (§3.3), not built; moving between weapons: §8. **Stage 10 (user, 2026-10-16, §9.16) - all three automatic:** the faster reload after the first firing that used neither other ability (**RULED: saved for a later firing** when the first used one); +15% on his weapon's first called shot that is not Piercing; Piercing as an ADDED firing mode on a gun without one, removed once fired - **RULED: single-mode Standard/Raking direct-fire guns only, at the usual -4 with no fighters** - and +4 on a native Piercing gun's first Piercing shot. All three tied to HIM, not the weapon. Moving officers between posts in Initial Orders (blue `#90b1ee` Officers menu); **RULED: the Matter Weapons Expert moves between the C&C and his Matter weapons too**. |
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

- Officers gaining experience or being promoted. *(Moving an officer between posts mid-battle was added by the user on
  2026-10-16 and built in Stage 10, §9.16.)*
- Buying officers in game. Lobby only, like every other enhancement.
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

### 9.14 Stage 8: placed officers - Expert Gunner (core) and the weapon-mounted Matter Weapons Expert (2026-10-16)

**Rules text (user, 2026-10-16).** Expert Gunner (League-2, Yolu): assigned to a specific weapon; +1 to hit, "not EW",
always; +1 damage per die, never above the die's highest roll, always; the weapon's rate of fire +1 turn (never better
than once a turn), piercing mode even where not normally allowed (no penalty where it is), and a called shot at +3 -
each **once per scenario**, none combinable with another; "does not combine any to-hit or damage bonuses with those of
other elite officers"; 5% of the base cost, doubled on non-Yolu ships; at most 1 on a medium ship or smaller, 2 on a
heavy, 3 on a capital, 4 on an enormous unit; may move between weapons by the officer movement rules. Matter Weapons
Expert: +1 to hit and damage with all Matter weapons (part of the fire control, cumulative with an improved gunsight);
may instead be moved into one specific weapon, which alone gets the bonus, plus its rate of fire +1 (never better than
once a turn); there he is "disabled if the weapon takes any damage at all (after armor), and automatically killed if
the weapon is destroyed"; 10%, C&C or a Matter weapon, Grome only.

**Rulings (user, 2026-10-16, asked before building):** (1) a Gunner on a Matter weapon the C&C expert also covers: **the
Gunner's bonuses replace the expert's on that weapon** (to-hit +1 either way; +1 per die instead of +1 flat); (2) the
Matter Weapons Expert is **one officer with two posts - C&C or a weapon, never both**; (3) the Gunner may take **any
weapon Gunsights may be bought for**, ballistics included, stacking with Gunsights and with Elite Crew's own +1 per die;
(4) the weapon-mounted expert **also makes the ordinary kill roll** (20+ killed) on top of his own text.

Built with defaults, stated here: **Stage 8 scope as the plan set it** - the Gunner's once-per-scenario abilities (Hard,
§3.3) and both officers' rate of fire (Stage 10, loading time) are not built, and an officer cannot change weapons in
game (§8: lobby only). **A weapon hosts one placed officer** (a Gunner and the weapon-mounted expert on one gun would
waste one of them under ruling 1). Size caps: shipSizeClass 0 (LCV) and 1 give 1, 2 gives 2, 3 gives 3, `$Enormous`
gives 4 (FV's size class 4 is not implemented) - so an enormous starbase gets 4. A weapon whose damage is not rolled on
dice gains no Gunner damage (the rule is per die). The weapon-mounted expert's pre-battle damage that does not destroy
his gun disables him from the start (any damage); one that destroys it removes and refunds him (D11, as any per-system row).

**Files.** Server: `Officers.php` (registry `OFF_GUN` / `OFF_MWEW` with the new keys `placed`, `hosts`, `shipLimit`,
`sameAs`, `anyDamage`; `$factionRules` OFF_GUN Yolu 5; `rulesId` - a sameAs officer reads his self's faction line;
`pickPlacedPosts`, `placedRows`, `keepPlacedRows` (THE rule), `placedLimit`, `gunnerLimit`, `mayHost`; `firstDamagedTurn`
and the anyDamage branch in `apply`; `applyGunner`, `gunnerDieBonus`, `applyWeaponMatterExpert`, `raiseMatterModes`,
`servesOn`; `matterDamageBonus($ship, $weapon)` and `applyMatterExpert` skip a Gunner's gun), `Enhancements.php`
(per-system registry entries with `officer`; `isSystemEnhancementOfficer`, `systemEnhancementOffered`; the offer's 6th
slot; `sysEnhEligibleOfficer*` / `sysEnhPriceOfficer*`; the summary count skips officers; `sanitisePlacedOfficers`),
`weapon.php` (`getFinalDamage` adds `gunnerDieBonus`; `getBonusDamage` passes the weapon). Client: `officers.js` (the
two registry rows; `placedRows`, `placedLimit`, `placedEntries` - the derived track 2 - `isPostDamaged`, `placedBlock`,
`shipLevelBlock`, `systemById`; `displayFireControl` for both; `stateText`'s pre-battle wording for anyDamage),
`systemEnhancements.js` (labels, `isOfficer`, the star / count skip officers, `refitCost`, `officerRows`, menu rows carry
`officer` and `blocked`, `set()` refuses a blocked buy, `describeRemoved` wording), `confirm.js` (`officerBlock` on a
ship officer row placed in a weapon, its note, `officerBlocked`), `gamelobby.js` (fleet-list lines; the menu's
`avoidBox`), React `SystemEnhancementsSection.js` (Officers sub-heading, their total, a refused row's disabled + and
reason), `SystemInfoMenu.js` + new `helpers/besideWindow.js`, `ShipWindow.js` (`data-ship-window`). Docs: About Officers
(placing an officer, the refund), the Matter Weapons Expert card (the weapon post), a new Expert Gunner card. Statics and
bundles rebuilt; no new PHP class, autoload unchanged.

**As built:**
- **Two tracks, one list (D1).** A placed officer is a per-system row (`tac_sys_enhancements`, `tac_saved_sysenh`), so
  D13's name check, saved-fleet re-validation and re-pricing, and the D11 destroyed-host refund came free. Everything that
  makes him an officer lives in `Officers.php`: `pickPosts` resolves the rows into `$ship->officers` (after the ship-level
  officers) and every effect, the insignia, the tooltip and the payload read that list, as before. The per-system
  registry's `officer` slot only keeps him off the ✦ and the "System Enhancements (n)" count, and has no `apply` (his
  effects need to know whether he serves, which only `Officers::apply` knows).
- ⭐ **`Officers::keepPlacedRows` is THE placement rule** - registry order, then system id: a system hosts one, no officer
  past his ship cap. The sanitiser (buy, save, reload - with a notice per dropped row) and the load both call it; the
  lobby's `officers.placedEntries` mirrors it. The **sameAs** rule (the C&C expert wins over a weapon row) is checked at
  load and in the lobby, not in the sanitiser (which sees only per-system rows).
- **The cap reaches the lobby as data**: the officer offer tuple carries a 6th slot, `Officers::placedLimit`, so
  `systemEnhancements.set()` refuses past it with no client copy of the size rule. Price = `Officers::rowPrice` (pct of
  the hull, whichever weapon): Omega 93, Yolu Aluin 55 (5%), Grome Trokan 90.
- **Effects.** Gunner: +1 on every non-null fire control of every mode (`fireControlArray` and the live one, `isModified`
  sends both to everyone, as Gunsights); +1 per die beside Elite Crew's in `Dice::$perDieBonus` (a heavy laser's 10/9/3/1
  reads 26 against 23). Weapon expert: the C&C rule on his gun's Matter modes. The C&C expert skips a gun a serving
  Gunner holds (fire control and damage), so it comes back when the Gunner falls.
- **Out of action.** Gunner: the ordinary disabled / killed. Weapon expert (`anyDamage`): disabled from the first damage
  through armour, killed when the gun falls (its boxes or its block; a hit that does both is a kill), and the kill roll.
- **UI.** The menu's gold section lists refits, then an OFFICERS sub-heading with the officer rows and their own total; a
  refused row is dimmed, its + disabled, the reason on hover ("This ship may carry 2 Expert Gunners (already on ...)",
  "This weapon already hosts the Expert Gunner", "The Matter Weapons Expert is posted at the C&C ..."). The buy and edit
  dialogs refuse the C&C expert while he is placed in a weapon, with that line in the Officers section. The fleet list
  shows "System Enhancements (n)" for refits only and a line per placed officer.
- **The menu beside the window (user request 2026-10-16).** `helpers/besideWindow`: the lobby passes the ship window's
  rectangle (`closest('[data-ship-window]')`), and the menu opens 8px off the window on its roomier side, level with the
  clicked icon (top on its top in the upper half, bottom on its bottom in the lower), never nearer than 8px to the screen
  edge; with no side 300px wide (a phone, a full-screen window) it falls back to the old at-icon placement. game.php
  passes no rectangle and is unchanged; the lobby's fighter / mine damage menus and the hover tooltip are unchanged.

**Measured.** Statics +414,859 B (+0.65%; Earth Alliance +0.61%): 17,107 Gunner offers on 1,802 hulls and 165
weapon-expert offers on 11 Grome hulls, each 6 slots.

**Verified.**
- `officersHarness.php` **583 / 0** (518 with `--no-db`; 81 new): the two registries agree; offers (exactly the Gunsights
  weapons; prices and caps on Omega, Aluin, Hastan, Falaren, GromeMorgatAM, Altarian, lupaEscort; the expert on the Grome
  Matter guns only, and on a mixed Altarian flown Grome its Matter Cannons only; none for OSATs, a flight, a hull opting
  out); posts (D13, not offered, count 0, a missing id; the cap's lowest ids; one per gun; C&C wins; Centauri refused);
  the sanitiser (three of four kept at 93, the fourth named; one per gun; the D11 removal); the summary line; the
  Gunner's fire control (every mode, +2 with Gunsights, a mode switch), the die bonus through the real `getFinalDamage`
  (+3, +2 per die with Elite Crew, the gate, the static restored), disabled, the kill roll's note on the gun, the payload
  per viewer; the weapon expert (Matter modes, damage, "no others do", the Gunner replacing the C&C expert and the
  expert's return when the Gunner is killed, every anyDamage state incl. pre-battle); a REAL load of a stored Gunner row
  (G'Quan, game 4472) and a REAL save / reload, both rolled back. Mutation-tested: **29 breaks, all caught** (the first
  run missed two: a dead skip in `addOffers`, removed - `isEligible` refuses placed officers for both callers - and a Grome
  hull whose guns were all Matter, so the mixed Altarian case was added).
- `officersClientHarness.js` **192 / 0** (40 new): the derived list, insignia without a star (and with one beside
  Gunsights), counts and costs, the caps and reasons, `set()` refusals, both officers' fire control, pre-battle, the
  real `SystemEnhancementsSection`, `confirm.officerBlocked`, `besideWindow`. **27 breaks, all caught.**
- `enhancementsDifferential.js` **575 cases / 0 differing** (49 new placed cases built off each hull's own offers: a
  Gunner, with Gunsights, on every gun, the weapon expert, with the C&C one, both on one gun); posts are now compared as
  a list per officer. **4 drift breaks, all caught.** `enhancementsHarness` 271 / 0 (the fingerprint covers ship-level
  offers only, so per-system ones are the differential's and officersHarness's).
- `fvbuild -Check`: autoload current, validator clean, replay **146 / 0**. Kirishiac refit harnesses 168 / 0 and 88 / 0.
- **Standalone lobby driver** (`officersE2E/lobby8.mjs`, real bundles, no PHP / DB / session - planting sessions is
  refused since 2026-10-15): 17 / 17 at 1600 and 1920 wide, 16 / 16 at 700 (the fallback). The real-site drivers
  (`lobby*.mjs`, `docs.mjs`, `game.mjs`) were not run this stage.

**Observations, not changed:**
1. The officer rows ride `pointCostSysEnh`, the refit bucket, so a saved fleet's refit total and the "refits re-priced"
   notices include them - correct points, a refit word.
2. The Gunner is offered on every Gunsights weapon, so an offer per gun on 1,802 hulls is the static growth above; if it
   matters, the cap slot could move to one ship-level field.
3. Not tested in a real game: a Gunner's die bonus and fire control on a live shot, and the weapon expert disabled by a
   live hit. Worth a play test (any Grome ship for the expert; any hull for the Gunner).

### 9.15 Stage 9: Expert Coordinator, and three system-menu refinements (2026-10-16)

**Rules text (user, 2026-10-16).** "This expert allows a flight to react quickly to nearby threats, often with
near-incredible feats of anticipation. The flight may make a combat pivot for half the normal thrust cost (i.e., 1 point
instead of 2) and with no weapons fire penalties as a result, unless the combat pivot was a full 180°, in which case a
penalty of −1 is applied. Point Cost: One-half the base cost of the fighter. Limitations: A flight may benefit from only
one coordinator at a time." The same day the user moved the **Expert Analyst** to the Hard tier (§3.3) and asked for
three refinements to the lobby's system menu (below).

**Built with defaults, not ruled** (§6, Coordinator): "a full 180°" is this turn's combat pivots counted **rights against
lefts** - 3 hex sides or more is the penalty (R-L-R is 60°; four one way passed through 180°); half the cost is rounded
**up** (fighter 2 → 1, shuttle / breaching pod 3 → 2); the waiver covers every shot of the flight that turn, as the
penalty itself does. "One coordinator per flight" is D9's one officer per flight. Price: 50% of a craft for a flight of
six, scaled by size (the §9.9 rule) - Aurora 15 / 29 / 58 at 3 / 6 / 12 craft.

**Files.** Server: `Officers.php` (`OFF_COOR`, last in the registry so a doctored second row never displaces an officer
bought before him; `COMBAT_PIVOT_FULL`; `waivesCombatPivotPenalty`), `movement.php` (`Movement::getCombatPivotRotation`),
`weapon.php` (the fighter branch's −1, behind `$officersPresent`). Client: `officers.js` (the registry row;
`combatPivotCost`, `waivesCombatPivotPenalty`), `movement.js` (`getCombatPivotCost` - now the one cost `canPivot`
checks, `doPivot` writes and the map tooltip quotes; `getCombatPivotRotation`; `hasCombatPivotPenalty`, the one penalty
test), `weaponManager.js` (`computeShotModifiers` asks `hasCombatPivotPenalty`), `ShipTooltip.js` (the firing modifier
and "Combat pivot cost"). Docs: a fifth Fighter Officers card. Statics and bundles rebuilt; no new PHP class.

**As built:**
- ⭐ **The thrust half is client-only.** movement.php recomputes no thrust cost (the validator trusts the row's
  `requiredThrust`), so the row `doPivot` writes IS the cost. Every reader of the combat-pivot cost goes through
  `shipManager.movement.getCombatPivotCost`; savedOrders restores rows whole, so it needs nothing.
- **Every flight is agile** (measured over the statics: 434 flights, pivot costs 0 / 1 / 2 / 3, all agile), so
  `isPivoting` is always "no" for a flight and each combat-pivot row is one 60° step the way it names - the rotation
  count needs no facing arithmetic.
- ⚠️ **weaponManager.js's second `calculateHitChange` (~line 2575) is the OLD one, inside a comment.** The live client
  twin of weapon.php's fighter branch is `computeShotModifiers`, which the current `calculateHitChange` calls. An edit
  first landed in the commented copy and was reverted.

**The system menu (user requests 2026-10-16).**
1. **No gap beside the ship window**: `besideWindow` places the menu flush on the window's frame; the 8px stays only as
   the screen-edge margin (`EDGE_MARGIN`), so the room test is unchanged. *Follow-up (user report, same day): a menu on
   the window's LEFT still fell ~17px short in the real lobby - the page's vertical scrollbar. The mount is a fixed
   `inset: 0` overlay, so `right:` is measured from the viewport WITHOUT scrollbars, while `window.innerWidth` includes
   them. `SystemInfoMenu` now passes `document.documentElement.clientWidth / clientHeight`. The headless driver had
   `--hide-scrollbars`, so it could not see this; `lobby8.mjs <out> <w> <h> scroll` now runs with real scrollbars and a
   scrolling page (25 / 25 at 1600 and 1920; it failed on the old bundle, 751 against 766).*
2. **The edited system is outlined** while the menu is open: `helpers/menuTarget` holds (ship object, system id);
   `SystemInfoMenu` sets it on mount / update and clears it on unmount - only for the lobby's menu (an `avoidBox` is
   passed), so game.php is untouched - and each change repaints the ship windows once. `SystemIcon` draws a 2px outline
   just inside the icon (`outline-offset: -2px`, no reflow, no border state displaced) with a glow, in the menu's focus
   blue (`MENU_CHROME.focus`, #8bcaf2). The ship is matched by object, so a store blueprint sharing an id never lights.
3. **An Officers section bar**: the Enhancements band (`SectionBar` + `band()`) in a new `SECTION_INK.officer` - the Buy
   dialog's Officers colours (confirm.css's default `--buy-sec-*`: bar #90b1ee, title white). The officer rows became a
   section of their own (its rail, and its ink on the tickers, price and total, as every section's rows wear their own);
   refits keep the bronze. With only officer rows there is no empty Enhancements bar. ⚠️ The user called the colour
   "gunmetal"; in confirm.css **gunmetal is Ammo & Ordnance** and Officers is this blue-grey. Built to match the Buy
   dialog's Officers section, as asked; a one-line swap if literal gunmetal (#8e9aa6) was meant.

**Measured.** Statics +17,617 B: 360 Coordinator offers (the same flights the Dogfighter and Evader are offered to).

**Verified.**
- `officersHarness.php` **601 / 0** (536 `--no-db`; 18 new): offer and price (Aurora 29; 15 / 29 / 58; a shuttle yes, a
  ship and a Hunter-Killer no); a doctored second row; `getCombatPivotRotation` (none 0, R 1, LL 2, RRR 3, LLLL 4, RLR 1,
  RLRL 0, last turn's and Movement-phase pivots ignored); the waiver (R / LL / RLR yes, RRR / LLLL / RRLRR no, none
  without him or once his craft is lost); the real `Weapon::calculateHitBase` (−5% pivoted without him, none with him
  short of 180, −5% at RRR, −5% with the gate off). **9 mutations, all caught.**
- `officersClientHarness.js` **214 / 0** (22 new): `combatPivotCost`, `getCombatPivotCost` (fighter 1 / 2, shuttle 2 / 3),
  the rotation and the waiver (the server's cases), `hasCombatPivotPenalty`, the real `computeShotModifiers`, the real
  `doPivot` (a row costing 1 / 2; with 1 thrust left only his flight pivots); `besideWindow` flush; the outline on the
  target icon only, its repaints, the Officers bar's CSS (via `ServerStyleSheet`). **23 mutations, all caught.**
- `enhancementsDifferential.js` **583 cases / 0 differing** (8 new: `coor`, `coor+dog` on the four flight hulls);
  `enhancementsHarness` 272 / 0; `fvbuild -Check` all passed, replay **146 / 0**.
- Standalone lobby driver `officersE2E/lobby8.mjs` (real bundles; its harness page now loads confirm.css, as
  gamelobby.php does): **24 / 24** at 1600 and 1920 wide, 23 / 23 at 700 - the menu flush on the window, the outline on
  the clicked gun only (kept through a purchase, moved to the next gun, gone on close), and the Officers bar's computed
  style equal to the Enhancements bar's but for its colours, which equal the Buy dialog's `--buy-sec-bar`.
- `docs.mjs` and `lobby4.mjs` expectations updated (five Fighter Officers cards; the Thunderbolt's fifth officer row at
  40 per 6 craft) but **not run**: both need a planted session, refused since 2026-10-15.

**Observations, not changed:**
1. An enemy flight's map tooltip still quotes the normal combat pivot cost (2): the halving is read off the officer list,
   which an enemy never receives. Publishing it would need a new field (the 2026-10-10 "a displayed stat is public"
   default) - not added without asking.
2. Not tested in a real game: no local game holds a Coordinator flight. Worth a play test - buy one on any fighter, then
   in the Firing phase pivot once (1 thrust, no −5 in the hit chance) and three times one way (−5 back).

### 9.16 Stage 10: faster reloads, the Expert Gunner's once-per-battle abilities, and moving officers (2026-10-16)

**Request (user, 2026-10-16).** Stage 10 (loading time −1), plus the Gunner's three once-per-battle abilities, simplified
so that none needs a player choice: (1) the faster reload applies just after the first time his weapon fires and begins
reloading, unless that shot used the Piercing or called-shot bonus, "in which case normal recharge applies"; (2) +15%
("+3") on the first called shot his weapon makes, not if Piercing; (3) Piercing added as a temporary firing mode where
the weapon has none (read off `$damageTypeArray`), removed once fired in it - and on a weapon with its own Piercing mode,
+4 fire control on its first Piercing shot, cancelling the usual −4. "Once-per-battle abilities ... should be tied to the
Officer, not the weapon." And a new feature: **moving officers** between posts - announced in Initial Orders through a new
`#90b1ee` React **Officers** menu (**Move Officer**, then a click on the system; the button becomes **Cancel Move**), shown
only on a system with a movable officer; disabled or killed officers cannot move; a moving officer loses his abilities for
the rest of that turn and serves at the new post from the next; the insignia changes colour on the transfer turn and the
tooltip says so.

**Rulings (user, 2026-10-16, asked before building - all four as recommended):**
1. The added Piercing mode goes only on **single-mode Standard/Raking direct-fire weapons** (65% of the 17,107 Gunner
   offers; nothing in their damage code branches on a mode number). Pulse, Flash, ballistic, hex-targeted, magazine-fed and
   multi-mode weapons offer no Piercing - he can use it after moving.
2. The added mode carries the **usual Piercing penalty**: fire control −4 and no shots at fighters (the Battle Laser's own
   Piercing mode). "No penalty where it is" is the +4 on a native Piercing gun.
3. The **Matter Weapons Expert moves between the C&C and his Matter weapons** (and weapon to weapon) - one man, OFF_MWE at
   the C&C and OFF_MWEW in a weapon.
4. A first firing that spent another ability **keeps the faster reload for a later firing** (not lost).

**Built with defaults, not ruled** (ask before changing):
- The faster reload is **one more turn of loading at the start of the reload** (granted if he served on the gun the turn
  it fired), capped at a full load - "never better than once a turn": a one-turn gun gains nothing and the Gunner's ability
  is not spent on it. Losing him mid-reload does not take it back. **Any firing counts, an interception included.**
- A called shot's +15% covers every called order his gun fires that turn (practically one); "spent on turn T" stays usable
  through turn T, which is also what makes a replay of that turn read the same.
- A moving officer **counts at his old post for the whole transfer turn** (its damage can disable him, its crit roll kill
  him); a new post destroyed that same turn leaves him **disabled on arrival**. Damage his new weapon took before he
  arrived does not touch the weapon-mounted Matter expert's "any damage" rule.
- A target must be a post he could be **bought** for (a placed officer's per-system offer; the MWE's C&C), undestroyed, and
  **not held** by another placed officer still in action, nor another's destination that turn - so **no swaps** in one turn.
  A killed or disabled officer holds nobody's post. One move per officer per turn.
- Insignia: **both** the old and the new post blue (`#90b1ee`) on the transfer turn - and already while the order is only
  pending in Initial Orders.

**Files.** Server: `Officers.php` (registry keys `movable`, `reload`, `abilities`; `TRANSFER_NOTE` OffTransfer and
`USE_NOTE` OffUsed; `apply` rebuilt around `resolveTransfers` (each entry's `stays`, `transit`, `moving`), `outState` (out
of action matched to the post he held at that turn), `resolveUses`, `settleFiringModes`; `mayMoveTo` - THE move rule -
`postAs`, `officerAt`, `saveTransfers`, `moveTargets`, `takeNote`; `serves` skips a transit turn; `gunnerShotBonus`,
`fasterReload`, `mayAddPiercing`, `addPiercingMode`, `spend`, `abilityOpen`; `addForJson` sends `moving`, `abilities`,
`mode`, `targets`), `ShipClasses.php` (kills as a list WITH their post; `$officerTransfers`, `$officerUses`,
`$officerTransferClaims`), `DBManager.php` (the kill note's post; `takeNote`), `Manager.php` (`officerTransfers` claim),
`InitialOrdersGamePhase.php` (`saveTransfers` on the reloaded ship), `weapon.php` (`gunnerShotBonus` in `calculateHitBase`,
`fasterReload` in `onAdvancingGamedata`, `$addedModeFields` / `markAddedMode` sent by `stripForJson`). Client: `officers.js`
(registry flags and the Gunner/MWEW summaries; `serves`, `postState` 'moving', `stateText`/`abilitiesText`,
`describePost`, `gunnerShotBonus`; the move store `ship.officerTransfers`, `movableOn`, `canManage`, `movingTo`,
`arrivingAt`, `destinations`, the pick (`startPick`, `isPickTarget`, `pickTarget`, `cancelPick`), `setTransfer`,
`cancelTransfer`, `transferClaims`), new React `OfficersMenu.js`, `SystemInfoButtons.js` (`canOfficersMenu`), `SystemIcon.js`
(blue insignia, the pick click, destinations outlined), `SystemInfo.js` (moving / arriving lines), `weaponManager.js`
(`computeShotModifiers`' "Expert Gunner" line), `ajaxInterface.js` (`officerTransfers` in the commit). Docs: About Officers
gains **Moving an officer** (and the blue insignia, the used abilities in the tooltip, what the opponent sees); the Matter
Weapons Expert and Expert Gunner cards. Bundles rebuilt and the served copy synced; statics unchanged (no offer moved); no
new PHP class, autoload unchanged.

**As built:**
- ⭐ **Out of action is matched to the post he held at the time** (`outState` over `stays`). This also FIXES a Stage 8 bug:
  kills were keyed by officer id only, so one Gunner's kill note killed every Gunner aboard. A kill note now names the
  officer of its id at its post on its turn - which is where `rollKills` always wrote it. (The harness's synthetic kill
  note on a Jump Engine was moved to the C&C; its non-delivery proof now uses notes on the Jump Engine that match nobody.)
- **Moves are notes, replayed at every load** (`resolveTransfers`): the client keeps the order on the ship until the commit
  carries it as a claim; `InitialOrdersGamePhase::process` checks each claim with `mayMoveTo` on the reloaded ship and
  writes one OffTransfer note on his old post ("id:to"); every later load replays them in turn order through the same
  rule. Notes are intercepted in DBManager's loop (`takeNote`), never delivered.
- **Abilities are notes too** (OffUsed, "id:ability", on his post): `gunnerShotBonus` spends Piercing / called at hit-chance
  time inside `advance()`, `fasterReload` spends the reload at the turn change - both write straight through, as the
  Scavenger's save does. `resolveUses` gives each to the officer who held that post on that turn (never a fallen one).
- **The added Piercing mode** is built at every load while his Piercing is unspent (or spent this turn): every public
  per-mode `*Array` field holding mode 1 gets the new key (not a split mount's arc list), FC −4 with fighters null, priority
  2; the Gunner's own +1 then raises it like every mode. The weapon sends `firingModes` and every filled field (the client
  merges a system's payload key by key over its blueprint) - to his own team, and to an opponent only in the replay of the
  turn it was fired (follow-up 1 below). A gun its loading row left in the vanished mode goes back to mode 1
  (`settleFiringModes`).
- **The faster reload** adds 1 to the loading the turn change writes (`Weapon::onAdvancingGamedata` → `fasterReload`), so the
  client's `isLoaded` and loading readout follow with no mirror, and no `loadingtime` is touched (the plan's §3.2 trap).
- The **Officers menu** (`OfficersMenu.js`, Officers blue) appears in Initial Orders on the player's own system holding a
  serving movable officer with somewhere to go (`targets`, which the server computes in phase 1 only). Move Officer closes
  the menu, a passive notice says what to do, and every destination is outlined in `#90b1ee`; the next click on that ship
  orders the move (a non-destination cancels it, another ship's click ends the pick and goes on).

**Measured.** Statics unchanged. Payload: `targets` only in Initial Orders and only for the owner's movable officers.

**Verified.**
- `officersHarness.php` **718 / 0** (650 `--no-db`; 117 new): moves (moved / transit turn / payload `moving`), every
  refusal (the C&C, his own gun, a missing system, a wreck, an occupied gun, a post he does not hold, after his death), a
  killed Gunner's gun taken over, one move a turn, no swaps, two moves in a row; out of action per stay (old post after he
  left, during transit, a wreck on arrival, new post later, kill notes per post, the transit kill roll at the old post, the
  two-Gunner kill fix); the Matter expert C&C → railgun → C&C and railgun → railgun, "any damage" only while he is there;
  `targets` (phase 1, owner, occupied, transit); `saveTransfers` (valid, refused, duplicates, flood); the added mode (fields,
  FC, damage, switch and back, payload, none on a multi-mode / magazine / native gun, spent, killed, the stale mode
  settled); the REAL `calculateHitBase` (+15% called once and through its turn, gone later, gate; −20% added Piercing,
  called Piercing is Piercing; native +25% and spent; a called Raking shot from a Battle Laser); abilities follow him; the
  fallen Gunner's gun; `fasterReload` (every case) and the REAL `Weapon::onAdvancingGamedata` writing loading 2; and a
  REAL database round trip of a move (claim → note → reload: moving, not serving), rolled back.
- `officersClientHarness.js` **247 / 0** (33 new): the registry mirror, `serves`, `gunnerShotBonus` and the real
  `computeShotModifiers` "Expert Gunner" line, the blue insignia and titles on both posts, the tooltip's moving / arriving /
  used lines, `canManage` per phase and state, the menu (render and colours), the pick (outline, order, cancel, another
  ship), `destinations`, `transferClaims`, Cancel Move.
- **Mutation-tested: 36 breaks (23 server, 13 client), all caught** - the first run missed one (a fallen officer collecting
  a newcomer's spent ability: test added) and skipped one on a line-ending pattern (rerun, caught).
- `enhancementsDifferential.js` 583 / 0, `enhancementsHarness` 272 / 0, Kirishiac refits 168 / 0 and 88 / 0,
  `fvbuild -Check` all passed (replay **146 / 0**).
- Not run: the real-site headless drivers (session planting refused since 2026-10-15) and a real game.

**Follow-ups (user, 2026-10-16, same day):**
1. **The added Piercing mode is hidden from the opponent** ("we really should hide it ... so that it does appear
   correctly in replay"). Done server-side rather than by a client `gamedata.replay` toggle, so the data never reaches
   an opponent in live play: `Weapon::stripForJson` sends the mode's fields (`markAddedMode($mode, $fields, $public)`) to
   the owner's team, and to anyone else only when `$public` - his Piercing spent ON the load turn - or the game is over;
   otherwise it sends the fire control list without the mode's key. His Piercing is only ever spent while that turn's
   shots resolve (inside `advance()`, which then moves the game on), so "spent on the load turn" holds only for a load OF
   that past turn: `replay.php` (`Manager::getReplayGameData`, the replay and the printed combat log) - where
   `combatLog.logFireOrders` switches the weapon to the order's mode and names the shot "Piercing". Opponents' replays
   of other turns, and live play, never carry it.
2. **A pending move drops the officer from the owner's hit-chance preview** ("misleading"): `weaponManager.
   computeFireControl` subtracts `officers.pendingMoveFireControl` - `displayFireControl`'s rule (now `fireControlRaise`)
   asked with every serving officer and without those with an uncommitted order, so a Gunner leaving a Matter gun the C&C
   expert covers costs it nothing, as on the server. `officers.gunnerShotBonus` skips him too. The opponent's view needed
   nothing: the move is committed before any of his ballistics resolve or show, and the server has dropped the +1 by then.
3. Save Orders not saving a pending move: accepted as standard for Save Orders.
Verified: officersHarness **721 / 0** (the payload per viewer, game over, the firing turn's replay), client **255 / 0**
(`pendingMoveFireControl` cases, the real `computeFireControl`, the shot bonus); 9 more mutations, all caught; differential
583 / 0; `fvbuild -Check` passed. Bundles rebuilt, served copy synced.

4. **The weapon tooltip's "Fire control" line drops it too** (user, same day: "if the weapon doesn't actually gain the FC
   bonus on that turn, the tooltip should not show it"): `officers.adjustDataDisplay` subtracts the same
   `pendingMoveFireControl`. Client 256 / 0, the mutation caught.

5. **Play test, game 4484 (user, 2026-10-16) - two bugs fixed:**
   - *A replayed / logged Piercing shot read "Raking".* `Manager::getReplayGameData` builds a past turn on a gamedata
     constructed at the game's CURRENT turn and only then `setTurn($turn)`s it, so `Officers::apply` judged the Gunner's
     Piercing by the current turn - spent on the replayed turn, it read as gone and the mode was never added (and with it
     "spent on the load turn", follow-up 1's public test, never held in a replay either). `Officers::restoreFiredModes(
     $gamedata, $turn)`, called right after `setTurn`, adds the mode back - public - to the weapon each Gunner held on the
     replayed turn when he spent his Piercing on it. Nothing else about officers is re-judged for a replay. ⚠️ Every other
     officer state in a replay is still the current turn's (pre-existing since Stage 1; display only).
   - *The faster reload was spent on the same firing as the Piercing shot* (notes on turns 1 and 6). The turn change runs
     on the gamedata `Manager::advanceGameState` loaded BEFORE the firing; `FireGamePhase::advance` resolves the shots on a
     gamedata of its own, so the Piercing / called-shot spend (in memory and as a note) is invisible to `fasterReload`.
     It now also reads the firing's own fire orders (`firingSpentAbility`: a Piercing mode while his Piercing was his, a
     standard called shot while his called shot was; interceptions spend neither). Game 4484's two wrongly spent reloads
     are left as they are (the user's test data).
   - *The +15% called-shot preview on turn 3* was not reproduced: the payload of a live turn-3 load carries "called shot
     used, turn 1" for the Gunner on #23 and the client answers 0 for him (+15% only for the Gunners on #9 and #10, who had
     not used theirs). Asked the user which weapon showed it.
   Verified: officersHarness **731 / 0** (restoreFiredModes, the firing's own orders, a REAL `getReplayGameData` of a turn
   with a spent Piercing for an opponent viewer, rolled back); 4 mutations, all caught; `fvbuild -Check` passed; synced.

**Observations, not changed:**
1. Not tested in a real game. Worth a play test: a Gunner on a laser (fire once - the next reload starts a turn on; a
   called shot shows "Expert Gunner +15%" once; the added Piercing mode in the mode selector until fired), then Move
   Officer in Initial Orders (both insignia blue, no bonuses that turn, the new gun's from the next).

### 9.17 Stage 11: Expert Laser Technician (2026-10-17)

**Rules text (user, 2026-10-17).** "With their natural affinity towards laser weapons, the Hyach have produced a few truly
extraordinary laser technicians ... all of which operate throughout the scenario. Spinal lasers score +3 damage, blast
lasers score +2 damage, and all other laser-category weapons score +1 damage. The player can shunt extra power into any
laser weapon for added effect. Each point of power provides +1 extra damage, in addition to the above benefit ... A weapon's
total damage cannot be increased by more than 50% through the use of this ability (e.g., if the base roll was 12 damage, it
could be raised to 18 with 6 points of power, but any additional energy is lost. Note that if used with a weapon with
multiple modes (such as the laser/pulse array), the above benefits affect only the laser portion of any shot. Location:
Reactor. Cost: 10% of the ship's base cost. Availability: Races other than the Hyach pay 50% more for this officer. Not
available on ships without laser weapon(s)." The user's design note: "Laser weapons essentially become boostable with this
Officer at 1 power = 1 damage, with no maxBoostLevel but a cap at the point of damage."

**Rulings (user, 2026-10-17, asked before building - all as recommended but the second):**
1. A laser that hits more than once a turn (pulse lasers, multi-gun lasers) shares its boost as **one pool for the turn**,
   handed to its hits in order, each taking at most half its own roll - 1 power = 1 damage, as the note puts it.
2. On a laser a serving **Expert Gunner** holds, his +1 per die **replaces the flat bonus only**; the boost still works
   there. (Recommended was "replaces both", the Matter Weapons Expert precedent of §9.14.)
3. Boosts are **hidden from opponents**: no laser is boostable without him, so a boosted one would name him.
4. The owner's tooltip **Damage line shows** his flat bonus and the boost, with a **Laser boost** line.

**Built with defaults, stated here (ask before changing):**
- Lasers = any weapon with a Laser mode (`weaponClass`, or a `weaponClassArray` entry); a shot gains only in a Laser mode -
  the fired mode's `weaponClass`, so a Laser/Pulse Array's pulse mode gains nothing and its boost that turn is wasted.
  Ballistic lasers (the Hyach Proximity Laser, the Nexus laser missiles) count. "Spinal" = `SpinalLaser` (+3); "blast" =
  `BlastLaser`, `ImprovedBlastLaser`, `LtBlastLaser` (+2; the last is fighter-only, so never meets a ship officer).
- The cap is half the roll **as the weapon made it** (`getDamage`, so Elite Crew's and a Gunner's per-die bonuses are part
  of it), rounded **down** ("dropping fractions"); the flat bonus is outside it. Both are added **after** the weapon's own
  reductions (range damage penalty, Piercing's cut against MCVs), as the Hyach Weapon Specialist's +3 is.
- A hit on a Chameleon simulacrum alone is shown what a real hit would get but spends nothing from the pool.
- Price: registry 15%, `$factionRules` Hyach Gerontocracy 10% ("Races other than the Hyach pay 50% more"); the Hyach
  `HyachEcamronTek` is a Civilians hull, so it pays 15%. Post: the reactor (the Technician's pick; both may share it).
- Not movable. The boost costs `LASER_BOOST_COST` (1) power a level; no ceiling (no laser class defines `hasMaxBoost`).

**Files.** Server: `Officers.php` (registry `OFF_LASR` after OFF_JUMP - the last fixed-post ship officer - with `needs`
`hasLaserWeapon` and no `apply`; `$factionRules` Hyach 10; `LASER_BOOST_COST`, `LASER_BOOST_CAP`, `$laserClassBonus`,
`laserFlatBonus`, `laserDamageBonus`, `$laserBoostSpent` (a WeakMap), `laserBoostDamage`, `boostOn`, `laserForJson`,
`isAboard`, `hasLaserWeapon`, `isLaserWeapon`), `weapon.php` (`getBonusDamage` asks `laserDamageBonus` for a Laser-mode
shot; `getFinalDamage` keeps `$rolled` and adds `laserBoostDamage` after `getDamageMod`; `stripForJson` calls
`laserForJson`). Client: `officers.js` (the registry row; `needs` `laserWeapon`; `isLaserWeapon`, `isLaserMode`,
`LASER_BONUS` by name, `laserDamageBonus`, `laserBoost`, `laserBoostText`, `laserDamageLine`; `adjustDataDisplay` rewrites a
laser's Damage line), React `SystemInfo.js` (the Laser boost line). Docs: an Expert Laser Technician card in Ship Officers,
and a sentence in Seeing your officers. Bundles and statics rebuilt; no new PHP class.

**As built:**
- ⭐ **The boost IS the generic boost.** The server tells the owner's team the laser is `boostable` (with `boostEfficiency`
  1) while he serves - `Officers::laserForJson`, from `Weapon::stripForJson`, behind `$officersPresent` - and the existing
  boost buttons, the type-2 power entries, the client's power balance (`countBoostPowerUsed`) and the commit all follow
  with no new code. The server object's own `boostable` is never set: nothing server-side reads it for a weapon, and a
  payload decides who sees it. The server trusts boost entries (the power balance is client-only), so a doctored boost
  is answered at damage time: `laserBoostDamage` asks whether he serves on the order's turn.
- **The flat bonus** is `Weapon::getBonusDamage` (beside the Matter Weapons Expert's), for a shot whose fired mode is
  Laser. None of the 65 laser classes overrides `getFinalDamage`, `getDamageMod`, `getBonusDamage`, `beforeDamage` or
  `damage` (measured), and each pulse and each gun's shot runs `getFinalDamage` once, so both bonuses land per hit.
- **The pool** is in memory only (a WeakMap: weapon => turn => points spent): every hit a weapon makes in a turn is resolved
  in one request on one weapon object. Replays never re-resolve damage.
- **Hidden boosts:** an opponent's payload carries the laser's power entries WITHOUT the type-2 ones while he is aboard
  (serving or not - an old boost would name him too), and no `boostable` / `boostEfficiency`. Game over discloses all.
  The owner is always sent `boostEfficiency` with the entries (power.js prices any boost it finds by it, and a null one
  would throw), `boostable` only while he serves.
- **The tooltip** (client only - the server applies both at damage time, so in game too): `officers.adjustDataDisplay`
  rewrites a laser's Damage line, which `setSystemDataWindow` builds from mode 1, as `roll + bonus + min(boost, half the
  roll)` on each end, reading the roll off the weapon's own min/max so a line a Hyach Weapon Specialist already raised
  keeps its +3 without it being halved; a line in any other shape is left alone. SystemInfo adds "Laser boost: +N damage this
  turn, never more than half a hit's roll" while boosted. Read at render time, never written into `system.data` (the
  shared-reference trap, [[arch_client_system_shared_reference]]).

**Measured.** 654 hulls carry a laser, 575 of them may carry officers; no laser is boostable today. 571 offers in the statics.

**Verified.**
- `officersHarness.php` **718 / 0 with `--no-db`** (53 new: offers and prices on five hulls incl. a Civilians Hyach hull at 15%,
  none without a laser or opted out, a doctored row; the post; the flat bonus through the REAL `getFinalDamage` on a Spinal,
  Blast, Improved Blast, Heavy, Maser, pulse (Gatling) and multi-gun (CW Quad) laser; the Laser/Pulse Array per mode; killed,
  gate off, a ship without him; the Gunner's laser; the client's `LASER_BONUS` against the server's table over all 65 laser
  names in the statics; the boost - 4, the cap, an odd roll, the pool over three hits and a fresh turn, a Gatling's pulses,
  the phantom hit, a doctored entry, killed, the pulse mode wasted, the gate, the Gunner's laser; the payload per viewer -
  owner, enemy, another system's boost, a ship without him, a laser whose only entry is a boost, game over, killed).
  The full run with the database: **787 / 0**.
- `officersClientHarness.js` **290 / 0** (34 new: the registry, `needs`, the flat bonus incl. the Gunner and his pending or
  committed move, the boost read, the Damage line in every case incl. odd rolls and a raised line, `laserBoostText`, the
  REAL SystemInfo, the REAL power.js charging 3 power for a boost of 3). Stage 8's registry-order check now states its
  intent (after every fixed-post ship officer) instead of "right after the Jump Officer".
- **Mutation-tested: 39 breaks (26 server, 13 client), all caught.** Run in a throwaway `fv-phpcheck:8.4` container against a
  Stage-11-only copy of the harness (deleted afterwards); files byte-identical after the run.
- `enhancementsHarness` **273 / 0**: the fingerprint re-recorded (old copy `fingerprint.before-officers-stage11.txt`) after
  proving the change is ONLY the 9 OFF_LASR offers and the 30 new case blocks (`lasr`, `lasr+tech+rea` on 15 hulls) - with
  those stripped, the new fingerprint is byte-identical to the old. `enhancementsDifferential.js` **601 / 0** (18 new);
  a client post moved to the Engine was caught on every laser hull. `fvbuild -Check`: autoload current, validator clean,
  replay **153 / 0**. (Run 2026-10-17 once the Docker stack was back up.)

### 9.18 Stage 12: Expert Plasma Scientist (2026-10-17)

**Rules text (user, 2026-10-17) - a 26th officer, not in the original brief.** "Being masters of plasma technologies, and
somewhat single-minded of purpose, some Pak'ma'ra have become true experts in their field ... All medium or heavy plasma
weapons on the ship score +1 damage (this would include medium plasma cannons and larger, as well as plasma streams, plasma
accelerators, etc.). Fuser torpedoes score +2 damage, not +1. All plasma weapons on the ship ignore the first two hexes
traveled when reducing damage scored against an opponent. For example, a weapon which suffered -1 point of damage per 2 hexes
which struck an enemy 10 hexes away would lose only 4 points of damage, not 5. All plasma batteries can store an extra point
of power, and receive a -3 bonus on any critical hit roll. They cannot operate if completely destroyed (despite the +1 power
bonus). Plasma webs receive a +1 bonus to range, even if suffering from a critical hit. Location: Engine. Cost: 10% of the
ship's base cost for Pak'ma'ra. Availability: Any race using plasma weapons can use these officers, but the cost is doubled.
Not available on ships without plasma weapon(s)."

**Rulings (user, 2026-10-17, asked before building - all as recommended):**
1. "Medium or heavy" = **every plasma weapon but the light ones**: Light Plasma Cannon, Light Plasma Bolter, Light Plasma
   Projector, Light Enhanced Plasma, Light Mag Gun, Point and Heavy Point Plasma Gun, Plasma Torch, Plasma Burst (and the Plasma
   Web, whose bonus is range). A new or custom plasma weapon is +1 until listed. Fusers = Fuser, Ranged Fuser, Fuser Array (+2).
2. A Plasma Battery still holding its **deployment charge starts full** at its new capacity.
3. The web's range and the battery's capacity are **shown to every viewer** - a displayed stat is public (2026-10-10 default).

**Built with defaults, stated here (ask before changing):**
- The range relief applies to **every** plasma weapon, light ones included ("All plasma weapons"), and to every range damage
  rule: `rangeDamagePenalty` and the eleven classes with rules of their own (a Heavy Plasma Bolter's 15 free hexes become 17).
  Only a shot whose fired mode is Plasma gains either bonus. Priced 10% for the Pak'ma'ra Confederacy, 20% for everyone else;
  posted in the strongest Engine, so the Pak'ma'ra and Corillani bases (no Engine) are not offered him.
- The web's +1 is its Anti-Fighter range only (3 -> 4); Defensive's 100 means "unlimited". Under its `ReducedRangeValue`
  critical the client now takes 2 off whatever range it has ("decrease range by 2", the critical's own description) instead of
  writing 1: identical without him (3 -> 1), 4 -> 2 with him. The server never reads that critical (the client enforces the
  web's range; no server range check exists for it).
- The owner's Damage line shows his +1/+2, as the Laser Technician's does (the shared `raiseDamageLine`). The range relief has
  no tooltip line (the officer's summary says it).

**Files.** Server: `Officers.php` (registry `OFF_PLAS` after OFF_LASR, `needs` `hasPlasmaWeapon`, `apply`
`applyPlasmaScientist`; `$factionRules` Pak'ma'ra 10; `PLASMA_RANGE_RELIEF`, `PLASMA_BATTERY_BONUS`, `PLASMA_BATTERY_CRIT_MOD`,
`PLASMA_WEB_RANGE`, `PLASMA_WEB_MODE`, `$plasmaClassBonus`, `plasmaFlatBonus`, `plasmaDamageBonus`, `plasmaRangeRelief`,
`webForJson`, `hasPlasmaWeapon`, `isPlasmaWeapon`; `weaponForJson`, the one Weapon payload hook, now calls `laserForJson`
(made private) and `webForJson`), `weapon.php` (`getRangeDamageDistance`; the base `getDamageMod` reads it; `getFinalDamage`
adds `plasmaDamageBonus` beside the laser boost), `plasma.php` and `customNexus.php` (the eleven overriding `getDamageMod`s read
`getRangeDamageDistance` - one call each, identical without him), `baseSystems.php` (`PlasmaBattery`: protected
`$capacityBonus` and `$deploymentCharge`, `getMaxCapacity` adds the bonus while it stands, `getOutput` caps at it,
`setCapacityBonus`, `holdsDeploymentCharge`, the stored-power note records whether it is the deployment fill). Client:
`officers.js` (the registry row, `needs` `plasmaWeapon`, `PLASMA_BONUS` by name, `isPlasmaWeapon`, `isPlasmaMode`,
`plasmaDamageBonus`, `plasmaDamageLine`, `batteryCapacityBonus`; the Laser Technician's line arithmetic moved into the
shared `raiseDamageLine`), `model/system/baseSystems.js` (`PlasmaBattery.doIndividualNotesTransfer` charges up to the extra
point), `model/weapon/plasma.js` (`PakmaraPlasmaWeb.initializationUpdate`'s crippled range). Docs: an Expert Plasma Scientist
card and his numbers in Seeing your officers. Statics, bundles and the served copy rebuilt; no new PHP class.

**As built:**
- ⭐ **The flat bonus is in `getFinalDamage`, not `getBonusDamage`.** The three Plasma Bolters and the three Nexus Enhanced
  Plasmas override `getDamageMod` and call the parent only to throw its result away, so anything `getBonusDamage` adds never
  reaches them - true today of the Hyach Weapon Specialist's +3 and the Matter Weapons Expert's +1 on any such weapon
  (observation, not changed). After `getDamageMod`, every class gets it.
- ⭐ **ONE range rule: `Weapon::getRangeDamageDistance`.** The distance every range damage penalty counts, less his two hexes
  for a Plasma shot from a ship he serves on (behind `$officersPresent`). The base `getDamageMod` and all eleven overriding
  classes (the Light/Medium/Heavy Plasma Bolters and the fighter Light Plasma Bolter; the Nexus Heavy Plasma Charge, Charged
  and Heavy Charged Plasma Guns, Light/Medium/Heavy Enhanced Plasmas and the fighter Light Enhanced Plasma) read it.
  ⚠️ A new weapon with its own range damage rule should read it too.
- **The battery's extra point** is the commented-out `capacityBonus` hook PlasmaBattery always had, made protected (no blueprint
  carries it). `Reactor::effectCriticals` folds every battery's charge into each reactor's output for Initial Orders - but in
  the per-system loop, BEFORE `Officers::apply` - so `applyPlasmaScientist` adds what he changed to each reactor the same way
  (phase <= 1). The client charges the battery at the commit up to its new capacity (`batteryCapacityBonus`) and the server
  takes the charge as sent, as before. "Starts full" reads the battery's last stored-power note: phase -1 (the deployment fill)
  means untouched since, so it is read as full at the new capacity; every Initial Orders writes a new note, which ends it.
- **The web's range** is raised at load (`rangeArray[2]`, and `range` if it is in that mode) and sent to every viewer
  (`webForJson`) - the client merges the payload over the static blueprint's 3.

**Measured.** 482 hulls carry a plasma weapon (438 may carry officers, 15 of them Pak'ma'ra); 396 offers in the statics
(the rest have no Engine). Statics +21,503 B.

**Verified.**
- `officersHarness.php` **842 / 0 with the database** (52 new: offers and prices on five hulls incl. a Civilians one, none
  without plasma or without an Engine, a doctored row; the post; the flat bonus through the REAL `getFinalDamage` on Medium,
  Heavy, Mega, Fuser, Light, Heavy Plasma Bolter, Nexus Medium Enhanced and Plasma Torch; the range relief as "a shot at N
  hexes does what it would at N - 2 without him, plus his bonus" on eight weapon/range pairs incl. the Bolter and Nexus rules;
  the rules' own example; the Bolter's 17 free hexes; killed, gate off, a non-Plasma mode; the client's `PLASMA_BONUS`
  against the server's over all 44 plasma names in the statics; batteries - +1, the output, destroyed, damaged, -3, full from
  a deployment note but not a phase-1 one, the reactor's share in Initial Orders and not after, killed, the data line to an
  enemy; webs - the range, every web, the payload to an enemy, a web without him, killed).
- `officersClientHarness.js` **304 / 0** (14 new: the registry, `needs`, `isPlasmaWeapon`, the flat bonus by name, the Damage
  line incl. a non-Plasma mode 1 and the Laser Technician's line through the shared helper, the REAL SystemInfo, the battery
  bonus, the REAL `PlasmaBattery.doIndividualNotesTransfer`, the REAL `PakmaraPlasmaWeb.initializationUpdate`).
- **Mutation-tested: 37 breaks (27 server, 10 client), all caught**, files byte-identical afterwards.
- `enhancementsHarness` **274 / 0** - re-recorded (old copy `fingerprint.before-officers-stage12.txt`) after proving the change
  is only the 3 OFF_PLAS offers and the 30 new case blocks (`plas`, `plas+eng+poor1`). `enhancementsDifferential.js` **607 /
  0** (6 new); a client post moved to the reactor was caught. `fvbuild -Check`: autoload current, validator clean, replay
  **153 / 0**.

**Observations, not changed:**
1. The Plasma Bolters and Nexus Enhanced Plasmas lose every `getBonusDamage` bonus (above) - a Hyach Weapon Specialist's +3 or
   a Matter Weapons Expert's +1 would never land on them. Not reachable today (no Hyach or Grome hull carries one), but any
   future bonus added there will miss them.
2. Not tested in a real game. Worth a play test on a Pak'ma'ra ship: a Plasma Battery reading "7/7"-style in Initial Orders on
   turn 1, a Medium Plasma's tooltip at +1, a hit at range losing two hexes less, and a web's Anti-Fighter range 4.

### 9.19 Stage 13: Expert Security Officer (2026-10-17)

**Rules text (user, 2026-10-17) - a 27th officer, not in the original brief.** "The security officer is vital for maintaining
order, catching spies, and controlling dissent among the crew. His presence improves morale and ensures command and control
operations proceed smoothly. This provides a +1 bonus to the ship's initiative, and a -2 to any critical hit rolls against the
C&C. The security officer provides improved defenses against raids by enemy boarding parties. Whenever a breaching pod
attacks, shift all die rolls by up to 2 in any direction desired by the defending player (after the roll is made), if desired.
For example, if an enemy attempts a Wreak Havoc mission and rolls a 10, the defender could change this to an 8, 9, 10, 11, or
12, depending on which result he feels is the least painful. If the attacker has any modifiers to his roll, he must add these
in before the security officer makes his alterations. Location: C&C. Cost: 5% of the ship's basic value for Earth Alliance (EA)
factions. Availability: Non-EA factions increase the cost by 50%." The user's note: "The 2 either way on Marine missions rolls
can just be a +2 to the roll since higher rolls are almost always worse for marine attackers. There's no need to build in a
player choice."

**Rulings (user, 2026-10-17, asked before building - all as recommended):**
1. In Capture combat the ship's own defending marines (who hit on 5 or less) get **-2** too: every boarding roll moves 2 in the
   defender's favour - Elite Crew's pattern (+1 on the boarders' rolls, -1 on the defenders'), doubled.
2. **Every boarding party**: breaching pods, Grappling Claws and the Trek Combat Transporter alike (each has its own delivery
   roll; Elite Crew already applies in all three).
3. **7.5% exactly** outside the Earth Alliance: a percent may now carry one decimal place.
4. On a hull with two C&Cs, **his own C&C only** - as the Technician's -2 is his own reactor's.

**Built with defaults, stated here (ask before changing):**
- "+1 initiative" is tabletop, so **+5** in FV (QX1 - the Evangelist's -2 became -10 the same way). Published to every viewer
  through the registry's `publish` list, as the Helmsman's is.
- The -2 is his post's own `critRollMod`, so it also eases the kill roll of every officer posted on that C&C (D3, "including any
  modifications") - the Technician's precedent. ⚠️ Asking about "two C&Cs" I said "about 20 hulls, most Pak'ma'ra"; measured
  afterwards, FV has **3** hulls with two real C&Cs (EmperorsTransport, palaceship, gaimShamor) and **60** with a C&C plus a
  `SecondaryCnC` (most Pak'ma'ra, the Ja'Stat, the Colotnar bases...). `SecondaryCnC` is not a `CnC` subclass, so it is never his
  post, and under ruling 4 it gets no -2 either way.
- EA = the faction strings `Earth Alliance`, `Earth Alliance (Early)` and `Earth Alliance (defenses)` (the last is one hull,
  HephaestusAM). `Earth Alliance (Custom)` has no hull today (its static file is an orphan from January), so it is not listed.
- The shift is summed with the roll's other modifiers before `max(0, ...)` - the rule's "add these in before" cannot differ in a
  sum. A captured ship's automatic delivery (`ShipDisabled` sets the roll to 1) is untouched.

**Files.** Server: `Officers.php` (registry `OFF_SECU` after OFF_PLAS - the last fixed-post ship officer - posted `cnc`, pct
7.5, `apply` `applySecurityOfficer`, `publish` iniativebonus; `$factionRules` the three EA factions at 5; `SECURITY_INI`,
`SECURITY_CNC_CRIT_MOD`, `SECURITY_BOARDING_SHIFT`, `applySecurityOfficer`, `boardingShift`; `price` and `craftPrice` count the
percent in tenths through `pctTenths`, and `pctFor` / `shipPrice` no longer cast it to int), `ShipSystem.php`
(`getMarineRollMod` adds the shift - Wreak Havoc, Sabotage, Rescue and the boarders' Capture roll all read it;
`doCaptureMission` takes it off the defenders'), `specialWeapons.php` (`Marines::getDeliveryRollMod`,
`GrapplingClaw::getDeliveryRollMod`), `customTrek.php` (`CombatTransporter::getDeliveryRollMod` - no hull mounts one today,
every use is commented out). Client: `officers.js` (the registry row, `stats` iniativebonus 5). Docs: an Expert Security Officer
card in Ship Officers, his initiative in Seeing your officers, and a -20% line in both of the FAQ's Boarding Actions modifier
lists. Statics and bundles rebuilt; no new PHP class.

**As built:**
- ⭐ **`Officers::boardingShift($ship)` is THE boarding shift**: 2 while he serves on `$ship`, else 0. Five call sites, all one
  line: three delivery rolls (`+`, on the target), `getMarineRollMod` (`+`, every mission roll and the boarders' Capture roll) and
  `doCaptureMission` (`-`, the defenders). ⚠️ A new boarding roll anywhere should ask it too. No `$officersPresent` gate: these
  rolls are rare, and `isActive` on a ship without officers is an empty loop.
- No client mirror for the boarding shift or the critical modifier: the client previews only a boarding unit's ATTACH chance,
  which no crew quality affects, and displays no critical modifier.
- **Prices with a decimal.** `price()` was ceil(hundredths x pct / 10000); it is now ceil(hundredths x tenths / 100000) - for a
  whole percent the same number by construction, and proved so over every percent 1-100 on 432 costs, rounded both ways, and for
  `craftPrice` at 1 and 6 craft. officersHarness's faction-table check now accepts a percent with one decimal place, and also
  checks the registry's own.

**Measured.** 1,818 offers in the statics (92 Earth Alliance, 1,726 others) - every hull with a C&C that may carry officers,
the Helmsman's reach. Statics +98,199 B uncompressed.

**Verified.**
- `officersHarness.php` **882 / 0 with the database**, 814 / 0 with `--no-db` (new: offers and prices on six hulls across all
  three EA factions and three others; 7.5% arithmetic, both roundings, and "up" for the smallest fraction; the whole-percent
  identity sweep; no offer without a C&C, on an Ancient or an OSAT; the
  post, the first of two C&Cs; +5, -2 on his C&C only - a second C&C and a SecondaryCnC untouched; stacking with a Helmsman and
  Elite Crew; the kill roll through the REAL `testCritical` - 20 reads 18 and he and the Helmsman beside him survive, 22 reads 20
  and both die; killed last turn: nothing; the enemy payload; the mission modifier on four mission classes; the delivery modifier
  for a pod, a claw and a transporter; a REAL Wreak Havoc roll (a 7 stays aboard without him, is eliminated with him); a REAL
  Capture combat - the boarder's 4 and the defenders' 6s - with and without him).
- `officersClientHarness.js` **311 / 0** (7 new: the registry row and order, the lobby list and initiative alone and with a
  Helmsman, a pre-battle destroyed C&C, a server-built list not added twice, the REAL SystemInfo line serving and killed).
- **Mutation-tested: 19 breaks (16 server, 3 client), all caught**, files byte-identical afterwards. Two (the ceil constant
  in `price` and `craftPrice`, off by a few units) first survived: with whole-point costs the remainder is never 1-9, so they
  change nothing real. A check that 0.1% of 0.01 points still costs 1 now kills both.
- `enhancementsHarness` **275 / 0** - re-recorded (old copy `fingerprint.before-officers-stage13.txt`) after proving the change
  is only the 15 OFF_SECU offers and the 30 new case blocks (`secu`, `secu+helm+elite1`): with those stripped, the new
  fingerprint is identical to the old - so the price rewrite moved no existing price either. `enhancementsDifferential.js`
  **637 / 0** (30 new). `fvbuild -Check`: autoload current, validator clean, replay **153 / 0**. Bundles rebuilt
  (`-Client`) and the served copy synced (`-Sync`).

**Observations, not changed:**
1. The FAQ's Capture paragraph describes "two rolls" per attacker (one to kill a defender, one to be eliminated, at 25%); the
   code rolls one d10 per attacker and one per DEFENDING marine unit. The Security Officer's -2 is on the latter.
2. Not tested in a real game. Worth a play test: buy him on an EA ship (5%) and a non-EA one (7.5%), check the initiative in the
   ship window, then board his ship with a pod and read the combat log's rolls.

### 9.20 Stage 14: Expert Religious Leader (2026-10-17)

**Rules text (user, 2026-10-17) - a 28th officer, not in the original brief.** "Higher level members of the religious caste
provide a morale boost to their crews, simply by virtue of their presence aboard ship. In addition, if they are killed, wounded
or captured, this can stir the crew into a berserker frenzy. A religious leader provides an automatic +10 to the ship's
initiative. In addition, each weapon the ship fires scores +1 damage per volley (not per sub-volley or per die). If he is
wounded, captured or killed, the initiative bonus is doubled, and the ship receives + 1 to its sensors and +5 to its defense
profiles thereafter (note that + 5 to the defense profile is not a bonus, but a penalty). Location: Any primary non-weapon
system. (Default to CnC when bought but can be moved to other non-weapon Primary Locations during the game) Cost: 15% of the
ship's base value. Availability: Minbari, Markab, Corillani and Yolu factions." The same request asked for the Ship Officers and
Fighter Officers cards of the Ship Options document (`ammo-options-enhancements.php#shipofficers` / `#fighterofficers`) in
alphabetical order.

**Rulings (user, 2026-10-17, asked before building):**
1. "+10 initiative" and "+5 to its defense profiles" are **FV's displayed units** (FV shows both x5): +10 (+20 once he is lost),
   and the profiles **+1 point** each (+5% to be hit) - Poor Crew's and the Geneticist's way.
2. A "volley" is **a hit**: +1 on each pulse of a pulse weapon, on each shot of a multi-gun weapon, once on a raking shot (not
   per rake), never per die.
3. Once he is lost the ship **keeps** the +1 damage - the frenzy adds to what he gave.
4. **Minbari Federation**, Markab Theocracy, Corillani Theocracy and Yolu Confederation - **not** the Minbari Protectorate.

**Built with defaults, stated here (ask before changing):**
- "Wounded, captured or killed" = **disabled or killed** in battle; FV models no officer capture. The frenzy starts the turn after
  (the turn his bonuses would end - D3) and lasts the rest of the battle. **Pre-battle damage causes no frenzy**: a post destroyed
  before battle means he never served (out 0), so he gives nothing at all - and the lobby needs no frenzy preview. *(Stage 15,
  §9.21: such an officer is now removed and refunded in the lobby; the game's out-0 path remains only for an old fleet or client.)*
- **+1 sensors** = +1 output on the strongest Scanner standing at the start of the turn (not destroyed by turn - 1, output above
  0; the first in construction order on a tie) - Improved Sensors' scanner, among the living, since a destroyed one would swallow
  the point.
- The +1 damage never lands on a hit whose roll is 0 (the Electro-Pulse Gun, Stun Beam, Gravity Net, Molecular Flayer and Marines
  roll none - their effect is something else) and never on a ram ("each weapon the ship fires").
- **Where he may move**: any system of the Primary section (location 0) that is not a weapon or Structure and can be targeted
  (`isTargetable` - an officer in a technical system could never be disabled or killed). ⚠️ The jump drive is a `Weapon` subclass
  in FV (it "fires" to open a jump point) and a drive in the rules: it counts. Thrusters, hangars, jammers and tractor beams count
  where a hull puts them in the Primary section. Stage 10's rules otherwise: one move a turn, serving nowhere that turn, never
  into a destroyed system or one another MOVABLE or placed officer holds (a Helmsman or Technician just shares the post).
- **The turn he moves** he serves nowhere (Stage 10): no +1 damage on that turn's hits. His +10 was already in the initiative
  rolled at the start of that turn, so that turn's order keeps it; the displayed initiative drops on that turn's later loads.
- Frenzy numbers are public: `iniativebonus`, `forwardDefense` and `sideDefense` go to every viewer while he is lost
  (`lostPublish`); a scanner's output always does. The officer stays hidden.
- The owner's weapon tooltips show the +1 on their Damage line (lobby and game), after a Laser Technician's or Plasma
  Scientist's raise; a line whose lowest roll is 0 keeps its 0.

**Framework additions** (the first officer whose LOSS does something, and the first ship-level officer with more than one
post): registry keys `lost` (a method `apply()` runs on every turn after he was disabled or killed in battle, in place of
`apply`), `lostPublish` (`publish`'s list for those turns) and `movesTo` (a ship-level movable officer's further posts, asked by
`postAs`); `Officers::isLost` ⚠️ MIRROR PAIR with `officers.isLost`. Officers without the new keys behave exactly as before.
*(Stage 15, §9.21, retired `movesTo`: away from his C&C he is now the placed twin OFF_RELIP, the Matter Weapons Expert's pattern.)*

**Files.** Server: `Officers.php` (registry `OFF_RELI` after OFF_SECU - now the last fixed-post ship officer - `cnc`, 15%,
`apply` `applyReligiousLeader`, `publish` iniativebonus, `lost` `applyReligiousFrenzy`, `lostPublish` iniativebonus /
forwardDefense / sideDefense, `movable`, `movesTo` `isPrimaryNonWeapon`; `$factionRules` `only` the four factions;
`RELIGIOUS_INI`, `RELIGIOUS_DAMAGE`, `FRENZY_SENSORS`, `FRENZY_PROFILE`, `isLost`, `isPrimaryNonWeapon`, `applyReligiousLeader`,
`applyReligiousFrenzy`, `religiousDamageBonus`; `apply()`, `addForJson()` and `postAs()` take the new keys), `weapon.php`
(`getFinalDamage` adds `religiousDamageBonus` beside the laser boost and the plasma bonus - it runs once per hit, which is his
"per volley"). Client: `officers.js` (the registry row with `movable` and `stats` iniativebonus 10, `isLost`,
`religiousDamageBonus`, `modeOneDamage`, `religiousDamageLine`; `adjustDataDisplay`'s Damage branch now chains the laser or plasma
line, then his). Docs: an Expert Religious Leader card; both officer card lists sorted alphabetically (the cards moved verbatim -
same lines, other order); him in Moving an officer (with a note that officers who never move share a post) and Seeing your
officers. Statics, bundles and the served copy rebuilt; no new PHP class.

**Measured.** 71 offers in the statics (Minbari Federation 34, Corillani 14, Yolu 13, Markab 10; none for the Protectorate).
Statics +3,877 B uncompressed.

**Verified.**
- `officersHarness.php` **951 / 0 with the database**, 882 / 0 with `--no-db` (69 new: `isLost` itself; offers and prices on five hulls across the four factions, none
  on the Protectorate, Earth Alliance, Dilgar or Pak'ma'ra, a doctored row; the post; +10 and nothing else while he serves; +1
  through the REAL `getFinalDamage` on a Fusion Cannon and a Neutron Laser (raking - one roll), 0 on an Electro-Pulse Gun, a roll
  of 0 and a ram, the gate; the enemy payload (+10, no profiles, no officer); a C&C destroyed before battle - nothing; killed or
  disabled last turn - +20, profiles +1, the Scanner +1, the +1 per hit kept; killed this turn - +10, no frenzy yet; three turns
  on, still frenzied; two equal Scanners - the first gets it, the second once the first is destroyed; the frenzied enemy
  payload; moved to the Reactor, the turn of the move, back to the C&C, lost at the Reactor - frenzied; refused: a weapon, the
  Primary Structure, a front thruster, an untargetable system, a destroyed one, the Retlata's Primary Fusion Cannon; the Initial
  Orders targets (Engine, Hangar, Jammer, Jump Engine, Reactor, Scanner, Tractor Beam on a Sharlin; the Retlata's two cargo bays and
  two Primary thrusters among its ten); `saveTransfers`' note).
- `officersClientHarness.js` **328 / 0** (17 new: the registry row and order, the lobby list and initiative, a pre-battle
  destroyed C&C, a server-built list not added twice, `isLost`, `religiousDamageBonus` in every state, the Damage line alone and
  over the Laser Technician's and Plasma Scientist's, `movableOn`, the REAL SystemInfo's Damage line and officer line, serving and
  killed).
- **Mutation-tested: 37 breaks (25 server, 12 client - one of them through the differential), all caught**, files byte-identical
  afterwards. Two first survived and got a test each: `isLost`'s `>=` turned `>` (equivalent in every caller, which asks `serves`
  first - now checked directly, as its client mirror is), and the Weapon test removed (the Sharlin has no Primary weapon - now
  the Retlata's).
- `enhancementsHarness` **276 / 0** - re-recorded (old copy `fingerprint.before-officers-stage14.txt`) after proving the change is
  only the Tinashi's OFF_RELI offer and the 30 new case blocks (`reli`, `reli+helm+elite1`): with those stripped, the new
  fingerprint is identical to the old. `enhancementsDifferential.js` **639 / 0** (2 new). `fvbuild -Check`: autoload current,
  validator clean, replay **153 / 0**. Bundles rebuilt (`-Client`) and the served copy synced (`-Sync`).

**Observations, not changed:**
1. An officer whose loss is worth more than his service invites feeding him to the enemy: moved into a fragile primary system,
   a Religious Leader is soon disabled, and the frenzy's +20 initiative and +1 sensors are the rule's own trade-off for +5% to be
   hit. Built as written.
2. Not tested in a real game. Worth a play test on a Minbari ship: the initiative in the ship window (+10), a weapon tooltip's
   Damage line +1, a move to the Reactor in Initial Orders, then the turn after his post falls: initiative +20, profile 70/80 for
   65/75, the Scanner one higher in the EW display.

### 9.21 Stage 15: moving officers in Fleet Selection, and every officer on a destroyed post refunded (2026-10-17)

**Request (user, 2026-10-17).** Let the player move the Expert Religious Leader in Fleet Selection "to freely place him in any
viable system (since moving during game carries a disadvantage)", the same method serving the weapon-placed officers "if players
change their mind during Fleet Selection"; "the 'Move' button should be incorporated carefully so as not to ruin the format of the
System Enhancements menu".

**Rulings (user, 2026-10-17):**
1. **Any system destroyed by pre-battle damage that contained any officer is refunded**, "for simplicity" - QX5 re-ruled
   (§4.9): a ship-level officer used to stay aboard disabled, with a warning.
2. **A move in Fleet Selection is free.**

**Built with defaults, stated here (ask before changing):**
- The Religious Leader can also be **bought directly** in any primary non-weapon system's menu (his twin's offer row there), as
  the Gunner is on any weapon - a side effect of storing his post as a per-system row.
- **Move lives in the Officers section's footer** - the "Officers: n pts" line, with Move at its left. A system hosts one
  officer who holds a post, so the footer's Move can only mean him, and every row above keeps its one control shape. The C&C
  officer (bought in the buy window) gets a row of his own in the C&C's menu, `fixed`: the same ticker, inert (Edit removes
  him), so Move has someone to move. Measured on a Sharlin: his row 28px with one box and two buttons, exactly the Hardened
  Armour row's; the footer one line of 23px (about 5px taller than the bare total line, for the 16px Move button); the menu
  283px wide, inside its 300px cap.
- Move closes the menu and outlines every post he could take (the game's pick, its Officers-blue outline); the next click on
  one moves him, a click on another system or on the page calls it off. No passive notice: the lobby has none
  (`officers.notice` is game.php's), and the outline is the prompt.
- **A move needs no out-of-action check in the lobby**: a weapon-mounted Matter Weapons Expert on a weapon damaged before the
  battle (disabled from the start - damaged, not destroyed, so ruling 1 does not refund him) may be moved to a sound weapon.
- **The server keeps trusting ship-level rows** at buy time: an honest lobby refunds the officer; a stale or doctored client
  that sends one on a destroyed post pays for an officer who never serves (out 0), as before. Placed rows on a destroyed
  system are dropped by the buy-time sanitiser (D11), as they always were.
- A saved fleet is swept on load (refits too, which the buy would drop anyway), with one notice - a fleet saved before ruling 1
  can hold such an officer. Fighters are never swept: pre-battle damage never destroys a fighter (a lost one is a smaller flight).

**Design.**
- ⭐ **`OFF_RELIP`, the Religious Leader's placed twin** - the Matter Weapons Expert's pattern (OFF_MWE / OFF_MWEW): the same
  officer (`sameAs` OFF_RELI: his faction rules, his price, one or the other), every effect his (`apply`, `lost`, `publish`,
  `lostPublish`), posted in any primary non-weapon system but the C&C his ship-level self holds (`hosts` `isReligiousPost`; new
  registry key `nonWeapon` lets `Officers::mayHost` accept a non-weapon). A per-system row gives him what the lobby needed - a
  stored post - and saved fleets, the D13 name check, the D11 refund and the buy-time sanitiser come with it. In game he moves
  between the two ids through `postAs`, so Stage 14's `movesTo` key is gone.
- ⭐ **The lobby move is client-side and writes rows** (`officers.lobbyMove`): a placed row re-filed to the new system, or the
  ship-level row traded for a twin row and back - off his post first (a twin is refused while his C&C self is still bought),
  then on; put back if refused. `officers.lobbyPostAs` is the rule (⚠️ a MIRROR of `Officers::postAs` and `mayMoveTo`'s occupancy:
  a post he could be bought on, never his own, a destroyed one, or one another placed or movable officer holds - a Helmsman just
  shares it; the ship cap not counting himself). The server needs nothing new: every row is re-checked at buy, save and reload.
- ⭐ **`officers.setShipLevelRow` is THE way a ship-level officer row changes outside the buy dialog**: its count, `pointCostEnh`
  and `pointCost` by one delta - the invariant pointCost = hull + pointCostEnh + pointCostEnh2 + pointCostSysEnh, and what the
  next Edit recomputes from the rows anyway. A twin row moves `pointCostSysEnh` and `pointCost` (as `settleRefitCost` does).
- `officers.dropDestroyedPosts` is ruling 1's ship-level half, beside `systemEnhancements.dropDestroyed` (D11) at its three
  sites: the system menu's `refresh` (every damage write), `doEditShip`, and now `doLoadFleet`. One warning names both.

**Files.** Server: `Officers.php` (registry `OFF_RELIP` after OFF_MWEW; `nonWeapon` documented; `mayHost` - `nonWeapon`, `hosts`
takes the ship too, the sameAs officer's per-hull opt-out; `postAs` without `movesTo`; `isReligiousPost`;
`religiousDamageBonus` by `rulesId`), `Enhancements.php` (per-system registry `OFF_RELIP`, `sysEnhEligibleOfficerRELIP`,
`sysEnhPriceOfficerRELIP`). Client: `officers.js` (registry `OFF_RELIP`; `destinations` and `pickTarget`/`startPick` branch for
the lobby; `moveFamily`, `shipLevelRow`, `lobbyPostAs`, `lobbyDestinations`, `lobbyEntryAt`, `setShipLevelRow`, `lobbyMove`,
`afterLobbyChange`, `dropDestroyedPosts`, `decorateMenuRows`; the two "one place" messages name Move), `systemEnhancements.js`
(the label; `menuRowsFor` hands its rows to `officers.decorateMenuRows`), `SystemEnhancementsSection.js` (the `fixed` row, the
Officers footer with Move), `ApplyDamageMenu.js` (`startMove`, the ship-level sweep in `refresh`), `gamelobby.js` (the sweep in
`doEditShip` and `doLoadFleet`, a page click calling a pick off). Docs: Buying officers (a system you choose, Move), Losing an
officer (refunded), Moving an officer (Fleet Selection), the Religious Leader's card. Statics and bundles rebuilt.

**Measured.** 437 OFF_RELIP offers in the statics (Minbari Federation 241, Corillani 86, Yolu 64, Markab 46), one per primary
non-weapon system but the C&C of the hulls offered him. Statics +11,204 B uncompressed.

**Verified.**
- `officersHarness.php` **978 / 0 with the database**, 909 / 0 with `--no-db` (27 new: the twin's registry entry; offers on a Sharlin's 7 primary
  non-weapon systems, not its C&C, at his price, limit 1, cap 1; a Retlata's 10, not its Primary Fusion Cannon; none for the
  Protectorate, Earth Alliance or Dilgar, none with OFF_RELI opted out; the Gunner still weapons-only; a twin row posted, +10, +1
  per hit, its frenzy and its enemy payload, the price equal to the C&C one; a doctored twin + C&C row, a twin row on the C&C or a
  weapon; the sanitiser keeping one and dropping one on a destroyed Scanner; in game from the Reactor, the C&C among his targets;
  and Stage 14's move tests now reading OFF_RELIP away from the C&C, plus a Reactor-to-Engine move).
- `officersClientHarness.js` **361 / 0** (33 new: the twin; destinations from the C&C and from a twin post, and none to a C&C
  whose hull does not offer him there; the C&C menu's fixed
  row and the twin row's Move; C&C -> Engine -> Reactor -> C&C with the three buckets checked each time; refusals; the Gunner's
  move and caps; the Matter Weapons Expert C&C <-> weapon; the pick - outline, move, call-off, cancel; `dropDestroyedPosts`,
  its warning, one way, placed and flights left alone; the REAL SystemEnhancementsSection - Move beside the total, rows the same
  shape, greyed when nowhere, the fixed row read-only).
- `officersE2E/lobby15.mjs` (new, standalone, real bundles and clicks) **18 / 18** at 1600x1000 and at 700x900: the C&C menu's
  shape measured, Move, the 7 outlines, C&C -> Engine -> C&C free, a Gunner bought and moved, a pick called off, the C&C
  destroyed refunding him with the warning. `lobby8.mjs` 24 / 24.
- **Mutation-tested: 37 breaks (12 server, 22 client, 3 through `lobby15.mjs` with the bundles rebuilt each time), 34 caught**;
  the 3 left change nothing (`lobbyPostAs`'s own-post test - the occupancy test already refuses his own post;
  `dropDestroyedPosts`' flight test - a flight's officers are all skipped anyway; the fixed row's `+` - already disabled at its
  maximum). Files byte-identical afterwards. Two first survived and got a test each: the C&C as a destination on a hull without
  his ship-level row, and the twin's lobby initiative - ⚠️ the differential compared NO ship field for a placed case until now
  (no placed officer moved one before the twin); its placed cases now send and compare initiative and both profiles.
- `enhancementsHarness` **277 / 0** (fingerprint unchanged - it covers ship-level offers only); `enhancementsDifferential.js`
  **642 / 0** (3 new twin cases; the 52 placed cases now compare initiative and profiles too). `fvbuild -Check`: autoload
  current, validator clean, replay **153 / 0**. Statics and bundles rebuilt, served copy synced.

**Observations, not changed:**
1. Every primary non-weapon system of a Minbari, Markab, Corillani or Yolu ship now shows an Officers row for him in its menu,
   as every weapon shows the Gunner's.
2. Not tested in a real game: buy him, move him in Fleet Selection, Ready, and check he serves where he was put.

### 9.22 Stage 16: EM hardening rebuilt to the tabletop rules, and the Expert Surge Officer (2026-10-18)

**Rules text (user, 2026-10-18) - the officer.** "An example of Ipsha specialization in electromagnetic technology, this officer
optimizes his ship's EM weapons as well as its resistance to similar devices. The following advantages are gained. All
electromagnetic-category weapons add +1 damage. A surge cannon adds +1 for each weapon involved in any combined shot, so a
five-weapon blast would score +5 damage. EM pulsars receive +1 damage per pulse. Capital ships and larger receive +2 points of
power, while medium ships and HCVs get +1 extra power. LCVs and smaller units gain no extra benefits. Electromagnetic weapons that
hit the ship suffer a -3 penalty (not -2) to their critical hit roll against any affected system. If the hit forces the
deactivation of a system, the system resists the effect if it rolls a 1 or 2 on a d6. Any spark field affecting the ship reduces
its damage by 1 point. Cost: 10%. Location: Mag-gravitic reactor. Availability: Ipsha only."

**Rules text (user, 2026-10-18) - EM hardening**, supplied when the first ruling round showed FV's to be a simplification:
"Any electromagnetic weapon suffers a -2 reduction to its critical hit roll against an affected system. Similarly, any fighter hit
by an EM weapon has a -2 shift (bonus) to its dropout roll. These are cumulative with all other bonuses or penalties including those
caused by other weapons on the same turn. Any Ipsha fighter hit by an EM weapon which automatically forces dropout can roll a single
d6. If the roll is a 1 or 2, the fighter ignores the forced dropout effect. If the weapon also caused damage, the fighter will still
take this damage, and as a result may need to make a normal dropout roll in the proper sequence. An EM weapon which forces the
deactivation of a system permits that system a roll on a single d6. If a 1 is rolled, the system is not deactivated, but resists
the effect. If any damage was caused by the attack, this is still suffered normally. EM weapons which reduce power levels reduce
their effect by 1. For example, a shock cannon hitting an Ipsha ship and causing the loss of 2 points of power would instead only
lower it by 1 point. If only 1 point was to be lost, this is reduced to zero!"

**What FV had.** `WeaponEM::isTargetEMResistant` treated `$EMHardened` as Advanced Armor ("for simplicity"): hardened units were
immune to every EM special effect (critical bonuses, forced dropouts and shutdowns, power loss, sensor and initiative cuts), the
armour-ignoring EM weapons halved their armour instead, and every Ipsha hull carried a flat `critRollMod = -1` in place of the
tabletop's -2 against EM criticals. The Ring Base had no hardening at all.

**Rulings (user, 2026-10-18, asked before building):**
1. The officer's "-3 (not -2)" is the tabletop step where the rule puts it: on systems an EM weapon hit. Then, the user finding FV's
   hardening a simplification: **build the tabletop rules, apply the hardening correctly, and harden the Ring Base**; the officer on top.
2. The -2 is **once per system per turn** (the system's one critical roll), however many EM hits it takes; the EM weapons' own +X still
   add up.
3. EM effects the tabletop hardening does not list (sensor/OB and initiative cuts, the EM Bolter's +1 to all criticals) **apply
   normally** - "but please only comment out the lines with the previous EMHardened immunity with a note in case I need to revisit".
4. Armour-ignoring EM weapons **ignore Ipsha armour fully** (no more halving).
5. FV's flat stand-in **removed** from every Ipsha hull: ships and the OSAT -1 -> 0, the Anemone -2 -> -1, the Urchin -3 -> -2 (their
   own comments gave these values).
6. **His own ship's Spark Field gains the +1** (it is an EM weapon, and hits friend and foe).

**Built with defaults, stated here (ask before changing):**
- Availability "Ipsha only" = the `Ipsha Baronies` faction: not the Civilians' Ipsha-built Cargo Hauler (hardened, with a Mag-Grav
  reactor). 14 hulls are offered him - every Ipsha warship and the Ring Base; the OSAT, mines and fighters never are (D9).
- His post is the first Mag-Gravitic Reactor that can be hit (`maggrav`); every Ipsha hull has exactly one. A Technician shares it
  (the 'reactor' post picks the same system there), and both raises add up.
- Power: "capital ships and larger" = size class 3 or `$Enormous` (+2: the Battleglobe-style capitals on HCV layouts, the Ring Base),
  medium ships and HCVs = 1 and 2 (+1), LCVs 0 (none exist among the Ipsha).
- +1 per HIT (the Religious Leader's rule - `getFinalDamage` runs once per hit): each EM Pulsar pulse, each section a Resonance
  Generator strikes, a raking Surge Cannon once - +N on a combined shot of N cannons (its `firingMode`; a combine that falls back to
  single shots gets +1 each). Never on a roll of 0 (the Burst Beams and Electro-Pulse Gun; a Spark Field boosted down to 0 stays 0).
  Only shots in an Electromagnetic mode.
- The -2 (-3) also reaches a critical forced by a weapon (the Burst Beam's "+4 forced roll" becomes +2), and a fighter's dropout roll.
  It eases the kill roll of any officer posted in the system, as every critical modifier does.
- A resisted dropout or shutdown writes " EM hardening: dropout resisted." / "... shutdown resisted." into the shot's public log; the
  Surge Officer's 1-2 is not named there.
- Power losses: one point less per attack (a Burst Beam's Structure hit 1 -> none, a Medium Burst Beam's 2 -> 1, a Heavy's 4 -> 3, a
  Shock Cannon's floor(damage/4) - 1, the CW Ion Cannons' 2 -> 1 and 4 -> 3, the BSG EM missile's 1 -> none, the Neutron Burst's 2 -> 1
  and its per-powerReq loss one less). A resisted Neutron Burst shutdown also takes no power with it (that loss IS the deactivated
  system's power).
- Spark Field relief: on each hit any Spark Field (his fleet's, an enemy's, a mine's) scores on his ship, never below 0 - so his own
  field's +1 and another Surge Officer's -1 cancel.
- Stun Beam: it never used the old immunity (a contributor had kept Ipsha "still affected"); its weapon/jammer/jump-drive shutdown and
  its fighter dropout now get the hardening d6, its other effects stay as they were.

**Design.**
- ⭐ `WeaponEM` (specialWeapons.php) holds the tabletop rules: `isHardened`, `hardenedCriticalRoll` (one hook in `Weapon::doDamage`,
  beside its single `onDamagedSystem` call - so EVERY EM weapon gets the -2; none overrides `doDamage`), `resistsDropout`,
  `resistsShutdown`, `powerLoss`. The last three are called at each weapon site that forces a dropout or shutdown or cuts power; the
  per-system "once a turn" is a WeakMap of system -> turn.
- `isTargetEMResistant` keeps Advanced Armor and mines; its two hardening lines are commented out with the note the user asked for.
  Every other caller - the "other effects", the armour bases, the Masers (a Laser that borrowed the helper) - follows with no edit.
- The officer: `Officers.php` registry `OFF_SURG` after OFF_RELI (`maggrav`, 10%, `apply` `applySurgeOfficer`); `$factionRules` `only`
  Ipsha Baronies; `surgePower` (⚠️ MIRROR PAIR with `officers.surgePower`), `surgeDamageBonus` (in `getFinalDamage` beside the other
  officers' - ⚠️ MIRROR PAIR with `officers.surgeDamageBonus`), `surgeHardening` (asked by WeaponEM), `sparkFieldRelief`
  (`SparkField::beforeDamage`). No new payload: the reactor's `output` is public.

**Files.** Server: `specialWeapons.php` (WeaponEM; Shock Cannon, Burst Beam, Burst Pulse Cannon, Medium/Heavy Burst Beam, Electro-Pulse
Gun, Stun Beam, Neutron Burst; Spark Field relief), `weapon.php` (the hook; `getFinalDamage`), `customBSG.php` (Lt/Med/Hvy Guided
Missile S, EM Missile), `customCW.php` (Ion Cannon, Heavy Ion Cannon), `customDevelopment.php` (Ancient Burst Beam, Ancient Shock
Cannon), `customEscalation.php` (EM Torpedo, Electron Polarizer), `customNexus.php` (Light Burst Beam), `defensive.php` (EM Wave
Disruptor), `Officers.php`; the 21 Ipsha hull files (the stand-in commented out with a note and the header comment updated; the Ring
Base hardened). Client: `officers.js` (registry row; `findPost` `maggrav`; `reactorBonus` adds his power; `surgePower`,
`surgeDamageBonus`, `surgeDamageLine`; `hitBonusLine` shared with `religiousDamageLine`; `adjustDataDisplay` chains his line). Docs: an
Expert Surge Officer card (ammo-options.html, alphabetical), the Ipsha "EM Hardened" section (factions-tiers.html) and FV_factions.txt
(the Ipsha entry; the Streib note). Statics and bundles rebuilt; no new PHP class.

**Measured.** 14 OFF_SURG offers in the statics, all Ipsha Baronies (10% rounded up): Battleglobe 70, Battlehex 50, Boltglobe 75,
Carrier Cube 80, Heavy Carrier Cube 80, Jumpsphere 100, Resohex 60, Rhombus Escort 39, Scout Wheel 80, Surgesphere 105, Tetra Escort
40, Tetraship 35, Warsphere 100, Ring Base 320 - about 0.7 KB of statics.

**Verified.**
- `officersHarness.php` **1050 / 0 with the database**, 981 / 0 with `--no-db` (72 new). EM hardening: every Ipsha unit hardened with
  its tabletop critRollMod; `isTargetEMResistant` (hardened no, Advanced Armor and mines yes); through the REAL `doDamage`: an EM
  Pulsar's +1 and -2 on a C&C, a second hit that turn +1 only, the next turn's own -2, a Resonance Generator -2 alone, a laser nothing,
  an unhardened ship +1 only, an Urchin's dropout +2 -2; dropout saves (2 yes, 3 no, no die for an unhardened unit; a Burst Beam on an
  Urchin - resisted with its log line, or out with no fall-through to the forced-critical branch; a Shock Cannon); shutdown saves (Burst
  Beam 1 / 2, an unhardened ship with no die, a Heavy Burst Beam's 3-turn one); power (1/2/4 -> 0/1/3; Burst Beam, Heavy Burst Beam and
  Shock Cannon on Structure, against an Omega's full losses); a Comm Jammer's initiative cut now landing; Burst Beam armour 0 on Ipsha,
  still halved by Advanced Armor; a Maser's critical bonus applying. The officer: offers and prices on four hulls, none on the Cargo
  Hauler, the OSAT, an Omega or an Urchin flight, a doctored row; the post, and a technical reactor skipped; power +2 / +2 / +1 / +1 on
  a Carrier Cube, the Ring Base, a Battlehex, a Tetraship, none on an LCV, +2 on anything enormous; +1 through the REAL
  `getFinalDamage` on an EM Pulsar, a Resonance Generator, a single Surge Cannon, +5 and +3 on combined shots, his own Spark Field; 0
  on a roll of 0, a ram, without him, a Burst Beam; the gate; Spark Field relief through a real `SparkField::beforeDamage` (an enemy's
  field -1, his own +1 on others and 0 on his ship); the enemy payload (raised output, no officer); killed last turn - nothing; his
  hardening -3 once, on his reactor too, shutdown 2 resisted / 3 not, gate off, without him or killed back to -2 and 1.
- `officersClientHarness.js` **379 / 0** (18 new: the registry row and order; the lobby post and `maggrav` skipping a plain or an
  untargetable reactor; `surgePower`; `reactorBonus` capital / HCV / with a Technician / in game / destroyed; the REAL power.js
  `getReactorPower` +2; `surgeDamageBonus` in every state and mode-1 rule; the Damage lines (9 -> 10, 2-11 -> 3-12, a Spark Field's 0
  kept), with a Religious Leader's too; the REAL SystemInfo).
- **Mutation-tested: 44 breaks (34 server, 9 client, 1 through the differential), all caught**, files byte-identical afterwards:
  the immunity restored, the -2's size, its once-a-turn guard (removed, or forgetting the turn), the `doDamage` hook (removed, or for
  every weapon class), both saves' sizes and their unhardened guard, the officer's two easings, the power relief and four weapon
  sites' guards, the log line; every officer clause (combined shots, a roll of 0, the class test, the serving test, each size step,
  the reactor raise, the Spark Field relief in both places, `getFinalDamage`'s call, the technical-reactor skip, the faction rule, the
  gate) and their JS mirrors, one through the lobby/server differential. Two first went untested and got a check each before the run:
  the server's untargetable-reactor skip and its `$Enormous` clause. Two dead checks in `surgeHardening` (a BaseShip / FighterFlight
  test - every caller has already asked `isHardened`, and no flight carries him) were removed instead.
- `enhancementsHarness` **278 / 0** - re-recorded (old copy `fingerprint.before-officers-stage16.txt`) after proving the change is only
  the Jumpsphere's (an Ipsha capital among its hulls): its ship crit +1 (the stand-in gone), its payload hash (a shorter auto-note), its
  OFF_SURG offer - and the 30 new case blocks (`surg`, `surg+tech+rea`). `enhancementsDifferential.js` **644 / 0** (2 new, the
  Jumpsphere's reactor compared as the lobby shows it: output + `officers.reactorBonus`).
- `fvbuild -Check`: autoload current, validator clean, replay **151 / 2** - games **4248 and 4336**, the corpus's Ipsha games, differ
  ONLY in each Ipsha ship's auto-generated notes line "Critical roll modifier: -1" (the stand-in removed); no damage or critical row
  moved. Not re-recorded: shown to the user first.

**Observations, not changed:**
1. ~~The Medium and Heavy Burst Beams (and the Heavy Guided Missile S) create an `OutputReduced1` critical object but save it as
   `OutputReduced2` / `OutputReduced4`.~~ **Fixed the same day (user: "ensure consistency of effect")**: each is now built as the
   class it is saved as (`new $loss(...)`, the CW Ion Cannons' pattern). Every later load always read the saved name, so the player
   saw the full loss from the next turn on before and after; only the firing request's own object was the 1-point class. A
   whole-tree scan found no other critical built under another name. officersHarness checks the class of all three (1050 / 0), and
   reverting either file's site fails it.
2. The EM Torpedo and Electron Polarizer say "+2 to dropout rolls" but their line for it is `if ($ship instanceof FighterFlight) 2;` -
   a statement that does nothing.
3. Not tested in a real game. Worth a play test with Ipsha against Ipsha: Surge Cannon rakes on a hardened ship (+2 per rake -2 once),
   a Burst Beam's "shutdown resisted" line, then a Surge Officer: the reactor's power +2 in Initial Orders, an EM Pulsar's Damage line
   10, a five-cannon shot +5 in the log.

### 9.23 Stage 17: Expert Ion Technician (2026-10-18)

**Rules text (user, 2026-10-18).** "Expert Ion technician: This expert knows ways to channel power from the ship's ion engines into its
weaponry for added effect. The following abilities are provided: All ion-based weapons (including the ioniclaser) score +1 damage per
die, but no die can be increased above its maximum roll (e.g., a d10 would score 10 damage on a roll of either 9 or 10). Weapons scoring
a flat amount of damage, like the dual ion bolter or ion torpedo, add +1 only. Rad cannons score 12 damage on structure instead of 10
(this supersedes the above benefit), and force a critical roll if they hit the sensors, engine or reactor. If they hit C&C, the critical
roll is at +4 on the die instead of the listed +2. Using the ionic drive system, this expert can shunt thrust into one or more of the
ship's guns. This decision is made at the same time systems are activated or deactivated for extra power. Three points of thrust produce
an extra 1d10+1 damage, should the weapon score a hit. This power must be applied on the turn of firing, and if applied, the weapon must
fire on that turn (if it does not, it discharges and is treated as having fired anyway). This ability will not function on non-ionic
weapons or with those that score a fixed amount of damage. Weapons being charged in this manner will be detected (and their strength
known) by anything that detects special weapon arming modes, such as the Expert Analyst. Cost: 10%. Location: Engine. Availability:
Cascor only." The user's note: "There is already a method for using thrust to boost a weapon's damage, see Kirishiac Hypergravation
Blaster for an example."

**Rulings (user, 2026-10-18, asked before building):**
1. **One boost level per gun a turn**: 3 thrust, +1d10+1; more thrust is spent by boosting more guns ("into one or more of the ship's
   guns"). First ruled repeatable (every 3 thrust another 1d10+1) and built so; **re-ruled the same day, after the build**: "it is best
   if we limit the boost to just one level per Ion weapon".
2. The **discharge is built**: a boosted gun that does not fire that turn loses its charge as if it had fired, and its tooltip says it
   must fire (as recommended).
3. A **Sub Reactor counts as the reactor** for the Rad Cannon's forced critical (as recommended): `SubReactorUniversal` is not a
   `Reactor` subclass in FV, and a Rad Cannon did nothing to one before.

**Built with defaults, stated here (ask before changing):**
- **Ion weapons** = any weapon with an Ion mode - the Ionic Laser is Ion-class in FV - and the **Ion Torpedo**, which FV files as a
  Ballistic weapon (a `Torpedo`) though the rules name it: by class on the server (`instanceof IonTorpedo`), by `name` (`ionTorpedo`)
  on the client. On Cascor ships: Dual Ion Bolter, Ion Torpedo, Ion Cannon, Ionic Laser, Rad Cannon, Ion Field Generator.
- **"A flat amount of damage"** = the weapon's damage in the fired mode is one number (`minDamageArray` = `maxDamageArray`, which a
  ReducedDamage critical scales alike): the Dual Ion Bolter's 8 and the Ion Torpedo's 15 get +1 a hit (on each of a bolter's two guns);
  the rolled ones +1 per die through `Dice::$perDieBonus`, each die capped at its face. Never on a roll of 0, so the Rad Cannon ("this
  supersedes the above benefit") and the Ion Field Generator gain nothing.
- **Stacking**: with Elite Crew's +1 per die (+2 a die, still capped - Elite Crew is no officer). On a weapon a serving Expert Gunner
  holds, the Gunner's +1 per die replaces his +1, per die or flat - the Laser Technician's ruling (§9.17: only the flat bonus is the
  Gunner's to replace); his thrust boost and the Rad Cannon's rules still work there (they are abilities and the weapon's own effects).
- **Rad Cannon**: min(12, boxes left) on Structure; a Scanner keeps its output -1 and an Engine its -2, AND each rolls a critical; a
  Reactor or Sub Reactor rolls one; the C&C at +4 instead of +2. The forced rolls are the systems' ordinary ones (d20 + their damage +
  their modifiers, no extra term), through Pass 1 (`isDamagedOnTurn` honours `forceCriticalRoll`). Only his own ship's Rad Cannons.
- **Thrust boost**: only on an ion gun whose damage is rolled (the Ion Cannon and Ionic Laser on Cascor hulls - "will not function ...
  with those that score a fixed amount of damage"); only on a gun ready to fire this turn ("must be applied on the turn of firing" - the
  buttons are sent only then, since a boost on a gun still charging would only discharge it); exactly 1d10+1 a level - neither his +1 per
  die nor Elite Crew's touches the boost's die; added once a turn, to the gun's first hit (no Cascor gun hits twice); a hit on a
  Chameleon simulacrum alone rolls it but spends nothing (the Laser Technician's rule).
- **Discharge**: an interception counts as firing (FV's own `firedOnTurn`); a discharge is no firing for the Expert Gunner's faster
  reload (`fasterReload` reads the fire orders); a gun switched off loses its loading anyway.
- **Hidden** from opponents, as the Laser Technician's boosts: the rules say only "anything that detects special weapon arming modes,
  such as the Expert Analyst" sees the charge, and the Analyst is not built. ⚠️ His stage should reveal what `ionForJson` (and
  `laserForJson`) strip.
- **Availability**: "Cascor only" = `Cascor Commonwealth` - not the Civilians' Cascor-built Calacca and Caracti. Only on a hull carrying
  an ion weapon (`needs`: the rules do not say so, but he would do nothing; every Cascor warship carries some). 10% of the base cost.
  Post: the strongest Engine - the Traqintor bases and the Ruqacc OSAT have none (and an OSAT never carries officers).

**Design.**
- ⭐ **The thrust boost IS the generic boost, priced in thrust** - the Hypergraviton Blaster's path (the user's note) on the Laser
  Technician's payload pattern. `Officers::ionForJson`, from `weaponForJson`, tells the owner's team the gun is `boostable` (while he
  serves and it is loaded) at `boostEfficiency` 0 with `thrustBoosted` and `thrustPerBoost` 3; movement.js `getRemainingEngineThrust`
  already takes every thrust-boosted system's boosts off the engine, and the Initial Orders commit already refuses a ship whose thrust
  goes negative (DK's check for the Blaster). No new UI, storage or client cost logic; the server sets nothing on the weapon object and
  trusts boost entries as ever - `ionBoostDamage` answers a doctored one at damage time.
- **One level per gun** (ruling 1): `Officers::ION_BOOST_MAX` - `ionBoostDamage` counts at most one level, so a doctored entry of more
  adds nothing - ⚠️ MIRROR PAIR with `officers.ION_BOOST_MAX`. The client's own cap (`system.hasMaxBoost()` + `maxBoostLevel`) is a
  per-class JS prototype method that the ion guns do not override, so `power.js clickPlus` asks `officers.ionBoostFull` beside it (the
  `efficientTransferBonus` precedent of an officer hook in power.js) and refuses a second level with the usual "You can not boost this
  system any further."; `officers.ionBoost` reads at most one level, so the tooltip agrees with the server.
- **Damage**: `ionDieBonus` joins the Gunner's in getFinalDamage's `$dieBonus`; `ionFlatBonus` and `ionBoostDamage` sit beside the other
  officers' after `getDamageMod`. The boost's once-a-turn record is in memory (a WeakMap), as the laser pool is.
- ⭐ **Discharge**: one condition beside `firedOnTurn` in `Weapon::calculateLoadingFromLastTurn` - `Officers::ionDischarged` - so the
  turn change writes the loading row a firing would. It reads only Initial Orders power entries and the declared orders, both present
  in the pre-firing gamedata the turn change runs on (§9.16). The client follows the loading row: no mirror.
- **Rad Cannon**: `Officers::improvesRadCannon($shooter)`, asked once in `RadCannon::onDamagedSystem`.
- Shared now: the Laser Technician's boost stripping is `stripBoosts`; the client's "a Gunner serves on this weapon" is
  `officers.gunnerServesOn` (laserDamageBonus and ionDamageBonus).

**Files.** Server: `Officers.php` (`ION_*` / `RAD_*` constants; `$factionRules` OFF_ION; registry `OFF_ION` after OFF_SURG - now the last
fixed-post ship officer - `engine`, 10%, `needs` `hasIonWeapon`, no `apply`; `isIonWeapon`, `isIonShot`, `scoresFlat`, `boostableIon`,
`ionServesFor`, `ionDieBonus`, `ionFlatBonus`, `$ionBoostSpent`, `ionBoostDamage`, `ionDischarged`, `ionForJson`, `improvesRadCannon`,
`hasIonWeapon`; `stripBoosts` out of laserForJson; weaponForJson calls ionForJson), `weapon.php` (`getFinalDamage`;
`calculateLoadingFromLastTurn`'s discharge), `specialWeapons.php` (`RadCannon::onDamagedSystem`). Client: `officers.js` (the registry
row; `needs` `ionWeapon`; `isIonWeapon` ⚠️ MIRROR PAIR, `isIonMode`, `ionScoresFlat` ⚠️ MIRROR PAIR, `ionDamageBonus` ⚠️ MIRROR PAIR,
`ionBoost`, `ionBoostText`, `ionDamageLine`, `radCannonSpecial`, `gunnerServesOn`; `adjustDataDisplay`'s Damage chain and the Rad
Cannon's Special text), React `SystemInfo.js` (the Thrust boost line). Docs: an Expert Ion Technician card in Ship Officers
(alphabetical, after the Helmsman) and his boosts in Seeing your officers. Statics, bundles and the served copy rebuilt; no new PHP class.

**As built - the owner's tooltip:** a fixed-damage ion weapon's Damage line +1 (8 reads 9); the Rad Cannon's 10 reads 12 and its Special
text is rewritten (12 boxes, the C&C at +4, Scanner and Engine criticals forced, a Reactor or Sub Reactor line) - each rewrite is matched
against the server's own sentence, so a reworded sentence is left as it is; a boosted gun's line + 2n on the low end and + 11n on the high
(12-30 boosted reads 14-41) and a **Thrust boost** line: "+1d10+1 damage if it hits (3 thrust). It must fire this turn, or it
discharges as if it had". His +1 per die is not shown on a rolled weapon's line, as Elite Crew's and the Gunner's are not (the client
cannot count dice).

**Measured.** 10 OFF_ION offers, all Cascor Commonwealth warships, 10% rounded up: Coqari 75, Crocti 42, Drocca 60, Nesacc 70, Norsca 70,
Norscator 83, Qoccata 95, Qoricc 50, Tacacci 44, Talacca 50. Statics +510 B uncompressed. 12 ion weapon names exist in the statics (the six
Cascor ones, the Balosian Improved Ion Cannon, the Nexus Ion Gun, Twin Ion Gun, Ion Bolter and Light Rad Cannon, the Shadows' Vortex
Disruptor); only Cascor ships carry him.

**Verified.**
- `officersHarness.php` **1132 / 0 with the database**, 1064 / 0 with `--no-db` (83 new): offers and prices on five hulls; none on the
  Civilians' Calacca, a base, the OSAT, an Omega, a flight, or a Norsca stripped of its ion weapons; the post; `hasIonWeapon`,
  `isIonWeapon` (the Ion Torpedo Ballistic, the Ionic Laser); the client's ion and flat tests against the server's over every weapon in the
  statics; through the REAL `getFinalDamage` - an Ion Cannon +2 (5, 5), 9 and 10 reading 10 and 10, the Ionic Laser +3, a Dual Ion Bolter
  and an Ion Torpedo +1, a Rad Cannon and an Ion Field Generator 0, Elite Crew +2 a die and still capped, the gate, killed, the Gunner's
  gun and bolter; the boost - one level, a doctored entry of 3 counting one, a d10 (300 seeded rolls span 2..11), the die untouched by Elite Crew, once a turn, the next
  turn's own, a phantom, the Ionic Laser, none on fixed guns, without him, killed, the gate, a Gunner's gun; the discharge through the REAL
  `calculateLoading` (boosted and unfired 1, unboosted 2, fired 1, an interception 1, last turn's boost, killed, without him, the gate) and
  `onAdvancingGamedata`'s loading row; the payload per viewer (a loaded gun boostable at 0 power and 3 thrust, a charging one priced but
  not boostable, fixed guns untouched, the enemy's stripped, game over, killed); the Rad Cannon through its REAL `onDamagedSystem` (12 /
  10 boxes, +4 / +2, the Scanner, Engine, Reactor and a Traqintor's Sub Reactor forced, a hangar and a weapon as before, 5 boxes left,
  killed, the gate).
- `officersClientHarness.js` **407 / 0** (28 new, on the Norsca's REAL static systems): the registry, `needs`, the ion and flat tests, the
  +1 in every state, the Damage lines, the boost and its text, the Rad Cannon's Special against the server's own wording, the REAL
  SystemInfo, the REAL power.js `clickPlus` (a first level taken, a second refused, another system uncapped by him), and the REAL
  movement.js `getRemainingEngineThrust` (with systems.js's own thrust-boost list): 12, one gun boosted 9, two 6, three 3.
- **Mutation-tested: 77 breaks (49 server, 26 client - two through the SystemInfo source, one through power.js - and 2 through the
  differential), 76 caught**, files byte-identical afterwards (the four for the one-level cap run after the re-ruling). The one left is equivalent: `scoresFlat` reading a mode the weapon lacks falls back to its
  current min/max, which on every single-mode ion weapon is the same number. Two went untested at first and got a check before the run:
  the boost die's size (the harness's forced dice ignore it - 300 seeded unforced rolls now cover it); and `needs` (every Cascor warship has an
  ion weapon - a Norsca stripped of its own now covers it).
- `enhancementsHarness` **279 / 0** - re-recorded (old copy `fingerprint.before-officers-stage17.txt`) after proving the change is only the
  Norsca's hull block (a new test hull - none was Cascor) and the 32 new case blocks (`ion`, `ion+eng+poor1`): with those stripped, the new
  fingerprint is identical to the old. `enhancementsDifferential.js` **686 / 0** (the Norsca's cases among them; a client post moved to the
  reactor, or the `needs` test broken, is caught). `fvbuild -Check`: autoload current, validator clean, replay **156 / 0** (game 4488 SKIP:
  surrendered since it was recorded - re-record it). Statics, bundles (`-Client`) and the served copy (`-Sync`) rebuilt.

**Observations, not changed:**
1. A Rad Cannon's Damage line reads 10 (its min/max, "for display and interception purposes") while it rolls 0, so a Religious Leader's
   client line would show it +1 though the server gives none. No Minbari, Markab, Corillani or Yolu hull carries one.
2. Play-tested by the user in game 4491 (below). Worth a further test on a Norsca: boost both Ion Cannons in Initial Orders (the Engine's
   12 thrust reads 6 in Movement; a second click on one is refused), fire them (+1d10+1 each in the log), then check the next turn shows
   them unboosted; boost one and hold its fire (it starts the next turn reloading); a Rad Cannon on an enemy's
   Structure (12 boxes).

**Play test 4491 (user, 2026-10-18) - a boost carried onto recharging guns.** Firing worked, but on the next turn both boosted guns were
still boosted, still took Engine thrust, and could not be un-boosted. Cause: at the start of every Initial Orders the client copies last
turn's power entries onto the new turn (`shipManager.power.repeatLastTurnPower` -> `copyLastTurnPower`), boosts included - FV's own
carry-over for every system. A gun that has just fired is recharging, so the server sent it no `boostable` (ionForJson), and the boost
buttons need it (SystemInfoButtons): nothing could take the copied boost off. Worse, once committed (4491's turn-2 rows were saved), the
discharge rule would have reset both guns' reload at every turn change, so they would never have fired again. The user's rule: **"if a
weapon is recharging (and therefore cannot fire that turn) we should remove its boost."** Built:
- **Client, every weapon** - `copyLastTurnPower` carries no boost onto a weapon that is not loaded this turn (`weaponManager.isLoaded`;
  the weapon's other entries, a switch-off, still carry). Checked first: every boostable weapon class reads its boost on the turn it
  fires - the Heavy Gravitic Bolt and Gravitic Cutter charge past their loading time but are loaded by then, the Sensor Charge
  Transceiver spends its boost only on a charge it sends - so a boost on a recharging weapon was only ever wasted. A drive that charges
  by boosting keeps its own rule (`getRepeatableBoost`, the Jump Engine - itself a Weapon); shields are not weapons.
- **Server, the discharge** - `Officers::ionDischarged` only for a gun that was loaded on that turn (calculateLoadingFromLastTurn asks
  before setLoading, so the weapon still holds that turn's loading): a boost on a gun that could not fire was never "applied on the turn
  of firing", from an old client, a doctored entry or a row already saved.
- Tests: officersHarness 1133 / 0 (a recharging boosted gun goes on charging, 1 -> 2), officersClientHarness 410 / 0 (the REAL
  copyLastTurnPower: a recharging gun gets no boost, a loaded one keeps it, a recharging gun's switch-off still carries, a non-weapon with
  a loading time and a charging drive carry as before). Mutation: 3 more breaks, all caught (one only after the non-weapon fixture got a
  loading time). `fvbuild -Check` replay 156 / 0. Bundles rebuilt, served copy synced.
- ⚠️ Game 4491 itself: its turn-2 boost rows (Norsca #3, systems 24 and 25) were saved before the fix, so that turn's Movement still
  shows the thrust taken; the server no longer discharges either gun for them.

### 9.24 Stage 18: Expert Pilot (2026-10-19)

**Rules text (user, 2026-10-19).** "Expert Pilot: Pilots all receive a certain amount of training, but some are so skilled, experienced
or simply gifted that they rank among the elite. Pilots provide the following benefits to their flight: Offense: +1 (+5% hit chance in
FV) to the flight's offensive bonus. Jinking: The flight can use one level of jinking without paying any thrust cost for it. Additional
levels require the standard amount of thrust (calculated as though this "free" level were not being used). The bonus level does not
count against jinking limits for that fighter class. Point Cost: Equal to 100% cost of a fighter. Limitations: Up to four expert pilots
in the same flight are cumulative, except for Raider flights, which can use no more than two due to poor training methods." The user's
notes: up to 4 (2 for Raiders), never more than the flight's size; the first officer a flight may carry several of, so each goes in his
own craft from the first, and his bonuses go when his craft disengages or is destroyed; the jinking bonus is "effectively +1 to jinking
level in game (be careful in gamelobby, I know jinking is used as a stat to identify heavy, medium and light fighters etc), and the first
level of Jinking costs no thrust". With it, for the Expert Evader and the Pilot: "An Expert Evader can use his evasion ability to reduce
sparkfield damage by 1 point on each fighter in his flight, and an Expert Pilot will also reduce damage by 1 point if he forgoes all his
other benefits for the entire turn. These benefits are cumulative, so, an elite flight (with four pilots and an evader) could
theoretically earn a maximum of -5 on spark field damage, but this would be an extremely expensive proposition."

**Rulings (user, 2026-10-19, asked before building):**
1. **Pilots ARE the flight's one officer** (D9 stands): 1-4 pilots, or one other officer, never both. The Spark Field text's "four
   pilots and an evader" therefore cannot happen in FV.
2. **Price: §9.9's scaled rule** - 100% of a craft per pilot, for a flight of six, scaled by the flight's size.
3. **The Spark Field relief is automatic**: "if flight doesn't jink or fire that turn they can get the damage reduction" (offered: a
   per-turn toggle in Initial Orders, a count of pilots who forgo, or deferring the clause).
4. **"Raider" flights = the `Raiders` faction**, the one the Scavenger's price and the Jump Officer's ban already name - not the two
   Escalation Wars raider factions.

**Built with defaults, stated here (ask before changing):**
- Every benefit is per SERVING pilot and cumulative: +1 OB, one free jinking level and +1 to the jinking limit each.
- "Fire" = any of the flight's fire orders that turn except an interception (`intercept`, `selfIntercept`) or the `meteorDefence`
  pseudo-order - an interception spends neither the OB nor a jink. A ballistic launch declared that turn counts; jinking or firing on an
  earlier turn does not.
- The relief is per pilot serving that turn, taken off each fighter's hit after its getDamageMod (shields), never below 0.
- The free levels stay the player's choice (a jink still costs the flight's own fire its penalty): the first N levels taken cost 0;
  taking a level off removes one that cost thrust first, so what is left costs what it would have.
- The limit: 4 (or the faction's `max`), and never above a set maxFlightSize, in the offer; the buy dialog also holds the count to the
  flight size it shows and takes extra pilots off when the size goes down; the server caps at the craft the flight has.
- A lobby flight shows one craft card, so all its pilots' insignia sit there (title "Expert Pilot (3)"); the lobby's gold line reads
  "Expert Pilot (3)", and the fleet list "Expert Pilot (3)" (describeTaken's own count). In game each pilot's card carries his own.
- Shuttles and breaching pods are offered him; Hunter-Killers, Ancients, mines and MicroSATs are not (D9, as every fighter officer).
- The Star Wars fighters' `ELITE_SW` "Elite Pilot" enhancement is something else and stacks with him.

**The Evader's half of the clause was already built.** His "-1 damage on all incoming fire" is `FighterFlight::getDamageMod`, which
`Weapon::getFinalDamage` subtracts, and `SparkField::beforeDamage` runs getFinalDamage once per fighter - so each fighter of his flight
already takes 1 less from a Spark Field. A harness check now proves it; nothing changed.

**Design.**
- ⭐ **Registry key `multi`: an officer bought as a COUNT.** `pickPosts` makes one runtime entry per pilot, posted in the flight's k-th
  craft (`nthCraft`), held to `maxCount` and to the craft the flight has; `apply` runs `applyPilot` per serving entry (+1 OB, published
  as the Evader's -1 is). `Officers::maxCount` is the offer's limit: the registry's 4, `$factionRules`' new key **`max`** (Raiders 2),
  a set maxFlightSize. ⚠️ `isActive` answers for an id's FIRST entry - count pilots with `servingCount` (⚠️ MIRROR PAIR with
  `officers.servingCount`).
- **Price**: `flightPrice($flight, $id, $crafts, $count)` - the listed price x count, scaled and rounded up ONCE for the row, which is
  the lobby's own `flightCost` sum (its enhCost is price x count), so no client price code changed: 2 pilots on 3 craft are 57, not
  2 x 29. `rowPrice($ship, $id, $count)`: `loadSavedFleet` re-prices BEFORE it writes the counts back, so it passes the count.
- ⭐ **The jinking is CLIENT-ONLY and never touches `jinkinglimit`**, which the lobby reads as the weight class (>= 6 heavy, 8 medium,
  10 light: two pilots would have made a heavy a medium), the hangars as box sizes and the flight-size cap as 9 or 12.
  `officers.freeJinks` (the serving pilots) is added to the limit in `shipManager.movement.canJink`; a free level is a player jink row
  assigned 0 thrust (`getFreeJinkRows`, `nextJinkIsFree`); `doJink` writes 0 while one is unused and, on a flight, removes a paid row
  first (a ship's jink rows keep their old removal). movement.php neither prices nor limits a jink (the thrust validator covers ship
  manoeuvres) and a flight's spent thrust is the sum of `assignedThrust`, so nothing server-side moved - the Coordinator's pattern.
- **Spark Field relief**: `Officers::pilotSparkRelief($flight, $turn)` in `SparkField::beforeDamage`'s flight branch, behind
  `$officersPresent`: serving pilots x 1 when `Movement::getJinking` is 0 and `getAllFireOrders($turn)` holds only interceptions.
  Both are known when a field resolves (movement committed in phase 2, every order in the firing gamedata).
- **Lost with his craft, follows it on a split** - the Stage 4 rules, per entry. ⭐ `OffMoved` notes are now matched by POST
  (`$officerMoves` a list of array(id, turn, post), as kills have been since Stage 10; DBManager passes the note's system), so one pilot
  leaving takes only himself. `followFighter` moves every travelling pilot (their crafts first, the new row bought at their count, one
  note each); `officerAmong` returns a COUNT (truthy exactly as before); `shareWithCraft`'s `$withOfficer` takes a count (true / false
  as before), each pilot's share of the row's price going with his fighter; the rail-escape remnant passes `officersAboard -
  officerAmong` - the one HangarOps site that negated it (`!$officerEscapes`).
- **Buy dialog**: `confirm.enhRowMax` (the + button, the typed box, the wheel) and `clampEnhRowsToSize` (from getTotalCost, before it
  sums); `officers.rowMax`; the price note "57pts each per 6 craft, one per craft"; `OFFICER_LIMIT_NOTE` "A flight may carry only one
  officer (or Expert Pilots, one per craft)." **Tooltips**: ShipInfo's flight OFFICERS block and the insignia title group identical
  lines (`officers.countLines`: "Expert Pilot (2)"; a lost one keeps a line of his own).

**Files.** Server: `Officers.php` (PILOT_* constants, `$notFiring`, `$factionRules` OFF_PILOT `max`, registry `OFF_PILOT` last with
`multi`; `maxCount`, `nthCraft`, `craftCount`, `boughtCount`, pickPosts' multi branch, outState's moves by post, `applyPilot`,
`servingCount`, `pilotSparkRelief`, `flightPrice` / `rowPrice` counts, `fighterOfficersAboard`, `officersAboard`, `officersCost`,
`shareWithCraft`, `travellingOfficers`, `officerAmong`, `followFighter`), `ShipClasses.php` (`$officerMoves`, `recordOfficerMove`'s
post), `DBManager.php` (the note's system), `Manager.php` (both saved-fleet `rowPrice` calls pass the count), `HangarOps.php` (the
rail-escape remnant), `specialWeapons.php` (SparkField). Client: `officers.js` (registry row; listFor's multi branch, `derivedEntry`,
`nthCraft`, `rowLimit`, `rowMax`, `boughtCount`, `servingCount`, `freeJinks`, `countLines`, `summaryLine`, `priceNote`), `movement.js`
(`canJink`, `doJink`, `getFreeJinkRows`, `jinkThrust`, `nextJinkIsFree`), `UI/confirm.js` (`enhRowMax`, `clampEnhRowsToSize`, the
note), React `ShipInfo.js`. Docs: an Expert Pilot card (Fighter Officers, alphabetical - last), the section's lede, the Evader card
(Spark Fields), the visible-numbers list; README's faction-rules how-to (`max`). Statics, bundles and the served copy rebuilt; no new
PHP class, no schema change.

**Measured.** 360 OFF_PILOT offers - the flights the Coordinator is offered - +15.9 KB uncompressed: limit 4 on 265, 3 on 38 (flights of
at most 3 craft), 2 on 57 (Raiders by the faction rule, and flights of at most 2 craft). Aurora: 57 a pilot.

**Verified.**
- `officersHarness.php` **1198 / 0 with the database**, 1124 / 0 with `--no-db` (65 new): offers and limits (Aurora 57 / 4, a
  Raiders Delta-V 2, a FangedSerpentAM 3 at 145, a single superheavy 1; none on a ship or a Hunter-Killer, a shuttle yes); the price
  table (1x1 10 ... 4x9 342) and rowPrice; posts on the first crafts, held to the craft (4 on 3), Raiders (2) and a doctored 9; beside
  another officer row the other serves; no kill roll; OB +1 / +2 / +4, a pilot's craft destroyed (serves that turn, gone the next),
  dropped out, docked, destroyed before battle, a craft without one; the payload per viewer; the REAL `calculateHitBase` +5% / +20%;
  the REAL `SparkField::beforeDamage` on each fighter (4 / 2 / 0 with 2 / 4 pilots, jinked or fired this turn none, last turn relief,
  an interception relief, a Gamma's ballistic launch none, a lost pilot's craft not hit and one less relief, gate off, the Evader's -1);
  `followFighter` with two of four travelling, the old row's two serving the next turn, a move note on a pilot-less craft; the cost
  split (the stayers' share after two left); the REAL partial dock (138 / 162) and the REAL rail escape (random_int: fresh fixtures
  until 1-2 pilots escape, the remnant right in every run); a REAL DB round trip - two pilots load into the first two craft, and an
  OffMoved note on the second one's craft moves him alone; and REAL saved fleets - two pilots stored as one row at 57, reloaded at 57,
  an older 50 re-priced with its notice.
- `officersClientHarness.js` **439 / 0** (29 new): the registry row; the lobby list (craft 1, 2, then the first again; the row's limit
  and the flight size); the lobby OB; "Expert Pilot (3)" / "Officers (4)"; `servingCount` and `freeJinks`; the same price table through
  `flightCost`; `priceNote`; `rowMax`; the REAL `confirm.enhRowMax`, `clampEnhRowsToSize`, `doOnPlusEnhancement` and `getTotalCost`
  (a flight cut to 2 keeps and charges two pilots); the insignia title and the REAL ShipInfo's grouped lines; the REAL movement.js
  (free rows 0-0-1, 8 levels on a limit of 6, none without them, 2 free levels with no thrust, a paid level removed first, a pilot
  lost last turn, an enemy flight, a ship's old removal).
- `enhancementsDifferential.js` **698 / 0** (12 new: `pilot`, `pilot4`, `pilot+dog` on the four flight hulls - a blueprint flight holds
  one craft, so one serves on both ends); `enhancementsHarness` **280 / 0** (fingerprint unchanged: ships only).
- `fvbuild -Check`: autoload current, validator clean, replay **156 / 0** (game 4488 SKIP, surrendered - as at Stage 17).
- **Mutation-tested: 57 breaks (34 server, 23 client), 54 caught**, files byte-identical afterwards. Of the other three: dropping the
  craft cap (server) CRASHES every load of a flight with more pilots than craft (nthCraft returns null) - caught, though not by a FAIL
  line; `submitSavedFleet` without the count (server) and counting paid jink rows as free (client) are equivalent - the saved row
  already carries its count, and a flight's free levels always come before its paid ones. Two client checks were added for the run:
  the REAL `doOnPlusEnhancement` and `getTotalCost` under a fake jQuery (the + button's cap, the clamp before the sum).

**Observations, not changed:**
1. The Spark Field text's "-5 (four pilots and an evader)" cannot happen under ruling 1: at most -4 with pilots, or the Evader's -1.
2. A flight's OB also funds mine detection (§9.7's Evader observation): a flight that neither fires nor jinks keeps its pilots' OB for
   detecting mines while it takes the Spark Field relief.
3. The free levels are client-side only, as the Coordinator's pivot cost is: movement.php validates no flight's jinking.
4. Not play-tested: no local game holds a pilot flight. Worth a test: buy 2 pilots on a flight of 6 (57 each, "Expert Pilot (2)"),
   drop the size to 1 (one left), back to 6; in game, OB +10, jink to the limit + 2 with the first two levels free; then a turn
   without jinking or firing inside an Ipsha Spark Field (2 less on each fighter).

### 9.25 Expert Evader made an Evade order; Expert Dogfighter wins ties (2026-10-20)

**User request, 2026-10-20** (replacing §9.6's "Evader: always on"): "Expert Evader should be a movement ability used in Firing Phase,
like a Combat Pivot. Use the Jink icon for Movement UI (but without the + and - buttons), movement tooltip reads 'Evade' for the icon
and it's hidden after being clicked (cancelled in the usual way for cancelling movement orders, the red cancel button). OB reduced in
client after Evade icon clicked. Effects of Evader applied on the server the same as they are now when an 'Evade' movement order is
present. Otherwise Evader does not apply any effects." And: "Expert Dogfighter should also break Initiative ties in the same way as
Expert Helmsman does for ships (in addition to the Initiative bonus it already has)."

**Built with defaults, stated here (ask before changing):**
- Evade sits behind the combat pivot's gate, `weaponManager.canCombatTurn`: only before the flight declares direct fire. That also
  keeps the shown hit chances current - no declared shot was priced at the old OB.
- No thrust, once a turn; Cancel takes it back (the red button, as for a combat pivot), after which it can be given again.
- "That turn" = the turn the row is dated: the -1 OB and -1 damage apply to everything resolved on it, so fire resolved in the
  Firing phase. Shots resolved BEFORE the order exists (Pre-Firing, movement-phase hazards) see no Evade.
- Hidden from the opponent until the Firing phase resolves, as combat pivots are: the row AND its -1 OB.
- A Dogfighter's flight tied with a Helmsman's ship: both "win", so the ordinary tie-break (bonus, then id) settles it.

**Design.**
- The order is a movement row, type `evade` (value 0, no thrust), made by `shipManager.movement.doEvade`, offered by `canEvade`
  (Firing phase, a serving Evader - `officers.canEvade` ↔ `Officers::mayEvade` - not yet given this turn, on the board, not attached,
  `canCombatTurn`). `isFiringPhaseMove` (a combat pivot or an Evade) replaced the combat-pivot-only tests in
  `hasDeletableMovements`, `deleteMove` and both Saved Orders phase-3 sites. FireGamePhase::process already submits a flight's
  phase-3 movement; `Movement::validateMovement` now keeps one `evade` row a turn for a flight whose Evader serves, asked of the
  authoritative load (the POST-side ship carries no officers: it is rebuilt from the client's JSON).
- Server effects: `Movement::hasEvaded($ship, $turn)`; `applyEvader` takes the OB only on such a turn (movement rows are loaded
  before `Officers::apply`); `evaderDamageReduction($flight, $turn)` gets getDamageMod's turn. The registry's `publish` of
  `offensivebonus` stays (the owner's preview reads the published figure).
- Client OB: `officers.pendingEvadeOb` = 1 while an Evade row is still the client's own (id -1); a sent row has its real id and the
  server's OB already carries it. Read by `displayStat` (ShipInfo, SystemInfo, the ship window rail) and `computeOEW`'s
  `effectiveOB` (the live hit chance). DERIVED, never written into `ship.offensivebonus`, so Cancel, Saved Orders and a reload
  cannot double-count it. The registry entry lost its `stats: { offensivebonus: -1 }`: the lobby shows the full OB now.
- Masking: `TacGamedata::hideEnemyCombatPivots` strips an enemy flight's `evade` row with its pivots and gives its OB back
  (`Officers::hideEvadeOb`).
- UI: `#evade` in game.php (`data-movement-type="Evade"`), drawn astern with the jink art (no value, no +/-) in the slot after the
  jink stack, Cancel behind it; `ShipMovementCallbacks.evadeCallback` (also removes the hover label, since the icon hides under the
  pointer). `FirePhaseStrategy.setSelectedShip` draws the movement UI for a flight that can Evade or has.
- Dogfighter: `Officers::winsInitiativeTies` = Helmsman OR Dogfighter. No client twin needed (§9.2: the client never sees a tie).
- Docs: the Evader card (how to Evade, effects that turn) and the Dogfighter card (+ the tie line); the Helmsman card's tie line
  now names both. officers.js summaries updated ("+5 initiative, wins ties"; "Evade in the Firing phase: ...").

**Verified.**
- `officersHarness.php` **1212 / 0**: no Evade = no effect; Evade this turn = -1 OB and +1 getDamageMod, another turn's = nothing;
  gate off; an Evade row with no Evader; a lost Evader with a row; Spark Field 5 with an Evade, 6 without; the payload's -1 with an
  Evade; the REAL `hideEnemyCombatPivots` hiding row and -1 from the enemy, keeping both for the owner; the REAL `sortShips`: a
  Dogfighter beats a higher bonus in either order, Helmsman vs Dogfighter settled by the bonus, a lost Dogfighter wins nothing.
- `officersClientHarness.js` **451 / 0**: the REAL movement.js `canEvade` / `doEvade` / `deleteMove` / `isFiringPhaseMove` (the row's
  shape, OB 7 -> 6 -> 7 through Cancel, a sent row not taken twice, last turn's row, the fire gate, no / lost / enemy Evader,
  Movement phase); lobby Evader OB unchanged (35 shown). **456** after the play-test fix below.
- **Play test, game 4494 (user, same day): the Evade icon showed but a click did nothing.** `UI.shipMovement.evadeCallback` had
  never been written (one edit of the build was lost) and the binding was in twice, so jQuery bound `undefined`. Fixed. The client
  harness now drives the CLICK through the REAL `UI/shipMovement.js` and `ShipMovementCallbacks.js` (fake jQuery): one click handler,
  the row made, the icon gone and Cancel shown on the redraw, every `.on()` handler in shipMovement.js defined. Mutation-checked: with
  the handler removed, 4 FAILs.
- `enhancementsDifferential.js` **698 / 0**; `enhancementsHarness` **280 / 0** (fingerprint unchanged). Bundles rebuilt (`fvbuild -Client`).
- Replay: **153 / 2** - games **4467** and **4475** FAIL as intended: each holds an Evader flight that (necessarily) never Evaded, so
  its OB is 1 higher (4475's recomputed to-hit 100 -> 105). NOT re-recorded, pending the user's OK. 4488 SKIP as before.
  `fvbuild -Check` stops earlier on a NEW ship-data validator error not from this work: KobolRanger's hit chart names a "Jump
  Drive" on location 0 that it does not have.
- Not play-tested in a browser.
