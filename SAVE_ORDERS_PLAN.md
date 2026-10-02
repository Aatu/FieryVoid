# Save Orders — park a half-finished phase and finish it later

A floppy-disk button beside the green commit tick. It stores the orders a player has given so far in
the current phase **without committing them**. When that player opens the game again (same device or
another), the page puts those orders back exactly as they were, and they carry on and commit as normal.

Status: **planned, nothing built.** All six decisions were ruled by the user on 2026-10-02 (see
Rulings), so Stage 0 can start whenever the user decides to go ahead. Stages 0-4 are the core and go
live together; Stages 5-6 (Deployment) are postponed; Stage 7 (Movement) is optional.

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

### Stage 5 (postponed) — Deployment: placement
- What a Deployment commit sends: this turn's `deploy` movement rows. DeploymentGamePhase::process
  validates them (`validateDeployment`) and inserts them.
- On the client, placement is tracked by the deploy rows, `ship.deploymove`, and a base's rotation (the
  `movement[1].value` that `onCommitClicked` checks). Arriving reinforcements are placed automatically
  when the phase opens, so the replace-after-activation rule (§0.4) applies here too.
- The restore has to move the deployment icons as well as the data, and leave DeploymentPhaseStrategy's
  own "still to place" bookkeeping consistent.

### Stage 6 (postponed, ships with 5) — Deployment: system settings
Through the Stage 3 mechanism:
- mine ranges: `CaptorMine`, `ProximityMine`, `MineControllerDEW` (`mineSet` and the ranges);
- stealth: `CloakingDevice`, `ShadingField` (`active`);
- deploy-start docks: Hangar `pendingDeployStartOrders`, DockingCollar `pendingLcvDeployStartOrders`,
  and the ships' `pendingDeployDock` / `pendingLcvDeployDock`;
- Specialist picks (the Deployment commit refuses without them);
- `KirishiacOrbital` (`active`; it also transfers in phase -1).

### Stage 7 (optional) — Movement
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

Rough size: Stages 0-2 are about one session; Stage 3 is about one more, most of it confirming each
class's fields in play. Stage 4 is small. Stages 5-6 together are about one session; Stage 7 is
unsized until its value is confirmed.
