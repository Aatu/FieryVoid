# Ship Enhancements - Advanced Engine Module, Stealth Coating, Jump Accelerator

Three new ship-level enhancements, a review of the enhancement code for efficiency, and distinct, iconed
section headers in the buy, edit and copy dialogs.

Status: **Stages 0-2 BUILT 2026-10-03, uncommitted** (§6.1 has the as-built notes and what Stage 1's
differential test found). Stages 3-7 not started. D1-D20 ruled 2026-10-03 (§1). D21 (Elite Crew's price
and Poor Crew's documented numbers) is still open; Stage 1 built its default, "the code is right".

## Where it stands

- **What the rulings changed from the drafted defaults:**
  - **Advanced Engine Module improves only the strongest Engine** (D1), chosen from the hull as built,
    before any enhancement row is applied, because Poor Crew lowers that same engine's thrust (§2.1.2).
    A critical on any Engine still removes it.
  - **Stealth Coating is 1 point (5%)** (D5), and it **needs a weapon that uses OEW** as well as a
    shooter holding OEW ≥ 1 (D9). It is open to **every ship-path unit except bases, terrain and
    factionAge ≥ 3** (D11), so OSATs and Middleborn hulls can buy it.
  - **Jump Accelerator is "-33%", meaning × 0.67, rounding halves up** (D13). This matches × 2/3 for every
    delay below 50. The 13 hulls at 50 or 65 come out one turn longer (34 instead of 33, 44 instead of 43).
    **Ancient drives are excluded** (D15): they are already legacy drives, and the offer also refuses
    factionAge ≥ 3, which covers the Vorlons' ordinary drives.
  - **The 64 px icons are committed** (D20), and the **optional review items become Stage 7** (D18).
  - **Player docs are drafted** (§2.4): three cards for the "Ammo, Options & Enhancements" document.
- **Found during the rulings pass:**
  - **The Jump Accelerator's +2 range also lengthens the hold distance**, from 4 to 6 hexes. The close
    check reads the same `$range` (§2.3.5). Built as-is unless you say otherwise.
  - **Poor Crew's docs say -1 Engine, but the code takes 2** (new in R6, folded into D21).

- **All three enhancements fit the existing machinery.** Each one is an offer block in
  `Enhancements::setEnhancementOptionsShip`, a `case` in `setEnhancementsShip`, a payload line in
  `addShipEnhancementsForJSON` or `addSystemEnhancementsForJSON`, and a lobby case in
  `lobbyEnhancements.js`. No schema change and no new table.
- **Two of them need one small new mechanism each:**
  - **Stealth Coating** is a dropdown whose choices cost different amounts. The only dropdown today (the
    Chameleon disguise) is free, and its widget forces the row's cost to 0 (`confirm.js:415, :421`). The
    widget needs an optional per-choice price list (§2.2.2).
  - **Advanced Engine Module** is "removed permanently". A critical is loaded only while its `turnend`
    is 0 or not yet behind the turn being loaded (`DBManager::getCriticalsForShips`), and self-repair
    ends one by setting `turnend` to the previous turn - so a repaired critical is gone from the very
    next load. "Has the engine ever had a critical?" cannot be read from the loaded list. The loss needs
    a one-time note (§2.1.3).
- **The Jump Accelerator's reactor deficit applies to ordinary reactors and is skipped on Mag-Grav
  (fixed-power) reactors** (D16, ruled). An ordinary reactor already covers the drive's normal draw out
  of its output, which is why the extra draw has to show up as a deficit. A fixed-power reactor covers
  nothing for free: it already subtracts every powered system's draw, so the doubled draw is enough by
  itself, and adding the deficit as well would charge the extra power twice. Two hulls are affected: the
  Ipsha Jumpsphere and the Ipsha Scout Wheel (§2.3.3).
- **Item 4 is a cleanup, not a speed-up.** Measured: applying enhancements costs 4-5 µs per ship with
  nothing bought and 8-14 µs with Elite or Poor Crew, against 115-175 µs just to construct the ship.
  Fewer loops cannot be seen in a page load. The review is still worth doing: it removes duplicated code,
  and it found two lobby bugs and two places where the player docs disagree with the code (§4 R6, D21).
- **`tac_enhancements.enhid` and `tac_saved_enh.enhid` are both `varchar(10)`.** A longer ID either
  fails the whole purchase or is cut short and never matches its `case`, depending on the server's SQL
  mode. The three IDs are `ADV_ENG` (7), `STEALTH_CT` (10) and `JUMP_ACC` (8).
- **The game and the lobby apply enhancements in opposite orders.** In game the rows come from a query
  with no `ORDER BY`, so InnoDB returns them in primary-key order, `enhid` ascending (checked on game
  4306). The lobby applies them options first, then by ID descending (`compareEnhancements`). Any result
  that depends on order differs between the two. The Jump Accelerator would be the first to hit this,
  because it rounds the same jump delay Elite/Poor Crew rounds. §4 R3 fixes the order on both ends.
  **Built (Stage 1):** the lobby now walks the rows in the game's order, enhid ascending, and both ends
  apply the jump delay in one post-pass. Stage 1's differential test showed the order mattered far beyond
  the jump delay (§6.1).

## 1. Decisions

D1-D20 were ruled on 2026-10-03, and the rest of this plan builds the rulings. Where a ruling differs
from the drafted default, the row says so. D21 is new; its default is what gets built unless you rule
otherwise.

| # | Question | Ruling |
|---|---|---|
| **Advanced Engine Module** | | |
| D1 | Hulls with more than one Engine (71 in the static files: Dilgar Abrithi, Brakiri Areko, ...). Improve every Engine, or only the strongest (as Improved Engine does)? | **Only the strongest Engine** (highest thrust; the first in construction order wins a tie), **and a critical on ANY Engine removes the module.** *Changed from "every Engine".* The strongest is picked from the hull as built, before any enhancement row is applied (§2.1.2). Re-measured: the offer goes to exactly the same 1,776 hulls either way, and only 5 hulls mix efficiencies across their engines (Dilgar Mishakur, Tratharti ×2, Hyperion Iota ×2). |
| D2 | "Any kind of critical" - does that include one-turn effects (Engine Flux's per-turn rolls, a Walker field's EdfThrustDrain) and pre-battle criticals? | **Yes, any kind**, on any Engine. An engine given a pre-battle critical in the lobby means no module from turn 1 (not warned about in the buy dialog - rare). |
| D3 | A critical lands at the end of turn N. From when is the module gone? | **From turn N+1.** The module worked during turn N. |
| D4 | "Max 1/1": efficiency never goes below 1, and the module is not offered where the strongest engine is already 1 (56 hulls). Hyach Thruster Specialists also take 1 off, with no floor, so module + specialist reaches 0 (free thrust) on an efficiency-2 engine. | **Floor the module at 1, and add the same floor to the Specialist** (`baseSystems.php` ~12607, `baseSystems.js:3385`). That changes only cases that would reach 0 today. |
| **Stealth Coating** | | |
| D5 | How many profile points does it remove? | **1 point (5%).** The plan writes 1 throughout. |
| D6 | Which coverage options? The brief gave "Full / Rear and Sides / Rear" and "None / All / Front & Sides / Front Only". | **None / All / Front & Sides / Front Only** (the dropdown list). |
| D7 | Price of partial coverage. "-25% per side not covered". | **Four facings: 100% / 75% / 25%.** Many hulls do not have four sections (`HeavyCombatVesselLeftRight`, `MediumShipLeftRight`, `BaseShipNoFwd`, six-section hulls), so the facing is read from the shooter's **bearing**, never from the section hit: 330-30 is the front, 150-210 the aft, anything else a side (§2.2.4). That gives every hull the same four facings, whatever sections it has. |
| D8 | Rounding. Full price = 1000 ÷ ((forward + side profile) ÷ 2). | **`ceil()` per choice**, the convention for every percentage-priced enhancement. Profiles 15/17 give 63 / 47 / 16. |
| D9 | What counts as "an OEW lock"? | **Both conditions: the weapon uses OEW (`useOEW`) AND the shooter holds OEW ≥ 1 on the coated unit after disruption EW.** *Changed: the draft ignored `useOEW`.* Your summary: the coating cancels 1 point of OEW. It is built as D5's 1-point profile cut, so **it never removes the lock**. A shooter on exactly 1 OEW keeps its lock (no doubled range penalty) and its SOEW, and still loses the 5%. Never triggered by a `useOEW = false` weapon or a mine (lock assumed, no OEW). A fighter triggers it only with OEW of its own (a Walker Mapmaker flight). |
| D10 | Advanced Sensors already ignore younger races' Jammers, BDEW and SDEW. The coating too? | **Yes.** It keeps the BDEW/SDEW test, target factionAge < 3 (`weapon.php:2104`). D11 makes that part always true, so in practice Advanced Sensors always ignore the coating. |
| D11 | Which units can buy it? | **Every unit on the ship path except bases, terrain and factionAge ≥ 3.** It is a standard enhancement, offered unless a set disables it. *Changed from "ships only".* OSATs, MCVs and Middleborn (factionAge 2) hulls are included. Terrain and every ship-path factionAge ≥ 3 hull are already on sets that block standard enhancements (checked); the factionAge test stays as a guard for future hulls. Bases need an explicit `$ship->base` test. Mines and shuttles are on blocking sets. **Fighters are not offered it** - they have their own offer path, and the formula would price a profile-7 fighter at 143 points (§2.2.1). |
| D12 | Ballistic shots? | **Judged from the launch point's bearing** - the same point the profile is already taken from. |
| **Jump Accelerator** | | |
| D13 | "Jump delay -33% (rounded)". | **-33%: delay × 0.67, fractions of 0.5 or more round up.** *Changed from × 2/3.* Computed in whole numbers, `intdiv(delay * 67 + 50, 100)` in PHP and `Math.floor((delay * 67 + 50) / 100)` in JS, so the two ends cannot round a float differently. Below delay 50 it is the same as × 2/3. It differs on the 12 hulls at delay 50 (34, not 33), the one at 65 (44, not 43), and a few Poor Crew combinations. |
| D14 | Elite/Poor Crew also scale the jump delay, ±20% per level, rounding at each step. Which goes first? | **Crew first, then the accelerator, on both ends.** It matters: delay 20 with one level of Elite Crew is 11 one way and 10 the other. |
| D15 | Which drives can take it? | **Drives that open jump points - not legacy and not Ancient drives.** Not the legacy one-click drives (Star Wars Hyperdrive, BSG FTL, Shadow Phasing Drive), which form no jump point, so the range and the opening roll mean nothing to them. Not gates or Trek nacelles. Ancient drives are already legacy (`markAncient` = `markLegacy` + `$ancientJump`, the same for `markWalker`). The Vorlons and the System Pursuit Leader carry ordinary drives on factionAge 3 hulls; their blocked sets keep them out today, and the offer also refuses factionAge ≥ 3 (§2.3.1). |
| D16 | Power. | **Yes: double the drive's draw and take its normal draw off the reactor.** An ordinary reactor already covers the normal draw, so the extra draw has to be a real deficit. **On fixed-power reactors only the draw doubles** (§2.3.3): they cover no draw for free, so the same reasoning leaves no deficit. |
| D17 | Is the failure chance doubled on Maintain turns too? | **No - only on the turn the jump point is opened.** Capped at 100%. |
| **Review and headers** | | |
| D18 | How much of item 4? | **R1-R7**, behaviour-identical plus the bug fixes. **O1-O3 become an optional Stage 7** (§4.3, §6). §4.4 is still unplanned. |
| D19 | Header colours. | **Palette A**: gunmetal / bronze / verdigris (§5.1). |
| D20 | The icons. | **18 px (20 px on touch screens), tinted to the section colour with a CSS mask. Commit 64 px copies** (about 2 KB each) under the same three names (§5.4). |
| D21 | **New, found while drafting the docs stage.** Two places where the player docs and the code disagree. **Elite Crew's price:** the code charges +50%, then +75% for the second level (Enhancements.php:238-239), while `docs/ammo-options.html` and the code comment say +40% / +60%. **Poor Crew's Engine and Reactor:** the docs (and the code comment) say -1 each, while the server takes 2 of each per level. The lobby already takes 2 off the Engine but only 1 off the Reactor. Which numbers are right? | **Default: the code.** It is what every game has charged and played with. Stage 1 corrects the comments and brings the lobby's Poor Crew reactor to -2 (R6); Stage 6 corrects the docs. If the docs are right instead, each fix is a one-line server change in Stage 1, and R6's lobby fix goes the other way. |

## 2. The three enhancements

### 2.1 Advanced Engine Module (`ADV_ENG`)

The strongest Engine's efficiency (the power one point of extra thrust costs) improves by one step,
never below 1. 10% of the ship's cost, limit 1. Removed permanently the first time ANY Engine on the
hull takes a critical (D1).

**2.1.1 Offer** - `setEnhancementOptionsShip`, a standard offer (offered unless the hull's enhancement
set disables it):
- Offered when the hull's **strongest** `Engine` (subclasses included; highest `output`, the first in
  construction order on a tie - Improved Engine's test, R1's `strongestSystem`) is `boostable` with
  `(int)boostEfficiency > 1`. Measured from the static files over the 1,832 standard-set hulls with an
  Engine, by the strongest engine's efficiency: 878 at 3, 470 at 2, 340 at 4, 63 at 5, 25 at 6 or more,
  56 at 1 (not offered). No hull has its strongest engine at 1 and another above it.
- `array('ADV_ENG', 'Advanced Engine Module', 0, 1, ceil($ship->pointCost * 0.1), 0, false)`.
- Add `'ADV_ENG'` to the ship branch of `blockStandardEnhancements` (Enhancements.php:58). Its comment
  says "ADD ANY NEW STANDARD ENHANCEMENTS HERE!" for a reason: without the line, every blocked set
  (Vorlon, Shadow, Mindrider, Torvalus, Triad, Terrain, Mines, Shuttles...) is offered it.
- OSATs have no Engine, so the OSAT set needs no change.

**2.1.2 Apply** - `case 'ADV_ENG'` in `setEnhancementsShip`:
- **Pick the Engine before the loop**, in a pre-pass that runs only when an `ADV_ENG` row is among the
  rows: `strongestSystem($ship, 'Engine', 'output')` on the hull as constructed. That is the engine the
  offer looked at, because no row has changed an output yet. The `case` then uses it.
  ⚠️ **This is a deliberate exception to R1's "ask at the moment of application".** Poor Crew takes 2
  thrust per level off that same strongest engine, and the two ends apply rows in opposite orders: the
  game goes `enhid` ascending (`ADV_ENG` before `POOR_CREW`), the lobby descending (`POOR_CREW` first).
  On a hull with two equal engines A and B, asking at application time would improve A in game and B in
  the lobby. Picking before the loop gives A on both. The lobby can do the same, because
  `lobbyEnhancements.apply` runs once per build, on an unenhanced copy (guarded by
  `enhancementsApplied`).
- Mark **every** Engine on the hull as watched (a protected flag on `Engine`, set through a method - a
  public property would be written into every static blueprint). The note writer needs it on every
  engine, because a critical on any of them ends the module (§2.1.3). Only the picked engine is
  improved.
- Work out **lost** for the hull: any Engine carries a critical with `turn < TacGamedata::$currentTurn`,
  or any Engine carries the `AdvEngLost` note. Both are loaded before `onConstructed` runs, which is where
  the enhancements are applied.
- If not lost: `boostEfficiency = max(1, boostEfficiency - 1)` on the picked Engine only.
- Clamp the bought count to 1. The server trusts the client's count for ship-level enhancements.

**2.1.3 Making the loss permanent** - `Engine::generateIndividualNotes`:
- Write `AdvEngLost` (value = the turn) on any watched Engine that is not yet noted and has any
  critical - the engine that took the critical, which need not be the improved one. Only in the phase-4
  sweep of `FireGamePhase::advance` (FireGamePhase.php:54). That sweep runs on a real
  reload, straight after `Criticals::setCriticals` has added this turn's criticals to
  `$system->criticals`. It is the same reasoning the Chameleon suite uses at FireGamePhase.php:60: catch
  it at the end of Firing, before a self-repair can bring it back.
- POST-side ships can never write it. The watched flag is set only by `setEnhancements`, which never runs
  on a ship rebuilt from a POST ([[arch_post_side_ship_reconstruction]]).
- Read it back in `Engine::onIndividualNotesLoaded`. Notes load with `turn <=` the viewed turn, so a
  replay of an earlier turn shows the module as it was then. `notekey_human` must stay under 40
  characters.

**2.1.4 Payload** - `addSystemEnhancementsForJSON`, `case 'ADV_ENG'`: send `boostEfficiency` for every
Engine, in both states. Only the picked one differs from the blueprint, but sending every Engine keeps
the payload case free of the pick. The static blueprint holds the unimproved value, and `ShipSystem::stripForJson`
never sends it. Nothing else changes on the client: `power.countBoostPowerUsed` multiplies by it
(power.js:1089) and `Engine.prototype.addInfo` shows it as "Efficiency" (baseSystems.js:190). The Hyach
Specialist block (ShipSystem.php:330) already sends the same field the same way.

**2.1.5 Tooltip** - its own line, like `CHAM_DISG` and `SHAD_TEND`: "Advanced Engine Module", or
"Advanced Engine Module (lost)". Exclude `ADV_ENG` from the generic line (Enhancements.php:2322).

**2.1.6 Lobby mirror** - `case 'ADV_ENG'` in `lobbyEnhancements.setEnhancementsShip`: the strongest
system named `engine` (highest `output`, strict `>`, picked before the loop exactly as on the server)
goes down by 1, never below 1, and `data["Efficiency"]` follows. Add its marker to
`resetEnhancementMarkersShip`, as every other case does. ⚠️ Mirror pair with the server pre-pass: same
pick, same moment.

### 2.2 Stealth Coating (`STEALTH_CT`)

Removes 1 point (5%) from the defence profile (D5) against a shot from a weapon that uses OEW, fired by
a unit holding OEW ≥ 1 on the coated unit after disruption EW (D9), when the shot comes in on a covered
facing. Coverage: None / All / Front & Sides / Front Only (D6). Price: 1000 ÷ the average of the forward
and side profiles, × 100% / 75% / 25% by coverage (D7), rounded up per choice (D8).

**2.2.1 Offer** - a choice-valued tuple, like `CHAM_DISG`, plus a new index 8:

    array('STEALTH_CT', 'Stealth Coating', 0, 3, $full, 0, false, $choices, $prices)
    $choices = [['', 'None'], ['all', 'All'], ['fs', 'Front & Sides'], ['front', 'Front Only']]
    $prices  = [0, ceil($full), ceil($full * 0.75), ceil($full * 0.25)]

- `$choices` comes from one static helper (`Enhancements::profileCoatingChoices()`), which the offer, the
  stored-name resolver and the tooltip all read.
- **Index 8 is new**: an optional price per choice. Absent means free, which keeps the Chameleon disguise
  exactly as it is.
- Not gated on `$offerChoiceLists`. That gate exists because the Chameleon's list costs a full ship
  enumeration; this one is a literal.
- `isOption` is false, so the row files under Enhancements (`confirm.buySectionOf`).
- Add `'STEALTH_CT'` to `blockStandardEnhancements`, which keeps it off every blocked set: terrain, mines,
  shuttles and all the Ancient sets.
- **Eligibility (D11):** offered in `setEnhancementOptionsShip` when `!$ship->base` and
  `$ship->factionAge < 3`. Every ship-path factionAge ≥ 3 hull is on a blocking set today (checked over
  all 105 files), so that test is a guard for future hulls. The OSAT set does not block standard
  enhancements, so OSATs get it, as D11 intends. Fighters never see it: `setEnhancementOptionsFighter`
  is a separate path, and the formula would price a profile-7 fighter at 143 points.
- Bases being out also settles a problem the draft had to work around: a base has no facing, and the
  client reads one profile for it.

**2.2.2 The dropdown, priced** - `confirm.applyEnhancementChoiceWidget` (confirm.js:392):
- When `enhancement[8]` exists: label each option with its price ("Front & Sides (47 pts)"), and set
  `data('enhCost')` from the price list, on build and on every change (plus `enhOptionCost` when the row
  is an option).
- ⚠️ **The edit and copy dialogs seed `enhCost` from the arithmetic series** for `count` levels
  (`addBuyEnhancementRows`, confirm.js:1105). For a choice, `count` is an index, so that seed is
  meaningless. The widget must overwrite it from the price list, or editing a coated ship charges for it
  twice.
- Every reader already uses `data('enhCost')` and `data('count')` - `getTotalCost`, `doBuyShip`,
  `readBulkPurchase`, the edit and copy paths - so none of them changes.

**2.2.3 Persisting the pick**
- `getStoredEnhancementName` (Enhancements.php:2075) returns the KEY (`all` / `fs` / `front`), resolved
  from the submitted index against the server's own list, so a doctored payload cannot invent a coverage.
- Saved fleets need no change: `Manager::loadSavedFleet` already re-derives the index from the stored name
  for any option carrying index 7.
- In game the row comes back as `[id, enhname, numbertaken, 0, 0, 0]`: resolve by name first, by index
  second, and treat anything else as None - the `resolveChameleonDisguise` shape. An unknown key never
  becomes a coating.

**2.2.4 The effect, server** - `Weapon::calculateHitBase` (weapon.php ~2127-2170):

    $coatingLock = false;                    // before the useOEW block (weapon.php:1800)
    // inside it, straight after `$oew -= $dist;`:
    $coatingLock = ($oew >= 1);
    ...
    if (TacGamedata::$profileCoatingPresent && $coatingLock) {
        $defence -= $target->getProfileCoatingReduction($shooter, $this->ballistic ? $launchPos : null);
    }

- `getProfileCoatingReduction` returns 0 or 1 (D5): 1 when the unit carries a coating that covers the
  shot's facing and the shooter lacks Advanced Sensors (D10).
- **Placed after `$defence` is set and before the ProfileIncreased block**, so a called shot at a system
  with its own flat profile (`$calledProfileOverride`, the Kirishiac orbitals) still replaces it.
- **The facing comes from the BEARING, not from a section number:** `getBearingOnUnit($shooter)` for
  direct fire, `getBearingOnPos(mathlib::hexCoToPixel($launchPos))` for ballistics (D12) - the helpers
  `doGetHitSection` and `getHitSystemByTable` already use (ShipClasses.php:3557, :3569). 330-30 is the
  front, 150-210 the aft, anything else a side, through `mathlib::isInArc`. These are exactly the arcs
  the client's `getShipDefenceValue` tests, so the two ends agree by construction. Section numbers would
  not do: `HeavyCombatVesselLeftRight`, `MediumShipLeftRight` and `BaseShipNoFwd` have no front section
  (a shot from ahead lands on section 3 or 4, carrying the FORWARD profile), and `getHitSection` returns
  0 once a section is destroyed (ShipClasses.php:3822), which would switch off a Front Only coating
  exactly when the front is gone. A bearing exactly on an arc boundary is already ambiguous for the
  profile itself; it resolves the same way on both ends.
- **Lock** (D9): `$coatingLock`, captured inside the `useOEW` block, so a weapon with `useOEW = false`
  never triggers it. Its `$oew` is `getOEW($target) - DIST` there, **before** the fighter branch rewrites
  `$oew` as the offensive bonus (weapon.php:1907-1911). Reading it any later would let every fighter
  "lock" through its OB. That one value covers every case D9 lists: a ship needs OEW ≥ 1 after
  disruption, a Mapmaker flight needs its own OEW, and a mine allocates none. Capturing it there also
  saves a second `getOEW` call.
- **The coating never touches `$oew` itself.** "Cancels 1 point of OEW" is how it is described, but it
  is built as a defence cut, so the no-lock penalty (`$oew < 1`, weapon.php:1957) and SOEW (zeroed below
  1 OEW) see the real OEW. A shooter on exactly 1 OEW keeps its lock and loses the 5%.
- **Jammer:** a Jammer working against the shooter already counts as "no lock-on" in FV (SOEW zeroed,
  weapon.php:1977), but the shooter's OEW still counts toward the shot. D9's test is OEW ≥ 1, so the
  coating applies under a Jammer too. A coated hull with a working Jammer gets both effects.
- **Advanced Sensors** (D10): no reduction when the shooter has them and the target's factionAge is < 3.
  D11 makes the second part always true.
- **`$defenceFake` is left alone.** The coating is real equipment on the real hull; the Chameleon
  suite's second threshold describes the simulacrum ([[project_chameleon_sensors]] D3b). A disguised
  ship's payload is built from the blueprint and never carries the coating, which is also correct.
- **The gate.** `TacGamedata::$profileCoatingPresent` is set to true by `setEnhancementsShip` whenever a
  coating resolves, and is **never reset within a request**. `FireGamePhase::advance` builds two gamedata
  loads in one request, so a reset in the wrong place would silently switch the coating off, while a
  stale true only costs one method call per shot. Every ordinary game then pays one static read per shot
  ([[arch_defensive_mod_aggregation]]).

**2.2.5 Payload** - `addShipEnhancementsForJSON`, `case 'STEALTH_CT'`: `profileCoating` (`all` / `fs` /
`front`) and `profileCoatingMod` (1). Public, like Elite Crew's profiles: the shooter's own hit-chance
preview needs it, which is the rule BaseShip::stripForJson already states (ShipClasses.php:929).

**2.2.6 The client preview** - `weaponManager.calculateHitChange` (weaponManager.js:2391):
- A new `weaponManager.getShipDefenceFacing(shooter, target)` returning `front` / `aft` / `side`, using
  the same arc tests as `getShipDefenceValue` (weaponManager.js:2860), plus a `Pos` twin for ballistics.
  Rewrite `getShipDefenceValue` on top of it, so the two cannot disagree about a facing.
- Lock: taken from `weaponManager.computeOEW` (weaponManager.js:2035), the client twin of the server's
  `useOEW` block. It returns one more field, `coatingLock = weapon.useOEW && oew >= 1`, captured
  straight after the `dist` subtraction and before the `shooter.flight` branch, exactly where the
  server captures it. Advanced Sensors mirrored.
- Subtract it in `goal` and add a breakdown row, "Stealth Coating", of -5. That keeps the
  `sum(modifiers) === hitChance` check at weaponManager.js:2540 satisfied.

**2.2.7 Text** - in game, its own tooltip line ("Stealth Coating: Front & Sides"), excluded from the
generic one like `CHAM_DISG`. In the lobby `describeTaken` already renders a choice that way.

**2.2.8 Naming** - call the code `profileCoating`, not `stealth...`. "Stealth" already has 230+ hits
(ships.js, ew.js, ShipTooltip.js, the masking code), and a grep for it is how the info-bleed audits are
done. The ID and the name players see keep "Stealth".

### 2.3 Jump Accelerator (`JUMP_ACC`)

Jump delay -33% (× 0.67, halves round up), jump point range +2, double power draw, and double the
failure chance on the turn the drive opens a jump point. 10% of the ship's cost, limit 1.

**2.3.1 Offer** - the hull has a Jump Engine that opens jump points, and is not Ancient (D15).
`$hasJumpRecharge` is protected, so add a public predicate, `JumpEngine::canTakeJumpAccelerator()` (not
legacy, not a gate, has a jump recharge). "Not legacy" already rules out every Ancient drive, because
`markAncient()` and `markWalker()` both call `markLegacy()`. The offer also requires
`$ship->factionAge < 3`. That covers the ordinary drives on factionAge 3 hulls (the 12 Vorlon hulls and
the System Pursuit Leader), which only their blocked sets keep out today. No hull in the fleet mounts two
Jump Engines (checked), but read them through `JumpEngine::getUnitJumpEngines` anyway - never a bare
sweep of `$ship->systems` ([[project_jump_points]]). Price `ceil(10%)`. Add `'JUMP_ACC'` to
`blockStandardEnhancements`.

**2.3.2 Apply**
- New `JumpEngine::applyJumpAccelerator()`. It is a method on JumpEngine for the same reasons
  `applyCrewJumpDelayModifier` is (baseSystems.php:7194): `$hasJumpRecharge` is protected, and the
  durable field is `$delay`.
  - `$range += 2`.
  - `$powerReq *= 2`; return the original draw, P.
  - Set a protected `$jumpAccelerated`, published only when true (the `ancientJump` convention).
- Jump delay, in the post-pass (§4 R3): Elite levels, then Poor levels (D14), then
  `max(1, intdiv($delay * 67 + 50, 100))` - × 0.67 with halves rounding up, in whole numbers (D13).
  Write `$delay`, `$loadingtime` and `$turnsloaded` exactly as `applyCrewJumpDelayModifier` does.
  Skipped when `$delay <= 0` (markLegacy and markGate zero it).
- Reactor: `$reactor->output -= P` on the hull's Reactor, **unless `$reactor->fixedPower`**. No hull with
  a Jump Engine has more than one Reactor (checked), so this is the one `getReactorPower` reads on the
  client.

**2.3.3 Power - why this exact shape.** A standard reactor's `output` is the ship's spare power with
everything switched on: the drive's normal draw P is already netted out of it, and switching a system
off gives its draw back ([[arch_lazy_window_side_effects]]). A fixed-power reactor works the other way
round: its `output` is total generation and every powered system subtracts its draw. In the table,
"balance" is the figure the ship shows today with an ordinary drive switched on; the right answer is
balance - P with the accelerated drive on, and balance + P with it off (the same as an ordinary drive off).

| | standard reactor: draw × 2, reactor - P | fixed-power: draw × 2 only | fixed-power: draw × 2, reactor - P |
|---|---|---|---|
| drive on | balance - P ✓ | balance - P ✓ | balance - 2P ✗ |
| drive off | balance + P ✓ | balance + P ✓ | balance ✗ |

The client's power code needs no change. `getReactorPower` reads the reactor's `output` (always in the
payload) and the drive's `powerReq` (sent by the `JUMP_ACC` payload case). `getRemainingFreeablePower`
then counts the doubled draw, and the Initial Orders commit gate works as it does today. On the server,
`EdfExposure::getMaxAvailablePower` reads the same live fields.

**2.3.4 Failure** - one helper, `getOpeningFailureMultiplier($ship)`, used at two sites:
- `JumpEngine::openVortex` (baseSystems.php ~9123-9129): the quoted "Chance of failure (N)" in the log.
- `JumpEngine::rollVortexJumpFailure` (~10329-10336): only when `(int)$this->vortexOpenTurn === $turn`. A
  Maintain turn rolls the normal chance (D17).
- `min(100, ...)` after the Ancient halving and before the Walker zeroing - both blocked anyway, but the
  order is written down.
- Not touched: `doHyperspaceJump` (the legacy boost path), `rollAbductionJumpFailure` (the EDJD) and the
  gate rolls.

**2.3.5 Payload** - `addSystemEnhancementsForJSON`, `case 'JUMP_ACC'`, on the Jump Engine: `range`,
`powerReq`, `jumpAccelerated` and `data`. The reactor's `output` is already sent every time.
`loadingtime` already comes from `getVortexRechargeTime()`, which reads `$delay`.
- The server's declaration check (firing.php:450) reads the live engine's range, and the client's
  `targetHex` and reach overlay read `weapon.range`, so +2 works on both ends once it is sent.
- **The +2 also lengthens the hold distance.** A jump point closes at end of turn when its holder is
  more than `$this->range` hexes from it (baseSystems.php:9593), the same field. An accelerated drive
  therefore declares at up to 6 hexes and also holds the jump point open from up to 6 away. That follows
  from the drive having one range, so the plan builds it as-is, and the docs card says so (§2.4.2). If
  only the declaration should grow, the close check needs its own base range, which is a separate
  change.
- `data`: `JumpEngine::setSystemDataWindow` (baseSystems.php:10404) gains one sentence for an accelerated
  drive, and its "within N hexes" already prints `$this->range`. `data` is rebuilt per load from the
  enhanced values, but it never reaches the client unless a payload case sends it
  ([[arch_system_info_tooltip_data_flow]]). Sending it replaces the static dict wholesale, which is safe
  here: the same method builds both, so the live dict is a superset.

**2.3.6 Lobby mirror** - `case 'JUMP_ACC'` plus the post-pass: `loadingtime` and `turnsloaded` become
`Math.max(1, Math.floor((delay * 67 + 50) / 100))` after the crew step (`applyCrewJumpDelay` moves out
of the loop), range +2, `powerReq` × 2, reactor output - P unless fixed-power, and the `data` text.
⚠️ Mirror pair with `JumpEngine::applyJumpAccelerator`: the same whole-number arithmetic (exact on both
ends, so no float can round differently) and the same order.

### 2.4 Player docs - the "Ammo, Options & Enhancements" document (Stage 6)

`source/public/docs/ammo-options.html` is the DATA ARCHIVE document (shown by `docViewer.js` in-page and
by `ammo-options-enhancements.php` full-page). The three enhancements get cards in its **Ship Enhancements** entry (`data-key="shipenhancements"`), in
the format its header comment sets out: `<article class="fvd-card">`, `<h4>` name, a `<dl class="fvd-spec">`
with Effect / Cost / Limit / Notes, inside the existing `<div class="fvd-cards">`. The words below are the
drafts. Stage 6 pastes them in once Stages 3-5 are built and checks every number against the built code.
"Defence Rating" in percent is the document's own term (the Elite Crew card says "-5% Defence Rating"),
so the coating's price is written in it: 5000 ÷ the average Defence Rating in % is the same number as
1000 ÷ the average profile.

**2.4.1 Advanced Engine Module** - a normal card, placed straight after Improved Engine.
- **Effect:** The ship's strongest Engine gets 1 better Efficiency - the power each extra point of thrust
  costs when you boost it - to a minimum of 1. An Efficiency 3 Engine boosts for 2 power per thrust.
- **Cost:** 10% of the ship's cost (rounded up)
- **Limit:** 1
- **Notes:** Lost for good the first time ANY of the ship's Engines suffers a critical of any kind. It
  stops working from the following turn, and repairing the critical does not bring it back. Not offered
  if the strongest Engine is already Efficiency 1. Hyach Thruster Specialists cannot take an Engine
  below Efficiency 1 either.

**2.4.2 Jump Accelerator** - a normal card, placed after Improved Sensors.
- **Effect:**
  - Jump delay reduced by 33% (fractions of 0.5 or more round up).
  - Jump Drive range +2 hexes (4 becomes 6): it can open a jump point up to 6 hexes away, and the jump
    point stays open while the ship ends its turns within 6 hexes of it.
  - The Jump Drive's power draw is doubled. The extra comes out of the ship's spare power.
  - At the end of the turn the ship **opens** a jump point, the chance of a damaged drive destroying the
    ship is doubled (to a maximum of 100%). Turns spent maintaining it roll the normal chance.
- **Cost:** 10% of the ship's cost (rounded up)
- **Limit:** 1
- **Notes:** Only for Jump Drives that open jump points - not the one-click drives (Star Wars Hyperdrive,
  BSG FTL, Shadow Phasing Drive), and not Ancient drives. With Elite or Poor Crew, the crew change is
  applied first: a 20-turn delay with Elite Crew becomes 16, then 11.

**2.4.3 Stealth Coating** - a **wide** card (`fvd-card--wide`), placed after Poor Crew, so the wide cards
stay together at the top and the grid of narrow cards below is unbroken.
- **Effect:** -5% to be hit (1 point of Defence Rating) against a shot from a weapon that uses OEW, fired
  by a unit holding at least 1 OEW on this ship (after Disruption EW), when the shot comes in on a covered
  facing. The front is the 60° ahead of the bow (30° either side), the aft the 60° behind the stern, and
  everything else is a side. This holds whatever sections the ship has. A ballistic shot is judged from
  where it was launched.
- **Coverage:** picked from a drop-down when buying the ship - All, Front & Sides, or Front Only.
- **Cost:** 5000 ÷ the average of the ship's forward and side Defence Ratings (in %), then
  - All: 100%
  - Front & Sides: 75%
  - Front Only: 25%

  each rounded up. A ship with 75% forward and 85% side Defence pays 63 / 47 / 16.
- **Limit:** 1
- **Notes:** No effect against weapons that do not use OEW, against mines, against fighters with no OEW
  of their own, or against a shooter with Advanced Sensors. It does not break a lock-on: a shooter on
  exactly 1 OEW keeps its lock and still takes the -5%. Not available to bases, terrain or Ancient
  units. OSATs can carry it.

**2.4.4 Corrections to existing cards** (D21, default "the code is right"):
- Elite Crew, Cost: "+40% of the ship's cost (a second time: +60%)" becomes "+50% of the ship's cost (a
  second time: +75%)".
- Poor Crew, Effect: "-1 Engine" and "-1 Reactor power" become "-2 Engine" and "-2 Reactor power". The
  Notes line follows wherever it repeats them.
- If D21 goes the other way, the cards stay as they are and the code changes instead (Stage 1).

**2.4.5 Verify** - the same check the document viewer work used ([[project_document_viewer]]): open the
Ship Enhancements entry in the in-page window and on the full page
(`ammo-options-enhancements.php#shipenhancements`), at 1920 wide and at a true 390 px (CDP device
emulation). Check the three cards, the wide card's price list, and no horizontal scroll. The new cards
need no anchors, since the existing cards have none. If one is ever wanted, use `data-anchor`, never `id=`.

## 3. Traps shared by all three

1. **`enhid` is `varchar(10)` in both tables.** Stage 0 adds an assertion that every offered ID fits.
2. **`blockStandardEnhancements` must list every new standard ID** (§2.1.1).
3. **Prefixes and names.** An ID starting `AMMO_`, `SHELL_` or `MINE_` is treated as ammunition
   (`isAmmoEnhancement`, and the client's magazine cap in `doOnPlusEnhancement`). A name containing
   "(AMMO)" is filed under Ammo & Ordnance.
4. **Statics.** The lobby buys from `static/json/<faction>.json`, so any offer change needs
   `fvbuild.ps1 -Server`. Expected growth: the Stealth Coating tuple with its choices and prices is about
   140 bytes on roughly 2,500 hulls, about 350 KB raw (gzip leaves about 7% of that).
5. **POST-side ships carry no enhancements and no notes.** Nothing here decides anything on one.
6. **The server trusts the client's ship-level prices and counts** (only per-system refits are
   re-priced). That is true of every existing enhancement. The apply side clamps the new counts to 1 and
   ignores unknown coating keys.
7. **New public properties on BaseShip or ShipSystem are written into every static blueprint.** Use
   protected properties published only when set, or add them to `$serverOnlyShipKeys` in BOTH generator
   files ([[arch_shipcompactor_key_stripping]]).
8. **`JSON_NUMERIC_CHECK`** rewrites numeric-looking strings in the payload. The coating keys are words.
9. **The replay harness** has no game carrying these rows, so every stage should leave the corpus
   byte-identical. Any diff is a regression unless it is one of the nine known failures - prove it by
   stash-and-compare ([[arch_replay_corpus_known_failures]]).
10. **Mirror pairs** to keep in step: the three apply cases ↔ `lobbyEnhancements`; the `ADV_ENG`
    engine pick before the loop ↔ the lobby's pick before the loop; the jump-delay post-pass ↔ the lobby
    post-pass; `$coatingLock` in the `useOEW` block ↔ `computeOEW`'s `coatingLock`;
    `getProfileCoatingReduction` ↔ the `calculateHitChange` row; the bearing-to-facing rule ↔
    `getShipDefenceFacing`.
11. **Deploy:** the `DouglasChanges` working copy needs the same change set.

## 4. Item 4 - the enhancement code review

### 4.1 What it costs today (measured 2026-10-03)

PHP CLI in the php container; Omega, G'Quan, Primus, Hyperion and Tinashi built straight from their
blueprints; 500 runs each after a warm-up pass, with the garbage collector off during the timed loop.
(With it on, collector pauses over the 500 retained ships showed up as 40 µs outliers on the Omega.)

| Path | Cost per ship | Notes |
|---|---|---|
| Constructing the ship (the floor) | 115-175 µs | Omega 174, Hyperion 160, Primus 123, G'Quan 122, Tinashi 115 |
| `setEnhancements`, nothing bought | 4-5 µs | an unconditional ammo-magazine sweep, plus the `NONE` row |
| `setEnhancements`, Elite Crew | 8-10 µs | four system sweeps, plus the jump-engine sweep |
| `setEnhancements`, Poor Crew ×2 + Improved Sensors + Improved Engine | 11-14 µs | |
| `addSystemEnhancementsForJSON`, whole ship, 3 rows bought | 7-11 µs | per uncached payload |
| `ShipSystem::stripForJson`'s two `getSystemByName` calls, whole ship | 38-84 µs | per uncached payload, grows with systems² |

So the loops in `setEnhancementsShip` and `setEnhancementsFighter` (a flight's loops run over at most 12
craft with a few subsystems each) are not where the time goes. The case for R1-R7 is fewer copies of the
same logic, the bugs found, and the order fix the Jump Accelerator needs.

### 4.2 Recommended (behaviour-identical, except the fixes in R6)

- **R1 - one `strongestSystem($ship, $class, $field)` helper.** Highest value, strict `>`, so the first in
  construction order wins a tie - exactly the hand-written loops it replaces. They are at eight apply
  sites (Elite ×3, Poor ×3, Improved Engine/Reactor/Sensors) and three offer sites. Each site keeps its
  own existing guard (`> 0`, or `!= null` for the reactor).
  ⚠️ **Ask at the moment of application; never cache the winner.** An earlier row can change the field:
  with two equal scanners A and B, Poor Crew takes A down, so a following Improved Sensors picks B today.
  A cached winner would pick A. The new `ADV_ENG` is the one deliberate exception: it picks before the
  loop, because it is new and has no existing behaviour to keep (§2.1.2).
- **R2 - Elite and Poor Crew rewritten on R1.** About 140 lines become about 40, with the same arithmetic
  per level.
- **R3 - one jump-delay post-pass:** Elite levels, then Poor levels, then the accelerator, after the loop.
  Elite before Poor is the order a game uses today (`enhid` ascending), so no existing game moves. The
  lobby moves to the same order, which also fixes its display for the unusual hull carrying both.
  *As built, the lobby also walks every row in the game's order* (a sorted copy of
  `enhancementOptions`), because R7 showed the order changes more than the jump delay (§6.1).
- **R4 - find the ammo magazine only when an ammo row is bought.** Today it is swept on every ship on
  every load.
- **R5 - delete the unreachable `foreach` in `addSystemEnhancementsForJSON`** (Enhancements.php:3312-3320,
  between a `break` and the next `case`). If it ever became reachable it would multiply a Mag-Grav
  reactor's output by 1.25 on every serialisation. Also correct two comments that name the wrong
  enhancement (`IMPR_PSY` says Spark Curtain, `GUNSIGHT` says Thought Shield).
- **R6 - the drift found between the lobby, the server and the docs** (each a real bug):
  - Elite Crew's critical modifier: the server applies -1 per level (Enhancements.php:2402), the lobby -2
    (lobbyEnhancements.js:203).
  - Poor Crew's reactor: the server takes 2 per level off (Enhancements.php:2833), the lobby 1
    (lobbyEnhancements.js:640). The docs say -1 as well, so the direction of this fix follows D21.
  - Poor Crew's engine: the server and the lobby both take 2 per level off the strongest engine
    (Enhancements.php:2819, lobbyEnhancements.js:624), while `docs/ammo-options.html:175` and the case
    comment (Enhancements.php:2772) say -1. Docs-only unless D21 says otherwise.
  - Elite Crew's price: the code charges +50%, then +75% more for the second level (125% in all,
    Enhancements.php:238-239). `docs/ammo-options.html:162` and the code comment above it say +40% / +60%.
    **D21** - the default keeps the code and corrects the docs and the comment.
- **R7 - a differential test** that applies Elite, Poor and Improved Engine/Reactor/Sensors through
  `lobbyEnhancements` (Node `vm`) and through the server (PHP) to the same hulls, and compares the
  numbers. R6's drift would have shown up there.

### 4.3 Optional - Stage 7 (D18)

D18 makes these an optional follow-up stage, built after Stages 0-6, or not at all. Each is
independent, and each must leave the Stage 1 fingerprints (§7) and the corpus identical. O2 keeps the
caveat below: the Stage 7 build should start by asking whether O2 is wanted at all.

- **O1 - a JSON prefilter.** A protected list of the bought rows that have a system-payload case,
  computed once in `setEnhancements`, would let most ships skip that loop for every system: about 7-11 µs
  per ship per uncached payload. It adds a list that must track the switch, so it would need an
  assertion that the two agree.
- **O2 - one ammunition table** (ID → ammo class) read by the offer, the apply and the lobby. About 400
  fewer lines across PHP and JS, no runtime gain. WEAPON_ENHANCEMENTS_PLAN.md D12 (Enhancements.php:3428)
  ruled out moving the ship-level switch onto a registry as churn, so it is listed here, not
  recommended.
- **O3 - the lobby's 50-odd per-enhancement marker flags as one object**, cleared in one line. They have
  been a second line of defence since `apply()` gained its own guard.

### 4.4 The one measurable win found - just outside the enhancement code

`ShipSystem::stripForJson` (ShipSystem.php:330-342), on the line after the enhancement call, runs
`getSystemByName("HyachSpecialists")` and `getSystemByName("MindriderEngine")` for **every system**: two
full sweeps per system, 38-84 µs per ship per uncached payload, five to eight times the whole
enhancement loop. `BaseShip::stripForJson` already asks the same questions in constant time with
`hasSpecialAbility(...)` (ShipClasses.php:965, 1008). On a blueprint that has not been through
`onConstructed` both blocks would then be skipped, which changes nothing in the static output - neither
block sends anything a blueprint does not already hold. This is outside the scope you asked for, so it is
listed here rather than planned. Say if you want it as a stage.

## 5. Item 5 - the buy dialog's section headers

### 5.1 The look

Today all three section heads wear the same `#90b1ee` band (confirm.css:1735). Palette A, as ruled
(D19):

| Section | Bar (3 px) | Wash colour | Title | Icon |
|---|---|---|---|---|
| Ammo & Ordnance - gunmetal | `#8e9aa6` | `rgb(142,154,166)` | `#eef2f5` | `#c9d2da` |
| Enhancements - bronze | `#b08d57` | `rgb(169,128,56)` (`theme.colors.enhBg`) | `#e8cf93` (`enhTitle`) | `#e8cf93` |
| Options - verdigris | `#4fa79a` | `rgb(79,167,154)` | `#dcf3ee` | `#8fd3c7` |

The wash keeps today's strengths: 0.12 under a 0.24 gradient fading to nothing at 70%, 0.22 on hover,
0.45 for the open section's bottom line. The reserved Officers section keeps today's blue as the
fallback.

Why these three: they are all metals. The bronze is the ship window's existing "bought, not standard
equipment" gold set (`theme.colors.enh*`), so an enhancement looks the same in the buy dialog as it does
in the ship window afterwards. Verdigris is the patina bronze turns, so it pairs with it while being a
different hue. All three avoid the colours that already mean something on these pages: allegiance
green/blue/red, warning yellow, mine purple and hyperspace cyan. (Palette B, which kept Options in
today's blue, was not chosen.)

A rendered comparison (today, A and B, plus A at phone width) was built while planning:
`buyHeaderPreview.html` / `.png` in that session's scratchpad. Rebuild it from the table above if it is
gone. It confirmed that the icons read clearly at 18 px on a 2× screen, and that the title, icon and
badge stay on one line at 358 px.

### 5.2 Markup

`confirm.BUY_SECTIONS` (confirm.js:813) gains an `icon` per section (none for Officers).
`buyDialogShell` (confirm.js:877) adds `<span class="buySectionIcon" aria-hidden="true"></span>`
straight after the title, so the icon sits on the right of the header text. The title stops growing
(`flex: 0 1 auto`) and the icon takes `margin-right: auto`, so the badge stays at the right edge. One
shell builds all four dialogs (buy, edit, copy, bulk), so this is one change.

### 5.3 Style

- Per-section custom properties on `.buySection[data-section="ammo|enhancements|options"]`
  (`--buy-sec-bar`, `--buy-sec-rgb`, `--buy-sec-title`, `--buy-sec-icon`), read by the existing head
  rules at confirm.css:1735-1785. Those are scoped properties, not a second `:root` block.
- `tokens.css` gains the gold set as CSS twins of `theme.colors.enh*` (with the sync-contract comment in
  both files) and two hue tokens, `--fv-gunmetal` and `--fv-verdigris`. `tokens.css` stays the only
  `:root` block ([[project_visual_unification]]).
- The icon is a mask: `background-color: var(--buy-sec-icon)` with `-webkit-mask` and `mask` set to
  `url("../img/Ordnance.png") center / contain no-repeat`. Wrap it in
  `@supports (mask-image: url("")) or (-webkit-mask-image: url(""))`: a browser without masks would
  otherwise draw a solid coloured square.
- 18 px, or 20 px inside the existing `(pointer: coarse)` block. In the filter's disabled state the icon
  dims to 0.45, as the disclosure box does (confirm.css:1782).
- No element `opacity` on the coloured parts - the fills carry their own alpha (the alpha-compounding
  trap).

### 5.4 The images

- **Commit 64 px copies (D20)**, under the same three names, in place of the 512 px originals (18-28 KB
  each today; about 2 KB each at 64 px). 64 px covers 18 px up to a 3.5× screen and 20 px up to 3.2×.
  - Copy the three originals out of the repo first, to `C:\FV_env\art\buy-section-icons\`, and never
    delete them. A redraw starts from those.
  - Resize with .NET `System.Drawing` from PowerShell: a 32bpp ARGB bitmap, `HighQualityBicubic`, saved
    as PNG, which keeps the alpha. Check one result before committing: still white on transparent, and
    the edges still clean at 18 px.
  - ⚠️ **No image tool is installed.** There is no ImageMagick, and the php container has no GD. The
    `convert` on this machine's PATH is `C:\Windows\System32\convert.exe`, Windows' FAT-to-NTFS **disk
    conversion** tool. Never run `convert` here.
  - The files are untracked today. When Stage 2 is committed, add these three paths by name (never
    `git add -A`).
- The names are capitalised (`Ordnance.png`, `Enhancements.png`, `Options.png`). Live is Linux and
  case-sensitive, so reference them exactly ([[arch_dockingcollar_icon_case]]).
- `url()` in a stylesheet resolves relative to `styles/`, so it is `../img/...`.
- Images are cached for a year and a CSS `url()` bypasses the `?v=` cache-buster. That is fine for new
  files; if one is redrawn later, give it a new name ([[arch_image_cache_busting]]).
- `mass_optimizer.php` writes `.webp` siblings on live. The stylesheet keeps loading the `.png`, which
  works.

### 5.5 Verify

Screenshots at 1920 wide and at a true 390 px (CDP device emulation, not `--window-size`;
[[howto_headless_chrome_phone_width]]), for the buy, edit, copy and bulk dialogs, plus the filter's
"nothing matches" state. Use a ship with all three sections (Kor-Lyan `VerlokaAM`, 28 rows) and a mine
(`dewMineDShal`).

## 6. Build order

Each stage leaves the game working and can be committed on its own.

| Stage | What | Needs | Build step | Gate |
|---|---|---|---|---|
| 0 | Baseline: run `fvbuild.ps1 -Check` and record the failure set. Start `tests/replay/enhancementsHarness.php` (in memory, real ship files, the `legacyRechargeHarness.php` shape) with the ID-length assertion. | - | - | - |
| 1 | Item 4: R1-R7. | D18; D21 (default: keep the code) | `-Server` | Corpus byte-identical; fingerprints identical (see §7); R7 passes |
| 2 | Item 5: headers, plus the 64 px icons (§5.4). | D19, D20 | `-Client` | Screenshots per §5.5 |
| 3 | Advanced Engine Module. | D1-D4 | `-Server` | Corpus identical; harness; local play |
| 4 | Jump Accelerator. | D13-D17 | `-Server` | Corpus identical; harness; client harness; local play |
| 5 | Stealth Coating. | D5-D12 | `-Server` | Corpus identical; server and client harnesses agree; local play |
| 6 | Player docs (§2.4): the three new cards in the "Ship Enhancements" entry of `docs/ammo-options.html`, and the D21 corrections to the Elite and Poor Crew cards. | Stages 3-5 built; D21 | - | Document viewer check (§2.4.5) |
| 7 | **Optional** (D18): O1-O3 (§4.3), each on its own. | Stages 0-6 | `-Server` for O1/O2 (statics must come out identical); none for O3 (lobby JS, watched) | Fingerprints and corpus identical; R7 passes |

All decisions are ruled, so nothing blocks any stage. Stage 1 goes first because the Jump Accelerator
builds on its post-pass and the harness proves "identical" before any behaviour changes. If item 4 is
declined, Stage 4 adds the post-pass by itself. Stage 2 is independent and can go at any point. Stealth
Coating is last because it is the only one touching hit chances on both ends. Stage 6 waits for the
three enhancements, so the docs never describe something players cannot buy yet. Stage 7 is optional
and can be dropped without touching anything else.

### 6.1 As built - Stages 0-2 (2026-10-03, uncommitted)

**Stage 0 - baseline and harness.**
- `fvbuild.ps1 -Check` on the clean tree (HEAD `f2c3d0ab8`): autoload current, validator 0 new errors,
  replay **127 passed / 5 failed: 4297, 4356, 4357, 4359, 4361**. All five differ only in a Kelly Phaser
  hull's `minDamage`/`maxDamage` (16 -> 6/7, 34 -> 36), from the "Kelly Phaser changes" commit, which
  has not been re-recorded. Nothing to do with enhancements.
- `tests/replay/enhancementsHarness.php` - `check` (default), `record`, `ids`, `fingerprint`,
  `diffdump`. No database: ships are built from their blueprints and given rows in the game's shape and
  order. The fingerprint covers 12 hulls x 18 cases, plus the Ipsha refits and each hull's own
  ammunition: 219 cases, 9,444 lines (offers, every stat an enhancement can move, and an md5 of the
  whole `stripForJson`). It was recorded on HEAD before any refactor, at
  `tests/replay/baseline_enhancements/fingerprint.txt`. Negative control: Poor Crew's critical modifier
  x3 instead of x2 changed 192 lines.
- The ID check covers 109 IDs: the `$enhID` literals, the per-system registry, and every ID a static
  blueprint offers. All fit in 10 characters.
- ⚠️ `/tests` is gitignored (root `.gitignore`), so both harness files and the recorded fingerprint
  are local-only, like the other stage harnesses.

**Stage 1 - R1-R7.** Gates: fingerprint identical (9,444 lines); replay report byte-identical to
Stage 0's, timings aside (same 127/5, same diffs); validator 0 new errors; R7 0 differing.
- R1 `Enhancements::strongestSystem($ship, $class, $field, $floor = -1)`. **`$floor` was added** so the
  helper is provably the old loops: each started at -1 or -1000, and a system had to beat that value.
  It is used at seven apply sites (through R2) and two offer sites (Improved Engine, Improved Reactor).
  **The Improved Sensors offer was left as it was**: its ELINT and Advanced flags stick to ANY scanner
  that was the running maximum, not only the final one, so moving it onto the helper would re-price
  some hulls. A comment there says so.
- R2 `applyCrewStats($ship, $step, $critMod)`, one signed block. Lobby twin:
  `lobbyEnhancements.applyCrewStats`, with `strongestOf` as the R1 twin.
- R3 post-pass on both ends. R4 looks for the magazine only when an ammunition row is bought. R5
  deleted the dead block and fixed the two comments.
- R6 under D21's default: the Elite Crew offer comment now reads +50% / +75% and -1 crit, the Poor
  Crew case comment reads -2 Engine / -2 Reactor / -1 Thrusters, the lobby's Elite crit is -1 per level,
  and the lobby's Poor reactor is -2. The docs are Stage 6's.
- **R7** `tests/replay/enhancementsDifferential.js` (Node; it runs the PHP `diffdump` in the container
  itself). It compares 198 cases on the same hulls and skips 6 where a row is not offered. **Its first
  run, on HEAD, found 134 differing.** That was R6's two drifts plus two more:
  - **Poor Crew's thrusters:** the server takes 1 off every thruster per level, and the lobby never did.
    Fixed in the lobby.
  - **Row order, much wider than the jump delay:** with Poor Crew in the mix, the lobby (ID descending)
    and the game (ascending) picked DIFFERENT "strongest" systems. Tratharti with Elite x2 + Poor x2
    showed engines 2 / 8 in the lobby and 6 / 4 in game; Areko and pirocia did the same on their tied
    engines and scanners. Fixed by the lobby walking rows ascending (a sorted copy, so the buy dialog's
    row order is untouched).
  - After the fixes: **0 differing**. The ammo magazine is excluded from the comparison: a fresh
    server magazine reports `output` 1, the blueprint the generator's round count.
- Built with `fvbuild.ps1 -Server`. The statics are untracked, so regenerating them changes nothing in
  git.

**Stage 2 - headers.** As §5, with three small departures:
- `tokens.css` also gained `--fv-bronze` (`#b08d57`, the Enhancements bar), so that value is a token
  like the other two metals rather than a stray literal.
- The **badge** takes `margin-left: auto` instead of the icon taking `margin-right: auto`. That way
  Officers, which has no icon, still keeps its badge on the right.
- **The filter box is not in the shell today** (its markup is commented out in `buyDialogShell`), so a
  player cannot reach the disabled "nothing matches" head. The screenshot forced `disabled` on a head
  instead; the icon dims to 0.45 with the disclosure box.

The other Stage 2 details:
- Icons: 64 px copies at 966 / 1,860 / 2,069 bytes, made with .NET `System.Drawing`. The 512 px
  originals are in `C:\FV_env\art\buy-section-icons\`.
- Bundles: rebuilt with `yarn build:legacy`, minified like the bundles it replaced. There is no React
  change: `theme.js` got a comment only.
- Verified on the real local lobby (game 4438, player 211, every non-GET request blocked): buy, edit,
  copy and bulk dialogs, at 1920 and at a true 390 px. Colours, bars and washes measured as §5.1. Icons
  are 18 px, or 20 px with `(pointer: coarse)`. Title, icon and badge sit on one line, with the badge
  flush right.

## 7. Testing

- **Stage 1 fingerprint.** For about 10 hulls × {Elite 1 and 2, Poor 1 and 2, Improved
  Engine/Reactor/Sensors alone and combined with Poor}, record every system's output, armour and
  maxhealth, the profiles, initiative, critical modifier, to-hit bonus, hangar outputs and jump delay -
  from HEAD (stash) and from the refactor. They must match exactly. R7 runs the same cases through the
  lobby code.
- **Advanced Engine Module.** Offered on exactly the hulls whose strongest engine is boostable above 1
  (the 1,776 of §2.1.1); -1 with the floor, on the strongest engine only. On a two-engine hull (Dilgar
  Tratharti, 6@3 + 4@2), only the 6-thrust engine moves. On a hull with two equal engines plus Poor Crew,
  the same engine is improved in game and in the lobby (the §2.1.2 pick). Lost from turn N+1 after a
  critical on turn N, **including a critical on an engine that was not improved**; still lost once that
  critical is repaired; never written from a POST-side ship; `boostEfficiency` in the payload in both
  states.
  Local play (a new game): buy it on an efficiency-3 hull and check the `tac_enhancements` row `ADV_ENG`.
  A boost should cost 2 power per thrust. After an engine critical, the next turn shows 3 again, and
  `tac_individual_notes` has `AdvEngLost`.
- **Jump Accelerator.** A delay table (16 → 11; 20 with Elite → 11; 50 → 34; 65 → 44; 45 with two
  levels of Poor → 65 → 44), with the PHP and JS formulas agreeing on every delay from 1 to 100;
  not offered on a Vorlon hull or any legacy/Ancient drive; range 6, draw doubled,
  reactor - P on a standard hull and untouched on `shipJumpsphere`, failure × 2 on the opening turn only
  and capped at 100, the payload keys. Client harness (`vm`, the Stage 6 jump-points pattern) for the
  lobby numbers. Local play: declare at 6 hexes; the reactor shows -P while the drive is on and +P when it
  is switched off; damage the drive and open a jump point - the log quotes double the chance.
- **Stealth Coating.** Price per choice on a few hulls; offered on an OSAT and a Middleborn hull, not on
  a base, a factionAge ≥ 3 hull, terrain or a fighter; name / index / garbage resolution. The lock (D9):
  OEW 2 against OEW 0; **OEW exactly 1 still locked** (no doubled range penalty, SOEW kept) and still -5%;
  OEW 1 cancelled to 0 by disruption EW; a `useOEW = false` weapon with OEW on the target (no
  reduction); a fighter with an offensive bonus and no OEW of its own (no reduction); a mine (no
  reduction). Covered against uncovered facings, including a `HeavyCombatVesselLeftRight` shot from
  ahead and a hull with a destroyed front section; ballistics; the flat-profile override; Advanced
  Sensors; a Jammer target; the gate. The client preview must equal the server's goal on the same
  fixtures, with the breakdown sum check passing. Local play: buy Front Only, take shots from the front
  and the side with and without OEW, and compare the `needed` values in `tac_fireorder`.
- Every stage ends with `fvbuild.ps1 -Check` (autoload, ship-data validator, replay harness).

## 8. Files

| File | Stages |
|---|---|
| `source/server/model/ships/Enhancements.php` | 1, 3, 4, 5 |
| `source/server/model/ships/ShipClasses.php` (BaseShip: coating state, `getProfileCoatingReduction`) | 5 |
| `source/server/model/systems/baseSystems.php` (Engine; JumpEngine; Hyach Specialist floor) | 3, 4 |
| `source/server/model/weapons/weapon.php` (`calculateHitBase`) | 5 |
| `source/server/model/TacGamedata.php` (`$profileCoatingPresent`) | 5 |
| `source/public/client/lobbyEnhancements.js` | 1, 3, 4, 5 |
| `source/public/client/UI/confirm.js` (priced choice widget; header markup) | 2, 5 |
| `source/public/styles/confirm.css`, `source/public/styles/tokens.css` | 2 |
| `source/public/client/UI/reactJs/styled/theme.js` (sync comment only) | 2 |
| `source/public/client/weaponManager.js` (facing helper; `computeOEW`'s `coatingLock`; coating row) | 5 |
| `source/public/client/model/system/baseSystems.js` (Hyach Specialist floor) | 3 |
| `source/public/docs/ammo-options.html` (three new cards; Elite/Poor Crew corrections) | 6 |
| `source/public/img/Ordnance.png`, `Enhancements.png`, `Options.png` (yours, untracked today; replaced by 64 px copies, originals kept outside the repo) | 2 |
| Stage 7 (optional): `Enhancements.php` (O1, O2), `lobbyEnhancements.js` (O2, O3) | 7 |
| `tests/replay/enhancementsHarness.php`, plus a Node harness | 0-5 |
