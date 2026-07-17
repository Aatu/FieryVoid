# Kirishiac Orbitals — Implementation Plan

Implement the full B5W deployed/undeployed orbital rules (dock/deploy, fighter-style targeting, sub-hit chart, overkill containment, docked structure merge, 5-turn regeneration) on top of the existing `KirishiacOrbital` skeleton.

> Test unit: `kirishiacLordship` (8 orbitals, paired `AntigravityBeam`s A–H). Test container: gameID `3730` (`TacGamedata::$safeGameID`) or a fresh Docker game.

---

## 0. Guiding constraints

- **Swap in place, never reorder.** System ids are construction-order positional; every change to `kirishiacLordship.php` must keep the `addXSystem` call order identical (see positional-system-id trap). New properties yes, new/moved systems no — or new phpclass for variants.
- **`autoload.php` entries are added by the user**, never by the assistant. One new class is expected (`OrbitalRepairing` crit; possibly `KirishiacOrbitalLight`— its line already exists commented out at [autoload.php:3549](source/autoload.php#L3549)).
- **No bundle commits** (`game.legacy.bundle.js` / `gamelobby.legacy.bundle.js`).
- **POST-side reconstruction trap:** ships rebuilt from player POSTs have no loaded notes. Any change-detected note write needs the null/default guard; server-only counters (turnsDocked) must only ever be written on the advance side.
- **Client shared-reference trap:** per-instance dynamic flags (`isTargetable`, `canOffLine`, `deployed`) must be sent via `stripForJson`, never mutated on the shared static blueprint client-side.
- **Static JSON regen:** after class-default changes, `php generateStaticShipFile.php` inside the php container.
- Each stage is a commit boundary and independently testable; Stage 0 is safe to ship alone.

---

## 1. Current-state audit (what the "basic form" actually does)

Verified against working tree 2026-07-03:

| # | Finding | Where |
|---|---|---|
| 1 | `KirishiacOrbital` exists with the ShadingField-style notes machinery (`Docked`/`Undocked`/`turnsDocked` notes), `calledShotBonus` profile math, client mirror with `[n/5] Docked` display. | [baseSystems.php:4393](source/server/model/systems/baseSystems.php#L4393), [baseSystems.js:937](source/public/client/model/system/baseSystems.js#L937) |
| 2 | **BUG (blueprint):** `$orbitalB->addOrbitalWeapon($beamE)` — orbital E has no paired weapon; orbital B's pairing is overwritten to beam E. | [kirishiacLordship.php:70](source/server/model/ships/kirishiacLordship.php#L70) |
| 3 | **BUG (fatal):** `criticalPhaseEffects` reads `$this->mirror`, which is never assigned since the `addMirror` → `addOrbitalWeapon` rename — `null->getRemainingHealth()` fatals in the crit phase for any live Lordship. Must use `$pairedWeapon`. | [baseSystems.php:4434](source/server/model/systems/baseSystems.php#L4434) |
| 4 | **WRONG vs rules:** the same function auto-heals a destroyed beam every turn while the orbital lives (Lightning-Gun "mirror" semantics, copied). Orbital rules: a killed beam stays dead until regeneration. Also the destroyed-branch pushes the beam-removal `DamageEntry` onto `$this->damage` (the orbital's array) instead of the beam's. | [baseSystems.php:4429-4453](source/server/model/systems/baseSystems.php#L4429-L4453) |
| 5 | **DEAD FLAGS:** `$hasSystemHitChart`/`$systemHitChart`, `$isAlwaysCalledShot`, `getFireControlIndexOverride()` are declared but consumed **nowhere** — the sub-hit chart never rolls, and the fighter-FC targeting never applies. | grep-verified |
| 6 | **ORBITALS UNHITTABLE:** the ship chart entry `"Kirishiac Orbital"` is resolved by `getSystemsByNameLoc`, which matches **displayName** only (`STRCASECMP`) — but the ctor sets displayName `'Orbital A'` etc. No match → empty list → every orbital roll silently lands on section Structure. | [ShipClasses.php:1580](source/server/model/ships/ShipClasses.php#L1580), [ShipClasses.php:2566-2570](source/server/model/ships/ShipClasses.php#L2566-L2570) |
| 7 | Called-shot profile math **works** both sides: server adds `checkforCalledShotBonus()` ([weapon.php:1393](source/server/model/weapons/weapon.php#L1393)), client mirrors ([weaponManager.js:1512](source/public/client/weaponManager.js#L1512)). `+8 − 7 = +1` vs the `−8` called-shot mod ⇒ effective profile 8. |
| 8 | **Dock toggle unreachable in practice:** client `canActivate` gates on `gamephase == -1` (Deployment), which is skipped on most turns; `turnsDocked` only increments in the phase −1 note write, so the counter is effectively frozen. | [baseSystems.js:954](source/public/client/model/system/baseSystems.js#L954), [baseSystems.php:4483-4508](source/server/model/systems/baseSystems.php#L4483-L4508) |
| 9 | **`KirishiacOrbitalLight` has no server class** (autoload line commented out) — `kirishiacConqueror` fatals if instantiated. The client class already exists. | [kirishiacConqueror.php:48](source/server/model/ships/kirishiac/kirishiacConqueror.php#L48) |
| 10 | Foundations already in place: `Structure::stripForJson` always sends `maxhealth` (Shadow coupling work) so a docked maxhealth merge has free client plumbing; `ShipSystem::isDestroyed` already folds "section Structure destroyed (prev turn) → system destroyed" with a `survivesStructureDestruction` opt-out; `DamageEntry` supports `undestroyed` (Vree structures, SelfRepair). | [baseSystems.php:4341](source/server/model/systems/baseSystems.php#L4341), [ShipSystem.php:1271](source/server/model/systems/ShipSystem.php#L1271) |

---

## 2. Design decisions (lock these before/while coding)

**D1 — Timing: a dock/deploy order given in the Firing Phase of turn N takes effect for turn N+1.**
Firing is simultaneous with the order, so it cannot affect turn N. This matches the FV Hangar Ops convention. Consequences: turn N's shots resolve against the *old* state; the regeneration clock starts at turn N+1; a deploy order during regeneration cancels it at N+1.

**D2 — State model:** keep the existing note keys (`Docked`/`Undocked`/`turnsDocked`) but expose the state internally as `$deployed` (default `true` — "considered deployed at the start of the scenario unless otherwise noted"). The Deployment-phase toggle is *kept* as the "otherwise noted by the player" scenario-start option; the Firing-phase toggle is the in-game action.

**D3 — Docked structure merge (NEEDS USER RULING).** "Its structure is treated as part of the associated structure block for all purposes." Two models:
- **Option A (safe minimum):** no pool change. Docked orbital: untargetable, orbital chart rolls → Structure, dies with the block. The block does NOT gain boxes.
- **Option B (user's dev note, fuller reading of "for all purposes"):** section `Structure->maxhealth += orbital remaining health` while docked (recomputed on every load after notes, Shadow-`shadowStructLost`-style; client gets it via the existing maxhealth send). Sub-decisions that must be locked if B:
  - **Bump amount** = orbital's *remaining* health at load (regeneration completing mid-dock grows the bump automatically).
  - **Damage while docked** stays on the block (orbital can't be hit directly while docked, so no per-orbital entries).
  - **Early undeploy with an over-damaged block:** if block damage exceeds the block's own max (i.e. the orbital's boxes were soaking), the overflow must transfer to the undocking orbital as a damage entry at undock resolution — otherwise removing the bump would flip the block to destroyed retroactively. With A+B both docked on Front, apportion overflow to the orbital(s) actually undocking, capped at their remaining; remainder stays on the block.
  - Recommendation: **ship Stage 4 with Option A, add Option B as a follow-up commit** once the overflow rule is confirmed (Discord/vets if in doubt). Everything else in this plan is identical under either option.

**D4 — Can the beam fire while docked? (NEEDS USER RULING).** Recommendation: **no** — the orbital "lands like a fighter" and a landed fighter can't fire; the rules' "weapons can be deactivated for extra power while undeployed" strongly implies stowed/non-operational. If confirmed: client hides fire orders + server `validateFireOrders` leniency (client-gate only is acceptable per existing trust model).

**D5 — "Targeted as if they were fighters" = FV called shot with three adjustments** (deployed only):
- `isTargetable = true` + `calledShotBonus` profile math (already works, finding #7);
- **fighter fire control**: wire the existing-but-dead `getFireControlIndexOverride()` into both hit-chance calcs (server [weapon.php:1552](source/server/model/weapons/weapon.php#L1552), client `calculateHitChange` FC-index selection);
- a called shot **on the orbital still rolls the Orbital Hits sub-chart** (rules: "If this is done, calculate the hit location as shown on the Orbital Hits chart").
Ship EW/defensive mods keep applying as normal (simplest; orbitals move with the ship).
Paired beams: `isTargetable = false` **always** ("called shots may not be made on orbitals or weapons attached to them" — and while docked the weapon can't be hit at all).

**D6 — Regeneration counting:** dock effective turn N+1 ⇒ docked-turn 1 is N+1; restoration executes in the **critical phase of docked-turn 5** (N+5), erasing *all* damage and criticals on orbital + beam **including damage scored that turn**, and undestroying both. The orbital is then whole and still docked; the player deploys whenever they like. Guard: no restore if `structureSystem->isDestroyed()` ("cannot regenerate if that block has been lost").

**D7 — Destroyed orbitals can still be recovered** ("if the pieces are damaged **or destroyed**, they can be regenerated. To do so, they must be recovered"). The Dock button must therefore remain reachable on a *destroyed* orbital — verify the React system menu renders for destroyed systems and un-gate as needed.

**D8 — Sub-chart is object-mapped, not name-mapped.** Resolution maps chart bands directly to `$this` / `$this->pairedWeapon` / `$this->structureSystem` objects — no `getSystemsByNameLoc` string round-trip, so beam displayNames ("Antigravity Beam A") never need to match anything. Recommend renaming the blueprint chart entries to `"Weapon"` / `"Orbital"` to make intent explicit: `array(6 => "Weapon", 20 => "Orbital")`.

---

## 3. Stages

### Stage 0 — Baseline bug fixes (shippable alone, before any new feature)

1. `kirishiacLordship.php:70` → `$orbitalE->addOrbitalWeapon($beamE);`
2. Rewrite `KirishiacOrbital::criticalPhaseEffects`: use `$this->pairedWeapon`; **delete** the auto-heal branch (finding #4); in the orbital-destroyed branch, push the beam's removal entry onto the **beam's** damage array; only destroy the beam if it isn't already destroyed (idempotency across crit phases).
3. **Hit-chart matching:** add an optional `public $hitChartName = null;` to `ShipSystem`; in `getSystemsByNameLoc` ([ShipClasses.php:1580](source/server/model/ships/ShipClasses.php#L1580)) also accept `STRCASECMP($system->hitChartName, $name)==0`. Set `$hitChartName = 'Kirishiac Orbital'` in the orbital ctor. (Additive, default-null ⇒ zero blast radius; keeps per-orbital displayNames "Orbital A"–"H".) Flash and no-Primary chart rebuilds go through the same function, so they inherit the fix.
4. `KirishiacOrbitalLight`: add the server class (extends `KirishiacOrbital`, smaller maxhealth default 15) so the Conqueror stops being a landmine; user uncomments the autoload line. (Or consciously park the Conqueror — decide now.)
5. Remove or annotate the dead `$isAlwaysCalledShot` flag (superseded by D5's isTargetable approach).

*Test:* Lordship in Docker: crit phase no longer fatals; section fire now actually hits orbitals; killed orbital removes its beam (right beam, once); killed beam stays dead.

### Stage 1 — Deploy/Dock state machine

Server (`KirishiacOrbital`):
- `$deployed = true` default; `onIndividualNotesLoaded` walks notes in order (sort first, ShadingField-style) and sets `$deployed` from the latest `Docked`/`Undocked`, `turnsDocked` from the latest counter note; then applies the state side effects (Stage 2/4 flags) to itself **and** `$this->pairedWeapon`.
- Notes writing:
  - **phase −1 (Deployment)** — keep: initial-state choice.
  - **phase 3 (player POST)** — new: hook orbitals into `BaseShip::generateAdditionalNotes` ([ShipClasses.php:1033](source/server/model/ships/ShipClasses.php#L1033), already called from [FireGamePhase.php:139](source/server/Phase/FireGamePhase.php#L139)) so `doIndividualNotesTransfer` writes the player's toggle. Write **only** the Docked/Undocked state (client-authoritative), **never** `turnsDocked` here (POST-side ships have no loaded notes — trap).
  - **fire-phase advance** (`generateIndividualNotes` runs at phase 4 on the *authoritative* reloaded gamedata, [FireGamePhase.php:21-26](source/server/Phase/FireGamePhase.php#L21-L26)) — new: increment/reset `turnsDocked` here, once per turn, from loaded state. Delete the current phase −1 increment and the phase 1 DB re-read hack ([baseSystems.php:4473-4481](source/server/model/systems/baseSystems.php#L4473-L4481)).
- `stripForJson`: send `deployed`, `turnsDocked` (already sends `active`/`turnsDocked` — rename), plus the Stage 2/4 dynamic flags.

Client (`baseSystems.js` KirishiacOrbital + KirishiacOrbitalLight):
- `canActivate`/`canDeactivate`: allow at `gamephase == -1` **or** `gamephase == 3`; allow on destroyed orbitals (D7 — Dock only).
- `doIndividualNotesTransfer`: also push at phase 3.
- Button labels: extend `SystemActivation` ([SystemActivation.js:166](source/public/client/UI/reactJs/system/SystemActivation.js#L166)) to prefer `system.getActivateLabel?.()` / `getDeactivateLabel?.()` over the generic "Activate"/"Deactivate"; orbital returns **"Deploy"** / **"Dock"**. (Generic, reusable by future systems.)
- No initiative penalties, no maneuver gating — simply don't add any (we never touch the hangar pipeline or `applyHangarOperationsCrit`).

*Test:* toggle at firing phase; state visible next turn; `[n/5]` counts up while docked, resets on deploy; toggling every turn never double-counts; a player who never opens the ship window doesn't clobber state (POST guard).

### Stage 2 — Targeting & hit allocation

- **Sub-chart hook:** `ShipSystem::resolveSubHitChart($gamedata)` default `return $this;`. Consume it at the end of `BaseShip::getHitSystem` ([ShipClasses.php:2447](source/server/model/ships/ShipClasses.php#L2447)) *and* on the `calledid` early-return inside `getHitSystemByTable` ([ShipClasses.php:2470-2474](source/server/model/ships/ShipClasses.php#L2470-L2474)). Audit for any direct external `getHitSystemByTable`/`getHitSystemByDice` callers and cover them.
- `KirishiacOrbital::resolveSubHitChart`:
  - deployed: d20 → 1-6 paired beam, 7-20 `$this` (D8). If the rolled band's system is destroyed, return it anyway (normal FV convention; overkill routing in Stage 3 handles pass-through).
  - docked: return `$this->structureSystem` ("treat any orbital roll as structure").
- **Fighter FC:** consume `getFireControlIndexOverride()` on called shots — server: at [weapon.php:1552](source/server/model/weapons/weapon.php#L1552) use the override index when `$fireOrder->calledid` resolves to a system exposing it; client: same in `calculateHitChange`'s FC selection and the breakdown tooltip.
- **Dynamic targetability:** orbital `isTargetable = $deployed`; beams `isTargetable = false` (class default). Send `isTargetable` in both classes' `stripForJson` (base does NOT send it — verified; and never mutate the client blueprint). Client called-shot gate already honours it ([weaponManager.js:673](source/public/client/weaponManager.js#L673)).

*Test:* section volley distributes per chart between orbital band and structure; orbital hit sub-rolls ~30% onto the beam; docked orbital rolls land on structure; called shot on orbital uses fighter FC + profile 8 and sub-rolls; called shot UI refuses beams and docked orbitals.

### Stage 3 — Overkill routing

Hook `Weapon::getOverkillSystem` ([weapon.php:1888-1924](source/server/model/weapons/weapon.php#L1888)): after the Flash re-roll branch, consult a new virtual `ShipSystem::getOverkillDestination($target)`:
- default `null` → existing behaviour (section Structure → primary);
- `AntigravityBeam` (orbital deployed): return the paired orbital ("overkill on the orbital's weapons passes to the orbital's structure"); docked: default (moot — can't be hit);
- `KirishiacOrbital` (deployed): return a **block sentinel** (`false`) → overkill is *lost*. Flash is unaffected because its branch re-rolls before this point ("flash damage may pass to another system").

The beam needs a back-reference to its orbital: set it inside `addOrbitalWeapon` (one call wires both directions).

*Test:* big raking hit through beam spills into orbital only; hit that kills a deployed orbital dissipates (no ship structure bleed); flash overkill still re-rolls.

### Stage 4 — Docked-state effects

- **Power:** while deployed, beams can't be turned off; while docked they can. The client offline gates ([SystemPowerSettings.js:248](source/public/client/UI/reactJs/system/SystemPowerSettings.js#L248), [SystemInfoButtons.js:742](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L742)) pass anything with `powerReq > 0`, so `canOffLine` alone won't block it — add a `powerLocked` flag (sent via beam `stripForJson`, set from orbital state on notes-load) and honour it in both gates. Note power is chosen in Initial Orders (phase 1), so the state as of turn start correctly governs.
- **Weapon fire while docked:** per D4 ruling — gate client fire-order creation (and Stage 2's targeting already blocks being hit).
- **Structure merge:** per D3 ruling — Option A needs no code beyond Stage 2's redirect; Option B adds the maxhealth bump on load (orbital `onIndividualNotesLoaded` → `structureSystem->maxhealth += remaining`) + undock overflow transfer at the fire-phase advance.
- **Repair isolation:** `repairPriority = 0` on `KirishiacOrbital` **and** `AntigravityBeam` (class defaults) so SelfRepair never touches them (regen is the only healer).
- **Block-death coupling:** already free — `isDestroyed()` folds in the structure block ([ShipSystem.php:1271](source/server/model/systems/ShipSystem.php#L1271); `survivesStructureDestruction` stays `false`). FV's "falls off next turn" convention applies; accept it (consistent with every other system).

*Test:* deployed beam has no Off button; docked beam does; SelfRepair list never offers orbital/beam; blowing the front block kills orbitals A+B (deployed or docked) and their beams.

### Stage 5 — Regeneration

- New crit class `OrbitalRepairing extends Critical` in `cricialClasses.php` (description "REGENERATING", `repairPriority = 0` so SelfRepair can't clear it; `$forInfo = true` — it's a status marker). **User adds the autoload entry.** Client `criticals.js` display string.
- Lifecycle (all inside `KirishiacOrbital`, server-authoritative):
  - **Created** on the fire-phase advance where the dock takes effect: `turn = N+1`, `turnend = N+5`.
  - **Cancelled** (turnend = now, `forceModify = true`, Lightning-Gun pattern [specialWeapons.php:5055-5061](source/server/model/weapons/specialWeapons.php#L5055-L5061)) when: the player deploys before completion, or the structure block is destroyed while docked (prevents post-mortem respawn — the explicit dev-note case).
  - **Completion** in `criticalPhaseEffects` (runs in Criticals pass 2, after firing — [criticals.php:96](source/server/handlers/criticals.php#L96)): when docked && `turnsDocked >= 5` && block alive → write armour-0 negative-damage entries with `undestroyed = true` for orbital and beam sized to their total damage (SelfRepair-entry pattern already in this class, [baseSystems.php:4439](source/server/model/systems/baseSystems.php#L4439)); expire all their criticals; remove the Repairing crit. `turnsDocked` note is the authoritative counter (crit is UI/track only) — keeps replay deterministic and survives the POST trap.
- Edge cases: orbital destroyed *then* docked (D7) — regen undestroys it; docked and re-destroyed mid-regen (only possible via block damage under D3-B or flash edge) — entries dated ≤ N+5 are all erased by the same negative entry + undestroy; ship destroyed — everything moot (systems die with ship).

*Test:* kill beam + cripple orbital → dock → `[5/5]` turn → both restored to full, crits gone, still docked; deploy at `[3/5]` → crit cancelled, damage kept; kill the block at `[4/5]` → crit cancelled, orbital dead for good; replay of all of the above renders identically.

### Stage 6 — UI polish

- `setSystemDataWindow` on orbital + beam: state, effective profile (8), sub-chart odds, regen rules, D4 firing rule.
- Orbital `outputDisplay`: deployed "ORB", docked "[n/5] Docked", regenerating flag; destroyed-but-docked rendering check (D7).
- Fire-menu/tooltip sanity: no called-shot entries for beams/docked orbitals (should fall out of `isTargetable`, verify).

### Stage 7 — Ship & release pass

- `kirishiacLordship.php`: chart entries per D8; ctor args unchanged (id-safe). `kirishiacConqueror` per Stage 0.4 decision.
- Regenerate static JSON (`php generateStaticShipFile.php` in container); `yarn watch:legacy` for client edits (user runs).
- Full Docker test matrix (below), including one **pre-change in-flight game** with a Lordship to confirm no id-shift/notes corruption.

---

## 4. Test matrix (Docker, per stage + final sweep)

| Scenario | Expect |
|---|---|
| Section volley vs Lordship front | hits split per chart 9-10 band → orbital sub-chart (≈30% beam / 70% orbital) |
| Called shot @ deployed orbital | fighter FC + profile 8; sub-chart rolls; beam un-callable |
| Overkill: beam killed | excess → orbital only |
| Overkill: orbital killed | excess lost; flash re-rolls |
| Dock at firing phase | effective next turn; no ini change either side; works while rolling/pivoting |
| Docked orbital | untargetable; orbital chart rolls → structure; beam Off button enabled; beam un-hittable; (D4) beam can't fire |
| Regen full cycle | dock 5 complete turns → orbital+beam fully restored incl. destroyed markers + crits + damage taken during regen |
| Regen aborts | early deploy; block destroyed mid-regen (no respawn) |
| Block destroyed, orbitals deployed | orbitals+beams destroyed (next-turn convention) |
| POST hygiene | submitting orders from a client that never opened the ship window changes nothing |
| Replay | dock/deploy/regen/undestroy render identically on re-watch |
| Regression | non-Kirishiac ship: hit charts, called shots, overkill, flash unchanged |

---

## 5. Engine touch-points (blast-radius list)

| File | Change | Risk |
|---|---|---|
| `ShipClasses.php::getSystemsByNameLoc` | additive `$hitChartName` alias | low (default null) |
| `ShipClasses.php::getHitSystem` + calledid path | `resolveSubHitChart` hook, default identity | low |
| `weapon.php::getOverkillSystem` | `getOverkillDestination` hook, default null | low |
| `weapon.php:1552` + client FC selection | called-shot FC override | low-medium (called shots everywhere — guard on method presence) |
| `ShipClasses.php::generateAdditionalNotes` | add orbital branch (phase 3) | low |
| `SystemActivation.js` | label override hooks | low |
| `SystemPowerSettings.js` / `SystemInfoButtons.js` | `powerLocked` gate | low-medium (power UI is global — flag defaults falsy) |
| `cricialClasses.php` | new crit class | none |
| `baseSystems.php` / `baseSystems.js` | KirishiacOrbital(+Light) rewrite | contained |
| `kirishiacLordship.php` | pairing fix + chart rename | contained (order untouched) |

---

## 6. Status

- [x] D3 / D4 rulings from user (2026-07-03): **D3 = full merge** — block max while docked = base + `getRemainingHealth()` of each docked orbital; damage past the merged max follows the normal overkill workflow; on early undeploy the overflow beyond the block's remaining boxes transfers to the departing orbital. **D4 = beams cannot fire while docked.**
- [x] Stage 0 — pairing fix (E→E), `$this->mirror` crash + auto-heal removed, `hitChartName` alias, `KirishiacOrbitalLight` server class, Conqueror un-parked (addMirror→addOrbitalWeapon, chart rows → "Kirishiac Orbital"). Bonus finds: Mastership/Kingship called `AntigravityBeam` with 5 args (fatal) — `$pairing` now optional with a proper standalone displayName.
- [x] Stage 1 — `active` (ordered) / `activeEffective` (in effect) split in `onIndividualNotesLoaded` (phase-3 notes apply NEXT turn; phase −1 applies immediately); phase-3 write via `generateAdditionalNotes`, guarded on transfer receipt; `turnsDocked` written only on the phase-4 advance (docked-turn ordinal of the coming turn).
- [x] Stage 2 — `resolveSubHitChart` hook consumed at end of `getHitSystem` (covers chart, flash, dice, and called-shot paths — all internal recursions return through the wrapper); deployed d20 (band map from blueprint `systemHitChart`, keys "Weapon"/"Orbital"); docked → structureSystem; fighter-FC override wired server (`weapon.php` `$fcIndex`) + client (`computeFireControl` 5th param `calledid`); dynamic `isTargetable` both sides.
- [x] Stage 3 — `getOverkillDestination` consulted in `getOverkillSystem` after the Flash branch; beam→orbital (or lost if orbital gone), deployed orbital→lost.
- [x] Stage 4 — `stowed` Weapon flag (fire blocked in beam `calculateHitBase`, intercept blocked in `isValidInterceptor`, client `targetShip` + `canSelfInterceptSingle`); `powerLocked` client gate (SystemInfoButtons + SystemPowerSettings `canOffline`); D3 merge bump applied on notes-load, withdrawn + overflow transferred in `finishUndocking`.
- [x] Stage 5 — `OrbitalRepairing` crit (forInfo, repairPriority 0; created on dock-effective when there is damage/crits, turn=N+1 turnend=N+5; cancelled on redeploy or block death); restore in `criticalPhaseEffects` at `turnsDocked >= 5` (negative-damage + undestroy entries for orbital & beam, lingering crits expired).
- [x] Stage 6 — Dock/Deploy labels via `getActivateLabel`/`getDeactivateLabel` (generic SystemActivation hook); `clickableWhenDestroyed` (SystemIcon click gate + interactive render with `$destroyed` styling) so a destroyed orbital can still be docked (D7); `[n/5] Docked` icon text; data windows on orbital + both beams; `OrbitalRepairing` entry in SystemInfo `CRIT_DESCRIPTIONS`.
- [~] Stage 7 — PHP all lints; Docker smoke test: 8/8 pairings + flags correct, sub-chart 1000 rolls ≈ 30/70, standalone beam OK. **Remaining (user):**
  1. autoload.php: add `'orbitalrepairing' => '/server/model/cricialClasses.php',` and uncomment `'kirishiacorbitallight'`.
  2. Then regen static JSON (`docker exec fieryvoid-php-1 php /usr/src/current/generateStaticShipFile.php`) — it constructs the Conqueror, so it fatals until step 1 is done.
  3. `yarn build` / `yarn watch:legacy` for the client edits.
  4. Docker test matrix (§4).

### Post-test refinements (2026-07-03, game 4217 feedback — all 8 done)
1. **Single Dock/Deploy button** — `SystemActivation` honours `system.singleActivationButton` (orbital prototype flag): only the applicable action renders.
2. **Stowed beam icon** — dimmed via `isLoading` (`|| system.stowed`) + "✕" icon text (same mechanism as the Augmenter's spent "✓").
3. **Docked orbital visual** — `suppressActiveBoost` kills the yellow active-as-boosted highlight; `showDockedVisual` + `activeEffective` drives a deep-blue 0.5 fade overlay (`$docked` prop on the System styled component).
4. **Cyan healthbar while docked** — `HealthBar $docked` renders the docked-fighter cyan regardless of crits.
5. **[n/5] only while regenerating** — client `initializationUpdate` gates the counter on `hasCritical(system,"OrbitalRepairing")` (turn-bounded); otherwise plain "Docked".
6. **Beams forced online on deploy** — `power.js copyLastTurnPower`: a `powerLocked` system found offline at turn start is `setOnline(...,true)` (same convention as expired-cooldown recovery).
7. **CV correctness** — `Structure::$orbitalBump` tracks the docked contribution; `calculateCombatValue` subtracts it from BOTH max and remaining (bump boxes are pristine — damage comes off the base pool; a min-clamp variant wrongly let the bump absorb damage and the ≥95% scratch rule hid it). fleetList uses the server-sent combatValue, so no client change. Verified: identically-damaged docked/deployed ships value identically (94.87 == 94.87).
8. **Docked merge actually applies now** — ROOT CAUSE: `DBManager::getTacGamedata` loads notes BEFORE `$gamedata->onConstructed()`, so `$this->structureSystem` was still null in `onIndividualNotesLoaded` and the guarded bump silently skipped. New `getStructureBlock()` helper resolves the block via `getUnit()->getStructureSystem($location)`; used in the bump, criticalPhaseEffects, finishUndocking and resolveSubHitChart. Smoke-tested: 72 → 90 with `orbitalBump` 18.

### Refinement round 2 (2026-07-03, game 4217 — all 4 done)
1. **Order-toggle button** — the single button is keyed on `activeEffective` (label never swaps mid-phase: "Dock" while deployed, "Deploy" while docked); `doActivate`/`doDeactivate` toggle `this.active` on/off, and the existing `$active` wiring lights the button exactly while an order is pending (green Dock / red Deploy).
2. **Pending-order icon highlight** — `hasPendingDockingOrder()` (`active !== activeEffective`) drives a cyan `$orderPending` border + glow on the orbital's SystemIcon.
3. **Tooltip Status line** — `data["Status"]` = Deployed / Docked / Docking / Deploying; server baseline in `setSystemDataWindow`, client `updateDockingStatus()` refreshes it live on button clicks and every `initializationUpdate`.
4. **Stowed beam standardised to the orbital treatment** — `isDockedOrbital` in SystemIcon now includes `system.stowed`, so the beam gets the same blue fade + cyan healthbar; the ✕ text and loading-dim were dropped (icon text shows "Docked").

### Refinement round 3 (2026-07-04)
1. **Deploy veto when the block depends on the orbital's boxes** — previously nothing stopped a deploy whose bump-withdrawal would zero the section Structure (finishUndocking left the block at exactly 0 remaining via the overflow transfer). New `undockingWouldBreachBlock()` (public, server): refuses when `block maxhealth − appliedStructureBump − block damage ≤ 0`. Enforced at the phase-4 advance: the order is vetoed, `active` forced back to docked, and a corrective `Docked` note (turn N, phase 4) is written so it sorts after the player's phase-3 order on every future load. Client mirror `deployWouldBreachStructure()` (merged block maxhealth − own remaining − block damage) hides the Deploy button pre-emptively (`canDeactivate`; still reachable if an order is somehow pending, so it can be cancelled) and both sides surface `Status: "Docked (cannot deploy - structure would collapse)"` + a Special-text rule line.
2. **[n/5] counter no longer faded while docked** — the `::before` fade overlay is positioned, so the static-flow `SystemText` painted underneath it. `SystemIcon.js`: when `$docked`, SystemText gets `position: relative; z-index: 1` (scoped — offline/loading/destroyed dimming elsewhere unchanged).
3. **Conqueror ship-window declutter via left/right sections — DONE (2026-07-04).** L-orientation orbitals A/F + beams → `addLeftSystem`, R-orientation C/D + beams → `addRightSystem` (call ORDER unchanged = ids safe; B/E stay front/aft). Front/aft association kept via new additive `ShipSystem::$structureHomeLocation = null` + `getStructureLocation()` helper, honoured in `onConstructed` (structureSystem assignment → isDestroyed fold), `KirishiacOrbital::getStructureBlock()` (merge/regen/redirect/undock/veto all funnel through it), and SelfRepair's destroyed-block check both sides. `KirishiacOrbital::setStructureHome()` sets orbital + beam; `addOrbitalWeapon` also propagates. Hit-chart rows switched to the TAG mechanism: `"TAG:ORBITALFWD"` / `"TAG:ORBITALAFT"` + `addTag()` on all six orbitals — `getSystemsByTag` searches ship-wide (section-independent) but is ARC-filtered, so the orbital ctor now sets `startArc = endArc = 360` (equal arcs = always in arc; also dodges `addSystem`'s auto section-arc, which would otherwise bearing-filter the CENTER orbitals B/E). HCV `getLocations()` maps all bearings to loc 1/2, so side shots still roll the front/aft tables — no new charts. Client: `getFinalArrangementThree` null-structure guard (side section with systems but no structure block); `deployWouldBreachStructure` + SelfRepairList block-check follow `structureHomeLocation` (sent via orbital + beam stripForJson when set). Behaviour note: TAG lookup prefers LOWEST repairPriority among matches, so with mixed dock states the roll now lands on deployed orbitals (0) over docked (3) — acceptable (docked hits redirect to Structure anyway). Verify in test: called shots (id-based, expected unaffected) and flash (a side-displayed orbital won't catch front-section flash).
4. **SelfRepair may service docked orbitals (rules clarification 2026-07-04).** `repairPriority` is now DYNAMIC, set in `onIndividualNotesLoaded`: docked = 3 (orbital) / 6 (beam), deployed = 0 (class defaults stay 0). Sent via stripForJson both classes (client SelfRepairList gates on `repairPriority === 0`). SelfRepair gather loop hardened: base `repairPriority < 1` now skips BEFORE the player priority-override is applied (guards a stale override set while docked from reviving repair after redeploy) and its destroyed-block check uses `getStructureLocation()`. Note: FV SelfRepair can also restore DESTROYED systems (+10 priority) — a destroyed docked orbital is therefore offerable; docked regeneration remains the bulk healer and `OrbitalRepairing` (priority 0) stays unrepairable/uncancellable by SelfRepair.

### Refinement round 4 (2026-07-04) — flat profiles, home-block arcs, HEAVY Orbital
1. **Flat defence profiles (absolute, all bearings).** The old `calledShotBonus = 8 + profileAdjust` math gave `shipBearingProfile + adjust` — correct only where fwd/side profiles are equal (Conqueror 14/14 → 7 ✓) but 9-from-the-front on the 16/15 Lordship. Replaced with `ShipSystem::getTargetProfileOverride()` (default null) + `KirishiacOrbital::$targetProfile` (8 / Light 7 / Heavy 10): in `Weapon::calculateHitBase` the override **replaces `$defence` entirely** and skips `getCalledShotMod()` + `checkforCalledShotBonus` (fighters don't take called-shot penalties — the profile IS the difficulty). Client mirror in `calculateHitChange` (defence swap) + `computeShotModifiers` (calledShot = 0), keyed on the `targetProfile` JSON field (public prop → in statics too). The ctor's `$profileAdjust` arg is retained but IGNORED (blueprint compatibility).
2. **Orbital hittable arc = home structure block's arc** (was 360/always). Ctor no longer sets 360/360; `BaseShip::addSystem`'s auto-arc now keys on `getStructureLocation()` (additive — only differs for systems with `structureHomeLocation`), so Conqueror side-displayed orbitals get 270..90 (front-homed) / 90..270 (aft-homed) — the HCV multi-entry arc-merge logic produces these correctly. Overlord/Lordship orbitals get their display=home section arcs (330..30 etc.). TAG lookups now genuinely arc-filter (FWD tag @ bearing 180 → empty). Client `canCalledshot` matches `outerSections` against the **home** location (`sysLoc`), covering the Conqueror (home loc 1/2 IS in outerSections with 3 arc entries each) and the Lordship alike.
3. **KirishiacHeavyOrbital** (Overlord test ship, `kirishiacheavyorbital` autoload entry needed — USER): extends KirishiacOrbital. Profile 10 flat, `getFireControlIndexOverride() = 1` (MEDIUM ships), default 42 boxes, `hitChartName = "Heavy Orbital"`, `$canRegenerate = false` (guards added in base `criticalPhaseEffects` + `startRegeneration`; turnsDocked notes still written, [n/5] never shows since no OrbitalRepairing crit is created). `addOrbitalSystem($selfRepair)` attaches its own Self Repair (isTargetable=false, linkedOrbital back-ref, home-loc propagation); sub-chart gains a `'Self Repair'` band (base `resolveSubHitChart`, maps to `$attachedSelfRepair`); a destroyed deployed orbital kills the SR too (crit-phase, like the beam). Ship's main SelfRepair may service the orbital "as usual in either state": heavy overrides the dynamic priorities to orbital 3 / beam 6 ALWAYS (not 0-when-deployed).
4. **Restricted / doubled Self Repair** (`SelfRepair` class): `$repairRestrictedTo` (id array, checked in BOTH gather loops), `$outputDoubled` (getEffectiveOutput += base output), `$linkedOrbital` + `getOverkillDestination` (overkill → orbital, like the beam). The heavy orbital recomputes the list on notes-load: deployed = [orbital, beam, itself]; docked = [beam, home structure block] + doubled. Client `SelfRepairList` filters on `repairRestrictedTo`.
5. **Docked heavy weapon stays operational with reduced arcs**: generic `Weapon::$stowedArcStart/End` + `setStowedArcs()` + `applyStowedArcs()` (swaps live startArc/endArc by `$stowed`; deployed set remembered privately). `HypergravitonBeam` gains optional `$pairing` (6th ctor arg → displayName 'Hypergraviton Beam C'), `linkedOrbital` + overkill routing, a defensive stowed fire-block ONLY when no stowed arcs are set, and a linkedOrbital-gated stripForJson block (stowed, powerLocked, isTargetable, repairPriority, **live startArc/endArc**, stowedArcStart/End). Server fire validation reads live arcs (weapon.php ~1459) → swap covers both sides; client instances get the swapped arcs via `Object.assign(base, systemJson)` in SystemFactory. Client stowed gates relaxed to `stowed && stowedArcStart == null`: weapon select (~336), targetShip (~2314), `showWeaponArc` (ShipIcon ~562, per the plan — stowed heavy weapons DO show their reduced arc), SystemIcon `isDockedOrbital` + `getText` (operational beam renders normally, no blue fade). Intercept while stowed stays blocked both sides (moot — HypergravitonBeam intercept 0).
6. **Blueprints**: Overlord — separate `$heavyOrbitalHitChart` (6 Weapon / 8 Self Repair / 20 Orbital; normal orbitals keep 6/20), `setStowedArcs(0,60)` C / `(300,360)` D, SelfRepair systems now actually mounted (`addLeftSystem($selfRepairC)` etc. — they were constructed but never added), beam D maxhealth normalised 20→30 to match C (**flag for user**), profileAdjust args → 0 (ignored). Lordship/Conqueror untouched (args vestigial). `generateStaticShipFile.php` strips the new defaults (stowed/outputDoubled false; stowedArc*/repairRestrictedTo/linkedOrbital/structureHomeLocation null).
7. **Docked heavy orbital hits still roll the sub-chart (rules correction, 2026-07-04).** "Any hits resolved to hitting the heavy weapon orbital use the heavy weapon orbital hit location chart as normal. Those shots hitting the orbital's structure hit the combined structure instead." New `KirishiacOrbital::$subChartWhileDocked` (false; heavy true): standard orbitals keep the full docked fold (every roll → block; the stowed beam can't be hit at all — base-orbital ruling), but a DOCKED heavy rolls the chart normally — weapon and Self Repair bands strike those systems; only the orbital-structure band diverts to the combined block (`orbitalStructureResult()` helper). Overkill from a docked-struck weapon/SR was ALREADY correct (getOverkillDestination returns null while docked → default flow → section Structure = the combined structure); only the "cannot be hit anyway" comments were updated. Verified (2nd Docker scratch, ALL PASS): deployed heavy 30/10/60 w/sr/orbital, block never; docked heavy 30/10/60 w/sr/BLOCK, self never; docked standard orbital all→block; overkill null docked / →orbital redeployed. Server-only change (hit allocation), no client or statics impact.
8. **Verified** (Docker scratch, 52 checks, ALL PASS): arcs on all 3 ships incl. HCV merge; profiles 8/7/10 + FC indices; pairings/back-refs; 'Heavy Orbital' chart row; sub-chart 30/12/58 over 2000 rolls; deployed + docked + redeployed notes-load state (arc swap 120..300 ⇄ 0..60, SR output 2→4, restriction lists, block merge +42, docked rolls → Structure). Static JSON regenerated & spot-checked. **Remaining (user): (1) autoload `'kirishiacheavyorbital' => '/server/model/systems/baseSystems.php',` (works without it via baseSystems side-load, but add for robustness); (2) `yarn build` / `yarn watch:legacy`; (3) in-game test.**

### Refinement round 5 (2026-07-04) — Heavy Orbital display polish
1. **Attached Self Repair named after its orbital**: `addOrbitalSystem` now sets `displayName = 'Orbital Self Repair ' . getPairing()` ("Orbital Self Repair A"/"B" on the Overlord — the user renamed the heavies C/D → A/B and committed). The ship's own Self Repair keeps plain "Self Repair"; the primary chart's "Self Repair" row still finds only the main SR (location filter + name mismatch, double-safe). Sub-chart unaffected (object-mapped).
2. **Docked visuals split** (`SystemIcon.js`): `isDockedOrbital` (blue fade + cyan healthbar) now also covers the attached SR while docked via a new `dockedWithOrbital` bool sent in the SelfRepair `stripForJson` linkedOrbital block (computed from `linkedOrbital->activeEffective` — not a stored prop, so no statics impact). New `hasDockedHealthbar` = `isDockedOrbital || stowed` feeds ONLY the HealthBar `$docked` prop, so the docked heavy beam (stowed WITH stowed arcs, still operational) renders a normal icon with just the cyan bar.
3. Verified (Docker scratch, ALL PASS): SR names A/B + main unchanged; primary-row lookup; `dockedWithOrbital` false deployed / true docked / absent on the main SR. Statics regenerated (SR displayNames + the user's A/B blueprint renames). Client changes need the usual `yarn build`.

### Refinement round 6 (2026-07-09) — deployed Heavy Orbital repair isolation (REVERSES round-4 item 3)
Bug report (Knightship port heavy orbital, struck while **deployed**): the mother ship's Self Repair cleared the criticals on the orbital, its torpedo and the attached Self Repair the following turn — which is only legal while docked. **Rules ruling (user, 2026-07-09): a DEPLOYED heavy orbital has access to its ON-BOARD Self Repair ONLY; the ship's main Self Repair may not repair criticals/damage/destroyed systems on a deployed orbital.** This overturns round-4 item 3 ("main vessel's SR services the orbital as usual in EITHER state").
- Root cause: `KirishiacHeavyOrbital::onIndividualNotesLoaded` set the orbital/weapon `repairPriority` to 3/6 **unconditionally** (undoing the parent's deployed-priority-0), and the attached SR kept its default priority 10 — so the unrestricted main SR saw all three as repairable while deployed.
- Fix: new additive `ShipSystem::$privateRepairOnly` (default false) = "only a *restricted* Self Repair may service me; a whole-ship SR may not." Both `SelfRepair::criticalPhaseEffects` gather loops (systems + criticals) skip a `privateRepairOnly` candidate when the repairer's `repairRestrictedTo === null`. The heavy orbital sets `privateRepairOnly = !activeEffective` on the orbital, its weapon and its attached SR (deployed = true, docked = false); it also restores the orbital/weapon to repairable priorities (3/6) while deployed so the ON-BOARD SR can still reach them (the priority-0 lever is too blunt — it would block the attached SR too). Priority is NOT the isolation lever here — `privateRepairOnly` is.
- Bug 2 ("attached SR repaired its own 2 pts structure") was reviewed and **kept intentionally** (user ruling): the deployed allow-list still includes the SR's own id.
- Sent to client via `privateRepairOnly` in `KirishiacOrbital::stripForJson`, the `SelfRepair` linkedOrbital stripForJson block, **and all five paired-weapon `linkedOrbital` stripForJson blocks** (HypergravitonBeam, HypergravitonBlaster-paired variant, AntigravityBeam, torpedo, GraviticAugmenter — a weapon's stripForJson is a whitelist that did NOT include the new prop, so a deployed heavy beam still showed on the ship-wide SR list even though the server correctly refused to repair it). `SelfRepairList.js` mirrors the guard (`sys.privateRepairOnly && !system.repairRestrictedTo`).
- **Follow-up verified against live game 4233 (Overlord #2, deployed heavy beams A/B):** JSON now carries `privateRepairOnly=true` on both heavy beams + orbitals + attached SRs; client-list simulation offers NONE of them to the main SR. (The standalone FRONT Hypergraviton Blaster, id 12, `linkedOrbital=null`, correctly still repairable — not an orbital weapon.)
- **Public-prop, so it lands in static JSON — regen after landing (default false everywhere else = inert).**
- Verified: Docker scratch, 13/13 — deployed: main SR candidate list EMPTY (systems + crits), attached SR services orbital+torp+itself; docked: main SR services all three "as usual", attached SR restricted to torp+combined block. PHP lints clean. **Remaining (user): `yarn build`/`yarn watch:legacy` (SelfRepairList.js), regen static JSON, in-game retest of the reported scenario.**

### Refinement round 7 (2026-07-09) — auto-dock destroyed orbitals
Rules ruling (user): docking is resolved in Hangar Operations, AFTER firing — a player would ALWAYS dock an orbital destroyed that turn (only a docked orbital regenerates / is reachable by Self Repair, and it can't fire anyway once dead). So the engine auto-docks a destroyed orbital instead of making the player wait a turn to dock it manually.
- All server-side, in `KirishiacOrbital::generateIndividualNotes` **case 4** (fire-phase advance — runs AFTER `Criticals::setCriticals` in `FireGamePhase::advance`, so `isDestroyed()` already reflects post-firing state):
  1. **Auto-dock:** at the top of case 4, `if (isDestroyed() && !$this->active){ $this->active = true; write corrective 'Docked' note; }` — the note (turn N, phase 4) sorts after any phase-3 player order, so every future load sees Docked as the latest order. Overrides ANY conflicting deploy order that turn (user ruling: "always force dock").
  2. **Destroyed-while-docked regen start:** new `else if ($newDocked && $oldDocked && isDestroyed())` branch calls `startRegeneration` so an already-docked orbital that dies (or was pristine and never had a marker) gets its 5-turn clock. `turnsDocked` is NOT reset — it keeps its existing count (user ruling: "keep existing count"). The clock/marker is UI/track; `turnsDocked >= 5` is the authoritative completion gate.
  3. **No duplicate markers:** new private `hasActiveRegenerationCrit()` guards `startRegeneration` (a live OrbitalRepairing marker → skip) so re-destruction mid-regen never stacks a second marker.
- **Can't undeploy a destroyed orbital:** the case-4 force-dock already overrides a deploy order server-side; client mirror added — `KirishiacOrbital.canDeactivate` returns false (hides the Deploy button) when the orbital is destroyed. The Dock action stays reachable (D7). The existing manual Dock/Deploy buttons are untouched ("keep the manual methods in case something goes wrong").
- Light/Heavy orbitals (`canRegenerate=false`) auto-dock too but never get a marker (correct — they don't regenerate; Heavy still repairs via its own Self Repair).
- Verified: Docker standalone scratch, 17/17 across 7 scenarios (deployed-kill auto-dock+regen, deploy-order override from both states, docked-kill regen with count kept, no-stack, healthy-regression, Light no-marker). PHP lints clean. **Remaining (user): `yarn build`/`yarn watch:legacy` (baseSystems.js `canDeactivate`), in-game test.**

### Open items / notes from implementation
- **Conqueror structure boxes**: RESOLVED by user — blocks restored to base values (72/60/60), the hand-added "+45" removed; the live merge now supplies the orbital boxes.
- The merge bump uses remaining health as of load; a regeneration completing mid-turn grows the pool from the NEXT load (one-turn lag, intentional).
- **Pending-order visibility**: RESOLVED — per-recipient pruning implemented (2026-07-03). Gamedata is built and APCu-cached PER PLAYER (`game_{gameid}_user_{userid}_json`), so stripForJson can mask by viewer: new `TacGamedata::$currentForPlayer` / `$currentForPlayerTeam` statics (set in `setForPlayer` / `onConstructed`, following the `$currentTurn` pattern) + generic `ShipSystem::isRevealedToCurrentViewer()` (owner + teammates; null context = revealed, so server-side processing and static-JSON generation are unaffected — JSON-masking only, never game logic). Consumers: **KirishiacOrbital** sends `active = activeEffective` to enemies (pending dock/deploy orders invisible until they happen — no glow, steady Status); **ShadingField** sends `active = false` to enemies (shading state learned only via detection rules and resolved fire; enemy hit previews show the unshaded mod — intended fog). Replays build fresh per-viewer JSON (no shared cache), so past-turn shading also stays masked for non-teammates there — accepted. Smoke-tested all four views (owner / teammate / enemy / no-context).
