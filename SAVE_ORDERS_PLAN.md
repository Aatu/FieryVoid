# Save Orders — park a half-finished phase and finish it later

A floppy-disk button beside the green commit tick. It stores the orders a player has given so far in
the current phase **without committing them**. When that player opens the game again (same device or
another), the page puts those orders back exactly as they were, and they carry on and commit as normal.

Status: **Stages 0-6 BUILT 2026-10-02 and committed (78dcc05d1). Stage 7 (Movement) BUILT 2026-10-03,
uncommitted.** Stages 0-4 were play-tested by the user (game 4437, Initial Orders) and Stages 5-6
(Deployment) end to end against the local site (game 4434). Stage 7 is verified by a vm harness only
(§7.6) and still needs a play-test. All six decisions were ruled by the user (see Rulings); Stage 7 went
ahead on the user's word that players do stop part-way through an activation. What the build changed or
added compared with the plan below is in §7 - read it before testing or extending.

A follow-up that builds on Stage 7 - tabletop mine detection at every hex, with the movement up to a
detection locked - is planned separately in `MINE_DETECTION_PLAN.md`.

## Why orders vanish today

Every order lives only in browser memory until the tick is clicked. The commit runs
`gamedata.onCommitClicked` → `doCommit` → `ajaxInterface.submitGamedata` → `construcGamedata()` builds
one POST → `Manager::submitTacGamedata` → the phase's `process()` writes `tac_power` / `tac_ew` /
`tac_fireorder` / `tac_individual_notes` and, as its last act, `updatePlayerStatus` marks the slot
committed. A reload rebuilds the page from the server (`game.php` → `parseServerData`), and the server
holds nothing for a phase the player has not committed — so the work is gone.

## Rulings (user, 2026-10-02)

- **D1 — drafts live in a server table** (`tac_savedorders`), so a save made on one device is there on
  any other. Browser localStorage was turned down because it would tie a save to one browser.
- **D2 — the core covers Initial Orders (1), Pre-Firing (5) and Firing (3).** Deployment (-1) is
  postponed to Stages 5-6, and Movement (2) is an optional Stage 7. Why they wait: Movement commits one
  activation at a time, so little is ever at risk, and restoring uncommitted moves means re-driving an
  icon pipeline that only consumes *committed* rows, plus thrust assignment and Extended Turn rows.
  Deployment's state is spread over deploy rows and a dozen client-only flags.
- **D3 — restore is automatic.** It is announced by a passive one-line notice directly beneath the phase
  banner (`#infowindow`), which fades with the banner and cannot be clicked (§1.3). No dialog.
- **D4 — one draft per player per game**, overwritten by each save; no history.
- **D5 — special-system settings are part of the core.** Stage 3 ships with Stages 0-2, because
  Adaptive Armor, hangar launches, Self-Repair and the rest are an important part of the game.
- **D6 — "Discard Saved Orders" lives in the OPTIONS tab** of the bottom panel, in a Saved Orders block
  above Save Fleet (§1.8). It deletes the saved copy and reloads the game, so the phase starts clean.

Out of scope, noted for later: autosave (a timer, or `beforeunload` + `navigator.sendBeacon`, through
the same capture), and an "unsaved changes" marker on the button.

## 0. What this builds on (investigation findings)

### 0.1 The commit path
- Button: `td.committurn` in `#phaseheader .uitable` (game.php:440), bound in windowevents.js:12.
- `gamedata.onCommitClicked` (gamedata.js:958) shows per-phase warnings; `gamedata.doCommit`
  (gamedata.js:1797) runs the blocking checks; both end in `ajaxInterface.submitGamedata`
  (ajaxInterface.js:323), which builds the payload with `construcGamedata` (ajaxInterface.js:992).
- `Manager::submitTacGamedata` (Manager.php:1622): player lock, transaction, turn/phase/activeship
  match, `hasAlreadySubmitted`, then the phase's `process()` (dispatch at Manager.php:1789-1801).
- What each `process()` reads from the POST:
  - Initial Orders (InitialOrdersGamePhase.php:174): power, individual notes, EW, fire orders
    (ballistic), the Jump Manifest (`arrivalVia`/`arrivalSpeed`/`arrivalHangar`).
  - Pre-Firing (PreFiringGamePhase.php:45): fire orders, late EW.
  - Firing (FireGamePhase.php:158): movement (combat pivots), fire orders, fire-phase declarations,
    individual notes, specialists used this phase, late EW.

### 0.2 Why a draft cannot go into the real order tables
1. `process()` is "validate, write, mark committed" in one pass — it ends in `updatePlayerStatus`
   (InitialOrdersGamePhase.php:365, FireGamePhase.php:250, PreFiringGamePhase.php:116).
2. The masking pipeline assumes write-once-at-commit. `TacGamedata::hideSystemFireOrders`
   (TacGamedata.php:1703) strips this turn's direct-fire orders in phase 3, and this turn's ballistic
   orders in phase 1, from **every viewer including the owner** — so orders parked there would not even
   come back to their author on reload.
3. `DBManager::submitFireorders` (DBManager.php:1513) filters by order type and phase, never by id:
   saving twice would insert twice.
4. Notes written by `generateIndividualNotes` load for every viewer, and some have load-time side
   effects that reach the enemy (`GraviticAugmenter::onIndividualNotesLoaded` had to be gated off for
   enemy viewers in phase 1 for exactly this reason).

So drafts need a store that no game logic reads.

### 0.3 Why a save cannot reuse `construcGamedata()`
It is a commit-time function with side effects:
- It calls `doIndividualNotesTransfer()` on every system, and several of those are **destructive**:
  - `Hangar` (baseSystems.js:389) **empties its pending launch/dock queues** after serialising them
    (baseSystems.js:417-431);
  - `DockingCollar` (baseSystems.js:1051) does the same to its LCV queues;
  - `ShieldReinforcement` (supportWeapons.js:115) **unsets boosts** when there is no fire order;
  - `PlasmaBattery` (baseSystems.js:3982) **charges `powerStoredFront` and `powerReq` up**.
  A Save built on it would wipe a player's queued fighter launches before the real commit.
- It splices past-turn power and fire orders out of the live systems (ajaxInterface.js:1067-1079,
  1102-1115).
- The checks in `doCommit` mutate too: `ew.convertUnusedToDEW` adds DEW (gamedata.js:2020) and
  `doVerifyAmmoUsage` rewrites `ammoUseArray`.

So a save needs its own read-only capture, and must not run any of the commit checks.

### 0.4 What the client builds by itself when a phase opens
- `InitialPhaseStrategy.activate` (InitialPhaseStrategy.js:18) runs
  `shipManager.power.repeatLastTurnPower()`, then `JumpEngine.continueAbductions()` and
  `continueVortexMaintains()`, which add this turn's orders.
- `copyLastTurnPower` appends last turn's power entries without checking what is already there
  (power.js:68).

So a restore must run **after** activation and **replace** this turn's entries, never add to them.

### 0.5 The page boot gives a clean hook
`game.php` inlines a per-player snapshot (it already sends `Cache-Control: no-store`) →
`gamedata.parseServerData` (game.php:185) → `webglScene.init` (game.php:197), which activates the phase
strategy synchronously (webglScene.js:96 → `activatePhaseStrategy`, PhaseDirector.js:90). Showing and
hiding the tick goes through exactly two functions, `gamedata.showCommitButton` / `hideCommitButton`
(gamedata.js:2699-2705), called by every phase strategy.

### 0.6 A player's own ships do not change until they commit
Within one turn and phase, nothing on the server changes a player's own ships until they commit: other
players' commits do not resolve until everybody has committed, and the phase cannot advance without
this player. (Jump gates are the one unit two players can order; another player's signal is stripped
from this player's phase-1 payload, so it never meets the draft.) That is what makes a draft safe: it is
valid for exactly one (game, player, turn, phase) and is ignored everywhere else.

## 1. Design

### 1.1 The invariant
**A restored draft puts the player's client back into exactly the state it was in when they clicked
Save, for the order kinds that phase's commit sends — nothing more.** If that holds, a commit after a
reload is indistinguishable from a commit without one, and none of the server's validation, masking or
resolution has to know drafts exist.

### 1.2 What is captured (own ships only)

| Kind | Phases | Source | Restore |
|---|---|---|---|
| EW | 1, 5, 3 | `ship.EW` entries with `turn == current` | replace this turn's entries |
| Power | 1 | `system.power` this turn (fighter systems too) | replace this turn's entries |
| Fire orders | 1, 5, 3 | `system.fireOrders` this turn (fighter systems too) | replace this turn's entries |
| Weapon mode | 1, 5, 3 | `weapon.firingMode` of a weapon holding this-turn orders | set |
| Combat pivots | 3 | `ship.movement` rows with `turn == current`, `id == -1`, `value == 'combatpivot'` (movement.js:1442) | drop such rows, append |
| System settings | per class | `system.getDraftState()` — Stage 3, §1.4 | `applyDraftState()` |
| Jump Manifest | 1 | `ship.arrivalVia` / `arrivalSpeed` / `arrivalHangar` — Stage 3 | set |

"Replace" also swaps entries the server sent (phase-1 EW re-sent in phase 3, ballistic launches in
phase 3, plasma-cloud markers). That is harmless: they cannot change within the phase (§0.6), and the
draft holds identical copies.

Format:

```json
{ "v": 1, "turn": 7, "phase": 1,
  "ships": { "<shipid>": {
      "EW":   [ ... ],
      "move": [ ... ],
      "ship": { "arrivalVia": 0, "arrivalSpeed": 0, "arrivalHangar": 0 },
      "sys":  { "<systemid>": { "power": [...], "fire": [...], "mode": 2, "state": { } } },
      "ftr":  { "<fighterid>": { "<systemid>": { "power": [...], "fire": [...], "mode": 1 } } } } } }
```

`v` lets the format change later; a draft with an unknown version is ignored. System ids are positional
(construction order), which is fine because a draft never outlives its turn and phase.

### 1.3 Restore
- **Where:** the end of `activatePhaseStrategy` (PhaseDirector.js:90) calls
  `savedOrders.applyPending(gamedata)`. One site serves all three phase strategies; the pending draft is
  consumed on first use, so later phase changes in the same session never see it.
- **Guards:** not replay, not waiting, phase in {1, 5, 3}, the draft's turn and phase equal the live
  ones. Ships are found with `gamedata.getShip` (the client has no `getShipById`) and must pass
  `ship.userid === gamedata.thisplayer`, the same test `construcGamedata` uses. Anything that does not
  resolve is skipped and counted.
- **Refresh:** `ShipEwChanged` and `SystemDataChanged` per restored ship, `ShipMovementChanged` for a
  ship whose pivots came back, `ballisticIconContainer.consumeGamedata` for launch markers, the fleet
  list, and `refreshHangarTooltip` on restored hangar queues.
- **Notice (D3):** one line directly beneath the phase banner: "Saved orders from 14:32 restored.
  Discard them in the OPTIONS tab." Add the skipped count if it is non-zero, and the day as well as the
  time when the save was not made today.
  - All three phase strategies raise the banner with `infowindow.informPhase(5000)` in `activate()`
    (InitialPhaseStrategy.js:29, PreFiringPhaseStrategy.js:20, FirePhaseStrategy.js:20). That runs just
    before this hook, so the notice fades in with the banner and out with it (`fadeTo(1000, 0.65)`,
    gone after 5 s).
  - Build it as a sibling element styled like `#infowindow` (tactical.css:620) and positioned under the
    banner's bottom edge, rather than as a line inside it: `#infowindow` is reused by `informFire`
    during replays, and a line left inside it would turn up there.
  - Give it `pointer-events: none`, so it never takes a click meant for the map.

### 1.4 Special-system settings (Stage 3)
About 25 client classes override `doIndividualNotesTransfer`. They fall into two groups.

**Nothing to save** — their commit value is derived from the arrays in §1.2: `PowerCapacitor`,
`PlasmaBattery` (reactor balance), `AmmoMagazine` (fire orders), `AmmoMissileRackF` and
`FlexPacketTorpedo` (fire orders + loading), `Engine` (movement; phase 2 anyway).

**State held outside the arrays** — needs a hook:

| Class | Phase | Fields (confirm while building) |
|---|---|---|
| AdaptiveArmorController | 1 | `allocatedAA`, `currchangedAA` |
| HyachComputer | 1 | `allocatedBFCP` |
| HyachSpecialists | 1, 3 | `currSelectedSpec`, `currAllocatedSpec` |
| SelfRepair | 1 | `priorityChanges` |
| StructureSelfRepair | 1 | `repairOrder` |
| ShieldReinforcement | 1 | `reinforceAmount` |
| ThirdspaceShield (+ ThoughtShield) | 1 | `currentHealth` |
| ChameleonSensors | 1 | `active` |
| FtrPetals, FtrGravShield | 1 | `active` |
| Hangar | 3 | `pendingLaunchOrders`, `pendingDockOrders`, `pendingBayShip*Orders` + their `*Dirty` flags |
| DockingCollar | 3 | `pendingLcvDockOrders`, `pendingLcvLaunchOrders` + their `*Dirty` flags |
| KirishiacOrbital | 3 | `active` |
| LightningArray | 3 | `active` (wide beam) |

Deployment-only, so they wait for Stage 6: `CloakingDevice`, `ShadingField`, `CaptorMine`,
`ProximityMine`, `MineControllerDEW`, and the deploy-start queues on Hangar/DockingCollar.

**Mechanism:** on the client `ShipSystem`, a `draftStateKeys` list (default `null`), a base
`getDraftState()` that deep-copies the listed fields, and `applyDraftState(state)` that assigns fresh
copies back and then calls an optional `afterDraftRestore()`. Most classes need one line, e.g.
`AdaptiveArmorController.prototype.draftStateKeys = ['allocatedAA', 'currchangedAA'];`. Opt-in, so a
class that is not listed behaves exactly as today. `doIndividualNotesTransfer` is never touched.

### 1.5 Save
- **Button:** a `td.saveturn` holding a floppy icon, to the left of `td.committurn` (game.php:440). It
  is shown by `showCommitButton()` only when the phase is 1, 5 or 3, and hidden by `hideCommitButton()`
  — so it follows the tick everywhere (waiting, replay, phase changes) with no visibility logic of its
  own.
- **Click:** capture (§1.2), then POST. No confirm dialog — saving destroys nothing. Show the blocking
  overlay (`ajaxInterface.showBlockingOverlay`) while the request is in flight, so the tick cannot be
  clicked mid-save. Do **not** go through `ajaxInterface.submiting`: `submitGamedata` returns early when
  it is set, so a commit clicked during a save would silently do nothing.
- **Feedback:** the same passive notice, shown on its own where the banner sits (top 130px): "Orders
  saved (14:32)". The floppy's `title` becomes "Save orders — last saved 14:32". Failures go through
  `confirm.error`.

### 1.6 Transport (D1)
Save and Discard POST to `saveOrders.php` (actions `save` and `clear`). On page load, game.php inlines
the player's saved copy as `window.fvSavedOrders = { savedAt, draft }`, or `null` when there is none.
Nothing else carries a draft.

### 1.7 Why not put the draft in the gamedata payload
- Gamedata is cached per player in APCu. A save would have to invalidate that player's cached JSON
  without `touchGame`, which would wake every other player's poll.
- A gamedata-level field reaches the client only if `parseServerData` copies it by name, and the
  payload is re-sent on every poll while the draft is needed once.

A separate global inlined by game.php avoids both, and game.php is already per-player and `no-store`.

### 1.8 Discard (D6) — the Saved Orders block in OPTIONS
The OPTIONS tab (game.php:945) holds the player's display preferences and, below them, the Save Fleet
block (`#fleetSavePanel`, driven by savedFleets.js). A **Saved Orders** block goes between the two
(`#savedOrdersPanel`, after `#gameOptionsList`), built the same way:
- **Content:** a `.fv-opt-heading` ("Saved Orders"); one sentence ("Keeps the orders you have given this
  phase so you can finish them later, on any device. Committing clears them."); a status line; and one
  chip button, **Discard Saved Orders**, styled like `#fleetSaveButton`
  (`.fv-log-chip .fv-log-chip--link`).
- **Status line:** "Turn 7, Initial Orders — saved 14:32." or "Nothing saved for this phase." The button
  is disabled when nothing is saved.
- **Visibility:** shown only to a player in the game who is not waiting and not in replay, in phase 1, 5
  or 3. Otherwise it is hidden the way `refreshSavePanel` hides Save Fleet (`css("display", …)`).
- **Refresh:** from the same places as Save Fleet — `parseServerData`, the tab's `onshow`, and after a
  save. Use the same state-string short-circuit, because `parseServerData` runs on every poll.
- **Discard:**
  1. `confirm.confirm` asks: "Discard your saved orders? This also resets every order you have given
     this phase, including changes you have not saved, and reloads the game."
  2. On yes, POST `clear`.
  3. Only once that succeeds, `location.reload()`. Reloading earlier would let game.php find the row
     and restore it again.
  4. On failure, show `confirm.error` and do not reload.

  It reloads rather than undoing in place because a reload is the same path as opening the game with
  nothing saved, so it cannot miss a field.

## 2. Server — Stages 0-1

### Stage 0 — table
`db/savedOrders.sql`, plus the same table in `db/emptyDatabase.sql`:

```sql
CREATE TABLE IF NOT EXISTS `tac_savedorders` (
  `gameid`   int(11)    NOT NULL,
  `playerid` int(11)    NOT NULL,
  `turn`     int(11)    NOT NULL,
  `phase`    int(11)    NOT NULL,
  `savedat`  int(11)    NOT NULL,
  `orders`   mediumtext NOT NULL,
  PRIMARY KEY (`gameid`, `playerid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

`savedat` is Unix time from PHP's `time()`, and the client formats it in the player's own time zone. A
`datetime` written with `NOW()` would bring in the database server's clock and zone, which on shared
hosting need not match PHP's.

DBManager gets `saveOrders` (`INSERT … ON DUPLICATE KEY UPDATE`), `getSavedOrders` and
`deleteSavedOrders`, and the table is added to `deleteGames()` (DBManager.php:4712). Not to
`leaveSlot()`: drafts only exist in live phases, never in the lobby, and the table is keyed by player,
not ship.

### Stage 1 — endpoint, delivery, cleanup
- **`source/public/saveOrders.php`**, modelled on saveFleet.php. The player comes from the session only,
  never from the request. Actions: `save`, which answers `{ "savedAt": <unix time> }`, and `clear`,
  which is what Discard calls (§1.8). `clear` needs only the session player and the gameid; it is
  allowed whatever the phase, so a stray row can always be removed.
- **`Manager::saveOrders(...)`** checks, in this order:
  1. gameid cast to int (`DBManager::getTacGame` interpolates it into SQL — DBManager.php:2795);
  2. the game exists and its status is ACTIVE;
  3. the player holds a slot;
  4. the posted turn and phase equal the game's, and the phase is 1, 5 or 3;
  5. `hasAlreadySubmitted` is false — built from `getTacGame` + `getSlotsInGame`, with no ship load
     (it only reads slots);
  6. the body is at most 1 MB and is valid JSON.

  It then re-encodes with `json_encode`'s **default** flags and upserts. Default flags keep the stored
  text pure ASCII, because the connection charset is 3-byte utf8 (see db/createGameRedesign.sql). Never
  use `JSON_UNESCAPED_UNICODE` or `JSON_NUMERIC_CHECK` here. No `touchGame`: nobody else needs to know.
- **game.php prologue** (after game.php:48): if the player is logged in, the data is valid, they are not
  waiting and the phase is 1, 5 or 3, read their row and keep it only if its turn and phase match. Wrap
  it in try/catch that falls back to `null`: a missing table (the live DB before the migration is
  applied) or a corrupt row must never break the game screen. Emit
  `<script>window.fvSavedOrders = { savedAt, draft };</script>` (or `null`), encoded with
  `JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT`.
- **Cleanup:** `deleteSavedOrders` in `submitTacGamedata` after a successful `process()` (around
  Manager.php:1803), and in the surrender branch (around Manager.php:1764). This is housekeeping, not
  correctness: a stale row can never be applied, because of the turn/phase and not-committed checks.
- The new endpoint stays out of the load guard's known-poll list (server_load_guard.php:54) — it is a
  user action, like saveFleet.php.
- Run `fvbuild.ps1 -Check` afterwards. Nothing here touches serialised ship state, so the replay
  harness should not move.

**No new trust.** The server never interprets a draft, returns it only to its author, and the client
applies it only to that author's own ships. The commit still runs the full validation, so a forged
draft can do nothing a forged commit POST could not already attempt.

## 3. Client — Stages 2-4 (core), 5-7 (later)

### Stage 2 — core
- New `source/public/client/savedOrders.js`: `capture()`, `applyPending()`, `save()`, `discard()`, the
  notice, and the OPTIONS block's refresh. Add its `<script defer>` tag to game.php's debug block (the
  bundler scrapes those tags), then build.
- The notice (§1.3): one element beside `#infowindow` in game.php, styled from `#infowindow`
  (tactical.css:620), plus `pointer-events: none`.
- The Saved Orders block (§1.8): markup in `#gameOptionsPanel` (game.php:946), between
  `#gameOptionsList` and `#fleetSavePanel`, with its rules beside Save Fleet's in logPanel.css §6 (see
  T11).
- Button markup at game.php:440, and a `.save` rule beside `.ok` (tactical.css:605). New
  `img/save.png`, 30×30 to match `ok.png`.
- `#phaseheader` reserves 45px of right padding for **one** icon (tactical.css:524, and again at :1233
  and :1348 in the two phone layouts). Widen all three, and shrink the floppy to 18px alongside the tick
  (tactical.css:1271-1275 and :1369-1373).
- Hooks: `showCommitButton` / `hideCommitButton` (gamedata.js:2699-2705) and `activatePhaseStrategy`
  (PhaseDirector.js:90).

### Stage 3 — system settings and the Jump Manifest (core, D5)
The base mechanism on the client `ShipSystem` (model/shipSystem.js), the opt-ins from §1.4,
`afterDraftRestore` on Hangar and DockingCollar (tooltip refresh), and the manifest fields.

### Stage 4 — docs
A paragraph in `source/public/docs/faq.html` and a line in `starter-guide.html`: what Save keeps, that
it covers only the current phase, that committing clears it, which phases have it, and that Discard is
in the OPTIONS tab.

**Release:** Stages 0-4 go live together. Apply `db/savedOrders.sql` to the live DB before uploading
the PHP (T10).

### Stage 5 — Deployment: placement (BUILT 2026-10-02 - see §7.5)
- What a Deployment commit sends: this turn's `deploy` movement rows. DeploymentGamePhase::process
  validates them (`validateDeployment`) and inserts them.
- On the client, placement is tracked by the deploy rows, `ship.deploymove`, and a base's rotation (the
  `movement[1].value` that `onCommitClicked` checks). Arriving reinforcements are placed automatically
  when the phase opens, so the replace-after-activation rule (§0.4) applies here too.
- The restore has to move the deployment icons as well as the data, and leave DeploymentPhaseStrategy's
  own "still to place" bookkeeping consistent.

### Stage 6 — Deployment: system settings (BUILT 2026-10-02 - see §7.5)
Through the Stage 3 mechanism:
- mine ranges: `CaptorMine`, `ProximityMine`, `MineControllerDEW` (`mineSet` and the ranges);
- stealth: `CloakingDevice`, `ShadingField` (`active`);
- deploy-start docks: Hangar `pendingDeployStartOrders`, DockingCollar `pendingLcvDeployStartOrders`,
  and the ships' `pendingDeployDock` / `pendingLcvDeployDock`;
- Specialist picks (the Deployment commit refuses without them);
- `KirishiacOrbital` (`active`; it also transfers in phase -1).

### Stage 7 (optional) — Movement (BUILT 2026-10-03 - see §7.6)
- Movement commits per activation: the active ship, or a whole initiative category under
  `SimultaneousMovementRule`. A save would hold that activation's uncommitted rows: moves, turns,
  pivots, rolls, speed changes with their `assignedThrust`, Extended Turn begin/complete rows, and
  Engine contraction.
- Ship icons consume only committed rows (`ShipIcon.consumeMovement`), and a `ShipMovementChanged` fired
  while a thrust panel is open redraws the thruster ring (an Extended Turns trap). So a restore would
  have to drive the movement UI rather than drop rows in.
- Before building it, find out whether players actually leave mid-activation.

## 4. Traps

- **T1.** Never build a draft with `construcGamedata()` or `doIndividualNotesTransfer()` (§0.3).
- **T2.** Never run the commit checks on save — `convertUnusedToDEW` and `doVerifyAmmoUsage` mutate
  (§0.3).
- **T3.** Restore after phase activation, replacing this turn's entries (§0.4).
- **T4.** Never write drafts into the `tac_*` order tables (§0.2).
- **T5.** Keep the save off `ajaxInterface.submiting` (§1.5).
- **T6.** game.php must survive a missing table or a corrupt row (§2, Stage 1).
- **T7.** The utf8 column needs ASCII JSON; the inline `<script>` needs the `JSON_HEX_*` flags (§2).
- **T8.** Some client system fields are shared between instances of the same class, so
  `applyDraftState` assigns fresh deep copies, never a shared parsed object.
- **T9.** A weapon's displayed mode lives on the weapon, while each order carries its own
  `firingMode`. Restore both, or a restored shot shows under the wrong mode.
- **T10.** Deploy order: apply `db/savedOrders.sql` to the live DB **before** uploading the PHP. The live
  DB is a remote shared MariaDB and `emptyDatabase.sql` never reaches it; T6 is the safety net if the
  order slips.
- **T11.** `#gameOptionsList:empty+#fleetSavePanel` (logPanel.css:1635) is an adjacent-sibling rule. With
  the Saved Orders block between the two it stops matching, so retarget it at the new block. Also add
  the block to the `max-width` rule (logPanel.css:1579) and the paragraph rule (:1641).
- **T12.** The OPTIONS block is refreshed from `parseServerData`, which runs on every poll. Skip the DOM
  writes unless a state string changed, as `refreshSavePanel` does.
- **T13.** Discard reloads only after `clear` succeeds; an early reload restores the draft again (§1.8).

## 5. Test matrix (create a fresh game per test)

1. **Initial Orders:** EW on three ships, a boost, a system powered down, a missile launch, an Adaptive
   Armor allocation, a Self-Repair priority. Save, reload: everything is back. Commit: `tac_ew`,
   `tac_power`, `tac_fireorder` and `tac_individual_notes` match a control game committed without a
   reload.
2. **Firing:** direct fire, a split-shot weapon, a called shot, a fighter combat pivot, a hangar
   launch, a Lightning Array wide beam. Save, reload, commit; compare as in 1.
3. **Pre-Firing:** a pre-firing weapon order survives a reload.
4. **Cross-device:** save in one browser, open the game in another: the orders are there.
5. **Housekeeping:** save, then commit — the row is deleted; the next phase restores nothing.
6. **Save twice** — the second overwrites the first.
7. **Save, then surrender** — the row is deleted.
8. **Opponent:** their gamedata, replay and combat log are unchanged, and the draft never appears in
   any payload but the author's (check the network tab).
9. **Waiting or spectating:** nothing is restored and no button shows.
10. **Live-migration slip:** with the table missing, game.php still loads and Save reports an error.
11. **Phones** (CDP device emulation at 390px, not `--window-size`): both icons visible and tappable in
    portrait and landscape.
12. **Stale row:** edit the row's turn by hand — it is ignored.
13. **Notice:** it appears beneath the phase banner and fades with it, and a click on the map underneath
    it still lands.
14. **Discard:** OPTIONS shows the saved time. Discard, then confirm: the game reloads with a clean
    phase, nothing is restored, and the row is gone. Cancelling the confirm changes nothing.
15. **Nothing to discard:** with nothing saved the button is disabled; while waiting, in replay, or in
    Movement or Deployment the block is hidden.
16. **An older save:** a save made on an earlier day shows the day in the notice and in the OPTIONS
    line.

Movement (Stage 7):

17. **One ship, standard movement:** accelerate, move, turn (thrust auto-assigned), pivot, roll, slip.
    Save, reload: the plot is back where it was, the movement icons sit at its end, and the thrust
    readouts match. Cancel back through two restored steps, re-plot, commit: `tac_shipmovement`
    matches a control game plotted the same way without a reload.
18. **Simultaneous movement:** two of your ships in one group, one plotted fully and one part-way.
    Save, reload, finish, commit. Then, with an opponent in the same group: they commit while your
    save is parked, you reload - your plot still comes back.
19. **Thrust panel open:** start a turn, leave its panel open, save, reload - that turn is gone and
    everything before it is there.
20. **Specialists and contraction:** a Hyach Engine Specialist used while moving, and a Mindrider
    contraction. Save, reload: the thrust budget, contraction level and defence match; cancelling
    the contraction takes it back to where the turn began.
21. **A detaching pod:** detach and move off, save, reload: still detached, still moved.
22. **Floppy and OPTIONS:** while you hold the activation the floppy shows from the start, before the
    tick; once you commit (waiting) both go and the OPTIONS block hides.
23. **Stale row:** save in one ship's activation, commit it, put the row back by hand, open your next
    activation - nothing is restored and OPTIONS says nothing is saved.

## 6. Files

| File | Stage | Change |
|---|---|---|
| `db/savedOrders.sql`, `db/emptyDatabase.sql` | 0 | new table |
| `source/server/controller/DBManager.php` | 0 | 3 methods; `deleteGames()` |
| `source/server/controller/Manager.php` | 1 | `saveOrders` / `clearSavedOrders` / `getSavedOrdersJSON`; cleanup in `submitTacGamedata` |
| `source/public/saveOrders.php` | 1 | new endpoint |
| `source/public/game.php` | 1-2 | prologue read, inline global, button, notice, Saved Orders block, script tag |
| `source/public/client/savedOrders.js` | 2 | new |
| `source/public/client/gamedata.js` | 2 | show/hide hooks |
| `source/public/client/renderer/PhaseDirector.js` | 2 | restore hook |
| `source/public/styles/tactical.css`, `source/public/img/save.png` | 2 | button, notice |
| `source/public/styles/logPanel.css` | 2 | Saved Orders block (T11) |
| `source/public/client/model/shipSystem.js` + the §1.4 class files | 3 | draft hooks |
| `source/public/docs/faq.html`, `starter-guide.html` | 4 | text |
| `source/public/client/savedOrders.js` | 7 | Movement capture / restore, floppy without the tick |
| `source/public/client/gamedata.js` | 7 | `hideCommitButton` keeps the floppy in Movement |
| `source/public/client/model/system/baseSystems.js` | 7 | Hyach Specialists also kept in phase 2 |
| `source/server/controller/Manager.php`, `source/public/game.php` | 7 | phase 2 allowed; activation check |
| `source/public/styles/tactical.css`, docs | 7 | comment; FAQ and starter-guide text |

Rough size: Stages 0-2 are about one session; Stage 3 is about one more, most of it confirming each
class's fields in play. Stage 4 is small. Stages 5-6 together are about one session; Stage 7 is
unsized until its value is confirmed.

## 7. Build notes (2026-10-02, Stages 0-4)

Everything in §2-§3 for Stages 0-4 is built as written, except where this section says otherwise.

### 7.1 Where the build departs from the plan, and why
- **`deleteGames()` guards its `tac_savedorders` DELETE** (try/catch, and only when there are ids).
  `game.php` calls `getTacGamedata` with turn null, which runs `deleteOldGames` → `deleteGames` on
  **every page load**, and global.php turns a failed `prepare` into an exception. Unguarded, a live DB
  without the table would have taken the game screen down for everybody (new trap T14).
- **Cleanup runs after the commit's transaction**, through `Manager::deleteSavedOrdersQuietly`, which
  swallows and logs any failure. A failing housekeeping DELETE inside the transaction would have
  rolled the player's commit back.
- **The draft travels as a JSON *string* inside the POST body**, the way `submitGamedata` sends
  `ships`. `saveOrders.php` decodes the body as an array, which would turn every `{}` in the draft into
  `[]`; the server decodes the draft itself as objects for the same reason (new trap T15).
- **Unowned jump gates.** Any player may signal a fixed gate in Initial Orders and `construcGamedata`
  posts that order even on an enemy-owned gate, so an own-ships-only draft would have dropped it. The
  draft carries such a gate as `{ gate: true, sys: { <engineId>: { fire } } }`, phase 1 only, and the
  restore touches only that gate's `jumpEngine` orders. (Safe for the same reason as §0.6: every
  current-turn order on a gate in a phase-1 payload is this client's own.)
- **Every own ship gets a draft entry, even an empty one, and the restore resets every system of a
  drafted ship**, not just the systems the draft lists. That is what keeps an order the player
  removed before saving removed - e.g. a re-declared abduction or vortex maintain cancelled in Initial
  Orders. A ship the draft does not list is left alone.
- **A weapon's mode goes back through `weaponManager.restoreFiringMode`** (→ `setFiringMode`, or a
  bounded `changeFiringMode` cycle), never a bare assignment: the mode switch rebuilds range, fire
  control, damage and the info readouts (T9).
- **The bar's two-icon reservation is dynamic.** `showCommitButton` / `hideCommitButton` toggle
  `#phaseheader.fv-save-shown` with the floppy (80px desktop, 70px on both phone layouts), so Movement
  and Deployment keep the one-icon 45px and their ship name is not squeezed for nothing. Measured under
  CDP emulation at 1280, 390 portrait and 844×390 landscape: both icons inside the bar, clear of the
  phase text.
- **The notice is `width: max-content`** between `min(400px, 90%)` and `90%`: one line on a desktop or a
  landscape phone, wrapping at the banner's width in portrait.
- **The blocking overlay's label is swapped** to "SAVING ORDERS..." / "DISCARDING SAVED ORDERS..." for
  the length of the request; "TRANSMITTING ORDERS..." would read as a commit.
- **The OPTIONS block also refreshes from the PhaseDirector hook**, so it follows replay entry and exit
  and the switch to waiting, none of which run `parseServerData`.

### 7.2 Stage 3 - the fields as confirmed
| Class | Phases | What is kept |
|---|---|---|
| ChameleonSensors | 1 | `active` |
| Hangar (+ Catapult, FighterRail, Shadow hangar) | 3 | `pendingLaunchOrders`, `pendingDockOrders`, both `*Dirty`; a Docking Bay's `pendingBayShipDock/LaunchOrders` + `*Dirty`. `afterDraftRestore` → `refreshHangarTooltip` |
| DockingCollar | 3 | its OWN `pendingLcvDockOrders`, `pendingLcvLaunchOrders` + `*Dirty` (it extends Hangar, so it must override the list) |
| KirishiacOrbital | 3 | `active`, then `updateDockingStatus()`. Deployment's half waits for Stage 6 |
| AdaptiveArmorController | 1 | `availableAA`, `allocatedAA`, `currchangedAA`, `AAtotal_used`, `AApreallocated_used`, **`pressignedReset`** - the last stops `initializationUpdate`'s once-per-page reset re-running over restored counters. Then `refreshData()` |
| HyachComputer | 1 | `allocatedBFCP`, `BFCPtotal_used`, then `refreshData()` |
| HyachSpecialists | 1, 3 | **A replay, not a field copy.** `doUse()` changes the SHIP (defence, reactor/scanner/engine output, to-hit bonus, weapon readouts), so the draft records which Specialists are `allocated` this phase and the restore calls `doUse()` for each behind `canUse()`. Not in Pre-Firing: its `process()` saves no notes |
| SelfRepair | 1 | `priorityChanges` |
| StructureSelfRepair (+ Coop) | 1 | `repairOrder` |
| ShieldReinforcement | 1 | `reinforceAmount` (derived from the boost on init, but nothing guarantees an init between restore and commit) |
| ThirdspaceShield (+ ThoughtShield) | 1 | `currentHealth`, then `initializationUpdate()` |
| **ThirdspaceShieldGenerator** (+ ThoughtShieldGenerator) | 1 | **`storedCapacity`** - not in the plan's list. Every shield point moves through it, and the commit warns when it is not zero |
| FtrPetals, FtrGravShield | 1 | `active` |
| LightningArray | 3 | `active`, then `initializationUpdate()` (the orders come back already priced) |

### 7.3 Two new traps
- **T14.** `DBManager::deleteGames()` runs on every `game.php` load. Anything added to it that can
  throw takes the game screen down; guard it.
- **T15.** A JSON POST body decoded as an array turns every `{}` inside it into `[]`. A map-shaped
  payload must travel as a string inside the body and be decoded as objects.

### 7.4 Verified, and still owed
- Verified: `php -l` on the four PHP files (in a throwaway `fieryvoid-php` container - the dev
  environment was down); the server's JSON round trip (`{}` kept, stored text pure ASCII, no `</script>`
  in the inline global, `"0012"` kept a string); `node --check` on every edited JS file and both
  rebuilt legacy bundles; an esbuild parse of `tactical.css` and `logPanel.css`; a vm harness that
  loads the real model files and checks save → reload → activation → restore in phases 1, 5 and 3 (90
  checks: EW, power, fire orders, modes, pivots, gate signals, AA, hangar queues, shields, petals,
  Lightning Array; stale, wrong-version, waiting and second-apply cases) - itself proved by three
  injected regressions - and a second one for the Specialists replay (14 checks, no double bonus).
- Since then (same day): `fvbuild.ps1 -Check` passed in full (autoload up to date, no new ship-data
  findings, replay corpus 132/0). The user's game 4437 (Initial Orders) was loaded headlessly as its
  player with every POST blocked: re-capturing the page straight after the restore reproduced the
  stored draft exactly, with the notice, floppy title and OPTIONS status all correct.
- Still owed: the rest of the play-test matrix in §5.
- Deploy: apply `db/savedOrders.sql` to the live DB **before** uploading the PHP (T10). Locally,
  apply it to the Docker DB before testing.

### 7.5 Stages 5-6 - Deployment (built 2026-10-02)
- **Phase -1 is a save phase** everywhere: `Manager::$savedOrdersPhases`, the `game.php` read and the
  client's `isSavePhase`. The phase is "Deployment" or "Pre-Turn Orders" in the status line, the same
  split the phase header makes.
- **The floppy shows from the start of Deployment** (`savedOrders.syncButton`, run from the
  PhaseDirector hook), not with the tick. Deployment's tick stays hidden until every unit has a legal
  placement, and the point of saving there is to stop before that. Waiting and replay still remove it.
- **Placement (Stage 5):** every uncommitted row of this turn on an own unit - in practice the one
  `deploy` row, which carries the speed and turn-arrow edits and a base's rotation in its `value`. The
  restore drops this turn's id -1 rows (including the ones activation just made for an arriving wave),
  appends the draft's, and re-points `ship.deploymove` at the restored row object: `deploy()` and the
  arrows edit that object in place, so a copy would cut them off.
- **The unit markers for a deploy-start dock** (`pendingDeployDock`, `pendingLcvDeployDock` - which also
  marks a Docking Bay ship, with `bay: true` - and `forcedDeployDock`) travel as the draft's `dock`
  record and are set or deleted to match it.
- **After a Deployment restore** the client calls `window.refreshDeploymentUIForDeployStart()`, the
  helper the deploy-dock dialog already uses: it re-runs the commit gate against the restored
  placements, hides docked units' icons, and refreshes the EDF previews and hangar tooltips.
- **Stage 6 system settings**, all through the Stage 3 mechanism:

| Class | What is kept in phase -1 |
|---|---|
| Hangar (+ Docking Bay, Catapult, Rail) | `pendingDeployStartOrders`, `pendingBayShipDeployStartOrders` + their `*Dirty` |
| DockingCollar | `pendingLcvDeployStartOrders` + `*Dirty` |
| CaptorMine | `allocatedRanges`, `mineSet` (the flag the commit's "ranges not set" warning reads), then `refreshData()` |
| ProximityMine | `allocatedShipTypes`, `mineSet`, then `refreshData()` |
| MineControllerDEW | `allocatedRanges` (flat or per weapon), `mineSet`, then `refreshData()` |
| CloakingDevice | `active`, then `initializationUpdate()` |
| ShadingField | `active` |
| KirishiacOrbital | `active` (now phases -1 and 3) |
| HyachSpecialists | the PICKS (`currSelectedSpec` = `selected`), replayed through `canSelect()` / `doSelect()` |

- **Checked, and needing nothing:** Adaptive Armor, Power Capacitor and Plasma Battery all write
  Deployment notes, but from server-side state alone - the commit reads no client input for them.
- **Verified:** a vm harness for Deployment (26 checks: placements, speed, facing, base rotation, mine
  ranges, cloak, a flight queued aboard a carrier, `deploymove` identity, an activation-placed arrival
  replaced rather than duplicated), proved by three injected regressions; the Specialists harness grew
  six checks for the picks; and an end-to-end run on the real local site (game 4434, player 210): 21
  units placed and five mines ranged through the page, Save posted for real, a fresh page load
  restored it identically, the test row then removed through `clear`. `php -l` clean on all changed PHP.

### 7.6 Stage 7 - Movement (built 2026-10-03)
- **Go-ahead (user, 2026-10-03):** players do stop part-way through an activation - one ship's move is
  many separate orders, and under `SimultaneousMovementRule` a whole group of ships is being plotted at
  once.
- **Phase 2 is a save phase** everywhere: `Manager::$savedOrdersPhases`, the `game.php` read, the client's
  `isSavePhase`, and the OPTIONS status line ("Movement").
- **A Movement draft belongs to an ACTIVATION, not just the turn and phase.** The draft carries
  `active` - this player's own active ship ids, sorted - and the restore runs only when that list equals
  the player's current active ships. Only their OWN: under simultaneous movement every other player's
  units leave `gamedata.activeship` as those players commit, so the whole list changes under a draft
  that is still good. A mismatch can only be a row a failed cleanup left behind after an earlier
  activation was committed; it is ignored silently and not remembered (OPTIONS really has nothing saved
  for this activation).
- **What is kept:** for each own ship of the activation, this turn's id -1 rows (the player's), up to
  the first row still waiting for its thrust (`commit` false - the thrust panel is open on it; it cannot
  be reopened on a fresh page, and nothing can be plotted after it). Rows the server sent are never
  touched: they have real ids, or none at all - the Gravitic Augmenter's transient free jink is id
  `null`. Forced rows that activation itself adds (a base's rotation, a gravitic ship's continuing pivot,
  an Extended Turn cancel) are kept too, and the restore swaps them for the copies rather than doubling
  them. No EW, power or fire orders: the Movement commit sends none.
- **A unit riding a host is not drafted** (attached and not detached): it gives no orders of its own and
  its rows are the host's, mirrored by `PhaseStrategy.onShipMovementChanged`, which the host's restore
  fires. A pod that has detached this turn is drafted like any other unit.
- **Beside the rows, three things are put back** (`applyPlottedMoves`):
  - `assignedThrust` is a sparse array keyed by thruster id, and JSON turns its holes into nulls. Its
    readers skip holes but not nulls: `revertAutoThrust` (the cancel button) subtracts each null from
    the `channeled` of whatever system has that id - NaN on an engine - and `calculateAssignedThrust`
    turns "nothing assigned" into 0 in the payment arithmetic. Rebuilt with its holes (new trap T16).
    Positions are rebuilt as `hexagon.Offset`, as a click makes them.
  - a committed contraction has already moved the Mindrider engine's level, the hull's defence and the
    Thought Shields' armour (`amendContractValue`, run by `doneAssignThrust`) - replayed once per row;
  - a detach sets `ship.detached`.
  Then each thruster's `channeled` is recomputed from the rows (`refreshMovementCaches`).
  `ship.currentturndelay` is deliberately left alone: the click paths write it, but its only reader is
  `adjustTurnDelay`, which nothing calls - every gate recomputes the delay from the rows.
- **Hyach Specialists are kept in Movement** (`draftStatePhases` now `[-1, 1, 2, 3]`): Engine,
  Maneuvering and Thruster can be used there, Movement's `process()` writes their notes, and an Engine
  Specialist's thrust is what the restored moves were paid with. Same replay through `canUse()` /
  `doUse()` as in the other phases.
- **After the restore the strategy re-picks its unit** (`MovementPhaseStrategy.selectActiveShip`):
  activation chose and drew icons for the first unit with movement left BEFORE the rows came back.
- **The floppy shows from the start of Movement**, before the tick, like Deployment
  (`savedOrders.syncButton`, now phases -1 and 2). Movement hides the tick after every step that leaves
  movement unspent, so `gamedata.hideCommitButton` now asks `savedOrders.keepsButtonWithoutTick()` and
  leaves the floppy while the player can save; it goes when they start waiting. The bar keeps its
  two-icon room all phase, so the phase text does not jump as the tick comes and goes. Measured under CDP
  emulation (header replica, long ship name): text clear of the icons and on one line at 1280, 390
  portrait and 844x390 landscape; in portrait the phase and ship name, already truncated before this,
  lose about four more characters.
- **Server: an activation check replaces the commit check** in `Manager::saveOrders`. `hasAlreadySubmitted`
  never fires in Movement (a slot's `lastphase` stays at Initial Orders until the phase ends), so phase 2
  asks `holdsMovementActivation` - one of the player's slots is not `waiting`. Movement clears `waiting`
  for exactly the players whose units are active, and it is the same flag `game.php` reads before
  inlining a draft. The cleanup after a commit already ran for every phase, Movement included.
- **Discard's confirm** now says it resets the orders "that are not yet committed", because in Movement
  the activations already committed this phase stay as they are. Same wording in the FAQ.
- **Verified:** a vm harness that loads the real model files and the real `ships.js`, `systems.js`,
  `criticals.js`, `power.js` and `movement.js`, plots through the real functions (speed change, moves, a
  paid turn, an Engine Specialist, a flight's jink, a contraction, a detach, a base's activation
  rotation, a second turn left with its panel open), saves, rebuilds the page from server state, runs
  activation, restores, and compares - then cancels back through restored rows. 53 checks, each of five
  injected regressions caught by exactly the checks it should trip (nulls left in `assignedThrust`, no
  contraction replay, no activation guard, pending row captured, `detached` not set). The core (90),
  Deployment (26) and Specialists (20) harnesses still pass. `php -l` clean on `Manager.php` and
  `game.php`; `node --check` clean on every changed file and on the rebuilt legacy bundles.
- **Still owed:** a play-test of §5 items 17-23 on the local site.
- **New trap:**
  - **T16.** A movement row's `assignedThrust` is sparse, keyed by thruster id. A JSON round trip leaves
    nulls where the holes were, and the readers treat a null as an entry. Anything that stores and
    restores movement rows must rebuild the holes.
