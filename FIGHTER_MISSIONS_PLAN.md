# Fighter Missions — Kitchen Sink §6.12 for fighter flights

Status: **PLANNED 2026-10-20 — nothing built.** Rulings R1-R4 (§0.1) are the user's; D1-D14 (§0.2) are
defaults taken without asking — say the word to change any. Build the stages of §5 one at a time; §6 lists
the gates, §7 the play test, §9 is the build record.

Sources: the user's brief (2026-10-20); *AoG Kitchen Sink* §6.12 *Optional Fighter Missions* (pp. 61-62), the
combat sequence (fighter missions are set and announced with EW, **after** initiative is rolled), and §11.5.1
*Expert Dogfighter*.

---

## 0. Rulings and defaults

### 0.1 Rulings (user, 2026-10-20)

| # | Question | Ruling |
|---|---|---|
| R1 | Do missions cost points? | **Free, in-game only.** The brief's CSP lines "Cost: 50% base cost of fighter / Availability: 1 per flight" belong to the Expert Dogfighter (KS 11.5.1); 6.12 prices no mission. Nothing changes in Fleet Selection |
| R2 | When is a pick free of the −60? | **At every arrival on the map**: the first Initial Orders after deployment, a reinforcement arrival, or any hangar launch — a re-launch after docking, a partial-launch Split and a carrier escape included (KS: "defined when the fighters launch"). A re-launched flight keeps its old mission unless it changes it; a new Split starts on No Mission |
| R3 | CSP "cannot intentionally leave" its zone | **Advisory.** Movement is never blocked. A CSP flight outside its zone gets **no initiative bonus**, and its **−15 OB against ships still applies** (it applies inside the zone too). A passive notice warns when a move ends outside |
| R4 | Who may not take a mission? | **Shuttle-type craft, Breaching Pods and unmanned craft**: every shuttle category (default, armed, assault, minesweeping, cargo/medical/presidential shuttles, Shuttlecraft, Flyers), lifeboats and yachts, the Orieni Hunter-Killers, Mapmaker probes, drones and weapon platforms (the officers' "no pilots" ruling) |

### 0.2 Defaults (ask before changing)

| # | Default |
|---|---|
| D1 | **Units.** FV values are tabletop × 5, as the brief writes them: −12 Ini → **−60**; the Dogfighter's +1 → **+5**; OB −3 → **−15** shown (3 points of `offensivebonus`), "minimum 1" → **5** shown; Anti-Ship +1 → **+5** shown; Scout's "defence −1" → **−5%** to be hit (1 profile point, both profiles) |
| D2 | **What a change is.** Any committed mission different from the one selected — a switch *to* No Mission and a new CSP key ship included. On the change turn the flight takes −60 Ini and has **no** mission effects (neither old nor new); the new mission works from the next turn |
| D3 | **When initiative moves.** The −60 and CSP's +5 are both applied when Initial Orders close, by the Triad Command Node's mechanism (rewrite `tac_iniative` after the roll). During Initial Orders players see the rolled order; it updates on commit (the brief's own note). CSP's +5 needs the key ship on the board and ≤ 10 hexes away **at that moment** — i.e. where both stood at the end of last turn's movement |
| D4 | **Ties after a shift** (classic movement; simultaneous movement re-derives the category, as the Triad does): CSP's +5 **wins** ties (nudged up, as the Dogfighter wins them); a −60 **loses** them (nudged down) |
| D5 | **OB arithmetic.** The mission term applies to the offensive bonus *before* the existing `tmpsensordown` / Detect Mines deductions. A penalty never takes OB below 1 point (and never raises one already below 1). Anti-Ship's +1 is for non-ballistic shots at non-fighter targets only. "Ship" = any unit that is not a `FighterFlight`; "fighter" = any `FighterFlight`, shuttles and pods included. The OB the ship window shows stays the base figure (the term depends on the target); the hit-chance breakdown carries it |
| D6 | **Defensive Intercept** = the existing fighter-escort intercept rule minus its "same hex at the end of last turn" half: the flight intercepts ballistics aimed at a friendly non-flight unit sharing its hex when fire resolves. Everything else is the escort rule unchanged (the weapon's arc toward the launch hex is "face its guns towards the launch hex"; ballistics only; never fire aimed at another flight) |
| D7 | **CSP key ship** = any unit of the flight's team that is on the board, not destroyed, not a flight, not terrain, not a mine. A destroyed or departed key ship ends the zone (no bonus), not the mission; re-designating is a change (D2) |
| D8 | **Who sees what.** Every viewer sees a flight's mission; a choice made in the current Initial Orders reaches opponents only once the phase closes (the EW rule — so an opponent's same-turn ballistic preview cannot know about a free pick, exactly as it cannot know DEW). The CSP **key ship is own-team only**: publishing it would point at a shaded or cloaked ship's whereabouts |
| D9 | **No initiative projection** during Initial Orders (the Triad has one, its C3). The tooltip says "−60 Ini when Initial Orders close" instead |
| D10 | **Scout's extra jink level is client-only**, beside the Expert Pilot's free levels — `movement.php` neither prices nor caps jinking. `jinkinglimit` is never raised: the lobby weight class, hangar boxes and flight-size cap all read it |
| D11 | Missions are picked in **Initial Orders only** (KS: "at the same time EW is announced") |
| D12 | **UI**: the tooltip icon opens an anchored dropdown (not a native `<select>`); choosing CSP opens a second list of key ships sorted by distance (after a launch the carrier is normally first — no carrier detection is built) |
| D13 | Not built: carrier tracking, a zone ring on the map, a combat-log line, a fleet-list badge (§8) |
| D14 | Mission data is **turn-tagged history** and every effect asks "on turn T", so a replay or the printed log shows the mission that was in force on the replayed turn — this sidesteps the replay-at-current-turn trap ([[arch_replay_and_advance_gamedata_turns]]) |

---

## 1. The rules, in FV terms

> The mission is defined when the fighters launch and can be changed during the scenario only by forcing the
> fighters to suffer a −12 initiative penalty on the turn the change is made (during which they operate with no
> specific mission parameters). Missions are changed at the same time EW is announced. Fighter missions are
> always known. *(KS 6.12)*

| Mission | Initiative | Offensive bonus (all weapons unless said) | Defence | Other |
|---|---|---|---|---|
| **No Mission** | — | — | — | the default |
| **Combat Space Patrol** | +5 and wins ties while within 10 hexes of its key ship (stacks with an Expert Dogfighter) | −15 vs ships (min 5) | — | zone is advisory (R3) |
| **Anti-Ship** | — | +5 vs ships, non-ballistic only; −15 vs fighters (min 5) | — | |
| **Scout Penetration** | — | −15 (min 5) | both profiles −5 | +1 jink level, paid in thrust as usual |
| **Defensive Intercept** | — | −15 (min 5) | — | escorts a friendly ship in its hex without the start-of-turn rule (D6) |

Every mission: changing costs −60 Ini on the change turn, which has no mission effects (D2); the pick at an
arrival is free (R2).

---

## 2. What exists today (findings)

- **Initiative** is rolled at the turn change: `Manager::changeTurn` → `generateIniative`
  ([Manager.php:2619](source/server/controller/Manager.php#L2619), [:2627](source/server/controller/Manager.php#L2627)),
  which also forces every total distinct. The **Triad Command Node** already changes it afterwards:
  `CnC::applyTriadCommandNodes` ([baseSystems.php:3426](source/server/model/systems/baseSystems.php#L3426)), called first in
  `InitialOrdersGamePhase::advance` ([InitialOrdersGamePhase.php:10](source/server/Phase/InitialOrdersGamePhase.php#L10)), shifts
  totals with the private `shiftIniative` (classic and simultaneous-movement aware, [:3491](source/server/model/systems/baseSystems.php#L3491))
  and `breakCommandNodeTies` ([:3516](source/server/model/systems/baseSystems.php#L3516)), then writes `DBManager::updateIniative`
  ([DBManager.php:1821](source/server/controller/DBManager.php#L1821)).
- **Fighter escort interception**: `Firing::isLegalIntercept`'s flight branch requires the same hex now **and** at the end of
  last turn ([firing.php:1569-1607](source/server/handlers/firing.php#L1569), the test at
  [:1599](source/server/handlers/firing.php#L1599)); the client twin is `weaponManager.canInterceptBallistic`
  ([weaponManager.js:3252-3257](source/public/client/weaponManager.js#L3252)) through `sharesHexNowAndAtStartOfTurn`
  ([:3153](source/public/client/weaponManager.js#L3153)). Automation, manual INCOMING-list orders and `FirePhaseStrategy`'s
  escort selection all go through these two predicates.
- **Fighter OB**: one `$effectiveOB` in `Weapon::calculateHitBase` ([weapon.php:1935-1942](source/server/model/weapons/weapon.php#L1935))
  feeds both direct fire ([:1979](source/server/model/weapons/weapon.php#L1979)) and the ballistic grant
  ([:1990](source/server/model/weapons/weapon.php#L1990)); the client twin is `computeOEW`'s `effectiveOB`
  ([weaponManager.js:2087](source/public/client/weaponManager.js#L2087), ballistic at [:2093](source/public/client/weaponManager.js#L2093)).
  An Expert Evader's sent Evade is already inside the server's `offensivebonus`; the client subtracts an unsent one
  (`officers.pendingEvadeOb`).
- **A fighter target's profile** has a flight-only branch in `calculateHitBase` (Petals +1,
  [weapon.php:2240-2248](source/server/model/weapons/weapon.php#L2240)). ⚠️ Petals' client mirror is NOT in weaponManager: when the
  flight's systems are first built, `FtrPetals.initializationUpdate` ([baseSystems.js:5078](source/public/client/model/system/baseSystems.js#L5078))
  rewrites the flight's `forwardDefense`/`sideDefense` (hard-coded per hull), so the preview reads it as Base Defense. Checked
  2026-10-20 against game 3671's payload — do not "add" it again. The client builds the goal and its breakdown rows in
  `calculateHitChange` ([weaponManager.js:2529](source/public/client/weaponManager.js#L2529), rows from
  [:2546](source/public/client/weaponManager.js#L2546), invariant check at [:2566](source/public/client/weaponManager.js#L2566)).
- **Jinking limit** is enforced only in the client, `movement.js` `canJink`
  ([movement.js:341](source/public/client/movement.js#L341)), already `jinkinglimit + officers.freeJinks(ship)`; the server's
  `Movement::getJinking` ([movement.php:1214](source/server/handlers/movement.php#L1214)) never caps.
- **A ship-level Initial Orders claim already exists**: officer moves. Client `ajaxInterface.construcGamedata`
  ([ajaxInterface.js:1143-1148](source/public/client/ajaxInterface.js#L1143)) → `Manager::getShipsFromJSON`
  ([Manager.php:2807](source/server/controller/Manager.php#L2807)) → `InitialOrdersGamePhase::process` checks it on the
  **reloaded** ship ([InitialOrdersGamePhase.php:303-304](source/server/Phase/InitialOrdersGamePhase.php#L303)) →
  `Officers::saveTransfers` writes a note with `Manager::insertIndividualNote` ([Officers.php:1083](source/server/model/ships/Officers.php#L1083)),
  which `DBManager::getSystemDataForShips` intercepts before any system sees it
  ([DBManager.php:3804-3827](source/server/controller/DBManager.php#L3804)).
- **A flight has no flight-level note hook**: `FighterFlight` hands generate/save/onIndividualNotesLoaded straight to its craft
  ([FighterFlight.php:518-546](source/server/model/ships/FighterFlight.php#L518)) — hence the interception route above.
- **Arrival on the map**: `BaseShip::getTurnDeployed` covers deployment, late slots and hyperspace reinforcements
  ([ShipClasses.php:3680](source/server/model/ships/ShipClasses.php#L3680)); `BaseShip::$hangarLaunchTurn` is the latest launch or
  escape turn, docking-bay riders excluded, stamped by `Hangar::onIndividualNotesLoaded`
  ([baseSystems.php:4651-4672](source/server/model/systems/baseSystems.php#L4651)) and already published to the client
  ([ShipClasses.php:955](source/server/model/ships/ShipClasses.php#L955)).
- **Initial Orders tooltip buttons** live in `ShipTooltipInitialOrdersMenu.buttons`
  ([shipTooltipInitialOrdersMenu.js:62-101](source/public/client/UI/shipTooltipInitialOrdersMenu.js#L62)); the Triad entries and
  their null-safe predicates and `redrawAfterTriadChoice` ([:315-392](source/public/client/UI/shipTooltipInitialOrdersMenu.js#L315)) are
  the pattern. A button action is called without its click event ([shipTooltipMenu.js:159](source/public/client/UI/shipTooltipMenu.js#L159)).
- **Status displays**: the tooltip's notice block ([ShipTooltip.js:490-544](source/public/client/UI/ShipTooltip.js#L490): cyan notices,
  the Triad's yellow line, Navigator/Escorting); the ship window's `getStatusBanners`
  ([ShipWindow.js:2375](source/public/client/UI/reactJs/shipWindow/ShipWindow.js#L2375), lobby-safe).
- **Phase-1 masking**: `TacGamedata::deleteHiddenData` strips an opponent's EW and power in Initial Orders
  ([TacGamedata.php:1680-1701](source/server/model/TacGamedata.php#L1680)); it runs on the live path only — a replay of a past turn
  calls `prepareForPlayer(true)`, which skips it ([:1060](source/server/model/TacGamedata.php#L1060)).
- **Save Orders** records phase-1 ship fields in `captureShip`'s `rec.ship`
  ([savedOrders.js:270-277](source/public/client/savedOrders.js#L270)); a flight's craft are not captured as systems
  ([:279-296](source/public/client/savedOrders.js#L279)), so a flight-level choice cannot ride `draftStateKeys`.
- **Static gates**: `TacGamedata::$officersPresent` and friends ([TacGamedata.php:20-88](source/server/model/TacGamedata.php#L20)),
  reset inside the note sweep ([DBManager.php:3793-3799](source/server/controller/DBManager.php#L3793)).
- **The icon exists**: `source/public/img/FighterMission.png` (40×40, tracked). Singular — the CSS must use this exact name (live is
  case-sensitive).

---

## 3. Design

One new server class (`FighterMissions`), one client rules module (`fighterMissions.js`), one small UI file (the dropdown), and a
gated line at each existing choke point. No schema change, no new table, no static-blueprint change.

### 3.1 Storage — one note per change

| Field | Value |
|---|---|
| table | `tac_individual_notes` |
| `notekey` | `FtrMission` |
| `notekey_human` | `Fighter mission: <label>` — ≤ 40 characters ("…Combat Space Patrol" is 36); `substr(…, 0, 40)` anyway |
| `notevalue` | `<MISSION>:<keyShipId or 0>:<free 0/1>` — e.g. `CSP:4123:1` |
| turn / phase | the Initial Orders turn / 1 |
| shipid / systemid | the flight / its sample fighter (`getSampleFighter()->id`) — a note needs a system id |

- **Written by the server only**, in `InitialOrdersGamePhase::process`, after validation on the reloaded load (§3.7).
- **`free` is decided once, by the server, when the note is written.** Nobody ever re-derives a past turn's arrival.
- **Read in the note sweep before delivery** (beside `Officers::takeNote`): recorded on the flight
  (`FighterFlight::recordFighterMission`), never handed to a craft, and it sets `TacGamedata::$fighterMissionsPresent = true`
  (reset at the top of the sweep, beside `$abductionPresent`).
- Why notes rather than a table: no migration, no `deleteGames()`/`leaveSlot()` cleanup ([[arch_orphan_shipid_rows_recycled_ids]]),
  and history loads for every turn for free.

### 3.2 The one rule: the mission in force on turn T

```
missionOn(flight, T):
  e = the flight's latest entry with e.turn <= T          (at most one entry per turn)
  no entry              -> NONE
  e.turn == T, !e.free  -> CHANGING: no mission effects; -60 Ini; e.mission from T+1
  otherwise             -> e.mission (with e.key for CSP)
```

"Selected" (for "is this pick a change?") is the latest entry with turn < T, read as its mission and key.

⚠️ **MIRROR PAIR**: `FighterMissions::missionOn($flight, $turn)` ↔ `fighterMissions.missionOn(ship, turn)`. On the client only, the
owner's **pending** choice (§3.9) counts as a would-be entry for the current turn during Initial Orders, so every preview matches what
the commit will do — the Evader's `pendingEvadeOb` precedent.

### 3.3 Arrival turn (the free pick)

```
arrivalTurn(flight) = max( getTurnDeployed(flight), hangarLaunchTurn + 1 when set )
free pick  <=>  T == arrivalTurn(flight)
```

A launch resolves at the end of turn N, so the flight's first Initial Orders on the board is N + 1. `$hangarLaunchTurn` is the
**latest** launch, which is exactly R2's "every arrival". The server is authoritative (the note's `free`); the client mirror
(`shipManager.getTurnDeployed` + the published `hangarLaunchTurn`) only labels the dropdown "free" or "−60". Stage 0 proves every
arrival path stamps one of the two.

### 3.4 Server: `FighterMissions.php`

New `source/server/model/ships/FighterMissions.php`, beside `Officers.php`, static only:

- `$registry` — id → label, summary. Ids: `NONE`, `CSP`, `ASA` (Anti-Ship), `SCOUT`, `DEFINT`. Never numeric strings
  ([[arch_json_numeric_check_free_text]]).
- Constants: `NOTE = 'FtrMission'`, `CHANGE_PENALTY = -60`, `CSP_INI = 5`, `CSP_RANGE = 10`, `OB_PENALTY = 3`, `OB_FLOOR = 1`,
  `ASA_OB = 1`, `SCOUT_PROFILE = -1`.
- `$excludedHangarTypes` + `isEligible($flight)` — §4, the one table.
- `takeNote`, `history`, `missionOn`, `selectedBefore`, `arrivalTurn`, `keyShipValid`, `inZone`.
- `saveClaim($flight, $claim, $gamedata)` — §3.7.
- `applyInitiative($gamedata, $dbManager)` — §3.6.
- `adjustOffensiveBonus($shooter, $target, $weapon, $turn, $ob)`, `profileModifier($target, $turn)`,
  `isDefensiveIntercept($flight, $turn)` — §3.5.
- `addForJson($flight, $stripped)`, `hideCurrentTurnChoice($flight, $turn)` — §3.8.

`FighterFlight` gains a **protected** `$fighterMissions = array()` and a protected `$fighterMissionClaim` with accessors. Protected
keeps both out of every static blueprint (the `$crewQuality` reasoning). New class → `fvbuild -Autoload`.

### 3.5 Effects — one gated line per choke point

Every server line opens with `TacGamedata::$fighterMissionsPresent`; every effect is asked **for the turn the shot or order belongs
to** (`$fireOrder->turn`), never the load turn (D14).

| Effect | Server | Client mirror |
|---|---|---|
| OB | `calculateHitBase`: build `$effectiveOB` from `adjustOffensiveBonus(…, $shooter->offensivebonus)` before the crit / mine deductions ([weapon.php:1935-1942](source/server/model/weapons/weapon.php#L1935)). Direct fire and the ballistic grant both read it unchanged | `computeOEW`: `adjustOffensiveBonus(shooter, target, weapon, shooter.offensivebonus - pendingEvadeOb)` before its deductions ([weaponManager.js:2087](source/public/client/weaponManager.js#L2087)) — both sides feed the same base |
| Profile (Scout) | the flight branch beside Petals ([weapon.php:2240-2248](source/server/model/weapons/weapon.php#L2240)): `$defence` and `$defenceFake` both | `calculateHitChange`: a term in the goal and a `Fighter Mission` row via `pushIfNonZero`, so the breakdown invariant holds; none under a flat called-shot profile |
| Defensive Intercept | [firing.php:1599](source/server/handlers/firing.php#L1599): `same hex now && (same hex last turn \|\| isDefensiveIntercept)` | [weaponManager.js:3257](source/public/client/weaponManager.js#L3257): the same OR, with `sharesHexNow` split out of `sharesHexNowAndAtStartOfTurn` |
| Jink (Scout) | none (D10) | `canJink`'s limit gains `+ fighterMissions.extraJinkLevels(ship)` ([movement.js:341](source/public/client/movement.js#L341)) |
| Initiative | §3.6 | none — the server writes `tac_iniative`; the tooltip explains |

```
adjustOffensiveBonus(shooter, target, weapon, T, ob):
  s = missionOn(shooter, T);  NONE or CHANGING -> ob
  penalty(b) = max(min(b, 1), b - 3)
  CSP    -> target is a flight ? ob : penalty(ob)
  ASA    -> target is a flight ? penalty(ob) : (weapon.ballistic ? ob : ob + 1)
  SCOUT, DEFINT -> penalty(ob)
```

### 3.6 Initiative at the close of Initial Orders

- `CnC::shiftIniative` and `CnC::breakCommandNodeTies` become `public static`, and the tie-breaker gains `$step = 1`. The Triad passes
  nothing and is unchanged. This is the smallest change that shares the one tested implementation (it already handles classic
  `unmodified_iniative` and the simultaneous-movement category).
- `FighterMissions::applyInitiative` is called in `InitialOrdersGamePhase::advance` **right after** `CnC::applyTriadCommandNodes`,
  before `setPhase(2)`. It opens on the static gate. Turn = `$gamedata->turn` (advance has not moved it); it never branches on the
  phase ([[arch_individual_notes_and_phase_hooks]] trap 1).
- For each eligible flight with history that is on the board (not removed/docked, not destroyed, not in hyperspace):
  `CHANGING` → shift −60 (nudge list *down*); `CSP` with its key ship valid and in zone → shift +5 (nudge list *up*).
- Classic movement: break ties (+1 for up, −1 for down), then `updateIniative` per changed row and `doSortShips()`.

### 3.7 Claim path, client → server

- **Client**: the pending choice lives on the ship (`ship.fighterMissionOrder = {m, k}`), Initial Orders only.
  `construcGamedata` sends `newShip.fighterMission` beside `officerTransfers`, only when it differs from the selected mission.
  Read-only, so the commit-time side-effect trap ([[arch_commit_time_client_side_effects]]) does not apply.
- **Server**: `Manager::getShipsFromJSON` carries it raw onto a `FighterFlight`'s claim; every phase but Initial Orders ignores it.
- `InitialOrdersGamePhase::process`, in the own-ships loop that saves officer moves:
  `FighterMissions::saveClaim($gd->getShipById($ship->id), $ship->getFighterMissionClaim(), $gameData)`. `$gd` is the reloaded load —
  the POST-side ship has no notes ([[arch_post_side_ship_reconstruction]]). It checks owner, eligibility, on the board, a known id, and for
  CSP a valid key ship (ids compared loosely — `getShipById` uses `===`); skips a no-change; sets `free = (turn == arrivalTurn)`; writes
  one note.

### 3.8 Payload and masking

- `FighterFlight::stripForJson` → `FighterMissions::addForJson`. An **eligible** flight always carries `fighterMission: {h: [...]}`,
  `h` = `[{t, m, k, f}]`, possibly empty; an ineligible one never does — so `isEligible` needs no client mirror. `k` only to the flight's
  own team (D8).
- **Masking**: in `deleteHiddenData`'s phase-1 branch, for a ship that is not the viewer's (the EW rule's own test),
  `hideCurrentTurnChoice` drops history entries dated the current turn. Live path only: a replay of a past turn shows everything,
  correctly.
- The replay harness's masking fingerprint gains `fighterMission`.

### 3.9 Client: `fighterMissions.js`

`source/public/client/fighterMissions.js` → `window.fighterMissions`, the `officers.js` pattern; a `<script defer>` after
`client/officers.js` in game.php's debug block ([game.php:409](source/public/game.php#L409)). game.php only; React callers guard on
`window.fighterMissions`.

API: `REGISTRY` (labels and summaries), `isEligible`, `missionOn`, `selected`, `arrivalTurn`, `isFreePick`, `canChoose`,
`pending` / `setPending` / `clearPending`, `claim`, `keyShipCandidates`, `inZone`, `adjustOffensiveBonus`, `profileModifier`,
`extraJinkLevels`, `isDefensiveIntercept`, `tooltipLine`, `bannerText`. Key ships are looked up with `gamedata.getShip`
([[arch_client_getship_vs_getshipbyid]]).

### 3.10 UI

1. **Tooltip icon** — one entry in `ShipTooltipInitialOrdersMenu.buttons` after the Triad entries:
   `{ className: "fighterMission", condition: [isSelf, canChooseFighterMission], action: openFighterMissionPicker, info: fighterMissionInfo }`.
   `canChooseFighterMission` tests `this.selectedShip` for null first — one throwing condition deletes the whole tooltip
   ([[arch_tooltip_menu_null_selection]]). CSS `.fighterMission { background-image: url("../img/FighterMission.png"); }` beside
   `.setCommandNode` in `styles/shipTooltip.css`. Hover line, e.g. "Fighter Mission: Anti-Ship → Scout (free this turn)" or
   "… (−60 Ini when Initial Orders close)" — short, it must not widen the tooltip.
2. **Dropdown** — `client/UI/fighterMissionPicker.js` (`window.FighterMissionPicker`): a body-level popover anchored to the clicked
   button, so a tooltip redraw cannot orphan it; closed by an outside click, Escape, the tooltip hiding or a phase change. Styled as
   `.fv-missionpicker` in `styles/hexPicker.css` (the hex stack picker's surface family; tokens only, no new `:root`). Five rows, each
   with a one-line effect summary; the selected mission marked, the pending one highlighted, the cost (free / −60) on top. CSP opens a
   second list of key ships: name, distance, "outside zone" beyond 10. Choosing the selected mission clears the pending order. After a
   choice the tooltip redraws (the Triad's `redrawAfterTriadChoice`).
   - `shipTooltipMenu.js` `onClick` passes its event as a second argument (`action.call(this, isMaxClick, event)`); existing actions
     ignore it. A long press has no event — the picker then anchors to the tooltip.
   - Clamped to the viewport with an 8px inset; no phone sheet mode.
3. **Status line, every viewer** — one line from `fighterMissions.tooltipLine(ship)` in the tooltip notice block: cyan "Combat Space
   Patrol (Omega 1, in zone)" / "Anti-Ship" / "Defensive Intercept: covering Omega 1"; yellow (`#e1b000`, the Triad's) "Changing mission
   −60 Ini → Scout Penetration"; own pending "Pending: Scout Penetration (free)". Opponents never see a key-ship name (D8).
4. **Ship window** — one banner from `bannerText` in `getStatusBanners`. React → `fvbuild -Client`.
5. **CSP advisory (R3)** — in the Movement commit ([gamedata.js:1463](source/public/client/gamedata.js#L1463)), for each own CSP flight
   among the active ships whose end hex is more than 10 from its key ship's current hex: `savedOrders.showNotice("Alpha Wing ends outside
   its CSP zone: no initiative bonus next turn (−15 OB vs ships still applies)")`. Passive, never a confirm ([[feedback_passive_notices]]).

### 3.11 Save Orders

`savedOrders.captureShip`, phase 1: `rec.mission = ship.fighterMissionOrder` when set; `applyShip` restores it; `countShipOrders`
counts it.

### 3.12 Docs

`docs/faq.html`: a new **Fighter Missions** entry (`data-key="fightermissions"`, *Rules & Mechanics*), straight after **Fighter
Escorts** — §1's table, the free pick, the −60, who sees what, how to pick. Fighter Escorts gains one line pointing to Defensive
Intercept. `data-anchor`, never `id=` ([[project_document_viewer]]).

### 3.13 What an ordinary game pays

- Every load: one string compare per note, one static reset.
- Every shot: one static read at each of three sites — false in any game without a mission note.
- Initial Orders close: one static read.
- Payload: about 20 bytes per eligible flight; nothing on ships or ineligible craft.
- Client: one property read (`ship.fighterMission`) per call site.

### 3.14 What must not move

- A game with no `FtrMission` note: bit-identical hit chances, interceptions and initiative. The only payload change is the new key on
  eligible flights. The replay corpus holds no mission notes, so any harness diff other than that key is a bug.
- The Triad Command Node: unchanged — default `$step`; regression cases in the new harness.
- Escort interception without Defensive Intercept: unchanged — the new clause is OR-ed behind the gate.
- Expert Pilot free jinks and the Evader's −1: unchanged; the mission term composes after them.

---

## 4. Eligibility (R4)

```
isEligible(flight):
  flight is a FighterFlight
  and not instanceof Shuttle                    (default shuttles, Flyers, minesweeping shuttles)
  and not $remoteControl                        (Orieni Hunter-Killers)
  and its hangar category, lowercased:
      contains no "shuttle"                     (armed, assault, minesweeping, cargo, medical, presidential, Shuttlecraft)
      and is not in $excludedHangarTypes        ('breaching pods', 'lifeboats', 'yacht', 'flyers', 'hunter-killers',
                                                 'mapmaker probes', 'weapon platforms', + drone categories Stage 0 finds)
```

One table: excluding another category is one line. Stage 0 prints every `FighterFlight` class with its category and verdict for
review before anything is built.

---

## 5. Stages

| Stage | Contents | Exit gate |
|---|---|---|
| **0 — Audit** (no behaviour change) | (a) a scratch CLI listing every `FighterFlight` class: hangar category, `Shuttle`?, `$remoteControl`, verdict. (b) a scratch loader printing `getTurnDeployed`, `hangarLaunchTurn` and `arrivalTurn` per flight per turn for every arrival path: deployment on turn 1, late slot, hyperspace reinforcement, hangar launch, deploy-start dock then launch, partial-launch Split, carrier escape, rail escape, Shadow Fighter Bomb, dock then re-launch (hangar-ops corpus games 4350, 4351, 4239, 4177, 4151; a fresh game for anything not covered) | both tables in §9, reviewed by the user; any path that stamps no arrival is fixed in Stage 1 or ruled out |
| **1 — Server core** | `FighterMissions.php` (registry, eligibility, history, `missionOn`, `arrivalTurn`, `saveClaim`, `addForJson`, `hideCurrentTurnChoice`); `FighterFlight` fields; note interception + static gate; `getShipsFromJSON` claim; Initial Orders `process` hook; phase-1 mask; autoload | `fighterMissionsHarness.php` incl. a real DB round trip in a rolled-back transaction; `fvbuild -Check` at today's baseline |
| **2 — Initiative** | `CnC` helpers shared; `FighterMissions::applyInitiative` | harness: classic and simultaneous movement; ties both ways; Triad regression (+10, swap, +1 nudge); off-board flights skipped; CSP in zone, out of zone, key ship destroyed |
| **3 — Combat effects** | server OB, profile and Defensive Intercept lines; `fighterMissions.js` (rules part); `computeOEW`, `calculateHitChange` row, `canInterceptBallistic`, `canJink` | harness: each mission × ship/fighter target × direct/ballistic; client harness over the real `weaponManager.js` / `movement.js`; one fixture table proving server and client agree on the hit chance |
| **4 — UI and commit path** | tooltip button and CSS, dropdown and CSS, status line, ship-window banner, `construcGamedata` claim, Save Orders, CSP notice, game.php script tags; `fvbuild -Client` | `initialOrdersTooltipHarness.js` with the new entry on a null selection; client harness for the picker; a standalone headless driver in the `officersE2E` style (session planting is refused) |
| **5 — Docs and play test** | `faq.html` entries; §7 with the user | the user's play-test game |

---

## 6. Gates (local; `/tests` is gitignored)

- `docker exec -w /usr/src/current fieryvoid-php-1 php tests/replay/fighterMissionsHarness.php [--no-db]` — new.
- `node tests/replay/fighterMissionsClientHarness.js` — new.
- `node tests/replay/initialOrdersTooltipHarness.js` — stays green with the new entry.
- `scripts/fvbuild.ps1 -Check` — autoload map, ship-data validator, replay harness (five checks) at today's baseline.
- `officersHarness.php` / `officersClientHarness.js` — unchanged (they exercise `sortShips`, the Evader's OB and the Expert Pilot's jinks).
- A mutation pass over each stage, as the officers stages do: drop each gate, flip each sign, remove the mask — every mutant must fail a
  harness.

---

## 7. Play test (new game per test; the user sends the gameID)

1. Turn 1, a flight deployed on the board: the icon shows; CSP with a key ship within 10 hexes is marked free; after commit there is no −60
   and the flight's initiative is +5. An opponent sees nothing until both have committed, then sees "Combat Space Patrol" without a key-ship name.
2. Turn 2: change CSP → Anti-Ship. The flight takes −60 when Initial Orders close; its turn-2 shots carry no mission term; from turn 3 its
   direct fire at ships is +5% and at fighters −15%, and its missiles get no +5%.
3. Scout Penetration: an enemy's hit chance against the flight is −5%; one jink level beyond the flight's limit is allowed (with thrust);
   its own shots are −15% (never under 5).
4. Defensive Intercept: the flight ends movement in a friendly ship's hex it did NOT start in; an enemy ballistic at that ship can be
   hand-intercepted from the INCOMING list, and the automation uses the flight when left to it.
5. CSP: end a move more than 10 hexes from the key ship → passive notice; next turn no +5; shots at ships still −15%.
6. Launch a flight from a hangar on turn N: its pick on N+1 is free. Dock it, re-launch it: free again, old mission kept if unchanged. A
   partial launch makes a Split that starts on No Mission with a free pick.
7. Shuttles, breaching pods and Hunter-Killers show no icon.
8. Save Orders: pick a mission, save, reload — the pending pick is restored and commits.
9. Replay an earlier turn: the tooltip shows the mission in force on that turn.

DB check after each: `tac_individual_notes` rows with `notekey = 'FtrMission'`; `tac_iniative` for the change turn.

---

## 8. Out of scope (built only on request)

- Carrier tracking (CSP defaulting to the carrier that launched the flight).
- A 10-hex zone ring drawn for a selected CSP flight.
- Initiative projection during Initial Orders (D9).
- A combat-log line per mission change, a fleet-list mission badge.
- Any lobby or Fleet Selection change (R1).

---

## 9. Build record

*(one subsection per stage, as built)*

---

## 10. Traps this feature walks past

- POST-side ships carry no notes or enhancements: validate the claim on the reloaded `$gd` ([[arch_post_side_ship_reconstruction]]).
- `advance()` has already moved the phase: use the turn, never branch on the phase ([[arch_individual_notes_and_phase_hooks]]).
- `notekey_human` is `varchar(40)`; an overflow aborts the whole submission.
- A throwing tooltip condition deletes the whole tooltip; `this.selectedShip` can be null; `shipManager.getTurnDeployed(null)` throws.
- Never raise `jinkinglimit` for a bonus ([[project_officers]] Stage 18).
- `TacGamedata::getShipById` compares with `===`: match note and JSON ids loosely ([[project_triad_advanced_features]]).
- weaponManager.js's second `calculateHitChange` (~2600-2800) is commented-out code — do not edit it.
- A new legacy JS file needs its `<script>` tag in game.php's debug block and a bundle rebuild; a React change needs `fvbuild -Client`.
- The static gate is reset inside the note sweep, not in `markUnavailableSetMarkers` (which runs after it) ([[arch_defensive_mod_aggregation]]).
- The image is `FighterMission.png` — exact case on live ([[arch_dockingcollar_icon_case]]).
