# Strategic AI Plan (NPC empires on the campaign map)

Status: **PLANNED 2026-11-12. Nothing built.** This is Stage 11 of `CAMPAIGN_PLAN.md` written out.
It cannot start before campaign Stages 0-7 exist (it drives the campaign's own actions).
Companion: `TACTICAL_AI_PLAN.md` (the CPU that plays the battle itself; independent of this plan).

Terms follow `CAMPAIGN_PLAN.md`: ST = Strategic Turn, empire, system, credits, Q-40, AutoCombat,
`CampaignView`, `CampaignConfig`. "Campaign 5.2" means section 5.2 of that plan.

---

## 1. What it covers

Three kinds of non-player empire (`camp_empire.kind`), in build order:

| Kind | What it is | Complexity |
|---|---|---|
| RAIDER | One shared empire. Fleets appear, harass, raid income, leave or die | Low - a script |
| NEUTRAL | One per neutral system: an FV faction holding a single hex with a defence fleet; reacts through favour | Low - mostly diplomacy rules |
| CPU | A full rival empire: expands, builds, negotiates, fights | High - the real strategic AI |

It does **not** cover fighting a battle (that is AutoCombat, or the tactical AI when available -
campaign section 7).

---

## 2. Principles

| # | Principle |
|---|---|
| S1 | **An NPC submits actions, it does not edit state.** Every decision becomes the same action a player POSTs (`fleet.move`, `queue.add`, `diplo.propose`, ... campaign section 9), run through `CampaignManager::applyAction` with the NPC's empire as the actor. Same validators, same costs, same Q-40 charges, same event log. The only NPC-only code paths are Raider spawning and the Raider bounty. |
| S2 | **No cheating by default.** A CPU empire plans from its own masked `CampaignView` (fog of war included). Difficulty is decision quality; the optional handicap is an income multiplier that the campaign set-up screen shows. Raiders and neutrals are scripted and may use global facts where the rules say so (Raider fleet size uses the players' average fleet value - that is a rule, not a peek). |
| S3 | **Deterministic.** All randomness comes from a generator seeded by campaign seed + ST + empire id, so a resolution that is interrupted and resumed (campaign 3.4) makes the same choices. |
| S4 | **Cheap and bounded.** Runs inside resolution step 1, one empire per step unit, each idempotent: an empire whose orders for this ST are already LOCKED is skipped. Target: well under a second per empire on a Large map. |
| S5 | **Explainable.** Each decision records its top-scoring option and the score terms in `camp_event` (visible only to admins), so odd behaviour can be diagnosed from the data. |
| S6 | **Never stalls the campaign.** An NPC is always "ready". If its planner throws, the error is logged and the empire passes the turn. |

---

## 3. Architecture

```
TurnResolver step 1 (campaign 5.2)
  for each NPC empire, in id order:
     StrategicAI::planTurn(campaign, empire)
        view   = CampaignView::build(campaign, empire)        // masked, same builder as for players
        brain  = RaiderBrain | NeutralBrain | EmpireBrain     // by kind
        actions = brain.decide(view, rng)
        foreach action: CampaignManager::applyAction(empire, action)   // rejected -> logged, skipped
        mark empire ready
```

Source: `source/server/campaign/ai/` - `StrategicAI`, `RaiderBrain`, `NeutralBrain`, `EmpireBrain`,
`InfluenceMap`, `Goals` (one class per goal), `BuildPlanner`, `DiplomacyBrain`, `StanceChooser`,
`StrategicAIConfig` (every weight), `Personalities`.

Shared services it calls: `AutoCombat::estimate()` (a dry run that returns win probability and
expected losses without changing state - add this entry point when AutoCombat is built, campaign
Stage 6), `FleetComposer` (TACTICAL_AI_PLAN section 8), `CampaignPricing`.

No new tables. Per-empire memory lives in a JSON column `camp_empire.ai_state` (standing goals,
grudges, last known enemy fleet positions with the ST they were seen).

---

## 4. Raiders (`RaiderBrain`)

Rulings (user, 2026-11-12): Raiders **spawn in unclaimed systems, at irregular intervals**, not at
the map edge; a Raider fleet is **just under the average player fleet** in value; **defeating one
pays a credit reward**.

* **Spawn.** Each ST, if fewer than `RAIDER_MAX_FLEETS` (default: 1 per 3 player empires, minimum 1)
  exist and at least one unclaimed system exists: spawn with probability `RAIDER_SPAWN_CHANCE`
  (0.25), rising by 0.1 for every ST since the last spawn - irregular, but never absent for long.
  No spawns before ST `RAIDER_GRACE` (5).
  The system is chosen at random among unclaimed systems, weighted toward those 2-5 hexes from a
  player-owned system and excluding any hex a player fleet currently occupies or sees at range 1
  (they emerge from the dark, not on top of a patrol). If no unclaimed system exists Raiders stop
  spawning - a fully claimed map has driven them out.
* **Size.** Fleet value = `RAIDER_SIZE_FACTOR` (0.9) x the mean value of all player fleets
  (fleets, not garrisons; minimum `RAIDER_MIN_VALUE` 500). Composed by `FleetComposer` from the
  generic `raiders` faction with the `raider pack` template at the campaign ISD. Raider fleets
  always include a jump-capable unit (they move off-lane) and pay no credits or Q-40.
* **Raiders never attack** (ruled 2026-11-12). Their only hostile act is **Raid System**. They never
  create a contact, never choose an attacking stance and never claim. They can be attacked: campaign
  5.2 offers every empire sharing their hex Attack / Ignore.
* **Target choice.** Score each system owned by a **player or CPU empire** within `RAIDER_RANGE`
  (6) hexes: `income x (1 - defence_ratio)`, where `defence_ratio` comes from an
  `AutoCombat::estimate` against what the Raiders can see there (how likely they are to be driven
  off). **Neutral systems are never targets**; nor are systems of this ST's tribute-exempt empire
  (campaign 5.5). Raiders see like any fleet (range 1), so they scout as they move and re-plan each ST.
* **Behaviour loop.** Move toward the target -> on arrival **raid** (campaign R7: 50% of that ST's
  income) for `RAIDER_STAY` (1-3) STs, then pick a new target. They avoid hexes where a clearly
  superior force is visible, and move away from one that arrives. After `RAIDER_LIFETIME` (8-12) STs
  the fleet withdraws to the nearest unclaimed hex and despawns.
* **When attacked** (`StanceChooser`, section 6): fight when the estimate is >= 0.55, otherwise
  retreat. A Raider fleet that retreats or is destroyed in an ST raids nothing that ST.
* **Bounty.** When Raider units are destroyed in a battle, the opposing empires receive
  `RAIDER_BOUNTY` (25%) of the destroyed value in credits, split by damage share (manual battle:
  from `tac_damage` rows per shooter's owner at harvest) or strength share (AutoCombat). Written by
  `BattleBridge::harvest` / `AutoCombat` as a `camp_event` so it shows in the turn report.
* **Tribute.** Bids are collected in the players' orders; at step 2 the highest bidder becomes exempt
  for the next ST and all bids are spent. Ties: nobody is exempt.
* Raider income from raids is not banked; it only matters to the victim.

---

## 5. Neutral systems (`NeutralBrain`)

* **Set-up** (map generation): `NEUTRAL_SHARE` (default 10%) of non-homeworld systems, never within
  3 hexes of a homeworld, each given a random FV faction from an allowed list (a campaign setting;
  default: the minor and league factions) and a garrison composed by `FleetComposer`
  (`defence group` template, value `NEUTRAL_GARRISON_FACTOR` 1.2 x starting player fleet value,
  including a base where the faction has one).
* **Behaviour.** Never moves. Each ST rebuilds lost garrison value at `NEUTRAL_REBUILD` (10% of its
  cap per ST). Always chooses the Fight/Auto stance when attacked. A conquered neutral system is an
  ordinary claim (2 STs after its garrison is destroyed) and its empire is eliminated.
* **Favour** (`camp_favour`, hidden, -100..100, starts at a faction-pair baseline from
  `Personalities`): 
  * rises with gifts (diminishing returns per ST), with a trade route, with destroying Raiders
    nearby, slowly with time at peace;
  * falls sharply when attacked or raided, moderately when a player attacks another neutral or an
    empire the neutral is friendly with, slightly when warfleets sit adjacent.
  * Thresholds: NAP at >= 20, Friendship at >= 50, Alliance at >= 80 (it will accept that proposal;
    below the threshold it refuses, with a flavour message). Below -50 it declares war on its own.
  * The value is never sent to players; they see only an attitude word (Hostile / Wary / Neutral /
    Cordial / Friendly / Devoted), as in Birth of the Federation.
* **Alliance benefit.** An allied neutral shares vision of its hex and neighbours, grants the trade
  route bonus, and its garrison fights beside the ally in its own hex.
* **Membership** (ruled in, 2026-11-12; the Birth of the Federation option). When an allied
  neutral's favour toward an empire has stayed at `MEMBERSHIP_FAVOUR` (95) or more for
  `MEMBERSHIP_TURNS` (3) consecutive STs, it offers to join (a `camp_proposal`, with a flavour
  message). On acceptance, at step 2 of the next resolution:
  * the system changes owner with its population, upgrades and queues intact - no claim period;
  * its garrison units become the empire's own `camp_unit` rows, keeping their class and damage.
    They are another faction's ships: usable, repairable and resuppliable, but the empire's build
    list stays its own faction (default; D-S8);
  * the neutral empire is removed; its other treaties lapse.
  If two empires qualify in the same ST the higher favour wins; a tie waits a turn. The offer stands
  until accepted or until favour drops below the threshold. CPU empires pursue and accept membership
  like players (a `Befriend` goal in 7.2 scored on the neutral's value against gift cost).

---

## 6. `StanceChooser` (all NPC kinds)

Called by `TurnResolver` in the BATTLES phase for each NPC side of a contact.

* `AutoCombat::estimate(sides)` -> win probability `p` and expected own loss share.
* Fight if `p >= fightThreshold` (by personality: aggressive 0.40, balanced 0.50, cautious 0.60);
  a defender of its own system fights at 0.15 lower; a fleet that cannot retreat always fights.
* Otherwise retreat. An NPC never offers or needs "both retreat" bargaining.
* Manual or automatic: the NPC accepts whatever the human chooses, subject to campaign section 7
  (manual only if the tactical AI can play that faction).
* Hold in hyperspace (campaign R12a): none in v1; later a personality option to hold a reserve.
* Mid-battle auto-resolve offers from a human are accepted when the current estimate is within
  0.15 of even or in the NPC's favour.

---

## 7. CPU empires (`EmpireBrain`)

A utility-based planner. Each ST: update knowledge, score goals, assign fleets, spend the budget,
do diplomacy.

### 7.1 Knowledge

* `InfluenceMap` over hexes from the masked view: own strength, known enemy strength (with decay by
  age of sighting, from `ai_state`), system value (income + Q-40 + minerals + position), frontier
  distance. Recomputed each ST; a Large map is under a thousand hexes.
* Paths use the campaign's own movement rules via the server's legal-move answers (lanes at 3
  hexes, off-lane only for fleets with a jump-capable unit).

### 7.2 Goals

Each goal scores candidate targets and asks for a force; the allocator fills the highest
score-per-strength first.

| Goal | Target | Score drivers |
|---|---|---|
| Expand | unclaimed system | value / distance; unopposed claims first |
| Defend | own system under threat | system value x threat seen within 3 hexes; homeworld weighted x3 |
| Attack | system of an empire at war | value x win estimate / distance; prefers isolated, weakly held systems |
| Intercept | enemy fleet seen near own space | threat x win estimate |
| Raid | enemy system it cannot hold | income x (1 - defence), when attack scores poorly |
| Hunt Raiders | Raider fleet in reach | bounty x win estimate |
| Regroup / Repair | damaged fleet | damage share; sends it to the nearest own base or shipyard and queues repairs |
| Garrison | recently taken system | until the claim completes |

Fleets are merged when a goal needs more strength than one fleet has, and split to leave a home guard.
A fleet in a paused battle (campaign 6.3) is reinforced or withdrawn by the same scoring: reinforce
when the estimate with reinforcements is favourable, otherwise pull out the units that reached hyperspace.

### 7.3 Economy and building (`BuildPlanner`)

* Budget split by personality and situation into economy / defence / fleet (for example balanced at
  peace 40 / 15 / 45; at war 15 / 20 / 65), re-weighted when income is low or a system is threatened.
* Economy: upgrades by payback time (cost / added income per ST); Industrial Investment and a second
  shipyard when the queue is saturated; a jump lane (2 Q-40) between its two highest-traffic systems
  when Q-40 allows and the lane shortens a defence path.
* Defence: a base for the homeworld and frontier systems once income >= 150 there; OSATs up to the
  population cap on threatened systems.
* Fleet: `FleetComposer` picks the purchase list for the fleet share; `CampaignPricing` prices it;
  **Q-40 is a hard constraint** - each jump-drive unit costs 1, so the planner keeps a reserve of
  `AI_Q40_RESERVE` (2) for a lane and buys jump-less escorts when Q-40 is short (they must then
  travel with a jump-capable unit or along lanes - the fleet assigner respects that).
* Ordnance stock and resupply are bought when a fleet returns below 50% ammunition.

### 7.4 Diplomacy (`DiplomacyBrain`)

* Attitude toward each known empire = baseline from `Personalities` (faction pair) + history in
  `ai_state` (attacks, broken pacts, gifts, shared enemies) + relative strength + border pressure.
* Proposes NAP to stronger neighbours and when fighting on another front; Friendship and trade when
  attitude is high and borders are quiet; Alliance against a common enemy that is stronger than both.
* Accepts or refuses a human's proposal by the same score, with a margin so it is not a pushover.
* Declares war when attitude is low, its win estimate against that empire's known forces is good and
  it has no other war; honours the one-ST notice on pacts; obeys forced mutual-defence wars.
* Sues for peace when losing systems for 3 STs running. Gifts: responds to tribute demands only on
  Easy; never spams proposals (at most one per empire per 3 STs); uses its one public declaration per
  ST for wars and alliances only.
* Messages are short canned lines per faction flavour, chosen from templates - no free text generation.

### 7.5 Personalities and difficulty

* `Personalities`: per FV faction a preset - aggression, expansionism, trust, preferred battle
  personality (passed to the tactical AI seat), baseline attitudes toward other factions (for
  example Narn and Centauri start hostile). A campaign setting can randomise them.
* Difficulty: Easy (slow to expand, 20% decision noise, poor concentration of force), Normal, Hard
  (better force concentration, uses estimates with reinforcements, targets the leading player).
  Optional income handicap per level (default 1.0 at all levels), shown at set-up.

---

## 8. Stages (inside campaign Stage 11)

| Stage | Deliverable | Done when |
|---|---|---|
| **S0** | `StrategicAI` loop in resolution step 1, NPC acting through `applyAction`, `AutoCombat::estimate`, `StanceChooser`, `camp_empire.ai_state`, simulation harness (section 9) | A scripted NPC moves a fleet each ST through the normal action path; an interrupted resolution repeats the same choices |
| **S1 Raiders** | spawn, composition via `FleetComposer`, target loop, raids, bounty, tribute | Raiders appear irregularly in unclaimed systems, raid, can be destroyed for a reward |
| **S2 Neutrals** | generation, garrisons, favour model, attitude word, treaty thresholds, membership | A neutral system can be befriended by gifts, brought into the empire as a member, or conquered |
| **S3 CPU expansion** | `InfluenceMap`, Expand / Defend / Garrison goals, `BuildPlanner` | A CPU empire grows on its own in an all-CPU simulation |
| **S4 CPU war** | Attack / Intercept / Raid / Regroup goals, fleet merge and split, paused-battle handling | CPU empires take systems from each other; simulations end with a winner |
| **S5 CPU diplomacy** | `DiplomacyBrain`, personalities, messages | Treaties form and break plausibly in simulation; a human can negotiate |
| **S6 Difficulty and single-player** | levels, handicap, campaign set-up options ("add CPU empire", Raiders on/off and frequency, neutrals share) | A single-player campaign is playable end to end |

S1 and S2 need only AutoCombat. Manual battles against any of these arrive automatically once
`TacticalAI::canPlay` answers yes for the faction (TACTICAL_AI_PLAN section 9).

---

## 9. Testing

* **Campaign simulation harness** (`tests/campaign/simHarness.php`, CLI): an all-NPC campaign
  (CPU empires + Raiders + neutrals, AutoCombat only) run for N STs from a seed. Asserts: no rejected
  action, no exception, no negative credits or Q-40, every ST resolves, determinism (same seed ->
  same final state hash, including across an injected mid-resolution interruption).
* The same harness is the **economy balance tool** for the campaign itself: it prints income curves,
  fleet values, time to first contact, Raider impact and game length for a set of seeds and
  `CampaignConfig` values. Use it before any human play-test of balance changes.
* Fog test: plan an empire's turn with the unmasked view substituted; flag any decision that changes
  where no hidden object is involved, and fail if `EmpireBrain` reads a field the mask removes.
* Raider tests: never create a contact or target a neutral system; spawn only in unclaimed systems, never adjacent to a visible player fleet, size
  within 0.85-0.95 of the mean player fleet value, bounty paid once.
* Favour tests: thresholds, attitude word never leaks the number, value absent from every player's
  `CampaignView` fingerprint.

---

## 10. Open decisions (defaults will be built unless ruled otherwise)

| # | Question | Default |
|---|---|---|
| D-S1 | Which factions may be neutral systems | A campaign setting; default the league and minor factions, excluding player-chosen factions |
| D-S2 | RULED 2026-11-12: Raider targets | Player and CPU empire systems; never neutral systems. Raiders only raid and never attack a fleet, but can be attacked |
| D-S3 | RULED 2026-11-12: peaceful membership | In (section 5) |
| D-S8 | Can an empire build a member system's faction ships after it joins | No; it keeps the garrison it inherited but builds only its own faction |
| D-S4 | Raider bounty rate | 25% of destroyed Raider value |
| D-S5 | Do CPU empires gang up on the leading human | Only on Hard |
| D-S6 | Raider frequency setting at campaign creation | Off / Rare / Normal / Frequent, scaling `RAIDER_SPAWN_CHANCE` |
| D-S7 | Does a Raider fleet carry over damage between raids | Yes (they are ordinary `camp_unit` rows); they never repair |
