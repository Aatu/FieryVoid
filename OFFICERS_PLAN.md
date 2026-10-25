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
committed (`76d449587`); the Expert Evader made an Evade order (§9.25) - committed (`e833a9adf`). Stage 19, the Expert Targeter
(Raiders; Llort at double), the first officer posted in a STRUCTURE block - +4 on called shots by the weapons of his block, or on a
Medium ship by one side's weapons on its hit chart (§9.26) - committed (`1f0900707`). Stage 20, the Expert ELINT Officer (Abbai 15%;
17 more races at 30%), in the ELINT array - sensors +1, sensor criticals -2, every ELINT range 25% longer, his blanket a third against
ballistic weapons (§9.27) - committed (`cff6a6094`). Stage 21, the Expert Tailgunner, with FV's missing TAIL GUN rule built first -
a fighter's tail gun fired with its craft's other weapons at -10% unless the flight has a navigator; under him no penalty, or +5% with
a tail gun that has none (§9.28) - committed (`3f8de3e6b`). Stage 22, the Expert Turret Officer (Vree), placed in a turret-mounted weapon
and aiding his whole turret - +1 fire control, -1 on criticals, -2 on the turret jam roll, and a turret that fires no shot SHELTERED from
all but called shots (§9.29) - committed (`260780e57`, `20cc13c40`). Stage 23, the Expert Anticipator (Torata 10%, everyone else 15%), in the
C&C - initiative rolled twice and the higher kept, one turn in the Firing phase as a fighter's combat pivot, and every damage roll of two or
more dice one die larger with its lowest dropped (§9.30) - committed (`b03679743`). Stage 24, the Breaching Expert (Balosians, Yolu, Narn and
Gaim 25% of one pod, everyone else 50%), in a breaching pod - the flight +1 thrust, his pod's Marines Elite, his pod's first capture party
three attack rolls on its first turn aboard, and at most 20% of a fleet's pods in the Fleet Correctness Report (§9.31) - committed
(`30cb21e5d`). Stage 25, the Expert Political Officer (Centauri capital ships and larger, 40%), in the C&C - an armed shuttle, one free Expert
Navigator, Helmsman, Engineer or Scanner, five free Hardened Armour (what the player leaves unchosen picked at random as the fleet is committed),
and once per battle a stirring speech in the Firing phase, told in the combat log, for +30 initiative next turn (§9.32) - committed
(`ce9f8e8a2`). Stage 26, the Expert Software Engineer (Hyach, 15%), in the Hyach Computer - one more bonus fire control point that damage never
takes and that may be held back to the Firing phase, the Computer ignoring the first point of every hit, a stealth ship's signature masked (5 hexes
further, fighters at half range), and one software Specialist used a second time - with the Computer Specialist rebuilt to its own rules on the same
Firing-phase allocation (§9.33) - committed (`4e523c0df`). Stage 27, the Expert Troop Leader (Gaim), aboard a ship in its Hangar (5%) - every boarding
roll against it 2 in its favour, +1 on the rolls of as many of his player's breaching pods as the ship can carry (its Pod Allowance), every Bulkhead a
box tougher for the whole battle - or bought for a Gaim breaching pod (50% of a pod, one to a pod): -3 on his pod's parties' rolls, and a d6 to escape
when one is eliminated (none on Wreak Havoc); with it, Breaching Experts one to a pod, and the two sharing a pod flight (§9.34) - BUILT 2026-10-23,
uncommitted. Stage 28, the Expert Warrior (Drazi, 50%), in the C&C - +1 to hit and +1 per die on every weapon, +10 initiative for his ship and for the
flights it launches, -1 on his ship's critical rolls and +1 on those it causes; once per battle each, never two in a turn, a Charge declared in the Movement
phase at a unit the player picks (+2 to hit it if the run holds, one cheaper snap turn), a precision strike (his ship's first called shot without its
penalty) and Supreme Anticipation from his C&C in the Firing phase (+50 initiative next turn, -50 the turn after) (§9.35) - BUILT 2026-10-23, uncommitted.
Stage 30, the Expert Quartermaster (Hurr 10%, everyone else 20%), in the C&C - one free refit, the dearest bought of Gunsights or Advanced Defensive Targeting
on two weapons, the Improved Reactor (its breach chance tripled), Improved Thrust on two thrusters, Hardened Armour on two systems of one side or the Jump
Accelerator; two free special missiles a rack (one a Class-SO); what is left unchosen rolled at the commit; the ship's first critical ignored; -30 initiative,
+10% turn delay and +2 on the C&C's criticals, all of it staying when he is lost (§9.37) - BUILT 2026-10-24, uncommitted.
Stage 31, the Expert Graviton Controller (Brakiri 10%; any other hull of a B5 setting with a gravitic drive or a gravitic weapon 15%), in the Engine - +1 on
every hit by a gravitic beam, a Graviton Pulsar's pulses counted as though it rolled 5% lower, the Engine and the largest aft thruster +1 (kept when he is
lost), and once per battle a rotation of the gravitic drive in the Movement phase: a d6 the server rolls picks the new facing, -15% to hit that turn, and
the rotation and every move before it LOCKED - a generic movement lock in tac_systemdata, kept on a reload (§9.38) - BUILT 2026-10-25, uncommitted.**
Written
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
§9.24). On 2026-10-20 they added the Expert Targeter with his rules text and notes (a system enhancement on the outer Structure
blocks, movable between them in battle; a Front / Aft choice on a Medium ship's one block; the insignia and a tooltip on the block's
health bar), and ruled four questions: his kill roll counts only the damage his block took that turn, a Medium ship's side is its
hit chart's reach (Primary included), an Expert Gunner's called-shot bonus gives way to his, and the insignia sits after the
section name (Stage 19, §9.26). Later that day they added the Expert ELINT Officer with his rules text and a list of the races that
may take him (no custom faction for now), and ruled four questions: the Abbai WotCR are Abbai and the Drazi WotCR left out, his +25%
on the ranged functions only, the -2 on every sensor array, and the blanket's fractions kept (Stage 20, §9.27). On 2026-10-21 they
added the Expert Tailgunner with his rules text and a note: FV may not apply the tail gun's penalty at all (it did not - FV_factions.txt
said so), so it was to be built first, flagging the few fighters that have a tail gun. They ruled three questions: the -10% on the
Tiqincc, the Tiqus and the custom tail-gun fighters "unless $this->hasNavigator = true"; the officer offered to all 15 tail-gun flights;
no +5% for a penalised tail gun fired alone; and, after the first build, only the craft's other DIRECT-FIRE weapons trigger the penalty,
never a torpedo or missile launched in Initial Orders (Stage 21, §9.28). Later that day they added the Expert Turret Officer with his rules
text and notes - a system enhancement on a turret weapon (FV has no turret system), aiding every weapon on that turret, one to a turret, and
"sheltered" automatic when no weapon on the turret fires, with no new declaration UI - and ruled four questions: only a shot breaks the
shelter, never an interception; a Gunner's +1 on that turret does not combine with his; a turret weapon linked to no other is a turret of its
own; and he moves as the other placed officers do (Stage 22, §9.29). Later that day they supplied the Expert Anticipator's rules text - one
of the original 25 - with a note (his Firing-phase turn "is similar to a fighter's combat pivot except that there are no associated firing
penalties and thrust must still be spent on ship thrusters as normal"), and ruled two questions: the turn takes effect as a combat pivot does,
before fire resolves, so this turn's fire meets the new facing; and on an Expert Gunner's weapon the extra die and the Gunner's +1 per die
both apply (Stage 23, §9.30). Later that day they supplied the Breaching Expert's rules text - one of the original 25 - and ruled four
questions: in a pair of pods his Elite benefit and his triple capture roll are his own pod's only ("single pod"), the +1 thrust the flight's;
his price is 25% (50%) of ONE pod, not scaled by the flight's size; the fleet's 20% is a Fleet Correctness Report line, as every
fleet-composition rule is; and on a pod already Elite only the Elite clause adds nothing (Stage 24, §9.31). Later that day they added the
Expert Political Officer with his rules text and notes - the free expert as the dearest of those bought (one at random with none), the armour
as five free Hardened Armour (random ones for any left unplaced), the speech as an Activate menu on the C&C in the Firing phase ("Speech!" /
"Cancel speech"), told in the combat log - and ruled four questions: both Centauri factions; the random picks the server's, at battle start;
his armed shuttle on top of Elite Crew's; and no +30 unless he still serves on the turn it lands (Stage 25, §9.32). On 2026-10-22 they supplied
the Expert Software Engineer's rules text - one of the original 25 - with notes (the software Specialists are Defence, Sensor, Targeting and Weapon;
+1 armour on the Computer would do if the damage rule proved complicated - it did not), and ruled three questions: the Firing-phase hold built, and the
Computer Specialist rebuilt to its own rules (once per scenario, in the Firing phase, +1 point and every point reshuffled) on the same allocation; the cap
of two per category stays, for both; and a software Specialist used on turn N may be used again from turn N+1 (Stage 26, §9.33). On 2026-10-23 they
added the Expert Troop Leader with his rules text and notes (no officer can move from a ship to a docked pod, so he is offered separately to Gaim pods at
half a pod; he goes with every party from his pod, and dies with one that is eliminated unless he escapes), asked for Breaching Experts and pod Troop
Leaders to be bought one to a pod, and ruled four questions and a follow-up: "his own ship's pods" are as many as the ship's Pod Allowance, in fleet
order, fixed; his pod's -3 includes his ship's 1; no escape on Wreak Havoc; and a pod flight may mix the two, one to a pod (Stage 27, §9.34). Later that day they
added the Expert Warrior with his rules text and notes (the Charge a new Movement-phase icon, its conditions checked by the server; the precision strike his
first called shot; Supreme Anticipation the Political Officer's speech path, in blue; the initiative numbers already FV's), and ruled four questions: both Drazi
factions; the +10 only for flights launched from his ship in battle; the player picks the unit charged; on a Gunner's weapon the Gunner's +1 and +1 replace his
(Stage 28, §9.35). Each officer's own rules
questions still only need ruling when that officer's stage starts. Next step: the rest of the Hard tier, one officer at a time.

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
| **Expert Quartermaster** | Makes this ship's other enhancements and ammo free, which touches every price display, total and the saved-fleet bucket split; a fleet-% price; a random post; "+10 turn delay" is unclear. *Built in Stage 30 (§9.37): the rules text makes it ONE of six refits (the dearest bought - user's note) and two missiles a rack, a ship price (10% / 20%) in the C&C, and "+10%" turn delay; the hard part is that his free refit may be a ship-level row (reactor, accelerator) or per-system refits, so a write to either price bucket can move the other - the client settles the ship-level bucket after every refit write.* | −30 initiative; the C&C critical modifier |
| **Breaching Expert** | His post is **one pod inside a flight**, and per-craft ids do not exist in the lobby (WEAPON plan D7). Adds a fleet-wide "20 % of all pods" cap, a change to the capture roll ([ShipSystem.php:951](source/server/model/systems/ShipSystem.php#L951)), and "+1 thrust" for one craft of a flight that moves as one. *Built in Stage 24 (§9.31) as an ordinary fighter officer in the flight's first pod: the fighter framework already gives a craft-level post, so "his pod" is that craft; the thrust is the flight's (user ruling); the 20 % is a Fleet Correctness Report line (user ruling); the triple roll is a mark on his capture party's critical.* | — |
| **Expert Evangelist** | Fleet-wide state: +1 to hit while he serves, then −10 initiative (QX1) and −1 to hit if he is **killed**, for every ship, with a client to-hit mirror. Also a fleet-% price, a random post, and the CPN/OSF gate exists only in notes prose | — |
| **Expert Laser Technician** | "+1 damage per power point sent, up to +50 %" needs a new per-turn allocation UI, persistence and a cap. *Built in Stage 11 (§9.17) with no new UI or persistence: the user's design note made every laser use the existing BOOST (power entries type 2, the generic boost buttons), so the allocation, its storage and the power balance all came free; what was new is the cap, a per-turn pool, and hiding the boosts from opponents.* | +3 Spinal / +2 Blast / +1 other lasers, through `getBonusDamage` (`SpinalLaser`, `BlastLaser`, `ImprovedBlastLaser`, `LtBlastLaser` exist) |
| **Expert Software Engineer** | Bonus fire-control points spendable in the Firing phase need a new allocation window (`HyachComputer` only allocates in Initial Orders, [baseSystems.php:12862](source/server/model/systems/baseSystems.php#L12862)). Also a "first point of damage ignored" rule in the damage path, a submarine detection range, and a self-contradictory specialist clause (§6, Software Engineer). *Built in Stage 26 (§9.33): the user's rules text settled the specialist clause and made the "submarine" a stealth unit; the Firing-phase window is the Computer's own menu reopened in the Firing phase, written as one note through `saveFirePhaseDeclaration` and judged on the loaded ship - built so that the rebuilt Computer Specialist rides it too (user ruling); the damage rule is one line beside the Scavenger's save.* | — |
| **Expert Ion Technician** | "3 thrust → 1d10+1 on one ion weapon" is a per-turn spend with UI. *Built in Stage 17 (§9.23) with no new UI or storage: the user's note pointed at the Hypergraviton Blaster, whose boost is the generic one priced in thrust - so the Laser Technician's payload pattern with `thrustBoosted` gave the buttons, the thrust balance and the commit check for free; what was new is the discharge of a boosted gun that does not fire, at the turn change.* | +1 per die and +1 flat on Ion-class weapons; +4 on C&C crits; the Rad Cannon's flat 12 and forced crits are Medium |
| **Expert Surge Officer** | Six unrelated hooks: EM damage, combined Surge Cannon shots, capacitor power by ship size, EM-hardening −3, a d6 to resist deactivation, Spark Field −1. *Built in Stage 16 (§9.22): the rules text made it reactor power by ship size; his two hardening clauses needed FV's EM hardening rebuilt to the tabletop rules first (WeaponEM), the bigger half of the stage.* | +1 damage on Electromagnetic-class weapons |
| **Expert Ballistic Officer** | "+1 magazine round per launcher" moves the lobby's ammo limits, which are derived from magazine capacity at offer time. Free missile swaps are lobby ammo pricing. *Built in Stage 29 (§9.36): the rules text makes it DISTANCE range, never launch range - one read-time term where FV tests it; the extra slots are the magazine's capacity raised before the ammo rows load, the free swaps a price rule (the N dearest rounds bought) with the rest rolled at the commit, as the Political Officer's picks; a weapon keeping its own count gets its extra round written once, at the commit.* | +5 launch range (`range` **and** `rangeArray` — the same mode-switch trap — and sent to the client) |
| **Expert Anticipator** | A free **turn** for a ship in the Firing phase is a new phase-3 movement for ships (today only flights move there, as combat pivots), with masking, validation and animation. *Built in Stage 23 (§9.30): the turn is an ordinary turn row marked `anticipate`, kept by the server's Firing-phase filter for his ship only and masked with the combat pivots; its thrust is paid on the Movement phase's own panel, wired into the Firing phase. The extra die is a `Dice` flag after all, and the one weapon that rolls its damage dice one call at a time - the Pentagon Array, his rules' own example - hands them to `Dice::keepBest`.* | Rolling initiative twice is Easy. Re-rolling the lowest die is Medium: a `Dice`-level flag, which the replay harness's own `Dice` stub must mirror ([[project_replay_harness]]) |
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
| Expert Quartermaster *(Stage 30, §9.37)* | `OFF_QM` | C&C (his rules text - the draft's random primary dropped) | 1, every hull with a C&C |
| Expert Graviton Controller *(Stage 31, §9.38)* | `OFF_GRAV` | Engine | 1, hulls with a gravitic drive or a gravitic weapon (B5 settings) |
| Expert Geneticist | `OFF_GENE` | C&C | 1 |
| Expert Scavenger | `OFF_SCAV` | Engine | 1 |
| Breaching Expert *(Stage 24, §9.31)* | `OFF_BRCH` | his pod: a breaching pod flight's first craft - since Stage 27 (§9.34) one per pod, from the first | 1 (flight), breaching pods only, priced off one pod (`unscaled`); bought as a count, `mixesWith` the pod Troop Leader |
| Expert Analyst | `OFF_ANLY` | Scanner | 1 |
| Expert Evangelist | `OFF_EVAN` | random primary | 1 |
| Expert Laser Technician | `OFF_LASR` | Reactor | 1 |
| Expert Plasma Scientist *(added 2026-10-17, §9.18)* | `OFF_PLAS` | Engine | 1 |
| Expert Security Officer *(added 2026-10-17, §9.19)* | `OFF_SECU` | C&C | 1 |
| Expert Religious Leader *(added 2026-10-17, §9.20)* | `OFF_RELI` | C&C, movable to any primary non-weapon system | 1 |
| Expert Religious Leader, elsewhere *(Stage 15, §9.21)* | `OFF_RELIP` | a chosen primary non-weapon system | 2 |
| Expert Targeter *(added 2026-10-20, §9.26)* | `OFF_TARG` | a chosen outer Structure block; on a hull with none, its Primary Structure, covering the first side | 2 |
| Expert Targeter, the other side *(§9.26)* | `OFF_TARG2` | a Medium ship's Primary Structure, covering its second side | 2 |
| Expert ELINT Officer *(added 2026-10-20, §9.27)* | `OFF_ELINT` | the ELINT array | 1 |
| Expert Turret Officer *(added 2026-10-21, §9.29)* | `OFF_TURR` | a chosen turret-mounted weapon, aiding every weapon on its turret | 2, one to a turret |
| Expert Political Officer *(added 2026-10-21, §9.32)* | `OFF_POL` | C&C | 1, Centauri capital ships and larger |
| Expert Software Engineer *(Stage 26, §9.33)* | `OFF_SOFT` | Computer | 1, Hyach only |
| Expert Troop Leader *(added 2026-10-23, §9.34)* | `OFF_TROOP` | the largest Hangar | 1, Gaim only |
| Expert Troop Leader, in a pod *(§9.34)* | `OFF_TRPOD` | one per pod, after any Breaching Experts | 1 (flight), Gaim breaching pods, bought as a count, priced off one pod (`unscaled`), `mixesWith` the Breaching Expert |
| Expert Warrior *(added 2026-10-23, §9.35)* | `OFF_WARR` | C&C | 1, Drazi only |
| Expert Ion Technician | `OFF_ION` | Engine | 1 |
| Expert Surge Officer | `OFF_SURG` | Mag-Grav Reactor | 1 |
| Expert Ballistic Officer *(Stage 29, §9.36)* | `OFF_BALL` | C&C | 1, hulls with counted ballistic ammunition |
| Expert Anticipator *(Stage 23, §9.30)* | `OFF_ANTI` | C&C | 1 |
| Matter Weapons Expert, C&C | `OFF_MWE` | C&C | 1 |
| Matter Weapons Expert, weapon | `OFF_MWEW` | chosen weapon | 2 |
| Expert Gunner | `OFF_GUN` | chosen weapon | 2 |
| Expert Dogfighter | `OFF_DOG` | lead craft | 1 (flight) |
| Expert Missileer | `OFF_MSL` | lead craft | 1 (flight) |
| Expert Evader | `OFF_EVAD` | lead craft | 1 (flight) |
| Expert Coordinator | `OFF_COOR` | lead craft | 1 (flight) |
| Expert Pilot *(added 2026-10-19, §9.24)* | `OFF_PILOT` | one per craft, from the first | 1 (flight), bought as a count |
| Expert Tailgunner *(added 2026-10-21, §9.28)* | `OFF_TAIL` | lead craft | 1 (flight), a flight with a tail gun only |
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
| ELINT array | the strongest `ElintScanner` by `output` | Expert ELINT Officer (Stage 20); the Chameleon suite is one, a Kirishiac ELINT module is not (no Scanner). No officer-eligible hull mixes a plain Scanner with an array (measured) |
| Reactor | the biggest `Reactor` by `maxhealth` | `ELITE_CREW`'s rule ([Enhancements.php:2485](source/server/model/ships/Enhancements.php#L2485)); `MagGravReactor` extends `Reactor` |
| Mag-Grav Reactor | the first `MagGravReactor` that can be hit (never a `MagGravReactorTechnical`) | Surge Officer (Stage 16: every Ipsha hull has exactly one) |
| Jump Drive | the first engine `JumpEngine::getUnitJumpEngines($ship)` returns that is neither legacy nor a gate | never a bare sweep of `$ship->systems` ([[project_jump_points]]) |
| Computer | the `HyachComputer` | |
| Hangar | the largest plain `Hangar` by `maxhealth`, the first on a tie | Expert Troop Leader (Stage 27): never a `Catapult`, `FighterRail`, `DockingCollar` or `DockingBay` (Hangar subclasses with jobs of their own) |
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
| Expert Targeter (Stage 19) | the hit chance of a called shot by a weapon he covers; which weapons those are (`covers`) | **Own team only**: the +4 is in the owner's own hit-chance preview ("Expert Targeter") and nowhere else; an opponent sees only the needed % the combat log prints for the shot, as for a Gunner's +3. The covered weapon ids ride the officer payload, which an opponent never receives |
| Expert Tailgunner (Stage 21) | the hit chance of his flight's tail guns | **Own team only**: the owner's hit-chance preview ("Expert Tailgunner" +5, or no "Tail Gun" -10) and the tail gun's tooltip line; an opponent sees only the needed % the combat log prints. The tail gun's own penalty is public: every viewer's tooltip has the "Tail Gun" line |
| Expert Turret Officer (Stage 22) | his turret's weapons' fire control; their critical and jam rolls; where hits land on a sheltered turret | Fire control: **yes, to every viewer**, as the Gunner's (isModified). The -1 on criticals is never sent (nothing displays one); the jam's 19+ is in the owner's "Turret Mount" line only. The shelter is server-only: an opponent sees where the hits land (another system, or Structure), never why |
| Breaching Expert (Stage 24) | a pod flight's `freethrust`; his pod's Marines Elite at delivery; his capture party's three attack rolls | Thrust: **yes, to every viewer** (his `publish` - the boarding hit chance reads it on both ends). The Elite is asked at delivery and never written into the weapon, whose "Elite" tooltip line every viewer is sent - so it stays the hull's, and his own team's client adds him to it. Which capture party is his is a key in its critical's `param`, stripped from every payload (`ShipSystem::stripForJson`). An opponent sees results only: an Elite mission class on one pod's party, the delivery and combat rolls the log prints |
| Expert Political Officer (Stage 25) | `iniativebonus` (+30 the turn after his speech); his speech itself; the armour of his five free Hardened Armour; an armed shuttle in a hangar | **Yes**: the speech is a combat log line every player reads once firing resolves ("The Political Officer on-board <ship> begins a stirring speech about ...", user request) - so his speech names him; the +30 through his `publish`. The armour and the armed shuttle show exactly as any bought refit and any hangar's contents do. Which expert he brought and which armour is free stay as hidden as any officer and refit |
| Expert Software Engineer (Stage 26) | the Computer's `output` (+1); where a point placed in the Firing phase went; the Computer's armour point; his stealth ship's detection; a Specialist used a second time | `output`: **yes, to every viewer**, as the Technician's. The Firing-phase allocation: **own team only until firing resolves** - an opponent's payload and tooltip carry Initial Orders' allocation while the Firing phase is open (`HyachComputer::shownBFCP`), since where a point went would point at the targets; afterwards it is public, as Initial Orders' always was. The armour point shows only as the damage the log records (one more armour), the masking only as a stealth ship staying hidden, the second Specialist use as that Specialist's line in its "used this turn" list, which every viewer is sent anyway. The list of Specialists he would let the ship reuse rides his own team's officer payload (`reuse`) |
| Expert Warrior (Stage 28) | `iniativebonus` (+10; +50 / -50 after his Supreme Anticipation), and a flight's his ship launched; his ship's critical modifier; his charge (a movement row); the hit chance of his ship's shots | Initiative: **yes, to every viewer** (`publish` / `lostPublish`, and a launched flight's through `addForJson`). The charge is a row of the ship's movement, which every viewer is sent - so a declared charge, and its target, are public (the tabletop declares it as it is performed). Which unit it holds against (`charged`) rides his team's officer payload, for their hit-chance preview; an opponent sees only the needed % the log prints, as for every to-hit officer. The critical modifiers are never sent (nothing displays one) |
| Expert Graviton Controller (Stage 31) | his Engine's and the largest aft thruster's `output`; his gravitic drive's rotation; the hit chance of his ship's shots that turn | Outputs: **yes**, `output` is sent unconditionally (as the Technician's), and they stay once he is lost. The rotation is a `gravRotate` row of the ship's movement, which every viewer is sent - so the rotation, and that he is aboard, are public (the tabletop rolls it in the open); its die is the server's, and the moves locked before it ride the owner's game.php only (`window.fvMoveLocks`). The -15% is the owner's hit-chance preview; an opponent sees only the needed % the log prints. His +1 on beams and the pulse count show only as their results |
| Expert ELINT Officer (Stage 20) | his array's `output`; every ELINT range from his ship; his blanket against ballistic weapons | **Yes, to every viewer**: `output` always is, and the ship payload carries `elintRangeBonus` (25) and `blanketBallistic` (3) while he serves - an opponent's own OEW under his Disruption and its hit chance against a unit under his blanket are previewed with them; his array's tooltip shows the ranges. The officer list stays own-team; a disguised Chameleon ship's payload is its simulacrum's and carries neither. The -2 on sensor criticals is never sent |
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
| **19** | Expert Targeter, added by the user - the first officer posted in a Structure block. **BUILT 2026-10-20** (§9.26) | Offered on every outer block of a Raiders or Llort hull (Llort 20%), one per block (never moved onto a held one), on a Medium ship's one block with a second-side twin only where the two sides differ, never elsewhere; +4 on a called shot by a covered weapon, nothing on an ordinary shot or a flat-profile call, the -8 kept; a block covers its location's weapons, a Medium ship's side its hit chart's reach through Primary; a covered Gunner's +3 neither used nor spent; the kill roll on this turn's damage only; a move between blocks and a side switch on the Stage 10 rules (serving nowhere that turn), free in Fleet Selection; the insignia in the bar, an officers-only tooltip on it, its click opening the Officers menu in Initial Orders |
| **20** | Expert ELINT Officer, added by the user. **BUILT 2026-10-20** (§9.27) | Offered on the ELINT hulls of the user's 19 factions only (Abbai and Abbai WotCR 15%, the rest 30%; never the Centauri or Drazi WotCR, the Early EA, a custom faction or an OSAT), posted in the strongest array; that array +1, every array -2 on criticals, his kill roll included; SOEW, Disruption and Jamming 38 hexes, SDEW 63 (its base corrected to 50), Blanket 25, at declaration (the real Initial Orders menu) and at use (the real EW sweeps, both ends); his blanket a third against ballistic weapons only, fractions kept, the best ELINT per weapon kind, in the real `calculateHitBase` and the live client breakdown; the numbers to every viewer, the officer to his own team |
| **21** | Expert Tailgunner, added by the user, with the tail gun rule FV lacked. **BUILT 2026-10-21** (§9.28) | A tail gun flagged on the 15 flights that have one (one a craft, firing aft beside a forward weapon), in the static blueprint; -10% on a turn its craft fires another direct-fire weapon - not another craft's, not a missile or torpedo launch, an interception, Meteor Defence, last turn's order or the ram - unless the flight has a navigator (7 of the 15, or one bought); the forward guns never penalised; the Tailgunner offered on those 15 only, 50% of a craft scaled; under him no penalty, or +5% on a tail gun without one and no other weapon; lost with his craft; the hit-chance breakdown and the tooltip line on the client, as on the server |
| **22** | Expert Turret Officer (Vree), added by the user - placed in a turret-mounted weapon, aiding his whole turret. **BUILT 2026-10-21** (§9.29) | Offered on every turret-mounted weapon of the 12 Vree hulls with turrets, 5%, one to a turret (a ship as many as it has turrets), never the Custom Ships' Shadow Xonn; +1 fire control on every weapon of his turret (not +2 on a Gunner's), -1 on their criticals (his kill roll included), -2 on their jam roll; a turret none of whose weapons fires a shot sheltered - an interception not counting - so a hit-chart roll on its weapons takes the destroyed-system fallback, a called shot still hitting, inside the real `Firing::fireWeapons` only; disabled with his own weapon; movable, never onto a turret another holds; the lobby's fire control and Turret Mount line, the client's one-per-turret rules |
| **23** | Expert Anticipator, one of the original 25 (Hard). **BUILT 2026-10-21** (§9.30) | 15%, the Torata 10%, in the C&C; the d100 of both initiative rolls thrown twice for his ship, the higher kept; one turn in the Firing phase - a real turnleft / turnright, its thrust through the thrusters on the Movement phase's panel - only with the turn delay complete (no snap turn) and the thrust unspent, before the ship declares direct fire, once a turn, never with a unit attached, kept by the server for his ship only and hidden from enemies until firing resolves, its facing the one this turn's fire meets; a commit refused while its thrust is unassigned; every damage roll of two or more dice one die larger, the lowest dropped (the real `lib/dice.php` and both harness stubs alike), the Pentagon Array's five rakes included, never a one-die or fixed roll or a ram, on a Gunner's weapon with his +1 per die as well |
| **24** | Breaching Expert, one of the original 25 (Hard). **BUILT 2026-10-21** (§9.31) | Offered on all 42 breaching pods and nothing else carrying Marines; 25% of one pod for the Balosians, Yolu, Narn and Gaim, 50% for everyone else, the same at any flight size (lobby and server); in the first pod, the flight's one officer, no kill roll, lost with his pod; the flight +1 thrust, published, previewed in the lobby, reaching the boarding speed match; his pod's Marines Elite at delivery (-1, the Elite mission classes), never the other pod's, nothing more on a pod Elite already, the "Elite" tooltip line his team's only; his pod's first delivered capture party - never a failed delivery, a Sabotage or a Rescue - three attack rolls on its first turn aboard in the real capture combat, still one attacker to the defenders, once per scenario through a reload; the mark never sent; the Fleet Correctness Report's "Breaching Experts" line, 20% of the fleet's pods rounded half up |
| **25** | Expert Political Officer, added by the user. **BUILT 2026-10-21** (§9.32) | Offered on the 60 capital ships and larger of the Centauri Republic and its WotCR, 40%, in the C&C; a free Expert Navigator, Helmsman, Engineer or Scanner - the dearest bought, a tie to the first in the registry - in the buy dialog, the fleet list, Fleet Selection's own writes and a saved fleet; five free Hardened Armour, the dearest, never Structure, priced 0 by the sanitiser and the lobby alike, none with his C&C destroyed; whatever is left unchosen picked at random as the fleet is committed, never Structure, a destroyed or already-armoured system; one more armed shuttle than Elite Crew gives, none with Poor Crew; "Speech!" / "Cancel speech" on his C&C in the Firing phase, once per battle, judged on the loaded ship; a "SPEECH:" combat log line as firing resolves; +30 initiative on the next turn while he still serves, shown to every viewer |
| **26** | Expert Software Engineer, one of the original 25 (Hard), with the Hyach Computer Specialist rebuilt to its own rules. **BUILT 2026-10-22** (§9.33) | Offered on the 13 Hyach hulls with a Computer (not the OSAT), 15%, Hyach only, in the Computer; its BFCP +1, never taken by damage (a base's 26 boxes leave him his point); one point left free in Initial Orders placed in the Firing phase - added, nothing moved, the cap of two kept - through the Computer's own menu, written as one note at the fire commit, judged on the loaded ship, used by that turn's shots, hidden from opponents until firing resolves, never carried into the next Initial Orders; no unassigned-BFCP warning for that one point; the first point of every hit on the Computer ignored, armour-ignoring weapons included, through the real damage path; a stealth ship (the Alichi Kav and Tal) detected as if 5 hexes further away, a flight's detection range halved and rounded up, through the real detection sweep; a Defence, Sensor, Targeting or Weapon Specialist used once usable again from the next turn, once per scenario, its effect applied again, shown "(again)" in the Specialists menu, no Specialist slot spent. The Computer Specialist: in the Firing phase only, +1 BFCP (it was +2 and +1 a category), every point reshuffled within the cap, cancelled back to Initial Orders' allocation |
| **27** | Expert Troop Leader (Gaim), added by the user, with Breaching Experts one to a pod. **BUILT 2026-10-23** (§9.34) | See §9.34 |
| **28** | Expert Warrior (Drazi), added by the user. **BUILT 2026-10-23** (§9.35) | Offered on the 51 Drazi hulls (both factions) with a C&C, 50%; +1 to hit and +1 per die on every weapon but the ram - not on a Gunner's, whose own replace them; +10 initiative, and +10 for every flight launched from his ship's bays from the turn after, while he serves; -1 on his ship's critical rolls (his kill roll included), +1 on the critical roll of an enemy system his ship damaged that turn, once, not a craft's dropout; a Charge declared before the first step at a unit the player clicks, kept by the server once, judged at fire (beaten on initiative, 4+ hexes nearer the target by the end of the move, the route free - third round, §9.35): +2 to hit it; one snap turn anywhere in the move, 1 thrust less (half on an agile ship); the precision strike - the first called shot (his lowest-numbered weapon's that turn) without its penalty, doubled at a base, a Gunner's +3 neither used nor spent; Supreme Anticipation from his C&C's blue box in the Firing phase, +50 next turn while he serves, -50 the turn after regardless; never two of the three in one turn |
| **29** | Expert Ballistic Officer, one of the original 25 (Hard). **BUILT 2026-10-24** (§9.36) | See §9.36 |
| **30** | Expert Quartermaster, one of the original 25 (Hard). **BUILT 2026-10-24** (§9.37) | See §9.37 |
| **31+** | The other Hard officers (the Expert Analyst among them since 2026-10-16), one at a time, each starting with its own short plan and ruling round | — |

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
| Quartermaster | "Free" = this ship's own other enhancements and ammo? What is "+10 turn delay"? How much on C&C crits? Post: random primary? Price: 5 % of the game's points limit? **Rules text and notes 2026-10-24 (§9.37)** settle all five: ONE of Gunsights (two weapons), Advanced Defensive Targeting (two weapons), Improved Reactor ("if the reactor is in danger of exploding, triple its chance"), Improved Thrust Rating (two thrusters), Hardened Armour (two non-Structure, non-primary systems on one side) or the Jump Accelerator free - the user's note: the dearest of those the player buys, one at random if none; two free special missiles per Class-S-or-better rack, one per Class-SO or older - the user's note: bought free up to that allowance, the rest at random; one critical ignored per scenario - the user's note: the first the ship suffers, no choice; -6 initiative (QX1: -30), +10% turn delay rounded up, +2 on the C&C's criticals; the C&C; 10% for the Hurr, double elsewhere. Asked: how the +10% turn delay applies; what stays when he is lost; which missiles may be free; where the random pick may land. **RULED 2026-10-24 (all as recommended):** **each turn's delay +10%, rounded up** (+1 on 1-10, +2 on 11-20), on the speed-and-rate delay before overthrust and Elite / Poor Crew; **everything stays** - the refit, the missiles, an unused patch and all three penalties; **any paid type the hull is offered**, a two-slot round counting as one; **the random pick only among the four refits with no drawback** - never the Improved Reactor or the Jump Accelerator. |
| Geneticist | "Scenario only" = never offered unless a hull opts in, or a Create Game rule? "−1 to hit other ships" = this ship's own weapons? **RULED 2026-10-10: offered on every hull with a C&C** ("scenario only" is by agreement, as the doc card says). The −1 was built with its default: this ship's own weapons. **RE-RULED 2026-10-15: Dilgar Imperium only** (§9.12). |
| Scavenger | A critical first, else 4 boxes? Which system first? May he repair Structure? **RULED 2026-10-15** (§9.8): **a critical first** (as the Engineer, same order and C&C roll), else 4 boxes, by Self Repair's default system order; **Structure yes**, never a destroyed system or anything on a destroyed section, never this turn's damage. His rules text adds a **once-per-scenario save** - "up to six points of damage can be ignored in any single volley" - ruled **automatic**: spent on the first hit that would destroy the C&C, a reactor or a Structure block when ignoring up to 6 points saves it ("note the Gaim Bulkhead already has a similar function"). The campaign clauses (permanent repairs after the battle) are not built. **Widened 2026-10-15 (§9.10): Scanners and Engines too.** **Faction prices 2026-10-15 (§9.12): 15% for the Descari, 20% for Balosians, Raiders and Llort.** |
| Breaching Expert | "+1 thrust" for one pod of a flight that moves as one? "20 % of total pods": fleet-wide, rounded down? What are "3 attack rolls on the first boarding turn" in FV's capture-mission terms? **Rules text 2026-10-21 (§9.31)** settles the last two: "No more than 20% of a fleets breaching pods (round fractions of 0.5 or more up)"; "the pod's contingent gets three attack rolls on the first turn of combat. It is still destroyed by a single hit" - three d10s for its CaptureShip critical on its first turn, one attacker to the defenders. Asked: in a pair of pods, whose Marines get the Elite benefit and the triple roll? His price: one pod flat, or §9.9's scaled rule? The 20%: a report line, also refused in the buy dialog, or enforced by the server? "He does not provide any further benefits" on a pod already Elite: the Elite clause only, or everything? **RULED 2026-10-21 (all as recommended):** **his pod only** (the thrust is the flight's); **one pod, flat**; **a Fleet Correctness Report line**; **the Elite clause only** - +1 thrust and the triple roll still apply. |
| Analyst | Does he need range, line of sight or an OEW lock? Every enemy ship, or only those in range? Weapon arming is already public in FV: leave it so? **Moved to the Hard tier 2026-10-16** (the full rules are more complex than the brief): his own plan and ruling round when he is reached. |
| Coordinator | **Rules text 2026-10-16** (§9.15): "may make a combat pivot for half the normal thrust cost (i.e., 1 point instead of 2) and with no weapons fire penalties as a result, unless the combat pivot was a full 180°, in which case a penalty of −1 is applied"; one-half the base cost of the fighter; one coordinator per flight. Built with defaults, not ruled: (a) "a full 180°" = this turn's combat pivots, rights against lefts, have turned the flight 3 hex sides or more (R-L-R is 60°, no penalty; four one way passed through 180°, penalty); (b) half rounded **up** (a shuttle's 3 → 2, a pivot-cost-3 flight's 5 → 3); (c) the −1 is waived on every shot of the flight that turn, ballistic launches included (the penalty's own scope). |
| Evangelist | The fleet = this player's ships, or the whole team? His penalty is worded "if killed": does a merely **disabled** Evangelist (post destroyed, kill roll survived) cost the fleet its +1 without the −10 initiative / −1 to hit? (Default: yes, the penalty needs a kill.) Is the penalty permanent? Do reinforcements that arrive later get the bonus? |
| Laser Technician | Lasers = `weaponClass` Laser (49 classes)? Is the power boost declared in Initial Orders? Is the +50 % cap on base damage? **Rules text and rulings 2026-10-17 (§9.17):** the text settles the first three - laser-category weapons (any weapon with a Laser mode; only a shot in that mode gains), the boost through the ordinary boost in Initial Orders (the user's design note: "boostable ... 1 power = 1 damage, with no maxBoostLevel but a cap"), the cap on the roll ("if the base roll was 12 damage, it could be raised to 18"). **RULED:** (1) a laser that hits more than once a turn shares its boost as **one pool** for the turn; (2) on a serving Expert Gunner's laser the Gunner's +1 per die **replaces the flat bonus only** - the boost still works there; (3) boosts **hidden from opponents**; (4) the owner's tooltip **Damage line shows** the flat bonus and the boost, with a **Laser boost** line. |
| Plasma Scientist | *Added 2026-10-17 with his rules text (§9.18).* Which plasma weapons are "medium or heavy"? Does a battery start the battle holding the extra point? Are the web's range and the battery's capacity shown to opponents? **RULED 2026-10-17:** every plasma weapon but the light ones (`Officers::$plasmaClassBonus`); yes, a battery still holding its deployment charge starts full; yes, to every viewer (a displayed stat is public). |
| Security Officer | *Added 2026-10-17 with his rules text (§9.19).* The user's note settles the main one: "shift all die rolls by up to 2 in any direction desired by the defending player" is a fixed **+2** ("higher rolls are almost always worse for marine attackers ... no need to build in a player choice"). Asked: do the defenders' own Capture rolls (5 or less hits) shift too? Breaching pods only, or Grappling Claws and transporters as well? 7.5% exactly, or 8%? On a hull with two C&Cs, which gets the -2? **RULED 2026-10-17 (all as recommended):** defenders **-2** too (Elite Crew's pattern, doubled); **every boarding party**; **7.5% exactly** (a percent may now carry one decimal place); **his own C&C only**. |
| Religious Leader | *Added 2026-10-17 with his rules text (§9.20).* Are "+10 initiative" and "+5 to its defense profiles" FV's displayed units or tabletop? What is a "volley" for his +1 damage? Does the ship keep the +1 damage once he is lost? Is the Minbari Protectorate one of the "Minbari" factions? **RULED 2026-10-17:** **FV display units** (+10, then +20; the profiles +1 point each, +5%); **+1 per hit** (each pulse, each gun's shot, a raking shot once, never per die); the frenzy **keeps** the +1 damage; **Minbari Federation only** - the Protectorate is not included. |
| Software Engineer | "Lets the computer specialist use their ability twice" and "Incompatible with Computer Specialist" contradict each other. Which one stands? "Submarine" = the Hyach stealth hulls (Alichi Kav and Tal)? Whose detection range gets +5? **Rules text 2026-10-22 (§9.33)** settles all three: "permits a specialist to use his abilities a second time ... of the software category ... only ... one specialist per scenario" (the user's note: Defence, Sensor, Targeting and Weapon); "if aboard a stealth unit ... five hexes further away for detection purposes ... except for fighters, which halve their detection range (round fractions up)". Asked: build the Firing-phase hold of the extra BFCP, or +1 in Initial Orders only? May his point take a category past two? A specialist used on turn N - again from N+1 or N+2 ("wait at least a full turn")? **RULED 2026-10-22:** **build the hold** - and **rebuild the Computer Specialist to its own rules** on it ("Once per scenario, this specialist adds 1 point to the ship's bonus fire control points and allows these points to be reshuffled to any desired values. This ability is activated during the Firing Phase ... The usual restrictions on the allocation of bonus fire control points must be observed"); **the cap stays two**, and the rebuilt Computer Specialist no longer raises it either; **from turn N+1**. The user's note on the damage clause: "+1 Armour to the Computer system is fine, if adding a damage reduction that affects armour-ignoring weapons is unnecessarily complicated" - it was not (one line beside the Scavenger's save), so the full rule is built. |
| Ion Technician | Rad Cannons always score 12, with no roll? Forced crits on Sensors, Engine and Reactor on every hit? When is the thrust boost declared? Is "+4 on C&C crits" a penalty? **Rules text 2026-10-18 (§9.23)** settles all four: 12 Structure boxes instead of 10 (the Rad Cannon rolls nothing anyway); a forced critical roll on a sensors, engine or reactor hit; the boost "is made at the same time systems are activated or deactivated for extra power" - Initial Orders; the C&C's roll "at +4 on the die instead of the listed +2" - his ship's Rad Cannons hitting an enemy C&C. Asked: may one gun take more than one boost? A boosted gun that does not fire - build the discharge? Does a Sub Reactor count as the reactor? **RULED 2026-10-18:** the boost was first ruled **repeatable** (every 3 thrust another 1d10+1), then, once built, **re-ruled to one level per gun** a turn (the recommendation); the **discharge is built**, with a tooltip line; a **Sub Reactor counts**. |
| Surge Officer | Which systems are the "caps" that get +2 / +1 power? Is "EMHardened gives −3" a change for the whole ship? **Rules text 2026-10-18 (§9.22)** settles the first: "Capital ships and larger receive +2 points of power, while medium ships and HCVs get +1" - the reactor. **RULED 2026-10-18:** the −3 is "−1 on EM-hit systems" (the tabletop −2 → −3 step, where the rule says); then, the user finding FV's hardening to be a simplification, **FV's EM hardening rebuilt to the tabletop rules** (supplied) and the Ring Base hardened, the officer on top; the −2 **once per system per turn**; EM effects the tabletop hardening does not list **apply normally** (the old immunity lines commented out with a note, to revisit); armour-ignoring EM weapons **ignore Ipsha armour fully**; the flat −1 stand-in **removed** from every Ipsha hull; **his own Spark Field gains the +1**. |
| Expert Pilot | *Added 2026-10-19 with his rules text and notes (§9.24).* Several pilots beside another officer (D9 says one; the Spark Field text pictures "four pilots and an evader")? 100% of a fighter each, or the §9.9 scaled rule? The Spark Field relief "if he forgoes all his other benefits for the entire turn" is a per-turn choice: build a toggle? Which factions are "Raiders"? **RULED 2026-10-19:** pilots **ARE the flight's one officer** (1-4 of them, never beside another); **scaled** by §9.9 (100% of a craft each, for a flight of six); the relief **automatic** - "if flight doesn't jink or fire that turn they can get the damage reduction"; **the Raiders faction only**. |
| Expert Targeter | *Added 2026-10-20 with his rules text and notes (§9.26).* His post is a Structure block, and FV rolls d20 + ALL the block's damage for one: under D3 that kills him almost at once - keep D3, roll on this turn's damage, or no kill roll? Which weapons does a Medium ship's side aid: those its hit chart reaches directly, through its Primary result too, or those in that section? A Gunner's gun he covers: both bonuses, his alone, or the Gunner's ("does not combine")? Where does the insignia go on a block's bar? **RULED 2026-10-20:** d20 + **this turn's damage** to the block, 20+ kills, the new code "heavily gated"; the side's **hit chart reach, Primary included**; **his +4 only** - the Gunner keeps his +1 fire control, his once-per-battle +3 neither used nor spent while covered; **in the bar, after the section name**. **Re-ruled after the play test (game 4495):** one **per Structure block**, not per ship, and never moved onto a block another Targeter holds. |
| Expert ELINT Officer | *Added 2026-10-20 with his rules text and the user's list of races (§9.27).* Two WotCR factions carry ELINT hulls the list does not name (Abbai Matriarchate (WotCR), Drazi Freehold (WotCR)): which get him? "The range of all ELINT functions" - which ranges? On a hull with several ELINT arrays, the -2 on every array or his own? His blanket's third: keep FV's fractions or round down? **RULED 2026-10-20 (all as recommended):** the **Abbai WotCR at the Abbai 15%**, the Drazi WotCR left out (as the Centauri WotCR); the **ranged functions** - SOEW, SDEW, Blanket, Disruption, Jamming - not Detect Stealth or the stealth detection range; the -2 on **every** array; **fractions kept** (BDEW 10 gives 3.33 against a ballistic). |
| Expert Tailgunner | *Added 2026-10-21 with his rules text and the user's note (§9.28):* FV applies no tail gun penalty (FV_factions.txt: "In tabletop, this gun has -2 penalty when fired together with main gun. In FV it does not"), so build it first - a tail gun firing alone is free, fired in the same turn as the forward guns -10% - flagging the few fighters with a tail gun rather than testing every fighter. Which tail guns take the penalty? Which tail-gun flights may buy him? A penalised tail gun fired alone under him: +5%, or nothing? **RULED 2026-10-21:** the penalty on the Tiqincc, the Tiqus and the custom tail-gun fighters **"unless $this->hasNavigator = true"** (so never the Badger or the Rogolon Vostor); **all 15** tail-gun flights may buy him; **no bonus** for a penalised tail gun fired alone. **Re-ruled after the first build:** only the craft's other **direct-fire** weapons fired in the Firing Phase count - never a ballistic launch from Initial Orders. |
| Expert Turret Officer | *Added 2026-10-21 with his rules text and the user's notes (§9.29):* FV has no turret system, so he is a system enhancement on a turret weapon - FV already marks turret weapons (the jammable mounts) and their turret mates (the linked firing group) - aiding every weapon on that turret, one officer to a turret; "sheltered" is automatic when no weapon on the turret fires, instead of a declaration UI. Does an interception count as the turret firing? Does a Gunner's +1 on that turret combine with his? Is a turret weapon linked to no other a turret of its own? May he move? **RULED 2026-10-21:** **offensive fire only** breaks the shelter (a turret may intercept and stay sheltered); **no combining** - the Gunner's +1 replaces his on the Gunner's gun, the -1, the -2 and the shelter still apply; **each unlinked turret weapon is its own turret** (Xorr 2, Xonn 3, Tyllz 6); **movable**, as the other placed officers. |
| Ballistic Officer | "+5 distance range" = launch range of every ballistic weapon, torpedoes included? How is "+1 total magazine rounds per missile weapon" counted? How are the free swaps priced (+2 % each)? **Rules text and notes 2026-10-24 (§9.36)** settle all three: "+5 to their distance range (but not their launch range) ... not ... proximity weapons"; "one extra round of ammo ... for missiles ... an extra missile slot" - the user's note: the ship's magazine total + 1 per missile rack, and a weapon with its own count (Proximity Laser, Limpet Bore Torpedo) +1; the swaps free, his price "5% of the base cost of the ship, plus 1% for each missile rack that received a free missile upgrade", doubled outside the Kor-Lyans - the user's note: the missiles bought free up to the racks, the dearest, the rest randomly allocated as the Political Officer's. Asked: which hulls are offered him? Do bomb racks and mine launchers count as racks? What does the ship keep when he is lost? Which rounds count as free picks, and are the unpicked ones filled? **RULED 2026-10-24:** **hulls with counted ballistic ammunition only** (235); **bomb racks are missile racks, a mine launcher adds one magazine slot only**; **the ammunition stays, the range goes**; **paid single-slot rounds only, the rest filled at the commit, his price fixed** at 5% + 1% per rack. **After play test 4512:** never a hull whose magazine has no ordnance to buy (235 -> 226); each type may be bought up to his enlarged magazine. |
| Expert Warrior | *Added 2026-10-23 with his rules text and the user's notes (§9.35):* the Charge a new Movement-phase icon in the Contraction / Half-Phase spot (the user's `chargeIcon.png`, recoloured), its conditions checked by the server; the precision strike "just on the first Called Shot of the game"; Supreme Anticipation the Political Officer's speech method, in the blue menu, with its follow-up penalty; the initiative numbers already FV's. Asked: "Drazi only" - which factions? "Any fighters or shuttles it launches" - which flights? How does a Charge pick its target, and when is it spent? On an Expert Gunner's weapon, do his +1 and +1 add to the Gunner's? **RULED 2026-10-23:** **both Drazi factions** (Freehold and its WotCR); **only flights launched from his ship in battle**; **the player picks the unit** (spending left to the default - a declared charge is spent); **the Gunner's replace his**. |
| Anticipator | Free turn: a full turn (not a pivot) in the Firing phase, paying its thrust? "Half price for some factions": which factions? **Rules text and note 2026-10-21 (§9.30)** settle both: a single turn "if desired", with "Turn Delay complete and enough thrust ... left unused to complete a turn maneuver", "not a roll, pivot, snap turn, or other maneuver" - the user's note: "similar to a fighter's combat pivot except that there are no associated firing penalties and thrust must still be spent on ship thrusters as normal"; 10% for the Torata, 50% more for everyone else (15%). Asked: does the turn take effect before fire resolves, as a combat pivot does, or after all fire? On an Expert Gunner's weapon, the extra die too, or only the Gunner's +1 per die ("does not combine")? **RULED 2026-10-21:** **like a combat pivot** - this turn's fire, his and at him, meets the new facing; on a Gunner's weapon **both apply**. |
| Political Officer | *Added 2026-10-21 with his rules text and the user's notes (§9.32):* the armed shuttle is Elite Crew's own method; the free expert is the dearest of the four bought, one at random with none; the armour is five free Hardened Armour on different systems, random for any left unplaced; the speech an Activate menu on the C&C in the Firing phase ("Speech!" / "Cancel speech"), a fire order in the combat log, +30 the following turn. Asked: "Centauri Only" - which Centauri factions? When are the unmade picks made: by the server at battle start, or in the lobby at purchase? With Elite Crew too, one armed shuttle or two? The +30 if he is killed or disabled in the turn he speaks? **RULED 2026-10-21:** **both Centauri factions** (the Republic and its WotCR); **the server, at battle start** (built as the fleet's commit: rolled once and stored); **they add up**; **no +30 unless he still serves** on the turn it lands. |
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
| **A Dice-level change** (Anticipator's re-roll) breaks every damage line in the harness | Add the same static to the harness's own `Dice` stub ([[project_replay_harness]]). *Done in Stage 23 (§9.30): `$extraDie` and `keepBest` in replayHarness.php and officersHarness.php; officersHarness runs one rule table over its stub AND the real `lib/dice.php` (evaluated in a namespace whose `random_int` is forced), so the pair cannot drift unseen.* |
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

### 9.26 Stage 19: Expert Targeter (2026-10-20)

**Rules text (user, 2026-10-20) - a 30th officer, not in the original brief.** "Expert Targeter: One area in which Raiders excel is
that of knocking critical systems (weapons, thrusters, etc.) off enemy units. In many cases, this is the only way they can complete
their goal of capturing freighters with their cargo intact. The expert target is a true boon to a Raider band. With him on the ship,
called shots have a +4 bonus to hit. This does not provide any benefit to normal attacks, only called shots. It does not cancel the
usual -8 called shot penalty, either. Location: One structure block. All weapons attached to this block receive the benefit. On a
medium ship, he must choose either forward or aft weapons, and will aid only those weapons which can be hit from the chosen
direction (on the Hit Location Chart). Cost: 10% of the ship's base cost for Raiders. Availability: Raiders. Llort can also take at
double the cost." The user's notes: a System Enhancement on the non-Primary Structure blocks, movable to the other outer blocks
during battle; a Medium ship has one block, so instead of Move its menu has a Front / Aft selection (Left / Right on a LeftRight
hull; default Front / Left), and switching sides in Initial Orders costs him that turn, as any officer move does; care with the
insignia on a block's bar ("less room, especially on the Starboard side"); and a hover tooltip on the bar giving the officer's
details "and nothing else", which the bar does not have today.

**Measured before asking.**
- FV's Structure makes the ordinary critical roll - d20 + ALL its damage - and simply has no criticals to apply, so under D3 the
  roll kills him: a block that has taken 10 kills him on 55% of later hit-turns, from 19 on certainly - and on a Medium ship most
  hits land on the one block.
- Raiders + Llort have 41 hulls with ONE Structure block (33 Medium ships, 7 LCVs, the Hawk - MediumShipLeftRight - and the Llort
  Graemlar OSAT, D9's). Every other hull of theirs has outer blocks, the bases' through `Structure::createAsOuter`; the Vree-built
  Jia and Vastar keep their weapons in the ring's locations.
- On those 41 the hit chart and the weapons' locations disagree: the Hactrus's four PRIMARY beams are hit only from the FRONT chart
  ("0:Light Particle Beam"), and the Hawk's FRONT cannon only through PRIMARY. Read off the charts: the 7 LCVs reach every weapon
  from both sides; 12 hulls reach nothing from the aft chart but what Primary gives; 22 split, 13 of them with Primary-chart weapons
  reachable from either side; no weapon is out of every side's reach.

**Rulings (user, 2026-10-20, asked before building):**
1. His kill roll is **d20 + the damage his block took THIS turn** (through armour), 20+ killed - not D3's roll on all the block's
   damage. "Make any new code heavily gated since this will be a rare occurrence."
2. A Medium ship's side aids the weapons **its hit chart reaches, directly or through its Primary result** - so a Primary-chart
   weapon is aided from either side, and an LCV's every weapon from both.
3. A called shot from an **Expert Gunner's** gun that he covers gets **his +4 only**: the Gunner keeps his +1 fire control; his
   once-per-battle +3 called shot is neither used nor spent while the gun is covered, so it is still his if he moves or the
   Targeter falls.
4. The insignia sits **in the bar, after the section name**.
5. *(After the user's play test, game 4495, the same day.)* **One per eligible Structure block**, "like other System Enhancements",
   not one per ship - and a Targeter is never moved onto a block where another one is. Built as a `shipLimit` (`targeterLimit`, the
   blocks he could serve in: 4 on a four-block capital, 2 on an HCV, 6 on a six-block base, 1 on a Medium ship) - the offer's cap
   slot, which the lobby's `set()` already obeys. `keepPlacedRows`' one officer to a system, and the move rule's occupancy test
   (mayMoveTo; the lobby's lobbyPostAs; the game's `destinations`), already kept two off one block: proved, not changed. With one
   per block no weapon is ever covered twice.

**Built with defaults, stated here (ask before changing):**
- **Posts.** Any outer Structure block (location not 0) of a Raiders or Llort hull; a hull with none - a Medium ship, an LCV - is
  offered its Primary Structure. ~~One per ship~~ - **re-ruled the same day (ruling 5): one per block** he could serve in.
- **A block covers the weapons in its own location** ("attached to this block"), so Primary weapons never, and a weapon a hit chart
  happens to reach from that side ("0:..." entries) only if it sits in the block's location. The jump drive (a `Weapon` in FV) never.
- **A Medium ship's sides** are its hit chart's non-Primary locations in order - 1 / 2 (Front / Aft), or 3 / 4 (Left / Right) on a
  LeftRight hull - each reaching, from its own bearing, every weapon its chart names ("N:" redirects and "TAG:" entries included;
  Structure ignored) and, through its "Primary" result, every weapon the Primary chart names. He covers the FIRST side when bought.
  The second side is offered (OFF_TARG2) only where the two sides reach different weapons, so an LCV has no selector.
- **+4 (+20%)** on a standard called shot - a call on a system with no flat profile of its own, i.e. every call that pays the -8;
  the -8 itself unchanged (-16 against a base); nothing on an ordinary shot or a Kirishiac flat-profile call. Ballistic called shots
  too (declared in Initial Orders, resolved when he serves or not).
- A **called Piercing shot** from a covered Gunner's gun: the Gunner's Piercing branch is unchanged (his Piercing ability is not a
  called-shot bonus), and the +4 applies.
- **Out of action.** Disabled when his block is destroyed (an outer block also falls with the Primary - the ship is lost then
  anyway); killed on ruling 1's roll, made on every turn the block takes damage through armour, while he serves or on the turn he is
  put out. Pre-battle damage destroying his block refunds him (Stage 15's rule).
- **Moves.** Between outer blocks in Initial Orders by Stage 10's rules (one a turn, serving nowhere that turn, never onto a
  destroyed block); free in Fleet Selection (Stage 15). A **side switch is a move to his own post** - the transfer note names his
  own block, and he becomes the other side's id there - so it costs the turn exactly as a move does, and is free in Fleet Selection.
- **Price** 10% of the hull, Llort 20% ($factionRules); "Raiders" is the `Raiders` faction (the Stage 18 reading - not the
  Escalation Wars raider factions).

**Design.**
- ⭐ **Two placed ids, the twin pattern** (OFF_MWE / OFF_MWEW, OFF_RELI / OFF_RELIP): `OFF_TARG` - a block, or a single-block hull's
  first side - and `OFF_TARG2` (`sameAs` OFF_TARG: his faction rules and price, one or the other) - its second side. A per-system
  row on the Structure, so the lobby buys him in the block's own menu (the health bar already opens it; Hardened Armour is sold
  there), and saved fleets, the D13 name check, the D11 refund and the sanitiser come free; `keepPlacedRows`' one officer per system
  keeps the two side rows exclusive. New registry keys: `switchTo` (the id he becomes on his own post - the side switch) and `side`
  (which of a single-block hull's sides he covers). `shipLimit` `targeterLimit` (ruling 5): one per block he could serve in.
- **The +4**: `Officers::targeterShotBonus` beside `gunnerShotBonus` in `Weapon::calculateHitBase`, behind `$officersPresent`, and
  only for a standard called shot: a list walk on a called shot by a ship with officers. `gunnerShotBonus` asks `targeterFor` before
  spending the called +3. ⚠️ MIRROR PAIR `officers.targeterShotBonus` in `weaponManager.computeShotModifiers` ("Expert Targeter").
- **Coverage is the server's alone**: `Officers::targeterCovers` (location, or `sideReach` over the hit chart through
  `getSystemsByNameLoc`), sent to the owner as the payload's `covers` (weapon ids) - so the client mirror reads a list and copies no
  hit-chart logic. The lobby needs no coverage (it shows no hit chance).
- **The kill roll** (ruling 1): one branch in `rollKills` for a Structure post, `blockKillRoll` = d20 + floor(`damageReceivedOnTurn`)
  + the critRollMods - reached only for a post holding an officer that took damage this turn, behind `$officersPresent`.
- **The side switch**: `mayMoveTo` answers a move to his own post with his `switchTo` id, where that is offered; `moveTargets` then
  lists his own block among the `targets`, which the client reads as "may switch". Everything after - the note, `transit`, `moving`,
  the blue insignia - is Stage 10's.
- **UI.** `ShipSection`'s bar: the insignia after the name; the pick outline on a destination bar; a click hands a pick its answer,
  and in Initial Orders opens the Officers menu (SystemClicked, menu); a hover (or long press) raises the officers-only tooltip
  (`SystemInfo` `officersOnly`) through the bar's own StructureMouseOver / Out events, in game and in the lobby, only when the viewer
  has an officer listed there. The Officers menu and the lobby menu's footer show a side selector for a side-switching Targeter in
  place of Move. `officers.systemName` names a block by its section ("Aft Structure #20").

**Files.** Server: `Officers.php` (constants, `$factionRules` OFF_TARG, registry `OFF_TARG` / `OFF_TARG2` after OFF_RELIP; `mayMoveTo`'s
switch; `isTargeterPost`, `isTargeterSecondSide`, `hasOuterBlock`, `hullSides`, `sideReach`, `targeterCovers`, `coveredWeapons`,
`targeterFor`, `targeterShotBonus`; `gunnerShotBonus`; `rollKills` / `blockKillRoll`; `addForJson` `covers`), `Enhancements.php`
(per-system registry entries and wrappers), `weapon.php` (`calculateHitBase`). Client: `officers.js`, `weaponManager.js`, React
`ShipSection.js`, `SystemInfo.js`, `OfficersMenu.js`, `SystemEnhancementsSection.js`, `ApplyDamageMenu.js`, `PhaseStrategy.js`,
`gamelobby.js`, `systemEnhancements.js` (the two labels). Docs: an Expert Targeter card (Ship Officers, alphabetical), About Officers
(buying in a block, moving, the insignia and the bar's tooltip). Statics, bundles and the served copy rebuilt; no new PHP class.

**Measured.** 254 OFF_TARG offers (Raiders 203, Llort 51) and 34 OFF_TARG2 (Raiders 33 - 32 Medium ships and the Hawk - Llort 1, the
Terillon), statics +7.3 KB uncompressed. Caps (the offer's 6th slot, ruling 5): 1 on a Medium ship, 2 on an HCV, 4 on a four-block
capital, 6 on a six-block base.

**Verified.**
- `officersHarness.php` **1316 / 0 with the database** (96 new): the registry pair; offers on every outer block (Battlewagon 4, a base's
  6 createAsOuter blocks), Llort 20%, a Medium ship's two sides, an LCV's one, none for the Omega, an OSAT or a Dilgar hull, none with
  OFF_TARG opted out, the Gunner still weapons-only; posts from rows (a Primary block or a weapon buys nothing; one per block, two on
  one block one); the sanitiser; coverage through the payload's `covers` - a block's own weapons (not the Primary ones, the jump drive
  or the RammingAttack), the Wolf's two sides with its Primary beam in both, the Hactrus's Primary beams on its front chart, the
  Hawk's Front cannon from both sides, the Lshon's every weapon; the REAL `calculateHitBase` (+20% on a covered call, nothing ordinary,
  uncovered, gate off, disabled, killed, moving; the Aft weapons once he arrives; the Medium ship's sides; a covered Gunner's +5 and
  +20 with nothing spent, his +15 back once the Targeter is killed); the kill roll (17 + 3 kills, 16 + 3 does not, 30 earlier damage
  ignored, the ship's critRollMod, no roll without damage through armour, a block destroyed this turn rolled, last turn's
  disabled); moves (to Port, refused to the Primary block, a weapon, his own block on a big ship, a wreck; targets; two Targeters -
  never onto the other's block, onto a free one, onto a killed one's; each one's targets the free blocks); the side switch (moving
  to his own block that turn, OFF_TARG2 the next, back again; refused on an LCV; targets; saveTransfers' note), a Gunner's own-gun
  order still refused; a REAL saved fleet (a Battlewagon's Aft block at 54, a Wolf's Aft side at 45), rolled back.
- `officersClientHarness.js` **500 / 0** (44 new): the registry mirror and order; block names; summaries; sides; the +4 and its
  states; the Gunner's +3 giving way; the REAL `computeShotModifiers`; the side selector in the REAL OfficersMenu (order, claim,
  "ordered to switch", the blue insignia, cancel); the REAL ShipSection bar (insignia after the name, gold / killed, nothing without
  an officer, the pick outline, a click ordering the move, the Initial Orders menu click, nothing in the Firing phase, the hover's
  element); the officers-only SystemInfo; the REAL PhaseStrategy.js hover handlers (shown, hidden, another block, a menu left open);
  the lobby (one row a block, the selector, a free switch and back, an LCV, Move on a big ship, a second Targeter bought, never moved
  onto it); the REAL footer.
- `enhancementsDifferential.js` **707 / 0** (9 new: `targ`, `targ+gun` on Areko; `targ`, `targ2`, `targ+targ2` on the Wolf, added for
  the placed cases only); `enhancementsHarness` **282 / 0** (fingerprint unchanged - ship-level offers only).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0**.
- **Mutation-tested: 34 breaks (16 server, 18 client), all caught**, files byte-identical afterwards. One first exited on an unguarded
  read (`payload.element.tag`) rather than a FAIL line; the check was made null-safe and then reported it.
- **Play test, game 4495 (user, 2026-10-20)** - read off its rows and a real load: three Targeters (a Pillager and an IML Attack
  Cruiser moved between blocks on turn 2, the Terillon switched to Aft - an OffTransfer to its own block, OFF_TARG2 from turn 3). On
  turn 1 the cruiser's covered Port beam needed 115 against 95 for its uncovered Starboard beam on the same target, and the Terillon's
  covered Front Scattergun 70 against 50 for its Aft one - +20 exactly. The user then re-ruled the limit (ruling 5).

**Follow-up (user request, 2026-10-20): the bar's ✦ and the menu outline** - both observations of the first build, fixed:
- A block carrying a system enhancement (Hardened Armour) shows SystemIcon's ✦ on its bar (`EnhancementStar`, now exported), after
  the name and before an officer's insignia - SystemIcon's badge order. Own team only, as the icon's (`systemEnhancements.hasAny`,
  which skips officer rows: a Targeter alone lights no ✦).
- The lobby menu's outline (helpers/menuTarget) draws on the block's bar while its menu is open, in the menu's focus blue, on the
  bar's edge as the pick outline is.
- **Room on the bar, measured** (`officersE2E/lobby19.mjs`, standalone, real bundles): every one of the 78 hulls with a 3-digit
  Port / Starboard / quarter block, plus Battlewagon, Brigand, Wolf, Hawk, Sanctuary, Erlorra, Kaedashkada and Omega, Hardened Armour
  on every block and a Targeter on every block offered one - **463 / 0**: no badge ever overlaps the readout, no readout is ever
  clipped. "Starboard" was the one name a 128px side column had no room for (49 bars, the Battlewagon's 52/52 among them), so with a
  badge it reads **"Stbd"** - the window's own quarter spelling. The name, never the readout, shortens with an ellipsis if still
  short of room (the readout does not shrink): 4 bars, the Kaedashkada (New)'s quarter blocks with both badges and 3-digit boxes.
- officersClientHarness **507 / 0** (7 new: the ✦ and its order, the ✦ alone, none elsewhere, the ellipsis and the fixed readout,
  "Stbd" only with a badge, the outline on the menu's block only, gone on close). Bundles rebuilt, served copy synced.

### 9.27 Stage 20: Expert ELINT Officer (2026-10-20)

**Rules text (user, 2026-10-20) - a 31st officer, not in the original brief.** "Expert ELINT Officer: With Abbai advances in electronics
and communications, it is only natural they have experts in this field. The expert ELINT officer is one such individual. He provides the
following advantages: The sensor rating of his ship is increased by + 1. All critical hit rolls against the sensors are reduced by 2. The
range of all ELINT functions is increased by 25% (round fractions of 0.5 or more up). If the ship puts up blanket protection, the total EW
value is divided by 3 (not 4) for purposes of ballistic weapons only. Thus, if 12 EW is applied to blanket protection, all friendly units in
range receive - 3 to their defense ratings for normal weapons and - 4 versus ballistic types. Location: Sensors. Cost: 15% of the ship's
base value for Abbai factions. Availability: ELINT vessel only. Double the cost for non-Abbai vessels. Not available for races whose current
mainline ships do not have at least an 8 sensor rating." The user's notes: the non-Abbai factions that may take him are the Centauri
Republic (not the WotCR Centauri), the Earth Alliance (not EA Early), Narn, Minbari Federation, Yolu, Torata, Gaim, Drazi, Vree, Dilgar,
Brakiri, Hyach, Markab, Corillani, Llort, Deneth and the Minbari Protectorate; no custom faction for now.

**Measured before asking.**
- FV's ELINT ranges: SOEW 30 (ELINT to target and to the supported ship), SDEW 30 at declaration but **50 in the server's firing check**
  (`EW::getSupportedDEW`; the tooltip says 30), Blanket 20, Disruption 30, Jamming 30 (`HkJamming::JAM_RANGE`). Detect
  Stealth has no range: +2 detection per point, on top of an ELINT's x3 stealth detection range. Declaration is checked only in the client
  (the Initial Orders menu's `isInElintDistance`); use, on both ends (the EW sweeps, ew.js twins).
- FV's blanket keeps fractions: the best ELINT's points x0.25 (BDEW 10 = 2.5), taken once in `EW::getBlanketDEW` - which every hit
  calculation asks, ballistic or not.
- From the statics: 44 ELINT hulls in the 19 named factions, 52 with the Abbai WotCR (3 of whose 8 are OSATs, D9's); none mixes a plain
  Scanner with an ELINT array; four carry several arrays - Oracle Scout AM (6 + 4), Oracle Scout Epsilon (7 + 4), Eyehawk (8 + 4),
  Fwellgon AM (6 + 2 + 2). Two WotCR factions the list does not name carry ELINT hulls: Abbai Matriarchate (WotCR) and Drazi Freehold (WotCR).

**Rulings (user, 2026-10-20, asked before building - all as recommended):**
1. The **Abbai WotCR are Abbai** (15%); the Drazi WotCR are left out, as the Centauri WotCR are.
2. His +25% covers the **ranged functions only**: SOEW, SDEW, Blanket, Disruption, Jamming - 30 -> 38, 50 -> 63 (SDEW), 20 -> 25. Detect
   Stealth and the stealth detection range are unchanged.
3. The -2 is on the critical rolls of **every** sensor array on his ship; the +1 on his own.
4. His blanket's third **keeps FV's fractions**: BDEW 10 gives 2.5 against an ordinary weapon and 3.33 against a ballistic one.

**Built with defaults, stated here (ask before changing):**
- **Post**: his ship's strongest ELINT array by output (the first on a tie - none exists among his hulls); the Chameleon suite is one.
  Out of action as any officer: disabled when the array is destroyed, killed on its own critical roll of 20+ - which his -2 eases ("including
  any modifications", the Technician's and the Security Officer's precedent). Pre-battle damage destroying it refunds him (Stage 15).
- **Against each weapon kind the best ELINT wins**: his 9 points beside a plain ELINT's 12 give 3 against either (2.25 / 3 for his); his 12
  beside a plain 12 give 3, and 4 against a ballistic. Fighter missiles count as ballistic (a fighter's direct fire ignores the blanket as
  before).
- **Public numbers** (the 2026-10-10 rule): the ranges and the ballistic third reach every viewer (`elintRangeBonus`, `blanketBallistic` on
  the ship payload) - an opponent's own OEW under his Disruption, and its hit chance against a unit under his blanket, need them - and the
  array's tooltip shows them to everyone; the officer himself stays own-team.
- 30% / Abbai 15%, rounded up as every officer's; one per ship.

**Design.**
- Registry `OFF_ELINT` after OFF_ION (the last fixed-post ship officer), post `elint` (findPost: the strongest `ElintScanner`), `$factionRules`
  `only` (the 19 factions) + `pct` (the two Abbai factions at 15). `applyElintOfficer`: `output` +1 on his array, `critRollMod` -2 on every
  `Scanner` of his ship.
- ⭐ **`Officers::elintRange($elint, $base)` is THE range of an ELINT function**: 25% longer while he serves, rounded half up in whole numbers
  (`intdiv(base x 125 + 50, 100)`), behind `$officersPresent`. Asked at every range test: `EW::getBlanketDEW` (20), `getSupportedOEW`
  (30, both legs), `getSupportedDEW` (50), `getDistruptionEW` (30), `HkJamming` (`JAM_RANGE`). ⚠️ MIRROR PAIR `officers.elintRange`, which reads
  the published percent (`ew.elintRange` delegates; ew.js getSupportedOEW / getSupportedBDEW / getDistruptionEW and the Initial Orders menu's
  `isInElintDistance` ask it).
- **The ballistic third**: `EW::getBlanketDEW($gamedata, $target, $ballistic = false)`; for a ballistic shot (behind `$officersPresent`) each
  ELINT's points pass through `Officers::ballisticBlanket` - x4/3 for his, counted in the quarters the existing x0.25 takes, so the best
  ELINT still wins and every other shot runs the unchanged path. `Weapon::calculateHitBase` passes `$this->ballistic`; every other caller
  (EdjdAbduction) the default. ⚠️ MIRROR PAIR `ew.getSupportedBDEW(ship, ballistic)` / `officers.ballisticBlanket`, at weaponManager's two
  call sites (the live `computeBaseDefenceBreakdown`; `calculateBaseHitChange`, whose ShipTooltip caller has no weapon - ordinary).
- **Lobby**: his +1 is added at read time in `shipManager.systems.getOutput` / `getOutputNoBoost` for an EW system (`officers.sensorBonus`,
  0 in game - the server raised `output`), so the icon and the EW pool show it; the differential compares it (an EW system's output +
  sensorBonus, as the reactor's + reactorBonus). The array's Special text is rewritten as it is shown (`officers.elintSpecial`, from
  `adjustDataDisplay`): "30 hexes" -> 38, "20 hexes" -> 25, and the blanket's quarter gains "(per 3 against ballistic weapons)" - in game
  for every viewer off the published numbers, in the lobby off his bought row.

**Files.** Server: `Officers.php` (constants, `$factionRules`, registry, findPost `elint`, `applyElintOfficer`, `elintRange`, `ballisticBlanket`,
`addForJson`), `EW.php` (the four sweeps), `HkJamming.php`, `weapon.php` (calculateHitBase). Client: `officers.js` (registry row, findPost,
`sensorBonus`, `elintRangeBonus`, `elintRange`, `ballisticBlanket`, `elintSpecial`, adjustDataDisplay), `ew.js` (`elintRange` and the three
sweeps), `systems.js` (getOutput / getOutputNoBoost), `weaponManager.js` (two call sites), `UI/shipTooltipInitialOrdersMenu.js`
(isInElintDistance). Docs: an Expert ELINT Officer card (Ship Officers, alphabetical - first, before the Engineer) and his numbers in About
Officers' list of what an opponent sees. Statics rebuilt; no new PHP class, no React change.

**Measured.** 49 OFF_ELINT offers (the 44 + the Abbai WotCR's 5 non-OSAT hulls), statics +2.6 KB uncompressed: Abbai 2, Abbai WotCR 5,
Brakiri 7, Centauri Republic 5 (the Dargan Chameleon included), Corillani 2, Deneth 1, Dilgar 2, Drazi Freehold 1, Earth Alliance 3, Gaim 2,
Hyach 2, Llort 1, Markab 2 (the Shadrak base included), Minbari Federation 2, Minbari Protectorate 2, Narn 5 (three of them unofficial
zzunoff hulls), Torata 2, Vree 2, Yolu 1.

**Verified.**
- `officersHarness.php` **1374 / 0 with the database** (55 new): the registry; offers and prices on 13 hulls across the factions (Skiatha 690
  -> 104, Delphi 825 -> 248, the Nota at the Abbai 15%), none for the Garut, the Oracle Alpha, the Egret, the Explorer Prototype, the Kirva
  OSAT, the Omega or the G'Quan; posts (the 6-array of the Oracle, the Fwellgon's 6, the Chameleon suite), unmoved by Improved Sensors and
  Poor Crew; a doctored row buys nothing; +1 on his array only, -2 on both of the Oracle's, the EW pool +1, a Hyach array too; the REAL
  testCritical roll 21 -> 19 (survives), 22 -> 20 (killed, one note); killed or disabled last turn - nothing, ranges 30, nothing published;
  the payload per viewer; `elintRange` (38 / 25 / 63 / 0, gate off 30); the REAL `getBlanketDEW` (3 / 4 at 22, nothing at 26, a plain ELINT
  nothing at 22 and 3 / 3 at 18, 2.5 / 3.33 for 10 points, gate off, two ELINTs both ways), `getSupportedOEW` (35: 3 / 0, 39: 0),
  `getSupportedDEW` (55: 2 / 0, 64: 0), `getDistruptionEW` (35: 2 / 0, 39: 0), `HkJamming::resolveJamming` (rolled at 35 from his only); the
  REAL `calculateHitBase` - a Norsca's Ion Torpedo needs 5% less under his blanket 12, its Rad Cannon the same.
- `officersClientHarness.js` **530 / 0** (23 new): the registry mirror and order; findPost on the Oracle's and the Chameleon's REAL static
  systems; `sensorBonus` (lobby, other array, none, in game, pre-battle destroyed); the REAL systems.js getOutput / getOutputNoBoost;
  `elintRange` / `ballisticBlanket`; the REAL ew.js getSupportedBDEW, getSupportedOEW and getDistruptionEW; the REAL
  computeBaseDefenceBreakdown (BDEW 4 against a ballistic, 3 against a beam); the tooltip text (published, lobby, none, the Chameleon
  suite's own text unchanged); the REAL SystemInfo (an opponent's array: the ranges, no officer; his own: his line too); the REAL Initial
  Orders menu's SOEW / SDEW / DIST buttons at 35 and 39 hexes.
- `enhancementsDifferential.js` **709 / 0** (2 new: `elint`, `elint+sens+poor1` on the Nota); `enhancementsHarness` **283 / 0** - the
  fingerprint re-recorded twice and proved additions only: one offer line (the Nota's, old copy `fingerprint.before-officers-stage20.txt`),
  then 32 new case blocks (16 hulls x 2), the rest byte-identical.
- `initialOrdersTooltipHarness.js` reads its known 4 / 10, identical to the output recorded on a clean tree (its stale stub lacks
  `shipManager.isTargetable` - [[arch_stale_stage_harnesses]]) - so the menu's range is tested in officersClientHarness instead.
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0**. Legacy bundles carry the change (the watcher's); served copy synced.
- **Mutation-tested: 31 breaks (17 server, 14 client), all caught**, files byte-identical afterwards. One first exited on an unguarded read
  (`findPost(...).name` on null, when the Chameleon suite was dropped from the post) rather than a FAIL line; the check was made null-safe and
  then reported it.

**Follow-up (user, 2026-10-20, the same day): two FV ELINT corrections, for every ELINT, not only his.**
- **SDEW's base range is 50**, as the server's firing check already had it: the Initial Orders menu's Add / Remove SDEW now offer it up to 50
  hexes (was 30), the ELINT array's tooltip and the FAQ say 50. With him 63 (the tooltip rewrites 50 -> 63 as well). The client's
  `ew.getSupportedDEW` - the hit-chance preview - checked no range at all; it now drops SDEW beyond `ew.elintRange(elint, 50)`, as the server
  does at firing (user request, the same day; client 532 / 0, mutation 2 / 2).
- **The blanket drawn on the map** (`ShipIcon.showBDEW`, user report the same day: it drew 20 hexes around his ship) - the radius is now
  `ew.elintRange(ship, 20)`, 25 with him, off the published number, so an opponent's map shows his reach too. client 533 / 0 (the REAL
  showBDEW, its HexRegion stubbed: 25 / 20), mutation 1 / 1. The Kirishiac module arc (`ELINT_ARC_HEXES` 30) is left: no Kirishiac unit
  can take him.
- **Blanket Protection's ConstrainedEW is the ELINT's**: `EW::getBlanketDEW` gave a fifth instead of a quarter when the TARGET was a Mindrider,
  once after the loop; now each ELINT's own ability decides, per ELINT, as `ew.getSupportedBDEW` always did. Unchanged wherever no Mindrider
  ELINT is on the board (a quarter of the best, or the best of the quarters, is one number); a Mindrider ELINT's blanket on an ally is now a
  fifth (BDEW 10: 2.5 -> 2), an ordinary ELINT's on a Mindrider a quarter (12: 2.4 -> 3).
- officersHarness **1375 / 0** (+1), client **531 / 0** (+1, SDEW 50 / 63 in the real menu; the tooltip checks now name SDEW 63 and
  Disruption 38 apart); statics rebuilt (the array's text); kirishiacRefits 168 / 88, enhancements 283, differential 709, replay 162 / 0 - no
  corpus game moved. Mutation 3 / 3 (the target read back, the menu's 30 back, the tooltip's 50 left long).

### 9.28 Stage 21: tail guns, and the Expert Tailgunner (2026-10-21)

**Rules text (user, 2026-10-21) - a 31st officer, not in the original brief.** "Expert Tailgunner: This fighter-only officer is adept at
operating his fighter's tailgun in concert with the forward-mounted weapons, and can train the rest of his fight in this special skill. The
flight receives no penalties when using the tailgun in the same turn as the other guns. Cost: Half the cost of a fighter. Availability: Races
can use this officer if they have fighters that meet the requirements e.g. an aft mounted weapon. If the tailgun has no penalty associated
with it, as in the Badger, the officer provides a +5% to hit with that gun only." The user's notes: FV may not apply any tail gun penalty
(the Cascor Tiqincc's -10%; the Earth Alliance Badger has none) - if not, build it first: a tail gun fired alone is free, fired in the same
turn as the forward guns -10%; and rather than testing every fighter, flag the few that have a tail gun.

**FV had no penalty.** FV_factions.txt said it outright ("Tiqincc ... In tabletop, this gun has -2 penalty when fired together with main
gun. In FV it does not."), and nothing in the to-hit code knew a tail gun. Measured over the statics: **15 flights** carry one - a weapon
whose arc covers dead astern and not dead ahead, beside a forward weapon (side guns, 0-180 / 180-360, excluded): Tiqincc, Tiqus, Badger,
Kobol Raptor Support, Circasian Kolam, Sal-bez Crevnen / Drazchk / Okchn, Velrax Resterax / Resterax Refit / Soress PDC, Rogolon Vastur /
Vostor / Vostor AM and the Star Wars ARC. Only the Tiqincc's notes named a penalty. The one local game holding any (4241, two Tiqincc,
surrendered on turn 1) fired nothing, so the replay corpus could not move.

**Rulings (user, 2026-10-21, asked before building):**
1. **The penalty: the Tiqincc, the Tiqus and the custom fighters found "unless $this->hasNavigator = true".** That is a rule, not a list: a
   flight with a navigator never takes it, so 8 of the 15 are penalised (Tiqincc, Tiqus, the three Sal-bez, the two Resterax, the ARC) and
   7 are not (the Badger, the three Rogolons, the Kobol, the Kolam, the Soress) - the two official Rogolon Vostors included, both navigated.
2. **All 15 tail-gun flights may buy him** (offered: the four official flights only).
3. **No +5% for a penalised tail gun fired alone** under him: the +5% is for a gun with no penalty in its rules; a penalised one only has it
   waived.
4. **Only DIRECT-FIRE weapons count** (re-ruled after the first build, which counted every weapon): "it should not count ballistic weapons
   fired in Initial Orders phase like Torpedoes, only other direct fire weapons fired during Firing Phase."

**Built with defaults, stated here (ask before changing):**
- "The other guns" = any other DIRECT-FIRE weapon of the SAME craft fired offensively that turn (ruling 4): a weapon flagged `ballistic`
  (FV's flag is per weapon, never per mode) never counts, nor do an interception, a self-intercept, a Meteor Defence declaration or the
  ramming attack - exactly the filter `isFtrFiringNonBallisticWeapons` already applies (`!ballistic && firedOffensivelyOnTurn`). Each craft
  is its own: another craft's forward gun does not touch this one's tail gun.
- Only the tail gun is penalised; the forward guns never are.
- A Navigator BOUGHT on a penalised flight (the ARC may buy one, 10 points) lifts the penalty too - it is the same `hasNavigator`, which the
  server already publishes once bought.
- Lost with his craft (destroyed, dropped out), as every fighter officer; no kill roll. "Half the cost of a fighter": 50% of a craft for a
  flight of six, scaled by §9.9 (Tiqincc 12 / 23 / 46 at 3 / 6 / 12 craft; Badger 35 a six).

**Design.**
- ⭐ **`Weapon::$tailGun` is PROTECTED** (false), set by `setTailGun()` in the fighter's own file (one line each in the 15), and read by
  `isTailGun()`. `ShipCompactor::annotateSystems` writes `tailGun: true` into the static blueprint - the `$seedsBelowFullCharge` /
  `survivesStructureDestruction` route - so it is on no payload: a PUBLIC property on Weapon lands on every missile of every MissileRack's
  payload (MissileRack::stripForJson reflects public properties). game.php reads it off the blueprint, as the lobby does.
- ⭐ **`Weapon::getTailGunMod($flight, $turn)` is THE rule**, one line in calculateHitBase's fighter branch (every tail-gun class reaches the
  base method - none overrides it): -`TAIL_GUN_PENALTY` (2, -10%) when `craftFiresOtherWeapon` (the craft's other non-ballistic weapons),
  unless `$flight->hasNavigator`; under a
  serving Tailgunner (`Officers::hasTailgunner`, behind `$officersPresent`) 0 on a penalised gun, +`Officers::TAILGUNNER_BONUS` (1) on the
  others. ⚠️ MIRROR PAIR with `weaponManager.getTailGunMod` / `craftFiresOtherWeapon` (the real `getFighterBySystem`), which
  `computeShotModifiers` shows as "Tail Gun -2" or "Expert Tailgunner +1". The preview follows the orders declared so far: a tail gun
  declared before its craft's other guns shows no penalty, though the server's resolution has it.
- The registry entry `OFF_TAIL` (last - a doctored second row never displaces an officer bought before him) `needs` `hasTailGun` (the first
  craft carries a flagged weapon) ↔ `officers.passesNeeds` 'tailGun'. No `apply`, no published stat.
- **Tooltip**: the server writes a "Tail Gun" line on the gun ("-10% on a turn its fighter fires another direct-fire weapon", or "No penalty
  (navigator)"); `officers.tailGunLine` (through `adjustDataDisplay`) keeps it right for a Navigator bought in the lobby and, for his own team,
  under the officer ("No penalty (Expert Tailgunner)" / "+5% (Expert Tailgunner)").
- Docs: an Expert Tailgunner card (Fighter Officers, alphabetical - last), explaining the tail gun rule too. FV_factions.txt's Tiqincc line
  corrected.

**Files.** Server: `weapon.php` (`$tailGun`, `setTailGun`, `isTailGun`, `TAIL_GUN_PENALTY`, `getTailGunMod`, `craftFiresOtherWeapon`, the
calculateHitBase line, the data line), `Officers.php` (`TAILGUNNER_BONUS`, `OFF_TAIL`, `hasTailGun`, `hasTailgunner`, header), `ShipCompactor.php`
(annotateSystems), the 15 fighter files. Client: `weaponManager.js` (`getTailGunMod`, `craftFiresOtherWeapon`, the breakdown line),
`officers.js` (registry row, `passesNeeds`, `hasTailgunner`, `tailGunLine`, the adjustDataDisplay hook). Docs `ammo-options.html`;
`FV_factions.txt`. Statics, legacy bundles (`scripts/bundle-legacy.js` - the watcher was not running) and the served copy rebuilt; no new
class, no schema change.

**Measured.** Statics +1,780 B in all: 15 `tailGun` flags (one a craft), 15 offers, 15 "Tail Gun" lines. Offers: Tiqincc 23, Tiqus 23,
Badger 35, Kobol 48, Kolam 19, Crevnen 9, Drazchk 26, Okchn 50, Resterax 22, Soress 43, Resterax Refit 25, Vastur 63, Vostor 43, Vostor AM
43, ARC 38.

**Verified.**
- `officersHarness.php` **1413 / 0 with the database** (38 new): the 15 flags (one a craft, aft, beside a forward gun;
  navigators on the 7); the blueprint flag on the tail gun alone, none on an Aurora; the tooltip lines; offers on exactly the 15 at their
  prices, none on an Aurora, a shuttle, a ship or a Hunter-Killer; flightPrice 12 / 23 / 46; doctored rows; the REAL `calculateHitBase` -
  -10% with the craft's forward gun, none with another craft's, an interception / self-intercept / Meteor Defence, last turn's order or the
  ram, none with a navigator (one bought too), the Tiqus's -10%, the forward gun never penalised, the Badger none, the ARC's torpedo launch
  NOT counting (ruling 4) and its Medium Lasers counting; under him none on the Tiqincc (alone no bonus), the -10% back with the gate off, beside a Dogfighter, or with his craft lost
  last turn (not this turn), the Badger's tail gun +5% alone or with the forward gun, its forward gun nothing, +5% on a Tiqincc with a
  Navigator bought.
- `officersClientHarness.js` **553 / 0** (20 new): the registry row; EVERY static blueprint - the 15 flagged flights are exactly those with a
  rear-only gun beside a forward one, and exactly those offered him; the lobby derivation (a Tiqincc yes, a doctored Aurora row no); the real
  `getTailGunMod` and `computeShotModifiers` on two-craft flights built from the real static blueprints (each case the server's, the ARC's
  torpedo and lasers included, and a tail gun's own declared order not counting against it); the tooltip
  line through `adjustDataDisplay`. The Stage 18 "OFF_PILOT last in the registry" check now reads one from last.
- `enhancementsDifferential.js` **709 / 0** (no case added: he moves no lobby stat, and no differential hull has a tail gun);
  `enhancementsHarness` **284 / 0** (fingerprint unchanged: ships only).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0**.
- **Mutation-tested: 37 breaks (20 server, 17 client), 36 caught**, files byte-identical afterwards (both harnesses green again). The one
  miss is equivalent: `hasTailgunner` dropping its `$turn` - its only caller passes the current turn, the default. A first pass on the
  pre-ruling-4 code (35 breaks) had missed one more, a tail gun's own order counting against it on the client - the check was added.

**Observations, not changed:**
1. officersHarness's Stage 18 rail-escape check ("...and a run let some pilots go and kept others") failed once in four runs during this
   stage - it needs a random_int escape of 1-3 pilots within 40 tries, and its comment's P(miss) ~ 1e-18 is clearly too optimistic. Not
   touched by this stage; worth making deterministic.
2. Not play-tested: no local game holds a tail-gun flight. Worth a test: a Tiqincc flight firing both guns at one target (the tail gun's hit
   chance 10% lower in the breakdown and the log), then one with an Expert Tailgunner (23 points) doing the same (no penalty), and a Badger
   with one (+5% on its Uni-Pulse tail gun only).

### 9.29 Stage 22: Expert Turret Officer (2026-10-21)

**Rules text (user, 2026-10-21) - a 32nd officer, not in the original brief.** "Expert Turret Officer: The expert turret officer is adept at
using his turret efficiently and getting its weapons out of harm's way when necessary. This has the following effects: Weapons on the turret
gain +1 to hit. When rolling for critical hits against turret-mounted weapons, subtract 1 from the die roll. When rolling for the special
turret jam critical, subtract 2 from the die roll. If no weapon on the turret fires in a given turn, the owning player can declare it to be
'sheltered.' If this is done, no incoming fire can hit its weapons unless they use called shots. Location: One turret-mounted weapon. The
turret operator can only aid that turret, not any other turrets on the same ship (if any). Cost: 5% of the ship's [base cost]. Availability:
Vree only." The user's notes: FV has no turret system, but it already marks turret weapons and the other weapons on the same turret - so he is
a system enhancement on a turret weapon, every weapon on that turret benefits, one officer per turret; and a turret with an officer whose
weapons did not fire is simply sheltered, with no new UI for the declaration.

**What FV calls a turret.** A turret-mounted weapon is one with the turret JAM (`setArcRestriction`, the "Turret Mount" tooltip line - the Vree
saucers' primary mounts, [[arch_vree_turret_jam]]); its turret is `Weapon::getTurretMates` - its linked firing group ("Turret 1"), or the gun
alone. Measured: 12 Vree hulls, 35 turret weapons - Xorr Turret 1 (2 cannons) + a lone cannon; Ximm a lone cannon + Turret 1 (2 defenders);
Xixx two mixed turrets; Xavan two; Xonn two mixed turrets + a lone cannon; Vymish one; Vaarl, Vaarka one gun; Vyreel, Xill, Xurr two lone
guns; the Tyllz starbase six lone Shredders. The Custom Ships' Shadow Xonn carries jammable mounts too, but is not Vree.

**Rulings (user, 2026-10-21, asked before building):**
1. **Only OFFENSIVE fire breaks the shelter**: a turret may intercept (or hold a weapon for Meteor Defence) and stay sheltered.
2. **No combining with an Expert Gunner** on the same turret: on the Gunner's gun his +1 replaces the Turret Officer's (the Gunner's own text,
   the Matter expert's Stage 8 precedent); the -1, the -2 and the shelter still apply to it.
3. **A turret weapon linked to no other is a turret of its own** (FV's "turret of one"): the Xorr may carry 2, the Xonn 3, the Tyllz 6.
4. **Movable**, as the other placed officers (free in Fleet Selection, Stage 10's rules in battle).

**Built with defaults, stated here (ask before changing):**
- "+1 to hit" = +1 fire control on every mode of every weapon on his turret, the Gunner's way (`isModified` - public, as Gunsights).
- "Cost: 5% of the ship's" (the text stops there) = 5% of the hull's base cost, per officer (Xorr 28, Tyllz 175).
- His post is the weapon he is bought on: he is disabled when THAT weapon is destroyed, even while a turret mate fights on, and his kill roll
  is that weapon's critical roll - his own -1 included, as the Technician's -2 is in his.
- The jam is its own d20, not a critical roll, so only the -2 applies to it (17+ becomes 19+), not the -1 as well.
- Shelter is decided once, at the start of the ship and fighter fire in `Firing::fireWeapons`, when every order of the turn exists. The rams
  that resolve before it (collisions, not fire) and Pre-Firing (which resolves before the turn's own orders are declared - meteors, the
  Pre-Firing weapons) are not sheltered against. Any fire order type but intercept / selfIntercept / meteorDefence is a shot
  (`Weapon::firedOffensivelyOnTurn`, the Tailgunner's filter) - a ballistic launch from Initial Orders included (no Vree turret weapon is one).
- A hit rolled onto a sheltered weapon takes the DESTROYED-system fallback of the pool that rolled it: another system of that name or tag
  (the tag pool's next repair priority), else the section's Structure. A called shot at one hits it (it is resolved before any pool).

**Design.**
- ⭐ **One per TURRET is a new registry key, `perTurret`** (`Officers::isPerTurret`): `keepPlacedRows` refuses a second of him on one turret
  (the sanitiser and the load), `mayMoveTo` refuses a move onto a turret another holds - on the same "in action that turn or the next" terms
  as a post - and `turretLimit` (`shipLimit`) caps a ship at its turret count. `Officers::turretKey` names a turret (`group:` + linked
  firing group, else `mount:` + id); ⚠️ MIRROR PAIR with `officers.turretKey`, which reads the static blueprint's `linkedFiringGroup`.
- `isTurretPost` (`hosts`) = `Weapon::isTurretMount()` (new, a method so it never reaches the blueprint); `getTurretMates` made public.
- Effects (`applyTurretOfficer`): `raiseEveryMode` - the Gunner's +1, now shared by `applyGunner` - on every mate a serving Gunner does not
  hold, and `critRollMod` -1 on every mate. Jam: `Officers::turretJamRelief` in `Weapon::testArcRestriction`, behind `$officersPresent`.
- ⭐ **Shelter**: `Officers::openShelter($gamedata)` / `closeShelter()` around the ship and fighter fire loops of `Firing::fireWeapons`,
  holding a per-request set; `Officers::isSheltered($ship, $system)` (one `empty()` in every other game) is read by
  `BaseShip::getSystemsByNameLoc` (not-destroyed branch) and `getSystemsByTag` (as `isDestroyed`) - every hit-chart roll of
  `getHitSystemByTable` / `ByDice`, Flash and Piercing charts included. Server only: nothing is sent.
- Client (`officers.js`): the registry row (`perTurret`); `placedEntries`, `placedBlock` ("This turret already has the Expert Turret Officer
  (on ...)") and `lobbyPostAs` mirror the one-per-turret rule; `fireControlRaise` now takes the ship and SUMS (Gunner 1 alone; else Turret
  Officer + Matter expert), so the lobby preview and `pendingMoveFireControl` cover him; `turretOfficerOn` + `turretMountLine` rewrite
  the owner's "Turret Mount" line to 19+ "(Expert Turret Officer)" through `adjustDataDisplay` (a jammed line is left alone).
  `systemEnhancements.LABELS` gains OFF_TURR.
- Docs: an Expert Turret Officer card (Ship Officers, after the Technician), and the About Officers bullets (placing, moving, what shows).

**Files.** Server: `Officers.php` (constants, `$factionRules`, registry entry + `perTurret` doc, `keepPlacedRows`, `isPerTurret`, `mayMoveTo`,
the Turret Officer block, `raiseEveryMode`, header), `Enhancements.php` (per-system entry, eligible / price wrappers, the sanitiser's notice),
`weapon.php` (`isTurretMount`, public `getTurretMates`, the jam relief), `ShipClasses.php` (the two pools), `firing.php` (open / close).
Client: `officers.js`, `systemEnhancements.js`. Docs `ammo-options.html`. Statics, legacy bundles (`scripts/bundle-legacy.js` - the watcher
was not running) rebuilt; no new class, no schema change.

**Measured.** Statics +853 B: 35 offers on 12 hulls (Xorr 3 at 28 cap 2, Tyllz 6 at 175 cap 6, Xonn 5 at 60 cap 3, Vymish 2 at 15 cap 1, ...).

**Verified.**
- `officersHarness.php` **1502 / 0 with the database** (89 new): the registry; every Vree hull's offers against its own turret mounts, price
  and turret count; none for the Shadow Xonn, a Raiders-flown Xorr, an opt-out, an Omega, a turretless Vree hull, a Vree OSAT or an exterior
  gun; posts (one to a turret by lowest id, two turrets two officers, a Gunner winning a shared gun, doctored rows); the sanitiser's kept
  rows, prices and notice; fire control (live, every mode, a mode switch) and critRollMod on the mates only, the lone cannon on its own; a
  Gunner's gun +1 not +2, the Turret Officer's back once that Gunner is killed; disabled with his own gun, not a mate's; the kill roll 19
  lives / 20 dies (a Gunner on his turret eased, on another turret not); the payload per viewer; the REAL `testArcRestriction` (17 jams
  without him, 18 not and 19 does with him, another turret's officer, the gate off, his kill last turn or his move changing nothing - his
  kill this turn still relieving); shelter (no shot, an interception /
  self-intercept / Meteor Defence, a shot this turn and last, two turrets, the gate, killed this turn / last turn, moving); the pools; the
  REAL `getHitSystemByTable` (baseline a cannon, Turret 1 sheltered -> the lone cannon, every turret -> the front Structure, a called shot
  -> its cannon); the REAL `Firing::fireWeapons` (a probe weapon sees the shelter open while its shot resolves, closed after); moves on the
  Xonn (to a mate, the lone cannon, a free turret; refused onto a held turret, allowed once its officer is dead; the payload's targets).
- `officersClientHarness.js` **577 / 0** (24 new): the registry; EVERY static blueprint (12 hulls, exactly the Turret Mount weapons, 5%, the
  turret count; no other faction); a lobby Xorr from its real static blueprint - the derived post, fire control on the mates only, the
  tooltip rows, the Turret Mount line (19+, the lone cannon unchanged, a jammed line untouched), one to a turret in `set()` / `placedBlock` /
  the menu row, the cap, a Gunner's gun +1 not +2, pre-battle destruction, `lobbyDestinations` / `lobbyMove`; in game nothing added, a
  pending move dropping the +1 and the 19+, an enemy's ship unchanged, the real `SystemInfo`.
- `enhancementsDifferential.js` **725 / 0** (16 new: the Xorr and the Xonn added for placed cases only - a Gunner, Gunner + Gunsights,
  a Gunner on every gun; the Turret Officer alone, on every turret weapon, with a Gunner or Gunsights on his mate, with a Gunner on his gun);
  `enhancementsHarness` **285 / 0** (fingerprint unchanged: ship-level offers only).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game has a Vree turret officer).
- **Mutation-tested: 46 breaks (28 server, 14 client, 4 through the differential), all caught**, files byte-identical afterwards. The
  first pass missed one - the jam relief not asking whether he serves - so the jam checks gained his kill last turn / this turn and a move.

**Follow-up (user, the same day): the Antimatter Shredder's Shredder mode keeps its 0/0/0.** "Fire control for the Antimatter Shredder
should remain 0/0/0 (e.g. no bonuses from Gunner or Turret Officers, or any others actually) since it uses its own special hit
calculation." Built for its Shredder mode (mode 1) only - the one with fire control 0/0/0 and its own per-target attacks (they resolve through
the ordinary calculateHitBase, so a raised mode 1 would have raised every Shredder attack); its Raking and Piercing modes are plain Antimatter
Cannon modes and keep every bonus. ⭐ One rule, `Weapon::$fixedFireControlModes` (PROTECTED, `array(1)` on AntimatterShredder) asked
through `hasFixedFireControl($mode)` by EVERY fire control raiser: `Officers::raiseEveryMode` (Gunner, Turret Officer), `raiseMatterModes`,
`Enhancements::sysEnhApplyGSGT` (Gunsights - a stored refit row is applied unchecked, so `sysEnhFixedFireControl` guards a non-weapon).
`ShipCompactor::annotateSystems` writes `fixedFireControlModes` into the static blueprint (13 Shredders), which the lobby's raisers read:
`systemEnhancements.hasFixedFireControl` (its Gunsights preview) and `officers.hasFixedFireControl` (`fireControlRaise` - so the lobby preview
and `pendingMoveFireControl` give mode 1 nothing). Docs: the Gunsights, Gunner and Turret Officer cards. Gates after it: officersHarness
**1512 / 0** (10 new - every raiser alone and together, a mode switch, Gunsights' bare applier, the Xonn's mixed turret), client **582 / 0**
(5 new - the statics, the lobby previews, lobby Gunsights, a pending move), differential **725 / 0** (its Xonn cases put the officer on a
Shredder), enhancements 285 / 0; **10 more breaks, all caught** (the Gunsights live-value guard needed the bare-applier check: on a real load
changeFiringMode re-reads the array over it).

**Second follow-up (user, the same day): no officer's called-shot bonus on a Shredder-mode attack either.** Asked: an Expert Gunner's
once-per-battle +15% called shot and an Expert Targeter's +20% still reached a Shredder-mode attack at a fighter (and the Gunner's was spent by
the first) - `prepareShredderOrders` declares one order per craft with `calledid` = that craft, so calculateHitBase saw standard called shots.
Ruled: "yes, the shredder should not benefit from these either". ⭐ One rule, `Officers::bonusCall($weapon, $called, $mode = null)` - a call
fired in a fixed-fire-control mode (`hasFixedFireControl`, the same "own hit calculation" marker) is no call to an officer - asked by
`targeterShotBonus`, `gunnerShotBonus` (so nothing is spent) and `firingSpentAbility` (so the faster reload is not held back by a Shredder
firing). ⚠️ MIRROR PAIR `officers.bonusCall` in `gunnerShotBonus` / `targeterShotBonus` (the client never previews a Shredder-mode call - it
targets a hex - but the pair stays honest). The Raking and Piercing modes keep both bonuses. No Raiders or Llort hull carries a Shredder, so the
Targeter half is a rule with no current case. Docs: the Gunner card's Notes. Gates: officersHarness **1518 / 0** (6 new - the REAL
calculateHitBase at an Aurora flight for a Gunner and a Targeter on a Shredder, their Raking called shots, targeterShotBonus per mode, the
faster reload after Shredder-mode attacks and after a called Raking shot), client **583 / 0** (1 new), differential 725 / 0, `-Check` replay
162 / 0; legacy bundles rebuilt. **7 breaks, all caught** (the first pass missed the reload one - its fixture's flight was not in the game, so
no call resolved; fixed).

**Observations, not changed:**
1. Not play-tested. Worth a test: a Xorr with a Turret Officer on Turret 1 that holds its fire for a turn while an enemy fires at it (hits
   that roll Turret 1 go to the lone cannon or Structure; a called shot at a Turret 1 cannon still lands), then fires (no shelter); the
   Turret 1 cannons' fire control +5% to both players; the owner's Turret Mount line at 19+.
2. A Vree turret's Antiproton Defenders keep intercepting while sheltered (ruling 1) - worth a look in play whether that feels right.

### 9.30 Stage 23: Expert Anticipator (2026-10-21)

**Rules text (user, 2026-10-21) - one of the original 25 (Hard tier, §3.3).** "Expert Anticipator: The Torata appreciate flexibility in all
things, not just their weapons. When they find an individual who can consider many different options at once, and come up with the
appropriate solution quickly, they put him on the bridge of a ship and let him go to work. While some of these abilities may be psi-related,
many are simply instincts that happen to turn out correctly more often than not. This provides the following advantages: When rolling for
initiative, this officer's ship rolls the die twice. The player then selects the higher one. This represents the officer's ability to 'look
ahead' at the battlefield and correctly predict enemy actions. After moving his ship, the player can use his Expert Anticipator to 'predict'
any remaining maneuvers still to come in the turn. To do this, he must ensure that his ship's Turn Delay is complete and enough thrust is left
unused to complete a turn maneuver. Then, at the end of during Firing, he can execute a single turn, if desired. This ability can only be
used for a turn, not a roll, pivot, snap turn, or other maneuver. By correctly interpreting the exact location of the target, this officer
can improve the damage of his ship's offensive weapons. When rolling for damage, roll an extra die and drop the lowest die from the
calculation. For example, a pentagon array would roll six dice, and if these came up 10, 8, 6, 5, 4, and 1, the 1 would be dropped. This
ability works only on weapons that roll at least two dice for damage, so it would not affect a light particle beam. Similarly, it would not
affect a fixed-damage weapon like a bolter or pulsar. Cost: 10% of the ship's base value for Torata. Location: CnC. Availability: Races other
than the Torata pay 50% more for this officer." The user's note: the turn "is similar to a fighter's combat pivot except that there are no
associated firing penalties and thrust must still be spent on ship thrusters as normal."

**What FV had.** Initiative is a d100 at two sites (`Manager::generateIniative`, `SimultaneousMovementRule::generateIniative`). Damage dice all
go through `Dice::d`, set up per shot by `Weapon::getFinalDamage` (Elite Crew's and the Gunner's `$perDieBonus`). Measured over every
ship weapon class (1,861 hulls, 391 classes): 194 modes roll their damage as ONE call of two or more dice, 104 one die, 172 none (fixed). Seven
modes make several calls: the **Pentagon Array** (five 1d10 rakes, one call each - the rules' own example), the custom **Light Phase Disruptor**'s
concentrated shot (two 3d6 rakes), and the three **Unreliable** weapons, whose first d10 is a MISFIRE roll, not damage. In the Firing phase a
ship's movement rows were all thrown away (`Movement::validateMovement`); only flights kept combat pivots and the Evader's Evade, and the thrust
panel existed only in `MovementPhaseStrategy`.

**Rulings (user, 2026-10-21, asked before building):**
1. **The turn takes effect like a combat pivot** (the text's "at the end of during Firing" being garbled): given in the Firing phase before the
   ship declares direct fire, with no to-hit penalty, and this turn's fire - his own and the fire at him - meets the new facing.
2. **On an Expert Gunner's weapon both apply**: the extra die, and the Gunner's +1 on every die (not the Ion Technician's "the Gunner's
   replaces it").

**Built with defaults, stated here (ask before changing):**
- 15% for everyone, 10% for the `Torata Regency` ("50% more" for other races); every faction may buy him (D9's exclusions aside).
- The initiative die is thrown twice in both initiative rules (ordinary and simultaneous-movement categories); the second only for his ship,
  so every other ship's roll is unchanged. Nothing about it is shown: the initiative itself is public as always.
- "Roll an extra die" = one more die of the same size, raised by any per-die bonus like the others, the lowest of all dropped. One call of 2+ dice
  is one damage roll. The Pentagon Array's five rakes are one roll (six dice, the lowest RAKE dropped); the Light Phase Disruptor's two 3d6 rakes
  one 6d6 roll (now rolled die by die, then split three and three - the same dice in the same order without him); an Unreliable weapon's
  misfire d10 never counts. At fighters the Pentagon's pulses are each their own 1d10, so they gain nothing. Never a ram (not one of the ship's
  weapons - the Religious Leader's exclusion). Same scope as Elite Crew's per-die bonus: weapons that roll damage outside `getFinalDamage`
  (a handful of AoE and custom paths) are untouched.
- The turn is a REAL turn - heading and facing - at the ordinary turn cost (the Navigator's ladder applies), assigned to the thrusters on the
  Movement phase's own panel; "Turn Delay complete" is `calculateCurrentTurndelay` = 0 with no agile snap-turn exception; once a turn; a new turn
  delay starts from it (next turn the ship moves that far before it turns again); every other rule of `canTurn` applies (not while pivoting,
  rolling, out of alignment, adrift, with the engine gone, on a jump-out). **Not while a unit is attached to his ship** (a boarding pod, a
  grappling claw, a docked unit): attached units follow their host only in the Movement phase (`MovementGamePhase::process`), so they would be
  left facing the old way.
- Enemies see it only once firing resolves (the combat pivots' masking); it then replays as an ordinary turn.

**Design.**
- `Officers`: registry `OFF_ANTI` (`post` cnc, `apply` null - right after the ELINT Officer, the last fixed-post ship officer), `$factionRules`
  Torata 10; `initiativeRoll($ship)` (both roll sites), `extraDamageDie($ship, $weapon)`, `mayAnticipate($ship)` (behind `$officersPresent`).
- ⭐ **`Dice::$extraDie` + `Dice::keepBest($rolls, $max)`** (lib/dice.php): while the flag is up, a `d()` of two or more dice keeps its best
  `$times` of `$times + 1`; a weapon rolling its damage dice one call at a time hands them to `keepBest` itself (the Pentagon Array, the Light Phase
  Disruptor). `Weapon::getFinalDamage` sets and restores the flag around `getDamage()`, beside `$perDieBonus`. ⚠️ MIRROR PAIR with BOTH harness
  stubs (replayHarness.php, officersHarness.php) - officersHarness runs one rule table over its stub and over the real file.
- ⭐ **`Movement::ANTICIPATED_TURN` / `isAnticipatedTurn`**: an ordinary `turnleft` / `turnright` row whose `value` is `anticipate` (the column is
  varchar(100), already holding `combatpivot`). `validateMovement`'s Firing-phase filter keeps ONE for a ship (`mayAnticipate` on the
  authoritative load, none already this turn - `hasAnticipated`) and still drops every other ship row; flights unchanged.
  `TacGamedata::hideEnemyCombatPivots` strips it for enemies while the Firing phase is open. Fire resolution reads the ship's last row, so its
  facing is the one his arcs and the incoming shots use. ⚠️ MIRROR PAIR with `shipManager.movement.ANTICIPATED_TURN` / `isAnticipatedTurn`.
- Client (`movement.js`): `canTurn` asks `canAnticipate` in the Firing phase (phase 3 only: the combat pivot's gate `weaponManager.canCombatTurn`,
  `officers.canAnticipate`, once a turn) and refuses a turn delay outright there; `doNormalTurn` marks the row in phase 3; `isFiringPhaseMove`
  includes it (so Cancel and Saved Orders handle it). `FirePhaseStrategy` draws the movement UI for a ship that may turn or has, and borrows
  `MovementPhaseStrategy`'s `onAssignThrust` / `repositionThrustUi` for the thrust panel. `gamedata.doCommit` (Firing phase) refuses a commit while
  his turn's thrust is unassigned. `officers.js`: the registry row and `canAnticipate` (⚠️ MIRROR PAIR with `Officers::mayAnticipate`).
- Docs: an Expert Anticipator card (Ship Officers - first, alphabetically).

**Files.** Server: `lib/dice.php`, `weapon.php` (getFinalDamage), `particle.php` (PentagonArray), `customs.php` (customLtPhaseDisruptorShip),
`Officers.php`, `Manager.php` and `SimultaneousMovementRule.php` (the roll), `handlers/movement.php`, `TacGamedata.php` (masking). Client:
`officers.js`, `movement.js`, `renderer/phaseStrategy/FirePhaseStrategy.js`, `gamedata.js`. Docs `ammo-options.html`. Harnesses: the `Dice` stubs
of `replayHarness.php` and `officersHarness.php`. Statics and legacy bundles (`scripts/bundle-legacy.js` - the watcher was not running) rebuilt;
no new class, no schema change.

**Measured.** Statics +~89 KB: 1,815 offers (16 on Torata hulls - the Dartoc 65, the Golthar 60). Fingerprint: exactly 16 new offer lines (the
fingerprinted hulls), nothing else moved (proved by stripping them and byte-comparing to the old baseline, kept as
`fingerprint.before-officers-stage23.txt`).

**Verified.**
- `officersHarness.php` **1578 / 0 with the database** (57 new): the registry place, offers and prices (Torata 10%, four other factions 15%, none
  for a flight or an OSAT), the C&C post, no stat at load; `initiativeRoll` (two dice, the higher kept either way; one die without him, with the
  gate off, or with his C&C destroyed last turn) and the REAL `Manager::generateIniative` and `SimultaneousMovementRule::generateIniative`; the
  Dice rule as ONE table over the REAL `lib/dice.php` (evaluated in a namespace whose `random_int` is forced) AND the harness stub; the REAL
  `getFinalDamage` - a Heavy Laser five dice with the 1 dropped, a Standard Particle Beam one die, both statics back to 0 after the shot, no ram,
  the gate, a pre-battle-destroyed C&C, a Gunner's laser with both bonuses (and a Gunner alone), the Pentagon Array's six dice and rakes (and one
  die a pulse at fighters), the Light Phase Disruptor's 6d6 split, the Unreliable Matter Cannon's misfire d10; `validateMovement` (one marked turn
  kept of several rows, none for a ship without him, with the gate off, a turn already stored, a unit attached or his C&C destroyed; a turn stored
  LAST turn not counting; a flight's combat pivot kept and a marked turn not), the masking per viewer, the facing after the row.
- `officersClientHarness.js` **608 / 0** (25 new): the registry row; `officers.canAnticipate`; the REAL movement.js - canTurn both ways, the row
  doTurn writes (marked, heading and facing, thrust required, uncommitted, the panel opened), one a turn, Cancel, a sent row, last turn's, turn
  delay (an agile ship's snap turn refused in phase 3 though allowed in phase 2), an ordinary unmarked turn in the Movement phase, thrust short,
  after direct fire, no officer / lost / enemy / attached; the REAL `UI/shipMovement.js` + `ShipMovementCallbacks.js` (both arrows drawn, the
  right arrow's click makes the row, redrawn without arrows and with Cancel once paid, no arrows for a ship without him); the REAL
  `gamedata.doCommit` in a sandbox (an unpaid turn refuses the commit naming that ship only; a paid turn or another player's does not); the REAL
  `FirePhaseStrategy` (the movement UI for his ship only, the thrust panel through the Movement phase's handler).
- `enhancementsDifferential.js` **725 / 0** (no case added: he moves no lobby stat); `enhancementsHarness` **286 / 0** (fingerprint re-recorded,
  additions only).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game has an Anticipator; the replay stub's new
  `$extraDie`/`keepBest` read 0 throughout).
- **Mutation-tested: 45 breaks (29 server, 16 client), 43 caught**, files byte-identical afterwards. Runner `mutate23.js` in the session
  scratchpad only. The two misses are equivalent: `d()`'s `$times >= 2` (a one-die array passes through `keepBest`'s own guard unchanged) and
  `mayAnticipate`'s flight test (its one caller excludes flights, and no flight can hold him). The first pass also missed `getFinalDamage`
  not restoring the flag - the "statics back to 0" check ran after the plain ship's shot, which resets it; it now runs after his.

**Observations, not changed:**
1. Not play-tested. Worth a test: a Torata Dartoc with him - its initiative across a few turns; a Pentagon Array volley at a ship (six dice in
   the log's damage, five rakes); in the Firing phase, after moving with the turn delay done and thrust left, the turn arrows, the thrust panel,
   then the ship firing on its new facing and the enemy's hits landing on its new sides; the enemy seeing the turn only after firing resolves.
2. Weapons that roll damage outside `getFinalDamage` (customBSG, customEscalation, customNexus, plasma.php's two AoE paths, specialWeapons'
   waveform check) get neither Elite Crew's per-die bonus nor his extra die - the existing scope, kept.

### 9.31 Stage 24: Breaching Expert (2026-10-21)

**Rules text (user, 2026-10-21) - one of the original 25 (Hard tier, §3.3).** "Breaching Expert (single pod): This highly trained breaching
specialist accompanies a breaching pod on its mission, greatly enhancing its capabilities. The following benefits are provided to the pod:
The pod receives +1 thrust and ignores any loss of thrust due to damage. The pod's Marines receive benefits as if they were Elite. Any chart
that lists a benefit for an Elite unit would also apply it to this pod. If this officer is used by a race that already provides Elite
advantages on these charts by default (e.g. narn or Gaim), He does not provide any further benefits. If attempting to capture a ship, the
pod's contingent gets three attack rolls on the first turn of combat. It is still destroyed by a single hit from the enemy, however. This
benefit can be received only once per scenario. Location: Breaching pod. Cost: 25% of the cost of the breaching pod. Availability: Any race
may purchase one, but races other than Balosian, Yolu, Narn or Gaim double the cost. No more than 20% of a fleets breaching pods (round
fractions of 0.5 or more up) can use these experts."

**What FV had.**
- **42 breaching pods**: 40 flights in the `'Breaching Pods'` hangar class (the two Chariot Breaching Sleds among them), and the Minbari
  Protectorate's Ronati and the Nausicaan pods, which ride shuttle hangars. Every one is `maxFlightSize` 2 with a pointCost for six, so a
  pod costs pointCost / 6. Four superheavies also carry Marines (the Dalithorn Assault Pinnace and Customs Vessel, the two Nausicaan
  Gliders) - not pods.
- **Elite** in FV is the Marines weapon's `eliteMarines`: -1 on the delivery roll (`getDeliveryRollMod`) and the Elite mission classes
  (`CaptureShipElite`, `SabotageElite`, `RescueMissionElite`), whose rolls are -1 (`getMarineRollMod`). The Narn T'Rakh and Gaim Ech'Akki pods
  are built Elite; most others may buy Elite Marines (`ELT_MAR`). No other chart lists an Elite benefit for a pod.
- **Capture**: a delivered party is a `CaptureShip` critical on the target's C&C from the next turn; each turn `ShipSystem::doCaptureMission`
  rolls one d10 per party (5 or less kills a defender) and one per defender (5 or less eliminates a party).
- **Thrust**: FV takes no thrust from a flight for damage. The only drain is the Walkers' Energy Draining Field
  (`FighterFlight::getEffectiveFreeThrust`) - a field's effect, not damage.
- No officer had a fleet-wide cap. Fleet-composition rules (hangar and pod capacity) are lines in the lobby's Fleet Correctness Report
  (`gamelobby.js` `checkChoices`), reported, not enforced.

**Rulings (user, 2026-10-21, asked before building - all as recommended):**
1. In a pair of pods the Elite benefit and the triple capture roll are **his own pod's only** ("single pod"); the +1 thrust is the flight's,
   as a flight moves as one.
2. His price is 25% (50%) of **one pod, flat** - not §9.9's scaled rule, which would charge a pair a third of it.
3. The 20% is **a Fleet Correctness Report line**, as every fleet-composition rule is - not refused in the buy dialog, not enforced by the server.
4. On a pod already Elite, "he does not provide any further benefits" covers **the Elite clause only**: the +1 thrust and the triple roll still apply.

**Built with defaults, stated here (ask before changing):**
- A breaching pod = a flight whose craft carries Marines and that is in the `'Breaching Pods'` hangar class or has "Breaching Pod" in its class
  name: the 42 above. (Before counting, the user was told "41"; 42 is the measured number.)
- He stays in his pod, under every fighter officer's rules: lost when it is destroyed or drops out, no kill roll, following it onto a split-off
  row with his cost, the flight's one officer (D9).
- The triple roll is **spent when his pod's first capture party is delivered** (its critical made), whatever becomes of him afterwards; a
  delivery that fails spends nothing, nor does a Sabotage or a Rescue party. The party rolls three d10s, each at its own modifiers, on the turn
  its critical starts (its first turn aboard), and is still one attacker to the defenders.
- "Ignores any loss of thrust due to damage": nothing to build. The Energy Draining Field's drain still applies.
- The fleet's pods are counted craft by craft (a flight's size); its experts are the Breaching Experts that would serve (one officer per
  flight). 20% rounded half up = floor((pods x 20 + 50) / 100): 3 pods for the first, 8 for two, 13 for three - so one flight of two pods
  never qualifies alone. The line shows only while the fleet carries one.
- His Elite is asked at delivery, never written into `eliteMarines`: that weapon's "Elite" tooltip line is sent to every viewer. His team's
  client shows "Yes (Breaching Expert)". No combat-log line names him (D4).

**Design.**
- `Officers`: registry `OFF_BRCH` (post `craft`, `flight`, pct 50, new key **`unscaled`**, `apply` applyBreachingExpert - +1 `freethrust` -
  `publish` freethrust, `needs` isBreachingPod), last; `$factionRules` 25 for the four races. `flightPrice` returns an `unscaled` officer's
  listed price (rowPrice, legacyPrice and shareWithCraft follow it). `isBreachingPod`; `eliteMarines($flight, $weapon, $turn)` (the serving
  officer in the craft that carries the weapon - `breachingExpertIndex`); `spendCaptureAssault` (one OffUsed note, ability `capture`, through
  Stage 10's `spend`); `captureRolls($critical, $gamedata)`; `BREACH_PARTY_KEY` ('expert'), the key in his party's critical's `param`.
- `Marines` (specialWeapons.php): `isElite($shooter, $gamedata)`, asked by `getDeliveryRollMod` and once per delivery for the four
  mission-class choices; the capture branch marks his party. ⚠️ The Grappling Claw repeats the same Elite lines - untouched.
- `ShipSystem::doCaptureMission`: each party attacks `captureRolls` times. `ShipSystem::stripForJson`: a critical's `param` is sent without
  `BREACH_PARTY_KEY`, to every viewer.
- Client: `officers.js` - the registry row (`unscaled`, `needs: 'breachingPod'`, `stats` freethrust 1), `isBreachingPod` (⚠️ MIRROR PAIR),
  `flightCost` / `priceNote` for an `unscaled` officer, `marinesEliteLine` through `adjustDataDisplay`, `ABILITY_NAMES.capture`,
  `breachingFleetCheck`. `gamelobby.js` `checkChoices`: "Breaching Experts: n (allowed up to m: 20% of p breaching pods)". React:
  ShipNotesPanel's and ShipInfo's flight Thrust through `displayStat` (the lobby's preview; in game the server's published figure).
- Docs: a Breaching Expert card (Fighter Officers - first, alphabetically), and the section's cost paragraph names him as the exception.

**Files.** Server: `Officers.php`, `weapons/specialWeapons.php` (Marines), `systems/ShipSystem.php`. Client: `officers.js`, `gamelobby.js`,
`UI/reactJs/shipWindow/ShipNotesPanel.js`, `UI/reactJs/system/ShipInfo.js`. Docs `ammo-options.html`. Harnesses: `officersHarness.php`,
`officersClientHarness.js`, `enhancementsHarness.php` (a pod hull and four cases in the diffdump), `enhancementsDifferential.js` (flight
`freethrust` compared). Statics and bundles rebuilt (`fvbuild -Statics`, `-Client`); no new class, no schema change - `tac_critical.param` is
varchar(200), and a party's JSON grows by 11 characters (`,"expert":1`).

**Measured.** Statics +1,974 B: 42 offers. The enhancements fingerprint is unchanged (it covers ships only).

**Verified.**
- `officersHarness.php` **1556 / 0 with `--no-db`** (52 new): the 42 pods offered him and the four Marine superheavies, the Streib Combat Pod, a
  fighter flight and a ship not; eleven prices; `flightPrice` / `rowPrice` / `legacyPrice` unscaled and the pair split; the first pod, one
  officer per flight, no kill roll; +1 thrust, `getEffectiveFreeThrust`, published to owner and enemy with the list to the owner only, the
  REAL `calculateHitBase` speed match (a target 7 faster: his pair 30%, the twin 0), lost with his pod (destroyed or dropped out), kept when
  the other pod falls; the REAL `getDeliveryRollMod` and `onDamagedSystem` - his pod's Marines Elite, the other pod's and a twin's not, never
  written into the weapon, nothing more for Narn pods or bought Elite Marines; the capture assault - a failed delivery spends nothing, his
  first party marked and one OffUsed note ("Breaching Expert: capture assault used"), no second (in the same load, or after a reload's note),
  the other pod's party unmarked, the payload's `abilities`; `captureRolls`; the REAL `doCaptureMission` - three attacks on the first turn,
  one on the second, one for an ordinary party, eliminated by a single defender's hit; the mark stripped from both viewers' payload.
- `officersClientHarness.js` **629 / 0** (21 new; two registry-order checks moved one place): the row; every static flight - the client's
  pod test and the server's offers agree (42); prices and the buy dialog's note; the lobby list and thrust through the REAL ShipNotesPanel
  and ShipInfo; the in-game thrust; the "Elite" line (his pod, the other, lost, an enemy, a Narn pair) through the REAL SystemInfo; the used
  ability's line; `breachingFleetCheck`; the REAL `checkChoices` (in a sandbox) - OK, FAILURE, no line.
- `enhancementsDifferential.js` **742 / 0** (17 new cases on an Abbai pod; `freethrust` now compared on every flight case);
  `enhancementsHarness` **287 / 0**.
- **Mutation-tested: 42 breaks (22 server, 2 differential, 18 client), 42 caught**, files byte-identical afterwards; every catch by a Stage 24
  check. Runner `mutate24.js` in the session scratchpad only. One more mutant was equivalent: the half-up offset +50 -> +40 (20% of whole
  pods is a multiple of 0.2, so any offset from 40 to 59 rounds alike); +30 and +60 are caught.
- ~~NOT yet run: `officersHarness.php` with the database, and `fvbuild -Check` (the replay corpus). The FieryVoid database container was
  stopped and the DouglasChanges stack held its ports.~~ Both run in Stage 25 (§9.32), with this stage's checks inside them: officersHarness
  with the database and `-Check`'s replay 162 / 0 both green.

**Observations, not changed:**
1. Not play-tested. Worth a test: two pairs of pods with one Expert - the Fleet Correctness Report's line; his pair's thrust in the lobby and
   in game; a capture delivery from his pod and its first turn's three attacks in the log's "Boarding Combat Summary"; his team's Marines
   tooltip "Yes (Breaching Expert)", the enemy's "No".
2. `breachingPodBrixadiiB.php` declares `phpclass` "breachingPodBrixadii", its predecessor's - a contributor typo; its static blueprint is
   keyed under the old name in its own faction file.
3. The orphan static `ZTrek Playtest Other Factions.json` (2026-09-26, a faction name the generator no longer writes) still holds a Nausicaan
   pod without the offer; the client harness's sweep reads only the files of the last statics run.

### 9.32 Stage 25: Expert Political Officer (2026-10-21)

**Rules text (user, 2026-10-21) - an officer added beyond the original 25.** "Expert Political Officer. Most Centauri ships are sponsored by
Imperial Houses, who like to keep an eye on their charges. One common means for this is the political officer, who is actually little more than
a glorified spy for the nobility ... Though they have no shipboard duties, expert political officers have Influence with a capital 'I.' This
enables them to acquire better equipment and service for their ship. This has the following benefits: The ship possesses one armed shuttle. If
this shuttle can use missiles, it comes loaded with one set of standard missiles at no cost. The political officer's reputation attracts other
important or skilled figures to the ship's crew. Select either an expert navigator, expert helmsman, expert engineer, expert scanner and add him
to the crew at no cost. These are not lost if the political officer is killed, but remain on the ship. Due to improved armor ... ship systems
(selected by the owner) receive +1 to their armor values. The owner must select 5 different systems (i.e., these cannot be combined) and cannot
select structure blocks. This armor is not lost during the scenario if the officer is killed. Once during each scenario, the political officer
can give a stirring speech to his ship's crew ... This provides the ship with +30 to its initiative on that turn. The speech must be announced
before initiative rolls are made on that turn (by any player). Location: C&C. Cost: 40% of the ship's basic value. Availability: Capital ships
or larger units only. Centauri Only." The user's notes: the armed shuttle - "Elite Crew enhancements already provides a method for this"; the
armour - "5 free Hardened Armour System Enhancements (on different systems) by player (or randomly allocated to eligible systems in case player
forgets)"; the free expert - "zero the cost of an expert navigator, expert helmsman, expert engineer, expert scanner as picked by player (most
expensive one is zero'd if several are picked, and one is randomly picked if none are chosen)"; the speech - "a new 'Activate' React menu for CnC
(using existing doActivate/doDeactivate methods in client) available in the Firing Phase that provides an Initiative bonus the following Turn
... a fireOrder so that it appears in Combat Log as 'The Political Officer on-board [Ship Name] begins a stirring speech about [insert random
noun here]'. The React buttons should just read 'Speech!' and 'Cancel speech'."

**What FV had.**
- **60 hulls** qualify: the capital ships and larger (size class 3) of the Centauri Republic (23) and the Centauri Republic (WotCR) (37), 5 of
  them bases. All but the Lias have a default shuttle; every one has 18 or more non-Structure systems that may take Hardened Armour.
- **Elite Crew's armed shuttle** is not a purchase: as the hangars fill (HangarOps::populateInitialHangarUsage, from onIndividualNotesLoaded -
  before onConstructed, so read off the rows), one default shuttle per Elite level is retyped into the faction's armed shuttle
  (`crewArmedShuttleUpgrades`; the Centauri one is ArmedShuttleCent); Poor Crew blocks armed shuttles (`crewBlocksArmedShuttles`). The lobby
  never sees it.
- **Hardened Armour** (`SYS_HARM`) is a per-system refit: +1 armour, any system with armour (Structure included), one to a system, priced
  ceil(maxhealth x max(2, armour) / 2). The server re-prices every refit row (`sanitiseSystemEnhancements` - at buy, saved-fleet save and
  reload); the lobby prices them in `systemEnhancements.js`, every write through `totalCost`.
- **Ship-level officer prices** are trusted from the client at buy; a saved fleet stores each officer row's price and re-prices it on reload
  (`Officers::rowPrice`) - one row at a time, each re-priced BEFORE its own count was written back.
- **A Firing-phase declaration** on a non-weapon system: the Wide-Beam Lightning Array's toggle - `individualNotesTransfer` posted at the
  commit, saved through `ShipSystem::saveFirePhaseDeclaration` in `FireGamePhase::process`. The generic `SystemActivation` box takes a
  system's own labels (`getActivateLabel` / `getDeactivateLabel`) and reads `active`.
- **A combat log line** with no shot: a log-only fire order on the ship's own Ramming Attack ([[howto_create_fire_orders]]); every entry was
  headed "FIRE: <shooter>" but the hyperspace kinds ("HYPERSPACE:").
- Next turn's initiative is rolled at the turn change on a fresh load AT the new turn (`Manager::changeTurn` -> `generateIniative`), so
  `Officers::apply` on that load can add a bonus earned the turn before.

**Rulings (user, 2026-10-21, asked before building):**
1. **Both Centauri factions** - the Republic and the Wars of the Centauri Republic.
2. **The server makes the random picks, at battle start** - not the lobby at purchase.
3. **His armed shuttle adds to Elite Crew's** (Elite Crew + him = two).
4. **No +30 unless he still serves** on the turn it lands - killed or disabled in the turn he speaks, the speech is wasted.

**Built with defaults, stated here (ask before changing):**
- "Capital ships or larger" = size class 3 (bases and enormous units included). 40% rounded up, as every ship officer. One per ship.
- The free expert is the dearest of the four bought, by his price on that hull (the Scanner's 7% rounds up, the Navigator's down, so the Scanner
  wins between the two unless 7% is whole); a tie goes to the first in the registry (Helmsman, Scanner, Engineer, Navigator). He is an
  ordinary officer in every other way - posted, disabled and killed by his own post.
- The five free Hardened Armour are the dearest five non-Structure rows bought (a tie to the lower system id); a sixth or more, and any on
  Structure, are paid as usual. None is free while his C&C is destroyed by pre-battle damage (he is refunded then - Stage 15). Each is an
  ordinary refit: its ✦, its +1, its tooltip.
- **The random picks are made as the fleet is COMMITTED** (`BuyingGamePhase`, right after the unit's refits are sanitised) and stored as
  ordinary rows - rolled once with `Dice`, not derived from a hash at each load: FV is open source and the repo's `$secret_phrase` is committed,
  so a hash an opponent can recompute would tell him which systems got the armour and which expert came aboard. From the player's side this is
  "at battle start": the lobby shows what is still unchosen, and nothing is random before the commit. A random expert is one of the four the hull
  is offered; random armour goes only where a Hardened Armour could be bought - never Structure, a system destroyed before battle, or one
  already armoured.
- The speech: "Speech!" / "Cancel speech" in the C&C's Activate box (the generic SystemActivation - in the Hyach Computer's purple, user request
  the same day: `CnC.prototype.activationMenuPurple`, SystemActivation's existing opt-in), in the Firing phase only,
  on the C&C he is posted in (a hull with two C&Cs shows it on his alone), while he serves and has not spoken. It may be given at any moment of
  the phase and withdrawn until the commit. The server spends it at the commit on the loaded ship. The C&C's icon does not glow as a power
  boost while one is ordered (`suppressActiveBoost`). Save Orders keeps an ordered speech.
- The log line is written as firing resolves, after every shot, and reads "SPEECH: The Political Officer on-board <ship> begins a stirring
  speech about <topic>." - the topic one of twenty at random (`$speechTopics`: the rules' "past glories of the Republic", "honor of serving the
  Emperor", and other mumbo-jumbo). Every player reads it, so the speech reveals him - as the user asked.

**Design.**
- `Officers`: registry `OFF_POL` (post `cnc`, pct 40, `apply` applyPoliticalOfficer, `publish` iniativebonus, `needs` isCapitalOrLarger) right after
  the Anticipator, before the placed officers; `$factionRules` `only` both Centauri factions; constants `SPEECH_INITIATIVE` 30,
  `POLITICAL_ARMOUR_SYSTEMS` 5, `SPEECH_DAMAGE_CLASS` 'PoliticalSpeech'; `$politicalExperts`, `$speechTopics`; `$abilityNames['speech']`.
  `politicalAboard` (the ROWS say he is bought and the hull is offered him - for everything that runs without the officer list);
  `politicalArmedShuttles`; ⭐ `freeExpertId` (⚠️ MIRROR PAIR with `officers.freeExpertId`), read by `rowPrice`; ⭐ `freeArmourRows` (⚠️ MIRROR
  PAIR with `officers.freeArmourRows`) and `pricePoliticalArmour`; `placePoliticalPicks` (+ `buyShipLevelRow`); `applyPoliticalOfficer`;
  `speakerIndex` (⚠️ MIRROR PAIR with `officers.canSpeak`), `saveSpeech` (Stage 10's `spend`: one OffUsed note `OFF_POL:speech`,
  "Expert Political Officer: speech used"), `speechLog`.
- `Enhancements::sanitiseSystemEnhancements` ends with `Officers::pricePoliticalArmour`; `sysEnhDestroyedSystemIds` made public.
  `HangarOps::crewArmedShuttleUpgrades` adds `politicalArmedShuttles` unless Poor Crew blocks it. `CnC::saveFirePhaseDeclaration` hands a
  posted `['speech']` to `saveSpeech`. `FireGamePhase::advance` calls `speechLog` before the new fire orders are written.
  `BuyingGamePhase` calls `placePoliticalPicks` after each unit's refits are sanitised. ⚠️ `Manager::loadSavedFleet` now writes every saved
  officer count onto the unit BEFORE re-pricing any officer row: one row's price can depend on the others now (the Navigator's was re-priced
  while the Political Officer's count, later in enhid order, still read 0).
- Client: `officers.js` - the registry row (`needs: 'capital'`), `POLITICAL_EXPERTS`, `freeExpertId` / `shipRows` / `politicalDiscount` /
  `shipRowCost`, `freeArmourRows` / `armourCandidates` / `pricePoliticalArmour` / `armourNextCost` / `armourFreeOpen` / `decorateArmourRow`
  (the system menu's Hardened Armour row: the price charged, what one more would add, a note), `politicalStatus` (his lobby summary: the free
  expert, "n of 5 placed"), `canSpeak`; `setShipLevelRow` takes the free expert into account and re-prices the armour when he comes or goes,
  `dropDestroyedPosts` reports what the points actually moved by. `systemEnhancements.totalCost` prices the armour first. `confirm.js`
  `isFreeExpert`, asked by `enhRowCost` - the buy dialog's totals and its "free" row. `gamelobby.js`: the ship read-backs of doBuyShip /
  doCopyShip / doEditShip through `enhRowCost`, `enhancementLines` through `shipRowCost`, and doEditShip re-prices the refits on every edit.
  `model/system/baseSystems.js`: CnC `canActivate` / `canDeactivate` / labels / `doActivate` / `doDeactivate`, `suppressActiveBoost`, a
  phase-3 branch in `doIndividualNotesTransfer`, `draftStateKeys` + `active` in phases 1 and 3. `combatLog.js`: the "SPEECH:" header, the ship
  not printed ahead of the sentence; `weaponManager.doShortLogText`: 'PoliticalSpeech'.
- Docs: an Expert Political Officer card (Ship Officers, alphabetically after the Plasma Scientist).

**Files.** Server: `Officers.php`, `Enhancements.php`, `systems/HangarOps.php`, `systems/baseSystems.php` (CnC), `controller/Manager.php`
(loadSavedFleet), `Phase/BuyingGamePhase.php`, `Phase/FireGamePhase.php`. Client: `officers.js`, `systemEnhancements.js`, `UI/confirm.js`,
`gamelobby.js`, `model/system/baseSystems.js`, `combatLog.js`, `weaponManager.js`. Docs `ammo-options.html`. Harnesses: `officersHarness.php`,
`officersClientHarness.js` (SystemActivation added to its bundle), the enhancements fingerprint. Statics (`fvbuild -Statics`) and legacy
bundles (`scripts/bundle-legacy.js` - the watcher was not running) rebuilt; no React source changed, no new class, no schema change.

**Measured.** Statics +3,301 B: 60 offers, all Centauri. Fingerprint: exactly one new line (the Primus's offer, the one Centauri hull it holds);
old copy `fingerprint.before-officers-stage25.txt`.

**Verified.**
- `officersHarness.php` **1718 / 0 with the database** (the Stage 23 registry-order check relaxed - he now sits between the Anticipator and
  the placed officers): the registry place; offers and prices on five hulls (two factions, a base, an enormous ship), none on a Centauri HCV,
  another faction's capital ship or a flight, and a sweep of every Centauri hull - offered on exactly the 60; the C&C post (the first of two);
  the free expert's table (dearest, the Scanner over the Navigator by rounding, ties to the registry, none without him or the four, none on a
  hull not offered him, a doctored Engineer row on a base - which has no Engine - not free), rowPrice; the commit's random expert for each d4
  face, none when one is bought, a base's from its three; his C&C lost, his Engineer serving on; the free armour through the REAL sanitiser
  (five of seven, the sixth and seventh and a Structure row paid, none without him or with his C&C destroyed before battle, a tie for the fifth
  to the lower system id); the commit's random armour (five different systems, none Structure, destroyed or already armoured, all free;
  nothing past five; Structure not counted; with only Structure left, nothing; with his C&C destroyed, no armour and no expert); +1 armour at
  load; the armed shuttle (none, one, two with Elite Crew, none with Poor Crew or on a
  hull not offered him) and the REAL hangar population (a Centauri Armed Shuttle, none on the shuttle-less Lias); the speech's +30 (the turn
  after only; not this turn's, not two turns on; none when his C&C fell or he was killed in that turn), published to an enemy without him, his
  team told it was used; the REAL `CnC::saveFirePhaseDeclaration` (one OffUsed note on his C&C, once; nothing from the other C&C, another
  player, a Command Node transfer, a ship without him, the gate off, after a reload that says he spoke, or with his C&C destroyed); the log line
  (his Ramming Attack, at itself, rolled 1, no shot, the sentence and a forced topic, none for last turn's speech); a saved fleet through the
  REAL `submitSavedFleet` / `loadSavedFleet` (the Navigator stored at 0, nothing re-priced on reload, five of six armour free).
- `officersClientHarness.js` **663 / 0** (34 new; the Stage 23 order check relaxed): the registry row; every Centauri static - the client's test
  and the server's offers agree (60); the free-expert table; the fleet list's costs; the buy dialog through `confirm.enhRowCost` / `isFreeExpert`;
  `setShipLevelRow` (a Navigator joining, him leaving); `dropDestroyedPosts` with his C&C destroyed; the armour through the REAL
  systemEnhancements (five free, then seven and a Structure one, the tie for the fifth, the menu's prices and notes, him un-bought, his C&C
  destroyed); his lobby
  summary; the C&C's buttons in every case (his, the other C&C, spoken, killed, another officer, an enemy, not mine, Initial Orders), the
  commit's transfer, Save Orders; the REAL SystemActivation box; the REAL combatLog's "SPEECH:" line (the ship named once) and a jump point's
  "HYPERSPACE:" line unchanged.
- `enhancementsHarness` **288 / 0** (fingerprint re-recorded, one line added); `enhancementsDifferential.js` **742 / 0** (no case added: he moves
  no lobby stat).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game has him).
- **Mutation-tested: 60 breaks (35 server, 25 client), 59 caught**, files byte-identical afterwards. Runner `mutate25.js` in the session
  scratchpad only (~30s a server mutant, ~3s a client one). The first pass missed five, each a missing check, now added: the free expert's
  eligibility test (only the five bases, with no Engine, can tell - a doctored Engineer row there), the armour's tie-break (the Primus fixture
  had no tie at the fifth place - Thrusters #20 and #25 now give one, both ends), a random armour landing on Structure (Structure blocks come
  last in system order, so the all-1 dice never reached them - a fixture with nothing but Structure left), and the commit ignoring a destroyed
  C&C. The one left is equivalent: `speakerIndex` letting a spent speech through, which `spend()` refuses itself.

**Observations, not changed:**
1. Not play-tested. Worth a test: a Centauri Primus with him and an Expert Navigator - the buy dialog's Navigator row reading "free"; five
   Hardened Armour placed (each 0p in the system menu), a sixth paid; then a second Primus with him and nothing else - after the commit, a random
   expert aboard (insignia) and five ✦; in the Firing phase, "Speech!" on the C&C, the log's SPEECH line once firing resolves, and +30 on the
   next turn's initiative.
2. Not covered by a harness: the two one-line phase hooks - `BuyingGamePhase`'s call to `placePoliticalPicks` and `FireGamePhase::advance`'s
   to `speechLog` (the methods themselves are; the phases need a whole game in the database).
3. Elite Crew's own armed shuttle comes "loaded with one set of standard missiles" only as far as FV's armed shuttle classes already carry
   them - nothing new for his.

### 9.33 Stage 26: Expert Software Engineer (2026-10-22)

**Rules text (user, 2026-10-22) - one of the original 25.** "Expert Software Engineer: This computer programmer is unique to the Hyach. He
provides several abilities that enhance the ship's computer, all of which operate constantly. The Hyach computer provides one extra bonus fire
control point (BFCP). This is lost only if the computer is completely destroyed. The player can leave this extra BFCP point unallocated until the
Weapons Fire Step of the Combat Sequence, recording it along with his weapons fire orders. The Hyach Computer ignores the first point of damage in
any volley (essentially, it can be treated as having an extra point of armor that cannot be damaged, destroyed or bypassed by weapons that affect or
ignore armor). If aboard a stealth unit, advanced electronic signature masking software makes the ship appear as though it were five hexes further
away for detection purposes (but not for weapons fire or other reasons), except for fighters, which halve their detection range (round fractions
up). Working as a team, the Expert Software Engineer permits a specialist to use his abilities a second time during the scenario. The specialist
must be of the software category, and must wait at least a full turn after using his special function before he can use it again. This only works
for one specialist per scenario. Location: Computer. Cost: 15% of the ship's base cost. Availability: Hyach only." The user's notes: "Specialists in
the Software category are; Defence, Sensor, Targeting, and Weapon. Just adding +1 Armour to the Computer system is fine, if adding a damage reduction
that affects armour-ignoring weapons is unnecessarily complicated."

**What FV had.**
- **13 hulls** qualify: every Hyach Gerontocracy hull with a Hyach Computer - two medium ships, five HCVs (two of them the stealth Alichi Kav and
  Tal) and six bases - but not the Takalti Kal, an OSAT (D9). No other faction's hull carries a Hyach Computer. Every one of the 13 has Specialists.
- **The Hyach Computer** (`HyachComputer`): `output` BFCP, allocated in Initial Orders only, at most `BFCPpertype` (2) to a category, stored as one
  individual note per point (key its index, value its FC type) and read back by `onIndividualNotesLoaded` - Initial Orders pre-fills last turn's,
  every other load counts this turn's. Only `getFCBonus`, in `Weapon::calculateHitBase`, uses them: the bonus is the allocation of the target's
  category. Its damage loss - 1 BFCP per 5 boxes - lived in `setSystemDataWindow`, which runs only as the payload is built (`beforeTurn`), never at
  fire resolution; nothing on the server checks an allocation against it. The allocation and output reach every viewer.
- **The Hyach Specialists**: chosen at deployment, each used once by the player (the client's + button), each use one `allocated;<type>` note
  that applies its effect at every load of that turn. The server writes the notes as the client sends them, unchecked; a Firing-phase use reaches
  the database through `BaseShip::generateAdditionalNotes` (the generic note sweep never runs in the Firing phase). The **Computer Specialist** gave
  +2 BFCP and +1 to the cap per category, in Initial Orders.
- **A Firing-phase declaration** on a non-weapon system: `ShipSystem::saveFirePhaseDeclaration` (the Wide-Beam Lightning Array's toggle, the
  Political Officer's speech), on the POST-side ship, judged at read time; the Firing phase resolves on a reload at phase 4.
- **Stealth detection by range** is one sweep, `Stealth::isDetectedMovement` (end of movement): a ship detects at its first Scanner's output x2 (x5
  a base, x3 plus Detect Stealth for ELINT), a flight at its OB, against the hex distance. The client's own range forecast is commented out.
- **The damage path's last word before damage is marked**: `ShipSystem::assignDamageReturnOverkill`, where the Scavenger's save already sits, after
  each weapon has worked out the armour it meets (`getSystemArmourComplete` - 0 for a weapon that ignores armour).

**Rulings (user, 2026-10-22, asked before building):**
1. **Build the Firing-phase hold** - and **rebuild the Computer Specialist to its own rules** on it: "Once per scenario, this specialist adds 1 point
   to the ship's bonus fire control points and allows these points to be reshuffled to any desired values. This ability is activated during the
   Firing Phase and must be done at the same time other weapons fire is targeted. The usual restrictions on the allocation of bonus fire control
   points must be observed."
2. **The cap per category stays two** - for his point, and for the rebuilt Computer Specialist, which no longer raises it.
3. **A software Specialist used on turn N may be used again from turn N+1.**

**Built with defaults, stated here (ask before changing):**
- "Lost only if the computer is completely destroyed": the Computer's damage loss never takes his point (`getBFCPAvailable` - on a base, 26 boxes
  leave him his point where the Computer alone would read -1); he is out of action with the Computer by D3 anyway, and killed by its kill roll.
- **The hold is ONE point ADDED in the Firing phase**: Initial Orders' points stay where they are, and the cap of two still applies. The player
  leaves a point free in Initial Orders, opens the Computer's menu in the Firing phase and presses + on a category (the menu shows only then, and
  says "Expert Software Engineer: one held point may be added"). A point not placed is unused. The Initial Orders commit's "you have not assigned
  available BFCP" warning ignores ONE free point while he serves (two still warn).
- The Firing-phase allocation counts for **every shot that turn's firing resolves** - the ballistic launches of Initial Orders included, since
  their hit chance is worked out there, as the Initial Orders points' always was.
- It is **hidden from opponents until firing resolves**: their payload and tooltip show Initial Orders' allocation while the Firing phase is open
  (fire orders are hidden until they resolve, and where the point went would point at the targets). It is never carried into the next turn's
  Initial Orders pre-fill.
- **The rebuilt Computer Specialist**: in the Firing phase only (no longer Initial Orders), once per scenario as every Specialist, +1 BFCP that turn
  and every point free to move within the cap - its own and the Software Engineer's included. Cancelling it before the commit puts the allocation
  back to Initial Orders'.
- **The armour point is per hit, as armour is**: each rake, each pulse. It is added to the armour each hit on his Computer meets AFTER the weapon has
  set that armour - so a weapon that ignores armour, or wears it down, still meets it - and the damage record carries it (one more armour). Not a
  stat: the Computer's armour readout is unchanged.
- **Signature masking** acts on the one range sweep (`isDetectedMovement`): every ship detector (ELINT's Detect Stealth total included) needs 5
  hexes more range; a flight's OB is halved, rounded up. Revealing itself by firing or by EW is untouched, as are its jammer and every weapon range.
  The Alichi Kav and Tal are the only stealth hulls offered him.
- **The second Specialist use**: a Defence, Sensor, Targeting or Weapon Specialist used once, on an earlier turn, while he serves; the first one used
  twice spends it for the battle; it obeys its own phase rules (Sensor in Initial Orders only) and has its full effect again. It spends no Specialist
  slot (the menu's "Specialists Used" count stays), and the menu lists it again as "Defence (again)". The server reads the uses from the notes it
  already loads, so nothing new is stored.

**Design.**
- `Officers`: registry `OFF_SOFT` (post `computer`, pct 15, `apply` applySoftwareEngineer) right after the Political Officer, before the placed
  officers; `$factionRules` `only` Hyach Gerontocracy; constants `SOFTWARE_BFCP` 1, `SOFTWARE_ARMOUR` 1, `SIGNATURE_MASK_HEXES` 5;
  `$softwareSpecialists` (⚠️ MIRROR PAIR with `officers.SOFTWARE_SPECIALISTS`). findPost `computer` = the HyachComputer. `applySoftwareEngineer`
  (`HyachComputer::addOfficerBFCP`); `holdsFiringBFCP` (⚠️ MIRROR PAIR with `officers.holdsFiringBFCP`); `computerArmour`; `maskSignature`;
  `specialistReuse` / `reuseSpentTurn` / `softwareUses`; addForJson's `reuse` list and `abilities.reuse` (the turn spent) on his own team's row.
- `HyachComputer` (baseSystems.php): `FIRING_NOTE` 'firingBFCP'; protected `$officerBFCP`, `$reshuffleBFCP`, `$firingBFCP`, `$firingRequested`,
  `$ioBFCP`, `$firingSettled`. `doIndividualNotesTransfer` keeps a marked transfer (`['firingBFCP', type per point]`) for ⭐
  `saveFirePhaseDeclaration` - ONE note, the points comma separated ('none' for no point), filtered to its categories and three caps;
  `onIndividualNotesLoaded` skips that note in Initial Orders' pre-fill and keeps this turn's highest-id one otherwise; ⭐ `settleFiringBFCP($turn)`
  (⚠️ MIRROR PAIR with `HyachComputer.firingMode` / `canIncrease` / `canDecrease`) judges it once, lazily - from `getFCBonus`,
  `setSystemDataWindow` and `stripForJson`, all after the load has resolved officers and Specialists; `getBFCPAvailable` (the damage loss,
  never his point); `shownBFCP` / `shownBFCPTotal` (opponents in the Firing phase see Initial Orders'); `ioBFCP` to the owner's team once the
  Firing phase's applied; `allowReshuffle`.
- `HyachSpecialists`: the Computer case +1 output and `allowReshuffle()` (was +2 and +1 cap); `$useTurns` / `getUseTurns` from the `allocated`
  notes; the tooltip's Computer line.
- `ShipSystem::assignDamageReturnOverkill`: `$effectiveArmor += Officers::computerArmour(...)` behind `$officersPresent`, before the Flash cap and
  the protectors. `Stealth::isDetectedMovement`: `Officers::maskSignature` behind `$officersPresent`.
- Client: `officers.js` - the registry row, findPost `computer`, `SOFTWARE_SPECIALISTS`, `SOFTWARE_BFCP`, `holdsFiringBFCP`, `bfcpHeldBack`,
  `computerBonus` / `bfcpLine` (the lobby's BFCP line, through adjustDataDisplay), `specialistReuseList` / `specialistReusable`, `ABILITY_NAMES.reuse`.
  `model/system/baseSystems.js` - HyachComputer `lockedBFCP` (built from `ioBFCP` or the loaded allocation), `firingMode`, phase-3 branches in
  `canIncrease` / `canDecrease`, the marked transfer, `totalOf` / `sameAllocation` / `restoreFiringBase`, `draftStatePhases` [1, 3]; HyachSpecialists
  `canUse` (the reuse; the Computer Specialist in the Firing phase only), `canDecrease`, `isSecondUse`, `doUse` / `doDecrease` (+1 / -1 and the
  restore; no slot spent on a second use). `gamedata.js` - the commit warning's exemption. React: `SystemInfoButtons` canBFCP (phase 3 while
  `firingMode()`), `HyachComputerList` (buttons ask the system's own rules; the Firing-phase line), `HyachSpecialistsList` (a reusable one listed,
  "(again)").
- Docs: an Expert Software Engineer card (Ship Officers, between the Security and the Surge Officer); the Computer Specialist's lines in
  `factions-tiers.html` and `files/FV_factions.txt`.

**Files.** Server: `Officers.php`, `systems/baseSystems.php` (HyachComputer, HyachSpecialists, Stealth), `systems/ShipSystem.php`. Client:
`officers.js`, `model/system/baseSystems.js`, `gamedata.js`, React `system/SystemInfoButtons.js`, `system/HyachComputerList.js`,
`system/HyachSpecialistsList.js`. Docs `ammo-options.html`, `factions-tiers.html`, `files/FV_factions.txt`. Harnesses: `officersHarness.php`,
`officersClientHarness.js` (its two Hyach menus added to the bundle; Stage 25's label checks brought to `ce9f8e8a2`'s "Speech!" / "No speech" /
"SPEECH!:"), `enhancementsHarness.php` (+HyachSenchlatKam, cases `soft` and `soft+elite1`; fingerprint re-recorded, old copy
`fingerprint.before-officers-stage26.txt`), `enhancementsDifferential.js` (the Computer's output + `computerBonus`). `yarn build` (UI bundle and
both legacy bundles - no watcher was running) and `fvbuild -Statics`; no new class, no schema change.

**Measured.** Statics +726 B: 13 offers, all Hyach. Fingerprint: 3,087 lines added, none changed - stripping the new hull's block and the two new
cases from the new fingerprint gives the old one byte for byte.

**Verified.**
- `officersHarness.php` **1800 / 0 with the database** (1718 --no-db; 79 new): the registry place; offers and prices on four hulls (HCV, stealth
  HCV, base, medium), none on the OSAT, a hull without a Computer, a Hyach flight or a Hyach Computer under another faction's name, and a sweep of
  every Hyach hull - offered on exactly the 13; the Computer post; +1 BFCP, kept through damage (10 and 14 boxes on the HCV, 26 on the base), none
  once killed; the Computer Specialist (+1 not +2, the cap 2, nothing the turn after, 5 with him) and its tooltip line; the Firing-phase allocation
  through `getFCBonus` - his held point on Fighter or a second Capital, refused when a point moved, two were added, a third Capital, without him,
  killed, an unknown category, past the damaged Computer's points; the Computer destroyed; the last note by id (loaded out of order); last turn's
  note; the reshuffle (accepted, five of four refused, three on one refused, "none", with him five, last turn's Specialist none); the next Initial
  Orders not pre-filled from it; the payload - the owner's allocation, ioBFCP and tooltip, an opponent's Initial Orders' only until firing resolves,
  a finished game's everything, no ioBFCP without one; the REAL POST-side `saveFirePhaseDeclaration` (one note, unchanged writes none, "none", a
  doctored list filtered); the armour point through the REAL `assignDamageReturnOverkill` (armour 4 and 0, a 1-point hit, another system, him
  killed, the gate off); `maskSignature` and the REAL `Stealth::isDetectedMovement` against an Omega and a flight; the reuse list (N+1, two at once,
  this turn's, not software, spent, without him, killed), a second use's effect applied, the payload's `reuse` and `abilities.reuse`, nothing to an
  opponent; the kill roll on his post.
- `officersClientHarness.js` **703 / 0** (40 new): the registry row and constants; every Hyach static offered where the client finds his post (13);
  the lobby post and BFCP line, in game untouched; the REAL HyachComputer in the Firing phase - hold (add, cap, one only, take off, the marked
  transfer, unchanged unmarked), destroyed, killed, without him, Initial Orders as ever, not my ship, a reload's `ioBFCP`; the REAL HyachSpecialists
  - the Computer Specialist (not in Initial Orders; +1, cap 2, reshuffle, cancelled back), the reuse (used without a slot, one per scenario, cancel,
  Sensor's phase rule, not on the list, killed), the spent line; the commit warning's exemption; the REAL `HyachSpecialistsList` ("(again)") and
  `HyachComputerList` (its Firing-phase line, its buttons from the system's rules).
- `enhancementsHarness` **289 / 0** (fingerprint re-recorded, additions only); `enhancementsDifferential.js` **780 / 0** (38 new cases - the Computer
  +1 compared on the Senchlat Kam; a negative control, `computerBonus` broken, gives the two `soft` cases differing).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game uses him or the Computer Specialist's new rule).
- **Mutation-tested: 79 breaks (46 server, 33 client and React, one through the differential), 77 caught**, files byte-identical afterwards.
  Runner `mutate26.js` in the session scratchpad only (~30s a server mutant, ~4s a client one; `DRY=1` checks every pattern occurs once). The
  first pass missed five, each a missing check, now added: the officer test of the hold (every "without him" note was refused by the Computer's
  own points first - now one that fits them), last turn's Firing-phase note (it was no valid hold anyway), the payload settling with no tooltip
  built first, the one-point limit of a hold with two points free (the Computer's total masked it), and a reused Specialist a third time in one
  turn. Of the two left: `count >= 1` for `=== 1` in specialistReuse is equivalent (a type used twice ends the reuse before the loop); removing
  the unknown-category check KILLS the load (the hold check's undefined index - FV's error handler ends the process), so it is load-bearing, and
  the reshuffle's own unknown-category check now fails as a FAIL line behind it.

**Observations, not changed:**
1. Not play-tested. Worth a test: a Hyach Senchlat Kam with him - 4 BFCP; in Initial Orders allocate 3, commit without a warning; in the Firing
   phase open the Computer, put the held point on the category of your target, fire, and see the hit chance include it; in an opponent's browser
   the Computer shows 3 points until firing resolves. A second ship with a Computer Specialist: use it in the Firing phase and move every point.
   An Alichi Kav with him near an enemy ship at its detection range plus 1-4 hexes: it stays hidden. A Defence Specialist used on turn 2 shows
   "Defence (again)" from turn 3.
2. Not covered by a harness: the commit warning's call site in `gamedata.js` and `SystemInfoButtons` canBFCP (the helpers they ask are), and
   `FireGamePhase::process`'s existing call to `saveFirePhaseDeclaration` (the method is).
3. The Specialists' notes are written as the client sends them, unchecked - a doctored second use of any Specialist works without him (as it
   always has).
4. A Computer Specialist used under the old rule shows +1 BFCP and a cap of 2 in a replay of that turn, where it showed +2 and 3: display only - the
   shots' bonuses come from the stored notes.
5. The Utara Tor's Computer (28 boxes, 4 BFCP) reads -1 BFCP at 25 or more boxes of damage, as it always did - so the negative-BFCP warning can fire
   with nothing allocated.
6. The Political Officer's docs card still says "Cancel speech"; `ce9f8e8a2` named the button "No speech".

### 9.34 Stage 27: Expert Troop Leader (2026-10-23)

**Rules text (user, 2026-10-23) - an officer the user added.** "Expert Troop Leader: The expert troop leader is a genetically engineered Gaim
designed for independent thought and leadership abilities. His primary focus is in the direction and control of ground forces, which does not apply to
most Babylon Wars situations. However, aboard a ship he still has a few abilities he can lend to his crew. If the ship is attacked by enemy breaching
pods, he provides a penalty of 2 on all enemy rolls and a bonus of 2 on all defense rolls. If any of his own ship's breaching pods attack an opponent,
they gain a +1 bonus on any attack table. The troop leader can also accompany a breaching pod, and if this is done, he gives a further -2 bonus (a total
of -3 for Gaim) to any rolls. If any result appears that "kills" one of his contingents, roll a d6, and on a roll of 1-4 he manages to escape with his
pod (but not the rest of his team e.g. the Marine unit is lost, but the Officer is not killed). This is not available on Wreak Havoc missions. The expert
troop leader also provides heightened training to many of his crew, enhancing their reflexes. Nowhere this is more acute than with the crewmen whose job
it is to control the bulkheads. Thus, all bulkheads on the ship are considered to have 1 extra box. This benefit remains even if the troop leader is
killed in combat, but only for the duration of that scenario, after which the bulkheads revert to normal. Location: Hangar. Cost: 5% of the ship's base
cost. Availability: Gaim Only." The user's notes: "We have no way to transfer Officer from ship to docked Breaching Pod atm. So we should instead also
offer this Officer separately to Gaim Breaching Pods at the cost of 50% the price of a pod (e.g. 20pts)"; "the Troop leader accompanies any Marine that
attacks from that pod, and so can be killed if marine unit is killed and he fails his saving throw". And a separate request: a flight of breaching pods
may take as many Breaching Experts as it has pods ("The fleetwide cap at 20% would still apply"), and pod Troop Leaders likewise, "since their bonuses
are on a pod basis".

**What FV had.**
- **36 Gaim hulls** with a plain hangar bay - not the three Rafa gunboats, which have none, nor the Tora OSAT. Every Gaim hull but the Tracha carries
  Bulkheads (`Bulkhead`: 1-4 boxes, absorbing hits on its own section, automatically). One Gaim pod: the Ech'Akki, 40 a pod, its Marines built Elite.
- **Boarding**: a delivery d10 (`Marines::getDeliveryRollMod`), the missions' d10s (`ShipSystem::getMarineRollMod` - Sabotage, Wreak Havoc, Rescue and
  each capture attack) and the defenders' d10s in capture combat; lower is better for the boarders; the Security Officer's `boardingShift` at five
  sites. A party is a critical whose `param` carries its pod flight (id, userid, team) - the only field it keeps from turn to turn.
- **A party is eliminated** by a delivery roll of 9+, a defender's hit in capture combat, a Sabotage roll of 4-5 (damage dealt, then eliminated) or 9+,
  a Wreak Havoc roll of 9+, and a Rescue roll of 7+ - or of 3-4, "complete, but heavy casualties", which ends the mission with no unit back in a pod.
- **No flight remembers its carrier**: a launched flight carries no home ship. The lobby's Fleet Correctness Report does count each ship's pods (its
  per-ship profile in `checkChoices`).
- One officer per flight (D9), the Expert Pilots excepted (a count of one kind).

**Rulings (user, 2026-10-23, asked before building):**
1. **"His own ship's breaching pods"** are as many of his player's pods as the ship's **Pod Allowance** - the Fleet Correctness Report's count - wherever
   they are; asked again, which: **in fleet order, fixed** (a lost pod's share passes to nobody).
2. **In a pod: -3 from him** - "a further -2 bonus (a total of -3 for Gaim)" read as his ship's 1 and the further 2, both carried by the pod officer -
   on top of the Ech'Akki's own Elite -1.
3. **Wreak Havoc: no escape** - he dies with the party (his -3 still helps its rolls).
4. **A pod flight may mix** Breaching Experts and Troop Leaders, **one officer to a pod**.

**Built with defaults, stated here (ask before changing):**
- His post is the **largest plain Hangar** (by boxes; the first on a tie), never a catapult, fighter rail, docking collar or docking bay. 5% of the hull,
  Gaim only; a hull with no pod slots (Pod Allowance 0) still has the defence and the Bulkheads.
- The defence is `boardingShift`'s +2 on the Security Officer's five sites - every boarding party, pods, Grappling Claws and transporters alike; with a
  Security Officer, +4.
- **Bulkheads +1 box from the start, for the whole battle**: `lost` runs his `apply`; every viewer is sent the boxes (a displayed stat). Pre-battle damage
  on his Hangar refunds him in the lobby, so he is never aboard and gives none.
- **Pod Allowance** = the report's per-ship count: its "Breaching Pods" racks (HANG_BP's included) + min(1 - 2 on a capital, 4 on a base or enormous
  unit, doubled on an Assault hull -, its assault shuttle + heavy-or-"normal" + medium slots). Worked out at `pickPosts`, from the blueprint's slots
  (Enhancements' hangar conversions rewrite `$ship->fighters` after it), carried on his entry (`pods`) and sent to his team.
- **Fleet order** = unit id on the server (BuyingGamePhase inserts the fleet in its order), Fleet Selection's own order in the lobby; craft by craft; any
  breaching pod of that player, whatever its faction; several Troop Leader ships of a player add their allowances; one out of action takes his away.
- Both bonuses are asked on the roll's turn: the ship's 1 while that Troop Leader serves; the pod's 3 while its officer serves - lost with his pod, as
  every fighter officer, so a party aboard loses it when its pod is destroyed.
- **The escape**: a d6 for every party from his pod that is eliminated (the results above); 5-6 kills him - one `OffKilled` note on his pod. He serves
  out the turn he dies (D3), so a second party lost that turn finds him dead and rolls nothing. A party that vanishes with a destroyed target ship rolls
  nothing (FV resolves nothing there). No combat log line names him (D4).
- **Breaching Experts one to a pod**: each makes his own pod's Marines Elite and has his own capture assault; the flight's +1 thrust once, however many;
  each priced at one pod; the 20% line counts every one. **Mixed**: Breaching Experts take the first pods, Troop Leaders the next, never more officers
  than pods; beside any other officer row, D9 (the first in registry order, alone).

**Design.**
- `Officers`: registry `OFF_TROOP` (post `hangar`, pct 5, `apply` and `lost` both `applyTroopLeader`) after the Software Engineer; `OFF_TRPOD` (post `craft`,
  `flight`, pct 50, `unscaled`, `multi` 6 - held to maxFlightSize -, `mixesWith` OFF_BRCH, `needs` isBreachingPod) last; OFF_BRCH gains `multi` 6 and
  `mixesWith` OFF_TRPOD. ⭐ New registry key **`mixesWith`** (pickPosts: after an officer bought as a count, one he mixes with takes the crafts after his).
  `$factionRules` `only` Gaim for both. findPost `hangar` (`isHangarBay`). Constants TROOP_BOARDING_SHIFT 2, TROOP_SHIP_POD_BONUS 1, TROOP_POD_BONUS 3,
  TROOP_ESCAPE_MAX 4, TROOP_BULKHEAD_BOXES 1, TROOP_PARTY_KEY 'troop'. `podAllowance` (⚠️ MIRROR PAIR with officers.podAllowance), `applyTroopLeader`,
  `troopBonus`, `troopDeliveryMod`, `markTroopParty` / `playerHasTroopLeader`, `troopPartyRollMod` / `troopParty`, `troopDeliveryLost` / `troopPartyLost`
  / `troopEscape`, `troopAidedCraft` (⚠️ MIRROR PAIR with officers.troopAidedCraft); `boardingShift` + his 2; `applyBreachingExpert` once a flight;
  addForJson's `pods` on his row.
- `Bulkhead` (baseSystems.php): protected `$boxesAdded`, `addBoxes`; stripForJson sends `maxhealth` when boxes were added.
- `Marines` (specialWeapons.php): getDeliveryRollMod + `troopDeliveryMod`; the party's critical marked (`markTroopParty`: its craft id in `param`, only
  for a player with a Troop Leader) after the mission switch; a delivery roll of 9+ -> `troopDeliveryLost`.
- `ShipSystem`: getMarineRollMod + `troopPartyRollMod`; `troopPartyLost` at Wreak Havoc 9+ (`$wreakHavoc`: no escape), a Sabotage party eliminated, each
  capture attacker eliminated, and Rescue 3-4 and 7+; stripForJson unsets TROOP_PARTY_KEY beside BREACH_PARTY_KEY.
- Client `officers.js`: the registry rows (OFF_TROOP, OFF_TRPOD; OFF_BRCH `multi`, `mixesWith`, `statsOnce`), findPost `hangar`, listFor's mixing,
  `rowMax(..., taken)`, `mixes`, priceNote "Npts each, one per pod", displayStat's `statsOnce`, the constants, `podAllowance` (the fleet check's
  `_originalFighters` and HANG_BP rows), `fleetOf`, `podCraftCount`, `troopAidedCraft`, `troopBonus`, `marinesEliteLine`'s troop suffix ("Yes; Expert
  Troop Leader: -3 on its rolls" / "...; a Troop Leader's ship: -1 on its rolls"), `troopBulkheads`, and dropDestroyedPosts giving the box back.
  `UI/confirm.js`: `enhRowMax(target, taken)`, `mixedTaken`, clampEnhRowsToSize keeping the first rows', officerBlocked letting partners pass,
  OFFICER_MIX_NOTE. `lobbyEnhancements.js`: case OFF_TROOP (`troopEnh`) and its reset.
- Docs: Expert Troop Leader cards in Ship Officers (between the Technician and the Turret Officer) and Fighter Officers (after the Tailgunner); the
  Breaching Expert's card and the Fighter Officers' opening paragraphs updated.

**Files.** Server: `Officers.php`, `systems/baseSystems.php` (Bulkhead), `systems/ShipSystem.php`, `weapons/specialWeapons.php` (Marines). Client:
`officers.js`, `UI/confirm.js`, `lobbyEnhancements.js`. Docs `ammo-options.html`. Harnesses: `officersHarness.php` (and the fixture it writes,
`troopPodAllowanceFixture.json`), `officersClientHarness.js` (four registry-order and price-note checks moved to the new layout), `enhancementsHarness.php`
(+gaimBassari, cases `troop` and `troop+secu`; flight hull breachingpodgaim, cases `brch2`, `trpod`, `brch+trpod`, `trpod+dog`; fingerprint re-recorded,
old copy `fingerprint.before-officers-stage27.txt`). `fvbuild -Statics` and both legacy bundles (`node scripts/bundle-legacy.js` - no watcher ran); no React
change, no new class, no schema change - a party's param grows by `,"troop":N` only for a player with a Troop Leader.

**Measured.** Offers: OFF_TROOP on the 36 Gaim hulls, OFF_TRPOD on the Ech'Akki; every breaching pod's OFF_BRCH limit 1 -> 2. Fingerprint: 4,083 lines
added (the Bassari's block and 34 new case blocks), none changed - stripped of them, the new fingerprint is the old one byte for byte.

**Verified.**
- `officersHarness.php` **1903 / 0 with the database** (1821 --no-db; 103 new): the ids, labels and registry places; offered on exactly the 36 Gaim
  hulls with a hangar bay at 5%, on no other faction's hull, pod or flight; the pod officer on the Ech'Akki alone at 20, limit 2, prices of one and two;
  the post - the biggest Hangar, the Korvex's aft, the Tocrat's first of two, never a catapult, docking bay, collar or rail; the Pod Allowance table (12
  hulls, HANG_BP, read before the conversions) and the fixture over every hull of the statics; the defence (shift, the REAL mission and delivery
  modifiers, the REAL capture combat, with a Security Officer 4, killed, disabled, his kill roll); the Bulkheads (+1 on all eight, the readout, sent to
  both viewers, none without him, the REAL absorption 3 not 2, kept killed or disabled, none from pre-battle damage); his pod's -3 through the REAL delivery
  and mission rolls, the mark (his pod, another pod of his player, none for a player without one), stripped from both viewers, lost with his pod; the ship's
  pods (fleet order by id past a fighter flight, two ships adding, any faction's pod, fixed, killed, a Grast's 0, another player's), the REAL rolls -1 and
  -3 not -4, the `pods` payload; the escape through the REAL delivery, Wreak Havoc, Sabotage (9+ and 4), Rescue (3, 7, a success) and capture combat - the
  note, serving out the turn, no second roll, a later load; mixed flights (posts, thrust once, each pod Elite, each assault, the payload).
- `officersClientHarness.js` **736 / 1** (34 new; the one failure is the user's "(repeat)" label, §9.33's): the rows and the constants against
  Officers.php; the statics - OFF_TROOP on the 36 hulls where the client finds his Hangar, OFF_TRPOD on the Ech'Akki alone, OFF_BRCH up to 2 on every pod;
  `podAllowance` equal to the server's on every hull with hangar slots (the fixture) and 0 on the rest; the post; the lobby list of mixed flights, the
  thrust once, rowMax, mixes, the price note, the 20% count; the REAL buy dialog (officerBlocked, enhRowMax, clampEnhRowsToSize, the Officers line); the
  pods aided in game (by id, two ships, killed, another player's) and in the lobby (Fleet Selection's order, one card for a pair); the Marines lines and the
  REAL SystemInfo; the lobby Bulkheads through the REAL lobbyEnhancements (once, after an edit, refunded, unbought).
- `enhancementsHarness` **291 / 0** (fingerprint re-recorded, additions only); `enhancementsDifferential.js` **840 / 0** (60 new cases - the Bassari's
  Bulkheads compared box for box; the Ech'Akki's posts and thrust); negative controls in the mutation pass.
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game holds a Troop Leader or a pod flight of two officers).
- **Mutation-tested: 101 breaks (60 server, 39 client and lobby, 2 through the differential), 99 caught**, files byte-identical afterwards. One of them
  (dropping the crafts already taken from the pod count) kills the load - a third pod that does not exist - so it is load-bearing. The first pass
  missed five, each a missing check, now added: the "no mark" check ran in a game with no officers at all (so the mark code never ran - now the enemy
  has a Troop Leader), twice; Rescue 7+ only tested an escape, which looks like no roll; units that carry no slot (the client's owner test was hidden by
  the slot test); and a second `setEnhancementsShip` past apply()'s own flag. Of the two left: the client listFor's "nobody but a partner after the
  first" guard cannot be reached today (no flight officer follows the pod Troop Leader in the registry - kept for one added later); and the
  differential cannot see the thrust counted once - its flights hold one craft, so two Breaching Experts never both serve there - which the client
  harness catches. Runner `mutate27.js` in the session scratchpad only (`--dry` checks every pattern occurs once). Before the pass,
  three gaps found while writing it were closed (a fighter rail and a tied pair of bays for the post, an enemy Troop Leader ship and a fighter flight in
  the fleet order), and the Pod Allowance's "no slots" early return was removed on both ends: racks + min(cap, 0) is already 0, so it was dead code.

**Observations, not changed:**
1. Not play-tested. Worth a test: a Gaim fleet with a Bassari (Troop Leader) and three pairs of Ech'Akki - the Marines tooltips of the first pair say
   "-1 on its rolls", the others nothing; a pair with a Breaching Expert and a Troop Leader (the buy dialog allows both, one to a pod); his pod's capture
   party's rolls in the log's Roll(Mod) 3 lower; the Bassari's Bulkheads at 3 boxes in both browsers; an enemy pod against the Bassari rolling 2 higher.
2. ~~Pre-existing: a Sabotage party whose target system is destroyed switches to Wreak Havoc without its pod info.~~ Fixed the same day on the user's
   request - and it was worse than first reported: see the follow-up below.
3. A Grappling Claw's marines (a ship's) are no pods: neither pod clause reaches them; his ship's defence does.
4. The lobby previews the Bulkhead box by changing `maxhealth` in lobbyEnhancements (as IPSH_ESSAN does), not at read time: `maxhealth` is read in too many
   places. dropDestroyedPosts takes it back. (User: "That seems ok to me.")

**Follow-up (user, 2026-10-23, the same day): a Sabotage party whose target is already destroyed; the Bulkhead's readout.**
- *What the code did* (corrected - observation 2 above had it wrong): `ShipSystem::criticalPhaseEffects` opens with "a destroyed system: return", so a
  Sabotage party aboard a system **destroyed by other fire** was never processed again - aboard for the rest of the battle doing nothing, never back in a
  pod, never on to Wreak Havoc. The in-loop "already destroyed" branch was reached only by a second party on a system the first destroyed in the same
  phase: it made a Wreak Havoc critical **without** the pod info, and left its own critical open.
- *Ruled (user)*: "If the Marines can return to an attached pod then they should do so, only if no pods are available should they resort to the Wreak
  Havoc mission" - the rule doSabotageMission already applies when a party destroys its own target.
- *Built*: `ShipSystem::sabotageTargetsLost($ship, $gamedata)` (from the early return: each active Sabotage party aboard a destroyed system, not the
  C&C's own Wreak Havoc parties) and `sabotageTargetLost($critical, $ship, $gamedata)` (also the in-loop branch, its body now one call): through
  `initMarineMission` (nothing on a captured ship), the critical ended, back to an attached pod by `returnMarines` (its own pod, its player's, its team's)
  with "The marine unit returns to a friendly pod.", else a Wreak Havoc critical on the C&C from the next turn with the pod info - a Troop Leader's mark
  among it - and "No pods available, the marine unit switches to Wreak Havoc." The FAQ's Sabotage paragraph and the Troop Leader card say so.
- *The Bulkhead's readout*: its static blueprint's `output` is 0 (the server sends the boxes left as `output` only in game), so a lobby window showed 0.
  The client `Bulkhead.prototype.initializationUpdate` (model/system/baseSystems.js) sets `outputDisplay` to its boxes left - maxhealth less
  damageManager.getDamage, the health bar's figure - on every render (SystemIcon.render runs initializeSystem); a destroyed one's 0 falls to the generic
  readout. In game it equals the server's `output`.
- *Verified*: officersHarness **1911 / 0 with the database** (1829 --no-db; 8 new through the REAL criticalPhaseEffects - back to the pod, no pod and the
  Wreak Havoc critical with the pod info, a captured ship, a live gun still rolling, two parties where the first destroys the gun, a destroyed C&C's party,
  a party not yet aboard, a party already ended); client **737 / 1** (the Bulkhead readout through the REAL damage.js: 2, 3 with his box, 1 after 2
  damage, 0 destroyed); `fvbuild -Check` replay **162 / 0** (no corpus game had a party on a destroyed system). Mutation: 10 more breaks, all caught (two
  only after the last two checks were added). Legacy bundles rebuilt; served copy synced.

### 9.35 Stage 28: Expert Warrior (2026-10-23)

**Rules text (user, 2026-10-23) - an officer the user added.** "Expert Warrior: A race so intent on conflict cannot help but produce experts in
combat. These are referred to as 'Tor 'galla,' the Chosen ... A ship with an expert warrior receives + 1 to hit with all weapons, and all weapons cause +1
extra damage per die. The ship receives a +10 bonus to its initiative and any fighters or shuttles it launches receive the same initiative (but not to-hit
or damage) benefit. All critical hits, regardless of system, have a -1 on their rolls, and any critical hits caused by the expert warrior's ship gain +1 on
their dice. Once per scenario the expert warrior can also do each of the following (but cannot do more than one of them in a given turn): He can 'charge'
an opponent by pointing his ship at the target and moving directly towards it for his full movement. The 'charge' is declared as this maneuver is performed.
He must move at least 4 hexes towards the enemy and must have beaten him in initiative. If this is done, he gains a further + 2 to hit that unit with all his
ship's weapons. His ship can execute one snap turn as though it were agile, at a cost of 1 point less thrust than it should. If the ship is already agile,
the thrust cost is halved instead. He can make a 'precision strike,' a single 'called shot', with only one weapon, without the usual called shot penalty.
He can make a 'supreme anticipation,' earning his ship a + 50 initiative bonus (in addition to any other benefits) on the next turn. However, on the turn
after that one, his ship suffers a -50 penalty. Location: C&C. Cost: 50% of the ship's base cost. Availability: Drazi only." The user's notes: the Charge
"probably needs a new Movement UI symbol (can be in the same position as Contraction / Half Phase icons go ... chargeIcon.png ... it'll need recoloured).
When clicked it activates the 'Charge' mode, but we'll still need to check the other conditions are met in server to actually apply the bonuses"; "the
'precision strike' bonus can just be on the first Called Shot of the game"; "Supreme anticipation can follow the same method as Centauri Political
Officer's speech mechanic ... (use Blue menu styles, not purple tho), then also have follow-up initiative penalty"; "I've already converted the
Initiative Bonus/Penalties to FV values."

**What FV had.**
- **51 Drazi hulls** may carry him: every hull of the Drazi Freehold (25) and Drazi Freehold (WotCR) (26) with a C&C that is not an OSAT or mine - three
  bases (Brostilli, BrostilliNew, Kromala) included. Drazi fighters, pods and shuttles are flights.
- **To hit**: `Weapon::calculateHitBase` (ships' called shots -8, doubled against a base; the officers' block behind `$officersPresent`); the client twin
  `weaponManager.computeShotModifiers`. **Per die**: `Dice::$perDieBonus` in `getFinalDamage` (Elite Crew, the Gunner, the Ion Technician).
- **Critical rolls**: `ShipSystem::testCritical` adds the system's and the SHIP's `critRollMod` (fighters' dropout reads both too); damage rows made during
  firing carry their `shooterid`, rows loaded from the database only their fire order.
- **Launches**: a hangar launch writes a `hangarLaunchEvent` note on the bay, value led by the unit's id (`HangarOps::performLaunch`), which every load
  reads (`Hangar::onIndividualNotesLoaded`); a launched flight keeps no link to its carrier. A flight's `iniativebonus` reaches the client from the static
  blueprint unless something publishes it.
- **Movement**: FV moves by initiative (lower first), so a unit that "beat" another moves after it and sees where it ended. An agile ship's snap turn is a
  turn straight after a turn without the delay met, at its full cost (`canTurn`); movement.php recomputes no thrust cost. Non-moving rows exist
  (`halfPhase`, `evade`, `contract`). The Minesweeping Mode icon is an on/off movement icon chosen before the first step.
- **The speech** (Stage 25): the C&C's Activate box (SystemActivation, `active`), a phase-3 transfer, `CnC::saveFirePhaseDeclaration` -> `Officers::saveSpeech`
  on the loaded ship (an OffUsed note), +30 on the next turn's load; the box purple through `CnC.prototype.activationMenuPurple`.

**Rulings (user, 2026-10-23, asked before building):**
1. **Both Drazi factions** - the Freehold and its WotCR (as the Political Officer's two Centauri factions).
2. **"Any fighters or shuttles it launches"** = **flights launched from his ship in battle** - not a hangar allowance.
3. **The player picks the unit charged** (click the icon, then the enemy); the server checks the rest.
4. **On an Expert Gunner's weapon the Gunner's +1 to hit and +1 per die replace his** (the Gunner's "does not combine").

**Built with defaults, stated here (ask before changing):**
- **+1 to hit and +1 per die**: every weapon of his ship, ballistics included, never the ram; per die capped at the face, stacking with Elite Crew; a
  fixed-damage weapon gains nothing. The charge's +2 stays on a Gunner's weapon (it is the charge's, not a standing bonus).
- **+10 for launched flights**: every FighterFlight his ship's bays (hangars, catapults, rails) have launched in battle, from the turn AFTER its first
  launch, while he serves on that turn - once, however many Warrior ships launched it; not an escape from a destroyed carrier, not an LCV or other ship.
- **-1 on his ship's critical rolls**: the ship's `critRollMod`, so every system's roll and the kill roll of every officer aboard. **+1 caused**: a system
  of another ship that took damage through its armour this turn from his ship (any weapon, the ram included) rolls 1 higher - once however many hits,
  in Pass 1 and the kill roll, taken back after. Not a fighter's dropout roll (it is not a critical); not his own ship.
- **The Charge** *(its run and snap turn superseded by the Third round below: 4 hexes nearer, route free, snap turn anywhere)*: declared before the ship's first step into a hex; the row is spent once committed whether or not the run holds (else the cheaper
  snap turn could be had every turn - the user's ruling 3 left spending open). It holds when, against where the target ended its own move: he has the
  better initiative (`BaseShip::hasBetterIniative`), the run is at least 4 hexes, every step a `move` (no slip) a hex closer (so never through and past
  the target), the target within 30 degrees either side of his bow at the first step and his facing = heading there, and no turn or pivot once moving
  (an Expert Anticipator's Firing-phase turn afterwards does not count). +2 to hit that unit with every weapon that turn, ballistics included.
- **The snap turn**: only while charging, after the charge row and before the first step - a turn straight after a turn with the delay not met (an agile
  ship's own snap turn, or a non-agile ship's one); 1 thrust less (never below 1), or half rounded up on an agile ship; one only. Client only, as every
  turn cost.
- **The precision strike**: automatic; his ship's FIRST standard called shot - of a turn's called shots, his lowest-numbered weapon's first order (the
  order the server prices shots in) - with the called shot penalty taken off again (doubled against a base, as the penalty); a Gunner's +3 on that shot
  neither used nor spent (the Targeter's precedent), also at the turn change's faster reload. Not on a turn he charged or ordered his anticipation.
- **Supreme Anticipation**: "Supreme Anticipation!" / "No anticipation" in his C&C's Activate box, in the default blue; +50 on the next turn only while he
  serves (the Political Officer's ruling), -50 on the turn after whatever became of him (`lost`). Not on a turn he charged.
- **One a turn** is counted on the turn each is used or ordered: a Charge (Movement) blocks that turn's anticipation and precision strike; an ordered
  anticipation (Firing) blocks that turn's precision strike.
- **Visibility**: his initiative changes and a launched flight's are published; the charge row is ordinary movement, public (the tabletop declares it);
  which unit it holds against rides his team's payload (`charged`).

**Design.**
- `Officers`: registry `OFF_WARR` (post `cnc`, pct 50, `apply` applyWarrior, `lost` applyWarriorLost, `publish` / `lostPublish` iniativebonus) after the Troop
  Leader, before the placed officers; `$factionRules` `only` both Drazi factions; constants WARRIOR_DIE_BONUS 1, WARRIOR_INI 10,
  WARRIOR_CRIT_MOD -1, WARRIOR_CAUSED_CRIT 1, CHARGE_HIT 2, CHARGE_MIN_HEXES 4, CHARGE_BOW_ARC 30, SUPREME_INITIATIVE 50, SUPREME_PENALTY -50; `$abilityNames`
  precision / anticipation. `warriorIndex`, `applyWarrior` (+1 fire control since the follow-up below) / `applyWarriorLost` / `anticipationShift`, ⭐ `chargeHitBonus`
  (⚠️ MIRROR PAIR with officers.chargeHitBonus; `warriorHitBonus` before the follow-up), `warriorDieBonus`, `resolveCharges` (abilities['charge'] off the movement rows, in `apply`), ⭐ `chargeTarget` / `chargeable` /
  `chargeRunHolds` / `positionOn`, `validateCharge`, ⭐ `precisionShot` (⚠️ MIRROR PAIR with officers.precisionWeapon) / `precisionStrike` / `sameOrder` /
  `precisionWeapon`, `saveAnticipation` / `anticipatorIndex` (⚠️ MIRROR PAIR with officers.canAnticipateSupreme), `markCausedCriticals` /
  `clearCausedCriticals` / `fireOrderShooters`, `afterShipsConstructed` -> `applyLaunchedFlights` + `settleCharges` (`charged` on his entry);
  `gunnerShotBonus` and `firingSpentAbility` give way to the precision strike; `addForJson` publishes a launched flight's iniativebonus and sends `charged`.
- `Movement` (movement.php): `CHARGE` 'charge', `CHARGE_SNAP_TURN` 'chargesnap' (⚠️ MIRROR PAIR with shipManager.movement's), `getChargeRow`, `isHexStep`.
- `BaseShip`: protected `$officerLaunchIni` + getter/setter. `Hangar`: protected `$launchedUnits` (unit id => first launch turn) from its `hangarLaunchEvent`
  notes, `getLaunchedUnits`. `CnC::saveFirePhaseDeclaration` takes ['anticipation'] beside ['speech'].
- Hooks: `Weapon::calculateHitBase` (precisionStrike before the Gunner's, chargeHitBonus after the Targeter's), `Weapon::getFinalDamage` (warriorDieBonus),
  `Criticals::setCriticals` (markCausedCriticals before Pass 1, clearCausedCriticals after the kill rolls), `TacGamedata::onConstructed`
  (afterShipsConstructed after the markers), `MovementGamePhase::process` (validateCharge on the authoritative ship).
- Client `officers.js`: the registry row (`stats` iniativebonus 10, critRollMod -1 - the lobby preview), ABILITY_NAMES, `warriorEntry`, `isWarriorPost`,
  `chargeHitBonus`, `fireControlRaise` (his +1, follow-up), `precisionOpen` / `precisionWeapon` / `precisionStrike`, `canCharge`, `canAnticipateSupreme`; `gunnerShotBonus` gives
  way. `movement.js`: CHARGE / CHARGE_SNAP_TURN, `chargePicking`, `getChargeRow`, `hasSteppedThisTurn`, `canCharge`, `getChargeState`, `toggleCharge`,
  `chargePickingFor`, `isChargeTarget`, `doCharge`, `isChargeSnap`, `chargeSnapCost`; `canTurn` allows the snap turn and prices it, `calculateRequiredThrust`
  prices it, `doNormalTurn` marks it. `UI/shipMovement.js`: the Charge icon (`chargeIcon` art, `drawChargeIcon` - off / picking / on / locked, its label
  naming the unit), `chargeCallback`; `ShipMovementCallbacks.chargeCallback`; `MovementPhaseStrategy.targetShip` takes the clicked enemy while it waits;
  game.php's `#charge` element. `model/system/baseSystems.js` CnC: `activationMenuPurple` a getter (false on his C&C), `isWarriorPost`, `mayDeclare`, his
  labels, the ['anticipation'] transfer. `weaponManager.computeShotModifiers`: "Precision Strike", "Charge" lines ("Expert Warrior" until the follow-up).
- Art: `img/chargeIcon.png` recoloured to the movement icons' green (36,105,9), alpha kept (the white original kept in the session scratchpad only).
- Docs: an Expert Warrior card (Ship Officers, after the Turret Officer).

**Files.** Server: `Officers.php`, `ShipClasses.php` (BaseShip), `systems/baseSystems.php` (Hangar, CnC), `weapons/weapon.php`, `handlers/movement.php`,
`handlers/criticals.php`, `TacGamedata.php`, `Phase/MovementGamePhase.php`. Client: `officers.js`, `movement.js`, `UI/shipMovement.js`,
`ShipMovementCallbacks.js`, `renderer/phaseStrategy/MovementPhaseStrategy.js`, `model/system/baseSystems.js`, `weaponManager.js`, `game.php`,
`img/chargeIcon.png`. Docs `ammo-options.html`. Harnesses: `officersHarness.php` (two Stage 27 registry-order checks moved), `officersClientHarness.js`,
`enhancementsHarness.php` (+Sunhawk, cases `warr`, `warr+helm+elite1`; fingerprint re-recorded, old copy `fingerprint.before-officers-stage28.txt`),
`enhancementsDifferential.js` (a ship's critical modifier compared through `officers.displayStat`, as every officer share). `fvbuild -Statics` and both
legacy bundles (`node scripts/bundle-legacy.js`); no React change, no new class, no schema change.

**Measured.** Statics: 51 offers, all Drazi. Fingerprint: 3,005 lines added (the Sunhawk's block and the two cases on every hull), none changed - stripped of
them, the new fingerprint is the old one byte for byte.

**Verified.**
- `officersHarness.php` **2013 / 0 with the database** before the gap tests below; **1937 / 0 --no-db** after them: the registry place; offers and prices on
  five hulls of both factions (a base, an agile hull, a WotCR hull), none on an OSAT, another faction or a flight, a sweep of both statics (51); the post;
  +10 and -1 (the REAL testCritical on his reactor and C&C, his own kill roll), published, none once killed; Supreme Anticipation's +50 / -50 (killed the turn
  he ordered it, after, on the turn it lands), published, the REAL `CnC::saveFirePhaseDeclaration` (one note, once; a speech asked, another player, a ship
  without him, the gate off, spent, a turn he charged, killed); +5% and +1 per die through the REAL calculateHitBase / getFinalDamage (a Gunner's beam, Elite
  Crew, the gate off, killed, the ram); the precision strike through the REAL calculateHitBase (+40% waived and +5%, spent once, this turn's, last turn's,
  a lower-numbered gun's call - intercepting, last turn's, in a fixed mode -, a charge or anticipation that turn, killed, a Gunner's +3 neither used nor
  spent, a base's doubled penalty) and `firingSpentAbility`; the charge's geometry (initiative, length, overshoot, a step no closer, a target that moved,
  the bow at 9 / 30 / 35 / 60 degrees, snap turns before, a turn / pivot / slip after, a pivoted bow, declared late, an Anticipator's turn after, last turn's,
  no Warrior, killed, another unit), +10% through the REAL calculateHitBase, the payload (`charged`, spent), `validateCharge` (kept; a second, after a step,
  no Warrior, charged before, own team, no unit, a destroyed one, killed - dropped); the caused +1 (shooter on the row or through its fire order, two hits, armour, last turn, killed,
  a flight, his own ship) and through the REAL `Criticals::setCriticals`; the launch record through the REAL `Hangar::onIndividualNotesLoaded`, the +10
  (cases), its publication, and through the REAL `TacGamedata::onConstructed`.
- `officersClientHarness.js` **800 / 1** (the one failure the user's "(repeat)" label, §9.33's): the row and constants against Officers.php / movement.php;
  the statics (51, all Drazi with a C&C); the lobby's 263 and +10; warriorHitBonus / precisionStrike / canCharge / canAnticipateSupreme; the REAL
  computeShotModifiers; the REAL movement.js (the charge row, its states, Cancel, the snap turn, its cost and affordability, the marking, agile); the REAL
  shipMovement.js icon and click; the REAL MovementPhaseStrategy pick; the REAL CnC box and SystemActivation colours.
- `enhancementsHarness` **292 / 0** (fingerprint re-recorded, additions only); `enhancementsDifferential.js` **876 / 0** (the first run caught the lobby
  missing his -1 - added to his `stats`, and the comparison moved to read time).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game holds a Drazi Warrior).
- **Mutation-tested:** the first run broke midway (the php container stopped); the whole set was rerun on 2026-10-24 with that day's follow-ups -
  **136 / 137 caught + 1 equivalent**, see "Second round" below.

**Observations, not changed:**
1. Not play-tested. Worth a test: a Drazi Sunhawk with him against two enemies - its Charge icon astern in the Movement phase, click it and an enemy that
   moved first, a snap turn to line up (1 thrust less), four hexes straight in: the Firing-phase preview shows "Charge +2" against that unit only; a called
   shot that turn shows no penalty-waiver (the charge took the turn), a called shot on a later turn shows "Precision Strike +8" once; "Supreme Anticipation!"
   in his C&C's blue box, +50 initiative next turn and -50 the turn after; a fighter flight launched from a Drazi carrier with him at +10 from the next turn.
2. Not covered by a harness: `MovementGamePhase::process`'s one-line call to `validateCharge` (the method itself is), and the in-browser drawing of the icon.
3. The charge row is public movement, so an opponent can read which unit was charged (the tabletop declares it); it also tells them he is aboard.
4. The server never checks a turn's thrust cost or delay (log-only validation), so a doctored client could take the cheaper snap turn without a charge -
   as it could already take any turn at any cost.

**Follow-ups (user, play test 4509, 2026-10-24) - built, uncommitted.**
1. **His +1 is FIRE CONTROL**, as every other "+1 to hit" officer's (Gunner, Turret Officer, the C&C Matter Weapons Expert): it did not show in a weapon's
   fire control. `applyWarrior` now raises every non-null entry of every mode of every weapon (`raiseEveryMode`, so fixed-fire-control modes are left
   alone and the weapon is sent `isModified` to every viewer, as the Gunner's are) - not the ram, not a jump drive (`JumpEngine extends Weapon`), not a
   serving Gunner's gun (his +1 replaces it). Only the charge's +2 stays a shot modifier, as it is for one unit: `warriorHitBonus` became
   `chargeHitBonus` (⚠️ MIRROR PAIR with officers.chargeHitBonus), `WARRIOR_HIT` is gone (both ends), the "Expert Warrior" breakdown line is gone (the
   +1 now sits in the Fire Control figure). Lobby: `officers.fireControlRaise` adds his +1 (not `isRammingAttack`, not `jumpEngine`; the Gunner's
   `on('OFF_GUN')` return comes first), so `displayFireControl` shows it and `pendingMoveFireControl` nets a moving Gunner to 0 on his gun. Registry
   summary and docs card now say "+1 fire control".
2. **The Charge icon** is painted red (`UI.shipMovement.chargeColour` #e8432e - through `drawUIElement`'s tint; the PNG's own colour no longer matters):
   its glow PULSES while it waits for the enemy click (`#chargecanvas.charge-picking`, a tactical.css keyframe reading `--charge-glow`), and once a
   unit is locked ('on' / 'locked') a crosshair is drawn over the art (`drawChargeReticle`, through a new optional `after` argument of
   `drawUIElement` / `drawUIimage`). 'locked' is a little fainter (0.75).
3. **A crosshair on the unit charged** while his ship is the SELECTED ship: `shipManager.movement.getChargeMarked` (Movement: the unit the charge row
   names; Firing: the unit `charged` says it holds against - a broken charge marks nothing; never a destroyed unit or another phase) ->
   `PhaseStrategy.syncChargeMarker` (one icon at a time, memo `chargeMarkedIcon`, a render kick only on a change; called from update / activate /
   setSelectedShip / deselectShip / onShipMovementChanged - deactivate clears it through its deselect) -> `ShipIcon.setChargeMarked` (sprite built on
   first use, one shared canvas texture in the Charge red with a dark halo, open centre; size max(0.9 hex, 0.85 x canvasSize); local z 115 - over the
   +100 lift, under the 120 movement-group badge; rides its own icon's lifts, a mark ON the unit). A replay selects nothing.
- Gates: officersHarness **2024 / 0** with the database (the +1 checked as fire control on the beam, the Cutter, the ram, a Peregrine's jump drive, a
  Gunner's beam, killed; the charge's +2 at another enemy); officersClientHarness **820 / 1** (the one the "(repeat)" label; new: the lobby's +1, fireControlRaise, chargeHitBonus, the
  icon's tint / pulse / crosshair, drawChargeReticle, getChargeMarked, the REAL syncChargeMarker and ShipIcon.setChargeMarked in sandboxes);
  enhancementsDifferential **876 / 0**; enhancementsHarness **292 / 0** after re-recording - only the two Sunhawk Warrior cases' `strip` lines moved
  (+280 bytes each = `fireControl` + `isModified` on its seven weapons, proved by a probe), old copy `fingerprint.before-warrior-firecontrol.txt`.
  `fvbuild -Check`: replay **162 / 0**; both legacy bundles rebuilt (`node scripts/bundle-legacy.js`), `-Sync` done.
- Mutation-tested: **18 / 18** (5 server, 12 client, 1 differential); the first pass missed one - the charge's +2 given at ANY unit while it holds
  (no server check shot another enemy) - check added. The ram mutant's first "catch" was the flaky Stage 18 rail-escape check; rerun, its own check
  fails. Runner `mutate28b.js`, session scratchpad only.
- The original Stage 28 build's mutation line was never filled - that session's server run broke midway (the php container stopped: 33 caught,
  2 missed, 35 broke; client 44/1, differential 2/2). CLOSED by the full rerun below.

**Second round (user, play test 4509, 2026-10-24) - built, uncommitted.**
1. **The Charge icon is GREEN until a unit is locked** (the art's own movement green, no tint, pulsing while it waits for the click), then red with
   the crosshair ('on' / 'locked'). The crosshair on the unit charged is see-through: `UI.shipMovement.chargeTargetOpacity` (0.6), the user's knob,
   read each time the mark is put on (`ShipIcon.CHARGE_MARK_OPACITY` retired).
2. **The snap turn, "as though it were agile"** (user: the ship turns twice in one hex, the second turn the snap, -1 thrust): it was built so, but the
   user could not find it. Two causes: (a) `isChargeSnap` asked `getLastCommitedMove`, which is the CHARGE ROW when the charge is declared between
   the turns - a turn, the charge, then a turn gave no snap (the likely play-test order), and an agile Warrior ship lost its own snap turn the same
   way; now the charge row is passed over. (b) Nothing marked it: the turn arrows are now drawn in the Charge red and read "Snap Turn Left / Right
   (Charge)" while the next turn would be the snap (`UI.shipMovement.turnArrowTint`). Still client only; still before the first step (a turn after
   it breaks the run anyway). Game 4509 was on turn 1: the Sunhawks' delay was clear, so their first turn was always free.
- Harness gaps closed while rerunning the original mutation set: the "35 degree" bow fixture (5, -4) is ALSO refused by a step no closer, so the
  arc constant was never tested alone - a 33.7-degree target (7, -5), every step closer, now is; the charge's +2 never with the ram, while it holds.
- Gates: officersHarness **2028 / 0** with the database (1946 --no-db); officersClientHarness **825 / 1** (the "(repeat)" label; new: a turn, the
  charge, then a turn - non-agile and agile -, the red labelled arrows through the REAL shipMovement.js, green until locked, the opacity knob);
  enhancementsDifferential **876 / 0**; enhancementsHarness **292 / 0**; `fvbuild -Check` replay **162 / 0**; both legacy bundles rebuilt, `-Sync` done.
- **Mutation-tested - the ORIGINAL Stage 28 set rerun in full** (its stale patterns moved to the renamed / reworked code) **plus both follow-up
  rounds: 137 mutants** (73 server, 61 client, 3 differential), ~37 minutes, every file byte-identical after. First pass 135 caught, 2 missed:
  #43 is EQUIVALENT (`anticipatorIndex`'s spent test - `spend()` repeats it, as Stage 25's speakerIndex); #69 (`Movement::isHexStep` not counting
  slips) was a gap - checks added (a slip among five steps, four of them moves; a charge declared after a slip). Three "catches" came only from the
  flaky Stage 18 rail-escape check; rerun listing every FAIL, #25 and #64 fail their own checks, #42 (`saveAnticipation`'s own-player test) did not -
  its "another player" and "gate off" refusals ran on a ship that had ALREADY spent his anticipation, so `spend()` refused first: fresh ship, plus a
  positive control on it. **Final: 136 caught + 1 equivalent.** The earlier partial run's two misses are closed too: the charge's +2 at any unit
  (first round) and the bow arc constant (the (7, -5) fixture above). Runners `mutate28full.js` (built from the original `mutate28.js` by
  `buildFull28.js`) and `recheck28.js`, session scratchpad only.

**Third round (user, 2026-10-24) - the charge's path is free; RULED, built, uncommitted.** "I'd like the snap turn to be made at any point in the ship's
movement ... a player might need to turn to move closer, and needs the snap turn later in the movement ... the 'move 4 hexes closer' and 'beats target in
initiative' are the only rules we need to follow." This REPLACES the run's shape in "Built with defaults" above (every step closer, no slip, the bow within
30 degrees at the first step, no turn or pivot once moving) and the snap turn's "before the first step".
1. **The charge holds** (`Officers::chargeRunHolds`) when, from the hex he declared in to the hex his move ends in this turn, he is at least
   `CHARGE_MIN_HEXES` (4) **nearer** the target where it ended its own move, and he beat it on initiative (unchanged). NET distance, not steps counted:
   four hexes closer and two back is 2 nearer - no (stated reading of "4 hexes closer"). Turns, pivots, slips, passing the target and where the bow points
   are all free. `CHARGE_BOW_ARC` is gone (PHP only - it had no JS twin). Unchanged: the charge is declared before the first step into a hex
   (`validateCharge` drops a later one; `chargeRunHolds` refuses one after a step), spent once declared.
2. **The snap turn** (`shipManager.movement.isChargeSnap`): the `hasSteppedThisTurn` gate is gone - one snap turn anywhere in the move, still a turn
   straight after a turn with the delay not met, still once.
3. Docs card rewritten (route free, snap turn anywhere).
4. **Net reading CONFIRMED** (user, same day): "the Charging ship closes the distance to the target by at least 4 hexes during its movement".
5. **The client offers only units he beat on initiative** (user, game 4511 turn 3: the Sunhawk at 51 could charge the Sentri at 126 and the Haven at
   120 - and take the snap turn the server would never let hold). `shipManager.movement.isChargeTarget` now asks `shipManager.hasBetterInitive`
   (ships.js - the twin of `BaseShip::hasBetterIniative`, ties to the bonus, then the higher id), and `canCharge` asks the new `hasChargeTarget`
   (any unit in `gamedata.ships` that qualifies), so with no such enemy the icon does not show at all. Client only, as asked: `validateCharge` still
   keeps a row at a unit that beat him (the server just gives no +2).
   - officersClientHarness **831 / 1** (the "(repeat)" label; the REAL hasBetterInitive lifted from ships.js; new: an enemy after him refused, the two
     tie-breaks, no icon when every enemy moves after him, an after-him enemy clicked while picking refused). Mutation **2 / 2** (no initiative test; the
     icon without a target). Legacy bundles rebuilt (the watcher missed it again), `-Sync` done.
6. **Simultaneous Movement** (user, same day): "ships can only charge units in brackets that have already moved (not ships in their own bracket)".
   It was NOT so: `iniative` is the bracket in a SimMv game, so two units of one bracket tie and `hasBetterIniative`'s tie-break (bonus, then id)
   let one "beat" the other, both ends. Now `Officers::chargeBeaten` (⚠️ MIRROR PAIR with `shipManager.movement.chargeBeaten`): with the
   `initiativeCategories` rule, a strictly LOWER bracket (`iniative >`); else `hasBetterIniative` as before (standard movement moves one unit at a
   time in that order, so its tie-break is right there). `chargeTarget` and `isChargeTarget` both ask it. Docs card says so.
   - officersHarness **2031 / 0** (lower bracket holds, own bracket with his bonus higher no, higher no; the same tie in standard movement holds);
     officersClientHarness **834 / 1** (the "(repeat)" label; own bracket refused with every tie-break his, lower yes, higher no, no icon with only
     own-bracket enemies, the same tie in standard yes). Mutation **4 / 4** (each end: the rule ignored, `>=` for `>`). Bundles rebuilt, `-Sync` done.
7. **Never a unit nearer than 4 hexes** (user, same day: "exactly 4 hexes is fine as they can move to range 0"): from 3 or nearer he could never
   close 4, yet the charge was offered, spent, and gave the snap turn. `isChargeTarget` now also needs `getShipPosition(ship).distanceTo(
   getShipPosition(target)) >= officers.CHARGE_MIN_HEXES` (new JS constant, ⚠️ MIRROR of Officers::CHARGE_MIN_HEXES, compared by the harness) -
   the target has moved (chargeBeaten), so its hex is final. Asked last, after the hidden test, so a hidden unit's position is never read. Client
   only; the server's net-4 judge already refuses such a run.
   - officersClientHarness **837 / 1** (the "(repeat)" label; the REAL getShipPosition over the REAL hexagon Offset / Cube - the harness's stub was
     always 3 away; new: exactly 4 yes, 3 no, his own hex no, 4 astern yes, measured from where he stands, no icon with only near enemies, the
     constant). Mutation **2 / 2** (no distance test; `>` for `>=`). Both legacy bundles rebuilt (the watcher missed them), `-Sync` done.
- Gates: officersHarness **2029 / 0** with the database (charge checks rewritten: past the target 5 -> 1 holds, 3 -> 1 does not; a turn / pivot / slip /
  bow pivoted away after the first step and a snap turn half-way along all hold; the 33.7-degree target holds, a 7 -> 4 one does not; four closer and
  two back no; two away then six in holds; the target that moved now starts BEHIND him, so only its end position passes); officersClientHarness **827 / 1**
  (the "(repeat)" label; new: the charge, two hexes, a turn - the snap turn at 4 thrust, marked, then none more). Legacy bundles rebuilt (the watcher
  missed `movement.js`), `-Sync` done. Mutation **5 / 5** (no step-before refusal, `>` for `>=`, the target's start position, closer steps counted
  instead of net, the old snap gate back); runner `mutate28c.js`, session scratchpad only.

### 9.36 Stage 29: Expert Ballistic Officer (2026-10-24)

**Rules text (user, 2026-10-24) - one of the original 25.** "Expert Ballistic Officer: This expert is not only skilled with the deployment of
ballistic weapons, but also in their logistics and operations as well. He provides all of the following benefits to his ship: All ballistic weapons
receive +5 to their distance range (but not their launch range). This does not apply to proximity weapons. All ballistic devices with limited
ammunition receive one extra round of ammo. For missiles, this would be an extra missile slot, for example. Devices that are self-arming, such as the
ballistic torpedo, gain no benefit. If the ammunition has a cost, it must be paid (it is not free) or no benefit is gained. All missile racks (but not
reload racks) can change one of their single-space (1 slot, e.g. not ammo like Starburst missiles which take 2 slots) missiles to any other legal type
at no cost. This can only be done before the scenario. Such missiles cannot be transferred between ships and will only function during that scenario
only. Location: C&C. Cost: 5% of the base cost of the ship, plus 1% for each missile rack that received a free missile upgrade. For example, on a
Solyrn, the cost would be 15%. Availability: Races other than the Kor-Lyans pay double the cost for this expert." The user's notes: "The free missile
choice can be that when an Expert Ballistics Officer is selected any missiles the player buys are free up to the number of missile racks on the ship.
Where the players buy more missiles than racks on the ship, the most expensive missile ammos are free. If the Expert Ballistics Officer is removed after
ammo is made free, we revert to normal prices. If the player doesn't pick enough ammo types we can randomly allocate some. Similar to an Expert
Political Officer. Because Missile launchers have a shared ammo pool in the Ship's Magazine, we should just adjust the total missiles allowed for the
ship by the number of missile racks on ship. Weapons with Ammo pools like Proximity Laser and Limpet Bore Torpedo should have their respective ammo
counts increased by 1."

**What FV had.**
- **Distance range** is tested in ONE place per class: `Weapon::isInDistanceRange` - a ballistic shot misses when the target ended its move more than
  `max(range, distanceRange)` from the launch hex, so a weapon with no distance range of its own (every torpedo) has its launch range as its distance
  range. Two classes override it (`MultiBombRack`, `MultiDefenseLauncher`, both their own `distanceRange`); a Homing Missile's fuel budget
  (`AmmoMissileHM::getFuelBudget`) is the same figure, captured at launch. The client's only use is the hit-chance preview's "Out of range"
  (`weaponManager.calculateHitChange`, `max(range, distanceRange)`) and the Range line shipSystem.js builds ("20/60"). The one proximity weapon is the
  Kor-Lyan **Proximity Laser** (`ProximityLaserNew`).
- **Ship missiles** live in ONE shared `AmmoMagazine` per hull (capacity = 20 a rack + 80 a Reload Rack: the Solyrn's 10 racks and 1 Reload Rack hold
  280 - 200 Basic, 80 Interceptor). Other types are ship-level `AMMO_*` rows (`MINE_*` for mine launchers), a per-unit price that differs by faction
  (Interceptors are free to the Kor-Lyans, Starburst 20 to them); each row's limit is the capacity over the round's size (Stealth and Homing a tenth).
  At every load the rows run in `setEnhancementsShip` and `addAmmoEntry` adds each, swapping out Basic (and, for Antifighter / Chaff, Interceptor)
  rounds once the magazine is full. The buy dialog caps the rows' total at the capacity (`confirm.getMaxAmmoFit`, Starburst and Multiwarhead 2 slots).
  The magazine's capacity and round total reach every viewer, its types the owner's team.
- **Rack kinds** (magazine-fed `AmmoMissileRackS` subclasses on officer-eligible ship hulls): Class-S/SO/L/LH/D/F/B/O/R/A racks (785), Bomb Racks (18),
  and three mine launchers (40: Ballistic, Abbai, Chouka - `BallisticMineLauncher`). The old `MissileLauncher` racks (Class-S/SO and others, 27 hulls
  have nothing else) track no ammunition in FV and fire only Basic missiles.
- **Weapons that keep their own count** (`ammunition`): Proximity Lasers (10), Limpet Bore Torpedoes (5; the base one 10), the Nexus Kinetic Box
  Launchers, Laser Missiles, Antifighter Launcher and Dart / Streak Interceptors, and the Custom Ships' light racks. Each persists its count in
  `tac_ammo` when it fires (`setAmmo` on every load), and `BuyingGamePhase` writes every one's starting count as a turn-0 row when the fleet is committed.
- **Fighters** are not his: a ship officer, and a flight's missiles ride its own magazine. FV has no ship-to-ship ammunition transfer.

**Rulings (user, 2026-10-24, asked before building):**
1. **Offered only on hulls with counted ballistic ammunition** - a magazine-fed rack or mine launcher, a Proximity Laser, a Limpet Bore or another
   ballistic weapon keeping its own count: **235 hulls of 31 factions** (not every hull with a ballistic weapon, 497). *226 after play test 4512 -
   see the follow-ups at the end of this section.*
2. **Bomb racks are missile racks** (an extra slot, a free pick, +1% on his price); **a mine launcher adds one magazine slot only** (an extra mine, paid
   as usual), no free pick and nothing on his price.
3. **When he is disabled or killed, the ammunition stays and the range goes**: the extra slots, rounds and free missiles were loaded before the
   battle; the +5 stops from the next turn, as every officer's effect.
4. **Free picks are paid single-slot rounds only, the rest filled at the commit, his price fixed**: never Basic, never a round that costs nothing on
   that hull (the Kor-Lyans' Interceptors), never Starburst or Multiwarhead; the dearest N bought are free; picks left unused are rolled once as the
   fleet is committed (the Political Officer's way), within each type's limit; his price is always 5% + 1% per rack, doubled outside the Kor-Lyans.

**Built with defaults, stated here (ask before changing):**
- **N (his free picks and his price's racks)** = the hull's missile racks and Bomb Racks - but 0 on a hull offering no paid single-slot round (9 of the
  175 rack hulls: the Hermes fast variants, the Hyperion Aegis, three Chouka hulls, the Dag'Kur, the Shlassan Triumvirate), where no rack can receive
  one: 5% there. Price 5% + N%, the Kor-Lyans ('Kor-Lyan Kingdoms'); everyone else twice that; rounded up as every ship officer (Solyrn 98 of 650, an
  EA Orion 1,050 of 3,500).
- **+5 distance range** on every ballistic weapon of his ship but a Proximity Laser (never the Jump Engine or the ram): `max(launch, distance) + 5` - so a
  torpedo 50 becomes 50/55 - and a Homing Missile's fuel; a base's stabilised rack (launch = distance) only its distance; the launch range never.
  While he serves. Shown to every viewer (the Range line), as every displayed stat.
- **Extra slots**: +1 capacity per missile rack, Bomb Rack and mine launcher, on the magazine that feeds it (Solyrn 280 -> 290). Bought rounds fill the
  empty slots before they swap anything out, so N free picks land in the new slots - the same magazine the tabletop gives (an extra slot holding a
  standard round, and N standard rounds swapped free). The slots hold what is bought - nothing is added to the hull's standard load.
- **Extra round**: every ballistic weapon of his ship keeping its own count: +1, written once as its starting count at the commit (FV prices none of
  them). Nothing on the old untracked racks.
- **The dearest N**: every round bought on a paid single-slot row is priced at its own level (price + level x step - the Light Missile row steps); the
  N dearest are free, a tie to the lower id (the dialog and the fleet list hold their rows in different orders; a tie only moves which row shows the
  saving, never the total). The commit's fill picks each missing round at random among the hull's paid single-slot types still under their offer's
  limit, as ordinary rows, at no cost.
- "Cannot be transferred between ships": FV has no such transfer - nothing to stop.

**Design.**
- `Officers`: registry `OFF_BALL` (post `cnc`, pct 10, `perPick` 20 - his pct grows by a fifth for each free pick, so 10 -> 30 with 10 racks and the
  Kor-Lyans' 5 -> 15; `needs` hasCountedBallisticAmmo, no `apply`) after the Warrior, before the placed officers; `$factionRules` pct 'Kor-Lyan Kingdoms'
  5; constants BALLISTIC_RANGE 5, BALLISTIC_EXTRA_ROUND 1; `$ballisticAmmo` (AMMO_ id => round class). `ballisticAboard` (rows), `ballisticPicks` (N),
  `ballisticSlots`, `paidPickTypes`, `ballisticPicksUsed`, `raiseMagazines` (after pickPosts, before the rows), `placeBallisticPicks` (the commit: the
  fill and the extra rounds), ⭐ `distanceRangeBonus` (⚠️ MIRROR PAIR with officers.distanceRangeBonus), `blueprintAnnotations` (the lobby's
  `ballisticPicks` / `ballisticSlots`, on the hulls offered him only).
- `AmmoMagazine::addCapacity` (protected `$capacityAdded`, idempotent); `AmmoMissileRackS::getAmmoMagazine` public. Hooks: `BaseShip::onConstructed`,
  `Weapon::isInDistanceRange`, `AmmoMissileHM::getFuelBudget`, `BuyingGamePhase` (after placePoliticalPicks), `ShipCompactor::compactShipObject`,
  `Officers::addForJson` (`distanceRangeBonus` while he serves, to every viewer). `MultiBombRack` and `MultiDefenseLauncher` keep their own distance test
  WITHOUT the +5: no hull mounts either (measured), so the edit was made and then withdrawn - add the term there if a hull ever gets one.
- Client (`officers.js`): the registry row; `distanceRangeBonus` (weaponManager's "Out of range", the Range line); ⭐ `ballisticFreeRounds` (⚠️ MIRROR
  of the server's pick test and the dearest-N rule), `ballisticDiscount`; `shipRowCost` and `confirm.enhRowCost` take the free rounds off;
  `confirm.getMaxAmmoFit` adds his slots; `setShipLevelRow` re-prices the ammunition when he comes or goes; `adjustDataDisplay` - the Range line, and in
  the lobby the magazine's total and a counted weapon's Ammunition line.
- Docs: an Expert Ballistic Officer card (Ship Officers, alphabetically after the Anticipator).

**Files.** Server: `Officers.php`, `ShipClasses.php` (onConstructed), `systems/baseSystems.php` (AmmoMagazine::addCapacity, AmmoMissileHM::getFuelBudget),
`weapons/missile.php` (getAmmoMagazine public), `weapons/weapon.php` (isInDistanceRange), `Phase/BuyingGamePhase.php`, `lib/ShipCompactor.php`. Client:
`officers.js`, `UI/confirm.js`, `weaponManager.js`. Docs `ammo-options.html`. Harnesses: `officersHarness.php`, `officersClientHarness.js`, the
enhancements fingerprint. Statics (`fvbuild -Statics`) and both legacy bundles (`scripts/bundle-legacy.js` - no watcher was running) rebuilt; no React
source, no new class, no schema change.

**Measured.** 235 offers in 31 factions (Kor-Lyan 22, Orieni 24, EA 15 + 13 Early + 1 defences, ...); 180 of them have exactly one magazine, 55 none
(counted weapons only) - no offered hull has two, so the buy dialog's one-magazine cap holds. Prices: Solyrn 98 (15%), Kolosk 111 (13%), Taloki 875
(25%), Trylkan 48 (9%: 4 racks, its 4 mine launchers slots only), Fenja 15 (5%, Limpet Bores only), EA Orion 1,050 (30%), Hyperion Aegis 80 (10%: two
racks but no paid single-slot missile, so no pick), the Abbai WotCR Motenai 63 (mine launchers only). Statics +21.7 KB raw (235 offers + 235
`ballisticPicks` / `ballisticSlots` pairs). Fingerprint: exactly two new lines (the two harness hulls offered him, AthraskalaAM 156 and one at 75),
proved additions-only; old copy `fingerprint.before-officers-stage29.txt`.

**Verified.**
- `officersHarness.php` **2094 / 0 with the database** (2 registry-order checks moved: the Troop Leader's and the Warrior's): the registry place; offers
  and prices on nine hulls (the rules' Solyrn, the doubled EA Orion, the Taloki, the Kolosk's Proximity Lasers adding nothing, the Trylkan's mine
  launchers slots only, the Koloskova, the Limpet-only Fenja, the pick-less Hyperion Aegis, the Abbai mine-launcher hull) with rowPrice and the blueprint
  fields; none on old untracked racks (KobolAdamant), Ion Torpedoes (TraqintorNew), a Grome railgun magazine or an Omega; a sweep of every hull - offered
  on exactly the 235, and the regenerated statics carrying the fields on exactly those; the REAL `ShipCompactor::compactShipObject`; the C&C post; a
  doctored row. The magazine through the real construction: 290 / 280, both holding 280; bought rounds into his slots first (10 Heavy: no Basic swapped;
  15: 5 swapped; 12 Antifighter: 2 Interceptors, the Antifighter rule), without him swapped as ever; addCapacity idempotent; the slots kept when he is
  out of action; the Trylkan +8; a doctored Grome row nothing; an enemy sent 290 and "280/290", not the types. The +5 through the real
  `isInDistanceRange`: Class-L 75 (76 out) with him, 70 without; Class-D 65; the Limpet 35; the Proximity Laser nothing while the same ship's rack gets
  it; a direct-fire weapon nothing; none from the turn after his C&C fell, still on the turn it falls; the gate; a Homing Missile's fuel +5;
  `distanceRangeBonus` published to an enemy, none from a ship without him or once he is out. The commit: unpicked picks rolled onto the rows (forced
  dice, each type named), none past N, never counting Interceptors at 0, Starburst, Multiwarhead or a doctored Basic row; a type at its limit passed
  over (a 30-round magazine's Stealth and Homing 3); a missing row appended as the server's offer; nothing without him or with his C&C destroyed
  before battle; the Limpets 5 -> 6 and the Proximity Lasers 10 -> 11, the Grappling Claws and racks untouched; the count never re-added at a load,
  and a hull fired empty still keeping him.
- `officersClientHarness.js` **862 / 0** (the two registry checks moved; the Stage 28 "Anticipation!" and Stage 26 "(repeat)" checks brought up to the
  labels the user committed - they had gone stale, not broken): the registry row; every static - offers, blueprint fields and `passesNeeds`
  'countedBallistic' agree on all 235; `isBallisticPick`; the dearest-N table (a stepped Light Missile row, a tie to the lower id, fewer paid rounds
  than picks); the fleet list's lines; `setShipLevelRow` both ways; a C&C destroyed before battle refunding him and re-pricing the missiles; the buy
  dialog's spinners through the REAL `confirm.enhRowCost` and its room through the REAL `confirm.getMaxAmmoFit` (290 / 280); `distanceRangeBonus` in
  the lobby and in game; the Range, Ammunition and magazine lines; the REAL `weaponManager.calculateHitChange`'s "Out of range" (75 in, 76 out).
- `enhancementsHarness` **293 / 0** (re-recorded); `enhancementsDifferential.js` **876 / 0** (no case added: he moves no lobby stat it compares).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game has him). `-Sync` done.
- **Mutation-tested: 52 breaks (30 server, 22 client), all caught**, files byte-identical afterwards (runner `mutate29.js`, session scratchpad only;
  ~32 s a server mutant, ~4 s a client one). The first pass missed one: dropping the ballistic test from `keepsOwnCount` is equivalent on every hull
  today (a Grappling Claw's CLASS count is 0, its rounds come from its constructor), now pinned with a probe class whose class count is 4.

**Observations, not changed:**
1. Not play-tested. Worth a test: a Kor-Lyan Solyrn with him - the buy dialog asks 98 for him, 3 Heavy bought read "free", 290 rounds may be bought;
   its magazine tooltip "280/290"; after the commit, in game, the magazine 290 with the 3 Heavy and 7 more at random, every rack's Range "x/y+5"; a
   Fenja with him - each Limpet Bore "Ammunition: 6" in game.
2. Not covered by a harness: `BuyingGamePhase`'s one-line call to `placeBallisticPicks` (the method is; the phase needs a whole game in the database).
3. ~~Each AMMO row's own limit is the hull's offer~~ - re-ruled after play test 4512, below.
4. Unticking him in the buy dialog does not take back rounds bought into his slots; the server's magazine caps them at load, as it always has.

**Follow-ups after play test 4512 (user, 2026-10-24).** The test game read back correctly through the real load: the Solyrn's magazine 290 (200
Basic, 80 Interceptor, its 10 bought rounds in his slots), the Apollo's 248 (240 + 8), every rack and both of the Fenja's Limpet Bores +5 and published
to the opponent (the officer list and the magazine's types only to their own side), the Limpets' turn-0 rows 6 and the Grappling Claws' 4, the stored
costs 98 / 15 / 234 (his price only - every bought missile within the picks free). Then two rulings and one acceptance:
1. **"Don't offer Ballistic Officer to hulls without Ordnance to purchase"** - read as: a magazine-fed hull is offered him only when its magazine has
   ordnance to buy (`hasMagazineOrdnance`: a magazine, and an AMMO_ / MINE_ / SHELL_ id enabled - `Enhancements::isAmmoEnhancement`, now public),
   since the slots could hold nothing otherwise. That drops exactly the 9 rack hulls with no ordnance at all (the Hyperion Aegis, the three fast
   Hermes, three Chouka hulls, the Dag'Kur, the Shlassan Triumvirate): **226 offers**. The mine-layers (Abbai, Chouka) keep him - they have mines
   to buy - and so do the 55 hulls whose counted weapons keep their own rounds (Fenja, the Nexus launchers...), which have no magazine at all: their
   benefit is the extra round, which needs no purchase. **CONFIRMED by the user:** ships like the Fenja, whose weapons keep their own ammunition,
   are offered him.
   Client mirror `officers.hasMagazineOrdnance` (the blueprint's ordnance offers), in `passesNeeds` 'countedBallistic'.
2. **"We should be able to purchase up to 290. The per-type limit is simply derived from the magazine total"** - each ordnance row's limit is the
   offer's own rule on his enlarged magazine: floor((capacity + slots) / size), a tenth for Stealth and Homing (a Solyrn's Heavy 290, Stealth 29,
   Starburst 145). Server: the commit's fill reads its limits off offers made on a blueprint whose magazine has his slots. Client:
   `officers.ballisticRowLimit` (⚠️ MIRROR PAIR with setEnhancementOptionsShip's ammunition limits), asked by `confirm.enhRowMax` for an AMMO_ / MINE_
   row while he is ticked in the dialog (the + button, the typed box and the wheel all ask enhRowMax). The static offers keep the hull's own limits.
3. The tie rule (two equal-priced missiles: the free one goes to the lower id) stands as built.
4. (user request, same day) The buy dialog's ordnance rows' note - "up to 280, 4pts each" - follows the row's limit: `confirm.paintOrdnanceNote`,
   from every `getTotalCost`, writes `enhancementPriceNote` again with `enhRowMax` (the arguments it was first written from kept on the spinner as
   `noteArgs`), so ticking him reads "up to 290", unticking "up to 280". Client 865 / 0.
- Verified: officersHarness **2101 / 0 with the database** (new: the no-ordnance hulls refused, two probe hulls - racks with only a non-ordnance option,
  and a zero-capacity magazine with its ammunition enabled - the 226 sweep, the statics, the enlarged limits, the fill stopping Stealth and Homing at 4
  of a 40-slot magazine, an appended row's 290; it writes `ballisticLimitsFixture.json`); officersClientHarness **864 / 0** (the client limit rule
  against the server's for all 1,048 ordnance rows of the 226 hulls; `enhRowMax` 290 / 29 / 145 ticked, 280 / 28 / 140 not; `passesNeeds` refusing the
  Hyperion Aegis, keeping the Abbai mine-layer); enhancements 293 / 0 (fingerprint unchanged - no fingerprint hull was among the 9); differential 876 / 0;
  `-Check` replay 162 / 0; statics regenerated; legacy bundles rebuilt; `-Sync` done. Mutation **12 / 12** (`mutate29b.js`); two were equivalent on
  every hull today and are pinned by the probe hulls. ⚠️ Lesson: a server mutant run rewrote the client's fixture file, so every client mutant run
  after it "failed" on the fixture - regenerate a harness-written fixture with a clean run before client mutants.

### 9.37 Stage 30: Expert Quartermaster (2026-10-24)

**Rules text (user, 2026-10-24) - one of the original 25.** "Expert Quartermaster: All Hurr quartermasters are experts in obtaining hard to find spares
and other items for their ships ... ONE of the following ship enhancements (player's choice) will be present on his ship at no extra cost: Gunsight (any
two weapons), Advanced Defensive Targeting (any two weapons), Improved Reactor (but if the reactor is in danger of exploding, triple its chance), Improved
Thrust Rating (any two thrusters), Hardened Armor (on any two non-structure and non-primary systems on the same shipside), or Jump Accelerator. Each of the
ship's class-S or better missile racks comes with two special missiles of any type normally available to the Hurr, at no extra cost. If the racks are of
the class-SO or an older class, they receive only one free special missile. Due to the availability of repair parts and system patches, the ship may
ignore one critical hit it receives during a scenario. The player must make the choice immediately after the critical roll ... Unfortunately, the
Quartermaster is sometimes too good ... any vessel with an Expert Quartermaster will suffer a permanent -6 to its initiative roll. In addition, the
navigation crew's response times are slowed, resulting in a +10% penalty (round fractions up) to its Turn Delay. Finally ... there is a +2 penalty on all
critical hits against the C&C. Location: C&C. Cost: 10% of the ship's base value for Hurr. Availability: Double the cost on non-Hurr ships." The user's
notes: the player chooses the free refit by buying it, "make the most expensive of the possible options free", one allocated at random if none is
chosen (the Political / Ballistic Officers' way); the missiles likewise, free up to his allowance, the rest random; "remove choice of crit to repair if
it adds to difficulty, first crit ship suffers is fine".

**What FV had.**
- The six refits: SYS_GSGT (one to a weapon), SYS_ADT and SYS_THR (several levels a system, each dearer), SYS_HARM (one to a system) - per-system rows,
  priced by the server's sanitiser (`sanitiseSystemEnhancements`, at buy, save and reload) and in the lobby by `systemEnhancements.js`; IMPR_REA (the
  biggest reactor, +1 power per size class) and JUMP_ACC (10% of the hull, a drive that opens jump points) - ship-level rows, whose prices sit in
  `pointCostEnh`, a lump the server trusts.
- A reactor's Containment Breach explodes on a d100 at or under its total damage (`Reactor::executeContainmentBreach`).
- A turn's delay is ceil(speed x rate) (`Movement::calculateTurndelay`; client `calculateTurndelay` / `calculateTurndelayAtMove` and two readouts).
- The Hurr Republic: 14 ships with a C&C (two Roskor bases), racks Class-S and Class-SO only, missiles Long Range, Heavy, Flash and Antifighter. The
  Gorek in the hurr folder is a Civilians hull.

**Rulings (user, 2026-10-24, asked before building - all as recommended):**
1. **Each turn's delay +10%, rounded up** (+1 on a delay of 1-10, +2 on 11-20), on the delay speed and rate give, before overthrust and Elite / Poor Crew.
2. **Everything stays when he is lost** - the refit, the missiles, an unused patch and all three penalties ("permanent").
3. **Any paid missile type the hull is offered** may be free, a two-slot round counting as one missile.
4. **The commit's random refit only among the four with no drawback** - Gunsights, Advanced Defensive Targeting, Improved Thrust, Hardened Armour - never
   the Improved Reactor (his tripled breach) or the Jump Accelerator (it takes reactor power).

**Built with defaults, stated here (ask before changing):**
- Offered on every hull with a C&C that may carry officers (1,812 hulls), 10% for 'Hurr Republic', 20% for everyone else (`$factionRules`).
- **What each refit is worth** (the free one is the dearest, a tie to the first in the rules' order): Gunsights / ADT / Thrust - the two dearest systems
  bought it, each at its dearest level (one level on each of two systems is his; a second level on one system is paid); Hardened Armour - the dearest
  two on one side (a tie to the lower location), never Structure or a Primary system; the Improved Reactor / Jump Accelerator - the hull's own offer
  (off a fresh blueprint - a POST-side ship carries the client's row prices). The worth is what the player BOUGHT, not what the option could be.
- **What the commit fills**: none of the six bought - one of the four at random (d4 over those the hull can take), on two random systems (a Hardened
  Armour pair on one random side with room for both); a refit bought on one system - a second at random (on that side). Never a system destroyed
  before battle or already holding it. A bought reactor or accelerator is whole.
- **Free missiles**: 2 per magazine-fed rack and Bomb Rack, 1 per Class-SO (FV's older racks keep no count); mine launchers and Reload Racks none; the
  dearest that many rounds bought free, the rest rolled at the commit. With an Expert Ballistic Officer aboard, HIS free rounds are taken first (his are
  single-slot only, so taking his first never costs the player) and the Quartermaster's count past them.
- With an Expert Political Officer aboard (a Centauri capital ship): the Quartermaster's free Hardened Armour first, her five among the rest.
- **The patch**: the first critical the ship's critical ROLLS give it (`testCritical` - every critical of that one roll), automatic, once per battle,
  stored as one OffUsed note `OFF_QM:patch` on his C&C. Not what a weapon does directly (an EM shutdown) or a magazine explosion. Shown to his team as
  "(used: critical patch, turn N)" - after his loss too (`stock`).
- The tripled breach only on the reactor IMPR_REA improves, and only while the Improved Reactor IS his free refit; at most 100%.
- The +2 on his own C&C (a hull with two: his), his kill roll there included. The -30 and the turn delay are published to every viewer.

**Design.**
- `Officers`: registry `OFF_QM` (post `cnc`, pct 20, `apply` = `lost` = applyQuartermaster, `publish` = `lostPublish` iniativebonus, new key `stock`)
  after the Ballistic Officer, before the placed officers; constants QM_INITIATIVE -30, QM_TURN_DELAY_PCT 10, QM_CNC_CRIT_MOD 2, QM_REFIT_SYSTEMS 2,
  QM_BREACH_FACTOR 3, QM_RACK_MISSILES 2, QM_OLD_RACK_MISSILES 1; `$quartermasterRefits` (⚠️ MIRROR PAIR with officers.QM_REFITS), `$quartermasterRandom`.
  `quartermasterAboard` (rows), `quartermasterIndex` (serving or lost in battle - never out 0), `applyQuartermaster`, ⭐ `turnDelayPenalty` (⚠️ MIRROR
  PAIR with movement.js getQuartermasterTurnDelay), `patchCritical`, `breachChance`, ⭐ `quartermasterRefit` / `quartermasterPair` (⚠️ MIRROR PAIR with
  officers.quartermasterRefit), `quartermasterOfferPrice`, ⭐ `priceFreeRefits` (replaces pricePoliticalArmour: his free refit, then her armour),
  `placeQuartermasterPicks`, `quartermasterPickTypes`, `quartermasterPicks`, `placeQuartermasterMissiles`; `blueprintAnnotations` adds
  `quartermasterPicks` (hulls offered him with picks only); `addForJson` publishes `turnDelayPenalty`. `resolveUses` keeps a `stock` officer's use made
  after his loss. `placePoliticalPicks` no longer counts his free armour as one of her five (found by the harness).
- Hooks: `Enhancements::sanitiseSystemEnhancements` -> priceFreeRefits; `BuyingGamePhase` placeQuartermasterPicks (before placePoliticalPicks) and
  placeQuartermasterMissiles (after placeBallisticPicks); `ShipSystem::testCritical` patchCritical before a roll's criticals are added;
  `Reactor::executeContainmentBreach` breachChance; `Movement::calculateTurndelay` turnDelayPenalty after ceil(speed x rate).
- Client (`officers.js`): the registry row (`stats` iniativebonus -30, `stock`); QM_REFITS / QM_SHIP_REFITS; `quartermasterRefit` (`anyPost` for the
  ship-level saving), `quartermasterPair`, `quartermasterSpared`, `quartermasterShipSaving`, ⭐ `settleQuartermaster` (after a refit write: the ship-level
  bucket moves by what the reactor / accelerator saving changed), `quartermasterFreeRounds` (after the Ballistic Officer's), `quartermasterDiscount`,
  `refitTryCost` (the system menu's next-level price: the real write tried and put back), `decorateQuartermasterRow`, `quartermasterStatus`;
  `priceFreeRefits` (renamed from pricePoliticalArmour - and it now re-prices EVERY row of the four kinds, so one that stopped being free is paid again),
  `armourCandidates` skips his rows, `shipRowCost`, `setShipLevelRow`, `stateText`, `summaryOf`, ABILITY_NAMES `patch`. `systemEnhancements.js`: set() and
  dropDestroyed settle the ship-level bucket. `confirm.js`: `isFreeQuartermasterRow`, `quartermasterSaved`, `dialogShip` (the edited unit, whose refits
  count). `movement.js` `getQuartermasterTurnDelay` in calculateTurndelay / calculateTurndelayAtMove; `ShipTooltip.js` and `ShipNotesPanel.js` (React)
  readouts. Docs: an Expert Quartermaster card (Ship Officers, after the Political Officer).

**Files.** Server: `Officers.php`, `Enhancements.php`, `Phase/BuyingGamePhase.php`, `systems/ShipSystem.php`, `systems/baseSystems.php` (Reactor),
`handlers/movement.php`. Client: `officers.js`, `systemEnhancements.js`, `UI/confirm.js`, `movement.js`, `UI/ShipTooltip.js`,
`UI/reactJs/shipWindow/ShipNotesPanel.js`. Docs `ammo-options.html`. Harnesses: `officersHarness.php` (writes `quartermasterFixture.json`),
`officersClientHarness.js`, `enhancementsHarness.php` (cases `qm`, `qm+helm+elite1`) and its fingerprint. Statics and all bundles rebuilt; no new class, no
schema change.

**Measured.** 1,812 offers (statics count 1,815 lines - three hulls sit in two factions' files); 166 hulls carry `quartermasterPicks` (Boroca 4, its Early
twin 2, the Roskor bases 16 / 32, a Solyrn 20). Prices: Boroca 50, Roskor 194, Primus 166, Gorek 28, Solyrn 130. The Boroca's options: two Gunsights 24,
two ADT 32, two Thrust 32, a Hardened Armour pair 68, Improved Reactor 30, Jump Accelerator 50. Fingerprint: additions only (the 19 fingerprint hulls'
offer lines, then the two new cases' blocks); old copy `fingerprint.before-officers-stage30.txt`.

**Verified.**
- `officersHarness.php` **2191 / 0 with the database** (2106 --no-db; the three registry-order checks moved): ids and notes; the registry place; prices and
  picks on ten hulls (Hurr, Civilians, Centauri, Kor-Lyan, the Trylkan's mine launchers none, the Dagkur's Bomb Racks without ordnance none) and the
  Athraskala's Bomb Racks counted; none on an OSAT, a mine or a flight; the sweep (1,812 offered, 166 blueprints with picks, each its own count); the REAL
  ShipCompactor; the post; a doctored row. The free refit through the REAL sanitiser: the reactor over two Gunsights, an armour pair over the reactor, two
  sides one free, three Gunsights two free, the ADT / Thrust tie, a two-level ADT, the accelerator, Primary and Structure never his, none without him or
  with his C&C destroyed, the Primus with both officers (7 of 8 free); the 13-case fixture. The commit: each d4 face, a base without thrusters, half-bought
  Gunsights and armour, the reactor whole, none without him or with his C&C destroyed, a destroyed weapon skipped, both officers on a Primus (7 systems).
  The missiles: 4 / 1 more / none / 2 for Class-SO, none without him, his C&C destroyed or no racks, the Solyrn with the Ballistic Officer (30, and with 12
  bought), a Starburst counted as one. The penalties: -30, +2, the REAL calculateTurndelay (6 -> 7, 11 -> 13), the 1-25 table, published to an enemy; lost -
  all kept; pre-battle - none. The patch through the REAL testCritical: first patched (one note on his C&C, the list spent), the second added, a plain
  ship's added, a no-critical roll spending nothing, an earlier use, lost (still patches, and a use after his loss read back), pre-battle none, the gate
  off; and the REAL Criticals::setCriticals. The breach: 10 -> 30, none without the free reactor, the armour's refit 10, capped at 100, lost 30. A saved fleet
  through the REAL submitSavedFleet / loadSavedFleet (his 50 stored; on reload his armour pair free, the third paid).
- `officersClientHarness.js` **891 / 0**: the registry row and QM_REFITS; the statics (1,812 offers at 10% / 20%, 166 with picks); the fixture's 13 cases
  replayed through the REAL systemEnhancements.totalCost and quartermasterRefit (the client's own prices); the free missiles (the Ballistic Officer first);
  the fleet list; the REAL systemEnhancements.set moving the ship-level bucket when the armour outbids the reactor, and back; the system menu's next price
  (30 for a 40 armour) leaving the ship untouched; setShipLevelRow both ways; dropDestroyedPosts; the buy dialog through the REAL confirm.enhRowCost (new
  and edited ship); his summary, stateText, the initiative preview; the REAL movement.js turn delay.
- `enhancementsHarness` **294 / 0** (re-recorded twice, additions only); `enhancementsDifferential.js` **914 / 0** (38 new cases: his -30 compared lobby
  against server).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game has him).
- **Mutation-tested: 53 breaks (31 server, 22 client), all caught after the follow-up checks**, files byte-identical afterwards (runner `mutate30.js`,
  session scratchpad only; client mutants run first, then server, then a clean server run to rewrite `quartermasterFixture.json`). The first pass missed
  four, each a missing or unsafe check, now fixed: his summary's hookup in summaryOf; the commit's fill never landing on a round free on that hull (the
  Kor-Lyans' Interceptors - a check whose dice reach every type); a registry check that read `lost` without `?? null`, so the mutant crashed the harness
  instead of failing a check; and a reactor price hard-coded at 30, equivalent on the Boroca (whose offer is 30) - now pinned on a Roskor base (40) and
  the Boroca's Jump Accelerator (50).
- Two bugs found by the harnesses on the way, fixed: `placePoliticalPicks` counted his free Hardened Armour as two of her five (she placed three); the
  client's re-pricing skipped a refit that had STOPPED being free (it kept its 0), and the ship-level saving read the C&C after the pre-battle editor had
  marked it destroyed (the refund lost the reactor's 30).

**Observations, not changed:**
1. Not play-tested. Worth a test: a Hurr Boroca with him - the buy dialog asks 50; buy the Improved Reactor and it reads "free"; then two Hardened Armour on
   one side in the system menu (the first quotes +30: it goes free and the reactor is paid); after the commit, 4 free missiles. A second Boroca with him
   and nothing else: two random refits of one of the four after the commit. In game: initiative -30, a turn at speed 4 (rate 1.5) asking a delay of 7,
   and the first critical roll that should land a critical landing none (his tooltip: "(used: critical patch, turn N)").
2. Not covered by a harness: BuyingGamePhase's two one-line hooks (the methods are covered). ShipTooltip.js's readout (ShipNotesPanel's too) is a
   one-line wrap of the tested helper.
3. A refit dropped by pre-battle damage that flips his free option names only the refit's own row in the refund warning; the ship-level move is still
   applied (settleQuartermaster).

### 9.38 Stage 31: Expert Graviton Controller (2026-10-25)

**Rules text (user, 2026-10-25) - an officer the user added.** "Expert Graviton Controller: Brakiri research in the area of gravities has produced a number of
experts in the field. These can provide special abilities to the ship's weapons and drive systems, as follows: All gravitic beam weapons score +1 damage. All
graviton pulsars or gravitic bolts roll to hit normally, but after they hit, they determine their result as though the to-hit die roll was 5% lower. For
example, if a graviton pulsar fired needing a 50 to hit and rolled a 35, this would be treated as a 30, thus resulting in +1 to the pulse volley count roll.
Tweaking of the Engine improved its thrust output by 1. Increase the thrust rating of the largest aft thruster by 1 as well (if equal ratings pick a random
one). Once during the scenario, during the Movement Step, the gravitic drive can be "rotated." This is generally a desperation move, as it is completely
uncontrollable. Roll 1 d6 and rotate the ship to face the resulting direction (with "1" indicating the east-facing of the map (heading 0 in FV terms), and
other numbers rotating clockwise from there). Heading, turn status, and the like are unaffected. On the turn a rotation is done, the ship's weapons suffer a
-3 penalty to all weapons fire, even if the ship winds up facing the same way it was originally heading. Location: Engine. Cost: 10% of the ship's base cost
for Brakiri. Availability: Non-Brakiri races which use gravitic drives or weapons can purchase one for 50% higher than the listed cost." The user's notes: the
rotation gets "its own icon similar to Contraction and Expert Warrior's 'Charge' icon" (`img/rotateIcon.png`, recoloured to the yellow of
`graviticTurnLeft1.png`); "when clicked, the random rotation move is done and it cannot be cancelled and re-done (to prevent exploiting). And should be saved
so that on page reload it remains, like we do with ships moving in Minesweeping Mode."

**What FV had.**
- **Gravitic drive** = the hull's `$gravitic` flag (BaseShip; the tooltip's "Gravitic Drive" note). **Gravitic weapons** = `weaponClass` (or a mode's
  `weaponClassArray`) 'Gravitic': on ships the Graviton Pulsar (Pulse, +1 pulse / 20%), Gravitic Bolt (Standard, fixed 9/12/15), Graviton Beam, Gravitic Lance
  (Lance / Beams / Split Beams) and Gravitic Cutter (all Raking), Gravitic Cannon (Standard), Gravitic Shifter, Gravity Net, Gravitic Mine and others.
- 43 Brakiri hulls can take him (the bases have no Engine); the Antoph Early has gravitic weapons but no gravitic drive. Elsewhere in B5 settings, 136 more
  hulls with an Engine carry FV's gravitic drive or a gravitic weapon (Minbari Federation 30 and Protectorate 13 - the Rogata in both -, Vree 16, Drakh 15,
  Hyach 14, Ipsha 13, Yolu 12, Streib 5, Civilians 8, Raiders 8, Custom Ships 3); FV also flags 59 Star Trek hulls and one BSG hull gravitic, and two
  Escalation Wars Chouka hulls mount a Gravitic Tracting Rod.
- **Pulses** = `Pulse::rollPulses($turn, $needed, $rolled)` in `Weapon::fire` - base pulses + floor((needed - rolled) / grouping), capped at maxpulses. A
  Standard weapon's roll decides only hit or miss, so the "5% lower" clause changes nothing for the Gravitic Bolt (fixed damage).
- **Manoeuvre penalties** = `Weapon::calculateHitBase`: -3 (d20 units, -15%) for a ship that pivoted this turn (`Movement::hasPivoted`), ballistics included,
  -3 for rolling - they stack; client twin `weaponManager.computeShotModifiers` ("Pivot for Shooting").
- **Thrust**: an Engine's `output` is its thrust; a Thruster's `output` its rating, `direction` 2 = main (aft). Both are sent to every viewer.
- **Locked moves**: Minesweeping Mode's lock (`tac_minesweep`: the rows plotted up to the swept hexes, restored on reload through `window.fvMineSweep`, the
  commit refused unless it passes the swept hexes). `tac_systemdata` is FV's generic per-unit, per-turn key/value store (weapon loading): rows of `gameid,
  shipid, systemid, subsystem, turn, data` (TEXT), the latest at or before the loaded turn handed to `$system->setSystemData` - a row whose system id is
  not a system is skipped - and already cleared by `deleteGames` and `leaveSlot`. Its existing writer binds `subsystem` as an integer.
- ⚠️ **Base rotation owns the `rotate` names**: movement.js `canRotate` / `doRotate` / `pickRotation` (a base's turn-1 rotation, rows `rotateLeft` /
  `rotateRight`), `ShipMovementCallbacks.rotateCallback(e, right)` and `UI.shipMovement.rotateCallback` (the `#rotateleft` / `#rotateright` icons).

**Rulings (user, 2026-10-25, asked before building):**
1. **Availability: per hull, B5 settings only.** Any officer-eligible hull with FV's gravitic drive or a gravitic-class weapon (and an Engine, his post);
   not the factions Fleet Selection files under the custom sub-groups Nexus, Escalation Wars and Other Universe (Star Trek, BSG, Star Wars...). Brakiri 10%,
   everyone else 15%.
2. **"Gravitic beam weapons" = every Raking gravitic weapon**: the Graviton Beam, the Gravitic Lance (all three modes) and the Gravitic Cutter.
3. **The rotation may be made at any time in the ship's move, and the moves before it lock with it** - Minesweeping Mode's way: Cancel cannot take any of
   them back, a reload puts them back, and the commit must keep them. Stored in the EXISTING `tac_systemdata` as a generic **movement lock** (user: "is
   there a way to use an existing db table, or make the new db table have wider applications") - no new table, no deploy SQL.
4. **The Engine's and aft thruster's +1 stay when he is lost** (a tweak made before battle); the weapon bonuses and the rotation need him serving.
5. **Equal aft thrusters: the first of them** (the lowest id) - no randomness, so the lobby shows exactly which.

**Built with defaults, stated here (ask before changing):**
- **+1 damage**: on every hit a Raking gravitic mode scores (a raking shot's total, before it rakes; each of the Lance's two Beams), never a hit rolling 0,
  in `Weapon::getFinalDamage` beside the other officers' flat bonuses.
- **Pulses**: the roll is taken 5 lower (d100) for the pulse count of a gravitic weapon firing pulses (the Graviton Pulsar) once it has hit - never to
  decide the hit. Interception and the hit itself are untouched.
- **The thruster**: the aft (main) thruster with the highest rating as the ship stands at the load (refits and crew included), the first on a tie.
- **The rotation**: his ship needs FV's gravitic drive (the Antoph Early and the non-Brakiri hulls with gravitic weapons only never rotate); he must serve
  that turn; once per battle, spent the moment the die is rolled; not while attached to another unit (detaching that turn is fine), after a jump-out order,
  while thrust is being assigned, or once the ship has committed. The new facing is d6 - 1 (1 = facing 0, east; clockwise), set by one `gravRotate` row (no
  thrust; hex, heading and speed unchanged; value = the die). It is ordinary movement, so every viewer sees it (and so learns he is aboard), as the Expert
  Warrior's charge. A passive notice tells the player the die.
- **-3 (-15%)** to hit with every weapon of his ship on the turn of a rotation, ballistics included and stacking with the pivot and roll penalties, as
  those do; never on an interception (no to-hit roll). The penalty follows the row, so it holds even if he is killed later that turn.
- No confirm before the roll: a click rolls (the user's note).

**Design.**
- `Officers`: registry `OFF_GRAV` (post `engine`, pct 15, `apply` = `lost` = applyGravitonTweaks, `needs` usesGravitics) after the Quartermaster, before the
  placed officers; `$factionRules` 'Brakiri Syndicracy' 10; constants GRAV_BEAM_DAMAGE 1, GRAV_PULSE_SHIFT 5, GRAV_ENGINE_THRUST 1, GRAV_THRUSTER 1,
  GRAV_ROTATION_PENALTY 3; `$abilityNames` rotate. `usesGravitics` / `isGraviticWeapon` (⚠️ MIRROR PAIR with officers.isGraviticWeapon) / `isOtherSetting`
  (⚠️ MIRROR of gamelobby.js `customSubgroups` - the harness compares them over every faction), `applyGravitonTweaks` / ⭐ `gravitonThruster` (⚠️ MIRROR PAIR
  with officers.gravitonThruster), ⭐ `gravitonBeamBonus` (⚠️ MIRROR PAIR with officers.gravitonBeamBonus), `gravitonPulseShift`, `rotationPenalty`,
  `gravitonIndex`, ⭐ `rotateGravitic` (the roll: checks, the `gravRotate` row, the spend - one OffUsed note `OFF_GRAV:rotate` on his Engine -, the lock).
- `Movement` (movement.php): `GRAV_ROTATE` 'gravRotate' (⚠️ MIRROR PAIR with shipManager.movement.GRAV_ROTATE), `hasGravRotated`.
- ⭐ **`MoveLock`** (new, `handlers/MoveLock.php`) - THE movement lock, for any order a unit cannot take back mid-move: one `tac_systemdata` row per unit and
  turn (system id -1, key `moveLock`), holding the unit's rows of the turn up to and including the order. `save` / `get` / `forTurn`, ⭐ `keepsLock` (the
  commit's own orders - this turn's rows that are neither preturn nor forced - must begin with the locked ones: type, hex, facing, heading, speed, and a
  lock-type row's value - ⚠️ MIRROR PAIR with moveLock.beginsWith), `dropUnlocked` (a lock-type row outside a lock is dropped), `LOCK_TYPES` (today
  'gravRotate' - ⚠️ MIRROR PAIR with moveLock.LOCK_TYPES), `forgetGame` (when Movement ends), MAX_BYTES / MAX_ROWS (the sweep's limits). DBManager
  `saveMoveLock` / `getMoveLock` / `getMoveLocks` (one player's with `$playerid`) / `deleteMoveLocks`. Minesweeping Mode keeps its own table.
- Endpoint `gravRotate.php` -> `Manager::rotateGraviticDrive` (the sweep's checks: active game, this turn's Movement, the unit moving now, the player's,
  not committed, the player's submit lock; then a real load in a transaction and `Officers::rotateGravitic`; the reply is the lock's rows and the die).
  Asked again on the same turn it answers the stored lock - never a second roll. game.php inlines `window.fvMoveLocks` (`Manager::getMoveLocksJSON`,
  Movement only, never throws).
- Hooks: `MovementGamePhase::process` (MoveLock::keepsLock beside the sweep check - a refused commit says reload; MoveLock::dropUnlocked after it),
  `MovementGamePhase::advance` (MoveLock::forgetGame beside MineSweep::forgetGame), `Weapon::calculateHitBase` (rotationPenalty beside the pivot's),
  `Weapon::fire` (gravitonPulseShift into both rollPulses calls), `Weapon::getFinalDamage` (gravitonBeamBonus).
- Client: new `client/moveLock.js` (the lock, generic: `lockIndex` / `isLastRowLocked` / `protects`, ⭐ `ask` - post a locking order with this turn's rows
  as a JSON string, the blocking overlay up, and put the rows the server answers in place -, `playerRows`, `beginsWith`, `onStrategyActivated` - the reload
  restore, after Save Orders' and Minesweeping's); `movement.js` (`isLastMoveFinal` asks it; doJink(-1) and doContraction(-1) never splice a locked row;
  `GRAV_ROTATE`, `getGravRotateRow`, `hasGravRotated`, `canGravRotate`, `getGravRotateState`, `doGravRotate`); `UI/shipMovement.js` (the Gravitic Rotate
  icon in the Charge / Contraction spot - no hull carries both; knobs `gravRotateIcon` / `gravRotateReadyOpacity` / `gravRotateGlowColour`,
  `drawGravRotateIcon`, `gravRotateCallback`), `ShipMovementCallbacks.gravRotateCallback`, game.php's `#gravRotate` element, the `moveLock.js` script tag
  and `window.fvMoveLocks`; `PhaseDirector` (the restore); `weaponManager.computeShotModifiers` ("Gravitic Rotation" -3); `systems.js` getOutput /
  getOutputNoBoost (`officers.gravitonThrustBonus`, the lobby's +1s); `officers.js` (the registry row, `needs` gravitics, `isGraviticWeapon`,
  `gravitonThruster`, `gravitonThrustBonus`, `gravitonBeamBonus` / `gravitonDamageLine` in adjustDataDisplay, `canGravRotate`, ABILITY_NAMES rotate).
  Art: `img/rotateIcon.png` (the user's) recoloured to graviticTurnLeft1's gold (139, 101, 32), alpha kept (the white original kept in the session scratchpad only).
- Docs: an Expert Graviton Controller card (Ship Officers, between the Geneticist and the Gunner).

**Files.** Server: `Officers.php`, `handlers/MoveLock.php` (new), `handlers/movement.php`, `controller/Manager.php`, `controller/DBManager.php`,
`weapons/weapon.php`, `Phase/MovementGamePhase.php`, `public/gravRotate.php` (new), `public/game.php`; `autoload.php` regenerated (MoveLock). Client:
`officers.js`, `moveLock.js` (new), `movement.js`, `UI/shipMovement.js`, `ShipMovementCallbacks.js`, `renderer/PhaseDirector.js`, `weaponManager.js`,
`systems.js`, `img/rotateIcon.png`. Docs `ammo-options.html`. Harnesses: `officersHarness.php` (writes `gravitonFixture.json`; four Stage 27-30 registry-order
checks moved), `officersClientHarness.js` (likewise four), `enhancementsHarness.php` (+Avioki; cases `grav`, `grav+eng+elite1`; fingerprint re-recorded, old
copy `fingerprint.before-officers-stage31.txt`), `enhancementsDifferential.js` (an Engine's or thruster's output compared with gravitonThrustBonus).
`fvbuild -Statics`, both legacy bundles (`node scripts/bundle-legacy.js`), `-Sync`. No React change, no schema change.

**Measured.** Statics: 180 offers on 179 hulls (the Rogata sits in both Minbari factions' files - and they are two different hulls of one class name):
43 Brakiri at 10% (Avioki 73, Brikorta 50, Antoph Early 40), 136 elsewhere at 15% (Sharlin 274, IML Attack Cruiser 68). Fingerprint: 3,544 lines added
(3 offers, the Avioki's block, the two cases on every hull), none changed - every old line is kept, in order.

**Verified.**
- `officersHarness.php` **2276 / 0 with the database** (2182 / 0 --no-db): the registry place, prices on five hulls (Brakiri with and without a gravitic
  drive, Minbari, Raiders), none on an Omega, a Star Trek or Escalation Wars hull, a base, an Ancient or a flight; isOtherSetting over 15 names and against
  gamelobby.js's own three tests over every faction of the statics; the sweep (179 hulls, 43 Brakiri, every price); the post; a doctored row. The tweaks:
  Engine 15 -> 16, the first of two 8s -> 9, a single 10 -> 11, an Improved Thrust making the second the largest, Elite Crew, killed (kept), his Engine
  destroyed (thruster kept), pre-battle (none). The REAL getFinalDamage (+1 on a Graviton Beam, every Lance mode, a Cutter; none on a Pulsar, a Bolt, a
  Cannon, once lost, the gate off; a roll of 0). The REAL Weapon::fire (the rules' example 50 / 35 -> 2 pulses; 31; 36; 50; 51 and 54 miss; 15 and 1 at the
  cap; lost; gate off). The REAL calculateHitBase (-15% on a rotation, -30% with a pivot, last turn's none, a turn after it still -15%, gate off / no
  officers none). rotateGravitic in memory (a fake DBManager): the row, one die, the note, the lock; asked again - the same answer, no die; each face 1-6
  -> facing 0-5; after moves, its hex and heading; another turn's row left out; nine refusals with no die, note or save. MoveLock: keepsLock (11 cases,
  also written to the fixture), dropUnlocked, save / forTurn / get / forgetGame. The REAL database, rolled back (game 4508): the row in tac_systemdata, a
  gamedata load untroubled by system id -1, forTurn by player, getMoveLocksJSON, forgetGame, rotateGraviticDrive's refusals.
- `officersClientHarness.js` **955 / 0**: the row and constants against Officers.php / movement.php / MoveLock.php; the statics (180 / 179, passesNeeds, every
  price); gravitonThruster against the server on all 179 hulls (gravitonFixture.json); the lobby through the REAL systems.js getOutput (Engine 16, thruster
  9 / 8; untouched in game and once his Engine is destroyed) and the Damage line (17-62 -> 18-63); gravitonBeamBonus; officers.canGravRotate; and, in a
  sandbox with the REAL officers.js, movement.js, savedOrders.js and moveLock.js: canGravRotate and its ten refusals, doGravRotate's request (the URL, the
  body, the moves as a string, one at a time), the answer applied, the notice, the lock (no Cancel, deleteMove refused, a move after it cancellable),
  doJink(-1) keeping a locked jink, a refusal / a failed request / a late answer, beginsWith against all 11 fixture cases, playerRows, the reload restore
  (put back, a draft that extends it kept, a draft that does not replaced, five cases left alone); the REAL computeShotModifiers ("Gravitic Rotation -3",
  beside "Pivot for Shooting -3"); the REAL shipMovement.js icon (drawn, clicked -> asked, "rotated", no Cancel; none without him); game.php and
  PhaseDirector.
- `enhancementsHarness` **295 / 0** (fingerprint re-recorded, additions only); `enhancementsDifferential.js` **964 / 0** (50 new cases).
- `fvbuild -Check`: autoload current, validator clean, replay **162 / 0** (no corpus game has him). `-Sync` done.
- ⚠️ **Found by the client harness and fixed**: the first build named its functions `canRotate` / `doRotate` / `rotateCallback`, which BASE rotation already
  uses - in movement.js's object literal the later key wins, so base rotation's `canRotate` / `doRotate` were replaced by the gravitic ones, while in
  ShipMovementCallbacks / UI.shipMovement base rotation's `rotateCallback` won and the gravitic icon did nothing. Everything is now `gravRotate*`, and the
  row type `gravRotate`.
- **Mutation-tested: 85 / 85 caught** (37 client, 1 differential, 44 server, 3 server with the database; ~45 minutes; every file byte-identical
  after; the client and differential mutants first, then a clean server run rewriting `gravitonFixture.json`). The first pass missed six, each now
  pinned by a check of its own: `moveLock.ask`'s own one-at-a-time guard and gravRotate's byte limit (both shadowed by the next check), a refusal
  that went on to apply its missing rows (the fixture had no plotted move to lose), the answer's server rows applied twice (none in the fixture), any
  thruster instead of an aft one (no fixture hull had a larger non-aft thruster first - a Brathon's retro now is), and a weapon gravitic in one mode
  only (no hull needs it - a probe). Runner `mutate31.js`, session scratchpad only.

**Observations, not changed:**
1. Not play-tested. Worth a test: a Brakiri Avioki with him (73 points) - the lobby shows the Engine 16 and one aft thruster 9; in game, its Gravitic Rotate
   icon (gold, in the Charge spot) during its move: a click rolls (a notice says the die), the ship faces the new way with its heading kept, Cancel is gone
   for every move before it, a page reload puts the moves and the rotation back, and the Firing-phase preview shows "Gravitic Rotation -15%". A Graviton
   Beam's Damage line reads 18-63; a Pulsar needing 50 that rolls 35 scores two pulses.
2. Not covered by a harness: `MovementGamePhase::process`'s two MoveLock lines and `advance`'s forgetGame (the methods are covered), gravRotate.php itself,
   and the in-browser drawing of the icon.
3. A unit that never commits (the Movement phase's server fallback moves it) loses a rotation it rolled - the die is spent, the lock ignored.
4. The rotation is a row of the ship's movement, which every viewer is sent - so the rotation, and that he is aboard, are public (the tabletop rolls it in
   the open).
5. The Gravitic Bolt is named in his rules text but has no to-hit-dependent result in FV, so his clause does nothing for it.
