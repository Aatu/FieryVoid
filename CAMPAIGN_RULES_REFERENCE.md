# Campaign Rules Reference (AoG Wars Kitchen Sink, ch. 13–14)

Source: *AoG Wars — Kitchen Sink edition* (draft 4/17/13, errata 15 Mar 2024), **pp. 267–285**
(§13.0 Tactical Campaigns, §14.0 Large Scale Campaigns), plus **Appendix C, p. 288** (Campaign Turn
Sequence). Appendix C is outside that page range, but it is the turn order for §14, so it is included.
Summaries of rules from *other* chapters that the campaign chapters rely on are in **Part C**.

Purpose: input for a future `CAMPAIGN_PLAN.md`. This file records the rules as written, with every
number kept. It is not a design. Wherever the source is garbled, contradictory, truncated or silent,
the text carries a **⚠️** marker. Every marker is also listed in **Part D**, so the planning pass can
settle them one at a time. **Part E** lists existing Fiery Void features a campaign would build on.

Conventions in this file:

* `§x.y` is the book's own section number, kept so each rule can be traced.
* `Pop` = a colony's population value. `ST` = strategic turn. `TT` = tactical turn (the campaign
  turn, not a battle turn). `CPV` = Combat Point value (the ship's printed point cost).
* Sizes: LCV = light; **MCV = medium**; HCV = heavy; Capital; Enormous.

---

## Part 0 — How the two chapters relate

The book contains **two separate campaign systems**, not one system in two halves:

| | §13 Tactical Campaigns | §14 Large Scale Campaigns |
|---|---|---|
| Author | core KS text | adapted from campaign rules by "Valthonis" |
| Scale | a few star systems; ships move hex by hex across a map of each system | a galaxy hex map; task forces move along hyperspace lanes |
| Time unit | **pulse** | **tactical turn** (4 per **strategic turn**) |
| Economy | Economic Points + Commerce Points (barely defined) | Credits, Ore, HA Ore, maintenance, food, research |
| Endurance | in pulses (Table 18) | in strategic turns (§14.4.2) |
| Repair | timed in pulses, priced by Table 17 | repair points at shipyards and bases (§14.11) |

They have the same purpose but conflict in detail (endurance, repair, detection). A plan has to pick
one as the base and borrow from the other, not implement both. ⚠️ (D1)

Time scale in §14, from Appendix C: **4 tactical turns = 1 strategic turn.** A battle (a normal
Fiery Void game) happens *inside* a tactical turn's Action Phase. Several §14 rules say "tactical
turn" where they clearly mean a battle turn. ⚠️ (D2)

---

# PART A — §13 TACTICAL CAMPAIGNS (pp. 267–269)

## §13.0 Overview

* A campaign is **several star systems linked by jump paths**. Each system is a **hex grid** whose
  size the campaign sets.
* Ships move across the system maps. A **battle happens only when opposing units make contact**, and
  is played as a scenario.
* After a battle, **the winner holds the hex; the loser must retreat to an adjacent hex on its next
  move.**
* **Movement is always simultaneous.**
* Two players: all ships and moves are visible on one shared map. With a third player as moderator,
  movement can be secret, which makes ambushes and raids possible.
* Play starts with a set-up defined by the campaign, then runs in **pulses**. Ships normally move
  **1 hex per pulse**. Jumps take longer, and construction and repair take many pulses.
* Keep a log of pending events and the pulse each one completes.

## §13.1 Production

* The campaign economy pays for **new units** and **repairs**.
* Production is paid with **Economic Points (EPs)** and **Commerce Points (CPs)**, up to a **fixed
  limit that depends on hull size**. ⚠️ The limit is never given, and "CP" also means Combat Points.
  (D3)
* Construction time for each unit comes from the campaign notes.
* Cost per pulse = ship cost ÷ production time. Simpler option: pay the full cost when construction
  starts.

## §13.2 System Maps

* Hex fields defined in the campaign set-up, using any blank hex map with counters.
* Ships and fighters move **1 hex per pulse** unless noted otherwise.

## §13.3 Movement

* **Open movement:** all moves are known to everyone; one map, all counters on it, simultaneous.
* **Secret movement:** a GM records moves and uses Table 16 to tell each player what their fleets can
  detect. Still simultaneous.

**Table 16 — Detection Ranges** (how far the unit sees enemy ships / enemy fighters)

| Detecting unit | Sees ships at | Sees fighters at |
|---|---|---|
| Fighters | same hex | same hex |
| Ships with EW < 12 | 1 hex | same hex |
| Ships with EW > 12; ELINTs < 10; Bases < 12 | 2 hexes | same hex |
| Bases < 12 *(sic)* | 3 hexes | same hex |
| ELINTs < 15 | 4 hexes | same hex |
| ELINTs 15+; ships with advanced sensors | 5 hexes | 1 hex |

⚠️ "Bases < 12" appears twice; the second row probably means Bases ≥ 12. EW of exactly 12 fits
neither "< 12" nor "> 12". "ELINTs < 15" probably means ELINT 10–14. (D4)

## §13.4 Combat

* Units in the same hex **fight if either side chooses to**.
* Played as a basic scenario: forces **enter from opposite sides of the map**, with weapons ready
  under the standard rules.

## §13.5 Repair

* **In the field (no base):** a ship can repair systems that are **not destroyed**.
  Time: **1 pulse per 5 boxes, plus 1 pulse per critical.** It **cannot** repair structure or
  destroyed systems this way.
* **At a base or repair ship:** **4 pulses per primary structure block** (primary must be repaired
  before any external block); **3 pulses per external block**. Each base has a **repair capacity**
  (how many blocks it can repair at once), set by the campaign notes.
* **Destroyed sections** can be rebuilt **only at a shipyard**. Time in pulses = **15% of production
  time** for capital ships, **25%** for HCVs.

**Table 17 — Structure Repair Costs**

| Ship size | Primary structure | Secondary structure |
|---|---|---|
| MCV / LCV | 80% of CPV × Y | n/a |
| HCV | 40% of CPV × Y | 25% of CPV × Y |
| Capital / Enormous | 30% of CPV × Y | 20% of CPV × Y |

**Y** = (destroyed structure boxes in the block + boxes of destroyed systems in that section), as a
percentage of the section's total boxes.
*Example:* a 32-box structure block plus two 5-box gravitic bolts = 42 boxes. Both bolts destroyed
and 10 structure boxes lost → Y = 20 / 42 = **48%**.

## §13.6 Hyperspace Movement

* The campaign map shows systems and jump routes. **The number on a route = pulses to travel it.**

## §13.7 Jumping Into Systems

* A ship with a jump engine can jump into **any hex of a system map**, if it knows the matching
  hyperspace location.
* A fleet with **no ELINT**, jumping into a system where its owner has **no facilities or ships**,
  rolls **d10 for scatter: on 1–6 it scatters in the direction shown.** ⚠️ Scatter distance is not
  given.
* Otherwise the fleet arrives in the target hex. ⚠️ The source sentence "If this results…" breaks off
  mid-sentence. (D5)

## Endurance (Optional) — printed as "12.8", between §13.7 and §13.8 ⚠️ (D5)

* Endurance = time before a unit must **resupply, refuel and rotate its crew**.
* **Fighters:** destroyed when endurance runs out.
* **LCVs:** stop when endurance runs out and are destroyed 2 pulses later.
* **Ships:** for every **2 pulses** past endurance (**MCVs: every pulse**): −1 initiative, −1 free
  thrust, −1 EW.
* At **0 thrust** the ship drifts dead and counts as abandoned. The owner may self-destruct it then,
  or leave it as a derelict to recover later. A derelict **can never** be self-destructed afterwards.
  If the enemy reaches it first it is **captured**, and after **2d6 pulses** at a base the captor can
  use it against its former owner.
* **Reset:** spend **1 pulse** in the same hex as a unit the campaign notes call a **supply point**,
  or **docked at a base**.

**Table 18 — Endurance:** Fighter 2 pulses · LCV 8 · MCV 25 · HCV 40 · Capital 60.
⚠️ Enormous units and bases are not listed.

## §13.8 Fleet Missions (Optional)

| Mission | Movement | Detection | Readiness / special |
|---|---|---|---|
| **Silent Running** (§13.8.1) | 1 hex per **2** pulses | Seen only by enemies **in the same hex**; it also sees only enemies in its own hex | Weapons off, jump engines off. If a battle starts, it begins with **no arming turns** completed. |
| **Convoy** (§13.8.2) | **2 hexes per pulse** | Drops **one rank** on Table 16 (minimum: same hex) | Commerce ships (plus escorts) run between **two set points** on a plotted route with waypoints and supply stops, earning **commerce points** (amount per campaign notes). All ships at **Cruise Stations** (Part C). |
| **Combat** (§13.8.3) | 1 hex per pulse | normal | Enters battle with **all weapons fully armed**. |
| **Intercept / Shadow** (§13.8.4) | 1 hex per pulse | normal | Targets one enemy fleet. *Intercept:* takes the shortest route to its hex. *Shadow:* stays within detection range of it. Two fleets targeting each other resolve simultaneously. Full readiness. |
| **Ambush** (§13.8.5) | cannot move | undetectable except from the same hex | Cannot open jump points. Under open movement it still hides the fleet's *contents*. When sprung, targets **not** on Combat or Intercept/Shadow suffer **Tactical Surprise** (Part C; the source cites "11.4.2" but means §12.4.2). The ambushed fleet **never** has jump engines ready, but may start powering them when the scenario begins. |

---

# PART B — §14 LARGE SCALE CAMPAIGNS (pp. 270–285)

## §14.1 Building the Galaxy

Roll **1d10 per hex** of the campaign map:

| d10 | Hex contents |
|---|---|
| 1–3 | star system |
| 4–9 | empty |
| 10 | unknown; must be explored |

### §14.1.1 Star System Contents

* Bodies per system: **1d10 + 1**.
* For each body roll **1d10 twice** (class, then type):

| 1st d10 | Class | 2nd d10 → type |
|---|---|---|
| 1–3 | Organic | 1–6 Terrestrial · 7–10 Oceanic |
| 4–8 | Lifeless | 1–6 Barren · 7–10 Irradiated |
| 9–10 | Uninhabitable | 1–6 Asteroid Belt · 7–10 Gas Giant |

* **Jump Gates already present**, per system roll **1d10** (+1 if the system has 2+ organic bodies;
  −2 if it has none):

| d10 | Jump Gates |
|---|---|
| 1–3 | none |
| 4–8 | one, orbiting a random body |
| 9–10 | 1d2, placed randomly |

### §14.1.2 Jump Gates (lanes)

* Each gate supports **up to 4 beacon links** (hyperspace lanes).
* A gate present at the start begins with **1d3 links**, to the **closest** systems that have gates.
* No lane between two gates in the same system. **At most one lane between any two systems.**
* Every player's home must have **at least one gate linked to another system.**

### §14.1.3 Maximum population per body

Terrestrial **60** · Oceanic **40** · Barren **20** · Irradiated **20** · Gas Giant **0** ·
Asteroid Belt **20**.
Bodies may be populated at the start or colonised during play.

### §14.1.4 Mineral Deposits

* Per body, **1d10**: 1–3 Poor · 4–8 Average · 9–10 Rich.
* Ore: Poor **50 × (Pop + 1d3)** · Average **100 × (Pop + 1d3)** · Rich **100 × (Pop + 1d6)**.
* **Hyperspace-Attuned (HA) Ore** deposits, **1d10**: 1–3 none · 4–8 one · 9–10 1d3.
* ⚠️ Is "Pop" the maximum or the current population? Is the HA roll per body or per system? How much
  ore is in one HA deposit? Is a deposit a stock that runs out or an income per turn? (D6)

### §14.1.5–14.1.9 Body statistics

| Stat | Terrestrial | Oceanic | Barren | Irradiated | Asteroid Belt |
|---|---|---|---|---|---|
| Farming Value | Pop + 1d6 | Pop + 1d6 | 1d3 − 1 (hydroponic) | none | 1d3 (hydroponic) |
| Food Requirement | = Pop | = Pop | = Pop | = Pop | = Pop |
| Economic Value | 100 × (Pop + 1d6) | 100 × (Pop + 1d6) | 50 × (Pop + 1d3) | 50 × (Pop + 1d3) | 50 × (Pop + 1d6) |
| Mineral Value | no penalty | −25% ore | no penalty | −25% ore | no penalty |
| Growth Value | +1 per 5 ST | +1 per 5 ST | +1 per 10 ST | +1 per 20 ST | +1 per 15 ST |
| Maintenance Value | 100 × Pop | 100 × Pop | 50 × Pop | 50 × Pop | 50 × Pop |
| Max Defense Value | 2 × Pop | 2 × Pop | = Pop | = Pop | ½ × Pop, **no Gun Battery points** |

* **Asteroid belts:** only races marked as belt-capable may colonise them. They may settle belts in
  other races' home systems, and can still colonise planets too.
* **Gas giants:** max Pop 0 and no stats, so they cannot be colonised.

My reading, from Appendix C ("Systems produce credits, Ore, HA Ore, maintenance, food"): **Economic
Value = credits per ST; Maintenance Value = maintenance points per ST** (the pool that pays unit
upkeep, §14.4); Farming minus Food Requirement = food surplus or deficit. ⚠️ None of this is stated
outright, and there are **no rules for starvation, food transport, how growth works, or what Defense
Value does outside §14.14**. (D7)

## §14.2 Economics

* **Credits** are the universal currency: they buy ships, research points, and goods from other
  players.
* **Devaluation:** when the referee judges an empire to be **losing a war**, it loses **1d6%** of its
  treasury, **+1d6 per tier of difference** if it is losing to a higher-tier empire (§14.22).
  ⚠️ How often this is checked is not said. (D8)

### §14.2.1 Trade Routes

* Between willing players. **Each side gets +10% economic value**, from the ST after ratification.

### §14.2.2 Jump Gates (use)

* Gates let **civilian traffic** travel between systems without jump engines. Ships with jump engines
  also prefer gates, to save their HA Ore and avoid the hazards of hyperspace.
  ⚠️ This implies a jump engine uses HA Ore per jump, but no rate is ever given. (D9)
* The owner may **lock** a system's gates, leave them **open**, or let chosen races through **free**
  or for a **toll (usually 1–10 credits per ship)**.
* A lock blocks only **entering** hyperspace; ships can still **arrive** through a locked gate.
* A gate's **beacon can be shut down**, stopping all traffic both ways. This is a desperate act and
  frowned upon. Task forces on that lane can get **lost** and must start **hyperspace navigation
  checks**. ⚠️ Navigation checks are never defined. (D10)

### §14.2.3 Jump Gate Construction

* **10 HA Ore + 1,000 Ore + 2,000 credits; 4 ST; maintenance 150.**
* Default capacity is 4 lanes. **Upgrade to 6 lanes: 1,500 Ore + 2,500 credits, 2 ST.** The gate is
  **inactive during the upgrade**, and its maintenance rises from 150 to **200**. The upgrade can be
  added to the initial build by adding its resources and time.
* ⚠️ How a new gate gets its lanes is not stated. (D10)

## §14.3 Production

* Facilities: **factories**, **shipyards**, **training facilities**. Each has a **production value**:
  what it produces per ST.
* **Build time (turns) = the ship's Ram factor ÷ the facility's production value, rounded up.**
  *Example:* Ram 250 at a 50-point yard → 5 turns.

**Unit production costs**

| Item | Production points | Ore | Credits |
|---|---|---|---|
| Fighter missile or standard mine | 1 | — | — |
| Captor mine | 2 | — | — |
| Basic ship missile | 2 | 1 | 1 |
| Light missile | ⅔ | ⅓ | ⅓ |
| DEW mine | = its structure blocks | — | — |
| Supply point / Repair point | 1 each | 1 each | 1 each |
| Ground Battery (point) | 150 | 200 | 100 |
| Sensor Net (point) | 100 | 250 | 500 |
| **A ship** | (see note) | ½ × production-point cost | ½ × production-point cost |

⚠️ A ship's "production points cost" is never defined. Build time uses the **Ram factor**, but the dry
dock rule (§14.3.2.4: 62.5% of point value in credits + 12.5% in Ore) only makes sense if a normally
built ship costs about **100% of its CPV**, split half Ore and half credits. Fighters, shuttles and
OSATs have no cost at all. (D11)

### §14.3.1 Factories

| | Small | Medium | Large |
|---|---|---|---|
| Produces | supply & repair points, missiles, mines, sensor nets | **small factories**, OSATs, fighters, shuttles | **medium factories**, ground battery points, super-heavy fighters, LCVs |
| Production value | 100 | 200 | 300 |
| Maintenance | 1 | 5 | 10 |
| Build cost | 25 Ore + 50 cr | 50 Ore + 100 cr | 150 Ore + 250 cr |
| Build time | 1 ST | 2 ST | 3 ST |

⚠️ Can a larger factory also make the smaller tiers' items? What builds a **large** factory? (D12)

### §14.3.2 Shipyards

* **Every shipyard produces 50 production points per ST.**
* A **jump-capable** ship also costs **HA Ore**: Enormous 5 · Capital 4 · HCV 3 · MCV 2 · LCV 1.

| | Small | Medium | Large |
|---|---|---|---|
| Builds | LCVs, MCVs | "all combat vessels" | HCVs, capital ships, enormous ships |
| Maintenance | 20 | 25 | 40 |
| Build cost | 125 Ore + 250 cr | 250 Ore + 500 cr | 600 Ore + 1,500 cr |
| Build time | 2 ST | 4 ST | 8 ST |

⚠️ Does "all combat vessels" for the medium yard include enormous ships? (D12)

**§14.3.2.4 Dry Dock**

* Costs **50% less** to build and to maintain than the equivalent shipyard.
* **Cannot build ships.** Instead it buys **stripped hulls from other empires** at **62.5% of point
  value in credits + 12.5% of point value in Ore**. The hull is then treated as **refitted into a
  variant**.
* An empire **cannot refuse** to sell a basic hull unless it is at war with the buyer or one of the
  buyer's allies.

**§14.3.2.5 Captured Shipyards** must be retooled before use: **½ the yard's build cost in credits,
½ its build time.**

### §14.3.3 Training Facilities

* **Marines: 10 credits per unit. Agents: 50 credits each.**
* Carrying capacity: assault shuttle **1** marine unit; breaching pod **2**; any ship up to **1% of
  its point value** in marines. Marines listed on a ship's SCS are **not** free.
* Each marine stationed on a planet adds **+1 Defense Value**.

| | Small | Medium | Large |
|---|---|---|---|
| Trains per ST | 1 marine | 2 marines | 4 marines **or** 1 agent |
| Maintenance | 1 | 5 | 15 |
| Build cost | 30 Ore + 75 cr | 50 Ore + 125 cr | 200 Ore + 200 cr |
| Build time | 1 ST | 2 ST | 3 ST |

### §14.3.4 Decommissioning Facilities

* Shutting down a factory, shipyard, dry dock or training facility takes **½ its build time
  (minimum 1 ST)**. Once closed it produces nothing and costs **no maintenance**.
* **Reactivation** is free: **1 ST** for small, **2 ST** for medium or large.

### §14.3.5 Base Construction

* Built like ships, with the **same cost calculation**, at **100 production points per ST**.
  ⚠️ Which facility builds bases is not said. (D12)

### §14.3.6 Fighter Bases (ground or orbital; orbital figures in brackets)

| | Small | Medium | Large |
|---|---|---|---|
| Fighter capacity | 12 | 24 | 48 |
| Maintenance | 20 (25) | 35 (45) | 60 (80) |
| Build cost | 20 Ore + 50 cr (25 + 60) | 50 + 100 (60 + 125) | 250 + 250 (300 + 300) |
| Build time | 1 ST | 2 ST | 3 ST |

### §14.3.7 Deployment Limitations

* Uncommon/rare and limited/restricted variant ratios (Part C, §9.2.1) apply **across the whole
  fleet**, not per task force.

### §14.3.8 ELINT Construction

* In a campaign designated an **Exploration Campaign**, all ELINT deployment limits are lifted. An
  ELINT ship that is normally limited, restricted, uncommon or rare ignores that limit but costs
  **3× to build and 3× to maintain**.

### §14.3.9 Decommissioning Vessels

* **1 ST** to decommission. Record where the ship is laid up. **No maintenance** while decommissioned.
* **Reactivation** needs a suitable shipyard and **1 ST**, with that turn's maintenance paid.
* Decommissioned ships may be **sold to other empires**.

## §14.4 Maintenance and Supply

| Unit | Maintenance |
|---|---|
| Facilities | listed cost **every ST**; if unpaid, the facility stops and production halts until paid |
| OSATs | **5%** of SCS point value per ST |
| Bases | **20%** per ST |
| Mines | **10%** per ST |
| Ships | **20%** at the **end of their endurance period** (§14.4.2) |
| Fighters | **10%** whenever their carrier or base pays maintenance |

### §14.4.1 Freighters

* Freighters carry **supply points** for task forces far from home:
  **1 supply point per cargo box; 1 supply point = 10 maintenance points.**

### §14.4.2 Endurance (strategic turns between maintenance)

Enormous **5** · Capital **4** · HCV **3** · MCV **2** · LCV **1** · Fighters **1** (only if not
assigned to a carrier or base).

* Any combat reduces a vessel's endurance **by 1 (minimum 1)**.
* Explorer-type ships get **+2**.

### §14.4.3 Out of Maintenance (after one ST without the maintenance it needed)

* All **thruster ratings −1**.
* Weapons with tracked ammunition (missiles etc.) **do not reload**.
* **Jump delay +10%** (round up).
* **+1 to critical rolls for each ST** spent out of maintenance.

### §14.4.4 Supply Lines

* At the **end of each tactical turn**, a task force within supply range has its **fighters and
  ammunition restocked**.
* Supply freighters are **abstract**: not tracked as units.
* Range: **4 hyperspace hexes from a friendly star system.**

### §14.4.5 Supply Depots

* Extend supply lines. A depot must be within **4 hexes** of a system it extends, and adds
  **another 4 hyperspace hexes** of range.
* **500 Ore + 1,000 credits, 2 ST.** No colony needed in the depot's system.
* ⚠️ Depot maintenance is not given, and it is unclear whether depots can chain off other depots. (D13)

### §14.4.6 Supply Line Raids (random raiders)

* Each ST, pick **one supply line at random**. Raiders strike on **1d10 ≥ 8**; each raid steals
  **3d10 credits**.
* If the first raid hits, roll again needing **9+**; if that hits, a third roll needing **10+**.
  **At most 3 per ST.**
* **−1** if ships on **Patrol** orders are at either end of the line. **+1** if raiders succeeded the
  previous turn.
* Skip the roll if all the player's ships are orbiting friendly systems **and** the player has no
  supply depots.

### §14.4.7 Missile and Ammunition Stockpiles

* Production beyond what ships carry is **stockpiled** (and tracked), at supply depots or planetside.
* Ships carrying **missile-capable fighters** carry reload missiles per hangar box:
  MCV **4** · HCV **8** · Capital **12** · Enormous **16**. Races that habitually use fighter missiles
  carry **+50%**.

## §14.5 Task Force Orders

A **task force** (TF) = a group of ships with identical orders. Readiness states are defined in Part C.

| Order | Readiness if attacked | Effect |
|---|---|---|
| **Ambush** (§14.5.1) | Battle Stations | Hides in terrain or hyperspace and engages the **first target of the type named in the order**. |
| **Assault** (§14.5.2) | Battle Stations | Attacks a planet. Assault ships may **hold back** (out of the battle; they land their shuttles only if the battle is won), or **fight** and launch assault shuttles during combat. |
| **Attack** (§14.5.3) | Battle Stations | Engages any hostile force at the end of a jump or within a system. **Up to half its fighters may deploy immediately** on arrival. |
| **Blockade** (§14.5.4) | Patrol Stations | Targets a planet or its Jump Gate. Planet blockaded → **all its production stops the next ST**. Gate blockaded → enemy ships using it are engaged. Any defending TF in the system on Blockade Run, Garrison Duty or Patrol must be **defeated first**. |
| **Blockade Run** (§14.5.5) | Battle Stations | Breaks through a blockade to deliver supplies or evacuate. |
| **Garrison Duty** (§14.5.6) | Patrol Stations | Defends a planet, supply depot or Jump Gate. |
| **Intelligence Gathering** (§14.5.7) | Patrol Stations | A **single ELINT ship**. Automatically learns **planet types and stellar anomalies**. For each planet, a **sensor sweep** reveals Ore and HA Ore quantities, TF strength and orders, minefields, bases and shipyards: **1d20 + Sensor Net modifiers (§14.8.1) ≤ sensor value**. It need not be in the scanned planet's theater. ⚠️ (D14) |
| **Move To** (§14.5.8) | Cruise Stations | Moves to a planet, a gate, or a **point in hyperspace** (it waits just off the lane). Staying off the lane **more than one ST** requires hyperspace navigation checks. |
| **Patrol** (§14.5.9) | Patrol Stations | Patrols a planet, gate, shipyard or depot and detects invaders using the Sensor Net rules (§14.8.1). On detection it may **intercept** (Outer Sphere) or **fall back** to the planetary defenses (Inner Sphere). Its detection also lets friendly **OSATs, bases and Garrison TFs** go to **Battle Stations**. |
| **Raid** (§14.5.10) | — | Preys on supply freighters. Raiders earn **no XP**. They can be detected by Sensor Nets; if the net fails and an enemy **Patrol** TF is present, the patrol finds them on **1d10: 1–6**. If found, the patrol engages the raiders, with **1d3 + 1 supply freighters** among them. ⚠️ What the raider gains is not stated. (D15) |
| **Refit/Repair** (§14.5.11) | **Tactical Surprise** | The TF shuts down at a shipyard. Allowed only where a suitable yard exists. |
| **Survey** (§14.5.12) | Cruise Stations | Learns a system's **planet types and stellar anomalies**. ELINT: **automatic, 1 TT**. Others: **2 TT** and **1d20 ≤ highest sensor value in the TF**. A further roll reveals resources. **ELINT +4.** ELINT need not be in the planet's theater; other ships must be. ⚠️ (D14) |
| **Training Maneuvers** (§14.5.13) | **Drill Stations** | Training for a stated number of TTs (**max 8 per ship**); crews earn **10 XP per TT**. The TF is **out of play** until it finishes. |

## §14.6 Task Force Movement

### §14.6.1 Hyperspace Movement

* Hyperspace is a large hex map; distance is counted in hexes.
* A ship's **hyperspace speed = the maximum acceleration it can make without boosting the engine or
  overthrusting**, per its SCS. ⚠️ (D16)
* A TF moves at its **slowest ship's** speed.
* A TF **cannot stop in hyperspace**, except under **Move To**.

### §14.6.2 Limited Movement Ships

* **Patrol, Monitor and Police** vessels may travel only between their own empire's systems.

### §14.6.3 Normal Space Movement

* Speed = **½ hyperspace speed, rounded up**.
* **Adjacent planets are 1 hex apart.** A system's layout is the **order its bodies were rolled**
  during galaxy generation (a one-dimensional chain).
* A jump-capable ship may instead **open its own jump point**. To follow a lane it must pass a
  **sensor check, 1d20 ≤ sensor value**, to lock onto the gate beacon.

## §14.7 Theaters of Operation

Each planet has **two spheres**:

* **§14.7.1 Outer Sphere:** **Jump Gates and patrols** are here. Arriving invaders may be detected by
  the system's Sensor Net or a patrol. A patrol that detects them may **engage** or **fall back**.
* **§14.7.2 Inner Sphere:** **bases, minefields, garrisons, OSATs**. **Shipyards** orbit lower than
  the OSATs.
* **§14.7.3 Arriving from hyperspace:** normally into the **Outer Sphere**, where the TF forms up
  (even if it is detected and engaged). A **surprise jump** straight into the **Inner Sphere** is
  allowed but risky. ⚠️ No mechanics are given. (D17)

## §14.8 Fleet Intelligence

### §14.8.1 Sensor Nets

* Each net point costs **250 Ore + 500 credits + 100 production points**. **Max 5 points per system.**
* Detecting a task force: **1d10 ≥ 7**, modified by:

| Modifier | Condition |
|---|---|
| +1 | per Sensor Net point **after the first** |
| +2 | the enemy TF **formed a jump point** |
| +1 | per enemy **ELINT intelligence-gathering bonus** (max +4) ⚠️ undefined |
| +2 | a friendly **Patrol with ELINT** support is present |
| −2 | a friendly **Patrol without ELINT** support is present ⚠️ (worse than no patrol?) |
| −4 | the enemy TF is **all ELINT ships** |
| −2 | the enemy TF mixes ELINT and non-ELINT ships |
| −1 | **all** enemy ships have jammers |
| −4 | an enemy **listening post** is present |

**Procedure when a system is attacked:**

1. The defender rolls for the **Sensor Net**.
2. Any **Patrol TF** in the system rolls next (§14.5.9).
3. **Neither succeeds:** all planetary defenses start the battle at **Patrol Stations**, and **no
   Patrol TF may take part**.
4. **Either succeeds:** the patrol and the **nearest planet's** defenses go to **Battle Stations**.
   The patrol may **intercept** the invader before it reaches the planet, or **fall back** to
   reinforce the defenses.

⚠️ Whether a system with **no** net points rolls at all is not said. (D18)

### §14.8.2 Listening Posts

* May be placed **regardless of treaties**.
* Detected by the Sensor Net rules, plus extra rolls by patrolling TFs.
* Gather intelligence **as an ELINT ship on Intelligence Gathering orders**.
* ⚠️ Cost, build time and maintenance are not given. (D18)

## §14.9 Combat

Attacking task forces are at **Battle Stations**.

| Location | Map |
|---|---|
| **§14.9.1 Outer Sphere** | Four 20 × 32 maps as one **40 × 64** area. **Fixed map if a Jump Gate is present, floating otherwise.** |
| **§14.9.2 Inner Sphere** | "Eight 20 × 32 maps" as one **80 × 128** area ⚠️ (80 × 128 needs 16 maps, not 8; D19). One short edge is the **planet's atmosphere**. **Shipyards within 3 hexes** of it; **bases and OSATs within 20 hexes**. For an **asteroid belt** colony, the battle is inside the field: stationary units in the **middle** of the map, with one hex marked as the **hub colony**. |
| **§14.9.3 Hyperspace** | "(Not completed)" in the source. ⚠️ (D19) |

### §14.9.4 Reinforcements

* An attacking TF may hold part of its force **in hyperspace** as reinforcements. They use the normal
  jump-in rules but **cannot enter until 1d3 + 3 turns have passed**.

### §14.9.5 Ramming

* A **defender** may ram if the attacker's combat points are "at least 150% greater" than its own
  ⚠️ (150% of, or 250% of? D20), **or** if its empire has **lost at least ⅓ of the systems** it held
  when the war began.

### §14.9.6 Fighters from Fighter Bases

* Fighters **docked to resupply** cannot reinforce.
* **Atmospheric** fighters on a **ground base** wait **1d3 + 3 turns** before entering. Fighters on an
  **orbital** base have **no delay**.

### §14.9.7 Mine Warfare

* **Mining a Jump Gate is against the rules of war.**
* Mines defending a planet **must have IFF or be command-controlled**.

## §14.10 Experience

* Track each crew's XP for the whole campaign.
* **Every 1,000 XP → one roll on Table 20** (ships) or **Table 21** (fighters). A ship may have
  **only one expert of each type** (re-roll duplicates). A fighter flight may have **up to 4 Expert
  Pilots**.
* **2,500 XP → Elite crew. 5,000 XP → Ultra Elite crew** (§11.4.23).
* A crew moved to a ship that is **not a variant of its original ship** has its XP **halved**.

**Table 19 — XP awards and penalties**

| Event | XP |
|---|---|
| Fought in a battle | +100 |
| On the winning side | +100 |
| Extreme valor | +100 × 1d3 |
| Ship crippled (primary section at 50% loss, or 2 sections destroyed) | −100 × 1d3 |
| Task force outclassed (enemy has ≥ 150% of its combat points) | **double** XP |
| Enemy outclassed (own TF has ≥ 150% of the enemy's) | **no** XP |
| Defending refugees | **double** XP |
| Training Maneuvers (§14.5.13) | +10 per TT |
| Raid orders (§14.5.10) | none |

**Table 20 — Ship expert officer (1d10)**

| d10 | Officer | d10 | Officer |
|---|---|---|---|
| 1 | Expert Helmsman (§11.4.1) | 6 | Expert Jump Officer (§11.4.6) |
| 2 | Expert Engineer (§11.4.2) | 7 | a Special Elite Officer (§11.6) |
| 3 | Expert Scanner (§11.4.3) | 8 | Lucky Captain (§11.8) |
| 4 | Expert Navigator (§11.4.4) | 9 | "((Expert Tactician?))": **placeholder, undefined** ⚠️ (D21) |
| 5 | Expert Technician (§11.4.5) | 10 | Expert Ship Captain (§14.10.1) |

**Table 21 — Fighter expert officer (1d8)**

| d8 | Officer | d8 | Officer |
|---|---|---|---|
| 1 | Expert Dogfighter (§11.5.1) | 5 | Expert Coordinator (§11.5.5) |
| 2 | Expert Motivator (§11.5.2) | 6 | Expert Electrician (§11.5.6) |
| 3 | Expert Missileer (§11.5.3) **or Pilot** | 7 | Expert Pilot (§11.5.7) |
| 4 | Expert Evader (§11.5.4) | 8 | Expert Tailgunner (§11.5.8) **or Pilot** |

("or Pilot" presumably applies when the flight has no missiles or no tail gun.)

### §14.10.1 Expert Ship Captain (campaign-only officer)

* After all ships are set up, the player may change **every ship on his side's starting speed and
  position by up to 10 hexes**.
* The ship he commands gets **+1 initiative** and **wins all initiative ties**, including against an
  Expert Helmsman.
* Located in **C&C**. ⚠️ No point cost, as it is only earned through XP.

## §14.11 Repairs

* **Criticals** are repaired automatically after combat, **unless the ship is out of maintenance**.
* **Damaged fighters and shuttles** that survive and return are restored to 100% **for free**.
* **Destroyed boxes:** the ship docks at a **shipyard of the right size**; **1 repair point per
  box**. A repair point costs **1 credit + 1 Ore** (plus 1 production point, §14.3).
* Rate: a **shipyard repairs up to 50 boxes per TT** regardless of size; a **base 20 per TT**.
* **Permanently reduced armor:** the **whole location's armor** must be replaced, at **1 repair point
  per box in that location**.
* ⚠️ Replacing **lost** fighters and shuttles is not covered. (D22)

## §14.12 Refits

* Converts a ship to **one of its variants** at a shipyard of suitable size. Takes **½ its original
  build time (minimum 1 ST)**, during which the yard does nothing else. Dry docks follow the same rule.
* ⚠️ Refit **cost** is not given. (D22)

## §14.13 Salvaging Ships

* A **derelict** whose **primary section and reactor survived** can be salvaged.
* Salvage takes **1 TT** and needs a **tug** (a civilian SCS available to everyone) to tow it off.
* Races that don't build their own ships may **repair and refit** salvage. Others may **sell or
  destroy** it.
* Battlefield clean-up: **1 ST, plus 1 ST per 4,000 combat points of derelicts**.

## §14.14 Conquering Systems

### §14.14.1 Planetary Bombardment

* Requires control of the **Inner Sphere**. Total the **Bombardment Points (BP)** and roll **1d10 per
  BP** on Table 22.
* BP per unit: MCV **2** · HCV **4** · Capital **6** · OSAT **4** · atmospheric fighter **1**.
  **+2** if the unit has matter-type weapons. **+8** for a weapon of mass destruction dealing more
  than 100 damage. A **Mass Driver** counts as **+10**.
* Each defending **ground fighter** removes **1 BP**, but no more than the number of **bombarding
  fighters**.
* ⚠️ LCV and enormous BP are not listed. (D23)

**Table 22 — Bombardment hits (1d10)**

| d10 | Hit | Effect |
|---|---|---|
| 1–2 | Ground defense | 1d2: −1 **Planet Defense** value, or −1 **Ground Battery** value |
| 3–4 | Fighter bases | **1d10** fighters or shuttles destroyed |
| 5–7 | Factory / training / storage | 1d3 picks which; everything stored, in production or in training there is **lost** |
| 8–10 | Civilians | **−1d3 current Pop** |

* A civilian hit also reduces the planet's available **maintenance by 100** and its **production by
  25%**. This is **temporary**; maximums are unchanged. ⚠️ Duration and stacking are not stated. (D23)

### §14.14.2 Mass Destruction

* **500 BP** makes the planet **Barren**. It may be delivered in one TT or over **consecutive** TTs;
  **any break resets** the count to zero.

### §14.14.3 Planetary Assault — Drop Phase

* Roll **1d10 for each** assault shuttle, fighter or atmospheric ship landing, **+1 per Ground Battery
  point**. On **8+** it is hit: a shuttle or fighter is **shot down**; an atmospheric ship takes one
  hit (random facing, then hit location, with damage from the defender's ground-battery weapon,
  chosen before play).
* An atmospheric ship that takes a **critical** to thrusters, engine, C&C, sensors or reactor, or is
  destroyed, **crash-lands**: it takes **ram damage against itself** (if not already destroyed) and
  **all marines aboard die**.

### §14.14.4 Planetary Assault — Assault Phase

* Each side rolls **1d10**. The attacker adds **+1 per marine**; the defender adds **+1 per planetary
  defense point**. Further modifiers:
  * **+1** to whoever won the orbital battle.
  * **+2** attacker, if orbital ships support the assault.
  * **+1** defender, if it still holds **at least one orbital base**.
* **8+ → the opponent loses one marine or one defense point.**
* If both sides still have forces, a **new Drop Phase** follows next TT.
* The defender may **reinforce** unless the attacker has a **Blockade** in place; then the defender
  needs **Blockade Run** orders first.
* ⚠️ With +1 per marine, a large force hits automatically every round but still kills only one
  defender per round. (D24)

### §14.14.5 Mixed Population

* After a conquest the occupier moves its own people in. Track **native** and **occupier**
  population separately. The **first** growth point goes to the occupier; each later point goes to
  whichever population the occupier chooses.

### §14.14.6 Genocide

* Removes the native population at **1 ST per 5 Pop**. At 0 natives, the occupier's Pop becomes **1**
  and the planet is entirely theirs. Gives **−2** to the guerrilla roll (and +6 unrest to the victim
  empire, §14.17).

### §14.14.7 Guerrillas

* **Every TT** a planet is occupied, a guerrilla force forms on **1d10 ≤ 8**. The threshold drops
  **by 1 per ST** (minimum **2**).
* Guerrillas stay hidden until they **attack**, **disrupt production**, or the occupier **fails an
  espionage check** ⚠️ (espionage is undefined, D25).
* **Attack:** both sides roll **1d10 + 1 per marine / guerrilla**; guerrillas **−1**. On **8+** the
  opponent loses one.
* **Disrupt production:** **−1d6** production on the planet that turn.
* Once guerrillas are revealed, marines may attack them on **1d10 = 1**, using up to **25% of their
  force** as the modifier.
* If guerrillas wipe out **all occupying marines**, the planet returns to **native control**.

**§14.14.7.1 Third occupier:** when a mixed-population planet is taken by a **third** empire, both
the natives' and the first occupier's guerrillas act. If the third empire's marines are wiped out,
the planet goes back to the **first occupier**, and **half its guerrillas become marines**.

### §14.14.8 Refugees

* After a planet is conquered, the **other colonies in that system** produce refugees the next turn.
* If there is combat at one of those planets, **each has 1d6 + 6 transports** trying to escape during
  the battle, **−1 per TT** (maximum **−4**).
* The exodus ends after **4 TTs**. A new conquest in the system restarts it.
* Attackers earn **no XP** for killing civilians; defenders earn **extra XP** (Table 19).

## §14.15 First Contact

* When two empires first meet, roll **1d10**:
  * **+1** for communications problems.
  * **−1** if **both** fleets are on **Exploration** orders ⚠️ (no such order exists; D26).
  * **+2** for an **Aggressive** empire; **−2** for a **Pacifist** one.
* **≤ 5: peaceful.** Otherwise **hostile**: a battle at **Cruise Stations**, with only **25% of each
  side's fighters** launched at the start.
* The two empires **cannot communicate** until contact is peaceful or the battle is resolved.

## §14.16 Research

* Each race's starting ships are limited by **ISD** (in-service date).
* **100 research points = +1 tech year. 1 research point = 30 credits.**
* **§14.16.1 Research Center:** **500 Ore + 1,500 credits, 4 ST, maintenance 50**; produces
  **1d6 research points per turn**. ⚠️ Per TT or per ST? (D27)
* **§14.16.2 Joint Research:** two empires not at war each gain **1d3 RP per ST**, rolled separately.

## §14.17 Civil Unrest

* Empires start at **20 unrest**. Calculated at the **end of each ST**, empire-wide.

| Event | Unrest | Event | Unrest |
|---|---|---|---|
| Losing the home world | +10 | War declared **on** the empire | −1d3 |
| Weapon of mass destruction used on the empire | +8 | Capturing a colony | −1 |
| Genocide of a captured colony | +6 | Tech breakthrough | −1 |
| Sanctions by 3+ empires | +5 | Peaceful first contact | −1 |
| Losing a colony | +4 | Rebellion put down | −1 |
| Leader assassinated | +3 | Protecting shipping from raiders | −1 |
| Breaking a treaty | +2 | New trade route | −1 |
| Rebellion | +2 | Winning a significant battle (4,000–5,999 pts) | −1 |
| Losing a major battle (6,000+ pts) | +2 | Winning a major battle (6,000+ pts) | −2 |
| Losing a significant battle (4,000–5,999 pts) | +1 | New colony | −2 |
| Declaring war | +1d6 | Liberating a colony | −2 |
| | | Signing a treaty | −2 |

The referee has discretion; when in doubt, use 1d3 as the modifier.

### §14.17.1 Production effects

* At unrest **24**: **−5% production**, plus a further **−5% per 2 points above 24**.
* At unrest **6**: **+5%**, plus a further **+5% per 2 points below 6**.

### §14.17.2 Civil War

* At unrest **≥ 30**: roll **1d20 + 1 per point above 30**; **15+ → civil war.**
* The player picks a side; the **referee** plays the other.
* The first rebel system is **random**. The **home world always stays loyal**. Ships at the rebel
  colony join the rebels.
* Every other ship and colony rolls **1d6**: **1 rebels · 2–4 unconvinced · 5–6 loyal**.
* **Unconvinced** units can be swayed at the start of each combat turn on **1d20 ≥ 17**, and then sit
  out the battle as non-combatants. A unit **damaged by rebels** can no longer be swayed. A colony not
  under rebel attack can be swayed on **17+**, once per TT.
* The rebels then become a **separate empire**.

## §14.18 Espionage

Heading only; **no rules in the source.** Agents are trained (§14.3.3) and receive orders
(Appendix C), and guerrillas refer to an "espionage check", but none of it is defined. ⚠️ (D25)

## §14.19 News Reports

* New trade routes and wars are broadcast. The referee may run a news service summarising events for
  all players.

## §14.20 Cargo

| Cargo | Boxes |
|---|---|
| Ore, HA Ore, credits, repair points, supply points | 1 per unit |
| Ship missiles | 1 per missile |
| Fighter missiles | 2 per box |
| Fighters | the fighter's **Ram factor** |

## §14.21 Diplomacy

* Happens in the **diplomacy phase of each TT**. Players act as **ambassadors**, who must be present
  to negotiate.
* Ambassadors must **physically travel** to the agreed meeting place. With no neutral base, they
  return to the home world.
* ⚠️ Ambassador movement and treaty mechanics are not defined. (D28)

## §14.22 Economic Tiers (starting resources)

| Tier | Credits | Ore | HA Ore | Colonies (systems) | Shipyards | Factories |
|---|---|---|---|---|---|---|
| 1 Major | 22,500 | 18,750 | 50 | 7 (3) | 3 large | 3 large |
| 2 Median | 15,000 | 12,500 | 20 | 5 (2) | 2 large | 2 large |
| 3 Minor | 7,500 | 6,250 | 10 | 3 (1) | 2 medium | 2 medium |
| 4 Special | 1,500 | 1,500 | 5 | 1 | 1 small | 1 small |

The tier also affects war devaluation (§14.2). ⚠️ No starting fleet, population or tech year is
given. (D29)

## Appendix C — Campaign Turn Sequence (p. 288)

**Tactical turn** (4 per strategic turn)

* **Order Phase**
  1. Check for raider activity
  2. Supply points and marines transfer to or from the ground
  3. Shuttles and fighters transfer between units
  4. Task forces receive orders
  5. Agents receive orders
  6. Guerrillas receive orders
  7. Empires negotiate
* **Action Phase**
  1. Sensor Net rolls made and revealed
  2. Agent actions resolved
  3. Raider activity resolved
  4. Task force **movement** orders resolved
  5. Task force **attack** orders resolved (a full Combat Sequence, i.e. a normal battle)
  6. Planetary bombardment resolved
  7. Assault orders resolved
  8. Guerrilla orders resolved

**Strategic turn**

* **Upkeep Phase**
  1. Check for random events ⚠️ (undefined)
  2. Government options take effect ⚠️ (undefined)
  3. Production and training orders complete
  4. Research orders complete
  5. Systems produce credits, Ore, HA Ore, maintenance and food
  6. Trade routes produce credits
  7. Check for starvation (food)
  8. Check for disrepair (maintenance)
  9. Supply lines resupply
  10. Check colony status ⚠️ (undefined)
* **Orders Phase**
  1. Place production orders
  2. Place training orders
  3. Choose government options

⚠️ §14.4.6 rolls raids "each strategic turn", but Appendix C checks raiders every tactical turn. (D8)

---

# PART C — Rules from other chapters that the campaign rules depend on

Summarised from the same book so the plan doesn't need to reopen the PDF.

## §12.4.1 Crew Readiness (Battle Stations unless stated otherwise)

| State | Initiative | Weapons and fighters at the start |
|---|---|---|
| **Battle Stations** | normal | Fully armed, fighters ready. Sustained-mode weapons do **not** start armed unless the scenario allows it. |
| **Patrol Stations** | **−3** on turn 1, improving by 1 per turn (zero by turn 4); does not apply to launched fighters | Each weapon and each fighter flight rolls **d20: 1–10 ready**. A failed weapon starts unpowered but may begin arming on turn 1. A failed flight's pilots arrive after **1d6 turns**. A ship with more than one flight always has at least one ready. |
| **Cruise Stations** | **−6** on turn 1, improving by 1 per turn (zero by turn 7) | Each weapon and flight is ready on **d20: 1–5**. Failures re-roll each turn with the range widened **+2 per turn** (1–7, then 1–9, …). A weapon cannot recharge, and a flight cannot launch, until it passes. Pilots also take **1d6 turns** to arrive, running at the same time. |
| **Drill Stations** | normal | Armed as Battle Stations, but at **drill level**: every damage die rolls as **1**, **no criticals**, missiles carry **dummy warheads**. After **1d6 turns** (one roll per ship), each weapon must be shut off for **1 turn** before it can arm live; fighters must spend 1 turn aboard. Anything not cycled keeps firing at drill level. |

## §12.4.2 Tactical Surprise

**Surprised side, turns 1–2:**

* **Every** system that uses power (even 0) is switched off, and that power cannot be used.
* Fighters cannot arm or fire; their offensive bonus is **halved** (round up).
* Engines give only **½ free thrust** (round up); this applies to fighters and shuttles too.
* **No launches** (landing is allowed) and no other hangar operations.
* Surprised units **automatically lose initiative**.

**Attacker benefits, turns 1–2** (only if the attacker is ready):

* Sustained-mode weapons may start armed.
* Called shots at **−4** instead of −8; called shots on **structure** allowed at **−8**.
* Damage dice: a **d10 below 3 counts as 3**, a **d6 below 2 counts as 2**.

**Recovery from turn 3:** **+25% of reactor power** returns each turn (round .5 up, less reactor
criticals); **+2 free thrust** per turn; launches and hangar operations allowed; initiative **−4** on
turn 3, improving by 1 per turn. Attacker benefits end.

*Example (12 free thrust, 47 power):*

| Turn | Thrust | Power | Initiative |
|---|---|---|---|
| 1–2 | 6 | 0 | loses automatically |
| 3 | 8 | 12 | −4 |
| 4 | 10 | 24 | −3 |
| 5 | 12 | 35 | −2 |
| 6 | 12 | 47 | −1 |
| 7 | 12 | 47 | normal |

## §12.3.4 Disengagement (needed for "who won", retreat and pursuit)

* **Voluntary:** spend a full turn accelerating at maximum safe thrust; the unit may then disengage
  once it is **out of range of all enemy weapons and at speed ≥ 20**.
* **Forced:** a player's forces are disengaged if **all** of them are **more than 50 hexes from the
  map centre, more than 25 hexes from the nearest enemy, and heading away**.

## §9.2.1 Variant deployment (referenced by §14.3.7)

* **Uncommon:** at most 1 in 3 of a class. **Rare:** at most 1 in 9.
* Fighters count per flight, per squadron.
* **In campaigns these limits apply to construction only.**

## Officers and crew quality (§11.4–11.8)

The Table 20 / 21 results and Elite / Ultra Elite crew (§11.4.23) are the same officers and crew
qualities Fiery Void already implements (Part E). Some officers have **campaign-only clauses** that
FV deferred because there was no campaign layer; see `OFFICERS_PLAN.md` around line 1312 (e.g. the
Expert Scavenger's permanent post-battle repairs).

---

# PART D — Gaps, contradictions and ambiguities to resolve before planning

Numbers match the ⚠️ references above.

1. **Two campaign systems.** §13 and §14 are independent. Choose a base: §14 is far more complete but
   has more holes; §13 is simpler and closer to a hex-by-hex map game.
2. **"Tactical turn" means two things in §14:** the campaign turn (4 per ST) and, in several places,
   a **battle** turn (reinforcement delay, ground-fighter delay, refugee escape, salvage, shipyard
   repair rate). Each use needs a decision.
3. **§13.1 economy undefined:** EP/CP income, the hull-size limit, and "CP" clashing with Combat Points.
4. **Table 16** has a duplicated row and a gap at EW = 12.
5. **§13.7 is truncated** ("If this results…") and gives no scatter distance. The Endurance section is
   misnumbered "12.8". Enormous and base endurance are missing.
6. **Mineral deposits:** max vs current Pop; HA roll per body or per system; HA deposit size; a stock
   that runs out vs income per turn; how Ore is actually extracted each ST.
7. **The colony model is unwritten:** exact meaning of Economic, Maintenance and Defense values;
   starvation; food shipping; population growth (Growth Value gives only a rate); how a colony is
   founded and what it costs; "colony status".
8. **Timing conflicts:** raids per ST (§14.4.6) vs per TT (App C); war devaluation "any time".
9. **HA Ore use by jump engines** is implied (§14.2.2) but has no rate.
10. **Hyperspace navigation checks / getting lost** are mentioned three times and never defined. Also
    undefined: how new gates get lanes, and lane length.
11. **Ship build cost** ("production points cost") is undefined; fighters, shuttles and OSATs have no
    cost at all.
12. **Facility tiers:** are the lists cumulative? What builds large factories? Does a medium yard
    build enormous ships? What builds bases?
13. **Supply depots:** maintenance cost; chaining depot to depot.
14. **Intelligence Gathering / Survey rolls** mix a d10 roll-over modifier table into a d20 roll-under
    check; the direction of "ELINT +4" is unclear.
15. **Raid orders:** what the raiding player gains.
16. **Hyperspace speed** = "maximum acceleration without boosting or overthrusting". Needs a formula
    from FV ship data (free thrust ÷ acceleration cost? limited by thruster ratings?).
17. **Surprise jump into the Inner Sphere** has no mechanical risk or reward.
18. **Sensor nets / listening posts:** systems with zero net points; listening post cost and build.
19. **Battle maps:** 8 maps ≠ 80 × 128; hyperspace battles "not completed".
20. **Ramming threshold** "150% greater than" (150% of, or 250% of?).
21. **Table 20 roll 9** "((Expert Tactician?))" is a placeholder.
22. **Repairs:** replacing destroyed fighters and shuttles; refit cost.
23. **Bombardment:** LCV and enormous BP; duration and stacking of the civilian-hit penalty.
24. **Ground combat** removes one loss per side per round with linear bonuses; probably needs
    redesign.
25. **Espionage and agents** are missing entirely, and guerrilla detection depends on them.
26. **"Exploration" orders** in First Contact do not exist in the order list.
27. **Research Center** yield per TT or per ST; how a new tech year unlocks ships in FV terms (ISD).
28. **Diplomacy, treaties, sanctions and trade-route creation** are flavour only.
29. **Campaign set-up:** §14 gives no starting fleets, populations, tech year or victory conditions.
    "Random events" and "government options" (App C) are undefined.

---

# PART E — Existing Fiery Void features to build on

Pointers only; verify each against the code before planning.

* **Pre-Battle Damage / fleet damage persistence:** `PREBATTLE_DAMAGE_PLAN.md`,
  `source/server/model/PreBattleDamage.php`, `source/public/client/battleDamage.js`, the
  `tac_saved_list` table (`db/prebattleDamage.sql`) and `savedFleets.saveCurrentFleet()`. This is the
  closest existing feature to carrying a damaged fleet from one battle to the next; the plan already
  mentions continuing a campaign. `tac_saved_list` rows have no expiry (`WEAPON_ENHANCEMENTS_PLAN.md`
  around line 581).
* **Officers:** `OFFICERS_PLAN.md`; campaign-only officer clauses were deferred (around line 1312).
  Table 20/21 rolls map onto these officers.
* **Elite / Poor crew:** `$crewQuality`; the XP thresholds for Elite and Ultra Elite would set it.
* **Readiness / surprise:** check whether any scenario option already models Patrol, Cruise or Drill
  Stations or Tactical Surprise before building them.
* **Also relevant:** Reinforcements / jump-in, Jump Points and Jump Gates, Hyperspace improvements,
  Mines, Save Orders.
* `README.md` around line 433: avoid changing a rule in the middle of a campaign.
