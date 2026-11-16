# Nebula — whole-map terrain (B5W Kitchen Sink 12.5.7)

Status: **planned 2026-11-06; D1–D4 ruled by the user the same day** (section 5). D5 onward are defaults built
unless told otherwise. **Stages 0-6 BUILT the same day, uncommitted** - section 9 is the as-built record.

---

## 1. The rule

User's text (2026-11-06), which is KS 12.5.7 almost verbatim, plus the user's own additions:

> As with dust zones, a nebula fills the entire map. Ships and other units in the nebula suffer various degrading
> effects during a scenario:
> - All thruster ratings are reduced by 1. This includes both the thrusters on ships and the thrust ratings of
>   fighters and shuttles.
> - No ships can use EW of any kind.
> - The fire control bonuses of all weapons and offensive bonuses for shuttles/fighters are cut in half, and fire
>   control penalties for called shots etc are doubled.
> - Weapons firing in defensive mode have their intercept ratings reduced by 1.
> - All weapons suffer -1 on each die of damage they roll. Pulse weapons do -1 point of damage for each pulse that
>   hits. Plasma weapons suffer double the usual heat dissipation per hex of range, in addition to the -1 damage per
>   die. The range of all missiles is halved (this includes both the launch and distance ranges).
> - Jinking requires 2 thrust per level, not 1 thrust.
> - Jammers do not function whilst in a nebula.
> - Electromagnetic weapons do not function unless mounted on advanced race ships.
> - +3 to the jump point scatter roll. Gravitic shields -1. Ancients (factionAge 3 or 4) ignore the effects on
>   thrusters and jinking; everything else applies to them.
> - Mines are not allowed in a nebula: Create Game offers Nebula OR Mines, never both. Nebula is a Yes/No
>   dropdown, default No. Most games will not have one, so every effect must be gated as cheaply as possible.

**Cross-references found in the Kitchen Sink** (`reference_b5w_kitchensink`), checked 2026-11-06:

| KS | Text | Effect on this plan |
|---|---|---|
| 5.12 Jammers | "in some terrains, such as nebulas, jammers won't function at all" | as listed |
| 8.6.4 Comm Disrupter | "Comm disrupters do not function in a nebula." | **Already covered**: FV's `CommDisruptor` and `CommJammer` are `weaponClass "Electromagnetic"` (specialWeapons.php:1026/1125), so the EM line switches them off. |
| Hyach Computer (BFCP) | "not affected by anything that would diminish or eliminate EW, such as ... jammers, stealth, nebulas" | Bonus fire control is added **after** the halving, unhalved (D7). |
| 10.10 Gravitic Shields | "function normally in all terrains except nebulas ... shield ratings are reduced by 1 point" | as listed |
| 9.9 Mines | "minefields will not appear in that terrain" | as listed |
| Jump arrival chart | "Advanced Race -5, arriving in nebula +3" | It is the ARRIVAL chart - FV's reinforcement exit deviation roll. |
| 12.5.14 | "Nebulae: Advanced races ignore the effects on thrusters and jinking, but all other items apply" | as listed |

---

## 2. Shape

- **Rules key `nebula: 1` -> `NebulaRule`** (`AllowMinesRule`'s shape). No unit and no hexes: unlike the Dust Zone
  there is no damage to own, so nothing needs a shooter, and the whole map is the only coverage.
- **Server gate: `TacGamedata::$nebulaPresent`**, assigned (true OR false) in `TacGamedata::__construct` straight
  from the GameRules it builds. Assigning both ways there means a request that builds a second game's gamedata can
  never inherit the first one's nebula. Every per-shot / per-load site reads that one static first, so an ordinary
  game pays one false read per site (arch_defensive_mod_aggregation's rule).
- **Client gate: `gamedata.rules.nebula`.** Rules already reach the client whole (`TacGamedata::stripForJson`
  publishes `$this->rules`), so no new payload key.
- **"Ancient" = `factionAge >= 3`**, read off the UNIT, never the weapon (a weapon's factionAge is always 1 -
  project_dust_meteor_rules).
- **Server-send where the nebula changes a number the server already publishes; mirror only per-shot terms.**
  Thruster output (`outputMod`), fighter thrust (`isModified` freethrust), gravitic shield output, intercept
  ratings, missile ranges and "this weapon cannot fire" are changed ONCE on the server at load and ride the
  payload, so the client needs no rule code for them (roadmap item 10, "server-send over client re-derive"). The
  to-hit terms (fire control, offensive bonus, called-shot penalty) are per shot and depend on the target, so they
  are a MIRROR PAIR at the one hit-chance calculation on each side, shown as one "Nebula" row in the hit-chance
  breakdown. Damage dice, pulse, plasma and the jump scatter are server-only (the client predicts none of them).

### The load hook

`Nebula::applyToShip($ship)`, called from `BaseShip::onConstructed` immediately after
`Enhancements::setSystemEnhancements($this)` and BEFORE the per-system `onConstructed` loop - the slot the Hardened
Shields comment (ShipClasses.php:1829) already reserves for output changes, because `Shield::onConstructed`
snapshots `tohitPenalty`/`damagePenalty` from `getOutput()`. `effectCriticals()` ADDS crit mods to `outputMod`
(ShipSystem.php:1594), so a -1 put there first survives it.

---

## 3. Effect map

| # | Effect | Server | Client | Notes |
|---|---|---|---|---|
| 1 | Thrusters -1 (not Ancients) | load hook: every `Thruster` subclass `outputMod -= 1` | none: `outputMod` is published when non-zero (ShipSystem::stripForJson:351) and `shipManager.systems.getOutput` adds it | `BioThruster` extends ShipSystem, not Thruster - check its owner's age. `InvulnerableThruster` is "unlimited" anyway. |
| 2 | Fighter/shuttle thrust -1 (not Ancients) | `FighterFlight::getEffectiveFreeThrust` | none: stripForJson already publishes the effective value (sets `isModified` when it differs) | `AutomatedMovement` must spend the effective value too. |
| 3 | Jink 2 thrust/level (not Ancients) | `AutomatedMovement` jink row cost 1 -> 2 | `movement.js` `doJink` cost (flight 1 -> 2; ship pivotcost -> x2) and `canJink`'s "thrust left > 0" test -> ">= cost" | CLIENT-ONLY rule today: movement.php neither prices nor limits a jink (movement.js:364). Expert Pilot free levels stay free. |
| 4 | No EW | `InitialOrdersGamePhase::process`: `$ship->EW = array()` before `submitEW`, beside `EW::stripDockingRiderEw`. `EW::submitLateEw` then finds no DEW row and spends nothing. | `ew.getEWLeft` 0, `ew.isEwSuspended` true, `convertUnusedToDEW` writes nothing; EW buttons / Ship Window EW panel hidden | The docking-rider suspension is the precedent. Consequence for to-hit: **D1**. |
| 5 | Fire control halved, FC penalties doubled | `Weapon::calculateHitBase`: `$firecontrol = $this->fireControl[$fcIndex]` (weapon.php:2278) - before the Hyach BFCP is added | `weaponManager.computeShotModifiers` FC read (weaponManager.js:2209); the tooltip line via `translateFCtoD100txt` | MIRROR PAIR. Halving rounds DOWN (**D4**, revised). |
| 6 | ~~Called-shot penalty doubled~~ | ROLLED BACK (D13) | - | - |
| 7 | Fighter offensive bonus halved | weapon.php:2088 `$effectiveOB = $shooter->offensivebonus` | `computeOEW` (weaponManager.js:2090); the tooltips via `nebula.showOffensiveBonus` (SystemInfo, ShipInfo) | halved first, before missions/crits/mine-detection deductions |
| 8 | Intercept -1 | at RESOLUTION: `Weapon::getInterceptionMod` (weapon.php:940) and the meteor-defence rating (specialWeapons.php:3646) subtract 1 from `getInterceptRating()`; plus an eligibility gate so a gun whose rating the nebula takes to 0 is never auto-assigned | manual-intercept options skip a gun whose rating would be 0 | ⚠️ NOT a load-time mutation of `intercept` (built that way on paper first): 15 classes override `getInterceptRating` with their own figure (boost levels, Trek "return 2", Particle Impeder's 3 + boost) and ignore the property, and ammo racks rebuild `interceptArray` from the ammo whenever the magazine changes. `getInterceptRating` is the one read every class honours. |
| 9 | -1 per damage die | new `Dice::$perDiePenalty`, set and restored in `Weapon::getFinalDamage` beside Elite Crew's `$perDieBonus` (its single writer, in the same finally) | none | Each die: Elite's +1 (capped at its face) first, then -1 (floored at 0). `Dice::d` returns a SUM, which is why it must be on the die (dice.php's own note). Excluded: Pulse mode (#10), rams and terrain collisions (D6). |
| 10 | Pulse: -1 per pulse hit | `getFinalDamage`, `damageType === 'Pulse'`: no per-die penalty, -1 on the result | none | getFinalDamage runs once per pulse hit (Officers.php:3107's note). Pulse damage is flat (pulse.php), so per-die would do nothing anyway. |
| 11 | Plasma: double range dissipation | `getDamageMod` (weapon.php:3326): `rangeDamagePenalty` x2 for `weaponClass === 'Plasma'` | none | ⚠️ six plasma classes override `getDamageMod` (Officers.php:2985's note) - each checked at build time. |
| 12 | Missile range halved | the internal arrays stay as they are; the one server read, `Weapon::isInDistanceRange` (weapon.php:3578), halves; `Weapon::stripForJson` PUBLISHES `range`/`rangeArray`/`distanceRange`/`distanceRangeArray` halved | none: `shipSystem.js` copies `rangeArray[mode]` on every mode change, and every client range check reads `weapon.range` | Halving the arrays in place would be undone: `AmmoMissileRackS::recompileFiringModes` rebuilds them from the ammo on every magazine change, mid-resolution included. The server never checks LAUNCH range itself (the client does). Rounds DOWN - the KS's own worked example of a halved missile range (5.12, jammers): "a missile with a normal launch range of 15 hexes could not be launched ... until ... 7 hexes". Which classes: **D3**. |
| 13 | Jammers off | `Jammer::getSpecialAbilityValue` returns 0 | `ew.getJammerValueFromTo` mirror | One server method covers to-hit (weapon.php:2206), SOEW/ELINT (EW.php:249/281), mines and missile-launch denial. Scope: **D2**. |
| 14 | EM weapons off unless Ancient | per MODE (`weaponClassArray`): fire orders rejected in `Firing::validateFireOrders`, never chosen as an interceptor | weaponManager refuses to target with an EM mode; an all-EM weapon shows as non-operational | Only three (custom BSG guided missiles) mix EM and non-EM modes; everything else is all-EM. |
| 15 | Gravitic shields -1 | load hook: every `GraviticShield` (incl. `KirishiacAdvGravShield`) `outputMod -= 1`, ALL factions | none: client `GraviticShield.getDefensiveHitChangeMod` reads `getOutput` | Must be in the load hook BEFORE the system loop (Shield snapshot). |
| 16 | Jump arrival +3 | `JumpEngine::getExitDeviationModifier` (baseSystems.php:9226) | none (no client preview of the roll exists) | Not the escape vortex's failure roll. |
| 17 | No mines | `Manager::createGame` drops `allowMines` when `nebula` is set | Create Game mutual exclusion (section 4) | Fleet Builder (`fleetTest`) keeps its mine exemption; it is not a scenario. |

---

## 4. Create Game, lobby, game screen

- **creategame.php, Terrain Features card**: a "Nebula" row, `<select>` No / Yes, default No (the Dust row's shape).
  `createGame.readTerrain` writes `rules.nebula = 1` or deletes it.
- **Mutual exclusion** (user): Nebula Yes disables and unticks Mines Allowed; Mines ticked disables the Nebula
  select (held at No). Each disabled control gets a one-line caption saying why. Saved presets and a browser-restored
  form must replay into a legal state (presets replay through the page's own handlers - CG plan §12.4).
- **scenarioCard.ruleChips**: a white terrain chip "Nebula", so it shows on Create Game's Confirm step, the lobby and
  game.php's DECLARATIONS briefing (which loads ruleChips for that).
- **Map Preview** (shared `mapPreview.js`: Create Game + lobby): a whole-map nebula tint and a legend entry
  "Nebula (entire map)".
- **In game**: `BallisticIconContainer.generateNebula` - one plane the size of the board, built like
  `buildFullDust` but with its own procedural texture (violet/teal gas banks, two incommensurate layers so the
  repeat never shows), below the hex grid. Static: no animation, so render-loop idle gating is untouched
  (arch_render_loop_idle_gating). Drawn always, like the dust (the dust's "never tie it to the map-background
  toggle" ruling).

---

## 5. Decisions

**User rulings (2026-11-06):**

- **D1 - No EW means no lock-on: RULES AS WRITTEN.** With no OEW nobody holds a lock, so every SHIP shot takes the
  ordinary no-lock penalty (range penalties doubled). Fighters are unaffected (they use OB, never a lock). Needs no
  extra code - it falls out of #4.
- **D2 - The `Jammer` system ONLY.** Stealth systems, Torvalus Shading Fields and fighter stealth keep their own
  jammer-like effect.
- **D3 - Missiles = missile racks only:** `MissileLauncher`, `MultiMissileLauncher`, `MultiBombRack` and the whole
  `AmmoMissileRackS` family (fighter and bomb racks included). Not torpedoes, not mine launchers
  (`BallisticMineLauncher` extends `AmmoMissileRackS` and must be excluded by name).
- **D4 - Halving rounds DOWN**, for both fire control and offensive bonus: FC +3 -> +1, +1 -> 0, OB 5 -> 2. (First
  ruled UP; the user changed it after play-test game 4545 - section 10.)
- **D13 (game 4545) - "penalties are doubled" means a NEGATIVE FIRE CONTROL VALUE only.** A called shot's penalty
  and a Piercing shot are NOT doubled. Both were built (called shots x2 from the start, Piercing -4 after 4545) and
  ROLLED BACK by the user the same day - do not reintroduce either.
- **D14 (game 4545) - whole-board terrain goes with the map background**: 100% dust and the nebula hide with the
  Disable Background button and the "Never show map background" option. Partial dust does NOT (the 2026-11-05
  ruling stands).

**Defaults (built unless told otherwise):**

- **D5** "FC penalties doubled" = a negative fire-control value on the weapon (x2), and nothing else (D13). Other
  to-hit penalties (called shots, Piercing, rolling, pivoting, criticals) are untouched.
- **D6** Ramming and terrain collisions (dust, meteors, asteroids) are not weapon dice: no -1.
- **D7** Hyach bonus fire control is added after the halving, unhalved (KS).
- **D8** Elite Crew (+1/die) and the nebula (-1/die) both apply, Elite first: a d10 rolling 10 reads 9, a 1 reads 1.
- **D9** EM exemption = the firing UNIT's factionAge >= 3, the same test as the thruster/jinking exemption.
- **D10** Gravitic shields -1 for every faction, Ancients included (only thrusters and jinking are exempt).
- **D11** Jinking ships (jinkinglimit > 0) pay double their per-level cost (2 x pivotcost), like fighters' 1 -> 2.
- **D12** Nebula and Dust may be combined.

---

## 6. Build stages

| Stage | Contents | Build |
|---|---|---|
| 0 | `NebulaRule`, GameRules entry, `TacGamedata::$nebulaPresent`, `Nebula` helper class; Create Game row + mines exclusion + presets; `Manager::createGame` mines drop; rule chip; Map Preview + legends; in-game render | autoload (new classes), legacy bundles |
| 1 | Movement: thrusters (#1), fighter thrust (#2), jinking (#3) incl. AutomatedMovement | legacy bundle |
| 2 | EW (#4) and jammers (#13) | legacy bundle, React if the Ship Window EW panel needs it |
| 3 | To-hit mirror pair: FC (#5), called shots (#6), OB (#7) + breakdown row | legacy bundle |
| 4 | Damage: dice (#9), pulse (#10), plasma (#11); gravitic shields (#15) | server only |
| 5 | Intercept (#8), missile ranges (#12) | server only (published values) |
| 6 | EM weapons (#14), jump arrival (#16) | legacy bundle |
| 7 | Verification pass, FAQ / Data Archive entry | - |

---

## 7. Verification

- **The gate**: `fvbuild.ps1 -Check` must stay green - the replay corpus has no nebula game, so ANY diff there means
  a gate leaked into ordinary games.
- **Server**: a scratch PHP script per stage (the Dust Zone recipe: a real local game, the `nebula` rule injected,
  forced Dice, everything inside a rolled-back transaction) asserting each effect ON with the rule and OFF without
  it - including the gate test (force the static false, assert the unmodified value).
- **Client mirror pairs** (#5-7): the vm-sandbox harness over the real `weaponManager.js`, asserting the client's
  hit chance equals the server's for the same shot with and without the rule.
- **User play-test**: create a nebula game locally and hand back the gameID.

---

## 8. Traps found while planning

- `Shield::onConstructed` snapshots `getOutput()` - the shield -1 must land before the system loop (section 2).
- `Dice::$perDieBonus` is a static: set and restored around ONE `getDamage` call in a finally, or every later roll in
  the request inherits it. The new penalty shares that writer and that finally.
- The weapon's own `factionAge` is always 1 - ask the unit.
- `AmmoMissileRackS` rebuilds its range arrays from the ammo after load.
- defensive.php's interceptors recompute `$this->intercept` from `getInterceptRating()`.
- POST-side ships (process()) are rebuilt from JSON and never run the load hook - harmless for thrust (server thrust
  validation is log-only, `$enforceThrustValidation = false`) but nothing on the POST path may trust a
  nebula-modified number.
- weaponManager.js has a second `calculateHitChange` at ~2667 - it is inside a comment block; the live one is ~2482.
  The same goes for the second ammo-rack LoS block (~2893) and the second `offensivebonus` read (~2751).

---

## 9. As built (2026-11-06, Stages 0-6 in one session, uncommitted)

**New files:** `source/server/model/NebulaRule.php`, `source/server/handlers/Nebula.php` (every helper and the
rounding rules), `source/public/client/nebula.js` (`window.nebula`, the client twins - loaded by game.php only).
`source/autoload.php` regenerated: +2 entries, nothing else moved.

**Deviations from sections 3-4, each found while building:**
- **Intercept (#8)** is applied where the rating is SPENT, not by mutating `intercept` - see the row. Server:
  `Weapon::getInterceptionMod`, the meteor-defence rating (specialWeapons.php), and an eligibility gate in
  `Firing::isValidInterceptor` (rating taken to 0, or an EM gun -> never auto-assigned). Client: one read,
  `weaponManager.getInterceptRatingInMode`, feeds manual-intercept eligibility, the interception preview and
  Meteor Defence.
- **Missile ranges (#12)** are halved at the one server read (`isInDistanceRange`) and in the PAYLOAD
  (`Nebula::missileRangesForJson`, which also rewrites the tooltip's Range line, "... (nebula)") - never in the
  arrays. Range halving rounds DOWN, the KS's own 15 -> 7 example; D4's "up" was asked about fire control and OB.
- **Plasma (#11)**: `Weapon::getRangeDamageFactor()` multiplies the rate in the base `getDamageMod` AND in the
  eleven plasma classes that compute their own range loss with their own rate property and discard the parent's
  result (plasma.php's four bolters, customNexus.php's seven) - so a bolter's free 15 hexes stay free.
- **EM weapons (#14)**: no `stowed` reuse (its UI says "Docked"). Server: `Firing::validateFireOrders` rejects
  an EM-mode order (every phase that writes orders runs it); `isValidInterceptor` skips the gun. Client:
  `weaponManager.selectWeapon` refuses with a passive notice (`savedOrders.showNotice`, "Electromagnetic weapons
  do not function in a nebula."). The icon is not greyed.
- **Thrust auto-assign**: `movement.js`'s auto-assigner compared against the RAW `output`, ignoring `outputMod`,
  while the thrust panel uses `getOutput`; it now reads `getOutput` (its own `autoCapacity` per thruster) so it
  cannot overthrust a nebula-reduced thruster. Outside a nebula `outputMod` is 0 on every thruster, so nothing
  changes there.
- **Expert Warrior's precision strike** waives the penalty actually added - doubled in a nebula - on both sides
  (`Officers::precisionStrike`, `officers.precisionStrike`).
- **Rendering**: the dust's three builders became look-generic (`buildLayerMaterial`, `buildLayeredMesh`,
  `buildBoardPlane` taking a LOOK - layers, texture cache, canvas painter); the dust passes `DUST_LOOK`, the nebula
  `NEBULA_LOOK`. Nebula at z -650, under the dust (-600). Look tuned once from a headless render of the real
  `getNebulaCanvas` over map 1.Default.jpg.
- **Ship Window EW panel** left alone: with no rows and `getEWLeft` 0 it simply reads 0 (no React change).

**Verified:**
- `scratchpad/nebula_test.php` (DB-free, throwaway `fv-phpcheck:8.4`, repo mounted read-only): **91/91** -
  rules + mines exclusion, the gate set AND cleared by the next game's constructor, all rounding, the load hook
  on a young (Abbai Bisaria) and an Ancient copy, shield hit-chance mod, fighter thrust (young / Ancient / gate
  off), jink multiplier, Minbari Jammer on/off, Comm Disruptor EM on a young vs advanced hull, missile payload
  (S-rack, a BSG ammo rack per mode), torpedo launcher excluded, every helper class name exists, dice (min 0 /
  max 9; with Elite min 1 / max 9; ordinary min 1), plasma factor, jump +3.
- `scratchpad/nebula_client_test.js` (vm sandbox over the REAL nebula.js, ew.js, movement.js, weaponManager.js):
  **38/38** - the same arithmetic table as the PHP twin, doJink rows (flight 2, ship 2 x pivot), EW suspended /
  left 0 / no DEW, jammer 0 vs 1, intercept and EM (incl. a mixed-mode weapon and a fighter weapon's flight),
  computeFireControl +3 -> +2, every rule off outside a nebula.
- PHP lint of all 18 touched files; `node --check` of all 10 touched client files; legacy bundles rebuilt
  (`FV_NO_MINIFY=1`, the form they were in) and grepped for the new code.

**Second session the same day (Docker up):**
- **Landing speed gate** (user-approved): `HangarOps::canApproachCarrier` now spends `getEffectiveFreeThrust()`
  (nebula -1 AND the Walkers' EDF drain), the figure the client's Dock option was already using - it used to accept
  a speed gap one larger than the client offered. `scratchpad/nebula_landing_test.php` 5/5.
- **BUG FOUND by the real-load test, fixed:** `AmmoMissileRackS::stripForJson` (every ammo rack) and
  `AmmoMissileRackF::stripForJson` call the parent - which halves the ranges - and then REWRITE `rangeArray` /
  `distanceRangeArray` (and F also `range` and `data`) from the raw ones. Both now re-apply
  `Nebula::missileRangesForJson` last (it recomputes from the weapon, so a second pass is harmless). ⚠️ Any future
  missile class that overrides stripForJson and writes a range field must do the same.
- **The harness Dice stand-ins**: nine `tests/replay` files replace `Dice` outright (replayHarness, officersHarness,
  fighterMissionsHarness and six probes); each gained `$perDiePenalty` (and the roll rule where they roll per die),
  or every damage replay died on "Access to undeclared static property" (74 corpus games).
- `scratchpad/nebula_db_test.php` - 24 local games loaded through `DBManager::getTacGamedata` as stored and again
  with `nebula` written into their rules inside a ROLLED-BACK transaction: **2,167 checks** (505 young + 8 ancient
  thrusters, 23 flights incl. payload freethrust, 126 missile racks incl. every published range field per mode,
  4 EM weapons, nothing else's outputMod moved, gate off/on per load, no nebula text in an ordinary payload).
  Its one "failure" was the user's own nebula game 4545, created through Create Game meanwhile (rules stored as
  `{"initiativeCategories":1,"allowReinforcements":1,"nebula":1}` - no allowMines).
- **Replay harness 184 passed / 3 failed, all three pre-existing:** 4440 (Trek Kelly phaser damage) and 4519
  (fighter-weapon damage arrays) fail BYTE-IDENTICALLY on a clean HEAD worktree - the 2026-11-04/05 customTrek.php
  commits; 4509 was played on since it was recorded. `fvbuild -Check`'s validator stage also fails on three
  committed contributor ship findings (MakarDeskoronRefit stray space, two Terran Federation hangar
  over-declarations), unrelated. `fvbuild -Sync` run, so the local site serves this code.

**Still NOT verified:** the Create Game page by hand (presets, the lock-out notes), and a played nebula game.

**Known edges, not built:** `LightningArray`'s combined-fire bonus is added on top of the halved base FC rather
than halved with it; `ThoughtWave` overrides `getFinalDamage` with its own 3d6 formula and takes no per-die
penalty.

---

## 10. Play-test round - game 4545 (2026-11-06)

User feedback, all four built the same day, uncommitted:

1. **Fire control line + rounding.** The ship window's "Fire control" line showed the blueprint's figures. The one
   live builder, `Weapon.prototype.translateFCtoD100txt` (shipSystem.js ~220 - the copy at ~727 is inside a comment
   block), now applies `nebula.fireControl` and appends " (nebula)"; every builder of the line goes through it (mode
   change, `systems.initializeSystem` - whose rebuild-on-load now also runs in a nebula game - and the officers'
   display hook). Rounding changed to DOWN on both twins (`Nebula::fireControl/offensiveBonus`,
   `nebula.fireControl/offensiveBonus`).
2. **Background** (D14): EwButtons.setBackground fires `webglScene.customEvent("MapBackgroundSet")`;
   `BallisticIconContainer.onMapBackgroundSet` re-judges every scene object tagged `userData.fullBoard` (set by
   `buildBoardPlane`, i.e. 100% dust and the nebula only). The state is read off `#background` itself
   (`backgroundImage === 'none'`, what setBackground writes), so a plane built after the switch starts hidden and no
   copy of the state exists anywhere; `show()` and `syncSceneObject` use the same rule (`isSceneObjectVisible`).
   React bundle rebuilt (`npx vite build`).
3. **Piercing** (D13): `Nebula::piercingPenalty` / `nebula.piercingPenalty` (4, on the weapon's CURRENT mode's
   damageType), in `Weapon::calculateHitBase` and as a 'Nebula: Piercing' row in `computeShotModifiers`.
4. **FAQ**: a Nebula entry in the Terrain section of `docs/faq.html` (`faq.php#nebula`).

**Second round (same day):** the user ROLLED BACK item 3 and the called-shot doubling (D13): every called-shot /
Piercing change is reverted - `Officers.php`/`officers.js` have no diff left, and `Nebula::calledShotMod`,
`Nebula::piercingPenalty` and their JS twins are gone. And the fighter **Offensive bonus** lines now show the nebula
figure, marked "(nebula)": `nebula.showOffensiveBonus` halves the base where the server does (before mine detection and
the shunt) in `SystemInfo.js` (a fighter weapon's tooltip) and `ShipInfo.js` (the flight's lines); `ShipNotesPanel` is
lobby-only and untouched. Verified by RENDERING the real components (esbuild bundle + react-dom/server:
`ob_test.cjs` 4/4, `si_test.cjs` 2/2), `nebula_test.php` 92/92, `nebula_client_test.js` 45/45 (asserting the rollback:
a called shot stays -8, -16 vs a base, no Piercing row), harness 184/3 (the same three), bundles rebuilt, synced.

Verified (first round): `nebula_test.php` 93/93 (tables re-pinned to round-down, Piercing), `nebula_client_test.js` 46/46 (the real
translateFCtoD100txt and computeShotModifiers' Piercing row, on and off), `nebula_bg_test.js` 7/7 (the real
container: plane hides with the background, clumps never do, show()/hide() interplay), replay harness 184 / 3 (the
same three pre-existing), legacy + React bundles rebuilt and grepped, `fvbuild -Sync`.
