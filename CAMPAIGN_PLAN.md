# Campaign Mode Plan

Status: **PLANNED 2026-11-12. Nothing built.** This file is the implementation brief. Rules input is
`CAMPAIGN_RULES_REFERENCE.md` (KS ch. 13-14); the user's spec and Part D answers of 2026-11-12 are
folded into section 1 and override the book wherever they differ.

Companion plans: `STRATEGIC_AI_PLAN.md` (NPC empires on the campaign map; campaign Stage 11) and
`TACTICAL_AI_PLAN.md` (a CPU opponent for FV battles; independent, may ship first). Section 7 says
how the three fit together.

Reading order for an implementer: section 1 (rulings), 3 (architecture), 4 (schema), 6 (battle bridge -
the riskiest part), then the stage you are building in section 12. Section 13 lists the decisions still
open; each has a default, so nothing there blocks Stage 0-2.

Conventions: **ST** = Strategic Turn (one campaign turn). **TT** = Tactical Turn (one turn of a Fiery
Void battle). **Credits** = the only currency, 1 credit = 1 FV point. **Empire** = one player's (or
NPC's) side in one campaign. **System** = a campaign hex holding a star system. Tunable numbers are
written as `CampaignConfig::NAME` and live in one PHP class so balance passes touch one file.

---

## 1. Rulings (user, 2026-11-12)

Base = KS section 13 Tactical Campaigns, heavily simplified, with a Birth of the Federation flavour.

| # | Ruling |
|---|---|
| R1 | One currency, **Credits**, 1:1 with FV points. Ships, fighters, bases, OSATs, mines cost their FV point cost. |
| R2 | 1 ST = 12 TT (the standard length of an FV battle). Optional real-time limit per ST set at campaign creation. |
| R3 | Galactic map is a generated hex map: star systems, nebulae, asteroid fields, black holes. Presets Small / Medium / Large plus custom size up to a cap. Systems sit 2-4 hexes apart. |
| R4 | Each system is generated with a **base income** (100-300), paid to whoever controls it, and may have Mineral Deposits (permanent income bonus) and/or a **Quantum-40** source. |
| R4a | **Q-40 is a resource that is spent** (ruled 2026-11-12, replacing "one lane per source"). A system has a chance to be generated as a Q-40 source; a source yields Q-40 each ST to its owner; a **homeworld always yields 1 per ST**. A unit that has a jump drive costs **1 Q-40** on top of its credits; a jump lane costs **2 Q-40**. Q-40 can be gifted. |
| R5 | Income = base income **plus a population bonus** (guideline 100 credits per billion; ruled 2026-11-12, to be fine-tuned). A newly claimed system starts with a small population that grows toward a cap; upgrades (terraforming, housing, hydroponics) raise cap or growth. No starvation in v1. |
| R6 | Claiming: occupy an unclaimed system uncontested for 1 full ST; an enemy-owned one for 2 full STs. A turn spent raiding does not count. |
| R7 | Raid: instead of claiming, steal 50% of the system's credit income for that ST. |
| R8 | Build queues: one per system (defences, upgrades, bases) and one per shipyard (ships). Each queue builds 500 points per ST, 1000 after investment. Bases take several STs. Population caps the defences and shipyards a system sustains; a starbase needs system income >= 150. |
| R9 | Movement: 1 hex per ST in any direction, 3 hexes per ST along an established jump lane. Ancients +1. Jump lanes are purchased. **Off-lane movement needs a jump engine in the fleet; a fleet without one can only travel lanes** (ruled 2026-11-12). Every lane is assumed to have a jump gate at each end. |
| R10 | Fog of war: sight 2 hexes from a populated colony, a base, or a fleet containing an ELINT ship; 1 hex for other fleets; 0 (own hex only) for fighter-only fleets; Ancients +1. Movement is secret; the server is the judge. |
| R11 | Contact (two or more hostile fleets in one hex) offers each side **Fight manually / Automatic combat / Retreat** (section 6.1 has the resolution matrix). |
| R12 | A manual fight is an ordinary FV game created by the campaign with players, fleets, terrain and rules pre-set. **If it has not ended naturally after 12 TT the game PAUSES** and another ST is played (ruled 2026-11-12): players may move new forces to the hex and withdraw ships that left to hyperspace during the battle; then the battle resumes for up to 12 more TT. Fleets arriving later join as reinforcements through their own jump point, which may not open within a buffer (default 10 hexes) of an enemy deployment zone; **a fleet with no jump engine joins through the system's jump gate**. |
| R12a | When a battle is being arranged, a player moving forces into the hex may choose to **hold any of them in hyperspace** and bring them in as reinforcements during the battle (ruled 2026-11-12). |
| R13 | No new defences may be built in a system with an active battle. |
| R14 | Both sides may agree at any point of a manual battle to finish it by Automatic combat. |
| R15 | Repair at bases and shipyards, priced like Self Repair (boxes and criticals); destroyed sections replaceable at extra up-front cost. Per-unit repair plus Repair All with the cost on the button. |
| R16 | Ammunition is restocked from an Ordnance stock bought into a base / shipyard / system. |
| R17 | Destroyed fighters are replaced at their point cost. Default shuttles are replaced free. |
| R18 | Diplomacy states: Non-Aggression Pact, Friendship (NAP + trade routes), Alliance (Friendship + mutual defence), War. Gifts of credits and Q-40. Free-text messages; one public declaration per empire per ST. Campaign chat with whisper. |
| R19 | NPCs: a generic Raider faction (payable; highest bidder is exempt; **spawns in unclaimed systems at irregular intervals, never at the map edge; fleet value just under the players' average fleet; defeating one pays a credit reward** - ruled 2026-11-12), neutral single-system factions drawn from FV factions, each with a hidden favour value per empire. CPU empires for single-player. |
| R19a | Campaign units are bought through the **existing FV buy dialog**, with all enhancements, options, refits and officers, since points = credits (ruled 2026-11-12). |
| R20 | Set-up: starting credits, tech year (ISD), victory conditions, starting systems (equal total income and population per player), optional per-player handicap. |
| R21 | **No subscription for now** (ruled 2026-11-12): every player is entitled. The entitlement architecture is still built, switched off, so it can be activated later. |
| R21a | Campaign is built in the background across routine FV releases. **Its entry points stay hidden on the live server** behind a feature flag until it is ready for Beta. |
| R22 | Existing FV maps and terrain are used for battles. Listening posts / sensor nets come later as units (+1 detection). |
| Deferred | Starvation, supply and maintenance, endurance, research, espionage, ground combat, bombardment, crew experience, refits, hyperspace navigation, fleet missions (KS 13.8), officers' campaign clauses. Do not build hooks for these. |

---

## 2. Language and stack decision

**Recommendation: campaign server in PHP, campaign client in TypeScript (React). Do not port FV.**

### 2.1 Campaign server stays PHP

* The campaign has to instantiate FV ship classes constantly: unit cost, hull size, ELINT and jump
  engine tests, hit charts for auto-combat, damage sanitising, and injecting fleets into `tac_*`
  tables. All of that is ~411,000 lines of PHP in 2,815 files (2,702 of them ship files). A
  TypeScript server would either re-implement that model or call PHP for every question.
* It shares the session, auth, `player` table, DB connection, APCu and Discord notifier.
* Live runs on shared LiteSpeed / lsphp hosting. A Node service means a second runtime, a process
  manager and probably a different host. (Assumption from project notes - confirm with the host if
  this ever becomes the deciding factor.)
* What PHP lacks here (types, structure) is bought cheaply: `declare(strict_types=1)`, typed
  properties and return types on every new campaign class, and PHPStan with a committed baseline
  (the same "fail only on NEW findings" pattern as `checkShipData.php`).

### 2.2 Campaign client is TypeScript

* It is greenfield, on its own page, with no dependency on the legacy globals. Types pay for
  themselves on exactly this kind of code: a large state object from the server, many panels reading it.
* Vite 8 compiles `.ts`/`.tsx` natively. Add `typescript` as a devDependency for `tsc --noEmit`
  only (type check in `fvbuild.ps1 -Check`); the build itself needs no new plugin.
* The existing `fv:jsx-in-js` plugin in `vite.config.mjs` only matches `.js`, so `.tsx` files are untouched by it.

### 2.3 Converting Fiery Void itself to TypeScript

| | Server PHP -> TS (Node) | Legacy client JS -> TS | New React code in TSX + typed payloads |
|---|---|---|---|
| Size | ~411k lines / 2,815 files | ~136k lines / 230 files, global scripts concatenated by `scripts/bundle-legacy.js` | ~21k lines React today |
| Gain | One language; rules code could be shared with the client, ending mirror drift | Compile-time checks on the code with the most runtime surprises | Checks on new code and on the gamedata contract |
| Cost (rough estimate) | 1-2 years solo even with AI help; verification is the bottleneck, not typing | 3-6 months for a full conversion, because the files must become ES modules first | Days to set up, then free |
| Risk | Very high: every rule re-proven against the replay corpus; hosting change; contributors' ship files all change language; feature freeze or double implementation meanwhile | Medium: load-order and global-name bugs during module conversion | Low |
| Verdict | **No** | **Not as a project**; opportunistic only | **Yes** |

The realistic modernisation path, in order of value:

1. New React components in `.tsx`; a hand-written `types/gamedata.d.ts` for the server payload.
2. `// @ts-check` + JSDoc on legacy files as they are touched, checked by `tsc --allowJs --checkJs`
   over an explicit file list, with a `globals.d.ts` for `gamedata`, `shipManager` etc.
3. PHPStan at a low level with a baseline for the server.
4. Mirror-drift reduction stays the principle already on the roadmap: the server sends, the client
   does not re-derive.

**Scope of TSX (clarified 2026-11-12; the user accepted this section's recommendations):** TypeScript
is for the campaign client and for NEW React components elsewhere. The existing ~21k lines of React in
`client/UI/reactJs/` stay `.js`. `.js` and `.tsx` files import each other freely in one bundle, so
there is no need for a bulk conversion; convert an existing component only when it is being
substantially reworked anyway. Never convert a file as a side effect of an unrelated fix.

A full port buys nothing a player can see and puts the replay-verified rules engine at risk. Revisit
only if hosting moves to a VPS and a second structural developer joins.

---

## 3. Architecture

### 3.1 Shape

```
Browser (campaign.php)                      Server (PHP)
  campaign.bundle.js (TS/React)   GET/POST    source/public/campaigndata.php
  - GalaxyMap (Canvas 2D)        ---------->  CampaignManager (facade, static)
  - panels (React)                              |- CampaignDB         all camp_* SQL
  - store (one CampaignView)                    |- CampaignView       per-empire masked payload
                                                |- TurnResolver       stepped, idempotent
                                                |- MapGenerator       seeded
                                                |- Visibility         fog of war
                                                |- Economy / BuildQueue / Repair
                                                |- Diplomacy
                                                |- AutoCombat
                                                '- BattleBridge  <->  Manager / DBManager / tac_* (existing FV)
```

* The campaign is a **layer above** FV. It owns `camp_*` tables and never stores campaign state in
  `tac_*`. A tactical game is created from campaign state and harvested back into it; in between,
  the campaign does not read the game except for its turn number and status.
* New server code lives in `source/server/campaign/`. Check that `autoload.sh` scans it (it scans the
  source tree; regenerate with `scripts/fvbuild.ps1 -Autoload`, never hand-edit `autoload.php`).
* SQL goes in a new `CampaignDB` class that receives the existing mysqli connection from
  `DBManager`. Do not add campaign queries to `DBManager.php` (5,800 lines already).
* Touch points inside existing FV code are deliberately few and listed in section 6.6. Every one is
  guarded by a cheap test (`$gamedata->rules->hasRuleName('campaign')`) so ordinary games pay one
  rule lookup.

### 3.2 Entry points (all include `global.php`)

| File | Purpose |
|---|---|
| `source/public/campaigns.php` | List my campaigns, open campaigns, create form. Linked from `games.php` only when the `campaign` feature flag is on (section 8). |
| `source/public/campaign.php` | The campaign screen. Emits the initial `CampaignView` inline as JSON TEXT, then loads `campaign.bundle.js`. |
| `source/public/campaigndata.php` | `GET ?campaign=ID&v=N` returns the view (or `{"unchanged":true}`); `POST` takes one action (section 9). |

Traps that apply (from project memory): a PHP entry file must have no BOM; `JSON_NUMERIC_CHECK`
rewrites numeric-looking free text, so do not use it on campaign payloads (cast numbers explicitly
instead); an empty PHP `array()` encodes as `[]`, so cast every map to `(object)`; the connection
charset is 3-byte utf8, so free text is stored via `json_encode` default flags or stripped of 4-byte
characters.

### 3.3 Client

* Source: `source/public/client/campaign/` (`main.tsx`, `store.ts`, `api.ts`, `types/api.ts`,
  `map/`, `panels/`). Output: `source/public/client/campaign/campaign.bundle.js`.
* Build: a second config `vite.campaign.config.mjs` (lib mode takes one entry; keep
  `output.strict`, and `process.env.NODE_ENV = 'production'` as in the main config). `yarn build`
  gains `vite build -c vite.campaign.config.mjs`; add a `watch:campaign` script.
* Styling: `styles/tokens.css` is the only `:root` block; the campaign reuses those tokens and the
  SCS theme (`reactJs/styled/theme.js`). Orbitron is allowed here (the ban is game.php only).
* State: one immutable `CampaignView` from the server plus local draft orders. No client-side rules:
  the server sends every legality answer the UI needs (reachable hexes for a fleet, affordable items,
  repair quotes). This is the mirror-drift principle applied from day one.
* Map renderer: **Canvas 2D**, three stacked canvases (terrain and grid; fog and borders; fleets,
  selection and animated icons). Reasons: at most a few thousand hexes, no WebGL1 concerns, no second
  copy of THREE. Animation: planets = a horizontally scrolling surface texture clipped to a circle
  with a shading overlay; black holes and nebulae = slow sprite rotation / alpha drift. The animation
  loop runs only while animated icons are on screen and the tab is visible, at ~20 fps.
* Polling: `GET` every 10 s while waiting on others, with the version number so the usual answer is
  a few bytes. No polling while the player is editing orders and nothing can change.

### 3.4 Turn processing on shared hosting

No daemon and no cron is assumed. Resolution is **lazy and stepped**:

* It is triggered by the request that submits the last Ready, or by any campaign request after the
  ST deadline.
* `camp_campaign.resolve_step` records progress. Each step is idempotent and commits on its own, so
  a request that hits the lsphp time or memory limit is resumed by the next request. Lock with
  `SELECT ... FOR UPDATE` on the campaign row plus a `resolve_lock` timestamp (stale after 60 s).
* The client shows "Resolving turn..." and polls until `phase` changes.
* Never run campaign resolution inside a tactical request (`gamedata.php`): the tactical side only
  flags a battle as needing harvest (section 6.4).

---

## 4. Data model

New file `db/campaign.sql` (idempotent, `CREATE TABLE IF NOT EXISTS`, InnoDB, utf8) and fold into
`db/emptyDatabase.sql` (which is not what live runs - the migration file is the deploy artefact).

| Table | Key columns | Notes |
|---|---|---|
| `camp_campaign` | id, name, creator, status (LOBBY / ACTIVE / FINISHED), turn, phase (ORDERS / RESOLVING / BATTLES), resolve_step, resolve_lock, deadline, version, seed, settings (JSON), password_hash, created | `version` increments on every state change; the poll compares it. `settings` holds map size, ISD, starting credits, ST time limit, victory conditions, battle rules (sim move, desperate, mines), handicaps. |
| `camp_empire` | id, campaign_id, playerid (NULL for NPC), kind (PLAYER / CPU / RAIDER / NEUTRAL), name, faction, colour, credits, q40, ready, eliminated, is_ancient, last_seen | Faction string exactly as FV uses it. `is_ancient` resolved once at join from the faction. |
| `camp_hex` | campaign_id, q, r, terrain (EMPTY / SYSTEM / NEBULA / ASTEROIDS / BLACKHOLE), system_id | One row per non-empty hex only. Axial coordinates. |
| `camp_system` | id, campaign_id, q, r, name, owner_empire, pop, pop_cap, growth, base_income, minerals_pct, q40_rate, build_rate, claim_empire, claim_turns, raided_turn, upgrades (JSON), ordnance (JSON), icon_seed | `pop` in millions (int). |
| `camp_lane` | id, campaign_id, system_a, system_b, owner_empire, path (JSON hex list), status (BUILDING / OPEN), turns_left | |
| `camp_fleet` | id, campaign_id, empire_id, name, q, r, prev_q, prev_r, status (IDLE / MOVING / IN_BATTLE / RETREATING), battle_id, stance | `prev_q/r` = hex it arrived from (retreat direction). |
| `camp_unit` | id, campaign_id, empire_id, fleet_id (NULL = garrison of system_id), system_id, phpclass, name, flightsize, bulk, base_cost, enh (JSON), sysenh (JSON), damage (JSON), ammo (JSON), status (ACTIVE / BUILDING / DESTROYED), built_turn | One row per ship / flight / base / OSAT. `damage` is the **sanitised PreBattleDamage payload** (`sys` / `ftr` / `mne` buckets). `enh` uses the saved-fleet tuple format. |
| `camp_queue` | id, campaign_id, system_id, shipyard_unit_id (NULL = system queue), position, kind (UNIT / UPGRADE / LANE / REPAIR), spec (JSON), cost, progress, status | Paid in full when queued. |
| `camp_order` | id, campaign_id, turn, empire_id, kind, payload (JSON), status (DRAFT / LOCKED / DONE / REJECTED), reject_reason | Fleet moves, stances, raids. Draft until Ready. |
| `camp_relation` | campaign_id, empire_a, empire_b (a < b), state (NEUTRAL / NAP / FRIENDSHIP / ALLIANCE / WAR), since_turn, ends_turn | One row per pair. |
| `camp_proposal` | id, campaign_id, turn, from_empire, to_empire, kind, payload (JSON), status (OPEN / ACCEPTED / REJECTED / EXPIRED) | Treaties, gifts needing consent, auto-resolve offers. |
| `camp_favour` | campaign_id, npc_empire, empire, value | Hidden; never serialised to players. |
| `camp_battle` | id, campaign_id, q, r, started_turn, status (PENDING / STANCES / ACTIVE / NEEDS_HARVEST / RESOLVED), gameid, mode (MANUAL / AUTO), seed, sides (JSON), report (JSON), checkpoint_tt | `sides` = empire -> team and fleet ids. |
| `camp_message` | id, campaign_id, turn, from_empire, to_empire (NULL = all), kind (CHAT / WHISPER / NOTE / DECLARATION / SYSTEM), body, time | Whisper masking is server-side. |
| `camp_event` | id, campaign_id, turn, empire_id (NULL = public), kind, payload (JSON) | The turn report and the audit trail. Every resolution change writes one. |
| `camp_seen` | campaign_id, empire_id, q, r, last_turn, snapshot (JSON) | Remembered fog: terrain and last-known owner. |
| `player_entitlement` | playerid, feature, expires (NULL = permanent), granted_by, granted | `feature = 'campaign'`. Separate table because `player` is MyISAM and holds credentials. |

Existing tables changed:

* `tac_ship`: add `campunitid int DEFAULT NULL`. The old `campaignX / campaignY / campaigngameid`
  columns are dead legacy (written as literal 0s by `DBManager::submitShip`); leave them alone.
  `submitShip` uses a named-column INSERT, so adding a column is safe; write the new one there.
* `tac_game`: no new column. The link is a `campaign` entry in the `rules` JSON (new
  `CampaignRule { campaignId, battleId }`, registered in `GameRules.php` like the others) plus
  `camp_battle.gameid`.

---

## 5. Campaign rules, in implementable form

All numbers are defaults in `CampaignConfig`.

### 5.1 Map generation (`MapGenerator`, seeded, deterministic)

* Presets: Small 16 x 12 (2-3 empires), Medium 24 x 18 (3-5), Large 36 x 26 (4-8); custom up to 60 x 40.
* Systems by Poisson-disc sampling, minimum distance 2, target 3, so neighbours are 2-4 apart
  (about one system per 9 hexes).
* Other terrain: 8% of remaining hexes nebula (clustered), 6% asteroid field (clustered), 1 black hole
  per ~150 hexes, never adjacent to a system.
* Each system: `base_income` 100-300 in steps of 10; `pop_cap` 1-4 bn; `pop` 0 until claimed;
  25% chance of minerals (+10% to +40% of base income); `Q40_SOURCE_CHANCE` (default 12%) of being a
  Q-40 source with `q40_rate` 1.
* Homeworlds: placed to maximise the minimum pairwise distance, then a balance pass adjusts systems
  within 4 hexes so every empire's neighbourhood income is within 10% of the mean. Homeworld default:
  base income 300, pop 5 bn (cap 8), `q40_rate` 1 always, one shipyard, one free base slot. Each
  empire also starts with `START_Q40` (default 3).
* Names from a word list; planet icon chosen by `icon_seed` from a small set of surface textures.
* Battle terrain mapping: SYSTEM -> `moons` rule (one large body); ASTEROIDS -> `asteroids` rule;
  NEBULA -> `nebula` rule; BLACKHOLE -> `gravityWell` rule; EMPTY -> none. A system with
  asteroid/nebula neighbours adds nothing (only the hex itself counts).

### 5.2 The Strategic Turn

```
ORDERS      every empire edits orders (moves, builds, repairs, diplomacy), then presses End Turn.
            Ends when all active empires are ready or the deadline passes.
RESOLVING   server, stepped (section 3.4):
              1 lock orders          5 claims and raids        9  fog and remembered hexes
              2 diplomacy takes effect   6 income and population   10 victory check
              3 movement (3 sub-steps)   7 build queues and lanes   11 events, notifications
              4 contact detection        8 repairs and resupply     12 turn + 1
BATTLES     only if step 4 found contacts or a paused battle exists. Stances collected, battles
            created, resumed or auto-resolved. Ends when every battle is RESOLVED or PAUSED at its
            TT checkpoint (12 x k). If the deadline passes first, a running battle pauses at the end
            of its current tactical turn. Then steps 5-12 run.
```

So during ORDERS every battle is either finished or paused; the two clocks never run at once.

* Movement is simultaneous. A lane move is 3 sub-steps, a normal move happens in sub-step 1 (Ancients:
  2 hexes over sub-steps 1-2). Contact is tested at the end of each sub-step: hostile fleets in the same
  hex stop there. Fleets that swap hexes pass each other (default; see D4).
* Off-lane moves are legal only for a fleet holding at least one unit with a jump engine (ask
  `getUnitJumpingEngine`, never sweep `$ship->systems`); one such unit carries the whole fleet
  (default; D15). Lane moves are open to any fleet. A fleet part-way along a lane is in hyperspace:
  it cannot be contacted and is seen only at the lane's ends (default; D16).
* Hostile = relation WAR. NEUTRAL fleets coexist in a hex with no combat.
* **Raiders never start a fight** (ruled 2026-11-12). A Raider fleet entering a hex does not create
  a contact. Every other empire with a fleet or garrison there is offered **Attack / Ignore** in the
  BATTLES phase; only an Attack creates a battle, in which the Raiders fight or retreat. If the
  Raiders are still in the hex at step 5 the raid succeeds (R7), whether or not defenders were
  present (default; D20).
* Steps 5-8 skip any system with an ACTIVE battle for building (R13) but still pay income to the owner
  unless raided.

### 5.3 Economy

* Income per ST = `(base_income x (1 + minerals_pct) + pop_bn x 100) x (1 + trade_bonus)`, halved
  for the owner and credited to the raider when raided (R7). The two terms are separate constants
  (`INCOME_PER_BN`, and a multiplier on base income) so the balance can be tuned later.
* A claimed system is seeded with `COLONY_START_POP` (default 10% of its cap). Growth per ST =
  `growth x pop x (1 - pop / pop_cap)`, growth default 8%.
* Claim: `claim_turns` counts consecutive full STs in which exactly one empire has a fleet (not
  fighter-only) in the hex, no battle, and is not raiding. Threshold 1 if unowned, 2 if owned by
  another. On transfer the population stays; queues are cancelled without refund; surviving bases of
  the old owner must have been destroyed in battle first (a defended system is a battle, not a claim).
* Upgrades (system queue): Terraforming (+25% cap), Improved Housing (+10% cap, repeatable x3),
  Hydroponics (+2% growth), Industrial Investment (build rate 500 -> 1000), Shipyard (adds a shipyard
  unit, `shipyardNew` exists as an FV terrain class - check it is usable as a battle unit before
  reusing it). Costs and build times in `CampaignConfig`.
* Capacity: shipyards <= 1 per 2 bn pop (min 0); defence points (OSATs + mines + bases) <=
  1,000 per bn pop; a starbase needs income >= 150.
* **Q-40** (R4a): `camp_empire.q40` is a stock. Each ST every owned source adds its `q40_rate`
  (step 6). A unit whose class has a jump drive (`getUnitJumpingEngine` on the server-built ship)
  costs `Q40_PER_JUMP_UNIT` (1) and a jump lane `Q40_PER_LANE` (2), charged with the credits on
  enqueue and refunded with them on cancel. The buy dialog in campaign purchase mode shows the Q-40
  cost and stock beside the points. A raided source yields nothing to its owner that ST and nothing
  to the raider (default; D19).
* Queues: cost is paid in full on enqueue; cancel before any progress = full refund, after = refund
  of unbuilt share. Each ST a queue adds `build_rate` progress to its head item; overflow carries to
  the next item in the same ST. A finished ship appears in the system garrison.
* Purchasable units = the empire's faction, filtered by the campaign ISD, bought through the
  existing FV buy dialog with everything it offers (R19a). See 5.3a.

### 5.3a Buying through the FV buy dialog

The buy dialog is legacy lobby code (`gamelobby.js`, `lobbyEnhancements.js`, `buyHelp.js`, the
officer and refit UIs) built on page globals. Do not port it and do not load it into the campaign
bundle. Host it instead:

* FV already has a one-slot lobby with a points cap and no map, teams or scenario: the **Fleet
  Builder** (`FleetTestRule`, `$isFleetTest` in `gamelobby.php`). Add a **campaign purchase mode** to
  that lobby (a `campaignPurchase` rule carrying campaign, system and queue ids): faction locked to
  the empire's, ISD locked to the campaign's, points cap = the empire's credits, and its Ready button
  replaced by "Add to build queue".
* `campaign.php` opens it in a modal iframe; on confirm the lobby POSTs its ordinary fleet JSON to
  `campaigndata.php` (`queue.add`) instead of to the buying phase, and tells the parent to refresh
  with `postMessage`. The temporary lobby game row is deleted afterwards.
* The server turns that JSON into ship objects with the extracted builder (6.2), then stores each as
  a `camp_unit` with its `enh`, `sysenh` and officer rows.
* ⚠️ **The lobby's ship-level enhancement cost is the client's claim**: `Manager::getShipsFromJSON`
  reads `pointCostEnh` straight from the POST, and `BuyingGamePhase`'s fleet-cost check is commented
  out. Only per-system refits are server-priced (`Enhancements::sanitiseSystemEnhancements`). Credits
  are persistent, so the campaign needs `CampaignPricing::priceUnit`: instantiate the class
  server-side and re-price every taken enhancement from that instance's own `enhancementOptions`
  tuple (price and priceStep), plus refits and officers (`Officers::sanitiseFleet` for the fleet
  rules). The client figure is used only to detect a mismatch, which rejects the purchase.
* Random results that the lobby rolls at fleet commit (officer free picks, `LOW_TRAIN`) are rolled
  **once, when the unit is built**, and stored on the unit; battles never re-roll them.
* Pre-battle damage authoring is disabled in campaign purchase mode.

### 5.4 Fog of war (`Visibility`)

* Sources and radius per R10; Ancients +1; listening posts later +1.
* ELINT test: the unit has an `ElintScanner` system (instanceof, on the server-built object).
  Fighter-only: every unit in the fleet is a `FighterFlight`.
* Per empire per ST the server computes the visible hex set, then builds `CampaignView` from it.
  **Hidden data is never sent** (the mine-detection lesson: a client-side hiding check is an oracle).
* Three levels: VISIBLE (current contents), REMEMBERED (terrain, system name, last-known owner, from
  `camp_seen`), UNKNOWN (terrain only if the campaign setting "map revealed" is on).
* A visible foreign fleet shows owner, unit count and hull-size breakdown; with an ELINT source in
  range it also shows classes (default; D6).
* Allies share vision (ALLIANCE only).

### 5.5 Diplomacy

* States and effects per R18. Default at start: NEUTRAL (no combat, no trade).
* Proposals (NAP, Friendship, Alliance, peace) need acceptance; they take effect at step 2 of the
  next resolution. Cancelling a pact takes effect one full ST later. War can be declared on a
  NEUTRAL empire with effect at this resolution; on a pact partner only after the pact has lapsed.
* Mutual defence: when war is declared on an empire, each of its allies is set to WAR with the
  aggressor at the same step (forced, with a public SYSTEM message).
* On WAR all trade routes between the two lapse at end of turn.
* Trade route (Friendship or better): links one system of each empire; each gets +10% income; one
  route per system; suspended while either end has an active battle.
* Gifts: credits and Q-40 (moved between the two empires' stocks). Immediate.
* Raider tribute: each ST empires may bid; the highest bidder is exempt from Raider attacks next ST;
  all bids are spent.
* NPC favour: hidden integer -100..100 per (NPC, empire); moved by gifts, by attacking them, by
  attacking their friends; thresholds unlock NAP / Friendship / Alliance with that NPC.

### 5.6 Repair, ordnance, replacements

* Where: a system with a friendly base or shipyard, unit in garrison or in a fleet in that hex, no
  active battle there.
* Price: `box_price = base_cost / total_boxes_of_unit x REPAIR_FACTOR (0.5)`.
  Damage costs `boxes x box_price`; a critical costs its `repairCost` (the Self Repair figure on the
  critical class; C&C criticals 4) x `box_price`; a destroyed system costs its full boxes; a destroyed
  structure section adds a surcharge of 10% of `base_cost` and needs a shipyard.
  Verify the per-box Self Repair cost in `SelfRepair` (`baseSystems.php`, around the repair queue
  builder) before fixing these numbers.
* Capacity: each base repairs up to 250 credits of work per ST, each shipyard 500 (separate from
  its build queue). Repairs are queued (`camp_queue.kind = REPAIR`) and completed in step 8.
* A repair edits the unit's `damage` payload server-side and re-runs `PreBattleDamage::sanitise`.
* Fighters: restore `flightsize` at `(pointCost / 6) x craft` (the lobby's own formula). Default
  shuttles are never stored, so they are "replaced" for free by construction.
* Ordnance: a system holds a stock per missile type (`camp_system.ordnance`), bought at lobby prices.
  Resupply moves rounds from stock into a unit's `ammo` map up to magazine capacity. Units start a
  battle with exactly their stored `ammo`.

### 5.7 Victory

Configured at creation, any combination: Conquest (all other player empires eliminated - no systems
and no fleets), Domination (hold N% of systems for 3 STs), Score at ST limit (income + fleet value).

---

## 6. Battles

### 6.1 Stance resolution

Sides are formed by relations (allies at war with the same enemy share a team). Each empire in the
contact picks a stance within the BATTLES phase (default on timeout: defender of an owned system =
AUTO, otherwise RETREAT).

| Side A \ Side B | Fight | Auto | Retreat |
|---|---|---|---|
| **Fight** | manual FV game | manual only if both chose Fight; otherwise auto | B chooses: fight manually, or leave it to auto "retreat under fire" |
| **Auto** | auto | auto | auto, retreat under fire |
| **Retreat** | (mirror) | (mirror) | nothing happens; both move 1 hex back toward `prev_q/r` |

With a Fight or Auto stance a player also marks which of their units **hold in hyperspace** (R12a).
In a manual battle those are bought into the game with the reinforcement flag (the lobby's
`reinforcementClaim`, honoured by `BuyingGamePhase::process` when the rule allows it; bases, OSATs
and terrain can never be held - `BaseShip::alwaysDeploysTurnOne`). A side must deploy at least one
unit. In automatic combat held units join from round 4.

A retreating fleet with nowhere legal to go (previous hex now enemy-held) picks the adjacent hex
furthest from enemies; if none, it cannot retreat. Bases and OSATs never retreat.

### 6.2 Creating the FV game (`BattleBridge::createGame`)

1. Build the `GameRules` array: `campaign` rule, terrain from the hex (5.1), `allowReinforcements`
   on, plus the campaign's battle settings (simultaneous movement, desperate, mines).
2. `DBManager::createGame(...)` with one slot per empire, teams from sides, the campaign ISD in
   `in_service_date`, a scenario JSON naming the campaign, hex and turn, and a random password hash
   so nobody else can take a slot. Seat each player with `Manager::takeSlot` directly.
   Deployment zones: defender (system owner, or whoever was in the hex first) on one edge, attackers
   on the other, using the standard zone geometry from Create Game.
3. For each slot, build the fleet server-side: for every `camp_unit` construct the ship object the
   way `Manager::getShipsFromJSON` does (same fields: `phpclass`, `name`, `userid`, `slot`,
   `flightSize`, `enhancementOptions` filled from `enh`, `systemEnhancements`, `preBattleDamage` =
   the stored payload, `bulkBuy`) and set `campUnitId`. Then run **the existing
   `BuyingGamePhase::process`** for that player and slot. That one method already writes ships,
   enhancements, per-system refits, pre-battle damage and criticals at turn 0, flight sizes and
   ammunition, and its fleet-cost check is disabled, so no parallel write path is needed.
4. Seams needed in `process` (both tiny, both gated on the campaign rule):
   * ammunition: where it writes `$ammo->amount` / `$weapon->ammunition`, use the unit's stored
     count when `$ship->campaignAmmo` is set;
   * skip the random rolls that only make sense at a lobby commit (officer free picks,
     `LOW_TRAIN` roll): a campaign unit carries the results rolled when it was built (5.3a).
   Refactor `getShipsFromJSON`'s per-ship body into a reusable builder rather than copying it.
5. Garrison units of the system owner (bases, OSATs, mines) join the owner's slot. A system at the
   end of any OPEN lane gets one neutral jump gate on the battle map (the existing gate terrain unit,
   see `JUMP_GATES_PLAN.md`), placed mid-map outside both deployment zones (default; D17).
6. Advance the game out of the buying phase exactly as the last Ready click would
   (`Manager::advanceGameState`), so the players land in Deployment.
7. Set `camp_battle.status = ACTIVE`, `gameid`, `checkpoint_tt = 12`; fleets `IN_BATTLE`.

Check first, in the Stage 1 spike: how `BuyingGamePhase::process` reads `$gameData->forPlayer` and
the per-slot "already submitted" test, because the bridge calls it once per player from a single
request with no session behind it.

### 6.3 During the battle

* `campaign.php` lists the battle with a link to `game.php?gameid=N`; `games.php` shows it as a normal game.
* **Pause at the checkpoint (R12).** When the turn change that would start TT `checkpoint_tt + 1`
  runs and the game is not finished, the battle becomes `PAUSED`.
  * Do not add a new `tac_game.status` value: many code paths switch on LOBBY / ACTIVE /
    SURRENDERED / FINISHED. The pause lives in `camp_battle.status`. With the campaign rule present,
    `Manager::submitTacGamedata` refuses orders and `advanceGameState` does nothing while it is
    paused; the gamedata payload carries a `campaignPaused` flag (it needs adding to
    `parseServerData`'s named-key copy) and game.php shows a passive "Paused - strategic turn in
    progress" banner with a link back to the campaign. Viewing, replay and chat still work.
  * The pause point is the fresh turn's first phase, before anyone has submitted, so nothing is
    half-committed. Surrender stays available while paused.
* **Partial harvest at the pause.** Units that left the map to hyperspace during those 12 TT are
  harvested (6.4) into a fleet in the hex, status `IDLE`, and may be given orders in the coming ST.
  Units still on the board, and units still waiting in hyperspace as reinforcements, stay `IN_BATTLE`.
  A side with nothing left on the board and nothing in reserve has lost; the battle resolves.
* **Late arrivals.** A fleet entering the hex during that ST is added to the paused game before it
  resumes: seat the empire in a new slot if needed, insert its units with `reinforcement = 1`
  through the same builder plus `Manager::insertSingleShip` and the per-ship writes from 6.2 step 3,
  and let the existing arrival flow bring them in (`REINFORCEMENTS_PLAN.md`; since Hyperspace H6 the
  server places the wave in `JumpEngine::placeArrivingReinforcements`).
  * A fleet with a jump engine opens its own exit, subject to the jump buffer below.
  * A fleet with none arrived by lane, so the system has a gate (6.2 step 5) and it comes in through
    a gate exit (`gateexit` claims; anyone may ride an open gate exit).
* **Resume.** When the ST's resolution reaches the BATTLES phase the battle goes back to `ACTIVE`,
  `checkpoint_tt += 12`, and the players are notified.
* Jump buffer (R12): in the arrival branch of `Firing::getVortexDeclarationBlock`, refuse an exit hex
  within `CAMPAIGN_JUMP_BUFFER` (10) of an enemy deployment zone when the campaign rule is present;
  mirror the refusal in the client predicate so the button is not offered (a client predicate
  narrower or wider than its server twin is a known bug shape here). `HexZone` and the mine
  deployment buffer are the precedent.
* Mid-battle auto-resolve (R14): a button in game.php's options tab, shown only with the campaign
  rule; both sides must accept; on agreement the game is marked FINISHED and the harvested state is
  fed to `AutoCombat` as the starting position.

### 6.4 Harvest (`BattleBridge::harvest`)

Fleet saving in FV today is built **by the client** (`ajaxInterface.constructSavedShips`). A campaign
cannot trust that, so harvest is new server code:

* Trigger: where a game becomes FINISHED (`Manager::changeTurn`, and the surrender path in
  `submitTacGamedata` / `completeSurrender`), if the campaign rule is present, set
  `camp_battle.status = NEEDS_HARVEST`. Nothing else happens in the tactical request.
* The next campaign request for that campaign runs the harvest inside the campaign lock:
  load the game with `DBManager::getTacGamedata` for a neutral viewer (confirm which `forPlayer`
  value gives an unmasked load), then for each ship with a `campunitid`:
  * destroyed by damage -> unit DESTROYED. Use the equivalent of the client's
    `isDestroyedByDamage`: a docked unit (`removed`) is alive; reabsorbed Shadow integrated fighters
    read as destroyed and are paid for by the carrier - keep that behaviour.
  * otherwise write `damage`: per system lost boxes and destroyed flag; persistent criticals only
    (drop one-turn ones, keep the PreBattleDamage deny list); fighters = surviving count with
    ordinals renumbered over survivors (ruling D8 of the pre-battle damage plan: a fighter is
    damaged, never stored destroyed); mines per ordinal. Pass the result through
    `PreBattleDamage::sanitise` so what is stored is exactly what `process` will accept next time.
  * `ammo` from the latest `tac_ammo` rows.
  * units that left through a jump point survive and count as retreated.
  * spawned units with no `campunitid` (mines laid, split flights) : mines laid are spent; a
    "- Split" flight fragment is merged back into its parent unit's flight size.
* Outcome: a side that surrendered or has nothing left on the map lost. Losers retreat 1 hex
  (6.1 rule); the winner holds the hex. A mutual-destruction or timeout result leaves the hex contested.
* Write a `camp_event` battle report (losses per empire, link to the replay).
* Round-trip invariant, enforced by a harness: unit -> game -> immediate harvest == the same unit.

### 6.5 Automatic combat (`AutoCombat`)

Goal: plausible, fair, fast, deterministic from a stored seed. It produces the **same output format
as a harvest** (damage payloads and destroyed units), so everything downstream is shared.

* Strength of a unit = `base_cost x health_fraction x modifiers`, where `health_fraction` comes from
  `calculateCombatValue()` on the server-built ship with its damage applied (FV's own measure of
  remaining worth). Modifiers: fighters x0.8 without a carrier present; base x1.25 when defending;
  ELINT in fleet +5% to the side; Ancients as priced.
* 12 rounds (one per TT). Each round each side deals `K x strength x U(0.8, 1.2)` damage points
  simultaneously. Points are spread over enemy units weighted by size (bigger hulls draw more fire).
* Applying damage to a unit: repeatedly pick a location and system through the ship's own hit chart
  and subtract boxes (armour ignored, but `K` is calibrated with that in mind), destroying systems
  and structure exactly as the PreBattleDamage model stores them. A unit whose primary structure is
  gone is DESTROYED. Fighters lose whole craft.
* A side whose strength falls below 25% of its start attempts to disengage and leaves after one
  more round.
* Retreat under fire: the retreating side takes 2 rounds of incoming fire at 50% and deals none,
  then retreats.
* `AutoCombat::estimate($sides)`: a dry run over a fixed set of seeds that returns win probability
  and expected loss share without changing state. The strategic AI's stance and target choices
  depend on it (`STRATEGIC_AI_PLAN.md` sections 3 and 6), so build it with AutoCombat, not later.
* Raider bounty (section 7) is paid here and in `BattleBridge::harvest`.
* Calibrate against real results where possible: the user has offered an export of finished live
  games (`TACTICAL_AI_PLAN.md` section 13). Points per side against winner and value lost gives
  measured targets in place of the guessed ones below. Get it before building this stage.
* Calibration harness (`tests/campaign/autoCombatHarness.php`): equal-point mirror fleets win 50%
  +/- 3 over 1,000 seeds; a 2:1 points advantage wins > 95% and loses < 25% of its value; the
  typical winner of an even fight keeps 40-60%. Tune `K` and the spread to those targets.
* The round log is stored in `camp_battle.report` and shown as the battle report.

### 6.6 Complete list of edits to existing FV code

| Where | Change |
|---|---|
| `GameRules.php` + new `CampaignRule.php` | register the `campaign` rule |
| `BuyingGamePhase::process` | two seams (6.2 step 4) |
| `Manager::getShipsFromJSON` | extract the per-ship builder for reuse |
| `DBManager::submitShip` | write `campunitid` |
| `Manager::changeTurn`, surrender path | flag `NEEDS_HARVEST`; set `PAUSED` at a checkpoint |
| `Manager::submitTacGamedata`, `advanceGameState`, gamedata payload + `parseServerData` | refuse / idle while paused; `campaignPaused` flag and banner |
| `gamelobby.php` + lobby JS (Fleet Builder path) | campaign purchase mode (5.3a) |
| `DBManager::getGamesToBeDeleted` | never delete a game whose `camp_battle` is not RESOLVED. Note `deleteGames` runs on every game.php load - keep the added condition a cheap indexed NOT EXISTS |
| `Firing::getVortexDeclarationBlock` + client twin | jump buffer |
| game.php options tab | mid-battle auto-resolve offer; "Return to campaign" link |
| `games.php` | Campaigns entry, rendered only when the `campaign` feature flag is on |
| `Manager::createGame` path | untouched; the bridge calls `DBManager::createGame` itself |

After a campaign battle is harvested the campaign no longer needs its `tac_*` rows, so the normal
3-month purge is safe.

---

## 7. NPCs and CPU opponents

Two different problems, each with its own plan:

* **`STRATEGIC_AI_PLAN.md`** - what a non-player empire does on the campaign map (Raiders, neutral
  systems, CPU empires). It submits the same actions a player does, through the same validators,
  in resolution step 1. Built as campaign Stage 11.
* **`TACTICAL_AI_PLAN.md`** - a CPU opponent that plays an FV battle. It is built on existing FV
  structures (a bot player seated in an ordinary slot, orders pushed through the ordinary phase
  `process` methods) and **may ship for ordinary skirmish games before the campaign goes live**.

How the campaign uses them:

* An NPC empire is a `camp_empire` row with `playerid = NULL`. When one of its fleets is in a
  manual battle, `BattleBridge::createGame` seats the tactical AI's bot account in that empire's
  slot (TACTICAL_AI_PLAN section 9). Nothing else in section 6 changes.
* The **Fight** stance against an NPC is offered only when the feature flag `cpuOpponent` is on
  and the NPC's faction is on the tactical AI's readiness list. Otherwise the battle is resolved by
  `AutoCombat` and Fight is hidden. So Raiders, neutral systems and single-player campaigns work
  with or without the tactical AI.
* The NPC's own stance (fight / auto / retreat) is chosen by the strategic AI from an AutoCombat
  dry run (STRATEGIC_AI_PLAN section 6).
* `FleetComposer` (TACTICAL_AI_PLAN section 8) builds NPC fleets for both plans.

**Raiders (R19):** they only ever choose Raid System, against player and CPU empire systems (never
neutral systems), and never attack a fleet, though they can be attacked (5.2). They spawn in a
random unclaimed system, at irregular intervals; fleet value is
`RAIDER_SIZE_FACTOR` (0.9) x the mean value of all player fleets; destroying or routing a Raider
fleet pays the victors `RAIDER_BOUNTY` (25%) of the Raider value destroyed, split by damage share in
a manual battle or by strength share in AutoCombat. Behaviour is in STRATEGIC_AI_PLAN section 4.

---

## 8. Access: feature flag and (dormant) entitlement

Two independent switches.

**Feature flag - is campaign code reachable at all on this server?**

* New `$feature_flags` array in `source/server/varconfig.php` (per environment, like the DB
  settings) read through a small `Features::enabled('campaign')` helper in `source/server/lib/`.
  Absent or false = off. Local Docker and the test instance turn it on; **live leaves it off until
  Beta** (R21a).
* Off means: the Campaigns link is not rendered anywhere (hidden, not greyed), and
  `campaigns.php`, `campaign.php` and `campaigndata.php` answer 404 before doing any work. The
  `camp_*` tables may exist on live and stay empty.
* Every edit to existing FV code in 6.6 is inert without a `campaign` rule on the game, so campaign
  code can ride along in routine releases. Keep it that way: no campaign change may alter an
  ordinary game's behaviour or payload, and the replay harness proves it at each stage.
* The same helper carries the `cpuOpponent` flag (TACTICAL_AI_PLAN section 2).
* An optional `betaTesters` list of player ids in the same config lets chosen players see the
  feature on live while the flag is otherwise off.

**Entitlement - may this player use it? Built, switched off.**

* `CampaignManager::hasAccess($playerid)` returns true for everyone while
  `CampaignConfig::REQUIRE_ENTITLEMENT` is false (the default, R21).
* The `player_entitlement` table and the check exist from Stage 0 so that turning the model on
  later is a config change plus a payment integration, not a refactor. When on: enforced at the top
  of every campaign entry point, the UI greys out campaign entry with a tooltip, a campaign setting
  `openAccess` lets a host cover invited players, and a lapsed player keeps campaigns already joined.
* `accesslevel` on `player` is not reused (it is an admin level).

---

## 9. API contract

`GET campaigndata.php?campaign=ID&v=N` -> `CampaignView` or `{"unchanged":true}`.

`CampaignView` (TypeScript source of truth: `client/campaign/types/api.ts`; PHP builder: `CampaignView.php`):

```
{ id, name, version, turn, phase, deadline, settings,
  me: { empireId, credits, income, q40, ready },
  empires: [{ id, name, faction, colour, kind, eliminated, relation, ready }],
  map: { width, height, hexes: [{ q, r, terrain, vis, system? }] },
  systems: [{ id, q, r, name, owner, pop?, popCap?, income?, minerals?, q40?, upgrades?, queue?, ordnance?, claim? }],
  lanes: [...], fleets: [{ id, owner, q, r, name?, status, summary, units?, moves? }],
  battles: [{ id, q, r, status, gameid?, mode, myStance?, report? }],
  orders: [...], proposals: [...], messages: [...], events: [...],
  catalogue?: { units: [...], upgrades: [...] } }
```

Optional fields are present only when the viewer may know them. `fleets[].moves` (legal destination
hexes) is computed server-side for own fleets.

`POST campaigndata.php` body `{ campaign, turn, action, payload }` -> `{ ok, error?, view }`.
Actions: `create`, `join`, `leave`, `start`, `fleet.move`, `fleet.split`, `fleet.merge`,
`fleet.rename`, `fleet.stance`, `fleet.raid`, `queue.add`, `queue.cancel`, `queue.reorder`,
`repair.quote`, `repair.order`, `ordnance.buy`, `ordnance.load`, `lane.build`, `diplo.propose`,
`diplo.respond`, `diplo.declareWar`, `diplo.gift`, `diplo.tribute`, `msg.send`, `turn.ready`,
`turn.unready`, `battle.stance`, `battle.offerAuto`.
Every action re-checks: entitlement, membership, phase, turn number, ownership, affordability.
A stale `turn` is rejected with the fresh view.

Contract test: a PHP harness dumps a view to a fixture; a script wraps it as
`const v: CampaignView = <json>;` and runs `tsc --noEmit` on it, so a renamed or mistyped field fails
the pre-deploy check.

---

## 10. UI

One screen, map-first, panels as docked overlays (SCS look). Follow the standing UI rulings: passive
fading notices for information, confirm dialogs only for destructive or costly acts, options the
player cannot take are hidden rather than greyed,
combos keep their input affordances.

* **Top bar**: campaign name, ST number, phase, deadline countdown, credits and income, End Turn.
* **Galactic map**: pan / zoom / touch; hex hover card; click a system or fleet to open its panel;
  drag or click-to-path fleet movement with the path drawn and lane hexes highlighted; fog as a
  dark overlay with remembered hexes desaturated; owner borders.
* **System panel**: stats, upgrades, system queue and each shipyard queue (drag-to-reorder: Pointer
  Events, see the drag-reorder note), ordnance stock, trade route, garrison.
* **Fleet panel**: unit list with health %, damage summary, ammo, repair cost per unit, Repair All
  with its total on the button, split / merge / rename, resupply. Later stage: read-only SSD by
  mounting the existing React `ShipWindow` (it already runs on gamelobby.php with prototype-less ships).
* **Build dialog**: the FV buy dialog in campaign purchase mode, in a modal iframe (5.3a).
* **Diplomacy panel**: relation matrix, proposals in and out, gifts, tribute, public declaration.
* **Messages**: campaign chat, whisper target selector, turn report (events), battle reports.
* **Battle prompt**: stance choice per contact with force summaries limited to what the viewer can see.
* Notifications: reuse `DiscordNotifier` for "your campaign turn is waiting" and "battle created".
* Phone width must work (the headless phone-width check applies).
* A DATA ARCHIVE document for campaign rules (`docs/*.html`, `data-anchor` not `id`).

---

## 11. Testing

* CLI harnesses in `tests/campaign/` (the directory is gitignored like the rest of `/tests`; the
  replay harness shows the bootstrap - do not include `global.php` from CLI, the load guard exits
  silently). Dice are stubbed with a seeded generator.
  * `mapGenHarness.php`: determinism per seed, spacing 2-4, homeworld balance within 10%.
  * `resolveHarness.php`: scripted orders -> expected state, including simultaneous moves into one
    hex, lane moves, claims, raids, queue overflow, resume after an interrupted resolution.
  * `visibilityHarness.php`: per-empire view never contains a hidden fleet, system detail or message
    (fingerprint the JSON, as the replay harness's masking check does).
  * `bridgeHarness.php`: unit -> game -> harvest round trip; damaged unit -> game shows the same
    damage; ammo carried.
  * `autoCombatHarness.php`: calibration targets (6.5).
* `fvbuild.ps1 -Check` gains: `tsc --noEmit`, the contract fixture, and the campaign harnesses.
* The replay harness must stay green after the edits in 6.6 (ordinary games must be byte-identical).
* Play-testing follows the usual routine: the user creates a fresh campaign locally and reports the id.

---

## 12. Stages

Each stage ends playable or demonstrably verified. Nothing is committed by the implementer; the user
reviews and commits. Each stage lists its migration, and a stage that changes `tac_*` or existing PHP
runs the replay harness.

| Stage | Deliverable | Done when |
|---|---|---|
| **0 Foundations** | `db/campaign.sql`; `CampaignConfig`, `CampaignDB`, `CampaignManager` skeletons; `Features` helper + `$feature_flags`; `player_entitlement` + dormant gate; `campaigns.php` / `campaign.php` / `campaigndata.php` shells; TS toolchain and second Vite config; harness bootstrap | With the flag on, any player opens an empty campaign page; with it off there is no link and the pages 404; `tsc` runs in `-Check` |
| **1 Bridge spike** (no UI) | `CampaignRule`, `campunitid`, the extracted ship builder, `BattleBridge::createGame` and `harvest`, the two `process` seams, `getGamesToBeDeleted` guard | `bridgeHarness.php` round trip passes; a hand-seeded pair of fleets produces a real game two local players can play, and its result harvests correctly; replay harness green |
| **2 Map and lobby** | `MapGenerator`; create / join / start; faction, name, colour; galactic map viewer with terrain, systems, owners; static icons | A started campaign shows a balanced generated map to every player |
| **3 Turn engine** | orders, Ready, stepped `TurnResolver` (movement only), deadlines, `Visibility` + `camp_seen`, events, polling, Discord ping | Fleets move simultaneously in secret; fog verified by the harness; an interrupted resolution resumes |
| **4 Economy** | income, population, Q-40 stock and yield, claims, queues, shipyards, unit purchase through the buy dialog in campaign purchase mode + `CampaignPricing`, fleet split / merge, starting set-up and handicaps | A campaign can be played as a pure expansion race; a tampered enhancement price is rejected |
| **5 Battles, manual** | contact detection, stance prompts, game creation from live campaign state, harvest, retreat, hex control, battle reports | Two players meet, fight in FV, and the survivors return damaged to the map |
| **6 Automatic combat** | `AutoCombat`, retreat under fire, the full stance matrix, mid-battle auto-resolve | Calibration harness meets its targets; a campaign can be played without any manual battle |
| **7 Repair and supply** | repair quotes and queue, Repair All, destroyed-section surcharge, fighter replacement, ordnance stock and resupply | A damaged fleet can be made whole for the quoted price; ammo persists between battles |
| **8 Long battles** | pause at the 12-TT checkpoint, partial harvest of units that left, hold-in-hyperspace at set-up, reinforcements into a paused game (own jump point or the system's gate), jump buffer, no building under siege | A battle pauses at TT 12, a third fleet is moved in during the ST and arrives after the resume; a jump-less fleet arrives through the gate |
| **9 Diplomacy and comms** | relations, proposals, mutual defence, gifts, trade routes, raiding, chat / whisper / declarations | The relation matrix gates combat correctly in a 3+ player campaign |
| **10 Lanes and upgrades** | jump lanes (2 Q-40), system upgrades, bases and defences, defence caps | Lane movement at 3 hexes works; upgrades change growth and build rate |
| **11 NPCs** | `STRATEGIC_AI_PLAN.md` stages S0-S6: Raiders, bounty, tribute, neutral systems, favour, CPU empires. Manual battles against NPCs if the tactical AI is live, AutoCombat otherwise | A single-player campaign is playable end to end |
| **12 Polish** | animated icons, read-only SSD, victory screens, rules document, phone pass | - |
| (parallel) **Tactical CPU** | `TACTICAL_AI_PLAN.md`, its own stages T0-T9; independent of the campaign and may ship first. T9 is the campaign wiring | - |

Stages 0-7 are the minimum playable campaign. Stage 1 is first on purpose: if fleets cannot be
injected and harvested cleanly through the existing buying path, the design changes, and it is
better to learn that before any UI exists.

---

## 13. Decisions

### 13.1 Ruled by the user, 2026-11-12

| # | Question | Ruling |
|---|---|---|
| D2 | Population of an unclaimed system | Income = base income + a population bonus; fine-tune later (R4, R5, 5.3) |
| D3 | Pause a battle at each 12-TT checkpoint? | **Yes**, and an ST is played while it is paused (R12, 6.3) |
| D5 | A fleet with no jump engine arriving at a battle | Joins through the system's jump gate; every lane has a gate at each end (R9, R12) |
| D8 | Subscription | Not for now: everyone is entitled, architecture built but off; campaign hidden on live by feature flag until Beta (R21, R21a, section 8) |
| - | Raiders | Spawn in unclaimed systems, irregularly; just under average player fleet size; bounty for defeating them; raid player and CPU empire systems only, never neutral ones; never attack a fleet but can be attacked (R19, 5.2, section 7) |
| - | Neutral systems | Peaceful membership is in: a neutral system can join an empire at sustained maximum favour (`STRATEGIC_AI_PLAN.md` section 5) |
| - | Q-40 | A spent resource: 1 per jump-drive unit, 2 per lane; sources yield per ST, homeworld always 1 (R4a, 5.3) |
| D9 | Enhancements, refits and officers on campaign units | Yes, through the existing FV buy dialog (R19a, 5.3a) |
| D10 | Surprise war | Allowed on a NEUTRAL empire, effective at the same resolution; pacts need one ST notice. (The user restated this default without objecting - confirm before Stage 9.) |

### 13.2 Still open (the default is what will be built unless ruled otherwise)

| # | Question | Default |
|---|---|---|
| D1 | Turn structure | Simultaneous orders, then resolution (WEGO), as in 5.2 |
| D4 | Do hostile fleets swapping hexes fight? | No, they pass; contact needs the same hex at the end of a sub-step |
| D6 | What a detected enemy fleet reveals | Owner, count, hull sizes; classes too with ELINT in range |
| D7 | Who may travel a jump lane | Owner and empires at Friendship or better |
| D11 | 3+ hostile sides in one hex | One FV game, one team per side (FV already supports multi-team) |
| D12 | Can a base be built where the owner has no shipyard? | Yes, through the system queue |
| D13 | Map renderer | Canvas 2D (3.3) |
| D14 | Maximum players per campaign | 8 |
| D15 | Does one jump-capable unit carry a whole fleet off-lane? | Yes |
| D16 | Can a fleet part-way along a lane be contacted or seen? | No; it is in hyperspace until it reaches an end |
| D17 | Where the jump gate stands on the battle map | Mid-map, outside both deployment zones; neutral, usable by anyone |
| D20 | Does a Raider raid succeed when defenders are present but choose not to attack? | Yes; only driving the Raiders off or destroying them that ST stops it |
| D19 | Does raiding a Q-40 source steal Q-40? | No; the source simply yields nothing that ST |
| D18 | Can a fleet with no jump engine retreat from a system with no lane? | It cannot have got there, so no case; if its lane is destroyed later it is stranded and cannot retreat |

---

## 14. Traps to carry in (from existing FV work)

* POST-side ships carry no enhancements and no loaded notes; the bridge builds ships itself, so load
  what it needs explicitly.
* `submitShip` returns the new id as a STRING; cast to int before any strict comparison
  (`Manager::insertSingleShip` shows the fix).
* System ids are construction-order; a stored `damage` payload is only valid for the same class
  definition. If a ship class changes shape mid-campaign, `PreBattleDamage::sanitise` drops what no
  longer fits - log it as an event rather than failing the battle.
* A new shipid-keyed table would need `deleteGames()` and `leaveSlot()` handling; the campaign avoids
  that by keying its own tables on `camp_unit.id`.
* No `public static` on `Weapon`; any new client-needed flag on `ShipSystem` is protected + getter.
* Fighter flights are one sample fighter plus a number until populated server-side; per-fighter data
  is keyed by ordinal.
* Terrain and player-placed mines are never part of a harvested fleet.
* Do not change a campaign rule's meaning mid-campaign: `camp_campaign.settings` records a
  `rulesVersion`; resolution code branches on it if a rule ever has to change.
