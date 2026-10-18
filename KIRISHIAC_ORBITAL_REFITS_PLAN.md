# Kirishiac Orbital Refits — Advanced Gravitic Shields & ELINT Sensor Modules — Implementation Plan

Status: **BUILT 2026-10-08 — Stages 0–6 done and tested, Stage 7 partly (optional arc display not
built), Stage 8 (Docker play-test, §13) is the user's. Uncommitted.** See §11 and §11.1 (as built).
Drafted 2026-10-06; the user answered
R1–R16 on 2026-10-08 (§2). Four answers changed the draft: R2/R12 (stats), R8/R9 (two EW pools, and
lost points are simply wasted — DEW is never touched), R13 (real power figures; the draft's
power-excess worry was wrong, §3 D16) and R16 (no blocking at Initial Orders). §8 is rewritten to
match. Stage progress is tracked in §11.

**Play-test fixes 2026-10-08 (game 4452, user):** (1) the module and generator arcs are drawn on hover
(§8.7); (2) the after-movement check carries Disruption in whole steps INSIDE the max-flow, so a cut DIST
step no longer strands a point another row needed (§8.4); (3) **R10 refined** - an area function (BDEW,
Detect Stealth) reaches a unit with only the points of the modules whose arc holds it (§8.6). Found on the
way: the client's module coverage ignored a rolled ship, and the server could read a unit dead ahead as
bearing 360 (on a 180..360 arc, not on 0..180) - both fixed in the module's own coverage test.

**Revised 2026-10-08 (second pass, user):** (a) **D15 reversed** - OEW is a normal function and is paid
from the scanner FIRST; module points carry only in-arc OEW the scanner has no room for (scanner-paid OEW
survives the target leaving the arc, so the lock and the range penalty are kept). E6 now ends exactly as the
book does: OEW 10, SOEW stands, 4 lost. (b) **R1 follow-up reversed** - the two generators are independent:
`SYS_AGS2` is a 2nd generator with its OWN rating at 150 per level, bought alongside `SYS_AGS`. (c) The
lobby's YOUR FLEET row shows the hull cost; each enhancement line shows its own cost (§9.6).

Sources: two AoG rules pages supplied by the user (`Downloads/OrbitalGravShields.png`,
`Downloads/OribitalSensorModule.png`), transcribed rule by rule in §1.

Builds on: **`WEAPON_ENHANCEMENTS_PLAN.md`** (the per-system "System Enhancements" track, its
registry, tables, lobby menu and saved-fleet rules — read its §0 D1–D13 first) and
**`KIRISHIAC_ORBITALS_PLAN.md`** (orbital dock/deploy, sub-hit chart, overkill, regeneration).
Related: `SHIP_ENHANCEMENTS_PLAN.md` (harness gates), `WALKERS_OF_SIGMA_PLAN.md` (late EW).

---

## 0. In one paragraph

Three new **System Enhancements** (per-system refits), bought on a Kirishiac **orbital** in the
lobby's "Add Enhancements & Damage" menu: `SYS_AGS` (one Advanced Gravitic Shield generator),
`SYS_AGS2` (two generators) and `SYS_ELINT` (ELINT Sensor Module, Mastership augmenter orbitals
only). Each one **replaces the orbital's mounted system with a system of a different class** —
the first enhancement in FV that changes a system's class rather than its numbers. That needs
three new mechanics: (1) a **mount swap at game load** that keeps every positional id valid and
does not let the client merge the new system onto the old weapon's blueprint; (2) **more than one
system per orbital** (redundant generators); (3) **arc-limited ELINT** with a ring-fenced EW pool,
checked once after movement, where the uncovered module points are lost. Everything is gated so a game with no Kirishiac refit pays one
`empty()` per ship at load and one static boolean per movement phase.

---

## 1. The rules, line by line

### 1.1 Advanced Gravitic Shield Generators

| # | Rule (paraphrased) | FV today | Plan |
|---|---|---|---|
| A1 | Do not need a Shield Generator to function | FV's `GraviticShield` has no generator coupling at all ([baseSystems.php:1086](source/server/model/systems/baseSystems.php#L1086)) | Subclass `GraviticShield`; nothing to remove |
| A2 | Exposed to space; only the smallest amount of armour | — | Class constants: armour 2, structure 3 (**R2**) |
| A3 | Small; usually more than one per orbital for redundancy; **redundant shields do not accumulate** | The defensive bucket keeps only the STRONGEST source per type ([ShipClasses.php:2926](source/server/model/ships/ShipClasses.php#L2926)) | Free. Two generators = two systems; only one rating ever counts |
| A4 | Orbitals only; function whether the orbital is deployed or docked | A docked orbital's weapon is `stowed` (orbitals plan D4) | Generators are never stowed |
| A5 | Lost when the generator or its orbital is destroyed; regenerated with the orbital | Orbital death destroys `pairedWeapon`; regeneration heals orbital + `pairedWeapon` ([baseSystems.php:11216](source/server/model/systems/baseSystems.php#L11216), [:11247](source/server/model/systems/baseSystems.php#L11247)) | Generalise both to every mounted system (D8) |
| A6 | 150 points per generator × shield rating; only vessels not already equipped | No Kirishiac hull carries them today | 150 per rating level (300 for two) |
| A7 | Standard orbital: loses its weapon/system, takes up to two. Light orbital: exactly one. Heavy: never | `KirishiacOrbital` / `KirishiacOrbitalLight` / `KirishiacHeavyOrbital` | Eligibility by class |
| A8 | Primordial: rating ≤ 3. Ancient: rating ≤ 5, but only 25% of the fleet's vessels may have shields at all | Conqueror is `factionAge` 4, the rest 3 | Limit by hull age; 25% as a Fleet Check row (**R6**) |
| A9 | All generators have the same structure and armour, however many are on an orbital | — | Class constants, not divided by count |

### 1.2 ELINT Sensor Module

| # | Rule (paraphrased) | FV today | Plan |
|---|---|---|---|
| E1 | Deployed on a standard orbital; typically a 180° arc | The Mastership's four augmenters are exactly 180° each: A 270–90, J 90–270, D 180–360, G 0–180 ([kirishiacMastership.php:59](source/server/model/ships/kirishiac/kirishiacMastership.php#L59), [:88](source/server/model/ships/kirishiac/kirishiacMastership.php#L88), [:102](source/server/model/ships/kirishiac/kirishiacMastership.php#L102), [:124](source/server/model/ships/kirishiac/kirishiacMastership.php#L124)) | Module inherits the augmenter's arc (**R3**) |
| E2 | The ship acts as an ELINT ship, but only for vessels within normal range **and** the module's arc | `isElint()` = special ability "ELINT" from any live, online system ([ShipClasses.php:2330](source/server/model/ships/ShipClasses.php#L2330)); ranges are checked at fire time ([EW.php:217](source/server/handlers/EW.php#L217) onward) | The module carries the ability; an arc rule is added on top (§8) |
| E3 | The module's rating may be used for any ELINT function, on top of normal EW. Linking costs EW, "normally taken into account on the SCS" | Pool = sum of every system with `outputType` "EW" ([EW.php:42](source/server/handlers/EW.php#L42), [ew.js:4](source/public/client/ew.js#L4)) | **Two pools** (R7, R8, D10): the scanner pays only non-ELINT types; modules pay OEW and the six ELINT functions, in arc only. The scanner loses 2 per module (E8) |
| E4 | Modules add together only where arcs overlap and the target lies in the overlap; a target that leaves the arc loses the extra points | — | Per-module coverage in the validation (§8.4) |
| E5 | Allocate as normal including module points, then check validity **after all movement** | EW is allocated in Initial Orders; `EW::validateEW` is a no-op ([EW.php:5](source/server/handlers/EW.php#L5)) | Client pool at allocation; one server pass at the end of Movement |
| E6 | Worked example: Citadel, 10 normal + 2 modules × 4, 12 OEW on a Shadow + 1 SOEW to each of four friendlies; the Shadow leaves both arcs → OEW 10, SOEW stands, 4 points lost | — | **The Stage 6 exit test** (§8.5). With OEW paid from the scanner first (D15) the result is the book's own: **OEW 10**, SOEW stands, DEW 0, 4 lost (2 cut + 2 never spent) |
| E7 | Module or orbital destroyed → ELINT lost in that arc; regenerated modules work | — | Live-module checks; regeneration generalised (D8) |
| E8 | 700 points: a Gravitic Augmenter becomes a 4-point module; normal EW −2 per module, cumulative; two needed for 360°; at most four per Mastership; any Ancient-timeframe Mastership; "a ship upgrade, not an enhancement" | Only the Mastership carries orbital augmenters (four) | Eligible = augmenter orbital on an Ancient hull; the cap of four is automatic. The Fleet Check "Enhancement(s) present" caution reads ship-level options only ([gamelobby.js:1579](source/public/client/gamelobby.js#L1579)), so a refit never trips it — which matches "not an enhancement" |

---

## 2. Rulings — settled by the user 2026-10-08

| # | Question | **Ruling** | Notes |
|---|---|---|---|
| **R1** | Is the shield rating per orbital or one rating for the whole ship? | **Each generator has its own rating, like a normal Gravitic Shield** — not one ship-wide rating | **Two generators on one orbital are independent too** (second pass 2026-10-08, replacing the first follow-up's shared "x2" rating): `SYS_AGS` and `SYS_AGS2` are two rows, each its own rating at 150 per level. Redundant shields never add (A3) - the higher rating counts, and the other is a backup |
| **R2** | Generator armour and structure (A2, A9) | **Armour 2, structure 3** | The same for every generator, however many an orbital carries (A9) |
| **R3** | Arc of a replacement system | **The replaced mount's arc**, identical docked and deployed | |
| **R4** | Two generators: two systems, or one system with doubled boxes? | **Two systems** (one in place, one appended) | |
| **R5** | Sub-chart with two generators | **1–3 first generator, 4–6 second**, 7–20 orbital | |
| **R6** | 25% cap (A8) | **Advisory Fleet Check row**; counts ships (not flights) carrying any AGS refit; Ancient hulls only; rounds down | |
| **R7** | Which EW types may module points pay for? | **Everything a normal ELINT scanner could pay for, but only toward units in a live module's arc**: OEW, SOEW, SDEW, BDEW, DIST, Detect Stealth, JAM | Follow-up answer 2026-10-08: **OEW is payable** (in arc). Never DEW (R9), and never CCEW or Detect Mines — neither has a subject an arc can test |
| **R8** | Do NORMAL points spent on an ELINT function also need a live module arc? | **Normal points cannot pay for ELINT functions at all** | Two pools: the scanner (N) pays OEW, DEW, CCEW and Detect Mines only; the modules (M) pay OEW and the six ELINT functions, in arc only (§8.1) |
| **R9** | After movement, who pays when module points no longer cover an entry? | **Nobody — the uncovered part is simply wasted.** DEW is never touched, in either direction | In code the wasted part is taken off the row at the end of Movement (D11), so every reader sees the effective value. With D15's scanner-first OEW, E6 ends as the book does (§8.5) |
| **R10** | BDEW and Detect Stealth have no single subject | **Arc-checked per unit** when a module ship produces them | **Refined after play-test (2026-10-08):** per MODULE, not all-or-nothing. A unit gets only the points of the modules whose arc holds it (E4): BDEW 8 from a port and a starboard module = 1 BDEW to a friendly on either side, 2 to the ship itself. Who carries which point is the §8.4 routing; when the function leaves module points unspent the lower-id module pays first |
| **R11** | Does an ELINT module work while its orbital is docked? | **Yes** | |
| **R12** | ELINT module armour, structure, criticals | **Armour 6, structure 12**; Scanner-style output criticals | |
| **R13** | Power of a replacement | **The real figures: ELINT module 4, Advanced Gravitic Shield 0** — and stay power-neutral | The real figures ARE power-neutral, see D16: a Kirishiac reactor's `output` is a surplus, so a smaller `powerReq` frees nothing on its own. The draft's "inherit the old mount's power" is dropped |
| **R14** | Is the −2 EW per module permanent? | **Yes**, even after the module dies | |
| **R15** | How faithful must the lobby preview be? | **Full swap** in the lobby ship window (§9.3) | |
| **R16** | Block out-of-arc ELINT allocations at Initial Orders? | **No — allow, with a passive hint** (follow-up answer 2026-10-08) | E5: allocate as normal, judge validity after movement |

---

## 3. Design decisions (fixed here)

| # | Decision | Rationale |
|---|---|---|
| **D1** | **Purchases attach to the ORBITAL** (`systemid` = orbital id), never to the mounted weapon | The weapon is `isTargetable = false`, so both `systemMayBeEnhanced` ([Enhancements.php:3832](source/server/model/ships/Enhancements.php#L3832)) and the lobby's `isPseudoSystem` ([SystemInfoButtons.js:883](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L883)) already refuse it. And the weapon's identity changes under the swap; the orbital's never does, so the D13 name check stays valid |
| **D2** | **Three enhIDs, each self-priced.** `SYS_AGS` (count = rating, 150/level), `SYS_AGS2` (a 2nd generator, count = its own rating, 150/level - revised 2026-10-08), `SYS_ELINT` (limit 1, 700). One mutual-exclusion group per orbital, ranked (§9.2) | Count-as-rating reuses the existing ticker. No price depends on another row, so the offer tuple, `priceStep` and the sanitiser work unchanged. All IDs ≤ 10 characters (`enhid` is `varchar(10)`) |
| **D3** | The registry gains two optional slots: **`mount`** (structural half, runs early) and **`group`** (mutual exclusion). `apply` keeps the stat half (the scanner's −2) | One literal still defines one refit, and the structural half cannot be forgotten. A missing slot already reads as "nothing to do" (`regCall` returns null) |
| **D4** | **Mount at load, early**: a new `Enhancements::mountSystemEnhancementSystems($ship)` called in `DBManager::getEnhancementsForShips` right after the per-system rows are validated ([DBManager.php:3265](source/server/controller/DBManager.php#L3265)) | Same reason as Extra Tendrils ([Enhancements.php:2016](source/server/model/ships/Enhancements.php#L2016)): damage, criticals, power, fire orders and notes all resolve by `getSystemById` afterwards and silently drop what does not resolve |
| **D5** | **In place keeps the id; extras are APPENDED**, swap rows processed in ascending orbital id. New `BaseShip::replaceEnhancementSystem($id, $system)` beside `addEnhancementSystem` ([ShipClasses.php:2441](source/server/model/ships/ShipClasses.php#L2441)) | Nothing already on the hull moves; the same rows rebuild the same ids on every load. A named method keeps the second post-constructor mutation greppable |
| **D6** | **Client: never merge a live system onto a blueprint system of a different `name`.** One string compare in `SystemFactory.createSystemsFromJson` ([systemFactory.js:16](source/public/client/model/systemFactory.js#L16)); replacements send their full blueprint fields (`addedByEnhancement = true` + `addBlueprintFieldsForJson`, [ShipSystem.php:362](source/server/model/systems/ShipSystem.php#L362)) | Today the factory does `Object.assign(copy of blueprint[id], payload)`, so a shield at a beam's id would inherit `weapon: true`, `fireControl`, `range`, `loadingtime` and the beam's `data`. Only fighters ever change `name` at runtime ([fighter.php:75](source/server/model/systems/fighter.php#L75)), and flights use a different factory path |
| **D7** | **POST-side ships are NOT swapped.** Constraint: a replacement takes no per-system player input — no power toggle, no notes, no fire orders | `Manager::getShipsFromJSON` resolves POSTed system data by id and drops what does not resolve ([Manager.php:2631](source/server/controller/Manager.php#L2631)); `validateEW` is a no-op; a fire order whose weaponid maps to a non-weapon is already rejected ([firing.php:8](source/server/handlers/firing.php#L8)). So nothing POSTed is lost. **One exception, by R13:** the ELINT module draws 4 power and, like every orbital mount, may be switched off while docked. Power rows are keyed by system id and the POST-side ship holds the augmenter at that id, whose class default is `canOffLine = true`, so the row is accepted and the reloaded module reads it — verify in Stage 2. Any further player input on a replacement makes POST-side mounting mandatory — say so in the class docblock |
| **D8** | **The orbital's single `pairedWeapon` generalises to a mounted list** (`getMountedSystems()`); `pairedWeapon` stays as its first entry | Sub-chart, death coupling, regeneration and docked state all read it; one list keeps them in step |
| **D9** | **The new classes declare every property the orbital writes** (`linkedOrbital`, `stowed`), and the orbital writes `stowed` only on a `Weapon`. `canOffLine` (declared on `ShipSystem`) is written on every mounted system that draws power — the ELINT module included (D16) | `stowed` is declared on `Weapon` only ([weapon.php:253](source/server/model/weapons/weapon.php#L253)) and `linkedOrbital` per weapon class. PHP 8.2 ([docker/php/Dockerfile](docker/php/Dockerfile)) deprecates dynamic properties, and Manager's global handler throws on every error ([Manager.php:5](source/server/controller/Manager.php#L5)) — **one undeclared write kills the game load** |
| **D10** | **The ELINT module is NOT a `Scanner`/`ElintScanner` subclass**; `outputType = 'ELINT'` | A Scanner subclass would join the normal pool, the stealth-detection scanner read ([baseSystems.php:266](source/server/model/systems/baseSystems.php#L266)) and the jump-exit scatter's sensor rating |
| **D11** | **Validation runs once, at the end of Movement, and rewrites `tac_ew`.** Rows that reach 0 are deleted | Every downstream reader — fire-time SOEW/SDEW/DIST, HK jamming, the client, replays — then reads validated values with no change. A zero OEW row must go: the client counts OEW rows as targets whatever their amount ([ew.js:253](source/public/client/ew.js#L253)) |
| **D12** | **Use-time arc gates only where an entry has no single subject**: BDEW and Detect Stealth, plus the late-EW window | Subject-based functions are settled by D11 |
| **D13** | **Static gate `TacGamedata::$elintModulesPresent`**, derived in `markUnavailableSetMarkers` ([TacGamedata.php:481](source/server/model/TacGamedata.php#L481)) from each ship's module list | The `$chameleonPresent` pattern ([[arch_defensive_mod_aggregation]]) |
| **D14** | **Per-ship and per-orbital lists are non-public** | The static blueprint serialises PUBLIC properties; an array of system objects would ride every Kirishiac blueprint |
| **D15** | **Funding order at allocation (client) - REVISED 2026-10-08 (user):** ELINT functions from M; **OEW from N first**; only in-arc OEW (subject inside a live module's arc *at the current position and facing*) that N has no room for spills onto what M has left; out-of-arc OEW from N only | OEW, CCEW and Detect Mines are normal functions, so the main sensors pay them first: OEW paid by a module is lost if the target leaves the arc - and with it the lock, doubling the range penalty - while scanner-paid OEW is not. (The first draft funded in-arc OEW from M first to keep the most DEW; the user overruled it.) The split is computed from the turn's totals, so click order never matters. No blocking (R16) — this only decides which pool pays. The server never needs the split: it derives the scanner's share from the committed DEW row (§8.4) |
| **D16** | **Power: the real figures (R13) are already power-neutral** | All six Kirishiac hulls use a standard `Reactor` (never the Mag-Grav `fixedPower` kind). Its `output` is a SURPLUS: client `getReactorPower` ([power.js:555](source/public/client/power.js#L555)) adds a system's `powerReq` back only when that system is switched OFF. So a smaller `powerReq` changes nothing until the system is switched off — and an orbital mount may be switched off only while docked. A docked ELINT module switched off gives +4 where the augmenter gave +7; the AGS has nothing to switch off. The swap can only lower available power. The draft's "inherit the old mount's powerReq" rested on a wrong premise and is dropped |

---

## 4. What exists — the code this touches

| Concern | Where |
|---|---|
| Per-system refit registry, eligibility, prices, offers | `$systemEnhancementRegistry` [Enhancements.php:3645](source/server/model/ships/Enhancements.php#L3645); `systemEnhancementAllowsAge` [:3809](source/server/model/ships/Enhancements.php#L3809) (the `ages` slot); `systemMayBeEnhanced` [:3832](source/server/model/ships/Enhancements.php#L3832); `setSystemEnhancementOptions` [:3918](source/server/model/ships/Enhancements.php#L3918) |
| Refit apply / JSON / buy-time sanitiser | `setSystemEnhancements` [:4300](source/server/model/ships/Enhancements.php#L4300); `addSystemEnhancementsOwnForJSON` [:4363](source/server/model/ships/Enhancements.php#L4363); `sanitiseSystemEnhancements` [:4402](source/server/model/ships/Enhancements.php#L4402) |
| Systems an enhancement mounts (precedent) | `addEnhancementSystems` [:2035](source/server/model/ships/Enhancements.php#L2035), `addedByEnhancement` [ShipSystem.php:195](source/server/model/systems/ShipSystem.php#L195) |
| Game load order | `getTacShips` [DBManager.php:2997](source/server/controller/DBManager.php#L2997): enhancements → initiative → moves → EW → power → criticals → damage → fire orders → notes; then `onConstructed` |
| Kirishiac ship-level set (only `IMPR_SR`) | `nonstandardEnhancementSet` [Enhancements.php:95](source/server/model/ships/Enhancements.php#L95) |
| Orbitals | `KirishiacOrbital` [baseSystems.php:11094](source/server/model/systems/baseSystems.php#L11094) — `addOrbitalWeapon` :11118, `resolveSubHitChart` :11175, `criticalPhaseEffects` :11216, `performRegeneration` :11247, `startRegeneration` :11288, `onIndividualNotesLoaded` :11421, `stripForJson` :11514; Light :11540 (`canRegenerate = false`); Heavy :11586 |
| Orbital weapon hooks to copy | `AntigravityBeam` [gravitic.php:2171](source/server/model/weapons/gravitic.php#L2171) — `getOverkillDestination`, the linkedOrbital `stripForJson` block |
| Replaced systems | `GraviticAugmenter` [supportWeapons.php:1320](source/server/model/weapons/supportWeapons.php#L1320) (defaults: structure 12, power 7) |
| Shields | `Shield` [baseSystems.php:956](source/server/model/systems/baseSystems.php#L956), `GraviticShield` :1086; client [defensive.js:51](source/public/client/model/system/defensive.js#L51), :99; aggregation `getHitChanceMod` / `checkIsValidAffectingSystem` [ShipClasses.php:2926](source/server/model/ships/ShipClasses.php#L2926), :2985; client `getHitChangeMod` [ship.js:162](source/public/client/model/ship.js#L162) |
| EW, server | `EW.php`: `getScannerOutput` :42, `getBlanketDEW` :217, `getSupportedOEW` :242, `getSupportedDEW` :314, `getDistruptionEW` :348, `submitLateEw` :614; [HkJamming.php:61](source/server/handlers/HkJamming.php#L61); stealth detection ELINT multiplier [baseSystems.php:275](source/server/model/systems/baseSystems.php#L275); `adjustEwAmount` [DBManager.php:2120](source/server/controller/DBManager.php#L2120) |
| EW, client | `ew.js`: `getEwLeftFor` :124, `convertUnusedToDEW` :275, `getEWLeft` :354, `AssignOEW` :542, `assignEW` :617, `getSupportedBDEW` :911; ELINT buttons [shipTooltipInitialOrdersMenu.js:43](source/public/client/UI/shipTooltipInitialOrdersMenu.js#L43); DEW row [ShipWindowEw.js:411](source/public/client/UI/reactJs/shipWindow/ShipWindowEw.js#L411) |
| Movement → Pre-Firing advance | [MovementGamePhase.php:111](source/server/Phase/MovementGamePhase.php#L111) (`setPhase(5)`) |
| Lobby refits | [systemEnhancements.js](source/public/client/systemEnhancements.js) (`LABELS` :46, `revert` :324, `apply` :352); menu gate [SystemInfoButtons.js:867](source/public/client/UI/reactJs/system/SystemInfoButtons.js#L867); Fleet Check `checkChoices` [gamelobby.js:1005](source/public/client/gamelobby.js#L1005) |

---

## 5. Data model

### 5.1 Registry entries

```php
'SYS_AGS'   => array('label' => 'Adv. Gravitic Shield', 'eligible' => 'sysEnhEligibleAGS', 'price' => 'sysEnhPriceAGS',
                     'limit' => 'sysEnhLimitAGS', 'mount' => 'sysEnhMountAGS',
                     'group' => 'orbitalMount', 'ages' => array(3, 4), 'serialise' => array()),
'SYS_AGS2'  => array('label' => '2nd Adv. Gravitic Shield', /* standard orbitals only; its OWN rating at 150 per level (R1 revised); with SYS_AGS it is appended */
                     'price' => 'sysEnhPriceAGS', 'mount' => 'sysEnhMountAGS', 'group' => 'orbitalMount', 'groupRank' => 1, 'ages' => array(3, 4), 'serialise' => array()),
'SYS_ELINT' => array('label' => 'ELINT Sensor Module', 'eligible' => 'sysEnhEligibleELINT', 'price' => 'sysEnhPriceELINT',
                     'limit' => 'sysEnhLimitOne', 'mount' => 'sysEnhMountELINT', 'apply' => 'sysEnhApplyELINT',
                     'group' => 'orbitalMount', 'ages' => array(3), 'serialise' => array()),
```

* **Eligible:** `SYS_AGS` — a `KirishiacOrbital`, not Heavy, with a mounted system. `SYS_AGS2` — the
  same, not Light either. `SYS_ELINT` — a standard orbital whose mounted system is a `GraviticAugmenter`.
* **Limit:** AGS `factionAge >= 4 ? 3 : 5`; ELINT 1.
* **`serialise` stays empty.** Nothing on the ORBITAL changes, and the scanner's reduced `output` is
  always sent ([ShipSystem.php:319](source/server/model/systems/ShipSystem.php#L319)).
* ⚠️ `ages` array(3, 4) makes these the **first refits open to Primordial hulls**, so
  `hullAgeHasAnySystemEnhancement` stops short-circuiting for age-4 hulls. That costs a per-system loop
  at static generation only, and only orbitals pass `eligible`.

### 5.2 New system classes

| | `KirishiacAdvGravShield extends GraviticShield` | `KirishiacElintModule extends ShipSystem implements SpecialAbility` |
|---|---|---|
| Output | rating (1–5), set by the factory | 4 ("4-point module"), crit-reducible |
| Arc | the replaced mount's (R3) | the replaced augmenter's 180° (R3) |
| Armour / structure | 2 / 3 (R2) | 6 / 12 (R12) |
| Power | 0 (R13); nothing to switch off | 4 (R13); switchable while docked only, like every orbital mount (D9, D16) |
| Abilities | `getDefensiveType()` "Shield" (inherited) | `specialAbilities = ['ELINT']`, `outputType = 'ELINT'` (D10) |
| Docked | functions; never stowed | functions (R11) |
| Overkill | → orbital while deployed; lost if the orbital is gone (copy `AntigravityBeam::getOverkillDestination`) | same |
| JSON | parent + the linkedOrbital block (`isTargetable`, `repairPriority`, `privateRepairOnly`, `structureHomeLocation`) + `addBlueprintFieldsForJson` | same |
| Declares | `linkedOrbital`, `stowed` (D9) | same |
| Factory | `forOrbital($orbital, $replaced, $rating, $ordinal)` | `forOrbital($orbital, $replaced)` |
| Client class | `KirishiacAdvGravShield` extending client `GraviticShield` (its Abbai branch is faction-gated) | `KirishiacElintModule` extending `ShipSystem` |

Display names follow the orbital letter: "Adv. Gravitic Shield A" / "A2", "ELINT Sensor Module A".
Icons: reuse `shield.png` / `elintArray.png` in v1; new art is a drop-in later. The client factory
picks the JS class from `name` with the first letter upper-cased, so `name` must equal the JS class
name.

### 5.3 Ship and orbital fields

* `BaseShip`: `protected $elintModules = array()` + `getElintModules()` (D14);
  `public $systemEnhancementSystemsMounted = false` (once-per-object guard, like `enhancementSystemsAdded`).
* `KirishiacOrbital`: `protected $mountedSystems = array()`, `getMountedSystems()`,
  `replaceOrbitalWeapon()`, `addMountedSystem()`.
* Static JSON (lobby only, R15): `public $systemEnhancementSwapPreviews = array()` — per eligible
  orbital, the replacement system(s) serialised exactly as blueprint systems are. Emitted only on the
  `$offerSystemEnhancements` path; add it to ShipCompactor's drop-when-empty list beside
  `systemEnhancementOffers` ([ShipCompactor.php:351](source/server/lib/ShipCompactor.php#L351)).

---

## 6. The swap

### 6.1 Server, step by step (`mountSystemEnhancementSystems`)

1. `if (empty($ship->systemEnhancements)) return;` — every ship without a refit stops here.
2. Collect the rows whose registry entry has a `mount` slot, keyed by orbital id; `ksort` (D5).
3. Per row: resolve the orbital and take `$old = $orbital->getOrbitalWeapon()`. Skip if null, or
   (ELINT) if it is not an augmenter.
4. Build the replacement with the class factory. It copies `id`, `location`,
   `structureHomeLocation` and `startArc`/`endArc` from `$old`, keeps its OWN `powerReq` (R13, D16),
   and sets `addedByEnhancement = true`.
5. `$ship->replaceEnhancementSystem($old->id, $new)`: same id, `setUnit`, `$this->systems[$id] = $new`.
   ⚠️ Not `addSystem`: that stamps a section arc on a 0/0 arc and has the LCV and structure side
   effects ([ShipClasses.php:2336](source/server/model/ships/ShipClasses.php#L2336)).
6. `$orbital->replaceOrbitalWeapon($new)` wires `linkedOrbital`, `isTargetable = false`,
   `repairPriority = 0`.
7. `SYS_AGS2`: a second generator, `addEnhancementSystem($gen2, $orbital->location)` (appended), then
   `$orbital->addMountedSystem($gen2)`.
8. `SYS_ELINT`: push the module onto the ship's module list.

`apply` (in `onConstructed`, before the per-system loop) then does the stat half: `sysEnhApplyELINT`
takes 2 off the strongest Scanner per module (reuse `strongestSystem`,
[Enhancements.php:2452](source/server/model/ships/Enhancements.php#L2452)).

### 6.2 Why the ids stay safe

* No hull constructor changes, so no existing game's ids move.
* Purchase rows name the **orbital** (D1), whose id and name never change.
* The in-place replacement exists from turn 0, so every damage, critical and power row written for
  that id belongs to it.
* Pre-battle damage cannot reach a mounted system (pseudo-system, D1); destroying the ORBITAL
  pre-battle drops the refit through the existing D11 sweep.
* Appended ids are rebuilt identically each load (D5). Ship-level appends (Extra Tendrils) run
  first, at [DBManager.php:3241](source/server/controller/DBManager.php#L3241), and never occur on
  Kirishiac hulls.
* ⚠️ The general hazard remains: a contributor inserting a system mid-constructor into a Kirishiac
  hull shifts the orbital ids; D13 then drops the row, and a running game reverts that orbital to
  its weapon. That is the standing "never restructure a hull in use" rule
  ([[arch_positional_system_id_trap]]).

### 6.3 Client

* `SystemFactory.createSystemsFromJson`: if the blueprint system at this id has a different `name`,
  use the payload alone (D6). The appended generator already has no blueprint entry, which works
  today for tendrils.
* Every other client blueprint read is keyed by phpclass, not system id (`fleetList.js`,
  `systems.js` `findFighterBlueprint`) — checked by grep.

### 6.4 Paths that need no change, and why

| Path | Why it is safe |
|---|---|
| Static blueprints, game.php `staticShips`, BlueprintCache | Built from pristine hulls; no purchase rows |
| POST-side ships | D7 |
| Replays | Same DB load per turn, same mount |
| Saved fleets | Rows are on the orbital and re-validated by the existing §4.7.1 machinery |
| Fire validation and load | A non-weapon weaponid is rejected at submit and skipped at load ([firing.php:8](source/server/handlers/firing.php#L8)) |

---

## 7. Advanced Gravitic Shield in play

* **Hit chance and damage:** inherited `Shield` maths through the existing bucket; the arc test is
  `checkIsValidAffectingSystem` ([ShipClasses.php:2985](source/server/model/ships/ShipClasses.php#L2985)).
  Two generators, or two orbitals covering one bearing, count once (A3). Fighters at range 0 fly
  under it, as with every Shield. The Advanced Sensors clause only zeroes defences of
  `factionAge < 3` targets, so it never touches these.
* **Docked:** keeps working (A4). A docked standard orbital folds every hit into its block, so a
  generator cannot be hit while docked — the same as today's stowed beam.
* **Destroyed:** a generator destroyed this turn still works until the end of the turn
  (`isDestroyed($turn-1)`); the orbital's death destroys every mounted system in the critical phase (D8).
* **Regenerated** with a standard orbital (Light orbitals cannot regenerate).
* **Sub-chart** (R5): the "Weapon" band maps to the mounted list, split 1–3 / 4–6 when there are two.
* **Client mirror:** the client shield class returns `output` as the defence mod, and the client
  bucket mirrors the arc test. Verify in Stage 4 against the server's real `calculateHitBase`.

---

## 8. ELINT Sensor Module — arc-limited ELINT (the new mechanic)

### 8.1 Pools (R7, R8)

| Pool | Size | Pays for | Never pays for |
|---|---|---|---|
| **N** — scanner | `getScannerOutput`, already −2 per module (E8, R14) | OEW, DEW, CCEW, Detect Mines | any ELINT function (R8) |
| **M** — modules | the sum of LIVE modules' `output` (4 each, crit-reducible; a docked module switched off counts 0) | OEW, SOEW, SDEW, BDEW, DIST, Detect Stealth, JAM — **only toward units inside that module's arc** (R7) | DEW, CCEW, Detect Mines |

Three type classes, one table in each language (`ElintModules::TYPE_*` / `ew.ELINT_MODULE_TYPES`):
**scanner-only** {DEW, CCEW, Detect Mines}, **module-only** {SOEW, SDEW, BDEW, DIST, Detect Stealth,
JAM}, **either** {OEW}. Unspent M is lost; it never becomes DEW. Ships without modules are untouched —
every branch below sits behind `ship.hasElintModules` / `getElintModules()`.

### 8.2 Allocation (Initial Orders, client) — no blocking (R16)

Per call, with the module ship's current-turn rows:

```
E  = module-only points used          S  = scanner-only points used (not DEW)
Oc = OEW on subjects inside a live module arc at the current position + facing
Ou = every other OEW
inN     = min(Oc, max(0, N - S - Ou))     in-arc OEW the scanner carries - all that fits (D15)
spill   = Oc - inN                        in-arc OEW the scanner has no room for -> modules
N_used  = S + Ou + inN
freeM   = max(0, M - E - spill)
getEWLeft(ship)                 = N - N_used           (= the DEW that commit writes; contract unchanged)
getEwLeftFor(ship, module-only) = freeM
getEwLeftFor(ship, OEW, target) = (N - N_used) + (target covered ? freeM : 0)
getEwLeftFor(ship, scanner-only)= (N - N_used) + min(inN, freeM)   a new scanner point may push in-arc OEW onto free module points
(outside Initial Orders: module-only 0, everything else N - N_used - nothing moves onto the modules late)
```

* `getEwLeftFor` gains an optional `target` argument; the four OEW call sites pass it. Without one, OEW
  is answered as uncovered (the safe, smaller figure).
* `convertUnusedToDEW` is unchanged — it already writes `getEWLeft`.
* An allocation that does not fit is refused exactly as today. An ELINT function aimed at a unit
  outside every live arc IS allowed (R16), with a passive fading hint
  ([[feedback_passive_notices]]): "outside every ELINT module arc — these points are lost unless it
  is in arc after movement".
* **Added after the play-test (2026-10-08, user):** the same passive notice when a click puts MORE on a
  unit than the modules covering it can pay - the pools are module-wide and cannot see that (DIST 6 on a
  unit only one 4-point module covers: "Only 3 of the 6 DIST points on G'Quan … can be paid by the ELINT
  modules covering it - the other 3 are lost after Movement unless more modules cover it then"). It asks
  the §8.4 routing (client mirror) before and after the click, at the current positions, and speaks only
  when the shortfall GREW - so a known shortfall is not repeated on every later click, and a click that
  starves ANOTHER row is named (a CCEW point pushing in-arc OEW onto the module an SDEW was using).
  Initial Orders only (`ew.getElintShortfalls` / `ew.noteElintShortfall`, called from `AssignOEW` and
  `assignEW`). Still never blocks (R16).
* ⚠️ **After validation the committed rows are the truth.** Outside Initial Orders a module ship's
  `getDefensiveEW` reads the committed DEW row (R9 leaves it untouched, so it is also what the
  ship really has), and the OEW/ELINT rows have already been cut to their effective values.
* Ship window EW panel: an "ELINT" row — `unused / M` during Initial Orders, "lost n" after
  validation (from the module's note).
* Late EW (EW Detector window, `EW::submitLateEw`): late points are debited from DEW, i.e. they are
  scanner points, so a module ship may add OEW late but **no ELINT function** (R8). Client: the
  module-only `getEwLeftFor` answers 0 outside phase 1; server: `submitLateEw` skips those deltas.

### 8.3 What counts as covered

A module covers a unit when the module is not destroyed, not switched off, and
`Mathlib::isInArc($host->getBearingOnUnit($unit), startArc, endArc)` holds — the weapon-arc helpers,
so same-hex cases behave as they do for weapons (client twin: `weaponManager.isOnWeaponArc`). BDEW and
Detect Stealth have no subject: any live module may pay for them, and their effect is gated per unit
(R10, §8.6). The subject of SOEW and SDEW is the friendly receiving it; of DIST the enemy; of JAM the
remote-controlled flight.

### 8.4 Validation after movement (server, once per turn)

Hook, just before `setPhase(5)` in [MovementGamePhase.php:111](source/server/Phase/MovementGamePhase.php#L111):

```php
if (TacGamedata::$elintModulesPresent) ElintModules::validateAfterMovement($latestgameData, $dbManager);
```

New handler `source/server/handlers/ElintModules.php`. Per ship with modules, not destroyed, deployed:

```
N      = EW::getScannerOutput(ship)                  // already -2 per module
S      = sum of CCEW + Detect Mines rows             // scanner-only, never cut
D      = the committed DEW row                       // UNTOUCHABLE (R9)
Navail = max(0, N - S - D)                           // what the scanner paid toward OEW at commit
live   = modules not destroyed and not off; capacity = getOutput()
rows   = OEW rows + module-only rows, in id order (= allocation order)
flow   = max-flow( row -> { scanner (OEW rows only, cap Navail),
                            each live module covering the row's subject (cap = output) } )
         BDEW / Detect Stealth connect to every live module; augmenting paths in row-id order,
         scanner before modules, modules in system-id order - deterministic
effective(row) = flow into it; DIST is carried in WHOLE STEPS of 3 (4 for ConstrainedEW): a step
         that cannot be carried in full is rolled back, so its stray points stay free for later rows
         (game 4452: DIST 6 + SOEW 1 on one 4-point module = DIST 3 + SOEW 1, 3 lost - rounding DIST
         down AFTER the flow had cut the SOEW too, 4 lost)
write: a row whose effective < amount is set to effective; a row at 0 is DELETED (D11)
       DEW is never written (R9)
note : lost = sum(amount - effective) -> IndividualNote "ELINT lost: n" on the first module
```

* Idempotent: a second run finds every row fully carried and writes nothing.
* Max-flow over at most five sinks is a few dozen operations, and runs only for module ships.
* New DB helpers `setEwAmount` / `deleteEwEntry(gameid, shipid, turn, type, targetid)`:
  `adjustEwAmount` clamps at 0 and keeps the row ([DBManager.php:2120](source/server/controller/DBManager.php#L2120)).
* EW-boosted systems (Particle Impeders) also count against N on the client; no Kirishiac hull
  carries one, so `Navail` ignores them — say so in the handler.

### 8.5 The worked example (E6) through the algorithm — the Stage 6 exit test

N = 10; two modules of 4 cover the four friendlies. Rows: OEW 12 on the Shadow and SOEW 1 on each
friendly. At commit the Shadow is in arc, so (D15, scanner first) inN = 10, spill = 2, E = 4, N_used = 10,
**DEW = 0**, and 2 module points are left unspent (they never become DEW).

After movement the Shadow is outside both arcs:

* Navail = 10 − 0 − 0 = 10.
* OEW 12 can reach only the scanner → 10. The four SOEW rows reach the modules → 4.

Result: **OEW 10, SOEW stands, DEW 0, 2 cut + 2 never spent = the book's 4 lost** - the rulebook's own
answer (the "ELINT lost" note reports the 2 cut). Had the Shadow stayed in arc: OEW = 10 + 2 = 12, SOEW 4,
nothing written.

### 8.6 Use-time gates (D12, R10)

| Site | Gate |
|---|---|
| `EW::getBlanketDEW` [EW.php:217](source/server/handlers/EW.php#L217) + client `ew.getSupportedBDEW` [ew.js:911](source/public/client/ew.js#L911) | A module ship's BDEW reaches a friendly with only the points its covering modules carry (`ElintModules::areaPointsReaching` / `ew.getElintAreaPointsReaching`) - 0 where none covers |
| Stealth detection [baseSystems.php:275](source/server/model/systems/baseSystems.php#L275) | ×3 only toward a covered unit (otherwise ×2), and +2 per Detect Stealth point its covering modules carry |
| `EW::submitLateEw` [EW.php:614](source/server/handlers/EW.php#L614) | A module ship's late ELINT-function deltas are skipped (§8.2) |

**Per module (R10 refined, 2026-10-08).** The split of an area function between modules is the §8.4
routing run on the rows as they stand - after Movement those are the validated rows, so every reader
(fire time, the stealth check, the client's hit-chance preview) gets the same split. The client mirrors
the routing (`ew.routeElintModuleRows` / `elintMaxFlow` / `elintFindPath`) and agrees with the server hex
for hex. A module ship with BDEW can carry nothing else module-paid but in-arc OEW spill (BDEW excludes
the other ELINT functions), so the split is forced whenever the blanket uses every module point; when it
does not, the lower-id module pays first. SOEW, SDEW, DIST and JAM need nothing (D11). The jump-vortex
"ELINT on the team" bonus stays fleet-wide.

**Coverage, both sides.** The client tests a module's arc with `weaponManager.isOnWeaponArc` (facing and
ROLL - the first build ignored a roll). The server's `KirishiacElintModule::coversUnit` reads 360 as 0:
`getBearingOnUnit` can answer 360 for a unit dead ahead, and `Mathlib::isInArc` puts 360 on 180..360
but not on 0..180. That quirk is in every server arc test; only the module's is normalised here.

### 8.7 Display (Stage 7, optional polish)

* ✅ 2026-10-08: the module's arc drawn on hover the way a weapon's is (user ruling: not the shield-style
  smooth wedge) - the hex-edged range arc out to 30 hexes (the DIST / SOEW / Jamming reach), in the EW
  panel's ELINT teal, filled at 0.15 like the EW Detector's disc (`ShipIcon.showWeaponArc`,
  `ELINT_ARC_*`). The generator keeps the shields' smooth wedge. The
  generator's shield wedge needed `KirishiacAdvGravShield.prototype.defensiveSystem = true`: an
  ordinary shield gets that flag from its static blueprint, and the generator's game payload (D6)
  never carried it.
* EW target rows flagged while the subject is outside every live arc (Initial Orders and Movement).
* Data window: rating, arc, the −2 penalty, and "functions only toward units in this arc".

---

## 9. Lobby

### 9.1 Offers and labels

Offers come from the registry automatically, on the orbital's icon. Add the three IDs to
`systemEnhancements.LABELS` (mirror pair). Labels must ellipsize: the menu's 300px max-width is
load-bearing.

### 9.2 Mutual exclusion

`systemEnhancements.set` zeroes the rows of the same `group` and a DIFFERENT `groupRank` on that orbital
(revised 2026-10-08: equal ranks combine - `SYS_AGS` and `SYS_AGS2` are both rank 1, `SYS_ELINT` rank 2).
The server's `sanitiseSystemEnhancements` keeps the highest rank per (orbital, group) and notices the
rest. The mount runs the highest rank first, equal ranks in enhID order (`SYS_AGS` in place, `SYS_AGS2`
appended; `SYS_AGS2` alone goes in place). The menu row note gains its exclusion sentence only where the
orbital is also offered a clashing refit.

### 9.3 Preview (R15)

`apply` gains a swap branch, and `revert` undoes it:

* Stash the original system object per id, and the list of appended ids, on the ship.
* Build replacements with `SystemFactory.createSystemFromJson` from a **deep clone** of the ship's
  `systemEnhancementSwapPreviews` entry; set the AGS `output` to the count.
* Replace `ship.systems[id]`; append the second generator at the id the server will give it (same order).
* Scanner −2 per module through `rememberBase`.
* ⚠️ Duck-type: lobby ships are jQuery.extend clones and `instanceof` fails ([[arch_lobby_ship_objects]]).
* ⚠️ Stage 3 must prove two identical hulls do not share a `systems` array: replacing an element on
  one must not show on the other. `copyShip` re-applies refits
  ([gamelobby.js:4041](source/public/client/gamelobby.js#L4041)).

### 9.4 Fleet Check (R6)

One row in `checkChoices`: "Advanced Gravitic Shields: k of n ships (max 25%)" with OK or TOO MANY!.

### 9.5 Buy path, saved fleets, docs

* Buy path and saved fleets: unchanged machinery; the new rows ride the existing tables.
* Docs: `docs/ammo-options.html` "Available Refits" gains three entries; `docs/factions-tiers.html`
  gets a Kirishiac note. Use `data-anchor`, never `id=` ([[project_document_viewer]]).

### 9.6 YOUR FLEET costs (2026-10-08, user)

A bought row shows the unit WITHOUT its enhancements; each enhancement line under it shows its own cost
("- System Enhancements (2)  +1400p" under a 2400p Mastership, not one 3800p figure). `gamedata.rowDisplay`
takes the lines off the row's real charge, so the row and its lines always add up to it; the points total
is untouched. Lines come from `gamedata.enhancementLines`: a ship-level option at the buy dialog's series
(level i = price + i x step; a choice at its listed price; x flightSize for a flight), the refit line at
`pointCostSysEnh`; a bulk row's lines are for all its units. `gameLobby.css` right-aligns the line cost.

---

## 10. Gates — what an ordinary game pays

| Path | Cost without these refits |
|---|---|
| Mount at load | one `empty()` per ship |
| Apply in `onConstructed` | the existing early exit |
| `stripForJson` | the existing early exit; the new classes exist only on refitted ships |
| Client factory (D6) | one string compare per system per build |
| Client EW pool | one cached boolean per pool call |
| Movement advance | one static boolean |
| BDEW, stealth detection, late EW | one property read per ELINT ship, inside loops only ELINT ships reach |

Test every gate by forcing it false with modules present and asserting the un-gated result.

---

## 11. Stages and exit tests

Never commit — the user reviews and commits ([[feedback_fv_workflow]]). Run `php -l` against
`/usr/src/current`, not `/usr/src/fieryvoid` ([[howto_docker_db_access]]).

| Stage | Content | Exit test | Status |
|---|---|---|---|
| **0** | Rulings; baselines | Replay harness `check` green apart from the five known-bad games ([[arch_replay_corpus_known_failures]]); `enhancementsHarness.php` and `enhancementsDifferential.js` green | ✅ 2026-10-08: replay 119 passed / 5 known-bad; enhancementsHarness 254/0; differential 0 differing |
| **1** | Both classes (server + client); orbital generalisation (D8, D9); no refit wiring | Scratch PHP: a Lordship orbital hand-swapped through the factory — sub-chart 30/70, and 1–3 / 4–6 with two; orbital death destroys both; regeneration heals both; docked fold; the payload carries blueprint fields. Node: the client factory builds the shield at a beam's id with `weapon` undefined. **Replay `check` unchanged** (the generalisation must be behaviour-neutral) | ✅ 2026-10-08: `kirishiacRefitsHarness.php` 56/0, `kirishiacRefitsClientHarness.js` 16/0; replay report byte-identical to Stage 0 |
| **2** | Registry + offers + sanitiser (groups) + mount at load + `replaceEnhancementSystem` + scanner −2; `fvbuild.ps1 -Autoload`, statics regen | Offer counts on fresh statics: Lordship 16, Kingship 6, Overlord 8, Mastership 28 (24 AGS + 4 ELINT), Conqueror 6, Knightship 4; none on Heavy orbitals or weapons. Scratch game with planted rows: ids swapped, appended ids identical over two loads, a damage row on the swapped id lands on the generator, the enemy payload carries it. A doctored POST with two group rows, ELINT on a beam orbital, or rating 5 on the Conqueror is dropped or clamped | ✅ 2026-10-08: offer counts exact on fresh hulls AND regenerated statics; sanitiser groups; mount at load proven on a real DB load of game 4226 (rows planted + rolled back); harness 128/0 |
| **3** | Lobby: labels, exclusion, preview, previews JSON, Fleet Check row | Node lobby harness: buy/unbuy/rebuy restores the originals; two identical hulls stay independent; a copy does not share; destroying the orbital pre-battle refunds and reverts; the 25% row | ✅ 2026-10-08 (bundle build pending): client harness 41/0. Templates not per-orbital previews (§9.3 note); exclusion shown as a row hover note, not a notice - the lobby has no passive-notice element |
| **4** | AGS in play | Server `calculateHitBase` on an in-memory TacGamedata (the SHIP_ENHANCEMENTS Stage 5 `coatShot` pattern): the rating comes off hit and damage inside the arc only, once for two generators, docked and deployed, and not after destruction. The client mirror agrees | ✅ 2026-10-08: real calculateHitBase -15/-10 in arc, once for two generators, docked, destroyed last turn vs this turn; client getHitChangeMod agrees on 34/34 shooter hexes (agsdump) |
| **5** | ELINT pools (client, §8.2) + passive hint + panel row + DEW-row read | Node: `getEWLeft` / `getEwLeftFor` / `convertUnusedToDEW` across type mixes and in/out-of-arc OEW (D15); module points never reach DEW; scanner points never reach an ELINT function | ✅ 2026-10-08: client harness 62/0; 71/0 after the second pass (pools, D15 split - scanner first, click-order independence, R16 notice, late window, committed DEW, BDEW gate; module coverage agrees with the server on 336/336 hexes) |
| **6** | Validation + DB writes + use-time gates + late-EW skip + lost-points note | PHP harness: E6 (§8.5: OEW 10, DEW 0, 2 cut); four overlapping modules; a destroyed module; idempotence; a non-module ship untouched; the gate forced false runs nothing | ✅ 2026-10-08: server harness 157/0; 158/0 after the second pass - E6 (OEW 10, DEW 0, 2 cut; was OEW 8 / DEW 2 / lost 4 under the first D15), independent generator ratings, augmenting-path reroute, DIST step rounding, delete at 0, destroyed module, idempotence, gate on/off, BDEW gate, DB writes (rolled back). Replay byte-identical to Stage 0 |
| **7** | Polish and docs (§8.7, §9.5), icons | Visual check in the lobby and in game | ◐ 2026-10-08: docs (ammo-options Available Refits: two cards; factions-tiers Orbitals: a refit line), module/generator data windows, a REFIT line on the orbital's own tooltip, bundles built. Module and generator arcs on hover added after the play-test (checked on the real game 4452, headless). NOT done (optional): EW rows flagged out of arc. New art not made (shield.png / elintArray.png reused) |
| **8** | Docker play-test matrix (§13) | User-run | ⏳ user-run |

### 11.1 As built (2026-10-08) - where everything lives

| Piece | Where |
|---|---|
| `KirishiacAdvGravShield`, `KirishiacElintModule` (+ `forOrbital` factories, `coversUnit`, `ratingLine`) | `baseSystems.php`, after `KirishiacHeavyOrbital`; client twins in `defensive.js` / `baseSystems.js` |
| Orbital mounted list (D8): `wireMountedSystem`, `replaceOrbitalWeapon`, `addMountedSystem`, `getMountedSystems`, sub-chart split, death, regeneration, docked state, `addRefitDataLine` | `KirishiacOrbital` in `baseSystems.php` |
| `replaceEnhancementSystem`, `claimSystemEnhancementMount`, `registerElintModule`, `getElintModules`, `$systemEnhancementSwapPreviews` | `BaseShip` in `ShipClasses.php` |
| Registry `SYS_AGS` / `SYS_AGS2` / `SYS_ELINT`, slot functions, `mountSystemEnhancementSystems`, group sanitiser, `addSwapPreview` | `Enhancements.php` |
| The mount call (D4) | end of `DBManager::getEnhancementsForShips` |
| `setEwAmountById`, `deleteEwEntryById` | `DBManager.php`, after `insertEwEntry` |
| The arc rule: `ElintModules` (type lists, coverage, `route` max-flow with whole DIST steps, `computeEffective`, `areaPointsReaching`, writes, note) | `source/server/handlers/ElintModules.php` (new) |
| Gate `TacGamedata::$elintModulesPresent`; hook | `TacGamedata::markUnavailableSetMarkers`; `MovementGamePhase::advance` before `setPhase(5)` |
| Use-time gates (R10, per module) and the late-EW skip | `EW::getBlanketDEW`, `EW::submitLateEw`; stealth detection in `baseSystems.php` (`elintReaches` + `areaPointsReaching`) and `customTrek.php` (`elintReaches`) |
| D6 factory guard | `systemFactory.js` `createSystemsFromJson` |
| Client pools (§8.2, D15), out-of-arc / shortfall notice (`getElintShortfalls` + `noteElintShortfall`), per-module coverage (`elintModuleCovers`), routing mirror (`routeElintModuleRows`), BDEW share, committed-DEW read | `ew.js` (block after `getEwLeftFor`); `shipTooltipInitialOrdersMenu.js` passes the OEW target |
| Arcs on hover (§8.7) | `ShipIcon.showWeaponArc` (module branch, `ELINT_ARC_*`); `defensive.js` (`defensiveSystem` on the generator) |
| EW panel ELINT row | `ShipWindowEw.js` |
| Lobby: labels, groups + hover note, swap preview (`applySwaps` / `revertSwaps` / `buildSwapSystem`), scanner -2, Fleet Check predicate | `systemEnhancements.js`; row note in `SystemEnhancementsSection.js`; the 25% row in `gamelobby.js` `checkChoices` |
| Docs | `docs/ammo-options.html` (systemrefits), `docs/factions-tiers.html` (Kirishiac, Orbitals) |
| EDF power-lock mirror reads the orbital, not `stowed` (identical for every weapon) | `EdfExposure::isPowerLocked` |

**Changes from the draft made while building** (each also noted where it applies above):

* **§9.3 preview data:** one compacted TEMPLATE per class per hull plus an orbital → mount id map
  (`{mounts, templates}`), ~2KB per hull, instead of a full entry per orbital (~35KB on a Mastership).
  The lobby patches id, section, arcs, name and rating; the shield's rating sentence lives in its own
  `Shield rating` data key so the lobby rewrites one line (`agsRatingLine`, mirror of `ratingLine`).
* **§9.2 notice:** the lobby has no passive-notice element, so the exclusion is a hover note on the
  menu row instead; the removed row visibly drops to 0 in the same menu.
* **D16 / R13:** real power figures (module 4, shield 0); no inheritance (see D16 for why).
* **DIST rounding:** the after-movement check rounds a cut Disruption row DOWN to whole steps of 3
  (4 for ConstrainedEW) - a part-step does nothing.

**Harnesses** (⚠️ `/tests` is in `.gitignore`: these live on disk only unless force-added):

```
docker exec -w /usr/src/current fieryvoid-php-1 php tests/replay/kirishiacRefitsHarness.php     # 168 checks, stages 1-6 + the game-4452 fixes (planted DB rows always rolled back)
node tests/replay/kirishiacRefitsClientHarness.js                                               # 88 checks, stages 1-5 + the fixes + the shortfall notice (+ agsdump differentials:
                                                                                                #   coverage module by module, 6 facings, rolled; BDEW split per hex)
```

After the play-test fixes (2026-10-08): server harness 168/0, client 88/0, replay `check` unchanged (118
passed; the only failures are the five known-bad games; three corpus games skipped because they were
played on since the baseline was recorded).

Regression state at the end of 2026-10-08: replay report byte-identical to Stage 0 (119 passed, the
five known-bad games); `enhancementsHarness` 257/0; lobby differential 0 differing; ship-data
validator 0 new. Every other stage harness that fails (`initialOrdersTooltipHarness`,
`lateEwElintHarness`, `ednPreviewHarness`, `reinforcementsStage8/9ClientHarness`,
`walkersStage12/13ClientHarness`, `reinforcementsStage6Harness`, `walkersStage13Harness`,
`walkersStage15Harness`) fails IDENTICALLY on HEAD d9d800755 - proved on a clean worktree.

---

## 12. Risk register

| Risk | Mitigation |
|---|---|
| The client merges the new system onto the old weapon's blueprint (`weapon: true` on a shield) | D6; Stage 1 Node test |
| An undeclared property write throws on PHP 8.2 and kills the game load | D9; Stage 1 loads a docked and a deployed refitted orbital |
| A system mounted after damage loads silently loses its damage | D4; Stage 2 damage-row test |
| Appended ids drift between loads, or between lobby and game | D5 ordering, mirrored in the lobby; Stage 2/3 tests |
| Module points leak into DEW, or the panel shows the recompute after validation | §8.2; Stage 5/6 tests |
| A zero OEW row keeps counting as a target | D11 delete; Stage 6 |
| Late EW bypasses the arc rule | §8.6 gate |
| The lobby preview shares a `systems` array between two hulls | Stage 3 two-hull test |
| A POST-side ship carries the old weapon class | D7 analysis; the constraint in the class docblock |
| The orbital generalisation changes existing games | Stage 1 replay `check` |
| Swaps free power for boosting | Cannot happen with a surplus reactor (D16); the swap only lowers what a docked switch-off frees |
| OEW on an out-of-arc enemy paid with module points, then lost | D15 funding order |
| The Mastership's jump-exit scatter worsens with the −2 scanner | Intended (it reads sensor rating); say so in the data window |

---

## 13. Docker play-test matrix (Stage 8)

| Scenario | Expect |
|---|---|
| Lordship, AGS 3 on two front orbitals | Front shots −15% and −3 damage, counted once; side shots unaffected |
| AGS 3 + 2nd AGS 2 on one orbital | Two generators; shots in arc -15%/-3 (the higher counts); kill the rating-3 one and the rating-2 one holds; the second kill drops it; overkill to the orbital |
| Orbital destroyed (deployed) | Both generators destroyed in the critical phase; no shield next turn |
| Dock and regenerate for 5 turns | Generators restored with the orbital |
| Mastership, ELINT on A + J | Scanner 14 → 10; ELINT row 8; SOEW/DIST/SDEW/JAM offered; the scanner alone can no longer pay for them |
| E6 re-enacted | Commit: DEW 0, ELINT row 2 / 8 unspent. OEW 12 → 10 after movement; SOEW stands; DEW 0 unchanged; ELINT lost 2 |
| OEW on an enemy in arc, within the scanner's points | Paid from the scanner at commit (D15); still 100% after the enemy leaves the arc |
| Bought / saved fleet in YOUR FLEET | Row shows the hull cost; "System Enhancements (n)" and each ship-level enhancement line show their own +cost; the points total is unchanged |
| BDEW with a friendly outside both arcs | That friendly gets no BDEW |
| BDEW 8 on a port + starboard pair (D + G), one friendly each side | Each friendly 1 BDEW (-5%), the Mastership itself 2 |
| DIST 6 on a unit one 4-point module covers + SOEW 1 on that module's side (game 4452) | After movement: DIST 3, SOEW stands, ELINT lost 3 |
| The same allocation at Initial Orders | The second DIST step is allowed, with a fading "Only 3 of the 6 DIST points on … the other 3 are lost" notice; the SOEW after it says nothing |
| Hover an ELINT module / Adv. Gravitic Shield icon | Module: teal hex-edged arc out to 30 hexes; generator: the cobalt shield wedge. Both turn with the ship (mirrored when rolled) |
| Enemy view | Sees the generators and their effect; no ✦ badge, no summary line |
| Saved fleet with refits | Reloads with the same refits and points; the preview shows the swap |
| Replay of a refitted game | Identical on re-watch |
| Ordinary game | No change (replay harness) |

---

## 14. Out of scope

* Heavy orbitals (A7).
* AGS on any non-Kirishiac hull.
* Real per-viewer masking of the generators — they must reach the enemy for hit-chance previews
  (WEAPON_ENHANCEMENTS_PLAN.md §6.3).
* A hard buy-time block for the 25% rule (the R6 alternative).
* Any change to `isElint()` for ships without modules.
